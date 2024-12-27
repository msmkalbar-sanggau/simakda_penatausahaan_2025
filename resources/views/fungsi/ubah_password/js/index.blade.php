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




        $('#simpan').on('click', function() {
            URL = window.location.href.split('/');
            let id = URL[URL.length - 1];
            // let id = document.getElementById('id').value;
            // let username = document.getElementById('username').value;
            let password_lama = document.getElementById('password_lama').value;
            let password = document.getElementById('password').value;
            let password2 = document.getElementById('password2').value;

            if (password.length < 8) {
                alert('Password minimal 8 karakter!');
                return
            }
            let lowerCaseLetters = /[a-z]/g;
            let upperCaseLetters = /[A-Z]/g;
            let numbers = /[0-9]/g;
            if (!password.match(lowerCaseLetters)) {
                alert('Password minimal ada huruf kecil!');
                return;
            }
            if (!password.match(upperCaseLetters)) {
                alert('Password minimal ada huruf besar!');
                return;
            }
            if (!password.match(numbers)) {
                alert('Password minimal angka!');
                return;
            }

            let data = {
                // id,
                id,
                // username,
                password_lama,
                password,
                password2
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('ubah_password.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Password Berhasil Diubah');
                        window.location.href =
                            "{{ route('home') }}";
                    } else if (response.message == '2') {
                        alert('Password tidak sama!');
                        $('#simpan').prop('disabled', false);
                        return;
                    } else if (response.message == '3') {
                        alert('Password lama tidak sesuai!');
                        $('#simpan').prop('disabled', false);
                        return;
                    } else if (response.message == '4') {
                        alert('Password tidak dapat diubah! Bukan akun Anda!');
                        $('#simpan').prop('disabled', false);
                        return;
                    } else {
                        alert('Password Gagal Diubah!');
                        $('#simpan').prop('disabled', false);
                        return;
                    }
                }
            })
        });

    });
</script>
