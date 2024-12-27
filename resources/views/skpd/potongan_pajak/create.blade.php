@extends('template.app')
@section('title', 'Tambah Potongan Pajak | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Potongan
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Nomor Bukti dan Tanggal --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No Bukti Terima</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required readonly>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ $tahun_anggaran }}">
                        </div>
                        <label for="tgl_bukti" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_bukti" name="tgl_bukti"
                                value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd"
                                value="{{ $skpd->kd_skpd }}" required readonly>
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd"
                                value="{{ $skpd->nm_skpd }}" required readonly>
                        </div>
                    </div>
                    {{-- No Transaksi dan Tanggal Transaksi --}}
                    <div class="mb-3 row">
                        <label for="no_transaksi" class="col-md-2 col-form-label">No Transaksi</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="no_transaksi"
                                name="no_transaksi">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_potongan as $potongan)
                                    <option value="{{ $potongan->no_bukti }}" data-tgl="{{ $potongan->tgl_bukti }}">
                                        {{ $potongan->no_bukti }} |
                                        {{ $potongan->tgl_bukti }} | {{ $potongan->no_sp2d }} |
                                        {{ rupiah($potongan->nilai) }} |
                                        {{ $potongan->ket }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="tgl_transaksi" class="col-md-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_transaksi" name="tgl_transaksi" readonly>
                        </div>
                    </div>
                    {{-- NO SP2D dan Pembayaran --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">No SP2D</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="no_sp2d" name="no_sp2d">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_sp2d as $sp2d)
                                    <option value="{{ $sp2d->no_sp2d }}">{{ $sp2d->no_sp2d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value=" ">TUNAI</option>
                                <option value="BANK">BANK</option>
                            </select>
                        </div>
                    </div>
                    {{-- Kode Kegiatan dan Nama Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kode Kegiatan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_sub_kegiatan" name="nm_sub_kegiatan" readonly>
                        </div>
                    </div>
                    {{-- Kode Rekening dan Nama Rekening --}}
                    <div class="mb-3 row">
                        <label for="kd_rekening" class="col-md-2 col-form-label">Kode Rekening</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_rekening"
                                name="kd_rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                        <label for="nm_rekening" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_rekening" name="nm_rekening" readonly>
                        </div>
                    </div>
                    {{-- Rekanan dan Pimpinan --}}
                    <div class="mb-3 row">
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekanan"
                                name="rekanan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_rekanan as $rekanan)
                                    <option value="{{ $rekanan->nmrekan }}" data-pimpinan="{{ $rekanan->pimpinan }}"
                                        data-npwp="{{ $rekanan->npwp }}" data-alamat="{{ $rekanan->alamat }}">
                                        {{ $rekanan->nmrekan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="pimpinan" class="col-md-2 col-form-label">Pimpinan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="pimpinan" name="pimpinan">
                        </div>
                    </div>
                    {{-- Rekanan --}}
                    <div class="mb-3 row" id="rekanan1">
                        <label for="rekanan" class="col-md-2 col-form-label">Nama Rekanan</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="nama_rekanan" name="nama_rekanan">
                        </div>
                    </div>
                    {{-- Beban dan NPWP --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="beban"
                                name="beban">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">UP</option>
                                <option value='2'>GU</option>
                                <option value='3'>TU</option>
                                <option value='4'>LS GAJI</option>
                                <option value='5'>LS PPKD</option>
                                <option value='6'>LS Barang Jasa</option>
                            </select>
                        </div>
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="npwp" name="npwp">
                        </div>
                    </div>
                    {{-- Alamat Perusahaan --}}
                    <div class="mb-3 row">
                        <label for="alamat" class="col-md-2 col-form-label">Alamat</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="alamat" name="alamat"></textarea>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    {{-- Rekening dan Nama Rekening --}}
                    <div class="mb-3 row">
                        <label for="rekening_potongan" class="col-md-2 col-form-label">Rekening Potongan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening_potongan"
                                name="rekening_potongan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_rek as $rek)
                                    <option value="{{ $rek->kd_rek6 }}" data-nama="{{ $rek->nm_rek6 }}">
                                        {{ $rek->kd_rek6 }} | {{ $rek->nm_rek6 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="nm_rekening_potongan" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_rekening_potongan"
                                name="nm_rekening_potongan" readonly>
                        </div>
                    </div>
                    {{-- No Billing dan Nilai --}}
                    <div class="mb-3 row">
                        <label for="no_billing" class="col-md-2 col-form-label">No Billing</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_billing" name="no_billing">
                        </div>
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nilai" name="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                        </div>
                    </div>
                    {{-- Tambahkan Potongan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12" style="text-align: center">
                            <button id="tambah_potongan" class="btn btn-primary btn-md">
                                {{-- <i class="far fa-plus-square"></i> --}}
                                Tambahkan Potongan</button>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_potongan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.potongan_pajak.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Input Detail --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Potongan
                </div>
                <div class="card-body table-responsive">
                    <table id="list_potongan" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Rek Trans</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Rekanan</th>
                                <th>NPWP</th>
                                <th>No Billing</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <hr>
                    <table style="width: 100%">
                        <tbody>
                            <tr>
                                <td style="padding-left: 600px">Total</td>
                                <td>:</td>
                                <td style="text-align: right"><input type="text"
                                        style="border:none;background-color:white;text-align:right" class="form-control"
                                        readonly id="total_potongan">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.potongan_pajak.js.create');
@endsection
