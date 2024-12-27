@extends('template.app')
@section('title', 'Pengesahan SPM TU | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    INPUT SPM
                </div>
                <div class="card-body">
                    @csrf
                    @if ($spm->status == 1 && $spm->sts_setuju == 1)
                        <div class="alert alert-danger" role="alert">
                            Sudah disahkan dan dibuat SP2D...!!!
                        </div>
                    @elseif ($spm->status == 1 && ($spm->sts_setuju == 0 || $spm->sts_setuju == ''))
                        <div class="alert alert-danger" role="alert">
                            Sudah dibuat SP2D...!!!
                        </div>
                    @elseif (($spm->status == 0 || $spm->status == '') && $spm->sts_setuju == 1)
                        <div class="alert alert-danger" role="alert">
                            Sudah disahkan...!!!
                        </div>
                    @endif
                    {{-- No SPM dan Tanggal SPM --}}
                    <div class="mb-3 row">
                        <label for="no_spm" class="col-md-2 col-form-label">No. SPM</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_spm" name="no_spm"
                                value="{{ $spm->no_spm }}" required readonly>
                        </div>
                        <label for="tgl_spm" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_spm" name="tgl_spm" required
                                value="{{ $spm->tgl_spm }}" readonly>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $spm->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $spm->nm_skpd }}">
                        </div>
                    </div>
                    {{-- No SPD dan Beban --}}
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">No SPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_spd" name="no_spd" required readonly
                                value="{{ $spm->no_spd }}">
                        </div>
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="beban" name="beban" required readonly
                                value="SPM TU">
                        </div>
                    </div>
                    {{-- Bank dan Nama Bank --}}
                    <div class="mb-3 row">
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="bank" name="bank" required readonly
                                value="{{ $spm->bank }}">
                        </div>
                        <label for="nama_bank" class="col-md-2 col-form-label">Nama Bank</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nama_bank" name="nama_bank" required readonly
                                value="{{ nama_bank($spm->bank) }}">
                        </div>
                    </div>
                    {{-- Rekening dan Bulan --}}
                    <div class="mb-3 row">
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="rekening" name="rekening" required readonly
                                value="{{ $spm->no_rek }}">
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="bulan" name="bulan" required readonly
                                value="{{ bulan($spm->bulan) }}">
                        </div>
                    </div>
                    {{-- Keperluan --}}
                    <div class="mb-3 row">
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keperluan" name="keperluan" readonly>{{ $spm->keperluan }}</textarea>
                        </div>
                    </div>
                    <!-- SETUJUI, BATAL SETUJUI DAN KEMBALI -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="setuju" class="btn btn-primary btn-md">Setujui</button>
                            <button id="batal_setuju" class="btn btn-danger btn-md">Batal Setujui</button>
                            <a href="{{ route('pengesahan_spm_tu.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Detail SPP --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Detail SPP
                </div>
                <div class="card-body table-responsive">
                    <table id="detail_spm" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Jumlah</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right;background-color:white;border:none;" readonly
                                class="form-control" id="total" name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal batal spm spp --}}
    <div id="spm_batal" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">KETERANGAN PEMBATALAN SPM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spm" class="col-md-12 col-form-label">No SPM</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="no_spm_batal" name="no_spm_batal">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="no_spp_batal" name="no_spp_batal">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="keterangan_batal" class="col-md-12 col-form-label">KETERANGAN PEMBATALAN SPM</label>
                        <div class="col-md-12">
                            <textarea type="text" class="form-control" id="keterangan_batal" name="keterangan_batal"></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-danger" id="input_batal"><i
                                    class="uil-ban"></i>Batal SPM - SPP</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i>Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('bud.pengesahan_spm_tu.js.edit');
@endsection
