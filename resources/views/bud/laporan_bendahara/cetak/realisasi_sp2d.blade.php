<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REGISTER SP2D (REALISASI)</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #header3>th {
            background-color: #CCCCCC;
        }

        .kanan {
            border-right: 1px solid black
        }

        .kiri {
            border-left: 1px solid black
        }

        .bawah {
            border-bottom: hidden
        }

        .atas {
            border-top: hidden
        }

        .angka {
            text-align: right
        }
    </style>
</head>

{{-- <body onload="window.print()"> --}}

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
            <td align="left" style="font-size:16px"><strong>BADAN KEUANGAN DAN ASET DAERAH</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <br>

    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center">
                REALISASI SURAT PERINTAH PENCAIRAN DANA (SP2D) PERDINAS/ISNTANSI/UNIT KERJA
            </td>
        </tr>
        <tr>
            <td style="text-align: center">
                @if (substr($pilihan, -1) == '2')
                    Periode {{ bulan($data_awal['bulan']) }}
                @elseif (substr($pilihan, -1) == '3')
                    Periode {{ tanggal($data_awal['periode1']) }} s/d
                    {{ tanggal($data_awal['periode2']) }}
                @endif
            </td>
        </tr>
    </table>

    <br><br>

    <table style="width: 100%" border="1" id="rincian">
        <thead>
            <tr id="header3">
                <th rowspan="2" style="width: 3%">Kode</th>
                <th rowspan="2" style="width: 10%">Urusan Permerintah Daerah</th>
                <th colspan="2" style="width: 5%">Belanja</th>
                <th rowspan="2" style="width: 5%">Persen<BR>tase</th>
                <th colspan="2" style="width: 5%">Jumlah Keseluruhan</th>
                <th rowspan="2" style="width: 5%">Persen<BR>tase</th>
            </tr>
            <tr id="header3">
                <th style="width: 5%">Plafond</th>
                <th style="width: 5%">Realisasi SP2D</th>
                <th style="width: 5%">Plafond</th>
                <th style="width: 5%">Realisasi SP2D</th>
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
            </tr>
        </thead>
        <tbody>
            @php
                $total_up = 0;
                $total_gu = 0;
                $total_tu = 0;
                $total_gaji = 0;
                $total_ls = 0;
                $total_ph3 = 0;
            @endphp
            @php
                $total_ang = 0;
                $total_bel = 0;
            @endphp
            @foreach ($register_sp2d as $register)
                @php
                    $total_ang += $register->ang;
                    $total_bel += $register->bel;
                @endphp
                <tr>
                    <td>{{ $register->kode }}</td>
                    <td>{{ Str::upper($register->nama) }}</td>
                    <td class="angka">{{ rupiah($register->ang) }}</td>
                    <td class="angka">{{ rupiah($register->bel) }}</td>
                    <td class="angka">
                        {{ $register->ang == 0 ? rupiah(0) : rupiah(($register->bel * 100) / $register->ang) }}
                    </td>
                    <td class="angka">{{ rupiah($register->ang) }}</td>
                    <td class="angka">{{ rupiah($register->bel) }}</td>
                    <td class="angka">
                        {{ $register->ang == 0 ? rupiah(0) : rupiah(($register->bel * 100) / $register->ang) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: center"><b>TOTAL</b></td>
                <td class="angka"><b>{{ rupiah($total_ang) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_bel) }}</b></td>
                <td class="angka"><b>{{ rupiah(($total_bel * 100) / $total_ang) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_ang) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_bel) }}</b></td>
                <td class="angka"><b>{{ rupiah(($total_bel * 100) / $total_ang) }}</b></td>
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
