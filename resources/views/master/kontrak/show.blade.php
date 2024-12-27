@extends('template.app')
@section('title', 'Kontrak | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ 'View Kontrak' }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'Kontrak' }}</a></li>
                                <li class="breadcrumb-item active">{{ 'View' }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('kontrak.index') }}" class="btn btn-warning btn-md" style="float: right;">Kembali</a>
                </div>
                <div class="card-body">
                    <!-- Kode SKPD -->
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD/Unit</label>
                        <div class="col-md-10">
                            <input type="text" readonly class="form-control" name="kd_skpd" id="kd_skpd"
                                value="{{ $data->kd_skpd }}">
                        </div>
                    </div>
                    <!-- Nama SKPD -->
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD/Unit</label>
                        <div class="col-md-10">
                            <input class="form-control" readonly type="text" value="{{ $skpd->nm_skpd }}" id="nm_skpd"
                                name="nm_skpd" readonly required>
                        </div>
                    </div>
                    <!-- No Kontrak -->
                    <div class="mb-3 row">
                        <label for="no_kontrak" class="col-md-2 col-form-label">No Kontrak</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" readonly value="{{ $data->no_kontrak }}"
                                id="no_kontrak" name="no_kontrak">
                        </div>
                    </div>
                    <!-- Tanggal Kontrak -->
                    <div class="mb-3 row">
                        <label for="tgl_kerja" class="col-md-2 col-form-label">Tanggal Kontrak</label>
                        <div class="col-md-10">
                            <input type="text" name="tgl_kerja" id="tgl_kerja" readonly
                                value="{{ \Carbon\Carbon::parse($data->tgl_kerja)->locale('id')->isoFormat('D MMMM Y') }}"
                                class="form-control">
                        </div>
                    </div>
                    <!-- Pelaksana Pekerjaan/Rekanan -->
                    <div class="mb-3 row">
                        <label for="nmpel" class="col-md-2 col-form-label">Pelaksana Pekerjaan/Rekanan</label>
                        <div class="col-md-10">
                            <input class="form-control" readonly value="{{ $data->nmpel }}" type="text" id="nmpel"
                                name="nmpel">
                        </div>
                    </div>
                    <!-- Pimpinan -->
                    <div class="mb-3 row">
                        <label for="pimpinan" class="col-md-2 col-form-label">Pimpinan</label>
                        <div class="col-md-10">
                            <input class="form-control" readonly type="text" value="{{ $data->pimpinan }}" id="pimpinan"
                                name="pimpinan">
                        </div>
                    </div>
                    <!-- Nama Pekerjaan -->
                    <div class="mb-3 row">
                        <label for="nm_kerja" class="col-md-2 col-form-label">Nama Pekerjaan</label>
                        <div class="col-md-10">
                            <input class="form-control" readonly type="text" value="{{ $data->nm_kerja }}"
                                id="nm_kerja" name="nm_kerja">
                        </div>
                    </div>

                    <!-- No Referensi Bank -->
                    <div class="mb-3 row">
                        <label for="no_ref" class="col-md-2 col-form-label">No Referensi Bank</label>
                        <div class="col-md-10">
                            <input class="form-control" readonly type="text"
                                value="{{ isset($data) && isset($data->no_ref) ? $data->no_ref : '-' }}" id="no_ref"
                                name="no_ref">
                        </div>
                    </div>

                    <!-- Rekanan -->
                    <div class="mb-3 row">
                        <label for="nm_rekening" class="col-md-2 col-form-label">(Rekanan) Nama Pemilik Rekening</label>
                        <div class="col-md-10">
                            <input type="text" readonly name="no_rekening" value="{{ $data->nm_rekening }}"
                                id="no_rekening" class="form-control">
                        </div>
                    </div>
                    <!-- No Rekening -->
                    <div class="mb-3 row">
                        <label for="no_rekening" class="col-md-2 col-form-label">No Rekening</label>
                        <div class="col-md-10">
                            <input type="text" readonly name="no_rekening" value="{{ $data_rekening->rekening }}"
                                id="no_rekening" class="form-control">
                        </div>
                    </div>
                    <!-- NPWP -->
                    <div class="mb-3 row">
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-10">
                            <input type="text" readonly name="npwp" value="{{ $data_rekening->npwp }}"
                                id="npwp" class="form-control">
                        </div>
                    </div>
                    <!-- Nilai Kontrak -->
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai Kontrak</label>
                        <div class="col-md-10">
                            <input type="text" readonly value="{{ $data->nilai }}" name="nilai" id="nilai"
                                class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
