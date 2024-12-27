<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LRA PERDA I.4 URUSAN</title>
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

<body >
{{-- <body> --}}
    @php
    $daftar_bulan = array(
            '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        $bulan=$daftar_bulan[$bulan];
    @endphp
    <table style="border-collapse:collapse;" width="100%" align="center" border="0" cellspacing="0" cellpadding="4">
            <tr>
                <td align="center" style="border-left:hidden;border-bottom:hidden"><strong>REGISTER SPJ <br> {{ strtoupper($bulan) }}</strong></td>
            </tr>

        </table>

    <hr>
 
    {{-- isi --}}
    <table  style="border-collapse:collapse; font-size:12px" width="100%" align="center" border="1" cellspacing="0" cellpadding="2">
        <thead>
            <tr>
                <td align="center" bgcolor="#CCCCCC" width="2%" >No</td>
                <td align="center" bgcolor="#CCCCCC" width="25%"  >Uraian</td>
                <td align="center" bgcolor="#CCCCCC" width="10%"  >Tanggal Terima</td>
                <td align="center" bgcolor="#CCCCCC" width="10%"  >Realisasi Terima</td>
                <td align="center" bgcolor="#CCCCCC" width="10%"  >Realisasi Setor</td>
                <td align="center" bgcolor="#CCCCCC" width="10%"  >Sisa</td>
                <td align="center" bgcolor="#CCCCCC" width="5%"  >SPJ</td>
                <td align="center" bgcolor="#CCCCCC" width="5%"  >BKU</td>
                <td align="center" bgcolor="#CCCCCC" width="5%"  >Rek. Koran</td>
               
                <td align="center" bgcolor="#CCCCCC" width="5%"  >STS</td>
                <td align="center" bgcolor="#CCCCCC" width="10%"  >Keterangan</td>
                <td align="center" bgcolor="#CCCCCC" width="3%"  >Check</td>
            </tr>
            
        </thead>
                @php
                    
					$total_terima   = 0;
					$total_keluar   = 0;
					$no          = 0;
                @endphp
                    @foreach ($rincian as $row)
                        @php
                               $no=$no+1;
                               $kd_skpd = $row->kd_skpd;
                               $nm_skpd = $row->nm_skpd;
                               $real_terima = $row->real_terima;
                               $real_setor = $row->real_setor;
                               $sisa = $row->sisa;
                               $tgl_terima = $row->tgl_terima;
                               $tanggal = empty($tgl_terima) || $tgl_terima == '1900-01-01' ? '-' :$tgl_terima;
                               $ket = $row->ket;
                               $spj = $row->spj=='1' ? '&#10003;' : '';
                               $bku = $row->bku=='1' ? '&#10003;' : '';
                               $koran = $row->koran=='1' ? '&#10003;' : '';
                               $sts = $row->sts=='1' ? '&#10003;' : '';
                               $cek = $row->cek=='1' ? '&#10003;' : '';
                               

                        @endphp


                              
                        
                        
                        <tr>
                            <td align='center' >{{$no}}</td>
                            <td>{{$nm_skpd}}</td>
                            <td align='center' >{{$tanggal}}</td>
                            <td align='right' >{{rupiah($real_terima)}}</td>
                            <td align='right' >{{rupiah($real_setor)}}</td>
                            <td align='right' >{{rupiah($sisa)}}</td>
                            <td align='center' ><?= $spj ?></td>
                            <td align='center' ><?=$bku ?></td>
                            <td align='center' ><?=$koran?></td>
                            
                            <td align='center' ><?=$sts?></td>
                            <td>{{$ket}}</td>
                            <td align='center' ><?=$cek?></td>
                        </tr>

                                

                    
                @endforeach

    </table>
    {{-- isi --}}
    

    {{-- tanda tangan --}}
    
</body>

</html>
