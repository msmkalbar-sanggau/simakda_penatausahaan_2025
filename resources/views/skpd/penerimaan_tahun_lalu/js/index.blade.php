<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#penerimaan_lalu').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('penerimaan_lalu.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.kunci == 1) {
                    $(row).css("background-color", "#4bbe68");
                    $(row).css("color", "white");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'no_terima',
                    name: 'no_terima',
                    className: "text-center",
                },
                {
                    data: 'tgl_terima',
                    name: 'tgl_terima',
                    className: "text-center",
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                    className: "text-center",
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
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
            ],
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

    function hapus(no_terima, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Terima : ' + no_terima);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('penerimaan_lalu.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_terima: no_terima,
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
