{{-- modal cetak SPJ --}}
<div id="modal_cetak" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak"></label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- SKPD --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Kode SKPD</label>
                        <select class="form-control select2-modal" style=" width: 100%;" id="kd_skpd" name="kd_skpd">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="periode1">
                        <label for="tanggal1" class="form-label">Periode</label>
                        <input type="date" id="tanggal1" name="tanggal1" class="form-control">
                    </div>
                    <div class="col-md-3" id="periode2">
                        <label for="tanggal2" class="form-label">&nbsp;</label>
                        <input type="date" id="tanggal2" name="tanggal2" class="form-control">
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <div id="bendahara1">
                            <label for="bendahara" class="form-label">Bendahara</label>
                            <select class="form-control select2-modal" style=" width: 100%;" id="bendahara"
                                name="bendahara">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    {{-- PA/KPA --}}
                    <div class="col-md-3">
                        <div id="tgl_ttd1">
                            <label for="pa_kpa" class="form-label">Tanggal TTD</label>
                            <input type="date" id="tgl_ttd" name="tgl_ttd" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="jenis_cetak" class="form-label">Format</label>
                        <select name="format" class="form-control select2-modal" id="format">
                            <option value="" selected disabled>Silahkan Pilih</option>
                            <option value="77">Permendagri 77</option>
                            <option value="13">Permendagri 13 (Untuk Kroscek)</option>
                        </select>
                    </div>
                </div>

                {{-- Bendahara --}}
                <div class="mb-3 row">
                    <div class="col-md-6" id="pa_kpa1">
                        <label for="pa_kpa" class="form-label">PA/KPA</label>
                        <select class="form-control select2-modal" style=" width: 100%;" id="pa_kpa" name="pa_kpa">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                    </div>


                    <div class="col-md-4" id="jenis1">
                        <label for="jenis_cetak" class="form-label">Jenis</label>
                        <select name="jenis_cetak" class="form-control select2-modal" id="jenis_cetak">
                            <option value="" selected disabled>Silahkan Pilih</option>
                            <option value="skpd">SKPD/Unit</option>
                        </select>
                    </div>
                    <div class="col-md-2" id="spasi1">
                        <label for="spasi" class="form-label">Spasi</label>
                        <input type="number" value="1" min="1" class="form-control" id="spasi"
                            name="spasi">
                    </div>
                </div>

                <div class="mb-3 row" id="jenisanggaran">
                    <div class="col-md-6">
                        <label for="jns_anggaran" class="form-label">Jenis Anggaran</label>
                        <select name="jns_anggaran" class="form-control select2-modal" id="jns_anggaran">
                            <option value="" selected disabled>Silahkan Pilih</option>
                            @foreach ($jns_anggaran as $anggaran)
                                <option value="{{ $anggaran->kode }}" data-nama="{{ $anggaran->nama }}">
                                    {{ $anggaran->kode }} | {{ $anggaran->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md bku_pdf" data-jenis="pdf"
                            name="bku_pdf"> PDF</button>
                        <button type="button" class="btn btn-dark btn-md bku_layar" data-jenis="layar"
                            name="bku_layar">Layar</button>
                        <button type="button" class="btn btn-success btn-md bku_excel" data-jenis="excel"
                            name="bku_excel">Excel</button>
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
{{-- modal cetak SPJ  --}}
