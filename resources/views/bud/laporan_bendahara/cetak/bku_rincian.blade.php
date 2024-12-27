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
            font-weight: normal
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
        {{-- <tr>
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
            <td align="left" style="font-size:14px"><strong>SKPD {{ $skpd->nm_skpd }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr> --}}
    </table>

    <table style="border-collapse:collapse;font-family: Open Sans; font-size:14px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0" cellpadding="{{ $no_urut }}">
        <tr>
            <td style="text-align: center"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td style="text-align: center"><b>BUKU KAS PENERIMAAN DAN PENGELUARAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center"><b>TAHUN ANGGARAN {{ tahun_anggaran() }}</b></td>
        </tr>
        <tr>
            <td style="text-align: center"><br>Per tanggal {{ tanggal($tanggal) }}<br></td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
    </table>

    <table style="width: 100%" border="1" id="rincian">
        <thead>
            <tr>
                <th style="width: 5%">No. Kas</th>
                <th style="width: 5%">Tanggal</th>
                <th style="width: 25%">Uraian<br>Penerimaan Dan Pengeluaran</th>
                <th style="width: 20%">Kode Rekening</th>
                <th style="width: 25%">Sub Rincian Objek<br>Penerimaan Dan Pengeluaran</th>
                <th style="width: 15%">Jumlah Rincian</th>
                <th style="width: 25%">Penerimaan</th>
                <th style="width: 25%">Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_terima = 0;
                $total_keluar = 0;

            @endphp
            @foreach ($data_bku as $bku)
                @php
                    $total_terima += $bku->terima;
                    $total_keluar += $bku->tot_kel;
                    // echo $total_keluar;
                @endphp
                <tr>
                    <td style="text-align: center;border-top:none;border-bottom:none">{{ $bku->no_kas }}</td>
                    @if ($bku->no_kas != '')
                        @if ($bku->jenis == '1')
                            <td style="text-align: center;border-top:none;border-bottom:none">
                                {{ tanggal_indonesia($bku->tgl) }}</td>
                            <td class="bawah">{{ $bku->uraian }}{{ $bku->netto == 0 ? '' : rupiah($bku->netto) }}
                            </td>
                            <td>{{ $bku->kode }}</td>
                            <td>{{ $bku->nm_rek6 }}</td>
                            <td></td>
                            <td align="right">{{ $bku->netto == 0 ? '' : rupiah($bku->netto) }}</td>
                            <td align="right"> 0,00</td>
                        @else
                            <td style="text-align: center;border-top:none;border-bottom:none">
                                {{ tanggal_indonesia($bku->tgl) }}</td>
                            <td class="bawah">{{ $bku->uraian }}{{ $bku->netto == 0 ? '' : rupiah($bku->netto) }}
                            </td>
                            <td>{{ $bku->kode }}</td>
                            <td>{{ $bku->nm_rek6 }}</td>
                            <td></td>
                            <td align="right"> 0,00 </td>
                            <td align="right">{{ rupiah($bku->keluar) }}</td>
                        @endif
                    @else
                        @if ($bku->jenis == '1')
                            <td style="text-align: center;border-top:none;border-bottom:none"></td>

                            <td class="atas"></td>
                            <td>{{ $bku->kode }}</td>
                            <td>{{ $bku->nm_rek6 }}</td>
                            <td class="angka">{{ rupiah($bku->terima) }}</td>
                            <td></td>
                            <td></td>
                        @else
                            <td style="text-align: center;border-top:none;border-bottom:none"></td>

                            <td class="atas"></td>
                            <td>{{ $bku->kode }}</td>
                            <td>{{ $bku->nm_rek6 }}</td>
                            <td class="angka">{{ rupiah($bku->keluar) }}</td>
                            <td></td>
                            <td></td>
                        @endif
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="6" class="angka">Jumlah Tanggal {{ tanggal($tanggal) }}</td>
                <td class="angka">{{ rupiah($total_terima) }}</td>
                <td class="angka">{{ rupiah($total_keluar) }}</td>
            </tr>
            @php
                $total_terima_akhir = $total_terima + $total_bku->trm_sbl + $saldo_awal->nil;
                $total_keluar_akhir = $total_keluar + $total_bku->klr_sbl;
            @endphp
            <tr>
                <td colspan="6" class="angka">Jumlah Sampai Dengan Tanggal {{ tanggal_sebelumnya($tanggal) }}
                </td>
                <td class="angka">{{ rupiah($total_bku->trm_sbl + $saldo_awal->nil) }}</td>
                <td class="angka">{{ rupiah($total_bku->klr_sbl) }}</td>
            </tr>
            <tr>
                <td colspan="6" class="angka">Jumlah Sampai Dengan Tanggal {{ tanggal($tanggal) }}</td>
                <td class="angka">{{ rupiah($total_terima_akhir) }}</td>
                <td class="angka">{{ rupiah($total_keluar_akhir) }}</td>
            </tr>
            {{-- <tr>
                <td colspan="6" class="angka">Sisa Kas</td>
                <td colspan="2" class="angka">{{ rupiah($total_terima_akhir - $total_keluar_akhir) }}</td>
            </tr> --}}
        </tbody>
    </table>

    <br>

    <table>
        <tr>
            <td>Pada hari ini, tanggal {{ tanggal($tanggal) }}</td>
        </tr>
        <tr>
            <td>Oleh kami didapat dalam kas Rp. {{ rupiah($total_terima_akhir - $total_keluar_akhir) }}</td>
        </tr>
        <tr>
            <td><i>({{ terbilang($total_terima_akhir - $total_keluar_akhir) }})</i></td>
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
