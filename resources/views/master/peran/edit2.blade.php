@extends('template.app')
@section('title', 'Ubah Peran | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('peran.update', $peran->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        {{-- Kode --}}
                        <div class="mb-3 row">
                            <label for="role" class="col-md-2 col-form-label">Kode</label>
                            <div class="col-md-10">
                                <input class="form-control @error('role') is-invalid @enderror" value="{{ $peran->role }}"
                                    type="text" placeholder="Silahkan isi dengan kode peran" id="role"
                                    name="role">
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Nama --}}
                        <div class="mb-3 row">
                            <label for="nama_role" class="col-md-2 col-form-label">Nama</label>
                            <div class="col-md-10">
                                <input class="form-control @error('nama_role') is-invalid @enderror"
                                    value="{{ $peran->nama_role }}" type="text"
                                    placeholder="Silahkan isi dengan nama peran" id="nama_role" name="nama_role">
                                @error('nama_role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="mb-3 row">
                            <label for="hak_akses" class="col-md-2 col-form-label">Hak Akses</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('hak_akses') is-invalid @enderror"
                                    name="hak_akses[]" id="hak_akses" multiple="multiple">
                                    <optgroup label="Daftar Hak Akses">
                                        @foreach ($daftar_hak_akses as $hak_akses)
                                            <option value="{{ $hak_akses->id }}"
                                                {{ collect(old('hak_akses'))->contains($hak_akses->id) ? 'selected' : '' }}>
                                                {{ $hak_akses->display_name }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('hak_akses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="mb-3 row">
                            <label for="hak_akses" class="col-md-12 col-form-label" style="text-align: center">Hak
                                Akses</label>
                            <div class="col-md-12">
                                @foreach ($daftar_hak_akses as $daftar)
                                    <div class="card" style="width: 100%">
                                        <div class="card-header" style="text-align: center">
                                            {{ $daftar->display_name }}
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3 row">
                                                @foreach ($daftar_hak_akses1 as $hak_akses)
                                                    @if ($daftar->id == $hak_akses->urut_akses)
                                                        <div class="col-md-4">
                                                            <div class="form-check form-switch form-switch-lg">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="hak_akses" name="hak_akses[]"
                                                                    value="{{ $hak_akses->id }}">
                                                                <label class="form-check-label">
                                                                    {{ $hak_akses->display_name }}
                                                                </label>
                                                            </div>
                                                            <br>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div style="float: right;">
                            <button type="submit" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('peran.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                placeholder: "Silahkan Pilih",
                theme: 'bootstrap-5'
            });
        });
    </script>
@endsection
