@extends('template.app')
@section('title', 'Input SPP GU | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input SPP
                </div>
                <div class="card-body">
                    @csrf
                    {{-- NO SPP dan Tanggal --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No. SPP</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="no_spp" name="no_spp" readonly>
                                <span class="input-group-btn">
                                    <button type="button" id="cari" class="btn btn-primary"><i
                                            class="uil-refresh"></i></button>
                                </span>
                            </div>
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_spp" name="tgl_spp" required>
                            <input class="form-control" type="date" id="tgl_lalu" name="tgl_lalu" required readonly
                                hidden value="{{ $tanggal_lalu->tgl_spp }}">
                            <input class="form-control" type="text" id="bulan" name="bulan" required readonly
                                hidden>
                            <input class="form-control" type="text" id="no_urut" name="no_urut" required readonly
                                hidden>
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $skpd->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $skpd->nm_skpd }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- No SPD --}}
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">No. SPD</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%;" id="no_spd"
                                name="no_spd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_spd as $spd)
                                    <option value="{{ $spd->no_spd }}">
                                        {{ $spd->no_spd }} | {{ $spd->tgl_spd }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- NO LPJ --}}
                    <div class="mb-3 row">
                        <label for="no_lpj" class="col-md-2 col-form-label">No. LPJ</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%;" id="no_lpj"
                                name="no_lpj">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_lpj as $lpj)
                                    <option value="{{ $lpj->no_lpj }}">
                                        {{ $lpj->no_lpj }} | {{ $lpj->tgl_lpj }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Kode dan Nama Bank --}}
                    <div class="mb-3 row">
                        <label for="bank" class="col-md-2 col-form-label">Kode Bank</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%;" id="bank"
                                name="bank">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_bank as $bank)
                                    <option value="{{ $bank->bank }}" data-nama="{{ $bank->nama_bank }}">
                                        {{ $bank->bank }} | {{ $bank->nama_bank }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="nm_bank" class="col-md-2 col-form-label">Nama Bank</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_bank" name="nm_bank" required readonly>
                        </div>
                    </div>
                    {{-- Rekening Bank dan Nama Rekening --}}
                    <div class="mb-3 row">
                        <label for="rekening" class="col-md-2 col-form-label">Rekening Bank</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%;" id="rekening"
                                name="rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_rekening as $rekening)
                                    <option value="{{ $rekening->rekening }}" data-nama="{{ $rekening->nm_rekening }}"
                                        data-npwp="{{ $rekening->npwp }}">
                                        {{ $rekening->rekening }} | {{ $rekening->nm_rekening }} | {{ $rekening->npwp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="nm_rekening" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_rekening" name="nm_rekening" required
                                readonly>
                        </div>
                    </div>
                    {{-- Jenis Beban dan NPWP --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%;" id="beban"
                                name="beban">
                                <option value="2" selected>GU</option>
                            </select>
                        </div>
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="npwp" name="npwp" required readonly>
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
                    <div class="mb-6 row" style="text-align;center">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('spp_gu.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rincian SPP GU --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rincian SPP GU
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_spp" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Unit</th>
                                <th>No Bukti</th>
                                <th>Sub Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Sumber</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total" name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.spp_gu.js.create');
@endsection
