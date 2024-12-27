<script>
    $('[data-bs-toggle="tooltip"]').tooltip();
</script>
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let tabel_spm = $('#spm').DataTable({
            responsive: true,
            ordering: false,
            processing: true,
            lengthMenu: [5, 10, 20, 50],
            ajax: {
                "url": "{{ route('verifikasi_spm.cari_spm') }}",
                "type": "POST",
                "data": function(d) {
                    d.beban = document.getElementById('beban').value;
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                }
            },
            createdRow: function(row, data, index) {
                if (data.status == 1) {
                    $(row).css("background-color", "#4bbe68");
                    $(row).css("color", "white");
                } else if (data.sp2d_batal == '1') {
                    $(row).css("background-color", "#ff0000");
                    $(row).css("color", "white");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center"
                }, {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                    className: "text-left",
                },
                {
                    data: 'no_spm',
                    name: 'no_spm',
                    // className: "text-center",
                },
                {
                    data: null,
                    name: 'nilai',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
                    }
                },
                {
                    data: null,
                    name: 'status',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.status == '1') {
                            return 'Disetujui';
                        } else if (data.status == '2') {
                            return 'Ditolak';
                        } else {
                            return 'Belum / Daftar Antrian';
                        }
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 70,
                    className: "text-center",
                },
                {
                    data: 'cek',
                    name: 'cek',
                    className: "text-center",
                    width: 20,
                    render: function(data, type, row, meta) {
                        // Buat checkbox dengan event listener
                        let checked = data == 1 ? 'checked' : '';
                        return `<input type="checkbox" ${checked} onclick="updateCekStatus(this, '${row.no_spm}')">`;
                    }
                },
            ],
            drawCallback: function(settings) {
                console.log('drawCallback');
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#bendahara').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#pptk').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#pa_kpa').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#ppkd').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#jenis_ls').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#bendahara').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_bendahara').val(nama);
        });

        $('#pptk').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_pptk').val(nama);
        });

        $('#pa_kpa').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_pa_kpa').val(nama);
        });

        $('#ppkd').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_ppkd').val(nama);
        });

        $('#jenis_ls').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_jenis').val(nama);
        });

        $('#beban').on('change', function() {
            beban = this.value;
            tabel_spm.ajax.reload();
            // cari data spm
            $.ajax({
                url: "{{ route('verifikasi_spm.cari_skpd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    beban: beban,
                },
                success: function(data) {
                    $('#kd_skpd').empty();
                    $('#kd_skpd').append(
                        `<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_skpd').append(
                            `<option value="${data.kd_skpd}" data-nm_skpd="${data.nm_skpd}">${data.kd_skpd} | ${data.nm_skpd}</option>`
                        );
                    })
                }
            })
        });

        $('#kd_skpd').on('select2:select', function() {
            let kd_skpd = this.value;
            let beban = document.getElementById('beban').value;
            $.ajax({
                url: "{{ route('verifikasi_spm.cari_spm') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    beban: beban,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    tabel_spm.ajax.reload();

                }
            })
        });

        // cetak kelengkapan
        $('.kelengkapan').on('click', function() {
            let no_spm = document.getElementById('no_spm').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let baris_spm = document.getElementById('baris_spm').value;
            let jenis_ls = document.getElementById('jenis_ls').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Pilih Bendahara Pengeluarran Terlebih Dahulu!');
                return;
            }
            if (!pptk) {
                alert("Pilih PPTK Terlebih Dahulu!");
                return;
            }
            if (!pa_kpa) {
                alert("Pilih Pengguna Anggaran Terlebih Dahulu!");
                return;
            }
            if (!ppkd) {
                alert("Pilih PPKD Terlebih Dahulu!");
                return;
            }
            if (!jenis_ls) {
                jenis_ls = '';
            }
            let url = new URL("{{ route('spm.cetak_kelengkapan') }}");
            let searchParams = url.searchParams;
            searchParams.append("jenis_ls", jenis_ls);
            searchParams.append("no_spm", no_spm);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("baris_spm", baris_spm);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak berkas spm
        $('.berkas_spm').on('click', function() {
            let no_spm = document.getElementById('no_spm').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let baris_spm = document.getElementById('baris_spm').value;
            //let jenis_ls = document.getElementById('jenis_ls').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Pilih Bendahara Pengeluarran Terlebih Dahulu!');
                return;
            }
            if (!pptk) {
                alert("Pilih PPTK Terlebih Dahulu!");
                return;
            }
            if (!pa_kpa) {
                alert("Pilih Pengguna Anggaran Terlebih Dahulu!");
                return;
            }
            if (!ppkd) {
                alert("Pilih PPKD Terlebih Dahulu!");
                return;
            }
            //if (!jenis_ls) {
            //    jenis_ls = '';
            //}
            let url = new URL("{{ route('spm.berkas_spm') }}");
            let searchParams = url.searchParams;
            //searchParams.append("jenis_ls", jenis_ls);
            searchParams.append("no_spm", no_spm);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("baris_spm", baris_spm);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak ringkasan
        $('.ringkasan').on('click', function() {
            let no_spm = document.getElementById('no_spm').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let baris_spm = document.getElementById('baris_spm').value;
            //let jenis_ls = document.getElementById('jenis_ls').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Pilih Bendahara Pengeluarran Terlebih Dahulu!');
                return;
            }
            if (!pptk) {
                alert("Pilih PPTK Terlebih Dahulu!");
                return;
            }
            if (!pa_kpa) {
                alert("Pilih Pengguna Anggaran Terlebih Dahulu!");
                return;
            }
            if (!ppkd) {
                alert("Pilih PPKD Terlebih Dahulu!");
                return;
            }
            //if (!jenis_ls) {
            //    jenis_ls = '';
            //}
            let url;
            if (beban == '1') {
                url = new URL("{{ route('spm.ringkasan_up') }}");
            } else if (beban == '2' || beban == '3' || beban == '4' || beban == '5' || beban == '6') {
                url = new URL("{{ route('spm.ringkasan_gu') }}");
            }
            let searchParams = url.searchParams;
            //searchParams.append("jenis_ls", jenis_ls);
            searchParams.append("no_spm", no_spm);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("baris_spm", baris_spm);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak pengantar
        $('.pengantar').on('click', function() {
            let no_spm = document.getElementById('no_spm').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let baris_spm = document.getElementById('baris_spm').value;
            //let jenis_ls = document.getElementById('jenis_ls').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Pilih Bendahara Pengeluarran Terlebih Dahulu!');
                return;
            }
            if (!pptk) {
                alert("Pilih PPTK Terlebih Dahulu!");
                return;
            }
            if (!pa_kpa) {
                alert("Pilih Pengguna Anggaran Terlebih Dahulu!");
                return;
            }
            if (!ppkd) {
                alert("Pilih PPKD Terlebih Dahulu!");
                return;
            }
            //if (!jenis_ls) {
            //    jenis_ls = '';
            //}
            let url = new URL("{{ route('spm.pengantar') }}");
            let searchParams = url.searchParams;
            //searchParams.append("jenis_ls", jenis_ls);
            searchParams.append("no_spm", no_spm);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("baris_spm", baris_spm);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak lampiran
        $('.lampiran').on('click', function() {
            let no_spm = document.getElementById('no_spm').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let baris_spm = document.getElementById('baris_spm').value;
            //let jenis_ls = document.getElementById('jenis_ls').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Pilih Bendahara Pengeluarran Terlebih Dahulu!');
                return;
            }
            if (!pptk) {
                alert("Pilih PPTK Terlebih Dahulu!");
                return;
            }
            if (!pa_kpa) {
                alert("Pilih Pengguna Anggaran Terlebih Dahulu!");
                return;
            }
            if (!ppkd) {
                alert("Pilih PPKD Terlebih Dahulu!");
                return;
            }
            //if (!jenis_ls) {
            //    jenis_ls = '';
            //}
            let url = new URL("{{ route('spm.lampiran') }}");
            let searchParams = url.searchParams;
            //searchParams.append("jenis_ls", jenis_ls);
            searchParams.append("no_spm", no_spm);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("baris_spm", baris_spm);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak tanggung jawab
        $('.tanggung_jawab').on('click', function() {
            let no_spm = document.getElementById('no_spm').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let baris_spm = document.getElementById('baris_spm').value;
            //let jenis_ls = document.getElementById('jenis_ls').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Pilih Bendahara Pengeluarran Terlebih Dahulu!');
                return;
            }
            if (!pptk) {
                alert("Pilih PPTK Terlebih Dahulu!");
                return;
            }
            if (!pa_kpa) {
                alert("Pilih Pengguna Anggaran Terlebih Dahulu!");
                return;
            }
            if (!ppkd) {
                alert("Pilih PPKD Terlebih Dahulu!");
                return;
            }
            //if (!jenis_ls) {
            //    jenis_ls = '';
            //}
            let url = new URL("{{ route('spm.tanggung') }}");
            let searchParams = url.searchParams;
            //searchParams.append("jenis_ls", jenis_ls);
            searchParams.append("no_spm", no_spm);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("baris_spm", baris_spm);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak pernyataan
        $('.pernyataan').on('click', function() {
            let no_spm = document.getElementById('no_spm').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let baris_spm = document.getElementById('baris_spm').value;
            //let jenis_ls = document.getElementById('jenis_ls').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Pilih Bendahara Pengeluarran Terlebih Dahulu!');
                return;
            }
            if (!pptk) {
                alert("Pilih PPTK Terlebih Dahulu!");
                return;
            }
            if (!pa_kpa) {
                alert("Pilih Pengguna Anggaran Terlebih Dahulu!");
                return;
            }
            if (!ppkd) {
                alert("Pilih PPKD Terlebih Dahulu!");
                return;
            }
            //if (!jenis_ls) {
            //    jenis_ls = '';
            //}
            let url = new URL("{{ route('spm.pernyataan') }}");
            let searchParams = url.searchParams;
            //searchParams.append("jenis_ls", jenis_ls);
            searchParams.append("no_spm", no_spm);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("baris_spm", baris_spm);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak list antrian SPM keseluruhan
        $('.spm_perskpd').on('click', function() {
            let jenis_print = $(this).data("jenis");

            let url = new URL("{{ route('verifikasi_spm.cek_list') }}");
            let searchParams = url.searchParams;
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('#input_batal').on('click', function() {
            let no_spm = document.getElementById('no_spm_batal').value;
            let no_spp = document.getElementById('no_spp_batal').value;
            let beban = document.getElementById('beban_batal').value;
            let keterangan = document.getElementById('keterangan_batal').value;
            let tanya = confirm('Anda yakin akan Membatalkan SPM: ' + no_spm + '  ?');
            // let batal_spm = document.getElementById('batal_spm').checked;
            if (tanya == true) {
                if (!keterangan) {
                    alert('Keterangan pembatalan SPM diisi terlebih dahulu!');
                    return;
                }
                $.ajax({
                    url: "{{ route('spm.batal_spm') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        no_spm: no_spm,
                        no_spp: no_spp,
                        keterangan: keterangan,
                        beban: beban,
                        // batal_spm: batal_spm,
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('SPM Berhasil Dibatalkan');
                            window.location.href = "{{ route('spm.index') }}";
                        } else {
                            alert('SPM Tidak Berhasil Dibatalkan');
                            return;
                        }
                    }
                })
            }
        });

    });

    function updateCekStatus(checkbox, no_spm) {
        let cek = checkbox.checked ? 1 : 0;
        let message = cek === 1 ? 'berkas sudah diterima oleh perbend' : 'berkas belum diterima oleh perbend';
        alert(message);

        $.ajax({
            url: "{{ route('verifikasi_spm.update_cek') }}",
            type: "POST",
            data: {
                no_spm: no_spm,
                cek: cek
            },
            success: function(response) {
                console.log('Status cek berhasil diperbarui.');
            },
            error: function(xhr) {
                console.error('Gagal memperbarui status cek.');
            }
        });
    }


    function cetak(no_spm, beban, kd_skpd) {
        $('#no_spm').val(no_spm);
        $('#beban').val(beban);
        $('#kd_skpd').val(kd_skpd);
        $('#modal_cetak').modal('show');
    }

    function batal_spm(no_spm, beban, kd_skpd, no_spp) {
        $('#no_spm_batal').val(no_spm);
        $('#beban_batal').val(beban);
        $('#no_spp_batal').val(no_spp);
        $('#spm_batal').modal('show');
    }

    function tampil(no_spm, status, kd_skpd, jns_spp) {
        $.ajax({
            url: "{{ route('verifikasi_spm.detail_spm') }}",
            type: "POST",
            dataType: 'json',
            data: {
                no_spm: no_spm,
                kd_skpd: kd_skpd
            },
            success: function(data) {
                $('#no_spmtampil').val(data.no_spm);
                $('#no_spptampil').val(data.no_spp);
                $('#no_tagihtampil').val(data.no_tagih);
                $('#pimpinantampil').val(data.pimpinan);
                $('#ket_basttampil').val(data.ket_bast);
                $('#nm_skpdtampil').val(data.nm_skpd);
                $('#rekanantampil').val(data.nmrekan);
                $('#no_rektampil').val(data.no_rek);
                $('#npwptampil').val(data.npwp);
                $('#banktampil').val(data.nmbank);
                $('#keperluan').val(data.keperluan);
                //$('#nilaitampil').val(data.nilai);
                $("#nilaitampil").val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.nilai));
                $('#statustampil').val(data.status);
                $('#jns_spptampil').val(data.jns_spp);
                $('#kd_skpdtampil').val(data.kd_skpd);
                $('#ket_spm').val(data.ket_validasi);
                $('#tgl_terima_spm').val(data.tgl_setujui);
                $('#stsspm').val(data.status).change();
            }
        });
        $('#modal_tampil').modal('show');

        if (status == 2) {
            $('#save').hide();
        } else {
            $('#save').show();
        }
    }

    function setuju_spm() {
        let no_spm = document.getElementById('no_spmtampil').value;
        let no_spp = document.getElementById('no_spptampil').value;
        let jns_spp = document.getElementById('jns_spptampil').value;
        let kd_skpd = document.getElementById('kd_skpdtampil').value;
        let status = document.getElementById('statustampil').value;
        let tgl_terima = document.getElementById('tgl_terima_spm').value;
        let stsspm = document.getElementById('stsspm').value;
        let ket = document.getElementById('ket_spm').value;
        let tabel = $('#spm').DataTable();

        if (!tgl_terima) {
            alert('Tanggal Terima SPM belum dipilih!');
            return;
        }

        if (!ket) {
            alert('Keterangan SPM Belum di isi!');
            return;
        }

        if (!stsspm) {
            alert('Keterangan SPM Belum di isi!');
            return;
        }

        $.ajax({
            url: "{{ route('verifikasi_spm.setuju_spm') }}",
            type: "POST",
            dataType: 'json',
            data: {
                no_spm: no_spm,
                no_spp: no_spp,
                jns_spp: jns_spp,
                kd_skpd: kd_skpd,
                status: status,
                tgl_terima: tgl_terima,
                stsspm: stsspm,
                ket: ket,
            },
            success: function(data) {
                if (data.message == '1') {
                    alert('Data Berhasil Disimpan!!');
                    $('#modal_tampil').modal('hide');
                    tabel.ajax.reload();
                } else {
                    alert('Data Gagal Disimpan...!!!');
                }
            }
        })
    }

    function deleteData(no_spp) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor SPP : ' + no_spp)
        if (tanya == true) {
            $.ajax({
                url: "{{ route('sppls.hapus_sppls') }}",
                type: "DELETE",
                dataType: 'json',
                data: {
                    no_spp: no_spp
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil dihapus!');
                        location.reload();
                    } else {
                        alert('Data gagal dihapus!');
                        location.reload();
                    }
                }
            })
        } else {
            return false;
        }
    }
</script>
