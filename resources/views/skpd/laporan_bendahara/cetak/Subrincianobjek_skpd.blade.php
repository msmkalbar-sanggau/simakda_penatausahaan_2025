<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sub Rincian SKPD</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #rincian>tbody>tr>td {
            vertical-align: top
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
    </style>
</head>

<body onload="window.print()">
    {{-- <body> --}}
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
            <td align="left" style="font-size:14px"><strong>SKPD {{ $skpd->nm_skpd }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    {{-- <hr> --}}

    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>BUKU PEMBANTU SUB RINCIAN OBYEK BELANJA</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE
                    {{ \Carbon\Carbon::parse($tanggal1)->locale('id')->isoFormat('DD MMMM Y') }} s/td
                    {{ \Carbon\Carbon::parse($tanggal2)->locale('id')->isoFormat('DD MMMM Y') }}</b></td>
        </tr>
    </table>

    <TABLE width="100%" style="font-size:12px;">
        @php
            $i = 0;
            $jumls = 0;
            $jumup = 0;
            $jumgu = 0;
            $jml = 0;
            $nos = 0;
            $jumlkeluar = 0;
        @endphp

        {{-- 1 --}}
        @foreach ($headers as $header)
            <TABLE width="100%" style="font-size:12px;">

                <TR>
                    <TD align="left" width="25%">Sub Kegiatan </TD>
                    <TD align="left" width="75%">: {{ $header->kd_sub_kegiatan }} -
                        {{ cari_nama($header->kd_sub_kegiatan, 'ms_sub_kegiatan', 'kd_sub_kegiatan', 'nm_sub_kegiatan') }}
                    </TD>
                </TR>
                <TR>
                    <TD align="left" width="25%">Rekening </TD>
                    <TD align="left" width="75%">: {{ $header->kd_rek6 }} -
                        {{ cari_nama($header->kd_rek6, 'ms_rek6', 'kd_rek6', 'nm_rek6') }}</TD>
                </TR>
            </TABLE>
            <TABLE style="border-collapse:collapse;font-size:14px" border="1" cellspacing="' . $spasi . '"
                cellpadding={{ $enter }} width="100%">
                <THEAD>
                    <TR>
                        <TD width="40%" rowspan="2" colspan="2" align="center"><b>Nomor dan Tanggal BKU</b>
                        </TD>
                        <TD width="60%" colspan="4" align="center"><b>Pengeluaran (Rp)</b></TD>
                    </TR>
                    <TR>
                        <TD width="15%" align="center"><b>LS</b></TD>
                        <TD width="15%" align="center"><b>UP/GU</b></TD>
                        <TD width="15%" align="center"><b>TU</b></TD>
                        <TD width="15%" align="center"><b>JUMLAH</b></TD>
                    </TR>
                </THEAD>
                @php
                    $jumls = 0;
                    $jumup = 0;
                    $jumgu = 0;
                    $jml = 0;
                @endphp
                @foreach ($rincian as $data)
                    @php
                        $no_bukti = $data->no_bukti;
                        $kd_rek = $data->kd_rek6;
                        $kd_sub_kegiatan = $data->kd_sub_kegiatan;
                        $uraian = $data->ket;
                        $no_sp2d = $data->no_sp2d;
                        $ls = $data->ls;
                        $up = $data->up;
                        $gu = $data->gu;
                        $tgl_bukti = $data->tgl_bukti;
                        $nos = $nos + 1;
                        $jumlkeluar = $jumlkeluar + $ls + $up + $gu;
                        $sisa = $jumlkeluar;
                    @endphp

                    @if ($header->kd_sub_kegiatan . '.' . $header->kd_rek6 == $kd_sub_kegiatan . '.' . $kd_rek)
                        <tr>
                            <td style="border-bottom:hidden;border-right:hidden;" align="left">
                                <b>&nbsp;{{ $no_bukti }}</b> </td>
                            <td style="border-bottom:hidden;border-left:hidden;" align="right">
                                {{ tanggal_indonesia($tgl_bukti) }} &nbsp;</td>
                            <td align="right">{{ rupiah($ls) }}</td>
                            <td align="right">{{ rupiah($up) }}</td>
                            <td align="right">{{ rupiah($gu) }}</td>
                            <td align="right">{{ rupiah($up + $gu + $ls) }}</td>
                        <tr>
                            <td colspan="2" align="left"><i>&nbsp;SP2D: {{ $no_sp2d }}</i> </td>

                        </tr>
                        @php
                            $jumls = $jumls + $ls;
                            $jumup = $jumup + $up;
                            $jumgu = $jumgu + $gu;
                            $jml = $jml + $ls + $up + $gu;
                        @endphp
                    @endif
                @endforeach

                <TR>
                    <TD colspan="2" align="left"><i><b>Jumlah</i></b></TD>
                    <TD align="right"><b>{{ rupiah($jumls) }}</b></TD>
                    <TD align="right"><b>{{ rupiah($jumup) }}</b></TD>
                    <TD align="right"><b>{{ rupiah($jumgu) }}</b></TD>
                    <TD align="right"><b>{{ rupiah($jml) }}</b></TD>
                </TR>
                @php
                    $laluls = 0;
                    $laluup = 0;
                    $lalugu = 0;
                    $totlalu = 0;
                @endphp
                @foreach ($lalu as $lal)
                    @if ($header->kd_sub_kegiatan . '.' . $header->kd_rek6 == $lal->kd_sub_kegiatan . '.' . $lal->kd_rek6)
                        @php
                            $laluls = $lal->ls;
                            $laluup = $lal->up;
                            $lalugu = $lal->gu;
                            $totlalu = $laluls + $laluup + $lalugu;
                        @endphp
                    @endif
                @endforeach
                <TR>
                    <TD colspan="2" align="left"><i><b>Jumlah s/d periode lalu </i></b></TD>
                    <TD align="right"><b>{{ rupiah($laluls) }}</b></TD>
                    <TD align="right"><b>{{ rupiah($laluup) }}</b></TD>
                    <TD align="right"><b>{{ rupiah($lalugu) }}</b></TD>
                    <TD align="right"><b>{{ rupiah($totlalu) }}</b></TD>
                </TR>
                @php
                    $sdinils = 0;
                    $sdiniup = 0;
                    $sdinigu = 0;
                    $totsdini = 0;
                @endphp
                @foreach ($sd_ini as $sdini)
                    @if ($header->kd_sub_kegiatan . '.' . $header->kd_rek6 == $sdini->kd_sub_kegiatan . '.' . $sdini->kd_rek6)
                        @php
                            $sdinils = $sdini->ls;
                            $sdiniup = $sdini->up;
                            $sdinigu = $sdini->gu;
                            $totsdini = $sdinils + $sdiniup + $sdinigu;
                        @endphp
                    @endif
                @endforeach
                <TR>
                    <TD colspan="2" align="left"><i><b>Jumlah s/d periode ini </i></b></TD>
                    <TD align="right"><b>{{ rupiah($sdinils) }}</b></TD>
                    <TD align="right"><b>{{ rupiah($sdiniup) }}</b></TD>
                    <TD align="right"><b>{{ rupiah($sdinigu) }}</b></TD>
                    <TD align="right"><b>{{ rupiah($totsdini) }}</b></TD>
                </TR>
            </table>
            <br />
            <br />
        @endforeach

    </table>
    @php
        for ($i = 0; $i <= $enter; $i++) {
            echo '<br>';
        }
    @endphp
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table" style="width: 100%;font-size:12px;font-family:Open Sans">
            <tr>
                <td style="margin: 2px 0px;text-align: center;">
                    Disetujui oleh
                </td>
                <td style="margin: 2px 0px;text-align: center;">
                    {{ $daerah->daerah }},
                    {{ \Carbon\Carbon::parse($tanggal_ttd)->locale('id')->isoFormat('DD MMMM Y') }}
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center;">
                    {{ ucwords(strtolower($cari_pa_kpa->jabatan)) }}
                </td>
                <td style="padding-bottom: 50px;text-align: center;">
                    {{ ucwords(strtolower($cari_bendahara->jabatan)) }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;"><b><u>{{ $cari_pa_kpa->nama }}</u></b></td>
                <td style="text-align: center;"><b><u>{{ $cari_bendahara->nama }}</u></b></td>
            </tr>
            <tr>
                <td style="text-align: center;">{{ $cari_pa_kpa->pangkat }}</td>
                <td style="text-align: center;">{{ $cari_bendahara->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">NIP. {{ $cari_pa_kpa->nip }}</td>
                <td style="text-align: center;">NIP. {{ $cari_bendahara->nip }}</td>
            </tr>

        </table>
    </div>
</body>

</html>
