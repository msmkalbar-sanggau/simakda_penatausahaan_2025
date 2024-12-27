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

        $('#kd_skpd').on('select2:select', function() {
            $('#kode_rekening').empty();
            let kd_skpd = this.value;

            // CARI KODE SUB KEGIATAN
            $.ajax({
                url: "{{ route('kunci_belanja.kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            })
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kd_sub_kegiatan = this.value;

            // CARI KODE SUB KEGIATAN
            $.ajax({
                url: "{{ route('kunci_belanja.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                },
                success: function(data) {
                    $('#kode_rekening').empty();
                    $('#kode_rekening').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        if (data.status_aktif == 1) {
                            $('#kode_rekening').append(
                                `<option value="${data.kd_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                            );
                        } else {
                            $('#kode_rekening').append(
                                `<option onlyslave="True" value="${data.kd_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                            );
                        }
                    })
                }
            })
        });

        $('#pilihan').on('click', function() {
            $('#modal_pilihan').modal('show');
        });

        $('.kunci').on('click', function() {
            let jenis = $(this).data("jenis");
            let kd_skpd = $('#kd_skpd').val();
            let kd_sub_kegiatan = $('#kd_sub_kegiatan').val();
            let kode_rekening = $('#kode_rekening').val();

            if (jenis == 'aktifkan_skpd' || jenis == 'nonaktifkan_skpd') {
                if (!kd_skpd) {
                    alert('Pilih SKPD terlebih dahulu');
                    return;
                }
            }

            if (jenis == 'aktifkan_kegiatan' || jenis == 'nonaktifkan_kegiatan') {
                if (!kd_skpd) {
                    alert('Pilih SKPD terlebih dahulu');
                    return;
                }
                if (!kd_sub_kegiatan) {
                    alert('Pilih Sub Kegiatan terlebih dahulu');
                    return;
                }
            }

            if (jenis == 'aktifkan_rekening' || jenis == 'nonaktifkan_rekening') {
                if (!kd_skpd) {
                    alert('Pilih SKPD terlebih dahulu');
                    return;
                }
                if (!kd_sub_kegiatan) {
                    alert('Pilih Sub Kegiatan terlebih dahulu');
                    return;
                }
                if (!kode_rekening) {
                    alert('Pilih Kode Rekening terlebih dahulu');
                    return;
                }
            }

            $.ajax({
                url: "{{ route('kunci_belanja.kunci') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    kode_rekening: kode_rekening,
                    jenis: jenis,
                },
                success: function(data) {
                    if (data.message == 1) {
                        alert('Berhasil');
                        $('#kd_sub_kegiatan').val(null).change();
                        $('#kode_rekening').empty();
                        $('#modal_pilihan').modal('hide');
                    }
                }
            });
        });
    });
</script>
