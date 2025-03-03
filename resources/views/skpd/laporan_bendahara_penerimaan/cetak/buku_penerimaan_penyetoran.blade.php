<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BUKU PENERIMAAN DAN PENYETORAN</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #rincian>tbody>tr>td {
            vertical-align: top
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
    </style>
</head>

<!-- <body onload="window.print()"> -->

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
            <td align="left" style="font-size:14px"><strong>SKPD {{ strtoupper($skpd->nm_skpd) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    {{-- <hr> --}}
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center"><b>BUKU PENERIMAAN DAN PENYETORAN <br> BENDAHARA PENERIMAAN</b></td>
        </tr>
        <tr>
            <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(tanggal_indonesia($tanggal1)) }}
                    s/d {{ strtoupper(tanggal_indonesia($tanggal2)) }}</b></td>
        </tr>
    </table>
    {{-- isi --}}
    <table style='border-collapse:collapse;' width='100%' align='center' border='1' cellspacing='1'
        cellpadding="{{ $spasi }}">
        <thead>
            <tr>
                <td bgcolor='#CCCCCC' align='center' rowspan='2'>NO</td>
                {{-- <td bgcolor='#CCCCCC' align='center' colspan='6'>Penerimaan</td> --}}
                <td bgcolor='#CCCCCC' align='center' colspan='5'>Penerimaan</td>
                <td bgcolor='#CCCCCC' align='center' colspan='3'>Penyetoran</td>
                <td bgcolor='#CCCCCC' align='center' rowspan='2'>Ket.</td>
                <td bgcolor='#CCCCCC' align='center' rowspan='2'>Status.</td>
            </tr>
            <tr>
                <td bgcolor='#CCCCCC' align='center'>Tgl</td>
                <td bgcolor='#CCCCCC' align='center'>No Bukti</td>
                {{-- <td bgcolor='#CCCCCC' align='center'>Cara Pembayaran</td> --}}
                <td bgcolor='#CCCCCC' align='center'>Kode Rekening</td>
                <td bgcolor='#CCCCCC' align='center'>Uraian</td>
                <td bgcolor='#CCCCCC' align='center'>Jumlah</td>
                <td bgcolor='#CCCCCC' align='center'>Tgl</td>
                <td bgcolor='#CCCCCC' align='center'>No STS</td>
                <td bgcolor='#CCCCCC' align='center'>Jumlah</td>
            </tr>
            <tr>
                <td bgcolor='#CCCCCC' align='center' width='5%'>1</td>
                <td bgcolor='#CCCCCC' align='center' width='8%'>2</td>
                <td bgcolor='#CCCCCC' align='center' width='10%'>3</td>
                <td bgcolor='#CCCCCC' align='center' width='10%'>4</td>
                <td bgcolor='#CCCCCC' align='center' width='7%'>5</td>
                <td bgcolor='#CCCCCC' align='center' width='16%'>6</td>
                <td bgcolor='#CCCCCC' align='center' width='8%'>7</td>
                <td bgcolor='#CCCCCC' align='center' width='9%'>8</td>
                <td bgcolor='#CCCCCC' align='center' width='10%'>9</td>
                <td bgcolor='#CCCCCC' align='center' width='8%'>10</td>
                <td bgcolor='#CCCCCC' align='center' width='18%'>11</td>
                {{-- <td bgcolor='#CCCCCC' align='center' width='3%'>12</td> --}}
            </tr>
        </thead>
        @php
            $lnnilai = 0;
            $lntotal = 0;
            $nomor = 0;
        @endphp
        @foreach ($rincian as $row)
            @php
                $nomor = ++$nomor;
                $lnnilai = $lnnilai + $row->nilai;
                $lntotal = $lntotal + $row->total;
                $tgl = $row->tgl_terima;
                $bukti = $row->no_terima;
                $rek = $row->kd_rek6;
                $uraian = $row->nm_rek6;
                $nilai = rupiah($row->nilai);
                $tgl_sts = $row->tgl_sts;
                $status_setor = $row->status_setor;
                $status = $row->status;
                $ket = $row->keterangan;
                $nosts = $row->no_sts;
                if ($status == 1) {
                    $s = '&#10004';
                } else {
                    $s = '&#10008';
                }
                if ($tgl == '') {
                    $tgl = '';
                } else {
                    $tgl = tanggal_indonesia($tgl);
                }

                if ($tgl_sts == '') {
                    $tgl_sts = '';
                } else {
                    $tgl_sts = tanggal_indonesia($tgl_sts);
                }

                $total = rupiah($row->total);

                if ($nilai == $total) {
                    $sts = '&#10004';
                } else {
                    $sts = '&#10008';
                }

            @endphp


            @if ($kd_skpd == '1.02.0.00.0.00.02.0000' || $kd_skpd == '1.02.0.00.0.00.03.0000')
                <tr>
                    <td align='center'>{{ $nomor }}</td>
                    <td align='center'>{{ $tgl }}</td>
                    <td align='center'>{{ $bukti }}</td>
                    {{-- <td align='center'>{{ $status_setor }}</td> --}}
                    <td align='center'>{{ $rek }}</td>
                    <td align='left'>{{ $uraian }}</td>
                    <td align='right'>{{ $nilai }}</td>
                    <td align='center'>{{ $tgl_sts }}</td>
                    <td align='center'>{{ $nosts }}</td>
                    <td align='right'>{{ $total }}</td>
                    <td align='left'>{{ $ket }}</td>
                    <td align='left'>{{ $sts }}</td>
                </tr>
            @else
                <tr>
                    <td align='center'>{{ $nomor }}</td>
                    <td align='center'>{{ $tgl }}</td>
                    <td align='center'>{{ $bukti }}</td>
                    {{-- <td align='center'>{{ $status_setor }}</td> --}}
                    <td align='center'>{{ $rek }}</td>
                    <td align='left'>{{ $uraian }}</td>
                    <td align='right'>{{ $nilai }}</td>
                    <td align='center'>{{ $tgl_sts }}</td>
                    <td align='center'>{{ $nosts }}</td>
                    <td align='right'>{{ $total }}</td>
                    <td align='left'>{{ $ket }}</td>
                    <td align='center'>{!! $s !!}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td colspan='5' align='center'><b>Jumlah</b></td>
            <td align='right'><b>{{ rupiah($lnnilai) }}</b></td>
            <td align='center'></td>
            <td align='center'></td>
            <td align='right'><b>{{ rupiah($lntotal) }}</b></td>
            <td align='left'></td>
            <td align='left'></td>
        </tr>

    </table>
    {{-- isi --}}
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table" style="width: 100%;font-size:12px;font-family:Open Sans">
            <tr>
                <td style="margin: 2px 0px;text-align: center;">
                    Disetujui oleh
                </td>
                <td style="margin: 2px 0px;text-align: center;">
                    {{ $daerah->daerah }},
                    {{ \Carbon\Carbon::parse($tanggal_ttd)->locale('id')->isoFormat('DD MMMM Y') }}
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center;">
                    {{ ucwords(strtolower($cari_pa_kpa->jabatan)) }}
                </td>
                <td style="padding-bottom: 50px;text-align: center;">
                    {{ ucwords(strtolower($cari_bendahara ? $cari_bendahara->jabatan : ' ')) }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;"><b><u>{{ $cari_pa_kpa->nama }}</u></b></td>
                <td style="text-align: center;"><b><u>{{ $cari_bendahara ? $cari_bendahara->nama : ' ' }}</u></b></td>
            </tr>
            <tr>
                <td style="text-align: center;">{{ $cari_pa_kpa->pangkat }}</td>
                <td style="text-align: center;">{{ $cari_bendahara ? $cari_bendahara->pangkat : ' ' }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">NIP. {{ $cari_pa_kpa->nip }}</td>
                <td style="text-align: center;">NIP. {{ $cari_bendahara ? $cari_bendahara->nip : ' ' }}</td>
            </tr>

        </table>
    </div>
</body>

</html>
