@extends('template.app')
@section('title', 'Input Setor CMS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Setor CMS
                </div>
                <div class="card-body">
                    @csrf
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($skpd as $kode)
                                    <option value="{{ $kode->kd_skpd }}" data-nama="{{ $kode->nm_skpd }}">
                                        {{ $kode->kd_skpd }} | {{ $kode->nm_skpd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly>
                        </div>
                    </div>
                    {{-- No Kas dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required readonly>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly value="{{ tahun_anggaran() }}" hidden>
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal Kas</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_kas" name="tgl_kas">
                        </div>
                    </div>
                    {{-- Jenis Beban dan Nama Beban --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="beban" name="beban">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1"> UP/GU</option>
                                <option value="3"> TU</option>
                                <option value="4"> LS Gaji</option>
                                <option value="6"> LS Barang Jasa</option>
                                <option value="5"> LS Pihak Ketiga Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="nama_beban" name="nama_beban" readonly>
                        </div>
                    </div>
                    {{-- Rek. Bank Bendahara Pengeluaran (UNIT) dan Kode Unit --}}
                    <div class="mb-3 row">
                        <label for="rekening_awal" class="col-md-2 col-form-label">Rek. Bank Bendahara Pengeluaran
                            (UNIT)</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening_awal"
                                name="rekening_awal">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="{{ $rekening_awal->rekening }}">{{ $rekening_awal->rekening }}</option>
                            </select>
                        </div>
                        <label for="kd_unit" class="col-md-2 col-form-label">Kode Unit</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="kd_unit" name="kd_unit" readonly
                                value="{{ $kd_skpd }}">
                        </div>
                    </div>
                    {{-- Rek. Bank Bendahara Pengeluaran (SKPD) dan Nama Rek. Tujuan --}}
                    <div class="mb-3 row">
                        <label for="rekening_tujuan" class="col-md-2 col-form-label">Rek. Bank Bendahara Pengeluaran
                            (SKPD)</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening_tujuan"
                                name="rekening_tujuan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($rekening_tujuan as $rekening)
                                    <option value="{{ $rekening->rekening }}" data-nama="{{ $rekening->nm_rekening }}">
                                        {{ $rekening->rekening }} |
                                        {{ $rekening->nm_rekening }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="nama_tujuan" class="col-md-2 col-form-label">Nama Rek. Tujuan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nama_tujuan" name="nama_tujuan" readonly>
                        </div>
                    </div>
                    {{-- Nama Bank Tujuan --}}
                    <div class="mb-3 row">
                        <label for="bank_tujuan" class="col-md-2 col-form-label">Nama Bank Tujuan</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="bank_tujuan"
                                name="bank_tujuan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($bank_tujuan as $bank)
                                    <option value="{{ $bank->kode }}">{{ $bank->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Sisa Kas Bank --}}
                    <div class="mb-3 row">
                        <label for="sisa_bank" class="col-md-2 col-form-label">Sisa Kas Bank</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="sisa_bank" id="sisa_bank"
                                style="text-align: right" readonly value="{{ rupiah($sisa_bank->sisa) }}">
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_setor" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.setor.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.list_setor_cms.js.create');
@endsection
