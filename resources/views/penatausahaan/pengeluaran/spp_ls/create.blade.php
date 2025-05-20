@extends('template.app')
@section('title', 'Tambah SPP LS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Dengan Penagihan --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="form-check form-switch form-switch-lg">
                        <input type="checkbox" class="form-check-input" id="dengan_penagihan">
                        <label class="form-check-label" for="dengan_penagihan">Dengan Penagihan</label>
                    </div>
                </div>
                <div id="card_penagihan" class="card-body">
                    <div class="mb-3 row">
                        <table id="rincian_penagihan" class="table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No Penagihan</th>
                                    <th>Tanggal Penagihan</th>
                                    <th>Nilai</th>
                                </tr>
                                <tr>
                                    <td>
                                        <select class="form-control select2-multiple" style="width: 100%" id="no_penagihan"
                                            name="no_penagihan" data-placeholder="Silahkan Pilih">
                                            <optgroup label="Daftar Pilihan">
                                                <option value="" disabled selected>...Pilih... </option>
                                                @foreach ($daftar_penagihan as $penagihan)
                                                    <option value="{{ $penagihan->no_bukti }}"
                                                        data-tgl="{{ $penagihan->tgl_bukti }}"
                                                        data-total="{{ $penagihan->total }}"
                                                        data-kontrak="{{ $penagihan->kontrak }}"
                                                        data-ket="{{ $penagihan->ket }}">
                                                        {{ $penagihan->no_bukti }} | {{ $penagihan->tgl_bukti }} |
                                                        {{ $penagihan->kd_skpd }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="tgl_penagihan" readonly
                                            name="tgl_penagihan">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="nilai_penagihan" readonly
                                            name="nilai_penagihan">
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data Penagihan
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No Tersimpan dan Tanggal SPP --}}
                    <div class="mb-3 row">
                        <label for="no_tersimpan" class="col-md-2 col-form-label">No. Tersimpan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_tersimpan" name="no_tersimpan" required
                                readonly>
                            <input class="form-control" type="text" id="no_urut" name="no_urut" required readonly
                                hidden>
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_spp" name="tgl_spp">
                            <input type="date" class="form-control" id="tgl_spp_lalu" name="tgl_spp_lalu" hidden
                                value="{{ $data_tgl->tgl_spp }}">
                        </div>
                    </div>
                    {{-- No SPP dan Bulan --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No. SPP</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                {{-- <input type="text" class="form-control" id="no_spp" name="no_spp" readonly>
                                <span class="input-group-btn">
                                    <button type="button" id="cari_nospp" class="btn btn-primary"><i
                                            class="uil-refresh"></i></button>
                                </span> --}}
                                <input type="number" name="no_spp" id="no_spp" min="1" class="form-control"
                                    oninput="this.value = Math.abs(this.value)" pattern="/^-?\d+\.?\d*$/">
                            </div>
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="bulan" name="bulan"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Bulan">
                                    <option value="" disabled selected>...Pilih Kebutuhan Bulan... </option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    {{-- KD SKPD dan Keperluan --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $data_skpd->kd_skpd }}">
                        </div>
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea type="text" class="form-control" id="keperluan" name="keperluan"></textarea>
                        </div>
                    </div>
                    {{-- Nama SKPD dan Bank --}}
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $data_skpd->nm_skpd }}">
                        </div>
                        <label for="nm_penerima" class="col-md-2 col-form-label">Nama Penerima</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%;" id="nm_penerima"
                                name="nm_penerima" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Penerima">
                                    <option value="" disabled selected>Silahkan Pilih Penerima</option>
                                    @foreach ($daftar_penerima as $penerima)
                                        <option value="{{ $penerima->nm_rekening }}" data-npwp="{{ $penerima->npwp }}"
                                            data-rekening="{{ $penerima->rekening }}"
                                            data-nmrekan="{{ $penerima->nmrekan }}"
                                            data-pimpinan="{{ $penerima->pimpinan }}"
                                            data-alamat="{{ $penerima->alamat }}" data-bank="{{ $penerima->bank }}"
                                            data-nmbank="{{ $penerima->nm_bank }}"
                                            {{ old('nm_penerima') == $penerima->nm_rekening ? 'selected' : '' }}>
                                            {{ $penerima->nm_rekening }} | {{ $penerima->rekening }} |
                                            {{ $penerima->npwp }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    {{-- Beban dan Penerima --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <select class="form-control" style="width: 100%" id="beban" name="beban">
                                <optgroup label="Daftar Beban">
                                    <option value="" disabled selected>...Pilih Beban... </option>
                                    <option value="4">LS GAJI</option>
                                    <option value="6">LS Barang Jasa</option>
                                    <option value="5">LS Piihak Ketiga Lainnya</option>
                                </optgroup>
                            </select>
                        </div>
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-1">
                            <input type="text" class="form-control" id="bank" name="bank" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="nmbank" name="nmbank" readonly>
                        </div>

                    </div>
                    {{-- Jenis dan Rekening --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style=" width: 100%;" id="jenis"
                                name="jenis" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Jenis">
                                </optgroup>
                            </select>
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rekening" name="rekening" readonly>
                        </div>
                    </div>
                    {{-- Nomor SPD dan NPWP --}}
                    <div class="mb-3 row">
                        <label for="nomor_spd" class="col-md-2 col-form-label">Nomor SPD</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style=" width: 100%;" id="nomor_spd"
                                name="nomor_spd" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Nomor SPD">
                                </optgroup>
                            </select>
                        </div>
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="npwp" name="npwp" readonly>
                        </div>
                    </div>
                    {{-- Tanggal SPD dan Rekanan --}}
                    <div class="mb-3 row">
                        <label for="tgl_spd" class="col-md-2 col-form-label">Tanggal SPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_spd" name="tgl_spd" required readonly>
                            <input class="form-control" type="text" id="blnawal" name="blnawal" required readonly
                                hidden>
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        {{-- <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%;" id="rekanan"
                                name="rekanan" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Rekanan">
                                    <option value="" disabled selected>Silahkan Pilih Rekanan</option>
                                    @foreach ($daftar_rekanan as $rekanan)
                                        <option value="{{ $rekanan->nmrekan }}" data-pimpinan="{{ $rekanan->pimpinan }}"
                                            data-alamat="{{ $rekanan->alamat }}"
                                            {{ old('rekanan') == $rekanan->nmrekan ? 'selected' : '' }}>
                                            {{ $rekanan->nmrekan }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div> --}}
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rekanan" name="rekanan" readonly>
                        </div>
                    </div>
                    {{-- Kode Sub Kegiatan dan Pimpinan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kode Sub Kegiatan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style=" width: 100%;" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Sub Kegiatan">
                                </optgroup>
                            </select>
                            <input type="hidden" name="kd_program" id="kd_program">
                            <input type="hidden" name="nm_program" id="nm_program">
                            <input type="hidden" name="bidang" id="bidang">
                        </div>
                        <label for="pimpinan" class="col-md-2 col-form-label">Pimpinan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="pimpinan" name="pimpinan" readonly>
                        </div>
                    </div>
                    {{-- Nama Sub Kegiatan dan Alamat --}}
                    <div class="mb-3 row">
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_sub_kegiatan" name="nm_sub_kegiatan"
                                required readonly>
                        </div>
                        <label for="alamat" class="col-md-2 col-form-label">Alamat Perusahaan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="alamat" name="alamat" readonly>
                        </div>
                    </div>
                    {{-- Tanggal Mulai dan Tanggal Akhir --}}
                    <div class="mb-3 row">
                        <label for="tgl_awal" class="col-md-2 col-form-label">Tanggal Mulai</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_awal" name="tgl_awal" required>
                        </div>
                        <label for="tgl_akhir" class="col-md-2 col-form-label">Tanggal Akhir</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir">
                        </div>
                    </div>
                    {{-- Lanjut dan Nomor Kontrak --}}
                    <div class="mb-3 row">
                        {{--  <label for="lanjut" class="col-md-2 col-form-label">Lanjut</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="lanjut"
                                name="lanjut" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Pilihan">
                                    <option value="" disabled selected>...Pilih... </option>
                                    <option value="1">YA</option>
                                    <option value="2">TIDAK</option>
                                </optgroup>
                            </select>
                        </div>  --}}
                        <label for="no_kontrak" class="col-md-2 col-form-label">Nomor Kontrak</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control" id="no_kontrak" name="no_kontrak">
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="simpan_penagihan" class="btn btn-primary btn-md">Simpan</button>
                        <a href="{{ route('sppls.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Detail SPP --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Detail SPP
                    <button type="button" style="float: right" id="tambah_rincian"
                        class="btn btn-primary btn-sm">Tambah Rekening</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_sppls" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Sub Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Kode Sumber</th> {{-- hidden --}}
                                <th>Sumber</th> {{-- hidden --}}
                                {{-- <th>Volume</th>
                                <th>Satuan</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly class="form-control" id="total"
                                name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tambah_rincianspp" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rincian SPP LS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- OPD/UNIT -->
                    <div class="mb-3 row">
                        <label for="opd_unit" class="col-md-2 col-form-label">OPD/Unit</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="opd_unit" readonly name="opd_unit"
                                value="{{ $data_opd->kd_skpd }}">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_opd_unit" readonly name="nm_opd_unit"
                                value="{{ $data_opd->nm_skpd }}">
                        </div>
                    </div>
                    <!-- SUB KEGIATAN -->
                    <div class="mb-3 row">
                        <label for="sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="sub_kegiatan" readonly
                                name="nm_sub_kegiatan">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nmsub_kegiatan" readonly
                                name="nmsub_kegiatan">
                        </div>
                    </div>
                    <!-- REKENING -->
                    <div class="mb-3 row">
                        <label for="kode_rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal" style=" width: 100%;" id="kode_rekening"
                                name="kode_rekening" data-placeholder="Silahkan Pilih">
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_rekening" readonly name="nm_rekening">
                        </div>
                    </div>
                    <!-- SUMBER DANA -->
                    <div class="mb-3 row">
                        <label for="sumber_dana" class="col-md-2 col-form-label">Sumber</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal" style=" width: 100%;" id="sumber_dana"
                                name="sumber_dana" data-placeholder="Silahkan Pilih">
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
                            <input type="text" readonly class="form-control" name="total_spd" id="total_spd">
                        </div>
                        <label for="realisasi_spd" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="realisasi_spd" id="realisasi_spd">
                        </div>
                        <label for="sisa_spd" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="sisa_spd" id="sisa_spd">
                        </div>
                    </div>
                    <!-- ANGGARAN KAS -->
                    <div class="mb-3 row">
                        <label for="total_angkas" class="col-md-2 col-form-label">Total Anggaran Kas</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_angkas" id="total_angkas">
                        </div>
                        <label for="realisasi_angkas" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="realisasi_angkas"
                                id="realisasi_angkas">
                        </div>
                        <label for="sisa_angkas" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="sisa_angkas" id="sisa_angkas">
                        </div>
                    </div>
                    <!-- ANGGARAN PENYUSUNAN -->
                    <div class="mb-3 row">
                        <label for="total_penyusunan" class="col-md-2 col-form-label">Anggaran Penyusunan</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_penyusunan"
                                id="total_penyusunan">
                        </div>
                        <label for="realisasi_penyusunan" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="realisasi_penyusunan"
                                id="realisasi_penyusunan">
                        </div>
                        <label for="sisa_penyusunan" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="sisa_penyusunan"
                                id="sisa_penyusunan">
                        </div>
                    </div>
                    <!-- NILAI SUMBER DANA -->
                    <div class="mb-3 row">
                        <label for="total_sumber" class="col-md-2 col-form-label">Sumber Dana Penyusunan</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_sumber" id="total_sumber">
                        </div>
                        <label for="realisasi_sumber" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="realisasi_sumber"
                                id="realisasi_sumber">
                        </div>
                        <label for="sisa_sumber" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="sisa_sumber" id="sisa_sumber">
                        </div>
                    </div>
                    <!-- Status Anggaran -->
                    <div class="mb-3 row">
                        <label for="status_anggaran" class="col-md-2 col-form-label">Status Anggaran</label>
                        <div class="col-md-10">
                            <input type="text" readonly class="form-control" name="status_anggaran"
                                id="status_anggaran">
                        </div>
                    </div>
                    <!-- Status Angkas -->
                    <div class="mb-3 row">
                        <label for="status_angkas" class="col-md-2 col-form-label">Status Anggaran Kas</label>
                        <div class="col-md-10">
                            <input type="text" readonly class="form-control" name="status_angkas" id="status_angkas">
                        </div>
                    </div>
                    {{-- Volume Output --}}
                    {{-- <div class="mb-3 row">
                        <label for="volume_output" class="col-md-2 col-form-label">Volume Output</label>
                        <div class="col-md-10">
                            <input type="text" disabled class="form-control" name="volume_output" id="volume_output">
                        </div>
                    </div> --}}
                    {{-- Satuan Output --}}
                    {{-- <div class="mb-3 row">
                        <label for="satuan_output" class="col-md-2 col-form-label">Satuan Output</label>
                        <div class="col-md-10">
                            <input type="text" disabled class="form-control" name="satuan_output" id="satuan_output">
                        </div>
                    </div> --}}
                    <!-- Nilai -->
                    <div class="mb-3 row">
                        <label for="nilai_rincian" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai_rincian" id="nilai_rincian"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                        </div>
                        {{-- <div class="col-md-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="minus">
                                <label class="form-check-label" for="minus">
                                    Minus
                                </label>
                            </div>
                        </div> --}}
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_detail_spp" class="btn btn-md btn-primary">Simpan</button>
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
    @include('penatausahaan.pengeluaran.spp_ls.js.create');
@endsection
