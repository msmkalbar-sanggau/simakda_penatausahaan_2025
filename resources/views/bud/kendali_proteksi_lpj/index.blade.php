@extends('template.app')
@section('title', 'Kendali Proteksi LPJ | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Data Pengesahan
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="kendali_proteksi" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Kode SKPD</th>
                                        <th style="width: 50px;text-align:center">Nama SKPD</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
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
    </div>

    {{-- Modal Proteksi --}}
    <div id="proteksi" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kendali Proteksi LPJ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-12 col-form-label">SKPD</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="kd_skpd" name="kd_skpd">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-12 col-form-label">Nama SKPD</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="nm_skpd" name="nm_skpd">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="status" class="col-md-12 col-form-label" style="vertical-align: top">Status</label>
                        <div class="col-md-12">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" id="status_lpj">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-primary" id="simpan"><i
                                    class="uil-save"></i>Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i>Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('bud.kendali_proteksi_lpj.js.index')
@endsection
