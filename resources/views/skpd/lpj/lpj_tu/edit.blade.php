@extends('template.app')
@section('title', 'Input LPJ TU | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input LPJ
                </div>
                <div class="card-body">
                    @csrf
                    {{-- NO LPJ dan Tanggal LPJ --}}
                    <div class="mb-3 row">
                        <label for="no_lpj" class="col-md-2 col-form-label">No. LPJ</label>
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <input type="number" id="no_lpj" class="form-control" min="0"
                                    value="{{ $nomor }}">
                                <div class="input-group-prepend">
                                    <input type="text" value="/LPJ/TU/{{ $skpd->kd_skpd }}/{{ tahun_anggaran() }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <label for="tgl_lpj" class="col-md-2 col-form-label">Tanggal LPJ</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_lpj" name="tgl_lpj" required
                                value="{{ $lpj->tgl_lpj }}">
                            <input class="form-control" type="text" id="no_lpj_simpan" name="no_lpj_simpan" required
                                hidden readonly value="{{ $lpj->no_lpj }}">
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
                                value="{{ nama_skpd($lpj->kd_skpd) }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- No SP2D dan Tanggal SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_lpj_simpan" class="col-md-2 col-form-label">No. SP2D</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_sp2d" name="no_sp2d" required readonly
                                value="{{ $lpj->no_sp2d }}">
                        </div>
                        <label for="tgl_sp2d" class="col-md-2 col-form-label">Tanggal SP2D</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_sp2d" name="tgl_sp2d" required readonly
                                value="{{ $lpj->tgl_awal }}">
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan">{{ $lpj->keterangan }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-6 row" style="text-align;center">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('lpj_tu.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                                <th>Unit</th>
                                <th>No Bukti</th>
                                <th>Sub Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($detail_lpj as $detail)
                                @php
                                    $total += $detail->nilai;
                                @endphp
                                <tr>
                                    <td>{{ $detail->kd_skpd }}</td>
                                    <td>{{ $detail->no_bukti }}</td>
                                    <td>{{ $detail->kd_sub_kegiatan }}</td>
                                    <td>{{ $detail->kd_rek6 }}</td>
                                    <td>{{ $detail->nm_rek6 }}</td>
                                    <td>{{ rupiah($detail->nilai) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total" name="total" value="{{ rupiah($total) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.lpj.lpj_tu.js.edit');
@endsection
