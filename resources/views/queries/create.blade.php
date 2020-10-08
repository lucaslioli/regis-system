@extends('layouts.app')

@section('content')

<div class="container">

    <h1>@isset($query->exists) Query id {{ $query->id }} @else New Query @endisset</h1>
    <hr>

    <form method="POST" action="/queries/{{ $query->id ?? 'store' }}">
        @csrf
        @isset($query->exists)
            @method('PUT')
        @endisset

        <div class="form-group">
            <label for="title"><strong>Query title</strong></label>
            <input class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $query->title ?? '') }}"
                type="text" id="title" name="title" placeholder="Inform the query">

            @error('title')
                <p class="text-danger">{{ $errors->first('title') }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="description"><strong>Description</strong></label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                placeholder="Describe the information need"
                id="description" name="description" rows="3">{{ old('description', $query->description ?? '') }}</textarea>

            @error('description')
                <p class="text-danger">{{ $errors->first('description') }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="narrative"><strong>Narrative</strong></label>
            <textarea class="form-control @error('narrative') is-invalid @enderror"
                placeholder="Describe what documents you consider relevant"
                id="narrative" name="narrative" rows="3">{{ old('narrative', $query->narrative ?? '') }}</textarea>

            @error('narrative')
                <p class="text-danger">{{ $errors->first('narrative') }}</p>
            @enderror
        </div>

        <div class="row d-flex justify-content-end">
            <div class="col-4">
                <button type="submit" class="btn btn-block btn-success">Submit</button>
            </div>
        </div>

        @if(!isset($query->exists))
        
            <br><hr>
            <h3>Upload multiple queries using a XML file</h3>
            <br>

            <div class="custom-file">
                <label class="custom-file-label" for="xml_file">Select a XML file (recomended size of 2M)</label>
                <input class="custom-file-input @error('xml_file') is-invalid @enderror"
                    type="file" id="xml_file" name="xml_file" value="{{ old('xml_file') }}">

                @error('xml_file')
                    <p class="text-danger">{{ $errors->first('xml_file') }}</p>
                @enderror
            </div>

            <br><br>

            <div class="row d-flex justify-content-end">
                <div class="col-4">
                    <button type="submit" class="btn btn-block btn-primary">Submit</button>
                </div>
            </div>

        @endif

    </form>
</div>

@endsection