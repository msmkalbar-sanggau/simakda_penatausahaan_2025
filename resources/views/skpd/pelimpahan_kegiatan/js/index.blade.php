<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let data_pelimpahan = $('#data_pelimpahan').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('skpd.pelimpahan_kegiatan.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status_upload == "1" && data.status_validasi == "1") {
                    $(row).css("background-color", "#B0E0E6");
                } else if (data.status_upload == "1") {
                    $(row).css("background-color", "#98FB98");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'id_user',
                    name: 'id_user',
                    className: "text-center",
                },
                {
                    data: 'kd_bpp',
                    name: 'kd_bpp',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
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

    function hapusPelimpahan(id_user, kd_bpp) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Id User : ' + id_user);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('skpd.pelimpahan_kegiatan.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    id_user: id_user,
                    kd_bpp: kd_bpp,
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
