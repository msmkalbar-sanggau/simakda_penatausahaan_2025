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
                    <div class="mb-3 row jns_penetapan">
                        <label for="jns_penetapan" class="col-md-2 col-form-label">Jenis Penetapan</label>
                        <div class="col-md-4">
                                <select class="form-control select2-multiple @error('jns_penetapan') is-invalid @enderror"
                                    style="width: 100%;" id="jns_penetapan" name="jns_penetapan" data-placeholder="Silahkan Pilih">
                                    <optgroup label="Jenis Penetapan">
                                        <option value="" disabled selected>Jenis Penetapan</option>
                                        <option value="1" {{ old('nomor') == '1' ? 'selected' : '' }}>BPHTB
                                        <option value="2" {{ old('nomor') == '1' ? 'selected' : '' }}>SKP BAPENDA
                                        <option value="3" {{ old('nomor') == '1' ? 'selected' : '' }}>PBB BAPENDA
                                        <option value="4" {{ old('nomor') == '1' ? 'selected' : '' }}>PBB KASDA
                                        </option>
                                    </optgroup>
                                </select>
                                @error('nomor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                        data-nm_rek="{{ $akun->nm_rek }}" data-kd_rek6="{{ $akun->kd_rek6}}" data-kd_rek="{{ $akun->kd_rek }}">
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
                            <input class="form-control" type="text" id="kode_rek" name="kode_rek" required readonly hidden>
                            <input class="form-control" type="text" id="kode_rek6" name="kode_rek6" required readonly hidden>
                        </div>
                    </div>
                    {{-- Pajak Hotel --}}
                    <div class="mb-3 row pajak_hotel">
                        <label for="pajak_hotel" class="col-md-2 col-form-label">Jenis Hotel</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('pajak_hotel') is-invalid @enderror"
                            style="width: 100%;" id="pajak_hotel" name="pajak_hotel" data-placeholder="Silahkan Pilih">
                            <optgroup label="Jenis Hotel">
                                <option value="" disabled selected>Pilih Jenis Hotel</option>
                                <option value="41010601000101">Hotel Bintang V Berlian</option>
                                <option value="41010601000102">Hotel Bintang V</option>
                                <option value="41010601000103">Hotel Bintang IV</option>
                                <option value="41010601000104">Hotel Bintang III</option>
                                <option value="41010601000105">Hotel Bintang II</option>
                                <option value="41010601000106">Hotel Bintang II</option>
                                <option value="41010601000107">Hotel Bintang I</option>
                                <option value="41010601000108">Hotel Melati III</option>
                                <option value="41010601000109">Hotel Melati II</option>
                                <option value="41010601000110">Hotel Melati I</option>
                                <option value="41010601000111">Motel</option>
                                <option value="41010601000112">Cottage</option>
                                <option value="41010601000113">losemen / penginapan / pesanggrahan / rumah kos</option>
                                <option value="41010601000114">Wisma Pariwisata</option>
                                <option value="41010601000115">Gubuk Pariwisata</option>
                            </optgroup>
                        </select>
                        </div>
                    </div>
                    {{-- Sub Kegiatan dan Nilai --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_sub_kegiatan" name="kd_sub_kegiatan" required
                                readonly>
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
                            <a href="{{ route('penetapan_pendapatan.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.penetapan_pendapatan.js.create');
@endsection
