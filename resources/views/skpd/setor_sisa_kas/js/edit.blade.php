<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let detail_sts = $('#detail_sts').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6',
                },
                {
                    data: 'sumber',
                    name: 'sumber',
                },
                {
                    data: 'rupiah',
                    name: 'rupiah',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('.select2-modal1').select2({
            dropdownParent: $('#modal_detail .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#tambah_sts').on('click', function() {
            $('#kd_rek6').empty();
            $('#sdana').empty();
            $('#nm_rek6').val(null);
            $('#nilai').val(null);
            let jenis_transaksi = document.getElementById('jenis_transaksi').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let kd_rek6 = detail_sts.rows().data().toArray().map((value) => {
                let result = {
                    kd_rek6: value.kd_rek6,
                };
                return result;
            });

            let total_detail = detail_sts.rows().data().toArray();
            if (total_detail.length > 0) {
                alert('Detail STS Tidak Boleh Lebih Dari 1!');
                return;
            }

            if (!jenis_transaksi) {
                alert('Jenis Transaksi harus dipilih!');
                return;
            }
            if (jenis_transaksi == '1') {
                if (!no_sp2d) {
                    alert('No SP2D harus dipilih!');
                    return;
                }
            } else if (jenis_transaksi == '5') {
                if (!no_sp2d || !kd_sub_kegiatan) {
                    alert('No SP2D dan Kode Sub Kegiatan harus dipilih!');
                    return;
                }
            }

            if (!pembayaran) {
                alert('Pembayaran harus dipilih!');
                return;
            }

            $.ajax({
                url: "{{ route('skpd.setor_sisa.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jenis_transaksi: jenis_transaksi,
                    no_sp2d: no_sp2d,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    kd_rek6: kd_rek6,
                },
                success: function(data) {
                    $('#kd_rek6').empty();
                    $('#kd_rek6').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_rek6').append(
                            `<option value="${data.kd_rek6}" data-nm_rek6="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            })
            $('#modal_detail').modal('show');
        });

        $('#jenis_transaksi').on('select2:select', function() {
            let jenis_transaksi = this.value;

            // if (jenis_transaksi == '1') {
            //     $('#kd_sub_kegiatan').prop('disabled', true);
            // } else if (jenis_transaksi == '5') {
            //     $('#kd_sub_kegiatan').prop('disabled', false);
            // }
            detail_sts.clear().draw();
            $('#jumlah').val(null);
            $('#kd_sub_kegiatan').empty();
            $('#no_sp2d').empty();
            $('#nm_sub_kegiatan').val(null);
            $('#jenis_cp').val(null);
            // Cari NO SP2D
            $.ajax({
                url: "{{ route('skpd.setor_sisa.no_sp2d') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jenis_transaksi: jenis_transaksi,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    $('#no_sp2d').empty();
                    $('#no_sp2d').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#no_sp2d').append(
                            `<option value="${data.no_sp2d}" data-jns_spp="${data.jns_spp}" data-jns_beban="${data.jns_beban}" data-jns_cp="${data.jns_cp}" data-nilai="${data.nilai}">${data.no_sp2d}</option>`
                        );
                    })
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        });

        $('#no_sp2d').on('select2:select', function() {
            let no_sp2d = this.value;
            let jns_cp = $(this).find(':selected').data('jns_cp');
            let nilai = parseFloat($(this).find(':selected').data('nilai')) || 0;
            let beban = $(this).find(':selected').data('jns_spp');
            $('#jenis_cp').val(jns_cp);

            detail_sts.clear().draw();
            $('#kd_sub_kegiatan').empty();
            $('#nm_sub_kegiatan').val(null);
            // Cari Kegiatan
            $.ajax({
                url: "{{ route('skpd.setor_sisa.kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sp2d: no_sp2d,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    let kegiatan = data.kegiatan;

                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(kegiatan, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nm_sub_kegiatan="${data.nm_sub_kegiatan}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    });

                    let sisa_bank = parseFloat(data.sisa_bank) || 0;
                    let sisa_tunai = parseFloat(data.sisa_tunai) || 0;
                    let potongan_ls = parseFloat(data.potongan_ls) || 0;

                    if (beban == '1' || beban == '2') {
                        $('#sisa_kas_bank').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(sisa_bank));
                    } else {
                        $('#sisa_kas_bank').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(nilai - potongan_ls));
                    }

                    $('#sisa_kas_tunai').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(sisa_tunai));
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            detail_sts.clear().draw();
            let nm_sub_kegiatan = $(this).find(':selected').data('nm_sub_kegiatan');
            $('#nm_sub_kegiatan').val(nm_sub_kegiatan);
        });

        $('#kd_rek6').on('select2:select', function() {
            let nm_rek6 = $(this).find(':selected').data('nm_rek6');
            let kd_rek6 = this.value;
            $('#nm_rek6').val(nm_rek6);

            cari_sumber(kd_rek6);

        });

        $('#simpan_detail').on('click', function() {
            let jenis_transaksi = document.getElementById('jenis_transaksi').value;
            let kd_rek6 = document.getElementById('kd_rek6').value;
            let nm_rek6 = document.getElementById('nm_rek6').value;
            let sdana = document.getElementById('sdana').value;
            let nilai = angka(document.getElementById('nilai').value);
            let jumlah = rupiah(document.getElementById('jumlah').value);
            let pembayaran = document.getElementById('pembayaran').value;
            let sisa_kas_tunai = rupiah(document.getElementById('sisa_kas_tunai').value);

            let sisa_kas_bank = rupiah(document.getElementById('sisa_kas_bank').value);

            let sp2d = $('#no_sp2d').find('option:selected');
            let jns_spp = sp2d.data('jns_spp');
            let jns_beban = sp2d.data('jns_beban');
            let nilai_sp2d = sp2d.data('nilai');

            let total = nilai + sisa_kas_tunai;

            if (!kd_rek6) {
                alert('Silahkan pilih Rekening!');
                return;
            }
            // if (jenis_transaksi == '1' && sisa_kas_tunai > total) {
            //     alert('Melebihi Kas Tunai');
            //     return;
            // }
            if (nilai == 0) {
                alert('Nilai 0...Cek Lagi!');
                return;
            }

            if (jns_spp == '4' || jns_spp == '5' || (jns_spp == '6' && jns_beban == '6')) {
                if (nilai > nilai_sp2d) {
                    alert('Nilai CP tidak boleh melebihi nilai SP2D');
                    return;
                }
            }

            if ((jns_spp == '6' && jns_beban != '6') || jns_spp == '3' || jns_spp == '1' || jns_spp ==
                '2') {
                if (pembayaran == 'BNK' && nilai > sisa_kas_bank) {
                    alert('Nilai CP tidak boleh melebihi nilai sisa kas bank!');
                    return;
                }
                if (pembayaran == 'TNK' && nilai > sisa_kas_tunai) {
                    alert('Nilai CP tidak boleh melebihi nilai sisa kas tunai!');
                    return;
                }
            }

            detail_sts.row.add({
                'kd_rek6': kd_rek6,
                'nm_rek6': nm_rek6,
                'sumber': sdana,
                'rupiah': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'aksi': `<a href="javascript:void(0);" onclick="deleteDetail('${kd_rek6}','${nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();
            $('#jumlah').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(jumlah + nilai));
            $('#kd_rek6').val(null).change();
            $('#nm_rek6').val(null);
            $('#nilai').val(null);
            $('#modal_detail').modal('hide');
        });

        $('#simpan_sts').on('click', function() {
            let no_kas = document.getElementById('no_kas').value;
            let tgl_kas = document.getElementById('tgl_kas').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let uraian = document.getElementById('uraian').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let jenis_transaksi = rupiah(document.getElementById('jenis_transaksi').value);
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let jenis_cp = document.getElementById('jenis_cp').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let nm_sub_kegiatan = document.getElementById('nm_sub_kegiatan').value;
            let hkpg = document.getElementById('hkpg_tahun_ini').checked;
            let hkpg_lalu = document.getElementById('hkpg_tahun_lalu').checked;
            let lain = document.getElementById('pemotongan_lainnya').checked;
            let jumlah = rupiah(document.getElementById('jumlah').value);
            let tahun_input = tgl_kas.substr(0, 4);
            let potlain = '';
            let jns_cp = '';
            let detail = detail_sts.rows().data().toArray().map((value) => {
                let result = {
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    sumber: value.sumber,
                    rupiah: rupiah(value.rupiah),
                };
                return result;
            });

            if (detail.length == 0) {
                alert('Detail STS Tidak Boleh Kosong!');
                return;
            }

            if ((hkpg == true) && (lain == true)) {
                alert('Tidak boleh memilih HKPG dan Pemotongan Lainnya sekaligus!');
                return;
            } else if ((hkpg == true) && (hkpg_lalu == true)) {
                alert('Tidak boleh memilih HKPG dan HKPG lalu Lainnya sekaligus!');
                return;
            } else if ((lain == true) && (hkpg_lalu == true)) {
                alert('Tidak boleh memilih Pemotongan Lainnya dan HKPG lalu Lainnya sekaligus!');
                return;
            } else if ((hkpg == true) && (lain == false) && (hkpg_lalu == false)) {
                potlain = 1;
            } else if ((hkpg == false) && (lain == true) && (hkpg_lalu == false)) {
                potlain = 2;
            } else if ((hkpg == false) && (lain == false) && (hkpg_lalu == true)) {
                potlain = 3;
            } else {
                potlain = 0;
            }

            if ((jenis_cp == "UP/GU/TU") || (jenis_cp == "UP") || (jenis_cp == "GU") || (jenis_cp ==
                    "TU") || (jenis_cp == "3")) {
                jns_cp = '3';
            } else if ((jenis_cp == "LS GAJI") || (jenis_cp == "1")) {
                jns_cp = "1";
            } else {
                jns_cp = "2"
            }

            if ((jenis_transaksi == '1') && (jns_cp != '1') && (potlain != 0)) {
                alert('HKPG dan Pemotongan lainnya hanya untuk Belanja Gaji!');
                return;
            }
            if ((jenis_transaksi == '5') && (jns_cp == '1') && (potlain == 0)) {
                alert('HKPG dan Pemotongan lainnya belum di pilih!');
                return;
            }

            if (!tgl_kas) {
                alert('Tanggal STS Tidak Boleh Kosong!');
                return;
            }

            if (!kd_skpd) {
                alert('SKPD Tidak Boleh Kosong!');
                return;
            }

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            let data = {
                no_kas,
                tgl_kas,
                kd_skpd,
                nm_skpd,
                uraian,
                pembayaran,
                jenis_transaksi,
                no_sp2d,
                jns_cp,
                kd_sub_kegiatan,
                nm_sub_kegiatan,
                jumlah,
                potlain,
                detail
            };

            $('#simpan_sts').prop('disabled', true);
            $.ajax({
                url: "{{ route('skpd.setor_sisa.update') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil ditambahkan, dengan Nomor STS : ' + response
                            .no_kas);
                        window.location.href =
                            "{{ route('skpd.setor_sisa.index') }}";
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan_sts').prop('disabled', false);
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

    function deleteDetail(kd_rek6, nilai) {
        let tabel = $('#detail_sts').DataTable();
        let jumlah = rupiah(document.getElementById('jumlah').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6.trim() + ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.kd_rek6 == kd_rek6.trim()
            }).remove().draw();
            $('#jumlah').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(jumlah - parseFloat(nilai)));
        } else {
            return false;
        }
    }

    function cari_sumber(kd_rek6) {
        $.ajax({
            url: "{{ route('skpd.transaksi_cms.sumber') }}",
            type: "POST",
            dataType: 'json',
            data: {
                kd_rek6: kd_rek6,
                kd_skpd: document.getElementById('kd_skpd').value,
                kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                no_sp2d: document.getElementById('no_sp2d').value,
                beban: "",
            },
            success: function(data) {
                $('#sdana').empty();
                $('#sdana').append(
                    `<option value="" disabled selected>Pilih Sumber Dana</option>`);
                $.each(data, function(index, data) {
                    $('#sdana').append(
                        `<option value="${data.sumber_dana}" data-anggaran="${data.nilai}">${data.sumber_dana}</option>`
                    );
                })
            }
        })
    }
</script>
