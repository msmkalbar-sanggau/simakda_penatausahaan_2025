<script>
    $(document).ready(function() {
        let thn_anggaran = "{{ tahun_anggaran() }}";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let tabel_spm = $('#rincian_spm').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('sp2d.load_rincian_spm') }}",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "data": function(d) {
                    d.no_spp = document.getElementById('no_spp').value
                }
            },
            ordering: false,
            columns: [{
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
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
                    data: null,
                    name: 'sisa',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.sisa)
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
            ]
        });

        let tabel_potongan = $('#rincian_potongan').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('sp2d.load_rincian_potongan') }}",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "data": function(d) {
                    d.no_spm = document.getElementById('no_spm').value
                }
            },
            ordering: false,
            columns: [{
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: 'nm_rek6',
                    name: 'nm_rek6',
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
                    name: 'status',
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
                    data: 'idBilling',
                    name: 'idBilling',
                },
            ]
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#cari_nospm').on('click', function() {
            let beban = document.getElementById('beban').value;
            if (!beban) {
                alert('Silahkan Pilih Beban!');
                return;
            }
            $('#no_spp').val(null);
            $('#no_spm').val(null);
            tabel_spm.ajax.reload();
            tabel_potongan.ajax.reload();
            $('#total_spm').val(null);
            $('#total_potongan').val(null);
            $.ajax({
                url: "{{ route('sp2d.cari_nomor') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#nomor_urut').val(data.nomor);
                    if (beban == '1') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/UP/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '2') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/GU/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '3') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/TU/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '4') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/LS/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '5' || beban == '6') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/LS/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '7') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/GU-NIHIL/' +
                            kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    }
                }
            })
            // cari data spm
            $.ajax({
                url: "{{ route('sp2d.cari_spm') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    beban: beban,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#no_spm').empty();
                    $('#no_spm').append(
                        `<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#no_spm').append(
                            `<option value="${data.no_spm}" data-bank="${data.bank}" data-bulan="${data.bulan}" data-keperluan="${data.keperluan}" data-kd_skpd="${data.kd_skpd}" data-nm_skpd="${data.nm_skpd}" data-nmrekan="${data.nmrekan}" data-no_rek="${data.no_rek}" data-no_spd="${data.no_spd}" data-no_spp="${data.no_spp}" data-npwp="${data.npwp}" data-tgl_spm="${data.tgl_spm}" data-tgl_spp="${data.tgl_spp}" data-jns_spd="${data.jns_spd}" data-jenis_beban="${data.jenis_beban}">${data.no_spm}</option>`
                        );
                    })
                }
            })
        });

        $('#cari_nosp2d').on('click', function() {
            let beban = document.getElementById('beban').value;
            $.ajax({
                url: "{{ route('sp2d.cari_nomor') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#nomor_urut').val(data.nomor);
                    if (beban == '1') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/UP/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '2') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/GU/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '3') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/TU/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '4') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/LS/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '5' || beban == '6') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/LS/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '7') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/GU-NIHIL/' +
                            kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    }
                }
            })
        });

        $('#beban').on('change', function() {
            $('#no_spm').empty();
            $('#no_sp2d').val('');
            $('#nomor_urut').val('');
            $('#tgl_spm').val('');
            $('#jenis').val('');
            $('#nama_jenis').val('');
            $('#no_spp').val('');
            $('#tgl_spp').val('');
            $('#kd_skpd').val('');
            $('#nm_skpd').val('');
            $('#bulan').val('');
            $('#nama_bulan').val('');
            $('#rekanan').val('');
            $('#keperluan').val('');
            $('#no_spd').val('');
            $('#jenis_spd').val('');
            $('#kode_bank').val('');
            $('#nama_bank').val('');
            $('#npwp').val('');
            $('#rekening').val('');
            $('#total_spm').val('');
            $('#total_potongan').val('');
            beban = this.value;
            tabel_spm.clear().draw();
            tabel_potongan.clear().draw();
            // cari no sp2d
            // $.ajax({
            //     url: "{{ route('sp2d.cari_nomor') }}",
            //     type: "POST",
            //     dataType: 'json',
            //     success: function(data) {
            //         $('#nomor_urut').val(data.nomor);
            //         if (beban == '1') {
            //             $('#no_sp2d').val(data.nomor + '/UP/' + thn_anggaran);
            //         } else if (beban == '2') {
            //             $('#no_sp2d').val(data.nomor + '/GU/' + thn_anggaran);
            //         } else if (beban == '3') {
            //             $('#no_sp2d').val(data.nomor + '/TU/' + thn_anggaran);
            //         } else if (beban == '4') {
            //             $('#no_sp2d').val('13.07/04.0/'+data.nomor + '/LS/' +'/'+thn_anggaran);
            //         } else if (beban == '5' || beban == '6') {
            //             $('#no_sp2d').val('13.07/04.0/'+data.nomor + '/LS/' +'/'+thn_anggaran);
            //         }
            //     }
            // })
            // cari data spm
            $.ajax({
                url: "{{ route('sp2d.cari_spm') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    beban: beban,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#no_spm').empty();
                    $('#no_spm').append(
                        `<option value="0">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#no_spm').append(
                            `<option value="${data.no_spm}" data-bank="${data.bank}" data-bulan="${data.bulan}" data-keperluan="${data.keperluan}" data-kd_skpd="${data.kd_skpd}" data-nm_skpd="${data.nm_skpd}" data-nmrekan="${data.nmrekan}" data-no_rek="${data.no_rek}" data-no_spd="${data.no_spd}" data-no_spp="${data.no_spp}" data-npwp="${data.npwp}" data-tgl_spm="${data.tgl_spm}" data-tgl_spp="${data.tgl_spp}" data-jns_spd="${data.jns_spd}" data-jenis_beban="${data.jenis_beban}">${data.no_spm} | ${data.nm_skpd}</option>`
                        );
                    })
                }
            })
        });

        $('#no_spm').on('select2:select', function() {
            $('#tgl_spm').val($(this).find(':selected').data('tgl_spm'));
            $('#tgl_spp').val($(this).find(':selected').data('tgl_spp'));
            $('#no_spp').val($(this).find(':selected').data('no_spp'));
            $('#kd_skpd').val($(this).find(':selected').data('kd_skpd'));
            $('#nm_skpd').val($(this).find(':selected').data('nm_skpd'));
            $('#rekanan').val($(this).find(':selected').data('nmrekan'));
            $('#keperluan').val($(this).find(':selected').data('keperluan'));
            $('#bulan').val($(this).find(':selected').data('bulan'));
            $('#no_spd').val($(this).find(':selected').data('no_spd'));
            $('#jenis_spd').val($(this).find(':selected').data('jns_spd'));
            $('#kode_bank').val($(this).find(':selected').data('bank'));
            $('#npwp').val($(this).find(':selected').data('npwp'));
            $('#rekening').val($(this).find(':selected').data('no_rek'));
            $('#jenis').val($(this).find(':selected').data('jenis_beban'));
            kd_skpd = $('#no_spm option:selected').data('kd_skpd');
            bulan = $('#no_spm option:selected').data('bulan');
            $.ajax({
                url: "{{ route('sp2d.cari_nomor') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    // alert(kd_skpd);
                    $('#nomor_urut').val(data.nomor);
                    if (beban == '1') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/UP/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '2') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/GU/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '3') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/TU/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '4') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/LS/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '5' || beban == '6') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/LS/' + kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    } else if (beban == '7') {
                        $('#no_sp2d').val('13.07/04.0/' + data.nomor + '/GU-NIHIL/' +
                            kd_skpd +
                            '/M/' + bulan + '/' + thn_anggaran);
                    }
                }
            })


            // Cari nama jenis
            $.ajax({
                url: "{{ route('sp2d.cari_jenis') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    beban: document.getElementById('beban').value,
                    jenis: document.getElementById('jenis').value,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#nama_jenis').val(data);
                }
            })
            // Cari nama bank
            $.ajax({
                url: "{{ route('spm.cari_bank') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    bank: $(this).find(':selected').data('bank'),
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#nama_bank').val(data.nama);
                }
            })
            // Cari nama bulan
            $.ajax({
                url: "{{ route('sp2d.cari_bulan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    bulan: $(this).find(':selected').data('bulan'),
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#nama_bulan').val(data);
                }
            })

            // Load Rincian SPM/Detail SPM
            tabel_spm.ajax.reload();
            // Load Rincian Potongan
            tabel_potongan.ajax.reload();
            // Load Total Detail SPM dan Total List Potongan
            $.ajax({
                url: "{{ route('sp2d.cari_total') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spp: $(this).find(':selected').data('no_spp'),
                    no_spm: this.value,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#total_spm').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total_spm));
                    $('#total_potongan').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.total_potongan));
                }
            })
        });

        $('#simpan_sp2d').on('click', function() {
            let beban = document.getElementById('beban').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let no_spm = document.getElementById('no_spm').value;
            let no_spp = document.getElementById('no_spp').value;
            let tgl_sp2d = document.getElementById('tgl_sp2d').value;
            if (!beban) {
                alert('Silahkan pilih jenis beban!');
                return;
            }
            if (!no_spm) {
                alert('Silahkan pilih no spm!');
                return;
            }
            if (!no_spp) {
                alert('Silahkan pilih no spm!');
                return;
            }
            if (!tgl_sp2d) {
                alert('Silahkan pilih tanggal SP2D!');
                return;
            }
            if (beban == '4') {
                if (!no_sp2d) {
                    alert('Nomor SP2D Tidak Boleh kosong');
                    return;
                }
            }

            if (no_sp2d == null) {
                alert('No SP2D tidak boleh kosong, silahkan refresh kembali!');
                return;
            }
            // simpan sp2d
            $('#simpan_sp2d').prop('disabled', true);
            $.ajax({
                url: "{{ route('sp2d.simpan_sp2d') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    beban: beban,
                    tgl_sp2d: tgl_sp2d,
                    no_spm: no_spm,
                    no_spp: no_spp,
                    ckd_skpd: kd_skpd,
                    cno_sp2d: document.getElementById('no_sp2d').value,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil ditambahkan, No SP2D yang tersimpan adalah: ' +
                            data.no_sp2d);
                        window.location.href = "{{ route('sp2d.index') }}";
                    } else if (data.message == '2') {
                        alert('Nomor SP2D Sudah Terpakai...!!!,  Ganti Nomor SP2D...!!!');
                        $('#simpan_sp2d').prop('disabled', false);
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan_sp2d').prop('disabled', false);
                    }
                }
            })
        });

    });
</script>
