<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'iBMS') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navigation.css') }}" rel="stylesheet">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/basics.css')}}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/document-register-element/1.4.1/document-register-element.js"></script>
</head>
<body class="fixed-nav sticky-footer bg-image" id="page-top">
    <div id="app">
        <nav class="navbar navbar-dark nav-bar-top bg-dark-nav navbar-expand-lg">
            <a class="navbar-title" href="/"><span class="custom_i">i</span>BMS</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                        <a class="nav-link" href="/">
                            <i class="fa fa-fw fa-dashboard"></i>
                            <span id="dashboard" class="nav-link-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('floor-view*') ? 'active' : '' }}">
                        <a class="nav-link" href="floor-view">
                            
                            <i class="fa fa-fw fa-location-arrow"></i>
                            <span id="devop" class="nav-link-text">Device Operation</span>
                        </a>
                    </li>
                    @if(in_array('User Management', $modules))
                        <li class="nav-item {{ Request::is('user-management*') ? 'active' : '' }}">
                            <a class="nav-link" href="user-management">
                                <i class="fa fa-users"></i>
                                <span id="userm" class="nav-link-text">User Management</span>
                            </a>
                        </li>
                    @endif
                    @if(count(array_intersect(['Gateway Management','Device Management','Binding Management','IR Management',], $modules)) != 0)
                        <li class="nav-item dropdown {{ Request::is('gateway-management*') || Request::is('device-management*') || Request::is('binding-management*') || Request::is('ir-management*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-cogs"></i>
                                <span id="hardwarem" class="nav-link-text">Hardware Management</span>
                            </a>
                            <div class="dropdown-menu nav-hardware">
                                @if(in_array('Gateway Management', $modules))
                                    <a id="gatewaym" class="dropdown-item" href="gateway-management">Gateway Management</a>
                                @endif
                                @if(in_array('Device Management', $modules))
                                    <a id="devicem" class="dropdown-item" href="device-management">Device Management</a>
                                @endif
                                @if(in_array('Binding Management', $modules))
                                    <a id="bindingm" class="dropdown-item" href="binding-management">Binding Management</a>
                                @endif
                                @if(in_array('IR Management', $modules))
                                    <a id="ir-m" class="dropdown-item" href="ir-management">IR Management</a>
                                @endif
                            </div>
                        </li>
                    @endif
                    @if(in_array('Floor Management', $modules))
                    <li class="nav-item {{ Request::is('floor-management*') ? 'active' : '' }}">
                        <a class="nav-link" href="floor-management">
                            <i class="fa fa-fw fa-building"></i>
                            <span id="floorm" class="nav-link-text">Floor Management</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array('Logs', $modules))
                    <li class="nav-item {{ Request::is('logs*') ? 'active' : '' }}">
                        <a class="nav-link" href="logs">
                            <i class="fa fa-history"></i>
                            <span id="logs" class="nav-link-text">Logs</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item {{ Request::is('about*') ? 'active' : '' }}">
                        <a class="nav-link" href="about">
                            <i class="fa fa-edit"></i>
                            <span id="about" class="nav-link-text">About Us</span>
                        </a>
                    </li>
                    <!-- for language select -->
                </ul>
                <ul class="navbar-nav ml-auto">
                    <notification :userid="{{ auth()->user()->USER_ID }}"></notification>
                    <li class="nav-item dropdown">
                        <usersettings :userid="{{ auth()->user()->USER_ID }}"></usersettings>
                        <div class="dropdown-menu dropdown-menu-right nav-profile-dropdown">
                            <a class="dropdown-item" href="logs">
                                <span class="text-default">
                                    <strong id="notif">
                                        Notification<i class="fa fa-fw fa-history"></i>
                                    </strong>
                                </span>
                            </a>
                            <a class="dropdown-item" data-toggle="modal" href="#logoutModal">
                                <span class="text-default">
                                    <strong id="logout">
                                        Logout<i class="fa fa-fw fa-sign-out"></i>
                                    </strong>
                                </span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="help">
                                <span class="text-default">
                                    <strong id="help">
                                        Help<i class="fa fa-fw fa-question-circle"></i>
                                    </strong>
                                </span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-submenu">
                                <a href="#" class="dropdown-item dropdown-toggle">
                                    <span class="text-default">
                                        <strong id="lang">
                                            Language
                                        </strong>
                                    </span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="#" class="dropdown-item" @click="changeLocale('en')">English</a></li>
                                    <li><a href="#" class="dropdown-item" @click="changeLocale('ja')">日本語</a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body" id="logoutMessage">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal" id="cancel">Cancel</button>
                        <a class="btn btn-primary" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" id="confirmLogout">Logout</a>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
        @yield('content')
    </div>
    <!-- Scripts -->
    <script src="//{{ env('IP_PUSH') }}:6002/socket.io/socket.io.js"></script>
    <script type="text/javascript">
        let authUser = {!! Auth::user() ? : '[]' !!};
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        $(window).bind('scroll', function () {
            if ($(window).scrollTop() > 20) {
                $('.navbar').addClass('fixed');
                $('.navbar').removeClass('bg-dark-nav');
            } else {
                $('.navbar').removeClass('fixed');
                $('.navbar').addClass('bg-dark-nav');
            }
        });
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
        $(function () {
          $('[data-toggle="popover"]').popover()
        })
        $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
          if (!$(this).next().hasClass('show')) {
            $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
          }
          var $subMenu = $(this).next(".dropdown-menu");
          $subMenu.toggleClass('show');


          $(this).parents('.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
            $('.dropdown-submenu .show').removeClass("show");
          });


          return false;
        });
    </script>
</body>
</html>
