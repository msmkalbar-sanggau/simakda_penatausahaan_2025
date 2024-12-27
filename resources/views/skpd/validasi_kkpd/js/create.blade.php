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
                    data: 'no_voucher',
                    name: 'no_voucher',
                },
                {
                    data: 'tgl_voucher',
                    name: 'tgl_voucher',
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
                    data: 'total',
                    name: 'total',
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
                    data: 'potongan',
                    name: 'potongan',
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
            let no_voucher = this.value;
            let total_transaksi = angka(document.getElementById('total_transaksi').value);
            let total = parseFloat($(this).find(':selected').data('total'));

            let tampungan = rincian_upload.rows().data().toArray().map((value) => {
                let result = {
                    no_voucher: value.no_voucher,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.no_voucher == no_voucher) {
                    return '1';
                }
            });
            if (kondisi.includes("1")) {
                alert('Nomor Transaksi ini sudah ada di LIST!');
                $("#data_transaksi").val(null).change();
                return;
            }

            rincian_upload.row.add({
                'no_voucher': no_voucher,
                'tgl_voucher': $(this).find(':selected').data('tgl'),
                'kd_skpd': $(this).find(':selected').data('kd_skpd'),
                'ket': $(this).find(':selected').data('ket'),
                'total': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format($(this).find(':selected').data('total')),
                'rekening_awal': $(this).find(':selected').data('rekening_awal'),
                'nm_rekening_tujuan': $(this).find(':selected').data('nm_rekening_tujuan'),
                'rekening_tujuan': $(this).find(':selected').data('rekening_tujuan'),
                'bank_tujuan': $(this).find(':selected').data('bank_tujuan'),
                'ket_tujuan': $(this).find(':selected').data('ket_tujuan'),
                'potongan': $(this).find(':selected').data('tot_pot'),
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${no_voucher}','${total}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi + total));
            $("#data_transaksi").val(null).change();
        });

        $('#proses_upload').on('click', function() {
            let total_transaksi = angka(document.getElementById('total_transaksi').value);
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

            let total_potongan = rincian_upload.rows().data().toArray().reduce((previousValue,
                currentValue) => (previousValue += currentValue.potongan), 0);

            let total_transfer = total_transaksi - total_potongan;
            if (total_transfer > sisa_saldo) {
                alert('Total Transaksi melebihi sisa Bank, Cek Kembali !');
                return;
            }

            let tanya = confirm("Apakah data yang akan di-Validasi sudah benar ?");
            if (tanya == true) {
                $('#proses_upload').prop("disabled", true);
                let rincian_data = rincian_upload.rows().data().toArray().map((value) => {
                    let data = {
                        no_voucher: value.no_voucher,
                        tgl_voucher: value.tgl_voucher,
                        kd_skpd: value.kd_skpd,
                        ket: value.ket,
                        total: angka(value.total),
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
                    url: "{{ route('skpd.transaksi_kkpd.proses_validasi') }}",
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
                                "{{ route('skpd.transaksi_kkpd.index_validasi') }}";
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

    function deleteData(no_voucher, total) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor Transaksi : ' + no_voucher);
        let tabel = $('#rincian_upload').DataTable();
        let total_transaksi = angka(document.getElementById('total_transaksi').value);
        if (tanya == true) {
            tabel.rows(function(idx, data, node) {
                return data.no_voucher == no_voucher
            }).remove().draw();
            $('#total_transaksi').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_transaksi - parseFloat(total)));
        } else {
            return false;
        }
    }
</script>
