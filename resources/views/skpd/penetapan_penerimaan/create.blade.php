@extends('template.app')
@section('title', 'Input Data Penetapan | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Penetapan
                </div>
                <div class="card-body">
                    @csrf
                    <p class="card-title-desc">Input Langsung hanya untuk Penetapan dan Penerimaan. Penyetoran tetap Input
                        Manual seperti Biasa</p>
                    {{-- Nomor dan Pilihan --}}
                    <div class="mb-3 row">
                        <label for="nomor" class="col-md-2 col-form-label">Nomor</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nomor" name="nomor"
                                placeholder="Tidak perlu diisi" required readonly>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" id="pilihan">
                                <label class="form-check-label" for="pilihan">
                                    Input penerimaan tanpa penetapan</label>
                            </div>
                        </div>
                    </div>
                    {{-- No Penetapan dan Tanggal Penetapan --}}
                    <div class="mb-3 row">
                        <label for="no_tetap" class="col-md-2 col-form-label">No. Penetapan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_tetap" name="no_tetap"
                                placeholder="Silahkan Diisi" required>
                        </div>
                        <label for="tgl_tetap" class="col-md-2 col-form-label">Tanggal Penetapan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_tetap" name="tgl_tetap" required>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- Kode dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $skpd->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $skpd->nm_skpd }}">
                        </div>
                    </div>
                    {{-- Kode dan Nama Akun --}}
                    <div class="mb-3 row">
                        <label for="kode_akun" class="col-md-2 col-form-label">Kode Akun</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kode_akun"
                                name="kode_akun">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_akun as $akun)
                                    <option value="{{ $akun->kd_rek6 }}" data-kd_sub_kegiatan="{{ $akun->kd_sub_kegiatan }}"
                                        data-nm_rek="{{ $akun->nm_rek }}" data-kd_rek="{{ $akun->kd_rek }}">
                                        {{ $akun->kd_rek6 }} | {{ $akun->kd_rek }} | {{ $akun->nm_rek }} |
                                        {{ $akun->nm_rek5 }} |
                                        {{ $akun->kd_sub_kegiatan }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                        <label for="nama_akun" class="col-md-2 col-form-label">Nama Akun</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nama_akun" name="nama_akun" required readonly>
                            <input class="form-control" type="text" id="kode_rek" name="kode_rek" required readonly
                                hidden>
                        </div>
                    </div>
                    {{-- Kode dan Nama Pengirim --}}
                    <div class="mb-3 row">
                        <label for="kode_pengirim" class="col-md-2 col-form-label">Kode Pengirim</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kode_pengirim"
                                name="kode_pengirim">
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
                    {{-- Sub Kegiatan dan Nilai --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_sub_kegiatan" name="kd_sub_kegiatan"
                                required readonly>
                        </div>
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
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
                            <a href="{{ route('penetapan_penerimaan.index') }}"
                                class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.penetapan_penerimaan.js.create');
@endsection
