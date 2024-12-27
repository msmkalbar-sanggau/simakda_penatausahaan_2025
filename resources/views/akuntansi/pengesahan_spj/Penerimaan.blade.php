@extends('template.app')
@section('title', 'Pengesahan SPJ Penerimaan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Pengesahan SPJ Penerimaan
                    
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        
                        <div class="col-md-2">
                            <select name="bulan" class="form-control" id="bulan">
                                <option value="">Silahkan Pilih Bulan</option>
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
                </div>
                <div class="mb-3 row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-danger btn-md bku_pdf" data-jenis="pdf"
                            name="bku_pdf"> PDF</button>
                        <button type="button" class="btn btn-dark btn-md bku_layar" data-jenis="layar"
                            name="bku_layar">Layar</button>
                        <button type="button" class="btn btn-success btn-md bku_excel" data-jenis="excel"
                            name="bku_excel">Excel</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="penerimaan_spj" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">Kode SKPD</th>
                                        <th style="width: 50px;text-align:center">Terima</th>
                                        <th style="width: 50px;text-align:center">Setor</th>
                                        <th style="width: 50px;text-align:center">Sisa</th>
                                        <th style="width: 50px;text-align:center">Tanggal Terima</th>
                                        <th style="width: 50px;text-align:center">Keterangan</th>
                                        <th style="width: 50px;text-align:center">cek</th>
                                        <th style="width: 200px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

   
    {{-- modal cetak SPJ  --}}
{{-- @include('akuntansi.modal.lrasap') --}}
@include('akuntansi.modal.pengesahan_spj.penerimaan_spj')
    {{-- modal cetak Sub Rincian Objek  --}}
@endsection
@section('js')
    <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });

        let data=$('#penerimaan_spj').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 20],
            ajax: {
                "url": "{{ route('pengesahan_spj.load_penerimaan') }}",
                "type": "POST",
                "data": function(d) {
                    d.bulan = document.getElementById('bulan').value
                }
            },
            columns: [{
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: null,
                    name: 'real_terima',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.real_terima)
                    }
                }, 
                {
                    data: null,
                    name: 'real_setor',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.real_setor)
                    }
                }, 
                {
                    data: null,
                    name: 'sisa',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.sisa)
                    }
                },
                {
                    data: 'tgl_terima',
                    name: 'tgl_terima',
                    className: "text-center",
                },
                {
                    data: 'ket',
                    name: 'ket',
                    className: "text-center",
                },
                {
                    data: 'cek',
                    name: 'cek',
                    className: "text-center",
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
            ],
        });

        $('#bulan').on('change',function(){
            let bulan = this.value
            data.ajax.reload()
        });


    });

    function keluar(){
        $("#modal_penerimaan_spj").dialog('close');
        lcstatus = 'edit';
     }

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }

    function formatCurrency(input, blur) {
        // appends $ to value, validates decimal side
        // and puts cursor back in right position.

        // get input value
        var input_val = input.val();

        // don't validate empty input
        if (input_val === "") {
            return;
        }

        // original length
        var original_len = input_val.length;

        // initial caret position
        var caret_pos = input.prop("selectionStart");

        // check for decimal
        if (input_val.indexOf(".") >= 0) {

            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(".");

            // split number by decimal point
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            // add commas to left side of number
            left_side = formatNumber(left_side);

            // validate right side
            right_side = formatNumber(right_side);

            // On blur make sure 2 numbers after decimal
            if (blur === "blur") {
                right_side += "00";
            }

            // Limit decimal to only 2 digits
            right_side = right_side.substring(0, 2);

            // join number by .
            input_val = left_side + "." + right_side;

        } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // final formatting
            if (blur === "blur") {
                input_val += ".00";
            }
        }

        // send updated string to input
        input.val(input_val);

        // put caret back in the right position
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }

    function angka(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function edit(kd_skpd, nm_skpd, tgl_terima, real_terima, real_setor, sisa, spj, bku,koran, sts, ket, cek) {
        $('#kd_skpd').val(kd_skpd);
        $('#nm_skpd').val(nm_skpd);
        $('#tgl_terima').val(tgl_terima);
        $('#real_terima').val(real_terima);
        $('#real_setor').val(real_setor);
        $('#sisa').val(sisa);
        // $('#spj').val(spj);
        spj == 1 ? $('#spj').prop('checked', true) : $('#spj').prop('checked', false);
        // $('#bku').val(bku);
        bku == 1 ? $('#bku').prop('checked', true) : $('#bku').prop('checked', false);
        // $('#koran').val(koran);
        koran == 1 ? $('#koran').prop('checked', true) : $('#koran').prop('checked', false);
        // $('#sts').val(sts);
        sts == 1 ? $('#sts').prop('checked', true) : $('#sts').prop('checked', false);
        $('#ket').val(ket);
        // $('#cek').val(cek);
        cek == 1 ? $('#cek').prop('checked', true) : $('#cek').prop('checked', false);
        
        $('#modal_penerimaan_spj').modal('show');
    }

        $('.bku_layar').on('click', function() {
            Cetak(1)
        });
        $('.bku_pdf').on('click', function() {
            Cetak(2)
        });
        $('.bku_excel').on('click', function() {
            Cetak(3)
        });
    function Cetak(jns_cetak) {

            // GET DATA
            let bulan                    = document.getElementById('bulan').value;
            // alert(labelcetak_semester)
            // PERINGATAN
                if (!bulan) {
                    alert('Bulan tidak boleh kosong!');
                    return;
                }

            // SET CETAKAN

                let url             = new URL("{{ route('pengesahan_spj.cetak_penerimaan_spj') }}");
                let searchParams    = url.searchParams;
                searchParams.append("bulan", bulan);
                searchParams.append("cetak", jns_cetak);
                window.open(url.toString(), "_blank");
            

    }
    function detail(kd_skpd, nm_skpd, tgl_terima, real_terima, real_setor, sisa, spj, bku,koran, sts, ket, cek) {
        $('#kd_skpd').val(kd_skpd);
        $('#nm_skpd').val(nm_skpd);
        $('#tgl_terima').val(tgl_terima);
        $('#real_terima').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(real_terima));
        $('#real_setor').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(real_setor));
        $('#sisa').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(sisa));
        $('#spj').val(spj);
        $('#bku').val(bku);
        $('#koran').val(koran);
        $('#sts').val(sts);
        $('#ket').val(ket);
        $('#cek').val(cek);
    }

    function simpan_pengesahan(){
        let cbulan = document.getElementById('bulan').value;
        let ckd = document.getElementById('kd_skpd').value;
        let ctgl_terima = document.getElementById('tgl_terima').value;
        let real_terima = angka(document.getElementById('real_terima').value);
        let real_setor = angka(document.getElementById('real_setor').value);
        let sisa = angka(document.getElementById('sisa').value);
        
        let cspj = document.getElementById('spj').checked;
        if (cspj==false){
           cspj=0;
        }else{
            cspj=1;
        }

        let cbku = document.getElementById('bku').checked;
        if (cbku==false){
           cbku=0;
        }else{
            cbku=1;
        }

        let ckoran = 0;
        if (ckoran==false){
           ckoran=0;
        }else{
            ckoran=1;
        }
        
        let csts = document.getElementById('sts').checked;
        if (csts==false){
           csts=0;
        }else{
            csts=1;
        }
        let ccek = document.getElementById('cek').checked;
        if (ccek==false){
           ccek=0;
        }else{
            ccek=1;
        }
         tot=cspj+cbku+csts;
         
         if((tot!=3) && (ccek==1)){
            alert('Persyaratan belum lengkap. Tidak bisa mencentang Cek');
            exit(); 
         }
        
        let cket = document.getElementById('ket').value;
        if (ckd==''){
            alert('SKPD Tidak Boleh Kosong');
            exit();
        }
        
        
        
            $(document).ready(function(){
                $.ajax({
                    type: "POST",
                    url: "{{ route('pengesahan_spj.simpan_penerimaan_spj') }}",
                    data: ({tabel:'trhspj_terima_ppkd',kdskpd:ckd,real_terima:real_terima,real_setor:real_setor,sisa:sisa,tgl_terima:ctgl_terima,spj:cspj,bku:cbku,koran:ckoran,sts:csts,ket:cket,cek:ccek,bulan:cbulan}),
                    dataType:"json"
                });
            });

        alert("Data Berhasil disimpan");
        //$("#dialog-modal").dialog('close');
        //$('#dg').edatagrid('reload');
    }
</script>
@endsection
