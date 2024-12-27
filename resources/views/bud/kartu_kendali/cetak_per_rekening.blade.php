<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CETAK PER REKENING</title>
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
            border-left: hidden
        }

        .bawah {
            border-bottom: hidden
        }

        .angka {
            text-align: right
        }
    </style>
</head>

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75"
                    height="100" />
            </td>
            <td align="left" style="font-size:14px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td style="text-align: left"><b> {{ strtoupper($header->nm_pemda) }}</b></td>
        </tr>
        <tr>
            <td style="text-align: left"><b>{{ $skpd->nm_skpd }}</b></td>
        </tr>
        <tr>
            <td style="text-align: left;padding-bottom:20px"><b>TAHUN ANGGARAN {{ tahun_anggaran() }}</b>
            </td>
        </tr>
    </table>

    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px;margin-top:20px" width="100%"
        align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center;font-size:20px"><b>KARTU KENDALI KEGIATAN</b></td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;font-family: Open Sans">
        <tr>
            <td><b>Nama Kegiatan</b></td>
            <td>:</td>
            <td><b>{{ $kegiatan->kd_kegiatan }} - {{ $kegiatan->nm_kegiatan }}</b></td>
        </tr>
        <tr>
            <td><b>Nama Sub Kegiatan</b></td>
            <td>:</td>
            <td><b>{{ $sub_kegiatan->kd_sub_kegiatan }} - {{ $sub_kegiatan->nm_sub_kegiatan }}</b></td>
        </tr>
        <tr>
            <td><b>Nama Rekening</b></td>
            <td>:</td>
            <td><b>{{ $sub_kegiatan->kd_sub_kegiatan }}.{{ $rekening->kd_rek6 }} - {{ $rekening->nm_rek6 }}</b></td>
        </tr>
        <tr>
            <td><b>Anggaran Penyusunan</b></td>
            <td>:</td>
            <td><b>{{ rupiah($nilai_ang->nilai) }}</b></td>
        </tr>
        <tr>
            <td><b>Anggaran Perubahan</b></td>
            <td>:</td>
            <td><b>{{ $jns_ang == 'M' ? rupiah(0) : rupiah($nilai_ang->nilai_ubah) }}</b></td>
        </tr>
        <tr>
            <td><b>Periode</b></td>
            <td>:</td>
            <td><b>{{ tanggal($periode_awal) }} s/d {{ tanggal($periode_akhir) }}</b></td>
        </tr>
    </table>
    <br>
    <table style="width: 100%" border="1" id="rincian">
        <thead>
            <tr>
                <th rowspan="2">No.<br>Urut</th>
                <th rowspan="2">Kode<br>Rekening</th>
                <th rowspan="2" style="width: 8%">Tanggal</th>
                <th rowspan="2">Uraian</th>
                <th colspan="3">Realisasi Kegiatan (BELANJA) <br>(Rp)</th>
                <th rowspan="2">Koreksi / CP</th>
                <th rowspan="2">Sisa Pagu Anggaran<br>(Rp.)</th>
            </tr>
            <tr>
                <th>UP/GU</th>
                <th>TU</th>
                <th>LS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_up = 0;
                $total_tu = 0;
                $total_ls = 0;
                $total_cp = 0;
                $total_sisa = 0;
            @endphp
            @foreach ($rincian as $detail)
                @php
                    $total_up += $detail->up;
                    $total_tu += $detail->tu;
                    $total_ls += $detail->ls;
                    $total_cp += $detail->cp;
                @endphp
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $detail->kd_rek6 }}</td>
                    <td style="text-align: center">{{ $detail->tgl_sp2d }}</td>
                    <td>{{ $detail->uraian }}</td>
                    <td style="text-align: right">{{ rupiah($detail->up) }}</td>
                    <td style="text-align: right">{{ rupiah($detail->tu) }}</td>
                    <td style="text-align: right">{{ rupiah($detail->ls) }}</td>
                    <td style="text-align: right">
                        {{ $detail->cp < 0 ? '(' . rupiah($detail->cp) . ')' : rupiah($detail->cp) }}
                    </td>
                    <td style="text-align: right">
                        {{ rupiah($nilai_ang->nilai_ubah - $total_up - $total_tu - $total_ls - $total_cp) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: center"><b>TOTAL</b></td>
                <td style="text-align: right"><b>{{ rupiah($total_up) }}</b></td>
                <td style="text-align: right"><b>{{ rupiah($total_tu) }}</b></td>
                <td style="text-align: right"><b>{{ rupiah($total_ls) }}</b></td>
                <td style="text-align: right">
                    <b>{{ $total_cp < 0 ? '(' . rupiah($total_cp) . ')' : rupiah($total_cp) }}</b>
                </td>
                <td style="text-align: right">
                    <b>{{ rupiah($nilai_ang->nilai_ubah - $total_up - $total_tu - $total_ls - $total_cp) }}</b>
                </td>
            </tr>
        </tbody>
    </table>
    {{-- @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:800px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="padding-bottom: 50px;text-align: center">
                        Kuasa Bendahara Umum Daerah
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center"><b><u>{{ $tanda_tangan->nama }}</u></b></td>
    </tr>
    <tr>
        <td style="text-align: center">{{ $tanda_tangan->pangkat }}</td>
    </tr>
    <tr>
        <td style="text-align: center">NIP. {{ $tanda_tangan->nip }}</td>
    </tr>
    </table>
    </div>
    @endif --}}
</body>

</html>
