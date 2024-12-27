<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cek Penerimaan</title>
    <style>
        table {
            border-collapse: collapse
        }

        #rincian>thead>tr>th {
            background-color: #CCCCCC;
        }

        .angka {
            text-align: right
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
            <td align="left" style="font-size:14px"><strong>{{ nama_skpd($skpd) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <hr>

    <table style="width: 100%">
        <tr>
            <td style="text-align: center"><b>BUKU PENYETORAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center"><b>BENDAHARA PENERIMAAN</b></td>
        </tr>
    </table>

    <table style="width: 100%;padding-top:30px">
        <tr>
            <td>OPD</td>
            <td>:</td>
            <td>{{ nama_skpd($skpd) }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td>:</td>
            <td>{{ tanggal($tanggal_awal) }} S.D {{ tanggal($tanggal_akhir) }}</td>
        </tr>
    </table>

    <table style="width: 100%" id="rincian" border="1">
        <thead>
            <tr>
                <th>No. STS</th>
                <th>Tgl STS</th>
                <th>Ket.</th>
                <th>Rek.</th>
                <th>Nama Rek.</th>
                <th>Nilai</th>
                <th>No. Terima</th>
                <th>Tgl Terima</th>
                <th>Sumber</th>
                <th>Pembayaran</th>
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
                <th>10</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_setor as $data)
                @if ($data->nomor == '1')
                    <tr>
                        <td><b>{{ $data->no_sts }}</b></td>
                        <td style="text-align: center"><b>{{ $data->tgl_sts == '' ? '' : tanggal($data->tgl_sts) }}</b>
                        </td>
                        <td><b>{{ $data->keterangan }}</b></td>
                        <td></td>
                        <td></td>
                        <td class="angka"><b>{{ rupiah($data->rupiah) }}</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td style="border-top: hidden"></td>
                        <td style="border-top: hidden"></td>
                        <td style="border-top: hidden"></td>
                        <td style="text-align: center">{{ $data->kd_rek6 }}</td>
                        <td>{{ $data->nm_rek6 }}</td>
                        <td class="angka"><b>{{ rupiah($data->rupiah) }}</b></td>
                        <td>{{ $data->no_terima }}</td>
                        <td style="text-align: center">
                            {{ $data->tgl_terima == '' ? '' : tanggal($data->tgl_terima) }}
                        </td>
                        <td>{{ $data->nm_sumber }}</td>
                        <td>
                            @if ($data->pembayaran == 0)
                                Tunai
                            @elseif ($data->pembayaran == 1)
                                QRIS
                            @elseif ($data->pembayaran == 2)
                                EDC
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td colspan="5" style="text-align: center"><b>Total</b></td>
                <td colspan="5"><b>{{ rupiah($total_setor->total) }}</b></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
