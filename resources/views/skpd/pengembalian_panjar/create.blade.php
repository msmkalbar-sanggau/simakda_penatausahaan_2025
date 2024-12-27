@extends('template.app')
@section('title', 'Input Pengembalian Panjar | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Pengembalian Panjar
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No tersimpan --}}
                    <div class="mb-3 row">
                        <label for="no_simpan" class="col-md-2 col-form-label">No. Tersimpan</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="no_simpan" name="no_simpan"
                                placeholder="Tidak perlu diisi atau diedit" required readonly>
                        </div>
                    </div>
                    {{-- No Panjar dan Tanggal Panjar --}}
                    <div class="mb-3 row">
                        <label for="no_panjar" class="col-md-2 col-form-label">No. Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_panjar" name="no_panjar" required
                                value="{{ $no_urut }}" readonly>
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
                    {{-- No. Panjar Lalu --}}
                    <div class="mb-3 row">
                        <label for="no_panjar_lalu" class="col-md-2 col-form-label">No. Panjar</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="no_panjar_lalu"
                                name="no_panjar_lalu">
                                <option value="" disabled selected></option>
                                @foreach ($daftar_panjar as $panjar)
                                    <option value="{{ $panjar->no_panjar }}" data-tgl="{{ $panjar->tgl_panjar }}"
                                        data-no_panjar_lalu="{{ $panjar->no_panjar_lalu }}">
                                        {{ $panjar->no_panjar }} | {{ $panjar->tgl_panjar }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                        <label for="tgl_panjar_lalu" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_panjar_lalu" name="tgl_panjar_lalu" required
                                readonly readonly>
                            <input class="form-control" type="text" id="no_panjar_lalu1" name="no_panjar_lalu1" required
                                readonly readonly hidden>
                        </div>
                    </div>
                    {{-- Panjar Awal --}}
                    <div class="mb-3 row">
                        <label for="panjar_awal" class="col-md-2 col-form-label">Panjar Awal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="panjar_awal" name="panjar_awal" required
                                readonly>
                        </div>
                        <label for="nilai_panjar_awal" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nilai_panjar_awal" name="nilai_panjar_awal"
                                required readonly style="text-align: right">
                        </div>
                    </div>
                    {{-- Tambahan Panjar --}}
                    <div class="mb-3 row">
                        <label for="tambahan_panjar" class="col-md-2 col-form-label">Tambahan Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="tambahan_panjar" name="tambahan_panjar" required
                                readonly style="text-align: right">
                        </div>
                        <label for="nilai_tambahan_panjar" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nilai_tambahan_panjar"
                                name="nilai_tambahan_panjar" required readonly style="text-align: right">
                        </div>
                    </div>
                    {{-- Total Panjar --}}
                    <div class="mb-3 row">
                        <label for="total_panjar" class="col-md-2 col-form-label">Total Panjar</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="total_panjar" name="total_panjar" required
                                readonly style="text-align: right">
                        </div>
                    </div>
                    {{-- Total Transaksi --}}
                    <div class="mb-3 row">
                        <label for="total_transaksi" class="col-md-2 col-form-label">Total Transaksi</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="total_transaksi" name="total_transaksi"
                                required readonly style="text-align: right">
                        </div>
                    </div>
                    {{-- Sisa Panjar --}}
                    <div class="mb-3 row">
                        <label for="sisa_panjar" class="col-md-2 col-form-label">Sisa Panjar</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="sisa_panjar" name="sisa_panjar" required
                                readonly style="text-align: right">
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
                            <a href="{{ route('kembalipanjar.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.pengembalian_panjar.js.create');
@endsection
