@extends('layouts.app')

@section('include')
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
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

                <div class="collapse @if(!$errors->has('multiple_queries_file') && !$errors->has('correlation_file')) show @endif"
                    id="collapse-create" data-parent="#accordion-queries">
                    <div class="card-body">

                        @isset($query->exists)

                            <div class="row">
                                <div class="col-4">
                                    <strong>Total of Documents related: </strong> {{ $query->documents->count() }}
                                </div>
                                <div class="col-4">
                                    <strong>Total of Judgments already made: </strong> {{ $query->judgments->count() }}
                                </div>
                                <div class="col-4">
                                    <strong>Number of Annotators: </strong> {{ $query->annotators }}
                                </div>
                            </div>

                            <hr>

                        @endisset

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
                                    <button type="submit" class="btn btn-block btn-success" id="btn-create">
                                        <i class="fas fa-save"></i> Submit
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

            @endif

            
            {{-- CORRELATE QUERIES WITH DOCUMENTS --}}
            
            @if(!isset($query->exists))
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

                            <form method="POST" action="/queries/attachDocumentById" enctype="multipart/form-data" id="queries-documents">
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

            @endif

        </div>

        {{-- DOCUMENTS CORRELATION --}}

        @if(isset($query->exists))

            <div class="accordion" id="accordion-query-documents">
                <div class="card">

                    <div class="card-header" data-toggle="collapse"
                        data-target="#collapse-documents" aria-expanded="true" aria-controls="collapse-documents">
                        <h2 class="mb-0 head-accordion">
                            <span>Documents related to the Query</span>
                            <i class="fas fa-chevron-down"></i>
                        </h2>
                    </div>

                    <div id="collapse-documents" class="collapse show" data-parent="#accordion-query-documents">
                        <div class="card-body">

                            {{-- ATTACH NEW DOCUMENT --}}

                            <form method="POST" action="/queries/{{ $query->id }}/attachDocumentById" id="form-attach-docid"
                                class="mb-3">
                                @csrf

                                <div class="form-row">
                                    <div class="col-12">
                                        <label><strong>Attach new document(s)</strong></label>
                                    </div>
                                </div>

                                <div class="form-row mb-0">

                                    <div class="form-group col-8">
                                        <input class="form-control @error('doc_ids') is-invalid @enderror" 
                                            value="{{ old('doc_ids') }}" required
                                            type="text" id="doc_ids" name="doc_ids" placeholder="Inform multiple doc IDs, separated by semicolon ( ; )">
                            
                                        @error('doc_ids')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-4 mb-0">
                                        <button type="submit" class="btn btn-dark btn-block" id="btn-doc_ids" onclick="start_loading(this)">
                                            <i class="fas fa-cog"></i> Attach document(s)
                                        </button>
                                    </div>

                                </div>
                            
                            </form>

                            {{-- LIST TABLE --}}

                            <div id="response" role="alert"></div>

                            <table class="table table-hover table-sm table-actions table-query-documents">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Doc ID</th>
                                        <th scope="col">File name</th>
                                        <th scope="col" class="text-center">File type</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse ($query->documents as $key => $doc)
                                        <tr id="tr-{{ $doc->id }}">
                                            <td> {{ $key+1 }} </td>
                                            <td> {{ $doc->doc_id }} </td>
                                            <td> {{ Str::of($doc->file_name)->limit(75) }} </td>
                                            <td class="text-center"> {{ $doc->file_type }} </td>
                                            <td class="text-center">
                                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-sm btn-outline-primary" 
                                                    id="viewDocument" data-id="{{ $doc->id }}" title="View document">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('queries.detachDocument', [$query, $doc]) }}" class="btn btn-sm btn-outline-danger" 
                                                    id="deleteDocument" data-id="{{ $doc->id }}" title="Detach document">
                                                    <i class="fas fa-unlink"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <i class="fas fa-ban"></i> No documents related.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        
                        </div>
                    </div>

                </div>
            </div>

        @endif

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
        // SUBMIT CREATE AND EDIT
        $(function(){
            $('form').on('submit', function(event){
                event.stopPropagation();

                $("#btn-create").prop('disabled', true);
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

        // DETACH DOCUMENT
        if($("#deleteDocument").length > 0){
            $(document).ready(function () {
                $("body").on("click", "#deleteDocument", function(e){
                    e.preventDefault();
                    delete_resource(this);
                });
            });
        }
    </script>

@endsection