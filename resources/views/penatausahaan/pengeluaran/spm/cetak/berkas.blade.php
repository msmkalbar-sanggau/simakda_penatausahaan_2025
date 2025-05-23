<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BERKAS SPM</title>
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=swap"
        rel="stylesheet"> --}}
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .bordered,
        .bordered>tbody>tr>th,
        .bordered>tbody>tr>td {
            border: 1px solid black;
        }

        body {
            padding-top: 42px;
            font-size: 12px;
            font-family: 'Open Sans', sans-serif;
        }

        #potongan {
            border-top: none;
            border-right: none;
            border-left: none;
        }

        #potongan tr:first-child th {
            border-top: none;
        }

        #potongan tr th:first-child,
        #potongan tr td:first-child {
            border-left: none;
            text-align: center;
        }

        #potongan tr th:last-child,
        #potongan tr td:last-child {
            border-right: none;
        }

        #potongan tr:not(:last-child) td:nth-child(3) {
            text-align: right;
        }

        #potongan tr:last-child td:nth-child(2) {
            text-align: right;
        }
    </style>
</head>

<body>
    <table style="border-collapse:collapse;font-family: Open Sans" width="100%" align="center" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td style="font-size:18px;text-align:center" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td style="font-size:18px;text-align:center">
                <strong>
                    {{ strtoupper($skpd->nm_org) }}
                </strong>
            </td>
        </tr>
        @if (substr($skpd->kd_skpd, -4) !== '0000')
            <tr>
                <td style="font-size:18px;text-align:center">
                    <strong>
                        {{ strtoupper($skpd->nm_skpd) }}
                    </strong>
                </td>
            </tr>
        @endif
        <tr>
            <td style="font-size:18px;text-align:center"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
    </table>
    <br>
    <hr>

    <table class="bordered" style="font-size: 16px;width: 100%;font-family:Open Sans">
        <tbody>
            <tr>
                <td style="vertical-align: top; width: 50%;">
                    <div><b>Tahun Anggaran : {{ tahun_anggaran() }}</b></div>
                </td>
                <td style="vertical-align: top;text-align:center"><b>No. SPM : {{ $no_spm }}</b></td>
            </tr>
        </tbody>
    </table>
    <table class="bordered" style="border-top: none;font-size: 14px;width: 100%;font-family:Open Sans;">
        <tbody>
            <tr>
                <td style="border-top: none; width: 50%; vertical-align: top; padding-left: 0px;">
                    <table style="font-size: 14px;width: 100%;font-family:Open Sans;">
                        <tbody>
                            <tr>
                                <td colspan="3"><strong>KUASA BENDAHARA UMUM DAERAH</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3"><strong>{{ ucwords($daerah->kab_kota) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3">Supaya menerbitkan SP2D Kepada :</td>
                            </tr>
                            <tr>
                                <td style="width: 20%">SKPD</td>
                                <td>: </td>
                                <td><b>{{ $skpd->nm_org }}</b></td>
                            </tr>
                            @if (substr($skpd->kd_skpd, -4) !== '0000')
                                <tr>
                                    <td style="width: 20%">UNIT</td>
                                    <td>: </td>
                                    <td><b>{{ $skpd->nm_skpd }}</b></td>
                                </tr>
                            @endif
                            <tr>
                                <td>Bendahara/pihak lain</td>
                                <td>: </td>
                                <td>
                                    @if (($data_spm->jns_spp == 6 && $data_spm->jns_beban == 6) || $data_spm->jns_spp == 5)
                                        {{-- {{ $pihak_lain->pimpinan }} {{ $pihak_lain->nmrekan }} --}}
                                        {{ $bendahara->nm_rekening }}
                                    @else
                                        {{ $bendahara->nm_rekening }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>No. Rekening Bank</td>
                                <td>: </td>
                                <td>{{ empty($data_spm->no_rek) ? '' : $data_spm->no_rek }}</td>
                            </tr>
                            <tr>
                                <td>Nama Bank</td>
                                <td>: </td>
                                <td>{{ empty($data_spm->bank) ? '' : nama_bank($data_spm->bank) }}</td>
                            </tr>
                            <tr>
                                <td>NPWP</td>
                                <td>: </td>
                                <td>
                                    {{ $wp->npwp == '000000000000000' || empty($wp->npwp) ? '-' : npwp($wp->npwp) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Dasar Pembayaran</td>
                                <td>: </td>
                                <td>{{ $data_spm->no_spd }} , {{ tanggal($data_spm->tgl_spd) }}</td>
                            </tr>
                            <tr style="border-top: 1px solid black; border-bottom: 1px solid black;">
                                <td>Untuk Keperluan</td>
                                <td>: </td>
                                <td style="text-align: justify">{{ $data_spm->keperluan }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div><strong>Pembebanan pada kode rekening:</strong></div>
                    <table style="font-size: 14px;width: 100%;font-family:Open Sans;"">
                        <thead>
                            <tr>
                                <th
                                    style="border-top: 1px solid black;border-bottom: 1px solid black;border-right:1px solid black;width:10%">
                                    Kode
                                    Kegiatan</th>
                                <th
                                    style="border-top: 1px solid black;border-bottom: 1px solid black;border-right:1px solid black;width:40%">
                                    Uraian
                                </th>
                                <th style="border-top: 1px solid black;border-bottom: 1px solid black;width:20%"
                                    colspan="2">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (in_array($beban, ['1', '2']))
                                <tr>
                                    <td>
                                        {{ $kd_skpd }}</td>
                                    <td>
                                        {{ $data_spm->nm_skpd }}</td>
                                    <td style="border-top: 1px solid black">Rp</td>
                                    <td style="border-top: 1px solid black;text-align:right">
                                        {{ rupiah($beban1->nilai) }}</td>
                                </tr>
                            @else
                                @if ($total_beban <= $baris)
                                    @foreach ($data_beban as $rincian_beban)
                                        <tr>
                                            <td style="vertical-align: text-top">
                                                {{ $rincian_beban->kode }}</td>
                                            <td style="vertical-align: text-top;word-wrap:break-word">
                                                {{ $rincian_beban->nama }}</td>
                                            <td style="vertical-align: text-top">Rp</td>
                                            <td style="text-align:right;vertical-align: text-top">
                                                {{ rupiah($rincian_beban->nilai) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach ($data_beban as $rincian_beban)
                                        <tr>
                                            <td style="vertical-align: text-top">
                                                {{ $rincian_beban->kode }}</td>
                                            <td style="vertical-align: text-top">
                                                {{ $rincian_beban->nama }}</td>
                                            <td style="vertical-align: text-top">Rp</td>
                                            <td style="text-align:right;vertical-align: text-top">
                                                {{ rupiah($rincian_beban->nilai) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endif
                            <tr style="">
                                <td style="text-align: center;border-top: 1px solid black;border-right:1px solid black"
                                    colspan="2">Jumlah</td>
                                <td style="border-top: 1px solid black">Rp</td>
                                <td style="border-top: 1px solid black;text-align:right">{{ rupiah($beban1->nilai) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="height: 15px;border-top: 1px solid black;border-bottom: 1px solid black"
                                    colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="vertical-align:text-top">Jumlah SPP Yang Diminta</td>
                                <td>Rp</td>
                                <td style="text-align: right">{{ rupiah($beban1->nilai) }}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: text-top">(terbilang)</td>
                                <td colspan="3"><i>{{ terbilang($beban1->nilai) }}</i></td>
                            </tr>
                            <tr style="border-top: 1px solid black">
                                <td style="height:20px">Nomor dan Tanggal SPP :</td>
                                <td colspan="3">{{ $data_spm->no_spp }} dan {{ tanggal($data_spm->tgl_spp) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="border-top: none; vertical-align: top; padding: 0px;">
                    <table id="potongan" class="bordered" style="font-size: 14px;width: 100%;font-family:Open Sans;">
                        <tbody>
                            <tr>
                                <td colspan="4" style="border-top:none;text-align: left"><strong>Potongan - Potongan
                                        :</strong></td>
                            </tr>
                            <tr>
                                <th style="width: 1%">No.</th>
                                <th>Uraian (No. Rekening)</th>
                                <th>Jumlah</th>
                                <th style="10%">Keterangan</th>
                            </tr>
                            @foreach ($data_potongan as $potongan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucwords($potongan->nm_pot) }}</td>
                                    <td>{{ rupiah($potongan->nilai) }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                            <tr style="font-weight: bold;">
                                <td></td>
                                <td>Jumlah Potongan</td>
                                <td style="text-align: right;">{{ rupiah($total_potongan) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4">Informasi :<i> (tidak mengurangi jumlah pembayaran SPM)</i></td>
                            </tr>
                            <tr>
                                <th>No.</th>
                                <th>Uraian (No. Rekening)</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                            @foreach ($data_potongan1 as $potongan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucwords($potongan->nm_rek6) }}</td>
                                    <td>{{ rupiah($potongan->nilai) }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                            <tr style="font-weight: bold;">
                                <td></td>
                                <td>Jumlah Potongan</td>
                                <td style="text-align: right;">{{ rupiah($total_potongan1) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" style="height:15px"></td>
                            </tr>
                            <tr>
                                <td style="text-align: left" colspan="4"><strong>SPM Yang Dibayarkan</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: left">Jumlah Yang Diminta</td>
                                <td colspan="2">Rp. {{ rupiah($beban1->nilai) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: left">Jumlah Potongan</td>
                                <td colspan="2">Rp. {{ rupiah($total_potongan) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: left">Jumlah Yang Dibayarkan</td>
                                <td colspan="2">Rp.
                                    {{ rupiah($beban1->nilai - $total_potongan) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" style="height:20px;text-align:left">Uang Sejumlah :
                                    <i>{{ terbilang($beban1->nilai - $total_potongan) }}</i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="text-align: center;vertical-align:text-bottom ;margin-top: 80px;">
                        <div style="font-size: 14px">{{ $daerah->daerah }},
                            @if ($tanpa == 1)
                                ______________{{ $tahun_anggaran }}
                            @else
                                {{ tanggal($data_spm->tgl_spm) }}
                            @endif
                        </div>
                        <div style="font-size:14px">{{ $pa_kpa->jabatan }}</div>
                        <!-- <div style="font-size:14px">Pengguna Anggaran</div> -->
                        <div style="height: 50px;"></div>
                        <div><b><u></u></b></div>
                        <div style="font-size:14px"><u><b>{{ $pa_kpa->nama }}</b></u></div>
                        <div style="font-size:14px">{{ $pa_kpa->pangkat }}</div>
                        <div style="font-size:14px">NIP. {{ $pa_kpa->nip }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;" colspan="2"><i>SPM ini sah apabila telah ditandatangani dan
                        distempel
                        oleh Kepala SKPD</i></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
