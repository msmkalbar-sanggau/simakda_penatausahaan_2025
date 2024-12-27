@extends('template.app')
@section('title', 'SPD Konfigurasi SPD | SIMAKDA')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                List Konfigurasi SPD
            </div>
            <div class="card-body">
                @method('patch')
                @csrf
                <div class="row">
                    <!-- no konfig spd -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="nomor">No konfig SPD.</label>
                            <input type="text" class="form-control" id="nomor" name="nomor" value="{{ $data_konfig->no_konfig_spd }}">
                        </div>
                    </div>

                    <!-- tanggal konfig spd-->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="tgl_con">Tanggal konfig SPD</label>
                            <input type="date" class="form-control" id="tgl_con" name="tgl_con" value="{{ $data_konfig->tgl_konfig_spd }}">
                        </div>
                    </div>

                    <!-- jenis bulan spd -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="jenis_spd">Jenis Bulan SPD</label>
                            <select class="form-control select2-multiple" style="width: 100%" name="jenis_spd" id="jenis_spd">
                                <option value=""></option>
                                <option value="1" {{ $data_konfig->jenis_spd == '1' ? 'selected' : '' }}>Perbulan</option>
                                <option value="2" {{ $data_konfig->jenis_spd == '2' ? 'selected' : '' }}>Triwulan</option>
                            </select>
                        </div>
                    </div>

                    <!-- no 1 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_1">No. 1</label>
                            <textarea name="ingat_1" id="ingat_1" rows="2" class="form-control">{{ $data_konfig->ingat1 }}</textarea>
                        </div>
                    </div>

                    <!-- no 2 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_1">No. 2</label>
                            <textarea name="ingat_2" id="ingat_2" rows="2" class="form-control">{{ $data_konfig->ingat2 }}</textarea>
                        </div>
                    </div>

                    <!-- no 3 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_3">No. 3</label>
                            <textarea name="ingat_3" id="ingat_3" rows="2" class="form-control">{{ $data_konfig->ingat3 }}</textarea>
                        </div>
                    </div>

                    <!-- no 4 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_4">No. 4</label>
                            <textarea name="ingat_4" id="ingat_4" rows="2" class="form-control">{{ $data_konfig->ingat4 }}</textarea>
                        </div>
                    </div>

                    <!-- no 5 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_5">No. 5</label>
                            <textarea name="ingat_5" id="ingat_5" rows="2" class="form-control">{{ $data_konfig->ingat5 }}</textarea>
                        </div>
                    </div>

                    <!-- 6 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_6">No. 6</label>
                            <textarea name="ingat_6" id="ingat_6" rows="2" class="form-control">{{ $data_konfig->ingat6 }}</textarea>
                        </div>
                    </div>

                    <!-- no 7 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_7">No. 7</label>
                            <textarea name="ingat_7" id="ingat_7" rows="2" class="form-control">{{ $data_konfig->ingat7 }}</textarea>
                        </div>
                    </div>

                    <!-- no 8 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_8">No. 8</label>
                            <textarea name="ingat_8" id="ingat_8" rows="2" class="form-control">{{ $data_konfig->ingat8 }}</textarea>
                        </div>
                    </div>

                    <!-- no 9 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_9">No. 9</label>
                            <textarea name="ingat_9" id="ingat_9" rows="2" class="form-control">{{ $data_konfig->ingat9 }}</textarea>
                        </div>
                    </div>

                    <!-- no 10 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_10">No. 10</label>
                            <textarea name="ingat_10" id="ingat_10" rows="2" class="form-control">{{ $data_konfig->ingat10 }}</textarea>
                        </div>
                    </div>

                    <!-- no 11 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_11">No. 11</label>
                            <textarea name="ingat_11" id="ingat_11" rows="2" class="form-control">{{ $data_konfig->ingat11 }}</textarea>
                        </div>
                    </div>

                    <!-- akhir -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="ingat_akhir">Akhir</label>
                            <textarea name="ingat_akhir" id="ingat_akhir" rows="2" class="form-control">{{ $data_konfig->ingat_akhir }}</textarea>
                        </div>
                    </div>

                     <!-- memutuskan -->
                     <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="memutuskan">Memutuskan</label>
                            <textarea name="memutuskan" id="memutuskan" rows="2" class="form-control">{{ $data_konfig->memutuskan }}</textarea>
                        </div>
                    </div>
                </div>
                <!-- SIMPAN -->
                <div class="mb-3 row" style="float: right;">
                    <div class="col-md-12" style="text-align: center">
                        <button id="simpan_konfigurasi" class="btn btn-primary btn-md">Simpan</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div> <!-- end col -->
</div>
@endsection
@section('js')
@include('penatausahaan.spd.konfigurasi_spd.js.index')
@endsection