@extends('template.app')
@section('title', 'Lihat Peran | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('peran.index') }}" class="btn btn-warning btn-md" style="float: right;">Kembali</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="tech-companies-1" class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($daftar_hak_akses as $hak_akses)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $hak_akses->display_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
