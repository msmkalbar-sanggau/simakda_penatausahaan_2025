<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#pemberian_panjarcms').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('pemberian_panjarcms.load') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status_upload == 1 || data.status_validasi == 1) {
                    $(row).css("background-color", "#00a5ff");
                } else if (data.status_upload == 1) {
                    $(row).css("background-color", "#12cc2e");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'no_panjar',
                    name: 'no_panjar',
                    className: "text-center",
                },
                {
                    data: 'tgl_panjar',
                    name: 'tgl_panjar',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: null,
                    name: 'nilai',
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

    function hapus(no_kas, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Panjar : ' + no_kas);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('pemberian_panjarcms.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_kas: no_kas,
                    kd_skpd: kd_skpd
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
