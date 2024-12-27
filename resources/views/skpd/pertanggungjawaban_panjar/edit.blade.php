@extends('template.app')
@section('title', 'Ubah Pertanggungjawaban Panjar | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Pertanggungjawaban Panjar
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No tersimpan --}}
                    <div class="mb-3 row">
                        <label for="no_simpan" class="col-md-2 col-form-label">No. Tersimpan</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="no_simpan" name="no_simpan"
                                value="{{ $panjar->no_kas }}" required readonly>
                            <small>Tidak perlu diisi atau diedit</small>
                        </div>
                    </div>
                    {{-- No Kas dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No. Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required
                                value="{{ $panjar->no_kas }}" readonly>
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_kas" name="tgl_kas" required
                                value="{{ $panjar->tgl_kas }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- No Panjar dan Tanggal Panjar --}}
                    <div class="mb-3 row">
                        <label for="no_panjar" class="col-md-2 col-form-label">No. Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_panjar" name="no_panjar" required
                                value="{{ $panjar->no_panjar }}" readonly>
                        </div>
                        <label for="tgl_panjar" class="col-md-2 col-form-label">Tanggal Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_panjar" name="tgl_panjar"
                                value="{{ $panjar->tgl_panjar }}" required readonly>
                        </div>
                    </div>
                    {{-- Kode dan Nilai --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd"
                                value="{{ $panjar->kd_skpd }}" required readonly>
                        </div>
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nilai" name="nilai"
                                value="{{ rupiah($panjar->nilai) }}" required readonly style="text-align: right">
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-2 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan" readonly>{{ $panjar->keterangan }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('jawabpanjar.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.pertanggungjawaban_panjar.js.edit');
@endsection
