<style>
    .text-right {
        text-align: right;
    }
</style>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('spm.total_show') }}",
            type: "POST",
            dataType: 'json',
            data: {
                no_spp: document.getElementById('no_spp').value
            },
            success: function(data) {
                $('#total').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data));
            }
        });
        let tabel = $('#rincian_spm').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('spm.load_rincian_show') }}",
                "type": "POST",
                "data": {
                    "no_spp": document.getElementById('no_spp').value
                }
            },
            columns: [{
                    data: 'kd_sub_kegiatan'
                },
                {
                    data: 'kd_rek6'
                },
                {
                    data: 'nm_rek6'
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
            ]
        });

        function rupiah(n) {
            let n1 = n.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }

    });
</script>
