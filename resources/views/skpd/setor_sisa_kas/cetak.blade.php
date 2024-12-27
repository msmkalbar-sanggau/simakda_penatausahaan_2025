<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REG. CP</title>
</head>

<body>
    <TABLE style="border-collapse:collapse; font-size:14px" width="100%" border="0" cellspacing="0" cellpadding="1"
        align=center>
        <TR>
            <TD align="center"><b>PEMERINTAH KABUPATEN SANGGAU </TD>
        </TR>
        <tr></tr>
        <TR>
            <TD align="center"><b>REGISTER CP <br>
            </TD>
        </TR>
    </TABLE><br />
    <TABLE style="border-collapse:collapse; font-size:14px" width="100%">
        <TR>
            <TD align="left" width="20%">OPD</TD>
            <TD align="left" width="100%">: {{ $opd }} {{ $nmopd }}</TD>
        </TR>
        <TR>
            <TD align="left">Kepala OPD</TD>
            <TD align="left">: {{ $pa->nama }}</TD>
        </TR>
    </TABLE>
    <TABLE style="border-collapse:collapse; font-size:14px" width="100%" border="1" cellspacing="2"
        cellpadding="2" align="center">
        <thead>
            <TR>
                <TD rowspan="4" bgcolor="#CCCCCC" align="center"><b>No.</b></TD>
                <TD rowspan="4" bgcolor="#CCCCCC" align="center"><b>Tanggal CP</b></TD>
                <TD rowspan="4" bgcolor="#CCCCCC" align="center"><b>No STS </b></TD>
                <TD rowspan="4" bgcolor="#CCCCCC" align="center"><b>No SP2D</b></TD>
                <TD rowspan="4" bgcolor="#CCCCCC" align="center"><b>Uraian</b></TD>
                <TD colspan="5" bgcolor="#CCCCCC" align="center"><b>Jumlah CP</b></TD>
            </TR>
            <TR>
                <TD rowspan="3" bgcolor="#CCCCCC" align="center"><b>UP/GU/TU</b></TD>
                <TD colspan="4" bgcolor="#CCCCCC" align="center"><b>LS</b></TD>
            </TR>
            <TR>
                <TD colspan="3" bgcolor="#CCCCCC" align="center"><b>Gaji</b></TD>
                <TD rowspan="2" bgcolor="#CCCCCC" align="center"><b>Barang<br>Jasa</b></TD>
            </TR>
            <TR>
                <TD bgcolor="#CCCCCC" align="center"><b>Pot. Lain</b></TD>
                <TD bgcolor="#CCCCCC" align="center"><b>HKPG</b></TD>
                <TD bgcolor="#CCCCCC" align="center"><b>CP</b></TD>
            </TR>
        </thead>
        <tbody>
            @php
                $lcno = 0;
                $tot_up_gu = 0;
                $tot_pot_lain = 0;
                $tot_hkpg = 0;
                $tot_cp = 0;
                $tot_ls = 0;
            @endphp

            @foreach ($detail as $row)
                @php
                    $tgl_sts = $row->tgl_sts;
                    $no_sts = $row->no_sts;
                    $no_sp2d = $row->no_sp2d;
                    $keterangan = $row->keterangan;
                    $up_gu = $row->up_gu;
                    $pot_lain = $row->pot_lain;
                    $hkpg = $row->hkpg;
                    $cp = $row->cp;
                    $ls = $row->ls;
                    // $lcno = $lcno + 1;
                    $tot_up_gu += $row->up_gu;
                    $tot_pot_lain += $row->pot_lain;
                    $tot_hkpg += $row->hkpg;
                    $tot_cp += $row->cp;
                    $tot_ls += $row->ls;
                @endphp
                <tr>
                    <td valign="top" align="center">
                        {{ $loop->iteration }} </td>
                    <td valign="top" align="left">{{ tgl_format_indonesia($tgl_sts) }}</td>
                    <td valign="top" align="center">{{ $no_sts }}</td>
                    <td valign="top" align="left">{{ $no_sp2d }}</td>
                    <td valign="top" align="left">{{ $keterangan }}</td>
                    <td valign="top" align="right">{{ rupiah($up_gu) }}</td>
                    <td valign="top" align="right">{{ rupiah($pot_lain) }}</td>
                    <td valign="top" align="right">{{ rupiah($hkpg) }}</td>
                    <td valign="top" align="right">{{ rupiah($cp) }}</td>
                    <td valign="top" align="right">{{ rupiah($ls) }}</td>
                </tr>
            @endforeach
        </tbody>
        @foreach ($detail as $row)
            @php
                
            @endphp
        @endforeach
        <tr>
            <td colspan="5" valign="top" align="center"><b>J U M L A H</b></td>
            <td valign="top" align="right"><b>{{ rupiah($tot_up_gu) }}</b></td>
            <td valign="top" align="right"><b>{{ rupiah($tot_pot_lain) }}</b></td>
            <td valign="top" align="right"><b>{{ rupiah($tot_hkpg) }}</b></td>
            <td valign="top" align="right"><b>{{ rupiah($tot_cp) }}</b></td>
            <td valign="top" align="right"><b>{{ rupiah($tot_ls) }}</b></td>
        </tr>

    </TABLE>
    <TABLE style="font-size:14px;" width="100%" align="center">
        <TR>
            <TD width="50%" align="center"><b>&nbsp;</TD>
            <TD width="50%" align="center"><b>&nbsp;</TD>
        </TR>
        <TR>
            <TD width="50%" align="center">Mengetahui,</TD>
            <TD width="50%" align="center">Kabupaten Sanggau, {{ tanggal($ttd) }}</TD>
        </TR>
        <TR>
            <TD width="50%" align="center">{{ $pa->jabatan }}</TD>
            <TD width="50%" align="center">{{ $bend->jabatan }}</TD>
        </TR>
        <TR>
            <TD width="50%" align="center"><b>&nbsp;</TD>
            <TD width="50%" align="center"><b>&nbsp;</TD>
        </TR>
        <TR>
            <TD width="50%" align="center"><b>&nbsp;</TD>
            <TD width="50%" align="center"><b>&nbsp;</TD>
        </TR>
        <TR>
            <TD width="50%" align="center"><b><u>{{ $pa->nama }}</u></b><br>{{ $pa->pangkat }}</TD>
            <TD width="50%" align="center"><b><u>{{ $bend->nama }}</u></b><br>{{ $bend->pangkat }}</TD>
        </TR>
        <TR>
            <TD width="50%" align="center">{{ $pa->nip }}</TD>
            <TD width="50%" align="center">{{ $bend->nip }}</TD>
        </TR>

    </TABLE><br />

</body>

</html>
