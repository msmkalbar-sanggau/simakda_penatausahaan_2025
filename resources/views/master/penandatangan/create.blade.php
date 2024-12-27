@extends('template.app')
@section('title', 'Tambah Tandatangan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{'Tambah Penandatangan'}}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{'Penandatangan'}}</a></li>
                                <li class="breadcrumb-item active">{{'Tambah Penandatangan'}}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tandatangan.store') }}" method="post">
                        @csrf
                        <!-- SKPD -->
                        <div class="mb-3 row">
                            <label for="kd_skpd" class="col-md-2 col-form-label">SKPD/Unit</label>
                            <div class="col-md-10">
                                <select class="form-control select2-modal @error('kd_skpd') is-invalid @enderror"
                                    style=" width: 100%;" id="kd_skpd" name="kd_skpd">
                                    <option value="" disabled selected>Silahkan Pilih</option>
                                </select>
                                @error('kd_skpd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- NIP -->
                        <div class="mb-3 row">
                            <label for="nip" class="col-md-2 col-form-label">NIP</label>
                            <div class="col-md-10">
                                <input type="text" placeholder="Isi NIP Penandatangan"
                                    class="form-control @error('nip') is-invalid @enderror"
                                    value="{{ old('nip') }}" id="nip" name="nip">
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- nama -->
                        <div class="mb-3 row">
                            <label for="nama" class="col-md-2 col-form-label">Nama Lengkap</label>
                            <div class="col-md-10">
                                <input type="text" placeholder="Isi nama Penandatangan"
                                    class="form-control @error('nama') is-invalid @enderror"
                                    value="{{ old('nama') }}" id="nama" name="nama">
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- jabatan -->
                        <div class="mb-3 row">
                            <label for="jabatan" class="col-md-2 col-form-label">Jabatan</label>
                            <div class="col-md-10">
                                <input type="text" placeholder="Isi jabatan Penandatangan"
                                    class="form-control @error('jabatan') is-invalid @enderror"
                                    value="{{ old('jabatan') }}" id="jabatan" name="jabatan">
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- pangkat -->
                        <div class="mb-3 row">
                            <label for="pangkat" class="col-md-2 col-form-label">Pangkat <small>Tanpa Golongan</small></label>
                            <div class="col-md-10">
                                <input type="text" placeholder="Isi pangkat Penandatangan tanpa golongan (Pembina Tingkat I)"
                                    class="form-control @error('pangkat') is-invalid @enderror"
                                    value="{{ old('pangkat') }}" id="pangkat" name="pangkat">
                                @error('pangkat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- pangkat -->
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
                theme: 'bootstrap-5'
            });
            
            $('#kd_skpd').select2({
                theme: 'bootstrap-5'
            });
            $('#kode').select2({
                theme: 'bootstrap-5'
            });
        
        
            

        });
    </script>
    <script>
        

        $(document).ready(function() {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
           
            $.ajax({
                url: "{{ route('tandatangan.skpd') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $('#kd_skpd').empty();
                    $('#kd_skpd').append(
                        `<option value="" disabled selected>Pilih SKPD</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_skpd').append(
                            `<option value="${data.kd_skpd}" data-nama="${data.nm_skpd}">${data.kd_skpd} | ${data.nm_skpd}</option>`
                        );
                    })
                }
            })
        
        });
    </script>
@endsection
