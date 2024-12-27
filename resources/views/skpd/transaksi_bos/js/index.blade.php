<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#transaksi_bos').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('transaksi_bos.load') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.ketlpj == 1 || data.ketspj == 1) {
                    $(row).css("color", "#02087f");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'no_bukti',
                    name: 'no_bukti',
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti',
                    className: "text-center",
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                },
                {
                    data: 'ket',
                    name: 'ket',
                },
                {
                    data: 'ketlpj',
                    name: 'ketlpj',
                    className: "text-center",
                },
                {
                    data: 'ketspj',
                    name: 'ketspj',
                    className: "text-center",
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
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor : ' + no_kas);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('transaksi_bos.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_kas: no_kas,
                    kd_skpd: kd_skpd,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Proses Hapus Berhasil');
                        window.location.reload();
                    } else {
                        alert('Proses Hapus Gagal...!!!');
                    }
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        } else {
            return false;
        }
    }
</script>
