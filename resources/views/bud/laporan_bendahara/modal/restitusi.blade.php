<div id="modal_restitusi" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">RESTITUSI</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="" class="form-label">Pilih</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_tgl_restitusi" value="2">
                            <label class="form-check-label" for="pilihan">Per Tanggal</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_periode_restitusi" value="3">
                            <label class="form-check-label" for="pilihan">Per Periode</label>
                        </div>
                    </div>
                </div>
                {{-- Per Tanggal --}}
                <div class="mb-3 row" id="pilih_tgl_restitusi">
                    <div class="col-md-6">
                        <label for="" class="form-label">Per Tanggal</label>
                        <input type="date" id="tgl_restitusi" class="form-control">
                    </div>
                </div>
                {{-- Per Periode --}}
                <div class="mb-3 row" id="pilih_periode_restitusi">
                    <label for="kd_unit" class="form-label">Per Periode</label>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode1_restitusi">
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="periode2_restitusi">
                    </div>
                </div>
                {{-- Kuasa BUD --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Kuasa BUD</label>
                        <select class="form-control select2-restitusi" style=" width: 100%;" id="ttd_restitusi">
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
                        <label for="halaman_restitusi" class="form-label">No. Halaman</label>
                        <input type="number" value="1" min="1" class="form-control" id="halaman_restitusi">
                    </div>
                    <div class="col-md-6">
                        <label for="spasi_restitusi" class="form-label">Spasi</label>
                        <input type="number" value="1" min="1" class="form-control" id="spasi_restitusi">
                    </div>
                </div>
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md cetak_restitusi"
                            data-jenis="pdf">PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_restitusi"
                            data-jenis="layar">Layar</button>
                        {{-- <button type="button" class="btn btn-dark btn-md cetak_restitusi"
                            data-jenis="excel">Excel</button> --}}
                        <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
