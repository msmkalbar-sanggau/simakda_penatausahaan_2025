@extends('template.app')
@section('title', 'Terima SP2D | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            @if (session()->has('message'))
                <div class="alert {{ session('alert') ?? 'alert-info' }}">
                    {{ session('message') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    List SP2D
                    <a href="{{ route('terima_sp2d.cek_terima') }}" class="btn btn-primary" style="float: right;" target="_blank">Cek SP2D Belum Terima</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="sp2d" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 100px;text-align:center">Nomor SP2D</th>
                                        <th style="width: 100px;text-align:center">Nomor SPM</th>
                                        <th style="width: 100px;text-align:center">Tanggal</th>
                                        <th style="width: 100px;text-align:center">SKPD</th>
                                        <th style="width: 100px;text-align:center">Status</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php
                                        $no = 0;
                                    @endphp
                                    @foreach ($cair_sp2d->chunk(5) as $data)
                                        @foreach ($data as $sp2d)
                                            @if ($sp2d->status_bud == '1')
                                                <tr>
                                                    <td>{{ ++$no }}</td>
                                                    <td style="background-color:#03d3ff">{{ $sp2d->no_sp2d }}
                                                    </td>
                                                    <td style="background-color:#03d3ff">{{ $sp2d->no_spm }}
                                                    </td>
                                                    <td style="background-color:#03d3ff">
                                                        {{ tanggal($sp2d->tgl_sp2d) }}</td>
                                                    <td style="background-color:#03d3ff">{{ $sp2d->kd_skpd }}
                                                    </td>
                                                    <td style="background-color:#03d3ff">
                                                        {{ $sp2d->status_terima == '1' ? 'Sudah diterima' : 'Belum diterima' }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('terima_sp2d.tampil_sp2d', $sp2d->no_sp2d) }}"
                                                            class="btn btn-info btn-sm"><i class="uil-eye"></i></a>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>{{ ++$no }}</td>
                                                    <td>{{ $sp2d->no_sp2d }}</td>
                                                    <td>{{ $sp2d->no_spm }}</td>
                                                    <td>{{ tanggal($sp2d->tgl_sp2d) }}</td>
                                                    <td>{{ $sp2d->kd_skpd }}</td>
                                                    <td>{{ $sp2d->status_terima == '1' ? 'Sudah diterima' : 'Belum diterima' }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('terima_sp2d.tampil_sp2d', $sp2d->no_sp2d) }}"
                                                            class="btn btn-info btn-sm"><i class="uil-eye"></i></a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach --}}
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
    @include('skpd.terima_sp2d.js.index')
@endsection
