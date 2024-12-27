<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RTH</title>
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
        <tr align="center" style="font-size: 16px">
            <td><b>REKAPITULASI TRANSAKSI HARIAN BELANJA DAERAH (RTH)</b></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px">
                <strong>
                    @if ($pilihan == '1')
                        BULAN {{ Str::upper(bulan($bulan)) }} {{ tahun_anggaran() }}<br>
                    @else
                        PERIODE {{ \Carbon\Carbon::parse($periode1)->format('d') }}
                        {{ bulan(\Carbon\Carbon::parse($periode1)->format('m')) }} s/d
                        {{ \Carbon\Carbon::parse($periode2)->format('d') }}
                        {{ bulan(\Carbon\Carbon::parse($periode2)->format('m')) }} {{ tahun_anggaran() }}
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
                <th rowspan="2">No.</th>
                <th rowspan="2">NAMA SKPD / KUASA BUD</th>
                <th colspan="2">SPM / SPD</th>
                <th colspan="2">SP2D</th>
                <th rowspan="2">POTONGAN PAJAK</th>
                <th rowspan="2">KET</th>
            </tr>
            <tr>
                <th>JUMLAH<br>TOTAL</th>
                <th>NILAI BELANJA<br>TOTAL</th>
                <th>JUMLAH<br>TOTAL</th>
                <th>NILAI BELANJA<br>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $banyak_spm = 0;
                $nilai_spm = 0;
                $banyak_sp2d = 0;
                $nilai_sp2d = 0;
                $nilai_pot = 0;
            @endphp
            @foreach ($data_rth as $data)
                @php
                    $banyak_spm += $data->banyak_spm;
                    $nilai_spm += $data->nil_spm;
                    $banyak_sp2d += $data->banyak_sp2d;
                    $nilai_sp2d += $data->nil_sp2d;
                    $nilai_pot += $data->nil_pot;
                @endphp
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $data->nm_skpd }}</td>
                    <td style="text-align: center">
                        {{ empty($data->banyak_spm) || $data->banyak_spm == 0 ? 0 : $data->banyak_spm }}
                    </td>
                    <td class="angka">
                        {{ empty($data->nil_spm) || $data->nil_spm == 0 ? rupiah(0) : rupiah($data->nil_spm) }}&nbsp;
                    </td>
                    <td style="text-align: center">
                        {{ empty($data->banyak_sp2d) || $data->banyak_sp2d == 0 ? 0 : $data->banyak_sp2d }}
                    </td>
                    <td class="angka">
                        {{ empty($data->nil_sp2d) || $data->nil_sp2d == 0 ? rupiah(0) : rupiah($data->nil_sp2d) }}&nbsp;
                    </td>
                    <td class="angka">
                        {{ empty($data->nil_pot) || $data->nil_pot == 0 ? rupiah(0) : rupiah($data->nil_pot) }}&nbsp;
                    </td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <td><b> TOTAL </b></td>
                <td style="text-align: center"><b>{{ $total_data }}</b></td>
                <td style="text-align: center"><b>{{ $banyak_spm }}</b></td>
                <td class="angka"><b>{{ rupiah($nilai_spm) }}</b>&nbsp;</td>
                <td style="text-align: center"><b>{{ $banyak_sp2d }}</b></td>
                <td class="angka"><b>{{ rupiah($nilai_sp2d) }}</b>&nbsp;</td>
                <td class="angka"><b>{{ rupiah($nilai_pot) }}</b>&nbsp;</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:600px">
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
