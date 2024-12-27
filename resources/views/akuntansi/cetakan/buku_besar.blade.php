<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Buku Besar</title>
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
    <TABLE width="100%">
        <TR>
            <TD align="center" ><B>BUKU BESAR </B></TD>
        </TR>
    </TABLE>
    <TABLE width="100%">
     <TR>
            <TD align="left" width="20%" >SKPD</TD>
            <TD align="left" width="80%" >: {{$skpd}} {{nama_skpd($skpd)}}</TD>
     </TR>
     <TR>
            <TD align="left" width="20%" >Rekening</TD>
            <TD align="left" width="80%" >: {{$rek6}} {{nama_rekening($rek6)}}</TD>
     </TR>
     <TR>
            <TD align="left" width="20%" >Periode</TD>
            <TD align="left" width="80%" >: {{tgl_format_oyoy($dcetak)}} s/d {{tgl_format_oyoy($dcetak2)}}</TD>
     </TR>
    </TABLE>
    <TABLE style="border-collapse:collapse;" width="100%" align="center" border="1" cellspacing="0" cellpadding="4">
    <THEAD>
     <TR>
        <TD width="15%"  bgcolor="#CCCCCC" align="center" >TANGGAL</TD>
        <TD width="35%" bgcolor="#CCCCCC" align="center" >URAIAN</TD>
        <TD width="5%" bgcolor="#CCCCCC" align="center" >REF</TD>
        <TD width="15%" bgcolor="#CCCCCC" align="center" >DEBET</TD>
        <TD width="15%" bgcolor="#CCCCCC" align="center" >KREDIT</TD>
        <TD width="15%" bgcolor="#CCCCCC" align="center" >SALDO</TD>
     </TR>
    </THEAD>
    @php
    $awaldebet = $csql3->debet;
    $awalkredit = $csql3->kredit;
    if ((substr($rek6,0,1)=='8') or (substr($rek6,0,1)=='5') or (substr($rek6,0,2)=='62') or (substr($rek6,0,2)=='62') or (substr($rek6,0,1)=='1')){                    
                        $saldo=$awaldebet-$awalkredit;
                    }else{
                        $saldo=$awalkredit-$awaldebet;
                    } 
                    if($saldo<0){
                    $a='(';
                    $saldox=$saldo*-1;
                    $b=')';
                    } else{
                    $a='';
                    $saldox=$saldo;
                    $b='';  
                    }
    @endphp
     <TR>
        <TD width="15%" align="left" ></TD>
        <TD width="35%" align="left" >saldo awal</TD>
        <TD width="5%" align="left" ></TD>
        <TD width="15%" align="right" ></TD>
        <TD width="15%" align="right" ></TD>
        <TD width="15%" align="right" >{{$a}}{{rupiah($saldox)}}{{$b}}</TD>
     </TR>
    @php
    $idx=1;
    $jdebet=0;
    $jkredit=0;
    $idx=1;
    @endphp
    @foreach($query as $res)
        @php
            $tgl_voucher=$res->tgl_voucher;
                    $ket=$res->ket;
                    $ref=$res->no_voucher;
                    $debet=$res->debet;
                    $kredit=$res->kredit;
                    $idx++;
                    if($debet<0){
                        $debet1=$debet*-1;
                        $c='(';
                        $d=')';
                        }else{
                        $c='';
                        $d='';  
                        $debet1=$debet;
                        }
                    if($kredit<0){
                        $kredit1=$kredit*-1;
                        $e='(';
                        $f=')';
                        }else{
                        $e='';
                        $f='';  
                        $kredit1=$kredit;
                        }   
                    $saldo=$saldo;
                    if ((substr($rek6,0,1)=='8') or (substr($rek6,0,1)=='5') or (substr($rek6,0,2)=='62') or (substr($rek6,0,2)=='62') or (substr($rek6,0,1)=='1')){                    
                        $saldo=$saldo+$debet-$kredit;
                        
                    }else{
                        $saldo=$saldo+$kredit-$debet;
                        
                    }
                    if($saldo<0){
                        $saldo1=$saldo*-1;
                        $i='(';
                        $j=')';
                        }else{
                        $saldo1=$saldo;
                        $i='';
                        $j='';  
                        }
        @endphp
        <TR>
            <TD width="15%" align="left" >{{$tgl_voucher}}</TD>
            <TD width="35%" align="left" >{{$ket}}</TD>
            <TD width="5%" align="left" >{{$ref}}</TD>
            <TD width="15%" align="right" >{{$c}}{{rupiah($debet1)}}{{$d}}</TD>
            <TD width="15%" align="right" >{{$e}}{{rupiah($kredit1)}}{{$f}}</TD>
            <TD width="15%" align="right" >{{$i}}{{rupiah($saldo1)}}{{$j}}</TD>
        </TR>
        @php
            $jdebet=$jdebet+$debet;
            $jkredit = $jkredit + $kredit;
        @endphp

    @endforeach

    @php
        if($jdebet<0){
            $jdebet1=$jdebet*-1;
            $k='(';
            $l=')';
        }else{
            $jdebet1=$jdebet;
            $k='';
            $l='';  
        }
        if($jkredit<0){
            $jkredit1=$jkredit*-1;
            $m='(';
            $n=')';
        }else{
            $jkredit1=$jkredit;
            $m='';
            $n='';  
        }
    @endphp
    <TR>
        <TD width="15%" align="left" ></TD>
        <TD width="35%" align="left" >JUMLAH</TD>
        <TD width="5%" align="left" ></TD>
        <TD width="15%" align="right" >{{$k}} {{rupiah($jdebet1)}} {{$l}}</TD>
        <TD width="15%" align="right" >{{$m}} {{rupiah($jkredit1)}} {{$n}}</TD>
        <TD width="15%" align="right" >  </TD>
    </TR>
    </TABLE>
    
</body>

</html>
