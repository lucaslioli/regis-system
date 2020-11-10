<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Lucas Lima" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'REGIS System') }}</title>

    <link rel="shortcut icon" href="{{ asset('images/regis-favicon.png') }}" />

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @yield('include')

</head>
<body>
    <div id="app" class="app">

        {{-- NAVBAR --}}

        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">

                <div class="navbar-all-brand d-flex align-items-center">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{-- {{ config('app.name', 'REGIS System') }} --}}
                        <img src="{{ asset('images/regis-logo.png') }}" alt="logo">
                    </a>
                    <span class="navbar-text">
                        Retrieval Evaluation for <br>Geoscientific Information Systems
                    </span>
                </div>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item {{ Request::is('judgments/create') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('judgments.create') }}">Annotation</a>
                            </li>

                            <li class="nav-item {{ Request::is('judgments/index') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('judgments.index') }}">My Annotations</a>
                            </li>

                            <li class="nav-item {{ Request::is('tiebreaks/*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('tiebreaks.index') }}">Tiebreaks</a>
                            </li>

                            <li class="nav-item nav-divisor"></li>

                            @can('id-admin')

                                <li class="nav-item {{ Request::is('documents/*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('documents.index') }}">Documents</a>
                                </li>

                                <li class="nav-item {{ Request::is('queries/*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('queries.index') }}">Queries</a>
                                </li>

                                <li class="nav-item {{ Request::is('users/index') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                                </li>

                                <li class="nav-item d-flex align-items-center ml-2 mr-2">
                                    
                                </li>

                                <li class="nav-item nav-divisor"></li>

                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" 
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        Project
                                    </a>
    
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('queries.qrels') }}">
                                            <i class="fas fa-download"></i> Download qrels
                                        </a>

                                        <a class="dropdown-item" href="{{ route('basic_seach') }}">
                                            <i class="fas fa-search"></i> Basic Search
                                        </a>
                                    </div>
                                </li>

                            @endcan

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        {{-- CONTENT --}}

        <main class="pt-4 content">
            @yield('content')
        </main>

    </div>

    {{-- FOOTER --}}

    <footer class="footer font-small bg-white pt-4">

        <div class="container">
    
            <div class="row">
        
                <div class="col-md-4">
        
                    <h5 class="text-uppercase">About</h5>
                    <p>REGIS is a test colletction for Multimodal Information Retrieval composed of queries, documents, and relevance judgements.
                    REGIS is being developed as part of a collaboration between the Institute of Informatics at UFRGS and Petrobras.
                    </p>
        
                </div>
        
                <div class="col-md-2">
        
                    <h5 class="text-uppercase">Links</h5>
            
                    <ul class="list-unstyled">
                        <li>
                            <a href="https://github.com/lucaslioli/regis-system" target="_blank">Github</a>
                        </li>
                        <li>
                            <a href="http://inf.ufrgs.br" target="_blank">Institute of Informatics</a>
                        </li>
                        <li>
                            <a href="http://ufrgs.br" target="_blank">UFRGS</a>
                        </li>
                        <li>
                            <a href="https://petrobras.com.br/" target="_blank">Petrobras</a>
                        </li>
                    </ul>
        
                </div>

                <div class="col-md-3">

                    <h5>&nbsp;</h5>
                    
                    <div class="d-flex">
                        <div class="logo">
                            <img src="{{ asset('images/logo-ppgc.png') }}" alt="Logo PPGC">
                        </div>
                        <div class="logo">
                            <img src="{{ asset('images/logo-ufrgs.png') }}" alt="Logo UFRGS">
                        </div>
                        <div class="logo">
                            <img src="{{ asset('images/logo-petrobras.png') }}" alt="Logo Petrobras">
                        </div>
                    </div>
                    
                </div>
        
                <div class="col-md-3">
        
                    <h5 class="text-uppercase">Address</h5>
                    <p>Institute of Informatics, UFRGS</b><br>Av. Bento Gonçalves, 9500<br>Porto Alegre, RS, Brasil<br>91501-970</p>
        
                </div>
        
            </div>
    
        </div>
    
    </footer>

    @yield('scripts')

</body>
</html>
