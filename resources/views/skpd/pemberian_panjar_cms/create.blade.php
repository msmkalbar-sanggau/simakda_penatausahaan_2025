@extends('template.app')
@section('title', 'Input Pemberian Panjar CMS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Pemberian Panjar CMS
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No tersimpan --}}
                    <div class="mb-3 row">
                        <label for="no_simpan" class="col-md-2 col-form-label">No. Tersimpan</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="no_simpan" name="no_simpan"
                                placeholder="Tidak perlu diisi atau diedit" required readonly>
                            <input class="form-control" type="text" id="tunai1" name="tunai1" required readonly
                                value="{{ $sisa_tunai }}" hidden>
                            <input class="form-control" type="text" id="bank1" name="bank1" required readonly
                                value="{{ $sisa_bank }}" hidden>
                        </div>
                    </div>
                    {{-- No Panjar dan Tanggal Panjar --}}
                    <div class="mb-3 row">
                        <label for="no_panjar" class="col-md-2 col-form-label">No. Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_panjar" name="no_panjar" required
                                value="{{ $no_urut }}">
                        </div>
                        <label for="tgl_panjar" class="col-md-2 col-form-label">Tanggal Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_panjar" name="tgl_panjar" required>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
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
                    {{-- Pembayaran dan Rek. Bank Bendahara --}}
                    <div class="mb-3 row">
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="BANK" selected>BANK</option>
                                </option>
                            </select>
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rek.Bank Bendahara</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening"
                                name="rekening">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($daftar_rekening_bank as $bank)
                                    <option value="{{ $bank->rekening }}">{{ $bank->rekening }}</option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kegiatan</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected></option>
                                @foreach ($daftar_kegiatan as $kegiatan)
                                    <option value="{{ $kegiatan->kd_sub_kegiatan }}"
                                        data-anggaran="{{ $kegiatan->anggaran }}"
                                        data-transaksi="{{ $kegiatan->transaksi }}">
                                        {{ $kegiatan->kd_sub_kegiatan }} | {{ $kegiatan->nm_sub_kegiatan }} |
                                        {{ rupiah($kegiatan->transaksi) }} | {{ rupiah($kegiatan->anggaran) }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Sisa Anggaran --}}
                    <div class="mb-3 row">
                        <label for="sisa_anggaran" class="col-md-2 col-form-label">Sisa Anggaran</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="sisa_anggaran" name="sisa_anggaran" required
                                readonly style="text-align: right">
                        </div>
                    </div>
                    {{-- Sisa Bank --}}
                    <div class="mb-3 row">
                        <label for="sisa_bank" class="col-md-2 col-form-label">Sisa Bank</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="sisa_bank" name="sisa_bank" required readonly
                                style="text-align: right">
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="nilai" name="nilai" required
                                data-type="currency" style="text-align: right">
                        </div>
                    </div>
                    {{-- Pajak --}}
                    <div class="mb-3 row">
                        <label for="pajak" class="col-md-2 col-form-label">Pajak</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="pajak" name="pajak" required readonly
                                style="text-align: right">
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
                            <a href="{{ route('pemberian_panjarcms.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                            <input type="text" style="border:none;background-color:white;text-align:right" readonly
                                class="form-control" id="total_transfer" name="total_transfer">
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
                    {{-- <div class="mb-3 row">
                        <label for="catatan" class="col-md-12 col-form-label" style="color: red">PERHATIAN!!!</label>
                        <label for="" class="col-md-12 col-form-label" style="color: red">1. Jika rekening tujuan
                            tidak ada silahkan
                            Anda input terlebih dahulu di menu
                            MASTER > REKENING BANK</label>
                        <label for="catatan" class="col-md-12 col-form-label" style="color: red">2. Jangan input
                            rekening tujuan secara
                            manual, karena akan terkendala saat unduh csv di menu Upload Transaksi (CMS)</label>
                    </div> --}}
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
    @include('skpd.pemberian_panjar_cms.js.create');
@endsection
