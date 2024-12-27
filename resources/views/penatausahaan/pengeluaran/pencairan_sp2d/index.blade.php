@extends('template.app')
@section('title', 'Pencairan SP2D | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-md btn-success tipe" data-jenis="cair">Hijau : SP2D SUDAH CAIR</button>
                    <button class="btn btn-md tipe" style="background-color: white;color:black; border: 1px solid black"
                        data-jenis="belum">Putih
                        : SP2D BELUM CAIR</button>
                    <input name="tipe" id="tipe" hidden>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="cair_sp2d" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 100px;text-align:center">Nomor SP2D</th>
                                        <th style="width: 100px;text-align:center">Nomor SPM</th>
                                        <th style="width: 100px;text-align:center">Tanggal</th>
                                        <th style="width: 100px;text-align:center">SKPD</th>
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
                                                    <td style="background-color:#4bbe68;color:white">{{ $sp2d->no_sp2d }}
                                                    </td>
                                                    <td style="background-color:#4bbe68;color:white">{{ $sp2d->no_spm }}
                                                    </td>
                                                    <td style="background-color:#4bbe68;color:white">
                                                        {{ tanggal($sp2d->tgl_sp2d) }}</td>
                                                    <td style="background-color:#4bbe68;color:white">{{ $sp2d->kd_skpd }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('pencairan_sp2d.tampil', $sp2d->no_sp2d) }}"
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
                                                    <td>
                                                        <a href="{{ route('pencairan_sp2d.tampil', $sp2d->no_sp2d) }}"
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
    <style>
        .orange {
            background-color: #FFA500;
            color: white
        }
    </style>
    @include('penatausahaan.pengeluaran.pencairan_sp2d.js.cetak')
@endsection
