<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#rekanan1').hide();

        let rincian_upload = $('#rincian_upload').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            lengthMenu: [5, 10],
            columns: [{
                    data: 'no_voucher',
                    name: 'no_voucher',
                },
                {
                    data: 'tgl_voucher',
                    name: 'tgl_voucher',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                },
                {
                    data: null,
                    name: 'ket',
                    render: function(data, type, row, meta) {
                        return data.ket.substr(0, 10) + '.....';
                    }
                },
                {
                    data: 'total',
                    name: 'total',
                },
                {
                    data: 'status_upload',
                    name: 'status_upload',
                    visible: false
                },
                {
                    data: 'no_upload',
                    name: 'no_upload',
                    visible: false
                },
                {
                    data: 'rekening_awal',
                    name: 'rekening_awal',
                    visible: false
                },
                {
                    data: 'nm_rekening_tujuan',
                    name: 'nm_rekening_tujuan',
                    visible: false
                },
                {
                    data: 'rekening_tujuan',
                    name: 'rekening_tujuan',
                    visible: false
                },
                {
                    data: 'bank_tujuan',
                    name: 'bank_tujuan',
                    visible: false
                },
                {
                    data: 'ket_tujuan',
                    name: 'ket_tujuan',
                    visible: false
                },
                {
                    data: 'potongan',
                    name: 'potongan',
                    visible: false
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        let list_potongan = $('#list_potongan').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            lengthMenu: [5, 10],
            columns: [{
                    data: 'kd_rek_trans',
                    name: 'kd_rek_trans',
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
                    data: 'rekanan',
                    name: 'rekanan',
                },
                {
                    data: 'npwp',
                    name: 'npwp',
                },
                {
                    data: 'no_billing',
                    name: 'no_billing',
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

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#no_sp2d').on('select2:select', function() {
            let no_sp2d = this.value;
            // Cari Kegiatan
            $.ajax({
                url: "{{ route('skpd.potongan_pajak.cari_kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sp2d: no_sp2d,
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            })
        });

        $('#no_transaksi').on('select2:select', function() {
            let tgl = $(this).find(':selected').data('tgl');
            $('#tgl_transaksi').val(tgl);
        });

        $('#rekanan').on('select2:select', function() {
            let rekanan = this.value;
            if (rekanan == 'Input Manual') {
                $('#rekanan1').show();
                $('#nama_rekanan').val(null);
            } else {
                $('#rekanan1').hide();
                $('#nama_rekanan').val(null);
            }

            let npwp = $(this).find(':selected').data('npwp');
            let pimpinan = $(this).find(':selected').data('pimpinan');
            let alamat = $(this).find(':selected').data('alamat');
            $('#npwp').val(npwp);
            $('#pimpinan').val(pimpinan);
            $('#alamat').val(alamat);
        });

        $('#rekening_potongan').on('select2:select', function() {
            let nm_rekening_potongan = $(this).find(':selected').data('nama');
            $('#nm_rekening_potongan').val(nm_rekening_potongan);
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            let kd_sub_kegiatan = this.value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let nama = $(this).find(':selected').data('nama');
            $('#nm_sub_kegiatan').val(nama);
            $.ajax({
                url: "{{ route('skpd.potongan_pajak.cari_rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    no_sp2d: no_sp2d,
                },
                success: function(data) {
                    $('#kd_rekening').empty();
                    $('#kd_rekening').append(
                        `<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_rekening').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            })
        });

        $('#kd_rekening').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_rekening').val(nama);
        });

        $('#tambah_potongan').on('click', function() {
            let rekanan = document.getElementById('rekanan').value;
            let nama_rekanan = document.getElementById('nama_rekanan').value;
            let rekening_potongan = document.getElementById('rekening_potongan').value;
            let kd_rekening = document.getElementById('kd_rekening').value;
            let nm_rekening = document.getElementById('nm_rekening').value;
            let nm_rekening_potongan = document.getElementById('nm_rekening_potongan').value;
            let nilai = rupiah(document.getElementById('nilai').value);
            let total_potongan = angka(document.getElementById('total_potongan').value);
            let npwp = document.getElementById('npwp').value;
            let no_billing = document.getElementById('no_billing').value;

            let rekan = '';
            if (rekanan == 'Input Manual') {
                rekan = nama_rekanan;
                if (!nama_rekanan) {
                    alert('Isi Rekanan Terlebih Dahulu!');
                    return;
                }
            } else {
                rekan = rekanan;
                if (!rekanan) {
                    alert('Isi Rekanan Terlebih Dahulu!');
                    return;
                }
            }

            if (!npwp) {
                alert('Isi NPWP Terlebih Dahulu!');
                return;
            }
            if (!rekanan) {
                alert('Isi Rekanan Terlebih Dahulu!');
                return;
            }
            if (!rekening_potongan) {
                alert('Isi Rekening Terlebih Dahulu!');
                return;
            }
            if (!no_billing) {
                alert('Isi No Billing Terlebih Dahulu!');
                return;
            }

            if (no_billing.length != 15) {
                alert('No Billing harus 15 angka!');
                return;
            }

            if (nilai == 0) {
                alert('Isi Nilai Terlebih Dahulu!');
                return;
            }

            list_potongan.row.add({
                'kd_rek_trans': kd_rekening,
                'kd_rek6': rekening_potongan,
                'nm_rek6': nm_rekening_potongan,
                'rekanan': rekan,
                'npwp': npwp,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'no_billing': no_billing,
                'aksi': `<a href="javascript:void(0);" onclick="deletePotongan('${kd_rekening}','${rekening_potongan}','${nm_rekening_potongan}','${nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();

            $('#total_potongan').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai + total_potongan));

            $('#nilai').val('');
            $('#rekening_potongan').val(null).change();
            $('#nm_rekening_potongan').val('');
        });

        $('#simpan_potongan').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let npwp = document.getElementById('npwp').value;
            let npwp1 = npwp.split('-').join('').split('.').join('');
            let beban = document.getElementById('beban').value;
            let nm_sub_kegiatan = document.getElementById('nm_sub_kegiatan').value;
            let nm_rekening = document.getElementById('nm_rekening').value;
            let alamat = document.getElementById('alamat').value;
            let pimpinan = document.getElementById('pimpinan').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kd_rekening = document.getElementById('kd_rekening').value;
            let rekanan1 = document.getElementById('rekanan').value;
            let nama_rekanan = document.getElementById('nama_rekanan').value;
            let no_transaksi = document.getElementById('no_transaksi').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let no_billing = document.getElementById('no_billing').value;
            let keterangan = document.getElementById('keterangan').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let total_potongan = angka(document.getElementById('total_potongan').value);
            let tahun_input = tgl_bukti.substring(0, 4);
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let tgl_transaksi = document.getElementById('tgl_transaksi').value;

            let rincian_potongan = list_potongan.rows().data().toArray().map((value) => {
                let data = {
                    kd_rek_trans: value.kd_rek_trans,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: angka(value.nilai),
                    rekanan: value.rekanan,
                    npwp: value.npwp,
                    no_billing: value.no_billing,
                };
                return data;
            });

            if (beban == '1' || beban == '2' || beban == '3') {
                if (pembayaran == 'BANK') {
                    let tanggal1 = new Date(tgl_bukti);
                    let bulan1 = tanggal1.getMonth() + 1;
                    let tanggal2 = new Date(tgl_transaksi);
                    let bulan2 = tanggal2.getMonth() + 1;

                    if (!no_transaksi) {
                        alert('No Transaksi Tidak boleh kosong utk pembayaran Bank');
                        return;
                    }
                    if (bulan1 != bulan2) {
                        alert('Bulan Terima Pajak dan Bulan Transaksi yg dipilih harus sama');
                        return;
                    }
                    if (tgl_bukti != tgl_transaksi) {
                        alert('Tanggal Terima Pajak dan Tanggal Transaksi yg dipilih harus sama');
                        return;
                    }
                }
            } else {
                if (!no_transaksi) {
                    alert(
                        'No Transaksi Hanya untuk jenis beban UP/GU/TU BANK. Kosongkan No Transaksi.'
                    );
                    return;
                }
            }

            if (npwp1.length > 0) {
                if (npwp1.length != 15) {
                    alert('NPWP tidak lengkap cek lagi');
                    return;
                }
            }
            if (!pimpinan) {
                alert('Nama Pimpinan Tidak Boleh Kosong');
                return;
            }
            if (!alamat) {
                alert('Alamat Tidak Boleh Kosong');
                return;
            }
            if (!tgl_bukti) {
                alert('Tanggal Bukti Tidak Boleh Kosong');
                return;
            }
            if (!beban) {
                alert('Beban Tidak Boleh Kosong');
                return;
            }
            if (!no_sp2d) {
                alert('Nomor Sp2d Tidak Boleh Kosong');
                return;
            }
            if (!kd_sub_kegiatan) {
                alert('Kegiatan Tidak Boleh Kosong');
                return;
            }
            if (!kd_rekening) {
                alert('Rekening Tidak Boleh Kosong');
                return;
            }
            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }
            if (rincian_potongan.length == 0) {
                alert('List potongan tidak boleh kosong!');
                return;
            }

            let rekanan = '';
            if (rekanan1 == 'Input Manual') {
                if (!nama_rekanan) {
                    alert('Nama Rekanan Tidak Boleh Kosong!');
                    return;
                }
                rekanan = nama_rekanan;
            } else {
                if (!rekanan1) {
                    alert('Rekanan Tidak Boleh Kosong!');
                    return;
                }
                rekanan = rekanan1;
            }

            let data = {
                rincian_potongan,
                tgl_bukti,
                npwp,
                beban,
                nm_sub_kegiatan,
                nm_rekening,
                alamat,
                pimpinan,
                alamat,
                no_sp2d,
                kd_sub_kegiatan,
                kd_rekening,
                rekanan,
                no_transaksi,
                kd_skpd,
                nm_skpd,
                no_billing,
                keterangan,
                pembayaran,
                total_potongan
            };
            $('#simpan_potongan').prop('disabled', true);
            // Simpan Potongan
            $.ajax({
                url: "{{ route('skpd.potongan_pajak.simpan_potongan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data,
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Potongan berhasil disimpan, dengan Nomor Bukti : ' +
                            response.no_bukti);
                        window.location.href =
                            "{{ route('skpd.potongan_pajak.index') }}"
                    } else {
                        alert('Potongan tidak berhasil disimpan!');
                        $('#simpan_potongan').prop('disabled', false);
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

    function angka(data) {
        let n1 = data.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function rupiah(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }

    function deletePotongan(kd_rek_trans, kd_rek6, nm_rek6, nilai) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor Transaksi : ' + kd_rek6);
        let tabel = $('#list_potongan').DataTable();
        let nilai_potongan = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(nilai);
        let total_potongan = angka(document.getElementById('total_potongan').value);
        if (tanya == true) {
            tabel.rows(function(idx, data, node) {
                return data.nilai == nilai_potongan
            }).remove().draw();
            $('#total_potongan').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_potongan - parseFloat(nilai)));
        } else {
            return false;
        }
    }
</script>
