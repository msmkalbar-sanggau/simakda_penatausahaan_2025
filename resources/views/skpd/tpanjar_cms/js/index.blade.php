    <script>
         $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#panjar').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [8, 20, 50, 100],
                ajax: {
                    "url": "{{ route('tpanjar_cms.load_data') }}",
                    "type": "POST",
                },
                createdRow: function(row, data, index) {
                if (data.status_upload == "1" && data.status_validasi == "1") {
                    $(row).css("background-color", "#98FB98");
                } else if (data.status_upload == "1") {
                    $(row).css("background-color", "#B0E0E6");
                }
            },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'no_panjar',
                        name: 'no_panjar'
                    },
                    {
                        data: 'tgl_panjar',
                        name: 'tgl_panjar'
                    },
                    {
                        data: 'kd_skpd',
                        name: 'kd_skpd'
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
                        width: '200px',
                        className: 'text-center'
                    },
                ],
            });

            $('#cetak_panjar').on('click', function() {
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            if (!tgl_voucher) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }
            let url = new URL("{{ route('tpanjar_cms.cetak_list') }}");
            let searchParams = url.searchParams;
            searchParams.append("tgl_voucher", tgl_voucher);
            window.open(url.toString(), "_blank");
        });
        });

        function deleteData(no_panjar, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Kas : ' + no_panjar);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('tpanjar_cms.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_panjar: no_panjar,
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