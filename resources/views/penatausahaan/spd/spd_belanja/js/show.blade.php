<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let data_awal = $('#spd_belanja').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 50],
            ajax: {
                "url": "{{ route('spd.spd_belanja.show_load_data') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_spd = document.getElementById('nomor').value;
                },
            },
            columns: [{
                    data: 'kd_unit',
                    name: 'kd_unit',
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: null,
                    name: 'nilai',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
                    },
                    "className": "text-right",
                },
            ],
        });

    });
</script>