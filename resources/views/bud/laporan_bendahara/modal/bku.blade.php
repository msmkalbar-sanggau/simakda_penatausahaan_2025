<div id="modal_bku" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">BKU (B IX)</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="" class="form-label">Pilih</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_tgl_bku" value="2">
                            <label class="form-check-label" for="pilihan">Per Tanggal</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_periode_bku" value="3">
                            <label class="form-check-label" for="pilihan">Per Periode</label>
                        </div>
                    </div>
                </div>
                {{-- Per Tanggal --}}
                <div class="mb-3 row" id="pilih_tgl_bku">
                    <div class="col-md-6">
                        <label for="" class="form-label">Per Tanggal</label>
                        <input type="date" id="tgl_bku" class="form-control">
                    </div>
                </div>
                {{-- Per Periode --}}
                <div class="mb-3 row" id="pilih_periode_bku">
                    <label for="kd_unit" class="form-label">Per Periode</label>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode1_bku">
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode2_bku">
                    </div>
                </div>
                {{-- Kuasa BUD dan Jenis --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="status_rek" class="form-label">Rekening Penerimaan</label>
                        <select class="form-control select2-bku" style=" width: 100%;" id="status_rek">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1">Keseluruhan</option>
                            <option value="3001006966">Bank-3001006966</option>
                            <option value="3001000016">Bank-3001000016</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select class="form-control select2-bku" style=" width: 100%;" id="jenis_bku">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1"> Tanpa Tanggal </option>
                            <option value="2"> Dengan Tanggal </option>
                            {{-- <option value="3"> Dengan Tanggal (Tanpa BLUD dan Jaspel)</option> --}}
                            <option value="4"> Rincian (Sementara Hanya pertanggal)</option>
                        </select>
                    </div>
                </div>
                {{-- Kuasa BUD --}}
                <div class="mb-3 row">
                    <div class="col-md-18">
                        <label for="ttd_bku" class="form-label">Kuasa BUD</label>
                        <select class="form-control select2-bku" style=" width: 100%;" id="ttd_bku">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($ppkd as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- No. Halaman dan Nomor Urut --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="halaman_bku" class="form-label">No. Halaman</label>
                        <input type="number" value="1" min="1" class="form-control" id="halaman_bku">
                    </div>
                    <div class="col-md-6">
                        <label for="no_urut_bku" class="form-label">Nomor Urut</label>
                        <input type="number" value="1" min="1" class="form-control" id="no_urut_bku">
                    </div>
                </div>
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md cetak_bku" data-jenis="pdf"> PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_bku"
                            data-jenis="layar">Layar</button>
                        {{-- <button type="button" class="btn btn-dark btn-md cetak_bku"
                            data-jenis="excel">Excel</button> --}}
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
