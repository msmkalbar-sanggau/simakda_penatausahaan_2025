<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Rincian</title>
    <style>
        table,
        th,
        td {
            border-collapse: collapse;
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
            <td align="left" style="font-size:16px">
                <strong>
                    @if ($beban == '4')
                        {{ $skpd->nm_skpd }}
                    @elseif($beban == '5')
                        {{ $skpd->nm_skpd }}
                    @elseif ($beban == '6')
                        SKPD {{ $skpd->nm_skpd }}
                    @endif
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
            <td style="font-size:16px">
                @if ($beban == '4')
                    SURAT PERMINTAAN PEMBAYARAN LANGSUNG GAJI DAN TUNJANGAN <br>
                    (SPP - {{ strtoupper($lcbeban) }}) <br>
                @elseif ($beban == '5')
                    SURAT PERNYATAAN PENGAJUAN SPP - {{ strtoupper($lcbeban) }} <br>
                    (SPP - {{ strtoupper($lcbeban) }}) <br>
                @else
                    SURAT PERMINTAAN PEMBAYARAN LANGSUNG BARANG DAN JASA <br>
                    (SPP - {{ strtoupper($lcbeban) }}) <br>
                @endif
                <b>Nomor : {{ $no_spp }}</b> <br>
                <b><u>RINGKASAN</u></b>
            </td>
        </tr>
        <tr>
            <td style="height: 20px"></td>
        </tr>
    </table>

    @if ($beban == '4')
        <table class="table table-striped rincian"
            style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" border="1">
            <tr>
                <td colspan="4" style="text-align: center">RINGKASAN DPA/DPPA/DPPAL-OPD</td>
            </tr>
            <tr>
                <td colspan="3">Jumlah dana DPA/DPPA/DPPAL-OPD</td>
                <td style="text-align: right">{{ rupiah($data_nilai->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center">RINGKASAN SPD</td>
            </tr>
            <tr>
                <td style="text-align: center">No. Urut</td>
                <td style="text-align: center">Nomor SPD</td>
                <td style="text-align: center">Tanggal SPD</td>
                <td style="text-align: center">Jumlah Dana</td>
            </tr>
            @foreach ($result as $data)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $data->no_spd }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tgl_spd)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="font-style: italic;text-align:right">JUMLAH</td>
                <td style="text-align: right">{{ rupiah($totalspd) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="font-style: italic;text-align:right">Sisa dana yang belum di SPD-kan</td>
                <td style="text-align: right">{{ rupiah($blmspd) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="height: 20px"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center">RINGKASAN BELANJA</td>
            </tr>
            <tr>
                <td colspan="3">Belanja UP/GU</td>
                <td style="text-align: right">{{ rupiah($nilai4->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja TU</td>
                <td style="text-align: right">{{ rupiah($nilai5->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja LS Pembayaran Gaji dan Tunjangan</td>
                <td style="text-align: right">{{ rupiah($nilai1->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja LS Pengadaan Barang dan Jasa</td>
                <td style="text-align: right">{{ rupiah($nilai3->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja LS Pihak Ketiga Lainnya</td>
                <td style="text-align: right">{{ rupiah($nilai2->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;font-style:italic">JUMLAH</td>
                <td style="text-align: right">{{ rupiah($totalbelanja) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="font-style:italic">Sisa SPD yang telah, belum dibelanjakan</td>
                <td style="text-align: right">{{ rupiah($sisaspp) }}</td>
            </tr>
        </table>
    @elseif ($beban == '5')
        @if ($jenis == '1' || $jenis == '2')
            <table class="table table-striped rincian"
                style="width: 100%;border:1px black solid;font-family:'Open Sans', Helvetica, Arial,sans-serif">
                <tr>
                    <td colspan="3" style="text-align: center;border-bottom:1px black solid">RINGKASAN KEGIATAN
                    </td>
                </tr>
                <tr>
                    <td style="width: 400px">1. Program</td>
                    <td>:</td>
                    <td>{{ $data_spp->nm_program }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">2. Kegiatan</td>
                    <td>:</td>
                    <td>{{ $data_spp->nm_sub_kegiatan }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">3. Nomor dan Tanggal DPA/DPPA/DPPAL-OPD</td>
                    <td>:</td>
                    <td>{{ $data_dpa->no_dpa }} -
                        {{ \Carbon\Carbon::parse($data_dpa->tgl_dpa)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">4. Nama Perusahaan</td>
                    <td>:</td>
                    <td>{{ $data_spp->nmrekan }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">5. Bentuk Perusahaan</td>
                    <td>:</td>
                    <td>
                        {{ substr($data_spp->nmrekan, 0, 2) == 'CV' || substr($data_spp->nmrekan, 0, 2) == 'PT' ? substr($data_spp->nmrekan, 0, 2) : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 400px">6. Alamat Perusahaan</td>
                    <td>:</td>
                    <td>{{ $data_spp->alamat }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">7. Nama Pimpinan Perusahaan</td>
                    <td>:</td>
                    <td>{{ $data_spp->pimpinan }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">8. Nama dan Nomor Rekening Bank</td>
                    <td>:</td>
                    <td>{{ $data_spp->nama_bank }} - {{ $data_spp->no_rek }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">9. Nomor Kontrak</td>
                    <td>:</td>
                    <td>{{ $data_spp->kontrak }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">10. Kegiatan Lanjutan</td>
                    <td>:</td>
                    <td>{{ $data_spp->lanjut == '1' ? 'Iya' : 'Bukan' }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">11. Waktu Pelaksanaan Kegiatan</td>
                    <td>:</td>
                    <td>
                        {{ isset($data_spp->tgl_mulai) ? tanggal($data_spp->tgl_mulai) : '-' }} s/d
                        {{ isset($data_spp->tgl_akhir) ? tanggal($data_spp->tgl_akhir) : '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 400px">12. Deskripsi Pekerjaan</td>
                    <td>:</td>
                    <td>
                        <pre>{{ $data_spp->keperluan }}</pre>
                    </td>
                </tr>
            </table>
        @endif
        <table class="table table-striped rincian"
            style="width: 100%;font-family:'Open Sans', Helvetica, Arial,sans-serif" border="1">
            <tr>
                <td colspan="4" style="text-align: center">RINGKASAN DPA/DPPA/DPPAL-OPD</td>
            </tr>
            <tr>
                <td colspan="3">Jumlah dana DPA/DPPA/DPPAL-OPD</td>
                <td style="text-align: right">{{ rupiah($data_nilai->nilai) }} (I)</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center">RINGKASAN SPD</td>
            </tr>
            <tr>
                <td style="text-align: center">No. Urut</td>
                <td style="text-align: center">Nomor SPD</td>
                <td style="text-align: center">Tanggal SPD</td>
                <td style="text-align: center">Jumlah Dana</td>
            </tr>
            @foreach ($result as $data)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $data->no_spd }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tgl_spd)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="font-style: italic;text-align:right">JUMLAH</td>
                <td style="text-align: right">{{ rupiah($totalspd) }} (II)</td>
            </tr>
            <tr>
                <td colspan="3" style="font-style: italic;text-align:right">Sisa dana yang belum di SPD-kan
                    (I-II)
                </td>
                <td style="text-align: right">{{ rupiah($blmspd) }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center">RINGKASAN BELANJA</td>
            </tr>
            <tr>
                <td colspan="3">Belanja UP/GU</td>
                <td style="text-align: right">{{ rupiah($nilai3->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja TU</td>
                <td style="text-align: right">{{ rupiah($nilai4->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja LS Gaji</td>
                <td style="text-align: right">{{ rupiah($nilai5->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja LS Pengadaan Barang dan Jasa</td>
                <td style="text-align: right">{{ rupiah($nilai1->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja LS Pihak Ketiga Lainnya</td>
                <td style="text-align: right">{{ rupiah($nilai2->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;font-style:italic">JUMLAH</td>
                <td style="text-align: right">{{ rupiah($totalbelanja) }} (III)</td>
            </tr>
            <tr>
                <td colspan="3" style="font-style:italic;text-align:right">Sisa SPD yang telah, belum
                    dibelanjakan (II-III)
                </td>
                <td style="text-align: right">{{ rupiah($sisaspp) }}</td>
            </tr>
        </table>
    @elseif ($beban == '6')
        @if ($jenis == '6')
            <table class="table table-striped"
                style="width: 100%;border:1px black solid;font-family:'Open Sans', Helvetica, Arial,sans-serif">
                <tr>
                    <td colspan="3" style="text-align: center;border-bottom:1px black solid">RINGKASAN KEGIATAN
                    </td>
                </tr>
                <tr>
                    <td style="width: 400px">1. Program</td>
                    <td>:</td>
                    <td>{{ $data_spp->nm_program }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">2. Kegiatan</td>
                    <td>:</td>
                    <td>{{ $data_spp->nm_sub_kegiatan }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">3. Nomor dan Tanggal DPA/DPPA/DPPAL-OPD</td>
                    <td>:</td>
                    <td>{{ $data_dpa->no_dpa }} -
                        {{ \Carbon\Carbon::parse($data_dpa->tgl_dpa)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">4. Nama Perusahaan</td>
                    <td>:</td>
                    <td>{{ $data_spp->nmrekan }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">5. Bentuk Perusahaan</td>
                    <td>:</td>
                    <td>
                        {{ substr($data_spp->nmrekan, 0, 2) == 'CV' || substr($data_spp->nmrekan, 0, 2) == 'PT' ? substr($data_spp->nmrekan, 0, 2) : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 400px">6. Alamat Perusahaan</td>
                    <td>:</td>
                    <td>{{ $data_spp->alamat }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">7. Nama Pimpinan Perusahaan</td>
                    <td>:</td>
                    <td>{{ $data_spp->pimpinan }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">8. Nama dan Nomor Rekening Bank</td>
                    <td>:</td>
                    <td>{{ $data_spp->nama_bank }} - {{ $data_spp->no_rek }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">9. Nomor Kontrak</td>
                    <td>:</td>
                    <td>{{ $data_spp->kontrak }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">10. Kegiatan Lanjutan</td>
                    <td>:</td>
                    <td>{{ $data_spp->lanjut == '1' ? 'Iya' : 'Bukan' }}</td>
                </tr>
                <tr>
                    <td style="width: 400px">11. Waktu Pelaksanaan Kegiatan</td>
                    <td>:</td>
                    <td>
                        {{ isset($data_spp->tgl_mulai) ? tanggal($data_spp->tgl_mulai) : '' }} s/d
                        {{ isset($data_spp->tgl_akhir) ? tanggal($data_spp->tgl_akhir) : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 400px">12. Deskripsi Pekerjaan</td>
                    <td>:</td>
                    <td>
                        <pre>{{ $data_spp->keperluan }}</pre>
                    </td>
                </tr>
            </table>
        @endif
        <table class="table table-striped rincian"
            style="width: 100%;font-family:'Open Sans', Helvetica, Arial,sans-serif" border="1">
            <tr>
                <td colspan="4" style="text-align: center">RINGKASAN DPA/DPPA/DPPAL-OPD</td>
            </tr>
            <tr>
                <td colspan="3">Jumlah dana DPA/DPPA/DPPAL-OPD</td>
                <td style="text-align: right">{{ rupiah($data_nilai->nilai) }} (I)</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center">RINGKASAN SPD</td>
            </tr>
            <tr>
                <td style="text-align: center">No. Urut</td>
                <td style="text-align: center">Nomor SPD</td>
                <td style="text-align: center">Tanggal SPD</td>
                <td style="text-align: center">Jumlah Dana</td>
            </tr>
            @foreach ($result as $data)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $data->no_spd }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tgl_spd)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="font-style: italic;text-align:right">JUMLAH</td>
                <td style="text-align: right">{{ rupiah($totalspd) }} (II)</td>
            </tr>
            <tr>
                <td colspan="3" style="font-style: italic;text-align:right">Sisa dana yang belum di SPD-kan
                    (I-II)
                </td>
                <td style="text-align: right">{{ rupiah($blmspd) }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center">RINGKASAN BELANJA</td>
            </tr>
            <tr>
                <td colspan="3">Belanja UP/GU</td>
                <td style="text-align: right">{{ rupiah($nilai2->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja TU</td>
                <td style="text-align: right">{{ rupiah($nilai3->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja LS Gaji</td>
                <td style="text-align: right">{{ rupiah($nilai5->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja LS Pengadaan Barang dan Jasa</td>
                <td style="text-align: right">{{ rupiah($nilai1->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3">Belanja LS Pihak Ketiga Lainnya</td>
                <td style="text-align: right">{{ rupiah($nilai4->nilai) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;font-style:italic">JUMLAH</td>
                <td style="text-align: right">{{ rupiah($totalbelanja) }} (III)</td>
            </tr>
            <tr>
                <td colspan="3" style="font-style:italic;text-align:right">Sisa SPD yang telah, belum
                    dibelanjakan (II-III)
                </td>
                <td style="text-align: right">{{ rupiah($sisaspp) }}</td>
            </tr>
        </table>
    @endif
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width:100%;font-family:'Open Sans', Helvetica, Arial,sans-serif">
            @if ($beban == '4')
                <tr>
                    <td style="margin: 2px 0px;text-align: center;padding-left:500px">
                        {{ $daerah->daerah }},
                        @if ($tanpa == 1)
                            ______________{{ $tahun_anggaran }}
                        @else
                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('DD MMMM Y') }}
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
            @elseif ($beban == '5')
                <tr>
                    <td style="text-align: center">MENGETAHUI :</td>
                    <td style="margin: 2px 0px;text-align: center">
                        {{ $daerah->daerah }},
                        @if ($tanpa == 1)
                            ______________{{ $tahun_anggaran }}
                        @else
                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center">
                        {{ $cari_pptk->jabatan }}
                    </td>
                    <td style="padding-bottom: 50px;text-align: center">
                        {{ $cari_bendahara->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center">
                        <b><u>{{ $cari_pptk->nama }}</u></b> <br>
                        {{ $cari_pptk->pangkat }} <br>
                        NIP. {{ $cari_pptk->nip }}
                    </td>
                    <td style="text-align: center">
                        <b><u>{{ $cari_bendahara->nama }}</u></b> <br>
                        {{ $cari_bendahara->pangkat }} <br>
                        NIP. {{ $cari_bendahara->nip }}
                    </td>
                </tr>
                {{-- <tr>
                    <td style="text-align: center">{{ $cari_pptk->pangkat }}</td>
                    <td style="text-align: center">{{ $cari_bendahara->pangkat }}</td>
                </tr>
                <tr>
                    <td style="text-align: center">NIP. {{ $cari_pptk->nip }}</td>
                    <td style="text-align: center">NIP. {{ $cari_bendahara->nip }}</td>
                </tr> --}}
            @elseif ($beban == '6')
                @if ($jumlah_spp > 0)
                    <tr>
                        <td style="margin: 2px 0px;text-align: center;padding-left:300px">
                            {{ $daerah->daerah }},
                            @if ($tanpa == 1)
                                ______________{{ $tahun_anggaran }}
                            @else
                                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 50px;text-align: center;padding-left:300px">
                            {{ $cari_bendahara->jabatan }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding-left:300px">
                            <b><u>{{ $cari_bendahara->nama }}</u></b> <br>
                            {{ $cari_bendahara->pangkat }} <br>
                            NIP. {{ $cari_bendahara->nip }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <td style="text-align: center;padding-left:300px">{{ $cari_bendahara->pangkat }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding-left:300px">NIP. {{ $cari_bendahara->nip }}</td>
                    </tr> --}}
                @else
                    <tr>
                        <td style="text-align: center">MENGETAHUI :</td>
                        <td style="margin: 2px 0px;text-align: center">
                            {{ $daerah->daerah }},
                            @if ($tanpa == 1)
                                ______________{{ $tahun_anggaran }}
                            @else
                                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 50px;text-align: center">
                            {{ $cari_pptk->jabatan }}
                        </td>
                        <td style="padding-bottom: 50px;text-align: center">
                            {{ $cari_bendahara->jabatan }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center">
                            <b><u>{{ $cari_pptk->nama }}</u></b> <br>
                            {{ $cari_pptk->pangkat }} <br>
                            NIP. {{ $cari_pptk->nip }}
                        </td>
                        <td style="text-align: center">
                            <b><u>{{ $cari_bendahara->nama }}</u></b> <br>
                            {{ $cari_bendahara->pangkat }} <br>
                            NIP. {{ $cari_bendahara->nip }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <td style="text-align: center">{{ $cari_pptk->pangkat }}</td>
                        <td style="text-align: center">{{ $cari_bendahara->pangkat }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center">NIP. {{ $cari_pptk->nip }}</td>
                        <td style="text-align: center">NIP. {{ $cari_bendahara->nip }}</td>
                    </tr> --}}
                @endif
            @endif
        </table>
    </div>
</body>

</html>
