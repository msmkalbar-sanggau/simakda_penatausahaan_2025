<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .unbold {
            font-weight: normal
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
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="120"
                    height="150" />
            </td>
            <td align="left" style="font-size:16px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px"><strong>{{ $skpd->nm_skpd }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <hr>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td>
                <b>
                    <u>
                        @if ($beban == '4')
                            SURAT PERNYATAAN PENGAJUAN SPP - LS {{ strtoupper($lcbeban) }}
                        @else
                            SURAT PERNYATAAN PENGAJUAN SPP - {{ strtoupper($lcbeban) }}
                        @endif
                    </u>
                </b>
            </td>
        </tr>
        <tr>
            <td><b>Nomor : {{ $no_spp }}</b></td>
        </tr>
    </table>
    <br>
    <br>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td>
                @if ($beban == '4' || $beban == '6')
                    Sehubungan dengan Surat Permintaan Pembayaran Langsung (SPP - LS {{ strtoupper($lcbeban) }})
                @elseif ($beban == '5')
                    Sehubungan dengan Surat Permintaan Pembayaran Langsung (SPP - {{ strtoupper($lcbeban) }})
                @endif
                Nomor
                {{ $no_spp }} Tanggal
                {{ \Carbon\Carbon::parse($data->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }} yang kami ajukan
                sebesar
                {{ rupiah($data->nilai) }} ({{ ucwords(terbilang($data->nilai)) }})
            </td>
        </tr>
        <tr>
            <td style="height:20px"></td>
        </tr>
        <tr>
            <td>
                Untuk Keperluan OPD : {{ $data->nm_skpd }} Tahun Anggaran
                {{ $tahun_anggaran }}
            </td>
        </tr>
        <tr>
            <td style="height:20px"></td>
        </tr>
        <tr>
            <td>
                Dengan ini menyatakan sebenarnya bahwa :
            </td>
        </tr>
        <tr>
            <td style="height:10px"></td>
        </tr>
    </table>


    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td style="vertical-align: top">
                1.
            </td>
            <td style="vertical-align: top">
                Jumlah Pembayaran Langsung (LS)
                {{ $lcbeban }}
                tersebut di atas akan
                dipergunakan untuk
                keperluan guna membiayai kegiatan yang akan kami laksanan sesuai DPA-OPD
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                2.
            </td>
            <td style="vertical-align: top">
                Jumlah Pembayaran Langsung (LS)
                {{ $lcbeban }}
                tersebut tidak akan dipergunakan untuk
                membiayai pengeluaran-pengeluaran yang menurut ketentuan yang berlaku
                harus dilaksanakan dengan Pembayaran Langsung
                @if ($beban == '4')
                    LS-Gaji
                @else
                    LS-Barang dan Jasa
                @endif
            </td>
        </tr>
    </table>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td style="height:5px"></td>
        </tr>
        <tr>
            <td>
                Demikian Surat pernyataan ini dibuat untuk melengkapi persyaratan
                pengajuan SPP-LS
                {{ $lcbeban }} OPD kami
            </td>
        </tr>
    </table>

    <br>
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
            <tr>
                <td style="margin: 2px 0px;text-align: center;padding-left:500px">
                    {{ $daerah->daerah }},
                    @if ($tanpa == 1)
                        ______________{{ $tahun_anggaran }}
                    @else
                        {{ \Carbon\Carbon::parse($data->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center;padding-left:500px">
                    {{ $cari_bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:500px">
                    <b><u>{{ $cari_bendahara->nama }}</u></b> <br>
                    {{ $cari_bendahara->pangkat }} <br>
                    NIP. {{ $cari_bendahara->nip }}
                </td>
            </tr>
            {{-- <tr>
                <td style="text-align: center;padding-left:500px">{{ $cari_bendahara->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:500px">NIP. {{ $cari_bendahara->nip }}</td>
            </tr> --}}
        </table>
    </div>
</body>

</html>
