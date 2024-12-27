<div id="modal_penerimaan_non_pendapatan" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">PENERIMAAN NON PENDAPATAN</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="" class="form-label">Pilih</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_tgl_penerimaan_non_pendapatan" value="2">
                            <label class="form-check-label" for="pilihan">Per Tanggal</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_periode_penerimaan_non_pendapatan" value="3">
                            <label class="form-check-label" for="pilihan">Per Periode</label>
                        </div>
                    </div>
                </div>
                {{-- Per Tanggal --}}
                <div class="mb-3 row" id="pilih_tgl_penerimaan_non_pendapatan">
                    <div class="col-md-6">
                        <label for="" class="form-label">Per Tanggal</label>
                        <input type="date" id="tgl_penerimaan_non_pendapatan" class="form-control">
                    </div>
                </div>
                {{-- Per Periode --}}
                <div class="mb-3 row" id="pilih_periode_penerimaan_non_pendapatan">
                    <label for="kd_unit" class="form-label">Per Periode</label>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode1_penerimaan_non_pendapatan">
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode2_penerimaan_non_pendapatan">
                    </div>
                </div>
                {{-- Kuasa BUD --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Kuasa BUD</label>
                        <select class="form-control select2-penerimaan_non_pendapatan" style=" width: 100%;"
                            id="ttd_penerimaan_non_pendapatan">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($bud as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- No. Halaman dan Spasi --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="halaman_penerimaan_non_pendapatan" class="form-label">No. Halaman</label>
                        <input type="number" value="1" min="1" class="form-control"
                            id="halaman_penerimaan_non_pendapatan">
                    </div>
                    <div class="col-md-6">
                        <label for="spasi_penerimaan_non_pendapatan" class="form-label">Spasi</label>
                        <input type="number" value="1" min="1" class="form-control"
                            id="spasi_penerimaan_non_pendapatan">
                    </div>
                </div>
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md cetak_penerimaan_non_pendapatan"
                            data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_penerimaan_non_pendapatan"
                            data-jenis="layar">Layar</button>
                        {{-- <button type="button" class="btn btn-dark btn-md cetak_penerimaan_non_pendapatan"
                            data-jenis="excel">Excel</button> --}}
                        <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
