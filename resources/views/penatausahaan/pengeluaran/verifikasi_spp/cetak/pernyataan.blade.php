<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Pernyataan</title>
    <style>
        h5 {
            font-weight: normal
        }

        .rincian>tbody>tr>td {
            font-size: 14px
        }
    </style>
</head>

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75" height="100" />
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
    <br>
    <div style="text-align: center">
        <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;">
            <tr>
                <td style="font-size: 16px"><strong>SURAT PERNYATAAN</td>
            </tr>
            <tr>
                <td style="font-size: 16px"><strong>VERIFIKASI KELENGKAPAN DAN KEABSAHAN</td>
            </tr>
            <tr>
                <td style="font-size: 16px;padding-bottom:50px;"><strong>DOKUMEN DAN LAMPIRAN SPP-{{ cari_jenis($spp->jns_spp) }}</td>
            </tr>
        </table>
    </div>
    <div>
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;">
            <tr>
                <td style="width: 50px;padding-bottom:20px;">&nbsp;</td>
                <td colspan="3">Saya yang bertanda tangan di bawah ini :</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td style="width: 5%">Nama</td>
                <td style="width:2%">:</td>
                <td style="font-weight: bold">{{ $pptk->nama }}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>NIP</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $pptk->nip }}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>Jabatan</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $pptk->jabatan }}</td>
            </tr>
        </table>
    </div>

    <div>
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;">
            <tr>
                <td style="padding-top:20px;text-indent:50px;">
                    Menyatakan dengan sesungguhnya bahwa dokumen dan lampiran <b>Surat Pemintaan Pembayaran {{ cari_jenis($spp->jns_spp) }}</b> nomor
                    <b>{{ $spp->no_spp }}</b> tanggal <b>{{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</b> telah
                    <b>saya verifikasi secara lengkap dan sah sesuai ketentuan peraturan perundang-undangan</b>
                </td>
            </tr>
            <tr>
                <td style="padding-top:20px;text-indent:50px;">
                    Demikian surat pernyataan ini dibuat untuk melengkapi persyaratan pengajuan <b>SPM-{{ cari_jenis($spp->jns_spp) }} SKPD</b> kami.
                </td>
            </tr>
        </table>
    </div>

    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;">
            <tr>
                <td style="margin: 2px 0px;text-align: center;padding-left:500px">
                    {{ $daerah->daerah }},
                    {{ \Carbon\Carbon::parse($spp->tgl_terima_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center;padding-left:500px">
                    {{ $pptk->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:500px">
                    <strong><u>{{ $pptk->nama }}</u></strong> <br>
                    {{ $pptk->pangkat }} <br>
                    NIP. {{ $pptk->nip }}
                </td>
            </tr>
            {{-- <tr>
                <td style="text-align: center;padding-left:500px">{{ $pa_kpa->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:500px">NIP. {{ $pa_kpa->nip }}</td>
            </tr> --}}
        </table>
    </div>
</body>

</html>
