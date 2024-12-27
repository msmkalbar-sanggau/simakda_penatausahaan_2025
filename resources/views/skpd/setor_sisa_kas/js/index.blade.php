<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#ttdb').select2({
            dropdownParent: $('#modalcetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#ttda').select2({
            dropdownParent: $('#modalcetak .modal-content'),
            theme: 'bootstrap-5'
        });

        //ini buat klo di klik ttd otomatis nampilin nama
        $('#ttda').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_bud').val(nama);
        });

        let sisa_kas = $('#sisa_kas').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('skpd.setor_sisa.load_data') }}",
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
                    className: 'text-right',
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

    function hapusSetor(no_sts, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Kas : ' + no_sts);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('skpd.setor_sisa.hapus') }}",
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

    // function cetakcp(kd_skpd, nm_skpd) {
    //     $('#kd_skpd').val(kd_skpd);
    //     $('#nm_skpd').val(nm_skpd);
    //     $('#modalcetak').modal('show');
    // }

    $('#cetakcp').on('click', function() {
        // $('#kd_skpd').val(kd_skpd);
        // $('#nm_skpd').val(nm_skpd);
        $('#modalcetak').modal('show');
    });

    // cetak permintaan layar
    $('.permintaan_layar').on('click', function() {
        let kd_skpd = document.getElementById('kd_skpd').value;
        let nm_skpd = document.getElementById('nm_skpd').value;
        let tgl1 = document.getElementById('tgl1').value;
        let tgl2 = document.getElementById('tgl2').value;
        let tgl_ttd = document.getElementById('tgl_ttd').value;
        let ttdb = document.getElementById('ttdb').value;
        let ttda = document.getElementById('ttda').value;
        let jenis_print = $(this).data("jenis");
        let tanpa;
        if (!tgl1) {
            alert('Tanggal 1 tidak boleh kosong!');
            return;
        }
        if (!tgl2) {
            alert('Tanggal 2 tidak boleh kosong!');
            return;
        }
        if (!tgl_ttd) {
            alert('Tanggal TTD tidak boleh kosong!');
            return;
        }
        if (!ttdb) {
            alert('Bendahara Pengeluaran tidak boleh kosong!');
            return;
        }
        if (!ttda) {
            alert('Pengguna Anggaran tidak boleh kosong!');
            return;
        }
        let url = new URL("{{ route('skpd.cetak_registerCP') }}");
        let searchParams = url.searchParams;
        searchParams.append("kd_skpd", kd_skpd);
        searchParams.append("nm_skpd", nm_skpd);
        searchParams.append("tgl1", tgl1);
        searchParams.append("tgl2", tgl2);
        searchParams.append("tgl_ttd", tgl_ttd);
        searchParams.append("ttdb", ttdb);
        searchParams.append("ttda", ttda);
        searchParams.append("jenis_print", jenis_print);
        window.open(url.toString(), "_blank");
    });
</script>
