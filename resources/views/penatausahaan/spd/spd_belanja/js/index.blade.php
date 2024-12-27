<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var format = function(num) {
            var str = num.toString().replace("", ""),
                parts = false,
                output = [],
                i = 1,
                formatted = null;
            if (str.indexOf(".") > 0) {
                parts = str.split(".");
                str = parts[0];
            }
            str = str.split("").reverse();
            for (var j = 0, len = str.length; j < len; j++) {
                if (str[j] != ",") {
                    output.push(str[j]);
                    if (i % 3 == 0 && j < (len - 1)) {
                        output.push(",");
                    }
                    i++;
                }
            }
            formatted = output.reverse().join("");
            return ("" + formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
        };

        let data_awal = $('#spd_belanja').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 10, 20, 50],
            ajax: {
                "url": "{{ route('spd.spd_belanja.load_data') }}",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            columns: [
                // {
                //     data: 'DT_RowIndex',
                //     name: 'DT_RowIndex',
                //     className: "text-center",
                // },
                {
                    data: 'no_spd',
                    name: 'no_spd',
                    render: function(data, type, row) {
                        return '<font size="2px">' + row.no_spd + '</font>';
                    },
                },
                {
                    data: 'tgl_spd',
                    name: 'tgl_spd',
                    render: function(data, type, row) {
                        return '<font size="2px">' + row.tgl_spd + '</font>';
                    },
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                    render: function(data, type, row) {
                        return '<font size="2px">' + row.nm_skpd + '</font>';
                    },
                },

                {
                    data: 'total',
                    name: 'total',
                    render: function(data, type, row) {
                        return '<font size="2px" align="right">' + format(row.total) +
                            '</font>';
                    }
                },
                {
                    data: 'nm_beban',
                    name: 'nm_beban',
                    className: "text-center",
                    render: function(data, type, row) {
                        return '<font size="2px">' + row.nm_beban + '</font>';
                    },
                },
                {
                    data: 'revisi_ke',
                    name: 'revisi_ke',
                    className: "text-center",
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
            ],

            drawCallback: function(settings) {
                console.log('drawCallback');
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        $('#nip').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });
        $('#jenis').select2({
            dropdownParent: $('#modal_cetak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('#cetak-lampiran, #cetak-otorisasi').click(function() {
            var url = new URL($(this).data('url'))
            let nospd = document.getElementById('no_spd').value;
            let nip = $("#nip").val();
            let jenis = $("#jenis").val();
            let atas = $("#atas").val();
            let bawah = $("#bawah").val();
            let kiri = $("#kiri").val();
            let kanan = $("#kanan").val();
            let spasi = $("#spasi").val();

            if (document.getElementById("tambahan").checked == true) {
                tambahan = '1';
            } else {
                tambahan = '0';
            }

            if (!nip) {
                return alert('Bendahara PPKD Belum Dipilih')
            }

            if (!jenis) {
                return alert('Jenis Cetakkan Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('no_spd', nospd);
            searchParams.append('nip', nip);
            searchParams.append('tambahan', tambahan);
            searchParams.append('jenis', jenis);
            searchParams.append('atas', atas);
            searchParams.append('bawah', bawah);
            searchParams.append('kiri', kiri);
            searchParams.append('kanan', kanan);
            searchParams.append('spasi', spasi);
            window.open(url.toString(), "_blank");
        })
    });

    function cetak(no_spd) {
        $('#no_spd').val(no_spd);
        $('#modal_cetak').modal('show');
    }

    function hapusSPD(no_spd) {
        let tanya = confirm('Apakah anda yakin untuk menghapus data ini');
        if (tanya == true) {
            $.ajax({
                url: "{{ route('spd.spd_belanja.hapus_data_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spd: no_spd,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Proses Hapus Berhasil');
                        window.location.reload();
                    } else {
                        alert('Proses Hapus Gagal...!!!');
                    }
                }
            })
        } else {
            return false;
        }
    }
</script>
