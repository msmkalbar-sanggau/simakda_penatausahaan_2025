<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TRANSFER DANA</title>
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
            <td align="center" style="font-size:16px" width="93%">LEMBAR KONFIRMASI TRANSFER KE DAERAH</td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <table style="width: 100%">
        <tr>
            <td>Telah Terima dari</td>
            <td>:</td>
            <td colspan="3">Direktur Jenderal Perbendaharaan Selaku Kuasa Bendahara Umum Negara</td>
        </tr>
        <tr>
            <td>Melalui KPPN sejumlah Rp.</td>
            <td>:</td>
            <td colspan="3">{{ rupiah($total_transfer) }}</td>
        </tr>
        <tr>
            <td>Terbilang</td>
            <td>:</td>
            <td colspan="3">{{ terbilang($total_transfer) }}</td>
        </tr>
        <tr>
            <td>Untuk Keperluan</td>
            <td>:</td>
            <td colspan="3">Pencairan Anggaran Transfer ke Daerah TA {{ tahun_anggaran() }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td style="width: 10%">Bulan</td>
            <td>:</td>
            <td>{{ bulan($bulan) }} {{ tahun_anggaran() }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td style="width: 10%">Daerah</td>
            <td>:</td>
            <td>Kabupaten Sanggau</td>
        </tr>
        <tr>
            <td>Dengan Rincian</td>
            <td>:</td>
            <td colspan="3"></td>
        </tr>
    </table>

    <table style="width: 100%" border="1">
        <thead>
            <tr>
                <th><b>JENIS ANGGARAN TRANSFER KE DAERAH</b></th>
                <th><b>JUMLAH KOTOR</b></th>
                <th><b>POT.</b></th>
                <th><b>JUMLAH BERSIH</b></th>
                <th><b>Diterima Tanggal</b></th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($data_transfer as $data)
                @php
                    $total += $data->rupiah;
                @endphp
                @if ($data->spasi == '1')
                    <tr>
                        <td>{{ $data->nama }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @elseif ($data->spasi == '2')
                    <tr>
                        <td>&nbsp;&nbsp;{{ $data->nama }},
                            @if ($bulan <= 3)
                                Triwulan 1 Tahun
                            @elseif ($bulan <= 6)
                                Triwulan 2 Tahun
                            @elseif ($bulan <= 9)
                                Triwulan 3 Tahun
                            @else
                                Triwulan 4 Tahun
                            @endif
                            {{ tahun_anggaran() }}
                        </td>
                        <td class="angka">{{ rupiah($data->rupiah) }}</td>
                        <td></td>
                        <td class="angka">{{ rupiah($data->rupiah) }}</td>
                        <td class="angka">{{ $data->tgl_kas == '1900-01-01' ? '' : tanggal($data->tgl_kas) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td>JUMLAH TOTAL PENERIMAAN TRANSFER</td>
                <td class="angka">{{ rupiah($total) }}</td>
                <td></td>
                <td class="angka">{{ rupiah($total) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%">
        <tr>
            <td colspan="6">Dana tersebut telah diterima pada Rekening Kas Daerah sebagai berikut:</td>
        </tr>
        <tr>
            <td></td>
            <td>- Rekening Kas Daerah</td>
            <td><b>&#8594;</b></td>
            <td>Nomor Rekening</td>
            <td>:</td>
            <td>1001002201</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Nama Rekening</td>
            <td>:</td>
            <td>Rekening Kas Umum Daerah Prov. Kalbar</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Nama Bank</td>
            <td>:</td>
            <td>Bank Kalbar Cabang Utama Pontianak</td>
        </tr>
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
