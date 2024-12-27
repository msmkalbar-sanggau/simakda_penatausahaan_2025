<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Rincian</title>
    <style>
        .unborder {
            font-weight: normal
        }

        table,
        tr,
        td {
            border-collapse: collapse
        }

        .rincian>tbody>tr>td {
            font-size: 14px
        }
    </style>
</head>

<body>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td><b>PEMERINTAH KABUPATEN SANGGAU</b></td>
        </tr>
        <tr>
            <td><b>SURAT PERMINTAAN PEMBAYARAN GANTI UANG PERSEDIAAN (SPP-GU)</b></td>
        </tr>
        <tr>
            <td>Nomor : {{ $no_spp }}</td>
        </tr>
        <tr>
            <td>Tahun Anggaran : {{ tahun_anggaran() }}</td>
        </tr>
        <tr>
            <td style="height: 5px"></td>
        </tr>
        <tr>
            <td><b>RINCIAN RENCANA PENGGUNAAN</b></td>
        </tr>
    </table>

    <br>

    <table class="table table-bordered rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif"
        border="1">
        <tr>
            <td style="font-weight: bold;text-align:center">No</td>
            <td style="font-weight: bold;text-align:center">Kode Rekening</td>
            <td style="font-weight: bold;text-align:center">Uraian</td>
            <td style="font-weight: bold;text-align:center">Nilai Rupiah</td>
        </tr>
        @php
            $total = 0;
        @endphp
        @foreach ($data_kegiatan as $data)
            @php
                $total += $data->nilaisub;
            @endphp
            <tr>
                <td colspan="4">&nbsp;&nbsp;{{ nama_sub_kegiatan($data->kd_sub_kegiatan) }} /
                    {{ $data->nm_sub_kegiatan }}
                </td>
            </tr>
            @php
                $rincian = DB::select(
                    'SELECT a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan,kd_rek6,nm_rek6,sum(b.nilai)as nilaispp FROM trhspp a inner join trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd where a.no_spp=? AND b.kd_skpd=? and b.kd_sub_kegiatan=? GROUP BY a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan,kd_rek6,nm_rek6',
                    [$no_spp, $skpd->kd_skpd, $data->kd_sub_kegiatan],
                );
            @endphp
            @foreach ($rincian as $spp1)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $spp1->kd_rek6 }}</td>
                    <td>{{ $spp1->nm_rek6 }}</td>
                    <td style="text-align: right">{{ rupiah($spp1->nilaispp) }}</td>
                </tr>
            @endforeach
        @endforeach
        <tr>
            <td style="text-align:right" colspan="3">Total</td>
            <td style="text-align:right">{{ rupiah($total) }}</td>
        </tr>
    </table>

    <br>

    <table class="rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td>Terbilang: ## <span style="font-style:italic">({{ ucwords(terbilang($total)) }})</span> ##
            </td>
        </tr>
    </table>

    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
            @if ($sub_kegiatan == '5.02.00.0.06.62')
                <tr>
                    <td style="margin: 2px 0px;text-align: center;padding-left:150px">
                        Sanggau,
                        @if ($tanpa == 1)
                            ______________{{ tahun_anggaran() }}
                        @else
                            {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('D MMMM Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center;padding-left:150px">
                        {{ $bendahara->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:150px">
                        <b><u>{{ $bendahara->nama }}</u></b> <br>
                        {{ $bendahara->pangkat }} <br>
                        NIP. {{ $bendahara->nip }}
                    </td>
                </tr>
            @else
                <tr>
                    <td style="text-align: center;padding-left:80px">Mengetahui/Menyetujui:</td>
                    <td style="margin: 2px 0px;text-align: center;padding-left:150px">
                        Sanggau,
                        @if ($tanpa == 1)
                            ______________{{ tahun_anggaran() }}
                        @else
                            {{ \Carbon\Carbon::parse($spp->tgl_spp)->locale('id')->isoFormat('DD MMMM Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center;padding-left:80px;font-weight: bold">
                        {{ $pa_kpa->jabatan }}
                    </td>
                    <td style="padding-bottom: 50px;text-align: center;padding-left:150px;font-weight: bold">
                        {{ $bendahara->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;padding-left:80px">
                        <u>{{ $pa_kpa->nama }}</u> <br>
                        {{ $pa_kpa->pangkat }} <br>
                        NIP. {{ $pa_kpa->nip }}
                    </td>
                    <td style="text-align: center;padding-left:150px">
                        <u>{{ $bendahara->nama }}</u> <br>
                        {{ $bendahara->pangkat }} <br>
                        NIP. {{ $bendahara->nip }}
                    </td>
                </tr>
            @endif
        </table>
    </div>
</body>

</html>
