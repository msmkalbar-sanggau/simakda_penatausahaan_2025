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

        $('.select2-modal').select2({
            dropdownParent: $('#modal_rincian .modal-content'),
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        cari_nomor();

        let detail = $('#rincian_spp').DataTable({
            responsive: true,
            ordering: false,
            processing: true,
            lengthMenu: [
                [-1],
                ["All"]
            ],
            columns: [{
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                    visible: false
                },
                {
                    data: 'nm_sub_kegiatan',
                    name: 'nm_sub_kegiatan',
                    visible: false
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6'
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
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                }
            ],
        });

        $('#tgl_spp').on('change', function() {
            let tanggal = this.value;
            let bulan = new Date(tanggal);
            let bulan1 = bulan.getMonth() + 1;
            $('#bulan').val(bulan1).trigger('change');
        });

        $('#bank').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $("#nm_bank").val(nama);
        });

        $('#rekening').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            let npwp = $(this).find(':selected').data('npwp');
            $("#nm_rekening").val(nama);
            $("#npwp").val(npwp);
        });

        $('#no_spd').on('select2:select', function() {
            $("#kd_sub_kegiatan").val(null).change();
            let no_spd = this.value;

            // CARI SUB KEGIATAN
            $.ajax({
                url: "{{ route('spp_tu.kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spd: no_spd,
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}" data-kd_program="${data.kd_program}" data-nm_program="${data.nm_program}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            });
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            $("#kode_rekening").val(null).change();
            let kd_sub_kegiatan = this.value;
            let nama = $(this).find(':selected').data('nama');

            if (kd_sub_kegiatan == '-') {
                $('#kd_sub_kegiatan').val(null).change();
                return;
            }

            $('#kegiatan').val(kd_sub_kegiatan + ' | ' + nama);

            // CARI REKENING
            $.ajax({
                url: "{{ route('spp_tu.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                },
                success: function(data) {
                    $('#kode_rekening').empty();
                    $('#kode_rekening').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kode_rekening').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            });
        });

        $('#kode_rekening').on('select2:select', function() {
            $("#sumber").val(null).change();
            let kd_rek6 = this.value;

            // CARI NILAI ANGKAS, SPD, ANGGARAN
            $.ajax({
                url: "{{ route('spp_tu.ang_spd_angkas') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_rek6: kd_rek6,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    no_spp: document.getElementById('no_spp').value,
                    tgl_spp: document.getElementById('tgl_spp').value,
                    beban: document.getElementById('beban').value,
                    status_angkas: document.getElementById('status_angkas').value,
                },
                success: function(data) {
                    $('#total_anggaran').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.anggaran.nilai));
                    $('#lalu_anggaran').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.anggaran.rektotal_spp_lalu));
                    $('#sisa_anggaran').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.anggaran.nilai - data.anggaran
                        .rektotal_spp_lalu));

                    $('#total_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.spd));
                    $('#lalu_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.transaksi));
                    $('#sisa_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.spd - data.transaksi));

                    $('#total_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.angkas));
                    $('#lalu_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.transaksi));
                    $('#sisa_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.angkas - data.transaksi));
                }
            });

            // CARI SUMBER DANA
            $.ajax({
                url: "{{ route('penagihan.cari_sumber_dana') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kdgiat: document.getElementById('kd_sub_kegiatan').value,
                    skpd: document.getElementById('kd_skpd').value,
                    kdrek: kd_rek6
                },
                success: function(data) {
                    $('#sumber').empty();
                    $('#sumber').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#sumber').append(
                            `<option value="${data.kd_sumber}" data-nama="${data.nm_sumber}" data-nilai="${data.nilai}">${data.kd_sumber} | ${data.nm_sumber}</option>`
                        );
                    })
                }
            });
        });

        $('#sumber').on('change', function() {
            let selected = $(this).find('option:selected');
            let sumber = this.value;
            if (sumber == 'null') {
                alert('Sumber dana tidak dapat digunakan!');
                $('#sumber_dana').val(null).change();
                return;
            }
            let nama = $(this).find(':selected').data('nama');
            let nilai = $(this).find(':selected').data('nilai');

            $("#total_sumber").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
            $.ajax({
                url: "{{ route('penagihan.realisasi_sumber_dana') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    sumber: sumber,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    kd_rek6: document.getElementById('kode_rekening').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                },
                success: function(data) {
                    $("#lalu_sumber").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data));
                    $("#sisa_sumber").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(nilai - data));
                }
            })
        });

        $('#tambah_rincian').on('click', function() {
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;

            let tgl_spp = document.getElementById('tgl_spp').value;

            if (!tgl_spp) {
                alert('Pilih Tanggal SPP Terlebih Dahulu...!');
                $('#kd_sub_kegiatan').val(null).change();
                return;
            }

            if (!kd_sub_kegiatan) {
                alert('Isi Kode Kegiatan Terlebih Dahulu....!!!')
                return;
            }

            $('#modal_rincian').modal('show');
        });

        $('#simpan_rincian').on('click', function() {
            let skpd = document.getElementById('skpd').value;
            let kegiatan = document.getElementById('kegiatan').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kode_rekening = document.getElementById('kode_rekening').value;
            let sumber = document.getElementById('sumber').value;
            let status_anggaran = document.getElementById('status_anggaran').value;
            let beban = document.getElementById('beban').value;

            let sisa_anggaran = rupiah(document.getElementById('sisa_anggaran').value);
            let sisa_spd = rupiah(document.getElementById('sisa_spd').value);
            let sisa_sumber = rupiah(document.getElementById('sisa_sumber').value);
            let sisa_angkas = rupiah(document.getElementById('sisa_angkas').value);

            let total = rupiah(document.getElementById('total').value);

            let nilai = angka(document.getElementById('nilai').value);

            let sub_kegiatan = $('#kd_sub_kegiatan').find('option:selected');
            let nm_sub_kegiatan = sub_kegiatan.data('nama');

            let rekening = $('#kode_rekening').find('option:selected');
            let nm_rek6 = rekening.data('nama');
            // let anggaran = rekening.data('nilai');
            // let lalu = rekening.data('lalu');
            // let sp2d = rekening.data('sp2d');

            if (!sumber) {
                alert("Sumber Dana kosong");
                return;
            }

            if (sumber == '221020101') {
                alert(
                    'Silahkan konfirmasi ke perbendaharaan jika ingin transaksi sumber dana DID, jika tidak maka transaksi tidak bisa di approve oleh perbendahaaraan, terima kasih'
                );
            }

            let akumulasi = nilai;

            if (nilai > sisa_angkas) {
                alert('Nilai Melebihi Sisa Anggaran Kas...!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai > sisa_spd) {
                alert('Nilai Melebihi Sisa Dana SPD...!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai > sisa_anggaran) {
                alert('Nilai Melebihi Sisa Anggaran...!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai > sisa_sumber) {
                alert('Nilai Melebihi Sisa Sumber Dana...!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai == 0) {
                alert('Nilai Nol.....!!!, Cek Lagi...!!!');
                return;
            }

            if (!kode_rekening) {
                alert("Rekening kosong");
                return;
            }

            // cek data di detail spp dan inputan
            let tampungan = detail.rows().data().toArray().map((value) => {
                let result = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    sumber: value.sumber,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.kd_rek6 == kode_rekening && data.sumber == sumber) {
                    return '2';
                }
                if (data.kd_sub_kegiatan != kd_sub_kegiatan) {
                    return '3';
                }
            });
            if (kondisi.includes("2")) {
                alert('Tidak boleh memilih rekening dengan sumber dana yang sama dlm 1 SPP');
                return;
            }
            if (kondisi.includes("3")) {
                alert('Tidak boleh memilih kegiatan berbeda dalam 1 SPP!');
                return;
            }

            // proses input ke tabel input detail spp
            alert('Data Detail Tersimpan');
            detail.row.add({
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'kd_rek6': kode_rekening,
                'nm_rek6': nm_rek6,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'sumber': sumber,
                'aksi': `<a href="javascript:void(0);" onclick="hapus('${kd_sub_kegiatan}','${kode_rekening}','${nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            $("#total").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total + nilai));
        });

        $('#simpan').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let no_urut = document.getElementById('no_urut').value;
            let tgl_spp = document.getElementById('tgl_spp').value;
            let tgl_lalu = document.getElementById('tgl_lalu').value;
            let bulan = document.getElementById('bulan').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let no_spd = document.getElementById('no_spd').value;
            let bank = document.getElementById('bank').value;
            let nm_bank = document.getElementById('nm_bank').value;
            let rekening = document.getElementById('rekening').value;
            let nm_rekening = document.getElementById('nm_rekening').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let beban = document.getElementById('beban').value;
            let npwp = document.getElementById('npwp').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let total = rupiah(document.getElementById('total').value);
            let tahun_input = tgl_spp.substr(0, 4);

            let sub_kegiatan = $('#kd_sub_kegiatan').find('option:selected');
            let nm_sub_kegiatan = sub_kegiatan.data('nama');
            let kd_program = sub_kegiatan.data('kd_program');
            let nm_program = sub_kegiatan.data('nm_program');

            let detail_spp = detail.rows().data().toArray().map((value) => {
                let data = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    nm_sub_kegiatan: value.nm_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: rupiah(value.nilai),
                    sumber: value.sumber,
                };
                return data;
            });

            if (!no_spp) {
                alert('Nomor tidak boleh kosong');
                return;
            }

            if (!tgl_spp) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }

            if (tgl_spp < tgl_lalu) {
                alert('Tanggal SPP tidak boleh kurang dari SPP Lalu...!!!');
                return;
            }

            if (tahun_anggaran != tahun_input) {
                alert('Tahun input tidak sesuai dengan tahun anggaran');
                return;
            }

            if (!kd_skpd) {
                alert('SKPD tidak boleh kosong!');
                return;
            }

            if (!no_spd) {
                alert('No. SPD tidak boleh kosong!');
                return;
            }

            if (!kd_sub_kegiatan) {
                alert('Kegiatan tidak boleh kosong!');
                return;
            }

            if (!bank) {
                alert('Bank tidak boleh kosong!');
                return;
            }

            if (!rekening) {
                alert('Rekening bank tidak boleh kosong!');
                return;
            }

            if (!npwp) {
                alert('NPWP tidak boleh kosong!');
                return;
            }

            if (!keterangan) {
                alert('Keterangan tidak boleh kosong!');
                return;
            }

            if (total == 0) {
                alert('Rincian tidak boleh kosong!');
                return;
            }

            if (detail_spp.length == 0) {
                alert('Rincian tidak boleh kosong!');
                return;
            }

            let data = {
                no_spp,
                no_urut,
                tgl_spp,
                bulan,
                kd_skpd,
                nm_skpd,
                no_spd,
                bank,
                nm_bank,
                beban,
                npwp,
                keterangan,
                rekening,
                nm_rekening,
                kd_sub_kegiatan,
                nm_sub_kegiatan,
                kd_program,
                nm_program,
                total,
                detail_spp
            };

            $('#simpan').prop('disabled', true);

            $.ajax({
                url: "{{ route('spp_tu.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil ditambahkan!');
                        window.location.href = "{{ route('spp_tu.index') }}";
                    } else if (response.message == '2') {
                        alert('Nomor Telah Dipakai!');
                        $('#simpan').prop('disabled', false);
                        return;
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan').prop('disabled', false);
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

        $('#cari').on('click', function() {
            $('#no_spp').val(null);
            cari_nomor();
        });

        function cari_nomor() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tahun_anggaran = "{{ tahun_anggaran() }}";

            $.ajax({
                url: "{{ route('sppls.cari_nospp') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    let no_spp = data.nilai + "/SPP/" + "TU" + "/" + kd_skpd + "/" +
                        tahun_anggaran;
                    $('#no_spp').val(no_spp);
                    $('#no_urut').val(data.nilai);
                }
            })
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

    function hapus(kd_sub_kegiatan, kd_rek6, nilai) {
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai :  ' + nilai +
            ' ?');
        let total = rupiah(document.getElementById('total').value);
        let tabel = $('#rincian_spp').DataTable();

        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.kd_sub_kegiatan == kd_sub_kegiatan && data.kd_rek6 == kd_rek6
            }).remove().draw();
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - parseFloat(nilai)));
        }
    }
</script>
