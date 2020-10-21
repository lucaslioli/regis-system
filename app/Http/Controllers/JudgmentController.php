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
        
        $judgments = Judgment::where('id', $qry)
            ->orWhere('judgment', 'LIKE', "%$qry%")
            ->orWhere('observation', 'LIKE', "%$qry%")
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
        // Test the current page that the user is annotation and return
        if(Auth::user()->current_query != NULL){
            $query = Query::where('id', Auth::user()->current_query)->first();

            
            // Get documents judged by the user for the query
            $documents_judged = Auth::user()->documentsJudgedByQuery($query->id);

            // Select a document to be annotated
            $document = Document::whereIn('id', $query->getDocumentsIds())
                    ->whereNotIn('id', $documents_judged)
                    ->first();

            $progress = [
                "document" => count($documents_judged)+1,
                "bar" => count($documents_judged)*100/count($query->documents)
            ];

        }else{
            // Get queries with documents related
            $queries_with_documents = DB::table('document_query')
                ->distinct()->pluck('query_id')->all();

            if(!$queries_with_documents)
                return view('judgments.create');

            // Get queries already annotated by the user
            $queries_judged = Auth::user()->getQueriesJudged();

            // Get the first query with 1 annotator
            $query = Query::where('annotators', 1)
                ->whereIn('id', $queries_with_documents)
                ->whereNotIn('id', $queries_judged)->first();

            // Get the first query
            if(!$query)
                $query = Query::where('annotators', '<', 2)
                    ->whereIn('id', $queries_with_documents)
                    ->whereNotIn('id', $queries_judged)->first();

            $document = Document::whereIn('id', $query->documents)->first();

            $progress = ["document" => 1, "bar" => 0];

            // Update the queries annotators and users current page
            $query->increaseAnnotators();
            Auth::user()->setCurrentQuery($query->id);
        }

        $document->text_file = highlightWords($document->text_file, removeStopWords($query->title));

        return view('judgments.create', [
            'query' => $query,
            'document' => $document,
            'progress' => (object)$progress
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
        $validated_judgment = $this->validateJudgment();
        $validated_judgment['user_id'] = Auth::user()->id;

        // Register the judgment
        Judgment::create($validated_judgment);

        $query = Auth::user()->getCurrentQuery();
        $documents_judged = Auth::user()->documentsJudgedByQuery($query->id);

        // Update the status if all documents have been judged
        if(count($query->documents) == count($documents_judged)){
            if($query->status == "Semi Complete")
                $query->setStatus("Complete");
            else
                $query->setStatus("Semi Complete");

            Auth::user()->setCurrentQuery(NULL);
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
        $documents_judged = Auth::user()->documentsJudgedByQuery($judgment->queryy->id);

        $progress = [
            "document" => count($documents_judged),
            "bar" => count($documents_judged)*100/count($judgment->queryy->documents)
        ];

        $judgment->document->text_file = highlightWords(
            $judgment->document->text_file, 
            removeStopWords($judgment->queryy->title));

        // Uses the same view as create
        return view('judgments.create', [
            'query' => $judgment->queryy,
            'document' => $judgment->document,
            'progress' => (object)$progress,
            'judgment' => $judgment
        ]);
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
