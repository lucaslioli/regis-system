<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;

use SimpleXMLElement;
use Exception;

class DocumentController extends Controller
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
        $this->authorize('id-admin');

        $document->text_file = textToHtml($document->text_file);

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
        $this->authorize('id-admin');

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
        $this->authorize('id-admin');

        $request->validate([
            'xml_files' => 'required',
            'xml_files.*' => 'mimes:xml'
        ]);

        $dbefore = Document::count();
        $dIgnored = "";
        
        $files = $request->file('xml_files');

        foreach($files as $file){
            $xmlString = file_get_contents($file);
            $xml = simplexml_load_string($xmlString);

            if ($xml === false)
                return response()
                    ->view('documents.create', ['response' => "ERROR: Failed loading XML from ".$file->getClientOriginalName()], 400);

            foreach($xml->doc as $doc)
                if(!$this->create_document_xml($doc))
                    $dIgnored .= $doc->field[0] . ", ";
        }

        $data = array(
            "response" => "Completed!",
            "documents" => (Document::count() - $dbefore),
            "ignored" => substr($dIgnored, 0, -2)
        );

        return response()
            ->view('documents.create', $data, 200);
    }

    public function store_from_path(Request $request)	
    {
        $this->authorize('id-admin');

        $request->validate([
            'directory' => 'required'
        ]);
            
        $dbefore = Document::count();
        $dIgnored = "";

        $path = $request->get('directory');
        $path = (substr($path, -1) == '/') ? $path: $path.'/';

        // The result of file_exists is cached
        clearstatcache();

        if(!file_exists($path))
            return response()
                ->view('documents.create', ['response' => "ERROR: Directory not found, ".$path], 400);
        
        $files = glob($path.'*.{xml}', GLOB_BRACE);

        foreach($files as $file){
            $xmlString = file_get_contents($file);
            $xml = simplexml_load_string($xmlString);

            if ($xml === false)
                return response()
                    ->view('documents.create', ['response' => "ERROR: Failed loading XML from ".$file->getClientOriginalName()], 400);

            foreach($xml->doc as $doc){
                if(!$this->create_document_xml($doc))
                    $dIgnored .= $doc->field[0] . ", ";
            }
        }

        $data = array(
            "response" => "Completed!",
            "documents" => (Document::count() - $dbefore),
            "ignored" => substr($dIgnored, 0, -2)
        );

        return response()
            ->view('documents.create', $data, 200);
    }

    public function create_document_xml(SimpleXMLElement $doc)
    {
        $test_document = Document::where('doc_id', $doc->field[0])->first();

        if($test_document)
            return false;

        $doc->field[1] = str_replace('%20', '_', $doc->field[1]);
        $doc->field[1] = str_replace(' ', '_', $doc->field[1]);
        $doc->field[1] = str_replace('%', '', $doc->field[1]);

        try{
            return Document::create([
                'doc_id' => $doc->field[0],
                'file_name' => $doc->field[1],
                'file_type' => $doc->field[2],
                'text_file' => $doc->field[3],
            ]);

        } catch (Exception $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062)
                return false;
        }
    }

    /**	
     * Upload PDF files to the project
     *	
     * @param  \Illuminate\Http\Request  $request	
     * @return \Illuminate\Http\Response	
     */	
    public function upload(Request $request)
    {
        $this->authorize('id-admin');

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
        $this->authorize('id-admin');

        $id = $document->id;
        $document->delete();

        return response("Document ".$id." deleted successfully!", 200);
    }

    public function search(Request $request)
    {
        $this->authorize('id-admin');

        $qry = $request->get('qry');
        
        $documents = Document::where('id', $qry)
            ->orWhere('doc_id', 'LIKE', "%$qry%")
            ->orWhere('file_name', 'LIKE', "%$qry%")
            ->orWhere('file_type', 'LIKE', "%$qry%")
            ->paginate(15);

        return view('documents.index', compact('documents'));
    }
}
