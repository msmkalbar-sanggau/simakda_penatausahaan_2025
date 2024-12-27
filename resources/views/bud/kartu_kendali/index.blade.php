@extends('template.app')
@section('title', 'Cetak Kartu Kendali | SIMAKDA')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ 'Cetak Kartu Kendali' }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'App' }}</a></li>
                        <li class="breadcrumb-item">{{ 'Cetak Kartu Kendali' }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Kode SKPD -->
            <div class="mb-3 row">
                <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                <div class="col-md-4">
                    <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                        <option value="" disabled selected>Silahkan Pilih</option>
                        @foreach ($skpd as $kode)
                            <option value="{{ $kode->kd_skpd }}" data-nama="{{ $kode->nm_skpd }}">
                                {{ $kode->kd_skpd }} | {{ $kode->nm_skpd }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" readonly>
                </div>
            </div>
            {{-- Kegiatan --}}
            <div class="mb-3 row">
                <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kegiatan</label>
                <div class="col-md-4">
                    <select class="form-control select2-multiple" style="width: 100%" id="kd_sub_kegiatan"
                        name="kd_sub_kegiatan">
                        <option value="" disabled selected>Silahkan Pilih</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="nm_sub_kegiatan" name="nm_sub_kegiatan" readonly>
                </div>
            </div>
            {{-- Rekening --}}
            <div class="mb-3 row">
                <label for="kd_rek" class="col-md-2 col-form-label">Rekening</label>
                <div class="col-md-4">
                    <select class="form-control select2-multiple" style="width: 100%" id="kd_rek" name="kd_rek">
                        <option value="" disabled selected>Silahkan Pilih</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="nm_rek" name="nm_rek" readonly>
                </div>
            </div>
            {{-- JENIS ANGGARAN --}}
            <div class="mb-3 row">
                <label for="jns_ang" class="col-md-2 col-form-label">Jenis Anggaran</label>
                <div class="col-md-4">
                    <select class="form-control select2-multiple" style="width: 100%" id="jns_ang" name="jns_ang">
                        <option value="" disabled selected>Silahkan Pilih</option>
                        @foreach ($jenis_anggaran as $anggaran)
                            <option value="{{ $anggaran->kode }}">{{ $anggaran->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- Periode --}}
            <div class="mb-3 row">
                <label for="periode" class="col-md-2 col-form-label">Periode</label>
                <div class="col-md-4">
                    <input type="date" class="form-control" id="periode_awal" name="periode_awal">
                </div>
                <label for="periode" class="col-md-2 col-form-label">Sampai Dengan</label>
                <div class="col-md-4">
                    <input type="date" class="form-control" id="periode_akhir" name="periode_akhir">
                </div>
                <small style="padding-left: 185px">(Silahkan isi periode pertama dengan 2021-01-01 jika ingin menampilkan
                    data dari januari)</small>
            </div>
            {{-- Penandatangan --}}
            <div class="mb-3 row">
                <label for="ttd" class="col-md-2 col-form-label">Penandatangan</label>
                <div class="col-md-10">
                    <select class="form-control select2-multiple" style="width: 100%" id="ttd" name="ttd">
                        <option value="" disabled selected>Silahkan Pilih</option>
                        @foreach ($daftar_ttd as $ttd)
                            <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- Cetak Per Sub Kegiatan --}}
            <div class="mb-3 row">
                <label for="ttd" class="col-md-3 col-form-label">Cetak Per Sub Kegiatan</label>
                <div class="col-md-9">
                    <button type="button" class="btn btn-primary btn-md cetak_kegiatan" data-jenis="layar"
                        name="cetak_kegiatan">Layar</button>
                    <button type="button" class="btn btn-warning btn-md cetak_kegiatan" data-jenis="pdf"
                        name="cetak_kegiatan">PDF</button>
                </div>
            </div>
            {{-- Cetak Per Rekening --}}
            <div class="mb-3 row">
                <label for="ttd" class="col-md-3 col-form-label">Cetak Per Rekening</label>
                <div class="col-md-9">
                    <button type="button" class="btn btn-primary btn-md cetak_rekening" data-jenis="layar"
                        name="cetak_rekening">Layar</button>
                    <button type="button" class="btn btn-warning btn-md cetak_rekening" data-jenis="pdf"
                        name="cetak_rekening">PDF</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    <!-- end page title -->
@endsection
@section('js')
    @include('bud.kartu_kendali.js.index')
@endsection
