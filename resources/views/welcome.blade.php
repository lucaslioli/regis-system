@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row d-flex flex-column align-items-center justify-content-center mt-5">
            <img src="{{ asset('images/regis-logo.png') }}" alt="REGIS Logo" class="w-25">

            <div class="col-2 mt-2">
                <hr>
            </div>

            <h2 class="cover-heading mt-3">Retrieval Evaluation for Geoscientific Information Systems</h2>

            <p class="col-7 lead text-center">Welcome to REGIS, a test collection for Information Retrieval for Geoscientific documents. You can help us providing relevance judgements for documents retrieved in response to queries.</p>

            @guest
                <div class="col-8 d-flex justify-content-center">
                    <a class="btn btn-outline-primary col-3 m-1" href="{{ route('login') }}">{{ __('Login') }}</a>
                    <a class="btn btn-primary col-3 m-1" href="{{ route('register') }}">{{ __('Register') }}</a>
                </div>
            @else
                @if(Auth::user()->judgments)
                    <a class="btn btn-primary col-3" href="{{ route('judgments.create') }}">Continue annotating</a>
                @else
                    <a class="btn btn-primary col-3" href="{{ route('judgments.create') }}">Start annotation</a>
                @endif
            @endguest
        </div>
    </div>
   
@endsection
