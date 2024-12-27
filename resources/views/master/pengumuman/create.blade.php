@extends('template.app')
@section('title', 'Tambah Pengumuman | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{'Tambah Pengumuman'}}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{'Pengumuman'}}</a></li>
                                <li class="breadcrumb-item active">{{'Tambah Pengumuman'}}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pengumuman.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <!-- Kode SKPD -->
                        
                        <div class="mb-3 row">
                            <label for="judul" class="col-md-2 col-form-label">Judul</label>
                            <div class="col-md-10">
                                <input type="text" placeholder="Judul Pengumuman"
                                    class="form-control @error('judul') is-invalid @enderror"
                                    value="{{ old('judul') }}" id="judul" name="judul">
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Tanggal pengumuman -->
                        <div class="mb-3 row">
                            <label for="tanggal" class="col-md-2 col-form-label">Tanggal pengumuman</label>
                            <div class="col-md-10">
                                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}"
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
                                    name="isi"></textarea>
                                @error('isi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- File -->
                        <div class="mb-3 row">
                            <label for="dokumen" class="col-md-2 col-form-label">File</label>
                            <div class="col-md-10">
                                <input type="file" name="dokumen" id="dokumen" value="{{ old('dokumen') }}"
                                    class="form-control @error('dokumen') is-invalid @enderror">
                                @error('dokumen')
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
                                        <option value="1" {{ old('aktif') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('aktif') == '0' ? 'selected' : '' }}>Tidak Aktif
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
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Muncul</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Muncul
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
                theme: 'bootstrap-5'
            });
            if($("#isi").length > 0){
                tinymce.init({
                    selector: "textarea#isi",
                    height:300,
                    plugins: [
                        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "save table contextmenu directionality emoticons template paste textcolor"
                    ],
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons",
                    style_formats: [
                        {title: 'Bold text', inline: 'b'},
                        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                        {title: 'Example 1', inline: 'span', classes: 'example1'},
                        {title: 'Example 2', inline: 'span', classes: 'example2'},
                        {title: 'Table styles'},
                        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                    ]
                });
            }
            
        });
    </script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endsection
