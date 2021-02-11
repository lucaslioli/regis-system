@extends('layouts.app')

@section('include')
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        @if(isset($query->exists) && isset($document->exists))

            {{-- QUERY SPACE --}}

            <div class="row d-flex flex-column">
                <label for="query_title"><strong>Query title:</strong></label>
                <div class="card mb-3">
                    <div class="card-body"> {{ $query->title }} </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="query_description"><strong>Description and Narrative:</strong></label>
                <div class="card w-100">
                    <div class="card-body"> 
                        <p class="mb-2"><strong>Description:</strong> {{ $query->description }}</p>
                        <p class="mb-0"><strong>Narrative:</strong> {{ $query->narrative }}</p>
                    </div>
                </div>
            </div>

            {{-- New documents notifications --}}
            @if(isset($incomplete_query) && $incomplete_query)
            
                <div class="row mb-3 text-primary">
                    <span><i class="fas fa-asterisk"></i> New documents were added to this query, 
                        please, complete it before go to the next one!</span>
                </div>

            @endif

            {{-- Progress bar and tiebreak identification --}}
            @if(isset($tiebreak) || (isset($judgment) && $judgment->untie))

                <div class="row progress mt-4 mb-5">
                    <div class="progress-bar progress-bar-striped bg-danger" role="progressbar"
                        style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">TIEBREAK</div>
                </div>

            @else

                <div class="row progress mt-4 mb-5">
                    <div class="progress-bar" role="progressbar"  style="width: {{ $progress->bar }}%;"
                        aria-valuenow="{{ $progress->bar }}" aria-valuemin="0" aria-valuemax="100">
                        {{ $progress->bar }}%
                    </div>
                </div>

            @endif

            {{-- Anotation space --}}

            <div class="row">

                <div class="form-group col-9">
                    <div class="form-group row">

                        {{-- Document header --}}

                        <div class="document-title d-flex justify-content-between">
                            <label><strong>Original document:</strong> {{ $document->doc_id }} - 
                                @if(file_exists(public_path()."/documents/".$document->file_name))
                                    <a href="/documents/{{ $document->file_name }}" target="blank">
                                        {{ $document->file_name }}
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                @else
                                    {{ $document->file_name }}
                                @endif
                            </label>
                            <label>
                                @if(isset($tiebreak) || (isset($judgment) && $judgment->untie))
                                    1/1
                                @else
                                    {{ $progress->document ?? 1 }}/{{ count($query->documents) }}
                                @endif
                            </label>
                        </div>

                        {{-- Document text --}}

                        <div class="card w-100">
                            <div class="card-body document-text w-100" id="content-unique">

                                @if($document->file_type == "IMG")
                                    <div class="card-body document-image d-flex justify-content-center">
                                        <img src="/documents/{{ $document->file_name }}" alt="Document image file">
                                    </div>
                                @endif

                                {!! $document->text_file !!}

                            </div>
                        </div>

                        <div class='d-flex justify-content-between align-items-baseline w-100 mt-2'>

                            {{-- Skip option --}}

                            @if(isset($query) && !isset($judgment) && !isset($tiebreak))
                                <a href="{{ route('users.skipQuery', $query) }}" id="btn-skip" class="btn btn-sm btn-dark pl-3 pr-3"
                                    data-toggle="tooltip" data-html="true" 
                                    title="In case the query requires extra domain knowledge, you can skip it. This action <b>can't be undone</b>.">
                                    <i class="fas fa-forward"></i> Skip Query
                                </a>
                            @endif

                            {{-- Navigation through markers --}}
                        
                            @if($document->file_type == "PDF" && isset($markers))
                                <div>
                                    <span class="text-muted">Navigate through the markers: </span>

                                    <span id='current-mark-unique' class='ml-2'>0</span><span>/{{ $markers }}</span>

                                    <button class="btn btn-sm btn-light" onclick='findFirstLast("unique", 1)'>
                                        <i class='fas fa-angle-double-left'></i> First
                                    </button>
                                    
                                    <button class="btn btn-sm btn-light" onclick='findPrevMarker("unique")' id='btnPrev-unique'>
                                        <i class='fas fa-angle-left'></i> Previous
                                    </button>
                                    
                                    <button class="btn btn-sm btn-light" onclick='findNextMarker("unique")' id='btnNext-unique'>
                                        Next <i class='fas fa-angle-right'></i>
                                    </button>
                                    
                                    <button class="btn btn-sm btn-light" onclick='findFirstLast("unique", 0)'>
                                        Last <i class='fas fa-angle-double-right'></i>
                                    </button>
                                </div>
                            @endif

                        </div>

                    </div>

                </div>

                {{-- Judgment space --}}
        
                <div class="form-group col-3">
                    <label>
                        <strong>With respect to the query, this document is:</strong>
                        <a href="" class="btn-link" data-toggle="modal" data-target="#helpModal" title="Relevance guide">
                            <i class="fas fa-question"></i>
                        </a>
                    </label>

                    {{-- Judgment form --}}
                    <form method="POST" action="/judgments/{{ $judgment->id ?? 'store' }}" id="form-judgment">
                        @csrf
                        @isset($judgment->exists)
                            @method('PUT')
                        @endisset

                        <input type="hidden" name="query_id" value="{{ $query->id }}">
                        <input type="hidden" name="document_id" value="{{ $document->id }}">
                        <input type="hidden" name="untie" value="{{ isset($tiebreak) ? 1 : 0 }}">

                        <div class="judgment">

                            <div class="custom-control custom-switch">
                                {{-- 1.0 --}}
                                <input type="radio" class="custom-control-input" id="very-relevant" name="judgment" value="Very Relevant" 
                                    {{ isset($judgment) && $judgment->judgment == "Very Relevant" ? 'checked' : '' }}
                                    {{ isset($tiebreak) && !in_array('Very Relevant', $tiebreak) ? 'disabled' : '' }}>
                                <label class="custom-control-label" for="very-relevant">Very Relevant</label> 
                            </div>

                            <div class="custom-control custom-switch">
                                {{-- 0.7 --}}
                                <input type="radio" class="custom-control-input" id="relevant" name="judgment" value="Relevant"
                                    {{ isset($judgment) && $judgment->judgment == "Relevant" ? 'checked' : '' }}
                                    {{ isset($tiebreak) && !in_array('Relevant', $tiebreak) ? 'disabled' : '' }}>
                                <label class="custom-control-label" for="relevant">Relevant</label>
                            </div>

                            <div class="custom-control custom-switch">
                                {{-- 0.3 --}}
                                <input type="radio" class="custom-control-input" id="little-relevant" name="judgment" value="Marginally Relevant" 
                                    {{ isset($judgment) && $judgment->judgment == "Marginally Relevant" ? 'checked' : '' }}
                                    {{ isset($tiebreak) && !in_array('Marginally Relevant', $tiebreak) ? 'disabled' : '' }}>
                                <label class="custom-control-label" for="little-relevant">Marginally Relevant</label>
                            </div>

                            <div class="custom-control custom-switch">
                                {{-- 0.0 --}}
                                <input type="radio" class="custom-control-input" id="not-relevant" name="judgment" value="Not Relevant"
                                    {{ isset($judgment) && $judgment->judgment == "Not Relevant" ? 'checked' : '' }}
                                    {{ isset($tiebreak) && !in_array('Not Relevant', $tiebreak) ? 'disabled' : '' }}>
                                <label class="custom-control-label" for="not-relevant">Not Relevant</label>
                            </div>

                        </div>

                        {{-- Observation --}}

                        <div class="form-group">
                            <label for="observation">Comments and suggestions (optional):</label>
                            <textarea class="form-control" id="observation" name="observation" 
                                rows="4" placeholder="Enter here your comments and suggestions (optional)...">{{
                                    (isset($judgment)) ? $judgment->observation : ''
                                }}</textarea>
                        </div>

                        {{-- Submit button --}}

                        @if(!isset($judgment) || !$judgment->untie)
                            <button type="submit" id="btn-submit" class="btn btn-block {{ isset($judgment) ? 'btn-primary' : 'btn-success' }}">
                                <i class="fas fa-save"></i> {{ isset($judgment) ? 'Edit' : 'Submit' }}
                            </button>
                        @endif
                    </form>

                    <!-- Modal with relevance guide -->
                    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="helpModalLabel"><i class="fas fa-question"></i> Relevance guide</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>To help you judge the query-document pairs, here we provide some relevance indicators:</p>
                            <ul>
                                <li>Very Relevant: Documents directly correlated with the query that answer it completely;</li>
                                <li>Relevant: Documents correlated with the query but do not answer it completely;</li>
                                <li>Marginally Relevant: Documents not correlated with the query but with associated information;</li>
                                <li>Not Relevant: Documents that do not present any correlation with the query.</li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                        </div>
                    </div>
                    </div>

                </div>

            </div>

            {{-- Tiebreak observations --}}

            @if(isset($tiebreak) && isset($observations))

                <div class="form-group row">
                    <label for="query_description"><strong>Annotators observations:</strong></label>
                    <div class="card w-100">
                        <div class="card-body"> 
                            @foreach ($observations as $key => $obs)
                                @if($obs != "")
                                    <strong>{{ $tiebreak[$key] }}:</strong> {{ $obs }}<br>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

            @endif

            {{-- Error message space --}}

            @if($errors->count())
                <p class="text-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </p>
            @endif

        {{-- Main admin message --}}

        @elseif(Auth::user()->id == 1 && Auth::user()->role == 'admin')

            <div class="d-flex flex-column align-items-center justify-content-center mt-5">
                <h1 class="mt-5"><i class="fas fa-user-shield"></i></h1>
                <h4>This is the main admin user account.</h4>
                <h4>No query has to be annotated.</h4>
            </div>

        {{-- Next query message --}}

        @elseif(isset($next_query))

            <div class="d-flex flex-column align-items-center justify-content-center mt-5">
                <h1 class="mt-5"><i class="fas fa-smile-wink"></i></h1>
                <h4>Thanks for completing the annotations for the last query!</h4>
                <h4>You can now move on to the next one!</h4>

                <a href="{{ route('judgments.create', ['next' => true]) }}" class="btn btn-primary mt-5">
                    Annotate next query <i class="fas fa-arrow-right"></i>
                </a>
            </div>

        {{-- No annotations available message --}}

        @else

            <div class="d-flex flex-column align-items-center justify-content-center mt-5">
                <h1 class="mt-5"><i class="fas fa-sad-tear"></i></h1>
                <h4>Sorry, there is no query available for annotation!</h4>
                <p>Thanks for participating! If new queries are added, we will let you know!</p>
            </div>

        @endif
    </div>

@endsection

@section('scripts')
    
    <script type="text/javascript">

        $(document).ready(function () {
            $('#btn-skip').tooltip('enable');

            $("#btn-skip").on("click", function(e){
                e.preventDefault();
                
                if(!confirm("Do you really want to do this?"))
                    return false;

                $("#btn-skip").prop('disabled', true);
                $("#btn-submit").prop('disabled', true);

                $("#btn-skip").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

                window.location = this.href;
            });
        });

    </script>

@endsection
