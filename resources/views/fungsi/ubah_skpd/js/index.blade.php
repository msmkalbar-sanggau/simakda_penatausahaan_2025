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

        $('#kd_skpd').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);
        });

        $('#simpan').on('click', function() {
            let id = document.getElementById('id').value;
            let username = document.getElementById('username').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            let data = {
                id,
                username,
                kd_skpd
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('ubah_skpd.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        Swal.fire({
                            icon: 'success',
                            title: 'SKPD Berhasil Diubah',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        //alert('SKPD Berhasil Diubah');
                        window.location.href =
                            "{{ route('home') }}";
                    } else {
                        alert('SKPD Gagal Diubah!');
                        $('#simpan').prop('disabled', false);
                        return;
                    }
                }
            })
        });

    });
</script>
