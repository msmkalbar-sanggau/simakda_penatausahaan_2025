@extends('template.app')
@section('title', 'Laporan Bendahara Penerimaan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ 'Laporan Bendahara Penerimaan' }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'App' }}</a></li>
                        <li class="breadcrumb-item">{{ 'Laporan Bendahara' }}</li>
                        <li class="breadcrumb-item active">{{ 'Penerimaan ' }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lapbku">
                <div class="card-body">
                    {{ 'Buku Penerimaan dan Penyetoran' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lapspj">
                <div class="card-body">
                    {{ 'SPJ Pendapatan' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lapsetoran">
                <div class="card-body">
                    {{ 'Cek Buku Setoran' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="laprincian">
                <div class="card-body">
                    {{ 'BP Sub Rincian Objek' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="register_kasda">
                <div class="card-body">
                    {{ 'Register Kasda' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        {{-- buat proteksi cuma muncul di kasubid penerimaan --}}
        @if (Auth::user()->role == '1027' || Auth::user()->role == '1028')
            <div class="col-md-6">
                <div class="card card-info collapsed-card card-outline" id="daiana">
                    <div class="card-body">
                        {{ 'Daiana' }}
                        <a class="card-block stretched-link" href="#">

                        </a>
                        <i class="fa fa-chevron-right float-end mt-2"></i>

                    </div>
                </div>
            </div>
        @endif
    </div>


    @include('skpd.laporan_bendahara_penerimaan.modal1')
    @include('skpd.laporan_bendahara_penerimaan.modal2')

    <div id="modal_register_kasda" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label for="labelcetak" id="labelcetak">REGISTER KASDA</label></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Pilihan --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="pilihan_keseluruhan_register" value="2">
                                <label class="form-check-label" for="pilihan">Keseluruhan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="pilihan_skpd_register" value="3">
                                <label class="form-check-label" for="pilihan">SKPD</label>
                            </div>
                        </div>
                    </div>
                    {{-- SKPD --}}
                    <div class="mb-3 row" id="pilih_skpd_register">
                        <label for="" class="form-label">SKPD</label>
                        <div class="col-md-6">
                            <select class="form-control select2-register_kasda" style=" width: 100%;" id="skpd_register">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_skpd as $skpd)
                                    <option value="{{ $skpd->kd_skpd }}">{{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- PERIODE --}}
                    <div class="mb-3 row">
                        <label for="" class="form-label">PERIODE</label>
                        <div class="col-md-6">
                            <input type="date" class="form-control" id="periode1_register">
                        </div>
                        <div class="col-md-6">
                            <input type="date" class="form-control" id="periode2_register">
                        </div>
                    </div>
                    {{-- BERDASARKAN SKPD --}}
                    <div class="mb-3 row">
                        <label for="" class="form-label" style="text-align: center">Berdasarkan SKPD</label>
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md berdasarkan_skpd"
                                data-jenis="pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md berdasarkan_skpd"
                                data-jenis="layar">Layar</button>
                            <button type="button" class="btn btn-success btn-md berdasarkan_skpd"
                                data-jenis="excel">Excel</button>
                        </div>
                    </div>
                    {{-- BERDASARKAN KASDA --}}
                    <div class="mb-3 row">
                        <label for="" class="form-label" style="text-align: center">Berdasarkan KASDA</label>
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md berdasarkan_kasda"
                                data-jenis="pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md berdasarkan_kasda"
                                data-jenis="layar">Layar</button>
                            <button type="button" class="btn btn-success btn-md berdasarkan_kasda"
                                data-jenis="excel">Excel</button>
                        </div>
                    </div>
                    {{-- CETAKAN DETAIL PENERIMAAN --}}
                    <div class="mb-3 row">
                        <label for="" class="form-label" style="text-align: center">CETAKAN DETAIL
                            PENERIMAAN</label>
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md detail_penerimaan"
                                data-jenis="pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md detail_penerimaan"
                                data-jenis="layar">Layar</button>
                            <button type="button" class="btn btn-success btn-md detail_penerimaan"
                                data-jenis="excel">Excel</button>
                        </div>
                    </div>
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button type="button" style="float:right" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Daiana --}}

    <div id="modal_daiana" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><label for="labelcetak" id="labelcetak">DAIANA</label></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Pilihan --}}
                    <div class="mb-3 row" id="row-hidden">
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptionsdaiana"
                                    id="pilihan_keseluruhan_daiana" value="2">
                                <label class="form-check-label" for="pilihan">Keseluruhan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptionsdaiana"
                                    id="pilihan_skpd_daiana" value="3">
                                <label class="form-check-label" for="pilihan">SKPD</label>
                            </div>
                        </div>
                    </div>
                    {{-- SKPD --}}
                    <div class="mb-3 row" id="pilih_skpd_daiana">
                        <label for="" class="form-label">SKPD</label>
                        <div class="col-md-6">
                            <select class="form-control select2-daiana" style=" width: 100%;" id="skpd_daiana">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_skpd as $skpd)
                                    <option value="{{ $skpd->kd_skpd }}">{{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- PERIODE --}}
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="pa_daiana" class="form-label">Pengguna Anggaran</label>
                            <select class="form-control select2-pa_daiana" style=" width: 100%;" id="pa_daiana"
                                name="pa_daiana">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($pa_bpkad as $ttd)
                                    <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tanggal1_2_2" class="form-label">Periode</label>
                            <input type="date" class="form-control" id="periode1_daiana">
                        </div>
                        <div class="col-md-3">
                            <label for="tanggal2_2" class="form-label">&nbsp;</label>
                            <input type="date" class="form-control" id="periode2_daiana">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6" id="ttd">
                            <label for="tgl_daiana" class="form-label">Tanggal TTD</label>
                            <input type="date" id="tgl_daiana" name="tgl_daiana" class="form-control">
                        </div>
                        <div class="col-md-6" id="spasi1">
                            <label for="spasi_daiana" class="form-label">Spasi</label>
                            <input type="number" value="1" min="1" class="form-control" id="spasi_daiana"
                                name="spasi_daiana">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="" class="form-label" style="text-align: center"></label>
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger btn-md daiana" data-jenis="pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md daiana" data-jenis="layar">Layar</button>
                            <button type="button" class="btn btn-success btn-md daiana"
                                data-jenis="excel">Excel</button>
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--  END Daiana --}}

@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2-modal').select2({
                dropdownParent: $('#modal_cetak'),
                theme: 'bootstrap-5'
            });

            $('.select2-modal2').select2({
                dropdownParent: $('#modal_cetak2'),
                theme: 'bootstrap-5'
            });

            $('.select2-register_kasda').select2({
                dropdownParent: $('#modal_register_kasda .modal-content'),
                theme: 'bootstrap-5'
            });

            $('.select2-daiana').select2({
                dropdownParent: $('#modal_daiana .modal-content'),
                theme: 'bootstrap-5'
            });

            $('.select2-pa_daiana').select2({
                dropdownParent: $('#modal_daiana .modal-content'),
                theme: 'bootstrap-5'
            });



        });
        let jenis_skpd = "{{ substr(Auth::user()->kd_skpd, 18, 4) }}";
        let jenis
        if (jenis_skpd == '0000') {
            jenis = 'skpd';
        } else {
            jenis = 'unit';
        }

        let modal

        $('#lapbku').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak').modal('show');
            $('#modal_cetak2').modal('hide');
            $('#modal_register_kasda').modal('hide');
            $('#modal_daiana').modal('hide');
            $("#labelcetak").html("Buku Penerimaan dan Pengeluaran");
            document.getElementById('jenisanggaran').hidden = true; // Hide
            document.getElementById('jenis1').hidden = false; // Hide
            document.getElementById('spasi1').hidden = false; // Hide
            document.getElementById('tgl_ttd1').hidden = false; // Hide
            document.getElementById('bendahara1').hidden = false; // Hide
            document.getElementById('pa_kpa1').hidden = false; // Hide
            cari_skpd(kd_skpd, jenis);
            modal = 1;
        });

        $('#lapspj').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak').modal('show');
            $('#modal_cetak2').modal('hide');
            $('#modal_register_kasda').modal('hide');
            $('#modal_daiana').modal('hide');
            $("#labelcetak").html("SPJ Pendapatan");
            cari_skpd(kd_skpd, jenis);
            document.getElementById('jenisanggaran').hidden = false; // Hide
            document.getElementById('jenis1').hidden = false; // Hide
            document.getElementById('spasi1').hidden = false; // Hide
            document.getElementById('tgl_ttd1').hidden = false; // Hide
            document.getElementById('bendahara1').hidden = false; // Hide
            document.getElementById('pa_kpa1').hidden = false; // Hide
            modal = 1;
        });
        // cari skpd/org
        $('#lapsetoran').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak2').modal('show');
            $('#modal_cetak').modal('hide');
            $('#modal_register_kasda').modal('hide');
            $('#modal_daiana').modal('hide');
            $("#labelcetak2").html("Cek Buku Setoran");
            cari_skpd2(kd_skpd, jenis);
            document.getElementById('jenisanggaran2').hidden = true; // Hide
            document.getElementById('jenis1').hidden = true; // Hide
            document.getElementById('spasi_2').hidden = false; // Hide
            document.getElementById('tgl_ttd1').hidden = true; // Hide
            document.getElementById('tipe1').hidden = false; // Hide
            document.getElementById('rekening1').hidden = false; // Hide
            document.getElementById('bendahara1').hidden = true; // Hide
            document.getElementById('pa_kpa1').hidden = true; // Hide
            modal = 2;

        });

        $('#laprincian').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak2').modal('show');
            $('#modal_cetak').modal('hide');
            $('#modal_register_kasda').modal('hide');
            $('#modal_daiana').modal('hide');
            $("#labelcetak2").html("BP Sub Rincian Objek");
            cari_skpd2(kd_skpd, jenis);

            document.getElementById('jenisanggaran2').hidden = false; // Hide
            document.getElementById('jenis1').hidden = true; // Hide
            document.getElementById('spasi1').hidden = true; // Hide
            document.getElementById('tgl_ttd1').hidden = false; // Hide
            document.getElementById('tipe1').hidden = true; // Hide
            document.getElementById('rekening1').hidden = true; // Hide
            document.getElementById('bendahara1').hidden = false; // Hide
            document.getElementById('pa_kpa1').hidden = false; // Hide
            modal = 2;

        });

        $('#register_kasda').on('click', function() {
            $('#modal_register_kasda').modal('show');
            $('#pilih_skpd_register').hide();
            // $("#labelcetak").html("Buku Penerimaan dan Pengeluaran");
            // document.getElementById('jenisanggaran').hidden = true; // Hide
            // document.getElementById('jenis1').hidden = false; // Hide
            // document.getElementById('spasi1').hidden = false; // Hide
            // document.getElementById('tgl_ttd1').hidden = false; // Hide
            // document.getElementById('bendahara1').hidden = false; // Hide
            // document.getElementById('pa_kpa1').hidden = false; // Hide
            // cari_skpd(kd_skpd, jenis);
            // modal = 1;
        });

        $('#daiana').on('click', function() {
            $('#modal_daiana').modal('show');
            $('#pilih_skpd_daiana').hide();
            // $("#labelcetak").html("Buku Penerimaan dan Pengeluaran");
            // document.getElementById('jenisanggaran').hidden = true; // Hide
            // document.getElementById('jenis1').hidden = false; // Hide
            // document.getElementById('spasi1').hidden = false; // Hide
            // document.getElementById('tgl_ttd1').hidden = false; // Hide
            // document.getElementById('bendahara1').hidden = false; // Hide
            // document.getElementById('pa_kpa1').hidden = false; // Hide
            // cari_skpd(kd_skpd, jenis);
            // modal = 1;
        });

        $('#pilihan_keseluruhan_register').on('click', function() {
            $('#pilih_skpd_register').hide();
        });

        $('#pilihan_skpd_register').on('click', function() {
            $('#pilih_skpd_register').show();
        });

        $('#pilihan_keseluruhan_daiana').on('click', function() {
            $('#pilih_skpd_daiana').hide();
        });

        $('#pilihan_skpd_daiana').on('click', function() {
            $('#pilih_skpd_daiana').show();
        });



        $('input:radio[name="inlineRadioOptions"]').change(function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            if ($(this).val() == 'skpd') {
                cari_skpd(kd_skpd, 'skpd')
            } else {
                cari_skpd(kd_skpd, 'unit')
            }
        });

        $('input:radio[name="inlineRadioOptionsdaiana"]').change(function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            if ($(this).val() == 'skpd') {
                cari_skpd(kd_skpd, 'skpd')
            } else {
                cari_skpd(kd_skpd, 'unit')
            }
        });

        function cari_skpd(kd_skpd, jenis) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara_penerimaan.skpd') }}",
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
                url: "{{ route('skpd.laporan_bendahara_penerimaan.skpd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    jenis: jenis
                },
                success: function(data) {
                    $('#kd_skpd_2').empty();
                    $('#kd_skpd_2').append(
                        `<option value="" disabled selected>Pilih SKPD</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_skpd_2').append(
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
            cari_rekening(kd_skpd);
        });

        $('#kd_skpd_2').on('select2:select', function() {
            let kd_skpd = this.value;
            cari_bendahara2(kd_skpd);
            cari_pakpa2(kd_skpd);
            cari_rekening(kd_skpd);
        });

        function cari_bendahara(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara_penerimaan.bendahara') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#bendahara').empty();
                    $('#bendahara').append(
                        `<option value="" disabled selected>Pilih Bendahara Penerimaan</option>`);
                    $.each(data, function(index, data) {
                        $('#bendahara').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }

        function cari_bendahara2(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara_penerimaan.bendahara') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#bendahara_2').empty();
                    $('#bendahara_2').append(
                        `<option value="" disabled selected>Pilih Bendahara Penerimaan</option>`);
                    $.each(data, function(index, data) {
                        $('#bendahara_2').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }

        function cari_pakpa(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara_penerimaan.pakpa') }}",
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

        function cari_pakpa2(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara_penerimaan.pakpa') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#pa_kpa_2').empty();
                    $('#pa_kpa_2').append(
                        `<option value="" disabled selected>Pilih PA/KPA</option>`);
                    $.each(data, function(index, data) {
                        $('#pa_kpa_2').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }

        function cari_rekening(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara_penerimaan.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#rekening3').empty();
                    $('#rekening3').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#rekening3').append(
                            `<option value="${data.kd_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            })
        }

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
            let jenis_cetak
            let jenis_print
            let spasi
            let tgl_ttd
            let bendahara
            let pa_kpa
            let kd_skpd
            let jenis_cetakan
            let tanggal1
            let tanggal2
            let jns_anggaran
            let rekening
            let tipe
            let format
            if (modal == 1) {
                spasi = document.getElementById('spasi').value;
                tgl_ttd = document.getElementById('tgl_ttd').value;
                bendahara = document.getElementById('bendahara').value;
                pa_kpa = document.getElementById('pa_kpa').value;
                kd_skpd = document.getElementById('kd_skpd').value;
                jenis_print = $(this).data("jenis");
                jenis_cetakan = document.getElementById('jenis_cetak').value;
                jenis_cetak = document.getElementById('labelcetak').textContent
                tanggal1 = document.getElementById('tanggal1').value;
                tanggal2 = document.getElementById('tanggal2').value;
                jns_anggaran = document.getElementById('jns_anggaran').value;
                format = document.getElementById('format').value;

                // alert validasi data
                if (!kd_skpd) {
                    alert('SKPD tidak boleh kosong!');
                    return;
                }
                // if (!bendahara) {
                //     alert('Bendahara Pengeluaran tidak boleh kosong!');
                //     return;
                // }
                if (!pa_kpa) {
                    alert("PA/KPA tidak boleh kosong!");
                    return;
                }
                if (!tanggal1) {
                    alert("Periode 1 tidak boleh kosong!");
                    return;
                }
                if (!tanggal2) {
                    alert("Periode 2 tidak boleh kosong!");
                    return;
                }
                if (!tgl_ttd) {
                    alert("Tanggal Penandatangan tidak boleh kosong!");
                    return;
                }
                if (jenis_cetak == 'SPJ Pendapatan') {
                    if (!jns_anggaran) {
                        alert("Jenis Anggaran tidak boleh kosong!");
                        return;
                    }
                }
            } else {
                spasi = document.getElementById('spasi_2').value;
                tgl_ttd = document.getElementById('tgl_ttd_2').value;
                bendahara = document.getElementById('bendahara_2').value;
                pa_kpa = document.getElementById('pa_kpa_2').value;
                kd_skpd = document.getElementById('kd_skpd_2').value;
                jenis_print = $(this).data("jenis_2");
                jenis_cetakan = document.getElementById('jenis_cetak_2').value;
                jenis_cetak = document.getElementById('labelcetak2').textContent;
                tanggal1 = document.getElementById('tanggal1_2').value;
                tanggal2 = document.getElementById('tanggal2_2').value;
                jns_anggaran = document.getElementById('jns_anggaran_2').value;
                rekening = document.getElementById('rekening').value;
                rekening3 = document.getElementById('rekening3').value;
                tipe = document.getElementById('tipe').value;

                // alert validasi data
                if (!kd_skpd) {
                    alert('SKPD tidak boleh kosong!');
                    return;
                }
                // if (!bendahara) {
                //     alert('Bendahara Pengeluaran tidak boleh kosong!');
                //     return;
                // }
                if (!pa_kpa) {
                    alert("PA/KPA tidak boleh kosong!");
                    return;
                }
                if (!tanggal1) {
                    alert("Periode 1 tidak boleh kosong!");
                    return;
                }
                if (!tanggal2) {
                    alert("Periode 2 tidak boleh kosong!");
                    return;
                }
                if (!tgl_ttd) {
                    alert("Tanggal Penandatangan tidak boleh kosong!");
                    return;
                }
                if (jenis_cetak == "BP Sub Rincian Objek") {
                    if (!rekening3) {
                        alert('Rekening tidak boleh kosong!');
                        return;
                    }
                } else {
                    if (!rekening) {
                        alert("Rekening tidak boleh kosong!");
                        return;
                    }
                }

                if (!tipe) {
                    alert("Tipe tidak boleh kosong!");
                    return;
                }



            }

            // subrincian objek


            if (jenis_cetak == 'Buku Penerimaan dan Pengeluaran') {
                let url = new URL("{{ route('skpd.laporan_bendahara_penerimaan.cetak_buku_penerimaan_penyetoran') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("tanggal1", tanggal1);
                searchParams.append("tanggal2", tanggal2);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("jenis_cetakan", jenis_cetakan);
                searchParams.append("cetak", jns_cetak);
                searchParams.append("format", format);


                window.open(url.toString(), "_blank");

            } else if (jenis_cetak == 'SPJ Pendapatan') {
                let url = new URL("{{ route('skpd.laporan_bendahara_penerimaan.cetak_spj_pendapatan') }}");
                let searchParams = url.searchParams;
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("tanggal1", tanggal1);
                searchParams.append("tanggal2", tanggal2);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("jenis_cetakan", jenis_cetakan);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("cetak", jns_cetak);

                window.open(url.toString(), "_blank");

            } else if (jenis_cetak == 'Cek Buku Setoran') {
                let url = new URL("{{ route('skpd.laporan_bendahara_penerimaan.cetak_buku_setoran') }}");
                let searchParams = url.searchParams;
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("tanggal1", tanggal1);
                searchParams.append("tanggal2", tanggal2);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("rekening", rekening);
                searchParams.append("tipe", tipe);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                searchParams.append("spasi", spasi);

                window.open(url.toString(), "_blank");

            } else if (jenis_cetak == 'BP Sub Rincian Objek') {
                let url = new URL("{{ route('skpd.laporan_bendahara_penerimaan.cetak_bp_sub_rincian_objek') }}");
                let searchParams = url.searchParams;
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("tanggal1", tanggal1);
                searchParams.append("tanggal2", tanggal2);
                searchParams.append("rekening", rekening3);
                searchParams.append("jns_anggaran", jns_anggaran);
                searchParams.append("tipe", tipe);
                searchParams.append("jenis_print", jenis_print);
                searchParams.append("cetak", jns_cetak);
                searchParams.append("spasi", spasi);

                window.open(url.toString(), "_blank");

            } else {
                alert('-' + jenis_cetak + '- Tidak ada url');

            }
        }

        $('.berdasarkan_skpd').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_register')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_register').checked;

            if (keseluruhan == false && skpd == false) {
                alert('Silahkan Pilih Keseluruhan atau SKPD');
                return;
            }

            let kd_skpd = document.getElementById('skpd_register').value;
            let periode1 = document.getElementById('periode1_register').value;
            let periode2 = document.getElementById('periode2_register').value;
            let jenis_print = $(this).data("jenis");

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }

            let pilihan = '';
            if (keseluruhan) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            }

            if (!periode1 && !periode2) {
                alert('Periode tidak boleh kosong!');
                return;
            }

            let url = new URL("{{ route('skpd.laporan_bendahara_penerimaan.berdasarkan_skpd') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.berdasarkan_kasda').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_register')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_register').checked;

            if (keseluruhan == false && skpd == false) {
                alert('Silahkan Pilih Keseluruhan atau SKPD');
                return;
            }

            let kd_skpd = document.getElementById('skpd_register').value;
            let periode1 = document.getElementById('periode1_register').value;
            let periode2 = document.getElementById('periode2_register').value;
            let jenis_print = $(this).data("jenis");

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }

            let pilihan = '';
            if (keseluruhan) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            }

            if (!periode1 && !periode2) {
                alert('Periode tidak boleh kosong!');
                return;
            }

            let url = new URL("{{ route('skpd.laporan_bendahara_penerimaan.berdasarkan_kasda') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.detail_penerimaan').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_register')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_register').checked;

            if (keseluruhan == false && skpd == false) {
                alert('Silahkan Pilih Keseluruhan atau SKPD');
                return;
            }

            let kd_skpd = document.getElementById('skpd_register').value;
            let periode1 = document.getElementById('periode1_register').value;
            let periode2 = document.getElementById('periode2_register').value;
            let jenis_print = $(this).data("jenis");

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }

            let pilihan = '';
            if (keseluruhan) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            }

            if (!periode1 && !periode2) {
                alert('Periode tidak boleh kosong!');
                return;
            }

            let url = new URL("{{ route('skpd.laporan_bendahara_penerimaan.detail_penerimaan') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.daiana').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_daiana')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_daiana').checked;

            if (keseluruhan == false && skpd == false) {
                alert('Silahkan Pilih Keseluruhan atau SKPD');
                return;
            }
            let pa = document.getElementById('pa_daiana').value;
            let kd_skpd = document.getElementById('skpd_daiana').value;
            let periode1 = document.getElementById('periode1_daiana').value;
            let periode2 = document.getElementById('periode2_daiana').value;
            let tgl = document.getElementById('tgl_daiana').value;
            let jenis_print = $(this).data("jenis");
            let spasi = document.getElementById('spasi_daiana').value;

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }

            let pilihan = '';
            if (keseluruhan) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            }

            if (!pa) {
                alert('Penandatangan belum dipililh!');
                return;
            }

            if (!periode1 && !periode2) {
                alert('Periode tidak boleh kosong!');
                return;
            }

            let url = new URL("{{ route('skpd.laporan_bendahara_penerimaan.daiana') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("pa", pa);
            searchParams.append("tgl", tgl);
            searchParams.append(
                "periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("kd_skpd",
                kd_skpd);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("spasi", spasi);
            window.open(url.toString(), "_blank");
        });
    </script>
@endsection
