@extends('template.app')
@section('title', 'Transaksi CMS | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Daftar Transaksi Non Tunai (CMS)
                    <a href="{{ route('skpd.transaksi_cms.create') }}"
                        class="btn btn-primary {{ $cek['selisih_angkas'] > 0 ? 'disabled' : '' }} {{ $cek['status_ang'] == '0' ? 'disabled' : '' }}"
                        style="float: right;">Tambah</a>
                    <input type="text" id="selisih_angkas" hidden readonly value="{{ $cek['selisih_angkas'] }}">
                    <input type="text" id="status_ang" hidden readonly value="{{ $cek['status_ang'] }}">
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <label for="tgl_voucher" class="col-md-1 col-form-label">Tanggal</label>
                        <div class="col-md-2">
                            <input type="date" class="form-control @error('tgl_voucher') is-invalid @enderror"
                                id="tgl_voucher" name="tgl_voucher">
                        </div>
                        <div class="col-md-2">
                            <button id="cetak_cms" class="btn btn-dark btn-md">Cetak List</button>
                        </div>
                    </div>
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="transaksi_cms" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 100px;text-align:center">Nomor Bukti</th>
                                        <th style="width: 100px;text-align:center">Tanggal</th>
                                        <th style="width: 100px;text-align:center">Nama SKPD</th>
                                        <th style="width: 100px;text-align:center">Keterangan</th>
                                        <th style="width: 50px;text-align:center">UPL</th>
                                        <th style="width: 50px;text-align:center">VAL</th>
                                        <th style="width: 50px;text-align:center">POT</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php
                                        $no = 0;
                                    @endphp
                                    @foreach ($data_cms->chunk(5) as $data)
                                        @foreach ($data as $cms)
                                            <tr>
                                                @if ($cms->status_upload == '1' && $cms->status_validasi == '1')
                                                    <td>{{ ++$no }}</td>
                                                    <td style="background-color: #00a5ff">{{ $cms->no_voucher }}</td>
                                                    <td style="background-color: #00a5ff">{{ $cms->tgl_voucher }}</td>
                                                    <td style="background-color: #00a5ff">{{ $cms->kd_skpd }}</td>
                                                    <td style="background-color: #00a5ff">{{ Str::limit($cms->ket, 20) }}
                                                    </td>
                                                    <td style="background-color: #00a5ff">{{ $cms->status_upload }}</td>
                                                    <td style="background-color: #00a5ff">{{ $cms->status_validasi }}</td>
                                                    <td style="background-color: #00a5ff">{{ $cms->status_trmpot }}</td>
                                                @elseif ($cms->status_upload == '1')
                                                    <td>{{ ++$no }}</td>
                                                    <td style="background-color: #12cc2e">{{ $cms->no_voucher }}</td>
                                                    <td style="background-color: #12cc2e">{{ $cms->tgl_voucher }}</td>
                                                    <td style="background-color: #12cc2e">{{ $cms->kd_skpd }}</td>
                                                    <td style="background-color: #12cc2e">{{ Str::limit($cms->ket, 20) }}
                                                    </td>
                                                    <td style="background-color: #12cc2e">{{ $cms->status_upload }}</td>
                                                    <td style="background-color: #12cc2e">{{ $cms->status_validasi }}</td>
                                                    <td style="background-color: #12cc2e">{{ $cms->status_trmpot }}</td>
                                                @else
                                                    <td>{{ ++$no }}</td>
                                                    <td>{{ $cms->no_voucher }}</td>
                                                    <td>{{ $cms->tgl_voucher }}</td>
                                                    <td>{{ $cms->kd_skpd }}</td>
                                                    <td>{{ Str::limit($cms->ket, 20) }}</td>
                                                    <td>{{ $cms->status_upload }}</td>
                                                    <td>{{ $cms->status_validasi }}</td>
                                                    <td>{{ $cms->status_trmpot }}</td>
                                                @endif
                                                <td style="width:200px">
                                                    <a href="{{ route('skpd.transaksi_cms.edit', $cms->no_voucher) }}"
                                                        class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                                    @if ($cms->status_upload == '1' || $cms->status_trmpot == '1')
                                                    @else
                                                        <a href="javascript:void(0);"
                                                            onclick="deleteData('{{ $cms->no_voucher }}');"
                                                            class="btn btn-danger btn-sm" id="delete"><i
                                                                class="fas fa-trash-alt"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
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
    @include('skpd.transaksi_cms.js.index')
@endsection
