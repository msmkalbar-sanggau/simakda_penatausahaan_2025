<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PENERIMAAN KAS</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #rincian>thead>tr>th {
            background-color: #CCCCCC;
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
            <td style="text-align: center;font-size:16px;border-bottom:solid 1px black;padding-bottom:4px">
                {{ $header->nm_pemda }}</td>
        </tr>
        <tr>
            <td style="text-align: center;font-size:16px;border-bottom:solid 1px black;padding-bottom:4px">SURAT TANDA
                SETORAN</td>
        </tr>
        <tr>
            <td style="text-align: center;font-size:16px;border-bottom:solid 1px black;padding-bottom:4px">(STS)</td>
        </tr>
    </table>
    <br>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width: 10%">No STS</td>
                <td style="width: 1px">:</td>
                <td style="width: 40%">{{ $no_sts }}</td>
                <td style="width: 10%">Bank</td>
                <td style="width: 1px">:</td>
                <td style="width: 40%">{{ $data->nm_bank }}</td>
            </tr>
            <tr>
                <td style="width: 10%">OPD</td>
                <td style="width: 1px">:</td>
                <td style="width: 40%">{{ $data->nm_skpd }}</td>
                <td style="width: 10%">No Rekening</td>
                <td style="width: 1px">:</td>
                <td style="width: 40%">{{ $data->rek_bank }}</td>
            </tr>
            <tr>
                <td colspan="3">Harap diterima uang sebesar <br>(dengan huruf)</td>
                <td colspan="3"><i>({{ terbilang($data->total) }})</i></td>
            </tr>
            <tr>
                <td colspan="6">Dengan rincian penerimaan sebagai berikut</td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%" border="1" id="rincian">
        <thead>
            <tr>
                <th>No</th>
                <th colspan="5">Kode Rekening</th>
                <th>Uraian Rincian Objek</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($detail as $data1)
                @php
                    $total += $data1->rupiah;
                @endphp
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td style="width: 3%">{{ Str::substr($data1->kd_rek6, 0, 1) }}</td>
                    <td style="width: 3%">{{ Str::substr($data1->kd_rek6, 1, 1) }}</td>
                    <td style="width: 3%">{{ Str::substr($data1->kd_rek6, 2, 1) }}</td>
                    <td style="width: 3%">{{ Str::substr($data1->kd_rek6, 3, 2) }}</td>
                    <td style="width: 3%">{{ Str::substr($data1->kd_rek6, 5, 2) }}</td>
                    <td>{{ $data1->nm_rek6 }}</td>
                    <td style="text-align: right">{{ rupiah($data1->rupiah) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7" style="text-align: right">Jumlah</td>
                <td style="text-align: right">{{ rupiah($total) }}</td>
            </tr>
        </tbody>
    </table>
    <table style="width:100%">
        <tr>
            <td colspan="6" style="text-align: center">Uang tersebut diterima pada tanggal
                {{ tanggal($data->tgl_sts) }}
            </td>
        </tr>
    </table>
    <div style="padding-top:20px">
        <table class="table" style="width:100%">
            <tr>
                <td style="text-align: center"><b>Mengetahui</b></td>
                <td style="margin: 2px 0px;text-align: center">
                    <b>Bendahara Penerimaan</b>
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center">
                    <b>Pengguna Anggaran/Kuasa Pengguna Anggaran</b>
                </td>
                <td style="padding-bottom: 50px;text-align: center">
                    <b>Bendahara Penerimaan Pembantu</b>
                </td>
            </tr>
            <tr>
                <td style="text-align: center"><b>Nama Lengkap</b></td>
                <td style="text-align: center"><b>Nama Lengkap</b></td>
            </tr>
            <tr>
                <td style="text-align: center"><b>NIP.</b></td>
                <td style="text-align: center"><b>NIP.</b></td>
            </tr>
        </table>
    </div>
</body>

</html>
