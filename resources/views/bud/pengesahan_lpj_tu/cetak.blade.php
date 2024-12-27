<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak LPJ TU</title>
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
    <table style="width: 100%;font-family:Open Sans;text-align:center">
        <tr>
            <td style="font-size:18px"><b>PEMERINTAH {{ strtoupper($header->nm_pemda) }}</b></td>
        </tr>
        <tr>
            <td style="font-size:18px"><b>LAPORAN PERTANGGUNG JAWABAN TAMBAHAN UANG (TU)</b></td>
        </tr>
        <tr>
            <td style="font-size:18px"><b>BENDAHARA PENGELUARAN</b></td>
        </tr>
    </table>

    <br><br>

    <table style="width: 100%;font-family:Open Sans">
        <tbody>
            <tr>
                <td>OPD</td>
                <td>:</td>
                <td>{{ $kd_skpd }}, {{ nama_skpd($kd_skpd) }}</td>
            </tr>
            <tr>
                <td>Program</td>
                <td>:</td>
                <td>{{ $lpj->kd_program }}, {{ $lpj->nm_program }}</td>
            </tr>
            <tr>
                <td>Kegiatan</td>
                <td>:</td>
                <td>{{ $lpj->kd_kegiatan }}, {{ $lpj->nm_kegiatan }}</td>
            </tr>
            <tr>
                <td>Sub Kegiatan</td>
                <td>:</td>
                <td>{{ $lpj->kd_sub_kegiatan }}, {{ $lpj->nm_sub_kegiatan }}</td>
            </tr>
            <tr>
                <td>No SP2D</td>
                <td>:</td>
                <td>{{ $no_sp2d }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <table style="width: 100%;font-family:Open Sans" border="1" id="rincian">
        <thead>
            <tr>
                <th>KODE REKENING</th>
                <th>URAIAN</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($data_lpj as $data)
                @php
                    $total += $data->nilai;
                @endphp
                <tr>
                    <td>{{ $data->kd_rek6 }}</td>
                    <td>{{ $data->nm_rek6 }}</td>
                    <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="height: 15px"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Total</b></td>
                <td style="text-align: right"><b>{{ rupiah($total) }}</b></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Tambahan Uang Persediaan Awal Periode</b></td>
                <td style="text-align: right"><b>{{ rupiah($persediaan) }}</b></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Tambahan Uang Persediaan Akhir Periode</b></td>
                <td style="text-align: right"><b>{{ rupiah($persediaan - $total) }}</b></td>
            </tr>
        </tbody>
    </table>

    <br>
    <div style="padding-top:20px">
        <table class="table" style="width: 100%;font-family:Open Sans;" class="rincian">
            <tr>
                <td style="text-align: center">Mengetahui <br>{{ $ttd->jabatan }}</td>
                <td style="margin: 2px 0px;text-align: center">
                    {{ $daerah->daerah }},
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <br> Telah diverifikasi <br>Petugas
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center">

                </td>
                <td style="padding-bottom: 50px;text-align: center">

                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    <br>
                    <b><u>{{ $ttd->nama }}</u></b> <br>
                    {{ $ttd->pangkat }} <br>
                    NIP. {{ $ttd->nip }}
                </td>
                <td style="text-align: center">
                    ___________________
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
