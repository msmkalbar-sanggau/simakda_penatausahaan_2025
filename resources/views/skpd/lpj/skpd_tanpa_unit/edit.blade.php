@extends('template.app')
@section('title', 'Ubah LPJ UP/GU (SKPD Tanpa Unit) | SIMAKDA')
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
                    {{-- SKPD dan Nilai UP --}}
                    <div class="mb-3 row">
                        @if ($lpj->status == '1')
                            <div class="alert alert-warning alert-block {{ $lpj->status == 1 ? '' : 'hidden' }}">
                                <b style="font-size:16px">LPJ sudah disetujui!</b>
                            </div>
                        @elseif ($lpj->status == '2')
                            <div class="alert alert-success {{ $lpj->status == 2 ? '' : 'hidden' }}">
                                <b style="font-size:16px">LPJ sudah dibuat SPP!</b>
                            </div>
                        @endif
                    </div>
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $lpj->kd_skpd }}">
                        </div>
                        <label for="nilai_up" class="col-md-2 col-form-label">Nilai UP</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nilai_up" name="nilai_up"
                                value="{{ rupiah($nilai_up->nilai) }}" required readonly style="text-align: right">
                        </div>
                    </div>
                    {{-- NAMA SKPD dan Nilai Minimal GU --}}
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ nama_skpd($lpj->kd_skpd) }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                        <label for="nilai_min_gu" class="col-md-2 col-form-label">Nilai Minimal GU</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nilai_min_gu" name="nilai_min_gu"
                                value="{{ rupiah($nilai_up->nilai * 0.75) }}" required readonly style="text-align: right">
                        </div>
                    </div>
                    {{-- NO LPJ dan Nilai LPJ --}}
                    <div class="mb-3 row">
                        <label for="no_lpj" class="col-md-2 col-form-label">No. LPJ</label>
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <input type="number" id="no_lpj" class="form-control" min="0"
                                    value="{{ $nomor }}">
                                <div class="input-group-prepend">
                                    <input type="text" value="/LPJ/UPGU/{{ $skpd->kd_skpd }}/{{ tahun_anggaran() }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <label for="nilai_lpj" class="col-md-2 col-form-label">Nilai LPJ</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nilai_lpj" name="nilai_lpj" required readonly
                                value="{{ rupiah($total_detail->nilai) }}" style="text-align: right">
                        </div>
                    </div>
                    {{-- NO LPJ Tersimpan dan Persentase --}}
                    <div class="mb-3 row">
                        <label for="no_lpj_simpan" class="col-md-2 col-form-label">No. LPJ Tersimpan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_lpj_simpan" name="no_lpj_simpan" required
                                readonly value="{{ $lpj->no_lpj }}">
                        </div>
                        <label for="persentase" class="col-md-2 col-form-label">Persentase</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="persentase" name="persentase" required readonly
                                style="text-align: right"
                                value="{{ $total_detail->nilai == 0 ? '0%' : rupiah(($total_detail->nilai / $nilai_up->nilai) * 100) . '%' }}">
                            <small>(minimal GU yang diajukan adalah 75% dari nilai UP)</small>
                        </div>
                    </div>
                    {{-- Tanggal LPJ dan SPD --}}
                    <div class="mb-3 row">
                        <label for="tgl_lpj" class="col-md-2 col-form-label">Tanggal LPJ</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_lpj" name="tgl_lpj" required
                                value="{{ $lpj->tgl_lpj }}">
                        </div>
                        <label for="total_spd" class="col-md-2 col-form-label">SPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="total_spd" name="total_spd" required readonly
                                value="{{ rupiah($spd_global->spd) }}" style="text-align: right">
                        </div>
                    </div>
                    {{-- Keterangan dan Realisasi SPD --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-4">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan">{{ $lpj->keterangan }}</textarea>
                        </div>
                        <label for="realisasi_spd" class="col-md-2 col-form-label">Realisasi SPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="realisasi_spd" name="realisasi_spd" required
                                readonly value="{{ rupiah($spd_global->transaksi) }}" style="text-align: right">
                        </div>
                    </div>
                    {{-- Sisa SPD --}}
                    <div class="mb-3 row">
                        <label class="col-md-6 col-form-label"></label>
                        <label for="sisa_spd" class="col-md-2 col-form-label">Sisa SPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="sisa_spd" name="sisa_spd" required readonly
                                value="{{ rupiah($spd_global->sisa_spd) }}" style="text-align: right">
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-6 row" style="text-align;center">
                        <div class="col-md-12" style="text-align: center">
                            @if ($lpj->status == '0')
                                <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            @endif
                            <a href="{{ route('lpj.skpd_tanpa_unit.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                <div class="card-body">
                    <div class="mb-3 row">
                        <label for="tgl_transaksi" class="col-md-12 col-form-label">Tanggal Transaksi</label>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="tgl_awal" value="{{ $lpj->tgl_awal }}"
                                readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="tgl_akhir" value="{{ $lpj->tgl_akhir }}"
                                readonly>
                        </div>
                        <div class="col-md-8">
                            <button class="btn btn-success" id="tampilkan" disabled><i class="uil-eye"></i>
                                Tampilkan</button>
                            <button href="#" class="btn btn-success" id="kosongkan" disabled><i
                                    class="uil-trash"></i>
                                Kosongkan</button>
                        </div>
                    </div>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_lpj as $detail)
                                <tr>
                                    <td>{{ $detail->kd_skpd }}</td>
                                    <td>{{ $detail->no_bukti }}</td>
                                    <td>{{ $detail->kd_sub_kegiatan }}</td>
                                    <td>{{ $detail->kd_rek6 }}</td>
                                    <td>{{ $detail->nm_rek6 }}</td>
                                    <td>{{ rupiah($detail->nilai) }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            onclick="hapus({{ $detail->no_bukti }},{{ $detail->kd_rek6 }},{{ $detail->nilai }})"
                                            class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="jumlah_spd" class="col-md-8 col-form-label" style="text-align: right">Total
                            SPD</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="jumlah_spd" name="jumlah_spd">
                        </div>
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Realisasi SPD
                            SPP</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="realisasi_spd_spp" name="realisasi_spd_spp">
                        </div>
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total" name="total"
                                value="{{ rupiah($total_detail->nilai) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.lpj.skpd_tanpa_unit.js.edit');
@endsection
