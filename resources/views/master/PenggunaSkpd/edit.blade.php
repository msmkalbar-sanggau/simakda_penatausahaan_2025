@extends('template.app')
@section('title', 'Ubah SKPD Pengguna | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('skpd_pengguna.update', $pengguna->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        {{-- Kode --}}
                        <div class="mb-3 row">
                            <label for="role" class="col-md-2 col-form-label">Nama</label>
                            <div class="col-md-10">
                                <input class="form-control @error('role') is-invalid @enderror"
                                    value="{{ $pengguna->nama }}" type="text" placeholder="Silahkan isi Nama"
                                    id="role" name="role" disabled>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Hak Akses --}}
                        <div class="mb-3 row">
                            <label for="hak_akses" class="col-md-12 col-form-label" style="text-align: center">SKPD</label>
                            <div class="col-md-12">
                                <div class="card" style="width: 100%">
                                    <div class="card-body">
                                        <div class="mb-3 row">
                                            @foreach ($daftar_skpd as $skpd)
                                                <div class="col-md-4">
                                                    <div class="form-check form-switch form-switch-lg">
                                                        <input type="checkbox" class="form-check-input" id="skpd"
                                                            name="skpd[]" value="{{ $skpd->kd_skpd }}"
                                                            {{ collect($list_skpd)->contains($skpd->kd_skpd) ? 'checked' : '' }}>
                                                        <label class="form-check-label" style="font-size: 9px">
                                                            {{ $skpd->nm_skpd }}
                                                        </label>
                                                    </div>
                                                    <br>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="float: right;">
                            <button type="submit" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd_pengguna.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
