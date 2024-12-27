<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REGISTER KOREKSI PENERIMAAN</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #pilihan1>thead>tr>th {
            background-color: #CCCCCC;
            font-weight: normal
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
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:16px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75"
                    height="100" />
            </td>
            <td align="left" style="font-size:14px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px">
                <strong>
                    SKPD BADAN KEUANGAN DAN ASET DAERAH
                </strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px">
                <strong>
                    TAHUN ANGGARAN {{ tahun_anggaran() }}
                </strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <hr>

    <table style="width: 100%" align="center">
        <tr>
            <td style="text-align: center"><b>LAPORAN KOREKSI TAHUN ANGGARAN {{ tahun_anggaran() }}</b></td>
        </tr>
    </table>

    <table style="width: 100%;margin-top:20px" border="1" id="pilihan1">
        <thead>
            <tr>
                <th><b>No. Urut</b></th>
                <th><b>Tanggal</b></th>
                <th><b>Uraian</b></th>
                <th><b>Kode Rekening</b></th>
                <th><b>Penerimaan (Rp)</b></th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td colspan="2">Tanggal :
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode1) }} s.d {{ tanggal($periode2) }}
                    @endif
                </td>
                <td></td>
                <td></td>
            </tr>
            @php
                $total = 0;
            @endphp
            @foreach ($data_koreksi as $data)
                @php
                    $total += $data->nilai;
                @endphp
                <tr>
                    <td style="text-align: center">{{ $data->no }}</td>
                    <td style="text-align: center">{{ tanggal($data->tanggal) }}</td>
                    <td>{{ $data->nm_skpd }} <br> {{ $data->keterangan }}</td>
                    <td style="text-align: center">{{ $data->kd_rek }}</td>
                    <td class="angka">{{ empty($data->nilai) ? rupiah(0) : rupiah($data->nilai) }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="bawah"></td>
                <td colspan="3" style="border-left:hidden;">Jumlah Tanggal
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode1) }} s.d {{ tanggal($periode2) }}
                    @endif
                </td>
                <td class="angka bawah">{{ rupiah($total) }}</td>
            </tr>
            <tr>
                <td class="bawah" style="border-top:hidden;"></td>
                <td colspan="3" style="border-top:hidden;border-left:hidden;">Jumlah Sampai Tanggal :
                    @if ($pilihan == '1')
                        @php
                            $tanggal_sebelumnya = strtotime('-1 day', strtotime($tanggal));
                            $tanggal_sebelumnya = date('Y-m-d', $tanggal_sebelumnya);
                        @endphp
                        {{ tanggal($tanggal_sebelumnya) }}
                    @elseif ($pilihan == '2')
                        @php
                            $tanggal_sebelumnya = strtotime('-1 day', strtotime($periode1));
                            $tanggal_sebelumnya = date('Y-m-d', $tanggal_sebelumnya);
                        @endphp
                        {{ tanggal($tanggal_sebelumnya) }}
                    @endif
                </td>
                <td class="angka" style="border-top:hidden;">{{ rupiah($koreksi_lalu) }}</td>
            </tr>
            <tr>
                <td style="border-top:hidden;"></td>
                <td colspan="3" class="kiri" style="border-top:hidden;border-left:hidden;">Jumlah s.d
                    Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode2) }}
                    @endif
                </td>
                <td class="angka">{{ rupiah($total + $koreksi_lalu) }}</td>
            </tr>
        </tbody>
    </table>

    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:800px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center">
                        @if (isset($tanggal))
                            Pontianak, {{ tanggal($tanggal) }}
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
