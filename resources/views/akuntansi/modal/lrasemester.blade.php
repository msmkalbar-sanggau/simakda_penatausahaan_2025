{{-- modal cetak SPJ --}}

<div id="modal_cetak_semester" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak_semester" id="labelcetak_semester"></label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan SKPD/Unit --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Pilih</label><br>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="pilihan0"
                                value="keseluruhan">
                            <label class="form-check-label" for="pilihan">Keseluruhan</label>
                        </div>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="pilihan1"
                                value="skpd">
                            <label class="form-check-label" for="pilihan">SKPD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="pilihan2"
                                value="unit">
                            <label class="form-check-label" for="pilihan">Unit</label>
                        </div>
                    </div>
                    {{-- Bulan --}}
                    <div class="col-md-6" >
                        <div id="baris_skpd">
                            <label for="kd_skpd" class="form-label">Kode SKPD</label>
                            <select class="form-control select2  @error('kd_skpd') is-invalid @enderror"
                                style=" width: 100%;" id="kd_skpd" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Pilih</label><br>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pilihanperiode" id="pilihan0"
                                value="tahun">
                            <label class="form-check-label" for="pilihan">Keseluruhan</label>
                        </div>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pilihanperiode" id="pilihan1"
                                value="periode">
                            <label class="form-check-label" for="pilihan">Periode</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pilihanperiode" id="pilihan2"
                                value="bulan">
                            <label class="form-check-label" for="pilihan">Bulan</label>
                        </div>
                    </div>
                    {{-- PERIODE /BULAN --}}
                        <div id="baris_periode1" class="col-md-3">
                            <label for="periode1" class="form-label">Periode</label>
                            <input type="date" id="tanggal1" name="tanggal1" class="form-control">
                        </div>
                        <div id="baris_periode2" class="col-md-3">
                            <label for="periode2" class="form-label">&nbsp;</label>
                            <input type="date" id="tanggal2" name="tanggal2" class="form-control">
                            
                        </div>
                        <div id="baris_bulan" class="col-md-6">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select name="bulan" class="form-control select2" id="bulan">
                                <option value="">Silahkan Pilih</option>
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

                {{-- SKPD --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="jns_anggaran" class="form-label">Jenis Anggaran</label>
                        <select name="jns_anggaran" class="form-control select2" id="jns_anggaran">
                            <option value="" selected disabled>Silahkan Pilih</option>
                            @foreach ($jns_anggaran as $anggaran)
                                <option value="{{ $anggaran->kode }}" data-nama="{{ $anggaran->nama }}">
                                    {{ $anggaran->kode }} | {{ $anggaran->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="jenis_data" class="form-label">Jenis Data</label>
                        <select name="jenis_data" class="form-control select2" id="jenis_data">
                            <option value="">Silahkan Pilih</option>
                            <option value="1">SP2D terbit & SPJ Pendapatan</option>
                            <option value="2">SP2D Advice & SPJ Pendapatan</option>
                            <option value="3">SP2D Lunas & SPJ Pendapatan</option>
                            <option value="4">SPJ Fungsional & Pendapatan</option>
                            <option value="5">Jurnal</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="ttd" class="form-label">Tanda Tangan</label>
                        <select class="form-control select2 @error('ttd') is-invalid @enderror"
                            style=" width: 100%;" id="ttd" name="ttd">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                        @error('ttd')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- PA/KPA --}}
                    <div class="col-md-6">
                        <label for="tgl_ttd" class="form-label">Tanggal TTD</label>
                        <input type="date" id="tgl_ttd" name="tgl_ttd" class="form-control">
                    </div>
                </div>

                <div class="mb-3 row" id="row-hidden3">
                    <div class="col-md-6">
                        <label for="format" class="form-label">Format</label>
                        <select class="form-control select2" id="format" name="format">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="sap">SAP - Semester</option>
                            <option value="djpk">DJPK</option>
                            <option value="p77">Permendagri 77</option>
                            <option value="sng">Sinergi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="jns_rincian" class="form-label">Pilihan 1</label>
                        <select class="form-control select2" id="jns_rincian" name="jns_rincian">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="4">Jenis</option>
                            <option value="6">Obyek</option>
                            <option value="8">Rincian Obyek</option>
                            <option value="12">Sub Rincian Obyek</option>
                        </select>
                    </div>
                    {{-- <div class="col-md-3">
                        <label for="pilihkonversi" class="form-label">Pilihan 2</label>
                        <select class="form-control select2" id="pilihkonversi" name="pilihkonversi">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="konversi">Konversi</option>
                            <option value="tanpakonversi">Tanpa Konversi</option>
                        </select>
                    </div> --}}
                    <div class="col-md-3">
                        <label for="pilihakumulsai" class="form-label">Pilihan 3</label>
                        <select class="form-control select2" id="pilihakumulsai" name="pilihakumulsai">
                            <option value="" disabled selected>Silahkan Pilih</option>
                            <option value="akum">Dengan Akumulasi</option>
                            <option value="noakum">Tanpa Akumulasi</option>
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

<div id="modal_cetak_neraca" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak_semester" id="labelcetak_semester"></label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan SKPD/Unit --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">Pilih</label><br>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pilihan_neraca" id="pilihan0"
                                value="keseluruhan">
                            <label class="form-check-label" for="pilihan">Keseluruhan</label>
                        </div>
                        <div class=" form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pilihan_neraca" id="pilihan1"
                                value="skpd">
                            <label class="form-check-label" for="pilihan">SKPD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pilihan_neraca" id="pilihan2"
                                value="unit">
                            <label class="form-check-label" for="pilihan">Unit</label>
                        </div>
                    </div>
                    {{-- Bulan --}}
                    <div class="col-md-6" >
                        <div id="baris_skpd_neraca">
                            <label for="kd_skpd" class="form-label">Kode SKPD</label>
                            <select class="form-control select_neraca  @error('kd_skpd') is-invalid @enderror"
                                style=" width: 100%;" id="kd_skpd_neraca" name="kd_skpd">
                                <option value="" disabled selected>Silahkan Pilih</option>
                            </select>
                            @error('kd_skpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                    </div>
                </div>

                <div class="mb-3 row">
                    
                    {{-- PERIODE /BULAN --}}
                        

                        <div id="baris_bulan" class="col-md-6">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select name="bulan" class="form-control select_neraca" id="bulan">
                                <option value="">Silahkan Pilih</option>
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