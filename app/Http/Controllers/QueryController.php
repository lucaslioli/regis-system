<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;

use App\Models\Query;
use App\Models\Document;

class QueryController extends Controller
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
        $this->authorize('id-admin');

        $queries = Query::paginate(15);

        return view('queries.index', compact('queries'));
    }

    /**
     * Display a view to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('id-admin');

        return view('queries.create');
    }

    /**	
     * Store a newly created resource in storage.
     *	
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response	
     */	
    public function store(Request $request)	
    {
        $this->authorize('id-admin');

        $qBefore = Query::count();
        $qIgnored = "";

        if($request->file('multiple_queries_file') != NULL){
            $request->validate([
                'multiple_queries_file' => 'required',
                'multiple_queries_file' => 'mimes:xml,html'
            ]);
        
            $file = $request->file('multiple_queries_file');

            $xmlString = file_get_contents($file);
            $xml = simplexml_load_string($xmlString);

            // Test valid XML
            if ($xml === false)
                return response()
                    ->view('queries.create', ['response' => "ERROR: Failed loading XML from ".$file->getClientOriginalName()], 400);

            if (!$xml->top || !$xml->top[0]->num || !$xml->top[0]->title || !$xml->top[0]->desc || !$xml->top[0]->narr)
                return response()
                    ->view('queries.create', ['response' => "ERROR: The file does not have the expected fields. See the example."], 400);

            foreach($xml->top as $top){
                // Test duplicate query
                $test_query = Query::where('qry_id', $top->num)
                    ->orWhere('title', $top->title)
                    ->first();

                if($test_query){
                    $qIgnored .= $top->num . ", ";
                    continue;
                }

                Query::create([
                    'qry_id' => $top->num,
                    'title' => $top->title,
                    'description' => $top->desc,
                    'narrative' => $top->narr
                ]);
            }
            
        }else
            Query::create($this->validateQuery());

        $data = array(
            "response" => "Completed!",
            "queries" => (Query::count() - $qBefore),
            "ignored" => substr($qIgnored, 0, -2)
        );

        return response()
            ->view('queries.create', $data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Query  $query
     * @return \Illuminate\Http\Response
     */
    public function edit(Query $query)
    {
        $this->authorize('id-admin');

        // Uses the same view as create
        return view('queries.create', ['query' => $query]);
    }

    /**
     * Updates the query
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Query  $query
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Query $query)
    {
        $this->authorize('id-admin');

        // $this->authorize('update', $query);
        $query->update($this->validateQuery());

        return redirect(route("queries.index"));
    }

    public function validateQuery()
    {
        return request()->validate([
            'qry_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'narrative' => 'required'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Query  $query
     * @return \Illuminate\Http\Response
     */
    public function destroy(Query $query)
    {
        $this->authorize('id-admin');

        $id = $query->id;
        $query->delete();

        return response("Query ".$id." deleted successfully!", 200);
    }

    public function search(Request $request)
    {
        $this->authorize('id-admin');

        $qry = $request->get('qry');
        
        $queries = Query::where('id', $qry)
            ->orWhere('title', 'LIKE', "%$qry%")
            ->orWhere('description', 'LIKE', "%$qry%")
            ->paginate(15);

        return view('queries.index', compact('queries'));
    }

    /**	
     * Attach a newly rellation between query and document.
     *	
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response	
     */	
    public function attachDocuments(Request $request)
    {
        $this->authorize('id-admin');

        $request->validate([
            'correlation_file' => 'required',
            'correlation_file' => 'mimes:xml'
        ]);

        $qBefore = DB::table('document_query')->count();
        $qIgnored = "";
        $dInvalid = "";
    
        $file = $request->file('correlation_file');

        $xmlString = file_get_contents($file);
        $xml = simplexml_load_string($xmlString);

        // Test valid XML
        if ($xml === false)
            return response()
                ->view('queries.create', ['response' => "ERROR: Failed loading XML from ".$file->getClientOriginalName()], 400);

        foreach($xml->top as $top){
            $query = Query::where('qry_id', $top->num)->first();

            // Test valid query
            if(!$query){
                $dInvalid .= $top->num . ", ";
                continue;
            }

            foreach ($top->doc->field as $doc_id) {
                $document = Document::where('doc_id', $doc_id)->first();

                // Test valid document
                if(!$document){
                    $dInvalid .= $doc_id . ", ";
                    continue;
                }

                // Test duplicate correlation
                $test_link = DB::table('document_query')
                    ->where('query_id', $query->id)
                    ->where('document_id', $document->id)
                    ->first();

                if($test_link){
                    $qIgnored .= "($top->num, $doc_id), ";
                    continue;
                }

                $query->documents()->attach($document);
            }
        }

        $data = array(
            "response" => "Completed!",
            "queries" => (DB::table('document_query')->count() - $qBefore),
            "ignored" => substr($qIgnored, 0, -2),
            "invalid" => substr($dInvalid, 0, -2)
        );

        return response()
            ->view('queries.create', $data, 200);
    }

    /**
     * Remove the specified correlation between query and document.
     *
     * @param  \App\Query  $query
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function detachDocument(Query $query, Document $document)
    {
        $this->authorize('id-admin');

        $id = $query->id;
        
        $query->documents()->detach($document);

        return response("Correlation with document ".$id." deleted successfully!", 200);
    }
}
