<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REKAP REGISTER PAJAK</title>
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
            <td style="text-align: center"><b>REKAP REGISTER PAJAK</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    <TABLE style="border-collapse:collapse;font-size:14px" border="1" cellspacing="' . $spasi . '"
        cellpadding={{ $enter }} width="100%">
        <THEAD>
            <TR>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">NO</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Bulan</TD>
                <TD bgcolor="#CCCCCC" colspan="5" align="center">Pajak UP/GU/TU</TD>
                <TD bgcolor="#CCCCCC" colspan="5" align="center">Pajak LS</TD>
                <TD bgcolor="#CCCCCC" colspan="7" align="center">Potongan Lainnya</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Pemotongan</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Penyetoran</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Saldo</TD>
            </TR>
            <TR>
                <TD bgcolor="#CCCCCC" align="center">PPN</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH21</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH22</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH23</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH4</TD>
                <TD bgcolor="#CCCCCC" align="center">PPN</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH21</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH22</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH23</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH4</TD>
                <TD bgcolor="#CCCCCC" align="center">PPNPN 1%</TD>
                <TD bgcolor="#CCCCCC" align="center">PPNPN 4%</TD>
                <TD bgcolor="#CCCCCC" align="center">IWP</TD>
                <TD bgcolor="#CCCCCC" align="center">TAPERUM</TD>
                <TD bgcolor="#CCCCCC" align="center">JKK</TD>
                <TD bgcolor="#CCCCCC" align="center">JKM</TD>
                <TD bgcolor="#CCCCCC" align="center">BPJS</TD>
            </TR>
        </THEAD>
        @php
            $jumlah = 0;
            $ppn_up_t = 0;
            $pph21_up_t = 0;
            $pph22_up_t = 0;
            $pph23_up_t = 0;
            $pph4_up_t = 0;
            $ppn_ls_t = 0;
            $pph21_ls_t = 0;
            $pph22_ls_t = 0;
            $pph23_ls_t = 0;
            $pph4_ls_t = 0;
            $ppnpn1_t = 0;
            $ppnpn4_t = 0;
            $iwp_t = 0;
            $taperum_t = 0;
            $jkk_t = 0;
            $jkm_t = 0;
            $bpjs_t = 0;
            $terima_t = 0;
            $setor_t = 0;
            $no = 0;
        @endphp
        @foreach ($rincian as $row)
            @php
                $bulan = $row->bulan;
                $ppn_up = $row->ppn_up;
                $pph21_up = $row->pph21_up;
                $pph22_up = $row->pph22_up;
                $pph23_up = $row->pph23_up;
                $pph4_up = $row->pph4_up;
                $ppn_ls = $row->ppn_ls;
                $pph21_ls = $row->pph21_ls;
                $pph22_ls = $row->pph22_ls;
                $pph23_ls = $row->pph23_ls;
                $pph4_ls = $row->pph4_ls;
                $ppnpn1 = $row->ppnpn1;
                $ppnpn4 = $row->ppnpn4;
                $iwp = $row->iwp;
                $taperum = $row->taperum;
                $jkk = $row->jkk;
                $jkm = $row->jkm;
                $bpjs = $row->bpjs;
                $terima = $row->terima;
                $setor = $row->setor;
                $jumlah = $jumlah + $terima - $setor;
                $ppn_up_t = $ppn_up_t + $ppn_up;
                $pph21_up_t = $pph21_up_t + $pph21_up;
                $pph22_up_t = $pph22_up_t + $pph22_up;
                $pph23_up_t = $pph23_up_t + $pph23_up;
                $pph4_up_t = $pph4_up_t + $pph4_up;
                $ppn_ls_t = $ppn_ls_t + $ppn_ls;
                $pph21_ls_t = $pph21_ls_t + $pph21_ls;
                $pph22_ls_t = $pph22_ls_t + $pph22_ls;
                $pph23_ls_t = $pph23_ls_t + $pph23_ls;
                $pph4_ls_t = $pph4_ls_t + $pph4_ls;
                $ppnpn1_t = $ppnpn1_t + $ppnpn1;
                $ppnpn4_t = $ppnpn4_t + $ppnpn4;
                $iwp_t = $iwp_t + $iwp;
                $taperum_t = $taperum_t + $taperum;
                $jkk_t = $jkk_t + $jkk;
                $jkm_t = $jkm_t + $jkm;
                $bpjs_t = $bpjs_t + $bpjs;
                $terima_t = $terima_t + $terima;
                $setor_t = $setor_t + $setor;
                $no = $no + 1;
            @endphp
            <TR>
                <td align="left">{{ $no }}</TD>
                <td align="left">{{ Bulan($bulan) }}</TD>
                <td align="right">{{ rupiah($ppn_up) }}</TD>
                <td align="right">{{ rupiah($pph21_up) }}</TD>
                <td align="right">{{ rupiah($pph22_up) }}</TD>
                <td align="right">{{ rupiah($pph23_up) }}</TD>
                <td align="right">{{ rupiah($pph4_up) }}</TD>
                <td align="right">{{ rupiah($ppn_ls) }}</TD>
                <td align="right">{{ rupiah($pph21_ls) }}</TD>
                <td align="right">{{ rupiah($pph22_ls) }}</TD>
                <td align="right">{{ rupiah($pph23_ls) }}</TD>
                <td align="right">{{ rupiah($pph4_ls) }}</TD>
                <td align="right">{{ rupiah($ppnpn1) }}</TD>
                <td align="right">{{ rupiah($ppnpn4) }}</TD>
                <td align="right">{{ rupiah($iwp) }}</TD>
                <td align="right">{{ rupiah($taperum) }}</TD>
                <td align="right">{{ rupiah($jkk) }}</TD>
                <td align="right">{{ rupiah($jkm) }}</TD>
                <td align="right">{{ rupiah($bpjs) }}</TD>
                <td align="right">{{ rupiah($terima) }}</TD>
                <td align="right">{{ rupiah($setor) }}</TD>
                <td align="right">{{ rupiah($jumlah) }}</TD>
            </TR>
        @endforeach
        <TR>
            <TD colspan="2" align="right">Jumlah </TD>
            <td align="right">{{ rupiah($ppn_up_t) }}</TD>
            <td align="right">{{ rupiah($pph21_up_t) }}</TD>
            <td align="right">{{ rupiah($pph22_up_t) }}</TD>
            <td align="right">{{ rupiah($pph23_up_t) }}</TD>
            <td align="right">{{ rupiah($pph4_up_t) }}</TD>
            <td align="right">{{ rupiah($ppn_ls_t) }}</TD>
            <td align="right">{{ rupiah($pph21_ls_t) }}</TD>
            <td align="right">{{ rupiah($pph22_ls_t) }}</TD>
            <td align="right">{{ rupiah($pph23_ls_t) }}</TD>
            <td align="right">{{ rupiah($pph4_ls_t) }}</TD>
            <td align="right">{{ rupiah($ppnpn1_t) }}</TD>
            <td align="right">{{ rupiah($ppnpn4_t) }}</TD>
            <td align="right">{{ rupiah($iwp_t) }}</TD>
            <td align="right">{{ rupiah($taperum_t) }}</TD>
            <td align="right">{{ rupiah($jkk_t) }}</TD>
            <td align="right">{{ rupiah($jkm_t) }}</TD>
            <td align="right">{{ rupiah($bpjs_t) }}</TD>
            <td align="right">{{ rupiah($terima_t) }}</TD>
            <td align="right">{{ rupiah($setor_t) }}</TD>
            <td align="right">{{ rupiah($terima_t - $setor_t) }}</TD>
        </TR>

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
