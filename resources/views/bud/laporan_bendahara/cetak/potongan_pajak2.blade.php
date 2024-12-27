<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DAFTAR POTONGAN PAJAK</title>
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
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:16px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px">
                <strong>
                    @if ($sp2d == '0')
                        DAFTAR POTONGAN SP2D GAJI
                    @else
                        DAFTAR POTONGAN SP2D NON GAJI
                    @endif
                </strong>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><b>DARI TANGGAL : {{ $tanggal1 }} s/d {{ $tanggal2 }}</b>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    @if ($sp2d == '0')
        @if ($pilihan == '1')
            <table style="width: 100%" border="1" id="pilihan1">
                <thead>
                    <tr>
                        <th rowspan="2"><b>No. Urut</b></th>
                        <th rowspan="2"><b>Nama Instansi</b></th>
                        <th rowspan="2"><b>Nilai SP2D</b></th>
                        <th colspan="4"><b>Potongan-Potongan</b></th>
                        <th rowspan="2"><b>Jumlah Potongan</b></th>
                    </tr>
                    <tr>
                        <th><b>IWP</b></th>
                        <th><b>TAPERUM</b></th>
                        <th><b>HKPG</b></th>
                        <th><b>PPH</b></th>
                    </tr>
                    <tr>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th>6</th>
                        <th>7</th>
                        <th>8</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_sp2d = 0;
                        $total_iwp = 0;
                        $total_taperum = 0;
                        $total_hkpg = 0;
                        $total_pph = 0;
                        $total_potongan = 0;
                    @endphp
                    @foreach ($data_potongan as $data)
                        @php
                            $total_sp2d += $data->nilai;
                            $total_iwp += $data->iwp;
                            $total_taperum += $data->taperum;
                            $total_hkpg += $data->hkpg;
                            $total_pph += $data->pph;
                            $total_potongan += $data->jumlah_potongan;
                        @endphp
                        <tr>
                            <td style="text-align: center">{{ $loop->iteration }}</td>
                            <td>{{ $data->nm_skpd }}</td>
                            <td class="angka">{{ rupiah($data->nilai) }}</td>
                            <td class="angka">{{ rupiah($data->iwp) }}</td>
                            <td class="angka">{{ rupiah($data->taperum) }}</td>
                            <td class="angka">{{ rupiah($data->hkpg) }}</td>
                            <td class="angka">{{ rupiah($data->pph) }}</td>
                            <td class="angka">{{ rupiah($data->jumlah_potongan) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: center"><b>JUMLAH</b></td>
                        <td class="angka"><b>{{ rupiah($total_sp2d) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_iwp) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_taperum) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_hkpg) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_pph) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_potongan) }}</b></td>
                    </tr>
                </tbody>
            </table>
        @else
            <table style="width: 100%" border="1" id="pilihan1">
                <thead>
                    <tr>
                        <th rowspan="2"><b>No. Urut</b></th>
                        <th rowspan="2"><b>Nama Instansi</b></th>
                        <th rowspan="2"><b>No Kas/Tanggal Kas</b></th>
                        <th rowspan="2"><b>No SP2D/Tanggal SP2D</b></th>
                        <th rowspan="2"><b>Nilai SP2D</b></th>
                        <th colspan="4"><b>Potongan-Potongan</b></th>
                        <th rowspan="2"><b>Jumlah Potongan</b></th>
                    </tr>
                    <tr>
                        <th><b>IWP</b></th>
                        <th><b>TAPERUM</b></th>
                        <th><b>HKPG</b></th>
                        <th><b>PPH</b></th>
                    </tr>
                    <tr>
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
                    </tr>
                </thead>
                <tbody>
                    @if ($pilihan == '2')
                        @php
                            $total_sp2d = 0;
                            $total_iwp = 0;
                            $total_taperum = 0;
                            $total_hkpg = 0;
                            $total_pph = 0;
                            $total_potongan = 0;
                        @endphp
                        @foreach ($data_potongan as $data)
                            @php
                                $total_sp2d += $data->nilai;
                                $total_iwp += $data->iwp;
                                $total_taperum += $data->taperum;
                                $total_hkpg += $data->hkpg;
                                $total_pph += $data->pph;
                                $total_potongan += $data->jumlah_potongan;
                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                <td>{{ $data->nm_skpd }}</td>
                                <td>{{ $data->no_kas_bud }} {{ $data->tgl_kas_bud }}</td>
                                <td>{{ $data->no_sp2d }} {{ $data->tgl_sp2d }}</td>
                                <td class="angka">{{ rupiah($data->nilai) }}</td>
                                <td class="angka">{{ rupiah($data->iwp) }}</td>
                                <td class="angka">{{ rupiah($data->taperum) }}</td>
                                <td class="angka">{{ rupiah($data->hkpg) }}</td>
                                <td class="angka">{{ rupiah($data->pph) }}</td>
                                <td class="angka">{{ rupiah($data->jumlah_potongan) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: right"><b>JUMLAH</b></td>
                            <td></td>
                            <td></td>
                            <td class="angka"><b>{{ rupiah($total_sp2d) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_iwp) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_taperum) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_hkpg) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_potongan) }}</b></td>
                        </tr>
                    @elseif ($pilihan == '3')
                        @php
                            $total_sp2d = 0;
                            $total_iwp = 0;
                            $total_taperum = 0;
                            $total_hkpg = 0;
                            $total_pph = 0;
                            $total_potongan = 0;
                        @endphp
                        @foreach ($data_potongan as $data)
                            @php
                                $total_sp2d += $data->nilai;
                                $total_iwp += $data->iwp;
                                $total_taperum += $data->taperum;
                                $total_hkpg += $data->hkpg;
                                $total_pph += $data->pph;
                                $total_potongan += $data->jumlah_potongan;
                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                <td>{{ $data->nm_skpd }}</td>
                                <td>{{ $data->no_kas_bud }} {{ $data->tgl_kas_bud }}</td>
                                <td>{{ $data->no_sp2d }} {{ $data->tgl_sp2d }}</td>
                                <td class="angka">{{ rupiah($data->nilai) }}</td>
                                <td class="angka">{{ rupiah($data->iwp) }}</td>
                                <td class="angka">{{ rupiah($data->taperum) }}</td>
                                <td class="angka">{{ rupiah($data->hkpg) }}</td>
                                <td class="angka">{{ rupiah($data->pph) }}</td>
                                <td class="angka">{{ rupiah($data->jumlah_potongan) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: right"><b>JUMLAH</b></td>
                            <td></td>
                            <td></td>
                            <td class="angka"><b>{{ rupiah($total_sp2d) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_iwp) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_taperum) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_hkpg) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_potongan) }}</b></td>
                        </tr>
                    @elseif ($pilihan == '4')
                        @php
                            $total_sp2d = 0;
                            $total_iwp = 0;
                            $total_taperum = 0;
                            $total_hkpg = 0;
                            $total_pph = 0;
                            $total_potongan = 0;
                        @endphp
                        @foreach ($data_potongan as $data)
                            @php
                                $total_sp2d += $data->nilai;
                                $total_iwp += $data->iwp;
                                $total_taperum += $data->taperum;
                                $total_hkpg += $data->hkpg;
                                $total_pph += $data->pph;
                                $total_potongan += $data->jumlah_potongan;
                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                <td>{{ $data->nm_skpd }}</td>
                                <td>{{ $data->no_kas_bud }} {{ $data->tgl_kas_bud }}</td>
                                <td>{{ $data->no_sp2d }} {{ $data->tgl_sp2d }}</td>
                                <td class="angka">{{ rupiah($data->nilai) }}</td>
                                <td class="angka">{{ rupiah($data->iwp) }}</td>
                                <td class="angka">{{ rupiah($data->taperum) }}</td>
                                <td class="angka">{{ rupiah($data->hkpg) }}</td>
                                <td class="angka">{{ rupiah($data->pph) }}</td>
                                <td class="angka">{{ rupiah($data->jumlah_potongan) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: right"><b>JUMLAH</b></td>
                            <td></td>
                            <td></td>
                            <td class="angka"><b>{{ rupiah($total_sp2d) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_iwp) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_taperum) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_hkpg) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_potongan) }}</b></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    @else
        @if ($pilihan == '1')
            <table style="width: 100%" border="1" id="pilihan1">
                <thead>
                    <tr>
                        <th><b>NO</b></th>
                        <th><b>Nama Instansi</b></th>
                        <th><b>Nilai SP2D</b></th>
                        <th><b>PPN</b></th>
                        <th><b>PPH 21</b></th>
                        <th><b>PPH 22</b></th>
                        <th><b>PPH 23</b></th>
                        <th><b>Pasal 4 ayat 2</b></th>
                        <th><b>Iuran Wajib PPNPN</b></th>
                        <th><b>Pot Lain-lain</b></th>
                        <th><b>JUMLAH POTONGAN</b></th>
                        <th><b>NILAI BERSIH</b></th>
                    </tr>
                    <tr>
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
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_sp2d = 0;
                        $total_ppn = 0;
                        $total_pph21 = 0;
                        $total_pph22 = 0;
                        $total_pph23 = 0;
                        $total_psl4_a2 = 0;
                        $total_iwppnpn = 0;
                        $total_pot_lain = 0;
                        $total_jumlah_potongan = 0;
                        $total_nilai_bersih = 0;
                    @endphp
                    @foreach ($data_potongan as $data)
                        @php
                            $total_sp2d += $data->nilai;
                            $total_ppn += $data->ppn;
                            $total_pph21 += $data->pph21;
                            $total_pph22 += $data->pph22;
                            $total_pph23 += $data->pph23;
                            $total_psl4_a2 += $data->psl4_a2;
                            $total_iwppnpn += $data->iwppnpn;
                            $total_pot_lain += $data->pot_lain;
                            $total_jumlah_potongan += $data->jumlah_potongan;
                            $total_nilai_bersih += $data->nilai_bersih;
                        @endphp
                        <tr>
                            <td style="text-align: center">{{ $loop->iteration }}</td>
                            <td>{{ $data->kd_skpd }}-{{ $data->nm_skpd }}</td>
                            <td class="angka">{{ rupiah($data->nilai) }}</td>
                            <td class="angka">{{ rupiah($data->ppn) }}</td>
                            <td class="angka">{{ rupiah($data->pph21) }}</td>
                            <td class="angka">{{ rupiah($data->pph22) }}</td>
                            <td class="angka">{{ rupiah($data->pph23) }}</td>
                            <td class="angka">{{ rupiah($data->psl4_a2) }}</td>
                            <td class="angka">{{ rupiah($data->iwppnpn) }}</td>
                            <td class="angka">{{ rupiah($data->pot_lain) }}</td>
                            <td class="angka">{{ rupiah($data->jumlah_potongan) }}</td>
                            <td class="angka">{{ rupiah($data->nilai_bersih) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: center"><b>JUMLAH</b></td>
                        <td class="angka"><b>{{ rupiah($total_sp2d) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_ppn) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_pph21) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_pph22) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_pph23) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_psl4_a2) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_iwppnpn) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_pot_lain) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_jumlah_potongan) }}</b></td>
                        <td class="angka"><b>{{ rupiah($total_nilai_bersih) }}</b></td>
                    </tr>
                </tbody>
            </table>
        @else
            <table style="width: 100%" border="1" id="pilihan1">
                <thead>
                    <tr>
                        <th><b>NO</b></th>
                        <th><b>Nama Instansi</b></th>
                        <th><b>NO. KAS/TGL. KAS</b></th>
                        <th><b>NO. SP2D/TGL. SP2D</b></th>
                        <th><b>Nilai SP2D</b></th>
                        <th><b>PPN</b></th>
                        <th><b>PPH 21</b></th>
                        <th><b>PPH 22</b></th>
                        <th><b>PPH 23</b></th>
                        <th><b>Pasal 4 ayat 2</b></th>
                        <th><b>Iuran Wajib PPNPN</b></th>
                        <th><b>Pot Lain-lain</b></th>
                        <th><b>JUMLAH POTONGAN<br>(Kol.6 s.d 12)</b></th>
                        <th><b>NILAI BERSIH <br>(Kol.5 - 13)</b></th>
                    </tr>
                    <tr>
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
                </thead>
                <tbody>
                    @if ($pilihan == '2')
                        @php
                            $total_sp2d = 0;
                            $total_ppn = 0;
                            $total_pph21 = 0;
                            $total_pph22 = 0;
                            $total_pph23 = 0;
                            $total_psl4_a2 = 0;
                            $total_iwppnpn = 0;
                            $total_pot_lain = 0;
                            $total_jumlah_potongan = 0;
                            $total_nilai_bersih = 0;
                        @endphp
                        @foreach ($data_potongan as $data)
                            @php
                                $total_sp2d += $data->nilai;
                                $total_ppn += $data->ppn;
                                $total_pph21 += $data->pph21;
                                $total_pph22 += $data->pph22;
                                $total_pph23 += $data->pph23;
                                $total_psl4_a2 += $data->psl4_a2;
                                $total_iwppnpn += $data->iwppnpn;
                                $total_pot_lain += $data->pot_lain;
                                $total_jumlah_potongan += $data->jumlah_potongan;
                                $total_nilai_bersih += $data->nilai_bersih;
                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                <td>{{ $data->nm_skpd }}</td>
                                <td>{{ $data->no_kas_bud }} {{ $data->tgl_kas_bud }}</td>
                                <td>{{ $data->no_sp2d }} {{ $data->tgl_sp2d }}</td>
                                <td class="angka">{{ rupiah($data->nilai) }}</td>
                                <td class="angka">{{ rupiah($data->ppn) }}</td>
                                <td class="angka">{{ rupiah($data->pph21) }}</td>
                                <td class="angka">{{ rupiah($data->pph22) }}</td>
                                <td class="angka">{{ rupiah($data->pph23) }}</td>
                                <td class="angka">{{ rupiah($data->psl4_a2) }}</td>
                                <td class="angka">{{ rupiah($data->iwppnpn) }}</td>
                                <td class="angka">{{ rupiah($data->pot_lain) }}</td>
                                <td class="angka">{{ rupiah($data->jumlah_potongan) }}</td>
                                <td class="angka">{{ rupiah($data->nilai_bersih) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: center"><b>JUMLAH</b></td>
                            <td></td>
                            <td></td>
                            <td class="angka"><b>{{ rupiah($total_sp2d) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_ppn) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph21) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph22) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph23) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_psl4_a2) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_iwppnpn) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pot_lain) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_jumlah_potongan) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_nilai_bersih) }}</b></td>
                        </tr>
                    @elseif ($pilihan == '3')
                        @php
                            $total_sp2d = 0;
                            $total_ppn = 0;
                            $total_pph21 = 0;
                            $total_pph22 = 0;
                            $total_pph23 = 0;
                            $total_psl4_a2 = 0;
                            $total_iwppnpn = 0;
                            $total_pot_lain = 0;
                            $total_jumlah_potongan = 0;
                            $total_nilai_bersih = 0;
                        @endphp
                        @foreach ($data_potongan as $data)
                            @php
                                $total_sp2d += $data->nilai;
                                $total_ppn += $data->ppn;
                                $total_pph21 += $data->pph21;
                                $total_pph22 += $data->pph22;
                                $total_pph23 += $data->pph23;
                                $total_psl4_a2 += $data->psl4_a2;
                                $total_iwppnpn += $data->iwppnpn;
                                $total_pot_lain += $data->pot_lain;
                                $total_jumlah_potongan += $data->jumlah_potongan;
                                $total_nilai_bersih += $data->nilai_bersih;
                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                <td>{{ $data->nm_skpd }}</td>
                                <td>{{ $data->no_kas_bud }} {{ $data->tgl_kas_bud }}</td>
                                <td>{{ $data->no_sp2d }} {{ $data->tgl_sp2d }}</td>
                                <td class="angka">{{ rupiah($data->nilai) }}</td>
                                <td class="angka">{{ rupiah($data->ppn) }}</td>
                                <td class="angka">{{ rupiah($data->pph21) }}</td>
                                <td class="angka">{{ rupiah($data->pph22) }}</td>
                                <td class="angka">{{ rupiah($data->pph23) }}</td>
                                <td class="angka">{{ rupiah($data->psl4_a2) }}</td>
                                <td class="angka">{{ rupiah($data->iwppnpn) }}</td>
                                <td class="angka">{{ rupiah($data->pot_lain) }}</td>
                                <td class="angka">{{ rupiah($data->jumlah_potongan) }}</td>
                                <td class="angka">{{ rupiah($data->nilai_bersih) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: center"><b>JUMLAH</b></td>
                            <td></td>
                            <td></td>
                            <td class="angka"><b>{{ rupiah($total_sp2d) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_ppn) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph21) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph22) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph23) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_psl4_a2) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_iwppnpn) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pot_lain) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_jumlah_potongan) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_nilai_bersih) }}</b></td>
                        </tr>
                    @elseif ($pilihan == '4')
                        @php
                            $total_sp2d = 0;
                            $total_ppn = 0;
                            $total_pph21 = 0;
                            $total_pph22 = 0;
                            $total_pph23 = 0;
                            $total_psl4_a2 = 0;
                            $total_iwppnpn = 0;
                            $total_pot_lain = 0;
                            $total_jumlah_potongan = 0;
                            $total_nilai_bersih = 0;
                        @endphp
                        @foreach ($data_potongan as $data)
                            @php
                                $total_sp2d += $data->nilai;
                                $total_ppn += $data->ppn;
                                $total_pph21 += $data->pph21;
                                $total_pph22 += $data->pph22;
                                $total_pph23 += $data->pph23;
                                $total_psl4_a2 += $data->psl4_a2;
                                $total_iwppnpn += $data->iwppnpn;
                                $total_pot_lain += $data->pot_lain;
                                $total_jumlah_potongan += $data->jumlah_potongan;
                                $total_nilai_bersih += $data->nilai_bersih;
                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                <td>{{ $data->nm_skpd }}</td>
                                <td>{{ $data->no_kas_bud }} {{ $data->tgl_kas_bud }}</td>
                                <td>{{ $data->no_sp2d }} {{ $data->tgl_sp2d }}</td>
                                <td class="angka">{{ rupiah($data->nilai) }}</td>
                                <td class="angka">{{ rupiah($data->ppn) }}</td>
                                <td class="angka">{{ rupiah($data->pph21) }}</td>
                                <td class="angka">{{ rupiah($data->pph22) }}</td>
                                <td class="angka">{{ rupiah($data->pph23) }}</td>
                                <td class="angka">{{ rupiah($data->psl4_a2) }}</td>
                                <td class="angka">{{ rupiah($data->iwppnpn) }}</td>
                                <td class="angka">{{ rupiah($data->pot_lain) }}</td>
                                <td class="angka">{{ rupiah($data->jumlah_potongan) }}</td>
                                <td class="angka">{{ rupiah($data->nilai_bersih) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: center"><b>JUMLAH</b></td>
                            <td></td>
                            <td></td>
                            <td class="angka"><b>{{ rupiah($total_sp2d) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_ppn) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph21) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph22) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pph23) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_psl4_a2) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_iwppnpn) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_pot_lain) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_jumlah_potongan) }}</b></td>
                            <td class="angka"><b>{{ rupiah($total_nilai_bersih) }}</b></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    @endif
    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:800px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center">
                        @if (isset($tanggal))
                            Pontianak, {{ tanggal($tanggal) }}
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
