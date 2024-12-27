{{-- modal cetak PERDA --}}


<div id="modal_cetak_lbb" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label for="labelcetak_semester" id="labelcetak_semester"></label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                

                <div class="mb-3 row">
                    
                    {{-- PERIODE --}}
                        

                        <div id="baris_periode1" class="col-md-3">
                            <label for="periode1" class="form-label">Periode</label>
                            <input type="date" id="tanggal1" name="tanggal1" class="form-control">
                        </div>
                        <div id="baris_periode2" class="col-md-3">
                            <label for="periode2" class="form-label">&nbsp;</label>
                            <input type="date" id="tanggal2" name="tanggal2" class="form-control">
                            
                        </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="kd_skpd" class="form-label">SKPD</label>
                        <select class="form-control select_lbb @error('rek6') is-invalid @enderror"
                            style=" width: 100%;" id="kd_skpd" name="kd_skpd">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                        @error('kd_skpd')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="rek6" class="form-label">Rekening</label>
                        <select class="form-control select_lbb @error('rek6') is-invalid @enderror"
                            style=" width: 100%;" id="rek6" name="rek6">
                            <option value="" disabled selected>Silahkan Pilih</option>
                        </select>
                        @error('rek6')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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