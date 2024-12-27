@extends('template.app')
@section('title', 'Edit Verifikasi SPP | SIMAKDA')
@section('content')
    <style>
        input[type='checkbox'] {
            width: 20px;
            height: 20px;
            border-radius: 5px;
            border: 2px solid #555;
        }

        input[type="checkbox"] {
            outline: 3px solid black;
        }
    </style>
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Verifikasi SPP
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No SPP dan Tanggal SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No. SPP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('no_spp') is-invalid @enderror" id="no_spp"
                                name="no_spp" readonly value="{{ $trhspp->no_spp ?? '' }}">
                            @error('no_spp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control @error('tgl_spp') is-invalid @enderror" id="tgl_spp"
                                name="tgl_spp" readonly value="{{ $trhspp->tgl_spp }}">
                            @error('tgl_spp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- NO SPD dan Tanggal SPD --}}
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">No. SPD</label>
                        <div class="col-md-4">
                            <input class="form-control @error('no_spd') is-invalid @enderror" type="text" id="no_spd"
                                name="no_spd" required readonly value="{{ $trhspp->no_spd ?? '' }}">
                            @error('no_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="tgl_spd" class="col-md-2 col-form-label">Tanggal SPD</label>
                        <div class="col-md-4">
                            <input class="form-control @error('tgl_spd') is-invalid @enderror" type="date" id="tgl_spd"
                                name="tgl_spd" required readonly value="{{ $trhspd->tgl_spd }}">
                            @error('tgl_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- OPD/Unit dan Bulan --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">OPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control @error('kd_skpd') is-invalid @enderror" type="text" id="kd_skpd"
                                name="kd_skpd" required readonly value="{{ $trhspp->kd_skpd ?? '' }}">
                            <input class="form-control @error('kd_sub_skpd') is-invalid @enderror" type="text"
                                id="kd_sub_skpd" name="kd_sub_skpd" required hidden
                                value="{{ $trhspp->kd_sub_skpd ?? '' }}">
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <input class="form-control @error('bulan') is-invalid @enderror" type="text"
                                id="bulan"name="bulan" required hidden readonly value="{{ $trhspp->bulan ?? '' }}">
                            <input class="form-control @error('nm_bulan') is-invalid @enderror" type="text"
                                id="nm_bulan" name="nm_bulan" required readonly
                                value="{{ MSbulan($trhspp->bulan) ?? '' }}">
                            @error('bulan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nama OPD/Unit dan Keperluan --}}
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama OPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control @error('nm_skpd') is-invalid @enderror" type="text" id="nm_skpd"
                                name="nm_skpd" required readonly value="{{ $trhspp->nm_skpd ?? '' }}">
                            @error('nm_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea name="keperluan" class="form-control" id="keperluan" readonly>{{ $trhspp->keperluan ?? '' }}</textarea>
                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Beban dan Rekanan --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <input class="form-control @error('beban') is-invalid @enderror" type="text" id="beban"
                                name="beban" hidden required readonly>
                            <input class="form-control @error('nm_beban') is-invalid @enderror" type="text"
                                id="nm_beban" name="nm_beban" required readonly>
                            @error('beban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('rekanan') is-invalid @enderror"
                                value="{{ $trhspp->nmrekan ?? old('rekanan') }}" id="rekanan" name="rekanan" readonly>
                            @error('rekanan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Jenis dan Bank --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <input class="form-control @error('jenis') is-invalid @enderror" type="text"
                                id="jenis" name="jenis" required readonly hidden>
                            <input class="form-control @error('nm_jenis') is-invalid @enderror" type="text"
                                id="nm_jenis" name="nm_jenis" required readonly>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <input class="form-control @error('bank') is-invalid @enderror" type="text"
                                id="bank" name="bank" required readonly hidden
                                value="{{ $ms_bank->kode ?? '' }}">
                            <input class="form-control @error('nm_bank') is-invalid @enderror" type="text"
                                id="nm_bank" name="nm_bank" required readonly value="{{ $ms_bank->nama ?? '' }}">
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- NPWP dan Rekening --}}
                    <div class="mb-5 row">
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input class="form-control @error('npwp') is-invalid @enderror" type="text"
                                id="npwp" name="npwp" required readonly value="{{ $trhspp->npwp ?? '' }}">
                            @error('npwp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <input class="form-control @error('rekening') is-invalid @enderror" type="text"
                                id="rekening" name="rekening" required readonly value="{{ $trhspp->no_rek ?? '' }}">
                            @error('rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="bg-secondary border-top border-secondary border-2">

                    <div class="mb-3 row">
                        <label for="tgl_terima_kelengkapan_spm" class="col-md-2 col-form-label">Tanggal Terima</label>
                        <div class="col-md-4">
                            <input type="date"
                                class="form-control @error('tgl_terima_kelengkapan_spm') is-invalid @enderror"
                                id="tgl_terima_kelengkapan_spm" name="tgl_terima_kelengkapan_spm"
                                value="{{ $trhspp->tgl_terima_kelengkapan_spm ?? '' }}">
                            @error('tgl_terima_kelengkapan_spm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <label for="tgl_kembali_kelengkapan_spm" class="col-md-2 col-form-label">Tanggal Kembali</label>
                        <div class="col-md-4">
                            <input type="date"
                                class="form-control @error('tgl_kembali_kelengkapan_spm') is-invalid @enderror"
                                id="tgl_kembali_kelengkapan_spm" name="tgl_kembali_kelengkapan_spm"
                                value="{{ $trhspp->tgl_kembali_kelengkapan_spm ?? '' }}">
                            @error('tgl_kembali_kelengkapan_spm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-5 row">
                        <label for="tgl_terima_kembali_kelengkapan_spm" class="col-md-2 col-form-label">Tanggal Terima
                            Kembali</label>
                        <div class="col-md-4">
                            <input type="date"
                                class="form-control @error('tgl_terima_kembali_kelengkapan_spm') is-invalid @enderror"
                                id="tgl_terima_kembali_kelengkapan_spm" name="tgl_terima_kembali_kelengkapan_spm"
                                value="{{ $trhspp->tgl_terima_kembali_kelengkapan_spm ?? '' }}">
                            @error('tgl_terima_kembali_kelengkapan_spm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="bg-secondary border-top border-secondary border-2">

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
                            <input type="text" style="text-align: right" readonly
                                class="form-control @error('total') is-invalid @enderror" id="total" name="total">
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Kelengkapan SPM --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Kelengkapan SPM
                </div>
                <div class="card-body table-responsive">
                    <div class="mb-3 row">
                        <label for="jenis_ls" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-12">
                            <select name="jenis_ls" class="form-control select2-multiple" id="jenis_ls">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="-">Non LS</option>
                                <option value="1">Gaji Induk, Gaji Terusan, Kekurangan Gaji, Gaji Susulan</option>
                                {{-- <option value="2">Gaji Susulan</option> --}}
                                <option value="3">Tambahan Penghasilan</option>
                                <option value="4">Honorarium PNS</option>
                                <option value="5">Honorarium Tenaga Kontrak</option>
                                <option value="6">Pengadaan Barang dan Jasa/Konstruksi/Konsultansi</option>
                                <option value="7">Pengadaan Konsumsi</option>
                                <option value="8">Sewa Rumah Jabatan/Gedung untuk Kantor/Gedung Pertemuan/Tempat
                                    Pertemuan/Tempat Penginapan/Kendaraan</option>
                                <option value="9">Pengadaan Sertifikat Tanah</option>
                                <option value="10">Pengadaan Tanah</option>
                                <option value="11">Hibah Barang dan Jasa pada Pihak Ketiga</option>
                                <option value="12">LS Bantuan Sosial pada Pihak Ketiga</option>
                                <option value="13">Hibah Uang Pada Pihak Ketiga</option>
                                <option value="14">Bantuan Keuangan Pada Kabupaten/Kota</option>
                                <option value="15">Bagi Hasil Pajak dan Bukan Pajak</option>
                                {{-- <option value="16">Hibah Konstruksi pada Pihak Ketiga</option> --}}
                                <option value="17">Belanja Perjalanan Dinas</option>
                                <option value="18">Belanja Kontribusi DIKLAT/BIMTEK/WORKSHOP dan Sejenisnya</option>
                                <option value="19">Belanja Tidak Terduga (BTT)</option>
                                <option value="20">Belanja Barang dan Jasa (Non PNS)</option>
                                <option value="98">Belanja Operasional KDH/WKDH dan Pimpinan DPRD</option>
                                <option value="99">Pembiayaan pada Pihak Ketiga Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <hr class="bg-secondary border-top border-secondary border-2">

                    <div class="col-10 align-items-center mb-5">

                        <table id="table_kelengkapan_spm" class="table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th class="id text-center">id</th>
                                    <th class="uraian">Uraian</th>
                                    <th class="ceklist">Ceklist</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <!-- SIMPAN -->
                    <div>
                        <div class="mb-3 row">
                            <label for="ket_kelengkapan_spm" class="col-md-3 col-form-label"><b>Keterangan Berkas
                                    Kelengkapan
                                    SPM : <b></label>
                            <div class="col-md-12">
                                <input class="form-control @error('ket_kelengkapan_spm') is-invalid @enderror"
                                    type="text" id="ket_kelengkapan_spm" name="ket_kelengkapan_spm"
                                    value="{{ $trhspp->ket_kelengkapan_spm ?? '' }}" required>
                                @error('ket_kelengkapan_spm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <div style="float: right;">
                            @if ($trhspp->status != 2)
                                <button id="setuju" class="btn btn-success btn-md"><i
                                        class="fas fa-check mx-2"></i>Setuju</button>
                            @endif
                            @if ($trhspp->status != 3)
                                <button id="kembali" class="btn btn-danger btn-md mx-2"><i
                                        class="fas fa-eject mx-2"></i>Dikembalikan</button>
                            @endif
                            @if ($trhspp->status != 2)
                                <button id="update" class="btn btn-primary btn-md"><i
                                        class="fas fa-save mx-2"></i>Update Draft</button>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('verifikasi_spp.index') }}" class="btn btn-warning btn-md"><i
                                    class="fas fa-arrow-left mx-2"></i>Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-center" id="konfirmasi_potongan" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p style="text-align: center">Apakah Anda ingin menambahkan potongan?</p>
                    <div class="mt-2" style="text-align: center">
                        <a href="#" id="potongan_spm" class="btn btn-primary btn-md">Ya</a>
                        <a href="{{ route('spm.index') }}" class="btn btn-danger btn-md">Tidak</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.verifikasi_spp.js.edit');
@endsection
