<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#koreksi_nominal').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('koreksi_nominal.load_data') }}",
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
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                },
                {
                    data: null,
                    name: 'ket',
                    render: function(data, type, row, meta) {
                        return data.ket.substr(0, 10) + '.....';
                    }
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

    function hapusRekening(no_bukti, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor : ' + no_bukti);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('koreksi_nominal.hapus') }}",
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
