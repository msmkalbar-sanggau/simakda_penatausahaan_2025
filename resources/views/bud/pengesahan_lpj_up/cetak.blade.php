<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CETAK RINCIAN</title>
    <style>
        #header>thead>tr>th {
            background-color: #CCCCCC;
        }

        .rincian>tbody>tr>td {
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div style="text-align: center">
        <table style="width: 100%;font-family:Open Sans;">
            <tr>
                <td style="font-size:18px"><b>PEMERINTAH {{ strtoupper($header->nm_pemda) }}</b></td>
            </tr>
            <tr>
                <td style="font-size:18px"><b>LAPORAN PERTANGGUNG JAWABAN UANG PERSEDIAAN</b></td>
            </tr>
        </table>
    </div>

    <div style="text-align: left;padding-top:20px">
        <table style="width: 100%;font-family:Open Sans;" class="rincian">
            <tr>
                <td>OPD</td>
                <td>:</td>
                <td>{{ $kd_skpd }} - {{ nama_skpd($kd_skpd) }}</td>
            </tr>
            @if ($pilihan == '0' || $pilihan == '1')
                <tr>
                    <td>No. LPJ</td>
                    <td>:</td>
                    <td>{{ $no_lpj }}</td>
                </tr>
            @else
                <tr>
                    <td>PERIODE</td>
                    <td>:</td>
                    <td>{{ tanggal($lpj->tgl_awal) }} s/d {{ tanggal($lpj->tgl_akhir) }}</td>
                </tr>
                <tr>
                    <td>Kegiatan</td>
                    <td>:</td>
                    <td>{{ $kegiatan }} - {{ nama_kegiatan($kegiatan) }}</td>
                </tr>
            @endif
        </table>
    </div>
    <br>
    <table style="border-collapse:collapse;font-family: Open Sans;width:100%" id="header" border="1"
        class="rincian">
        <thead>
            <tr>
                <th>NO</th>
                <th>KODE REKENING</th>
                <th>URAIAN</th>
                <th>JUMLAH</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
                $total = 0;
            @endphp
            @foreach ($data_lpj as $data)
                @if ($pilihan == '0')
                    @if ($data->urut == 1)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            <td style="text-align: center"><i><b>{{ $i }}</b></i></td>
                            <td><i><b>{{ $data->kode }}</b></i></td>
                            <td><i><b>{{ $data->uraian }}</b></i></td>
                            <td style="text-align: right"><i><b>{{ rupiah($data->nilai) }}</b></i></td>
                        </tr>
                    @elseif ($data->urut == 2)
                        <tr>
                            <td style="text-align: center"><b></b></td>
                            <td><b>{{ $data->kode }}</b></td>
                            <td><b>{{ $data->uraian }}</b></td>
                            <td style="text-align: right"><b>{{ rupiah($data->nilai) }}</b></td>
                        </tr>
                    @elseif ($data->urut == 7)
                        @php
                            $total += $data->nilai;
                        @endphp
                        <tr>
                            <td style="text-align: center"></td>
                            <td>{{ $data->kode }}</td>
                            <td>{{ $data->uraian }}</td>
                            <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td style="text-align: center"></td>
                            <td>{{ $data->kode }}</td>
                            <td>{{ $data->uraian }}</td>
                            <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                        </tr>
                    @endif
                @elseif ($pilihan == '1')
                    @if ($data->urut == 1)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            <td style="text-align: center"><b>{{ $i }}</b></td>
                            <td><b>{{ $data->kode }}</b></td>
                            <td><b>{{ $data->uraian }}</b></td>
                            <td style="text-align: right"><b>{{ rupiah($data->nilai) }}</b></td>
                        </tr>
                    @elseif ($data->urut == 2)
                        @php
                            $total += $data->nilai;
                        @endphp
                        <tr>
                            <td style="text-align: center"><b></b></td>
                            <td><b>{{ $data->kode }}</b></td>
                            <td><b>{{ $data->uraian }}</b></td>
                            <td style="text-align: right"><b>{{ rupiah($data->nilai) }}</b></td>
                        </tr>
                    @else
                        <tr>
                            <td style="text-align: center"></td>
                            <td>{{ $data->kode }}</td>
                            <td>{{ $data->uraian }}</td>
                            <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                        </tr>
                    @endif
                @elseif ($pilihan == '2')
                    @if ($data->urut == 1)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            <td style="text-align: center"><i><b>{{ $i }}</b></i></td>
                            <td><i><b>{{ $data->kode }}</b></i></td>
                            <td><i><b>{{ $data->uraian }}</b></i></td>
                            <td style="text-align: right"><i><b>{{ rupiah($data->nilai) }}</b></i></td>
                        </tr>
                    @elseif ($data->urut == 2)
                        <tr>
                            <td style="text-align: center"><b></b></td>
                            <td><b>{{ $data->kode }}</b></td>
                            <td><b>{{ $data->uraian }}</b></td>
                            <td style="text-align: right"><b>{{ rupiah($data->nilai) }}</b></td>
                        </tr>
                    @else
                        @php
                            $total += $data->nilai;
                        @endphp
                        <tr>
                            <td style="text-align: center"></td>
                            <td>{{ $data->rek }}</td>
                            <td>{{ $data->uraian }}</td>
                            <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                        </tr>
                    @endif
                @endif
            @endforeach
            <tr>
                <td style="height: 15px"></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="text-align: right"><b>Total</b></td>
                <td style="text-align: right"><b>{{ rupiah($total) }}</b></td>
            </tr>
            @if ($pilihan == '0' || $pilihan == '1')
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: right"><b>Uang Persediaan Awal Periode</b></td>
                    <td style="text-align: right"><b>{{ rupiah($persediaan) }}</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: right"><b>Uang Persediaan Akhir Periode</b></td>
                    <td style="text-align: right"><b>{{ rupiah($persediaan - $total) }}</b></td>
                </tr>
            @endif
        </tbody>
    </table>

    <br>
    <div style="padding-top:20px">
        <table class="table" style="width: 100%;font-family:Open Sans;" class="rincian">
            <tr>
                <td style="text-align: center">Disetujui <br>Kuasa Bendahara Umum Daerah</td>
                <td style="margin: 2px 0px;text-align: center">
                    {{ $daerah->daerah }},
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <br> Telah diverifikasi <br>Petugas
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center">

                </td>
                <td style="padding-bottom: 50px;text-align: center">

                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    <br>
                    <b><u>{{ $ttd->nama }}</u></b> <br>
                    {{ $ttd->pangkat }} <br>
                    NIP. {{ $ttd->nip }}
                </td>
                <td style="text-align: center">
                    ___________________
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
