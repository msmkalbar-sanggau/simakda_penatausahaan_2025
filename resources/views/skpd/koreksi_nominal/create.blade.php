@extends('template.app')
@section('title', 'Input Koreksi Nominal | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Koreksi Transaksi
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No. BKU dan Tanggal --}}
                    <div class="mb-3 row">
                        <label for="no_bku" class="col-md-2 col-form-label">No. BKU</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="no_bku" name="no_bku" required readonly>
                            <small>Tidak Perlu Diisi Atau Diedit</small>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly value="{{ tahun_anggaran() }}" hidden>
                        </div>
                    </div>
                    {{-- No Bukti dan Tanggal Transaksi --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No. Bukti</label>
                        <div class="col-md-2">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required readonly>
                        </div>
                        <label for="tgl_transaksi" class="col-md-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="date" id="tgl_transaksi" name="tgl_transaksi" required>
                        </div>
                        <label for="tgl_koreksi" class="col-md-2 col-form-label">Tanggal Koreksi</label>
                        <div class="col-md-2">
                            <input class="form-control" type="date" id="tgl_koreksi" name="tgl_koreksi" required>
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $skpd->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $skpd->nm_skpd }}">
                        </div>
                    </div>
                    {{-- Jenis Beban dan Jenis Pembayaran --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="beban" name="beban">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">UP/GU</option>
                                {{-- <option value="4">LS Gaji</option>
                                <option value="6">LS Barang Jasa</option>
                                <option value="5">LS Pihak Ketiga Lainnya</option> --}}
                            </select>
                        </div>
                        <label for="pembayaran" class="col-md-2 col-form-label">Jenis Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="TUNAI"> TUNAI</option>
                                <option value="BANK"> BANK</option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_koreksi" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('koreksi_nominal.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rincian --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rincian
                    <button id="tambah_rekening" class="btn btn-success btn-md" style="float: right;">Tambah</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>No SP2D</th>
                                <th>Sub Kegiatan</th>
                                <th>Nama Sub Kegiatan</th>
                                <th>Kode Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Sumber</th>
                                <th>Sudah Dibayarkan</th>
                                <th>SP2D Non UP</th>
                                <th>Anggaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <hr>
                    <table style="width: 100%">
                        <tbody>
                            <tr>
                                <td style="padding-left: 600px">Total</td>
                                <td>:</td>
                                <td style="text-align: right"><input type="text"
                                        style="border:none;background-color:white;text-align:right" class="form-control"
                                        readonly id="total">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_rekening" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        {{-- <div class="col-md-6"> --}}
                        <div class="card">
                            <div class="card-header" style="background-color:red;color:white">
                                Pilih Transaksi Awal
                            </div>
                            <div class="card-body">
                                <!-- SUB KEGIATAN -->
                                <div class="mb-3 row">
                                    <label for="kd_sub_kegiatan_awal" class="col-md-2 col-form-label">Sub
                                        Kegiatan</label>
                                    <div class="col-md-6">
                                        <select class="form-control select2-modal" style=" width: 100%;"
                                            id="kd_sub_kegiatan_awal" name="kd_sub_kegiatan_awal">
                                            <option value="" disabled selected>Silahkan Pilih</option>
                                            @foreach ($daftar_kegiatan as $kegiatan)
                                                <option value="{{ $kegiatan->kd_sub_kegiatan }}"
                                                    data-nama="{{ $kegiatan->nm_sub_kegiatan }}">
                                                    {{ $kegiatan->kd_sub_kegiatan }} |
                                                    {{ $kegiatan->nm_sub_kegiatan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="nm_sub_kegiatan_awal" readonly
                                            name="nm_sub_kegiatan_awal">
                                    </div>
                                </div>
                                {{-- Nomor SP2D --}}
                                <div class="mb-3 row">
                                    <label for="no_sp2d_awal" class="col-md-2 col-form-label">Nomor SP2D</label>
                                    <div class="col-md-10">
                                        <select class="form-control select2-modal" style=" width: 100%;"
                                            id="no_sp2d_awal" name="no_sp2d_awal">
                                            <option value="" disabled selected>Silahkan Pilih</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- REKENING -->
                                <div class="mb-3 row">
                                    <label for="kd_rekening_awal" class="col-md-2 col-form-label">Rekening
                                        Belanja</label>
                                    <div class="col-md-6">
                                        <select class="form-control select2-modal" style=" width: 100%;"
                                            id="kd_rekening_awal" name="kd_rekening_awal">
                                            <option value="" disabled selected>Silahkan Pilih</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="nm_rekening_awal" readonly
                                            name="nm_rekening_awal">
                                    </div>
                                </div>
                                <!-- SUMBER DANA -->
                                <div class="mb-3 row">
                                    <label for="sumber_awal" class="col-md-2 col-form-label">Sumber Dana</label>
                                    <div class="col-md-6">
                                        <select class="form-control select2-modal" style=" width: 100%;" id="sumber_awal"
                                            name="sumber_awal">
                                            <option value="" disabled selected>Silahkan Pilih</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="nm_sumber_awal" readonly
                                            name="nm_sumber_awal">
                                        <input type="text" class="form-control" id="kd_sumber_awal" readonly
                                            name="kd_sumber_awal" hidden>
                                    </div>
                                </div>
                                {{-- Nilai --}}
                                <div class="mb-3 row">
                                    <label for="nilai_sumber_awal" class="col-md-2 col-form-label">Nilai</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="nilai_sumber_awal" readonly
                                            name="nilai_sumber_awal" style="text-align: right">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="col-md-12 text-center">
                                    <button id="tambah_transaksi_awal" class="btn btn-md btn-success">Tambah</button>
                                </div>
                            </div>
                        </div>
                        {{-- </div> --}}
                        {{-- <div class="col-md-6"> --}}
                        <div class="card">
                            <div class="card-header" style="background-color:green;color:white">
                                Pilih Transaksi Koreksi
                            </div>
                            <div class="card-body">
                                <!-- SUB KEGIATAN -->
                                <div class="mb-3 row">
                                    <label for="kd_sub_kegiatan_koreksi" class="col-md-2 col-form-label">Sub
                                        Kegiatan</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="kd_sub_kegiatan_koreksi" readonly
                                            name="kd_sub_kegiatan_koreksi">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="nm_sub_kegiatan_koreksi" readonly
                                            name="nm_sub_kegiatan_koreksi">
                                    </div>
                                </div>
                                {{-- Nomor SP2D --}}
                                <div class="mb-3 row">
                                    <label for="no_sp2d_koreksi" class="col-md-2 col-form-label">Nomor SP2D</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="no_sp2d_koreksi" readonly
                                            name="no_sp2d_koreksi">
                                    </div>
                                </div>
                                <!-- REKENING -->
                                <div class="mb-3 row">
                                    <label for="kd_rekening_koreksi" class="col-md-2 col-form-label">Rekening
                                        Belanja</label>
                                    <div class="col-md-6">
                                        {{-- <input type="text" class="form-control" id="kd_rekening_koreksi" readonly
                                            name="kd_rekening_koreksi"> --}}
                                        <select class="form-control select2-modal" style=" width: 100%;"
                                            id="kd_rekening_koreksi" name="kd_rekening_koreksi">
                                            <option value="" disabled selected>Silahkan Pilih</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="nm_rekening_koreksi" readonly
                                            name="nm_rekening_koreksi">
                                    </div>
                                </div>
                                <!-- SUMBER DANA -->
                                <div class="mb-3 row">
                                    <label for="sumber_koreksi" class="col-md-2 col-form-label">Sumber Dana</label>
                                    <div class="col-md-6">
                                        {{-- <input type="text" class="form-control" id="sumber_koreksi" readonly
                                            name="sumber_koreksi"> --}}
                                        <select class="form-control select2-modal" style=" width: 100%;"
                                            id="sumber_koreksi" name="sumber_koreksi">
                                            <option value="" disabled selected>Silahkan Pilih</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="nm_sumber_koreksi" readonly
                                            name="nm_sumber_koreksi">
                                        <input type="text" class="form-control" id="kd_sumber_koreksi" readonly
                                            name="kd_sumber_koreksi" hidden>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- </div> --}}
                    </div>
                    <div class="mb-3 row">
                        <div class="card">
                            <div class="card-body">
                                <!-- TOTAL SPD -->
                                <div class="mb-3 row">
                                    <label for="total_spd" class="col-md-2 col-form-label">Total SPD</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="total_spd"
                                            id="total_spd" style="text-align: right">
                                    </div>
                                    <label for="realisasi_spd" class="col-md-2 col-form-label">Realisasi</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="realisasi_spd"
                                            id="realisasi_spd" style="text-align: right">
                                    </div>
                                    <label for="sisa_spd" class="col-md-1 col-form-label">Sisa</label>
                                    <div class="col-md-3">
                                        <input type="text" readonly class="form-control" name="sisa_spd"
                                            id="sisa_spd" style="text-align: right">
                                    </div>
                                </div>
                                <!-- ANGKAS -->
                                <div class="mb-3 row">
                                    <label for="total_angkas" class="col-md-2 col-form-label">Total Anggaran Kas</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="total_angkas"
                                            id="total_angkas" style="text-align: right">
                                    </div>
                                    <label for="realisasi_angkas" class="col-md-2 col-form-label">Realisasi</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="realisasi_angkas"
                                            id="realisasi_angkas" style="text-align: right">
                                    </div>
                                    <label for="sisa_angkas" class="col-md-1 col-form-label">Sisa</label>
                                    <div class="col-md-3">
                                        <input type="text" readonly class="form-control" name="sisa_angkas"
                                            id="sisa_angkas" style="text-align: right">
                                    </div>
                                </div>
                                <!-- Anggaran -->
                                <div class="mb-3 row">
                                    <label for="total_anggaran" class="col-md-2 col-form-label">Anggaran</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="total_anggaran"
                                            id="total_anggaran" style="text-align: right">
                                    </div>
                                    <label for="realisasi_anggaran" class="col-md-2 col-form-label">Realisasi</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="realisasi_anggaran"
                                            id="realisasi_anggaran" style="text-align: right">
                                    </div>
                                    <label for="sisa_anggaran" class="col-md-1 col-form-label">Sisa</label>
                                    <div class="col-md-3">
                                        <input type="text" readonly class="form-control" name="sisa_anggaran"
                                            id="sisa_anggaran" style="text-align: right">
                                    </div>
                                </div>
                                <!-- NILAI SUMBER DANA -->
                                <div class="mb-3 row">
                                    <label for="total_sumber" class="col-md-2 col-form-label">Nilai Sumber Dana</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="total_sumber"
                                            id="total_sumber" style="text-align: right">
                                    </div>
                                    <label for="realisasi_sumber" class="col-md-2 col-form-label">Realisasi</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="realisasi_sumber"
                                            id="realisasi_sumber" style="text-align: right">
                                    </div>
                                    <label for="sisa_sumber" class="col-md-1 col-form-label">Sisa</label>
                                    <div class="col-md-3">
                                        <input type="text" readonly class="form-control" name="sisa_sumber"
                                            id="sisa_sumber" style="text-align: right">
                                    </div>
                                </div>
                                <!-- Status Anggaran, Status Anggaran Kas, Sisa Kas Bank -->
                                <div class="mb-3 row">
                                    <label for="status_anggaran" class="col-md-2 col-form-label">Status Anggaran</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="status_anggaran"
                                            id="status_anggaran">
                                    </div>
                                    <label for="status_angkas" class="col-md-2 col-form-label">Status Anggaran Kas</label>
                                    <div class="col-md-2">
                                        <input type="text" readonly class="form-control" name="status_angkas"
                                            id="status_angkas">
                                    </div>
                                    <label for="nilai" class="col-md-1 col-form-label">Nilai</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="nilai" id="nilai"
                                            style="text-align: right" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                                            data-type="currency">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_transaksi_koreksi" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="total_input_rekening" style="text-align: right" class="col-md-6 col-form-label">Total
                        :</label>
                    <div class="col-md-3" style="padding-right: 30px">
                        <input type="text" width="100%" class="form-control"
                            style="border:none;background-color:white;text-align:right" readonly
                            name="total_input_rekening" id="total_input_rekening">
                    </div>
                    <div class="col-md-3" style="padding-right: 30px">
                        <input type="text" width="100%" class="form-control"
                            style="border:none;background-color:white;text-align:right" readonly name="nilai_atas"
                            id="nilai_atas">
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="card">
                            <div class="card-header">
                                Input Rekening
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered border-primary mb-0" style="width: 100%"
                                        id="rincian_inputan">
                                        <thead>
                                            <tr>
                                                <th>No Bukti</th> {{-- hidden --}}
                                                <th>No SP2D</th>
                                                <th>Kegiatan</th>
                                                <th>Nama Kegiatan</th> {{-- hidden --}}
                                                <th>Kode Rekening</th>
                                                <th>Nama Rekening</th>
                                                <th>Rupiah</th>
                                                <th>Sumber</th>
                                                <th>Sudah Dibayarkan</th>
                                                <th>SP2D Non UP</th>
                                                <th>Anggaran</th>
                                                <th>Aksi</th>
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
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.koreksi_nominal.js.create');
@endsection
