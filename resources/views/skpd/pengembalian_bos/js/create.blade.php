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

        $('.select2-modal1').select2({
            dropdownParent: $('#modal_rekening .modal-content'),
            theme: 'bootstrap-5'
        });

        let tabel_rincian = $('#detail').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_sts',
                    name: 'no_sts',
                    visible: false
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6',
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        $('#tambah_rincian').on('click', function() {
            let no_kas = document.getElementById('no_kas').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let satdik = document.getElementById('satdik').value;

            if (no_kas != '' && satdik != '') {
                $.ajax({
                    url: "{{ route('pengembalian_bos.sisa_bos') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        kd_skpd: document.getElementById('kd_skpd').value,
                        satdik: document.getElementById('satdik').value,
                    },
                    success: function(data) {
                        $('#sisa_bos').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.terima - data.keluar));
                    }
                });
                $('#modal_rekening').modal('show');
            } else {
                alert('Nomor Sts dan Satdik tidak boleh kosong');
                return;
            }
        });

        $('#tgl_kas').on('change', function() {
            let tanggal = this.value;
            $.ajax({
                url: "{{ route('pengembalian_bos.kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    tanggal: tanggal,
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            })
        });

        $('#simpan_rincian').on('click', function() {
            let rekening = document.getElementById('rekening').value;

            let kd_rek6 = $('#rekening').find('option:selected');
            let nama_rekening = kd_rek6.data('nama');

            let no_kas = document.getElementById('no_kas').value;
            let jenis_transaksi = document.getElementById('jenis_transaksi').value;
            let nilai = angka(document.getElementById('nilai').value);
            let sisa_bos = rupiah(document.getElementById('sisa_bos').value);
            let total = rupiah(document.getElementById('total').value);

            let akumulasi = nilai + sisa_bos;

            if (jenis_transaksi == 1 && sisa_bos > akumulasi) {
                alert("Melebihi Kas Tunai");
                return;
            }

            if (rekening != '' && nilai != 0) {
                tabel_rincian.row.add({
                    'no_sts': no_kas,
                    'kd_rek6': rekening,
                    'nm_rek6': nama_rekening,
                    'nilai': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(nilai),
                    'aksi': `<a href="javascript:void(0);" onclick="hapus('${no_kas}','${rekening}','${nama_rekening}','${nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
                }).draw();

                $('#total').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total + nilai));
            }

            $('#rekening').val(null).change();
            $('#nilai').val(null);
            $('#modal_rekening').modal('hide');
        });

        $('#simpan').on('click', function() {
            let no_kas = alltrim(document.getElementById('no_kas').value);
            let tgl_kas = document.getElementById('tgl_kas').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;

            let sub_kegiatan = $('#kd_sub_kegiatan').find('option:selected');
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let nm_sub_kegiatan = sub_kegiatan.data('nama');

            let kd_satdik = $('#satdik').find('option:selected');
            let satdik = document.getElementById('satdik').value;
            let nama_satdik = kd_satdik.data('nama');

            let jenis_transaksi = document.getElementById('jenis_transaksi').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let total = rupiah(document.getElementById('total').value);

            if (!no_kas) {
                alert('No Kas Tidak Boleh Kosong');
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

            let tahun_input = tgl_kas.substr(0, 4);

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            let rincian = tabel_rincian.rows().data().toArray().map((value) => {
                let data = {
                    no_sts: value.no_sts,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: rupiah(value.nilai),
                };
                return data;
            });

            let data = {
                no_kas,
                tgl_kas,
                kd_skpd,
                nm_skpd,
                kd_sub_kegiatan,
                nm_sub_kegiatan,
                satdik,
                nama_satdik,
                keterangan,
                pembayaran,
                jenis_transaksi,
                total,
                rincian
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('pengembalian_bos.simpan') }}",
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
                            "{{ route('pengembalian_bos.index') }}";
                    } else if (response.message == '2') {
                        alert('No Kas telah digunakan!');
                        $('#simpan').prop('disabled', false);
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

    function hapus(no_kas, kd_rek6, nm_rek6, nilai) {
        let tabel = $('#detail').DataTable();
        let total = rupiah(document.getElementById('total').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + nm_rek6 + '  Nilai :  ' + nilai +
            ' ?');

        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.kd_rek6 == kd_rek6 && rupiah(data.nilai) == parseFloat(
                    nilai)
            }).remove().draw();
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - parseFloat(nilai)));
        } else {
            return false;
        }

    }
</script>
