<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BP Pajak</title>
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
    {{-- <hr> --}}
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="1" cellpadding="2">
        <tr>
            <td style="text-align: center"><b>BUKU PEMBANTU PAJAK</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    <p>Kode/Nama akun : {{ $pilihan3 }} / {{ $namapilihan3 }}</p>
    {{-- isi --}}
    <table style='border-collapse:collapse;' width='100%' align='center' border='1' cellspacing='1'
        cellpadding='2'>
        <table style="border-collapse:collapse;font-size:12px" width="100%" border="1" cellspacing="0"
            cellpadding={{ $enter }} align=center>
            <thead>
                <tr>
                    <td width="20" align="center">No Urut</td>
                    <td width="90" align="center">Tanggal</td>
                    <td width="50" align="center">No. Buku Kas</td>
                    <td width="400" align="center">Uraian</td>
                    <td width="150" align="center">Penerimaan</td>
                    <td width="150" align="center">Pengeluaran</td>
                </tr>
                <tr>
                    <td align="center">1</td>
                    <td align="center">2</td>
                    <td align="center">3</td>
                    <td align="center">4</td>
                    <td align="center">5</td>
                    <td align="center">6</td>
                </tr>
            </thead>

            @php
                $saldo = $saldopjk;
                $jumlahin = 0;
                $jumlahout = 0;
                $i = 0;
            @endphp
            @foreach ($rincian as $rinci)
                @php
                    $i = $i + 1;
                    $bukti = $rinci->bku;
                    $tanggal = $rinci->tgl;
                    $ket = $rinci->ket;
                    $in = $rinci->terima;
                    $out = $rinci->keluar;
                    $saldo = $saldo + $rinci->terima - $rinci->keluar;
                    $sal = empty($saldo) || $saldo == 0 ? '' : number_format($saldo, '2', ',', '.');
                    $jumlahin = $jumlahin + $in;
                    $jumlahout = $jumlahout + $out;
                @endphp
                <tr>
                    <TD align="center">{{ $i }}</TD>
                    <TD align="center">{{ tanggal_indonesia($tanggal) }}</TD>
                    <TD align="center">{{ $bukti }}</TD>
                    <TD align="left">{{ $ket }}</TD>
                    <TD align="right">{{ rupiah($in) }}</TD>
                    <TD align="right">{{ rupiah($out) }}</TD>
                </tr>
            @endforeach
            <tr>
                <td colspan ="4" align="left">JUMLAH BULAN INI<br> JUMLAH S/D BULAN LALU <br>JUMLAH SELURUHNYA</td>
                <td align="right">{{ rupiah($jumlahin) }} <br> {{ rupiah($terima) }}
                    <br> {{ rupiah($terima + $jumlahin) }}
                </td>
                <td align="right">{{ rupiah($jumlahout) }}<br> {{ rupiah($keluar) }}
                    <br> {{ rupiah($keluar + $jumlahout) }}
                </td>

            </tr>
            <tr>
                <td colspan ="4" align="left">SISA YANG BELUM DISETOR</td>
                <td colspan ="2" align="right">{{ rupiah($terima + $jumlahin - ($keluar + $jumlahout)) }}</td>
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
