@extends('layouts.app')

@section('include')
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        <div class="accordion" id="accordion-queries">

            {{-- CREATE AND EDIT --}}

            <div class="card">

                <div class="card-header" data-toggle="collapse"
                    data-target="#collapse-create" aria-expanded="true" aria-controls="collapse-create">
                    <h2 class="mb-0 head-accordion">
                        <span>@isset($query->exists) Query id {{ $query->id }} @else New Query @endisset</span>
                        <i class="fas fa-chevron-down"></i>
                    </h2>
                </div>

                <div id="collapse-create" class="collapse show" data-parent="#accordion-queries">
                    <div class="card-body">

                        <form method="POST" action="/queries/{{ $query->id ?? 'store' }}" id="form-create-query">
                            @csrf
                            @isset($query->exists)
                                @method('PUT')
                            @endisset

                            <div class="row">
                                {{-- title --}}
                                <div class="form-group col-8">
                                    <label for="title"><strong>Query title</strong></label>
                                    <input class="form-control @error('title') is-invalid @enderror" 
                                        value="{{ old('title', $query->title ?? '') }}" required
                                        type="text" id="title" name="title" placeholder="Inform the query">
                        
                                    @error('title')
                                        <p class="text-danger">{{ $errors->first('title') }}</p>
                                    @enderror
                                </div>

                                {{-- id --}}
                                <div class="form-group col-4">
                                    <label for="title"><strong>Query ID</strong></label>
                                    <input class="form-control @error('qry_id') is-invalid @enderror" 
                                        value="{{ old('qry_id', $query->qry_id ?? '') }}" required
                                        type="text" id="qry_id" name="qry_id" placeholder="Inform the query ID">
                        
                                    @error('qry_id')
                                        <p class="text-danger">{{ $errors->first('qry_id') }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- description --}}
                            <div class="form-group">
                                <label for="description"><strong>Description</strong></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Describe the information need"
                                    id="description" name="description" required
                                    rows="3">{{ old('description', $query->description ?? '') }}</textarea>

                                @error('description')
                                    <p class="text-danger">{{ $errors->first('description') }}</p>
                                @enderror
                            </div>

                            {{-- narrative --}}
                            <div class="form-group">
                                <label for="narrative"><strong>Narrative</strong></label>
                                <textarea class="form-control @error('narrative') is-invalid @enderror"
                                    placeholder="Describe what documents you consider relevant"
                                    id="narrative" name="narrative" required
                                    rows="3">{{ old('narrative', $query->narrative ?? '') }}</textarea>

                                @error('narrative')
                                    <p class="text-danger">{{ $errors->first('narrative') }}</p>
                                @enderror
                            </div>

                            <div class="form-row d-flex justify-content-end">
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-block btn-success" id="btn-create">
                                        <i class="fas fa-plus"></i> Submit
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>

            {{-- MULTIPLE QUERIES --}}
            
            @if(!isset($query->exists))
            
                <div class="card">

                    <div class="card-header" data-toggle="collapse"
                        data-target="#collapse-upload" aria-expanded="true" aria-controls="collapse-upload">
                        <h2 class="mb-0 head-accordion">
                            <span>Upload multiple queries using a XML file</span>
                            <i class="fas fa-chevron-down"></i>
                        </h2>
                    </div>

                    <div id="collapse-upload" class="collapse" data-parent="#accordion-queries">
                        <div class="card-body">

                            <form method="POST" action="/queries/{{ $query->id ?? 'store' }}" 
                                enctype="multipart/form-data" id="form-create-query">
                                @csrf

                                <div class="form-row mb-0">

                                    <div class="custom-file col-md-8 mb-0">
                                        <input class="custom-file-input @error('xml_file') is-invalid @enderror" accept="text/xml"
                                            type="file" id="xml_file" name="xml_file" value="{{ old('xml_file') }}">
                                        <label class="custom-file-label" for="xml_file">Select a XML file (recomended size of 2M)</label>

                                        @error('xml_file')
                                            <p class="text-danger">{{ $errors->first('xml_file') }}</p>
                                        @enderror
                                    </div>

                                    <br><br>

                                    <div class="form-group col-md-4 mb-0">
                                        <button type="submit" class="btn btn-block btn-primary" id="btn-xml" onclick="start_loading(this)">
                                            <i class="fas fa-cogs"></i> Submit
                                        </button>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-12 d-flex">
                                        <a href="{{ asset('examples/queries_example.xml') }}" target="blank">
                                            <i class="fas fa-link"></i> XML example file
                                        </a>
                                        <p class="text-muted ml-3">* Repeated queries will be ignored</p>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>

            @endif

            
            {{-- CORRELATE QUERIES WITH DOCUMENTS --}}
            
            @if(!isset($query->exists))
                <div class="card">

                    <div class="card-header" data-toggle="collapse" 
                        data-target="#collapse-correlate" aria-expanded="true" aria-controls="collapse-correlate">
                        <h2 class="mb-0 head-accordion">
                            <span>Correlate queries with documents using JSON file</span>
                            <i class="fas fa-chevron-down"></i>
                        </h2>
                    </div>

                    <div id="collapse-correlate" class="collapse" data-parent="#accordion-queries">
                        <div class="card-body">

                            <form method="POST" action="/queries/documents" enctype="multipart/form-data" id="queries-documents">
                                @csrf

                                <div class="form-row">

                                    <div class="form-group col-md-8">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="json_file" name="json_file" accept="text/xml"
                                                accept="application/json" required>
                                            <label class="custom-file-label" for="json_file">Select JSON file</label>
                                        </div>

                                        @if($errors->has('json_file.*'))
                                            <p class="text-danger">{{ $errors->first('json_file') }}</p>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-4">
                                        <button type="submit" class="btn btn-dark btn-block" id="btn-json" onclick="start_loading(this)">
                                            <i class="fas fa-cog"></i> Process file
                                        </button>
                                    </div>

                                </div>

                            </form>

                        </div>
                    </div>

                </div>

            @endif

        </div>

        {{-- DISPLAY RESPONSE --}}

        <div id="response-text">
            @if(isset($response))
                <div class="{{ Str::contains($response, 'ERROR') ? 'text-danger' : 'text-success' }}" role="alert"> {{ $response }} </div>

                @if(isset($queries))
                    Total of {{ $queries }} querie(s) inserted. <br>
                @endif

                @if(isset($ignored) && $ignored != 0)
                    Total of {{ $ignored }} querie(s) ignored.
                @endif
            @endif
        </div>

    </div>

@endsection

@section('scripts')

    <script >
        /* show file value after file selected */
        document.getElementById('xml_file').addEventListener('change',function(e){
            var fileName = document.getElementById("xml_file").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });

        /* show file value after file selected */
        document.getElementById('json_file').addEventListener('change',function(e){
            var fileName = document.getElementById("json_file").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });

        $(function(){
            $('form').on('submit', function(event){
                event.stopPropagation();

                $("#btn-create").prop('disabled', true);
                $("#btn-xmls").prop('disabled', true);
                $("#btn-docs").prop('disabled', true);
            });
        });

        function start_loading(OBJ) {
            if ((OBJ.id == 'btn-xml' && $("#xml_file").val() == "") && (OBJ.id == 'btn-doc' && $("#json_file").val() == ""))
                return;

            OBJ.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

            $("#response-text").html('<div class="text-danger" role="alert">Processing data...</div>');
        };
    </script>

@endsection