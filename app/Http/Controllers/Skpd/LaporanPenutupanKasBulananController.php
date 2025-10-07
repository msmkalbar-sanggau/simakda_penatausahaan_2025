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

class LaporanPenutupanKasBulananController extends Controller
{


    // Cetak Buku Panjar
    public function cetakLaporanPenutupanKasBulanan(Request $request)
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
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where([
                'nip' => $bendahara,
                'kd_skpd' => $kd_skpd
            ])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();

        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))
            ->first();

        $saldobank  = DB::select("SELECT sum(terima) as terima , sum(keluar) as keluar from (
                        -- SP2 Terima
                        SELECT SUM(b.nilai) as terima,0 as keluar  FROM trhsp2d a INNER JOIN trdspp b ON a.no_spp = b.no_spp INNER JOIN trhspp c ON a.no_spp = c.no_spp WHERE a.kd_skpd = '$kd_skpd'
                        AND  MONTH(a.tgl_kas)< ? and a.status='1'
                        -- PAJAK dan Potongan TERIMA
                        UNION ALL
                        SELECT SUM(b.nilai), 0 FROM trhtrmpot a INNER JOIN trdtrmpot b ON a.no_bukti = b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd =  ?
                        AND b.kd_rek6 in ('210106010001','210105010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001')
                        AND MONTH(a.tgl_bukti)< ?

                        -- Lain-Lain terima
                        UNION ALL

                        SELECT
                        SUM(ISNULL(lainlain,0)) as terima, 0 as keluar
                        FROM(
                            SELECT
                            SUM(ISNULL(a.nilai,0)) AS lainlain
                            FROM trdtrmpot a INNER JOIN trhtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                            WHERE a.kd_skpd= ? and left(a.kd_rek6,6)<>'210601' AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','410411010001')
                            and b.jns_spp IN ('1','2','3','4','5','6') AND MONTH(b.tgl_bukti)< ?
                            UNION ALL
                            SELECT
                            SUM(a.nilai) AS lainlain
                            FROM TRHINLAIN a WHERE pengurang_belanja !='1'  and a.jns_beban in ('1','4','5','6')
                            and MONTH(a.tgl_bukti)< ?
                            AND a.kd_skpd= ?
                        ) a

                        -- PAJAK TUNAI TERIMA
                        UNION ALL
                        SELECT SUM(z.nilai_pot) as terima, 0 keluar
                        from (
                        select a.kd_skpd,c.tgl_bukti as tgl,b.kd_rek6 as rek,b.nilai as nilai_pot,b.no_sp2d from trdtransout a
                        LEFT JOIN trhtransout c on c.kd_skpd=a.kd_skpd and a.no_bukti=c.no_bukti
                        LEFT JOIN (
                        select b.tgl_bukti,a.kd_rek6,a.nilai,b.no_sp2d from trdstrpot a left join trhstrpot b on b.kd_skpd=a.kd_skpd and b.no_bukti=a.no_bukti
                        )b on b.no_sp2d=a.no_sp2d
                        where right(a.kd_skpd,2)!='00' and c.jns_spp in ('4','5','6') and c.pay='TUNAI'
                        )z
                        where z.no_sp2d <> '' and kd_skpd= ? and MONTH(z.tgl)< ?

                        --DROPPING TERIMA
                        UNION ALL
                        SELECT sum(x.sd_bln_ini) terima, 0 keluar from(
                        select SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                        from tr_setorpelimpahan WHERE kd_skpd= ?
                        UNION ALL
                        select SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                        from tr_setorpelimpahan_bank WHERE kd_skpd= ?
                        UNION ALL
                        -- select SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                        -- from tr_setorpelimpahan_tunai WHERE kd_skpd= ?
                        -- UNION ALL
                        SELECT SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as jar_sd_bln_ini
                        from tr_setorsimpanan WHERE kd_skpd= ? and jenis='3'
                        )x

                        -- PANJAR TERIMA
                        UNION ALL
                        SELECT SUM(x.jar_sd_bln_ini) terima, 0 as keluar FROM(
                                    SELECT SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as jar_sd_bln_ini
                                    from  tr_jpanjar where jns=1 and kd_skpd= ?
                                    )x
                        -- BLUD
                        -- UNION ALL
                        -- SELECT
                        --     SUM(CASE WHEN MONTH(tgl_kas)< ? THEN b.nilai ELSE 0 END) as terima, 0 as keluar
                        --     from trhtransout_blud a inner join trdtransout_blud b on a.kd_skpd=b.kd_skpd and a.no_bukti=b.no_bukti
                        --     where a.kd_skpd= ? and left(b.kd_rek6,1)='5' and b.sumber='BLUD'

                        -- Denda keterlambatan
                        UNION ALL
                        SELECT SUM(b.nilai) as terima, 0 as keluar FROM trhtrmpot a INNER JOIN trdtrmpot b
                            ON a.no_bukti = b.no_bukti AND a.kd_skpd=b.kd_skpd
                            WHERE a.kd_skpd =  ? AND
                            b.kd_rek6 in ('410411010001') AND MONTH(a.tgl_bukti)< ? AND a.jns_spp IN('1','2','3','4','5','6')



                        UNION ALL



                        SELECT 0 as terima , sum(gaji_ini)+ sum(brg_ini)+sum(up_ini)as keluar from

                        (
                            select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ?
                            and jns_spp in (1,2,3) and pay not in ('PANJAR')
                        union all

                        -- select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout_blud a join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and left(kd_rek6,1)='5'
                        --     and jns_spp in (1,2,3)
                        -- union all

                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai*-1,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdinlain a join TRHINLAIN b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.TGL_BUKTI)< ? and b.pengurang_belanja=1
                        union all

                        select a.kd_skpd, isnull(a.nilai,0) as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and jns_spp in (4)
                        union all

                        select a.kd_skpd, isnull(a.rupiah*-1,0) as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (1) and b.pot_khusus=1
                        union all

                        select a.kd_skpd, 0 as gaji_ini, isnull(a.nilai,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and jns_spp in ('5','6')
                        union all

                        select a.kd_skpd, 0 as gaji_ini, isnull(a.rupiah*-1,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (2) and b.pot_khusus=0
                        union all

                        select a.kd_skpd, 0 as gaji_ini, isnull(a.rupiah*-1,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (2) and b.pot_khusus=2
                        union all

                        select a.kd_skpd, 0 as gaji_ini, isnull(a.rupiah,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (2) and b.pot_khusus=2 and kd_rek6='410411010001'
                        union all


                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, isnull(a.nilai,0) as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and jns_spp in (1,2,3) and pay not in ('PANJAR')
                        union all

                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, isnull(a.nilai*-1,0) as up_lalu from trdinlain a join TRHINLAIN b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.TGL_BUKTI)< ? and b.pengurang_belanja=1
                        union all

                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, isnull(a.nilai,0) as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and jns_spp in (4)
                        union all

                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, isnull(a.rupiah*-1,0) as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (1) and b.pot_khusus=1
                        union all

                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.nilai,0) as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and jns_spp in ('5','6')
                        union all

                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.rupiah*-1,0) as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (2) and b.pot_khusus=2

                        union all
                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.rupiah,0) as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (2) and b.pot_khusus=2 and kd_rek6='410411010001'

                        union all

                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.rupiah*-1,0) as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (2) and b.pot_khusus=0

                        ) a
                        WHERE a.kd_skpd= ?

                        UNION ALL

                        --  PAJAK KELUAR
                        SELECT 0 as masuk, SUM(b.nilai)as keluar FROM trhstrpot a INNER JOIN trdstrpot b
                        ON a.no_bukti = b.no_bukti AND a.kd_skpd=b.kd_skpd
                        WHERE a.kd_skpd =  ? AND
                        b.kd_rek6 in ('210106010001','210105010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','') AND MONTH(a.tgl_bukti)< ? AND
                        a.jns_spp IN('1','2','3','4','5','6')

                        -- denda keterlambatan
                        UNION ALL
                        SELECT 0 as masuk, SUM(isnull((case when MONTH(tgl_bukti)< ? then b.nilai else 0 end),0)) AS keluar
                                    FROM trhstrpot a
                                    INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                    WHERE a.kd_skpd = ? AND b.kd_rek6='410411010001'

                        -- pot penghasilan lainnya
                        UNION ALL
                                SELECT 0 as masuk,
                            SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '3' and MONTH(tgl_sts)< ?  then a.rupiah else 0 end),0)) +
                            SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '2' and MONTH(tgl_sts)< ?  then a.rupiah else 0 end),0)) +
                            SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '1' and MONTH(tgl_sts)< ?  then a.rupiah else 0 end),0)) AS keluar
                            FROM trdkasin_pkd a
                            INNER JOIN trhkasin_pkd b on a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                            WHERE a.kd_skpd = ? AND jns_trans='5'

                        --  HKPG
                        UNION ALL
                        SELECT
                            0 as masuk,
                            SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '3' and MONTH(tgl_sts)< ? then a.rupiah else 0 end),0)) +
                            SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '2' and MONTH(tgl_sts)< ? then a.rupiah else 0 end),0)) +
                            SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '1' and MONTH(tgl_sts)< ? then a.rupiah else 0 end),0)) AS keluar
                            FROM trdkasin_pkd a
                            INNER JOIN trhkasin_pkd b on a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                            WHERE a.kd_skpd =  ? AND jns_trans='5' AND LEFT(kd_rek6,1)<>4

                        -- CP
                        UNION ALL
                        SELECT  0 as masuk,
                                SUM(isnull((case when rtrim(jns_cp)= '3' and MONTH(tgl_sts)< ? then z.nilai else 0 end),0)) +
                                SUM(isnull((case when rtrim(jns_cp)= '1' and MONTH(tgl_sts)< ? then z.nilai else 0 end),0)) +
                                SUM(isnull((case when rtrim(jns_cp)= '2' and MONTH(tgl_sts)< ? then z.nilai else 0 end),0)) AS keluar
                                from (select rupiah as nilai,jns_trans,pot_khusus,jns_cp,d.tgl_sts ,d.kd_skpd from
                                trdkasin_pkd c INNER JOIN trhkasin_pkd d ON c.no_sts = d.no_sts AND c.kd_skpd = d.kd_skpd where d.kd_skpd = ? AND
                                ((jns_trans='5' AND pot_khusus='0') OR jns_trans='1')) z

                        -- lain2 setoran
                        UNION ALL
                        SELECT
                                0 as masuk,
                                SUM(ISNULL(jlain_up_ini,0))+
                                SUM(ISNULL(jlain_gaji_ini,0)) +
                                SUM(ISNULL(jlain_brjs_ini,0)) as keluar
                                    FROM(
                                SELECT
                                SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND MONTH(b.tgl_bukti)< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','410411010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ini,
                                SUM(CASE WHEN b.jns_spp IN ('4') AND MONTH(b.tgl_bukti)< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','410411010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ini,
                                SUM(CASE WHEN b.jns_spp IN ('5','6') AND MONTH(b.tgl_bukti)< ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','410411010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ini
                                FROM trdstrpot a INNER JOIN trhstrpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                WHERE a.kd_skpd= ? and left(a.kd_rek6,6)<>'210601'

                                UNION ALL
                                SELECT
                                SUM(CASE WHEN a.jns_beban='1' AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS jlain_up_ini,
                                SUM(CASE WHEN a.jns_beban='4' AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ini,
                                SUM(CASE WHEN a.jns_beban in ('5','6') AND MONTH(a.tgl_bukti)< ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ini
                                FROM TRHOUTLAIN a
                                WHERE a.kd_skpd= ? and thnlalu=0
                                ) a

                        -- DROPPING KELUAR
                        UNION ALL
                        SELECT 0 as masuk, SUM(z.sd_bln_ini) as keluar from(
                                select
                                SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                                from tr_setorpelimpahan_bank
                                WHERE kd_skpd_sumber= ?
                                UNION ALL
                                -- select
                                -- SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                                -- from tr_setorpelimpahan_tunai
                                -- WHERE kd_skpd_sumber= ?
                                -- UNION ALL
                                select
                                SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                                from tr_setorpelimpahan
                                WHERE kd_skpd_sumber= ?
                                )z
                        -- PANJAR KELUAR
                        UNION ALL
                        SELECT
                                0 as masuk,
                                SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as keluar
                                from tr_panjar
                                WHERE kd_skpd= ? and jns='1'

                        -- BLUD
                        -- UNION ALL
                        -- SELECT
                        --         0 as masuk,
                        --         SUM(CASE WHEN MONTH(tgl_bukti)< ? THEN a.nilai ELSE 0 END) as keluar
                        --         from trdtransout_blud a inner join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                        --         WHERE a.kd_skpd= ? and right(kd_rek6,6)='999999'



                        )zzz", [$bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd, $kd_skpd, $bulan, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd, $kd_skpd, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd]);
        foreach ($saldobank as $sawalbank) {
            $saldoawalbank   = $sawalbank->terima - $sawalbank->keluar;
        }


        $terimakeluarbank = DB::select("SELECT sum(terima) as terima , sum(keluar) as keluar from (
                -- SP2 Terima
                SELECT SUM(b.nilai) as terima,0 as keluar  FROM trhsp2d a INNER JOIN trdspp b ON a.no_spp = b.no_spp INNER JOIN trhspp c ON a.no_spp = c.no_spp WHERE a.kd_skpd =  ?
                AND  MONTH(a.tgl_kas)= ? and a.status='1'
                -- PAJAK dan Potongan TERIMA
                UNION ALL
                SELECT SUM(b.nilai), 0 FROM trhtrmpot a INNER JOIN trdtrmpot b ON a.no_bukti = b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd =  ?
                AND b.kd_rek6 in ('210106010001','210105010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001')
                AND MONTH(a.tgl_bukti)= ?

                -- Lain-Lain terima
                UNION ALL

                SELECT
                SUM(ISNULL(lainlain,0)) as terima, 0 as keluar
                FROM(
                    SELECT
                    SUM(ISNULL(a.nilai,0)) AS lainlain
                    FROM trdtrmpot a INNER JOIN trhtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                    WHERE a.kd_skpd= ? and left(a.kd_rek6,6)<>'210601' AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','410411010001')
                    and b.jns_spp IN ('1','2','3','4','5','6') AND MONTH(b.tgl_bukti)= ?
                    UNION ALL
                    SELECT
                    SUM(a.nilai) AS lainlain
                    FROM TRHINLAIN a WHERE pengurang_belanja !='1'  and a.jns_beban in ('1','4','5','6')
                    and MONTH(a.tgl_bukti)= ?
                    AND a.kd_skpd= ?
                ) a

                -- PAJAK TUNAI TERIMA
                UNION ALL
                SELECT SUM(z.nilai_pot) as terima, 0 keluar
                from (
                select a.kd_skpd,c.tgl_bukti as tgl,b.kd_rek6 as rek,b.nilai as nilai_pot,b.no_sp2d from trdtransout a
                LEFT JOIN trhtransout c on c.kd_skpd=a.kd_skpd and a.no_bukti=c.no_bukti
                LEFT JOIN (
                select b.tgl_bukti,a.kd_rek6,a.nilai,b.no_sp2d from trdstrpot a left join trhstrpot b on b.kd_skpd=a.kd_skpd and b.no_bukti=a.no_bukti
                )b on b.no_sp2d=a.no_sp2d
                where right(a.kd_skpd,2)!='00' and c.jns_spp in ('4','5','6') and c.pay='TUNAI'
                )z
                where z.no_sp2d <> '' and kd_skpd= ? and MONTH(z.tgl)= ?

                --DROPPING TERIMA
                UNION ALL
                SELECT sum(x.sd_bln_ini) terima, 0 keluar from(
                select SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                from tr_setorpelimpahan WHERE kd_skpd= ?
                UNION ALL
                select SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                from tr_setorpelimpahan_bank WHERE kd_skpd= ?
                UNION ALL
                -- select SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                -- from tr_setorpelimpahan_tunai WHERE kd_skpd= ?
                -- UNION ALL
                SELECT SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as jar_sd_bln_ini
                from tr_setorsimpanan WHERE kd_skpd= ? and jenis='3'
                )x

                -- PANJAR TERIMA
                UNION ALL
                SELECT SUM(x.jar_sd_bln_ini) terima, 0 as keluar FROM(
                            SELECT SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as jar_sd_bln_ini
                            from  tr_jpanjar where jns=1 and kd_skpd= ?
                            )x
                -- BLUD
                -- UNION ALL
                -- SELECT
                --     SUM(CASE WHEN MONTH(tgl_kas)= ? THEN b.nilai ELSE 0 END) as terima, 0 as keluar
                --     from trhtransout_blud a inner join trdtransout_blud b on a.kd_skpd=b.kd_skpd and a.no_bukti=b.no_bukti
                --     where a.kd_skpd= ? and left(b.kd_rek6,1)='5' and b.sumber='BLUD'

                -- Denda keterlambatan
                UNION ALL
                SELECT SUM(b.nilai) as terima, 0 as keluar FROM trhtrmpot a INNER JOIN trdtrmpot b
                    ON a.no_bukti = b.no_bukti AND a.kd_skpd=b.kd_skpd
                    WHERE a.kd_skpd =  ? AND
                    b.kd_rek6 in ('410411010001') AND MONTH(a.tgl_bukti)= ? AND a.jns_spp IN('1','2','3','4','5','6')



                UNION ALL



                SELECT 0 as terima , sum(gaji_ini)+ sum(brg_ini)+sum(up_ini)as keluar from

                (
                    select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)= ?
                    and jns_spp in (1,2,3) and pay not in ('PANJAR')
                union all

                -- select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout_blud a join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)= ? and left(kd_rek6,1)='5'
                --     and jns_spp in (1,2,3)
                -- union all

                select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai*-1,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdinlain a join TRHINLAIN b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.TGL_BUKTI)= ? and b.pengurang_belanja=1
                union all

                select a.kd_skpd, isnull(a.nilai,0) as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)= ? and jns_spp in (4)
                union all

                select a.kd_skpd, isnull(a.rupiah*-1,0) as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)= ? and b.jns_cp in (1) and b.pot_khusus=1
                union all

                select a.kd_skpd, 0 as gaji_ini, isnull(a.nilai,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)= ? and jns_spp in ('5','6')
                union all

                select a.kd_skpd, 0 as gaji_ini, isnull(a.rupiah*-1,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)= ? and b.jns_cp in (2) and b.pot_khusus=0
                union all

                select a.kd_skpd, 0 as gaji_ini, isnull(a.rupiah*-1,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)= ? and b.jns_cp in (2) and b.pot_khusus=2
                union all

                select a.kd_skpd, 0 as gaji_ini, isnull(a.rupiah,0) as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)= ? and b.jns_cp in (2) and b.pot_khusus=2 and kd_rek6='410411010001'
                union all


                select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, isnull(a.nilai,0) as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and jns_spp in (1,2,3) and pay not in ('PANJAR')
                union all

                select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, 0 as brg_lalu, isnull(a.nilai*-1,0) as up_lalu from trdinlain a join TRHINLAIN b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.TGL_BUKTI)< ? and b.pengurang_belanja=1
                union all

                select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, isnull(a.nilai,0) as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and jns_spp in (4)
                union all

                select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, isnull(a.rupiah*-1,0) as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (1) and b.pot_khusus=1
                union all

                select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.nilai,0) as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and jns_spp in ('5','6')
                union all

                select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.rupiah*-1,0) as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (2) and b.pot_khusus=2

                union all
                select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.rupiah,0) as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (2) and b.pot_khusus=2 and kd_rek6='410411010001'

                union all

                select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, 0 as up_ini, 0 as gaji_lalu, isnull(a.rupiah*-1,0) as brg_lalu, 0 as up_lalu from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_sts)< ? and b.jns_cp in (2) and b.pot_khusus=0

                ) a
                WHERE a.kd_skpd= ?

                UNION ALL

                --  PAJAK KELUAR
                SELECT 0 as masuk, SUM(b.nilai)as keluar FROM trhstrpot a INNER JOIN trdstrpot b
                ON a.no_bukti = b.no_bukti AND a.kd_skpd=b.kd_skpd
                WHERE a.kd_skpd =  ? AND
                b.kd_rek6 in ('210106010001','210105010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','') AND MONTH(a.tgl_bukti)= ? AND
                a.jns_spp IN('1','2','3','4','5','6')

                -- denda keterlambatan
                UNION ALL
                SELECT 0 as masuk, SUM(isnull((case when MONTH(tgl_bukti)= ? then b.nilai else 0 end),0)) AS keluar
                            FROM trhstrpot a
                            INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                            WHERE a.kd_skpd = ? AND b.kd_rek6='410411010001'

                -- pot penghasilan lainnya
                UNION ALL
                        SELECT 0 as masuk,
                    SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '3' and MONTH(tgl_sts)= ?  then a.rupiah else 0 end),0)) +
                    SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '2' and MONTH(tgl_sts)= ?  then a.rupiah else 0 end),0)) +
                    SUM(isnull((case when pot_khusus='2' AND rtrim(jns_cp)= '1' and MONTH(tgl_sts)= ?  then a.rupiah else 0 end),0)) AS keluar
                    FROM trdkasin_pkd a
                    INNER JOIN trhkasin_pkd b on a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                    WHERE a.kd_skpd = ? AND jns_trans='5'

                --  HKPG
                UNION ALL
                SELECT
                    0 as masuk,
                    SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '3' and MONTH(tgl_sts)= ? then a.rupiah else 0 end),0)) +
                    SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '2' and MONTH(tgl_sts)= ? then a.rupiah else 0 end),0)) +
                    SUM(isnull((case when pot_khusus='1' AND rtrim(jns_cp)= '1' and MONTH(tgl_sts)= ? then a.rupiah else 0 end),0)) AS keluar
                    FROM trdkasin_pkd a
                    INNER JOIN trhkasin_pkd b on a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                    WHERE a.kd_skpd =  ? AND jns_trans='5' AND LEFT(kd_rek6,1)<>4

                -- CP
                UNION ALL
                SELECT  0 as masuk,
                        SUM(isnull((case when rtrim(jns_cp)= '3' and MONTH(tgl_sts)= ? then z.nilai else 0 end),0)) +
                        SUM(isnull((case when rtrim(jns_cp)= '1' and MONTH(tgl_sts)= ? then z.nilai else 0 end),0)) +
                        SUM(isnull((case when rtrim(jns_cp)= '2' and MONTH(tgl_sts)= ? then z.nilai else 0 end),0)) AS keluar
                        from (select rupiah as nilai,jns_trans,pot_khusus,jns_cp,d.tgl_sts ,d.kd_skpd from
                        trdkasin_pkd c INNER JOIN trhkasin_pkd d ON c.no_sts = d.no_sts AND c.kd_skpd = d.kd_skpd where d.kd_skpd = ? AND
                        ((jns_trans='5' AND pot_khusus='0') OR jns_trans='1')) z

                -- lain2 setoran
                UNION ALL
                SELECT
                        0 as masuk,
                        SUM(ISNULL(jlain_up_ini,0))+
                        SUM(ISNULL(jlain_gaji_ini,0)) +
                        SUM(ISNULL(jlain_brjs_ini,0)) as keluar
                            FROM(
                        SELECT
                        SUM(CASE WHEN b.jns_spp IN ('1','2','3') AND MONTH(b.tgl_bukti)= ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','410411010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_up_ini,
                        SUM(CASE WHEN b.jns_spp IN ('4') AND MONTH(b.tgl_bukti)= ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','410411010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_gaji_ini,
                        SUM(CASE WHEN b.jns_spp IN ('5','6') AND MONTH(b.tgl_bukti)= ? AND a.kd_rek6 NOT IN ('210105010001','210106010001','210105020001','210105030001','210108010001','210107010001','210109010001','210102010001','410411010001') THEN  ISNULL(a.nilai,0) ELSE 0 END) AS jlain_brjs_ini
                        FROM trdstrpot a INNER JOIN trhstrpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                        WHERE a.kd_skpd= ? and left(a.kd_rek6,6)<>'210601'

                        UNION ALL
                        SELECT
                        SUM(CASE WHEN a.jns_beban='1' AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS jlain_up_ini,
                        SUM(CASE WHEN a.jns_beban='4' AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS jlain_gaji_ini,
                        SUM(CASE WHEN a.jns_beban in ('5','6') AND MONTH(a.tgl_bukti)= ? THEN  a.nilai ELSE 0 END) AS jlain_brjs_ini
                        FROM TRHOUTLAIN a
                        WHERE a.kd_skpd= ? and thnlalu=0
                        ) a

                -- DROPPING KELUAR
                UNION ALL
                SELECT 0 as masuk, SUM(z.sd_bln_ini) as keluar from(
                        select
                        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                        from tr_setorpelimpahan_bank
                        WHERE kd_skpd_sumber= ?
                        UNION ALL
                        -- select
                        -- SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                        -- from tr_setorpelimpahan_tunai
                        -- WHERE kd_skpd_sumber= ?
                        -- UNION ALL
                        select
                        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                        from tr_setorpelimpahan
                        WHERE kd_skpd_sumber= ?
                        )z
                -- PANJAR KELUAR
                UNION ALL
                SELECT
                        0 as masuk,
                        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as keluar
                        from tr_panjar
                        WHERE kd_skpd= ? and jns='1'

                -- BLUD
                -- UNION ALL
                -- SELECT
                --         0 as masuk,
                --         SUM(CASE WHEN MONTH(tgl_bukti)= ? THEN a.nilai ELSE 0 END) as keluar
                --         from trdtransout_blud a inner join trhtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                --         WHERE a.kd_skpd= ? and right(kd_rek6,6)='999999'
                        )zzz", [$kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd, $kd_skpd, $bulan, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd,  $kd_skpd, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $bulan, $kd_skpd, $kd_skpd, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd]);


        foreach ($terimakeluarbank as $trmklrbank) {
            $terimabank     = $trmklrbank->terima;
            $keluarbank     = $trmklrbank->keluar;
        }

        // saldo tunai lalu

        $tahun_lalu = DB::table('ms_skpd')
            ->select(DB::raw('isnull(sld_awal,0) AS nilai'), 'sld_awalpajak')
            ->where('kd_skpd', $kd_skpd)
            ->first();

        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');

        $tunai_lalu = collect(DB::select("exec kas_tunai_lalu ?,?", array($kd_skpd, $bulan)))->first();

        $xhasil_lalu = ($tunai_lalu->terima - $tunai_lalu->keluar) + $tahun_lalu->sld_awalpajak;

        $tunai      = collect(DB::select("exec kas_tunai ?,?", array($kd_skpd, $bulan)))->first();

        $xhasil_tunai = ($tunai->terima - $tunai->keluar) + $xhasil_lalu;

        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();

        $saldo_berharga = collect(DB::select("select sum(nilai) as total from trhsp2d where month(tgl_terima)=? and kd_skpd=?
                    and status_terima = '1' and month(tgl_kas) > ?", [$bulan, $kd_skpd, $bulan]))->first();

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'saldoawalbank'     => $saldoawalbank,
            'nm_skpd'           => $nm_skpd,
            'terimabank'        => $terimabank,
            'keluarbank'        => $keluarbank,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara,
            'xhasil_tunai'      => $xhasil_tunai,
            'saldoberharga'     => $saldo_berharga->total,

        ];

        $view =  view('skpd.laporan_bendahara.cetak.laporan_penutupan_kas_bulanan')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('LAORAN PENUTUPAN KAS BULANAN.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="LAORAN PENUTUPAN KAS BULANAN - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    public function cetakLaporanPenutupanKasBulananSIPD(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where([
                'nip' => $bendahara,
                'kd_skpd' => $kd_skpd
            ])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();

        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))
            ->first();

        $saldobank  = DB::select("SELECT sum(terima) as terima , sum(keluar) as keluar from (
                        /* SP2D Terima */
                        SELECT SUM(b.nilai) as terima,0 as keluar  FROM trhsp2d a INNER JOIN trdspp b ON a.no_spp = b.no_spp 
                        INNER JOIN trhspp c ON a.no_spp = c.no_spp WHERE a.kd_skpd = ?
                        AND  MONTH(a.tgl_kas)< ? and a.status='1' and a.jns_spp in ('1','2')

                        UNION ALL
                        SELECT SUM(a.nilai) as terima,SUM(a.nilai) as terima  FROM tr_ambilsimpanan a WHERE a.kd_skpd =  ?
                        AND  MONTH(a.tgl_kas)< ?

                        /* PANJAR TERIMA */
                        UNION ALL
                        select sum(nilai) as masuk, 0 as keluar 
                        from tr_jpanjar where kd_skpd=? and MONTH(tgl_kas)<? and jns='1'

                        /* DROPPING TERIMA */
                        UNION ALL
                        SELECT sum(x.sd_bln_ini) terima, 0 keluar from(
                        select SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                        from tr_setorpelimpahan WHERE kd_skpd= ?
                        UNION ALL
                        select SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                        from tr_setorpelimpahan_bank WHERE kd_skpd= ?
                        UNION ALL
                        SELECT SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as jar_sd_bln_ini
                        from tr_setorsimpanan WHERE kd_skpd= ? and jenis='3'
                        )x

                        UNION ALL
                        SELECT 0 as terima , SUM(a.gaji_ini + a.brg_ini + a.up_ini) as keluar from

                        (
                        select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)< ? and jns_spp in (1,2,3) and pay not in ('PANJAR')

                        ) a
                        WHERE a.kd_skpd= ?

                        /* DROPPING KELUAR */
                        UNION ALL
                        SELECT 0 as masuk, SUM(z.sd_bln_ini) as keluar from(
                                select
                                SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                                from tr_setorpelimpahan_bank
                                WHERE kd_skpd_sumber= ?
                                UNION ALL
                                select
                                SUM(CASE WHEN MONTH(tgl_kas)< ? THEN nilai ELSE 0 END) as sd_bln_ini
                                from tr_setorpelimpahan
                                WHERE kd_skpd_sumber= ?
                                )z
                        /* PANJAR KELUAR */  
                        UNION ALL                        
                        select 0 as masuk, sum(nilai) as keluar 
                        from tr_panjar where kd_skpd=? and jns='1' and MONTH(tgl_kas)<?
                

                        )zzz", [$kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan]);
        foreach ($saldobank as $sawalbank) {
            $saldoawalbank   = $sawalbank->terima - $sawalbank->keluar;
        }


        $terimakeluarbank = DB::select("SELECT sum(terima) as terima , sum(keluar) as keluar from (
                /* SP2 Terima */
                SELECT SUM(b.nilai) as terima,0 as keluar  FROM trhsp2d a 
                INNER JOIN trdspp b ON a.no_spp = b.no_spp 
                INNER JOIN trhspp c ON a.no_spp = c.no_spp 
                WHERE a.kd_skpd =  ? AND  MONTH(a.tgl_kas)= ? and a.status='1' and a.jns_spp in ('1','2')

                UNION ALL

                SELECT SUM(a.nilai) as terima,SUM(a.nilai) as terima  FROM tr_ambilsimpanan a WHERE a.kd_skpd =  ?
                AND  MONTH(a.tgl_kas)= ?

                union all

                /** Pengembalian Panjar */
                select sum(nilai) as masuk,sum(nilai)  as keluar 
                from tr_jpanjar where kd_skpd=? and MONTH(tgl_kas)=?  and jns='2'

                /* PANJAR TERIMA */
                UNION ALL
                select 0 as masuk, sum(nilai)*-1 as keluar 
                from tr_jpanjar where kd_skpd=? and MONTH(tgl_kas)=?  and jns='1'

                /* DROPPING TERIMA */
                UNION ALL
                SELECT sum(x.sd_bln_ini) terima, 0 keluar from(
                select SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                from tr_setorpelimpahan WHERE kd_skpd= ?
                UNION ALL
                select SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                from tr_setorpelimpahan_bank WHERE kd_skpd= ?
                UNION ALL
                SELECT SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as jar_sd_bln_ini
                from tr_setorsimpanan WHERE kd_skpd= ? and jenis='3'
                )x

                UNION ALL

                SELECT 0 as terima , sum(gaji_ini)+ sum(brg_ini)+sum(up_ini)as keluar from

                (
                    select a.kd_skpd, 0 as gaji_ini, 0 as brg_ini, isnull(a.nilai,0) as up_ini, 0 as gaji_lalu, 0 as brg_lalu, 0 as up_lalu 
                    from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where MONTH(b.tgl_bukti)= ?
                    and jns_spp in (1,2,3) and pay not in ('PANJAR')

                ) a
                WHERE a.kd_skpd= ?


                /* DROPPING KELUAR */
                UNION ALL
                SELECT 0 as masuk, SUM(z.sd_bln_ini) as keluar from(
                        select
                        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                        from tr_setorpelimpahan_bank
                        WHERE kd_skpd_sumber= ?
                        UNION ALL
                        select
                        SUM(CASE WHEN MONTH(tgl_kas)= ? THEN nilai ELSE 0 END) as sd_bln_ini
                        from tr_setorpelimpahan
                        WHERE kd_skpd_sumber= ?
                        )z

                union all

                select 0 as terima , sum(nilai) as keluar from tr_panjar a where MONTH(tgl_panjar)= ? and kd_skpd = ?  and jns='1'

                union all 

                SELECT /*SUM(b.nilai)*/ 0 as terima,/*SUM(b.nilai)*/ 0 as keluar  FROM trhsp2d a INNER JOIN trdspp b ON a.no_spp = b.no_spp INNER JOIN trhspp c ON a.no_spp = c.no_spp WHERE a.kd_skpd =  ?
                AND  MONTH(a.tgl_kas)= ? and a.status='1' and a.jns_spp in ('1','2') and b.nm_rek6='Belanja Dana Operasional KDH/WKDH'

                        )zzz", [$kd_skpd, $bulan,  $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan]);



        foreach ($terimakeluarbank as $trmklrbank) {
            $terimabank     = $trmklrbank->terima;
            $keluarbank     = $trmklrbank->keluar;
        }

        // saldo tunai lalu

        $tahun_lalu = DB::table('ms_skpd')
            ->select(DB::raw('isnull(sld_awal,0) AS nilai'), 'sld_awalpajak')
            ->where('kd_skpd', $kd_skpd)
            ->first();

        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');

        $tunai_lalu = collect(DB::select("exec kas_tunai_lalu ?,?", array($kd_skpd, $bulan)))->first();

        $xhasil_lalu = ($tunai_lalu->terima - $tunai_lalu->keluar) + $tahun_lalu->sld_awalpajak;

        $tunai      = collect(DB::select("exec kas_tunai ?,?", array($kd_skpd, $bulan)))->first();

        $xhasil_tunai = ($tunai->terima - $tunai->keluar) + $xhasil_lalu;

        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();

        $saldo_berharga = collect(DB::select("select sum(nilai) as total from trhsp2d where month(tgl_terima)=? and kd_skpd=?
                    and status_terima = '1' and month(tgl_kas) > ?", [$bulan, $kd_skpd, $bulan]))->first();

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'saldoawalbank'     => $saldoawalbank,
            'nm_skpd'           => $nm_skpd,
            'terimabank'        => $terimabank,
            'keluarbank'        => $keluarbank,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara,
            'xhasil_tunai'      => $xhasil_tunai,
            'saldoberharga'     => $saldo_berharga->total,

        ];

        $view =  view('skpd.laporan_bendahara.cetak.laporan_penutupan_kas_bulanan_sipd')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('LAORAN PENUTUPAN KAS BULANAN.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="LAORAN PENUTUPAN KAS BULANAN - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }
}
