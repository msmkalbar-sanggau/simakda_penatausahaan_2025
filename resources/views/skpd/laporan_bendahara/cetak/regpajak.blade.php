<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register Pajak</title>
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
            <td style="text-align: center"><b>REGISTER PAJAK {{ $jenispajak }}</b></td>
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
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Tanggal</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Uraian</TD>
                <TD bgcolor="#CCCCCC" colspan="5" align="center">Pajak {{ $jenispajak }}</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Pemotongan</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Penyetoran</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Jumlah</TD>
            </TR>
            <TR>
                <TD bgcolor="#CCCCCC" align="center">PPN</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH21</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH22</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH23</TD>
                <TD bgcolor="#CCCCCC" align="center">PPH4</TD>
            </TR>
        </THEAD>
        @php
            $nomor = 0;
            $tot_nilai = 0;
            $tot_nilai_belanja = 0;
            $tot_nilai_pot = 0;
        @endphp
        @foreach ($lalu as $row)
            @php
                $ppn_l = $row->ppn_l;
                $pph21_l = $row->pph21_l;
                $pph22_l = $row->pph22_l;
                $pph23_l = $row->pph23_l;
                $pph4_l = $row->pph4_l;
                $terima_l = $row->terima_l;
                $setor_l = $row->setor_l;
                $jumlah_l = $terima_l - $setor_l;
                $jumlah_lalu = $terima_l - $setor_l;
            @endphp
            {{-- LALU  --}}
            <TR>
                <TD colspan="3" align="right">Saldo Lalu</TD>
                <TD align="right"></TD>
                <TD align="right"></TD>
                <TD align="right"></TD>
                <TD align="right"></TD>
                <TD align="right"></TD>
                <TD align="right"></TD>
                <TD align="right"></TD>
                <TD align="right">{{ rupiah($jumlah_lalu) }}</TD>
            </TR>
        @endforeach

        {{-- SEKARANG --}}
        @php
            $jumlah = $jumlah_lalu;
            $ppn_t = 0;
            $pph21_t = 0;
            $pph22_t = 0;
            $pph23_t = 0;
            $pph4_t = 0;
            $terima_t = 0;
            $setor_t = 0;
        @endphp
        @foreach ($rincian as $rinci)
            @php
                $bukti = $rinci->no_bukti;
                $tanggal = $rinci->tgl_bukti;
                $ket = $rinci->ket;
                $ppn = $rinci->ppn;
                $pph21 = $rinci->pph21;
                $pph22 = $rinci->pph22;
                $pph23 = $rinci->pph23;
                $pph4 = $rinci->pph4;
                $terima = $rinci->terima;
                $setor = $rinci->setor;
                $jumlah = $jumlah + $terima - $setor;
                $ppn_t = $ppn_t + $ppn;
                $pph21_t = $pph21_t + $pph21;
                $pph22_t = $pph22_t + $pph22;
                $pph23_t = $pph23_t + $pph23;
                $pph4_t = $pph4_t + $pph4;
                $terima_t = $terima_t + $terima;
                $setor_t = $setor_t + $setor;
            @endphp
            <TR>
                <TD align="left">{{ $bukti }}</TD>
                <TD align="left">{{ tanggal_indonesia($tanggal) }}</TD>
                <TD align="left">{{ $ket }}</TD>
                <TD align="right">{{ rupiah($ppn) }}</TD>
                <TD align="right">{{ rupiah($pph21) }}</TD>
                <TD align="right">{{ rupiah($pph22) }}</TD>
                <TD align="right">{{ rupiah($pph23) }}</TD>
                <TD align="right">{{ rupiah($pph4) }}</TD>
                <TD align="right">{{ rupiah($terima) }}</TD>
                <TD align="right">{{ rupiah($setor) }}</TD>
                <TD align="right">{{ rupiah($jumlah) }}</TD>
            </TR>
        @endforeach
        <TR>
            <TD colspan="3" align="right">Jumlah bulan {{ Bulan($bulan) }}</TD>
            <TD align="right">{{ rupiah($ppn_t) }}</TD>
            <TD align="right">{{ rupiah($pph21_t) }}</TD>
            <TD align="right">{{ rupiah($pph22_t) }}</TD>
            <TD align="right">{{ rupiah($pph23_t) }}</TD>
            <TD align="right">{{ rupiah($pph4_t) }}</TD>
            <TD align="right">{{ rupiah($terima_t) }}</TD>
            <TD align="right">{{ rupiah($setor_t) }}</TD>
            <TD align="right">{{ rupiah($terima_t - $setor_t) }}</TD>
        </TR>
        <TR>
            <TD colspan="3" align="right">Jumlah sampai bulan Sebelumnya</TD>
            <TD align="right">{{ rupiah($ppn_l) }}</TD>
            <TD align="right">{{ rupiah($pph21_l) }}</TD>
            <TD align="right">{{ rupiah($pph22_l) }}</TD>
            <TD align="right">{{ rupiah($pph23_l) }}</TD>
            <TD align="right">{{ rupiah($pph4_l) }}</TD>
            <TD align="right">{{ rupiah($terima_l) }}</TD>
            <TD align="right">{{ rupiah($setor_l) }}</TD>
            <TD align="right">{{ rupiah($jumlah_l) }}</TD>
        </TR>
        <TR>
            <TD colspan="3" align="right">Jumlah sampai bulan {{ Bulan($bulan) }}</TD>
            <TD align="right">{{ rupiah($ppn_l + $ppn_t) }}</TD>
            <TD align="right">{{ rupiah($pph21_l + $pph21_t) }}</TD>
            <TD align="right">{{ rupiah($pph22_l + $pph22_t) }}</TD>
            <TD align="right">{{ rupiah($pph23_l + $pph23_t) }}</TD>
            <TD align="right">{{ rupiah($pph4_l + $pph4_t) }}</TD>
            <TD align="right">{{ rupiah($terima_l + $terima_t) }}</TD>
            <TD align="right">{{ rupiah($setor_l + $setor_t) }}</TD>
            <TD align="right">{{ rupiah($jumlah_l + $terima_t - $setor_t) }}</TD>
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
