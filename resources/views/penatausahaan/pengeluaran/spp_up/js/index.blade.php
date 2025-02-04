<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#spp_ls').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 20, 50],
            ajax: {
                "url": "{{ route('sppup.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.sp2d_batal == "1") {
                    $(row).css("background-color", "#ff0000");
                    $(row).css("color", "white");
                }
                if (data.status == 1) {
                    $(row).css("background-color", "#4bbe68");
                    $(row).css("color", "white");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, {
                    data: 'no_spp',
                    name: 'no_spp'
                },
                {
                    data: 'tgl_spp',
                    name: 'tgl_spp'
                },
                {
                    data: 'keperluan',
                    name: 'keperluan',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: '200px',
                    className: 'text-center'
                },
            ],
        });

        $('.select2-multiple').select2({
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

        // cetak pengantar layar
        $('.pengantar').on('click', function() {
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
            let url = new URL("{{ route('sppup.pengantar_up') }}");
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
        $('.rincian').on('click', function() {
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
            let url = new URL("{{ route('sppup.rincian_up') }}");
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

        $('.ringkasan').on('click', function() {
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
            let url = new URL("{{ route('sppup.ringkasan_up') }}");
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

        $('.pernyataan').on('click', function() {
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
            let url = new URL("{{ route('sppup.pernyataan_up') }}");
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

        $('.spp').on('click', function() {
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
            let url = new URL("{{ route('sppup.spp_up') }}");
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

        $('.rincian77').on('click', function() {
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
            let url = new URL("{{ route('sppup.rincian77_up') }}");
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
                    url: "{{ route('sppup.batal_spp') }}",
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
                            window.location.href = "{{ route('sppup.index') }}";
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

    function cetak(no_spp, beban, kd_skpd) {
        $('#no_spp').val(no_spp);
        $('#beban').val(beban);
        $('#kd_skpd').val(kd_skpd);
        $('#modal_cetak').modal('show');
    }

    function batal_spp(no_spp, beban, kd_skpd) {
        $('#no_spp_batal').val(no_spp);
        $('#beban_batal').val(beban);
        $('#batal_spp').modal('show');
    }

    function deleteData(no_spp, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor SPP : ' + no_spp)
        if (tanya == true) {
            $.ajax({
                url: "{{ route('sppup.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spp: no_spp,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil dihapus!');
                        location.reload();
                    } else if (data.message == '2') {
                        alert('SPP telah jadi SPM!Tidak dapat dihapus!');
                        return;
                    } else {
                        alert('Data gagal dihapus!');
                        location.reload();
                        return;
                    }
                }
            })
        } else {
            return false;
        }
    }
</script>
