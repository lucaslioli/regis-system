<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documents = Document::paginate(15);

        return view('documents.index', compact('documents'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        return view('documents.show', [
            'document' => $document
        ]);
    }

    /**
     * Display a view to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('documents.create');
    }

    /**	
     * Store a newly created resource in storage.
     *	
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response	
     */	
    public function store(Request $request)	
    {
        $request->validate([
            'xml_files' => 'required',
            'xml_files.*' => 'mimes:xml'
        ]);

        $dbefore = Document::count();
        
        $files = $request->file('xml_files');

        foreach($files as $file){
            $xmlString = file_get_contents($file);
            $xml = simplexml_load_string($xmlString);

            if ($xml === false)
                return response()
                    ->view('documents.create', ['response' => "ERROR: Failed loading XML from ".$file->getClientOriginalName()], 400);

            foreach($xml->doc as $doc){
                $test_document = Document::where('doc_id', $doc->field[0])->first();

                if($test_document)
                    continue;

                    Document::create([
                    'doc_id' => $doc->field[0],
                    'file_name' => $doc->field[1],
                    'file_type' => $doc->field[2],
                    'text_file' => $doc->field[3],
                ]);
            }
        }

        $data = array(
            "response" => "Success!",
            "documents" => (Document::count() - $dbefore),
        );

        return response()
            ->view('documents.create', $data, 200);
    }

    /**	
     * Upload PDF files to the project
     *	
     * @param  \Illuminate\Http\Request  $request	
     * @return \Illuminate\Http\Response	
     */	
    public function upload(Request $request)
    {
        $request->validate([
            'doc_files' => 'required',
            'doc_files.*' => 'mimes:pdf,jpeg,png,gif'
        ]);

        $files = $request->file('doc_files');

        foreach($files as $file)
            $file->move(public_path("documents/"), $file->getClientOriginalName());

        $data = array(
            "response" => "Files uploaded successfully!"
        );

        if($request->get('show-document'))
            return redirect(route("documents.show", $request->get('show-document')));

        return response()
            ->view('documents.create', $data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        $id = $document->id;
        $document->delete();

        return response("Document ".$id." deleted successfully!", 200);
    }

    public function search(Request $request)
    {
        $qry = $request->get('qry');
        
        $documents = Document::where('id', $qry)
            ->orWhere('doc_id', 'LIKE', "%$qry%")
            ->orWhere('file_name', 'LIKE', "%$qry%")
            ->orWhere('file_type', 'LIKE', "%$qry%")
            ->paginate(15);

        return view('documents.index', compact('documents'));
    }
}
