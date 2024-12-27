<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
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
            <td align="left" style="font-size:14px"><strong>SKPD {{ $skpd->nm_skpd }}</strong></td>
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
            <td style="text-align: center"><b>BUKU {{ $jenis }}</b></td>
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
                <td bgcolor='#CCCCCC' align='center' width='8%'>No. STS</td>
                <td bgcolor='#CCCCCC' align='center' width='10%'>Tgl STS</td>
                <td bgcolor='#CCCCCC' align='center' width='10%'>Ket.</td>
                <td bgcolor='#CCCCCC' align='center' width='5%'>Rek.</td>
                <td bgcolor='#CCCCCC' align='center' width='22%'>Nama Rek.</td>
                <td bgcolor='#CCCCCC' align='center' width='8%'>Nilai</td>
                <td bgcolor='#CCCCCC' align='center' width='12%'>No. Terima</td>
                <td bgcolor='#CCCCCC' align='center' width='10%'>Tgl Terima</td>
                <td bgcolor='#CCCCCC' align='center' width='15%'>Sumber</td>
            </tr>
            <tr>
                <td bgcolor='#CCCCCC' align='center'>1</td>
                <td bgcolor='#CCCCCC' align='center'>2</td>
                <td bgcolor='#CCCCCC' align='center'>3</td>
                <td bgcolor='#CCCCCC' align='center'>4</td>
                <td bgcolor='#CCCCCC' align='center'>5</td>
                <td bgcolor='#CCCCCC' align='center'>6</td>
                <td bgcolor='#CCCCCC' align='center'>7</td>
                <td bgcolor='#CCCCCC' align='center'>8</td>
                <td bgcolor='#CCCCCC' align='center'>9</td>
            </tr>
        </thead>
        <tbody>
            @php
                $lcno = 0;
                $lnnilai = 0;
                $total = 0;
            @endphp
            @foreach ($rincian as $row)
                @php
                    $nomor = $row->nomor;
                    $no_sts = $row->no_sts;
                    $tgl_sts = $row->tgl_sts;
                    $keterangan = $row->keterangan;
                    $kd_rek5 = $row->kd_rek6;
                    $nm_rek5 = $row->nm_rek6;
                    $rupiah = $row->rupiah;
                    $no_terima = $row->no_terima;
                    $tgl_terima = $row->tgl_terima;
                    $sumber = $row->sumber;
                    $nm_sumber = $row->nm_sumber;

                    $nilai = rupiah($row->rupiah);

                    if ($tgl_sts == '') {
                        $tgl_sts = '';
                    } else {
                        $tgl_sts = tgl_format_indonesia($tgl_sts);
                    }

                    if ($tgl_terima == '') {
                        $tgl_terima = '';
                    } else {
                        $tgl_terima = tgl_format_indonesia($tgl_terima);
                    }

                @endphp


                @if ($nomor == '1')
                    <tr>
                        <td align='left'><b>{{ $no_sts }}</b></td>
                        <td align='center'><b>{{ $tgl_sts }}</b></td>
                        <td align='left'><b>{{ $keterangan }}</b></td>
                        <td align='center'></td>
                        <td align='center'></td>
                        <td align='right'><b>{{ $nilai }}</b></td>
                        <td align='center'></td>
                        <td align='center'></td>
                        <td align='center'></td>
                    </tr>
                @else
                    <tr>
                        <td align='center' style='border-top:hidden;'></td>
                        <td align='center' style='border-top:hidden;'></td>
                        <td align='center' style='border-top:hidden;'></td>
                        <td align='center'>{{ $kd_rek5 }}</td>
                        <td align='left'>{{ $nm_rek5 }}</td>
                        <td align='right'>{{ $nilai }}</td>
                        <td align='left'>{{ $no_terima }}</td>
                        <td align='center'>{{ $tgl_terima }}</td>
                        <td align='left'>{{ $nm_sumber }}</td>
                    </tr>

                    @php
                        $total = $total + $rupiah;
                    @endphp
                @endif
            @endforeach
            <tr>
                <td align='center' colspan='5'><b>Total</b></td>
                <td align='left' colspan='4'><b>{{ rupiah($total) }}</b></td>
            </tr>
        </tbody>
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
