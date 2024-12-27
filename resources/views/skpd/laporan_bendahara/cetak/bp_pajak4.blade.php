<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BP Pajak</title>
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
        border="0" cellspacing="1" cellpadding="2">
        <tr>
            <td style="text-align: center"><b>BUKU PEMBANTU PAJAK</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    {{-- isi --}}
    <table style="border-collapse:collapse; font-size:12px" width="100%" border="1" cellspacing="0"
        cellpadding={{ $enter }} align=center>
        <thead>
            <tr>
                <td rowspan="2" width="5" align="center">NO</td>
                <td rowspan="2" width="10" align="center">Tanggal</td>
                <td rowspan="2" width="20" align="center">Uraian</td>
                <td colspan="5" width="20" align="center">Potongan Belanja Barang dan Modal</td>
                <td rowspan="2" width="15" align="center">Pemotongan (Rp)</td>
                <td rowspan="2" width="15" align="center">Penyetoran (Rp)</td>
                <td rowspan="2" width="15" align="center">Saldo</td>
            </tr>
            <tr>
                <td width="15" align="center">PPh Pasal 21</td>
                <td width="15" align="center">PPh Pasal 22</td>
                <td width="15" align="center">PPh Pasal 23</td>
                <td width="15" align="center">PPn</td>
                <td width="15" align="center">Lain-Lain</td>
            </tr>
        </thead>
        <tr>
            <td align="center">1</td>
            <td align="center">2</td>
            <td align="center">3</td>
            <td align="center">4</td>
            <td align="center">5</td>
            <td align="center">6</td>
            <td align="center">7</td>
            <td align="center">8</td>
            <td align="center">9=(4+5+6+7+8)</td>
            <td align="center">10</td>
            <td align="center">11=(9-10)</td>
        </tr>

        @if ($pilihan2 == '4') {{-- Global --}}
            @php
                $jum_pph21 = 0;
                $jum_pph22 = 0;
                $jum_pph23 = 0;
                $jum_pphn = 0;
                $jum_lain = 0;
                $jum_pot = 0;
                $jum_setor = 0;
                $jum_saldo = 0;
                $ii = 0;
            @endphp
            @foreach ($rincian as $rinci)
                @php
                    $bulan = $rinci->bulan;
                    $pph21 = $rinci->pph21;
                    $pph22 = $rinci->pph22;
                    $pph23 = $rinci->pph23;
                    $pphn = $rinci->pphn;
                    $lain = $rinci->lain;
                    $pot = $rinci->pot;
                    $setor = $rinci->setor;
                    $saldo = $rinci->saldo;
                    $ii = $ii + 1;
                    $pph21_1 = empty($pph21) || $pph21 == 0 ? rupiah(0) : rupiah($pph21);
                    $pph22_1 = empty($pph22) || $pph22 == 0 ? rupiah(0) : rupiah($pph22);
                    $pph23_1 = empty($pph23) || $pph23 == 0 ? rupiah(0) : rupiah($pph23);
                    $pphn_1 = empty($pphn) || $pphn == 0 ? rupiah(0) : rupiah($pphn);
                    $lain_1 = empty($lain) || $lain == 0 ? rupiah(0) : rupiah($lain);
                    $pot_1 = empty($pot) || $pot == 0 ? rupiah(0) : rupiah($pot);
                    $setor_1 = empty($setor) || $setor == 0 ? rupiah(0) : rupiah($setor);
                    $saldo_1 = empty($saldo) || $saldo == 0 ? rupiah(0) : rupiah($saldo);
                    $jum_pph21 = $jum_pph21 + $pph21;
                    $jum_pph22 = $jum_pph22 + $pph22;
                    $jum_pph23 = $jum_pph23 + $pph23;
                    $jum_pphn = $jum_pphn + $pphn;
                    $jum_lain = $jum_lain + $lain;
                    $jum_pot = $jum_pot + $pot;
                    $jum_setor = $jum_setor + $setor;
                    $jum_saldo = $jum_saldo + $saldo;
                @endphp
                <tr>
                    <td align="center">{{ $ii }}</td>
                    <td align="center"></td>
                    <td align="left">{{ bulan($bulan) }}</td>
                    <td align="right">{{ $pph21_1 }}</td>
                    <td align="right">{{ $pph22_1 }}</td>
                    <td align="right">{{ $pph23_1 }}</td>
                    <td align="right">{{ $pphn_1 }}</td>
                    <td align="right">{{ $lain_1 }}</td>
                    <td align="right">{{ $pot_1 }}</td>
                    <td align="right">{{ $setor_1 }}</td>
                    <td align="right">{{ $saldo_1 }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" align="center">JUMLAH</td>
                <td align="right">{{ rupiah($jum_pph21) }}</td>
                <td align="right">{{ rupiah($jum_pph22) }}</td>
                <td align="right">{{ rupiah($jum_pph23) }}</td>
                <td align="right">{{ rupiah($jum_pphn) }}</td>
                <td align="right">{{ rupiah($jum_lain) }}</td>
                <td align="right">{{ rupiah($jum_pot) }}</td>
                <td align="right">{{ rupiah($jum_setor) }}</td>
                <td align="right">{{ rupiah($jum_saldo) }}</td>
            </tr>
        @else
            <TR>
                <TD align="center"></TD>
                <TD align="center"></TD>
                <TD align="center">Saldo s/d Bulan Lalu</TD>
                <TD align="right">{{ rupiah($salpph21) }}</TD>
                <TD align="right">{{ rupiah($salpph22) }}</TD>
                <TD align="right">{{ rupiah($salpph23) }}</TD>
                <TD align="right">{{ rupiah($salpphn) }}</TD>
                <TD align="right">{{ rupiah($sallain) }}</TD>
                <TD align="right">{{ rupiah($salpot) }}</TD>
                <TD align="right">{{ rupiah($salset) }}</TD>
                <TD align="right">{{ rupiah($saldopjk) }}</TD>
            </TR>
            <TR>
                <TD align="center">&nbsp;</TD>
                <TD align="center"></TD>
                <TD align="center"></TD>
                <TD align="center"></TD>
                <TD align="center"></TD>
                <TD align="center"></TD>
                <TD align="center"></TD>
                <TD align="center"></TD>
                <TD align="center"></TD>
                <TD align="center"></TD>
                <TD align="center"></TD>
            </TR>
            @php
                $jum_pph21 = 0;
                $jum_pph22 = 0;
                $jum_pph23 = 0;
                $jum_pphn = 0;
                $jum_lain = 0;
                $jum_pot = 0;
                $jum_setor = 0;
                $jum_saldo = 0;
                $saldo = $saldopjk;
            @endphp
            @foreach ($rincian as $row)
                @php
                    $no_bukti = $row->bku;
                    $tgl_bukti = $row->tgl_bukti;
                    $ket = $row->ket;
                    $pph21 = $row->pph21;
                    $pph22 = $row->pph22;
                    $pph23 = $row->pph23;
                    $pphn = $row->pphn;
                    $lain = $row->lain;
                    $pot = $row->pot;
                    $setor = $row->setor;
                    $saldo = $saldo + $pot - $setor;
                    $pph21_1 = empty($pph21) || $pph21 == 0 ? rupiah(0) : rupiah($pph21);
                    $pph22_1 = empty($pph22) || $pph22 == 0 ? rupiah(0) : rupiah($pph22);
                    $pph23_1 = empty($pph23) || $pph23 == 0 ? rupiah(0) : rupiah($pph23);
                    $pphn_1 = empty($pphn) || $pphn == 0 ? rupiah(0) : rupiah($pphn);
                    $lain_1 = empty($lain) || $lain == 0 ? rupiah(0) : rupiah($lain);
                    $pot_1 = empty($pot) || $pot == 0 ? rupiah(0) : rupiah($pot);
                    $setor_1 = empty($setor) || $setor == 0 ? rupiah(0) : rupiah($setor);
                    $saldo_1 = empty($saldo) || $saldo == 0 ? rupiah(0) : rupiah($saldo);
                    $jum_pph21 = $jum_pph21 + $pph21;
                    $jum_pph22 = $jum_pph22 + $pph22;
                    $jum_pph23 = $jum_pph23 + $pph23;
                    $jum_pphn = $jum_pphn + $pphn;
                    $jum_lain = $jum_lain + $lain;
                    $jum_pot = $jum_pot + $pot;
                    $jum_setor = $jum_setor + $setor;
                    $jum_saldo = $jum_saldo + $saldo;
                @endphp
                <TR>
                    <TD align="center">{{ $no_bukti }}</TD>
                    <TD align="center">{{ tanggal_indonesia($tgl_bukti) }}</TD>
                    <TD align="left">{{ $ket }}</TD>
                    <TD align="right">{{ $pph21_1 }}</TD>
                    <TD align="right">{{ $pph22_1 }}</TD>
                    <TD align="right">{{ $pph23_1 }}</TD>
                    <TD align="right">{{ $pphn_1 }}</TD>
                    <TD align="right">{{ $lain_1 }}</TD>
                    <TD align="right">{{ $pot_1 }}</TD>
                    <TD align="right">{{ $setor_1 }}</TD>
                    <TD align="right">{{ $saldo_1 }}</TD>
                </TR>
            @endforeach
            <TR>
                <TD colspan="3" align="center">JUMLAH</TD>
                <TD align="right">{{ rupiah($jum_pph21) }}</TD>
                <TD align="right">{{ rupiah($jum_pph22) }}</TD>
                <TD align="right">{{ rupiah($jum_pph23) }}</TD>
                <TD align="right">{{ rupiah($jum_pphn) }}</TD>
                <TD align="right">{{ rupiah($jum_lain) }}</TD>
                <TD align="right">{{ rupiah($jum_pot) }}</TD>
                <TD align="right">{{ rupiah($jum_setor) }}</TD>
                <TD align="right">{{ rupiah($saldo) }}</TD>
            </TR>
        @endif



    </table>
    {{-- isi --}}
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
