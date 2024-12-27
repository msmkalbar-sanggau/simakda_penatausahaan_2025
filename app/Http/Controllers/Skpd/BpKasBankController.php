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

class BpKasBankController extends Controller
{


    // Cetak List
    public function cetakBpkasBank(Request $request)
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
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        // $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $sawalskpd = DB::select("SELECT isnull(sld_awal_bank,0)+sld_awalpajak AS sal_lalu
        FROM ms_skpd where kd_skpd= ? ", [$kd_skpd]);
        foreach ($sawalskpd as $swl){
            $sawalskpd1 = $swl -> sal_lalu;
        }
        // saldo lalu
        $sawal = DB::select("SELECT terima-keluar as saldo_lalu FROM(select
                                SUM(case when jns=1 then jumlah else 0 end) AS terima,
                                SUM(case when jns=2 then jumlah else 0 end) AS keluar
                                from (

                                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_setorsimpanan
                                union
                                SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM trhINlain WHERE pay='BANK' union
                                select c.tgl_kas [tgl],c.no_kas [bku] ,c.keterangan [ket],c.nilai [jumlah],'1' [jns],c.kd_skpd [kode] from tr_jpanjar c join tr_panjar d on
                                c.no_panjar_lalu=d.no_panjar and c.kd_skpd=d.kd_skpd where c.jns='2' and c.kd_skpd= ?  and  d.pay='BANK' union all
                                select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],sum(b.nilai) [jumlah],'1' [jns],a.kd_skpd [kode] from trhtrmpot a
                                join trdtrmpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                                where a.kd_skpd= ?  and a.pay='BANK' and jns_spp not in('1','2','3') group by a.tgl_bukti,a.no_bukti,a.ket,a.kd_skpd
                                union all
                                select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
                                from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                                where jns_trans IN ('5') and bank='BNK' and a.kd_skpd= ?
                                GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd  union all
                                SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,total-isnull(pot,0)-isnull(f.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM trhtransout
                                a join trhsp2d b on a.no_sp2d=b.no_sp2d left join (select no_spm, sum(nilai)pot
                                from trspmpot group by no_spm) c on b.no_spm=c.no_spm
                                left join
                                (
                                select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
                                where e.kd_skpd= ?  and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
                                ) f on f.no_kas=a.no_bukti and f.kd_skpd=a.kd_skpd
                                WHERE pay='BANK' and
                                (panjar not in ('1','3') or panjar is null)

                                union
                                select d.tgl_bukti, d.no_bukti,d.ket [ket],sum(e.nilai) [jumlah],'1' [jns],d.kd_skpd [kode] from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
                                where e.kd_skpd= ?  and d.no_sp2d='2977/TU/2022' and d.pay='BANK' group by d.tgl_bukti,d.no_bukti,d.ket,d.kd_skpd
                                union
                                select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],sum(b.nilai) [jumlah],'2' [jns],a.kd_skpd [kode] from trhstrpot a
                                join trdstrpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                                where a.kd_skpd= ?  and a.pay='BANK' group by a.tgl_bukti,a.no_bukti,a.ket,a.kd_skpd
                                UNION
                                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_ambilsimpanan union
                                SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM trhoutlain WHERE pay='BANK' union
                                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'2' AS jns,kd_skpd_sumber AS kode FROM tr_setorpelimpahan_bank union

                                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_ambilsimpanan WHERE status_drop!='1' union

                                SELECT a.tgl_kas AS tgl,a.no_panjar AS bku,a.keterangan as ket,a.nilai-isnull(b.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM tr_panjar a
                                left join
                                (
                                select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
                                where e.kd_skpd= ?  and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
                                ) b on a.no_panjar=b.no_kas and a.kd_skpd=b.kd_skpd
                                where a.pay='BANK' and a.kd_skpd= ?
                                union all
                                select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
                                from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                                where jns_trans NOT IN ('4','2','5') and pot_khusus =0  and bank='BNK' and a.kd_skpd= ?
                                GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd union all
                                select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '1' as jns, a.kd_skpd as kode
                                from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                                where jns_trans IN ('5') and bank='BNK' and a.kd_skpd= ?
                                GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
                                ) a
                                where month(tgl)< ?  and kode= ? ) a", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $bulan, $kd_skpd]);
        foreach ($sawal as $sawal) {
            $saldo_awal             = $sawal->saldo_lalu;
        }

