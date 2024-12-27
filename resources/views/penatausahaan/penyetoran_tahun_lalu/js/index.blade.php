<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#penyetoran_lalu').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('penyetoran_lalu.load_data') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'no_sts',
                    name: 'no_sts',
                    className: "text-center",
                },
                {
                    data: 'tgl_sts',
                    name: 'tgl_sts',
                    className: "text-center",
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
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
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
            ],
        });

        $('#cek').on('click', function() {
            let tgl_awal = document.getElementById('tgl_awal').value;
            let tgl_akhir = document.getElementById('tgl_akhir').value;

            if (!tgl_awal || !tgl_akhir) {
                alert('Silahkan Pilih Tanggal');
                return;
            }

            if (tgl_akhir < tgl_awal) {
                alert('Tanggal akhir tidak boleh kecil dari tanggal awal!');
                return;
            }

            let url = new URL("{{ route('penyetoran_lalu.cek') }}");
            let searchParams = url.searchParams;
            searchParams.append("tgl_awal", tgl_awal);
            searchParams.append("tgl_akhir", tgl_akhir);
            window.open(url.toString(), "_blank");
        });

        $('#validasi').on('click', function() {
            let tgl_awal = document.getElementById('tgl_awal').value;
            let tgl_akhir = document.getElementById('tgl_akhir').value;

            if (!tgl_awal || !tgl_akhir) {
                alert('Silahkan Pilih Tanggal');
                return;
            }

            if (tgl_akhir < tgl_awal) {
                alert('Tanggal akhir tidak boleh kecil dari tanggal awal!');
                return;
            }
            let tny = confirm('Tgl STS ' + tgl_awal + ' s/d ' + tgl_akhir +
                ' akan dikirim ke Kasda, Apakah Anda yakin ?');
            if (tny == true) {
                $.ajax({
                    url: "{{ route('penyetoran_lalu.validasi') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        tgl_awal: tgl_awal,
                        tgl_akhir: tgl_akhir,
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('Data berhasil divalidasi');
                            window.location.href = "{{ route('penyetoran_lalu.index') }}";
                        } else if (data.message == '2 ') {
                            alert('Hanya Untuk UPPD BPKPD!');
                            return;
                        } else {
                            alert('Data gagal divalidasi!');
                            return;
                        }
                    }
                })
            }
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

    function hapus(no_sts, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Penyetoran : ' + no_sts);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('penyetoran_lalu.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
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
        } else {
            return false;
        }
    }
</script>
