@extends('template.app')
@section('title', 'Setor Sisa Kas/CP | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List STS
                    <a href="{{ route('skpd.setor_sisa.create') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                    <button data-toggle="modal" data-target="#cetakcp" id="cetakcp" class="btn btn-success"
                        style="float: right; margin-right:4px;"><i class="fa fa-print"></i> Register CP</button>

                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="sisa_kas" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Nomor STS</th>
                                        <th style="width: 50px;text-align:center">Tanggal STS</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalcetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label for="modalcetak" id="modalcetak">Cetak Register CP</label></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- SKPD --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-6">
                            <label for="kd_skpd" class="form-label">SKPD</label>
                            <input type="text" class="form-control select2-modal" name="kd_skpd" id="kd_skpd"
                                value="{{ $skpd1->kd_skpd }}" disabled>
                        </div>
                        {{-- Nama SKPD --}}
                        <div class="col-md-6">
                            <label for="nm_skpd" class="form-label">
                                Nama</label>
                            <input type="text" class="form-control select2-modal" name="nm_skpd" id="nm_skpd"
                                value="{{ $skpd1->nm_skpd }}" disabled>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        {{-- Periode --}}
                        <div class="col-md-3">
                            <label for="tgl1" class="form-label">Periode</label>
                            <input type="date" id="tgl1" name="tgl1" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="tgl2" class="form-label">&nbsp;</label>
                            <input type="date" id="tgl2" name="tgl2" class="form-control">
                        </div>
                        {{-- Tanggal TTD --}}
                        <div class="col-md-6">
                            <label for="tgl_ttd" class="form-label">Tanggal TTD</label>
                            <input type="date" id="tgl_ttd" name="tgl_ttd" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        {{-- Bendahara Pengeluaran --}}
                        <div class="col-md-6">
                            <label for="ttdb" class="form-label">Bendahara Pengeluaran</label>
                            <select class="form-control select2-modal @error('ttdb') is-invalid @enderror"
                                style=" width: 100%;" id="ttdb" name="ttdb">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($bend as $ttdbend)
                                    <option value="{{ $ttdbend->nip }}" data-nama="{{ $ttdbend->nama }}">
                                        {{ $ttdbend->nip }} | {{ $ttdbend->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ttda" class="form-label">Pengguna Anggaran</label>
                            <select class="form-control select2-modal @error('ttda') is-invalid @enderror"
                                style=" width: 100%;" id="ttda" name="ttda">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($pa as $ttdpa)
                                    <option value="{{ $ttdpa->nip }}" data-nama="{{ $ttdpa->nama }}">
                                        {{ $ttdpa->nip }} | {{ $ttdpa->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        {{-- Cetakan --}}
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md permintaan_layar" data-jenis="pdf"
                                name="permintaan_pdf">
                                PDF</button>
                            <button type="button" class="btn btn-dark btn-md permintaan_layar" data-jenis="layar"
                                name="permintaan_layar">Layar</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.setor_sisa_kas.js.index')
@endsection
