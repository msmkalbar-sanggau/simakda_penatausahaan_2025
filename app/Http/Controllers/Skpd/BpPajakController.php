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

class BpPajakController extends Controller
{

    // Pilihan Cetak
    public function cariJenis(Request $request)
    {
        $pajak1 = $request->pajak1;
        if ($pajak1 == 'semua' || $pajak1 == 'tanpalsphk' || $pajak1 == 'hanyalsphk') {
            $data = [
                [
                    "id"   => 1,
                    "text" => " Penerimaan & Penyetoran (P77)"
                ],
                [
                    "id"   => 2,
                    "text" => " Rekapitulasi Penerimaan & Penyetoran"
                ],
                [
                    "id"   => 3,
                    "text" => " Per Pasal"
                ]
            ];
        } else {
            $data = [
                [
                    "id"   => 4,
                    "text" => "Global"
                ],
                [
                    "id"   => 5,
                    "text" => " Rinci"
                ]
            ];
        }

        return response()->json($data);
    }

    public function cariPasal()
    {
        $cari_pasal = DB::select('SELECT a.kd_rek6, b.nm_rek6 FROM trdtrmpot a
        INNER JOIN ms_pot b ON a.kd_rek6=b.kd_rek6
        GROUP BY a.kd_rek6,b.nm_rek6');
        return response()->json($cari_pasal);
    }

    // Cetak bppajak1
    public function cetakBpPajak(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $pilihan1       = $request->pilihan1;
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

        // saldo lalu
        if ($pilihan1 == 'semua') {
            $saldo_lalu = DB::select("SELECT
                            sum(case when jns=1 then terima else 0 end) as terima,
                            sum(case when jns=2 then keluar else 0 end ) as keluar
                            FROM(
                            SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+no_sp2d) AS ket,SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd FROM trhtrmpot a
                            INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                            GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd
                            UNION ALL
                            SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+no_terima) AS ket,'0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd FROM trhstrpot a
                            INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                            GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_terima, a.kd_skpd) a WHERE MONTH(tgl)< ?  AND kd_skpd= ? ", [$bulan, $kd_skpd]);
            $rincian = DB::select("SELECT * FROM(
                            SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+no_sp2d) AS ket,SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd FROM trhtrmpot a
                            INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                            GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd
                            UNION ALL
                            SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+no_terima) AS ket,'0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd FROM trhstrpot a
                            INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                            GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_terima, a.kd_skpd ) a
                            WHERE MONTH(tgl)= ? AND kd_skpd= ? ORDER BY tgl,Cast(bku as decimal)", [$bulan, $kd_skpd]);
        } else if ($pilihan1 == 'tanpalsphk') {
            $saldo_lalu = DB::select("SELECT SUM(terima) as terima, SUM(keluar) as keluar  FROM
                                        (
                                        SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                                        SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                                        FROM trhtrmpot a
                                        INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                                        WHERE (nocek = '' OR nocek IS NULL)	AND a.kd_skpd= ?
                                        GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                                        UNION ALL
                                        SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                                        '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                                        FROM trhstrpot a
                                        INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                                        WHERE (nocek = '' OR nocek IS NULL)	AND a.kd_skpd= ?
                                        GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                                        ) z WHERE MONTH(tgl)< ? ", [$kd_skpd, $kd_skpd, $bulan]);
            $rincian = DB::select("SELECT * FROM
                                        (
                                        SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                                        SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                                        FROM trhtrmpot a
                                        INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                                        WHERE (nocek = '' OR nocek IS NULL)	AND a.kd_skpd= ?
                                        GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                                        UNION ALL
                                        SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                                        '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                                        FROM trhstrpot a
                                        INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                                        WHERE (nocek = '' OR nocek IS NULL) AND a.kd_skpd= ?
                                        GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                                        ) z WHERE MONTH(tgl)= ?
                                        ORDER BY tgl, Cast(bku as decimal)", [$kd_skpd, $kd_skpd, $bulan]);
        } else if ($pilihan1 == 'hanyalsphk') {
            $saldo_lalu = DB::select("SELECT SUM(terima) as terima, SUM(keluar) as keluar  FROM
                                        (
                                        SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                                        SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                                        FROM trhtrmpot a
                                        INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                                        WHERE (nocek !='' AND nocek IS NOT NULL)	AND a.kd_skpd= ?
                                        GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                                        UNION ALL
                                        SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                                        '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                                        FROM trhstrpot a
                                        INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                                        WHERE (nocek !='' AND nocek IS NOT NULL)	AND a.kd_skpd= ?
                                        GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                                        ) z WHERE MONTH(tgl)< ? ", [$kd_skpd, $kd_skpd, $bulan]);
            $rincian = DB::select("SELECT * FROM
                                    (
                                    SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                                    SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                                    FROM trhtrmpot a
                                    INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                    LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                                    WHERE (nocek != '' AND nocek IS NOT NULL) AND a.kd_skpd= ?
                                    GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                                    UNION ALL
                                    SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                                    '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                                    FROM trhstrpot a
                                    INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                                    LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                                    WHERE (nocek != '' AND nocek IS NOT NULL) AND a.kd_skpd= ?
                                    GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                                    ) z WHERE MONTH(tgl)= ?
                                    ORDER BY tgl, Cast(bku as decimal)", [$kd_skpd, $kd_skpd, $bulan]);
        }


        foreach ($saldo_lalu as $saldo_lalu) {
            $saldoawal               = $saldo_lalu->terima - $saldo_lalu->keluar;
        }

        // rincian



        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'saldo_awal'        => $saldoawal,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.bp_pajak1')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('BP PAJAK.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="BP PAJAK - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    // Cetak bppajak2
    public function cetakBpPajak2(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $pilihan1       = $request->pilihan1;
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
        // return $pilihan1;
        // saldo lalu
        if ($pilihan1 == 'semua') {
            $rincian = DB::select("SELECT
	a.kd_rek6,
	a.nm_rek6,
	isnull( SUM ( terima_lalu ), 0 ) AS terima_lalu,
	isnull( SUM ( terima_ini ), 0 ) AS terima_ini,
	isnull( SUM ( setor_lalu ), 0 ) AS setor_lalu,
	isnull( SUM ( setor_ini ), 0 ) AS setor_ini
FROM
	(
	SELECT
		RTRIM( map_pot ) AS kd_rek6,
		nm_rek6
	FROM
		ms_pot
	WHERE
		map_pot IN ( '210107010001', '210108010001a','210108010001b', '210106010001', '210105010001', '210105020001', '210106010001', '210105030001', '210109010001' )
	) a
	LEFT JOIN (
	SELECT
		b.map_pot as kd_rek6,
		(SELECT nm_rek6 FROM ms_pot WHERE b.map_pot=map_pot) as nm_rek6,
		a.kd_skpd,
		SUM ( CASE WHEN MONTH ( tgl_bukti ) < ? THEN b.nilai ELSE 0 END ) AS terima_lalu,
		SUM ( CASE WHEN MONTH ( tgl_bukti ) = ? THEN b.nilai ELSE 0 END ) AS terima_ini,
		0 AS setor_lalu,
		0 AS setor_ini
	FROM
		trhtrmpot a
		INNER JOIN trdtrmpot b ON a.no_bukti= b.no_bukti
		AND a.kd_skpd= b.kd_skpd
		LEFT JOIN trhsp2d c ON a.kd_skpd= c.kd_skpd
		AND a.no_sp2d= c.no_sp2d
	WHERE
		a.kd_skpd= ?
	GROUP BY
		b.map_pot,
		a.kd_skpd UNION ALL
	SELECT
		b.map_pot as kd_rek6,
		(SELECT nm_rek6 FROM ms_pot WHERE b.map_pot=map_pot) as nm_rek6,
		a.kd_skpd,
		0 AS terima_lalu,
		0 AS terima_ini,
		SUM ( CASE WHEN MONTH ( tgl_bukti ) < ? THEN b.nilai ELSE 0 END ) AS setor_lalu,
		SUM ( CASE WHEN MONTH ( tgl_bukti ) = ? THEN b.nilai ELSE 0 END ) AS setor_ini
	FROM
		trhstrpot a
		INNER JOIN trdstrpot b ON a.no_bukti= b.no_bukti
		AND a.kd_skpd= b.kd_skpd
		LEFT JOIN trhsp2d c ON a.kd_skpd= c.kd_skpd
		AND a.no_sp2d= c.no_sp2d
	WHERE
		a.kd_skpd= ?
	GROUP BY
		b.map_pot,
		a.kd_skpd
	) b ON a.kd_rek6= b.kd_rek6
GROUP BY
	a.kd_rek6,
	a.nm_rek6
ORDER BY
	kd_rek6", [$bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd]);
        } else if ($pilihan1 == 'tanpalsphk') {
            $rincian = DB::select("SELECT a.kd_rek6, a.nm_rek6, ISNULL( SUM ( terima_lalu ), 0 ) AS terima_lalu, ISNULL( SUM ( terima_ini ), 0 ) AS terima_ini, ISNULL( SUM ( setor_lalu ), 0 ) AS setor_lalu, ISNULL( SUM ( setor_ini ), 0 ) AS setor_ini FROM
	            (
	                SELECT
		                RTRIM( map_pot ) AS kd_rek6,
		                nm_rek6
	                FROM
		                ms_pot
	                WHERE
		                map_pot IN ( '210107010001', '210108010001a','210108010001b', '210106010001', '210105010001', '210105020001', '210106010001',               '210105030001', '210109010001' )
	                ) a
	                LEFT JOIN (
	                SELECT
		                b.map_pot as kd_rek6,
		                (SELECT nm_rek6 FROM ms_pot WHERE b.map_pot=map_pot) as nm_rek6,
		                a.kd_skpd,
		                SUM ( CASE WHEN MONTH ( tgl_bukti ) < ? THEN b.nilai ELSE 0 END ) AS terima_lalu,
		                SUM ( CASE WHEN MONTH ( tgl_bukti ) = ? THEN b.nilai ELSE 0 END ) AS terima_ini,
		                0 AS setor_lalu,
		                0 AS setor_ini
	                FROM
		                trhtrmpot a
		                INNER JOIN trdtrmpot b ON a.no_bukti= b.no_bukti
		                AND a.kd_skpd= b.kd_skpd
		                LEFT JOIN trhsp2d c ON a.kd_skpd= c.kd_skpd
		                AND a.no_sp2d= c.no_sp2d
	                WHERE
		                ( nocek = '' OR nocek IS NULL )
		                AND a.kd_skpd= ?
	                GROUP BY
		                b.map_pot,
		                a.kd_skpd UNION ALL
	                SELECT
		                b.map_pot as kd_rek6,
		                (SELECT nm_rek6 FROM ms_pot WHERE b.map_pot=map_pot) as nm_rek6,
		                a.kd_skpd,
		                0 AS terima_lalu,
		                0 AS terima_ini,
		                SUM ( CASE WHEN MONTH ( tgl_bukti ) < ? THEN b.nilai ELSE 0 END ) AS setor_lalu,
		                SUM ( CASE WHEN MONTH ( tgl_bukti ) = ? THEN b.nilai ELSE 0 END ) AS setor_ini
	                FROM
		                trhstrpot a
		                INNER JOIN trdstrpot b ON a.no_bukti= b.no_bukti
		                AND a.kd_skpd= b.kd_skpd
		                LEFT JOIN trhsp2d c ON a.kd_skpd= c.kd_skpd
		                AND a.no_sp2d= c.no_sp2d
	                WHERE
		                ( nocek = '' OR nocek IS NULL )
		                AND a.kd_skpd= ?
	                GROUP BY
		                b.map_pot,
		                a.kd_skpd
	                ) b ON a.kd_rek6= b.kd_rek6
                GROUP BY
	                a.kd_rek6,
	                a.nm_rek6
                ORDER BY
	                kd_rek6", [$bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd]);
        } else if ($pilihan1 == 'hanyalsphk') {
            $rincian = DB::select("SELECT a.kd_rek6, a.nm_rek6, ISNULL(SUM(terima_lalu),0) as terima_lalu, ISNULL(SUM(terima_ini),0) as terima_ini,
                            ISNULL(SUM(setor_lalu),0) as setor_lalu, ISNULL(SUM(setor_ini),0) as setor_ini
                            FROM
                            (SELECT RTRIM(map_pot) as kd_rek6,nm_rek6 FROM ms_pot WHERE kd_rek6 IN ( '210107010001', '210108010001a','210108010001b', '210106010001', '210105010001', '210105020001', '210106010001', '210105030001', '210109010001' ))a
                            LEFT JOIN
                            (SELECT b.map_pot as kd_rek6, (SELECT nm_rek6 FROM ms_pot WHERE b.map_pot=map_pot) as nm_rek6,a.kd_skpd,
                            SUM(CASE WHEN MONTH(tgl_bukti)< ? THEN b.nilai ELSE 0 END) AS terima_lalu,
                            SUM(CASE WHEN MONTH(tgl_bukti)= ? THEN b.nilai ELSE 0 END) AS terima_ini,
                            0 as setor_lalu,
                            0 as setor_ini
                            FROM trhtrmpot a
                            INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                            LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                            WHERE (nocek !='' AND nocek IS NOT NULL)
                            AND	a.kd_skpd= ?
                            GROUP BY  b.map_pot, a.kd_skpd

                            UNION ALL

                            SELECT b.map_pot,(SELECT nm_rek6 FROM ms_pot WHERE b.map_pot=map_pot) as nm_rek6,a.kd_skpd,
                            0 as terima_lalu,
                            0 as terima_ini,
                            SUM(CASE WHEN MONTH(tgl_bukti)< ? THEN b.nilai ELSE 0 END) AS setor_lalu,
                            SUM(CASE WHEN MONTH(tgl_bukti)= ? THEN b.nilai ELSE 0 END) AS setor_ini
                            FROM trhstrpot a
                            INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                            LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                            WHERE (nocek !='' AND nocek IS NOT NULL)
                            AND	a.kd_skpd= ?
                            GROUP BY  b.map_pot, a.kd_skpd)b
                            ON a.kd_rek6=b.kd_rek6
                            GROUP BY a.kd_rek6, a.nm_rek6
                            ORDER BY kd_rek6", [$bulan, $bulan, $kd_skpd, $bulan, $bulan, $kd_skpd]);
        }


        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view = view('skpd.laporan_bendahara.cetak.bp_pajak2')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('BP PAJAK.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="BP PAJAK - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    // Cetak bppajak3
    public function cetakBpPajak3(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $pilihan1       = $request->pilihan1;
        $pilihan3       = $request->pilihan3;
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

        // saldo lalu
        if ($pilihan1 == 'semua') {
            $saldo_awal = DB::select("SELECT SUM(terima) as terima, SUM(keluar) as keluar  FROM
                    (
                    SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                    SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                    FROM trhtrmpot a
                    INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                    LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                    WHERE  a.kd_skpd= ?
                    AND b.kd_rek6= ?
                    GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                    UNION ALL
                    SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                    '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                    FROM trhstrpot a
                    INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                    LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                    WHERE  a.kd_skpd= ?
                    AND b.kd_rek6= ?
                    GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                    ) z WHERE MONTH(tgl)< ? ", [$kd_skpd, $pilihan3, $kd_skpd, $pilihan3, $bulan]);
            $rincian = DB::select("SELECT * FROM
                (
                SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                FROM trhtrmpot a
                INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                WHERE a.kd_skpd= ?
                AND b.kd_rek6= ?
                GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                UNION ALL
                SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                FROM trhstrpot a
                INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                WHERE a.kd_skpd= ?
                AND b.kd_rek6= ?
                GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                ) z WHERE MONTH(tgl)= ?
                ORDER BY tgl, Cast(bku as decimal)", [$kd_skpd, $pilihan3, $kd_skpd, $pilihan3, $bulan]);
        } else if ($pilihan1 == 'tanpalsphk') {
            $saldo_awal = DB::select("SELECT SUM(terima) as terima, SUM(keluar) as keluar  FROM
                        (
                        SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                        SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                        FROM trhtrmpot a
                        INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                        WHERE (nocek = '' OR nocek IS NULL)	AND a.kd_skpd= ?
                        AND b.kd_rek6= ?
                        GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                        UNION ALL
                        SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                        '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                        FROM trhstrpot a
                        INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                        WHERE (nocek = '' OR nocek IS NULL)	AND a.kd_skpd= ?
                        AND b.kd_rek6= ?
                        GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                        ) z WHERE MONTH(tgl)< ? ", [$kd_skpd, $pilihan3, $kd_skpd, $pilihan3, $bulan]);
            $rincian = DB::select("SELECT * FROM
                (
                SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                FROM trhtrmpot a
                INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                WHERE (nocek = '' OR nocek IS NULL)	AND a.kd_skpd= ?
                AND b.kd_rek6= ?
                GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                UNION ALL
                SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                FROM trhstrpot a
                INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                WHERE (nocek = '' OR nocek IS NULL)	AND a.kd_skpd= ?
                AND b.kd_rek6= ?
                GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                ) z WHERE MONTH(tgl)= ?
                ORDER BY tgl, Cast(bku as decimal)", [$kd_skpd, $pilihan3, $kd_skpd, $pilihan3, $bulan]);
        } else if ($pilihan1 == 'hanyalsphk') {
            $saldo_awal = DB::select("SELECT SUM(terima) as terima, SUM(keluar) as keluar  FROM
                    (
                    SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                    SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                    FROM trhtrmpot a
                    INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                    LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                    WHERE (nocek !='' AND nocek IS NOT NULL) AND a.kd_skpd= ?
                    AND b.kd_rek6= ?
                    GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                    UNION ALL
                    SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                    '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                    FROM trhstrpot a
                    INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                    LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                    WHERE (nocek !='' AND nocek IS NOT NULL) AND a.kd_skpd= ?
                    AND b.kd_rek6= ?
                    GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                    ) z WHERE MONTH(tgl)< ? ", [[$kd_skpd, $pilihan3, $kd_skpd, $pilihan3, $bulan]]);
            $rincian = DB::select("SELECT * FROM
                (
                SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  no sp2d:'+a.no_sp2d) AS ket,
                SUM(b.nilai) AS terima,'0' AS keluar,'1' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                FROM trhtrmpot a
                INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                WHERE (nocek != '' AND nocek IS NOT NULL) AND a.kd_skpd= ?
                AND b.kd_rek6= ?
                GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, c.no_sp2d, nocek
                UNION ALL
                SELECT a.no_bukti AS bku,a.tgl_bukti AS tgl,(ket+'  terima:'+a.no_terima) AS ket,
                '0' AS terima,SUM(b.nilai) AS keluar,'2' as jns,a.kd_skpd, c.no_sp2d, c.nocek
                FROM trhstrpot a
                INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
                WHERE (nocek != '' AND nocek IS NOT NULL) AND a.kd_skpd= ?
                AND b.kd_rek6= ?
                GROUP BY a.no_bukti, a.tgl_bukti, a.ket, a.no_sp2d, a.kd_skpd, a.no_terima, c.no_sp2d, nocek
                ) z WHERE MONTH(tgl)= ?
                ORDER BY tgl, Cast(bku as decimal)", [$kd_skpd, $pilihan3, $kd_skpd, $pilihan3, $bulan]);
        }

        foreach ($saldo_awal as $sawal) {
            $saldopjk = $sawal->terima - $sawal->keluar;
            $terima   = $sawal->terima;
            $keluar   = $sawal->keluar;
        }

        $namapilihan3 = cari_nama($pilihan3, 'ms_pot', 'kd_rek6', 'nm_rek6');
        // dd($namapilihan3);
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'pilihan3'          => $pilihan3,
            'namapilihan3'      => $namapilihan3,
            'bulan'             => $bulan,
            'saldopjk'          => $saldopjk,
            'terima'            => $terima,
            'keluar'            => $keluar,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.bp_pajak3')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('BP PAJAK.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="BP PAJAK - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }


    // Cetak bppajak3
    public function cetakBpPajak4(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $pilihan1       = $request->pilihan1;
        $pilihan2       = $request->pilihan2;
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

        // saldo lalu
        if ($pilihan1 == 'upgutu' && $pilihan2 == '4') {
            $rincian = DB::select("SELECT a.bulan, ISNULL(SUM(pph21),0) as pph21, ISNULL(SUM(pph22),0) as pph22, ISNULL(SUM(pph23),0) as pph23,
                ISNULL(SUM(pphn),0) as pphn, ISNULL(SUM(lain),0) as lain, ISNULL(SUM(pot),0) as pot, ISNULL(SUM(setor),0) as setor,
                ISNULL(SUM(pot)-SUM(setor),0) as saldo
                FROM
                (SELECT 1 as bulan UNION ALL
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
                SELECT 12 as bulan)a
                LEFT JOIN
                (SELECT MONTH(tgl_bukti) as bulan,
                SUM(CASE WHEN a.kd_rek6='210105010001' THEN a.nilai ELSE 0 END) AS pph21,
                SUM(CASE WHEN a.kd_rek6='210105020001' THEN a.nilai ELSE 0 END) AS pph22,
                SUM(CASE WHEN a.kd_rek6='210105030001' THEN a.nilai ELSE 0 END) AS pph23,
                SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai ELSE 0 END) AS pphn,
                SUM(CASE WHEN a.kd_rek6 NOT IN ('210105010001','210105020001','210105030001','210106010001') THEN a.nilai ELSE 0 END) AS lain,
                SUM(a.nilai) as pot,
                0 as setor
                FROM trdtrmpot a INNER JOIN trhtrmpot b on a.no_bukti=b.no_bukti
                AND a.kd_skpd=b.kd_skpd
                WHERE jns_spp in('1','2','3')  AND a.kd_skpd= ?
                GROUP BY month(tgl_bukti)
                UNION ALL
                SELECT MONTH(tgl_bukti) as bulan,
                0 AS pph21,
                0 AS pph22,
                0 AS pph23,
                0 AS pphn,
                0 AS lain,
                0 as pot,
                SUM(a.nilai) as setor
                FROM trdstrpot a INNER JOIN trhstrpot b on a.no_bukti=b.no_bukti
                AND a.kd_skpd=b.kd_skpd
                WHERE jns_spp in('1','2','3') AND a.kd_skpd= ?
                GROUP BY month(tgl_bukti)
                ) b
                ON a.bulan=b.bulan
                WHERE a.bulan<= ?
                GROUP BY a.bulan
                ORDER BY a.bulan", [$kd_skpd, $kd_skpd, $bulan]);

            $salpph21   = "";
            $salpph22   = "";
            $salpph23   = "";
            $salpphn    = "";
            $salppnpn   = "";
            $sallain    = "";
            $saliwp     = "";
            $saltaperum = "";
            $salhkpg    = "";
            $salpot     = "";
            $salset     = "";
            $saldopjk   = "";
        } else if ($pilihan1 == 'upgutu' && $pilihan2 == '5') {
            $saldo_awal = DB::select("SELECT  SUM(pph21) as pph21, SUM(pph22) as pph22, SUM(pph23) as pph23,
                    SUM(pphn) as pphn, SUM(lain) as lain, SUM(pot) as pot, SUM(setor) as setor,
                    SUM(pot)-SUM(setor) as saldo FROM
                    (SELECT a.no_bukti,tgl_bukti, ket,
                    SUM(CASE WHEN a.kd_rek6='210105010001' THEN a.nilai ELSE 0 END) AS pph21,
                    SUM(CASE WHEN a.kd_rek6='210105020001' THEN a.nilai ELSE 0 END) AS pph22,
                    SUM(CASE WHEN a.kd_rek6='210105030001' THEN a.nilai ELSE 0 END) AS pph23,
                    SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai ELSE 0 END) AS pphn,
                    SUM(CASE WHEN a.kd_rek6 NOT IN ('210105010001','210105020001','210105030001','210106010001') THEN a.nilai ELSE 0 END) AS lain,
                    SUM(a.nilai) as pot,
                    0 as setor,
                    1 as urut
                    FROM trdtrmpot a INNER JOIN trhtrmpot b on a.no_bukti=b.no_bukti
                    AND a.kd_skpd=b.kd_skpd
                    WHERE jns_spp in('1','2','3')  AND a.kd_skpd= ? AND MONTH(tgl_bukti)< ?
                    GROUP BY a.no_bukti,tgl_bukti,ket
                    UNION ALL
                    SELECT a.no_bukti, tgl_bukti, ket,
                    SUM(CASE WHEN a.kd_rek6='210105010001' THEN a.nilai*-1 ELSE 0 END) AS pph21,
                    SUM(CASE WHEN a.kd_rek6='210105020001' THEN a.nilai*-1 ELSE 0 END) AS pph22,
                    SUM(CASE WHEN a.kd_rek6='210105030001' THEN a.nilai*-1 ELSE 0 END) AS pph23,
                    SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai*-1 ELSE 0 END) AS pphn,
                    SUM(CASE WHEN a.kd_rek6 NOT IN ('210105010001','210105020001','210105030001','210106010001') THEN a.nilai*-1 ELSE 0 END) AS lain,
                    0 as pot,
                    SUM(a.nilai) as setor,
                    2 as urut
                    FROM trdstrpot a INNER JOIN trhstrpot b on a.no_bukti=b.no_bukti
                    AND a.kd_skpd=b.kd_skpd
                    WHERE jns_spp in('1','2','3') AND a.kd_skpd= ? AND MONTH(tgl_bukti)< ?
                    GROUP BY a.no_bukti,tgl_bukti, ket)z", [$kd_skpd, $bulan, $kd_skpd, $bulan]);

            $rincian = DB::select("SELECT * FROM (SELECT a.no_bukti bku,tgl_bukti, ket,
                    SUM(CASE WHEN a.kd_rek6='210105010001' THEN a.nilai ELSE 0 END) AS pph21,
                    SUM(CASE WHEN a.kd_rek6='210105020001' THEN a.nilai ELSE 0 END) AS pph22,
                    SUM(CASE WHEN a.kd_rek6='210105030001' THEN a.nilai ELSE 0 END) AS pph23,
                    SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai ELSE 0 END) AS pphn,
                    SUM(CASE WHEN a.kd_rek6 NOT IN ('210105010001','210105020001','210105030001','210106010001') THEN a.nilai ELSE 0 END) AS lain,
                    SUM(a.nilai) as pot,
                    0 as setor,
                    1 as urut
                    FROM trdtrmpot a INNER JOIN trhtrmpot b on a.no_bukti=b.no_bukti
                    AND a.kd_skpd=b.kd_skpd
                    WHERE jns_spp in('1','2','3')  AND a.kd_skpd= ? AND MONTH(tgl_bukti)= ?
                    GROUP BY a.no_bukti,tgl_bukti,ket
                    UNION ALL
                    SELECT a.no_bukti bku, tgl_bukti, ket,
                    0 AS pph21,
                    0 AS pph22,
                    0 AS pph23,
                    0 AS pphn,
                    0 AS lain,
                    0 as pot,
                    SUM(a.nilai) as setor,
                    2 as urut
                    FROM trdstrpot a INNER JOIN trhstrpot b on a.no_bukti=b.no_bukti
                    AND a.kd_skpd=b.kd_skpd
                    WHERE jns_spp in('1','2','3') AND a.kd_skpd= ? AND MONTH(tgl_bukti)= ?
                    GROUP BY a.no_bukti,tgl_bukti, ket)a
                    ORDER BY tgl_bukti, Cast(bku as decimal), urut", [$kd_skpd, $bulan, $kd_skpd, $bulan]);

            foreach ($saldo_awal as $sawal) {
                $salpph21   = $sawal->pph21;
                $salpph22   = $sawal->pph22;
                $salpph23   = $sawal->pph23;
                $salpphn    = $sawal->pphn;
                $salppnpn   = "";
                $sallain    = $sawal->lain;
                $saliwp     = "";
                $saltaperum = "";
                $salhkpg    = "";
                $salpot     = $sawal->pot;
                $salset     = $sawal->setor;
                $saldopjk   = $sawal->saldo;
            }
        } else if ($pilihan1 == 'ls' && $pilihan2 == '4') {
            $saldo_awal = "";
            $rincian = DB::select("SELECT a.bulan, ISNULL(SUM(pph21),0) as pph21, ISNULL(SUM(pph22),0) as pph22, ISNULL(SUM(pph23),0) as pph23,
                ISNULL(SUM(pphn),0) as pphn, ISNULL(SUM(lain),0) as lain, ISNULL(SUM(iwp),0) as iwp, ISNULL(SUM(taperum),0) as taperum,
                ISNULL(SUM(hkpg),0) as hkpg, ISNULL(SUM(pot),0) as pot, ISNULL(SUM(setor),0) as setor,
                ISNULL(SUM(pot)-SUM(setor),0) as saldo
                FROM
                (SELECT 1 as bulan UNION ALL
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
                SELECT 12 as bulan)a
                LEFT JOIN
                (SELECT MONTH(tgl_bukti) as bulan,
                SUM(CASE WHEN a.kd_rek6='210105010001' THEN a.nilai ELSE 0 END) AS pph21,
                SUM(CASE WHEN a.kd_rek6='210105020001' THEN a.nilai ELSE 0 END) AS pph22,
                SUM(CASE WHEN a.kd_rek6='210105030001' THEN a.nilai ELSE 0 END) AS pph23,
                SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai ELSE 0 END) AS pphn,
                SUM(CASE WHEN a.kd_rek6 in ('2110701','2110702','2110703') THEN a.nilai ELSE 0 END) AS iwp,
                SUM(CASE WHEN a.kd_rek6='210107010001' THEN a.nilai ELSE 0 END) AS taperum,
                SUM(CASE WHEN a.kd_rek6='210601010007' THEN a.nilai ELSE 0 END) AS hkpg,
                SUM(CASE WHEN a.kd_rek6 NOT IN ('210105010001','210105020001','210105030001','210106010001','2110701','2110702','2110703','210107010001','210601010007') THEN a.nilai ELSE 0 END) AS lain,
                SUM(a.nilai) as pot,
                0 as setor
                FROM trdtrmpot a INNER JOIN trhtrmpot b on a.no_bukti=b.no_bukti
                AND a.kd_skpd=b.kd_skpd
                WHERE jns_spp in('4','6','5')  AND a.kd_skpd= ?
                GROUP BY month(tgl_bukti)
                UNION ALL
                SELECT MONTH(tgl_bukti) as bulan,
                0 AS pph21,
                0 AS pph22,
                0 AS pph23,
                0 AS pphn,
                0 AS iwp,
                0 AS taperum,
                0 AS hkpg,
                0 AS lain,
                0 as pot,
                SUM(a.nilai) as setor
                FROM trdstrpot a INNER JOIN trhstrpot b on a.no_bukti=b.no_bukti
                AND a.kd_skpd=b.kd_skpd
                WHERE jns_spp in('4','6','5') AND a.kd_skpd= ?
                GROUP BY month(tgl_bukti)
                ) b
                ON a.bulan=b.bulan
                WHERE a.bulan<= ?
                GROUP BY a.bulan
                ORDER BY a.bulan", [$kd_skpd, $kd_skpd, $bulan]);

            $salpph21   = "";
            $salpph22   = "";
            $salpph23   = "";
            $salpphn    = "";
            $salppnpn   = "";
            $sallain    = "";
            $saliwp     = "";
            $saltaperum = "";
            $salhkpg    = "";
            $salpot     = "";
            $salset     = "";
            $saldopjk   = "";
        } else if ($pilihan1 == 'ls' && $pilihan2 == '5') {
            $saldo_awal = DB::select("SELECT  SUM(pph21) as pph21, SUM(pph22) as pph22, SUM(pph23) as pph23,
                SUM(pphn) as pphn, SUM(ppnpn) as ppnpn, SUM(lain) as lain, ISNULL(SUM(iwp),0) as iwp, ISNULL(SUM(taperum),0) as taperum,
                ISNULL(SUM(hkpg),0) as hkpg, ISNULL(SUM(pot),0) as pot, ISNULL(SUM(setor),0) as setor,
                ISNULL(SUM(pot)-SUM(setor),0) as saldo FROM
                (SELECT a.no_bukti,tgl_bukti, ket,
                SUM(CASE WHEN a.kd_rek6='210105010001' THEN a.nilai ELSE 0 END) AS pph21,
                SUM(CASE WHEN a.kd_rek6='210105020001' THEN a.nilai ELSE 0 END) AS pph22,
                SUM(CASE WHEN a.kd_rek6='210105030001' THEN a.nilai ELSE 0 END) AS pph23,
                SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai ELSE 0 END) AS pphn,
                SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai ELSE 0 END) AS ppnpn,
                SUM(CASE WHEN a.kd_rek6 in ('2110701','2110702','2110703') THEN a.nilai ELSE 0 END) AS iwp,
                SUM(CASE WHEN a.kd_rek6='210107010001' THEN a.nilai ELSE 0 END) AS taperum,
                SUM(CASE WHEN a.kd_rek6='210601010007' THEN a.nilai ELSE 0 END) AS hkpg,
                SUM(CASE WHEN a.kd_rek6 NOT IN ('210105010001','210105020001','210105030001','210106010001','2110701','2110702','2110703','210107010001','210601010007','210106010001') THEN a.nilai ELSE 0 END) AS lain,
                SUM(a.nilai) as pot,
                0 as setor,
                1 as urut
                FROM trdtrmpot a INNER JOIN trhtrmpot b on a.no_bukti=b.no_bukti
                AND a.kd_skpd=b.kd_skpd
                WHERE jns_spp in('4','6','5')  AND a.kd_skpd= ? AND MONTH(tgl_bukti)< ?
                GROUP BY a.no_bukti,tgl_bukti,ket
                UNION ALL
                SELECT a.no_bukti, tgl_bukti, ket,
                SUM(CASE WHEN a.kd_rek6='210105010001' THEN a.nilai*-1 ELSE 0 END) AS pph21,
                SUM(CASE WHEN a.kd_rek6='210105020001' THEN a.nilai*-1 ELSE 0 END) AS pph22,
                SUM(CASE WHEN a.kd_rek6='210105030001' THEN a.nilai*-1 ELSE 0 END) AS pph23,
                SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai*-1 ELSE 0 END) AS pphn,
                SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai*-1 ELSE 0 END) AS ppnpn,
                SUM(CASE WHEN a.kd_rek6 in ('2110701','2110702','2110703') THEN a.nilai*-1 ELSE 0 END) AS iwp,
                SUM(CASE WHEN a.kd_rek6='210107010001' THEN a.nilai*-1 ELSE 0 END) AS taperum,
                SUM(CASE WHEN a.kd_rek6='210601010007' THEN a.nilai*-1 ELSE 0 END) AS hkpg,
                SUM(CASE WHEN a.kd_rek6 NOT IN ('210105010001','210105020001','210105030001','210106010001','2110701','2110702','2110703','210107010001','210601010007','210106010001') THEN a.nilai*-1 ELSE 0 END) AS lain,
                0 as pot,
                SUM(a.nilai) as setor,
                2 as urut
                FROM trdstrpot a INNER JOIN trhstrpot b on a.no_bukti=b.no_bukti
                AND a.kd_skpd=b.kd_skpd
                WHERE jns_spp in('4','6','5') AND a.kd_skpd= ? AND MONTH(tgl_bukti)< ?
                GROUP BY a.no_bukti,tgl_bukti, ket)z", [$kd_skpd, $bulan, $kd_skpd, $bulan]);

            $rincian = DB::select("SELECT * FROM(
                    SELECT a.no_bukti bku,tgl_bukti, ket,
                    SUM(CASE WHEN a.kd_rek6='210105010001' THEN a.nilai ELSE 0 END) AS pph21,
                    SUM(CASE WHEN a.kd_rek6='210105020001' THEN a.nilai ELSE 0 END) AS pph22,
                    SUM(CASE WHEN a.kd_rek6='210105030001' THEN a.nilai ELSE 0 END) AS pph23,
                    SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai ELSE 0 END) AS pphn,
                    SUM(CASE WHEN a.kd_rek6='210106010001' THEN a.nilai ELSE 0 END) AS ppnpn,
                    SUM(CASE WHEN a.kd_rek6 in ('2110701','2110702','2110703') THEN a.nilai ELSE 0 END) AS iwp,
                    SUM(CASE WHEN a.kd_rek6='210107010001' THEN a.nilai ELSE 0 END) AS taperum,
                    SUM(CASE WHEN a.kd_rek6='210601010007' THEN a.nilai ELSE 0 END) AS hkpg,
                    SUM(CASE WHEN a.kd_rek6 NOT IN ('210105010001','210105020001','210105030001','210106010001','2110701','2110702','2110703','210107010001','210601010007','210106010001') THEN a.nilai ELSE 0 END) AS lain,
                    SUM(a.nilai) as pot,
                    0 as setor,
                    1 as urut
                    FROM trdtrmpot a INNER JOIN trhtrmpot b on a.no_bukti=b.no_bukti
                    AND a.kd_skpd=b.kd_skpd
                    WHERE jns_spp in('4','6','5')  AND a.kd_skpd= ? AND MONTH(tgl_bukti)= ?
                    GROUP BY a.no_bukti,tgl_bukti,ket
                    UNION ALL
                    SELECT a.no_bukti bku, tgl_bukti, ket,
                    0 AS pph21,
                    0 AS pph22,
                    0 AS pph23,
                    0 AS pphn,
                    0 AS ppnpn,
                    0 AS iwp,
                    0 AS taperum,
                    0 AS hkpg,
                    0 AS lain,
                    0 as pot,
                    SUM(a.nilai) as setor,
                    2 as urut
                    FROM trdstrpot a INNER JOIN trhstrpot b on a.no_bukti=b.no_bukti
                    AND a.kd_skpd=b.kd_skpd
                    WHERE jns_spp in('4','6','5') AND a.kd_skpd= ? AND MONTH(tgl_bukti)= ?
                    GROUP BY a.no_bukti,tgl_bukti, ket) a
                    ORDER BY tgl_bukti,CAST(bku as numeric), urut ", [$kd_skpd, $bulan, $kd_skpd, $bulan]);

            foreach ($saldo_awal as $sawal) {
                $salpph21   = $sawal->pph21;
                $salpph22   = $sawal->pph22;
                $salpph23   = $sawal->pph23;
                $salpphn    = $sawal->pphn;
                $salppnpn   = $sawal->ppnpn;
                $sallain    = $sawal->lain;
                $saliwp     = $sawal->iwp;
                $saltaperum = $sawal->taperum;
                $salhkpg    = $sawal->hkpg;
                $salpot     = $sawal->pot;
                $salset     = $sawal->setor;
                $saldopjk   = $sawal->saldo;
            }
        }
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();

        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,

            'pilihan2'          => $pilihan2,

            'salpph21'         => $salpph21,
            'salpph22'         => $salpph22,
            'salpph23'         => $salpph23,
            'salpphn'          => $salpphn,
            'salppnpn'         => $salppnpn,
            'sallain'          => $sallain,
            'saliwp'           => $saliwp,
            'saltaperum'       => $saltaperum,
            'salhkpg'          => $salhkpg,
            'salpot'           => $salpot,
            'salset'           => $salset,
            'saldopjk'         => $saldopjk,

            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.bp_pajak4')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('BP PAJAK.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="BP PAJAK - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }
}
