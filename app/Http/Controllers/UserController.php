<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Query;

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
     * @param  \App\Query  $query
     * @return \Illuminate\Http\Response	
     */	
    public function skipQuery(Query $query)
    {
        $user = Auth::user();

        $user->setCurrentQuery(NULL);

        $query->decreaseAnnotators();

        $user->queries()->updateExistingPivot(
            $query->id, ['skip' => 1]);

        $user->eraseJudgments($query);

        return redirect(route('judgments.create'));
    }

    /**
     * Delete user judgments for a specific query
     *
     * @param  \App\User   $user
     * @return \Illuminate\Http\Response
     */
    public function eraseCurrentJudgments(User $user)
    {
        $this->authorize('id-admin');

        $query = Query::find($user->current_query);

        $user->eraseJudgments($query);

        return response("User judgments for current query were deleted successfully!", 200);
    }

    /**
     * Reset all skipped queries by a user
     * 
     * @param  \App\User   $user
     * @return \Illuminate\Http\Response
     */
    public function resetSkippedQueries(User $user)
    {
        $this->authorize('id-admin');

        foreach ($user->queriesSkipped(False) as $query_id){
            $query = Query::find($query_id);

            if($query->status != "Complete")
                $user->queries()->detach($query);
        }

        return response("Skipped queries (and not completed yet) were reseted!", 200);
    }

    /**
     * Remove the specified correlation between user and query.
     *
     * @param  \App\User   $user
     * @param  \App\Query  $query
     * @return \Illuminate\Http\Response
     */
    public function detachQuery(User $user, Query $query)
    {
        $this->authorize('id-admin');

        if(count($user->documentsJudgedByQuery($query->id)) > 0)
            return response("ERROR: The pair already has judgments", 400);

        $query->decreaseAnnotators();
        
        $user->queries()->detach($query);
        $user->setCurrentQuery(NULL);

        return response("Correlation with query ".$query->id." deleted successfully!", 200);
    }

    /**
     * Generate link to recover user password
     * 
     * @param  \App\User   $user
     * @return \Illuminate\Http\Response
     */
    public function recoverPassword(User $user)
    {
        $this->authorize('id-admin');

        //Create Password Reset Token
        $token = app('auth.password.broker')->createToken($user);

        $link = url(route('password.reset', ['token' => $token], false));

        return response("Password recover link for user <b>".$user->name."</b>: <i>".$link."</i>", 200);
    }

}
