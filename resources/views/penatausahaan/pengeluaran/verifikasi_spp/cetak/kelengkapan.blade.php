<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KELENGKAPAN SPM</title>
    <style>
        .row1 {
            width: 80%;
            padding-left: 30px;
        }

        .row2 {
            border: 1px solid black;
            width: 10%;
        }

        .row3 {
            border: 1px solid black;
            width: 10%
        }

        .row4 {
            width: 80%;
            padding-left: 40px;
        }

        .row5 {
            padding-left: 50px
        }

        .row6 {
            padding-left: 60px
        }

        table,
        th,
        td {
            font-weight: normal;
            font-size: 12px;
            font-family: 'Open Sans', sans-serif;
        }

        h3,
        h4 {
            font-family: 'Open Sans', sans-serif;
        }

        .judul1 {
            padding-left: 10px
        }

        .judul2 {
            padding-left: 30px
        }

        .rincian>tbody>tr>td {
            font-size: 14px;
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

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td style="font-weight:bold;font-size:18px">
                @if ($beban == '1')
                    Laporan Penelitian Kelengkapan Dokumen Penerbitan SPM-UP(PPK/PPKP)
                @elseif ($beban == '2')
                    Laporan Penelitian Kelengkapan Dokumen Penerbitan SPM-GU(PPK/PPKP)
                @elseif ($beban == '3')
                    Laporan Penelitian Kelengkapan Dokumen Penerbitan SPM-TU(PPK/PPKP)
                @elseif ($beban == '4')
                    Laporan Penelitian Kelengkapan Dokumen Penerbitan SPM-LS(PPK/PPKP)
                    @if ($jenis == '1')
                        (Untuk Gaji Induk, Gaji Terusan, Kekurangan Gaji)
                    @elseif ($jenis == '3')
                        (Untuk Tambahan Penghasilan)
                    @elseif ($jenis == '5')
                        (Untuk Honorarium Tenaga Kontrak)
                    @elseif ($jenis == '6')
                        (Untuk Pengadaan Barang dan Jasa/Konstruksi/Konsultansi)
                    @elseif ($jenis == '7')
                        (Untuk Pengadaan Konsumsi)
                    @elseif ($jenis == '8')
                        (Untuk Sewa Rumah Jabatan/Gedung untuk Kantor/G
                        Pertemuan/Tempat
                        Pertemuan/Tempat Penginapan/Kendaraan)</h3>
                    @elseif ($jenis == '9')
                        (Untuk Pengadaan Sertifikat Tanah)
                    @elseif ($jenis == '10')
                        (Untuk Pengadaan Tanah)
                    @elseif ($jenis == '11')
                        (Untuk Hibah Barang dan Jasa pada Pihak Ketiga)
                    @elseif ($jenis == '12')
                        (Untuk LS Bantuan Sosial pada Pihak Ketiga)
                    @elseif ($jenis == '13')
                        (Untuk Hibah Uang Pada Pihak Ketiga)
                    @elseif ($jenis == '14')
                        (Untuk Bantuan Keuangan Pada Kabupaten/Kota)
                    @elseif ($jenis == '15')
                        (Untuk Bagi Hasil Pajak dan Bukan Pajak)
                    @elseif ($jenis == '16')
                        (Untuk Hibah Konstruksi Pada Pihak Ketiga)
                    @else
                    @endif
                @elseif ($beban == '5')
                    Laporan Penelitian Kelengkapan Dokumen Penerbitan SPM-LS(PPK/PPKP)
                    @if ($jenis == '1')
                        (Untuk Gaji Induk, Gaji Terusan, Kekurangan Gaji)
                    @elseif ($jenis == '3')
                        (Untuk Tambahan Penghasilan)
                    @elseif ($jenis == '4')
                        (Untuk Honorarium PNS)
                    @elseif ($jenis == '5')
                        (Untuk Honorarium Tenaga Kontrak)
                    @elseif ($jenis == '6')
                        (Untuk Pengadaan Barang dan Jasa/Konstruksi/Konsultansi)
                    @elseif ($jenis == '7')
                        (Untuk Pengadaan Konsumsi)
                    @elseif ($jenis == '8')
                        (Untuk Sewa Rumah Jabatan/Gedung untuk Kantor/G
                        Pertemuan/Tempat
                        Pertemuan/Tempat Penginapan/Kendaraan)</h3>
                    @elseif ($jenis == '9')
                        (Untuk Pengadaan Sertifikat Tanah)
                    @elseif ($jenis == '10')
                        (Untuk Pengadaan Tanah)
                    @elseif ($jenis == '11')
                        (Untuk Hibah Barang dan Jasa pada Pihak Ketiga)
                    @elseif ($jenis == '12')
                        (Untuk LS Bantuan Sosial pada Pihak Ketiga)
                    @elseif ($jenis == '13')
                        (Untuk Hibah Uang Pada Pihak Ketiga)
                    @elseif ($jenis == '14')
                        (Untuk Bantuan Keuangan Pada Kabupaten/Kota)
                    @elseif ($jenis == '15')
                        (Untuk Bagi Hasil Pajak dan Bukan Pajak)
                    @elseif ($jenis == '16')
                        (Untuk Hibah Konstruksi Pada Pihak Ketiga)
                    @elseif ($jenis == '98')
                        (Untuk Pengadaan Barang dan Jasa/Konstruksi/Konsultansi)
                    @elseif ($jenis == '99')
                        (Untuk Pengeluaran Pembiayaan)
                    @endif
                @elseif ($beban == '6')
                    Laporan Penelitian Kelengkapan Dokumen Penerbitan SPM-LS(PPK/PPKP)
                    @if ($jenis == '1')
                        (Untuk Gaji Induk, Gaji Terusan, Kekurangan Gaji)
                    @elseif ($jenis == '3')
                        (Untuk Tambahan Penghasilan)
                    @elseif ($jenis == '4')
                        (Untuk Honorarium PNS)
                    @elseif ($jenis == '5')
                        (Untuk Honorarium Tenaga Kontrak)
                    @elseif ($jenis == '6')
                        (Untuk Pengadaan Barang dan Jasa/Konstruksi/Konsultansi)
                    @elseif ($jenis == '7')
                        (Untuk Pengadaan Konsumsi)
                    @elseif ($jenis == '8')
                        (Untuk Sewa Rumah Jabatan/Gedung untuk Kantor/G
                        Pertemuan/Tempat
                        Pertemuan/Tempat Penginapan/Kendaraan)</h3>
                    @elseif ($jenis == '9')
                        (Untuk Pengadaan Sertifikat Tanah)
                    @elseif ($jenis == '10')
                        (Untuk Pengadaan Tanah)
                    @elseif ($jenis == '11')
                        (Untuk Hibah Barang dan Jasa pada Pihak Ketiga)
                    @elseif ($jenis == '12')
                        (Untuk LS Bantuan Sosial pada Pihak Ketiga)
                    @elseif ($jenis == '13')
                        (Untuk Hibah Uang Pada Pihak Ketiga)
                    @elseif ($jenis == '14')
                        (Untuk Bantuan Keuangan Pada Kabupaten/Kota)
                    @elseif ($jenis == '15')
                        (Untuk Bagi Hasil Pajak dan Bukan Pajak)
                    @elseif ($jenis == '16')
                        (Untuk Hibah Konstruksi Pada Pihak Ketiga)
                    @elseif ($jenis == '98')
                        (Belanja Operasional KDH/WKDH dan Pimpinan DPRD)
                    @elseif ($jenis == '99')
                        (Pembiayaan pada Pihak Ketiga Lainnya)
                    @endif
                @else
                @endif
            </td>
        </tr>
    </table>


    <br>
    <br>


    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        @if ($beban == '1')
            <tr>
                <td>SKPD/BIRO/UPT</td>
                <td>:</td>
                <td>{{ $nm_skpd }}</td>
            </tr>
            <tr>
                <td colspan="3" class="judul1">A. PENERIMAAN SPP-UP</td>
            </tr>
            <tr>
                <td class="judul2">1. Nomor dan Tanggal SPP-UP</td>
                <td>:</td>
                <td>{{ $spp->no_spp }} dan
                    {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td class="judul2">2. Tanggal Terima SPP-UP</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($spp->tgl_terima_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td style="height: 20px"></td>
            </tr>
            <tr>
                <td style="padding-top: 5px" colspan="3" class="judul1">B. Kelengkapan dan Persyaratan SPM-UP
                </td>
            </tr>
        @elseif ($beban == '2')
            <tr>
                <td>SKPD/BIRO/UPT</td>
                <td>:</td>
                <td>{{ $nm_skpd }}</td>
            </tr>
            <tr>
                <td colspan="3" class="judul1">A. PENERIMAAN SPP-GU</td>
            </tr>
            <tr>
                <td class="judul2">1. Nomor dan Tanggal SPP-GU</td>
                <td>:</td>
                <td>{{ $spp->no_spp }} dan
                    {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td class="judul2">2. Tanggal Terima SPP-GU</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($spp->tgl_terima_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td style="height: 20px"></td>
            </tr>
            <tr>
                <td style="padding-top: 5px" colspan="3" class="judul1">B. Kelengkapan dan Persyaratan SPM-GU
                </td>
            </tr>
        @elseif ($beban == '3')
            <tr>
                <td>SKPD/BIRO/UPT</td>
                <td>:</td>
                <td>{{ $nm_skpd }}</td>
            </tr>
            <tr>
                <td colspan="3" class="judul1">A. PENERIMAAN SPP-TU</td>
            </tr>
            <tr>
                <td class="judul2">1. Nomor dan Tanggal SPP-TU</td>
                <td>:</td>
                <td>{{ $spp->no_spp }} dan
                    {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td class="judul2">2. Tanggal Terima SPP-TU</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($spp->tgl_terima_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td style="height: 20px"></td>
            </tr>
            <tr>
                <td style="padding-top: 5px" colspan="3" class="judul1">B. Kelengkapan dan Persyaratan SPM-TU
                </td>
            </tr>
        @elseif ($beban == '4')
            <tr>
                <td>SKPD/BIRO/UPT</td>
                <td>:</td>
                <td>{{ $nm_skpd }}</td>
            </tr>
            @if ($jenis == '1' || $jenis == '2')
                <tr>
                    <td colspan="3" class="judul1">A. PENERIMAAN SPP-Gaji</td>
                </tr>
                <tr>
                    <td class="judul2">1. Nomor dan Tanggal SPP-Gaji</td>
                    <td>:</td>
                    <td>{{ $spp->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="judul2">2. Tanggal Terima SPP-Gaji</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($spp->tgl_terima_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="padding-top: 5px" colspan="3" class="judul1">B. Kelengkapan dan Persyaratan
                        SPM-LS
                        @if ($jenis == '1')
                            Gaji
                        @else
                        @endif
                    </td>
                </tr>
            @elseif (in_array($jenis, ['3', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']))
                <tr>
                    <td colspan="3" class="judul1">A. PENERIMAAN
                        @if ($jenis == '14')
                            SPM-LS
                        @else
                            SPP-LS
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="judul2">1. Nomor dan Tanggal
                        @if ($jenis == '14')
                            SPM-LS
                        @else
                            SPP-LS
                        @endif
                    </td>
                    <td>:</td>
                    <td>{{ $spp->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="judul2">2. Tanggal Terima
                        @if ($jenis == '14')
                            SPM-LS
                        @else
                            SPP-LS
                        @endif
                    </td>
                    <td>:</td>
                    <td>........................................................................</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="padding-top: 5px" colspan="3" class="judul1">B. Kelengkapan dan Persyaratan
                        SPM-LS
                        @if ($jenis == '3')
                            untuk Tambahan Penghasilan
                        @elseif ($jenis == '5')
                            untuk Honorarium Tenaga Kontrak
                        @elseif ($jenis == '6')
                            untuk Pengadaan Barang dan Jasa
                        @elseif ($jenis == '7')
                            untuk Pengadaan Konsumsi
                        @elseif ($jenis == '8')
                            Sewa
                        @elseif ($jenis == '9' || $jenis == '10')
                            untuk Pengadaan Sertifikat Tanah
                        @elseif ($jenis == '11')
                            untuk Bantuan Hibah Barang dan Jasa pada Pihak Ketiga
                        @elseif ($jenis == '12')
                            untuk Bantuan Sosial pada Pihak Ketiga
                        @elseif ($jenis == '13')
                            untuk Bantuan Hibah Uang pada Pihak Ketiga
                        @elseif ($jenis == '14')
                            untuk Bantuan Keuangan Kepada Kabupaten/Kota
                        @elseif ($jenis == '15')
                            untuk Bagi Hasil Pajak dan Bukan Pajak
                        @elseif ($jenis == '16')
                            untuk Bantuan Hibah Barang dan Jasa pada Pihak Ketiga
                        @else
                        @endif
                    </td>
                </tr>
            @else
            @endif
        @elseif ($beban == '5')
            <tr>
                <td>SKPD/BIRO/UPT</td>
                <td>:</td>
                <td>{{ $nm_skpd }}</td>
            </tr>
            @if ($jenis == '1' || $jenis == '2')
                <tr>
                    <td colspan="3" class="judul1">A. PENERIMAAN SPP-Gaji</td>
                </tr>
                <tr>
                    <td class="judul2">1. Nomor dan Tanggal SPP-Gaji</td>
                    <td>:</td>
                    <td>{{ $spp->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="judul2">2. Tanggal Terima SPP-Gaji</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($spp->tgl_terima_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="padding-top: 5px" colspan="3" class="judul1">B. Kelengkapan dan Persyaratan
                        SPM-LS
                        @if ($jenis == '1')
                            Gaji
                        @else
                        @endif
                    </td>
                </tr>
            @elseif (in_array($jenis, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98', '99']))
                <tr>
                    <td colspan="3" class="judul1">A. PENERIMAAN
                        @if ($jenis == '14')
                            SPM-LS
                        @else
                            SPP-LS
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="judul2">1. Nomor dan Tanggal
                        @if ($jenis == '14')
                            SPM-LS
                        @else
                            SPP-LS
                        @endif
                    </td>
                    <td>:</td>
                    <td>{{ $spp->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="judul2">2. Tanggal Terima
                        @if ($jenis == '14')
                            SPM-LS
                        @else
                            SPP-LS
                        @endif
                    </td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($spp->tgl_terima_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="padding-top: 5px" colspan="3" class="judul1">B. Kelengkapan dan Persyaratan
                        @if ($jenis == '3')
                            SPM-LS untuk Tambahan Penghasilan
                        @elseif ($jenis == '4')
                            SPM-LS untuk Honorarium PNS
                        @elseif ($jenis == '5')
                            SPM-LS untuk Honorarium Tenaga Kontrak
                        @elseif ($jenis == '6')
                            SPM-LS untuk Pengadaan Barang dan Jasa
                        @elseif ($jenis == '7')
                            SPM-LS untuk Pengadaan Konsumsi
                        @elseif ($jenis == '8')
                            SPM-LS Sewa
                        @elseif ($jenis == '9' || $jenis == '10')
                            SPM-LS untuk Pengadaan Sertifikat Tanah
                        @elseif ($jenis == '11')
                            SPM-LS untuk Bantuan Hibah Barang dan Jasa pada Pihak Ketiga
                        @elseif ($jenis == '12')
                            SPM-LS untuk Bantuan Sosial pada Pihak Ketiga
                        @elseif ($jenis == '13')
                            SPM-LS untuk Bantuan Hibah Uang pada Pihak Ketiga
                        @elseif ($jenis == '14')
                            SPM-LS untuk Bantuan Keuangan Kepada Kabupaten/Kota
                        @elseif ($jenis == '15')
                            SPM-LS untuk Bagi Hasil Pajak dan Bukan Pajak
                        @elseif ($jenis == '16')
                            SPM-LS untuk Bantuan Hibah Barang dan Jasa pada Pihak Ketiga
                        @elseif ($jenis == '98')
                            SPM-LS untuk Pengadaan Barang dan Jasa.
                        @elseif ($jenis == '99')
                            SPP-LS untuk Pengeluaran Pembiayaan
                        @else
                        @endif
                    </td>
                </tr>
            @endif
        @elseif ($beban == '6')
            <tr>
                <td>SKPD/BIRO/UPT</td>
                <td>:</td>
                <td>{{ $nm_skpd }}</td>
            </tr>
            @if ($jenis == '1' || $jenis == '2')
                <tr>
                    <td colspan="3" class="judul1">A. PENERIMAAN SPP-Gaji</td>
                </tr>
                <tr>
                    <td class="judul2">1. Nomor dan Tanggal SPP-Gaji</td>
                    <td>:</td>
                    <td>{{ $spp->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="judul2">2. Tanggal Terima SPP-Gaji</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($spp->tgl_terima_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="padding-top: 5px" colspan="3" class="judul1">B. Kelengkapan dan Persyaratan
                        SPM-LS
                        @if ($jenis == '1')
                            Gaji
                        @else
                        @endif
                    </td>
                </tr>
            @elseif (in_array($jenis, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98', '99']))
                <tr>
                    <td colspan="3" class="judul1">A. PENERIMAAN
                        @if ($jenis == '14')
                            SPM-LS
                        @else
                            SPP-LS
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="judul2">1. Nomor dan Tanggal
                        @if ($jenis == '14')
                            SPM-LS
                        @else
                            SPP-LS
                        @endif
                    </td>
                    <td>:</td>
                    <td>{{ $spp->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="judul2">2. Tanggal Terima
                        @if ($jenis == '14')
                            SPM-LS
                        @else
                            SPP-LS
                        @endif
                    </td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($spp->tgl_terima_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="padding-top: 5px" colspan="3" class="judul1">B. Kelengkapan dan Persyaratan
                        @if ($jenis == '3')
                            SPM-LS untuk Tambahan Penghasilan
                        @elseif ($jenis == '4')
                            SPM-LS untuk Honorarium PNS
                        @elseif ($jenis == '5')
                            SPM-LS untuk Honorarium Tenaga Kontrak
                        @elseif ($jenis == '6')
                            SPM-LS untuk Pengadaan Barang dan Jasa
                        @elseif ($jenis == '7')
                            SPM-LS untuk Pengadaan Konsumsi
                        @elseif ($jenis == '8')
                            SPM-LS Sewa
                        @elseif ($jenis == '9' || $jenis == '10')
                            SPM-LS untuk Pengadaan Sertifikat Tanah
                        @elseif ($jenis == '11')
                            SPM-LS untuk Bantuan Hibah Barang dan Jasa pada Pihak Ketiga
                        @elseif ($jenis == '12')
                            SPM-LS untuk Bantuan Sosial pada Pihak Ketiga
                        @elseif ($jenis == '13')
                            SPM-LS untuk Bantuan Hibah Uang pada Pihak Ketiga
                        @elseif ($jenis == '14')
                            SPM-LS untuk Bantuan Keuangan Kepada Kabupaten/Kota
                        @elseif ($jenis == '15')
                            SPM-LS untuk Bagi Hasil Pajak dan Bukan Pajak
                        @elseif ($jenis == '16')
                            SPM-LS untuk Bantuan Hibah Barang dan Jasa pada Pihak Ketiga
                        @elseif ($jenis == '98')
                            SPM-LS
                        @elseif ($jenis == '99')
                            SPM-LS Pembiayaan pada Pihak Ketiga Lainnya
                        @else
                        @endif
                    </td>
                </tr>
            @endif
        @endif
    </table>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <thead>
            <tr>
                <th></th>
                <th colspan="2"></th>
                <th>Ada</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kelengkapan_spm as $row)
                <tr>
                    @if ($row->level == 0)
                        <td style="vertical-align: top;width: 2px;padding-left:30px;">{{ $row->list_id }}</td>
                        <td colspan="2" style="vertical-align: top;">{{ $row->uraian }}</td>
                    @else
                        <td>&nbsp;</td>
                        <td style="vertical-align: top;width: 2px;padding-left:5px;">{{ $row->list_id }}</td>
                        <td style="vertical-align: top;">{{ $row->uraian }}</td>
                    @endif

                    @if ($row->checked == 1)
                        <td class="row2" style="text-align:center;">&#10004;</td>
                    @else
                        <td class="row2" style="text-align:center;"></td>
                    @endif
                    </td>
                    @if ($row->checked == 0)
                        <td class="row3" style="text-align:center;">&#10004;</td>
                    @else
                        <td class="row2" style="text-align:center;"></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <br>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        @if (in_array($beban, ['1', '2', '3', '4']))
            <tr>
                <td style="width: 300px">Tanggal Pengembalian SPP</td>
                <td>: {{ is_null($spp->tgl_kembali_kelengkapan_spm)? '': \Carbon\Carbon::parse($spp->tgl_kembali_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td style="height: 20px"></td>
            </tr>
            <tr>
                <td style="width: 300px">Tanggal Terima Kembali SPP</td>
                <td>:
                    {{ is_null($spp->tgl_terima_kembali_kelengkapan_spm)? '': \Carbon\Carbon::parse($spp->tgl_terima_kembali_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}
                </td>
            </tr>
        @elseif ($beban == '5')
            @if (in_array($jenis, ['11', '98']))
                <tr>
                    <td style="width: 300px">Tanggal Pengembalian SPP</td>
                    <td>: {{ is_null($spp->tgl_kembali_kelengkapan_spm)? '': \Carbon\Carbon::parse($spp->tgl_kembali_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="width: 300px">Tanggal Terima Kembali SPP</td>
                    <td>: {{ is_null($spp->tgl_terima_kembali)? '': \Carbon\Carbon::parse($spp->tgl_terima_kembali)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
            @else
            @endif
        @elseif ($beban == '6')
            @if (in_array($jenis, ['11', '99']))
                <tr>
                    <td style="width: 300px">Tanggal Pengembalian SPP</td>
                    <td>: {{ is_null($spp->tgl_kembali_kelengkapan_spm)? '': \Carbon\Carbon::parse($spp->tgl_kembali_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="width: 300px">Tanggal Terima Kembali SPP</td>
                    <td>:
                        {{ is_null($spp->tgl_terima_kembali_kelengkapan_spm)? '': \Carbon\Carbon::parse($spp->tgl_terima_kembali_kelengkapan_spm)->locale('id')->isoFormat('DD MMMM Y') }}
                    </td>
                </tr>
            @else
            @endif
        @endif
    </table>
    <br>
    <br>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <tbody>
            <tr>
                <td style="width: 50%"></td>
                <td style="text-align: center">
                    Dikerjakan oleh
                </td>
            </tr>
            <tr>
                <td style="width: 50%"></td>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $pptk->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%"></td>
                <td style="text-align: center">
                    <b><u>{{ $pptk->nama }}</u></b>
                    <br>
                    {{ $pptk->pangkat }}
                    <br>
                    NIP. {{ $pptk->nip }}
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>
