<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>UYHD</title>
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
                    UYHD TAHUN ANGGARAN {{ tahun_anggaran() }}
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
                <th><b>No. Urut</b></th>
                <th><b>Uraian</b></th>
                <th><b>Kode Rekening</b></th>
                <th><b>Penerimaan (Rp)</b></th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td>Tanggal :
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode1) }} s.d {{ tanggal($periode2) }}
                    @endif
                </td>
                <td></td>
                <td></td>
            </tr>
            @php
                $total = 0;
            @endphp
            @foreach ($data_uyhd as $data)
                @php
                    $total += $data->rupiah;
                @endphp
                @if ($data->urut == '1')
                    <tr>
                        <td style="text-align: center">{{ $data->no_kas }}</td>
                        <td>{{ $data->nm_pengirim == '' ? $data->nm_skpd : $data->nm_pengirim }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td style="border-top: hidden"></td>
                        <td style="border-top: hidden">{{ $data->nm_rek6 }} (UHYD)</td>
                        <td style="border-top: hidden">
                            {{ $data->kd_sub_kegiatan == '' ? '' : $data->kd_sub_kegiatan . $data->kd_rek6 }} (UHYD)
                        </td>
                        <td class="angka" style="border-top: hidden">
                            {{ $data->rupiah < 0 ? '(' . rupiah($data->rupiah * -1) . ')' : rupiah($data->rupiah) }}
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td class="bawah"></td>
                <td colspan="2" class="bawah kiri">Jumlah Tanggal
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode1) }} s.d {{ tanggal($periode2) }}
                    @endif
                </td>
                <td class="angka bawah">{{ rupiah($total) }}</td>
            </tr>
            <tr>
                <td class="bawah"></td>
                <td colspan="2" class="bawah kiri">Jumlah Sampai Tanggal :
                    @if ($pilihan == '1')
                        @php
                            $tanggal_sebelumnya = strtotime('-1 day', strtotime($tanggal));
                            $tanggal_sebelumnya = date('Y-m-d', $tanggal_sebelumnya);
                        @endphp
                        {{ tanggal($tanggal_sebelumnya) }}
                    @elseif ($pilihan == '2')
                        @php
                            $tanggal_sebelumnya = strtotime('-1 day', strtotime($periode1));
                            $tanggal_sebelumnya = date('Y-m-d', $tanggal_sebelumnya);
                        @endphp
                        {{ tanggal($tanggal_sebelumnya) }}
                    @endif
                </td>
                <td class="angka">{{ rupiah($uyhd_lalu) }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2" class="kiri">Jumlah s.d
                    Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode2) }}
                    @endif
                </td>
                <td class="angka">{{ rupiah($total + $uyhd_lalu) }}</td>
            </tr>
        </tbody>
    </table>
    @if (isset($tanda_tangan))
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
