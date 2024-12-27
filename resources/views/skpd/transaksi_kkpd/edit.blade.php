@extends('template.app')
@section('title', 'Edit Transaksi KKPD | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Transaksi Non Tunai
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No voucher dan tanggal transaksi --}}
                    <div class="mb-3 row">
                        <label for="no_voucher" class="col-md-2 col-form-label">No Voucher</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_voucher" name="no_voucher"
                                value="{{ $cms->no_voucher }}" readonly placeholder="Tidak perlu diisi atau diedit">
                        </div>
                        <label for="tgl_voucher" class="col-md-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_voucher" name="tgl_voucher"
                                value="{{ $cms->tgl_voucher }}">
                            <input type="text" class="form-control" id="tahun_anggaran" name="tahun_anggaran" hidden
                                value="{{ tahun_anggaran() }}">
                            <input type="text" class="form-control" id="ketcms" name="ketcms" hidden>
                            <input type="text" class="form-control" id="persen_kkpd" value="{{ $persen->persen_kkpd }}"
                                hidden>
                            <input type="text" class="form-control" id="persen_tunai" value="{{ $persen->persen_tunai }}"
                                hidden>
                        </div>
                    </div>
                    {{-- No bukti cms dan jenis beban --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No Bukti CMS</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_bukti" name="no_bukti" readonly
                                value="{{ $cms->no_bukti }}">
                        </div>
                        <label for="beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select name="beban" id="beban" class="form-control select2-multiple">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="1" {{ $cms->jns_spp == '1' ? 'selected' : '' }}
                                    {{ $cms->jns_spp != '1' ? 'disabled' : '' }}>UP/GU</option>
                                <option value="3" {{ $cms->jns_spp == '3' ? 'selected' : '' }}
                                    {{ $cms->jns_spp != '3' ? 'disabled' : '' }}>TU</option>
                                <option value="4" {{ $cms->jns_spp == '4' ? 'selected' : '' }}
                                    {{ $cms->jns_spp != '4' ? 'disabled' : '' }}>Gaji</option>
                                <option value="6" {{ $cms->jns_spp == '6' ? 'selected' : '' }}
                                    {{ $cms->jns_spp != '6' ? 'disabled' : '' }}>Barang dan Jasa
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Kode OPD/Unit dan Nama OPD/Unit --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode OPD/Unit</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="kd_skpd" name="kd_skpd" readonly
                                value="{{ $cms->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama OPD/Unit</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" readonly
                                value="{{ $cms->nm_skpd }}">
                        </div>
                    </div>
                    {{-- Pembayaran dan Rekening Bank Bendahara --}}
                    <div class="mb-3 row">
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-4">
                            <select name="pembayaran" id="pembayaran" class="form-control select2-multiple">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="BANK" {{ $cms->pay == 'BANK' ? 'selected' : '' }}>BANK</option>
                            </select>
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening Bank Bendahara</label>
                        <div class="col-md-4">
                            <select name="rekening" id="rekening" class="form-control select2-multiple">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="{{ rtrim($data_rek->rekening) }}"
                                    {{ $cms->rekening_awal == rtrim($data_rek->rekening) ? 'selected' : '' }}>
                                    {{ $data_rek->rekening }}</option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea name="keterangan" id="keterangan" rows="4" class="form-control">{{ $cms->ket }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        @if ($cms->status_validasi == '1')
                            <button id="simpan_cms" class="btn btn-primary btn-md" disabled>Simpan</button>
                        @else
                            <button id="simpan_cms" class="btn btn-primary btn-md">Simpan</button>
                        @endif
                        <a href="{{ route('skpd.transaksi_kkpd.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekening --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rekening
                    <button type="button" style="float: right" id="tambah_rek" class="btn btn-primary btn-sm">Tambah
                        Sub Kegiatan</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_rekening" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th> {{-- hidden --}}
                                <th>No SP2D</th>
                                <th>Kegiatan</th>
                                <th>Nama Kegiatan</th> {{-- hidden --}}
                                <th>Kode Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Sumber</th>
                                <th>Sudah Dibayarkan</th>
                                <th>SP2D Non UP</th>
                                <th>Anggaran</th>
                                <th>Volume</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_rincian_rekening as $rincian_rek)
                                <tr>
                                    <td>{{ $rincian_rek->no_voucher }}</td>
                                    <td>{{ $rincian_rek->no_sp2d }}</td>
                                    <td>{{ $rincian_rek->kd_sub_kegiatan }}</td>
                                    <td>{{ $rincian_rek->nm_sub_kegiatan }}</td>
                                    <td>{{ $rincian_rek->kd_rek6 }}</td>
                                    <td>{{ $rincian_rek->nm_rek6 }}</td>
                                    <td>{{ rupiah($rincian_rek->nilai) }}</td>
                                    <td>{{ $rincian_rek->sumber }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $rincian_rek->volume }}</td>
                                    <td>{{ $rincian_rek->satuan }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            onclick="deleteData('{{ $rincian_rek->no_voucher }}','{{ $rincian_rek->kd_sub_kegiatan }}','{{ $rincian_rek->kd_rek6 }}','{{ $rincian_rek->sumber }}','{{ $rincian_rek->nilai }}')"
                                            class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total_belanja" class="col-md-8 col-form-label" style="text-align: right">Total
                            Belanja</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly
                                class="form-control @error('total_belanja') is-invalid @enderror" id="total_belanja"
                                name="total_belanja" value="{{ rupiah($cms->total) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Rekening Tujuan --}}
        {{-- <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Daftar Rekening Tujuan
                    <button type="button" style="float: right" id="tambah_rek_tujuan"
                        class="btn btn-primary btn-sm">Tambah</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rekening_tujuan" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>Tanggal</th>
                                <th>Rekening Awal</th>
                                <th>Nama</th>
                                <th>Rek. Tujuan</th>
                                <th>Bank</th>
                                <th>SKPD</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rincian_rek_tujuan as $rekening_tujuan)
                                <tr>
                                    <td>{{ $rekening_tujuan->no_voucher }}</td>
                                    <td>{{ $rekening_tujuan->tgl_voucher }}</td>
                                    <td>{{ $rekening_tujuan->rekening_awal }}</td>
                                    <td>{{ $rekening_tujuan->nm_rekening_tujuan }}</td>
                                    <td>{{ $rekening_tujuan->rekening_tujuan }}</td>
                                    <td>{{ $rekening_tujuan->bank_tujuan }}</td>
                                    <td>{{ $rekening_tujuan->kd_skpd }}</td>
                                    <td>{{ rupiah($rekening_tujuan->nilai) }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            onclick="deleteRek('{{ $rekening_tujuan->no_voucher }}','{{ $rekening_tujuan->rekening_tujuan }}','{{ $rekening_tujuan->nilai }}')"
                                            class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total_transfer" class="col-md-8 col-form-label" style="text-align: right">Total
                            Transfer</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly
                                class="form-control @error('total_transfer') is-invalid @enderror" id="total_transfer"
                                name="total_transfer" value="{{ rupiah($total_transfer) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <div id="modal_kegiatan" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rincian Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- SUB KEGIATAN -->
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal" style=" width: 100%;" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_sub_kegiatan" readonly
                                name="nm_sub_kegiatan">
                        </div>
                    </div>
                    {{-- Nomor SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">Nomor SP2D</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal" style=" width: 100%;" id="no_sp2d"
                                name="no_sp2d">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <!-- REKENING -->
                    <div class="mb-3 row">
                        <label for="kd_rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal" style=" width: 100%;" id="kd_rekening"
                                name="kd_rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_rekening" readonly name="nm_rekening">
                        </div>
                    </div>
                    <!-- SUMBER DANA -->
                    <div class="mb-3 row">
                        <label for="sumber" class="col-md-2 col-form-label">Sumber</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal" style=" width: 100%;" id="sumber"
                                name="sumber">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_sumber" readonly name="nm_sumber">
                        </div>
                    </div>
                    <!-- TOTAL SPD -->
                    <div class="mb-3 row">
                        <label for="total_spd" class="col-md-2 col-form-label">Total SPD</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_spd" id="total_spd"
                                style="text-align: right">
                        </div>
                        <label for="realisasi_spd" class="col-md-2 col-form-label">Realisasi</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="realisasi_spd" id="realisasi_spd"
                                style="text-align: right">
                        </div>
                        <label for="sisa_spd" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_spd" id="sisa_spd"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- ANGKAS -->
                    <div class="mb-3 row">
                        <label for="total_angkas" class="col-md-2 col-form-label">Total Anggaran Kas</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_angkas" id="total_angkas"
                                style="text-align: right">
                        </div>
                        <label for="realisasi_angkas" class="col-md-2 col-form-label">Realisasi</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="realisasi_angkas"
                                id="realisasi_angkas" style="text-align: right">
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
                        <label for="realisasi_anggaran" class="col-md-2 col-form-label">Realisasi</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="realisasi_anggaran"
                                id="realisasi_anggaran" style="text-align: right">
                        </div>
                        <label for="sisa_anggaran" class="col-md-2 col-form-label">Sisa</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_anggaran" id="sisa_anggaran"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- NILAI SUMBER DANA -->
                    <div class="mb-3 row">
                        <label for="total_sumber" class="col-md-2 col-form-label">Nilai Sumber Dana</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_sumber" id="total_sumber"
                                style="text-align: right">
                        </div>
                        <label for="realisasi_sumber" class="col-md-2 col-form-label">Realisasi</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="realisasi_sumber"
                                id="realisasi_sumber" style="text-align: right">
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
                        </div>
                        <label for="status_angkas" class="col-md-2 col-form-label">Status Anggaran Kas</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="status_angkas" id="status_angkas">
                        </div>
                        <label for="sisa_kas" class="col-md-2 col-form-label">Sisa Kas Bank</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="sisa_kas" id="sisa_kas"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Potongan LS -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="potongan_ls" class="col-md-2 col-form-label">Potongan LS</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="potongan_ls" id="potongan_ls"
                                style="text-align: right" readonly>
                        </div>
                    </div>
                    <!-- Total Sisa -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="total_sisa" class="col-md-2 col-form-label">Total Sisa</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="total_sisa" id="total_sisa"
                                style="text-align: right" readonly>
                        </div>
                    </div>
                    <!-- Volume Output -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="volume" class="col-md-2 col-form-label">Volume Output</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="volume" id="volume"
                                style="text-align: right">
                        </div>
                    </div>
                    <!-- Satuan Output -->
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="satuan" class="col-md-2 col-form-label">Satuan Output</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="satuan" id="satuan"
                                style="text-align: right">
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="jarak" class="col-md-8 col-form-label"></label>
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                style="text-align: right" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                        </div>
                    </div>
                    {{-- Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_rekening" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="total_input_rekening" style="text-align: right"
                        class="col-md-9 col-form-label">Total</label>
                    <div class="col-md-3" style="padding-right: 30px">
                        <input type="text" width="100%" class="form-control" style="text-align: right" readonly
                            name="total_input_rekening" id="total_input_rekening" value="{{ rupiah($cms->total) }}">
                    </div>
                </div>
                <div class="card" style="margin: 4px">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border-primary mb-0" style="width: 100%"
                                id="input_rekening">
                                <thead>
                                    <tr>
                                        <th>No Bukti</th> {{-- hidden --}}
                                        <th>No SP2D</th>
                                        <th>Kegiatan</th>
                                        <th>Nama Kegiatan</th> {{-- hidden --}}
                                        <th>Kode Rekening</th>
                                        <th>Nama Rekening</th>
                                        <th>Rupiah</th>
                                        <th>Sumber</th>
                                        <th>Sudah Dibayarkan</th>
                                        <th>SP2D Non UP</th>
                                        <th>Anggaran</th>
                                        <th>Volume</th>
                                        <th>Satuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_rincian_rekening as $rincian_rek)
                                        <tr>
                                            <td>{{ $rincian_rek->no_voucher }}</td>
                                            <td>{{ $rincian_rek->no_sp2d }}</td>
                                            <td>{{ $rincian_rek->kd_sub_kegiatan }}</td>
                                            <td>{{ $rincian_rek->nm_sub_kegiatan }}</td>
                                            <td>{{ $rincian_rek->kd_rek6 }}</td>
                                            <td>{{ $rincian_rek->nm_rek6 }}</td>
                                            <td>{{ rupiah($rincian_rek->nilai) }}</td>
                                            <td>{{ $rincian_rek->sumber }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $rincian_rek->volume }}</td>
                                            <td>{{ $rincian_rek->satuan }}</td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="deleteData('{{ $rincian_rek->no_voucher }}','{{ $rincian_rek->kd_sub_kegiatan }}','{{ $rincian_rek->kd_rek6 }}','{{ $rincian_rek->sumber }}','{{ $rincian_rek->nilai }}')"
                                                    class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
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
    @include('skpd.transaksi_kkpd.js.edit')
@endsection
