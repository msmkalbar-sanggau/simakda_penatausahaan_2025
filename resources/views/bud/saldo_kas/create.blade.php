@extends('template.app')
@section('title', 'Input SP2B | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Rekening
                    <p style="color: red"><b>Semua inputan harus diisi!</b></p>
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No tersimpan --}}
                    {{-- <div class="mb-3 row">
                        <label for="nomor" class="col-md-2 col-form-label">No. Tersimpan</label>
                        <div class="col-md-10">
                            <input class="form-control @error('nomor') is-invalid @enderror " type="text" id="nomor"
                                name="nomor" placeholder="Tidak perlu diisi atau diedit" readonly>
                            @error('nomor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> --}}
                    <div class="mb-3 row">
                        <label for="rek_bank" class="col-md-2 col-form-label">Kode Rekening</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rek_bank"
                                name="rek_bank">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="3001006966">Bank-3001006966</option>
                                <option value="3001000016">Bank-3001000016</option>
                            </select>
                        </div>
                        <label for="uraian" class="col-md-2 col-form-label">Uraian</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="uraian" name="uraian" value="Saldo Awal"
                                disabled>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                style="text-align: right" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>

                    <!-- SIMPAN -->
                    <div class="mb-6 row" style="text-align;center">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('saldo_kas.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
    @section('js')
        @include('bud.saldo_kas.js.create');
    @endsection
