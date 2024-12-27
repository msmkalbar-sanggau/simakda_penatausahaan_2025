<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let data_awal = $('#list_blm_validasi').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('skpd.pelimpahan.load_validasi') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, {
                    data: 'no_bukti',
                    name: 'no_bukti'
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti'
                },
                {
                    data: 'tgl_validasi',
                    name: 'tgl_validasi',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd'
                },
                {
                    data: null,
                    name: 'keterangan',
                    render: function(data, type, row, meta) {
                        return data.keterangan.substr(0, 10) + '.....';
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
                    data: 'no_upload',
                    name: 'no_upload',
                    visible: false
                },
                {
                    data: 'status_upload',
                    name: 'status_upload',
                    visible: false
                },
                {
                    data: 'tgl_upload',
                    name: 'tgl_upload',
                    visible: false
                },
                {
                    data: null,
                    name: 'status_validasi',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.status_validasi == '1') {
                            return '&#10004';
                        } else {
                            return '&#10008';
                        }
                    }
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

        let data_validasi = $('#draft_validasi').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            autoWidth: false,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('skpd.pelimpahan.draft_validasi') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                }, {
                    data: 'no_bukti',
                    name: 'no_bukti',
                    visible: false
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti',
                    visible: false
                },
                {
                    data: 'no_kas',
                    name: 'no_kas',
                },
                {
                    data: 'tgl_validasi',
                    name: 'tgl_validasi',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd'
                },
                {
                    data: null,
                    name: 'keterangan',
                    render: function(data, type, row, meta) {
                        return data.keterangan.substr(0, 10) + '.....';
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
                    data: 'no_upload',
                    name: 'no_upload',
                    visible: false
                },
                {
                    data: 'status_upload',
                    name: 'status_upload',
                    visible: false
                },
                {
                    data: 'tgl_upload',
                    name: 'tgl_upload',
                    visible: false
                },
                {
                    data: 'status_validasi',
                    name: 'status_validasi',
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
                {
                    data: null,
                    name: 'aksi',
                    width: '100px',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        if (data.status_ambil1 == '1' || !data.tgl_validasi) {
                            return ``;
                        } else {
                            return `<a href = "javascript:void(0);" onclick = "batalValidasi('${data.no_kas}','${data.kd_skpd}','${data.no_bukti}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`;
                        }
                    }
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

        $('#cetak_cms').on('click', function() {
            let data = JSON.stringify(data_awal.rows().data().toArray());
        });

        $('#cetakCsvKalbar').on('click', function() {
            let no_upload = document.getElementById('no_upload').value;
            let url = new URL("{{ route('skpd.upload_cms.cetak_csv_kalbar') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_upload", no_upload);
            window.open(url.toString(), "_blank");
        });

        $('#cetakCsvLuarKalbar').on('click', function() {
            let no_upload = document.getElementById('no_upload').value;
            let url = new URL("{{ route('skpd.upload_cms.cetak_csv_luar_kalbar') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_upload", no_upload);
            window.open(url.toString(), "_blank");
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

    function lihatData(nm_skpd, no_voucher, tgl_voucher, no_sp2d, ket, kd_sub_kegiatan, nm_sub_kegiatan, kd_skpd) {
        $('#kd_skpd').val(kd_skpd);
        $('#nm_skpd').val(nm_skpd);
        $('#nomor').val(no_voucher);
        $('#tanggal').val(tgl_voucher);
        $('#no_sp2d').val(no_sp2d);
        $('#keterangan').val(ket);
        $('#kd_sub_kegiatan').val(kd_sub_kegiatan);
        $('#nm_sub_kegiatan').val(nm_sub_kegiatan);
        $('#rekening_transaksi').DataTable().ajax.reload();
        $('#rekening_potongan').DataTable().ajax.reload();
        $('#rekening_tujuan').DataTable().ajax.reload();
        $('#modal_lihat').modal('show');
    }

    function pilihUpload(no_voucher, tgl_voucher, kd_skpd, ket, total, bersih, status_upload, rekening_awal,
        nm_rekening_tujuan, rekening_tujuan, bank_tujuan, ket_tujuan) {
        let total_transaksi = rupiah(document.getElementById('total_transaksi').value);
        let nilai = parseFloat(total);
        let nilai_bersih = parseFloat(bersih);
        let pot = nilai - nilai_bersih;

        let pilih = document.getElementById('pilih' + no_voucher).checked;
        if (pilih == true) {
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi + nilai));
        } else {
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi - nilai));
        }

    }

    function batalValidasi(no_kas, kd_skpd, no_bukti) {
        let tanya = confirm('Apakah anda yakin untuk membatalkan dengan Nomor Bukti : ' + no_kas);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('skpd.pelimpahan.batal_validasi') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_kas: no_kas,
                    kd_skpd: kd_skpd,
                    no_bukti: no_bukti,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Proses Batal Berhasil');
                        window.location.href = "{{ route('skpd.pelimpahan.validasi') }}";
                    } else {
                        alert('Proses Batal Gagal...!!!');
                    }
                }
            })
        } else {
            return false;
        }
    }

    function lihatDataUpload(no_upload, total) {
        $('#no_upload').val(no_upload);
        $('#total_transaksi_satuan').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(total));
        let tabel = $('#data_transaksi1').DataTable();
        tabel.ajax.reload();
        $('#modal_transaksi').modal('show');
    }
</script>
