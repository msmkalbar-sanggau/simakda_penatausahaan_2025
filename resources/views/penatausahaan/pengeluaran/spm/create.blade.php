@extends('template.app')
@section('title', 'Tambah SPM | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input SPM
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No SPP dan Tanggal SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No. SPP</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('no_spp') is-invalid @enderror"
                                style="width: 100%" id="no_spp" name="no_spp" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar SPP">
                                    @foreach ($data_spp as $spp)
                                        <option value="" disabled selected></option>
                                        <option value="{{ $spp->no_spp }}" data-tgl_spp="{{ $spp->tgl_spp }}"
                                            data-no_spd="{{ $spp->no_spd }}" data-bulan="{{ $spp->bulan }}"
                                            data-bank="{{ $spp->bank }}" data-kd_skpd="{{ $spp->kd_skpd }}"
                                            data-nm_skpd="{{ $spp->nm_skpd }}" data-keperluan="{{ $spp->keperluan }}"
                                            data-beban="{{ $spp->jns_spp }}" data-rekanan="{{ $spp->nmrekan }}"
                                            data-jenis="{{ $spp->jns_beban }}" data-npwp="{{ $spp->npwp }}"
                                            data-rekening="{{ $spp->no_rek }}">
                                            {{ $spp->no_spp }} | {{ $spp->kd_skpd }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('no_spp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control @error('tgl_spp') is-invalid @enderror" id="tgl_spp"
                                name="tgl_spp" readonly>
                            @error('tgl_spp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- No SPM dan Tanggal SPM --}}
                    <div class="mb-3 row">
                        <label for="no_spm" class="col-md-2 col-form-label">No. SPM</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="no_spm" name="no_spm" readonly>
                                <input type="text" class="form-control" id="urut" name="urut" hidden readonly>
                                <span class="input-group-btn">
                                    <button type="button" id="cari_nospm" class="btn btn-primary"><i
                                            class="uil-refresh"></i></button>
                                </span>
                                @error('no_spm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <label for="tgl_spm" class="col-md-2 col-form-label">Tanggal SPM</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control @error('tgl_spm') is-invalid @enderror" id="tgl_spm"
                                name="tgl_spm">
                            <input type="text" class="form-control @error('bulan_spm') is-invalid @enderror" id="bulan_spm"
                                name="bulan_spm" hidden readonly>
                            <input type="date" class="form-control @error('tgl_spm_lalu') is-invalid @enderror"
                                id="tgl_spm_lalu" name="tgl_spm_lalu" hidden>
                            @error('tgl_spm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- NO SPD dan Tanggal SPD --}}
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">No. SPD</label>
                        <div class="col-md-4">
                            <input class="form-control @error('no_spd') is-invalid @enderror" type="text" id="no_spd"
                                name="no_spd" required readonly>
                            @error('no_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="tgl_spd" class="col-md-2 col-form-label">Tanggal SPD</label>
                        <div class="col-md-4">
                            <input class="form-control @error('tgl_spd') is-invalid @enderror" type="date" id="tgl_spd"
                                name="tgl_spd" required readonly>
                            @error('tgl_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- OPD/Unit dan Bulan --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">OPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control @error('kd_skpd') is-invalid @enderror" type="text" id="kd_skpd"
                                name="kd_skpd" required readonly>
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <input class="form-control @error('bulan') is-invalid @enderror" type="text" id="bulan"
                                name="bulan" required hidden readonly>
                            <input class="form-control @error('nm_bulan') is-invalid @enderror" type="text"
                                id="nm_bulan" name="nm_bulan" required readonly>
                            @error('bulan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nama OPD/Unit dan Keperluan --}}
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama OPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control @error('nm_skpd') is-invalid @enderror" type="text"
                                id="nm_skpd" name="nm_skpd" required readonly>
                            @error('nm_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea name="keperluan" class="form-control" id="keperluan" readonly></textarea>
                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Beban dan Rekanan --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <input class="form-control @error('beban') is-invalid @enderror" type="text"
                                id="beban" name="beban" hidden required readonly>
                            <input class="form-control @error('nm_beban') is-invalid @enderror" type="text"
                                id="nm_beban" name="nm_beban" required readonly>
                            @error('beban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('rekanan') is-invalid @enderror"
                                value="{{ old('rekanan') }}" id="rekanan" name="rekanan" readonly>
                            @error('rekanan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Jenis dan Bank --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <input class="form-control @error('jenis') is-invalid @enderror" type="text"
                                id="jenis" name="jenis" required readonly hidden>
                            <input class="form-control @error('nm_jenis') is-invalid @enderror" type="text"
                                id="nm_jenis" name="nm_jenis" required readonly>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <input class="form-control @error('bank') is-invalid @enderror" type="text"
                                id="bank" name="bank" required readonly hidden>
                            <input class="form-control @error('nm_bank') is-invalid @enderror" type="text"
                                id="nm_bank" name="nm_bank" required readonly>
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- NPWP dan Rekening --}}
                    <div class="mb-3 row">
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input class="form-control @error('npwp') is-invalid @enderror" type="text"
                                id="npwp" name="npwp" required readonly>
                            @error('npwp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <input class="form-control @error('rekening') is-invalid @enderror" type="text"
                                id="rekening" name="rekening" required readonly>
                            @error('rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="simpan_spm" class="btn btn-primary btn-md">Simpan</button>
                        <a href="{{ route('spm.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Detail SPM --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Detail SPM
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_spm" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly
                                class="form-control @error('total') is-invalid @enderror" id="total" name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-center" id="konfirmasi_potongan" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p style="text-align: center">Apakah Anda ingin menambahkan potongan?</p>
                    <div class="mt-2" style="text-align: center">
                        <a href="#" id="potongan_spm" class="btn btn-primary btn-md">Ya</a>
                        <a href="{{ route('spm.index') }}" class="btn btn-danger btn-md">Tidak</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.spm.js.create');
@endsection
