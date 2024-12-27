@extends('template.app')
@section('title', 'Input Data Penerimaan Lain PPKD | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Penerimaan Lain PPKD
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No. Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required>
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_kas" name="tgl_kas" required>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- Jenis dan Nama Jenis --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis" name="jenis">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_jenis as $jenis)
                                    <option value="{{ $jenis->kd_rek6 }}" data-nama="{{ $jenis->nm_rek6 }}">
                                        {{ $jenis->kd_rek6 }} | {{ $jenis->nm_rek6 }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                        <label for="nama_jenis" class="col-md-2 col-form-label">Nama Jenis</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nama_jenis" name="nama_jenis" required readonly>
                        </div>
                    </div>
                    {{-- Pengirim dan Nama Pengirim --}}
                    <div class="mb-3 row">
                        <label for="pengirim" class="col-md-2 col-form-label">Pengirim</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pengirim"
                                name="pengirim">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_pengirim as $pengirim)
                                    <option value="{{ $pengirim->kd_pengirim }}" data-nama="{{ $pengirim->nm_pengirim }}">
                                        {{ $pengirim->kd_pengirim }} | {{ $pengirim->nm_pengirim }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                        <label for="nama_pengirim" class="col-md-2 col-form-label">Nama Pengirim</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nama_pengirim" name="nama_pengirim" required
                                readonly>
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
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('penerimaan_ppkd.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.penerimaan_lain_ppkd.js.create');
@endsection
