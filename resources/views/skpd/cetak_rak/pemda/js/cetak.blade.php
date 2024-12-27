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
        $('#ttd1').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
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


        $('#ttd2').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
        });

        $('#jenis_rak').on('select2:select', function() {
            let jenis_rak       = this.value;
            let jenis_anggaran  = document.getElementById('jenis_anggaran').value;
            let hidden          = 'hidden';

            if (!jenis_rak || !jenis_anggaran) {
                alert('Silahkan Lengkapi Inputan!');
                return
            } else {
                let url = new URL("{{ route('skpd.cetak_rak.pemda_preview') }}");
                let searchParams = url.searchParams;
                searchParams.append("jenis_anggaran", jenis_anggaran);
                searchParams.append("jenis_rak", jenis_rak);
                searchParams.append("hidden", hidden);

                document.getElementById("demo").innerHTML = "<embed src=" + url +
                    " width='100%' height='500px'></embed>";
            }
        });

        $('.cetak_rak').on('click', function() {
            let jenis_anggaran  = document.getElementById('jenis_anggaran').value;
            let jenis_rak       = document.getElementById('jenis_rak').value;
            let ttd2            = document.getElementById('ttd2').value;
            let tanggal_ttd     = document.getElementById('tanggal_ttd').value;
            let jenis_print     = $(this).data("jenis");

            if (!ttd2 || !tanggal_ttd) {
                alert("Harap Lengkapi Inputan.");
                return;
            }

            let url = new URL("{{ route('skpd.cetak_rak.pemda_preview') }}");
            let searchParams = url.searchParams;
            searchParams.append("jenis_anggaran", jenis_anggaran);
            searchParams.append("jenis_rak", jenis_rak);
            searchParams.append("ttd2", ttd2);
            searchParams.append("tanggal_ttd", tanggal_ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
    });
</script>
