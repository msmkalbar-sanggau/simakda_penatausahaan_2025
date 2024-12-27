@extends('template.app')
@section('title', 'Tambah Upload Setor CMS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data Setor CMS
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Data Transaksi --}}
                    <div class="mb-3 row">
                        <label for="data_upload" class="col-md-12 col-form-label">Data Setor CMS</label>
                        <div class="col-md-12">
                            <select name="data_upload" id="data_upload" class="form-control select2-multiple">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_upload as $upload)
                                    <option value="{{ $upload->no_kas }}" data-tgl_kas="{{ $upload->tgl_kas }}"
                                        data-kd_skpd="{{ $upload->kd_skpd }}" data-nilai="{{ $upload->nilai }}"
                                        data-jenis_spp="{{ $upload->jenis_spp }}"
                                        data-keterangan="{{ $upload->keterangan }}"
                                        data-kd_skpd_sumber="{{ $upload->kd_skpd_sumber }}"
                                        data-rekening_awal="{{ $upload->rekening_awal }}"
                                        data-nm_rekening_tujuan="{{ $upload->nm_rekening_tujuan }}"
                                        data-rekening_tujuan="{{ $upload->rekening_tujuan }}"
                                        data-bank_tujuan="{{ $upload->bank_tujuan }}"
                                        data-ket_tujuan="{{ $upload->ket_tujuan }}"
                                        data-status_upload="{{ $upload->status_upload }}"
                                        data-tgl_upload="{{ $upload->tgl_upload }}"
                                        data-lpj_unit="{{ $upload->lpj_unit }}">
                                        {{ $upload->no_kas }} |
                                        {{ $upload->tgl_kas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="proses_upload" class="btn btn-primary btn-md"><i class="uil-search-alt"></i>Proses
                            Upload</button>
                        <a href="{{ route('skpd.upload_setor.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                                        readonly id="total_upload">
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
    @include('skpd.upload_setor_cms.js.create');
@endsection
