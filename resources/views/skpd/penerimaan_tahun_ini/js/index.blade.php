<style>
    table.dataTable tbody tr td {
        font-size: 11px
    }
</style>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#penerimaan_tahun_ini').DataTable({
            // responsive: true,
            scrollX: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 20],
            ajax: {
                "url": "{{ route('penerimaan_ini.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.kunci == 1) {
                    $(row).css("background-color", "#4bbe68");
                    $(row).css("color", "white");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'no_terima',
                    name: 'no_terima',
                    width: '10px'
                    // className: "text-center",
                },
                {
                    data: 'tgl_terima',
                    name: 'tgl_terima',
                    className: "text-center",
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                    className: "text-center",
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
                    name: 'simbol',
                    className: 'text-center',
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
                    width: '200px',
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

    function hapus(no_terima, no_tetap, kd_skpd) {
        // let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Terima : ' + no_terima);
        // if (tanya == true) {
        //     let tanya1 = confirm('Apakah anda ingin menghapus hanya penerimaan atau penerimaan dengan penetapan?');
        //     if (tanya1 == true) {
        //         $.ajax({
        //             url: "{{ route('penerimaan_ini.hapus') }}",
        //             type: "POST",
        //             dataType: 'json',
        //             data: {
        //                 no_terima: no_terima,
        //                 kd_skpd: kd_skpd,
        //             },
        //             success: function(data) {
        //                 if (data.message == '1') {
        //                     alert('Proses Hapus Berhasil');
        //                     window.location.reload();
        //                 } else {
        //                     alert('Proses Hapus Gagal...!!!');
        //                 }
        //             }
        //         })
        //     } else {
        //         return false;
        //     }
        // } else {
        //     return false;
        // }
        Swal.fire({
            title: 'Apakah Anda yakin untuk menghapus data?',
            text: "Data tidak dapat dikembalikan!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya!',
            cancelButtonText: 'Tidak',
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                Swal.fire({
                    title: 'Apakah hanya menghapus penerimaan atau penerimaan dengan penetapan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hanya Penerimaan?',
                    cancelButtonText: 'Penerimaan Dengan Penetapan?',
                    confirmButtonClass: 'btn btn-warning mt-2',
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('penerimaan_ini.hapus') }}",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                no_terima: no_terima,
                                no_tetap: no_tetap,
                                kd_skpd: kd_skpd,
                                jenis: '1'
                            },
                            beforeSend: function() {
                                $("#overlay").fadeIn(100);
                            },
                            success: function(data) {
                                if (data.message == '1') {
                                    alert('Proses Hapus Berhasil');
                                    window.location.reload();
                                } else {
                                    alert('Proses Hapus Gagal...!!!');
                                }
                            },
                            complete: function(data) {
                                $("#overlay").fadeOut(100);
                            }
                        })
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        $.ajax({
                            url: "{{ route('penerimaan_ini.hapus') }}",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                no_terima: no_terima,
                                no_tetap: no_tetap,
                                kd_skpd: kd_skpd,
                                jenis: '2'
                            },
                            beforeSend: function() {
                                $("#overlay").fadeIn(100);
                            },
                            success: function(data) {
                                if (data.message == '1') {
                                    alert('Proses Hapus Berhasil');
                                    window.location.reload();
                                } else {
                                    alert('Proses Hapus Gagal...!!!');
                                }
                            },
                            complete: function(data) {
                                $("#overlay").fadeOut(100);
                            }
                        })
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Hapus Dibatalkan!',
                    icon: 'info'
                })
            }
        });
    }
</script>
