@extends('template.app')
@section('title', 'Laporan Keuangan | SIMAKDA')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ 'Laporan Perda' }}</h4>

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
            <div class="card card-info collapsed-card card-outline" id="i1">
                <div class="card-body">
                    {{ 'PERDA LAMP I.1' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="i1_ringkasan">
                <div class="card-body">
                    {{ 'PERDA LAMP I.1 RINGKASAN' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="i2">
                <div class="card-body">
                    {{ 'PERDA LAMP 1.2' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="i3_rincian">
                <div class="card-body">
                    {{ 'PERDA LAMP 1.3 RINCIAN' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="i4_urusan">
                <div class="card-body">
                    {{ 'PERDA LAMP 1.4 URUSAN' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="i6_piutang">
                <div class="card-body">
                    {{ 'PERDA LAMP 1.6 PIUTANG' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="i8_aset_tetap">
                <div class="card-body">
                    {{ 'PERDA LAMP 1.8 ASET TETAP' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="c_1">
                <div class="card-body">
                    {{ 'PERDA LAMP C 1 KESELARASAN' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="c_2">
                <div class="card-body">
                    {{ 'PERDA LAMP C 2' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="c_3">
                <div class="card-body">
                    {{ 'PERDA LAMP C 3' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

   
    {{-- modal cetak SPJ  --}}
{{-- @include('akuntansi.modal.lrasap') --}}
@include('akuntansi.modal.lraperda')
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

            $(".select_i4_urusan").select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modal_cetak_i4_urusan .modal-content'),
                
            });
            
        });


    // onclick card start
        $('#i4_urusan').on('click', function() {
            // let kd_skpd = "{{ $data_skpd->kd_skpd }}";
            $('#modal_cetak_i4_urusan').modal('show');
            $("#labelcetak_semester").html("Cetak I.4 URUSAN");
            // document.getElementById('row-hidden').hidden = true; // Hide
        });

        

        // function cari_skpd(jenis) {
        //     $.ajax({
        //         url: "{{ route('laporan_akuntansi.skpd') }}",
        //         type: "POST",
        //         dataType: 'json',
        //         data: {
        //             jenis: jenis
        //         },
        //         success: function(data) {
        //             $('#kd_skpd').empty();
        //             $('#kd_skpd').append(
        //                 `<option value="" disabled selected>Pilih SKPD</option>`);
        //             $.each(data, function(index, data) {
        //                 $('#kd_skpd').append(
        //                     `<option value="${data.kd_skpd}" data-nama="${data.nm_skpd}">${data.kd_skpd} | ${data.nm_skpd}</option>`
        //                 );
        //             })

        //         }
        //     })
        // }

        


        


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
            let bulan                    = document.getElementById('bulan').value;
            let tgl_ttd                  = document.getElementById('tgl_ttd').value;
            let jns_anggaran             = document.getElementById('jns_anggaran').value;
            let jenis                    = document.getElementById('jenis').value;
            let labelcetak_semester      = document.getElementById('labelcetak_semester').textContent;
            // alert(labelcetak_semester)
            // PERINGATAN
                if (!tgl_ttd) {
                    alert('Tanggal Tanda Tangan tidak boleh kosong!');
                    return;
                }
                if (!jenis) {
                    alert('jenis Data tidak boleh kosong!');
                    return;
                }
                if (!bulan) {
                    alert('Bulan tidak boleh kosong!');
                    return;
                }
                if (!jns_anggaran) {
                    alert('Jenis Anggaran tidak boleh kosong!');
                    return;
                }

            // SET CETAKAN
            if (labelcetak_semester == 'Cetak I.4 URUSAN') {
                let url             = new URL("{{ route('laporan_akuntansi.perda.cetak_i4_urusan') }}");
                let searchParams    = url.searchParams;
                searchParams.append("bulan", bulan);
                searchParams.append("tgl_ttd", tgl_ttd);
                searchParams.append("jenis", jenis);
                searchParams.append("jenis_anggaran", jns_anggaran);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            
            }else{
                alert('-' + jns_cetak + '- Tidak ada cetakan');
            }
        }
    </script>
@endsection
