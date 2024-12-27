@extends('template.app')
@section('title', 'Tambah Potongan Pajak | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Potongan
                </div>
                <div class="card-body">
                    @csrf
                    {{-- Nomor Bukti dan Tanggal --}}
                    <div class="mb-3 row">
                        <label for="no_bukti" class="col-md-2 col-form-label">No Bukti</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_bukti" name="no_bukti" required
                                value="{{ $no_urut }}">
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ $tahun_anggaran }}">
                        </div>
                        <label for="tgl_bukti" class="col-md-2 col-form-label">Tanggal</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_bukti" name="tgl_bukti">
                        </div>
                    </div>
                    {{-- No Terima dan NTPN --}}
                    <div class="mb-3 row">
                        <label for="no_terima" class="col-md-2 col-form-label">No Terima</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="no_terima"
                                name="no_terima">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_terima as $terima)
                                    <option value="{{ $terima->no_bukti }}" data-tgl_bukti="{{ $terima->tgl_bukti }}"
                                        data-ket="{{ $terima->ket }}" data-no_sp2d="{{ $terima->no_sp2d }}"
                                        data-npwp="{{ $terima->npwp }}" data-jns_spp="{{ $terima->jns_spp }}"
                                        data-kd_sub_kegiatan="{{ $terima->kd_sub_kegiatan }}"
                                        data-nm_sub_kegiatan="{{ $terima->nm_sub_kegiatan }}"
                                        data-kd_rek6="{{ $terima->kd_rek6 }}" data-nm_rek6="{{ $terima->nm_rek6 }}"
                                        data-alamat="{{ $terima->alamat }}" data-nmrekan="{{ $terima->nmrekan }}"
                                        data-pimpinan="{{ $terima->pimpinan }}" data-kd_skpd="{{ $terima->kd_skpd }}"
                                        data-nm_skpd="{{ $terima->nm_skpd }}">
                                        {{ $terima->no_bukti }} |
                                        {{ $terima->tgl_bukti }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="ntpn" class="col-md-2 col-form-label">NTPN</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="ntpn" name="ntpn" readonly>
                        </div>
                    </div>
                    {{-- NO SP2D dan Pembayaran --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">No SP2D</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="no_sp2d" name="no_sp2d" readonly>
                        </div>
                        <label for="pembayaran" class="col-md-2 col-form-label">Pembayaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="pembayaran"
                                name="pembayaran">
                                <option value=" " selected>TUNAI</option>
                                <option value="BANK">BANK</option>
                            </select>
                        </div>
                    </div>
                    {{-- Kode Kegiatan dan Nama Kegiatan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kode Kegiatan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="kd_sub_kegiatan" name="kd_sub_kegiatan" readonly>
                        </div>
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Kegiatan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_sub_kegiatan" name="nm_sub_kegiatan" readonly>
                        </div>
                    </div>
                    {{-- Kode Rekening dan Nama Rekening --}}
                    <div class="mb-3 row">
                        <label for="kd_rekening" class="col-md-2 col-form-label">Kode Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="kd_rekening" name="kd_rekening" readonly>
                        </div>
                        <label for="nm_rekening" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_rekening" name="nm_rekening" readonly>
                        </div>
                    </div>
                    {{-- Rekanan dan Pimpinan --}}
                    <div class="mb-3 row">
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rekanan" name="rekanan" readonly>
                        </div>
                        <label for="pimpinan" class="col-md-2 col-form-label">Pimpinan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="pimpinan" name="pimpinan" readonly>
                        </div>
                    </div>
                    {{-- Beban dan NPWP --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="beban" name="beban" readonly hidden>
                            <input type="text" class="form-control" id="nama_beban" name="nama_beban" readonly>
                        </div>
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="npwp" name="npwp" readonly>
                        </div>
                    </div>
                    {{-- Alamat Perusahaan --}}
                    <div class="mb-3 row">
                        <label for="alamat" class="col-md-2 col-form-label">Alamat</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="alamat" name="alamat" readonly></textarea>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    {{-- SKPD dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly>
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" required readonly>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan_potongan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('skpd.setor_potongan.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Input Detail --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Potongan
                </div>
                <div class="card-body table-responsive">
                    <table id="list_potongan" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID TERIMA POTONGAN</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Rekanan</th>
                                <th>NPWP</th>
                                <th>Nilai</th>
                                <th>NTPN</th>
                                <th>No Billing</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <hr>
                    <table style="width: 100%">
                        <tbody>
                            <tr>
                                <td style="padding-left: 600px">Total</td>
                                <td>:</td>
                                <td style="text-align: right"><input type="text"
                                        style="border:none;background-color:white;text-align:right" class="form-control"
                                        readonly id="total_potongan">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal NTPN --}}
    <div id="modal_ntpn" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">EDIT NTPN/E-Billing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Catatan --}}
                    <div class="mb-3 row">
                        <label for="catatan" class="col-md-12 col-form-label">PERHATIAN!!!</label>
                        <label for="" class="col-md-12 col-form-label">Jika status sukses dan NTPN tidak muncul
                            harap infokan ke pihak bank kalbar, karena simakda mengambil data dari bank kalbar.</label>
                    </div>
                    <hr>
                    {{-- Id Terima Potongan dan Id Setor Potongan --}}
                    <div class="mb-3 row">
                        <label for="id_terima" class="col-md-2 col-form-label">ID Terima Potongan</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control" id="id_terima" name="id_terima">
                        </div>
                        <label for="id_setor" class="col-md-2 col-form-label">ID Setor Potongan</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control" id="id_setor" name="id_setor">
                        </div>
                    </div>
                    {{-- Rekening dan Nama Rekening --}}
                    <div class="mb-3 row">
                        <label for="kd_rek6" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control" id="kd_rek6" name="kd_rek6">
                        </div>
                        <label for="nm_rek6" class="col-md-2 col-form-label">Nama Rekening</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control" id="nm_rek6" name="nm_rek6">
                        </div>
                    </div>
                    {{-- Nilai dan ID Billing --}}
                    <div class="mb-3 row">
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control" maxlength="15" id="nilai"
                                name="nilai">
                        </div>
                        <label for="id_billing" class="col-md-2 col-form-label">ID Billing</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="id_billing" name="id_billing" readonly>
                                <span class="input-group-btn">
                                    <button type="button" id="cek_billing" class="btn btn-primary"><i
                                            class="uil-refresh"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- ID Billing Validasi dan NTPN Validasi --}}
                    <div class="mb-3 row">
                        <label for="id_billing_validasi" class="col-md-2 col-form-label">ID Billing Validasi</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="id_billing_validasi"
                                name="id_billing_validasi">
                        </div>
                        <label for="ntpn_validasi" class="col-md-2 col-form-label">NTPN Validasi</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="ntpn_validasi" name="ntpn_validasi">
                        </div>
                    </div>
                    <br>
                    {{-- Simpan, Cetak Bukti dan Keluar --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-primary" id="simpan_ntpn">Simpan</button>
                            <button type="button" class="btn btn-md btn-dark" id="cetak_bukti">Cetak Bukti</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                    <hr>
                    {{-- Catatan --}}
                    <div class="mb-3 row">
                        <label for="catatan" class="col-md-12 col-form-label">PERHATIAN!!!</label>
                        <label for="" class="col-md-12 col-form-label">1. Silahkan masukkan nomor/id billing pada
                            kolom berwarna hijau yang sudah disediakan.</label>
                        <label for="catatan" class="col-md-12 col-form-label">2. Klik Tombol cek yang ada disebelah kolom
                            input nomor/id billing</label>
                        <label for="catatan" class="col-md-12 col-form-label">3. Apabila Id Billing valid maka id billing
                            dan NTPN validasi akan terisi, dan silahkan klik simpan</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('skpd.setor_potongan.js.create');
@endsection
