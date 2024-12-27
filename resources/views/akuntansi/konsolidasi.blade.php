@extends('template.app')
@section('title', 'Laporan Keuangan | SIMAKDA')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ 'Laporan Konsolidasi' }}</h4>

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
            <div class="card card-info collapsed-card card-outline" id="lra">
                <div class="card-body">
                    {{ 'Laporan Realisasi Anggaran (SAP, DJPK, P77)' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lraperda">
                <div class="card-body">
                    {{ 'Laporan Perda' }}
                    <a class="card-block stretched-link" href="{{ route('laporan_akuntansi.perda') }}">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lo">
                <div class="card-body">
                    {{ 'Laporan Operasional' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lraperkada">
                <div class="card-body">
                    {{ 'LRA PERKDA' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lpe">
                <div class="card-body">
                    {{ 'LPE' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="neraca">
                <div class="card-body">
                    {{ 'NERACA' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lpsal">
                <div class="card-body">
                    {{ 'LPSAL' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lak">
                <div class="card-body">
                    {{ 'LAK' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

   
    {{-- modal cetak SPJ  --}}
{{-- @include('akuntansi.modal.lrasap') --}}
@include('akuntansi.modal.lrasemester')
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

            $(".select2").select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modal_cetak_semester .modal-content'),
                
            });
            $(".select_neraca").select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modal_cetak_neraca .modal-content'),
                
            });
            // hidden
            document.getElementById('baris_skpd').hidden = true; // Hide
            document.getElementById('baris_periode1').hidden = true; // Hide
            document.getElementById('baris_periode2').hidden = true; // Hide
            document.getElementById('baris_bulan').hidden = true; // Hide
            let typeu = "{{Auth::user()->is_admin}}";
            if (typeu == '1') {
                 document.getElementById('pilihan0').hidden = false;
                 document.getElementById('lraperda').hidden = false;
                 document.getElementById('lpsal').hidden = false;
                 document.getElementById('lak').hidden = false;
            }else{
                document.getElementById('pilihan0').hidden = true;
                document.getElementById('lraperda').hidden = true;
                 document.getElementById('lpsal').hidden = true;
                 document.getElementById('lak').hidden = true;
            }
            // tambahclass()

            // hidden
            document.getElementById('baris_skpd_neraca').hidden = true; // Hide
            // tambahclass()
        });

    // function tambahclass(){
    //     $(".select2").select2({
    //             containerCssClass: "pink",
    //             theme: 'bootstrap-5',
    //             templateResult: function (data, container) {
    //                 if (data.id==1) {
    //                     $(container).addClass($(data.element).attr("class"));
    //                 }
    //                 return data.text;
    //             }
    //             });
    // }
        

    // onclick card start
        $('#lra').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak_semester').modal('show');
            $("#labelcetak_semester").html("Cetak LRA");
            // document.getElementById('row-hidden').hidden = true; // Hide
        });

        $('#neraca').on('click', function() {
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak_neraca').modal('show');
            $("#labelcetak_semester").html("Cetak NERACA");
            // document.getElementById('row-hidden').hidden = true; // Hide
        });


    // onclick card end

        // cari skpd/org
        $('input:radio[name="inlineRadioOptions"]').change(function() {
            cari_ttd();
            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            if ($(this).val() == 'keseluruhan') {
                document.getElementById('baris_skpd').hidden = true; // Hide
            }else if ($(this).val() == 'skpd') {
                cari_skpd('skpd')
                document.getElementById('baris_skpd').hidden = false; // show
            } else {
                cari_skpd('unit')
                document.getElementById('baris_skpd').hidden = false; // show
            }
        });

        $('input:radio[name="pilihan_neraca"]').change(function() {

            let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            if ($(this).val() == 'keseluruhan') {
                document.getElementById('baris_skpd_neraca').hidden = true; // Hide
            }else if ($(this).val() == 'skpd') {
                cari_skpd('skpd')
                document.getElementById('baris_skpd_neraca').hidden = false; // show
            } else {
                cari_skpd('unit')
                document.getElementById('baris_skpd_neraca').hidden = false; // show
            }
        });

         // cari periode
         $('input:radio[name="pilihanperiode"]').change(function() {
            if ($(this).val() == 'tahun') {
                document.getElementById('baris_periode1').hidden = true; // Hide
                document.getElementById('baris_periode2').hidden = true; // Hide
                document.getElementById('baris_bulan').hidden = true; // Hide
            }else if ($(this).val() == 'periode') {
                document.getElementById('baris_periode1').hidden = false; // show
                document.getElementById('baris_periode2').hidden = false; // Hide
                document.getElementById('baris_bulan').hidden = true; // Hide
            } else {
                document.getElementById('baris_periode1').hidden = true; // show
                document.getElementById('baris_periode2').hidden = true; // Hide
                document.getElementById('baris_bulan').hidden = false; // Hide
            }
        });

        function cari_skpd(jenis) {
            $.ajax({
                url: "{{ route('laporan_akuntansi.skpd') }}",
                type: "POST",
                dataType: 'json',
                data: {
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
                    $('#kd_skpd_neraca').empty();
                    $('#kd_skpd_neraca').append(
                        `<option value="" disabled selected>Pilih SKPD</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_skpd_neraca').append(
                            `<option value="${data.kd_skpd}" data-nama="${data.nm_skpd}">${data.kd_skpd} | ${data.nm_skpd}</option>`
                        );
                    })
                }
            })
        }

        


        function cari_ttd() {
            $.ajax({
                url: "{{ route('laporan_akuntansi.ttd') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $('#ttd').empty();
                    $('#ttd').append(
                        `<option value="" disabled selected>Pilih Tanda Tangan</option>
                        <option value="0">Tanpa Tanda Tangan</option>`);
                    $.each(data, function(index, data) {
                        $('#ttd').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
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

            // GET DATA
            let kd_skpd                  = document.getElementById('kd_skpd').value;
            let bulan                    = document.getElementById('bulan').value;
            let tanggal1                 = document.getElementById('tanggal1').value;
            let tanggal2                 = document.getElementById('tanggal2').value;
            let tgl_ttd                  = document.getElementById('tgl_ttd').value;
            let ttd                      = document.getElementById('ttd').value;
            let jns_anggaran             = document.getElementById('jns_anggaran').value;
            let jenis_data               = document.getElementById('jenis_data').value;
            let jns_rincian              = document.getElementById('jns_rincian').value;
            // let pilihkonversi            = document.getElementById('pilihkonversi').value;
            let pilihakumulsai           = document.getElementById('pilihakumulsai').value;
            let format                   = document.getElementById('format').value;
            let labelcetak_semester      = document.getElementById('labelcetak_semester').textContent;
            let periodebulan             = $('input:radio[name="pilihanperiode"]:checked').val();
            let skpdunit                 = $('input:radio[name="inlineRadioOptions"]:checked').val();
            
            // PERINGATAN
                if (!ttd) {
                    alert('Tanda Tangan tidak boleh kosong!');
                    return;
                }
                if (!tgl_ttd) {
                    alert('Tanggal Tanda Tangan tidak boleh kosong!');
                    return;
                }
                // if (!pilihkonversi) {
                //     alert('Pilihan Konversi tidak boleh kosong!');
                //     return;
                // }
                if (!pilihakumulsai) {
                    alert('Pilihan Akumulasi tidak boleh kosong!');
                    return;
                }
                if (!format) {
                    alert('Format tidak boleh kosong!');
                    return;
                }
                if (!jenis_data) {
                    alert('jenis Data tidak boleh kosong!');
                    return;
                }

            // SET CETAKAN
            if (labelcetak_semester == 'Cetak LRA') {
                let url             = new URL("{{ route('laporan_akuntansi.konsolidasi.cetak_lra') }}");
                let searchParams    = url.searchParams;
                searchParams.append("format", format);
                searchParams.append("tanggal1", tanggal1);
                searchParams.append("tanggal2", tanggal2);
                searchParams.append("ttd", ttd);
                searchParams.append("bulan", bulan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_data", jenis_data);
                searchParams.append("jenis_anggaran", jns_anggaran);
                searchParams.append("skpdunit", skpdunit);
                searchParams.append("pilihakumulsai", pilihakumulsai);
                searchParams.append("jns_rincian", jns_rincian);
                searchParams.append("cetak", jns_cetak);
                searchParams.append("periodebulan", periodebulan);
                window.open(url.toString(), "_blank");
            
            }else if (labelcetak_semester == 'Cetak NERACA') {
                let url             = new URL("{{ route('laporan_akuntansi.konsolidasi.cetak_lra') }}");
                let searchParams    = url.searchParams;
                searchParams.append("format", format);
                searchParams.append("tanggal1", tanggal1);
                searchParams.append("tanggal2", tanggal2);
                searchParams.append("ttd", ttd);
                searchParams.append("bulan", bulan);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis_data", jenis_data);
                searchParams.append("jenis_anggaran", jns_anggaran);
                searchParams.append("skpdunit", skpdunit);
                searchParams.append("pilihakumulsai", pilihakumulsai);
                searchParams.append("jns_rincian", jns_rincian);
                searchParams.append("cetak", jns_cetak);
                searchParams.append("periodebulan", periodebulan);
                window.open(url.toString(), "_blank");
            
            }else{
                alert('-' + jenis_cetak + '- Tidak ada cetakan');
            }
        }
    </script>
@endsection
