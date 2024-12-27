@extends('template.app')
@section('title', 'Input Data Penerimaan Kas | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Penerimaan Kas
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No. Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas"
                                placeholder="Tidak Perlu Diisi" required readonly>
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_kas" name="tgl_kas" required>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_skpd as $skpd)
                                    <option value="{{ $skpd->kd_skpd }}" data-nama="{{ $skpd->nm_skpd }}">
                                        {{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly>
                        </div>
                    </div>
                    {{-- No Bukti dan Tanggal Bukti --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No Bukti</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="no_bukti"
                                name="no_bukti">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                </option>
                            </select>
                        </div>
                        <label for="tgl_bukti" class="col-md-2 col-form-label">Tanggal Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_bukti" name="tgl_bukti" required readonly>
                            <input class="form-control" type="text" id="sumber" name="sumber" required readonly
                                hidden>
                            <input class="form-control" type="text" id="jns_cp" name="jns_cp" required readonly
                                hidden>
                        </div>
                    </div>
                    {{-- Kegiatan dan Nama Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_sub_kegiatan" name="kd_sub_kegiatan" required
                                readonly>
                        </div>
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_sub_kegiatan" name="nm_sub_kegiatan" required
                                readonly>
                        </div>
                    </div>
                    {{-- Jenis Transaksi --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis Transaksi</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis" name="jenis">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="4">Pendapatan</option>
                                <option value="5">Belanja</option>
                                <option value="61">Penerimaan Pembiayaan</option>
                                <option value="62">Pengeluaran Pembiayaan</option>
                                <option value="1">Rekening Kas</option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan" readonly></textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('penerimaan_kas.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                </div>
                <div class="card-body table-responsive">
                    <table id="detail_sts" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>No STS</th>
                                <th>Nomor Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Jenis</th>
                                <th>Rupiah</th>
                                <th>Sumber</th>
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
    @include('skpd.penerimaan_kas.js.create');
@endsection
