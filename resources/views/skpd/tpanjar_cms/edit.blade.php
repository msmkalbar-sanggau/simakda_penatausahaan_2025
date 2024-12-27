@extends('template.app')
@section('title', 'Edit Data Tambah Panjar CMS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                   Edit Panjar CMS
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Nomor dan Tanggal --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No. Tambah Panjar</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" value="{{ $data_panjar->no_panjar }}" readonly>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly value="{{ tahun_anggaran() }}" hidden>
                        </div>
                        <label for="tgl_voucher" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_voucher" name="tgl_voucher" value="{{ $data_panjar->tgl_kas }}">
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $skpd->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $skpd->nm_skpd }}">
                        </div>
                    </div>
                    {{-- Kegiatan dan Nama Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kegiatan</label>
                        <div class="col-md-4">
                        <select class="form-control select2-multiple @error('kd_sub_kegiatan') is-invalid @enderror" style="width: 100%" id="kd_sub_kegiatan"
                                name="kd_sub_kegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($sub_kegiatan as $kd_sub_kegiatan)
                                            <option value="{{ $kd_sub_kegiatan->kd_sub_kegiatan }}"  data-nm_sub_kegiatan="{{ $kd_sub_kegiatan->nm_sub_kegiatan }}" 
                                                {{ $kd_sub_kegiatan->kd_sub_kegiatan == $data_panjar->kd_sub_kegiatan ? 'selected' : '' }}>
                                                {{ $kd_sub_kegiatan->kd_sub_kegiatan }} | {{ $kd_sub_kegiatan->nm_sub_kegiatan }}</option>
                                   @endforeach
                            </select>
                        </div>
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-md-4">
                       <input class="form-control" type="text" id="nm_sub_kegiatan" name="nm_sub_kegiatan" value="" required readonly>
                        </div>
                    </div>
                    {{-- No Panjar dan Nilai Panjar --}}
                    <div class="mb-3 row">
                        <label for="no_panjar_lalu" class="col-md-2 col-form-label">No Panjar</label>
                        <div class="col-md-4">
                        <select class="form-control select2-multiple" style="width: 100%" id="no_panjar_lalu"
                                name="no_panjar_lalu">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="{{ $no_panjar->no_panjar_lalu }}" data-nilaipanjar="{{$no_panjar->nilai}}"
                                    {{ $no_panjar->no_panjar_lalu == $data_panjar->no_panjar_lalu ? 'selected' : '' }}>
                                    {{ $no_panjar->no_panjar_lalu }} | {{ $no_panjar->nilai }}</option>
                            </select>
                        </div>
                        <label for="nilaipanjar" class="col-md-2 col-form-label">Nilai Panjar</label>
                        <div class="col-md-4">
                       <input class="form-control" type="text" id="nilaipanjar" name="nilaipanjar" value="{{ ($no_panjar->nilai) }}" required readonly>
                        </div>
                    </div>
                    {{-- Jenis Beban dan Jenis Pembayaran --}}
                    <div class="mb-3 row">
                    <label for="pembayaran" class="col-md-2 col-form-label">Jenis Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="BANK"  {{ $data_panjar->pay == 'BANK' ? 'selected' : '' }}> BANK</option>
                            </select>
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rek. Bank Bendahara</label>
                        <div class="col-md-4">
                        <select class="form-control select2-multiple" style="width: 100%" id="rekening" name="rekening">
                                <option value=" " selected disabled>Silahkan Pilih</option>
                                    <option value="{{ rtrim($daftar_rekening->rekening) }}"
                                    {{ $data_panjar->rekening_awal == rtrim($daftar_rekening->rekening) ? 'selected' : '' }}>
                                    {{ $daftar_rekening->rekening }}
                                    </option>
                            </select>
                        </div>
                    </div>
                   
                    {{-- Sisa Anggaran dan Sisa Bank --}}
                    <div class="mb-3 row">
                        <label for="nilaiang" class="col-md-2 col-form-label">Sisa Anggaran</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="nilaiang" id="nilaiang"  style="text-align: right" readonly  >
                        </div>
                        <label for="sisabank" class="col-md-2 col-form-label">Sisa Bank</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="sisabank" id="sisabank"
                                 style="text-align: right" readonly>
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" data-type="currency" name="nilai" id="nilai" onkeyup="hitung()" value="{{ ($data_panjar->nilai) }}"
                                 style="text-align: right">
                        </div>
                    </div>
                    {{-- Pajak --}}
                    <div class="mb-3 row">
                        <label for="nil_pot2" class="col-md-2 col-form-label">Pajak</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="nil_pot2" id="nil_pot2"  value="{{rupiah ($pajak) }}" style="text-align: right"readonly >
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan" >{{ $data_panjar->keterangan }}</textarea>
                        </div>
                    </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            @if ($data_panjar->status_upload == '1')
                                <button id="simpan_panjar" class="btn btn-primary btn-md" disabled>Simpan</button>
                            @else
                                <button id="simpan_panjar" class="btn btn-primary btn-md">Simpan</button>
                            @endif
                                <a href="{{ route('tpanjar_cms.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Rekening Tujuan --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Daftar Rekening Tujuan
                    <button type="button" style="float: right" id="tambah_rek_tujuan"
                        class="btn btn-primary btn-sm">Tambah</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rekening_tujuan" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                                <th>Tanggal</th>
                                <th>Rekening Awal</th>
                                <th>Nama</th>
                                <th>Rek. Tujuan</th>
                                <th>Bank</th>
                                <th>SKPD</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($rincian_rek_tujuan as $rekening_tujuan)
                                <tr>
                                    <td>{{ $rekening_tujuan->no_bukti }}</td>
                                    <td>{{ $rekening_tujuan->tgl_bukti }}</td>
                                    <td>{{ $rekening_tujuan->rekening_awal }}</td>
                                    <td>{{ $rekening_tujuan->nm_rekening_tujuan }}</td>
                                    <td>{{ $rekening_tujuan->rekening_tujuan }}</td>
                                    <td>{{ $rekening_tujuan->bank_tujuan }}</td>
                                    <td>{{ $rekening_tujuan->kd_skpd }}</td>
                                    <td>{{ rupiah($rekening_tujuan->nilai) }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            onclick="deleteRek('{{ $rekening_tujuan->nm_rekening_tujuan }}','{{ $rekening_tujuan->rekening_tujuan }}','{{ $rekening_tujuan->nilai }}')"
                                            class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total_transfer" class="col-md-8 col-form-label" style="text-align: right">Total
                            Transfer</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly
                                class="form-control @error('total_transfer') is-invalid @enderror" id="total_transfer"
                                name="total_transfer"  value="{{ rupiah($rekening_tujuan->total) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_rekening" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rekening Tujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nilai Potongan -->
                    <div class="mb-3 row">
                        <label for="nilpotongan" class="col-md-2 col-form-label">Nilai Total Potongan</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('nilpotongan') is-invalid @enderror"
                                name="nilpotongan" id="nilpotongan" style="text-align: right"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                            @error('nilpotongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nilpotongan" class="col-form-label">*Harus diisi jika ada potongan</label>
                        </div>
                    </div>
                    <!-- REKENING TUJUAN -->
                    <div class="mb-3 row">
                        <label for="rek_tujuan" class="col-md-2 col-form-label">Rekening Tujuan</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal1 @error('rek_tujuan') is-invalid @enderror"
                                style=" width: 100%;" id="rek_tujuan" name="rek_tujuan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($data_rek_tujuan as $rek_tujuan)
                                    <option value="{{ $rek_tujuan->rekening }}"
                                        data-nama="{{ $rek_tujuan->nm_rekening }}">{{ $rek_tujuan->rekening }} |
                                        {{ $rek_tujuan->nm_rekening }}</option>
                                @endforeach
                            </select>
                            @error('rek_tujuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nama Rekening Tujuan --}}
                    <div class="mb-3 row">
                        <label for="nm_rekening_tujuan" class="col-md-2 col-form-label">A.N. Rekening</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('nm_rekening_tujuan') is-invalid @enderror"
                                name="nm_rekening_tujuan" id="nm_rekening_tujuan" readonly>
                            @error('nm_rekening_tujuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Bank -->
                    <div class="mb-3 row">
                        <label for="nm_bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-6">
                            <select class="form-control select2-modal1 @error('nm_bank') is-invalid @enderror"
                                style=" width: 100%;" id="nm_bank" name="nm_bank">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($data_bank as $nm_bank)
                                    <option value="{{ $nm_bank->nama }}" data-nama="{{ $nm_bank->nama }}">
                                        {{ $nm_bank->nama }}</option>
                                @endforeach
                            </select>
                            @error('nm_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nilai Transfer --}}
                    <div class="mb-3 row">
                        <label for="nilai_transfer" class="col-md-2 col-form-label">Nilai Transfer</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('nilai_transfer') is-invalid @enderror"
                                name="nilai_transfer" id="nilai_transfer" style="text-align: right"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                            @error('nilai_transfer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="catatan" class="col-md-12 col-form-label" style="color: red">PERHATIAN!!!</label>
                        <label for="" class="col-md-12 col-form-label" style="color: red">1. Jika rekening tujuan
                            tidak ada, silahkan
                            Anda input terlebih dahulu di menu
                            MASTER > REKENING BANK</label>
                        <label for="catatan" class="col-md-12 col-form-label" style="color: red">2. Jangan input
                            rekening tujuan secara
                            manual, karena akan terkendala saat unduh csv di menu Upload Transaksi (CMS)</label>
                    </div>
                    {{-- Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_rekening_tujuan" class="btn btn-md btn-primary">Simpan</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.tpanjar_cms.js.edit');
@endsection
