<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let tabel = $('#penerimaan_kas').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 20, 50, 100, 500, 1000],
            ajax: {
                "url": "{{ route('penerimaan_kas.load_data') }}",
                "type": "POST",
                "data": function(d) {
                    d.tipe = document.getElementById('tipe').value;
                },
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
                    name: 'DT_RowIndex',
                    className: "text-center",
                },
                {
                    data: 'no_kas',
                    name: 'no_kas',
                    className: "text-center",
                },
                {
                    data: 'no_sts',
                    name: 'no_sts',
                    className: "text-center",
                },
                {
                    data: 'tgl_kas',
                    name: 'tgl_kas',
                    className: "text-center",
                },
                {
                    data: 'tgl_sts',
                    name: 'tgl_sts',
                    className: "text-center",
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: null,
                    name: 'total',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.total)
                    }
                },
                {
                    data: 'keterangan',
                    name: 'keterangan',
                    // render: function(data, type, row, meta) {
                    //     return data.keterangan.substr(0, 10) + '.....';
                    // }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
            ],
        });

        $('.tipe').on('click', function() {
            let jenis = $(this).data("jenis");
            $('#tipe').val(jenis);
            tabel.ajax.reload();
        });

    });

    function angka(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function hapus(no_kas, no_sts, kd_skpd, tgl_kas) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Kas : ' + no_kas);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('penerimaan_kas.kunci_kasda') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (tgl_kas < data) {
                        alert('Tanggal kas lebih kecil dari tanggal kuncian!');
                        return;
                    } else {
                        $.ajax({
                            url: "{{ route('penerimaan_kas.hapus') }}",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                no_kas: no_kas,
                                no_sts: no_sts,
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
                    }
                }
            })
        } else {
            return false;
        }
    }

    function cetak(no_kas, no_sts, kd_skpd) {
        let url = new URL("{{ route('penerimaan_kas.cetak') }}");
        let searchParams = url.searchParams;
        searchParams.append("no_kas", no_kas);
        searchParams.append("no_sts", no_sts);
        searchParams.append("kd_skpd", kd_skpd);
        window.open(url.toString(), "_blank");
    }
</script>
