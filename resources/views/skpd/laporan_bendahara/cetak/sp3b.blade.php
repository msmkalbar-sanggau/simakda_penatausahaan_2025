<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SP3B</title>
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
    <style type="text/css" media="print">
        @page {
            size: Legal landscape;
        }
    </style>
</head>

<body>
    <TABLE style="border-collapse:collapse; font-size:14px" width="100%" border="1" cellspacing="0" cellpadding="1"
        align=center>
        <TR>
            <TD align="center">
                <b>
                    PEMERINTAH KABUPATEN SANGGAU <br>
                    <b>DINAS KESEHATAN</b>
                    <br>SURAT PERMINTAAN PENGESAHAN PENDAPATAN DAN BELANJA (SP3B) BLUD
                </b>
                <br>Tanggal : {{ \Carbon\Carbon::parse($tgl2)->locale('id')->isoFormat('DD MMMM Y') }} &nbsp;&nbsp;Nomor
                : 001/SP3B/RSUD-BLUD/2024

            </TD>
        </TR>
    </TABLE>
    <TABLE
        style="border-collapse:collapse; border-top:none; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:13px;"
        width="100%">
        <TR>
            <TD align="left">Kepala SKPD Dinas Kesehatan Kabupaten Sanggau memohon kepada : </TD>
        </TR>
    </TABLE>
    <TABLE
        style="border-collapse:collapse; border-top:none; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:13px;"
        width="100%">
        <TR>
            <TD align="left">Bendahara Umum Daerah Selaku PPKD </TD>
        </TR>
    </TABLE>
    <TABLE
        style="border-collapse:collapse; border-top:none; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:13px;"
        width="100%">
        <TR>
            <TD align="left" width="100%" colspan="3">Agar mengesahkan dan membukukan pendapatan dan belanja dana
                BLUD sejumlah :</TD>
        </TR>
        <TR>
            <TD align="left" width="10%"></TD>
            <TD align="left" width="25%">1. &nbsp;Saldo Awal</TD>
            <TD align="left" width="65%">Rp {{ rupiah($sld_awal->sld_awal) }}</TD>
        </TR>
        <TR>
            <TD align="left" width="10%"></TD>
            <TD align="left" width="25%">2. &nbsp;Pendapatan</TD>
            <TD align="left" width="65%">Rp {{ rupiah($pendapatan->terima) }}</TD>
        </TR>
        <TR>
            <TD align="left" width="10%"></TD>
            <TD align="left" width="25%">3. &nbsp;Belanja</TD>
            <TD align="left" width="65%">Rp {{ rupiah($belanja->keluar) }}</TD>
        </TR>
        <TR>
            <TD align="left" width="10%"></TD>
            <TD align="left" width="25%">4. &nbsp;Saldo Akhir</TD>
            <TD align="left" width="65%">Rp.
                {{ rupiah($sld_awal->sld_awal + $pendapatan->terima - $belanja->keluar) }}</TD>
        </TR>
    </TABLE>
    <TABLE
        style="border-collapse:collapse; border-top:none; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:13px;"
        width="100%">
        <TR>
            <TD align="left">
                Dari tanggal <b>{{ \Carbon\Carbon::parse($tgl1)->locale('id')->isoFormat('DD MMMM Y') }}</b>
                sampai dengan <b>{{ \Carbon\Carbon::parse($tgl2)->locale('id')->isoFormat('DD MMMM Y') }}</b>
            </TD>
            <TD align="left">
                Tahun Anggaran : <b>{{ tahun_anggaran() }}</b>
            </TD>
        </TR>
    </TABLE>
    <TABLE
        style="border-collapse:collapse; border-top:none; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:13px;"
        width="100%">
        <TR>
            <TD align="left" width="18%">Dasar Pengesahan</TD>
            <TD align="left" width="17%">Urusan</TD>
            <TD align="left" width="28%">Organisasi</TD>
            <TD align="center" width="37%">Nama</TD>
        </TR>
        <TR>
            <TD align="left"></TD>
            <TD align="left">1.02 Kesehatan</TD>
            <TD align="left">1.02.1.02.01 Dinas Kesehatan</TD>
            <TD align="center" rowspan="2">
                <b>{{ $nmskpd }}</b>
            </TD>
        </TR>
        <TR>
            <TD align="left"></TD>
            <TD align="left">Upaya Kesehatan Masyarakat</TD>
            <TD align="left">Penyediaan Biaya Operasional dan Pemeliharaan</TD>
        </TR>
    </TABLE>
    <TABLE
        style="border-collapse:collapse; border-top:none; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:13px;"
        width="100%">
        <TR>
            <TD align="center" colspan="3" width="50%"
                style="border-collapse:collapse; border-right:solid 1px black;">
                <b>PENDAPATAN</b>
            </TD>
            <TD align="center" colspan="3" width="50%">
                <b>BELANJA</b>
            </TD>
        </TR>
        <TR>
            <TD align="center" colspan="2"
                style="border-collapse:collapse; border-top:solid 1px black; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black;"
                width="30%">
                <b>Kode Rekening</b>
            </TD>
            <TD align="center"
                style="border-collapse:collapse; border-top:solid 1px black; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black;"
                width="20%">
                <b>Jumlah</b>
            </TD>
            <TD align="center" colspan="2"
                style="border-collapse:collapse; border-top:solid 1px black; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black;"
                width="30%">
                <b>Kode Rekening</b>
            </TD>
            <TD align="center"
                style="border-collapse:collapse; border-top:solid 1px black; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black;"
                width="20%">
                <b>Jumlah</b>
            </TD>
        </TR>
        <tbody>

            @php
                $jum_kd_pen = 0;
                $jum_kd_bel = 0;
            @endphp

            @foreach ($detail as $data)
                @php
                    //perulangan (mengulangi penjumlahan selama ada data yang baru)
                    $jum_kd_pen += $data->real_pen;
                    $jum_kd_bel += $data->real_bel;
                @endphp
                <tr>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="10%" align="center">
                        {{ $data->kd_pen }}</td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="25%" align="right">
                        {{ $data->nm_pen }}</td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="15%" align="right">
                        {{ rupiah($data->real_pen) }}</td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="10%" align="center">{{ $data->kd_bel }}
                    </td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="25%" align="left"> {{ $data->nm_bel }}
                    </td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="15%" align="right">{{ rupiah($data->real_bel) }}
                    </td>
                </tr>
                {{-- <tr>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="10%" align="center">
                    </td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="25%" align="right">
                    </td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="15%" align="right">
                    </td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="10%" align="center">
                        {{ $data->kd_bel }}</td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="25%" align="left">
                        {{ $data->nm_bel }}</td>
                    <td style="border-collapse:collapse; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black; font-size:12px;"
                        width="15%" align="right">
                        {{ rupiah($data->real_bel) }}</td>
                </tr> --}}
            @endforeach
        </tbody>
        <TR>
            <TD align="center" colspan="2"
                style="border-collapse:collapse; border-top:solid 1px black; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black;"
                width="35%">
                <b>Jumlah Pendapatan</b>
            </TD>
            <TD align="right"
                style="border-collapse:collapse; border-top:solid 1px black; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black;"
                width="15%">
                {{ rupiah($jum_kd_pen) }}
            </TD>
            <TD align="center" colspan="2"
                style="border-collapse:collapse; border-top:solid 1px black; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black;"
                width="35%">
                <b>Jumlah Belanja</b>
            </TD>
            <TD align="right"
                style="border-collapse:collapse; border-top:solid 1px black; border-left:solid 1px black; border-right:solid 1px black; border-bottom:solid 1px black;"
                width="15%">
                {{ rupiah($jum_kd_bel) }}
            </TD>
        </TR>
    </TABLE>
    &nbsp;
    <TABLE style="font-size:13px;" width="100%" align="center">
        <TR>
            <TD width="50%" align="center"></TD>
            <TD width="50%" align="center">Sanggau,
                {{ \Carbon\Carbon::parse($tgl_ttd)->locale('id')->isoFormat('DD MMMM Y') }}</TD>
        </TR>
        <TR>
            <TD width="50%" align="center"></TD>
            <TD width="50%" align="center">{{ $ttd->jabatan }} <br> RSUD M.Th Djaman</TD>
        </TR>
        <TR>
            <TD width="50%" align="center"><b>&nbsp;</TD>
            <TD width="50%" align="center"><b>&nbsp;</TD>
        </TR>
        <TR>
            <TD width="50%" align="center"><b>&nbsp;</TD>
            <TD width="50%" align="center"><b>&nbsp;</TD>
        </TR>
        <TR>
            <TD width="50%" align="center"><b>&nbsp;</TD>
            <TD width="50%" align="center"><b>&nbsp;</TD>
        </TR>
        <TR>
            <TD width="50%" align="center"></TD>
            <TD width="50%" align="center"><b><u>{{ $ttd->nama }}</u></b><br>{{ $ttd->pangkat }}</TD>
        </TR>
        <TR>
            <TD width="50%" align="center"></TD>
            <TD width="50%" align="center">{{ $ttd->nip }}</TD>
        </TR>
    </TABLE><br />


</body>

</html>
