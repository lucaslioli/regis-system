<?php

namespace App\Http\Controllers;

use App\Models\Query;
use Illuminate\Http\Request;

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
        // $this->authorize('id-admin');

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
        $qBefore = Query::count();
        $qIgnoreded = 0;

        if($request->file('xml_file') != NULL){
            $request->validate([
                'xml_files' => 'required',
                'xml_files' => 'mimes:xml'
            ]);
        
            $file = $request->file('xml_file');

            $xmlString = file_get_contents($file);
            $xml = simplexml_load_string($xmlString);

            if ($xml === false)
                return response()
                    ->view('queries.create', ['response' => "ERROR: Failed loading XML from ".$file->getClientOriginalName()], 400);

            foreach($xml->top as $top){
                $test_query = Query::where('qry_id', $top->num)
                    ->orWhere('title', $top->title)
                    ->first();

                if($test_query){
                    $qIgnoreded++;
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
            Query::create($this->validadeQuery());

        $data = array(
            "response" => "Success!",
            "queries" => (Query::count() - $qBefore),
            "ignored" => $qIgnoreded
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
        // $this->authorize('update', $query);
        $query->update($this->validadeQuery());

        return redirect(route("queries.index"));
    }

    public function validadeQuery()
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
        $id = $query->id;
        $query->delete();

        return response("Query ".$id." deleted successfully!", 200);
    }

    public function search(Request $request)
    {
        $qry = $request->get('qry');
        
        $queries = Query::where('id', $qry)
            ->orWhere('title', 'LIKE', "%$qry%")
            ->orWhere('description', 'LIKE', "%$qry%")
            ->paginate(15);

        return view('queries.index', compact('queries'));
    }

    public function setDocuments(Request $resquest)
    {
        // TO DO
        $data = array();
        return response()
            ->view('queries.create', $data, 200);
    }
}
