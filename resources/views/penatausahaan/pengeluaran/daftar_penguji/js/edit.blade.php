<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let rincian_penguji = $('#rincian_penguji').DataTable({
            processing: true,
            ordering: false,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('daftar_penguji.load_detail') }}",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "data": function(d) {
                    d.no_advice = document.getElementById('no_advice').value
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center"
                }, {
                    data: 'no_sp2d',
                    name: 'no_sp2d',
                },
                {
                    data: 'tgl_sp2d',
                    name: 'tgl_sp2d',
                },
                {
                    data: 'no_spm',
                    name: 'no_spm',
                },
                {
                    data: 'tgl_spm',
                    name: 'tgl_spm',
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                },
                {
                    data: 'bank',
                    name: 'bank',
                    visible: false
                },
                {
                    data: 'bic',
                    name: 'bic',
                    visible: false
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                },

            ]
        });

        // Load SP2D
        load_sp2d();

        $('#sp2d_online').prop('disabled', true);

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#no_sp2d').on('select2:select', function() {
            let sp2d_online = document.getElementById('sp2d_online').value;
            if (!sp2d_online) {
                alert('Silahkan Pilih SP2D Online!');
                $('#no_sp2d').val(null).change();
                return;
            }

            let no_sp2d = this.value;
            let tgl_sp2d = $(this).find(':selected').data('tgl_sp2d');
            let no_spm = $(this).find(':selected').data('no_spm');
            let tgl_spm = $(this).find(':selected').data('tgl_spm');
            let nilai = $(this).find(':selected').data('nilai');
            let bank = $(this).find(':selected').data('bank');
            let jns_spp = $(this).find(':selected').data('jns_spp');
            let jenis_beban = $(this).find(':selected').data('jenis_beban');
            let bic = $(this).find(':selected').data('bic').trim();

            let no_advice = document.getElementById('no_advice').value;
            let tanggal = document.getElementById('tanggal').value;
            if (!tanggal) {
                alert('Pilih tanggal terlebih dahulu!');
                $("#no_sp2d").val(null).change();
                return;
            }

            if ((sp2d_online == '1' && jns_spp == '4' && jenis_beban == '1') || (sp2d_online == '1' &&
                    jns_spp == '4' &&
                    jenis_beban == '10')) {
                alert('SP2D Gaji dan Tunjangan Tidak Dapat SP2D Online');
                return;
            }

            let tampungan = rincian_penguji.rows().data().toArray().map((value) => {
                let result = {
                    no_sp2d: value.no_sp2d,
                    tgl_sp2d: value.tgl_sp2d,
                    no_spm: value.no_spm,
                    tgl_spm: value.tgl_spm,
                    bank: value.bank,
                    bic: value.bic,
                };
                return result;
            });

            let daftar_bic = ["PDKBIDJ1"];

            let kondisi = tampungan.map(function(data) {
                if (data.no_sp2d == no_sp2d && data.no_spm == no_spm) {
                    return '1';
                }
                if (daftar_bic.includes(data.bic) == true) {
                    if (daftar_bic.includes(bic) == false) {
                        return '2';
                    }
                }

                if (daftar_bic.includes(data.bic) == false) {
                    if (daftar_bic.includes(bic) == true) {
                        return '3';
                    }
                }
            });

            if (tampungan.length > 10) {
                alert('Detail Penguji tidak boleh lebih dari 10 SP2D');
                return;
            }

            if (kondisi.includes("1")) {
                alert('Nomor SP2D ini sudah ada di LIST!');
                $("#no_sp2d").val(null).change();
                return;
            }
            if (kondisi.includes("2")) {
                alert('Dilist sudah ada Bank Kalbar,Tidak boleh pakai Bank Lain!');
                $("#no_sp2d").val(null).change();
                return;
            }

            if (kondisi.includes("3")) {
                alert('Dilist sudah ada Selain Bank Kalbar,Tidak boleh pakai Bank Kalbar!');
                $("#no_sp2d").val(null).change();
                return;
            }

            $.ajax({
                url: "{{ route('daftar_penguji.tambah_rincian') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_advice: no_advice,
                    no_sp2d: no_sp2d,
                    tanggal: tanggal,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil ditambahkan!');
                        load_sp2d();
                        rincian_penguji.ajax.reload();
                    } else {
                        alert('Data gagal ditambahkan!');
                        return;
                    }
                }
            })
            $("#no_sp2d").val(0).change();
        });

        $('#simpan_penguji').on('click', function() {

            let no_advice = document.getElementById('no_advice').value;
            let tanggal = document.getElementById('tanggal').value;
            let sp2d_online = document.getElementById('sp2d_online').value;
            if (!tanggal) {
                alert('Tanggal Bukti Tidak Boleh Kosong');
                return;
            }
            if (!sp2d_online) {
                alert('SP2D Online Tidak Boleh Kosong');
                return;
            }
            let rincian = rincian_penguji.rows().data().toArray();
            if (rincian.length == 0) {
                alert('Detail Daftar Penguji tidak boleh kosong!');
                return;
            }
            if (rincian.length > 10) {
                alert('Detail Daftar Penguji tidak boleh lebih dari 10 SP2D');
                return;
            }

            let detail_penguji = rincian_penguji.rows().data().toArray().map((value) => {
                let data = {
                    no_sp2d: value.no_sp2d,
                    tgl_sp2d: value.tgl_sp2d,
                    no_spm: value.no_spm,
                    tgl_spm: value.tgl_spm,
                    nilai: angka(value.nilai),
                };
                return data;
            });
            // simpan daftar penguji
            $('#simpan_penguji').prop("disabled", true);
            $.ajax({
                url: "{{ route('daftar_penguji.simpan_edit_penguji') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_advice: no_advice,
                    tanggal: tanggal,
                    sp2d_online: sp2d_online,
                    detail_penguji: detail_penguji,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil diubah!');
                        window.location.href = "{{ route('daftar_penguji.index') }}";
                    } else {
                        alert('Data tidak berhasil diubah!');
                        $('#simpan_penguji').prop("disabled", false);
                        return;
                    }
                }
            })
        });

        function angka(data) {
            let n1 = data.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }

    });

    function load_sp2d() {
        $.ajax({
            url: "{{ route('daftar_penguji.load_sp2d') }}",
            type: "POST",
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                $('#no_sp2d').empty();
                $('#no_sp2d').append(
                    `<option value="0" disabled selected>Silahkan Pilih</option>`);
                $.each(data, function(index, data) {
                    $('#no_sp2d').append(
                        `<option value="${data.no_sp2d}" data-tgl_sp2d="${data.tgl_sp2d}" data-no_spm="${data.no_spm}" data-tgl_spm="${data.tgl_spm}" data-nilai="${data.nilai}" data-bank="${data.bank}" data-bic="${data.bic}" data-jns_spp="${data.jns_spp}" data-jenis_beban="${data.jenis_beban}">${data.no_sp2d} | ${data.tgl_sp2d} | ${data.nama_bank} | ${data.nm_skpd}</option>`
                    );
                })
            }
        })
    }

    function deleteData(no_sp2d, no_spm, status) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor SP2D : ' + no_sp2d);
        let tabel = $('#rincian_penguji').DataTable();
        if (tanya == true) {
            $.ajax({
                url: "{{ route('daftar_penguji.hapus_detail_penguji') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_sp2d: no_sp2d,
                    no_advice: document.getElementById('no_advice').value,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil dihapus!');
                        load_sp2d();
                        tabel.ajax.reload();
                    } else {
                        alert('Data gagal dihapus!');
                        return;
                    }
                }
            })
            // tabel.rows(function(idx, data, node) {
            //     return data.no_sp2d == no_sp2d && data.no_spm == no_spm
            // }).remove().draw();
        } else {
            return false;
        }
    }
</script>
