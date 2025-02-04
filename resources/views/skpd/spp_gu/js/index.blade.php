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

        $('#spp_gu').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 20, 50],
            ajax: {
                "url": "{{ route('spp_gu.load') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status == 1 && (data.sp2d_batal == '0' || data.sp2d_batal == '' || data
                        .sp2d_batal == null)) {
                    $(row).css("background-color", "#03d3ff");
                    $(row).css("color", "white");
                } else if (data.status == 1 && data.sp2d_batal == '1') {
                    $(row).css("background-color", "#ff0000");
                    $(row).css("color", "white");
                } else if (data.sp2d_batal == '1') {
                    $(row).css("background-color", "#ff0000");
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
                    className: "text-center",
                },
                {
                    data: 'tgl_spp',
                    name: 'tgl_spp',
                    className: "text-center",
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                },
                {
                    data: 'keperluan',
                    name: 'keperluan',
                    className: 'text-right',
                },
                {
                    data: null,
                    name: 'nilai',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
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
            let spasi = document.getElementById('spasi').value;
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
            let url = new URL("{{ route('spp_gu.pengantar') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("spasi", spasi);
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
            let spasi = document.getElementById('spasi').value;
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
            let url = new URL("{{ route('spp_gu.rincian') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("spasi", spasi);
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
            let spasi = document.getElementById('spasi').value;
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
            let url = new URL("{{ route('spp_gu.permintaan') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("spasi", spasi);
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
            let spasi = document.getElementById('spasi').value;
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
            let url = new URL("{{ route('spp_gu.ringkasan') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("spasi", spasi);
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
            let spasi = document.getElementById('spasi').value;
            let no_spp = document.getElementById('no_spp').value;
            let beban = document.getElementById('beban').value;
            let bendahara = document.getElementById('bendahara').value;
            let pptk = document.getElementById('pptk').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let ppkd = document.getElementById('ppkd').value;
            let ppk = document.getElementById('ppk').value;
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
            let url = new URL("{{ route('spp_gu.pernyataan') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("spasi", spasi);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("ppk", ppk);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.sptb_layar').on('click', function() {
            let spasi = document.getElementById('spasi').value;
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
            let url = new URL("{{ route('spp_gu.sptb') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("spasi", spasi);
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
            let spasi = document.getElementById('spasi').value;
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
            let url = new URL("{{ route('spp_gu.spp') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("spasi", spasi);
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
            let spasi = document.getElementById('spasi').value;
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
            let url = new URL("{{ route('spp_gu.rincian77') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp);
            searchParams.append("beban", beban);
            searchParams.append("spasi", spasi);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pptk", pptk);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("ppkd", ppkd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('#batal_sppls').on('click', function() {
            let no_spp = document.getElementById('no_spp_batal').value;
            let keterangan = document.getElementById('keterangan_batal').value;
            let beban = document.getElementById('beban_batal').value;
            let tanya = confirm('Anda yakin akan Membatalkan SPP: ' + no_spp + '  ?');
            if (tanya == true) {
                if (!keterangan) {
                    alert('Keterangan harus diisi!');
                    return;
                }
                $.ajax({
                    url: "{{ route('spp_gu.batal') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        no_spp: no_spp,
                        keterangan: keterangan,
                        beban: beban,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('SPP Berhasil Dibatalkan');
                            window.location.href = "{{ route('spp_gu.index') }}";
                        } else if (data.message == '2') {
                            alert('SPP telah jadi SPM!Tidak dapat dihapus!');
                            return;
                        } else {
                            alert('SPP Berhasil Dibatalkan');
                            return;
                        }
                    }
                })
            }
        });
    });

    function cetak(no_spp, jenis, kd_skpd) {
        $('#no_spp').val(no_spp);
        $('#jenis').val(jenis);
        $('#kd_skpd').val(kd_skpd);
        $('#modal_cetak').modal('show');
    }

    function batal_spp(no_spp, beban, kd_skpd) {
        $('#no_spp_batal').val(no_spp);
        $('#beban_batal').val(beban);
        $('#batal_spp').modal('show');
    }

    function hapus(no_spp, no_lpj, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor SPP : ' + no_spp);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('spp_gu.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spp: no_spp,
                    no_lpj: no_lpj,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Proses Hapus Berhasil');
                        window.location.reload();
                    } else if (data.message == '2') {
                        alert('SPP telah jadi SPM!Tidak dapat dihapus!');
                        return;
                    } else {
                        alert('Proses Hapus Gagal...!!!');
                        return;
                    }
                }
            })
        } else {
            return false;
        }
    }
</script>
