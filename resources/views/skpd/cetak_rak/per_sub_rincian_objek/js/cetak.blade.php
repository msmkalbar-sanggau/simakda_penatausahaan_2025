<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#ttd1').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });
        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });
        // document.getElementById('baris_subkegiatan').hidden = true;
        $('#kd_skpd').on('select2:select', function() {
            let kd_skpd = this.value;
            let nama = $(this).find(':selected').data('nama');
            cari_ttd_skpd(kd_skpd);
            // Cari Jenis Anggaran
            $.ajax({
                url: "{{ route('skpd.input_rak.jenis_anggaran') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $('#jenis_anggaran').empty();
                    $('#jenis_anggaran').append(
                        `<option value="">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#jenis_anggaran').append(
                            `<option value="${data.kode}">${data.nama}</option>`
                        );
                    })
                }
            })
        });

        $('input:radio[name="inlineRadioOptions"]').change(function() {
            if ($(this).val() == 'keseluruhan') {
                document.getElementById('baris_subkegiatan').hidden = true; // Hide
            } else {
                document.getElementById('baris_subkegiatan').hidden = false; // show
            }
        });

        function cari_ttd_skpd(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.cetak_rak.ttdskpd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#ttd1').empty();
                    $('#ttd1').append(
                        `<option value="" disabled selected>Pilih penandatangan</option>`);
                    $.each(data, function(index, data) {
                        $('#ttd1').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }
        $('#jenis_anggaran').on('select2:select', function() {
            let jns_ang = this.value;

            // Kosongkan
            $('#jenis_rak').empty();
            $('#kd_sub_kegiatan').empty();
            $('#nm_sub_kegiatan').val(null);
            document.getElementById("demo").innerHTML = '';

            // Cari Jenis RAK
            $.ajax({
                url: "{{ route('skpd.input_rak.jenis_rak') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jns_ang: jns_ang
                },
                success: function(data) {
                    $('#jenis_rak').empty();
                    $('#jenis_rak').append(
                        `<option value="">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#jenis_rak').append(
                            `<option value="${data.kode}">${data.nama}</option>`
                        );
                    })
                }
            })
        });

        $('#jenis_rak').on('select2:select', function() {
            let jenis_rak = this.value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jns_ang = document.getElementById('jenis_anggaran').value;

            // Kosongkan
            $('#kd_sub_kegiatan').empty();
            $('#nm_sub_kegiatan').val(null);
            document.getElementById("demo").innerHTML = '';

            // Cari Sub Kegiatan
            $.ajax({
                url: "{{ route('skpd.input_rak.sub_kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jns_ang: jns_ang,
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}" data-total="${data.total}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            })
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            let kd_sub_kegiatan = this.value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jenis_anggaran = document.getElementById('jenis_anggaran').value;
            let jenis_rak = document.getElementById('jenis_rak').value;
            let nama = $(this).find(':selected').data('nama');
            $('#nm_sub_kegiatan').val(nama);
            let hidden = 'hidden';

            if (!kd_sub_kegiatan || !kd_skpd || !jenis_anggaran || !jenis_rak) {
                alert('Silahkan Lengkapi Inputan');
                return;
            } else {
                let url = new URL("{{ route('skpd.cetak_rak.cetak_objek') }}");
                let searchParams = url.searchParams;
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("jenis_anggaran", jenis_anggaran);
                searchParams.append("jenis_rak", jenis_rak);
                searchParams.append("kd_sub_kegiatan", kd_sub_kegiatan);
                searchParams.append("hidden", hidden);

                document.getElementById("demo").innerHTML = "<embed src=" + url +
                    " width='100%' height='500px'></embed>";
            }
        });

        $('#ttd1').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_ttd1').val(nama);
        });

        $('#ttd2').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_ttd2').val(nama);
        });

        $('.cetak_rak').on('click', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jenis_anggaran = document.getElementById('jenis_anggaran').value;
            let jenis_rak = document.getElementById('jenis_rak').value;
            let ttd1 = document.getElementById('ttd1').value;
            let ttd2 = document.getElementById('ttd2').value;
            let tanggal_ttd = document.getElementById('tanggal_ttd').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let margin = document.getElementById('margin').value;
            let jenis_print = $(this).data("jenis");

            if (!kd_skpd || !ttd1 || !ttd2 || !tanggal_ttd) {
                alert("Harap Lengkapi Inputan.");
                return;
            }

            let url = new URL("{{ route('skpd.cetak_rak.cetak_objek') }}");
            let searchParams = url.searchParams;
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("jenis_anggaran", jenis_anggaran);
            searchParams.append("jenis_rak", jenis_rak);
            searchParams.append("kd_sub_kegiatan", kd_sub_kegiatan);
            searchParams.append("ttd1", ttd1);
            searchParams.append("ttd2", ttd2);
            searchParams.append("tanggal_ttd", tanggal_ttd);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("margin", margin);
            window.open(url.toString(), "_blank");
        });
    });
</script>
