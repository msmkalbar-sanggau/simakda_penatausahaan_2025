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
            <td align="left" style="font-size:18px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px">
                <strong>
                    {{ $nama_skpd->nm_skpd }}
                </strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <hr>
    <br>
    <div style="text-align: center">
        <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;">
            <tr>
                <td style="font-size: 16px"><strong><u>SURAT PERNYATAAN SPM
                            {{ Str::upper(nama_beban($beban, $data_beban->jenis_beban)) }}</u></strong></td>
            </tr>
            <tr>
                <td style="font-size: 16px"><strong>Nomor: {{ $no_spm }}</strong></td>
            </tr>
        </table>
    </div>
    <br>
    <div>
        <table class="rincian"
            style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:justify">
            <tr>
                <td>Sehubungan dengan Surat Perintah Membayar
                    {{ nama_spm($beban, $data_beban->jenis_beban) }}
                    Nomor {{ $no_spm }} Tanggal {{ tanggal($data_beban->tgl_spm) }} yang kami ajukan sebesar
                    Rp{{ rupiah($data_beban->nilai) }} ({{ ucwords(terbilang($data_beban->nilai)) }})</td>
            </tr>
            <tr>
                <td>Untuk Keperluan SKPD {{ $data_beban->nm_skpd }} Tahun Anggaran
                    {{ $tahun_anggaran }}</td>
            </tr>
            <tr>
                <td style="height: 10px"></td>
            </tr>
            <tr>
                <td>Dengan ini menyatakan sebenarnya bahwa :</td>
            </tr>
            <tr>
                <td style="height: 10px"></td>
            </tr>
        </table>
    </div>
    <div>
        <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;" class="rincian">
            <tr>
                <td style="padding-left:10px;width:2%;vertical-align:top">1.</td>
                <td style="text-align:justify">
                    Jumlah Pembayaran {{ nama_spm1($beban, $data_beban->jenis_beban) }}
                    tersebut di atas akan dipergunakan untuk keperluan guna membiayai kegiatan yang akan kami
                    laksanakan sesuai DPA-SKPD.
                </td>
            </tr>
            <tr>
                <td style="padding-left:10px;vertical-align:top">2.</td>
                <td style="text-align: justify">
                    Jumlah Pembayaran {{ nama_spm1($beban, $data_beban->jenis_beban) }}
                    tersebut tidak akan
                    dipergunakan untuk membiayai pengeluaran-pengeluaran yang menurut ketentuan yang berlaku
                    harus dilaksanakan dengan Pembayaran Langsung (LS).
                    {{-- {{ nama_beban1($beban, $data_beban->jenis_beban) }} --}}
                </td>
            </tr>
            <tr>
                <td style="height: 10px"></td>
            </tr>
            <tr>
                <td colspan="2">Demikian Surat Pernyataan ini dibuat untuk melengkapi persyaratan pengajuan
                    SPM-{{ nama_beban2($beban, $data_beban->jenis_beban) }}.</td>
            </tr>
        </table>
    </div>
    <br>
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;">
            <tr>
                <td style="margin: 2px 0px;text-align: center;padding-left:500px">
                    {{ $daerah->daerah }},
                    @if ($tanpa == 1)
                        ______________{{ $tahun_anggaran }}
                    @else
                        {{ tanggal($data_beban->tgl_spm) }}
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
                    <strong><u>{{ $pa_kpa->nama }}</u></strong> <br>
                    {{ $pa_kpa->pangkat }} <br>
                    NIP. {{ $pa_kpa->nip }}
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
