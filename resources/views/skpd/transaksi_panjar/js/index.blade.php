<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#transaksipanjar').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('transaksipanjar.load') }}",
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
                    name: 'keterangan',
                    render: function(data, type, row, meta) {
                        return data.ket.substr(0, 10) + '.....';
                    }
                },
                {
                    data: null,
                    name: 'lpj',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.ketlpj == '1') {
                            return '&#10004';
                        } else {
                            return '&#10008';
                        }
                    }
                },
                {
                    data: null,
                    name: 'spj',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.ketspj == '1') {
                            return '&#10004';
                        } else {
                            return '&#10008';
                        }
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

    function hapus(no_bukti, no_kas, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Bukti : ' + no_kas);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('transaksipanjar.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_kas: no_kas,
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
