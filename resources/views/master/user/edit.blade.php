@extends('template.app')
@section('title', 'Ubah Pengguna | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('user.update', $user->id) }}" method="post">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <!-- Username -->
                        <div class="mb-3 row">
                            <label for="username" class="col-md-2 col-form-label">Username</label>
                            <div class="col-md-10">
                                <input class="form-control  @error('username') is-invalid @enderror"
                                    value="{{ $user->username }}" type="text" placeholder="Silahkan isi dengan username"
                                    id="username" name="username" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Nama -->
                        <div class="mb-3 row">
                            <label for="nama" class="col-md-2 col-form-label">Nama</label>
                            <div class="col-md-10">
                                <input class="form-control @error('nama') is-invalid @enderror" value="{{ $user->nama }}"
                                    type="text" placeholder="Silahkan isi dengan nama" id="nama" name="nama"
                                    required>
                                @error('nama')
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
                                                {{ $user->kd_skpd == $skpd->kd_skpd ? 'selected' : '' }}>
                                                {{ $skpd->kd_skpd }}
                                                | {{ $skpd->nm_skpd }}</option>
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
                                        <option value="1" {{ $user->is_admin == '1' ? 'selected' : '' }}>Simakda
                                        </option>
                                        <option value="2" {{ $user->is_admin == '2' ? 'selected' : '' }}>Simakda SKPD
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
                                        <option value="1" {{ $user->status == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="2" {{ $user->status == '2' ? 'selected' : '' }}>Tidak Aktif
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
                                    name="peran" required>
                                    <optgroup label="Daftar Hak Akses">
                                        <option value="" selected disabled>Silahkan Pilih Peran</option>
                                        @foreach ($daftar_role as $peran)
                                            <option value="{{ $peran->id }}"
                                                {{ $user->role == $peran->id ? 'selected' : '' }}>{{ $peran->role }} |
                                                {{ $peran->nama_role }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('peran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="peran" class="col-md-2 col-form-label">Password Lama</label>
                            <div class="col-md-10">
                                <input type="password" name="password_lama"
                                    class="form-control @error('password_lama') is-invalid @enderror"
                                    placeholder="Masukkan password lama">
                                @error('password_lama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="mb-3 row">
                            <label for="peran" class="col-md-2 col-form-label">Password Baru</label>
                            <div class="col-md-10">
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Masukkan password baru">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="mb-3 row">
                            <label for="peran" class="col-md-2 col-form-label">Konfirmasi Password</label>
                            <div class="col-md-10">
                                <input type="password" name="confirmation_password"
                                    class="form-control @error('confirmation_password') is-invalid @enderror"
                                    placeholder="Konfirmasi password baru">
                                @error('confirmation_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Jabatan -->
                        {{-- <div class="mb-3 row">
                                <label for="jabatan" class="col-md-2 col-form-label">Jabatan</label>
                                <div class="col-md-10">
                                    <input class="form-control @error('jabatan') is-invalid @enderror"
                                        value="{{ $user->jabatan }}" type="text"
                                        placeholder="Silahkan isi dengan jabatan" id="jabatan" name="jabatan" required>
                                    @error('jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
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
                theme: 'bootstrap-5',
                width: 'resolve'
            });
        });
    </script>
@endsection
