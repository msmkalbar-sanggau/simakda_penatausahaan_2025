<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Pengantar</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #rincian>tbody>tr>td {
            vertical-align: top
        }

        .kanan {
            border-right: 1px solid black
        }

        .kiri {
            border-left: 1px solid black
        }

        .bawah {
            border-bottom: 1px solid black
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
                    {{ $nama_skpd->nm_skpd }}
                </strong>
            </td>
        </tr>
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
                <td style="font-size:16px"><strong>SURAT PENYAMPAIAN BERKAS SPM</strong></td>
            </tr>
        </table>
    </div>
    <br>
    <div>
        <table class="table table-bordered" width="100%" align="center" border="1" cellspacing="1">
            <thead>
                <tr>
                    <th bgcolor="#CCCCCC" align="center" width="5%"
                        style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;" hidden>Pilih
                    </th>
                    <th bgcolor="#CCCCCC" align="center" width="5%"
                        style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;">No Urut</th>
                    <th bgcolor="#CCCCCC" align="center" width="10%"
                        style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;">No.SPM</th>
                    <th bgcolor="#CCCCCC" align="center" width="8%"
                        style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;">Tgl SPM</th>
                    <th bgcolor="#CCCCCC" align="center" width="10%"
                        style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;">Jenis SPP</th>
                    <th bgcolor="#CCCCCC" align="center" width="10%"
                        style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;">Jenis Beban
                    </th>
                    <th bgcolor="#CCCCCC" align="center" width="15%"
                        style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;">Keperluan</th>
                    <th bgcolor="#CCCCCC" align="center" width="10%"
                        style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data_spm as $data)
                    <tr>
                        <td align="center" hidden
                            style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black">
                            <input type="checkbox" name="spm_checkbox" value="{{ $data->no_spm }}">
                        </td>
                        <td align="center"
                            style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black">
                            {{ $loop->iteration }}</td>
                        <td align="center"
                            style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black">
                            {{ $data->no_spm }}</td>
                        <td align="center"
                            style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black">
                            {{ tanggal($data->tgl_spm) }}</td>
                        <td align="center"
                            style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black">
                            @switch($data->jns_spp)
                                @case(1)
                                    UP
                                @break

                                @case(2)
                                    GU
                                @break

                                @case(3)
                                    TU
                                @break

                                @case(4)
                                    LS GAJI
                                @break

                                @case(5)
                                    LS Pihak Ketiga Lainnya
                                @break

                                @case(6)
                                    LS Barang jasa
                                @break

                                @case(7)
                                    GU NIHIL
                                @break

                                @default
                                    Tidak ada jenis SPP
                            @endswitch
                        </td>
                        <td align="center"
                            style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black">
                            @if ($data->jns_spp == 3)
                                @if ($data->jenis_beban == 1)
                                    TU
                                @else
                                    Jenis Beban Tidak Ada
                                @endif
                            @elseif($data->jns_spp == 4)
                                @switch($data->jenis_beban)
                                    @case(1)
                                        Gaji & Tunjangan
                                    @break

                                    @case(7)
                                        Tambah/Kekurangan Gaji Tunjangan
                                    @break

                                    @case(8)
                                        Tunjangan Transport
                                    @break

                                    @case(9)
                                        Tunjangan Lainnya
                                    @break

                                    @default
                                        Jenis Beban Tidak Ada
                                @endswitch
                            @elseif($data->jns_spp == 5)
                                @switch($data->jenis_beban)
                                    @case(1)
                                        Hibah berupa uang
                                    @break

                                    @case(2)
                                        Bantuan Sosial berupa uang
                                    @break

                                    @case(3)
                                        Bantuan Keuangan
                                    @break

                                    @case(4)
                                        Subsidi
                                    @break

                                    @case(5)
                                        Bagi Hasil
                                    @break

                                    @case(6)
                                        Belanja Tidak Terduga
                                    @break

                                    @case(7)
                                        Pembayaran kewajiban pemda atas putusan pengadilan, dan rekomendasi APIP dan/atau
                                        rekomendasi BPK
                                    @break

                                    @case(8)
                                        Pengeluaran Pembiayaan
                                    @break

                                    @case(9)
                                        Barang yang diserahkan ke masyarakat
                                    @break

                                    @default
                                        Jenis Beban Tidak Ada
                                @endswitch
                            @elseif($data->jns_spp == 6)
                                @switch($data->jenis_beban)
                                    @case(1)
                                        LS Rutin (PNS)
                                    @break

                                    @case(2)
                                        LS Rutin (Non PNS)
                                    @break

                                    @case(3)
                                        LS Pihak Ketiga
                                    @break

                                    @default
                                        Jenis Beban Tidak Ada
                                @endswitch
                            @else
                                Jenis Beban Tidak Dikenal
                            @endif
                        </td>

                        <td align="left"
                            style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black">
                            {{ $data->keperluan }}</td>
                        <td align="right"
                            style="font-size:12px;border-bottom:solid 1px black;border-top:solid 1px black">
                            {{ rupiah($data->nilai) }}</td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" align="center">Tidak ada SPM untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <br>
        <br>
        {{-- tanda tangan --}}
        <div style="padding-top:20px">
            <table class="table" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;" class="rincian">
                <tr>
                    <td style="margin: 2px 0px;text-align: center;padding-left:300px">
                        {{ $daerah->daerah }},
                        {{ tanggal($tgl_ttd) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center;padding-left:300px">
                        {{ $pptk->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:300px">
                        <strong><u>{{ $pptk->nama }}</u></strong> <br>
                        {{ $pptk->pangkat }} <br>
                        NIP. {{ $pptk->nip }}
                    </td>
                </tr>
            </table>
            <table class="table" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;" class="rincian"
                border="0">
                <tr>
                    <td>Yang menerima</td>
                    <td style="padding-right:600px">:</td>
                </tr>
                <tr>
                    <td>Tanggal diterima</td>
                    <td>:</td>
                </tr>
                <tr>
                    <td>TTD Penerima</td>
                    <td>:</td>
                </tr>
                <tr>
                    <td><br></td>
                </tr>
                <tr>
                    <td>Yang menyampaikan (ASN / Tekon / Rekanan) </td>
                    <td>:</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                </tr>
                <tr>
                    <td>No Telepon</td>
                    <td>:</td>
                </tr>
                <tr>
                    <td>TTD</td>
                    <td>:</td>
                </tr>
            </table>
        </div>


    </body>

    </html>
