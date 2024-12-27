<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rincian</title>
    <style>
        .unborder {
            font-weight: normal;
            text-align: justify
        }

        #rincian>tbody>tr>:first-child {
            padding-left: 20px;
            width: 200px;
        }

        #rincian>tbody>tr>:last-child {
            padding-left: 20px;
        }

        #rincian>tbody>tr>:nth-child(2) {
            padding-left: 100px;
        }

        #rincian>tbody>tr>td {
            font-size: 12px
        }

        table,
        tr,
        td {
            border-collapse: collapse
        }

        th {
            text-align: center;
            background-color: #CCCCCC
        }

        .rincian>tbody>tr>td,
        .rincian>thead>tr>th {
            font-size: 14px
        }
    </style>
</head>

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75"
                    height="100" />
            </td>
            <td align="left" style="font-size:16px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>SKPD {{ $skpd->nm_skpd }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <hr>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td><b>SURAT PERMINTAAN PEMBAYARAN UANG PERSEDIAAN</b></td>
        </tr>
        <tr>
            <td><b>(SPP-UP)</b></td>
        </tr>
        <tr>
            <td><b>Nomor : {{ $no_spp }}</b></td>
        </tr>
        <tr>
            <td><b>RINCIAN RENCANA PENGGUNA ANGGARAN</b></td>
        </tr>
    </table>
    <br>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" border="1">
        <thead>
            <tr>
                <th>No Urut</th>
                <th>Kode Rekening</th>
                <th>Uraian</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center">1</td>
                <td style="text-align: center">{{ $kd_skpd }}</td>
                <td>{{ $skpd->nm_skpd }}</td>
                <td style="text-align: right">{{ rupiah($spp->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center">Jumlah</td>
                <td style="text-align: right">{{ rupiah($total->nilai) }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td>Terbilang : <b><i>{{ ucwords(terbilang($total->nilai)) }}</i></b></td>
        </tr>
    </table>
    <br>
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
            <tr>
                <td width='50%'></td>
                <td width='50%' style="margin: 2px 0px;text-align: center">
                    {{ daerah($kd_skpd) }},
                    @if ($tanpa == 1)
                        ______________{{ tahun_anggaran() }}
                    @else
                        {{ tanggal($spp1->tgl_spp) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td width='50%'></td>
                <td width='50%' style="padding-bottom: 50px;text-align: center">
                    {{ $bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                <td width='50%'></td>
                <td width='50%' style="text-align: center">
                    <b><u>{{ $bendahara->nama }}</u></b> <br>
                    {{ $bendahara->pangkat }} <br>
                    NIP. {{ $bendahara->nip }}
                </td>
            </tr>
            {{-- <tr>
                <td width='50%'></td>
                <td width='50%' style="text-align: center"> {{ $bendahara->pangkat }}</td>
            </tr>
            <tr>
                <td width='50%'></td>
                <td width='50%' style="text-align: center">NIP. {{ $bendahara->nip }}</td>
            </tr> --}}
        </table>
    </div>
</body>

</html>
