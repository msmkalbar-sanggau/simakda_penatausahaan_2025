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

        $('.select2-modal').select2({
            dropdownParent: $('#modal_rincian .modal-content'),
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('.select2-modal1').select2({
            dropdownParent: $('#modal_rekening .modal-content'),
            theme: 'bootstrap-5'
        });

        $("#satuan").prop('disabled', true);
        $("#volume").prop('disabled', true);

        let tabel_rincian = $('#rincian').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_bukti',
                    name: 'no_bukti',
                    visible: false
                },
                {
                    data: 'no_sp2d',
                    name: 'no_sp2d',
                    visible: false
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'nm_sub_kegiatan',
                    name: 'nm_sub_kegiatan',
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
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'sumber',
                    name: 'sumber',
                },
                {
                    data: 'lalu',
                    name: 'lalu',
                    visible: false
                },
                {
                    data: 'sp2d',
                    name: 'sp2d',
                    visible: false
                },
                {
                    data: 'anggaran',
                    name: 'anggaran',
                    visible: false
                },
                {
                    data: 'volume',
                    name: 'volume',
                },
                {
                    data: 'satuan',
                    name: 'satuan',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        let input_rincian = $('#input_rincian').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_bukti',
                    name: 'no_bukti',
                    visible: false
                },
                {
                    data: 'no_sp2d',
                    name: 'no_sp2d',
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'nm_sub_kegiatan',
                    name: 'nm_sub_kegiatan',
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
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'lalu',
                    name: 'lalu',
                },
                {
                    data: 'sumber',
                    name: 'sumber',
                },
                {
                    data: 'sp2d',
                    name: 'sp2d',
                },
                {
                    data: 'anggaran',
                    name: 'anggaran',
                },
                {
                    data: 'anggaran',
                    name: 'anggaran',
                    visible: false
                },
                {
                    data: 'anggaran',
                    name: 'anggaran',
                    visible: false
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        let tabel_tujuan = $('#rekening_tujuan').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_bukti',
                    name: 'no_bukti',
                    visible: false
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti',
                    visible: false
                },
                {
                    data: 'rekening_awal',
                    name: 'rekening_awal',
                    visible: false
                },
                {
                    data: 'nm_rekening_tujuan',
                    name: 'nm_rekening_tujuan',
                },
                {
                    data: 'rekening_tujuan',
                    name: 'rekening_tujuan',
                },
                {
                    data: 'bank_tujuan',
                    name: 'bank_tujuan',
                    visible: false
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    visible: false
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        $('#tambah_rincian').on('click', function() {
            let beban = document.getElementById('beban').value;
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            let tampungan = tabel_rincian.rows().data().toArray().map((value) => {
                let result = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    sumber: value.sumber,
                };
                return result;
            });

            if (tampungan.length == 1 && beban == '1') {
                alert(
                    'Pada transaski UP/GU hanya boleh 1 Rekening Belanja, Info lebih lanjut silahkan hubungi bidang Perbendaharaan'
                );
                return;
            }

            if (beban == '' || tgl_bukti == '' || kd_skpd == '' || no_bukti == '') {
                alert('Harap Isi Kode SKPD, Tanggal Transaksi & Jenis Beban SP2D')
                return;
            }
            load_kegiatan();
            $('#modal_rincian').modal('show');
        });

        $('#tambah_rek_tujuan').on('click', function() {
            $('#modal_rekening').modal('show');
        });

        $('#rek_tujuan').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            let bank = $(this).find(':selected').data('bank');

            $("#nm_rekening_tujuan").val(nama);
            $("#bank").val(bank).trigger('change');
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            $('#no_sp2d').empty();
            $('#kode_rekening').empty();
            $('#sumber').empty();
            let kd_sub_kegiatan = this.value;
            let beban = document.getElementById('beban').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            $.ajax({
                url: "{{ route('transaksi_lalu.sp2d') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    beban: beban,
                },
                success: function(data) {
                    $('#no_sp2d').empty();
                    $('#no_sp2d').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#no_sp2d').append(
                            `<option value="${data.no_sp2d}" data-tgl="${data.tgl_sp2d}">${data.no_sp2d} | ${data.tgl_sp2d}</option>`
                        );
                    })
                }
            });
        });

        $('#no_sp2d').on('select2:select', function() {
            $('#kode_rekening').empty();
            $('#sumber').empty();
            let no_sp2d = this.value;
            let beban = document.getElementById('beban').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let tgl_sp2d = $(this).find(':selected').data('tgl');
            $('#nomor_sp2d').val(no_sp2d);
            if (tgl_sp2d > tgl_bukti) {
                alert('Kesalahan, Tanggal Sp2d lebih kecil Dari Tanggal Bukti');
                $("#no_sp2d").val(null).change();
                return;
            }

            $.ajax({
                url: "{{ route('transaksi_lalu.potongan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sp2d: document.getElementById('no_sp2d').value,
                },
                success: function(data) {
                    $('#sisa_bank').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.sisa_bank));
                    $('#potongan_ls').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.potongan));
                    $('#total_sisa').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.sisa_bank + data.potongan));
                }
            });

            $.ajax({
                url: "{{ route('transaksi_lalu.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_bukti: document.getElementById('no_bukti').value,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                    beban: document.getElementById('beban').value,
                    no_sp2d: no_sp2d,
                },
                success: function(data) {
                    $('#kode_rekening').empty();
                    $('#kode_rekening').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kode_rekening').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}" data-anggaran="${data.anggaran}" data-lalu="${data.lalu}" data-sp2d="${data.sp2d}">${data.kd_rek6} | ${data.nm_rek6} | ${new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2}).format(data.lalu)}</option>`
                        );
                    })
                }
            });
        });

        $('#kode_rekening').on('select2:select', function() {
            let kd_rek6 = this.value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let anggaran = $(this).find(':selected').data('anggaran');
            let lalu = $(this).find(':selected').data('lalu');
            let sp2d = $(this).find(':selected').data('sp2d');
            let beban = document.getElementById('beban').value;

            if (kd_rek6.substr(0, 2) == '52') {
                $("#satuan").prop('disabled', false);
                $("#volume").prop('disabled', false);
            }

            if (kd_rek6.substr(0, 4) == '5105') {
                $("#satuan").prop('disabled', false);
                $("#volume").prop('disabled', false);
            }

            if (kd_rek6.substr(0, 4) == '5106') {
                $("#satuan").prop('disabled', false);
                $("#volume").prop('disabled', false);
            }

            if (kd_rek6.substr(0, 4) == '5402') {
                $("#satuan").prop('disabled', false);
                $("#volume").prop('disabled', false);
            }

            if (kd_rek6.substr(0, 6) == '510203') {
                $("#satuan").prop('disabled', false);
                $("#volume").prop('disabled', false);
            }

            let sisa = 0;
            if (beban == '1') {
                sisa = anggaran - lalu;
                $('#total_anggaran').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(anggaran));
            } else {
                sisa = sp2d - lalu;
                $('#total_anggaran').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(sp2d));
            }

            $('#lalu_anggaran').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(lalu));
            $('#sisa_anggaran').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(sisa));

            // LOAD ANGKAS DAN SPD
            $.ajax({
                url: "{{ route('transaksi_lalu.angkas_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    tgl_kas: document.getElementById('tgl_bukti').value,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                    status_angkas: document.getElementById('status_angkas').value,
                    beban: document.getElementById('beban').value,
                    no_sp2d: document.getElementById('no_sp2d').value,
                    kd_rek6: kd_rek6,
                },
                success: function(data) {
                    $('#total_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.angkas));
                    $('#lalu_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.transaksi));
                    $('#sisa_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.angkas - data.transaksi));

                    $('#total_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.spd));
                    $('#lalu_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.transaksi));
                    $('#sisa_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.spd - data.transaksi));
                }
            });

            $.ajax({
                url: "{{ route('transaksi_lalu.sumber_dana') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    tgl_bukti: document.getElementById('tgl_bukti').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    no_sp2d: document.getElementById('no_sp2d').value,
                    beban: document.getElementById('beban').value,
                    kd_rek6: kd_rek6,
                },
                success: function(data) {
                    $('#sumber').empty();
                    $('#sumber').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#sumber').append(
                            `<option value="${data.sumber}" data-nilai="${data.nilai}">${data.sumber} | ${data.nm_sumber} | ${new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2}).format(data.nilai)} | ${kd_sub_kegiatan} | ${kd_rek6}</option>`
                        );
                    })
                }
            });
        });

        $('#sumber').on('select2:select', function() {
            let sumber = this.value;
            let nilai = $(this).find(':selected').data('nilai');

            $('#total_sumber').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));

            // LOAD SUMBER DANA
            $.ajax({
                url: "{{ route('transaksipanjar.sumber_dana') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                    sumber: sumber,
                    kd_rek6: document.getElementById('kode_rekening').value,
                    no_sp2d: document.getElementById('no_sp2d').value,
                    beban: document.getElementById('beban').value,
                },
                success: function(data) {
                    $('#lalu_sumber').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total));
                    $('#sisa_sumber').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(nilai - data.total));
                }
            });
        });

        $('#simpan_rincian').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kode_rekening = document.getElementById('kode_rekening').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let sumber = document.getElementById('sumber').value;
            let status_anggaran = document.getElementById('status_anggaran').value;
            let beban = document.getElementById('beban').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let volume = document.getElementById('volume').value;
            let satuan = document.getElementById('satuan').value;

            let sisa_anggaran = rupiah(document.getElementById('sisa_anggaran').value);
            let sisa_angkas = rupiah(document.getElementById('sisa_angkas').value);
            let sisa_spd = rupiah(document.getElementById('sisa_spd').value);
            let sisa_sumber = rupiah(document.getElementById('sisa_sumber').value);
            let total_sisa = rupiah(document.getElementById('total_sisa').value);
            let sisa_bank = rupiah(document.getElementById('sisa_bank').value);
            let total_rincian = rupiah(document.getElementById('total_rincian').value);
            let nilai = angka(document.getElementById('nilai').value);

            let sub_kegiatan = $('#kd_sub_kegiatan').find('option:selected');
            let nm_sub_kegiatan = sub_kegiatan.data('nama');

            let rekening = $('#kode_rekening').find('option:selected');
            let nm_rek6 = rekening.data('nama');
            let anggaran = rekening.data('anggaran');
            let lalu = rekening.data('lalu');
            let sp2d = rekening.data('sp2d');

            let tahun_input = tgl_bukti.substr(0, 4);

            if (kode_rekening.substr(0, 2) == '52' && (satuan == '' || volume == '')) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            if (kode_rekening.substr(0, 4) == '5105' && (satuan == '' || volume == '')) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            if (kode_rekening.substr(0, 4) == '5106' && (satuan == '' || volume == '')) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            if (kode_rekening.substr(0, 4) == '5402' && (satuan == '' || volume == '')) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            if (kode_rekening.substr(0, 6) == '510203' && (satuan == '' || volume == '')) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            if (kode_rekening.substr(0, 2) == '52' && nilai > 15000000) {
                alert('Akun Belanja ini tidak boleh melebihi 15 juta');
                return;
            }

            if (kode_rekening.substr(0, 6) == '510203' && nilai > 15000000) {
                alert('Akun Belanja ini tidak boleh melebihi 15 juta');
                return;
            }

            let akumulasi = total_rincian + nilai;

            if (nilai > sisa_angkas) {
                alert('Nilai Melebihi Sisa Anggaran Kas...!!!, Cek Lagi...!!!');
                return;
            }

            if (!sumber) {
                alert('Pilih Sumber Dana Dahulu');
                return;
            }

            if (!kode_rekening) {
                alert('Pilih rekening Dahulu');
                return;
            }

            if (!no_sp2d) {
                alert('Pilih SP2D Dahulu');
                return;
            }

            if (nilai > total_sisa) {
                alert('Total Transaksi melebihi Total Sisa');
                return;
            }

            if (nilai == 0) {
                alert('Nilai Nol.....!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai > sisa_anggaran) {
                alert('Nilai Melebihi Sisa Anggaran...!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai > sisa_sumber) {
                alert('Nilai Melebihi Sisa Anggaran Sumber Dana...!!!, Cek Lagi...!!!');
                return;
            }

            if (beban == '1' && (akumulasi > sisa_spd)) {
                alert('Total Transaksi melebihi Sisa SPD');
                return;
            }

            if (!kd_sub_kegiatan) {
                alert('Pilih Kegiatan Dahulu');
                return;
            }

            let tampungan = tabel_rincian.rows().data().toArray().map((value) => {
                let result = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    sumber: value.sumber,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.kd_rek6 == kode_rekening && data.sumber == sumber) {
                    return '2';
                }
                if (data.kd_sub_kegiatan != kd_sub_kegiatan) {
                    return '3';
                }
                if (data.kd_sub_kegiatan == kd_sub_kegiatan && data.kd_rek6 == kode_rekening) {
                    return '4';
                }
            });
            if (kondisi.includes("2")) {
                alert('Tidak boleh memilih rekening dengan sumber dana yang sama dlm 1 Transaksi');
                return;
            }
            if (kondisi.includes("3")) {
                alert('Tidak boleh memilih kegiatan berbeda dalam 1 Transaksi!');
                return;
            }
            if (kondisi.includes("4")) {
                alert('Tidak boleh memilih rekening yang sama dalam 1 Transaksi!');
                return;
            }

            // proses input ke tabel input detail spp
            alert('Data Detail Tersimpan');
            tabel_rincian.row.add({
                'no_bukti': no_bukti,
                'no_sp2d': no_sp2d,
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'kd_rek6': kode_rekening,
                'nm_rek6': nm_rek6,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'lalu': lalu,
                'sumber': sumber,
                'sp2d': sp2d,
                'anggaran': anggaran,
                'volume': volume,
                'satuan': satuan,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kode_rekening}','${nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            input_rincian.row.add({
                'no_bukti': no_bukti,
                'no_sp2d': no_sp2d,
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'kd_rek6': kode_rekening,
                'nm_rek6': nm_rek6,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'lalu': lalu,
                'sumber': sumber,
                'sp2d': sp2d,
                'anggaran': anggaran,
                'volume': volume,
                'satuan': satuan,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kode_rekening}','${nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            $("#total").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(akumulasi));
            $("#total_rincian").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(akumulasi));

            $('#kd_sub_kegiatan').val(null).change();
            $('#no_sp2d').empty();
            $('#kode_rekening').empty();
            $('#sumber').empty();

            $('#total_spd').val(null);
            $('#lalu_spd').val(null);
            $('#sisa_spd').val(null);

            $('#total_anggaran').val(null);
            $('#lalu_anggaran').val(null);
            $('#sisa_anggaran').val(null);

            $('#total_angkas').val(null);
            $('#lalu_angkas').val(null);
            $('#sisa_angkas').val(null);

            $('#total_sumber').val(null);
            $('#lalu_sumber').val(null);
            $('#sisa_sumber').val(null);

            $('#potongan_ls').val(null);
            $('#total_sisa').val(null);

            $('#volume').val(null);
            $('#satuan').val(null);

            $('#nilai').val(null);
        });

        $('#simpan_rekening_tujuan').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let rekening = document.getElementById('rekening').value;
            let nm_rekening_tujuan = document.getElementById('nm_rekening_tujuan').value;
            let rek_tujuan = document.getElementById('rek_tujuan').value;
            let bank = document.getElementById('bank').value;
            let total = rupiah(document.getElementById('total').value);
            let total_transfer = rupiah(document.getElementById('total_transfer').value);
            let total_potongan = rupiah(document.getElementById('total_potongan').value);
            let nilai_potongan = angka(document.getElementById('nilai_potongan').value);
            let nilai_transfer = angka(document.getElementById('nilai_transfer').value);

            let hasil_akumulasi = total - nilai_potongan;
            let akumulasi = total_transfer + nilai_transfer;

            if (nilai_transfer == 0) {
                alert("Nilai Tidak Boleh Nol");
                return;
            }

            if (akumulasi > hasil_akumulasi) {
                alert('Nilai Melebihi Total Belanja');
                return;
            }

            if (nilai_transfer > hasil_akumulasi) {
                alert('Nilai Melebihi Total Belanja');
                return;
            }

            if (total_transfer > hasil_akumulasi) {
                alert('Nilai Melebihi Total Belanja');
                return;
            }

            if (!rekening) {
                alert('Pilih Rekening Sumber');
                return;
            }

            if (!nm_rekening_tujuan) {
                alert('Pilih rekening');
                return;
            }

            if (!rek_tujuan) {
                alert('Pilih rekening');
                return;
            }

            if (!bank) {
                alert('Pilih rekening');
                return;
            }

            tabel_tujuan.row.add({
                'no_bukti': no_bukti,
                'tgl_bukti': tgl_bukti,
                'rekening_awal': rekening,
                'nm_rekening_tujuan': nm_rekening_tujuan,
                'rekening_tujuan': rek_tujuan,
                'bank_tujuan': bank,
                'kd_skpd': kd_skpd,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai_transfer),
                'aksi': `<a href="javascript:void(0);" onclick="deleteRek('${no_bukti}','${rek_tujuan}','${nilai_transfer}','${nilai_potongan}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();

            $('#total_potongan').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai_potongan));
            $('#total_transfer').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transfer + nilai_transfer));

            $('#rek_tujuan').val(null).change();
            $('#nm_rekening_tujuan').val(null);
            $('#bank').val(null).change();
            $('#nilai_transfer').val(null);
            // $('#modal_rekening').modal('hide');
        });

        $('#simpan').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let nomor_sp2d = document.getElementById('nomor_sp2d').value;
            let keterangan = document.getElementById('keterangan').value;
            let beban = document.getElementById('beban').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let rekening = document.getElementById('rekening').value;
            let total = rupiah(document.getElementById('total').value);
            let total_rincian = rupiah(document.getElementById('total_rincian').value);
            let sisa_bank = rupiah(document.getElementById('sisa_bank').value);
            let total_potongan = rupiah(document.getElementById('total_potongan').value);
            let total_transfer = rupiah(document.getElementById('total_transfer').value);
            let tahun_input = tgl_bukti.substr(0, 4);

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (total > (sisa_bank + total_potongan)) {
                alert('Nilai Melebihi sisa Simpanan Bank');
                return;
            }

            if (!no_bukti) {
                alert('No Bukti Tidak Boleh Kosong');
                return;
            }

            if (!tgl_bukti) {
                alert('Tanggal Bukti Tidak Boleh Kosong');
                return;
            }

            if (!kd_skpd) {
                alert('SKPD Tidak Boleh Kosong');
                return;
            }

            if (!pembayaran) {
                alert('Pembayaran Tidak Boleh Kosong');
                return;
            }

            if (!beban) {
                alert('Beban Tidak Boleh Kosong');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }

            if (!rekening) {
                alert('Isian Rekening Belum Lengkap!');
                return;
            }

            if (total == 0) {
                alert('Rincian Tidak ada rekening!');
                return;
            }

            if (total_transfer > total) {
                alert('Total Transfer melebihi Total Belanja!');
                return;
            }

            let rincian_rekening = tabel_rincian.rows().data().toArray().map((value) => {
                let data = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    nm_sub_kegiatan: value.nm_sub_kegiatan,
                    no_sp2d: value.no_sp2d,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: rupiah(value.nilai),
                    sumber: value.sumber,
                    volume: value.volume,
                    satuan: value.satuan,
                };
                return data;
            });

            let rincian_rek_tujuan = tabel_tujuan.rows().data().toArray().map((value) => {
                let data = {
                    no_bukti: value.no_bukti,
                    tgl_bukti: value.tgl_bukti,
                    rekening_awal: value.rekening_awal,
                    nm_rekening_tujuan: value.nm_rekening_tujuan,
                    rekening_tujuan: value.rekening_tujuan,
                    bank_tujuan: value.bank_tujuan,
                    kd_skpd: value.kd_skpd,
                    nilai: rupiah(value.nilai),
                };
                return data;
            });

            if (rincian_rekening.length == 0) {
                alert('Rincian Tidak ada rekening!');
                return;
            }

            if (nomor_sp2d == '') {
                alert('SP2D belum di SET');
                return;
            }

            let data = {
                no_bukti,
                tgl_bukti,
                beban,
                kd_skpd,
                nm_skpd,
                pembayaran,
                rekening,
                nomor_sp2d,
                keterangan,
                total,
                rincian_rekening,
                rincian_rek_tujuan
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('transaksi_lalu.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        let potongan = confirm(
                            'Data Berhasil Tersimpan...!, Apakah Transaksi ini Terdapat Terima Potongan Pajak ?'
                        );
                        if (potongan == true) {
                            window.location.href =
                                "{{ route('skpd.potongan_pajak.index') }}";
                        } else {
                            window.location.href =
                                "{{ route('transaksi_lalu.index') }}";
                        }
                    } else if (response.message == '4') {
                        alert("Nomor Telah Dipakai!");
                        $('#simpan').prop('disabled', false);
                    } else {
                        alert('Data gagal disimpan!');
                        $('#simpan').prop('disabled', false);
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

        function load_data(no_panjar, no_panjar_lalu) {
            $('#panjar_awal').val(null);
            $('#nilai_panjar_awal').val(null);
            $('#tambahan_panjar').val(null);
            $('#nilai_tambahan_panjar').val(null);
            $('#total_panjar').val(null);
            $('#total_transaksi').val(null);
            $('#sisa_panjar').val(null);

            $.ajax({
                url: "{{ route('kembalipanjar.load_data') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_panjar: no_panjar,
                    no_panjar_lalu: no_panjar_lalu,
                },
                success: function(data) {
                    $('#panjar_awal').val(data.load_detail.no_panjar);
                    $('#nilai_panjar_awal').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_detail.nilai));

                    $('#tambahan_panjar').val(data.load_detail.no_panjar2);
                    $('#nilai_tambahan_panjar').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_detail.nilai2));

                    $('#total_panjar').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_total.panjar));

                    $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_total.trans));

                    $('#sisa_panjar').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.load_total.panjar - data.load_total.trans));
                }
            })
        };

        function load_kegiatan() {
            $.ajax({
                url: "{{ route('transaksi_lalu.kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: document.getElementById('kd_skpd').value,
                    beban: document.getElementById('beban').value,
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            });
        }
    });

    function hitung() {
        let nilai_panjar_lalu = rupiah(document.getElementById('nilai_panjar_lalu').value);
        let nilai = angka(document.getElementById('nilai').value);

        // Total
        $('#total').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(nilai_panjar_lalu + nilai));
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

    function angka(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function deleteData(no_bukti, kd_sub_kegiatan, kd_rek6, nilai) {
        let tabel = $('#rincian').DataTable();
        let tabel1 = $('#input_rincian').DataTable();
        let total = rupiah(document.getElementById('total').value);
        let total_rincian = rupiah(document.getElementById('total_rincian').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai :  ' + nilai +
            ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.kd_sub_kegiatan == kd_sub_kegiatan && data.no_bukti == no_bukti && data.kd_rek6 ==
                    kd_rek6
            }).remove().draw();
            tabel1.rows(function(idx, data, node) {
                return data.kd_sub_kegiatan == kd_sub_kegiatan && data.no_bukti == no_bukti && data.kd_rek6 ==
                    kd_rek6
            }).remove().draw();
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - parseFloat(nilai)));
            $('#total_rincian').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_rincian - parseFloat(nilai)));
        } else {
            return false;
        }
    }

    function deleteRek(no_bukti, rek_tujuan, nilai_transfer, nilai_potongan) {
        let tabel = $('#rekening_tujuan').DataTable();
        let transfer_sementara = rupiah(document.getElementById('total_transfer').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + rek_tujuan + '  Nilai :  ' + nilai_transfer +
            ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.no_bukti == no_bukti && data.rekening_tujuan == rek_tujuan
            }).remove().draw();
            $('#total_transfer').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(transfer_sementara - nilai_transfer));
        } else {
            return false;
        }

    }
</script>
