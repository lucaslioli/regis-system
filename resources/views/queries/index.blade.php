@extends('layouts.app')

@section('include')
    {{-- <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script> --}}
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">
            <h1>Queries</h1>
            <div class="d-flex align-items-baseline">
                <h5 class="text-secondary mr-2">
                    {{ $queries->total() }} queries found
                </h5>

                <a href="{{ route('project.qrelsPreliminary') }}"  class="btn btn-link mr-2" type="button"
                    data-toggle="tooltip" data-placement="bottom"
                    title="Export preliminary qrels considering all Completed and Semi Completed queries">
                    <i class="fas fa-download"></i> Download preliminary qrels
                </a>
                
                <a href="{{ route('queries.create') }}" class="btn btn-success" title="Create and attach Documents">
                    <i class="fas fa-tools"></i> Manage Queries
                </a>
            </div>
        </div>

        <hr>

        <form method="GET" action="{{ route('queries.search') }}">
            <div class="row">

                <div class="col-md-10">
                    <input type="text" name="qry" id="qry" class="form-control"
                        placeholder="Enter an ID or part of query..." value="{{ old('qry') }}">
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
                    <th scope="col">ID</th>
                    {{-- <th scope="col">Num</th> --}}
                    <th scope="col" class="td-highlight">Query Title</th>
                    <th scope="col">Description</th>
                    {{-- <th scope="col">Narrative</th> --}}
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" class="text-center" title="Documents">Docs.</th>
                    <th scope="col" class="text-center" title="Annotators">Annots.</th>
                    <th scope="col" class="text-center" title="Judgments">Judgs.</th>
                    <th scope="col" class="text-center" title="Times skipped">Skipeed</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>

            @forelse ($queries as $query)

                <tr id="tr-{{ $query->id }}">
                    <td>{{ $query->id }}</td>

                    {{-- <td>{{ $query->id }}</td> --}}

                    <td>{{ $query->qry_id }}</td>

                    <td class="td-highlight">{{ $query->title }}</td>

                    <td class="text-muted">
                        {{ Str::of($query->description)->limit(80) }}
                    </td>

                    {{-- <td class="text-muted">
                        {{ Str::of($query->narrative)->limit(50) }}
                    </td> --}}

                    <td class="text-center">
                        <span class="badge badge-pill @switch($query->status)
                            @case("Incomplete") {{ "badge-secondary" }} @break
                            @case("Semi Complete") {{ "badge-primary" }} @break
                            @case("Complete") {{ "badge-success" }} @break
                            @case("Tiebreak") {{ "badge-danger" }} @break
                        @endswitch">{{ $query->status }}</span>
                    </td>

                    <td class="text-center">{{ $query->documents->count() }}</td>

                    <td class="text-center">{{ $query->annotators }}</td>

                    <td class="text-center">{{ $query->judgments->count() }}</td>

                    <td class="text-center">{{ $query->countSkipped() }}</td>

                    <td class="text-center">
                        <a href="{{ route('queries.edit', $query) }}" class="btn btn-sm btn-outline-primary" title="Edit query">
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
                    <td colspan="10" class="text-center">
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
                e.preventDefault();
                delete_resource(this);
            });
        });

    </script>

@endsection