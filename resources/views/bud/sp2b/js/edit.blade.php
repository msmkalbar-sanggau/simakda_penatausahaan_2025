<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // let revisi = document.getElementById('revisi').value;

        // if(revisi == 1){

        // }else{

        // }

        let detail = $('#detail_sp3b').DataTable({
            responsive: true,
            ordering: false,
            lengthMenu: [5, 10, 20, 50, 100],
            columns: [{
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan'
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6'
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6'
                },
                {
                    data: 'nilai',
                    name: 'nilai'
                },
                // {
                //     data: 'aksi',
                //     name: 'aksi'
                // }
            ]
        });

        $('#tgl_sp2b').on('change', function() {
            let tanggal = this.value;
            let bulan = new Date(tanggal);
            let bulan1 = bulan.getMonth() + 1;
            $('#bulan').val(bulan1).trigger('change');
        });

        $('#tampilkan').on('click', function() {
            let tgl_awal = document.getElementById('tgl_awal').value;
            let tgl_akhir = document.getElementById('tgl_akhir').value;
            let skpd = document.getElementById('kd_skpd').value;

            if (!tgl_awal || !tgl_akhir) {
                alert('Silahkan pilih tanggal!');
                return;
            }

            $.ajax({
                url: "{{ route('sp2b.detail') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    tgl_awal: tgl_awal,
                    tgl_akhir: tgl_akhir,
                    kd_skpd: skpd
                },
                success: function(data) {
                    let total = rupiah(document.getElementById('total').value);
                    $.each(data, function(index, data) {
                        detail.row.add({
                            'kd_sub_kegiatan': data.kd_sub_kegiatan,
                            'kd_rek6': data.kd_rek6,
                            'nm_rek6': data.nm_rek6,
                            'nilai': new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2
                            }).format(data.nilai),
                            // 'aksi': `<a href="javascript:void(0);" onclick="hapus('${data.no_bukti}','${data.kd_rek6}','${data.nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
                        }).draw();
                        total += parseFloat(data.nilai);
                        $('#tampilkan').prop('disabled', true);
                    });
                    $('#total').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total));
                }
            });
        });

        $('#kosongkan').on('click', function() {
            $('#total').val(null);
            detail.clear().draw();
        });

        $('#simpan_edit').on('click', function() {
            let no_sp2b = document.getElementById('no_sp2b').value;
            let tgl_sp2b = document.getElementById('tgl_sp2b').value;
            // let no_sp3b = document.getElementById('no_sp3b').value;
            // let tgl_sp3b = document.getElementById('tgl_sp3b').value;
            let skpd = document.getElementById('kd_skpd').value;
            let bulan = document.getElementById('bulan').value;
            let keterangan = document.getElementById('keterangan').value;
            let tgl_awal = document.getElementById('tgl_awal').value;
            let tgl_akhir = document.getElementById('tgl_akhir').value;
            let total = rupiah(document.getElementById('total').value);
            if (document.getElementById("revisi").checked == true) {
                revisi = '1';
            } else {
                revisi = '0';
            }

            let detail_sp3b = detail.rows().data().toArray().map((value) => {
                let data = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: parseFloat(value.nilai),
                };
                return data;
            });

            let data = {
                no_sp2b,
                tgl_sp2b,
                no_sp3b,
                tgl_sp3b,
                keterangan,
                revisi,
            };

            //   $('#simpan_edit').prop('disabled', true);
            $.ajax({
                url: "{{ route('sp2b.simpan_edit') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data,
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil diubah');
                        window.location.href =
                            "{{ route('sp2b.index') }}";
                    } else if (response.message == '2') {
                        alert('Nomor telah digunakan!');
                        $('#simpan').prop('disabled', false);
                    } else {
                        alert('Data gagal disimpan!');
                        $('#simpan').prop('disabled', false);
                    }
                }
            })
            // });

            $("input[data-type='currency']").on({
                keyup: function() {
                    formatCurrency($(this));
                },
                blur: function() {
                    formatCurrency($(this), "blur");
                }
            });
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

    // function hapus(no_bukti, kd_rek6, nilai) {
    //     let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai :  ' + nilai +
    //         ' ?');
    //     let total = rupiah(document.getElementById('total').value);
    //     let tabel = $('#detail_lpj').DataTable();

    //     if (hapus == true) {
    //         tabel.rows(function(idx, data, node) {
    //             return data.no_bukti == no_bukti && data.kdrek6 == kd_rek6
    //         }).remove().draw();
    //         $('#total').val(new Intl.NumberFormat('id-ID', {
    //             minimumFractionDigits: 2
    //         }).format(total - parseFloat(nilai)));
    //     }
    // }
</script>
