<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CETAK SPTB</title>
</head>

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75"
                    height="100" />
            </td>
            <td align="left" style="font-size:14px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>{{ nama_skpd($kd_skpd) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <hr>

    <div style="text-align: center">
        <table style="width: 100%">
            <tr>
                <td><u><b>SURAT PERNYATAAN TANGGUNG JAWABAN BELANJA (SPTB)</b></u></td>
            </tr>
        </table>
    </div>

    <div style="text-align: left;padding-top:20px">
        <table style="width: 100%">
            <tr>
                <td>1. OPD</td>
                <td>:</td>
                <td>{{ $kd_skpd }} - {{ nama_skpd($kd_skpd) }}</td>
            </tr>
            <tr>
                <td>2. Satuan Kerja</td>
                <td>:</td>
                <td>{{ $kd_skpd }} - {{ nama_skpd($kd_skpd) }}</td>
            </tr>
            <tr>
                <td>3. Tanggal/NO. DPA</td>
                <td>:</td>
                <td>{{ tanggal($dpa->tgl_dpa) }} dan {{ $dpa->no_dpa }}</td>
            </tr>
            <tr>
                <td>4. Tahun Anggaran</td>
                <td>:</td>
                <td>{{ tahun_anggaran() }}</td>
            </tr>
            <tr>
                <td>5. Jumlah Belanja</td>
                <td>:</td>
                <td>Rp. {{ rupiah($jumlah_belanja->nilai) }}</td>
            </tr>
        </table>
    </div>

    <br>

    <table style="font-family: Open Sans; font-size:16px">
        <tr>
            <td>Yang bertanda tangan dibawah ini adalah Pengguna Anggaran Satuan Kerja {{ nama_skpd($kd_skpd) }}
                Menyatakan bahwa
                saya bertanggung jawab penuh atas segala pengeluaran yang telah dibayar lunas oleh Bendahara Pengeluaran
                kepada yang berhak menerima, sebagimana tertera dalam Laporan Pertanggung jawaban Tambah Uang
                disampaikan oleh Bendahara Pengeluaran</td>
        </tr>
        <tr>
            <td style="height: 10px"></td>
        </tr>
        <tr>
            <td>Bukti-bukti belanja tertera dalam Laporan Pertanggung Jawaban Uang Disimpan sesuai ketentuan yang
                berlaku pada Satuan Kerja {{ nama_skpd($kd_skpd) }}
                Untuk kelengkapan administrasi dan keperluan pemeriksaan aparat pengawasan Fungsional.</td>
        </tr>
        <tr>
            <td style="height: 10px"></td>
        </tr>
        <tr>
            <td>Demikian Surat Pernyataan ini dibuat dengan sebenarnya.</td>
        </tr>
    </table>

    <br>
    <br>
    <div style="padding-top:20px">
        <table class="table" style="width: 100%">
            <tr>
                <td style="width: 50%"></td>
                <td style="margin: 2px 0px;text-align: center">
                    {{ $daerah->daerah }}, {{ tanggal($tgl_ttd) }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%"></td>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $pa_kpa->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%"></td>
                <td style="text-align: center">
                    <b><u>{{ $pa_kpa->nama }}</u></b> <br>
                    {{ $pa_kpa->pangkat }} <br>
                    NIP. {{ $pa_kpa->nip }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
