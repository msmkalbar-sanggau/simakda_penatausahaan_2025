{{-- modal cetak SPJ --}}
<div id="modal_cetak2" class="modal" role="dialog"aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak2" id="labelcetak2"></label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- SKPD --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="kd_skpd_2" class="form-label">Kode SKPD</label>
                        {{-- <input type="text"  class="form-control" id="kd_skpd_2" name="kd_skpd_2" value="{{ $data_skpd->kd_skpd_2 }}" readonly> --}}
                        <select class="form-control select2-modal2" style=" width: 100%;" id="kd_skpd_2"
                            name="kd_skpd_2">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="periode1">
                        <label for="tanggal1_2_2" class="form-label">Periode</label>
                        <input type="date" id="tanggal1_2" name="tanggal1_2" class="form-control">
                    </div>
                    <div class="col-md-3" id="periode2">
                        <label for="tanggal2_2" class="form-label">&nbsp;</label>
                        <input type="date" id="tanggal2_2" name="tanggal2_2" class="form-control">
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <div id="bendahara1">
                            <label for="bendahara_2" class="form-label">Bendahara</label>
                            <select class="form-control select2-modal2" style=" width: 100%;" id="bendahara_2"
                                name="bendahara_2">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    {{-- PA/KPA --}}
                    <div class="col-md-6">
                        <div id="tgl_ttd1">
                            <label for="tgl_ttd_2" class="form-label">Tanggal TTD</label>
                            <input type="date" id="tgl_ttd_2" name="tgl_ttd_2" class="form-control">
                        </div>
                    </div>
                </div>


                {{-- Bendahara --}}
                <div class="mb-3 row">
                    <div class="col-md-6" id="pa_kpa1">
                        <label for="pa_kpa_2" class="form-label">PA/KPA</label>
                        <select class="form-control select2-modal2" style=" width: 100%;" id="pa_kpa_2"
                            name="pa_kpa_2">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="jenis1">
                        <label for="jenis_cetak_2" class="form-label">Jenis</label>
                        <select name="jenis_cetak_2" class="form-control select2-modal2" id="jenis_cetak_2">
                            <option value="" selected disabled>Silahkan Pilih</option>
                            <option value="skpd">SKPD/Unit</option>
                        </select>
                    </div>
                    <div class="col-md-2" id="spasi1">
                        <label for="spasi_2" class="form-label">Spasi</label>
                        <input type="number" value="1" min="1" class="form-control" id="spasi_2"
                            name="spasi_2">
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <div id="rekening1">
                            <label for="rekening" class="form-label">Rekening</label>
                            <select class="form-control select2-modal2" style=" width: 100%;" id="rekening"
                                name="rekening">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_rekening as $rekening)
                                    <option value="{{ $rekening->kd_rek6 }}">
                                        {{ $rekening->kd_rek6 }} | {{ $rekening->nm_rek6 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- PA/KPA --}}
                    <div class="col-md-6">
                        <div id="tipe1">
                            <label for="tipe" class="form-label">Data</label>
                            <select class="form-control select2-modal2" style=" width: 100%;" id="tipe"
                                name="tipe">
                                <option value="1" selected>Penerimaan</option>
                                <option value="2">Penyetoran</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3 row" id="jenisanggaran2">
                    <div class="col-md-6">
                        <label for="jns_anggaran_2" class="form-label">Jenis Anggaran</label>
                        <select name="jns_anggaran_2" class="form-control select2-modal2" id="jns_anggaran_2">
                            <option value="" selected disabled>Silahkan Pilih</option>
                            @foreach ($jns_anggaran as $anggaran)
                                <option value="{{ $anggaran->kode }}" data-nama="{{ $anggaran->nama }}">
                                    {{ $anggaran->kode }} | {{ $anggaran->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div id="rekening2">
                            <label for="rekening3" class="form-label">Rekening</label>
                            <select class="form-control select2-modal2" style=" width: 100%;" id="rekening3"
                                name="rekening3">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                        </div>
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
