@extends('template.app')
@section('title', 'Tambah SPD Belanja | SIMAKDA')
@section('content')

    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    SPD Belanja
                </div>
                <div class="card-body">
                    @csrf
                    <input id="idpage" name="idpage" type="hidden" value="{{ $idpage }}">
                    <input id="jenisbln" name="jenisbln" type="hidden" value="{{ $jenisblnspd->jenis_spd }}">
                    <!-- Kode SKPD dan Nama SKPD -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="kd_skpd">Kode SKPD</label>
                                <select type="text" class="form-control select2-multiple" style="width: 100%"
                                    id="kd_skpd" name="kd_skpd"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="nm_skpd">Nama SKPD</label>
                                <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" readonly />
                            </div>
                        </div>
                    </div>

                    <!-- nip dan nama skpd dan beban-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="nip">NIP SKPD</label>
                                <select type="text" class="form-control select2-multiple" style="width: 100%"
                                    id="nip" name="nip"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="nama_bend">Nama Kepala SKPD</label>
                                <input type="text" class="form-control" id="nama_bend" name="nama_bend" readonly />
                            </div>
                        </div>
                    </div>

                    <!-- no spd dan tgl spd -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="no_spd">No SPD
                                    <small style="color: red;">*Format nomor SPD : 61.03/01.0/XXXXXX/KODE
                                        SKPD/M/1/2024</small>
                                </label>
                                <input type="text" class="form-control" id="nomor" name="nomor">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="tanggal">Tanggal SPD</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <!-- periode bulan dan jenis beban -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label" for="bulan_awal">Periode Bulan Awal</label>
                                <select class="form-control select2-multiple" style="width: 100%" name="bulan_awal"
                                    id="bulan_awal">
                                    <option value=""></option>
                                    @foreach (getMonths() as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label" for="bulan_akhir">Periode Bulan Akhir</label>
                                <select class="form-control select2-multiple" style="width: 100%" name="bulan_akhir"
                                    id="bulan_akhir">
                                    <option value=""></option>
                                    @foreach (getMonths() as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="jenis">Beban</label>
                                <select class="form-control select2-multiple" style="width: 100%" name="jenis"
                                    id="jenis">
                                    <option value=""></option>
                                    <option value="5">Belanja</option>
                                    <option value="6">Pembiayaan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label" for="revisi">Jenis SPD</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="revisi" name="revisi">
                                    <label class="form-check-label" for="revisi">Revisi</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- jenis anggaran dan status angkas -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="jenis_anggaran">Jenis Anggaran</label>
                                <select type="text" class="form-control select2-multiple" style="width: 100%"
                                    id="jenis_anggaran" name="jenis_anggaran"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="status_angkas">Status Angkas</label>
                                <select type="text" class="form-control" style="width: 100%" id="status_angkas"
                                    name="status_angkas"></select>
                            </div>
                        </div>
                    </div>
                    <!-- keterangan -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="keterangan">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_spd" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('spd_belanja.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- belanja -->
    <div class="row belanja">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Daftar Kegiatan dan Rekening Anggaran Kas
                    <button id="insert-all" class="btn btn-primary" style="float: right;">Tambah Semua</button>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="spd_belanja" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;text-align:center">Kode Unit</th>
                                        <th style="width: 100px;text-align:center">Kode Sub Kegiatan</th>
                                        <th style="width: 100px;text-align:center">Kode Rekening</th>
                                        <th style="width: 100px;text-align:center">SPD Ini</th>
                                        <th style="width: 100px;text-align:center">SPD Lalu</th>
                                        <th style="width: 100px;text-align:center">Anggaran</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <div class="row belanja">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rincian SPD
                    <button id="delete-all" class="btn btn-danger" style="float: right;">Hapus Semua</button>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="spd_belanja_temp" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;text-align:center">Kode Unit</th>
                                        <th style="width: 100px;text-align:center">Kode Sub Kegiatan</th>
                                        <th style="width: 100px;text-align:center">Kode Rekening</th>
                                        <th style="width: 100px;text-align:center">SPD Ini</th>
                                        <th style="width: 100px;text-align:center">SPD Lalu</th>
                                        <th style="width: 100px;text-align:center">Anggaran</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="mb-2 mt-2 row">
                            <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total
                                SPD</label>
                            <div class="col-md-4">
                                <input type="text" style="text-align: right" readonly class="form-control"
                                    id="total" name="total">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loading" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <img src='{{ asset('template/loading.gif') }}' width='100%' height='200px'>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.spd.spd_belanja.js.create')
@endsection
