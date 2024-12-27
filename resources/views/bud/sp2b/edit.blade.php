@extends('template.app')
@section('title', 'Input SP2B | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Edit SP2B
                </div>
                <div class="card-body">
                    @csrf
                    {{-- SP2B --}}
                    <div class="mb-3 row">
                        <label for="no_sp2b" class="col-md-2 col-form-label">No SP2B</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_sp2b" name="no_sp2b"
                                value="{{ $datasp2b->no_sp2b }}">
                            <p style="color: red"><b>XXX/SP2B/RSUD-BLUD/2023</b></p>
                        </div>
                        <label for="tgl_sp2b" class="col-md-2 col-form-label">Tanggal SP2B</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_sp2b" name="tgl_sp2b"
                                value="{{ $datasp2b->tgl_sp2b }}">
                            <input type="text" class="form-control" id="bulan" name="bulan"
                                value="{{ $datasp2b->bulan }}" hidden>
                        </div>
                    </div>
                    {{-- SP3B --}}
                    {{-- <div class="mb-3 row">
                        <label for="no_sp3b" class="col-md-2 col-form-label">No SP3B</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_sp3b" name="no_sp3b"
                                value="{{ $datasp2b->no_sp3b }}">
                            <p style="color: red"><b>XXX/SP2B/RSUD-BLUD/2023</b></p>
                        </div>
                        <label for="tgl_sp3b" class="col-md-2 col-form-label">Tanggal SP3B</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_sp3b" name="tgl_sp3b"
                                value="{{ $datasp2b->tgl_sp3b }}">
                        </div>
                    </div> --}}
                    {{-- SKPD --}}
                    <div class="mb-3 row">
                        <label for="skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $datasp2b->kd_skpd }}">
                        </div>
                        <label for="skpd" class="col-md-2 col-form-label">NAMA SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $datasp2b->nm_skpd }}">
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-4">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"
                                value="{{ $datasp2b->keterangan }}">{{ $datasp2b->keterangan }}</textarea>
                        </div>
                        <label for="revisi" class="col-md-2 col-form-label">Jenis SP2B</label>
                        <div class="col-md-4">
                            <input type="checkbox" class="form-check-input" disabled id="revisi" name="revisi"
                                {{ $datasp2b->revisi_ke == '0' ? '' : 'checked' }}>
                            <label class="form-check-label" for="revisi">Revisi</label>
                        </div>
                    </div>
                    {{--  SIMPAN --}}
                    <div class="mb-6 row" style="text-align;center">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_edit" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('sp2b.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Detail LPJ --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Detail SP3B
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <label for="tgl_transaksi" class="col-md-12 col-form-label">Tanggal Transaksi</label>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="tgl_awal" name="tgl_awal"
                                value="{{ $datasp2b->tgl_awal }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir"
                                value="{{ $datasp2b->tgl_akhir }}">
                        </div>
                        <div class="col-md-8">
                            <button class="btn btn-success" id="tampilkan"><i class="uil-eye"></i>
                                Tampilkan</button>
                            <button href="#" class="btn btn-success" id="kosongkan"><i class="uil-trash"></i>
                                Kosongkan</button>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="detail_sp3b" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Kode Sub Kegiatan</th>
                                <th>Kode Rek 6</th>
                                <th>Nama Rekening 6</th>
                                <th>Nilai</th>
                                {{-- <th>Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail as $detailsp2b)
                                <tr>
                                    <td>{{ $detailsp2b->kd_sub_kegiatan }}</td>
                                    <td>{{ $detailsp2b->kd_rek6 }}</td>
                                    <td>{{ $detailsp2b->nm_rek6 }}</td>
                                    <td>{{ rupiah($detailsp2b->nilai) }}</td>
                                    {{-- <td>
                                    <a href="javascript:void(0);"
                                        onclick="hapus({{ $detailsp2b->kd_sub_kegiatan }},{{ $detailsp2b->kd_rek6 }})"
                                        class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>
                                 </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total
                            Saldo</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total" name="total"
                                value="{{ rupiah($datasp2b->total) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loading" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <img src='{{ asset('template/loading.gif') }}' width='100%' height='200px'>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('bud.sp2b.js.edit');
@endsection
