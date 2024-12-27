<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Rincian</title>
    <style>
        table,
        th,
        td {
            border-collapse: collapse;
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
            <td style="font-size: 16px"><b>SURAT PERMINTAAN PEMBAYARAN {{ strtoupper($lcbeban) }}</b></td>
        </tr>
        <tr>
            <td style="font-size: 16px"><b>(SPP - LS {{ strtoupper($lcbeban) }})</b></td>
        </tr>
        <tr>
            <td style="font-size: 16px"><b>Nomor : {{ $no_spp }}</b></td>
        </tr>
    </table>
    <br>

    <table class="table rincian"
        style="width: 100%;border:1px black solid;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        {{-- OPD --}}
        <tr>
            <td width='30%'>1. OPD</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $nama_skpd }}</td>
        </tr>
        {{-- Unit Kerja --}}
        <tr>
            <td width='30%'>2. Unit Kerja</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $nama_skpd }}</td>
        </tr>
        {{-- Alamat --}}
        <tr>
            <td width='30%'>3. Alamat</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $alamat_skpd }}</td>
        </tr>
        {{-- Nomor dan Tanggal DPA/DPPA/DPPAL-OPD --}}
        <tr>
            <td width='30%'>4. Nomor dan Tanggal DPA/DPPA/DPPAL-OPD</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $dpa->no_dpa }} / {{ $dpa->tgl_dpa }}</td>
        </tr>
        {{-- Tahun Anggaran --}}
        <tr>
            <td width='30%'>5. Tahun Anggaran</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $tahun_anggaran }}</td>
        </tr>
        {{-- Bulan --}}
        <tr>
            <td width='30%'>6. Bulan</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ bulan($data_spp->bulan) }} {{ $tahun_anggaran }}</td>
        </tr>
        {{-- Urusan Pemerintahan --}}
        <tr>
            <td width='30%'>7. Urusan Pemerintahan</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $data_spp->kd_bidang_urusan }} {{ $data_spp->nm_bidang_urusan }}</td>
        </tr>
        {{-- Nama Program --}}
        <tr>
            <td width='30%'>8. Nama Program</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $nama_program }}</td>
        </tr>
        {{-- Nama Kegiatan --}}
        <tr>
            <td width='30%'>9. Nama Kegiatan</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $nama_kegiatan }}</td>
        </tr>
        {{-- Nama Sub Kegiatan --}}
        <tr>
            <td width='30%'>10. Nama Sub Kegiatan</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $data_spp->nm_sub_kegiatan }}</td>
        </tr>
    </table>

    <br>

    <table style="width: 100%;text-align:center;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <tr>
            <td><b>Kepada Yth:</b></td>
        </tr>
        <tr>
            <td><b>Pengguna Anggaran/Kuasa Pengguna Anggaran</b></td>
        </tr>
        <tr>
            <td><b>{{ $nama_skpd }}</b></td>
        </tr>
        <tr>
            <td><b>di {{ $daerah->daerah }}</b></td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <tr>
            <td style="text-align: justify">Dengan memperhatikan Peraturan Gubernur Kalimantan Barat
                {{ $nogub }}
                tentang Penjabaran APBD Tahun
                Anggaran {{ $tahun_anggaran }}, bersama ini kami mengajukan Surat Permintaan Pembayaran Langsung Barang
                dan
                Jasa sebagai berikut:</td>
        </tr>
    </table>
    <br>
    <table class="table rincian"
        style="width: 100%;border:1px black solid;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        {{-- Jumlah Pembayaran Yang Diminta --}}
        <tr>
            <td width='30%'>a. Jumlah Pembayaran Yang Diminta</td>
            <td width='2%'>:</td>
            <td width='68%'>Rp.{{ rupiah($data_spp->nilai) }}</td>
        </tr>
        {{-- Terbilang --}}
        <tr>
            <td style="width:400px;text-align:center">(terbilang)</td>
            <td>:</td>
            <td style="font-style: italic">{{ ucwords(terbilang($data_spp->nilai)) }}</td>
        </tr>
        {{-- Untuk Keperluan --}}
        <tr>
            <td width='30%'>b. Untuk Keperluan</td>
            <td width='2%'>:</td>
            <td width='68%'>{{ $data_spp->keperluan }}</td>
        </tr>
        {{-- Nama Bendahara --}}
        @if ($beban == '6' && $jenis == '6')
            <tr>
                <td width='30%'>c. Nama Pihak Ketiga</td>
                <td width='2%'>:</td>
                <td width='68%'>{{ $data_spp->nmrekan }}</td>
            </tr>
            <tr>
                <td width='30%'>d. Dasar Bendahara Pengeluaran</td>
                <td width='2%'>:</td>
                <td width='68%'>{{ $no_spd }}</td>
            </tr>
            <tr>
                <td width='30%'>e. Alamat</td>
                <td width='2%'>:</td>
                <td width='68%'>{{ $data_spp->alamat }}</td>
            </tr>
            <tr>
                <td width='30%'>f. Nama dan Nomor Rekening</td>
                <td width='2%'>:</td>
                <td width='68%'>{{ $data_spp->nama_bank_rek }} / {{ $data_spp->no_rek_rek }}</td>
            </tr>
        @else
            <tr>
                <td width='30%'>c. Nama Bendahara</td>
                <td width='2%'>:</td>
                <td width='68%'>{{ $cari_bendahara->nama }}</td>
            </tr>
            <tr>
                <td width='30%'>d. Dasar Bendahara Pengeluaran</td>
                <td width='2%'>:</td>
                <td width='68%'>{{ $no_spd }}</td>
            </tr>
            <tr>
                <td width='30%'>e. Alamat</td>
                <td width='2%'>:</td>
                <td width='68%'>{{ $data_spp->alamat }}</td>
            </tr>
            <tr>
                <td width='30%'>f. Nama dan Nomor Rekening</td>
                <td width='2%'>:</td>
                <td width='68%'>{{ $nama_bank->nama }} / {{ $data_spp->no_rek }}</td>
            </tr>
        @endif
    </table>

    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
            @if ($beban == '4' || $sub_kegiatan == '5.02.00.0.06.62')
                <tr>
                    <td style="margin: 2px 0px;text-align: center;padding-left:500px">
                        {{ $daerah->daerah }},
                        @if ($tanpa == 1)
                            ______________{{ $tahun_anggaran }}
                        @else
                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center;padding-left:500px">
                        {{ $cari_bendahara->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:500px"><b><u>{{ $cari_bendahara->nama }}</u></b></td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:500px">{{ $cari_bendahara->pangkat }}</td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:500px">NIP. {{ $cari_bendahara->nip }}</td>
                </tr>
            @elseif ($beban == '2')
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
                    <td style="text-align: center;padding-left:300px"><b><u>{{ $cari_bendahara->nama }}</u></b></td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:300px">{{ $cari_bendahara->pangkat }}</td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:300px">NIP. {{ $cari_bendahara->nip }}</td>
                </tr>
            @else
                @if ($jumlah_spp > 0 || $cari_pptk->jabatan == '')
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
                        <td style="text-align: center;padding-left:300px"><b><u>{{ $cari_bendahara->nama }}</u></b>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding-left:300px">{{ $cari_bendahara->pangkat }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding-left:300px">NIP. {{ $cari_bendahara->nip }}</td>
                    </tr>
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
                        <td style="text-align: center"><b><u>{{ $cari_pptk->nama }}</u></b></td>
                        <td style="text-align: center"><b><u>{{ $cari_bendahara->nama }}</u></b></td>
                    </tr>
                    <tr>
                        <td style="text-align: center">{{ $cari_pptk->pangkat }}</td>
                        <td style="text-align: center">{{ $cari_bendahara->pangkat }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center">{{ $cari_pptk->nip }}</td>
                        <td style="text-align: center">NIP. {{ $cari_bendahara->nip }}</td>
                    </tr>
                @endif
            @endif
        </table>
    </div>
</body>

</html>
