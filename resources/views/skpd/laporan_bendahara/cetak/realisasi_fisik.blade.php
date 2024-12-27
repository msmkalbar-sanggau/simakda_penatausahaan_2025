<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Realisasi Fisik</title>
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
            <td style="text-align: center"><b>REALISASI FISIK</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    <table style='border-collapse:collapse;font-family: Open Sans; font-size:12px' width='90%' align='center'
        border='1' cellspacing='0' cellpadding={{ $enter }}>
        <thead>
            <tr>
                <td align='center' bgcolor='#CCCCCC'><b>KODE REKENING</b></td>
                <td align='center' bgcolor='#CCCCCC'><b>URAIAN</b></td>
                <td align='center' bgcolor='#CCCCCC'><b>ANGARAN</b></td>
                <td align='center' bgcolor='#CCCCCC'><b>REALISASI</b></td>
                <td align='center' bgcolor='#CCCCCC'><b>SISA ANGGARAN</b></td>
                <td align='center' bgcolor='#CCCCCC'><b>%</b></td>
            </tr>
        </thead>
        <tbody>

            @php
                $tot_nilai = 0;
                $tot_realisasi = 0;
                $saldo = 0;
                $tot_persen = 0;
            @endphp
            @foreach ($rincian as $data)
                @php
                    $rekening = $data->rek;
                    $urut = $data->urut;
                    $uraian = $data->uraian;
                    $nilai = $data->nilai;
                    $realisasi = $data->realisasi;
                    $sisa = $data->sisa;
                    $persen = $data->persen;

                    $nilai1 = empty($nilai) || $nilai == 0 ? rupiah(0) : rupiah($nilai);
                    $realisasi1 = empty($realisasi) || $realisasi == 0 ? rupiah(0) : rupiah($realisasi);
                    $sisa1 = empty($sisa) || $sisa == 0 ? rupiah(0) : rupiah($sisa);
                    $persen1 = empty($persen) || $persen == 0 ? rupiah(0) : rupiah($persen);
                @endphp
                @if (strlen($rekening) == 7)
                    @php
                        $tot_nilai = $tot_nilai + $nilai;
                        $tot_realisasi = $tot_realisasi + $realisasi;
                    @endphp
                    @if ($tot_nilai != 0 || $tot_realisasi != 0)
                        @php
                            $tot_persen = ($tot_realisasi / $tot_nilai) * 100;
                        @endphp
                    @else
                        @php
                            $tot_persen = 0;
                        @endphp
                    @endif
                    <tr>
                        <td align='left'><b>{{ $urut }}</b></td>
                        <td align='left'><b>{{ $uraian }}</b></td>
                        <td align='right'><b>{{ $nilai1 }}</b></td>
                        <td align='right'><b>{{ $realisasi1 }}</b></td>
                        <td align='right'><b>{{ $sisa1 }}</b></td>
                        <td align='right'><b>{{ $persen1 }}</b></td>
                    </tr>
                @elseif (strlen($rekening) == 12 || strlen($rekening) == 15)
                    <tr>
                        <td align='left'><b>{{ $urut }}</b></td>
                        <td align='left'><b>{{ $uraian }}</b></td>
                        <td align='right'><b>{{ $nilai1 }}</b></td>
                        <td align='right'><b>{{ $realisasi1 }}</b></td>
                        <td align='right'><b>{{ $sisa1 }}</b></td>
                        <td align='right'><b>{{ $persen1 }}</b></td>
                    </tr>
                @else
                    @php
                        $saldo = $saldo - $realisasi;
                        $sal = empty($saldo) || $saldo == 0 ? '' : rupiah($saldo);
                    @endphp
                    <tr>
                        <td align='left'>{{ $urut }}</td>
                        <td align='left'>{{ $uraian }}</td>
                        <td align='right'>{{ $nilai1 }}</td>
                        <td align='right'>{{ $realisasi1 }}</td>
                        <td align='right'>{{ $sisa1 }}</td>
                        <td align='right'>{{ $persen1 }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td colspan='2' align='center'>Total</td>
                <td align='right'>{{ rupiah($tot_nilai) }}</td>
                <td align='right'>{{ rupiah($tot_realisasi) }}</td>
                <td align='right'>{{ rupiah($tot_nilai - $tot_realisasi) }}</td>
                <td align='right'>{{ rupiah($tot_persen) }}</td>
            </tr>
        </tbody>
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
