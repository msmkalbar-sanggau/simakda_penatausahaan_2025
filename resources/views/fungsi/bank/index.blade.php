@extends('template.app')
@section('title', 'BANK | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    LIST BANK
                    <a href="#" class="btn btn-primary" style="float: right;" id="tambah">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="bank" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Kode</th>
                                        <th style="width: 50px;text-align:center">Nama</th>
                                        <th style="width: 50px;text-align:center">BIC</th>
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

    <div id="modal_bank" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">INPUT BANK</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="kode" class="col-md-2 col-form-label">Kode</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="kode" name="kode">
                            <input type="text" readonly class="form-control" id="jenis" name="jenis" hidden>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nama" class="col-md-2 col-form-label">Nama</label>
                        <div class="col-md-12">
                            <input type="text" class="form-control" id="nama" name="nama">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="bic" class="col-md-2 col-form-label">BIC</label>
                        <div class="col-md-12">
                            <select class="form-control select2-modal" style="width: 100%" id="bic" name="bic" data-placeholder="Silahkan Pilih">
                                <option value="" disabled selected>...Pilih... </option>
                                @foreach ($daftar_bic as $bic)
                                    <option value="{{ $bic->bic }}">{{ $bic->bic }} | {{ $bic->nama_bank }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-primary" id="simpan">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('fungsi.bank.js.index')
@endsection
