<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Ringkasan</title>
    <style>
        table,
        th,
        td {
            border-collapse: collapse;
        }

        .border {
            border: 1px solid black;
        }

        .border1 {
            border-bottom: 1px solid black;
        }

        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
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
            <td align="left" style="font-size:16px" width="93%"><strong>SKPD {{ $skpd->nm_skpd }}</strong></td>
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
            <td style="font-size:16px">
                <b>SURAT PERMINTAAN PEMBAYARAN TAMBAHAN UANG PERSEDIAAN</b> <br>
                <b>(SPP - TU)</b> <br>
                <b>Nomor : {{ $no_spp }}</b> <br>
                <b><u>RINGKASAN</u></b>
            </td>
        </tr>
        <tr>
            <td style="height: 20px"></td>
        </tr>
    </table>

    <table style="width: 100%;border:1px solid black;font-family:'Open Sans', Helvetica,Arial,sans-serif"
        class="rincian">
        <tbody>
            <tr>
                <td colspan="5" class="center border" style="text-align: center"><strong>RINGKASAN
                        DPA/DPPA/DPPAL-OPD</strong></td>
            </tr>
            <tr>
                <td colspan="3" class="border">Jumlah dana DPA/DPPA/DPPAL-OPD</td>
                <td style="text-align: right">{{ rupiah($nilai_ang->nilai) }}</td>
                <td style="text-align: right">(I)</td>
            </tr>
            <tr>
                <td colspan="5" class="center border" style="text-align: center"><strong>RINGKASAN SPD</strong></td>
            </tr>
            <tr>
                <td class="center border">No. Urut</td>
                <td class="center border">Nomor SPD</td>
                <td class="center border">Tanggal SPD</td>
                <td class="center border" colspan="2">Jumlah Dana</td>
            </tr>
            @php
                $total_spd = 0;
            @endphp
            @foreach ($data_beban as $data1)
                @php
                    $total_spd += $data1->nilai;
                @endphp
                <tr>
                    <td class="center border">{{ $loop->iteration }}</td>
                    <td class="border">{{ $data1->no_spd }}</td>
                    <td class="border">{{ tanggal($data1->tgl_spd) }}</td>
                    <td class="border1" style="text-align: right">{{ rupiah($data1->nilai) }}</td>
                    <td class="border1"></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right" class="border"><i>JUMLAH</i></td>
                <td class="border1" style="text-align: right">{{ rupiah($total_spd) }}</td>
                <td class="border1" style="text-align: right">(II)</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right" class="border"><i>Sisa dana yang belum di SPD-kan
                        (I-II)</i></td>
                <td style="text-align: right">{{ rupiah($nilai_ang->nilai - $total_spd) }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="height: 20px" colspan="3" class="border"></td>
                <td class="border" colspan="2"></td>
            </tr>
            <tr>
                <td colspan="5" class="center border" style="text-align: center"><strong>RINGKASAN BELANJA</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="border">SP2D Belanja UP</td>
                <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                    {{ sp2dbelanja_up($kd_skpd, $beban, $no_spp, $tgl_spp, $kd_sub_kegiatan) }}
                </td>
                <td class="border1"></td>
            </tr>
            <tr>
                <td colspan="3" class="border">SP2D Belanja GU</td>
                <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                    {{ sp2dbelanja_gu($kd_skpd, $no_spp, $tgl_spp, $beban, $kd_sub_kegiatan) }}
                </td>
                <td class="border1"></td>
            </tr>
            <tr>
                <td colspan="3" class="border">SP2D Belanja TU</td>
                <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                    {{ sp2dbelanja_tu($kd_skpd, $no_spp, $tgl_spp, $beban, $kd_sub_kegiatan) }}
                </td>
                <td class="border1"></td>
            </tr>
            <tr>
                <td colspan="3" class="border">SP2D Belanja LS GAJI</td>
                <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                    {{ sp2dbelanja_lsgaji($kd_skpd, $no_spp, $tgl_spp, $beban, $kd_sub_kegiatan) }}
                </td>
                <td class="border1"></td>
            </tr>
            <tr>
                <td colspan="3" class="border">SP2D Belanja LS Pengadaan Barang dan Jasa</td>
                <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                    {{ sp2dbelanja_barang($kd_skpd, $no_spp, $tgl_spp, $beban, $kd_sub_kegiatan) }}
                </td>
                <td class="border1"></td>
            </tr>
            <tr>
                <td colspan="3" class="border">SP2D Belanja LS Pihak Ketiga Lainnya</td>
                <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                    {{ sp2dbelanja_pihak($kd_skpd, $no_spp, $tgl_spp, $beban, $kd_sub_kegiatan) }}
                </td>
                <td class="border1"></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right" class="border"><i>JUMLAH</i></td>
                <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                    {{ total_belanja($kd_skpd, $no_spp, $tgl_spp, $beban, $kd_sub_kegiatan) }}
                </td>
                <td class="border1"></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right"><i>Sisa SPD yang telah diterbitkan, belum
                        dibelanjakan
                        (II-III) </i></td>
                <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                    {{ sisa_spd($total_spd, $kd_skpd, $no_spp, $tgl_spp, $beban, $kd_sub_kegiatan) }}
                </td>
                <td class="border1" style="text-align: right">(III)</td>
            </tr>
        </tbody>
    </table>
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width:100%;font-family:'Open Sans', Helvetica, Arial,sans-serif">
            <tr>
                <td style="text-align: center">MENGETAHUI :</td>
                <td style="margin: 2px 0px;text-align: center">
                    {{ $daerah->daerah }},
                    @if ($tanpa == 1)
                        ______________{{ tahun_anggaran() }}
                    @else
                        {{ tanggal($tgl_spp) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $pptk->jabatan }}
                </td>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    <b><u>{{ $pptk->nama }}</u></b> <br>
                    {{ $pptk->pangkat }} <br>
                    NIP. {{ $pptk->nip }}
                </td>
                <td style="text-align: center">
                    <b><u>{{ $bendahara->nama }}</u></b> <br>
                    {{ $bendahara->pangkat }} <br>
                    NIP. {{ $bendahara->nip }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
