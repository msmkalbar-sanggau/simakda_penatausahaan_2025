<div id="modal_daftar_pengeluaran" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">DAFTAR PENGELUARAN</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Pilih</label><br>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_semua_daftar_pengeluaran" value="1">
                            <label class="form-check-label" for="pilihan">Semua</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_skpd_daftar_pengeluaran" value="2">
                            <label class="form-check-label" for="pilihan">Per SKPD</label>
                        </div>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_periode_daftar_pengeluaran" value="1">
                            <label class="form-check-label" for="pilihan">Per Periode</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_unit_daftar_pengeluaran" value="3">
                            <label class="form-check-label" for="pilihan">Per Unit</label>
                        </div>
                    </div>
                </div>
                {{-- SKPD --}}
                <div class="mb-3 row" id="pilih_skpd_daftar_pengeluaran">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">SKPD</label>
                        <select class="form-control select2-daftar_pengeluaran" style=" width: 100%;"
                            id="kd_skpd_daftar_pengeluaran">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($daftar_org as $org)
                                <option value="{{ $org->kd_org }}" data-nama="{{ $org->nm_org }}">
                                    {{ $org->kd_org }} | {{ $org->nm_org }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="nm_skpd" class="form-label"></label>
                        <input type="text" style="border:none;background-color:white" class="form-control" readonly
                            id="nm_skpd_daftar_pengeluaran">
                    </div>
                </div>
                {{-- Unit --}}
                <div class="mb-3 row" id="pilih_unit_daftar_pengeluaran">
                    <div class="col-md-6">
                        <label for="kd_unit" class="form-label">Unit</label>
                        <select class="form-control select2-daftar_pengeluaran" style=" width: 100%;"
                            id="kd_unit_daftar_pengeluaran">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($daftar_skpd as $skpd)
                                <option value="{{ $skpd->kd_skpd }}" data-nama="{{ $skpd->nm_skpd }}">
                                    {{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="nm_unit" class="form-label"></label>
                        <input type="text" style="border:none;background-color:white" class="form-control" readonly
                            id="nm_unit_daftar_pengeluaran">
                    </div>
                </div>
                {{-- Penandatangan dan Jenis SP2D --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Kuasa BUD</label>
                        <select class="form-control select2-daftar_pengeluaran" style=" width: 100%;"
                            id="ttd_daftar_pengeluaran">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($bud as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="jenis_beban" class="form-label">Jenis Beban</label>
                        <select class="form-control select2-daftar_pengeluaran" style=" width: 100%;"
                            id="beban_daftar_pengeluaran">
                            <option value="0" selected>GAJI</option>
                            <option value="1">LS</option>
                            <option value="2">UP</option>
                            <option value="3">TU</option>
                            <option value="4">GU</option>
                        </select>
                    </div>
                </div>
                {{-- Bulan dan Tanggal Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select class="form-control select2-daftar_pengeluaran" style=" width: 100%;"
                            id="bulan_daftar_pengeluaran">
                            <option value="1" selected>Januari</option>
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
                    <div class="col-md-6">
                        <label for="tanggal_cetak" class="form-label">Tanggal Cetak</label>
                        <input type="date" id="tgl_daftar_pengeluaran" class="form-control">
                    </div>
                </div>
                {{-- spasi --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="spasi_daftar_pengeluaran" class="form-label">Ukuran Baris</label>
                        <input type="number" value="1" min="1" class="form-control"
                            id="spasi_daftar_pengeluaran">
                    </div>
                </div>
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row text-center">
                    <label for="cetak" class="col-form-label" style="text-align: center">Cetak</label>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-danger btn-md cetak_daftar_pengeluaran"
                            data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_daftar_pengeluaran"
                            data-jenis="layar">Layar</button>
                        {{-- <button type="button" class="btn btn-dark btn-md cetak_daftar_pengeluaran"
                            data-jenis="excel">Excel</button> --}}
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <button type="button" style="float: right" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
