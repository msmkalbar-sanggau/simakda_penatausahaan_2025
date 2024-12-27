@extends('template.app')
@section('title', 'SPM | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-md btn-success">Hijau : SP2D Sudah Dibuat</button>
                    <button class="btn btn-md btn-warning">Kuning : SPM Sudah Diverifikasi Perbend</button>
                    <button class="btn btn-md btn-danger">Merah : SPP-SPM DIbatalkan</button>
                    <a href="{{ route('spm.create') }}" id="tambah_spp_ls" class="btn btn-primary" style="float: right;"
                        {{ $kunci == 1 ? 'hidden' : '' }}>Tambah</a>
                    <a href="javascript:void(0);" class="btn btn-success" style="float: right;" data-bs-toggle="tooltip"
                        onclick="cetak2()" data-target="#cetakModal">
                        <i class="uil-print"></i> Cetak Pengiriman SPM</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="spm" class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 10px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Nomor SPM</th>
                                        <th style="width: 50px;text-align:center">Tanggal</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($data_spm as $spm)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $spm->no_spm }}</td>
                                            <td>{{ \Carbon\Carbon::parse($spm->tgl_spm)->locale('id')->isoFormat('DD MMMM Y') }}
                                            </td>
                                            <td style="text-align: justify">{{ Str::limit($spm->keperluan, '20') }}</td>
                                            <td>
                                                <a href="{{ route('spm.tambah_potongan', $spm->no_spm) }}"
                                                    id="tambah_potongan" class="btn btn-secondary btn-sm"><i
                                                        class="uil-percentage"></i></a>
                                                <button type="button"
                                                    onclick="cetak('{{ $spm->no_spm }}', '{{ $spm->jns_spp }}', '{{ $spm->kd_skpd }}')"
                                                    class="btn btn-success btn-sm"><i class="uil-print"></i></button>
                                                <button type="button"
                                                    onclick="batal_spm('{{ $spm->no_spm }}', '{{ $spm->jns_spp }}', '{{ $spm->kd_skpd }}', '{{ $spm->no_spp }}')"
                                                    class="btn btn-danger btn-sm"><i class="uil-ban"></i></button>
                                                <a href="{{ route('spm.tampil', $spm->no_spm) }}"
                                                    class="btn btn-info btn-sm"><i class="uil-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    {{-- modal batal spm spp --}}
    <div id="spm_batal" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">KETERANGAN PEMBATALAN SPM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spm" class="col-md-12 col-form-label">No SPM</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="no_spm_batal" name="no_spm_batal">
                            <input type="text" readonly class="form-control" id="beban_batal" name="beban_batal" hidden>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="no_spp_batal" name="no_spp_batal">
                        </div>
                    </div>
                    {{-- <div class="mb-3 row">
                        <label for="batal_spm" class="col-md-12 col-form-label">BATAL SPM-SPP</label>
                        <div class="col-md-2">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" id="batal_spm">
                            </div>
                        </div>
                        <div class="col-md-10">
                            <h5 style="font-size: 12px">(Jika Ya, Batal SPM - SPP)</h5>
                            <h5 style="font-size: 12px">(Jika Tidak, Batal SPM Saja)</h5>
                        </div>
                    </div> --}}
                    <div class="mb-3 row">
                        <label for="keterangan_batal" class="col-md-12 col-form-label">KETERANGAN PEMBATALAN SPM</label>
                        <div class="col-md-12">
                            <textarea type="text" class="form-control" id="keterangan_batal" name="keterangan_batal"></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-danger" id="input_batal"><i
                                    class="uil-ban"></i>Batal SPM - SPP</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i>Keluar</button>
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
                    {{-- <div class="mb-3 row">
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
                    </div> --}}
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
                    {{-- Berkas SPM, Tanggung Jawab SPM --}}
                    {{-- <div class="mb-3 row">
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
                    </div> --}}
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
                    {{-- Kelengkapan SPM, SPTJM SPM --}}
                    <div class="mb-3 row">
                        <label for="tanggung_jawab" class="col-md-2 col-form-label">Tanggung Jawab SPM</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md tanggung_jawab" data-jenis="pdf"
                                name="tanggung_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md tanggung_jawab" data-jenis="layar"
                                name="tanggung_jawab">Layar</button>
                            <button type="button" class="btn btn-warning btn-md tanggung_jawab" data-jenis="download"
                                name="tanggung_jawab">Download</button>
                        </div>
                    </div>
                    {{-- Pengantar --}}
                    {{-- <div class="mb-3 row">
                        <label for="pengantar" class="col-md-2 col-form-label">Pengantar</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md pengantar" data-jenis="pdf"
                                name="pengantar_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pengantar" data-jenis="layar"
                                name="pengantar">Layar</button>
                            <button type="button" class="btn btn-warning btn-md pengantar" data-jenis="download"
                                name="pengantar">Download</button>
                        </div>
                    </div> --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="modal_cetak2" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Berkas SPM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Periode --}}
                    <div class="mb-3 row">
                        <label for="periode2" class="col-md-2 col-form-label">Periode</label>
                        <div class="col-md-3" id="periode1">
                            <input type="date" id="tanggal1" name="tanggal1" class="form-control">
                        </div>
                        <div class="col-md-3" id="periode2">
                            <input type="date" id="tanggal2" name="tanggal2" class="form-control">
                        </div>
                    </div>
                    {{-- PPTK --}}
                    <div class="mb-3 row">
                        <label for="pptk2" class="col-md-2 col-form-label">PPTK/PPK</label>
                        <div class="col-md-6">
                            <select name="pptk2" class="form-control" id="pptk2">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($pptk as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_pptk2" id="nama_pptk2" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- TGL TTD --}}
                    <div class="mb-3 row">
                        <label for="tgl_ttd2" class="col-md-2 col-form-label">Tanggal TTD</label>
                        <div class="col-md-6" id="tgl_ttd2">
                            <input type="date" id="tgl_ttd" name="tgl_ttd" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="pilih_spm" class="col-md-2 col-form-label">Pilih SPM</label>
                        <div class="col-md-6">
                            <div id="spm-list">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md cetak_berkas" data-jenis="pdf"
                                name="cetak_berkas_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md cetak_berkas" data-jenis="layar"
                                name="cetak_berkas">Layar</button>
                            <button type="button" class="btn btn-warning btn-md cetak_berkas" data-jenis="download"
                                name="cetak_berkas">Download</button>
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
    @include('penatausahaan.pengeluaran.spm.js.cetak')
@endsection
