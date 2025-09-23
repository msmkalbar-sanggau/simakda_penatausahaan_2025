@extends('template.app')
@section('title', 'Edit Setor Sisa Kas/CP | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Setor Sisa Kas/CP
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Nomor dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required readonly value="{{ $setor->no_sts }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal Kas</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_kas" name="tgl_kas" value="{{ $setor->tgl_sts }}">
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly value="{{ $skpd->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly value="{{ $skpd->nm_skpd }}">
                        </div>
                    </div>
                    {{-- Uraian --}}
                    <div class="mb-3 row">
                        <label for="uraian" class="col-md-2 col-form-label">Uraian</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="uraian" name="uraian">{{ $setor->keterangan }}</textarea>
                        </div>
                    </div>
                    {{-- Pembayaran --}}
                    <div class="mb-3 row">
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran" name="pembayaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="BNK" {{ $setor->bank == 'BNK' ? 'selected' : '' }}>BANK</option>
                                <option value="TNK" {{ $setor->bank == 'TNK' ? 'selected' : '' }}>TUNAI</option>
                            </select>
                        </div>
                    </div>
                    {{-- Jenis Transaksi --}}
                    <div class="mb-3 row">
                        <label for="jenis_transaksi" class="col-md-2 col-form-label">Jenis Transaksi</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis_transaksi" name="jenis_transaksi">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="5" disabled {{ $setor->jns_trans == 5 ? 'selected' : '' }}>Belanja
                                </option>
                                <option value="1" disabled {{ $setor->jns_trans == 1 ? 'selected' : '' }}>Rekening Kas
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- SP2D dan Jenis CP --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">SP2D</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_sp2d" name="no_sp2d" required readonly value="{{ $setor->no_sp2d }}">
                        </div>
                        <label for="jenis_cp" class="col-md-2 col-form-label">Jenis CP</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="jenis_cp" name="jenis_cp" required readonly
                                value="{{ $setor->jns_cp == 1 ? 'UP/GU/TU' : ($setor->jns_cp == 2 ? 'LS BARANG/JASA' : 'LS GAJI') }}">
                            {{-- value="{{ $setor->jns_cp == 1 ? 'UP/GU/TU' : 'LS GAJI' }}"> --}}
                        </div>
                    </div>
                    {{-- Kegiatan dan Nama Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_sub_kegiatan" name="kd_sub_kegiatan" required readonly value="{{ $setor->kd_sub_kegiatan }}">
                        </div>
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_sub_kegiatan" name="nm_sub_kegiatan" required readonly>
                        </div>
                    </div>
                    <br>
                    {{-- HKPG Tahun Ini, HKPG Tahun Lalu, Pemotongan Lainnya --}}
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <input type="checkbox" id="hkpg_tahun_ini" name="hkpg_tahun_ini" value="{{ $setor->pot_khusus == 1 ? 'checked' : '' }}">
                            <label for="hkpg_tahun_ini">HKPG Tahun Ini</label>
                            <input type="checkbox" id="hkpg_tahun_lalu" name="hkpg_tahun_lalu" style="margin-left: 10px" value="{{ $setor->pot_khusus == 3 ? 'checked' : '' }}">
                            <label for="hkpg_tahun_lalu">HKPG Tahun Lalu</label>
                            <input type="checkbox" id="pemotongan_lainnya" name="pemotongan_lainnya" style="margin-left: 10px"
                                value="{{ $setor->pot_khusus == 2 ? 'checked' : '' }}">
                            <label for="pemotongan_lainnya">Pemotongan Lainnya</label>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_sts" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.setor_sisa.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                    <button type="button" style="float: right" id="tambah_sts" class="btn btn-primary btn-sm">Tambah</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="detail_sts" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nomor Rekening</th>
                                <th>Nama Rekening</th>
                                <th>S. Dana</th>
                                <th>Rupiah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $jumlah = 0;
                            @endphp
                            @foreach ($data_list as $list)
                                @php
                                    $jumlah += $list->rupiah;
                                @endphp
                                <tr>
                                    <td>{{ $list->kd_rek6 }}</td>
                                    <td>{{ $list->nm_rek6 }}</td>
                                    <td>{{ $list->sumber }}</td>
                                    <td>{{ rupiah($list->rupiah) }}</td>
                                    <td>
                                        <a href="javascript:void(0);" onclick="deleteDetail('{{ $list->kd_rek6 }}','{{ $list->rupiah }}')" class="btn btn-danger btn-sm"><i
                                                class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <table style="width: 100%">
                            <tbody>
                                <tr>
                                    <td style="padding-left: 600px">Jumlah</td>
                                    <td>:</td>
                                    <td style="text-align: right"><input type="text" style="border:none;background-color:white;text-align:right" class="form-control" readonly
                                            id="jumlah" value="{{ rupiah($jumlah) }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_detail" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rekening</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Kode Rekening -->
                    <div class="mb-3 row">
                        <label for="kd_rek6" class="col-md-2 col-form-label">Kode Rekening</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal1" style=" width: 100%;" id="kd_rek6" name="kd_rek6">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- Nama Rekening -->
                    <div class="mb-3 row">
                        <label for="nm_rek6" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nm_rek6" id="nm_rek6" readonly>
                        </div>
                    </div>
                    <!-- Kode Rekening -->
                    <div class="mb-3 row">
                        <label for="sdana" class="col-md-2 col-form-label">Sumber Dana</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal1" style=" width: 100%;" id="sdana" name="sdana">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    {{-- Sisa Kas Tunai --}}
                    <div class="mb-3 row">
                        <label for="sisa_kas_tunai" class="col-md-2 col-form-label">Sisa Kas Tunai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="sisa_kas_tunai" id="sisa_kas_tunai" readonly>
                        </div>
                    </div>
                    {{-- Sisa Kas Bank --}}
                    <div class="mb-3 row">
                        <label for="sisa_kas_bank" class="col-md-2 col-form-label">Sisa Kas Bank</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="sisa_kas_bank" id="sisa_kas_bank" readonly>
                        </div>
                    </div>
                    <!-- Nilai -->
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai" id="nilai" style="text-align: right" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                                data-type="currency">
                        </div>
                    </div>
                    {{-- Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_detail" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.setor_sisa_kas.js.edit');
@endsection
