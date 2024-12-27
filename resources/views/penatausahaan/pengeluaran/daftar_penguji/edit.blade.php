@extends('template.app')
@section('title', 'Edit Daftar Penguji | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @csrf
                    {{-- No Advices dan Tanggal --}}
                    <div class="mb-2 row">
                        <label for="no_advice" class="col-md-2 col-form-label">No. Advices</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('no_advice') is-invalid @enderror" id="no_advice"
                                name="no_advice" value="{{ $penguji->no_uji }}" readonly
                                placeholder="Terisi otomatis sesuai urutan">
                            @error('no_advice')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="tanggal" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input type="date" value="{{ $penguji->tgl_uji }}"
                                class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal">
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
                                <option value="1" {{ $penguji->sp2d_online == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ $penguji->sp2d_online == '0' ? 'selected' : '' }}>Tidak</option>
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
                                <th>No</th>
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
                            {{-- @foreach ($rincian_penguji as $penguji)
                                <tr>
                                    <td>{{ $penguji->no_sp2d }}</td>
                                    <td>{{ $penguji->tgl_sp2d }}</td>
                                    <td>{{ $penguji->no_spm }}</td>
                                    <td>{{ $penguji->tgl_spm }}</td>
                                    <td>{{ rupiah($penguji->nilai) }}</td>
                                    <td>
                                        <button type="button"
                                            onclick="deleteData('{{ $penguji->no_sp2d }}','{{ $penguji->no_spm }}')"
                                            class="btn btn-danger btn-sm"><i class="uil-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.daftar_penguji.js.edit');
@endsection
