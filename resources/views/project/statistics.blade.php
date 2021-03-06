@extends('layouts.app')

@section('include')
    {{-- include --}}
    <script src="{{ asset('js/charts_loader.js') }}"></script>
@endsection

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">
            <h1>Project statistics</h1>
            <div class="d-flex align-items-baseline">
                <a href="{{ route('project.statistics') }}"  class="btn btn-link" type="button">
                    <i class="fas fa-redo"></i> Refresh
                </a>
            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-between">
            <span><strong>Total judgments:</strong> {{ $total['judgments'] }}</span>
            <span><strong>Total tiebreaks:</strong> {{ $total['tiebreaks']+$total['solved'] }}</span>
            <span><strong>Solved tiebreaks:</strong> {{ $total['solved'] }}</span>
            <span><strong>Remaining tiebreaks:</strong> {{ $total['tiebreaks'] }}</span>
        </div>

        <hr>

        <div class="d-flex justify-content-between">
            <div id="queriesStatusChart" style="width: 500px; height: 330px;"></div>
            <div id="judgmentsChart" style="width: 550px; height: 330px;"></div>
        </div>

        <div class="d-flex justify-content-between">
            <div class="w-50 mt-5 pr-4">
                <h4>Completed queries</h4>
                <table class="table table-hover table-sm table-stats">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Query</th>
                            <th scope="col" class="text-center">Very R.</th>
                            <th scope="col" class="text-center">Fairly R.</th>
                            <th scope="col" class="text-center">Marginally</th>
                            <th scope="col" class="text-center">Not R.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($completed_queries as $key => $query)
                            <tr>
                                <th scope="row">{{ $key+1 }}</th>
                                
                                <td title="{{ $query['title'] }}">
                                    <a href="{{ route('queries.edit', $query['id']) }}" title="View query">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{ $query['qry_id'] }}
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge badge-pill badge-success">{{ $query['very'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-primary">{{ $query['relevant'] }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-advise">{{ $query['marginally'] }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">{{ $query['not'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="w-50 mt-5 pl-4">
                <h4>Annotators ranking</h4>
                <table class="table table-hover table-sm table-stats table-stats-user">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">User</th>
                            <th scope="col" class="text-center">Judgments</th>
                            <th scope="col" class="text-center">Queries</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $key => $user)
                            <tr>
                                <th scope="row">{{ $key+1 }}</th>
                                <td>{{ $user['name'] }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-primary">{{ $user['judgments'] }}</span>
                                </td>
                                <td class="text-center">{{ $user['queries'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <hr>

        <div class="d-flex align-items-baseline">
            <span><strong>Download analysis data:</strong></span>
            
            <a href="{{ route('project.solvedTiebreaks') }}"  class="btn btn-link" type="button"
                data-toggle="tooltip" data-placement="bottom"
                title="Export solved tiebreaks">
                <i class="fas fa-download"></i> Solved tiebreaks
            </a>

            <a href="{{ route('project.judgmentsKappa') }}"  class="btn btn-link" type="button"
                data-toggle="tooltip" data-placement="bottom"
                title="Export judgments to calculate Fleiss Kappa">
                <i class="fas fa-download"></i> Judgments for Kappa
            </a>
        </div>

    </div>

@endsection

@section('scripts')
    {{-- scripts --}}
    <script type="text/javascript">
        var queries_status = @php echo $queries_status_json; @endphp;
        var judgments = @php echo $judgments_json; @endphp;

        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawQueryStatusChart);
        google.charts.setOnLoadCallback(drawJudgmentsChart);

        function drawQueryStatusChart() {
            var data = google.visualization.arrayToDataTable(queries_status);
            
            var options = {
                title: 'Queris Status',
                chartArea:{left:50,top:30,width:'85%',height:'85%'},
                backgroundColor: 'transparent',
                fontSize: '14',
                slices: {
                    0: {color: '#28a745', offset: 0.1}, 
                    1: {color: '#6c757d'},
                    2: {color: '#007bff'}
                }
            };
          
            var chart = new google.visualization.PieChart(document.getElementById('queriesStatusChart'));
            chart.draw(data, options);
        }

        function drawJudgmentsChart() {
            judgments[0].push({role: 'style'}, {role: 'annotation'});
            judgments[1].push('#28a745', judgments[1][1]);
            judgments[2].push('#007bff', judgments[2][1]);
            judgments[3].push('#ff9a00', judgments[3][1]);
            judgments[4].push('#e3342f', judgments[4][1]);

            var data = google.visualization.arrayToDataTable(judgments);
            
            var options = {
                title: 'Judgments',
                chartArea:{left:70,top:30,width:'85%',height:'85%'},
                legend: { position: "none" },
                backgroundColor: 'transparent',
                fontSize: '14',
            };
          
            var chart = new google.visualization.BarChart(document.getElementById('judgmentsChart'));
            chart.draw(data, options);
        }
    </script>
  
@endsection