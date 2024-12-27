<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#jenis').select2({
            disabled: true
        });
        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        let detail = $('#detail_sts').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('penerimaan_kas.detail_sts') }}",
                "type": "POST",
                "data": function(d) {
                    d.no_bukti = document.getElementById('no_bukti').value;
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                    d.jenis = document.getElementById('jenis').value;
                },
                "dataSrc": function(data) {
                    recordsTotal = data.data;
                    return recordsTotal;
                },
            },
            ordering: false,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: 'nm_rek',
                    name: 'nm_rek',
                },
                {
                    data: null,
                    name: 'rupiah',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.rupiah)
                    }
                },
                {
                    data: 'sumber',
                    name: 'sumber',
                    visible: false
                },
            ],
            drawCallback: function(select) {
                let total = recordsTotal.reduce((previousValue,
                    currentValue) => (previousValue += parseFloat(currentValue.rupiah)), 0);
                $('#total').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total));
            }
        });

        // $('#tgl_kas').on('change', function() {
        //     $('#kd_skpd').val(null).change();
        //     $('#nm_skpd').val(null);
        //     $('#no_bukti').empty();
        //     $('#tgl_bukti').val(null);
        //     $('#kd_sub_kegiatan').val(null);
        //     $('#nm_sub_kegiatan').val(null);
        //     $('#jenis').val(null).change();
        //     $('#keterangan').val(null);
        //     detail.ajax.reload();
        // });

        $('#kd_skpd').on('select2:select', function() {
            let tgl_kas = document.getElementById('tgl_kas').value;
            if (!tgl_kas) {
                alert('Silahkan pilih tanggal kas!');
                $('#kd_skpd').val(null).change();
                return;
            }
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);
            let kd_skpd = this.value;
            $('#no_bukti').empty();
            $('#tgl_bukti').val(null);
            $('#kd_sub_kegiatan').val(null);
            $('#nm_sub_kegiatan').val(null);
            $('#jenis').val(null).change();
            $('#keterangan').val(null);
            detail.ajax.reload();
            $.ajax({
                url: "{{ route('penerimaan_kas.no_bukti') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    tgl_kas: tgl_kas,
                },
                success: function(data) {
                    $('#no_bukti').empty();
                    $('#no_bukti').append(`<option value="">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#no_bukti').append(
                            `<option value="${data.no_sts}" data-tanggal="${data.tgl_sts}" data-keterangan="${data.keterangan}" data-kd_sub_kegiatan="${data.kd_sub_kegiatan}" data-jenis="${data.jns_trans}" data-jns_cp="${data.jns_cp}" data-sumber="${data.sumber}">${data.no_sts} | ${new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2}).format(data.total)} | ${data.keterangan}</option>`
                        );
                    })
                }
            })
        });

        $('#no_bukti').on('select2:select', function() {
            let tanggal = $(this).find(':selected').data('tanggal');
            let keterangan = $(this).find(':selected').data('keterangan');
            let kd_sub_kegiatan = $(this).find(':selected').data('kd_sub_kegiatan');
            let jenis = $(this).find(':selected').data('jenis');
            let jns_cp = $(this).find(':selected').data('jns_cp');
            let sumber = $(this).find(':selected').data('sumber');
            $('#tgl_bukti').val(tanggal);
            $('#keterangan').val(keterangan);
            $('#kd_sub_kegiatan').val(kd_sub_kegiatan);
            $('#sumber').val(sumber);
            $('#jns_cp').val(jns_cp);
            $.ajax({
                url: "{{ route('penerimaan_kas.nm_sub_kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: kd_sub_kegiatan,
                },
                success: function(data) {
                    $('#nm_sub_kegiatan').val(data);
                }
            })
            $("#jenis").select2("val", jenis);
            detail.ajax.reload();
        });

        $('#simpanedit').on('click', function() {
            let no_kas = document.getElementById('no_kas').value;
            let tgl_kas = document.getElementById('tgl_kas').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let no_bukti = document.getElementById('no_bukti').value;
            let tgl_bukti = document.getElementById('tgl_bukti').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            let jenis = document.getElementById('jenis').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let nm_sub_kegiatan = document.getElementById('nm_sub_kegiatan').value;
            let keterangan = document.getElementById('keterangan').value;
            let jns_cp = document.getElementById('jns_cp').value;
            let rek_bank = document.getElementById('pembayaran').value;
            let sumber = document.getElementById('sumber').value;
            let total = rupiah(document.getElementById('total').value);
            let tahun_input = tgl_kas.substr(0, 4);

            let detail_sts = detail.rows().data().toArray().map((value) => {
                let data = {
                    no_sts: value.no_sts,
                    kd_rek6: value.kd_rek6,
                    nm_rek: value.nm_rek,
                    nm_rek5: value.nm_rek5,
                    rupiah: parseFloat(value.rupiah),
                    sumber: value.sumber,
                };
                return data;
            });

            if (detail_sts.length == 0) {
                alert('Detail STS tidak boleh kosong!');
                return;
            }

            if (!no_kas) {
                alert('No Tidak Boleh Kosong');
                return;
            }

            if (!tgl_kas) {
                alert('Tanggal Tidak Boleh Kosong');
                return;
            }

            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            if (!kd_skpd) {
                alert('SKPD Tidak Boleh Kosong');
                return;
            }

            if (!no_bukti) {
                alert('No Bukti Tidak Boleh Kosong');
                return;
            }

            if (!rek_bank) {
                alert('Pembayaran Tidak Boleh Kosong');
                return;
            }

            let data = {
                no_kas,
                tgl_kas,
                kd_skpd,
                no_bukti,
                tgl_bukti,
                keterangan,
                total,
                jenis,
                kd_sub_kegiatan,
                jns_cp,
                rek_bank,
                sumber,
                detail_sts
            };

            $('#simpan').prop('disabled', true);
            $.ajax({
                url: "{{ route('penerimaan_kas.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data Berhasil divalidasi, Nomor Kas yang tersimpan adalah: ' +
                            response.nomor);
                        // window.location.href =
                        //     "{{ route('penerimaan_kas.index') }}";
                        location.reload();
                    } else if (response.message == '2') {
                        alert('Nomor telah digunakan!');
                        $('#simpan').prop('disabled', false);
                    } else {
                        alert('Data gagal disimpan!');
                        $('#simpan').prop('disabled', false);
                    }
                }
            })
        });

        $('#batalkas').on('click', function() {
            let no_kas = document.getElementById('no_kas').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let no_bukti = document.getElementById('no_bukti').value;
            //let tahun_input = tgl_kas.substr(0, 4);
            let tanya = confirm('Anda yakin akan Batal Validasi: ' + no_kas + '  ?');

            let detail_sts = detail.rows().data().toArray().map((value) => {
                let data = {
                    no_sts: value.no_sts,
                    kd_rek6: value.kd_rek6,
                    nm_rek: value.nm_rek,
                    nm_rek5: value.nm_rek5,
                    rupiah: parseFloat(value.rupiah),
                    sumber: value.sumber,
                };
                return data;
            });
            let data = {
                no_kas,
                kd_skpd,
                no_bukti
            };
            if (tanya == true) {
                $.ajax({
                    url: "{{ route('penerimaan_kas.batal') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        data: data
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('Data Berhasil Batal Validasi..!!');
                            // window.location.href = "{{ route('penerimaan_kas.index') }}";
                            location.reload();
                        } else {
                            alert('Gagal Validasi..!!');
                            return;
                        }
                    }
                })
            }
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

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }

    function formatCurrency(input, blur) {
        // appends $ to value, validates decimal side
        // and puts cursor back in right position.

        // get input value
        var input_val = input.val();

        // don't validate empty input
        if (input_val === "") {
            return;
        }

        // original length
        var original_len = input_val.length;

        // initial caret position
        var caret_pos = input.prop("selectionStart");

        // check for decimal
        if (input_val.indexOf(".") >= 0) {

            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(".");

            // split number by decimal point
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            // add commas to left side of number
            left_side = formatNumber(left_side);

            // validate right side
            right_side = formatNumber(right_side);

            // On blur make sure 2 numbers after decimal
            if (blur === "blur") {
                right_side += "00";
            }

            // Limit decimal to only 2 digits
            right_side = right_side.substring(0, 2);

            // join number by .
            input_val = left_side + "." + right_side;

        } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // final formatting
            if (blur === "blur") {
                input_val += ".00";
            }
        }

        // send updated string to input
        input.val(input_val);

        // put caret back in the right position
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }

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
