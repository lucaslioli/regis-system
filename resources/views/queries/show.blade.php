@extends('layouts.app')

@section('include')
    {{-- <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script> --}}
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        <div class="accordion" id="accordion-queries">

            {{-- EDIT --}}

            <div class="card">

                <div class="card-header" data-toggle="collapse"
                    data-target="#collapse-create" aria-expanded="true" aria-controls="collapse-create">
                    <h2 class="mb-0 head-accordion">
                        <span>
                            Query id {{ $query->id }} 
                            <span class="badge badge-pill mb-2 @switch($query->status)
                                @case("Incomplete") {{ "badge-secondary" }} @break
                                @case("Semi Complete") {{ "badge-primary" }} @break
                                @case("Complete") {{ "badge-success" }} @break
                                @case("Tiebreak") {{ "badge-danger" }} @break
                            @endswitch">{{ $query->status }}</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </h2>
                </div>

                <div class="collapse @if(!$errors->has('multiple_queries_file') && !$errors->has('correlation_file')) show @endif"
                    id="collapse-create" data-parent="#accordion-queries">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-3">
                                <span class="badge badge-pill badge-success text-success mb-1">.</span>
                                <strong>Very Relevant judgs.: </strong> {{ $query->judgmentsByClass("Very Relevant") }}
                            </div>
                            <div class="col-3">
                                <span class="badge badge-pill badge-primary text-primary mb-1">.</span>
                                <strong>Fairly Relevant judgs.: </strong> {{ $query->judgmentsByClass("Relevant") }}
                            </div>
                            <div class="col-3">
                                <span class="badge badge-pill badge-advise text-advise mb-1">.</span>
                                <strong>Marginally Relevant judgs.: </strong> {{ $query->judgmentsByClass("Marginally Relevant") }}
                            </div>
                            <div class="col-3">
                                <span class="badge badge-pill badge-danger text-danger mb-1">.</span>
                                <strong>Not Relevant judgs.: </strong> {{ $query->judgmentsByClass("Not Relevant") }}
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-3">
                                <strong>Total Documents related: </strong> {{ $query->documents->count() }}
                            </div>
                            <div class="col-3">
                                <strong>Total Judgments made: </strong> {{ $query->judgments->count() }}
                            </div>
                            <div class="col-3">
                                <strong>Times skiped: </strong> {{ $query->countSkipped() }}
                            </div>
                            <div class="col-3">
                                <strong>Annotators ({{ $query->annotators }}): </strong> 
                                @foreach($query->users as $user) 
                                    @if(!$user->pivot->skip)
                                        {{ $user->name }}{{ (!$loop->last) ? ',' : '' }}
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        <form method="POST" action="/queries/{{ $query->id ?? 'store' }}" id="form-create-query">
                            @csrf
                            @method('PUT')

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
                                    <button type="submit" class="btn btn-block btn-success" id="btn-submit" onclick="start_loading(this)">
                                        <i class="fas fa-save"></i> Submit
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>

        </div>

        {{-- DOCUMENTS CORRELATION --}}

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

                        {{-- ATTACH NEW DOCUMENT(S) --}}

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

                        <table class="table table-hover table-sm table-actions" id="table-query-documents">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Doc ID</th>
                                    <th scope="col">File name</th>
                                    <th scope="col" class="text-center">File type</th>
                                    <th scope="col">Judgements</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($query->documents as $key => $doc)
                                    <tr id="tr-{{ $doc->id }}">
                                        <td> {{ $key+1 }} </td>

                                        <td> {{ $doc->doc_id }} </td>

                                        <td title="{{ $doc->file_name }}">
                                            {{ Str::of($doc->file_name)->limit(36) }}
                                        </td>

                                        <td class="text-center"> {{ $doc->file_type }} </td>

                                        <td> {!! $doc->judgmentsByQuery($query->id, true) !!} </td>

                                        <td> {{ $doc->statusByQueryPair($query->id) }} </td>

                                        <td class="text-center">
                                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-sm btn-outline-primary" 
                                                id="viewDocument" data-id="{{ $doc->id }}" title="View document">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('queries.detachDocument', [$query, $doc]) }}" 
                                                class="btn btn-sm btn-outline-danger {{ ($query->documentJudgments($doc->id)>0)?"disabled":"" }}"
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

                        {{-- DETACH ALL --}}
                        @if(count($query->judgments) == 0 && $query->documents)
                            <a href="{{ route('queries.detachAll', $query) }}" id="btn-detachAll" class="btn btn-sm btn-danger pl-3 pr-3"
                                data-toggle="tooltip" data-html="true">
                                <i class="fas fa-exclamation-triangle"></i> Detach all documents
                            </a>
                        @endif
                    
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
        // SUBMIT EDIT
        $(function(){
            $('form').on('submit', function(event){
                event.stopPropagation();

                $("#btn-submit").prop('disabled', true);
                $("#btn-multiples").prop('disabled', true);
                $("#btn-docs").prop('disabled', true);
                $("#btn-doc_ids").prop('disabled', true);
            });
        });

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

        // DETACH ALL
        if($("#btn-detachAll").length > 0){
            $(document).ready(function () {
                $("body").on("click", "#btn-detachAll", function(e){
                    e.preventDefault();
                    delete_resource(this, "table-query-documents");
                });
            });
        }
    </script>

@endsection