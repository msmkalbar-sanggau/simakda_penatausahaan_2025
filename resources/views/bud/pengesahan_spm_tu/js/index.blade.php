<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#pengesahan_spm').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('pengesahan_spm_tu.load') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.sts_setuju == 1) {
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
                    data: 'no_spm',
                    name: 'no_spm',
                    className: "text-center",
                },
                {
                    data: 'tgl_spm',
                    name: 'tgl_spm',
                    className: "text-center",
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: null,
                    name: 'keperluan',
                    render: function(data, type, row, meta) {
                        return data.keperluan.substr(0, 10) + '.....';
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

    function cetak(no_kas, no_sts, kd_skpd) {
        let url = new URL("{{ route('penerimaan_kas.cetak') }}");
        let searchParams = url.searchParams;
        searchParams.append("no_kas", no_kas);
        searchParams.append("no_sts", no_sts);
        searchParams.append("kd_skpd", kd_skpd);
        window.open(url.toString(), "_blank");
    }
</script>
