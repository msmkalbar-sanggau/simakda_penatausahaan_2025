@extends('template.app')
@section('title', 'Cek Anggaran | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    CEK NILAI ANGGARAN DAN ANGGARAN KAS
                </div>
                <div class="card-body">
                    @csrf
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD/UNIT</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($skpd as $kode)
                                    <option value="{{ $kode->kd_skpd }}" data-nama="{{ $kode->nm_skpd }}">
                                        {{ $kode->kd_skpd }} | {{ $kode->nm_skpd }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" required readonly>
                        </div>
                    </div>
                    {{-- Jenis Anggaran --}}
                    <div class="mb-3 row">
                        <label for="jenis_anggaran" class="col-md-2 col-form-label">Status Anggaran</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis_anggaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    {{-- Jenis Anggaran Kas --}}
                    <div class="mb-3 row">
                        <label for="jenis_rak" class="col-md-2 col-form-label">Status Angkas</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis_rak">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: center;">
                        <div class="col-md-12" style="text-align: center">
                            <button class="btn btn-dark btn-md cek_rak" data-jenis="layar">Layar</button>
                            <button class="btn btn-warning btn-md cek_rak" data-jenis="pdf">PDF</button>
                            <button class="btn btn-success btn-md cek_rak" data-jenis="excel">Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <label id="demo" class="col-md-12"></label>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.cek_rak.js.cetak');
@endsection
