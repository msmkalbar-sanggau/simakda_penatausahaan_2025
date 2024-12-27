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
                    d.no_spp = document.getElementById('no_spp').value;
                    d.tipe = "edit";
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

        $('#rekening').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            let npwp = $(this).find(':selected').data('npwp');
            $("#nm_rekening").val(nama);
            $("#npwp").val(npwp);
        });

        $('#simpan').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let tgl_spp = document.getElementById('tgl_spp').value;
            let tgl_lalu = document.getElementById('tgl_lalu').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let bank = document.getElementById('bank').value;
            let nm_bank = document.getElementById('nm_bank').value;
            let rekening = document.getElementById('rekening').value;
            let nm_rekening = document.getElementById('nm_rekening').value;
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
                tgl_spp,
                kd_skpd,
                nm_skpd,
                bank,
                nm_bank,
                rekening,
                nm_rekening,
                npwp,
                keterangan,
                total,
                detail_spp
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('spp_gu_nihil.update') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil diperbaharui!');
                        window.location.href = "{{ route('spp_gu_nihil.index') }}";
                    } else if (response.message == '2') {
                        alert('Nomor Telah Dipakai!');
                        $('#simpan').prop('disabled', false);
                        return;
                    } else {
                        alert('Data tidak berhasil diperbaharui!');
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
</script>
