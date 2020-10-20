@extends('layouts.app')

@section('include')
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">
            <h1>Documents</h1>
            <div class="d-flex align-items-baseline">
                <h5 class="text-secondary mr-3">
                    {{ $documents->total() }} documents find
                </h5>
                <a href="{{ route('documents.create') }}" class="btn btn-success" title="Upload documents">
                    <i class="fas fa-plus"></i> Upload documents
                </a>
            </div>
        </div>

        <hr>

        <form method="GET" action="{{ route('documents.search') }}">
            <div class="row">

                <div class="col-md-10">
                    <input type="text" name="qry" id="qry" class="form-control"
                        placeholder="Enter a doc ID, file name or file type..." value="{{ old('qry') }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block" id="btn-search">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>

            </div>
        </form>

        <br>

        <div id="response" role="alert"></div>

        <table class="table table-hover table-actions">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Doc ID</th>
                    <th scope="col">File Name</th>
                    <th scope="col">Type</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>

            @forelse ($documents as $document)

                <tr id="tr-{{ $document->id }}">
                    <td> {{ $document->id }} </td>

                    <td> {{ $document->doc_id }} </td>

                    <td>
                        @if(file_exists(public_path()."/documents/".$document->file_name))
                            <a href="/documents/{{ $document->file_name }}" target="blank">{{ Str::of($document->file_name)->limit(100) }}</a>
                        @else
                            {{ Str::of($document->file_name)->limit(100) }}
                        @endif
                    </td>

                    <td> {{ $document->file_type }} </td>

                    <td class="text-center">
                        <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-outline-primary" 
                            id="viewDocument" data-id="{{ $document->id }}" title="View document">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('documents.destroy', $document) }}" class="btn btn-sm btn-outline-danger" 
                            id="deleteDocument" data-id="{{ $document->id }}" title="Delete document">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        <i class="fas fa-ban"></i> No documents found.
                    </td>
                </tr>
                
            @endforelse

            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            {{ $documents->links() }}
        </div>

    </div>

@endsection

@section('scripts')
    
    <script type="text/javascript">

        $(document).ready(function () {
            $("body").on("click", "#deleteDocument", function(e){
                e.preventDefault();
                delete_resource(this);
            });
        });

    </script>

@endsection