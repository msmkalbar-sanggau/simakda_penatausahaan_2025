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
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>BUKU PEMBANTU PAJAK</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    {{-- isi --}}
    <table style='border-collapse:collapse;' width='100%' align='center' border='1' cellspacing='1'
        cellpadding={{ $enter }}>
        <thead>
            <tr>
                <td rowspan="2" align="center">NO</td>
                <td rowspan="2" align="center">URAIAN</td>
                <td colspan="3" align="center">PENERIMAAN</td>
                <td colspan="3" align="center">PENYETORAN</td>
                <td rowspan="2" align="center">SISA BELUM DISETOR</td>
            </tr>
            <tr>
                <td align="center">S/D BULAN LALU</td>
                <td align="center">BULAN INI</td>
                <td align="center">S/D BULAN INI</td>
                <td align="center">S/D BULAN LALU</td>
                <td align="center">BULAN INI</td>
                <td align="center">S/D BULAN INI</td>
            </tr>
            <tr>
                <td align="center">1</td>
                <td align="center">2</td>
                <td align="center">3</td>
                <td align="center">4</td>
                <td align="center">5</td>
                <td align="center">6</td>
                <td align="center">7</td>
                <td align="center">8</td>
                <td align="center">9</td>
            </tr>
        </thead>
        <tr>
            <td align="center">&nbsp;</td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
        </tr>

        @php
            $i = 0;
            $jum_terima_lalu = 0;
            $jum_terima_ini = 0;
            $jum_terima = 0;
            $jum_setor_lalu = 0;
            $jum_setor_ini = 0;
            $jum_setor = 0;
        @endphp
        @foreach ($rincian as $rinci)
            @php
                $i = $i + 1;
                $uraian = $rinci->nm_rek6;
                $terima_lalu = $rinci->terima_lalu;
                $terima_ini = $rinci->terima_ini;
                $setor_lalu = $rinci->setor_lalu;
                $setor_ini = $rinci->setor_ini;
                $sisa = $terima_lalu + $terima_ini - $setor_lalu - $setor_ini;

                $jum_terima_lalu = $jum_terima_lalu + $terima_lalu;
                $jum_terima_ini = $jum_terima_ini + $terima_ini;
                $jum_terima = $jum_terima + $jum_terima_lalu + $jum_terima_ini;

                $jum_setor_lalu = $jum_setor_lalu + $setor_lalu;
                $jum_setor_ini = $jum_setor_ini + $setor_ini;
                $jum_setor = $jum_setor + $jum_setor_ini + $jum_setor_lalu;
            @endphp
            <tr>
                <td align="center">{{ $i }}</td>
                <td align="left">{{ $uraian }}</td>
                <td align="right">{{ rupiah($terima_lalu) }}</td>
                <td align="right">{{ rupiah($terima_ini) }}</td>
                <td align="right">{{ rupiah($terima_lalu + $terima_ini) }}</td>
                <td align="right">{{ rupiah($setor_lalu) }}</td>
                <td align="right">{{ rupiah($setor_ini) }}</td>
                <td align="right">{{ rupiah($setor_lalu + $setor_ini) }}</td>
                <td align="right">{{ rupiah($sisa) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan ="2" align="center">JUMLAH</td>
            <td align="right">{{ rupiah($jum_terima_lalu) }}</td>
            <td align="right">{{ rupiah($jum_terima_ini) }}</td>
            <td align="right">{{ rupiah($jum_terima_lalu + $jum_terima_ini) }}</td>
            <td align="right">{{ rupiah($jum_setor_lalu) }}</td>
            <td align="right">{{ rupiah($jum_setor_ini) }}</td>
            <td align="right">{{ rupiah($jum_setor_lalu + $jum_setor_ini) }}</td>
            <td align="right">{{ rupiah($jum_terima - $jum_setor) }}</td>
        </tr>

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
