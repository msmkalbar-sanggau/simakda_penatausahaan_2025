<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sub Rincian Objek 77</title>
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
    <TABLE width="100%" style="font-size:12px;">
        <TR>
            <TD align="left" width="25%">Kode Rekening </TD>
            <TD align="left" width="75%">: {{ $kd_akunbelanja }}</TD>
        </TR>
        <TR>
            <TD align="left" width="25%">Nama Rekening </TD>
            <TD align="left" width="75%">: {{ $nm_akunbelanja }}</TD>
        </TR>
        <TR>
            <TD align="left" width="25%">Jumlah Anggaran (DPA) </TD>
            <TD align="left" width="75%">: Rp {{ rupiah($dpa->nilai) }} </TD>
        </TR>
        <TR>
            <TD align="left" width="25%">Jumlah Anggaran (DPPA) </TD>
            <TD align="left" width="75%">: Rp {{ rupiah($dppa->nilai) }} </TD>
        </TR>
    </TABLE>

    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>BUKU PEMBANTU SUB RINCIAN OBYEK BELANJA</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE
                    {{ \Carbon\Carbon::parse($tanggal1)->locale('id')->isoFormat('DD MMMM Y') }} s/td
                    {{ \Carbon\Carbon::parse($tanggal2)->locale('id')->isoFormat('DD MMMM Y') }}</b></td>
        </tr>
    </table>
    <TABLE style="border-collapse:collapse;font-size:14px" border="1" cellspacing="' . $spasi . '"
        cellpadding={{ $enter }} width="100%">
        <THEAD>
            <TR>
                <TD bgcolor="#CCCCCC" width="5%" align="center"><b>No</b></TD>
                <TD bgcolor="#CCCCCC" width="10%" align="center"><b>Tanggal</b></TD>
                <TD bgcolor="#CCCCCC" width="5%" align="center"><b>No. BKU</b></TD>
                <TD bgcolor="#CCCCCC" width="20%" align="center"><b>Uraian</b></TD>
                <TD bgcolor="#CCCCCC" width="15%" align="center"><b>Belanja LS</b></TD>
                <TD bgcolor="#CCCCCC" width="15%" align="center"><b>Belanja UP/GU</b></TD>
                <TD bgcolor="#CCCCCC" width="15%" align="center"><b>Belanja TU</b></TD>
                <TD bgcolor="#CCCCCC" width="15%" align="center"><b>Saldo</b></TD>
            </TR>
        </THEAD>
        @php
            $i = 0;
            $jumls = 0;
            $jumup = 0;
            $jumgu = 0;
            $jml = 0;
            $nos = 0;
            $jumlkeluar = 0;
        @endphp

        @foreach ($rincian as $data)
            @php
                $no_bukti = $data->no_bukti;
                $uraian = $data->ket;
                $no_sp2d = $data->no_sp2d;
                $ls = $data->ls;
                $up = $data->up;
                $gu = $data->gu;
                $tgl_bukti = $data->tgl_bukti;
                $nos = $nos + 1;
                $jumlkeluar = $jumlkeluar + $ls + $up + $gu;
                $sisa = $dppa->nilai - $jumlkeluar;
            @endphp

            <tr>
                <td align="center">{{ $nos }}</td>
                <td align="center">{{ $tgl_bukti }} </td>
                <td align="center">{{ $no_bukti }}</td>
                <td align="left">{{ $uraian }}</td>
                <td align="right">{{ rupiah($ls) }}</td>
                <td align="right">{{ rupiah($up) }}</td>
                <td align="right">{{ rupiah($gu) }}</td>
                <td align="right">{{ rupiah($dppa->nilai - $jumlkeluar) }}</td>
            </tr>
        @endforeach

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
