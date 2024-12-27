<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        let detail = $('#detail_lpj').DataTable({
            responsive: true,
            ordering: false,
            columns: [{
                    data: 'no',
                    name: 'no'
                }, {
                    data: 'kd_skpd',
                    name: 'kd_skpd'
                },
                {
                    data: 'no_bukti',
                    name: 'no_bukti'
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan'
                },
                {
                    data: 'kdrek6',
                    name: 'kdrek6'
                },
                {
                    data: 'nmrek6',
                    name: 'nmrek6',
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
            ]
        });


        $('#setuju').on('click', function() {
            let no_lpj = document.getElementById('no_lpj').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            $('#setuju').prop('disabled', true);
            $.ajax({
                url: "{{ route('lpj.validasi.setuju') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_lpj: no_lpj,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('LPJ telah disetujui');
                        window.location.reload();
                    } else {
                        alert('LPJ gagal disetujui!');
                        $('#setuju').prop('disabled', false);
                    }
                }
            });
        });

        $('#batal_setuju').on('click', function() {
            let no_lpj = document.getElementById('no_lpj').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            $('#batal_setuju').prop('disabled', true);
            $.ajax({
                url: "{{ route('lpj.validasi.batal_setuju') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_lpj: no_lpj,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('LPJ telah dibatalkan');
                        window.location.reload();
                    } else if (data.message == '2') {
                        alert('LPJ UNIT TELAH DIBUAT LPJ GLOBAL!');
                        $('#batal_setuju').prop('disabled', false);
                    } else {
                        alert('LPJ gagal dibatalkan!');
                        $('#batal_setuju').prop('disabled', false);
                    }
                }
            });
        });
    });
</script>
