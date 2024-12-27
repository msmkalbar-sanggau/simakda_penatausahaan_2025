@extends('template.app')
@section('title', 'Edit Penerimaan BOS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Penerimaan BOS
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Kode dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $terima->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $terima->nm_skpd }}">
                        </div>
                    </div>
                    {{-- No dan Tanggal Terima --}}
                    <div class="mb-3 row">
                        <label for="no_terima" class="col-md-2 col-form-label">No. Terima</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_terima" name="no_terima"
                                value="{{ $terima->no_terima }}" required readonly>
                        </div>
                        <label for="tgl_terima" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_terima" name="tgl_terima" required
                                value="{{ $terima->tgl_terima }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- Sub Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_kegiatan as $kegiatan)
                                    <option value="{{ $kegiatan->kd_sub_kegiatan }}"
                                        data-nama="{{ $kegiatan->nm_sub_kegiatan }}"
                                        {{ $kegiatan->kd_sub_kegiatan == $terima->kd_sub_kegiatan ? 'selected' : '' }}>
                                        {{ $kegiatan->kd_sub_kegiatan }} | {{ $kegiatan->nm_sub_kegiatan }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Rekening --}}
                    <div class="mb-3 row">
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="rekening"
                                name="rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_rekening as $rekening)
                                    <option value="{{ $rekening->kd_rek6 }}" data-nama="{{ $rekening->nm_rek6 }}"
                                        {{ $rekening->kd_rek6 == $terima->kd_rek6 ? 'selected' : '' }}>
                                        {{ $rekening->kd_rek6 }} | {{ $rekening->nm_rek6 }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- SATDIK --}}
                    <div class="mb-3 row">
                        <label for="satdik" class="col-md-2 col-form-label">SATDIK</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="satdik" name="satdik">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1" data-nama="SMA/SMK NEGERI"
                                    {{ $terima->kd_satdik == '1' ? 'selected' : '' }}>SMA/SMK NEGERI</option>
                                <option value="2" data-nama="SMA/SMK SWASTA"
                                    {{ $terima->kd_satdik == '2' ? 'selected' : '' }}>SMA/SMK SWASTA</option>
                                <option value="3" data-nama="DIKSUS"
                                    {{ $terima->kd_satdik == '3' ? 'selected' : '' }}>DIKSUS</option>
                                </option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-2 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan">{{ $terima->keterangan }}</textarea>
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right"
                                value="{{ $terima->nilai }}">
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('penerimaan_bos.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.penerimaan_bos.js.edit');
@endsection
