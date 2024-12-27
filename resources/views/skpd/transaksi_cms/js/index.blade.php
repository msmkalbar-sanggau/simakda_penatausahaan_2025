<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let status_ang = document.getElementById('status_ang').value;
        let selisih_angkas = document.getElementById('selisih_angkas').value;
        if (status_ang == 0 || status_ang == '0') {
            alert('DPA Belum Disahkan!');
        }
        if (selisih_angkas > 0) {
            alert('Masih ada ' + selisih_angkas +
                ' Selisih antara Anggaran dan Anggaran Kas, Anda tidak bisa melanjutkan transaksi');
        }

        $('#transaksi_cms').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10, 20, 50],
            ajax: {
                "url": "{{ route('skpd.transaksi_cms.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status_upload == 1 && data.status_validasi == 1) {
                    $(row).css("background-color", "#00a5ff");
                } else if (data.status_upload == 1) {
                    $(row).css("background-color", "#12cc2e");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center"
                }, {
                    data: 'no_voucher',
                    name: 'no_voucher',
                    className: "text-center",
                },
                {
                    data: 'tgl_voucher',
                    name: 'tgl_voucher',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                },
                {
                    data: null,
                    name: 'ket',
                    render: function(data, type, row, meta) {
                        return data.ket.substr(0, 10) + '.....';
                    }
                },
                {
                    data: 'status_upload',
                    name: 'status_upload',
                },
                {
                    data: 'status_validasi',
                    name: 'status_validasi',
                },
                {
                    data: 'status_trmpot',
                    name: 'status_trmpot',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 200,
                    className: "text-center",
                },
            ],
        });
        $('#cetak_cms').on('click', function() {
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            if (!tgl_voucher) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }
            let url = new URL("{{ route('skpd.transaksi_cms.cetak_list') }}");
            let searchParams = url.searchParams;
            searchParams.append("tgl_voucher", tgl_voucher);
            window.open(url.toString(), "_blank");
        });

    });

    function deleteData(no_voucher) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor Bukti : ' + no_voucher)
        if (tanya == true) {
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.hapus_cms') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_voucher: no_voucher
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil dihapus!');
                        location.reload();
                    } else {
                        alert('Data gagal dihapus!');
                        return;
                    }
                }
            })
        } else {
            return false;
        }
    }
</script>
