@extends('template.app')
@section('title', 'Input Data Penyetoran Atas Penerimaan Tahun Ini | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Penyetoran Atas Penerimaan Tahun Ini
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No STS dan Tanggal STS --}}
                    <div class="mb-3 row">
                        <label for="no_sts" class="col-md-2 col-form-label">No. STS</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_sts" name="no_sts"
                                placeholder="Silahkan Diisi" required value="{{ $sts->no_sts }}">
                        </div>
                        <label for="tgl_sts" class="col-md-2 col-form-label">Tanggal STS</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_sts" name="tgl_sts" required
                                value="{{ $sts->tgl_sts }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- Kode dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $sts->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $sts->nm_skpd }}">
                        </div>
                    </div>
                    {{-- Kegiatan dan Nama Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kegiatan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_kegiatan as $kegiatan)
                                    <option value="{{ $kegiatan->kd_sub_kegiatan }}"
                                        data-nama="{{ $kegiatan->nm_sub_kegiatan }}"
                                        {{ $kegiatan->kd_sub_kegiatan == $sts->kd_sub_kegiatan ? 'selected' : '' }}>
                                        {{ $kegiatan->kd_sub_kegiatan }} | {{ $kegiatan->nm_sub_kegiatan }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_sub_kegiatan" name="nm_sub_kegiatan" required
                                readonly value="{{ nama_kegiatan($sts->kd_sub_kegiatan) }}">
                        </div>
                    </div>
                    {{-- Tanggal Terima dan No Simpan --}}
                    <div class="mb-3 row">
                        <label for="tgl_terima" class="col-md-2 col-form-label">Tanggal Terima</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_terima" name="tgl_terima" required>
                        </div>
                        <label for="no_simpan" class="col-md-2 col-form-label">No STS Tersimpan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_simpan" name="no_simpan" required
                                value="{{ $sts->no_sts }}" readonly>
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required
                                value="{{ $sts->no_kas }}" readonly hidden>
                        </div>
                    </div>
                    {{-- No Terima --}}
                    <div class="mb-3 row">
                        <label for="no_terima" class="col-md-2 col-form-label">No Terima</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="no_terima"
                                name="no_terima">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                </option>
                            </select>
                        </div>

                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-2 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan">{{ $sts->keterangan }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('penyetoran_ini.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                                <th>No STS</th>
                                <th>Nomor Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Rupiah</th>
                                <th>Sumber</th>
                                {{-- <th>Nama Lokasi</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($detail_sts as $detail)
                                @php
                                    $total += $detail->rupiah;
                                @endphp
                                <tr>
                                    <td>{{ $detail->no_sts }}</td>
                                    <td>{{ $detail->kd_rek6 }}</td>
                                    <td>{{ $detail->nm_rek }}</td>
                                    <td>{{ rupiah($detail->rupiah) }}</td>
                                    <td>{{ $detail->sumber }}</td>
                                    <td>{{ $detail->nm_pengirim }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            onclick="deleteData('{{ $detail->no_terima }}','{{ $detail->kd_rek6 }}','{{ $detail->nm_rek }}','{{ $detail->rupiah }}')"
                                            class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Jumlah</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
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
                    <h5 class="modal-title">Input Rekening</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Rekening -->
                    <div class="mb-3 row">
                        <label for="rekening" class="col-md-2 col-form-label">Kode Rekening</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="rekening"
                                name="rekening" data-placeholder="Silahkan Pilih">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nm_rekening" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="nm_rekening" readonly name="nm_rekening">
                        </div>
                    </div>
                    <!-- Nilai -->
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                        </div>
                    </div>
                    {{-- Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_detail" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.penyetoran_tahun_ini.js.edit');
@endsection
