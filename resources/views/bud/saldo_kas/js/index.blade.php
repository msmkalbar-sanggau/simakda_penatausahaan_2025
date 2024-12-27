<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#saldo_kas').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('saldo_kas.load') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'rek_bank',
                    name: 'rek_bank',
                    className: "text-center",
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 200,
                    className: "text-center",
                },
            ],
        });

    });

    function hapus(nomor) {
        let tanya = confirm('Apakah Anda yakin akan hapus Saldo Kas ?');
        if (tanya == true) {
            $.ajax({
                url: "{{ route('saldo_kas.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    nomor: nomor,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil dihapus!');
                        window.location.href = "{{ route('saldo_kas.index') }}";
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
</script>
