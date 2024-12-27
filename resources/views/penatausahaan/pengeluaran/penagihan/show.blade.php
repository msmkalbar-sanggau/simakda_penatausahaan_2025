@extends('template.app')
@section('title', 'Tampil Penagihan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data Penagihan
                </div>
                <div class="card-body">
                    <!-- No. Bast / Penagihan Tanggal Penagihan -->
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No.BAST / Penagihan</label>
                        <div class="col-md-4">
                            <input class="form-control" id="no_bukti" value="{{ $data_tagih->no_bukti }}" name="no_bukti"
                                readonly required>
                        </div>
                        <label for="tgl_bukti" class="col-md-2 col-form-label">Tanggal Penagihan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control"
                                value="{{ \Carbon\Carbon::parse($data_tagih->tgl_bukti)->locale('id')->isoFormat('D MMMM Y') }}"
                                readonly id="tgl_bukti" name="tgl_bukti">
                        </div>
                    </div>
                    <!-- Kode SKPD Nama SKPD -->
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode OPD / Unit</label>
                        <div class="col-md-4">
                            <input type="text" readonly name="kd_skpd" id="kd_skpd" value="{{ $data_tagih->kd_skpd }}"
                                class="form-control">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama OPD / Unit</label>
                        <div class="col-md-4">
                            <input class="form-control" value="{{ $data_tagih->nm_skpd }}" readonly type="text"
                                id="nm_skpd" name="nm_skpd">
                        </div>
                    </div>
                    <!-- Keterangan Keterangan BAST -->
                    <div class="mb-3 row">
                        <label for="ket" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-4">
                            <textarea class="form-control" type="text" id="ket" name="ket" readonly>{{ $data_tagih->ket }}</textarea>
                        </div>
                        <label for="ket_bast" class="col-md-2 col-form-label">Keterangan (BA)</label>
                        <div class="col-md-4">
                            <textarea type="text" name="ket_bast" id="ket_bast" readonly class="form-control">{{ $data_tagih->ket_bast }}</textarea>
                        </div>
                    </div>
                    <!-- No BA Penyelesaian pekerjaan & serah terima pekerjaan  -->
                    <div class="mb-3 row">
                        <label for="bapp" class="col-md-2 col-form-label">No BA Penyelesaian Pekerjaan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text"
                                placeholder="Silahkan isi No BA Penyelesaian Pekerjaan" id="bapp" name="bapp"
                                value="{{ $data_tagih->no_bapp }}" readonly></input>
                        </div>
                        <label for="basthp" class="col-md-2 col-form-label">No BA Serah Terima Hasil Pekerjaan</label>
                        <div class="col-md-4">
                            <input type="text" name="basthp"
                                placeholder="Silahkan isi dengan NO BA Serah Terima Hasil Pekerjaan " id="basthp"
                                value="{{ $data_tagih->no_basthp }}" class="form-control" readonly></input>
                        </div>
                    </div>
                    <!-- No BA Pembayaran  -->
                    <div class="mb-3 row">
                        <label for="bap" class="col-md-2 col-form-label">No BA Pembayaran</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" placeholder="Silahkan isi No BA Pembayaran"
                                value="{{ $data_tagih->no_bap }}" id="bap" name="bap" readonly></input>
                        </div>
                    </div>
                    <!-- Status Jenis -->
                    <div class="mb-3 row">
                        <label for="status_bayar" class="col-md-2 col-form-label">Status</label>
                        <div class="col-md-4">
                            <input type="text" readonly name="status_bayar"
                                value="{{ $data_tagih->status == '1' ? 'Selesai' : 'Belum Selesai' }}" id="status_bayar"
                                class="form-control">
                        </div>
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            @if ($data_tagih->jenis == '')
                                <input type="text" readonly name="jenis" value="Tanpa Termin / Sekali Pembayaran"
                                    id="jenis" class="form-control">
                            @elseif ($data_tagih->jenis == '1')
                                <input type="text" readonly name="jenis" value="Konstruksi Dalam Pengerjaan"
                                    id="jenis" class="form-control">
                            @elseif ($data_tagih->jenis == '2')
                                <input type="text" readonly name="jenis" value="Uang Muka" id="jenis"
                                    class="form-control">
                            @elseif ($data_tagih->jenis == '3')
                                <input type="text" readonly name="jenis" value="Hutang Tahun Lalu" id="jenis"
                                    class="form-control">
                            @elseif ($data_tagih->jenis == '4')
                                <input type="text" readonly name="jenis" value="Perbulan" id="jenis"
                                    class="form-control">
                            @elseif ($data_tagih->jenis == '5')
                                <input type="text" readonly name="jenis" value="Bertahap" id="jenis"
                                    class="form-control">
                            @elseif ($data_tagih->jenis == '6')
                                <input type="text" readonly name="jenis"
                                    value="Berdasarkan Progres / Pengajuan Pekerjaan" id="jenis"
                                    class="form-control">
                            @endif
                        </div>
                    </div>
                    <!-- No Kontrak Rekanan -->
                    <div class="mb-3 row">
                        <label for="no_kontrak" class="col-md-2 col-form-label">Nomor Kontrak</label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $data_tagih->kontrak }}" readonly name="no_kontrak"
                                id="no_kontrak" class="form-control">
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $data_tagih->nm_rekanan }}" readonly name="rekanan"
                                id="rekanan" class="form-control">
                        </div>
                    </div>
                    <!-- No Kontrak Rekanan -->
                    <div class="mb-3 row">
                        <label for="no_kontrak" class="col-md-2 col-form-label">
                            <fieldset></fieldset>
                        </label>
                        <form class="form-floating">
                            <input type="text" value="{{ $data_tagih->nm_kerja }} || {{ $data_tagih->nmpel }}"
                                readonly name="no_kontrak" id="no_kontrak" class="form-control">
                            <label for="floatingInputValue">Detail Kontrak</label>

                        </form>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <a href="{{ route('penagihan.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rincian Penagihan --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Rincian Penagihan
                </div>
                <div class="card-body">
                    <table id="rincian_penagihan" class="table">
                        <thead>
                            <tr>
                                <th>Kode Sub Kegiatan</th>
                                <th>Kode Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Sumber</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_tagih as $data)
                                <tr>
                                    <td>{{ $data->kd_sub_kegiatan }}</td>
                                    <td>{{ $data->kd_rek6 }}</td>
                                    <td>{{ $data->nm_rek6 }}</td>
                                    <td>{{ rupiah($data->nilai) }}</td>
                                    <td>{{ $data->sumber }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Totalan --}}
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 row">
                        <label for="total_nilai" class="col-md-4 col-form-label">Total</label>
                        <div class="col-md-8">
                            <input type="text" readonly style="text-align: right"
                                value="{{ rupiah($data_tagih->total) }}" class="form-control" name="total_nilai"
                                id="total_nilai">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nilai_lalu" class="col-md-4 col-form-label">Nilai
                            Lalu</label>
                        <div class="col-md-8">
                            <input type="text" readonly style="text-align: right" class="form-control"
                                name="nilai_lalu" id="nilai_lalu" value="{{ rupiah($dttagih->lalu) }}">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nilai_kontrak" class="col-md-4 col-form-label">Nilai
                            Kontrak</label>
                        <div class="col-md-8">
                            <input type="text" readonly style="text-align: right" class="form-control"
                                name="nilai_kontrak" value="{{ $kontrak ? rupiah($kontrak->nilai) : '' }}"
                                id="nilai_kontrak">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="sisa_kontrak" class="col-md-4 col-form-label">Sisa
                            Kontrak</label>
                        <div class="col-md-8">
                            <input type="text" readonly style="text-align: right" class="form-control"
                                name="sisa_kontrak" id="sisa_kontrak"
                                value="{{ $kontrak ? rupiah($kontrak->nilai - $data_tagih->total) : '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
