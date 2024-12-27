@extends('template.app')
@section('title', 'Pengesahan Angkas | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Data Pengesahan Angkas
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="pengesahan_angkas" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Kode SKPD</th>
                                        <th style="width: 50px;text-align:center">Nama SKPD</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="detail_angkas" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Pengesahan Anggaran Kas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Kode Sub Kegiatan dan Kode Rekening -->
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="kd_skpd" readonly>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="nm_skpd" readonly
                                style="border: none;background-color:white">
                        </div>
                    </div>
                    {{-- Angkas Murni --}}
                    <div class="card">
                        <div class="card-body">
                            <label for="angkas_murni" class="col-md-12 col-form-label" style="color: red">Angkas
                                penetapan</label>
                            {{-- Murni dan Murni Geser I --}}
                            <div class="mb-1 row">
                                <label for="angkas_murni" class="col-md-2 col-form-label align-top">Penetapan</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_murni">
                                    </div>
                                </div>
                                <label for="angkas_murni_geser1" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">Penetapan Geser I</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_murni_geser1">
                                    </div>
                                </div>
                            </div>
                            {{-- Murni Geser II dan Murni Geser III --}}
                            <div class="mb-1 row">
                                <label for="angkas_murni_geser2" class="col-md-2 col-form-label align-top">Penetapan Geser
                                    II</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_murni_geser2">
                                    </div>
                                </div>
                                <label for="angkas_murni_geser3" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">Penetapan Geser III</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_murni_geser3">
                                    </div>
                                </div>
                            </div>
                            {{-- Murni Geser IV dan Murni Geser V --}}
                            <div class="mb-1 row">
                                <label for="angkas_murni_geser4" class="col-md-2 col-form-label align-top">Penetapan Geser
                                    IV</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_murni_geser4">
                                    </div>
                                </div>
                                <label for="angkas_murni_geser5" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">Penetapan Geser V</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_murni_geser5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Angkas Penyempurnaan I --}}
                    <div class="card">
                        <div class="card-body">
                            <label for="angkas_sempurna1" class="col-md-12 col-form-label" style="color: red">Angkas
                                Pergeseran I</label>
                            {{-- Sempurna 1 dan Sempurna 1 Geser I --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna1" class="col-md-2 col-form-label align-top">pergeseran 1</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna1">
                                    </div>
                                </div>
                                <label for="angkas_sempurna1_geser1" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 1 Geser I</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna1_geser1">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 1 Geser II dan Sempurna 1 Geser III --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna1_geser2" class="col-md-2 col-form-label align-top">pergeseran 1
                                    Geser II</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna1_geser2">
                                    </div>
                                </div>
                                <label for="angkas_sempurna1_geser3" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 1 Geser III</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna1_geser3">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 1 Geser IV dan Sempurna 1 Geser V --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna1_geser4" class="col-md-2 col-form-label align-top">pergeseran 1
                                    Geser IV</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna1_geser4">
                                    </div>
                                </div>
                                <label for="angkas_sempurna1_geser5" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 1 Geser V</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna1_geser5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Angkas Penyempurnaan II --}}
                    <div class="card">
                        <div class="card-body">
                            <label for="angkas_sempurna2" class="col-md-12 col-form-label" style="color: red">Angkas
                                Pergeseran II</label>
                            {{-- Sempurna 2 dan Sempurna 2 Geser I --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna2" class="col-md-2 col-form-label align-top">pergeseran 2</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna2">
                                    </div>
                                </div>
                                <label for="angkas_sempurna2_geser1" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 2 Geser I</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna2_geser1">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 2 Geser II dan Sempurna 2 Geser III --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna2_geser2" class="col-md-2 col-form-label align-top">pergeseran 2
                                    Geser II</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna2_geser2">
                                    </div>
                                </div>
                                <label for="angkas_sempurna2_geser3" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 2 Geser III</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna2_geser3">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 2 Geser IV dan Sempurna 2 Geser V --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna2_geser4" class="col-md-2 col-form-label align-top">pergeseran 2
                                    Geser IV</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna2_geser4">
                                    </div>
                                </div>
                                <label for="angkas_sempurna2_geser5" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 2 Geser V</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna2_geser5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Angkas Penyempurnaan III --}}
                    <div class="card">
                        <div class="card-body">
                            <label for="angkas_sempurna3" class="col-md-12 col-form-label" style="color: red">Angkas
                                Pergeseran III</label>
                            {{-- Sempurna 3 dan Sempurna 3 Geser I --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna3" class="col-md-2 col-form-label align-top">pergeseran 3</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna3">
                                    </div>
                                </div>
                                <label for="angkas_sempurna3_geser1" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 3 Geser I</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna3_geser1">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 3 Geser II dan Sempurna 3 Geser III --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna3_geser2" class="col-md-2 col-form-label align-top">pergeseran 3
                                    Geser II</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna3_geser2">
                                    </div>
                                </div>
                                <label for="angkas_sempurna3_geser3" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 3 Geser III</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna3_geser3">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 3 Geser IV dan Sempurna 3 Geser V --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna3_geser4" class="col-md-2 col-form-label align-top">pergeseran 3
                                    Geser IV</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna3_geser4">
                                    </div>
                                </div>
                                <label for="angkas_sempurna3_geser5" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 3 Geser V</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna3_geser5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Angkas Penyempurnaan IV --}}
                    <div class="card">
                        <div class="card-body">
                            <label for="angkas_sempurna4" class="col-md-12 col-form-label" style="color: red">Angkas
                                Pergeseran IV</label>
                            {{-- Sempurna 4 dan Sempurna 4 Geser I --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna4" class="col-md-2 col-form-label align-top">pergeseran 4</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna4">
                                    </div>
                                </div>
                                <label for="angkas_sempurna4_geser1" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 4 Geser I</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna4_geser1">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 4 Geser II dan Sempurna 4 Geser III --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna4_geser2" class="col-md-2 col-form-label align-top">pergeseran 4
                                    Geser II</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna4_geser2">
                                    </div>
                                </div>
                                <label for="angkas_sempurna4_geser3" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 4 Geser III</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna4_geser3">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 4 Geser IV dan Sempurna 4 Geser V --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna4_geser4" class="col-md-2 col-form-label align-top">pergeseran 4
                                    Geser IV</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna4_geser4">
                                    </div>
                                </div>
                                <label for="angkas_sempurna4_geser5" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 4 Geser V</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna4_geser5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Angkas Penyempurnaan V --}}
                    <div class="card">
                        <div class="card-body">
                            <label for="angkas_sempurna5" class="col-md-12 col-form-label" style="color: red">Angkas
                                Pergeseran V</label>
                            {{-- Sempurna 5 dan Sempurna 5 Geser I --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna5" class="col-md-2 col-form-label align-top">pergeseran 5</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna5">
                                    </div>
                                </div>
                                <label for="angkas_sempurna5_geser1" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 5 Geser I</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna5_geser1">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 5 Geser II dan Sempurna 5 Geser III --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna5_geser2" class="col-md-2 col-form-label align-top">pergeseran 5
                                    Geser II</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna5_geser2">
                                    </div>
                                </div>
                                <label for="angkas_sempurna5_geser3" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 5 Geser III</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna5_geser3">
                                    </div>
                                </div>
                            </div>
                            {{-- Sempurna 5 Geser IV dan Sempurna 5 Geser V --}}
                            <div class="mb-1 row">
                                <label for="angkas_sempurna5_geser4" class="col-md-2 col-form-label align-top">pergeseran 5
                                    Geser IV</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna5_geser4">
                                    </div>
                                </div>
                                <label for="angkas_sempurna5_geser5" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">pergeseran 5 Geser V</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_sempurna5_geser5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Angkas Perubahan --}}
                    <div class="card">
                        <div class="card-body">
                            <label for="angkas_ubah" class="col-md-12 col-form-label" style="color: red">Angkas
                                Perubahan</label>
                            {{-- Perubahan dan Perubahan Geser I --}}
                            <div class="mb-1 row">
                                <label for="angkas_ubah" class="col-md-2 col-form-label align-top">Perubahan</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah">
                                    </div>
                                </div>
                                <label for="angkas_ubah_geser1" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">Perubahan Geser I</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah_geser1">
                                    </div>
                                </div>
                            </div>
                            {{-- Perubahan Geser II dan Perubahan Geser III --}}
                            <div class="mb-1 row">
                                <label for="angkas_ubah_geser2" class="col-md-2 col-form-label align-top">Perubahan
                                    Geser II</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah_geser2">
                                    </div>
                                </div>
                                <label for="angkas_ubah_geser3" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">Perubahan Geser III</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah_geser3">
                                    </div>
                                </div>
                            </div>
                            {{-- Perubahan Geser IV dan Perubahan Geser V --}}
                            <div class="mb-1 row">
                                <label for="angkas_ubah_geser4" class="col-md-2 col-form-label align-top">Perubahan
                                    Geser IV</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah_geser4">
                                    </div>
                                </div>
                                <label for="angkas_ubah_geser5" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">Perubahan Geser V</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah_geser5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Angkas Perubahan II --}}
                    <div class="card">
                        <div class="card-body">
                            <label for="angkas_ubah2" class="col-md-12 col-form-label" style="color: red">Angkas
                                Pergeseran Perubahan I</label>
                            {{-- Perubahan 2 dan Perubahan 2 Geser I --}}
                            <div class="mb-1 row">
                                <label for="angkas_ubah2" class="col-md-2 col-form-label align-top">Pergeseran Perubahan 2</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah2">
                                    </div>
                                </div>
                                <label for="angkas_ubah2_geser1" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">Pergeseran Perubahan 2 Geser I</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah2_geser1">
                                    </div>
                                </div>
                            </div>
                            {{-- Perubahan 2 Geser II dan Perubahan 2 Geser III --}}
                            <div class="mb-1 row">
                                <label for="angkas_ubah2_geser2" class="col-md-2 col-form-label align-top">Pergeseran Perubahan 2
                                    Geser II</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah2_geser2">
                                    </div>
                                </div>
                                <label for="angkas_ubah2_geser3" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">Pergeseran Perubahan 2 Geser III</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah2_geser3">
                                    </div>
                                </div>
                            </div>
                            {{-- Perubahan 2 Geser IV dan Perubahan 2 Geser V --}}
                            <div class="mb-1 row">
                                <label for="angkas_ubah2_geser4" class="col-md-2 col-form-label align-top">Pergeseran Perubahan 2
                                    Geser IV</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah2_geser4">
                                    </div>
                                </div>
                                <label for="angkas_ubah2_geser5" class="col-md-2 col-form-label"
                                    style="vertical-align:text-top">Pergeseran Perubahan 2 Geser V</label>
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input type="checkbox" class="form-check-input" id="angkas_ubah2_geser5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- SIMPAN --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Kembali</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengesahan_angkas.js.index')
@endsection
