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
        cari_nomor();

        let detail = $('#rincian_spp').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [
                [-1],
                ["All"]
            ],
            ajax: {
                "url": "{{ route('spp_gu_nihil.detail') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_lpj = document.getElementById('no_lpj').value;
                    d.tipe = "create";
                },
                "dataSrc": function(data) {
                    recordsTotal = data.data;
                    return recordsTotal;
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'kd_unit',
                    name: 'kd_unit'
                },
                {
                    data: 'no_bukti',
                    name: 'no_bukti'
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan'
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6'
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
                    data: 'sumber',
                    name: 'sumber',
                }
            ],
            drawCallback: function(select) {
                let total = recordsTotal.reduce((previousValue,
                    currentValue) => (previousValue += parseFloat(currentValue.nilai)), 0);
                $('#total').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total));
            }
        });

        $('#bank').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $("#nm_bank").val(nama);
        });

        $('#tgl_spp').on('change', function() {
            let tanggal = this.value;
            let bulan = new Date(tanggal);
            let bulan1 = bulan.getMonth() + 1;
            $("#bulan").val(bulan1);
            cari_nomor();
        });

        $('#rekening').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            let npwp = $(this).find(':selected').data('npwp');
            $("#nm_rekening").val(nama);
            $("#npwp").val(npwp);
        });

        $('#no_lpj').on('select2:select', function() {
            detail.clear().draw();
            detail.ajax.reload();
        });

        $('#no_sp2d').on('select2:select', function() {
            detail.clear().draw();
            let no_sp2d = this.value
            let tgl = $(this).find(':selected').data('tgl');
            $("#tgl_sp2d").val(tgl);

            $.ajax({
                url: "{{ route('lpj_tu.detail') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sp2d: no_sp2d,
                },
                success: function(data) {
                    let total = rupiah(document.getElementById('total').value);
                    $.each(data, function(index, data) {
                        detail.row.add({
                            'kd_skpd': data.kd_bp_skpd,
                            'no_bukti': data.no_bukti,
                            'kd_sub_kegiatan': data.kd_sub_kegiatan,
                            'kd_rek6': data.kd_rek6,
                            'nm_rek6': data.nm_rek6,
                            'nilai': new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2
                            }).format(data.nilai)
                        }).draw();
                        total += parseFloat(data.nilai);
                    })
                    $('#total').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total));
                }
            });
        });

        $('#simpan').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let no_urut = document.getElementById('no_urut').value;
            let tgl_spp = document.getElementById('tgl_spp').value;
            let tgl_lalu = document.getElementById('tgl_lalu').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let no_spd = document.getElementById('no_spd').value;
            let no_lpj = document.getElementById('no_lpj').value;
            let bank = document.getElementById('bank').value;
            let nm_bank = document.getElementById('nm_bank').value;
            let rekening = document.getElementById('rekening').value;
            let nm_rekening = document.getElementById('nm_rekening').value;
            let beban = document.getElementById('beban').value;
            let npwp = document.getElementById('npwp').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let keterangan = document.getElementById('keterangan').value;
            let total = rupiah(document.getElementById('total').value);
            let tahun_input = tgl_spp.substr(0, 4);

            let detail_spp = detail.rows().data().toArray().map((value) => {
                let data = {
                    kd_unit: value.kd_unit,
                    no_bukti: value.no_bukti,
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    nilai: rupiah(value.nilai),
                    sumber: value.sumber,
                };
                return data;
            });

            if (!no_spp) {
                alert('Nomor tidak boleh kosong');
                return;
            }

            if (!tgl_spp) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }

            if (tgl_spp < tgl_lalu) {
                alert('Tanggal SPP tidak boleh kurang dari SPP Lalu...!!!');
                return;
            }

            if (tahun_anggaran != tahun_input) {
                alert('Tahun input tidak sesuai dengan tahun anggaran');
                return;
            }

            if (!kd_skpd) {
                alert('SKPD tidak boleh kosong!');
                return;
            }

            if (!no_lpj) {
                alert('No. LPJ tidak boleh kosong!');
                return;
            }

            if (!no_spd) {
                alert('No. SPD tidak boleh kosong!');
                return;
            }

            if (!bank) {
                alert('Bank tidak boleh kosong!');
                return;
            }

            if (!rekening) {
                alert('Rekening bank tidak boleh kosong!');
                return;
            }

            if (!npwp) {
                alert('NPWP tidak boleh kosong!');
                return;
            }

            if (!keterangan) {
                alert('Keterangan tidak boleh kosong!');
                return;
            }

            if (total == 0) {
                alert('Rincian tidak boleh kosong!');
                return;
            }

            if (detail_spp.length == 0) {
                alert('Rincian tidak boleh kosong!');
                return;
            }

            let data = {
                no_spp,
                no_urut,
                tgl_spp,
                kd_skpd,
                nm_skpd,
                no_spd,
                no_lpj,
                bank,
                nm_bank,
                rekening,
                nm_rekening,
                beban,
                npwp,
                keterangan,
                total,
                detail_spp
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('spp_gu_nihil.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil ditambahkan!');
                        window.location.href = "{{ route('spp_gu_nihil.index') }}";
                    } else if (response.message == '2') {
                        alert('Nomor Telah Dipakai!');
                        $('#simpan').prop('disabled', false);
                        return;
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan').prop('disabled', false);
                        return;
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

        $('#cari').on('click', function() {
            $('#no_spp').val(null);
            cari_nomor();
        });

        function cari_nomor() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tahun_anggaran = "{{ tahun_anggaran() }}";
            let bulan = document.getElementById('bulan').value;

            $.ajax({
                url: "{{ route('sppls.cari_nospp') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    let no_spp = "61.03/02.0/" + data.nilai + "/" + "GU-NIHIL" + "/" + kd_skpd +
                        "/" +
                        "M" + "/" + bulan + "/" +
                        tahun_anggaran;
                    $('#no_spp').val(no_spp);
                    $('#no_urut').val(data.nilai);
                }
            })
        }
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

    function hapus(no_bukti, kd_rek6, nilai) {
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai :  ' + nilai +
            ' ?');
        let total = rupiah(document.getElementById('total').value);
        let tabel = $('#detail_lpj').DataTable();

        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.no_bukti == no_bukti && data.kdrek6 == kd_rek6
            }).remove().draw();
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - parseFloat(nilai)));
        }
    }
</script>
