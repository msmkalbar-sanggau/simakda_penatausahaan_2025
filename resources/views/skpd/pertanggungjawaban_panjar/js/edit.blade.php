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

        $('#no_panjar').on('select2:select', function() {
            let tgl = $(this).find(':selected').data('tgl');
            let kd_skpd = $(this).find(':selected').data('kd_skpd');
            let nilai = $(this).find(':selected').data('nilai');
            let keterangan = $(this).find(':selected').data('keterangan');
            $('#tgl_panjar').val(tgl);
            $('#kd_skpd').val(kd_skpd);
            $('#nilai').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
            $('#keterangan').val(keterangan);
        });

        $('#simpan').on('click', function() {
            let no_simpan = document.getElementById('no_simpan').value;
            let no_kas = document.getElementById('no_kas').value;
            let tgl_kas = document.getElementById('tgl_kas').value;
            let no_panjar = document.getElementById('no_panjar').value;
            let tgl_panjar = document.getElementById('tgl_panjar').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let nilai = rupiah(document.getElementById('nilai').value);
            let tahun_input = tgl_kas.substr(0, 4);

            if (!tgl_kas) {
                alert('Tanggal Tidak Boleh Kosong');
                return;
            }

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!no_kas) {
                alert('No Kas Tidak Boleh Kosong');
                return;
            }

            if (no_kas == no_panjar) {
                alert('Nomor Panjar dan Pertanggungjawaban tidak boleh sama');
                return;
            }

            if (!no_panjar) {
                alert('No Panjar Tidak Boleh Kosong');
                return;
            }

            if (!tgl_panjar) {
                alert('Tanggal Panjar Tidak Boleh Kosong');
                return;
            }

            if (!kd_skpd) {
                alert('Kode SKPD Tidak Boleh Kosong');
                return;
            }

            if (!nilai) {
                alert('Nilai Tidak Boleh Kosong');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }

            let data = {
                no_simpan,
                no_kas,
                tgl_kas,
                no_panjar,
                tgl_panjar,
                kd_skpd,
                keterangan,
                nilai,
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('jawabpanjar.update') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil disimpan!');
                        window.location.href =
                            "{{ route('jawabpanjar.index') }}";
                    } else if (response.message == '4') {
                        alert("Nomor Telah Dipakai!");
                        $('#simpan').prop('disabled', false);
                    } else {
                        alert('Data gagal disimpan!');
                        $('#simpan').prop('disabled', false);
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

    function deleteData(no_sts, kd_rek6, nm_rek6, nilai) {
        let tabel = $('#detail_sts').DataTable();
        let total = rupiah(document.getElementById('total').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai :  ' + nilai +
            ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.kd_rek6 == kd_rek6
            }).remove().draw();
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - parseFloat(nilai)));
        } else {
            return false;
        }
    }
</script>
