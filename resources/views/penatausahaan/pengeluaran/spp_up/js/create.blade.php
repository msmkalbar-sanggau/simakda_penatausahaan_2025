<script>
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

        $('#rekening').on('change', function() {
            let nama = $(this).find(':selected').data('nama');
            let npwp = $(this).find(':selected').data('npwp');
            $("#nama_penerima").val(nama);
            $("#npwp").val(npwp);
        });

        $('#tgl_spp').on('change', function() {
            let tanggal = this.value;
            let bulan = new Date(tanggal);
            let bulan1 = bulan.getMonth() + 1;
            $("#bulan").val(bulan1);
            // get_spp();
        });

        $('#kode_akun').on('change', function() {
            let nama = $(this).find(':selected').data('nama');
            $("#nama_akun").val(nama);
        });

        $('#simpan_spp').on('click', function() {
            let no_spp = document.getElementById('no_spp').value;
            let tgl_spp = document.getElementById('tgl_spp').value;
            let tgl_spp_lalu = document.getElementById('tgl_spp_lalu').value;
            let beban = document.getElementById('beban').value;
            let keperluan = document.getElementById('keperluan').value;
            let bank = document.getElementById('bank').value;
            let no_spd = document.getElementById('no_spd').value;
            let npwp = document.getElementById('npwp').value;
            let rekening = document.getElementById('rekening').value;
            let nama_penerima = document.getElementById('nama_penerima').value;
            let nm_skpd = document.getElementById('nm_skpd').value;
            let kode_akun = document.getElementById('kode_akun').value;
            let nama_akun = document.getElementById('nama_akun').value;
            let tahun_anggaran = document.getElementById('tahun_anggaran').value;
            // let no_urut = document.getElementById('no_urut').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let nilai_up = rupiah(document.getElementById('nilai_up').value);
            let bulan = document.getElementById('bulan').value;
            let tahun_input = tgl_spp.substring(0., 4);

            let statusAnggaran = document.getElementById('statusAnggaran').value;
            let bulanInputan = document.getElementById('bulanInputan').value;

            if (!statusAnggaran) return alert("Pilih status anggaran Terlebih Dahulu...!!!");
            if (!bulanInputan) return alert("Pilih bulan inputan Terlebih Dahulu...!!!");

            if (!no_spp) {
                alert('Nomor SPP wajib diisi!');
                return;
            }

            if (no_spp == 0) {
                alert('Nomor SPP tidak boleh NULL!');
                return;
            }

            if (tgl_spp < tgl_spp_lalu) {
                alert('Tanggal SPP tidak boleh kurang dari SPP Lalu...!!!');
                return;
            }
            if (!kode_akun) {
                alert('Pilih Rekening/Kode Akun Terlebih Dahulu');
                return;
            }
            if (tahun_input != tahun_anggaran) {
                alert('Tahun tidak sama dengan tahun Anggaran');
                return;
            }
            if (!no_spd) {
                alert("Nilai Nomor SPD tidak boleh Kosong");
                return;
            }
            if (!keperluan) {
                alert("Isi Keperluan Terlebih Dahulu...!!!");
                return;
            }
            if (!npwp) {
                alert("Isi NPWP Terlebih Dahulu...!!!");
                return;
            }
            if (!bank) {
                alert("Isi BANK Terlebih Dahulu...!!!");
                return;
            }
            $('#simpan_spp').prop('disabled', true);
            $.ajax({
                url: "{{ route('sppup.simpan_spp') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spp: no_spp,
                    tgl_spp: tgl_spp,
                    bulan: bulan,
                    beban: beban,
                    keperluan: keperluan,
                    bank: bank,
                    no_spd: no_spd,
                    npwp: npwp,
                    rekening: rekening,
                    nama_penerima: nama_penerima,
                    nm_skpd: nm_skpd,
                    kode_akun: kode_akun,
                    nama_akun: nama_akun,
                    nilai_up: nilai_up,
                    // no_urut: no_urut,
                    kd_skpd: kd_skpd,
                    statusAnggaran,
                    bulanInputan
                },
                success: function(data) {
                    if (data.message == '0') {
                        alert('Gagal Simpan..!!');
                        $('#simpan_spp').prop('disabled', false);
                    } else if (data.message == '2') {
                        alert('Nomor SPP Sudah Terpakai...!!!,  Ganti Nomor SPP...!!!');
                        $('#simpan_spp').prop('disabled', false);
                    } else if (data.message == '1') {
                        alert('Data berhasil ditambahkan!');
                        window.location.href = "{{ route('sppup.index') }}";
                    }
                }
            })
        });

        function simpan_up() {
            let no_spp = document.getElementById('no_spp').value;
            let kode_akun = document.getElementById('kode_akun').value;
            let nama_akun = document.getElementById('nama_akun').value;
            let nilai_up = rupiah(document.getElementById('nilai_up').value);
            let kd_skpd = document.getElementById('kd_skpd').value;

            $.ajax({
                url: "{{ route('sppup.simpan_detail_spp') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    no_spp: no_spp,
                    kode_akun: kode_akun,
                    nama_akun: nama_akun,
                    nilai_up: nilai_up,
                    kd_skpd: kd_skpd,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil ditambahkan!');
                        window.location.href = "{{ route('sppup.index') }}";
                    } else {
                        alert('Data gagal ditambahkan!');
                        $('#simpan_spp').prop('disabled', false);
                    }
                }
            })
        }

        function nilai(n) {
            let nilai = n.split(',').join('');
            return parseFloat(nilai) || 0;
        }

        function rupiah(n) {
            let n1 = n.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }

        function get_spp() {
            let jenis_beban = document.getElementById('beban').value;
            let kd_skpd = document.getElementById('kd_skpd').value;
            let bulan = document.getElementById('bulan').value;
            let tahun_anggaran = "{{ tahun_anggaran() }}";

            // no spp
            $.ajax({
                url: "{{ route('sppls.cari_nospp') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    jenis_beban: jenis_beban,
                },
                success: function(data) {
                    let no_spp = "61.03/02.0/" + data.nilai + "/" + "UP" + "/" + kd_skpd + "/M/" +
                        bulan + "/" +
                        tahun_anggaran;
                    $('#no_urut').val(data.nilai);
                    $('#no_spp').val(no_spp);
                }
            })
        }

    });
</script>
