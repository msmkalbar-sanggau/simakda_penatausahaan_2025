 <!-- edit -->
 <div id="edit-dialog" class="modal fade" tabindex="-1" role="dialog">
     <div class="modal-dialog modal-xl" role="document">
         <div class="modal-content">
             <div class="modal-header bg-secondary">
                 <h5 class="modal-title text-white">Penerimaan</h5>
             </div>
             <div class="modal-body">
                 <form id="edit-form" action="" method="POST">
                     @csrf
                     <div class="form-group mb-2">
                         <div class="form-check form-switch form-switch-m">
                             <input type="checkbox" class="form-check-input" id="pilihan_edit">
                             <label class="form-check-label" for="pilihan_edit">
                                 Dengan Penetapan</label>
                         </div>
                     </div>
                     <div class="form-group mb-2" id="dengan_penetapan1_edit">
                         <div class="mb-2 row">
                             <label for="no_tetap_edit" class="col-md-2 col-form-label-sm">No.
                                 Penetapan</label>
                             <div class="col-md-4">
                                 <select class="form-control form-control-sm" style="width: 100%" id="no_tetap_edit"
                                     name="no_tetap_edit">
                                     <option value="">-- Pilih -- </option>
                                 </select>
                             </div>
                             <label for="tgl_tetap_edit" class="col-md-2 col-form-label-sm">Tanggal
                                 Penetapan</label>
                             <div class="col-md-2">
                                 <input class="form-control form-control-sm" type="date" id="tgl_tetap_edit"
                                     name="tgl_tetap_edit">
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2" id="dengan_penetapan2_edit">
                         <div class="mb-2 row">
                             <label for="nilai_tetap_edit" class="col-md-2 col-form-label-sm">Nilai
                                 Penetapan</label>
                             <div class="col-md-4">
                                 <input id="nilai_tetap_edit" type="text" class="form-control form-control-sm"
                                     name="nilai_tetap_edit" placeholder="Nilai" disabled>
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2">
                         <div class="mb-2 row">
                             <label for="no_terima_edit" class="col-md-2 col-form-label-sm">No Terima</label>
                             <div class="col-md-4">
                                 <input id="no_terima_edit" type="text" class="form-control form-control-sm"
                                     name="no_terima_edit" placeholder="No Terima">
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2">
                         <div class="mb-2 row">
                             <label for="jns_edit" class="col-md-2 col-form-label-sm">Jenis Setor</label>
                             <div class="col-md-2">
                                 <div class="form-check form-switch form-switch-m">
                                     <input type="checkbox" class="form-check-input" name="status_setor_edit"
                                         id="tanpa_setor_edit" value="Tanpa Setor" onclick="">
                                     <label class="form-check-label col-form-label-sm" for="tanpa_setor_edit">
                                         Tanpa Setor
                                     </label>
                                 </div>
                             </div>
                             <div class="col-md-2">
                                 <div class="form-check form-switch form-switch-m">
                                     <input type="checkbox" class="form-check-input" name="status_setor_edit"
                                         id="dengan_setor_edit" value="Dengan Setor" onclick="">
                                     <label class="form-check-label col-form-label-sm" for="dengan_setor_edit">
                                         Dengan Setor
                                     </label>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2" id="jns_bayar_edit">
                         <div class="mb-2 row">
                             <label for="jenis_pembayaran_edit" class="col-md-2 col-form-label-sm">Jenis
                                 Pembayaran</label>
                             <div class="col-md-4">
                                 <select id="jenis_pembayaran_edit" name="jenis_pembayaran_edit"
                                     class="form-control form-control-sm">
                                     <option value="TUNAI"> Tunai</option>
                                     <option value="BANK"> Bank</option>
                                 </select>
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2">
                         <div class="mb-2 row">
                             <label for="tanggal_edit" class="col-md-2 col-form-label-sm">Tanggal</label>
                             <div class="col-md-4">
                                 <input id="tanggal_edit" type="date" class="form-control form-control-sm"
                                     name="tanggal_edit" placeholder="" required>
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2">
                         <div class="mb-2 row">
                             <label for="rekening_edit" class="col-md-2 col-form-label-sm">Rekening</label>
                             <div class="col-md-4">
                                 <select id="rekening_edit" name="rekening_edit"
                                     class="form-control form-control-sm">
                                     <option value="">-- Pilih -- </option>
                                 </select>
                                 <input id="kd_rek_lo_edit" type="text" class="form-control form-control-sm"
                                     name="kd_rek_lo_edit" placeholder="" hidden>
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2" id="jns_pajak_edit">
                         <div class="mb-2 row">
                             <label for="jenispajak_edit" class="col-md-2 col-form-label-sm">Jenis
                                 Pajak</label>
                             <div class="col-md-4">
                                 <select id="jenispajak_edit" name="jenispajak_edit"
                                     class="form-control form-control-sm">
                                     <option value="">-- Pilih -- </option>

                                 </select>
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2">
                         <div class="mb-2 row">
                             <label for="nilai_terima_edit" class="col-md-2 col-form-label-sm">Nilai
                                 Penerimaan</label>
                             <div class="col-md-4">
                                 <input id="nilai_terima_edit" type="text" class="form-control form-control-sm"
                                     name="nilai_terima_edit" placeholder="Nilai">
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2">
                         <div class="mb-2 row">
                             <label for="ket_edit" class="col-md-2 col-form-label-sm">Keterangan</label>
                             <div class="col-md-4">
                                 <textarea class="form-control" id="ket_edit" name="ket_edit"></textarea>
                             </div>
                         </div>
                     </div>
                     <div class="form-group mb-2">
                         <div class="mb-2 row">
                             <label for="kdkegiatan_edit" class="col-md-2 col-form-label-sm">Sub
                                 Kegiatan</label>
                             <div class="col-md-4">
                                 <input type="text" class="form-control form-control-sm" id="kdkegiatan_edit"
                                     name="kdkegiatan_edit">
                             </div>
                         </div>
                     </div>
                 </form>
             </div>
             <div class=" modal-footer">
                 <button id="edit-btn" class="btn btn-sm btn-primary">Update</button>
                 <button id="keluar-edit" type="button" class="btn btn-sm btn-secondary"
                     data-dismiss="modal">Close</button>
             </div>
         </div>
     </div>
 </div>
 <!-- End edit -->
