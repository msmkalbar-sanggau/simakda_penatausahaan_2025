@extends('template.app')
@section('title', 'Penerimaan Kas | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-md btn-success tipe" data-jenis="cair">Hijau : SUDAH TERVALIDASI</button>
                    <button class="btn btn-md tipe" style="background-color: white;color:black; border: 1px solid black"
                        data-jenis="belum">Putih
                        : BELUM TERVALIDASI</button>
                    <input type="hidden" name="tipe" id="tipe">
                    <a href="{{ route('penerimaan_kas.cekvalidasi') }}" class="btn btn-primary" style="float: right;"
                        target="_blank">Cek Data
                        Belum
                        Validasi</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="penerimaan_kas" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Nomor Kas</th>
                                        <th style="width: 50px;text-align:center">No STS</th>
                                        <th style="width: 60px;text-align:center">Tanggal STS</th>
                                        <th style="width: 60px;text-align:center">Tanggal Kas</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
                                        <th style="width: 50px;text-align:center">Nilai</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
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
    @include('skpd.penerimaan_kas.js.index')
@endsection
