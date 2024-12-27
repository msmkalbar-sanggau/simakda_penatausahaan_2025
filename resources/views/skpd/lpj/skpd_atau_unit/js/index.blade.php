<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select-modal').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#skpd_atau_unit').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('lpj.skpd_atau_unit.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status == "1" || data.status == "2") {
                    $(row).css("background-color", "#e4b4bb");
                    $(row).css("color", "black");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'no_lpj',
                    name: 'no_lpj',
                    className: "text-center",
                },
                {
                    data: 'tgl_lpj',
                    name: 'tgl_lpj',
                    width: '150px',
                    className: "text-center",
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                },
                {
                    data: null,
                    name: 'keterangan',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return data.keterangan.substr(0, 10) + '.....';
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: '100px',
                    className: "text-center",
                },
            ],
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
                    url: "{{ route('lpj.skpd_atau_unit.sub_kegiatan') }}",
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

        $('.sptb').on('click', function() {
            let no_lpj = document.getElementById('no_lpj').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let jenis_print = $(this).data("jenis");

            if (!pa_kpa) {
                alert("Pengguna Anggaran tidak boleh kosong!");
                return;
            }

            let url = new URL("{{ route('lpj.skpd_atau_unit.cetak_sptb') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_lpj", no_lpj);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.rincian').on('click', function() {
            let no_lpj = document.getElementById('no_lpj').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let bendahara = document.getElementById('bendahara').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let pilihan = document.getElementById('pilihan').value;
            let margin_kiri = document.getElementById('margin_kiri').value;
            let margin_kanan = document.getElementById('margin_kanan').value;
            let margin_atas = document.getElementById('margin_atas').value;
            let margin_bawah = document.getElementById('margin_bawah').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let jenis_print = $(this).data("jenis");


            if (!bendahara) {
                alert("Bendahara Pengeluaran tidak boleh kosong!");
                return;
            }

            if (!pa_kpa) {
                alert("Pengguna Anggaran tidak boleh kosong!");
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

            let url = new URL("{{ route('lpj.skpd_atau_unit.rincian') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_lpj", no_lpj);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pilihan", pilihan);
            searchParams.append("kd_sub_kegiatan", kd_sub_kegiatan);
            searchParams.append("margin_kiri", margin_kiri);
            searchParams.append("margin_kanan", margin_kanan);
            searchParams.append("margin_atas", margin_atas);
            searchParams.append("margin_bawah", margin_bawah);
            searchParams.append("jenis_print", jenis_print);
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

    function cetak(no_lpj, jenis, kd_skpd) {
        $('#no_lpj').val(no_lpj);
        $('#jenis').val(jenis);
        $('#kd_skpd').val(kd_skpd);
        $('#modal_cetak').modal('show');
    }

    function hapus(no_lpj, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor LPJ : ' + no_lpj);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('lpj.skpd_atau_unit.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_lpj: no_lpj,
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
