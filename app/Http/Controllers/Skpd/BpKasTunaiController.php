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

class BpKasTunaiController extends Controller
{


    // Cetak List
    public function cetakBpkasTunai(Request $request)
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
        // SALDO AWAL
        $saldoawal = DB::table('ms_skpd')->select('sld_awal_tunai', 'sld_awalpajak')->where(['kd_skpd' => $kd_skpd])->first();
        // saldo lalu
        $saldo_lalu = DB::select("SELECT
                                SUM(case when jns=1 then jumlah else 0 end ) as terima, SUM(case when jns=2 then jumlah else 0 end) AS keluar
                                FROM (
                                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_ambilsimpanan
                                UNION ALL
                                select f.tgl_kas as tgl,f.no_kas as bku,f.keterangan as ket, f.nilai as jumlah, '1' as jns,f.kd_skpd as kode from tr_jpanjar f join tr_panjar g
                                on f.no_panjar_lalu=g.no_panjar and f.kd_skpd=g.kd_skpd where f.jns=2 and g.pay='TUNAI' UNION ALL
                                select tgl_bukti [tgl],no_bukti [bku],ket [ket],nilai [jumlah],'1' [jns],kd_skpd [kode] from trhtrmpot a
                                where kd_skpd= ? and pay='' and jns_spp in ('1','2','3') union all
                                select tgl_panjar as tgl,no_panjar as bku,keterangan as ket, nilai as jumlah, '2' as jns,kd_skpd as kode from tr_panjar where pay='TUNAI'  UNION ALL
                                select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
                                from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                                where jns_trans NOT IN ('4','2') and pot_khusus =0  and bank='TNK' and jns_cp not in ('2')
                                GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
                                UNION ALL
                                SELECT	a.tgl_bukti AS tgl,	a.no_bukti AS bku, a.ket AS ket, SUM(z.nilai) - isnull(pot, 0) AS jumlah, '2' AS jns, a.kd_skpd AS kode
                                FROM trhtransout a INNER JOIN trdtransout z ON a.no_bukti=z.no_bukti AND a.kd_skpd=z.kd_skpd
                                LEFT JOIN trhsp2d b ON z.no_sp2d = b.no_sp2d
                                LEFT JOIN (SELECT no_spm, SUM (nilai) pot	FROM trspmpot GROUP BY no_spm) c
                                ON b.no_spm = c.no_spm WHERE pay = 'TUNAI' AND panjar NOT IN('1','3')
                                AND MONTH(a.tgl_bukti)< ? and a.kd_skpd= ?
                                AND a.no_bukti NOT IN(
                                select no_bukti from trhtransout
                                where no_sp2d in
                                (SELECT no_sp2d as no_bukti FROM trhtransout where kd_skpd= ? GROUP BY no_sp2d HAVING COUNT(no_sp2d)>1)
                                AND MONTH(tgl_bukti)< ? and  no_kas not in
                                (SELECT min(z.no_kas) as no_bukti FROM trhtransout z WHERE z.jns_spp in (4,5,6) and kd_skpd= ?
                                AND MONTH(tgl_bukti)< ?
                                GROUP BY z.no_sp2d HAVING COUNT(z.no_sp2d)>1)
                                and jns_spp in (4,5,6) and kd_skpd= ?)
                                GROUP BY a.tgl_bukti,a.no_bukti,a.ket,a.no_sp2d,z.no_sp2d,a.total,pot,a.kd_skpd
                                UNION ALL
                                SELECT	tgl_bukti AS tgl,	no_bukti AS bku, ket AS ket,  isnull(total, 0) AS jumlah, '2' AS jns, kd_skpd AS kode
                                from trhtransout
                                WHERE pay = 'TUNAI' AND panjar NOT IN('1','3') and no_sp2d in
                                (SELECT no_sp2d as no_bukti FROM trhtransout where kd_skpd= ? GROUP BY no_sp2d HAVING COUNT(no_sp2d)>1)
                                AND MONTH(tgl_bukti)< ? and  no_kas not in
                                (SELECT min(z.no_kas) as no_bukti FROM trhtransout z WHERE z.jns_spp in (4,5,6) and kd_skpd= ?
                                AND MONTH(tgl_bukti)< ?
                                GROUP BY z.no_sp2d HAVING COUNT(z.no_sp2d)>1)
                                and jns_spp in (4,5,6) and kd_skpd= ?

                                UNION ALL
                                SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM trhoutlain  WHERE pay='TUNAI' UNION ALL
                                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_setorsimpanan WHERE jenis ='2' UNION ALL
                                SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM trhINlain WHERE pay='TUNAI' union all
                                select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],a.nilai [jumlah],'2' [jns],a.kd_skpd [kode] from trhstrpot a
                                where a.kd_skpd= ? and a.pay='' and jns_spp in ('1','2','3')
                                ) a
                                where month(a.tgl)< ? and kode= ?", [$kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan, $kd_skpd]);

        foreach ($saldo_lalu as $saldo_lalu) {
            $saldo_awal             = $saldo_lalu->terima + $saldoawal->sld_awal_tunai + $saldoawal->sld_awalpajak - $saldo_lalu->keluar;
        }

