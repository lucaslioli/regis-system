<?php

namespace App\Http\Controllers;

use DB;
use Auth;

use Illuminate\Http\Request;

use App\Models\Judgment;
use App\Models\Query;
use App\Models\Document;

class JudgmentController extends Controller
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
        $judgments = Judgment::where('user_id', Auth::user()->id)
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
        
        return view('judgments.index', compact('judgments'));
    }

    public function search(Request $request)
    {
        $qry = $request->get('qry');

        $judgments = DB::table('judgments')
            ->join('queries', 'queries.id', '=', 'judgments.query_id')
            ->join('documents', 'documents.id', '=', 'judgments.document_id')
            ->select('judgments.*')
            ->where('user_id', Auth::user()->id)
            ->where(function ($query) use ($qry) {
                return $query->where('judgment', 'LIKE', "%$qry%")
                ->orWhere('observation', 'LIKE', "%$qry%")
                ->orWhere('queries.title', 'LIKE', "%$qry%")
                ->orWhere('queries.qry_id', "$qry")
                ->orWhere('documents.doc_id', "$qry")
                ->orWhere('documents.file_name', 'LIKE', "%$qry%");
            })
            ->paginate(15);

        return view('judgments.index', compact('judgments'));
    }

    /**
     * Select a query and document to be displayed
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        if($user->id == 1)
            return view('judgments.create');

        $incomplete_query = 0;

        // Test if there is a query that the user is already judging
        if($user->current_query != NULL){
            $query = Query::where('id', $user->current_query)->first();

            // Cases when the query has been deleted
            if(!$query){
                $user->queries()->detach($query);
                $user->setCurrentQuery(NULL);
                return redirect(route('judgments.create'));
            }

            // Cases when the documents have been detached
            if(!count($query->documents)){
                $query->decreaseAnnotators();
                $user->queries()->detach($query);
                $user->setCurrentQuery(NULL);

                return redirect(route('judgments.create'));
            }
            
            // Get documents judged by the user for the query
            $documents_judged = $user->documentsJudgedByQuery($query->id);

            // Select a document to be annotated
            $document = Document::whereIn('id', $query->documents->map->id)
                ->whereNotIn('id', $documents_judged)
                ->first();

        }else{
            // Get queries with documents related
            $queries_with_documents = DB::table('document_query')
                ->distinct()->pluck('query_id')->all();

            if(!$queries_with_documents)
                return view('judgments.create');

            // Get queries attached to the user, judged or skipped
            $queries_judged = $user->queries->map->id;

            // Check if there are any query that get new documents attached
            // after the user complete it and not skipped
            foreach ($user->queries as $query) {
                if($user->querySkipped($query->id))
                    continue;

                $documents_judged = $user->documentsJudgedByQuery($query->id);
                if(count($query->documents) > count($documents_judged)){
                    $incomplete_query = $query;
                    break;
                }
            }

            // To avoid pick a query to a user that will not annotate, wait to click next
            if(!$incomplete_query && count($user->queries) > 0 && !request('next'))
                return view('judgments.create', ['next_query' => true]);

            // Get the first query with 1 annotator
            if(!$incomplete_query)
                $query = Query::where('annotators', 1)
                    ->whereIn('id', $queries_with_documents)
                    ->whereNotIn('id', $queries_judged)
                    ->orderByDesc('description')->first();
            
            else{
                $query = $incomplete_query;
                $incomplete_query = 1;
            }

            // Get the first query available, prioritizing queries with descriptions
            if(!$query)
                $query = Query::where('annotators', '<', 2)
                    ->whereIn('id', $queries_with_documents)
                    ->whereNotIn('id', $queries_judged)
                    ->orderByDesc('description')->first();
            
            if(!$query)
                return view('judgments.create');

            // Get documents judged by the user for the query
            $documents_judged = $user->documentsJudgedByQuery($query->id);

            $document = Document::whereIn('id', $query->documents->map->id)
                ->whereNotIn('id', $documents_judged)->first();

            // Update the queries annotators and users current page
            if(!$incomplete_query){
                $query->increaseAnnotators();
                $user->queries()->attach($query);
            }
            $user->setCurrentQuery($query->id);
        }

        if(count($documents_judged)>0)
            $progress = [
                "document" => count($documents_judged)+1,
                "bar" => round(count($documents_judged)*100/count($query->documents), 1)
            ];
        else
            $progress = ["document" => 1, "bar" => 0];

        // Update the text_file with the markers
        $document->text_file = highlightWords($document->text_file, $query->title);
        $markers = substr_count($document->text_file, '<mark>');

        return view('judgments.create', [
            'query' => $query,
            'document' => $document,
            'markers' => $markers,
            'progress' => (object)$progress,
            'incomplete_query' => $incomplete_query
        ]);
    }

    /**	
     * Store a newly created resource in storage.
     *	
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response	
     */	
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated_judgment = $this->validateJudgment();
        $validated_judgment['user_id'] = $user->id;

        // Register the judgment
        $judgment = Judgment::create($validated_judgment);

        // Increments the judgments counter for the document-query pair
        $pivot_judgments = $judgment->queryy->documentJudgments($judgment->document->id);
        $judgment->queryy->documents()->updateExistingPivot(
            $judgment->document->id, ['judgments' => $pivot_judgments+1]);

        if(!$judgment->untie){
            $query = $user->getCurrentQuery();
            $documents_judged = $user->documentsJudgedByQuery($query->id);

            // Update the status if all documents have been judged
            if(count($query->documents) <= count($documents_judged)){
                if($query->status == "Semi Complete")
                    $query->setStatus("Complete");
                else
                    $query->setStatus("Semi Complete");

                $user->setCurrentQuery(NULL);
            }
        }else{
            $judgment->queryy->documents()->updateExistingPivot(
                $judgment->document->id, ['status' => 'solved']);
        }

        return redirect(route('judgments.create'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Judgment  $judgment
     * @return \Illuminate\Http\Response
     */
    public function edit(Judgment $judgment)
    {
        if($judgment->user_id != Auth::user()->id && !Auth::user()->isAdmin())
            return abort(403);

        $documents_judged = Auth::user()->documentsJudgedByQuery($judgment->queryy->id);

        // Set up the progress for the query
        $progress = [
            "document" => count($documents_judged),
            "bar" => count($documents_judged)*100/count($judgment->queryy->documents)
        ];

        // Update the text_file with the markers
        $judgment->document->text_file = highlightWords(
            $judgment->document->text_file, $judgment->queryy->title);

        $markers = substr_count($judgment->document->text_file, '<mark>');

        // Response data
        $data = array(
            'query' => $judgment->queryy,
            'document' => $judgment->document,
            'markers' => $markers,
            'progress' => (object)$progress,
            'judgment' => $judgment
        );

        // In the case of a tiebreaker judgment
        if($judgment->untie){
            // Set up tiebreak variables
            $tiebreak = Judgment::where('query_id', $judgment->queryy->id)
                ->where('document_id', $judgment->document->id)
                ->get();

            $observations = $tiebreak->pluck('observation')->all();
            $tiebreak = $tiebreak->pluck('judgment')->all();

            $data['observations'] = $observations;
            $data['tiebreak'] = $tiebreak;
        }

        // Uses the same view as create
        return view('judgments.create', $data);
    }

    /**
     * Updates the query
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Judgment  $judgment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Judgment $judgment)
    {
        // $this->authorize('update', $query);
        $judgment->update($this->validateJudgment());

        return redirect(route("judgments.index"));
    }

    public function validateJudgment()
    {
        return request()->validate([
            'judgment' => ['required', 'in:Very Relevant,Relevant,Marginally Relevant,Not Relevant'],
            'observation' => 'nullable',
            'untie' => 'required|bool',
            'query_id' => 'required',
            'document_id' => 'required'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Judgment  $judgment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Judgment $judgment)
    {
        $id = $judgment->id;
        $judgment->delete();

        return response("Judgment ".$id." deleted successfully!", 200);
    }
}
