<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BUKU KAS PEMBANTU PENERIMAAN</title>
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
            <td style="text-align: center"><b> {{ strtoupper($header->nm_pemda) }}</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:20px"><b>BUKU KAS PEMBANTU PENERIMAAN TAHUN ANGGARAN
                    {{ tahun_anggaran() }}</b>
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
                <td>Tanggal :
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
                $nilai_penerimaan = 0;
            @endphp
            @foreach ($data_penerimaan as $penerimaan)
                @php
                    $nilai_penerimaan += $penerimaan->rupiah;
                @endphp
                @if ($penerimaan->urut == '1')
                    <tr>
                        <td style="text-align: center">{{ $penerimaan->no_kas }}</td>
                        <td>{{ empty($penerimaan->nm_pengirim) ? $penerimaan->nm_skpd : $penerimaan->nm_pengirim }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td style="border-top: hidden"></td>
                        <td style="border-top: hidden">{{ $penerimaan->nm_rek6 }}
                            {{ empty($penerimaan->nm_pengirim) ? '' : $penerimaan->nm_pengirim }}</td>
                        <td style="border-top: hidden">{{ $penerimaan->kd_sub_kegiatan }}.{{ $penerimaan->kd_rek6 }}
                        </td>
                        <td class="angka" style="border-top: hidden">
                            @if ($penerimaan->rupiah < 0)
                                ({{ rupiah($penerimaan->rupiah * -1) }})
                            @else
                                {{ rupiah($penerimaan->rupiah) }}
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td class="bawah"></td>
                <td colspan="2" class="bawah kiri">Jumlah Tanggal
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode1) }} s.d {{ tanggal($periode2) }}
                    @endif
                </td>
                <td class="angka bawah">{{ rupiah($nilai_penerimaan) }}</td>
            </tr>
            <tr>
                <td class="bawah"></td>
                <td colspan="2" class="bawah kiri">Jumlah Sampai Tanggal :
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
                <td class="angka">{{ rupiah($total_penerimaan) }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2" class="kiri">Jumlah s.d
                    Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode2) }}
                    @endif
                </td>
                <td class="angka">{{ rupiah($nilai_penerimaan + $total_penerimaan) }}</td>
            </tr>
        </tbody>
    </table>
    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:500px">
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
