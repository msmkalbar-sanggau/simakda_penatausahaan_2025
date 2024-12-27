@extends('template.app')
@section('title', 'Input Data Buku Kas Penerimaan | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Buku Kas Penerimaan
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">Nomor</label>
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
                    {{-- Jenis --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis" name="jenis">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1"> Deposito </option>
                                <option value="2"> Penerimaan Non SP2D </option>
                            </select>
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
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('non_pendapatan.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.penerimaan_non_pendapatan.js.create');
@endsection
