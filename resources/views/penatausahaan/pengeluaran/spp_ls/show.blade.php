@extends('template.app')
@section('title', 'Tampil SPP LS | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data Penagihan
                </div>
                <div class="card-body">
                    @csrf
                    @if ($sppls->sp2d_batal == '1')
                        <div class="alert alert-danger" role="alert">
                            SPP - SPM DALAM STATUS BATAL
                        </div>
                    @endif
                    <div class="alert alert-warning alert-block {{ $sppls->status == 1 ? '' : 'hidden' }}">
                        <b style="font-size:16px">Sudah di Buat SPM!!</b>
                    </div>

                    {{-- No Tersimpan dan Tanggal SPP --}}
                    <div class="mb-3 row">
                        <label for="no_tersimpan" class="col-md-2 col-form-label">No. Tersimpan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_tersimpan" name="no_tersimpan" required
                                readonly value="{{ $sppls->no_spp }}">
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control"
                                value="{{ \Carbon\Carbon::parse($sppls->tgl_spp)->locale('id')->isoFormat('D MMMM Y') }}"
                                id="tgl_spp" readonly name="tgl_spp">
                        </div>
                    </div>
                    {{-- No SPP dan Bulan --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No. SPP</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_spp" name="no_spp" required readonly
                                value="{{ $sppls->no_spp }}">
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="bulan" name="bulan" required readonly
                                value="{{ bulan($sppls->bulan) }}">
                        </div>
                    </div>
                    {{-- KD SKPD dan Keperluan --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $sppls->kd_skpd }}">
                        </div>
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea type="text" class="form-control" id="keperluan" name="keperluan" readonly>{{ $sppls->keperluan }}</textarea>
                        </div>
                    </div>
                    {{-- Nama SKPD dan Bank --}}
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $sppls->nm_skpd }}">
                        </div>
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="bank" name="bank" required readonly
                                value="{{ $bank->nama }}">
                        </div>
                    </div>
                    {{-- Beban dan Rekanan --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            @if ($sppls->jns_spp == '4')
                                <input class="form-control" type="text" id="beban" name="beban" required readonly
                                    value="LS GAJI">
                            @elseif ($sppls->jns_spp == '5')
                                <input class="form-control" type="text" id="beban" name="beban" required readonly
                                    value="LS Pihak Ketiga Lainnya">
                            @else
                                <input class="form-control" type="text" id="beban" name="beban" required readonly
                                    value="LS Barang Jasa">
                            @endif
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="rekanan" name="rekanan" required readonly
                                value="{{ $sppls->nmrekan }}">
                        </div>
                    </div>
                    {{-- Jenis dan Pimpinan --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="jenis" name="jenis" required readonly
                                value="{{ jenis($sppls->jns_spp, $sppls->jns_beban) }}">
                        </div>
                        <label for="pimpinan" class="col-md-2 col-form-label">Pimpinan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="pimpinan" name="pimpinan"
                                readonly value="{{ $sppls->pimpinan }}">
                        </div>
                    </div>
                    {{-- Nomor SPD dan Alamat Perusahaan --}}
                    <div class="mb-3 row">
                        <label for="nomor_spd" class="col-md-2 col-form-label">Nomor SPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nomor_spd" name="nomor_spd" readonly
                                value="{{ $sppls->no_spd }}">
                        </div>
                        <label for="alamat" class="col-md-2 col-form-label">Alamat Perusahaan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="alamat" name="alamat" readonly
                                value="{{ $sppls->alamat }}">
                        </div>
                    </div>
                    {{-- Tanggal SPD dan Nama Penerima --}}
                    <div class="mb-3 row">
                        <label for="tgl_spd" class="col-md-2 col-form-label">Tanggal SPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="tgl_spd" name="tgl_spd" required readonly
                                value="{{ \Carbon\Carbon::parse($tgl_spd->tgl_spd)->locale('id')->isoFormat('D MMMM Y') }}">
                        </div>
                        <label for="nm_penerima" class="col-md-2 col-form-label">Nama Penerima</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_penerima" name="nm_penerima" required
                                readonly value="{{ $sppls->penerima }}">
                        </div>
                    </div>
                    {{-- Kode Sub Kegiatan dan Rekening --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kode Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_sub_kegiatan" name="kd_sub_kegiatan"
                                required readonly value="{{ $sppls->kd_sub_kegiatan }}">
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rekening" name="rekening" readonly
                                value="{{ $sppls->no_rek }}">
                        </div>
                    </div>
                    {{-- Nama Sub Kegiatan dan NPWP --}}
                    <div class="mb-3 row">
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_sub_kegiatan" name="nm_sub_kegiatan"
                                readonly value="{{ $sppls->nm_sub_kegiatan }}">
                        </div>
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="npwp" name="npwp" readonly
                                value="{{ $sppls->npwp }}">
                        </div>
                    </div>
                    {{-- Tanggal Mulai dan Tanggal Akhir --}}
                    <div class="mb-3 row">
                        <label for="tgl_awal" class="col-md-2 col-form-label">Tanggal Mulai</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="tgl_awal" name="tgl_awal" required readonly
                                value="{{ $sppls->tgl_mulai? \Carbon\Carbon::parse($sppls->tgl_mulai)->locale('id')->isoFormat('D MMMM Y'): null }}">
                        </div>
                        <label for="tgl_akhir" class="col-md-2 col-form-label">Tanggal Akhir</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="tgl_akhir" name="tgl_akhir" readonly
                                value="{{ $sppls->tgl_akhir? \Carbon\Carbon::parse($sppls->tgl_akhir)->locale('id')->isoFormat('D MMMM Y'): null }}">
                        </div>
                    </div>
                    {{-- Lanjut dan Nomor Kontrak --}}
                    <div class="mb-3 row">
                        <label for="lanjut" class="col-md-2 col-form-label">Lanjut</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="lanjut" name="lanjut" readonly
                                value="{{ $sppls->lanjut == '1' ? 'Ya' : 'Tidak' }}">
                        </div>
                        <label for="no_kontrak" class="col-md-2 col-form-label">Nomor Kontrak</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control" id="no_kontrak" name="no_kontrak"
                                value="{{ $sppls->kontrak }}">
                        </div>
                    </div>
                    <div style="float: right;">
                        <a href="{{ route('sppls.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Detail SPP --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Detail SPP
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_sppls" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Sub Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Sumber</th>
                                <th>Volume</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_spp as $data)
                                <tr>
                                    <td>{{ $data->kd_sub_kegiatan }}</td>
                                    <td>{{ $data->kd_rek6 }}</td>
                                    <td>{{ $data->nm_rek6 }}</td>
                                    <td>{{ $data->nilai }}</td>
                                    <td>{{ $data->nm_sumber_dana1 }}</td>
                                    <td>{{ $data->volume }}</td>
                                    <td>{{ $data->satuan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly class="form-control" id="total"
                                name="total" value="{{ nilai($sppls->nilai) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
