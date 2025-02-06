<script>
    $(document).ready(function() {
        let kd_skpd = document.getElementById('kd_skpd').value;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.dengan_penetapan').hide();
        $('#jns_pem1').hide();
        $('#jns_pem2').hide();
        $('.pajak_hotel').hide();
        $('.pajak_mineral').hide();


        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#jenis_pembayaran_tambah').select2({
            width: '100%',
            dropdownParent: $("#tambah-dialog"),
            theme: 'bootstrap-5'
        })

        // if (kd_skpd == '5.02.0.00.0.00.03.0000') {
        //     $('.jns_penerimaan').show();

        //     $('#jns_penerimaan').on('select2:select', function() {
        //         let nomor = this.value;
        //         let jenis;

        //         if (nomor == 1) {
        //             jenis = "/SKP BAPENDA/2024";
        //         }
        //         if (nomor == 2) {
        //             jenis = "/STS KASDA/2024";
        //         }
        //         if (nomor == 3) {
        //             jenis = "/BPHTB BAPENDA/2024";
        //         }
        //         if (nomor == 4) {
        //             jenis = "/PBB BAPENDA/2024";
        //         }
        //         if (nomor == 5) {
        //             jenis = "/PBB KASDA/2024";
        //         }


        //         let nomor1 = jenis;
        //         $('#no_terima').val(nomor1);

        //     });
        // } else {
        //     $('.jns_penerimaan').hide();
        // }

        let data = false;
        $('#pilihan').on('change', function() {
            if ($(this).is(':checked')) {
                data = $(this).is(':checked');
                if (data == true) {
                    $('#no_tetap').val(null).change();
                    $('#tgl_tetap').val(null);
                    $('#nilai_tetap').val(null);
                    $('#nilai').val(null);
                    $('#kode_akun').val(null).change();
                    $('#nama_akun').val(null);
                    $('#kd_sub_kegiatan').val(null);
                    $('.dengan_penetapan').show();
                }
            } else {
                data = $(this).is(':checked');
                if (data == false) {
                    $('#no_tetap').val(null).change();
                    $('#tgl_tetap').val(null);
                    $('#nilai_tetap').val(null);
                    $('#nilai').val(null);
                    $('#kode_akun').val(null).change();
                    $('#nama_akun').val(null);
                    $('#kd_sub_kegiatan').val(null);
                    $('.dengan_penetapan').hide();
                }
            }
        });

        $('#kode_akun').on('select2:select', function() {
            let kd_sub_kegiatan = $(this).find(':selected').data('kd_sub_kegiatan');
            let nm_rek = $(this).find(':selected').data('nm_rek').toUpperCase();
            let kd_rek = $(this).find(':selected').data('kd_rek');
            let kd_rek6 = $(this).find(':selected').data('kd_rek6');
            $('#kd_sub_kegiatan').val(kd_sub_kegiatan);
            $('#nama_akun').val(nm_rek);
            $('#kode_rek').val(kd_rek);
            $('#kode_rek6').val(kd_rek6);

            if (kd_rek6 == '410106010001') {
                $('.pajak_hotel').show();
                // $('.pajak_mineral').hide();
            } else if (kd_rek6 == '410114370001') {
                $('.pajak_hotel').hide();
                // $('.pajak_mineral').show();
            } else {
                $('.pajak_hotel').hide();
                // $('.pajak_mineral').hide();
            }
        });

        $('#no_tetap').on('select2:select', function() {
            let tgl = $(this).find(':selected').data('tgl');
            let nilai = $(this).find(':selected').data('nilai');
            let kode = $(this).find(':selected').data('kode');
            let nmakun = $(this).find(':selected').data('nmakun');
            let kd_rek_lo = $(this).find(':selected').data('kd_rek_lo');
            let kd_rek6 = $(this).find(':selected').data('kd_rek6');
            let giat = $(this).find(':selected').data('kd_sub_kegiatan');
            $('#tgl_tetap').val(tgl);
            $('#nama_akun').val(nmakun);
            $('#kd_sub_kegiatan').val(giat);
            $('#kode_rek').val(kd_rek_lo);
            $('#kode_rek6').val(kd_rek6);
            $('#kode_akun').val(kode).trigger('change');
            $('#nilai_tetap').val(nilai);
            $('#nilai').val(nilai);
        });


        $('#simpan').on('click', function() {
            let no_terima = document.getElementById('no_terima').value;
            let tgl_terima = document.getElementById('tgl_terima').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let kode_akun = document.getElementById('kode_akun').value;
            let nama_akun = document.getElementById('nama_akun').value;
            let kode_rek = document.getElementById('kode_rek').value;
            let kode_rek6 = document.getElementById('kode_rek6').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let nilai = angka(document.getElementById('nilai').value);
            let nilai_tetap = angka(document.getElementById('nilai_tetap').value);
            let no_tetap = document.getElementById('no_tetap').value;
            let tgl_tetap = document.getElementById('tgl_tetap').value;
            let tahun_input = tgl_terima.substr(0, 4);
            let pajak_hotel = document.getElementById('pajak_hotel').value;
            // let pajak_mineral = document.getElementById('pajak_mineral').value;
            let statusSetor = $('input[name="status_setor"]:checked').val();
            let jns_pembayaran = document.getElementById('jenis_pembayaran_tambah').value;
            let pilihan = document.getElementById('pilihan').checked;
            let dengan_penetapan;
            let pajakk;

            // if (kode_rek6 == '410106010001') {
            //     pajakk = document.getElementById('pajak_hotel').value;
            // } else if (kode_rek6 == '410114370001') {
            //     pajakk = document.getElementById('pajak_mineral').value;
            // } else {
            //     pajakk = '';
            // }

            if (kode_rek6 == '410106010001') {
                pajakk = document.getElementById('pajak_hotel').value;
            } else {
                pajakk = '';
            }

            if (pilihan == false) {
                dengan_penetapan = 0;
            } else {
                dengan_penetapan = 1;
            }

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!no_terima) {
                alert('No Terima Tidak Boleh Kosong');
                return;
            }

            if (!tgl_terima) {
                alert('Tanggal Tidak Boleh Kosong');
                return;
            }

            if (!kd_skpd) {
                alert('Kode SKPD Tidak Boleh Kosong');
                return;
            }

            if (!kode_akun) {
                alert('Kode Akun Tidak Boleh Kosong');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }

            if (nilai == '0') {
                alert('Nilai 0!Cek Lagi!!!');
                return;
            }

            if (statusSetor == undefined) {
                alert('Pilih Jenis Setor Dulu!!')
                return;
            }

            if (pilihan == true) {
                if (dengan_penetapan = 1) {
                    if (tgl_terima < tgl_tetap) {
                        alert('Tanggal penerimaan tidak boleh lebih kecil dari tanggal penetapan !');
                        return;
                    }
                    if (nilai_tetap < nilai) {
                        alert('Nilai penerimaan tidak boleh melebihi nilai penetapan !');
                        return;
                    }
                }
            }



            let data = {
                dengan_penetapan,
                pajakk,
                jns_pembayaran,
                no_terima,
                tgl_terima,
                no_tetap,
                statusSetor,
                tgl_tetap,
                kd_skpd,
                nm_skpd,
                kode_akun,
                nama_akun,
                kode_rek,
                kd_sub_kegiatan,
                keterangan,
                nilai,
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('penerimaan_ini.cek_simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_terima: no_terima,
                },
                success: function(response) {
                    if (response == 1) {
                        alert("Nomor Telah Dipakai!");
                        $('#simpan').prop('disabled', false);
                    } else {
                        alert("Nomor Bisa dipakai");
                        simpan_penerimaan(data);
                    }
                }
            })
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

    function simpan_penerimaan(data) {
        $.ajax({
            url: "{{ route('penerimaan_ini.simpan') }}",
            type: "POST",
            dataType: 'json',
            data: {
                data: data,
            },
            success: function(response) {
                if (response.message == '1') {
                    alert('Data berhasil disimpan!');
                    window.location.href =
                        "{{ route('penerimaan_ini.index') }}";
                } else {
                    alert('Data gagal disimpan!');
                    $('#simpan').prop('disabled', false);
                }
            }
        })
    }

    function opt(val) {
        ctk = val;
        if (ctk == "Dengan Setor") {
            $("#tanpa_setor").prop("checked", false);
            $("#jenis_pembayaran_tambah").val("TUNAI").change();
            $("#jenis_pembayaran_tambah").prop("disabled", true);
        } else if (ctk == "Tanpa Setor") {
            $("#dengan_setor").prop("checked", false);
            $("#jenis_pembayaran_tambah").val("BANK").change();
            $("#jenis_pembayaran_tambah").prop("disabled", true);
        }
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
</script>