        // rincian
        $rincian = DB::select("SELECT * FROM (
                            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS masuk,0 AS keluar,kd_skpd AS kode FROM tr_ambilsimpanan UNION ALL
                            select f.tgl_kas as tgl,f.no_kas as bku,f.keterangan as ket, f.nilai as masuk, 0 as keluar,f.kd_skpd as kode from tr_jpanjar f join tr_panjar g
                            on f.no_panjar_lalu=g.no_panjar and f.kd_skpd=g.kd_skpd where f.jns=2 and g.pay='TUNAI' UNION ALL
                            select tgl_bukti [tgl],no_bukti [bku],ket [ket],nilai AS masuk,0 AS keluar,kd_skpd [kode] from trhtrmpot a
                            where kd_skpd= ? and pay='' and jns_spp in('1','2','3') union all
                            select tgl_panjar as tgl,no_panjar as bku,keterangan as ket, 0 as masuk,nilai as keluar,kd_skpd as kode from tr_panjar where pay='TUNAI' UNION ALL
                            select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, 0 as masuk,SUM(b.rupiah) as keluar, a.kd_skpd as kode
                            from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                            where jns_trans NOT IN ('4','2') and pot_khusus =0  and bank='TNK'  and jns_cp not in ('2')
                            GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
                            UNION ALL
                            SELECT a.tgl_bukti AS tgl,a.no_bukti AS bku,a.ket AS ket,0 AS masuk, SUM(z.nilai)-isnull(pot,0)  AS keluar,a.kd_skpd AS kode
                            FROM trhtransout a INNER JOIN trdtransout z ON a.no_bukti=z.no_bukti AND a.kd_skpd=z.kd_skpd
                            LEFT JOIN trhsp2d b ON z.no_sp2d = b.no_sp2d
                            LEFT JOIN (SELECT no_spm, SUM (nilai) pot	FROM trspmpot GROUP BY no_spm) c
                            ON b.no_spm = c.no_spm WHERE pay = 'TUNAI' AND panjar NOT IN('1','3')
                            AND MONTH(a.tgl_bukti)= ? and a.kd_skpd= ?
                            AND a.no_bukti NOT IN(
                            select no_bukti from trhtransout
                            where no_sp2d in
                            (SELECT ISNULL(no_sp2d,'') as no_bukti FROM trhtransout where kd_skpd= ? GROUP BY no_sp2d HAVING COUNT(no_sp2d)>1)
                            AND MONTH(tgl_bukti)= ? and  no_kas not in
                            (SELECT ISNULL(min(z.no_kas),'') as no_bukti FROM trhtransout z WHERE z.jns_spp in (4,5,6) and kd_skpd= ?
                            AND MONTH(tgl_bukti)= ?
                            GROUP BY z.no_sp2d HAVING COUNT(z.no_sp2d)>1)
                            and jns_spp in (4,5,6) and kd_skpd= ?)
                            GROUP BY a.tgl_bukti,a.no_bukti,a.ket,a.no_sp2d,z.no_sp2d,a.total,pot,a.kd_skpd
                            UNION ALL
                            select tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,0 AS masuk, ISNULL(total,0)  AS keluar,kd_skpd AS kode
                            from trhtransout
                            WHERE pay = 'TUNAI' AND panjar NOT IN('1','3') AND no_sp2d in
                            (SELECT ISNULL(no_sp2d,'') as no_bukti FROM trhtransout where kd_skpd= ? GROUP BY no_sp2d HAVING COUNT(no_sp2d)>1)
                            AND MONTH(tgl_bukti)= ? and  no_kas not in
                            (SELECT ISNULL(min(z.no_kas),'') as no_bukti FROM trhtransout z WHERE z.jns_spp in (4,5,6) and kd_skpd= ?
                            AND MONTH(tgl_bukti)= ?
                            GROUP BY z.no_sp2d HAVING COUNT(z.no_sp2d)>1)
                            and jns_spp in (4,5,6) and kd_skpd= ?

                            UNION ALL
                            SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,0 as masuk,nilai AS keluar,kd_skpd AS kode FROM trhoutlain WHERE pay='TUNAI' UNION ALL
                            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket, 0 as masuk,nilai AS keluar,kd_skpd AS kode FROM tr_setorsimpanan WHERE jenis ='2'
                            UNION ALL
                            SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,nilai as masuk,0 AS keluar,kd_skpd AS kode FROM trhINlain WHERE pay='TUNAI' union all
                            select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],0 as masuk,nilai AS keluar,a.kd_skpd [kode] from trhstrpot a
                            where a.kd_skpd= ? and a.pay='' and jns_spp in ('1','2','3')
                            )a
                            where month(a.tgl)= ? and kode= ? ORDER BY a.tgl,CAST(bku AS int)", [$kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $kd_skpd, $bulan, $kd_skpd]);


        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'saldo_awal'        => $saldo_awal,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];
        // dd ($data['saldo_awal']);

        $view =  view('skpd.laporan_bendahara.cetak.bp_tunai')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('BP KAS TUNAI.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="BP KAS TUNAI - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }
}
