<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ANGKAS SUB KEGIATAN</title>
    <style>
        #header tr>td {
            font-weight: bold;
            text-align: center
        }

        #tabel_angkas,
        th,
        td {
            border-collapse: collapse;
        }

        .angka {
            text-align: right
        }
    </style>
</head>

<body>
    <table style="width: 100%" id="header">
        <tr>
            <td>ANGGARAN KAS SUB KEGIATAN</td>
        </tr>
        <tr>
            <td>{{ Str::upper($nama_angkas->nama) }}</td>
        </tr>
        <tr>
            <td>{{ $nama_skpd->nm_skpd }}</td>
        </tr>
        <tr>
            <td>TAHUN {{ tahun_anggaran() }}</td>
        </tr>
    </table>

    <table style="width: 100%" id="tabel_angkas" border="1">
        <thead>
            <tr>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Uraian</th>
                <th rowspan="2">Anggaran</th>
                <th colspan="3">Triwulan I (Rp).</th>
                <th colspan="3">Triwulan II (Rp).</th>
                <th colspan="3">Triwulan III (Rp).</th>
                <th colspan="3">Triwulan IV (Rp).</th>
                <th rowspan="2">Jumlah</th>
            </tr>
            <tr>
                <th>Jan</th>
                <th>Feb</th>
                <th>Mar</th>
                <th>Apr</th>
                <th>Mei</th>
                <th>Jun</th>
                <th>Jul</th>
                <th>Ags</th>
                <th>Sep</th>
                <th>Okt</th>
                <th>Nov</th>
                <th>Des</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_ang = 0;
                $total_jan = 0;
                $total_feb = 0;
                $total_mar = 0;
                $total_apr = 0;
                $total_mei = 0;
                $total_jun = 0;
                $total_jul = 0;
                $total_ags = 0;
                $total_sep = 0;
                $total_okt = 0;
                $total_nov = 0;
                $total_des = 0;
                $total_jumlah = 0;
            @endphp
            @foreach ($data_giat as $giat)
                @php
                    if (strlen($giat->giat) == 17) {
                        $total_ang += $giat->ang;
                        $total_jan += $giat->jan;
                        $total_feb += $giat->feb;
                        $total_mar += $giat->mar;
                        $total_apr += $giat->apr;
                        $total_mei += $giat->mei;
                        $total_jun += $giat->jun;
                        $total_jul += $giat->jul;
                        $total_ags += $giat->ags;
                        $total_sep += $giat->sep;
                        $total_okt += $giat->okt;
                        $total_nov += $giat->nov;
                        $total_des += $giat->des;
                        $total_jumlah = $total_jan + $total_feb + $total_mar + $total_apr + $total_mei + $total_jun + $total_jul + $total_ags + $total_sep + $total_okt + $total_nov + $total_des;
                    }
                @endphp
                <tr>
                    <td>{{ $giat->giat }}</td>
                    <td>{{ $giat->nm_giat }}</td>
                    <td class="angka">{{ rupiah($giat->ang) }}</td>
                    <td class="angka">{{ rupiah($giat->jan) }}</td>
                    <td class="angka">{{ rupiah($giat->feb) }}</td>
                    <td class="angka">{{ rupiah($giat->mar) }}</td>
                    <td class="angka">{{ rupiah($giat->apr) }}</td>
                    <td class="angka">{{ rupiah($giat->mei) }}</td>
                    <td class="angka">{{ rupiah($giat->jun) }}</td>
                    <td class="angka">{{ rupiah($giat->jul) }}</td>
                    <td class="angka">{{ rupiah($giat->ags) }}</td>
                    <td class="angka">{{ rupiah($giat->sep) }}</td>
                    <td class="angka">{{ rupiah($giat->okt) }}</td>
                    <td class="angka">{{ rupiah($giat->nov) }}</td>
                    <td class="angka">{{ rupiah($giat->des) }}</td>
                    <td class="angka">
                        {{ rupiah($giat->jan + $giat->feb + $giat->mar + $giat->apr + $giat->mei + $giat->jun + $giat->jul + $giat->ags + $giat->sep + $giat->okt + $giat->nov + $giat->des) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: center"><b>Total</b></td>
                <td class="angka"><b>{{ rupiah($total_ang) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_jan) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_feb) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_mar) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_apr) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_mei) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_jun) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_jul) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_ags) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_sep) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_okt) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_nov) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_des) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_jumlah) }}</b></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center"><b>Total Triwulan</b></td>
                <td class="angka"><b>{{ rupiah(0) }}</b></td>
                <td colspan="3" style="text-align: center"><b>{{ rupiah($total_jan + $total_feb + $total_mar) }}</b>
                </td>
                <td colspan="3" style="text-align: center"><b>{{ rupiah($total_apr + $total_mei + $total_jun) }}</b>
                </td>
                <td colspan="3" style="text-align: center"><b>{{ rupiah($total_jul + $total_ags + $total_sep) }}</b>
                </td>
                <td colspan="3" style="text-align: center"><b>{{ rupiah($total_okt + $total_nov + $total_des) }}</b>
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="12" style="border-left:hidden;border-bottom:hidden;border-right:hidden"></td>
                <td colspan="4" style="border-left:hidden;border-bottom:hidden;border-right:hidden">
                    @if ($hidden != 'hidden')
                        <div style="padding-top:20px">
                            <table class="table" style="width:100%">
                                <tr>
                                    <td style="margin: 2px 0px;text-align: center">
                                        Kabupaten Sanggau, {{ tanggal($tanggal) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 50px;text-align: center">
                                        {{ $ttd1->jabatan }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center"><b><u>{{ $ttd1->nama }}</u></b></td>
                                </tr>
                                <tr>
                                    <td style="text-align: center">NIP. {{ $ttd1->nip }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    {{-- @if ($hidden != 'hidden')
        <div style="padding-top:20px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center">
                        Pontianak, {{ tanggal($tanggal) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center">
                        {{ $ttd1->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center"><b><u>{{ $ttd1->nama }}</u></b></td>
                </tr>
                <tr>
                    <td style="text-align: center">NIP. {{ $ttd1->nip }}</td>
                </tr>
            </table>
        </div>
    @endif --}}
</body>

</html>
