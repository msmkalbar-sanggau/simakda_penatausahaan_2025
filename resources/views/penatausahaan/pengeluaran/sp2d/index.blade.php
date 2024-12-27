@extends('template.app')
@section('title', 'SP2D | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-md btn-success tipe" data-jenis="cair">Hijau : CAIR SP2D</button>
                    <button class="btn btn-md btn-danger tipe" data-jenis="batal">Merah : BATAL SP2D</button>
                    <button class="btn btn-md tipe" style="background-color: #bf00ff;color:white" data-jenis="nampung">Ungu
                        :
                        PENAMPUNG</button>
                    <input type="hidden" name="tipe" id="tipe">
                    <a href="{{ route('sp2d.create') }}" id="tambah_sp2d" class="btn btn-primary"
                        style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="sp2d" class="table" style="width: 100%;font-size:12px">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No.</th>
                                        <th style="width: 100px;text-align:center">No.SP2D</th>
                                        <th style="width: 100px;text-align:center">Tanggal Transfer</th>
                                        <th style="width: 50px;text-align:center">No.Penguji</th>
                                        <th style="width: 50px;text-align:center">No.SPM</th>
                                        <th style="width: 150px;text-align:center">Tanggal</th>
                                        <th style="width: 150px;text-align:center">SKPD</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($data_sp2d->chunk(5) as $data)
                                        @foreach ($data as $sp2d)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $sp2d->no_sp2d }}</td>
                                                <td>{{ $sp2d->no_spm }}</td>
                                                <td>{{ tanggal($sp2d->tgl_sp2d) }}</td>
                                                <td>{{ $sp2d->kd_skpd }}</td>
                                                <td style="text-align: center">
                                                    <a href="{{ route('sp2d.tampil', $sp2d->no_sp2d) }}"
                                                        class="btn btn-info btn-sm"><i class="uil-eye"></i></a>
                                                    <button type="button"
                                                        onclick="cetak('{{ $sp2d->no_sp2d }}', '{{ $sp2d->jns_spp }}', '{{ $sp2d->kd_skpd }}')"
                                                        class="btn btn-success btn-sm"><i class="uil-print"></i></button>
                                                    @if ($sp2d->status_bud != '1' || !$sp2d->no_advice)
                                                        <button type="button"
                                                            onclick="batal_sp2d('{{ $sp2d->no_sp2d }}', '{{ $sp2d->jns_spp }}', '{{ $sp2d->kd_skpd }}', '{{ $sp2d->no_spm }}','{{ $sp2d->no_spp }}','{{ $sp2d->status_bud }}')"
                                                            class="btn btn-danger btn-sm"><i class="uil-ban"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    {{-- modul batal sp2d --}}
    <div id="sp2d_batal" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">KETERANGAN PEMBATALAN SP2D</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-12 col-form-label">No SP2D</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="no_sp2d_batal" name="no_sp2d_batal">
                            <input type="text" readonly class="form-control" id="beban_batal" name="beban_batal" hidden>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="no_spm" class="col-md-2 col-form-label">No SPM</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="no_spm_batal" name="no_spm_batal">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="no_spp_batal" name="no_spp_batal">
                            <input type="text" readonly class="form-control" id="status_bud" name="status_bud" hidden>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="keterangan_batal" class="col-md-12 col-form-label">KETERANGAN PEMBATALAN SP2D</label>
                        <div class="col-md-12">
                            <textarea type="text" class="form-control" id="keterangan_batal" name="keterangan_batal"></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-danger" id="input_batal"><i
                                    class="uil-ban"></i>Batal SP2D</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i>Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modul cetak --}}
    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak SP2D</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-4 col-form-label">No SP2D</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control" id="no_sp2d" name="no_sp2d">
                            <input type="text" hidden class="form-control" id="beban" name="beban">
                            <input type="text" hidden class="form-control" id="kd_skpd" name="kd_skpd">
                        </div>
                    </div>
                    {{-- Penandatangan BUD --}}
                    <div class="mb-3 row">
                        <label for="bendahara" class="col-md-4 col-form-label">Penandatangan BUD</label>
                        <div class="col-md-8">
                            <select name="ttd_bud" class="form-control" id="ttd_bud">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd1 as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Penandatangan I --}}
                    {{-- <div class="mb-3 row">
                        <label for="ttd1" class="col-md-4 col-form-label">Penandatangan I</label>
                        <div class="col-md-8">
                            <select name="ttd1" class="form-control" id="ttd1">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd1 as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                    {{-- Penandatangan II --}}
                    {{-- <div class="mb-3 row">
                        <label for="ttd2" class="col-md-4 col-form-label">Penandatangan II</label>
                        <div class="col-md-8">
                            <select name="ttd2" class="form-control" id="ttd2">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ttd2 as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                    {{-- Jenis --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-4 col-form-label">Jenis</label>
                        <div class="col-md-8">
                            <select name="jenis" class="form-control" id="jenis">
                                {{-- <option value="" selected disabled>Silahkan Pilih</option> --}}
                                <option value="1" selected>Normal</option>
                                <option value="2">Keterangan Panjang</option>
                                <option value="3">Baris Manual</option>
                            </select>
                        </div>
                    </div>
                    {{-- KOP --}}
                    <div class="mb-3 row">
                        <label for="kop" class="col-md-4 col-form-label">KOP</label>
                        <div class="col-md-8">
                            <select name="kop" class="form-control" id="kop">
                                {{-- <option value="" selected disabled>Silahkan Pilih</option> --}}
                                <option value="0" selected>Tanpa KOP</option>
                                <option value="1">Dengan KOP</option>
                            </select>
                        </div>
                    </div>
                    {{-- Baris --}}
                    <div class="mb-3 row">
                        <label for="baris" class="col-md-4 col-form-label">Baris</label>
                        <div class="col-md-8">
                            <input type="number" value="1" min="1" class="form-control" id="baris"
                                name="baris">
                        </div>
                    </div>
                    {{-- Margin Atas --}}
                    <div class="mb-3 row">
                        <label for="baris" class="col-md-4 col-form-label">Margin Atas</label>
                        <div class="col-md-8">
                            <input type="number" value="1" min="1" class="form-control" id="margin_atas"
                                name="margin_atas">
                        </div>
                    </div>
                    {{-- SP2D, lampiran --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md sp2d orange" data-jenis="pdf"><b>SP2D</b></button>
                            <button type="button" class="btn btn-md lampiran orange"
                                data-jenis="pdf"><b>Lampiran</b></button>
                            {{-- <button type="button" class="btn btn-md lampiran_lama orange" id="lampiran_lama"
                                data-jenis="pdf"><b>Lampiran
                                    Lama</b></button> --}}
                            {{-- <button type="button" class="btn btn-md kelengkapan orange"
                                data-jenis="pdf"><b>Kelengkapan</b></button> --}}
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12" style="text-align: right">
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <style>
        .orange {
            background-color: #FFA500
        }
    </style>
    @include('penatausahaan.pengeluaran.sp2d.js.cetak')
@endsection
