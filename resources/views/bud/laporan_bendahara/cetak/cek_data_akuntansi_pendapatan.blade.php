<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CEK DATA AKUNTANSI</title>
    <style>
        table {
            border-collapse: collapse
        }

        .t1 {
            font-weight: normal
        }

        #pilihan1>thead>tr>th {
            background-color: #CCCCCC;
            font-weight: normal
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

{{-- <body onload="window.print()"> --}}

<body>
    <table style="border-collapse:collapse;font-family: Open Sans; font-size:16px" width="100%" align="center"
        border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>CEK DATA AKUNTANSI PENDAPATAN ANTARA SPJ DAN LRA</strong>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>{{ strtoupper(MSbulan($bulan)) }}<strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>

    <table style="width: 100%" border="1">
        <thead>
            <tr>
                <th style="font-size:10px"><b>No. Urut</b></th>
                <th style="font-size:10px"><b>Kode</b></th>
                <th style="font-size:10px"><b>Nama SKPD</b></th>
                <th style="font-size:10px"><b>Nilai Terima</b></th>
                <th style="font-size:10px"><b>Nilai Penyetoran</b></th>
                <th style="font-size:10px"><b>Nilai LRA Pendapatan</b></th>
                <th style="font-size:10px"><b>Nilai LRA Pendapatan Inputan</b></th>
                <th style="font-size:10px"><b>Total Nilai LRA Pendapatan</b></th>
                <th style="font-size:10px"><b>Selisih Penerimaan dan Penyetoran</b></th>
                <th style="font-size:10px"><b>Selisih Penyetoran dan Akuntansi</b></th>
                <th style="font-size:10px"><b>Keterangan</b></th>
            </tr>
        </thead>
        <tr>
            <td style="font-size:10px" align="center"><b>1</b></td>
            <td style="font-size:10px" align="center"><b>2</b></td>
            <td style="font-size:10px" align="center"><b>3</b></td>
            <td style="font-size:10px" align="center"><b>4</b></td>
            <td style="font-size:10px" align="center"><b>5</b></td>
            <td style="font-size:10px" align="center"><b>6</b></td>
            <td style="font-size:10px" align="center"><b>7</b></td>
            <td style="font-size:10px" align="center"><b>8</b></td>
            <td style="font-size:10px" align="center"><b>9</b></td>
            <td style="font-size:10px" align="center"><b>10</b></td>
            <td style="font-size:10px" align="center"><b>11</b></td>
        </tr>
        <tbody>
            @php
                $total_tr = 0;
                $total_trnsak = 0;
                $total_LRA = 0;
                $total_LRA_input = 0;
                $total_LRA_tt = 0;
                $total_selisih_tran = 0;
                $total_selisih_jur = 0;
                
            @endphp
            @foreach ($data_akuntansi as $data)
                @php
                    
                    $total_tr += $data->tr_trnsak;
                    $total_trnsak += $data->trnsak;
                    $total_LRA += $data->jurnal;
                    $total_LRA_input += $data->jurnal_input;
                    $total_LRA_tt += $data->tt_jurnal;
                    $total_selisih_tran += $data->sels_tran;
                    $total_selisih_jur += $data->sels_jur;
                    
                @endphp
                @if ($data->ket == 'Nilai Sesuai')
                    <tr>
                        <td style="text-align: center;font-size:12px; width:10px">{{ $loop->iteration }}</td>
                        <td style="font-size:12px; width:120px">{{ $data->kode_skpd }}</td>
                        <td style="font-size:12px; width:150px">{{ $data->nm_skpd }}</td>
                        <td style="font-size:12px; width:150px" class="angka">{{ rupiah($data->tr_trnsak) }}</td>
                        <td style="font-size:12px; width:150px" class="angka">{{ rupiah($data->trnsak) }}</td>
                        <td style="font-size:12px; width:150px" class="angka">{{ rupiah($data->jurnal) }}</td>
                        <td style="font-size:12px; width:150px" class="angka">{{ rupiah($data->jurnal_input) }}</td>
                        <td style="font-size:12px; width:150px" class="angka">{{ rupiah($data->tt_jurnal) }}</td>
                        <td style="font-size:12px; width:150px" class="angka">{{ rupiah($data->sels_tran) }}</td>
                        <td style="font-size:12px; width:150px" class="angka">{{ rupiah($data->sels_jur) }}</td>
                        <td style="font-size:12px; text-align: center">{{ $data->ket }}</td>
                    </tr>
                @else
                    <tr>
                        <td bgcolor="yellow" style="text-align: center;font-size:12px; width:10px">
                            {{ $loop->iteration }}</td>
                        <td bgcolor="yellow" style="font-size:12px; width:120px">{{ $data->kode_skpd }}</td>
                        <td bgcolor="yellow" style="font-size:12px; width:150px">{{ $data->nm_skpd }}</td>
                        <td bgcolor="yellow" style="font-size:12px; width:150px" class="angka">
                            {{ rupiah($data->tr_trnsak) }}</td>
                        <td bgcolor="yellow" style="font-size:12px; width:150px" class="angka">
                            {{ rupiah($data->trnsak) }}</td>
                        <td bgcolor="yellow" style="font-size:12px; width:150px" class="angka">
                            {{ rupiah($data->jurnal) }}</td>
                        <td bgcolor="yellow" style="font-size:12px; width:150px" class="angka">
                            {{ rupiah($data->jurnal_input) }}
                        </td>
                        <td bgcolor="yellow" style="font-size:12px; width:150px" class="angka">
                            {{ rupiah($data->tt_jurnal) }}</td>
                        <td bgcolor="yellow" style="font-size:12px; width:150px" class="angka">
                            {{ rupiah($data->sels_tran) }}</td>
                        <td bgcolor="yellow" style="font-size:12px; width:150px" class="angka">
                            {{ rupiah($data->sels_jur) }}</td>
                        <td bgcolor="yellow" style="font-size:12px; text-align: center">{{ $data->ket }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td colspan="3" style="font-size:12px;text-align:center"><b>JUMLAH </b></td>
                <td class="angka"><b>{{ rupiah($total_tr) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_trnsak) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_LRA) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_LRA_input) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_LRA_tt) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_selisih_tran) }}</b></td>
                <td class="angka"><b>{{ rupiah($total_selisih_jur) }}</b></td>
            </tr>
        </tbody>
    </table>

    {{-- @if (isset($tanda_tangan))
        <div style="padding-top:20px;padding-left:800px">
            <table class="table" style="width:100%">
                <tr>
                    <td style="margin: 2px 0px;text-align: center">
                        @if (isset($tanggal))
                            Pontianak, {{ tanggal($tanggal) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 50px;text-align: center">
                        {{ $tanda_tangan->jabatan }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center"><b><u>{{ $tanda_tangan->nama }}</u></b></td>
                </tr>
                <tr>
                    <td style="text-align: center">{{ $tanda_tangan->pangkat }}</td>
                </tr>
                <tr>
                    <td style="text-align: center">NIP. {{ $tanda_tangan->nip }}</td>
                </tr>
            </table>
        </div>
    @endif --}}
</body>

</html>
