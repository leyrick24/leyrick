<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<!-- <html lang="{{ app()->getLocale() }}" oncontextmenu="return false"> -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'iBMS') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
            {{-- @foreach($modules as $module)
            <div>{{$module}}</div>
            @endforeach --}}
            <a class="navbar-brand" href="/">iBMS</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav navbar-sidenav">
                    {{-- @foreach ($modules as $module) --}}
                        {{-- @if(auth()->user()->USER_TYPE == 1 || auth()->user()->USER_TYPE == 2 || auth()->user()->USER_TYPE == 3 || in_array('Dashboard', $modules)) --}}
                            <li class="nav-item {{ Request::is('/') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Dashboard">
                                <a class="nav-link" href="/">
                                    <i class="fa fa-fw fa-dashboard"></i>
                                    <span class="nav-link-text">Dashboard</span>
                                    {{-- <span class="nav-link-text"></span> --}}
                                </a>
                            </li>
                        {{-- @endif --}}

                        {{-- @if(in_array('Device Operation', $modules)) --}}
                            <li class="nav-item {{ Request::is('floor-view*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Floors">
                                <a class="nav-link" href="floor-view">
                                    <i class="fa fa-location-arrow"></i>
                                    <span class="nav-link-text">Device Operation</span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        {{-- @if(auth()->user()->USER_TYPE == 1 || auth()->user()->USER_TYPE == 2) --}}
                        {{-- @if(auth()->user()->USER_TYPE == 1 && in_array('User Management', $modules)) --}}
                        @if(in_array('User Management', $modules))
                            <li class="nav-item {{ Request::is('user-management*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="User Management">
                                <a class="nav-link" href="user-management">
                                    <i class="fa fa-users"></i>
                                    <span class="nav-link-text">User Management</span>
                                </a>
                            </li>
                        @endif

                        {{-- @if(auth()->user()->USER_TYPE == 1 || in_array('Hardware Management', $modules)) --}}
                        {{-- @if((auth()->user()->USER_TYPE == 1 || auth()->user()->USER_TYPE == 2) && in_array('Gateway Management', $modules) || in_array('Device Management', $modules) || in_array('Binding Management', $modules) || in_array('IR Management', $modules)) --}}
                        {{-- @if(in_array('Gateway Management', $modules) || in_array('Device Management', $modules) || in_array('Binding Management', $modules) || in_array('IR Management', $modules)) --}}
                        @if(count(array_intersect(['Gateway Management','Device Management','Binding Management','IR Management',], $modules)) != 0)
                            <!-- <li class="nav-item {{ Request::is('hardware-management*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Hardware Management">
                                <a class="nav-link" href="hardware-management">
                                    <i class="fa fa-fw fa-microchip"></i>
                                    <span class="nav-link-text">Hardware Management</span>
                                </a>
                            </li> -->
                            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Hardware Management">
                                <a class="nav-link accordion-heading" data-toggle="collapse" data-target="#hardware-sub-menu">
                                    <!-- <span class="nav-header-primary">Menu Link <span class="pull-right"><b class="caret"></b></span></span> -->
                                    <i class="fa fa-fw fa-microchip"></i>
                                    <span class="nav-link-text">Hardware Management</span>
                                    <i class="fa fa-fw fa-caret-down"></i>
                                </a>
                                <ul class="nav nav-list collapse ml-4" id="hardware-sub-menu">
                                    @if(in_array('Gateway Management', $modules))
                                        <li class="nav-item {{ Request::is('gateway-management*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Gateway Management">
                                            <a class="nav-link accordion-heading" href="gateway-management">
                                                <i class="fa fa-fw fa-server"></i>
                                                <span class="nav-link-text">Gateway Management</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if(in_array('Device Management', $modules))
                                        <li class="nav-item {{ Request::is('device-management*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Device Management">
                                            <a class="nav-link accordion-heading" href="device-management">
                                                <i class="fa fa-fw fa-tablet"></i>
                                                <span class="nav-link-text">Device Management</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if(in_array('Binding Management', $modules))
                                        <li class="nav-item {{ Request::is('binding-management*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Binding Management">
                                            <a class="nav-link accordion-heading" href="binding-management">
                                                <i class="fa fa-fw fa-refresh"></i>
                                                <span class="nav-link-text">Binding Management</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if(in_array('IR Management', $modules))
                                        <li class="nav-item {{ Request::is('ir-management*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="IR Management">
                                            <a class="nav-link accordion-heading" href="ir-management">
                                                <i class="fa fa-fw fa-plus"></i>
                                                <span class="nav-link-text">IR Management</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- @if(auth()->user()->USER_TYPE == 1 && in_array('Floor Management', $modules)) --}}
                        @if(in_array('Floor Management', $modules))
                            <li class="nav-item {{ Request::is('floor-management*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Floor Management">
                                <a class="nav-link" href="floor-management">
                                    <i class="fa fa-fw fa-wrench"></i>
                                    <span class="nav-link-text">Floor Management</span>
                                </a>
                            </li>
                        @endif

                        {{-- @if(auth()->user()->USER_TYPE == 1 || auth()->user()->USER_TYPE == 2) --}}
                        {{-- @if(auth()->user()->USER_TYPE == 1 && in_array('Logs', $modules)) --}}
                        @if(in_array('Logs', $modules))
                            <li class="nav-item {{ Request::is('logs*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Logs">
                                <a class="nav-link" href="logs">
                                    <i class="fa fa-history"></i>
                                    <span class="nav-link-text">Logs</span>
                                </a>
                            </li>
                        @endif
                    {{-- @endforeach --}}
                </ul>
                <ul class="navbar-nav sidenav-toggler">
                    <li class="nav-item">
                        <a class="nav-link text-center" id="sidenavToggler">
                            <i class="fa fa-fw fa-angle-left"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <notification :userid="{{ auth()->user()->USER_ID }}"></notification>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle mr-lg-2" id="alertsDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Welcome {{ auth()->user()->USERNAME }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="alertsDropdown">
                            <a class="btn dropdown-item" data-toggle="modal" data-target="#exampleModal">
                                <span class="text-default">
                                <strong>
                                    <i class="fa fa-fw fa-sign-out"></i>Logout
                                </strong>
                                </span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Logout Modal-->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hide">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
        @yield('content')
    </div>
    <!-- Scripts -->
    <script src="//{{ env('IP_PUSH') }}:6001/socket.io/socket.io.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/sb-admin.js') }}"></script>
    <script>
        // console.log(window.location.hostname);
        // Echo.channel('test').listen('.testEvent', (e) => {
        //     console.log(e.data);
        // });
        // $(document).keydown(function(e){
        //     if(e.which === 123){
        //        return false;
        //     }
        // });
    </script>
</body>
</html>
