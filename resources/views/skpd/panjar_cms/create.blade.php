@extends('template.app')
@section('title', 'Input Panjar CMS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Panjar CMS
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Nomor dan Tanggal --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No. Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required readonly>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly value="{{ tahun_anggaran() }}" hidden>
                        </div>
                        <label for="tgl_voucher" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_voucher" name="tgl_voucher">
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
                    {{-- Kegiatan dan Nama Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kegiatan</label>
                        <div class="col-md-4">
                        <select class="form-control select2-multiple" style="width: 100%" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-md-4">
                       <input class="form-control" type="text" id="nm_sub_kegiatan" name="nm_sub_kegiatan" required readonly>
                        </div>
                    </div>
                    {{-- Jenis Beban dan Jenis Pembayaran --}}
                    <div class="mb-3 row">
                    <label for="pembayaran" class="col-md-2 col-form-label">Jenis Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="BANK"> BANK</option>
                            </select>
                        </div>
                        <label for="beban" class="col-md-2 col-form-label">Rek. Bank Bendahara</label>
                        <div class="col-md-4">
                        <select class="form-control select2-multiple @error('drek') is-invalid @enderror"
                                style="width: 100%;" id="drek" name="drek" data-placeholder="Silahkan Pilih">
                                <optgroup label="Rekening Bendahara">
                                    <option value="" disabled selected>Silahkan Pilih Rekening</option>
                                    @foreach ($daftar_rekening as $drek)
                                        <option value="{{ $drek->rekening }}"
                                            {{ old('drek') == $drek->rekening ? 'selected' : '' }}>
                                            {{ $drek->rekening }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>
                   
                    {{-- Sisa Anggaran dan Sisa Bank --}}
                    <div class="mb-3 row">
                        <label for="sisa_ang" class="col-md-2 col-form-label">Sisa Anggaran</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="sisa_ang" id="sisa_ang" style="text-align: right" readonly  >
                        </div>
                        <label for="sisabank" class="col-md-2 col-form-label">Sisa Bank</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="sisabank" id="sisabank" style="text-align: right" readonly>
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" onkeyup="hitung()" style="text-align: right">
                        </div>
                    </div>
                    {{-- Pajak --}}
                    <div class="mb-3 row">
                        <label for="nil_pot2" class="col-md-2 col-form-label">Pajak</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nil_pot2" id="nil_pot2" style="text-align: right" readonly>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_panjar" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('panjar_cms.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                        class="btn btn-primary btn-sm">Tambah</button>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rekening Tujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nilai Potongan -->
                    <div class="mb-3 row">
                        <label for="nilpotongan" class="col-md-2 col-form-label">Nilai Total Potongan</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('nilpotongan') is-invalid @enderror"
                                name="nilpotongan" id="nilpotongan" style="text-align: right"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                            @error('nilpotongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nilpotongan" class="col-form-label">*Harus diisi jika ada potongan</label>
                        </div>
                    </div>
                    <!-- REKENING TUJUAN -->
                    <div class="mb-3 row">
                        <label for="rek_tujuan" class="col-md-2 col-form-label">Rekening Tujuan</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal1 @error('rek_tujuan') is-invalid @enderror"
                                style=" width: 100%;" id="rek_tujuan" name="rek_tujuan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($data_rek_tujuan as $rek_tujuan)
                                    <option value="{{ $rek_tujuan->rekening }}"
                                        data-nama="{{ $rek_tujuan->nm_rekening }}">{{ $rek_tujuan->rekening }} |
                                        {{ $rek_tujuan->nm_rekening }}</option>
                                @endforeach
                            </select>
                            @error('rek_tujuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nama Rekening Tujuan --}}
                    <div class="mb-3 row">
                        <label for="nm_rekening_tujuan" class="col-md-2 col-form-label">A.N. Rekening</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('nm_rekening_tujuan') is-invalid @enderror"
                                name="nm_rekening_tujuan" id="nm_rekening_tujuan" readonly>
                            @error('nm_rekening_tujuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Bank -->
                    <div class="mb-3 row">
                        <label for="nm_bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal1 @error('nm_bank') is-invalid @enderror"
                                style=" width: 100%;" id="nm_bank" name="nm_bank">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($data_bank as $nm_bank)
                                    <option value="{{ $nm_bank->nama }}" data-nama="{{ $nm_bank->nama }}">
                                        {{ $nm_bank->nama }}</option>
                                @endforeach
                            </select>
                            @error('nm_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nilai Transfer --}}
                    <div class="mb-3 row">
                        <label for="nilai_transfer" class="col-md-2 col-form-label">Nilai Transfer</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('nilai_transfer') is-invalid @enderror"
                                name="nilai_transfer" id="nilai_transfer" style="text-align: right"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                            @error('nilai_transfer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="catatan" class="col-md-12 col-form-label" style="color: red">PERHATIAN!!!</label>
                        <label for="" class="col-md-12 col-form-label" style="color: red">1. Jika rekening tujuan
                            tidak ada, silahkan
                            Anda input terlebih dahulu di menu
                            MASTER > REKENING BANK</label>
                        <label for="catatan" class="col-md-12 col-form-label" style="color: red">2. Jangan input
                            rekening tujuan secara
                            manual, karena akan terkendala saat unduh csv di menu Upload Transaksi (CMS)</label>
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
    @include('skpd.panjar_cms.js.create');
@endsection
