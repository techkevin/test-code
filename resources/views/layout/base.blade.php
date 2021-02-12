<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Backoffice - Printerqoe">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="lang" content="{{ App::getLocale() }}">

    <title>@yield('title', 'User')</title>

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <link rel="icon" href="/favicon.ico"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    @stack('css')
</head>
<body>
    <div id="app">
        @yield('body')
    </div>

    @include ('layout.script')
</body>
</html>
