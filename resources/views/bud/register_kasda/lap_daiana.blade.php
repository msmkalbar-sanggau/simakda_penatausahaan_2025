<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DAIANA</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #rincian>thead>tr>th {
            background-color: #CCCCCC;
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

        .angka {
            text-align: right
        }
    </style>
</head>

{{-- <body onload="window.print()"> --}}

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="4">
        <tr>
            <td colspan = "11" style="text-align: center;font-size:14px"><b>DATA PENCATATAN TRANSAKSI HARIAN PENDAPATAN
                    DAERAH
                </b></td>
        </tr>
        <tr>
            <td colspan = "11" style="text-align: center;font-size:14px"><b>KABUPATEN SANGGAU</b></td>
        </tr>
        <tr>
            <td colspan = "11" style="text-align: center;font-size:14px"><b></b></td>
        </tr>
        @if ($pilihan == '2')
            <tr>
                <td colspan="2">Nama SKPD
                <td>: {{ $skpd }} {{ nama_skpd($skpd) }} </td>

            </tr>
            <tr>
                <td colspan="2">Periode<br>
                <td>: {{ tanggal($periode1) }} S/d
                    {{ tanggal($periode2) }}</td>
            </tr>
        @elseif ($pilihan == '1')
            <tr>
                <td colspan="2">Periode</td>
                <td>: {{ tanggal($periode1) }} S/d
                    {{ tanggal($periode2) }}</td>
            </tr>
        @endif
    </table>

    <table style="width: 100%;font-size:14px" border="1" id="rincian" cellpadding="{{ $spasi }}">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th colspan="7">Penerimaan</th>
                <th colspan="4">Pengeluaran</th>
                <th rowspan="2">Ket. Penerimaan</th>
                <th rowspan="2">Status</th>
            </tr>
            <tr>
                <th>Tgl</th>
                <th>No Bukti</th>
                <th>Nama SKPD</th>
                <th>Status Setor</th>
                <th>Kode Rekening</th>
                <th>Uraian</th>
                <th>Jumlah</th>
                <th>Tgl</th>
                <th>No STS</th>
                <th>Nama SKPD</th>
                <th>Jumlah</th>
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
                <th>11</th>
                <th>12</th>
                <th>13</th>
                <th>14</th>
            </tr>
        </thead>
        <tbody>
            @php
                $lnnilai = 0;
                $lntotal = 0;
            @endphp
            @foreach ($rincian as $data)
                @if ($data->tgl_terima == '')
                    @php
                        $a = $data->tgl_terima;
                        $tgl = '';
                    @endphp
                @else
                    @php
                        $a = $data->tgl_terima;
                        $tgl = tanggal_indonesia($a);
                    @endphp
                @endif

                @if ($data->tgl_sts == '')
                    @php
                        $b = $data->tgl_sts;
                        $tgl_sts = '';
                    @endphp
                @else
                    @php
                        $b = $data->tgl_sts;
                        $tgl_sts = tanggal_indonesia($b);
                    @endphp
                @endif

                @if ($data->status == 1)
                    @php
                        $s = 'V';
                    @endphp
                @else
                    @php
                        $s = 'X';
                    @endphp
                @endif
                @php
                    $lnnilai += $data->nilai;
                    $lntotal += $data->total;
                @endphp
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td align="center" style="width:100px">{{ $tgl }}</td>
                    <td align="center">{{ $data->no_terima }}</td>
                    <td>{{ $data->nm_skpd_t }}</td>
                    <td>{{ $data->status_setor }}</td>
                    <td align="center">{{ $data->kd_rek6 }}</td>
                    <td>{{ $data->nm_rek6 }}</td>
                    <td align="right" class="angka">{{ rupiah($data->nilai) }}</td>
                    <td align="center" style="width:100px">{{ $tgl_sts }}</td>
                    <td align="center">{{ $data->no_sts }}</td>
                    <td>{{ $data->nm_skpd }}</td>
                    <td align="right" class="angka">{{ rupiah($data->total) }}</td>
                    <td>{{ $data->keterangan }}</td>
                    <td align="center"><b>{{ $s }}</b></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7" align="center"><b> Jumlah</b></td>
                <td align="right" class="angka"><b>{{ rupiah($lnnilai) }}</b></td>
                <td colspan="3" align="center"><b> Jumlah</b></td>
                <td align="right" class="angka"><b>{{ rupiah($lntotal) }}</b></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:800px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center">
                        @if (isset($tgl))
                            Kabupaten Sanggau, {{ tanggal($tgl) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center">
                        {{ $tanda_tangan->jabatan }}
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
    @endif
</body>

</html>
