@extends('template.app')
@section('title', 'Verifikasi SPP | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-md btn-success">Hijau : SPP sudah di verifikasi</button>
                    <button class="btn btn-md btn-warning">Kuning : SPP di kembalikan</button>
                    <button class="btn btn-md btn-light" style="background-color: white; border-color:grey">Putih : SPP sudah
                        dibuat SPM</button>
                    <a href="{{ route('verifikasi_spp.create') }}" id="tambah_spp_ls" class="btn btn-primary"
                        style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="verifikasi_spp" class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 10px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Nomor SPP</th>
                                        <th style="width: 55px;text-align:center">Tanggal</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
                                        <th style="width: 20px;text-align:center">Keterangan</th>
                                        <th style="width: 180px;text-align:center">Aksi</th>
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

    {{-- modul cetak --}}
    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Kelengkapan SPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SPM --}}
                    <div class="mb-3 row">
                        <label for="no_spp_modal" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control" id="no_spp_modal" name="no_spp_modal">
                        </div>
                    </div>
                    {{-- PPTK --}}
                    <div class="mb-3 row">
                        <label for="pptk" class="col-md-2 col-form-label">PPK</label>
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
                        <label for="kelengkapan" class="col-md-2 col-form-label">Kelengkapan SPM</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md kelengkapan" data-jenis="pdf"
                                name="kelengkapan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md kelengkapan" data-jenis="layar"
                                name="kelengkapan">Layar</button>
                            <button type="button" class="btn btn-warning btn-md kelengkapan" data-jenis="download"
                                name="kelengkapan">Download</button>
                        </div>
                    </div>

                    {{-- Surat Pernyataan --}}
                    <div class="mb-3 row">
                        <label for="pernyataan" class="col-md-2 col-form-label">Surat Pernyataan</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md pernyataan" data-jenis="pdf"
                                name="pernyataan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pernyataan" data-jenis="layar"
                                name="pernyataan">Layar</button>
                            <button type="button" class="btn btn-warning btn-md pernyataan" data-jenis="download"
                                name="pernyataan">Download</button>
                        </div>
                    </div>

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
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.verifikasi_spp.js.cetak')
@endsection
