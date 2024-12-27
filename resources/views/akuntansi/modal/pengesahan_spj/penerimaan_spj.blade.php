{{-- modal cetak PERDA --}}


<div id="modal_penerimaan_spj" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title"><label  >Edit Data Pengesahan DPA & DPPA</label></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <table align="center" style="width:100%;" border="0">
            <tr>
                <td width="30%">SKPD</td>
                <td width="1%">:</td>
                <td><input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                ><input class="form-control" type="text" id="nm_skpd" name="nm_skpd" required readonly
                                ></td>
            </tr> 
            <tr>
                <td width="30%">TGL Terima</td>
                <td width="1%">:</td>
                <td><input type="date" id="tgl_terima" name="tgl_terima" class="form-control"></td>  
            </tr>
            <tr>
                <td width="30%">Realisasi Peneriman</td>
                <td width="1%">:</td>
                <td><input type="text" class="form-control" name="real_terima" id="real_terima"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                </td>  
            </tr>
            <tr>
                <td width="30%">Realisasi Penyetoran</td>
                <td width="1%">:</td>
                <td><input type="text" class="form-control" name="real_setor" id="real_setor"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right"></td>  
            </tr>
            <tr>
                <td width="30%">Sisa</td>
                <td width="1%">:</td>
                <td><input type="text" class="form-control" name="sisa" id="sisa"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right"></td>  
            </tr>
            
            <tr>
            <td width="30%">SPJ</td>
            <td width="1%">:</td>
            <td><input type="checkbox" id="spj" /></td>
            </tr>
            <tr>
            <td width="30%">Buku Kas Penerimaan</td>
            <td width="1%">:</td>
            <td><input type="checkbox" id="bku" /></td>
            </tr>
            <tr>
            <td width="30%">STS</td>
            <td width="1%">:</td>
            <td><input type="checkbox" id="sts"  /></td>
            </tr>
            <tr>
                <td width="30%">Keterangan</td>
                <td width="1%">:</td>
                <td><div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="ket" name="ket"></textarea>
                        </div></td>  
            </tr>
            <tr>
            <td width="30%">CEK</td>
            <td width="1%">:</td>
            <td><input type="checkbox" id="cek" /></td>
            </tr>
            <tr>
            <td colspan="5">&nbsp;</td>
            </tr>            
            <tr>
                <td colspan="5" align="center">
                    <button id="simpan" class="btn btn-md btn-primary" onclick="javascript:simpan_pengesahan();">Simpan</button>
                    <button type="button" class="btn btn-dark btn-md" data-bs-dismiss="modal">Keluar</button>
                </td>                
            </tr>
        </table>
        </div>
        </div>
    </div>
</div>