<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#volume').prop('disabled', true);
        $('#satuan').prop('disabled', true);

        $('#tgl_voucher').on('change', function() {
            let tanggal = this.value;
            let bulan = new Date(tanggal);
            let bulan1 = bulan.getMonth() + 1;
            $('#bulan').val(bulan1).trigger('change');
        });

        let rekening_belanja = $('#rekening_belanja').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            lengthMenu: [5, 10],
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
                    visible: false
                },
                {
                    data: 'satuan',
                    name: 'Satuan',
                    visible: false
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        let rekening_tujuan = $('#rekening_tujuan').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            lengthMenu: [5, 10],
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

        let tabel_rekening = $('#input_rekening').DataTable({
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

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('.select2-modal').select2({
            dropdownParent: $('#modal_rekening .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-modal1').select2({
            dropdownParent: $('#modal_rekening_tujuan .modal-content'),
            theme: 'bootstrap-5'
        });


        $('#tambah_rekening').on('click', function() {
            let beban = document.getElementById('beban').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tgl_voucher = document.getElementById('tgl_voucher').value;

            let tampungan = tabel_rekening.rows().data().toArray().map((value) => {
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

            if (beban && kd_skpd && tgl_voucher) {
                status_anggaran();
                status_angkas();
                $('#modal_rekening').modal('show');
            } else {
                Swal.fire({
                    title: 'Harap Isi Kode SKPD, Tanggal Transaksi & Jenis Beban SP2D',
                    confirmButtonColor: '#5b73e8',
                })
            }
        });

        $('#tambah_rekening_tujuan').on('click', function() {
            $('#modal_rekening_tujuan').modal('show');
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            let kd_sub_kegiatan = this.value;
            let nm_sub_kegiatan = $(this).find(':selected').data('nama');
            $('#nm_sub_kegiatan').val(nm_sub_kegiatan);

            $('#no_sp2d').empty();
            $('#kd_rekening').empty();
            $('#sumber').empty();
            $('#nm_rekening').val(null);
            $('#nm_sumber').val(null);
            $('#total_spd').val(null);
            $('#realisasi_spd').val(null);
            $('#sisa_spd').val(null);
            $('#total_angkas').val(null);
            $('#realisasi_angkas').val(null);
            $('#sisa_angkas').val(null);
            $('#total_anggaran').val(null);
            $('#realisasi_anggaran').val(null);
            $('#sisa_anggaran').val(null);
            $('#total_sumber').val(null);
            $('#realisasi_sumber').val(null);
            $('#sisa_sumber').val(null);
            $('#sisa_kas').val(null);
            $('#potongan_ls').val(null);
            $('#total_sisa').val(null);
            cari_nomor(kd_sub_kegiatan);
        });

        $('#no_sp2d').on('select2:select', function() {
            let no_sp2d = this.value;
            let tgl_sp2d = $(this).find(':selected').data('tgl');
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            let beban = document.getElementById('beban').value;
            if (tgl_sp2d > tgl_voucher) {
                alert('Kesalahan, Tanggal Sp2d lebih kecil Dari Tanggal Bukti');
                $("#no_sp2d").val(null).change();
                return;
            }

            $('#kd_rekening').empty();
            $('#sumber').empty();
            $('#nm_rekening').val(null);
            $('#nm_sumber').val(null);
            $('#total_spd').val(null);
            $('#realisasi_spd').val(null);
            $('#sisa_spd').val(null);
            $('#total_angkas').val(null);
            $('#realisasi_angkas').val(null);
            $('#sisa_angkas').val(null);
            $('#total_anggaran').val(null);
            $('#realisasi_anggaran').val(null);
            $('#sisa_anggaran').val(null);
            $('#total_sumber').val(null);
            $('#realisasi_sumber').val(null);
            $('#sisa_sumber').val(null);
            $('#sisa_kas').val(null);
            $('#potongan_ls').val(null);
            $('#total_sisa').val(null);
            cari_rekening(no_sp2d);
        });

        $('#kd_rekening').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            let sp2d = $(this).find(':selected').data('sp2d');
            let anggaran = $(this).find(':selected').data('anggaran');
            let lalu = $(this).find(':selected').data('lalu');
            $('#nm_rekening').val(nama);
            let kd_rek6 = this.value;
            let beban = document.getElementById('beban').value;

            $('#sumber').empty();
            $('#nm_sumber').val(null);
            $('#total_spd').val(null);
            $('#realisasi_spd').val(null);
            $('#sisa_spd').val(null);
            $('#total_angkas').val(null);
            $('#realisasi_angkas').val(null);
            $('#sisa_angkas').val(null);
            $('#total_anggaran').val(null);
            $('#realisasi_anggaran').val(null);
            $('#sisa_anggaran').val(null);
            $('#total_sumber').val(null);
            $('#realisasi_sumber').val(null);
            $('#sisa_sumber').val(null);
            $('#potongan_ls').val(null);
            $('#sisa_kas').val(null);
            $('#total_sisa').val(null);

            cari_sumber(kd_rek6);
            if (kd_rek6.substr(0, 2) == '52') {
                $('#volume').prop('disabled', false);
                $('#satuan').prop('disabled', false);
            }
            if (kd_rek6.substr(0, 4) == '5105') {
                $('#volume').prop('disabled', false);
                $('#satuan').prop('disabled', false);
            }
            if (kd_rek6.substr(0, 4) == '5106') {
                $('#volume').prop('disabled', false);
                $('#satuan').prop('disabled', false);
            }
            if (kd_rek6.substr(0, 4) == '5402') {
                $('#volume').prop('disabled', false);
                $('#satuan').prop('disabled', false);
            }
            if (kd_rek6.substr(0, 6) == '510203') {
                $('#volume').prop('disabled', false);
                $('#satuan').prop('disabled', false);
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
            $('#realisasi_anggaran').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(lalu));
            $('#sisa_anggaran').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(sisa));
            load_angkas();
            let no_sp2d = document.getElementById('no_sp2d').value;
            load_potongan_ls(no_sp2d);
        })

        $('#sumber').on('select2:select', function() {
            let sumber = this.value;
            if (sumber == 'null') {
                alert('Sumber dana tidak dapat digunakan!');
                $('#sumber').val(null).change();
                return;
            }
            let anggaran = $(this).find(':selected').data('anggaran');
            $('#total_sumber').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(anggaran));
            $.ajax({
                url: "{{ route('penagihan.cari_nama_sumber') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    sumber_dana: sumber
                },
                success: function(data) {
                    $('#nm_sumber').val(data.nm_sumber_dana1);
                }
            })
            $('#realisasi_sumber').val(null);
            $('#sisa_sumber').val(null);
            load_dana(sumber);
        })

        $('#simpan_rekening').on('click', function() {
            let no_bukti = document.getElementById('no_voucher').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let nm_sub_kegiatan = document.getElementById('nm_sub_kegiatan').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let kd_rekening = document.getElementById('kd_rekening').value;
            let sumber = document.getElementById('sumber').value;
            let beban = document.getElementById('beban').value;
            let nm_rekening = document.getElementById('nm_rekening').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let status_anggaran = document.getElementById('status_anggaran').value;
            let volume = document.getElementById('volume').value;
            let satuan = document.getElementById('satuan').value;

            let sisa_anggaran = rupiah(document.getElementById('sisa_anggaran').value);
            let nilai = angka(document.getElementById('nilai').value);
            let total_sisa = rupiah(document.getElementById('total_sisa').value);
            let sisa_spd = rupiah(document.getElementById('sisa_spd').value);
            let total_input_rekening = rupiah(document.getElementById('total_input_rekening').value);
            let potongan_ls = rupiah(document.getElementById('potongan_ls').value);
            let sisa_sumber = rupiah(document.getElementById('sisa_sumber').value);
            let sisa_angkas = rupiah(document.getElementById('sisa_angkas').value);
            let realisasi_anggaran = rupiah(document.getElementById('realisasi_anggaran').value);
            let kd_rekening1 = $('#kd_rekening').find('option:selected');
            let anggaran = kd_rekening1.data('anggaran');
            let lalu = kd_rekening1.data('lalu');
            let sp2d = kd_rekening1.data('sp2d');

            let sp2d_1 = no_sp2d.split('/');
            let sp2d_2 = no_bukti + "." + sp2d_1[0] + "/" + sp2d_1[2] + "." + kd_rekening;
            $('#ketcms').val(sp2d_2);

            let akumulasi = nilai + total_input_rekening;

            if (sumber == '221020101') {
                alert(
                    'Silahkan konfirmasi ke perbendaharaan jika ingin transaksi sumber dana DID, jika tidak maka transaksi tidak bisa di approve oleh perbendahaaraan, terima kasih'
                );
            }

            if (kd_rekening.substr(0, 2) == '52' && (!satuan || !volume)) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            if (kd_rekening.substr(0, 4) == '5105' && (!satuan || !volume)) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            if (kd_rekening.substr(0, 4) == '5106' && (!satuan || !volume)) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            if (kd_rekening.substr(0, 4) == '5402' && (!satuan || !volume)) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            if (kd_rekening.substr(0, 6) == '510203' && (!satuan || !volume)) {
                alert('Volume atau Satuan Output Harus Diisi');
                return;
            }

            // if (kd_rekening.substr(0, 2) == '52' && nilai > 15000000) {
            //     alert('Akun Belanja ini tidak boleh melebihi 15 juta');
            //     return;
            // }

            if (beban == '1' && kd_sub_kegiatan.substr(0, 15) == '5.06.01.1.09.02' && kd_rekening
                .substr(0, 12) == '510203020035' && nilai > 50000000) {
                alert('Akun Belanja ini tidak boleh melebihi 50 juta');
                return;
            }

            // if (kd_rekening.substr(0, 6) == '510203' && nilai > 15000000 && kd_sub_kegiatan.substr(0,
            //         15) == '5.06.01.1.09.0002') {
            //     alert('Akun Belanja ini tidak boleh melebihi 15 juta');
            //     return;
            // }

            if (!kd_sub_kegiatan) {
                alert('Pilih Kegiatan Dahulu');
                return;
            }

            if (!nm_sub_kegiatan) {
                alert('Pilih Kegiatan Dahulu');
                return;
            }

            if (!no_sp2d) {
                alert('Pilih SP2D Dahulu');
                return;
            }

            if (!kd_rekening) {
                alert('Pilih Rekening Dahulu');
                return;
            }

            if (!nm_rekening) {
                alert('Pilih Rekening Dahulu');
                return;
            }

            if (!sumber) {
                alert('Pilih Sumber Dana Dahulu');
                return;
            }

            if (!status_anggaran) {
                alert('Pilih Tanggal Dahulu');
                return;
            }

            let tampungan = tabel_rekening.rows().data().toArray().map((value) => {
                let result = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    sumber: value.sumber,
                };
                return result;
            });

            let kondisi = tampungan.map(function(data) {
                if (data.kd_rek6 == kd_rekening && data.sumber == sumber) {
                    return '2';
                } else if (data.kd_sub_kegiatan != kd_sub_kegiatan) {
                    return '3';
                } else {
                    return '1';
                }
            });

            if (kondisi.includes("2")) {
                alert('Tdk boleh memilih rekening dgn sumber dana yg sama dlm 1 no bku');
                return;
            }

            if (kondisi.includes("3")) {
                alert('Tdk boleh memilih kegiatan berbeda dlm 1 no bku');
                return;
            }

            if (!pembayaran) {
                alert('Jenis Pembayaran Tidak Boleh Kosong');
                return;
            }

            if (pembayaran == 'BANK' && (nilai > total_sisa)) {
                alert('Total Transaksi melebihi Sisa Simpanan Bank');
                return;
            }

            if (nilai == 0) {
                alert('Nilai Nol.....!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai > sisa_angkas) {
                alert('Nilai Transaksi melebihi Sisa Anggaran Kas');
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

            if (nilai > sisa_spd) {
                alert('Nilai Melebihi Sisa Anggaran SPD...!!!, Cek Lagi...!!!');
                return;
            }

            // if (beban == '1' && (akumulasi > sisa_spd)) {
            //     alert('Total Transaksi melebihi Sisa SPD');
            //     return;
            // }

            rekening_belanja.row.add({
                'no_bukti': no_bukti,
                'no_sp2d': no_sp2d,
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'kd_rek6': kd_rekening,
                'nm_rek6': nm_rekening,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'sumber': sumber,
                'lalu': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(lalu),
                'sp2d': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(sp2d),
                'anggaran': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(anggaran),
                'volume': volume,
                'satuan': satuan,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kd_rekening}','${sumber}','${nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();
            tabel_rekening.row.add({
                'no_bukti': no_bukti,
                'no_sp2d': no_sp2d,
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'kd_rek6': kd_rekening,
                'nm_rek6': nm_rekening,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'sumber': sumber,
                'lalu': lalu,
                'sp2d': sp2d,
                'anggaran': anggaran,
                'volume': volume,
                'satuan': satuan,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kd_rekening}','${sumber}','${nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();

            $('#total_input_rekening').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_input_rekening + nilai));
            $('#total_belanja').val(new Intl.NumberFormat(
                'id-ID', {
                    minimumFractionDigits: 2
                }).format(total_input_rekening + nilai));
        });

        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });

        $('#beban').on('select2:select', function() {
            rekening_belanja.clear().draw();
            tabel_rekening.clear().draw();
            $('#total_belanja').val(null);
            $('#total_input_rekening').val(null);
        });

        $('#rek_tujuan').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $("#nm_rekening_tujuan").val(nama);
        });

        $('#simpan_rekening_tujuan').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let rekening = document.getElementById('rekening').value;
            let nm_rekening_tujuan = document.getElementById('nm_rekening_tujuan').value;
            let rek_tujuan = document.getElementById('rek_tujuan').value;
            let bank = document.getElementById('bank').value;
            let total_belanja = rupiah(document.getElementById('total_belanja').value);
            let total_transfer = rupiah(document.getElementById('total_transfer').value);
            let total_potongan = rupiah(document.getElementById('total_potongan').value);
            let nilai_potongan = angka(document.getElementById('nilai_potongan').value);
            let nilai_transfer = angka(document.getElementById('nilai_transfer').value);

            let hasil_akumulasi = total_belanja - nilai_potongan;
            let akumulasi = total_transfer + nilai_transfer;

            if (!no_bukti) {
                alert("No Bukti harus diisi!");
                return;
            }

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

            rekening_tujuan.row.add({
                'no_bukti': no_bukti,
                'tgl_bukti': tgl_voucher,
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
            }).format(total_potongan + nilai_potongan));
            $('#total_transfer').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transfer + nilai_transfer));

            $('#nilai_potongan').val(null);
            $('#rek_tujuan').val(null).change();
            $('#nm_rekening_tujuan').val(null);
            $('#bank').val(null).change();
            $('#nilai_transfer').val(null);
            $('#modal_rekening').modal('hide');
        });

        $('#simpan_transaksi').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            let no_voucher = document.getElementById('no_voucher').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let keterangan = document.getElementById('keterangan').value;
            let beban = document.getElementById('beban').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let rekening = document.getElementById('rekening').value;
            let ketcms = document.getElementById('ketcms').value;

            let total_belanja = rupiah(document.getElementById('total_belanja').value);
            let total_sisa = rupiah(document.getElementById('total_sisa').value);
            let total_potongan = rupiah(document.getElementById('total_potongan').value);
            let total_transfer = rupiah(document.getElementById('total_transfer').value);

            let rincian_rekening = rekening_belanja.rows().data().toArray().map((value) => {
                let data = {
                    no_bukti: value.no_bukti,
                    no_sp2d: value.no_sp2d,
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    nm_sub_kegiatan: value.nm_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: rupiah(value.nilai),
                    sumber: value.sumber,
                    lalu: value.lalu,
                    sp2d: value.sp2d,
                    anggaran: value.anggaran,
                    volume: value.volume,
                    satuan: value.satuan,
                };
                return data;
            });

            if (!no_bukti) {
                alert('No bukti harus diisi!');
                return;
            }
            // if (beban == '1') {
            //     alert(
            //         'Pada transaski UP/GU hanya boleh 1 Rekening Belanja, Info lebih lanjut silahkan hubungi bidang Perbendaharaan'
            //     );
            // }

            // let r = confirm("Yakin Data akan disimpan ?");
            // if (r == true) {
            if (rincian_rekening.length == 0) {
                alert('Rincian Rekening tidak boleh kosong!');
                return;
            }

            let no_sp2d = rekening_belanja.rows().data().toArray().map((value) => {
                let data = {
                    no_sp2d: value.no_sp2d,
                };
                return data;
            });
            let sp2d = no_sp2d[0]['no_sp2d'];

            let rincian_rek_tujuan = rekening_tujuan.rows().data().toArray().map((value) => {
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

            let tahun_input = tgl_voucher.substr(0, 4);

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!pembayaran) {
                alert('Jenis Pembayaran Tidak Boleh Kosong');
                return;
            }

            if (pembayaran == 'BANK' && total_belanja > total_sisa) {
                alert('Nilai Melebihi sisa Simpanan Bank');
                return;
            }

            // if (!no_bukti) {
            //     alert('Nomor Bukti Tidak Boleh Kosong');
            //     return;
            // }

            if (!tgl_voucher) {
                alert('Tanggal Bukti Tidak Boleh Kosong');
                return;
            }

            if (!kd_skpd) {
                alert('Kode SKPD Tidak Boleh Kosong');
                return;
            }

            if (!beban) {
                alert('Jenis beban Tidak Boleh Kosong');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }

            if (total_belanja == '0') {
                alert('Rincian Tidak ada rekening!');
                return;
            }

            if (total_transfer > total_belanja) {
                alert('Total Transfer melebihi Total Belanja!');
                return;
            }

            if (!rekening) {
                alert('Isian Rekening Belum Lengkap!');
                return;
            }

            let response = {
                no_bukti,
                tgl_voucher,
                kd_skpd,
                nm_skpd,
                beban,
                keterangan,
                total_belanja,
                pembayaran,
                rekening,
                ketcms,
                sp2d,
                rincian_rekening,
                rincian_rek_tujuan
            };

            $('#simpan_transaksi').prop('disabled', true);
            $.ajax({
                url: "{{ route('skpd.transaksi_pemindahbukuan.simpan_transaksi') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: response
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil ditambahkan, dengan Nomor Bukti : ' + data
                            .no_bukti);
                        // window.location.href =
                        //     "{{ route('skpd.transaksi_pemindahbukuan.index') }}";
                        let potongan = confirm(
                            'Data Berhasil Tersimpan...!, Apakah Transaksi ini Terdapat Terima Potongan Pajak ?'
                        );
                        if (potongan == true) {
                            window.location.href =
                                "{{ route('skpd.potongan_pajak.index') }}";
                        } else {
                            window.location.href =
                                "{{ route('skpd.transaksi_pemindahbukuan.index') }}";
                        }
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan_transaksi').prop('disabled', false);
                        return;
                    }
                }
            })
            // } else {
            //     alert('Silahkan Periksa Lagi...');
            //     return;
            // }
        });

        function cari_nomor(kd_sub_kegiatan) {
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.nomor_sp2d') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    beban: document.getElementById('beban').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                    tgl: document.getElementById('tgl_voucher').value,
                },
                success: function(data) {
                    $('#no_sp2d').empty();
                    $('#no_sp2d').append(
                        `<option value="" disabled selected>Pilih Nomor SP2D</option>`);
                    $.each(data, function(index, data) {
                        $('#no_sp2d').append(
                            `<option value="${data.no_sp2d}" data-tgl="${data.tgl_sp2d}">${data.no_sp2d} | ${data.tgl_sp2d}</option>`
                        );
                    })
                }
            })
        }

        function cari_rekening(no_sp2d) {
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sp2d: no_sp2d,
                    no_bukti: document.getElementById('no_voucher').value,
                    beban: document.getElementById('beban').value,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                },
                success: function(data) {
                    let sp2d = 0;
                    let lalu = 0;
                    $.each(data, function(index, data) {
                        sp2d += parseFloat(data.sp2d);
                        lalu += parseFloat(data.lalu) || 0;
                    });
                    $('#total_sp2d').val(sp2d - lalu);

                    $('#kd_rekening').empty();
                    $('#kd_rekening').append(
                        `<option value="" disabled selected>Pilih Rekening</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_rekening').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}" data-sp2d="${data.sp2d}" data-anggaran="${data.anggaran}" data-lalu="${data.lalu}">${data.kd_rek6} | ${data.nm_rek6} | ${data.lalu}</option>`
                        );
                    })
                }
            })
        }

        function cari_sumber(kd_rek6) {
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.sumber') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_rek6: kd_rek6,
                    kd_skpd: document.getElementById('kd_skpd').value,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    no_sp2d: document.getElementById('no_sp2d').value,
                    beban: document.getElementById('beban').value,
                },
                success: function(data) {
                    $('#sumber').empty();
                    $('#sumber').append(
                        `<option value="" disabled selected>Pilih Sumber Dana</option>`);
                    $.each(data, function(index, data) {
                        $('#sumber').append(
                            `<option value="${data.sumber_dana}" data-anggaran="${data.nilai}">${data.nm_sumber} | ${parseFloat(data.nilai)}</option>`
                        );
                    })
                }
            })
        }

        function load_sisa_bank(potongan_ls) {
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.sisa_bank') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    let nilai = parseFloat(data) || 0;
                    let persen_kkpd = document.getElementById('persen_kkpd').value;
                    let persen_tunai = document.getElementById('persen_tunai').value;
                    let beban = document.getElementById('beban').value;
                    // let sisa_kas;
                    // if (beban == 1) {
                    //     sisa_kas = (persen_kkpd / 100) * nilai;
                    // } else {
                    //     sisa_kas = (persen_tunai / 100) * nilai;
                    // }
                    // $('#sisa_kas').val(new Intl.NumberFormat('id-ID', {
                    //     minimumFractionDigits: 2
                    // }).format(nilai));
                    if (beban != '1') {
                        let total_sp2d = rupiah(document.getElementById('total_sp2d').value);
                        $('#sisa_kas').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_sp2d - potongan_ls));
                        $('#total_sisa').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_sp2d - potongan_ls + potongan_ls));
                    } else {
                        $('#sisa_kas').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(nilai));
                        let sisa_kas = rupiah(document.getElementById('sisa_kas').value);
                        $('#total_sisa').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(sisa_kas + potongan_ls));
                    }
                }
            })
        }

        function load_potongan_ls(no_sp2d) {
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.potongan_ls') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sp2d: no_sp2d
                },
                success: function(data) {
                    let nilai = parseFloat(data) || 0;
                    $('#potongan_ls').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(nilai));
                    // let sisa_kas = rupiah(document.getElementById('sisa_kas').value);
                    // $('#total_sisa').val(new Intl.NumberFormat('id-ID', {
                    //     minimumFractionDigits: 2
                    // }).format(sisa_kas + nilai));
                    load_sisa_bank(nilai);
                }
            })
        }

        function load_angkas() {
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kd_rekening = document.getElementById('kd_rekening').value;
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            let beban = document.getElementById('beban').value;
            let status_angkas = document.getElementById('status_angkas').value;

            $.ajax({
                url: "{{ route('skpd.transaksi_cms.load_angkas') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    kd_skpd: kd_skpd,
                    kd_rekening: kd_rekening,
                    tgl_voucher: tgl_voucher,
                    beban: beban,
                    status_angkas: status_angkas,
                },
                success: function(data) {
                    $('#total_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                    load_angkas_lalu();
                }
            })
        }

        function load_angkas_lalu() {
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kd_rekening = document.getElementById('kd_rekening').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let tgl_voucher = document.getElementById('tgl_voucher').value;
            let beban = document.getElementById('beban').value;
            let sumber = document.getElementById('sumber').value;
            let total_angkas = rupiah(document.getElementById('total_angkas').value);
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.load_angkas_lalu') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    kd_skpd: kd_skpd,
                    kd_rekening: kd_rekening,
                    no_sp2d: no_sp2d,
                    tgl_voucher: tgl_voucher,
                    beban: beban,
                    sumber: sumber,
                },
                success: function(data) {
                    $('#realisasi_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total));
                    $('#realisasi_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total));
                    $('#sisa_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_angkas - data.total));
                    load_spd();
                }
            })
        }

        function load_spd() {
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kd_rekening = document.getElementById('kd_rekening').value;
            let bulan = document.getElementById('bulan').value;
            let realisasi_spd = rupiah(document.getElementById('realisasi_spd').value);

            $.ajax({
                url: "{{ route('skpd.transaksi_cms.load_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    kd_skpd: kd_skpd,
                    kd_rekening: kd_rekening,
                    bulan: bulan,
                },
                success: function(data) {
                    $('#total_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total));
                    $('#sisa_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total - realisasi_spd));
                }
            })
        }

        function load_dana(sumber) {
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.load_dana') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                    sumber: sumber,
                    kd_rekening: document.getElementById('kd_rekening').value,
                    no_sp2d: document.getElementById('no_sp2d').value,
                    beban: document.getElementById('beban').value,
                },
                success: function(data) {
                    $('#realisasi_sumber').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data));
                    let total_sumber = rupiah(document.getElementById('total_sumber').value);
                    $('#sisa_sumber').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_sumber - data));
                }
            })
        }

        function status_anggaran() {
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.status_ang') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $('#status_anggaran').val(data.nama);
                }
            })
        }

        function status_angkas() {
            let tanggal = document.getElementById('tgl_voucher').value;
            $.ajax({
                url: "{{ route('penagihan.cek_status_ang') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $('#status_angkas').val(data.status);
                }
            })
        }
    });

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

    function deleteData(no_bukti, kd_sub_kegiatan, kd_rek, sumber, nilai) {
        let tabel = $('#input_rekening').DataTable();
        let tabel1 = $('#rekening_belanja').DataTable();
        let nilai_rekening = parseFloat(nilai);
        let nilai_sementara = rupiah(document.getElementById('total_input_rekening').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek + '  Nilai :  ' + nilai +
            ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.sumber == sumber && data.kd_sub_kegiatan == kd_sub_kegiatan &&
                    data.kd_rek6 == kd_rek
            }).remove().draw();
            tabel1.rows(function(idx, data, node) {
                return data.sumber == sumber && data.kd_sub_kegiatan == kd_sub_kegiatan &&
                    data.kd_rek6 == kd_rek
            }).remove().draw();
            $('#total_input_rekening').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai_sementara - nilai_rekening));
            $('#total_belanja').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai_sementara - nilai_rekening));
        } else {
            return false;
        }

    }

    function deleteRek(no_bukti, rek_tujuan, nilai_transfer, nilai_potongan) {
        let tabel = $('#rekening_tujuan').DataTable();
        let potongan_sementara = rupiah(document.getElementById('total_potongan').value);
        let transfer_sementara = rupiah(document.getElementById('total_transfer').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + rek_tujuan + '  Nilai :  ' + nilai_transfer +
            ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.no_bukti == no_bukti && data.rekening_tujuan == rek_tujuan
            }).remove().draw();
            $('#total_potongan').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(potongan_sementara - nilai_potongan));
            $('#total_transfer').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(transfer_sementara - nilai_transfer));
        } else {
            return false;
        }

    }
</script>
