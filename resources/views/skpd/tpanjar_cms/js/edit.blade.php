<script>
         $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#panjar').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [8, 20, 50, 100],
                ajax: {
                    "url": "{{ route('tpanjar_cms.load_data') }}",
                    "type": "POST",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'no_panjar',
                        name: 'no_panjar'
                    },
                    {
                        data: 'tgl_panjar',
                        name: 'tgl_panjar'
                    },
                    {
                        data: 'kd_skpd',
                        name: 'kd_skpd'
                    },
                    {
                    data: null,
                    name: 'nilai',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
                    }
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        width: '200px',
                        className: 'text-center'
                    },
                ],
            });

            let tabel_tujuan = $('#rekening_tujuan').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_bukti',
                    name: 'no_bukti',
                    visible: false
                },
                {
                    data: 'tgl_voucher',
                    name: 'tgl_voucher',
                    visible: false
                },
                {
                    data: 'rekening_awal',
                    name: 'rekening_awal',
                    visible: false
                },
                {
                    data: 'nm_rekening_tujuan',
                    name: 'nm_rekening_tujuan',
                },
                {
                    data: 'rekening_tujuan',
                    name: 'rekening_tujuan',
                },
                {
                    data: 'nm_bank',
                    name: 'nm_bank',
                    visible: false
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    visible: false
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
            })

            $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
            });

            $('#tambah_rek_tujuan').on('click', function() {
            $('#modal_rekening').modal('show');
            });

            $('#rek_tujuan').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $("#nm_rekening_tujuan").val(nama);
        });
        
            $('.select2-modal1').select2({
                dropdownParent: $('#modal_rekening .modal-content'),
                theme: 'bootstrap-5'
            });
            
            load_sisa_bank();
            load_sisa_ang();

            
            // Oncahnge Kode Sub Kegiatan
        $('#kd_sub_kegiatan').on('change', function() {
            let nm_sub_kegiatan = $('#kd_sub_kegiatan option:selected').data('nm_sub_kegiatan');
                $('#nm_sub_kegiatan').val(nm_sub_kegiatan).change();
            load_sisa_ang();
            hitung();
        });

        $('#no_panjar_lalu').on('change', function() {
                let nilai = parseFloat($(this).find(':selected').data('nilai')) || 0;
            });

          $('#simpan_panjar').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let keterangan = document.getElementById('keterangan').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let rek_awal = document.getElementById('rekening').value;
            let rek_tujuan = document.getElementById('rek_tujuan').value;
            let nm_bank = document.getElementById('nm_bank').value;
            let no_tpanjar = document.getElementById('no_panjar').value;
            let nm_rekening_tujuan = document.getElementById('nm_rekening_tujuan').value;
            // let sp2d = document.getElementById('sp2d_sementara').value;
            let total_belanja = angka(document.getElementById('nilai').value);
            let nil_pot1 = angka(document.getElementById('nil_pot2').value);
            let sisabank = rupiah(document.getElementById('sisabank').value);
            let sisa_ang = rupiah(document.getElementById('nilaiang').value);
            let nilai_potongan = angka(document.getElementById('nilpotongan').value);
            let nilai_transfer = angka(document.getElementById('nilai_transfer').value);

            let total_transfer = rupiah(document.getElementById('total_transfer').value);

            let ket_tujuan = "TPNJR.KEG."+kd_sub_kegiatan;

//             alert(sisabank);
//             alert(total_belanja);

