<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let tabelBelanja = $('#spd_belanja').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: false,
            searchDelay: 1000,
            deferLoading: 0,
            ajax: {
                url: "{{ route('spd.spd_belanja.load_spd_belanja') }}",
                "type": "POST",
                data: function(data) {
                    data.kd_skpd = document.getElementById('kd_skpd').value;
                    data.jns_ang = document.getElementById('jenis_anggaran').value;
                    data.nomor = document.getElementById('nomor').value;
                    data.tanggal = document.getElementById('tanggal').value;
                    data.bln_awal = document.getElementById('bulan_awal').value;
                    data.bln_akhir = document.getElementById('bulan_akhir').value;
                    data.jenis = document.getElementById('jenis').value;
                    data.page = document.getElementById('idpage').value;
                    data.status_ang = document.getElementById('status_angkas').value;
                    if (document.getElementById("revisi").checked == true) {
                        data.revisi = '1';
                    } else {
                        data.revisi = '0';
                    }
                }
            },
            columns: [{
                    data: 'kd_unit',
                    name: 'kd_unit',
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: null,
                    name: 'nilai',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
                    },
                    "className": "text-right",
                },
                {
                    data: null,
                    name: 'lalu',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.lalu)
                    },
                    "className": "text-right",
                },
                {
                    data: null,
                    name: 'anggaran',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.anggaran)
                    },
                    "className": "text-right",
                }
            ]
        });

        let daftarSpdTempTable = $('#spd_belanja_temp').DataTable({
            responsive: true,
            // processing: true,
            serverSide: true,
            ordering: false,
            // searchDelay: 1000,
            // deferLoading: 0,
            lengthMenu: [
                [-1],
                ["All"]
            ],
            ajax: {
                "url": "{{ route('spd.spd_belanja.load_spd_belanja_temp') }}",
                "type": "POST",
                "data": function(d) {
                    d.kd_skpd = document.getElementById('kd_skpd').value;
                    d.jns_ang = document.getElementById('jenis_anggaran').value;
                    d.nomor = document.getElementById('nomor').value;
                    d.tanggal = document.getElementById('tanggal').value;
                    d.bln_awal = document.getElementById('bulan_awal').value;
                    d.bln_akhir = document.getElementById('bulan_akhir').value;
                    d.jenis = document.getElementById('jenis').value;
                    d.status_ang = document.getElementById('status_angkas').value;
                    d.page = document.getElementById('idpage').value;
                    if (document.getElementById("revisi").checked == true) {
                        d.revisi = '1';
                    } else {
                        d.revisi = '0';
                    }
                },
                "dataSrc": function(data) {
                    record = data.data;
                    return record;
                }
            },
            columns: [{
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                },
                {
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                },
                {
                    data: 'kd_rek6',
                    name: 'kd_rek6',
                },
                {
                    data: null,
                    name: 'nilai',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai)
                    },
                    "className": "text-right",
                },
                {
                    data: null,
                    name: 'nilai_lalu',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.nilai_lalu)
                    },
                    "className": "text-right",
                },
                {
                    data: null,
                    name: 'anggaran',
                    render: function(data, type, row, meta) {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2
                        }).format(data.anggaran)
                    },
                    "className": "text-right",
                }
            ],
            "drawCallback": function(settings) {
                let total = record.reduce((previousValue,
                    currentValue) => (previousValue += parseFloat(currentValue.nilai)), 0);
                $('#total').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total));
            },
        })

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#jenis').on('select2:select', function() {
            let jenis = document.getElementById('jenis').value;
            if (jenis == 5) {
                $('#revisi').prop('disabled', false)
            } else {
                $('#revisi').prop('disabled', true)
            }
        }).trigger('select2:select');

        // skpd
        $('#kd_skpd').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5',
            ajax: {
                delay: 1000,
                url: "{{ route('spd.spd_belanja.skpd') }}",
                type: 'POST',
                dataType: 'json',
                data: function(params) {
                    var query = {
                        term: params.term,
                    }
                    return query
                },
            },
            dropdownAutoWidth: true,
            templateResult: function(result) {
                if (!result.id) return 'Searching';
                return `${result.id} | ${result.text}`;
            },
            escapeMarkup: (m) => m,
            templateSelection: function(result) {
                return result.id || result.text;
            },
        });


        $('#kd_skpd').on('select2:select', function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
            var skpd = $(this).select2('data')[0];
            let tahun = "{{ tahun_anggaran() }}";
            let jenis_anggaran = document.getElementById('jenis_anggaran').value;
            $('#nip').val(null).trigger('change').trigger('select2:select');
            if (skpd) {
                $('#nm_skpd').val(skpd.nm_skpd)
                $('#nip').prop('disabled', false)
                $('#nomor').prop('disabled', false)
                $("#nomor").val('61.03/01.0//' + skpd.kd_skpd + '/' + jenis_anggaran + '/' +
                    bulanspd() + '/' + tahun)
                $('#jenis_anggaran').prop('disabled', false)
            } else {
                $('#nm_skpd').val(null)
                $('#nip').prop('disabled', true)
                $('#nomor').prop('disabled', true)
                $('#jenis_anggaran').prop('disabled', true)
            }
        }).trigger('select2:select');

        $('#jenis_anggaran').on('select2:select', function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
            let tahun = "{{ tahun_anggaran() }}";
            let skpd = document.getElementById('kd_skpd').value;
            let jenis_anggaran = document.getElementById('jenis_anggaran').value;
            $("#nomor").val('61.03/01.0//' + skpd + '/' + jenis_anggaran + '/' + bulanspd() + '/' +
                tahun)
        }).trigger('select2:select');

        $('#bulan_awal').on('select2:select', function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
            let tahun = "{{ tahun_anggaran() }}";
            let skpd = document.getElementById('kd_skpd').value;
            let jenis_anggaran = document.getElementById('jenis_anggaran').value;
            $("#nomor").val('61.03/01.0//' + skpd + '/' + jenis_anggaran + '/' + bulanspd() + '/' +
                tahun)
        }).trigger('select2:select');

        //nip
        $('#nip').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5',
            ajax: {
                delay: 1000,
                type: 'POST',
                url: "{{ route('spd.spd_belanja.nip') }}",
                dataType: 'json',
                data: function(params) {
                    var query = {
                        term: params.term,
                    }
                    var skpd = $('#kd_skpd').select2('data')[0]
                    if (skpd) query.kd_skpd = skpd.kd_skpd
                    return query
                },
            },
            dropdownAutoWidth: true,
            templateResult: function(result) {
                if (!result.id) return 'Searching';
                return `${result.id} | ${result.text}`;
            },
            escapeMarkup: (m) => m,
            templateSelection: function(result) {
                return result.id || result.text;
            },
        });

        $('#nip').on('select2:select', function() {
            var data = $(this).select2('data')[0]
            $('#nama_bend').val(data ? data.nama : null)
        });

        //jenis_anggaran
        $('#jenis_anggaran').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5',
            ajax: {
                delay: 1000,
                type: 'POST',
                url: "{{ route('spd.spd_belanja.jns_ang') }}",
                dataType: 'json',
                data: function(params) {
                    var query = {
                        term: params.term,
                    }
                    var skpd = $('#kd_skpd').select2('data')[0]
                    if (skpd) query.kd_skpd = skpd.kd_skpd
                    return query
                },
            },
            dropdownAutoWidth: true,
            templateResult: function(result) {
                if (!result.id) return 'Searching';
                return `${result.text}`;
            },
            escapeMarkup: (m) => m,
            templateSelection: function(result) {
                return result.text;
            },
        });

        $('#jenis_anggaran').on('select2:select', function() {
            let kd_skpd = $('#kd_skpd').val();
            let jns_ang = $('#jenis_anggaran').val();

            $.ajax({
                delay: 1000,
                url: "{{ route('spd.spd_belanja.cek_skpd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    jns_ang: jns_ang,
                },
                success: function(data) {
                    if (kd_skpd && jns_ang) {
                        if (data.message == '0') {
                            alert('SKPD Belum Lengkap Di Anggaran ' + data.nama);
                            return;
                        } else {
                            var jenis_anggaran = $(this).select2('data')[0];
                            $('#status_angkas').val(null).trigger('change').trigger(
                                'select2:select');
                            if (jenis_anggaran) {
                                $('#status_angkas').prop('disabled', false)
                            } else {
                                $('#status_angkas').prop('disabled', true)
                            }
                        }
                    }
                }
            });
        }).trigger('select2:select');

        //status angkas
        $('#status_angkas').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5',
            ajax: {
                delay: 1000,
                type: 'POST',
                url: "{{ route('spd.spd_belanja.status_angkas') }}",
                dataType: 'json',
                data: function(params) {
                    var query = {
                        term: params.term,
                    }
                    var skpd = $('#kd_skpd').select2('data')[0];
                    var jenis_anggaran = $('#jenis_anggaran').select2('data')[0]
                    if (skpd && jenis_anggaran) query.kd_skpd = skpd.kd_skpd;
                    query.kode = jenis_anggaran.kode;
                    return query
                },
            },
            dropdownAutoWidth: true,
            escapeMarkup: (m) => m,
            templateSelection: function(result) {
                return result.text;
            },
        });

        $('#status_angkas').on('select2:select', function() {
            let kd_skpd = $('#kd_skpd').val();
            let jns_ang = $('#jenis_anggaran').val();
            let sts_ang = $('#status_angkas').val();
            let bln_awal = document.getElementById('bulan_awal').value;
            let bln_akhir = document.getElementById('bulan_akhir').value;

            /*
            //MATIKAN SEMENTARA KEPERLUAN GAJI, SETELAH SELESAI NYALAKAN LAGI YA
            $.ajax({
                delay: 1000,
                url: "{{ route('spd.spd_belanja.cek_angkas') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    jns_ang: jns_ang,
                    sts_ang: sts_ang,
                    bln_awal: bln_awal,
                    bln_akhir: bln_akhir,
                },
                success: function(data) {

                    if (data.message == '0') {
                        alert(
                            'ADA KEGIATAN ANGGARAN KAS (RAK) DENGAN ANGGARAN (RKA) YANG MASIH SELISIH !!!'
                        );
                        $('#simpan_spd').prop('disabled', true);
                        $('#insert-all').prop('disabled', true);

                        return;
                    } else {
                        var status_angkas = $(this).select2('data')[0];
                    }

                }
            });
             //SAMPAI SINI
            */
        }).trigger('select2:select');

        $('#status_angkas').change(function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
        })

        $('#bulan_awal').change(function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
        })

        $('#bulan_akhir').change(function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
        })

        $('#tanggal').change(function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
        })

        $('#jenis').change(function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
        })

        $('#nomor').change(function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
        })

        $('#revisi').change(function() {
            tabelBelanja.clear().draw();
            daftarSpdTempTable.clear().draw();
        })

        $('#spd_belanja tbody').on('click', 'tr', function() {
            let tabel = tabelBelanja.row(this).data();
            if (!tabel) return
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanggal = document.getElementById('tanggal').value;
            let bln_awal = document.getElementById('bulan_awal').value;
            let bln_akhir = document.getElementById('bulan_akhir').value;
            let jns_ang = document.getElementById('jenis_anggaran').value;
            let jenis = document.getElementById('jenis').value;
            let page = document.getElementById('idpage').value;
            let status_ang = document.getElementById('status_angkas').value;
            if (document.getElementById("revisi").checked == true) {
                revisi = '1';
            } else {
                revisi = '0';
            }

            let data = {
                page: page,
                kd_skpd: kd_skpd,
                bln_awal: bln_awal,
                bln_akhir: bln_akhir,
                jns_ang: jns_ang,
                revisi: revisi,
                jenis: jenis,
                status_ang: status_ang,
                kd_rek6: tabel.kd_rek6,
                kd_sub_kegiatan: tabel.kd_sub_kegiatan,
                nilai: tabel.nilai,
                lalu: tabel.lalu,
                anggaran: tabel.anggaran,
            }
            $.ajax({
                delay: 1000,
                url: "{{ route('spd.spd_belanja.insert_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },

                success: function(data) {
                    if (data.message == '1') {
                        toastr.success(
                            'Sub Kegiatan dan Rekening telah berhasil ditambahkan');
                        tabelBelanja.clear().draw();
                        daftarSpdTempTable.clear().draw();
                    } else {
                        toastr.error('Sub Kegiatan dan Rekening gagal ditambahkan');
                        tabelBelanja.clear().draw();
                    }
                }
            })
        })

        $('#spd_belanja_temp tbody').on('click', 'tr', function() {
            let tabel = daftarSpdTempTable.row(this).data();
            if (!tabel) return
            let kd_skpd = document.getElementById('kd_skpd').value;
            let tanggal = document.getElementById('tanggal').value;
            let bln_awal = document.getElementById('bulan_awal').value;
            let bln_akhir = document.getElementById('bulan_akhir').value;
            let jns_ang = document.getElementById('jenis_anggaran').value;
            let jenis = document.getElementById('jenis').value;
            let page = document.getElementById('idpage').value;
            let status_ang = document.getElementById('status_angkas').value;
            if (document.getElementById("revisi").checked == true) {
                revisi = '1';
            } else {
                revisi = '0';
            }

            let data = {
                page: page,
                kd_skpd: kd_skpd,
                bln_awal: bln_awal,
                bln_akhir: bln_akhir,
                jns_ang: jns_ang,
                revisi: revisi,
                jenis: jenis,
                status_ang: status_ang,
                kd_rek6: tabel.kd_rek6,
                kd_sub_kegiatan: tabel.kd_sub_kegiatan,
                nilai: tabel.nilai,
                lalu: tabel.nilai_lalu,
                anggaran: tabel.anggaran,
            }

            $.ajax({
                delay: 1000,
                url: "{{ route('spd.spd_belanja.delete_spd_temp') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(data) {
                    if (data.message == '1') {
                        toastr.success('Sub Kegiatan dan Rekening telah berhasil dihapus');
                        daftarSpdTempTable.clear().draw();
                        tabelBelanja.clear().draw();
                    } else {
                        toastr.error('Sub Kegiatan dan Rekening gagal dihapus');
                        daftarSpdTempTable.clear().draw();
                    }
                }
            })
        })

        $('#insert-all').click(function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jns_ang = document.getElementById('jenis_anggaran').value;
            let nomor = document.getElementById('nomor').value;
            let tanggal = document.getElementById('tanggal').value;
            let bln_awal = document.getElementById('bulan_awal').value;
            let bln_akhir = document.getElementById('bulan_akhir').value;
            let jenis = document.getElementById('jenis').value;
            let page = document.getElementById('idpage').value;
            let status_ang = document.getElementById('status_angkas').value;
            if (document.getElementById("revisi").checked == true) {
                revisi = '1';
            } else {
                revisi = '0';
            }

            let data = {
                page: page,
                kd_skpd: kd_skpd,
                nomor: nomor,
                tanggal: tanggal,
                bln_awal: bln_awal,
                bln_akhir: bln_akhir,
                jns_ang: jns_ang,
                revisi: revisi,
                jenis: jenis,
                status_ang: status_ang,
            }
            $.ajax({
                // delay: 1000,
                url: "{{ route('spd.spd_belanja.insert_all_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    if (data.message == '1') {
                        toastr.success(
                            'Sub Kegiatan dan Rekening telah berhasil ditambahkan');
                        tabelBelanja.clear().draw();
                        daftarSpdTempTable.clear().draw();
                    } else {
                        toastr.error('Sub Kegiatan dan Rekening gagal ditambahkan');
                        tabelBelanja.clear().draw();
                    }
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        })

        $('#delete-all').click(function() {
            let kd_skpd = document.getElementById('kd_skpd').value;
            let jns_ang = document.getElementById('jenis_anggaran').value;
            let bln_awal = document.getElementById('bulan_awal').value;
            let bln_akhir = document.getElementById('bulan_akhir').value;
            let jenis = document.getElementById('jenis').value;
            let page = document.getElementById('idpage').value;
            let status_ang = document.getElementById('status_angkas').value;
            if (document.getElementById("revisi").checked == true) {
                revisi = '1';
            } else {
                revisi = '0';
            }

            let data = {
                page: page,
                kd_skpd: kd_skpd,
                bln_awal: bln_awal,
                bln_akhir: bln_akhir,
                jns_ang: jns_ang,
                revisi: revisi,
                jenis: jenis,
                status_ang: status_ang,
            }
            $.ajax({
                // delay: 1000,
                url: "{{ route('spd.spd_belanja.delete_all_spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(data) {
                    if (data.message == '1') {
                        toastr.success('Sub Kegiatan dan Rekening telah berhasil dihapus');
                        tabelBelanja.clear().draw();
                        daftarSpdTempTable.clear().draw();
                    } else {
                        toastr.error('Sub Kegiatan dan Rekening gagal dihapus');
                        tabelBelanja.clear().draw();
                    }
                }
            })
        })

        $('#simpan_spd').on('click', function() {
            let kd_skpd = $('#kd_skpd').select2('data')[0];
            let skpd = kd_skpd.kd_skpd;
            let nip = $('#nip').select2('data')[0];
            let nipp = nip.nip;
            let nomor = document.getElementById('nomor').value;
            let tanggal = document.getElementById('tanggal').value;
            let bulan_awal = document.getElementById('bulan_awal').value;
            let bulan_akhir = document.getElementById('bulan_akhir').value;
            let jenis = document.getElementById('jenis').value;
            let tahun = "{{ tahun_anggaran() }}";
            let tahun_input = tanggal.substr(0, 4);
            if (document.getElementById("revisi").checked == true) {
                revisi = '1';
            } else {
                revisi = '0';
            }
            let jenis_anggaran = document.getElementById('jenis_anggaran').value;
            let status_angkas = document.getElementById('status_angkas').value;
            let keterangan = document.getElementById('keterangan').value;

            if (tahun_input != tahun) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }

            let daftar_spd1 = daftarSpdTempTable.rows().data().toArray().map((value) => {
                let data = {
                    kd_skpd: value.kd_skpd,
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nilai: value.nilai,
                };
                return data;
            });

            if (daftar_spd1.length == 0) {
                alert('Daftar Rincian Tidak Boleh Kosong');
                return;
            }

            const totalNilai = daftar_spd1.reduce((prev, current) => prev + parseFloat(current.nilai),
                0);

            let daftar_spd = JSON.stringify(daftar_spd1);


            if (!kd_skpd) {
                alert('SKPD Tidak Boleh Kosong');
                return;
            }

            if (!nip) {
                alert('NIP Tidak Boleh Kosong');
                return;
            }

            if (!nomor) {
                alert('Nomor SPD Tidak Boleh Kosong');
                return;
            }

            if (!tanggal) {
                alert('tanggal Tidak Boleh Kosong');
                return;
            }

            if (!bulan_awal) {
                alert('Bulan Awal Tidak Boleh Kosong');
                return;
            }

            if (!bulan_akhir) {
                alert('Bulan Akhir Tidak Boleh Kosong');
                return;
            }

            if (!jenis) {
                alert('Beban Tidak Boleh Kosong');
                return;
            }

            if (!jenis_anggaran) {
                alert('jenis Anggaran Tidak Boleh Kosong');
                return;
            }

            if (!status_angkas) {
                alert('Status Angkas Tidak Boleh Kosong');
                return;
            }

            if (!keterangan) {
                alert('Keterangan Tidak Boleh Kosong');
                return;
            }
            if (jenis_anggaran == 'M') {
                if (nomor.length != 49) {
                    alert('Format Nomor SPD Belum Lengkap');
                    return;
                }
            } else {
                if (nomor.length != 51) {
                    alert('Format Nomor SPD Belum Lengkap');
                    return;
                }
            }

            let response = {
                skpd,
                nipp,
                nomor,
                tanggal,
                bulan_awal,
                bulan_akhir,
                jenis,
                revisi,
                jenis_anggaran,
                status_angkas,
                keterangan,
                totalNilai,
                daftar_spd
            };

            $('#simpan_spd').prop('disabled', true);
            $.ajax({
                url: "{{ route('spd.spd_belanja.simpanSpp') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: response
                },
                beforeSend: function() {
                    // Show image container
                    $("#loading").modal('show');
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data Berhasil Tersimpan!!!');
                        // return;
                        window.location.href = "{{ route('spd_belanja.index') }}"
                    } else if (data.message == '2') {
                        alert('Nomor SPD Sudah Digunakan!!!');
                        $('#simpan_spd').prop('disabled', false);
                        return;
                    } else {
                        alert("Data Gagal Tersimpan!!!");
                        $('#simpan_spd').prop('disabled', false);
                        return;
                    }
                },
                complete: function(data) {
                    // Hide image container
                    $("#loading").modal('hide');
                }
            })
        });

        function bulanspd() {
            let bln = document.getElementById('bulan_awal').value;
            let jenisbln = document.getElementById('jenisbln').value;
            // if (jenisbln == '1') {
            //     return bulan = bln;
            // } else {
            //     if (bln == '1' || bln == '2' || bln == '3') {
            //         return bulan = 1;
            //     } else if (bln == '4' || bln == '5' || bln == '6') {
            //         return bulan = 2;
            //     } else if (bln == '7' || bln == '8' || bln == '9') {
            //         return bulan = 3;
            //     } else if (bln == '10' || bln == '11' || bln == '12') {
            //         return bulan = 4;
            //     } else {
            //         return bulan = '';
            //     }
            // }
            if (bln == '1') {
                return bulan = 1;
            } else if (bln == '2') {
                return bulan = 2;
            } else if (bln == '3') {
                return bulan = 3;
            } else if (bln == '4') {
                return bulan = 4;
            } else if (bln == '5') {
                return bulan = 5;
            } else if (bln == '6') {
                return bulan = 6;
            } else if (bln == '7') {
                return bulan = 7;
            } else if (bln == '8') {
                return bulan = 8;
            } else if (bln == '8') {
                return bulan = 8;
            } else if (bln == '9') {
                return bulan = 9;
            } else if (bln == '10') {
                return bulan = 10;
            } else if (bln == '11') {
                return bulan = 11;
            } else if (bln == '12') {
                return bulan = 12;
            } else {
                return bulan = '';
            }
        }
    });
</script>
