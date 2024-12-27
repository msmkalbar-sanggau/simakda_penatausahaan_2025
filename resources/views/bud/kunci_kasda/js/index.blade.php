<style>
    .select2-results span[onlyslave="True"] {
        color: red
    }
</style>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function formatState(state) {
            if (!state.element) return;
            var os = $(state.element).attr('onlyslave');
            return $('<span onlyslave="' + os + '">' + state.text + '</span>');
        }

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5',
            templateResult: formatState
        });

        $('#skpd').hide();

        $('#keseluruhan').on('click', function() {
            $('#skpd').hide();
        });

        $('#per_skpd').on('click', function() {
            $('#skpd').show();
        });

        $('#kd_skpd').on('select2:select', function() {
            let tgl = $(this).find(':selected').data('tgl');
            $('#tgl_kunci').val(tgl);
        });

        $('#simpan').on('click', function() {
            let keseluruhan = document.getElementById('keseluruhan')
                .checked;
            let skpd = document.getElementById('per_skpd').checked;

            if (keseluruhan == false && skpd == false) {
                alert('Silahkan Pilih Keseluruhan atau SKPD!');
                return;
            }

            let kd_skpd = document.getElementById('kd_skpd').value;
            let tgl_kunci = document.getElementById('tgl_kunci').value;
            let tgl_akhir = document.getElementById('tgl_akhir').value;

            if (!tgl_akhir) {
                alert('Silahkan Pilih Tanggal Akhir!');
                return;
            }

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
                if (tgl_kunci < tgl_akhir) {
                    alert('Tanggal Akhir Lebih Besar dari Tanggal Kunci');
                    return;
                }
            }

            let pilihan = '';
            if (keseluruhan) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            }

            $.ajax({
                url: "{{ route('kunci_kasda.kunci') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    pilihan: pilihan,
                    kd_skpd: kd_skpd,
                    tgl_akhir: tgl_akhir,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil Terkunci');
                        window.location.reload();
                    } else {
                        alert('Data gagal Terkunci');
                        return;
                    }
                }
            });
        })
    });
</script>
