<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JudgmentController extends Controller
{
    /**
     * Select a query and document to be displayed
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('judgments.create');
    }
}
