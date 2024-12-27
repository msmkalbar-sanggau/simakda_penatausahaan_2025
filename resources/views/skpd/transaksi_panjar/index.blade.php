@extends('template.app')
@section('title', 'Transaksi Panjar | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Listing Data Transaksi Panjar
                    <a href="{{ route('transaksipanjar.tambah') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="transaksipanjar" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Nomor Bukti</th>
                                        <th style="width: 50px;text-align:center">Tanggal</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
                                        <th style="width: 50px;text-align:center">LPJ</th>
                                        <th style="width: 50px;text-align:center">SPJ</th>
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
@endsection
@section('js')
    @include('skpd.transaksi_panjar.js.index')
@endsection
