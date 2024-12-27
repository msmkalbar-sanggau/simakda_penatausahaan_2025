<div class="right-bar">
    <div data-simplebar class="h-100">
        <div class="rightbar-title d-flex align-items-center p-3">

            <h5 class="m-0 me-2">Profile</h5>

            <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                <i class="mdi mdi-close noti-icon"></i>
            </a>
        </div>

        <!-- Settings -->
        <hr class="m-0" />

        <div class="p-4">
            {{-- <h6 class="mb-3">Layout</h6>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout" id="layout-vertical" value="vertical">
                <label class="form-check-label" for="layout-vertical">Vertical</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout" id="layout-horizontal" value="horizontal">
                <label class="form-check-label" for="layout-horizontal">Horizontal</label>
            </div>

            <h6 class="mt-4 mb-3 pt-2">Layout Mode</h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-mode" id="layout-mode-light" value="light">
                <label class="form-check-label" for="layout-mode-light">Light</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-mode" id="layout-mode-dark" value="dark">
                <label class="form-check-label" for="layout-mode-dark">Dark</label>
            </div>

            <h6 class="mt-4 mb-3 pt-2">Layout Width</h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-width" id="layout-width-fuild"
                    value="fuild" onchange="document.body.setAttribute('data-layout-size', 'fluid')">
                <label class="form-check-label" for="layout-width-fuild">Fluid</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-width" id="layout-width-boxed"
                    value="boxed" onchange="document.body.setAttribute('data-layout-size', 'boxed')">
                <label class="form-check-label" for="layout-width-boxed">Boxed</label>
            </div>

            <h6 class="mt-4 mb-3 pt-2">Layout Position</h6>

            <h6 class="mt-4 mb-3 pt-2">Topbar Color</h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="topbar-color" id="topbar-color-light"
                    value="light" onchange="document.body.setAttribute('data-topbar', 'light')">
                <label class="form-check-label" for="topbar-color-light">Light</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="topbar-color" id="topbar-color-dark" value="dark"
                    onchange="document.body.setAttribute('data-topbar', 'dark')">
                <label class="form-check-label" for="topbar-color-dark">Dark</label>
            </div>

            <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Sidebar Size</h6>

            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-size" id="sidebar-size-default"
                    value="default" onchange="document.body.setAttribute('data-sidebar-size', 'lg')">
                <label class="form-check-label" for="sidebar-size-default">Default</label>
            </div>
            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-size" id="sidebar-size-compact"
                    value="compact" onchange="document.body.setAttribute('data-sidebar-size', 'small')">
                <label class="form-check-label" for="sidebar-size-compact">Compact</label>
            </div>
            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-size" id="sidebar-size-small"
                    value="small" onchange="document.body.setAttribute('data-sidebar-size', 'sm')">
                <label class="form-check-label" for="sidebar-size-small">Small (Icon View)</label>
            </div>

            <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Sidebar Color</h6>

            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-color" id="sidebar-color-light"
                    value="light" onchange="document.body.setAttribute('data-sidebar', 'light')">
                <label class="form-check-label" for="sidebar-color-light">Light</label>
            </div>
            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-color" id="sidebar-color-dark"
                    value="dark" onchange="document.body.setAttribute('data-sidebar', 'dark')">
                <label class="form-check-label" for="sidebar-color-dark">Dark</label>
            </div>
            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-color" id="sidebar-color-colored"
                    value="colored" onchange="document.body.setAttribute('data-sidebar', 'colored')">
                <label class="form-check-label" for="sidebar-color-colored">Colored</label>
            </div>

            <h6 class="mt-4 mb-3 pt-2">Direction</h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-direction" id="layout-direction-ltr"
                    value="ltr">
                <label class="form-check-label" for="layout-direction-ltr">LTR</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-direction" id="layout-direction-rtl"
                    value="rtl">
                <label class="form-check-label" for="layout-direction-rtl">RTL</label>
            </div> --}}

            <div class="card h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="dropdown float-end">
                            <a class="text-body dropdown-toggle font-size-18" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true">
                                <i class="uil uil-ellipsis-v"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Edit Profile</a>
                                <a class="dropdown-item"
                                    href="{{ route('ubah_password', Crypt::encryptString(Auth::user()->id)) }}">Ubah
                                    Pasword</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <img src="{{ asset('template/assets/images/logo_pemda.png') }}" alt=""
                                height="80">
                        </div>
                        <h5 class="mt-3 mb-1">{{ Auth::user()->nama }}</h5>
                        <p class="text-muted">{{ cari_nama(Auth::user()->role, 'peran', 'id', 'nama_role') }}</p>
                        <p class="text-muted">
                            @if ((Auth::user()->is_admin == 2 && Auth::user()->role == 1016) || Auth::user()->is_admin == 1)
                                <a class="dropdown-item bg-warning text-white"
                                    href="{{ route('ubah_skpd', Crypt::encryptString(Auth::user()->id)) }}"><i
                                        class="uil uil-wallet font-size-18 align-middle me-1 text-white"></i> <span
                                        class="align-middle">Ganti SKPD</span></a>
                            @endif
                        </p>
                        <p class="text-muted">
                            <a class="dropdown-item bg-danger text-white"
                                href="{{ route('logout') }}"onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                                    class="uil
                                uil-sign-out-alt font-size-18 align-middle me-1 text-white"></i>
                                <span class="align-middle">Logout</span></a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        </p>


                    </div>

                    <hr class="my-4">

                    {{-- <div class="text-muted"> --}}

                    <div class="table-responsive mt-4">
                        <div class="mt-4">
                            <p class="mb-1">Kode SKPD :</p>
                            <h5 class="font-size-16">{{ Auth::user()->kd_skpd }}</h5>
                        </div>
                        <div class="mt-4">
                            <p class="mb-1">Nama SKPD :</p>
                            <h5 class="font-size-16">
                                {{ cari_nama(Auth::user()->kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd') }}</h5>
                        </div>
                        <div class="mt-4">
                            <p class="mb-1">Jenis Anggaran :</p>
                            <h5 class="font-size-16">
                                {{ cari_nama(status_anggaran_dashboard(), 'tb_status_anggaran', 'kode', 'nama') }}</h5>
                        </div>

                    </div>
                    {{-- </div> --}}
                </div>
            </div>

        </div>

    </div> <!-- end slimscroll-menu-->
</div>
