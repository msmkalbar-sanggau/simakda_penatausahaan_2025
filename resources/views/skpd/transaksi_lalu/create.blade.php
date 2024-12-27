@extends('template.app')
@section('title', 'Input Transaksi Tahun Lalu | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Transaksi Tahun Lalu
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No tersimpan --}}
                    <div class="mb-3 row">
                        <label for="no_voucher" class="col-md-2 col-form-label">No. Voucher</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_voucher" name="no_voucher"
                                placeholder="Tidak perlu diisi atau diedit" required readonly>
                        </div>
                        <label for="beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="beban" name="beban">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">UP/GU</option>
                                <option value="3">TU</option>
                                <option value="4">GAJI</option>
                                <option value="6">Barang & Jasa</option>
                            </select>
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
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                            <input class="form-control" type="text" id="nomor_sp2d" name="nomor_sp2d" required readonly
                                hidden>
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
                    {{-- Pembayaran dan Rekening Bank Bendahara --}}
                    <div class="mb-3 row">
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="BANK">BANK</option>
                            </select>
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening Bank Bend.</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening"
                                name="rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_rekening as $rekening)
                                    <option value="{{ $rekening->rekening }}">{{ $rekening->rekening }}</option>
                                @endforeach
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

        {{-- Rekening Belanja --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rekening Belanja
                    <button type="button" style="float: right" id="tambah_rincian"
                        class="btn btn-primary btn-md">Tambah</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian" class="table" style="width: 100%">
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
                                <th>Volume</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total
                            Belanja</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total" name="total">
                        </div>
                    </div>
                    <div class="mb-2 mt-2 row">
                        <label for="total_potongan" class="col-md-8 col-form-label" style="text-align: right">Total
                            Potongan</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total_potongan" name="total_potongan">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Rekening Tujuan --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Daftar Rekening Tujuan
                    <button type="button" style="float: right" id="tambah_rek_tujuan"
                        class="btn btn-primary btn-md">Tambah</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rekening_tujuan" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>Tanggal</th>
                                <th>Rekening Awal</th>
                                <th>Nama</th>
                                <th>Rek. Tujuan</th>
                                <th>Bank</th>
                                <th>SKPD</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total_transfer" class="col-md-8 col-form-label" style="text-align: right">Total
                            Transfer</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total_transfer" name="total_transfer">
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
                    <!-- Total SPD -->
                    <div class="mb-3 row">
                        <label for="total_spd" class="col-md-2 col-form-label">Total SPD</label>
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
                    <!-- Total Anggaran Kas -->
                    <div class="mb-3 row">
                        <label for="total_angkas" class="col-md-2 col-form-label">Total Anggaran Kas</label>
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
                    <!-- Anggaran -->
                    <div class="mb-3 row">
                        <label for="total_anggaran" class="col-md-2 col-form-label">Anggaran</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_anggaran"
                                id="total_anggaran" style="text-align: right">
                        </div>
                        <label for="lalu_anggaran" class="col-md-2 col-form-label">Lalu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="lalu_anggaran" id="lalu_anggaran"
                                style="text-align: right">
                        </div>
                        <label for="sisa_anggaran" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_anggaran" id="sisa_anggaran"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Nilai Sumber Dana -->
                    <div class="mb-3 row">
                        <label for="total_sumber" class="col-md-2 col-form-label">Nilai Sumber Dana</label>
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
                    <!-- Status Anggaran, Status Anggaran Kas, Sisa Kas Bank -->
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
                        <label for="sisa_bank" class="col-md-2 col-form-label">Sisa Kas Bank</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_bank" id="sisa_bank"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Potongan LS -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="potongan_ls" class="col-md-2 col-form-label">Potongan LS</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="potongan_ls" id="potongan_ls"
                                style="text-align: right" readonly>
                        </div>
                    </div>
                    <!-- Total Sisa -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="total_sisa" class="col-md-2 col-form-label">Total Sisa</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="total_sisa" id="total_sisa"
                                style="text-align: right" readonly>
                        </div>
                    </div>
                    <!-- Volume Output -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="volume" class="col-md-2 col-form-label">Volume Output</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="volume" id="volume"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Satuan Output -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="satuan" class="col-md-2 col-form-label">Satuan Output</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="satuan" id="satuan"
                                style="text-align: right">
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
                                        <th>Volume</th>
                                        <th>Satuan</th>
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

    <div id="modal_rekening" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rekening Tujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nilai Potongan -->
                    <div class="mb-3 row">
                        <label for="nilai_potongan" class="col-md-2 col-form-label">Nilai Total Potongan</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="nilai_potongan" id="nilai_potongan"
                                style="text-align: right" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                        <div class="col-md-4">
                            <label for="nilai_potongan" class="col-form-label">*Harus diisi jika ada potongan</label>
                        </div>
                    </div>
                    <!-- REKENING TUJUAN -->
                    <div class="mb-3 row">
                        <label for="rek_tujuan" class="col-md-2 col-form-label">Rekening Tujuan</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal1" style=" width: 100%;" id="rek_tujuan"
                                name="rek_tujuan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($data_rek_tujuan as $rek_tujuan)
                                    <option value="{{ $rek_tujuan->rekening }}"
                                        data-nama="{{ $rek_tujuan->nm_rekening }}" data-bank="{{ $rek_tujuan->bank }}">
                                        {{ $rek_tujuan->rekening }} |
                                        {{ $rek_tujuan->nm_rekening }} | {{ $rek_tujuan->bank }} |
                                        {{ $rek_tujuan->keterangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Nama Rekening Tujuan --}}
                    <div class="mb-3 row">
                        <label for="nm_rekening_tujuan" class="col-md-2 col-form-label">A.N. Rekening</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="nm_rekening_tujuan" id="nm_rekening_tujuan"
                                readonly>
                        </div>
                    </div>
                    <!-- Bank -->
                    <div class="mb-3 row">
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal1" style=" width: 100%;" id="bank"
                                name="bank">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($data_bank as $bank)
                                    <option value="{{ $bank->kode }}" data-nama="{{ $bank->nama }}">
                                        {{ $bank->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Nilai Transfer --}}
                    <div class="mb-3 row">
                        <label for="nilai_transfer" class="col-md-2 col-form-label">Nilai Transfer</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="nilai_transfer" id="nilai_transfer"
                                style="text-align: right" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="" class="col-md-12 col-form-label" style="color: red">*) Lakukan pencarian
                            cepat dengan <b>"Nomor Rekening dan Nama"</b> di kolom Rekening Tujuan</label>
                    </div>
                    {{-- Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_rekening_tujuan" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.transaksi_lalu.js.create');
@endsection
