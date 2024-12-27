<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Pernyataan</title>
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
            <td align="left" style="font-size:16px"><strong>{{ $skpd->nm_skpd }}</strong></td>
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
            <td>
                <b>
                    <u>
                        SURAT PERNYATAAN PENGAJUAN SPP - TU
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
                Sehubungan dengan Surat Permintaan Pembayaran Tambahan Uang Persediaan (SPP - TU) Nomor
                {{ $no_spp }} Tanggal
                {{ tanggal($data->tgl_spp) }} yang kami ajukan sebesar
                {{ rupiah($data->nilai) }} ({{ ucwords(terbilang($data->nilai)) }})
            </td>
        </tr>
        <tr>
            <td style="height:20px"></td>
        </tr>
        <tr>
            <td>
                Untuk Keperluan SKPD : {{ $data->nm_skpd }} Tahun Anggaran
                {{ tahun_anggaran() }}
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
                Jumlah Tambahan Uang Persediaan (TU) tersebut di atas akan dipergunakan untuk keperluan membayar
                kegiatan yang akan kami laksanan sesuai DPA-OPD
                dalam waktu 1 (satu) bulan sejak diterbitkan SP2D
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                2.
            </td>
            <td style="vertical-align: top">
                Apabila dana TU tersebut sampai batas 1 (satu) bulan tidak habis terpakai maka sisa dana TU akan kami
                setorkan ke Rekening Kas Umum Daerah Pemerintah
                Provinsi Kalimatan Barat.
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                3.
            </td>
            <td style="vertical-align: top">
                Jumlah Tambahan Uang Persediaan (TU) tersebut tidak akan dipergunakan untuk membiayai
                pengeluaran-pengeluaran yang menurut ketentuan yang berlaku
                harus dilaksanakan dengan Pembayaran Langsung TU
            </td>
        </tr>
    </table>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td style="height:5px"></td>
        </tr>
        <tr>
            <td>
                Demikian Surat pernyataan ini dibuat untuk melengkapi persyaratan pengajuan SPP-TU SKPD kami
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
                        ______________{{ tahun_anggaran() }}
                    @else
                        {{ tanggal($data->tgl_spp) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center;padding-left:500px">
                    {{ $pa_kpa->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:500px">
                    <b><u>{{ $pa_kpa->nama }}</u></b> <br>
                    {{ $pa_kpa->pangkat }} <br>
                    NIP. {{ $pa_kpa->nip }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
