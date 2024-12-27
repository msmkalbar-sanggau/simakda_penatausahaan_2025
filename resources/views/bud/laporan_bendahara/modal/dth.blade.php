<div id="modal_dth" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">DTH</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="" class="form-label">Pilih</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_tgl_dth" value="2">
                            <label class="form-check-label" for="pilihan">Per Bulan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_periode_dth" value="3">
                            <label class="form-check-label" for="pilihan">Per Periode</label>
                        </div>
                    </div>
                </div>
                {{-- Per Bulan --}}
                <div class="mb-3 row" id="pilih_tgl_dth">
                    <div class="col-md-6">
                        <label for="" class="form-label">Per Bulan</label>
                        <select id="bulan_dth" class="form-control select2-dth">
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
                <div class="mb-3 row" id="pilih_periode_dth">
                    <label for="kd_unit" class="form-label">Per Periode</label>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode1_dth">
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode2_dth">
                    </div>
                </div>
                {{-- SKPD --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="skpd_dth" class="form-label">Kode SKPD</label>
                        <select class="form-control select2-dth" style=" width: 100%;" id="skpd_dth" name="skpd_dth">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($daftar_skpd as $skpd)
                                <option value="{{ $skpd->kd_skpd }}">{{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- BENDAHARAHA PENGELUARAN DAN PENGGUNA ANGGARAN --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="bendahara" class="form-label">Bendahara</label>
                        <select class="form-control select2-dth" style=" width: 100%;" id="bendahara" name="bendahara">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="pa_kpa" class="form-label">PA/KPA</label>
                        <select class="form-control select2-dth" style=" width: 100%;" id="pa_kpa" name="pa_kpa">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                    </div>
                </div>
                {{-- Kuasa BUD dan Tanggal TTD dan Spasi --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Tanggal TTD</label>
                        <input type="date" class="form-control" id="tgl_dth">
                    </div>
                    <div class="col-md-6">
                        <label for="spasi_dth" class="form-label">Ukuran Baris</label>
                        <input type="number" value="1" min="1" class="form-control" id="spasi_dth">
                    </div>
                </div>
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-primary btn-md cetak_dth"
                            data-jenis="keseluruhan">Keseluruhan</button>
                        <button type="button" class="btn btn-danger btn-md cetak_dth" data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_dth"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetak_dth"
                            data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
