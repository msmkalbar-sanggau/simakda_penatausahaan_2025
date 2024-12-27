@extends('template.app')
@section('title', 'Ubah Pembayaran Panjar | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Pembayaran Panjar
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No tersimpan --}}
                    <div class="mb-3 row">
                        <label for="no_simpan" class="col-md-2 col-form-label">No. Tersimpan</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="no_simpan" name="no_simpan"
                                placeholder="Tidak perlu diisi atau diedit" required readonly value="{{ $panjar->no_kas }}">
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
                                value="{{ $panjar->no_kas }}" readonly>
                        </div>
                        <label for="tgl_panjar" class="col-md-2 col-form-label">Tanggal Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_panjar" name="tgl_panjar" required
                                value="{{ $panjar->tgl_kas }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- Kode dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $panjar->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ nama_skpd($panjar->kd_skpd) }}">
                        </div>
                    </div>
                    {{-- Pembayaran --}}
                    <div class="mb-3 row">
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="pembayaran" name="pembayaran" required readonly
                                value="{{ $panjar->pay }}">
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
                                        data-transaksi="{{ $kegiatan->transaksi }}"
                                        {{ $kegiatan->kd_sub_kegiatan == $panjar->kd_sub_kegiatan ? 'selected' : '' }}>
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
                    {{-- Sisa Kas Tunai --}}
                    <div class="mb-3 row">
                        <label for="sisa_tunai" class="col-md-2 col-form-label">Sisa Kas Tunai</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="sisa_tunai" name="sisa_tunai" required readonly
                                style="text-align: right"
                                value="{{ $panjar->pay == 'TUNAI' ? rupiah($sisa_tunai) : rupiah(0) }}">
                        </div>
                    </div>
                    {{-- Sisa Bank --}}
                    <div class="mb-3 row">
                        <label for="sisa_bank" class="col-md-2 col-form-label">Sisa Bank</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="sisa_bank" name="sisa_bank" required readonly
                                style="text-align: right"
                                value="{{ $panjar->pay == 'BANK' ? rupiah($sisa_bank) : rupiah(0) }}">
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="nilai" name="nilai" required
                                data-type="currency" style="text-align: right" value="{{ $panjar->nilai }}">
                            <input class="form-control" type="text" id="nilai_simpan" name="nilai_simpan" required
                                style="text-align: right" value="{{ rupiah($panjar->nilai) }}" hidden readonly>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-2 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan">{{ $panjar->keterangan }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('bayarpanjar.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.pembayaran_panjar.js.edit');
@endsection
