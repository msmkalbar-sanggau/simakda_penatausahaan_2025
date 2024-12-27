<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table,
        tr,
        td,
        th {
            border-collapse: collapse
        }

        .center {
            text-align: center
        }

        .border {
            border: 1px solid black;
        }

        .border1 {
            border-bottom: 1px solid black;
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
            <td align="left" style="font-size:14px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px">
                <strong>
                    {{ $data_skpd->nm_skpd }}
                </strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <hr>

    <div>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td class="center">SURAT PERINTAH MEMBAYAR GANTI UANG PERSEDIAAN</td>
                </tr>
                <tr>
                    <td class="center">(SPM - GU)</td>
                </tr>
                <tr>
                    <td class="center"><strong>Nomor : {{ $no_spm }}</strong></td>
                </tr>
                <tr>
                    <td class="center"><strong><u>RINGKASAN</u></strong></td>
                </tr>
                <tr>
                    <td style="height: 20px"></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%;border:1px solid black">
            <tbody>
                <tr>
                    <td colspan="5" class="center border"><strong>RINGKASAN DPA/DPPA/DPPAL-OPD</strong></td>
                </tr>
                <tr>
                    <td colspan="3" class="border">Jumlah dana DPA/DPPA/DPPAL-OPD</td>
                    <td style="text-align: right">{{ rupiah($nilai_ang->nilai) }}</td>
                    <td style="text-align: right">(I)</td>
                </tr>
                <tr>
                    <td colspan="5" class="center border"><strong>RINGKASAN SPD</strong></td>
                </tr>
                <tr>
                    <td class="center border">No. Urut</td>
                    <td class="center border">Nomor SPD</td>
                    <td class="center border">Tanggal SPD</td>
                    <td class="center border" colspan="2">Jumlah Dana</td>
                </tr>
                @foreach ($data_beban as $data1)
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
                    <td colspan="5" class="center border"><strong>RINGKASAN BELANJA</strong></td>
                </tr>
                <tr>
                    <td colspan="3" class="border">SP2D Belanja UP</td>
                    <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                        {{ sp2dbelanja_up($kd_skpd) }}</td>
                    <td class="border1"></td>
                </tr>
                <tr>
                    <td colspan="3" class="border">SP2D Belanja GU</td>
                    <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                        {{ sp2dbelanja_gu($kd_skpd, $no_spp->no_spp, $tgl_spp->tgl_spp) }}</td>
                    <td class="border1"></td>
                </tr>
                <tr>
                    <td colspan="3" class="border">SP2D Belanja TU</td>
                    <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                        {{ sp2dbelanja_tu($kd_skpd, $no_spp->no_spp, $tgl_spp->tgl_spp) }}</td>
                    <td class="border1"></td>
                </tr>
                <tr>
                    <td colspan="3" class="border">SP2D Belanja LS GAJI</td>
                    <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                        {{ sp2dbelanja_lsgaji($kd_skpd, $no_spp->no_spp, $tgl_spp->tgl_spp) }}</td>
                    <td class="border1"></td>
                </tr>
                <tr>
                    <td colspan="3" class="border">SP2D Belanja LS Pengadaan Barang dan Jasa</td>
                    <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                        {{ sp2dbelanja_barang($kd_skpd, $no_spp->no_spp, $tgl_spp->tgl_spp) }}</td>
                    <td class="border1"></td>
                </tr>
                <tr>
                    <td colspan="3" class="border">SP2D Belanja LS Pihak Ketiga Lainnya</td>
                    <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                        {{ sp2dbelanja_pihak($kd_skpd, $no_spp->no_spp, $tgl_spp->tgl_spp) }}</td>
                    <td class="border1"></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right" class="border"><i>JUMLAH</i></td>
                    <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                        {{ total_belanja($kd_skpd, $no_spp->no_spp, $tgl_spp->tgl_spp) }}</td>
                    <td class="border1"></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right"><i>Sisa SPD yang telah diterbitkan, belum
                            dibelanjakan
                            (II-III) </i></td>
                    <td style="text-align: right;border-top:1px solid black;border-left:1px solid black">
                        {{ sisa_spd($total_spd, $kd_skpd, $no_spp->no_spp, $tgl_spp->tgl_spp) }}</td>
                    <td class="border1" style="text-align: right">(III)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="padding-top: 30px">
        <table style="width: 100%">
            <tr>
                {{-- <td style="text-align: center">MENGETAHUI :</td> --}}
                <td style="width: 50%"></td>
                <td style="margin: 2px 0px;text-align: center">
                    {{ $daerah->daerah }},
                    @if ($tanpa == 1)
                        ______________{{ $tahun_anggaran }}
                    @else
                        {{ tanggal($tgl_spm->tgl_spm) }}
                    @endif
                </td>
            </tr>
            <tr>
                {{-- <td style="padding-bottom: 50px;text-align: center">
                    {{ $pptk->jabatan }}
                </td> --}}
                <td style="width: 50%"></td>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                {{-- <td style="text-align: center">
                    <strong><u>{{ $pptk->nama }}</u></strong> <br>
                    {{ $pptk->pangkat }} <br>
                    NIP. {{ $pptk->nip }}
                </td> --}}
                <td style="width: 50%"></td>
                <td style="text-align: center">
                    <strong><u>{{ $bendahara->nama }}</u></strong> <br>
                    {{ $bendahara->pangkat }} <br>
                    NIP. {{ $bendahara->nip }}
                </td>
            </tr>
            {{-- <tr>
                <td style="text-align: center">{{ $pptk->pangkat }}</td>
                <td style="text-align: center">{{ $bendahara->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center">NIP. {{ $pptk->nip }}</td>
                <td style="text-align: center">NIP. {{ $bendahara->nip }}</td>
            </tr> --}}
        </table>
    </div>
</body>

</html>
