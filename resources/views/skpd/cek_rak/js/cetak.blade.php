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
            let kd_skpd = this.value;
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);

            // Cari Jenis Anggaran
            $.ajax({
                url: "{{ route('skpd.input_rak.jenis_anggaran') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $('#jenis_anggaran').empty();
                    $('#jenis_rak').empty();
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

        $('#jenis_anggaran').on('select2:select', function() {
            let jns_ang = this.value;

            // Kosongkan
            $('#jenis_rak').empty();
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
            let jenis_anggaran = document.getElementById('jenis_anggaran').value;

            if (!kd_skpd || !jenis_anggaran) {
                alert('Silahkan Lengkapi Inputan!');
                return;
            } else {
                let url = new URL("{{ route('skpd.cek_rak.cetakan_cek_anggaran') }}");
                let searchParams = url.searchParams;
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("jenis_anggaran", jenis_anggaran);
                searchParams.append("jenis_rak", jenis_rak);

                document.getElementById("demo").innerHTML = "<embed src=" + url +
                    " width='100%' height='500px'></embed>";
            }
        });

        $('.cek_rak').on('click', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jenis_anggaran = document.getElementById('jenis_anggaran').value;
            let jenis_rak = document.getElementById('jenis_rak').value;
            let jenis_print = $(this).data("jenis");

            if (!kd_skpd || !jenis_anggaran || !jenis_rak) {
                alert("Harap Lengkapi Inputan.");
                return;
            }

            let url = new URL("{{ route('skpd.cek_rak.cetakan_cek_anggaran') }}");
            let searchParams = url.searchParams;
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("jenis_anggaran", jenis_anggaran);
            searchParams.append("jenis_rak", jenis_rak);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
    });
</script>
