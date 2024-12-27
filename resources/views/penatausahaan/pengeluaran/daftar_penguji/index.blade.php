@extends('template.app')
@section('title', 'Daftar Penguji | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('daftar_penguji.create') }}" id="tambah_sp2d" class="btn btn-primary"
                        style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="daftar_penguji" class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">No Penguji</th>
                                        <th style="width: 50px;text-align:center">Tanggal</th>
                                        <th style="width: 150px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($daftar_penguji->chunk(5) as $data)
                                        @foreach ($data as $penguji)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $penguji->no_uji }}</td>
                                                <td>{{ tanggal($penguji->tgl_uji) }}</td>
                                                <td style="text-align: center">
                                                    <a href="{{ route('daftar_penguji.tampil', $penguji->no_uji) }}"
                                                        class="btn btn-info btn-sm"><i class="uil-eye"></i></a>
                                                    <button type="button" onclick="hapusData('{{ $penguji->no_uji }}')"
                                                        class="btn btn-danger btn-sm"><i class="uil-trash"></i></button>
                                                    <button type="button" onclick="cetak('{{ $penguji->no_uji }}')"
                                                        class="btn btn-dark btn-sm"><i class="uil-print"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    {{-- modul cetak --}}
    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No Penguji --}}
                    <div class="mb-3 row">
                        <label for="no_uji" class="col-md-4 col-form-label">No Daftar Penguji</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control" id="no_uji" name="no_uji">
                        </div>
                    </div>
                    {{-- Penandatangan --}}
                    <div class="mb-3 row">
                        <label for="ttd" class="col-md-4 col-form-label">Penandatangan</label>
                        <div class="col-md-8">
                            <select name="ttd" class="form-control" id="ttd">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd1 as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nama }} | {{ $ttd->jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-dark btn-md cetak_penguji"
                                data-jenis="layar"><b>Layar</b></button>
                            <button type="button" class="btn btn-md lampiran orange cetak_penguji"
                                data-jenis="pdf"><b>PDF</b></button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal"><b>Tutup</b></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <style>
        .orange {
            background-color: #FFA500;
            color: white
        }
    </style>
    @include('penatausahaan.pengeluaran.daftar_penguji.js.cetak')
@endsection
