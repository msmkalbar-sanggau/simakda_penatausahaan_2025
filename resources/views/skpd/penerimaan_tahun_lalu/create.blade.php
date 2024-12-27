@extends('template.app')
@section('title', 'Input Data Penerimaan Atas Piutang Tahun Lalu | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Penerimaan Atas Piutang Tahun Lalu
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Nomor Khusus Bapenda --}}
                    <div class="mb-3 row jns_penerimaan">
                        <label for="jns_penerimaan" class="col-md-2 col-form-label">Jenis Penerimaan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('jns_penerimaan') is-invalid @enderror"
                                style="width: 100%;" id="jns_penerimaan" name="jns_penerimaan"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Jenis Penerimaan">
                                    <option value="" disabled selected>Jenis Penerimaan</option>
                                    <option value="1" {{ old('nomor') == '1' ? 'selected' : '' }}>PBB KASDA
                                    <option value="2" {{ old('nomor') == '1' ? 'selected' : '' }}>PBB BAPENDA
                                    <option value="3" {{ old('nomor') == '1' ? 'selected' : '' }}>SKP
                                    <option value="4" {{ old('nomor') == '1' ? 'selected' : '' }}>STS KASDA
                                    </option>
                                </optgroup>
                            </select>
                            @error('nomor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- No dan Tanggal Terima --}}
                    <div class="mb-3 row">
                        <label for="no_terima" class="col-md-2 col-form-label">No. Terima</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_terima" name="no_terima"
                                placeholder="Silahkan Diisi" required>
                        </div>
                        <label for="tgl_terima" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_terima" name="tgl_terima" required>
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
                    {{-- Rekening dan Nama Rekening --}}
                    <div class="mb-3 row">
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening"
                                name="rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_akun as $akun)
                                    <option value="{{ $akun->kd_rek6 }}" data-kd_sub_kegiatan="{{ $akun->kd_sub_kegiatan }}"
                                        data-nm_rek="{{ $akun->nm_rek }}" data-kd_rek6="{{ $akun->kd_rek6 }}"
                                        data-kd_rek="{{ $akun->kd_rek }}">
                                        {{ $akun->kd_rek6 }} | {{ $akun->kd_rek }} | {{ $akun->nm_rek }} |
                                        {{ $akun->nm_rek5 }} |
                                        {{ $akun->kd_sub_kegiatan }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                        <label for="nama_rekening" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nama_rekening" name="nama_rekening" required
                                readonly>
                            <input class="form-control" type="text" id="kode_rek" name="kode_rek" required readonly
                                hidden>
                            <input class="form-control" type="text" id="kode_rek6" name="kode_rek6" required readonly
                                hidden>
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
                                    <option value="41010601000108">Hotel Melati III</option>
                                    <option value="41010601000113">losemen / penginapan / pesanggrahan / rumah kos</option>
                                </optgroup>
                            </select>
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
                    {{-- Penyetoran dan Pembayaran --}}
                    <div class="mb-3 row">
                        <label for="penyetoran" class="col-md-2 col-form-label">Jenis Setor</label>
                        <div class="col-md-2">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" name="status_setor" id="tanpa_setor"
                                    value="Tanpa Setor" onclick="opt(this.value)">
                                <label class="form-check-label col-form-label-sm" for="tanpa_setor">
                                    Tanpa Setor
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" name="status_setor" id="dengan_setor"
                                    value="Dengan Setor" onclick="opt(this.value)">
                                <label class="form-check-label col-form-label-sm" for="dengan_setor">
                                    Dengan Setor
                                </label>
                            </div>
                        </div>

                        <label for="jenis_pembayaran_tambah" class="col-md-2 col-form-label">Jenis
                            Pembayaran</label>
                        <div class="col-md-4">
                            <select id="jenis_pembayaran_tambah" name="jenis_pembayaran_tambah"
                                class="form-control form-control-sm" disabled>
                                <option value="TUNAI"> Tunai</option>
                                <option value="BANK"> Bank</option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-2 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    {{-- Catatan --}}
                    <div class="mb-1 row" style="color: red">
                        <label for="catatan" class="col-md-12 col-form-label">PERHATIAN!!!</label>
                        <label for="" class="col-md-12 col-form-label">Jika Kode rekening LO tidak tampil,
                            silahkan lakukan mapping rekening akuntansi</label>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('penerimaan_lalu.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.penerimaan_tahun_lalu.js.create');
@endsection
