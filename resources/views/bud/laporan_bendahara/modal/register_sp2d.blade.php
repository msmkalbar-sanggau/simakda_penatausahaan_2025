<div id="modal_register_sp2d" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">REGISTER SP2D</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Pilih</label><br>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_keseluruhan_register_sp2d">
                            <label class="form-check-label" for="pilihan">Keseluruhan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_skpd_register_sp2d">
                            <label class="form-check-label" for="pilihan">Per SKPD</label>
                        </div>
                    </div>
                </div>
                <hr style="border: 1px solid black">
                {{-- SKPD --}}
                <div class="mb-3 row" id="pilih_skpd_register_sp2d">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">SKPD</label>
                        <select class="form-control select2-register_sp2d" style=" width: 100%;"
                            id="kd_skpd_register_sp2d">
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
                            id="nm_skpd_register_sp2d">
                    </div>
                </div>
                {{-- Keseluruhan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="formRadios"
                                id="pilihan_keseluruhan_register_sp2d1">
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
                                id="pilihan_bulan_register_sp2d">
                            <label class="form-check-label">
                                BULAN
                            </label>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control select2-register_sp2d" style=" width: 100%;"
                            id="bulan_register_sp2d">
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
                                id="pilihan_periode_register_sp2d">
                            <label class="form-check-label">
                                PERIODE
                            </label>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <input type="date" id="periode1_register_sp2d" class="form-control">
                    </div>
                    <div class="col-md-5">
                        <input type="date" id="periode2_register_sp2d" class="form-control">
                    </div>
                </div>
                {{-- Status --}}
                <div class="mb-3 row">
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control select2-register_sp2d" style=" width: 100%;"
                            id="status_register_sp2d">
                            <option value="1">SP2D TERBIT</option>
                            <option value="2">SP2D LUNAS</option>
                            <option value="3">SP2D ADVICE</option>
                            <option value="4">SP2D BELUM CAIR</option>
                            <option value="5">SP2D BELUM ADVICE</option>
                        </select>
                    </div>
                </div>
                {{-- Penandatangan --}}
                <div class="mb-3 row">
                    <div class="col-md-2">
                        <label for="ttd" class="form-label">Penandatangan</label>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control select2-register_sp2d" style=" width: 100%;"
                            id="ttd_register_sp2d">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($bud as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Anggaran --}}
                <div class="mb-3 row">
                    <div class="col-md-2">
                        <label for="anggaran" class="form-label">Anggaran</label>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control select2-register_sp2d" style=" width: 100%;"
                            id="anggaran_register_sp2d">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($daftar_anggaran as $anggaran)
                                <option value="{{ $anggaran->kode }}">{{ $anggaran->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Urutan --}}
                <div class="mb-3 row">
                    <div class="col-md-2">
                        <label for="urutan" class="form-label">Urutan</label>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control select2-register_sp2d" style=" width: 100%;"
                            id="urutan_register_sp2d">
                            <option value="1">NO SP2D</option>
                            <option value="2">NO KAS</option>
                        </select>
                    </div>
                </div>
                {{-- Margin --}}
                <div class="mb-3 row">
                    <label for="sptb" class="col-md-12 col-form-label">
                        Ukuran Margin Untuk Cetakan PDF (Milimeter)
                    </label>
                    <label for="sptb" class="col-md-2 col-form-label"></label>
                    <label for="" class="col-md-1 col-form-label">Kiri</label>
                    <div class="col-md-1">
                        <input type="number" class="form-control" id="margin_kiri" name="margin_kiri"
                            value="15">
                    </div>
                    <label for="" class="col-md-1 col-form-label">Kanan</label>
                    <div class="col-md-1">
                        <input type="number" class="form-control" id="margin_kanan" name="margin_kanan"
                            value="15">
                    </div>
                    <label for="" class="col-md-1 col-form-label">Atas</label>
                    <div class="col-md-1">
                        <input type="number" class="form-control" id="margin_atas" name="margin_atas"
                            value="15">
                    </div>
                    <label for="" class="col-md-1 col-form-label">Bawah</label>
                    <div class="col-md-1">
                        <input type="number" class="form-control" id="margin_bawah" name="margin_bawah"
                            value="15">
                    </div>
                </div>
                {{-- Tampilkan Kasda --}}
                <div class="mb-3 row">
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-10">
                        <input type="checkbox" id="kasda_register_sp2d">
                        <label for="kasda_register_sp2d">Tampilkan No Kasda</label>
                    </div>
                </div>
                {{-- Register --}}
                <div class="mb-3 row">
                    <div class="col-md-2">
                        <label for="urutan" class="form-label">Register</label>
                    </div>
                    <div class="col-md-10">
                        <button type="button" class="btn btn-danger btn-md cetak_register_sp2d" data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_register_sp2d"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetak_register_sp2d"
                            data-jenis="excel">Excel</button>
                    </div>
                </div>
                {{-- Realisasi --}}
                <div class="mb-3 row">
                    <div class="col-md-2">
                        <label for="urutan" class="form-label">Realisasi</label>
                    </div>
                    <div class="col-md-10">
                        <button type="button" class="btn btn-danger btn-md cetak_realisasi_sp2d" data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_realisasi_sp2d"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetak_realisasi_sp2d"
                            data-jenis="excel">Excel</button>
                    </div>
                </div>
                {{-- Dengan atau Tanpa UP --}}
                <div class="mb-3 row">
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-10">
                        <input type="radio" id="dengan_register_sp2d" name="up" checked>
                        <label for="dengan_register_sp2d">Dengan UP</label>
                        <input type="radio" id="tanpa_register_sp2d" name="up">
                        <label for="tanpa_register_sp2d">Tanpa UP</label>
                    </div>
                </div>
                {{-- Realisasi Per SKPD --}}
                <div class="mb-3 row">
                    <div class="col-md-2">
                        <label for="urutan" class="form-label">Realisasi Per SKPD</label>
                    </div>
                    <div class="col-md-10">
                        <button type="button" class="btn btn-danger btn-md cetak_realisasiskpd_sp2d"
                            data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_realisasiskpd_sp2d"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-success btn-md cetak_realisasiskpd_sp2d"
                            data-jenis="excel">Excel</button>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
