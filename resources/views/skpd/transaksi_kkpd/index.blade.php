@extends('template.app')
@section('title', 'Transaksi KKPD | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Daftar Transaksi Non Tunai KKPD
                    <a href="{{ route('skpd.transaksi_kkpd.create') }}" class="btn btn-primary"
                        style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    {{-- <div class="mb-3 row">
                        <label for="tgl_voucher" class="col-md-1 col-form-label">Tanggal</label>
                        <div class="col-md-2">
                            <input type="date" class="form-control @error('tgl_voucher') is-invalid @enderror"
                                id="tgl_voucher" name="tgl_voucher">
                        </div>
                        <div class="col-md-2">
                            <button id="cetak_cms" class="btn btn-dark btn-md">Cetak List</button>
                        </div>
                    </div> --}}
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="transaksi_kkpd" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 100px;text-align:center">Nomor Bukti</th>
                                        <th style="width: 100px;text-align:center">Tanggal</th>
                                        <th style="width: 100px;text-align:center">Nama SKPD</th>
                                        <th style="width: 100px;text-align:center">Keterangan</th>
                                        <th style="width: 50px;text-align:center">VAL</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
@section('js')
    @include('skpd.transaksi_kkpd.js.index')
@endsection
