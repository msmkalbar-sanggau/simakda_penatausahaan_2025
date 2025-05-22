<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#card_penagihan').hide();

        let tabel = $('#rincian_sppls').DataTable({
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
                {
                    visible: false,
                    data: 'sumber'
                },
                {
                    data: 'nm_sumber'
                },
                // {
                //     visible: false,
                //     data: 'volume_output'
                // },
                // {
                //     visible: false,
                //     data: 'satuan_output'
                // },
                {
                    data: 'hapus'
                }
            ]
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('.select2-modal').select2({
            dropdownParent: $('#tambah_rincianspp .modal-content'),
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#beban').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#tgl_spp').on('change', function() {
            let tanggal = this.value;
            let bulan = new Date(tanggal);
            let bulan1 = bulan.getMonth() + 1;
            $('#bulan').val(bulan1).trigger('change');
        });

        $('#rekanan').on('change', function() {
            let pimpinan = $(this).find(':selected').data('pimpinan');
            let alamat = $(this).find(':selected').data('alamat');
            $("#pimpinan").val(pimpinan);
            $("#alamat").val(alamat);
        });

        $('#nm_penerima').on('select2:select', function() {
            let beban = document.getElementById('beban').value;
            let jenis = document.getElementById('jenis').value;

            if (!beban || !jenis) {
                alert('Silahkan Pilih Beban dan Jenis!');
                $('#nm_penerima').val(null).change();
                return;
            }

            let rekening = $(this).find(':selected').data('rekening');
            let npwp = $(this).find(':selected').data('npwp');
            let nmrekan = $(this).find(':selected').data('nmrekan');
            let pimpinan = $(this).find(':selected').data('pimpinan');
            let bank = $(this).find(':selected').data('bank');
            let nmbank = $(this).find(':selected').data('nmbank');
            let alamat = $(this).find(':selected').data('alamat');
            $("#rekening").val(rekening);
            $("#npwp").val(npwp);
            $("#bank").val(bank);
            $("#nmbank").val(nmbank);
            $("#rekanan").val(nmrekan);
            $("#pimpinan").val(pimpinan);
            $("#alamat").val(alamat);

            if (beban == '6' || beban == '5') {
                $('#rekanan').val(nmrekan);
                $('#pimpinan').val(pimpinan);
                $('#alamat').val(alamat);
            } else {
                if (beban == '4' && jenis == '9') {
                    $('#rekanan').val(nmrekan);
                    $('#pimpinan').val(pimpinan);
                    $('#alamat').val(alamat);
                }
            }
        });

        $('#beban').on('select2:select', function() {
            let beban = this.value;
            let jenis = document.getElementById('jenis').value;
            let tgl_spp = document.getElementById('tgl_spp').value;
            let tahun_anggaran = "{{ tahun_anggaran() }}";
            let kd_skpd = document.getElementById('kd_skpd').value;
            let bulan = document.getElementById('bulan').value;
            kosong();
            if (!tgl_spp) {
                alert('Pilih tanggal SPD terlebih dahulu!');
                $("#beban").val(null).change();
                return;
            }
            // cari nomor spp
            let jenis_beban;
            let jenis_beban2;
            if (beban == '4') {
                jenis_beban = "LS";
                let tampungan = tabel.rows().data().toArray().map((value) => {
                    let result = {
                        kd_rek6: value.kd_rek6,
                    };
                    return result;
                });
                let kondisi = tampungan.map(function(data) {
                    let kdrek6 = data.kd_rek6;
                    if (kdrek6.substr(0, 5) == '51101') {
                        jenis_beban = "LS";
                    }
                });
                jenis_beban2 = "LS";
            } else {
                jenis_beban = "LS";
                jenis_beban2 = "BL";
            }
            // no spp
            $.ajax({
                url: "{{ route('sppls.cari_nospp') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jenis_beban2: jenis_beban2,
                },
                success: function(data) {
                    let no_spp = "61.03/02.0/" + data.nilai + "/" + jenis_beban + "/" +
                        kd_skpd + "/M/" + bulan + "/" +
                        tahun_anggaran;
                    $('#no_urut').val(data.nilai);
                    $('#no_spp').val(no_spp);
                }
            })
            // cari jenis
            $.ajax({
                url: "{{ route('sppls.cari_jenis') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    beban: beban,
                },
                success: function(data) {
                    $('#jenis').empty();
                    $('#jenis').append(`<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#jenis').append(
                            `<option value="${data.id}" data-nama="${data.text}">${data.text}</option>`
                        );
                    })
                }
            })
            // cari nomor spd
            $.ajax({
                url: "{{ route('sppls.cari_nomor_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    beban: beban,
                    tgl_spp: tgl_spp,
                },
                success: function(data) {
                    $('#nomor_spd').empty();
                    $('#nomor_spd').append(`<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#nomor_spd').append(
                            `<option value="${data.no_spd}" data-tgl="${data.tgl_spd}" data-bln="${data.bulan_awal}" data-total="${data.total}">${data.no_spd} | ${data.tgl_spd} | ${data.total} </option>`
                        );
                    })
                }
            })

            // PROTEKSI LS PIHAK KETIGA LAINNYA
            if (beban == '5') {
                // document.getElementById('tambah_rincian').setAttribute("disabled", "disabled");
                // document.getElementById('dengan_penagihan').checked = true;
                // $('#card_penagihan').show();
            } else {
                document.getElementById('tambah_rincian').removeAttribute("disabled");
                // document.getElementById('dengan_penagihan').checked = false;
                // $('#card_penagihan').hide();
                // $('#no_penagihan').val(null).change();
                // $('#tgl_penagihan').val(null);
                // $('#nilai_penagihan').val(null);
                // $('#total').val(null);
                // tabel.clear().draw();
            }

            // PROTEKSI LS PIHAK KETIGA LAINNYA
            if (beban != '5') {
                $('#tambah_rincian').prop("disabled", false);
            }

            // cek beban dan jenis
            if (beban == '6' || beban == '5') {
                document.getElementById('npwp').removeAttribute("disabled");
                $('#tgl_awal').prop('disabled', false);
                $('#tgl_akhir').prop('disabled', false);
                document.getElementById('rekanan').removeAttribute("disabled");
                document.getElementById('pimpinan').removeAttribute("disabled");
                document.getElementById('alamat').removeAttribute("disabled");
                $('#no_kontrak').prop('disabled', false);
                document.getElementById('bank').removeAttribute("disabled");
                document.getElementById('rekening').removeAttribute("disabled");
            } else {
                if (beban == '4' && jenis == '9') {
                    // kondisi
                    document.getElementById('npwp').removeAttribute("disabled");
                    $('#tgl_awal').prop('disabled', true);
                    $('#tgl_akhir').prop('disabled', true);
                    document.getElementById('rekanan').removeAttribute("disabled");
                    document.getElementById('pimpinan').removeAttribute("disabled");
                    document.getElementById('alamat').removeAttribute("disabled");
                    $('#no_kontrak').prop('disabled', true);
                    document.getElementById('bank').removeAttribute("disabled");
                    document.getElementById('rekening').removeAttribute("disabled");
                    // hapus data yang di disabled
                    document.getElementById('tgl_awal').value = '';
                    document.getElementById('tgl_akhir').value = '';
                    document.getElementById('no_kontrak').value = '';
                } else {
                    document.getElementById('npwp').removeAttribute("disabled");
                    $('#tgl_awal').prop('disabled', true);
                    $('#tgl_akhir').prop('disabled', true);
                    document.getElementById('rekanan').setAttribute("disabled", "disabled");
                    document.getElementById('pimpinan').setAttribute("disabled", "disabled");
                    document.getElementById('alamat').setAttribute("disabled", "disabled");
                    $('#no_kontrak').prop('disabled', true);
                    document.getElementById('bank').removeAttribute("disabled");
                    document.getElementById('rekening').removeAttribute("disabled");

                    document.getElementById('tgl_awal').value = '';
                    document.getElementById('tgl_akhir').value = '';
                    $("#rekanan").val(null).change();
                    document.getElementById('pimpinan').value = '';
                    document.getElementById('alamat').value = '';
                    document.getElementById('no_kontrak').value = '';
                }
            }
        });

        $('#jenis').on('select2:select', function() {
            let jenis = this.value;
            let beban = document.getElementById('beban').value;

            // PROTEKSI LS BARANG JASA DAN LS PIHAK KETIGA LAINNYA
            // if (beban == '6' && jenis == '6') {
            //     document.getElementById('tambah_rincian').setAttribute("disabled", "disabled");
            //     document.getElementById('dengan_penagihan').checked = true;
            //     $('#card_penagihan').show();
            // } else {
            //     document.getElementById('tambah_rincian').removeAttribute("disabled");
            // document.getElementById('dengan_penagihan').checked = false;
            // $('#card_penagihan').hide();
            // $('#no_penagihan').val(null).change();
            // $('#tgl_penagihan').val(null);
            // $('#nilai_penagihan').val(null);
            // $('#total').val(null);
            // tabel.clear().draw();
            // }

            // PROTEKSI LS BARANG JASA DAN LS PIHAK KETIGA LAINNYA
            if ((beban == '6') && (jenis == '3')) {
                $("#tgl_awal").prop('disabled', false);
                $("#tgl_akhir").prop('disabled', false);
            } else if ((beban == '5') && (jenis == '1')) {
                $("#tgl_awal").prop('disabled', false);
                $("#tgl_akhir").prop('disabled', false);
            } else if ((beban == '5') && (jenis == '2')) {
                $("#tgl_awal").prop('disabled', true);
                $("#tgl_akhir").prop('disabled', true);
            } else if ((beban == '6') && (jenis == '2')) {
                $("#tgl_awal").prop('disabled', false);
                $("#tgl_akhir").prop('disabled', false);
            } else if ((beban == '4') && (jenis == '9')) {
                $("#tgl_awal").prop('disabled', true);
                $("#tgl_akhir").prop('disabled', true);
            } else {
                $("#tgl_awal").prop('disabled', true);
                $("#tgl_akhir").prop('disabled', true);
            }
            // cek beban dan jenis
            // if (beban == '6' && jenis == '6') {
            //     document.getElementById('npwp').removeAttribute("disabled");
            //     document.getElementById('tgl_awal').removeAttribute("disabled");
            //     document.getElementById('tgl_akhir').removeAttribute("disabled");
            //     document.getElementById('rekanan').removeAttribute("disabled");
            //     document.getElementById('pimpinan').removeAttribute("disabled");
            //     document.getElementById('alamat').removeAttribute("disabled");
            //     document.getElementById('no_kontrak').removeAttribute("disabled");
            //     document.getElementById('bank').removeAttribute("disabled");
            //     document.getElementById('rekening').removeAttribute("disabled");
            // } else if (beban == '5') {
            //     document.getElementById('npwp').removeAttribute("disabled");
            //     document.getElementById('tgl_awal').setAttribute("disabled", "disabled");
            //     document.getElementById('tgl_akhir').setAttribute("disabled", "disabled");
            //     document.getElementById('rekanan').removeAttribute("disabled");
            //     document.getElementById('pimpinan').removeAttribute("disabled");
            //     document.getElementById('alamat').removeAttribute("disabled");
            //     document.getElementById('no_kontrak').removeAttribute("disabled");
            //     document.getElementById('bank').removeAttribute("disabled");
            //     document.getElementById('rekening').removeAttribute("disabled");
            //     // hapus data yang di disabled
            //     document.getElementById('tgl_awal').value = '';
            //     document.getElementById('tgl_akhir').value = '';
            // } else if (beban == '6' && jenis == '4') {
            //     document.getElementById('npwp').removeAttribute("disabled");
            //     document.getElementById('tgl_awal').setAttribute("disabled", "disabled");
            //     document.getElementById('tgl_akhir').setAttribute("disabled", "disabled");
            //     document.getElementById('rekanan').removeAttribute("disabled");
            //     document.getElementById('pimpinan').removeAttribute("disabled");
            //     document.getElementById('alamat').removeAttribute("disabled");
            //     document.getElementById('no_kontrak').removeAttribute("disabled");
            //     document.getElementById('bank').removeAttribute("disabled");
            //     document.getElementById('rekening').removeAttribute("disabled");
            //     // hapus data yang di disabled
            //     document.getElementById('tgl_awal').value = '';
            //     document.getElementById('tgl_akhir').value = '';
            // } else if (beban == '4' && jenis == '9') {
            //     document.getElementById('npwp').removeAttribute("disabled");
            //     document.getElementById('tgl_awal').setAttribute("disabled", "disabled");
            //     document.getElementById('tgl_akhir').setAttribute("disabled", "disabled");
            //     document.getElementById('rekanan').removeAttribute("disabled");
            //     document.getElementById('pimpinan').removeAttribute("disabled");
            //     document.getElementById('alamat').removeAttribute("disabled");
            //     document.getElementById('no_kontrak').setAttribute("disabled", "disabled");
            //     document.getElementById('bank').removeAttribute("disabled");
            //     document.getElementById('rekening').removeAttribute("disabled");
            //     // hapus data yang di disabled
            //     document.getElementById('tgl_awal').value = '';
            //     document.getElementById('tgl_akhir').value = '';
            //     document.getElementById('no_kontrak').value = '';
            // } else {
            //     document.getElementById('npwp').removeAttribute("disabled");
            //     document.getElementById('tgl_awal').setAttribute("disabled", "disabled");
            //     document.getElementById('tgl_akhir').setAttribute("disabled", "disabled");
            //     document.getElementById('rekanan').setAttribute("disabled", "disabled");
            //     document.getElementById('pimpinan').setAttribute("disabled", "disabled");
            //     document.getElementById('alamat').setAttribute("disabled", "disabled");
            //     document.getElementById('no_kontrak').setAttribute("disabled", "disabled");
            //     document.getElementById('bank').removeAttribute("disabled");
            //     document.getElementById('rekening').removeAttribute("disabled");
            //     // hapus data yang di disabled
            //     document.getElementById('tgl_awal').value = '';
            //     document.getElementById('tgl_akhir').value = '';
            //     $("#rekanan").val(null).change();
            //     document.getElementById('pimpinan').value = '';
            //     document.getElementById('alamat').value = '';
            //     document.getElementById('no_kontrak').value = '';
            // }
        });

        $('#nomor_spd').on('change', function() {
            let spd = this.value;
            let tgl = $(this).find(':selected').data('tgl');
            let bln = $(this).find(':selected').data('bln');
            let total = $(this).find(':selected').data('total');
            $("#tgl_spd").val(tgl);
            $("#blnawal").val(bln);
            $("#nm_sub_kegiatan").val('');
            // cari kode sub kegiatan
            $.ajax({
                url: "{{ route('sppls.cari_sub_kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    spd: spd,
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}" data-kdprogram="${data.kd_program}" data-nmprogram="${data.nm_program}" data-bidang="${data.bidang}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            })
        });

        $('#kd_sub_kegiatan').on('change', function() {
            let kd_sub_kegiatan = this.value;
            let nama = $(this).find(':selected').data('nama');
            let kdprogram = $(this).find(':selected').data('kdprogram');
            let nmprogram = $(this).find(':selected').data('nmprogram');
            let bidang = $(this).find(':selected').data('bidang');
            $("#nm_sub_kegiatan").val(nama);
            $('#nmsub_kegiatan').val(nama);
            $('#sub_kegiatan').val(kd_sub_kegiatan);
            $('#kd_program').val(kdprogram);
            $('#nm_program').val(nmprogram);
            $('#bidang').val(bidang);
            // sumber dana penyusunan
            $('#total_sumber').val('');
            $('#realisasi_sumber').val('');
            $('#sisa_sumber').val('');
            // total spd
            $('#total_spd').val('');
            $('#realisasi_spd').val('');
            $('#sisa_spd').val('');
            // total anggaran kas
            $('#total_angkas').val('');
            $('#realisasi_angkas').val('');
            $('#sisa_angkas').val('');
            // anggaran penyusunan
            $('#total_penyusunan').val('');
            $('#realisasi_penyusunan').val('');
            $('#sisa_penyusunan').val('');
            // nama rekening
            $('#nm_rekening').val('');
            // sumber dana
            $('#sumber_dana').empty();
            $('#nm_sumber').val('');
            // cari rekening
            $.ajax({
                url: "{{ route('sppls.cari_rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    $('#kode_rekening').empty();
                    $('#kode_rekening').append(`<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kode_rekening').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        });

        $('#kode_rekening').on('change', function() {
            $('#sumber_dana').empty();
            let kode_rekening = this.value;
            let kdrek6 = $(this).find(':selected').data('nama');
            let kd_sub_kegiatan = document.getElementById('sub_kegiatan').value;
            let skpd = document.getElementById('opd_unit').value;
            let status_ang = document.getElementById('status_anggaran').value;
            let status_angkas = document.getElementById('status_angkas').value;
            let tgl_spd = document.getElementById('tgl_spd').value;
            let blnawal = document.getElementById('blnawal').value;
            let tgl_spp = document.getElementById('tgl_spp').value;
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let nomor_spd = document.getElementById('nomor_spd').value;
            let nama = $(this).find(':selected').data('nama');
            $("#nm_rekening").val(nama);
            // sumber dana
            $('#total_sumber').val(null);
            $('#realisasi_sumber').val(null);
            $('#sisa_sumber').val(null);
            // cari sumber dana
            $.ajax({
                url: "{{ route('penagihan.cari_sumber_dana') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    skpd: skpd,
                    kdgiat: kd_sub_kegiatan,
                    kdrek: kode_rekening,
                    status_ang: status_ang
                },
                success: function(data) {
                    $('#sumber_dana').empty();
                    $('#sumber_dana').append(
                        `<option value="" disabled selected>Pilih Sumber Dana</option>`);
                    $.each(data, function(index, data) {
                        $('#sumber_dana').append(
                            // `<option value="${data.sumber_dana}" data-lalu="${data.lalu}" data-nilai="${data.nilai}">${data.sumber_dana}</option>`
                            `<option value="${data.kd_sumber}" data-nm_sumber="${data.nm_sumber}" data-nilai="${data.nilai}">${data.kd_sumber} | ${data.nm_sumber}</option>`
                        );
                    })
                }
            })
            // kondisi untuk kode rekening tertentu
            // if (kode_rekening.substr(0, 2) == '52') {
            //     document.getElementById('volume_output').removeAttribute("disabled");
            //     document.getElementById('satuan_output').removeAttribute("disabled");
            // } else if (kode_rekening.substr(0, 4) == '5105') {
            //     document.getElementById('volume_output').removeAttribute("disabled");
            //     document.getElementById('satuan_output').removeAttribute("disabled");
            // } else if (kode_rekening.substr(0, 4) == '5106') {
            //     document.getElementById('volume_output').removeAttribute("disabled");
            //     document.getElementById('satuan_output').removeAttribute("disabled");
            // } else if (kode_rekening.substr(0, 4) == '5402') {
            //     document.getElementById('volume_output').removeAttribute("disabled");
            //     document.getElementById('satuan_output').removeAttribute("disabled");
            // } else if (kode_rekening.substr(0, 6) == '510203') {
            //     document.getElementById('volume_output').removeAttribute("disabled");
            //     document.getElementById('satuan_output').removeAttribute("disabled");
            // } else if (kode_rekening == '510101080001' && kode_rekening == '5110105') {
            //     // document.getElementById('minus').removeAttribute("disabled");
            // } else {
            //     // document.getElementById('minus').setAttribute("disabled", "disabled");
            //     // document.getElementById('minus').checked = false;
            // }
            // anggaran penyusunan
            $.ajax({
                url: "{{ route('sppls.jumlah_anggaran_penyusunan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    skpd: skpd,
                    kdgiat: kd_sub_kegiatan,
                    kdrek: kode_rekening,
                    no_spp: no_spp
                },
                success: function(data) {
                    let rektotal = parseFloat(data.rektotal) || 0;
                    let rektotal_lalu = parseFloat(data.rektotal_lalu) || 0;
                    let sisa_dana = parseFloat(rektotal - rektotal_lalu) || 0;
                    $("#total_penyusunan").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(rektotal));
                    $("#realisasi_penyusunan").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(rektotal_lalu));
                    $("#sisa_penyusunan").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(sisa_dana));
                }
            })
            // total spd
            $.ajax({
                url: "{{ route('sppls.total_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    skpd: skpd,
                    kdgiat: kd_sub_kegiatan,
                    kdrek: kode_rekening,
                    no_spp: no_spp,
                    tgl_spd: tgl_spd,
                    tgl_spp: tgl_spp,
                    beban: beban,
                    blnawal: blnawal
                },
                success: function(data) {
                    let total_spd = parseFloat(data.total_spd) || 0;
                    $("#total_spd").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_spd));
                    total_angkas();
                }
            })
            // total angkas
            // $.ajax({
            //     url: "{{ route('sppls.total_angkas') }}",
            //     type: "POST",
            //     dataType: 'json',
            //     data: {
            //         skpd: skpd,
            //         kdgiat: kd_sub_kegiatan,
            //         kdrek: kode_rekening,
            //         no_spp: no_spp,
            //         nomor_spd: nomor_spd,
            //         tgl_spd: tgl_spd,
            //         tgl_spp: tgl_spp,
            //         beban: beban,
            //         status_ang: status_ang,
            //         status_angkas: status_angkas,
            //     },
            //     success: function(data) {
            //         let total_angkas = parseFloat(data.nilai) || 0;
            //         $("#total_angkas").val(new Intl.NumberFormat('id-ID', {
            //             minimumFractionDigits: 2
            //         }).format(total_angkas));
            //     }
            // })
            // realisasi spd
            // $.ajax({
            //     url: "{{ route('sppls.realisasi_spd') }}",
            //     type: "POST",
            //     dataType: 'json',
            //     data: {
            //         skpd: skpd,
            //         kdgiat: kd_sub_kegiatan,
            //         kdrek: kode_rekening,
            //     },
            //     success: function(data) {
            //         let realisasi_spd = parseFloat(data.total) || 0;
            //         let total_spd = document.getElementById('total_spd').value;
            //         let total = total_spd.split(".").join("");
            //         let total1 = total.split(",").join(".");
            //         let total2 = parseFloat(total1) || 0;
            //         let sisa_spd = total2 - realisasi_spd;
            //         $("#realisasi_spd").val(new Intl.NumberFormat('id-ID', {
            //             minimumFractionDigits: 2
            //         }).format(realisasi_spd));
            //         $("#realisasi_angkas").val(new Intl.NumberFormat('id-ID', {
            //             minimumFractionDigits: 2
            //         }).format(realisasi_spd));
            //         $("#sisa_spd").val(new Intl.NumberFormat('id-ID', {
            //             minimumFractionDigits: 2
            //         }).format(sisa_spd));
            //         $("#sisa_angkas").val(new Intl.NumberFormat('id-ID', {
            //             minimumFractionDigits: 2
            //         }).format(sisa_spd));
            //     }
            // })
        });

        $('#sumber_dana').on('change', function() {
            let selected = $(this).find('option:selected');
            let sumber_dana = this.value;
            if (sumber_dana == 'null') {
                alert('Sumber dana tidak dapat digunakan!');
                $('#sumber_dana').val(null).change();
                return;
            }
            let nama = $(this).find(':selected').data('nm_sumber');
            let nilai = $(this).find(':selected').data('nilai');
            $('#nm_sumber').val(nama);
            $("#total_sumber").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
            $.ajax({
                url: "{{ route('penagihan.realisasi_sumber_dana') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    sumber: sumber_dana,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    kd_rek6: document.getElementById('kode_rekening').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    $("#realisasi_sumber").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data));
                    $("#sisa_sumber").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(nilai - data));
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
            // $.ajax({
            //     url: "{{ route('penagihan.cari_nama_sumber') }}",
            //     type: "POST",
            //     dataType: 'json',
            //     data: {
            //         sumber_dana: sumber_dana,
            //     },
            //     success: function(data) {
            //         $('#nm_sumber').val(data.nm_sumber_dana1);
            //     }
            // })
            // let dana = parseInt(selected.data('nilai')) || 0;
            // let dana_lalu = parseInt(selected.data('lalu')) || 0;
            // let sisa_dana = parseInt(dana - dana_lalu) || 0;
            // $("#total_sumber").val(new Intl.NumberFormat('id-ID', {
            //     minimumFractionDigits: 2
            // }).format(dana));
            // $("#realisasi_sumber").val(new Intl.NumberFormat('id-ID', {
            //     minimumFractionDigits: 2
            // }).format(dana_lalu));
            // $("#sisa_sumber").val(new Intl.NumberFormat('id-ID', {
            //     minimumFractionDigits: 2
            // }).format(sisa_dana));
        });

        $('#no_penagihan').on('select2:select', function() {
            let no_kontrak = this.value;
            let tgl = $(this).find(':selected').data('tgl');
            let kontrak = $(this).find(':selected').data('kontrak');
            let ket = $(this).find(':selected').data('ket');
            let total = parseFloat($(this).find(':selected').data('total')) || 0;
            $("#tgl_penagihan").val(tgl);
            $("#no_kontrak").val(kontrak);
            $("#keperluan").val(ket);
            $("#nilai_penagihan").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total));
            // tabel.clear().draw();
            $("#total").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total));
            $.ajax({
                url: "{{ route('sppls.cari_penagihan_sppls') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_bukti: no_kontrak
                },
                success: function(data) {
                    $.each(data, function(index, data) {
                        tabel.row.add({
                            'kd_sub_kegiatan': data.kd_sub_kegiatan,
                            'kd_rek6': data.kd_rek,
                            'nm_rek6': data.nm_rek6,
                            'nilai': new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2
                            }).format(data.nilai),
                            'sumber': data.sumber,
                            'nm_sumber': data.nm_sumber_dana1,
                            // 'volume_output': null,
                            // 'satuan_output': null,
                            'hapus': `<a href="javascript:void(0);" onclick="deleteData('${data.kd_sub_kegiatan}','${data.kd_rek}','${data.nm_rek6}','${data.sumber}','${data.nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
                        }).draw();
                    })
                }
            })

        });

        let data = false;
        $('#dengan_penagihan').on('change', function() {
            if ($(this).is(':checked')) {
                data = $(this).is(':checked');
                if (data == true) {
                    document.getElementById('tambah_rincian').setAttribute("disabled", "disabled");
                    document.getElementById('tgl_penagihan').value = '';
                    document.getElementById('nilai_penagihan').value = '';
                    document.getElementById('no_kontrak').value = '';
                    // $('#no_penagihan').val(null).change();
                    // tabel.clear().draw();
                    // $('#total').val('');
                    $('#card_penagihan').show();
                }
            } else {
                data = $(this).is(':checked');
                if (data == false) {
                    document.getElementById('tambah_rincian').removeAttribute("disabled");
                    document.getElementById('tgl_penagihan').value = '';
                    document.getElementById('nilai_penagihan').value = '';
                    document.getElementById('no_kontrak').value = '';
                    $('#no_penagihan').val(null).change();
                    // $('#total').val('');
                    // tabel.clear().draw();
                    $('#card_penagihan').hide();
                }
            }
        });

        // tambah_rincian
        $('#tambah_rincian').on("click", function() {
            // cek sub kegiatan sudah dipilih atau belum
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            if (!kd_sub_kegiatan) {
                alert('Isi kode kegiatan terlebih dahulu!');
                return;
            }
            // cari status angkas
            let tgl_spp = document.getElementById('tgl_spp').value;
            // cek status anggaran
            if (tgl_spp) {
                $.ajax({
                    url: "{{ route('penagihan.cek_status_ang_new') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        tgl_bukti: tgl_spp,
                    },
                    success: function(data) {
                        $('#status_anggaran').val(data.nama);
                    }
                })
                // cek status angkas
                $.ajax({
                    url: "{{ route('penagihan.cek_status_ang') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        tgl_bukti: tgl_spp,
                    },
                    success: function(data) {
                        $('#status_angkas').val(data.status);
                    }
                })
            }
            // cek tanggal spp dengan tanggal spp lalu
            if (!tgl_spp) {
                alert('Tanggal SPP harus dipilih!');
                return;
            }
            let tgl_spp_lalu = document.getElementById('tgl_spp_lalu').value;
            // if (tgl_spp < tgl_spp_lalu) {
            //     alert("tanggal SPP tidak boleh kurang dari SPP Lalu...!!!");
            //     return;
            // }

            let no_penagihan = document.getElementById('no_penagihan').value;
            let beban = document.getElementById('beban').value;
            let jenis = document.getElementById('jenis').value;
            if (beban == '6' && jenis == '6' && no_penagihan == '') {
                alert('Nomor Penagihan Masih Kosong....!!!');
                return;
            }

            $('#tambah_rincianspp').modal('show');
        });

        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });

        $('#simpan_detail_spp').on('click', function() {
            let rincian_spp = tabel.rows().data().toArray();
            let opd_unit = document.getElementById('opd_unit').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kode_rekening = document.getElementById('kode_rekening').value;
            let sumber_dana = document.getElementById('sumber_dana').value;
            let nm_sumber = document.getElementById('nm_sumber').value;
            let nilai_rincian = nilai(document.getElementById('nilai_rincian').value);
            let sisa_spd = rupiah(document.getElementById('sisa_spd').value);
            let sisa_angkas = rupiah(document.getElementById('sisa_angkas').value);
            let sisa_penyusunan = rupiah(document.getElementById('sisa_penyusunan').value);
            let sisa_sumber = rupiah(document.getElementById('sisa_sumber').value);
            let status_anggaran = document.getElementById('status_anggaran').value;
            let beban = document.getElementById('beban').value;
            let total = rupiah(document.getElementById('total').value);
            let akumulasi = nilai_rincian + total;
            let nm_rekening = document.getElementById('nm_rekening').value;

            // let minus = document.getElementById('minus').checked;

            if (!sumber_dana) {
                alert('Pilih sumber dana terlebih dahulu!');
                return;
            }

            // if (minus == true) {
            //     nilai_rincian = (-1 * nilai_rincian);
            // }

            if (!status_anggaran && beban != '4') {
                alert('Anggaran belum disahkan!');
                return;
            }

            if ((nilai_rincian > sisa_spd) && beban != '4') {
                alert('Nilai Melebihi Sisa SPD...!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai_rincian > sisa_angkas) {
                alert('Nilai Melebihi Sisa Anggaran Kas...!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai_rincian == 0) {
                alert('Nilai Nol.....!!!, Cek Lagi...!!!');
                return;
            }

            if ((nilai_rincian > sisa_penyusunan) && beban != '4') {
                alert('Nilai Melebihi Sisa Anggaran...!!!, Cek Lagi...!!!');
                return;
            }

            if ((nilai_rincian > sisa_sumber) && beban != '4') {
                alert('Nilai Melebihi Sisa Sumber Dana...!!!, Cek Lagi...!!!');
                return;
            }

            if ((nilai_rincian > sisa_spd) && beban == '4') {
                alert('Nilai Melebihi Sisa SPD...!!!, Cek Lagi...!!!');
                return;
            }

            if ((nilai_rincian > sisa_penyusunan) && beban == '4') {
                alert('Nilai Melebihi Sisa Anggaran...!!!, Cek Lagi...!!!');
                return;
            }

            if ((nilai_rincian > sisa_sumber) && beban == '4') {
                alert('Nilai Melebihi Sisa Sumber Dana...!!!, Cek Lagi...!!!');
                return;
            }

            //if (Number.isInteger(nilai_rincian) == false) {
            //    alert('Nilai Rincian tidak boleh ada koma');
            //    return;
            //};
            // cek data di detail spp dan inputan
            let tampungan = tabel.rows().data().toArray().map((value) => {
                let result = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    sumber: value.sumber,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.kd_sub_kegiatan.trim() == kd_sub_kegiatan.trim() && data.kd_rek6
                    .trim() == kode_rekening.trim() && data.sumber.trim() == sumber_dana.trim()
                ) {
                    return '2';
                } else if (data.kd_sub_kegiatan.trim() != kd_sub_kegiatan.trim()) {
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
            tabel.row.add({
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'kd_rek6': kode_rekening,
                'nm_rek6': nm_rekening,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai_rincian),
                'sumber': sumber_dana,
                'nm_sumber': nm_sumber,
                'hapus': `<a href="javascript:void(0);" onclick="deleteData('${kd_sub_kegiatan}','${kode_rekening}','${nm_rekening}','${sumber_dana}','${nilai_rincian}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            kosong_input_detail();
            $("#total").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(akumulasi));
        });

        $('#cari_nospp').on('click', function() {
            let beban = document.getElementById('beban').value;
            let tahun_anggaran = "{{ tahun_anggaran() }}";
            let kd_skpd = document.getElementById('kd_skpd').value;
            let bulan = document.getElementById('bulan').value;
            let jenis_beban;
            let jenis_beban2;
            if (beban == '4') {
                jenis_beban = "GJ";
                let tampungan = tabel.rows().data().toArray().map((value) => {
                    let result = {
                        kd_rek6: value.kd_rek6,
                    };
                    return result;
                });
                let kondisi = tampungan.map(function(data) {
                    let kdrek6 = data.kd_rek6;
                    if (kdrek6.substr(0, 5) == '51101') {
                        jenis_beban = "GJ";
                    }
                });
                jenis_beban2 = "GJ";
            } else {
                jenis_beban = "LS";
                jenis_beban2 = "BL";
            }
            // no spp
            $.ajax({
                url: "{{ route('sppls.cari_nospp') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jenis_beban2: jenis_beban2,
                },
                success: function(data) {
                    let no_spp = "61.03/02.0/" + data.nilai + "/" + jenis_beban + "/" +
                        kd_skpd + "/M/" + bulan + "/" +
                        tahun_anggaran;
                    $('#no_urut').val(data.nilai);
                    $('#no_spp').val(no_spp);
                }
            })
        });

        $('#simpan_penagihan').on('click', function() {
            let beban = document.getElementById('beban').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tahun_anggaran = "{{ tahun_anggaran() }}";
            let no_urut = document.getElementById('no_urut').value;
            let no_spp = document.getElementById('no_spp').value;
            let no_tersimpan = document.getElementById('no_tersimpan').value;
            let tgl_spp = document.getElementById('tgl_spp').value;
            let tgl_spp_lalu = document.getElementById('tgl_spp_lalu').value;
            let bulan = document.getElementById('bulan').value;
            let keperluan = document.getElementById('keperluan').value;
            let rekanan = document.getElementById('rekanan').value;
            let pimpinan = document.getElementById('pimpinan').value;
            let bank = document.getElementById('bank').value;
            let nm_penerima = document.getElementById('nm_penerima').value;
            let npwp = document.getElementById('npwp').value;
            let rekening = document.getElementById('rekening').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let total = rupiah(document.getElementById('total').value);
            let nm_sub_kegiatan = document.getElementById('nm_sub_kegiatan').value;
            let alamat = document.getElementById('alamat').value;
            let no_kontrak = document.getElementById('no_kontrak').value;
            //let lanjut = document.getElementById('lanjut').value;
            let tgl_awal = document.getElementById('tgl_awal').value;
            let tgl_akhir = document.getElementById('tgl_akhir').value;
            let jenis = document.getElementById('jenis').value;
            let nomor_spd = document.getElementById('nomor_spd').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let no_penagihan = document.getElementById('no_penagihan').value;
            let tgl_penagihan = document.getElementById('tgl_penagihan').value;
            let opd_unit = document.getElementById('opd_unit').value;
            let dengan_penagihan = document.getElementById('dengan_penagihan').checked;
            let tanggal = new Date(tgl_spp);
            let tahun = tanggal.getFullYear();
            let kd_program = document.getElementById('kd_program').value;
            let nm_program = document.getElementById('nm_program').value;
            let bidang = document.getElementById('bidang').value;

            let statusAnggaran = document.getElementById('statusAnggaran').value;
            let bulanInputan = document.getElementById('bulanInputan').value;

            let rincian_rekening = tabel.rows().data().toArray().map((value) => {
                let data = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: rupiah(value.nilai),
                    sumber: value.sumber,
                    // volume_output: value.volume_output,
                    // satuan_output: value.satuan_output,
                };
                return data;
            });

            let tampungan = tabel.rows().data().toArray().map((value) => {
                let result = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan
                };
                return result;
            });

            let kondisi = tampungan.map(function(data) {
                if (data.kd_sub_kegiatan != kd_sub_kegiatan) {
                    return '2';
                }
            });

            if (kondisi.includes("2")) {
                alert('Kode Sub Kegiatan Tidak Sama Dengan Rincian SPP');
                return;
            }

            let sts_tagih = dengan_penagihan == false ? 0 : 1;

            if (rincian_rekening.length == 0) {
                alert('Rincian Rekening tidak boleh kosong!');
                return;
            }

            if (!no_spp) {
                alert("Isi Nomor SPP Terlebih Dahulu...!!!");
                return;
            }

            if (no_spp == 0) {
                alert('Nomor SPP tidak boleh NULL!');
                return;
            }

            if (!statusAnggaran) return alert("Pilih status anggaran Terlebih Dahulu...!!!");
            if (!bulanInputan) return alert("Pilih bulan inputan Terlebih Dahulu...!!!");

            if (!nomor_spd) {
                alert("Isi Nomor SPD Terlebih Dahulu...!!!");
                return;
            }

            if (!nomor_spd) {
                alert("Isi Nomor SPD Terlebih Dahulu...!!!");
                return;
            }
            if (!jenis) {
                alert("Isi Beban Terlebih Dahulu...!!!");
                return;
            }
            if ((beban == '5' && jenis == '1') || (beban == '5' && jenis == '3') || (beban == '5' &&
                    jenis == '5')) {} else {
                if (!npwp) {
                    alert("Isi NPWP Terlebih Dahulu...!!!");
                    return;
                }
            }
            if (!rekening) {
                alert("Isi Rekening Terlebih Dahulu...!!!");
                return;
            }
            if (!keperluan) {
                alert("Isi Keperluan Terlebih Dahulu...!!!");
                return;
            }
            if (!bank) {
                alert("Isi Bank Terlebih Dahulu...!!!");
                return;
            }
            if (!tgl_spp) {
                alert("Isi Tanggal Terlebih Dahulu...!!!");
                return;
            }
            // if (tgl_spp < tgl_spp_lalu) {
            //     alert("tanggal SPP tidak boleh kurang dari SPP Lalu...!!!");
            //     return;
            // }
            if (!kd_skpd) {
                alert("Isi SKPD Terlebih Dahulu...!!!");
                return;
            }

            if (tahun != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!beban) {
                alert("Isi Beban Terlebih Dahulu...!!!");
                return;
            }
            if (!bulan) {
                alert("Isi Kebutuhan Bulan Terlebih Dahulu...!!!");
                return;
            }
            if (!kd_sub_kegiatan) {
                alert("Isi Kode Kegiatan Terlebih Dahulu...!!!");
                return;
            }
            if (kd_sub_kegiatan.length != '17') {
                alert("Kode Kegiatan Salah!");
                return;
            }
            if (!nm_sub_kegiatan) {
                alert("Pilih Kegiatan Terlebih Dahulu...!!!");
                return;
            }
            if (beban == '6' && jenis == '3' && no_kontrak == '') {
                alert("Nomor Kontrak Harus Diisi...!!!");
                return;
            }
            if (!nm_penerima) {
                alert("Penerima Harus Diisi...!!!");
                return;
            }
            if (beban == '6' && jenis == '3' && !rekanan) {
                alert("Rekanan Harus Diisi...!!!");
                return;
            }
            if (beban == '6' && jenis == '3' && !pimpinan) {
                alert("Direktur/Nama Rekanan Harus Diisi...!!!");
                return;
            }
            if (beban == '6' && jenis == '3' && !no_penagihan) {
                alert("Nomor Penagihan Tidak Boleh Kosong...!!!");
                return;
            }

            if (beban == '6' && jenis == '3' && !tgl_awal) {
                alert("Tanggal Awal Tidak Boleh Kosong...!!!");
                return;
            }

            if (beban == '6' && jenis == '3' && !tgl_akhir) {
                alert("Tanggal Akhir Tidak Boleh Kosong...!!!");
                return;
            }

            if (keperluan.length > 1000) {
                alert('Keterangan Tidak boleh lebih dari 1000 karakter');
                return;
            }
            if (total == 0) {
                alert('Rincian Rekening Kosong');
                return;
            }
            let data = {
                no_spp,
                no_tersimpan,
                tgl_spp,
                tgl_spp_lalu,
                bulan,
                keperluan,
                rekanan,
                pimpinan,
                bank,
                nm_penerima,
                npwp,
                rekening,
                nm_skpd,
                total,
                nm_sub_kegiatan,
                alamat,
                no_kontrak,
                //lanjut,
                tgl_awal,
                tgl_akhir,
                jenis,
                nomor_spd,
                kd_sub_kegiatan,
                no_penagihan,
                tgl_penagihan,
                opd_unit,
                dengan_penagihan,
                tanggal,
                tahun,
                beban,
                kd_skpd,
                tahun_anggaran,
                sts_tagih,
                no_urut,
                kd_program,
                nm_program,
                bidang,
                rincian_rekening,
                statusAnggaran,
                bulanInputan
            };

            // proses simpan
            $('#simpan_penagihan').prop('disabled', true);
            $.ajax({
                url: "{{ route('sppls.simpan_sppls') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data Berhasil Tersimpan...!!!');
                        window.location.href = "{{ route('sppls.index') }}";
                    } else if (response.message == '2') {
                        alert('Nomor telah dipakai...!!!');
                        $('#simpan_penagihan').prop('disabled', false);
                    } else {
                        alert('Data Gagal Tersimpan...!!!');
                        $('#simpan_penagihan').prop('disabled', false);
                    }
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        });

        // function simpan_spp(data) {
        //     $.ajax({
        //         url: "{{ route('sppls.simpan_sppls') }}",
        //         type: "POST",
        //         dataType: 'json',
        //         data: {
        //             data: data,
        //         },
        //         success: function(response) {
        //             if (response.message == '0') {
        //                 alert('Gagal Simpan..!!');
        //                 $('#simpan_penagihan').prop('disabled', false);
        //             }
        //             if (response.message == '2') {
        //                 alert('Data Sudah Ada..!!');
        //                 $('#simpan_penagihan').prop('disabled', false);
        //             } else {
        //                 simpan_detail_spp(data);
        //             }
        //         }
        //     })
        // }

        // function simpan_detail_spp(data) {
        //     $.ajax({
        //         url: "{{ route('sppls.simpan_detail_sppls') }}",
        //         type: "POST",
        //         dataType: 'json',
        //         data: {
        //             data: data,
        //         },
        //         success: function(response) {
        //             if (response.message == '1') {
        //                 alert('Data Berhasil Tersimpan...!!!');
        //                 window.location.href = "{{ route('sppls.index') }}";
        //             } else {
        //                 alert('Detail Gagal Tersimpan...!!!');
        //                 $('#simpan_penagihan').prop('disabled', false);
        //             }
        //         }
        //     })
        // }
        // kosongin data ketika ganti beban
        function kosong() {
            $('#nm_penerima').val(null).change();
            $('#bank').val(null);
            $('#nmbank').val(null);
            $('#rekening').val(null);
            $('#npwp').val(null);
            $('#rekanan').val(null);
            $('#pimpinan').val(null);
            $('#alamat').val(null);

            document.getElementById('tgl_spd').value = '';
            $('#kd_sub_kegiatan').empty();
            document.getElementById('nm_sub_kegiatan').value = '';
            document.getElementById('sub_kegiatan').value = '';
            document.getElementById('nmsub_kegiatan').value = '';
            $('#kode_rekening').empty();
            document.getElementById('nm_rekening').value = '';
            $('#sumber_dana').empty();
            document.getElementById('nm_sumber').value = '';
            // total spd
            document.getElementById('total_spd').value = '';
            document.getElementById('realisasi_spd').value = '';
            document.getElementById('sisa_spd').value = '';
            // total anggaran kas
            document.getElementById('total_angkas').value = '';
            document.getElementById('realisasi_angkas').value = '';
            document.getElementById('sisa_angkas').value = '';
            // anggaran penyusunan
            document.getElementById('total_penyusunan').value = '';
            document.getElementById('realisasi_penyusunan').value = '';
            document.getElementById('sisa_penyusunan').value = '';
            // sumber dana penyusunan
            document.getElementById('total_sumber').value = '';
            document.getElementById('realisasi_sumber').value = '';
            document.getElementById('sisa_sumber').value = '';
            // volume output, satuan output, nilai
            // document.getElementById('volume_output').value = '';
            // document.getElementById('satuan_output').value = '';
            // document.getElementById('nilai_rincian').value = '';
        }

        function kosong_input_detail() {
            $('#kode_rekening').val(null).change();
            $('#nm_rekening').val('');
            $('#sumber_dana').empty();
            $('#nm_sumber').val('');

            $('#total_spd').val(null);
            $('#realisasi_spd').val(null);
            $('#sisa_spd').val(null);
            // total anggaran kas
            $('#total_angkas').val(null);
            $('#realisasi_angkas').val(null);
            $('#sisa_angkas').val(null);
            // anggaran penyusunan
            $('#total_penyusunan').val(null);
            $('#realisasi_penyusunan').val(null);
            $('#sisa_penyusunan').val(null);
            // sumber dana penyusunan
            $('#total_sumber').val(null);
            $('#realisasi_sumber').val(null);
            $('#sisa_sumber').val(null);
            // volume output, satuan output, nilai
            // $('#volume_output').val(null);
            // $('#satuan_output').val(null);
            // $('#nilai_rincian').val(null);
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

        function nilai(n) {
            let nilai = n.split(',').join('');
            return parseFloat(nilai) || 0;
        }

        function rupiah(n) {
            let n1 = n.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }

        function total_angkas() {
            $.ajax({
                url: "{{ route('sppls.total_angkas') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    skpd: document.getElementById('opd_unit').value,
                    kdgiat: document.getElementById('kd_sub_kegiatan').value,
                    kdrek: document.getElementById('kode_rekening').value,
                    no_spp: document.getElementById('no_spp').value,
                    nomor_spd: document.getElementById('nomor_spd').value,
                    tgl_spd: document.getElementById('tgl_spd').value,
                    tgl_spp: document.getElementById('tgl_spp').value,
                    beban: document.getElementById('beban').value,
                    status_ang: document.getElementById('status_anggaran').value,
                    status_angkas: document.getElementById('status_angkas').value,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    let total_angkas = parseFloat(data.nilai) || 0;
                    $("#total_angkas").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_angkas));
                    total_realisasi();
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        }

        function total_realisasi() {
            $.ajax({
                url: "{{ route('sppls.realisasi_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    skpd: document.getElementById('opd_unit').value,
                    kdgiat: document.getElementById('kd_sub_kegiatan').value,
                    kdrek: document.getElementById('kode_rekening').value,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    let realisasi_spd = parseFloat(data.total) || 0;
                    let total_spd = rupiah(document.getElementById('total_spd').value);
                    let total_angkas = rupiah(document.getElementById('total_angkas').value);
                    $("#realisasi_spd").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(realisasi_spd));
                    $("#realisasi_angkas").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(realisasi_spd));
                    $("#sisa_spd").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_spd - realisasi_spd));
                    $("#sisa_angkas").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_angkas - realisasi_spd));
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        }
    });

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

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
