@extends('template.app')
@section('title', 'Edit Pengeluaran Lain-Lain | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Pengeluaran Lain-Lain
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Nomor dan Tanggal --}}
                    <div class="mb-3 row">
                        <label for="nomor" class="col-md-2 col-form-label">Nomor</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nomor" name="nomor" required readonly
                                value="{{ $lain->NO_BUKTI }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly value="{{ tahun_anggaran() }}" hidden>
                        </div>
                        <label for="tanggal" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tanggal" name="tanggal"
                                value="{{ $lain->TGL_BUKTI }}">
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
                                <option value="1" {{ $lain->jns_beban == 1 ? 'selected' : '' }}> UP</option>
                                <option value="4" {{ $lain->jns_beban == 4 ? 'selected' : '' }}> LS Gaji</option>
                                <option value="6" {{ $lain->jns_beban == 6 ? 'selected' : '' }}> LS Barang Jasa
                                </option>
                                <option value="5" {{ $lain->jns_beban == 5 ? 'selected' : '' }}> LS Pihak Ketiga
                                    Lainnya
                                </option>
                                <option value="7" {{ $lain->jns_beban == 7 ? 'selected' : '' }}> Pajak</option>
                            </select>
                        </div>
                        <label for="pembayaran" class="col-md-2 col-form-label">Jenis Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="TUNAI" {{ $lain->pay == 'TUNAI' ? 'selected' : '' }}> TUNAI</option>
                                <option value="BANK" {{ $lain->pay == 'BANK' ? 'selected' : '' }}> BANK</option>
                            </select>
                        </div>
                    </div>
                    {{-- Rekening Pajak dan Nama Rekening --}}
                    <div class="mb-3 row" id="rek_pajak">
                        <label for="kd_rek6" class="col-md-2 col-form-label">KD. Rek Pajak</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_rek6" name="kd_rek6">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($rekening as $rek)
                                    <option value="{{ $rek->kd_rek6 }}" data-nama="{{ $rek->nm_rek6 }}"
                                        {{ $lain->kd_rek6 == $rek->kd_rek6 ? 'selected' : '' }}>
                                        {{ $rek->kd_rek6 }}
                                        | {{ $rek->nm_rek6 }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="nm_rek6" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_rek6" name="nm_rek6" required readonly>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan">{{ $lain->KET }}</textarea>
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right"
                                value="{{ $lain->nilai }}">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <input type="checkbox" id="tahun_lalu" {{ $lain->thnlalu == 1 ? 'checked' : '' }}>
                            <label for="tahun_lalu">Tahun Lalu</label>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_pengeluaran" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.pengeluaran_lain.index') }}"
                                class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.pengeluaran_lain.js.edit');
@endsection
