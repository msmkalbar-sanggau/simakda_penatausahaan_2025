<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .unbold {
            font-weight: normal;
            margin: 2px 0px;
        }

        .table {
            border: 1px solid black;
        }

        table,
        th,
        td {
            border-collapse: collapse;
        }

        .border {
            border: 1px solid black;
        }

        .rincian>tbody>tr>td {
            font-size: 14px
        }
    </style>
</head>

<body>
    <table style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif;text-align:center">
        <tr>
            <td><b>PEMERINTAH KABUPATEN SANGGAU</b></td>
        </tr>
        <tr>
            <td><b>SURAT PERMINTAAN PEMBAYARAN (SPP)</b></td>
        </tr>
        <tr>
            <td>Nomor : {{ $no_spp }}</td>
        </tr>
    </table>
    <br>

    <table class="table table-bordered rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td colspan="4" style="text-align: center;border:1px solid black">{{ $jenisspp }}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;border:1px solid black">{{ $jenis_spp }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">1. Nama SKPD/Unit Kerja</td>
            <td>:</td>
            <td>{{ $skpd->nm_skpd }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">2. Kode dan Nama Sub Kegiatan</td>
            <td>:</td>
            <td>{{ $kd_sub_kegiatan1 }} {{ $nm_sub_kegiatan1 }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">3. Nama Pengguna Anggaran/Kuasa Pengguna Anggaran</td>
            <td>:</td>
            <td>{{ $cari_pa->nama }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">4. Nama PPTK</td>
            <td>:</td>
            <td>{{ $cari_pptk->nama }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">5. Nama Bendahara Pengeluaran/Bendahara Pengeluaran Pembantu</td>
            <td>:</td>
            <td>{{ $cari_bendahara->nama }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">6. NPWP Bendahara Pengeluaran/Bendahara Pengeluaran Pembantu</td>
            <td>:</td>
            <td>{{ $skpd->npwp }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">7. Nama Bank</td>
            <td>:</td>
            <td>{{ $bank->nama }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">8. Nomor Rekening Bank</td>
            <td>:</td>
            <td>{{ $data->no_rek }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">9. Untuk Keperluan</td>
            <td>:</td>
            <td>{{ $data->keperluan }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="2">10. Dasar Pengeluaran</td>
            <td>:</td>
            <td>SPD....Nomor: {{ $data->no_spd }} tanggal
                {{ \Carbon\Carbon::parse($tglspd->tgl_spd)->locale('id')->isoFormat('DD MMMM Y') }}</td>
        </tr>
        <tr style="border:1px solid black">
            <td colspan="3"></td>
            <td>Sebesar: Rp {{ rupiah($nilaispd->nilai) }} <span
                    style="font-style: italic">({{ terbilang($nilaispd->nilai) }})</span></td>
        </tr>
    </table>
    <br>
    <table class="table table-bordered rincian" style="width: 100%;font-family:'Open Sans', Helvetica,Arial,sans-serif">
        <tr>
            <td class="border" style="width:40px">No</td>
            <td class="border" colspan="3" style="text-align: center">Uraian</td>
        </tr>
        <tr>
            <td class="border" style="width: 40px">I</td>
            <td class="border" colspan="2">SPD</td>
            <td class="border"></td>
        </tr>
        @foreach ($dataspd as $spd)
            <tr>
                <td class="border"></td>
                <td class="border">{{ \Carbon\Carbon::parse($spd->tgl_spd)->locale('id')->isoFormat('DD MMMM Y') }}
                </td>
                <td class="border">{{ $spd->no_spd }}</td>
                <td class="border">Rp. {{ rupiah($spd->total) }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="border" style="width: 40px">II</td>
            <td class="border" colspan="2">SP2D Sebelumnya</td>
            <td class="border"></td>
        </tr>
        @foreach ($datasp2d as $sp2d)
            <tr>
                <td class="border"></td>
                <td class="border">
                    {{ \Carbon\Carbon::parse($sp2d->tgl_sp2d)->locale('id')->isoFormat('DD MMMM Y') }}</td>
                <td class="border">{{ $sp2d->no_sp2d }}</td>
                <td class="border">Rp {{ rupiah($sp2d->total) }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="border" colspan="4" style="text-align: center">Pada SPP ini ditetapkan lampiran-lampiran
                yang diperlukan
                sebagaimana tertera pada
                daftar kelengkapan dokumen SPP ini</td>
        </tr>
        @if ($sub_kegiatan == '5.02.00.0.06.62')
            <tr>
                <td colspan="4" style="margin: 2px 0px;text-align: center;padding-left:700px;padding-top:20px">
                    Sanggau,
                    @if ($tanpa == 1)
                        ______________{{ $tahun_anggaran }}
                    @else
                        {{ \Carbon\Carbon::parse($data->tgl_spp)->locale('id')->isoFormat('D MMMM Y') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-bottom: 50px;text-align: center;padding-left:700px">
                    {{ $cari_bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center;padding-left:700px">
                    <b><u>{{ $cari_bendahara->nama }}</u></b> <br>
                    {{ $cari_bendahara->pangkat }} <br>
                    NIP. {{ $cari_bendahara->nip }}
                </td>
            </tr>
            {{-- <tr>
                <td colspan="4" style="text-align: center;padding-left:700px">{{ $cari_bendahara->pangkat }}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center;padding-left:700px">NIP. {{ $cari_bendahara->nip }}
                </td>
            </tr> --}}
        @else
            <tr>
                <td colspan="2" style="margin: 2px 0px;text-align: center;padding-left:100px">
                </td>
                <td colspan="2" style="margin: 2px 0px;padding-top:20px;text-align: center;padding-left:300px"
                    class="unborder">
                    Sanggau,
                    @if ($tanpa == 1)
                        ______________{{ $tahun_anggaran }}
                    @else
                        {{ \Carbon\Carbon::parse($data->tgl_spp)->locale('id')->isoFormat('D MMMM Y') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom: 50px;text-align: center;padding-left:100px">
                    {{-- {{ $cari_pptk->jabatan }} --}}
                    Pejabat Pelaksana Teknis Kegiatan
                </td>
                <td colspan="2" style="padding-bottom: 50px;text-align: center;padding-left:300px">
                    {{ $cari_bendahara->jabatan }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;padding-left:100px">
                    <b><u>{{ $cari_pptk->nama }}</u></b> <br>
                    {{ $cari_pptk->pangkat }} <br>
                    NIP. {{ $cari_pptk->nip }}
                </td>
                <td colspan="2" style="text-align: center;padding-left:300px">
                    <b><u>{{ $cari_bendahara->nama }}</u></b> <br>
                    {{ $cari_bendahara->pangkat }} <br>
                    NIP. {{ $cari_bendahara->nip }}
                </td>
            </tr>
            {{-- <tr>
                <td colspan="2" style="text-align: center;padding-left:100px">{{ $cari_pptk->pangkat }}</td>
                <td colspan="2" style="text-align: center;padding-left:300px">{{ $cari_bendahara->pangkat }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;padding-left:100px">{{ $cari_pptk->nip }}</td>
                <td colspan="2" style="text-align: center;padding-left:300px">NIP. {{ $cari_bendahara->nip }}
                </td>
            </tr> --}}
        @endif
        <tr>
            <td style="font-size: 12px;font-weight:bold;padding-top:30px" colspan="4">Lembar Asli :
                <span style="font-weight: normal">Untuk Pengguna
                    Anggaran/PPK-SKPD</span>
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px;font-weight:bold" colspan="4">Salinan 1 : <span
                    style="font-weight: normal">Untuk Kuasa BUD</span></td>
        </tr>
        <tr>
            <td style="font-size: 12px;font-weight:bold" colspan="4">Salinan 2 : <span
                    style="font-weight: normal">Untuk Bendahara Pengeluaran/PPTK</span>
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px;text-align:left;font-weight:bold" colspan="4">Salinan 3 : <span
                    style="font-weight: normal">Untuk Arsip
                    Bendahara Pengeluaran/PPTK</span></td>
        </tr>
    </table>
</body>

</html>
