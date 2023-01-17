@php
$segment2 = Request::segment(2);
$segment3 = Request::segment(3);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{SITE_NAME}}</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--Favicon-->
    @if(SITE_LOGO <> '' && SITE_LOGO <> null)

            <link rel="shortcut icon" href="{!! checkImage(asset('storage/uploads/images/'.@SITE_LOGO)) !!}" type="image/x-icon">
            <link rel="icon" href="{!! checkImage(asset('storage/uploads/images/'.@SITE_LOGO)) !!}" type="image/x-icon">
            @else
            <link rel="shortcut icon" href="{{ _asset('backend/images/favicon.png') }}" type="image/x-icon">
            <link rel="icon" href="{{ _asset('backend/images/favicon.png') }}" type="image/x-icon">
            @endif

            <!-- Google Font: Source Sans Pro -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
            <!-- Font Awesome Icons -->
            <link rel="stylesheet" href="{{ _asset('backend/plugins/fontawesome-free/css/all.min.css') }}">
            <!-- SweetAlert2 -->
            <link rel="stylesheet" href="{{ _asset('backend/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
            <!-- overlayScrollbars -->
            <link rel="stylesheet" href="{{ _asset('backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
            <!-- Theme style -->
            <link rel="stylesheet" href="{{ _asset('backend/css/adminlte.min.css') }}">
            <link rel="stylesheet" href="{{ _asset('backend/css/custom.css') }}">


            <!-- jQuery -->
            <script src="{{ _asset('backend/plugins/jquery/jquery.min.js') }}"></script>
            <!-- Bootstrap 4 -->
            <script src="{{ _asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
            @yield('styles')
            <link rel="stylesheet" href="{{ _asset('backend/plugins/progress/jqprogress.min.css') }}">
            <script>
                var ASSET_URL = '{{asset("storage/uploads")}}/';
            </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <span class="progress"></span>
        <!-- Navbar -->
        @include('admin.sections.header')
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        @include('admin.sections.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            @yield('content')
            @include('sweetalert::alert')
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('admin.sections.footer')
    </div>
    <!-- ./wrapper -->
    <!-- REQUIRED SCRIPTS -->
    <!-- SweetAlert2 -->
    <script src="{{ _asset('backend/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ _asset('backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

    <!-- jquery-validation -->
    <script src="{{ _asset('backend/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ _asset('backend/js/adminlte.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/plugins/progress/jqprogress.min.js')}}"></script>
    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <!-- <script src="{{ _asset('backend/plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script> -->
    <!-- <script src="{{ _asset('backend/plugins/raphael/raphael.min.js') }}"></script> -->
    <!-- <script src="{{ _asset('backend/plugins/jquery-mapael/jquery.mapael.min.js') }}"></script> -->
    <!-- <script src="{{ _asset('backend/plugins/jquery-mapael/maps/usa_states.min.js') }}"></script> -->
    <!-- ChartJS -->
    <!-- <script src="{{ _asset('backend/plugins/chart.js/Chart.min.js') }}"></script> -->
    <!-- AdminLTE for demo purposes -->
    <script src="{{ _asset('backend/js/demo.js') }}"></script>
    @if($segment2 == 'dashboard')
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ _asset('backend/js/pages/dashboard2.js') }}"></script>
    @endif
    @if($segment2 == 'sellers')
    <script type="text/javascript" src="{{ _asset('node/js/wallet-address-validator.min.js') }}"></script>
    @endif
    <script src="{{ _asset('backend/js/custom.js') }}"></script>
    @yield('scripts')
</body>

</html>