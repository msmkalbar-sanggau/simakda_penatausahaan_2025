<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Rincian</title>
    <style>
        table,
        th,
        td {
            border-collapse: collapse;
        }

        .rincian>tbody>tr>td {
            font-size: 14px
        }
    </style>
</head>

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:12px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" width="7%"
                <?= substr($skpd->kd_skpd, -4) !== '0000' ? 'rowspan="6"' : 'rowspan="5"' ?>>
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="125"
                    height="150" />
            </td>
            <td align="left" style="font-size:18px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px">
                <strong>
                    {{ strtoupper($skpd->nm_org) }}
                </strong>
            </td>
        </tr>
        @if (substr($skpd->kd_skpd, -4) !== '0000')
            <tr>
                <td align="left" style="font-size:18px">
                    <strong>
                        {{ strtoupper($skpd->nm_skpd) }}
                    </strong>
                </td>
            </tr>
        @endif
        <tr>
            <td align="left" style="font-size:18px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:18px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <hr>
    <br>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td style="font-size: 16px"><b>LAMPIRAN SURAT PERINTAH MEMBAYAR</b></td>
        </tr>
        <tr>
            <td style="font-size: 16px"><b>(LAMPIRAN SPM)</b></td>
        </tr>
        <tr>
            <td style="font-size: 16px"><b>{{ $no_spm }}</b></td>
        </tr>
    </table>
    <br>
    <div>
        <table class="table table-striped rincian"
            style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;" border="1">
            <tr>
                <th style="text-align: center">No Urut</th>
                <th style="text-align: center">Kode Rekening</th>
                <th style="text-align: center">Uraian</th>
                <th style="text-align: center">Jumlah</th>
            </tr>
            @php
                $total = 0;
            @endphp
            @foreach ($data_beban as $data)
                <tr>
                    @if ($data->urut == '1')
                        @php
                            $total += $data->nilai;
                        @endphp
                        <td style="text-align: center"><b>{{ $loop->iteration }}</b></td>
                        <td><b>{{ $data->kode }}</b></td>
                        <td><b>{{ $data->nama }}</b></td>
                        <td style="text-align: right"><b>{{ rupiah($data->nilai) }}</b></td>
                    @elseif ($data->urut == '6')
                        <td></td>
                        <td>{{ $data->kode }}</td>
                        <td>{{ $data->nama }}</td>
                        <td style="text-align: right">{{ rupiah($data->nilai) }}</td>
                    @else
                        <td></td>
                        <td><b>{{ $data->kode }}</b></td>
                        <td><b>{{ $data->nama }}</b></td>
                        <td style="text-align: right"><b>{{ rupiah($data->nilai) }}</b></td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td style="text-align: center" colspan="3"><b>JUMLAH</b></td>
                <td style="text-align: right"><b>{{ rupiah($total) }}</b></td>
            </tr>
        </table>
    </div>
    <br>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;" class="rincian">
        <tr>
            <td>Terbilang : <i>{{ ucwords(terbilang($total)) }}</i></td>
        </tr>
    </table>
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;" class="rincian">
            <tr>
                <td style="margin: 2px 0px;text-align: center;padding-left:500px">
                    {{ $daerah->daerah }},
                    @if ($tanpa == 1)
                        ______________{{ $tahun_anggaran }}
                    @else
                        {{ tanggal($data_spm->tgl_spm) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 50px;text-align: center;padding-left:500px">
                    {{ $pa_kpa->jabatan }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:500px">
                    <strong><u>{{ $pa_kpa->nama }}</u></strong> <br>
                    {{ $pa_kpa->pangkat }} <br>
                    NIP. {{ $pa_kpa->nip }}
                </td>
            </tr>
            {{-- <tr>
                <td style="text-align: center;padding-left:500px">{{ $pa_kpa->pangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center;padding-left:500px">NIP. {{ $pa_kpa->nip }}</td>
            </tr> --}}
        </table>
    </div>
</body>

</html>
