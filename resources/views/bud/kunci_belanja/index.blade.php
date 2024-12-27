@extends('template.app')
@section('title', 'Buka/Kunci Sub Rincian Belanja Renja | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rencana Anggaran Kas Belanja Sub Rincian Objek
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Kode SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-12 col-form-label">Kode SKPD</label>
                        <div class="col-md-12">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($skpd as $kode)
                                    <option value="{{ $kode->kd_skpd }}">
                                        {{ $kode->kd_skpd }} | {{ $kode->nm_skpd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Sub Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-12 col-form-label">Kode Sub Kegiatan</label>
                        <div class="col-md-12">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    {{-- KODE REKENING --}}
                    <div class="mb-3 row">
                        <label for="kode_rekening" class="col-md-12 col-form-label">Kode Rekening</label>
                        <div class="col-md-12">
                            <select class="form-control select2-multiple" style="width: 100%" id="kode_rekening"
                                name="kode_rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- AKTIFKAN/NON AKTIFKAN -->
                    <div class="text-center">
                        <button id="pilihan" class="btn btn-primary btn-md">PILIHAN</button>
                    </div>
                    <div class="mb-3 row">
                        <label for="catatan" class="col-md-12 col-form-label" style="color: red">MERAH BERARTI REKENING
                            NON-AKTIF</label>
                        <label for="catatan" class="col-md-12 col-form-label">HITAM BERARTI REKENING AKTIF</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_pilihan" class="modal fade bs-example-modal-center" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">PILIHAN AKTIFKAN/NON-AKTIFKAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-info kunci" data-jenis="aktifkan_skpd">Aktifkan Per
                                SKPD</button>
                            <button type="button" class="btn btn-md btn-danger kunci"
                                data-jenis="nonaktifkan_skpd">Nonaktifkan Per
                                SKPD</button>
                            <button type="button" class="btn btn-md btn-info kunci" data-jenis="aktifkan_kegiatan">Aktifkan
                                Per Sub Kegiatan</button>
                            <button type="button" class="btn btn-md btn-danger kunci"
                                data-jenis="nonaktifkan_kegiatan">Nonaktifkan Per Sub Kegiatan</button>
                            <button type="button" class="btn btn-md btn-info kunci" data-jenis="aktifkan_rekening">Aktifkan
                                Per Rekening</button>
                            <button type="button" class="btn btn-md btn-danger kunci"
                                data-jenis="nonaktifkan_rekening">Nonaktifkan Per Rekening</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('bud.kunci_belanja.js.index')
@endsection
