<style>
    p {
        margin: 2px 0px
    }

    .text-right {
        text-align: right
    }

    .no_spm {
        width: 20%
    }

    .kd_rek6 {
        width: 10%
    }

    .nm_rek6 {
        width: 15%;
        text-align: justify
    }

    .idBilling {
        width: 10%
    }

    .nilai {
        width: 15%
    }

    .hapus {
        width: 30%;
        text-align: center;
    }
</style>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#kode_map').prop('disabled', true);
        $('#kode_setor').prop('disabled', true);

        let tabel_pot = $('#tabel_pot').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('spm.load_rincian') }}",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "data": {
                    "no_spm": document.getElementById('no_spm_pajak').value
                }
            },
            columns: [{
                    data: 'kd_trans',
                    name: 'kd_trans'
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6'
                },
                {
                    data: 'map_pot',
                    name: 'map_pot',
                    visible: false
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6'
                },
                {
                    data: 'idBilling',
                    name: 'idBilling'
                },
                {
                    data: null,
                    name: 'status_potongan',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.status_potongan == '1') {
                            return '&#10004';
                        } else {
                            return '&#10008';
                        }
                    }
                },
                {
                    data: null,
                    name: 'nilai',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                }
            ]
        });

        let tabel_pot_tampungan = $('#tabel_pot_tampungan').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('spm.load_rincian_tampungan') }}",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "data": {
                    "no_spm": document.getElementById('no_spm_pajak').value
                },
                "dataSrc": function(data) {
                    recordsTotal = data.data;
                    return recordsTotal;
                },
            },
            columns: [{
                    data: 'kd_trans',
                    name: 'kd_trans'
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6'
                },
                {
                    data: 'map_pot',
                    name: 'map_pot',
                    visible: false
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6'
                },
                {
                    data: 'idBilling',
                    name: 'idBilling'
                },
                {
                    data: null,
                    name: 'pot',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.pot == '1') {
                            return '&#10004';
                        } else {
                            return '&#10008';
                        }
                    }
                },
                {
                    data: null,
                    name: 'nilai',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                }
            ],
            drawCallback: function(select) {
                let total = recordsTotal.reduce((previousValue,
                    currentValue) => (previousValue += parseFloat(currentValue.nilai)), 0);
                $('#total_pot_tampungan').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total));
            }
        });

        let tabel_pajak = $('#tabel_pajak').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('spm.load_rincian') }}",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "data": {
                    "no_spm": document.getElementById('no_spm_pajak').value
                }
            },
            ordering: false,
            columns: [{
                    data: 'no_spm',
                    name: 'no_spm',
                    className: 'no_spm'
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                    className: 'kd_rek6'
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6',
                    className: 'nm_rek6'
                },
                {
                    data: 'idBilling',
                    name: 'idBilling',
                    className: 'idBilling'
                },
                {
                    data: null,
                    name: 'nilai',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                }
            ]
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#masa_pajak_awal').on('select2:select', function() {
            let masa = this.value;
            $('#masa_pajak_akhir').val(masa).trigger('change');
        });

        $('#form_pajak').hide();
        $('#rincian_pajak').hide();

        $('#input_potongan').on('click', function() {
            $('#form_potongan').show();
            $('#form_pajak').hide();

            $('#list_potongan').show();
            $('#rincian_pajak').hide();
        })

        $('#input_pajak').on('click', function() {
            $('#form_pajak').show();
            $('#form_potongan').hide();

            $('#rincian_pajak').show();
            $('#list_potongan').hide();
        })

        $('#cek_npwp').on('click', function() {
            $('#modal_cek_npwp').modal('show');
        });

        $('#cek_npwp_bukti').on('click', function() {
            let npwp = document.getElementById('npwp_cek').value;
            let kd_map = document.getElementById('kode_map_cek').value;
            let kd_setor = document.getElementById('kode_setor_cek').value;

            let kode_setor_cek = $('#kode_setor_cek').find('option:selected');
            let npwp_rekanan = kode_setor_cek.data('npwp_rekanan');
            let npwp_lain = kode_setor_cek.data('npwp_lain');
            let no_sk = kode_setor_cek.data('nosk');
            let npwp_nol = kode_setor_cek.data('npwp_nol');
            let nop = kode_setor_cek.data('nop');
            let nik_rekanan = kode_setor_cek.data('nik_rekanan');
            let no_faktur = kode_setor_cek.data('no_faktur');
            let masa_bulan = kode_setor_cek.data('masa_bulan');

            if (npwp.substr(0, 9) == '000000000') {
                $('#modal_cek_npwp').modal('hide');
            } else {
                if (!npwp) {
                    alert('NPWP tidak boleh kosong');
                    return;
                }
                if (!kd_map) {
                    alert('Kode Akun tidak boleh kosong');
                    return;
                }
                if (!kd_setor) {
                    alert('Kode setor tidak boleh kosong');
                    return;
                }

                $.ajax({
                    url: "{{ route('penerima.cekNpwp') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        kode_akun: kd_map,
                        kode_setor: kd_setor,
                        npwp: npwp,
                        "_token": "{{ csrf_token() }}",
                    },
                    beforeSend: function() {
                        $("#overlay").fadeIn(100);
                    },
                    success: function(data) {
                        let data1 = $.parseJSON(data);
                        //console.log(data1);
                        if (data1.data[0].response_code == '00') {
                            alert(data1.data[0].message);
                            $("#npwp").attr("value", data1.data[0].data
                                .nomorPokokWajibPajak);
                            $("#nama_wajib_pajak").attr("value", data1.data[0].data
                                .namaWajibPajak);
                            $("#alamat_wajib_pajak").val(data1.data[0].data
                                .alamatWajibPajak);
                            $("#kota").attr("value", data1.data[0].data
                                .kota);
                            $("#kode_map").val(data1.data[0].data
                                .kodeMap).trigger('change');
                            $("#nama_map").attr("value", data1.data[0].data
                                .keteranganKodeMap);
                            $("#kode_setor").val(data1.data[0].data
                                .kodeSetor).change();
                            $("#nama_setor").attr("value", data1.data[0].data
                                .keteranganKodeSetor);
                            $("#nama_wajib_pajak").prop('disabled', true);
                            $("#alamat_wajib_pajak").prop('disabled', true);

                            $('#masa_pajak_akhir').prop('disabled', true);
                            if (no_sk == '1') {
                                $("#no_sk").prop('disabled', false);
                                $("#no_sk").val('');
                            } else {
                                $("#no_sk").prop('disabled', true);
                                $("#no_sk").val('');
                            }

                            if (npwp_nol != 0) {
                                $("#nik").prop('disabled', false);
                                $("#nik").val('');
                                $("#kota").prop('disabled', false);
                                $("#kota").val('');
                                $("#nama_wajib_pajak").prop('disabled', false);
                                $("#nama_wajib_pajak").val('');
                                $("#alamat_wajib_pajak").prop('disabled', false);
                                $("#alamat_wajib_pajak").val('');
                            } else {
                                $("#nik").prop('disabled', true);
                                $("#nik").val('');
                                $("#kota").prop('disabled', true);
                                $("#kota").val('');
                                $("#nama_wajib_pajak").prop('disabled', true);
                                $("#nama_wajib_pajak").val('');
                                $("#alamat_wajib_pajak").prop('disabled', true);
                                $("#alamat_wajib_pajak").val('');
                            }

                            if (nop == 1) {
                                $("#nop").prop('disabled', false);
                                $("#nop").val('');
                            } else {
                                $("#nop").prop('disabled', true);
                                $("#nop").val('');
                            }

                            if (npwp_rekanan == 1) {
                                $("#npwp_rekanan").prop('disabled', false);
                                $("#npwp_rekanan").val('');
                                $("#npwp_setor").val(data1.data[0].data
                                    .nomorPokokWajibPajak);
                            } else {
                                $("#npwp_rekanan").prop('disabled', true);
                                $("#npwp_rekanan").val('');
                            }

                            if (nik_rekanan == 1) {
                                $("#nik_rekanan").prop('disabled', false);
                                $("#nik_rekanan").val('');
                            } else {
                                $("#nik_rekanan").prop('disabled', true);
                                $("#nik_rekanan").val('');
                            }

                            if (no_faktur == 1) {
                                $("#no_faktur").prop('disabled', false);
                                $("#no_faktur").val('');
                            } else {
                                $("#no_faktur").prop('disabled', true);
                                $("#no_faktur").val('');
                            }

                            if (npwp_lain == 0) {
                                $("#npwp_setor").val(data1.data[0].data
                                    .nomorPokokWajibPajak);
                            }

                            if (masa_bulan != '1') {
                                $('#masa_pajak_akhir').prop('disabled', false);
                                $("#no_faktur").val('');
                            }
                            // $("#nama_wajib_pajak").val('');
                            // $("#alamat_wajib_pajak").val('');
                            $('#modal_cek_npwp').modal('hide');
                        }
                        if (data1.status == 'false') {
                            alert(data1.message);
                            $("#nama_wajib_pajak").prop('disabled', true);
                            $("#alamat_wajib_pajak").prop('disabled', true);
                        }
                    },
                    complete: function(data) {
                        $("#overlay").fadeOut(100);
                    }
                });
            }
        });

        $('#kode_map').on('select2:select', function() {
            $("#kode_akun_transaksi").val(null).change();
            $("#kode_akun_potongan").empty();
            $("#nama_setor").val("");
            $("#nama_akun_transaksi").val("");
            $("#nama_akun_potongan").val("");
            let kd_map = this.value;
            let nama = $(this).find(':selected').data('nama');
            $('#nama_map').val(nama);
            $.ajax({
                type: "POST",
                url: "{{ route('penerima.kodeSetor') }}",
                dataType: 'json',
                data: {
                    kd_map: kd_map,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(data) {
                    $('#kode_setor').empty();
                    $('#kode_setor').append(
                        `<option value="0">Pilih Kode Setor</option>`);
                    $.each(data, function(index, data) {
                        $('#kode_setor').append(
                            `<option value="${data.kd_setor}" data-nik_rekanan="${data.nik_rekanan}" data-npwp_rekanan="${data.npwp_rekanan}" data-nop="${data.butuh_nop}" data-npwp_nol="${data.npwp_nol}" data-nosk="${data.butuh_nosk}" data-nama="${data.nm_setor}" data-npwp_lain="${data.npwp_lain}" data-masa_bulan="${data.masa_bulan}" data-no_faktur="${data.no_faktur}">${data.kd_setor} | ${data.nm_setor}</option>`
                        );
                    })
                }
            })
        });

        $('#kode_setor').on('select2:select', function() {
            $("#kode_akun_transaksi").val(null).change();
            $("#kode_akun_potongan").val(null).change();

            // let nama = $(this).find(':selected').data('nama');
            // let no_sk = $(this).find(':selected').data('nosk');
            // let npwp_nol = $(this).find(':selected').data('npwp_nol');
            // let nop = $(this).find(':selected').data('nop');
            // let npwp_rekanan = $(this).find(':selected').data('npwp_rekanan');
            // let nik_rekanan = $(this).find(':selected').data('nik_rekanan');
            // let no_faktur = $(this).find(':selected').data('no_faktur');
            // let npwp_lain = $(this).find(':selected').data('npwp_lain');
            // let masa_bulan = $(this).find(':selected').data('masa_bulan');
            // let npwp = document.getElementById('npwp').value;

            // $('#nama_setor').val(nama);
            // $('#masa_pajak_akhir').prop('disabled', true);
            // if (no_sk == '1') {
            //     $("#no_sk").prop('disabled', false);
            //     $("#no_sk").val('');
            // } else {
            //     $("#no_sk").prop('disabled', true);
            //     $("#no_sk").val('');
            // }

            // if (npwp_nol != 0) {
            //     $("#nik").prop('disabled', false);
            //     $("#nik").val('');
            //     $("#kota").prop('disabled', false);
            //     $("#kota").val('');
            //     $("#nama_wajib_pajak").prop('disabled', false);
            //     $("#nama_wajib_pajak").val('');
            //     $("#alamat_wajib_pajak").prop('disabled', false);
            //     $("#alamat_wajib_pajak").val('');
            // } else {
            //     $("#nik").prop('disabled', true);
            //     $("#nik").val('');
            //     $("#kota").prop('disabled', true);
            //     $("#kota").val('');
            //     $("#nama_wajib_pajak").prop('disabled', true);
            //     $("#nama_wajib_pajak").val('');
            //     $("#alamat_wajib_pajak").prop('disabled', true);
            //     $("#alamat_wajib_pajak").val('');
            // }

            // if (nop == 1) {
            //     $("#nop").prop('disabled', false);
            //     $("#nop").val('');
            // } else {
            //     $("#nop").prop('disabled', true);
            //     $("#nop").val('');
            // }

            // if (npwp_rekanan == 1) {
            //     $("#npwp_rekanan").prop('disabled', false);
            //     $("#npwp_rekanan").val('');
            //     $("#npwp_setor").val(npwp);
            // } else {
            //     $("#npwp_rekanan").prop('disabled', true);
            //     $("#npwp_rekanan").val('');
            // }

            // if (nik_rekanan == 1) {
            //     $("#nik_rekanan").prop('disabled', false);
            //     $("#nik_rekanan").val('');
            // } else {
            //     $("#nik_rekanan").prop('disabled', true);
            //     $("#nik_rekanan").val('');
            // }

            // if (no_faktur == 1) {
            //     $("#no_faktur").prop('disabled', false);
            //     $("#no_faktur").val('');
            // } else {
            //     $("#no_faktur").prop('disabled', true);
            //     $("#no_faktur").val('');
            // }

            // if (npwp_lain == 0) {
            //     $("#npwp_setor").val(npwp);
            // }

            // if (masa_bulan != '1') {
            //     $('#masa_pajak_akhir').prop('disabled', false);
            //     $("#no_faktur").val('');
            // }
        });

        $('#kode_map_cek').on('select2:select', function() {
            let kd_map = this.value;
            let nama = $(this).find(':selected').data('nama');
            $('#nama_map_cek').val(nama);
            $.ajax({
                type: "POST",
                url: "{{ route('penerima.kodeSetor') }}",
                dataType: 'json',
                data: {
                    kd_map: kd_map,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(data) {
                    $('#kode_setor_cek').empty();
                    $('#kode_setor_cek').append(
                        `<option value="0">Pilih Kode Setor</option>`);
                    $.each(data, function(index, data) {
                        // $('#kode_setor_cek').append(
                        //     `<option value="${data.kd_setor}" data-nama="${data.nm_setor}">${data.kd_setor} | ${data.nm_setor}</option>`
                        // );
                        $('#kode_setor_cek').append(
                            `<option value="${data.kd_setor}" data-nik_rekanan="${data.nik_rekanan}" data-npwp_rekanan="${data.npwp_rekanan}" data-nop="${data.butuh_nop}" data-npwp_nol="${data.npwp_nol}" data-nosk="${data.butuh_nosk}" data-nama="${data.nm_setor}" data-npwp_lain="${data.npwp_lain}" data-masa_bulan="${data.masa_bulan}" data-no_faktur="${data.no_faktur}">${data.kd_setor} | ${data.nm_setor}</option>`
                        );
                    });
                    $('#kode_setor').empty();
                    $('#kode_setor').append(
                        `<option value="0">Pilih Kode Setor</option>`);
                    $.each(data, function(index, data) {
                        $('#kode_setor').append(
                            `<option value="${data.kd_setor}" data-nik_rekanan="${data.nik_rekanan}" data-npwp_rekanan="${data.npwp_rekanan}" data-nop="${data.butuh_nop}" data-npwp_nol="${data.npwp_nol}" data-nosk="${data.butuh_nosk}" data-nama="${data.nm_setor}" data-npwp_lain="${data.npwp_lain}" data-masa_bulan="${data.masa_bulan}" data-no_faktur="${data.no_faktur}">${data.kd_setor} | ${data.nm_setor}</option>`
                        );
                    });
                }
            })
        });

        $('#kode_setor_cek').on('select2:select', function() {
            let nama1 = $(this).find(':selected').data('nama');
            $('#nama_setor_cek').val(nama1);

            let nama = $(this).find(':selected').data('nama');
            let no_sk = $(this).find(':selected').data('nosk');
            let npwp_nol = $(this).find(':selected').data('npwp_nol');
            let nop = $(this).find(':selected').data('nop');
            let npwp_rekanan = $(this).find(':selected').data('npwp_rekanan');
            let nik_rekanan = $(this).find(':selected').data('nik_rekanan');
            let no_faktur = $(this).find(':selected').data('no_faktur');
            let npwp_lain = $(this).find(':selected').data('npwp_lain');
            let masa_bulan = $(this).find(':selected').data('masa_bulan');
            let npwp = document.getElementById('npwp').value;

            $('#nama_setor').val(nama);
            $('#masa_pajak_akhir').prop('disabled', true);
            if (no_sk == '1') {
                $("#no_sk").prop('disabled', false);
                $("#no_sk").val('');
            } else {
                $("#no_sk").prop('disabled', true);
                $("#no_sk").val('');
            }

            if (npwp_nol != 0) {
                $("#nik").prop('disabled', false);
                $("#nik").val('');
                $("#kota").prop('disabled', false);
                $("#kota").val('');
                $("#nama_wajib_pajak").prop('disabled', false);
                // $("#nama_wajib_pajak").val('');
                $("#alamat_wajib_pajak").prop('disabled', false);
                // $("#alamat_wajib_pajak").val('');
            } else {
                $("#nik").prop('disabled', true);
                $("#nik").val('');
                $("#kota").prop('disabled', true);
                $("#kota").val('');
                $("#nama_wajib_pajak").prop('disabled', true);
                // $("#nama_wajib_pajak").val('');
                $("#alamat_wajib_pajak").prop('disabled', true);
                // $("#alamat_wajib_pajak").val('');
            }

            if (nop == 1) {
                $("#nop").prop('disabled', false);
                $("#nop").val('');
            } else {
                $("#nop").prop('disabled', true);
                $("#nop").val('');
            }

            if (npwp_rekanan == 1) {
                $("#npwp_rekanan").prop('disabled', false);
                $("#npwp_rekanan").val('');
                $("#npwp_setor").val(npwp);
            } else {
                $("#npwp_rekanan").prop('disabled', true);
                $("#npwp_rekanan").val('');
            }

            if (nik_rekanan == 1) {
                $("#nik_rekanan").prop('disabled', false);
                $("#nik_rekanan").val('');
            } else {
                $("#nik_rekanan").prop('disabled', true);
                $("#nik_rekanan").val('');
            }

            if (no_faktur == 1) {
                $("#no_faktur").prop('disabled', false);
                $("#no_faktur").val('');
            } else {
                $("#no_faktur").prop('disabled', true);
                $("#no_faktur").val('');
            }

            if (npwp_lain == 0) {
                $("#npwp_setor").val(npwp);
            }

            if (masa_bulan != '1') {
                $('#masa_pajak_akhir').prop('disabled', false);
                $("#no_faktur").val('');
            }
        });

        $('#kode_akun_transaksi').on('select2:select', function() {
            let kode_akun = document.getElementById('kode_map').value;
            let kode_setor = document.getElementById('kode_setor').value;
            if (!kode_akun && !kode_setor) {
                alert('Kode Map dan Kode Setor harus diisi terlebih dahulu!');
                $("#kode_akun_transaksi").val(null).change();
                return;
            }
            let kode_akun_transaksi = this.value;
            let nama = $(this).find(':selected').data('nama');
            $('#nama_akun_transaksi').val(nama);

            $.ajax({
                type: "POST",
                url: "{{ route('spm.cari_rek_pot') }}",
                dataType: 'json',
                data: {
                    kode_akun: kode_akun,
                    kode_setor: kode_setor,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(data) {
                    $('#kode_akun_potongan').empty();
                    $('#kode_akun_potongan').append(
                        `<option value="0">Pilih Kode Setor</option>`);
                    $.each(data, function(index, data) {
                        $('#kode_akun_potongan').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}" data-map_pot=${data.map_pot}>${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            })

        });

        $('#kode_akun_potongan').on('change', function() {
            let nama = $(this).find(':selected').data('nama');
            let map_pot = $(this).find(':selected').data('map_pot');
            $('#nama_akun_potongan').val(nama);
            $('#map_pot').val(map_pot);
        });

        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });

        $('#rekening_transaksi').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_rek_trans').val(nama);
        });

        $('#rekening_potongan').on('select2:select', function() {
            let rekening_potongan = this.value;

            // if (rekening_potongan == '210105010001' || rekening_potongan == '210105020001' ||
            //     rekening_potongan == '210105030001' || rekening_potongan == '210109010001' ||
            //     rekening_potongan == '210105040001' || rekening_potongan == '210106010001') {
            //     $('#id_billing').prop('disabled', false);
            // } else {
            //     $('#id_billing').prop('disabled', true);
            //     $('#id_billing').val(null);
            // }

            let nama = $(this).find(':selected').data('nama');
            let map_pot = $(this).find(':selected').data('map_pot');
            let kd_map = $(this).find(':selected').data('kd_map');
            $('#nm_rek_pot').val(nama);
            $('#map_pot').val(map_pot);
            $('#kd_map').val(kd_map);
            let kode_rek = this.value;
            if (kode_rek.substr(0, 6) == '210601') {
                peringatan();
            }
        });

        $('#simpan_potongan').on('click', function() {
            let no_spm = document.getElementById('no_spm_potongan').value;
            let rekening_transaksi = document.getElementById('rekening_transaksi').value;
            let rekening_potongan = document.getElementById('rekening_potongan').value;
            let nm_rek_pot = document.getElementById('nm_rek_pot').value.trim();
            // let id_billing = document.getElementById('id_billing').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let map_pot = document.getElementById('map_pot').value;
            let kd_map = document.getElementById('kd_map').value;
            let nilai_pot = angka(document.getElementById('nilai_pot').value);
            let total_pot = rupiah(document.getElementById('total_pot').value);
            let jns_pot1 = document.getElementById('jns_pot1').value;

            if (!no_spm) {
                alert("Isi No SPM Terlebih Dahulu...!!!");
                return;
            }

            if (!rekening_transaksi) {
                alert("Isi Rekening Transaksi Terlebih Dahulu...!!!");
                return;
            }

            if (!rekening_potongan) {
                alert("Isi Rekening Pajak Terlebih Dahulu...!!!");
                return;
            }

            if (nilai_pot == 0) {
                alert("Isi Nilai Terlebih Dahulu...!!!");
                return;
            }
            if (!jns_pot1) {
                alert("Pilih Jenis Potongan Terlebih Dahulu...!!!");
                return;
            }
            // if (rekening_potongan == '210105010001' || rekening_potongan == '210105020001' ||
            //     rekening_potongan == '210105030001' || rekening_potongan == '210109010001' ||
            //     rekening_potongan == '210105040001' || rekening_potongan == '210106010001') {
            //     if (!id_billing) {
            //         alert('ID Billing wajib diisi!');
            //         return;
            //     }
            // }

            let tampungan = tabel_pot.rows().data().toArray().map((value) => {
                let result = {
                    map_pot: value.map_pot,
                };
                return result;
            });

            let kondisi = tampungan.map(function(data) {
                if (data.map_pot.trim() == map_pot.trim()) {
                    return '2';
                } else {
                    return '1';
                }
            });

            if (kondisi.includes("2")) {
                alert('Rekening: ' + rekening_potongan + ' Nama Rekening: ' + nm_rek_pot +
                    ' telah ada di list potongan' + '!');
                return;
            }

            let tampungan1 = tabel_pot_tampungan.rows().data().toArray().map((value) => {
                let result = {
                    map_pot: value.map_pot,
                };
                return result;
            });

            let kondisi1 = tampungan1.map(function(data) {
                if (data.map_pot.trim() == map_pot.trim()) {
                    return '2';
                } else {
                    return '1';
                }
            });

            if (kondisi1.includes("2")) {
                alert('Rekening: ' + rekening_potongan + ' Nama Rekening: ' + nm_rek_pot +
                    ' telah ada di draft potongan' + '!');
                return;
            }

            // if (!id_billing) {
            //     tambah_list_potongan(rekening_transaksi, rekening_potongan, map_pot, nm_rek_pot,
            //         id_billing, nilai_pot, no_spm, kd_skpd, total_pot);
            // } else {
            //     $('#simpan_potongan').prop('disabled', true);
            //     $.ajax({
            //         type: "POST",
            //         url: "{{ route('spm.isi_list_pot') }}",
            //         dataType: 'json',
            //         data: {
            //             id_billing: id_billing
            //         },
            //         dataType: "json",
            //         success: function(data) {
            //             let data1 = $.parseJSON(data);
            //             if (data1.status == 'true' || data1.status == true) {
            //                 if (data1.data[0].response_code == '00') {
            //                     alert(data1.data[0].message);
            //                     console.log(data1.data[0].data)
            //                     console.log(data1.data[0].data.kodeMap);
            //                     console.log(kd_map);
            //                     return;
            //                     if (nilai_pot != data1.data[0].data.jumlahBayar) {
            //                         alert(
            //                             'Nilai inputan tidak sesuai dengan nominal di Billing'
            //                         );
            //                         return;
            //                     }

            //                     if (kd_map != data1.data[0].data.kodeMap) {
            //                         alert(
            //                             'Rekening Potongan tidak sesuai dengan kode Rekening di Billing!'
            //                         );
            //                         return;
            //                     }

            //                     tambah_list_potongan(rekening_transaksi, rekening_potongan,
            //                         map_pot, nm_rek_pot, data1.data[0].data.idBilling,
            //                         nilai_pot, no_spm,
            //                         kd_skpd, total_pot);
            //                 } else {
            //                     alert(data1.data[0].message);
            //                     $('#simpan_potongan').prop('disabled', false);
            //                 }
            //             } else {
            //                 alert(data1.message);
            //                 $('#simpan_potongan').prop('disabled', false);
            //             }
            //         }
            //     })
            // }

            $.ajax({
                type: "POST",
                url: "{{ route('spm.isi_tampungan') }}",
                dataType: 'json',
                data: {
                    rekening_transaksi: rekening_transaksi,
                    rekening_potongan: rekening_potongan,
                    map_pot: map_pot,
                    nm_rek_pot: nm_rek_pot,
                    nilai_pot: nilai_pot,
                    no_spm: no_spm,
                    kd_skpd: kd_skpd,
                    jns_pot1: jns_pot1,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                beforeSend: function() {
                    $('#simpan_potongan').prop('disabled', true);
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    if (data.success) {
                        Swal.fire(
                            'Saved!',
                            data.message,
                            data.icon
                        )
                    } else {
                        Swal.fire(
                            data.icon.toUpperCase(),
                            data.message,
                            data.icon
                        )
                    }

                    tabel_pot.ajax.reload();
                    tabel_pot_tampungan.ajax.reload();
                    tabel_pajak.ajax.reload();
                },
                complete: function(data) {
                    $('#simpan_potongan').prop('disabled', false);
                    $("#overlay").fadeOut(100);
                }
            })
        });


        $('#create_billing').on('click', function() {
            let npwp = document.getElementById('npwp').value;
            let kode_map = document.getElementById('kode_map').value;
            let nama_map = document.getElementById('nama_map').value;
            let kode_setor = document.getElementById('kode_setor').value;
            let nama_setor = document.getElementById('nama_setor').value;
            let masa_pajak = document.getElementById('masa_pajak_awal').value + '' + document
                .getElementById('masa_pajak_akhir').value;
            let tahun_pajak = document.getElementById('tahun_pajak').value;
            let jumlah_bayar = angka(document.getElementById('jumlah_bayar').value);
            let nop = document.getElementById('nop').value;
            let no_sk = document.getElementById('no_sk').value;
            let npwp_setor = document.getElementById('npwp_setor').value;
            let nama_wajib_pajak = document.getElementById('nama_wajib_pajak').value;
            let alamat_wajib_pajak = document.getElementById('alamat_wajib_pajak').value;
            let kota = document.getElementById('kota').value;
            let nik = document.getElementById('nik').value;
            let npwp_rekanan = document.getElementById('npwp_rekanan').value;
            let nik_rekanan = document.getElementById('nik_rekanan').value;
            let no_faktur = document.getElementById('no_faktur').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let no_spm = document.getElementById('no_spm_pajak').value;
            let nama_akun_potongan = document.getElementById('nama_akun_potongan').value;
            let kode_akun_potongan = document.getElementById('kode_akun_potongan').value;
            let kode_akun_transaksi = document.getElementById('kode_akun_transaksi').value;
            let map_pot = document.getElementById('map_pot').value;
            let total_pajak = rupiah(document.getElementById('total_pajak').value);
            let total_pot = rupiah(document.getElementById('total_pot').value);
            let jns_pot = document.getElementById('jns_pot').value;

            if (!kd_skpd) {
                alert('Kode SKPD tidak boleh kosong');
                return;
            }
            if (!no_spm) {
                alert('Nomor SPM tidak boleh kosong');
                return;
            }
            if (!kode_setor) {
                alert('Kode setor tidak boleh kosong');
                return;
            }
            if (!kode_map) {
                alert('Kode Map tidak boleh kosong');
                return;
            }
            if (!npwp) {
                alert('NPWP tidak boleh kosong');
                return;
            }
            if (!kode_akun_potongan) {
                alert('Kode Akun Potongan tidak boleh kosong');
                return;
            }
            if (!kode_akun_transaksi) {
                alert('Kode Akun Transaksi tidak boleh kosong');
                return;
            }

            if (jumlah_bayar.toString().length > 12) {
                alert('Jumlah bayar terlalu besar');
                return;
            }

            if (jumlah_bayar == 0) {
                alert('Jumlah Bayar tidak boleh Nol');
                return;
            }
            if (kode_map == '411122' && kode_setor == '920') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411124' && kode_setor == '100') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }

                if (!nik_rekanan) {
                    alert('nik Rekanan tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411124' && kode_setor == '104') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }
                if (!nik_rekanan) {
                    alert('nik Rekanan tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411128' && kode_setor == '402') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }
                if (!nik_rekanan) {
                    alert('nik Rekanan tidak boleh kosong');
                    return;
                }
                if (!nop) {
                    alert('Nomor Objek Pajak tidak boleh kosong');
                    return;
                }
                if (nop.length < '18') {
                    alert('Nomor Objek Pajak harus berjumlah 18 digit');
                    return;
                }
            }

            if (kode_map == '411128' && kode_setor == '403') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }
                if (!nik_rekanan) {
                    alert('nik Rekanan tidak boleh kosong');
                    return;
                }
                if (!nop) {
                    alert('Nomor Objek Pajak tidak boleh kosong');
                    return;
                }
                if (nop.length < '18') {
                    alert('Nomor Objek Pajak harus berjumlah 18 digit');
                    return;
                }
            }

            if (kode_map == '411128' && kode_setor == '409') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }

                if (!nik_rekanan) {
                    alert('nik Rekanan tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411128' && kode_setor == '423') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411128' && kode_setor == '410') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }

                if (!nik_rekanan) {
                    alert('nik Rekanan tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411128' && kode_setor == '411') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }

                if (!nik_rekanan) {
                    alert('nik Rekanan tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411211' && kode_setor == '920') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }

                if (!no_faktur) {
                    alert('Faktur Pajak tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411212' && kode_setor == '920') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }

                if (!no_faktur) {
                    alert('Faktur Pajak tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411221' && kode_setor == '920') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }

                if (!no_faktur) {
                    alert('Faktur Pajak tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411222' && kode_setor == '920') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }

                if (!no_faktur) {
                    alert('Faktur Pajak tidak boleh kosong');
                    return;
                }
            }

            if (kode_map == '411619' && kode_setor == '920') {
                if (!npwp_rekanan) {
                    alert('NPWP Rekanan tidak boleh kosong');
                    return;
                }

                if (!no_faktur) {
                    alert('Faktur Pajak tidak boleh kosong');
                    return;
                }
            }
            if (!jns_pot) {
                alert("Pilih Jenis Potongan Terlebih Dahulu...!!!");
                return;
            }

            let tampungan = tabel_pajak.rows().data().toArray().map((value) => {
                let result = {
                    kd_rek6: value.kd_rek6,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.kd_rek6 == kode_akun_potongan) {
                    return '2';
                } else {
                    return '1';
                }
            });
            if (kondisi.includes("2")) {
                alert('Rekening: ' + kode_akun_potongan + ' Nama Rekening: ' + nama_akun_potongan +
                    ' telah ada di rincian pajak' + '!');
                return;
            }
            $('#create_billing').prop('disabled', true);
            $.ajax({
                type: "POST",
                url: "{{ route('spm.create_id_billing') }}",
                dataType: 'json',
                data: {
                    npwp: npwp,
                    kode_map: kode_map,
                    nama_map: nama_map,
                    kode_setor: kode_setor,
                    nama_setor: nama_setor,
                    masa_pajak: masa_pajak,
                    tahun_pajak: tahun_pajak,
                    jumlah_bayar: jumlah_bayar,
                    nop: nop,
                    no_sk: no_sk,
                    npwp_setor: npwp_setor,
                    nama_wajib_pajak: nama_wajib_pajak,
                    alamat_wajib_pajak: alamat_wajib_pajak,
                    kota: kota,
                    nik: nik,
                    npwp_rekanan: npwp_rekanan,
                    nik_rekanan: nik_rekanan,
                    no_faktur: no_faktur,
                    kd_skpd: kd_skpd,
                    no_spm: no_spm,
                    nama_akun_potongan: nama_akun_potongan,
                    kode_akun_potongan: kode_akun_potongan,
                    kode_akun_transaksi: kode_akun_transaksi,
                    jns_pot: jns_pot,
                    map_pot: map_pot,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(data) {
                    let data1 = $.parseJSON(data);
                    if (data1.data[0].response_code == '00') {
                        alert(data1.data[0].message);
                        // document.getElementById("ebilling").value = data1.data[0].data
                        //     .idBilling;
                        // load_rincian(no_spm);
                        tabel_pajak.ajax.reload();
                        tabel_pot.ajax.reload();
                        total_pajak += jumlah_bayar;
                        $('#total_pajak').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_pajak))
                        $('#total_pot').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_pajak))
                        $("#kode_map").val(null).change();
                        $("#kode_setor").val(null).change();
                        $("#masa_pajak_awal").val(null).change();
                        $("#masa_pajak_akhir").val(null).change();
                        $("#kode_akun_transaksi").val(null).change();
                        $("#kode_akun_potongan").val(null).change();
                        $('#nama_map').val('');
                        $('#nama_setor').val('');
                        $('#nama_akun_transaksi').val('');
                        $('#nama_akun_potongan').val('');
                        $('#jumlah_bayar').val('');
                        $('#create_billing').prop('disabled', false);
                    } else {
                        alert(data1.data[0].message);
                        return;
                    }
                    if (data1.status == 'false') {
                        alert(data1.message);
                        $("#nama_wajib_pajak").prop('disabled', true);
                        $("#alamat_wajib_pajak").prop('disabled', true);
                    }
                }
            })
        });

        $('.cetak_billing').on('click', function() {
            let id_billing = document.getElementById('id_billing_cetak').value;
            let jnsreport = $(this).data("cetak");

            if (!id_billing) {
                alert('Pilih ID Billing!');
                return;
            }

            $.ajax({
                type: "POST",
                url: "{{ route('spm.create_report') }}",
                dataType: 'json',
                data: {
                    id_billing: id_billing,
                    jnsreport: jnsreport,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    let data1 = $.parseJSON(data);
                    console.table(data1);
                    if (data1.data[0].response_code == '00') {
                        alert(data1.data[0].message);
                        $("#link1").attr("value", data1.data[0].data.linkDownload);
                        window.open(data1.data[0].data.linkDownload);
                    } else {
                        alert(data1.data[0].message);
                    }
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        });

        $('#input_billing').on('click', function() {
            load_rekening()
            $('#modal_billing').modal('show');
        });

        $('#simpan_billing').on('click', function() {
            let no_spm = document.getElementById('no_spm_potongan').value;
            let id_billing = document.getElementById('id_billing').value;
            let rekening_tampungan = $("#rekening_tampungan").select2("val")

            if (!id_billing) {
                alert('ID Billing wajib diisi!');
                return;
            }

            if (rekening_tampungan.length == 0) {
                alert('Rekening tampungan wajib diisi!');
                return;
            }

            $.ajax({
                type: "POST",
                url: "{{ route('spm.simpan_billing') }}",
                dataType: 'json',
                data: {
                    no_spm: no_spm,
                    id_billing: id_billing,
                    rekening_tampungan: rekening_tampungan,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    if (data.success) {

                        $('#modal_billing').modal('hide');
                        $('#id_billing').val(null);
                        $('#rekening_tampungan').val(null).change()

                        Swal.fire(
                            'Saved!',
                            data.message,
                            data.icon
                        )


                    } else {
                        Swal.fire(
                            data.icon.toUpperCase(),
                            data.message,
                            data.icon
                        )
                    }

                    tabel_pot.ajax.reload();
                    tabel_pot_tampungan.ajax.reload();
                    tabel_pajak.ajax.reload();


                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        });

        $('#simpan_tampungan').on('click', function() {
            let no_spm = document.getElementById('no_spm_potongan').value;

            let rekening_mpn = ["210105010001", "210105020001", "210105030001", "210109010001",
                "210105040001", "210106010001"
            ];

            let tampungan = tabel_pot_tampungan.rows().data().toArray().map((value) => {
                let result = {
                    idBilling: value.idBilling,
                    kd_rek6: value.kd_rek6,
                };
                return result;
            });

            let kondisi = tampungan.map(function(data) {
                if (rekening_mpn.includes(data.kd_rek6.trim()) && !data.idBilling) {
                    return '2';
                } else {
                    return '1';
                }
            });

            // if (kondisi.includes("2")) {
            //     alert('Potongan MPN wajib memiliki id billing!');
            //     return;
            // }

            $.ajax({
                type: "POST",
                url: "{{ route('spm.simpan_tampungan') }}",
                dataType: 'json',
                data: {
                    no_spm: no_spm,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                beforeSend: function() {
                    $('#simpan_tampungan').prop('disabled', true)
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    if (data.success) {
                        Swal.fire(
                            'Saved!',
                            data.message,
                            data.icon
                        )
                    } else {
                        Swal.fire(
                            data.icon.toUpperCase(),
                            data.message,
                            data.icon
                        )
                    }

                    tabel_pot.ajax.reload();
                    tabel_pot_tampungan.ajax.reload();
                    tabel_pajak.ajax.reload();
                },
                complete: function(data) {
                    $('#simpan_tampungan').prop('disabled', false)
                    $("#overlay").fadeOut(100);
                }
            })
        });

        function rupiah(n) {
            let n1 = n.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }

        function angka(n) {
            let n1 = n.split(',').join('');
            return parseFloat(n1) || 0;
        }

        function formatNumber(n) {
            // format number 1000000 to 1,234,567
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }

        function formatCurrency(input, blur) {
            // appends $ to value, validates decimal side
            // and puts cursor back in right position.

            // get input value
            var input_val = input.val();

            // don't validate empty input
            if (input_val === "") {
                return;
            }

            // original length
            var original_len = input_val.length;

            // initial caret position
            var caret_pos = input.prop("selectionStart");

            // check for decimal
            if (input_val.indexOf(".") >= 0) {

                // get position of first decimal
                // this prevents multiple decimals from
                // being entered
                var decimal_pos = input_val.indexOf(".");

                // split number by decimal point
                var left_side = input_val.substring(0, decimal_pos);
                var right_side = input_val.substring(decimal_pos);

                // add commas to left side of number
                left_side = formatNumber(left_side);

                // validate right side
                right_side = formatNumber(right_side);

                // On blur make sure 2 numbers after decimal
                if (blur === "blur") {
                    right_side += "00";
                }

                // Limit decimal to only 2 digits
                right_side = right_side.substring(0, 2);

                // join number by .
                input_val = left_side + "." + right_side;

            } else {
                // no decimal entered
                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // final formatting
                if (blur === "blur") {
                    input_val += ".00";
                }
            }

            // send updated string to input
            input.val(input_val);

            // put caret back in the right position
            var updated_len = input_val.length;
            caret_pos = updated_len - original_len + caret_pos;
            input[0].setSelectionRange(caret_pos, caret_pos);
        }

        function tambah_list_potongan(rekening_transaksi, rekening_potongan, map_pot, nm_rek_pot, jns_pot1,
            id_billing,
            nilai_pot, no_spm, kd_skpd, total_pot) {
            $.ajax({
                type: "POST",
                url: "{{ route('spm.tambah_list_potongan') }}",
                dataType: 'json',
                data: {
                    rekening_transaksi: rekening_transaksi,
                    rekening_potongan: rekening_potongan,
                    map_pot: map_pot,
                    nm_rek_pot: nm_rek_pot,
                    id_billing: id_billing,
                    jns_pot1: jns_pot1,
                    nilai_pot: nilai_pot,
                    no_spm: no_spm,
                    kd_skpd: kd_skpd,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil ditambahkan!');
                        tabel_pot.ajax.reload();
                        tabel_pajak.ajax.reload();
                        $("#rekening_potongan").val(null).change();
                        $("#nm_rek_pot").val(null);
                        $("#map_pot").val(null);
                        $("#jns_pot").val(null);
                        $("#id_billing").val(null);
                        $("#nilai_pot").val(null);
                        total_pot += nilai_pot;
                        $('#total_pot').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_pot));
                        $('#total_pajak').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_pot));
                        $('#simpan_potongan').prop('disabled', false);
                    } else {
                        alert('Data gagal ditambahkan!');
                        $('#simpan_potongan').prop('disabled', false);
                    }
                }
            })
        }

        function peringatan() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda melampirkan STS untuk utang belanja ?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire(
                        'Pastikan sekali lagi anda melampirkan STS',
                        'Anda bisa melanjutkan ke tahap selanjutnya',
                        'success'
                    )
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Tidak Melampirkan STS',
                        'Info lebih lanjut silahkan hubungi bidang perbendaharaan',
                        'error'
                    )
                    $('#rekening_potongan').val(null).change();
                    $('#nm_rek_pot').val(null);
                    $('#map_pot').val(null);
                }
            })
        }

        function load_rekening() {
            $.ajax({
                url: "{{ route('spm.rekening_tampungan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spm: document.getElementById('no_spm_pajak').value,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#rekening_tampungan').empty();
                    $.each(data, function(index, data) {
                        $('#rekening_tampungan').append(
                            `<option value="${data.kd_rek6}">${data.kd_rek6} - ${data.nm_rek6}</option>`
                        );
                    })
                }
            })
        }

        // function load_rincian(no_spm) {
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ route('spm.load_rincian') }}",
        //         dataType: 'json',
        //         data: {
        //             no_spm: no_spm
        //         },
        //         dataType: "json",
        //         success: function(data) {
        //             $.each(data, function(index, data) {
        //                 tabel_pajak.row.add({
        //                     'no_spm': data.no_spm,
        //                     'kd_rek6': data.kd_rek6,
        //                     'nm_rek6': data.nm_rek6,
        //                     'idbilling': data.idBilling,
        //                     'nilai': new Intl.NumberFormat('id-ID', {
        //                         minimumFractionDigits: 2
        //                     }).format(data.nilai),
        //                     'hapus': `<a href="javascript:void(0);" onclick="hapusPajak('${data.no_spm}','${data.kd_rek6}','${data.nm_rek6}','${data.idBilling}','${data.nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
        //                 }).draw();
        //                 let total_pajak = rupiah(document.getElementById('total_pajak')
        //                     .value);
        //                 total_pajak += data.nilai;
        //             })
        //             $('#total_pajak').val(new Intl.NumberFormat('id-ID', {
        //                 minimumFractionDigits: 2
        //             }).format(total_pajak))
        //             $("#kode_map").val(null).change();
        //             $("#kode_setor").val(null).change();
        //             $("#masa_pajak_awal").val(null).change();
        //             $("#masa_pajak_akhir").val(null).change();
        //             $("#kode_akun_transaksi").val(null).change();
        //             $("#kode_akun_potongan").val(null).change();
        //             $('#nama_map').val('');
        //             $('#nama_setor').val('');
        //             $('#nama_akun_transaksi').val('');
        //             $('#nama_akun_potongan').val('');
        //             $('#jumlah_bayar').val('');
        //         }
        //     })
        // }
    });

    function hapusPot(rekening_transaksi, rekening_potongan, map_pot, nm_rek_pot, id_billing, nilai_pot, no_spm) {
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + rekening_potongan + '  Nilai :  ' + nilai_pot +
            ' ?');
        let total = rupiah(document.getElementById('total_pot').value);
        let tabel_pot = $('#tabel_pot').DataTable();
        let tabel_pajak = $('#tabel_pajak').DataTable();
        if (hapus == true) {
            $.ajax({
                type: "POST",
                url: "{{ route('spm.hapus_rincian_pajak') }}",
                dataType: 'json',
                data: {
                    no_spm: no_spm,
                    kd_rek6: rekening_potongan,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil dihapus!');
                        tabel_pot.ajax.reload();
                        tabel_pajak.ajax.reload();
                        $('#total_pot').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total - nilai_pot));
                        $('#total_pajak').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total - nilai_pot));
                    } else {
                        alert('Data gagal dihapus!');
                    }
                }
            })
            // tabel_pot.rows(function(idx, data, node) {
            //     return data.kd_trans == rekening_transaksi && data.kd_rek6 == rekening_potongan
            // }).remove().draw();
            // tabel_pajak.rows(function(idx, data, node) {
            //     return data.kd_rek6 == rekening_potongan
            // }).remove().draw();
            // $('#total_pajak').val(new Intl.NumberFormat('id-ID', {
            //     minimumFractionDigits: 2
            // }).format(total - nilai_pot));
            // $('#total_pot').val(new Intl.NumberFormat('id-ID', {
            //     minimumFractionDigits: 2
            // }).format(total - nilai_pot));
        } else {
            return;
        }
    }

    function hapusPajak(no_spm, kd_rek6, nm_rek6, idBilling, nilai, status_setor) {
        if (status_setor == '1') {
            alert(nm_rek6 + ' Telah Disetor!');
            return;
        }
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai : Rp. ' + nilai +
            ' ?');
        let total = rupiah(document.getElementById('total_pajak').value);
        let tabel_pajak = $('#tabel_pajak').DataTable();
        let tabel_pot = $('#tabel_pot').DataTable();
        if (hapus == true) {
            $.ajax({
                type: "POST",
                url: "{{ route('spm.hapus_rincian_pajak') }}",
                dataType: 'json',
                data: {
                    no_spm: no_spm,
                    kd_rek6: kd_rek6,
                    idBilling: idBilling,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil dihapus!');
                        tabel_pajak.ajax.reload();
                        tabel_pot.ajax.reload();
                        $('#total_pajak').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total - nilai));
                        $('#total_pot').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total - nilai));
                    } else {
                        alert('Data gagal dihapus!');
                    }
                }
            })
            // tabel_pot.rows(function(idx, data, node) {
            //     return data.kd_rek6 == rekening_potongan
            // }).remove().draw();
            // tabel_pajak.rows(function(idx, data, node) {
            //     return data.no_spm == no_spm && data.kd_rek6 == kd_rek6
            // }).remove().draw();
            // $('#total_pajak').val(new Intl.NumberFormat('id-ID', {
            //     minimumFractionDigits: 2
            // }).format(total - nilai));
            // $('#total_pot').val(new Intl.NumberFormat('id-ID', {
            //     minimumFractionDigits: 2
            // }).format(total - nilai));
        } else {
            return;
        }
    }

    function hapusTampungan(no_spm, kd_rek6, nm_rek6, idBilling, nilai, status_setor) {
        if (status_setor == '1') {
            alert(nm_rek6 + ' Telah Disetor!');
            return;
        }

        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai : Rp. ' + nilai +
            ' ?');

        let tabel_pot_tampungan = $('#tabel_pot_tampungan').DataTable();
        let tabel_pot = $('#tabel_pot').DataTable();
        let tabel_pajak = $('#tabel_pajak').DataTable();

        if (hapus == true) {
            $.ajax({
                type: "POST",
                url: "{{ route('spm.hapus_tampungan') }}",
                dataType: 'json',
                data: {
                    no_spm: no_spm,
                    kd_rek6: kd_rek6,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    if (data.success) {
                        Swal.fire(
                            'Saved!',
                            data.message,
                            data.icon
                        )
                    } else {
                        Swal.fire(
                            data.icon.toUpperCase(),
                            data.message,
                            data.icon
                        )
                    }

                    tabel_pot.ajax.reload();
                    tabel_pot_tampungan.ajax.reload();
                    tabel_pajak.ajax.reload();
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        } else {
            return;
        }
    }

    function cetakPajak(no_spm, kd_rek6, nm_rek6, nilai, idBilling) {
        $.ajax({
            url: "{{ route('spm.billing_cetak') }}",
            type: "POST",
            dataType: 'json',
            data: {
                no_spm: document.getElementById('no_spm_potongan').value,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                $('#id_billing_cetak').empty();
                $.each(data, function(index, data) {
                    $('#id_billing_cetak').append(
                        `<option value="${data.idBilling}">${data.idBilling}</option>`
                    );
                })
            }
        })
        // $("#id_billing_cetak").val(idBilling);
        $('#modal_cetak').modal('show');
    }

    function cetakPot(idBilling) {
        // $("#id_billing_cetak").val(idBilling);
        $('#modal_cetak').modal('show');
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }
</script>
