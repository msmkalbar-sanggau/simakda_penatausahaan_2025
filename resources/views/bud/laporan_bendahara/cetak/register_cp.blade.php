<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REGISTER CP</title>
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
            <td align="center" style="font-size:16px"><strong>REGISTER PENERIMAAN CONTRA POS TAHUN ANGGARAN
                    {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px">Tanggal {{ $tanggal1 }} s/d {{ $tanggal2 }}</td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    @if ($pilihan == '1')
        <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>KODE</th>
                    <th>NAMA SKPD</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp
                @foreach ($data_register as $data)
                    @php
                        $total += $data->total;
                    @endphp
                    <tr>
                        <td style="text-align: center">{{ $loop->iteration }}</td>
                        <td>{{ $data->kd_skpd }}</td>
                        <td>{{ $data->nm_skpd }}</td>
                        <td class="angka">{{ rupiah($data->total) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: center"><b>JUMLAH</b></td>
                    <td class="angka">{{ rupiah($total) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
            <thead>
                <tr>
                    <th>No. Kas</th>
                    <th>Tanggal Kas</th>
                    <th>Uraian</th>
                    <th>Kode Rekening</th>
                    <th>Penerimaan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @if ($pilihan == '2')
                    @php
                        $total = 0;
                    @endphp
                    @foreach ($data_register as $register)
                        @if ($register->jenis == 1)
                            <tr>
                                <td style="text-align: center">{{ $register->no_kas }}</td>
                                <td style="text-align: center">{{ $register->tgl_kas }}</td>
                                <td>{{ $register->keterangan }}<br>SKPD : {{ $register->kd_skpd }}<br>Total :
                                    {{ rupiah($register->nilai) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @else
                            @php
                                $total += $register->nilai;
                            @endphp
                            <tr>
                                <td style="border-top:hidden"></td>
                                <td style="border-top:hidden"></td>
                                <td style="border-top:hidden"></td>
                                <td style="border-top:hidden;vertical-align:top">
                                    {{ $register->kd_sub_kegiatan }}.{{ $register->kd_rek }}</td>
                                <td style="border-top:hidden;vertical-align:top;text-align:right">
                                    {{ rupiah($register->nilai) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="4" style="text-align: right"><b>Jumlah Periode Ini</b>&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td style="text-align: right">{{ rupiah($total) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: right"><b>Jumlah Periode Lalu</b>&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td style="text-align: right">{{ rupiah($total_lalu->nilai_lalu) }}</td>
                    </tr>
                @elseif ($pilihan == '3')
                    @php
                        $total = 0;
                    @endphp
                    @foreach ($data_register as $register)
                        @if ($register->jenis == 1)
                            <tr>
                                <td style="text-align: center">{{ $register->no_kas }}</td>
                                <td style="text-align: center">{{ $register->tgl_kas }}</td>
                                <td>{{ $register->keterangan }}<br>SKPD : {{ $register->kd_skpd }}<br>Total :
                                    {{ rupiah($register->nilai) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @else
                            @php
                                $total += $register->nilai;
                            @endphp
                            <tr>
                                <td style="border-top:hidden"></td>
                                <td style="border-top:hidden"></td>
                                <td style="border-top:hidden"></td>
                                <td style="border-top:hidden;vertical-align:top">
                                    {{ $register->kd_sub_kegiatan }}.{{ $register->kd_rek }}</td>
                                <td style="border-top:hidden;vertical-align:top;text-align:right">
                                    {{ rupiah($register->nilai) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="4" style="text-align: right"><b>Jumlah Periode Ini</b>&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td style="text-align: right">{{ rupiah($total) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: right"><b>Jumlah Periode Lalu</b>&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td style="text-align: right">{{ rupiah($total_lalu->nilai_lalu) }}</td>
                    </tr>
                @elseif ($pilihan == '4')
                    @php
                        $total = 0;
                    @endphp
                    @foreach ($data_register as $register)
                        @if ($register->jenis == 1)
                            <tr>
                                <td style="text-align: center">{{ $register->no_kas }}</td>
                                <td style="text-align: center">{{ $register->tgl_kas }}</td>
                                <td>{{ $register->keterangan }}<br>SKPD : {{ $register->kd_skpd }}<br>Total :
                                    {{ rupiah($register->nilai) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @else
                            @php
                                $total += $register->nilai;
                            @endphp
                            <tr>
                                <td style="border-top:hidden"></td>
                                <td style="border-top:hidden"></td>
                                <td style="border-top:hidden"></td>
                                <td style="border-top:hidden;vertical-align:top">
                                    {{ $register->kd_sub_kegiatan }}.{{ $register->kd_rek }}</td>
                                <td style="border-top:hidden;vertical-align:top;text-align:right">
                                    {{ rupiah($register->nilai) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="4" style="text-align: right"><b>Jumlah Periode Ini</b>&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td style="text-align: right">{{ rupiah($total) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: right"><b>Jumlah Periode Lalu</b>&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td style="text-align: right">{{ rupiah($total_lalu->nilai_lalu) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

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
