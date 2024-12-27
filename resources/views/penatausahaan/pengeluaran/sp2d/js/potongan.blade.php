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
        let tabel_pot = $('#tabel_pot').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('spm.load_rincian') }}",
                "type": "POST",
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
                    name: 'nilai',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
                    }
                },
                {
                    data: null,
                    name: 'hapus',
                    className: 'hapus',
                    render: function(data, type, row, meta) {
                        return `<a href="javascript:void(0);" onclick="hapusPot('${data.kd_trans}','${data.kd_rek6}','${data.map_pot}','${data.nm_rek6}','${data.idBilling}','${data.nilai}','${data.no_spm}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                        <button type="button" onclick="cetakPot('${data.idBilling}')" class="btn btn-success btn-sm"><i class="uil-print"></i></button>`
                    }
                }
            ]
        });
        let tabel_pajak = $('#tabel_pajak').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('spm.load_rincian') }}",
                "type": "POST",
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
                    data: null,
                    name: 'hapus',
                    className: 'hapus',
                    render: function(data, type, row, meta) {
                        return `<a href="javascript:void(0);" onclick="hapusPajak('${data.no_spm}','${data.kd_rek6}','${data.nm_rek6}','${data.idBilling}','${data.nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                        <button type="button" onclick="cetakPajak('${data.no_spm}', '${data.kd_rek6}', '${data.nm_rek6}','${data.nilai}')" class="btn btn-success btn-sm"><i class="uil-print"></i></button>`
                    }
                }
            ]
        });
        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
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
                        npwp: npwp
                    },
                    success: function(data) {
                        let data1 = $.parseJSON(data);
                        if (data1.data[0].response_code == '00') {
                            alert(data1.data[0].message);
                            $("#npwp").attr("value", data1.data[0].data
                                .nomorPokokWajibPajak);
                            $("#nama_wajib_pajak").prop('disabled', true);
                            $("#alamat_wajib_pajak").prop('disabled', true);
                            $("#nama_wajib_pajak").val('');
                            $("#alamat_wajib_pajak").val('');
                            $('#modal_cek_npwp').modal('hide');
                        } else {
                            alert(data1.data[0].message);
                            $("#nama_wajib_pajak").prop('disabled', false);
                            $("#alamat_wajib_pajak").prop('disabled', false);
                        }
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
                    kd_map: kd_map
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

            if (npwp_nol != '0') {
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

            if (nop == '1') {
                $("#nop").prop('disabled', false);
                $("#nop").val('');
            } else {
                $("#nop").prop('disabled', true);
                $("#nop").val('');
            }

            if (npwp_rekanan == '1') {
                $("#npwp_rekanan").prop('disabled', false);
                $("#npwp_rekanan").val('');
                $("#npwp_setor").val(npwp);
            } else {
                $("#npwp_rekanan").prop('disabled', true);
                $("#npwp_rekanan").val('');
            }

            if (npwp_rekanan == '1') {
                $("#nik_rekanan").prop('disabled', false);
                $("#nik_rekanan").val('');
            } else {
                $("#nik_rekanan").prop('disabled', true);
                $("#nik_rekanan").val('');
            }

            if (no_faktur == '1') {
                $("#no_faktur").prop('disabled', false);
                $("#no_faktur").val('');
            } else {
                $("#no_faktur").prop('disabled', true);
                $("#no_faktur").val('');
            }

            if (npwp_lain == '0') {
                $("#npwp_setor").val(npwp);
            }

            if (masa_bulan != '1') {
                $('#masa_pajak_akhir').prop('disabled', false);
                $("#no_faktur").val('');
            }
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
                    kd_map: kd_map
                },
                dataType: "json",
                success: function(data) {
                    $('#kode_setor_cek').empty();
                    $('#kode_setor_cek').append(
                        `<option value="0">Pilih Kode Setor</option>`);
                    $.each(data, function(index, data) {
                        $('#kode_setor_cek').append(
                            `<option value="${data.kd_setor}" data-nama="${data.nm_setor}">${data.kd_setor} | ${data.nm_setor}</option>`
                        );
                    })
                }
            })
        });

        $('#kode_setor_cek').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_setor_cek').val(nama);
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
                    kode_setor: kode_setor
                },
                dataType: "json",
                success: function(data) {
                    $('#kode_akun_potongan').empty();
                    $('#kode_akun_potongan').append(
                        `<option value="0">Pilih Kode Setor</option>`);
                    $.each(data, function(index, data) {
                        $('#kode_akun_potongan').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            })

        });

        $('#kode_akun_potongan').on('change', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nama_akun_potongan').val(nama);
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
            let nama = $(this).find(':selected').data('nama');
            let map_pot = $(this).find(':selected').data('map_pot');
            $('#nm_rek_pot').val(nama);
            $('#map_pot').val(map_pot);
        });

        $('#simpan_potongan').on('click', function() {
            let no_spm = document.getElementById('no_spm_potongan').value;
            let rekening_transaksi = document.getElementById('rekening_transaksi').value;
            let rekening_potongan = document.getElementById('rekening_potongan').value;
            let nm_rek_pot = document.getElementById('nm_rek_pot').value.trim();
            let id_billing = document.getElementById('id_billing').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let map_pot = document.getElementById('map_pot').value;
            let nilai_pot = angka(document.getElementById('nilai_pot').value);
            let total_pot = rupiah(document.getElementById('total_pot').value);
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
            let tampungan = tabel_pot.rows().data().toArray().map((value) => {
                let result = {
                    kd_rek6: value.kd_rek6,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.kd_rek6 == rekening_potongan) {
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
            if (!id_billing) {
                tambah_list_potongan(rekening_transaksi, rekening_potongan, map_pot, nm_rek_pot,
                    id_billing, nilai_pot, no_spm, kd_skpd, total_pot);
            } else {
                $('#simpan_potongan').prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "{{ route('spm.isi_list_pot') }}",
                    dataType: 'json',
                    data: {
                        id_billing: id_billing
                    },
                    dataType: "json",
                    success: function(data) {
                        let data1 = $.parseJSON(data);
                        if (data1.status == 'true' || data1.status == true) {
                            if (data1.data[0].response_code == '00') {
                                alert(data1.data[0].message);
                                tambah_list_potongan(rekening_transaksi, rekening_potongan,
                                    map_pot, nm_rek_pot, id_billing, nilai_pot, no_spm,
                                    kd_skpd, total_pot);
                            } else {
                                alert(data1.data[0].message);
                                $('#simpan_potongan').prop('disabled', false);
                            }
                        } else {
                            alert(data1.message);
                            $('#simpan_potongan').prop('disabled', false);
                        }
                    }
                })
            }
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
            let total_pajak = rupiah(document.getElementById('total_pajak').value);
            let total_pot = rupiah(document.getElementById('total_pot').value);

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
                    kode_akun_transaksi: kode_akun_transaksi
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
                        $('#create_billing').prop('disabled', false);
                    }
                }
            })
        });

        $('.cetak_billing').on('click', function() {
            let id_billing = document.getElementById('id_billing_cetak').value;
            let jnsreport = $(this).data("cetak");

            $.ajax({
                type: "POST",
                url: "{{ route('spm.create_report') }}",
                dataType: 'json',
                data: {
                    id_billing: id_billing,
                    jnsreport: jnsreport,
                },
                dataType: "json",
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

        function tambah_list_potongan(rekening_transaksi, rekening_potongan, map_pot, nm_rek_pot, id_billing,
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
                    nilai_pot: nilai_pot,
                    no_spm: no_spm,
                    kd_skpd: kd_skpd,
                },
                dataType: "json",
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil ditambahkan!');
                        tabel_pot.ajax.reload();
                        tabel_pajak.ajax.reload();
                        $("#rekening_potongan").val(null).change();
                        $("#nm_rek_pot").val('');
                        $("#map_pot").val('');
                        $("#id_billing").val('');
                        $("#nilai_pot").val('');
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
        } else {
            return;
        }
    }

    function hapusPajak(no_spm, kd_rek6, nm_rek6, idBilling, nilai) {
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
        } else {
            return;
        }
    }

    function cetakPajak(no_spm, kd_rek6, nm_rek6, nilai) {
        $('#modal_cetak').modal('show');
    }

    function cetakPot(idBilling) {
        $("#id_billing_cetak").val(idBilling);
        $('#modal_cetak').modal('show');
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }
</script>
