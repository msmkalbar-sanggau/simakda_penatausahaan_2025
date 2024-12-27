<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#cair_sp2d').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('skpd.pencairan_sp2d.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status == 1) {
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
                    data: null,
                    name: 'status',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.status == 1) {
                            return 'Sudah Cair';
                        } else {
                            return 'Belum Cair';
                        }
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 200,
                    className: "text-center",
                },
            ],
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
