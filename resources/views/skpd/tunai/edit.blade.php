@extends('template.app')
@section('title', 'Edit Ambil Simpanan Bank Ke Tunai | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Ambil Simpanan
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
                    {{-- Nomor dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required readonly
                                placeholder="Tidak perlu diisi atau diedit" value="{{ $tunai->no_kas }}">
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal Kas</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_kas" name="tgl_kas"
                                value="{{ $tunai->tgl_kas }}">
                        </div>
                    </div>
                    {{-- Nomor dan Tanggal Bukti --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required readonly
                                placeholder="Tidak perlu diisi atau diedit" value="{{ $tunai->no_bukti }}">
                        </div>
                        <label for="tgl_bukti" class="col-md-2 col-form-label">Tanggal Bukti</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_bukti" name="tgl_bukti" readonly
                                value="{{ $tunai->tgl_bukti }}">
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
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right"
                                value="{{ $tunai->nilai }}">
                        </div>
                    </div>
                    {{-- Bank dan Nama Bank --}}
                    <div class="mb-3 row">
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <select name="bank" id="bank" style="width: 100%" class="form-control select2-multiple">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_bank as $bank)
                                    <option value="{{ $bank->kode }}" data-nama="{{ $bank->nama }}"
                                        {{ $bank->kode == Str::of($tunai->bank)->rtrim() ? 'selected' : '' }}>
                                        {{ $bank->kode }}
                                        | {{ $bank->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="nm_bank" class="col-md-2 col-form-label">Nama Bank</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_bank" name="nm_bank" readonly>
                        </div>
                    </div>
                    {{-- Nama Rekening --}}
                    <div class="mb-3 row">
                        <label for="nm_rekening" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="nm_rekening" name="nm_rekening">{{ $tunai->nm_rekening }}</textarea>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan">{{ $tunai->keterangan }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_tunai" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.simpanan_bank.tunai') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.tunai.js.edit');
@endsection
