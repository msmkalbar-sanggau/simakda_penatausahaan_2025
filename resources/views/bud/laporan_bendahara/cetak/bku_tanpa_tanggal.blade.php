<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BUKU KAS PENERIMAAN DAN PENGELUARAN</title>
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
            <td style="text-align: center"><b>BUKU KAS PENERIMAAN DAN PENGELUARAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:20px"><b>TAHUN ANGGARAN {{ tahun_anggaran() }}</b>
            </td>
        </tr>

    </table>

    <table style="width: 100%" border="1" id="rincian" cellpadding="{{ $no_urut }}">
        <thead>
            <tr>
                <th>NOMOR</th>
                <th>URAIAN</th>
                <th>PENERIMAAN</th>
                <th>PENGELUARAN</th>
            </tr>
            <tr>
                <th style="font-weight: normal">1</th>
                <th style="font-weight: normal">2</th>
                <th style="font-weight: normal">3</th>
                <th style="font-weight: normal">4</th>
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
                $total_terima = 0;
                $total_keluar = 0;
            @endphp
            @foreach ($data_bku as $bku)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $bku->nama }}</td>
                    <td class="angka">
                        @if ($bku->jenis == '1')
                            @php
                                $total_terima += $bku->nilai;
                            @endphp
                            @if ($bku->nilai < 0)
                                ({{ rupiah($bku->nilai * -1) }})
                            @else
                                {{ rupiah($bku->nilai) }}
                            @endif
                        @else
                            @php
                                $total_keluar += $bku->nilai;
                            @endphp
                            {{ rupiah(0) }}
                        @endif
                    </td>
                    <td class="angka">
                        @if ($bku->jenis == '1')
                            {{ rupiah(0) }}
                        @else
                            {{ rupiah($bku->nilai) }}
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="bawah">Jumlah Tanggal
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode1) }} s.d {{ tanggal($periode2) }}
                    @endif
                </td>
                <td class="angka bawah">{{ rupiah($total_terima) }}</td>
                <td class="angka bawah">{{ rupiah($total_keluar) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="bawah">Jumlah Sampai Tanggal :
                    @if ($pilihan == '1')
                        {{ tanggal_sebelumnya($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal_sebelumnya($periode1) }}
                    @endif
                </td>
                <td class="angka bawah">{{ rupiah($total_bku->trm_sbl + $saldo_awal) }}</td>
                <td class="angka bawah">{{ rupiah($total_bku->klr_sbl) }}</td>
            </tr>
            <tr>
                <td colspan="2">Jumlah s.d
                    Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    @if ($pilihan == '1')
                        {{ tanggal($tanggal) }}
                    @elseif ($pilihan == '2')
                        {{ tanggal($periode2) }}
                    @endif
                </td>
                <td class="angka">{{ rupiah($total_bku->trm_sbl + $saldo_awal + $total_terima) }}</td>
                <td class="angka">{{ rupiah($total_bku->klr_sbl + $total_keluar) }}</td>
            </tr>
        </tbody>
    </table>
    <br>

    <table>
        <tr>
            <td>Pada hari ini,
                @if ($pilihan == '1')
                    tanggal {{ tanggal($tanggal) }}
                @elseif ($pilihan == '2')
                    s.d tanggal {{ tanggal($periode2) }}
                @endif
            </td>
        </tr>
        <tr>
            @php
                $terima = $total_bku->trm_sbl + $saldo_awal + $total_terima;
                $keluar = $total_bku->klr_sbl + $total_keluar;
            @endphp
            <td>
                Oleh kami didapat dalam kas Rp. {{ rupiah($terima - $keluar) }}
            </td>
        </tr>
        <tr>
            <td>
                <i>({{ terbilang($terima - $keluar) }})</i>
            </td>
        </tr>
    </table>

    @if (isset($tanda_tangan))
        <div style="padding-top:20px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="width: 50%"></td>
                    <td style="padding-bottom: 50px;text-align: center">
                        {{ $tanda_tangan->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%"></td>
                    <td style="text-align: center">
                        <b><u>{{ $tanda_tangan->nama }}</u></b> <br>
                        {{ $tanda_tangan->pangkat }} <br>
                        NIP. {{ $tanda_tangan->nip }}
                    </td>
                </tr>
            </table>
        </div>
    @endif
</body>

</html>
