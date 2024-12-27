@extends('template.app')
@section('title', 'Input Saldo Kas | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Listing Data Saldo
                    <a href="{{ route('saldo_kas.tambah') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                    {{-- <a href="{{ route('sp2b.cetak') }}" class="btn btn-success"
                        style="float: right;">Cetak</a> --}}
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="saldo_kas" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5px;text-align:center">No.</th>
                                        <th style="width: 300px;text-align:center">Nilai</th>
                                        <th style="width: 300px;text-align:center">Kode Rekening</th>
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
    @include('bud.saldo_kas.js.index')
@endsection
