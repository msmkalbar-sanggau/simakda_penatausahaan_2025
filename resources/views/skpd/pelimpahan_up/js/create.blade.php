<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#kd_skpd').on('select2:select', function() {
            let skpd_sumber = $(this).find(':selected').data('skpd_sumber');
            let skpd_ringkas = $(this).find(':selected').data('skpd_ringkas');
            let nm_skpd = $(this).find(':selected').data('nm_skpd');
            let nilai = $(this).find(':selected').data('nilai');
            $('#nm_skpd').val(nm_skpd);
            $('#skpd_sumber').val(skpd_sumber);
            $('#skpd_ringkas').val(skpd_ringkas);
            $('#nilai').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
        });

        $('#beban').on('select2:select', function() {
            let beban = this.value;
            let skpd_ringkas = document.getElementById('skpd_ringkas').value;

            if (!skpd_ringkas) {
                alert('Pilih Tujuan SKPD Terlebih Dahulu!');
                $('#beban').val(null).change();
                return;
            }

            if (beban == '1') {
                $('#ketcms').val('DROP.' + 'UP/GU' + '.' + skpd_ringkas);
            } else if (beban == '3') {
                $('#ketcms').val('DROP.' + 'TU' + '.' + skpd_ringkas);
            } else if (beban == '4' || beban == '6' || beban == '5') {
                $('#ketcms').val('DROP.' + 'LS' + '.' + skpd_ringkas);
            }

        });

        $('#rekening_tujuan').on('select2:select', function() {
            let nm_rekening = $(this).find(':selected').data('nm_rekening');
            let nm_bank = $(this).find(':selected').data('nm_bank');
            $('#bank_tujuan').val(nm_bank);
            $('#nama_tujuan').val(nm_rekening);
        });

        $('#simpan_pelimpahan').on('click', function() {
            let no_kas = document.getElementById('no_kas').value;
            let tgl_kas = document.getElementById('tgl_kas').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let skpd_sumber = document.getElementById('skpd_sumber').value;
            let skpd_ringkas = document.getElementById('skpd_ringkas').value;
            let keterangan = document.getElementById('keterangan').value;
            let beban = document.getElementById('beban').value;
            let sisa_kas = rupiah(document.getElementById('sisa_kas').value);
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let ketcms = document.getElementById('ketcms').value;
            let rekening_bendahara = document.getElementById('rekening_bendahara').value;
            let rekening_tujuan = document.getElementById('rekening_tujuan').value;
            let nama_tujuan = document.getElementById('nama_tujuan').value;
            let bank_tujuan = document.getElementById('bank_tujuan').value;
            let nilai = rupiah(document.getElementById('nilai').value);
            let tahun_input = tgl_kas.substr(0, 4);

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            // if (nilai > sisa_kas) {
            //     alert('Nilai Lebih Besar dari Sisa Bank');
            //     return;
            // }

            if (!tgl_kas) {
                alert('Tanggal  Tidak Boleh Kosong');
                return;
            }

            if (!kd_skpd) {
                alert('Kode SKPD Tidak Boleh Kosong');
                return;
            }

            if (!beban) {
                alert('Jenis beban Tidak Boleh Kosong');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }

            if (!rekening_bendahara || !rekening_tujuan || !nama_tujuan || !bank_tujuan) {
                alert('Isian Rekening Belum Lengkap!');
                return;
            }

            if (nilai == '0') {
                alert('Nilai 0!Cek Lagi!!!');
                return;
            }

            let data = {
                no_kas,
                tgl_kas,
                kd_skpd,
                skpd_sumber,
                skpd_ringkas,
                keterangan,
                beban,
                sisa_kas,
                ketcms,
                rekening_bendahara,
                rekening_tujuan,
                nama_tujuan,
                bank_tujuan,
                nilai,
            };

            $('#simpan_pelimpahan').prop('disabled', true);
            $.ajax({
                url: "{{ route('skpd.pelimpahan.simpan_up') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data,
                    "_token": "{{ csrf_token() }}",
                    // data: encrypt(data)
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil ditambahkan, dengan Nomor Kas : ' + response
                            .no_kas);
                        window.location.href =
                            "{{ route('skpd.pelimpahan.up_index') }}";
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan_pelimpahan').prop('disabled', false);
                        return;
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
