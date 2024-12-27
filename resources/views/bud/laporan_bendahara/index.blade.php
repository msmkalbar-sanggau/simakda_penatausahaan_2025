@extends('template.app')
@section('title', 'Laporan bendahara | SIMAKDA')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ 'Laporan Bendahara Umum Daerah' }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'App' }}</a></li>
                        <li class="breadcrumb-item">{{ 'Laporan Bendahara Umum Daerah' }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="realisasi_pendapatan">
                <div class="card-body">
                    {{ 'Realisasi Pendapatan' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="pembantu_penerimaan">
                <div class="card-body">
                    {{ 'Buku Kas Pembantu Penerimaan' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="bku">
                <div class="card-body">
                    {{ 'BKU (B IX)' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="pajak_daerah">
                <div class="card-body">
                    {{ 'Penerimaan Pajak Daerah' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="rekap_gaji">
                <div class="card-body">
                    {{ 'Rekap Gaji' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="buku_besar_kasda">
                <div class="card-body">
                    {{ 'Buku Besar Kasda' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="pembantu_pengeluaran">
                <div class="card-body">
                    {{ 'Buku Kas Pembantu Pengeluaran' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="retribusi">
                <div class="card-body">
                    {{ 'Retribusi' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="register_cp">
                <div class="card-body">
                    {{ 'Register CP' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        {{-- <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="potongan_pajak2">
                <div class="card-body">
                    {{ 'Daftar Potongan Pajak' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div> --}}

        {{-- created by elvara --}}
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="potongan_pajak">
                <div class="card-body">
                    {{ 'Daftar Potongan Pajak' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="daftar_pengeluaran">
                <div class="card-body">
                    {{ 'Daftar Pengeluaran' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="daftar_penerimaan">
                <div class="card-body">
                    {{ 'Daftar Penerimaan' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="penerimaan_non_pendapatan">
                <div class="card-body">
                    {{ 'Penerimaan Non Pendapatan' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="transfer_dana">
                <div class="card-body">
                    {{ 'Transfer Dana' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="restitusi">
                <div class="card-body">
                    {{ 'Restitusi' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="rth">
                <div class="card-body">
                    {{ 'RTH' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="pengeluaran_non_sp2d">
                <div class="card-body">
                    {{ 'Buku Pembantu Pengeluaran Non SP2D' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="dth">
                <div class="card-body">
                    {{ 'DTH' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="koreksi_penerimaan">
                <div class="card-body">
                    {{ 'Register Koreksi Penerimaan' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="harian_kasda">
                <div class="card-body">
                    {{ 'Kas Harian Kasda' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="uyhd">
                <div class="card-body">
                    {{ 'UYHD' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="koreksi_pengeluaran">
                <div class="card-body">
                    {{ 'Koreksi Pengeluaran' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="koreksi_penerimaan2">
                <div class="card-body">
                    {{ 'Koreksi Penerimaan' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="register_sp2d">
                <div class="card-body">
                    {{ 'Register SP2D' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info collapsed-card card-outline" id="cek_data_akuntansi">
                <div class="card-body">
                    {{ 'Cek Data Akuntansi' }}
                    <a class="card-block stretched-link" href="#">

                    </a>
                    <i class="fa fa-chevron-right float-end mt-2"></i>

                </div>
            </div>
        </div>
    </div>

    {{-- modal cetak realisasi pendapatan --}}
    @include('bud.laporan_bendahara.modal.realisasi_pendapatan')
    {{-- modal cetak realisasi pendapatan  --}}

    {{-- modal cetak buku kas pembantu penerimaan --}}
    @include('bud.laporan_bendahara.modal.buku_kas_pembantu_penerimaan')
    {{-- modal cetak buku kas pembantu penerimaan --}}

    {{-- modal cetak bku (b ix) --}}
    @include('bud.laporan_bendahara.modal.bku')
    {{-- modal cetak bku (b ix) --}}

    {{-- modal cetak penerimaan pajak daerah --}}
    @include('bud.laporan_bendahara.modal.penerimaan_pajak_daerah')
    {{-- modal cetak penerimaan pajak daerah --}}

    {{-- modal cetak rekap gaji --}}
    @include('bud.laporan_bendahara.modal.rekap_gaji')
    {{-- modal cetak rekap gaji  --}}

    {{-- modal buku besar kasda --}}
    @include('bud.laporan_bendahara.modal.buku_besar_kasda')
    {{-- modal buku besar kasda  --}}

    {{-- modal cetak buku kas pembantu pengeluaran --}}
    @include('bud.laporan_bendahara.modal.buku_kas_pembantu_pengeluaran')
    {{-- modal cetak buku kas pembantu pengeluaran --}}

    {{-- modal cetak restibusi --}}
    @include('bud.laporan_bendahara.modal.retribusi')
    {{-- modal cetak restibusi --}}

    {{-- modal cetak register cp --}}
    @include('bud.laporan_bendahara.modal.register_cp')
    {{-- modal cetak register cp  --}}

    {{-- modal cetak daftar potongan pajak --}}
    {{-- @include('bud.laporan_bendahara.modal.potongan_pajak2') --}}
    {{-- modal cetak daftar potongan pajak  --}}

    {{-- create by elvara --}}
    {{-- modal cetak daftar potongan pajak --}}
    @include('bud.laporan_bendahara.modal.potongan_pajak')
    {{-- modal cetak daftar potongan pajak  --}}

    {{-- modal cetak daftar pengeluaran --}}
    @include('bud.laporan_bendahara.modal.daftar_pengeluaran')
    {{-- modal cetak daftar pengeluaran  --}}

    {{-- modal cetak daftar penerimaan --}}
    @include('bud.laporan_bendahara.modal.daftar_penerimaan')
    {{-- modal cetak daftar penerimaan --}}

    {{-- modal cetak penerimaan non pendapatan --}}
    @include('bud.laporan_bendahara.modal.penerimaan_non_pendapatan')
    {{-- modal cetak penerimaan non pendapatan --}}

    {{-- modal cetak transfer dana --}}
    @include('bud.laporan_bendahara.modal.transfer_dana')
    {{-- modal cetak transfer dana --}}

    {{-- modal cetak restitusi --}}
    @include('bud.laporan_bendahara.modal.restitusi')
    {{-- modal cetak restitusi --}}

    {{-- modal cetak rth --}}
    @include('bud.laporan_bendahara.modal.rth')
    {{-- modal cetak rth --}}

    {{-- modal cetak buku pembantu pengeluaran non sp2d --}}
    @include('bud.laporan_bendahara.modal.pengeluaran_non_sp2d')
    {{-- modal cetak buku pembantu pengeluaran non sp2d --}}

    {{-- modal cetak dth --}}
    @include('bud.laporan_bendahara.modal.dth')
    {{-- modal cetak dth --}}

    {{-- modal cetak register koreksi penerimaan --}}
    @include('bud.laporan_bendahara.modal.koreksi_penerimaan')
    {{-- modal cetak register koreksi penerimaan --}}

    {{-- modal cetak kas harian kasda --}}
    @include('bud.laporan_bendahara.modal.harian_kasda')
    {{-- modal cetak kas harian kasda --}}

    {{-- modal cetak uyhd --}}
    @include('bud.laporan_bendahara.modal.uyhd')
    {{-- modal cetak uyhd --}}

    {{-- modal cetak koreksi pengeluaran --}}
    @include('bud.laporan_bendahara.modal.koreksi_pengeluaran')
    {{-- modal cetak koreksi pengeluaran --}}

    {{-- modal cetak koreksi penerimaan --}}
    @include('bud.laporan_bendahara.modal.koreksi_penerimaan2')
    {{-- modal cetak koreksi penerimaan --}}

    {{-- modal cetak register_sp2d --}}
    @include('bud.laporan_bendahara.modal.register_sp2d')
    {{-- modal cetak register_sp2d  --}}

    {{-- modal cek data akuntansi --}}
    @include('bud.laporan_bendahara.modal.cek_data_akuntansi')
    {{-- modal cek data akuntansi  --}}
@endsection
@section('js')
    @include('bud.laporan_bendahara.js.index')
@endsection
