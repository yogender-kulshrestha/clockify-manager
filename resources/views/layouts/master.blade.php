<!--
|--------------------------------------------------------------------------
| master
|--------------------------------------------------------------------------
|
| This view handles all the mail content for view file like css, js and html.
|
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('assets/img/apple-icon.png')}}">
    <link rel="icon" type="image/png" href="{{asset('assets/img/favicon.png')}}">

    <title>{{ config('app.name', 'Clockify') }} :: @yield('title')</title>

    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{asset('assets/css/nucleo-icons.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/css/spinner.css')}}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="{{asset('assets/css/nucleo-svg.css')}}" rel="stylesheet" />
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
    <link id="pagestyle" href="{{asset('assets/css/soft-ui-dashboard.css?v=1.0.5')}}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    @yield('style')
    <style>
        .dataTables_info {
            font-size: small;
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">
<div id="spinner">
    <img src="{{ asset('assets/img/loading-buffering.gif') }}"/>
</div>
{{--@include('layouts.sidebar')--}}
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
@include('layouts.header-employee')
<!-- End Navbar -->
    <div class="container-fluid py-4">
        <!-- Start Content -->
    @yield('content')
    <!-- End Content -->
        <!-- Start Footer -->
    @include('layouts.footer')
    <!-- End Footer -->
    </div>
</main>
@include('layouts.theme')
<!--   Core JS Files   -->
<script src="{{asset('assets/js/core/popper.min.js')}}"></script>
<script src="{{asset('assets/js/core/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/smooth-scrollbar.min.js')}}"></script>
<!-- Kanban scripts -->
<script src="{{asset('assets/js/plugins/dragula/dragula.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/jkanban/jkanban.js')}}"></script>
<script src="{{asset('assets/js/plugins/chartjs.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/threejs.js')}}"></script>
<script src="{{asset('assets/js/plugins/orbit-controls.js')}}"></script>
<script src="{{asset('assets/js/plugins/sweetalert.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- Start Toastr msg -->
<script type="text/javascript">
    @if(Session()->has('success'))
        toastr.options = {"progressBar": true}
    toastr.success('{{ Session('success') }}')
    @endif
        @if(Session()->has('info'))
        toastr.options = {"progressBar": true}
    toastr.info('{{ Session('info') }}')
    @endif
        @if(Session()->has('error'))
        toastr.options = {"progressBar": true}
    toastr.error('{{ Session('error') }}')
    @endif
        @if(Session()->has('warning'))
        toastr.options = {"progressBar": true}
    toastr.warning('{{ Session('warning') }}')
    @endif
    function numbersonly(e) {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) { //if the key isn't the backspace key (which we should allow)
            if (unicode != 46 && unicode > 31 && unicode < 48 || unicode > 57) //if not a number
                return false //disable key press
        }
    }
</script>
<!-- End Toastr msg -->
@yield('script')
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{asset('assets/js/soft-ui-dashboard.min.js?v=1.0.5')}}"></script>
<script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
<!-- Start Spinner js -->
<script type="text/javascript">
    function spinnershow() {
        document.getElementById("spinner").classList.add("show");
    }
    function spinnerhide() {
        document.getElementById("spinner").classList.remove("show");
    }
</script>
<!-- End Spinner js -->
</body>

</html>
