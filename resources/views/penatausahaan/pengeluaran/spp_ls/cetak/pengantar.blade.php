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
            <td style="font-size: 16px">
                <b>
                    @if ($beban == '4')
                        SURAT PERMINTAAN PEMBAYARAN LANGSUNG GAJI DAN TUNJANGAN <br>
                        (SPP - {{ strtoupper($lcbeban) }})
                    @elseif ($beban == '5')
                        SURAT PERMINTAAN PEMBAYARAN LANGSUNG PIHAK KETIGA LAINNYA <br>
                        (SPP - LS {{ strtoupper($lcbeban) }})
                    @else
                        SURAT PERMINTAAN PEMBAYARAN LANGSUNG BARANG DAN JASA <br>
                        (SPP - LS {{ strtoupper($lcbeban) }})
                    @endif
                </b>
            </td>
        </tr>
        <tr>
            <td style="font-size: 16px"><b><u>SURAT PENGANTAR</u></b></td>
        </tr>
        <tr>
            <td style="font-size: 16px"><b>Nomor : {{ $no_spp }}</b></td>
        </tr>
    </table>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <tr>
            <td>Kepada Yth:</td>
        </tr>
        <tr>
            <td>{{ $peng }}</td>
        </tr>
        <tr>
            <td>SKPD : {{ $cari_data->nm_skpd }}</td>
        </tr>
        <tr>
            <td>Di <b><u>{{ strtoupper($daerah->daerah) }}</u></b></td>
        </tr>
    </table>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <tr>
            <td style="height: 20px"></td>
        </tr>
        <tr>
            <td>
                @if ($beban == '4')
                    Dengan memperhatikan Peraturan Gubernur Kalimantan Barat
                    {{ $nogub }}
                    tentang Penjabaran APBD Tahun Anggaran {{ $tahun_anggaran }}. Bersama ini kami mengajukan Surat
                    Permintaan Pembayaran Langsung Barang dan Jasa sebagai berikut:
                @elseif ($beban == '5')
                    @if ($kd_skpd == '1.03.01.01')
                        Dengan memperhatikan Peraturan Gubernur Kalimantan Barat tentang
                        {{ $nogub }} tentang Perubahan Peraturan Gubernur Kalimantan Barat No. 84 Tahun 2015
                        tentang
                        Penjabaran APBD Tahun Anggaran {{ $tahun_anggaran }}. Bersama ini kami mengajukan Surat
                        Permintaan
                        Pembayaran Langsung Pihak Ketiga Lainnya sebagai berikut:
                    @else
                        Dengan memperhatikan Peraturan Gubernur Kalimantan Barat
                        {{ $nogub }} tentang Perubahan atas Peraturan Gubernur Nomor 155 Tahun 2020 tanggal
                        30
                        Desember 2020 tentang Penjabaran APBD Tahun Anggaran {{ $tahun_anggaran }}. Bersama ini
                        kami
                        mengajukan Surat Permintaan Pembayaran Langsung Pihak Ketiga Lainnya sebagai berikut:
                    @endif
                @else
                    @if ($kd_skpd == '1.03.01.01')
                        Dengan memperhatikan Peraturan Gubernur Kalimantan Barat
                        {{ $nogub }} tentang Perubahan Peraturan Gubernur Kalimantan Barat No. 84 Tahun 2015
                        tentang
                        Penjabaran APBD Tahun Anggaran {{ $tahun_anggaran }}. Bersama ini kami mengajukan Surat
                        Permintaan
                        Pembayaran Langsung Barang dan Jasa sebagai berikut:
                    @else
                        Dengan memperhatikan Peraturan Gubernur Kalimantan Barat
                        {{ $nogub }} tentang Perubahan atas Peraturan Gubernur Nomor 155 Tahun 2020 tanggal
                        30
                        Desember 2020 tentang Penjabaran APBD Tahun Anggaran {{ $tahun_anggaran }}. Bersama ini
                        kami
                        mengajukan Surat Permintaan Pembayaran Langsung Barang dan Jasa sebagai berikut:
                    @endif
                @endif
            </td>
        </tr>
    </table>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" id="rincian1">
        {{-- Urusan Pemerintahan --}}
        <tr>
            <td style="height: 5px"></td>
        </tr>
        <tr>
            <td style="width: 40%">a. Urusan Pemerintahan</td>
            <td>:</td>
            <td>{{ $cari_data->kd_bidang_urusan }} - {{ $cari_data->nm_bidang_urusan }}</td>
        </tr>
        {{-- OPD --}}
        <tr>
            <td>b. SKPD</td>
            <td>:</td>
            <td>{{ $cari_data->kd_skpd }} - {{ $cari_data->nm_skpd }}</td>
        </tr>
        {{-- Tahun Anggaran --}}
        <tr>
            <td>c. Tahun Anggaran</td>
            <td>:</td>
            <td>{{ $tahun_anggaran }}</td>
        </tr>
        {{-- Dasar Pengeluaran SPD --}}
        <tr>
            <td>d. Dasar Pengeluaran SPD</td>
            <td>:</td>
            <td>{{ $cari_data->no_spd }}</td>
        </tr>
        {{-- Jumlah Sisa Dana SPD --}}
        <tr>
            <td>e. Jumlah Sisa Dana SPD</td>
            <td>:</td>
            <td>Rp. {{ rupiah($cari_data->spd - $cari_data->spp) }}</td>
        </tr>
        <tr>
            <td style="text-align: center">(terbilang)</td>
            <td></td>
            <td style="font-style: italic">({{ ucwords(terbilang($cari_data->spd - $cari_data->spp)) }})</td>
        </tr>
        {{-- Untuk Keperluan Bulan --}}
        <tr>
            <td>f. Untuk Keperluan Bulan</td>
            <td>:</td>
            <td>{{ bulan($cari_data->bulan) }}</td>
        </tr>
        {{-- Jumlah Pembayaran yang Diminta --}}
        <tr>
            <td>g. Jumlah Pembayaran yang Diminta</td>
            <td>:</td>
            <td>Rp. {{ rupiah($cari_data->nilai) }}</td>
        </tr>
        <tr>
            <td style="text-align: center">(terbilang)</td>
            <td></td>
            <td style="font-style: italic">({{ ucwords(terbilang($cari_data->nilai)) }})</td>
        </tr>
        {{-- Nama Bendahara Pengeluaran --}}
        @if ($beban == '4')
            <tr>
                <td>h. Nama {{ ucwords($cari_bendahara->jabatan) }}</td>
                <td>:</td>
                <td>{{ $cari_bendahara->nama }}</td>
            </tr>
        @elseif ($beban == '5')
            @if ($jenis == '3')
                <tr>
                    <td>h. Nama Pihak Ketiga</td>
                    <td>:</td>
                    <td>{{ $cari_data->nmrekan }}</td>
                </tr>
            @else
                <tr>
                    <td>h. Nama Bendahara Pengeluaran</td>
                    <td>:</td>
                    <td>{{ $cari_bendahara->nama }}</td>
                </tr>
            @endif
        @else
            @if ($jenis == '3')
                <tr>
                    <td>h. Nama Pihak Ketiga</td>
                    <td>:</td>
                    <td>{{ $cari_data->nmrekan }}</td>
                </tr>
            @else
                <tr>
                    <td>h. Nama Bendahara Pengeluaran</td>
                    <td>:</td>
                    <td>{{ $cari_bendahara->nama }}</td>
                </tr>
            @endif
        @endif
        {{-- Nama Nomor Rekening Bank dan NPWP --}}
        <tr>
            @if ($beban == '4')
                <td>i. Nama, Nomor Rekening Bank dan NPWP</td>
                <td>:</td>
                <td>{{ $bank->nama }} / {{ $cari_data->no_rek }} / {{ $cari_data->npwp }}</td>
            @elseif ($beban == '5')
                @if ($jenis == '3')
                    <td>i. Nama, Nomor Rekening Bank dan NPWP</td>
                    <td>:</td>
                    <td>{{ $bank->nama }} / {{ $cari_data->no_rek }} / {{ $cari_data->npwp }}</td>
                @else
                    <td>i. Nama, Nomor Rekening Bank</td>
                    <td>:</td>
                    <td>{{ $bank->nama }} / {{ $cari_data->no_rek }}</td>
                @endif
            @else
                <td>i. Nama, Nomor Rekening Bank</td>
                <td>:</td>
                <td>{{ $bank->nama }} / {{ $cari_data->no_rek }}</td>
            @endif

        </tr>
    </table>

    <br>
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
            @if ($beban == '4')
                <tr>
                    <td style="margin: 2px 0px;text-align: center;padding-left:300px">
                        {{ $daerah->daerah }},
                        @if ($tanpa == 1)
                            ______________{{ $tahun_anggaran }}
                        @else
                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center;padding-left:300px">
                        {{ $cari_bendahara->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:300px">
                        <b><u>{{ $cari_bendahara->nama }}</u></b> <br>
                        {{ $cari_bendahara->pangkat }} <br>
                        NIP. {{ $cari_bendahara->nip }}
                    </td>
                </tr>
                {{-- <tr>
                    <td style="text-align: center;padding-left:600px">{{ $cari_bendahara->pangkat }}</td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:600px">NIP. {{ $cari_bendahara->nip }}</td>
                </tr> --}}
            @elseif ($beban == '5')
                @if ($sub_kegiatan == '5.02.00.0.06.62')
                    <tr>
                        <td style="margin: 2px 0px;text-align: center;padding-left:300px">
                            {{ $daerah->daerah }},
                            @if ($tanpa == 1)
                                ______________{{ $tahun_anggaran }}
                            @else
                                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 50px;text-align: center;padding-left:300px">
                            {{ $cari_bendahara->jabatan }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding-left:300px">
                            <b><u>{{ $cari_bendahara->nama }}</u></b> <br>
                            {{ $cari_bendahara->pangkat }} <br>
                            {{ $cari_bendahara->nip }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <td style="text-align: center;padding-left:300px">{{ $cari_bendahara->pangkat }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding-left:300px">{{ $cari_bendahara->nip }}</td>
                    </tr> --}}
                @else
                    <tr>
                        <td style="text-align: center">MENGETAHUI :</td>
                        <td style="margin: 2px 0px;text-align: center">
                            {{ $daerah->daerah }},
                            @if ($tanpa == 1)
                                ______________{{ $tahun_anggaran }}
                            @else
                                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 50px;text-align: center">
                            {{ $cari_pptk->jabatan }}
                        </td>
                        <td style="padding-bottom: 50px;text-align: center">
                            {{ $cari_bendahara->jabatan }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center">
                            <u><b>{{ $cari_pptk->nama }}</b></u> <br>
                            {{ $cari_pptk->pangkat }} <br>
                            NIP. {{ $cari_pptk->nip }}
                        </td>
                        <td style="text-align: center">
                            <u><b>{{ $cari_bendahara->nama }}</b></u> <br>
                            {{ $cari_bendahara->pangkat }} <br>
                            NIP. {{ $cari_bendahara->nip }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <td style="text-align: center">{{ $cari_pptk->pangkat }}</td>
                        <td style="text-align: center">{{ $cari_bendahara->pangkat }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center">NIP. {{ $cari_pptk->nip }}</td>
                        <td style="text-align: center">NIP. {{ $cari_bendahara->nip }}</td>
                    </tr> --}}
                @endif
            @elseif ($beban == '6')
                @if ($sub_kegiatan == '5.02.00.0.06.62')
                    <tr>
                        <td style="margin: 2px 0px;text-align: center;padding-left:300px">
                            {{ $daerah->daerah }},
                            @if ($tanpa == 1)
                                ______________{{ $tahun_anggaran }}
                            @else
                                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 50px;text-align: center;padding-left:300px">
                            {{ $cari_bendahara->jabatan }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding-left:300px">
                            <b><u>{{ $cari_bendahara->nama }}</u></b> <br>
                            {{ $cari_bendahara->pangkat }} <br>
                            {{ $cari_bendahara->nip }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <td style="text-align: center;padding-left:300px">{{ $cari_bendahara->pangkat }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding-left:300px">{{ $cari_bendahara->nip }}</td>
                    </tr> --}}
                @else
                    @if ($jumlah_spp > 0)
                        <tr>
                            <td style="margin: 2px 0px;text-align: center;padding-left:300px">
                                {{ $daerah->daerah }},
                                @if ($tanpa == 1)
                                    ______________{{ $tahun_anggaran }}
                                @else
                                    {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 50px;text-align: center;padding-left:300px">
                                {{ $cari_bendahara->jabatan }}
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;padding-left:300px">
                                <u><b>{{ $cari_bendahara->nama }}</b></u> <br>
                                {{ $cari_bendahara->pangkat }} <br>
                                {{ $cari_bendahara->nip }}
                            </td>
                        </tr>
                        {{-- <tr>
                            <td style="text-align: center;padding-left:300px">{{ $cari_bendahara->pangkat }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;padding-left:300px">{{ $cari_bendahara->nip }}</td>
                        </tr> --}}
                    @else
                        <tr>
                            <td style="text-align: center">MENGETAHUI :</td>
                            <td style="margin: 2px 0px;text-align: center">
                                {{ $daerah->daerah }},
                                @if ($tanpa == 1)
                                    ______________{{ $tahun_anggaran }}
                                @else
                                    {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 50px;text-align: center">
                                {{ $cari_pptk->jabatan }}
                            </td>
                            <td style="padding-bottom: 50px;text-align: center">
                                {{ $cari_bendahara->jabatan }}
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center">
                                {{ $cari_pptk->nama }} <br>
                                {{ $cari_pptk->pangkat }} <br>
                                NIP. {{ $cari_pptk->nip }}
                            </td>
                            <td style="text-align: center">
                                {{ $cari_bendahara->nama }} <br>
                                {{ $cari_bendahara->pangkat }} <br>
                                NIP. {{ $cari_bendahara->nip }}
                            </td>
                        </tr>
                        {{-- <tr>
                            <td style="text-align: center">{{ $cari_pptk->pangkat }}</td>
                            <td style="text-align: center">{{ $cari_bendahara->pangkat }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center">NIP. {{ $cari_pptk->nip }}</td>
                            <td style="text-align: center">NIP. {{ $cari_bendahara->nip }}</td>
                        </tr> --}}
                    @endif
                @endif
            @endif
        </table>
    </div>
</body>

</html>
