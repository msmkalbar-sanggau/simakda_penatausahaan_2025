<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
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
    <table style="width: 100%">
        <tbody>
            <tr>
                <td><strong>{{ strtoupper($daerah->kab_kota) }}</strong></td>
            </tr>
            <tr>
                <td><strong>SKPD {{ $skpd->nm_skpd }}</strong></td>
            </tr>
            <tr>
                <td><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table style="width: 100%">
        <tr>
            <td style="text-align: center"><b>LIST TRANSAKSI</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ tanggal($tgl_voucher) }}</b></td>
        </tr>
    </table>
    <table style="width: 100%;margin-top:10px" border="1" id="rincian">
        <thead>
            <tr>
                <th style="width:5px">No</th>
                <th style="width:5%">SKPD</th>
                <th style="width:20%">Kode Rekening</th>
                <th style="width:40%">Uraian</th>
                <th style="width:10px">Penerimaan</th>
                <th style="width:10px">Pengeluaran</th>
                <th style="width:10px">ST</th>
            </tr>
            <tr>
                <th class="t1">1</th>
                <th class="t1">2</th>
                <th class="t1">3</th>
                <th class="t1">4</th>
                <th class="t1">5</th>
                <th class="t1">6</th>
                <th class="t1">7</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_terima = 0;
                $total_keluar = 0;
            @endphp
            @foreach ($data_cms as $cms)
                @if ($cms->urut != '3')
                    @php
                        $total_terima += $cms->terima;
                        $total_keluar += $cms->keluar;
                    @endphp
                @endif
                <tr>
                    @if ($cms->urut == '1')
                        <td style="text-align:center">{{ $cms->no_voucher }}</td>
                        <td style="text-align:center">{{ $cms->kd_skpd }}</td>
                        <td style="text-align: center">{{ $cms->kegiatan }}.{{ $cms->rekening }}</td>
                        <td style="text-align: justify">{{ $cms->ket }}</td>
                        <td></td>
                        <td></td>
                        <td style="text-align: center">{{ $cms->status_upload }}</td>
                    @elseif ($cms->urut == '3')
                        <td></td>
                        <td></td>
                        <td style="text-align: center">{{ $cms->kegiatan }}</td>
                        <td style="text-align: right">{{ $cms->ket }}</td>
                        <td></td>
                        <td style="text-align: left">{{ rupiah($cms->keluar) }}</td>
                        <td></td>
                    @else
                        <td></td>
                        <td></td>
                        <td style="text-align: center">{{ $cms->kegiatan }}.{{ $cms->rekening }}</td>
                        <td style="text-align: justify">{{ $cms->ket }}</td>
                        <td style="text-align: right">{{ rupiah($cms->terima) }}</td>
                        <td style="text-align: right">{{ rupiah($cms->keluar) }}</td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: center">JUMLAH</td>
                <td style="text-align: right">{{ rupiah($total_terima) }}</td>
                <td style="text-align: right">{{ rupiah($total_keluar) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td colspan="2" style="padding-top: 10px" class="kanan kiri">Saldo Sampai Dengan Tanggal
                    {{ tanggal($tgl_voucher) }},</td>
            </tr>
            <tr>
                <td class="kiri" style="width: 10%">- Saldo Bank</td>
                <td class="kanan">: Rp. {{ rupiah($bank->terima - $bank->keluar) }}</td>
            </tr>
            <tr>
                <td class="kiri" style="width: 10%">- Jumlah Terima</td>
                <td class="kanan">: Rp. {{ rupiah($total_terima) }}</td>
            </tr>
            <tr>
                <td class="kiri bawah" style="width: 10%">- Jumlah Keluar</td>
                <td class="kanan bawah">: Rp. {{ rupiah($total_keluar) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 10px" class="kanan kiri">Perkiraan Akhir Saldo,</td>
            </tr>
            <tr>
                <td class="kiri bawah" style="width: 10%">- Saldo Bank</td>
                <td class="kanan bawah">: Rp.
                    {{ rupiah($bank->terima - $bank->keluar + $total_terima - $total_keluar) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
