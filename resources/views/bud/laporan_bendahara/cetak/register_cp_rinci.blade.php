<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REGISTER CP RINCI</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #pilihan1>thead>tr>th {
            background-color: #CCCCCC;
            font-weight: normal
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

{{-- <body onload="window.print()"> --}}

<body>
    @if ($pilihan == '1')
        <table style="border-collapse:collapse;font-family: Open Sans; font-size:16px" width="100%" align="center"
            border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center" style="font-size:16px" width="93%"><strong>PEMERINTAH
                        {{ strtoupper($header->nm_pemda) }}</strong></td>
            </tr>
            <tr>
                <td align="center" style="font-size:16px"><strong>REGISTER PENERIMAAN CONTRA POS TAHUN ANGGARAN
                        {{ tahun_anggaran() }}</strong></td>
            </tr>
            <tr>
                <td align="center" style="font-size:16px">Tanggal {{ \Carbon\Carbon::parse($tanggal1)->format('d') }}
                    {{ bulan(\Carbon\Carbon::parse($tanggal1)->format('m')) }} s/d
                    {{ \Carbon\Carbon::parse($tanggal2)->format('d') }}
                    {{ bulan(\Carbon\Carbon::parse($tanggal2)->format('m')) }} {{ tahun_anggaran() }}</td>
            </tr>
            <tr>
                <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
            </tr>
        </table>
    @elseif ($pilihan == '2' || $pilihan == '3')
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
                <td align="left" style="font-size:14px">
                    <strong>SKPD
                        @if ($pilihan == '2')
                            {{ nama_org($skpd) }}
                        @elseif ($pilihan == '3')
                            {{ nama_skpd($unit) }}
                        @endif
                    </strong>
                </td>
            </tr>
            <tr>
                <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
            </tr>
            <tr>
                <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
            </tr>
        </table>
    @elseif ($pilihan == '4')
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
                <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
            </tr>
            <tr>
                <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
            </tr>
        </table>
    @endif

    @if ($pilihan == '2' || $pilihan == '3')
        <table style="width: 100%">
            <tr>
                <td style="text-align: center"><b>REGISTER PENERIMAAN CONTRA POS</b></td>
            </tr>
            <tr>
                <td style="text-align: center">Tanggal {{ \Carbon\Carbon::parse($tanggal1)->format('d') }}
                    {{ bulan(\Carbon\Carbon::parse($tanggal1)->format('m')) }} s/d
                    {{ \Carbon\Carbon::parse($tanggal2)->format('d') }}
                    {{ bulan(\Carbon\Carbon::parse($tanggal2)->format('m')) }} {{ tahun_anggaran() }}</td>
            </tr>
        </table>
    @elseif ($pilihan == '4')
        <table style="width: 100%">
            <tr>
                <td style="text-align: center"><b>REGISTER PENERIMAAN CONTRA POS</b></td>
            </tr>
            <tr>
                <td style="text-align: center">Tanggal {{ \Carbon\Carbon::parse($tanggal1)->format('d') }}
                    {{ bulan(\Carbon\Carbon::parse($tanggal1)->format('m')) }} s/d
                    {{ \Carbon\Carbon::parse($tanggal2)->format('d') }}
                    {{ bulan(\Carbon\Carbon::parse($tanggal2)->format('m')) }} {{ tahun_anggaran() }}</td>
            </tr>
        </table>
    @endif

    <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
        <thead>
            @if ($pilihan == '1')
                <tr>
                    <th rowspan="4">KODE</th>
                    <th rowspan="4">Uraian</th>
                    <th colspan="12">CP</th>
                    <th rowspan="4">Jumlah</th>
                </tr>
                <tr>
                    <th colspan="7">LS</th>
                    <th colspan="5">UP/GU/TU</th>
                </tr>
                <tr>
                    <th colspan="3">Gaji</th>
                    <th colspan="3">Barang dan Jasa</th>
                    <th rowspan="2">Pihak Ketiga Lainnya</th>
                    <th rowspan="2">UP</th>
                    <th rowspan="2">GU</th>
                    <th colspan="3">TU</th>
                </tr>
                <tr>
                    <th>HKPG</th>
                    <th>Pot. Lain</th>
                    <th>CP</th>
                    <th>Peg.</th>
                    <th>Brng.</th>
                    <th>Modal.</th>
                    <th>Peg.</th>
                    <th>Brng.</th>
                    <th>Modal.</th>
                </tr>
            @elseif($pilihan == '2' || $pilihan == '3' || $pilihan == '4')
                <tr>
                    <th rowspan="4">No. Kas</th>
                    <th rowspan="4">Tgl Kas</th>
                    <th rowspan="4">Uraian</th>
                    <th rowspan="4">Rekening</th>
                    <th colspan="11">CP</th>
                    <th rowspan="4">Jumlah</th>
                </tr>
                <tr>
                    <th colspan="7">LS</th>
                    <th colspan="4">UP/GU/TU</th>
                </tr>
                <tr>
                    <th colspan="3">Gaji</th>
                    <th colspan="3">Barang dan Jasa</th>
                    <th rowspan="2">Pihak Ketiga Lainnya</th>
                    <th rowspan="2">UP/GU</th>
                    <th colspan="3">TU</th>
                </tr>
                <tr>
                    <th>HKPG</th>
                    <th>Pot. Lain</th>
                    <th>CP</th>
                    <th>Peg.</th>
                    <th>Brng.</th>
                    <th>Modal.</th>
                    <th>Pegawai</th>
                    <th>Barang</th>
                    <th>Modal</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @if ($pilihan == '1')
                @php
                    $total_hkpg = 0;
                    $total_pot_lain = 0;
                    $total_cp = 0;
                    $total_ls_peg = 0;
                    $total_ls_brng = 0;
                    $total_ls_modal = 0;
                    $total_ls_phl = 0;
                    $total_gu = 0;
                    $total_up_gu_peg = 0;
                    $total_up_gu_brng = 0;
                    $total_up_gu_modal = 0;
                    $total_total = 0;
                @endphp
                @foreach ($data_register as $register)
                    @php
                        $total_hkpg += $register->hkpg;
                        $total_pot_lain += $register->pot_lain;
                        $total_cp += $register->cp;
                        $total_ls_peg += $register->ls_peg;
                        $total_ls_brng += $register->ls_brng;
                        $total_ls_modal += $register->ls_modal;
                        $total_ls_phl += $register->ls_phl;
                        $total_gu += $register->gu;
                        $total_up_gu_peg += $register->up_gu_peg;
                        $total_up_gu_brng += $register->up_gu_brng;
                        $total_up_gu_modal += $register->up_gu_modal;
                        $total_total += $register->total;
                    @endphp
                    <tr>
                        <td>{{ $register->kd_skpd }}</td>
                        <td>{{ $register->nm_skpd }}</td>
                        <td class="angka">{{ rupiah($register->hkpg) }}</td>
                        <td class="angka">{{ rupiah($register->pot_lain) }}</td>
                        <td class="angka">{{ rupiah($register->cp) }}</td>
                        <td class="angka">{{ rupiah($register->ls_peg) }}</td>
                        <td class="angka">{{ rupiah($register->ls_brng) }}</td>
                        <td class="angka">{{ rupiah($register->ls_modal) }}</td>
                        <td class="angka">{{ rupiah($register->ls_phl) }}</td>
                        <td class="angka">{{ rupiah(0) }}</td>
                        <td class="angka">{{ rupiah($register->gu) }}</td>
                        <td class="angka">{{ rupiah($register->up_gu_peg) }}</td>
                        <td class="angka">{{ rupiah($register->up_gu_brng) }}</td>
                        <td class="angka">{{ rupiah($register->up_gu_modal) }}</td>
                        <td class="angka">{{ rupiah($register->total) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="angka">Jumlah</td>
                    <td class="angka">{{ rupiah($total_hkpg) }}</td>
                    <td class="angka">{{ rupiah($total_pot_lain) }}</td>
                    <td class="angka">{{ rupiah($total_cp) }}</td>
                    <td class="angka">{{ rupiah($total_ls_peg) }}</td>
                    <td class="angka">{{ rupiah($total_ls_brng) }}</td>
                    <td class="angka">{{ rupiah($total_ls_modal) }}</td>
                    <td class="angka">{{ rupiah($total_ls_phl) }}</td>
                    <td class="angka">{{ rupiah(0) }}</td>
                    <td class="angka">{{ rupiah($total_gu) }}</td>
                    <td class="angka">{{ rupiah($total_up_gu_peg) }}</td>
                    <td class="angka">{{ rupiah($total_up_gu_brng) }}</td>
                    <td class="angka">{{ rupiah($total_up_gu_modal) }}</td>
                    <td class="angka">{{ rupiah($total_total) }}</td>
                </tr>
            @elseif ($pilihan == '2' || $pilihan == '3' || $pilihan == '4')
                @php
                    $total_hkpg = 0;
                    $total_pot_lain = 0;
                    $total_cp = 0;
                    $total_ls_peg = 0;
                    $total_ls_brng = 0;
                    $total_ls_modal = 0;
                    $total_ls_phl = 0;
                    $total_up_gu = 0;
                    $total_tu_peg = 0;
                    $total_tu_brng = 0;
                    $total_tu_modal = 0;
                    $total_total = 0;
                @endphp
                @foreach ($data_register as $register)
                    @if ($register->jenis == '1')
                        <tr>
                            <td>{{ $register->no_kas }}</td>
                            <td>{{ $register->tgl_kas }}</td>
                            <td>{{ $register->keterangan }} <br>SKPD : {{ nama_skpd($register->kd_skpd) }} <br> Total
                                : {{ rupiah($register->total) }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @else
                        @php
                            $total_hkpg += $register->hkpg;
                            $total_pot_lain += $register->pot_lain;
                            $total_cp += $register->cp;
                            $total_ls_peg += $register->ls_peg;
                            $total_ls_brng += $register->ls_brng;
                            $total_ls_modal += $register->ls_modal;
                            $total_ls_phl += $register->ls_phl;
                            $total_up_gu += $register->up_gu;
                            $total_tu_peg += $register->tu_peg;
                            $total_tu_brng += $register->tu_brng;
                            $total_tu_modal += $register->tu_modal;
                            $total_total += $register->total;
                        @endphp
                        <tr>
                            <td style="border-top:hidden;"></td>
                            <td style="border-top:hidden;"></td>
                            <td style="border-top:hidden;"></td>
                            <td style="border-top:hidden;">{{ $register->kd_sub_kegiatan }}.{{ $register->kd_rek }}
                            </td>
                            <td class="angka">{{ rupiah($register->hkpg) }}</td>
                            <td class="angka">{{ rupiah($register->pot_lain) }}</td>
                            <td class="angka">{{ rupiah($register->cp) }}</td>
                            <td class="angka">{{ rupiah($register->ls_peg) }}</td>
                            <td class="angka">{{ rupiah($register->ls_brng) }}</td>
                            <td class="angka">{{ rupiah($register->ls_modal) }}</td>
                            <td class="angka">{{ rupiah($register->ls_phl) }}</td>
                            <td class="angka">{{ rupiah($register->up_gu) }}</td>
                            <td class="angka">{{ rupiah($register->tu_peg) }}</td>
                            <td class="angka">{{ rupiah($register->tu_brng) }}</td>
                            <td class="angka">{{ rupiah($register->tu_modal) }}</td>
                            <td class="angka">{{ rupiah($register->total) }}</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="4" class="angka"><b>Jumlah Periode Ini</b>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td class="angka">{{ rupiah($total_hkpg) }}</td>
                    <td class="angka">{{ rupiah($total_pot_lain) }}</td>
                    <td class="angka">{{ rupiah($total_cp) }}</td>
                    <td class="angka">{{ rupiah($total_ls_peg) }}</td>
                    <td class="angka">{{ rupiah($total_ls_brng) }}</td>
                    <td class="angka">{{ rupiah($total_ls_modal) }}</td>
                    <td class="angka">{{ rupiah($total_ls_phl) }}</td>
                    <td class="angka">{{ rupiah($total_up_gu) }}</td>
                    <td class="angka">{{ rupiah($total_tu_peg) }}</td>
                    <td class="angka">{{ rupiah($total_tu_brng) }}</td>
                    <td class="angka">{{ rupiah($total_tu_modal) }}</td>
                    <td class="angka">{{ rupiah($total_total) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:500px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center">
                        @if (isset($tanggal))
                            Sanggau, {{ tanggal($tanggal) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center">
                        {{ $tanda_tangan->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center"><b><u>{{ $tanda_tangan->nama }}</u></b></td>
                </tr>
                <tr>
                    <td style="text-align: center">{{ $tanda_tangan->pangkat }}</td>
                </tr>
                <tr>
                    <td style="text-align: center">NIP. {{ $tanda_tangan->nip }}</td>
                </tr>
            </table>
        </div>
    @endif
</body>

</html>
