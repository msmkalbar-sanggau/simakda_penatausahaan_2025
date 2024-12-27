<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register {{$jenis_reg}}</title>
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

{{-- <body onload="window.print()"> --}}
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
        <td style="text-align: center"><b>REGISTER {{$jenis_reg}}</b></td>
    </tr>
    <tr>
        <td style="text-align: center;padding-bottom:30px"><b>PERIODE {{ strtoupper(bulan($bulan)) }}</b></td>
    </tr>

    <TABLE style="border-collapse:collapse;font-size:10px" border="1" cellspacing="2" cellpadding={{$enter}} width="100%" >
        <thead>

            <tr>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='3%' rowspan='3'><b>No.<br>Urut</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%' rowspan='3'><b>Tanggal</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='36%' colspan='6'><b>Nomor</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='19%' rowspan='3'><b>Uraian</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='36%' colspan='6'><b>Jumlah<br>(Rp)</b></td>
            </tr>
            <tr>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%' rowspan='2'><b>UP</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%' rowspan='2'><b>GU</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%' rowspan='2'><b>TU</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='18%' colspan='3'><b>LS</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%' rowspan='2'><b>UP</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%' rowspan='2'><b>GU</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%' rowspan='2'><b>TU</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='18%' colspan='3'><b>LS</b></td>
              </tr>
              <tr>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%'><b>Gaji</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%'><b>Barang&<br>Jasa</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%'><b>LS Pihak Ketiga Lainnya</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%'><b>Gaji</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%'><b>Barang&<br>Jasa</b></td>
                <td style='font-size:10px' bgcolor='#CCCCCC' align='center' width='6%'><b>LS Pihak Ketiga Lainnya</b></td>
              </tr>
              <tr>
                <td style='font-size:10px' align='center' width='3%'><b>1</b></td>
                <td style='font-size:10px' align='center' width='6%'><b>2</b></td>
                <td style='font-size:10px' align='center' width='36%' colspan='6'><b>3</b></td>
                <td style='font-size:10px' align='center' width='19%'><b>4</b></td>
                <td style='font-size:10px' align='center' width='36%' colspan='6'><b>5</b></td>
              </tr>
              </thead>

            @php
                $no                 =0;
                $jumlahup           = 0;
                $jumlahgu           = 0;
                $jumlahtu           = 0;
                $jumlahgj           = 0;
                $jumlahbj           = 0;
                $jumlahpk           = 0;
            @endphp
            @foreach ($rincian as $row)
                 @php
					$no                 = $no+1;
                 @endphp
                 @switch($row->jns_spp)
                     @case(1)
                        @php
                            $jumlahup=$jumlahup+$row->nilai;
                        @endphp
                        <tr>
                            <td align='center'  style='font-size:10px'>{{{$no}}}</td>
                            <td align='left'  style='font-size:10px'>{{tanggal_indonesia($row->tanggal)}}</td>
                            <td align='left'  style='font-size:10px'>{{$row->nomor}}</td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'>{{$row->keperluan}}</td>
                            <td align='right'  style='font-size:10px'>{{rupiah($row->nilai)}}</td>
                            <td align='right'  style='font-size:10px'></td>
                            <td align='right'  style='font-size:10px'></td>
                            <td align='right'  style='font-size:10px'></td>
                            <td align='right'  style='font-size:10px'></td>
                            <td align='right'  style='font-size:10px'></td>
                        </tr>
                         @break
                     @case(2)
                        @php
                            $jumlahgu=$jumlahgu+$row->nilai;
                        @endphp
                        <tr>
                            <td align='center'  style='font-size:10px'>{{$no}}</td>
                            <td align='left'  style='font-size:10px'>{{tanggal_indonesia($row->tanggal)}}</td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'>{{$row->nomor}}</td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'></td>
                            <td align='left'  style='font-size:10px'>{{$row->keperluan}}</td>
                            <td align='right'  style='font-size:10px'></td>
                            <td align='right'  style='font-size:10px'>{{rupiah($row->nilai)}}</td>
                            <td align='right'  style='font-size:10px'></td>
                            <td align='right'  style='font-size:10px'></td>
                            <td align='right'  style='font-size:10px'></td>
                            <td align='right'  style='font-size:10px'></td>
                        </tr>
                         @break
                         @case(3)
                            @php
                                $jumlahtu=$jumlahtu+$row->nilai;
                            @endphp
                            <tr>
                                <td align='center'  style='font-size:10px'>{{$no}}</td>
                                <td align='left'  style='font-size:10px'>{{tanggal_indonesia($row->tanggal)}}</td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'>{{$row->nomor}}</td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'>{{$row->keperluan}}</td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'>{{rupiah($row->nilai)}}</td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                            </tr>
                         @break
                         @case(4)
                            @php
                                $jumlahgj=$jumlahgj+$row->nilai;
                            @endphp
                            <tr>
                                <td align='center'  style='font-size:10px'>{{$no}}</td>
                                <td align='left'  style='font-size:10px'>{{tanggal_indonesia($row->tanggal)}}</td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'>{{$row->nomor}}</td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'>{{$row->keperluan}}</td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'>{{rupiah($row->nilai)}}</td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                            </tr>
                         @break
                         @case(5)
                            @php
                                $jumlahpk=$jumlahpk+$row->nilai;
                            @endphp
                            <tr>
                                <td align='center'  style='font-size:10px'>{{$no}}</td>
                                <td align='left'  style='font-size:10px'>{{tanggal_indonesia($row->tanggal)}}</td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'>{{$row->nomor}}</td>
                                <td align='left'  style='font-size:10px'>{{$row->keperluan}}</td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'>{{rupiah($row->nilai)}}</td>
                            </tr>
                         @break
                         @case(6)
                            @php
                                $jumlahbj=$jumlahbj+$row->nilai;
                            @endphp
                            <tr>
                                <td align='center'  style='font-size:10px'>{{$no}}</td>
                                <td align='left'  style='font-size:10px'>{{tanggal_indonesia($row->tanggal)}}</td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'>{{$row->nomor}}</td>
                                <td align='left'  style='font-size:10px'></td>
                                <td align='left'  style='font-size:10px'>{{$row->keperluan}}</td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'></td>
                                <td align='right'  style='font-size:10px'>{{rupiah($row->nilai)}}</td>
                                <td align='right'  style='font-size:10px'></td>
                            </tr>
                         @break

                 @endswitch


            @endforeach

                <tr>
                    <td colspan='9' align='center' width='3%' style='font-size:10px'>J U M L A H</td>
                    <td align='right' width='6%' style='font-size:10px'>{{rupiah($jumlahup)}}</td>
                    <td align='right' width='6%' style='font-size:10px'>{{rupiah($jumlahgu)}}</td>
                    <td align='right' width='6%' style='font-size:10px'>{{rupiah($jumlahtu)}}</td>
                    <td align='right' width='6%' style='font-size:10px'>{{rupiah($jumlahgj)}}</td>
                    <td align='right' width='6%' style='font-size:10px'>{{rupiah($jumlahbj)}}</td>
                    <td align='right' width='6%' style='font-size:10px'>{{rupiah($jumlahpk)}}</td>
                </tr>


    </table>
    @php
        for ($i = 0; $i <= $enter; $i++) {
            echo "<br>";
        }
    @endphp
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
                    {{ ucwords(strtolower($cari_bendahara->jabatan)) }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;"><b><u>{{ $cari_pa_kpa->nama }}</u></b></td>
                <td style="text-align: center;"><b><u>{{ $cari_bendahara->nama }}</u></b></td>
            </tr>
            <tr>
                <td style="text-align: center;">{{ $cari_pa_kpa->pangkat }}</td>
                <td style="text-align: center;">{{ $cari_bendahara->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">NIP. {{ $cari_pa_kpa->nip }}</td>
                <td style="text-align: center;">NIP. {{ $cari_bendahara->nip }}</td>
            </tr>

        </table>
    </div>
</body>

</html>
