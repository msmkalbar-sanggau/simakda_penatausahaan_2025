<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LRA SAP SEMESTER</title>
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

<body onload="window.print()">
{{-- <body> --}}
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td rowspan="5" align="left" width="7%">
                    <img src="{{asset('template/assets/images/'.$header->logo_pemda_hp) }}"  width="75" height="100" />
                </td>
                <td align="left" style="font-size:18px" width="93%">&nbsp;</td>
                <td rowspan="5" align="left" width="7%">
                   &nbsp;
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size:18px" width="93%"><strong>PEMERINTAH {{ strtoupper($header->nm_pemda) }}</strong></td>
            </tr>
            <tr>
                <td align="center" style="font-size:18px" ><strong>LAPORAN REALISASI {{strtoupper(bulan($judul))}} ANGGARAN PENDAPATAN DAN BELANJA DAERAH</strong></td>
            </tr>
            <tr>
                <td align="center" style="font-size:18px" ><strong>PROGNOSIS {{12-$judul}} ({{strtoupper(bulan(12-$judul))}}) BULAN BERIKUTNYA<br> TAHUN ANGGARAN {{ tahun_anggaran() }} </strong></td>
            </tr>
            <tr>
                <td align="left" style="font-size:18px" ><strong>&nbsp;</strong></td>
            </tr>
        </table>

    <hr>
 
    {{-- isi --}}
    <table style='border-collapse:collapse;;font-family: Open Sans; font-size:14px' width='100%' align='center' border='1' cellspacing='1' cellpadding='$spasi'>
        <thead>
            <tr>
                <td style="font-size:14px;font-family:Open Sans" width='7%' align='center' bgcolor='#CCCCCC' ><b>KD REK</b></td>
                <td style="font-size:14px;font-family:Open Sans" width='32%' align='center' bgcolor='#CCCCCC' ><b>URAIAN</b></td>
                <td style="font-size:14px;font-family:Open Sans" width='15%' align='center' bgcolor='#CCCCCC' ><b>JUMLAH ANGGARAN</b></td>
                <td style="font-size:14px;font-family:Open Sans" width='15%' align='center' bgcolor='#CCCCCC' ><b>REALISASI <br>{{$pilih}}<br> {{strtoupper(bulan($judul))}}</b></td>
                <td style="font-size:14px;font-family:Open Sans" width='15%' align='center' bgcolor='#CCCCCC' ><b>SISA ANGGARAN</b></td>
                <td style="font-size:14px;font-family:Open Sans" width='15%' align='center' bgcolor='#CCCCCC' ><b>PROGNOSIS</b></td>
                <td style="font-size:14px;font-family:Open Sans" width='10%' align='center' bgcolor='#CCCCCC' ><b>%</b></td>
            </tr>
            <tr>
               <td style="font-size:14px;font-family:Open Sans" align='center' bgcolor='#CCCCCC' >1</td> 
               <td style="font-size:14px;font-family:Open Sans" align='center' bgcolor='#CCCCCC' >2</td> 
               <td style="font-size:14px;font-family:Open Sans" align='center' bgcolor='#CCCCCC' >3</td> 
               <td style="font-size:14px;font-family:Open Sans" align='center' bgcolor='#CCCCCC' >4</td> 
               <td style="font-size:14px;font-family:Open Sans" align='center' bgcolor='#CCCCCC' >5</td> 
               <td style="font-size:14px;font-family:Open Sans" align='center' bgcolor='#CCCCCC' >6</td> 
               <td style="font-size:14px;font-family:Open Sans" align='center' bgcolor='#CCCCCC' >7</td> 
            </tr>
            </thead>
                @php
                    $ang_surplus = $sus->ang_surplus;
                    $nil_surplus = $sus->nil_surplus;
                    $ang_neto    = $sus->ang_neto;
                    $nil_neto    = $sus->nil_neto;
                    $sisa_surplus = $ang_surplus - $nil_surplus;
                    $sisa_neto = $ang_neto - $nil_neto;
                    $ang_silpa = $ang_surplus+$ang_neto;
                    $nil_silpa = $nil_surplus+$nil_neto;
                    $sisa_silpa = $ang_silpa - $nil_silpa;

                        if (($ang_surplus == 0) || ($ang_surplus == '')) {
                            $persen_surplus = 0;
                        } else {
                            $persen_surplus = $nil_surplus / $ang_surplus * 100;
                        }
                        if (($ang_neto == 0) || ($ang_neto == '')) {
                            $persen_neto = 0;
                        }else {
                            $persen_neto = $nil_neto / $ang_neto * 100;
                        }
                        if (($ang_silpa == 0) || ($ang_silpa == '')) {
                            $persen_silpa = 0;
                        }else {
                            $persen_silpa = ($nil_silpa / $ang_silpa) * 100;
                        }

                        if ($ang_surplus < 0) {
                            $ang_surplus = $ang_surplus * -1;
                            $aa = '(';
                            $bb = ')';
                        }else {
                            $ang_surplus = $ang_surplus;
                            $aa = '';
                            $bb = '';
                        }

                        if ($nil_surplus < 0) {
                            $nil_surplus = $nil_surplus * -1;
                            $c = '(';
                            $d = ')';
                        } else {
                            $nil_surplus = $nil_surplus;
                            $c = '';
                            $d = '';
                        }

                        if ($ang_neto < 0) {
                            $ang_neto = $ang_neto * -1;
                            $e = '(';
                            $f = ')';
                        }else {
                            $ang_neto = $ang_neto;
                            $e = '';
                            $f = '';
                        }

                        if ($nil_neto < 0) {
                            $nil_neto = $nil_neto * -1;
                            $g = '(';
                            $h = ')';
                        } else {
                            $nil_neto = $nil_neto;
                            $g = '';
                            $h = '';
                        }

                        if ($sisa_surplus < 0) {
                            $sisa_surplus = $sisa_surplus * -1;
                            $i = '(';
                            $j = ')';
                        }else {
                            $sisa_surplus = $sisa_surplus;
                            $i = '';
                            $j = '';
                        }

                        if ($sisa_neto < 0) {
                            $sisa_neto = $sisa_neto * -1;
                            $k = '(';
                            $l = ')';
                        } else {
                            $sisa_neto = $sisa_neto;
                            $k = '';
                            $l = '';
                        }

                        if ($ang_silpa < 0) {
                            $ang_silpa = $ang_silpa * -1;
                            $m = '(';
                            $n = ')';
                        } else {
                            $ang_silpa = $ang_silpa;
                            $m = '';
                            $n = '';
                        }

                        if ($nil_silpa < 0) {
                            $nil_silpa = $nil_silpa * -1;
                            $o = '(';
                            $p = ')';
                        } else {
                            $nil_silpa = $nil_silpa;
                            $o = '';
                            $p = '';
                        }

                        if ($sisa_silpa < 0) {
                            $sisa_silpa = $sisa_silpa * -1;
                            $q = '(';
                            $r = ')';
                        } else {
                            $sisa_silpa = $sisa_silpa;
                            $q = '';
                            $r = '';
                        }
					$total_terima   = 0;
					$total_keluar   = 0;
					$nomor          = 0;
                @endphp
                    @foreach ($rincian as $row)
                        @php
                            $kd_rek         = $row->kd_rek;
                            $nm_rek         = $row->nama;
                            $nil_ang        = $row->anggaran;
                            $group_id       = $row->group_id;
                            $bold           = $row->is_bold;
                            $show_kd_rek    = $row->is_show_kd_rek;
                            $right_align    = $row->is_right_align;
                
                            $realisasi   = $row->realisasi;
                            $sisa           = $nil_ang - $realisasi;
                            
                            if (($nil_ang == 0) || ($nil_ang == '')) {
                            $persen = 0;
                            } else {
                                $persen = $realisasi / $nil_ang * 100;
                            }
                            $sisa1          = $sisa < 0 ? $sisa * -1 : $sisa;
                            $a              = $sisa < 0 ? '(' : '';
                            $b              = $sisa < 0 ? ')' : '';
                            $leng           = strlen($kd_rek);
                        
                        if ($group_id==1 and $kd_rek==4) {
                            $nanggaran_pendapatan = $nil_ang;
                            $nrealisasi_pendapatan = $realisasi;
                        }else{
                            $nanggaran_pendapatan = $nil_ang;
                            $nrealisasi_pendapatan = $realisasi;
                        }
                        
                        if ($group_id==1 and $kd_rek==5) {
                            $nanggaran_belanja = $nil_ang;
                            $nrealisasi_belanja = $realisasi;
                        }else{
                            $nanggaran_belanja = $nil_ang;
                            $nrealisasi_belanja = $realisasi;
                        }


                        if ($group_id==2 and $row->kd_rek==61) {
                            $apenerimaan_pembiayaan = $nil_ang;
                            $bpenerimaan_pembiayaan = $realisasi;
                            
                        }else{
                            $apenerimaan_pembiayaan = $nil_ang;
                            $bpenerimaan_pembiayaan = $realisasi;
                            
                        }
                        
                        if ($group_id==2 and $row->kd_rek==62) {
                            $apengeluaran_pembiayaan=$nil_ang;
                            $bpengeluaran_pembiayaan=$realisasi;
                        }else{
                            $apengeluaran_pembiayaan=$nil_ang;
                            $bpengeluaran_pembiayaan=$realisasi;
                        }

                        $persenj         = !empty($nanggaran_pendapatan-$nanggaran_belanja) || ($nanggaran_pendapatan-$nanggaran_belanja) == 0 ? 0 : (($nrealisasi_pendapatan-$nrealisasi_belanja)/($nanggaran_pendapatan-$nanggaran_belanja)) * 100;

                        $persenpem         = !empty($apenerimaan_pembiayaan-$apengeluaran_pembiayaan) || ($apenerimaan_pembiayaan-$apengeluaran_pembiayaan) == 0 ? 0 : (($bpenerimaan_pembiayaan-$bpengeluaran_pembiayaan)/($apenerimaan_pembiayaan-$apengeluaran_pembiayaan)) * 100;





                    @endphp
                    @if ($show_kd_rek==1)
                        @php
                            $kd_rek=$kd_rek;
                        @endphp
                    @else
                        @php
                            $kd_rek="";
                        @endphp
                    @endif
                    


                              
                        @if ($group_id == 0)
                        <tr>
                            <td style="font-size:14px;font-family:Open Sans" colspan="7">&nbsp;</td>
                        </tr>
                        @if ($row->kd_rek == 45)       
                            <tr>
                                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right"  valign="top" style="padding-left: 10px"><b>{{$nm_rek}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$aa}}{{ rupiah($ang_surplus) }}{{$bb}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$c}}{{ rupiah($nil_surplus) }}{{$d}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$i}}{{ rupiah($sisa_surplus) }}{{$j}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b> {{$i}}{{ rupiah($sisa_surplus) }}{{$j}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen_surplus, "2", ",", ".")}}</b></td> 
                            </tr>
                        @elseif ($row->kd_rek == 6263)
                            <tr>
                                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right"  valign="top" style="padding-left: 10px"><b>{{$nm_rek}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$e}}{{ rupiah($ang_neto) }}{{$f}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$i}}{{ rupiah($nil_neto) }}{{$j}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$k}}{{ rupiah($sisa_neto) }}{{$l}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b> {{$k}}{{ rupiah($sisa_neto) }}{{$l}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen_neto, "2", ",", ".")}}</b></td> 
                            </tr>        
                        @else
                        <tr>
                            <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right"  valign="top"><b>{{$nm_rek}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($nil_ang, "2", ",", ".")}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($realisasi, "2", ",", ".")}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen, "2", ",", ".")}}</b></td> 
                        </tr>         
                        @endif
                    
            @elseif ($group_id == 1)
                    @if ($row->kd_rek== 5 || $row->kd_rek== 6 || $group_id==0)
                        <tr>
                            <td style="font-size:14px;font-family:Open Sans" colspan="7">&nbsp;</td>
                        </tr>
                        @if ($right_align==1)
                            <tr>
                                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top"><b>{{$nm_rek}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($nil_ang, "2", ",", ".")}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($realisasi, "2", ",", ".")}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen, "2", ",", ".")}}</b></td> 
                            </tr>        
                        @else
                            <tr>
                                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top"><b>{{$nm_rek}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($nil_ang, "2", ",", ".")}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($realisasi, "2", ",", ".")}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen, "2", ",", ".")}}</b></td> 
                            </tr>    
                        @endif
                        
                    @else
                        <tr>
                            <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top"><b>{{$nm_rek}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($nil_ang, "2", ",", ".")}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($realisasi, "2", ",", ".")}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                            <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen, "2", ",", ".")}}</b></td> 
                        </tr>    
                    @endif
                    
            @elseif ($group_id == 2)
                <tr>
                    <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top" style="padding-left: 10px"><b>{{$nm_rek}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($nil_ang, "2", ",", ".")}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($realisasi, "2", ",", ".")}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen, "2", ",", ".")}}</b></td> 
                </tr>     
            @elseif ($group_id == 3)
            <tr>
                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top" style="padding-left: 60px"><b>{{$nm_rek}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($nil_ang, "2", ",", ".")}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($realisasi, "2", ",", ".")}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen, "2", ",", ".")}}</b></td> 
            </tr>
                        
            @elseif ($group_id == 4)
                
            <tr>
                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top" style="padding-left: 20px"><b>{{$nm_rek}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($nil_ang, "2", ",", ".")}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($realisasi, "2", ",", ".")}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen, "2", ",", ".")}}</b></td> 
            </tr>   
            @elseif ($group_id == 5)
                    
            <tr>
                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"><b>{{dotrek($kd_rek)}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top" style="padding-left: 80px"><b>{{$nm_rek}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($nil_ang, "2", ",", ".")}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($realisasi, "2", ",", ".")}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</b></td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{number_format($persen, "2", ",", ".")}}</b></td> 
            </tr>
                
            @elseif ($group_id == 6)
            <tr>
                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top">{{dotrek($kd_rek)}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top" style="padding-left: 30px">{{$nm_rek}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{number_format($nil_ang, "2", ",", ".")}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{number_format($realisasi, "2", ",", ".")}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{number_format($persen, "2", ",", ".")}}</td> 
            </tr>
            @elseif ($group_id == 8)
            <tr>
                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top">{{dotrek($kd_rek)}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top" style="padding-left: 40px">{{$nm_rek}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{number_format($nil_ang, "2", ",", ".")}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{number_format($realisasi, "2", ",", ".")}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{number_format($persen, "2", ",", ".")}}</td> 
            </tr>
            @else
            <tr>
                <td style="font-size:14px;font-family:Open Sans" align="left" valign="top">{{dotrek($kd_rek)}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top" style="padding-left: 50px">{{$nm_rek}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{number_format($nil_ang, "2", ",", ".")}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{number_format($realisasi, "2", ",", ".")}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{$a}} {{number_format($sisa1, "2", ",", ".")}} {{$b}}</td> 
                <td style="font-size:14px;font-family:Open Sans" align="right" valign="top">{{number_format($persen, "2", ",", ".")}}</td> 
            </tr>
            @endif   
                        
                @endforeach
                @php
                    $silpa_anggaran = $nanggaran_pendapatan-$nanggaran_belanja+$apenerimaan_pembiayaan-$apengeluaran_pembiayaan;
                    $silpa_belanja = $nrealisasi_pendapatan-$nrealisasi_belanja+$bpenerimaan_pembiayaan-$bpengeluaran_pembiayaan; 
                    if($silpa_anggaran !=0 && $silpa_belanja!=0){
                        $persensilpa = $silpa_belanja/$silpa_anggaran*100;
                    }elseif($silpa_anggaran == 0 || $silpa_belanja !=0){
                        $persensilpa = 100;
                    }elseif($silpa_anggaran !=0 || $silpa_belanja ==0){
                        $persensilpa = 0;
                    }else{
                        $persensilpa = 0;
                    }
                @endphp
                <tr>
                    <td style="font-size:14px;font-family:Open Sans" align="left" valign="top"></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="left"  valign="top" style="padding-left: 50px"><b>SISA LEBIH PEMBIYAAN ANGGARAN TAHUN BERKENAAN (SILPA)</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$m}}{{ rupiah($ang_silpa) }}{{$n}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$o}}{{ rupiah($nil_silpa) }}{{$p}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$q}}{{ rupiah($sisa_silpa) }}{{$r}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{$q}}{{ rupiah($sisa_silpa) }}{{$r}}</b></td> 
                    <td style="font-size:14px;font-family:Open Sans" align="right" valign="top"><b>{{rupiah($persen_silpa)}}</b></td> 
                </tr>

    </table>
    {{-- isi --}}
    @if ($jenis_ttd !=0)
    <div style="padding-top:20px">
        <table class="table" style="width: 100%;font-size:12px;font-family:Open Sans">
            <tr>
                <td style="font-size:14px;font-family:Open Sans;margin: 2px 0px;text-align: center;" width='50%'>
                    &nbsp;
                </td>
                <td style="font-size:14px;font-family:Open Sans;margin: 2px 0px;text-align: center;" width='50%'>
                    {{ $daerah->daerah }},
                        {{ \Carbon\Carbon::parse($tanggal_ttd)->locale('id')->isoFormat('DD MMMM Y') }}
                </td>
            </tr>
            <tr>
                <td style="font-size:14px;font-family:Open Sans;padding-bottom: 50px;text-align: center;">
                </td>
                <td style="font-size:14px;font-family:Open Sans;padding-bottom: 50px;text-align: center;">
                    {{ ucwords(strtolower($tandatangan->jabatan)) }}
                </td>
            </tr>
            <tr>
                <td style="font-size:14px;font-family:Open Sans;text-align: center;"></td>
                <td style="font-size:14px;font-family:Open Sans;text-align: center;"></td>
            </tr>
            <tr>
                <td style="font-size:14px;font-family:Open Sans;text-align: center;"><b></b></td>
                <td style="font-size:14px;font-family:Open Sans;text-align: center;"><b><u>{{ $tandatangan->nama }}</u></b></td>
            </tr>
            <tr>
                <td style="font-size:14px;font-family:Open Sans;text-align: center;"></td>
                <td style="font-size:14px;font-family:Open Sans;text-align: center;"></td>
            </tr>
            <tr>
                <td style="font-size:14px;font-family:Open Sans;text-align: center;"></td>
                <td style="font-size:14px;font-family:Open Sans;text-align: center;"></td>
            </tr>

        </table>
    </div>
    @endif
    {{-- tanda tangan --}}
    
</body>

</html>
