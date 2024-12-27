<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .a1 {
            padding-left: 20px;
        }

        .a2 {
            padding-left: 30px;
        }
    </style>
</head>

<body>
    <div style="text-align: center;margin-top:20px">
        <h5 style="margin: 2px 0px"><strong>LEMBAR VERIFIKASI KELENGKAPAN DOKUMEN PENERBITAN DANA (SP2D-UP)</strong></h5>
        <div style="clear: both"></div>
    </div>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>SKPD/SATEKER</td>
                <td>:</td>
                <td>{{ $sp2d->nm_skpd }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>PROVINSI KALIMANTAN BARAT</td>
            </tr>
            <tr>
                <td colspan="3">A. PENERIMAAN
                    @if (in_array($beban, ['1', '4']))
                        SPM
                    @elseif ($beban == '2')
                        SPM
                    @elseif ($beban == '3')
                        SPM-TU
                    @elseif (in_array($beban, ['5', '6']))
                        SPM-LS
                    @endif
                </td>
            </tr>
            <tr>
                <td class="a1">1. Nomor dan Tanggal
                    @if (in_array($beban, ['1', '4']))
                        SPM
                    @elseif ($beban == '2')
                        SPM-GU
                    @elseif ($beban == '3')
                        SPM-TU
                    @elseif (in_array($beban, ['5', '6']))
                        SPM-LS
                    @endif
                </td>
                <td>:</td>
                <td>{{ $sp2d->no_spm }} dan {{ tanggal($sp2d->tgl_spm) }}</td>
            </tr>
            <tr>
                <td class="a1">2. Tanggal Terima
                    @if (in_array($beban, ['1', '4']))
                        SPM
                    @elseif ($beban == '2')
                        SPM-GU
                    @elseif ($beban == '3')
                        SPM-TU
                    @elseif (in_array($beban, ['5', '6']))
                        SPM-LS
                    @endif
                </td>
                <td>:</td>
                <td>........................................................................</td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top:20px">B. KELENGKAPAN PERSYARATAN</td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%" style="border:1px solid black">
        <thead>
            <th></th>
            <th style="font-weight: normal">Ada</th>
            <th style="font-weight: normal">Tidak</th>
        </thead>
        <tbody>
            @include('penatausahaan.pengeluaran.sp2d.cetak.kelengkapan_persyaratan')
        </tbody>
    </table>
    <table style="margin-top: 20px">
        <tr>
            <td class="a1">3. Tanggal Pengembalian SPM</td>
            <td>:</td>
            <td>........................................................................</td>
        </tr>
        <tr>
            <td class="a1">4. Tanggal Terima Kembali SPM</td>
            <td>:</td>
            <td>........................................................................</td>
        </tr>
    </table>
    <div style="padding-top:20px">
        <table class="table" style="width: 100%">
            <tr>
                <td style="text-align: center">Diperiksa dan diteruskan oleh</td>
                <td style="margin: 2px 0px;text-align: center">
                    Diteliti dan dikerjakan oleh
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $ttd1->jabatan }}
                </td>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $ttd2->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center"><b>{{ $ttd1->nama }}</b></td>
                <td style="text-align: center"><b>{{ $ttd2->nama }}</b></td>
            </tr>
            <tr>
                <td style="text-align: center">{{ $ttd1->pangkat }}</td>
                <td style="text-align: center">{{ $ttd2->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center">NIP. {{ $ttd1->nip }}</td>
                <td style="text-align: center">NIP. {{ $ttd2->nip }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
