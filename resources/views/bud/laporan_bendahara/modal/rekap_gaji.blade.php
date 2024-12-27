<div id="modal_rekap_gaji" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">REKAP GAJI</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Pilih</label><br>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_keseluruhan_rekap_gaji">
                            <label class="form-check-label" for="pilihan">Keseluruhan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_skpd_rekap_gaji">
                            <label class="form-check-label" for="pilihan">Per SKPD</label>
                        </div>
                    </div>
                </div>
                <hr style="border: 1px solid black">
                {{-- SKPD --}}
                <div class="mb-3 row" id="pilih_skpd_rekap_gaji">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">SKPD</label>
                        <select class="form-control select2-rekap_gaji" style=" width: 100%;" id="kd_skpd_rekap_gaji">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($daftar_skpd as $skpd)
                                <option value="{{ $skpd->kd_skpd }}" data-nama="{{ $skpd->nm_skpd }}">
                                    {{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="nm_skpd" class="form-label"></label>
                        <input type="text" style="border:none;background-color:white" class="form-control" readonly
                            id="nm_skpd_rekap_gaji">
                    </div>
                </div>
                {{-- Keseluruhan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="formRadios"
                                id="pilihan_keseluruhan_rekap_gaji1">
                            <label class="form-check-label">
                                KESELURUHAN
                            </label>
                        </div>
                    </div>
                </div>
                {{-- Bulan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="formRadios"
                                id="pilihan_bulan_rekap_gaji">
                            <label class="form-check-label">
                                BULAN
                            </label>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control select2-rekap_gaji" style=" width: 100%;" id="bulan_rekap_gaji">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1">Januari</option>
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
                </div>
                {{-- Periode --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="formRadios"
                                id="pilihan_periode_rekap_gaji">
                            <label class="form-check-label">
                                PERIODE
                            </label>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <input type="date" id="periode1_rekap_gaji" class="form-control">
                    </div>
                    <div class="col-md-5">
                        <input type="date" id="periode2_rekap_gaji" class="form-control">
                    </div>
                </div>
                {{-- Penandatangan --}}
                {{-- <div class="mb-3 row">
                        <div class="col-md-2">
                            <label for="penandatangan" class="form-label">Penandatangan</label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control select2-rekap_gaji" style=" width: 100%;" id="ttd_rekap_gaji">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($pa_kpa as $ttd)
                                    <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md cetak_rekap_gaji" data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_rekap_gaji"
                            data-jenis="layar">Layar</button>
                        {{-- <button type="button" class="btn btn-dark btn-md cetak_rekap_gaji"
                            data-jenis="excel">Excel</button> --}}
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
