@extends('template.app')
@section('title', 'Verifikasi SPM | SIMAKDA')
@section('content')
    <style>
        input[type='checkbox'] {
            width: 20px;
            height: 20px;
            border-radius: 5px;
            border: 2px solid #555;
        }

        input[type="checkbox"] {
            outline: 3px solid black;
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    VERIFIKASI SPM
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Jenis Beban --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style="width: 100%" id="beban" name="beban">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">UP</option>
                                <option value="2">GU</option>
                                <option value="3">TU</option>
                                <option value="4">LS GAJI</option>
                                <option value="5">LS Pihak Ketiga Lainnya</option>
                                <option value="6">LS Barang Jasa</option>
                                <option value="7">GU NIHIL</option>
                            </select>
                        </div>
                    </div>
                    {{-- Kode SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="spm_perskpd" class="col-md-2 col-form-label">Cetak List Antrian SPM Keseluruhan</label>
                        <div class="col-md-6">
                            <button class="btn btn-dark btn-md spm_perskpd" data-jenis="layar"><i
                                    class="uil-print"></i></button>
                            <button class="btn btn-warning btn-md spm_perskpd" data-jenis="pdf"><i
                                    class="fas fa-file-pdf"></i></button>
                            <button class="btn btn-success btn-md spm_perskpd" data-jenis="excel"><i
                                    class="fas fa-file-excel"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    LIST SPM YANG BELUM DI SP2D-KAN
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="spm" class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 10px;text-align:center">No.</th>
                                        <th style="width: 100px;text-align:center">SKPD</th>
                                        <th style="width: 100px;text-align:center">No SPM</th>
                                        <th style="width: 50px;text-align:center">Nilai</th>
                                        <th style="width: 50px;text-align:center">Status</th>
                                        <th style="width: 50px;text-align:center">Aksi</th>
                                        <th style="width: 20px;text-align:center">Terima Berkas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <div id="modal_tampil" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data SPM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SPM --}}
                    <div class="modal-scroll">
                        @method('patch')
                        @csrf
                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">No SPM</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="no_spmtampil" name="no_spmtampil">
                                <input type="text" readonly hidden="form-control" id="statustampil" name="statustampil">
                                <input type="text" readonly hidden="form-control" id="jns_spptampil"
                                    name="jns_spptampil">
                            </div>
                            <label for="no_spm" class="col-md-2 col-form-label">No SPP</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="no_spptampil" name="no_spptampil">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">Kode SKPD</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="kd_skpdtampil"
                                    name="kd_skpdtampil">
                            </div>
                            <label for="no_spm" class="col-md-2 col-form-label">Nama SKPD</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="nm_skpdtampil"
                                    name="nm_skpdtampil">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">Rekanan</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="rekanantampil"
                                    name="rekanantampil">
                            </div>
                            <label for="no_spm" class="col-md-2 col-form-label">Pimpinan</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="pimpinantampil"
                                    name="pimpinantampil">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">No Rek</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="no_rektampil"
                                    name="no_rektampil">
                            </div>
                            <label for="no_spm" class="col-md-2 col-form-label">NPWP</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="npwptampil" name="npwptampil">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">No Tagih</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="no_tagihtampil"
                                    name="no_tagihtampil">
                            </div>
                            <label for="no_spm" class="col-md-2 col-form-label">Bank</label>
                            <div class="col-md-4">
                                <input type="text" readonly class="form-control" id="banktampil" name="banktampil">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">Keperluan</label>
                            <div class="col-md-10">
                                <textarea type="text" class="form-control" id="keperluan" name="keperluan" readonly></textarea>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">Ket Bast</label>
                            <div class="col-md-10">
                                <textarea type="text" class="form-control" id="ket_basttampil" name="ket_basttampil" readonly></textarea>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">Nilai</label>
                            <div class="col-md-10">
                                <input type="text" style="text-align: right" class="form-control" id="nilaitampil"
                                    readonly name="nilaitampil">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">Tanggal Terima & Setujui SPM</label>
                            <div class="col-md-4">
                                <input class="form-control" type="date" id="tgl_terima_spm" name="tgl_terima_spm"
                                    required>
                            </div>
                            <label for="stsspm" class="col-md-2 col-form-label">Status SPM</label>
                            <div class="col-md-4">
                                <select class="form-control select2-multiple" style="width: 100%" id="stsspm"
                                    name="stsspm">
                                    <option value="" disabled selected>Silahkan Pilih</option>
                                    <option value="1">Berkas Lengkap Dan Disetujui</option>
                                    <option value="0">Berkas SPP SPM Ditunda</option>
                                    <option value="2">Berkas SPP SPM Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="no_spm" class="col-md-2 col-form-label">Keterangan</label>
                            <div class="col-md-10">
                                <textarea type="text" class="form-control" id="ket_spm" name="ket_spm"></textarea>
                            </div>

                        </div>
                    </div>
                    <br>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="save" name="save" onclick="setuju_spm();"
                                class="btn btn-primary btn-md ">
                                Simpan Terima SPM</button>
                            <button type="button" class="btn btn-dark btn-md" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modul cetak --}}
    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak SPM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SPM --}}
                    <div class="mb-3 row">
                        <label for="no_spm" class="col-md-2 col-form-label">No SPM</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control" id="no_spm" name="no_spm">
                            <input type="text" hidden class="form-control" id="beban" name="beban">
                            <input type="text" hidden class="form-control" id="kd_skpd" name="kd_skpd">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" id="tanpa_tanggal">
                                <label class="form-check-label" for="tanpa_tanggal">Tanpa Tanggal</label>
                            </div>
                        </div>
                    </div>
                    {{-- Bendahara --}}
                    <div class="mb-3 row">
                        <label for="bendahara" class="col-md-2 col-form-label">Bendahara Pengeluaran</label>
                        <div class="col-md-6">
                            <select name="bendahara" class="form-control" id="bendahara">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($bendahara as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_bendahara" id="nama_bendahara" class="form-control"
                                readonly>
                        </div>
                    </div>
                    {{-- PPTK --}}
                    <div class="mb-3 row">
                        <label for="pptk" class="col-md-2 col-form-label">PPTK/PPK</label>
                        <div class="col-md-6">
                            <select name="pptk" class="form-control" id="pptk">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($pptk as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_pptk" id="nama_pptk" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- PA/KPA --}}
                    <div class="mb-3 row">
                        <label for="pa_kpa" class="col-md-2 col-form-label">PA/KPA</label>
                        <div class="col-md-6">
                            <select name="pa_kpa" class="form-control" id="pa_kpa">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($pa_kpa as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_pa_kpa" id="nama_pa_kpa" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- PPKD --}}
                    <div class="mb-3 row">
                        <label for="ppkd" class="col-md-2 col-form-label">PPKD</label>
                        <div class="col-md-6">
                            <select name="ppkd" class="form-control" id="ppkd">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ppkd as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_ppkd" id="nama_ppkd" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- Jenis --}}
                    <div class="mb-3 row">
                        <label for="jenis_ls" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-6">
                            <select name="jenis_ls" class="form-control" id="jenis_ls">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="1">Gaji Induk, Gaji Terusan, Kekurangan Gaji</option>
                                <option value="2">Gaji Susulan</option>
                                <option value="3">Tambahan Penghasilan</option>
                                <option value="4">Honorarium PNS</option>
                                <option value="5">Honorarium Tenaga Kontrak</option>
                                <option value="6">Pengadaan Barang dan Jasa/Konstruksi/Konsultansi</option>
                                <option value="7">Pengadaan Konsumsi</option>
                                <option value="8">Sewa Rumah Jabatan/Gedung untuk Kantor/Gedung Pertemuan/Tempat
                                    Pertemuan/Tempat Penginapan/Kendaraan</option>
                                <option value="9">Pengadaan Sertifikat Tanah</option>
                                <option value="10">Pengadaan Tanah</option>
                                <option value="11">Hibah Barang dan Jasa pada Pihak Ketiga</option>
                                <option value="12">LS Bantuan Sosial pada Pihak Ketiga</option>
                                <option value="13">Hibah Uang Pada Pihak Ketiga</option>
                                <option value="14">Bantuan Keuangan Pada Kabupaten/Kota</option>
                                <option value="15">Bagi Hasil Pajak dan Bukan Pajak</option>
                                <option value="16">Hibah Konstruksi pada Pihak Ketiga</option>
                                <option value="98">Belanja Operasional KDH/WKDH dan Pimpinan DPRD</option>
                                <option value="99">Pembiayaan pada Pihak Ketiga Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_jenis" id="nama_jenis" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- Baris SPM --}}
                    <div class="mb-3 row">
                        <label for="baris_spm" class="col-md-2 col-form-label">Baris SPM</label>
                        <div class="col-md-6">
                            <input type="number" value="7" min="1" class="form-control" id="baris_spm"
                                name="baris_spm">
                        </div>
                    </div>
                    {{-- Kelengkapan, lampiran --}}
                    <div class="mb-3 row">
                        <label for="berkas_spm" class="col-md-2 col-form-label">Cetak Berkas SPM</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md berkas_spm" data-jenis="pdf"
                                name="berkas_spm_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md berkas_spm" data-jenis="layar"
                                name="berkas_spm">Layar</button>
                            <button type="button" class="btn btn-warning btn-md berkas_spm" data-jenis="download"
                                name="berkas_spm">Download</button>
                        </div>
                        <label for="lampiran" class="col-md-2 col-form-label">Lampiran</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md lampiran" data-jenis="pdf"
                                name="lampiran_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md lampiran" data-jenis="layar"
                                name="lampiran">Layar</button>
                            <button type="button" class="btn btn-warning btn-md lampiran" data-jenis="download"
                                name="lampiran">Download</button>
                        </div>
                    </div>
                    <!-- {{-- Berkas SPM, Tanggung Jawab SPM --}}
                                                                                                                                                                                                                                                                                            <div class="mb-3 row">
                                                                                                                                                                                                                                                                                                <label for="berkas_spm" class="col-md-2 col-form-label">Berkas SPM</label>
                                                                                                                                                                                                                                                                                                <div class="col-md-4">
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-danger btn-md berkas_spm" data-jenis="pdf"
                                                                                                                                                                                                                                                                                                        name="berkas_spm_pdf">PDF</button>
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-dark btn-md berkas_spm" data-jenis="layar"
                                                                                                                                                                                                                                                                                                        name="berkas_spm">Layar</button>
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-warning btn-md berkas_spm" data-jenis="download"
                                                                                                                                                                                                                                                                                                        name="berkas_spm">Download</button>
                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                <label for="tanggung_jawab" class="col-md-2 col-form-label">Tanggung Jawab SPM</label>
                                                                                                                                                                                                                                                                                                <div class="col-md-4">
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-danger btn-md tanggung_jawab" data-jenis="pdf"
                                                                                                                                                                                                                                                                                                        name="tanggung_pdf">PDF</button>
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-dark btn-md tanggung_jawab" data-jenis="layar"
                                                                                                                                                                                                                                                                                                        name="tanggung_jawab">Layar</button>
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-warning btn-md tanggung_jawab" data-jenis="download"
                                                                                                                                                                                                                                                                                                        name="tanggung_jawab">Download</button>
                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                            </div> -->
                    {{-- Ringkasan, Pernyataan --}}
                    <div class="mb-3 row">
                        <label for="kelengkapan" class="col-md-2 col-form-label">Kelengkapan</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md kelengkapan" data-jenis="pdf"
                                name="kelengkapan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md kelengkapan" data-jenis="layar"
                                name="kelengkapan">Layar</button>
                            <button type="button" class="btn btn-warning btn-md kelengkapan" data-jenis="download"
                                name="kelengkapan">Download</button>
                        </div>
                        <label for="pernyataan" class="col-md-2 col-form-label">Pernyataan</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md pernyataan" data-jenis="pdf"
                                name="pernyataan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pernyataan" data-jenis="layar"
                                name="pernyataan">Layar</button>
                            <button type="button" class="btn btn-warning btn-md pernyataan" data-jenis="download"
                                name="pernyataan">Download</button>
                        </div>
                    </div>
                    <!-- {{-- Pengantar --}}
                                                                                                                                                                                                                                                                                            <div class="mb-3 row">
                                                                                                                                                                                                                                                                                                <label for="pengantar" class="col-md-2 col-form-label">Pengantar</label>
                                                                                                                                                                                                                                                                                                <div class="col-md-4">
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-danger btn-md pengantar" data-jenis="pdf"
                                                                                                                                                                                                                                                                                                        name="pengantar_pdf">PDF</button>
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-dark btn-md pengantar" data-jenis="layar"
                                                                                                                                                                                                                                                                                                        name="pengantar">Layar</button>
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-warning btn-md pengantar" data-jenis="download"
                                                                                                                                                                                                                                                                                                        name="pengantar">Download</button>
                                                                                                                                                                                                                                                                                                </div> -->
                </div>
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.verifikasi_spm.js.cetak')
@endsection
