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

        let detail = $('#detail_lpj').DataTable({
            responsive: true,
            ordering: false,
            columns: [{
                    data: 'kd_skpd',
                    name: 'kd_skpd'
                },
                {
                    data: 'no_bukti',
                    name: 'no_bukti'
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan'
                },
                {
                    data: 'kdrek6',
                    name: 'kdrek6'
                },
                {
                    data: 'nmrek6',
                    name: 'nmrek6',
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'aksi',
                    name: 'aksi'
                }
            ]
        });

        $('#tampilkan').on('click', function() {
            let tgl_awal = document.getElementById('tgl_awal').value;
            let tgl_akhir = document.getElementById('tgl_akhir').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            if (!tgl_awal || !tgl_akhir) {
                alert('Silahkan pilih tanggal!');
                return;
            }

            $.ajax({
                url: "{{ route('lpj.skpd_atau_unit.detail') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    tgl_awal: tgl_awal,
                    tgl_akhir: tgl_akhir,
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    let total = rupiah(document.getElementById('total').value);
                    $.each(data, function(index, data) {
                        detail.row.add({
                            'kd_skpd': data.kd_skpd,
                            'no_bukti': data.no_bukti,
                            'kd_sub_kegiatan': data.kd_sub_kegiatan,
                            'kdrek6': data.kd_rek6,
                            'nmrek6': data.nm_rek6,
                            'nilai': new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2
                            }).format(data.nilai),
                            'aksi': `<a href="javascript:void(0);" onclick="hapus('${data.no_bukti}','${data.kd_rek6}','${data.nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
                        }).draw();
                        total += parseFloat(data.nilai);
                    })
                    $('#total').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total));
                }
            });

            $('#tampilkan').prop('disabled', true);
            $('#kosongkan').prop('disabled', false);
        });

        $('#kosongkan').on('click', function() {
            $('#total').val(null);
            detail.clear().draw();
            $('#tampilkan').prop('disabled', false);
            $('#kosongkan').prop('disabled', true);
        });

        $('#simpan').on('click', function() {
            let total = rupiah(document.getElementById('total').value);

            let no_lpj = document.getElementById('no_lpj').value;
            if (no_lpj < 0) {
                alert("No LPJ harus diisi dengan benar!");
                return;
            }
            let no_lpj_simpan = document.getElementById('no_lpj_simpan').value;
            let tgl_lpj = document.getElementById('tgl_lpj').value;
            let tgl_awal = document.getElementById('tgl_awal').value;
            let tgl_akhir = document.getElementById('tgl_akhir').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let tahun_input = tgl_lpj.substr(0, 4);

            let detail_lpj1 = detail.rows().data().toArray().map((value) => {
                let data = {
                    kd_skpd: value.kd_skpd,
                    no_bukti: value.no_bukti,
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kdrek6: value.kdrek6,
                    nmrek6: value.nmrek6,
                    nilai: rupiah(value.nilai),
                };
                return data;
            });

            let detail_lpj = JSON.stringify(detail_lpj1);

            if (!no_lpj) {
                alert('Nomor tidak boleh kosong');
                return;
            }

            if (!tgl_lpj) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }

            if (tahun_anggaran != tahun_input) {
                alert('Tahun input tidak sesuai dengan tahun anggaran');
                return;
            }

            if (total == 0) {
                alert('Rincian tidak boleh kosong!');
                return;
            }

            if (detail_lpj1.length == 0) {
                alert('Rincian tidak boleh kosong!');
                return;
            }

            let data = {
                no_lpj,
                no_lpj_simpan,
                tgl_lpj,
                tgl_awal,
                tgl_akhir,
                kd_skpd,
                keterangan,
                total,
                detail_lpj
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('lpj.skpd_atau_unit.update') }}",
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
                        alert('Data berhasil ditambahkan!');
                        window.location.href =
                            "{{ route('lpj.skpd_atau_unit.index') }}";
                    } else if (response.message == '2') {
                        alert('Nomor Telah Dipakai!');
                        $('#simpan').prop('disabled', false);
                        return;
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan').prop('disabled', false);
                        return;
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

    function hapus(no_bukti, kd_rek6, nilai) {
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai :  ' + nilai +
            ' ?');
        let total = rupiah(document.getElementById('total').value);
        let tabel = $('#detail_lpj').DataTable();

        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.no_bukti == no_bukti && data.kdrek6 == kd_rek6
            }).remove().draw();
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - parseFloat(nilai)));
        }
    }
</script>
