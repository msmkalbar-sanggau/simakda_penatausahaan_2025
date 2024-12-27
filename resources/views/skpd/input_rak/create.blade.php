@extends('template.app')
@section('title', 'Input RAK | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rencana Anggaran Kas Belanja Sub Rincian Objek
                </div>
                <div class="card-body">
                    @csrf
                    {{-- OPD/Unit --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">OPD/UNIT</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($skpd as $kode)
                                    <option value="{{ $kode->kd_skpd }}" data-nama="{{ $kode->nm_skpd }}">
                                        {{ $kode->kd_skpd }} | {{ $kode->nm_skpd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="nm_skpd" id="nm_skpd" readonly
                                style="border:none;background-color:white">
                            <input type="text" class="form-control" id="username" readonly hidden
                                value="{{ $username }}">
                        </div>
                    </div>
                    {{-- Jenis Anggaran --}}
                    <div class="mb-3 row">
                        <label for="jenis_anggaran" class="col-md-2 col-form-label">Jenis Anggaran</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis_anggaran"
                                name="jenis_anggaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    {{-- Jenis RAK --}}
                    <div class="mb-3 row">
                        <label for="jenis_rak" class="col-md-2 col-form-label">Jenis RAK</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis_rak"
                                name="jenis_rak">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    {{-- Sub Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="nm_sub_kegiatan" id="nm_sub_kegiatan" readonly
                                style="border:none;background-color:white">
                        </div>
                    </div>
                    {{-- Nilai Anggaran --}}
                    <div class="mb-3 row">
                        <label for="nilai_anggaran" class="col-md-2 col-form-label">Nilai Anggaran</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="nilai_anggaran" id="nilai_anggaran"
                                style="text-align: right" readonly style="text-align:right">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Perhatian --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body table-responsive bg-info">
                    <table class="table" style="width: 100%;color:white">
                        <tbody>
                            <tr>
                                <td>Perhatian!!!</td>
                            </tr>
                            <tr>
                                <td>
                                    <ul>
                                        <li>Setelah selesai melakukan pengisian rencana anggaran kas (RAK) diharapkan untuk
                                            mengesahkan RAK sesuai jenis RAK</li>
                                        <li>Setelah RAK disahkan, diharapkan segera membuat SPD dengan dasar RAK yang sudah
                                            disahkan</li>
                                        <li>Jika terdapat perubahan atau pergeseran rencana anggaran kas (RAK), segera
                                            perbaharui pengesahan RAK dan SPD</li>
                                        <li>Pastikan jumlah anggaran kas Global (Dinas dan Unit) sama dengan jumlah SPD</li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- Rekening RAK --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rekening RAK
                </div>
                <div class="card-body table-responsive">
                    <table id="rekening_rak" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai Anggaran</th>
                                <th>Nilai RAK</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="detail_rak" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Kode Sub Kegiatan dan Kode Rekening -->
                    <div class="mb-3 row">
                        <label for="kode_sub_kegiatan" class="col-md-2 col-form-label">Kode Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="kode_sub_kegiatan" readonly>
                        </div>
                        <label for="kode_rekening" class="col-md-2 col-form-label">Kode Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="kode_rekening" readonly>
                        </div>
                    </div>
                    {{-- Nama Sub Kegiatan dan Nama Rekening --}}
                    <div class="mb-3 row">
                        <label for="nama_sub_kegiatan" class="col-md-2 col-form-label">Nama Sub Kegiatan</label>
                        <div class="col-md-4">
                            <textarea id="nama_sub_kegiatan" class="form-control" readonly></textarea>
                        </div>
                        <label for="nama_rekening" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-4">
                            <textarea id="nama_rekening" class="form-control" readonly></textarea>
                        </div>
                    </div>
                    {{-- Anggaran Sub Kegiatan dan Anggaran Rekening --}}
                    <div class="mb-3 row">
                        <label for="anggaran_sub_kegiatan" class="col-md-2 col-form-label">Anggaran Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="anggaran_sub_kegiatan" readonly
                                style="text-align: right">
                        </div>
                        <label for="anggaran_rekening" class="col-md-2 col-form-label">Anggaran Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="anggaran_rekening" readonly
                                style="text-align: right">
                        </div>
                    </div>
                    <hr>
                    {{-- RAK Terinput dan RAK Belum Terinput --}}
                    <div class="mb-3 row">
                        <label for="rak_terinput" class="col-md-2 col-form-label">RAK Terinput</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rak_terinput" readonly
                                style="text-align: right">
                        </div>
                        <label for="rak_belum_terinput" class="col-md-2 col-form-label">RAK Belum Terinput</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rak_belum_terinput" readonly
                                style="text-align: right">
                        </div>
                    </div>
                    <hr>
                    {{-- Triwulan I dan Triwulan II --}}
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Triwulan I</div>
                                <div class="card-body">
                                    <table class="table" id="triwulan1">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>RAK</th>
                                                <th>Realisasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Januari</td>
                                                <td><input type="text" class="form-control tw" id="rak_januari"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_januari"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Februari</td>
                                                <td><input type="text" class="form-control tw" id="rak_februari"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_februari"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Maret</td>
                                                <td><input type="text" class="form-control tw" id="rak_maret"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_maret"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td><input type="text" class="form-control tw" id="total_rak_tw1"
                                                        readonly></td>
                                                <td><input type="text" class="form-control tw"
                                                        id="total_realisasi_tw1" readonly></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Triwulan II</div>
                                <div class="card-body">
                                    <table class="table" id="triwulan2">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>RAK</th>
                                                <th>Realisasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>April</td>
                                                <td><input type="text" class="form-control tw" id="rak_april"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_april"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Mei</td>
                                                <td><input type="text" class="form-control tw" id="rak_mei"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_mei"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Juni</td>
                                                <td><input type="text" class="form-control tw" id="rak_juni"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_juni"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td><input type="text" class="form-control tw" id="total_rak_tw2"
                                                        readonly>
                                                </td>
                                                <td><input type="text" class="form-control tw"
                                                        id="total_realisasi_tw2" readonly></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Triwulan III dan Triwulan IV --}}
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Triwulan III</div>
                                <div class="card-body">
                                    <table class="table" id="triwulan3">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>RAK</th>
                                                <th>Realisasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Juli</td>
                                                <td><input type="text" class="form-control tw" id="rak_juli"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_juli"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Agustus</td>
                                                <td><input type="text" class="form-control tw" id="rak_agustus"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_agustus"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td>September</td>
                                                <td><input type="text" class="form-control tw" id="rak_september"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw"
                                                        id="realisasi_september" readonly></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td><input type="text" class="form-control tw" id="total_rak_tw3"
                                                        readonly></td>
                                                <td><input type="text" class="form-control tw"
                                                        id="total_realisasi_tw3" readonly></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Triwulan IV</div>
                                <div class="card-body">
                                    <table class="table" id="triwulan4">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>RAK</th>
                                                <th>Realisasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Oktober</td>
                                                <td><input type="text" class="form-control tw" id="rak_oktober"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_oktober"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td>November</td>
                                                <td><input type="text" class="form-control tw" id="rak_november"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_november"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Desember</td>
                                                <td><input type="text" class="form-control tw" id="rak_desember"
                                                        data-type="currency" onkeyup="hitung()"></td>
                                                <td><input type="text" class="form-control tw" id="realisasi_desember"
                                                        readonly></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td><input type="text" class="form-control tw" id="total_rak_tw4"
                                                        readonly></td>
                                                <td><input type="text" class="form-control tw"
                                                        id="total_realisasi_tw4" readonly></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- info --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <p id="informasi" hidden>Inputan dikunci!! Anggaran Kas Sudah disahkan</p>
                        </div>
                    </div>
                    {{-- SIMPAN --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_detail" class="btn btn-md btn-primary">Simpan</button>
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
    @include('skpd.input_rak.js.create');
@endsection
