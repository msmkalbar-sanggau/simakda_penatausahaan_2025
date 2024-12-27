@extends('template.app')
@section('title', 'Tambah Potongan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button id="input_potongan" class="btn btn-primary btn-md">Input Potongan/Pajak</button>
                    <button id="input_pajak" class="btn btn-success btn-md">Buat ID Billing</button>
                </div>

                {{-- Input Potongan --}}
                <div class="card-body" id="form_potongan">
                    @csrf
                    {{-- CATATAN --}}
                    <div class="row">
                        <label for="catatan" class="col-md-12 col-form-label" style="color:red">CATATAN!!!</label>
                    </div>
                    <div class="mb-1 row">
                        <button class="btn btn-primary btn-md col-md-1">Biru</button>
                        <label for="catatan" class="col-md-11 col-form-label" style="color:red">Input Potongan
                            Digunakan
                            Untuk Yang
                            Menggunakan ID Billing dari luar SIMAKDA (PPH/PPN)</label>
                    </div>
                    <div class="mb-3 row">
                        <button class="btn btn-success btn-md col-md-1">Hijau</button>
                        <label for="catatan" class="col-md-11 col-form-label" style="color:red">Input Pajak Digunakan Untuk
                            Membuat ID
                            BILLING</label>
                    </div>

                    {{-- No SPM --}}
                    <div class="mb-3 row">
                        <label for="no_spm_potongan" class="col-md-2 col-form-label">No SPM</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control @error('no_spm_potongan') is-invalid @enderror"
                                id="no_spm_potongan" readonly value="{{ $no_spm }}">
                            @error('no_spm_potongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="text" class="form-control @error('kd_skpd') is-invalid @enderror" id="kd_skpd"
                                readonly value="{{ $kd_skpd }}" hidden>
                        </div>
                    </div>
                    {{-- Rekening Transaksi --}}
                    <div class="mb-3 row">
                        <label for="rekening_transaksi" class="col-md-2 col-form-label">Rekening Transaksi</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('rekening_transaksi') is-invalid @enderror"
                                style="width: 100%;" id="rekening_transaksi" name="rekening_transaksi"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Rekening Transaksi">
                                    <option value="" disabled selected>Silahkan Pilih Rekening Transaksi</option>
                                    @foreach ($daftar_transaksi as $transaksi)
                                        <option value="{{ $transaksi->kd_rek6 }}" data-nama="{{ $transaksi->nm_rek6 }}">
                                            {{ $transaksi->kd_rek6 }} | {{ $transaksi->nm_rek6 }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('rekening_transaksi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('nm_rek_trans') is-invalid @enderror"
                                id="nm_rek_trans" name="nm_rek_trans" readonly>
                        </div>
                    </div>
                    {{-- Rekening Potongan --}}
                    <div class="mb-3 row">
                        <label for="rekening_potongan" class="col-md-2 col-form-label">Rekening Potongan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('rekening_potongan') is-invalid @enderror"
                                style="width: 100%;" id="rekening_potongan" name="rekening_potongan"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Rekening Potongan">
                                    <option value="" disabled selected>Silahkan Pilih Rekening Potongan</option>
                                    @foreach ($daftar_potongan as $potongan)
                                        <option value="{{ $potongan->kd_rek6 }}" data-nama="{{ $potongan->nm_rek6 }}"
                                            data-map_pot="{{ $potongan->map_pot }}"
                                            data-kd_map="{{ kd_map($potongan->kd_rek6) }}">
                                            {{ $potongan->kd_rek6 }} | {{ $potongan->nm_rek6 }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('rekening_potongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('nm_rek_pot') is-invalid @enderror"
                                id="nm_rek_pot" name="nm_rek_pot" readonly>
                            <input type="text" class="form-control @error('map_pot') is-invalid @enderror" id="map_pot"
                                name="map_pot" readonly hidden>
                            <input type="text" class="form-control @error('kd_map') is-invalid @enderror" id="kd_map"
                                name="kd_map" readonly hidden>
                        </div>
                    </div>
                    {{-- Jenis Potongan --}}
                    <div class="mb-3 row">
                        <label for="jns_potongan" class="col-md-2 col-form-label">Jenis Potongan</label>
                        <div class="col-md-10">
                            <select class="form-control select2-multiple" style="width: 100%" id="jns_pot1" name="jns_pot1"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Jenis Potongan">
                                    <option value="" disabled selected>...Pilih Jenis Potongan... </option>
                                    <option value="1">Memotong Langsung SP2D</option>
                                    <option value="0">Sebagai Infromasi ( Tidak Memotong Pencairan SP2D)</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    {{-- ID BILLING --}}
                    <div class="mb-3 row">
                        <label for="id_billing" class="col-md-2 col-form-label">ID Billing</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control @error('id_billing') is-invalid @enderror"
                                id="id_billing" name="id_billing">
                            @error('id_billing')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nilai --}}
                    <div class="mb-3 row">
                        <label for="nilai_pot" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control @error('nilai_pot') is-invalid @enderror"
                                id="nilai_pot" name="nilai_pot" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                                data-type="currency" style="text-align: right">
                            @error('nilai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div style="text-align:center">
                        <button id="simpan_potongan" {{ $spm->is_verified > 0 ? 'hidden' : '' }}
                            class="btn btn-primary btn-md">Tambah Potongan</button>
                        <a href="{{ route('spm.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>

                {{-- Input Pajak --}}
                <div class="card-body" id="form_pajak">
                    @csrf
                    {{-- No SPM dan NPWP --}}
                    <div class="mb-3 row">
                        <label for="no_spm_pajak" class="col-md-2 col-form-label">No. SPM</label>
                        <div class="col-md-4">
                            <input class="form-control @error('no_spm_pajak') is-invalid @enderror" type="text"
                                id="no_spm_pajak" name="no_spm_pajak" required readonly value="{{ $no_spm }}">
                            @error('no_spm_pajak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control @error('npwp') is-invalid @enderror"
                                    id="npwp" name="npwp" readonly>
                                <span class="input-group-btn">
                                    <button type="button" id="cek_npwp" class="btn btn-primary">Cek NPWP</button>
                                </span>
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- Nama Wajib Pajak dan Alamat Wajib Pajak --}}
                    <div class="mb-3 row">
                        <label for="nama_wajib_pajak" class="col-md-2 col-form-label">Nama Wajib Pajak</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="nama_wajib_pajak">
                                @error('nama_wajib_pajak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <label for="alamat_wajib_pajak" class="col-md-2 col-form-label">Alamat Wajib Pajak</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <textarea type="text" class="form-control" id="alamat_wajib_pajak"></textarea>
                                @error('alamat_wajib_pajak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- Kode Map dan Nama Map --}}
                    <div class="mb-3 row">
                        <label for="kode_map" class="col-md-2 col-form-label">Kode Map</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('kode_map') is-invalid @enderror"
                                style="width: 100%;" id="kode_map" name="kode_map" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Kode Map">
                                    <option value="" disabled selected>Silahkan Pilih Kode Map</option>
                                    @foreach ($daftar_kode_akun as $kode_akun)
                                        <option value="{{ $kode_akun->kd_map }}" data-nama="{{ $kode_akun->nm_map }}">
                                            {{ $kode_akun->kd_map }} | {{ $kode_akun->nm_map }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('kode_map')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nama_map" class="col-md-2 col-form-label">Nama Map</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nama_map') is-invalid @enderror"
                                id="nama_map" name="nama_map" readonly>
                            @error('nama_map')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Kode Setor dan Nama Setor --}}
                    <div class="mb-3 row">
                        <label for="kode_setor" class="col-md-2 col-form-label">Kode Setor</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('kode_setor') is-invalid @enderror"
                                style="width: 100%;" id="kode_setor" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Kode Setor">
                                    <option value="" disabled selected>Silahkan Pilih Kode Setor</option>
                                </optgroup>
                            </select>
                            @error('kode_setor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nama_setor" class="col-md-2 col-form-label">Nama Setor</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nama_setor') is-invalid @enderror"
                                id="nama_setor" name="nama_setor" readonly>
                            @error('nama_setor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Kota dan NIK --}}
                    <div class="mb-3 row">
                        <label for="kota" class="col-md-2 col-form-label">Kota</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="kota">
                                @error('kota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <label for="nik" class="col-md-2 col-form-label">NIK</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="nik" maxlength="16">
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- Masa Pajak dan Tahun Pajak --}}
                    <div class="mb-3 row">
                        <label for="masa_pajak" class="col-md-2 col-form-label">Masa Pajak</label>
                        <div class="col-md-2">
                            <select class="form-control select2-multiple @error('masa_pajak_awal') is-invalid @enderror"
                                style=" width: 100%;" id="masa_pajak_awal" data-placeholder="Silahkan Pilih">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="01">1</option>
                                <option value="02">2</option>
                                <option value="03">3</option>
                                <option value="04">4</option>
                                <option value="05">5</option>
                                <option value="06">6</option>
                                <option value="07">7</option>
                                <option value="08">8</option>
                                <option value="09">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                            @error('masa_pajak_awal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <select class="form-control select2-multiple @error('masa_pajak_akhir') is-invalid @enderror"
                                style=" width: 100%;" id="masa_pajak_akhir" data-placeholder="Silahkan Pilih">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="01">1</option>
                                <option value="02">2</option>
                                <option value="03">3</option>
                                <option value="04">4</option>
                                <option value="05">5</option>
                                <option value="06">6</option>
                                <option value="07">7</option>
                                <option value="08">8</option>
                                <option value="09">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                            @error('masa_pajak_akhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="tahun_pajak" class="col-md-2 col-form-label">Tahun Pajak</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('tahun_pajak') is-invalid @enderror"
                                style=" width: 100%;" id="tahun_pajak" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Masa Pajak">
                                    <option value="" selected disabled>Silahkan Pilih</option>
                                    <option value="2011">2011</option>
                                    <option value="2012">2012</option>
                                    <option value="2013">2013</option>
                                    <option value="2014">2014</option>
                                    <option value="2015">2015</option>
                                    <option value="2016">2016</option>
                                    <option value="2017">2017</option>
                                    <option value="2018">2018</option>
                                    <option value="2019">2019</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023" selected>2023</option>
                                </optgroup>
                            </select>
                            @error('tahun_pajak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- NOP (Nomor Objek Pajak) dan Nomor SK --}}
                    <div class="mb-3 row">
                        <label for="nop" class="col-md-2 col-form-label">NOP (Nomor Objek Pajak)</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="nop">
                                @error('nop')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <label for="no_sk" class="col-md-2 col-form-label">Nomor SK</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="no_sk">
                                @error('no_sk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- NPWP Penyetor dan Nomor Faktur Pajak --}}
                    <div class="mb-3 row">
                        <label for="npwp_setor" class="col-md-2 col-form-label">NPWP Penyetor</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="npwp_setor" maxlength="15">
                                @error('npwp_setor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <label for="no_faktur" class="col-md-2 col-form-label">Nomor Faktur Pajak</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="no_faktur">
                                @error('no_faktur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- NPWP Rekanan dan NIK Rekanan --}}
                    <div class="mb-3 row">
                        <label for="npwp_rekanan" class="col-md-2 col-form-label">NPWP Rekanan</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="npwp_rekanan" maxlength="15">
                                @error('npwp_rekanan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <label for="nik_rekanan" class="col-md-2 col-form-label">NIK Rekanan</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="nik_rekanan" maxlength="16">
                                @error('nik_rekanan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- Kode Akun Transaksi dan Nama Akun Transaksi --}}
                    <div class="mb-3 row">
                        <label for="kode_akun_transaksi" class="col-md-2 col-form-label">Kode Akun Transaksi</label>
                        <div class="col-md-4">
                            <select
                                class="form-control select2-multiple @error('kode_akun_transaksi') is-invalid @enderror"
                                style="width: 100%;" id="kode_akun_transaksi" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Kode Akun Transaksi">
                                    <option value="" disabled selected>Silahkan Pilih Kode Akun Transaksi</option>
                                    @foreach ($daftar_transaksi as $transaksi)
                                        <option value="{{ $transaksi->kd_rek6 }}" data-nama="{{ $transaksi->nm_rek6 }}">
                                            {{ $transaksi->kd_rek6 }} | {{ $transaksi->nm_rek6 }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('kode_akun_transaksi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nama_akun_transaksi" class="col-md-2 col-form-label">Nama Akun Transaksi</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nama_akun_transaksi') is-invalid @enderror"
                                id="nama_akun_transaksi" name="nama_akun_transaksi" readonly>
                            @error('nama_akun_transaksi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Kode Akun Potongan dan Nama Akun Potongan --}}
                    <div class="mb-3 row">
                        <label for="kode_akun_potongan" class="col-md-2 col-form-label">Kode Akun Potongan</label>
                        <div class="col-md-4">
                            <select
                                class="form-control select2-multiple @error('kode_akun_potongan') is-invalid @enderror"
                                style="width: 100%;" id="kode_akun_potongan" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Kode Akun Potongan">
                                    <option value="" disabled selected>Silahkan Pilih Kode Akun Potongan</option>
                                </optgroup>
                            </select>
                            @error('kode_akun_potongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nama_akun_potongan" class="col-md-2 col-form-label">Nama Akun Potongan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nama_akun_potongan') is-invalid @enderror"
                                id="nama_akun_potongan" name="nama_akun_potongan" readonly>
                            @error('nama_akun_potongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Jumlah Bayar --}}
                    <div class="mb-3 row">
                        <label for="jumlah_bayar" class="col-md-2 col-form-label">Jumlah Bayar</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('jumlah_bayar') is-invalid @enderror"
                                id="jumlah_bayar" name="jumlah_bayar" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                                data-type="currency" maxlength="15">
                            @error('jumlah_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="jns_potongan" class="col-md-2 col-form-label">Jenis Potongan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="jns_pot"
                                name="jns_pot" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Jenis Potongan">
                                    <option value="" disabled selected>...Pilih Jenis Potongan... </option>
                                    <option value="1">Memotong Langsung SP2D</option>
                                    <option value="0">Sebagai Infromasi ( Tidak Memotong Pencairan SP2D)</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <p style="font-weight:bold">Perhatian!!!</p>
                        <p>1. Sebelum klik tombol create id billing, Pastikan semua inputan sudah benar.</p>
                        <p>2. Ketika tombol create idbilling diklik, maka idbilling otomatis dibuat dan pajak otomatis masuk
                            di potongan SPM.</p>
                        <p>3. Setelah selesai create id billing dan pajak sudah masuk di list, silahkan klik kembali dan
                            simpan SPM.</p>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="create_billing" class="btn btn-primary btn-md">Create Id Billing</button>
                        <a href="{{ route('spm.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- List Potongan --}}
        <div class="col-12">
            <div class="card" id="list_potongan">
                <div class="card-header">
                    List Potongan
                </div>
                <div class="card-body">
                    <table class="table" id="tabel_pot" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Rek. Trans</th>
                                <th>Rekening</th>
                                <th>Map Pot</th>
                                <th>Nama Rekening</th>
                                <th>ID Billing</th>
                                <th style="text-align: center">Nilai</th>
                                <th>Memotong SP2D</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($rincian_spm as $spm)
                                <tr>
                                    <td>{{ $spm->kd_trans }}</td>
                                    <td>{{ $spm->kd_rek6 }}</td>
                                    <td>{{ $spm->map_pot }}</td>
                                    <td>{{ $spm->nm_rek6 }}</td>
                                    <td>{{ $spm->idBilling }}</td>
                                    <td>{{ rupiah($spm->nilai) }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            onclick="hapusPot('{{ $spm->kd_trans }}','{{ $spm->kd_rek6 }}','{{ $spm->map_pot }}','{{ $spm->nm_rek6 }}','{{ $spm->idBilling }}','{{ $spm->nilai }}','{{ $spm->no_spm }}')"
                                            class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                        <button type="button" onclick="cetakPot('{{ $spm->idBilling }}')"
                                            class="btn btn-success btn-sm"><i class="uil-print"></i></button>
                                    </td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly
                                class="form-control @error('total_pot') is-invalid @enderror"
                                value="{{ rupiah($total_pajak->nilai) }}" id="total_pot" name="total_pot">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rincian Pajak --}}
        <div class="col-12">
            <div class="card" id="rincian_pajak">
                <div class="card-header">
                    Rincian Pajak
                </div>
                <div class="card-body">
                    <table class="table" id="tabel_pajak" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No SPM</th>
                                <th>Kode Potongan</th>
                                <th>Nama Rekening</th>
                                <th>ID Billing</th>
                                <th style="text-align: center">Nilai</th>
                                <th>Memotong SP2D</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($rincian_spm as $spm)
                                <tr>
                                    <td>{{ $spm->no_spm }}</td>
                                    <td>{{ $spm->kd_rek6 }}</td>
                                    <td>{{ $spm->nm_rek6 }}</td>
                                    <td>{{ $spm->idBilling }}</td>
                                    <td>{{ rupiah($spm->nilai) }}</td>
                                    <td>
                                        <a href="javascript:void(0);"
                                            onclick="hapusPajak('{{ $spm->no_spm }}','{{ $spm->kd_rek6 }}','{{ $spm->nm_rek6 }}','{{ $spm->idBilling }}','{{ $spm->nilai }}')"
                                            class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                        <button type="button"
                                            onclick="cetakPajak('{{ $spm->no_spm }}', '{{ $spm->kd_rek6 }}', '{{ $spm->nm_rek6 }}','{{ $spm->nilai }}')"
                                            class="btn btn-success btn-sm"><i class="uil-print"></i></button>
                                    </td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly
                                class="form-control @error('total_pajak') is-invalid @enderror" id="total_pajak"
                                name="total_pajak" value="{{ rupiah($total_pajak->nilai) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="modal_cek_npwp" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rincian Penagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- No SPM dan NPWP -->
                    <div class="mb-3 row">
                        <label for="no_spm_cek" class="col-md-2 col-form-label">No SPM</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('no_spm_cek') is-invalid @enderror"
                                id="no_spm_cek" readonly name="no_spm_cek" value="{{ $no_spm }}">
                            @error('no_spm_cek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="npwp_cek" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('npwp_cek') is-invalid @enderror"
                                id="npwp_cek" name="npwp_cek" value="{{ $spm->npwp }}">
                            @error('npwp_cek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Kode Map dan Nama Map --}}
                    <div class="mb-3 row">
                        <label for="kode_map_cek" class="col-md-2 col-form-label">Kode Map</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('kode_map_cek') is-invalid @enderror"
                                style="width: 100%;" id="kode_map_cek" name="kode_map_cek"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Kode Map">
                                    <option value="" disabled selected>Silahkan Pilih Kode Map</option>
                                    @foreach ($daftar_kode_akun as $kode_akun)
                                        <option value="{{ $kode_akun->kd_map }}" data-nama="{{ $kode_akun->nm_map }}">
                                            {{ $kode_akun->kd_map }} | {{ $kode_akun->nm_map }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('kode_map_cek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nama_map_cek" class="col-md-2 col-form-label">Nama Map</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nama_map_cek') is-invalid @enderror"
                                id="nama_map_cek" name="nama_map_cek" readonly>
                            @error('nama_map_cek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Kode Setor dan Nama Setor --}}
                    <div class="mb-3 row">
                        <label for="kode_setor_cek" class="col-md-2 col-form-label">Kode Setor</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('kode_setor_cek') is-invalid @enderror"
                                style="width: 100%;" id="kode_setor_cek" name="kode_setor_cek"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Kode Map">
                                    <option value="" disabled selected>Silahkan Pilih Kode Setor</option>
                                </optgroup>
                            </select>
                            @error('kode_setor_cek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nama_setor_cek" class="col-md-2 col-form-label">Nama Setor</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nama_setor_cek') is-invalid @enderror"
                                id="nama_setor_cek" name="nama_setor_cek" readonly>
                            @error('nama_setor_cek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="cek_npwp_bukti" class="btn btn-md btn-primary">Cek NPWP</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Kembali</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--  -->
                    <div class="mb-3 row">
                        <label for="cetak_billing" class="col-md-6 col-form-label">Cetak Bukti Create Id Billing</label>
                        <div class="col-md-6">
                            <button type="button" data-cetak="ReportCreateBilling"
                                class="btn btn-md btn-success cetak_billing" style="float:center">Download</button>
                            <input type="text" hidden id="id_billing_cetak">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="cetak_pembayaran" class="col-md-6 col-form-label">Cetak Bukti pembayaran Pajak</label>
                        <div class="col-md-6">
                            <button type="button" data-cetak="ReportBPN" class="btn btn-md btn-success cetak_billing"
                                style="float:center">Download</button>
                        </div>
                    </div>
                    <hr style="border: 1px solid black">
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal"
                                style="float: right">Kembali</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    @include('penatausahaan.pengeluaran.spm.js.potongan')
@endsection
