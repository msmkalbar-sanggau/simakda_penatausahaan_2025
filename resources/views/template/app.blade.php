<!doctype html>
<html lang="en">

@include('template.head')

@if (Auth::user()->is_admin == 1)

    <body data-sidebar='light'>
    @else

        <body data-sidebar="dark">
@endif
<div id="overlay">
    <div class="cv-spinner">
        <span class="spinner"></span>
    </div>
</div>

<!-- <body data-layout="horizontal" data-topbar="colored"> -->

<!-- Begin page -->
<div id="layout-wrapper">


    @include('template.header')
    <!-- ========== Left Sidebar Start ========== -->
    <div class="vertical-menu">

        <!-- LOGO -->
        <div class="navbar-brand-box">
            <a href="index.html" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ asset('template/assets/images/logo-sm.png') }}" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('template/assets/images/logo-dark.png') }}" alt="" height="20">
                </span>
            </a>

            <a href="index.html" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ asset('template/assets/images/logo-sm.png') }}" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('template/assets/images/logo-light.png') }}" alt="" height="20">
                </span>
            </a>
        </div>

        <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
            <i class="fa fa-fw fa-bars"></i>
        </button>

        @include('template.sidebar')
    </div>
    <!-- Left Sidebar End -->



    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                @yield('content')

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->


        @include('template.footer')
    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->



<!-- Right Sidebar -->
@include('template.right-sidebar')
<!-- /Right-bar -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<!-- JAVASCRIPT -->
@include('template.js')


</body>

</html>
