@extends('template.app')
@section('title', 'Tambah Panjar CMS | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    
                    Listing Data Tambah Panjar
                    <a href="{{ route('tpanjar_cms.create') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <label for="tgl_voucher" class="col-md-1 col-form-label">Tanggal</label>
                        <div class="col-md-2">
                            <input type="date" class="form-control @error('tgl_voucher') is-invalid @enderror"
                                id="tgl_voucher" name="tgl_voucher">
                        </div>
                        <div class="col-md-2">
                            <button id="cetak_panjar" class="btn btn-dark btn-md">Cetak List</button>
                        </div>
                    </div>
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="panjar" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px">No.</th>
                                        <th style="width: 50px">Nomor Panjar</th>
                                        <th style="width: 50px">Tanggal Panjar</th>
                                        <th style="width: 50px">SKPD</th>
                                        <th style="width: 50px">Nilai</th>
                                        <th style="width: 200px">Aksi</th>
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
@endsection
@section('js')
    @include('skpd.tpanjar_cms.js.index')
@endsection