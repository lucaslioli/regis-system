@extends('layouts.app')

@section('include')
    {{-- <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script> --}}
@endsection

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">
            <h1>My Annotations</h1>
            <div class="d-flex align-items-baseline">
                <h5 class="text-secondary mr-3">
                    {{ $judgments->total() }} annotations found
                </h5>
            </div>
        </div>

        <hr>

        <form method="GET" action="{{ route('judgments.search') }}">
            <div class="row">

                <div class="col-md-10">
                    <input type="text" name="qry" id="qry" class="form-control"
                        placeholder="Enter some information about the judgment to filter by it..." value="{{ old('qry') }}">
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
                    {{-- <th scope="col">ID</th> --}}
                    <th scope="col">Query</th>
                    <th scope="col">Document</th>
                    <th scope="col" class="td-highlight">Judgment</th>
                    <th scope="col">Observation</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>

            @forelse ($judgments as $key => $judgment)

                <tr id="tr-{{ $judgment->id }}">
                    <td>{{ $key+1 }}</td>

                    {{-- <td>{{ $judgment->id }}</td> --}}

                    <td> 
                        {{ Str::of($judgment->queryy->title)->limit(50) }}
                    </td>

                    <td>
                        {{ Str::of($judgment->document->file_name)->limit(40) }}
                    </td>

                    <td class="td-highlight @switch($judgment->judgment)
                            @case("Very Relevant") {{ "text-success" }} @break
                            @case("Relevant") {{ "text-primary" }} @break
                            @case("Marginally Relevant") {{ "text-advise" }} @break
                            @case("Not Relevant") {{ "text-danger" }} @break
                        @endswitch">
                        {{ $judgment->judgment }}
                    </td>

                    <td class="">
                        @if($judgment->untie)
                            <span class="badge badge-pill badge-danger mr-1">Tiebreak</span>
                        @endif
                        {{ Str::of($judgment->observation)->limit(40) }}
                    </td>

                    <td class="text-center">
                        @if($judgment->untie)
                            <a href="{{ route('judgments.edit', $judgment) }}" class="btn btn-sm btn-outline-danger" title="Look judgment">
                                <i class="fas fa-eye"></i>
                            </a>
                        @else
                            <a href="{{ route('judgments.edit', $judgment) }}" class="btn btn-sm btn-outline-primary" title="Edit judgment">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif

                        {{-- <a href="{{ route('judgments.destroy', $judgment) }}" class="btn btn-sm btn-outline-danger" 
                            id="deleteJudgment" data-id="{{ $judgment->id }}" title="Delete Judgment">
                            <i class="fas fa-trash"></i>
                        </a> --}}

                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        <i class="fas fa-ban"></i> No annotations found.
                    </td>
                </tr>
                
            @endforelse

            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            {{ $judgments->links() }}
        </div>

    </div>

@endsection

@section('scripts')
    
    {{-- <script type="text/javascript">

        $(document).ready(function () {
            $("body").on("click", "#deleteJudgment", function(e){
                e.preventDefault();
                delete_resource(this);
            });
        });

    </script> --}}

@endsection