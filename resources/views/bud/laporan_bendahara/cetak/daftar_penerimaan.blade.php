<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DAFTAR PENERIMAAN</title>
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
                    DAFTAR PENERIMAAN <br> ({{ $pengirim->nm_pengirim }}) <br>
                    @if ($pilihan == '1')
                        TANGGAL : {{ tanggal($tanggal1) }} S/D {{ tanggal($tanggal2) }}
                    @elseif ($pilihan == '2')
                        BULAN : {{ bulan($periode1) }} S/D {{ bulan($periode2) }} {{ tahun_anggaran() }}
                    @endif
                </strong>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
        <thead>
            <tr>
                <th><b>No. Kas</b></th>
                <th><b>Tanggal</b></th>
                <th><b>Penerimaan (Rp)</b></th>
                <th><b>Keterangan</b></th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_periode_ini = 0;
            @endphp
            @foreach ($data_penerimaan as $data)
                @php
                    $total_periode_ini += $data->total;
                @endphp
                @if (in_array($kd_pengirim, $list_pengirim))
                    <tr>
                        <td style="text-align: center">{{ $data->no_sts }}</td>
                        <td style="text-align: center">{{ $data->tgl_sts }}</td>
                        <td class="angka">{{ empty($data->total) ? rupiah(0) : rupiah($data->total) }}</td>
                        <td>{{ $data->keterangan }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $data->no_sts }}</td>
                        <td>{{ $data->tgl_sts }}</td>
                        <td class="angka">{{ empty($data->total) ? rupiah(0) : rupiah($data->total) }}</td>
                        <td>{{ $data->nm_pengirim }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td colspan="2">Jumlah Periode Ini</td>
                <td class="angka"><b>{{ rupiah($total_periode_ini) }}</b></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2" style="border-top:hidden">Jumlah s/d Periode Lalu</td>
                <td class="angka" style="border-top:hidden"><b>{{ rupiah($penerimaan_lalu) }}</b></td>
                <td style="border-top:hidden"></td>
            </tr>
            <tr>
                <td colspan="2" style="border-top:hidden">Jumlah s/d Periode Ini</td>
                <td class="angka" style="border-top:hidden"><b>{{ rupiah($total_periode_ini + $penerimaan_lalu) }}</b>
                </td>
                <td style="border-top:hidden"></td>
            </tr>
        </tbody>
    </table>
    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:500px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center">
                        @if (isset($tanggal))
                            Sanggau, {{ tanggal($tanggal) }}
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
