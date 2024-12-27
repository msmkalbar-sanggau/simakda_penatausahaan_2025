<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let rincian_validasi = $('#rincian_validasi').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_kas',
                    name: 'no_kas',
                },
                {
                    data: 'tgl_kas',
                    name: 'tgl_kas',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                },
                {
                    data: 'keterangan',
                    name: 'keterangan',
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'no_upload',
                    name: 'no_upload',
                    visible: false
                },
                {
                    data: 'status_upload',
                    name: 'status_upload',
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
                    visible: false
                },
                {
                    data: 'rekening_tujuan',
                    name: 'rekening_tujuan',
                    visible: false
                },
                {
                    data: 'bank_tujuan',
                    name: 'bank_tujuan',
                    visible: false
                },
                {
                    data: 'ket_tujuan',
                    name: 'ket_tujuan',
                    visible: false
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },
            ]
        });

        load_transaksi();

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#data_transaksi').on('select2:select', function() {
            let no_kas = this.value;
            let total_transaksi = angka(document.getElementById('total_transaksi').value);
            let nilai = parseFloat($(this).find(':selected').data('nilai'));
            let tgl_bukti = $(this).find(':selected').data('tgl');

            let tgl_validasi = document.getElementById('tgl_validasi').value;
            if (!tgl_validasi) {
                alert('Tanggal Validasi belum dipilih!');
                $("#data_transaksi").val(null).change();
                return;
            }

            if (tgl_validasi < tgl_bukti) {
                alert('Tanggal validasi tidak boleh dibawah tanggal upload/transaksi');
                return;
            }

            let tampungan = rincian_validasi.rows().data().toArray().map((value) => {
                let result = {
                    no_kas: value.no_kas,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.no_kas == no_kas) {
                    return '1';
                }
            });
            if (kondisi.includes("1")) {
                alert('Nomor Transaksi ini sudah ada di LIST!');
                $("#data_transaksi").val(null).change();
                return;
            }

            rincian_validasi.row.add({
                'no_kas': no_kas,
                'tgl_kas': $(this).find(':selected').data('tgl'),
                'kd_skpd': $(this).find(':selected').data('kd_skpd'),
                'keterangan': $(this).find(':selected').data('keterangan'),
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format($(this).find(':selected').data('nilai')),
                'no_upload': $(this).find(':selected').data('no_upload'),
                'status_upload': $(this).find(':selected').data('status_upload'),
                'rekening_awal': $(this).find(':selected').data('rekening_awal'),
                'nm_rekening_tujuan': $(this).find(':selected').data('nm_rekening_tujuan'),
                'rekening_tujuan': $(this).find(':selected').data('rekening_tujuan'),
                'bank_tujuan': $(this).find(':selected').data('bank_tujuan'),
                'ket_tujuan': $(this).find(':selected').data('ket_tujuan'),
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_kas}','${nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi + nilai));
            $("#data_transaksi").val(null).change();
            load_transaksi();
        });


        $('#proses_validasi').on('click', function() {
            let total_transaksi = angka(document.getElementById('total_transaksi').value);
            let sisa_saldo = angka(document.getElementById('sisa_saldo').value);
            let tgl_validasi = document.getElementById('tgl_validasi').value;
            let rincian = rincian_validasi.rows().data().toArray();
            if (rincian.length == 0) {
                alert('List Data belum dipilih');
                return;
            }
            if (!tgl_validasi) {
                alert('Tanggal Validasi belum dipilih!');
                $("#data_transaksi").val(null).change();
                return;
            }

            let total_transfer = rincian_validasi.rows().data().toArray().reduce((previousValue,
                currentValue) => (previousValue += angka(currentValue.nilai)), 0);

            // let total_transfer = total_transaksi - total_potongan;

            if (total_transfer > sisa_saldo) {
                alert('Total Transaksi melebihi sisa Bank, Cek Kembali !');
                return;
            }

            let tanya = confirm("Apakah data yang akan di-Validasi sudah benar ?");
            if (tanya == true) {
                let rincian_data = rincian_validasi.rows().data().toArray().map((value) => {
                    let data = {
                        no_kas: value.no_kas,
                        tgl_kas: value.tgl_kas,
                        kd_skpd: value.kd_skpd,
                        keterangan: value.keterangan,
                        nilai: angka(value.nilai),
                        no_upload: value.no_upload,
                        status_upload: value.status_upload,
                        rekening_awal: value.rekening_awal,
                        nm_rekening_tujuan: value.nm_rekening_tujuan,
                        rekening_tujuan: value.rekening_tujuan,
                        bank_tujuan: value.bank_tujuan,
                        ket_tujuan: value.ket_tujuan,
                    };
                    return data;
                });
                $('#proses_validasi').prop("disabled", true);
                $.ajax({
                    url: "{{ route('validasi_panjarcms.proses_validasi') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        total_transaksi: total_transaksi,
                        rincian_data: rincian_data,
                        tanggal_validasi: tgl_validasi,
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('Data berhasil divalidasi');
                            window.location.href =
                                "{{ route('validasi_panjarcms.index') }}";
                        } else {
                            alert('Data tidak berhasil divalidasi!');
                            $('#proses_validasi').prop("disabled", false);
                        }
                    }
                })
            } else {
                alert('Silahkan Cek lagi, Pastikan Data Sudah Benar...');
                $('#proses_upload').prop("disabled", false);
            }
        });
    });

    function angka(data) {
        let n1 = data.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function load_transaksi() {
        $('#data_transaksi').empty();
        let rincian_validasi = $('#rincian_validasi').DataTable();
        let detail_rincian = rincian_validasi.rows().data().toArray().map((value) => {
            let data = {
                no_kas: value.no_kas,
            };
            return data;
        });

        $.ajax({
            url: "{{ route('validasi_panjarcms.data_transaksi') }}",
            type: "POST",
            dataType: 'json',
            data: {
                no_kas: detail_rincian.length == 0 ? '0' : detail_rincian
            },
            success: function(data) {
                $('#data_transaksi').empty();
                $('#data_transaksi').append(
                    `<option value="" disabled selected>Silahkan Pilih</option>`);
                $.each(data, function(index, data) {
                    $('#data_transaksi').append(
                        `<option value="${data.no_kas}" data-tgl="${data.tgl_kas}" data-kd_skpd="${data.kd_skpd}" data-keterangan="${data.keterangan}" data-nilai="${data.nilai}" data-status_upload="${data.status_upload}" data-no_upload="${data.no_upload}" data-rekening_awal="${data.rekening_awal}" data-nm_rekening_tujuan="${data.nm_rekening_tujuan}" data-rekening_tujuan="${data.rekening_tujuan}" data-bank_tujuan="${data.bank_tujuan}" data-ket_tujuan="${data.ket_tujuan}">${data.no_kas} | ${data.tgl_kas}</option>`
                    );
                })
            }
        })
    }

    function deleteData(no_kas, total) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor Transaksi : ' + no_kas);
        let tabel = $('#rincian_validasi').DataTable();
        let total_transaksi = angka(document.getElementById('total_transaksi').value);
        if (tanya == true) {
            tabel.rows(function(idx, data, node) {
                return data.no_kas == no_kas
            }).remove().draw();
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi - parseFloat(total)));
            load_transaksi();
        } else {
            return false;
        }
    }
</script>
