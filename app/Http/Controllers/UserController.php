<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Query;
use App\Models\Document;
use App\Models\Judgment;

class UserController extends Controller
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

        $users = User::paginate(15);

        return view('users.index', compact('users'));
    }

    public function search(Request $request)
    {
        $this->authorize('id-admin');

        $qry = $request->get('qry');
        
        $users = User::where('id', $qry)
            ->orWhere('name', 'LIKE', "%$qry%")
            ->orWhere('email', 'LIKE', "%$qry%")
            ->paginate(15);

            return view('users.index', compact('users'));
    }

    public function makeAdmin(User $user)
    {
        $this->authorize('id-admin');

        $user->update(['role' => 'admin']);

        return redirect(route('users.index'));
    }

    public function revokeAdmin(User $user)
    {
        $this->authorize('id-admin');

        if($user->id != 1)
            $user->update(['role' => 'default']);

        return redirect(route('users.index'));
    }

    /**	
     * Skip query for logged user and delete al judgments related
     *	
     * @param  \Illuminate\Http\QUery  $query
     * @return \Illuminate\Http\Response	
     */	
    public function skipQuery(Query $query)
    {
        $user = Auth::user();

        $user->setCurrentQuery(NULL);

        $query->decreaseAnnotators();

        $user->queries()->updateExistingPivot(
            $query->id, ['skip' => 1]);

        // Documents judged by the user for the query
        $documents_judged = $user->documentsJudgedByQuery($query->id);

        $documents = Document::whereIn('id', $documents_judged)->get();

        // Before delete judgments, update doc-query pairs pivot columns
        foreach ($documents as $document) {
            $pivot_judgments = $query->documentJudgments($document->id);
            $query->documents()->updateExistingPivot(
                $document->id, [
                    'judgments' => $pivot_judgments-1, 
                    'status' => 'review']);
        }

        // Delete judgments
        Judgment::where('query_id', $query->id)
            ->where('user_id', $user->id)
            ->delete();

        return redirect(route('judgments.create'));
    }

}
