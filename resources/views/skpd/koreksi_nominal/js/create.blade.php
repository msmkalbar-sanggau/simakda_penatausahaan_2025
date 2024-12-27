<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#kd_rekening_koreksi').prop('disabled', true);
        $('#sumber_koreksi').prop('disabled', true);

        let rincian = $('#rincian').DataTable({
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
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        let rincian_inputan = $('#rincian_inputan').DataTable({
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

        $('#tambah_rekening').on('click', function() {
            let beban = document.getElementById('beban').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tgl_transaksi = document.getElementById('tgl_transaksi').value;
            let tgl_koreksi = document.getElementById('tgl_koreksi').value;
            if (beban && kd_skpd && tgl_transaksi && tgl_koreksi) {
                status_anggaran();
                status_angkas();
                $('#modal_rekening').modal('show');
            } else {
                Swal.fire({
                    title: 'Harap Isi Kode SKPD, Tanggal Transaksi, Tanggal Koreksi & Jenis Beban',
                    confirmButtonColor: '#5b73e8',
                })
            }
        });

        // TRANSAKSI AWAL
        $('#kd_sub_kegiatan_awal').on('select2:select', function() {
            let kd_sub_kegiatan = this.value;
            let nm_sub_kegiatan = $(this).find(':selected').data('nama');

            $('#nm_sub_kegiatan_awal').val(nm_sub_kegiatan);
            $('#kd_sub_kegiatan_koreksi').val(kd_sub_kegiatan);
            $('#nm_sub_kegiatan_koreksi').val(nm_sub_kegiatan);

            $('#no_sp2d_koreksi').val(null);
            $('#kd_rekening_koreksi').val(null);
            $('#nm_rekening_koreksi').val(null);
            $('#sumber_koreksi').val(null);
            $('#nm_sumber_koreksi').val(null);
            $('#kd_sumber_koreksi').val(null);

            $('#no_sp2d_awal').empty();
            $('#kd_rekening_awal').empty();
            $('#sumber_awal').empty();
            $('#nm_rekening_awal').val(null);
            $('#nm_sumber_awal').val(null);
            $('#kd_sumber_awal').val(null);
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
            cari_nomor_koreksi(kd_sub_kegiatan);
        });

        $('#no_sp2d_awal').on('select2:select', function() {
            let no_sp2d = this.value;
            let beban = document.getElementById('beban').value;
            $('#no_sp2d_koreksi').val(no_sp2d);

            $('#kd_rekening_koreksi').val(null);
            $('#nm_rekening_koreksi').val(null);
            $('#sumber_koreksi').val(null);
            $('#nm_sumber_koreksi').val(null);
            $('#kd_sumber_koreksi').val(null);

            $('#kd_rekening_awal').empty();
            $('#sumber_awal').empty();
            $('#nm_rekening_awal').val(null);
            $('#nm_sumber_awal').val(null);
            $('#kd_sumber_awal').val(null);

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
            cari_rekening_koreksi(no_sp2d);
        });

        $('#kd_rekening_awal').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            let no_bukti = parseFloat($(this).find(':selected').data('no_bukti'));
            // let sp2d = parseFloat($(this).find(':selected').data('sp2d')) || 0;
            // let anggaran = parseFloat($(this).find(':selected').data('anggaran')) || 0;
            // let lalu = parseFloat($(this).find(':selected').data('lalu')) || 0;
            $('#nm_rekening_koreksi').val(nama);
            let kd_rek6 = this.value;
            $('#kd_rekening_koreksi').val(kd_rek6).change();

            let rekening = $('#kd_rekening_koreksi').find('option:selected');
            let sp2d = rekening.data('sp2d');
            let anggaran = rekening.data('anggaran');
            let lalu = rekening.data('lalu');

            let beban = document.getElementById('beban').value;

            $('#kd_rekening_koreksi').val(kd_rek6);
            $('#nm_rekening_koreksi').val(nama);
            $('#nm_rekening_awal').val(nama);

            $('#sumber_koreksi').val(null);
            $('#nm_sumber_koreksi').val(null);
            $('#kd_sumber_koreksi').val(null);

            $('#sumber_awal').empty();
            $('#nm_sumber_awal').val(null);
            $('#kd_sumber_awal').val(null);

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

            let sisa = 0;
            if (beban == '1' || beban == 1) {
                sisa = lalu < 0 ? anggaran + lalu : anggaran - lalu;
                $('#total_anggaran').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(anggaran));
            } else {
                sisa = lalu < 0 ? sp2d + lalu : sp2d - lalu;
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
            cari_sumber_koreksi(kd_rek6, no_bukti);
        });

        $('#sumber_awal').on('select2:select', function() {
            let sumber = this.value.trim();
            let nilai = $(this).find(':selected').data('nilai');

            $('#sumber_koreksi').val(sumber).change();

            let sumber1 = $('#sumber_koreksi').find('option:selected');
            let nilai_sumber = sumber1.data('nilai_sumber');

            // $('#sumber_koreksi').val(sumber);
            $('#nilai_sumber_awal').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
            $.ajax({
                url: "{{ route('penagihan.cari_nama_sumber') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    sumber_dana: sumber
                },
                success: function(data) {
                    $('#nm_sumber_awal').val(data.nm_sumber_dana1);
                    $('#kd_sumber_awal').val(data.kd_sumber_dana1);
                    $('#nm_sumber_koreksi').val(data.nm_sumber_dana1);
                    $('#kd_sumber_koreksi').val(data.kd_sumber_dana1);
                }
            });

            $('#total_sumber').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai_sumber));
            $('#realisasi_sumber').val(null);
            $('#sisa_sumber').val(null);
            load_dana(sumber);
        });

        $('#tambah_transaksi_awal').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan_awal').value;
            let nm_sub_kegiatan = document.getElementById('nm_sub_kegiatan_awal').value;
            let no_sp2d = document.getElementById('no_sp2d_awal').value;
            let kd_rekening = document.getElementById('kd_rekening_awal').value;
            let sumber = document.getElementById('kd_sumber_awal').value;
            let beban = document.getElementById('beban').value;
            let nm_rekening = document.getElementById('nm_rekening_awal').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let nilai = rupiah(document.getElementById('nilai_sumber_awal').value);
            let total = rupiah(document.getElementById('total').value);

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

            if (nilai == 0) {
                alert('Nilai Nol.....!!!, Cek Lagi...!!!');
                return;
            }

            let tampungan = rincian_inputan.rows().data().toArray().map((value) => {
                let result = {
                    nilai: rupiah(value.nilai),
                };
                return result;
            });

            let kondisi = tampungan.map(function(data) {
                if (data.nilai < 0) {
                    return '1';
                }
            });

            if (kondisi.includes("1")) {
                alert('Rincian hanya boleh 1 transaksi awal!');
                return;
            }

            rincian.row.add({
                'no_bukti': no_bukti,
                'no_sp2d': no_sp2d,
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'kd_rek6': kd_rekening,
                'nm_rek6': nm_rekening,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai * -1),
                'sumber': sumber,
                'lalu': 0,
                'sp2d': 0,
                'anggaran': 0,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kd_rekening}','${sumber}','${nilai * -1}','${no_sp2d}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            rincian_inputan.row.add({
                'no_bukti': no_bukti,
                'no_sp2d': no_sp2d,
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'kd_rek6': kd_rekening,
                'nm_rek6': nm_rekening,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai * -1),
                'sumber': sumber,
                'lalu': 0,
                'sp2d': 0,
                'anggaran': 0,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kd_rekening}','${sumber}','${nilai * -1}','${no_sp2d}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();

            $('#total_input_rekening').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total + nilai * -1));
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total + nilai * -1));
            $('#nilai_atas').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
            $('#kd_sub_kegiatan_awal').val(null).change();
            $('#no_sp2d_awal').empty();
            $('#kd_rekening_awal').empty();
            $('#sumber_awal').empty();
            $('#nm_sub_kegiatan_awal').val(null);
            $('#nm_rekening_awal').val(null);
            $('#nm_sumber_awal').val(null);
            $('#kd_sumber_awal').val(null);
            $('#nilai_sumber_awal').val(null);
        });

        // Simpan Transaksi Koreksi

        $('#simpan_transaksi_koreksi').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan_koreksi').value;
            let nm_sub_kegiatan = document.getElementById('nm_sub_kegiatan_koreksi').value;
            let no_sp2d = document.getElementById('no_sp2d_koreksi').value;
            let kd_rekening = document.getElementById('kd_rekening_koreksi').value;
            let sumber = document.getElementById('kd_sumber_koreksi').value;
            let nama_sumber = document.getElementById('nm_sumber_koreksi').value;
            let beban = document.getElementById('beban').value;
            let nm_rekening = document.getElementById('nm_rekening_koreksi').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let status_anggaran = document.getElementById('status_anggaran').value;

            let sisa_anggaran = rupiah(document.getElementById('sisa_anggaran').value);
            let nilai = angka(document.getElementById('nilai').value);
            let sisa_spd = rupiah(document.getElementById('sisa_spd').value);
            let total_input_rekening = rupiah(document.getElementById('total_input_rekening').value);
            let sisa_sumber = rupiah(document.getElementById('sisa_sumber').value);
            let sisa_angkas = rupiah(document.getElementById('sisa_angkas').value);
            let total_anggaran = rupiah(document.getElementById('total_anggaran').value);
            let realisasi_anggaran = rupiah(document.getElementById('realisasi_anggaran').value);
            let total_sumber = rupiah(document.getElementById('total_sumber').value);
            let realisasi_sumber = rupiah(document.getElementById('realisasi_sumber').value);
            let nilai_atas = rupiah(document.getElementById('nilai_atas').value);

            let akumulasi = nilai + total_input_rekening;
            let sisa_ang = total_anggaran - (realisasi_anggaran + nilai);
            let sisa_susun = total_sumber - (nilai + realisasi_sumber);

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

            let tampungan = rincian.rows().data().toArray().map((value) => {
                let result = {
                    nilai: rupiah(value.nilai),
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.nilai > 0) {
                    return '1';
                }
            });
            if (kondisi.length == 0) {
                alert('Silahkan Pilih Transaksi Awal Terlebih Dahulu!');
                return;
            }
            if (kondisi.includes("1")) {
                alert('Transaksi Koreksi hanya boleh 1!!!');
                return;
            }

            if (nilai == 0) {
                alert('Nilai Nol.....!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai > (sisa_angkas + nilai_atas)) {
                alert('Nilai Koreksi melebihi Sisa Anggaran Kas');
                return;
            }

            if (nilai > (sisa_spd + nilai_atas)) {
                alert('Nilai koreksi melebihi sisa SPD!');
                return;
            }

            if (sisa_ang < 0) {
                alert('Nilai Koreksi Melebihi Anggaran Penyusunan!!!, Cek Lagi...!!!');
                return;
            }

            if (sisa_susun < 0) {
                alert("Nilai " + nama_sumber +
                    " Melebihi Sisa Sumber Dana Penyusunan!!!, Cek Lagi...!!!");
                return;
            }

            rincian.row.add({
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
                'lalu': 0,
                'sp2d': 0,
                'anggaran': 0,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kd_rekening}','${sumber}','${nilai}','${no_sp2d}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            rincian_inputan.row.add({
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
                'lalu': 0,
                'sp2d': 0,
                'anggaran': 0,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${kd_sub_kegiatan}','${kd_rekening}','${sumber}','${nilai}','${no_sp2d}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();

            $('#total_input_rekening').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_input_rekening + nilai));
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_input_rekening + nilai));
            $('#kd_sub_kegiatan_koreksi').val(null);
            $('#nm_sub_kegiatan_koreksi').val(null);
            $('#no_sp2d_koreksi').val(null);
            $('#kd_rekening_koreksi').val(null);
            $('#nm_rekening_koreksi').val(null);
            $('#sumber_koreksi').val(null);
            $('#nm_sumber_koreksi').val(null);
            $('#kd_sumber_koreksi').val(null);

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
            $('#nilai').val(null);
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
            rincian.clear().draw();
            rincian_inputan.clear().draw();
            $('#total_belanja').val(null);
            $('#total_input_rekening').val(null);
        });

        $('#simpan_koreksi').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_transaksi = document.getElementById('tgl_transaksi').value;
            let tgl_koreksi = document.getElementById('tgl_koreksi').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let keterangan = document.getElementById('keterangan').value;
            let beban = document.getElementById('beban').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;

            let total = rupiah(document.getElementById('total').value);

            let rincian_rekening = rincian.rows().data().toArray().map((value) => {
                let data = {
                    no_bukti: value.no_bukti,
                    no_sp2d: value.no_sp2d,
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    nm_sub_kegiatan: value.nm_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: rupiah(value.nilai),
                    sumber: value.sumber,
                };
                return data;
            });

            if (rincian_rekening.length == 0) {
                alert('Rincian Koreksi tidak boleh kosong!');
                return;
            }

            let tahun_input = tgl_transaksi.substr(0, 4);

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!pembayaran) {
                alert('Jenis Pembayaran Tidak Boleh Kosong');
                return;
            }

            if (!tgl_transaksi) {
                alert('Tanggal Transaksi Tidak Boleh Kosong');
                return;
            }

            if (!tgl_koreksi) {
                alert('Tanggal Koreksi Tidak Boleh Kosong');
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

            // if (total != 0) {
            //     alert('Nilai Koreksi masih ada sisa');
            //     return;
            // }

            let response = {
                tgl_transaksi,
                tgl_koreksi,
                kd_skpd,
                nm_skpd,
                beban,
                keterangan,
                total,
                pembayaran,
                rincian_rekening,
            };

            $('#simpan_koreksi').prop('disabled', true);
            $.ajax({
                url: "{{ route('koreksi_nominal.simpan_koreksi') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: response
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil ditambahkan, dengan Nomor Bukti : ' + data
                            .no_bukti);
                        window.location.href =
                            "{{ route('koreksi_nominal.index') }}";
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan_koreksi').prop('disabled', false);
                    }
                }
            })
        });
        // TRANSAKSI AWAL
        function cari_nomor_koreksi(kd_sub_kegiatan) {
            $.ajax({
                url: "{{ route('koreksi_rekening.no_sp2d') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    beban: document.getElementById('beban').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                },
                success: function(data) {
                    $('#no_sp2d_awal').empty();
                    $('#no_sp2d_awal').append(
                        `<option value="" disabled selected>Pilih Nomor SP2D</option>`);
                    $.each(data, function(index, data) {
                        $('#no_sp2d_awal').append(
                            `<option value="${data.no_sp2d}">${data.no_sp2d}</option>`
                        );
                    })
                }
            })
        }

        function cari_rekening_koreksi(no_sp2d) {
            $.ajax({
                url: "{{ route('koreksi_rekening.rekening_koreksi') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sp2d: no_sp2d,
                    beban: document.getElementById('beban').value,
                    no_bukti: document.getElementById('no_bukti').value,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan_koreksi').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                },
                success: function(data) {
                    let rekening_awal = data.rekening_awal;
                    let rekening_koreksi = data.rekening_koreksi;
                    // data-lalu="${data.lalu}" data-anggaran="${data.anggaran}" data-sp2d="${data.sp2d}"
                    $('#kd_rekening_awal').empty();
                    $('#kd_rekening_awal').append(
                        `<option value="" disabled selected>Pilih Rekening</option>`);
                    $.each(rekening_awal, function(index, rekening_awal) {
                        $('#kd_rekening_awal').append(
                            `<option value="${rekening_awal.kd_rek6}" data-nama="${rekening_awal.nm_rek6}" data-no_bukti="${rekening_awal.no_bukti}">${rekening_awal.no_bukti} | ${rekening_awal.kd_rek6} | ${rekening_awal.nm_rek6} | ${rekening_awal.nilai}</option>`
                        );
                    });
                    $.each(rekening_koreksi, function(index, rekening_koreksi) {
                        $('#kd_rekening_koreksi').append(
                            `<option value="${rekening_koreksi.kd_rek6}" data-nama="${rekening_koreksi.nm_rek6}"
                            data-lalu="${rekening_koreksi.lalu}" data-anggaran="${rekening_koreksi.anggaran}" data-sp2d="${rekening_koreksi.sp2d}"
                            >${rekening_koreksi.kd_rek6} | ${rekening_koreksi.nm_rek6} | ${rekening_koreksi.lalu}</option>`
                        );
                    })
                }
            })
        }

        function cari_sumber_koreksi(kd_rek6, no_bukti) {
            $.ajax({
                url: "{{ route('koreksi_rekening.sumber_koreksi') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_rek6: kd_rek6,
                    no_bukti: no_bukti,
                    kd_skpd: document.getElementById('kd_skpd').value,
                    tgl_koreksi: document.getElementById('tgl_koreksi').value,
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan_koreksi').value,
                    no_sp2d: document.getElementById('no_sp2d_koreksi').value,
                    beban: document.getElementById('beban').value,
                },
                success: function(data) {
                    let sumber_awal = data.sumber_awal;
                    let sumber_koreksi = data.sumber_koreksi;

                    $('#sumber_awal').empty();
                    $('#sumber_awal').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(sumber_awal, function(index, sumber_awal) {
                        $('#sumber_awal').append(
                            `<option value="${sumber_awal.sumber.trim()}" data-nilai="${sumber_awal.nilai}">${sumber_awal.sumber} | ${sumber_awal.nilai}</option>`
                        );
                    })
                    $.each(sumber_koreksi, function(index, sumber_koreksi) {
                        $('#sumber_koreksi').append(
                            `<option value="${sumber_koreksi.sumber.trim()}" data-nilai_sumber="${sumber_koreksi.nilai_sumber}">${sumber_koreksi.sumber} | ${sumber_koreksi.nilai_sumber}</option>`
                        );
                    })
                }
            })
        }

        function load_angkas() {
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan_koreksi').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kd_rekening = document.getElementById('kd_rekening_koreksi').value;
            let tgl_koreksi = document.getElementById('tgl_koreksi').value;
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
                    tgl_voucher: tgl_koreksi,
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
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan_koreksi').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kd_rekening = document.getElementById('kd_rekening_koreksi').value;
            let no_sp2d = document.getElementById('no_sp2d_koreksi').value;
            let tgl_koreksi = document.getElementById('tgl_koreksi').value;
            let beban = document.getElementById('beban').value;
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
                    tgl_voucher: tgl_koreksi,
                    beban: beban,
                },
                success: function(data) {
                    $('#realisasi_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total));
                    $('#realisasi_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total));
                    let total = parseFloat(data.total) || 0;
                    if (total < 0) {
                        $('#sisa_angkas').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_angkas + total));
                    } else {
                        $('#sisa_angkas').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_angkas - total));
                    }
                    load_spd();
                }
            })
        }

        function load_spd() {
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan_koreksi').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let kd_rekening = document.getElementById('kd_rekening_koreksi').value;
            let realisasi_spd = rupiah(document.getElementById('realisasi_spd').value);

            $.ajax({
                url: "{{ route('skpd.transaksi_cms.load_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    kd_skpd: kd_skpd,
                    kd_rekening: kd_rekening,
                },
                success: function(data) {
                    $('#total_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total));
                    let total = parseFloat(data.total);
                    if (realisasi_spd < 0) {
                        $('#sisa_spd').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total + realisasi_spd));
                    } else {
                        $('#sisa_spd').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total - realisasi_spd));
                    }
                }
            })
        }

        function load_dana(sumber) {
            $.ajax({
                url: "{{ route('skpd.transaksi_cms.load_dana') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: document.getElementById('kd_sub_kegiatan_koreksi').value,
                    kd_skpd: document.getElementById('kd_skpd').value,
                    sumber: sumber,
                    kd_rekening: document.getElementById('kd_rekening_koreksi').value,
                    no_sp2d: document.getElementById('no_sp2d_koreksi').value,
                    beban: document.getElementById('beban').value,
                },
                success: function(data) {
                    $('#realisasi_sumber').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data));
                    let total_sumber = rupiah(document.getElementById('total_sumber').value);
                    let total = parseFloat(data);
                    if (total < 0) {
                        $('#sisa_sumber').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_sumber + total));
                    } else {
                        $('#sisa_sumber').val(new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(total_sumber - total));
                    }
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
            let tanggal = document.getElementById('tgl_transaksi').value;
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

    function deleteData(no_bukti, kd_sub_kegiatan, kd_rek, sumber, nilai, no_sp2d) {
        let tabel = $('#rincian').DataTable();
        let tabel1 = $('#rincian_inputan').DataTable();
        let nilai_rekening = parseFloat(nilai);
        let nilai_sementara = rupiah(document.getElementById('total_input_rekening').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek + '  Nilai :  ' + nilai +
            ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.sumber == sumber && data.kd_sub_kegiatan == kd_sub_kegiatan &&
                    data.kd_rek6 == kd_rek && data.no_sp2d == no_sp2d
            }).remove().draw();
            tabel1.rows(function(idx, data, node) {
                return data.sumber == sumber && data.kd_sub_kegiatan == kd_sub_kegiatan &&
                    data.kd_rek6 == kd_rek && data.no_sp2d == no_sp2d
            }).remove().draw();
            $('#total_input_rekening').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai_sementara - nilai_rekening));
            $('#total').val(new Intl.NumberFormat('id-ID', {
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
