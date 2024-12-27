<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BUKU KAS PENERIMAAN DAN PENGELUARAN</title>
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
            border-left: hidden
        }

        .bawah {
            border-bottom: hidden
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
            <td style="text-align: center"><b> {{ strtoupper($header->nm_pemda) }}</b></td>
        </tr>
        <tr>
            <td style="text-align: center"><b>BUKU KAS PENERIMAAN DAN PENGELUARAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:20px"><b>TAHUN ANGGARAN {{ tahun_anggaran() }}</b>
            </td>
        </tr>

    </table>

    <table style="width: 100%" border="1" id="rincian" cellpadding="{{ $no_urut }}">
        <thead>
            <tr>
                <th>NOMOR</th>
                <th>TANGGAL</th>
                <th>PENERIMAAN</th>
                <th>PENGELUARAN</th>
            </tr>
            <tr>
                <th style="font-weight: normal">1</th>
                <th style="font-weight: normal">2</th>
                <th style="font-weight: normal">3</th>
                <th style="font-weight: normal">4</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_terima = 0;
                $total_keluar = 0;
            @endphp
            @foreach ($data_bku as $bku)
                @php
                    $total_terima += $bku->terima;
                    $total_keluar += $bku->keluar;
                @endphp
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td style="text-align: center">{{ tanggal($bku->tgl_kas) }}</td>
                    <td class="angka">
                        {{ rupiah($bku->terima) }}
                    </td>
                    <td class="angka">
                        {{ rupiah($bku->keluar) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2"><b>Jumlah Tanggal
                        {{ tanggal($periode1) }} s.d {{ tanggal($periode2) }}</b>
                </td>
                <td class="angka"><b>{{ rupiah($total_terima) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_keluar) }}</b></td>
            </tr>
        </tbody>
    </table>
    <br>
    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:800px">
            <table class="table" style="width:100%">
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
