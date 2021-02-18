@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">
            <h1>Tiebreaks</h1>
            <div class="d-flex align-items-baseline">
                <h5 class="text-secondary">
                    {{ $tiebreaks->total() }} tiebreaks found
                </h5>
            </div>
        </div>

        <hr>

        <form method="GET" action="{{ route('tiebreaks.search') }}">
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
                    <th scope="col">Q.ID</th>
                    <th scope="col">Query</th>
                    <th scope="col">D.ID</th>
                    <th scope="col">Document</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>

            @forelse ($tiebreaks as $key => $tiebreak)

                <tr id="tr-{{ $tiebreak->id }}">
                    <td>{{ $key+1 }}</td>

                    {{-- <td>{{ $tiebreak->id }}</td> --}}

                    <td> 
                        {{ $tiebreak->qry_id }}
                    </td>

                    <td> 
                        {{ Str::of($tiebreak->title)->limit(36) }}
                    </td>

                    <td> 
                        {{ $tiebreak->doc_id }}
                    </td>

                    <td>
                        {{ Str::of($tiebreak->file_name)->limit(36) }}
                    </td>

                    <td class="text-center">
                        <a href="{{ route('tiebreaks.edit', [$tiebreak->query_id, $tiebreak->document_id]) }}"
                            class="btn btn-sm btn-outline-primary" title="Solve">
                            <i class="fas fa-edit"></i> Solve
                        </a>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        <i class="fas fa-ban"></i> No tiebreaks found for you to solve.
                    </td>
                </tr>
                
            @endforelse

            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            {{ $tiebreaks->links() }}
        </div>

    </div>

@endsection
