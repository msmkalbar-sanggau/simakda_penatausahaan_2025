@extends('template.app')
@section('title', 'Koreksi Data | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Koreksi Data
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Pilihan --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-12">
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="sp2d"
                                    checked>
                                <label class="form-check-label" for="pilihan">SP2D</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="spm">
                                <label class="form-check-label" for="pilihan">SPM</label>
                            </div>
                        </div>
                    </div>
                    {{-- SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD/UNIT</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_skpd as $skpd)
                                    <option value="{{ $skpd->kd_skpd }}">
                                        {{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- NO SP2D --}}
                    <div class="mb-3 row" id="pilih_sp2d">
                        <label for="no_sp2d" class="col-md-2 col-form-label">No. SP2D</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="no_sp2d" name="no_sp2d">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- NO SPM --}}
                    <div class="mb-3 row" id="pilih_spm">
                        <label for="no_spm" class="col-md-2 col-form-label">No. SPM</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="no_spm" name="no_spm">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Tanggal Mulai dan Tanggal Akhir --}}
                    <div class="mb-3 row">
                        <label for="tgl_mulai" class="col-md-2 col-form-label">Tanggal Mulai</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_mulai" name="tgl_mulai" required>
                        </div>
                        <label for="tgl_akhir" class="col-md-2 col-form-label">Tanggal Akhir</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir">
                        </div>
                    </div>
                    {{-- Jenis --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%;" id="jenis"
                                name="jenis">
                                <option value=" ">Tanpa Termin / Sekali Pembayaran</option>
                                <option value="1">Konstruksi Dalam Pengerjaan</option>
                                <option value="2">Uang Muka</option>
                                <option value="3">Hutang Tahun Lalu</option>
                                <option value="4">Perbulan</option>
                                <option value="5">Bertahap</option>
                                <option value="6">Berdasarkan Progres / Pengajuan Pekerjaan</option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" rows="5" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">UPDATE</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('fungsi.koreksi_data.js.create');
@endsection
