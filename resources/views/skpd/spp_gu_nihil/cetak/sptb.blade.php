<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat SPTB</title>
    <style>
        .unbold {
            font-weight: normal;
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
            <td rowspan="5" align="left" width="7%">
                <img src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75"
                    height="100" />
            </td>
            <td align="left" style="font-size:16px" width="93%">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px" width="93%"><strong>PEMERINTAH
                    {{ strtoupper($header->nm_pemda) }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px">
                <strong>
                    SKPD {{ $skpd->nm_skpd }}
                </strong>
            </td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>TAHUN ANGGARAN {{ tahun_anggaran() }}</strong></td>
        </tr>
        <tr>
            <td align="left" style="font-size:16px"><strong>&nbsp;</strong></td>
        </tr>
    </table>
    <hr>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td style="font-size: 16px">
                <b><u>SURAT PERNYATAAN TANGGUNG JAWABAN BELANJA</u></b>
            </td>
        </tr>
        {{-- <tr>
            <td style="font-size: 16px">
                <b>Nomor : {{ $no_spp }}</b>
            </td>
        </tr> --}}
    </table>
    <br>
    <br>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <tr>
            <td>1. SKPD</td>
            <td>:</td>
            <td>{{ $skpd->kd_skpd }} - {{ $skpd->nm_skpd }}</td>
        </tr>
        <tr>
            <td>2. Satuan Kerja</td>
            <td>:</td>
            <td>{{ $skpd->kd_skpd }} - {{ $skpd->nm_skpd }}</td>
        </tr>
        <tr>
            <td>3. Tanggal/NO.DPA</td>
            <td>:</td>
            <td>
                {{ $data_dpa->tgl_dpa == ''? 'Belum ada Tanggal DPA': \Carbon\Carbon::parse($data_dpa->tgl_dpa)->locale('id')->isoFormat('DD MMMM Y') }}
                dan {{ $data_dpa->no_dpa }}
            </td>
        </tr>
        <tr>
            <td>4. Tahun Anggaran</td>
            <td>:</td>
            <td>{{ tahun_anggaran() }}</td>
        </tr>
        <tr>
            <td>5. Jumlah Belanja</td>
            <td>:</td>
            <td>Rp. {{ rupiah($data->nilai) }}</td>
        </tr>
        <tr>
            <td style="height: 5px"></td>
        </tr>
    </table>

    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif" class="rincian">
        <tr>
            <td>Yang bertanda tangan di bawah ini adalah {{ $pa_kpa->jabatan }} Satuan Kerja {{ $skpd->nm_skpd }}
                Menyatakan bahwa
                saya
                bertanggung jawab penuh atas segala pengeluaran-pengeluaran
                yang telah dibayar lunas oleh Bendahara Pengeluaran kepada yang berhak menerima, sebagaimana tertera
                dalam Laporan Pertanggung Jawaban Ganti Uang di sampaikan oleh Bendahara Pengeluaran</td>
        </tr>
        <tr>
            <td style="height: 5px"></td>
        </tr>
        <tr>
            <td>Bukti-bukti belanja tertera dalam Laporan Pertanggung Jawaban Ganti Uang disimpan sesuai ketentuan yang
                berlaku pada sistem Satuan Kerja {{ $skpd->nm_skpd }}
                untuk kelengkapan administrasi dan keperluan pemeriksaan aparat pengawasan Fungsional</td>
        </tr>
        <tr>
            <td style="height: 5px"></td>
        </tr>
        <tr>
            <td>Demikian Surat Pernyataan ini dibuat dengan sebenarnya</td>
        </tr>
    </table>
    <br>
    <br>
    {{-- tanda tangan --}}
    <div style="padding-top:20px">
        <table class="table rincian" style="width: 100%">
            <tr>
                <td style="margin: 2px 0px;text-align: center;padding-left:500px">
                    {{ $daerah->daerah }},
                    @if ($tanpa == 1)
                        ______________{{ $tahun_anggaran }}
                    @else
                        {{ tanggal($data->tgl_spp) }}
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
                    <b><u>{{ $pa_kpa->nama }}</u></b> <br>
                    {{ $pa_kpa->pangkat }} <br>
                    NIP. {{ $pa_kpa->nip }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
