@extends('layouts.app')

@section('include')
    {{-- <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script> --}}
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        <div class="accordion" id="accordion-queries">

            {{-- CREATE --}}

            <div class="card">

                <div class="card-header" data-toggle="collapse"
                    data-target="#collapse-create" aria-expanded="true" aria-controls="collapse-create">
                    <h2 class="mb-0 head-accordion">
                            New Query 
                            <i class="fas fa-chevron-down"></i>
                    </h2>
                </div>

                <div class="collapse @if(!$errors->has('multiple_queries_file') && !$errors->has('correlation_file')) show @endif"
                    id="collapse-create" data-parent="#accordion-queries">
                    <div class="card-body">

                        <form method="POST" action="/queries/{{ $query->id ?? 'store' }}" id="form-create-query">
                            @csrf

                            <div class="row">
                                {{-- title --}}
                                <div class="form-group col-8">
                                    <label for="title"><strong>Query title</strong></label>
                                    <input class="form-control @error('title') is-invalid @enderror" 
                                        value="{{ old('title', $query->title ?? '') }}" required
                                        type="text" id="title" name="title" placeholder="Inform the query">
                        
                                    @error('title')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- id --}}
                                <div class="form-group col-4">
                                    <label for="title"><strong>Query ID</strong></label>
                                    <input class="form-control @error('qry_id') is-invalid @enderror" 
                                        value="{{ old('qry_id', $query->qry_id ?? '') }}" required
                                        type="text" id="qry_id" name="qry_id" placeholder="Inform the query ID">
                        
                                    @error('qry_id')
                                        <p class="text-danger">{{ $message }}</p>
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
                                    <p class="text-danger">{{ $message }}</p>
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
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-row d-flex justify-content-end">
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-block btn-success" id="btn-submit">
                                        <i class="fas fa-save"></i> Submit
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>

            {{-- UPLOAD MULTIPLE QUERIES --}}
            
            <div class="card">

                <div class="card-header" data-toggle="collapse"
                    data-target="#collapse-upload" aria-expanded="true" aria-controls="collapse-upload">
                    <h2 class="mb-0 head-accordion">
                        <span>Upload multiple queries using a XML file</span>
                        <i class="fas fa-chevron-down"></i>
                    </h2>
                </div>

                <div id="collapse-upload" class="collapse @error('multiple_queries_file') show @enderror" data-parent="#accordion-queries">
                    <div class="card-body">

                        <form method="POST" action="/queries/{{ $query->id ?? 'store' }}" 
                            enctype="multipart/form-data" id="form-multiple-query">
                            @csrf

                            <div class="form-row mb-0">

                                <div class="custom-file col-md-8 mb-0">
                                    <input class="custom-file-input @error('multiple_queries_file') is-invalid @enderror" 
                                        accept="text/xml" type="file" id="multiple_queries_file" name="multiple_queries_file"
                                        value="{{ old('multiple_queries_file') }}">
                                    <label class="custom-file-label" for="multiple_queries_file">Select a XML file (recomended size of 2M)</label>

                                    @error('multiple_queries_file')
                                        <p class="text-danger mt-4">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group col-md-4 mb-0">
                                    <button type="submit" class="btn btn-block btn-primary" id="btn-multiple" onclick="start_loading(this)">
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

            {{-- UPLOAD QUERIES AND DOCUMENTS CORRELATION --}}
            
            <div class="card">

                <div class="card-header" data-toggle="collapse" 
                    data-target="#collapse-correlate" aria-expanded="true" aria-controls="collapse-correlate">
                    <h2 class="mb-0 head-accordion">
                        <span>Correlate queries with documents using XML file</span>
                        <i class="fas fa-chevron-down"></i>
                    </h2>
                </div>

                <div id="collapse-correlate" class="collapse @error('correlation_file') show @enderror" data-parent="#accordion-queries">
                    <div class="card-body">

                        <form method="POST" action="/queries/attachDocuments" enctype="multipart/form-data" id="queries-documents">
                            @csrf

                            <div class="form-row mb-0">

                                <div class="form-group col-md-8 mb-0">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('correlation_file') is-invalid @enderror" 
                                            id="correlation_file" name="correlation_file"
                                            accept="text/xml" required>
                                        <label class="custom-file-label" for="correlation_file">Select XML file</label>
                                    </div>

                                    @if($errors->has('correlation_file.*'))
                                        <p class="text-danger mt-4">{{ $errors->first('correlation_file') }}</p>
                                    @endif
                                </div>

                                <div class="form-group col-md-4 mb-0">
                                    <button type="submit" class="btn btn-dark btn-block" id="btn-correlate" onclick="start_loading(this)">
                                        <i class="fas fa-cog"></i> Process file
                                    </button>
                                </div>

                            </div>

                            <div class="form-row">
                                <div class="col-md-12 d-flex">
                                    <a href="{{ asset('examples/queries_documents_example.xml') }}" target="blank">
                                        <i class="fas fa-link"></i> XML example file
                                    </a>
                                    <p class="text-muted ml-3">* Repeated correlations will be ignored</p>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>

        </div>

        {{-- DISPLAY RESPONSE --}}

        <div id="response-text">
            @if(isset($response))
                <div class="alert {{ Str::contains($response, 'ERROR') ? 'alert-danger' : 'alert-success' }}" role="alert"> {{ $response }} </div>

                @if(isset($queries))
                    Total of {{ $queries }} data recorded. <br><br>
                @endif

                @if(isset($ignored) && $ignored != "")
                    Items {{ $ignored }} have been <strong>ignored due to duplication</strong>. <br><br>
                @endif

                @if(isset($invalid) && $invalid != "")
                    The items {{ $invalid }} are invalid.
                @endif
            @endif
        </div>

    </div>

@endsection

@section('scripts')

    <script>
        // SUBMIT CREATE
        $(function(){
            $('form').on('submit', function(event){
                event.stopPropagation();

                $("#btn-submit").prop('disabled', true);
                $("#btn-multiples").prop('disabled', true);
                $("#btn-docs").prop('disabled', true);
                $("#btn-doc_ids").prop('disabled', true);
            });
        });

        // SUBMIT UPLOAD AND CORRELATION
        if($("#multiple_queries_file").length > 0){
            /* show file value after file selected */
            document.getElementById('multiple_queries_file').addEventListener('change',function(e){
                var fileName = document.getElementById("multiple_queries_file").files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });

            /* show file value after file selected */
            document.getElementById('correlation_file').addEventListener('change',function(e){
                var fileName = document.getElementById("correlation_file").files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        }

        function start_loading(OBJ) {
            if ((OBJ.id == 'btn-multiple' && $("#multiple_queries_file").val() == "") 
                && (OBJ.id == 'btn-correlate' && $("#correlation_file").val() == ""))
                return;

            OBJ.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

            $("#response-text").html('<div class="text-danger" role="alert">Processing data...</div>');
        };

    </script>

@endsection