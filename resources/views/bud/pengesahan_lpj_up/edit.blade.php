@extends('template.app')
@section('title', 'Pengesahan LPJ UP/GU | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    INPUT LPJ
                </div>
                <div class="card-body">
                    @csrf
                    @if ($lpj->status == '1')
                        <div class="alert alert-danger" role="alert">
                            SUDAH DISETUJUI
                        </div>
                    @elseif ($lpj->status == '2')
                        <div class="alert alert-danger" role="alert">
                            SUDAH DIBUAT SPP
                        </div>
                    @endif
                    {{-- SETUJU DAN BATAL SETUJU --}}
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <a href="{{ route('lpj.validasi.index') }}" class="btn btn-warning btn-md">Kembali</a>
                            @if ($lpj->status == '1' || $lpj->status == '2')
                                <button class="btn btn-md btn-danger" id="batal_setuju" value="0">BATAL SETUJU</button>
                            @else
                                <button class="btn btn-md btn-primary" id="setuju" style="border: 1px solid black"
                                    value="1">SETUJU</button>
                            @endif
                        </div>
                    </div>
                    {{-- No dan Tanggal LPJ --}}
                    <div class="mb-3 row">
                        <label for="no_lpj" class="col-md-2 col-form-label">No. LPJ</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_lpj" name="no_lpj"
                                value="{{ $lpj->no_lpj }}" required readonly>
                        </div>
                        <label for="tgl_lpj" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_lpj" name="tgl_lpj" required
                                value="{{ $lpj->tgl_lpj }}" readonly>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $lpj->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $lpj->nm_skpd }}">
                        </div>
                    </div>
                    {{-- No SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">No SP2D</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_sp2d" name="no_sp2d" required readonly
                                value="{{ $lpj->no_sp2d }}">
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan" readonly>{{ $lpj->keterangan }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Detail LPJ --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Detail LPJ
                </div>
                <div class="card-body table-responsive">
                    <table id="detail_lpj" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>UNIT</th>
                                <th>No Bukti</th>
                                <th>Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Jumlah</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total" name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('bud.pengesahan_lpj_up.js.edit');
@endsection
