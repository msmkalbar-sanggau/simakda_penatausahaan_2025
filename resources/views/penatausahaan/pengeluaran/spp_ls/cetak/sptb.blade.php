<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SURAT SPTJM</title>
    <style>
        .unbold {
            font-weight: normal;
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
            <td align="left" width="7%"
                <?= substr($skpd->kd_skpd, -4) !== '0000' ? 'rowspan="6"' : 'rowspan="5"' ?>>
                <!-- isi sel -->
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
                    @if ($beban == '4')
                        {{ strtoupper($skpd->nm_org) }}
                    @elseif($beban == '5')
                        {{ strtoupper($skpd->nm_org) }}
                    @elseif ($beban == '6')
                        SKPD {{ strtoupper($skpd->nm_org) }}
                    @endif
                </strong>
            </td>
        </tr>
        @if (substr($skpd->kd_skpd, -4) !== '0000')
            <tr>
                <td align="left" style="font-size:16px">
                    <strong>
                        @if ($beban == '4')
                            {{ strtoupper($skpd->nm_skpd) }}
                        @elseif($beban == '5')
                            {{ strtoupper($skpd->nm_skpd) }}
                        @elseif ($beban == '6')
                            UNIT {{ strtoupper($skpd->nm_skpd) }}
                        @endif
                    </strong>
                </td>
            </tr>
        @endif
        <tr>
            <td align="left" style="font-size:16px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <hr>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        @if ($beban == '4')
            <tr>
                <td style="font-size: 16px">
                    <b><u>SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK</u></b>
                </td>
            </tr>
        @elseif ($beban == '5')
            <tr>
                <td style="font-size: 16px">
                    <b><u>SURAT PERNYATAAN TANGGUNG JAWABAN BELANJA</u></b>
                </td>
            </tr>
        @else
            <tr>
                <td style="font-size: 16px">
                    <b><u>SURAT PERNYATAAN PENGAJUAN SPP -
                            {{ strtoupper($lcbeban) }}</u></b>
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px">
                    <b>Nomor : {{ $no_spp }}</b>
                </td>
            </tr>
        @endif
    </table>
    <br>
    <br>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        @if ($beban == '4')
            <tr>
                <td>Yang Bertanda tangan di bawah ini :</td>
            </tr>
            <tr>
                <td style="height: 5px"></td>
            </tr>
            <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
                <tr>
                    <td>Nama</td>
                    <td style="padding-left: 50px">: {{ $cari_bendahara->nama }}</td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td style="padding-left: 50px">: {{ $cari_bendahara->nip }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td style="padding-left: 50px">: {{ $cari_bendahara->jabatan }}</td>
                </tr>
                <tr>
                    <td style="height: 5px"></td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;vertical-align:top">1.</td>
                    <td style="padding-left:50px;text-align:justify">
                        Perhitungan yang terdapat pada Daftar Perhitungan
                        Tambahan Penghasilan bagi PNS di Lingkungan Pemerintah KABUPATEN SANGGAU
                        {{ strtoupper($lcbeban) }} bulan {{ bulan($data->bulan) }} {{ $tahun_anggaran }} bagi
                        {{ $data->nm_skpd }} telah dhitung dengan benar dan berdasarkan daftar hadir kerja Pegawai
                        Negeri Sipil Daerah pada {{ $data->nm_skpd }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;vertical-align:top">2.</td>
                    <td style="padding-left:50px;text-align:justify">
                        Apabila dikemudian hari terdapat kelebihan atas
                        pembayaran {{ strtoupper($lcbeban) }} tersebut kami bersedia untuk menyetorkan kelebihan
                        tersebut ke Kas Daerah
                    </td>
                </tr>
            </table>
        @elseif ($beban == '5')
            <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
                <tr>
                    <td>1. OPD</td>
                    <td>:</td>
                    <td>{{ $kd_skpd }} - {{ $data->nm_skpd }}</td>
                </tr>
                <tr>
                    <td>2. Satuan Kerja</td>
                    <td>:</td>
                    <td>{{ $kd_skpd }} - {{ $data->nm_skpd }}</td>
                </tr>
                <tr>
                    <td>3. Tanggal/NO.DPA</td>
                    <td>:</td>
                    <td>
                        {{ $data_dpa->tgl_dpa == '' ? 'Belum ada Tanggal DPA' : \Carbon\Carbon::parse($data_dpa->tgl_dpa)->locale('id')->isoFormat('DD MMMM Y') }}
                        - {{ $data_dpa->no_dpa }}
                    </td>
                </tr>
                <tr>
                    <td>4. Tahun Anggaran</td>
                    <td>:</td>
                    <td>{{ $tahun_anggaran }}</td>
                </tr>
                <tr>
                    <td>5. Jumlah Belanja</td>
                    <td>:</td>
                    <td>{{ rupiah($data->nilai) }}</td>
                </tr>
                <tr>
                    <td style="height: 5px"></td>
                </tr>
            </table>
        @elseif($beban == '6')
            <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
                <tr>
                    <td>Sehubungan dengan Surat Permintaan Pembayaran Langsung (SPP - LS
                        {{ strtoupper($lcbeban) }} Nomor
                        {{ $no_spp }} Tanggal {{ tanggal($data->tgl_spp) }} yang kami ajukan sebesar
                        {{ rupiah($data->nilai) }}
                        ({{ terbilang($data->nilai) }})</td>
                </tr>
                <tr>
                    <td>Untuk Keperluan OPD : {{ $data->nm_skpd }} Tahun Anggaran
                        {{ $tahun_anggaran }}</td>
                </tr>
                <tr>
                    <td style="height: 5px"></td>
                </tr>
                <tr>
                    <td>Dengan ini menyatakan sebenarnya bahwa :</td>
                </tr>
                <tr>
                    <td style="height: 5px"></td>
                </tr>
            </table>
        @endif
    </table>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        @if ($beban == '4')
            <tr>
                <td style="height: 5px"></td>
            </tr>
            <tr>
                <td>Demikian pernyataan ini kami buat dengan sebenar-benarnya.</td>
            </tr>
        @elseif ($beban == '5')
            <tr>
                <td>Yang bertanda tangan di bawah ini adalah
                    {{ $cari_bendahara->jabatan }} Satuan Kerja
                    {{ $data->nm_skpd }} Menyatakan bahwa saya bertanggung jawab penuh atas segala
                    pengeluaran-pengeluaran
                    yang telah dibayar lunas oleh Bendahara Pengeluaran kepada yang berhak menerima, sebagaimana tertera
                    dalam Laporan Pertanggung Jawaban Tambah Uang di sampaikan oleh Bendahara Pengeluaran</td>
            </tr>
            <tr>
                <td style="height: 5px"></td>
            </tr>
            <tr>
                <td>Bukti-bukti belanja tertera dalam Laporan
                    Pertanggung Jawaban
                    Tambah Uang disimpan sesuai
                    ketentuan yang
                    berlaku pada sistem Satuan Kerja {{ $data->nm_skpd }} untuk kelengkapan administrasi dan keperluan
                    pemeriksaan aparat pengawasan Fungsional</td>
            </tr>
            <tr>
                <td style="height: 5px"></td>
            </tr>
            <tr>
                <td>Demikian pernyataan
                    ini kami buat dengan
                    sebenar-benarnya.</td>
            </tr>
        @elseif ($beban == '6')
            <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
                <tr>
                    <td style="padding-left: 20px;vertical-align:top">1.</td>
                    <td style="padding-left: 50px;text-align:justify">Jumlah Pembayaran Langsung (LS)
                        {{ $lcbeban }} tersebut di atas
                        akan dipergunakan untuk
                        keperluan guna membiayai kegiatan yang akan kami laksanan sesuai DPA-OPD</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;vertical-align:top">2.</td>
                    <td style="padding-left: 50px;text-align:justify">Jumlah Pembayaran Langsung (LS)
                        {{ $lcbeban }} tersebut tidak
                        akan dipergunakan untuk
                        membiayai
                        pengeluaran-pengeluaran yang menurut ketentuan yang berlaku
                        harus dilakasanakan dengan Pembayaran Langsung (UP/GU/TU/LS-Gaji)</td>
                </tr>
                <tr>
                    <td style="height:5px"></td>
                </tr>
                <tr>
                    <td colspan="2">Demikian Surat pernyataan ini dibuat untuk melengkapi persyaratan
                        pengajuan
                        SPP-LS {{ $lcbeban }} OPD kami</td>
                </tr>
            </table>
        @endif
    </table>
    <br>
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%">
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
