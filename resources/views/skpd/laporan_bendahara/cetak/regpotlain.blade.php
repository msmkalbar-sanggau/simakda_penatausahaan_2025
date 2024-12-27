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
            <td style="text-align: center"><b>REGISTER POTONGAN LAINNYA</b></td>
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
                <TD bgcolor="#CCCCCC" colspan="7" align="center">Potongan Lainnya</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Pemotongan</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Penyetoran</TD>
                <TD bgcolor="#CCCCCC" rowspan="2" align="center">Jumlah</TD>
            </TR>
            <TR>
                <TD bgcolor="#CCCCCC" align="center">IWP</TD>
                <TD bgcolor="#CCCCCC" align="center">TAPERUM</TD>
                <TD bgcolor="#CCCCCC" align="center">PPNPN 1%</TD>
                <TD bgcolor="#CCCCCC" align="center">PPNPN 4%</TD>
                <TD bgcolor="#CCCCCC" align="center">JKK</TD>
                <TD bgcolor="#CCCCCC" align="center">JKM</TD>
                <TD bgcolor="#CCCCCC" align="center">BPJS</TD>
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
                $iwp_l = $row->iwp_l;
                $taperum_l = $row->taperum_l;
                $ppnpn1persen_l = $row->ppnpn1persen_l;
                $ppnpn4persen_l = $row->ppnpn4persen_l;
                $jkk_l = $row->jkk_l;
                $jkm_l = $row->jkm_l;
                $bpjs_l = $row->bpjs_l;
                $terima_l = $row->terima_l;
                $setor_l = $row->setor_l;
                $jumlah_l = $terima_l - $setor_l;
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
                <TD align="right"></TD>
                <TD align="right"></TD>
                <TD align="right">{{ rupiah($jumlah_l) }}</TD>
            </TR>
        @endforeach

        {{-- SEKARANG --}}
        @php
            $jumlah = 0;
            $iwp_t = 0;
            $taperum_t = 0;
            $ppnpn1persen_t = 0;
            $ppnpn4persen_t = 0;
            $jkk_t = 0;
            $jkm_t = 0;
            $bpjs_t = 0;
            $terima_t = 0;
            $setor_t = 0;
        @endphp
        @foreach ($rincian as $rinci)
            @php
                $bukti = $rinci->no_bukti;
                $tanggal = $rinci->tgl_bukti;
                $ket = $rinci->ket;
                $iwp = $rinci->iwp;
                $taperum = $rinci->taperum;
                $ppnpn1persen = $rinci->ppnpn1persen;
                $ppnpn4persen = $rinci->ppnpn4persen;
                $jkk = $rinci->jkk;
                $jkm = $rinci->jkm;
                $bpjs = $rinci->bpjs;
                $terima = $rinci->terima;
                $setor = $rinci->setor;
                $jumlah = $jumlah + $terima - $setor;
                $iwp_t = $iwp_t + $iwp;
                $taperum_t = $taperum_t + $taperum;
                $ppnpn1persen_t = $ppnpn1persen_t + $ppnpn1persen;
                $ppnpn4persen_t = $ppnpn4persen_t + $ppnpn4persen;
                $jkk_t = $jkk_t + $jkk;
                $jkm_t = $jkm_t + $jkm;
                $bpjs_t = $bpjs_t + $bpjs;
                $terima_t = $terima_t + $terima;
                $setor_t = $setor_t + $setor;
            @endphp
            <TR>
                <TD align="left">{{ $bukti }}</TD>
                <TD align="left">{{ tanggal_indonesia($tanggal) }}</TD>
                <TD align="left">{{ $ket }}</TD>
                <TD align="right">{{ rupiah($iwp) }}</TD>
                <TD align="right">{{ rupiah($taperum) }}</TD>
                <TD align="right">{{ rupiah($ppnpn1persen) }}</TD>
                <TD align="right">{{ rupiah($ppnpn4persen) }}</TD>
                <TD align="right">{{ rupiah($jkk) }}</TD>
                <TD align="right">{{ rupiah($jkm) }}</TD>
                <TD align="right">{{ rupiah($bpjs) }}</TD>
                <TD align="right">{{ rupiah($terima) }}</TD>
                <TD align="right">{{ rupiah($setor) }}</TD>
                <TD align="right">{{ rupiah($jumlah) }}</TD>
            </TR>
        @endforeach
        <TR>
            <TD colspan="3" align="right">Jumlah bulan {{ Bulan($bulan) }}</TD>

            <TD align="right">{{ rupiah($iwp_t) }}</TD>
            <TD align="right">{{ rupiah($taperum_t) }}</TD>
            <TD align="right">{{ rupiah($ppnpn1persen_t) }}</TD>
            <TD align="right">{{ rupiah($ppnpn4persen_t) }}</TD>
            <TD align="right">{{ rupiah($jkk_t) }}</TD>
            <TD align="right">{{ rupiah($jkm_t) }}</TD>
            <TD align="right">{{ rupiah($bpjs_t) }}</TD>
            <TD align="right">{{ rupiah($terima_t) }}</TD>
            <TD align="right">{{ rupiah($setor_t) }}</TD>
            <TD align="right">{{ rupiah($terima_t - $setor_t) }}</TD>
        </TR>
        <TR>
            <TD colspan="3" align="right">Jumlah sampai bulan Sebelumnya</TD>
            <TD align="right">{{ rupiah($iwp_l) }}</TD>
            <TD align="right">{{ rupiah($taperum_l) }}</TD>
            <TD align="right">{{ rupiah($ppnpn1persen_l) }}</TD>
            <TD align="right">{{ rupiah($ppnpn4persen_l) }}</TD>
            <TD align="right">{{ rupiah($jkk_l) }}</TD>
            <TD align="right">{{ rupiah($jkm_l) }}</TD>
            <TD align="right">{{ rupiah($bpjs_l) }}</TD>
            <TD align="right">{{ rupiah($terima_l) }}</TD>
            <TD align="right">{{ rupiah($setor_l) }}</TD>
            <TD align="right">{{ rupiah($jumlah_l) }}</TD>
        </TR>
        <TR>
            <TD colspan="3" align="right">Jumlah sampai bulan {{ Bulan($bulan) }}</TD>
            <TD align="right">{{ rupiah($iwp_l + $iwp_t) }}</TD>
            <TD align="right">{{ rupiah($taperum_l + $taperum_t) }}</TD>
            <TD align="right">{{ rupiah($ppnpn1persen_l + $ppnpn1persen_t) }}</TD>
            <TD align="right">{{ rupiah($ppnpn4persen_l + $ppnpn4persen_t) }}</TD>
            <TD align="right">{{ rupiah($jkk_l + $jkk_t) }}</TD>
            <TD align="right">{{ rupiah($jkm_l + $jkm_t) }}</TD>
            <TD align="right">{{ rupiah($bpjs_l + $bpjs_t) }}</TD>
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
