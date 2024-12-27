<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let detail = $('#detail_lpj').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('pengesahan_lpj_upgu.detail') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_lpj = document.getElementById('no_lpj').value;
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                },
                "dataSrc": function(data) {
                    recordsTotal = data.data;
                    return recordsTotal;
                },
            },
            lengthMenu: [
                [-1],
                ["All"]
            ],
            ordering: false,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd'
                },
                {
                    data: 'no_bukti',
                    name: 'no_bukti',
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
                    data: 'nm_rek6',
                    name: 'nm_rek6',
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
                }
            ],
            drawCallback: function(select) {
                let total = recordsTotal.reduce((previousValue,
                    currentValue) => (previousValue += parseFloat(currentValue.nilai)), 0);
                $('#total').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total));
            }
        });

        $('#setuju').on('click', function() {
            let no_lpj = document.getElementById('no_lpj').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            $('#setuju').prop('disabled', true);
            $.ajax({
                url: "{{ route('pengesahan_lpj_upgu.setuju') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_lpj: no_lpj,
                    kd_skpd: kd_skpd
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('LPJ Sudah Disetujui');
                        window.location.reload();
                    } else {
                        alert('LPJ Gagal Disetujui!');
                        $('#setuju').prop('disabled', false);
                    }
                }
            })
        });


        $('#batal_setuju').on('click', function() {
            let no_lpj = document.getElementById('no_lpj').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            $('#batal_setuju').prop('disabled', true);
            $.ajax({
                url: "{{ route('pengesahan_lpj_upgu.batal_setuju') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_lpj: no_lpj,
                    kd_skpd: kd_skpd
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('LPJ Sudah Dibatalkan');
                        window.location.reload();
                    } else {
                        alert('LPJ Gagal Dibatalkan!');
                        $('#batal_setuju').prop('disabled', false);
                    }
                }
            })
        });
    });
</script>
