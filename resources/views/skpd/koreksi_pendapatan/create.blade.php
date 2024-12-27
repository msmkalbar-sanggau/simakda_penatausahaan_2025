@extends('template.app')
@section('title', 'Input Data Koreksi | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Koreksi
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas"
                                placeholder="Tidak Perlu Diisi" required readonly>
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_kas" name="tgl_kas" required>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                value="{{ tahun_anggaran() }}" readonly hidden>
                        </div>
                    </div>
                    {{-- Kode dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_skpd as $skpd)
                                    <option value="{{ $skpd->kd_skpd }}" data-nama="{{ $skpd->nm_skpd }}">
                                        {{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" required readonly>
                        </div>
                    </div>
                    {{-- Jenis dan Nama Jenis --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis" name="jenis">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                </option>
                            </select>
                        </div>
                        <label for="nama_jenis" class="col-md-2 col-form-label">Nama Jenis</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nama_jenis" name="nama_jenis" required readonly>
                            <input class="form-control" type="text" id="kode_rek" name="kode_rek" required readonly
                                hidden>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>

                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                        </div>
                    </div>
                    {{-- Minus dan Mempengaruhi Realisasi Pendapatan --}}
                    <div class="mb-3 row">
                        <div class="col-md-2">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" id="minus">
                                <label class="form-check-label" for="minus">
                                    Minus</label>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" id="pengaruh_realisasi">
                                <label class="form-check-label" for="pengaruh_realisasi">
                                    <i>(Mempengaruhi Realisasi Pendapatan)</i></label>
                            </div>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('koreksi_pendapatan.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.koreksi_pendapatan.js.create');
@endsection
