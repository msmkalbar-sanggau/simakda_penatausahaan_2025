<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <style>
        table {
            border-collapse: collapse
        }

        #header2>tr>td:first-child {
            width: 30%;
            vertical-align: top
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
            <td rowspan="7" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="100"
                    height="100" />
            </td>
            <td align="left" style="font-size:16px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>BUKU PEMBANTU</strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>PER RINCIAN OBYEK PENERIMAAN</strong></td>
        </tr>
        <tr>
            <td style="height: 10px"></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px">PERIODE {{ strtoupper(tanggal($tanggal1)) }}
                s/d {{ strtoupper(tanggal($tanggal2)) }}</td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <br>
    <br>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" id="header2">
        <tr>
            <td>Urusan Pemerintahan</td>
            <td>:</td>
            <td>{{ $urusan->kd_urusan }} - {{ $urusan->nm_urusan }}</td>
        </tr>
        <tr>
            <td>Bidang Pemerintahan</td>
            <td>:</td>
            <td>{{ $urusan1->kd_u }} - {{ $urusan1->nm_u }}</td>
        </tr>
        <tr>
            <td>Unit Organisasi</td>
            <td>:</td>
            <td>{{ $org->kd_org }} - {{ $org->nm_org }}</td>
        </tr>
        <tr>
            <td>Sub Unit Organisasi</td>
            <td>:</td>
            <td>{{ $skpd->kd_skpd }} - {{ $skpd->nm_skpd }}</td>
        </tr>
        <tr>
            <td>Kode Rekening</td>
            <td>:</td>
            <td>{{ $rekening }}</td>
        </tr>
        <tr>
            <td>Nama Rekening</td>
            <td>:</td>
            <td>{{ nama_rekening($rekening) }}</td>
        </tr>
        <tr>
            <td>Jumlah Anggaran</td>
            <td>:</td>
            <td>Rp. {{ rupiah($anggaran->anggaran) }}</td>
        </tr>
        <tr>
            <td>Tahun Anggaran</td>
            <td>:</td>
            <td>{{ tahun_anggaran() }}</td>
        </tr>
    </table>
    <br>
    <br>
    {{-- isi --}}
    <table style='border-collapse:collapse;' width='100%' align='center' border='1' cellspacing='1'
        cellpadding="{{ $spasi }}">
        <thead>
            <tr>
                <th bgcolor='#CCCCCC' align='center' width='8%'>NO</th>
                <th bgcolor='#CCCCCC' align='center' width='10%'>NO BKU</th>
                <th bgcolor='#CCCCCC' align='center' width='10%'>TGL SETOR</th>
                <th bgcolor='#CCCCCC' align='center' width='40%'>NO. STS & BUKTI PENERIMAAN LAINNYA</th>
                <th bgcolor='#CCCCCC' align='center' width='22%'>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($rincian as $row)
                @php
                    $total += $row->rupiah;
                @endphp
                <tr>
                    <td style="text-align:center">{{ $loop->iteration }}</td>
                    <td style="text-align:center">{{ $row->no_kas }}</td>
                    <td style="text-align:center">{{ tanggal($row->tgl_sts) }}</td>
                    <td>{{ $row->no_sts }}</td>
                    <td style="text-align:right">{{ rupiah($row->rupiah) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3">Jumlah Periode Ini</td>
                <td style="text-align: center" colspan="2">Rp. {{ rupiah($total) }}</td>
            </tr>
            <tr>
                <td colspan="3">Jumlah Periode Lalu</td>
                <td style="text-align: center" colspan="2">Rp. {{ rupiah($lalu->total) }}</td>
            </tr>
            <tr>
                <td colspan="3">Jumlah Sampai Dengan Periode Ini</td>
                <td style="text-align: center" colspan="2">Rp. {{ rupiah($total + $lalu->total) }}</td>
            </tr>
        </tbody>
    </table>
    {{-- isi --}}
    {{-- tanda tangan --}}
    <br>
    <br>
    <div style="padding-top:20px">
        <table class="table" style="width: 100%;font-size:12px;font-family:Open Sans">
            <tr>
                <td style="margin: 2px 0px;text-align: center;">
                    MENGETAHUI,
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
