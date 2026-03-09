<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Medical booking system')</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/manual.css') }}">
    <link rel="stylesheet" href="{{ asset('css/component.css') }}?v={{ filemtime(public_path('css/component.css')) }}">

    {{-- DATATABLES with Responsive Extension --}}
    <link rel="stylesheet" href__="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href__="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    {{-- END DATATABLES --}}

<link rel="stylesheet" href="{{ asset('css/claude.css') }}?v={{ filemtime(public_path('css/claude.css')) }}">
    {{-- END DATATABLES --}}
    {{-- manual js --}}
<script src="{{ asset('js/claude.js') }}?v={{ filemtime(public_path('js/claude.js')) }}" defer></script>

    {{-- end --}}
    {{-- NICE SELECT --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    {{-- END --}}
    @stack('styles')
</head>

<body class="{{ auth()->check() && auth()->user()->email === 'abinav@soulcreationz.com' ? 'has-sidebar' : '' }}">
    @php
    use Illuminate\Support\Facades\Storage;
@endphp
    <div class="d-flex flex-column min-vh-100">

        {{-- HEADER --}}
        @include('layouts.header')

        <div class="d-flex flex-fill flex-grow-1">

    {{-- SIDEBAR ONLY FOR ABINAV --}}
    @if(auth()->check() && auth()->user()->email === 'abinav@soulcreationz.com')
        @include('layouts.sidebar')
    @endif

    {{-- MAIN CONTENT --}}
    <main class="flex-fill p-4">
        @yield('content')
    </main>

</div>


        {{-- FOOTER --}}
        @include('layouts.footer')

    </div>


    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{-- JQUERY END --}}
    {{-- DATATABLES --}}
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    {{-- END DATATABLES --}}
    {{-- selecttwo --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- end --}}
    <script src="{{ asset('js/claude.js') }}" defer></script>
    @stack('scripts')
</body>

</html>
