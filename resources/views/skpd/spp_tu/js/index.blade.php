<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select-modal').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#spp_tu').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 20, 50],
            ajax: {
                "url": "{{ route('spp_tu.load') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status == 1 && data.sp2d_batal != '1') {
                    $(row).css("background-color", "#03d3ff");
                } else if (data.sp2d_batal == '1') {
                    $(row).css("background-color", "#ad3e4f");
                    $(row).css("color", "white");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'no_spp',
                    name: 'no_spp',
                },
                {
                    data: 'tgl_spp',
                    name: 'tgl_spp',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                },
                {
                    data: null,
                    name: 'keperluan',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return data.keperluan.substr(0, 10) + '.....';
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: '100px',
                    className: "text-center",
                },
            ],
        });

        // cetak pengantar layar
        $('.pengantar_layar').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Bendahara Penghasilan tidak boleh kosong!');
                return;
            }
            if (!pptk) {
                alert("PPTK tidak boleh kosong!");
                return;
            }
            if (!ppkd) {
                alert("PPKD tidak boleh kosong!");
                return;
            }
            let url = new URL("{{ route('spp_tu.pengantar') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        // cetak rincian layar
        $('.rincian_layar').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Bendahara Penghasilan tidak boleh kosong!');
                return;
            }
            if (!pptk) {
                alert("PPTK tidak boleh kosong!");
                return;
            }
            if (!ppkd) {
                alert("PPKD tidak boleh kosong!");
                return;
            }
            let url = new URL("{{ route('spp_tu.rincian') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        // cetak permintaan layar
        $('.permintaan_layar').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Bendahara Penghasilan tidak boleh kosong!');
                return;
            }
            if (!pptk) {
                alert("PPTK tidak boleh kosong!");
                return;
            }
            if (!ppkd) {
                alert("PPKD tidak boleh kosong!");
                return;
            }
            let url = new URL("{{ route('spp_tu.permintaan') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.ringkasan_layar').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Bendahara Penghasilan tidak boleh kosong!');
                return;
            }
            if (!pptk) {
                alert("PPTK tidak boleh kosong!");
                return;
            }
            if (!ppkd) {
                alert("PPKD tidak boleh kosong!");
                return;
            }
            let url = new URL("{{ route('spp_tu.ringkasan') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.pernyataan_layar').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Bendahara Penghasilan tidak boleh kosong!');
                return;
            }
            if (!pptk) {
                alert("PPTK tidak boleh kosong!");
                return;
            }
            if (!ppkd) {
                alert("PPKD tidak boleh kosong!");
                return;
            }
            let url = new URL("{{ route('spp_tu.pernyataan') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.sptb_layar').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Bendahara Penghasilan tidak boleh kosong!');
                return;
            }
            if (!pptk) {
                alert("PPTK tidak boleh kosong!");
                return;
            }
            if (!ppkd) {
                alert("PPKD tidak boleh kosong!");
                return;
            }
            let url = new URL("{{ route('spp_tu.sptb') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.spp_layar').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Bendahara Penghasilan tidak boleh kosong!');
                return;
            }
            if (!pptk) {
                alert("PPTK tidak boleh kosong!");
                return;
            }
            if (!ppkd) {
                alert("PPKD tidak boleh kosong!");
                return;
            }
            let url = new URL("{{ route('spp_tu.spp') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.rincian77_layar').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
            let jenis_print = $(this).data("jenis");
            let tanpa;
            if (tanpa_tanggal == false) {
                tanpa = 0;
            } else {
                tanpa = 1;
            }
            if (!bendahara) {
                alert('Bendahara Penghasilan tidak boleh kosong!');
                return;
            }
            if (!pptk) {
                alert("PPTK tidak boleh kosong!");
                return;
            }
            if (!ppkd) {
                alert("PPKD tidak boleh kosong!");
                return;
            }
            let url = new URL("{{ route('spp_tu.rincian77') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
    });

    function cetak(no_spp, beban, kd_skpd) {
        $('#no_spp').val(no_spp);
        $('#beban').val(beban);
        $('#kd_skpd').val(kd_skpd);
        $('#modal_cetak').modal('show');
    }

    function hapus(no_spp, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor SPP : ' + no_spp);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('spp_tu.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spp: no_spp,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Proses Hapus Berhasil');
                        window.location.reload();
                    } else {
                        alert('Proses Hapus Gagal...!!!');
                    }
                }
            })
        } else {
            return false;
        }
    }
</script>
