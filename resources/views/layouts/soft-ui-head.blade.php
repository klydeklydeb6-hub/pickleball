<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('soft-ui-dashboard-main/assets/img/apple-icon.png') }}">
<link rel="icon" type="image/png" href="{{ asset('soft-ui-dashboard-main/assets/img/favicon.png') }}">

<title>{{ $title ?? config('app.name', 'Pickle BALLan Ni Juan') }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
<link href="{{ asset('soft-ui-dashboard-main/assets/css/nucleo-icons.css') }}" rel="stylesheet">
<link href="{{ asset('soft-ui-dashboard-main/assets/css/nucleo-svg.css') }}" rel="stylesheet">
<link href="{{ asset('soft-ui-dashboard-main/assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])
<link id="pagestyle" href="{{ asset('soft-ui-dashboard-main/assets/css/soft-ui-dashboard.css?v=1.1.0') }}" rel="stylesheet">
@stack('styles')
