<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Penggunaan Anggaran</title>
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
            <td style="text-align: center"><b>BUKU PEMBANTU SUB RINCIAN OBYEK BELANJA</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE
                    {{ strtoupper(\Carbon\Carbon::parse($tanggal1)->locale('id')->isoFormat('DD MMMM Y')) }} S/D
                    {{ strtoupper(\Carbon\Carbon::parse($tanggal2)->locale('id')->isoFormat('DD MMMM Y')) }}</b></td>
        </tr>
    </table>
    <TABLE width="100%" style="font-size:12px;">
        <TR>
            <TD align="left" width="25%">Nama Sub Kegiatan </TD>
            <TD align="left" width="75%">: {{ $kd_subkegiatan }} - {{ $nm_subkegiatan }}</TD>
        </TR>
        <TR>
            <TD align="left" width="25%">Nama Rekening </TD>
            <TD align="left" width="75%">: {{ $kd_akunbelanja }} - {{ $nm_akunbelanja }}</TD>
        </TR>
        <TR>
            <TD align="left" width="25%">Jumlah Anggaran </TD>
            <TD align="left" width="75%">: Rp {{ rupiah($dppa->nilai) }} </TD>
        </TR>
    </TABLE>
    <TABLE style="border-collapse:collapse;font-size:12px" border="1" cellspacing="2"
        cellpadding={{ $enter }} width="100%">
        <THEAD>
            <TR>
                <TD align="center"><b>No SP2D/ No SPP /No Tagih/ No Bukti</b></TD>
                <TD align="center"><b>Tanggal</b></TD>
                <TD align="center"><b>Keterangan</b></TD>
                <TD align="center"><b>Sumber Dana</b></TD>
                <TD align="center"><b>Nilai</b></TD>
            </TR>
        </THEAD>
        @php
            $tnilai = 0;
        @endphp

        @foreach ($rincian as $row)
            @php
                $no1 = $row->no1;
                $tgl = $row->tgl;
                $ket = $row->ket;
                $nilai = $row->nilai;
                $sumberdana = $row->sumberdana;
                $tnilai = $tnilai + $nilai;
            @endphp

            <TR>
                <TD align="left">{{ $no1 }}</TD>
                <TD align="left">{{ tanggal_indonesia($tgl) }}</TD>
                <TD align="left">{{ $ket }}</TD>
                <TD align="left">{{ $sumberdana }}</TD>
                <TD align="right">{{ rupiah($nilai) }}</TD>
            </TR>
        @endforeach
        <TR>
            <TD colspan="4" align="right"><b>Jumlah Anggaran</b></TD>
            <TD align="right">{{ rupiah($dppa->nilai) }}</TD>

        </TR>
        <TR>
            <TD colspan="4" align="right"><b>Total Inputan</b></TD>
            <TD align="right">{{ rupiah($tnilai) }}</TD>

        </TR>
        <TR>
            <TD colspan="4" align="right"><b>Sisa Anggaran</b></TD>
            <TD align="right">{{ rupiah($dppa->nilai - $tnilai) }}</TD>

        </TR>
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
