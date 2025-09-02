<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#ttd').select2({
            dropdownParent: $('#modal_cetak'),
            theme: 'bootstrap-5'
        });

        $('#daftar_penguji').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10, 50],
            ajax: {
                "url": "{{ route('daftar_penguji.load_data') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center"
                }, {
                    data: 'no_uji',
                    name: 'no_uji',
                    className: "text-center",
                },
                {
                    data: 'tgl_uji',
                    name: 'tgl_uji',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
                {
                    data: 'no_sp2d',
                    name: 'no_sp2d',
                    visible: false,
                    searchable: true
                }
            ],
            drawCallback: function(settings) {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        // cetak penguji
        $('.cetak_penguji').on('click', function() {
            let no_uji = document.getElementById('no_uji').value;
            let ttd = document.getElementById('ttd').value;
            let jenis_print = $(this).data("jenis");
            if (!ttd) {
                alert('Pilih Penandatangan Terlebih Dahulu!');
                return;
            }
            let url = new URL("{{ route('daftar_penguji.cetak_penguji') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_uji", no_uji);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
    });

    function cetak(no_uji) {
        $('#no_uji').val(no_uji);
        $('#modal_cetak').modal('show');
    }

    function hapusData(no_uji) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor Penguji : ' + no_uji);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('daftar_penguji.hapus_penguji') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_uji: no_uji,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil dihapus!');
                        window.location.reload();
                    } else {
                        alert('Data gagal dihapus!');
                        return;
                    }
                }
            })
        } else {
            return false;
        }
    }

    function siapKirim(no_uji, status) {
        $.ajax({
            url: "{{ route('daftar_penguji.status_bank') }}",
            type: "POST",
            dataType: 'json',
            data: {
                no_uji: no_uji,
                status: status,
            },
            success: function(data) {
                if (data.message == '1') {
                    alert('Daftar penguji berhasil diperbaharui!');
                    window.location.reload();
                } else {
                    alert('Data gagal diperbaharui!');
                    return;
                }
            }
        })
    }
</script>
