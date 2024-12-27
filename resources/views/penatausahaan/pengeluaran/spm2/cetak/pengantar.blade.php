<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Pengantar</title>
    <style>
        h5 {
            font-weight: normal
        }

        #rincian>tbody>tr>td {
            vertical-align: top
        }

        .rincian1>tbody>tr>td {
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
            <td align="left" style="font-size:16px">
                <strong>
                    {{ $skpd->nm_skpd }}
                </strong>
            </td>
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
            <td style="font-size:16px"><strong><u>SURAT PENGANTAR</u></strong></td>
        </tr>
        @if (in_array($beban, ['2', '3', '4', '5', '6']))
            <tr>
                <td style="font-size:16px"><strong>Nomor : {{ $no_spm }}</strong></td>
            </tr>
        @endif
    </table>
    <br>
    <br>
    <table style="font-size: 13px;width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian1">
        <tr>
            <td>Kepada Yth.</td>
        </tr>
        <tr>
            <td>Kuasa Bendahara Umum Daerah Provinsi Kalimantan Barat</td>
        </tr>
        @if (in_array($beban, ['1', '2', '3', '4']))
            <tr>
                <td>SKPD : </td>
            </tr>
        @endif
        <tr>
            <td>Di <strong><u>Pontianak</u></strong></td>
        </tr>
        <tr>
            <td style="height: 20px"></td>
        </tr>
        <tr>
            <td>
                Dengan memperhatikan Peraturan Gubernur Kalimantan Barat
                {{ nogub($status_anggaran->jns_ang, $kd_skpd) }} tentang Penjabaran APBD Tahun Anggaran
                {{ $tahun_anggaran }},
                bersama ini kami mengajukan Surat Perintah Membayar @if ($beban == '1')
                    (SPM-UP)
                @elseif ($beban == '2')
                    (SPM-GU)
                @elseif ($beban == '3')
                    (SPM-TU)
                @elseif (in_array($beban, ['4', '5', '6']))
                    (SPM-LS)
                @endif Nomor {{ $no_spm }} tanggal
                @if ($tanpa == 1)
                    ______________{{ $tahun_anggaran }}
                @else
                    {{ tanggal($data_beban->tgl_spm) }}
                @endif untuk diterbitkan SP2D sebagai berikut:
            </td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;font-size: 13px;font-family:'Open Sans', Helvetica,Arial,sans-serif" id="rincian"
        class="rincian1">
        <tbody>
            <tr>
                <td style="width: 2%">1.</td>
                <td style="width: 35%">Urusan Pemerintahan</td>
                <td style="padding-right:5px">: </td>
                <td> {{ $data_beban->kd_bidang_urusan }} - {{ $data_beban->nm_bidang_urusan }}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>SKPD</td>
                <td style="padding-right:5px">: </td>
                <td> {{ $data_beban->kd_skpd }} - {{ $data_beban->nm_skpd }}</td>
            </tr>
            <tr>
                <td>3.</td>
                <td>Tahun Anggaran</td>
                <td style="padding-right:5px">: </td>
                <td> {{ $tahun_anggaran }}</td>
            </tr>
            <tr>
                <td>4.</td>
                <td>Dasar Pengeluaran SPD Nomor</td>
                <td style="padding-right:5px">: </td>
                <td> {{ $data_beban->no_spd }}</td>
            </tr>
            <tr>
                <td>5.</td>
                <td>Jumlah Sisa Dana SPD</td>
                <td style="padding-right:5px">: </td>
                <td> Rp. {{ rupiah($data_beban->spd - $data_beban->spp) }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td><i>Terbilang ({{ terbilang1($data_beban->spd - $data_beban->spp) }})</i></td>
            </tr>
            <tr>
                <td>6.</td>
                <td>Jumlah dana yang diminta untuk dicairkan</td>
                <td style="padding-right:5px">: </td>
                <td> Rp. {{ rupiah($data_beban->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td><i>Terbilang ({{ terbilang1($data_beban->nilai) }})</i></td>
            </tr>
            <tr>
                @if (in_array($beban, ['1', '2', '3', '4', '6']))
                    @if ($beban == '6' && $data_beban->jns_beban == '6')
                        <td>7.</td>
                        <td>Nama Pihak Ketiga</td>
                        <td style="padding-right:5px">: </td>
                        <td> {{ $data_beban->nmrekan }}</td>
                    @else
                        <td>7.</td>
                        <td>Nama Bendahara Pengeluaran</td>
                        <td style="padding-right:5px">:</td>
                        <td> {{ $bendahara->nama }}</td>
                    @endif
                @elseif ($beban == '5')
                    <td>7.</td>
                    <td>Nama Pihak Ketiga</td>
                    <td style="padding-right:5px">: </td>
                    <td> {{ $data_beban->nmrekan }}</td>
                @endif

            </tr>
            <tr>
                <td>8.</td>
                @if (in_array($beban, ['1', '2', '3', '4', '6']))
                    @if ($beban == '6' && $data_beban->jns_beban == '6')
                        <td>Nama dan Nomor Rekening dan NPWP</td>
                    @else
                        <td>Nama dan Nomor Rekening Bank</td>
                    @endif
                @elseif ($beban == '5')
                    <td>Nama dan Nomor Rekening dan NPWP</td>
                @endif
                <td style="padding-right:5px">: </td>
                @if ($beban == '1')
                    <td> {{ $data_beban->nama_bank }} - {{ $data_beban->no_rek }}</td>
                @elseif (in_array($beban, ['2', '3', '4', '6']))
                    @if ($beban == '6' && $data_beban->jns_beban == '6')
                        <td> {{ $data_beban->nama_bank_rek }} / {{ $data_beban->no_rek_rek }} /
                            {{ $data_beban->npwp_rek }}</td>
                    @else
                        <td> {{ cari_bank_spm($kd_skpd) }} - {{ $data_beban->no_rek }}</td>
                    @endif
                @elseif ($beban == '5')
                    <td> {{ $data_beban->nama_bank_rek }} / {{ $data_beban->no_rek_rek }} /
                        {{ $data_beban->npwp_rek }}</td>
                @endif
            </tr>
        </tbody>
    </table>

    <br>
    <br>
    <br>
    <table class="table" style="width: 100%;font-size: 13px;font-family:'Open Sans', Helvetica,Arial,sans-serif;"
        class="rincian1">
        @if (in_array($beban, ['1', '2', '3', '4', '5', '6']))
            <tr>
                <td style="margin: 2px 0px;text-align: center;padding-left:600px">
                    {{ $daerah->daerah }},
                    @if ($tanpa == 1)
                        ______________{{ $tahun_anggaran }}
                    @else
                        {{ tanggal($data_beban->tgl_spm) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center;padding-left:600px">
                    {{ $pptk->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:600px">
                    <strong><u>{{ $pptk->nama }}</u></strong> <br>
                    {{ $pptk->pangkat }} <br>
                    NIP. {{ $pptk->nip }}
                </td>
            </tr>
            {{-- <tr>
                <td style="text-align: center;padding-left:600px">{{ $pptk->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:600px">NIP. {{ $pptk->nip }}</td>
            </tr> --}}
        @endif
    </table>

</body>

</html>
