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

        $('#kode_akun').on('select2:select', function() {
            let kd_sub_kegiatan = $(this).find(':selected').data('kd_sub_kegiatan');
            let nm_rek = $(this).find(':selected').data('nm_rek').toUpperCase();
            let kd_rek = $(this).find(':selected').data('kd_rek');
            $('#kd_sub_kegiatan').val(kd_sub_kegiatan);
            $('#nama_akun').val(nm_rek);
            $('#kode_rek').val(kd_rek);
        });

        $('#kode_pengirim').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_pengirim').val(nama);
        });

        $('#simpan').on('click', function() {
            let no_tetap = document.getElementById('no_tetap').value;
            let tgl_tetap = document.getElementById('tgl_tetap').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let kode_akun = document.getElementById('kode_akun').value;
            let nama_akun = document.getElementById('nama_akun').value;
            let kode_pengirim = document.getElementById('kode_pengirim').value;
            let nama_pengirim = document.getElementById('nama_pengirim').value;
            let kode_rek = document.getElementById('kode_rek').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let nilai = angka(document.getElementById('nilai').value);
            let tahun_input = tgl_tetap.substr(0, 4);
            let pilihan = document.getElementById('pilihan').checked;
            let tanpa_penetapan;
            if (pilihan == false) {
                tanpa_penetapan = 0;
            } else {
                tanpa_penetapan = 1;
            }

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!no_tetap) {
                alert('No Penetapan Tidak Boleh Kosong');
                return;
            }

            if (!tgl_tetap) {
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

            if (!kode_pengirim) {
                alert('Kode Pengirim Tidak Boleh Kosong');
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
                tanpa_penetapan,
                no_tetap,
                tgl_tetap,
                kd_skpd,
                nm_skpd,
                kode_akun,
                nama_akun,
                kode_pengirim,
                kode_rek,
                kd_sub_kegiatan,
                keterangan,
                nilai,
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('penetapan_penerimaan.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil disimpan!');
                        window.location.href =
                            "{{ route('penetapan_penerimaan.index') }}";
                    } else if (response.message == '2') {
                        alert('No Penetapan telah digunakan!');
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
</script>
