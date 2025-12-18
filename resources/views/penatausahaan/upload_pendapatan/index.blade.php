@extends('template.app')
@section('title', 'Upload Pendapatan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Upload Pendapatan
                </div>
                <form enctype="multipart/form-data" id="form">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kd_skpd" class="form-label">Kode SKPD</label>
                                <select class="form-control select2-modal @error('kd_skpd') is-invalid @enderror" style=" width: 100%;" id="kd_skpd" name="kd_skpd">
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
@endsection
@section('js')
    @include('penatausahaan.upload_pendapatan.js.index')
@endsection
