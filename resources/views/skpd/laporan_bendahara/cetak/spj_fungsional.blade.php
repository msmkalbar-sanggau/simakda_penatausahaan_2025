<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $judul }}</title>
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
    <style type="text/css" media="print">
        @page {
            size: Legal landscape;
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
            <td style="text-align: center"><b>LAPORAN PERTANGGUNGJAWABAN BENDAHARA PENGELUARAN<br />
                    (SPJ {{ strtoupper($judul) }})</b></td>
        </tr>

        <?php if ($periode == 'bulan7') :?>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
        <?php else : ?>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE
                    {{ \Carbon\Carbon::parse($tanggal_awal)->locale('id')->isoFormat('DD MMMM Y') }} s/d
                    {{ \Carbon\Carbon::parse($tanggal_akhir)->locale('id')->isoFormat('DD MMMM Y') }}
                </b></td>
        </tr>
        <?php endif ?>

    </table>

    {{-- ISI CETAKAN --}}

    <table style='border-collapse:collapse;font-size:12px' width='100%' align='center' border='1' cellspacing='1'
        cellpadding={{ $enter }}>
        <thead>
            <tr>
                <td bgcolor='#CCCCCC' align='center' rowspan='2' style='font-size:12px'><b>Kode<br>Rekening</b></td>
                <td bgcolor='#CCCCCC' align='center' rowspan='2' style='font-size:12px'><b>Uraian</b></td>
                <td bgcolor='#CCCCCC' align='center' rowspan='2' style='font-size:12px'><b>Jumlah<br>Anggaran</b>
                </td>
                <td bgcolor='#CCCCCC' align='center' colspan='3' style='font-size:12px'><b>SPJ-LS Gaji</b></td>
                <td bgcolor='#CCCCCC' align='center' colspan='3' style='font-size:12px'><b>SPJ-LS Barang & Jasa</b>
                </td>
                <td bgcolor='#CCCCCC' align='center' colspan='3' style='font-size:12px'><b>SPJ UP/GU/TU</b></td>
                <td bgcolor='#CCCCCC' align='center' rowspan='2' style='font-size:12px'><b>Jumlah
                        SPJ<br>(LS+UP/GU/TU)<br>s.d Bulan Ini</b></td>
                <td bgcolor='#CCCCCC' align='center' rowspan='2' style='font-size:12px'><b>Sisa Pagu<br>Anggaran</b>
                </td>
            </tr>
            <tr>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'><b>s.d<br>Bulan<br>lalu</b></td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'><b>Bulan Ini</b></td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'><b>s.d<br>Bulan Ini</b></td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'><b>s.d<br>Bulan<br>lalu</b></td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'><b>Bulan Ini</b></td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'><b>s.d<br>Bulan Ini</b></td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'><b>s.d<br>Bulan<br>lalu</b></td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'><b>Bulan Ini</b></td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'><b>s.d<br>Bulan Ini</b></td>
            </tr>
            <tr>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>1</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>2</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>3</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>4</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>5</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>6</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>7</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>8</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>9</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>10</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>11</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>12</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>13</td>
                <td bgcolor='#CCCCCC' align='center' style='font-size:12px'>14</td>
            </tr>
        </thead>
        <tr>
            <td align='center' style='font-size:12px'>&nbsp;</td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
        </tr>

        {{-- LOOPING --}}
        @php
            $total = 0;
            $sisa = 0;
        @endphp
        @foreach ($rincian as $rinci)
            @php
                $akun = $rinci->kd_rek;
                $kode = $rinci->kode;
                $uraian = $rinci->uraian;
                $nilai = $rinci->anggaran;
                $real_up_ini = $rinci->up_ini;
                $real_up_ll = $rinci->up_lalu;
                $real_gaji_ini = $rinci->gaji_ini;
                $real_gaji_ll = $rinci->gaji_lalu;
                $real_brg_js_ini = $rinci->brg_ini;
                $real_brg_js_ll = $rinci->brg_lalu;
                $total =
                    $real_gaji_ll + $real_gaji_ini + $real_brg_js_ll + $real_brg_js_ini + $real_up_ll + $real_up_ini;
                $sisa =
                    $nilai -
                    $real_gaji_ll -
                    $real_gaji_ini -
                    $real_brg_js_ll -
                    $real_brg_js_ini -
                    $real_up_ll -
                    $real_up_ini;
                $panjang_akun = strlen($akun);
            @endphp

            @if ($panjang_akun == 7)
                <tr>
                    <td valign='top' width='5%' align='left' style='font-size:10px'>
                        <b>{{ $akun }}</b>
                    </td>
                    <td valign='top' align='left' width='28%' style='font-size:10px'>
                        <b>{{ $uraian }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($nilai) }}</b></td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_gaji_ll) }}</b></td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_gaji_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'>
                        <b>{{ rupiah($real_gaji_ll + $real_gaji_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_brg_js_ll) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_brg_js_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'>
                        <b>{{ rupiah($real_brg_js_ll + $real_brg_js_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_up_ll) }}</b></td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_up_ini) }}</b></td>
                    <td valign='top' align='right' style='font-size:10px'>
                        <b>{{ rupiah($real_up_ll + $real_up_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($total) }}</b></b></td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($sisa) }}</b></td>
                </tr>
            @elseif ($panjang_akun == 12 || $panjang_akun == 15)
                <tr>
                    <td valign='top' width='8%' align='left' style='font-size:10px'>
                        <b>{{ $akun }}</b>
                    </td>
                    <td valign='top' align='left' width='25%' style='font-size:10px'>
                        <b>{{ $uraian }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($nilai) }}</b></td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_gaji_ll) }}</b></td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_gaji_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'>
                        <b>{{ rupiah($real_gaji_ll + $real_gaji_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_brg_js_ll) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_brg_js_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'>
                        <b>{{ rupiah($real_brg_js_ll + $real_brg_js_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_up_ll) }}</b></td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($real_up_ini) }}</b></td>
                    <td valign='top' align='right' style='font-size:10px'>
                        <b>{{ rupiah($real_up_ll + $real_up_ini) }}</b>
                    </td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($total) }}</b></b></td>
                    <td valign='top' align='right' style='font-size:10px'><b>{{ rupiah($sisa) }}</b></td>
                </tr>
            @else
                <tr>
                    <td valign='top' width='8%' align='left' style='font-size:10px'>{{ $kode }}
                    </td>
                    <td valign='top' align='left' width='25%' style='font-size:10px'>{{ $uraian }}
                    </td>
                    <td valign='top' align='right' style='font-size:10px'>{{ rupiah($nilai) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>{{ rupiah($real_gaji_ll) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>{{ rupiah($real_gaji_ini) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>
                        {{ rupiah($real_gaji_ll + $real_gaji_ini) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>{{ rupiah($real_brg_js_ll) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>{{ rupiah($real_brg_js_ini) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>
                        {{ rupiah($real_brg_js_ll + $real_brg_js_ini) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>{{ rupiah($real_up_ll) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>{{ rupiah($real_up_ini) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>
                        {{ rupiah($real_up_ll + $real_up_ini) }}</td>
                    <td valign='top' align='right' style='font-size:10px'>{{ rupiah($total) }}</b></td>
                    <td valign='top' align='right' style='font-size:10px'>{{ rupiah($sisa) }}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td valign='top' align='center' style='font-size:12px'>&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>Penerimaan :</td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td valign='top' align='center' style='font-size:12px'></td>
        </tr>
        {{-- terima sp2d --}}

        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- SP2D</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sp2d_gj_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sp2d_gj_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sp2d_gj_ll + $sp2d_gj_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sp2d_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sp2d_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sp2d_brjs_ll + $sp2d_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sp2d_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sp2d_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sp2d_up_ll + $sp2d_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($sp2d_gj_ll + $sp2d_gj_ini + $sp2d_brjs_ll + $sp2d_brjs_ini + $sp2d_up_ll + $sp2d_up_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- terima potongan pajak --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Potongan Pajak</td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;&ensp;&ensp;a. PPN Pusat</td>
            <td align='right' style='font-size:12px'>{{ rupiah($ppn_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($ppn_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($ppn_gaji_ll + $ppn_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($ppn_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($ppn_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($ppn_brjs_ll + $ppn_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($ppn_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($ppn_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($ppn_up_ll + $ppn_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($ppn_up_ll + $ppn_up_ini + $ppn_gaji_ll + $ppn_gaji_ini + $ppn_brjs_ll + $ppn_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;&ensp;&ensp;b. PPH 21</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph21_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph21_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph21_gaji_ll + $pph21_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph21_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph21_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph21_brjs_ll + $pph21_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph21_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph21_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph21_up_ll + $pph21_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($pph21_up_ll + $pph21_up_ini + $pph21_gaji_ll + $pph21_gaji_ini + $pph21_brjs_ll + $pph21_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;&ensp;&ensp;c. PPH 22</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph22_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph22_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph22_gaji_ll + $pph22_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph22_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph22_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph22_brjs_ll + $pph22_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph22_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph22_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph22_up_ll + $pph22_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($pph22_up_ll + $pph22_up_ini + $pph22_gaji_ll + $pph22_gaji_ini + $pph22_brjs_ll + $pph22_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;&ensp;&ensp;d. PPH 23</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph23_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph23_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph23_gaji_ll + $pph23_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph23_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph23_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph23_brjs_ll + $pph23_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph23_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph23_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph23_up_ll + $pph23_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($pph23_up_ll + $pph23_up_ini + $pph23_gaji_ll + $pph23_gaji_ini + $pph23_brjs_ll + $pph23_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- terimz IWP --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Pot. IWP</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_iwp_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_iwp_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_iwp_gaji_ll + $trm_iwp_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_iwp_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_iwp_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_iwp_brjs_ll + $trm_iwp_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_iwp_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_iwp_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_iwp_up_ll + $trm_iwp_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($trm_iwp_up_ll + $trm_iwp_up_ini + $trm_iwp_brjs_ll + $trm_iwp_brjs_ini + $trm_iwp_gaji_ll + $trm_iwp_gaji_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- terimz taperum --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Pot. taperum</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_taperum_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_taperum_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_taperum_gaji_ll + $trm_taperum_gaji_ini) }}
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_taperum_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_taperum_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_taperum_brjs_ll + $trm_taperum_brjs_ini) }}
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_taperum_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_taperum_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_taperum_up_ll + $trm_taperum_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($trm_taperum_up_ll + $trm_taperum_up_ini + $trm_taperum_brjs_ll + $trm_taperum_brjs_ini + $trm_taperum_gaji_ll + $trm_taperum_gaji_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- PPH Pasal 4 Ayat 2
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph4ayat2_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph4ayat2_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph4ayat2_gaji_ll + $pph4ayat2_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph4ayat2_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph4ayat2_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph4ayat2_brjs_ll + $pph4ayat2_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph4ayat2_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph4ayat2_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pph4ayat2_up_ll + $pph4ayat2_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($pph4ayat2_up_ll + $pph4ayat2_up_ini + $pph4ayat2_gaji_ll + $pph4ayat2_gaji_ini + $pph4ayat2_brjs_ll + $pph4ayat2_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- Pot. Jaminan Kesehatan --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Pot. Jaminan Kesehatan </td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_ppnpn_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_ppnpn_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_ppnpn_gaji_ll + $trm_ppnpn_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_ppnpn_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_ppnpn_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_ppnpn_brjs_ll + $trm_ppnpn_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_ppnpn_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_ppnpn_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($trm_ppnpn_up_ll + $trm_ppnpn_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($trm_ppnpn_up_ll + $trm_ppnpn_up_ini + $trm_ppnpn_brjs_ll + $trm_ppnpn_brjs_ini + $trm_ppnpn_gaji_ll + $trm_ppnpn_gaji_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- lainnya --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Lain-lain</td>
            <td align='right' style='font-size:12px'>{{ rupiah($jlain_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($jlain_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($jlain_gaji_ll + $jlain_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($jlain_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($jlain_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($jlain_brjs_ll + $jlain_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($jlain_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($jlain_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($jlain_up_ll + $jlain_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($jlain_gaji_ll + $jlain_gaji_ini + $jlain_brjs_ll + $jlain_brjs_ini + $jlain_up_ll + $jlain_up_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- Droping Dana --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Droping Dana </td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pelimpahan_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pelimpahan_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pelimpahan_up_ll + $pelimpahan_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($pelimpahan_up_ll + $pelimpahan_up_ini) }}</td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>

        {{-- Panjar --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Panjar Dana</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($panjar_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($panjar_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($panjar_up_ll + $panjar_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($panjar_up_ll + $panjar_up_ini) }}</td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>

        {{-- BLUD --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- BLUD</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($blud_bln_lalu) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($blud_bln_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($blud_bln_ini + $blud_bln_lalu) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($blud_bln_ini + $blud_bln_lalu) }}</td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>


        @php
            $sub_trm_gaji_lalu =
                $sp2d_gj_ll +
                $ppn_gaji_ll +
                $pph21_gaji_ll +
                $pph22_gaji_ll +
                $pph23_gaji_ll +
                $pph4ayat2_gaji_ll +
                $trm_iwp_gaji_ll +
                $trm_taperum_gaji_ll +
                $trm_ppnpn_gaji_ll +
                $trm_dk_gaji_ll +
                $jlain_gaji_ll;

            $sub_trm_gaji_ini =
                $sp2d_gj_ini +
                $ppn_gaji_ini +
                $pph21_gaji_ini +
                $pph22_gaji_ini +
                $pph23_gaji_ini +
                $pph4ayat2_gaji_ini +
                $trm_iwp_gaji_ini +
                $trm_taperum_gaji_ini +
                $trm_ppnpn_gaji_ini +
                $trm_dk_gaji_ini +
                $jlain_gaji_ini;

            $sub_trm_gaji_sini = $sub_trm_gaji_lalu + $sub_trm_gaji_ini;

            $sub_trm_barjas_lalu =
                $sp2d_brjs_ll +
                $ppn_brjs_ll +
                $pph21_brjs_ll +
                $pph22_brjs_ll +
                $pph23_brjs_ll +
                $pph4ayat2_brjs_ll +
                $trm_iwp_brjs_ll +
                $trm_taperum_brjs_ll +
                $trm_ppnpn_brjs_ll +
                $trm_dk_brjs_ll +
                $blud_bln_lalu +
                $jlain_brjs_ll;
            $sub_trm_barjas_ini =
                $sp2d_brjs_ini +
                $ppn_brjs_ini +
                $pph21_brjs_ini +
                $pph22_brjs_ini +
                $pph23_brjs_ini +
                $pph4ayat2_brjs_ini +
                $trm_iwp_brjs_ini +
                $trm_taperum_brjs_ini +
                $trm_ppnpn_brjs_ini +
                $trm_dk_brjs_ini +
                $blud_bln_ini +
                $jlain_brjs_ini;
            $sub_trm_barjas_sini = $sub_trm_barjas_lalu + $sub_trm_barjas_ini;

            $sub_trm_uptu_lalu =
                $sp2d_up_ll +
                $ppn_up_ll +
                $pph21_up_ll +
                $pph22_up_ll +
                $pph23_up_ll +
                $pph4ayat2_up_ll +
                $trm_iwp_up_ll +
                $trm_taperum_up_ll +
                $trm_ppnpn_up_ll +
                $trm_dk_up_ll +
                $pelimpahan_up_ll +
                $panjar_up_ll +
                $jlain_up_ll;
            $sub_trm_uptu_ini =
                $sp2d_up_ini +
                $ppn_up_ini +
                $pph21_up_ini +
                $pph22_up_ini +
                $pph23_up_ini +
                $pph4ayat2_up_ini +
                $trm_iwp_up_ini +
                $trm_taperum_up_ini +
                $trm_ppnpn_up_ini +
                $trm_dk_up_ini +
                $pelimpahan_up_ini +
                $panjar_up_ini +
                $jlain_up_ini;
            $sub_trm_uptu_sini = $sub_trm_uptu_lalu + $sub_trm_uptu_ini;

            $sub_trm =
                $sub_trm_gaji_lalu +
                $sub_trm_gaji_ini +
                $sub_trm_barjas_lalu +
                $sub_trm_barjas_ini +
                $sub_trm_uptu_lalu +
                $sub_trm_uptu_ini;
        @endphp
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'><b>Jumlah Penerimaan</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_gaji_lalu) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_gaji_sini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_barjas_lalu) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_barjas_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_barjas_sini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_uptu_lalu) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_uptu_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_uptu_sini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm) }}</td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- spasi --}}
        <tr>
            <td align='center' style='font-size:12px'>&ensp;&ensp;</td>
            <td align='center' style='font-size:12px' colspan='2'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
        </tr>
        {{-- pengeluaran --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>Pengeluaran :</td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- SPJ --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;- SPJ(LS + UP/GU/TU)</td>
            <td align='right' style='font-size:12px'>{{ rupiah($spj_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($spj_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($spj_gaji_ini + $spj_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($spj_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($spj_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($spj_brjs_ini + $spj_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($spj_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($spj_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($spj_up_ini + $spj_up_ll) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($spj_gaji_ini + $spj_gaji_ll + $spj_brjs_ini + $spj_brjs_ll + $spj_up_ini + $spj_up_ll) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;- Penyetoran Pajak</td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='center' style='font-size:12px'></td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;&ensp;&ensp;a. PPN Pusat</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppn_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppn_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppn_gaji_ll + $str_ppn_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppn_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppn_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppn_brjs_ll + $str_ppn_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppn_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppn_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppn_up_ll + $str_ppn_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_ppn_up_ll + $str_ppn_up_ini + $str_ppn_gaji_ll + $str_ppn_gaji_ini + $str_ppn_brjs_ll + $str_ppn_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;&ensp;&ensp;b. PPH 21</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph21_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph21_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph21_gaji_ll + $str_pph21_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph21_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph21_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph21_brjs_ll + $str_pph21_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph21_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph21_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph21_up_ll + $str_pph21_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_pph21_up_ll + $str_pph21_up_ini + $str_pph21_gaji_ll + $str_pph21_gaji_ini + $str_pph21_brjs_ll + $str_pph21_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;&ensp;&ensp;c. PPH 22</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph22_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph22_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph22_gaji_ll + $str_pph22_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph22_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph22_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph22_brjs_ll + $str_pph22_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph22_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph22_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph22_up_ll + $str_pph22_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_pph22_up_ll + $str_pph22_up_ini + $str_pph22_gaji_ll + $str_pph22_gaji_ini + $str_pph22_brjs_ll + $str_pph22_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;&ensp;&ensp;d. PPH 23</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph23_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph23_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph23_gaji_ll + $str_pph23_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph23_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph23_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph23_brjs_ll + $str_pph23_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph23_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph23_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph23_up_ll + $str_pph23_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_pph23_up_ll + $str_pph23_up_ini + $str_pph23_gaji_ll + $str_pph23_gaji_ini + $str_pph23_brjs_ll + $str_pph23_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- terimz IWP --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Pot. IWP</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_iwp_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_iwp_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_iwp_gaji_ll + $str_iwp_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_iwp_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_iwp_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_iwp_brjs_ll + $str_iwp_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_iwp_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_iwp_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_iwp_up_ll + $str_iwp_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_iwp_up_ll + $str_iwp_up_ini + $str_iwp_brjs_ll + $str_iwp_brjs_ini + $str_iwp_gaji_ll + $str_iwp_gaji_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- terimz taperum --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Pot. taperum</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_taperum_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_taperum_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_taperum_gaji_ll + $str_taperum_gaji_ini) }}
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_taperum_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_taperum_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_taperum_brjs_ll + $str_taperum_brjs_ini) }}
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_taperum_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_taperum_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_taperum_up_ll + $str_taperum_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_taperum_up_ll + $str_taperum_up_ini + $str_taperum_brjs_ll + $str_taperum_brjs_ini + $str_taperum_gaji_ll + $str_taperum_gaji_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- PPH pasal 4 ayat 2 --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- PPH Pasal 4 Ayat 2
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph4ayat2_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph4ayat2_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_pph4ayat2_gaji_ll + $str_pph4ayat2_gaji_ini) }}
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph4ayat2_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph4ayat2_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_pph4ayat2_brjs_ll + $str_pph4ayat2_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph4ayat2_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph4ayat2_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pph4ayat2_up_ll + $str_pph4ayat2_up_ini) }}
            </td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_pph4ayat2_up_ll + $str_pph4ayat2_up_ini + $str_pph4ayat2_gaji_ll + $str_pph4ayat2_gaji_ini + $str_pph4ayat2_brjs_ll + $str_pph4ayat2_brjs_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        {{-- Pot. Jaminan Kesehatan --}}
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Pot. Jaminan Kesehatan </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppnpn_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppnpn_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppnpn_gaji_ll + $str_ppnpn_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppnpn_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppnpn_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppnpn_brjs_ll + $str_ppnpn_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppnpn_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppnpn_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_ppnpn_up_ll + $str_ppnpn_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_ppnpn_up_ll + $str_ppnpn_up_ini + $str_ppnpn_brjs_ll + $str_ppnpn_brjs_ini + $str_ppnpn_gaji_ll + $str_ppnpn_gaji_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Pot. Penghasilan Lainnya </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pplain_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pplain_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pplain_gaji_ll + $str_pplain_gaji_ini) }}
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pplain_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pplain_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pplain_brjs_ll + $str_pplain_brjs_ini) }}
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pplain_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pplain_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pplain_up_ll + $str_pplain_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_pplain_up_ll + $str_pplain_up_ini + $str_pplain_brjs_ll + $str_pplain_brjs_ini + $str_pplain_gaji_ll + $str_pplain_gaji_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- HKPG </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_hkpg_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_hkpg_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_hkpg_gaji_ll + $str_hkpg_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_hkpg_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_hkpg_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_hkpg_brjs_ll + $str_hkpg_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_hkpg_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_hkpg_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_hkpg_up_ll + $str_hkpg_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_hkpg_up_ll + $str_hkpg_up_ini + $str_hkpg_brjs_ll + $str_hkpg_brjs_ini + $str_hkpg_gaji_ll + $str_hkpg_gaji_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Contra Post </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_cp_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_cp_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_cp_gaji_ll + $str_cp_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_cp_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_cp_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_cp_brjs_ll + $str_cp_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_cp_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_cp_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_cp_up_ll + $str_cp_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($str_cp_up_ll + $str_cp_up_ini + $str_cp_brjs_ll + $str_cp_brjs_ini + $str_cp_gaji_ll + $str_cp_gaji_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- BLUD</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_blud_ls_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_blud_ls_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_blud_ls_ll + $str_blud_ls_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_blud_ls_ll + $str_blud_ls_ini) }}</td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Lain-lain</td>
            <td align='right' style='font-size:12px'>{{ rupiah($lain_gaji_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($lain_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($lain_gaji_ll + $lain_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($lain_brjs_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($lain_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($lain_brjs_ll + $lain_brjs_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($lain_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($lain_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($lain_up_ll + $lain_up_ini) }}</td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($lain_gaji_ll + $lain_gaji_ini + $lain_brjs_ll + $lain_brjs_ini + $lain_up_ll + $lain_up_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Droping Dana</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pelimpahan_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pelimpahan_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pelimpahan_up_ll + $str_pelimpahan_up_ini) }}
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_pelimpahan_up_ll + $str_pelimpahan_up_ini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>&ensp;&ensp;- Panjar Dana</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah(0) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_panjar_up_ll) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_panjar_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_panjar_up_ll + $str_panjar_up_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($str_panjar_up_ll + $str_panjar_up_ini) }}</td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>

        @php
            $sub_str_gaji_lalu =
                $spj_gaji_ll +
                $str_ppn_gaji_ll +
                $str_pph21_gaji_ll +
                $str_pph22_gaji_ll +
                $str_pph23_gaji_ll +
                $str_pph4ayat2_gaji_ll +
                $str_iwp_gaji_ll +
                $str_taperum_gaji_ll +
                $str_ppnpn_gaji_ll +
                $str_dk_gaji_ll +
                $lain_gaji_ll +
                $str_cp_gaji_ll;
            // $str_hkpg_gaji_ll;

            $sub_str_gaji_ini =
                $spj_gaji_ini +
                $str_ppn_gaji_ini +
                $str_pph21_gaji_ini +
                $str_pph22_gaji_ini +
                $str_pph23_gaji_ini +
                $str_pph4ayat2_gaji_ini +
                $str_iwp_gaji_ini +
                $str_taperum_gaji_ini +
                $str_ppnpn_gaji_ini +
                $str_dk_gaji_ini +
                $lain_gaji_ini +
                $str_cp_gaji_ini;
            // $str_hkpg_gaji_ini;
            $sub_str_gaji_sini = $sub_str_gaji_lalu + $sub_str_gaji_ini;

            $sub_str_barjas_lalu =
                $spj_brjs_ll +
                $str_ppn_brjs_ll +
                $str_pph21_brjs_ll +
                $str_pph22_brjs_ll +
                $str_pph23_brjs_ll +
                $str_pph4ayat2_brjs_ll +
                $str_iwp_brjs_ll +
                $str_taperum_brjs_ll +
                $str_ppnpn_brjs_ll +
                $str_dk_brjs_ll +
                $lain_brjs_ll +
                // $str_cp_brjs_ll +
                $str_blud_ls_ll +
                $str_hkpg_brjs_ll;

            $sub_str_barjas_ini =
                $spj_brjs_ini +
                $str_ppn_brjs_ini +
                $str_pph21_brjs_ini +
                $str_pph22_brjs_ini +
                $str_pph23_brjs_ini +
                $str_pph4ayat2_brjs_ini +
                $str_iwp_brjs_ini +
                $str_taperum_brjs_ini +
                $str_ppnpn_brjs_ini +
                $str_dk_brjs_ini +
                $lain_brjs_ini +
                // $str_cp_brjs_ini +
                $str_blud_ls_ini +
                $str_hkpg_brjs_ini;
            $sub_str_barjas_sini = $sub_str_barjas_lalu + $sub_str_barjas_ini;

            $sub_str_uptu_lalu =
                $spj_up_ll +
                $str_ppn_up_ll +
                $str_pph21_up_ll +
                $str_pph22_up_ll +
                $str_pph23_up_ll +
                $str_pph4ayat2_up_ll +
                $str_iwp_up_ll +
                $str_taperum_up_ll +
                $str_ppnpn_up_ll +
                $str_dk_up_ll +
                $str_pelimpahan_up_ll +
                $str_panjar_up_ll +
                $lain_up_ll +
                $str_cp_up_ll +
                $str_hkpg_up_ll;
            $sub_str_uptu_ini =
                $spj_up_ini +
                $str_ppn_up_ini +
                $str_pph21_up_ini +
                $str_pph22_up_ini +
                $str_pph23_up_ini +
                $str_pph4ayat2_up_ini +
                $str_iwp_up_ini +
                $str_taperum_up_ini +
                $str_ppnpn_up_ini +
                $str_dk_up_ini +
                $str_pelimpahan_up_ini +
                $str_panjar_up_ini +
                $lain_up_ini +
                $str_cp_up_ini;
            $sub_str_uptu_sini = $sub_str_uptu_lalu + $sub_str_uptu_ini + $str_hkpg_up_ini;

            $sub_str =
                $sub_str_gaji_lalu +
                $sub_str_gaji_ini +
                $sub_str_barjas_lalu +
                $sub_str_barjas_ini +
                $sub_str_uptu_lalu +
                $sub_str_uptu_ini;
        @endphp
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'><b>Jumlah Pengeluaran</b></td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str_gaji_lalu) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str_gaji_sini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str_barjas_lalu) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str_barjas_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str_barjas_sini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str_uptu_lalu) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str_uptu_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str_uptu_sini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_str) }}</td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td>&ensp;&ensp;</td>
            <td colspan='12'></td>
            <td>&ensp;&ensp;</td>
        </tr>
        <tr>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
            <td align='left' style='font-size:12px' colspan='2'>Saldo Kas</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_gaji_lalu - $sub_str_gaji_lalu) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_gaji_ini - $sub_str_gaji_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_gaji_sini - $sub_str_gaji_sini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_barjas_lalu - $sub_str_barjas_lalu) }}
            </td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_barjas_ini - $sub_str_barjas_ini) }}</td>
            <td align='right' style='font-size:12px'>{{ rupiah($sub_trm_barjas_sini - $sub_str_barjas_sini) }}
            </td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($sub_trm_uptu_lalu - $sub_str_uptu_lalu) }}
            </td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($sub_trm_uptu_ini - $sub_str_uptu_ini) }}
            </td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($sub_trm_uptu_sini - $sub_str_uptu_sini) }}
            </td>
            <td align='right' style='font-size:12px'>
                {{ rupiah($sub_trm_gaji_sini - $sub_str_gaji_sini + $sub_trm_barjas_sini - $sub_str_barjas_sini + $sub_trm_uptu_sini - $sub_str_uptu_sini) }}
            </td>
            <td align='left' style='font-size:12px;border-top:hidden;'>&ensp;&ensp;</td>
        </tr>

    </table>
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
                <td style="text-align: center;"><u><b>{{ $cari_pa_kpa->nama }} </b></u></td>
                <td style="text-align: center;"><u><b>{{ $cari_bendahara->nama }} </b></u></td>
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
<script></script>

</html>
