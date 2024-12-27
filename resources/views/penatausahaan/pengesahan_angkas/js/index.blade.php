<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let tabel_angkas = $('#pengesahan_angkas').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('pengesahan_angkas.load_data') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
            ],
        });

        $('#simpan').on('click', function() {
            let kd_skpd = document.getElementById('kd_skpd').value;

            let angkas_murni = document.getElementById('angkas_murni').checked == true ? 1 : 0;
            let angkas_murni_geser1 = document.getElementById('angkas_murni_geser1').checked == true ?
                1 : 0;
            let angkas_murni_geser2 = document.getElementById('angkas_murni_geser2').checked == true ?
                1 : 0;
            let angkas_murni_geser3 = document.getElementById('angkas_murni_geser3').checked == true ?
                1 : 0;
            let angkas_murni_geser4 = document.getElementById('angkas_murni_geser4').checked == true ?
                1 : 0;
            let angkas_murni_geser5 = document.getElementById('angkas_murni_geser5').checked == true ?
                1 : 0;

            let angkas_sempurna1 = document.getElementById('angkas_sempurna1').checked == true ? 1 : 0;
            let angkas_sempurna1_geser1 = document.getElementById('angkas_sempurna1_geser1').checked ==
                true ? 1 : 0;
            let angkas_sempurna1_geser2 = document.getElementById('angkas_sempurna1_geser2').checked ==
                true ? 1 : 0;
            let angkas_sempurna1_geser3 = document.getElementById('angkas_sempurna1_geser3').checked ==
                true ? 1 : 0;
            let angkas_sempurna1_geser4 = document.getElementById('angkas_sempurna1_geser4').checked ==
                true ? 1 : 0;
            let angkas_sempurna1_geser5 = document.getElementById('angkas_sempurna1_geser5').checked ==
                true ? 1 : 0;

            let angkas_sempurna2 = document.getElementById('angkas_sempurna2').checked == true ? 1 : 0;
            let angkas_sempurna2_geser1 = document.getElementById('angkas_sempurna2_geser1').checked ==
                true ? 1 : 0;
            let angkas_sempurna2_geser2 = document.getElementById('angkas_sempurna2_geser2').checked ==
                true ? 1 : 0;
            let angkas_sempurna2_geser3 = document.getElementById('angkas_sempurna2_geser3').checked ==
                true ? 1 : 0;
            let angkas_sempurna2_geser4 = document.getElementById('angkas_sempurna2_geser4').checked ==
                true ? 1 : 0;
            let angkas_sempurna2_geser5 = document.getElementById('angkas_sempurna2_geser5').checked ==
                true ? 1 : 0;

            let angkas_sempurna3 = document.getElementById('angkas_sempurna3').checked == true ? 1 : 0;
            let angkas_sempurna3_geser1 = document.getElementById('angkas_sempurna3_geser1').checked ==
                true ? 1 : 0;
            let angkas_sempurna3_geser2 = document.getElementById('angkas_sempurna3_geser2').checked ==
                true ? 1 : 0;
            let angkas_sempurna3_geser3 = document.getElementById('angkas_sempurna3_geser3').checked ==
                true ? 1 : 0;
            let angkas_sempurna3_geser4 = document.getElementById('angkas_sempurna3_geser4').checked ==
                true ? 1 : 0;
            let angkas_sempurna3_geser5 = document.getElementById('angkas_sempurna3_geser5').checked ==
                true ? 1 : 0;

            let angkas_sempurna4 = document.getElementById('angkas_sempurna4').checked == true ? 1 : 0;
            let angkas_sempurna4_geser1 = document.getElementById('angkas_sempurna4_geser1').checked ==
                true ? 1 : 0;
            let angkas_sempurna4_geser2 = document.getElementById('angkas_sempurna4_geser2').checked ==
                true ? 1 : 0;
            let angkas_sempurna4_geser3 = document.getElementById('angkas_sempurna4_geser3').checked ==
                true ? 1 : 0;
            let angkas_sempurna4_geser4 = document.getElementById('angkas_sempurna4_geser4').checked ==
                true ? 1 : 0;
            let angkas_sempurna4_geser5 = document.getElementById('angkas_sempurna4_geser5').checked ==
                true ? 1 : 0;

            let angkas_sempurna5 = document.getElementById('angkas_sempurna5').checked == true ? 1 : 0;
            let angkas_sempurna5_geser1 = document.getElementById('angkas_sempurna5_geser1').checked ==
                true ? 1 : 0;
            let angkas_sempurna5_geser2 = document.getElementById('angkas_sempurna5_geser2').checked ==
                true ? 1 : 0;
            let angkas_sempurna5_geser3 = document.getElementById('angkas_sempurna5_geser3').checked ==
                true ? 1 : 0;
            let angkas_sempurna5_geser4 = document.getElementById('angkas_sempurna5_geser4').checked ==
                true ? 1 : 0;
            let angkas_sempurna5_geser5 = document.getElementById('angkas_sempurna5_geser5').checked ==
                true ? 1 : 0;

            let angkas_ubah = document.getElementById('angkas_ubah').checked == true ? 1 : 0;
            let angkas_ubah2 = document.getElementById('angkas_ubah2').checked == true ? 1 : 0;

            if (!kd_skpd) {
                alert('Silahkan Refresh!SKPD tidak ada!');
                return;
            }

            let data = {
                kd_skpd,
                angkas_murni,
                angkas_murni_geser1,
                angkas_murni_geser2,
                angkas_murni_geser3,
                angkas_murni_geser4,
                angkas_murni_geser5,
                angkas_sempurna1,
                angkas_sempurna1_geser1,
                angkas_sempurna1_geser2,
                angkas_sempurna1_geser3,
                angkas_sempurna1_geser4,
                angkas_sempurna1_geser5,
                angkas_sempurna2,
                angkas_sempurna2_geser1,
                angkas_sempurna2_geser2,
                angkas_sempurna2_geser3,
                angkas_sempurna2_geser4,
                angkas_sempurna2_geser5,
                angkas_sempurna3,
                angkas_sempurna3_geser1,
                angkas_sempurna3_geser2,
                angkas_sempurna3_geser3,
                angkas_sempurna3_geser4,
                angkas_sempurna3_geser5,
                angkas_sempurna4,
                angkas_sempurna4_geser1,
                angkas_sempurna4_geser2,
                angkas_sempurna4_geser3,
                angkas_sempurna4_geser4,
                angkas_sempurna4_geser5,
                angkas_sempurna5,
                angkas_sempurna5_geser1,
                angkas_sempurna5_geser2,
                angkas_sempurna5_geser3,
                angkas_sempurna5_geser4,
                angkas_sempurna5_geser5,
                angkas_ubah,
                angkas_ubah2
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('pengesahan_angkas.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data,
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data Berhasil Tersimpan...!!!');
                        $('#detail_angkas').modal('hide');
                        $('#simpan').prop('disabled', false);
                        tabel_angkas.ajax.reload();
                    } else if (response.message == '3') {
                        alert('Anda tidak mempunyai akses...!!!');
                        window.location.href = "{{ route('403') }}";
                        $('#simpan').prop('disabled', false);
                        return;
                    } else {
                        alert('Data Gagal Tersimpan...!!!');
                        $('#simpan').prop('disabled', false);
                        return;
                    }
                }
            })
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

    function detail(kd_skpd, nm_skpd, murni, murni_geser1, murni_geser2, murni_geser3, murni_geser4, murni_geser5,
        sempurna1, sempurna1_geser1, sempurna1_geser2, sempurna1_geser3, sempurna1_geser4, sempurna1_geser5, sempurna2,
        sempurna2_geser1, sempurna2_geser2, sempurna2_geser3, sempurna2_geser4, sempurna2_geser5, sempurna3,
        sempurna3_geser1, sempurna3_geser2, sempurna3_geser3, sempurna3_geser4, sempurna3_geser5, sempurna4,
        sempurna4_geser1, sempurna4_geser2, sempurna4_geser3, sempurna4_geser4, sempurna4_geser5, sempurna5,
        sempurna5_geser1, sempurna5_geser2, sempurna5_geser3, sempurna5_geser4, sempurna5_geser5, ubah, ubah2) {
        $('#kd_skpd').val(kd_skpd);
        $('#nm_skpd').val(nm_skpd);

        murni == 1 ? $('#angkas_murni').prop('checked', true) : $('#angkas_murni').prop('checked', false);
        murni_geser1 == 1 ? $('#angkas_murni_geser1').prop('checked', true) : $('#angkas_murni_geser1').prop('checked',
            false);
        murni_geser2 == 1 ? $('#angkas_murni_geser2').prop('checked', true) : $('#angkas_murni_geser2').prop('checked',
            false);
        murni_geser3 == 1 ? $('#angkas_murni_geser3').prop('checked', true) : $('#angkas_murni_geser3').prop('checked',
            false);
        murni_geser4 == 1 ? $('#angkas_murni_geser4').prop('checked', true) : $('#angkas_murni_geser4').prop('checked',
            false);
        murni_geser5 == 1 ? $('#angkas_murni_geser5').prop('checked', true) : $('#angkas_murni_geser5').prop('checked',
            false);

        sempurna1 == 1 ? $('#angkas_sempurna1').prop('checked', true) : $('#angkas_sempurna1').prop('checked', false);
        sempurna1_geser1 == 1 ? $('#angkas_sempurna1_geser1').prop('checked', true) : $('#angkas_sempurna1_geser1')
            .prop('checked', false);
        sempurna1_geser2 == 1 ? $('#angkas_sempurna1_geser2').prop('checked', true) : $('#angkas_sempurna1_geser2')
            .prop('checked', false);
        sempurna1_geser3 == 1 ? $('#angkas_sempurna1_geser3').prop('checked', true) : $('#angkas_sempurna1_geser3')
            .prop('checked', false);
        sempurna1_geser4 == 1 ? $('#angkas_sempurna1_geser4').prop('checked', true) : $('#angkas_sempurna1_geser4')
            .prop('checked', false);
        sempurna1_geser5 == 1 ? $('#angkas_sempurna1_geser5').prop('checked', true) : $('#angkas_sempurna1_geser5')
            .prop('checked', false);

        sempurna2 == 1 ? $('#angkas_sempurna2').prop('checked', true) : $('#angkas_sempurna2').prop('checked', false);
        sempurna2_geser1 == 1 ? $('#angkas_sempurna2_geser1').prop('checked', true) : $('#angkas_sempurna2_geser1')
            .prop('checked', false);
        sempurna2_geser2 == 1 ? $('#angkas_sempurna2_geser2').prop('checked', true) : $('#angkas_sempurna2_geser2')
            .prop('checked', false);
        sempurna2_geser3 == 1 ? $('#angkas_sempurna2_geser3').prop('checked', true) : $('#angkas_sempurna2_geser3')
            .prop('checked', false);
        sempurna2_geser4 == 1 ? $('#angkas_sempurna2_geser4').prop('checked', true) : $('#angkas_sempurna2_geser4')
            .prop('checked', false);
        sempurna2_geser5 == 1 ? $('#angkas_sempurna2_geser5').prop('checked', true) : $('#angkas_sempurna2_geser5')
            .prop('checked', false);

        sempurna3 == 1 ? $('#angkas_sempurna3').prop('checked', true) : $('#angkas_sempurna3').prop('checked', false);
        sempurna3_geser1 == 1 ? $('#angkas_sempurna3_geser1').prop('checked', true) : $('#angkas_sempurna3_geser1')
            .prop('checked', false);
        sempurna3_geser2 == 1 ? $('#angkas_sempurna3_geser2').prop('checked', true) : $('#angkas_sempurna3_geser2')
            .prop('checked', false);
        sempurna3_geser3 == 1 ? $('#angkas_sempurna3_geser3').prop('checked', true) : $('#angkas_sempurna3_geser3')
            .prop('checked', false);
        sempurna3_geser4 == 1 ? $('#angkas_sempurna3_geser4').prop('checked', true) : $('#angkas_sempurna3_geser4')
            .prop('checked', false);
        sempurna3_geser5 == 1 ? $('#angkas_sempurna3_geser5').prop('checked', true) : $('#angkas_sempurna3_geser5')
            .prop('checked', false);

        sempurna4 == 1 ? $('#angkas_sempurna4').prop('checked', true) : $('#angkas_sempurna4').prop('checked', false);
        sempurna4_geser1 == 1 ? $('#angkas_sempurna4_geser1').prop('checked', true) : $('#angkas_sempurna4_geser1')
            .prop('checked', false);
        sempurna4_geser2 == 1 ? $('#angkas_sempurna4_geser2').prop('checked', true) : $('#angkas_sempurna4_geser2')
            .prop('checked', false);
        sempurna4_geser3 == 1 ? $('#angkas_sempurna4_geser3').prop('checked', true) : $('#angkas_sempurna4_geser3')
            .prop('checked', false);
        sempurna4_geser4 == 1 ? $('#angkas_sempurna4_geser4').prop('checked', true) : $('#angkas_sempurna4_geser4')
            .prop('checked', false);
        sempurna4_geser5 == 1 ? $('#angkas_sempurna4_geser5').prop('checked', true) : $('#angkas_sempurna4_geser5')
            .prop('checked', false);

        sempurna5 == 1 ? $('#angkas_sempurna5').prop('checked', true) : $('#angkas_sempurna5').prop('checked', false);
        sempurna5_geser1 == 1 ? $('#angkas_sempurna5_geser1').prop('checked', true) : $('#angkas_sempurna5_geser1')
            .prop('checked', false);
        sempurna5_geser2 == 1 ? $('#angkas_sempurna5_geser2').prop('checked', true) : $('#angkas_sempurna5_geser2')
            .prop('checked', false);
        sempurna5_geser3 == 1 ? $('#angkas_sempurna5_geser3').prop('checked', true) : $('#angkas_sempurna5_geser3')
            .prop('checked', false);
        sempurna5_geser4 == 1 ? $('#angkas_sempurna5_geser4').prop('checked', true) : $('#angkas_sempurna5_geser4')
            .prop('checked', false);
        sempurna5_geser5 == 1 ? $('#angkas_sempurna5_geser5').prop('checked', true) : $('#angkas_sempurna5_geser5')
            .prop('checked', false);

        ubah == 1 ? $('#angkas_ubah').prop('checked', true) : $('#angkas_ubah').prop('checked', false);
        ubah2 == 1 ? $('#angkas_ubah2').prop('checked', true) : $('#angkas_ubah2').prop('checked', false);

        $('#detail_angkas').modal('show');
    }

    function hapus(no_tetap, kd_skpd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor Penetapan : ' + no_tetap);
        if (tanya == true) {
            $.ajax({
                url: "{{ route('penetapan_penerimaan.hapus') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_tetap: no_tetap,
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
