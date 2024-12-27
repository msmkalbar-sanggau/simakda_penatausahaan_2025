<script>
    let table_kelengkapan_spm;
    let detail_kelengkapan_spm_old = @json($verifikasi_spp);
    let detail_spp = @json($trdspp);
    let tabel;

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

        tabel = $('#rincian_spm').DataTable({
            responsive: true,
            ordering: false,
            columns: [{
                    data: 'kd_sub_kegiatan'
                },
                {
                    data: 'kd_rek6'
                },
                {
                    data: 'nm_rek6'
                },
                {
                    data: 'nilai'
                },
            ]
        });

        $('#jenis_ls').on('select2:select', function(e) {
            if (e.params != undefined) {
                table_kelengkapan_spm.clear().draw();

                // console.log(e.params.data.id);
                $.ajax({
                    url: "{{ route('verifikasi_spp.load_kelengkapan_spm') }}",
                    type: "get",
                    dataType: 'json',
                    data: {
                        no_spp: document.getElementById("no_spp").value,
                        jenis_ls: document.getElementById("jenis_ls").value
                    },
                    success: function(response) {
                        let uraian = "";
                        response.data.forEach(element => {
                            uraian = `${element.list_id} ${element.uraian}`;

                            table_kelengkapan_spm.row.add({
                                'id': element.id,
                                'uraian': uraian,
                                'ceklist': "",
                            }).draw();
                        });

                    }
                });

            }
        });

        table_kelengkapan_spm = $('#table_kelengkapan_spm').DataTable({
            responsive: true,
            ordering: false,
            paging: false,
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false,
                    searchable: false,
                },
                {
                    data: 'uraian',
                    name: 'uraian',
                    className: "text-left",
                },
                {
                    data: 'ceklist',
                    name: 'ceklist',
                    width: 200,
                    className: "text-center",
                },
            ],
            "columnDefs": [{
                targets: "ceklist",
                render: function(data, type, row, meta) {
                    var checked = (data == '1') ? 'checked' : '';
                    return '<div class="d-flex justify-content-center">' +
                        `<label class="switch">` +
                        `<input class="chk_collected" type="checkbox" ${checked} >` +
                        '<span class="slider round"></span>' +
                        '</label>' +
                        '</div>';
                }
            }],
            drawCallback: function(settings) {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        $('#update').on('click', function() {
            let data_send = {};

            data_send.tgl_terima_kelengkapan_spm = document.getElementById("tgl_terima_kelengkapan_spm")
                .value;
            if (data_send.tgl_terima_kelengkapan_spm == "") {
                alert('Silahkan pilih tanggal Terima!');
                return;
            }

            data_send.ket_kelengkapan_spm = document.getElementById("ket_kelengkapan_spm").value;
            if (data_send.ket_kelengkapan_spm == "") {
                alert('Silahkan Isi Keterangan Berkas Kelengkapan SPM!');
                return;
            }

            let tahun_anggaran = "{{ tahun_anggaran() }}";
            let tahun_input = data_send._kelengkapan_spm.substring(0, 4);
            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            data_send.no_spp = document.getElementById("no_spp").value;

            data_send.tgl_terima_kembali_kelengkapan_spm = document.getElementById(
                "tgl_terima_kembali_kelengkapan_spm").value;
            data_send.tgl_kembali_kelengkapan_spm = document.getElementById(
                "tgl_kembali_kelengkapan_spm").value;
            if (data_send.tgl_kembali_kelengkapan_spm != "") {
                if (data_send.tgl_terima_kembali_kelengkapan_spm == "") {
                    alert("Jika ada Tanggal Kembali. Maka tanggal 'Tanggal Terima Kembali' harus ada");
                    return;
                }
            }

            data_send.jenis_ls = document.getElementById("jenis_ls").value;

            let data_detail = [];
            table_kelengkapan_spm.rows().every(function(rowIdx) {
                let data = this.data();
                let checked = $(this.node()).find("input[class*='chk_collected']")[0].checked ?
                    1 : 0;

                data_detail.push({
                    id: data.id,
                    uraian: data.uraian,
                    checked: checked,
                });
            });
            data_send.data_detail = data_detail;

            $.ajax({
                url: "{{ route('verifikasi_spp.update') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $('#update').prop('disabled', true);
                },
                data: data_send,
                success: function(response) {
                    alert(response.message);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    response = jqXHR.responseJSON;
                    alert(response.message);
                },
                complete: function(data) {
                    $('#update').prop('disabled', false);
                },
            });
        });

        $('#setuju').on('click', function() {
            let failed = 0;
            let data_detail = [];
            let data_send = {};

            table_kelengkapan_spm.rows().every(function(rowIdx) {
                let data = this.data();
                let checked = $(this.node()).find("input[class*='chk_collected']")[0].checked ?
                    1 : 0;

                if (!data.uraian.includes('Dokumen lain yang dipersyaratkan')) {
                    if (checked == 0) failed += 1;
                }

                data_detail.push({
                    id: data.id,
                    uraian: data.uraian,
                    checked: checked,
                });
            });

            // data_send.jenis_ls = document.getElementById("jenis_ls").value;
            // if (data_send.jenis_ls == '1' || data_send.jenis_ls == '2' || data_send.jenis_ls == '3') {
            //     if (failed > 0) {
            //         alert("Kelengkapan Gaji Hanya Bisa Disetuji Jika Semua data sudah dichecklist");
            //         return;
            //     }
            // }

            data_send.tgl_terima_kelengkapan_spm = document.getElementById("tgl_terima_kelengkapan_spm")
                .value;
            if (data_send.tgl_terima_kelengkapan_spm == "") {
                alert('Silahkan pilih tanggal Terima!');
                return;
            }

            data_send.ket_kelengkapan_spm = document.getElementById("ket_kelengkapan_spm").value;
            if (data_send.ket_kelengkapan_spm == "") {
                alert('Silahkan Isi Keterangan Berkas Kelengkapan SPM!');
                return;
            }

            data_send.no_spp = document.getElementById("no_spp").value;
            data_send.tgl_terima_kembali_kelengkapan_spm = document.getElementById(
                "tgl_terima_kembali_kelengkapan_spm").value;
            data_send.tgl_kembali_kelengkapan_spm = document.getElementById(
                "tgl_kembali_kelengkapan_spm").value;
            data_send.data_detail = data_detail;

            if (data_send.tgl_kembali_kelengkapan_spm != "") {
                if (data_send.tgl_terima_kembali_kelengkapan_spm == "") {
                    alert("Jika ada Tanggal Kembali. Maka tanggal 'Tanggal Terima Kembali' harus ada");
                    return;
                }
            }

            $.ajax({
                url: "{{ route('verifikasi_spp.setuju') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $('#setuju').prop('disabled', true);
                },
                data: data_send,
                success: function(response) {
                    alert(response.message);
                    window.location.href = "{{ route('verifikasi_spp.index') }}";
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    response = jqXHR.responseJSON;
                    alert(response.message);
                },
                complete: function(data) {
                    $('#setuju').prop('disabled', false);
                },
            });
        });


        $('#kembali').on('click', function() {
            let data_send = {};

            data_send.tgl_terima_kelengkapan_spm = document.getElementById("tgl_terima_kelengkapan_spm")
                .value;
            if (data_send.tgl_terima_kelengkapan_spm == "") {
                alert('Silahkan pilih tanggal Terima!');
                return;
            }

            data_send.ket_kelengkapan_spm = document.getElementById("ket_kelengkapan_spm").value;
            if (data_send.ket_kelengkapan_spm == "") {
                alert('Silahkan Isi Keterangan Berkas Kelengkapan SPM!');
                return;
            }

            let tahun_anggaran = "{{ tahun_anggaran() }}";
            let tahun_input = data_send.tgl_terima_kelengkapan_spm.substring(0, 4);
            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            data_send.no_spp = document.getElementById("no_spp").value;
            data_send.tgl_terima_kembali_kelengkapan_spm = document.getElementById(
                "tgl_terima_kembali_kelengkapan_spm").value;
            data_send.tgl_kembali_kelengkapan_spm = document.getElementById(
                "tgl_kembali_kelengkapan_spm").value;
            if (data_send.tgl_kembali_kelengkapan_spm == "") {
                alert('Silahkan pilih tanggal Kembali!');
                return;
            }

            data_send.jenis_ls = document.getElementById("jenis_ls").value;

            let data_detail = [];
            table_kelengkapan_spm.rows().every(function(rowIdx) {
                let data = this.data();
                let checked = $(this.node()).find("input[class*='chk_collected']")[0].checked ?
                    1 : 0;

                data_detail.push({
                    id: data.id,
                    uraian: data.uraian,
                    checked: checked,
                });
            });
            data_send.data_detail = data_detail;

            $.ajax({
                url: "{{ route('verifikasi_spp.kembali') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $('#kembali').prop('disabled', true);
                },
                data: data_send,
                success: function(response) {
                    alert(response.message);
                    window.location.href = "{{ route('verifikasi_spp.index') }}";
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    response = jqXHR.responseJSON;
                    alert(response.message);
                },
                complete: function(data) {
                    $('#kembali').prop('disabled', false);
                },
            });
        });

        init();
    });

    function init() {

        $("#jenis_ls").val("{{ $trhspp->jenis_ls_kelengkapan_spm }}").trigger('change');

        table_kelengkapan_spm.clear().draw();

        detail_kelengkapan_spm_old.forEach(element => {
            uraian = `${element.list_id} ${element.uraian}`;

            table_kelengkapan_spm.row.add({
                'id': element.id,
                'uraian': uraian,
                'ceklist': element.checked,
            }).draw();
        });


        document.getElementById("nm_beban").value = cari_beban(parseFloat("{{ $trhspp->jns_spp }}"));

        let total = 0;
        $.each(detail_spp, function(index, data) {
            tabel.row.add({
                'kd_sub_kegiatan': data.kd_sub_kegiatan,
                'kd_rek6': data.kd_rek6,
                'nm_rek6': data.nm_rek6,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.nilai),
            }).draw();
            total += parseFloat(data.nilai);
        })

        $('#total').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(total));

        $.ajax({
            url: "{{ route('spm.cari_jenis') }}",
            type: "POST",
            dataType: 'json',
            data: {
                beban: "{{ $trhspp->jns_spp }}",
                jenis: "{{ $trhspp->jns_beban }}",
            },
            success: function(data) {
                $('#nm_jenis').val(data.nama);
            }
        })
    }

    function cari_bulan(bulan) {
        switch (bulan) {
            case 1:
                return 'Januari';
                break;
            case 2:
                return 'Februari';
                break;
            case 3:
                return 'Maret';
                break;
            case 4:
                return 'April';
                break;
            case 5:
                return 'Mei';
                break;
            case 6:
                return 'Juni';
                break;
            case 7:
                return 'Juli';
                break;
            case 8:
                return 'Agustus';
                break;
            case 9:
                return 'September';
                break;
            case 10:
                return 'Oktober';
                break;
            case 11:
                return 'November';
                break;
            case 12:
                return 'Desember';
                break;
            default:
                break;
        }
    }

    function cari_beban(beban) {
        switch (beban) {
            case 1:
                return 'UP'
                break;
            case 2:
                return 'GU'
                break;
            case 3:
                return 'TU'
                break;
            case 4:
                return 'LS GAJI'
                break;
            case 5:
                return 'LS Pihak Ketiga Lainnya'
                break;
            case 6:
                return 'LS Barang Jasa'
                break;
            default:
                break;
        }
    }

    function cari_jenis(beban, jenis) {
        $.ajax({
            url: "{{ route('spm.cari_jenis') }}",
            type: "POST",
            dataType: 'json',
            data: {
                beban: beban,
                jenis: jenis,
            },
            success: function(data) {
                $('#nm_jenis').val(data.nama);
            }
        })
    }

    function cari_bank(bank) {
        $.ajax({
            url: "{{ route('spm.cari_bank') }}",
            type: "POST",
            dataType: 'json',
            data: {
                bank: bank,
            },
            success: function(data) {
                $('#nm_bank').val(data.nama);
            }
        })
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function deleteData(kd_sub_kegiatan, kode_rekening, nm_rekening, sumber_dana, nilai_rincian) {
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kode_rekening + '  Nilai :  ' + nilai_rincian +
            ' ?');
        let total = rupiah(document.getElementById('total').value);
        let tabel = $('#rincian_sppls').DataTable();
        if (hapus == true) {
            tabel.rows(function(idx, data, node) {
                return data.sumber == sumber_dana && data.kd_sub_kegiatan == kd_sub_kegiatan &&
                    data.kd_rek6 == kode_rekening
            }).remove().draw();
            $('#total').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total - nilai_rincian));
        }
    }
</script>
