<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DAFTAR PENGELUARAN</title>
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
                    DAFTAR PENGELUARAN
                    @if ($beban == '0')
                        GAJI
                    @elseif ($beban == '1')
                        LS
                    @elseif ($beban == '2')
                        UP
                    @elseif ($beban == '3')
                        TU
                    @elseif ($beban == '4')
                        GU
                    @endif
                    TAHUN ANGGARAN {{ tahun_anggaran() }}
                </strong>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <tr>
        <td><b>BULAN : {{ Str::upper(bulan($bulan)) }}</b></td>
    </tr>
    <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
        <thead>
            <tr>
                <th>TANGGAL PENCAIRAN</th>
                <th>NOMOR KAS</th>
                <th>NOMOR SP2D TANGGAL SP2D</th>
                <th>URAIAN</th>
                <th>KODE REKENING</th>
                <th>PENERIMAAN</th>
                <th>PENGELUARAN</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
            </tr>
        </thead>
        <tbody>
            {{-- @php
                $total = 0;
            @endphp --}}
            @foreach ($data_pengeluaran as $data)
                {{-- @php
                    $total += $data->total;
                @endphp --}}
                @if ($data->urut == '1')
                    <tr>
                        <td style="text-align: center">{{ $data->tgl_kas_bud }}</td>
                        <td style="text-align: center">{{ $data->no_kas_bud }}</td>
                        <td style="text-align: center">{{ $data->no_sp2d }} <br> {{ $data->tgl_sp2d }}</td>
                        <td>{{ empty(nama_bend($data->kd_skpd)->nama) ? 'Belum Ada data Bendahara' : nama_bend($data->kd_skpd)->nama }},
                            &nbsp; {{ $data->nm_skpd }}
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td style="border-top: hidden"></td>
                        <td style="border-top: hidden"></td>
                        <td style="border-top: hidden"></td>
                        <td style="border-top: hidden">{{ $data->nm_rek6 }}</td>
                        <td style="border-top: hidden;text-align:center">
                            {{ empty($data->kd_sub_kegiatan) ? '' : $data->kd_sub_kegiatan . dotrek($data->kd_rek6) }}
                        </td>
                        <td class="angka" style="border-top: hidden;">{{ rupiah(0) }}</td>
                        <td class="angka" style="border-top: hidden;">{{ rupiah($data->nilai) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td colspan="5" style="text-align: right"><b>JUMLAH</b></td>
                <td class="angka"><b>{{ rupiah(0) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_pengeluaran) }}</b></td>
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
