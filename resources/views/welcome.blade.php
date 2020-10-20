@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row d-flex flex-column align-items-center justify-content-center mt-5">
            <img src="{{ asset('images/regis-logo.png') }}" alt="Regis Logo" class="w-25">

            <div class="col-2 mt-2">
                <hr>
            </div>

            <h2 class="cover-heading mt-3">Retrieval Evaluation for Geoscientific Information Systems</h2>

            <p class="col-8 lead text-center">Welcome to REGIS, a tool that will help us to create a new multimodal test collection in geosicence domain. You can help us by using REGIS to provide relevance judgments about documents for specific queries previously selected.</p>

            @guest
                <div class="col-8 d-flex justify-content-center">
                    <a class="btn btn-outline-primary col-3 m-1" href="{{ route('login') }}">{{ __('Login') }}</a>
                    <a class="btn btn-primary col-3 m-1" href="{{ route('register') }}">{{ __('Register') }}</a>
                </div>
            @else
                <a class="btn btn-primary col-3" href="{{ route('judgments.create') }}">Start annotation</a>
            @endguest
        </div>
    </div>
   
@endsection
