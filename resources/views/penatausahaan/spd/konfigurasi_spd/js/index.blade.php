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
        
        $('#simpan_konfigurasi').on('click', function() {
            let no_konfig = document.getElementById('nomor').value;
            let tgl_konfig = document.getElementById('tgl_con').value;
            let ingat1 = document.getElementById('ingat_1').value;
            let ingat2 = document.getElementById('ingat_2').value;
            let ingat3 = document.getElementById('ingat_3').value;
            let ingat4 = document.getElementById('ingat_4').value;
            let ingat5 = document.getElementById('ingat_5').value;
            let ingat6 = document.getElementById('ingat_6').value;
            let ingat7 = document.getElementById('ingat_7').value;
            let ingat8 = document.getElementById('ingat_8').value;
            let ingat9 = document.getElementById('ingat_9').value;
            let ingat10 = document.getElementById('ingat_10').value;
            let ingat11 = document.getElementById('ingat_11').value;
            let ingat_akhir = document.getElementById('ingat_akhir').value;
            let memutuskan = document.getElementById('memutuskan').value;
            let jenis = document.getElementById('jenis_spd').value;
            
            let data = {
                no_konfig, tgl_konfig, ingat1, ingat2, ingat3, ingat4, ingat5, ingat6,
                ingat7, ingat8, ingat9, ingat10, ingat11, ingat_akhir, memutuskan, jenis
            };

            $.ajax({
                url: "{{ route('spd.konfigurasi_spd.update') }}",
                type: "patch",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data Berhasil Tersimpan!!!');
                        // return;
                        window.location.href = "{{ route('konfigurasi_spd.index') }}"
                    } else {
                        alert("Data Gagal Tersimpan!!!");
                        return;
                    }
                }
            })
        });
    });
</script>