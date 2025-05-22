@extends('template.app')
@section('title', 'Tambah SPP UP | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input SPP UP
                </div>
                <div class="card-body">
                    @csrf
                    {{-- No SPP dan Tanggal SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-4">
                            {{-- <input class="form-control" type="text" id="no_spp" name="no_spp" required readonly>
                            <input class="form-control @error('no_urut') is-invalid @enderror" type="text" id="no_urut"
                                name="no_urut" required readonly hidden value="{{ $no_up }}"> --}}
                            <input type="number" name="no_spp" id="no_spp" min="1" class="form-control"
                                oninput="this.value = Math.abs(this.value)" pattern="/^-?\d+\.?\d*$/">
                            @error('no_spp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control @error('tgl_spp') is-invalid @enderror" id="tgl_spp"
                                name="tgl_spp">
                            <input class="form-control" type="text" id="bulan" name="bulan" required readonly
                                hidden>
                            @error('tgl_spp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="date" class="form-control @error('tgl_spp_lalu') is-invalid @enderror"
                                id="tgl_spp_lalu" name="tgl_spp_lalu" hidden value="{{ $data_tgl->tgl_spp }}">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">Status Anggaran</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="statusAnggaran"
                                name="statusAnggaran" data-placeholder="Silahkan Pilih">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftarAnggaran as $anggaran)
                                    <option value="{{ $anggaran->kode }}">
                                        {{ $anggaran->kode }} - {{ $anggaran->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan Inputan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="bulanInputan"
                                name="bulanInputan" data-placeholder="Silahkan Pilih">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>
                    {{-- KD SKPD dan Beban --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD/Unit</label>
                        <div class="col-md-4">
                            <input class="form-control @error('kd_skpd') is-invalid @enderror" type="text" id="kd_skpd"
                                name="kd_skpd" required readonly value="{{ $skpd->kd_skpd }}">
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="beban" class="col-md-2 col-form-label">Beban</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('beban') is-invalid @enderror"
                                style="width: 100%" id="beban" name="beban">
                                <option value="" disabled>Silahkan Pilih</option>
                                <option value="1" selected>UP</option>
                            </select>
                            @error('beban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Nama SKPD dan Keperluan --}}
                    <div class="mb-3 row">
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control @error('nm_skpd') is-invalid @enderror" type="text" id="nm_skpd"
                                name="nm_skpd" required readonly value="{{ $skpd->nm_skpd }}">
                            @error('nm_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea type="text" class="form-control @error('keperluan') is-invalid @enderror" id="keperluan"
                                name="keperluan"></textarea>
                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- No SPD dan Bank --}}
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">Nomor SPD</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('no_spd') is-invalid @enderror"
                                style="width: 100%" id="no_spd" name="no_spd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($data_spd as $spd)
                                    <option value="{{ $spd->no_spd }}">{{ $spd->no_spd }} | {{ $spd->tgl_spd }}
                                    </option>
                                @endforeach
                            </select>
                            @error('no_spd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('bank') is-invalid @enderror"
                                style="width: 100%;" id="bank" name="bank" data-placeholder="Silahkan Pilih">
                                <option value="" disabled selected>Silahkan Pilih Bank</option>
                                @foreach ($data_bank as $bank)
                                    <option value="{{ $bank->kode }}">{{ $bank->nama }}</option>
                                @endforeach
                            </select>
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Rekening Bank dan Nama Penerima --}}
                    <div class="mb-3 row">
                        <label for="rekening" class="col-md-2 col-form-label">Rekening</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('rekening') is-invalid @enderror"
                                style=" width: 100%;" id="rekening" name="rekening" data-placeholder="Silahkan Pilih">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($data_rek as $rek)
                                    <option value="{{ $rek->rekening }}" data-nama="{{ $rek->nm_rekening }}"
                                        data-npwp="{{ $rek->npwp }}">
                                        {{ $rek->rekening }} || {{ $rek->nm_rekening }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nama_penerima" class="col-md-2 col-form-label">Nama Penerima</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nama_penerima') is-invalid @enderror"
                                id="nama_penerima" name="nama_penerima" readonly>
                            @error('nama_penerima')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Kode Akun dan Nama Akun --}}
                    <div class="mb-3 row">
                        <label for="kode_akun" class="col-md-2 col-form-label">Kode Akun</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('kode_akun') is-invalid @enderror"
                                style=" width: 100%;" id="kode_akun" name="kode_akun" data-placeholder="Silahkan Pilih">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="11010301002" data-nama="Uang Persediaan">Uang Persediaan</option>
                            </select>
                            @error('kode_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nama_akun" class="col-md-2 col-form-label">Nama Akun</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nama_akun') is-invalid @enderror"
                                id="nama_akun" name="nama_akun" readonly>
                            @error('nama_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- NPWP dan Nilai UP --}}
                    <div class="mb-3 row">
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input class="form-control @error('npwp') is-invalid @enderror" type="text"
                                id="npwp" name="npwp" required readonly>
                            @error('npwp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label for="nilai_up" class="col-md-2 col-form-label">Nilai UP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control @error('nilai_up') is-invalid @enderror"
                                id="nilai_up" name="nilai_up" readonly value="{{ rupiah($nilai_up->nilai) }}">
                            <input type="text" class="form-control @error('tahun_anggaran') is-invalid @enderror"
                                id="tahun_anggaran" name="tahun_anggaran" readonly value="{{ $tahun_anggaran }}" hidden>
                            @error('nilai_up')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="simpan_spp" class="btn btn-primary btn-md">Simpan</button>
                        <a href="{{ route('sppup.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('penatausahaan.pengeluaran.spp_up.js.create');
@endsection
