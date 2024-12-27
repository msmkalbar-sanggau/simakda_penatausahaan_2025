<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Pengantar</title>

    <style>
        #rincian1>tbody>tr>td {
            vertical-align: top;
            font-size: 14px
        }

        .rincian>tbody>tr>td {
            font-size: 14px
        }
    </style>
</head>

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
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

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td style="font-size: 16px">
                <b>
                    SURAT PERMINTAAN PEMBAYARAN GANTI UANG PERSEDIAAN
                </b>
            </td>
        </tr>
        <tr>
            <td style="font-size: 16px"><b>(SPP - GU)</b></td>
        </tr>
        <tr>
            <td style="font-size: 16px"><b><u>SURAT PENGANTAR</u></b></td>
        </tr>
        <tr>
            <td style="font-size: 16px"><b>Nomor : {{ $no_spp }}</b></td>
        </tr>
    </table>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td>Kepada Yth:</td>
        </tr>
        <tr>
            <td>{{ $peng }}</td>
        </tr>
        <tr>
            <td>SKPD : {{ $skpd->nm_skpd }}</td>
        </tr>
        <tr>
            <td>Di <b><u>Kabupaten Sanggau</u></b></td>
        </tr>
    </table>

    <br><br>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td style="text-align: justify">
                Dengan memperhatikan Peraturan Gubernur Kalimantan Barat
                tentang APBD atas Peraturan Gubernur Nomor 216 Tahun 2021 tanggal 31 Desember 2021 tentang Penjabaran
                APBD Tahun Anggaran {{ tahun_anggaran() }}, bersama ini kami mengajukan Surat Permintaan Pembayaran
                Ganti Uang
                Persediaan sebagai berikut:
            </td>
        </tr>
    </table>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" id="rincian1">
        <tr>
            <td style="height: 5px"></td>
        </tr>
        <tr>
            <td style="width: 40%">a. &nbsp;Urusan Pemerintahan</td>
            <td>:</td>
            <td>{{ $spp->kd_bidang_urusan }} - {{ $spp->nm_bidang_urusan }}</td>
        </tr>
        <tr>
            <td>b. &nbsp;SKPD</td>
            <td>:</td>
            <td>{{ $spp->kd_skpd }} - {{ $spp->nm_skpd }}</td>
        </tr>
        <tr>
            <td>c. &nbsp;Tahun Anggaran</td>
            <td>:</td>
            <td>{{ tahun_anggaran() }}</td>
        </tr>
        <tr>
            <td>d. &nbsp;Dasar Pengeluaran SPD</td>
            <td>:</td>
            <td>{{ $spp->no_spd }}</td>
        </tr>
        <tr>
            <td>e. &nbsp;Jumlah Sisa Dana SPD</td>
            <td>:</td>
            <td>Rp. {{ rupiah($spp->spd - $spp->spp) }}</td>
        </tr>
        <tr>
            <td style="text-align: center">(terbilang)</td>
            <td></td>
            <td style="font-style: italic">({{ ucwords(terbilang($spp->spd - $spp->spp)) }})</td>
        </tr>
        <tr>
            <td>f. &nbsp;Untuk Keperluan Bulan</td>
            <td>:</td>
            <td>{{ bulan($spp->bulan) }}</td>
        </tr>
        <tr>
            <td>g. Jumlah Pembayaran yang Diminta</td>
            <td>:</td>
            <td>Rp. {{ rupiah($spp->nilai) }}</td>
        </tr>
        <tr>
            <td style="text-align: center">(terbilang)</td>
            <td></td>
            <td style="font-style: italic">({{ ucwords(terbilang($spp->nilai)) }})</td>
        </tr>
        <tr>
            <td>h. Nama {{ $bendahara->jabatan }}</td>
            <td>:</td>
            <td>{{ $bendahara->nama }}</td>
        </tr>
        <tr>
            <td>i. &nbsp;Nama, Nomor Rekening Bank dan NPWP</td>
            <td>:</td>
            <td>{{ $nama_bank }} / {{ $spp->no_rek }} / {{ $spp->npwp }}</td>
        </tr>
    </table>

    <br>
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
            <tr>
                {{-- <td style="text-align: center">MENGETAHUI :</td> --}}
                <td style="width:50%"></td>
                <td style="margin: 2px 0px;text-align: center">
                    {{ $daerah->daerah }},
                    @if ($tanpa == 1)
                        ______________{{ tahun_anggaran() }}
                    @else
                        {{ tanggal($spp->tgl_spp) }}
                    @endif
                </td>
            </tr>
            <tr>
                {{-- <td style="padding-bottom: 50px;text-align: center">
                    {{ $pptk->jabatan }}
                </td> --}}
                <td style="width:50%"></td>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                {{-- <td style="text-align: center">
                    <u><b>{{ $pptk->nama }}</b></u> <br>
                    {{ $pptk->pangkat }} <br>
                    NIP. {{ $pptk->nip }}
                </td> --}}
                <td style="width:50%"></td>
                <td style="text-align: center">
                    <u><b>{{ $bendahara->nama }}</b></u> <br>
                    {{ $bendahara->pangkat }} <br>
                    NIP. {{ $bendahara->nip }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
