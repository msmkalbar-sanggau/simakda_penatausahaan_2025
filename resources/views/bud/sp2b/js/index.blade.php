<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#bud').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#bud').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_bud').val(nama);
        });

        $('#sp2b').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('sp2b.load_data') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'no_sp2b',
                    name: 'no_sp2b',
                },
                {
                    data: 'tgl_sp2b',
                    name: 'tgl_sp2b',
                    className: "text-center",
                },
                {
                    data: 'skpd',
                    name: 'skpd',
                    className: "text-center",
                },
                // {
                //     data: 'no_sp3b',
                //     name: 'no_sp3b',
                // },
                // {
                //     data: 'tgl_sp3b',
                //     name: 'tgl_sp3b',
                //     className: "text-center",
                // },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 200,
                    className: "text-center",
                },
            ],
        });
    });

    function hapus(no_sp2b, nosp2b) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor SP2B : ' + no_sp2b);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('sp2b.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sp2b: no_sp2b,
                    nosp2b: nosp2b,
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

    function cetak(no_sp2b, kd_skpd) {
        $('#no_sp2b').val(no_sp2b);
        $('#kd_skpd').val(kd_skpd);
        $('#modal_cetak').modal('show');
    }

    // cetak permintaan layar
    $('.permintaan_layar').on('click', function() {
        let no_sp2b = document.getElementById('no_sp2b').value;
        let kd_skpd = document.getElementById('kd_skpd').value;
        let bud = document.getElementById('bud').value;
        let jenis_print = $(this).data("jenis");
        let tanpa;
        if (!bud) {
            alert('Kuasa BUD tidak boleh kosong!');
            return;
        }
        let url = new URL("{{ route('sp2b.cetak_permintaan') }}");
        let searchParams = url.searchParams;
        searchParams.append("no_sp2b", no_sp2b);
        searchParams.append("kd_skpd", kd_skpd);
        searchParams.append("bud", bud);
        searchParams.append("tanpa", tanpa);
        searchParams.append("jenis_print", jenis_print);
        window.open(url.toString(), "_blank");
    });
</script>
