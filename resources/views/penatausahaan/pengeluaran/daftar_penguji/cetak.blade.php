<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        #rincian {
            border-collapse: collapse
        }

        #judul,
        #rincian,
        #tanda {
            font-size: 14px;
            font-family: Tahoma;
        }

        table,
        tr,
        td,
        th {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div style="text-align: center;margin-top:20px">
        <h3 style="margin: 2px 0px"><strong>DAFTAR PENGUJI / PENGANTAR</strong></h3>
        <h3 style="margin: 2px 0px"><strong>SURAT PERINTAH PENCAIRAN DANA</strong></h3>
        <div style="clear: both"></div>
    </div>
    <table style="width: 100%" id="judul">
        <tbody>
            <tr>
                <td><b>Tanggal</b></td>
                <td><b>:</b></td>
                <td><b>{{ tanggal($tanggal->tgl_uji) }}</b></td>
                <td style="text-align: right"><b>Lembaran ke 1</b></td>
            </tr>
            <tr>
                <td><b>Nomor</b></td>
                <td><b>:</b></td>
                <td><b>{{ $no_uji }}</b></td>
                <td style="text-align: right"><b>Terdiri dari {{ $jumlah_detail }} lembar</b></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%" border="1" id="rincian">
        <thead>
            <tr>
                <th>NO</th>
                <th>TANGGAL DAN NOMOR SP2D</th>
                <th>ATAS NAMA <br>( YANG BERHAK )</th>
                <th>OPD</th>
                <th>KEPERLUAN</th>
                <th>JUMLAH KOTOR <br>(Rp)</th>
                <th>JUMLAH POTONGAN</th>
                <th>JUMLAH BERSIH</th>
                <th>TANGGAL TRANSFER</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_penguji as $penguji)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td style="text-align: center">{{ $penguji->no_sp2d }}<br>{{ $penguji->tgl_sp2d }}</td>
                    @if ($penguji->jns_spp == '6' && $penguji->jenis_beban == '3')
                        <td>{{ Str::upper($penguji->nmrekan) }}, {{ $penguji->pimpinan }}<br>{{ $penguji->alamat }}</td>
                    @elseif ($penguji->jns_spp == '5')
                        <td>{{ Str::upper($penguji->nmrekan) }}<br>{{ $penguji->nm_skpd }}</td>
                    @else
                        <td>{{ Str::upper($penguji->kd_skpd) }}<br>{{ $penguji->nm_skpd }}</td>
                    @endif
                    <td>{{ $penguji->kd_skpd }}<br>{{ $penguji->nm_skpd }}</td>
                    <td>{{ $penguji->keperluan }}</td>
                    <td style="text-align: right">{{ rupiah($penguji->kotor) }}</td>
                    <td style="text-align: right">{{ rupiah($penguji->pot) }}</td>
                    <td style="text-align: right">{{ rupiah($penguji->kotor - $penguji->pot) }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" style="text-align: center"><b>TOTAL</b></td>
                <td style="text-align: right"><b>{{ rupiah($total_kotor) }}</b></td>
                <td style="text-align: right"><b>{{ rupiah($total_pot) }}</b></td>
                <td style="text-align: right"><b>{{ rupiah($jumlah_bersih) }}</b></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%;margin-top:20px" id="tanda">
        <tbody>
            <tr>
                <td style="width: 10%"><b>Diterima oleh</b></td>
                <td>:</td>
                <td><b>..................................................</b></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3"><b>....................................................................</b></td>
                <td style="text-align: center">
                    {{-- <b>Kuasa Bendahara Umum Daerah</b> --}}
                    <b>
                        {{-- Kuasa Bendahara Umum Daerah --}}
                        {{ $ttd->jabatan }}
                        <br> {{ $ttd->jabatan2 }}
                    </b>
                </td>
            </tr>
            <tr>
                <td colspan="3"><b>Petugas Bank / POS</b></td>
                <td style="text-align: center;padding-bottom:70px"></td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td style="text-align: center"><b><u>{{ $ttd->nama }}</u></b></td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td style="text-align: center"><b>{{ $ttd->pangkat }}</b></td>
            </tr>
            <tr>
                <td colspan="3"><b>__________________________________</b></td>
                <td style="text-align: center"><b>NIP. {{ $ttd->nip }}</b></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
