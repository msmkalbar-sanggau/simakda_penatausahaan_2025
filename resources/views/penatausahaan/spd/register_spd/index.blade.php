@extends('template.app')
@section('title', 'SPD Register SPD | SIMAKDA')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                CETAK REGISTER SPD
            </div>
            <div class="card-body">
                <!-- Kode SKPD dan Nama SKPD -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="kd_skpd">Kode SKPD</label>
                            <select type="text" class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd"></select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="nm_skpd">Nama SKPD</label>
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" readonly />
                        </div>
                    </div>
                </div>

                <!-- nip dan nama skpd dan beban-->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="nip">NIP SKPD</label>
                            <select type="text" class="form-control select2-multiple" style="width: 100%" id="nip" name="nip"></select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="nama_bend">Nama Kepala SKPD</label>
                            <input type="text" class="form-control" id="nama_bend" name="nama_bend" readonly />
                        </div>
                    </div>
                </div>

                <!-- periode-->
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="tanggal_awal">Periode Awal</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="tanggal_akhir">Periode Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="tanggal_ttd">Tanggal TTD</label>
                            <input type="date" class="form-control" id="tanggal_ttd" name="tanggal_ttd" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <!-- SIMPAN -->
                <hr>
                <div class="mb-4 row">
                    <label for="unit" class="col-md-2 col-form-label">- Unit</label>
                    <div class="col-md-4">
                        <a data-jenis="layar" href="#" class="btn btn-dark btn-md unit">Layar</a> &nbsp;
                        <a data-jenis="pdf" href="#" class="btn btn-danger btn-md unit">PDF</a> &nbsp;
                        <a data-jenis="excel" href="#" class="btn btn-success btn-md unit">Excel</a>
                    </div>
                    <label for="skpd" class="col-md-2 col-form-label">- SKPD</label>
                    <div class="col-md-4">
                        <a data-jenis="layar" href="#" class="btn btn-dark btn-md skpd">Layar</a> &nbsp;
                        <a data-jenis="pdf" href="#" class="btn btn-danger btn-md skpd">PDF</a> &nbsp;
                        <a data-jenis="excel" href="#" class="btn btn-success btn-md skpd">Excel</a>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="keseluruhan" class="col-md-2 col-form-label">- Keseluruhan</label>
                    <div class="col-md-4">
                        <a data-jenis="layar" href="#" class="btn btn-dark btn-md keseluruhan">Layar</a> &nbsp;
                        <a data-jenis="pdf" href="#" class="btn btn-danger btn-md keseluruhan">PDF</a> &nbsp;
                        <a data-jenis="excel" href="#" class="btn btn-success btn-md keseluruhan">Excel</a>
                    </div>
                    <label for="keseluruhan_revisi" class="col-md-2 col-form-label">- Keseluruhan (SPD revisi terakhir)</label>
                    <div class="col-md-4">
                        <a data-jenis="layar" href="#" class="btn btn-dark btn-md keseluruhan_revisi">Layar</a> &nbsp;
                        <a data-jenis="pdf" href="#" class="btn btn-danger btn-md keseluruhan_revisi">PDF</a> &nbsp;
                        <a data-jenis="excel" href="#" class="btn btn-success btn-md keseluruhan_revisi">Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>

@endsection
@section('js')
@include('penatausahaan.spd.register_spd.js.index')
@endsection