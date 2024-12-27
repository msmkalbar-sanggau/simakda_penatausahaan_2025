@extends('template.app')
@section('title', 'Input Transaksi Panjar | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Transaksi Panjar
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No tersimpan --}}
                    <div class="mb-3 row">
                        <label for="no_bku" class="col-md-2 col-form-label">No. BKU</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="no_bku" name="no_bku"
                                placeholder="Tidak perlu diisi atau diedit" required readonly>
                        </div>
                    </div>
                    {{-- No Kas dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No. Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required
                                value="{{ $no_urut }}" readonly>
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_kas" name="tgl_kas" required>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- No Bukti dan Tanggal Bukti --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No. Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required
                                value="{{ $no_urut }}">
                        </div>
                        <label for="tgl_bukti" class="col-md-2 col-form-label">Tanggal Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_bukti" name="tgl_bukti" required>
                        </div>
                    </div>
                    {{-- No Panjar --}}
                    <div class="mb-3 row">
                        <label for="no_panjar" class="col-md-2 col-form-label">No. Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_panjar" name="no_panjar" required readonly>
                        </div>
                    </div>
                    {{-- Kode dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
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
                    {{-- No. Panjar --}}
                    <div class="mb-3 row">
                        <label for="no_panjar" class="col-md-2 col-form-label">No. Panjar</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="no_panjar" name="no_panjar" required readonly
                                readonly>
                        </div>
                    </div>
                    {{-- Pembayaran dan Jenis Beban --}}
                    <div class="mb-3 row">
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="TUNAI">TUNAI</option>
                                <option value="BANK">BANK</option>
                            </select>
                        </div>
                        <label for="beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="beban"
                                name="beban">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">UP/GU</option>
                                <option value="3">TU</option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-2 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('transaksipanjar.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekening --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rekening
                    <button type="button" style="float: right" id="tambah_rincian"
                        class="btn btn-primary btn-md">Tambah Kegiatan</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_panjar" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>No SP2D</th>
                                <th>Kegiatan</th>
                                <th>Nama Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Sumber Dana</th>
                                <th>Sudah Dibayarkan</th> {{-- hidden --}}
                                <th>SP2D Non UP</th> {{-- hidden --}}
                                <th>Anggaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total" name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_rincian" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- No Panjar -->
                    <div class="mb-3 row">
                        <label for="nopanjar" class="col-md-2 col-form-label">No Panjar</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="nopanjar"
                                name="nopanjar">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_panjar as $panjar)
                                    <option value="{{ $panjar->no_panjar_lalu }}" data-nilai="{{ $panjar->nilai }}"
                                        data-kembali="{{ $panjar->kembali }}">
                                        {{ $panjar->no_panjar_lalu }} |
                                        {{ rupiah($panjar->nilai) }} | {{ rupiah($panjar->kembali) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Kode Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kode Kegiatan</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- No SP2D -->
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">No SP2D</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="no_sp2d"
                                name="no_sp2d">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- Kode Rekening -->
                    <div class="mb-3 row">
                        <label for="kode_rekening" class="col-md-2 col-form-label">Kode Rekening</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="kode_rekening"
                                name="kode_rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- Sumber Dana -->
                    <div class="mb-3 row">
                        <label for="sumber" class="col-md-2 col-form-label">Sumber Dana</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="sumber"
                                name="sumber">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- Anggaran / SP2D -->
                    <div class="mb-3 row">
                        <label for="total_sp2d" class="col-md-2 col-form-label">Anggaran / SP2D</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_sp2d" id="total_sp2d"
                                style="text-align: right">
                        </div>
                        <label for="lalu_sp2d" class="col-md-2 col-form-label">Lalu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="lalu_sp2d" id="lalu_sp2d"
                                style="text-align: right">
                        </div>
                        <label for="sisa_sp2d" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_sp2d" id="sisa_sp2d"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Anggaran Sumber Dana / SP2D -->
                    <div class="mb-3 row">
                        <label for="total_sumber" class="col-md-2 col-form-label">Anggaran Sumber Dana / SP2D</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_sumber" id="total_sumber"
                                style="text-align: right">
                        </div>
                        <label for="lalu_sumber" class="col-md-2 col-form-label">Lalu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="lalu_sumber" id="lalu_sumber"
                                style="text-align: right">
                        </div>
                        <label for="sisa_sumber" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_sumber" id="sisa_sumber"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- SPD -->
                    <div class="mb-3 row">
                        <label for="total_spd" class="col-md-2 col-form-label">SPD</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_spd" id="total_spd"
                                style="text-align: right">
                        </div>
                        <label for="lalu_spd" class="col-md-2 col-form-label">Lalu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="lalu_spd" id="lalu_spd"
                                style="text-align: right">
                        </div>
                        <label for="sisa_spd" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_spd" id="sisa_spd"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Angkas -->
                    <div class="mb-3 row">
                        <label for="total_angkas" class="col-md-2 col-form-label">Angkas</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_angkas" id="total_angkas"
                                style="text-align: right">
                        </div>
                        <label for="lalu_angkas" class="col-md-2 col-form-label">Lalu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="lalu_angkas" id="lalu_angkas"
                                style="text-align: right">
                        </div>
                        <label for="sisa_angkas" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_angkas" id="sisa_angkas"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Status Anggaran, Status Anggaran Kas, Total Panjar -->
                    <div class="mb-3 row">
                        <label for="status_anggaran" class="col-md-2 col-form-label">Status Anggaran</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="status_anggaran"
                                id="status_anggaran" value="{{ status_anggaran_new()->nama }}">
                        </div>
                        <label for="status_angkas" class="col-md-2 col-form-label">Status Anggaran Kas</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="status_angkas" id="status_angkas"
                                value="{{ status_angkas_penagihan() }}">
                        </div>
                        <label for="total_panjar" class="col-md-2 col-form-label">Total Panjar</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_panjar" id="total_panjar"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Transaksi Panjar -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="transaksi_panjar" class="col-md-2 col-form-label">Transaksi Panjar</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="transaksi_panjar" id="transaksi_panjar"
                                style="text-align: right" readonly>
                        </div>
                    </div>
                    <!-- Kembali Panjar -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="kembali_panjar" class="col-md-2 col-form-label">Kembali Panjar</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="kembali_panjar" id="kembali_panjar"
                                style="text-align: right" readonly>
                        </div>
                    </div>
                    <!-- Sisa Panjar -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="sisa_panjar" class="col-md-2 col-form-label">Sisa Panjar</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="sisa_panjar" id="sisa_panjar"
                                style="text-align: right" readonly>
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                style="text-align: right" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    {{-- Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_rincian" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="total_rincian" style="text-align: right" class="col-md-9 col-form-label">Total</label>
                    <div class="col-md-3" style="padding-right: 30px">
                        <input type="text" width="100%" class="form-control"
                            style="text-align: right;background-color:white;border:none;" readonly name="total_rincian"
                            id="total_rincian">
                    </div>
                </div>
                <div class="card" style="margin: 4px">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border-primary mb-0" style="width: 100%"
                                id="input_rincian">
                                <thead>
                                    <tr>
                                        <th>No Bukti</th> {{-- hidden --}}
                                        <th>No SP2D</th>
                                        <th>Kegiatan</th>
                                        <th>Nama Kegiatan</th> {{-- hidden --}}
                                        <th>Kode Rekening</th>
                                        <th>Nama Rekening</th>
                                        <th>Rupiah</th>
                                        <th>Sudah Dibayarkan</th>
                                        <th>Sumber</th>
                                        <th>SP2D Non UP</th>
                                        <th>Anggaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.transaksi_panjar.js.create');
@endsection
