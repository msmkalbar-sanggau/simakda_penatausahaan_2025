@extends('template.app')
@section('title', 'Upload Pendapatan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <label class="font-weight-bold h1">Upload Pendapatan</label>
                </div>
                <form enctype="multipart/form-data" id="form">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kd_skpd" class="form-label">Kode SKPD</label>
                                <select class="form-control select2-multiple @error('kd_skpd') is-invalid @enderror" style=" width: 100%;" id="kd_skpd" name="kd_skpd">
                                    <option value="" disabled selected>Silahkan Pilih</option>
                                    @foreach ($ms_skpd as $row)
                                        <option value="{{ $row->kd_skpd }}">{{ $row->kd_skpd . ' | ' . $row->nm_skpd }}</option>
                                    @endforeach
                                </select>
                                @error('kd_skpd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <input type="file" name="file" id="file" required>
                        <button type="submit">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="loading" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <img src='{{ asset('template/loading.gif') }}' width='100%' height='200px'>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.upload_pendapatan.js.index')
@endsection
