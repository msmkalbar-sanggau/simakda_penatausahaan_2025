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

        $('#kd_rek6').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_rek6').val(nama);
        });

        $('#simpan_uyhd_pajak').on('click', function() {
            let nomor = document.getElementById('nomor').value;
            let tanggal = document.getElementById('tanggal').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let beban = document.getElementById('beban').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let kd_rek6 = document.getElementById('kd_rek6').value;
            let tahun_lalu = document.getElementById('tahun_lalu').checked;
            let nilai = angka(document.getElementById('nilai').value);
            let tahun_input = tanggal.substr(0, 4);
            let lalu = '';

            if (tahun_lalu == true) {
                if (beban == '4' || beban == '6') {
                    alert('Penyetoran tahun lalu harus memakai jenis beban UP/Pajak');
                    return;
                }
                lalu = 1;
            } else {
                lalu = 0;
            }

            if (!tanggal) {
                alert('Tanggal  Tidak Boleh Kosong');
                return;
            }

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
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

            if (nilai == '0') {
                alert('Nilai 0.....!Cek Lagi!!!');
                return;
            }

            if (beban == '7') {
                if (kd_rek6.length != 12) {
                    alert('Kode Rekening belum diisi!!!');
                    return;
                }
            }

            let data = {
                nomor,
                tanggal,
                kd_skpd,
                nm_skpd,
                beban,
                pembayaran,
                beban,
                keterangan,
                kd_rek6,
                lalu,
                nilai,
            };

            $('#simpan_uyhd_pajak').prop('disabled', true);
            $.ajax({
                url: "{{ route('skpd.uyhd_pajak.update') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil ditambahkan, dengan Nomor : ' + response
                            .nomor);
                        window.location.href =
                            "{{ route('skpd.uyhd_pajak.index') }}";
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan_uyhd_pajak').prop('disabled', false);
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
