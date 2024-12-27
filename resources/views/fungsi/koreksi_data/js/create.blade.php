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

        $('#pilih_sp2d').show();
        $('#pilih_spm').hide();

        $('#sp2d').on('click', function() {
            $('#pilih_sp2d').show();
            $('#pilih_spm').hide();

            $('#no_spm').val(null).change();
        });

        $('#spm').on('click', function() {
            $('#pilih_sp2d').hide();
            $('#pilih_spm').show();

            $('#no_sp2d').val(null).change();
        });

        $('#kd_skpd').on('change', function() {
            $.ajax({
                url: "{{ route('koreksi_data.sp2d') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: this.value,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    $('#no_sp2d').empty();
                    $('#no_sp2d').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data.sp2d, function(index, sp2d) {
                        $('#no_sp2d').append(
                            `<option value="${sp2d.no_sp2d}" data-keperluan="${sp2d.keperluan}" data-tgl_mulai="${spm.tgl_mulai}" data-tgl_akhir="${spm.tgl_akhir}" data-jenis="${spm.jenis}">${sp2d.no_sp2d}</option>`
                        );
                    });

                    $('#no_spm').empty();
                    $('#no_spm').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data.spm, function(index, spm) {
                        $('#no_spm').append(
                            `<option value="${spm.no_spm}" data-keperluan="${spm.keperluan}" data-tgl_mulai="${spm.tgl_mulai}" data-tgl_akhir="${spm.tgl_akhir}" data-jenis="${spm.jenis}">${spm.no_spm}</option>`
                        );
                    });
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        });

        $('#no_sp2d').on('change', function() {
            let keperluan = $(this).find(':selected').data('keperluan');
            let tgl_mulai = $(this).find(':selected').data('tgl_mulai');
            let tgl_akhir = $(this).find(':selected').data('tgl_akhir');
            let jenis = $(this).find(':selected').data('jenis');

            let data_jenis = jenis == '' ? ' ' : jenis;

            $("#keterangan").val(keperluan);
            $("#tgl_mulai").val(tgl_mulai);
            $("#tgl_akhir").val(tgl_akhir);
            $("#jenis").val(data_jenis).change();
        });

        $('#no_spm').on('change', function() {
            let keperluan = $(this).find(':selected').data('keperluan');
            let tgl_mulai = $(this).find(':selected').data('tgl_mulai');
            let tgl_akhir = $(this).find(':selected').data('tgl_akhir');
            let jenis = $(this).find(':selected').data('jenis');

            let data_jenis = jenis == '' ? ' ' : jenis;

            $("#keterangan").val(keperluan);
            $("#tgl_mulai").val(tgl_mulai);
            $("#tgl_akhir").val(tgl_akhir);
            $("#jenis").val(data_jenis).change();
        });

        $('#simpan').on('click', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let no_spm = document.getElementById('no_spm').value;
            let tgl_mulai = document.getElementById('tgl_mulai').value;
            let tgl_akhir = document.getElementById('tgl_akhir').value;
            let jenis = document.getElementById('jenis').value;
            let keterangan = document.getElementById('keterangan').value;

            let sp2d = document.getElementById('sp2d').checked;
            let spm = document.getElementById('spm').checked;

            if (!kd_skpd) {
                alert('SKPD Tidak Boleh Kosong');
                return;
            }

            if (sp2d == true && !no_sp2d) {
                alert('Silahkan pilih SP2D!!!');
                return;
            }

            if (spm == true && !no_spm) {
                alert('Silahkan pilih SPM!!!');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }

            let pilihan = '';

            if (sp2d) {
                pilihan = '1';
            } else if (spm) {
                pilihan = '2';
            }

            let data = {
                pilihan,
                kd_skpd,
                no_sp2d,
                no_spm,
                tgl_mulai,
                tgl_akhir,
                jenis,
                keterangan
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('koreksi_data.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil disimpan!');
                        $('#simpan').prop('disabled', false);

                        $('#kd_skpd').val(null).change();
                        $('#no_sp2d').val(null).change();
                        $('#no_spm').val(null).change();
                        $('#tgl_mulai').val(null);
                        $('#tgl_akhir').val(null);
                        $('#jenis').val(null).change();
                        $('#keterangan').val(null);
                    } else {
                        alert('Data gagal disimpan!');
                        $('#simpan').prop('disabled', false);
                    }
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        });
    });
</script>
