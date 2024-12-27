<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CEK TERIMA SP2D SKPD</title>
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
            <td style="text-align: center;font-size:16px;"><b>DATA SP2D YANG BELUM DITERIMA</b></td>
        </tr>
        <tr>
            <td style="text-align: center;font-size:16px;"><b>{{ strtoupper($skpd) }}</b></td>
        </tr>
    </table>
    <br>
    <table style="width: 100%" border="1" id="rincian">
        <thead>
            <tr>
                <th>No</th>
                <th>SP2D</th>
                <th>Tgl SP2D</th>
                <th>Tgl Cair SP2D</th>
                <th>No SPM</th>
                <th>Nama SKPD</th>
                <th>Keperluan</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($detail as $data1)
                @php
                    $total += $data1->nilai;
                @endphp
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td style="text-align: center">{{ $data1->no_sp2d }}</td>
                    <td style="text-align: center;width: 10%">{{ $data1->tgl_sp2d }}</td>
                    <td style="text-align: center;width: 10%">{{ $data1->tgl_kas_bud }}</td>
                    <td>{{ $data1->no_spm }}</td>
                    <td style="width: 30%">{{ $data1->nm_skpd }}</td>
                    <td style="width: 50%">{{ $data1->keperluan }}</td>
                    <td style="text-align: left;width: 15%">Rp.{{ rupiah($data1->nilai) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="6" style="font-size:12px;text-align:center"><b>J U M L A H </b></td>
                <td class="angka"><b>Rp.{{ rupiah($total) }}</b></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