        // rincian
        $rincian = DB::select("SELECT * FROM (
                                    SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_setorsimpanan union
                                    SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM trhINlain WHERE pay='BANK' union
                                    select c.tgl_kas [tgl],c.no_kas [bku] ,c.keterangan [ket],c.nilai [jumlah],'1' [jns],c.kd_skpd [kode] from tr_jpanjar c join tr_panjar d on
                                    c.no_panjar_lalu=d.no_panjar  and c.kd_skpd=d.kd_skpd  where c.jns='2' and c.kd_skpd= ? and  d.pay='BANK' union all
                                    select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],sum(b.nilai) [jumlah],'1' [jns],a.kd_skpd [kode] from trhtrmpot a
                                    join trdtrmpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                                    where a.kd_skpd= ? and a.pay='BANK' and jns_spp not in('1','2','3') group by a.tgl_bukti,a.no_bukti,a.ket,a.kd_skpd
                                    union all
                                    select a.tgl_sts as tgl,a.no_sts as bku,'Terima'+ a.keterangan as ket, SUM(b.rupiah) as jumlah, '1' as jns, a.kd_skpd as kode
                                    from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                                    where jns_trans IN ('5') and bank='BNK' and a.kd_skpd= ?
                                    GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd  union all
                                    SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,total-isnull(pot,0)-isnull(f.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM trhtransout
                                    a join trhsp2d b on a.no_sp2d=b.no_sp2d left join (select no_spm, sum(nilai)pot
                                    from trspmpot group by no_spm) c on b.no_spm=c.no_spm
                                    left join
                                    (
                                    select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
                                    where e.kd_skpd= ? and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
                                    ) f on f.no_kas=a.no_bukti and f.kd_skpd=a.kd_skpd
                                    WHERE pay='BANK' and
                                    (panjar not in ('1','3') or panjar is null)
                                    union
                                    select d.tgl_bukti, d.no_bukti,d.ket [ket],sum(e.nilai) [jumlah],'1' [jns],d.kd_skpd [kode] from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
                                    where e.kd_skpd= ? and d.no_sp2d='2977/TU/2022' and d.pay='BANK' group by d.tgl_bukti,d.no_bukti,d.ket,d.kd_skpd
                                    union
                                    select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],sum(b.nilai) [jumlah],'2' [jns],a.kd_skpd [kode] from trhstrpot a
                                    join trdstrpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                                    where a.kd_skpd= ? and a.pay='BANK' group by a.tgl_bukti,a.no_bukti,a.ket,a.kd_skpd
                                    union
                                    select a.tgl_kas as tgl,a.no_kas as bku, a.keterangan as ket, SUM(nilai) as jumlah, '2' as jns, a.kd_skpd_sumber as kode
                                    from tr_setorpelimpahan_bank a where a.kd_skpd_sumber= ?
                                    GROUP BY a.tgl_kas,a.no_kas, a.keterangan,a.kd_skpd_sumber
                                    UNION
                                    SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_ambilsimpanan union
                                    SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM trhoutlain WHERE pay='BANK' union
                                    SELECT a.tgl_kas AS tgl,a.no_panjar AS bku,a.keterangan as ket,a.nilai-isnull(b.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM tr_panjar a
                                    left join
                                    (
                                    select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
                                    where e.kd_skpd= ? and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
                                    ) b on a.no_panjar=b.no_kas and a.kd_skpd=b.kd_skpd
                                    where a.pay='BANK' and a.kd_skpd= ?
                                    union all
                                    select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
                                    from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                                    where jns_trans NOT IN ('4','2','5') and pot_khusus =0  and bank='BNK' and a.kd_skpd= ?
                                    GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd union all
                                    select a.tgl_sts as tgl,a.no_sts+1 as bku, 'Setor '+a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
                                    from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                                    where jns_trans IN ('5') and bank='BNK' and a.kd_skpd= ?
                                    GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd

                                    ) a
                                    where month(a.tgl)= ? and kode= ? ORDER BY a.tgl,Cast(bku  as int), jns", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $bulan, $kd_skpd]);


        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'saldo_awal'        => $saldo_awal + $sawalskpd1,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view = view('skpd.laporan_bendahara.cetak.bp_bank')->with($data);

        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('BP KAS BANK.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="BP KAS BANK - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }
}
