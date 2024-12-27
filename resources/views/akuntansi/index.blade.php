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
            <div class="card card-info collapsed-card card-outline" id="lkonsol">
                <div class="card-body">
                    {{ 'Laporan Konsolidasi' }}
                    <a class="card-block stretched-link" href="{{ route('laporan_akuntansi.konsolidasi.konsolidasi') }}">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lkeu">
                <div class="card-body">
                    {{ 'Laporan Keuangan' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lbb">
                <div class="card-body">
                    {{ 'Buku Besar' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="lrba">
                <div class="card-body">
                    {{ 'Rekon BA' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

@include('akuntansi.modal.bukubesar')
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            cari_rek6();
            cari_skpdbb();

            $(".select_lbb").select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modal_cetak_lbb .modal-content'),
                
            });

        });

        let jenis_skpd = "{{ substr(Auth::user()->kd_skpd, 18, 4) }}";
        let jenis
        if (jenis_skpd == '0000') {
            jenis = 'skpd';
        } else {
            jenis = 'unit';
        }

         // onclick card start
        $('#lbb').on('click', function() {
            // let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak_lbb').modal('show');
            $("#labelcetak_semester").html("Cetak Buku Besar");
            // document.getElementById('row-hidden').hidden = true; // Hide
        });
        function cari_rek6() {
            $.ajax({
                url: "{{ route('laporan_akuntansi.rek6') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    // console.log(data);
                    $('#rek6').empty();
                    $('#rek6').append(
                        `<option value="" disabled selected>Pilih Rekening</option>`);
                    $.each(data, function(index, data) {
                        $('#rek6').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            })
        }
        function cari_skpdbb(kd_skpd, jenis) {
            $.ajax({
                url: "{{ route('laporan_akuntansi.skpd2') }}",
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
            let tanggal1                    = document.getElementById('tanggal1').value;
            let tanggal2                    = document.getElementById('tanggal2').value;
            let kd_skpd             = document.getElementById('kd_skpd').value;
            let rek6                    = document.getElementById('rek6').value;
            let labelcetak_semester      = document.getElementById('labelcetak_semester').textContent;
            // alert(labelcetak_semester)
            // PERINGATAN
                if (!tanggal1) {
                    alert('Tanggal Awal tidak boleh kosong!');
                    return;
                }
                if (!tanggal2) {
                    alert('Tanggal Akhir tidak boleh kosong!');
                    return;
                }
                if (!kd_skpd) {
                    alert('SKPD tidak boleh kosong!');
                    return;
                }
                if (!rek6) {
                    alert('Rekening tidak boleh kosong!');
                    return;
                }

            // SET CETAKAN
            if (labelcetak_semester == 'Cetak Buku Besar') {
                let url             = new URL("{{ route('laporan_akuntansi.cbb') }}");
                let searchParams    = url.searchParams;
                searchParams.append("tanggal1", tanggal1);
                searchParams.append("tanggal2", tanggal2);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("rek6", rek6);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            
            }else{
                alert('-' + jns_cetak + '- Tidak ada cetakan');
            }
        }
    </script>
@endsection
