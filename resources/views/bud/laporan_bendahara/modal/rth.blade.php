<div id="modal_rth" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">RTH</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="" class="form-label">Pilih</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_tgl_rth" value="2">
                            <label class="form-check-label" for="pilihan">Per Bulan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_periode_rth" value="3">
                            <label class="form-check-label" for="pilihan">Per Periode</label>
                        </div>
                    </div>
                </div>
                {{-- Per Bulan --}}
                <div class="mb-3 row" id="pilih_tgl_rth">
                    <div class="col-md-6">
                        <label for="" class="form-label">Per Bulan</label>
                        <select id="bulan_rth" class="form-control select2-rth">
                            <option value="1" selected> Januari </option>
                            <option value="2"> Februari </option>
                            <option value="3"> Maret </option>
                            <option value="4"> April </option>
                            <option value="5"> Mei </option>
                            <option value="6"> Juni </option>
                            <option value="7"> Juli </option>
                            <option value="8"> Agustus </option>
                            <option value="9"> September </option>
                            <option value="10"> Oktober </option>
                            <option value="11"> November </option>
                            <option value="12"> Desember </option>
                        </select>
                    </div>
                </div>
                {{-- Per Periode --}}
                <div class="mb-3 row" id="pilih_periode_rth">
                    <label for="kd_unit" class="form-label">Per Periode</label>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode1_rth">
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode2_rth">
                    </div>
                </div>
                {{-- Kuasa BUD dan Tanggal TTD --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Kuasa BUD</label>
                        <select class="form-control select2-rth" style=" width: 100%;" id="ttd_rth">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($bud as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Tanggal TTD</label>
                        <input type="date" class="form-control" id="tgl_rth">
                    </div>
                </div>
                {{-- Format dan Spasi --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">FORMAT</label>
                        <select class="form-control select2-rth" style=" width: 100%;" id="format_rth">
                            <option value="1" selected>RTH</option>
                            <option value="2">SINERGI</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="spasi_rth" class="form-label">Ukuran Baris</label>
                        <input type="number" value="1" min="1" class="form-control" id="spasi_rth">
                    </div>
                </div>
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md cetak_rth" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_rth"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetak_rth"
                            data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
