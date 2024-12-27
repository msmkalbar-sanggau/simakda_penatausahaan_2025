<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        let tabel = $('#cair_sp2d').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10, 20, 30, 40, 50, 100],
            ajax: {
                "url": "{{ route('pencairan_sp2d.load_data') }}",
                "type": "POST",
                "data": function(d) {
                    d.tipe = document.getElementById('tipe').value;
                },
            },
            createdRow: function(row, data, index) {
                if (data.status_bud == 1) {
                    $(row).css("background-color", "#4bbe68");
                    $(row).css("color", "white");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center"
                }, {
                    data: 'no_sp2d',
                    name: 'no_sp2d',
                    className: "text-center",
                },
                {
                    data: 'no_spm',
                    name: 'no_spm',
                    // className: "text-center",
                },
                {
                    data: 'tgl_sp2d',
                    name: 'tgl_sp2d',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 200,
                    className: "text-center",
                },
            ],
        });

        $('.tipe').on('click', function() {
            let jenis = $(this).data("jenis");
            $('#tipe').val(jenis);
            tabel.ajax.reload();
        });

        $('#ttd_bud').select2({
            dropdownParent: $('#modal_cetak'),
            theme: 'bootstrap-5'
        });

        $('#ttd1').select2({
            dropdownParent: $('#modal_cetak'),
            theme: 'bootstrap-5'
        });

        $('#ttd2').select2({
            dropdownParent: $('#modal_cetak'),
            theme: 'bootstrap-5'
        });

        $('#jenis').select2({
            dropdownParent: $('#modal_cetak'),
            theme: 'bootstrap-5'
        });

        // cetak sp2d
        $('.sp2d').on('click', function() {
            let no_sp2d = document.getElementById('no_sp2d').value;
            let ttd_bud = document.getElementById('ttd_bud').value;
            let ttd1 = document.getElementById('ttd1').value;
            let ttd2 = document.getElementById('ttd2').value;
            let baris = document.getElementById('baris').value;
            let jenis = document.getElementById('jenis').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            if (!ttd_bud) {
                alert('Pilih Penandatangan BUD Terlebih Dahulu!');
                return;
            }
            if (!ttd1) {
                alert("Pilih Penandatangan I Terlebih Dahulu!");
                return;
            }
            if (!ttd2) {
                alert("Pilih Penandatangan II Terlebih Dahulu!");
                return;
            }
            if (!jenis) {
                alert("Pilih Jenis Terlebih Dahulu!");
                return;
            }
            let url = new URL("{{ route('sp2d.cetak_sp2d') }}");
            let searchParams = url.searchParams;
            searchParams.append("jenis", jenis);
            searchParams.append("no_sp2d", no_sp2d);
            searchParams.append("ttd_bud", ttd_bud);
            searchParams.append("ttd1", ttd1);
            searchParams.append("ttd2", ttd2);
            searchParams.append("baris", baris);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak lampiran
        $('.lampiran').on('click', function() {
            let no_sp2d = document.getElementById('no_sp2d').value;
            let ttd_bud = document.getElementById('ttd_bud').value;
            let ttd1 = document.getElementById('ttd1').value;
            let ttd2 = document.getElementById('ttd2').value;
            let baris = document.getElementById('baris').value;
            let jenis = document.getElementById('jenis').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            if (!ttd_bud) {
                alert('Pilih Penandatangan BUD Terlebih Dahulu!');
                return;
            }
            if (!ttd1) {
                alert("Pilih Penandatangan I Terlebih Dahulu!");
                return;
            }
            if (!ttd2) {
                alert("Pilih Penandatangan II Terlebih Dahulu!");
                return;
            }
            if (!jenis) {
                alert("Pilih Jenis Terlebih Dahulu!");
                return;
            }
            let url = new URL("{{ route('sp2d.cetak_lampiran') }}");
            let searchParams = url.searchParams;
            searchParams.append("jenis", jenis);
            searchParams.append("no_sp2d", no_sp2d);
            searchParams.append("ttd_bud", ttd_bud);
            searchParams.append("ttd1", ttd1);
            searchParams.append("ttd2", ttd2);
            searchParams.append("baris", baris);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak lampiran lama
        $('.lampiran_lama').on('click', function() {
            let no_sp2d = document.getElementById('no_sp2d').value;
            let ttd_bud = document.getElementById('ttd_bud').value;
            let ttd1 = document.getElementById('ttd1').value;
            let ttd2 = document.getElementById('ttd2').value;
            let baris = document.getElementById('baris').value;
            let jenis = document.getElementById('jenis').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            if (!ttd_bud) {
                alert('Pilih Penandatangan BUD Terlebih Dahulu!');
                return;
            }
            if (!ttd1) {
                alert("Pilih Penandatangan I Terlebih Dahulu!");
                return;
            }
            if (!ttd2) {
                alert("Pilih Penandatangan II Terlebih Dahulu!");
                return;
            }
            if (!jenis) {
                alert("Pilih Jenis Terlebih Dahulu!");
                return;
            }
            let url = new URL("{{ route('sp2d.cetak_lampiran_lama') }}");
            let searchParams = url.searchParams;
            searchParams.append("jenis", jenis);
            searchParams.append("no_sp2d", no_sp2d);
            searchParams.append("ttd_bud", ttd_bud);
            searchParams.append("ttd1", ttd1);
            searchParams.append("ttd2", ttd2);
            searchParams.append("baris", baris);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        // cetak kelengkapan
        $('.kelengkapan').on('click', function() {
            let no_sp2d = document.getElementById('no_sp2d').value;
            let ttd_bud = document.getElementById('ttd_bud').value;
            let ttd1 = document.getElementById('ttd1').value;
            let ttd2 = document.getElementById('ttd2').value;
            let baris = document.getElementById('baris').value;
            let jenis = document.getElementById('jenis').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let beban = document.getElementById('beban').value;
            if (!ttd_bud) {
                alert('Pilih Penandatangan BUD Terlebih Dahulu!');
                return;
            }
            if (!ttd1) {
                alert("Pilih Penandatangan I Terlebih Dahulu!");
                return;
            }
            if (!ttd2) {
                alert("Pilih Penandatangan II Terlebih Dahulu!");
                return;
            }
            if (!jenis) {
                alert("Pilih Jenis Terlebih Dahulu!");
                return;
            }
            let url = new URL("{{ route('sp2d.cetak_kelengkapan') }}");
            let searchParams = url.searchParams;
            searchParams.append("jenis", jenis);
            searchParams.append("no_sp2d", no_sp2d);
            searchParams.append("ttd_bud", ttd_bud);
            searchParams.append("ttd1", ttd1);
            searchParams.append("ttd2", ttd2);
            searchParams.append("baris", baris);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("beban", beban);
            window.open(url.toString(), "_blank");
        });

        $('#input_batal').on('click', function() {
            let no_sp2d = document.getElementById('no_sp2d_batal').value;
            let no_spm = document.getElementById('no_spm_batal').value;
            let no_spp = document.getElementById('no_spp_batal').value;
            let beban = document.getElementById('beban_batal').value;
            let status_bud = document.getElementById('status_bud').value;
            let keterangan = document.getElementById('keterangan_batal').value;
            if (status_bud == '1') {
                alert("SP2D telah diterima SKPD. Batalkan Penerimaan terlebih dahulu");
                return;
            }
            let tanya = confirm('Anda yakin akan Membatalkan SP2D: ' + no_sp2d + '  ?');
            if (tanya == true) {
                if (!keterangan) {
                    alert('Keterangan pembatalan SP2D diisi terlebih dahulu!');
                    return;
                }
                $.ajax({
                    url: "{{ route('sp2d.batal_sp2d') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        no_sp2d: no_sp2d,
                        no_spm: no_spm,
                        no_spp: no_spp,
                        keterangan: keterangan,
                        beban: beban,
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('SP2D Berhasil Dibatalkan');
                            window.location.href = "{{ route('sp2d.index') }}";
                        } else {
                            alert('SP2D Tidak Berhasil Dibatalkan');
                            return;
                        }
                    }
                })
            }
        });

    });

    function cetak(no_sp2d, beban, kd_skpd) {
        $('#no_sp2d').val(no_sp2d);
        $('#beban').val(beban);
        $('#kd_skpd').val(kd_skpd);
        if (beban == '4') {
            $('#lampiran_lama').show();
        } else {
            $('#lampiran_lama').hide();
        }
        $('#modal_cetak').modal('show');
    }

    function batal_sp2d(no_sp2d, beban, kd_skpd, no_spm, no_spp, status) {
        $('#no_sp2d_batal').val(no_sp2d);
        $('#beban_batal').val(beban);
        $('#no_spm_batal').val(no_spm);
        $('#no_spp_batal').val(no_spp);
        $('#status_bud').val(status);
        $('#sp2d_batal').modal('show');
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
