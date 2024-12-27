<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">


<!-- Mirrored from themesbrand.com/minible/layouts/auth-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 Aug 2022 03:52:06 GMT -->

<head>

    <meta charset="utf-8" />
    <title>{{ 'SIMAKDA' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('template/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('template/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('template/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        .authentication-bgs {
            background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
            background-size: 100% auto;
        }

        */
        /* For Desktop View */
        /* 1920 × 1080 */

        @media screen and (min-width: 1500px) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }

        @media screen and (min-width: 1422px) and (max-device-width: 1500px) {
            .authentication-bgs {
                background-image: url('https://simakda.kalbarprov.go.id/list/template/assets/bg/lebaran-bg14.png');
                background-size: 100% auto;
            }
        }

        @media screen and (min-width: 1366px) and (max-device-width: 1422px) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }

        @media screen and (min-width: 1024px) and (max-device-width: 1366px) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For Tablet View */
        @media screen and (min-device-width: 768px) and (max-device-width: 1024px) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For Mobile Portrait View */
        @media screen and (max-device-width: 480px) and (orientation: portrait) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For Mobile Landscape View */
        @media screen and (max-device-width: 640px) and (orientation: landscape) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For Mobile Phones Portrait or Landscape View */
        @media screen and (max-device-width: 640px) {
            .authentication-bgs {

                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For iPhone 4 Portrait or Landscape View */
        @media screen and (min-device-width: 320px) and (-webkit-min-device-pixel-ratio: 2) {
            .authentication-bgs {

                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For iPhone 5 Portrait or Landscape View */
        @media (device-height: 568px) and (device-width: 320px) and (-webkit-min-device-pixel-ratio: 2) {
            .authentication-bgs {

                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For iPhone 6 and 6 plus Portrait or Landscape View */
        @media (min-device-height: 667px) and (min-device-width: 375px) and (-webkit-min-device-pixel-ratio: 3) {
            .authentication-bgs {

                background-image: url('{{ asset('template/assets/images/bg3.jpg') }}');
                background-size: 100% auto;
            }
        }




        .authentication-bgs .auth-logo .logo {
            margin: 0px auto;
        }
    </style>
</head>

{{-- nyalakan jika background gambar}}
{{--  <body class="authentication-bgs">  --}}

<body class="authentication-bgs">
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <a href="index.html" class="mb-5 d-block auth-logo">
                            <img src="{{ asset('template/assets/images/logo-dark.png') }}" alt=""
                                height="50" class="logo logo-dark">
                            <img src="{{ asset('template/assets/images/logo-light.png') }}" alt=""
                                height="50" class="logo logo-light">
                        </a>
                    </div>
                </div>
            </div>
            <div class="row align-items-center justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <table width="100%" border="0">
                                    <tr>
                                        <td width="25%" align="center">
                                            <img src="{{ asset('template/assets/images/logo_pemda_hp.png') }}"
                                                alt="" height="100" class="logo logo-dark">
                                        </td>
                                        <td width="75%" align="left">
                                            <br>
                                            <h5 class="text-primary">{{ $daerah->nm_pemda }}</h5>
                                            <p style="font-size:12px" class="text-muted">{{ $daerah->nm_badan }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @if (session('message'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('message') }}
                                </div>
                            @endif
                            <div class="p-2 mt-4">
                                <form method="POST" action="{{ route('login.index') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label" for="username">Username</label>
                                        <input id="username" type="text"
                                            class="form-control @error('username') is-invalid @enderror" name="username"
                                            value="{{ old('username') }}" required autocomplete="username" autofocus>
                                        @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <div class="float-end">
                                            <a href="auth-recoverpw.html" class="text-muted">Forgot password?</a>
                                        </div>
                                        <label class="form-label" for="userpassword">Password</label>
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="current-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>

                                    <div class="mt-3 text-end">
                                        <button type="submit"
                                            class="btn btn-primary w-sm waves-effect waves-light  -,k">
                                            {{ __('Login') }}
                                        </button>

                                        @if (Route::has('password.request'))
                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        @endif
                                    </div>



                                    {{-- <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="font-size-14 mb-3 title">Sign in with</h5>
                                            </div>


                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                    <a href="javascript:void()" class="social-list-item bg-primary text-white border-primary">
                                                        <i class="mdi mdi-facebook"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript:void()" class="social-list-item bg-info text-white border-info">
                                                        <i class="mdi mdi-twitter"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript:void()" class="social-list-item bg-danger text-white border-danger">
                                                        <i class="mdi mdi-google"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div> --}}

                                    {{-- <div class="mt-4 text-center">
                                            <p class="mb-0">Don't have an account ? <a href="auth-register.html" class="fw-medium text-primary"> Signup now </a> </p>
                                        </div> --}}
                                </form>
                            </div>

                        </div>
                    </div>

                    {{-- <div class="mt-5 text-center">
                            <p>© <script>document.write(new Date().getFullYear())</script> SIMAKDA V.5 by PT. MSM Cabang Kalbar</p>
                        </div> --}}

                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('template/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('template/assets/js/app.js') }}"></script>

</body>

<!-- Mirrored from themesbrand.com/minible/layouts/auth-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 Aug 2022 03:52:06 GMT -->

</html>




{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="username" class="col-md-4 col-form-label text-md-end">{{ __('Username') }}</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>

                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
