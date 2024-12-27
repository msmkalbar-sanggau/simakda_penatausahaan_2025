@extends('template.app')
@section('title', 'Tampil SPM | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input SPM
                </div>
                <div class="card-body">
                    @if ($data_spm->sp2d_batal == '1')
                        <div class="alert alert-danger" role="alert">
                            SPP - SPM DALAM STATUS BATAL
                        </div>
                    @endif
                    @if ($data_spm->status == 1)
                        <div class="alert alert-warning alert-block">
                            <b style="font-size:16px">SPM sudah di verifikasi perbend, jika ada kesalahan dalam penginputan
                                pajak, silahkan hubungi perbend terlebih dahulu !!</b>
                        </div>
                    @endif
                    @if ($data_spm->status == 3)
                        <div class="alert alert-warning alert-block">
                            <b style="font-size:16px">Sudah di Buat SP2D!!</b>
                        </div>
                    @endif
                    @csrf
                    {{-- No SPP dan Tanggal SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No. SPP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_spp" value="{{ $data_spm->no_spp }}"
                                name="no_spp" readonly>
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="tgl_spp"
                                value="{{ tanggal($data_spm->tgl_spp) }}" name="tgl_spp" readonly>
                        </div>
                    </div>
                    {{-- No SPM dan Tanggal SPM --}}
                    <div class="mb-3 row">
                        <label for="no_spm" class="col-md-2 col-form-label">No. SPM</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_spm" value="{{ $data_spm->no_spm }}"
                                name="no_spm" readonly>
                        </div>
                        <label for="tgl_spm" class="col-md-2 col-form-label">Tanggal SPM</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="tgl_spm"
                                value="{{ tanggal($data_spm->tgl_spm) }}" name="tgl_spm" readonly>
                        </div>
                    </div>
                    {{-- NO SPD dan Tanggal SPD --}}
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">No. SPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_spd" value="{{ $data_spm->no_spd }}"
                                name="no_spd" readonly>
                        </div>
                        <label for="tgl_spd" class="col-md-2 col-form-label">Tanggal SPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="tgl_spd"
                                value="{{ tgl_spd($data_spm->no_spd) }}" name="tgl_spd" readonly>
                        </div>
                    </div>
                    {{-- OPD/Unit dan Bulan --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">OPD/Unit</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="kd_skpd" value="{{ $data_spm->kd_skpd }}"
                                name="kd_skpd" readonly>
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="bulan" value="{{ bulan($data_spm->bulan) }}"
                                name="bulan" readonly>
                        </div>
                    </div>
                    {{-- Nama OPD/Unit dan Keperluan --}}
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama OPD/Unit</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" value="{{ $data_spm->nm_skpd }}" id="nm_skpd"
                                name="nm_skpd" readonly>
                        </div>
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea type="text" class="form-control" id="keperluan" name="keperluan" readonly>{{ $data_spm->keperluan }}</textarea>
                        </div>
                    </div>
                    {{-- Beban dan Rekanan --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" value="{{ beban($data_spm->jns_spp) }}"
                                id="beban" name="beban" readonly>
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" value="{{ $data_spm->nmrekan }}" id="rekanan"
                                name="rekanan" readonly>
                        </div>
                    </div>
                    {{-- Jenis dan Bank --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="jenis"
                                value="{{ jenis($data_spm->jns_spp, $data_spm->jenis_beban) }}" name="jenis" readonly>
                        </div>
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="bank"
                                value="{{ bank($data_spm->bank) }}" name="bank" readonly>
                        </div>
                    </div>
                    {{-- NPWP dan Rekening --}}
                    <div class="mb-3 row">
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="npwp" value="{{ $data_spm->npwp }}"
                                name="npwp" readonly>
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rekening" value="{{ $data_spm->no_rek }}"
                                name="rekening" readonly>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
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
                            <input type="text" style="text-align: right" readonly class="form-control" id="total"
                                name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.spm.js.show');
@endsection
