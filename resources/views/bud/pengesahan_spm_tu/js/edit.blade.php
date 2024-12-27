<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        let status = "{{ $spm->status }}";
        let sts_setuju = "{{ $spm->sts_setuju }}";

        if (status == 1 && sts_setuju == 1) {
            $('#setuju').prop('disabled', true);
            $('#batal_setuju').prop('disabled', true);
        } else if (status == 1 && (sts_setuju == 0 || sts_setuju == '')) {
            $('#setuju').prop('disabled', true);
            $('#batal_setuju').prop('disabled', true);
        } else if ((status == 0 || status == '') && sts_setuju == 1) {
            $('#setuju').prop('disabled', true);
            $('#batal_setuju').prop('disabled', false);
        } else {
            $('#setuju').prop('disabled', false);
            $('#batal_setuju').prop('disabled', true);
        }

        $('#detail_spm').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('pengesahan_spm_tu.detail') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_spp = "{{ $spm->no_spp }}";
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
            let no_spp = "{{ $spm->no_spp }}";
            let kd_skpd = document.getElementById('kd_skpd').value;

            $('#setuju').prop('disabled', true);
            $.ajax({
                url: "{{ route('pengesahan_spm_tu.setuju') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spp: no_spp,
                    kd_skpd: kd_skpd
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('SPM TU Sudah Disetujui');
                        window.location.reload();
                    } else {
                        alert('SPM TU Gagal Disetujui!');
                        $('#setuju').prop('disabled', false);
                    }
                }
            })
        });

        $('#batal_setuju').on('click', function() {
            let no_spm = document.getElementById('no_spm').value;
            let no_spp = "{{ $spm->no_spp }}";

            $('#no_spm_batal').val(no_spm);
            $('#no_spp_batal').val(no_spp);
            $('#spm_batal').modal('show');
        });

        $('#input_batal').on('click', function() {
            let no_spp = "{{ $spm->no_spp }}";
            let beban = "{{ $spm->jns_spp }}";
            let kd_skpd = document.getElementById('kd_skpd').value;
            let keterangan = document.getElementById('keterangan_batal').value;

            $('#batal_setuju').prop('disabled', true);
            $.ajax({
                url: "{{ route('pengesahan_spm_tu.batal_setuju') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spp: no_spp,
                    kd_skpd: kd_skpd,
                    keterangan: keterangan,
                    beban: beban,
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('SPM TU telah dibatalkan');
                        window.location.href = "{{ route('pengesahan_spm_tu.index') }}";;
                    } else {
                        alert('SPM TU Gagal Dibatalkan!');
                        $('#batal_setuju').prop('disabled', false);
                    }
                }
            })
        });
    });
</script>
