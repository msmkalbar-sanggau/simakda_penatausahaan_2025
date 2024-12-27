<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REALISASI PENDAPATAN</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #rincian>thead>tr>th {
            background-color: #CCCCCC;
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
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75"
                    height="100" />
            </td>
            <td align="left" style="font-size:14px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>{{ $skpd->nm_skpd }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>LAPORAN REALISASI ANGGARAN PENDAPATAN DAERAH</b></td>
        </tr>
        <tr>
            <td style="text-align: center"><b>TAHUN ANGGARAN {{ tahun_anggaran() }}</b></td>
        </tr>
        <tr>
            <td style="text-align: center"><b>REALISASI S/D {{ Str::upper(bulan($periode)) }}
                    {{ tahun_anggaran() }}</b></td>
        </tr>
    </table>
    <table style="width: 100%" border="1" id="rincian" cellpadding="{{ $spasi }}">
        <thead>
            <tr>
                <th rowspan="2">KD REK</th>
                <th rowspan="2">URAIAN</th>
                <th rowspan="2">ANGGARAN</th>
                <th colspan="3">REALISASI (Rp.)</th>
                <th rowspan="2">SISA</th>
                <th rowspan="2">PERSENTASE</th>
                <th rowspan="2">DASAR HUKUM</th>
            </tr>
            <tr>
                <th>s/d Bulan Lalu</th>
                <th>Bulan Ini</th>
                <th>s/d Bulan Ini</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6=(4+5)</th>
                <th>7</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @php
                $sisa = 0;
                $persen = 0;
                $total_anggaran = 0;
                $total_bulan_ini = 0;
                $total_bulan_lalu = 0;
                $total_sd_bulan_ini = 0;
                $total_sisa = 0;
                $total_persen = 0;
            @endphp
            @foreach ($daftar_realisasi as $data)
                @php
                    $sisa = $data->anggaran - $data->sd_bulan_ini;
                    $sisa = $sisa < 0 ? $sisa * -1 : $sisa;
                    $persen = empty($data->anggaran) || $data->anggaran == 0 ? 0 : ($data->sd_bulan_ini / $data->anggaran) * 100;
                @endphp
                @if (Str::length($data->urut2) > 12)
                    <tr>
                        <td>{{ dotrek($data->kd_rek) }}.{{ $data->kd_skpd }}</td>
                        <td>{{ $data->nm_skpd }}</td>
                        <td class="angka">{{ rupiah($data->anggaran) }}</td>
                        <td class="angka">{{ rupiah($data->bulan_lalu) }}</td>
                        <td class="angka">{{ rupiah($data->bulan_ini) }}</td>
                        <td class="angka">{{ rupiah($data->sd_bulan_ini) }}</td>
                        <td class="angka">{{ rupiah($sisa) }}</td>
                        <td class="angka">{{ rupiah($persen) }}</td>
                        <td></td>
                    </tr>
                @elseif (Str::length($data->urut2) <= 12 && Str::length($data->urut2) > 1)
                    <tr>
                        <td><b>{{ dotrek($data->kd_rek) }}</b></td>
                        <td><b>{{ $data->nm_rek }}</b></td>
                        <td class="angka"><b>{{ rupiah($data->anggaran) }}</b></td>
                        <td class="angka"><b>{{ rupiah($data->bulan_lalu) }}</b></td>
                        <td class="angka"><b>{{ rupiah($data->bulan_ini) }}</b></td>
                        <td class="angka"><b>{{ rupiah($data->sd_bulan_ini) }}</b></td>
                        <td class="angka"><b>{{ rupiah($sisa) }}</b></td>
                        <td class="angka"><b>{{ rupiah($persen) }}</b></td>
                        <td></td>
                    </tr>
                @elseif (Str::length($data->urut2) == 1)
                    @php
                        $total_anggaran += $data->anggaran;
                        $total_bulan_ini += $data->bulan_ini;
                        $total_bulan_lalu += $data->bulan_lalu;
                        $total_sd_bulan_ini += $data->sd_bulan_ini;
                        $total_sisa = $total_anggaran - $total_sd_bulan_ini;
                        $total_persen = ($total_sd_bulan_ini / $total_anggaran) * 100;
                    @endphp
                    <tr>
                        <td><b>{{ dotrek($data->kd_rek) }}</b></td>
                        <td><b>{{ $data->nm_rek }}</b></td>
                        <td class="angka"><b>{{ rupiah($data->anggaran) }}</b></td>
                        <td class="angka"><b>{{ rupiah($data->bulan_lalu) }}</b></td>
                        <td class="angka"><b>{{ rupiah($data->bulan_ini) }}</b></td>
                        <td class="angka"><b>{{ rupiah($data->sd_bulan_ini) }}</b></td>
                        <td class="angka"><b>{{ rupiah($sisa) }}</b></td>
                        <td class="angka"><b>{{ rupiah($persen) }}</b></td>
                        <td></td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td colspan="2" style="text-align: center"><b>JUMLAH PENDAPATAN</b></td>
                <td class="angka"><b>{{ rupiah($total_anggaran) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_bulan_lalu) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_bulan_ini) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_sd_bulan_ini) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_sisa) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_persen) }}</b></td>
                <td><b></b></td>
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
