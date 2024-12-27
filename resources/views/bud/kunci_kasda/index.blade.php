@extends('template.app')
@section('title', 'Kunci Data Kasda | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Kunci Data Kasda
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Pilihan --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-6">
                            <label for="" class="form-label">Pilih</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="keseluruhan"
                                    value="2">
                                <label class="form-check-label" for="pilihan">Keseluruhan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="per_skpd"
                                    value="3">
                                <label class="form-check-label" for="pilihan">Per SKPD</label>
                            </div>
                        </div>
                    </div>
                    {{-- Per SKPD --}}
                    <div class="mb-3 row" id="skpd">
                        <label for="kd_skpd" class="col-md-12 col-form-label">Kode SKPD</label>
                        <div class="col-md-12">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($skpd as $kode)
                                    <option value="{{ $kode->kd_skpd }}" data-tgl="{{ $kode->tgl_kunci }}">
                                        {{ $kode->kd_skpd }} | {{ nama_skpd($kode->kd_skpd) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="tgl_kunci" class="col-md-12 col-form-label">Tanggal Kunci</label>
                        <div class="col-md-12">
                            <input type="date" id="tgl_kunci" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- TANGGAL AKHIR --}}
                    <div class="mb-3 row">
                        <label for="tgl_akhir" class="col-md-12 col-form-label">Tanggal Akhir</label>
                        <div class="col-md-12">
                            <input type="date" id="tgl_akhir" class="form-control">
                        </div>
                    </div>
                    {{-- SIMPAN --}}
                    <div class="text-center">
                        <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('bud.kunci_kasda.js.index')
@endsection
