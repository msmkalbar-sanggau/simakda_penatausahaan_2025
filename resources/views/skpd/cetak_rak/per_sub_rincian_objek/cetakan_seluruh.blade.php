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

            {{-- @foreach ($anggaran as $ang)
                @php
                    $sub_kegiatan       = $ang->kd_sub_kegiatan;
                    $nm_sub_kegiatan    = $ang->nm_sub_kegiatan;
                    $kd_rek             = $ang->kd_rek;
                    $nm_rek             = $ang->nm_rek;
                    $anggaran           = $ang->anggaran;
                    $total_ang          += $ang->anggaran;
                @endphp --}}


            @foreach ($angkas as $rak)
                @php
                    if (strlen($rak->urut) > 37) {
                        $total_jan += $rak->jan;
                        $total_feb += $rak->feb;
                        $total_mar += $rak->mar;
                        $total_apr += $rak->apr;
                        $total_mei += $rak->mei;
                        $total_jun += $rak->jun;
                        $total_jul += $rak->jul;
                        $total_ags += $rak->ags;
                        $total_sep += $rak->sep;
                        $total_okt += $rak->okt;
                        $total_nov += $rak->nov;
                        $total_des += $rak->des;
                        $total_ang += anggaran_rekening_objek($rak->kd_skpd, $rak->kd_sub_kegiatan, $rak->kd_rek, $jenis_anggaran);
                        $anggaran = anggaran_rekening_objek($rak->kd_skpd, $rak->kd_sub_kegiatan, $rak->kd_rek, $jenis_anggaran);
                    } else {
                        $anggaran = anggaran_subkegiatan($rak->kd_skpd, $rak->kd_sub_kegiatan, $jenis_anggaran);
                    }
                @endphp
                @if (strlen($rak->urut) == 37)
                    <tr>
                        <td bgcolor="#d3d3d3"style="text-align:center"><b>{{ ++$no }}</b></td>
                        <td bgcolor="#d3d3d3"><b>{{ $rak->kd_rek }} <br>{{ $rak->nm_rek }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>
                                {{ rupiah($anggaran) }}
                            </b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->jan) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->feb) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->mar) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka" style="color:#44c3c7">
                            <b>{{ rupiah($rak->jan + $rak->feb + $rak->mar) }}</b>
                        </td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->apr) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->mei) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->jun) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka" style="color:#44c3c7">
                            <b>{{ rupiah($rak->apr + $rak->mei + $rak->jun) }}</b>
                        </td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->jul) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->ags) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->sep) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka" style="color:#44c3c7">
                            <b>{{ rupiah($rak->jul + $rak->ags + $rak->sep) }}</b>
                        </td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->okt) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->nov) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($rak->des) }}</b></td>
                        <td bgcolor="#d3d3d3" class="angka" style="color:#44c3c7">
                            <b>{{ rupiah($rak->okt + $rak->nov + $rak->des) }}</b>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td style="text-align:center">{{ ++$no }}</td>
                        <td>{{ $rak->kd_rek }} <br>{{ $rak->nm_rek }}</td>
                        <td class="angka">
                            {{ rupiah($anggaran) }}
                        </td>
                        <td class="angka">{{ rupiah($rak->jan) }}</td>
                        <td class="angka">{{ rupiah($rak->feb) }}</td>
                        <td class="angka">{{ rupiah($rak->mar) }}</td>
                        <td class="angka" style="color:#44c3c7">{{ rupiah($rak->jan + $rak->feb + $rak->mar) }}</td>
                        <td class="angka">{{ rupiah($rak->apr) }}</td>
                        <td class="angka">{{ rupiah($rak->mei) }}</td>
                        <td class="angka">{{ rupiah($rak->jun) }}</td>
                        <td class="angka" style="color:#44c3c7">{{ rupiah($rak->apr + $rak->mei + $rak->jun) }}</td>
                        <td class="angka">{{ rupiah($rak->jul) }}</td>
                        <td class="angka">{{ rupiah($rak->ags) }}</td>
                        <td class="angka">{{ rupiah($rak->sep) }}</td>
                        <td class="angka" style="color:#44c3c7">{{ rupiah($rak->jul + $rak->ags + $rak->sep) }}</td>
                        <td class="angka">{{ rupiah($rak->okt) }}</td>
                        <td class="angka">{{ rupiah($rak->nov) }}</td>
                        <td class="angka">{{ rupiah($rak->des) }}</td>
                        <td class="angka" style="color:#44c3c7">{{ rupiah($rak->okt + $rak->nov + $rak->des) }}</td>
                    </tr>
                @endif
            @endforeach
            {{-- @endforeach --}}


            <tr>
                <td bgcolor="#d3d3d3" style="text-align: center" colspan="2"><b>Total</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_ang) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_jan) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_feb) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_mar) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka" style="color:#44c3c7">
                    <b>{{ rupiah($total_jan + $total_feb + $total_mar) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_apr) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_mei) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_jun) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka" style="color:#44c3c7">
                    <b>{{ rupiah($total_apr + $total_mei + $total_jun) }}</b>
                </td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_jul) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_ags) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_sep) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka" style="color:#44c3c7">
                    <b>{{ rupiah($total_jul + $total_ags + $total_sep) }}</b>
                </td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_okt) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_nov) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka"><b>{{ rupiah($total_des) }}</b></td>
                <td bgcolor="#d3d3d3" class="angka" style="color:#44c3c7">
                    <b>{{ rupiah($total_okt + $total_nov + $total_des) }}</b>
                </td>
            </tr>
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
