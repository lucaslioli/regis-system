<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Judgment;
use App\Models\User;

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
        // $this->authorize('id-admin');

        $users = User::paginate(15);

        return view('users.index', compact('users'));
    }

    public function search(Request $request)
    {
        // $this->authorize('id-admin');

        $qry = $request->get('qry');
        
        $users = User::where('id', $qry)
            ->orWhere('name', 'LIKE', "%$qry%")
            ->orWhere('email', 'LIKE', "%$qry%")
            ->paginate(15);

            return view('users.index', compact('users'));
    }

}
