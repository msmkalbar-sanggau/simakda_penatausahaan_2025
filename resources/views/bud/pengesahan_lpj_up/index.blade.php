@extends('template.app')
@section('title', 'Pengesahan LPJ UP/GU | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Data LPJ UP/GU
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="pengesahan_lpj" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">No LPJ</th>
                                        <th style="width: 50px;text-align:center">Tanggal</th>
                                        <th style="width: 50px;text-align:center">Nama SKPD</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
                                        <th style="width: 50px;text-align:center">Status</th>
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

    {{-- modul cetak --}}
    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak LPJ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No. LPJ --}}
                    <div class="mb-3 row">
                        <label for="no_lpj" class="col-md-2 col-form-label">No. LPJ</label>
                        <div class="col-md-10">
                            <input type="text" readonly class="form-control" id="no_lpj" name="no_lpj">
                            <input type="text" hidden class="form-control" id="jenis" name="jenis">
                            <input type="text" hidden class="form-control" id="kd_skpd" name="kd_skpd">
                        </div>
                    </div>
                    {{-- Penandatangan --}}
                    <div class="mb-3 row">
                        <label for="ttd" class="col-md-2 col-form-label">Penandatangan</label>
                        <div class="col-md-10">
                            <select name="ttd" class="form-control select-modal" id="ttd">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd1 as $ttd)
                                    <option value="{{ $ttd->nip }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Pilihan --}}
                    <div class="mb-3 row">
                        <label for="pilihan" class="col-md-2 col-form-label">Pilihan</label>
                        <div class="col-md-10">
                            <select name="pilihan" class="form-control select-modal" id="pilihan">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="0">Rinci</option>
                                <option value="1">Rekap Rincian</option>
                                <option value="2">Rincian Perkegiatan</option>
                            </select>
                        </div>
                    </div>
                    {{-- Sub Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-10">
                            <select name="kd_sub_kegiatan" class="form-control select-modal" id="kd_sub_kegiatan">
                                <option value="" selected disabled>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    {{-- Cetak --}}
                    <div class="mb-3 row">
                        <label for="cetak" class="col-md-2 col-form-label">Cetak</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md cetak" data-jenis="pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md cetak" data-jenis="layar">Layar</button>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('bud.pengesahan_lpj_up.js.index')
@endsection
