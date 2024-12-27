<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kartu Kendali Sub Kegiatan</title>
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
    <hr>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>KARTU KENDALI SUB KEGIATAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    <TABLE width="100%" style="font-size:12px;">

        <TR>
            <TD align="left" width="25%">Program </TD>
            <TD align="left" width="75%">: {{ substr($subkegiatan, 0, 7) }} -
                {{ cari_nama(substr($subkegiatan, 0, 7), 'ms_program', 'kd_program', 'nm_program') }}</TD>
        </TR>
        <TR>
            <TD align="left" width="25%">Kegiatan </TD>
            <TD align="left" width="75%">: {{ substr($subkegiatan, 0, 12) }} -
                {{ cari_nama(substr($subkegiatan, 0, 12), 'ms_kegiatan', 'kd_kegiatan', 'nm_kegiatan') }}</TD>
        </TR>
        <TR>
            <TD align="left" width="25%">Sub Kegiatan </TD>
            <TD align="left" width="75%">: {{ $subkegiatan }} -
                {{ cari_nama($subkegiatan, 'ms_sub_kegiatan', 'kd_sub_kegiatan', 'nm_sub_kegiatan') }}</TD>
        </TR>
    </TABLE>
    <TABLE style="border-collapse:collapse;font-size:14px" border="1" cellspacing="' . $spasi . '"
        cellpadding="' . $spasi . '" width="100%">
        <thead>
            <tr>
                <td rowspan ='2' align='center' bgcolor='#CCCCCC'><b>NO URUT</b></td>
                <td rowspan ='2' align='center' bgcolor='#CCCCCC'><b>KODE REKENING</b></td>
                <td rowspan ='2' align='center' bgcolor='#CCCCCC'><b>URAIAN</b></td>
                <td colspan ='2' align='center' bgcolor='#CCCCCC'><b>PAGU</b></td>
                <td colspan ='3' align='center' bgcolor='#CCCCCC'><b>REALISASI</b></td>
                <td rowspan ='2' align='center' bgcolor='#CCCCCC'><b>SISA PAGU</b></td>
            </tr>
            <tr>
                <td align='center' bgcolor='#CCCCCC'><b>PENETAPAN</b></td>
                <td align='center' bgcolor='#CCCCCC'><b>PERUBAHAN</b></td>
                <td align='center' bgcolor='#CCCCCC'><b>LS</b></td>
                <td align='center' bgcolor='#CCCCCC'><b>UP/GU</b></td>
                <td align='center' bgcolor='#CCCCCC'><b>TU</b></td>
            </tr>
        </thead>
        @php
            $no = 0;
            $nilai12 = 0;
            $nilai_ubah12 = 0;
            $real_ls1 = 0;
            $real_up1 = 0;
            $real_tu1 = 0;
            $sisa1 = 0;
            $real_ls12 = 0;
            $real_up12 = 0;
            $real_tu12 = 0;
            $sisa12 = 0;
        @endphp
        @foreach ($rincian as $row)
            @php
                $no = $no + 1;
                $kd_rek6 = $row->kd_rek6;
                $nilai = $row->nilai;
                $nilai_ubah = $row->nilai_ubah;
                $uraian = $row->uraian;
                $real_ls = $row->real_ls;
                $real_up = $row->real_up;
                $real_tu = $row->real_tu;
                $sisa = $row->sisa;

                $nilai1 = empty($nilai) || $nilai == 0 ? '' : rupiah($nilai);
                $nilai_ubah1 = empty($nilai_ubah) || $nilai_ubah == 0 ? rupiah(0) : rupiah($nilai_ubah);
                $real_ls1 = empty($real_ls) || $real_ls == 0 ? rupiah(0) : rupiah($real_ls);
                $real_up1 = empty($real_up) || $real_up == 0 ? rupiah(0) : rupiah($real_up);
                $real_tu1 = empty($real_tu) || $real_tu == 0 ? rupiah(0) : rupiah($real_tu);
                $sisa1 = empty($sisa) || $sisa == 0 ? rupiah(0) : rupiah($sisa);

                $nilai12 = $nilai12 + $nilai;
                $nilai_ubah12 = $nilai_ubah12 + $nilai_ubah;
                $real_ls12 = $real_ls12 + $row->real_ls;
                $real_up12 = $real_up12 + $row->real_up;
                $real_tu12 = $real_tu12 + $row->real_tu;
                $sisa12 = $sisa12 + $row->sisa;
            @endphp
            @if ($sisa1 < 0)
                <tr>
                    <td bgcolor='#CC3333' align='center'>{{ $no }}</td>
                    <td bgcolor='#CC3333' align='left'>{{ $kd_rek6 }}</td>
                    <td bgcolor='#CC3333' align='left'>{{ $uraian }}</td>
                    <td bgcolor='#CC3333' align='right'>{{ $nilai1 }}</td>
                    <td bgcolor='#CC3333' align='right'>{{ $nilai_ubah1 }}</td>
                    <td bgcolor='#CC3333' align='right'>{{ $real_ls1 }}</td>
                    <td bgcolor='#CC3333' align='right'>{{ $real_up1 }}</td>
                    <td bgcolor='#CC3333' align='right'>{{ $real_tu1 }}</td>
                    <td bgcolor='#CC3333' align='right'>{{ $sisa1 }}</td>
                </tr>
            @else
                <tr>
                    <td align='center'>{{ $no }}</td>
                    <td align='left'>{{ $kd_rek6 }}</td>
                    <td align='left'>{{ $uraian }}</td>
                    <td align='right'>{{ $nilai1 }}</td>
                    <td align='right'>{{ $nilai_ubah1 }}</td>
                    <td align='right'>{{ $real_ls1 }}</td>
                    <td align='right'>{{ $real_up1 }}</td>
                    <td align='right'>{{ $real_tu1 }}</td>
                    <td align='right'>{{ $sisa1 }}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td colspan='3' align='center'>TOTAL</td>
            <td align='right'>{{ rupiah($nilai12) }}</td>
            <td align='right'>{{ rupiah($nilai_ubah12) }}</td>
            <td align='right'>{{ rupiah($real_ls12) }}</td>
            <td align='right'>{{ rupiah($real_up12) }}</td>
            <td align='right'>{{ rupiah($real_tu12) }}</td>
            <td align='right'>{{ rupiah($sisa12) }}</td>
        </tr>

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
