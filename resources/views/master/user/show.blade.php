@extends('template.app')
@section('title', 'Tambah Pengguna | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('user.index') }}" class="btn btn-warning btn-md" style="float: right;">Kembali</a>
                </div>
                <div class="card-body">
                    <!-- Username -->
                    <div class="mb-3 row">
                        <label for="username" class="col-md-2 col-form-label">Username</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $user->username }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Nama -->
                    <div class="mb-3 row">
                        <label for="nama" class="col-md-2 col-form-label">Nama</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $user->nama }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Kode SKPD -->
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $user->kd_skpd }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Tipe -->
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Tipe</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" readonly
                                value="{{ $user->is_admin == '1' ? 'SIMAKDA' : 'SIMAKDA SKPD' }}">
                        </div>
                    </div>
                    <!-- Status -->
                    <div class="mb-3 row">
                        <label for="status" class="col-md-2 col-form-label">Status</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" readonly
                                value="{{ $user->status == '1' ? 'Aktif' : 'Tidak Aktif' }}">
                        </div>
                    </div>
                    <!-- Peran -->
                    <div class="mb-3 row">
                        <label for="role" class="col-md-2 col-form-label">Peran</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $role->nama_role }}" readonly class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
