@extends('template.app')
@section('title', 'SPP TU | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List SPP TU
                    <a href="{{ route('spp_tu.tambah') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="spp_tu" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">No SPP</th>
                                        <th style="width: 100px;text-align:center">Tanggal</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
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
                    <h5 class="modal-title">Cetak SPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No. SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No. SPP</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control" id="no_spp" name="no_spp">
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
                        <div class="col-md-10">
                            <select name="bendahara" class="form-control select-modal" id="bendahara">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd1 as $bendahara)
                                    <option value="{{ $bendahara->nip }}">
                                        {{ $bendahara->nip }} | {{ $bendahara->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- PPTK --}}
                    <div class="mb-3 row">
                        <label for="pptk" class="col-md-2 col-form-label">PPTK</label>
                        <div class="col-md-10">
                            <select name="pptk" class="form-control select-modal" id="pptk">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd2 as $pptk)
                                    <option value="{{ $pptk->nip }}">
                                        {{ $pptk->nip }} | {{ $pptk->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- PA/KPA --}}
                    <div class="mb-3 row">
                        <label for="pa_kpa" class="col-md-2 col-form-label">PA/KPA</label>
                        <div class="col-md-10">
                            <select name="pa_kpa" class="form-control select-modal" id="pa_kpa">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd3 as $pa)
                                    <option value="{{ $pa->nip }}">
                                        {{ $pa->nip }} | {{ $pa->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- PPKD --}}
                    <div class="mb-3 row">
                        <label for="ppkd" class="col-md-2 col-form-label">PPKD</label>
                        <div class="col-md-10">
                            <select name="ppkd" class="form-control select-modal" id="ppkd">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd4 as $pa)
                                    <option value="{{ $pa->nip }}">
                                        {{ $pa->nip }} | {{ $pa->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Pengantar, Ringkasan dan Format Permandagri 77 --}}
                    <div class="mb-3 row">
                        <label for="pengantar" class="col-md-2 col-form-label">Pengantar</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md pengantar_layar" data-jenis="pdf"
                                name="pengantar_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pengantar_layar" data-jenis="layar"
                                name="pengantar_layar">Layar</button>
                        </div>
                        <label for="ringkasan" class="col-md-2 col-form-label">Ringkasan</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md ringkasan_layar" data-jenis="pdf"
                                name="ringkasan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md ringkasan_layar" data-jenis="layar"
                                name="ringkasan_layar">Layar</button>
                        </div>
                        <label for="ringkasan" style="text-align: center" class="col-md-4 col-form-label">Format
                            Permendagri 77</label>
                    </div>
                    {{-- Rincian, Pernyataan dan SPP --}}
                    <div class="mb-3 row">
                        <label for="rincian" class="col-md-2 col-form-label">Rincian</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md rincian_layar" data-jenis="pdf"
                                name="rincian_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md rincian_layar" data-jenis="layar"
                                name="rincian_layar">Layar</button>
                        </div>
                        <label for="pernyataan" class="col-md-2 col-form-label">Pernyataan</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md pernyataan_layar" data-jenis="pdf"
                                name="pernyataan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pernyataan_layar" data-jenis="layar"
                                name="pernyataan_layar">Layar</button>
                        </div>
                        <label for="spp" class="col-md-2 col-form-label">SPP</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md spp_layar" data-jenis="pdf"
                                name="spp_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md spp_layar" data-jenis="layar"
                                name="spp_layar">Layar</button>
                        </div>
                    </div>
                    {{-- Permintaan, SPTB dan Rincian --}}
                    <div class="mb-3 row">
                        <label for="permintaan" class="col-md-2 col-form-label">Permintaan</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md permintaan_layar" data-jenis="pdf"
                                name="permintaan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md permintaan_layar" data-jenis="layar"
                                name="permintaan_layar">Layar</button>
                        </div>
                        <label for="sptb" class="col-md-2 col-form-label">SPTB</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md sptb_layar" data-jenis="pdf"
                                name="sptb_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md sptb_layar" data-jenis="layar"
                                name="sptb_layar">Layar</button>
                        </div>
                        <label for="rincian77" class="col-md-2 col-form-label">Rincian</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md rincian77_layar" data-jenis="pdf"
                                name="rincian77_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md rincian77_layar" data-jenis="layar"
                                name="rincian77_layar">Layar</button>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.spp_tu.js.index')
@endsection
