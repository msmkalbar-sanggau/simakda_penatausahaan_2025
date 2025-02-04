<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        let tabel = $('#rincian_spm').DataTable({
            responsive: true,
            ordering: false,
            columns: [{
                    data: 'kd_sub_kegiatan'
                },
                {
                    data: 'kd_rek6'
                },
                {
                    data: 'nm_rek6'
                },
                {
                    data: 'nilai'
                },
            ]
        });

        $.ajax({
            url: "{{ route('spm.tgl_spm_lalu') }}",
            type: "POST",
            dataType: 'json',
            success: function(data) {
                $('#tgl_spm_lalu').val(data.tanggal);
            }
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#no_spp').on('select2:select', function() {
            let no_spp = this.value;
            let str = no_spp.split('/');

            let tgl_spp = $(this).find(':selected').data('tgl_spp');
            let no_spd = $(this).find(':selected').data('no_spd');
            let bulan = $(this).find(':selected').data('bulan');
            let bank = $(this).find(':selected').data('bank');
            let kd_skpd = $(this).find(':selected').data('kd_skpd');
            let nm_skpd = $(this).find(':selected').data('nm_skpd');
            let keperluan = $(this).find(':selected').data('keperluan');
            let beban = $(this).find(':selected').data('beban');
            let rekanan = $(this).find(':selected').data('rekanan');
            let jenis = $(this).find(':selected').data('jenis');
            let npwp = $(this).find(':selected').data('npwp');
            let rekening = $(this).find(':selected').data('rekening');
            let no_spm = document.getElementById('no_spm').value;
            let tahun_anggaran = "{{ tahun_anggaran() }}";
            let nm_bulan = cari_bulan(bulan);
            let nm_beban = cari_beban(beban);
            let nm_jenis = cari_jenis(beban, jenis);
            let nm_bank = cari_bank(bank);
            let tanggal1 = new Date(tgl_spp);
            let bulan1 = tanggal1.getMonth() + 1;
            $("#tgl_spp").val(tgl_spp);
            $("#no_spd").val(no_spd);
            $("#bulan").val(bulan);
            $('#nm_bulan').val(nm_bulan);
            $("#bank").val(bank);
            $("#nm_bank").val(nm_bank);
            $("#kd_skpd").val(kd_skpd);
            $("#nm_skpd").val(nm_skpd);
            $("#keperluan").val(keperluan);
            $("#beban").val(beban);
            $("#bulan_spm").val(bulan1);
            $("#nm_beban").val(nm_beban);
            $("#rekanan").val(rekanan);
            $("#jenis").val(jenis);
            $("#nm_jenis").val(nm_jenis);
            $("#npwp").val(npwp);
            $("#rekening").val(rekening);
            tabel.clear().draw();
            // Detail SPM
            $.ajax({
                url: "{{ route('spm.detail_spm') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spp: no_spp
                },
                success: function(data) {
                    let total = 0;
                    $.each(data, function(index, data) {
                        tabel.row.add({
                            'kd_sub_kegiatan': data.kd_sub_kegiatan,
                            'kd_rek6': data.kd_rek6,
                            'nm_rek6': data.nm_rek6,
                            'nilai': new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2
                            }).format(data.nilai),
                        }).draw();
                        total += parseFloat(data.nilai);
                    })
                    $('#total').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total));
                }
            });

            // No SPD
            $.ajax({
                url: "{{ route('spm.cari_nospd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spd: no_spd
                },
                success: function(data) {
                    $('#tgl_spd').val(data.tgl_spd);
                }
            });

            get_spm(no_spp, kd_skpd, beban, tahun_anggaran, no_spm, str);
        });

        // $('#cari_nospm').on('click', function() {
        //     let no_spp = document.getElementById('no_spp').value;
        //     let kd_skpd = document.getElementById('kd_skpd').value;
        //     let beban = document.getElementById('beban').value;
        //     let tahun_anggaran = "{{ tahun_anggaran() }}";
        //     let no_spm = document.getElementById('no_spm').value;
        //     get_spm(no_spp, kd_skpd, beban, tahun_anggaran, no_spm);
        // });
        // simpan spm
        $('#simpan_spm').on('click', function() {
            let no_spm = document.getElementById("no_spm").value;
            let no_spp = document.getElementById("no_spp").value;
            let tgl_spm = document.getElementById("tgl_spm").value;
            let tgl_spm_lalu = document.getElementById("tgl_spm_lalu").value;
            let urut = document.getElementById("no_spm").value;
            let tgl_spp = document.getElementById("tgl_spp").value;
            let beban = document.getElementById("beban").value;
            let bulan = document.getElementById("bulan").value;
            let keperluan = document.getElementById("keperluan").value;
            let rekanan = document.getElementById("rekanan").value;
            let bank = document.getElementById("bank").value;
            let npwp = document.getElementById("npwp").value;
            let rekening = document.getElementById("rekening").value;
            let nm_skpd = document.getElementById("nm_skpd").value;
            let kd_skpd = document.getElementById("kd_skpd").value;
            let no_spd = document.getElementById("no_spd").value;
            let jenis = document.getElementById("jenis").value;
            let total = rupiah(document.getElementById("total").value);
            let tahun_anggaran = "{{ tahun_anggaran() }}";
            let tahun_input = tgl_spm.substring(0, 4);
            if (!tgl_spm) {
                alert('Silahkan pilih tanggal SPM!');
                return;
            }
            if (tgl_spm < tgl_spm_lalu) {
                alert('Tanggal SPM tidak boleh kurang dari SPM Lalu...!!!');
                return;
            }
            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }
            if (!no_spm) {
                alert("No SPM Tidak Boleh Kosong");
                return;
            }
            if (no_spm == 0) {
                alert("No SPM Tidak Boleh NULL");
                return;
            }
            if (!no_spd) {
                alert("No SPD Tidak Boleh Kosong");
                return;
            }
            if (tgl_spm < tgl_spp) {
                alert("Tanggal SPM tidak boleh lebih kecil dari tanggal SPP");
                return;
            }
            if (keperluan.length > 1000) {
                alert('Keterangan Tidak boleh lebih dari 1000 karakter');
                return;
            }
            if (total == 0) {
                alert('Total Rincian tidak boleh kosong!Silahkan refresh!');
                return;
            }
            $('#simpan_spm').prop('disabled', true);
            $.ajax({
                url: "{{ route('spm.simpan_spm') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spm: no_spm,
                    tgl_spm: tgl_spm,
                    no_spp: no_spp,
                    kd_skpd: kd_skpd,
                    nm_skpd: nm_skpd,
                    tgl_spp: tgl_spp,
                    bulan: bulan,
                    no_spd: no_spd,
                    keperluan: keperluan,
                    beban: beban,
                    bank: bank,
                    rekanan: rekanan,
                    rekening: rekening,
                    npwp: npwp,
                    total: total,
                    urut: urut,
                    no_spp: no_spp,
                    jenis: jenis,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    if (data.message == '0') {
                        alert('Gagal Simpan..!!');
                        $('#simpan_spm').prop('disabled', false);
                    } else if (data.message == '1') {
                        alert('Nomor SPM Sudah Terpakai...!!!,  Ganti Nomor SPM...!!!');
                        $('#simpan_spm').prop('disabled', false);
                    } else if (data.message == '3') {
                        alert(
                            'Nomor SPP Sudah Terpakai...!!!,  Pilih Nomor SPP Lainnya...!!!'
                        );
                        $('#simpan_spm').prop('disabled', false);
                    } else {
                        alert('Nomor bisa dipakai');
                        $('#simpan_spm').prop('disabled', false);
                        document.getElementById('potongan_spm').href = data.url;
                        $('#konfirmasi_potongan').modal('show');
                    }
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            });
        });

        function cari_bulan(bulan) {
            switch (bulan) {
                case 1:
                    return 'Januari';
                    break;
                case 2:
                    return 'Februari';
                    break;
                case 3:
                    return 'Maret';
                    break;
                case 4:
                    return 'April';
                    break;
                case 5:
                    return 'Mei';
                    break;
                case 6:
                    return 'Juni';
                    break;
                case 7:
                    return 'Juli';
                    break;
                case 8:
                    return 'Agustus';
                    break;
                case 9:
                    return 'September';
                    break;
                case 10:
                    return 'Oktober';
                    break;
                case 11:
                    return 'November';
                    break;
                case 12:
                    return 'Desember';
                    break;
                default:
                    break;
            }
        }

        function cari_beban(beban) {
            switch (beban) {
                case 1:
                    return 'UP'
                    break;
                case 2:
                    return 'GU'
                    break;
                case 3:
                    return 'TU'
                    break;
                case 4:
                    return 'LS GAJI'
                    break;
                case 5:
                    return 'LS Pihak Ketiga Lainnya'
                    break;
                case 6:
                    return 'LS Barang Jasa'
                    break;
                case 7:
                    return 'GU NIHIL'
                    break;
                default:
                    break;
            }
        }

        function cari_jenis(beban, jenis) {
            $.ajax({
                url: "{{ route('spm.cari_jenis') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    beban: beban,
                    jenis: jenis,
                },
                success: function(data) {
                    $('#nm_jenis').val(data.nama);
                }
            })
        }

        function cari_bank(bank) {
            $.ajax({
                url: "{{ route('spm.cari_bank') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    bank: bank,
                },
                success: function(data) {
                    $('#nm_bank').val(data.nama);
                }
            })
        }

        function get_spm(no_spp, kd_skpd, beban, tahun_anggaran, no_spm, str) {
            let bulan1 = document.getElementById('bulan').value;
            let bulan2 = document.getElementById('bulan_spm').value;
            if (!no_spp) {
                alert('Pilih terlebih dahulu No SPP');
                return;
            }
            let jns;
            let jns2;
            if (beban == '4' || beban == '6' || beban == '5') {
                jns = 'LS';
                bulan = bulan1;
            } else if (beban == '1') {
                jns = 'UP';
                bulan = bulan1;
            } else if (beban == '2') {
                jns = 'GU';
                bulan = bulan2;
            } else if (beban == '3') {
                jns = 'TU';
                bulan = bulan1;
            } else if (beban == '7') {
                jns = 'GU-NIHIL';
                bulan = bulan2;
            }

            if (beban == 4) {
                $jns2 = 'BTL';
            } else {
                $jns2 = 'BL';
            }

            $.ajax({
                url: "{{ route('spm.cari_nospm') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spm: no_spm,
                    no_spp: no_spp
                },
                success: function(data) {
                    if (str[2] != data.nilai) {
                        alert('Nomor SPM tidak sama dengan Nomor SPP, No SPM Terakhir ' + data
                            .nilai + ' !');
                        return;
                    }
                    let nomor = "61.03/03.0/" + data.nilai + "/" + jns + "/" + kd_skpd + "/" + "M" +
                        "/" + bulan + "/" + tahun_anggaran
                    $('#no_spm').val(nomor);
                    $('#urut').val(data.nilai);
                }
            });
        }

        function rupiah(n) {
            let n1 = n.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }

    });

    function deleteData(kd_sub_kegiatan, kode_rekening, nm_rekening, sumber_dana, nilai_rincian) {
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kode_rekening + '  Nilai :  ' + nilai_rincian +
            ' ?');
        let total = rupiah(document.getElementById('total').value);
        let tabel = $('#rincian_sppls').DataTable();
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.sumber == sumber_dana && data.kd_sub_kegiatan == kd_sub_kegiatan &&
                    data.kd_rek6 == kode_rekening
            }).remove().draw();
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - nilai_rincian));
        }
    }
</script>
