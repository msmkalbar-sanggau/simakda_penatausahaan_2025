<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Rincian</title>
    <style>
        table,
        th,
        td {
            border-collapse: collapse;
        }

        #rincian1>thead>tr>th {
            background-color: #CCCCCC;
            font-size: 14px
        }

        #rincian>tbody>tr>td {
            font-size: 14px
        }

        .rincian>tbody>tr>td {
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

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td style="text-align:center">SURAT PERMINTAAN PEMBAYARAN TAMBAHAN UANG PERSEDIAAN <br>
                (SPP - TU) <br>
                <b>Nomor : {{ $no_spp }}</b> <br>
                <b><u>RINCIAN</u></b>
            </td>
        </tr>
        <tr>
            <td style="height: 50px"></td>
        </tr>
        <tr>
            <td>RENCANA PENGGUNA ANGGARAN</td>
        </tr>
    </table>

    <table id="rincian1" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" border="1"
        class="rincian">
        <thead>
            <tr>
                <th style="text-align: center">No Urut</th>
                <th style="text-align: center">Kode Rekening</th>
                <th style="text-align: center">Uraian</th>
                <th style="text-align: center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($data_spp as $data)
                @if ($data->urut == 1)
                    <tr>
                        <td style="text-align:center"><b>{{ $loop->iteration }}</b></td>
                        <td><b>{{ $data->kode }}</b></td>
                        <td><b>{{ $data->nama }}</b></td>
                        <td style="text-align: right"><b>{{ rupiah($data->nilai) }}</b></td>
                    </tr>
                @elseif ($data->urut == 7)
                    @php
                        $total += $data->nilai;
                    @endphp
                    <tr>
                        <td style="text-align:center"></td>
                        <td>{{ left($data->kode, 15) }}.{{ dotrek(substr($data->kode, 16, 13)) }}</td>
                        <td>{{ $data->nama }}</td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    </tr>
                @else
                    <tr>
                        <td style="text-align:center"></td>
                        <td>{{ left($data->kode, 16) }}{{ dotrek(substr($data->kode, 16, 12)) }}</td>
                        <td>{{ $data->nama }}</td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td style="text-align:right"><b>JUMLAH</b></td>
                <td style="text-align: right"><b>{{ rupiah($total) }}</b></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <tr>
            <td>Terbilang : {{ ucwords(terbilang($total)) }}</td>
        </tr>
    </table>

    <br>
    <br>
    <br>
    {{-- tanda tangan --}}
    <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td style="text-align: center">MENGETAHUI :</td>
            <td style="margin: 2px 0px;text-align: center">
                {{ $daerah->daerah }},
                @if ($tanpa == 1)
                    ______________{{ tahun_anggaran() }}
                @else
                    {{ tanggal($tanggal) }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 50px;text-align: center">
                {{ $pptk->jabatan }}
            </td>
            <td style="padding-bottom: 50px;text-align: center">
                {{ $bendahara->jabatan }}
            </td>
        </tr>
        <tr>
            <td style="text-align: center">
                <b><u>{{ $pptk->nama }}</u></b> <br>
                {{ $pptk->pangkat }} <br>
                {{ $pptk->nip }}
            </td>
            <td style="text-align: center">
                <b><u>{{ $bendahara->nama }}</u></b> <br>
                {{ $bendahara->pangkat }} <br>
                NIP. {{ $bendahara->nip }}
            </td>
        </tr>
    </table>
    <br><br>
    <table class="table rincian"
        style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td style="text-align: center">MENGETAHUI :</td>
        </tr>
        <tr>
            <td style="padding-bottom: 50px;text-align: center">
                Kuasa Bendahara Umum Daerah <br>
                {{ $ppkd->jabatan }}
            </td>
        </tr>
        <tr>
            <td style="text-align: center">
                <b><u>{{ $ppkd->nama }}</u></b> <br>
                {{ $ppkd->pangkat }} <br>
                {{ $ppkd->nip }}
            </td>
        </tr>
    </table>
</body>

</html>
