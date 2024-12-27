<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ANGKAS PEMDA</title>
    <style>
        #header tr>td {
            font-weight: bold;
            text-align: center;
            font-family: "Open Sans", Helvetica, Arial, sans-serif;
        }

        #tabel_angkas,
        th,
        td {
            border-collapse: collapse;
            font-family: "Open Sans", Helvetica, Arial, sans-serif;
        }

        .angka {
            text-align: right;
            vertical-align: top;
            font-family: "Open Sans", Helvetica, Arial, sans-serif;
        }

        .ttd1 {
            border-left: hidden;
            border-bottom: hidden;
            border-right: hidden;
            font-family: "Open Sans", Helvetica, Arial, sans-serif;
        }

        .ttd2 {
            border-bottom: hidden;
            border-right: hidden;
            font-family: "Open Sans", Helvetica, Arial, sans-serif;
        }
    </style>
</head>

<body>
    <table style="width: 100%;font-size:14px" id="header">
        <tr>
            <td>{{ $header->nm_pemda }}</td>
        </tr>
        <tr>
            <td>ANGGARAN KAS</td>
        </tr>
        <tr>
            <td>{{ strtoupper($nama_angkas->nama) }}</td>
        </tr>
        <tr>
            <td>TAHUN ANGGARAN {{ tahun_anggaran() }}</td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;font-size:12px" id="tabel_angkas" border="1" class="horizontal-scroll">
        <thead>
            <tr>
                <th rowspan="3">Kode<br>Rekening</th>
                <th rowspan="3" colspan="4">Uraian</th>
                <th rowspan="3">Anggaran<br>Tahun Ini<br>(Rp)</th>
                <th colspan="3">Triwulan I (Rp).</th>
                <th colspan="3">Triwulan II (Rp).</th>
                <th colspan="3">Triwulan III (Rp).</th>
                <th colspan="3">Triwulan IV (Rp).</th>
            </tr>
            <tr>
                <th colspan="3">(Rp)</th>
                <th colspan="3">(Rp)</th>
                <th colspan="3">(Rp)</th>
                <th colspan="3">(Rp)</th>
            </tr>
            <tr>
                <th>Januari</th>
                <th>Februari</th>
                <th>Maret</th>
                <th>April</th>
                <th>Mei</th>
                <th>Juni</th>
                <th>Juli</th>
                <th>Agustus</th>
                <th>September</th>
                <th>Oktober</th>
                <th>November</th>
                <th>Desember</th>
            </tr>

        </thead>
        <tbody>
            <tr>
                <td colspan="18" style="background-color:#CCCCCC"><b>ALOKASI PENDAPATAN DAN PENERIMAAN PEMBIAYAAN</b>
                </td>
            </tr>
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
            @endphp
            @foreach ($pendapatan as $rek)
                @if (strlen($rek->giat) == 2)
                    @php
                        $total_ang += $rek->ang;
                        $total_jan += $rek->jan;
                        $total_feb += $rek->feb;
                        $total_mar += $rek->mar;
                        $total_apr += $rek->apr;
                        $total_mei += $rek->mei;
                        $total_jun += $rek->jun;
                        $total_jul += $rek->jul;
                        $total_ags += $rek->ags;
                        $total_sep += $rek->sep;
                        $total_okt += $rek->okt;
                        $total_nov += $rek->nov;
                        $total_des += $rek->des;
                    @endphp
                    <tr>
                        <td style="vertical-align: top"><b>{{ dotrek($rek->giat) }}</b></td>
                        <td colspan="4"><b>{{ $rek->nm_giat }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->ang) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->jan) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->feb) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->mar) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->apr) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->mei) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->jun) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->jul) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->ags) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->sep) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->okt) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->nov) }}</b></td>
                        <td class="angka"><b>{{ rupiah($rek->des) }}</b></td>
                    </tr>
                @else
                    <tr>
                        <td style="vertical-align: top">{{ dotrek($rek->giat) }}</td>
                        <td colspan="4">{{ $rek->nm_giat }}</td>
                        <td class="angka">{{ rupiah($rek->ang) }}</td>
                        <td class="angka">{{ rupiah($rek->jan) }}</td>
                        <td class="angka">{{ rupiah($rek->feb) }}</td>
                        <td class="angka">{{ rupiah($rek->mar) }}</td>
                        <td class="angka">{{ rupiah($rek->apr) }}</td>
                        <td class="angka">{{ rupiah($rek->mei) }}</td>
                        <td class="angka">{{ rupiah($rek->jun) }}</td>
                        <td class="angka">{{ rupiah($rek->jul) }}</td>
                        <td class="angka">{{ rupiah($rek->ags) }}</td>
                        <td class="angka">{{ rupiah($rek->sep) }}</td>
                        <td class="angka">{{ rupiah($rek->okt) }}</td>
                        <td class="angka">{{ rupiah($rek->nov) }}</td>
                        <td class="angka">{{ rupiah($rek->des) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td style="height: 15px"></td>
                <td style="height: 15px" colspan="4"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: left"><b>JUMLAH RENCANA PENDAPATAN DAN<br>PENERIMAAN PEMBIAYAAN
                        PER BULAN</b></td>
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
            </tr>
            <tr>
                <td colspan="5"><b>JUMLAH RENCANA PENDAPATAN DAN<br>PENERIMAAN PEMBIAYAAN PER TRIWULAN</b></td>
                <td class="angka"><b>{{ rupiah($total_ang) }}</b></td>
                <td colspan="3" class="angka"><b>{{ rupiah($total_jan + $total_feb + $total_mar) }}</b></td>
                <td colspan="3" class="angka"><b>{{ rupiah($total_apr + $total_mei + $total_jun) }}</b></td>
                <td colspan="3" class="angka"><b>{{ rupiah($total_jul + $total_ags + $total_sep) }}</b></td>
                <td colspan="3" class="angka"><b>{{ rupiah($total_okt + $total_nov + $total_des) }}</b></td>
            </tr>
            <tr>
                <td style="height: 15px"></td>
                <td style="height: 15px" colspan="4"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
            </tr>
            <tr>
                <td colspan="18" style="background-color:#CCCCCC"><b>ALOKASI BELANJA DAN PENGELUARAN PEMBIAYAAN</b>
                </td>
            </tr>
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
            @endphp
            @foreach ($belanja as $giat)
                @if (strlen($giat->giat) == 2)
                    @php
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
                    @endphp
                    <tr>
                        <td style="vertical-align: top"><b> {{ dotrek($giat->giat) }} </b></td>
                        <td colspan="4"><b> {{ $giat->nm_giat }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->ang) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->jan) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->feb) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->mar) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->apr) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->mei) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->jun) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->jul) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->ags) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->sep) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->okt) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->nov) }} </b></td>
                        <td class="angka"><b> {{ rupiah($giat->des) }} </b></td>
                    </tr>
                @else
                    <tr>
                        <td style="vertical-align: top">{{ dotrek($giat->giat) }}</td>
                        <td colspan="4">{{ $giat->nm_giat }}</td>
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
                    </tr>
                @endif
            @endforeach
            <tr>
                <td style="height: 15px"></td>
                <td style="height: 15px" colspan="4"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
                <td style="height: 15px"></td>
            </tr>
            <tr>
                <td colspan="5"><b>JUMLAH RENCANA BELANJA DAN/ATAU
                        PENGELUARAN PEMBIAYAAN PER BULAN</b></td>
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
            </tr>
            <tr>
                <td colspan="5"><b>JUMLAH RENCANA BELANJA DAN/ATAU
                        PENGELUARAN PEMBIAYAAN PER TRIWULA</b></td>
                <td class="angka"><b>{{ rupiah($total_ang) }}</b></td>
                <td colspan="3" class="angka"><b>{{ rupiah($total_jan + $total_feb + $total_mar) }}</b></td>
                <td colspan="3" class="angka"><b>{{ rupiah($total_apr + $total_mei + $total_jun) }}</b></td>
                <td colspan="3" class="angka"><b>{{ rupiah($total_jul + $total_ags + $total_sep) }}</b></td>
                <td colspan="3" class="angka"><b>{{ rupiah($total_okt + $total_nov + $total_des) }}</b></td>
            </tr>
            @if ($hidden != 'hidden')
                <tr>
                    <td colspan="18" class="ttd1" style="height: 25px"></td>
                </tr>
                <tr>
                    <td colspan="14" class="ttd1"></td>
                    <td style="margin: 2px 0px;text-align: center;border-left:hidden" colspan="4" class="ttd2">
                        Sanggau, {{ tanggal($tanggal) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="14" class="ttd1"></td>
                    <td style="margin: 2px 0px;text-align: center" colspan="4" class="ttd2">
                        Disiapkan oleh,
                    </td>
                </tr>
                <tr>
                    <td colspan="14" class="ttd1"></td>
                    <td style="padding-bottom: 50px;text-align: center" colspan="4" class="ttd2">
                        {{ $ttd2->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td colspan="14" class="ttd1"></td>
                    <td style="text-align: center" colspan="4" class="ttd2"><b><u>{{ $ttd2->nama }}</u></b>
                    </td>
                </tr>
                <tr>
                    <td colspan="14" class="ttd1"></td>
                    <td style="text-align: center" colspan="4" class="ttd2">NIP. {{ $ttd2->nip }}</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>

</html>