//             alert(sisa_ang);
//             alert(total_belanja);
// return
            let rincian_rek_tujuan = tabel_tujuan.rows().data().toArray().map((value) => {
                    let data = {
                        no_bukti: value.no_bukti,
                        tgl_voucher : value.tgl_voucher,
                        rekening_awal : value.rekening_awal,
                        nm_rekening_tujuan: value.nm_rekening_tujuan,
                        rekening_tujuan: value.rekening_tujuan,
                        bank_tujuan: value.nm_bank,
                        kd_skpd: value.kd_skpd,
                        nilai: rupiah(value.nilai),
                    };
                    return data;
                });
        

            let tahun_input = tgl_voucher.substr(0, 4);
            
            if (total_transfer!=total_belanja-nil_pot1){
    			alert('Nilai Transfer Tidak Sama Dengan Nilai Panjar + Pajak');
                return;
    	    }

            if (sisa_ang<total_belanja){
    			alert('Tidak boleh melebihi sisa Anggaran');
                return;
    		}

            if (sisabank<total_belanja){
    			alert('Tidak boleh melebihi sisa Kas Bank');
                return;
    		}

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!no_bukti) {
                alert('Nomor Bukti Tidak Boleh Kosong');
                return;
            }

            if (!tgl_voucher) {
                alert('Tanggal Bukti Tidak Boleh Kosong');
                return;
            }

            if (!kd_sub_kegiatan) {
                alert('Silahkan Pilih Kegiatan Terlebih Dulu');
                return;
            }

            if (!pembayaran) {
                alert('Jenis Pembayaran Tidak Boleh Kosong');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }

            if (total_belanja == 0) {
                alert("Nilai 0...Cek Lagi!");
                return;
            }
            
            let data = {
                no_bukti,
                tgl_voucher,
                kd_skpd,
                total_belanja,
                keterangan,
                no_tpanjar,
                ket_tujuan,
                pembayaran,
                kd_sub_kegiatan,
                rek_awal,
                rincian_rek_tujuan
            };
            
            //PROSES UPDATE DATA PANJAR
            $.ajax({
                url: "{{ route('tpanjar_cms.update') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data,
                },
                success: function(response) {
                    if (response == '1') {
                        alert('Data Gagal Tersimpan...!!!');
                        return;
                    } else {
                        alert('Data Berhasil Tersimpan...!!!');
                        window.location.href = "{{ route('tpanjar_cms.index') }}";
                    }
                }
            })
        });

        $('#simpan_rekening_tujuan').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let rek_awal = document.getElementById('rekening').value;
           // let rekening = document.getElementById('rekening').value;
            let nm_rekening_tujuan = document.getElementById('nm_rekening_tujuan').value;
            let rek_tujuan = document.getElementById('rek_tujuan').value;
            let nm_bank = document.getElementById('nm_bank').value;
            let total_belanja = angka(document.getElementById('nilai').value);
            let total_transfer = rupiah(document.getElementById('total_transfer').value);
            //let total_potongan = rupiah(document.getElementById('total_potongan').value);
            let nilai_potongan = angka(document.getElementById('nilpotongan').value);
            let nilai_transfer = angka(document.getElementById('nilai_transfer').value);

            let hasil_akumulasi = total_belanja;
            let akumulasi = total_transfer + nilai_transfer;
 
            let tampungan = tabel_tujuan.rows().data().toArray().map((value) => {
                let result = {
                    rekening_tujuan: value.rekening_tujuan,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.rekening_tujuan == rek_tujuan) {
                    return '2';
                } else {
                    return '1';
                }
            });
            if (kondisi.includes("2")) {
                alert('Tidak boleh memilih rekening yang sama');
                return;
            }
               
            if (nilai_transfer == 0) {
                alert("Nilai Tidak Boleh Nol");
                return;
            }

            if (akumulasi > hasil_akumulasi) {
                alert('Nilai Melebihi Total Belanja 1');
                return;
            }

            if (nilai_transfer > hasil_akumulasi) {
                alert('Nilai Melebihi Total Belanja 2');
                return;
            }

            if (total_transfer > hasil_akumulasi) {
                alert('Nilai Melebihi Total Belanja 3');
                return;
            }

            if (!rek_tujuan) {
                alert('Pilih rekening tujuan');
                return;
            }

            if (!nm_bank) {
                alert('Pilih bank');
                return;
            }


            tabel_tujuan.row.add({
                'no_bukti' : no_bukti,
                'tgl_voucher' : tgl_voucher,
                'rekening_awal' : rek_awal,
                'nm_rekening_tujuan': nm_rekening_tujuan,
                'rekening_tujuan': rek_tujuan,
                'nm_bank' : nm_bank,
                'kd_skpd' : kd_skpd,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai_transfer),
                'aksi': `<a href="javascript:void(0);" onclick="deleteDetail('${nm_rekening_tujuan}','${rek_tujuan}','${nilai_transfer}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();
            
            $('#total_transfer').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transfer + nilai_transfer));

            $('#nilpotongan').val(null);
            $('#rek_tujuan').val(null).change();
            $('#nm_rekening_tujuan').val(null);
            $('#nm_bank').val(null).change();
            $('#nilai_transfer').val(null);
            $('#modal_rekening').modal('hide');
        });

        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });       
    });

    

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

    function hitung(){
        let total_belanja = angka(document.getElementById('nilai').value);
        let total_transfer = rupiah(document.getElementById('total_transfer').value);

        let itung = total_transfer-total_belanja;
        $('#nil_pot2').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(itung));
        
   }

        function load_sisa_bank() {
             let nm_sub_kegiatan = $('#kd_sub_kegiatan option:selected').data('nm_sub_kegiatan');
            $('#nm_sub_kegiatan').val(nm_sub_kegiatan).change();
            $.ajax({
                url: "{{ route('tpanjar_cms.sisaBank') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    let sisa = parseFloat(data) || 0;
                    $('#sisabank').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(sisa));
                }
            })
        }
        
        function load_sisa_ang(){
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            // alert(kd_sub_kegiatan);
            // return;
            let no_bukti = document.getElementById('no_bukti').value;
            $.ajax({
                url: "{{ route('tpanjar_cms.sisa_ang') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    no_bukti : no_bukti,
                },
                success: function(data) {
                    let sisa = parseFloat(data.sisa);
                    // alert(sisa);
                    // return;
                    let tran = parseFloat(data.transaksi) || 0;

                    $('#nilaiang').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(sisa));
                }
            });
        }

        function deleteRek(nm_rekening_tujuan, rek_tujuan,nilai_transfer) {
        let tabel = $('#rekening_tujuan').DataTable();
        let total_transfer = rupiah(document.getElementById('total_transfer').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + nm_rekening_tujuan +
            ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.rekening_tujuan == rek_tujuan
            }).remove().draw();
            $('#total_transfer').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transfer - parseFloat(nilai_transfer)));
        } else {
            return false;
        }
    }
    </script>