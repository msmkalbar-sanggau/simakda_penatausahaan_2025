@extends('template.app')
@section('title', 'Tampil SP2D | SIMAKDA')
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
                    {{-- No SPM dan Tanggal SPM --}}
                    <div class="mb-3 row">
                        <label for="no_spm" class="col-md-2 col-form-label">No. SPM</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" name="" id="no_spm"
                                value="{{ $sp2d->no_spm }}" readonly>
                        </div>
                        <label for="tgl_spm" class="col-md-2 col-form-label">Tanggal SPM</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="tgl_spm" value="{{ tanggal($sp2d->tgl_spm) }}"
                                readonly>
                        </div>
                    </div>
                    {{-- No SP2D dan Tanggal SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">No. SP2D</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_sp2d" value="{{ $sp2d->no_sp2d }}" readonly>
                        </div>
                        <label for="tgl_sp2d" class="col-md-2 col-form-label">Tanggal SP2D</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="tgl_sp2d" value="{{ tanggal($sp2d->tgl_sp2d) }}"
                                readonly>
                        </div>
                    </div>
                    {{-- No SPP dan Tanggal SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No. SPP</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_spp" value="{{ $sp2d->no_spp }}" readonly>
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="tgl_spp" value="{{ tanggal($sp2d->tgl_spp) }}"
                                readonly>
                        </div>
                    </div>
                    {{-- KD SKPD dan NM SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" value="{{ $sp2d->kd_skpd }}" readonly>
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" value="{{ $sp2d->nm_skpd }}" readonly>
                        </div>
                    </div>
                    {{-- Bulan dan Rekanan --}}
                    <div class="mb-3 row">
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="bulan" value="{{ bulan($sp2d->bulan) }}"
                                readonly>
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nmrekan" value="{{ $sp2d->nmrekan }}" readonly>
                        </div>
                    </div>
                    {{-- Jenis dan Keperluan --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="jenis_beban"
                                value="{{ jenis($sp2d->jns_spp, $sp2d->jenis_beban) }}" readonly>
                        </div>
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea name="keperluan" class="form-control" id="keperluan" readonly>{{ $sp2d->keperluan }}</textarea>
                        </div>
                    </div>
                    {{-- Nomor SPD dan Jenis SPD --}}
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">Nomor SPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_spd" value="{{ $sp2d->no_spd }}"
                                readonly>
                        </div>
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="beban"
                                value="{{ beban($sp2d->jns_spp) }}" readonly>
                        </div>
                    </div>
                    {{-- Kode Bank dan Nama Bank --}}
                    <div class="mb-3 row">
                        <label for="kode_bank" class="col-md-2 col-form-label">Kode Bank</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="bank" value="{{ $sp2d->bank }}"
                                readonly>
                        </div>
                        <label for="nama_bank" class="col-md-2 col-form-label">Nama Bank</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="bank" value="{{ bank($sp2d->bank) }}"
                                readonly>
                        </div>
                    </div>
                    {{-- NPWP dan Rekening --}}
                    <div class="mb-3 row">
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="npwp" value="{{ $sp2d->npwp }}"
                                readonly>
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Nomor Rekening</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_rek" value="{{ $sp2d->no_rek }}"
                                readonly>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <a href="{{ route('sp2d.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
                                <th>Sisa</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly class="form-control"
                                value="{{ rupiah($total_rincian->nilai) }}" id="total_spm" name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- List Potongan --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Potongan
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_potongan" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Memotong SP2D</th>
                                <th>Id Billing</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly class="form-control"
                                value="{{ rupiah($total_potongan->nilai) }}" id="total_potongan" name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.sp2d.js.create');
@endsection
