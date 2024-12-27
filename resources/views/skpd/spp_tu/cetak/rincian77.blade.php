<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Rincian</title>
    <style>
        .unborder {
            font-weight: normal
        }

        table,
        tr,
        td {
            border-collapse: collapse
        }

        .rincian>tbody>tr>td {
            font-size: 14px
        }
    </style>
</head>

<body>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td><b>PEMERINTAH PROVINSI KALIMANTAN BARAT</b></td>
        </tr>
        <tr>
            <td><b>SURAT PERMINTAAN PEMBAYARAN TAMBAHAN UANG PERSEDIAAN (SPP-TU)</b></td>
        </tr>
        <tr>
            <td>Nomor : {{ $no_spp }}</td>
        </tr>
        <tr>
            <td>Tahun Anggaran : {{ tahun_anggaran() }}</td>
        </tr>
        <tr>
            <td style="height: 5px"></td>
        </tr>
        <tr>
            <td><b>RINCIAN RENCANA PENGGUNAAN</b></td>
        </tr>
    </table>

    <br>

    <table class="table table-bordered rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif"
        border="1">
        <tr>
            <td style="font-weight: bold;text-align:center">No</td>
            <td style="font-weight: bold;text-align:center">Kode Rekening</td>
            <td style="font-weight: bold;text-align:center">Uraian</td>
            <td style="font-weight: bold;text-align:center">Nilai Rupiah</td>
        </tr>
        <tr>
            <td colspan="4">{{ $nama_kegiatan->nm_kegiatan }} / {{ $data_spp->nm_sub_kegiatan }}</td>
        </tr>
        @foreach ($data_spp_rinci as $item)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td>{{ $item->kd_rek6 }}</td>
                <td>{{ $item->nm_rek6 }}</td>
                <td style="text-align: right">{{ rupiah($item->nilaispp) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="text-align:right" colspan="3">Total</td>
            <td style="text-align:right">{{ rupiah($data_spp->nilaisub) }}</td>
        </tr>
    </table>

    <br>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td>Terbilang: ## <span style="font-style:italic">({{ ucwords(terbilang($data_spp->nilaisub)) }})</span> ##
            </td>
        </tr>
    </table>

    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
            @if ($sub_kegiatan == '5.02.00.0.06.62')
                <tr>
                    <td style="margin: 2px 0px;text-align: center;padding-left:300px">
                        Pontianak,
                        @if ($tanpa == 1)
                            ______________{{ tahun_anggaran() }}
                        @else
                            {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('D MMMM Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center;padding-left:300px">
                        {{ $bendahara->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:300px">
                        <b><u>{{ $bendahara->nama }}</u></b> <br>
                        {{ $bendahara->pangkat }} <br>
                        NIP. {{ $bendahara->nip }}
                    </td>
                </tr>
            @else
                <tr>
                    <td style="text-align: center;padding-left:100px">Mengetahui/Menyetujui:</td>
                    <td style="margin: 2px 0px;text-align: center;padding-left:300px">
                        Pontianak,
                        @if ($tanpa == 1)
                            ______________{{ tahun_anggaran() }}
                        @else
                            {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center;padding-left:100px;font-weight: bold">
                        {{ $bendahara->jabatan }}
                    </td>
                    <td style="padding-bottom: 50px;text-align: center;padding-left:300px;font-weight: bold">
                        {{ $pptk->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:100px">
                        <u>{{ $bendahara->nama }}</u> <br>
                        {{ $bendahara->pangkat }} <br>
                        NIP. {{ $bendahara->nip }}
                    </td>
                    <td style="text-align: center;padding-left:300px">
                        <u>{{ $pptk->nama }}</u> <br>
                        {{ $pptk->pangkat }} <br>
                        NIP. {{ $pptk->nip }}
                    </td>
                </tr>
            @endif
        </table>
    </div>
</body>

</html>
