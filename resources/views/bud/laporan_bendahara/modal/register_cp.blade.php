<div id="modal_register_cp" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">REGISTER CP</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Pilih</label><br>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_rekap_register_cp" value="1">
                            <label class="form-check-label" for="pilihan">Rekap per SKPD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_skpd_register_cp" value="2">
                            <label class="form-check-label" for="pilihan">SKPD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_unit_register_cp" value="3">
                            <label class="form-check-label" for="pilihan">Unit</label>
                        </div>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_keseluruhan_register_cp" value="1">
                            <label class="form-check-label" for="pilihan">Keseluruhan</label>
                        </div>
                    </div>
                </div>
                {{-- SKPD --}}
                <div class="mb-3 row" id="pilih_skpd_register_cp">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">SKPD</label>
                        <select class="form-control select2-register_cp" style=" width: 100%;" id="kd_skpd_register_cp">
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
                            id="nm_skpd_register_cp">
                    </div>
                </div>
                {{-- Unit --}}
                <div class="mb-3 row" id="pilih_unit_register_cp">
                    <div class="col-md-6">
                        <label for="kd_unit" class="form-label">Unit</label>
                        <select class="form-control select2-register_cp" style=" width: 100%;" id="kd_unit_register_cp">
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
                            id="nm_unit_register_cp">
                    </div>
                </div>
                {{-- Tanggal Awal dan Tanggal Akhir --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="tanggal_ttd" class="form-label">Tanggal Awal</label>
                        <input type="date" id="tgl1_register_cp" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_ttd" class="form-label">Tanggal Akhir</label>
                        <input type="date" id="tgl2_register_cp" class="form-control">
                    </div>
                </div>
                {{-- Penandatangan dan spasi --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Kuasa BUD</label>
                        <select class="form-control select2-register_cp" style=" width: 100%;" id="ttd_register_cp">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($bud as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="spasi_register_cp" class="form-label">Ukuran Baris</label>
                        <input type="number" value="1" min="1" class="form-control"
                            id="spasi_register_cp">
                    </div>
                </div>
                {{-- Pilihan Cetak dan Rinci --}}
                <div class="mb-3 row">
                    <label for="cetak" class="col-form-label col-md-1" style="text-align: center">Cetak</label>
                    <div class="col-md-5">
                        <button type="button" class="btn btn-danger btn-md cetak_register_cp" data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_register_cp"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetak_register_cp"
                            data-jenis="excel">Excel</button>
                    </div>
                    <label for="rinci" class="col-form-label col-md-1" style="text-align: center">Rinci</label>
                    <div class="col-md-5">
                        <button type="button" class="btn btn-danger btn-md rinci_register_cp" data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md rinci_register_cp"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md rinci_register_cp"
                            data-jenis="excel">Excel</button>
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
