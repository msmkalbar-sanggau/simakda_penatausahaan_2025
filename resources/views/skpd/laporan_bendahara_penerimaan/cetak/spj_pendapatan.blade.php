<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SPJ Pendapatan</title>
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

<!-- <body onload="window.print()"> -->

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
            <td style="text-align: center"><b>LAPORAN PERTANGGUNGJAWABAN BENDAHARA PENERIMAAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE
                    {{ strtoupper(tgl_format_indonesia($tanggal1)) }}
                    s/d {{ strtoupper(tgl_format_indonesia($tanggal2)) }}</b></td>
        </tr>
    </table>
    {{-- isi --}}
    <table style='border-collapse:collapse;' width='100%' align='center' border='1' cellspacing='1'
        cellpadding="{{ $spasi }}">
        <thead>
            <tr>
                <td align='center' bgcolor='#CCCCCC' rowspan='2' width='5%' style='font-size:12px'>
                    Kode<br>Rekening</td>
                <td align='center' bgcolor='#CCCCCC' rowspan='2' width='15%' style='font-size:12px'>Uraian</td>
                <td align='center' bgcolor='#CCCCCC' rowspan='2' style='font-size:12px'>Jumlah<br>Anggaran</td>
                <td align='center' bgcolor='#CCCCCC' colspan='3' style='font-size:12px'>Sampai dengan Bulan Lalu</td>
                <td align='center' bgcolor='#CCCCCC' colspan='3' style='font-size:12px'>Bulan Ini</td>
                <td align='center' bgcolor='#CCCCCC' colspan='4' style='font-size:12px'>Sampai dengan Bulan Ini</td>
            </tr>
            <tr>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Penerimaan</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Penyetoran</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Sisa</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Penerimaan</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Penyetoran</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Sisa</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Jumlah<br>Anggaran<br>yang<br>Terealisasi
                </td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Jumlah<br>Anggaran<br>yang telah<br>Disetor
                </td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Sisa yang<br>Belum<br>Disetor</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>Sisa Anggaran
                    yang<br>Belum<br>Terealisasi/Pelam-<br>pauan Anggaran</td>
            </tr>
            <tr>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>1</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>2</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>3</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>4</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>5</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>6=(5-4)</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>7</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>8</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>9=(8-7)</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>10=(4+7)</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>11=(5+8)</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>12=(11-10)</td>
                <td align='center' bgcolor='#CCCCCC' style='font-size:12px'>13=(3-10)</td>
            </tr>
        </thead>
        @php
            $lcterima_ini = 0;
            $lckeluar_ini = 0;
            $lcprog_lama = '';
            $lckeg_lama = '';
            $ln_jlh1 = 0;
            $ln_jlh2 = 0;
            $ln_jlh3 = 0;
            $ln_jlh4 = 0;
            $ln_jlh5 = 0;
            $ln_jlh6 = 0;
            $ln_jlh7 = 0;
            $ln_jlh8 = 0;
            $ln_jlh9 = 0;
            $ln_jlh10 = 0;
            $ln_jlh11 = 0;
        @endphp
        @foreach ($rincian as $row)
            @php
                $kode = $row->kode;
                $nama = $row->nama;
                $leng = strlen($kode);

            @endphp

            @switch($leng)
                @case(12)
                    <tr>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'>{{ dotrek($kode) }}</b>
                        </td>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'>{{ $nama }}
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>{{ rupiah($row->ang) }}
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->terima_lalu) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu - $row->terima_lalu) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->terima_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_ini - $row->terima_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->terima_lalu + $row->terima_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu + $row->keluar_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu + $row->keluar_ini - ($row->terima_lalu + $row->terima_ini)) }}
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->ang - ($row->terima_lalu + $row->terima_ini)) }}</td>
                    </tr>

                    @php
                        $ln_jlh1 = $ln_jlh1 + $row->ang;
                        $ln_jlh2 = $ln_jlh2 + $row->terima_lalu;
                        $ln_jlh3 = $ln_jlh3 + $row->keluar_lalu;
                        $ln_jlh4 = $ln_jlh3 - $ln_jlh2;
                        $ln_jlh5 = $ln_jlh5 + $row->terima_ini;
                        $ln_jlh6 = $ln_jlh6 + $row->keluar_ini;
                        $ln_jlh7 = $ln_jlh6 - $ln_jlh5;
                        $ln_jlh8 = $ln_jlh5 + $ln_jlh2;
                        $ln_jlh9 = $ln_jlh6 + $ln_jlh3;
                        $ln_jlh10 = $ln_jlh9 - $ln_jlh8;
                        $ln_jlh11 = $ln_jlh1 - $ln_jlh8;
                    @endphp
                @break

                {{--  @case(8)
                    <tr>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'><b>{{ dotrek($kode) }}</b>
                        </td>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'><b>{{ $nama }}</b<<
                                    /td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->ang) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->terima_lalu) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu * -1) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu * -1 - $row->terima_lalu) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->terima_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_ini * -1) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_ini * -1 - $row->terima_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->terima_lalu + $row->terima_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu * -1 - $row->keluar_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah(($row->keluar_lalu + $row->keluar_ini + ($row->terima_lalu + $row->terima_ini)) * -1) }}
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->ang - ($row->terima_lalu + $row->terima_ini)) }}</td>
                    </tr>
                @break

                @case(4)
                    <tr>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'>
                            <b>{{ dotrek($kode) }}</b>
                        </td>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'><b>
                                {{ $nama }}</b></td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->ang) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->terima_lalu) }}sad</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_lalu) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_lalu - $row->terima_lalu) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->terima_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_ini - $row->terima_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->terima_lalu + $row->terima_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_lalu + $row->keluar_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_lalu + $row->keluar_ini - ($row->terima_lalu + $row->terima_ini)) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->ang - ($row->terima_lalu + $row->terima_ini)) }}</b>
                        </td>
                    </tr>
                @break

                @case(2)
                    <tr>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'><b>{{ dotrek($kode) }}</b>
                        </td>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'><b>{{ $nama }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->ang) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->terima_lalu) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu * -1) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu * -1 - $row->terima_lalu) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->terima_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_ini * -1) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_ini * -1 - $row->terima_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->terima_lalu + $row->terima_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->keluar_lalu * -1 - $row->keluar_ini) }}</td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah(($row->keluar_lalu + $row->keluar_ini + ($row->terima_lalu + $row->terima_ini)) * -1) }}
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            {{ rupiah($row->ang - ($row->terima_lalu + $row->terima_ini)) }}</td>
                    </tr>
                @break  --}}

                @default
                    <tr>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'>
                            <b>{{ dotrek($kode) }}</b>
                        </td>
                        <td valign='top' align='left' style='font-size:12px;border-top:none'><b>
                                {{ $nama }}</b></td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->ang) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->terima_lalu) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_lalu) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_lalu - $row->terima_lalu) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->terima_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_ini - $row->terima_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->terima_lalu + $row->terima_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_lalu + $row->keluar_ini) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->keluar_lalu + $row->keluar_ini - ($row->terima_lalu + $row->terima_ini)) }}</b>
                        </td>
                        <td valign='top' align='right' style='font-size:12px;border-top:none'>
                            <b>{{ rupiah($row->ang - ($row->terima_lalu + $row->terima_ini)) }}</b>
                        </td>
                    </tr>
            @endswitch
        @endforeach
        <tr>
            <td valign='top' bgcolor='#CCCCCC' align='center' colspan='2' style='font-size:12px'><b>J U M
                    L A H</b></td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh1) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh2) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh3) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh4) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh5) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh6) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh7) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh8) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh9) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh10) }}</b>
            </td>
            <td valign='top' bgcolor='#CCCCCC' align='right' style='font-size:12px'>
                <b>{{ rupiah($ln_jlh11) }}</b>
            </td>
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
                    {{ ucwords(strtolower($cari_bendahara ? $cari_bendahara->jabatan : ' ')) }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;"><b><u>{{ $cari_pa_kpa->nama }}</u></b></td>
                <td style="text-align: center;"><b><u>{{ $cari_bendahara ? $cari_bendahara->nama : ' ' }}</u></b></td>
            </tr>
            <tr>
                <td style="text-align: center;">{{ $cari_pa_kpa->pangkat }}</td>
                <td style="text-align: center;">{{ $cari_bendahara ? $cari_bendahara->pangkat : ' ' }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">NIP. {{ $cari_pa_kpa->nip }}</td>
                <td style="text-align: center;">NIP. {{ $cari_bendahara ? $cari_bendahara->nip : ' ' }}</td>
            </tr>

        </table>
    </div>
</body>

</html>
