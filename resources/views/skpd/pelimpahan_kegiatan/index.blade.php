@extends('template.app')
@section('title', 'Pelimpahan Sub Kegiatan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Listing Data Pelimpahan Sub Kegiatan
                    <a href="{{ route('skpd.pelimpahan_kegiatan.create') }}" class="btn btn-primary"
                        style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="data_pelimpahan" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Id User</th>
                                        <th style="width: 50px;text-align:center">Kode BPP</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
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
@endsection
@section('js')
    @include('skpd.pelimpahan_kegiatan.js.index')
@endsection
