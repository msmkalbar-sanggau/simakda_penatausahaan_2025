<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REGISTER SP2D</title>
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
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center">
                <b>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}
                </b>
            </td>
        </tr>
        <tr>
            <td style="text-align: center">
                <b>
                    DAFTAR SPM DAN SP2D TAHUN ANGGARAN {{ tahun_anggaran() }}
                </b>
            </td>
        </tr>
    </table>

    <br><br>

    <table style="width: 100%" border="1" id="rincian">
        <thead>
            <tr id="header3">
                <th rowspan="3" style="width: 3%">No.<br>Urut</th>
                <th colspan="10" style="width: 10%">SPM</th>
                <th colspan="8" style="width: 5%">SP2D</th>
            </tr>
            <tr id="header3">
                <th style="width: 5%" rowspan="2">KODE</th>
                <th style="width: 5%" rowspan="2">NAMA</th>
                <th style="width: 5%" rowspan="2">NO</th>
                <th style="width: 5%" rowspan="2">TANGGAL</th>
                <th style="width: 5%" rowspan="2">UP</th>
                <th style="width: 5%" rowspan="2">GU</th>
                <th style="width: 5%" rowspan="2">TU</th>
                <th style="width: 5%" colspan="3">LS</th>
                <th style="width: 5%" rowspan="2">NO</th>
                <th style="width: 5%" rowspan="2">TANGGAL</th>
                <th style="width: 5%" rowspan="2">UP</th>
                <th style="width: 5%" rowspan="2">GU</th>
                <th style="width: 5%" rowspan="2">TU</th>
                <th style="width: 5%" colspan="3">LS</th>
            </tr>
            <tr id="header3">
                <th style="width: 5%">GAJI</th>
                <th style="width: 5%">Barang <br>& Jasa</th>
                <th style="width: 5%">Pihak Ketiga</th>
                <th style="width: 5%">GAJI</th>
                <th style="width: 5%">Barang <br>& Jasa</th>
                <th style="width: 5%">Pihak Ketiga</th>
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
                <th>15</th>
                <th>16</th>
                <th>17</th>
                <th>18</th>
                <th>19</th>
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
            @foreach ($register_sp2d as $register)
                @php
                    $total_up += $register->up;
                    $total_gu += $register->gu;
                    $total_tu += $register->tu;
                    $total_gaji += $register->gaji;
                    $total_ls += $register->ls;
                    $total_ph3 += $register->ph3;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $register->kd_skpd }}</td>
                    <td>{{ $register->nm_skpd }}</td>
                    <td>{{ $register->no_spm }}</td>
                    <td>{{ tanggal_indonesia($register->tgl_spm) }}</td>
                    <td class="angka">{{ rupiah($register->up) }}</td>
                    <td class="angka">{{ rupiah($register->gu) }}</td>
                    <td class="angka">{{ rupiah($register->tu) }}</td>
                    <td class="angka">{{ rupiah($register->gaji) }}</td>
                    <td class="angka">{{ rupiah($register->ls) }}</td>
                    <td class="angka">{{ rupiah($register->ph3) }}</td>
                    <td>{{ $register->no_sp2d }}</td>
                    <td>{{ tanggal_indonesia($register->tgl_sp2d) }}</td>
                    <td class="angka">{{ rupiah($register->up) }}</td>
                    <td class="angka">{{ rupiah($register->gu) }}</td>
                    <td class="angka">{{ rupiah($register->tu) }}</td>
                    <td class="angka">{{ rupiah($register->gaji) }}</td>
                    <td class="angka">{{ rupiah($register->ls) }}</td>
                    <td class="angka">{{ rupiah($register->ph3) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" style="text-align: center"><b>Jumlah</b></td>
                <td class="angka"><b>{{ rupiah($total_up) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_gu) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_tu) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_gaji) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_ls) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_ph3) }}</b></td>
                <td colspan="2" style="text-align: center"><b>Jumlah</b></td>
                <td class="angka"><b>{{ rupiah($total_up) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_gu) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_tu) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_gaji) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_ls) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_ph3) }}</b></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center"><b>Total</b></td>
                <td colspan="6" class="angka">
                    <b>{{ rupiah($total_up + $total_gu + $total_tu + $total_gaji + $total_ls + $total_ph3) }}</b>
                </td>
                <td colspan="2" style="text-align: center"><b>Jumlah</b></td>
                <td colspan="6" class="angka">
                    <b>{{ rupiah($total_up + $total_gu + $total_tu + $total_gaji + $total_ls + $total_ph3) }}</b>
                </td>
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
