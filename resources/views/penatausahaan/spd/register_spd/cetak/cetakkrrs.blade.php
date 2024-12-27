<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SPD</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Open Sans', sans-serif;
        }

        .bordered {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .bordered th,
        .bordered td {
            border: 1px solid black;
            padding: 4px;
        }

        .bordered th {
            background-color: #cccccc;
        }

        .bordered td:nth-child(n+3) {
            text-align: right;
        }

        .unborder {
            font-weight: normal;
        }
    </style>
</head>

<body>
    <div style="text-align: center;">
        <h4 style="margin: 2px 0px;">{{ title() }}</h4>
        <h4 style="margin: 2px 0px;">REGISTER SPD</h4>
        <h4 style="margin: 2px 0px;">TAHUN ANGGARAN {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <div style="text-align: left;">
        <h4 style="margin: 2px 0px" class="unborder">KODE / NAMA SKPD &emsp; &nbsp; : Keseluruhan (SPD Revisi Terakhir)</h4>
        <h4 style="margin: 2px 0px" class="unborder">PADA TANGGAL &emsp; &emsp; &emsp; : {{ tanggal($tgl_awal) }} s/d {{ tanggal($tgl_akhir) }}</h4>
    </div>
    <table class="bordered" id="register">
        <thead align="center">
            <tr>
                <th>No SPD / Keperluan</th>
                <th>Tgl SPD</th>
                <th>Kode / Nama SKPD</th>
                <th>Nilai (Rp)</th>
                <th>Bendahara Pengeluaran</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data1 as $key => $value)
            <?php 
                $nilai = $value->nilai;
                $total = $nilai + $total;
            ?>
            <tr>
               <td style="text-align: left; width: 17%;">{{ $value->no_spd }}</td>
               <td style="text-align: left; width: 8%;">{{ tanggal_indonesia($value->tgl_spd) }}</td>
               <td style="text-align: left; width: 45%;">{{ $value->kd_skpd }} - {{ $value->nm_skpd }}</td>
               <td style="text-align: right; width: 12%;">{{ rupiah($value->nilai) }}</td>
               <td style="text-align: left; width: 18%;">{{ $value->nama }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right;"><b>Jumlah Total</b></td>
                <td style="text-align: right;"><b>{{ rupiah($total) }}</b></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>

</html>