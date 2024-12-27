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

        $('#no_panjar_lalu').on('select2:select', function() {
            let no_panjar = this.value;
            let tgl = $(this).find(':selected').data('tgl');
            let no_panjar_lalu = $(this).find(':selected').data('no_panjar_lalu');
            $('#tgl_panjar_lalu').val(tgl);
            $('#no_panjar_lalu1').val(no_panjar_lalu);

            load_data(no_panjar, no_panjar_lalu);
        });

        $('#simpan').on('click', function() {
            let no_simpan = document.getElementById('no_simpan').value;
            let no_panjar = document.getElementById('no_panjar').value;
            let tgl_panjar = document.getElementById('tgl_panjar').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let no_panjar_lalu = document.getElementById('no_panjar_lalu').value;
            let tgl_panjar_lalu = document.getElementById('tgl_panjar_lalu').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let sisa_panjar = rupiah(document.getElementById('sisa_panjar').value);
            let tahun_input = tgl_panjar.substr(0, 4);

            if (!tgl_panjar) {
                alert('Tanggal Tidak Boleh Kosong');
                return;
            }

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!no_panjar) {
                alert('No Panjar Tidak Boleh Kosong');
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

            let data = {
                no_panjar,
                tgl_panjar,
                kd_skpd,
                keterangan,
                sisa_panjar,
                no_panjar_lalu,
                tgl_panjar_lalu,
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('kembalipanjar.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil disimpan!');
                        window.location.href =
                            "{{ route('kembalipanjar.index') }}";
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

        function load_data(no_panjar, no_panjar_lalu) {
            $('#panjar_awal').val(null);
            $('#nilai_panjar_awal').val(null);
            $('#tambahan_panjar').val(null);
            $('#nilai_tambahan_panjar').val(null);
            $('#total_panjar').val(null);
            $('#total_transaksi').val(null);
            $('#sisa_panjar').val(null);

            $.ajax({
                url: "{{ route('kembalipanjar.load_data') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_panjar: no_panjar,
                    no_panjar_lalu: no_panjar_lalu,
                },
                success: function(data) {
                    $('#panjar_awal').val(data.load_detail.no_panjar);
                    $('#nilai_panjar_awal').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_detail.nilai));

                    $('#tambahan_panjar').val(data.load_detail.no_panjar2);
                    $('#nilai_tambahan_panjar').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_detail.nilai2));

                    $('#total_panjar').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_total.panjar));

                    $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_total.trans));

                    $('#sisa_panjar').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_total.panjar - data.load_total.trans));
                }
            })
        };
    });

    function hitung() {
        let nilai_panjar_lalu = rupiah(document.getElementById('nilai_panjar_lalu').value);
        let nilai = angka(document.getElementById('nilai').value);

        // Total
        $('#total').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(nilai_panjar_lalu + nilai));
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
