<script>
    $(document).ready(function() {
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
                "url": "{{ route('pencairan_sp2d.load_rincian_spm') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_spp = document.getElementById('no_spp').value
                }
            },
            ordering: false,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                }, {
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
                "url": "{{ route('pencairan_sp2d.load_rincian_potongan') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_spm = document.getElementById('no_spm').value
                }
            },
            ordering: false,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                }, {
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
            ]
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#pencairan').on('click', function() {
            let cair = this.value;
            let no_kas = document.getElementById('no_kas').value;

            if (!no_kas) {
                alert('No kas harus terisi!');
                return;
            }

            if (cair == '1') {
                // Cair
                simpan_cair();
            } else if (cair == '0') {
                // Batal Cair
                batal_cair();
            }
        });

        function simpan_cair() {
            let no_kas = document.getElementById('no_kas').value;
            let tgl_cair = document.getElementById('tgl_cair').value;
            let jenis = document.getElementById('jenis').value;
            let no_kontrak = document.getElementById('no_kontrak').value;
            let tgl_terima = document.getElementById('tgl_terima').value;
            let nilai = rupiah(document.getElementById('nilai').value);
            let no_sp2d = document.getElementById('no_sp2d').value;
            let opd = document.getElementById('opd').value;
            let keperluan = document.getElementById('keperluan').value;
            let beban = document.getElementById('beban').value;
            let npwp = document.getElementById('npwp').value;
            let total_potongan = rupiah(document.getElementById('total_potongan').value);
            let tgl_sp2d = document.getElementById('tgl_sp2d').value;
            let tahun_anggaran = "{{ tahun_anggaran() }}";

            let tahun_input = tgl_cair.substring(0, 4);
            if (!tgl_cair) {
                alert('Tanggal cair tidak boleh kosong!');
                return;
            }
            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun anggaran!');
                return;
            }
            if (tgl_terima > tgl_cair) {
                alert('Tanggal Pencairan tidak boleh lebih kecil dari tanggal Penerimaan');
                return;
            }
            if (tgl_terima != tgl_cair) {
                alert('Tanggal  Pencairan Harus sama dengan Tanggal Penerimaan');
                return;
            }

            $('#pencairan').prop('disabled', true);
            $.ajax({
                url: "{{ route('skpd.pencairan_sp2d.simpan_cair') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_kas: no_kas,
                    tgl_cair: tgl_cair,
                    jenis: jenis,
                    no_kontrak: no_kontrak,
                    tgl_terima: tgl_terima,
                    nilai: nilai,
                    no_sp2d: no_sp2d,
                    opd: opd,
                    keperluan: keperluan,
                    npwp: npwp,
                    beban: beban,
                    total_potongan: total_potongan,
                    tgl_sp2d: tgl_sp2d,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('SP2D Telah Dicairkan');
                        window.location.reload();
                    } else {
                        alert('SP2D Tidak berhasil dicairkan');
                        $('#pencairan').prop('disabled', false);
                        return;
                    }
                }
            });
        }

        function batal_cair() {
            let no_kas = document.getElementById('no_kas').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let beban = document.getElementById('beban').value;
            let jenis = document.getElementById('jenis').value;

            $('#pencairan').prop('disabled', true);
            $.ajax({
                url: "{{ route('skpd.pencairan_sp2d.batal_cair') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_kas: no_kas,
                    no_sp2d: no_sp2d,
                    beban: beban,
                    jenis: jenis,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('SP2D telah Dibatalkan');
                        window.location.reload();
                    } else {
                        alert('SP2D Tidak berhasil dibatalkan');
                        $('#pencairan').prop('disabled', false);
                        return;
                    }
                }
            });
        }

        function rupiah(n) {
            let n1 = n.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }

    });
</script>
