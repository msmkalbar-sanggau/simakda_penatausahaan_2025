@extends('template.app')
@section('title', 'Input LPJ TU | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List LPJ TU
                    <a href="{{ route('lpj_tu.tambah') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="lpj_tu" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">No LPJ</th>
                                        <th style="width: 100px;text-align:center">Tanggal LPJ</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
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
                    {{-- No. SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">No. SP2D</label>
                        <div class="col-md-10">
                            <input type="text" readonly class="form-control" id="no_sp2d" name="no_sp2d">
                        </div>
                    </div>
                    {{-- Tanggal TTD --}}
                    <div class="mb-3 row">
                        <label for="tgl_ttd" class="col-md-2 col-form-label">Tanggal TTD</label>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="tgl_ttd" name="tgl_ttd">
                        </div>
                    </div>
                    {{-- Bendahara --}}
                    <div class="mb-3 row">
                        <label for="bendahara" class="col-md-2 col-form-label">Bendahara Pengeluaran</label>
                        <div class="col-md-10">
                            <select name="bendahara" class="form-control select-modal" id="bendahara">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd1 as $bendahara)
                                    <option value="{{ $bendahara->nip }}">
                                        {{ $bendahara->nip }} | {{ $bendahara->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- PA/KPA --}}
                    <div class="mb-3 row">
                        <label for="pa_kpa" class="col-md-2 col-form-label">PA/KPA</label>
                        <div class="col-md-10">
                            <select name="pa_kpa" class="form-control select-modal" id="pa_kpa">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd2 as $pa)
                                    <option value="{{ $pa->nip }}">
                                        {{ $pa->nip }} | {{ $pa->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Cetak SPTB dan Cetak LPJ TU --}}
                    <div class="mb-3 row">
                        <label for="sptb" class="col-md-2 col-form-label">Cetak SPTB</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md sptb" data-jenis="pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md sptb" data-jenis="layar">Layar</button>
                        </div>
                        <label for="rincian" class="col-md-2 col-form-label">Cetak LPJ TU</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md rincian" data-jenis="pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md rincian" data-jenis="layar">Layar</button>
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
    @include('skpd.lpj.lpj_tu.js.index')
@endsection
