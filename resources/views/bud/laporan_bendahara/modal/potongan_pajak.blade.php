<div id="modal_potongan_pajak" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">DAFTAR POTONGAN PAJAK</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Pilih</label><br>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_rekap_potongan_pajak" value="1">
                            <label class="form-check-label" for="pilihan">Rekap per SKPD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_skpd_potongan_pajak" value="2">
                            <label class="form-check-label" for="pilihan">SKPD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_unit_potongan_pajak" value="3">
                            <label class="form-check-label" for="pilihan">Unit</label>
                        </div>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_keseluruhan_potongan_pajak" value="1">
                            <label class="form-check-label" for="pilihan">Keseluruhan</label>
                        </div>
                    </div>
                </div>
                {{-- SKPD --}}
                <div class="mb-3 row" id="pilih_skpd_potongan_pajak">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">SKPD</label>
                        <select class="form-control select2-potongan_pajak" style=" width: 100%;"
                            id="kd_skpd_potongan_pajak">
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
                            id="nm_skpd_potongan_pajak">
                    </div>
                </div>
                {{-- Unit --}}
                <div class="mb-3 row" id="pilih_unit_potongan_pajak">
                    <div class="col-md-6">
                        <label for="kd_unit" class="form-label">Unit</label>
                        <select class="form-control select2-potongan_pajak" style=" width: 100%;"
                            id="kd_unit_potongan_pajak">
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
                            id="nm_unit_potongan_pajak">
                    </div>
                </div>
                {{-- Tanggal Awal dan Tanggal Akhir --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="tanggal_ttd" class="form-label">Tanggal Awal</label>
                        <input type="date" id="tgl1_potongan_pajak" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_ttd" class="form-label">Tanggal Akhir</label>
                        <input type="date" id="tgl2_potongan_pajak" class="form-control">
                    </div>
                </div>
                {{-- Penandatangan dan Jenis SP2D --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Kuasa BUD</label>
                        <select class="form-control select2-potongan_pajak" style=" width: 100%;"
                            id="ttd_potongan_pajak">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($bud as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="jenis_sp2d" class="form-label">Jenis SP2D</label>
                        <select class="form-control select2-potongan_pajak" style=" width: 100%;"
                            id="sp2d_potongan_pajak">
                            <option value="0" selected>GAJI</option>
                            <option value="1">NON GAJI</option>
                        </select>
                    </div>
                </div>
                {{-- Jenis Belanja dan spasi --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="jenis_belanja" class="form-label">Jenis Belanja</label>
                        <select class="form-control select2-potongan_pajak" style=" width: 100%;"
                            id="belanja_potongan_pajak">
                            <option value="0" selected>BL</option>
                            <option value="1">BTL</option>
                            <option value="2">BL dan BTL</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="spasi_potongan_pajak" class="form-label">Spasi</label>
                        <input type="number" value="1" min="1" class="form-control"
                            id="spasi_potongan_pajak">
                    </div>
                </div>

                {{-- Pilihan Cetak --}}
                <div class="mb-3 row text-center">
                    <label for="cetak" class="col-form-label" style="text-align: center">Cetak</label>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-danger btn-md cetak_potongan_pajak" data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_potongan_pajak"
                            data-jenis="layar">Layar</button>
                        {{-- <button type="button" class="btn btn-dark btn-md cetak_potongan_pajak"
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
