<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PENERIMAAN PAJAK DAERAH</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #rincian>thead>tr>th {
            background-color: #CCCCCC;
        }

        #header3>th {
            font-weight: normal
        }

        .kanan {
            border-right: 1px solid black
        }

        .kiri {
            border-left: 1px solid black
        }

        .bawah {
            border-bottom: hidden
        }

        .atas {
            border-top: hidden
        }

        .angka {
            text-align: right
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
            <td align="left" style="font-size:14px"><strong>{{ $skpd->nm_skpd }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>REKAPITULASI PENERIMAAN PAJAK DAERAH</b></td>
        </tr>
        @if ($pilihan == '41' || $pilihan == '42')
            <tr>
                <td style="text-align: center"><b>{{ $wilayah->nm_wilayah }}</b></td>
            </tr>
        @endif
        <tr>
            <td style="text-align: left">
                <b>
                    @if ($pilihan == '1')
                        Bulan : {{ Str::upper(bulan($data_awal['bulan_perbulan'])) }}
                    @elseif ($pilihan == '2')
                        Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_pertanggal'])) }}
                    @elseif ($pilihan == '31')
                        Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_pengirim'])) }}
                    @elseif ($pilihan == '32')
                        Bulan : {{ Str::upper(bulan($data_awal['bulan1_pengirim'])) }} s/d
                        {{ Str::upper(bulan($data_awal['bulan2_pengirim'])) }} {{ tahun_anggaran() }}
                    @elseif ($pilihan == '41')
                        Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_wilayah'])) }}
                    @elseif ($pilihan == '42')
                        Bulan : {{ Str::upper(bulan($data_awal['bulan1_wilayah'])) }} s/d
                        {{ Str::upper(bulan($data_awal['bulan2_wilayah'])) }} {{ tahun_anggaran() }}
                    @elseif ($pilihan == '5')
                        Bulan : {{ Str::upper(bulan($data_awal['bulan_rekap1'])) }} s/d
                        {{ Str::upper(bulan($data_awal['bulan_rekap2'])) }} {{ tahun_anggaran() }}
                    @endif
                </b>
            </td>
        </tr>
    </table>
    <table style="width: 100%" border="1" id="rincian">
        <thead>
            @if ($pilihan == '32')
                <tr>
                    <th rowspan="2" style="width: 4%">TANGGAL</th>
                    <th rowspan="2" style="width: 10%">UPPD/SAMSAT</th>
                    <th colspan="3" style="width: 20%">PKB</th>
                    <th colspan="2" style="width: 10%">BBN-KB</th>
                    <th rowspan="2" style="width: 7%">BBN-TKA</th>
                    <th rowspan="2" style="width: 7%">PKA</th>
                    <th rowspan="2" style="width: 9%">BBN-KA</th>
                    <th rowspan="2" style="width: 9%">PAP</th>
                    <th rowspan="2" style="width: 5%">DENDA PAP</th>
                    <th rowspan="2" style="width: 9%">SP 3</th>
                    <th rowspan="2" style="width: 7%">PBB-KB</th>
                    <th rowspan="2" style="width: 10%">JUMLAH</th>
                </tr>
                <tr>
                    <th style="width: 8%">PKB</th>
                    <th style="width: 7%">DENDA PKB</th>
                    <th style="width: 7%">Tgk. PKB</th>
                    <th style="width: 8%">BBN</th>
                    <th style="width: 5%">Denda BBN</th>
                </tr>
                <tr id="header3">
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                    <th>11</th>
                    <th>12</th>
                    <th>13</th>
                    <th>14</th>
                    <th>15</th>
                </tr>
            @else
                <tr>
                    <th rowspan="2" style="width: 10%">UPPD/SAMSAT</th>
                    <th colspan="3" style="width: 20%">PKB</th>
                    <th colspan="2" style="width: 10%">BBN-KB</th>
                    <th rowspan="2" style="width: 7%">BBN-TKA</th>
                    <th rowspan="2" style="width: 7%">PKA</th>
                    <th rowspan="2" style="width: 9%">BBN-KA</th>
                    <th rowspan="2" style="width: 9%">PAP</th>
                    <th rowspan="2" style="width: 5%">DENDA PAP</th>
                    <th rowspan="2" style="width: 9%">SP 3</th>
                    <th rowspan="2" style="width: 7%">PBB-KB</th>
                    <th rowspan="2" style="width: 10%">JUMLAH</th>
                </tr>
                <tr>
                    <th style="width: 8%">PKB</th>
                    <th style="width: 7%">DENDA PKB</th>
                    <th style="width: 7%">Tgk. PKB</th>
                    <th style="width: 8%">BBN</th>
                    <th style="width: 5%">Denda BBN</th>
                </tr>
                <tr id="header3">
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                    <th>11</th>
                    <th>12</th>
                    <th>13</th>
                    <th>14</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @php
                $jumlah_pkb = 0;
                $jumlah_denda_pkb = 0;
                $jumlah_tgk_pkb = 0;
                $jumlah_bbn = 0;
                $jumlah_denda_bbn = 0;
                $jumlah_denda_bbntka = 0;
                $jumlah_pka = 0;
                $jumlah_bbn_ka = 0;
                $jumlah_pap = 0;
                $jumlah_denda_pap = 0;
                $jumlah_sp3 = 0;
                $jumlah_pbb_kb = 0;
                $jumlah_jumlah = 0;
            @endphp
            @if ($pilihan == '32')
                @foreach ($pajak_daerah as $pajak)
                    @php
                        $jumlah_pkb += $pajak->pkb;
                        $jumlah_denda_pkb += $pajak->denda_pkb;
                        $jumlah_tgk_pkb += $pajak->tgk_pkb;
                        $jumlah_bbn += $pajak->bbn;
                        $jumlah_denda_bbn += $pajak->denda_bbn;
                        $jumlah_denda_bbntka += $pajak->denda_bbntka;
                        $jumlah_pka += $pajak->pka;
                        $jumlah_bbn_ka += $pajak->bbn_ka;
                        $jumlah_pap += $pajak->pap;
                        $jumlah_denda_pap += $pajak->denda_pap;
                        $jumlah_sp3 += $pajak->sp3;
                        $jumlah_pbb_kb += $pajak->pbb_kb;
                        $jumlah_jumlah += $pajak->jumlah;
                    @endphp
                    <tr>
                        <td>{{ $pajak->tgl_kas }}</td>
                        <td>{{ $pajak->kd_pengirim }} {{ $pajak->nm_pengirim }}</td>
                        <td class="angka">{{ rupiah($pajak->pkb) }}</td>
                        <td class="angka">{{ rupiah($pajak->denda_pkb) }}</td>
                        <td class="angka">{{ rupiah($pajak->tgk_pkb) }}</td>
                        <td class="angka">{{ rupiah($pajak->bbn) }}</td>
                        <td class="angka">{{ rupiah($pajak->denda_bbn) }}</td>
                        <td class="angka">{{ rupiah($pajak->denda_bbntka) }}</td>
                        <td class="angka">{{ rupiah($pajak->pka) }}</td>
                        <td class="angka">{{ rupiah($pajak->bbn_ka) }}</td>
                        <td class="angka">{{ rupiah($pajak->pap) }}</td>
                        <td class="angka">{{ rupiah($pajak->denda_pap) }}</td>
                        <td class="angka">{{ rupiah($pajak->sp3) }}</td>
                        <td class="angka">{{ rupiah($pajak->pbb_kb) }}</td>
                        <td class="angka">{{ rupiah($pajak->jumlah) }}</td>
                    </tr>
                @endforeach
            @else
                @foreach ($pajak_daerah as $pajak)
                    @php
                        $jumlah_pkb += $pajak->pkb;
                        $jumlah_denda_pkb += $pajak->denda_pkb;
                        $jumlah_tgk_pkb += $pajak->tgk_pkb;
                        $jumlah_bbn += $pajak->bbn;
                        $jumlah_denda_bbn += $pajak->denda_bbn;
                        $jumlah_denda_bbntka += $pajak->denda_bbntka;
                        $jumlah_pka += $pajak->pka;
                        $jumlah_bbn_ka += $pajak->bbn_ka;
                        $jumlah_pap += $pajak->pap;
                        $jumlah_denda_pap += $pajak->denda_pap;
                        $jumlah_sp3 += $pajak->sp3;
                        $jumlah_pbb_kb += $pajak->pbb_kb;
                        $jumlah_jumlah += $pajak->jumlah;
                    @endphp
                    <tr>
                        <td>{{ $pajak->kd_pengirim }} {{ $pajak->nm_pengirim }}</td>
                        <td class="angka">{{ rupiah($pajak->pkb) }}</td>
                        <td class="angka">{{ rupiah($pajak->denda_pkb) }}</td>
                        <td class="angka">{{ rupiah($pajak->tgk_pkb) }}</td>
                        <td class="angka">{{ rupiah($pajak->bbn) }}</td>
                        <td class="angka">{{ rupiah($pajak->denda_bbn) }}</td>
                        <td class="angka">{{ rupiah($pajak->denda_bbntka) }}</td>
                        <td class="angka">{{ rupiah($pajak->pka) }}</td>
                        <td class="angka">{{ rupiah($pajak->bbn_ka) }}</td>
                        <td class="angka">{{ rupiah($pajak->pap) }}</td>
                        <td class="angka">{{ rupiah($pajak->denda_pap) }}</td>
                        <td class="angka">{{ rupiah($pajak->sp3) }}</td>
                        <td class="angka">{{ rupiah($pajak->pbb_kb) }}</td>
                        <td class="angka">{{ rupiah($pajak->jumlah) }}</td>
                    </tr>
                @endforeach
            @endif
            <tr>
                @if ($pilihan == '32')
                    <td colspan="2">
                        @if ($pilihan == '1')
                            Jumlah Bulan : {{ Str::upper(bulan($data_awal['bulan_perbulan'])) }}
                        @elseif ($pilihan == '2')
                            Jumlah Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_pertanggal'])) }}
                        @elseif ($pilihan == '31')
                            Jumlah Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_pengirim'])) }}
                        @elseif ($pilihan == '32')
                            Jumlah Bulan : {{ Str::upper(bulan($data_awal['bulan1_pengirim'])) }} s/d
                            {{ Str::upper(bulan($data_awal['bulan2_pengirim'])) }}
                        @elseif ($pilihan == '41')
                            Jumlah Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_wilayah'])) }}
                        @elseif ($pilihan == '42')
                            Jumlah Bulan : {{ Str::upper(bulan($data_awal['bulan1_wilayah'])) }} s/d
                            {{ Str::upper(bulan($data_awal['bulan2_wilayah'])) }}
                        @elseif ($pilihan == '5')
                            Jumlah Bulan : {{ Str::upper(bulan($data_awal['bulan_rekap1'])) }} s/d
                            {{ Str::upper(bulan($data_awal['bulan_rekap2'])) }}
                        @endif
                    </td>
                @else
                    <td>
                        @if ($pilihan == '1')
                            Jumlah Bulan : {{ Str::upper(bulan($data_awal['bulan_perbulan'])) }}
                        @elseif ($pilihan == '2')
                            Jumlah Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_pertanggal'])) }}
                        @elseif ($pilihan == '31')
                            Jumlah Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_pengirim'])) }}
                        @elseif ($pilihan == '32')
                            Jumlah Bulan : {{ Str::upper(bulan($data_awal['bulan1_pengirim'])) }} s/d
                            {{ Str::upper(bulan($data_awal['bulan2_pengirim'])) }}
                        @elseif ($pilihan == '41')
                            Jumlah Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_wilayah'])) }}
                        @elseif ($pilihan == '42')
                            Jumlah Bulan : {{ Str::upper(bulan($data_awal['bulan1_wilayah'])) }} s/d
                            {{ Str::upper(bulan($data_awal['bulan2_wilayah'])) }}
                        @elseif ($pilihan == '5')
                            Jumlah Bulan : {{ Str::upper(bulan($data_awal['bulan_rekap1'])) }} s/d
                            {{ Str::upper(bulan($data_awal['bulan_rekap2'])) }}
                        @endif
                    </td>
                @endif
                <td class="angka">{{ rupiah($jumlah_pkb) }}</td>
                <td class="angka">{{ rupiah($jumlah_denda_pkb) }}</td>
                <td class="angka">{{ rupiah($jumlah_tgk_pkb) }}</td>
                <td class="angka">{{ rupiah($jumlah_bbn) }}</td>
                <td class="angka">{{ rupiah($jumlah_denda_bbn) }}</td>
                <td class="angka">{{ rupiah($jumlah_denda_bbntka) }}</td>
                <td class="angka">{{ rupiah($jumlah_pka) }}</td>
                <td class="angka">{{ rupiah($jumlah_bbn_ka) }}</td>
                <td class="angka">{{ rupiah($jumlah_pap) }}</td>
                <td class="angka">{{ rupiah($jumlah_denda_pap) }}</td>
                <td class="angka">{{ rupiah($jumlah_sp3) }}</td>
                <td class="angka">{{ rupiah($jumlah_pbb_kb) }}</td>
                <td class="angka">{{ rupiah($jumlah_jumlah) }}</td>
            </tr>
            @if ($pilihan == '32')
                @foreach ($total_pajak_sebelumnya as $total)
                    <tr>
                        <td colspan="2">
                            @if ($pilihan == '1')
                                Jumlah s/d Bulan : {{ Str::upper(bulan($data_awal['bulan_perbulan'])) }}
                            @elseif ($pilihan == '2')
                                Jumlah s/d Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_sbl_pertanggal'])) }}
                            @elseif ($pilihan == '31')
                                Jumlah s/d Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_sbl_pengirim'])) }}
                            @elseif ($pilihan == '32')
                                Jumlah s/d Bulan : {{ Str::upper(bulan($data_awal['bulan2_pengirim'])) }}
                            @elseif ($pilihan == '41')
                                Jumlah s/d Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_sbl_wilayah'])) }}
                            @elseif ($pilihan == '42')
                                Jumlah s/d Bulan : {{ Str::upper(bulan($data_awal['bulan2_wilayah'])) }}
                            @elseif ($pilihan == '5')
                                Jumlah s/d Bulan : {{ Str::upper(bulan($data_awal['bulan_rekap2'])) }}
                            @endif
                        </td>
                        <td class="angka">{{ rupiah($total->pkb) }}</td>
                        <td class="angka">{{ rupiah($total->denda_pkb) }}</td>
                        <td class="angka">{{ rupiah($total->tgk_pkb) }}</td>
                        <td class="angka">{{ rupiah($total->bbn) }}</td>
                        <td class="angka">{{ rupiah($total->denda_bbn) }}</td>
                        <td class="angka">{{ rupiah($total->denda_bbntka) }}</td>
                        <td class="angka">{{ rupiah($total->pka) }}</td>
                        <td class="angka">{{ rupiah($total->bbn_ka) }}</td>
                        <td class="angka">{{ rupiah($total->pap) }}</td>
                        <td class="angka">{{ rupiah($total->denda_pap) }}</td>
                        <td class="angka">{{ rupiah($total->sp3) }}</td>
                        <td class="angka">{{ rupiah($total->pbb_kb) }}</td>
                        <td class="angka">{{ rupiah($total->jumlah) }}</td>
                    </tr>
                @endforeach
            @else
                @foreach ($total_pajak_sebelumnya as $total)
                    <tr>
                        <td>
                            @if ($pilihan == '1')
                                Jumlah s/d Bulan : {{ Str::upper(bulan($data_awal['bulan_perbulan'])) }}
                            @elseif ($pilihan == '2')
                                Jumlah s/d Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_sbl_pertanggal'])) }}
                            @elseif ($pilihan == '31')
                                Jumlah s/d Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_sbl_pengirim'])) }}
                            @elseif ($pilihan == '32')
                                Jumlah s/d Bulan : {{ Str::upper(bulan($data_awal['bulan2_pengirim'])) }}
                            @elseif ($pilihan == '41')
                                Jumlah s/d Tanggal : {{ Str::upper(tanggal($data_awal['tgl_kas_sbl_wilayah'])) }}
                            @elseif ($pilihan == '42')
                                Jumlah s/d Bulan : {{ Str::upper(bulan($data_awal['bulan2_wilayah'])) }}
                            @elseif ($pilihan == '5')
                                Jumlah s/d Bulan : {{ Str::upper(bulan($data_awal['bulan_rekap2'])) }}
                            @endif
                        </td>
                        <td class="angka">{{ rupiah($total->pkb) }}</td>
                        <td class="angka">{{ rupiah($total->denda_pkb) }}</td>
                        <td class="angka">{{ rupiah($total->tgk_pkb) }}</td>
                        <td class="angka">{{ rupiah($total->bbn) }}</td>
                        <td class="angka">{{ rupiah($total->denda_bbn) }}</td>
                        <td class="angka">{{ rupiah($total->denda_bbntka) }}</td>
                        <td class="angka">{{ rupiah($total->pka) }}</td>
                        <td class="angka">{{ rupiah($total->bbn_ka) }}</td>
                        <td class="angka">{{ rupiah($total->pap) }}</td>
                        <td class="angka">{{ rupiah($total->denda_pap) }}</td>
                        <td class="angka">{{ rupiah($total->sp3) }}</td>
                        <td class="angka">{{ rupiah($total->pbb_kb) }}</td>
                        <td class="angka">{{ rupiah($total->jumlah) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>

</html>
