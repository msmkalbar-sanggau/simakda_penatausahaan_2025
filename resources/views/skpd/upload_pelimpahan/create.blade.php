@extends('template.app')
@section('title', 'Tambah Upload Pelimpahan UP/GU | SIMAKDA')
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
                    {{-- Data Transaksi --}}
                    <div class="mb-3 row">
                        <label for="data_pelimpahan" class="col-md-12 col-form-label">Data Pelimpahan</label>
                        <div class="col-md-12">
                            <select name="data_pelimpahan" id="data_pelimpahan" class="form-control select2-multiple">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_pelimpahan as $pelimpahan)
                                    <option value="{{ $pelimpahan->no_kas }}" data-tgl_kas="{{ $pelimpahan->tgl_kas }}"
                                        data-kd_skpd="{{ $pelimpahan->kd_skpd }}" data-nilai="{{ $pelimpahan->nilai }}"
                                        data-jenis_spp="{{ $pelimpahan->jenis_spp }}"
                                        data-keterangan="{{ $pelimpahan->keterangan }}"
                                        data-kd_skpd_sumber="{{ $pelimpahan->kd_skpd_sumber }}"
                                        data-rekening_awal="{{ $pelimpahan->rekening_awal }}"
                                        data-nm_rekening_tujuan="{{ $pelimpahan->nm_rekening_tujuan }}"
                                        data-rekening_tujuan="{{ $pelimpahan->rekening_tujuan }}"
                                        data-bank_tujuan="{{ $pelimpahan->bank_tujuan }}"
                                        data-ket_tujuan="{{ $pelimpahan->ket_tujuan }}"
                                        data-status_upload="{{ $pelimpahan->status_upload }}"
                                        data-tgl_upload="{{ $pelimpahan->tgl_upload }}">
                                        {{ $pelimpahan->no_kas }} |
                                        {{ $pelimpahan->tgl_kas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="proses_upload" class="btn btn-primary btn-md"><i class="uil-search-alt"></i>Proses
                            Upload</button>
                        <a href="{{ route('skpd.pelimpahan.upload') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Input Detail --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Data Upload
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_upload" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>Tanggal Bukti</th>
                                <th>SKPD</th>
                                <th>Keterangan</th>
                                <th>Total</th>
                                <th>Status Upload</th>
                                <th>Rekening Awal</th>
                                <th>Nama Rekening Tujuan</th>
                                <th>Rekening Tujuan</th>
                                <th>Bank Tujuan</th>
                                <th>Ket Tujuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <hr>
                    <table style="width: 100%">
                        <tbody>
                            <tr>
                                <td style="padding-left: 600px">Total</td>
                                <td>:</td>
                                <td style="text-align: right"><input type="text"
                                        style="border:none;background-color:white;text-align:right" class="form-control"
                                        readonly id="total_pelimpahan">
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
    @include('skpd.upload_pelimpahan.js.create');
@endsection
