<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BKU SKPD</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #rincian>tbody>tr>td {
            vertical-align: top;
            font-size: 14px
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
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75" height="100" />
            </td>
            <td align="left" style="font-size:16px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>SKPD {{ $skpd->nm_skpd }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    {{-- <hr> --}}
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center" width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>BUKU KAS UMUM PENGELUARAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ $keterangan_periode }}</b></td>
        </tr>
    </table>
    <br>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center" border="1" id="rincian">
        <thead>
            <td align="center" bgcolor="#CCCCCC" width="3%" style="font-size:14px;font-weight:bold;">No</td>
            <td align="center" bgcolor="#CCCCCC" width="10%" style="font-size:14px;font-weight:bold">Tanggal</td>
            <td align="center" bgcolor="#CCCCCC" width="2%" style="font-size:14px;font-weight:bold">No. Bukti</td>
            <td align="center" bgcolor="#CCCCCC" width="40%" style="font-size:14px;font-weight:bold">Uraian</td>
            <td align="center" bgcolor="#CCCCCC" width="13%" style="font-size:14px;font-weight:bold">Penerimaan</td>
            <td align="center" bgcolor="#CCCCCC" width="13%" style="font-size:14px;font-weight:bold">Pengeluaran</td>
            <td align="center" bgcolor="#CCCCCC" width="13%" style="font-size:14px;font-weight:bold">Saldo</td>
            </tr>
            <tr>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black">1</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black">2</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black">3</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black">4</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black">5</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black">6</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black">7</td>
            </tr>

        </thead>

        <tfoot>
            <tr>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black"></td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black"></td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black"></td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black"></td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black"></td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black"></td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:14px;border-top:solid 1px black"></td>
            </tr>

            </thead>
            <tbody>
                {{-- SALDO AWAL --}}
                <tr>
                    <td width="5%" align="center" style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                    <td width="10%" align="center" style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                    <td width="13%" align="center" style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                    <td width="20%" align="left" style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black">Saldo Lalu</td>
                    <td width="13%" align="right" style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                    <td width="13%" align="right" style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                    <td width="13%" align="right" style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black">
                        {{ rupiah($data_sawal->terima - $data_sawal->keluar + $data_tahun_lalu->sld_awalpajak + $data_tahun_lalu->nilai) }}
                    </td>
                </tr>
                </tr>

                @php
                    $lhasil = $data_sawal->terima - $data_sawal->keluar + $data_tahun_lalu->sld_awalpajak + $data_tahun_lalu->nilai;
                    $totkeluar = 0;
                    $totterima = 0;
                    $lcno = 0;
                    $lckeluar = 0;
                    $lcterima = 0;
                    $lcterima_pajak = 0;
                    $lckeluar_pajak = 0;
                @endphp
                @foreach ($data_rincian as $data)
                    <tr>
                        @php
                            $lhasil = $lhasil + $data->terima - $data->keluar;
                            $totkeluar = $totkeluar + $data->keluar;
                            $totterima = $totterima + $data->terima;
                        @endphp
                        @if ($data->tanggal != '' && $data->tanggal != '1900-01-01')
                            @php
                                $a = $data->tanggal;
                                $tanggal = tanggal_indonesia($a);
                                $lcno = $lcno + 1;
                                $no_bku = $data->no_kas;
                            @endphp
                            <td align="center" style="font-size:14px;border-bottom:none 1px gray;border-top:solid 1px gray">
                                {{ $lcno }}</td>
                            <td align="center" style="font-size:14px;border-bottom:none 1px gray;border-top:solid 1px gray">
                                {{ $tanggal }}</td>
                            <td align="center" style="font-size:14px;border-bottom:none 1px gray;border-top:solid 1px gray">
                                {{ $no_bku }}</td>
                            <td align="left" style="font-size:14px;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                {{ $data->uraian }}</td>

                            @if (empty($data->terima) or $data->terima == 0)
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                </td>
                            @else
                                @php
                                    $lcterima = $lcterima + $data->terima;
                                @endphp
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                    {{ rupiah($data->terima) }}</td>
                            @endif
                            @if (empty($data->keluar) or $data->keluar == 0)
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                </td>
                            @else
                                @php
                                    $lckeluar = $lckeluar + $data->keluar;
                                @endphp
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                    {{ rupiah($data->keluar) }}</td>
                            @endif
                            @if (empty($data->terima) and empty($data->keluar) or $data->terima == 0 and $data->keluar == 0)
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                </td>
                            @else
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                    {{ rupiah($lhasil) }}</td>
                            @endif
                        @else
                            <td align="center" style="font-size:14px;border-bottom:none 1px gray;border-top:none 1px gray">&nbsp;</td>
                            <td align="center" style="font-size:14px;border-bottom:none 1px gray;border-top:none 1px gray">&nbsp;</td>
                            <td align="center" style="font-size:14px;border-bottom:none 1px gray;border-top:none 1px gray"></td>
                            <td align="left" style="font-size:14px;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                {{ $data->uraian }}</td>
                            @if (empty($data->terima) or $data->terima == 0)
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                </td>
                            @else
                                @php
                                    if ($data->jns_trans == '3') {
                                        $lcterima_pajak = $lcterima_pajak + $data->terima;
                                    } else {
                                        $lcterima = $lcterima + $data->terima;
                                    }
                                @endphp
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                    {{ rupiah($data->terima) }}</td>
                            @endif

                            @if (empty($data->keluar) or $data->keluar == 0)
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                </td>
                            @else
                                @php
                                    if ($data->jns_trans == '4') {
                                        $lckeluar_pajak = $lckeluar_pajak + $data->keluar;
                                    } else {
                                        $lckeluar = $lckeluar + $data->keluar;
                                    }
                                @endphp
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                    {{ rupiah($data->keluar) }}</td>
                            @endif

                            @if (empty($data->terima) and empty($data->keluar) or $data->terima == 0 and $data->keluar == 0)
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                </td>
                            @else
                                <td align="right" style="font-size:14px;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                    {{ rupiah($lhasil) }}</td>
                            @endif
                    </tr>
                @endif
                @endforeach

            </tbody>
    </table>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center" border="0" id="rincian">
        <thead>
            <td align="center" width="5%" style="font-size:14px;font-weight:bold;"></td>
            <td align="center" width="10%" style="font-size:14px;font-weight:bold"></td>
            <td align="center" width="13%" style="font-size:14px;font-weight:bold">
            </td>
            <td align="center" width="40%" style="font-size:14px;font-weight:bold"></td>
            <td width="13%" style="font-size:14px;font-weight:bold">
                {{ rupiah($hasil->terima + $data_sawal->terima - $data_sawal->keluar + $data_tahun_lalu->sld_awalpajak + $data_tahun_lalu->nilai) }}</br>
            </td>
            <td width="13%" style="font-size:14px;font-weight:bold"> {{ rupiah($hasil->keluar) }}
            </td>
            <td align="center" width="13%" style="font-size:14px;font-weight:bold"></td>
        </thead>

    </table>
    {{-- @php
        for ($i = 0; $i <= $enter; $i++) {
            echo '<br>';
        }
    @endphp --}}
    <br>
    <table style="border-collapse:collapse; border-color: black;font-family:Open Sans" width="100%" align="center" border="0" cellspacing="1" cellpadding="1">
        <tr>
            <td colspan="14" align="left" style="font-size:14px;border: solid 1px white;">Saldo Kas di Bendahara
                Pengeluaran/Bendahara Pengeluaran Pembantu bulan {{ strtolower(bulan($bulan)) }} </td>
            <td align="right" style="font-size:14px;border: solid 1px white;"></td>
            <td align="right" style="font-size:14px;border: solid 1px white;"></td>
        </tr>
        {{-- belum ditambah $trh1->jmterima + $lcterima_pajak - $trh1->jmkeluar+ $lckeluar_pajak --}}
        <tr>
            <td colspan="12" align="left" style="font-size:14px;border: solid 1px white;">
                Rp{{ rupiah($lhasil) }}</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="right" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="right" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="right" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="12" align="left" style="font-size:14px;border: solid 1px white;"><i>(Terbilang :
                    {{ terbilang($lhasil) }})</i></td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="right" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="right" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="right" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="2" align="left" style="font-size:14px;border: solid 1px white;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Terdiri dari :</u></b></td>
            <td colspan="14 align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="12" align="left" style="font-size:14px;border: solid 1px white;">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. Saldo Tunai
            </td>
            <td align="right" style="font-size:14px;border: solid 1px white;"><b>Rp
                    {{ rupiah($terima - $keluar + $terima_lalu - $keluar_lalu) }}
            </td>
            <td align="center" style="font-size:14px;border: solid 1px white;"></td>
            <td align="center" style="font-size:14px;border: solid 1px white;"></td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="12" align="left" style="font-size:14px;border: solid 1px white;">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. Saldo Bank
            </td>
            <td align="right" style="font-size:14px;border: solid 1px white;"><b>Rp {{ rupiah($saldo_bank->sisa) }}
            </td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="12" align="left" style="font-size:14px;border: solid 1px white;">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. Surat Berharga
            </td>
            <td align="right" style="font-size:14px;border: solid 1px white;"><b>Rp
                    {{ rupiah($surat_berharga->nilai) }} </td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="12" align="left" style="font-size:14px;border: solid 1px white;">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. Saldo Pajak
            </td>
            <td align="right" style="font-size:14px;border: solid 1px white;"><b>Rp {{ rupiah($pajak) }} </td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="12" align="left" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="12" align="left" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
            <td align="center" style="font-size:14px;border: solid 1px white;">&nbsp;</td>
        </tr>
        <tr>
    </table>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table" style="width: 100%;font-size:14px;font-family:Open Sans">
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
                <td style="text-align: center;">{{ $cari_pa_kpa->nama }}</td>
                <td style="text-align: center;">{{ $cari_bendahara->nama }}</td>
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
