<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penutupan Kas Bulanan</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #rincian>tbody>tr>td {
            vertical-align: top
        }

        .kanan {
            border-right: 1px solid black
        }

        .kiri {
            border-left: 1px solid black
        }

        .bawah {
            border-bottom: 1px solid black
        }
    </style>
</head>

{{-- <body onload="window.print()"> --}}

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
            <td align="left" style="font-size:14px"><strong>SKPD {{ $skpd->nm_skpd }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    {{-- <hr> --}}
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>LAPORAN PENUTUPAN KAS BULANAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    <TABLE width='100%' style='font-size:12px'>
        <TR>
            <TD align='left' width='20%'>Kepada Yth. <br>
                Di tempat</TD>
        </TR>
        <TR>
            <TD align='left'>Dengan memperhatikan Peraturan Bupati Kabupaten Sanggau No. 70 Tahun 2013 Tentang
                mekanisme pembayaran dan pertanggungjawaban
                Penggunaan Dana Atas Beban APBD Kabupaten Sanggau, Bersama ini kami sampaikan Laporan Kas
                Bulanan yang terdapat di Bendahara Pengeluaran
                OPD {{ ucwords(strtolower($nm_skpd)) }} adalah sejumlah
                Rp.{{ rupiah($saldoawalbank + $terimabank - $keluarbank) }} (
                {{ ucwords(terbilang($saldoawalbank + $terimabank - $keluarbank)) }}) dengan rincian sebagai berikut:
            </TD>
        </TR>
    </TABLE>

    <TABLE width='100%' style='font-size:12px'>
        <TR>
            <TD align='left' width='5%'>A.</TD>
            <TD align='left' width='60%'>Kas Bendahara Pengeluaran : </TD>
            <TD align='left' width='5%'>&nbsp;</TD>
            <TD align='left' width='15%'>&nbsp;</TD>
            <TD align='left' width='15%'>&nbsp;</TD>
        </TR>
        <TR>
            <TD align='left' width='5%'>&nbsp;</TD>
            <TD align='left' width='60%'>A.1 Saldo Awal </TD>
            <TD align='left' width='5%'>Rp.</TD>
            <TD align='right' width='15%'>{{ rupiah($saldoawalbank) }}</TD>
            <TD align='left' width='15%'>&nbsp;</TD>
        </TR>
        <TR>
            <TD align='left' width='5%'>&nbsp;</TD>
            <TD align='left' width='60%'>A.2 Jumlah Penerimaan </TD>
            <TD align='left' width='5%'>Rp.</TD>
            <TD align='right' width='15%'>{{ rupiah($terimabank) }}</TD>
            <TD align='left' width='15%'>&nbsp;</TD>
        </TR>
        <TR>
            <TD align='left' width='5%'>&nbsp;</TD>
            <TD align='left' width='60%'>A.3 Jumlah Pengeluaran </TD>
            <TD align='left' width='5%'>Rp.</TD>
            <TD align='right' width='15%'>{{ rupiah($keluarbank) }}</TD>
            <TD align='left' width='15%'>&nbsp;</TD>
        </TR>
        <TR>
            <TD align='left' width='5%'>&nbsp;</TD>
            <TD align='left' width='60%'>A.4 Saldo Akhir Bulan </TD>
            <TD align='left' width='5%'>Rp.</TD>
            <TD align='right' width='15%'>{{ rupiah($saldoawalbank + $terimabank - $keluarbank) }}</TD>
            <TD align='left' width='15%'>&nbsp;</TD>
        </TR>
        <TR>
            <TD align='left' width='5%'>&nbsp;</TD>
            <TD colspan='4' align='left' width='40%'><br>
                Saldo Akhir Bulan Tanggal:
                {{ \Carbon\Carbon::parse($tanggal_ttd)->locale('id')->isoFormat('DD MMMM Y') }} Terdiri dari Saldo Kas
                Tunai sebesar Rp {{ rupiah($xhasil_tunai) }} <br>
                Saldo di Bank sebesar Rp {{ rupiah($saldoawalbank + $terimabank - $keluarbank) }} dan Saldo Surat
                Berharga sebesar Rp {{ rupiah($saldoberharga) }}
                <br>
            </TD>
        </TR>
    </TABLE>

    @php
        for ($i = 0; $i <= $enter; $i++) {
            echo '<br>';
        }
    @endphp
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table" style="width: 100%;font-size:12px;font-family:Open Sans">
            <tr>
                <td style="margin: 2px 0px;text-align: center;">
                    Disetujui oleh
                </td>
                <td style="margin: 2px 0px;text-align: center;">
                    {{ $daerah->daerah }},
                    {{ \Carbon\Carbon::parse($tanggal_ttd)->locale('id')->isoFormat('DD MMMM Y') }}
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center;">
                    {{ ucwords(strtolower($cari_pa_kpa->jabatan)) }}
                </td>
                <td style="padding-bottom: 50px;text-align: center;">
                    {{ ucwords(strtolower($cari_bendahara->jabatan)) }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;"><b><u>{{ $cari_pa_kpa->nama }}</u></b></td>
                <td style="text-align: center;"><b><u>{{ $cari_bendahara->nama }}</u></b></td>
            </tr>
            <tr>
                <td style="text-align: center;">{{ $cari_pa_kpa->pangkat }}</td>
                <td style="text-align: center;">{{ $cari_bendahara->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">NIP. {{ $cari_pa_kpa->nip }}</td>
                <td style="text-align: center;">NIP. {{ $cari_bendahara->nip }}</td>
            </tr>

        </table>
    </div>
</body>

</html>
