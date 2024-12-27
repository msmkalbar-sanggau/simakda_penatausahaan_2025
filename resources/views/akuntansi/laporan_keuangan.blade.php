@extends('template.app')
@section('title', 'Laporan Keuangan | SIMAKDA')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ 'Laporan Keuangan' }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'App' }}</a></li>
                        <li class="breadcrumb-item active">{{ 'LK' }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lapbku">
                <div class="card-body">
                    {{ 'Laporan Realisasi Anggaran (LRA)' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lapspj">
                <div class="card-body">
                    {{-- {{ 'SPJ Fungsional' }} --}}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    {{-- modal cetak SPJ --}}
    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label for="labelcetak" id="labelcetak"></label></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Pilihan SKPD/Unit --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-6">
                            <label for="kd_skpd" class="form-label">Pilih</label><br>
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="pilihan1"
                                    value="skpd">
                                <label class="form-check-label" for="pilihan">SKPD</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="pilihan2"
                                    value="unit">
                                <label class="form-check-label" for="pilihan">Unit</label>
                            </div>
                        </div>
                        {{-- Bulan --}}
                        <div class="col-md-6">
                            <label for="jns_anggaran" class="form-label">Jenis Anggaran</label>
                            <select name="jns_anggaran" class="form-control" id="jns_anggaran">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($jns_anggaran as $anggaran)
                                    <option value="{{ $anggaran->kode }}" data-nama="{{ $anggaran->nama }}">
                                        {{ $anggaran->kode }} | {{ $anggaran->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- SKPD --}}
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="kd_skpd" class="form-label">Kode SKPD</label>
                            {{-- <input type="text"  class="form-control" id="kd_skpd" name="kd_skpd" value="{{ $data_skpd->kd_skpd }}" readonly> --}}
                            <select class="form-control select2-modal @error('kd_skpd') is-invalid @enderror"
                                style=" width: 100%;" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="nm_skpd" class="form-label">Bulan</label>
                            <select name="bulan" class="form-control" id="bulan">
                                <option value="">Silahkan Pilih</option>
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

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="bendahara" class="form-label">Bendahara</label>
                            <select class="form-control select2-modal @error('bendahara') is-invalid @enderror"
                                style=" width: 100%;" id="bendahara" name="bendahara">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('bendahara')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- PA/KPA --}}
                        <div class="col-md-6">
                            <label for="pa_kpa" class="form-label">Tanggal TTD</label>
                            <input type="date" id="tgl_ttd" name="tgl_ttd" class="form-control">
                        </div>
                    </div>

                    {{-- Bendahara --}}
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="pa_kpa" class="form-label">PA/KPA</label>
                            <select class="form-control select2-modal @error('pa_kpa') is-invalid @enderror"
                                style=" width: 100%;" id="pa_kpa" name="pa_kpa">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('pa_kpa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="spasi" class="form-label">Spasi</label>
                            <input type="number" value="1" min="1" class="form-control" id="spasi"
                                name="spasi">
                        </div>
                    </div>
                    <div class="mb-3 row" id="row-hidden3">
                        <div class="col-md-6">
                            <label for="pil_reg_pajak2" class="form-label">Pilihan 1</label>
                            <select class="form-control select2-modal" id="pil_reg_pajak2" name="pil_reg_pajak2">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="rinci">Rincian</option>
                                <option value="rekap">Rekap</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pil_reg_pajak" class="form-label">Pilihan 2</label>
                            <select class="form-control select2-modal" id="pil_reg_pajak" name="pil_reg_pajak">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="upgutu">UP/GU/TU</option>
                                <option value="ls">LS</option>
                                <option value="pl">Potongan Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row" id="row-hidden2">
                        {{-- pilihan1 --}}
                        <div class="col-md-4">
                            <label for="pajak1" class="form-label">Pilihan 1</label>
                            <select name="pajak1" class="form-control" id="pajak1">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                <option value="semua">Cetak Semua</option>
                                <option value="tanpalsphk">Cetak Tanpa LS Barang & Jasa Pihak Ketiga</option>
                                <option value="hanyalsphk">Cetak Hanya LS Barang & Jasa Pihak Ketiga</option>
                                <option value="upgutu">UP/GU/TU</option>
                                <option value="ls">LS</option>
                            </select>
                        </div>
                        {{-- pilihan2 --}}
                        <div class="col-md-4">
                            <label for="pajak2" class="form-label">Pilihan 2</label>
                            <select class="form-control select2-multiple @error('pajak2') is-invalid @enderror"
                                style=" width: 100%;" id="pajak2" name="pajak2" data-placeholder="Silahkan Pilih">

                            </select>
                            @error('pajak2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- pilihan pasal --}}
                        <div class="col-md-4">
                            <label for="pajak3" class="form-label">Pasal</label>
                            <select class="form-control select2-multiple @error('pajak3') is-invalid @enderror"
                                style=" width: 100%;" id="pajak3" name="pajak3" data-placeholder="Silahkan Pilih"
                                disabled>
                            </select>
                            @error('pajak3')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row" id="row-hidden4">
                        <div class="col-md-6">
                            <label for="pil_sppspmsp2d" class="form-label">Pilih Format</label>
                            <select class="form-control select2-modal" id="pil_sppspmsp2d" name="pil_sppspmsp2d">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="SPP">Register SPP</option>
                                <option value="SPM">Register SPM</option>
                                <option value="SP2D">Register SP2D</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md bku_pdf" data-jenis="pdf"
                                name="bku_pdf"> PDF</button>
                            <button type="button" class="btn btn-dark btn-md bku_layar" data-jenis="layar"
                                name="bku_layar">Layar</button>
                            <button type="button" class="btn btn-success btn-md bku_excel" data-jenis="excel"
                                name="bku_excel">Excel</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- modal cetak SPJ  --}}

    {{-- modal cetak Sub Rincian Objek --}}
    <div id="modal_cetak2" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label for="labelcetak2" id="labelcetak2"></label></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- SKPD --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-6">
                            <label for="kd_skpd2" class="form-label">Kode SKPD</label>
                            <select class="form-control select2-modal @error('kd_skpd2') is-invalid @enderror"
                                style=" width: 100%;" id="kd_skpd2" name="kd_skpd2">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- bendahara --}}

                        <div class="col-md-6">
                            <label for="bendahara2" class="form-label">Bendahara</label>
                            <select class="form-control select2-modal @error('bendahara2') is-invalid @enderror"
                                style=" width: 100%;" id="bendahara2" name="bendahara2">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('bendahara2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="bulan" class="form-label">Jenis Anggaran</label>
                            <select name="jns_anggaran2"
                                class="form-control select2-modal @error('jns_anggaran2') is-invalid @enderror"
                                style=" width: 100%;" id="jns_anggaran2">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($jns_anggaran2 as $anggaran2)
                                    <option value="{{ $anggaran2->kode }}" data-nama="{{ $anggaran2->nama }}">
                                        {{ $anggaran2->kode }} | {{ $anggaran2->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PA/KPA --}}
                        <div class="col-md-6">
                            <label for="pa_kpa2" class="form-label">PA/KPA</label>
                            <select class="form-control select2-modal @error('pa_kpa2') is-invalid @enderror"
                                style=" width: 100%;" id="pa_kpa2" name="pa_kpa2">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('pa_kpa2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="subkegiatan" class="form-label">Sub Kegiatan</label>
                            <select class="form-control select2-modal @error('subkegiatan') is-invalid @enderror"
                                style=" width: 100%;" id="subkegiatan" name="subkegiatan">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('subkegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6" id="periode0">
                            <label for="nm_skpd" class="form-label">Bulan</label>
                            <select name="bulan2" class="form-control" id="bulan2">
                                <option value="">Silahkan Pilih</option>
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
                        <div class="col-md-3" id="periode1">
                            <label for="tanggal1" class="form-label">Periode</label>
                            <input type="date" id="tanggal1" name="tanggal1" class="form-control">
                        </div>
                        <div class="col-md-3" id="periode2">
                            <label for="tanggal2" class="form-label">&nbsp;</label>
                            <input type="date" id="tanggal2" name="tanggal2" class="form-control">
                        </div>
                    </div>


                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="akunbelanja" class="form-label">Akun Belanja</label>
                            <select class="form-control select2-modal @error('akunbelanja') is-invalid @enderror"
                                style=" width: 100%;" id="akunbelanja" name="akunbelanja">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('akunbelanja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="pa_kpa" class="form-label">Tanggal TTD</label>
                            <input type="date" id="tgl_ttd2" name="tgl_ttd2" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6" id="jenissro">
                            <label for="jns_cetaksubrincianobjek" class="form-label">Jenis</label>
                            <select class="form-control select2-modal" id="jns_cetaksubrincianobjek"
                                name="jns_cetaksubrincianobjek">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="1">Permendagri 77</option>
                                <option value="2">Per Akun Belanja</option>
                                <option value="3">per Sub Kegiatan</option>
                                <option value="4">Semua Akun Belanja</option>
                                <option value="5">Cek pemakaian anggaran Akun belanja</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="jeniskk">
                            <label for="jns_cetak_kk" class="form-label">Jenis Realisasi</label>
                            <select class="form-control select2-modal" id="jns_cetak_kk" name="jns_cetak_kk">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                <option value="spj">SPJ</option>
                                {{-- <option value="pengajuan">Pengajuan</option> --}}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="spasi" class="form-label">Spasi</label>
                            <input type="number" value="1" min="1" class="form-control" id="spasi"
                                name="spasi">
                        </div>
                    </div>


                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md bku_pdf" data-jenis="pdf"
                                name="bku_pdf"> PDF</button>
                            <button type="button" class="btn btn-dark btn-md bku_layar" data-jenis="layar"
                                name="bku_layar">Layar</button>
                            <button type="button" class="btn btn-dark btn-md bku_excel" data-jenis="excel"
                                name="bku_excel">excel</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- modal cetak Sub Rincian Objek  --}}
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#bendahara').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });

            $('#bulan').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });

            $('#kd_skpd').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });

            $('#pa_kpa').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });
            $('#pajak1').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });
            $('#pajak2').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });
            $('#pajak3').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });
            $('#jns_anggaran').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });
            $('#pil_reg_pajak').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });
            $('#pil_sppspmsp2d').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });
            $('#jns_cetaksubrincianobjek').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });
            $('#jns_cetak_kk').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });
            $('#pil_reg_pajak2').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });

            document.getElementById('pil_reg_pajak').disabled = true;

            // modal 2
            $('#bendahara2').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });

            $('#pa_kpa2').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });

            $('#kd_skpd2').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });

            $('#jns_anggaran2').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });

            $('#subkegiatan').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });

            $('#bulan2').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });

            $('#akunbelanja').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });

        });

        // onclick card
        $('#lapbku').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak').modal('show');
            document.getElementById('jns_anggaran').disabled = true;
            $("#labelcetak").html("Cetak BKU");
            $("#labelcetak2").html("");
            document.getElementById('row-hidden').hidden = true; // Hide
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
            cari_skpd(kd_skpd, 'unit');
        });
        $('#lapspj').on('click', function() {
            $('#modal_cetak').modal('show');
            $("#labelcetak").html("Cetak SPJ Fungsional");
            $("#labelcetak2").html("");
            document.getElementById('jns_anggaran').disabled = false;
            document.getElementById('row-hidden').hidden = false; //show
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
        });

        $('#lapspja').on('click', function() {
            $('#modal_cetak').modal('show');
            $("#labelcetak").html("Cetak SPJ Administratif");
            $("#labelcetak2").html("");
            document.getElementById('jns_anggaran').disabled = false;
            document.getElementById('row-hidden').hidden = false; //show
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
        });

        $('#lapbpbank').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak').modal('show');
            document.getElementById('jns_anggaran').disabled = true;
            $("#labelcetak").html("Cetak Buku Pembantu Kas Bank");
            $("#labelcetak2").html("");
            document.getElementById('row-hidden').hidden = true; // Hide
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
            cari_skpd(kd_skpd, 'unit');
        });

        $('#lapbptunai').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak').modal('show');
            document.getElementById('jns_anggaran').disabled = true;
            $("#labelcetak").html("Cetak Buku Pembantu Kas Tunai");
            $("#labelcetak2").html("");
            document.getElementById('row-hidden').hidden = true; // Hide
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
            cari_skpd(kd_skpd, 'unit');
        });

        $('#lapbppajak').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak').modal('show');
            document.getElementById('jns_anggaran').disabled = true;
            $("#labelcetak").html("Cetak Buku Pembantu Pajak");
            $("#labelcetak2").html("");
            document.getElementById('row-hidden').hidden = true; // Hide
            document.getElementById('row-hidden2').hidden = false; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
            cari_skpd(kd_skpd, 'unit');
        });

        $('#lapbppanjar').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak').modal('show');
            document.getElementById('jns_anggaran').disabled = true;
            $("#labelcetak").html("Cetak Buku Pembantu Panjar");
            $("#labelcetak2").html("");
            document.getElementById('row-hidden').hidden = true; // Hide
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
            cari_skpd(kd_skpd, 'unit');
        });

        $('#laprealfisik').on('click', function() {
            $('#modal_cetak').modal('show');
            $("#labelcetak").html("Cetak Realisasi Fisik");
            $("#labelcetak2").html("");
            document.getElementById('jns_anggaran').disabled = false;
            document.getElementById('row-hidden').hidden = false; //show
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
        });

        $('#lapkasab').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            cari_skpd(kd_skpd, 'unit');
            $('#modal_cetak').modal('show');
            $("#labelcetak").html("Cetak Laporan Penutupan Kas Bulanan");
            $("#labelcetak2").html("");
            document.getElementById('jns_anggaran').disabled = false;
            document.getElementById('row-hidden').hidden = true; //show
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
        });

        $('#lapdth').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            cari_skpd(kd_skpd, 'unit');
            $('#modal_cetak').modal('show');
            $("#labelcetak").html("Cetak DTH");
            $("#labelcetak2").html("");
            document.getElementById('jns_anggaran').disabled = false;
            document.getElementById('row-hidden').hidden = true; //show
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
        });

        $('#lapregpajak').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            cari_skpd(kd_skpd, 'unit');
            $('#modal_cetak').modal('show');
            $("#labelcetak").html("Cetak Register Pajak");
            $("#labelcetak2").html("");
            document.getElementById('jns_anggaran').disabled = false;
            document.getElementById('row-hidden').hidden = true; //show
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = false; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
        });

        $('#lapregcp').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            cari_skpd(kd_skpd, 'unit');
            $('#modal_cetak').modal('show');
            $("#labelcetak").html("Cetak Register CP");
            $("#labelcetak2").html("");
            document.getElementById('jns_anggaran').disabled = false;
            document.getElementById('row-hidden').hidden = true; //show
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
        });

        $('#lapregsppspm').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak').modal('show');
            document.getElementById('jns_anggaran').disabled = true;
            $("#labelcetak").html("Cetak Register SPP/SPM/SP2D");
            $("#labelcetak2").html("");
            document.getElementById('row-hidden').hidden = true; // Hide
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = false; // Hide
            cari_skpd(kd_skpd, 'unit');
        });

        $('#lapbpsrobjek').on('click', function() {
            let kd_skpd2 = "{{ $data_skpd->kd_skpd }}";
            cari_skpd2(kd_skpd2, 'unit');
            $('#modal_cetak2').modal('show');
            $("#labelcetak2").html("Cetak Buku Sub Rincian Objek");
            $("#labelcetak").html("");
            document.getElementById('jns_anggaran').disabled = false;
            document.getElementById('akunbelanja').disabled = false;
            document.getElementById('tanggal1').disabled = false;
            document.getElementById('tanggal2').disabled = false;
            document.getElementById('row-hidden').hidden = true; //show
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
            document.getElementById('jenissro').hidden = false; // Hide
            document.getElementById('jeniskk').hidden = true; // Hide
            document.getElementById('periode1').hidden = false; // Hide
            document.getElementById('periode2').hidden = false; // Hide
            document.getElementById('periode0').hidden = true; // show
        });

        $('#lapkartukendali').on('click', function() {
            let kd_skpd2 = "{{ $data_skpd->kd_skpd }}";
            cari_skpd2(kd_skpd2, 'unit');
            $('#modal_cetak2').modal('show');
            $("#labelcetak2").html("Cetak Kartu Kendali Sub Kegiatan");
            $("#labelcetak").html("");
            document.getElementById('jns_anggaran').disabled = false;
            document.getElementById('akunbelanja').disabled = true;
            document.getElementById('tanggal1').disabled = true;
            document.getElementById('tanggal2').disabled = true;
            document.getElementById('row-hidden').hidden = true; //show
            document.getElementById('row-hidden2').hidden = true; // Hide
            document.getElementById('row-hidden3').hidden = true; // Hide
            document.getElementById('row-hidden4').hidden = true; // Hide
            document.getElementById('jenissro').hidden = true; // Hide
            document.getElementById('jeniskk').hidden = false; // show
            document.getElementById('periode1').hidden = true; // Hide
            document.getElementById('periode2').hidden = true; // Hide
            document.getElementById('periode0').hidden = false; // show
        });


        // cari skpd/org

        $('input:radio[name="inlineRadioOptions"]').change(function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            if ($(this).val() == 'skpd') {
                cari_skpd(kd_skpd, 'skpd')
            } else {
                cari_skpd(kd_skpd, 'unit')
            }
        });

        function cari_skpd(kd_skpd, jenis) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara.skpd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    jenis: jenis
                },
                success: function(data) {
                    $('#kd_skpd').empty();
                    $('#kd_skpd').append(
                        `<option value="" disabled selected>Pilih SKPD</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_skpd').append(
                            `<option value="${data.kd_skpd}" data-nama="${data.nm_skpd}">${data.kd_skpd} | ${data.nm_skpd}</option>`
                        );
                    })
                }
            })
        }

        function cari_skpd2(kd_skpd, jenis) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara.skpd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    jenis: jenis
                },
                success: function(data) {
                    $('#kd_skpd2').empty();
                    $('#kd_skpd2').append(
                        `<option value="" disabled selected>Pilih SKPD</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_skpd2').append(
                            `<option value="${data.kd_skpd}" data-nama="${data.nm_skpd}">${data.kd_skpd} | ${data.nm_skpd}</option>`
                        );
                    })
                }
            })
        }

        // action skpd
        $('#kd_skpd').on('select2:select', function() {
            let kd_skpd = this.value;
            cari_bendahara(kd_skpd);
            cari_pakpa(kd_skpd);
        });

        $('#kd_skpd2').on('select2:select', function() {
            let kd_skpd2 = this.value;
            cari_bendahara2(kd_skpd2);
            cari_pakpa2(kd_skpd2);
        });

        $('#jns_anggaran2').on('select2:select', function() {
            let kd_skpd2 = $('#kd_skpd2').val();
            let jns_anggaran2 = this.value;
            cari_subkegiatan(kd_skpd2, jns_anggaran2);
        });

        $('#subkegiatan').on('select2:select', function() {
            let kd_skpd2 = $('#kd_skpd2').val();
            let jns_anggaran2 = $('#jns_anggaran2').val();
            let subkegiatan = this.value;
            cari_akunbelanja(kd_skpd2, subkegiatan, jns_anggaran2);
        });

        $('#pil_reg_pajak2').on('select2:select', function() {
            if (this.value == 'rinci') {
                document.getElementById('pil_reg_pajak').disabled = false;
            } else {
                $('#pil_reg_pajak').val('').trigger("change");
                document.getElementById('pil_reg_pajak').disabled = true;
            }

        });

        function cari_bendahara(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara.bendahara') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#bendahara').empty();
                    $('#bendahara').append(
                        `<option value="" disabled selected>Pilih Bendahara Pengeluaran</option>`);
                    $.each(data, function(index, data) {
                        $('#bendahara').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }

        function cari_bendahara2(kd_skpd2) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara.bendahara') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd2
                },
                success: function(data) {
                    $('#bendahara2').empty();
                    $('#bendahara2').append(
                        `<option value="" disabled selected>Pilih Bendahara Pengeluaran</option>`);
                    $.each(data, function(index, data) {
                        $('#bendahara2').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }

        function cari_pakpa(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara.pakpa') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#pa_kpa').empty();
                    $('#pa_kpa').append(
                        `<option value="" disabled selected>Pilih PA/KPA</option>`);
                    $.each(data, function(index, data) {
                        $('#pa_kpa').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }

        function cari_pakpa2(kd_skpd2) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara.pakpa') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd2
                },
                success: function(data) {
                    $('#pa_kpa2').empty();
                    $('#pa_kpa2').append(
                        `<option value="" disabled selected>Pilih PA/KPA</option>`);
                    $.each(data, function(index, data) {
                        $('#pa_kpa2').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }

        // cari sub kegiatan
        function cari_subkegiatan(kd_skpd2, jns_anggaran2) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara.subkegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd2,
                    jns_anggaran: jns_anggaran2
                },
                success: function(data) {
                    $('#subkegiatan').empty();
                    $('#subkegiatan').append(
                        `<option value="" disabled selected>Pilih Sub Kegiatan</option>`);
                    $.each(data, function(index, data) {
                        $('#subkegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            })
        }

        // cari Akun Belanja
        function cari_akunbelanja(kd_skpd2, subkegiatan, jns_anggaran2) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara.akunbelanja') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd2,
                    jns_anggaran: jns_anggaran2,
                    subkegiatan: subkegiatan
                },
                success: function(data) {
                    $('#akunbelanja').empty();
                    $('#akunbelanja').append(
                        `<option value="" disabled selected>Pilih Akun Belanja</option>`);
                    $.each(data, function(index, data) {
                        $('#akunbelanja').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            })
        }

        $('#pajak1').on('select2:select', function() {
            let pajak1 = document.getElementById('pajak1').value;
            $.ajax({
                url: "{{ route('cetak_bppajak.cari_jenis') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    pajak1: pajak1,
                },
                success: function(data) {
                    $('#pajak2').empty();
                    $('#pajak2').append(`<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#pajak2').append(
                            `<option value="${data.id}" data-nama="${data.text}">${data.text}</option>`
                        );
                    })
                }
            })
        });
        $('#pajak2').on('select2:select', function() {
            if (document.getElementById('pajak2').value == '3') {
                document.getElementById('pajak3').disabled = false;
                $.ajax({
                    url: "{{ route('cetak_bppajak.cari_pasal') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $('#pajak3').empty();
                        $('#pajak3').append(`<option value="0">Silahkan Pilih</option>`);
                        $.each(data, function(index, data) {
                            $('#pajak3').append(
                                `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}">${data.kd_rek6} - ${data.nm_rek6}</option>`
                            );
                        })
                    }
                })
            } else {
                document.getElementById('pajak3').disabled = true;
                document.getElementById('pajak3').value = '';
            }
        });

        $('.bku_layar').on('click', function() {
            Cetak(1)
        });
        $('.bku_pdf').on('click', function() {
            Cetak(2)
        });
        $('.bku_excel').on('click', function() {
            Cetak(3)
        });

        function Cetak(jns_cetak) {

            let spasi = document.getElementById('spasi').value;
            let bulan = document.getElementById('bulan').value;
            let tgl_ttd = document.getElementById('tgl_ttd').value;
            let bendahara = document.getElementById('bendahara').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jns_anggaran = document.getElementById('jns_anggaran').value;
            let jenis_print = $(this).data("jenis");
            let pajak1 = document.getElementById('pajak1').value;
            let pajak2 = document.getElementById('pajak2').value;
            let pajak3 = document.getElementById('pajak3').value;
            let regpajak1 = document.getElementById('pil_reg_pajak').value;
            let regpajak2 = document.getElementById('pil_reg_pajak2').value;
            let regsppspmsp2d = document.getElementById('pil_sppspmsp2d').value;
            let jenis_cetak = document.getElementById('labelcetak').textContent
            let jenis_cetak2 = document.getElementById('labelcetak2').textContent
            let jenis_cetak3 = document.getElementById('jns_cetaksubrincianobjek').value
            let jenis_cetak4 = document.getElementById('jns_cetak_kk').value


            // subrincian objek
            let bendahara2 = document.getElementById('bendahara2').value;
            let pa_kpa2 = document.getElementById('pa_kpa2').value;
            let kd_skpd2 = document.getElementById('kd_skpd2').value;
            let tanggal1 = document.getElementById('tanggal1').value;
            let tanggal2 = document.getElementById('tanggal2').value;
            let bulan2 = document.getElementById('bulan2').value;
            let tgl_ttd2 = document.getElementById('tgl_ttd2').value;
            let subkegiatan = document.getElementById('subkegiatan').value;
            let akunbelanja = document.getElementById('akunbelanja').value;
            let jns_anggaran2 = document.getElementById('jns_anggaran2').value;


            if (jenis_cetak2 == 'Cetak Buku Sub Rincian Objek') {
                if (!bendahara2) {
                    alert('Bendahara Pengeluaran tidak boleh kosong!');
                    return;
                }
                if (!kd_skpd2) {
                    alert('kd_skpd tidak boleh kosong!');
                    return;
                }
                if (!pa_kpa2) {
                    alert("PA/KPA tidak boleh kosong!");
                    return;
                }
                if (!subkegiatan) {
                    alert("Sub Kegiatan tidak boleh kosong!");
                    return;
                }
                if (!akunbelanja) {
                    alert("Akun Belanja tidak boleh kosong!");
                    return;
                }

                if (!tanggal1) {
                    alert("Periode 1 tidak boleh kosong!");
                    return;
                }

                if (!tanggal2) {
                    alert("Tanggal tandatangan tidak boleh kosong!");
                    return;
                }
                if (!tgl_ttd2) {
                    alert("Akun Belanja tidak boleh kosong!");
                    return;
                }
            } else if (jenis_cetak2 == 'Cetak Kartu Kendali Sub Kegiatan') {
                if (!bendahara2) {
                    alert('Bendahara Pengeluaran tidak boleh kosong!');
                    return;
                }
                if (!kd_skpd2) {
                    alert('kd_skpd tidak boleh kosong!');
                    return;
                }
                if (!pa_kpa2) {
                    alert("PA/KPA tidak boleh kosong!");
                    return;
                }
                if (!subkegiatan) {
                    alert("Sub Kegiatan tidak boleh kosong!");
                    return;
                }


                if (!tgl_ttd2) {
                    alert("Akun Belanja tidak boleh kosong!");
                    return;
                }
            } else {
                if (!bendahara) {
                    alert('Bendahara Pengeluaran tidak boleh kosong!');
                    return;
                }
                if (!kd_skpd) {
                    alert('kd_skpd tidak boleh kosong!');
                    return;
                }
                if (!pa_kpa) {
                    alert("PA/KPA tidak boleh kosong!");
                    return;
                }
            }

            if (jenis_cetak == 'Cetak BKU') {

                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_bku') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);


                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak SPJ Fungsional') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_spj_fungsional') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("judul", 'Fungsional');
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak SPJ Administratif') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_spj_fungsional') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("judul", 'Administratif');
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Buku Pembantu Kas Bank') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_bp_kasbank') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Buku Pembantu Kas Tunai') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_bp_kastunai') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Buku Pembantu Pajak') {
                let url
                if (!pajak2) {
                    alert('Pilihan tidak boleh kosong!');
                    return;
                }
                if (pajak2 == '1') {
                    url = new URL("{{ route('skpd.laporan_bendahara.cetak_bp_pajak1') }}");
                } else if (pajak2 == '2') {
                    url = new URL("{{ route('skpd.laporan_bendahara.cetak_bp_pajak2') }}");
                } else if (pajak2 == '3') {
                    url = new URL("{{ route('skpd.laporan_bendahara.cetak_bp_pajak3') }}");
                } else if (pajak2 == '4' || pajak2 == '5') {
                    url = new URL("{{ route('skpd.laporan_bendahara.cetak_bp_pajak4') }}");
                }

                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("pilihan1", pajak1);
                searchParams.append("pilihan2", pajak2);
                searchParams.append("pilihan3", pajak3);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Buku Pembantu Panjar') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_bp_panjar') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Realisasi Fisik') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_realisasi_fisik') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Laporan Penutupan Kas Bulanan') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_laporan_penutupan_kas_bulanan') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak DTH') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_dth') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Register Pajak') {
                let url
                if (!regpajak2) {
                    alert('Pilihan 1 tidak boleh kosong!');
                    return;
                }
                if (regpajak2 == 'rinci') {
                    if (regpajak1 == 'upgutu' || regpajak1 == 'ls') { //UPGUTU
                        url = new URL("{{ route('skpd.laporan_bendahara.cetak_reg_pajak') }}");
                    } else if (regpajak1 == 'pl') {
                        url = new URL("{{ route('skpd.laporan_bendahara.cetak_reg_pajakpl') }}");
                    }
                } else {
                    url = new URL("{{ route('skpd.laporan_bendahara.cetak_reg_pajak_rekap') }}");
                }


                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("jenis", regpajak1);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Register CP') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_register_cp') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Buku Pembantu Kas Bank') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_bp_kasbank') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else if (jenis_cetak == 'Cetak Register SPP/SPM/SP2D') {
                let url = new URL("{{ route('skpd.laporan_bendahara.cetak_regsppspm') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("bulan", bulan);
                searchParams.append("jenis_reg", regsppspmsp2d);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            } else {
                if (jenis_cetak2 == 'Cetak Buku Sub Rincian Objek') {
                    let url;

                    if (jenis_cetak3 == '1') {
                        url = new URL("{{ route('skpd.laporan_bendahara.cetak_sub_rincian_objek') }}");
                    } else if (jenis_cetak3 == '2') {
                        url = new URL("{{ route('skpd.laporan_bendahara.cetak_sub_rincian_objek2') }}");
                    } else if (jenis_cetak3 == '3') {
                        url = new URL("{{ route('skpd.laporan_bendahara.cetak_sub_rincian_objek3') }}");
                    } else if (jenis_cetak3 == '4') {
                        url = new URL("{{ route('skpd.laporan_bendahara.cetak_sub_rincian_objek4') }}");
                    } else if (jenis_cetak3 == '5') {
                        url = new URL("{{ route('skpd.laporan_bendahara.cetak_sub_rincian_objek5') }}");
                    }

                    let searchParams = url.searchParams;
                    searchParams.append("spasi", spasi);
                    searchParams.append("bendahara", bendahara2);
                    searchParams.append("pa_kpa", pa_kpa2);
                    searchParams.append("tanggal1", tanggal1);
                    searchParams.append("tanggal2", tanggal2);
                    searchParams.append("subkegiatan", subkegiatan);
                    searchParams.append("akunbelanja", akunbelanja);
                    searchParams.append("jns_anggaran", jns_anggaran2);
                    searchParams.append("kd_skpd", kd_skpd2);
                    searchParams.append("tgl_ttd", tgl_ttd2);
                    searchParams.append("jenis_print", jenis_print);
                    searchParams.append("cetak", jns_cetak);
                    window.open(url.toString(), "_blank");
                } else if (jenis_cetak2 == 'Cetak Kartu Kendali Sub Kegiatan') {
                    let url;

                    if (jenis_cetak4 == 'spj') {
                        url = new URL("{{ route('skpd.laporan_bendahara.cetak_kk_spj') }}");
                    } else if (jenis_cetak4 == 'pengajuan') {
                        url = new URL("{{ route('skpd.laporan_bendahara.cetak_kk_pengajuan') }}");
                    }

                    let searchParams = url.searchParams;
                    searchParams.append("spasi", spasi);
                    searchParams.append("bendahara", bendahara2);
                    searchParams.append("pa_kpa", pa_kpa2);
                    searchParams.append("bulan", bulan2);
                    searchParams.append("subkegiatan", subkegiatan);
                    searchParams.append("jns_anggaran", jns_anggaran2);
                    searchParams.append("kd_skpd", kd_skpd2);
                    searchParams.append("tgl_ttd", tgl_ttd2);
                    searchParams.append("jenis_print", jenis_print);
                    searchParams.append("cetak", jns_cetak);
                    window.open(url.toString(), "_blank");
                } else {
                    alert('-' + jenis_cetak + '- Tidak ada url');
                }

            }
        }
    </script>
@endsection
