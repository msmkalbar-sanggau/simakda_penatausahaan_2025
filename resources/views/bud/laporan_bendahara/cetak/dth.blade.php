<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DTH</title>
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

        #pilihan1>tfoot>tr>td {
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
                    DAFTAR TRANSAKSI HARIAN BELANJA DAERAH (DTH)
                </strong>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px">
                <strong>
                    @if ($pilihan == '1')
                        {{ Str::upper(bulan($bulan)) }}
                    @endif
                </strong>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <table style="width: 100%">
        <tr>
            <td>OPD</td>
            <td>: {{ $skpd }} {{ nama_skpd($skpd) }}</td>
        </tr>
    </table>

    <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th colspan="2">SPM/SPD</th>
                <th colspan="2">SP2D</th>
                <th rowspan="2">Akun Belanja</th>
                <th colspan="3">Potongan Pajak</th>
                <th rowspan="2">NPWP</th>
                <th rowspan="2">Nama Rekanan</th>
                <th rowspan="2">NTPN</th>
                <th rowspan="2">Ket</th>
            </tr>
            <tr>
                <th>No. SPM</th>
                <th>Nilai Belanja(Rp)</th>
                <th>No. SP2D</th>
                <th>Nilai Belanja (Rp)</th>
                <th>Akun Potongan</th>
                <th>Jenis</th>
                <th>Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_data = count($data_dth);
                $total_nilai = 0;
                $total_belanja = 0;
                $total_potongan = 0;
            @endphp
            @foreach ($data_dth as $data)
                @php
                    $total_nilai += $data->nilai;
                    $total_belanja += $data->nilai_belanja;
                    $total_potongan += $data->nilai_pot;
                @endphp
                @if ($data->urut == 1)
                    <tr>
                        <td style="text-align: center">{{ $loop->iteration }}</td>
                        <td>{{ $data->no_spm }}</td>
                        <td class="angka">{{ rupiah($data->nilai) }}</td>
                        <td>{{ $data->no_sp2d }}</td>
                        <td class="angka">
                            {{ $data->jns_spp == '2' ? rupiah($data->nilai) : rupiah($data->nilai_belanja) }}
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $data->npwp }}</td>
                        <td>{{ $data->nmrekan }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td style="border-top:hidden"></td>
                        <td style="border-top:hidden"></td>
                        <td style="border-top:hidden"></td>
                        <td style="border-top:hidden"></td>
                        <td style="border-top:hidden"></td>
                        <td style="border-top:hidden">{{ $data->kode_belanja }}</td>
                        <td style="border-top:hidden">
                            @if ($data->kd_rek6 == '2130301')
                                411211
                            @elseif ($data->kd_rek6 == '2130101')
                                411121
                            @elseif ($data->kd_rek6 == '2130201')
                                411122
                            @elseif ($data->kd_rek6 == '2130401')
                                411124
                            @elseif ($data->kd_rek6 == '2130501')
                                411128
                            @elseif ($data->kd_rek6 == '2130601')
                                411128
                            @elseif ($data->kd_rek6 == '2110301')
                                411128
                            @endif
                        </td>
                        <td style="border-top:hidden">
                            @if ($data->kd_rek6 == '2130301')
                                PPn
                            @elseif ($data->kd_rek6 == '2130101')
                                PPh 21
                            @elseif ($data->kd_rek6 == '2130201')
                                PPh 22
                            @elseif ($data->kd_rek6 == '2130401')
                                PPh 23
                            @elseif ($data->kd_rek6 == '2130501')
                                PPh 4
                            @elseif ($data->kd_rek6 == '2130601')
                                PPh 4 Ayat (2)
                            @elseif ($data->kd_rek6 == '2110301')
                                PPh Pusat
                            @endif
                        </td>
                        <td style="border-top:hidden" class="angka">{{ rupiah($data->nilai_pot) }}</td>
                        <td style="border-top:hidden">{{ $data->npwp }}</td>
                        <td style="border-top:hidden">{{ $data->nmrekan }}</td>
                        <td style="border-top:hidden">{{ $data->ntpn }}</td>
                        <td style="border-top:hidden">{{ $data->ket }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align: center">Total</td>
                <td style="text-align: center">{{ $total_data }}</td>
                <td class="angka">{{ rupiah($total_nilai) }}</td>
                <td></td>
                <td class="angka">{{ rupiah($total_belanja) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="angka">{{ rupiah($total_potongan) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="padding-top:20px;padding-left:900px">
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
                    @if ($jenis_print == 'keseluruhan')
                        {{ $pa_kpa->jabatan }}
                    @else
                        {{ $bendahara->jabatan }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    <b>
                        <u>
                            @if ($jenis_print == 'keseluruhan')
                                {{ $pa_kpa->nama }}
                            @else
                                {{ $bendahara->nama }}
                            @endif
                        </u>
                    </b>
                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    @if ($jenis_print == 'keseluruhan')
                        {{ $pa_kpa->pangkat }}
                    @else
                        {{ $bendahara->pangkat }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="text-align: center">NIP.
                    @if ($jenis_print == 'keseluruhan')
                        {{ $pa_kpa->nip }}
                    @else
                        {{ $bendahara->nip }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
