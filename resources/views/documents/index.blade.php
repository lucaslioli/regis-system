@extends('layouts.app')

@section('include')
    {{-- <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script> --}}
@endsection

@section('content')

    <div class="container">

        <h1>@isset($document->exists) Document id {{ $document->id }} @else New Document @endisset</h1>
        <hr>

    </div>

@endsection

@section('scripts')
    
    <script type="text/javascript">
        // DELETE
    </script>

@endsection