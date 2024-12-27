<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BP Panjar</title>
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
            <td style="text-align: center"><b>BUKU PEMBANTU PANJAR</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    <TABLE style="border-collapse:collapse" width="100%" border="1" cellspacing="0"
        cellpadding={{ $enter }} align=center>
        <THEAD>
            <tr>
                <td width="5%" align="center">NO</td>
                <td width="15%" align="center">Tanggal</td>
                <td width="40%" align="center">Uraian</td>
                <td width="10%" align="center">Terima (Rp)</td>
                <td width="10%" align="center">Keluar (Rp)</td>
                <td width="10%" align="center">Saldo (Rp)</td>
            </tr>
        </THEAD>

        {{-- SALDO AWAL --}}
        <tbody>
            <tr>
                <td width="80" align="center"></td>
                <td width="90" align="center"></td>
                <td width="350" align="left">Saldo Lalu</td>
                <td width="100" align="center"></td>
                <td width="100" align="center"></td>
                <td width="120" align="right">{{ rupiah($saldoawal) }}</td>
            </tr>


            @php
                $saldo = 0;
            @endphp
            @foreach ($rincian as $data)
                <tr>
                    @php
                        $bukti = $data->bku;
                        $tanggal = $data->tgl;
                        $turis = tanggal_indonesia($tanggal);
                        $ket = $data->ket;
                        $in = $data->terima;
                        $out = $data->keluar;
                    @endphp
                    @if ($data->jns == '1')
                        @php
                            $saldo = $saldo + $data->terima;
                            $sal = empty($saldo) || $saldo == 0 ? '' : rupiah($saldo);
                        @endphp
                    @else
                        @php
                            $saldo = $saldo - $data->keluar;
                            $sal = empty($saldo) || $saldo == 0 ? '' : rupiah($saldo);
                        @endphp
                    @endif

                <TR>
                    <TD width="80" valign="top" align="center">{{ $bukti }}</TD>
                    <TD width="90" valign="top" align="center">{{ $turis }}</TD>
                    <TD width="350" valign="top" align="left">{{ $ket }}</TD>
                    <TD width="100" valign="top" align="right">{{ rupiah($in) }}</TD>
                    <TD width="100" valign="top" align="right">{{ rupiah($out) }}</TD>
                    <TD width="120" valign="top" align="right">{{ rupiah($saldo + $saldoawal) }}</TD>
                </TR>
            @endforeach

        </tbody>
    </table>
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
                <td style="text-align: center;">{{ $cari_pa_kpa->nama }}</td>
                <td style="text-align: center;">{{ $cari_bendahara->nama }}</td>
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
