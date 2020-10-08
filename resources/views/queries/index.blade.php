@extends('layouts.app')

@section('include')
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">
            <h1>Queries</h1>
            <div class="d-flex align-items-baseline">
                <h5 class="text-secondary mr-3">
                    {{ $queries->total() }} queries find
                </h5>
                <a href="{{ route('queries.create') }}" class="btn btn-success" title="New query">
                    <i class="fas fa-plus"></i> New Query
                </a>
            </div>
        </div>

        <hr>

        <form method="GET" action="{{ route('queries.search') }}">
            <div class="row">

                <div class="col-md-10">
                    <input type="text" name="qry" id="qry" class="form-control" placeholder="Enter an ID or part of query..." value="{{ old('qry') }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block" id="btn-search"><i class="fas fa-search"></i> Search</button>
                </div>

            </div>
        </form>

        <br>

        <div id="response" role="alert"></div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Query Title</th>
                    <th scope="col">Description</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>

            @forelse ($queries as $query)

                <tr id="tr-{{ $query->id }}">
                    <td>{{ $query->id }}</td>
                    <td>
                        {{ $query->title }}
                    
                        <footer class="blockquote-footer">
                            <strong>Correction:</strong> <i>{{ $query->description }}</i>
                        </footer>
                    </td>
                    <td class="text-muted">
                        {{ Str::of($query->description)->limit(120) }}
                    </td>

                    <td class="text-center">
                        <a href="{{ route('queries.edit', $query) }}" class="btn btn-sm btn-outline-secondary" title="Edit query">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="{{ route('queries.destroy', $query) }}" class="btn btn-sm btn-outline-danger" 
                            id="deleteQuery" data-id="{{ $query->id }}" title="Delete query">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        <i class="fas fa-ban"></i> No queries found.
                    </td>
                </tr>
                
            @endforelse

            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            {{ $queries->links() }}
        </div>

    </div>

@endsection

@section('scripts')
    
    <script type="text/javascript">

        $(document).ready(function () {

            $("body").on("click", "#deleteQuery", function(e){

                if(!confirm("Do you really want to do this?")) {
                    return false;
                }

                e.preventDefault();
                var id = $(this).data("id");
                var token = $("meta[name='csrf-token']").attr("content");

                $.ajax({
                    url: this.href, //or use url: "query/"+id,
                    type: 'DELETE',
                    data: {
                        _token: token,
                        id: id
                    },
                    success: function (response){
                        $("#response").removeClass("alert alert-danger");
                        $("#response").addClass("alert alert-success");
                        $("#response").html(response);
                        $("#tr-"+id).remove();
                    }
                });
                return false;
            });
            

        });
    </script>

@endsection