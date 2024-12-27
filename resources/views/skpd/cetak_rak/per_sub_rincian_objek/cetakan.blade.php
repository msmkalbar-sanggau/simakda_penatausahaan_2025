<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RAK BELANJA</title>
    <style>
        #header tr>td {
            font-weight: bold;
            text-align: center;
            font-family: "Open Sans", Helvetica, Arial, sans-serif;
        }

        #sub_header tr>td {
            font-weight: normal;
            text-align: left;
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
            font-family: "Open Sans", Helvetica, Arial, sans-serif;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <table style="width: 100%;text-align:center;font-size:12px" id="tabel_angkas" border="1" cellspacing='0'
        cellpadding='2'>
        <tr>
            <td><b>RENCANA ANGGARAN KAS<br>
                    SATUAN KERJA PERANGKAT DAERAH</b></td>
            <td rowspan="2"><b>Formulir<br>RAK

                    @if (substr($sub_header1->subkegiatan, 5, 10) == '00.0.00.04')
                        Pendapatan
                    @elseif(substr($sub_header1->subkegiatan, 5, 10) == '00.0.00.61')
                        Penerimaan Pembiayaan
                    @elseif(substr($sub_header1->subkegiatan, 5, 10) == '00.0.00.62')
                        Pengeluaran Pembiayaan
                    @else
                        Belanja
                    @endif
                </b></td>
        </tr>
        <tr>
            <td style="font-weight: normal">Pemerintah Kabupaten Sanggau Tahun Anggaran {{ tahun_anggaran() }}
            </td>
        </tr>
    </table>

    <table style="width: 100%;padding-top:20px;font-size:10px" id="sub_header">
        <tr>
            <td>Urusan</td>
            <td>: {{ $sub_header->urusan }} {{ $sub_header->nmurusan }}</td>
        </tr>
        <tr>
            <td>Bidang</td>
            <td>: {{ $sub_header->bidang }} {{ $sub_header->nmbidang }}</td>
        </tr>
        <tr>
            <td>Unit Organisasi</td>
            <td>: {{ $sub_header->org }}.0000 {{ $sub_header->nmorg }}</td>
        </tr>
        <tr>
            <td>Sub Unit Organisasi</td>
            <td>: {{ $sub_header->unit }} {{ $sub_header->nmunit }}</td>
        </tr>
        @if (substr($sub_header1->subkegiatan, 5, 10) != '00.0.00.04' &&
                substr($sub_header1->subkegiatan, 5, 10) != '00.0.00.61' &&
                substr($sub_header1->subkegiatan, 5, 10) != '00.0.00.62')
            <tr>
                <td>Program</td>
                <td>: {{ $sub_header1->program }} {{ $sub_header1->nmprogram }}</td>
            </tr>
            <tr>
                <td>Kegiatan</td>
                <td>: {{ $sub_header1->kegiatan }} {{ ucwords($sub_header1->nmkegiatan) }}</td>
            </tr>
            <tr>
                <td>Sub Kegiatan</td>
                <td>: {{ $sub_header1->subkegiatan }}{{ ucwords($sub_header1->nmsubkegiatan) }}</td>
            </tr>
        @endif

        <tr>
            <td style="vertical-align:top">Nilai Anggaran</td>
            <td style="vertical-align:top">: {{ rupiah($sub_header1->ang) }}<br>
                &nbsp;&nbsp;({{ ucwords(terbilang($sub_header1->ang)) }})</td>
        </tr>
    </table>

    <table style="width: 100%;font-size:9px" id="tabel_angkas" border="1" cellspacing='0' cellpadding='2'>
        <thead>
            <tr>
                <th rowspan="2">No. </th>
                <th rowspan="2">Rekening</th>
                <th rowspan="2">Jumlah Anggaran</th>
                <th colspan="16">Jumlah Kebutuhan Dana</th>
            </tr>
            <tr>
                <th>Januari</th>
                <th>Februari</th>
                <th>Maret</th>
                <th style="color:#44c3c7">Triwulan I</th>
                <th>April</th>
                <th>Mei</th>
                <th>Juni</th>
                <th style="color:#44c3c7">Triwulan II</th>
                <th>Juli</th>
                <th>Agustus</th>
                <th>September</th>
                <th style="color:#44c3c7">Triwulan III</th>
                <th>Oktober</th>
                <th>November</th>
                <th>Desember</th>
                <th style="color:#44c3c7">Triwulan IV</th>
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
                $no = 0;
            @endphp
            @foreach ($data_giat as $giat)
                @php
                    $total_ang += anggaran_rekening_objek($kd_skpd, $kd_sub_kegiatan, $giat->kd_rek6, $jenis_anggaran);
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
                    <td style="text-align:center">{{ ++$no }}</td>
                    <td>{{ $giat->kd_rek6 }} <br>{{ $giat->nm_rek }}</td>
                    <td class="angka">
                        {{ rupiah(anggaran_rekening_objek($kd_skpd, $kd_sub_kegiatan, $giat->kd_rek6, $jenis_anggaran)) }}
                    </td>
                    <td class="angka">{{ rupiah($giat->jan) }}</td>
                    <td class="angka">{{ rupiah($giat->feb) }}</td>
                    <td class="angka">{{ rupiah($giat->mar) }}</td>
                    <td class="angka" style="color:#44c3c7">{{ rupiah($giat->jan + $giat->feb + $giat->mar) }}</td>
                    <td class="angka">{{ rupiah($giat->apr) }}</td>
                    <td class="angka">{{ rupiah($giat->mei) }}</td>
                    <td class="angka">{{ rupiah($giat->jun) }}</td>
                    <td class="angka" style="color:#44c3c7">{{ rupiah($giat->apr + $giat->mei + $giat->jun) }}</td>
                    <td class="angka">{{ rupiah($giat->jul) }}</td>
                    <td class="angka">{{ rupiah($giat->ags) }}</td>
                    <td class="angka">{{ rupiah($giat->sep) }}</td>
                    <td class="angka" style="color:#44c3c7">{{ rupiah($giat->jul + $giat->ags + $giat->sep) }}</td>
                    <td class="angka">{{ rupiah($giat->okt) }}</td>
                    <td class="angka">{{ rupiah($giat->nov) }}</td>
                    <td class="angka">{{ rupiah($giat->des) }}</td>
                    <td class="angka" style="color:#44c3c7">{{ rupiah($giat->okt + $giat->nov + $giat->des) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="text-align: center" colspan="2"><b>Total</b></td>
                <td class="angka"><b>{{ rupiah($total_ang) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_jan) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_feb) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_mar) }}</b></td>
                <td class="angka" style="color:#44c3c7"><b>{{ rupiah($total_jan + $total_feb + $total_mar) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_apr) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_mei) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_jun) }}</b></td>
                <td class="angka" style="color:#44c3c7"><b>{{ rupiah($total_apr + $total_mei + $total_jun) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_jul) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_ags) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_sep) }}</b></td>
                <td class="angka" style="color:#44c3c7"><b>{{ rupiah($total_jul + $total_ags + $total_sep) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_okt) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_nov) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_des) }}</b></td>
                <td class="angka" style="color:#44c3c7"><b>{{ rupiah($total_okt + $total_nov + $total_des) }}</b></td>
            </tr>
            {{-- <tr>
                <td colspan="14" style="border-left:hidden;border-bottom:hidden;border-right:hidden"></td>
                <td colspan="4" style="border-left:hidden;border-bottom:hidden;border-right:hidden">
                    @if ($hidden != 'hidden')
                        @if ($hidden != 'hidden')
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
                        @endif
                    @endif
                </td>
            </tr> --}}
        </tbody>
    </table>

    @if ($hidden != 'hidden')
        <div style="padding-top:20px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="width: 60%"></td>
                    <td style="margin: 2px 0px;text-align: center">
                        Kabupaten Sanggau, {{ tanggal($tanggal) }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 60%"></td>
                    <td style="padding-bottom: 50px;text-align: center">
                        {{ $ttd1->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 60%"></td>
                    <td style="text-align: center"><b><u>{{ $ttd1->nama }}</u></b></td>
                </tr>
                <tr>
                    <td style="width: 60%"></td>
                    <td style="text-align: center">NIP. {{ $ttd1->nip }}</td>
                </tr>
            </table>
        </div>
    @endif
</body>

</html>
