@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">
            <h1>Annotation Ranking</h1>
        </div>

        <hr>

        <br>

        <div id="response" role="alert"></div>

        <table class="table table-hover table-ranking">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"></th>
                    <th scope="col">User name</th>
                    <th scope="col" class="text-center">Total judgments</th>
                    <th scope="col" class="text-center">Total queries</th>
                </tr>
            </thead>
            <tbody>

            @forelse ($users as $key => $user)

                <tr>
                    <td>{{ $key+1 }}</td>

                    <td>
                        @switch($key)
                            @case(0)
                                <i class="fas fa-trophy gold-trophy"></i>
                                @break
                            @case(1)
                                <i class="fas fa-trophy silver-trophy"></i>
                                @break
                            @case(2)
                                <i class="fas fa-trophy bronze-trophy"></i>
                                @break
                        @endswitch
                    </td>
                    
                    <td>{{ $user['name'] }}</td>
                    
                    <td class="text-center">
                        <span class="badge badge-pill badge-{{ ($user['judgments']) ? 'primary' : 'secondary' }}">
                            {{ $user['judgments'] ?? 0 }}
                        </span>
                    </td>
                    
                    <td class="text-center">{{ $user['queries'] }}</td>
                </tr>

            @empty

                <tr>
                    <td colspan="4" class="text-center">
                        <i class="fas fa-ban"></i> No judgments have been made.
                    </td>
                </tr>
                
            @endforelse

            </tbody>
        </table>

    </div>

@endsection
