<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let list_potongan = $('#list_potongan').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('skpd.setor_potongan.list_potongan') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_terima = document.getElementById('no_terima').value;
                }
            },
            ordering: false,
            lengthMenu: [5, 10],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6',
                },
                {
                    data: 'rekanan',
                    name: 'rekanan',
                },
                {
                    data: 'npwp',
                    name: 'npwp',
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'ntpn',
                    name: 'ntpn',
                },
                {
                    data: 'ebilling',
                    name: 'ebilling',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#no_terima').on('select2:select', function() {
            let no_terima = this.value;
            let ket = $(this).find(':selected').data('ket');
            let no_sp2d = $(this).find(':selected').data('no_sp2d');
            let npwp = $(this).find(':selected').data('npwp');
            let jns_spp = $(this).find(':selected').data('jns_spp');
            let kd_sub_kegiatan = $(this).find(':selected').data('kd_sub_kegiatan');
            let nm_sub_kegiatan = $(this).find(':selected').data('nm_sub_kegiatan');
            let kd_rek6 = $(this).find(':selected').data('kd_rek6');
            let nm_rek6 = $(this).find(':selected').data('nm_rek6');
            let alamat = $(this).find(':selected').data('alamat');
            let nmrekan = $(this).find(':selected').data('nmrekan');
            let pimpinan = $(this).find(':selected').data('pimpinan');
            let kd_skpd = $(this).find(':selected').data('kd_skpd');
            let nm_skpd = $(this).find(':selected').data('nm_skpd');

            kosong();

            $('#keterangan').val(ket);
            $('#no_sp2d').val(no_sp2d);
            $('#npwp').val(npwp);
            $('#beban').val(jns_spp);

            if (jns_spp == 1) {
                $('#nama_beban').val('UP');
            } else if (jns_spp == 2) {
                $('#nama_beban').val('GU');
            } else if (jns_spp == 3) {
                $('#nama_beban').val('TU');
            } else if (jns_spp == 4) {
                $('#nama_beban').val('LS GAJI');
            } else if (jns_spp == 5) {
                $('#nama_beban').val('LS Pihak Ketiga Lainnya');
            } else if (jns_spp == 6) {
                $('#nama_beban').val('LS Barang Jasa');
            }

            $('#kd_sub_kegiatan').val(kd_sub_kegiatan);
            $('#nm_sub_kegiatan').val(nm_sub_kegiatan);
            $('#kd_rekening').val(kd_rek6);
            $('#nm_rekening').val(nm_rek6);
            $('#alamat').val(alamat);
            $('#rekanan').val(nmrekan);
            $('#pimpinan').val(pimpinan);
            $('#kd_skpd').val(kd_skpd);
            $('#nm_skpd').val(nm_skpd);
            list_potongan.ajax.reload();

            // total potongan
            $.ajax({
                url: "{{ route('skpd.setor_potongan.total_potongan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_terima: no_terima,
                },
                success: function(data) {
                    $('#total_potongan').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                }
            })
        });

        // $('#cek_billing').on('click', function() {
        //     let kd_rek6 = document.getElementById('kd_rek6').value;
        //     let id_billing = document.getElementById('id_billing').value;

        //     if (kd_rek6 == '210102010001' || kd_rek6 == '210102010001' || kd_rek6 == '210103010001' ||
        //         kd_rek6 == '210104010001' || kd_rek6 == '210108010001' || kd_rek6 == '210102010001') {
        //         $('#id_billing_validasi').val('-');
        //         $('#ntpn_validasi').val('-');
        //         $('#simpan_ntpn').prop('disabled', false);
        //     } else {
        //         $('#id_billing_validasi').val(null);
        //         $('#ntpn_validasi').val(null);
        //         // cek billing
        //         $.ajax({
        //             url: "{{ route('skpd.setor_potongan.cek_billing') }}",
        //             type: "POST",
        //             dataType: 'json',
        //             data: {
        //                 id_billing: id_billing,
        //             },
        //             success: function(data) {
        //                 let data1 = $.parseJSON(data);
        //                 if (data1.data[0].response_code == '00') {
        //                     alert(data1.data[0].message);
        //                     $("#id_billing").val(null);
        //                     $("#id_billing_validasi").val(data1.data[0].data
        //                         .idBilling);
        //                     $("#ntpn_validasi").val(data1.data[0].data.ntpn);
        //                     $('#simpan_ntpn').prop('disabled', false);
        //                 } else {
        //                     alert(data1.data[0].message);
        //                     $('#simpan_ntpn').prop('disabled', true);
        //                     $("#id_billing_validasi").val(null);
        //                     $("#ntpn_validasi").val(null);
        //                 }
        //             }
        //         })
        //     }
        // });

        $('#simpan_ntpn').on('click', function() {
            let id_terima = document.getElementById('id_terima').value;
            let id_setor = document.getElementById('id_setor').value;
            let no_terima = document.getElementById('no_terima').value;
            let no_bukti = document.getElementById('no_bukti').value;
            let kd_rek6 = document.getElementById('kd_rek6').value;
            let nm_rek6 = document.getElementById('nm_rek6').value;
            let ntpn_validasi = document.getElementById('ntpn_validasi').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let id_billing_validasi = document.getElementById('id_billing_validasi').value;

            if (!id_terima) {
                alert('Silahkan hapus semua dan input kembali');
                return;
            }
            if (!ntpn_validasi) {
                alert('NTPN tidak boleh kosong');
                return;
            }
            if (!id_billing_validasi) {
                alert('ebilling tidak boleh kosong');
                return;
            }
            // EDIT NTPN DAN BILLING
            $.ajax({
                url: "{{ route('skpd.setor_potongan.edit_ntpn') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    id_terima: id_terima,
                    id_setor: id_setor,
                    no_terima: no_terima,
                    no_bukti: no_bukti,
                    kd_rek6: kd_rek6,
                    nm_rek6: nm_rek6,
                    ntpn_validasi: ntpn_validasi,
                    kd_skpd: kd_skpd,
                    id_billing_validasi: id_billing_validasi,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('OK');
                        list_potongan.ajax.reload();
                        $('#modal_ntpn').modal('hide');
                    } else {
                        alert('Simpan Gagal');
                        return;
                    }
                }
            })
        });

        $('#simpan_potongan').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let tahun_input = tgl_bukti.substring(0, 4);

            if (!tgl_bukti) {
                alert('Tanggal Tidak Boleh kosong');
                return;
            }
            if (tahun_anggaran != tahun_input) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            let data = {
                no_bukti,
                tgl_bukti,
                pembayaran,
            };

            $('#simpan_potongan').prop('disabled', true);
            // Simpan Potongan
            $.ajax({
                url: "{{ route('skpd.setor_potongan.edit_potongan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data,
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Potongan berhasil disimpan, dengan Nomor Bukti : ' +
                            response.no_bukti);
                        window.location.href =
                            "{{ route('skpd.setor_potongan.index') }}"
                    } else {
                        alert('Potongan tidak berhasil disimpan!');
                        $('#simpan_potongan').prop('disabled', false);
                        return;
                    }
                }
            })
        });

        $('#cetak_bukti').on('click', function() {
            let id_billing = document.getElementById('id_billing_validasi').value;
            let jnsreport = 'ReportBPN';

            $.ajax({
                url: "{{ route('spm.create_report') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    id_billing: id_billing,
                    jnsreport: jnsreport,
                },
                success: function(data) {
                    let data1 = $.parseJSON(data);
                    if (data1.data[0].response_code == '00') {
                        alert(data1.data[0].message);
                        // $("#link2").attr("value", data1.data[0].data.linkDownload);
                        window.open(data1.data[0].data.linkDownload);
                    } else {
                        alert(data1.data[0].message);
                    }

                }
            })
        });

        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });
    });

    function angka(data) {
        let n1 = data.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function kosong() {
        $('#keterangan').val(null);
        $('#no_sp2d').val(null);
        $('#npwp').val(null);
        $('#beban').val(null);
        $('#nama_beban').val(null);
        $('#kd_sub_kegiatan').val(null);
        $('#nm_sub_kegiatan').val(null);
        $('#kd_rekening').val(null);
        $('#nm_rekening').val(null);
        $('#alamat').val(null);
        $('#rekanan').val(null);
        $('#pimpinan').val(null);
        $('#kd_skpd').val(null);
        $('#nm_skpd').val(null);
    }

    function rupiah(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }

    function deletePotongan(kd_rek_trans, kd_rek6, nm_rek6, nilai) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor Transaksi : ' + kd_rek6);
        let tabel = $('#list_potongan').DataTable();
        let nilai_potongan = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(nilai);
        let total_potongan = angka(document.getElementById('total_potongan').value);
        if (tanya == true) {
            tabel.rows(function(idx, data, node) {
                return data.nilai == nilai_potongan
            }).remove().draw();
            $('#total_potongan').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_potongan - parseFloat(nilai)));
        } else {
            return false;
        }
    }

    function tambahNtpn(id, kd_rek6, nm_rek6, npwp, nilai, ntpn, ebilling) {
        $('#id_terima').val(id);
        $('#id_setor').val(id);
        $('#kd_rek6').val(kd_rek6);
        $('#nm_rek6').val(nm_rek6);
        $('#nilai').val(nilai);
        $('#ntpn_validasi').val(ntpn);
        $('#id_billing_validasi').val(ebilling);
        $('#simpan_ntpn').prop('disabled', false);
        $('#modal_ntpn').modal('show');
    }
</script>
