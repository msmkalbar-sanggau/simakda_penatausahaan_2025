<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Buku Besar Kasda</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #header3>th {
            background-color: #CCCCCC;
            font-weight: normal
        }

        .kanan {
            border-right: 1px solid black
        }

        .kiri {
            border-left: 1px solid black
        }

        .bawah {
            border-bottom: hidden
        }

        .atas {
            border-top: hidden
        }

        .angka {
            text-align: right
        }
    </style>
</head>

{{-- <body onload="window.print()"> --}}

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px" width="100%" align="center"
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

    <table style="border-collapse:collapse;font-family: Arial; font-size:18px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="30">
        <tr>
            <td style="text-align: center">
                <b>
                    BUKU BESAR
                </b>
            </td>
        </tr>
    </table>
    <table width="100%" style="font-size:16px;font-family: Arial;">
        <TR>
            <TD align="left" width="25%">Rekening </TD>
            <TD align="left" width="85%">: {{ $rekening->kd_rek6 }} - {{ $rekening->nm_rek6 }} </TD>
        </TR>
        <TR>
            <TD align="left" width="25%">Periode </TD>
            <TD align="left" width="85%">: {{ $periode1 }} - {{ $periode2 }} </TD>
        </TR>
    </table>

    <table style="width: 100%;font-family: Arial;font-size:12px;"cellpadding="5" border="1" id="rincian">
        <thead>
            <tr id="header3">
                <th rowspan="2" style="width: 10%">TANGGAL</th>
                <th rowspan="2" style="width: 8%">NO BUKTI</th>
                <th rowspan="2" style="width: 40%">URAIAN</th>
                <th rowspan="2" style="width: 5%">REF</th>
                <th rowspan="2" style="width: 15%">DEBET</th>
                <th rowspan="2" style="width: 15%">KREDIT</th>
                <th rowspan="2" style="width: 15%">SALDO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td>SALDO AWAL</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="angka">{{ rupiah(0) }}</td>
            </tr>
            @php
                $saldo_awal = 0;
                $jumlah = 0;
            @endphp
            @foreach ($buku_besar_kasda as $rekap)
                @php
                    $saldo_awal = $saldo_awal + $rekap->kredit;
                    $jumlah = $jumlah + $rekap->kredit;
                @endphp
                <tr>
                    <td>{{ tanggal($rekap->tgl_kas) }}</td>
                    <td>{{ $rekap->no_kas }}</td>
                    <td style="width: 40%;text-align:justify">{{ $rekap->keterangan }}</td>
                    <td>{{ $rekap->no_sts }}</td>
                    <td class="angka" style="width: 10%">{{ rupiah($rekap->debet) }}</td>
                    <td class="angka" style="width: 10%">{{ rupiah($rekap->kredit) }}</td>
                    <td class="angka" style="width: 10%">{{ rupiah($rekap->kredit) }}</td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>JUMLAH</td>
                <td></td>
                <td class="angka">{{ rupiah(0) }}</td>
                <td class="angka">{{ rupiah($jumlah) }}</td>
                <td class="angka">{{ rupiah($jumlah) }}</td>
            </tr>
        </tbody>
    </table>
    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:500px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center">
                        @if (isset($tanggal))
                            Sanggau, {{ tanggal($tanggal) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center">
                        {{ $tanda_tangan->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center"><b><u>{{ $tanda_tangan->nama }}</u></b></td>
                </tr>
                <tr>
                    <td style="text-align: center">{{ $tanda_tangan->pangkat }}</td>
                </tr>
                <tr>
                    <td style="text-align: center">NIP. {{ $tanda_tangan->nip }}</td>
                </tr>
            </table>
        </div>
    @endif
</body>

</html>
