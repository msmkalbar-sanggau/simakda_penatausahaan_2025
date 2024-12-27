<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BP Kas Tunai</title>
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

<body onload="window.print()">
    {{-- <body> --}}
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
    <hr>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>BUKU PEMBANTU KAS TUNAI</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    {{-- isi --}}
    <table style='border-collapse:collapse;' width='100%' align='center' border='1' cellspacing='1'
        cellpadding={{ $enter }}>
        <thead>
            <tr>
                <td bgcolor='#CCCCCC' align='center' width='5%'
                    style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'>No.</td>
                <td bgcolor='#CCCCCC' align='center' width='10%'
                    style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'>Tanggal.</td>
                <td bgcolor='#CCCCCC' align='center' width='10%'
                    style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'>No. Bukti</td>
                <td bgcolor='#CCCCCC' align='center' width='30%'
                    style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'>Uraian</td>
                <td bgcolor='#CCCCCC' align='center' width='15%'
                    style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'>Penerimaan</td>
                <td bgcolor='#CCCCCC' align='center' width='15%'
                    style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'>Pengeluaran</td>
                <td bgcolor='#CCCCCC' align='center' width='15%'
                    style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'>Saldo</td>
            </tr>
        </thead>
        <tr>
            <td align='center' width='10%'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'></td>
            <td align='center' width='10%'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'></td>
            <td align='center' width='10%'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'></td>
            <td align='right' width='35%'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'>Saldo Lalu</td>
            <td align='center' width='15%'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'></td>
            <td align='center' width='15%'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'></td>
            <td align='right' width='15%'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;'>
                {{ rupiah($saldo_awal) }}</td>
        </tr>
        @php
            $saldo = $saldo_awal;
            $total_terima = 0;
            $total_keluar = 0;
            $nomor = 0;
        @endphp
        @foreach ($rincian as $rinci)
            @php
                $nomor = $nomor + 1;
            @endphp
            @php
                $saldo = $saldo + $rinci->masuk - $rinci->keluar;
                $total_terima = $total_terima + $rinci->masuk;
                $total_keluar = $total_keluar + $rinci->keluar;
            @endphp
            <tr>
                <td align='center' style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                    {{ $nomor }}</td>
                <td align='center' style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                    {{ tanggal_indonesia($rinci->tgl) }}</td>
                <td align='center' style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                    {{ $rinci->bku }}</td>
                <td align='left' style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                    {{ $rinci->ket }}</td>
                <td align='right' style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                    {{ rupiah($rinci->masuk) }}</td>
                <td align='right' style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                    {{ rupiah($rinci->keluar) }}</td>
                <td align='right' style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                    {{ rupiah($saldo) }}</td>
            </tr>
        @endforeach
        <tr>
            <td bgcolor='#CCCCCC' colspan='4' valign='top' align='center'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>JUMLAH</td>
            <td bgcolor='#CCCCCC' valign='top' align='right'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                {{ rupiah($total_terima) }}</td>
            <td bgcolor='#CCCCCC' valign='top' align='right'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                {{ rupiah($total_keluar) }}</td>
            <td bgcolor='#CCCCCC' valign='top' align='right'
                style='font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black'>
                {{ rupiah($total_terima - $total_keluar + $saldo_awal) }}</td>
        </tr>

    </table>
    {{-- isi --}}
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
