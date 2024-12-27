<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CEK DATA VALIDASI</title>
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
            <td style="text-align: center;font-size:16px;">SURAT TANDA
                SETORAN </td>
        </tr>
        <tr>
            <td style="text-align: center;font-size:16px;border-bottom:solid 1px black;padding-bottom:4px">(STS)</td>
        </tr>
        <tr>
            <td style="text-align: center;font-size:16px;">DATA SETORAN YANG BELUM VALIDASI</td>
        </tr>
    </table>
    <br>
    <table style="width: 100%" border="1" id="rincian">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No STS</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $data1)
                <tr>
                    <td style="text-align: center;width: 5%">{{ $loop->iteration }}</td>
                    <td style="text-align: center;width: 10%">{{ $data1->tgl_sts }}</td>
                    <td style="width: 30%">{{ $data1->no_sts }}</td>
                    <td style="text-align: left;width: 10%">Rp.{{ rupiah($data1->total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
