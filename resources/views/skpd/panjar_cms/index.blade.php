@extends('template.app')
@section('title', 'Pemberian Panjar CMS | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    
                    Listing Data Pengembalian Panjar
                    <a href="{{ route('panjar_cms.create') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                    <br><br> Keterangan :
                    <button class="btn btn-info btn-md" style="pointer-events: none">Sudah Upload</button>
                    <button class="btn btn-success btn-md" style="pointer-events: none">Sudah Validasi dan Upload</button>
                    <button class="btn btn-light btn-md" style="pointer-events: none;border:1px solid black">Belum Validasi dan Upload</button>
                </div>
                <div class="card-body">
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
    @include('skpd.panjar_cms.js.index')
@endsection