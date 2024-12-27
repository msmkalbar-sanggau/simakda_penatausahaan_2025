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

        $('#bank').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_bank').val(nama);
        });

        $('#tgl_kas').on('change', function() {
            let tgl_kas = this.value;
            $('#tgl_bukti').val(tgl_kas);
        });

        $('#simpan_tunai').on('click', function() {
            let no_kas = document.getElementById('no_kas').value;
            let tgl_kas = document.getElementById('tgl_kas').value;
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let keterangan = document.getElementById('keterangan').value;
            let nm_rekening = document.getElementById('nm_rekening').value;
            let bank = document.getElementById('bank').value;
            let sisa_kas = rupiah(document.getElementById('sisa_kas').value);
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let nilai = angka(document.getElementById('nilai').value);
            let tahun_input = tgl_kas.substr(0, 4);

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (nilai > sisa_kas) {
                alert('Nilai Lebih Besar dari Sisa Bank');
                return;
            }

            if (!tgl_kas) {
                alert('Tanggal Tidak Boleh Kosong');
                return;
            }

            if (!kd_skpd) {
                alert('Kode SKPD Tidak Boleh Kosong');
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

            let data = {
                no_kas,
                tgl_kas,
                tgl_bukti,
                kd_skpd,
                nm_rekening,
                bank,
                keterangan,
                nilai,
            };

            $('#simpan_tunai').prop('disabled', true);
            $.ajax({
                url: "{{ route('skpd.simpanan_bank.update_tunai') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil ditambahkan, dengan Nomor Kas : ' + response
                            .no_kas);
                        window.location.href =
                            "{{ route('skpd.simpanan_bank.tunai') }}";
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan_tunai').prop('disabled', false);
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
