@extends('template.app')
@section('title', 'Tambah Validasi Panjar CMS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data Transaksi
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Tanggal Validasi --}}
                    <div class="mb-3 row">
                        <label for="tgl_validasi" class="col-md-12 col-form-label">Tanggal Validasi</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_validasi" name="tgl_validasi">
                        </div>
                    </div>
                    {{-- Data Transaksi --}}
                    <div class="mb-3 row">
                        <label for="data_transaksi" class="col-md-12 col-form-label">Data Transaksi</label>
                        <div class="col-md-12">
                            <select name="data_transaksi" id="data_transaksi" class="form-control select2-multiple">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                {{-- @foreach ($daftar_transaksi as $data)
                                    <option value="{{ $data->no_kas }}" data-tgl="{{ $data->tgl_kas }}"
                                        data-kd_skpd="{{ $data->kd_skpd }}" data-keterangan="{{ $data->keterangan }}"
                                        data-nilai="{{ $data->nilai }}" data-status_upload="{{ $data->status_upload }}"
                                        data-no_upload="{{ $data->no_upload }}"
                                        data-rekening_awal="{{ $data->rekening_awal }}"
                                        data-nm_rekening_tujuan="{{ $data->nm_rekening_tujuan }}"
                                        data-rekening_tujuan="{{ $data->rekening_tujuan }}"
                                        data-bank_tujuan="{{ $data->bank_tujuan }}"
                                        data-ket_tujuan="{{ $data->ket_tujuan }}">
                                        {{ $data->no_kas }} |
                                        {{ $data->tgl_kas }}
                                    </option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="proses_validasi" class="btn btn-primary btn-md"><i class="uil-search-alt"></i>Proses
                            Validasi</button>
                        <a href="{{ route('validasi_panjarcms.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Input Detail --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Data Validasi
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_validasi" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>Tanggal Bukti</th>
                                <th>SKPD</th>
                                <th>Keterangan</th>
                                <th>Nilai Pengeluaran</th>
                                <th>No Upload</th>
                                <th>Status Upload</th>
                                <th>Rekening Awal</th>
                                <th>Nama Rekening Tujuan</th>
                                <th>Rekening Tujuan</th>
                                <th>Bank Tujuan</th>
                                <th>Ket Tujuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                    <hr>
                    <table style="width: 100%">
                        <tbody>
                            <tr>
                                <td style="padding-left: 600px">Total Transaksi</td>
                                <td>:</td>
                                <td style="text-align: right"><input type="text"
                                        style="border:none;background-color:white;text-align:right" class="form-control"
                                        readonly id="total_transaksi">
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left: 600px">Total Potongan</td>
                                <td>:</td>
                                <td style="text-align: right"></td>
                            </tr>
                            <tr>
                                <td style="padding-left: 600px">Sisa Saldo Bank</td>
                                <td>:</td>
                                <td><input type="text" style="border:none;background-color:white;text-align:right"
                                        readonly id="sisa_saldo" class="form-control"
                                        value="{{ rupiah($sisa_bank->sisa) }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.validasi_panjar_cms.js.create');
@endsection
