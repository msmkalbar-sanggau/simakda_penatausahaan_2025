<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REALISASI SKPD SP2D</title>
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

        .rincian>tbody>tr>td {
            font-size: 14px
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
            <td align="left" style="font-size:16px">
                <strong>
                    BADAN KEUANGAN DAN ASET DAERAH
                </strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <br>
    <table style="border-collapse:collapse;font-family: Open Sans" width="100%" align="center" border="0"
        cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center;font-size:16px">REALISASI SURAT PERINTAH PENCAIRAN DANA (SP2D)
                PERDINAS/INSTANSI/UNIT
                KERJA</td>
        </tr>
    </table>
    <br>
    <table style="width: 100%" border="1" class="rincian">
        <thead>
            <tr id="header3">
                <th rowspan="2" style="width: 1%">KODE</th>
                <th rowspan="2" style="width: 20%">Urusan Pemerintah Daerah</th>
                <th colspan="2" style="width: 5%">Belanja</th>
                <th rowspan="2" style="width: 5%">Persen <br> tase</th>
            </tr>
            <tr id="header3">
                <th style="width: 5%">Plafond</th>
                <th style="width: 5%">Realisasi SP2D</th>
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
            @php
                $tot_ang = 0;
                $tot_bel = 0;
            @endphp
            @foreach ($realisasi as $rekap)
                @php
                    $tot_ang += $rekap->ang;
                    $tot_bel += $rekap->bel;
                @endphp
                <tr>
                    <td>{{ $rekap->kode }}</td>
                    <td>{{ $rekap->nama }}</td>
                    <td class="angka">{{ rupiah($rekap->ang) }}</td>
                    <td class="angka">{{ rupiah($rekap->bel) }}</td>
                    <td class="angka">
                        @if ($rekap->bel != 0 && $rekap->ang != 0)
                            {{ rupiah(($rekap->bel * 100) / $rekap->ang) }}
                        @else
                            {{ rupiah(0) }}
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: center"><b>TOTAL</b></td>
                <td class="angka"><b>{{ rupiah($tot_ang) }}</b></td>
                <td class="angka"><b>{{ rupiah($tot_bel) }}</b></td>
                <td class="angka">
                    <b>
                        @if ($tot_bel != 0 && $tot_ang != 0)
                            {{ rupiah(($tot_bel * 100) / $tot_ang) }}
                        @else
                            {{ rupiah(0) }}
                        @endif
                    </b>
                </td>
            </tr>
            <tr>
                <td colspan="5"><b>PENGELUARAN PEMBIAYAAN DAERAH</b></td>
            </tr>
            <tr>
                <td colspan="2"><b>Penyertaan Modal/Investasi Pemerintah Daerah</b></td>
                <td class="angka"><b>{{ rupiah($pembiayaan) }}</b></td>
                <td class="angka"><b>{{ rupiah($realisasi_pembiayaan) }}</b></td>
                <td class="angka"><b>{{ rupiah(($pembiayaan * 100) / $realisasi_pembiayaan) }}</b></td>
            </tr>
            <tr>
                <td colspan="2">Penyertaan Modal</td>
                <td class="angka">{{ rupiah($pembiayaan) }}</td>
                <td class="angka">{{ rupiah($realisasi_pembiayaan) }}</td>
                <td class="angka">{{ rupiah(($pembiayaan * 100) / $realisasi_pembiayaan) }}</td>
            </tr>
        </tbody>
    </table>
    <br>


    <table style="width: 100%" class="rincian">
        <tr>
            <td colspan="2"><b>Catatan:</b></td>
        </tr>
        <tr>
            <td>1.</td>
            <td>Plafond Anggaran RSUD DR.SOEDARSO telah dikurangi BLUD sebesar Rp. {{ rupiah($blud_soedarso) }}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Plafond Anggaran RUMAH SAKIT JIWA PROVINSI telah dikurangi BLUD sebesar Rp. {{ rupiah($blud_rsj) }}
            </td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Plafond Anggaran Dinas Pendidikan telah dikurangi BOS sebesar Rp. {{ rupiah($bos_dikbud) }}</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Plafond Anggaran Badan Keuangan dan Aset Daerah dikurangi :</td>
        </tr>
        <tr>
            <td></td>
            <td>- Bantuan Keuangan sebesar Rp. {{ rupiah($bantuan_keuangan) }}</td>
        </tr>
        <tr>
            <td></td>
            <td>- BTT sebesar Rp. {{ rupiah($btt) }}</td>
        </tr>
        <tr>
            <td></td>
            <td>- Dana Bagi Hasil Provinsi sebesar Rp. {{ rupiah($bagi_hasil) }}</td>
        </tr>
        <tr>
            <td style="height: 20px"></td>
        </tr>
        <tr>
            <td colspan="2">* Posisi Pagu {{ $nama_anggaran->nama }}</td>
        </tr>
        <tr>
            <td colspan="2">
                @if ($dengan == 'true')
                    * Dengan UP
                @else
                    * Tanpa UP
                @endif
            </td>
        </tr>
    </table>


    @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:500px">
            <table class="table rincian" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center;font-size:16px">
                        @if (isset($tanggal))
                            Pontianak, {{ tanggal($tanggal) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center;font-size:16px">
                        {{-- {{ $tanda_tangan->jabatan }} --}}
                        Kepala Bidang Perbendaharaan <br> Badan Keuangan dan Aset Daerah <br> Provinsi Kalimantan Barat
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;font-size:16px"><b><u>{{ $tanda_tangan->nama }}</u></b></td>
                </tr>
                <tr>
                    <td style="text-align: center;font-size:16px">{{ $tanda_tangan->pangkat }}</td>
                </tr>
                <tr>
                    <td style="text-align: center;font-size:16px">NIP. {{ $tanda_tangan->nip }}</td>
                </tr>
            </table>
        </div>
    @endif
</body>

</html>
