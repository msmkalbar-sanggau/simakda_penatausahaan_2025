<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let rincian_upload = $('#rincian_upload').DataTable({
            responsive: true,
            processing: true,
            ordering: false,
            columns: [{
                    data: 'no_bukti',
                    name: 'no_bukti',
                },
                {
                    data: 'tgl_bukti',
                    name: 'tgl_bukti',
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                },
                {
                    data: null,
                    name: 'ket',
                    render: function(data, type, row, meta) {
                        return data.ket.substr(0, 10) + '.....';
                    }
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'status_upload',
                    name: 'status_upload',
                    visible: false
                },
                {
                    data: 'no_upload',
                    name: 'no_upload',
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

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#data_transaksi').on('select2:select', function() {
            let no_bukti = this.value;
            let total_transaksi = angka(document.getElementById('total_transaksi').value);
            let nilai = parseFloat($(this).find(':selected').data('nilai'));

            let tampungan = rincian_upload.rows().data().toArray().map((value) => {
                let result = {
                    no_bukti: value.no_bukti,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.no_bukti == no_bukti) {
                    return '1';
                }
            });
            if (kondisi.includes("1")) {
                alert('Nomor Transaksi ini sudah ada di LIST!');
                $("#data_transaksi").val(null).change();
                return;
            }

            rincian_upload.row.add({
                'no_bukti': no_bukti,
                'tgl_bukti': $(this).find(':selected').data('tgl'),
                'kd_skpd': $(this).find(':selected').data('kd_skpd'),
                'ket': $(this).find(':selected').data('ket'),
                'status_upload': $(this).find(':selected').data('status_upload'),
                'no_upload': $(this).find(':selected').data('no_upload'),
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format($(this).find(':selected').data('nilai')),
                'rekening_awal': $(this).find(':selected').data('rekening_awal'),
                'nm_rekening_tujuan': $(this).find(':selected').data('nm_rekening_tujuan'),
                'rekening_tujuan': $(this).find(':selected').data('rekening_tujuan'),
                'bank_tujuan': $(this).find(':selected').data('bank_tujuan'),
                'ket_tujuan': $(this).find(':selected').data('ket_tujuan'),
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_bukti}','${nilai}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi + nilai));
            $("#data_transaksi").val(null).change();
        });

        $('#proses_upload').on('click', function() {
            let total_transaksi1 = angka(document.getElementById('total_transaksi').value);
            let sisa_saldo = angka(document.getElementById('sisa_saldo').value);
            let tanggal_validasi = document.getElementById('tanggal_validasi').value;
            let rincian = rincian_upload.rows().data().toArray();

            if (!tanggal_validasi) {
                alert('Tanggal validasi belum dipilih!');
                return;
            }
            if (rincian.length == 0) {
                alert('List Data belum dipilih');
                return;
            }

            let nilai_transaksi = rincian_upload.rows().data().toArray().reduce((previousValue,
                currentValue) => (previousValue += currentValue.nilai), 0);

            if (nilai_transaksi > sisa_saldo) {
                alert('Total Transaksi melebihi sisa Bank, Cek Kembali !');
                return;
            }

            let tanya = confirm("Apakah data yang akan di-Validasi sudah benar ?");
            if (tanya == true) {
                $('#proses_upload').prop("disabled", true);
                let rincian_data = rincian_upload.rows().data().toArray().map((value) => {
                    let data = {
                        no_bukti: value.no_bukti,
                        tgl_bukti: value.tgl_bukti,
                        kd_skpd: value.kd_skpd,
                        ket: value.ket,
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
                $.ajax({
                    url: "{{ route('skpd.pelimpahan.proses_validasi') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        rincian_data: rincian_data,
                        tanggal_validasi: tanggal_validasi,
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('Data berhasil divalidasi');
                            window.location.href =
                                "{{ route('skpd.pelimpahan.validasi') }}";
                        } else {
                            alert('Data tidak berhasil divalidasi!');
                            $('#proses_upload').prop("disabled", false);
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

    function deleteData(no_bukti, total) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor Transaksi : ' + no_bukti);
        let tabel = $('#rincian_upload').DataTable();
        let total_transaksi = angka(document.getElementById('total_transaksi').value);
        if (tanya == true) {
            tabel.rows(function(idx, data, node) {
                return data.no_bukti == no_bukti
            }).remove().draw();
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi - parseFloat(total)));
        } else {
            return false;
        }
    }
</script>
