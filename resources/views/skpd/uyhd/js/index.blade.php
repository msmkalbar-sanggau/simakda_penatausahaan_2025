<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#uyhd').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('skpd.uyhd.load_data') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'NO_BUKTI',
                    name: 'NO_BUKTI',
                    className: "text-center",
                },
                {
                    data: 'TGL_BUKTI',
                    name: 'TGL_BUKTI',
                    className: "text-center",
                },
                {
                    data: 'KD_SKPD',
                    name: 'KD_SKPD',
                    className: "text-center",
                },
                {
                    data: 'pay',
                    name: 'pay',
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

    function hapusUyhd(no_bukti, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor : ' + no_bukti);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('skpd.uyhd.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_bukti: no_bukti,
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
