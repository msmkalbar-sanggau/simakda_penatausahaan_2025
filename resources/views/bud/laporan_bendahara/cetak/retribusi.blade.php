<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RETRIBUSI</title>
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

{{-- <body onload="window.print()"> --}}

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>PEMERINTAH {{ strtoupper($header->nm_pemda) }}</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:20px">
                <b>
                    RETRIBUSI TAHUN ANGGARAN {{ tahun_anggaran() }}
                </b>
            </td>
        </tr>
    </table>
    <table style="width: 100%" border="1" id="rincian" cellpadding="{{ $spasi }}">
        <thead>
            <tr>
                <th>No. Urut</th>
                <th>Uraian</th>
                <th>Kode Rekening</th>
                <th>Penerimaan (Rp.)</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td>Tanggal : {{ tanggal($tanggal) }}
                </td>
                <td></td>
                <td></td>
            </tr>
            @php
                $nilai_retribusi = 0;
            @endphp
            @foreach ($daftar_retribusi as $retribusi)
                @php
                    $nilai = empty($retribusi->rupiah) ? 0 : $retribusi->rupiah;
                @endphp
                @if ($retribusi->urut == '1')
                    <tr>
                        <td style="text-align: center">{{ $retribusi->no_kas }}</td>
                        <td>
                            {{ $retribusi->nm_pengirim == '' ? $retribusi->nm_skpd : $retribusi->nm_pengirim }}
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    @php
                        $nilai_retribusi += $retribusi->rupiah;
                    @endphp
                    <tr>
                        <td style="border-top: hidden"></td>
                        <td style="border-top: hidden">{{ $retribusi->nm_rek6 }}</td>
                        <td style="border-top: hidden">
                            {{ $retribusi->kd_sub_kegiatan == '' ? '' : $retribusi->kd_sub_kegiatan . '.' . $retribusi->kd_rek6 }}
                        </td>
                        <td class="angka" style="border-top: hidden">
                            {{ $nilai < 0 ? '(' . rupiah($nilai) . ')' : rupiah($nilai) }}
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td class="bawah"></td>
                <td colspan="2" class="bawah kiri">Jumlah Tanggal
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    {{ tanggal($tanggal) }}
                </td>
                <td class="angka bawah">{{ rupiah($nilai_retribusi) }}</td>
            </tr>
            <tr>
                <td class="bawah"></td>
                <td colspan="2" class="bawah kiri">Jumlah Sampai Tanggal :
                    @php
                        $tanggal_sebelumnya = strtotime('-1 day', strtotime($tanggal));
                        $tanggal_sebelumnya = date('Y-m-d', $tanggal_sebelumnya);
                    @endphp
                    {{ tanggal($tanggal_sebelumnya) }}
                </td>
                <td class="angka">{{ rupiah($total_retribusi_lalu) }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2" class="kiri">Jumlah s.d
                    Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    {{ tanggal($tanggal) }}
                </td>
                <td class="angka">{{ rupiah($nilai_retribusi + $total_retribusi_lalu) }}</td>
            </tr>
        </tbody>
    </table>
    @if (isset($tanda_tangan))
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
    @endif
</body>

</html>
