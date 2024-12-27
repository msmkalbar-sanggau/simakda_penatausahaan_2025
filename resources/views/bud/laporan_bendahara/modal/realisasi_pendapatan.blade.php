<div id="modal_realisasi_pendapatan" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak" id="labelcetak">REALISASI PENDAPATAN</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan --}}
                <div class="mb-3 row" id="row-hidden">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Pilih</label><br>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_keseluruhan_realisasi_pendapatan" value="1">
                            <label class="form-check-label" for="pilihan">Keseluruhan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_skpd_realisasi_pendapatan" value="2">
                            <label class="form-check-label" for="pilihan">SKPD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                id="pilihan_unit_realisasi_pendapatan" value="3">
                            <label class="form-check-label" for="pilihan">Unit</label>
                        </div>
                    </div>
                </div>
                {{-- SKPD --}}
                <div class="mb-3 row" id="pilih_skpd_realisasi_pendapatan">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">SKPD</label>
                        <select class="form-control select2-realisasi_pendapatan" style=" width: 100%;"
                            id="kd_skpd_realisasi_pendapatan">
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
                            id="nm_skpd_realisasi_pendapatan">
                    </div>
                </div>
                {{-- Unit --}}
                <div class="mb-3 row" id="pilih_unit_realisasi_pendapatan">
                    <div class="col-md-6">
                        <label for="kd_unit" class="form-label">Unit</label>
                        <select class="form-control select2-realisasi_pendapatan" style=" width: 100%;"
                            id="kd_unit_realisasi_pendapatan">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($daftar_skpd as $skpd)
                                <option value="{{ $skpd->kd_skpd }}" data-nama="{{ $skpd->nm_skpd }}">
                                    {{ $skpd->kd_skpd }} | {{ $skpd->nm_skpd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="nm_unit" class="form-label"></label>
                        <input type="text" style="border:none;background-color:white" class="form-control" readonly
                            id="nm_unit_realisasi_pendapatan">
                    </div>
                </div>
                {{-- Periode dan Jenis --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="periode" class="form-label">Periode</label>
                        <select class="form-control select2-realisasi_pendapatan" style=" width: 100%;"
                            id="periode_realisasi_pendapatan">
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
                    <div class="col-md-6">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select class="form-control select2-realisasi_pendapatan" id="jenis_realisasi_pendapatan">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="4">Jenis</option>
                            <option value="8">Objek</option>
                            <option value="12">Rincian Objek</option>
                        </select>
                    </div>
                </div>
                {{-- Anggaran dan Tanggal TTD --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="anggaran" class="form-label">Anggaran</label>
                        <select class="form-control select2-realisasi_pendapatan" style=" width: 100%;"
                            id="anggaran_realisasi_pendapatan">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($jns_anggaran as $anggaran)
                                <option value="{{ $anggaran->kode }}" data-nama="{{ $anggaran->nama }}">
                                    {{ $anggaran->kode }} | {{ $anggaran->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_ttd" class="form-label">Tanggal TTD</label>
                        <input type="date" id="tgl_ttd_realisasi_pendapatan" class="form-control">
                    </div>
                </div>
                {{-- Penandatangan --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="penandatangan" class="form-label">Penandatangan</label>
                        <select class="form-control select2-realisasi_pendapatan" style=" width: 100%;"
                            id="ttd_realisasi_pendapatan">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            @foreach ($bud as $ttd)
                                <option value="{{ $ttd->nip }}">{{ $ttd->nip }} | {{ $ttd->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="spasi_realisasi_pendapatan" class="form-label">Ukuran Baris</label>
                        <input type="number" value="1" min="1" class="form-control"
                            id="spasi_realisasi_pendapatan">
                    </div>
                </div>
                {{-- Pilihan Cetak --}}
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md cetak_realisasi_pendapatan"
                            data-jenis="pdf"> PDF</button>
                        <button type="button" class="btn btn-dark btn-md cetak_realisasi_pendapatan"
                            data-jenis="layar">Layar</button>
                        <button type="button" class="btn btn-dark btn-md cetak_realisasi_pendapatan"
                            data-jenis="excel">Excel</button>
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
