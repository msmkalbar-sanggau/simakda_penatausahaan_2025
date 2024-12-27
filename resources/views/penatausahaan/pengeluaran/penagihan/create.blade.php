@extends('template.app')
@section('title', 'Tambah Penagihan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data Penagihan
                </div>
                <div class="card-body">
                    @csrf
                    <!-- No Tersimpan -->
                    <div class="mb-3 row">
                        <label for="no_tersimpan" class="col-md-2 col-form-label">No Tersimpan</label>
                        <div class="col-md-10">
                            <input type="text" readonly class="form-control" name="no_tersimpan" id="no_tersimpan">
                        </div>
                    </div>
                    <!-- No. Bast / Penagihan Tanggal Penagihan -->
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No.BAST / Penagihan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required>
                        </div>
                        <label for="tgl_bukti" class="col-md-2 col-form-label">Tanggal Penagihan</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_bukti" name="tgl_bukti">
                            <input type="text" class="form-control" id="bulan" name="bulan" hidden>
                        </div>
                    </div>
                    <!-- Kode SKPD Nama SKPD -->
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode OPD / Unit</label>
                        <div class="col-md-4">
                            <input type="text" readonly name="kd_skpd" id="kd_skpd" value="{{ $kd_skpd }}"
                                class="form-control">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama OPD / Unit</label>
                        <div class="col-md-4">
                            <input class="form-control" value="{{ $skpd->nm_skpd }}" readonly type="text"
                                placeholder="Silahkan isi dengan nama pelaksana pekerjaan" id="nm_skpd" name="nm_skpd">
                        </div>
                    </div>
                    <!-- Keterangan Keterangan BAST -->
                    <div class="mb-3 row">
                        <label for="ket" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-4">
                            <textarea class="form-control" type="text" placeholder="Silahkan isi dengan keterangan" id="ket"
                                name="ket"></textarea>
                        </div>
                        <label for="ket_bast" class="col-md-2 col-form-label">Keterangan (BA)</label>
                        <div class="col-md-4">
                            <textarea type="text" name="ket_bast" placeholder="Silahkan isi dengan keterangan (BA)" id="ket_bast"
                                class="form-control"></textarea>
                        </div>
                    </div>


                    <!-- No BA Penyelesaian pekerjaan & serah terima pekerjaan  -->
                    <div class="mb-3 row">
                        <label for="bapp" class="col-md-2 col-form-label">No BA Penyelesaian Pekerjaan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text"
                                placeholder="Silahkan isi No BA Penyelesaian Pekerjaan" id="bapp"
                                name="bapp"></input>
                        </div>
                        <label for="basthp" class="col-md-2 col-form-label">No BA Serah Terima Hasil Pekerjaan</label>
                        <div class="col-md-4">
                            <input type="text" name="basthp"
                                placeholder="Silahkan isi dengan NO BA Serah Terima Hasil Pekerjaan " id="basthp"
                                class="form-control"></input>
                        </div>
                    </div>
                    <!-- No BA Pembayaran  -->
                    <div class="mb-3 row">
                        <label for="bap" class="col-md-2 col-form-label">No BA Pembayaran</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" placeholder="Silahkan isi No BA Pembayaran"
                                id="bap" name="bap"></input>
                        </div>
                    </div>
                    <!-- Status Jenis -->
                    <div class="mb-3 row">
                        <label for="status_bayar" class="col-md-2 col-form-label">Status</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%;" id="status_bayar"
                                name="status_bayar" data-placeholder="Silahkan Pilih">
                                <option value="" disabled selected>Silahkan Pilih Status</option>
                                <option value="1">Selesai</option>
                                <option value="0">Belum Selesai</option>
                            </select>
                        </div>
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%;" id="jenis"
                                name="jenis">
                                <option value=" ">Tanpa Termin / Sekali Pembayaran</option>
                                <option value="1">Konstruksi Dalam Pengerjaan</option>
                                <option value="2">Uang Muka</option>
                                <option value="3">Hutang Tahun Lalu</option>
                                <option value="4">Perbulan</option>
                                <option value="5">Bertahap</option>
                                {{--  <option value="6">Berdasarkan Progres / Pengajuan Pekerjaan</option>  --}}
                            </select>
                        </div>
                    </div>
                    <!-- No Kontrak Rekanan -->
                    <div class="mb-3 row">
                        <label for="no_kontrak" class="col-md-2 col-form-label">Nomor Kontrak</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style=" width: 100%;" id="no_kontrak"
                                name="no_kontrak" data-placeholder="Silahkan Pilih">
                                <option value="" disabled selected>Kontrak | Nilai Kontrak | Lalu</option>
                                @foreach ($daftar_kontrak as $kontrak)
                                    <option value="{{ $kontrak->no_kontrak }}" data-nilai="{{ $kontrak->nilai }}"
                                        data-lalu="{{ $kontrak->lalu }}" data-nmpel="{{ $kontrak->nmpel }}">
                                        {{ $kontrak->no_kontrak }} | {{ $kontrak->nilai }} | {{ $kontrak->lalu }} |
                                        {{ $kontrak->nmpel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <input class="form-control @error('nmpel') is-invalid @enderror" type="text" readonly
                                value="{{ old('nmpel') }}" id="nmpel" name="nmpel">
                            @error('nmpel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="simpan_penagihan" class="btn btn-primary btn-md">Simpan</button>
                        <a href="{{ route('penagihan.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rincian Penagihan --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rincian Penagihan
                    <button type="button" style="float: right" id="tambah_rincian"
                        class="btn btn-primary btn-md">Tambah Rincian</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_penagihan" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>No SP2D</th>
                                <th>Kode Sub Kegiatan</th>
                                <th>Nama Sub Kegiatan</th>
                                <th>Kode Rekening</th>
                                <th>REK 13</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Lalu</th>
                                <th>SP2D</th>
                                <th>Anggaran</th>
                                <th>Sumber</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Totalan --}}
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 row">
                        <label for="total_nilai" class="col-md-4 col-form-label">Total</label>
                        <div class="col-md-8">
                            <input type="text" readonly style="text-align: right" class="form-control"
                                name="total_nilai" id="total_nilai">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nilai_lalu" class="col-md-4 col-form-label">Nilai
                            Lalu</label>
                        <div class="col-md-8">
                            <input type="text" readonly style="text-align: right" class="form-control"
                                name="nilai_lalu" id="nilai_lalu">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nilai_kontrak" class="col-md-4 col-form-label">Nilai
                            Kontrak</label>
                        <div class="col-md-8">
                            <input type="text" readonly style="text-align: right" class="form-control"
                                name="nilai_kontrak" id="nilai_kontrak">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="sisa_kontrak" class="col-md-4 col-form-label">Sisa
                            Kontrak</label>
                        <div class="col-md-8">
                            <input type="text" readonly style="text-align: right" class="form-control"
                                name="sisa_kontrak" id="sisa_kontrak">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tambah-penagihan" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rincian Penagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- SUB KEGIATAN -->
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style=" width: 100%;" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan" data-placeholder="Silahkan Pilih">
                                <option value="" disabled selected>Kode Sub Kegiatan | Nama Sub Kegiatan</option>
                                @foreach ($daftar_sub_kegiatan as $sub_kegiatan)
                                    <option value="{{ $sub_kegiatan->kd_sub_kegiatan }}"
                                        data-nama="{{ $sub_kegiatan->nm_sub_kegiatan }}">
                                        {{ $sub_kegiatan->kd_sub_kegiatan }} | {{ $sub_kegiatan->nm_sub_kegiatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nm_sub_kegiatan') is-invalid @enderror"
                                value="{{ old('nm_sub_kegiatan') }}" id="nm_sub_kegiatan" readonly
                                name="nm_sub_kegiatan">
                            @error('nm_sub_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- REKENING -->
                    <div class="mb-3 row">
                        <label for="kode_rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style=" width: 100%;" id="kode_rekening"
                                name="kode_rekening" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Rekening">
                                    <option value="" disabled selected>Kode Rekening Ang. | Kode Rekening | Nama
                                        Rekening | Lalu | SP2D | Anggaran</option>
                                </optgroup>
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
                            <select class="form-control select2-multiple" style=" width: 100%;" id="sumber_dana"
                                name="sumber_dana" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Sumber Dana">
                                    <option value="" disabled selected>Sumber Dana</option>
                                </optgroup>
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
                    <!-- ANGKAS -->
                    <div class="mb-3 row">
                        <label for="total_angkas" class="col-md-2 col-form-label">Angkas</label>
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
                    <!-- PAGU -->
                    <div class="mb-3 row">
                        <label for="total_pagu" class="col-md-2 col-form-label">Pagu</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="total_pagu" id="total_pagu">
                        </div>
                        <label for="realisasi_pagu" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="realisasi_pagu"
                                id="realisasi_pagu">
                        </div>
                        <label for="sisa_pagu" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="sisa_pagu" id="sisa_pagu">
                        </div>
                    </div>
                    <!-- NILAI SUMBER DANA -->
                    <div class="mb-3 row">
                        <label for="nilai_sumber_dana" class="col-md-2 col-form-label">Nilai Sumber Dana</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control" name="nilai_sumber_dana"
                                id="nilai_sumber_dana">
                        </div>
                        <label for="realisasi_sumber_dana" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="realisasi_sumber_dana"
                                id="realisasi_sumber_dana">
                        </div>
                        <label for="sisa_sumber_dana" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control" name="sisa_sumber_dana"
                                id="sisa_sumber_dana">
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
                        <label for="status_angkas" class="col-md-2 col-form-label">Status Angkas</label>
                        <div class="col-md-10">
                            <input type="text" readonly class="form-control" name="status_angkas" id="status_angkas">
                        </div>
                    </div>
                    <!-- Nilai -->
                    <div class="mb-3 row">
                        <label for="nilai_penagihan" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai_penagihan" id="nilai_penagihan"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan-btn" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="total_input_penagihan" style="text-align: right"
                        class="col-md-9 col-form-label">Total</label>
                    <div class="col-md-3" style="padding-right: 30px">
                        <input type="text" width="100%" class="form-control" readonly name="total_input_penagihan"
                            id="total_input_penagihan" style="text-align: right;background-color:white;border:none">
                    </div>
                </div>
                <div class="card" style="margin: 4px">
                    <div class="table-responsive">
                        <table class="table table-bordered border-primary mb-0" style="width: 100%" id="input_penagihan">
                            <thead>
                                <tr>
                                    <th>No Bukti</th> {{-- hidden --}}
                                    <th>No SP2D</th> {{-- hidden --}}
                                    <th>Kode Sub Kegiatan</th>
                                    <th>Nama Sub Kegiatan</th> {{-- hidden --}}
                                    <th>REK LO</th>
                                    <th>REK 13</th>
                                    <th>Nama Rekening</th>
                                    <th>Rupiah</th>
                                    <th>Lalu</th> {{-- hidden --}}
                                    <th>SP2D</th> {{-- hidden --}}
                                    <th>Anggaran</th>
                                    <th>Sumber</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.penagihan.js.create')
@endsection
