<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SURAT SP2B</title>
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
            border-left: 1px solid black
        }

        .bawah {
            border-bottom: 1px solid black
        }

        .angka {
            text-align: right
        }
    </style>
</head>


<body>
    <table style="font-family: Open Sans; font-size:14px" width="100%" align="center" border="0" cellspacing="0"
        cellpadding="0">
        <tr>
            <td colspan="2"
                style="text-align: center;font-size:16px;border-bottom:solid 1px black;padding-bottom:4px">
            </td>
        </tr>
        <tr>
            <td style="border-left:solid 1px black"><img
                    src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" alt="" height="80">
            </td>
            <td
                style="text-align: center;font-size:14px;padding-bottom:10px;padding-top:5px;border-right:solid 1px black;">
                <b>PEMERINTAH KABUPATEN SANGGAU <br> SURAT PENGESAHAN PENDAPATAN DAN BELANJA FKTP</b>
            </td>
        </tr>
        <tr>
            <td colspan="2"
                style="text-align: center;font-size:16px;border-bottom:solid 1px black;padding-bottom:2px"></td>
        </tr>
    </table>


    <table style="width: 100%; border-spacing: 30px;" border="1">
        <tbody>
            <tr>
                <td colspan="3" style="text-align: center;font-size:16px;border-bottom:hidden;padding-bottom:20px">
                </td>
                <td colspan="3" style="text-align: center;font-size:16px;border-bottom:hidden;padding-bottom:20px">
                </td>
            </tr>
            <tr>
                <td style="width: 10%; border-right-style:hidden; border-bottom:hidden;">No SP3B</td>
                <td style="width: 1px; border-right-style:hidden; border-bottom:hidden;">:</td>
                <td style="width: 40%; border-bottom:hidden;">{{ $no_sp3b }}</td>
                <td style="width: 10%; border-right-style:hidden; border-bottom:hidden;">Nama BUD</td>
                <td style="width: 1px; border-right-style:hidden; border-bottom:hidden;;">:</td>
                <td style="width: 40%; border-bottom:hidden;">{{ $nama }}</td>
            </tr>
            <tr>
                <td style="width: 10%; border-right-style:hidden; border-bottom:hidden;">Tanggal</td>
                <td style="width: 1px; border-right-style:hidden; border-bottom:hidden;">:</td>
                <td style="width: 40%; border-bottom:hidden;">{{ tanggal($tgl_sp3b) }}</td>
                <td style="width: 10%; border-right-style:hidden; border-bottom:hidden;">Nomor</td>
                <td style="width: 1px; border-right-style:hidden; border-bottom:hidden;">:</td>
                <td style="width: 40%; border-bottom:hidden;"> {{ $nosp2b }}</td>
            </tr>
            <tr>
                <td style="width: 10%; border-right-style:hidden; border-bottom:hidden;">SKPD</td>
                <td style="width: 1px; border-right-style:hidden; border-bottom:hidden;">:</td>
                <td style="width: 40%; border-bottom:hidden;">{{ $skpd }}</td>
                <td style="width: 10%; border-right-style:hidden; border-bottom:hidden;">Tanggal</td>
                <td style="width: 1px; border-right-style:hidden; border-bottom:hidden;">:</td>
                <td style="width: 40%; border-bottom:hidden;">{{ tanggal($tgl_sp2b) }}</td>
            </tr>
            <tr>
                <td style="width: 10%; border-right-style:hidden; border-bottom:hidden;">FKTP</td>
                <td style="width: 1px; border-right-style:hidden; border-bottom:hidden;">:</td>
                <td style="width: 40%; border-bottom:hidden;">{{ $skpd }}</td>
                <td style="width: 10%; border-right-style:hidden; border-bottom:hidden;">Tahun Anggaran</td>
                <td style="width: 1px; border-right-style:hidden; border-bottom:hidden;">:</td>
                <td style="width: 40%; border-bottom:hidden;">{{ $tahun }}</td>
            </tr>
            <tr>
                <td colspan="3"
                    style="text-align: center;font-size:16px;border-bottom:solid 0px black;padding-bottom:50px"></td>
                <td colspan="3"
                    style="text-align: center;font-size:16px;border-bottom:solid 0px black;padding-bottom:50px"></td>
            </tr>

        </tbody>
    </table>


    <table id="rincian" style="width: 100%;" border="1">
        <tbody>
            <tr>
                <td colspan="6" style="text-align: center;font-size:16px;border-bottom:hidden;padding-bottom:20px">
                </td>
            </tr>
            <tr>
                <td colspan="6"style="border-bottom:hidden;">Telah disahkan Pendapatan dan
                    Belanja sejumlah :</td>
            </tr>
            <tr>
                <td rowspan="5" style="border-right:hidden; width: 20%;"></td>
                <td style="width: 2%; border-right-style:hidden; border-bottom:hidden;">1.</td>
                <td style="width: 20%;border-right-style:hidden; border-bottom:hidden;">Saldo Awal</td>
                <td style="width: 40%;border-bottom:hidden; border-top:hidden; ">Rp. {{ rupiah($sawal) }}
                </td>
            </tr>
            <tr>
                <td style="width: 2%; border-right-style:hidden; border-bottom:hidden;">2.</td>
                <td style="width: 20%;border-right-style:hidden; border-bottom:hidden;">Pendapatan</td>
                <td style="width: 40%;border-bottom:hidden;">Rp. {{ rupiah($pend->sld_pend) }}</td>
            </tr>
            <tr>
                <td style="width: 2%; border-right-style:hidden; border-bottom:hidden;">3.</td>
                <td style="width: 20%;border-right-style:hidden; border-bottom:hidden;">Belanja</td>
                <td style="width: 40%;border-bottom:hidden;">Rp. {{ rupiah($belanja->sld_bel) }}</td>
            </tr>
            <tr>
                <td style="width: 2%; border-right-style:hidden;border-bottom:hidden;">4.</td>
                <td style="width: 20%;border-right-style:hidden;border-bottom:hidden;">Saldo Akhir</td>
                <td style="width: 40%;border-bottom:hidden;">Rp.
                    {{ rupiah($sawal + $pend->sld_pend - $belanja->sld_bel) }}</td>
            </tr>
            <tr>
                <td colspan="6"
                    style="text-align: center;font-size:16px;border-bottom:solid 0px black;padding-bottom:100px"></td>
            </tr>
        </tbody>
    </table>
    <table class="table" style="width: 50%; float: right;" border="0">
        <tr>
            <td colspan="6" style="text-align: center;font-size:16px;border-bottom:hidden;padding-bottom:20px"></td>
        </tr>
        <tr>
            <td style="text-align:center;border-bottom:hidden;">
                <b>Sanggau, {{ tanggal($tgl_sp2b) }}</b>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom:80px; text-align:center;border-bottom:hidden;">
                <b>Bendahara Umum Daerah</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:center;border-bottom:hidden;"><u><b>{{ $nama }}</b></u></td>
        </tr>
        <tr>
            <td style="text-align:center;"><b>NIP. {{ $nip }}</b></td>
        </tr>
    </table>

</body>

</html>
