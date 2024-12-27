@extends('template.app')
@section('title', 'Tampil SP2D | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data SP2D
                </div>
                <div class="card-body">
                    @csrf
                    @if ($sp2d->status_bud == '1' )
                        <div class="alert alert-success" role="alert">
                            SP2D Sudah dicairkan!
                        </div>
                    @endif
                    @csrf
                    {{-- No Cair --}}
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <a href="{{ route('pencairan_sp2d.index') }}" class="btn btn-warning btn-md">Kembali</a>
                            @if ($sp2d->status_bud == '1')
                                <button class="btn btn-md btn-danger" id="pencairan" value="0">BATAL CAIR</button>
                            @else
                                <button class="btn btn-md btn-primary" id="pencairan" style="border: 1px solid black"
                                    value="1">CAIR</button>
                            @endif
                        </div>
                    </div>
                    {{-- No Cair --}}
                    <div class="mb-3 row">
                        <label for="no_kas" class="col-md-2 col-form-label">No Cair</label>
                        <div class="col-md-2">
                            @if ($sp2d->status_bud == '1')
                                <input type="text" class="form-control" id="no_kas" name="no_kas"
                                    value="{{ $sp2d->no_kas_bud }}" readonly>
                            @else
                                <input type="text" class="form-control" id="no_kas" name="no_kas"
                                    value="{{ $urut }}" readonly>
                            @endif

                        </div>
                        <label for="tgl_cair" class="col-md-1 col-form-label">Tgl Cair</label>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="tgl_cair" name="tgl_cair"
                                value="{{ $sp2d->tgl_kas_bud }}">
                        </div>
                        <label for="no_advice" class="col-md-1 col-form-label">No Advice</label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="no_advice" name="no_advice"
                                value="{{ $sp2d->no_uji }}" readonly>
                            <input type="text" class="form-control" id="no_kontrak" name="no_kontrak"
                                value="{{ $sp2d->nocek }}" readonly hidden>
                        </div>
                    </div>
                    {{-- Nilai dan Tanggal Terima --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nilai" name="nilai"
                                value="{{ rupiah($sp2d->nilai) }}" readonly>
                        </div>
                        <label for="tgl_terima" class="col-md-2 col-form-label">Tanggal Terima</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_terima" name="tgl_terima"
                                value="{{ $sp2d->tgl_terima }}" readonly>
                        </div>
                    </div>
                    {{-- No SP2D dan Tanggal SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">No SP2D</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_sp2d" name="no_sp2d"
                                value="{{ $sp2d->no_sp2d }}" readonly>
                        </div>
                        <label for="tgl_sp2d" class="col-md-2 col-form-label">Tanggal SP2D</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_sp2d" name="tgl_sp2d"
                                value="{{ $sp2d->tgl_sp2d }}" readonly>
                        </div>
                    </div>
                    {{-- No SPM dan Tanggal SPM --}}
                    <div class="mb-3 row">
                        <label for="no_spm" class="col-md-2 col-form-label">No SPM</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_spm" name="no_spm"
                                value="{{ $sp2d->no_spm }}" readonly>
                        </div>
                        <label for="tgl_spm" class="col-md-2 col-form-label">Tanggal SPM</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_spm" name="tgl_spm"
                                value="{{ $sp2d->tgl_spm }}" readonly>
                        </div>
                    </div>
                    {{-- No SPP dan Tanggal SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_spp" name="no_spp"
                                value="{{ $sp2d->no_spp }}" readonly>
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_spp" name="tgl_spp"
                                value="{{ $sp2d->tgl_spp }}" readonly>
                        </div>
                    </div>
                    {{-- OPD dan Bulan --}}
                    <div class="mb-3 row">
                        <label for="opd" class="col-md-2 col-form-label">OPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="opd" name="opd"
                                value="{{ $sp2d->kd_skpd }}" readonly>
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="bulan" name="bulan"
                                value="{{ bulan($sp2d->bulan) }}" readonly>
                        </div>
                    </div>
                    {{-- Nama OPD dan Keperluan --}}
                    <div class="mb-3 row">
                        <label for="nm_opd" class="col-md-2 col-form-label">Nama OPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_opd" name="nm_opd"
                                value="{{ $sp2d->nm_skpd }}" readonly>
                        </div>
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea type="text" class="form-control" id="keperluan" name="keperluan" readonly>{{ $sp2d->keperluan }}</textarea>
                        </div>
                    </div>
                    {{-- Nomor SPD dan Rekanan --}}
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">No SPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_spd" name="no_spd"
                                value="{{ $sp2d->no_spd }}" readonly>
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rekanan" name="rekanan"
                                value="{{ $sp2d->nmrekan }}" readonly>
                        </div>
                    </div>
                    {{-- Beban dan Bank --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="beban" name="beban"
                                value="{{ beban($sp2d->jns_spp) }}" readonly>
                        </div>
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="bank" name="bank"
                                value="{{ $sp2d->bank }}" readonly>
                        </div>
                    </div>
                    {{-- Jenis dan Nama Bank --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_jenis" name="nm_jenis"
                                value="{{ jenis($sp2d->jns_spp, $sp2d->jenis_beban) }}" readonly>
                            <input type="text" id="jenis" value="{{ $sp2d->jenis_beban }}" hidden>
                        </div>
                        <label for="nama_bank" class="col-md-2 col-form-label">Nama Bank</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nama_bank" name="nama_bank"
                                value="{{ bank($sp2d->bank) }}" readonly>
                        </div>
                    </div>
                    {{-- NPWP dan Rekening --}}
                    <div class="mb-3 row">
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="npwp" name="npwp"
                                value="{{ $sp2d->npwp }}" readonly>
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rekening" name="rekening"
                                value="{{ $sp2d->no_rek }}" readonly>
                        </div>
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
                                <th>No</th>
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
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total
                            Belanja</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly class="form-control" id="total_spm"
                                name="total" value="{{ rupiah($total_spm->nilai) }}">
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
                                <th>No</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total
                            Potongan</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly class="form-control"
                                id="total_potongan" name="total" value="{{ rupiah($total_potongan->nilai) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.pencairan_sp2d.js.show');
@endsection
