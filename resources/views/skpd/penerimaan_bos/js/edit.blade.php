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

        $('#simpan').on('click', function() {
            let no_terima = alltrim(document.getElementById('no_terima').value);
            let tgl_terima = document.getElementById('tgl_terima').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;

            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let sub_kegiatan = $('#kd_sub_kegiatan').find('option:selected');
            let nm_sub_kegiatan = sub_kegiatan.data('nama');

            let rekening = document.getElementById('rekening').value;
            let kd_rek6 = $('#rekening').find('option:selected');
            let nm_rek6 = kd_rek6.data('nama');

            let satdik = document.getElementById('satdik').value;
            let kd_satdik = $('#satdik').find('option:selected');
            let nm_satdik = kd_satdik.data('nama');

            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let nilai = angka(document.getElementById('nilai').value);
            let tahun_input = tgl_terima.substr(0, 4);

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

            if (!kd_sub_kegiatan) {
                alert('Sub Kegiatan Tidak Boleh Kosong');
                return;
            }

            if (!rekening) {
                alert('Rekening Tidak Boleh Kosong');
                return;
            }

            if (!satdik) {
                alert('Satdik Tidak Boleh Kosong');
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
                no_terima,
                tgl_terima,
                kd_skpd,
                nm_skpd,
                kd_sub_kegiatan,
                nm_sub_kegiatan,
                rekening,
                nm_rek6,
                satdik,
                nm_satdik,
                keterangan,
                nilai,
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('penerimaan_bos.update') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil disimpan!');
                        window.location.href =
                            "{{ route('penerimaan_bos.index') }}";
                    } else {
                        alert('Data gagal disimpan!');
                        $('#simpan').prop('disabled', false);
                    }
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
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

        function alltrim(kata) {
            //alert(kata);
            // $cnmgiatx = $cnmgiats.split("/" && ",").join(" ");
            b = (kata.split("'").join("`"));
            c = (b.split(" ").join(""));
            d = (c.replace(/\s/g, ""));
            return d
        }
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
