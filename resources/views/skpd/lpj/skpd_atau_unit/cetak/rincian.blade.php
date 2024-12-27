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
                <td><b>LAPORAN PERTANGGUNG JAWABAN UANG PERSEDIAAN</b></td>
            </tr>
            <tr>
                <td><b>{{ Str::upper($bendahara->jabatan) }}</b></td>
            </tr>
        </table>
    </div>

    <div style="text-align: left;padding-top:20px">
        <table style="width: 100%">
            @if ($pilihan == '0' || $pilihan == '1')
                <tr>
                    <td>Periode</td>
                    <td>:</td>
                    <td>{{ $lpj->tgl_awal }} s/d {{ $lpj->tgl_akhir }}</td>
                </tr>
            @else
                <tr>
                    <td>Sub Kegiatan</td>
                    <td>:</td>
                    <td>{{ $kegiatan }} - {{ nama_kegiatan($kegiatan) }}</td>
                </tr>
            @endif
        </table>
    </div>
    <table style="border-collapse:collapse;font-family: Open Sans;width:100%" id="header" border="1">
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
                            <td>{{ $data->kode }}</td>
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
                    <td style="text-align: right"><b>{{ rupiah($persediaan->nilai) }}</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: right"><b>Uang Persediaan Akhir Periode</b></td>
                    <td style="text-align: right"><b>{{ rupiah($persediaan->nilai - $total) }}</b></td>
                </tr>
            @endif
        </tbody>
    </table>

    <br>
    <div style="padding-top:20px">
        <table class="table" style="width: 100%">
            <tr>
                <td style="text-align: center">MENGETAHUI :</td>
                <td style="margin: 2px 0px;text-align: center">
                    {{ $daerah->daerah }}, {{ tanggal($lpj->tgl_lpj) }}
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
