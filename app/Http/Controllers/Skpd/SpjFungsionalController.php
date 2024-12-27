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

class SpjFungsionalController extends Controller
{

    public function cetakSpjFungsional(Request $request)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);
        $tanggal_ttd    = $request->tgl_ttd;
        $judul          = $request->judul;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $jns_anggaran   = $request->jns_anggaran;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;
        $tahun_anggaran = tahun_anggaran();
        $tanggal_awal    = $request->tanggal71;
        $tanggal_akhir   = $request->tanggal72;
        $pilihan_spj    = $request->pilihan_spj;

        // get daerah
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        // get bendahara
        $cari_bendahara = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first();
        // get pa kpa
        // $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();

        if ($pilihan_spj == 'bulan7') {
        // tabel rincian
        $rincian = DB::select("exec spj_skpd ?,?,?", array($kd_skpd, $bulan, $jns_anggaran));

        // Penerimaan Sp2D
        $terima_sp2d = DB::select("SELECT (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        MONTH(a.tgl_kas)=? AND c.jns_spp IN ('1','2','3') AND a.status='1') AS sp2d_up_ini,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        MONTH(a.tgl_kas)<? AND c.jns_spp IN ('1','2','3') AND a.status='1') AS sp2d_up_ll,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        MONTH(a.tgl_kas)=? AND c.jns_spp ='4' AND a.status='1') AS sp2d_gj_ini,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        MONTH(a.tgl_kas)<? AND c.jns_spp ='4'  AND a.status='1') AS sp2d_gj_ll,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        MONTH(a.tgl_kas)=? AND c.jns_spp in ('5','6')  AND a.status='1') AS sp2d_brjs_ini,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        MONTH(a.tgl_kas)<? AND c.jns_spp in ('5','6') AND a.status='1') AS sp2d_brjs_ll", [$kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan]);

        foreach ($terima_sp2d as $trm_sp2d) {
            $sp2d_gj_ll     = $trm_sp2d->sp2d_gj_ll;
            $sp2d_gj_ini    = $trm_sp2d->sp2d_gj_ini;
            $sp2d_brjs_ll   = $trm_sp2d->sp2d_brjs_ll;
            $sp2d_brjs_ini  = $trm_sp2d->sp2d_brjs_ini;
            $sp2d_up_ll     = $trm_sp2d->sp2d_up_ll;
            $sp2d_up_ini    = $trm_sp2d->sp2d_up_ini;
        }

        // terima potongan ppn
        $trmpot_pot_ppn = get_terimapotongan($kd_skpd, '210106010001', $bulan);

        foreach ($trmpot_pot_ppn as $trm_ppn) {
            $ppn_gaji_ll    = $trm_ppn->pot_gaji_ll;
            $ppn_gaji_ini   = $trm_ppn->pot_gaji_ini;
            $ppn_brjs_ini   = $trm_ppn->pot_brjs_ini;
            $ppn_brjs_ll    = $trm_ppn->pot_brjs_ll;
            $ppn_up_ll      = $trm_ppn->pot_up_ll;
            $ppn_up_ini     = $trm_ppn->pot_up_ini;
        }
        // terima potongan PPH 21
        $trmpot_pot_pph21 = get_terimapotongan($kd_skpd, '210105010001', $bulan);
        foreach ($trmpot_pot_pph21 as $trm_pph21) {
            $pph21_gaji_ll    = $trm_pph21->pot_gaji_ll;
            $pph21_gaji_ini   = $trm_pph21->pot_gaji_ini;
            $pph21_brjs_ini   = $trm_pph21->pot_brjs_ini;
            $pph21_brjs_ll    = $trm_pph21->pot_brjs_ll;
            $pph21_up_ll      = $trm_pph21->pot_up_ll;
            $pph21_up_ini     = $trm_pph21->pot_up_ini;
        }

        // terima potongan PPH 22
        $trmpot_pot_pph22 = get_terimapotongan($kd_skpd, '210105020001', $bulan);
        foreach ($trmpot_pot_pph22 as $trm_pph22) {
            $pph22_gaji_ll    = $trm_pph22->pot_gaji_ll;
            $pph22_gaji_ini   = $trm_pph22->pot_gaji_ini;
            $pph22_brjs_ini   = $trm_pph22->pot_brjs_ini;
            $pph22_brjs_ll    = $trm_pph22->pot_brjs_ll;
            $pph22_up_ll      = $trm_pph22->pot_up_ll;
            $pph22_up_ini     = $trm_pph22->pot_up_ini;
        }


        // terima potongan PPH 23
        $trmpot_pot_pph23 = get_terimapotongan($kd_skpd, '210105030001', $bulan);
        foreach ($trmpot_pot_pph23 as $trm_pph23) {
            $pph23_gaji_ll    = $trm_pph23->pot_gaji_ll;
            $pph23_gaji_ini   = $trm_pph23->pot_gaji_ini;
            $pph23_brjs_ini   = $trm_pph23->pot_brjs_ini;
            $pph23_brjs_ll    = $trm_pph23->pot_brjs_ll;
            $pph23_up_ll      = $trm_pph23->pot_up_ll;
            $pph23_up_ini     = $trm_pph23->pot_up_ini;
        }

        // terima potongan PPH Pasal 4 Ayat 2
        $trmpot_pot_pph4ayat2 = get_terimapotongan($kd_skpd, '210109010001', $bulan);
        foreach ($trmpot_pot_pph4ayat2 as $trm_pph4ayat2) {
            $pph4ayat2_gaji_ll    = $trm_pph4ayat2->pot_gaji_ll;
            $pph4ayat2_gaji_ini   = $trm_pph4ayat2->pot_gaji_ini;
            $pph4ayat2_brjs_ini   = $trm_pph4ayat2->pot_brjs_ini;
            $pph4ayat2_brjs_ll    = $trm_pph4ayat2->pot_brjs_ll;
            $pph4ayat2_up_ll      = $trm_pph4ayat2->pot_up_ll;
            $pph4ayat2_up_ini     = $trm_pph4ayat2->pot_up_ini;
        }
        //
        // terima IWP
        $terima_iwp = get_terimapotongan($kd_skpd, '210108010001', $bulan);

        foreach ($terima_iwp as $trm_iwp) {
            $iwp_gaji_ll    = $trm_iwp->pot_gaji_ll;
            $iwp_gaji_ini   = $trm_iwp->pot_gaji_ini;
            $iwp_brjs_ini   = $trm_iwp->pot_brjs_ini;
            $iwp_brjs_ll    = $trm_iwp->pot_brjs_ll;
            $iwp_up_ll      = $trm_iwp->pot_up_ll;
            $iwp_up_ini     = $trm_iwp->pot_up_ini;
        }


        // terima taperum
        $terima_taperum = get_terimapotongan($kd_skpd, '210107010001', $bulan);

        foreach ($terima_taperum as $trm_taperum) {
            $taperum_gaji_ll    = $trm_taperum->pot_gaji_ll;
            $taperum_gaji_ini   = $trm_taperum->pot_gaji_ini;
            $taperum_brjs_ini   = $trm_taperum->pot_brjs_ini;
            $taperum_brjs_ll    = $trm_taperum->pot_brjs_ll;
            $taperum_up_ll      = $trm_taperum->pot_up_ll;
            $taperum_up_ini     = $trm_taperum->pot_up_ini;
        }
        //  PPNPN / Jaminan Kesehatan
        $terima_ppnpn = get_terimapotongan($kd_skpd, '210102010001', $bulan);

        foreach ($terima_ppnpn as $trm_ppnpn) {
            $ppnpn_gaji_ll    = $trm_ppnpn->pot_gaji_ll;
            $ppnpn_gaji_ini   = $trm_ppnpn->pot_gaji_ini;
            $ppnpn_brjs_ini   = $trm_ppnpn->pot_brjs_ini;
            $ppnpn_brjs_ll    = $trm_ppnpn->pot_brjs_ll;
            $ppnpn_up_ll      = $trm_ppnpn->pot_up_ll;
            $ppnpn_up_ini     = $trm_ppnpn->pot_up_ini;
        }
        // Denda keterlambatan 410411010001
        $terima_dk = get_terimapotongan($kd_skpd, '410411010001', $bulan);

        foreach ($terima_dk as $trm_dk) {
            $dk_gaji_ll    = $trm_dk->pot_gaji_ll;
            $dk_gaji_ini   = $trm_dk->pot_gaji_ini;
            $dk_brjs_ini   = $trm_dk->pot_brjs_ini;
            $dk_brjs_ll    = $trm_dk->pot_brjs_ll;
            $dk_up_ll      = $trm_dk->pot_up_ll;
            $dk_up_ini     = $trm_dk->pot_up_ini;
        }

        // Pelimpahan
        $terima_pelimpahan = DB::select("SELECT sum(x.bln_lalu) as pot_up_ll,sum(x.bln_ini) as pot_up_ini from(
                                        select
                                                    SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as bln_lalu,
                                                    SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as bln_ini
                                                    from tr_setorpelimpahan
                                                    WHERE kd_skpd= ?
                                        UNION ALL
                                        select
                                                    SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as bln_lalu,
                                                    SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as bln_ini
                                                    from tr_setorpelimpahan_bank
                                                    WHERE kd_skpd= ?
                                        UNION ALL
                                        SELECT
                                                    SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as bln_lalu,
                                                    SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as bln_ini
                                        from tr_setorsimpanan WHERE kd_skpd= ? and jenis='3'
                                        )x", [$bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd]);
        foreach ($terima_pelimpahan as $trm_pelimpahan) {
            $pelimpahan_up_ll      = $trm_pelimpahan->pot_up_ll;
            $pelimpahan_up_ini     = $trm_pelimpahan->pot_up_ini;
        }

        // Terima PANJAR
        $terima_panjar = DB::select("SELECT SUM(x.pot_up_ll) pot_up_ll, SUM(x.pot_up_ini) pot_up_ini FROM(
                                        SELECT
                                        SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as pot_up_ll,
                                        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as pot_up_ini
                                        from
                                        tr_jpanjar where jns=1 and kd_skpd= ?
                                        )x", [$bulan, $bulan, $kd_skpd]);
        foreach ($terima_panjar as $trm_panjar) {
            $panjar_up_ll      = $trm_panjar->pot_up_ll;
            $panjar_up_ini     = $trm_panjar->pot_up_ini;
        }

        // BOS
        // $terima_bos = DB::select("SELECT SUM(x.bos_bln_lalu) bos_bln_lalu, SUM(x.bos_bln_ini) bos_bln_ini FROM(
        //                             SELECT
        //                             SUM(CASE WHEN MONTH(tgl_sp2b)< ? THEN b.nilai ELSE 0 END) as bos_bln_lalu,
        //                             SUM(CASE WHEN MONTH(tgl_sp2b)= ? THEN b.nilai ELSE 0 END) as bos_bln_ini
        //                             from trhsp2b a inner join trdsp2b b on a.kd_skpd=b.kd_skpd and a.no_sp2b=b.no_sp2b
        //                             where a.kd_skpd= ?
        //                             UNION

        //                             SELECT
        //                             SUM(CASE WHEN MONTH(tgl_sp2h)< ? THEN b.nilai ELSE 0 END) as bos_bln_lalu,
        //                             SUM(CASE WHEN MONTH(tgl_sp2h)= ? THEN b.nilai ELSE 0 END) as bos_bln_ini
        //                             from trhsp2h a inner join trdsp2h b on a.no_sp2h=b.no_sp2h
        //                             where a.kd_skpd= ? )x", [$bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd]);

        // foreach ($terima_bos as $trm_bos) {
        //     $bos_bln_lalu      = $trm_bos->bos_bln_lalu;
        //     $bos_bln_ini        = $trm_bos->bos_bln_ini;
        // }

        // terima BLUD
        $terima_blud = DB::select("SELECT SUM(CASE WHEN MONTH(tgl_kas)< ? THEN b.nilai ELSE 0 END) as blud_bln_lalu,
        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN b.nilai ELSE 0 END) as blud_bln_ini
        from trhtransout_blud a inner join trdtransout_blud b on a.kd_skpd=b.kd_skpd and a.no_bukti=b.no_bukti
        where a.kd_skpd= ? and left(b.kd_rek6,1)='5' and b.sumber='BLUD'", [$bulan, $bulan, $kd_skpd]);

        foreach ($terima_blud as $trm_blud) {
            $blud_bln_lalu      = $trm_blud->blud_bln_lalu;
            $blud_bln_ini       = $trm_blud->blud_bln_ini;
        }

        // Terima lain-lain
        $terima_lain = DB::select("SELECT
        SUM(ISNULL(jlain_up_ll,0)) jlain_up_ll, SUM(ISNULL(jlain_up_ini,0)) jlain_up_ini,
        SUM(ISNULL(jlain_gaji_ll,0)) jlain_gaji_ll, SUM(ISNULL(jlain_gaji_ini,0)) jlain_gaji_ini,
        SUM(ISNULL(jlain_brjs_ll,0)) jlain_brjs_ll, SUM(ISNULL(jlain_brjs_ini,0)) jlain_brjs_ini
         FROM(
        SELECT
        SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND MONTH(b.tgl_bukti)< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ll,
        SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND MONTH(b.tgl_bukti)= ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ini,
        SUM(CASE WHEN b.jns_spp IN ('4') AND MONTH(b.tgl_bukti)< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ll,
        SUM(CASE WHEN b.jns_spp IN ('4') AND MONTH(b.tgl_bukti)= ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ini,
        SUM(CASE WHEN b.jns_spp in ('5','6') AND MONTH(b.tgl_bukti)< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ll,
        SUM(CASE WHEN b.jns_spp in ('5','6') AND MONTH(b.tgl_bukti)= ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ini
        FROM trdtrmpot a INNER JOIN trhtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
        WHERE a.kd_skpd= ?
        UNION ALL

        SELECT
        SUM(CASE WHEN a.jns_beban='1' AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS jlain_up_ll,
        SUM(CASE WHEN a.jns_beban='1' AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS jlain_up_ini,
        SUM(CASE WHEN a.jns_beban='4' AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ll,
        SUM(CASE WHEN a.jns_beban='4' AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ini,
        SUM(CASE WHEN a.jns_beban in ('5','6') AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ll,
        SUM(CASE WHEN a.jns_beban in ('5','6') AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ini
        FROM TRHINLAIN a WHERE pengurang_belanja !='1'
        AND a.kd_skpd= ?
        union all
        SELECT
		isnull(sld_awal_bank,0) + isnull(sld_awal_tunai,0) AS jlain_up_ll, '0' as jlain_up_ini, '0' as jlain_gaji_ll ,'0' as jlain_gaji_ini, '0' as jlain_brjs_ll, '0' as jlain_brjs_ini FROM ms_skpd where kd_skpd= ?
        ) a ", [$bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd, $kd_skpd]);

        foreach ($terima_lain as $trm_lain) {
            $jlain_gaji_ll    = $trm_lain->jlain_gaji_ll;
            $jlain_gaji_ini   = $trm_lain->jlain_gaji_ini;
            $jlain_brjs_ll    = $trm_lain->jlain_brjs_ll;
            $jlain_brjs_ini   = $trm_lain->jlain_brjs_ini;
            $jlain_up_ll      = $trm_lain->jlain_up_ll;
            $jlain_up_ini     = $trm_lain->jlain_up_ini;
        }

        // Pengeluaran
        $keluar_spj = DB::select("SELECT sum(gaji_lalu) as spj_gaji_ll, sum(gaji_ini) as spj_gaji_ini, sum(brg_lalu) as spj_brjs_ll,
        sum(brg_ini) as spj_brjs_ini, sum(up_lalu) as spj_up_ll, sum(up_ini) as spj_up_ini from
        (select  a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_bukti)=?
        --and panjar not in (5)
        AND jns_spp in (1,2,3) and pay not in ('PANJAR')
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout_blud a join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_bukti)= ?  and left(kd_rek6,1)='5' and jns_spp in (1,2,3)
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai*-1,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdinlain a join TRHINLAIN b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where MONTH(b.TGL_BUKTI)=? and b.pengurang_belanja=1
        union all
        select a.kd_skpd, isnull(a.nilai,0) as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_bukti)=?
        --and panjar not in (5)
        AND jns_spp in (4)
        union all
        select  a.kd_skpd, isnull(a.rupiah*-1,0) as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_sts)=? and b.jns_cp in (1) and b.pot_khusus not in (0,1,2,3)
        union all
        select a.kd_skpd, 0 as gaji_ini, isnull(a.nilai,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_bukti)=?
        --and panjar not in (5)
        AND jns_spp in ('5','6')
        union all
        select  a.kd_skpd, 0 as gaji_ini, isnull(a.rupiah*-1,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_sts)=? and b.jns_cp in (2) and b.pot_khusus not in (0,1,2,3)
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, isnull(a.nilai,0) as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_bukti)<?
        --and panjar not in (5)
        AND jns_spp in (1,2,3) and pay not in ('PANJAR')
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, isnull(a.nilai*-1,0) as up_lalu from trdinlain a join TRHINLAIN b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where MONTH(b.TGL_BUKTI)<? and b.pengurang_belanja=1
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, isnull(a.nilai,0) as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_bukti)<?
        --and panjar not in (5)
        AND jns_spp in (4)
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, isnull(a.rupiah*-1,0) as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_sts)<? and b.jns_cp in (1) and b.pot_khusus not in (0,1,2,3)
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.nilai,0) as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_bukti)<?
        --and panjar not in (5)
        AND jns_spp in ('5','6')
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.rupiah*-1,0) as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
        where MONTH(b.tgl_sts)<? and b.jns_cp in (2) and b.pot_khusus not in (0,1,2,3)) a
        WHERE a.kd_skpd=?", [$bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd]);
        foreach ($keluar_spj as $klr_spj) {
            $spj_gaji_ll    = $klr_spj->spj_gaji_ll;
            $spj_gaji_ini   = $klr_spj->spj_gaji_ini;
            $spj_brjs_ll    = $klr_spj->spj_brjs_ll;
            $spj_brjs_ini   = $klr_spj->spj_brjs_ini;
            $spj_up_ll      = $klr_spj->spj_up_ll;
            $spj_up_ini     = $klr_spj->spj_up_ini;
        }

        // setor PPN
        $setor_ppn    = get_setorpotongan($kd_skpd, '210106010001', $bulan);
        foreach ($setor_ppn as $str_ppn) {
            $str_ppn_gaji_ll    = $str_ppn->pot_gaji_ll;
            $str_ppn_gaji_ini   = $str_ppn->pot_gaji_ini;
            $str_ppn_brjs_ini   = $str_ppn->pot_brjs_ini;
            $str_ppn_brjs_ll    = $str_ppn->pot_brjs_ll;
            $str_ppn_up_ll      = $str_ppn->pot_up_ll;
            $str_ppn_up_ini     = $str_ppn->pot_up_ini;
        }


        $setor_pph21  = get_setorpotongan($kd_skpd, '210105010001', $bulan);
        foreach ($setor_pph21 as $str_pph21) {
            $str_pph21_gaji_ll    = $str_pph21->pot_gaji_ll;
            $str_pph21_gaji_ini   = $str_pph21->pot_gaji_ini;
            $str_pph21_brjs_ini   = $str_pph21->pot_brjs_ini;
            $str_pph21_brjs_ll    = $str_pph21->pot_brjs_ll;
            $str_pph21_up_ll      = $str_pph21->pot_up_ll;
            $str_pph21_up_ini     = $str_pph21->pot_up_ini;
        }
        // PPH22
        $setor_pph22  = get_setorpotongan($kd_skpd, '210105020001', $bulan);
        foreach ($setor_pph22 as $str_pph22) {
            $str_pph22_gaji_ll    = $str_pph22->pot_gaji_ll;
            $str_pph22_gaji_ini   = $str_pph22->pot_gaji_ini;
            $str_pph22_brjs_ini   = $str_pph22->pot_brjs_ini;
            $str_pph22_brjs_ll    = $str_pph22->pot_brjs_ll;
            $str_pph22_up_ll      = $str_pph22->pot_up_ll;
            $str_pph22_up_ini     = $str_pph22->pot_up_ini;
        }
        // PPh23
        $setor_pph23  = get_setorpotongan($kd_skpd, '210105030001', $bulan);
        foreach ($setor_pph23 as $str_pph23) {
            $str_pph23_gaji_ll    = $str_pph23->pot_gaji_ll;
            $str_pph23_gaji_ini   = $str_pph23->pot_gaji_ini;
            $str_pph23_brjs_ini   = $str_pph23->pot_brjs_ini;
            $str_pph23_brjs_ll    = $str_pph23->pot_brjs_ll;
            $str_pph23_up_ll      = $str_pph23->pot_up_ll;
            $str_pph23_up_ini     = $str_pph23->pot_up_ini;
        }
        // pasal 4 ayat 2
        $setor_pph4ayat2  = get_setorpotongan($kd_skpd, '210109010001', $bulan);
        foreach ($setor_pph4ayat2 as $str_pph4ayat2) {
            $str_pph4ayat2_gaji_ll    = $str_pph4ayat2->pot_gaji_ll;
            $str_pph4ayat2_gaji_ini   = $str_pph4ayat2->pot_gaji_ini;
            $str_pph4ayat2_brjs_ini   = $str_pph4ayat2->pot_brjs_ini;
            $str_pph4ayat2_brjs_ll    = $str_pph4ayat2->pot_brjs_ll;
            $str_pph4ayat2_up_ll      = $str_pph4ayat2->pot_up_ll;
            $str_pph4ayat2_up_ini     = $str_pph4ayat2->pot_up_ini;
        }
        // IWP
        $setor_iwp  = get_setorpotongan($kd_skpd, '210108010001', $bulan);
        foreach ($setor_iwp as $str_iwp) {
            $str_iwp_gaji_ll    = $str_iwp->pot_gaji_ll;
            $str_iwp_gaji_ini   = $str_iwp->pot_gaji_ini;
            $str_iwp_brjs_ini   = $str_iwp->pot_brjs_ini;
            $str_iwp_brjs_ll    = $str_iwp->pot_brjs_ll;
            $str_iwp_up_ll      = $str_iwp->pot_up_ll;
            $str_iwp_up_ini     = $str_iwp->pot_up_ini;
        }

        // taperum
        $setor_taperum  = get_setorpotongan($kd_skpd, '210107010001', $bulan);
        foreach ($setor_taperum as $str_taperum) {
            $str_taperum_gaji_ll    = $str_taperum->pot_gaji_ll;
            $str_taperum_gaji_ini   = $str_taperum->pot_gaji_ini;
            $str_taperum_brjs_ini   = $str_taperum->pot_brjs_ini;
            $str_taperum_brjs_ll    = $str_taperum->pot_brjs_ll;
            $str_taperum_up_ll      = $str_taperum->pot_up_ll;
            $str_taperum_up_ini     = $str_taperum->pot_up_ini;
        }
        // taperum
        $setor_ppnpn  = get_setorpotongan($kd_skpd, '210102010001', $bulan);
        foreach ($setor_ppnpn as $str_ppnpn) {
            $str_ppnpn_gaji_ll    = $str_ppnpn->pot_gaji_ll;
            $str_ppnpn_gaji_ini   = $str_ppnpn->pot_gaji_ini;
            $str_ppnpn_brjs_ini   = $str_ppnpn->pot_brjs_ini;
            $str_ppnpn_brjs_ll    = $str_ppnpn->pot_brjs_ll;
            $str_ppnpn_up_ll      = $str_ppnpn->pot_up_ll;
            $str_ppnpn_up_ini     = $str_ppnpn->pot_up_ini;
        }

        // dk
        $setor_dk  = get_setorpotongan($kd_skpd, '410411010001', $bulan);
        foreach ($setor_dk as $str_dk) {
            $str_dk_gaji_ll    = $str_dk->pot_gaji_ll;
            $str_dk_gaji_ini   = $str_dk->pot_gaji_ini;
            $str_dk_brjs_ini   = $str_dk->pot_brjs_ini;
            $str_dk_brjs_ll    = $str_dk->pot_brjs_ll;
            $str_dk_up_ll      = $str_dk->pot_up_ll;
            $str_dk_up_ini     = $str_dk->pot_up_ini;
        }

        // potongan penghasilan lainnya
        $setor_pplain = DB::select("SELECT
                    SUM(ISNULL(up_lain_lalu,0)) up_lain_lalu, SUM(ISNULL(up_lain_ini,0)) up_lain_ini,
                    SUM(ISNULL(ls_lain_lalu,0)) ls_lain_lalu, SUM(ISNULL(ls_lain_ini,0)) ls_lain_ini,
                    SUM(ISNULL(gj_lain_lalu,0)) gj_lain_lalu, SUM(ISNULL(gj_lain_ini,0)) gj_lain_ini
                    FROM(

                    SELECT
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '3' and MONTH(tgl_sts)< ?  then a.rupiah else 0 end),0)) AS up_lain_lalu,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '3' and MONTH(tgl_sts)= ?  then a.rupiah else 0 end),0)) AS up_lain_ini,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '2' and MONTH(tgl_sts)< ?  then a.rupiah else 0 end),0)) AS ls_lain_lalu,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '2' and MONTH(tgl_sts)= ?  then a.rupiah else 0 end),0)) AS ls_lain_ini,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '1' and MONTH(tgl_sts)< ?  then a.rupiah else 0 end),0)) AS gj_lain_lalu,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '1' and MONTH(tgl_sts)= ?  then a.rupiah else 0 end),0)) AS gj_lain_ini
                FROM trdkasin_pkd a
                INNER JOIN trhkasin_pkd b on a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                WHERE a.kd_skpd = ? AND jns_trans='5'

                    )zzz", [$bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd]);
        foreach ($setor_pplain as $str_pplain) {
            $str_pplain_gaji_ll    = $str_pplain->gj_lain_lalu;
            $str_pplain_gaji_ini   = $str_pplain->gj_lain_ini;
            $str_pplain_brjs_ini   = $str_pplain->ls_lain_lalu;
            $str_pplain_brjs_ll    = $str_pplain->ls_lain_ini;
            $str_pplain_up_ll      = $str_pplain->up_lain_lalu;
            $str_pplain_up_ini     = $str_pplain->up_lain_ini;
        }

        // HKPG
        $setor_hkpg = DB::select("SELECT
                                SUM(ISNULL(up_hkpg_lalu,0)) up_hkpg_lalu, SUM(ISNULL(up_hkpg_ini,0)) up_hkpg_ini,
                                SUM(ISNULL(ls_hkpg_lalu,0)) ls_hkpg_lalu, SUM(ISNULL(ls_hkpg_ini,0)) ls_hkpg_ini,
                                SUM(ISNULL(gj_hkpg_lalu,0)) gj_hkpg_lalu, SUM(ISNULL(gj_hkpg_ini,0)) gj_hkpg_ini
                                FROM(

                                    SELECT
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '3' and MONTH(tgl_sts)<? then a.rupiah else 0 end),0)) AS up_hkpg_lalu,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '3' and MONTH(tgl_sts)=? then a.rupiah else 0 end),0)) AS up_hkpg_ini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '3' and MONTH(tgl_sts)<=? then a.rupiah else 0 end),0)) AS up_hkpg_sdini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '2' and MONTH(tgl_sts)<? then a.rupiah else 0 end),0)) AS ls_hkpg_lalu,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '2' and MONTH(tgl_sts)=? then a.rupiah else 0 end),0)) AS ls_hkpg_ini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '2' and MONTH(tgl_sts)<=? then a.rupiah else 0 end),0)) AS ls_hkpg_sdini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '1' and MONTH(tgl_sts)<? then a.rupiah else 0 end),0)) AS gj_hkpg_lalu,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '1' and MONTH(tgl_sts)=? then a.rupiah else 0 end),0)) AS gj_hkpg_ini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '1' and MONTH(tgl_sts)<=? then a.rupiah else 0 end),0)) AS gj_hkpg_sdini
                FROM trdkasin_pkd a
                INNER JOIN trhkasin_pkd b on a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                WHERE a.kd_skpd = ? AND jns_trans='5' AND LEFT(kd_rek6,1)<>4
                    )zz", [$bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd]);
        foreach ($setor_hkpg as $str_hkpg) {
            $str_hkpg_gaji_ll    = $str_hkpg->gj_hkpg_lalu;
            $str_hkpg_gaji_ini   = $str_hkpg->gj_hkpg_ini;
            $str_hkpg_brjs_ini   = $str_hkpg->ls_hkpg_lalu;
            $str_hkpg_brjs_ll    = $str_hkpg->ls_hkpg_ini;
            $str_hkpg_up_ll      = $str_hkpg->up_hkpg_lalu;
            $str_hkpg_up_ini     = $str_hkpg->up_hkpg_ini;
        }

        // CP (Contra Post)
        $setor_cp = DB::select(
            "SELECT
                        SUM(isnull((case when rtrim(jns_cp)= '3' and MONTH(tgl_sts)= ? then z.nilai else 0 end),0)) AS up_cp_ini,
                        SUM(isnull((case when rtrim(jns_cp)= '3' and MONTH(tgl_sts)< ? then z.nilai else 0 end),0)) AS up_cp_lalu,
                        SUM(isnull((case when rtrim(jns_cp)= '1' and MONTH(tgl_sts)= ? then z.nilai else 0 end),0)) AS gj_cp_ini,
                        SUM(isnull((case when rtrim(jns_cp)= '1' and MONTH(tgl_sts)< ? then z.nilai else 0 end),0)) AS gj_cp_lalu,
                        SUM(isnull((case when rtrim(jns_cp)= '2' and MONTH(tgl_sts)= ? then z.nilai else 0 end),0)) AS ls_cp_ini,
                        SUM(isnull((case when rtrim(jns_cp)= '2' and MONTH(tgl_sts)< ? then z.nilai else 0 end),0)) AS ls_cp_lalu
                        from (select rupiah as nilai,jns_trans,pot_khusus,jns_cp,d.tgl_sts ,d.kd_skpd from
                        trdkasin_pkd c INNER JOIN trhkasin_pkd d ON c.no_sts = d.no_sts AND c.kd_skpd = d.kd_skpd where d.kd_skpd = ? AND
                        ((jns_trans='5' AND pot_khusus='0') OR jns_trans='1')) z",
            [$bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd]
        );

        foreach ($setor_cp as $str_cp) {
            $str_cp_gaji_ll    = $str_cp->gj_cp_lalu;
            $str_cp_gaji_ini   = $str_cp->gj_cp_ini;
            $str_cp_brjs_ini   = $str_cp->ls_cp_lalu;
            $str_cp_brjs_ll    = $str_cp->ls_cp_ini;
            $str_cp_up_ll      = $str_cp->up_cp_lalu;
            $str_cp_up_ini     = $str_cp->up_cp_ini;
        }

        // Pelimpahan UP/GU
        $setor_pelimpahan = DB::select("SELECT SUM(z.bln_lalu) up_pelimpahan_lalu, SUM(z.bln_ini) up_pelimpahan_ini from(
                                        select
                                        SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as bln_lalu,
                                        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as bln_ini
                                        from tr_setorpelimpahan_bank
                                        WHERE kd_skpd_sumber= ?
                                        UNION ALL
                                        select
                                        SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as bln_lalu,
                                        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as bln_ini
                                        from tr_setorpelimpahan
                                        WHERE kd_skpd_sumber= ?
                                        )z", [$bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd]);
        foreach ($setor_pelimpahan as $str_pelimpahan) {
            $str_pelimpahan_up_ll      = $str_pelimpahan->up_pelimpahan_lalu;
            $str_pelimpahan_up_ini     = $str_pelimpahan->up_pelimpahan_ini;
        }

        // setor panjar
        $setor_panjar   = DB::select("SELECT
                        SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as up_panjar_lalu,
                        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as up_panjar_ini
                        from tr_panjar
                        WHERE kd_skpd= ? and jns='1'", [$bulan, $bulan, $kd_skpd]);

        foreach ($setor_panjar as $str_panjar) {
            $str_panjar_up_ll      = $str_panjar->up_panjar_lalu;
            $str_panjar_up_ini     = $str_panjar->up_panjar_ini;
        }

        // setor BOS
        // $setor_bos  = DB::select("SELECT
        //                 SUM(CASE WHEN MONTH(tgl_bukti)< ? THEN a.nilai ELSE 0 END) as gj_bos_lalu,
        //                 SUM(CASE WHEN MONTH(tgl_bukti)= ? THEN a.nilai ELSE 0 END) as gj_bos_ini
        //                 from trdtransout_blud a inner join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        //                 WHERE a.kd_skpd= ? AND jns_spp in (6) and left(a.kd_rek6,1) IN ('5')", [$bulan, $bulan, $kd_skpd]);

        // foreach ($setor_bos as $str_bos) {
        //     $str_bos_gj_ll      = $str_bos->gj_bos_lalu;
        //     $str_bos_gj_ini     = $str_bos->gj_bos_ini;
        // }

       // Setor BLUD
       $setor_blud     = DB::select("SELECT
        SUM(CASE WHEN MONTH(tgl_bukti)< ? THEN a.nilai ELSE 0 END) as ls_blud_lalu,
        SUM(CASE WHEN MONTH(tgl_bukti)= ? THEN a.nilai ELSE 0 END) as ls_blud_ini
        from trdtransout_blud a inner join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        WHERE a.kd_skpd= ? and right(kd_rek6,6)='999999'", [$bulan, $bulan, $kd_skpd]);
        foreach ($setor_blud as $str_blud) {
            $str_blud_ls_ll      = $str_blud->ls_blud_lalu;
            $str_blud_ls_ini     = $str_blud->ls_blud_ini;
        }

        // setor lain-lain
        $setor_lain     = DB::select("SELECT
                        SUM(ISNULL(jlain_up_ll,0)) lain_up_ll, SUM(ISNULL(jlain_up_ini,0)) lain_up_ini,
                        SUM(ISNULL(jlain_gaji_ll,0)) lain_gaji_ll, SUM(ISNULL(jlain_gaji_ini,0)) lain_gaji_ini,
                        SUM(ISNULL(jlain_brjs_ll,0)) lain_brjs_ll, SUM(ISNULL(jlain_brjs_ini,0)) lain_brjs_ini
                        FROM(
                        SELECT
                        SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND MONTH(b.tgl_bukti)< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ll,
                        SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND MONTH(b.tgl_bukti)= ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ini,
                        SUM(CASE WHEN b.jns_spp IN ('4') AND MONTH(b.tgl_bukti)< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ll,
                        SUM(CASE WHEN b.jns_spp IN ('4') AND MONTH(b.tgl_bukti)= ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ini,
                        SUM(CASE WHEN b.jns_spp IN ('5','6') AND MONTH(b.tgl_bukti)< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ll,
                        SUM(CASE WHEN b.jns_spp IN ('5','6') AND MONTH(b.tgl_bukti)= ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ini
                        FROM trdstrpot a INNER JOIN trhstrpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                        WHERE a.kd_skpd= ?

                        UNION ALL
                        SELECT
                        SUM(CASE WHEN a.jns_beban='1' AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS jlain_up_ll,
                        SUM(CASE WHEN a.jns_beban='1' AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS jlain_up_ini,
                        SUM(CASE WHEN a.jns_beban='4' AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ll,
                        SUM(CASE WHEN a.jns_beban='4' AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ini,
                        SUM(CASE WHEN a.jns_beban in ('5','6') AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ll,
                        SUM(CASE WHEN a.jns_beban in ('5','6') AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ini
                        FROM TRHOUTLAIN a
                        WHERE a.kd_skpd= ? and thnlalu=1
                        ) a ", [$bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd]);
        foreach ($setor_lain as $klr_lain) {
            $lain_gaji_ll    = $klr_lain->lain_gaji_ll;
            $lain_gaji_ini   = $klr_lain->lain_gaji_ini;
            $lain_brjs_ll    = $klr_lain->lain_brjs_ll;
            $lain_brjs_ini   = $klr_lain->lain_brjs_ini;
            $lain_up_ll      = $klr_lain->lain_up_ll;
            $lain_up_ini     = $klr_lain->lain_up_ini;
        }

        // UYHD
        $saldo_uyhd         = DB::table('ms_skpd')->select(['sld_awal', 'sld_awalpajak'])->where('kd_skpd', $kd_skpd)->first();

        // penyetoran tahun lalu
        $tahun_lalu = DB::select("SELECT SUM(ISNULL(tahun_lalu_uyhd,0)) tahun_lalu_uyhd, SUM(ISNULL(tahun_lalu_uyhd_ini,0)) tahun_lalu_uyhd_ini,
                        SUM(ISNULL(tahun_lalu_uyhd_pjk,0)) tahun_lalu_uyhd_pjk, SUM(ISNULL(tahun_lalu_uyhd_pjk_ini,0)) tahun_lalu_uyhd_pjk_ini FROM(
                        SELECT
                        SUM(CASE WHEN a.jns_beban ='1' AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS tahun_lalu_uyhd,
                        SUM(CASE WHEN a.jns_beban ='1' AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS tahun_lalu_uyhd_ini,
                        SUM(CASE WHEN a.jns_beban ='7' AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS tahun_lalu_uyhd_pjk,
                        SUM(CASE WHEN a.jns_beban ='7' AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS tahun_lalu_uyhd_pjk_ini
                        FROM TRHOUTLAIN a
                        WHERE a.kd_skpd= ? and thnlalu=1
                    ) a ", [$bulan, $bulan, $bulan, $bulan, $kd_skpd]);
        foreach ($tahun_lalu as $thn_lalu) {
            $tahun_lalu_uyhd            = $thn_lalu->tahun_lalu_uyhd;
            $tahun_lalu_uyhd_ini        = $thn_lalu->tahun_lalu_uyhd_ini;
            $tahun_lalu_uyhd_pjk        = $thn_lalu->tahun_lalu_uyhd_pjk;
            $tahun_lalu_uyhd_pjk_ini    = $thn_lalu->tahun_lalu_uyhd_pjk_ini;
        }

    } else{
        // START SPJ PERPERIODE
        // tabel rincian
        $rincian = DB::select("exec spj_skpd_tgl ?,?,?,?", array($kd_skpd, $tanggal_awal, $tanggal_akhir, $jns_anggaran));

        // Penerimaan Sp2D
        $terima_sp2d = DB::select("SELECT (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        a.tgl_kas BETWEEN ? AND ? AND c.jns_spp IN ('1','2','3') AND a.status='1') AS sp2d_up_ini,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        a.tgl_kas<? AND c.jns_spp IN ('1','2','3') AND a.status='1') AS sp2d_up_ll,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        a.tgl_kas BETWEEN ? AND ? AND c.jns_spp ='4' AND a.status='1') AS sp2d_gj_ini,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        a.tgl_kas<? AND c.jns_spp ='4'  AND a.status='1') AS sp2d_gj_ll,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        a.tgl_kas BETWEEN ? AND ? AND c.jns_spp in ('5','6')  AND a.status='1') AS sp2d_brjs_ini,
        (SELECT SUM(b.nilai) FROM trhsp2d a INNER JOIN trdspp b
        ON a.no_spp = b.no_spp INNER JOIN trhspp c
        ON a.no_spp = c.no_spp WHERE a.kd_skpd = ? AND
        a.tgl_kas<? AND c.jns_spp in ('5','6') AND a.status='1') AS sp2d_brjs_ll", [$kd_skpd, $tanggal_awal, $tanggal_akhir, $kd_skpd, $tanggal_awal, $kd_skpd, $tanggal_awal, $tanggal_akhir, $kd_skpd, $tanggal_awal, $kd_skpd, $tanggal_awal, $tanggal_akhir, $kd_skpd, $tanggal_awal]);

        foreach ($terima_sp2d as $trm_sp2d) {
            $sp2d_gj_ll     = $trm_sp2d->sp2d_gj_ll;
            $sp2d_gj_ini    = $trm_sp2d->sp2d_gj_ini;
            $sp2d_brjs_ll   = $trm_sp2d->sp2d_brjs_ll;
            $sp2d_brjs_ini  = $trm_sp2d->sp2d_brjs_ini;
            $sp2d_up_ll     = $trm_sp2d->sp2d_up_ll;
            $sp2d_up_ini    = $trm_sp2d->sp2d_up_ini;
        }

        // terima potongan ppn
        $trmpot_pot_ppn = get_terimapotongan2($kd_skpd, '210106010001', $tanggal_awal, $tanggal_akhir);

        foreach ($trmpot_pot_ppn as $trm_ppn) {
            $ppn_gaji_ll    = $trm_ppn->pot_gaji_ll;
            $ppn_gaji_ini   = $trm_ppn->pot_gaji_ini;
            $ppn_brjs_ini   = $trm_ppn->pot_brjs_ini;
            $ppn_brjs_ll    = $trm_ppn->pot_brjs_ll;
            $ppn_up_ll      = $trm_ppn->pot_up_ll;
            $ppn_up_ini     = $trm_ppn->pot_up_ini;
        }
        // terima potongan PPH 21
        $trmpot_pot_pph21 = get_terimapotongan2($kd_skpd, '210105010001', $tanggal_awal, $tanggal_akhir);
        foreach ($trmpot_pot_pph21 as $trm_pph21) {
            $pph21_gaji_ll    = $trm_pph21->pot_gaji_ll;
            $pph21_gaji_ini   = $trm_pph21->pot_gaji_ini;
            $pph21_brjs_ini   = $trm_pph21->pot_brjs_ini;
            $pph21_brjs_ll    = $trm_pph21->pot_brjs_ll;
            $pph21_up_ll      = $trm_pph21->pot_up_ll;
            $pph21_up_ini     = $trm_pph21->pot_up_ini;
        }

        // terima potongan PPH 22
        $trmpot_pot_pph22 = get_terimapotongan2($kd_skpd, '210105020001', $tanggal_awal, $tanggal_akhir);
        foreach ($trmpot_pot_pph22 as $trm_pph22) {
            $pph22_gaji_ll    = $trm_pph22->pot_gaji_ll;
            $pph22_gaji_ini   = $trm_pph22->pot_gaji_ini;
            $pph22_brjs_ini   = $trm_pph22->pot_brjs_ini;
            $pph22_brjs_ll    = $trm_pph22->pot_brjs_ll;
            $pph22_up_ll      = $trm_pph22->pot_up_ll;
            $pph22_up_ini     = $trm_pph22->pot_up_ini;
        }


        // terima potongan PPH 23
        $trmpot_pot_pph23 = get_terimapotongan2($kd_skpd, '210105030001', $tanggal_awal, $tanggal_akhir);
        foreach ($trmpot_pot_pph23 as $trm_pph23) {
            $pph23_gaji_ll    = $trm_pph23->pot_gaji_ll;
            $pph23_gaji_ini   = $trm_pph23->pot_gaji_ini;
            $pph23_brjs_ini   = $trm_pph23->pot_brjs_ini;
            $pph23_brjs_ll    = $trm_pph23->pot_brjs_ll;
            $pph23_up_ll      = $trm_pph23->pot_up_ll;
            $pph23_up_ini     = $trm_pph23->pot_up_ini;
        }

        // terima potongan PPH Pasal 4 Ayat 2
        $trmpot_pot_pph4ayat2 = get_terimapotongan2($kd_skpd, '210109010001', $tanggal_awal, $tanggal_akhir);
        foreach ($trmpot_pot_pph4ayat2 as $trm_pph4ayat2) {
            $pph4ayat2_gaji_ll    = $trm_pph4ayat2->pot_gaji_ll;
            $pph4ayat2_gaji_ini   = $trm_pph4ayat2->pot_gaji_ini;
            $pph4ayat2_brjs_ini   = $trm_pph4ayat2->pot_brjs_ini;
            $pph4ayat2_brjs_ll    = $trm_pph4ayat2->pot_brjs_ll;
            $pph4ayat2_up_ll      = $trm_pph4ayat2->pot_up_ll;
            $pph4ayat2_up_ini     = $trm_pph4ayat2->pot_up_ini;
        }
        //
        // terima IWP
        $terima_iwp = get_terimapotongan2($kd_skpd, '210108010001', $tanggal_awal, $tanggal_akhir);

        foreach ($terima_iwp as $trm_iwp) {
            $iwp_gaji_ll    = $trm_iwp->pot_gaji_ll;
            $iwp_gaji_ini   = $trm_iwp->pot_gaji_ini;
            $iwp_brjs_ini   = $trm_iwp->pot_brjs_ini;
            $iwp_brjs_ll    = $trm_iwp->pot_brjs_ll;
            $iwp_up_ll      = $trm_iwp->pot_up_ll;
            $iwp_up_ini     = $trm_iwp->pot_up_ini;
        }


        // terima taperum
        $terima_taperum = get_terimapotongan2($kd_skpd, '210107010001', $tanggal_awal, $tanggal_akhir);

        foreach ($terima_taperum as $trm_taperum) {
            $taperum_gaji_ll    = $trm_taperum->pot_gaji_ll;
            $taperum_gaji_ini   = $trm_taperum->pot_gaji_ini;
            $taperum_brjs_ini   = $trm_taperum->pot_brjs_ini;
            $taperum_brjs_ll    = $trm_taperum->pot_brjs_ll;
            $taperum_up_ll      = $trm_taperum->pot_up_ll;
            $taperum_up_ini     = $trm_taperum->pot_up_ini;
        }
        //  PPNPN / Jaminan Kesehatan
        $terima_ppnpn = get_terimapotongan2($kd_skpd, '210102010001', $tanggal_awal, $tanggal_akhir);

        foreach ($terima_ppnpn as $trm_ppnpn) {
            $ppnpn_gaji_ll    = $trm_ppnpn->pot_gaji_ll;
            $ppnpn_gaji_ini   = $trm_ppnpn->pot_gaji_ini;
            $ppnpn_brjs_ini   = $trm_ppnpn->pot_brjs_ini;
            $ppnpn_brjs_ll    = $trm_ppnpn->pot_brjs_ll;
            $ppnpn_up_ll      = $trm_ppnpn->pot_up_ll;
            $ppnpn_up_ini     = $trm_ppnpn->pot_up_ini;
        }
        // Denda keterlambatan 410411010001
        $terima_dk = get_terimapotongan2($kd_skpd, '410411010001', $tanggal_awal, $tanggal_akhir);

        foreach ($terima_dk as $trm_dk) {
            $dk_gaji_ll    = $trm_dk->pot_gaji_ll;
            $dk_gaji_ini   = $trm_dk->pot_gaji_ini;
            $dk_brjs_ini   = $trm_dk->pot_brjs_ini;
            $dk_brjs_ll    = $trm_dk->pot_brjs_ll;
            $dk_up_ll      = $trm_dk->pot_up_ll;
            $dk_up_ini     = $trm_dk->pot_up_ini;
        }

        // Pelimpahan
        $terima_pelimpahan = DB::select("SELECT sum(x.bln_lalu) as pot_up_ll,sum(x.bln_ini) as pot_up_ini from(
                                        select
                                                    SUM(CASE WHEN tgl_kas< ? THEN nilai ELSE 0 END) as bln_lalu,
                                                    SUM(CASE WHEN tgl_kas BETWEEN ? AND ? THEN nilai ELSE 0 END) as bln_ini
                                                    from tr_setorpelimpahan
                                                    WHERE kd_skpd= ?
                                        UNION ALL
                                        select
                                                    SUM(CASE WHEN tgl_kas< ? THEN nilai ELSE 0 END) as bln_lalu,
                                                    SUM(CASE WHEN tgl_kas BETWEEN ? AND ? THEN nilai ELSE 0 END) as bln_ini
                                                    from tr_setorpelimpahan_bank
                                                    WHERE kd_skpd= ?
                                        UNION ALL
                                        SELECT
                                                    SUM(CASE WHEN tgl_kas< ? THEN nilai ELSE 0 END) as bln_lalu,
                                                    SUM(CASE WHEN tgl_kas BETWEEN ? AND ? THEN nilai ELSE 0 END) as bln_ini
                                        from tr_setorsimpanan WHERE kd_skpd= ? and jenis='3'
                                        )x", [$tanggal_awal, $tanggal_awal, $tanggal_akhir, $kd_skpd, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $kd_skpd, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $kd_skpd]);
        foreach ($terima_pelimpahan as $trm_pelimpahan) {
            $pelimpahan_up_ll      = $trm_pelimpahan->pot_up_ll;
            $pelimpahan_up_ini     = $trm_pelimpahan->pot_up_ini;
        }

        // Terima PANJAR
        $terima_panjar = DB::select("SELECT SUM(x.pot_up_ll) pot_up_ll, SUM(x.pot_up_ini) pot_up_ini FROM(
                                        SELECT
                                        SUM(CASE WHEN tgl_kas< ? THEN nilai ELSE 0 END) as pot_up_ll,
                                        SUM(CASE WHEN tgl_kas BETWEEN ? AND ? THEN nilai ELSE 0 END) as pot_up_ini
                                        from
                                        tr_jpanjar where jns=1 and kd_skpd= ?
                                        )x", [$tanggal_awal, $tanggal_awal, $tanggal_akhir, $kd_skpd]);
        foreach ($terima_panjar as $trm_panjar) {
            $panjar_up_ll      = $trm_panjar->pot_up_ll;
            $panjar_up_ini     = $trm_panjar->pot_up_ini;
        }

        // BOS
        // $terima_bos = DB::select("SELECT SUM(x.bos_bln_lalu) bos_bln_lalu, SUM(x.bos_bln_ini) bos_bln_ini FROM(
        //                             SELECT
        //                             SUM(CASE WHEN MONTH(tgl_sp2b)< ? THEN b.nilai ELSE 0 END) as bos_bln_lalu,
        //                             SUM(CASE WHEN MONTH(tgl_sp2b)= ? THEN b.nilai ELSE 0 END) as bos_bln_ini
        //                             from trhsp2b a inner join trdsp2b b on a.kd_skpd=b.kd_skpd and a.no_sp2b=b.no_sp2b
        //                             where a.kd_skpd= ?
        //                             UNION

        //                             SELECT
        //                             SUM(CASE WHEN MONTH(tgl_sp2h)< ? THEN b.nilai ELSE 0 END) as bos_bln_lalu,
        //                             SUM(CASE WHEN MONTH(tgl_sp2h)= ? THEN b.nilai ELSE 0 END) as bos_bln_ini
        //                             from trhsp2h a inner join trdsp2h b on a.no_sp2h=b.no_sp2h
        //                             where a.kd_skpd= ? )x", [$bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd]);

        // foreach ($terima_bos as $trm_bos) {
        //     $bos_bln_lalu      = $trm_bos->bos_bln_lalu;
        //     $bos_bln_ini        = $trm_bos->bos_bln_ini;
        // }

        // terima BLUD
        $terima_blud = DB::select("SELECT SUM(CASE WHEN tgl_kas BETWEEN ? AND ? THEN b.nilai ELSE 0 END) as blud_bln_lalu,
        SUM(CASE WHEN tgl_kas BETWEEN ? AND ? THEN b.nilai ELSE 0 END) as blud_bln_ini
        from trhtransout_blud a inner join trdtransout_blud b on a.kd_skpd=b.kd_skpd and a.no_bukti=b.no_bukti
        where a.kd_skpd= ? and left(b.kd_rek6,1)='5' and b.sumber='BLUD'", [$tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_akhir, $kd_skpd]);

        foreach ($terima_blud as $trm_blud) {
            $blud_bln_lalu      = $trm_blud->blud_bln_lalu;
            $blud_bln_ini       = $trm_blud->blud_bln_ini;
        }

        // Terima lain-lain
        $terima_lain = DB::select("SELECT
        SUM(ISNULL(jlain_up_ll,0)) jlain_up_ll, SUM(ISNULL(jlain_up_ini,0)) jlain_up_ini,
        SUM(ISNULL(jlain_gaji_ll,0)) jlain_gaji_ll, SUM(ISNULL(jlain_gaji_ini,0)) jlain_gaji_ini,
        SUM(ISNULL(jlain_brjs_ll,0)) jlain_brjs_ll, SUM(ISNULL(jlain_brjs_ini,0)) jlain_brjs_ini
         FROM(
        SELECT
        SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND b.tgl_bukti< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ll,
        SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND b.tgl_bukti BETWEEN ? AND ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ini,
        SUM(CASE WHEN b.jns_spp IN ('4') AND b.tgl_bukti< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ll,
        SUM(CASE WHEN b.jns_spp IN ('4') AND b.tgl_bukti BETWEEN ? AND ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ini,
        SUM(CASE WHEN b.jns_spp in ('5','6') AND b.tgl_bukti< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ll,
        SUM(CASE WHEN b.jns_spp in ('5','6') AND b.tgl_bukti BETWEEN ? AND ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ini
        FROM trdtrmpot a INNER JOIN trhtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
        WHERE a.kd_skpd= ?
        UNION ALL

        SELECT
        SUM(CASE WHEN a.jns_beban='1' AND a.tgl_bukti< ? THEN  a.nilai ELSE 0 END) AS jlain_up_ll,
        SUM(CASE WHEN a.jns_beban='1' AND a.tgl_bukti BETWEEN ? AND ? THEN  a.nilai ELSE 0 END) AS jlain_up_ini,
        SUM(CASE WHEN a.jns_beban='4' AND a.tgl_bukti< ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ll,
        SUM(CASE WHEN a.jns_beban='4' AND a.tgl_bukti BETWEEN ? AND ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ini,
        SUM(CASE WHEN a.jns_beban in ('5','6') AND a.tgl_bukti< ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ll,
        SUM(CASE WHEN a.jns_beban in ('5','6') AND a.tgl_bukti BETWEEN ? AND ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ini
        FROM TRHINLAIN a WHERE pengurang_belanja !='1'
        AND a.kd_skpd= ?
        union all
        SELECT
		isnull(sld_awal_bank,0) + isnull(sld_awal_tunai,0) AS jlain_up_ll, '0' as jlain_up_ini, '0' as jlain_gaji_ll ,'0' as jlain_gaji_ini, '0' as jlain_brjs_ll, '0' as jlain_brjs_ini FROM ms_skpd where kd_skpd= ?
        ) a ", [$tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir,  $kd_skpd, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir,  $kd_skpd, $kd_skpd]);

        foreach ($terima_lain as $trm_lain) {
            $jlain_gaji_ll    = $trm_lain->jlain_gaji_ll;
            $jlain_gaji_ini   = $trm_lain->jlain_gaji_ini;
            $jlain_brjs_ll    = $trm_lain->jlain_brjs_ll;
            $jlain_brjs_ini   = $trm_lain->jlain_brjs_ini;
            $jlain_up_ll      = $trm_lain->jlain_up_ll;
            $jlain_up_ini     = $trm_lain->jlain_up_ini;
        }

        // Pengeluaran
        $keluar_spj = DB::select("SELECT sum(gaji_lalu) as spj_gaji_ll, sum(gaji_ini) as spj_gaji_ini, sum(brg_lalu) as spj_brjs_ll,
        sum(brg_ini) as spj_brjs_ini, sum(up_lalu) as spj_up_ll, sum(up_ini) as spj_up_ini from
        (select  a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where b.tgl_bukti BETWEEN ? AND ?
        --and panjar not in (5)
        AND jns_spp in (1,2,3) and pay not in ('PANJAR')
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout_blud a join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where b.tgl_bukti BETWEEN ? AND ?  and left(kd_rek6,1)='5' and jns_spp in (1,2,3)
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai*-1,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdinlain a join TRHINLAIN b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where b.TGL_BUKTI BETWEEN ? AND ? and b.pengurang_belanja=1
        union all
        select a.kd_skpd, isnull(a.nilai,0) as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where b.tgl_bukti BETWEEN ? AND ?
        --and panjar not in (5)
        AND jns_spp in (4)
        union all
        select  a.kd_skpd, isnull(a.rupiah*-1,0) as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
        where b.tgl_sts BETWEEN ? AND ? and b.jns_cp in (1) and b.pot_khusus not in (0,1,2,3)
        union all
        select a.kd_skpd, 0 as gaji_ini, isnull(a.nilai,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where b.tgl_bukti BETWEEN ? AND ?
        --and panjar not in (5)
        AND jns_spp in ('5','6')
        union all
        select  a.kd_skpd, 0 as gaji_ini, isnull(a.rupiah*-1,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
        where b.tgl_sts BETWEEN ? AND ? and b.jns_cp in (2) and b.pot_khusus not in (0,1,2,3)
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, isnull(a.nilai,0) as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where b.tgl_bukti<?
        --and panjar not in (5)
        AND jns_spp in (1,2,3) and pay not in ('PANJAR')
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, isnull(a.nilai*-1,0) as up_lalu from trdinlain a join TRHINLAIN b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where b.TGL_BUKTI<? and b.pengurang_belanja=1
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, isnull(a.nilai,0) as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where b.tgl_bukti<?
        --and panjar not in (5)
        AND jns_spp in (4)
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, isnull(a.rupiah*-1,0) as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
        where b.tgl_sts<? and b.jns_cp in (1) and b.pot_khusus not in (0,1,2,3)
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.nilai,0) as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        where b.tgl_bukti<?
        --and panjar not in (5)
        AND jns_spp in ('5','6')
        union all
        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.rupiah*-1,0) as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
        where b.tgl_sts<? and b.jns_cp in (2) and b.pot_khusus not in (0,1,2,3)) a
        WHERE a.kd_skpd=?", [$tanggal_awal, $tanggal_akhir,$tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_awal, $tanggal_awal, $tanggal_awal, $tanggal_awal, $kd_skpd]);
        foreach ($keluar_spj as $klr_spj) {
            $spj_gaji_ll    = $klr_spj->spj_gaji_ll;
            $spj_gaji_ini   = $klr_spj->spj_gaji_ini;
            $spj_brjs_ll    = $klr_spj->spj_brjs_ll;
            $spj_brjs_ini   = $klr_spj->spj_brjs_ini;
            $spj_up_ll      = $klr_spj->spj_up_ll;
            $spj_up_ini     = $klr_spj->spj_up_ini;
        }

        // setor PPN
        $setor_ppn    = get_setorpotongan2($kd_skpd, '210106010001', $tanggal_awal, $tanggal_akhir);
        foreach ($setor_ppn as $str_ppn) {
            $str_ppn_gaji_ll    = $str_ppn->pot_gaji_ll;
            $str_ppn_gaji_ini   = $str_ppn->pot_gaji_ini;
            $str_ppn_brjs_ini   = $str_ppn->pot_brjs_ini;
            $str_ppn_brjs_ll    = $str_ppn->pot_brjs_ll;
            $str_ppn_up_ll      = $str_ppn->pot_up_ll;
            $str_ppn_up_ini     = $str_ppn->pot_up_ini;
        }


        $setor_pph21  = get_setorpotongan2($kd_skpd, '210105010001', $tanggal_awal, $tanggal_akhir);
        foreach ($setor_pph21 as $str_pph21) {
            $str_pph21_gaji_ll    = $str_pph21->pot_gaji_ll;
            $str_pph21_gaji_ini   = $str_pph21->pot_gaji_ini;
            $str_pph21_brjs_ini   = $str_pph21->pot_brjs_ini;
            $str_pph21_brjs_ll    = $str_pph21->pot_brjs_ll;
            $str_pph21_up_ll      = $str_pph21->pot_up_ll;
            $str_pph21_up_ini     = $str_pph21->pot_up_ini;
        }
        // PPH22
        $setor_pph22  = get_setorpotongan2($kd_skpd, '210105020001', $tanggal_awal, $tanggal_akhir);
        foreach ($setor_pph22 as $str_pph22) {
            $str_pph22_gaji_ll    = $str_pph22->pot_gaji_ll;
            $str_pph22_gaji_ini   = $str_pph22->pot_gaji_ini;
            $str_pph22_brjs_ini   = $str_pph22->pot_brjs_ini;
            $str_pph22_brjs_ll    = $str_pph22->pot_brjs_ll;
            $str_pph22_up_ll      = $str_pph22->pot_up_ll;
            $str_pph22_up_ini     = $str_pph22->pot_up_ini;
        }
        // PPh23
        $setor_pph23  = get_setorpotongan2($kd_skpd, '210105030001', $tanggal_awal, $tanggal_akhir);
        foreach ($setor_pph23 as $str_pph23) {
            $str_pph23_gaji_ll    = $str_pph23->pot_gaji_ll;
            $str_pph23_gaji_ini   = $str_pph23->pot_gaji_ini;
            $str_pph23_brjs_ini   = $str_pph23->pot_brjs_ini;
            $str_pph23_brjs_ll    = $str_pph23->pot_brjs_ll;
            $str_pph23_up_ll      = $str_pph23->pot_up_ll;
            $str_pph23_up_ini     = $str_pph23->pot_up_ini;
        }
        // pasal 4 ayat 2
        $setor_pph4ayat2  = get_setorpotongan2($kd_skpd, '210109010001', $tanggal_awal, $tanggal_akhir);
        foreach ($setor_pph4ayat2 as $str_pph4ayat2) {
            $str_pph4ayat2_gaji_ll    = $str_pph4ayat2->pot_gaji_ll;
            $str_pph4ayat2_gaji_ini   = $str_pph4ayat2->pot_gaji_ini;
            $str_pph4ayat2_brjs_ini   = $str_pph4ayat2->pot_brjs_ini;
            $str_pph4ayat2_brjs_ll    = $str_pph4ayat2->pot_brjs_ll;
            $str_pph4ayat2_up_ll      = $str_pph4ayat2->pot_up_ll;
            $str_pph4ayat2_up_ini     = $str_pph4ayat2->pot_up_ini;
        }
        // IWP
        $setor_iwp  = get_setorpotongan2($kd_skpd, '210108010001', $tanggal_awal, $tanggal_akhir);
        foreach ($setor_iwp as $str_iwp) {
            $str_iwp_gaji_ll    = $str_iwp->pot_gaji_ll;
            $str_iwp_gaji_ini   = $str_iwp->pot_gaji_ini;
            $str_iwp_brjs_ini   = $str_iwp->pot_brjs_ini;
            $str_iwp_brjs_ll    = $str_iwp->pot_brjs_ll;
            $str_iwp_up_ll      = $str_iwp->pot_up_ll;
            $str_iwp_up_ini     = $str_iwp->pot_up_ini;
        }

        // taperum
        $setor_taperum  = get_setorpotongan2($kd_skpd, '210107010001', $tanggal_awal, $tanggal_akhir);
        foreach ($setor_taperum as $str_taperum) {
            $str_taperum_gaji_ll    = $str_taperum->pot_gaji_ll;
            $str_taperum_gaji_ini   = $str_taperum->pot_gaji_ini;
            $str_taperum_brjs_ini   = $str_taperum->pot_brjs_ini;
            $str_taperum_brjs_ll    = $str_taperum->pot_brjs_ll;
            $str_taperum_up_ll      = $str_taperum->pot_up_ll;
            $str_taperum_up_ini     = $str_taperum->pot_up_ini;
        }
        // taperum
        $setor_ppnpn  = get_setorpotongan2($kd_skpd, '210102010001', $tanggal_awal, $tanggal_akhir);
        foreach ($setor_ppnpn as $str_ppnpn) {
            $str_ppnpn_gaji_ll    = $str_ppnpn->pot_gaji_ll;
            $str_ppnpn_gaji_ini   = $str_ppnpn->pot_gaji_ini;
            $str_ppnpn_brjs_ini   = $str_ppnpn->pot_brjs_ini;
            $str_ppnpn_brjs_ll    = $str_ppnpn->pot_brjs_ll;
            $str_ppnpn_up_ll      = $str_ppnpn->pot_up_ll;
            $str_ppnpn_up_ini     = $str_ppnpn->pot_up_ini;
        }

        // dk
        $setor_dk  = get_setorpotongan2($kd_skpd, '410411010001', $tanggal_awal, $tanggal_akhir);
        foreach ($setor_dk as $str_dk) {
            $str_dk_gaji_ll    = $str_dk->pot_gaji_ll;
            $str_dk_gaji_ini   = $str_dk->pot_gaji_ini;
            $str_dk_brjs_ini   = $str_dk->pot_brjs_ini;
            $str_dk_brjs_ll    = $str_dk->pot_brjs_ll;
            $str_dk_up_ll      = $str_dk->pot_up_ll;
            $str_dk_up_ini     = $str_dk->pot_up_ini;
        }

        // potongan penghasilan lainnya
        $setor_pplain = DB::select("SELECT
                    SUM(ISNULL(up_lain_lalu,0)) up_lain_lalu, SUM(ISNULL(up_lain_ini,0)) up_lain_ini,
                    SUM(ISNULL(ls_lain_lalu,0)) ls_lain_lalu, SUM(ISNULL(ls_lain_ini,0)) ls_lain_ini,
                    SUM(ISNULL(gj_lain_lalu,0)) gj_lain_lalu, SUM(ISNULL(gj_lain_ini,0)) gj_lain_ini
                    FROM(

                    SELECT
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '3' and tgl_sts< ?  then a.rupiah else 0 end),0)) AS up_lain_lalu,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '3' and tgl_sts BETWEEN ? AND ?  then a.rupiah else 0 end),0)) AS up_lain_ini,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '2' and tgl_sts< ?  then a.rupiah else 0 end),0)) AS ls_lain_lalu,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '2' and tgl_sts BETWEEN ? AND ?  then a.rupiah else 0 end),0)) AS ls_lain_ini,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '1' and tgl_sts< ?  then a.rupiah else 0 end),0)) AS gj_lain_lalu,
                SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '1' and tgl_sts BETWEEN ? AND ?  then a.rupiah else 0 end),0)) AS gj_lain_ini
                FROM trdkasin_pkd a
                INNER JOIN trhkasin_pkd b on a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                WHERE a.kd_skpd = ? AND jns_trans='5'

                    )zzz", [$tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $kd_skpd]);
        foreach ($setor_pplain as $str_pplain) {
            $str_pplain_gaji_ll    = $str_pplain->gj_lain_lalu;
            $str_pplain_gaji_ini   = $str_pplain->gj_lain_ini;
            $str_pplain_brjs_ini   = $str_pplain->ls_lain_lalu;
            $str_pplain_brjs_ll    = $str_pplain->ls_lain_ini;
            $str_pplain_up_ll      = $str_pplain->up_lain_lalu;
            $str_pplain_up_ini     = $str_pplain->up_lain_ini;
        }

        // HKPG
        $setor_hkpg = DB::select("SELECT
                                SUM(ISNULL(up_hkpg_lalu,0)) up_hkpg_lalu, SUM(ISNULL(up_hkpg_ini,0)) up_hkpg_ini,
                                SUM(ISNULL(ls_hkpg_lalu,0)) ls_hkpg_lalu, SUM(ISNULL(ls_hkpg_ini,0)) ls_hkpg_ini,
                                SUM(ISNULL(gj_hkpg_lalu,0)) gj_hkpg_lalu, SUM(ISNULL(gj_hkpg_ini,0)) gj_hkpg_ini
                                FROM(

                                    SELECT
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '3' and tgl_sts<? then a.rupiah else 0 end),0)) AS up_hkpg_lalu,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '3' and tgl_sts BETWEEN ? AND ? then a.rupiah else 0 end),0)) AS up_hkpg_ini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '3' and tgl_sts<=? then a.rupiah else 0 end),0)) AS up_hkpg_sdini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '2' and tgl_sts<? then a.rupiah else 0 end),0)) AS ls_hkpg_lalu,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '2' and tgl_sts BETWEEN ? AND ? then a.rupiah else 0 end),0)) AS ls_hkpg_ini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '2' and tgl_sts<=? then a.rupiah else 0 end),0)) AS ls_hkpg_sdini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '1' and tgl_sts<? then a.rupiah else 0 end),0)) AS gj_hkpg_lalu,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '1' and tgl_sts BETWEEN ? AND ? then a.rupiah else 0 end),0)) AS gj_hkpg_ini,
                SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '1' and tgl_sts<=? then a.rupiah else 0 end),0)) AS gj_hkpg_sdini
                FROM trdkasin_pkd a
                INNER JOIN trhkasin_pkd b on a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                WHERE a.kd_skpd = ? AND jns_trans='5' AND LEFT(kd_rek6,1)<>4
                    )zz", [$tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_awal, $tanggal_akhir,$tanggal_awal, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $kd_skpd]);
        foreach ($setor_hkpg as $str_hkpg) {
            $str_hkpg_gaji_ll    = $str_hkpg->gj_hkpg_lalu;
            $str_hkpg_gaji_ini   = $str_hkpg->gj_hkpg_ini;
            $str_hkpg_brjs_ini   = $str_hkpg->ls_hkpg_lalu;
            $str_hkpg_brjs_ll    = $str_hkpg->ls_hkpg_ini;
            $str_hkpg_up_ll      = $str_hkpg->up_hkpg_lalu;
            $str_hkpg_up_ini     = $str_hkpg->up_hkpg_ini;
        }

        // CP (Contra Post)
        $setor_cp = DB::select(
            "SELECT
                        SUM(isnull((case when rtrim(jns_cp)= '3' and tgl_sts BETWEEN ? AND ? then z.nilai else 0 end),0)) AS up_cp_ini,
                        SUM(isnull((case when rtrim(jns_cp)= '3' and tgl_sts< ? then z.nilai else 0 end),0)) AS up_cp_lalu,
                        SUM(isnull((case when rtrim(jns_cp)= '1' and tgl_sts BETWEEN ? AND ? then z.nilai else 0 end),0)) AS gj_cp_ini,
                        SUM(isnull((case when rtrim(jns_cp)= '1' and tgl_sts< ? then z.nilai else 0 end),0)) AS gj_cp_lalu,
                        SUM(isnull((case when rtrim(jns_cp)= '2' and tgl_sts BETWEEN ? AND ? then z.nilai else 0 end),0)) AS ls_cp_ini,
                        SUM(isnull((case when rtrim(jns_cp)= '2' and tgl_sts< ? then z.nilai else 0 end),0)) AS ls_cp_lalu
                        from (select rupiah as nilai,jns_trans,pot_khusus,jns_cp,d.tgl_sts ,d.kd_skpd from
                        trdkasin_pkd c INNER JOIN trhkasin_pkd d ON c.no_sts = d.no_sts AND c.kd_skpd = d.kd_skpd where d.kd_skpd = ? AND
                        ((jns_trans='5' AND pot_khusus='0') OR jns_trans='1')) z",
            [$tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $kd_skpd]
        );

        foreach ($setor_cp as $str_cp) {
            $str_cp_gaji_ll    = $str_cp->gj_cp_lalu;
            $str_cp_gaji_ini   = $str_cp->gj_cp_ini;
            $str_cp_brjs_ini   = $str_cp->ls_cp_lalu;
            $str_cp_brjs_ll    = $str_cp->ls_cp_ini;
            $str_cp_up_ll      = $str_cp->up_cp_lalu;
            $str_cp_up_ini     = $str_cp->up_cp_ini;
        }

        // Pelimpahan UP/GU
        $setor_pelimpahan = DB::select("SELECT SUM(z.bln_lalu) up_pelimpahan_lalu, SUM(z.bln_ini) up_pelimpahan_ini from(
                                        select
                                        SUM(CASE WHEN tgl_kas< ? THEN nilai ELSE 0 END) as bln_lalu,
                                        SUM(CASE WHEN tgl_kas BETWEEN ? AND ? THEN nilai ELSE 0 END) as bln_ini
                                        from tr_setorpelimpahan_bank
                                        WHERE kd_skpd_sumber= ?
                                        UNION ALL
                                        select
                                        SUM(CASE WHEN tgl_kas< ? THEN nilai ELSE 0 END) as bln_lalu,
                                        SUM(CASE WHEN tgl_kas BETWEEN ? AND ? THEN nilai ELSE 0 END) as bln_ini
                                        from tr_setorpelimpahan
                                        WHERE kd_skpd_sumber= ?
                                        )z", [$tanggal_awal, $tanggal_awal, $tanggal_akhir, $kd_skpd, $tanggal_awal, $tanggal_awal, $tanggal_akhir,$kd_skpd]);
        foreach ($setor_pelimpahan as $str_pelimpahan) {
            $str_pelimpahan_up_ll      = $str_pelimpahan->up_pelimpahan_lalu;
            $str_pelimpahan_up_ini     = $str_pelimpahan->up_pelimpahan_ini;
        }

        // setor panjar
        $setor_panjar   = DB::select("SELECT
                        SUM(CASE WHEN tgl_kas< ? THEN nilai ELSE 0 END) as up_panjar_lalu,
                        SUM(CASE WHEN tgl_kas BETWEEN ? AND ? THEN nilai ELSE 0 END) as up_panjar_ini
                        from tr_panjar
                        WHERE kd_skpd= ? and jns='1'", [$tanggal_awal, $tanggal_awal, $tanggal_akhir, $kd_skpd]);

        foreach ($setor_panjar as $str_panjar) {
            $str_panjar_up_ll      = $str_panjar->up_panjar_lalu;
            $str_panjar_up_ini     = $str_panjar->up_panjar_ini;
        }

        // setor BOS
        // $setor_bos  = DB::select("SELECT
        //                 SUM(CASE WHEN MONTH(tgl_bukti)< ? THEN a.nilai ELSE 0 END) as gj_bos_lalu,
        //                 SUM(CASE WHEN MONTH(tgl_bukti)= ? THEN a.nilai ELSE 0 END) as gj_bos_ini
        //                 from trdtransout_blud a inner join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        //                 WHERE a.kd_skpd= ? AND jns_spp in (6) and left(a.kd_rek6,1) IN ('5')", [$bulan, $bulan, $kd_skpd]);

        // foreach ($setor_bos as $str_bos) {
        //     $str_bos_gj_ll      = $str_bos->gj_bos_lalu;
        //     $str_bos_gj_ini     = $str_bos->gj_bos_ini;
        // }

        // Setor BLUD
        $setor_blud     = DB::select("SELECT
                    SUM(CASE WHEN tgl_bukti BETWEEN ? AND ? THEN a.nilai ELSE 0 END) as ls_blud_lalu,
                    SUM(CASE WHEN tgl_bukti BETWEEN ? AND ? THEN a.nilai ELSE 0 END) as ls_blud_ini
                    from trdtransout_blud a inner join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                    WHERE a.kd_skpd= ? and right(kd_rek6,6)='999999'", [$tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_akhir, $kd_skpd]);
        foreach ($setor_blud as $str_blud) {
            $str_blud_ls_ll      = $str_blud->ls_blud_lalu;
            $str_blud_ls_ini     = $str_blud->ls_blud_ini;
        }

        // setor lain-lain
        $setor_lain     = DB::select("SELECT
                        SUM(ISNULL(jlain_up_ll,0)) lain_up_ll, SUM(ISNULL(jlain_up_ini,0)) lain_up_ini,
                        SUM(ISNULL(jlain_gaji_ll,0)) lain_gaji_ll, SUM(ISNULL(jlain_gaji_ini,0)) lain_gaji_ini,
                        SUM(ISNULL(jlain_brjs_ll,0)) lain_brjs_ll, SUM(ISNULL(jlain_brjs_ini,0)) lain_brjs_ini
                        FROM(
                        SELECT
                        SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND b.tgl_bukti< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ll,
                        SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND b.tgl_bukti BETWEEN ? AND ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ini,
                        SUM(CASE WHEN b.jns_spp IN ('4') AND b.tgl_bukti< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ll,
                        SUM(CASE WHEN b.jns_spp IN ('4') AND b.tgl_bukti BETWEEN ? AND ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ini,
                        SUM(CASE WHEN b.jns_spp IN ('5','6') AND b.tgl_bukti< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ll,
                        SUM(CASE WHEN b.jns_spp IN ('5','6') AND b.tgl_bukti BETWEEN ? AND ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ini
                        FROM trdstrpot a INNER JOIN trhstrpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                        WHERE a.kd_skpd= ?

                        UNION ALL
                        SELECT
                        SUM(CASE WHEN a.jns_beban='1' AND a.tgl_bukti< ? THEN  a.nilai ELSE 0 END) AS jlain_up_ll,
                        SUM(CASE WHEN a.jns_beban='1' AND a.tgl_bukti BETWEEN ? AND ? THEN  a.nilai ELSE 0 END) AS jlain_up_ini,
                        SUM(CASE WHEN a.jns_beban='4' AND a.tgl_bukti< ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ll,
                        SUM(CASE WHEN a.jns_beban='4' AND a.tgl_bukti BETWEEN ? AND ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ini,
                        SUM(CASE WHEN a.jns_beban in ('5','6') AND a.tgl_bukti< ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ll,
                        SUM(CASE WHEN a.jns_beban in ('5','6') AND a.tgl_bukti BETWEEN ? AND ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ini
                        FROM TRHOUTLAIN a
                        WHERE a.kd_skpd= ? and thnlalu=1
                        ) a ", [$tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $kd_skpd, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir, $kd_skpd]);
        foreach ($setor_lain as $klr_lain) {
            $lain_gaji_ll    = $klr_lain->lain_gaji_ll;
            $lain_gaji_ini   = $klr_lain->lain_gaji_ini;
            $lain_brjs_ll    = $klr_lain->lain_brjs_ll;
            $lain_brjs_ini   = $klr_lain->lain_brjs_ini;
            $lain_up_ll      = $klr_lain->lain_up_ll;
            $lain_up_ini     = $klr_lain->lain_up_ini;
        }

        // UYHD
        $saldo_uyhd         = DB::table('ms_skpd')->select(['sld_awal', 'sld_awalpajak'])->where('kd_skpd', $kd_skpd)->first();

        // penyetoran tahun lalu
        $tahun_lalu = DB::select("SELECT SUM(ISNULL(tahun_lalu_uyhd,0)) tahun_lalu_uyhd, SUM(ISNULL(tahun_lalu_uyhd_ini,0)) tahun_lalu_uyhd_ini,
                        SUM(ISNULL(tahun_lalu_uyhd_pjk,0)) tahun_lalu_uyhd_pjk, SUM(ISNULL(tahun_lalu_uyhd_pjk_ini,0)) tahun_lalu_uyhd_pjk_ini FROM(
                        SELECT
                        SUM(CASE WHEN a.jns_beban ='1' AND a.tgl_bukti< ? THEN  a.nilai ELSE 0 END) AS tahun_lalu_uyhd,
                        SUM(CASE WHEN a.jns_beban ='1' AND a.tgl_bukti BETWEEN ? AND ? THEN  a.nilai ELSE 0 END) AS tahun_lalu_uyhd_ini,
                        SUM(CASE WHEN a.jns_beban ='7' AND a.tgl_bukti< ? THEN  a.nilai ELSE 0 END) AS tahun_lalu_uyhd_pjk,
                        SUM(CASE WHEN a.jns_beban ='7' AND a.tgl_bukti BETWEEN ? AND ? THEN  a.nilai ELSE 0 END) AS tahun_lalu_uyhd_pjk_ini
                        FROM TRHOUTLAIN a
                        WHERE a.kd_skpd= ? and thnlalu=1
                    ) a ", [$tanggal_awal, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_awal, $tanggal_akhir,  $kd_skpd]);
        foreach ($tahun_lalu as $thn_lalu) {
            $tahun_lalu_uyhd            = $thn_lalu->tahun_lalu_uyhd;
            $tahun_lalu_uyhd_ini        = $thn_lalu->tahun_lalu_uyhd_ini;
            $tahun_lalu_uyhd_pjk        = $thn_lalu->tahun_lalu_uyhd_pjk;
            $tahun_lalu_uyhd_pjk_ini    = $thn_lalu->tahun_lalu_uyhd_pjk_ini;
        }
    }
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'rincian'           => $rincian,
            'judul'             => $judul,
            'periode'           => $pilihan_spj,
            'tanggal_awal'      => $tanggal_awal,
            'tanggal_akhir'     => $tanggal_akhir,
            // terima
            // sp2d
            'sp2d_gj_ll'        => $sp2d_gj_ll,
            'sp2d_gj_ini'       => $sp2d_gj_ini,
            'sp2d_brjs_ll'      => $sp2d_brjs_ll,
            'sp2d_brjs_ini'     => $sp2d_brjs_ini,
            'sp2d_up_ll'        => $sp2d_up_ll,
            'sp2d_up_ini'       => $sp2d_up_ini,
            // potongan
            'ppn_gaji_ll'       => $ppn_gaji_ll,
            'ppn_gaji_ini'      => $ppn_gaji_ini,
            'ppn_brjs_ini'      => $ppn_brjs_ini,
            'ppn_brjs_ll'       => $ppn_brjs_ll,
            'ppn_up_ll'         => $ppn_up_ll,
            'ppn_up_ini'        => $ppn_up_ini,

            'pph21_gaji_ll'     => $pph21_gaji_ll,
            'pph21_gaji_ini'    => $pph21_gaji_ini,
            'pph21_brjs_ini'    => $pph21_brjs_ini,
            'pph21_brjs_ll'     => $pph21_brjs_ll,
            'pph21_up_ll'       => $pph21_up_ll,
            'pph21_up_ini'      => $pph21_up_ini,

            'pph22_gaji_ll'     => $pph22_gaji_ll,
            'pph22_gaji_ini'    => $pph22_gaji_ini,
            'pph22_brjs_ini'    => $pph22_brjs_ini,
            'pph22_brjs_ll'     => $pph22_brjs_ll,
            'pph22_up_ll'       => $pph22_up_ll,
            'pph22_up_ini'      => $pph22_up_ini,

            'pph23_gaji_ll'     => $pph23_gaji_ll,
            'pph23_gaji_ini'    => $pph23_gaji_ini,
            'pph23_brjs_ini'    => $pph23_brjs_ini,
            'pph23_brjs_ll'     => $pph23_brjs_ll,
            'pph23_up_ll'       => $pph23_up_ll,
            'pph23_up_ini'      => $pph23_up_ini,

            'pph4ayat2_gaji_ll'     => $pph4ayat2_gaji_ll,
            'pph4ayat2_gaji_ini'    => $pph4ayat2_gaji_ini,
            'pph4ayat2_brjs_ini'    => $pph4ayat2_brjs_ini,
            'pph4ayat2_brjs_ll'     => $pph4ayat2_brjs_ll,
            'pph4ayat2_up_ll'       => $pph4ayat2_up_ll,
            'pph4ayat2_up_ini'      => $pph4ayat2_up_ini,

            // IWP
            'trm_iwp_gaji_ll'   => $iwp_gaji_ll,
            'trm_iwp_gaji_ini'  => $iwp_gaji_ini,
            'trm_iwp_brjs_ini'  => $iwp_brjs_ini,
            'trm_iwp_brjs_ll'   => $iwp_brjs_ll,
            'trm_iwp_up_ll'     => $iwp_up_ll,
            'trm_iwp_up_ini'    => $iwp_up_ini,

            // taperum
            'trm_taperum_gaji_ll'   => $taperum_gaji_ll,
            'trm_taperum_gaji_ini'  => $taperum_gaji_ini,
            'trm_taperum_brjs_ini'  => $taperum_brjs_ini,
            'trm_taperum_brjs_ll'   => $taperum_brjs_ll,
            'trm_taperum_up_ll'     => $taperum_up_ll,
            'trm_taperum_up_ini'    => $taperum_up_ini,

            // ppnpn
            'trm_ppnpn_gaji_ll'   => $ppnpn_gaji_ll,
            'trm_ppnpn_gaji_ini'  => $ppnpn_gaji_ini,
            'trm_ppnpn_brjs_ini'  => $ppnpn_brjs_ini,
            'trm_ppnpn_brjs_ll'   => $ppnpn_brjs_ll,
            'trm_ppnpn_up_ll'     => $ppnpn_up_ll,
            'trm_ppnpn_up_ini'    => $ppnpn_up_ini,

            // denda keterlambatan
            'trm_dk_gaji_ll'   => $dk_gaji_ll,
            'trm_dk_gaji_ini'  => $dk_gaji_ini,
            'trm_dk_brjs_ini'  => $dk_brjs_ini,
            'trm_dk_brjs_ll'   => $dk_brjs_ll,
            'trm_dk_up_ll'     => $dk_up_ll,
            'trm_dk_up_ini'    => $dk_up_ini,

            // Pelimpahan
            'pelimpahan_up_ll' => $pelimpahan_up_ll,
            'pelimpahan_up_ini' => $pelimpahan_up_ini,

            // panjar
            'panjar_up_ll'      => $panjar_up_ll,
            'panjar_up_ini'     => $panjar_up_ini,

            // BOS
            // 'bos_bln_lalu'       => $bos_bln_lalu,
            // 'bos_bln_ini'        => $bos_bln_ini,

            // // BLUD
            'blud_bln_lalu'     => $blud_bln_lalu,
            'blud_bln_ini'      => $blud_bln_ini,

            // Lainnya
            'jlain_gaji_ll'     => $jlain_gaji_ll,
            'jlain_gaji_ini'    => $jlain_gaji_ini,
            'jlain_brjs_ll'     => $jlain_brjs_ll,
            'jlain_brjs_ini'    => $jlain_brjs_ini,
            'jlain_up_ll'       => $jlain_up_ll,
            'jlain_up_ini'      => $jlain_up_ini,
            // keluar
            // S{J}
            'spj_gaji_ll'       => $spj_gaji_ll,
            'spj_gaji_ini'      => $spj_gaji_ini,
            'spj_brjs_ll'       => $spj_brjs_ll,
            'spj_brjs_ini'      => $spj_brjs_ini,
            'spj_up_ll'         => $spj_up_ll,
            'spj_up_ini'        => $spj_up_ini,
            // setor PPN
            'str_ppn_gaji_ll'   => $str_ppn_gaji_ll,
            'str_ppn_gaji_ini'  => $str_ppn_gaji_ini,
            'str_ppn_brjs_ini'  => $str_ppn_brjs_ini,
            'str_ppn_brjs_ll'   => $str_ppn_brjs_ll,
            'str_ppn_up_ll'     => $str_ppn_up_ll,
            'str_ppn_up_ini'    => $str_ppn_up_ini,
            // setor pph21
            'str_pph21_gaji_ll' => $str_pph21_gaji_ll,
            'str_pph21_gaji_ini' => $str_pph21_gaji_ini,
            'str_pph21_brjs_ini' => $str_pph21_brjs_ini,
            'str_pph21_brjs_ll' => $str_pph21_brjs_ll,
            'str_pph21_up_ll'   => $str_pph21_up_ll,
            'str_pph21_up_ini'  => $str_pph21_up_ini,
            // setor pph22
            'str_pph22_gaji_ll' => $str_pph22_gaji_ll,
            'str_pph22_gaji_ini' => $str_pph22_gaji_ini,
            'str_pph22_brjs_ini' => $str_pph22_brjs_ini,
            'str_pph22_brjs_ll' => $str_pph22_brjs_ll,
            'str_pph22_up_ll'   => $str_pph22_up_ll,
            'str_pph22_up_ini'  => $str_pph22_up_ini,
            // setor pph23
            'str_pph23_gaji_ll' => $str_pph23_gaji_ll,
            'str_pph23_gaji_ini' => $str_pph23_gaji_ini,
            'str_pph23_brjs_ini' => $str_pph23_brjs_ini,
            'str_pph23_brjs_ll' => $str_pph23_brjs_ll,
            'str_pph23_up_ll'   => $str_pph23_up_ll,
            'str_pph23_up_ini'  => $str_pph23_up_ini,
            // setor pph4ayat2
            'str_pph4ayat2_gaji_ll' => $str_pph4ayat2_gaji_ll,
            'str_pph4ayat2_gaji_ini' => $str_pph4ayat2_gaji_ini,
            'str_pph4ayat2_brjs_ini' => $str_pph4ayat2_brjs_ini,
            'str_pph4ayat2_brjs_ll' => $str_pph4ayat2_brjs_ll,
            'str_pph4ayat2_up_ll'   => $str_pph4ayat2_up_ll,
            'str_pph4ayat2_up_ini'  => $str_pph4ayat2_up_ini,
            // setor iwp
            'str_iwp_gaji_ll'       => $str_iwp_gaji_ll,
            'str_iwp_gaji_ini'      => $str_iwp_gaji_ini,
            'str_iwp_brjs_ini'      => $str_iwp_brjs_ini,
            'str_iwp_brjs_ll'       => $str_iwp_brjs_ll,
            'str_iwp_up_ll'         => $str_iwp_up_ll,
            'str_iwp_up_ini'        => $str_iwp_up_ini,
            // setor taperum
            'str_taperum_gaji_ll'   => $str_taperum_gaji_ll,
            'str_taperum_gaji_ini'  => $str_taperum_gaji_ini,
            'str_taperum_brjs_ini'  => $str_taperum_brjs_ini,
            'str_taperum_brjs_ll'   => $str_taperum_brjs_ll,
            'str_taperum_up_ll'     => $str_taperum_up_ll,
            'str_taperum_up_ini'    => $str_taperum_up_ini,
            // setor ppnpn
            'str_ppnpn_gaji_ll'     => $str_ppnpn_gaji_ll,
            'str_ppnpn_gaji_ini'    => $str_ppnpn_gaji_ini,
            'str_ppnpn_brjs_ini'    => $str_ppnpn_brjs_ini,
            'str_ppnpn_brjs_ll'     => $str_ppnpn_brjs_ll,
            'str_ppnpn_up_ll'       => $str_ppnpn_up_ll,
            'str_ppnpn_up_ini'      => $str_ppnpn_up_ini,
            // setor dk
            'str_dk_gaji_ll'        => $str_dk_gaji_ll,
            'str_dk_gaji_ini'       => $str_dk_gaji_ini,
            'str_dk_brjs_ini'       => $str_dk_brjs_ini,
            'str_dk_brjs_ll'        => $str_dk_brjs_ll,
            'str_dk_up_ll'          => $str_dk_up_ll,
            'str_dk_up_ini'         => $str_dk_up_ini,
            // pplain
            'str_pplain_gaji_ll'    => $str_pplain_gaji_ll,
            'str_pplain_gaji_ini'   => $str_pplain_gaji_ini,
            'str_pplain_brjs_ini'   => $str_pplain_brjs_ini,
            'str_pplain_brjs_ll'    => $str_pplain_brjs_ll,
            'str_pplain_up_ll'      => $str_pplain_up_ll,
            'str_pplain_up_ini'     => $str_pplain_up_ini,
            // hkpg
            'str_hkpg_gaji_ll'    => $str_hkpg_gaji_ll,
            'str_hkpg_gaji_ini'   => $str_hkpg_gaji_ini,
            'str_hkpg_brjs_ini'   => $str_hkpg_brjs_ini,
            'str_hkpg_brjs_ll'    => $str_hkpg_brjs_ll,
            'str_hkpg_up_ll'      => $str_hkpg_up_ll,
            'str_hkpg_up_ini'     => $str_hkpg_up_ini,
            // cp
            'str_cp_gaji_ll'    => $str_cp_gaji_ll,
            'str_cp_gaji_ini'   => $str_cp_gaji_ini,
            'str_cp_brjs_ini'   => $str_cp_brjs_ini,
            'str_cp_brjs_ll'    => $str_cp_brjs_ll,
            'str_cp_up_ll'      => $str_cp_up_ll,
            'str_cp_up_ini'     => $str_cp_up_ini,
            // pelimpahan
            'str_pelimpahan_up_ll'  => $str_pelimpahan_up_ll,
            'str_pelimpahan_up_ini' => $str_pelimpahan_up_ini,
            // panjar
            'str_panjar_up_ll'      => $str_panjar_up_ll,
            'str_panjar_up_ini'     => $str_panjar_up_ini,
            // bos
            // 'str_bos_gj_ll'     => $str_bos_gj_ll,
            // 'str_bos_gj_ini'    => $str_bos_gj_ini,
            // // blud
            'str_blud_ls_ll'    => $str_blud_ls_ll,
            'str_blud_ls_ini'   => $str_blud_ls_ini,
            // lain-lain
            'lain_gaji_ll'      => $lain_gaji_ll,
            'lain_gaji_ini'     => $lain_gaji_ini,
            'lain_brjs_ll'      => $lain_brjs_ll,
            'lain_brjs_ini'     => $lain_brjs_ini,
            'lain_up_ll'        => $lain_up_ll,
            'lain_up_ini'       => $lain_up_ini,

            // UYHD
            'saldo_uyhd'        => $saldo_uyhd,
            // setor UYHD
            'tahun_lalu_uyhd'           => $tahun_lalu_uyhd,
            'tahun_lalu_uyhd_ini'       => $tahun_lalu_uyhd_ini,
            'tahun_lalu_uyhd_pjk'       => $tahun_lalu_uyhd_pjk,
            'tahun_lalu_uyhd_pjk_ini'   => $tahun_lalu_uyhd_pjk_ini,

            // footer
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara,
            'terima_sp2d'       => $terima_sp2d
        ];
       //dd($data['spj_brjs_ini']);
        $view =  view('skpd.laporan_bendahara.cetak.spj_fungsional')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('SPJ ' . $judul . '.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="SPJ ' . $judul . ' - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }
}
