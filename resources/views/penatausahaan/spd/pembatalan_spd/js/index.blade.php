<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#pembatalan_spd').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 10, 20, 50],
            ajax: {
                "url": "{{ route('spd.pembatalan_spd.load_data') }}",
                "type": "get",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                },
                {
                    data: 'no_spd',
                    name: 'no_spd',
                    className: "text-left",
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                    className: "text-left",
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    className: "text-center",
                },
            ],
            drawCallback: function (settings) {
                console.log('drawCallback');
                $('[data-bs-toggle="tooltip"]').tooltip();
                }
        });
    });

    function ubahStatus(no_spd, status) {
        $.ajax({
            url: "{{ route('spd.spd_belanja.update_status') }}",
            type: "patch",
            dataType: 'json',
            data: {
                no_spd: no_spd,
                status: status,
            },
            success: function(data) {
                if (data.message == '1') {
                    alert('Proses Status Aktif Berhasil');
                } else if (data.message == '2') {
                    alert('Proses Status Tidak Aktif Berhasil');
                } else {
                    alert("Data Gagal Tersimpan!!!");
                    return;
                }
            }
        })
    }
</script>