@extends('template.app')
@section('title', 'Ubah SPP LS | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Dengan Penagihan --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="form-check form-switch form-switch-lg">
                        <input type="checkbox" class="form-check-input" id="dengan_penagihan">
                        <label class="form-check-label" for="dengan_penagihan">Dengan Penagihan</label>
                    </div>
                </div>
                <div id="card_penagihan" class="card-body">
                    <div class="mb-3 row">
                        <table id="rincian_penagihan" class="table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No Penagihan</th>
                                    <th>Tanggal Penagihan</th>
                                    <th>Nilai</th>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text"
                                            class="form-control @error('no_penagihan') is-invalid @enderror"
                                            id="no_penagihan" readonly name="no_penagihan" value="{{ $sppls->no_tagih }}">
                                    </td>
                                    <td>
                                        <input type="date"
                                            class="form-control @error('tgl_penagihan') is-invalid @enderror"
                                            id="tgl_penagihan" readonly name="tgl_penagihan"
                                            value="{{ $sppls->tgl_tagih }}">
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control @error('nilai_penagihan') is-invalid @enderror"
                                            id="nilai_penagihan" readonly name="nilai_penagihan"
                                            value="{{ $sppls->nilai }}">
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data Penagihan
                </div>
                <div class="alert alert-warning alert-block {{ $sppls->status == 1 ? '' : 'hidden' }}">
                    <b style="font-size:16px">Sudah di Buat SPM!!</b>
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No Tersimpan dan Tanggal SPP --}}
                    <div class="mb-3 row">
                        <label for="no_tersimpan" class="col-md-2 col-form-label">No. Tersimpan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_tersimpan" name="no_tersimpan" required
                                readonly value="{{ $sppls->no_spp }}">
                            <input class="form-control" type="text" id="no_urut" name="no_urut" required readonly
                                hidden value="{{ $sppls->urut }}">
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" value="{{ $sppls->tgl_spp }}" id="tgl_spp"
                                name="tgl_spp">
                            <input type="date" class="form-control" id="tgl_spp_lalu" name="tgl_spp_lalu" hidden
                                value="{{ $data_tgl->tgl_spp }}">
                        </div>
                    </div>
                    {{-- No SPP dan Bulan --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No. SPP</label>
                        <div class="col-md-4">
                            <div class="md-form input-group mt-md-0 mb-0">
                                <input type="text" class="form-control" id="no_spp" name="no_spp" readonly
                                    value="{{ $sppls->no_spp }}">
                                <span class="input-group-btn">
                                    <button type="button" id="cari_nospp" disabled class="btn btn-primary"><i
                                            class="uil-refresh"></i></button>
                                </span>
                            </div>
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('bulan') is-invalid @enderror"
                                style="width: 100%" id="bulan" name="bulan" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Bulan">
                                    <option value="" disabled selected>...Pilih Kebutuhan Bulan... </option>
                                    <option value="1" {{ $sppls->bulan == '1' ? 'selected' : '' }}>Januari</option>
                                    <option value="2" {{ $sppls->bulan == '2' ? 'selected' : '' }}>Februari</option>
                                    <option value="3" {{ $sppls->bulan == '3' ? 'selected' : '' }}>Maret</option>
                                    <option value="4" {{ $sppls->bulan == '4' ? 'selected' : '' }}>April</option>
                                    <option value="5" {{ $sppls->bulan == '5' ? 'selected' : '' }}>Mei</option>
                                    <option value="6" {{ $sppls->bulan == '6' ? 'selected' : '' }}>Juni</option>
                                    <option value="7" {{ $sppls->bulan == '7' ? 'selected' : '' }}>Juli</option>
                                    <option value="8" {{ $sppls->bulan == '8' ? 'selected' : '' }}>Agustus</option>
                                    <option value="9" {{ $sppls->bulan == '9' ? 'selected' : '' }}>September</option>
                                    <option value="10" {{ $sppls->bulan == '10' ? 'selected' : '' }}>Oktober</option>
                                    <option value="11" {{ $sppls->bulan == '11' ? 'selected' : '' }}>November</option>
                                    <option value="12" {{ $sppls->bulan == '12' ? 'selected' : '' }}>Desember</option>
                                </optgroup>
                            </select>
                            @error('bulan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- KD SKPD dan Keperluan --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control @error('kd_skpd') is-invalid @enderror" type="text"
                                id="kd_skpd" name="kd_skpd" required readonly value="{{ $sppls->kd_skpd }}">
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea type="text" class="form-control @error('keperluan') is-invalid @enderror" id="keperluan"
                                name="keperluan">{{ $sppls->keperluan }}</textarea>
                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nama SKPD dan Bank --}}
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control @error('nm_skpd') is-invalid @enderror" type="text"
                                id="nm_skpd" name="nm_skpd" required readonly value="{{ $sppls->nm_skpd }}">
                            @error('nm_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('bank') is-invalid @enderror"
                                style="width: 100%;" id="bank" name="bank" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Bank">
                                    <option value="" disabled selected>Silahkan Pilih Bank</option>
                                    @foreach ($daftar_bank as $bank)
                                        <option value="{{ $bank->kode }}" data-nama="{{ $bank->nama }}"
                                            {{ $sppls->bank == $bank->kode ? 'selected' : '' }}>
                                            {{ $bank->kode }} | {{ $bank->nama }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Beban dan Penerima --}}
                    <div class="mb-3 row">
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <select class="form-control @error('beban') is-invalid @enderror" style="width: 100%"
                                id="beban" name="beban">
                                <optgroup label="Daftar Beban">
                                    <option value="" disabled selected>...Pilih Beban... </option>
                                    <option value="4" {{ $sppls->jns_spp == '4' ? 'selected' : '' }}>LS GAJI</option>
                                    <option value="6" {{ $sppls->jns_spp == '6' ? 'selected' : '' }}>LS Barang Jasa
                                    </option>
                                    <option value="5" {{ $sppls->jns_spp == '5' ? 'selected' : '' }}>LS Piihak Ketiga
                                        Lainnya</option>
                                </optgroup>
                            </select>
                            @error('beban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nm_penerima" class="col-md-2 col-form-label">Nama Penerima</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('nm_penerima') is-invalid @enderror"
                                style="width: 100%;" id="nm_penerima" name="nm_penerima"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Penerima">
                                    <option value="" disabled selected>Silahkan Pilih Penerima</option>
                                    @foreach ($daftar_penerima as $penerima)
                                        <option value="{{ $penerima->nm_rekening }}" data-npwp="{{ $penerima->npwp }}"
                                            data-rekening="{{ $penerima->rekening }}"
                                            data-nmrekan="{{ $penerima->nmrekan }}"
                                            data-pimpinan="{{ $penerima->pimpinan }}"
                                            data-alamat="{{ $penerima->alamat }}"
                                            {{ $sppls->penerima == $penerima->nm_rekening ? 'selected' : '' }}>
                                            {{ $penerima->nm_rekening }} | {{ $penerima->rekening }} |
                                            {{ $penerima->npwp }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('nm_penerima')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Jenis dan Rekening --}}
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('jenis') is-invalid @enderror"
                                style=" width: 100%;" id="jenis" name="jenis" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Jenis">
                                </optgroup>
                            </select>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('rekening') is-invalid @enderror"
                                value="{{ $sppls->no_rek }}" id="rekening" name="rekening" readonly>
                            @error('rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nomor SPD dan NPWP --}}
                    <div class="mb-3 row">
                        <label for="nomor_spd" class="col-md-2 col-form-label">Nomor SPD</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('nomor_spd') is-invalid @enderror"
                                style=" width: 100%;" id="nomor_spd" name="nomor_spd" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Nomor SPD">
                                </optgroup>
                            </select>
                            @error('nomor_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('npwp') is-invalid @enderror"
                                value="{{ $sppls->npwp }}" id="npwp" name="npwp" readonly>
                            @error('npwp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Tanggal SPD dan Rekanan --}}
                    <div class="mb-3 row">
                        <label for="tgl_spd" class="col-md-2 col-form-label">Tanggal SPD</label>
                        <div class="col-md-4">
                            <input class="form-control @error('tgl_spd') is-invalid @enderror" type="date"
                                id="tgl_spd" name="tgl_spd" required readonly>
                            @error('tgl_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                        {{-- <div class="col-md-4">
                            <select class="form-control select2-multiple @error('rekanan') is-invalid @enderror"
                                style="width: 100%;" id="rekanan" name="rekanan" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Rekanan">
                                    <option value="" disabled selected>Silahkan Pilih Rekanan</option>
                                    @foreach ($daftar_rekanan as $rekanan)
                                        <option value="{{ $rekanan->nmrekan }}" data-pimpinan="{{ $rekanan->pimpinan }}"
                                            data-alamat="{{ $rekanan->alamat }}"
                                            {{ $sppls->nmrekan == $rekanan->nmrekan ? 'selected' : '' }}>
                                            {{ $rekanan->nmrekan }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('rekanan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="rekanan" name="rekanan" readonly
                                value="{{ $sppls->nmrekan }}">
                        </div>
                    </div>
                    {{-- Kode Sub Kegiatan dan Pimpinan --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Kode Sub Kegiatan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('kd_sub_kegiatan') is-invalid @enderror"
                                style=" width: 100%;" id="kd_sub_kegiatan" name="kd_sub_kegiatan"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Sub Kegiatan">
                                </optgroup>
                            </select>
                            <input type="hidden" name="kd_program" id="kd_program">
                            <input type="hidden" name="nm_program" id="nm_program">
                            <input type="hidden" name="bidang" id="bidang">
                            @error('kd_sub_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="pimpinan" class="col-md-2 col-form-label">Pimpinan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('pimpinan') is-invalid @enderror"
                                value="{{ $sppls->pimpinan }}" id="pimpinan" name="pimpinan" readonly>
                            @error('pimpinan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nama Sub Kegiatan dan Alamat --}}
                    <div class="mb-3 row">
                        <label for="nm_sub_kegiatan" class="col-md-2 col-form-label">Nama Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control @error('nm_sub_kegiatan') is-invalid @enderror" type="text"
                                id="nm_sub_kegiatan" name="nm_sub_kegiatan" required readonly
                                value="{{ $sppls->nm_sub_kegiatan }}">
                            @error('nm_sub_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="alamat" class="col-md-2 col-form-label">Alamat Perusahaan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('alamat') is-invalid @enderror"
                                value="{{ $sppls->alamat }}" id="alamat" name="alamat" readonly>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Tanggal Mulai dan Tanggal Akhir --}}
                    <div class="mb-3 row">
                        <label for="tgl_awal" class="col-md-2 col-form-label">Tanggal Mulai</label>
                        <div class="col-md-4">
                            <input class="form-control @error('tgl_awal') is-invalid @enderror" type="date"
                                id="tgl_awal" name="tgl_awal" required value="{{ $sppls->tgl_mulai }}">
                            @error('tgl_awal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="tgl_akhir" class="col-md-2 col-form-label">Tanggal Akhir</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control @error('tgl_akhir') is-invalid @enderror"
                                value="{{ old('tgl_akhir') }}" id="tgl_akhir" name="tgl_akhir"
                                value="{{ $sppls->tgl_akhir }}">
                            @error('tgl_akhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Lanjut dan Nomor Kontrak --}}
                    <div class="mb-3 row">
                        {{--  <label for="lanjut" class="col-md-2 col-form-label">Lanjut</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('lanjut') is-invalid @enderror"
                                style="width: 100%" id="lanjut" name="lanjut" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Pilihan">
                                    <option value="" disabled selected>...Pilih... </option>
                                    <option value="1" {{ $sppls->lanjut == '1' ? 'selected' : '' }}>YA</option>
                                    <option value="2" {{ $sppls->lanjut == '2' ? 'selected' : '' }}>TIDAK</option>
                                </optgroup>
                            </select>
                            @error('lanjut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>  --}}
                        <label for="no_kontrak" class="col-md-2 col-form-label">Nomor Kontrak</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control @error('no_kontrak') is-invalid @enderror"
                                value="{{ $sppls->kontrak }}" id="no_kontrak" name="no_kontrak">
                            @error('no_kontrak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        @if ($sppls->status == 1)
                        @else
                            <button id="simpan_penagihan" class="btn btn-primary btn-md">Simpan</button>
                        @endif
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
                    <button type="button" style="float: right" id="tambah_rincian" class="btn btn-primary btn-sm"
                        disabled>Tambah Rekening</button>
                </div>
                <div class="card-body table-responsive">
                    <table id="rincian_sppls" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Sub Kegiatan</th>
                                <th>Rekening</th>
                                <th>Nama Rekening</th>
                                <th>Nilai</th>
                                <th>Kode Sumber</th> {{-- hidden --}}
                                <th>Sumber</th> {{-- hidden --}}
                                <th>Volume</th>
                                <th>Satuan</th>
                                {{-- <th>Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_spp as $data)
                                <tr>
                                    <td>{{ $data->kd_sub_kegiatan }}</td>
                                    <td>{{ $data->kd_rek6 }}</td>
                                    <td>{{ $data->nm_rek6 }}</td>
                                    <td>{{ nilai($data->nilai) }}</td>
                                    <td>{{ $data->sumber }}</td>
                                    <td>{{ $data->nm_sumber_dana1 }}</td>
                                    <td>{{ $data->volume }}</td>
                                    <td>{{ $data->satuan }}</td>
                                    {{-- <td>
                                        <a href="javascript:void(0);"
                                            onclick="deleteData('{{ $data->kd_sub_kegiatan }}','{{ $data->kd_rek6 }}','{{ $data->nm_rek6 }}','{{ $data->sumber }}','{{ $data->nilai }}')"
                                            class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly
                                class="form-control @error('total') is-invalid @enderror" id="total" name="total"
                                value="{{ nilai($sppls->nilai) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tambah_rincianspp" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Rincian Penagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- OPD/UNIT -->
                    <div class="mb-3 row">
                        <label for="opd_unit" class="col-md-2 col-form-label">OPD/Unit</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('opd_unit') is-invalid @enderror"
                                id="opd_unit" readonly name="opd_unit" value="{{ $sppls->kd_skpd }}">
                            @error('opd_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nm_opd_unit') is-invalid @enderror"
                                id="nm_opd_unit" readonly name="nm_opd_unit" value="{{ $sppls->nm_skpd }}">
                            @error('nm_opd_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- SUB KEGIATAN -->
                    <div class="mb-3 row">
                        <label for="sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('sub_kegiatan') is-invalid @enderror"
                                id="sub_kegiatan" readonly name="nm_sub_kegiatan" value="{{ $sppls->kd_sub_kegiatan }}">
                            @error('sub_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nmsub_kegiatan') is-invalid @enderror"
                                id="nmsub_kegiatan" readonly name="nmsub_kegiatan"
                                value="{{ $sppls->nm_sub_kegiatan }}">
                            @error('nmsub_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- REKENING -->
                    <div class="mb-3 row">
                        <label for="kode_rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple @error('kode_rekening') is-invalid @enderror"
                                style=" width: 100%;" id="kode_rekening" name="kode_rekening"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Rekening">
                                </optgroup>
                            </select>
                            @error('kode_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nm_rekening') is-invalid @enderror"
                                value="{{ old('nm_rekening') }}" id="nm_rekening" readonly name="nm_rekening">
                            @error('nm_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- SUMBER DANA -->
                    <div class="mb-3 row">
                        <label for="sumber_dana" class="col-md-2 col-form-label">Sumber</label>
                        <div class="col-md-6">
                            <select class="form-control select2-multiple @error('sumber_dana') is-invalid @enderror"
                                style=" width: 100%;" id="sumber_dana" name="sumber_dana"
                                data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Sumber Dana">
                                </optgroup>
                            </select>
                            @error('sumber_dana')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nm_sumber') is-invalid @enderror"
                                value="{{ old('nm_sumber') }}" id="nm_sumber" readonly name="nm_sumber">
                            @error('nm_sumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- TOTAL SPD -->
                    <div class="mb-3 row">
                        <label for="total_spd" class="col-md-2 col-form-label">Total SPD</label>
                        <div class="col-md-2">
                            <input type="text" readonly class="form-control @error('total_spd') is-invalid @enderror"
                                name="total_spd" id="total_spd">
                            @error('total_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="realisasi_spd" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly
                                class="form-control @error('realisasi_spd') is-invalid @enderror" name="realisasi_spd"
                                id="realisasi_spd">
                            @error('realisasi_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="sisa_spd" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control @error('sisa_spd') is-invalid @enderror"
                                name="sisa_spd" id="sisa_spd">
                            @error('sisa_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- ANGGARAN KAS -->
                    <div class="mb-3 row">
                        <label for="total_angkas" class="col-md-2 col-form-label">Total Anggaran Kas</label>
                        <div class="col-md-2">
                            <input type="text" readonly
                                class="form-control @error('total_angkas') is-invalid @enderror" name="total_angkas"
                                id="total_angkas">
                            @error('total_angkas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="realisasi_angkas" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly
                                class="form-control @error('realisasi_angkas') is-invalid @enderror"
                                name="realisasi_angkas" id="realisasi_angkas">
                            @error('realisasi_angkas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="sisa_angkas" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly
                                class="form-control @error('sisa_angkas') is-invalid @enderror" name="sisa_angkas"
                                id="sisa_angkas">
                            @error('sisa_angkas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- ANGGARAN PENYUSUNAN -->
                    <div class="mb-3 row">
                        <label for="total_penyusunan" class="col-md-2 col-form-label">Anggaran Penyusunan</label>
                        <div class="col-md-2">
                            <input type="text" readonly
                                class="form-control @error('total_penyusunan') is-invalid @enderror"
                                name="total_penyusunan" id="total_penyusunan">
                            @error('total_penyusunan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="realisasi_penyusunan" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly
                                class="form-control @error('realisasi_penyusunan') is-invalid @enderror"
                                name="realisasi_penyusunan" id="realisasi_penyusunan">
                            @error('realisasi_penyusunan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="sisa_penyusunan" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly
                                class="form-control @error('sisa_penyusunan') is-invalid @enderror"
                                name="sisa_penyusunan" id="sisa_penyusunan">
                            @error('sisa_penyusunan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- NILAI SUMBER DANA -->
                    <div class="mb-3 row">
                        <label for="total_sumber" class="col-md-2 col-form-label">Sumber Dana Penyusunan</label>
                        <div class="col-md-2">
                            <input type="text" readonly
                                class="form-control @error('total_sumber') is-invalid @enderror" name="total_sumber"
                                id="total_sumber">
                            @error('total_sumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="realisasi_sumber" class="col-md-1 col-form-label">Realisasi</label>
                        <div class="col-md-3">
                            <input type="text" readonly
                                class="form-control @error('realisasi_sumber') is-invalid @enderror"
                                name="realisasi_sumber" id="realisasi_sumber">
                            @error('realisasi_sumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="sisa_sumber" class="col-md-1 col-form-label">Sisa</label>
                        <div class="col-md-3">
                            <input type="text" readonly
                                class="form-control @error('sisa_sumber') is-invalid @enderror" name="sisa_sumber"
                                id="sisa_sumber">
                            @error('sisa_sumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Status Anggaran -->
                    <div class="mb-3 row">
                        <label for="status_anggaran" class="col-md-2 col-form-label">Status Anggaran</label>
                        <div class="col-md-10">
                            <input type="text" readonly
                                class="form-control @error('status_anggaran') is-invalid @enderror"
                                name="status_anggaran" id="status_anggaran">
                            @error('status_anggaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Status Angkas -->
                    <div class="mb-3 row">
                        <label for="status_angkas" class="col-md-2 col-form-label">Status Anggaran Kas</label>
                        <div class="col-md-10">
                            <input type="text" readonly
                                class="form-control @error('status_angkas') is-invalid @enderror" name="status_angkas"
                                id="status_angkas">
                            @error('status_angkas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Volume Output --}}
                    <div class="mb-3 row">
                        <label for="volume_output" class="col-md-2 col-form-label">Volume Output</label>
                        <div class="col-md-10">
                            <input type="text" disabled
                                class="form-control @error('volume_output') is-invalid @enderror" name="volume_output"
                                id="volume_output">
                            @error('volume_output')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Satuan Output --}}
                    <div class="mb-3 row">
                        <label for="satuan_output" class="col-md-2 col-form-label">Satuan Output</label>
                        <div class="col-md-10">
                            <input type="text" disabled
                                class="form-control @error('satuan_output') is-invalid @enderror" name="satuan_output"
                                id="satuan_output">
                            @error('satuan_output')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Nilai -->
                    <div class="mb-3 row">
                        <label for="nilai_rincian" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control @error('nilai_rincian') is-invalid @enderror"
                                name="nilai_rincian" id="nilai_rincian" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                                data-type="currency">
                            @error('nilai_rincian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="minus">
                                <label class="form-check-label" for="minus">
                                    Minus
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button id="simpan_detail_spp" class="btn btn-md btn-primary">Simpan</button>
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
    @include('penatausahaan.pengeluaran.spp_ls.js.edit');
@endsection
