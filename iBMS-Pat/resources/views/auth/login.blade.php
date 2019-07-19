<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'iBMS') }} </title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navigation.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet"> -->
</head>
<body class="bg-image">
    <div id="app">
        <login></login>
    </div>

    <!-- Scripts -->
    <script src="//{{ env('IP_PUSH') }}:6002/socket.io/socket.io.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/sb-admin.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/document-register-element/1.4.1/document-register-element.js"></script>
</body>
</html>