<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#kendali_proteksi').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('kendali_proteksi_lpj.load') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
            ],
        });

        $('#simpan').on('click', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let status_lpj = document.getElementById('status_lpj').checked;

            if (!kd_skpd) {
                alert('Maaf!!! SKPD Belum dipilih.');
                return;
            }

            let status = 0;
            if (status_lpj == true) {
                status = 1;
            } else {
                status = 0;
            }

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('kendali_proteksi_lpj.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    nm_skpd: nm_skpd,
                    status: status,
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data Berhasil disimpan');
                        window.location.reload();
                    } else {
                        alert('Data Gagal disimpan');
                        $('#simpan').prop('disabled', false);
                    }
                }
            })
        });
    });

    function hapus(no_kas, no_sts, kd_skpd, tgl_kas) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Kas : ' + no_kas);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('penerimaan_kas.kunci_kasda') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (tgl_kas < data) {
                        alert('Tanggal kas lebih kecil dari tanggal kuncian!');
                        return;
                    } else {
                        $.ajax({
                            url: "{{ route('penerimaan_kas.hapus') }}",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                no_kas: no_kas,
                                no_sts: no_sts,
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
                    }
                }
            })
        } else {
            return false;
        }
    }

    function proteksi(kd_skpd, nm_skpd, status) {
        $('#kd_skpd').val(kd_skpd);
        $('#nm_skpd').val(nm_skpd);
        if (status == '1') {
            $('#status_lpj').prop('checked', true);
        } else {
            $('#status_lpj').prop('checked', false);
        }
        $('#proteksi').modal('show');
    }
</script>
