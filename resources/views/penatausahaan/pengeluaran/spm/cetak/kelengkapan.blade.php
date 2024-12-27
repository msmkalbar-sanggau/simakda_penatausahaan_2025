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
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="125"
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
                    {{ $skpd->nm_skpd }}
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

    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0" cellspacing="0"
        cellpadding="0">
        <tr>
            <td align="center" style ="font-size:16px"><b><u>PENELITIAN KELENGKAPAN DOKUMEN SPP
                        {{ Str::upper(nama_beban($beban, $data_beban->jenis_beban)) }}</u></b></td>
        </tr>
        <tr>
            <td align="center" style ="font-size:12px">No SPP : {{ $spm->no_spp }} </td>
        </tr>
    </table>SPP :<table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Surat Pengantar SPP</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0" cellspacing="0"
        cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Ringkasan SPP</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0" cellspacing="0"
        cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Rincian SPP</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0" cellspacing="0"
        cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Salinan SPD</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Salinan Surat Rekomendasi dari SKPD Terkait</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">SSP disertai Faktur Pajak (PPN dan PPh) yang telah
                ditandatangani wajib pajak dan wajib pungut</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Surat Perjanjian Kerjasama/kontrak antara Pengguna
                Anggaran/Kuasa Pengguna Anggaran dengan pihak ketiga serta mencantumkan nomor rekening bank Pihak Ketiga
            </td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Berita Acara Penyelesaian Pekerjaan</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Berita Acara Serah Terima Barang dan Jasa</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Berita Acara Pembayaran</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Kuitansi bermaterai, nota/faktur yang ditandatangani
                Pihak Ketiga dan PPTK serta disetujui oleh Pengguna Anggaran/Kuasa Pengguna Anggaran</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Surat Jaminan Bank atau yang dipersamakan yang
                dikeluarkan oleh bank atau lembaga keuangan non bank</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Dokumen lain yang dipersyaratkan untuk
                kontrak-kontrak yang dananya sebagian atau seluruhnya bersumber dari perusahaan pinjaman/hibah luar
                negeri</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Berita Acara Pemeriksaan yang ditandatangani oleh
                Pihak Ketiga/Rekanan serta unsur Panitia Pemeriksaan Barang berikut lampiran daftar barang yang
                diperiksa</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Surat angkutan atau konosemen apabila pengadaan
                barang dilaksanakan diluar wilayah kerja</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Surat pemberitahuan potongan denda keterlambatan
                pekerjaan dari PPTK apabila pekerjaan mengalami keterlambatan</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Foto/buku/dokumentasi tingkat kemajuan/penyelesaian
                pekerjaan</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" style ="font-size:12px;width:10%;height:35px;">
                <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="1"
                    cellspacing="0" cellpadding="0">
                    <tr>
                        <td style ="height:30px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="left" style ="font-size:12px;width:5%;"></td>
            <td align="left" style ="font-size:12px;width:85%;">Potongan Jamsostek (potongan sesuai dengan ketentuan
                yang berlaku/surat pemberitahuan Jamsostek) khusus untuk pekerjaan konsultan yang perhitungan harganya
                menggunakan biaya personil (billing rate) Berita Acara Prestasi Kemajuan Pekerjaan dan bukti kehadiran
                dari tenaga konsultan sesuai pentahapan waktu pekerjaan dilampiri dengan bukti penyewaan/pembelian alat
                penunjang serta bukti pengeluaran lainnya berdasarkan rincian dalam Surat Penawaran</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style ="font-size:16px;height:30px;">&nbsp;</td>
        </tr>
    </table>
    <table style="border-collapse:collapse;font-family: Times New Roman;" width="100%" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style ="font-size:13px;" colspan = "3"><u><b>PENELITIAN KELENGKAPAN DOKUMEN
                        SPP<b /></u></td>
        </tr>
        <tr>
            <td align="left" style ="font-size:12px;width:15%;">Tanggal</td>
            <td align="center" style ="font-size:12px;width:5%;">:</td>
            <td align="left" style ="font-size:12px;width:80%;">
                {{ \Carbon\Carbon::parse($spm->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}</td>
        </tr>
        <tr>
            <td align="left" style ="font-size:12px;width:15%;">Nama</td>
            <td align="center" style ="font-size:12px;width:5%;">:</td>
            <td align="left" style ="font-size:12px;width:80%;"> {{ $pptk->nama }}</td>
        </tr>
        <tr>
            <td align="left" style ="font-size:12px;width:15%;">NIP</td>
            <td align="center" style ="font-size:12px;width:5%;">:</td>
            <td align="left" style ="font-size:12px;width:80%;">{{ $pptk->nip }}</td>
        </tr>
        <tr>
            <td align="left" style ="font-size:12px;width:15%;" valign="top">Tanda Tangan</td>
            <td align="center" style ="font-size:12px;width:5%;" valign="top">:</td>
            <td align="left" style ="font-size:12px;width:80%;height:60px;" valign="bottom">....................
            </td>
        </tr>
        <tr>
            <td align="left" style ="font-size:10px;width:15%;">Lembar Asli<br>Salinan 1<br>Salinan 2<br>Salinan 3
            </td>
            <td align="center" style ="font-size:10px;width:5%;">:<br>:<br>:<br>:</td>
            <td align="left" style ="font-size:10px;width:80%;height:60px;">Untuk PA / KPA / PPK - SKPD<br>Untuk
                Kuasa BUD<br>Untuk Bendahara Pengeluaran / PPTK<br>Arsip Bendahara Pengeluaran / PPTK</td>
        </tr>
    </table>
    <br>
    <br>


    <br>
    <br>
    {{-- <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <tbody>
            <tr>
                <td style="text-align: center;padding-left:600px">
                    Dikerjakan oleh
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center;padding-left:600px">
                    {{ $pptk->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:600px">
                    <b><u>{{ $pptk->nama }}</u></b>
                    <br>
                    {{ $pptk->pangkat }}
                    <br>
                    NIP. {{ $pptk->nip }}
                </td>
            </tr> --}}
    {{-- <tr>
                <td style="text-align: center;padding-left:600px">{{ $pptk->pangkat }}</td>
            </tr> --}}
    {{-- <tr>
                <td style="text-align: center;padding-left:600px">NIP. {{ $pptk->nip }}</td>
            </tr> --}}
    {{-- </tbody>
    </table> --}}

</body>



</html>
