@extends('template.app')
@section('title', 'Penyetoran Atas Penerimaan Tahun Ini | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Listing Data Penyetoran Atas Penerimaan Tahun Ini
                    <a href="{{ route('penyetoran_ini.tambah') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                {{-- <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="tgl_awal">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="tgl_akhir">
                        </div>
                        <div class="col-md-8">
                            <a href="#" class="btn btn-success" id="cek"><i class="uil-search-alt"></i> Cek</a>
                            <a href="#" class="btn btn-dark" id="validasi"><i class="uil-check-circle"></i>
                                Validasi</a>
                        </div>
                    </div>
                </div> --}}
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="penyetoran_ini" class="table table-striped table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Nomor STS</th>
                                        <th style="width: 50px;text-align:center">Tanggal</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
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
    @include('penatausahaan.penyetoran_tahun_ini.js.index')
@endsection
