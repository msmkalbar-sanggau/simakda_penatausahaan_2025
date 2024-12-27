@extends('template.app')
@section('title', 'Input Ambil Simpanan Bank Ke Kasben | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Terima Perbidang
                </div>
                <div class="card-body">
                    @csrf
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
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- Informasi Setor Sisa Dropping --}}
                    <div class="mb-3 row">
                        <label for="ketdrop" class="col-md-2 col-form-label">Informasi Setor Sisa</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="ketdrop" name="ketdrop">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($ketdrop as $drop)
                                    <option value="{{ $drop->kd_skpd_sumber }}" data-no_bukti="{{ $drop->no_bukti }}"
                                        data-tgl_bukti="{{ $drop->tgl_bukti }}" data-nilai="{{ $drop->nilai }}">
                                        {{ $drop->kd_skpd_sumber }} |
                                        {{ $drop->no_bukti }} | {{ $drop->keterangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan Dana --}}
                    <div class="mb-3 row">
                        <label for="ketdana" class="col-md-2 col-form-label"></label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="ketdana" name="ketdana" required readonly>
                        </div>
                    </div>
                    {{-- Nomor dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required readonly>
                            <input class="form-control" type="text" id="no_kas_asli" name="no_kas_asli" required readonly
                                hidden>
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal Kas</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_kas" name="tgl_kas" readonly>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    {{-- Sisa Kas Bank dan Nilai --}}
                    <div class="mb-3 row">
                        <label for="sisa_kas" class="col-md-2 col-form-label">Sisa Kas Bank</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="sisa_kas" name="sisa_kas" required readonly
                                value="{{ rupiah($sisa_bank) }}" style="text-align: right">
                        </div>
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_kasben" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.simpanan_bank.kasben') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.kasben.js.create');
@endsection
