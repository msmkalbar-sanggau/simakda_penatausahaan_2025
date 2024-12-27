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

        $('#spm').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 20, 50],
            ajax: {
                "url": "{{ route('spm.load_data') }}",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            createdRow: function(row, data, index) {
                if (data.status == 1 && data.sp2d_batal != '1') {
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
                    data: 'no_spm',
                    name: 'no_spm',
                    className: "text-center",
                },
                {
                    data: 'tgl_spm',
                    name: 'tgl_spm',
                    // className: "text-center",
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: null,
                    name: 'keperluan',
                    render: function(data, type, row, meta) {
                        return data.keperluan.substr(0, 10) + '.....';
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 200,
                    className: "text-center",
                },
            ],
            drawCallback: function(settings) {
                console.log('drawCallback');
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
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
            let url = new URL("{{ route('spm.berkas_spm') }}");
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

        // cetak ringkasan
        $('.ringkasan').on('click', function() {
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
            let url;
            if (beban == '1') {
                url = new URL("{{ route('spm.ringkasan_up') }}");
            } else if (beban == '2' || beban == '3' || beban == '4' || beban == '5' || beban == '6') {
                url = new URL("{{ route('spm.ringkasan_gu') }}");
            }
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

        // cetak pengantar
        $('.pengantar').on('click', function() {
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
            let url = new URL("{{ route('spm.pengantar') }}");
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

        // cetak lampiran
        $('.lampiran').on('click', function() {
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
            let url = new URL("{{ route('spm.lampiran') }}");
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

        // cetak tanggung jawab
        $('.tanggung_jawab').on('click', function() {
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
            let url = new URL("{{ route('spm.tanggung') }}");
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

        // cetak pernyataan
        $('.pernyataan').on('click', function() {
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
            let url = new URL("{{ route('spm.pernyataan') }}");
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
                        "_token": "{{ csrf_token() }}",
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

    function deleteData(no_spp) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor SPP : ' + no_spp)
        if (tanya == true) {
            $.ajax({
                url: "{{ route('sppls.hapus_sppls') }}",
                type: "DELETE",
                dataType: 'json',
                data: {
                    no_spp: no_spp,
                    "_token": "{{ csrf_token() }}",
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
