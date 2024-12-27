<div id="modal_cek_data_akuntansi" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">CEK DATA AKUNTANSI</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Bulan --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="" class="form-label">Bulan</label>
                        <select id="bulan_cek_data_akuntansi" class="form-control select2-cek_data_akuntansi">
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
                    <div class="col-md-6">
                        <label for="" class="form-label">Tanggal Cetak</label>
                        <input type="date" id="tgl_cek_data_akuntansi" class="form-control">
                    </div>

                </div>
                {{-- Kuasa BUD --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Kuasa BUD</label>
                        <select class="form-control select2-cek_data_akuntansi" style=" width: 100%;"
                            id="ttd_cek_data_akuntansi">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($bud as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Pilihan Cetak dan Rinci --}}
                <div class="mb-3 row">
                    <label for="belanja" class="col-form-label col-md-2" style="text-align: center">Cek Belanja
                    </label>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger btn-md cetak_cek_data_akuntansi_belanja"
                            data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_cek_data_akuntansi_belanja"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetak_cek_data_akuntansi_belanja"
                            data-jenis="excel">Excel</button>
                    </div>
                    <label for="pendapatan" class="col-form-label col-md-2" style="text-align: center">Cek Pendapatan
                    </label>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger btn-md cetak_cek_data_akuntansi_pendapatan"
                            data-jenis="pdf">
                            PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_cek_data_akuntansi_pendapatan"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetak_cek_data_akuntansi_pendapatan"
                            data-jenis="excel">Excel</button>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <button type="button" style="float: right" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
