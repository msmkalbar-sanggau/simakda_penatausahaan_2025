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
            theme: 'bootstrap-5'
        });

        let tabel_rincian = $('#detail').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_bukti',
                    name: 'no_bukti',
                    visible: false
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'nm_sub_kegiatan',
                    name: 'nm_sub_kegiatan',
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
                    data: 'sumber',
                    name: 'sumber',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        let input_rincian = $('#input_rincian').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_bukti',
                    name: 'no_bukti',
                    visible: false
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'nm_sub_kegiatan',
                    name: 'nm_sub_kegiatan',
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
                    data: 'sumber',
                    name: 'sumber',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        $('#tambah_rincian').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jenis_beban = document.getElementById('jenis_beban').value;

            if (no_bukti != '' && tgl_bukti != '' && kd_skpd != '' && jenis_beban != '') {
                load_kegiatan();
                $('#modal_rincian').modal('show');
            } else {
                alert('Harap Isi Kode SKPD, Tanggal Transaksi & Jenis Beban SP2D');
                return;
            }
        });

        $('#tgl_bukti').on('change', function() {
            let tanggal = this.value;
            $.ajax({
                url: "{{ route('transaksi_bos.status') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    tanggal: tanggal,
                },
                success: function(data) {
                    $('#status_anggaran').val(data.status_ang.nama);
                    $('#jns_ang').val(data.status_ang.jns_ang);

                    $('#status_angkas').val(data.status_angkas.status);
                }
            })
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            $('#kode_rekening').empty();
            $('#sumber').empty();
            let kd_sub_kegiatan = this.value;
            let no_bukti = document.getElementById('no_bukti').value;
            let jenis_beban = document.getElementById('jenis_beban').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            $('#total_spd').val(null);
            $('#lalu_spd').val(null);
            $('#sisa_spd').val(null);

            $('#total_angkas').val(null);
            $('#lalu_angkas').val(null);
            $('#sisa_angkas').val(null);

            $('#total_anggaran').val(null);
            $('#lalu_anggaran').val(null);
            $('#sisa_anggaran').val(null);

            $('#total_sumber').val(null);
            $('#lalu_sumber').val(null);
            $('#sisa_sumber').val(null);
            $.ajax({
                url: "{{ route('transaksi_bos.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_bukti: no_bukti,
                    kd_skpd: kd_skpd,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    jenis_beban: jenis_beban,
                },
                success: function(data) {
                    $('#kode_rekening').empty();
                    $('#kode_rekening').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kode_rekening').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}" data-sp2d="${data.sp2d}" data-lalu="${data.lalu}" data-anggaran="${data.anggaran}">${data.kd_rek6} | ${data.nm_rek6} | ${new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.lalu)}</option>`
                        );
                    })
                }
            });
        });

        $('#kode_rekening').on('select2:select', function() {
            $('#sumber').empty();
            let rekening = this.value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let no_bukti = document.getElementById('no_bukti').value;
            let jenis_beban = document.getElementById('jenis_beban').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jns_ang = document.getElementById('jns_ang').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let status_angkas = document.getElementById('status_angkas').value;

            let anggaran = $(this).find(':selected').data('anggaran');
            let lalu = $(this).find(':selected').data('lalu');
            let sp2d = $(this).find(':selected').data('sp2d');

            $('#total_spd').val(null);
            $('#lalu_spd').val(null);
            $('#sisa_spd').val(null);

            $('#total_angkas').val(null);
            $('#lalu_angkas').val(null);
            $('#sisa_angkas').val(null);

            $('#total_anggaran').val(null);
            $('#lalu_anggaran').val(null);
            $('#sisa_anggaran').val(null);

            $('#total_sumber').val(null);
            $('#lalu_sumber').val(null);
            $('#sisa_sumber').val(null);

            $('#total_anggaran').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(anggaran));
            $('#lalu_anggaran').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(lalu));
            $('#sisa_anggaran').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(anggaran - lalu));

            $.ajax({
                url: "{{ route('transaksi_bos.spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_bukti: no_bukti,
                    status_angkas: status_angkas,
                    tgl_bukti: tgl_bukti,
                    kd_skpd: kd_skpd,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    jenis_beban: jenis_beban,
                    rekening: rekening,
                    jns_ang: jns_ang,
                },
                success: function(data) {
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

            $.ajax({
                url: "{{ route('transaksi_bos.sumber') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_bukti: no_bukti,
                    kd_skpd: kd_skpd,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    jenis_beban: jenis_beban,
                    rekening: rekening,
                    jns_ang: jns_ang,
                },
                success: function(data) {
                    $('#sumber').empty();
                    $('#sumber').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#sumber').append(
                            `<option value="${data.kode}" data-nama="${data.nama}" data-nilai="${data.nilai}" data-lalu="${data.lalu}">${data.kode} | ${data.nama}</option>`
                        );
                    })
                }
            });
        });

        $('#sumber').on('select2:select', function() {
            let nilai = $(this).find(':selected').data('nilai');
            let lalu = $(this).find(':selected').data('lalu');

            $('#total_sumber').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
            $('#lalu_sumber').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(lalu));
            $('#sisa_sumber').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai - lalu));
        });

        $('#simpan_rincian').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kode_rekening = document.getElementById('kode_rekening').value;
            let sumber = document.getElementById('sumber').value;
            let status_anggaran = document.getElementById('status_anggaran').value;
            let beban = document.getElementById('jenis_beban').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let status_angkas = document.getElementById('status_angkas').value;

            let sisa_anggaran = rupiah(document.getElementById('sisa_anggaran').value);
            let sisa_angkas = rupiah(document.getElementById('sisa_angkas').value);
            let sisa_spd = rupiah(document.getElementById('sisa_spd').value);
            let sisa_sumber = rupiah(document.getElementById('sisa_sumber').value);
            let total_rincian = rupiah(document.getElementById('total_rincian').value);
            let total = rupiah(document.getElementById('total').value);
            let nilai = angka(document.getElementById('nilai').value);

            let sub_kegiatan = $('#kd_sub_kegiatan').find('option:selected');
            let nm_sub_kegiatan = sub_kegiatan.data('nama');

            let rekening = $('#kode_rekening').find('option:selected');
            let nm_rek6 = rekening.data('nama');
            let anggaran = rekening.data('anggaran');
            let lalu = rekening.data('lalu');
            let sp2d = rekening.data('sp2d');

            let tahun_input = tgl_bukti.substr(0, 4);

            let akumulasi = total_rincian + nilai;

            if (!sumber) {
                alert('Pilih Sumber Dana Dahulu');
                return;
            }

            if (!kode_rekening) {
                alert('Pilih rekening Dahulu');
                return;
            }

            if (!status_anggaran) {
                alert('pilih tanggal dahulu');
                return;
            }

            if (nilai == 0) {
                alert('Nilai Nol.....!!!, Cek Lagi...!!!');
                return;
            }

            if (!kd_sub_kegiatan) {
                alert('Pilih Kegiatan Dahulu');
                return;
            }

            let tampungan = tabel_rincian.rows().data().toArray().map((value) => {
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
                if (data.kd_sub_kegiatan == kd_sub_kegiatan && data.kd_rek6 == kode_rekening) {
                    return '4';
                }
            });
            if (kondisi.includes("2")) {
                alert('Tidak boleh memilih rekening dengan sumber dana yang sama dlm 1 Transaksi');
                return;
            }
            if (kondisi.includes("3")) {
                alert('Tidak boleh memilih kegiatan berbeda dalam 1 Transaksi!');
                return;
            }
            if (kondisi.includes("4")) {
                alert('Tidak boleh memilih rekening yang sama dalam 1 Transaksi!');
                return;
            }

            // proses input ke tabel input detail spp
            alert('Data Detail Tersimpan');
            tabel_rincian.row.add({
                'no_bukti': no_bukti,
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'kd_rek6': kode_rekening,
                'nm_rek6': nm_rek6,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'sumber': sumber,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kode_rekening}','${nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            input_rincian.row.add({
                'no_bukti': no_bukti,
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'kd_rek6': kode_rekening,
                'nm_rek6': nm_rek6,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'sumber': sumber,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kode_rekening}','${nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            $("#total").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(akumulasi));
            $("#total_rincian").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(akumulasi));

            $('#kd_sub_kegiatan').val(null).change();
            $('#kode_rekening').empty();
            $('#sumber').empty();

            $('#total_spd').val(null);
            $('#lalu_spd').val(null);
            $('#sisa_spd').val(null);

            $('#total_anggaran').val(null);
            $('#lalu_anggaran').val(null);
            $('#sisa_anggaran').val(null);

            $('#total_angkas').val(null);
            $('#lalu_angkas').val(null);
            $('#sisa_angkas').val(null);

            $('#total_sumber').val(null);
            $('#lalu_sumber').val(null);
            $('#sisa_sumber').val(null);

            $('#nilai').val(null);
        });

        $('#simpan').on('click', function() {
            let no_bukti = alltrim(document.getElementById('no_bukti').value);
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;

            let sub_kegiatan = $('#kd_sub_kegiatan').find('option:selected');
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let nm_sub_kegiatan = sub_kegiatan.data('nama');

            let kd_satdik = $('#satdik').find('option:selected');
            let satdik = document.getElementById('satdik').value;
            let nama_satdik = kd_satdik.data('nama');

            let beban = document.getElementById('jenis_beban').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let jenis_bos = document.getElementById('jenis_bos').value;
            let tahap = document.getElementById('tahap').value;
            let keterangan = document.getElementById('keterangan').value;
            let total = rupiah(document.getElementById('total').value);

            if (!no_bukti) {
                alert('Nomor Tidak Boleh Kosong');
                return;
            }

            if (!tgl_bukti) {
                alert('Tanggal Tidak Boleh Kosong');
                return;
            }

            if (!kd_skpd) {
                alert('Kode SKPD Tidak Boleh Kosong');
                return;
            }

            let tahun_input = tgl_bukti.substr(0, 4);

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!satdik) {
                alert('SATDIK Tidak Boleh Kosong');
                return;
            }

            if (!beban) {
                alert('Jenis Beban Tidak Boleh Kosong');
                return;
            }

            if (!jenis_bos) {
                alert('Jenis BOS Tidak Boleh Kosong');
                return;
            }

            if (!tahap) {
                alert('Tahap Tidak Boleh Kosong');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }

            let rincian = tabel_rincian.rows().data().toArray().map((value) => {
                let data = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    nm_sub_kegiatan: value.nm_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    sumber: value.sumber,
                    nilai: rupiah(value.nilai),
                };
                return data;
            });

            if (total == 0) {
                alert('Rincian Tidak ada rekening!');
                return;
            }

            let data = {
                no_bukti,
                tgl_bukti,
                kd_skpd,
                nm_skpd,
                satdik,
                nama_satdik,
                keterangan,
                pembayaran,
                beban,
                jenis_bos,
                tahap,
                total,
                rincian
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('transaksi_bos.simpan') }}",
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
                            "{{ route('transaksi_bos.index') }}";
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

        function load_kegiatan() {
            $('#kd_sub_kegiatan').empty();
            let jenis_beban = document.getElementById('jenis_beban').value;
            let jns_ang = document.getElementById('jns_ang').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            $.ajax({
                url: "{{ route('transaksi_bos.kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jenis_beban: jenis_beban,
                    jns_ang: jns_ang,
                    kd_skpd: kd_skpd,
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
            });
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

    function deleteData(no_bukti, kd_rek6, kd_rek6, nilai) {
        let tabel = $('#detail').DataTable();
        let tabel1 = $('#input_rincian').DataTable();
        let total = rupiah(document.getElementById('total').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai :  ' + nilai +
            ' ?');

        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.kd_rek6 == kd_rek6 && rupiah(data.nilai) == parseFloat(
                    nilai)
            }).remove().draw();
            tabel1.rows(function(idx, data, node) {
                return data.kd_rek6 == kd_rek6 && rupiah(data.nilai) == parseFloat(
                    nilai)
            }).remove().draw();
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - parseFloat(nilai)));
            $('#total_rincian').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - parseFloat(nilai)));
        } else {
            return false;
        }

    }
</script>
