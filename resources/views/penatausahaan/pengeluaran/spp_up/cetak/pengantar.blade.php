<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Pengantar</title>
    <style>
        .unborder {
            font-weight: normal;
            text-align: justify
        }

        #rincian>tbody>tr>:first-child {
            padding-left: 20px;
            width: 200px;
        }

        #rincian>tbody>tr>:last-child {
            padding-left: 20px;
        }

        #rincian>tbody>tr>:nth-child(2) {
            padding-left: 100px;
        }

        #rincian>tbody>tr>td {
            font-size: 14px
        }

        .rincian>tbody>tr>td {
            font-size: 14px
        }
    </style>
</head>

<body>
    <table style="border-collapse:collapse;font-family: Open Sans" width="100%" align="center" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75"
                    height="100" />
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
    <hr>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" border="0" cellspacing="0"
        cellpadding="0">
        <tr>
            <td align="center">
                <b>SURAT PERMINTAAN PEMBAYARAN UANG PERSEDIAAN</b>
            </td>
        </tr>
        <tr>
            <td align="center">
                <b>(SPP-UP)</b>
            </td>
        </tr>
        <tr>
            <td align="center">
                <b>Nomor : {{ $no_spp }}</b>
            </td>
        </tr>
        <tr>
            <td align="center">
                <b>SURAT PENGANTAR</b>
            </td>
        </tr>
    </table>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td>
                Kepada Yth.
            </td>
        </tr>
        <tr>
            <td>
                {{ $peng }}
            </td>
        </tr>
        <tr>
            <td>
                OPD : {{ $skpd->nm_skpd }}
            </td>
        </tr>
        <tr>
            <td>
                Di Tempat
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
        </tr>

        <tr>
            <td>
                Dengan memperhatikan Peraturan Gubernur Kalimantan Barat
                {{ $pergub->no_pergub }} Tanggal {{ tanggal($pergub->tgl_pergub) }} Tentang {{ $pergub->tentang }},
                bersama ini kami mengajukan Surat Permintaan
                Pembayaran UP sebagai berikut:</h5>
            </td>
        </tr>
    </table>

    <table id="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td style="height: 5px"></td>
        </tr>
        {{-- Urusan Pemerintahan --}}
        <tr>
            <td style="width: 30%">a. Urusan Pemerintahan</td>
            <td align="left">:</td>
            <td align="left">{{ $spp->kd_bidang_urusan }} - {{ $spp->nm_bidang_urusan }}</td>
        </tr>
        {{-- OPD --}}
        <tr>
            <td style="width: 30%">b. OPD</td>
            <td align="left">:</td>
            <td align="left">{{ $spp->kd_skpd }} - {{ $spp->nm_skpd }}</td>
        </tr>
        {{-- Tahun Anggaran --}}
        <tr>
            <td style="width: 30%">c. Tahun Anggaran</td>
            <td align="left">:</td>
            <td align="left">{{ tahun_anggaran() }}</td>
        </tr>
        {{-- Dasar Pengeluaran SPD --}}
        <tr>
            <td style="width: 30%">d. Dasar Pengeluaran SPD</td>
            <td align="left">:</td>
            <td align="left">{{ $spp->no_spd }}</td>
        </tr>
        {{-- Jumlah Sisa Dana SPD --}}
        <tr>
            <td style="width: 30%">e. Jumlah Sisa Dana SPD</td>
            <td align="left" width='2%'>:</td>
            <td align="left">Rp. {{ rupiah($spp->spd - $spp->spp) }}</td>
        </tr>
        <tr>
            <td style="text-align: center"></td>
            <td></td>
            <td style="font-style: italic">({{ ucwords(terbilang($spp->spd - $spp->spp)) }})</td>
        </tr>
        {{-- Untuk Keperluan Bulan --}}
        <tr>
            <td>f. Untuk Keperluan Bulan</td>
            <td>:</td>
            <td>{{ bulan($spp->bulan) }}</td>
        </tr>
        {{-- Jumlah Pembayaran yang Diminta --}}
        <tr>
            <td>g. Jumlah Pembayaran yang Diminta</td>
            <td>:</td>
            <td>Rp. {{ rupiah($spp->nilai) }}</td>
        </tr>
        <tr>
            <td style="text-align: center"></td>
            <td></td>
            <td style="font-style: italic">({{ ucwords(terbilang($spp->nilai)) }})</td>
        </tr>
        {{-- Nama Bendahara Pengeluaran --}}
        <tr>
            <td>h. Nama {{ ucwords($bendahara->jabatan) }}</td>
            <td>:</td>
            <td>{{ $bendahara->nama }}</td>
        </tr>
        {{-- Nama Nomor Rekening Bank dan NPWP --}}
        <tr>
            <td>i. Nama dan Nomor Rekening Bank</td>
            <td>:</td>
            <td>{{ bank($spp->bank) }} / {{ $spp->no_rek }} / {{ $spp->npwp }}</td>
        </tr>
    </table>
    <br>
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian"
            style="width:100%;border-collapse:collapse;font-family:'Open Sans', Helvetica,Arial,sans-serif">
            <tr>
                <td width='50%'></td>
                <td width='50%' style="margin: 2px 0px;text-align: center">
                    {{ daerah($kd_skpd) }},
                    @if ($tanpa == 1)
                        ______________{{ tahun_anggaran() }}
                    @else
                        {{ tanggal($spp->tgl_spp) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td width='50%'></td>
                <td width='50%' style="padding-bottom: 50px;text-align: center">
                    {{ $bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                <td width='50%'></td>
                <td width='50%' style="text-align: center">
                    <b><u>{{ $bendahara->nama }}</u></b> <br>
                    {{ $bendahara->pangkat }} <br>
                    NIP. {{ $bendahara->nip }}
                </td>
            </tr>
            {{-- <tr>
                <td width='50%'></td>
                <td width='50%' style="text-align: center">{{ $bendahara->pangkat }}</td>
            </tr>
            <tr>
                <td width='50%'></td>
                <td width='50%' style="text-align: center">NIP. {{ $bendahara->nip }}</td>
            </tr> --}}
        </table>
    </div>
</body>

</html>
