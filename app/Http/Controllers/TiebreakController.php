<?php

namespace App\Http\Controllers;

use DB;
use Auth;

use Illuminate\Http\Request;

use App\Models\Judgment;
use App\Models\Query;
use App\Models\Document;

class TiebreakController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all queries completed
        $completed_pairs = DB::table('document_query')
            ->where('judgments', 2)
            ->where('status', 'review')
            ->get();

        foreach ($completed_pairs as $pair) {
            // Get all judgments for the query ordered by document for each annotator
            $judge_1 = Judgment::where('query_id', $pair->query_id)
                ->where('document_id', $pair->document_id)->first();

            $judge_2 = Judgment::where('query_id', $pair->query_id)
                ->where('document_id', $pair->document_id)->skip(1)->first();

            // Extra check for bug cases
            if(!$judge_1 || !$judge_2)
                continue;

            // Compare if the judgments are the same and update the status
            if($judge_1->judgment != $judge_2->judgment)
                $judge_1->queryy->documents()->updateExistingPivot(
                    $judge_1->document->id, ['status' => 'tiebreak']);
            else
                $judge_1->queryy->documents()->updateExistingPivot(
                    $judge_1->document->id, ['status' => 'agreed']);
        }
                
        // Return all the pairs
        $tiebreaks = DB::table('document_query')
            ->join('queries', 'queries.id', '=', 'document_query.query_id')
            ->join('documents', 'documents.id', '=', 'document_query.document_id')
            ->select('document_query.*', 'queries.title', 'documents.file_name')
            ->where('document_query.status', 'tiebreak')
            ->whereNotIn('query_id', Auth::user()->queries->map->id)
            ->paginate(15);

        return view('tiebreaks.index', compact('tiebreaks'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Query  $query
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Query $query, Document $document)
    {
        // Set up tiebreak variables
        $tiebreak = Judgment::where('query_id', $query->id)
            ->where('document_id', $document->id)
            ->pluck('judgment')
            ->all();

        // Update the text_file with the markers
        $document->text_file = highlightWords($document->text_file, removeStopWords($query->title));

        // Uses the same view as create
        return view('judgments.create', [
            'query' => $query,
            'document' => $document,
            'tiebreak' => $tiebreak
        ]);
    }

    public function search(Request $request)
    {
        $qry = $request->get('qry');

        $tiebreaks = DB::table('document_query')
            ->join('queries', 'queries.id', '=', 'document_query.query_id')
            ->join('documents', 'documents.id', '=', 'document_query.document_id')
            ->select('document_query.*, queries.title, documents.file_name')
            ->where('document_query.id', $qry)
            ->orWhere('queries.title', 'LIKE', "%$qry%")
            ->orWhere('documents.file_name', 'LIKE', "%$qry%")
            ->get();

        return view('tiebreaks.index', compact('tiebreaks'));
    }
}
