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
            <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
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
            <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
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
                            <td colspan="4" style="text-align: center"><b>JUMLAH</b></td>
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
                            <td colspan="4" style="text-align: center"><b>JUMLAH</b></td>
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
                            <td colspan="4" style="text-align: center"><b>JUMLAH</b></td>
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
            <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
                <thead>
                    <tr>
                        <th><b>No Kas</b></th>
                        <th><b>No. SP2D</b></th>
                        <th><b>PERUSAHAAN / INSTANSI</b></th>
                        <th><b>NAMA SKPD</b></th>
                        <th><b>TGL. KAS</b></th>
                        <th><b>PPN</b></th>
                        <th><b>PPH 21</b></th>
                        <th><b>PPH 22</b></th>
                        <th><b>PPH 23</b></th>
                        <th><b>Pasal 4 Ayat 2</b></th>
                        <th><b>JUMLAH POTONGAN &nbsp; (Kol.6 s.d 10)</b></th>
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
                    </tr>
                </thead>
                <tbody>
                    @php
                        //$nomor   ='0';
                        $jum_nilai = 0;
                        $jum_ppn = 0;
                        $jum_pph21 = 0;
                        $jum_pph22 = 0;
                        $jum_pph23 = 0;
                        $jum_psl4_a2 = 0;
                        $jum_iwppnpn = 0;
                        $jum_pot_lain = 0;
                        $jum_jumlah_potongan = 0;
                        $jum_nilai_bersih = 0;
                        $jumlah_sp2d = 0;
                        $jumlah_ppn = 0;
                        $jumlah_pph21 = 0;
                        $jumlah_pph22 = 0;
                        $jumlah_pph23 = 0;
                        $jumlah_psl4_a2 = 0;
                        $jumlah_iwppnpn = 0;
                        $jumlah_pot_lain = 0;
                        $jumlah_jumlah_potongan = 0;
                        $jumlah_nilai_bersih = 0;
                    @endphp
                    @foreach ($data_potongan as $data)
                        @php
                            $kd_skpd = $data->kd_skpd;
                            $nm_skpd = $data->nm_skpd;
                            $nmrekan = $data->nmrekan;
                            $no_kas_bud = $data->no_kas_bud;
                            $tgl_kas_bud = $data->tgl_kas_bud;
                            $no_sp2d = $data->no_sp2d;
                            $tgl_sp2d = $data->tgl_sp2d;
                            $nilai = $data->nilai;
                            $ppn = $data->ppn;
                            $pph21 = $data->pph21;
                            $pph22 = $data->pph22;
                            $pph23 = $data->pph23;
                            $psl4_a2 = $data->psl4_a2;
                            $iwppnpn = $data->iwppnpn;
                            $pot_lain = $data->pot_lain;
                            $jumlah_potongan = $data->jumlah_potongan;
                            $nilai_bersih = $nilai - $jumlah_potongan;

                            //total
                            $jumlah_ppn += $data->ppn;
                            $jumlah_pph21 += $data->pph21;
                            $jumlah_pph22 += $data->pph22;
                            $jumlah_pph23 += $data->pph23;
                            $jumlah_psl4_a2 += $data->psl4_a2;
                            $jumlah_iwppnpn += $data->iwppnpn;
                            $jumlah_pot_lain += $data->pot_lain;
                            $jumlah_jumlah_potongan += $data->jumlah_potongan;
                            $jumlah_nilai_bersih += $nilai - $jumlah_potongan;
                        @endphp
                        <tr>
                            <td style="text-align: center">{{ $no_kas_bud }}</td>
                            <td>{{ $no_sp2d }}</td>
                            <td>{{ $nmrekan }}</td>
                            <td>{{ $nm_skpd }}</td>
                            <td>{{ tanggal($tgl_kas_bud) }}</td>
                            <td class="angka">{{ rupiah($ppn) }}</td>
                            <td class="angka">{{ rupiah($pph21) }}</td>
                            <td class="angka">{{ rupiah($pph22) }}</td>
                            <td class="angka">{{ rupiah($pph23) }}</td>
                            <td class="angka">{{ rupiah($psl4_a2) }}</td>
                            <td class="angka">{{ rupiah($jumlah_potongan) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" style="text-align: center"><b>JUMLAH</b></td>
                        <td class="angka"><b>{{ rupiah($jumlah_ppn) }}</b></td>
                        <td class="angka"><b>{{ rupiah($jumlah_pph21) }}</b></td>
                        <td class="angka"><b>{{ rupiah($jumlah_pph22) }}</b></td>
                        <td class="angka"><b>{{ rupiah($jumlah_pph23) }}</b></td>
                        <td class="angka"><b>{{ rupiah($jumlah_psl4_a2) }}</b></td>
                        <td class="angka"><b>{{ rupiah($jumlah_jumlah_potongan) }}</b></td>
                    </tr>
                </tbody>
            </table>
        @else
            <table style="width: 100%" border="1" id="pilihan1" cellpadding="{{ $spasi }}">
                <thead>
                    <tr>
                        <th><b>No Kas</b></th>
                        <th><b>No. SP2D</b></th>
                        <th><b>PERUSAHAAN / INSTANSI</b></th>
                        <th><b>NAMA SKPD</b></th>
                        <th><b>TGL. KAS</b></th>
                        <th><b>PPN</b></th>
                        <th><b>PPH 21</b></th>
                        <th><b>PPH 22</b></th>
                        <th><b>PPH 23</b></th>
                        <th><b>Pasal 4 Ayat 2</b></th>
                        <th><b>JUMLAH POTONGAN &nbsp; (Kol.6 s.d 10)</b></th>
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
                    </tr>
                </thead>
                <tbody>
                    @if ($pilihan == '2')
                        @php
                            //$nomor   ='0';
                            $jum_nilai = 0;
                            $jum_ppn = 0;
                            $jum_pph21 = 0;
                            $jum_pph22 = 0;
                            $jum_pph23 = 0;
                            $jum_psl4_a2 = 0;
                            $jum_iwppnpn = 0;
                            $jum_pot_lain = 0;
                            $jum_jumlah_potongan = 0;
                            $jum_nilai_bersih = 0;
                            $jumlah_sp2d = 0;
                            $jumlah_ppn = 0;
                            $jumlah_pph21 = 0;
                            $jumlah_pph22 = 0;
                            $jumlah_pph23 = 0;
                            $jumlah_psl4_a2 = 0;
                            $jumlah_iwppnpn = 0;
                            $jumlah_pot_lain = 0;
                            $jumlah_jumlah_potongan = 0;
                            $jumlah_nilai_bersih = 0;
                        @endphp
                        @foreach ($data_potongan as $data)
                            @php
                                $kd_skpd = $data->kd_skpd;
                                $nm_skpd = $data->nm_skpd;
                                $nmrekan = $data->nmrekan;
                                $no_kas_bud = $data->no_kas_bud;
                                $tgl_kas_bud = $data->tgl_kas_bud;
                                $no_sp2d = $data->no_sp2d;
                                $tgl_sp2d = $data->tgl_sp2d;
                                $nilai = $data->nilai;
                                $ppn = $data->ppn;
                                $pph21 = $data->pph21;
                                $pph22 = $data->pph22;
                                $pph23 = $data->pph23;
                                $psl4_a2 = $data->psl4_a2;
                                $iwppnpn = $data->iwppnpn;
                                $pot_lain = $data->pot_lain;
                                $jumlah_potongan = $data->jumlah_potongan;
                                $nilai_bersih = $nilai - $jumlah_potongan;

                                //total
                                $jumlah_ppn += $data->ppn;
                                $jumlah_pph21 += $data->pph21;
                                $jumlah_pph22 += $data->pph22;
                                $jumlah_pph23 += $data->pph23;
                                $jumlah_psl4_a2 += $data->psl4_a2;
                                $jumlah_iwppnpn += $data->iwppnpn;
                                $jumlah_pot_lain += $data->pot_lain;
                                $jumlah_jumlah_potongan += $data->jumlah_potongan;
                                $jumlah_nilai_bersih += $nilai - $jumlah_potongan;
                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $no_kas_bud }}</td>
                                <td>{{ $no_sp2d }}</td>
                                <td>{{ $nmrekan }}</td>
                                <td>{{ $nm_skpd }}</td>
                                <td>{{ tanggal($tgl_kas_bud) }}</td>
                                <td class="angka">{{ rupiah($ppn) }}</td>
                                <td class="angka">{{ rupiah($pph21) }}</td>
                                <td class="angka">{{ rupiah($pph22) }}</td>
                                <td class="angka">{{ rupiah($pph23) }}</td>
                                <td class="angka">{{ rupiah($psl4_a2) }}</td>
                                <td class="angka">{{ rupiah($jumlah_potongan) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" style="text-align: center"><b>JUMLAH</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_ppn) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_pph21) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_pph22) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_pph23) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_psl4_a2) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_jumlah_potongan) }}</b></td>
                        </tr>
                    @elseif ($pilihan == '3')
                        @php
                            //$nomor   ='0';
                            $jum_nilai = 0;
                            $jum_ppn = 0;
                            $jum_pph21 = 0;
                            $jum_pph22 = 0;
                            $jum_pph23 = 0;
                            $jum_psl4_a2 = 0;
                            $jum_iwppnpn = 0;
                            $jum_pot_lain = 0;
                            $jum_jumlah_potongan = 0;
                            $jum_nilai_bersih = 0;
                            $jumlah_sp2d = 0;
                            $jumlah_ppn = 0;
                            $jumlah_pph21 = 0;
                            $jumlah_pph22 = 0;
                            $jumlah_pph23 = 0;
                            $jumlah_psl4_a2 = 0;
                            $jumlah_iwppnpn = 0;
                            $jumlah_pot_lain = 0;
                            $jumlah_jumlah_potongan = 0;
                            $jumlah_nilai_bersih = 0;
                        @endphp
                        @foreach ($data_potongan as $data)
                            @php
                                $kd_skpd = $data->kd_skpd;
                                $nm_skpd = $data->nm_skpd;
                                $nmrekan = $data->nmrekan;
                                $no_kas_bud = $data->no_kas_bud;
                                $tgl_kas_bud = $data->tgl_kas_bud;
                                $no_sp2d = $data->no_sp2d;
                                $tgl_sp2d = $data->tgl_sp2d;
                                $nilai = $data->nilai;
                                $ppn = $data->ppn;
                                $pph21 = $data->pph21;
                                $pph22 = $data->pph22;
                                $pph23 = $data->pph23;
                                $psl4_a2 = $data->psl4_a2;
                                $iwppnpn = $data->iwppnpn;
                                $pot_lain = $data->pot_lain;
                                $jumlah_potongan = $data->jumlah_potongan;
                                $nilai_bersih = $nilai - $jumlah_potongan;

                                //total
                                $jumlah_ppn += $data->ppn;
                                $jumlah_pph21 += $data->pph21;
                                $jumlah_pph22 += $data->pph22;
                                $jumlah_pph23 += $data->pph23;
                                $jumlah_psl4_a2 += $data->psl4_a2;
                                $jumlah_iwppnpn += $data->iwppnpn;
                                $jumlah_pot_lain += $data->pot_lain;
                                $jumlah_jumlah_potongan += $data->jumlah_potongan;
                                $jumlah_nilai_bersih += $nilai - $jumlah_potongan;
                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $no_kas_bud }}</td>
                                <td>{{ $no_sp2d }}</td>
                                <td>{{ $nmrekan }}</td>
                                <td>{{ $nm_skpd }}</td>
                                <td>{{ tanggal($tgl_kas_bud) }}</td>
                                <td class="angka">{{ rupiah($ppn) }}</td>
                                <td class="angka">{{ rupiah($pph21) }}</td>
                                <td class="angka">{{ rupiah($pph22) }}</td>
                                <td class="angka">{{ rupiah($pph23) }}</td>
                                <td class="angka">{{ rupiah($psl4_a2) }}</td>
                                <td class="angka">{{ rupiah($jumlah_potongan) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" style="text-align: center"><b>JUMLAH</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_ppn) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_pph21) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_pph22) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_pph23) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_psl4_a2) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_jumlah_potongan) }}</b></td>
                        </tr>
                    @elseif ($pilihan == '4')
                        @php
                            //$nomor   ='0';
                            $jum_nilai = 0;
                            $jum_ppn = 0;
                            $jum_pph21 = 0;
                            $jum_pph22 = 0;
                            $jum_pph23 = 0;
                            $jum_psl4_a2 = 0;
                            $jum_iwppnpn = 0;
                            $jum_pot_lain = 0;
                            $jum_jumlah_potongan = 0;
                            $jum_nilai_bersih = 0;
                            $jumlah_sp2d = 0;
                            $jumlah_ppn = 0;
                            $jumlah_pph21 = 0;
                            $jumlah_pph22 = 0;
                            $jumlah_pph23 = 0;
                            $jumlah_psl4_a2 = 0;
                            $jumlah_iwppnpn = 0;
                            $jumlah_pot_lain = 0;
                            $jumlah_jumlah_potongan = 0;
                            $jumlah_nilai_bersih = 0;
                        @endphp
                        @foreach ($data_potongan as $data)
                            @php
                                $kd_skpd = $data->kd_skpd;
                                $nm_skpd = $data->nm_skpd;
                                $nmrekan = $data->nmrekan;
                                $no_kas_bud = $data->no_kas_bud;
                                $tgl_kas_bud = $data->tgl_kas_bud;
                                $no_sp2d = $data->no_sp2d;
                                $tgl_sp2d = $data->tgl_sp2d;
                                $nilai = $data->nilai;
                                $ppn = $data->ppn;
                                $pph21 = $data->pph21;
                                $pph22 = $data->pph22;
                                $pph23 = $data->pph23;
                                $psl4_a2 = $data->psl4_a2;
                                $iwppnpn = $data->iwppnpn;
                                $pot_lain = $data->pot_lain;
                                $jumlah_potongan = $data->jumlah_potongan;
                                $nilai_bersih = $nilai - $jumlah_potongan;

                                //total
                                $jumlah_ppn += $data->ppn;
                                $jumlah_pph21 += $data->pph21;
                                $jumlah_pph22 += $data->pph22;
                                $jumlah_pph23 += $data->pph23;
                                $jumlah_psl4_a2 += $data->psl4_a2;
                                $jumlah_iwppnpn += $data->iwppnpn;
                                $jumlah_pot_lain += $data->pot_lain;
                                $jumlah_jumlah_potongan += $data->jumlah_potongan;
                                $jumlah_nilai_bersih += $nilai - $jumlah_potongan;
                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $no_kas_bud }}</td>
                                <td>{{ $no_sp2d }}</td>
                                <td>{{ $nmrekan }}</td>
                                <td>{{ $nm_skpd }}</td>
                                <td>{{ tanggal($tgl_kas_bud) }}</td>
                                <td class="angka">{{ rupiah($ppn) }}</td>
                                <td class="angka">{{ rupiah($pph21) }}</td>
                                <td class="angka">{{ rupiah($pph22) }}</td>
                                <td class="angka">{{ rupiah($pph23) }}</td>
                                <td class="angka">{{ rupiah($psl4_a2) }}</td>
                                <td class="angka">{{ rupiah($jumlah_potongan) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" style="text-align: center"><b>JUMLAH</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_ppn) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_pph21) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_pph22) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_pph23) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_psl4_a2) }}</b></td>
                            <td class="angka"><b>{{ rupiah($jumlah_jumlah_potongan) }}</b></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    @endif
    <div style="padding-top:20px;padding-left:500px">
        <table class="table" style="width:100%" border="0">
            <tr>
                <td style="margin: 2px 0px;text-align: center">
                    @if (isset($tanggal))
                        Sanggau, {{ tanggal($tanggal) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $tanda->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center"><b><u>{{ $tanda->nama }}</u></b></td>
            </tr>
            <tr>
                <td style="text-align: center">{{ $tanda->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center">NIP. {{ $tanda->nip }}</td>
            </tr>
        </table>
    </div>


</body>

</html>
