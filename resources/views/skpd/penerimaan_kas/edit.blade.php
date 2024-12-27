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
                <div class="alert alert-warning alert-block {{ $terima->status == 1 ? '' : 'hidden' }}">
                    <b style="font-size:16px">Data Sudah Tervalidasi!!</b>
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No. Kas</label>
                        @if ($terima->status == 1)
                            <div class="col-md-4">
                                <input class="form-control" type="text" id="no_kas" maxlength="7" name="no_kas"
                                    value="{{ $terima->no_kas }}" required disabled>
                            </div>
                        @else
                            <div class="col-md-4">
                                <input class="form-control" type="text" id="no_kas" maxlength="8" name="no_kas"
                                    value="PB{{ $terima->no_kas }}" required>
                            </div>
                        @endif

                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_kas" name="tgl_kas" required
                                value="{{ $terima->tgl_kas }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $terima->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ nama_skpd($terima->kd_skpd) }}">
                        </div>
                    </div>
                    {{-- No Bukti dan Tanggal Bukti --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required readonly
                                value="{{ $terima->no_sts }}">
                        </div>
                        <label for="tgl_bukti" class="col-md-2 col-form-label">Tanggal Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_bukti" name="tgl_bukti" required readonly
                                value="{{ $terima->tgl_sts }}">
                            <input class="form-control" type="text" id="sumber" name="sumber" required readonly
                                hidden value="{{ $terima->sumber }}">
                            <input class="form-control" type="text" id="jns_cp" name="jns_cp" required readonly
                                hidden value="{{ $terima->jns_cp }}">
                        </div>
                    </div>
                    {{-- Kegiatan dan Nama Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_sub_kegiatan" name="kd_sub_kegiatan" required
                                readonly value="{{ $terima->kd_sub_kegiatan }}">
                        </div>
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_sub_kegiatan" name="nm_sub_kegiatan" required
                                readonly value="{{ $terima->nm_sub_kegiatan }}">
                        </div>
                    </div>
                    {{-- Jenis Transaksi --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis Transaksi</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis"
                                name="jenis">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="4" {{ Str::of($terima->jns_trans)->trim() == '4' ? 'selected' : '' }}>
                                    Pendapatan
                                </option>
                                <option value="5" {{ Str::of($terima->jns_trans)->trim() == '5' ? 'selected' : '' }}>
                                    Belanja</option>
                                <option value="61"
                                    {{ Str::of($terima->jns_trans)->trim() == '61' ? 'selected' : '' }}>
                                    Penerimaan
                                    Pembiayaan</option>
                                <option value="62"
                                    {{ Str::of($terima->jns_trans)->trim() == '62' ? 'selected' : '' }}>
                                    Pengeluaran
                                    Pembiayaan</option>
                                <option value="1" {{ Str::of($terima->jns_trans)->trim() == '1' ? 'selected' : '' }}>
                                    Rekening Kas
                                </option>
                                </option>
                            </select>
                        </div>
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar pembayaran">
                                    <option value="" disabled selected>...Pilih Pembayaran... </option>
                                    <option value="3001006966"
                                        {{ Str::of($terima->rek_bank1)->trim() == '3001006966' ? 'selected' : '' }}>Bank -
                                        3001006966
                                    </option>
                                    <option value="3001000016"
                                        {{ Str::of($terima->rek_bank1)->trim() == '3001000016' ? 'selected' : '' }}>Bank -
                                        3001000016
                                    </option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan" readonly>{{ $terima->keterangan }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <div>
                                @if ($terima->status == 1)
                                    <button id="batalkas" class="btn btn-danger btn-md">Batal Validasi</button>
                                    <a href="{{ route('penerimaan_kas.index') }}"
                                        class="btn btn-warning btn-md">Kembali</a>
                                @elseif ($terima->status == 0)
                                    <button id="simpanedit" class="btn btn-primary btn-md">Validasi</button>
                                    <a href="{{ route('penerimaan_kas.index') }}"
                                        class="btn btn-warning btn-md">Kembali</a>
                                @endif
                            </div>


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
                                <th>Nomor Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Rupiah</th>
                                <th>Sumber</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Jumlah</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total" name="total"
                                value="{{ rupiah($terima->total) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.penerimaan_kas.js.edit');
@endsection
