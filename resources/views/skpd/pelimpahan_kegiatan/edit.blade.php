@extends('template.app')
@section('title', 'Edit Pelimpahan Sub Kegiatan | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Pelimpahan Sub Kegiatan
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Tujuan --}}
                    <div class="mb-3 row">
                        <label for="kd_bpp" class="col-md-2 col-form-label">Tujuan</label>
                        <div class="col-md-10">
                            <input type="text" name="kd_bpp" id="kd_bpp" class="form-control" readonly
                                value="{{ $data_bpp->kd_bpp }}">
                            <input type="text" name="id_user" id="id_user" class="form-control" readonly hidden
                                value="{{ $data_bpp->id_user }}">
                        </div>
                    </div>
                    {{-- Kode Sub Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kode Sub Kegiatan</label>
                        <div class="col-md-10">
                            <select name="kd_sub_kegiatan" id="kd_sub_kegiatan" style="width: 100%"
                                class="form-control select2-multiple">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_kegiatan as $kegiatan)
                                    <option value="{{ $kegiatan->kd_sub_kegiatan }}"
                                        data-nama="{{ $kegiatan->nm_sub_kegiatan }}">{{ $kegiatan->kd_sub_kegiatan }} |
                                        {{ $kegiatan->nm_sub_kegiatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_pelimpahan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.pelimpahan_kegiatan.index') }}"
                                class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    List Sub Kegiatan
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="data_sub_kegiatan" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;text-align:center">Kode Sub Kegiatan</th>
                                        <th style="width: 50px;text-align:center">Nama Sub Kegiatan</th>
                                        <th style="width: 100px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($daftar_data as $data)
                                        <tr>
                                            <td>{{ $data->kd_sub_kegiatan }}</td>
                                            <td>{{ $data->nm_sub_kegiatan }}</td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="deleteData('{{ $data->kd_sub_kegiatan }}','{{ $data->nm_sub_kegiatan }}')"
                                                    class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
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
    @include('skpd.pelimpahan_kegiatan.js.edit');
@endsection
