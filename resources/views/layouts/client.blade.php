<?php $setting = DB::table('settings')->where('deleted_at', '=', null)->first(); ?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel=icon href={{ asset('assets_setup/images/logo.png') }}>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> WorkTick- Ultimate HRM & Project Management</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
    @yield('before-css')
    {{-- theme css --}}


    @if (Session::get('layout') == 'vertical')
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-free-5.10.1-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/metisMenu.min.css') }}">

    @endif
    <link id="worktick-theme" rel="stylesheet" href="{{ asset('assets\fonts\iconsmind\iconsmind.css') }}">
    <link id="worktick-theme" rel="stylesheet" href="{{ asset('assets/styles/css/themes/lite-purple.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/toastr.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/vue-select.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">

    {{-- page specific css --}}
    @yield('page-css')
</head>



<body class="text-left">
    @php
    $layout = session('layout');
    @endphp

    <!-- Pre Loader Strat  -->
    <div class='loadscreen' id="preloader">

        <div class="loader spinner-bubble spinner-bubble-primary">

        </div>
    </div>
    <!-- Pre Loader end  -->

    <!-- ============ Large SIdebar Layout start ============= -->
    @if($layout=="normal")


    @include('layouts.large-vertical-sidebar.master')

    <!-- ============ Large Sidebar Layout End ============= -->

    @else
    <!-- ============Deafult  Large SIdebar Layout start ============= -->

    @include('layouts.large-vertical-sidebar.master')


    <!-- ============ Large Sidebar Layout End ============= -->

    @endif

    <!-- ============ Customizer UI Start ============= -->
    @include('layouts.common.customizer')
    <!-- ============ Customizer UI Start ============= -->

    {{-- vue js --}}
    <script src="{{ asset('assets/js/vue.js') }}"></script>

    {{-- axios js --}}
    <script src="{{ asset('assets/js/axios.js') }}"></script>

    {{-- vue select js --}}
    <script src="{{ asset('assets/js/vue-select.js') }}"></script>



    {{-- sweetalert2 --}}
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>

    {{-- common js --}}
    <script src="{{ asset('assets/js/common-bundle-script.js') }}"></script>
    {{-- page specific javascript --}}
    @yield('page-js')

    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script src="{{asset('assets/js/vendor/toastr.min.js')}}"></script>
    <script src="{{asset('assets/js/toastr.script.js')}}"></script>

    @if ($layout == 'compact')
    <script src="{{ asset('assets/js/sidebar.compact.script.js') }}"></script>


    @elseif($layout=='normal')
    <script src="{{ asset('assets/js/sidebar.large.script.js') }}"></script>


    @else
    <script src="{{ asset('assets/js/sidebar.large.script.js') }}"></script>

    @endif



    <script src="{{ asset('assets/js/customizer.script.js') }}"></script>


    @yield('bottom-js')

</body>

</html>