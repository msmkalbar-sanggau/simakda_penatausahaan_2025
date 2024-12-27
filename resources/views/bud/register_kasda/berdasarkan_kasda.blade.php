<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REGISTER KASDA</title>
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
            <td style="text-align: center">REGISTER PENDAPATAN DAN CP</td>
        </tr>
        <tr>
            <td style="text-align: center">(INPUTAN KASDA)</td>
        </tr>
        @if ($pilihan == '2')
            <tr>
                <td style="text-align: center">{{ nama_skpd($skpd) }} <br> {{ tanggal($periode1) }} SD
                    {{ tanggal($periode2) }}</td>
            </tr>
        @endif
    </table>

    <table style="width: 100%" border="1" id="rincian">
        <thead>
            @if ($pilihan == '2')
                <tr>
                    <th>TANGGAL</th>
                    <th>KODE</th>
                    <th>URAIAN</th>
                    <th>PENDAPATAN</th>
                    <th>CP</th>
                    <th>JUMLAH</th>
                </tr>
                <tr>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                </tr>
            @else
                <tr>
                    <th>KODE</th>
                    <th>URAIAN</th>
                    <th>PENDAPATAN</th>
                    <th>CP</th>
                    <th>JUMLAH</th>
                </tr>
                <tr>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @php
                $total_pend = 0;
                $total_cp = 0;
            @endphp
            @foreach ($rincian as $data)
                @php
                    $total_pend += $data->pend;
                    $total_cp += $data->cp;
                @endphp
                @if ($pilihan == '1')
                    <tr>
                        <td>{{ $data->kode }}</td>
                        <td>{{ $data->uraian }}</td>
                        <td class="angka">{{ rupiah($data->pend) }}</td>
                        <td class="angka">{{ rupiah($data->cp) }}</td>
                        <td class="angka"><b>{{ rupiah($data->pend + $data->cp) }}</b></td>
                    </tr>
                @elseif ($pilihan == '2')
                    <tr>
                        <td style="text-align: center">{{ $data->tgl }}</td>
                        <td>{{ $data->kode }}</td>
                        <td>{{ $data->uraian }}</td>
                        <td class="angka">{{ rupiah($data->pend) }}</td>
                        <td class="angka">{{ rupiah($data->cp) }}</td>
                        <td class="angka"><b>{{ rupiah($data->pend + $data->cp) }}</b></td>
                    </tr>
                @endif
            @endforeach
            @if ($pilihan == '1')
                <tr>
                    <td colspan="2" style="text-align: center"><b>T O T A L</b></td>
                    <td class="angka"><b>{{ rupiah($total_pend) }}</b></td>
                    <td class="angka"><b>{{ rupiah($total_cp) }}</b></td>
                    <td class="angka"><b>{{ rupiah($total_pend + $total_cp) }}</b></td>
                </tr>
            @elseif ($pilihan == '2')
                <tr>
                    <td colspan="3" style="text-align: center"><b>T O T A L</b></td>
                    <td class="angka"><b>{{ rupiah($total_pend) }}</b></td>
                    <td class="angka"><b>{{ rupiah($total_cp) }}</b></td>
                    <td class="angka"><b>{{ rupiah($total_pend + $total_cp) }}</b></td>
                </tr>
            @endif
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
