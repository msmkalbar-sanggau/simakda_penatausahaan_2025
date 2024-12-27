@extends('template.app')
@section('title', 'Penyetoran Atas Penerimaan Tahun Lalu | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Listing Data Penyetoran Atas Penerimaan Tahun Lalu
                    <a href="{{ route('penyetoran_lalu.tambah') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="tgl_awal" hidden>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="tgl_akhir" hidden>
                        </div>
                        <div class="col-md-8">
                            <a href="#" class="btn btn-success" id="cek" hidden><i class="uil-search-alt"></i>
                                Cek</a>
                            <a href="#" class="btn btn-dark" id="validasi" hidden><i class="uil-check-circle"></i>
                                Validasi</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="penyetoran_lalu" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Nomor STS</th>
                                        <th style="width: 50px;text-align:center">Tanggal</th>
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
@endsection
@section('js')
    @include('penatausahaan.penyetoran_tahun_lalu.js.index')
@endsection
