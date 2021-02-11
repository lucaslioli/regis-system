@extends('layouts.app')

@section('include')
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">
            <h1>Users</h1>
            <h5 class="text-secondary">
                {{ $users->total() }} registered users
            </h5>
        </div>

        <hr>

        <form method="GET" action="{{ route('users.search') }}">
            <div class="row">

                <div class="col-md-10">
                    <input type="text" name="qry" id="qry" class="form-control" 
                        placeholder="Enter an ID, user name or e-mail..." value="{{ old('qry') }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary btn-block" id="btn-search">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>

            </div>
        </form>

        <br>

        <div id="response" role="alert"></div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">User name</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Role</th>
                    <th scope="col" class="text-center" title="Annotations completed">Annotations</th>
                    <th scope="col" class="text-center" title="Queries completed">Queries</th>
                    <th scope="col" class="text-center" title="Queries skipped">Skip</th>
                    <th scope="col" class="text-center" title="Current Query Id">Curr. Query</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>

            @forelse ($users as $user)

                <tr>
                    <td>{{ $user->id }}</td>

                    <td>{{ $user->name }}</td>

                    <td class="text-muted">{{ $user->email }}</td>

                    <td class="text-muted">
                        <span class="badge badge-pill @switch($user->role)
                            @case("admin") {{ "badge-dark" }} @break
                            @case("default") {{ "badge-seccondary" }} @break
                        @endswitch">{{ $user->role }}</span>
                    </td>

                    <td class="text-center">
                        <span class="badge badge-{{ $user->judgments->count() ? 'primary' : 'secondary' }} badge-pill">
                            {{ $user->judgments->count() ?? 0 }}
                        </span>
                    </td>

                    <td class="text-center">
                        <span class="badge badge-{{ $user->queriesCompleted() ? 'primary' : 'secondary' }} badge-pill">
                            {{ $user->queriesCompleted() ?? 0 }}
                        </span>
                    </td>

                    <td class="text-center">
                        {{ $user->queriesSkipped() ?? 0 }}
                    </td>

                    <td class="text-center">
                        @if($user->current_query)
                            {{ $user->current_query }}
                            <a href="{{ route('users.detachQuery', [$user, $user->current_query]) }}"
                                class="btn btn-sm btn-link text-danger" id="detachUserQuery" title="Detach user from query">
                                <i class="fas fa-unlink"></i>
                            </a>
                        @else
                            None
                        @endif
                    </td>

                    <td class="text-center">
                        @if($user->role == 'default')
                            <a href="{{ route('users.makeAdmin', $user) }}" class="btn btn-sm btn-outline-danger"
                                title="Make user admin">
                                <i class="fas fa-users-cog"></i> Make Admin
                            </a>
                        @elseif($user->id != 1)
                            <a href="{{ route('users.revokeAdmin', $user) }}" class="btn btn-sm btn-outline-dark" 
                                title="Revoke admin privileges">
                                <i class="fas fa-user-lock"></i> Revoke Admin
                            </a>
                        @endif

                    </td>
                </tr>
                
            @empty
                <tr>
                    <td colspan="8" class="text-center">
                        <i class="fas fa-ban"></i> No users found.
                    </td>
                </tr>
                
            @endforelse

            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            {{ $users->links() }}
        </div>

    </div>

@endsection

@section('scripts')

    <script>

        // DETACH DOCUMENT
        if($("#detachUserQuery").length > 0){
            $(document).ready(function () {
                $("body").on("click", "#detachUserQuery", function(e){
                    e.preventDefault();
                    delete_resource(this);
                });
            });
        }

    </script>

@endsection