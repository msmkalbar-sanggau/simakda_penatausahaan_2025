@extends('template.app')
@section('title', 'Input Transaksi BOS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Transaksi BOS
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No BKU --}}
                    <div class="mb-3 row">
                        <label for="no_bku" class="col-md-2 col-form-label">No. BKU</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="no_bku" name="no_bku" required readonly
                                placeholder="TIDAK PERLU DIISI ATAU DIEDIT">
                        </div>
                    </div>
                    {{-- No dan Tanggal Bukti --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No. Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required
                                value="{{ $bos->no_kas }}" readonly>
                            <input class="form-control" type="text" id="no_simpan" name="no_simpan" required
                                value="{{ $bos->no_kas }}" readonly hidden>
                        </div>
                        <label for="tgl_bukti" class="col-md-2 col-form-label">Tanggal Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_bukti" name="tgl_bukti" required
                                value="{{ $bos->tgl_kas }}">
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
                                value="{{ $bos->nm_skpd }}">
                        </div>
                    </div>
                    {{-- SATDIK dan Jenis Beban --}}
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
                        <label for="jenis_beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis_beban"
                                name="jenis_beban">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="4" {{ $bos->jns_spp == 4 ? 'selected' : '' }}>BOS PPKD</option>
                                <option value="6" {{ $bos->jns_spp == 6 ? 'selected' : '' }}>BOS SKPD</option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Tahap dan Jenis BOS --}}
                    <div class="mb-3 row">
                        <label for="tahap" class="col-md-2 col-form-label">Tahap</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="tahap" name="tahap">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1" {{ $bos->tahap == 1 ? 'selected' : '' }}>Tahap 1</option>
                                <option value="2" {{ $bos->tahap == 2 ? 'selected' : '' }}>Tahap 2</option>
                                <option value="3" {{ $bos->tahap == 3 ? 'selected' : '' }}>Tahap 3</option>
                                <option value="4" {{ $bos->tahap == 4 ? 'selected' : '' }}>Tahap 4</option>
                                </option>
                            </select>
                        </div>
                        <label for="jenis_bos" class="col-md-2 col-form-label">Jenis BOS</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="jenis_bos"
                                name="jenis_bos">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="reguler" {{ $bos->jns_bos == 'reguler' ? 'selected' : '' }}>Bos Reguler
                                </option>
                                <option value="hibah" {{ $bos->jns_bos == 'hibah' ? 'selected' : '' }}>Bos Hibah
                                </option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Jenis Pembayaran --}}
                    <div class="mb-3 row">
                        <label for="pembayaran" class="col-md-2 col-form-label">Jenis Pembayaran</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="TUNAI">TUNAI</option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan">{{ $bos->ket }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('transaksi_bos.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Rincian --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Detail Rincian
                    <button type="button" style="float: right" id="tambah_rincian"
                        class="btn btn-primary btn-md">Tambah Rincian</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="detail" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>Kegiatan</th>
                                <th>Nama Kegiatan</th>
                                <th>Kode Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Sumber</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($data_bos as $data)
                                @php
                                    $total += $data->nilai;
                                @endphp
                                <tr>
                                    <td>{{ $data->no_bukti }}</td>
                                    <td>{{ $data->kd_sub_kegiatan }}</td>
                                    <td>{{ $data->nm_sub_kegiatan }}</td>
                                    <td>{{ $data->kd_rek6 }}</td>
                                    <td>{{ $data->nm_rek6 }}</td>
                                    <td>{{ rupiah($data->nilai) }}</td>
                                    <td>{{ $data->sumber }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            onclick="deleteData('{{ $data->no_bukti }}','{{ $data->kd_sub_kegiatan }}','{{ $data->kd_rek6 }}','{{ $data->nilai }}')"
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

    <div id="modal_rincian" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Kode Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kode Kegiatan</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- Kode Rekening -->
                    <div class="mb-3 row">
                        <label for="kode_rekening" class="col-md-2 col-form-label">Kode Rekening</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="kode_rekening"
                                name="kode_rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- Sumber Dana -->
                    <div class="mb-3 row">
                        <label for="sumber" class="col-md-2 col-form-label">Sumber Dana</label>
                        <div class="col-md-10">
                            <select class="form-control select2-modal" style=" width: 100%;" id="sumber"
                                name="sumber">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- Total SPD -->
                    <div class="mb-3 row">
                        <label for="total_spd" class="col-md-2 col-form-label">Total SPD</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_spd" id="total_spd"
                                style="text-align: right">
                        </div>
                        <label for="lalu_spd" class="col-md-2 col-form-label">Lalu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="lalu_spd" id="lalu_spd"
                                style="text-align: right">
                        </div>
                        <label for="sisa_spd" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_spd" id="sisa_spd"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Total Anggaran Kas -->
                    <div class="mb-3 row">
                        <label for="total_angkas" class="col-md-2 col-form-label">Total Anggaran Kas</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_angkas" id="total_angkas"
                                style="text-align: right">
                        </div>
                        <label for="lalu_angkas" class="col-md-2 col-form-label">Lalu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="lalu_angkas" id="lalu_angkas"
                                style="text-align: right">
                        </div>
                        <label for="sisa_angkas" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_angkas" id="sisa_angkas"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Anggaran -->
                    <div class="mb-3 row">
                        <label for="total_anggaran" class="col-md-2 col-form-label">Anggaran</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_anggaran"
                                id="total_anggaran" style="text-align: right">
                        </div>
                        <label for="lalu_anggaran" class="col-md-2 col-form-label">Lalu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="lalu_anggaran" id="lalu_anggaran"
                                style="text-align: right">
                        </div>
                        <label for="sisa_anggaran" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_anggaran" id="sisa_anggaran"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Sumber Dana -->
                    <div class="mb-3 row">
                        <label for="total_sumber" class="col-md-2 col-form-label">Sumber Dana</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_sumber" id="total_sumber"
                                style="text-align: right">
                        </div>
                        <label for="lalu_sumber" class="col-md-2 col-form-label">Lalu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="lalu_sumber" id="lalu_sumber"
                                style="text-align: right">
                        </div>
                        <label for="sisa_sumber" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_sumber" id="sisa_sumber"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Status Anggaran, Status Anggaran Kas, Sisa Kas Bank -->
                    <div class="mb-3 row">
                        <label for="status_anggaran" class="col-md-2 col-form-label">Status Anggaran</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="status_anggaran"
                                id="status_anggaran">
                            <input type="text" readonly class="form-control" name="jns_ang" id="jns_ang" hidden>
                        </div>
                        <label for="status_angkas" class="col-md-2 col-form-label">Status Anggaran Kas</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="status_angkas" id="status_angkas">
                        </div>
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-2">
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
                <div class="mb-3 row">
                    <label for="total_rincian" style="text-align: right" class="col-md-9 col-form-label">Total</label>
                    <div class="col-md-3" style="padding-right: 30px">
                        <input type="text" width="100%" class="form-control"
                            style="text-align: right;background-color:white;border:none;" readonly name="total_rincian"
                            id="total_rincian" value="{{ rupiah($total) }}">
                    </div>
                </div>
                <div class="card" style="margin: 4px">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border-primary mb-0" style="width: 100%"
                                id="input_rincian">
                                <thead>
                                    <tr>
                                        <th>No Bukti</th>
                                        <th>Kegiatan</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Kode Rekening</th>
                                        <th>Nama Rekening</th>
                                        <th>Rupiah</th>
                                        <th>Sumber</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_bos as $data)
                                        <tr>
                                            <td>{{ $data->no_bukti }}</td>
                                            <td>{{ $data->kd_sub_kegiatan }}</td>
                                            <td>{{ $data->nm_sub_kegiatan }}</td>
                                            <td>{{ $data->kd_rek6 }}</td>
                                            <td>{{ $data->nm_rek6 }}</td>
                                            <td>{{ rupiah($data->nilai) }}</td>
                                            <td>{{ $data->sumber }}</td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="deleteData('{{ $data->no_bukti }}','{{ $data->kd_sub_kegiatan }}','{{ $data->kd_rek6 }}','{{ $data->nilai }}')"
                                                    class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.transaksi_bos.js.edit');
@endsection
