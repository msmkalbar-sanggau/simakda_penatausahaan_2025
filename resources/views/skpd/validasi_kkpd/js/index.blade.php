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
                "url": "{{ route('skpd.transaksi_kkpd.load_data_validasi') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'text-center'
                }, {
                    data: 'no_voucher',
                    name: 'no_voucher'
                },
                {
                    data: 'tgl_voucher',
                    name: 'tgl_voucher'
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
                    name: 'ket',
                    render: function(data, type, row, meta) {
                        return data.ket.substr(0, 10) + '.....';
                    }
                },
                {
                    data: null,
                    name: 'total',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.total)
                    }
                },
                {
                    data: 'status_validasi',
                    name: 'status_validasi',
                    className: 'text-center'
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
                    data: 'status_trmpot',
                    name: 'status_trmpot',
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
                "url": "{{ route('skpd.transaksi_kkpd.draft_validasi') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'text-center'
                }, {
                    data: 'no_voucher',
                    name: 'no_voucher'
                },
                {
                    data: 'tgl_voucher',
                    name: 'tgl_voucher'
                },
                {
                    data: 'no_bukti',
                    name: 'no_bukti',
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
                    name: 'ket',
                    render: function(data, type, row, meta) {
                        return data.ket.substr(0, 10) + '.....';
                    }
                },
                {
                    data: null,
                    name: 'total',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.total)
                    }
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
                    data: 'status_trmpot',
                    name: 'status_trmpot',
                    visible: false
                },
                {
                    data: null,
                    name: 'aksi',
                    width: '100px',
                    render: function(data, type, row, meta) {
                        return `<a href = "javascript:void(0);" onclick = "batalValidasi('${data.no_voucher}','${data.kd_skpd}','${data.no_bukti}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`;
                    }
                },
            ],
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

    function batalValidasi(no_voucher, kd_skpd, no_bukti) {
        let tanya = confirm('Apakah anda yakin untuk membatalkan dengan Nomor Voucher : ' + no_voucher);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('skpd.transaksi_kkpd.batal_validasi') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_voucher: no_voucher,
                    kd_skpd: kd_skpd,
                    no_bukti: no_bukti,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Proses Batal Berhasil');
                        window.location.href = "{{ route('skpd.transaksi_kkpd.index_validasi') }}";
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
