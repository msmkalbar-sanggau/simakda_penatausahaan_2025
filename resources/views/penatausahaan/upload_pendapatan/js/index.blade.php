<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        document.getElementById('file').value = "";

        $('.select2-multiple').select2({
            theme: 'bootstrap-5'
        });

        $("#form").on('submit', function(e) {
            e.preventDefault();

            let formdata = new FormData(document.getElementById("form"));

            let url = "{{ route('upload_pendapatan.simpan') }}";

            $.ajax({
                url: url,
                type: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#loading").modal('show');
                },
                data: formdata,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        text: response.message,
                    })
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    response = jqXHR.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        text: response.message,
                    })
                    // $("#loading").modal('hide');
                },
                complete: function(data) {
                    $("#loading").modal('hide');
                },
            });
        });
    });

    function angka(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function hapus(no_sts, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Penyetoran : ' + no_sts);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('penyetoran_lalu.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sts: no_sts,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Proses Hapus Berhasil');
                        window.location.reload();
                    } else {
                        alert('Proses Hapus Gagal...!!!');
                    }
                }
            })
        } else {
            return false;
        }
    }
</script>
