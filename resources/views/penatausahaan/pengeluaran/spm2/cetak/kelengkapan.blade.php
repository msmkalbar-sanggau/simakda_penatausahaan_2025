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
                        (Untuk Honorarium PNS)
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
                <td>{{ $skpd->nm_skpd }}</td>
            </tr>
            <tr>
                <td colspan="3" class="judul1">A. PENERIMAAN SPP-UP</td>
            </tr>
            <tr>
                <td class="judul2">1. Nomor dan Tanggal SPP-UP</td>
                <td>:</td>
                <td>{{ $spm->no_spp }} dan
                    {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td class="judul2">2. Tanggal Terima SPP-UP</td>
                <td>:</td>
                <td>........................................................................</td>
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
                <td>{{ $skpd->nm_skpd }}</td>
            </tr>
            <tr>
                <td colspan="3" class="judul1">A. PENERIMAAN SPP-GU</td>
            </tr>
            <tr>
                <td class="judul2">1. Nomor dan Tanggal SPP-GU</td>
                <td>:</td>
                <td>{{ $spm->no_spp }} dan
                    {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td class="judul2">2. Tanggal Terima SPP-GU</td>
                <td>:</td>
                <td>........................................................................</td>
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
                <td>{{ $skpd->nm_skpd }}</td>
            </tr>
            <tr>
                <td colspan="3" class="judul1">A. PENERIMAAN SPP-TU</td>
            </tr>
            <tr>
                <td class="judul2">1. Nomor dan Tanggal SPP-TU</td>
                <td>:</td>
                <td>{{ $spm->no_spp }} dan
                    {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
            </tr>
            <tr>
                <td class="judul2">2. Tanggal Terima SPP-TU</td>
                <td>:</td>
                <td>........................................................................</td>
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
                <td>{{ $skpd->nm_skpd }}</td>
            </tr>
            @if ($jenis == '1' || $jenis == '2')
                <tr>
                    <td colspan="3" class="judul1">A. PENERIMAAN SPP-Gaji</td>
                </tr>
                <tr>
                    <td class="judul2">1. Nomor dan Tanggal SPP-Gaji</td>
                    <td>:</td>
                    <td>{{ $spm->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="judul2">2. Tanggal Terima SPP-Gaji</td>
                    <td>:</td>
                    <td>........................................................................</td>
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
                    <td>{{ $spm->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
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
                <td>{{ $skpd->nm_skpd }}</td>
            </tr>
            @if ($jenis == '1' || $jenis == '2')
                <tr>
                    <td colspan="3" class="judul1">A. PENERIMAAN SPP-Gaji</td>
                </tr>
                <tr>
                    <td class="judul2">1. Nomor dan Tanggal SPP-Gaji</td>
                    <td>:</td>
                    <td>{{ $spm->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="judul2">2. Tanggal Terima SPP-Gaji</td>
                    <td>:</td>
                    <td>........................................................................</td>
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
                    <td>{{ $spm->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
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
                <td>{{ $skpd->nm_skpd }}</td>
            </tr>
            @if ($jenis == '1' || $jenis == '2')
                <tr>
                    <td colspan="3" class="judul1">A. PENERIMAAN SPP-Gaji</td>
                </tr>
                <tr>
                    <td class="judul2">1. Nomor dan Tanggal SPP-Gaji</td>
                    <td>:</td>
                    <td>{{ $spm->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="judul2">2. Tanggal Terima SPP-Gaji</td>
                    <td>:</td>
                    <td>........................................................................</td>
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
                    <td>{{ $spm->no_spp }} dan
                        {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
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
                            SPM-LS untuk Honorarium PNS
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
                <th>Ada</th>
                <th>Tidak</th>
            </tr>
        </thead>
        <tbody>
            @if ($beban == '1')
                <tr>
                    <td class="row1">a. Pengantar SPP-UP</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">b. SPP-UP</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">c. Ringkasan SPP-UP</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">d. Rincian SPP-UP</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">e. Surat Pernyataan Pengajuan SPP-UP</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">f. Lampiran SPP-UP</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">1. Salinan SPD</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">2. Fotocopy Rekening Koran Per 31 Desember</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">3. Fotocopy Keputusan Gubenur tentang Uang Persediaan</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
            @elseif ($beban == '2')
                <tr>
                    <td class="row1">a. Pengantar SPP-GU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">b. SPP-GU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">c. Ringkasan SPP-GU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">d. Rincian SPP-GU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">e. Surat Pernyataan Pengajuan SPP-GU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">f. Lampiran SPP-GU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">1. Salinan SPD</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">2. Laporan Pertanggungjawaban(LPJ-UP)</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">3. Surat Pernyataan Tanggung Jawab Belanja(SPTB)</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">4. Fotocopy Surat Setoran Elektronik (SSE) PPn dan PPh (Resi)</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
            @elseif ($beban == '3')
                <tr>
                    <td class="row1">a. Pengantar SPP-TU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">b. SPP-TU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">c. Ringkasan SPP-TU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">d. Rincian Rencana Penggunaan TU (Persetujuan BUD)</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">e. Surat Pernyataan Pengajuan SPP-TU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row1">f. Lampiran SPP-TU</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">1. Salinan SPD</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">2. Jadwal Pelaksanaan Kegiatan</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">3. Poto Copy Rekening Koran bulan berkenaan</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">4. Laporan Pertanggungjawaban (LPJ-TU) untuk pengajuan SPP-TU berikutnya
                    </td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">5. Surat Pernyataan Tanggung Jawab Belanja (SPTB)</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">6. Surat Setoran Elektronik (SSE) PPn dan PPh beserta resi (bukti setor)
                    </td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">7. Bukti Setor uang yang tidak habis dipertanggungjawabkan dalam waktu 1
                        (satu) bulan</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
                <tr>
                    <td class="row4">8. Dokumen lain yang dipersyaratkan</td>
                    <td class="row2"></td>
                    <td class="row3"></td>
                </tr>
            @elseif ($beban == '4')
                @if (in_array($jenis, ['1', '2', '3', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']))
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">a. Pengantar SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']))
                            <td class="row1">a. Pengantar SPP-LS</td>
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">b. SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']))
                            <td class="row1">b. SPP-LS</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">c. Ringkasan SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']))
                            <td class="row1">c. Ringkasan SPP-LS</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">d. Rincian SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '5', '6', '7', '10', '11', '12', '13', '14', '15', '16']))
                            <td class="row1">d. Rincian SPP-LS</td>
                        @elseif (in_array($jenis, ['8', '9']))
                            <td class="row1">d. Rincian</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">e. Surat Pernyataan Pengajuan SPP-Gaji</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif ($jenis == '3')
                            <td class="row1">e. Surat Pernyataan Pengajuan SPP-Tambahan Penghasilan</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['6', '7']))
                            <td class="row1">e. Surat Pernyataan Pengajuan SPP-LS</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['8', '9', '10', '13', '14', '15']))
                            <td class="row1">e. Surat Pernyataan SPP-LS</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['11', '12', '16']))
                            <td class="row1">e. Surat Pernyataan SPP-LSk</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @else
                        @endif

                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">f. Lampiran SPP-Gaji</td>
                        @elseif ($jenis == '3')
                            <td class="row1">f. Lampiran SPP-Tambahan Penghasilan</td>
                        @elseif ($jenis == '5')
                            <td class="row1">e. Lampiran SPP-Honorarium Tenaga Kontrak</td>
                        @elseif (in_array($jenis, ['6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']))
                            <td class="row1">f. Lampiran SPP-LS</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                @else
                @endif
                <tr>
                    @if (in_array($jenis, ['1', '2', '3', '5', '6', '7', '8', '9']))
                        <td class="row4">1. Salinan SPD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">1. Persetujuan Panitia Pengadaan Tanah untuk tanah yang luasnya lebih
                            dari 1(satu) hektar</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">1. Photo Copy SPD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['12', '13']))
                        <td class="row4">1. Proposal Bantuan Sosial dari Pihak Ketiga</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['14']))
                        <td class="row4">1. Keputusan Gubernur tentang Penetapan Bantuan Keuangan kepada
                            Kabupaten/Kota</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['15']))
                        <td class="row4">1. Keputusan Gubernur tentang Rencana Bagi Hasil Pajak
                            Provinsi/Rencana Bagi Hasil Pajak Rokok provinsi kalimantan Barat Kepada Kabupaten/Kota
                            Sekalimantan Barat Tahun Anggaran {{ tahun_anggaran() }}</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2']))
                        <td class="row4">2. Daftar Gaji</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">2. Daftar penerima Tambahan Penghasilan(Tanda tangan Penerima, Pembuat
                            Daftar, Setuju dibayar PA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">2. SK. Pengangkatan sebagai Pegawai Non PNS/Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">2. Nota Pencairan Dana yang ditandatangani oleh PPTK</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['8', '9']))
                        <td class="row4">2. Dokumen Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">2. Foto copy bukti kepemilikan tanah/sertifikat hak atas tanah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">2. Proposal Hibah dari Pihak Ketiga</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '12')
                        <td class="row4">2. Keputusan Gubernur tentang Penetapan Bantuan Sosial</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '13')
                        <td class="row4">2. Naskah Perjanjian Hibah Daerah (NPHD)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '14')
                        <td class="row4">2. Surat keterangan rekening Kas Umum Daerah Kabupaten/Kota</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '15')
                        <td class="row4">2. Foto copy rekening Kas Umum Daerah Kabupaten/Kota(diutamakan Bank
                            Pemerintah)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2']))
                        <td class="row4">3. Rekapitulasi gaji Induk</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">3. Daftar Hadir/Absensi Bulanan mengetahui Kepala SKPD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">3. Surat Perjanjian Kontrak pada saat pengajuan di awal tahun</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">3. Dokumen Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">3. Nota Pencairan Dana yang ditandatangani oleh PPTK</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['9', '10']))
                        <td class="row4">3. Kuitansi Asli bermaterai</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">3. SK Penerima Hibah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['12', '13']))
                        <td class="row4">3. Kuitansi Asli Bermaterai (tanda tangan yang menerima dana, mengetahui
                            PPTK dan setuju dibayar oleh PA/KPA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['14', '15']))
                        <td class="row4">3. Syarat-syarat lainnya sesuai ketentuan yang berlaku</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif

                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2']))
                        <td class="row4">4. Rekapitulasi Gaji golongan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">4. Rekap daftar hadir harian Pegawai (Tanda tangan pembuat daftar dan
                            mengetahui Kepala SKPD)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">4. Daftar tanda terima Pembayaran Honor yang sudah di tanda tangan
                            (Tanda tangan Pembuat Daftar, Mengetahui PPTK dan Setuju dibayar Pengguna Anggaran)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">4. Kwitansi Asli bermaterai</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">4. Kwitansi Asli bermaterai(tanda tangan yang menerima penyedia barang,
                            mengetahui PPTK, setuju dibayar oleh PA/KPA dan Lunas Bayar oleh Bendahara)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">4. Referensi Bank Asli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">4. SPPT PBB tahun transaksi</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">4. Naskah Perjanjian Hibah Daerah (NPHD)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '12')
                        <td class="row4">4. Foto copy rekening bank atas nama penerima sesuai dengan yang
                            tercantum dalan Surat Keputusan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '13')
                        <td class="row4">4. Foto copy rekening bank atas nama penerima bantuan hibah sesuai
                            dengan yang tercantum dalam Surat Keputusan atau Naskah Perjanjian Hibah Daerah)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1' || $jenis == '2')
                        <td class="row4">5. Surat Setoran Elektronik (SSE) PPh. 21</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">5. Kuitansi Asli (Tanda tangan Penerima Bendahara Pengeluaran dan Setuju
                            dibayar PA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">5. Kuitansi Asli (Tanda tangan Penerima Bendahara Pengeluaran,
                            Mengetahui PPTK dan Setuju dibayar PA/KPA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">5. Referensi Bank Asli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">5. Referensi Bank Asli dan Nama Penyedia barang harus sesuai dengan
                            Surat Pesanan/Surat Perintah Kerja/Surat Perjanjian</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">5. Berita Acara Pemeriksaan Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">5. Surat Persetujuan Harga antara pemilik dan pihak pembeli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">5. Dokumen Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '12')
                        <td class="row4">5. Surat Pernyataan Tanggung jawab Mutlak dari pihak penerima bantuan
                            sosial</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '13')
                        <td class="row4">5. Surat Pernyataan Tanggung jawab Mutlak dari pihak penerima Hibah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">6. SK. Perubahan status dari CPNS menjadi PNS</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">6. SK Mutasi Pindah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">6. Surat Setoran Elektronik(SSE) PPh 21</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">6. Surat Setoran Elektronik (SSE) PPh 21 melebihi PTKP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">6. Fotocopy NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">6. Berita Acara Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">6. Berita Acara Penerimaan Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">6. Surat Pernyataan dari penjual bahwa tanah/akta jual beli ddi hadapan
                            PPAT</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">6. NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['12', '13']))
                        <td class="row4">6. Syarat-syarat lainnya sesuai ketentuan yang berlaku</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">7. SK. Kenaikan Pangkat/Penurunan Pangkat</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">7. Surat Keterangan Pemberhentian Pembayaran (SKPP)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">7. Surat Tanda Setoran(STS) jika ada pemotongan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">7. Surat Setoran Elektronik PNBP 1% dan 4%</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '6')
                        <td class="row4">7. Jaminan Uang Muka(Asli)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">7. Berita Acara Pemeriksaan Barang dibuat setiap hari pelaksanaan makan
                            minum</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">7. Fotocopy NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">7. Berita Acara Serah Terima Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">7. Surat Pelepasan adat(bila diperlukan)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">7. Rekening Bank</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">8. SK. Pengangkatan dalam jabatan Struktural/Fungsional</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">8. Surat Keputusan Pengangkatan sebagai CPNS</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '6')
                        <td class="row4">8. Jaminan Pemeliharaan (Asli)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">8. Berita Acara Penerimaan Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">8. e-Faktur pajak dan Surat Setoran Elektronik (SSE)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">8. BA. Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">8. e-Faktur pajak dan Surat Setoran Elektronik(SSE) PPh Final tas
                            pelepasan Hak = 5%</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">8. Jaminan Uang Muka</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">9. Keputusan Kenaikan Gaji Penyesuaian Masa Kerja.</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">9. Surat Pernyataan melaksanakan Tugas</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '6')
                        <td class="row4">9. Ringkasan Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">9. Berita Acara Serah Terima dibuat setiap hari pelaksanaan makan minum
                        </td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">9. Khusus untuk sewa rumah jabatan/gedung untuk kantor/gedung
                            pertemuan/tempat pertemuan/tempat penginapan/kendaraan, Berita Acara Pemeriksaan, Berita
                            Acara Serah Terima(dilakukan pada tanggal mulai sewa). Untuk sewa lebih dari 1 bulan
                            wajib melampirkan Surat Perjanjian Sewa</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">9. Berita Acara Penyelesaian Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">9. Foto copy Rekening Bank atas nama pemilik/penerima</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">9. Jaminan Pemeliharaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">10. Keputusan Pindah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">10. Surat Pernyataan Tanggungjawab mutlak (SPTJM)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '11', '16']))
                        <td class="row4">10. Laporan Kemajuan Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">10. Berita Acara Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">10. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">10. Fotocopy NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">10. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1' || $jenis == '2')
                        <td class="row4">11. Daftar Keluarga</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '6')
                        <td class="row4">11. Berita Acara Pemeriksaan Barang/Jasa/Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['7', '9']))
                        <td class="row4">11. e-Faktur pajak dan Surat Setoran Elektronik (SSE)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">11. BA-pemeriksaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">12. Surat Pernyataan melaksanakan tugas(Surat)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '6')
                        <td class="row4">12. Berita Acara Penerimaan Barang/Jasa/Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">12. Jadwal Pelaksanaan Kegiatan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">12. Khusus pengadaan sertifikasi tanah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">12. BA-serah terima</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                @if ($jenis == '9')
                    <tr>
                        <td class="row5">a. Untuk pembayaran angsuran tahap I maksimum 50% pada saat pendaftaran
                            ukur dengan dilampiri foto copy Bukti Pendaftaran Ukur</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        <td class="row5">b. Untuk pembayaran angsuran tahap I maksimum 50% pada saat pendaftaran
                            ukur dengan dilampiri foto copy Bukti Pendaftaran Ukur</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        <td class="row5">b. Untuk pembayaran sisa angsuran sebesar 10% dengan dilampiri</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        <td class="row6">- Asli Sertifikat Hak atas Tanah dari Pejabat Pertahanan yang berwenang
                        </td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                @endif
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">13. Tambahan/pengurangan keluarga karena Kawin, dilampiri foto copy
                            surat
                            nikah/akte perkawinan Tambah anak, dilampiri foto copy akte kelahiran Cerai, dilampiri
                            akte
                            cerai</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '6')
                        <td class="row4">13. Berita Acara Serah Terima berdasarkan Kemajuan pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">13. Absensi Peserta dan Panitia</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">13. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">13. BA-pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">14. Surat Keputusan Pengangkatan sebagai CPNS</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '6')
                        <td class="row4">14. Berita Acara Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">14. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">14. Dokumen lain yang dipersyaratkan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">15. Surat Pernyataan Tanggungjawab mutlak(SPTJM)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '6')
                        <td class="row4">15. Jaminan Pelaksanaan Pekerjaan Asli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '6')
                        <td class="row4">16. Foto Fisik Pekerjaan(Masing-masing progress/kemajuan fisik)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '6')
                        <td class="row4">17. e-Faktur pajak dan Surat Setoran Elektronik (SSE)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '6')
                        <td class="row4">18. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
            @elseif ($beban == '5')
                @if (in_array($jenis, $beban5))
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">a. Pengantar SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98', '99']))
                            <td class="row1">a. Pengantar SPP-LS</td>
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">b. SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98', '99']))
                            <td class="row1">b. SPP-LS</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">c. Ringkasan SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98', '99']))
                            <td class="row1">c. Ringkasan SPP-LS</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">d. Rincian SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '4', '5', '6', '8', '10', '11', '12', '13', '14', '15', '16', '98', '99']))
                            <td class="row1">d. Rincian SPP-LS</td>
                        @elseif (in_array($jenis, ['7', '9']))
                            <td class="row1">d. Rincian</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">e. Surat Pernyataan SPP-Gaji</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['3', '4']))
                            <td class="row1">e. Surat Pernyataan Pengajuan SPP-Tambahan Penghasilan</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['6', '7', '98']))
                            <td class="row1">e. Surat Pernyataan Pengajuan SPP-LS</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['8', '9', '10', '13', '14', '15', '99']))
                            <td class="row1">e. Surat Pernyataan SPP-LS</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['11', '12', '16']))
                            <td class="row1">e. Surat Pernyataan SPP-LSk</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @endif
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">f. Lampiran SPP-Gaji</td>
                        @elseif ($jenis == '3')
                            <td class="row1">f. Lampiran SPP-Tambahan Penghasilan</td>
                        @elseif ($jenis == '4')
                            <td class="row1">f. Lampiran SPP-Honorarium PNS</td>
                        @elseif ($jenis == '5')
                            <td class="row1">e. Lampiran SPP-Honorarium Tenaga Kontrak</td>
                        @elseif (in_array($jenis, ['6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98', '99']))
                            <td class="row1">f. Lampiran SPP-LS</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                @else
                @endif
                <tr>
                    @if (in_array($jenis, ['1', '2', '3', '4', '5', '6', '7', '8', '9', '98']))
                        <td class="row4">1. Salinan SPD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">1. Persetujuan Panitia Pengadaan Tanah untuk tanah yang luasnya lebih
                            dari 1(satu) hektar</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">1. Photo Copy SPD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['12', '13']))
                        <td class="row4">1. Proposal Bantuan Sosial dari Pihak Ketiga</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['14']))
                        <td class="row4">1. Keputusan Gubernur tentang Penetapan Bantuan Keuangan kepada
                            Kabupaten/Kota</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['15']))
                        <td class="row4">1. Keputusan Gubernur tentang Rencana Bagi Hasil Pajak
                            Provinsi/Rencana Bagi Hasil Pajak Rokok provinsi kalimantan Barat Kepada Kabupaten/Kota
                            Sekalimantan Barat Tahun Anggaran {{ tahun_anggaran() }}</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '99')
                        <td class="row4">1. Kuitansi Asli Bermaterai (tanda tangan yang menerima dana, mengetahui
                            PPTK dan setuju dibayar oleh PA/KPA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2']))
                        <td class="row4">2. Daftar Gaji</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">2. Daftar penerima Tambahan Penghasilan(Tanda tangan Penerima, Pembuat
                            Daftar, Setuju dibayar PA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '4')
                        <td class="row4">2. SK. Pembentukan Tim/Panitia Pelaksanaan Kegiatan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">2. SK. Pengangkatan sebagai Pegawai Non PNS/Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7', '98']))
                        <td class="row4">2. Nota Pencairan Dana yang ditandatangani oleh PPTK</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['8', '9']))
                        <td class="row4">2. Dokumen Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">2. Foto copy bukti kepemilikan tanah/sertifikat hak atas tanah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">2. Proposal Hibah dari Pihak Ketiga</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '12')
                        <td class="row4">2. Keputusan Gubernur tentang Penetapan Bantuan Sosial</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '13')
                        <td class="row4">2. Naskah Perjanjian Hibah Daerah (NPHD)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '14')
                        <td class="row4">2. Surat keterangan rekening Kas Umum Daerah Kabupaten/Kota</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '15')
                        <td class="row4">2. Foto copy rekening Kas Umum Daerah Kabupaten/Kota(diutamakan Bank
                            Pemerintah)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '99')
                        <td class="row4">2. Surat Pernyataan Tanggung jawab Mutlak dari pihak penerima Hibah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2']))
                        <td class="row4">3. Rekapitulasi gaji Induk</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">3. Daftar Hadir/Absensi Bulanan mengetahui Kepala SKPD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '4')
                        <td class="row4">3. Daftar tanda terima Pembayaran Honor yang sudah di tanda tangan
                            (Tanda tangan Pembuat Daftar, Mengetahui PPTK dan Setuju dibayar Pengguna Anggaran)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">3. Surat Perjanjian Kontrak pada saat pengajuan di awal tahun</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7', '98']))
                        <td class="row4">3. Dokumen Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">3. Nota Pencairan Dana yang ditandatangani oleh PPTK</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['9', '10']))
                        <td class="row4">3. Kuitansi Asli bermaterai</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">3. SK Penerima Hibah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['12', '13']))
                        <td class="row4">3. Kuitansi Asli Bermaterai (tanda tangan yang menerima dana, mengetahui
                            PPTK dan setuju dibayar oleh PA/KPA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['14', '15', '99']))
                        <td class="row4">3. Syarat-syarat lainnya sesuai ketentuan yang berlaku</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2']))
                        <td class="row4">4. Rekapitulasi Gaji golongan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">4. Rekap daftar hadir harian Pegawai (Tanda tangan pembuat daftar dan
                            mengetahui Kepala SKPD)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '4')
                        <td class="row4">4. Kuitansi Asli(Tanda tangan Penerima Bendahara Pengeluaran, Mengetahui
                            PPTK dan Setuju dibayar PA/KPA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">4. Daftar tanda terima Pembayaran Honor yang sudah di tanda tangan
                            (Tanda tangan Pembuat Daftar, Mengetahui PPTK dan Setuju dibayar Pengguna Anggaran)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7', '98']))
                        <td class="row4">4. Kwitansi Asli bermaterai</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">4. Kwitansi Asli bermaterai(tanda tangan yang menerima penyedia barang,
                            mengetahui PPTK, setuju dibayar oleh PA/KPA dan Lunas Bayar oleh Bendahara)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">4. Referensi Bank Asli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">4. SPPT PBB tahun transaksi</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">4. Naskah Perjanjian Hibah Daerah (NPHD)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '12')
                        <td class="row4">4. Foto copy rekening bank atas nama penerima sesuai dengan yang
                            tercantum dalan Surat Keputusan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '13')
                        <td class="row4">4. Foto copy rekening bank atas nama penerima bantuan hibah sesuai
                            dengan yang tercantum dalam Surat Keputusan atau Naskah Perjanjian Hibah Daerah)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2', '4']))
                        <td class="row4">5. Surat Setoran Elektronik (SSE) PPh. 21</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">5. Kuitansi Asli (Tanda tangan Penerima Bendahara Pengeluaran dan Setuju
                            dibayar PA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">5. Kuitansi Asli (Tanda tangan Penerima Bendahara Pengeluaran,
                            Mengetahui PPTK dan Setuju dibayar PA/KPA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7', '98']))
                        <td class="row4">5. Referensi Bank Asli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">5. Referensi Bank Asli dan Nama Penyedia barang harus sesuai dengan
                            Surat Pesanan/Surat Perintah Kerja/Surat Perjanjian</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">5. Berita Acara Pemeriksaan Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">5. Surat Persetujuan Harga antara pemilik dan pihak pembeli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">5. Dokumen Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '12')
                        <td class="row4">5. Surat Pernyataan Tanggung jawab Mutlak dari pihak penerima bantuan
                            sosial</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '13')
                        <td class="row4">5. Surat Pernyataan Tanggung jawab Mutlak dari pihak penerima Hibah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">6. SK. Perubahan status dari CPNS menjadi PNS</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">6. SK Mutasi Pindah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">6. Surat Setoran Elektronik(SSE) PPh 21</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">6. Surat Setoran Elektronik (SSE) PPh 21 melebihi PTKP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7', '98']))
                        <td class="row4">6. Fotocopy NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">6. Berita Acara Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">6. Berita Acara Penerimaan Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">6. Surat Pernyataan dari penjual bahwa tanah/akta jual beli ddi hadapan
                            PPAT</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">6. NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['12', '13']))
                        <td class="row4">6. Syarat-syarat lainnya sesuai ketentuan yang berlaku</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">7. SK. Kenaikan Pangkat/Penurunan Pangkat</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">7. Surat Keterangan Pemberhentian Pembayaran (SKPP)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">7. Surat Tanda Setoran(STS) jika ada pemotongan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">7. Surat Setoran Elektronik PNBP 1% dan 4%</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '98']))
                        <td class="row4">7. Jaminan Uang Muka(Asli)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">7. Berita Acara Pemeriksaan Barang dibuat setiap hari pelaksanaan makan
                            minum</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">7. Fotocopy NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">7. Berita Acara Serah Terima Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">7. Surat Pelepasan adat(bila diperlukan)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">7. Rekening Bank</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">8. SK. Pengangkatan dalam jabatan Struktural/Fungsional</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">8. Surat Keputusan Pengangkatan sebagai CPNS</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '98']))
                        <td class="row4">8. Jaminan Pemeliharaan (Asli)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">8. Berita Acara Penerimaan Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">8. e-Faktur pajak dan Surat Setoran Elektronik (SSE)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">8. BA. Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">8. e-Faktur pajak dan Surat Setoran Elektronik(SSE) PPh Final tas
                            pelepasan Hak = 5%</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">8. Jaminan Uang Muka</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">9. Keputusan Kenaikan Gaji Penyesuaian Masa Kerja.</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">9. Surat Pernyataan melaksanakan Tugas</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '98']))
                        <td class="row4">9. Ringkasan Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">9. Berita Acara Serah Terima dibuat setiap hari pelaksanaan makan minum
                        </td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">9. Khusus untuk sewa rumah jabatan/gedung untuk kantor/gedung
                            pertemuan/tempat pertemuan/tempat penginapan/kendaraan, Berita Acara Pemeriksaan, Berita
                            Acara Serah Terima(dilakukan pada tanggal mulai sewa). Untuk sewa lebih dari 1 bulan
                            wajib melampirkan Surat Perjanjian Sewa</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">9. Berita Acara Penyelesaian Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">9. Foto copy Rekening Bank atas nama pemilik/penerima</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">9. Jaminan Pemeliharaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">10. Keputusan Pindah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">10. Surat Pernyataan Tanggungjawab mutlak (SPTJM)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '11', '16', '98']))
                        <td class="row4">10. Laporan Kemajuan Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">10. Berita Acara Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">10. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">10. Fotocopy NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">10. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1' || $jenis == '2')
                        <td class="row4">11. Daftar Keluarga</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '98']))
                        <td class="row4">11. Berita Acara Pemeriksaan Barang/Jasa/Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['7', '9']))
                        <td class="row4">11. e-Faktur pajak dan Surat Setoran Elektronik (SSE)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">11. BA-pemeriksaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">12. Surat Pernyataan melaksanakan tugas(Surat)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '98']))
                        <td class="row4">12. Berita Acara Penerimaan Barang/Jasa/Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">12. Jadwal Pelaksanaan Kegiatan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">12. Khusus pengadaan sertifikasi tanah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">12. BA-serah terima</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                @if ($jenis == '9')
                    <tr>
                        <td class="row5">a. Untuk pembayaran angsuran tahap I maksimum 50% pada saat pendaftaran
                            ukur dengan dilampiri foto copy Bukti Pendaftaran Ukur</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        <td class="row5">b. Untuk pembayaran angsuran tahap I maksimum 50% pada saat pendaftaran
                            ukur dengan dilampiri foto copy Bukti Pendaftaran Ukur</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        <td class="row5">b. Untuk pembayaran sisa angsuran sebesar 10% dengan dilampiri</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        <td class="row6">- Asli Sertifikat Hak atas Tanah dari Pejabat Pertahanan yang berwenang
                        </td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                @endif
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">13. Tambahan/pengurangan keluarga karena Kawin, dilampiri foto copy
                            surat nikah/akte perkawinan Tambah anak, dilampiri foto copy akte kelahiran Cerai,
                            dilampiri akte
                            cerai</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '98']))
                        <td class="row4">13. Berita Acara Serah Terima berdasarkan Kemajuan pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">13. Absensi Peserta dan Panitia</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">13. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">13. BA-pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">14. Surat Keputusan Pengangkatan sebagai CPNS</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '98']))
                        <td class="row4">14. Berita Acara Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">14. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">14. Dokumen lain yang dipersyaratkan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">15. Surat Pernyataan Tanggungjawab mutlak(SPTJM)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '98']))
                        <td class="row4">15. Jaminan Pelaksanaan Pekerjaan Asli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['6', '98']))
                        <td class="row4">16. Foto Fisik Pekerjaan(Masing-masing progress/kemajuan fisik)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['6', '98']))
                        <td class="row4">17. e-Faktur pajak dan Surat Setoran Elektronik (SSE)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['6', '98']))
                        <td class="row4">18. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
            @elseif ($beban == '6')
                @if (in_array($jenis, $beban5))
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">a. Pengantar SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '99']))
                            <td class="row1">a. Pengantar SPP-LS</td>
                        @elseif ($jenis == '98')
                            <td class="row1">a. SPP-LS</td>
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">b. SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '99']))
                            <td class="row1">b. SPP-LS</td>
                        @elseif ($jenis == '98')
                            <td class="row1">b. Pengantar SPP-LS</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">c. Ringkasan SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98', '99']))
                            <td class="row1">c. Ringkasan SPP-LS</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">d. Rincian SPP-Gaji</td>
                        @elseif (in_array($jenis, ['3', '4', '5', '6', '8', '10', '11', '12', '13', '14', '15', '16', '98', '99']))
                            <td class="row1">d. Rincian SPP-LS</td>
                        @elseif (in_array($jenis, ['7', '9']))
                            <td class="row1">d. Rincian</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">e. Surat Pernyataan SPP-Gaji</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['3', '4']))
                            <td class="row1">e. Surat Pernyataan Pengajuan SPP-Tambahan Penghasilan</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['6', '7', '99']))
                            <td class="row1">e. Surat Pernyataan Pengajuan SPP-LS</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['8', '9', '10', '13', '14', '15']))
                            <td class="row1">e. Surat Pernyataan SPP-LS</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @elseif (in_array($jenis, ['11', '12', '16', '98']))
                            <td class="row1">e. Surat Pernyataan SPP-LSk</td>
                            <td class="row2"></td>
                            <td class="row3"></td>
                        @endif
                    </tr>
                    <tr>
                        @if ($jenis == '1' || $jenis == '2')
                            <td class="row1">f. Lampiran SPP-Gaji</td>
                        @elseif ($jenis == '3')
                            <td class="row1">f. Lampiran SPP-Tambahan Penghasilan</td>
                        @elseif (in_array($jenis, ['4', '99']))
                            <td class="row1">f. Lampiran SPP-Honorarium PNS</td>
                        @elseif ($jenis == '5')
                            <td class="row1">e. Lampiran SPP-Honorarium Tenaga Kontrak</td>
                        @elseif (in_array($jenis, ['6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98']))
                            <td class="row1">f. Lampiran SPP-LS</td>
                        @else
                        @endif
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                @else
                @endif
                <tr>
                    @if (in_array($jenis, ['1', '2', '3', '4', '5', '6', '7', '8', '9', '99']))
                        <td class="row4">1. Salinan SPD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">1. Persetujuan Panitia Pengadaan Tanah untuk tanah yang luasnya lebih
                            dari 1(satu) hektar</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16', '98']))
                        <td class="row4">1. Photo Copy SPD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['12', '13']))
                        <td class="row4">1. Proposal Bantuan Sosial dari Pihak Ketiga</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['14']))
                        <td class="row4">1. Keputusan Gubernur tentang Penetapan Bantuan Keuangan kepada
                            Kabupaten/Kota</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['15']))
                        <td class="row4">1. Keputusan Gubernur tentang Rencana Bagi Hasil Pajak
                            Provinsi/Rencana Bagi Hasil Pajak Rokok provinsi kalimantan Barat Kepada Kabupaten/Kota
                            Sekalimantan Barat Tahun Anggaran {{ tahun_anggaran() }}</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2']))
                        <td class="row4">2. Daftar Gaji</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">2. Daftar penerima Tambahan Penghasilan(Tanda tangan Penerima, Pembuat
                            Daftar, Setuju dibayar PA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['4', '99']))
                        <td class="row4">2. SK. Pembentukan Tim/Panitia Pelaksanaan Kegiatan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">2. SK. Pengangkatan sebagai Pegawai Non PNS/Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">2. Nota Pencairan Dana yang ditandatangani oleh PPTK</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['8', '9']))
                        <td class="row4">2. Dokumen Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">2. Foto copy bukti kepemilikan tanah/sertifikat hak atas tanah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">2. Proposal Hibah dari Pihak Ketiga</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '12')
                        <td class="row4">2. Keputusan Gubernur tentang Penetapan Bantuan Sosial</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '13')
                        <td class="row4">2. Naskah Perjanjian Hibah Daerah (NPHD)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '14')
                        <td class="row4">2. Surat keterangan rekening Kas Umum Daerah Kabupaten/Kota</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '15')
                        <td class="row4">2. Foto copy rekening Kas Umum Daerah Kabupaten/Kota(diutamakan Bank
                            Pemerintah)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '98')
                        <td class="row4">2. Kuitansi Asli (ditandatangani oleh KDH/WKDH dan Pimpinan DPRD yang
                            menerima,
                            bendahara pengeluaran dan setuju bayar oleh PA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '99')
                        <td class="row4">2. Surat Pernyataan Tanggung jawab Mutlak dari pihak penerima Hibah
                        </td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2']))
                        <td class="row4">3. Rekapitulasi gaji Induk</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">3. Daftar Hadir/Absensi Bulanan mengetahui Kepala SKPD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['4', '99']))
                        <td class="row4">3. Daftar tanda terima Pembayaran Honor yang sudah di tanda tangan
                            (Tanda tangan Pembuat Daftar, Mengetahui PPTK dan Setuju dibayar Pengguna Anggaran)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">3. Surat Perjanjian Kontrak pada saat pengajuan di awal tahun</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">3. Dokumen Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">3. Nota Pencairan Dana yang ditandatangani oleh PPTK</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['9', '10']))
                        <td class="row4">3. Kuitansi Asli bermaterai</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">3. SK Penerima Hibah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['12', '13']))
                        <td class="row4">3. Kuitansi Asli Bermaterai (tanda tangan yang menerima dana,
                            mengetahui
                            PPTK dan setuju dibayar oleh PA/KPA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['14', '15', '99']))
                        <td class="row4">3. Syarat-syarat lainnya sesuai ketentuan yang berlaku</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '98')
                        <td class="row4">3. Daftar penerimaan biaya operasional KDH/WKDH dan Pimpinan DPRD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2']))
                        <td class="row4">4. Rekapitulasi Gaji golongan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">4. Rekap daftar hadir harian Pegawai (Tanda tangan pembuat daftar dan
                            mengetahui Kepala SKPD)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '4')
                        <td class="row4">4. Kuitansi Asli(Tanda tangan Penerima Bendahara Pengeluaran,
                            Mengetahui
                            PPTK dan Setuju dibayar PA/KPA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">4. Daftar tanda terima Pembayaran Honor yang sudah di tanda tangan
                            (Tanda tangan Pembuat Daftar, Mengetahui PPTK dan Setuju dibayar Pengguna Anggaran)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">4. Kwitansi Asli bermaterai</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">4. Kwitansi Asli bermaterai(tanda tangan yang menerima penyedia barang,
                            mengetahui PPTK, setuju dibayar oleh PA/KPA dan Lunas Bayar oleh Bendahara)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">4. Referensi Bank Asli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">4. SPPT PBB tahun transaksi</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">4. Naskah Perjanjian Hibah Daerah (NPHD)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '12')
                        <td class="row4">4. Foto copy rekening bank atas nama penerima sesuai dengan yang
                            tercantum dalan Surat Keputusan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '13')
                        <td class="row4">4. Foto copy rekening bank atas nama penerima bantuan hibah sesuai
                            dengan yang tercantum dalam Surat Keputusan atau Naskah Perjanjian Hibah Daerah)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '98')
                        <td class="row4">4. Fakta Integritas penggunaan belanja operasional KDH/WKDH dan
                            Pimpinan DPRD</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '99')
                        <td class="row4">4. Surat Setoran Elektronik (SSE) PPh 21</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['1', '2', '4']))
                        <td class="row4">5. Surat Setoran Elektronik (SSE) PPh. 21</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">5. Kuitansi Asli (Tanda tangan Penerima Bendahara Pengeluaran dan
                            Setuju
                            dibayar PA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">5. Kuitansi Asli (Tanda tangan Penerima Bendahara Pengeluaran,
                            Mengetahui PPTK dan Setuju dibayar PA/KPA)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">5. Referensi Bank Asli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">5. Referensi Bank Asli dan Nama Penyedia barang harus sesuai dengan
                            Surat Pesanan/Surat Perintah Kerja/Surat Perjanjian</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">5. Berita Acara Pemeriksaan Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">5. Surat Persetujuan Harga antara pemilik dan pihak pembeli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">5. Dokumen Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '12')
                        <td class="row4">5. Surat Pernyataan Tanggung jawab Mutlak dari pihak penerima bantuan
                            sosial</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '13')
                        <td class="row4">5. Surat Pernyataan Tanggung jawab Mutlak dari pihak penerima Hibah
                        </td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '98')
                        <td class="row4">5. Syarat-syarat lainnya sesuai ketentuan peraturan perundang-undangan
                        </td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">6. SK. Perubahan status dari CPNS menjadi PNS</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">6. SK Mutasi Pindah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">6. Surat Setoran Elektronik(SSE) PPh 21</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">6. Surat Setoran Elektronik (SSE) PPh 21 melebihi PTKP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '7']))
                        <td class="row4">6. Fotocopy NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">6. Berita Acara Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">6. Berita Acara Penerimaan Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">6. Surat Pernyataan dari penjual bahwa tanah/akta jual beli ddi hadapan
                            PPAT</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">6. NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['12', '13']))
                        <td class="row4">6. Syarat-syarat lainnya sesuai ketentuan yang berlaku</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">7. SK. Kenaikan Pangkat/Penurunan Pangkat</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">7. Surat Keterangan Pemberhentian Pembayaran (SKPP)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '3')
                        <td class="row4">7. Surat Tanda Setoran(STS) jika ada pemotongan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '5')
                        <td class="row4">7. Surat Setoran Elektronik PNBP 1% dan 4%</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6']))
                        <td class="row4">7. Jaminan Uang Muka(Asli)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">7. Berita Acara Pemeriksaan Barang dibuat setiap hari pelaksanaan makan
                            minum</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">7. Fotocopy NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">7. Berita Acara Serah Terima Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">7. Surat Pelepasan adat(bila diperlukan)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">7. Rekening Bank</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">8. SK. Pengangkatan dalam jabatan Struktural/Fungsional</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">8. Surat Keputusan Pengangkatan sebagai CPNS</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6']))
                        <td class="row4">8. Jaminan Pemeliharaan (Asli)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">8. Berita Acara Penerimaan Barang</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">8. e-Faktur pajak dan Surat Setoran Elektronik (SSE)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">8. BA. Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">8. e-Faktur pajak dan Surat Setoran Elektronik(SSE) PPh Final tas
                            pelepasan Hak = 5%</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">8. Jaminan Uang Muka</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">9. Keputusan Kenaikan Gaji Penyesuaian Masa Kerja.</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">9. Surat Pernyataan melaksanakan Tugas</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6']))
                        <td class="row4">9. Ringkasan Kontrak</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">9. Berita Acara Serah Terima dibuat setiap hari pelaksanaan makan minum
                        </td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">9. Khusus untuk sewa rumah jabatan/gedung untuk kantor/gedung
                            pertemuan/tempat pertemuan/tempat penginapan/kendaraan, Berita Acara Pemeriksaan, Berita
                            Acara Serah Terima(dilakukan pada tanggal mulai sewa). Untuk sewa lebih dari 1 bulan
                            wajib melampirkan Surat Perjanjian Sewa</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">9. Berita Acara Penyelesaian Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">9. Foto copy Rekening Bank atas nama pemilik/penerima</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">9. Jaminan Pemeliharaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">10. Keputusan Pindah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '2')
                        <td class="row4">10. Surat Pernyataan Tanggungjawab mutlak (SPTJM)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6', '11', '16']))
                        <td class="row4">10. Laporan Kemajuan Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">10. Berita Acara Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '8')
                        <td class="row4">10. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">10. Fotocopy NPWP</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '10')
                        <td class="row4">10. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1' || $jenis == '2')
                        <td class="row4">11. Daftar Keluarga</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6']))
                        <td class="row4">11. Berita Acara Pemeriksaan Barang/Jasa/Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['7', '9']))
                        <td class="row4">11. e-Faktur pajak dan Surat Setoran Elektronik (SSE)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">11. BA-pemeriksaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">12. Surat Pernyataan melaksanakan tugas(Surat)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6']))
                        <td class="row4">12. Berita Acara Penerimaan Barang/Jasa/Pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">12. Jadwal Pelaksanaan Kegiatan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">12. Khusus pengadaan sertifikasi tanah</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">12. BA-serah terima</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                @if ($jenis == '9')
                    <tr>
                        <td class="row5">a. Untuk pembayaran angsuran tahap I maksimum 50% pada saat pendaftaran
                            ukur dengan dilampiri foto copy Bukti Pendaftaran Ukur</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        <td class="row5">b. Untuk pembayaran angsuran tahap I maksimum 50% pada saat pendaftaran
                            ukur dengan dilampiri foto copy Bukti Pendaftaran Ukur</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        <td class="row5">b. Untuk pembayaran sisa angsuran sebesar 10% dengan dilampiri</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                    <tr>
                        <td class="row6">- Asli Sertifikat Hak atas Tanah dari Pejabat Pertahanan yang berwenang
                        </td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    </tr>
                @endif
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">13. Tambahan/pengurangan keluarga karena Kawin, dilampiri foto copy
                            surat nikah/akte perkawinan Tambah anak, dilampiri foto copy akte kelahiran Cerai,
                            dilampiri akte
                            cerai</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6']))
                        <td class="row4">13. Berita Acara Serah Terima berdasarkan Kemajuan pekerjaan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">13. Absensi Peserta dan Panitia</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '9')
                        <td class="row4">13. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">13. BA-pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">14. Surat Keputusan Pengangkatan sebagai CPNS</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6']))
                        <td class="row4">14. Berita Acara Pembayaran</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif ($jenis == '7')
                        <td class="row4">14. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['11', '16']))
                        <td class="row4">14. Dokumen lain yang dipersyaratkan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if ($jenis == '1')
                        <td class="row4">15. Surat Pernyataan Tanggungjawab mutlak(SPTJM)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @elseif (in_array($jenis, ['6']))
                        <td class="row4">15. Jaminan Pelaksanaan Pekerjaan Asli</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['6']))
                        <td class="row4">16. Foto Fisik Pekerjaan(Masing-masing progress/kemajuan fisik)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['6']))
                        <td class="row4">17. e-Faktur pajak dan Surat Setoran Elektronik (SSE)</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
                <tr>
                    @if (in_array($jenis, ['6']))
                        <td class="row4">18. Dokumen lain yang diperlukan</td>
                        <td class="row2"></td>
                        <td class="row3"></td>
                    @endif
                </tr>
            @endif
        </tbody>
    </table>
    <br>
    <br>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        @if (in_array($beban, ['1', '2', '3', '4']))
            <tr>
                <td style="width: 300px">Tanggal Pengembalian SPP</td>
                <td>: ..............................................................................</td>
            </tr>
            <tr>
                <td style="height: 20px"></td>
            </tr>
            <tr>
                <td style="width: 300px">Tanggal Terima Kembali SPP</td>
                <td>: ..............................................................................</td>
            </tr>
        @elseif ($beban == '5')
            @if (in_array($jenis, ['11', '98']))
                <tr>
                    <td style="width: 300px">Tanggal Pengembalian SPP</td>
                    <td>: ..............................................................................</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="width: 300px">Tanggal Terima Kembali SPP</td>
                    <td>: ..............................................................................</td>
                </tr>
            @else
            @endif
        @elseif ($beban == '6')
            @if (in_array($jenis, ['11', '99']))
                <tr>
                    <td style="width: 300px">Tanggal Pengembalian SPP</td>
                    <td>: ..............................................................................</td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
                <tr>
                    <td style="width: 300px">Tanggal Terima Kembali SPP</td>
                    <td>: ..............................................................................</td>
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
