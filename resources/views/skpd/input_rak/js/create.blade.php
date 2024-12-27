<style>
    .tw {
        text-align: right
    }

    #triwulan1 tr td:first-child,
    #triwulan2 tr td:first-child,
    #triwulan3 tr td:first-child,
    #triwulan4 tr td:first-child {
        vertical-align: middle
    }

    #triwulan1 tr>th,
    #triwulan2 tr>th,
    #triwulan3 tr>th,
    #triwulan4 tr>th {
        text-align: center
    }
</style>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let user = document.getElementById('username').value;

        // nanti dihidupkan awal tahun ya

        // if (user == 'superadmin' || user == 'pemprov' || user == 'HARDIMANSYAH' || user == 'srilestari' ||
        //     user ==
        //     'Sugino' || user == 'ROSNAWATI' || user == 'SAMANTO' || user == 'RISNAWATI' ||
        //     user == 'SYATHIBIE' || user == 'WAWAN' || user == 'willy' || user == 'SUYATNA' || user == 'yana' ||
        //     user == 'ANDAY68' || user == 'YULI' || user == 'NURMEIDA' || user == 'Sri Hartati' || user ==
        //     'SUYATNA22' || user == 'URAY ELPIDA' || user == 'maorin') {
        //     alert('Selamat Datang ' + user);
        // } else {
        //     proteksi();
        // }
        let role = "{{ $role }}";
        if (role == '1006' || role == '1012' || role == '1016' || role == '1017') {
            alert('Selamat Datang ' + user);
        } else {
            proteksi();
        }

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        let rekening = $('#rekening_rak').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                "url": "{{ route('skpd.input_rak.rekening_rak') }}",
                "type": "POST",
                "data": function(d) {
                    d.kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                    d.jenis_rak = document.getElementById('jenis_rak').value;
                    d.jenis_anggaran = document.getElementById('jenis_anggaran').value;
                }
            },
            createdRow: function(row, data, index) {
                if ((data.nilai - data.nilai_rak) == 0) {
                    $(row).css("background-color", "#ffffff");
                } else {
                    $(row).css("background-color", "#ff471a");
                }
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
                    data: null,
                    name: 'nilai_rak',
                    className: 'text-right',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai_rak)
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    className: 'text-center'
                },
            ]
        });

        $('#kd_skpd').on('select2:select', function() {
            let kd_skpd = this.value;
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd').val(nama);

            // Cari Jenis Anggaran
            $.ajax({
                url: "{{ route('skpd.input_rak.jenis_anggaran') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $('#jenis_anggaran').empty();
                    $('#jenis_anggaran').append(
                        `<option value="">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#jenis_anggaran').append(
                            `<option value="${data.kode}">${data.nama}</option>`
                        );
                    })
                }
            })
        });

        $('#jenis_anggaran').on('select2:select', function() {
            let jns_ang = this.value;

            // Kosongkan
            $('#jenis_rak').empty();
            $('#kd_sub_kegiatan').empty();
            $('#nm_sub_kegiatan').val(null);
            $('#kode_sub_kegiatan').val(null);
            $('#nama_sub_kegiatan').val(null);
            $('#nilai_anggaran').val(null);
            $('#anggaran_sub_kegiatan').val(null);
            rekening.ajax.reload();

            // Cari Jenis RAK
            $.ajax({
                url: "{{ route('skpd.input_rak.jenis_rak') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jns_ang: jns_ang
                },
                success: function(data) {
                    $('#jenis_rak').empty();
                    $('#jenis_rak').append(
                        `<option value="">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#jenis_rak').append(
                            `<option value="${data.kode}">${data.nama}</option>`
                        );
                    })
                }
            })
        });

        $('#jenis_rak').on('select2:select', function() {
            let jenis_rak = this.value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jns_ang = document.getElementById('jenis_anggaran').value;

            // Kosongkan
            $('#kd_sub_kegiatan').empty();
            $('#nm_sub_kegiatan').val(null);
            $('#kode_sub_kegiatan').val(null);
            $('#nama_sub_kegiatan').val(null);
            $('#nilai_anggaran').val(null);
            $('#anggaran_sub_kegiatan').val(null);
            rekening.ajax.reload();

            // Cari Sub Kegiatan
            $.ajax({
                url: "{{ route('skpd.input_rak.sub_kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jns_ang: jns_ang,
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="">Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}" data-total="${data.total}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                }
            })
            // CEK STATUS KUNCI
            status_kunci();
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            let kd_sub_kegiatan = this.value;
            let nama = $(this).find(':selected').data('nama');
            let total = $(this).find(':selected').data('total');
            $('#nm_sub_kegiatan').val(nama);
            $('#kode_sub_kegiatan').val(kd_sub_kegiatan);
            $('#nama_sub_kegiatan').val(nama);
            $('#nilai_anggaran').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total));
            $('#anggaran_sub_kegiatan').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total));
            rekening.ajax.reload();
        });

        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });

        $('#simpan_detail').on('click', function() {
            let proteksi_januari = document.getElementById('rak_januari');
            let proteksi_februari = document.getElementById('rak_februari');
            let proteksi_maret = document.getElementById('rak_maret');
            let proteksi_april = document.getElementById('rak_april');
            let proteksi_mei = document.getElementById('rak_mei');
            let proteksi_juni = document.getElementById('rak_juni');
            let proteksi_juli = document.getElementById('rak_juli');
            let proteksi_agustus = document.getElementById('rak_agustus');
            let proteksi_september = document.getElementById('rak_september');
            let proteksi_oktober = document.getElementById('rak_oktober');
            let proteksi_november = document.getElementById('rak_november');
            let proteksi_desember = document.getElementById('rak_desember');

            let rak_januari = document.getElementById('rak_januari').value;
            let rak_februari = document.getElementById('rak_februari').value;
            let rak_maret = document.getElementById('rak_maret').value;
            let rak_april = document.getElementById('rak_april').value;
            let rak_mei = document.getElementById('rak_mei').value;
            let rak_juni = document.getElementById('rak_juni').value;
            let rak_juli = document.getElementById('rak_juli').value;
            let rak_agustus = document.getElementById('rak_agustus').value;
            let rak_september = document.getElementById('rak_september').value;
            let rak_oktober = document.getElementById('rak_oktober').value;
            let rak_november = document.getElementById('rak_november').value;
            let rak_desember = document.getElementById('rak_desember').value;

            if (proteksi_januari.disabled) {
                rak_januari = rupiah(rak_januari); //BULAN 1
            } else {
                rak_januari = angka(rak_januari); //BULAN 1
            }
            if (proteksi_februari.disabled) {
                rak_februari = rupiah(rak_februari); //BULAN 2
            } else {
                rak_februari = angka(rak_februari); //BULAN 2
            }
            if (proteksi_maret.disabled) {
                rak_maret = rupiah(rak_maret); //BULAN 3
            } else {
                rak_maret = angka(rak_maret); //BULAN 3
            }
            if (proteksi_april.disabled) {
                rak_april = rupiah(rak_april); //BULAN 4
            } else {
                rak_april = angka(rak_april); //BULAN 4
            }
            if (proteksi_mei.disabled) {
                rak_mei = rupiah(rak_mei); //BULAN 5
            } else {
                rak_mei = angka(rak_mei); //BULAN 5
            }
            if (proteksi_juni.disabled) {
                rak_juni = rupiah(rak_juni); //BULAN 6
            } else {
                rak_juni = angka(rak_juni); //BULAN 6
            }
            if (proteksi_juli.disabled) {
                rak_juli = rupiah(rak_juli); //BULAN 7
            } else {
                rak_juli = angka(rak_juli); //BULAN 7
            }
            if (proteksi_agustus.disabled) {
                rak_agustus = rupiah(rak_agustus); //BULAN 8
            } else {
                rak_agustus = angka(rak_agustus); //BULAN 8
            }
            if (proteksi_september.disabled) {
                rak_september = rupiah(rak_september); //BULAN 9
            } else {
                rak_september = angka(rak_september); //BULAN 9
            }
            if (proteksi_oktober.disabled) {
                rak_oktober = rupiah(rak_oktober); //BULAN 10
            } else {
                rak_oktober = angka(rak_oktober); //BULAN 10
            }
            if (proteksi_november.disabled) {
                rak_november = rupiah(rak_november); //BULAN 11
            } else {
                rak_november = angka(rak_november); //BULAN 11
            }
            if (proteksi_desember.disabled) {
                rak_desember = rupiah(rak_desember); //BULAN 12
            } else {
                rak_desember = angka(rak_desember); //BULAN 12
            }

            let total_rak_tw1 = rupiah(document.getElementById('total_rak_tw1').value);
            let total_rak_tw2 = rupiah(document.getElementById('total_rak_tw2').value);
            let total_rak_tw3 = rupiah(document.getElementById('total_rak_tw3').value);
            let total_rak_tw4 = rupiah(document.getElementById('total_rak_tw4').value);

            //kalo mau pake koma ini matikan, kalo engga akan pke koma ini nyala kan
            // if (Number.isInteger(total_rak_tw1) == false) {
            //     alert('RAK Januari, Februari, Maret tidak boleh ada koma');
            //     return;
            // };
            // if (Number.isInteger(total_rak_tw2) == false) {
            //     alert('RAK April, Mei, Juni tidak boleh ada koma');
            //     return;
            // };
            // if (Number.isInteger(total_rak_tw3) == false) {
            //     alert('RAK Juli, Agustus, September tidak boleh ada koma');
            //     return;
            // };
            // if (Number.isInteger(total_rak_tw4) == false) {
            //     alert('RAK Oktober, November, Desember tidak boleh ada koma');
            //     return;
            // };

            let total_realisasi_tw1 = rupiah(document.getElementById('total_realisasi_tw1').value);
            let total_realisasi_tw2 = rupiah(document.getElementById('total_realisasi_tw2').value);
            let total_realisasi_tw3 = rupiah(document.getElementById('total_realisasi_tw3').value);
            let total_realisasi_tw4 = rupiah(document.getElementById('total_realisasi_tw4').value);

            let rak_terinput = rupiah(document.getElementById('rak_terinput').value);
            let rak_belum_terinput = rupiah(document.getElementById('rak_belum_terinput').value);

            let anggaran_sub_kegiatan = rupiah(document.getElementById('anggaran_sub_kegiatan').value);
            let anggaran_rekening = rupiah(document.getElementById('anggaran_rekening').value);

            let realisasi_januari = rupiah(document.getElementById('realisasi_januari').value);
            let realisasi_februari = rupiah(document.getElementById('realisasi_februari').value);
            let realisasi_maret = rupiah(document.getElementById('realisasi_maret').value);
            let realisasi_april = rupiah(document.getElementById('realisasi_april').value);
            let realisasi_mei = rupiah(document.getElementById('realisasi_mei').value);
            let realisasi_juni = rupiah(document.getElementById('realisasi_juni').value);
            let realisasi_juli = rupiah(document.getElementById('realisasi_juli').value);
            let realisasi_agustus = rupiah(document.getElementById('realisasi_agustus').value);
            let realisasi_september = rupiah(document.getElementById('realisasi_september').value);
            let realisasi_oktober = rupiah(document.getElementById('realisasi_oktober').value);
            let realisasi_november = rupiah(document.getElementById('realisasi_november').value);
            let realisasi_desember = rupiah(document.getElementById('realisasi_desember').value);

            let kd_skpd = document.getElementById('kd_skpd').value;
            let jenis_rak = document.getElementById('jenis_rak').value;
            let jenis_anggaran = document.getElementById('jenis_anggaran').value;
            let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;
            let kode_rekening = document.getElementById('kode_rekening').value;
            let proteksi_kegiatan = kd_sub_kegiatan.substr(5, 10);

            if (rak_januari < 0 || rak_februari < 0 || rak_maret < 0 || rak_april < 0 || rak_mei < 0 ||
                rak_juni < 0 || rak_juli < 0 || rak_agustus < 0 || rak_september < 0 || rak_oktober <
                0 || rak_november < 0 || rak_desember < 0 || total_rak_tw1 < 0 || total_rak_tw2 < 0 ||
                total_rak_tw3 < 0 || total_rak_tw4 < 0) {
                alert('Nilai tidak boleh kurang dari 0');
                return;
            }
            // // PROTEKSI SUB KEGIATAN GAJI
            if (proteksi_kegiatan == '01.1.02.01') {
                // RAK JANUARI SAMPAI FEBRUARI HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI
                let cek1 = (rak_januari + rak_februari - realisasi_januari);
                if (cek1 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari');
                    return;
                }

                // RAK JANUARI SAMPAI MARET HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI FEBRUARI
                let cek2 = (rak_januari + rak_februari + rak_maret) - (realisasi_januari +
                    realisasi_februari);
                if (cek2 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-Februari');
                    return;
                }

                // RAK JANUARI SAMPAI APRIL HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI MARET
                let cek3 = (rak_januari + rak_februari + rak_maret + rak_april) - (realisasi_januari +
                    realisasi_februari + realisasi_maret);
                if (cek3 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-Maret');
                    return;
                }

                // RAK JANUARI SAMPAI MEI HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI APRIL
                let cek4 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei) - (
                    realisasi_januari + realisasi_februari + realisasi_maret + realisasi_april);
                if (cek4 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-April');
                    return;
                }

                // RAK JANUARI SAMPAI JUNI HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI MEI
                let cek5 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni) - (
                    realisasi_januari + realisasi_februari + realisasi_maret + realisasi_april +
                    realisasi_mei);
                if (cek5 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-Mei');
                    return;
                }

                // RAK JANUARI SAMPAI JULI HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI JUNI
                let cek6 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli) - (realisasi_januari + realisasi_februari + realisasi_maret +
                    realisasi_april + realisasi_mei + realisasi_juni);
                if (cek6 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-Juni');
                    return;
                }

                // RAK JANUARI SAMPAI AGUSTUS HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI JULI
                let cek7 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus) - (realisasi_januari + realisasi_februari +
                    realisasi_maret + realisasi_april + realisasi_mei + realisasi_juni +
                    realisasi_juli);
                if (cek7 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-Juli');
                    return;
                }

                // RAK JANUARI SAMPAI SEPTEMBER HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI AGUSTUS
                let cek8 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus + rak_september) - (realisasi_januari +
                    realisasi_februari + realisasi_maret + realisasi_april + realisasi_mei +
                    realisasi_juni + realisasi_juli + realisasi_agustus);
                if (cek8 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-Agustus');
                    return;
                }

                // RAK JANUARI SAMPAI OKTOBER HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI SEPTEMBER
                let cek9 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus + rak_september + rak_oktober) - (realisasi_januari +
                    realisasi_februari + realisasi_maret + realisasi_april + realisasi_mei +
                    realisasi_juni + realisasi_juli + realisasi_agustus + realisasi_september);
                if (cek9 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-September');
                    return;
                }

                // RAK JANUARI SAMPAI NOVEMBER HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI OKTOBER
                let cek10 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus + rak_september + rak_oktober + rak_november) - (
                    realisasi_januari + realisasi_februari + realisasi_maret + realisasi_april +
                    realisasi_mei + realisasi_juni + realisasi_juli + realisasi_agustus +
                    realisasi_september + realisasi_oktober);
                if (cek10 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-Oktober');
                    return;
                }

                // RAK JANUARI SAMPAI DESEMBER HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI NOVEMBER
                let cek11 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus + rak_september + rak_oktober + rak_november +
                    rak_desember) - (realisasi_januari + realisasi_februari + realisasi_maret +
                    realisasi_april + realisasi_mei + realisasi_juni + realisasi_juli +
                    realisasi_agustus + realisasi_september + realisasi_oktober + realisasi_november
                );
                if (cek11 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-November');
                    return;
                }

                // RAK JANUARI SAMPAI DESEMBER HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI DESEMBER
                let cek12 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus + rak_september + rak_oktober + rak_november +
                    rak_desember) - (realisasi_januari + realisasi_februari + realisasi_maret +
                    realisasi_april + realisasi_mei + realisasi_juni + realisasi_juli +
                    realisasi_agustus + realisasi_september + realisasi_oktober +
                    realisasi_november + realisasi_desember
                );
                if (cek12 < 0) {
                    alert('Akumulasi RAK tidak boleh kurang dari realisasi Bulan Januari-Desember');
                    return;
                } else {
                    alert('Data berhasil disimpan!');
                }
            } else {
                // RAK JANUARI HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI
                let cek1 = (rak_januari - realisasi_januari);
                if (cek1 < 0) {
                    alert('RAK tidak boleh kurang dari realisasi Bulan Januari');
                    return;
                }

                // RAK JANUARI SAMPAI FEBRUARI HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI FEBRUARI
                let cek2 = (rak_januari + rak_februari) - (realisasi_januari + realisasi_februari);
                if (cek2 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-Februari'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI MARET HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI MARET
                let cek3 = (rak_januari + rak_februari + rak_maret) - (realisasi_januari +
                    realisasi_februari + realisasi_maret);
                if (cek3 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-Maret'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI APRIL HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI APRIL
                let cek4 = (rak_januari + rak_februari + rak_maret + rak_april) - (
                    realisasi_januari + realisasi_februari + realisasi_maret + realisasi_april);
                if (cek4 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-April'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI MEI HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI MEI
                let cek5 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei) - (
                    realisasi_januari + realisasi_februari + realisasi_maret + realisasi_april +
                    realisasi_mei);
                if (cek5 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-Mei'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI JUNI HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI JUNI
                let cek6 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni) - (
                    realisasi_januari + realisasi_februari + realisasi_maret + realisasi_april +
                    realisasi_mei + realisasi_juni);
                if (cek6 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-Juni'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI JULI HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI JULI
                let cek7 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli) - (realisasi_januari + realisasi_februari + realisasi_maret +
                    realisasi_april + realisasi_mei + realisasi_juni + realisasi_juli);
                if (cek7 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-Juli'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI AGUSTUS HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI AGUSTUS
                let cek8 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus) - (realisasi_januari + realisasi_februari +
                    realisasi_maret + realisasi_april + realisasi_mei + realisasi_juni +
                    realisasi_juli + realisasi_agustus);
                if (cek8 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-Agustus'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI SEPTEMBER HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI SEPTEMBER
                let cek9 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus + rak_september) - (realisasi_januari +
                    realisasi_februari + realisasi_maret + realisasi_april + realisasi_mei +
                    realisasi_juni + realisasi_juli + realisasi_agustus + realisasi_september);
                if (cek9 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-September'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI OKTOBER HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI OKTOBER
                let cek10 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus + rak_september + rak_oktober) - (realisasi_januari +
                    realisasi_februari + realisasi_maret + realisasi_april + realisasi_mei +
                    realisasi_juni + realisasi_juli + realisasi_agustus + realisasi_september +
                    realisasi_oktober);
                if (cek10 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-Oktober'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI NOVEMBER HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI NOVEMBER
                let cek11 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus + rak_september + rak_oktober + rak_november) - (
                    realisasi_januari + realisasi_februari + realisasi_maret + realisasi_april +
                    realisasi_mei + realisasi_juni + realisasi_juli + realisasi_agustus +
                    realisasi_september + realisasi_oktober + realisasi_november
                );
                if (cek11 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-November'
                    );
                    return;
                }

                // RAK JANUARI SAMPAI DESEMBER HARUS LEBIH BESAR ATAU SAMA DENGAN REALISASI JANUARI SAMPAI DESEMBER
                let cek12 = (rak_januari + rak_februari + rak_maret + rak_april + rak_mei + rak_juni +
                    rak_juli + rak_agustus + rak_september + rak_oktober + rak_november +
                    rak_desember) - (realisasi_januari + realisasi_februari + realisasi_maret +
                    realisasi_april + realisasi_mei + realisasi_juni + realisasi_juli +
                    realisasi_agustus + realisasi_september + realisasi_oktober +
                    realisasi_november + realisasi_desember
                );
                if (cek12 < 0) {
                    alert(
                        'Akumulasi RAK tidak boleh kurang dari Akumulasi realisasi Bulan Januari-Desember'
                    );
                    return;
                } else {
                    alert('Data berhasil disimpan!');
                }
            }

            if (rak_belum_terinput < 0) {
                alert('Pembagian Anggaran Melebihi Total Anggaran...!!!');
                return;
            }

            if (rak_belum_terinput > 0) {
                alert('Masih ada sisa Anggaran yang belum dibagi...!!!');
                return;
            }

            if (anggaran_rekening != rak_terinput) {
                alert('Total Rekening Tidak Sama...!!!');
                return;
            }

            if (total_rak_tw1 < total_realisasi_tw1) {
                alert('Nilai anggaran kurang dari nilai realisasi di Triwulan 1');
                return;
            }

            if (((total_rak_tw1 - total_realisasi_tw1) + total_rak_tw2) < total_realisasi_tw2) {
                alert('Nilai anggaran kurang dari nilai realisasi di Triwulan 2');
                return;
            }

            if (((total_rak_tw1 - total_realisasi_tw1) + (total_rak_tw2 - total_realisasi_tw2) +
                    total_rak_tw3) < total_realisasi_tw3) {
                alert('Nilai anggaran kurang dari nilai realisasi di Triwulan 3');
                return;
            }

            if (((total_rak_tw1 - total_realisasi_tw1) + (total_rak_tw2 - total_realisasi_tw2) + (
                        total_rak_tw3 - total_realisasi_tw3) +
                    total_rak_tw4) < total_realisasi_tw4) {
                alert('Nilai anggaran kurang dari nilai realisasi di Triwulan 4');
                return;
            }

            let bln1 = rak_januari;
            let bln2 = rak_februari;
            let bln3 = rak_maret;
            let bln4 = rak_april;
            let bln5 = rak_mei;
            let bln6 = rak_juni;
            let bln7 = rak_juli;
            let bln8 = rak_agustus;
            let bln9 = rak_september;
            let bln10 = rak_oktober;
            let bln11 = rak_november;
            let bln12 = rak_desember;

            let data = {
                jenis_rak,
                kd_skpd,
                jenis_rak,
                jenis_anggaran,
                kd_sub_kegiatan,
                kode_rekening,
                bln1,
                bln2,
                bln3,
                bln4,
                bln5,
                bln6,
                bln7,
                bln8,
                bln9,
                bln10,
                bln11,
                bln12,
                total_rak_tw1,
                total_rak_tw2,
                total_rak_tw3,
                total_rak_tw4,
            };

            if (rak_belum_terinput == 0) {
                $('#simpan_detail').prop('disabled', true);
                $.ajax({
                    url: "{{ route('skpd.input_rak.simpan_rak') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        data: data,
                    },
                    success: function(response) {
                        if (response.message == '1') {
                            // alert('Data Berhasil Tersimpan...!!!');
                            // window.location.reload();
                            $('#detail_rak').modal('hide');
                            rekening.ajax.reload();
                        } else if (response.message == '3') {
                            // alert('Anda tidak mempunyai akses...!!!');
                            window.location.href = "{{ route('403') }}";
                            $('#simpan_detail').prop('disabled', false);
                            return;
                        } else {
                            alert('Data Gagal Tersimpan...!!!');
                            $('#simpan_detail').prop('disabled', false);
                            return;
                        }
                    }
                })
            } else {
                alert('sisa Anggaran harus sama dengan nilai nol...!!!');
                return;
            }
        });

        function proteksi() {
            // let bulan = new Date().getMonth();
            let bulan = 0;
            let tahun = "{{ tahun_anggaran() }}";

            if (bulan == '0' && tahun == '2024') {

            } else if (bulan == '1' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
            } else if (bulan == '2' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
            } else if (bulan == '3' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
            } else if (bulan == '4' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
                $('#rak_april').prop('disabled', true);
            } else if (bulan == '5' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
                $('#rak_april').prop('disabled', true);
                $('#rak_mei').prop('disabled', true);
            } else if (bulan == '6' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
                $('#rak_april').prop('disabled', true);
                $('#rak_mei').prop('disabled', true);
                $('#rak_juni').prop('disabled', true);
            } else if (bulan == '7' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
                $('#rak_april').prop('disabled', true);
                $('#rak_mei').prop('disabled', true);
                $('#rak_juni').prop('disabled', true);
                $('#rak_juli').prop('disabled', true);
            } else if (bulan == '8' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
                $('#rak_april').prop('disabled', true);
                $('#rak_mei').prop('disabled', true);
                $('#rak_juni').prop('disabled', true);
                $('#rak_juli').prop('disabled', true);
                $('#rak_agustus').prop('disabled', true);
            } else if (bulan == '9' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
                $('#rak_april').prop('disabled', true);
                $('#rak_mei').prop('disabled', true);
                $('#rak_juni').prop('disabled', true);
                $('#rak_juli').prop('disabled', true);
                $('#rak_agustus').prop('disabled', true);
                $('#rak_september').prop('disabled', true);
            } else if (bulan == '10' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
                $('#rak_april').prop('disabled', true);
                $('#rak_mei').prop('disabled', true);
                $('#rak_juni').prop('disabled', true);
                $('#rak_juli').prop('disabled', true);
                $('#rak_agustus').prop('disabled', true);
                $('#rak_september').prop('disabled', true);
                $('#rak_oktober').prop('disabled', true);
            } else if (bulan == '11' && tahun == '2024') {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
                $('#rak_april').prop('disabled', true);
                $('#rak_mei').prop('disabled', true);
                $('#rak_juni').prop('disabled', true);
                $('#rak_juli').prop('disabled', true);
                $('#rak_agustus').prop('disabled', true);
                $('#rak_september').prop('disabled', true);
                $('#rak_oktober').prop('disabled', true);
                $('#rak_november').prop('disabled', true);
            } else {
                $('#rak_januari').prop('disabled', true);
                $('#rak_februari').prop('disabled', true);
                $('#rak_maret').prop('disabled', true);
                $('#rak_april').prop('disabled', true);
                $('#rak_mei').prop('disabled', true);
                $('#rak_juni').prop('disabled', true);
                $('#rak_juli').prop('disabled', true);
                $('#rak_agustus').prop('disabled', true);
                $('#rak_september').prop('disabled', true);
                $('#rak_oktober').prop('disabled', true);
                $('#rak_november').prop('disabled', true);
                $('#rak_desember').prop('disabled', true);
            }
        }
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

    function status_kunci() {
        $.ajax({
            url: "{{ route('skpd.input_rak.status_kunci') }}",
            type: "POST",
            dataType: 'json',
            data: {
                jenis_rak: document.getElementById('jenis_rak').value,
                kd_skpd: document.getElementById('kd_skpd').value
            },
            success: function(data) {
                if (data.status == '1') {
                    $('#simpan_detail').prop('disabled', true);
                    document.getElementById('informasi').removeAttribute('hidden');
                } else {
                    document.getElementById('informasi').setAttribute('hidden', true);
                    $('#simpan_detail').prop('disabled', false);
                }
            }
        })
    }

    function detail(kd_rek6, nm_rek6, nilai) {
        let kd_skpd = document.getElementById('kd_skpd').value;
        let jenis_rak = document.getElementById('jenis_rak').value;
        let kd_sub_kegiatan = document.getElementById('kd_sub_kegiatan').value;

        $("#rak_januari").val(null);
        $("#rak_februari").val(null);
        $("#rak_maret").val(null);
        $("#rak_april").val(null);
        $("#rak_mei").val(null);
        $("#rak_juni").val(null);
        $("#rak_juli").val(null);
        $("#rak_agustus").val(null);
        $("#rak_september").val(null);
        $("#rak_oktober").val(null);
        $("#rak_november").val(null);
        $("#rak_desember").val(null);
        // Isi Nilai RAK Triwulan 1 - 4
        $.ajax({
            url: "{{ route('skpd.input_rak.nilai_triwulan') }}",
            type: "POST",
            dataType: 'json',
            data: {
                kd_skpd: kd_skpd,
                jenis_rak: jenis_rak,
                kd_rek6: kd_rek6,
                kd_sub_kegiatan: kd_sub_kegiatan,
            },
            success: function(data) {
                nilai_rak_bulan(data);
            }
        })
        // Isi Nilai Realisasi Triwulan 1 - 4
        $.ajax({
            url: "{{ route('skpd.input_rak.nilai_realisasi') }}",
            type: "POST",
            dataType: 'json',
            data: {
                kd_skpd: kd_skpd,
                kd_sub_kegiatan: kd_sub_kegiatan,
                kd_rek6: kd_rek6,
            },
            success: function(data) {
                $('#total_realisasi_tw1').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.tw1.nilai));
                $('#total_realisasi_tw2').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.tw2.nilai));
                $('#total_realisasi_tw3').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.tw3.nilai));
                $('#total_realisasi_tw4').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.tw4.nilai));
            }
        })
        // Isi Nilai Realisai Masing-Masing Bulan
        $.ajax({
            url: "{{ route('skpd.input_rak.nilai_realisasi_bulan') }}",
            type: "POST",
            dataType: 'json',
            data: {
                kd_skpd: kd_skpd,
                kd_sub_kegiatan: kd_sub_kegiatan,
                kd_rek6: kd_rek6,
            },
            success: function(data) {
                nilai_realisasi_bulan(data);
            }
        })
        status_kunci();
        $('#kode_rekening').val(kd_rek6);
        $('#nama_rekening').val(nm_rek6);
        $('#anggaran_rekening').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(nilai));
        $('#detail_rak').modal('show');
    }

    function hitung() {
        let proteksi_januari = document.getElementById('rak_januari');
        let proteksi_februari = document.getElementById('rak_februari');
        let proteksi_maret = document.getElementById('rak_maret');
        let proteksi_april = document.getElementById('rak_april');
        let proteksi_mei = document.getElementById('rak_mei');
        let proteksi_juni = document.getElementById('rak_juni');
        let proteksi_juli = document.getElementById('rak_juli');
        let proteksi_agustus = document.getElementById('rak_agustus');
        let proteksi_september = document.getElementById('rak_september');
        let proteksi_oktober = document.getElementById('rak_oktober');
        let proteksi_november = document.getElementById('rak_november');
        let proteksi_desember = document.getElementById('rak_desember');
        // Anggaran Rekening
        let anggaran_rekening = rupiah(document.getElementById('anggaran_rekening').value);
        // RAK JANUARI-DESEMBER
        let rak_januari = document.getElementById('rak_januari').value;
        let rak_februari = document.getElementById('rak_februari').value;
        let rak_maret = document.getElementById('rak_maret').value;
        let rak_april = document.getElementById('rak_april').value;
        let rak_mei = document.getElementById('rak_mei').value;
        let rak_juni = document.getElementById('rak_juni').value;
        let rak_juli = document.getElementById('rak_juli').value;
        let rak_agustus = document.getElementById('rak_agustus').value;
        let rak_september = document.getElementById('rak_september').value;
        let rak_oktober = document.getElementById('rak_oktober').value;
        let rak_november = document.getElementById('rak_november').value;
        let rak_desember = document.getElementById('rak_desember').value;

        if (proteksi_januari.disabled) {
            rak_januari = rupiah(rak_januari); //BULAN 1
        } else {
            rak_januari = angka(rak_januari); //BULAN 1
        }
        if (proteksi_februari.disabled) {
            rak_februari = rupiah(rak_februari); //BULAN 2
        } else {
            rak_februari = angka(rak_februari); //BULAN 2
        }
        if (proteksi_maret.disabled) {
            rak_maret = rupiah(rak_maret); //BULAN 3
        } else {
            rak_maret = angka(rak_maret); //BULAN 3
        }
        if (proteksi_april.disabled) {
            rak_april = rupiah(rak_april); //BULAN 4
        } else {
            rak_april = angka(rak_april); //BULAN 4
        }
        if (proteksi_mei.disabled) {
            rak_mei = rupiah(rak_mei); //BULAN 5
        } else {
            rak_mei = angka(rak_mei); //BULAN 5
        }
        if (proteksi_juni.disabled) {
            rak_juni = rupiah(rak_juni); //BULAN 6
        } else {
            rak_juni = angka(rak_juni); //BULAN 6
        }
        if (proteksi_juli.disabled) {
            rak_juli = rupiah(rak_juli); //BULAN 7
        } else {
            rak_juli = angka(rak_juli); //BULAN 7
        }
        if (proteksi_agustus.disabled) {
            rak_agustus = rupiah(rak_agustus); //BULAN 8
        } else {
            rak_agustus = angka(rak_agustus); //BULAN 8
        }
        if (proteksi_september.disabled) {
            rak_september = rupiah(rak_september); //BULAN 9
        } else {
            rak_september = angka(rak_september); //BULAN 9
        }
        if (proteksi_oktober.disabled) {
            rak_oktober = rupiah(rak_oktober); //BULAN 10
        } else {
            rak_oktober = angka(rak_oktober); //BULAN 10
        }
        if (proteksi_november.disabled) {
            rak_november = rupiah(rak_november); //BULAN 11
        } else {
            rak_november = angka(rak_november); //BULAN 11
        }
        if (proteksi_desember.disabled) {
            rak_desember = rupiah(rak_desember); //BULAN 12
        } else {
            rak_desember = angka(rak_desember); //BULAN 12
        }

        let rak_tw1 = rak_januari + rak_februari + rak_maret;
        let rak_tw2 = rak_april + rak_mei + rak_juni;
        let rak_tw3 = rak_juli + rak_agustus + rak_september;
        let rak_tw4 = rak_oktober + rak_november + rak_desember;
        // Total RAK TRIWULAN 1
        $('#total_rak_tw1').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(rak_tw1));
        // Total RAK TRIWULAN 2
        $('#total_rak_tw2').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(rak_tw2));
        // Total RAK TRIWULAN 3
        $('#total_rak_tw3').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(rak_tw3));
        // Total RAK TRIWULAN 4
        $('#total_rak_tw4').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(rak_tw4));

        let rak_terinput = rak_tw1 + rak_tw2 + rak_tw3 + rak_tw4;
        $('#rak_terinput').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(rak_terinput));
        let selisih = anggaran_rekening - rak_terinput;
        $('#rak_belum_terinput').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(selisih));
    }

    function nilai_rak_bulan(data) {
        let proteksi_januari = document.getElementById('rak_januari');
        let proteksi_februari = document.getElementById('rak_februari');
        let proteksi_maret = document.getElementById('rak_maret');
        let proteksi_april = document.getElementById('rak_april');
        let proteksi_mei = document.getElementById('rak_mei');
        let proteksi_juni = document.getElementById('rak_juni');
        let proteksi_juli = document.getElementById('rak_juli');
        let proteksi_agustus = document.getElementById('rak_agustus');
        let proteksi_september = document.getElementById('rak_september');
        let proteksi_oktober = document.getElementById('rak_oktober');
        let proteksi_november = document.getElementById('rak_november');
        let proteksi_desember = document.getElementById('rak_desember');

        $.each(data, function(index, data) {
            let bulan = data.bulan;
            if (bulan == '1') {
                if (proteksi_januari.disabled) {
                    $('#rak_januari').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_januari").val(data.nilai);
                }
            }
            if (bulan == '2') {
                if (proteksi_februari.disabled) {
                    $('#rak_februari').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_februari").val(data.nilai);
                }
            }
            if (bulan == '3') {
                if (proteksi_maret.disabled) {
                    $('#rak_maret').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_maret").val(data.nilai);
                }
            }
            if (bulan == '4') {
                if (proteksi_april.disabled) {
                    $('#rak_april').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_april").val(data.nilai);
                }
            }
            if (bulan == '5') {
                if (proteksi_mei.disabled) {
                    $('#rak_mei').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_mei").val(data.nilai);
                }
            }
            if (bulan == '6') {
                if (proteksi_juni.disabled) {
                    $('#rak_juni').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_juni").val(data.nilai);
                }
            }
            if (bulan == '7') {
                if (proteksi_juli.disabled) {
                    $('#rak_juli').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_juli").val(data.nilai);
                }
            }
            if (bulan == '8') {
                if (proteksi_agustus.disabled) {
                    $('#rak_agustus').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_agustus").val(data.nilai);
                }
            }
            if (bulan == '9') {
                if (proteksi_september.disabled) {
                    $('#rak_september').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_september").val(data.nilai);
                }
            }
            if (bulan == '10') {
                if (proteksi_oktober.disabled) {
                    $('#rak_oktober').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_oktober").val(data.nilai);
                }
            }
            if (bulan == '11') {
                if (proteksi_november.disabled) {
                    $('#rak_november').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_november").val(data.nilai);
                }
            }
            if (bulan == '12') {
                if (proteksi_desember.disabled) {
                    $('#rak_desember').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.nilai));
                } else {
                    $("#rak_desember").val(data.nilai);
                }
            }
            // switch (bulan) {
            //     case '1':

            //     case '2':
            //         $("#rak_februari").val(data.nilai);
            //         break;
            //     case '3':
            //         $("#rak_maret").val(data.nilai);
            //         break;
            //     case '4':
            //         $("#rak_april").val(data.nilai);
            //         break;
            //     case '5':
            //         $("#rak_mei").val(data.nilai);
            //         break;
            //     case '6':
            //         $("#rak_juni").val(data.nilai);
            //         break;
            //     case '7':
            //         $("#rak_juli").val(data.nilai);
            //         break;
            //     case '8':
            //         $("#rak_agustus").val(data.nilai);
            //         break;
            //     case '9':
            //         $("#rak_september").val(data.nilai);
            //         break;
            //     case '10':
            //         $("#rak_oktober").val(data.nilai);
            //         break;
            //     case '11':
            //         $("#rak_november").val(data.nilai);
            //         break;
            //     case '12':
            //         $("#rak_desember").val(data.nilai);
            //         break;
            // }
        })
        hitung();
    }

    function nilai_realisasi_bulan(data) {
        // JANUARI
        $('#realisasi_januari').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.januari.nilai));
        // FEBRUARI
        $('#realisasi_februari').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.februari.nilai));
        // MARET
        $('#realisasi_maret').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.maret.nilai));
        // APRIL
        $('#realisasi_april').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.april.nilai));
        // MEI
        $('#realisasi_mei').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.mei.nilai));
        // JUNI
        $('#realisasi_juni').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.juni.nilai));
        // JULI
        $('#realisasi_juli').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.juli.nilai));
        // AGUSTUS
        $('#realisasi_agustus').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.agustus.nilai));
        // SEPTEMBER
        $('#realisasi_september').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.september.nilai));
        // OKTOBER
        $('#realisasi_oktober').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.oktober.nilai));
        // NOVEMBER
        $('#realisasi_november').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.november.nilai));
        // DESEMBER
        $('#realisasi_desember').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data.desember.nilai));
    }
</script>
