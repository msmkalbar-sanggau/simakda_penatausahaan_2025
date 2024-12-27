<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RESTITUSI</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #pilihan1>thead>tr>th {
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
            border-bottom: 1px solid black
        }

        .angka {
            text-align: right
        }
    </style>
</head>

{{-- <body onload="window.print()"> --}}

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:16px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px">
                <strong>
                    LAPORAN RESTITUSI <br>
                    @if ($pilihan == '1')
                        TANGGAL {{ tanggal($tanggal) }} <br>
                    @else
                        PERIODE {{ tanggal($periode1) }} SAMPAI DENGAN {{ tanggal($periode2) }} <br>
                    @endif
                    TAHUN ANGGARAN {{ tahun_anggaran() }}
                </strong>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <table style="width: 100%" border="1" id="pilihan1">
        <thead>
            <tr>
                <th><b>No.</b></th>
                <th><b>Kode SKPD</b></th>
                <th><b>SKPD</b></th>
                <th><b>No. Bukti</b></th>
                <th><b>Tgl Bukti</b></th>
                <th><b>Keterangan</b></th>
                <th><b>Rekening</b></th>
                <th><b>Nilai (Rp)</b></th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($data_restitusi as $data)
                @php
                    $total += $data->rupiah;
                @endphp
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td style="text-align: center">{{ $data->kd_skpd }}</td>
                    <td>{{ $data->nm_skpd }}</td>
                    <td style="text-align: center">{{ $data->no_bukti }}</td>
                    <td style="text-align: center">{{ tanggal($data->tgl_bukti) }}</td>
                    <td>{{ $data->keterangan }}</td>
                    <td>{{ $data->kd_rek6 }} - {{ $data->nm_rek6 }}</td>
                    <td class="angka">{{ empty($data->rupiah) ? rupiah(0) : rupiah($data->rupiah) }}</td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td><b>TOTAL</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="angka"><b>{{ rupiah($total) }}</b></td>
            </tr>
        </tbody>
    </table>

    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:800px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center">
                        @if (isset($tanggal))
                            Pontianak, {{ tanggal($tanggal) }}
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
