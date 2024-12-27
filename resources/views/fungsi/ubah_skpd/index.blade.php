@extends('template.app')
@section('title', 'UBAH SKPD | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Ubah SKPD
                </div>
                <div class="card-body">
                    @csrf
                    {{-- ID --}}
                    <div class="mb-3 row">
                        <label for="id" class="col-md-2 col-form-label">ID</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="id" name="id"
                                value="{{ $user->id }}" required readonly>
                        </div>
                    </div>
                    {{-- Username dan Nama Lengkap --}}
                    <div class="mb-3 row">
                        <label for="username" class="col-md-2 col-form-label">USER NAME</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="username" name="username"
                                value="{{ $user->username }}" required readonly>
                        </div>
                        <label for="nama" class="col-md-2 col-form-label">Nama Lengkap</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nama" name="nama"
                                value="{{ $user->nama }}" required readonly>
                        </div>
                    </div>
                    {{-- KD SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD/UNIT</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($kd_skpd as $skpd)
                                    <option value="{{ $skpd->kd_skpd }}" data-nama="{{ $skpd->nm_skpd }}"
                                        {{ $skpd->kd_skpd == $user->kd_skpd ? 'selected' : '' }}>{{ $skpd->kd_skpd }} |
                                        {{ $skpd->nm_skpd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- NAMA SKPD --}}
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">NAMA SKPD/UNIT</label>
                        <div class="col-md-10">
                            <textarea name="nm_skpd" id="nm_skpd" class="form-control" readonly>{{ nama_skpd($user->kd_skpd) }}</textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: center;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('fungsi.ubah_skpd.js.index');
@endsection
