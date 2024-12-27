@extends('template.app')
@section('title', 'Verifikasi SP2D | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            @if (session()->has('message'))
                <div class="alert {{ session('alert') ?? 'alert-info' }}">
                    {{ session('message') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    Verifikasi SP2D
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- TAB --}}
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills" role="tablist">
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link active" data-bs-toggle="tab" href="#" id="bverif"
                                    role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">Belum Verifikasi</span>
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" data-bs-toggle="tab" href="#" role="tab" id="sverif">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Sudah Verifikasi</span>
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" data-bs-toggle="tab" href="#" role="tab" id="salur">
                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                    <span class="d-none d-sm-block">Sudah Tersalurkan</span>
                                </a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="navpills-profile" role="tabpanel">
                                <table id="sp2dverif" class="table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 25px;text-align:center">No.</th>
                                            <th style="width: 100px;text-align:center">Nomor SP2D</th>
                                            <th style="width: 100px;text-align:center">Tanggal</th>
                                            <th style="width: 100px;text-align:center">Keterangan</th>
                                            <th style="width: 100px;text-align:center">Nilai</th>
                                            <th style="width: 100px;text-align:center">User Verif</th>
                                            <th style="width: 200px;text-align:center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- TAB --}}
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

@endsection
@section('js')
    @include('bud.verif_sp2d.js.index')
@endsection
