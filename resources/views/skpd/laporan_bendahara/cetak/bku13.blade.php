<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BKU SKPD 13</title>
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

        .angka {
            text-align: right
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
            <td style="text-align: center"><b>BUKU KAS UMUM PENGELUARAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
        </tr>
    </table>
    <table style="width: 100%;margin-top:10px" border="1" id="rincian" cellpadding={{ $enter }}>
        <thead>
            <td align="center" bgcolor="#CCCCCC" width="3%" style="font-size:12px;font-weight:bold;">No</td>
            <td align="center" bgcolor="#CCCCCC" width="10%" style="font-size:12px;font-weight:bold">Tanggal</td>
            <td align="center" bgcolor="#CCCCCC" width="10%" colspan="10" style="font-size:12px;font-weight:bold">
                Kode Rekening
            </td>
            <td align="center" bgcolor="#CCCCCC" width="22%" style="font-size:12px;font-weight:bold">Uraian</td>
            <td align="center" bgcolor="#CCCCCC" width="13%" style="font-size:12px;font-weight:bold">Penerimaan</td>
            <td align="center" bgcolor="#CCCCCC" width="13%" style="font-size:12px;font-weight:bold">Pengeluaran</td>
            <td align="center" bgcolor="#CCCCCC" width="13%" style="font-size:12px;font-weight:bold">Saldo</td>
            </tr>
            <tr>
                <td align="center" bgcolor="#CCCCCC" style="font-size:12px;border-top:solid 1px black">1</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:12px;border-top:solid 1px black">2</td>
                <td align="center" bgcolor="#CCCCCC" colspan="10" style="font-size:12px;border-top:solid 1px black">3
                </td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:12px;border-top:solid 1px black">4</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:12px;border-top:solid 1px black">5</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:12px;border-top:solid 1px black">6</td>
                <td align="center" bgcolor="#CCCCCC" style="font-size:12px;border-top:solid 1px black">7</td>
            </tr>

        </thead>
        <tbody>
            {{-- SALDO AWAL --}}
            <tr>
                <td width="5%" align="center"
                    style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                <td width="10%" align="center"
                    style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                <td width="10%" align="center" colspan="9"
                    style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                <td width="10%" align="center"
                    style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                <td width="10%" style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black">
                    Saldo Awal</td>
                <td width="10%" style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black">
                </td>
                <td width="10%" align="center"
                    style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black"></td>
                <td width="13%" class="angka"
                    style="font-size:14px;border-bottom:solid 1px gray;border-top:solid 1px black">
                    {{ rupiah($saldo_awal) }}
                </td>
            </tr>
            @php
                $total_terima = 0;
                $total_keluar = 0;
                $total_pajak = 0;
                $total_pajak_keluar = 0;
                $hasil = $saldo_awal;
            @endphp
            @foreach ($data_bku as $data)
                @php
                    $hasil = $hasil + $data->terima - $data->keluar;
                @endphp
                <tr>
                    @if (!empty($data->tanggal))
                        <td
                            style="text-align: center;vertical-align:top;border-bottom:none 1px gray;border-top:solid 1px gray">
                            {{ $data->no_kas }}</td>
                        <td
                            style="text-align: center;vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                            {{ tanggal_indonesia($data->tanggal) }}</td>
                        <td colspan="9"
                            style="text-align: center;vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                            {{ $data->kegiatan }}</td>
                        <td
                            style="text-align: center;vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                            {{ $data->rekening }}</td>
                        <td style="vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                            {{ $data->uraian }}</td>

                        @if (empty($data->terima) || $data->terima == 0)
                            <td style="vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                            </td>
                        @else
                            @php
                                $total_terima = $total_terima + $data->terima;
                            @endphp
                            <td class="angka"
                                style="vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                {{ rupiah($data->terima) }}</td>
                        @endif

                        @if (empty($data->keluar) || $data->keluar == 0)
                            <td style="vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                            </td>
                        @else
                            @php
                                $total_keluar = $total_keluar + $data->keluar;
                            @endphp
                            <td class="angka"
                                style="vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                {{ rupiah($data->keluar) }}</td>
                        @endif

                        @if ((empty($data->terima) && empty($data->keluar)) || ($data->terima == 0 && $data->keluar == 0))
                            <td style="vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                            </td>
                        @else
                            <td class="angka"
                                style="vertical-align:top;border-bottom:dashed 1px gray;border-top:solid 1px gray">
                                {{ rupiah($hasil) }}</td>
                        @endif
                    @else
                        <td
                            style="text-align:center;vertical-align:top;border-bottom:none 1px gray;border-top:none 1px gray">
                            &nbsp;
                        </td>
                        <td
                            style="text-align:center;vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                            &nbsp;
                        </td>
                        <td colspan="9"
                            style="text-align:center;vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                            {{ $data->kegiatan }}
                        </td>
                        <td colspan="1"
                            style="text-align:center;vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                            {{ $data->rekening }}
                        </td>
                        <td style="vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                            {{ $data->uraian }}
                        </td>

                        @if (empty($data->terima) || $data->terima == 0)
                            <td style="vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                            </td>
                        @else
                            @if ($data->jns_trans == '3')
                                @php
                                    $total_pajak = $total_pajak + $data->terima;
                                @endphp
                            @else
                                @php
                                    $total_terima = $total_terima + $data->terima;
                                @endphp
                            @endif
                            <td class="angka"
                                style="vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                {{ rupiah($data->terima) }}</td>
                        @endif

                        @if (empty($data->keluar) || $data->keluar == 0)
                            <td style="vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                            </td>
                        @else
                            @if ($data->jns_trans == '4')
                                @php
                                    $total_pajak_keluar = $total_pajak_keluar + $data->keluar;
                                @endphp
                            @else
                                @php
                                    $total_keluar = $total_keluar + $data->keluar;
                                @endphp
                            @endif
                            <td class="angka"
                                style="vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                {{ rupiah($data->keluar) }}</td>
                        @endif

                        @if ((empty($data->terima) && empty($data->keluar)) || ($data->terima == 0 && $data->keluar == 0))
                            <td style="vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                            </td>
                        @else
                            <td class="angka"
                                style="vertical-align:top;border-bottom:dashed 1px gray;border-top:dashed 1px gray">
                                {{ rupiah($hasil) }}</td>
                        @endif
                    @endif
                </tr>
            @endforeach
            <tr>
                <td style="font-size:12px;border-top:none">&nbsp;</td>
                <td style="font-size:12px;border-top:none">&nbsp;</td>
                <td colspan="9" style="font-size:12px;border-top:none">&nbsp;</td>
                <td style="font-size:12px;border-top:none">&nbsp;</td>
                <td style="font-size:12px;border-top:none">&nbsp;</td>
                <td style="font-size:12px;border-top:none">&nbsp;</td>
                <td style="font-size:12px;border-top:none">&nbsp;</td>
                <td style="font-size:12px;border-top:none">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="12"
                    style="font-size:12px;border: solid 1px
                    white;border-top:solid 1px black;">
                    &nbsp;</td>
                <td style="font-size:12px;border: solid 1px white;border-top:solid 1px
                    black;">
                    &nbsp;</td>
                <td style="font-size:12px;border: solid 1px white;border-top:solid 1px
                    black;">
                    &nbsp;</td>
                <td style="font-size:12px;border: solid 1px white;border-top:solid 1px
                    black;">
                    &nbsp;</td>
                <td style="font-size:12px;border: solid 1px white;border-top:solid 1px
                    black;">
                    &nbsp;</td>
            </tr>
            <tr>
                <td colspan="16" style="height: 10px;border:hidden"></td>
            </tr>
            <tr>
                <td colspan="12" style="border:hidden">Kas di Bendahara Pengeluaran Bulan {{ bulan($bulan) }}</td>
                <td style="border:hidden"></td>
                <td style="border:hidden" class="angka">
                    {{ rupiah($nilai->jmterima + $total_terima + $total_pajak + $saldo_awal_pajak->jumlah + $saldo_awal_pajak->sld_awalpajak) }}
                </td>
                <td style="border:hidden" class="angka">
                    {{ rupiah($nilai->jmkeluar + $total_keluar + $total_pajak_keluar) }}
                </td>
                <td style="border:hidden" class="angka">
                    {{ rupiah($nilai->jmterima + $total_terima + $total_pajak + $saldo_awal_pajak->jumlah + $saldo_awal_pajak->sld_awalpajak - ($nilai->jmkeluar + $total_keluar + $total_pajak_keluar)) }}
                </td>
            </tr>
            <tr>
                <td colspan="12" style="border:hidden">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Terdiri dari :</u></b>
                </td>
                <td colspan="14" style="border:hidden"></td>
            </tr>
            <tr>
                <td colspan="12" style="border:hidden">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>1. Saldo Tunai</b>
                </td>
                <td style="border:hidden" class="angka"><b>Rp. {{ rupiah($hasil_tunai) }}</b></td>
                <td style="border:hidden" class="angka"></td>
                <td style="border:hidden" class="angka"></td>
                <td style="border:hidden" class="angka"></td>
            </tr>
            <tr>
                <td colspan="12" style="border:hidden">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>2. Saldo Bank</b>
                </td>
                <td style="border:hidden" class="angka"><b>Rp. {{ rupiah($sisa_bank->sisa) }}</b></td>
                <td style="border:hidden" class="angka"></td>
                <td style="border:hidden" class="angka"></td>
                <td style="border:hidden" class="angka"></td>
            </tr>
            <tr>
                <td colspan="12" style="border:hidden">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>3. Surat Berharga</b>
                </td>
                <td style="border:hidden" class="angka"><b>Rp. {{ rupiah($saldo_berharga->total) }}</b></td>
                <td style="border:hidden" class="angka"></td>
                <td style="border:hidden" class="angka"></td>
                <td style="border:hidden" class="angka"></td>
            </tr>
            <tr>
                <td colspan="12" style="border:hidden">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>4. Saldo Pajak</b>
                </td>
                <td style="border:hidden" class="angka"><b>Rp. {{ rupiah($saldo_pajak->sisa) }}</b></td>
                <td style="border:hidden" class="angka"></td>
                <td style="border:hidden" class="angka"></td>
                <td style="border:hidden" class="angka"></td>
            </tr>
        </tbody>
    </table>


    {{-- tanda tangan --}}
    {{-- <div style="padding-top:20px">
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
    </div> --}}
</body>

</html>
