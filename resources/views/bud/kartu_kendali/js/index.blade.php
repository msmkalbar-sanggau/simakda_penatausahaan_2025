<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2-multiple').select2({
            theme: 'bootstrap-5'
        });

        $('#kd_skpd').on('select2:select', function() {
            let kd_skpd = this.value;
            let nm_skpd = $(this).find(':selected').data('nama');

            $('#nm_skpd').val(nm_skpd);

            $('#kd_sub_kegiatan').empty();
            $('#nm_sub_kegiatan').val(null);
            $('#kd_rek').empty();
            $('#nm_rek').val(null);

            $.ajax({
                url: "{{ route('kartu_kendali.kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            })
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            let kd_sub_kegiatan = this.value;
            let nama = $(this).find(':selected').data('nama');

            $('#nm_sub_kegiatan').val(nama);

            $('#kd_rek').empty();
            $('#nm_rek').val(null);

            $.ajax({
                url: "{{ route('kartu_kendali.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: document.getElementById('kd_skpd').value,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                },
                success: function(data) {
                    $('#kd_rek').empty();
                    $('#kd_rek').append(
                        `<option value="">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_rek').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            })
        });

        $('#kd_rek').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');

            $('#nm_rek').val(nama);
        });

        $('.cetak_kegiatan').on('click', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let periode_awal = document.getElementById('periode_awal').value;
            let periode_akhir = document.getElementById('periode_akhir').value;
            let ttd = document.getElementById('ttd').value;
            let jns_ang = document.getElementById('jns_ang').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kd_rek = document.getElementById('kd_rek').value;

            let jenis_print = $(this).data("jenis");

            if (!kd_skpd) {
                alert('Pilih SKPD Terlebih Dahulu!');
                return;
            }
            if (!kd_sub_kegiatan) {
                alert("Pilih Kegiatan Terlebih Dahulu!");
                return;
            }
            if (!kd_rek) {
                alert("Pilih Rekening Terlebih Dahulu!");
                return;
            }
            if (!jns_ang) {
                alert("Pilih Jenis Anggaran Terlebih Dahulu!");
                return;
            }
            if (!periode_awal || !periode_akhir) {
                alert("Pilih Periode Terlebih Dahulu!");
                return;
            }

            let url = new URL("{{ route('kartu_kendali.cetak_kegiatan') }}");
            let searchParams = url.searchParams;
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("kd_sub_kegiatan", kd_sub_kegiatan);
            searchParams.append("kd_rek", kd_rek);
            searchParams.append("jns_ang", jns_ang);
            searchParams.append("periode_awal", periode_awal);
            searchParams.append("periode_akhir", periode_akhir);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.cetak_rekening').on('click', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let periode_awal = document.getElementById('periode_awal').value;
            let periode_akhir = document.getElementById('periode_akhir').value;
            let ttd = document.getElementById('ttd').value;
            let jns_ang = document.getElementById('jns_ang').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kd_rek = document.getElementById('kd_rek').value;

            let jenis_print = $(this).data("jenis");

            if (!kd_skpd) {
                alert('Pilih SKPD Terlebih Dahulu!');
                return;
            }
            if (!kd_sub_kegiatan) {
                alert("Pilih Kegiatan Terlebih Dahulu!");
                return;
            }
            if (!kd_rek) {
                alert("Pilih Rekening Terlebih Dahulu!");
                return;
            }
            if (!jns_ang) {
                alert("Pilih Jenis Anggaran Terlebih Dahulu!");
                return;
            }
            if (!periode_awal || !periode_akhir) {
                alert("Pilih Periode Terlebih Dahulu!");
                return;
            }

            let url = new URL("{{ route('kartu_kendali.cetak_rekening') }}");
            let searchParams = url.searchParams;
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("kd_sub_kegiatan", kd_sub_kegiatan);
            searchParams.append("kd_rek", kd_rek);
            searchParams.append("jns_ang", jns_ang);
            searchParams.append("periode_awal", periode_awal);
            searchParams.append("periode_akhir", periode_akhir);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
    });
</script>
