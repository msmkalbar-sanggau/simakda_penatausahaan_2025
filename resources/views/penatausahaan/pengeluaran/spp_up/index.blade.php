@extends('template.app')
@section('title', 'SPP UP | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('sppup.create') }}" id="tambah_spp_ls" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="spp_ls" class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 25px">No.</th>
                                        <th style="width: 150px">Nomor SPP</th>
                                        <th style="width: 100px">Tanggal</th>
                                        <th style="width: 100px">Keterangan</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($data_spp as $data)
                                        <tr>
                                            <td style="width: 25px">{{ $loop->iteration }}</td>
                                            <td style="width: 150px">{{ $data->no_spp }}</td>
                                            <td style="width: 100px">
                                                {{ \Carbon\Carbon::parse($data->tgl_spp)->locale('id')->isoFormat('D MMMM Y') }}
                                            </td>
                                            <td style="width: 100px">{{ Str::limit($data->keperluan, 20) }}</td>
                                            <td style="width: 200px">
                                                <a href="{{ route('sppup.edit', $data->no_spp) }}"
                                                    class="btn btn-warning btn-sm"><i class="uil-edit"></i></a>
                                                <button type="button"
                                                    onclick="cetak('{{ $data->no_spp }}', '{{ $data->jns_spp }}', '{{ $data->kd_skpd }}')"
                                                    class="btn btn-success btn-sm"><i class="uil-print"></i></button>
                                                @if ($data->status == 0)
                                                    <a href="javascript:void(0);"
                                                        onclick="deleteData('{{ $data->no_spp }}');"
                                                        class="btn btn-danger btn-sm" id="delete"><i
                                                            class="fas fa-trash-alt"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    {{-- modal cetak sppls --}}
    <div id="modal_cetak" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak SPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control" id="no_spp" name="no_spp">
                            <input type="text" hidden class="form-control" id="beban" name="beban">
                            <input type="text" hidden class="form-control" id="kd_skpd" name="kd_skpd">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" id="tanpa_tanggal">
                                <label class="form-check-label" for="tanpa_tanggal">Tanpa Tanggal</label>
                            </div>
                        </div>
                    </div>
                    {{-- Bendahara --}}
                    <div class="mb-3 row">
                        <label for="bendahara" class="col-md-2 col-form-label">Bendahara</label>
                        <div class="col-md-6">
                            <select name="bendahara" class="form-control select2-multiple" id="bendahara">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($bendahara as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_bendahara" id="nama_bendahara" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- PPTK --}}
                    <div class="mb-3 row">
                        <label for="pptk" class="col-md-2 col-form-label">PPTK</label>
                        <div class="col-md-6">
                            <select name="pptk" class="form-control select2-multiple" id="pptk">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($pptk as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_pptk" id="nama_pptk" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- PA/KPA --}}
                    <div class="mb-3 row">
                        <label for="pa_kpa" class="col-md-2 col-form-label">PA/KPA</label>
                        <div class="col-md-6">
                            <select name="pa_kpa" class="form-control select2-multiple" id="pa_kpa">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($pa_kpa as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_pa_kpa" id="nama_pa_kpa" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- PPKD --}}
                    <div class="mb-3 row">
                        <label for="ppkd" class="col-md-2 col-form-label">PPKD</label>
                        <div class="col-md-6">
                            <select name="ppkd" class="form-control select2-multiple" id="ppkd">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ppkd as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_ppkd" id="nama_ppkd" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- Pengantar, Ringkasan --}}
                    <div class="mb-3 row">
                        <label for="pengantar" class="col-md-2 col-form-label">Pengantar</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md pengantar" data-jenis="pdf" name="pengantar_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pengantar" data-jenis="layar" name="pengantar_layar">Layar</button>
                        </div>
                        <label for="ringkasan" class="col-md-2 col-form-label">Ringkasan</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md ringkasan" data-jenis="pdf" name="ringkasan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md ringkasan" data-jenis="layar" name="ringkasan_layar">Layar</button>
                        </div>

                    </div>
                    {{-- Rincian, Pernyataan --}}
                    <div class="mb-3 row">
                        <label for="rincian" class="col-md-2 col-form-label">Rincian</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md rincian" data-jenis="pdf" name="rincian_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md rincian" data-jenis="layar" name="rincian_layar">Layar</button>
                        </div>
                        <label for="pernyataan" class="col-md-2 col-form-label">Pernyataan</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md pernyataan" data-jenis="pdf" name="pernyataan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pernyataan" data-jenis="layar" name="pernyataan_layar">Layar</button>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label for="permendagri" style="text-align: center" class="col-md-12 col-form-label">Permendagri
                            77</label>
                    </div>
                    {{-- SPP dan RINCIAN --}}
                    <div class="mb-3 row">
                        <label for="spp" class="col-md-2 col-form-label">SPP</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md spp" data-jenis="pdf" name="spp_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md spp" data-jenis="layar" name="spp_layar">Layar</button>
                        </div>
                        <label for="rincian77" class="col-md-2 col-form-label">Rincian</label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-md rincian77" data-jenis="pdf" name="rincian77_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md rincian77" data-jenis="layar" name="rincian77_layar">Layar</button>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="batal_spp" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">KETERANGAN PEMBATALAN SPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp_batal" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="no_spp_batal" name="no_spp_batal">
                            <input type="text" readonly class="form-control" id="beban_batal" name="beban_batal" hidden>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="keterangan_batal" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-12">
                            <textarea type="text" class="form-control" id="keterangan_batal" name="keterangan_batal"></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-danger" id="batal_sppls"><i class="uil-ban"></i>Batal SPP</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal"><i class="fa fa-undo"></i>Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.spp_up.js.index')
@endsection
