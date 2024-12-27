<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        #rincian td {
            padding: 4px;
        }
    </style>
</head>

<body>
    <table style="width: 100%;text-align:center;font-size:16px;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td>PEMERINTAH KABUPATEN SANGGAU<br />
                PEJABAT PENGELOLA KEUANGAN DAERAH SELAKU BENDAHARA UMUM DAERAH<br />
                NOMOR {{ $nospd }}<br />
                TENTANG<br />
                SURAT PENYEDIAAN DANA ANGGARAN BELANJA DAERAH<br />
                TAHUN ANGGARAN {{ tahun_anggaran() }}<br /><br />
                PPKD SELAKU BUD<br /></td>
        </tr>
    </table>
    <table
        style="margin-top: 16px; padding-left: 20px;width:100%;font-size:12px;letter-spacing:normal;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tbody>
            <tr>
                <td style="vertical-align: top; width: 13%;"><b>Menimbang</b></td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top; text-align: justify;font-weight:300">Bahwa untuk
                    melaksanakan
                    Anggaran
                    {{ $jenis }} sub kegiatan Tahun Anggaran {{ tahun_anggaran() }} berdasarkan DPA-SKPD dan
                    anggaran kas yang telah ditetapkan, perlu disediakan pendanaan dengan menerbitkan Surat Penyediaan
                    Dana (SPD);</td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><b>Mengingat</b></td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top;text-align:justify">
                    <ol style="margin: 0px;">
                        @for ($tes = 1; $tes <= $total_ingat; $tes++)
                            <li>{{ ${'konfig'}->{'ingat' . $tes} }}</li>
                        @endfor
                        <li>{{ $konfig->ingat_akhir }} {{ $no_dpa->no_dpa }} Tahun Anggaran {{ tahun_anggaran() }}</li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td style="height: 5px"></td>
            </tr>
        </tbody>
    </table>
    <div>
        <br>
        <div style="text-align: center;"><strong>M E M U T U S K A N :</strong></div>
        <br>
        {{-- <div>
            <p style="text-indent: 24px;">{{ $konfig->memutuskan }}</p>
        </div> --}}
        <table style="width: 100%;;font-size:12px;font-family:'Open Sans', Helvetica,Arial,sans-serif">
            <tr>
                <td style="height: 5px"></td>
            </tr>
            <tr>
                <td style="text-align: justify;text-indent:24px">{{ $konfig->memutuskan }}</td>
            </tr>
        </table>
        <table id="rincian"
            style="font-family:'Open Sans', Helvetica,Arial,sans-serif; font-size: 12px;letter-spacing:0.5px">
            <tbody>
                <tr>
                    <td style="width: 3%;">1.</td>
                    <td style="width: 35%;">Dasar penyediaan dana:</td>
                    <td style="width: 2%;"></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>DPA-SKPD</td>
                    <td>:</td>
                    <td>{{ $no_dpa->no_dpa }}</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>Ditujukan kepada SKPD</td>
                    <td>:</td>
                    <td>{{ $data->nm_skpd }}</td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td>Kepala SKPD</td>
                    <td>:</td>
                    <td>{{ $kepala_skpd->nama }}</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>Jumlah Penyediaan Dana</td>
                    <td>:</td>
                    <td>Rp{{ number_format($total, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><i>({{ terbilang($total) }})</i></td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>Untuk Kebutuhan</td>
                    <td>:</td>
                    <td>{{ $tambahanbln }} Bulan {{ getMonths()[$data->bulan_awal] }} s/d
                        {{ getMonths()[$data->bulan_akhir] }}</td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td>Ikhtisar penyediaan dana :</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>a. Jumlah dana DPA-SKPD</td>
                    <td>:</td>
                    <td>Rp{{ number_format($total_anggaran, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><i>({{ terbilang($total_anggaran) }})</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td>b. Akumulasi SPD sebelumnya</td>
                    <td>:</td>
                    <td>Rp{{ number_format($total_spd_lalu, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><i>({{ terbilang($total_spd_lalu) }})</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td>c. Jumlah dana yang di-SPD-kan saat ini</td>
                    <td>:</td>
                    <td>Rp{{ number_format($total, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><i>({{ terbilang($total) }})</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td>d. Sisa jumlah dana DPA-SKPD yang belum di-SPD-kan</td>
                    <td>:</td>
                    <td>Rp{{ number_format($total_anggaran - $total_spd_lalu - $total, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><i>({{ terbilang($hasil) }})</i>
                    </td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td>Ketentuan-ketentuan lain</td>
                    <td>:</td>
                    <td>{{ $data->klain }}</td>
                </tr>
            </tbody>
        </table>

        <table
            style="float: right; text-align: center; padding-right: 128px; padding-top: 32px;font-family:Arial, Helvetica, sans-serif;font-size:12px">
            <tr>
                <td style="width: 50%"></td>
                <td>
                    Ditetapkan di Kabupaten Sanggau<br />
                    Pada tanggal {{ tanggal($data->tgl_spd) }}<br />
                    PPKD SELAKU BUD,<br />
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="padding-top:40px"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <u>{{ $ttd->nama }}</u><br />
                    NIP. {{ $ttd->nip }}
                </td>
            </tr>
        </table>

        <table style="font-family:'Open Sans', Helvetica,Arial,sans-serif;font-size:12px">
            <tr>
                <td>
                    Tembusan disampaikan kepada:<br />
                    1. Inspektur *)<br />
                    2. Arsip<br />
                    *) Coret yang tidak perlu
                </td>
            </tr>
        </table>
        {{-- <div>
            <div
                style="float: right; text-align: center; padding-right: 128px; padding-top: 32px;font-family:Arial, Helvetica, sans-serif;font-size:12px">
                Ditetapkan di Kabupaten Sanggau<br />
                Pada tanggal {{ tanggal($data->tgl_spd) }}<br />
                PPKD SELAKU BUD,<br />
                <div style="margin-bottom: 48px;"></div>
                <u>{{ $ttd->nama }}</u><br />
                NIP. {{ $ttd->nip }}
                <div style="margin-bottom: 48px;"></div>
            </div>
            <div style="clear: both;"></div>
        </div> --}}
        {{-- <div style="font-family:'Open Sans', Helvetica,Arial,sans-serif;font-size:12px">
            Tembusan disampaikan kepada:<br />
            1. Inspektur *)<br />
            2. Arsip<br />
            *) Coret yang tidak perlu
        </div> --}}
    </div>
</body>

</html>
