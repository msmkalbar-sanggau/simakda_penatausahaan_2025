@extends('template.app')
@section('title', 'UBAH SKPD | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Ubah Password
                </div>
                <div class="card-body">
                    @csrf
                    {{-- ID --}}
                    {{-- <div class="mb-3 row">
                        <label for="id" class="col-md-2 col-form-label">ID</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="id" name="id"
                                value="{{ $user->id }}" required readonly>
                        </div> --}}
                    {{-- </div> --}}
                    {{-- Username dan Nama Lengkap --}}
                    <div class="mb-3 row">
                        {{-- <label for="username" class="col-md-2 col-form-label">Username</label>
                    <div class="col-md-4">
                        <input class="form-control" type="text" id="username" name="username"
                            value="{{ $user->username }}" required readonly>
                    </div> --}}
                        <label for="nama" class="col-md-2 col-form-label">Nama Lengkap</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" id="nama" name="nama"
                                value="{{ $user->nama }}" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password" class="col-md-2 col-form-label">Password Lama</label>
                        <div class="col-md-10">
                            <input class="form-control" type="password" id="password_lama" name="password_lama" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password" class="col-md-2 col-form-label">Password Baru</label>
                        <div class="col-md-10">
                            <input class="form-control" type="password" id="password" name="password" required
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="password2" class="col-md-2 col-form-label">Ulangi Password</label>
                        <div class="col-md-10">
                            <input class="form-control" type="password" id="password2" name="password2" required>
                        </div>

                        <!-- SIMPAN -->

                    </div>
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('js')
        @include('fungsi.ubah_password.js.index');
    @endsection
