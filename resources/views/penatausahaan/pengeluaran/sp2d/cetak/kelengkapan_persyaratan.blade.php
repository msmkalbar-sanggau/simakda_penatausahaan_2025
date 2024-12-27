@if ($beban == '1')
    <tr>
        <td class="a1">a. Pengantar SPM UP</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">b. SPM UP</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">c. Surat Pernyataan Pengajuan SPM UP</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">d. Salinan SPD</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">e. Foto Copy Surat Keputusan Penetapan UP</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">f. Laporan Penelitian Kelengakapan dokumen UP</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">g. Surat Pernyataan Tanggung Jawab mutlak</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">h. Lampiran lain yang diperlukan</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
@elseif ($beban == '2')
    <tr>
        <td class="a1">a. SPM- GU</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">b. Ringkasan SPM-GU</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">c. Lampiran SPM-GU</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Laporan Penelitian Kelengkapan Dokumen Penerbitan SPM-GU (PPK/PPKP)</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Surat Pernyataan Tanggungjawab Mutlak (SPTJM)</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Dokumen lain yang diperlukan</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
@elseif ($beban == '3')
    <tr>
        <td class="a1">a. SPM-TU</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">b. Ringkasan SPM-TU</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">c. Lampiran SPM TU</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Laporan Penelitian Kelengkapan Dokumen Penerbitan SPM TU</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Laporan Pertanggung Jawaban (LPJ)TU) dan Surat Pernyataan
            Tanggungjawab Belanja (SPTB) untuk Pengajuan SPM-TU berikutnya</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Bukti Setor sisa UP Sebelunya Jika ada sisa (TU)</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Surat Pernyataan Tanggungjawab Mutlak (SPTJM)</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Dokumen lain yang diperlukan</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
@elseif (in_array($beban, ['5', '6']))
    <tr>
        <td class="a1">a. SPM-LS</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">b. Ringkasan SPM-LS</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">c. Lampiran SPM Gaji LS</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Laporan Penelitian Kelengkapan Dokumen Penerbitan</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Daftar tanda terima pembayaran Honor yang sudah ditandatangan (Tanda Tangan pembuat daftar,
            mengetahui PPTK dan setuju dibayar PA/KPA)</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Surat Setoran Elektronik (SSE BPJS 2 % dan 3 %</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Surat Pernyataan Tanggungjawab Mutlak</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Syarat-syarat lainnya sesuai ketentuan yang berlaku</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
@elseif ($beban == '4')
    <tr>
        <td class="a1">a. Pengantar SPM LS Gaji</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">b. Ringkasan SPM LS Gaji</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">c. Rincian SPM LS Gaji</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a1">d. Lampiran SPM LS Gaji</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Salinan SPD</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- SSP PPh</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- SSBP</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Daftar Gaji dan Rekap</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Surat Pernyataan Tanggungjawab Mutlak (SPTJM)</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
    <tr>
        <td class="a2">- Dokumentasi lain yang dipersyaratkan</td>
        <td style="border: 1px solid black;width:10%"></td>
        <td style="border: 1px solid black;width:10%"></td>
    </tr>
@endif
