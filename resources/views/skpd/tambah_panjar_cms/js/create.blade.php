<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let tabel_tujuan = $('#rekening_tujuan').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_bukti',
                    name: 'no_bukti',
                    visible: false
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti',
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
                },
                {
                    data: 'rekening_tujuan',
                    name: 'rekening_tujuan',
                },
                {
                    data: 'bank_tujuan',
                    name: 'bank_tujuan',
                    visible: false
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    visible: false
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
        })

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('.select2-modal1').select2({
            dropdownParent: $('#modal_rekening .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#tambah_rek_tujuan').on('click', function() {
            $('#modal_rekening').modal('show');
        });

        $('#rek_tujuan').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            let bank = $(this).find(':selected').data('bank');

            $("#nm_rekening_tujuan").val(nama);
            $("#bank").val(bank).trigger('change');
        });

        $('#pembayaran').on('select2:select', function() {
            $('#kd_sub_kegiatan').val(null).change();
            $('#sisa_anggaran').val(null);
            $('#sisa_tunai').val(null);
            $('#sisa_bank').val(null);
        });

        $('#no_panjar').on('select2:select', function() {
            let nilai = $(this).find(':selected').data('nilai');
            $('#nilai_panjar').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
            let no_panjar = this.value;
            // CARI KODE SUB KEGIATAN
            $.ajax({
                url: "{{ route('tambah_panjarcms.kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_panjar: no_panjar,
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-anggaran="${data.anggaran}" data-transaksi="${data.transaksi}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan} | ${new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2}).format(data.transaksi)} | ${new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2}).format(data.anggaran)}</option>`
                        );
                    })
                }
            })
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            let pembayaran = document.getElementById('pembayaran').value;
            if (!pembayaran) {
                alert('Pilih Jenis Pembayaran Terlebih Dahulu!');
                $('#kd_sub_kegiatan').val(null).change();
                return;
            }
            let transaksi = $(this).find(':selected').data('transaksi');
            let anggaran = $(this).find(':selected').data('anggaran');
            $('#sisa_anggaran').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(anggaran - transaksi));

            let tunai1 = document.getElementById('tunai1').value;
            let bank1 = document.getElementById('bank1').value;

            if (pembayaran == 'TUNAI') {
                $('#sisa_tunai').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(tunai1));
                $('#sisa_bank').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(0));
            } else {
                $('#sisa_tunai').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(0));
                $('#sisa_bank').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(bank1));
            }
        });

        $('#simpan_rekening_tujuan').on('click', function() {
            let no_bukti = document.getElementById('no_panjar').value;
            let tgl_voucher = document.getElementById('tgl_panjar').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let rekening = document.getElementById('rekening').value;
            let nm_rekening_tujuan = document.getElementById('nm_rekening_tujuan').value;
            let rek_tujuan = document.getElementById('rek_tujuan').value;
            let bank = document.getElementById('bank').value;
            let total_belanja = angka(document.getElementById('nilai').value);
            let total_transfer = rupiah(document.getElementById('total_transfer').value);
            let nilai_potongan = angka(document.getElementById('nilai_potongan').value);
            let nilai_transfer = angka(document.getElementById('nilai_transfer').value);
            let pajak = rupiah(document.getElementById('pajak').value);

            let hasil_akumulasi = total_belanja - nilai_potongan;
            let akumulasi = total_transfer + nilai_transfer;

            if (nilai_transfer == 0) {
                alert("Nilai Tidak Boleh Nol");
                return;
            }

            if (akumulasi > hasil_akumulasi) {
                alert('Nilai Melebihi Total Belanja');
                return;
            }

            if (nilai_transfer > hasil_akumulasi) {
                alert('Nilai Melebihi Total Belanja');
                return;
            }

            if (total_transfer > hasil_akumulasi) {
                alert('Nilai Melebihi Total Belanja');
                return;
            }

            if (!rekening) {
                alert('Pilih Rekening Sumber');
                return;
            }

            if (!nm_rekening_tujuan) {
                alert('Pilih rekening');
                return;
            }

            if (!rek_tujuan) {
                alert('Pilih rekening');
                return;
            }

            if (!bank) {
                alert('Pilih rekening');
                return;
            }

            tabel_tujuan.row.add({
                'no_bukti': no_bukti,
                'tgl_bukti': tgl_voucher,
                'rekening_awal': rekening,
                'nm_rekening_tujuan': nm_rekening_tujuan,
                'rekening_tujuan': rek_tujuan,
                'bank_tujuan': bank,
                'kd_skpd': kd_skpd,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai_transfer),
                'aksi': `<a href="javascript:void(0);" onclick="deleteRek('${no_bukti}','${rek_tujuan}','${nilai_transfer}','${nilai_potongan}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();

            $('#total_transfer').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transfer + nilai_transfer));
            $('#pajak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(pajak + nilai_potongan));

            $('#rek_tujuan').val(null).change();
            $('#nm_rekening_tujuan').val(null);
            $('#bank').val(null).change();
            $('#modal_rekening').modal('hide');
        });

        $('#simpan').on('click', function() {
            let nomor_panjar = document.getElementById('nomor_panjar').value;
            let tgl_panjar = document.getElementById('tgl_panjar').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let keterangan = document.getElementById('keterangan').value;
            let no_panjar = document.getElementById('no_panjar').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let rekening = document.getElementById('rekening').value;

            let sisa_anggaran = rupiah(document.getElementById('sisa_anggaran').value);
            let sisa_bank = rupiah(document.getElementById('sisa_bank').value);
            let nilai = angka(document.getElementById('nilai').value);
            let pajak = rupiah(document.getElementById('pajak').value);
            let total = rupiah(document.getElementById('total').value);
            let total_transfer = rupiah(document.getElementById('total_transfer').value);

            let tahun_input = tgl_panjar.substr(0, 4);

            let ket_tujuan = "TPNJR.KEG." + kd_sub_kegiatan;

            if (!nomor_panjar) {
                alert('No Panjar Tidak Boleh Kosong');
                return;
            }

            if (!tgl_panjar) {
                alert('Tanggal Panjar Tidak Boleh Kosong');
                return;
            }

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (total_transfer != nilai - pajak) {
                alert('Nilai Transfer Tidak Sama Dengan Nilai Panjar + Pajak');
                return;
            }

            if (total_transfer == 0) {
                alert('Nilai Transfer Tidak Boleh Nol');
                return;
            }

            if (!kd_skpd) {
                alert('Kode SKPD Tidak Boleh Kosong');
                return;
            }

            if (!kd_sub_kegiatan) {
                alert('Kode Sub Kegiatan Tidak Boleh Kosong');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }

            if (sisa_bank < nilai && sisa_anggaran < nilai) {
                alert('Tidak boleh melebihi sisa Kas Bank dan Anggaran!');
                return;
            }

            let rincian_rek_tujuan = tabel_tujuan.rows().data().toArray().map((value) => {
                let data = {
                    no_bukti: value.no_bukti,
                    rekening_awal: value.rekening_awal,
                    nm_rekening_tujuan: value.nm_rekening_tujuan,
                    rekening_tujuan: value.rekening_tujuan,
                    bank_tujuan: value.bank_tujuan,
                    kd_skpd: value.kd_skpd,
                    nilai: rupiah(value.nilai),
                };
                return data;
            });

            if (rincian_rek_tujuan.length == 0) {
                alert('Rincian Daftar Rekening Tujuan Tidak Boleh Kosong!');
                return;
            }

            let data = {
                nomor_panjar,
                no_panjar,
                tgl_panjar,
                kd_skpd,
                nm_skpd,
                kd_sub_kegiatan,
                keterangan,
                nilai,
                pembayaran,
                pajak,
                rekening,
                ket_tujuan,
                total_transfer,
                rincian_rek_tujuan
            };

            $('#simpan').prop('disabled', true);

            $.ajax({
                url: "{{ route('tambah_panjarcms.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data Berhasil Disimpan!');
                        window.location.href = "{{ route('tambah_panjarcms.index') }}";
                    } else if (response.message == '4') {
                        alert('Nomor Telah Digunakan!');
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

    function hitung() {
        let nilai_panjar = rupiah(document.getElementById('nilai_panjar').value);
        let nilai = angka(document.getElementById('nilai').value);

        // Total
        $('#total').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(nilai_panjar + nilai));
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

    function deleteRek(no_bukti, rek_tujuan, nilai_transfer, nilai_potongan) {
        let tabel = $('#rekening_tujuan').DataTable();
        let transfer_sementara = rupiah(document.getElementById('total_transfer').value);
        let pajak = rupiah(document.getElementById('pajak').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + rek_tujuan + '  Nilai :  ' + nilai_transfer +
            ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.no_bukti == no_bukti && data.rekening_tujuan == rek_tujuan
            }).remove().draw();
            $('#pajak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(pajak - nilai_potongan));
            $('#total_transfer').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(transfer_sementara - nilai_transfer));
        } else {
            return false;
        }

    }
</script>
