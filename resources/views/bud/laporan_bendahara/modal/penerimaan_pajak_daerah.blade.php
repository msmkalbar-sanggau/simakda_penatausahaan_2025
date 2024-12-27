<div id="modal_pajak_daerah" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">PENERIMAAN PAJAK DAERAH</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-12">
                        <label for="" class="form-label">Pilih</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="pilihan_bulan_pajak_daerah"
                                name="inlineRadioOptions">
                            <label class="form-check-label" for="pilihan">Per Bulan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="pilihan_tanggal_pajak_daerah"
                                name="inlineRadioOptions">
                            <label class="form-check-label" for="pilihan">Per Tanggal</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="pilihan_pengirim_pajak_daerah"
                                name="inlineRadioOptions">
                            <label class="form-check-label" for="pilihan">Per Pengirim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="pilihan_wilayah_pajak_daerah"
                                name="inlineRadioOptions">
                            <label class="form-check-label" for="pilihan">Per Wilayah</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="pilihan_rekap_pajak_daerah"
                                name="inlineRadioOptions">
                            <label class="form-check-label" for="pilihan">Rekap</label>
                        </div>
                    </div>
                </div>
                {{-- Per Bulan --}}
                <div class="mb-3 row" id="pilih_bulan_pajak_daerah">
                    <div class="col-md-6">
                        <label for="" class="form-label">Bulan</label>
                        <select id="bulan_pajak_daerah" class="form-control select2-pajak_daerah">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1"> Januari </option>
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
                {{-- Per Tanggal --}}
                <div class="mb-3 row" id="pilih_tanggal_pajak_daerah">
                    <div class="col-md-6">
                        <label for="" class="form-label">Tanggal Kas </label>
                        <input type="date" class="form-control" id="tgl_kas_pajak_daerah">
                    </div>
                    <div class="col-md-6">
                        <label for="" class="form-label">Tanggal Kas Sebelumnya</label>
                        <input type="date" class="form-control" id="tgl_kas_sbl_pajak_daerah">
                    </div>
                </div>
                {{-- Per Pengirim --}}
                <div class="mb-3 row" id="pilih_pengirim_pajak_daerah">
                    <label for="" class="form-label">Nama Pengirim</label>
                    <div class="col-md-6">
                        <select id="pengirim_pajak_daerah" class="form-control select2-pajak_daerah">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($daftar_pengirim as $pengirim)
                                <option value="{{ $pengirim->kd_pengirim }}" data-nama="{{ $pengirim->nm_pengirim }}">
                                    {{ $pengirim->kd_pengirim }} |
                                    {{ $pengirim->nm_pengirim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="nm_pengirim_pajak_daerah" readonly>
                    </div>
                    <br>
                    <div class="col-md-12">
                        <div class="form-check form-check-right">
                            <input class="form-check-input" type="radio" name="formRadiosRight"
                                id="pilihan_tgl_pengirim_pajak_daerah">
                            <label class="form-check-label">
                                Per Tanggal
                            </label>
                        </div>
                        <div class="form-check form-check-right">
                            <input class="form-check-input" type="radio" name="formRadiosRight"
                                id="pilihan_bulan_pengirim_pajak_daerah">
                            <label class="form-check-label">
                                Per Bulan
                            </label>
                        </div>
                    </div>
                </div>
                {{-- Per Tanggal Pengirim --}}
                <div class="mb-3 row" id="pilih_tgl_pengirim_pajak_daerah">
                    <div class="col-md-6">
                        <label for="" class="form-label">Tanggal Kas </label>
                        <input type="date" class="form-control" id="tgl_kas_pengirim_pajak_daerah">
                    </div>
                    <div class="col-md-6">
                        <label for="" class="form-label">Tanggal Kas Sebelumnya</label>
                        <input type="date" class="form-control" id="tgl_kas_sbl_pengirim_pajak_daerah">
                    </div>
                </div>
                {{-- Per Bulan Pengirim --}}
                <div class="mb-3 row" id="pilih_bulan_pengirim_pajak_daerah">
                    <div class="col-md-6">
                        <label for="" class="form-label">Bulan</label>
                        <select id="bulan_pengirim1_pajak_daerah" class="form-control select2-pajak_daerah">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1"> Januari </option>
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
                    <div class="col-md-6">
                        <label for="" class="form-label">s/d</label>
                        <select id="bulan_pengirim2_pajak_daerah" class="form-control select2-pajak_daerah">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1"> Januari </option>
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
                {{-- Per Wilayah --}}
                <div class="mb-3 row" id="pilih_wilayah_pajak_daerah">
                    <label for="" class="form-label">Nama Wilayah</label>
                    <div class="col-md-6">
                        <select id="wilayah_pajak_daerah" class="form-control select2-pajak_daerah">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($daftar_wilayah as $wilayah)
                                <option value="{{ $wilayah->kd_wilayah }}" data-nama="{{ $wilayah->nm_wilayah }}">
                                    {{ $wilayah->kd_wilayah }} |
                                    {{ $wilayah->nm_wilayah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="nm_wilayah_pajak_daerah" readonly>
                    </div>
                    <br>
                    <div class="col-md-12">
                        <div class="form-check form-check-right">
                            <input class="form-check-input" type="radio" name="formRadiosRight"
                                id="pilihan_tgl_wilayah_pajak_daerah">
                            <label class="form-check-label">
                                Per Tanggal
                            </label>
                        </div>
                        <div class="form-check form-check-right">
                            <input class="form-check-input" type="radio" name="formRadiosRight"
                                id="pilihan_bulan_wilayah_pajak_daerah">
                            <label class="form-check-label">
                                Per Bulan
                            </label>
                        </div>
                    </div>
                </div>
                {{-- Per Tanggal Wilayah --}}
                <div class="mb-3 row" id="pilih_tgl_wilayah_pajak_daerah">
                    <div class="col-md-6">
                        <label for="" class="form-label">Tanggal Kas </label>
                        <input type="date" class="form-control" id="tgl_kas_wilayah_pajak_daerah">
                    </div>
                    <div class="col-md-6">
                        <label for="" class="form-label">Tanggal Kas Sebelumnya</label>
                        <input type="date" class="form-control" id="tgl_kas_sbl_wilayah_pajak_daerah">
                    </div>
                </div>
                {{-- Per Bulan Wilayah --}}
                <div class="mb-3 row" id="pilih_bulan_wilayah_pajak_daerah">
                    <div class="col-md-6">
                        <label for="" class="form-label">Bulan</label>
                        <select id="bulan_wilayah1_pajak_daerah" class="form-control select2-pajak_daerah">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1"> Januari </option>
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
                    <div class="col-md-6">
                        <label for="" class="form-label">s/d</label>
                        <select id="bulan_wilayah2_pajak_daerah" class="form-control select2-pajak_daerah">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1"> Januari </option>
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
                {{-- Rekap --}}
                <div class="mb-3 row" id="pilih_rekap_pajak_daerah">
                    <div class="col-md-6">
                        <label for="" class="form-label">Bulan</label>
                        <select id="bulan_rekap1_pajak_daerah" class="form-control select2-pajak_daerah">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1"> Januari </option>
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
                    <div class="col-md-6">
                        <label for="" class="form-label">s/d</label>
                        <select id="bulan_rekap2_pajak_daerah" class="form-control select2-pajak_daerah">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="1"> Januari </option>
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
                {{-- No. Halaman --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="halaman_pajak_daerah" class="form-label">No. Halaman</label>
                        <input type="number" value="1" min="1" class="form-control"
                            id="halaman_pajak_daerah">
                    </div>
                </div>
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md cetak_pajak_daerah" data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_pajak_daerah"
                            data-jenis="layar">Layar</button>
                        {{-- <button type="button" class="btn btn-dark btn-md cetak_pajak_daerah"
                            data-jenis="excel">Excel</button> --}}
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
