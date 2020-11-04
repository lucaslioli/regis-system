@extends('layouts.app')

@section('include')
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        <h1>Upload New Documents</h1>
        <p class="text-muted">* Repeated files will be ignored</p>
        <hr>

        {{-- SAVE MULTIPLES XML FILES --}}

        <form method="POST" action="/documents/store" enctype="multipart/form-data" class="mb-5">
            @csrf

            <div class="form-row">
                <div class="col-12">
                    <label><strong>Upload XML Documents</strong></label>
                </div>
            </div>

            <div class="form-row mb-0">

                <div class="form-group col-8 mb-0">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="xml_files" name="xml_files[]" accept="text/xml" multiple required>
                        <label class="custom-file-label" for="xml_files">Select XML files (max of 20 files)</label>
                    </div>

                    @if($errors->has('xml_files.*'))
                        <p class="text-danger">{{ $errors->first('xml_files.*') }}</p>
                    @endif
                </div>

                <div class="form-group col-4 mb-0">
                    <button type="submit" class="btn btn-success btn-block" id="btn-xmls" onclick="start_loading(this)">
                        <i class="fas fa-cog"></i> Process XML files
                    </button>
                </div>

            </div>

            <div class="form-row">
                <div class="col-12">
                    <a href="{{ asset('examples/doc_example.xml') }}" target="blank">
                        <i class="fas fa-link"></i> XML example file
                    </a>
                </div>
            </div>
        
        </form>

        {{-- PROCESS FILES FROM DIRECTORY --}}

        <form method="POST" action="/documents/store_from_path" enctype="multipart/form-data" class="mb-5">
            @csrf

            <div class="form-row">
                <div class="col-12">
                    <label><strong>Process XML Documents from a Directory (in the server)</strong></label>
                </div>
            </div>

            <div class="form-row mb-0">

                <div class="form-group col-8">
                    <input class="form-control @error('directory') is-invalid @enderror" 
                        value="{{ old('Directory') }}" required
                        type="text" id="directory" name="directory" placeholder="Inform the full server directory">
        
                    @error('title')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group col-4 mb-0">
                    <button type="submit" class="btn btn-dark btn-block" id="btn-directory" onclick="start_loading(this)">
                        <i class="fas fa-cogs"></i> Process path files
                    </button>
                </div>

            </div>

            <div class="form-row mb-0">

                <div class="form-group col-8">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="document_list" name="document_list" accept="text/plain">
                        <label class="custom-file-label" for="document_list">Select a file with a list of documents</label>
                    </div>

                    @if($errors->has('document_list'))
                        <p class="text-danger">{{ $errors->first('document_list.*') }}</p>
                    @endif
                </div>

            </div>
        
        </form>

        <hr>

        {{-- SAVE MULTIPLES PDF OR IMAGE FILES --}}

        <form method="POST" action="/documents/upload" enctype="multipart/form-data">
            @csrf

            <div class="form-row">
                <div class="col-12">
                    <label><strong>Upload the own documents (PDF or Image)</strong></label>
                </div>
            </div>

            <div class="form-row">

                <div class="form-group col-8">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="doc_files" name="doc_files[]" 
                            accept="application/pdf,image/png,image/jpeg,image/gif" multiple required>
                        <label class="custom-file-label" for="doc_files">Select PDF or image files (max of 20 files)</label>
                    </div>

                    @if($errors->has('doc_files.*'))
                        <p class="text-danger">{{ $errors->first('doc_files.*') }}</p>
                    @endif
                </div>

                <div class="form-group col-4">
                    <button type="submit" class="btn btn-primary btn-block" id="btn-docs" onclick="start_loading(this)">
                        <i class="fas fa-file-upload"></i> Upload files
                    </button>
                </div>

            </div>

        </form>

        <hr>

        {{-- DISPLAY RESPONSE --}}

        <div id="response-text">
            @if(isset($response))
                <div class="alert {{ Str::contains($response, 'ERROR') ? 'alert-danger' : 'alert-success' }}" role="alert">
                    {{ $response }}
                </div>

                @if(isset($documents))
                    Total of {{ $documents }} documents inserted. <br>
                @endif

                @if(isset($ignored) && $ignored != "")
                    Documents {{ $ignored }} have been <strong>ignored due to duplication</strong>.
                @endif
            @endif
        </div>

    </div>

@endsection

@section('scripts')

    <script >
        /* show number of files selected */
        document.getElementById('xml_files').addEventListener('change',function(e){
            var files = document.getElementById("xml_files").files.length;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = files + " files selected";
        });

        /* show number of files selected */
        document.getElementById('doc_files').addEventListener('change',function(e){
            var files = document.getElementById("doc_files").files.length;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = files + " files selected";
        });

        document.getElementById('document_list').addEventListener('change',function(e){
            var fileName = document.getElementById("document_list").files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
        });

        $(function(){
            $('form').on('submit', function(event){
                event.stopPropagation();
        
                $("#btn-xmls").prop('disabled', true);
                $("#btn-directory").prop('disabled', true);
                $("#btn-docs").prop('disabled', true);
            });
        });

        function start_loading(OBJ) {
            if ((OBJ.id == 'btn-xmls' && $("#xml_files").val() == "") || 
                (OBJ.id == 'btn-directory' && $("#directory").val() == "") ||
                (OBJ.id == 'btn-docs' && $("#doc_files").val() == ""))
                return ;

            OBJ.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

            $("#response-text").html('<div class="text-danger" role="alert">Processing data...</div>');
        };
    </script>

@endsection
