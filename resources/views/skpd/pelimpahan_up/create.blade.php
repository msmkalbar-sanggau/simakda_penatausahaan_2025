@extends('template.app')
@section('title', 'Input Pelimpahan UP | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Pelimpahan UP
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Tujuan SKPD dan Sisa Kas Bank --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Tujuan SKPD</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($tujuan_skpd as $skpd)
                                    <option value="{{ $skpd->kd_skpd }}" data-nm_skpd="{{ $skpd->nm_skpd }}"
                                        data-skpd_sumber="{{ $skpd->skpd }}" data-skpd_ringkas="{{ $skpd->kd_ringkas }}">
                                        {{ $skpd->kd_skpd }} - {{ $skpd->nm_skpd }}</option>
                                @endforeach
                            </select>
                            <input class="form-control" type="text" id="skpd_sumber" name="skpd_sumber" hidden readonly>
                            <input class="form-control" type="text" id="skpd_ringkas" name="skpd_ringkas" hidden
                                readonly>
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- Nomor dan Tanggal Kas --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No Kas</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_kas" name="no_kas" required readonly>
                        </div>
                        <label for="tgl_kas" class="col-md-2 col-form-label">Tanggal Kas</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_kas" name="tgl_kas">
                        </div>
                    </div>
                    {{-- Rek. Bank Bendahara dan Rek. Bank Tujuan --}}
                    <div class="mb-3 row">
                        <label for="rekening_bendahara" class="col-md-2 col-form-label">Rek. Bank Bendahara</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening_bendahara"
                                name="rekening_bendahara">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="{{ $rekening_bendahara->rekening }}">{{ $rekening_bendahara->rekening }}
                                </option>
                            </select>
                        </div>
                        <label for="rekening_tujuan" class="col-md-2 col-form-label">Rek. Bank Tujuan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening_tujuan"
                                name="rekening_tujuan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($rekening_tujuan as $rekening)
                                    <option value="{{ $rekening->rekening }}"
                                        data-nm_rekening="{{ $rekening->nm_rekening }}"
                                        data-nm_bank="{{ $rekening->nm_bank }}">{{ $rekening->rekening }} |
                                        {{ $rekening->nm_rekening }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Nama Bank Tujuan dan Nama Rek. Tujuan --}}
                    <div class="mb-3 row">
                        <label for="bank_tujuan" class="col-md-2 col-form-label">Nama Bank Tujuan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="bank_tujuan" name="bank_tujuan" required
                                readonly>
                        </div>
                        <label for="nama_tujuan" class="col-md-2 col-form-label">Nama Rek. Tujuan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nama_tujuan" name="nama_tujuan" required
                                readonly>
                        </div>
                    </div>
                    {{-- Jenis Beban dan Nilai --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="beban" name="beban">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">UP/GU</option>
                                <option value="3">TU</option>
                                <option value="4">LS GAJI</option>
                                <option value="6">LS Barang Jasa</option>
                                <option value="5">LS Piihak Ketiga Lainnya</option>
                            </select>
                            <input type="text" class="form-control" name="ketcms" id="ketcms" hidden readonly>
                        </div>
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                        </div>
                    </div>
                    {{-- Sisa Kas Bank --}}
                    <div class="mb-3 row">
                        <label for="sisa_kas" class="col-md-2 col-form-label">Sisa Kas Bank</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="sisa_kas" name="sisa_kas" required readonly
                                value="{{ rupiah($sisa_bank->sisa) }}" style="text-align: right">
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
                            <button id="simpan_pelimpahan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.pelimpahan.up_index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.pelimpahan_up.js.create');
@endsection
