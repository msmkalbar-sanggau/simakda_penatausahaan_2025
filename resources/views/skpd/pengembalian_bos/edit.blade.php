@extends('template.app')
@section('title', 'Edit Pengembalian BOS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Pengembalian BOS
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No. Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required
                                value="{{ $bos->no_sts }}">
                            <input class="form-control" type="text" id="no_simpan" name="no_simpan" required
                                value="{{ $bos->no_sts }}" hidden>
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_kas" name="tgl_kas" required
                                value="{{ $bos->tgl_sts }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- Kode dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $bos->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ nama_skpd($bos->kd_skpd) }}">
                        </div>
                    </div>
                    {{-- SATDIK dan Jenis Transaksi --}}
                    <div class="mb-3 row">
                        <label for="satdik" class="col-md-2 col-form-label">SATDIK</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="satdik" name="satdik">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1" data-nama="SMA/SMK NEGERI"
                                    {{ $bos->kd_satdik == 1 ? 'selected' : '' }}>SMA/SMK NEGERI</option>
                                <option value="2" data-nama="SMA/SMK SWASTA"
                                    {{ $bos->kd_satdik == 2 ? 'selected' : '' }}>SMA/SMK SWASTA</option>
                                <option value="3" data-nama="DIKSUS" {{ $bos->kd_satdik == 3 ? 'selected' : '' }}>
                                    DIKSUS</option>
                                </option>
                            </select>
                        </div>
                        <label for="jenis_transaksi" class="col-md-2 col-form-label">Jenis Transaksi</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis_transaksi"
                                name="jenis_transaksi">
                                <option value="1">Rekening Kas</option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Sub Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Pembayaran --}}
                    <div class="mb-3 row">
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="BNK" {{ $bos->bank == 'BNK' ? 'selected' : '' }}>BANK</option>
                                <option value="TNK" {{ $bos->bank == 'TNK' ? 'selected' : '' }}>TUNAI</option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-2 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan">{{ $bos->keterangan }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md"
                                {{ $bos->total_spb > 0 ? 'hidden' : '' }}>Simpan</button>
                            <a href="{{ route('pengembalian_bos.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail STS --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Detail STS
                    <button type="button" style="float: right" id="tambah_rincian"
                        class="btn btn-primary btn-md">Tambah Rekening</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="detail" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No STS</th>
                                <th>Nomor Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Rupiah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($detail_bos as $data)
                                @php
                                    $total += $data->rupiah;
                                @endphp
                                <tr>
                                    <td>{{ $data->no_sts }}</td>
                                    <td>{{ $data->kd_rek6 }}</td>
                                    <td>{{ $data->nm_rek6 }}</td>
                                    <td>{{ rupiah($data->rupiah) }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            onclick="hapus('{{ $data->no_sts }}','{{ $data->kd_rek6 }}','{{ $data->nm_rek6 }}','{{ $data->rupiah }}')"
                                            class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="border:none;background-color:white;text-align:right" readonly
                                class="form-control" id="total" name="total" value="{{ rupiah($total) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_rekening" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rekening Tujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- REKENING -->
                    <div class="mb-3 row">
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal1" style=" width: 100%;" id="rekening"
                                name="rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_rekening as $rekening)
                                    <option value="{{ $rekening->kd_rek6 }}" data-nama="{{ $rekening->nm_rek6 }}">
                                        {{ $rekening->kd_rek6 }} |
                                        {{ $rekening->nm_rek6 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Sisa KAS BOS --}}
                    <div class="mb-3 row">
                        <label for="sisa_bos" class="col-md-2 col-form-label">Sisa KAS BOS</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="sisa_bos" id="sisa_bos"
                                style="text-align: right" readonly>
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                style="text-align: right" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    {{-- Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_rincian" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.pengembalian_bos.js.edit');
@endsection
