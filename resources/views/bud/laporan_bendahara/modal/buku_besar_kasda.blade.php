<div id="modal_buku_besar_kasda" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">BUKU BESAR KASDA</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- SKPD --}}
                <div class="mb-3 row">
                    <label for="kd_skpd" class="form-label">SKPD</label>
                    <div class="col-md-12">
                        <select class="form-control select2-buku_besar_kasda" style=" width: 100%;"
                            id="kd_skpd_buku_besar_kasda">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($daftar_skpd as $skpd)
                                <option value="{{ $skpd->kd_skpd }}" data-nama="{{ $skpd->nm_skpd }}">
                                    {{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Rekening --}}
                <div class="mb-3 row">
                    <label for="rekening" class="form-label">Rekening</label>
                    <div class="col-md-12">
                        <select class="form-control select2-buku_besar_kasda" style=" width: 100%;"
                            id="rekening_buku_besar_kasda">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                    </div>
                </div>
                {{-- Periode --}}
                <div class="mb-3 row">
                    <label for="kd_skpd" class="form-label">Periode</label>
                    <div class="col-md-6">
                        <input type="date" id="periode1_buku_besar_kasda" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <input type="date" id="periode2_buku_besar_kasda" class="form-control">
                    </div>
                </div>
                {{-- Penandatangan --}}
                <div class="mb-3 row">
                    <label for="ttd" class="form-label">Penandatangan</label>
                    <div class="col-md-12">
                        <select class="form-control select2-buku_besar_kasda" style=" width: 100%;"
                            id="ttd_buku_besar_kasda">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($pa_kpa as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md cetak_buku_besar_kasda" data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_buku_besar_kasda"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
