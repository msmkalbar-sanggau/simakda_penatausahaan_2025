<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#transaksi_lalu').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('transaksi_lalu.load') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'no_bukti',
                    name: 'no_bukti',
                    className: "text-center",
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
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
                    data: 'ketpot',
                    name: 'ketpot',
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

    function hapus(no_bukti, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Bukti : ' + no_bukti);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('transaksi_lalu.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_bukti: no_bukti,
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
