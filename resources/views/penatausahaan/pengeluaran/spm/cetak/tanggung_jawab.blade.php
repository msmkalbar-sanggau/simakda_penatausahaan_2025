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
            <td align="left" width="7%"
                <?= substr($nama_skpd->kd_skpd, -4) !== '0000' ? 'rowspan="6"' : 'rowspan="5"' ?>>
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
                    {{ strtoupper($nama_skpd->nm_org) }}
                </strong>
            </td>
        </tr>
        @if (substr($nama_skpd->kd_skpd, -4) !== '0000')
            <tr>
                <td align="left" style="font-size:16px">
                    <strong>
                        {{ strtoupper($nama_skpd->nm_skpd) }}
                    </strong>
                </td>
            </tr>
        @endif
        <tr>
            <td align="left" style="font-size:16px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <hr>
    <br>
    <div style="text-align: center">
        <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center"
            class="rincian">
            <tr>
                <td style="font-size:16px"><strong>SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK</strong></td>
            </tr>
        </table>
    </div>
    <br>
    <br>
    <div>
        <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
            <tr>
                <td>Yang bertanda tangan di bawah ini :</td>
            </tr>
        </table>

        <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
            <tbody>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $pa_kpa->nama }}</td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>:</td>
                    <td>{{ $pa_kpa->nip }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $pa_kpa->jabatan }}</td>
                </tr>
                <tr>
                    <td style="height: 10px" colspan="3"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div>
        <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
            <tbody>
                <tr>
                    <td colspan="2">Menyatakan dengan sesungguhnya bahwa :</td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 10px"></td>
                </tr>
                {{-- <h5 style="margin: 2px 0px">Menyatakan dengan sesungguhnya bahwa:</h5> --}}
                <tr>
                    <td style="padding-left:10px;width: 5%;vertical-align:top">1.</td>
                    <td style="text-align:justify">
                        <p style="margin: 2px 0px">SPM {{ cari_jenis($beban) }} Nomor: {{ $no_spm }} tanggal
                            {{ tanggal($data_spm->tgl_spm) }} dengan nominal sebesar Rp.
                            {{ rupiah($data_spm->nilai) }}
                            yang kami
                            ajukan untuk diterbitkan Surat Perintah Pencairan
                            Dana (SP2D), semua dokumen kelengkapannya sudah kami verifikasi dan sudah lengkap dan benar
                            berdasarkan persyaratan dan ketentuan yang berlaku.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:10px;vertical-align:top">2.</td>
                    <td style="text-align: justify">
                        <p style="margin: 2px 0px">Bahwa semua dokumen persyaratan dan kelengkapan untuk penerbitan SPM
                            tersebut disimpan sesuai dengan ketentuan yang berlaku pada
                            <u><i>{{ $nama_skpd->nm_skpd }}</i></u> sebagai bukti pertanggungjawaban pelaksanaan
                            kegiatan
                            serta keperluan pemeriksaan oleh aparat pengawas fungsional dan kebutuhan lainnya.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="height: 10px"></td>
                </tr>
                <tr>
                    <td colspan="2">Demikian surat pernyataan ini dibuat dengan sebenar-benarnya, apabila dokumen
                        yang
                        dipersyaratkan tersebut terdapat kekeliruan, kekurangan dan tidak sah sesuai ketentuan yang
                        berlaku akan
                        menjadi tanggungjawab kami sepenuhnya selaku {{ $pa_kpa->jabatan }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br><br>
    <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif"
        class="rincian">
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
            <td style="text-align: center;padding-left:600px">{{ $pa_kpa->pangkat }}</td>
        </tr>
        <tr>
            <td style="text-align: center;padding-left:600px">NIP. {{ $pa_kpa->nip }}</td>
        </tr> --}}
    </table>

</body>

</html>
