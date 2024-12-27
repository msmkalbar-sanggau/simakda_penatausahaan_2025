<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2-realisasi_pendapatan').select2({
            dropdownParent: $('#modal_realisasi_pendapatan .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-pembantu_penerimaan').select2({
            dropdownParent: $('#modal_pembantu_penerimaan .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-bku').select2({
            dropdownParent: $('#modal_bku .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-pajak_daerah').select2({
            dropdownParent: $('#modal_pajak_daerah .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-rekap_gaji').select2({
            dropdownParent: $('#modal_rekap_gaji .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-buku_besar_kasda').select2({
            dropdownParent: $('#modal_buku_besar_kasda .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-pembantu_pengeluaran').select2({
            dropdownParent: $('#modal_pembantu_pengeluaran .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-retribusi').select2({
            dropdownParent: $('#modal_retribusi .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-register_cp').select2({
            dropdownParent: $('#modal_register_cp .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-potongan_pajak').select2({
            dropdownParent: $('#modal_potongan_pajak .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-daftar_pengeluaran').select2({
            dropdownParent: $('#modal_daftar_pengeluaran .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-daftar_penerimaan').select2({
            dropdownParent: $('#modal_daftar_penerimaan .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-penerimaan_non_pendapatan').select2({
            dropdownParent: $('#modal_penerimaan_non_pendapatan .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-transfer_dana').select2({
            dropdownParent: $('#modal_transfer_dana .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-restitusi').select2({
            dropdownParent: $('#modal_restitusi .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-rth').select2({
            dropdownParent: $('#modal_rth .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-pengeluaran_non_sp2d').select2({
            dropdownParent: $('#modal_pengeluaran_non_sp2d .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-dth').select2({
            dropdownParent: $('#modal_dth .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-koreksi_penerimaan').select2({
            dropdownParent: $('#modal_koreksi_penerimaan .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-harian_kasda').select2({
            dropdownParent: $('#modal_harian_kasda .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-uyhd').select2({
            dropdownParent: $('#modal_uyhd .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-koreksi_pengeluaran').select2({
            dropdownParent: $('#modal_koreksi_pengeluaran .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-koreksi_penerimaan2').select2({
            dropdownParent: $('#modal_koreksi_penerimaan2 .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-register_sp2d').select2({
            dropdownParent: $('#modal_register_sp2d .modal-content'),
            theme: 'bootstrap-5'
        });

        $('.select2-cek_data_akuntansi').select2({
            dropdownParent: $('#modal_cek_data_akuntansi .modal-content'),
            theme: 'bootstrap-5'
        });

        // CETAKAN REALISASI PENDAPATAN
        $('#pilih_skpd_realisasi_pendapatan').hide();
        $('#pilih_unit_realisasi_pendapatan').hide();

        $('#pilihan_keseluruhan_realisasi_pendapatan').on('click', function() {
            $('#kd_skpd_realisasi_pendapatan').val(null).change();
            $('#nm_skpd_realisasi_pendapatan').val(null);
            $('#kd_unit_realisasi_pendapatan').val(null).change();
            $('#nm_unit_realisasi_pendapatan').val(null);
            $('#pilih_unit_realisasi_pendapatan').hide();
            $('#pilih_skpd_realisasi_pendapatan').hide();
        });

        $('#pilihan_skpd_realisasi_pendapatan').on('click', function() {
            $('#kd_skpd_realisasi_pendapatan').val(null).change();
            $('#nm_skpd_realisasi_pendapatan').val(null);
            $('#pilih_unit_realisasi_pendapatan').hide();
            $('#pilih_skpd_realisasi_pendapatan').show();
        });

        $('#pilihan_unit_realisasi_pendapatan').on('click', function() {
            $('#kd_unit_realisasi_pendapatan').val(null).change();
            $('#nm_unit_realisasi_pendapatan').val(null);
            $('#pilih_skpd_realisasi_pendapatan').hide();
            $('#pilih_unit_realisasi_pendapatan').show();
        });

        $('#kd_skpd_realisasi_pendapatan').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd_realisasi_pendapatan').val(nama);
        });

        $('#kd_unit_realisasi_pendapatan').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_unit_realisasi_pendapatan').val(nama);
        });

        $('#realisasi_pendapatan').on('click', function() {
            $('#modal_realisasi_pendapatan').modal('show');
        });

        $('.cetak_realisasi_pendapatan').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_realisasi_pendapatan')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_realisasi_pendapatan').checked;
            let unit = document.getElementById('pilihan_unit_realisasi_pendapatan').checked;

            if (keseluruhan == false) {
                if (skpd == false) {
                    if (unit == false) {
                        alert('Silahkan Pilih Keseluruhan, SKPD atau Unit!');
                        return;
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_realisasi_pendapatan').value;
            let kd_unit = document.getElementById('kd_unit_realisasi_pendapatan').value;
            let periode = document.getElementById('periode_realisasi_pendapatan').value;
            let anggaran = document.getElementById('anggaran_realisasi_pendapatan').value;
            let jenis = document.getElementById('jenis_realisasi_pendapatan').value;
            let ttd = document.getElementById('ttd_realisasi_pendapatan').value;
            let spasi = document.getElementById('spasi_realisasi_pendapatan').value;
            let tgl_ttd = document.getElementById('tgl_ttd_realisasi_pendapatan').value;
            let jenis_print = $(this).data("jenis");

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }
            if (unit) {
                if (!kd_unit) {
                    alert('Silahkan Pilih Unit!');
                    return;
                }
            }
            let pilihan = '';
            if (keseluruhan) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            } else if (unit) {
                pilihan = '3';
            }

            if (!periode) {
                alert('Periode tidak boleh kosong!');
                return;
            }
            if (!anggaran) {
                alert("Anggaran tidak boleh kosong!");
                return;
            }
            if (!jenis) {
                alert("Jenis tidak boleh kosong!");
                return;
            }
            let url = new URL("{{ route('laporan_bendahara_umum.realisasi_pendapatan') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode", periode);
            searchParams.append("anggaran", anggaran);
            searchParams.append("jenis", jenis);
            searchParams.append("ttd", ttd);
            searchParams.append("tgl_ttd", tgl_ttd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("kd_unit", kd_unit);
            searchParams.append("spasi", spasi);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN REALISASI PENDAPATAN

        // CETAKAN BUKU KAS PEMBANTU PENERIMAAN
        $('#pilih_tgl_pembantu_penerimaan').hide();
        $('#pilih_periode_pembantu_penerimaan').hide();

        $('#pilihan_tgl_pembantu_penerimaan').on('click', function() {
            $('#tgl_pembantu_penerimaan').val(null);
            $('#pilih_periode_pembantu_penerimaan').hide();
            $('#pilih_tgl_pembantu_penerimaan').show();
        });

        $('#pilihan_periode_pembantu_penerimaan').on('click', function() {
            $('#periode1_pembantu_penerimaan').val(null);
            $('#periode2_pembantu_penerimaan').val(null);
            $('#pilih_tgl_pembantu_penerimaan').hide();
            $('#pilih_periode_pembantu_penerimaan').show();
        });

        $('#pembantu_penerimaan').on('click', function() {
            $('#modal_pembantu_penerimaan').modal('show');
        });

        $('.cetak_pembantu_penerimaan').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_pembantu_penerimaan').checked;
            let pilih_periode = document.getElementById('pilihan_periode_pembantu_penerimaan').checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_pembantu_penerimaan').value;
            let periode1 = document.getElementById('periode1_pembantu_penerimaan').value;
            let periode2 = document.getElementById('periode2_pembantu_penerimaan').value;
            let halaman = document.getElementById('halaman_pembantu_penerimaan').value;
            let spasi = document.getElementById('spasi_pembantu_penerimaan').value;
            let ttd = document.getElementById('ttd_pembantu_penerimaan').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.buku_kas_pembantu_penerimaan') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN BUKU KAS PEMBANTU PENERIMAAN

        // CETAKAN BKU IX
        $('#pilih_tgl_bku').hide();
        $('#pilih_periode_bku').hide();

        $('#pilihan_tgl_bku').on('click', function() {
            $('#tgl_bku').val(null);
            $('#pilih_periode_bku').hide();
            $('#pilih_tgl_bku').show();
        });

        $('#pilihan_periode_bku').on('click', function() {
            $('#periode1_bku').val(null);
            $('#periode2_bku').val(null);
            $('#pilih_tgl_bku').hide();
            $('#pilih_periode_bku').show();
        });

        $('#bku').on('click', function() {
            $('#modal_bku').modal('show');
        });

        $('.cetak_bku').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_bku').checked;
            let pilih_periode = document.getElementById('pilihan_periode_bku').checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_bku').value;
            let periode1 = document.getElementById('periode1_bku').value;
            let periode2 = document.getElementById('periode2_bku').value;
            let st_rek = document.getElementById('status_rek').value;
            let halaman = document.getElementById('halaman_bku').value;
            let no_urut = document.getElementById('no_urut_bku').value;
            let jenis = document.getElementById('jenis_bku').value;
            let ttd = document.getElementById('ttd_bku').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            if (!jenis) {
                alert('Silahkan Pilih Jenis!');
                return;
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            if (pilihan == '1') {
                if (jenis == '2' || jenis == '3') {
                    alert("Maaf Untuk Jenis Tersebut Tidak Ada");
                    return;
                }
            }

            // if (pilihan == '2') {
            //     if (jenis == '4') {
            //         alert("Maaf Untuk Jenis Tersebut Tidak Ada");
            //         return;
            //     }
            // }

            if (jenis == '1') {
                let url = new URL("{{ route('laporan_bendahara_umum.bku_tanpa_tanggal1') }}");
                let searchParams = url.searchParams;
                searchParams.append("pilihan", pilihan);
                searchParams.append("periode1", periode1);
                searchParams.append("periode2", periode2);
                searchParams.append("st_rek", st_rek);
                searchParams.append("tgl", tgl);
                searchParams.append("halaman", halaman);
                searchParams.append("no_urut", no_urut);
                searchParams.append("ttd", ttd);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            }
            if (jenis == '2') {
                let url = new URL("{{ route('laporan_bendahara_umum.bku_dengan_tanggal1') }}");
                let searchParams = url.searchParams;
                searchParams.append("pilihan", pilihan);
                searchParams.append("periode1", periode1);
                searchParams.append("periode2", periode2);
                searchParams.append("st_rek", st_rek);
                searchParams.append("halaman", halaman);
                searchParams.append("no_urut", no_urut);
                searchParams.append("ttd", ttd);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            }
            if (jenis == '3') {
                let url = new URL("{{ route('laporan_bendahara_umum.bku_tanpa_blud') }}");
                let searchParams = url.searchParams;
                searchParams.append("pilihan", pilihan);
                searchParams.append("periode1", periode1);
                searchParams.append("periode2", periode2);
                searchParams.append("halaman", halaman);
                searchParams.append("no_urut", no_urut);
                searchParams.append("ttd", ttd);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            }
            if (jenis == '4') {
                if (pilih_tgl) {
                    let url = new URL("{{ route('laporan_bendahara_umum.bku_rincian1') }}");
                    let searchParams = url.searchParams;
                    searchParams.append("pilihan", pilihan);
                    searchParams.append("periode1", periode1);
                    searchParams.append("periode2", periode2);
                    searchParams.append("tgl", tgl);
                    searchParams.append("st_rek", st_rek);
                    searchParams.append("halaman", halaman);
                    searchParams.append("no_urut", no_urut);
                    searchParams.append("ttd", ttd);
                    searchParams.append("jenis_print", jenis_print);
                    window.open(url.toString(), "_blank");
                } else {
                    let url = new URL("{{ route('laporan_bendahara_umum.bku_rincianperiode') }}");
                    let searchParams = url.searchParams;
                    searchParams.append("pilihan", pilihan);
                    searchParams.append("periode1", periode1);
                    searchParams.append("periode2", periode2);
                    searchParams.append("tgl", tgl);
                    searchParams.append("st_rek", st_rek);
                    searchParams.append("halaman", halaman);
                    searchParams.append("no_urut", no_urut);
                    searchParams.append("ttd", ttd);
                    searchParams.append("jenis_print", jenis_print);
                    window.open(url.toString(), "_blank");
                }
            }
        });
        // CETAKAN BKU IX

        // CETAKAN PENERIMAAN PAJAK DAERAH
        $('#pilih_bulan_pajak_daerah').hide();
        $('#pilih_tanggal_pajak_daerah').hide();
        $('#pilih_pengirim_pajak_daerah').hide();
        $('#pilih_wilayah_pajak_daerah').hide();
        $('#pilih_rekap_pajak_daerah').hide();

        $('#pilih_tgl_pengirim_pajak_daerah').hide();
        $('#pilih_bulan_pengirim_pajak_daerah').hide();
        $('#pilih_tgl_wilayah_pajak_daerah').hide();
        $('#pilih_bulan_wilayah_pajak_daerah').hide();

        // PILIH PER BULAN
        $('#pilihan_bulan_pajak_daerah').on('click', function() {
            $('#bulan_pajak_daerah').val(null).change();

            $('#pilih_tanggal_pajak_daerah').hide();
            $('#pilih_pengirim_pajak_daerah').hide();
            $('#pilih_wilayah_pajak_daerah').hide();
            $('#pilih_rekap_pajak_daerah').hide();

            $('#pilih_tgl_pengirim_pajak_daerah').hide();
            $('#pilih_bulan_pengirim_pajak_daerah').hide();

            $('#pilih_tgl_wilayah_pajak_daerah').hide();
            $('#pilih_bulan_wilayah_pajak_daerah').hide();

            $('#pilih_bulan_pajak_daerah').show();
        });

        // PILIH PER TANGGAL
        $('#pilihan_tanggal_pajak_daerah').on('click', function() {
            $('#tgl_kas_pajak_daerah').val(null);
            $('#tgl_kas_sbl_pajak_daerah').val(null);

            $('#pilih_bulan_pajak_daerah').hide();
            $('#pilih_pengirim_pajak_daerah').hide();
            $('#pilih_wilayah_pajak_daerah').hide();
            $('#pilih_rekap_pajak_daerah').hide();

            $('#pilih_tgl_pengirim_pajak_daerah').hide();
            $('#pilih_bulan_pengirim_pajak_daerah').hide();

            $('#pilih_tgl_wilayah_pajak_daerah').hide();
            $('#pilih_bulan_wilayah_pajak_daerah').hide();

            $('#pilih_tanggal_pajak_daerah').show();
        });

        // PILIH PENGIRIM
        $('#pilihan_pengirim_pajak_daerah').on('click', function() {
            $('#pengirim_pajak_daerah').val(null).change();
            $('#nm_pengirim_pajak_daerah').val(null);

            $('#pilih_bulan_pajak_daerah').hide();
            $('#pilih_tanggal_pajak_daerah').hide();
            $('#pilih_wilayah_pajak_daerah').hide();
            $('#pilih_rekap_pajak_daerah').hide();

            $('#pilih_tgl_pengirim_pajak_daerah').hide();
            $('#pilih_bulan_pengirim_pajak_daerah').hide();

            $('#pilih_tgl_wilayah_pajak_daerah').hide();
            $('#pilih_bulan_wilayah_pajak_daerah').hide();

            $('#pilih_pengirim_pajak_daerah').show();
        });

        // PILIH PENGIRIM PER TANGGAL
        $('#pilihan_tgl_pengirim_pajak_daerah').on('click', function() {
            $('#tgl_kas_pengirim_pajak_daerah').val(null);
            $('#tgl_kas_sbl_pengirim_pajak_daerah').val(null);

            $('#pilih_bulan_pengirim_pajak_daerah').hide();

            $('#pilih_tgl_pengirim_pajak_daerah').show();
        });

        // PILIH PENGIRIM PER TANGGAL
        $('#pilihan_bulan_pengirim_pajak_daerah').on('click', function() {
            $('#bulan_pengirim1_pajak_daerah').val(null).change();
            $('#bulan_pengirim2_pajak_daerah').val(null).change();

            $('#pilih_tgl_pengirim_pajak_daerah').hide();

            $('#pilih_bulan_pengirim_pajak_daerah').show();
        });

        // PILIH WILAYAH
        $('#pilihan_wilayah_pajak_daerah').on('click', function() {
            $('#wilayah_pajak_daerah').val(null).change();
            $('#nm_wilayah_pajak_daerah').val(null);

            $('#pilih_bulan_pajak_daerah').hide();
            $('#pilih_tanggal_pajak_daerah').hide();
            $('#pilih_pengirim_pajak_daerah').hide();
            $('#pilih_rekap_pajak_daerah').hide();

            $('#pilih_tgl_pengirim_pajak_daerah').hide();
            $('#pilih_bulan_pengirim_pajak_daerah').hide();

            $('#pilih_tgl_wilayah_pajak_daerah').hide();
            $('#pilih_bulan_wilayah_pajak_daerah').hide();

            $('#pilih_wilayah_pajak_daerah').show();
        });

        // PILIH WILAYAH PER TANGGAL
        $('#pilihan_tgl_wilayah_pajak_daerah').on('click', function() {
            $('#tgl_kas_wilayah_pajak_daerah').val(null);
            $('#tgl_kas_sbl_wilayah_pajak_daerah').val(null);

            $('#pilih_bulan_wilayah_pajak_daerah').hide();

            $('#pilih_tgl_wilayah_pajak_daerah').show();
        });

        // PILIH WILAYAH PER TANGGAL
        $('#pilihan_bulan_wilayah_pajak_daerah').on('click', function() {
            $('#bulan_wilayah1_pajak_daerah').val(null).change();
            $('#bulan_wilayah2_pajak_daerah').val(null).change();

            $('#pilih_tgl_wilayah_pajak_daerah').hide();

            $('#pilih_bulan_wilayah_pajak_daerah').show();
        });

        // PILIH REKAP
        $('#pilihan_rekap_pajak_daerah').on('click', function() {
            $('#bulan_rekap1_pajak_daerah').val(null).change();
            $('#bulan_rekap2_pajak_daerah').val(null).change();

            $('#pilih_bulan_pajak_daerah').hide();
            $('#pilih_tanggal_pajak_daerah').hide();
            $('#pilih_pengirim_pajak_daerah').hide();
            $('#pilih_wilayah_pajak_daerah').hide();

            $('#pilih_tgl_pengirim_pajak_daerah').hide();
            $('#pilih_bulan_pengirim_pajak_daerah').hide();

            $('#pilih_tgl_wilayah_pajak_daerah').hide();
            $('#pilih_bulan_wilayah_pajak_daerah').hide();

            $('#pilih_rekap_pajak_daerah').show();
        });

        $('#pajak_daerah').on('click', function() {
            $('#modal_pajak_daerah').modal('show');
        });

        $('#pengirim_pajak_daerah').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_pengirim_pajak_daerah').val(nama);
        });

        $('#wilayah_pajak_daerah').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_wilayah_pajak_daerah').val(nama);
        });

        $('.cetak_pajak_daerah').on('click', function() {
            let pilih_bulan = document.getElementById('pilihan_bulan_pajak_daerah').checked;
            let pilih_tanggal = document.getElementById('pilihan_tanggal_pajak_daerah').checked;
            let pilih_pengirim = document.getElementById('pilihan_pengirim_pajak_daerah').checked;
            let pilih_wilayah = document.getElementById('pilihan_wilayah_pajak_daerah').checked;
            let pilih_rekap = document.getElementById('pilihan_rekap_pajak_daerah').checked;

            let pilih_tgl_pengirim = document.getElementById('pilihan_tgl_pengirim_pajak_daerah')
                .checked;
            let pilih_bulan_pengirim = document.getElementById('pilihan_bulan_pengirim_pajak_daerah')
                .checked;

            let pilih_tgl_wilayah = document.getElementById('pilihan_tgl_wilayah_pajak_daerah')
                .checked;
            let pilih_bulan_wilayah = document.getElementById('pilihan_bulan_wilayah_pajak_daerah')
                .checked;

            if (pilih_bulan == false) {
                if (pilih_tanggal == false) {
                    if (pilih_pengirim == false) {
                        if (pilih_wilayah == false) {
                            if (pilih_rekap == false) {
                                alert('Silahkan Pilih!!!');
                                return;
                            }
                        }
                    }
                }
            }

            let bulan = document.getElementById('bulan_pajak_daerah').value;
            let tgl_kas = document.getElementById('tgl_kas_pajak_daerah').value;
            let tgl_kas_sebelumnya = document.getElementById('tgl_kas_sbl_pajak_daerah').value;
            let pengirim = document.getElementById('pengirim_pajak_daerah').value;
            let wilayah = document.getElementById('wilayah_pajak_daerah').value;
            let halaman = document.getElementById('halaman_pajak_daerah').value;

            let tgl_kas_pengirim = document.getElementById('tgl_kas_pengirim_pajak_daerah').value;
            let tgl_kas_sbl_pengirim = document.getElementById('tgl_kas_sbl_pengirim_pajak_daerah')
                .value;
            let bulan_pengirim1 = document.getElementById('bulan_pengirim1_pajak_daerah').value;
            let bulan_pengirim2 = document.getElementById('bulan_pengirim2_pajak_daerah').value;

            let tgl_kas_wilayah = document.getElementById('tgl_kas_wilayah_pajak_daerah').value;
            let tgl_kas_sbl_wilayah = document.getElementById('tgl_kas_sbl_wilayah_pajak_daerah').value;
            let bulan_wilayah1 = document.getElementById('bulan_wilayah1_pajak_daerah').value;
            let bulan_wilayah2 = document.getElementById('bulan_wilayah2_pajak_daerah').value;

            let bulan_rekap1 = document.getElementById('bulan_rekap1_pajak_daerah').value;
            let bulan_rekap2 = document.getElementById('bulan_rekap2_pajak_daerah').value;

            let jenis_print = $(this).data("jenis");

            if (pilih_bulan) {
                if (!bulan) {
                    alert('Silahkan Pilih Bulan!');
                    return;
                }
            }

            if (pilih_tanggal) {
                if (!tgl_kas || !tgl_kas_sebelumnya) {
                    alert('Silahkan Pilih Tanggal Kas atau Tanggal Kas Sebelumnya!');
                    return;
                }
            }

            if (pilih_pengirim) {
                if (!pengirim) {
                    alert('Silahkan Pilih Nama Pengirim!');
                    return;
                }
                if (pilih_tgl_pengirim == false) {
                    if (pilih_bulan_pengirim == false) {
                        alert('Silahkan Pilih Per Tanggal atau Per Bulan');
                        return;
                    }
                }
                if (pilih_tgl_pengirim == true) {
                    if (!tgl_kas_pengirim || !tgl_kas_sbl_pengirim) {
                        alert('Silahkan isi Tanggal Kas!');
                        return;
                    }
                }
                if (pilih_bulan_pengirim == true) {
                    if (!bulan_pengirim1 || !bulan_pengirim2) {
                        alert('Silahkan isi Bulan!');
                        return;
                    }
                }
            }

            if (pilih_wilayah) {
                if (!wilayah) {
                    alert('Silahkan Pilih Nama Wilayah!');
                    return;
                }
                if (pilih_tgl_wilayah == false) {
                    if (pilih_bulan_wilayah == false) {
                        alert('Silahkan Pilih Per Tanggal atau Per Bulan');
                        return;
                    }
                }
                if (pilih_tgl_wilayah == true) {
                    if (!tgl_kas_wilayah || !tgl_kas_sbl_wilayah) {
                        alert('Silahkan isi Tanggal Kas!');
                        return;
                    }
                }
                if (pilih_bulan_wilayah == true) {
                    if (!bulan_wilayah1 || !bulan_wilayah2) {
                        alert('Silahkan isi Bulan!');
                        return;
                    }
                }
            }

            if (pilih_rekap) {
                if (!bulan_rekap1 || !bulan_rekap2) {
                    alert('Silahkan isi Bulan!');
                    return;
                }
            }

            if (!halaman) {
                alert('Halaman tidak boleh kosong!');
                return;
            }

            let pilihan = '';
            if (pilih_bulan) {
                pilihan = '1';
            } else if (pilih_tanggal) {
                pilihan = '2';
            } else if (pilih_pengirim) {
                if (pilih_tgl_pengirim) {
                    pilihan = '31';
                }
                if (pilih_bulan_pengirim) {
                    pilihan = '32';
                }
            } else if (pilih_wilayah) {
                if (pilih_tgl_wilayah) {
                    pilihan = '41';
                }
                if (pilih_bulan_wilayah) {
                    pilihan = '42';
                }
            } else if (pilih_rekap) {
                pilihan = '5';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.penerimaan_pajak_daerah') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("bulan_perbulan", bulan);
            searchParams.append("tgl_kas_pertanggal", tgl_kas);
            searchParams.append("tgl_kas_sbl_pertanggal", tgl_kas_sebelumnya);
            searchParams.append("pengirim", pengirim);
            searchParams.append("tgl_kas_pengirim", tgl_kas_pengirim);
            searchParams.append("tgl_kas_sbl_pengirim", tgl_kas_sbl_pengirim);
            searchParams.append("bulan1_pengirim", bulan_pengirim1);
            searchParams.append("bulan2_pengirim", bulan_pengirim2);
            searchParams.append("wilayah", wilayah);
            searchParams.append("tgl_kas_wilayah", tgl_kas_wilayah);
            searchParams.append("tgl_kas_sbl_wilayah", tgl_kas_sbl_wilayah);
            searchParams.append("bulan1_wilayah", bulan_wilayah1);
            searchParams.append("bulan2_wilayah", bulan_wilayah2);
            searchParams.append("bulan_rekap1", bulan_rekap1);
            searchParams.append("bulan_rekap2", bulan_rekap2);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN PENERIMAAN PAJAK DAERAH

        // CETAKAN REKAP GAJI
        $('#pilih_skpd_rekap_gaji').hide();

        $('#pilihan_keseluruhan_rekap_gaji').on('click', function() {
            $('#kd_skpd_rekap_gaji').val(null).change();
            $('#nm_skpd_rekap_gaji').val(null);

            $('#pilih_skpd_rekap_gaji').hide();
        });

        $('#pilihan_skpd_rekap_gaji').on('click', function() {
            $('#kd_skpd_rekap_gaji').val(null).change();
            $('#nm_skpd_rekap_gaji').val(null);

            $('#pilih_skpd_rekap_gaji').show();
        });

        $('#kd_skpd_rekap_gaji').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd_rekap_gaji').val(nama);
        });

        $('#rekap_gaji').on('click', function() {
            $('#modal_rekap_gaji').modal('show');
        });

        $('.cetak_rekap_gaji').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_rekap_gaji')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_rekap_gaji').checked;

            let keseluruhan1 = document.getElementById('pilihan_keseluruhan_rekap_gaji1')
                .checked;
            let bulan = document.getElementById('pilihan_bulan_rekap_gaji')
                .checked;
            let periode = document.getElementById('pilihan_periode_rekap_gaji')
                .checked;

            if (keseluruhan == false) {
                if (skpd == false) {
                    alert('Silahkan Pilih Keseluruhan atau SKPD!');
                    return;
                }
            }

            if (keseluruhan) {
                if (keseluruhan1 == false) {
                    if (bulan == false) {
                        if (periode == false) {
                            alert('Silahkan Pilih Keseluruhan, Bulan atau Periode!');
                            return;
                        }
                    }
                }
            }

            if (skpd) {
                if (keseluruhan1 == false) {
                    if (bulan == false) {
                        if (periode == false) {
                            alert('Silahkan Pilih Keseluruhan, Bulan atau Periode!');
                            return;
                        }
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_rekap_gaji').value;
            let bulan1 = document.getElementById('bulan_rekap_gaji').value;
            let periode1 = document.getElementById('periode1_rekap_gaji').value;
            let periode2 = document.getElementById('periode2_rekap_gaji').value;
            // let ttd = document.getElementById('ttd_rekap_gaji').value;
            let jenis_print = $(this).data("jenis");

            if (keseluruhan || skpd) {
                if (bulan) {
                    if (!bulan1) {
                        alert('Silahkan Pilih Bulan!');
                        return;
                    }
                }
            }

            if (keseluruhan || skpd) {
                if (periode) {
                    if (!periode1 || !periode2) {
                        alert('Silahkan Pilih Periode!');
                        return;
                    }
                }
            }

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }

            let pilihan = '';
            if (keseluruhan) {
                if (keseluruhan1) {
                    pilihan = '11';
                }
                if (bulan) {
                    pilihan = '12';
                }
                if (periode) {
                    pilihan = '13';
                }
            }
            if (skpd) {
                if (keseluruhan1) {
                    pilihan = '21';
                }
                if (bulan) {
                    pilihan = '22';
                }
                if (periode) {
                    pilihan = '23';
                }
            }

            let url = new URL("{{ route('laporan_bendahara_umum.rekap_gaji') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("bulan", bulan1);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            // searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN REKAP GAJI


        // CETAKAN BUKU BESAR KASDA
        $('#kd_skpd_buku_besar_kasda').on('select2:select', function() {
            let kd_skpd = this.value;
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara_penerimaan.rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#rekening_buku_besar_kasda').empty();
                    $('#rekening_buku_besar_kasda').append(
                        `<option value="" disabled selected>Silahkan Pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#rekening_buku_besar_kasda').append(
                            `<option value="${data.kd_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                }
            })
        });

        $('#buku_besar_kasda').on('click', function() {
            $('#modal_buku_besar_kasda').modal('show');
        });

        $('.cetak_buku_besar_kasda').on('click', function() {
            let skpd = document.getElementById('kd_skpd_buku_besar_kasda').value;
            let rekening = document.getElementById('rekening_buku_besar_kasda').value;
            let ttd = document.getElementById('ttd_buku_besar_kasda').value;
            let periode1 = document.getElementById('periode1_buku_besar_kasda')
                .value;
            let periode2 = document.getElementById('periode2_buku_besar_kasda')
                .value;
            let jenis_print = $(this).data("jenis");

            let url = new URL("{{ route('laporan_bendahara_umum.buku_besar_kasda') }}");
            let searchParams = url.searchParams;
            searchParams.append("kd_skpd", skpd);
            searchParams.append("rekening", rekening);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN BUKU BESAR KASDA

        // CETAKAN BUKU KAS PEMBANTU PENGELUARAN
        $('#pilih_tgl_pembantu_pengeluaran').hide();
        $('#pilih_periode_pembantu_pengeluaran').hide();

        $('#pilihan_tgl_pembantu_pengeluaran').on('click', function() {
            $('#tgl_pembantu_pengeluaran').val(null);
            $('#pilih_periode_pembantu_pengeluaran').hide();
            $('#pilih_tgl_pembantu_pengeluaran').show();
        });

        $('#pilihan_periode_pembantu_pengeluaran').on('click', function() {
            $('#periode1_pembantu_pengeluaran').val(null);
            $('#periode2_pembantu_pengeluaran').val(null);
            $('#pilih_tgl_pembantu_pengeluaran').hide();
            $('#pilih_periode_pembantu_pengeluaran').show();
        });

        $('#pembantu_pengeluaran').on('click', function() {
            $('#modal_pembantu_pengeluaran').modal('show');
        });

        $('.cetak_pembantu_pengeluaran').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_pembantu_pengeluaran').checked;
            let pilih_periode = document.getElementById('pilihan_periode_pembantu_pengeluaran').checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_pembantu_pengeluaran').value;
            let periode1 = document.getElementById('periode1_pembantu_pengeluaran').value;
            let periode2 = document.getElementById('periode2_pembantu_pengeluaran').value;
            let halaman = document.getElementById('halaman_pembantu_pengeluaran').value;
            let spasi = document.getElementById('spasi_pembantu_pengeluaran').value;
            let ttd = document.getElementById('ttd_pembantu_pengeluaran').value;
            let tipe = document.getElementById('tipe_pembantu_pengeluaran').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.buku_kas_pembantu_pengeluaran') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("tipe", tipe);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN BUKU KAS PEMBANTU PENGELUARAN

        // CETAKAN RETRIBUSI
        $('#retribusi').on('click', function() {
            $('#modal_retribusi').modal('show');
        });

        $('.cetak_retribusi').on('click', function() {
            let tgl = document.getElementById('tgl_retribusi').value;
            let halaman = document.getElementById('halaman_retribusi').value;
            let spasi = document.getElementById('spasi_retribusi').value;
            let ttd = document.getElementById('ttd_retribusi').value;
            let jenis_print = $(this).data("jenis");

            if (!tgl) {
                alert("Silahkan Pilih Tanggal!");
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.retribusi') }}");
            let searchParams = url.searchParams;
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        //CETAKAN RETRIBUSI


        // CETAKAN REGISTER CP
        $('#pilih_skpd_register_cp').hide();
        $('#pilih_unit_register_cp').hide();

        $('#pilihan_keseluruhan_register_cp').on('click', function() {
            $('#kd_skpd_register_cp').val(null).change();
            $('#nm_skpd_register_cp').val(null);
            $('#kd_unit_register_cp').val(null).change();
            $('#nm_unit_register_cp').val(null);
            $('#pilih_unit_register_cp').hide();
            $('#pilih_skpd_register_cp').hide();
        });

        $('#pilihan_rekap_register_cp').on('click', function() {
            $('#kd_skpd_register_cp').val(null).change();
            $('#nm_skpd_register_cp').val(null);
            $('#kd_unit_register_cp').val(null).change();
            $('#nm_unit_register_cp').val(null);
            $('#pilih_unit_register_cp').hide();
            $('#pilih_skpd_register_cp').hide();
        });

        $('#pilihan_skpd_register_cp').on('click', function() {
            $('#kd_skpd_register_cp').val(null).change();
            $('#nm_skpd_register_cp').val(null);
            $('#pilih_unit_register_cp').hide();
            $('#pilih_skpd_register_cp').show();
        });

        $('#pilihan_unit_register_cp').on('click', function() {
            $('#kd_unit_register_cp').val(null).change();
            $('#nm_unit_register_cp').val(null);
            $('#pilih_skpd_register_cp').hide();
            $('#pilih_unit_register_cp').show();
        });

        $('#kd_skpd_register_cp').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd_register_cp').val(nama);
        });

        $('#kd_unit_register_cp').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_unit_register_cp').val(nama);
        });

        $('#register_cp').on('click', function() {
            $('#modal_register_cp').modal('show');
        });

        $('.cetak_register_cp').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_register_cp')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_register_cp').checked;
            let unit = document.getElementById('pilihan_unit_register_cp').checked;
            let rekap = document.getElementById('pilihan_rekap_register_cp').checked;

            if (rekap == false) {
                if (keseluruhan == false) {
                    if (skpd == false) {
                        if (unit == false) {
                            alert('Silahkan Pilih Keseluruhan, SKPD, Rekap per SKPD atau Unit!');
                            return;
                        }
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_register_cp').value;
            let kd_unit = document.getElementById('kd_unit_register_cp').value;
            let tgl1 = document.getElementById('tgl1_register_cp').value;
            let tgl2 = document.getElementById('tgl2_register_cp').value;
            let ttd = document.getElementById('ttd_register_cp').value;
            let spasi = document.getElementById('spasi_register_cp').value;
            let jenis_print = $(this).data("jenis");

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }
            if (unit) {
                if (!kd_unit) {
                    alert('Silahkan Pilih Unit!');
                    return;
                }
            }
            let pilihan = '';
            if (rekap) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            } else if (unit) {
                pilihan = '3';
            } else if (keseluruhan) {
                pilihan = '4';
            }

            if (!tgl1 || !tgl2) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }
            if (!ttd) {
                alert("Penandatangan tidak boleh kosong!");
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.register_cp') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("tgl1", tgl1);
            searchParams.append("tgl2", tgl2);
            searchParams.append("ttd", ttd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("kd_unit", kd_unit);
            searchParams.append("spasi", spasi);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.rinci_register_cp').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_register_cp')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_register_cp').checked;
            let unit = document.getElementById('pilihan_unit_register_cp').checked;
            let rekap = document.getElementById('pilihan_rekap_register_cp').checked;

            if (rekap == false) {
                if (keseluruhan == false) {
                    if (skpd == false) {
                        if (unit == false) {
                            alert('Silahkan Pilih Keseluruhan, SKPD, Rekap per SKPD atau Unit!');
                            return;
                        }
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_register_cp').value;
            let kd_unit = document.getElementById('kd_unit_register_cp').value;
            let tgl1 = document.getElementById('tgl1_register_cp').value;
            let tgl2 = document.getElementById('tgl2_register_cp').value;
            let spasi = document.getElementById('spasi_register_cp').value;
            let ttd = document.getElementById('ttd_register_cp').value;
            let jenis_print = $(this).data("jenis");

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }
            if (unit) {
                if (!kd_unit) {
                    alert('Silahkan Pilih Unit!');
                    return;
                }
            }
            let pilihan = '';
            if (rekap) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            } else if (unit) {
                pilihan = '3';
            } else if (keseluruhan) {
                pilihan = '4';
            }

            if (!tgl1 || !tgl2) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }
            if (!ttd) {
                alert("Penandatangan tidak boleh kosong!");
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.register_cp_rinci') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("tgl1", tgl1);
            searchParams.append("tgl2", tgl2);
            searchParams.append("ttd", ttd);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("kd_unit", kd_unit);
            searchParams.append("spasi", spasi);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN REGISTER CP

        // CETAKAN DAFTAR POTONGAN PAJAK
        $('#pilih_skpd_potongan_pajak').hide();
        $('#pilih_unit_potongan_pajak').hide();

        $('#pilihan_keseluruhan_potongan_pajak').on('click', function() {
            $('#kd_skpd_potongan_pajak').val(null).change();
            $('#nm_skpd_potongan_pajak').val(null);
            $('#kd_unit_potongan_pajak').val(null).change();
            $('#nm_unit_potongan_pajak').val(null);
            $('#pilih_unit_potongan_pajak').hide();
            $('#pilih_skpd_potongan_pajak').hide();
        });

        $('#pilihan_rekap_potongan_pajak').on('click', function() {
            $('#kd_skpd_potongan_pajak').val(null).change();
            $('#nm_skpd_potongan_pajak').val(null);
            $('#kd_unit_potongan_pajak').val(null).change();
            $('#nm_unit_potongan_pajak').val(null);
            $('#pilih_unit_potongan_pajak').hide();
            $('#pilih_skpd_potongan_pajak').hide();
        });

        $('#pilihan_skpd_potongan_pajak').on('click', function() {
            $('#kd_skpd_potongan_pajak').val(null).change();
            $('#nm_skpd_potongan_pajak').val(null);
            $('#pilih_unit_potongan_pajak').hide();
            $('#pilih_skpd_potongan_pajak').show();
        });

        $('#pilihan_unit_potongan_pajak').on('click', function() {
            $('#kd_unit_potongan_pajak').val(null).change();
            $('#nm_unit_potongan_pajak').val(null);
            $('#pilih_skpd_potongan_pajak').hide();
            $('#pilih_unit_potongan_pajak').show();
        });

        $('#kd_skpd_potongan_pajak').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd_potongan_pajak').val(nama);
        });

        $('#kd_unit_potongan_pajak').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_unit_potongan_pajak').val(nama);
        });

        $('#potongan_pajak2').on('click', function() {
            $('#modal_potongan_pajak2').modal('hide');
        });

        $('#potongan_pajak').on('click', function() {
            $('#modal_potongan_pajak').modal('show');
        });

        $('.cetak_potongan_pajak2').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_potongan_pajak')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_potongan_pajak').checked;
            let unit = document.getElementById('pilihan_unit_potongan_pajak').checked;
            let rekap = document.getElementById('pilihan_rekap_potongan_pajak').checked;

            if (rekap == false) {
                if (keseluruhan == false) {
                    if (skpd == false) {
                        if (unit == false) {
                            alert('Silahkan Pilih Keseluruhan, SKPD, Rekap per SKPD atau Unit!');
                            return;
                        }
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_potongan_pajak').value;
            let kd_unit = document.getElementById('kd_unit_potongan_pajak').value;
            let tgl1 = document.getElementById('tgl1_potongan_pajak').value;
            let tgl2 = document.getElementById('tgl2_potongan_pajak').value;
            let ttd = document.getElementById('ttd_potongan_pajak').value;
            let sp2d = document.getElementById('sp2d_potongan_pajak').value;
            let belanja = document.getElementById('belanja_potongan_pajak').value;
            let jenis_print = $(this).data("jenis");

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }
            if (unit) {
                if (!kd_unit) {
                    alert('Silahkan Pilih Unit!');
                    return;
                }
            }
            let pilihan = '';
            if (rekap) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            } else if (unit) {
                pilihan = '3';
            } else if (keseluruhan) {
                pilihan = '4';
            }

            if (!tgl1 || !tgl2) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }
            if (!ttd) {
                alert("Penandatangan tidak boleh kosong!");
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.potongan_pajak2') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("tgl1", tgl1);
            searchParams.append("tgl2", tgl2);
            searchParams.append("ttd", ttd);
            searchParams.append("sp2d", sp2d);
            searchParams.append("belanja", belanja);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("kd_unit", kd_unit);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN DAFTAR POTONGAN PAJAK

        $('.cetak_potongan_pajak').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_potongan_pajak')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_potongan_pajak').checked;
            let unit = document.getElementById('pilihan_unit_potongan_pajak').checked;
            let rekap = document.getElementById('pilihan_rekap_potongan_pajak').checked;

            if (rekap == false) {
                if (keseluruhan == false) {
                    if (skpd == false) {
                        if (unit == false) {
                            alert('Silahkan Pilih Keseluruhan, SKPD, Rekap per SKPD atau Unit!');
                            return;
                        }
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_potongan_pajak').value;
            let kd_unit = document.getElementById('kd_unit_potongan_pajak').value;
            let tgl1 = document.getElementById('tgl1_potongan_pajak').value;
            let tgl2 = document.getElementById('tgl2_potongan_pajak').value;
            let ttd = document.getElementById('ttd_potongan_pajak').value;
            let sp2d = document.getElementById('sp2d_potongan_pajak').value;
            let spasi = document.getElementById('spasi_potongan_pajak').value;
            let belanja = document.getElementById('belanja_potongan_pajak').value;
            let jenis_print = $(this).data("jenis");

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }
            if (unit) {
                if (!kd_unit) {
                    alert('Silahkan Pilih Unit!');
                    return;
                }
            }
            let pilihan = '';
            if (rekap) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            } else if (unit) {
                pilihan = '3';
            } else if (keseluruhan) {
                pilihan = '4';
            }

            if (!tgl1 || !tgl2) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }
            if (!ttd) {
                alert("Penandatangan tidak boleh kosong!");
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.potongan_pajak') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("tgl1", tgl1);
            searchParams.append("tgl2", tgl2);
            searchParams.append("ttd", ttd);
            searchParams.append("sp2d", sp2d);
            searchParams.append("belanja", belanja);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("kd_unit", kd_unit);
            searchParams.append("spasi", spasi);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        // CETAKAN DAFTAR PENGELUARAN
        $('#pilih_skpd_daftar_pengeluaran').hide();
        $('#pilih_unit_daftar_pengeluaran').hide();

        $('#pilihan_semua_daftar_pengeluaran').on('click', function() {
            $('#kd_skpd_daftar_pengeluaran').val(null).change();
            $('#nm_skpd_daftar_pengeluaran').val(null);
            $('#kd_unit_daftar_pengeluaran').val(null).change();
            $('#nm_unit_daftar_pengeluaran').val(null);
            $('#pilih_unit_daftar_pengeluaran').hide();
            $('#pilih_skpd_daftar_pengeluaran').hide();
        });

        $('#pilihan_periode_daftar_pengeluaran').on('click', function() {
            $('#kd_skpd_daftar_pengeluaran').val(null).change();
            $('#nm_skpd_daftar_pengeluaran').val(null);
            $('#kd_unit_daftar_pengeluaran').val(null).change();
            $('#nm_unit_daftar_pengeluaran').val(null);
            $('#pilih_unit_daftar_pengeluaran').hide();
            $('#pilih_skpd_daftar_pengeluaran').hide();
        });

        $('#pilihan_skpd_daftar_pengeluaran').on('click', function() {
            $('#kd_skpd_daftar_pengeluaran').val(null).change();
            $('#nm_skpd_daftar_pengeluaran').val(null);
            $('#pilih_unit_daftar_pengeluaran').hide();
            $('#pilih_skpd_daftar_pengeluaran').show();
        });

        $('#pilihan_unit_daftar_pengeluaran').on('click', function() {
            $('#kd_unit_daftar_pengeluaran').val(null).change();
            $('#nm_unit_daftar_pengeluaran').val(null);
            $('#pilih_skpd_daftar_pengeluaran').hide();
            $('#pilih_unit_daftar_pengeluaran').show();
        });

        $('#kd_skpd_daftar_pengeluaran').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd_daftar_pengeluaran').val(nama);
        });

        $('#kd_unit_daftar_pengeluaran').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_unit_daftar_pengeluaran').val(nama);
        });

        $('#daftar_pengeluaran').on('click', function() {
            $('#modal_daftar_pengeluaran').modal('show');
        });

        $('.cetak_daftar_pengeluaran').on('click', function() {
            let periode = document.getElementById('pilihan_periode_daftar_pengeluaran').checked;
            let skpd = document.getElementById('pilihan_skpd_daftar_pengeluaran').checked;
            let unit = document.getElementById('pilihan_unit_daftar_pengeluaran').checked;
            let semua = document.getElementById('pilihan_semua_daftar_pengeluaran').checked;

            if (semua == false) {
                if (periode == false) {
                    if (skpd == false) {
                        if (unit == false) {
                            alert('Silahkan Pilih Semua, SKPD, Per Periode atau Unit!');
                            return;
                        }
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_daftar_pengeluaran').value;
            let kd_unit = document.getElementById('kd_unit_daftar_pengeluaran').value;
            let ttd = document.getElementById('ttd_daftar_pengeluaran').value;
            let tgl = document.getElementById('tgl_daftar_pengeluaran').value;
            let beban = document.getElementById('beban_daftar_pengeluaran').value;
            let bulan = document.getElementById('bulan_daftar_pengeluaran').value;
            let spasi = document.getElementById('spasi_daftar_pengeluaran').value;
            let jenis_print = $(this).data("jenis");

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }
            if (unit) {
                if (!kd_unit) {
                    alert('Silahkan Pilih Unit!');
                    return;
                }
            }
            let pilihan = '';
            if (semua) {
                pilihan = '1';
            } else if (skpd) {
                pilihan = '2';
            } else if (unit) {
                pilihan = '3';
            } else if (periode) {
                pilihan = '4';
            }

            if (!tgl) {
                alert('Tanggal tidak boleh kosong!');
                return;
            }
            if (!ttd) {
                alert("Penandatangan tidak boleh kosong!");
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.daftar_pengeluaran') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("tgl", tgl);
            searchParams.append("ttd", ttd);
            searchParams.append("beban", beban);
            searchParams.append("bulan", bulan);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("kd_unit", kd_unit);
            searchParams.append("spasi", spasi);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN DAFTAR PENGELUARAN

        // CETAKAN DAFTAR PENERIMAAN
        $('#pilih_tgl_daftar_penerimaan').hide();
        $('#pilih_periode_daftar_penerimaan').hide();

        $('#pilihan_tgl_daftar_penerimaan').on('click', function() {
            $('#pilih_periode_daftar_penerimaan').hide();
            $('#pilih_tgl_daftar_penerimaan').show();
        });

        $('#pilihan_periode_daftar_penerimaan').on('click', function() {
            $('#pilih_tgl_daftar_penerimaan').hide();
            $('#pilih_periode_daftar_penerimaan').show();
        });

        $('#daftar_penerimaan').on('click', function() {
            $('#modal_daftar_penerimaan').modal('show');
        });

        $('.cetak_daftar_penerimaan').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_daftar_penerimaan').checked;
            let pilih_periode = document.getElementById('pilihan_periode_daftar_penerimaan').checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl1 = document.getElementById('tgl1_daftar_penerimaan').value;
            let tgl2 = document.getElementById('tgl2_daftar_penerimaan').value;
            let periode1 = document.getElementById('periode1_daftar_penerimaan').value;
            let periode2 = document.getElementById('periode2_daftar_penerimaan').value;
            let halaman = document.getElementById('halaman_daftar_penerimaan').value;
            let spasi = document.getElementById('spasi_daftar_penerimaan').value;
            let ttd = document.getElementById('ttd_daftar_penerimaan').value;
            let pengirim = document.getElementById('pengirim_daftar_penerimaan').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl1 || !tgl2) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.daftar_penerimaan') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl1", tgl1);
            searchParams.append("tgl2", tgl2);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("pengirim", pengirim);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN DAFTAR PENERIMAAN

        // CETAKAN PENERIMAAN NON PENDAPATAN
        $('#pilih_tgl_penerimaan_non_pendapatan').hide();
        $('#pilih_periode_penerimaan_non_pendapatan').hide();

        $('#pilihan_tgl_penerimaan_non_pendapatan').on('click', function() {
            $('#pilih_periode_penerimaan_non_pendapatan').hide();
            $('#pilih_tgl_penerimaan_non_pendapatan').show();
        });

        $('#pilihan_periode_penerimaan_non_pendapatan').on('click', function() {
            $('#pilih_tgl_penerimaan_non_pendapatan').hide();
            $('#pilih_periode_penerimaan_non_pendapatan').show();
        });

        $('#penerimaan_non_pendapatan').on('click', function() {
            $('#modal_penerimaan_non_pendapatan').modal('show');
        });

        $('.cetak_penerimaan_non_pendapatan').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_penerimaan_non_pendapatan').checked;
            let pilih_periode = document.getElementById('pilihan_periode_penerimaan_non_pendapatan')
                .checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_penerimaan_non_pendapatan').value;
            let periode1 = document.getElementById('periode1_penerimaan_non_pendapatan').value;
            let periode2 = document.getElementById('periode2_penerimaan_non_pendapatan').value;
            let halaman = document.getElementById('halaman_penerimaan_non_pendapatan').value;
            let spasi = document.getElementById('spasi_penerimaan_non_pendapatan').value;
            let ttd = document.getElementById('ttd_penerimaan_non_pendapatan').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.penerimaan_non_pendapatan') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN PENERIMAAN NON PENDAPATAN

        // CETAKAN TRANSFER DANA
        $('#transfer_dana').on('click', function() {
            $('#modal_transfer_dana').modal('show');
        });

        $('.cetak_transfer_dana').on('click', function() {
            let tgl = document.getElementById('tgl_transfer_dana').value;
            let ttd = document.getElementById('ttd_transfer_dana').value;
            let bulan = document.getElementById('bulan_transfer_dana').value;
            let jenis_print = $(this).data("jenis");

            if (!tgl) {
                alert("Silahkan Pilih Tanggal!");
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.transfer_dana') }}");
            let searchParams = url.searchParams;
            searchParams.append("tgl", tgl);
            searchParams.append("ttd", ttd);
            searchParams.append("bulan", bulan);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        //CETAKAN TRANSFER DANA

        // CETAKAN RESTITUSI
        $('#pilih_tgl_restitusi').hide();
        $('#pilih_periode_restitusi').hide();

        $('#pilihan_tgl_restitusi').on('click', function() {
            $('#pilih_periode_restitusi').hide();
            $('#pilih_tgl_restitusi').show();
        });

        $('#pilihan_periode_restitusi').on('click', function() {
            $('#pilih_tgl_restitusi').hide();
            $('#pilih_periode_restitusi').show();
        });

        $('#restitusi').on('click', function() {
            $('#modal_restitusi').modal('show');
        });

        $('.cetak_restitusi').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_restitusi').checked;
            let pilih_periode = document.getElementById('pilihan_periode_restitusi')
                .checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_restitusi').value;
            let periode1 = document.getElementById('periode1_restitusi').value;
            let periode2 = document.getElementById('periode2_restitusi').value;
            let halaman = document.getElementById('halaman_restitusi').value;
            let spasi = document.getElementById('spasi_restitusi').value;
            let ttd = document.getElementById('ttd_restitusi').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.restitusi') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN RESTITUSI

        // CETAKAN RTH
        $('#pilih_tgl_rth').hide();
        $('#pilih_periode_rth').hide();

        $('#pilihan_tgl_rth').on('click', function() {
            $('#pilih_periode_rth').hide();
            $('#pilih_tgl_rth').show();
        });

        $('#pilihan_periode_rth').on('click', function() {
            $('#pilih_tgl_rth').hide();
            $('#pilih_periode_rth').show();
        });

        $('#rth').on('click', function() {
            $('#modal_rth').modal('show');
        });

        $('.cetak_rth').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_rth').checked;
            let pilih_periode = document.getElementById('pilihan_periode_rth')
                .checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let bulan = document.getElementById('bulan_rth').value;
            let periode1 = document.getElementById('periode1_rth').value;
            let periode2 = document.getElementById('periode2_rth').value;
            let tgl = document.getElementById('tgl_rth').value;
            let ttd = document.getElementById('ttd_rth').value;
            let spasi = document.getElementById('spasi_rth').value;
            let format = document.getElementById('format_rth').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!bulan) {
                    alert('Silahkan Pilih Bulan!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            if (!tgl) {
                alert('Tanggal TTD tidak boleh kosong!');
                return;
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.rth') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("bulan", bulan);
            searchParams.append("ttd", ttd);
            searchParams.append("spasi", spasi);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN RTH

        // CETAKAN BUKU PEMBANTU PENGELUARAN NON SP2D
        $('#pilih_tgl_pengeluaran_non_sp2d').hide();
        $('#pilih_periode_pengeluaran_non_sp2d').hide();

        $('#pilihan_tgl_pengeluaran_non_sp2d').on('click', function() {
            $('#pilih_periode_pengeluaran_non_sp2d').hide();
            $('#pilih_tgl_pengeluaran_non_sp2d').show();
        });

        $('#pilihan_periode_pengeluaran_non_sp2d').on('click', function() {
            $('#pilih_tgl_pengeluaran_non_sp2d').hide();
            $('#pilih_periode_pengeluaran_non_sp2d').show();
        });

        $('#pengeluaran_non_sp2d').on('click', function() {
            $('#modal_pengeluaran_non_sp2d').modal('show');
        });

        $('.cetak_pengeluaran_non_sp2d').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_pengeluaran_non_sp2d').checked;
            let pilih_periode = document.getElementById('pilihan_periode_pengeluaran_non_sp2d')
                .checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_pengeluaran_non_sp2d').value;
            let periode1 = document.getElementById('periode1_pengeluaran_non_sp2d').value;
            let periode2 = document.getElementById('periode2_pengeluaran_non_sp2d').value;
            let halaman = document.getElementById('halaman_pengeluaran_non_sp2d').value;
            let spasi = document.getElementById('spasi_pengeluaran_non_sp2d').value;
            let ttd = document.getElementById('ttd_pengeluaran_non_sp2d').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.pengeluaran_non_sp2d') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN BUKU PEMBANTU PENGELUARAN NON SP2D

        // CETAKAN RTH
        $('#pilih_tgl_dth').hide();
        $('#pilih_periode_dth').hide();

        $('#pilihan_tgl_dth').on('click', function() {
            $('#pilih_periode_dth').hide();
            $('#pilih_tgl_dth').show();
        });

        $('#pilihan_periode_dth').on('click', function() {
            $('#pilih_tgl_dth').hide();
            $('#pilih_periode_dth').show();
        });

        $('#dth').on('click', function() {
            $('#modal_dth').modal('show');
        });

        $('#skpd_dth').on('select2:select', function() {
            let kd_skpd = this.value;
            cari_bendahara(kd_skpd);
            cari_pakpa(kd_skpd);
        });

        function cari_bendahara(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara_penerimaan.bendahara') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#bendahara').empty();
                    $('#bendahara').append(
                        `<option value="" disabled selected>Pilih Bendahara Penerimaan</option>`
                    );
                    $.each(data, function(index, data) {
                        $('#bendahara').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }

        function cari_pakpa(kd_skpd) {
            $.ajax({
                url: "{{ route('skpd.laporan_bendahara_penerimaan.pakpa') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd
                },
                success: function(data) {
                    $('#pa_kpa').empty();
                    $('#pa_kpa').append(
                        `<option value="" disabled selected>Pilih PA/KPA</option>`);
                    $.each(data, function(index, data) {
                        $('#pa_kpa').append(
                            `<option value="${data.nip}" data-nama="${data.nama}">${data.nip} | ${data.nama}</option>`
                        );
                    })
                }
            })
        }

        $('.cetak_dth').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_dth').checked;
            let pilih_periode = document.getElementById('pilihan_periode_dth')
                .checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let skpd = document.getElementById('skpd_dth').value;
            let bulan = document.getElementById('bulan_dth').value;
            let periode1 = document.getElementById('periode1_dth').value;
            let periode2 = document.getElementById('periode2_dth').value;
            let tgl = document.getElementById('tgl_dth').value;
            let bendahara = document.getElementById('bendahara').value;
            let spasi = document.getElementById('spasi_dth').value;
            let pa_kpa = document.getElementById('pa_kpa').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!bulan) {
                    alert('Silahkan Pilih Bulan!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            if (!tgl) {
                alert('Tanggal TTD tidak boleh kosong!');
                return;
            }

            if (!skpd) {
                alert('SKPD tidak boleh kosong!');
                return;
            }

            if (!bendahara) {
                alert('Bendahara tidak boleh kosong!');
                return;
            }

            if (!pa_kpa) {
                alert('Pengguna Anggaran TTD tidak boleh kosong!');
                return;
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.dth') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("skpd", skpd);
            searchParams.append("bulan", bulan);
            searchParams.append("bendahara", bendahara);
            searchParams.append("pa_kpa", pa_kpa);
            searchParams.append("spasi", spasi);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN RTH

        // CETAKAN REGISTER KOREKSI PENERIMAAN
        $('#pilih_tgl_koreksi_penerimaan').hide();
        $('#pilih_periode_koreksi_penerimaan').hide();

        $('#pilihan_tgl_koreksi_penerimaan').on('click', function() {
            $('#pilih_periode_koreksi_penerimaan').hide();
            $('#pilih_tgl_koreksi_penerimaan').show();
        });

        $('#pilihan_periode_koreksi_penerimaan').on('click', function() {
            $('#pilih_tgl_koreksi_penerimaan').hide();
            $('#pilih_periode_koreksi_penerimaan').show();
        });

        $('#koreksi_penerimaan').on('click', function() {
            $('#modal_koreksi_penerimaan').modal('show');
        });

        $('.cetak_koreksi_penerimaan').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_koreksi_penerimaan').checked;
            let pilih_periode = document.getElementById('pilihan_periode_koreksi_penerimaan')
                .checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_koreksi_penerimaan').value;
            let periode1 = document.getElementById('periode1_koreksi_penerimaan').value;
            let periode2 = document.getElementById('periode2_koreksi_penerimaan').value;
            let halaman = document.getElementById('halaman_koreksi_penerimaan').value;
            let spasi = document.getElementById('spasi_koreksi_penerimaan').value;
            let ttd = document.getElementById('ttd_koreksi_penerimaan').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.koreksi_penerimaan') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN REGISTER KOREKSI PENERIMAAN

        // CETAKAN KAS HARIAN KASDA
        $('#harian_kasda').on('click', function() {
            $('#modal_harian_kasda').modal('show');
        });

        $('.cetak_harian_kasda').on('click', function() {
            let tgl = document.getElementById('tgl_harian_kasda').value;
            let halaman = document.getElementById('halaman_harian_kasda').value;
            let spasi = document.getElementById('spasi_harian_kasda').value;
            let ttd = document.getElementById('ttd_harian_kasda').value;
            let jenis_print = $(this).data("jenis");

            if (!tgl) {
                alert("Silahkan Pilih Tanggal!");
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.harian_kasda') }}");
            let searchParams = url.searchParams;
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        //CETAKAN KAS HARIAN KASDA

        // CETAKAN UYHD
        $('#pilih_tgl_uyhd').hide();
        $('#pilih_periode_uyhd').hide();

        $('#pilihan_tgl_uyhd').on('click', function() {
            $('#pilih_periode_uyhd').hide();
            $('#pilih_tgl_uyhd').show();
        });

        $('#pilihan_periode_uyhd').on('click', function() {
            $('#pilih_tgl_uyhd').hide();
            $('#pilih_periode_uyhd').show();
        });

        $('#uyhd').on('click', function() {
            $('#modal_uyhd').modal('show');
        });

        $('.cetak_uyhd').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_uyhd').checked;
            let pilih_periode = document.getElementById('pilihan_periode_uyhd')
                .checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_uyhd').value;
            let periode1 = document.getElementById('periode1_uyhd').value;
            let periode2 = document.getElementById('periode2_uyhd').value;
            let halaman = document.getElementById('halaman_uyhd').value;
            let spasi = document.getElementById('spasi_uyhd').value;
            let ttd = document.getElementById('ttd_uyhd').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.uyhd') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN UYHD

        // CETAKAN KOREKSI PENGELUARAN
        $('#pilih_tgl_koreksi_pengeluaran').hide();
        $('#pilih_periode_koreksi_pengeluaran').hide();

        $('#pilihan_tgl_koreksi_pengeluaran').on('click', function() {
            $('#pilih_periode_koreksi_pengeluaran').hide();
            $('#pilih_tgl_koreksi_pengeluaran').show();
        });

        $('#pilihan_periode_koreksi_pengeluaran').on('click', function() {
            $('#pilih_tgl_koreksi_pengeluaran').hide();
            $('#pilih_periode_koreksi_pengeluaran').show();
        });

        $('#koreksi_pengeluaran').on('click', function() {
            $('#modal_koreksi_pengeluaran').modal('show');
        });

        $('.cetak_koreksi_pengeluaran').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_koreksi_pengeluaran').checked;
            let pilih_periode = document.getElementById('pilihan_periode_koreksi_pengeluaran')
                .checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_koreksi_pengeluaran').value;
            let periode1 = document.getElementById('periode1_koreksi_pengeluaran').value;
            let periode2 = document.getElementById('periode2_koreksi_pengeluaran').value;
            let halaman = document.getElementById('halaman_koreksi_pengeluaran').value;
            let spasi = document.getElementById('spasi_koreksi_pengeluaran').value;
            let ttd = document.getElementById('ttd_koreksi_pengeluaran').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.koreksi_pengeluaran') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN KOREKSI PENGELUARAN

        // CETAKAN KOREKSI PENERIMAAN
        $('#pilih_tgl_koreksi_penerimaan2').hide();
        $('#pilih_periode_koreksi_penerimaan2').hide();

        $('#pilihan_tgl_koreksi_penerimaan2').on('click', function() {
            $('#pilih_periode_koreksi_penerimaan2').hide();
            $('#pilih_tgl_koreksi_penerimaan2').show();
        });

        $('#pilihan_periode_koreksi_penerimaan2').on('click', function() {
            $('#pilih_tgl_koreksi_penerimaan2').hide();
            $('#pilih_periode_koreksi_penerimaan2').show();
        });

        $('#koreksi_penerimaan2').on('click', function() {
            $('#modal_koreksi_penerimaan2').modal('show');
        });

        $('.cetak_koreksi_penerimaan2').on('click', function() {
            let pilih_tgl = document.getElementById('pilihan_tgl_koreksi_penerimaan2').checked;
            let pilih_periode = document.getElementById('pilihan_periode_koreksi_penerimaan2')
                .checked;

            if (pilih_tgl == false) {
                if (pilih_periode == false) {
                    alert('Silahkan Pilih Tanggal atau Periode!');
                    return;
                }
            }

            let tgl = document.getElementById('tgl_koreksi_penerimaan2').value;
            let periode1 = document.getElementById('periode1_koreksi_penerimaan2').value;
            let periode2 = document.getElementById('periode2_koreksi_penerimaan2').value;
            let halaman = document.getElementById('halaman_koreksi_penerimaan2').value;
            let spasi = document.getElementById('spasi_koreksi_penerimaan2').value;
            let ttd = document.getElementById('ttd_koreksi_penerimaan2').value;
            let jenis_print = $(this).data("jenis");

            if (pilih_tgl) {
                if (!tgl) {
                    alert('Silahkan Pilih Tanggal!');
                    return;
                }
            }
            if (pilih_periode) {
                if (!periode1 || !periode2) {
                    alert('Silahkan Pilih Periode!');
                    return;
                }
            }

            let pilihan = '';
            if (pilih_tgl) {
                pilihan = '1';
            } else if (pilih_periode) {
                pilihan = '2';
            }

            let url = new URL("{{ route('laporan_bendahara_umum.koreksi_penerimaan2') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("tgl", tgl);
            searchParams.append("halaman", halaman);
            searchParams.append("spasi", spasi);
            searchParams.append("ttd", ttd);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN KOREKSI PENERIMAAN

        // CETAKAN REGISTER SP2D
        $('#pilih_skpd_register_sp2d').hide();

        $('#pilihan_keseluruhan_register_sp2d').on('click', function() {
            $('#kd_skpd_register_sp2d').val(null).change();
            $('#nm_skpd_register_sp2d').val(null);

            $('#pilih_skpd_register_sp2d').hide();
        });

        $('#pilihan_skpd_register_sp2d').on('click', function() {
            $('#kd_skpd_register_sp2d').val(null).change();
            $('#nm_skpd_register_sp2d').val(null);

            $('#pilih_skpd_register_sp2d').show();
        });

        $('#kd_skpd_register_sp2d').on('select2:select', function() {
            let nama = $(this).find(':selected').data('nama');
            $('#nm_skpd_register_sp2d').val(nama);
        });

        $('#register_sp2d').on('click', function() {
            $('#modal_register_sp2d').modal('show');
        });

        $('#cek_data_akuntansi').on('click', function() {
            $('#modal_cek_data_akuntansi').modal('show');
        });

        $('.cetak_register_sp2d').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_register_sp2d')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_register_sp2d').checked;

            let keseluruhan1 = document.getElementById('pilihan_keseluruhan_register_sp2d1')
                .checked;
            let bulan = document.getElementById('pilihan_bulan_register_sp2d')
                .checked;
            let periode = document.getElementById('pilihan_periode_register_sp2d')
                .checked;

            if (keseluruhan == false) {
                if (skpd == false) {
                    alert('Silahkan Pilih Keseluruhan atau SKPD!');
                    return;
                }
            }

            if (keseluruhan) {
                if (keseluruhan1 == false) {
                    if (bulan == false) {
                        if (periode == false) {
                            alert('Silahkan Pilih Keseluruhan, Bulan atau Periode!');
                            return;
                        }
                    }
                }
            }

            if (skpd) {
                if (keseluruhan1 == false) {
                    if (bulan == false) {
                        if (periode == false) {
                            alert('Silahkan Pilih Keseluruhan, Bulan atau Periode!');
                            return;
                        }
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_register_sp2d').value;
            let bulan1 = document.getElementById('bulan_register_sp2d').value;
            let periode1 = document.getElementById('periode1_register_sp2d').value;
            let periode2 = document.getElementById('periode2_register_sp2d').value;
            let ttd = document.getElementById('ttd_register_sp2d').value;
            let anggaran = document.getElementById('anggaran_register_sp2d').value;
            let urutan = document.getElementById('urutan_register_sp2d').value;
            let status = document.getElementById('status_register_sp2d').value;
            let kasda = document.getElementById('kasda_register_sp2d').checked;
            let dengan = document.getElementById('dengan_register_sp2d').checked;
            let tanpa = document.getElementById('tanpa_register_sp2d').checked;
            let jenis_print = $(this).data("jenis");

            if (keseluruhan || skpd) {
                if (bulan) {
                    if (!bulan1) {
                        alert('Silahkan Pilih Bulan!');
                        return;
                    }
                }
            }

            if (keseluruhan || skpd) {
                if (periode) {
                    if (!periode1 || !periode2) {
                        alert('Silahkan Pilih Periode!');
                        return;
                    }
                }
            }

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }

            let pilihan = '';
            if (keseluruhan) {
                if (keseluruhan1) {
                    pilihan = '11';
                }
                if (bulan) {
                    pilihan = '12';
                }
                if (periode) {
                    pilihan = '13';
                }
            }
            if (skpd) {
                if (keseluruhan1) {
                    pilihan = '21';
                }
                if (bulan) {
                    pilihan = '22';
                }
                if (periode) {
                    pilihan = '23';
                }
            }

            if (!ttd) {
                alert('Silahkan Pilih Penandatangan!');
                return;
            }
            if (!anggaran) {
                alert('Silahkan Pilih Anggaran!');
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.register_sp2d') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("bulan", bulan1);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("ttd", ttd);
            searchParams.append("status", status);
            searchParams.append("anggaran", anggaran);
            searchParams.append("urutan", urutan);
            searchParams.append("kasda", kasda);
            searchParams.append("dengan", dengan);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.cetak_realisasi_sp2d').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_register_sp2d')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_register_sp2d').checked;

            let keseluruhan1 = document.getElementById('pilihan_keseluruhan_register_sp2d1')
                .checked;
            let bulan = document.getElementById('pilihan_bulan_register_sp2d')
                .checked;
            let periode = document.getElementById('pilihan_periode_register_sp2d')
                .checked;

            if (keseluruhan == false) {
                if (skpd == false) {
                    alert('Silahkan Pilih Keseluruhan atau SKPD!');
                    return;
                }
            }

            if (keseluruhan) {
                if (keseluruhan1 == false) {
                    if (bulan == false) {
                        if (periode == false) {
                            alert('Silahkan Pilih Keseluruhan, Bulan atau Periode!');
                            return;
                        }
                    }
                }
            }

            if (skpd) {
                if (keseluruhan1 == false) {
                    if (bulan == false) {
                        if (periode == false) {
                            alert('Silahkan Pilih Keseluruhan, Bulan atau Periode!');
                            return;
                        }
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_register_sp2d').value;
            let bulan1 = document.getElementById('bulan_register_sp2d').value;
            let periode1 = document.getElementById('periode1_register_sp2d').value;
            let periode2 = document.getElementById('periode2_register_sp2d').value;
            let ttd = document.getElementById('ttd_register_sp2d').value;
            let anggaran = document.getElementById('anggaran_register_sp2d').value;
            let urutan = document.getElementById('urutan_register_sp2d').value;
            let status = document.getElementById('status_register_sp2d').value;
            let kasda = document.getElementById('kasda_register_sp2d').checked;
            let dengan = document.getElementById('dengan_register_sp2d').checked;
            let tanpa = document.getElementById('tanpa_register_sp2d').checked;
            let jenis_print = $(this).data("jenis");

            if (keseluruhan || skpd) {
                if (bulan) {
                    if (!bulan1) {
                        alert('Silahkan Pilih Bulan!');
                        return;
                    }
                }
            }

            if (keseluruhan || skpd) {
                if (periode) {
                    if (!periode1 || !periode2) {
                        alert('Silahkan Pilih Periode!');
                        return;
                    }
                }
            }

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }

            let pilihan = '';
            if (keseluruhan) {
                if (keseluruhan1) {
                    pilihan = '11';
                }
                if (bulan) {
                    pilihan = '12';
                }
                if (periode) {
                    pilihan = '13';
                }
            }
            if (skpd) {
                if (keseluruhan1) {
                    pilihan = '21';
                }
                if (bulan) {
                    pilihan = '22';
                }
                if (periode) {
                    pilihan = '23';
                }
            }

            if (!ttd) {
                alert('Silahkan Pilih Penandatangan!');
                return;
            }
            if (!anggaran) {
                alert('Silahkan Pilih Anggaran!');
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.realisasi_sp2d') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("bulan", bulan1);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("ttd", ttd);
            searchParams.append("status", status);
            searchParams.append("anggaran", anggaran);
            searchParams.append("urutan", urutan);
            searchParams.append("kasda", kasda);
            searchParams.append("dengan", dengan);
            searchParams.append("tanpa", tanpa);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.cetak_realisasiskpd_sp2d').on('click', function() {
            let keseluruhan = document.getElementById('pilihan_keseluruhan_register_sp2d')
                .checked;
            let skpd = document.getElementById('pilihan_skpd_register_sp2d').checked;

            let keseluruhan1 = document.getElementById('pilihan_keseluruhan_register_sp2d1')
                .checked;
            let bulan = document.getElementById('pilihan_bulan_register_sp2d')
                .checked;
            let periode = document.getElementById('pilihan_periode_register_sp2d')
                .checked;

            if (keseluruhan == false) {
                if (skpd == false) {
                    alert('Silahkan Pilih Keseluruhan atau SKPD!');
                    return;
                }
            }

            if (keseluruhan) {
                if (keseluruhan1 == false) {
                    if (bulan == false) {
                        if (periode == false) {
                            alert('Silahkan Pilih Keseluruhan, Bulan atau Periode!');
                            return;
                        }
                    }
                }
            }

            if (skpd) {
                if (keseluruhan1 == false) {
                    if (bulan == false) {
                        if (periode == false) {
                            alert('Silahkan Pilih Keseluruhan, Bulan atau Periode!');
                            return;
                        }
                    }
                }
            }

            let kd_skpd = document.getElementById('kd_skpd_register_sp2d').value;
            let bulan1 = document.getElementById('bulan_register_sp2d').value;
            let periode1 = document.getElementById('periode1_register_sp2d').value;
            let periode2 = document.getElementById('periode2_register_sp2d').value;
            let ttd = document.getElementById('ttd_register_sp2d').value;
            let anggaran = document.getElementById('anggaran_register_sp2d').value;
            let urutan = document.getElementById('urutan_register_sp2d').value;
            let status = document.getElementById('status_register_sp2d').value;
            let kasda = document.getElementById('kasda_register_sp2d').checked;
            let dengan = document.getElementById('dengan_register_sp2d').checked;
            let tanpa = document.getElementById('tanpa_register_sp2d').checked;
            let jenis_print = $(this).data("jenis");

            let margin_kiri = document.getElementById('margin_kiri').value;
            let margin_kanan = document.getElementById('margin_kanan').value;
            let margin_atas = document.getElementById('margin_atas').value;
            let margin_bawah = document.getElementById('margin_bawah').value;

            if (keseluruhan || skpd) {
                if (bulan) {
                    if (!bulan1) {
                        alert('Silahkan Pilih Bulan!');
                        return;
                    }
                }
            }

            if (keseluruhan || skpd) {
                if (periode) {
                    if (!periode1 || !periode2) {
                        alert('Silahkan Pilih Periode!');
                        return;
                    }
                }
            }

            if (skpd) {
                if (!kd_skpd) {
                    alert('Silahkan Pilih SKPD!');
                    return;
                }
            }

            let pilihan = '';
            if (keseluruhan) {
                if (keseluruhan1) {
                    pilihan = '11';
                }
                if (bulan) {
                    pilihan = '12';
                }
                if (periode) {
                    pilihan = '13';
                }
            }
            if (skpd) {
                if (keseluruhan1) {
                    pilihan = '21';
                }
                if (bulan) {
                    pilihan = '22';
                }
                if (periode) {
                    pilihan = '23';
                }
            }

            if (!ttd) {
                alert('Silahkan Pilih Penandatangan!');
                return;
            }
            if (!anggaran) {
                alert('Silahkan Pilih Anggaran!');
                return;
            }

            let url = new URL("{{ route('laporan_bendahara_umum.realisasiskpd_sp2d') }}");
            let searchParams = url.searchParams;
            searchParams.append("pilihan", pilihan);
            searchParams.append("kd_skpd", kd_skpd);
            searchParams.append("bulan", bulan1);
            searchParams.append("periode1", periode1);
            searchParams.append("periode2", periode2);
            searchParams.append("ttd", ttd);
            searchParams.append("status", status);
            searchParams.append("anggaran", anggaran);
            searchParams.append("urutan", urutan);
            searchParams.append("kasda", kasda);
            searchParams.append("dengan", dengan);
            searchParams.append("tanpa", tanpa);
            searchParams.append("margin_kiri", margin_kiri);
            searchParams.append("margin_kanan", margin_kanan);
            searchParams.append("margin_atas", margin_atas);
            searchParams.append("margin_bawah", margin_bawah);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        // CETAKAN REGISTER SP2D

        // CETAKAN CEK DATA AKUTANSI
        $('#cek_data_akuntansi').on('click', function() {
            $('#modal_cek_data_akuntansi').modal('show');
        });

        $('.cetak_cek_data_akuntansi_belanja').on('click', function() {
            let tgl = document.getElementById('tgl_cek_data_akuntansi').value;
            let ttd = document.getElementById('ttd_cek_data_akuntansi').value;
            let bulan = document.getElementById('bulan_cek_data_akuntansi').value;
            let jenis_print = $(this).data("jenis");

            if (!tgl) {
                alert("Silahkan Pilih Tanggal!");
                return;
            }


            let url = new URL("{{ route('laporan_bendahara_umum.cekdataakuntansib') }}");
            let searchParams = url.searchParams;
            searchParams.append("tgl", tgl);
            searchParams.append("ttd", ttd);
            searchParams.append("bulan", bulan);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });

        $('.cetak_cek_data_akuntansi_pendapatan').on('click', function() {
            let tgl = document.getElementById('tgl_cek_data_akuntansi').value;
            let ttd = document.getElementById('ttd_cek_data_akuntansi').value;
            let bulan = document.getElementById('bulan_cek_data_akuntansi').value;
            let jenis_print = $(this).data("jenis");

            if (!tgl) {
                alert("Silahkan Pilih Tanggal!");
                return;
            }


            let url = new URL("{{ route('laporan_bendahara_umum.cekdataakuntansip') }}");
            let searchParams = url.searchParams;
            searchParams.append("tgl", tgl);
            searchParams.append("ttd", ttd);
            searchParams.append("bulan", bulan);
            searchParams.append("jenis_print", jenis_print);
            window.open(url.toString(), "_blank");
        });
        //CETAKAN CEK DATA AKUTANSI

    });
</script>
