<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#pengesahan_lpj').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('pengesahan_lpj_upgu.load') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status == 1 || data.status == 2) {
                    $(row).css("background-color", "#4bbe68");
                    $(row).css("color", "white");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                },
                {
                    data: 'no_lpj',
                    name: 'no_lpj',
                    className: "text-center",
                },
                {
                    data: 'tgl_lpj',
                    name: 'tgl_lpj',
                    className: "text-center",
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                    className: "text-center",
                },
                {
                    data: null,
                    name: 'keterangan',
                    render: function(data, type, row, meta) {
                        return data.keterangan.substr(0, 10) + '.....';
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

        $('.select-modal').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#pilihan').on('select2:select', function() {
            let pilihan = this.value;
            if (pilihan != '2') {
                $('#kd_sub_kegiatan').val(null).change();
                $('#kd_sub_kegiatan').prop('disabled', true);
            } else {
                $('#kd_sub_kegiatan').val(null).change();
                $('#kd_sub_kegiatan').prop('disabled', false);

                // CARI KODE SUB KEGIATAN
                $.ajax({
                    type: "POST",
                    url: "{{ route('pengesahan_lpj_upgu.kegiatan') }}",
                    dataType: 'json',
                    data: {
                        no_lpj: document.getElementById('no_lpj').value
                    },
                    success: function(data) {
                        $('#kd_sub_kegiatan').empty();
                        $('#kd_sub_kegiatan').append(
                            `<option value="" disabled selected>Silahkan Pilih</option>`
                        );
                        $.each(data, function(index, data) {
                            $('#kd_sub_kegiatan').append(
                                `<option value="${data.kd_sub_kegiatan}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                            );
                        })
                    }
                });
            }
        });

        $('.cetak').on('click', function() {
            let no_lpj = document.getElementById('no_lpj').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let ttd = document.getElementById('ttd').value;
            let pilihan = document.getElementById('pilihan').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let jenis_print = $(this).data("jenis");

            if (!ttd) {
                alert("Penandatangan tidak boleh kosong!");
                return;
            }

            if (!pilihan) {
                alert('Silahkan diisi Pilihan!');
                return;
            }

            if (pilihan == '2' && !kd_sub_kegiatan) {
                alert('Pilihan Rincian Perkegiatan, Sub Kegiatan wajib dipilih!');
                return;
            }

            let url = new URL("{{ route('pengesahan_lpj_upgu.cetak') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_lpj", no_lpj);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("ttd", ttd);
            searchParams.append("pilihan", pilihan);
            searchParams.append("kd_sub_kegiatan", kd_sub_kegiatan);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
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

    function cetak(no_lpj, kd_skpd) {
        $('#no_lpj').val(no_lpj);
        $('#kd_skpd').val(kd_skpd);
        $('#modal_cetak').modal('show');
    }
</script>
