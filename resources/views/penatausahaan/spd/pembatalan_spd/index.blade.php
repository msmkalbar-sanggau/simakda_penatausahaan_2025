@extends('template.app')
@section('title', 'Aktif/Batal SPD | SIMAKDA')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 style="font-weight: bold;">Aktif/Batal SPD</h5>
            </div>
            <div class="card-body">
                <div class="table-rep-plugin">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="pembatalan_spd" class="table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="width: 25px;text-align:center">No.</th>
                                    <th style="text-align:center">Nomor SPM</th>
                                    <th style="text-align:center">Nama SKPD</th>
                                    <th style="width: 50px;text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>
@endsection
@section('js')
@include('penatausahaan.spd.pembatalan_spd.js.index')
@endsection