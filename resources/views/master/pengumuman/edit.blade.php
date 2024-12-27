@extends('template.app')
@section('title', 'Pengumuman | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ 'Edit Pengumuman' }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'Pengumuman' }}</a></li>
                                <li class="breadcrumb-item active">{{ 'Edit Pengumuman' }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pengumuman.update', Crypt::encryptString($data_pengumuman->id)) }}"
                        method="post" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="mb-3 row">
                            <label for="judul" class="col-md-2 col-form-label">Judul</label>
                            <div class="col-md-10">
                                <input type="text" placeholder="Judul Pengumuman"
                                    class="form-control @error('judul') is-invalid @enderror"
                                    value="{{ $data_pengumuman->judul }}" id="judul" name="judul">
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Tanggal pengumuman -->
                        <div class="mb-3 row">
                            <label for="tanggal" class="col-md-2 col-form-label">Tanggal pengumuman</label>
                            <div class="col-md-10">
                                <input type="date" name="tanggal" id="tanggal" value="{{ $data_pengumuman->tanggal }}"
                                    class="form-control @error('tanggal') is-invalid @enderror">
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Isi pengumuman -->
                        <div class="mb-3 row">
                            <label for="isi" class="col-md-2 col-form-label">Isi pengumuman</label>
                            <div class="col-md-10">
                                <textarea class="form-control @error('isi') is-invalid @enderror" value="{{ old('isi') }}"
                                    type="text" rows="5" placeholder="Silahkan isi" id="isi"
                                    name="isi">{{ $data_pengumuman->isi }}</textarea>
                                @error('isi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- File -->
                        <div class="mb-3 row">
                            <label for="dokumen" class="col-md-2 col-form-label">File</label>
                            <div class="col-md-5">
                                <input type="file" name="dokumen" id="dokumen" value="{{ $data_pengumuman->file }}"
                                    class="form-control @error('dokumen') is-invalid @enderror">
                                @error('dokumen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="dokumenasli" id="dokumenasli" value="{{ $data_pengumuman->file }}"
                                    class="form-control @error('dokumenasli') is-invalid @enderror" readonly>
                                @error('dokumenasli')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="aktif" class="col-md-2 col-form-label">Aktif/Tidak Aktif</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('aktif') is-invalid @enderror"
                                    name="aktif" required>
                                        <option value="" selected disabled>Silahkan Pilih</option>
                                        <option value="1" {{ $data_pengumuman->aktif == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $data_pengumuman->aktif == '0' ? 'selected' : '' }}>Tidak Aktif
                                        </option>
                                </select>
                                @error('aktif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="status" class="col-md-2 col-form-label">Muncul di beranda</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('status') is-invalid @enderror"
                                    name="status" required>
                                        <option value="" selected disabled>Silahkan Pilih</option>
                                        <option value="1" {{ $data_pengumuman->status == '1' ? 'selected' : '' }}>Muncul</option>
                                        <option value="0" {{ $data_pengumuman->status == '0' ? 'selected' : '' }}>Tidak Muncul
                                        </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- SIMPAN -->
                        <div style="float: right;">
                            <button type="submit" id="save" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('pengumuman.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
           
        });
        $('#nm_rekening').on("change", function() {
            let rekening = $(this).find(':selected').data('rekening');
            let npwp = $(this).find(':selected').data('npwp');
            $("#no_rekening").val(rekening);
            $("#npwp").val(npwp);
        });
    </script>
     <script>
        ClassicEditor
        .create( document.querySelector( '#isi' ) )
        .catch( error => {
            console.error( error );
        } );
        </script>
@endsection
