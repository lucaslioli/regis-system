<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Support\Facades\Storage;

use App\Models\Query;
use App\Models\Document;
use App\Models\Judgment;
use App\Models\User;

class ProjectController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function qrelsExport()
    {
        // Get all queries completed
        $completed_pairs = DB::table('document_query')
            ->whereIn('status', ['agreed', 'solved'])
            ->orderBy('query_id')
            ->get();

        $response = array();
        
        foreach ($completed_pairs as $pair) {
            $query = Query::find($pair->query_id);
            $document = Document::find($pair->document_id);

            if($pair->status == "agreed"){
                $judgment = Judgment::where('query_id', $pair->query_id)
                    ->where('document_id', $pair->document_id)->first();

                $relevance = $judgment->judgment;

            }else{
                $judgments = Judgment::where('query_id', $pair->query_id)
                    ->where('document_id', $pair->document_id)->pluck('judgment')->all();

                // Group and count judgments to get the one with 2 votes
                $relevance = array_search(2, array_count_values($judgments));
            }

            $relevance = mapJudgment($relevance);

            
            // Format: Q1 0 BR-BG.00001 1.0
            array_push($response, $query->qry_id." 0 ".$document->doc_id." ".$relevance);
        }

        Storage::disk('local')->put('qrels.txt', implode(PHP_EOL, $response));
        return Storage::download('qrels.txt');

        return back();
    }

    public function statistics(){
        $this->authorize('id-admin');
  
        $queries_status = Query::statusChartData();
        $judgments = Judgment::judgmentsChartData();
        $completed_queries = Query::completedQuriesStatsData();
        $users = User::userStatsData();

        return view('project.statistics', [
            'queries_status' => json_encode($queries_status),
            'judgments' => json_encode($judgments),
            'completed_queries' => $completed_queries,
            'users' => $users]
        );
    }

    public function userRanking()
    {
        $users = User::userStatsData(False); 

        return view('project.ranking', compact('users'));
    }
}
