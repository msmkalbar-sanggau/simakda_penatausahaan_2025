<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KAS HARIAN KASDA</title>
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
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75"
                    height="100" />
            </td>
            <td align="left" style="font-size:14px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px">
                <strong>
                    LAPORAN POSISI KAS HARIAN
                </strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px">
                <strong>
                    TAHUN ANGGARAN {{ tahun_anggaran() }}
                </strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <hr>

    <table style="width: 100%" align="center">
        <tr>
            <td style="width: 10%">Hari</td>
            <td>: {{ hari(\Carbon\Carbon::parse($tanggal)->format('D')) }}</td>
        </tr>
        <tr>
            <td style="width: 10%">Tanggal</td>
            <td>: {{ tanggal($tanggal) }}</td>
        </tr>
    </table>

    <table style="width: 100%;margin-top:20px" border="1" id="pilihan1">
        <thead>
            <tr>
                <th rowspan="2"><b>No.</b></th>
                <th colspan="3"><b>No. Bukti Transaksi</b></th>
                <th rowspan="2"><b>Uraian</b></th>
                <th rowspan="2"><b>Penerimaan</b></th>
                <th rowspan="2"><b>Pengeluaran</b></th>
            </tr>
            <tr>
                <th>SP2D</th>
                <th>STS</th>
                <th>Lain-lain</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_masuk = 0;
                $total_keluar = 0;
            @endphp
            @foreach ($data_kasda as $data)
                @php
                    $total_masuk += $data->masuk;
                    $total_keluar += $data->keluar;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($data->kode == 1)
                            {{ $data->nomor }}
                        @endif
                    </td>
                    <td>
                        @if ($data->kode == 2)
                            {{ $data->nomor }}
                        @endif
                    </td>
                    <td>
                        @if ($data->kode == 1 || $data->kode == 2)
                        @else
                            {{ $data->nomor }}
                        @endif
                    </td>
                    <td>
                        @if ($data->jenis == 'sp2d')
                            @if (($data->jns_spp == '6' && $data->jns_beban == '6') || $data->jns_spp == '5')
                                {{ harian_kasda_pihak_ketiga($data->nomor) }}
                            @else
                                {{ harian_kasda_bukan_ketiga($data->nomor) }}
                            @endif
                        @else
                            {{ $data->uraian }}
                        @endif
                    </td>
                    <td class="angka">
                        {{ rupiah($data->masuk) }}
                    </td>
                    <td class="angka">
                        {{ rupiah($data->keluar) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="angka" colspan="5" style="border-left:hidden;border-bottom:hidden">Jumlah</td>
                <td class="angka">{{ rupiah($total_masuk) }}</td>
                <td class="angka">{{ rupiah($total_keluar) }}</td>
            </tr>
            <tr>
                <td class="angka" colspan="5" style="border-top:hidden;border-left:hidden;">Perubahan posisi
                    kas hari ini</td>
                <td class="angka">{{ rupiah($kasda_lalu->masuk + $total_masuk) }}</td>
                <td class="angka">{{ rupiah($kasda_lalu->keluar + $total_keluar) }}</td>
            </tr>
            <tr>
                <td class="angka" colspan="5" style="border-top:hidden;border-left:hidden;">Posisi Kas (H-1)
                </td>
                <td style="border-right: hidden"></td>
                <td class="angka">{{ rupiah($kasda_lalu->masuk - $kasda_lalu->keluar) }}</td>
            </tr>
            <tr>
                <td class="angka" colspan="5" style="border-top:hidden;border-left:hidden;border-bottom:hidden;">
                    Posisi Kas (H)
                </td>
                <td style="border-right: hidden"></td>
                <td class="angka">
                    {{ rupiah($kasda_lalu->masuk - $kasda_lalu->keluar + ($total_masuk - $total_keluar)) }}
                </td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%">
        <tr>
            <td>Rekapitulasi posisi kas di BUD</td>
        </tr>
        <tr>
            <td>Saldo Bank 1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Rp
                {{ rupiah($kasda_lalu->masuk - $kasda_lalu->keluar + ($total_masuk - $total_keluar)) }}
            </td>
        </tr>
        <tr>
            <td>Saldo Bank 2 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: -</td>
        </tr>
        <tr>
            <td>Total Saldo Kas &nbsp;&nbsp;&nbsp;: Rp
                {{ rupiah($kasda_lalu->masuk - $kasda_lalu->keluar + ($total_masuk - $total_keluar)) }}
            </td>
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
