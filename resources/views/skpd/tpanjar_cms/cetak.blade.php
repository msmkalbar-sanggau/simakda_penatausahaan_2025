<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
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

<body>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td><strong>{{ strtoupper($daerah->kab_kota) }}</strong></td>
            </tr>
            <tr>
                <td><strong>SKPD {{ $skpd->nm_skpd }}</strong></td>
            </tr>
            <tr>
                <td><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table style="width: 100%">
        <tr>
            <td style="text-align: center"><b>LIST TRANSAKSI</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ tanggal($tgl_voucher) }}</b></td>
        </tr>
    </table>
    <table style="width: 100%;margin-top:10px" border="1" id="rincian">
        <thead>
            <tr>
                <th style="width:5px">No</th>
                <th style="width:5%">SKPD</th>
                <th style="width:20%">Kode Rekening</th>
                <th style="width:40%">Uraian</th>
                <th style="width:10px">Penerimaan</th>
                <th style="width:10px">Pengeluaran</th>
                <th style="width:10px">ST</th>
            </tr>
            <tr>
                <th class="t1">1</th>
                <th class="t1">2</th>
                <th class="t1">3</th>
                <th class="t1">4</th>
                <th class="t1">5</th>
                <th class="t1">6</th>
                <th class="t1">7</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <table style="width: 100%">
    </table>
</body>

</html>
