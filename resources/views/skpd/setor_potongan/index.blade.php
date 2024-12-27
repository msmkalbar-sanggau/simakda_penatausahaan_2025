@extends('template.app')
@section('title', 'Setor Potongan Pajak | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Setor Potongan
                    <a href="{{ route('skpd.setor_potongan.create') }}" class="btn btn-primary"
                        style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="draft_potongan" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 50px;text-align:center">Nomor Bukti</th>
                                        <th style="width: 50px;text-align:center">Tanggal Bukti</th>
                                        <th style="width: 150px;text-align:center">Keterangan</th>
                                        <th style="width: 100px;text-align:center">Aksi</th>
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
    @include('skpd.setor_potongan.js.index')
@endsection
