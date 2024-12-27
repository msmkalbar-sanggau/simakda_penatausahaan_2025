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
            <td style="text-align: center"><b>REGISTER CP</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    <TABLE style="border-collapse:collapse;font-size:14px" border="1" cellspacing="' . $spasi . '"
        cellpadding={{ $enter }} width="100%">
        <THEAD>
            <TR>
                <TD bgcolor="#CCCCCC" rowspan="4" align="center">NO</TD>
                <TD bgcolor="#CCCCCC" rowspan="4" align="center">Tanggal CP</TD>
                <TD bgcolor="#CCCCCC" rowspan="4" align="center">No STS</TD>
                <TD bgcolor="#CCCCCC" rowspan="4" align="center">No SP2D</TD>
                <TD bgcolor="#CCCCCC" rowspan="4" align="center">Uraian</TD>
                <TD bgcolor="#CCCCCC" colspan="11" align="center">CP</TD>
                <TD bgcolor="#CCCCCC" rowspan="4" align="center">Jumlah</TD>
            </TR>
            <TR>
                <TD colspan="6" bgcolor="#CCCCCC" align="center">LS</TD>
                <TD colspan="5" bgcolor="#CCCCCC" align="center">UP/GU/TU</TD>
            </TR>
            <TR>
                <TD colspan="2" bgcolor="#CCCCCC" align="center">Gaji</TD>
                <TD colspan="3" bgcolor="#CCCCCC" align="center">Barang dan Jasa</TD>
                <TD rowspan="2" bgcolor="#CCCCCC" align="center">Pihak Ketiga Lainnya</TD>
                <TD rowspan="2" bgcolor="#CCCCCC" align="center">UP</TD>
                <TD rowspan="2" bgcolor="#CCCCCC" align="center">GU</TD>
                <TD colspan="3" bgcolor="#CCCCCC" align="center">TU</TD>
            </TR>
            <TR>
                <TD bgcolor="#CCCCCC" align="center">HKPG</TD>
                <TD bgcolor="#CCCCCC" align="center">Pot. Lain</TD>

                <TD bgcolor="#CCCCCC" align="center">Pegawai</TD>
                <TD bgcolor="#CCCCCC" align="center">Barang</TD>
                <TD bgcolor="#CCCCCC" align="center">Modal</TD>

                <TD bgcolor="#CCCCCC" align="center">Pegawai</TD>
                <TD bgcolor="#CCCCCC" align="center">Barang</TD>
                <TD bgcolor="#CCCCCC" align="center">Modal</TD>
            </TR>
        </THEAD>
        @php
            $no = 0;
            $hkpg_t = 0;
            $pot_lain_t = 0;
            $cp_t = 0;
            $ls_peg_t = 0;
            $ls_brng_t = 0;
            $ls_modal_t = 0;
            $ls_phl_t = 0;
            $up_gu_peg_t = 0;
            $up_gu_brng_t = 0;
            $up_gu_modal_t = 0;
            $gu_t = 0;
            $total_t = 0;
        @endphp
        @foreach ($rincian as $row)
            @php
                $bukti = $row->no_sts;
                $tanggal = $row->tgl_sts;
                $ket = $row->keterangan;
                $no_sts = $row->no_sts;
                $no_sp2d = $row->no_sp2d;
                $hkpg = $row->hkpg;
                $pot_lain = $row->pot_lain;
                $cp = $row->cp;
                $ls_peg = $row->ls_peg;
                $ls_brng = $row->ls_brng;
                $ls_modal = $row->ls_modal;
                $ls_phl = $row->ls_phl;
                $up_gu_peg = $row->up_gu_peg;
                $up_gu_brng = $row->up_gu_brng;
                $up_gu_modal = $row->up_gu_modal;
                $gu = $row->gu;
                $total = $row->total;
                $hkpg_t = $hkpg_t + $hkpg;
                $pot_lain_t = $pot_lain_t + $pot_lain;
                $cp_t = $cp_t + $cp;
                $ls_peg_t = $ls_peg_t + $ls_peg;
                $ls_brng_t = $ls_brng_t + $ls_brng;
                $ls_modal_t = $ls_modal_t + $ls_modal;
                $ls_phl_t = $ls_phl_t + $ls_phl;
                $up_gu_peg_t = $up_gu_peg_t + $up_gu_peg;
                $up_gu_brng_t = $up_gu_brng_t + $up_gu_brng;
                $up_gu_modal_t = $up_gu_modal_t + $up_gu_modal;
                $total_t = $total_t + $total;
                $gu_t = $gu_t + $gu;
                $no = $no + 1;
            @endphp

            <TR>
                <TD align="left">{{ $no }}</TD>
                <TD align="left">{{ tanggal_indonesia($tanggal) }}</TD>
                <TD align="left">{{ $no_sts }}</TD>
                <TD align="left">{{ $no_sp2d }}</TD>
                <TD align="left">{{ $ket }}</TD>
                <TD align="right">{{ rupiah($hkpg) }}</TD>
                <TD align="right">{{ rupiah($pot_lain) }}</TD>

                <TD align="right">{{ rupiah($ls_peg) }}</TD>
                <TD align="right">{{ rupiah($ls_brng) }}</TD>
                <TD align="right">{{ rupiah($ls_modal) }}</TD>

                <TD align="right">{{ rupiah($ls_phl) }}</TD>

                <TD align="right">{{ rupiah(0) }}</TD>
                <TD align="right">{{ rupiah($gu) }}</TD>
                <TD align="right">{{ rupiah($up_gu_peg) }}</TD>
                <TD align="right">{{ rupiah($up_gu_brng) }}</TD>
                <TD align="right">{{ rupiah($up_gu_modal) }}</TD>
                <TD align="right">{{ rupiah($total) }}</TD>
            </TR>
        @endforeach

        <TR>
            <TD colspan="5" align="right">Jumlah bulan {{ Bulan($bulan) }}</TD>
            <TD align="right">{{ rupiah($hkpg_t) }}</TD>
            <TD align="right">{{ rupiah($pot_lain_t) }}</TD>

            <TD align="right">{{ rupiah($ls_peg_t) }}</TD>
            <TD align="right">{{ rupiah($ls_brng_t) }}</TD>
            <TD align="right">{{ rupiah($ls_modal_t) }}</TD>
            <TD align="right">{{ rupiah($ls_phl_t) }}</TD>
            <TD align="right">{{ rupiah(0) }}</TD>
            <TD align="right">{{ rupiah($gu_t) }}</TD>
            <TD align="right">{{ rupiah($up_gu_peg_t) }}</TD>
            <TD align="right">{{ rupiah($up_gu_brng_t) }}</TD>
            <TD align="right">{{ rupiah($up_gu_modal_t) }}</TD>
            <TD align="right">{{ rupiah($total_t) }}</TD>
        </TR>

        @foreach ($lalu as $rincian)
            @php
                $hkpg_l = $rincian->hkpg_l;
                $pot_lain_l = $rincian->pot_lain_l;
                $cp_l = $rincian->cp_l;
                $ls_peg_l = $rincian->ls_peg_l;
                $ls_brng_l = $rincian->ls_brng_l;
                $ls_modal_l = $rincian->ls_modal_l;
                $ls_phl_l = $rincian->ls_phl_l;
                $up_gu_peg_l = $rincian->up_gu_peg_l;
                $up_gu_brng_l = $rincian->up_gu_brng_l;
                $up_gu_modal_l = $rincian->up_gu_modal_l;
                $gu_l = $rincian->gu_l;
                $total_l = $rincian->total_l;
            @endphp
            <TR>
                <TD colspan="5" align="right">Jumlah sampai bulan Sebelumnya</TD>
                <TD align="right">{{ rupiah($hkpg_l) }}</TD>
                <TD align="right">{{ rupiah($pot_lain_l) }}</TD>

                <TD align="right">{{ rupiah($ls_peg_l) }}</TD>
                <TD align="right">{{ rupiah($ls_brng_l) }}</TD>
                <TD align="right">{{ rupiah($ls_modal_l) }}</TD>
                <TD align="right">{{ rupiah($ls_phl_l) }}</TD>
                <TD align="right">{{ rupiah(0) }}</TD>
                <TD align="right">{{ rupiah($gu_l) }}</TD>
                <TD align="right">{{ rupiah($up_gu_peg_l) }}</TD>
                <TD align="right">{{ rupiah($up_gu_brng_l) }}</TD>
                <TD align="right">{{ rupiah($up_gu_modal_l) }}</TD>
                <TD align="right">{{ rupiah($total_l) }}</TD>
            </TR>
            <TR>
                <TD colspan="5" align="right">Jumlah sampai bulan {{ Bulan($bulan) }}</TD>
                <TD align="right">{{ rupiah($hkpg_t + $hkpg_l) }}</TD>
                <TD align="right">{{ rupiah($pot_lain_t + $pot_lain_l) }}</TD>

                <TD align="right">{{ rupiah($ls_peg_t + $ls_peg_l) }}</TD>
                <TD align="right">{{ rupiah($ls_brng_t + $ls_brng_l) }}</TD>
                <TD align="right">{{ rupiah($ls_modal_t + $ls_modal_l) }}</TD>
                <TD align="right">{{ rupiah($ls_phl_t + $ls_phl_l) }}</TD>
                <TD align="right">{{ rupiah(0) }}</TD>
                <TD align="right">{{ rupiah($gu_t + $gu_l) }}</TD>
                <TD align="right">{{ rupiah($up_gu_peg_t + $up_gu_peg_l) }}</TD>
                <TD align="right">{{ rupiah($up_gu_brng_t + $up_gu_brng_l) }}</TD>
                <TD align="right">{{ rupiah($up_gu_modal_t + $up_gu_modal_l) }} </TD>
                <TD align="right">{{ rupiah($total_t + $total_l) }}</TD>
            </TR>
        @endforeach

    </table>
    {{-- @php
        for ($i = 0; $i <= $enter; $i++) {
            echo '<br>';
        }
    @endphp --}}
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
