@extends('layouts.app')

@section('include')
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection

@section('content')

    <div class="container document-show">

        <div class="d-flex justify-content-between align-items-center">
            <h3>Document: <strong> {{ $document->doc_id }} </strong> </h3>
            <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Return</a>
        </div>
        <hr>

        <div id="response" role="alert"></div>
        
        <div class="d-flex justify-content-between">
            <div>
                <p><strong>Database id: </strong> {{ $document->id }} </p>
                <p><strong>File name: </strong> 
                    @if(file_exists(public_path()."/documents/".$document->file_name))
                        <a href="/documents/{{ $document->file_name }}" target="blank">
                            {{ $document->file_name }} <i class="fas fa-external-link-alt"></i>
                        </a>
                    @else
                        {{ $document->file_name }} <i class="fas fa-exclamation-triangle"></i> 
                    @endif
                </p>
                <p><strong>FIle type: </strong> {{ $document->file_type }} </p>
            </div>
                
            <div>
                <a href="{{ route('documents.destroy', $document) }}" class="btn btn-outline-danger" 
                    id="deleteDocument" data-id="{{ $document->id }}" title="Delete document">
                    <i class="fas fa-trash"></i> Delete document
                </a>
            </div>
        </div>
        
        <div class="form-group">
            <div class="card">
                <div class="card-body document-text">
                    {{ $document->text_file }}
                </div>
            </div>
        </div>

        <hr>

        {{-- SAVE MULTIPLES PDF OR IMAGE FILES --}}
        @if(!file_exists(public_path()."/documents/".$document->file_name))

            <form method="POST" action="/documents/upload" enctype="multipart/form-data" id="upload-form">
                @csrf

                <input type="hidden" name="show-document" value="{{ $document->id }}">

                <div class="form-row">
                    <div class="col-md-12">
                        <label><strong>Upload the own document (PDF or Image)</strong></label>
                    </div>
                </div>

                <div class="form-row">

                    <div class="form-group col-md-8">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="doc_files" name="doc_files[]" 
                                accept="application/pdf,image/png,image/jpeg,image/gif" required>
                            <label class="custom-file-label" for="doc_files">Select PDF or image file</label>
                        </div>
    
                        @if($errors->has('doc_files.*'))
                            <p class="text-danger">{{ $errors->first('doc_files.*') }}</p>
                        @endif

                        <p class="text-muted">*Select the file with the same name as "File name"</p>
                    </div>
    
                    <div class="form-group col-md-4">
                        <button type="submit" class="btn btn-secondary btn-block" id="btn-doc" onclick="start_loading(this)">Process file</button>
                    </div>
    
                </div>

            </form>

        @endif

    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function () {
            $("body").on("click", "#deleteDocument", function(e){
                e.preventDefault();
                delete_resource(this);
                if($("#upload-form").length > 0)
                    $("#upload-form").remove();
            });
        });

        if($("#upload-form").length > 0){
            /* show file value after file selected */
            document.getElementById('doc_files').addEventListener('change',function(e){
                var fileName = document.getElementById("doc_files").files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });

            $(function(){
                $('form').on('submit', function(event){
                    event.stopPropagation();
                    $("#btn-doc").prop('disabled', true);
                });
            });

            function start_loading(OBJ) {
                if (OBJ.id == 'btn-doc' && $("#doc_files").val() == "")
                    return ;

                OBJ.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

                $("#response-text").html('<div class="text-danger" role="alert">Processing data...</div>');
            };
        }
    </script>

@endsection