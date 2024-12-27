<style>
    input[type=checkbox] {
        -ms-transform: scale(1);
        -moz-transform: scale(1);
        -webkit-transform: scale(1);
        -o-transform: scale(1);
        padding: 10px;
    }

    .card:hover {
        transform: scale(1);
        box-shadow: 0 10px 20px rgba(0, 0, 0, .12), 0 4px 8px rgba(0, 0, 0, .06);
    }
</style>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let data_awal = $('#upload_cms').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('upload_panjarcms.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status_upload == 1 && data.status_validasi == 1) {
                    $(row).css("background-color", "#B0E0E6");
                } else if (data.status_upload == 1) {
                    $(row).css("background-color", "#90EE90");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, {
                    data: 'no_kas',
                    name: 'no_kas'
                },
                {
                    data: 'tgl_kas',
                    name: 'tgl_kas'
                },
                {
                    data: 'tgl_upload',
                    name: 'tgl_upload',
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
                    name: 'bersih',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.bersih)
                    }
                },
                {
                    data: null,
                    name: 'status_upload',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        if (data.status_upload == 1) {
                            return "&#10004";
                        } else {
                            return "X";
                        }
                    }
                },
                {
                    data: null,
                    name: 'aksi',
                    width: 200,
                    render: function(data, type, row, meta) {
                        return `<button type="button" onclick="lihatData('${data.nm_skpd}','${data.no_voucher}','${data.tgl_voucher}','${data.no_sp2d}','${data.ket}','${data.kd_sub_kegiatan}','${data.nm_sub_kegiatan}','${data.kd_skpd}')" class="btn btn-info btn-sm"><i class="fas fa-info-circle"></i></button>`
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

        let data_upload = $('#draft_upload').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            autoWidth: false,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('upload_panjarcms.draft_upload') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'no_upload',
                    name: 'no_upload'
                },
                {
                    data: 'no_upload_tgl',
                    name: 'no_upload_tgl'
                },
                {
                    data: 'no_panjar',
                    name: 'no_panjar',
                },
                {
                    data: 'tgl_panjar',
                    name: 'tgl_panjar'
                },
                {
                    data: 'tgl_upload',
                    name: 'tgl_upload'
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
                    data: null,
                    name: 'status_upload',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        if (data.status_upload == 1) {
                            return "&#10004";
                        } else {
                            return "X";
                        }
                    }
                },
                // {
                //     data: null,
                //     name: 'aksi',
                //     width: 200,
                //     render: function(data, type, row, meta) {
                //         return `<button type="button" onclick="lihatData('${data.nm_skpd}','${data.no_voucher}','${data.tgl_voucher}','${data.no_sp2d}','${data.ket}','${data.kd_sub_kegiatan}','${data.nm_sub_kegiatan}','${data.kd_skpd}')" class="btn btn-info btn-sm"><i class="fas fa-info-circle"></i></button>`
                //     }
                // },
                {
                    data: 'aksi',
                    name: 'aksi'
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

        let transaksi_data = $('#data_transaksi1').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('upload_panjarcms.data_upload') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_upload = document.getElementById('no_upload').value;
                },
                "dataSrc": function(data) {
                    record = data.data;
                    return record;
                }
            },
            ordering: false,
            columns: [{
                    data: 'no_bukti',
                    name: 'no_bukti'
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti'
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
            "drawCallback": function(settings) {
                let total = record.reduce((previousValue,
                    currentValue) => (previousValue += parseFloat(currentValue.nilai)), 0);
                $('#total_transaksi_satuan').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total));
            },
        });

        let rekening_transaksi = $('#rekening_transaksi').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('upload_panjarcms.rekening_transaksi') }}",
                "type": "POST",
                "data": function(d) {
                    d.nomor = document.getElementById('nomor').value;
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                }
            },
            ordering: false,
            columns: [{
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6',
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
                    data: 'sumber',
                    name: 'sumber',
                },
            ]
        });

        let rekening_potongan = $('#rekening_potongan').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('upload_panjarcms.rekening_potongan') }}",
                "type": "POST",
                "data": function(d) {
                    d.nomor = document.getElementById('nomor').value;
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                }
            },
            ordering: false,
            columns: [{
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6',
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
            ]
        });

        let rekening_tujuan = $('#rekening_tujuan').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('upload_panjarcms.rekening_tujuan') }}",
                "type": "POST",
                "data": function(d) {
                    d.nomor = document.getElementById('nomor').value;
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                }
            },
            ordering: false,
            columns: [{
                    data: 'no_voucher',
                    name: 'no_voucher',
                    visible: false
                },
                {
                    data: 'tgl_voucher',
                    name: 'tgl_voucher',
                    visible: false
                },
                {
                    data: 'rekening_awal',
                    name: 'rekening_awal',
                },
                {
                    data: 'nm_rekening_tujuan',
                    name: 'nm_rekening_tujuan',
                },
                {
                    data: 'rekening_tujuan',
                    name: 'rekening_tujuan',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    visible: false
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
                    data: 'bank_tujuan',
                    name: 'bank_tujuan',
                },
            ]
        });

        $('#cetak_cms').on('click', function() {
            let data = JSON.stringify(data_awal.rows().data().toArray());
        });

        $('#cetakCsvKalbar').on('click', function() {
            let no_upload = document.getElementById('no_upload').value;
            let url = new URL("{{ route('upload_panjarcms.cetak_csv_kalbar') }}");
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

    // let data_transaksi1 = $('#data_transaksi1').DataTable({
    //     responsive: true,
    //     processing: true,
    //     ordering: false,
    //     columns: [{
    //             data: 'no_bukti',
    //             name: 'no_bukti'
    //         },
    //         {
    //             data: 'tgl_bukti',
    //             name: 'tgl_bukti'
    //         },
    //         {
    //             data: 'kd_skpd',
    //             name: 'kd_skpd'
    //         },
    //         {
    //             data: 'nilai',
    //             name: 'nilai'
    //         },
    //         {
    //             data: 'rekening_awal',
    //             name: 'rekening_awal',
    //             visible: false
    //         },
    //         {
    //             data: 'nm_rekening_tujuan',
    //             name: 'nm_rekening_tujuan',
    //             visible: false
    //         },
    //         {
    //             data: 'rekening_tujuan',
    //             name: 'rekening_tujuan',
    //             visible: false
    //         },
    //         {
    //             data: 'bank_tujuan',
    //             name: 'bank_tujuan',
    //             visible: false
    //         },
    //         {
    //             data: 'ket_tujuan',
    //             name: 'ket_tujuan',
    //             visible: false
    //         },
    //     ],
    // });

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
            // data_transaksi.row.add({
            //     'no_voucher': no_voucher,
            //     'tgl_voucher': tgl_voucher,
            //     'kd_skpd': kd_skpd,
            //     'ket': ket,
            //     'total': parseFloat(total),
            //     'netto': new Intl.NumberFormat('id-ID', {
            //         minimumFractionDigits: 2
            //     }).format(nilai_bersih),
            //     'pot': new Intl.NumberFormat('id-ID', {
            //         minimumFractionDigits: 2
            //     }).format(pot),
            //     'nilai_total': new Intl.NumberFormat('id-ID', {
            //         minimumFractionDigits: 2
            //     }).format(total),
            //     'status_upload': status_upload,
            //     'rekening_awal': rekening_awal,
            //     'nm_rekening_tujuan': nm_rekening_tujuan,
            //     'rekening_tujuan': rekening_tujuan,
            //     'bank_tujuan': bank_tujuan,
            //     'ket_tujuan': ket_tujuan,
            // }).draw();
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi + nilai));
        } else {
            // data_transaksi.rows(function(idx, data, node) {
            //     return data.no_voucher == no_voucher
            // }).remove().draw();
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi - nilai));
        }

    }

    function batalUpload(no_upload, no_kas, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk membatalkan dengan Nomor Upload : ' + no_upload);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('upload_panjarcms.batal_upload') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_upload: no_upload,
                    no_kas: no_kas,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Proses Batal Berhasil');
                        window.location.href = "{{ route('upload_panjarcms.index') }}";
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
        // $('#total_transaksi_satuan').val(new Intl.NumberFormat('id-ID', {
        //     minimumFractionDigits: 2
        // }).format(total));
        let tabel = $('#data_transaksi1').DataTable();
        tabel.ajax.reload();
        $('#modal_transaksi').modal('show');
    }
</script>
