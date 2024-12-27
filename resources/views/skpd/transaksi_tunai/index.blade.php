@extends('template.app')
@section('title', 'Transaksi Tunai | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Transaksi
                    <a href="{{ route('skpd.transaksi_tunai.create') }}" class="btn btn-primary"
                        style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="transaksi_tunai" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Nomor Bukti</th>
                                        <th style="width: 50px;text-align:center">Tanggal Bukti</th>
                                        <th>SKPD</th>
                                        <th>Nama SKPD</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
                                        <th style="width: 50px;text-align:center">LPJ</th>
                                        <th style="width: 50px;text-align:center">SPJ</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_lihat" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Review Data Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- SKPD --}}
                    <div class="mb-1 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-10">
                            <input type="text" readonly style="border:none;background-color:white" class="form-control"
                                id="nm_skpd" name="nm_skpd">
                            <input type="text" class="form-control" id="kd_skpd" name="kd_skpd" hidden>
                        </div>
                    </div>
                    {{-- Nomor, Tanggal, SP2D --}}
                    <div class="mb-1 row">
                        <label for="nomor" class="col-md-2 col-form-label">Nomor</label>
                        <div class="col-md-2">
                            <input type="text" readonly style="border:none;background-color:white" class="form-control"
                                id="nomor" name="nomor">
                        </div>
                        <label for="tanggal" class="col-md-1 col-form-label">Tanggal</label>
                        <div class="col-md-3">
                            <input type="text" readonly style="border:none;background-color:white" class="form-control"
                                id="tanggal" name="tanggal">
                        </div>
                        <label for="no_sp2d" class="col-md-1 col-form-label">SP2D</label>
                        <div class="col-md-3">
                            <input type="text" readonly style="border:none;background-color:white" class="form-control"
                                id="no_sp2d" name="no_sp2d">
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-1 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea name="keterangan" id="keterangan" cols="30"class="form-control"
                                style="border:none;background-color:white;text-align:justify"></textarea>
                        </div>
                    </div>
                    <hr style="border: 1px solid black">
                    {{-- Kegiatan --}}
                    <div class="row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kegiatan</label>
                        <div class="col-md-10">
                            <input type="text" readonly style="border:none;background-color:white" class="form-control"
                                id="kd_sub_kegiatan" name="kd_sub_kegiatan">
                        </div>
                    </div>
                    {{-- Nama Kegiatan --}}
                    <div class="mb-1 row">
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label"></label>
                        <div class="col-md-10">
                            <input type="text" readonly style="border:none;background-color:white" class="form-control"
                                id="nm_sub_kegiatan" name="nm_sub_kegiatan">
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            Rekening Transaksi
                        </div>
                        <div class="card-body">
                            <table style="width: 100%" id="rekening_transaksi">
                                <thead>
                                    <tr>
                                        <th>Kode Rek</th>
                                        <th>Nama Rekening</th>
                                        <th>Nilai</th>
                                        <th>Sumber</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            Rekening Potongan Transaksi
                        </div>
                        <div class="card-body">
                            <table style="width: 100%" id="rekening_potongan">
                                <thead>
                                    <tr>
                                        <th>Kode Rek</th>
                                        <th>Nama Rekening</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            Rekening Tujuan Transfer
                        </div>
                        <div class="card-body">
                            <table style="width: 100%" id="rekening_tujuan">
                                <thead>
                                    <tr>
                                        <th>No Bukti</th>
                                        <th>Tanggal Bukti</th>
                                        <th>Rek. Bendahara</th>
                                        <th>Atas Nama Rek. Tujuan</th>
                                        <th>Rek. Tujuan</th>
                                        <th>Kd Skpd</th>
                                        <th>Nilai</th>
                                        <th>Bank</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i>Kembali</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_transaksi" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lis Data Upload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <input type="hidden" name="no_upload" id="no_upload">
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            Data Transaksi
                        </div>
                        <div class="card-body">
                            <table style="width: 100%" id="data_transaksi1" class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Voucher</th>
                                        <th>Tanggal Voucher</th>
                                        <th>KD SKPD</th>
                                        <th>Keterangan</th>
                                        <th>Total</th>
                                        <th>Netto</th>
                                        <th>Potongan</th>
                                        <th>Nilai Pengeluaran</th>
                                        <th>Status Upload</th>
                                        <th>Rek Bend</th>
                                        <th>Nama Rek</th>
                                        <th>Rek Tujuan</th>
                                        <th>Bank Tujuan</th>
                                        <th>Ket. Tujuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <hr>
                            <table style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td><button type="button" class="btn btn-sm btn-primary" id="cetakCsvKalbar"><i
                                                    class="uil-print"></i>[Unduh CSV] Bank
                                                Kalbar</button></td>
                                        <td><button type="button" class="btn btn-sm btn-primary"
                                                id="cetakCsvLuarKalbar"><i class="uil-print"></i>[Unduh CSV] Di Luar Bank
                                                Kalbar</button></td>
                                        <td style="padding-left: 200px">Total Transaksi</td>
                                        <td>:</td>
                                        <td><input type="text"
                                                style="border:none;background-color:white;text-align:right" readonly
                                                id="total_transaksi_satuan" class="form-control">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i>Kembali</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.transaksi_tunai.js.index')
@endsection
