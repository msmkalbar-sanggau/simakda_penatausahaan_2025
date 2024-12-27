@extends('template.app')
@section('title', 'Input SP2B | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List SP2B
                    <a href="{{ route('sp2b.tambah') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                    {{-- <a href="{{ route('sp2b.cetak') }}" class="btn btn-success"
                        style="float: right;">Cetak</a> --}}
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="sp2b" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5px;text-align:center">No.</th>
                                        <th style="width: 300px;text-align:center">SP2B</th>
                                        <th style="width: 300px;text-align:center">Tanggal SP2B</th>
                                        <th style="widht: 100px;text-align:center">Kode SKPD</th>
                                        {{-- <th style="width: 200px;text-align:center">SP3B</th>
                                        <th style="width: 100px;text-align:center">Tanggal SP3B</th> --}}
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

    {{-- Modal Cetak --}}

    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-l">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak SP2B</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SP2b --}}
                    <div class="mb-3 row">
                        <label for="no_sp2b" class="col-md-4 col-form-label">No SP2B</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control" id="no_sp2b" name="no_sp2b">
                            <input type="text" hidden class="form-control" id="kd_skpd" name="kd_skpd">
                        </div>
                    </div>
                    {{-- Kuasa BUD --}}
                    <div class="mb-3 row">
                        <label for="bud" class="col-md-4 col-form-label">Kuasa BUD</label>
                        <div class="col-md-8">
                            <select name="bud" class="form-control" id="bud">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($bud as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-4">
                            <input type="text" name="nama_bud" id="nama_bud" class="form-control" readonly>
                        </div> --}}
                    </div>

                    {{-- Permintaan, SPTB dan Rincian --}}
                    <div class="mb-3 row">
                        <label for="permintaan" class="col-md-4 col-form-label">Permintaan</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md permintaan_layar" data-jenis="pdf"
                                name="permintaan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md permintaan_layar" data-jenis="layar"
                                name="permintaan_layar">Layar</button>
                        </div>
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
    @include('bud.sp2b.js.index')
@endsection
