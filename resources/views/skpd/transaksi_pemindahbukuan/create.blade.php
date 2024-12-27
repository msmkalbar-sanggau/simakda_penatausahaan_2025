@extends('template.app')
@section('title', 'Tambah Transaksi Pemindahbukuan | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Transaksi Pemindahbukuan Bank
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Nomor Voucher dan Tanggal Transaksi --}}
                    <div class="mb-3 row">
                        <label for="no_voucher" class="col-md-2 col-form-label">No Voucher</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_voucher" name="no_voucher"
                                placeholder="Tidak Perlu diisi atau diedit" required readonly>
                            <input type="text" class="form-control" id="ketcms" name="ketcms" hidden>
                            <input type="text" class="form-control" id="tahun_anggaran" name="tahun_anggaran"
                                value="{{ tahun_anggaran() }}" hidden>
                            <input type="text" class="form-control" id="persen_tunai" value="{{ $persen->persen_tunai }}"
                                hidden>
                            <input type="text" class="form-control" id="persen_kkpd" value="{{ $persen->persen_kkpd }}"
                                hidden>
                        </div>
                        <label for="tgl_voucher" class="col-md-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_voucher" name="tgl_voucher">
                            <input type="text" class="form-control" id="bulan" name="bulan" hidden>
                        </div>
                    </div>
                    {{-- No Bukti dan Jenis Beban --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required
                                value="{{ $no_urut }}" readonly>
                        </div>
                        <label for="beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="beban" name="beban">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">UP/GU</option>
                                <option value='3'>TU</option>
                                <option value='4'>GAJI</option>
                                <option value='6'>Barang & Jasa</option>
                            </select>
                        </div>
                    </div>
                    {{-- Kode SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode OPD/UNIT</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="kd_skpd" name="kd_skpd"
                                value="{{ $skpd->kd_skpd }}" readonly>
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama OPD/UNIT</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd"
                                value="{{ $skpd->nm_skpd }}" readonly>
                        </div>
                    </div>
                    {{-- Pembayaran dan Rekening Bank Bendahara --}}
                    <div class="mb-3 row">
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="BANK">BANK</option>
                            </select>
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening Bank Bend</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening"
                                name="rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($rekening_awal as $rek_awal)
                                    <option value="{{ $rek_awal->rekening }}">{{ $rek_awal->rekening }}</option>
                                @endforeach
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
                            <button id="simpan_transaksi" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.transaksi_pemindahbukuan.index') }}"
                                class="btn btn-warning btn-md">Kembali</a>
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
                    <button id="tambah_rekening" class="btn btn-success btn-md" style="float: right;">Tambah</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rekening_belanja" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>No SP2D</th>
                                <th>Kegiatan</th>
                                <th>Nama Kegiatan</th>
                                <th>Kode Rek</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Sumber</th>
                                <th>Sudah Dibayarkan</th>
                                <th>SP2D Non UP</th>
                                <th>Anggaran</th>
                                <th>Volume</th>
                                <th>Satuan</th>
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
                                <td style="padding-left: 600px">Total Belanja</td>
                                <td>:</td>
                                <td style="text-align: right"><input type="text"
                                        style="border:none;background-color:white;text-align:right" class="form-control"
                                        readonly id="total_belanja">
                                </td>
                                <input type="text" style="text-align: right" readonly class="form-control"
                                    id="total_sp2d" name="total_sp2d" hidden>
                            </tr>
                            <tr>
                                <td style="padding-left: 600px">Total Potongan</td>
                                <td>:</td>
                                <td style="text-align: right"><input type="text"
                                        style="border:none;background-color:white;text-align:right" class="form-control"
                                        readonly id="total_potongan">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Daftar Rekening Tujuan --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Daftar Rekening Tujuan
                    <button type="button" style="float: right" id="tambah_rekening_tujuan"
                        class="btn btn-success btn-md">Tambah</button>
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
                            <input type="text" style="text-align: right" readonly
                                class="form-control @error('total_transfer') is-invalid @enderror" id="total_transfer"
                                name="total_transfer">
                        </div>
                    </div>
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
                    <!-- SUB KEGIATAN -->
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal" style=" width: 100%;" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_kegiatan as $kegiatan)
                                    <option value="{{ $kegiatan->kd_sub_kegiatan }}"
                                        data-nama="{{ $kegiatan->nm_sub_kegiatan }}">{{ $kegiatan->kd_sub_kegiatan }} |
                                        {{ $kegiatan->nm_sub_kegiatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_sub_kegiatan" readonly
                                name="nm_sub_kegiatan">
                        </div>
                    </div>
                    {{-- Nomor SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">Nomor SP2D</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="no_sp2d"
                                name="no_sp2d">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- REKENING -->
                    <div class="mb-3 row">
                        <label for="kd_rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal" style=" width: 100%;" id="kd_rekening"
                                name="kd_rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_rekening" readonly name="nm_rekening">
                        </div>
                    </div>
                    <!-- SUMBER DANA -->
                    <div class="mb-3 row">
                        <label for="sumber" class="col-md-2 col-form-label">Sumber</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal" style=" width: 100%;" id="sumber"
                                name="sumber">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_sumber" readonly name="nm_sumber">
                        </div>
                    </div>
                    <!-- TOTAL SPD -->
                    <div class="mb-3 row">
                        <label for="total_spd" class="col-md-2 col-form-label">Total SPD</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_spd" id="total_spd"
                                style="text-align: right">
                        </div>
                        <label for="realisasi_spd" class="col-md-2 col-form-label">Realisasi</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="realisasi_spd" id="realisasi_spd"
                                style="text-align: right">
                        </div>
                        <label for="sisa_spd" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_spd" id="sisa_spd"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- ANGKAS -->
                    <div class="mb-3 row">
                        <label for="total_angkas" class="col-md-2 col-form-label">Total Anggaran Kas</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_angkas" id="total_angkas"
                                style="text-align: right">
                        </div>
                        <label for="realisasi_angkas" class="col-md-2 col-form-label">Realisasi</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="realisasi_angkas"
                                id="realisasi_angkas" style="text-align: right">
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
                        <label for="realisasi_anggaran" class="col-md-2 col-form-label">Realisasi</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="realisasi_anggaran"
                                id="realisasi_anggaran" style="text-align: right">
                        </div>
                        <label for="sisa_anggaran" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_anggaran" id="sisa_anggaran"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- NILAI SUMBER DANA -->
                    <div class="mb-3 row">
                        <label for="total_sumber" class="col-md-2 col-form-label">Nilai Sumber Dana</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_sumber" id="total_sumber"
                                style="text-align: right">
                        </div>
                        <label for="realisasi_sumber" class="col-md-2 col-form-label">Realisasi</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="realisasi_sumber"
                                id="realisasi_sumber" style="text-align: right">
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
                                id="status_anggaran">
                        </div>
                        <label for="status_angkas" class="col-md-2 col-form-label">Status Anggaran Kas</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="status_angkas" id="status_angkas">
                        </div>
                        <label for="sisa_kas" class="col-md-2 col-form-label">Sisa Kas Bank</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_kas" id="sisa_kas"
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
                            <button id="simpan_rekening" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="total_input_rekening" style="text-align: right"
                        class="col-md-9 col-form-label">Total</label>
                    <div class="col-md-3" style="padding-right: 30px">
                        <input type="text" width="100%" class="form-control" style="text-align: right" readonly
                            name="total_input_rekening" id="total_input_rekening">
                    </div>
                </div>
                <div class="card" style="margin: 4px">
                    <div class="card-header">
                        Input Rekening
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border-primary mb-0" style="width: 100%"
                                id="input_rekening">
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
                                        <th>Volume</th>
                                        <th>Satuan</th>
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

    <div id="modal_rekening_tujuan" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rekening Tujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nilai Potongan -->
                    <div class="mb-3 row">
                        <label for="nilai_potongan" class="col-md-2 col-form-label">Nilai Total Pot.</label>
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
                                        data-nama="{{ $rek_tujuan->nm_rekening }}">{{ $rek_tujuan->rekening }} |
                                        {{ $rek_tujuan->nm_rekening }}</option>
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
                    {{-- Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_rekening_tujuan" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                    {{-- CATATAN --}}
                    <div class="mb-3 row">
                        <label for="" class="col-md-12 col-form-label" style="color: red">*) Lakukan pencarian
                            cepat dengan "Nomor Rekening dan Nama" di kolom Rekening Tujuan</label>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.transaksi_pemindahbukuan.js.create');
@endsection
