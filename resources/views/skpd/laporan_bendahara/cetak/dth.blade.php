<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DTH SKPD</title>
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
            <td style="text-align: center"><b>DAFTAR TRANSAKSI HARIAN (DTH)</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    <table style="border-collapse:collapse; font-size:10px" width="100%" border="1" cellspacing="2"
        cellpadding={{ $enter }} align="center">
        <thead>
            <tr>
                <td rowspan="2" width="80" bgcolor="#CCCCCC" align="center">No.</td>
                <td colspan="2" width="90" bgcolor="#CCCCCC" align="center">SPM/SPD</td>
                <td colspan="2" width="150" bgcolor="#CCCCCC" align="center">SP2D </td>
                <td rowspan="2" width="150" bgcolor="#CCCCCC" align="center">Akun Belanja</td>
                <td colspan="3" width="150" bgcolor="#CCCCCC" align="center">Potongan Pajak</td>
                <td rowspan="2" width="150" bgcolor="#CCCCCC" align="center">NPWP</td>
                <td rowspan="2" width="150" bgcolor="#CCCCCC" align="center">Nama Rekanan</td>
                <td rowspan="2" width="80" bgcolor="#CCCCCC" align="center">NTPN</td>
                <td rowspan="2" width="80" bgcolor="#CCCCCC" align="center">No Billing</td>
                <td rowspan="2" width="150" bgcolor="#CCCCCC" align="center">Ket</td>
            </tr>
            <tr>
                <td width="90" bgcolor="#CCCCCC" align="center">No. SPM</td>
                <td width="150" bgcolor="#CCCCCC" align="center">Nilai Belanja(Rp)</td>
                <td width="150" bgcolor="#CCCCCC" align="center">No. SP2D </td>
                <td width="150" bgcolor="#CCCCCC" align="center">Nilai Belanja (Rp)</td>
                <td width="150" bgcolor="#CCCCCC" align="center">Akun Potongan</td>
                <td width="150" bgcolor="#CCCCCC" align="center">Jenis</td>
                <td width="150" bgcolor="#CCCCCC" align="center">jumlah (Rp)</td>
            </tr>
        </thead>
        @php
            $nomor = 0;
            $tot_nilai = 0;
            $tot_nilai_belanja = 0;
            $tot_nilai_pot = 0;
        @endphp
        @foreach ($rincian as $rincian)
            @if ($rincian->jns_spp == '2')
                @php
                    $nilai_belanja = $rincian->nilai;
                @endphp
            @else
                @php
                    $nilai_belanja = $rincian->nilai_belanja;
                @endphp
            @endif

            @if ($rincian->kd_rek6 == '210106010001')
                @php
                    $kd_rek6 = '210106010001';
                    $jenis_pajak = 'PPn';
                @endphp
            @elseif($rincian->kd_rek6 == '210105010001')
                @php
                    $kd_rek6 = '210105010001';
                    $jenis_pajak = 'PPh 21';
                @endphp
            @elseif($rincian->kd_rek6 == '210105020001')
                @php
                    $kd_rek6 = '210105020001';
                    $jenis_pajak = 'PPh 22';
                @endphp
            @elseif($rincian->kd_rek6 == '210105030001')
                @php
                    $kd_rek6 = '210105030001';
                    $jenis_pajak = 'PPh 23';
                @endphp
            @elseif($rincian->kd_rek6 == '210109010001')
                @php
                    $kd_rek6 = '210109010001';
                    $jenis_pajak = 'PPh Pasal 4 Ayat 2';
                @endphp
            @endif
            @if ($rincian->urut == 1)
                <tr>
                    <td width="80" valign="top" align="center">{{ ++$nomor }}</td>
                    <td width="90" valign="top">{{ $rincian->no_spm }}</td>
                    <td width="150" valign="top" align="right">{{ rupiah($rincian->nilai) }}</td>
                    <td width="150" valign="top">{{ $rincian->no_sp2d }}</td>
                    <td width="150" valign="top" align="right">{{ rupiah($nilai_belanja) }}</td>
                    <td width="150" align="right"></td>
                    <td width="150" align="left"></td>
                    <td width="150" align="left"></td>
                    <td width="150" align="left"></td>
                    <td width="150" align="left"></td>
                    <td width="150" align="left"></td>
                    <td width="150" align="left"></td>
                    <td width="150" valign="top" align="left"> </td>
                </tr>
            @else
                <TR>
                    <TD align="right" style="border-top:hidden;"></TD>
                    <TD align="right" style="border-top:hidden;"></TD>
                    <TD align="right" style="border-top:hidden;"></TD>
                    <TD align="right" style="border-top:hidden;"></TD>
                    <TD align="right" style="border-top:hidden;"></TD>
                    <TD width="150" valign="top" align="left" style="border-top:hidden;">
                        {{ $rincian->kode_belanja }}</TD>
                    <TD width="150" valign="top" align="center" style="border-top:hidden;">
                        {{ $rincian->kd_rek6 }}</TD>
                    <TD width="150" valign="top" align="left" style="border-top:hidden;">{{ $jenis_pajak }}
                    </TD>
                    <TD width="150" valign="top" align="right" style="border-top:hidden;">
                        {{ rupiah($rincian->nilai_pot) }}</TD>
                    <TD width="150" valign="top" align="left" style="border-top:hidden;">
                        {{ $rincian->npwp }}</TD>
                    <TD width="150" valign="top" align="left" style="border-top:hidden;">
                        {{ $rincian->nmrekan }}</TD>
                    <TD align="right" style="border-top:hidden;">{{ $rincian->ntpn }}</TD>
                    <TD align="right" style="border-top:hidden;">{{ $rincian->ebilling }}</TD>
                    <TD style="border-top:hidden;" width="150" valign="top" align="left">{{ $rincian->ket }}
                    </TD>
                </TR>
            @endif
            @php
                $tot_nilai = $tot_nilai + $rincian->nilai;
                $tot_nilai_belanja = $tot_nilai_belanja + $nilai_belanja;
                $tot_nilai_pot = $tot_nilai_pot + $rincian->nilai_pot;
            @endphp
        @endforeach
        <TR>
            <TD width="90" bgcolor="#CCCCCC" align="center">Total</TD>
            <TD width="90" bgcolor="#CCCCCC" align="center"></TD>
            <TD width="90" bgcolor="#CCCCCC" align="right">{{ rupiah($tot_nilai) }}</td>
            <TD width="90" bgcolor="#CCCCCC" align="center"></TD>
            <TD width="90" bgcolor="#CCCCCC" align="right">{{ rupiah($tot_nilai_belanja) }}</td>
            <TD width="90" bgcolor="#CCCCCC" align="center"></TD>
            <TD width="150" bgcolor="#CCCCCC" align="center"></TD>
            <TD width="150" bgcolor="#CCCCCC" align="center"></TD>
            <TD width="150" bgcolor="#CCCCCC" align="right">{{ rupiah($tot_nilai_pot) }}</td>
            <TD width="150" bgcolor="#CCCCCC" align="center"></TD>
            <TD width="150" bgcolor="#CCCCCC" align="center"></TD>
            <TD width="150" bgcolor="#CCCCCC" align="center"></TD>
            <TD width="150" bgcolor="#CCCCCC" align="center"></TD>
            <TD width="150" bgcolor="#CCCCCC" align="center"></TD>
        </TR>
    </table>
    {{-- @php
        for ($i = 0; $i <= $enter; $i++) {
            echo '<br>';
        }
    @endphp --}}
    <br>
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
