@extends('template.app')
@section('title', 'Kontrak | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ 'Edit Penandatangan' }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'Penandatangan' }}</a></li>
                                <li class="breadcrumb-item active">{{ 'Edit Penandatangan' }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tandatangan.update', Crypt::encryptString($data_tandatangan->id)) }}"
                        method="post">
                        @method('PUT')
                        @csrf
                        <!-- Kode SKPD -->
                        <div class="mb-3 row">
                            <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD/Unit</label>
                            <div class="col-md-10">
                                <input type="text" readonly class="form-control @error('kd_skpd') is-invalid @enderror"
                                    name="kd_skpd" id="kd_skpd" value="{{ $data_tandatangan->kd_skpd }}">
                                @error('kd_skpd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Nama SKPD -->
                        <div class="mb-3 row">
                            <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD/Unit</label>
                            <div class="col-md-10">
                                <input class="form-control @error('nm_skpd') is-invalid @enderror" type="text"
                                    id="nm_skpd" name="nm_skpd" readonly required value="{{ cari_nama($data_tandatangan->kd_skpd,'ms_skpd','kd_skpd','nm_skpd') }}">
                                @error('nm_skpd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- NIP -->
                        <div class="mb-3 row">
                            <label for="nip" class="col-md-2 col-form-label">NIP</label>
                            <div class="col-md-10">
                                <input type="text" placeholder="Isi Nomor Kontrak Tanpa Spasi"
                                    class="form-control @error('nip') is-invalid @enderror"
                                    value="{{ $data_tandatangan->nip }}" id="nip" name="nip">
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- NAMA -->
                        <div class="mb-3 row">
                            <label for="nama" class="col-md-2 col-form-label">Nama Lengkap</label>
                            <div class="col-md-10">
                                <input type="text" name="nama" id="nama"
                                    value="{{ $data_tandatangan->nama }}"
                                    class="form-control @error('nama') is-invalid @enderror">
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Jabatan -->
                        <div class="mb-3 row">
                            <label for="jabatan" class="col-md-2 col-form-label">Jabatan</label>
                            <div class="col-md-10">
                                <input class="form-control @error('jabatan') is-invalid @enderror"
                                    value="{{ $data_tandatangan->jabatan }}" type="text"
                                    placeholder="Silahkan isi dengan nama pelaksana pekerjaan" id="jabatan"
                                    name="jabatan">
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Pangkat -->
                        <div class="mb-3 row">
                            <label for="pangkat" class="col-md-2 col-form-label">Pangkat (Tanpa Golongan)</label>
                            <div class="col-md-10">
                                <input class="form-control @error('pangkat') is-invalid @enderror" type="text"
                                    placeholder="Silahkan isi dengan nama pangkat tanpa golongan (Pembina Tingkat I)" value="{{ $data_tandatangan->pangkat }}"
                                    id="pangkat" name="pangkat">
                                @error('pangkat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                         <!-- kode -->
                         <div class="mb-3 row">
                            <label for="kode" class="col-md-2 col-form-label">Jenis</label>
                            <div class="col-md-10">
                                <select class="form-control select2-modal" id="kode" name="kode">
                                    <option value="" disabled selected>Silahkan Pilih</option>
                                    <option value="1">Gubernur</option>
                                    <option value="2">Bupati</option>
                                    <option value="3">Walikota</option>
                                    <option value="PA">Pengguna Anggaran</option>
                                    <option value="KPA">Kuasa Pengguna Anggaran</option>
                                    <option value="BK">Bendahara Pengeluaran</option>
                                    <option value="BPP">Bendahara Pengeluaran Pembantu</option>
                                    <option value="BP">Bendahara Penerimaan </option>
                                    <option value="PPTK">PPTK</option>
                                    <option value="PPK">PPK</option>
                                    <option value="BUD">BUD</option>
                                    <option value="SETDA">SETDA/SEKDA</option>
                                </select>
                            </div>
                        </div>
                       
                       
                        <!-- SIMPAN -->
                        <div style="float: right;">
                            <button type="submit" id="save" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('tandatangan.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
            });

            $('#kode').select2({
                theme: 'bootstrap-5'
            });

            
            $('#kode').val("{{ $data_tandatangan->kode }}").trigger("change")
        });
       
    </script>
@endsection
