<?php

namespace App\Http\Controllers\Skpd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Static_;
use PDF;
use Knp\Snappy\Pdf as SnappyPdf;

class LaporanRegPajakController extends Controller
{


    // Cetak Reg Pajak Up Gu Tu ls
    public function cetakRegPajak(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $jenis          = $request->jenis;
        $cetak          = $request->cetak;
        $tahun_anggaran = tahun_anggaran();

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first();
        // $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');

        if ($jenis == 'upgutu') {
            $jns        = "AND jns_spp IN ('1','2','3')";
            $jenispajak = "UP/GU/TU";
        } elseif ($jenis == 'ls') {
            $jns = "AND jns_spp IN ('4','5','6')";
            $jenispajak = "LS";
        }

        $lalu = DB::select("SELECT
                                SUM(ppn) as ppn_l
                                ,SUM(pph21) as pph21_l
                                ,SUM(pph22) as pph22_l
                                ,SUM(pph23) as pph23_l
                                ,SUM(pph4) as pph4_l
                                ,SUM(terima) as terima_l
                                ,SUM(setor) as setor_l FROM(
                                SELECT
                                    a.no_bukti,tgl_bukti, ket
                                    ,CASE WHEN b.kd_rek6='210106010001' THEN b.nilai ELSE 0 END AS ppn
                                    ,CASE WHEN b.kd_rek6='210105010001' THEN b.nilai ELSE 0 END AS pph21
                                    ,CASE WHEN b.kd_rek6='210105020001' THEN b.nilai ELSE 0 END AS pph22
                                    ,CASE WHEN b.kd_rek6='210105030001' THEN b.nilai ELSE 0 END AS pph23
                                    ,CASE WHEN b.kd_rek6='210109010001' THEN b.nilai ELSE 0 END AS pph4,
                                    b.nilai as terima,
                                    0 as setor
                                    FROM trhtrmpot a INNER JOIN trdtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?
                                    AND MONTH(a.tgl_bukti) < ? $jns AND b.kd_rek6 IN('210106010001','210105010001','210105020001','210105030001','210109010001')
                                    UNION ALL
                                    SELECT
                                    a.no_bukti,tgl_bukti, ket
                                    ,0 AS ppn
                                    ,0 AS pph21
                                    ,0 AS pph22
                                    ,0 AS pph23
                                    ,0 AS pph4,
                                    0 as terima,
                                    b.nilai as setor
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?
                                    AND MONTH(a.tgl_bukti) < ? $jns  AND b.kd_rek6 IN('210106010001','210105010001','210105020001','210105030001','210109010001')) a", [$kd_skpd, $bulan, $kd_skpd, $bulan]);
        $rincian = DB::select("SELECT * FROM(
                                    SELECT
                                    a.no_bukti,tgl_bukti, ket
                                    ,CASE WHEN b.kd_rek6='210106010001' THEN b.nilai ELSE 0 END AS ppn
                                    ,CASE WHEN b.kd_rek6='210105010001' THEN b.nilai ELSE 0 END AS pph21
                                    ,CASE WHEN b.kd_rek6='210105020001' THEN b.nilai ELSE 0 END AS pph22
                                    ,CASE WHEN b.kd_rek6='210105030001' THEN b.nilai ELSE 0 END AS pph23
                                    ,CASE WHEN b.kd_rek6='210109010001' THEN b.nilai ELSE 0 END AS pph4,
                                    b.nilai as terima,
                                    0 as setor
                                    FROM trhtrmpot a INNER JOIN trdtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?
                                    AND MONTH(a.tgl_bukti)= ? $jns AND b.kd_rek6 IN('210106010001','210105010001','210105020001','210105030001','210109010001')
                                    UNION ALL
                                    SELECT
                                    a.no_bukti,tgl_bukti, ket
                                    ,0 AS ppn
                                    ,0 AS pph21
                                    ,0 AS pph22
                                    ,0 AS pph23
                                    ,0 AS pph4,
                                    0 as terima,
                                    b.nilai as setor
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?
                                    AND MONTH(a.tgl_bukti)= ? $jns  AND b.kd_rek6 IN('210106010001','210105010001','210105020001','210105030001','210109010001')) a
                                    ORDER BY CAST(a.no_bukti as int) ", [$kd_skpd, $bulan, $kd_skpd, $bulan]);

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'nm_skpd'           => $nm_skpd,
            'jenispajak'        => $jenispajak,
            'lalu'              => $lalu,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.regpajak')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('REGISTER PAJAK.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="REGISTER PAJAK - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    // Cetak Reg Potongan Lainnya
    public function cetakRegPajakPl(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;
        $tahun_anggaran = tahun_anggaran();

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first();
        // $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');


        $lalu = DB::select("SELECT
                                SUM(iwp) as iwp_l
                                ,SUM(taperum) as taperum_l
                                ,SUM(ppnpn1persen) as ppnpn1persen_l
                                ,SUM(ppnpn4persen) as ppnpn4persen_l
                                ,SUM(jkk) as jkk_l
                                ,SUM(jkm) as jkm_l
                                ,SUM(bpjs) as bpjs_l
                                ,SUM(terima) as terima_l
                                ,SUM(setor) as setor_l FROM(
                                SELECT
                                SUM(CASE WHEN b.kd_rek6 in ('210108010001') THEN b.nilai ELSE 0 END) AS iwp
                                ,SUM(CASE WHEN b.kd_rek6='210107010001' THEN b.nilai ELSE 0 END) AS taperum
                                ,SUM(CASE WHEN b.map_pot='210102010001c' THEN b.nilai ELSE 0 END) AS ppnpn1persen
                                ,SUM(CASE WHEN b.map_pot='210102010001d' THEN b.nilai ELSE 0 END) AS ppnpn4persen
                                ,SUM(CASE WHEN b.kd_rek6='210103010001' THEN b.nilai ELSE 0 END) AS jkk
                                ,SUM(CASE WHEN b.kd_rek6='210104010001' THEN b.nilai ELSE 0 END) AS jkm
                                ,SUM(CASE WHEN b.map_pot='210102010001' THEN b.nilai ELSE 0 END) AS bpjs
                                ,SUM(b.nilai) as terima,
                                0 as setor
                                FROM trhtrmpot a INNER JOIN trdtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?
                                AND MONTH(a.tgl_bukti)< ?  AND b.kd_rek6 IN ('210108010001','210107010001','210102010001a','210102010001b','210102010001c','210102010001d','210103010001','210104010001','210102010001')
                                UNION ALL
                                SELECT
                                    0 AS iwp
                                    ,0 AS taperum
                                    ,0 AS ppnpn1persen
                                    ,0 AS ppnpn4persen
                                    ,0 AS jkk
                                    ,0 AS jkm
                                    ,0 AS bpjs
                                    ,0 as terima,
                                SUM(b.nilai) as setor
                                FROM trhstrpot a INNER JOIN trdstrpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?
                                AND MONTH(a.tgl_bukti)< ?   AND b.kd_rek6 IN ('210108010001','210107010001','210102010001a','210102010001b','210102010001c','210102010001d','210103010001','210104010001','210102010001')) a", [$kd_skpd, $bulan, $kd_skpd, $bulan]);


        $rincian = DB::select("SELECT * FROM(
                                    SELECT
                                    a.no_bukti,tgl_bukti, ket
                                    ,CASE WHEN b.kd_rek6='210108010001' THEN b.nilai ELSE 0 END AS iwp
                                    ,CASE WHEN b.map_pot='210107010001' THEN b.nilai ELSE 0 END AS taperum
                                    ,CASE WHEN b.map_pot='210102010001c' THEN b.nilai ELSE 0 END AS ppnpn1persen
                                    ,CASE WHEN b.map_pot='210102010001d' THEN b.nilai ELSE 0 END AS ppnpn4persen
                                    ,CASE WHEN b.map_pot='210103010001' THEN b.nilai ELSE 0 END AS jkk
                                    ,CASE WHEN b.map_pot='210104010001' THEN b.nilai ELSE 0 END AS jkm
                                    ,CASE WHEN b.map_pot in ('210102010001','210102010001a','210102010001b') THEN b.nilai ELSE 0 END AS bpjs,
                                    b.nilai as terima,
                                    0 as setor
                                    FROM trhtrmpot a INNER JOIN trdtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?
                                    AND MONTH(a.tgl_bukti)= ?  AND b.map_pot IN ('210108010001b','210108010001a','210107010001','210102010001a','210102010001b','210102010001c','210102010001d','210103010001','210104010001','210102010001')
                                    UNION ALL
                                    SELECT
                                    a.no_bukti,tgl_bukti, ket
                                    ,0 AS iwp
                                    ,0 AS taperum
                                    ,0 AS ppnpn1persen
                                    ,0 AS ppnpn4persen
                                    ,0 AS jkk
                                    ,0 AS jkm
                                    ,0 AS bpjs
                                    ,0 as terima,
                                    b.nilai as setor
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?
                                    AND MONTH(a.tgl_bukti)= ? AND b.map_pot IN ('210108010001b','210108010001a','210108010001','210107010001','210102010001a','210102010001b','210102010001c','210102010001d','210103010001','210104010001','210102010001')) a
                                    ORDER BY CAST(a.no_bukti as int)", [$kd_skpd, $bulan, $kd_skpd, $bulan]);

        // KIRIM KE VIEW

        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'nm_skpd'           => $nm_skpd,
            'lalu'              => $lalu,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.regpotlain')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('REGISTER PAJAK.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="REGISTER PAJAK - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    // cetak_reg_pajak_rekap
    public function cetakRekapPajakPotongan(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;
        $tahun_anggaran = tahun_anggaran();

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first();
        // $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');

        $rincian = DB::select("SELECT a.bulan
                                ,ISNULL(ppn_up,0) AS ppn_up
                                ,ISNULL(pph21_up,0) AS pph21_up
                                ,ISNULL(pph22_up,0) AS pph22_up
                                ,ISNULL(pph23_up,0) AS pph23_up
                                ,ISNULL(pph4_up,0) AS pph4_up
                                ,ISNULL(ppn_ls,0) AS ppn_ls
                                ,ISNULL(pph21_ls,0) AS pph21_ls
                                ,ISNULL(pph22_ls,0) AS pph22_ls
                                ,ISNULL(pph23_ls,0) AS pph23_ls
                                ,ISNULL(pph4_ls,0) AS pph4_ls
                                ,ISNULL(ppnpn1,0) AS ppnpn1
                                ,ISNULL(ppnpn4,0) AS ppnpn4
                                ,ISNULL(iwp,0) AS iwp
                                ,ISNULL(taperum,0) AS taperum
                                ,ISNULL(jkk,0) AS jkk
                                ,ISNULL(jkm,0) AS jkm
                                ,ISNULL(bpjs,0) AS bpjs
                                ,ISNULL(terima,0) as terima
                                ,ISNULL(setor,0) as setor
                                FROM (
                                SELECT 1 as bulan UNION ALL
                                SELECT 2 as bulan UNION ALL
                                SELECT 3 as bulan UNION ALL
                                SELECT 4 as bulan UNION ALL
                                SELECT 5 as bulan UNION ALL
                                SELECT 6 as bulan UNION ALL
                                SELECT 7 as bulan UNION ALL
                                SELECT 8 as bulan UNION ALL
                                SELECT 9 as bulan UNION ALL
                                SELECT 10 as bulan UNION ALL
                                SELECT 11 as bulan UNION ALL
                                SELECT 12 as bulan) a LEFT JOIN
                                (
                                SELECT bulan
                                ,SUM(ppn_up) AS ppn_up
                                ,SUM(pph21_up) AS pph21_up
                                ,SUM(pph22_up) AS pph22_up
                                ,SUM(pph23_up) AS pph23_up
                                ,SUM(pph4_up) AS pph4_up
                                ,SUM(ppn_ls) AS ppn_ls
                                ,SUM(pph21_ls) AS pph21_ls
                                ,SUM(pph22_ls) AS pph22_ls
                                ,SUM(pph23_ls) AS pph23_ls
                                ,SUM(pph4_ls) AS pph4_ls
                                ,SUM(ppnpn1) AS ppnpn1
                                ,SUM(ppnpn4) AS ppnpn4
                                ,SUM(iwp) AS iwp
                                ,SUM(taperum) AS taperum
                                ,SUM(jkk) AS jkk
                                ,SUM(jkm) AS jkm
                                ,SUM(bpjs) AS bpjs
                                ,SUM(terima) as terima
                                ,SUM(setor) as setor
                                FROM
                                (
                                SELECT MONTH(a.tgl_bukti) as bulan
                                ,SUM(CASE WHEN b.kd_rek6='210106010001' AND a.jns_spp IN('1','2','3') THEN b.nilai ELSE 0 END) AS ppn_up
                                ,SUM(CASE WHEN b.kd_rek6='210105010001' AND a.jns_spp IN('1','2','3') THEN b.nilai ELSE 0 END) AS pph21_up
                                ,SUM(CASE WHEN b.kd_rek6='210105020001' AND a.jns_spp IN('1','2','3') THEN b.nilai ELSE 0 END) AS pph22_up
                                ,SUM(CASE WHEN b.kd_rek6='210105030001' AND a.jns_spp IN('1','2','3') THEN b.nilai ELSE 0 END) AS pph23_up
                                ,SUM(CASE WHEN b.kd_rek6='210109010001' AND a.jns_spp IN('1','2','3') THEN b.nilai ELSE 0 END) AS pph4_up
                                ,SUM(CASE WHEN b.kd_rek6='210106010001' AND a.jns_spp IN('4','5','6') THEN b.nilai ELSE 0 END) AS ppn_ls
                                ,SUM(CASE WHEN b.kd_rek6='210105010001' AND a.jns_spp IN('4','5','6') THEN b.nilai ELSE 0 END) AS pph21_ls
                                ,SUM(CASE WHEN b.kd_rek6='210105020001' AND a.jns_spp IN('4','5','6') THEN b.nilai ELSE 0 END) AS pph22_ls
                                ,SUM(CASE WHEN b.kd_rek6='210105030001' AND a.jns_spp IN('4','5','6') THEN b.nilai ELSE 0 END) AS pph23_ls
                                ,SUM(CASE WHEN b.kd_rek6='210109010001' AND a.jns_spp IN('4','5','6') THEN b.nilai ELSE 0 END) AS pph4_ls
                                ,0 ppnpn1,0 ppnpn4
                                ,SUM(CASE WHEN b.kd_rek6='210108010001' AND a.jns_spp IN('4','5','6') THEN b.nilai ELSE 0 END) AS iwp
                                ,0 taperum
                                ,SUM(CASE WHEN b.kd_rek6='210103010001' AND a.jns_spp IN('4','5','6') THEN b.nilai ELSE 0 END) jkk
                                ,SUM(CASE WHEN b.kd_rek6='210104010001' AND a.jns_spp IN('4','5','6') THEN b.nilai ELSE 0 END) jkm
                                ,SUM(CASE WHEN b.kd_rek6 in('210102010001','210102010001a','210102010001b') AND a.jns_spp IN('4','5','6') THEN b.nilai ELSE 0 END) bpjs
                                ,SUM(b.nilai) as terima
                                ,0 as setor
                                FROM trhtrmpot a INNER JOIN trdtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ? and month(a.tgl_bukti)<= ?
                                AND b.kd_rek6 IN('210106010001','210105010001','210105020001','210105030001','210109010001','210108010001', '210103010001','210104010001','210102010001')
                                GROUP BY MONTH(a.tgl_bukti)

                                UNION ALL

                                SELECT MONTH(a.tgl_bukti) as bulan
                                ,0 AS ppn_up
                                ,0 AS pph21_up
                                ,0 AS pph22_up
                                ,0 AS pph23_up
                                ,0 AS pph4_up
                                ,0 AS ppn_ls
                                ,0 AS pph21_ls
                                ,0 AS pph22_ls
                                ,0 AS pph23_ls
                                ,0 AS pph4_ls
                                ,SUM(CASE WHEN b.map_pot in ('210102010001c','210102010001a') THEN b.nilai ELSE 0 END) AS ppnpn1
                                ,SUM(CASE WHEN b.map_pot in ('210102010001d','210102010001b') THEN b.nilai ELSE 0 END) AS ppnpn4
                                ,SUM(CASE WHEN b.map_pot in ('210108010001b','210108010001a') THEN b.nilai ELSE 0 END) AS iwp
                                ,SUM(CASE WHEN b.map_pot='210107010001' THEN b.nilai ELSE 0 END) AS taperum
                                ,SUM(CASE WHEN b.map_pot='210103010001' THEN b.nilai ELSE 0 END) AS jkk
                                ,SUM(CASE WHEN b.map_pot='210104010001' THEN b.nilai ELSE 0 END) AS jkm
                                ,SUM(CASE WHEN b.map_pot='210102010001' THEN b.nilai ELSE 0 END) AS bpjs
                                ,SUM(b.nilai) as terima
                                ,0 as setor
                                FROM trhtrmpot a INNER JOIN trdtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?  and month(a.tgl_bukti)<= ?
                                AND b.map_pot IN ('210102010001c','210102010001a','210102010001b','210102010001d','210108010001b','210108010001a','210107010001','210103010001','210104010001','210102010001')
                                GROUP BY  MONTH(a.tgl_bukti)

                                UNION ALL

                                SELECT MONTH(a.tgl_bukti) as bulan
                                ,0 AS ppn_up
                                ,0 AS pph21_up
                                ,0 AS pph22_up
                                ,0 AS pph23_up
                                ,0 AS pph4_up
                                ,0 AS ppn_ls
                                ,0 AS pph21_ls
                                ,0 AS pph22_ls
                                ,0 AS pph23_ls
                                ,0 AS pph4_ls
                                ,0 ppnpn1
                                ,0 ppnpn4
                                ,0 iwp
                                ,0 taperum
                                ,0 jkk
                                ,0 jkm
                                ,0 bpjs
                                ,0 terima
                                ,SUM(b.nilai) as setor
                                FROM trhstrpot a INNER JOIN trdstrpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd= ?  and month(a.tgl_bukti)<= ?
                                AND b.kd_rek6 IN ('210102010001c','210102010001a','210102010001b','210102010001d','210108010001','210107010001','210103010001','210104010001','210102010001','210106010001','210105010001','210105020001','210105030001','210109010001')
                                GROUP BY MONTH(a.tgl_bukti)
                                )a
                                GROUP BY bulan) b
                                ON a.bulan=b.bulan ORDER BY a.bulan", [$kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan]);

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'nm_skpd'           => $nm_skpd,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.regrekap_pajak_potlain')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('REGISTER PAJAK.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="REGISTER PAJAK - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }
}
