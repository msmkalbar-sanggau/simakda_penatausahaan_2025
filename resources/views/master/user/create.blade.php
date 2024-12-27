@extends('template.app')
@section('title', 'Tambah Pengguna | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('user.store') }}" method="post">
                        @csrf
                        <!-- Username -->
                        <div class="mb-3 row">
                            <label for="username" class="col-md-2 col-form-label">Username</label>
                            <div class="col-md-10">
                                <input class="form-control @error('username') is-invalid @enderror" type="text"
                                    placeholder="Silahkan isi dengan username" id="username" name="username"
                                    value="{{ old('username') }}">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Nama -->
                        <div class="mb-3 row">
                            <label for="nama" class="col-md-2 col-form-label">Nama</label>
                            <div class="col-md-10">
                                <input class="form-control @error('nama') is-invalid @enderror" type="text"
                                    placeholder="Silahkan isi dengan nama" id="nama" name="nama"
                                    value="{{ old('nama') }}">
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="mb-3 row">
                            <label for="password" class="col-md-2 col-form-label">Password</label>
                            <div class="col-md-10">
                                <input class="form-control @error('password') is-invalid @enderror" type="password"
                                    placeholder="Silahkan isi dengan password" id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Konfirmasi Password -->
                        <div class="mb-3 row">
                            <label for="confirmation_password" class="col-md-2 col-form-label">Konfirmasi Password</label>
                            <div class="col-md-10">
                                <input class="form-control @error('confirmation_password') is-invalid @enderror"
                                    type="password" placeholder="Silahkan isi dengan konfirmasi password"
                                    id="confirmation_password" name="confirmation_password">
                                @error('confirmation_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Kode SKPD -->
                        <div class="mb-3 row">
                            <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('kd_skpd') is-invalid @enderror"
                                    name="kd_skpd" required>
                                    <optgroup label="Daftar Kode SKPD">
                                        <option value="" selected disabled>Silahkan Pilih Kode SKPD</option>
                                        @foreach ($daftar_skpd as $skpd)
                                            <option value="{{ $skpd->kd_skpd }}"
                                                {{ old('kd_skpd') == $skpd->kd_skpd ? 'selected' : '' }}>
                                                {{ $skpd->kd_skpd }} |
                                                {{ $skpd->nm_skpd }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('kd_skpd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Tipe -->
                        <div class="mb-3 row">
                            <label for="tipe" class="col-md-2 col-form-label">Tipe</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('tipe') is-invalid @enderror"
                                    name="tipe" required>
                                    <optgroup label="Daftar Tipe">
                                        <option value="" selected disabled>Silahkan Pilih Tipe</option>
                                        <option value="1" {{ old('tipe') == '1' ? 'selected' : '' }}>Simakda</option>
                                        <option value="2" {{ old('tipe') == '2' ? 'selected' : '' }}>Simakda SKPD
                                        </option>
                                    </optgroup>
                                </select>
                                @error('tipe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Status -->
                        <div class="mb-3 row">
                            <label for="status" class="col-md-2 col-form-label">Status</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('status') is-invalid @enderror"
                                    name="status" required>
                                    <optgroup label="Daftar Kode SKPD">
                                        <option value="" selected disabled>Silahkan Pilih Kode SKPD</option>
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>Tidak Aktif
                                        </option>
                                    </optgroup>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Peran -->
                        <div class="mb-3 row">
                            <label for="peran" class="col-md-2 col-form-label">Peran</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('peran') is-invalid @enderror"
                                    name="peran" id="peran">
                                    <optgroup label="Daftar Hak Akses">
                                        <option value="" selected disabled>Silahkan Pilih
                                            Peran</option>
                                        @foreach ($daftar_role as $peran)
                                            <option value="{{ $peran->id }}"
                                                {{ old('peran') == $peran->id ? 'selected' : '' }}>{{ $peran->role }} |
                                                {{ $peran->nama_role }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('peran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div style="float: right;">
                            <button type="submit" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('user.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2({
                width: 'resolve',
                theme: 'bootstrap-5'
            });
        });
    </script>
@endsection
