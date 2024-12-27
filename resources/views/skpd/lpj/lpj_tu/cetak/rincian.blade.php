<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CETAK LPJ TU</title>

    <style>
        #rincian>thead>tr>th {
            background-color: #CCCCCC;
        }
    </style>
</head>

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
            <td align="left" style="font-size:14px"><strong>{{ nama_skpd($kd_skpd) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:14px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <hr>

    <div style="text-align: center">
        <table style="width: 100%">
            <tr>
                <td><u><b>LAPORAN PERTANGGUNG JAWABAN TAMBAHAN UANG (TU)</b></u></td>
            </tr>
            <tr>
                <td><b>BENDAHARA PENGELUARAN</b></td>
            </tr>
        </table>
    </div>
    <br>
    <div style="text-align: left;padding-top:20px">
        <table style="width: 100%">
            <tr>
                <td>Program</td>
                <td>:</td>
                <td>{{ $program->kd_program }} - {{ nama_program($program->kd_program) }}</td>
            </tr>
            <tr>
                <td>Kegiatan</td>
                <td>:</td>
                <td>{{ $program->kd_kegiatan }} - {{ nama_keg($program->kd_kegiatan) }}</td>
            </tr>
            <tr>
                <td>Sub Kegiatan</td>
                <td>:</td>
                <td>{{ $program->kd_sub_kegiatan }} - {{ nama_kegiatan($program->kd_sub_kegiatan) }}</td>
            </tr>
            <tr>
                <td>No SP2D</td>
                <td>:</td>
                <td>{{ $no_sp2d }}</td>
            </tr>
        </table>
    </div>

    <br>

    <table id="rincian" style="font-family: Open Sans; font-size:16px;width:100%;border-collapse:collapse"
        border="1">
        <thead>
            <tr>
                <th>KODE REKENING</th>
                <th>URAIAN</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_rincian as $data)
                <tr>
                    <td>{{ Str::substr($data->kd_rek6, -2) == '.1' ? '' : $data->kd_rek6 }}</td>
                    <td>{{ $data->nm_rek6 }}</td>
                    <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="height: 15px"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Total</b></td>
                <td style="text-align: right"><b>{{ rupiah($total) }}</b></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Tambahan Uang Persediaan Awal Periode</b></td>
                <td style="text-align: right"><b>{{ rupiah($persediaan) }}</b></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Tambahan Uang Persediaan Ahir Periode</b></td>
                <td style="text-align: right"><b>{{ rupiah($persediaan - $total) }}</b></td>
            </tr>
        </tbody>
    </table>

    <br>
    <br>
    <div style="padding-top:20px">
        <table class="table" style="width: 100%">
            <tr>
                <td style="text-align: center">Mengetahui</td>
                <td style="margin: 2px 0px;text-align: center">
                    {{ $daerah->daerah }}, {{ tanggal($tgl_ttd) }}
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $pa_kpa->jabatan }}
                </td>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    <b><u>{{ $pa_kpa->nama }}</u></b> <br>
                    {{ $pa_kpa->pangkat }} <br>
                    NIP. {{ $pa_kpa->nip }}
                </td>
                <td style="text-align: center">
                    <b><u>{{ $bendahara->nama }}</u></b> <br>
                    {{ $bendahara->pangkat }} <br>
                    NIP. {{ $bendahara->nip }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
