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

        #rincian>thead>tr>th {
            background-color: #CCCCCC;
            font-size: 14px
        }

        #rincian>tbody>tr>td {
            font-size: 14px
        }

        .rincian1>tbody>tr>td {
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
            <td align="left" style="font-size:16px"><strong>{{ $skpd->nm_skpd }}</strong></td>
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
            <td>
                @if ($beban == '4')
                    SURAT PERMINTAAN PEMBAYARAN LANGSUNG GAJI DAN TUNJANGAN <br>
                    (SPP - LS {{ strtoupper($lcbeban) }}) <br>
                @elseif ($beban == '5')
                    SURAT PERNYATAAN PENGAJUAN SPP - LS Pihak Ketiga Lainnya <br>
                    (SPP - {{ strtoupper($lcbeban) }}) <br>
                @else
                    (SPP - LS {{ strtoupper($lcbeban) }})
                    <br>
                @endif
                <b>Nomor : {{ $no_spp }}</b> <br>
                <b><u>RINCIAN</u></b>
            </td>
        </tr>
    </table>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian1">
        <tr>
            <td>
                RENCANA PENGGUNA ANGGARAN <br>
                @if ($beban == '4')
                    BULAN : {{ Str::upper(bulan($cari_data->bulan)) }}</u>
                @else
                    <h5></h5>
                @endif
            </td>
        </tr>
        <tr>
            <td style="height: 5px"></td>
        </tr>
    </table>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" border="1" id="rincian">
        <thead>
            <tr>
                <th style="text-align: center">No Urut</th>
                <th style="text-align: center">Kode Rekening</th>
                <th style="text-align: center">Uraian</th>
                <th style="text-align: center">Jumlah</th>
            </tr>
        </thead>
        @foreach ($result as $data)
            <tr>
                @if ($beban == '4')
                    @if ($data->urut == '1')
                        <td style="text-align: center"><b>{{ $data->urut }}</b></td>
                        <td><b>{{ $data->kode }}</b></td>
                        <td><b>{{ $data->nama }}</b></td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @elseif ($data->urut == '5')
                        <td></td>
                        <td>{{ Str::substr($data->kode, 0, 21) }}.{{ dotrek(STR::substr($data->kode, 22, 7)) }}
                        </td>
                        <td>{{ $data->nama }}</td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @else
                        <td></td>
                        <td>{{ Str::substr($data->kode, 0, 22) }}{{ dotrek(STR::substr($data->kode, 22, 7)) }}
                        </td>
                        <td>{{ $data->nama }}</td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @endif
                @elseif ($beban == '5')
                    @if ($data->urut == '1')
                        <td style="text-align: center"><b>{{ $data->urut }}</b></td>
                        <td><b>{{ $data->kode }}</b></td>
                        <td><b>{{ $data->nama }}</b></td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @elseif ($data->urut == '7')
                        <td></td>
                        <td>{{ Str::substr($data->kode, 0, 15) }}.{{ dotrek(STR::substr($data->kode, 16, 13)) }}
                        </td>
                        <td>{{ $data->nama }}</td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @else
                        <td></td>
                        <td>{{ Str::substr($data->kode, 0, 16) }}{{ dotrek(STR::substr($data->kode, 16, 12)) }}
                        </td>
                        <td>{{ $data->nama }}</td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @endif
                @elseif ($beban == '6')
                    @if ($data->urut == '1')
                        <td style="text-align: center"><b>{{ $data->urut }}</b></td>
                        <td><b>{{ $data->kode }}</b></td>
                        <td><b>{{ $data->nama }}</b></td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @elseif ($data->urut == '5')
                        <td></td>
                        <td>{{ Str::substr($data->kode, 0, 21) }}.{{ dotrek(STR::substr($data->kode, 22, 7)) }}
                        </td>
                        <td>{{ $data->nama }}</td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @else
                        <td></td>
                        <td>{{ Str::substr($data->kode, 0, 22) }}{{ dotrek(STR::substr($data->kode, 22, 7)) }}
                        </td>
                        <td>{{ $data->nama }}</td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @endif
                @endif
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td style="text-align:right"><b>JUMLAH</b></td>
            <td style="text-align: right">{{ rupiah($total) }}</td>
        </tr>
    </table>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian1">
        <tr>
            <td style="height: 5px"></td>
        </tr>
        <tr>
            <td>Terbilang : {{ ucwords(terbilang($total)) }}</td>
        </tr>
    </table>

    <br>
    <br>
    <br>
    {{-- tanda tangan --}}
    <table class="table rincian1" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        @if ($beban == '4')
            <tr>
                <td style="margin: 2px 0px;text-align: center;padding-left:500px">
                    {{ $daerah->daerah }},
                    @if ($tanpa == 1)
                        ______________{{ $tahun_anggaran }}
                    @else
                        {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}
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
            @if ($sub_kegiatan == '5.02.00.0.06.62')
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
                        {{ $cari_pptk->nip }}
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
                    <td style="text-align: center">{{ $cari_pptk->nip }}</td>
                    <td style="text-align: center">NIP. {{ $cari_bendahara->nip }}</td>
                </tr> --}}
            @endif
        @elseif ($beban == '6')
            @if ($sub_kegiatan == '5.02.00.0.06.62')
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
        @endif
    </table>
</body>

</html>
