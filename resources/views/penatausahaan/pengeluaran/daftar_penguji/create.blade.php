@extends('template.app')
@section('title', 'Tambah Daftar Penguji | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Daftar Penguji
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No Advices dan Tanggal --}}
                    <div class="mb-2 row">
                        <label for="no_advice" class="col-md-2 col-form-label">No. Advices</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('no_advice') is-invalid @enderror"
                                id="no_advice" name="no_advice" readonly placeholder="Terisi otomatis sesuai urutan">
                            @error('no_advice')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="tanggal" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                                name="tanggal">
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- SP2D ONLINE --}}
                    <div class="mb-3 row">
                        <label for="sp2d_online" class="col-md-12 col-form-label">SP2D Online</label>
                        <div class="col-md-12">
                            <select name="sp2d_online" id="sp2d_online" class="form-control select2-multiple">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                    </div>
                    {{-- No SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-12 col-form-label">No. SP2D <small>(otomatis turun ke tabel
                                bawah)</small></label>
                        <div class="col-md-12">
                            <select name="no_sp2d" id="no_sp2d" class="form-control select2-multiple">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_sp2d as $sp2d)
                                    <option value="{{ $sp2d->no_sp2d }}" data-tgl_sp2d="{{ $sp2d->tgl_sp2d }}"
                                        data-no_spm="{{ $sp2d->no_spm }}" data-tgl_spm="{{ $sp2d->tgl_spm }}"
                                        data-nilai="{{ $sp2d->nilai }}" data-bank="{{ $sp2d->bank }}" data-nmbank="{{ $sp2d->nmbank }}"
                                        data-bic="{{ $sp2d->bic }}" data-jns_spp="{{ $sp2d->jns_spp }}"
                                        data-jenis_beban="{{ $sp2d->jenis_beban }}">
                                        {{ $sp2d->no_sp2d }} | {{ $sp2d->tgl_sp2d }} |
                                        {{ $sp2d->nmbank }} | {{ $sp2d->nm_skpd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="simpan_penguji" class="btn btn-primary btn-md">Simpan</button>
                        <a href="{{ route('daftar_penguji.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Input Detail --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Detail Daftar Penguji
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_penguji" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No SP2D</th>
                                <th>Tanggal SP2D</th>
                                <th>No SPM</th>
                                <th>Tanggal SPM</th>
                                <th>Nilai</th>
                                <th>Bank</th>
                                <th>BIC</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.daftar_penguji.js.create');
@endsection
