@extends('template.app')
@section('title', 'SPP TU | SIMAKDA')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Input SPP TU
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <!-- <div class="mb-3 row">
                        <label for="no_tersimpan" class="col-md-2 col-form-label">No Tersimpan</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control js-example-responsive" id="no_tersimpan" name="no_tersimpan" placeholder="Tidak Perlu diisi atau di Edit">
                            <input type="hidden" readonly class="form-control js-example-responsive" id="tahun" name="tahun" value="{{tahun_anggaran()}}">
                        </div>
                    </div> -->
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode OPD</label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $data_skpd->kd_skpd }}" class="form-control js-example-responsive" id="kd_skpd" name="kd_skpd" readonly>
                            <input type="hidden" readonly class="form-control js-example-responsive" id="tahun" name="tahun" value="{{tahun_anggaran()}}">
                        </div>
                        <label for="nm_opd" class="col-md-2 col-form-label">Nama OPD</label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $data_skpd->nm_skpd }}" class="form-control js-example-responsive" id="nm_opd" name="nm_opd" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="date" value="{{ $dataspptu->tgl_spp }}" class="form-control js-example-responsive" id="tgl_spp" name="tgl_spp">
                            <!-- <input type="hidden" class="form-control" id="blnspp" name="blnspp" value=""> -->
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <select name="kebutuhan_bulan" class="form-control js-example-responsive" id="kebutuhan_bulan" disabled>
                                <option value="">Pilih Kebutuhan Bulan</option>
                                <option value="1" {{ $dataspptu->bulan == '1' ? 'selected' : '' }}>1 | Januari</option>
                                <option value="2" {{ $dataspptu->bulan == '2' ? 'selected' : '' }}>2 | Februari</option>
                                <option value="3" {{ $dataspptu->bulan == '3' ? 'selected' : '' }}>3 | Maret</option>
                                <option value="4" {{ $dataspptu->bulan == '4' ? 'selected' : '' }}>4 | April</option>
                                <option value="5" {{ $dataspptu->bulan == '5' ? 'selected' : '' }}>5 | Mei</option>
                                <option value="6" {{ $dataspptu->bulan == '6' ? 'selected' : '' }}>6 | Juni</option>
                                <option value="7" {{ $dataspptu->bulan == '7' ? 'selected' : '' }}>7 | Juli</option>
                                <option value="8" {{ $dataspptu->bulan == '8' ? 'selected' : '' }}>8 | Agustus</option>
                                <option value="9" {{ $dataspptu->bulan == '9' ? 'selected' : '' }}>9 | September</option>
                                <option value="10" {{ $dataspptu->bulan == '10' ? 'selected' : '' }}>10 | Oktober</option>
                                <option value="11" {{ $dataspptu->bulan == '11' ? 'selected' : '' }}>11 | November</option>
                                <option value="12" {{ $dataspptu->bulan == '12' ? 'selected' : '' }}>12 | Desember</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="jenis_beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select class="form-control js-example-responsive" id="jenis_beban" name="jenis_beban">
                                <option value="3" {{ $dataspptu->jns_spp == '3' ? 'selected' : '' }}>TU</option>
                            </select>
                        </div>
                        <label for="no_spp" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control js-example-responsive" style="background-color:powderblue;" value="{{ $dataspptu->no_spp }}" id="no_spp" name="no_spp" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="no_spd_edit" class="col-md-2 col-form-label">No SPD</label>
                        <div class="col-md-4">
                            <select class="form-control js-example-responsive" id="no_spd_edit" name="no_spd_edit">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($nomor_spd as $no_spd)
                                <option value="{{ $no_spd->no_spd }}" data-no_spd="{{ $no_spd->tgl_spd }}" {{ $no_spd->no_spd == $dataspptu->no_spd ? 'selected' : '' }}>
                                    {{ $no_spd->no_spd }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="tgl_spd_edit" class="col-md-2 col-form-label">Tanggal SPD</label>
                        <div class="col-md-4">
                            <input type="date" @foreach ($nomor_spd as $no_spd) value="{{$no_spd->tgl_spd}}" {{ $no_spd->no_spd == $dataspptu->no_spd ? 'selected' : '' }} @endforeach class="form-control" id="tgl_spd_edit" name="tgl_spd_edit" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="kd_subkeg_edit" class="col-md-2 col-form-label">Kode Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $dataspptu->kd_sub_kegiatan }}" class="form-control js-example-responsive" id="kd_subkeg_edit" name="kd_subkeg_edit" readonly>
                        </div>
                        <label for="nm_sub_edit" class="col-md-2 col-form-label">Nama Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input type="text" value='{{ $dataspptu->nm_sub_kegiatan }}' readonly class="form-control" id="nm_sub_edit" name="nm_sub_edit">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="bank_edit" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <select class="form-control js-example-responsive" id="bank_edit" name="bank_edit">
                                <option value="">Pilih Rekening</option>
                                @foreach ($master_bank as $bank)
                                <option value="{{ $bank->kode }}" {{ $bank->kode == $dataspptu->bank ? 'selected' : '' }}>
                                    Nama Bank : {{ $bank->nama }}
                                </option>
                                @endforeach
                            </select>

                        </div>
                        <label for="rek_bank_edit" class="col-md-2 col-form-label">Rekening Bank</label>
                        <div class="col-md-4">
                            <select class="form-control js-example-responsive" id="rek_bank_edit" name="rek_bank_edit">
                                @foreach ($rekening_bank as $rek_bank)
                                <option value="{{ $rek_bank->rekening }}" data-nm_rekening="{{ $rek_bank->nm_rekening }}" data-npwp="{{ $rek_bank->npwp }}" {{ $rek_bank->rekening == $dataspptu->no_rek ? 'selected' : '' }}>
                                    {{ $rek_bank->nm_rekening }} || {{ $rek_bank->npwp }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="keperluan_edit" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea name="keperluan" id="keperluan_edit" class="form-control js-example-responsive" style="height:100px;">{{ $dataspptu->keperluan }}</textarea>
                        </div>
                        <label for="npwp_edit" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control js-example-responsive" id="npwp_edit" name="npwp">
                        </div>
                    </div>
                    <div style="float: right;">
                        <button id="simpan" class="btn btn-primary btn-md" type="submit" onclick="SimpanData()">Simpan</button>
                        <a href="{{ route('spptu.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Detail Rekening SPP -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Input Detail SPP TU
                <button type="button" style="float: right" id="tambah_rincian_edit" data-toggle="modal" class="btn btn-primary"><i class="bx bx-plus-circle"></i> Tambah Rekening</button>
            </div>
            <div class="card-body table-responsive">
                <table id="rincian_spptu_edit" class="table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Rekening</th>
                            <th>Nama Rekening</th>
                            <th>Nilai</th>
                            <th>Sumber</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="mb-2 mt-2 row">
                    <label for="total_nilai_edit" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                    <div class="col-md-4">
                        <input type="text" style="text-align: right" readonly class="form-control @error('total_nilai') is-invalid @enderror" id="total_nilai" name="total_nilai">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade bd-example-modal-xl js-example-responsive" tabindex="-1" role="dialog" id="myModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable js-example-responsive">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-secondary">
                <h5 class="modal-title text-white">Rincian Rekening SPP</h5>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="mb-3 row">
                    <label for="no_spd_edit" class="col-md-3 col-form-label js-example-responsive">No SPD</label>
                    <div class="col-md-6">
                        <select class="form-control js-example-responsive" id="no_spd_edit" name="no_spd_edit">
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="kd_sub_keg" class="col-md-3 col-form-label js-example-responsive">Kode Sub Kegiatan</label>
                    <div class="col-md-6">
                        <input type="text" readonly class="form-control js-example-responsive" id="kd_sub_keg" name="kd_sub_keg">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="nm_sub_keg" class="col-md-3 col-form-label js-example-responsive">Nama Sub Kegiatan</label>
                    <div class="col-md-6">
                        <input type="text" readonly class="form-control js-example-responsive" id="nm_sub_keg" name="nm_sub_keg">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="kd_rek" class="col-md-3 col-form-label js-example-responsive">Kode Rekening</label>
                    <div class="col-md-6">
                        <select class="form-control js-example-responsive" id="kd_rek" name="kd_rek">
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="kd_sumberdana" class="col-md-3 col-form-label js-example-responsive">Sumber Dana</label>
                    <div class="col-md-6">
                        <select class="form-control js-example-responsive" id="kd_sumberdana" name="kd_sumberdana">
                        </select>
                    </div>
                </div>


                <!-- Total SPD -->
                <div class="mb-3 row">
                    <label for="total_spd" class="col-md-2 col-form-label js-example-responsive">Total SPD</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="total_spd" id="total_spd">
                    </div>
                    <label for="realisasi_angkas" class="col-md-1 col-form-label js-example-responsive">Realisasi</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" name="realisasi_spd" id="realisasi_spd">
                    </div>
                    <label for="sisa_angkas" class="col-md-1 col-form-label js-example-responsive">Sisa</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" style="background-color:#B0E0E6;" name="sisa_spd" id="sisa_spd">
                    </div>
                </div>

                <!-- Total Angkas -->
                <div class="mb-3 row">
                    <label for="total_angkas" class="col-md-2 col-form-label js-example-responsive">Total Anggaran Kas</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="total_angkas" id="total_angkas">
                    </div>
                    <label for="realisasi_angkas" class="col-md-1 col-form-label js-example-responsive">Realisasi</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" name="realisasi_angkas" id="realisasi_angkas">
                    </div>
                    <label for="sisa_angkas" class="col-md-1 col-form-label js-example-responsive">Sisa</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" style="background-color:#B0E0E6;" name="sisa_angkas" id="sisa_angkas">
                    </div>
                </div>

                <!-- Anggaran -->
                <div class="mb-3 row">
                    <label for="total_anggaran" class="col-md-2 col-form-label js-example-responsive">Anggaran</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="total_anggaran" id="total_anggaran">
                    </div>
                    <label for="realisasi_anggaran" class="col-md-1 col-form-label js-example-responsive">Realisasi</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" name="realisasi_anggaran" id="realisasi_anggaran">
                    </div>
                    <label for="sisa_anggaran" class="col-md-1 col-form-label js-example-responsive">Sisa</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" style="background-color:#B0E0E6;" name="sisa_anggaran" id="sisa_anggaran">
                    </div>
                </div>

                <!-- Nilai Sumber Dana -->
                <div class="mb-3 row">
                    <label for="total_sumber" class="col-md-2 col-form-label js-example-responsive">Sumber Dana</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="total_sumber" id="total_sumber">
                    </div>
                    <label for="realisasi_sumber" class="col-md-1 col-form-label js-example-responsive">Realisasi</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" name="realisasi_sumber" id="realisasi_sumber">
                    </div>
                    <label for="sisa_sumber" class="col-md-1 col-form-label js-example-responsive">Sisa</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" style="background-color:#B0E0E6;" name="sisa_sumber" id="sisa_sumber">
                    </div>
                </div>

                <!-- Status Anggaran dan Anggaran Kas -->
                <div class="mb-3 row">
                    <label for="status_anggaran" class="col-md-2 col-form-label js-example-responsive">Status Anggaran</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="status_anggaran" id="status_anggaran">
                    </div>
                    <label for="status_angkas" class="col-md-2 col-form-label js-example-responsive">Status Anggaran Kas</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="status_angkas" id="status_angkas">
                    </div>
                </div>

                <!-- Inputan nilai -->
                <div class="mb-3 row">
                    <label for="input_nilai" class="col-md-2 col-form-label js-example-responsive">Nilai</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control js-example-responsive" id="input_nilai" name="input_nilai" data-type="currency">
                    </div>
                </div>
                <div class=" mb-4 row text-center">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success" id="tambahrincianrekening" style="margin-right:4px"><i class="bx bx-plus-circle"></i> Tambah Rincian Rekening
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!-- <div class="card"> -->
                        <div class="card-body table-responsive">
                            <table id="detailrincian_spptu" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Rekening</th>
                                        <th>Nama Rekening</th>
                                        <th>Nilai</th>
                                        <th>Sumber</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btnbtn_close btn-danger" data-dismiss="modal">Kembali</button>
            </div>

        </div>
    </div>
</div>
<!-- End -->

@endsection
@section('js')
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#tambah_rincian').on('click', function() {
            // Modal Pop Up
            $('#myModal').modal('show');
        });

        $('#no_spd_edit').select2({
            placeholder: 'Pilih No SPD',
            theme: 'bootstrap-5'
        });
        $('#bank_edit').select2({
            theme: 'bootstrap-5'
        });

        $('#rek_bank_edit').select2({
            placeholder: 'Pilih No Rekening Bank',
            theme: 'bootstrap-5'
        });

        $('#kd_subkeg_edit').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
@endsection