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
            let nilai = rupiah(document.getElementById('nilai').value);
            let no_sp2d = document.getElementById('no_sp2d').value;
            let no_advice = document.getElementById('no_advice').value;
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

            // cek simpan
            $('#pencairan').prop('disabled', true);
            // $.ajax({
            //     url: "{{ route('pencairan_sp2d.cek_simpan') }}",
            //     type: "POST",
            //     dataType: 'json',
            //     data: {
            //         no_kas: no_kas
            //     },
            // success: function(data) {
            // if (data > 0) {
            //     alert('Nomor telah dipakai!');
            //     $('#pencairan').prop('disabled', false);
            // } else {
            //     alert('Nomor bisa dipakai!');
            $.ajax({
                url: "{{ route('pencairan_sp2d.simpan_cair') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_kas: document.getElementById('no_kas').value,
                    tgl_cair: tgl_cair,
                    nilai: nilai,
                    no_sp2d: no_sp2d,
                    no_advice: no_advice,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('No Kas bisa dipakai, SP2D Telah Dicairkan !!');
                        window.location.reload();
                    } else if (data.message == '2') {
                        alert(
                            'Nomor Kas Sudah Terpakai...!!!,  Ganti Nomor Kas...!!!'
                        );
                    } else {
                        alert('SP2D Tidak berhasil dicairkan !');
                        $('#pencairan').prop('disabled', false);
                    }
                }
            });
            // }
            // }
            // });
        }

        function batal_cair() {
            let no_kas = document.getElementById('no_kas').value;
            let no_sp2d = document.getElementById('no_sp2d').value;
            let hapus = confirm('Apakah Yakin Ingin Batal Cair SP2D No : ' + no_sp2d + '?');
            if (hapus == true) {
                $('#pencairan').prop('disabled', true);
                $.ajax({
                    url: "{{ route('pencairan_sp2d.batal_cair') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        no_kas: no_kas,
                        no_sp2d: no_sp2d,
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('SP2D Telah Dibatalkan');
                            window.location.reload();
                        } else {
                            alert('SP2D Tidak berhasil dibatalkan');
                            $('#pencairan').prop('disabled', false);
                        }
                    }
                });
            } else {
                return;
            }
        }

        function rupiah(n) {
            let n1 = n.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }

    });
</script>
