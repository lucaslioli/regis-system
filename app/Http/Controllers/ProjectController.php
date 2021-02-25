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
        // Get all pairs from completed queries
        $completed_pairs = DB::table('document_query')
            ->join('queries', 'queries.id', '=', 'document_query.query_id')
            ->select('document_query.*')
            ->whereIn('document_query.status', ['agreed', 'solved'])
            ->where('queries.status', 'Complete')
            ->orderBy('document_query.query_id')
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

    public function qrelsExportPreliminary()
    {
        // Get all pairs from completed and semi competed queries
        $preliminary_pairs = DB::table('document_query')
            ->join('queries', 'queries.id', '=', 'document_query.query_id')
            ->select('document_query.*')
            ->where('queries.status', '!=', 'Incomplete')
            ->orderBy('document_query.query_id')
            ->get();

        $response = array();
        
        foreach ($preliminary_pairs as $pair) {
            $query = Query::find($pair->query_id);
            $document = Document::find($pair->document_id);

            if($pair->status == "solved"){
                $judgments = Judgment::where('query_id', $pair->query_id)
                    ->where('document_id', $pair->document_id)->pluck('judgment')->all();

                // Group and count judgments to get the one with 2 votes
                $relevance = array_search(2, array_count_values($judgments));

            // For agreed, tiebreak, or under review pairs, gets ony only one judgment
            }else{
                $judgment = Judgment::where('query_id', $pair->query_id)
                    ->where('document_id', $pair->document_id)->first();

                $relevance = $judgment->judgment;
            }

            $relevance = mapJudgment($relevance);
            
            // Format: Q1 0 BR-BG.00001 1.0
            array_push($response, $query->qry_id." 0 ".$document->doc_id." ".$relevance);
        }

        Storage::disk('local')->put('preliminary_qrels.txt', implode(PHP_EOL, $response));
        return Storage::download('preliminary_qrels.txt');

        return back();
    }

    /**
     * Export file with judgments for each pair that has a tiebreak
     */
    public function tiebreaksExport()
    {
        // Get all pairs from completed queries
        $queries = Query::whereIn("status", ['Semi Complete', 'Complete'])
            ->get();

        $response = array();
        
        foreach ($queries as $query) {

            foreach ($query->documents as $document) {

                if($document->statusByQueryPair($query->id) == "Solved"){
                    $judgments = $document->judgmentsByQuery($query->id, False, "\t", True);
                    // Format: Q1    BR-BG.00001    3    2    3
                    array_push($response, $query->qry_id."\t".$document->doc_id."\t".$judgments);
                }

            }

        }

        Storage::disk('local')->put('tiebreaks-solved.txt', implode(PHP_EOL, $response));
        return Storage::download('tiebreaks-solved.txt');

        return back();
    }

    /** 
     * Export file with all judgments for each pair to calculate Fleiss Kappa.
     * Relevance levels are reperesented by letter (a, b, c and d).
     * Tiebreak judgments are not included.
     */
    public function judgmentsKappaExport()
    {
        // Get all pairs from completed queries
        $queries = Query::where("status", 'Complete')->get();

        $response = array();
        
        foreach ($queries as $query) {

            foreach ($query->documents as $document) {

                if($document->statusByQueryPair($query->id) != "Review"){
                    $judgments = $document->judgmentsByQuery($query->id, False, "\t", True, True);
                    // Format: Q1-BR-BG.00001    a    b
                    array_push($response, $query->qry_id."-".$document->doc_id."\t".$judgments);
                }

            }

        }

        Storage::disk('local')->put('judgments-kappa.txt', implode(PHP_EOL, $response));
        return Storage::download('judgments-kappa.txt');

        return back();
    }

    public function statistics(){
        $this->authorize('id-admin');
  
        $queries_status = Query::statusChartData();
        $judgments = Judgment::judgmentsChartData();
        $completed_queries = Query::completedQuriesStatsData();
        $users = User::userStatsData();

        $tiebreaks = DB::table('document_query')
            ->select(DB::raw('status, count(status) as total'))
            ->whereIn('status', ['solved', 'tiebreak'])
            ->groupBy('status')->orderBy('status')
            ->get();

        $total = array(
            'judgments' => ($judgments[1][1] + $judgments[2][1] + $judgments[3][1] + $judgments[4][1]),
            'solved' => (isset($tiebreaks[0]))?$tiebreaks[0]->total:0,
            'tiebreaks' => (isset($tiebreaks[1]))?$tiebreaks[1]->total:0,
        );

        return view('project.statistics', [
            'queries_status_json' => json_encode($queries_status),
            'judgments_json' => json_encode($judgments),
            'completed_queries' => $completed_queries,
            'total' => $total,
            'judgments' => $judgments,
            'users' => $users]
        );
    }

    public function userRanking()
    {
        $users = User::userStatsData(False); 

        return view('project.ranking', compact('users'));
    }
}
