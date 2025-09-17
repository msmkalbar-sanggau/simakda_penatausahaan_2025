@extends('template.app')
@section('title', 'Validasi LPJ UP/GU Unit | SIMAKDA')
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
                    <div class="mb-3 row">
                        @if ($lpj->status_validasi == '1')
                            <div class="alert alert-warning alert-block {{ $lpj->status_validasi == 1 ? '' : 'hidden' }}">
                                <b style="font-size:16px">LPJ sudah disetujui!</b>
                            </div>
                        @elseif ($lpj->status == '2')
                            <div class="alert alert-success {{ $lpj->status == 2 ? '' : 'hidden' }}">
                                <b style="font-size:16px">LPJ sudah dibuat SPP!</b>
                            </div>
                        @endif
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly value="{{ $lpj->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly value="{{ nama_skpd($lpj->kd_skpd) }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- NO LPJ dan Tanggal LPJ --}}
                    <div class="mb-3 row">
                        <label for="no_lpj" class="col-md-2 col-form-label">No. LPJ</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_lpj" name="no_lpj" required readonly value="{{ $lpj->no_lpj }}">
                        </div>
                        <label for="tgl_lpj" class="col-md-2 col-form-label">Tanggal LPJ</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_lpj" name="tgl_lpj" required readonly value="{{ $lpj->tgl_lpj }}">
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan" readonly>{{ $lpj->keterangan }}</textarea>
                        </div>
                    </div>
                    <!-- SETUJU ATAU BATAL SETUJU -->
                    <div class="mb-6 row" style="text-align;center">
                        <div class="col-md-12" style="text-align: center">
                            @if ($lpj->status == '1')
                                <button id="batal_setuju" class="btn btn-danger btn-md">BATAL SETUJU</button>
                            @elseif ($lpj->status == '2')
                                <button id="batal_setuju" class="btn btn-danger btn-md" disabled>BATAL SETUJU</button>
                            @else
                                <button id="setuju" class="btn btn-primary btn-md">SETUJU</button>
                            @endif
                            <a href="{{ route('lpj.validasi.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                                <th>Unit</th>
                                <th>No Bukti</th>
                                <th>Sub Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_lpj as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
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
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly class="form-control" id="total" name="total"
                                value="{{ rupiah($total_detail->nilai) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.lpj.validasi_lpj.js.edit');
@endsection
