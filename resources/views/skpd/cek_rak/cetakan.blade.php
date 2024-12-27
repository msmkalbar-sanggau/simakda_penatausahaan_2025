<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lap Perbandingan Anggaran</title>
    <style>
        #header tr>td {
            font-weight: bold;
            text-align: center
        }

        #tabel_angkas,
        th,
        td {
            border-collapse: collapse;
        }

        .angka {
            text-align: right;
        }

        .selisih {
            background-color: #ff5d47;
        }
    </style>
</head>

<body>
    <table style="width: 100%" id="header">
        <tr>
            <td>LAPORAN PERBANDINGAN</td>
        </tr>
        <tr>
            <td>NILAI ANGGARAN DAN NILAI ANGGARAN KAS {{ Str::upper($nama_angkas->nama) }}</td>
        </tr>
        <tr>
            <td>{{ $nama_skpd->nm_skpd }}</td>
        </tr>
    </table>

    <table style="width: 100%" id="tabel_angkas" border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Sub Kegiatan</th>
                <th>Nama Sub Kegiatan</th>
                <th>Kode Rekening</th>
                <th>Nama Rekening</th>
                <th>Anggaran</th>
                <th>Anggaran Kas</th>
                <th>Ket</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cek_rak as $rak)
                @if ($rak->hasil == 'SAMA')
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $rak->kd_kegiatan }}</td>
                        <td>{{ $rak->nm_kegiatan }}</td>
                        <td>{{ $rak->kd_rek6 }}</td>
                        <td>{{ $rak->nm_rek6 }}</td>
                        <td class="angka">{{ rupiah($rak->nilai_ang) }}</td>
                        <td class="angka">{{ rupiah($rak->nilai_kas) }}</td>
                        <td>{{ $rak->hasil }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="selisih" align="center">{{ $loop->iteration }}</td>
                        <td class="selisih">{{ $rak->kd_kegiatan }}</td>
                        <td class="selisih">{{ $rak->nm_kegiatan }}</td>
                        <td class="selisih">{{ $rak->kd_rek6 }}</td>
                        <td class="selisih">{{ $rak->nm_rek6 }}</td>
                        <td class="angka selisih">{{ rupiah($rak->nilai_ang) }}</td>
                        <td class="angka selisih">{{ rupiah($rak->nilai_kas) }}</td>
                        <td class="selisih">{{ $rak->hasil }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- <div style="padding-top:20px">
        <table class="table" style="width:100%">
            <tr>
                <td style="margin: 2px 0px;text-align: center">
                    Pontianak, {{ tanggal($tanggal) }}
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center">
                    {{ $ttd1->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center"><b><u>{{ $ttd1->nama }}</u></b></td>
            </tr>
            <tr>
                <td style="text-align: center">NIP. {{ $ttd1->nip }}</td>
            </tr>
        </table>
    </div> --}}
</body>

</html>
