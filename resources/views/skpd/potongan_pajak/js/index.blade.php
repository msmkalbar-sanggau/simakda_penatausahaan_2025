<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let data_awal = $('#draft_potongan').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('skpd.potongan_pajak.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status == "1") {
                    $(row).css("color", "#6293BB");
                    $(row).css("font-weight", "bold");
                }
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
                    name: 'tgl_bukti'
                },
                {
                    data: null,
                    name: 'ket',
                    render: function(data, type, row, meta) {
                        return data.ket.substr(0, 10) + '.....';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
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

        let transaksi_data = $('#data_transaksi1').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('skpd.upload_cms.data_upload') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_upload = document.getElementById('no_upload').value;
                }
            },
            ordering: false,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, {
                    data: 'no_voucher',
                    name: 'no_voucher'
                },
                {
                    data: 'tgl_voucher',
                    name: 'tgl_voucher'
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd'
                },
                {
                    data: null,
                    name: 'ket',
                    render: function(data, type, row, meta) {
                        return data.ket.substr(0, 10) + '.....';
                    }
                },
                {
                    data: 'total',
                    name: 'total',
                    visible: false
                },
                {
                    data: null,
                    name: 'bersh',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.bersih)
                    }
                },
                {
                    data: null,
                    name: 'pot',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.pot)
                    }
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
                    data: 'status_upload',
                    name: 'status_upload',
                },
                {
                    data: 'rekening_awal',
                    name: 'rekening_awal',
                    visible: false
                },
                {
                    data: 'nm_rekening_tujuan',
                    name: 'nm_rekening_tujuan',
                    visible: false
                },
                {
                    data: 'rekening_tujuan',
                    name: 'rekening_tujuan',
                    visible: false
                },
                {
                    data: 'bank_tujuan',
                    name: 'bank_tujuan',
                    visible: false
                },
                {
                    data: 'ket_tujuan',
                    name: 'ket_tujuan',
                    visible: false
                },
            ],
        });
    });

    let data_transaksi = $('#data_transaksi').DataTable({
        responsive: true,
        processing: true,
        ordering: false,
        columns: [{
                data: 'no_voucher',
                name: 'no_voucher',
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
                data: 'total',
                name: 'total',
                visible: false
            },
            {
                data: 'netto',
                name: 'netto',
            },
            {
                data: 'pot',
                name: 'pot',
            },
            {
                data: 'nilai_total',
                name: 'nilai_total',
            },
            {
                data: 'status_upload',
                name: 'status_upload',
                visible: false
            },
            {
                data: 'rekening_awal',
                name: 'rekening_awal',
                visible: false
            },
            {
                data: 'nm_rekening_tujuan',
                name: 'nm_rekening_tujuan',
                visible: false
            },
            {
                data: 'rekening_tujuan',
                name: 'rekening_tujuan',
                visible: false
            },
            {
                data: 'bank_tujuan',
                name: 'bank_tujuan',
                visible: false
            },
            {
                data: 'ket_tujuan',
                name: 'ket_tujuan',
                visible: false
            },
        ]
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

    function hapusPotongan(no_bukti) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Bukti : ' + no_bukti);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('skpd.potongan_pajak.hapus_potongan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_bukti: no_bukti,
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
