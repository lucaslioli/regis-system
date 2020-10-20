<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Judgment;
use Illuminate\Http\Request;

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
        return view('judgments.create');
    }

    // store

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Judgment  $judgment
     * @return \Illuminate\Http\Response
     */
    public function edit(Judgment $judgment)
    {
        // Uses the same view as create
        return view('judgment.create', ['judgment' => $judgment]);
    }

    // update

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
