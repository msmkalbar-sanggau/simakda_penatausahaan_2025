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

        let tabel_rincian = $('#rincian_panjar').DataTable({
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
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        $('#tambah_rincian').on('click', function() {
            let beban = document.getElementById('beban').value;
            let no_kas = document.getElementById('no_kas').value;
            let tgl_kas = document.getElementById('tgl_kas').value;
            let kd_skpd = document.getElementById('kd_skpd').value;

            if (beban == '' || tgl_kas == '' || kd_skpd == '' || no_kas == '') {
                alert('Harap Isi Kode SKPD, Tanggal Transaksi & Jenis Beban SP2D')
                return;
            }
            $('#modal_rincian').modal('show');
        });

        $('#nopanjar').on('select2:select', function() {
            let no_panjar = this.value;
            let nilai = $(this).find(':selected').data('nilai');
            let kembali = $(this).find(':selected').data('kembali');

            $('#no_panjar').val(no_panjar);
            $('#total_panjar').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai));
            $('#kembali_panjar').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(kembali));

            $('#no_sp2d').empty();
            $('#kode_rekening').empty();
            $('#sumber').empty();

            $('#total_spd').val(null);
            $('#lalu_spd').val(null);
            $('#sisa_spd').val(null);

            $('#total_sumber').val(null);
            $('#lalu_sumber').val(null);
            $('#sisa_sumber').val(null);

            $('#total_angkas').val(null);
            $('#lalu_angkas').val(null);
            $('#sisa_angkas').val(null);

            $('#total_sp2d').val(null);
            $('#lalu_sp2d').val(null);
            $('#sisa_sp2d').val(null);

            $.ajax({
                url: "{{ route('transaksipanjar.kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_panjar: no_panjar,
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
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            let kd_sub_kegiatan = this.value;
            let beban = document.getElementById('beban').value;

            $.ajax({
                url: "{{ route('transaksipanjar.sp2d') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    beban: beban,
                },
                success: function(data) {
                    $('#no_sp2d').empty();
                    $('#no_sp2d').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#no_sp2d').append(
                            `<option value="${data.no_sp2d}">${data.no_sp2d} | ${data.tgl_sp2d}</option>`
                        );
                    })
                }
            });
        });

        $('#no_sp2d').on('select2:select', function() {
            let no_sp2d = this.value;
            let beban = document.getElementById('beban').value;

            $.ajax({
                url: "{{ route('transaksipanjar.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_panjar: document.getElementById('nopanjar').value,
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
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}" data-nilai="${data.nilai}" data-lalu="${data.lalu}" data-sp2d="${data.sp2d}" data-panjar_lalu="${data.panjar_lalu}">${data.kd_rek6} | ${data.nm_rek6} | ${new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2}).format(data.lalu)} | ${new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2}).format(data.panjar_lalu)} | ${new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2}).format(data.nilai)}</option>`
                        );
                    })
                }
            });
        });

        $('#kode_rekening').on('select2:select', function() {
            let kd_rek6 = this.value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let total_panjar = rupiah(document.getElementById('total_panjar').value);
            let kembali_panjar = rupiah(document.getElementById('kembali_panjar').value);
            let nilai = $(this).find(':selected').data('nilai');
            let lalu = $(this).find(':selected').data('lalu');
            let sp2d = $(this).find(':selected').data('sp2d');
            let panjar_lalu = $(this).find(':selected').data('panjar_lalu') || 0;

            $('#transaksi_panjar').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(panjar_lalu));

            $('#sisa_panjar').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(parseFloat(total_panjar) - parseFloat(panjar_lalu) - parseFloat(
                kembali_panjar)));

            let beban = document.getElementById('beban').value;

            let sisa_anggaran = 0;
            if (beban == '1') {
                sisa_anggaran = nilai - lalu;
                $('#total_sp2d').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai));
            } else {
                sisa_anggaran = sp2d - lalu;
                $('#total_sp2d').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(sp2d));
            }

            $('#lalu_sp2d').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(lalu));

            $('#sisa_sp2d').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(sisa_anggaran));

            // LOAD ANGKAS DAN SPD
            $.ajax({
                url: "{{ route('transaksipanjar.angkas_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    tgl_kas: document.getElementById('tgl_kas').value,
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
                url: "{{ route('transaksipanjar.sumber') }}",
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
                            `<option value="${data.sumber_dana}" data-nilai="${data.nilai}">${data.sumber_dana} | ${data.nm_sumber} | ${new Intl.NumberFormat('id-ID', {
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

        $('#no_panjar_lalu').on('select2:select', function() {
            let no_panjar = this.value;
            let tgl = $(this).find(':selected').data('tgl');
            let no_panjar_lalu = $(this).find(':selected').data('no_panjar_lalu');
            $('#tgl_panjar_lalu').val(tgl);
            $('#no_panjar_lalu1').val(no_panjar_lalu);

            load_data(no_panjar, no_panjar_lalu);
        });

        $('#simpan_rincian').on('click', function() {
            let no_kas = document.getElementById('no_kas').value;
            let tgl_kas = document.getElementById('tgl_kas').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kode_rekening = document.getElementById('kode_rekening').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let sumber = document.getElementById('sumber').value;
            let status_anggaran = document.getElementById('status_anggaran').value;
            let beban = document.getElementById('beban').value;

            let sisa_panjar = rupiah(document.getElementById('sisa_panjar').value);
            let sisa_sp2d = rupiah(document.getElementById('sisa_sp2d').value);
            let sisa_spd = rupiah(document.getElementById('sisa_spd').value);
            let sisa_sumber = rupiah(document.getElementById('sisa_sumber').value);
            let total_rincian = rupiah(document.getElementById('total_rincian').value);
            let nilai = angka(document.getElementById('nilai').value);

            let sub_kegiatan = $('#kd_sub_kegiatan').find('option:selected');
            let nm_sub_kegiatan = sub_kegiatan.data('nama');

            let rekening = $('#kode_rekening').find('option:selected');
            let nm_rek6 = rekening.data('nama');
            let anggaran = rekening.data('nilai');
            let lalu = rekening.data('lalu');
            let sp2d = rekening.data('sp2d');

            let tahun_input = tgl_kas.substr(0, 4);

            if (sumber == '221020101') {
                alert(
                    'Silahkan konfirmasi ke perbendaharaan jika ingin transaksi sumber dana DID, jika tidak maka transaksi tidak bisa di approve oleh perbendahaaraan, terima kasih'
                );
            }

            let akumulasi = total_rincian + nilai;

            if (!no_sp2d) {
                alert("No sp2d kosong");
                return;
            }

            if (!sumber) {
                alert("Sumber Dana kosong");
                return;
            }

            if (!kode_rekening) {
                alert("Rekening kosong");
                return;
            }

            if (!kd_sub_kegiatan) {
                alert("Kegiatan kosong");
                return;
            }

            if (nilai == 0) {
                alert('Nilai Nol.....!!!, Cek Lagi...!!!');
                return;
            }

            if (nilai > sisa_panjar) {
                alert('Transaksi melebihi Sisa Panjar');
                return;
            }

            if (akumulasi > sisa_panjar) {
                alert('Total Transaksi melebihi Sisa Panjar');
                return;
            }

            if ((status_anggaran == 'Penyusunan') && (nilai > sisa_sp2d)) {
                alert('Nilai Melebihi Sisa Anggaran Penyusunan...!!!, Cek Lagi...!!!');
                return;
            }

            if ((beban == '1') && (akumulasi > sisa_spd)) {
                alert('Total Transaksi melebihi Sisa SPD');
                return;
            }

            // proses input ke tabel input detail spp
            alert('Data Detail Tersimpan');
            tabel_rincian.row.add({
                'no_bukti': no_kas,
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
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_kas}','${kd_sub_kegiatan}','${kode_rekening}','${nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            input_rincian.row.add({
                'no_bukti': no_kas,
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
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_kas}','${kd_sub_kegiatan}','${kode_rekening}','${nilai}')" class="btn btn-danger btn-sm"><i class="uil-trash"></i></a>`,
            }).draw();
            $("#total").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(akumulasi));
            $("#total_rincian").val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(akumulasi));
        });

        $('#simpan').on('click', function() {
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let no_kas = document.getElementById('no_kas').value;
            let tgl_kas = document.getElementById('tgl_kas').value;
            let no_panjar = document.getElementById('no_panjar').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let beban = document.getElementById('beban').value;
            let pembayaran = document.getElementById('pembayaran').value;
            let total = rupiah(document.getElementById('total').value);
            let total_rincian = rupiah(document.getElementById('total_rincian').value);
            let tahun_input = tgl_bukti.substr(0, 4);


            alert('Nomor Panjar :' + no_panjar);

            if (!no_kas) {
                alert('No Kas Tidak Boleh Kosong');
                return;
            }

            if (!no_bukti) {
                alert('No Kas Tidak Boleh Kosong');
                return;
            }

            if (!tgl_bukti) {
                alert('Tanggal Bukti Tidak Boleh Kosong');
                return;
            }

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
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

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
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
                };
                return data;
            });

            if (rincian_rekening.length == 0) {
                alert('Rincian Tidak ada rekening!');
                return;
            }

            let data = {
                no_bukti,
                tgl_bukti,
                no_kas,
                tgl_kas,
                kd_skpd,
                nm_skpd,
                keterangan,
                beban,
                total,
                pembayaran,
                no_panjar,
                rincian_rekening
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('transaksipanjar.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil disimpan!');
                        window.location.href =
                            "{{ route('transaksipanjar.index') }}";
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

    function deleteData(no_kas, kd_sub_kegiatan, kd_rek6, nilai) {
        let tabel = $('#rincian_panjar').DataTable();
        let tabel1 = $('#input_rincian').DataTable();
        let total = rupiah(document.getElementById('total').value);
        let total_rincian = rupiah(document.getElementById('total_rincian').value);
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai :  ' + nilai +
            ' ?');
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.kd_sub_kegiatan == kd_sub_kegiatan && data.no_bukti == no_kas && data.kd_rek6 ==
                    kd_rek6
            }).remove().draw();
            tabel1.rows(function(idx, data, node) {
                return data.kd_sub_kegiatan == kd_sub_kegiatan && data.no_bukti == no_kas && data.kd_rek6 ==
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
</script>
