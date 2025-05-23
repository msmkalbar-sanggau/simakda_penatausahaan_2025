<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table,
        td,
        th {
            border-collapse: collapse
        }

        .center {
            text-align: center;
            border: 1px solid black;
        }

        .border {
            border-left: 1px solid black;
            border-bottom: 1px solid black
        }

        .bottom {
            border-bottom: 1px solid black;
        }

        #atas {
            font-size: 14px;
            font-family: Tahoma;
        }

        #rincian {
            font-size: 14px;
            font-family: Tahoma
        }

        #potongan {
            font-size: 14px;
            font-family: Tahoma
        }

        #bawah {
            font-size: 14px;
            font-family: Tahoma
        }

        #ttd {
            font-size: 14px;
            font-family: Tahoma
        }

        .bottom1 {
            border-bottom: 1px solid black;
            border-right: 1px solid black;
        }

        .kanan {
            border-right: 1px solid black;
        }

        .kiri {
            border-left: 1px solid black;
        }

        #rincian>thead>tr>th {
            border: 1px solid black;
            text-align: center
        }

        /* #rincian>tbody>tr>td {
            border: 1px solid black;
        } */

        #potongan>thead>tr>th {
            /* border: 1px solid black; */
            text-align: center;
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        #potongan>tbody>tr>td {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    @if ($kop == '1')
        <table style="border-collapse:collapse;font-family: Tahoma; font-size:12px" width="100%" align="center"
            border="0">
            <tr>
                <td rowspan="6" align="left" width="10%">
                    <img src="{{ asset('template/assets/images/' . $header->logo_pemda_warna) }}" width="75"
                        height="100" />
                </td>
                <td align="left" style="font-size:14px" width="93%">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" style="font-size:16px" width="93%"><strong>PEMERINTAH KABUPATEN SANGGAU</strong>
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size:16px;margin-top:2px" width="93%"><strong>BADAN PENGELOLA KEUANGAN
                        DAN ASET
                        DAERAH</strong>
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size:16px">
                    Jalan Jenderal Sudirman No.101 fax. (0564) 21386
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size:16px">S A N G G A U</td>
            </tr>
            <tr>
                <td align="right" style="font-size:16px">Kode Pos: 78512</td>
            </tr>
        </table>
        <hr style="border: 1px solid black">
    @else
        <br><br><br><br><br><br>
    @endif
    <div>
        <table style="width: 100%" style="border:1px solid black" id="atas">
            <tbody>
                <tr>
                    <td colspan="3" class="center" style="font-size: 14px;width:50%"><b>PEMERINTAH KABUPATEN
                            SANGGAU</b>
                    </td>
                    <td colspan="3" class="center" style="font-size: 14px;width:50%"><b>SURAT PERINTAH PENCAIRAN DANA
                            (SP2D)<br>Nomor :
                            {{ $no_sp2d }}</b></td>
                </tr>
                <tr>
                    <td style="border-left: 1px solid black">Nomor SPM</td>
                    <td>:</td>
                    <td>{{ $sp2d->no_spm }}</td>

                    <td style="border-left: 1px solid black">Dari</td>
                    <td>:</td>
                    <td style="border-right: 1px solid black">Kuasa BUD</td>
                </tr>
                <tr>
                    <td style="border-left: 1px solid black">Tanggal</td>
                    <td>:</td>
                    <td>{{ tanggal($sp2d->tgl_spm) }}</td>

                    <td style="border-left: 1px solid black">NPWP</td>
                    <td>:</td>
                    <td style="border-right: 1px solid black"></td>
                </tr>
                @if (substr($skpd->kd_skpd, -4) === '0000')
                    <tr>
                        <td class="border" style="vertical-align:top">Nama SKPD</td>
                        <td style="vertical-align: top">:</td>
                        <td class="bottom"><strong>{{ $sp2d->nm_skpd }}</strong></td>

                        <td class="border" style="vertical-align: top">Tahun Anggaran</td>
                        <td class="bottom" style="vertical-align: top">:</td>
                        <td class="bottom1" style="vertical-align: top">{{ tahun_anggaran() }}</td>
                    </tr>
                @else
                    <tr>
                        <td style="border-left: 1px solid black">Nama SKPD</td>
                        <td>:</td>
                        <td><strong>{{ $skpd->nm_org }}</strong></td>

                        <td style="border-left: 1px solid black">Tahun Anggaran</td>
                        <td>:</td>
                        <td style="border-right: 1px solid black">{{ tahun_anggaran() }}</td>
                    </tr>
                    <tr>
                        <td class="border" style="vertical-align:top">Nama Unit</td>
                        <td style="vertical-align: top">:</td>
                        <td class="bottom"><strong>{{ $skpd->nm_skpd }}</strong></td>

                        <td class="border" style="vertical-align: top"></td>
                        <td class="bottom" style="vertical-align: top"></td>
                        <td class="bottom1" style="vertical-align: top"></td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2" style="border-left: 1px solid black">Bank Pengirim
                    </td>
                    <td colspan="4" style="border-right:1px solid black">: PT. Bank Kalbar Cabang Sanggau
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="border-left: 1px solid black;border-right:1px solid black">Hendaklah
                        mencairkan / memindahbukukan dari baki Rekening Nomor 3001006966</td>
                </tr>
                <tr>
                    <td colspan="2" class="border">Uang sebesar Rp</td>
                    <td colspan="4" class="bottom1">: Rp. {{ rupiah($nilai_sp2d->nilai) }}
                        ({{ terbilang($nilai_sp2d->nilai) }})</td>
                </tr>
                <tr>
                    <td class="kiri">Kepada</td>
                    <td>:</td>
                    <td colspan="4" class="kanan">
                        @if (($sp2d->jns_spp == '6' && $sp2d->jenis_beban == '6') || $sp2d->jns_spp == '5')
                            {{-- {{ $sp2d->pimpinan }}, {{ $sp2d->nmrekan }}, {{ $sp2d->alamat }} --}}
                            {{ $bendahara->nm_rekening }}
                        @else
                            {{-- {{ $ttd_skpd->nama ? $ttd_skpd->nama : 'Belum Ada data Bendahara' }} -
                            {{ $ttd_skpd->jabatan ? $ttd_skpd->jabatan : '' }} --}}
                            {{ $bendahara->nm_rekening }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="kiri">NPWP</td>
                    <td>:</td>
                    <td colspan="4" class="kanan">
                        @if (($sp2d->jns_spp == '6' && $sp2d->jenis_beban == '6') || $sp2d->jns_spp == '5')
                            {{ $sp2d->npwp == '000000000000000' ? '-' : npwp($sp2d->npwp) }}
                        @else
                            {{ $bank->npwp ? npwp($bank->npwp) : 0 }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="kiri" style="width: 15%">No. Rekening Bank</td>
                    <td>:</td>
                    <td colspan="4" class="kanan">
                        @if (($sp2d->jns_spp == '6' && $sp2d->jenis_beban == '6') || $sp2d->jns_spp == '5')
                            {{ $sp2d->no_rek }}
                        @else
                            {{ $bank->no_rek ? $bank->no_rek : 0 }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="kiri">Bank Penerima</td>
                    <td>:</td>
                    <td colspan="4" class="kanan">
                        @if (($sp2d->jns_spp == '6' && $sp2d->jenis_beban == '6') || $sp2d->jns_spp == '5')
                            {{ $sp2d->bank ? bank($sp2d->bank) : 'Belum Pilih Bank' }}
                        @else
                            {{ $bank->bank ? bank($bank->bank) : 'Belum Pilih Bank' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="kiri" style="vertical-align: top">Keperluan Untuk</td>
                    <td style="vertical-align: top">:</td>
                    <td colspan="4" class="kanan">{{ $sp2d->keperluan }}</td>
                </tr>
                <tr>
                    <td class="kiri">Pagu Anggaran</td>
                    <td>:</td>
                    <td colspan="4" class="kanan">Rp. {{ nilai_pagu($sp2d->kd_skpd, $sp2d->no_spp, $beban) }}
                    </td>
                </tr>
            </tbody>
        </table>
        <table id="rincian" style="width: 100%">
            <thead>
                <tr>
                    <th><strong>NO</strong></th>
                    <th><strong>KODE KEGIATAN/SUB KEGIATAN</strong></th>
                    <th><strong>URAIAN</strong></th>
                    <th><strong>JUMLAH</strong><br>(Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1</td>
                    <td class="center">2</td>
                    <td class="center">3</td>
                    <td class="center">4</td>
                </tr>
                @if (in_array($sp2d->jns_spp, ['1', '2']))
                    <tr>
                        <td style="text-align: center;border-left:1px solid black;border-right:1px solid black">1</td>
                        <td style="border-right:1px solid black">{{ $sp2d->kd_skpd }}</td>
                        <td style="border-right:1px solid black">{{ $sp2d->nm_skpd }}</td>
                        <td style="text-align: right;border-right:1px solid black">{{ rupiah($total->nilai) }}</td>
                    </tr>
                @else
                    @foreach ($data_sp2d as $item)
                        @if ($item->urut == '3')
                            <tr>
                                <td
                                    style="text-align: center;border-left:1px solid black;border-right:1px solid black">
                                    {{ $loop->iteration }}</td>
                                <td style="border-right:1px solid black">{{ dotrek($item->kd_rek) }}</td>
                                <td style="border-right:1px solid black">{{ $item->nm_rek }}</td>
                                <td style="text-align: right;border-right:1px solid black">{{ rupiah($item->nilai) }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td
                                    style="text-align: center;border-left:1px solid black;border-right:1px solid black">
                                    <b>{{ $loop->iteration }}</b>
                                </td>
                                <td style="border-right:1px solid black"><b>{{ $item->kd_rek }}</b></td>
                                <td style="border-right:1px solid black"><b>{{ $item->nm_rek }}</b></td>
                                <td style="text-align: right;border-right:1px solid black">
                                    <b>{{ rupiah($item->nilai) }}</b>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif
                @if ($jumlah <= $baris)
                    @for ($i = $jumlah; $i <= $baris; $i++)
                        <tr>
                            <td
                                style="text-align: center;border-left:1px solid black;border-right:1px solid black;height:{{ $i }}px">
                            </td>
                            <td style="border-right:1px solid black"></td>
                            <td style="border-right:1px solid black"></td>
                            <td style="text-align: right;border-right:1px solid black">
                            </td>
                        </tr>
                    @endfor
                @endif
                <tr>
                    <td colspan="3"
                        style="text-align: right;border-bottom:1px solid black;border-left:1px solid black;border-top:1px solid black">
                        <strong>JUMLAH</strong>
                    </td>
                    <td
                        style="text-align: right;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black">
                        {{ rupiah($total->nilai) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="border:1px solid black">Potongan-potongan</td>
                </tr>
            </tbody>
        </table>
        <table id="potongan" style="width: 100%" style="border:1px solid black">
            <thead>
                <tr>
                    <th><strong>NO</strong></th>
                    <th><strong>Uraian (No. Rekening)</strong></th>
                    <th><strong>Jumlah (Rp)</strong></th>
                    <th><strong>Keterangan</strong></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($potongan1 as $potongan)
                    <tr>
                        <td style="text-align: center">{{ $loop->iteration }}</td>
                        <td>{{ dotrek($potongan->kd_rek6) }} {{ $potongan->nm_rek6 }}</td>
                        <td style="text-align: right">{{ rupiah($potongan->nilai) }}</td>
                        <td></td>
                    </tr>
                @endforeach
                @if ($jumlah_potongan1 <= '4')
                    @for ($i = $jumlah_potongan1; $i < 4; $i++)
                        <tr>
                            <td style="height: 20px"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                @endif
                <tr>
                    <td colspan="2" style="text-align:right"><b>Jumlah</b></td>
                    <td style="text-align: right"><b>{{ rupiah($total_potongan1->nilai) }}</b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4">Informasi <i>(tidak mengurangi jumlah pembayaran SP2D)</i></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th><strong>NO</strong></th>
                    <th><strong>Uraian (No. Rekening)</strong></th>
                    <th><strong>Jumlah (Rp)</strong></th>
                    <th><strong>Keterangan</strong></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($potongan2 as $potongan)
                    <tr>
                        <td style="text-align: center">{{ $loop->iteration }}</td>
                        <td>{{ dotrek($potongan->kd_rek6) }} {{ $potongan->nm_rek6 }}</td>
                        <td style="text-align: right">{{ rupiah($potongan->nilai) }}</td>
                        <td></td>
                    </tr>
                @endforeach
                @if ($jumlah_potongan2 <= '4')
                    @for ($i = $jumlah_potongan2; $i < 4; $i++)
                        <tr>
                            <td style="height: 20px"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                @endif
                <tr>
                    <td colspan="2" style="text-align:right"><b>Jumlah</b></td>
                    <td style="text-align: right"><b>{{ rupiah($total_potongan2->nilai) }}</b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4"><strong>SP2D yang Dibayarkan</strong></td>
                </tr>
            </tbody>
            <table style="width: 100%" style="border: 1px solid black" id="bawah">
                <tr>
                    <td colspan="2" class="border">Jumlah yang Diminta</td>
                    <td class="bottom">Rp</td>
                    <td style="text-align: right" class="bottom kanan">{{ rupiah($total->nilai) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="border">Jumlah Potongan</td>
                    <td class="bottom">Rp</td>
                    <td style="text-align: right" class="bottom kanan">
                        {{ rupiah($total_potongan1->nilai) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="border"><b>Jumlah yang Dibayarkan</b></td>
                    <td class="bottom"><strong>Rp</strong></td>
                    <td style="text-align: right" class="bottom kanan">
                        <b>{{ rupiah($total->nilai - $total_potongan1->nilai) }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="border kanan"><strong>Uang Sejumlah :
                            {{-- ({{ terbilang($total->nilai - ($total_potongan1->nilai + $total_potongan2->nilai)) }})</strong> --}}
                            ({{ terbilang($total->nilai - $total_potongan1->nilai) }})</strong>
                    </td>
                </tr>
            </table>
            <table style="width: 100%" id="ttd" style="border: 1px solid black">
                <tr>
                    <td colspan="6" class="kanan kiri" style="height: 20px"></td>
                </tr>
                <tr>
                    <td class="kiri">Lembar 1</td>
                    <td>:</td>
                    <td>Bank Yang Ditunjuk</td>
                    <td></td>
                    <td style="text-align: center" class="kanan"><b>Sanggau, {{ tanggal($sp2d->tgl_sp2d) }}</b>
                    </td>
                </tr>
                <tr>
                    <td class="kiri">Lembar 2</td>
                    <td>:</td>
                    <td>Pengguna Anggaran/Kuasa Pengguna Anggaran</td>
                    <td></td>
                    <td style="text-align: center" class="kanan">
                    </td>
                </tr>
                <tr>
                    <td class="kiri">Lembar 3</td>
                    <td>:</td>
                    <td>BUD/Kuasa BUD</td>
                    <td></td>
                    <td style="text-align: center" class="kanan"><b>{{ $ttd1->jabatan }}</b></td>
                </tr>
                <tr>
                    <td class="kiri">Lembar 4</td>
                    <td>:</td>
                    <td>Pihak Penerima</td>
                    <td></td>
                    <td style="text-align: center" class="kanan"></td>
                </tr>
                <tr>
                    <td class="kiri">Lembar 5</td>
                    <td>:</td>
                    <td>Arsip</td>
                    <td></td>
                    <td style="text-align: center" class="kanan"></td>
                </tr>
                <tr>
                    <td colspan="6" class="kanan kiri" style="height: 20px"></td>
                </tr>
                <tr>
                    <td class="kiri"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: center" class="kanan"><b><u>{{ $ttd1->nama }}</u></b></td>
                </tr>
                <tr>
                    <td class="kiri"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: center" class="kanan"><b>{{ $ttd1->pangkat }}</b></td>
                </tr>
                <tr>
                    <td class="kiri bottom"></td>
                    <td class="bottom"></td>
                    <td class="bottom"></td>
                    <td class="bottom"></td>
                    <td style="text-align: center" class="kanan bottom"><b>NIP. {{ $ttd1->nip }}</b></td>
                </tr>
            </table>
        </table>
    </div>
</body>

</html>
