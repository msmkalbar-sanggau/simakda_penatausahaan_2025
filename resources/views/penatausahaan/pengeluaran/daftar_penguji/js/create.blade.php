<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let rincian_penguji = $('#rincian_penguji').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_sp2d',
                    name: 'no_sp2d',
                },
                {
                    data: 'tgl_sp2d',
                    name: 'tgl_sp2d',
                },
                {
                    data: 'no_spm',
                    name: 'no_spm',
                },
                {
                    data: 'tgl_spm',
                    name: 'tgl_spm',
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'bank',
                    name: 'bank',
                    visible: false
                },
                {
                    data: 'bic',
                    name: 'bic',
                    visible: false
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        cari_sp2d();

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#no_sp2d').on('select2:select', function() {
            let sp2d_online = document.getElementById('sp2d_online').value;
            if (!sp2d_online) {
                alert('Silahkan Pilih SP2D Online!');
                $('#no_sp2d').val(null).change();
                return;
            }
            let no_sp2d = this.value;
            let tgl_sp2d = $(this).find(':selected').data('tgl_sp2d');
            let no_spm = $(this).find(':selected').data('no_spm');
            let tgl_spm = $(this).find(':selected').data('tgl_spm');
            let nilai = $(this).find(':selected').data('nilai');
            let bank = $(this).find(':selected').data('bank');
            let jns_spp = $(this).find(':selected').data('jns_spp');
            let jenis_beban = $(this).find(':selected').data('jenis_beban');
            let bic = $(this).find(':selected').data('bic').trim();

            if ((sp2d_online == '1' && jns_spp == '4' && jenis_beban == '1') || (sp2d_online == '1' &&
                    jns_spp == '4' &&
                    jenis_beban == '10')) {
                alert('SP2D Gaji dan Tunjangan Tidak Dapat SP2D Online');
                return;
            }

            let tampungan = rincian_penguji.rows().data().toArray().map((value) => {
                let result = {
                    no_sp2d: value.no_sp2d,
                    tgl_sp2d: value.tgl_sp2d,
                    no_spm: value.no_spm,
                    tgl_spm: value.tgl_spm,
                    bank: value.bank,
                    bic: value.bic,
                };
                return result;
            });

            let daftar_bic = ["PDKBIDJ1"];

            // let daftar_bic2 = ["BSMDIDJA", "PDKBIDJ1", "SYKBIDJ1"];
            let kondisi = tampungan.map(function(data) {
                if (data.no_sp2d == no_sp2d && data.no_spm == no_spm) {
                    return '1';
                }
                // Perbaikan terbaru
                if (daftar_bic.includes(data.bic) == true) {
                    if (daftar_bic.includes(bic) == false) {
                        return '2';
                    }
                }

                if (daftar_bic.includes(data.bic) == false) {
                    if (daftar_bic.includes(bic) == true) {
                        return '3';
                    }
                }
            });
            if (kondisi.includes("1")) {
                alert('Nomor SP2D ini sudah ada di LIST!');
                $("#no_sp2d").val(null).change();
                return;
            }

            if (kondisi.includes("2")) {
                alert('Dilist sudah ada Bank Kalbar,Tidak boleh pakai Bank Lain!');
                $("#no_sp2d").val(null).change();
                return;
            }

            if (kondisi.includes("3")) {
                alert('Dilist sudah ada Selain Bank Kalbar,Tidak boleh pakai Bank Kalbar!');
                $("#no_sp2d").val(null).change();
                return;
            }

            rincian_penguji.row.add({
                'no_sp2d': no_sp2d,
                'tgl_sp2d': tgl_sp2d,
                'no_spm': no_spm,
                'tgl_spm': tgl_spm,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai),
                'bank': bank,
                'bic': bic,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_sp2d}','${tgl_sp2d}','${no_spm}','${tgl_spm}','${nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();
            $("#no_sp2d").val(null).change();
            $('#sp2d_online').prop('disabled', true);

            cari_sp2d();
        });

        $('#simpan_penguji').on('click', function() {
            let no_advice = document.getElementById('no_advice').value;
            let tanggal = document.getElementById('tanggal').value;
            let sp2d_online = document.getElementById('sp2d_online').value;
            if (!tanggal) {
                alert('Tanggal Bukti Tidak Boleh Kosong');
                return;
            }
            if (!sp2d_online) {
                alert('SP2D Online Tidak Boleh Kosong');
                return;
            }
            let rincian = rincian_penguji.rows().data().toArray();
            if (rincian.length == 0) {
                alert('Detail Daftar Penguji tidak boleh kosong!');
                return;
            }

            let detail_penguji = rincian_penguji.rows().data().toArray().map((value) => {
                let data = {
                    no_sp2d: value.no_sp2d,
                    tgl_sp2d: value.tgl_sp2d,
                    no_spm: value.no_spm,
                    tgl_spm: value.tgl_spm,
                    nilai: angka(value.nilai),
                };
                return data;
            });
            // simpan daftar penguji
            $('#simpan_penguji').prop("disabled", true);
            $.ajax({
                url: "{{ route('daftar_penguji.simpan_penguji') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_advice: no_advice,
                    tanggal: tanggal,
                    sp2d_online: sp2d_online,
                },
                success: function(data) {
                    let nomor_baru = data.no_uji;
                    if (data.message == '1') {
                        alert('Data berhasil ditambahkan, Nomor Baru yang tersimpan adalah: ' +
                            nomor_baru);
                        $.ajax({
                            url: "{{ route('daftar_penguji.simpan_detail_penguji') }}",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                nomor_baru: nomor_baru,
                                tanggal: tanggal,
                                detail_penguji: detail_penguji
                            },
                            success: function(data) {
                                if (data.message == '1') {
                                    alert('Data berhasil ditambahkan');
                                    window.location.href =
                                        "{{ route('daftar_penguji.index') }}";
                                } else {
                                    alert('Data tidak berhasil ditambahkan!');
                                    $('#simpan_penguji').prop("disabled",
                                        false);
                                    return;
                                }
                            }
                        })
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan_penguji').prop("disabled", false);
                        return;
                    }
                }
            })
        });

        function angka(data) {
            let n1 = data.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }

    });

    function cari_sp2d() {
        let rincian_penguji = $('#rincian_penguji').DataTable();
        let rincian = rincian_penguji.rows().data().toArray().map((value) => {
            let data = {
                no_sp2d: value.no_sp2d,
            };
            return data;
        });

        $.ajax({
            url: "{{ route('daftar_penguji.load_sp2d') }}",
            type: "POST",
            dataType: 'json',
            data: {
                sp2d: rincian.length == 0 ? '0' : rincian
            },
            success: function(data) {
                $('#no_sp2d').empty();
                $('#no_sp2d').append(
                    `<option value="" disabled selected>Silahkan Pilih</option>`);
                $.each(data, function(index, data) {
                    $('#no_sp2d').append(
                        `<option value="${data.no_sp2d}" data-tgl_sp2d="${data.tgl_sp2d}" data-no_spm="${data.no_spm}" data-tgl_spm="${data.tgl_spm}" data-nilai="${data.nilai}" data-bank="${data.bank}" data-nmbank="{data.nmbank}" data-bic="${data.bic}" data-jns_spp="${data.jns_spp}" data-jenis_beban="${data.jenis_beban}" data-jenis="${data.jenis}">${data.no_sp2d} | ${data.tgl_sp2d} | ${data.nmbank} | ${data.nm_skpd}</option>`
                    );
                })
            }
        })
    }


    function deleteData(no_sp2d, tgl_sp2d, no_spm, tgl_spm, nilai) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor SP2D : ' + no_sp2d);
        let tabel = $('#rincian_penguji').DataTable();
        if (tanya == true) {
            tabel.rows(function(idx, data, node) {
                return data.no_sp2d == no_sp2d && data.no_spm == no_spm
            }).remove().draw();
            cari_sp2d()
        } else {
            return false;
        }
    }
</script>
