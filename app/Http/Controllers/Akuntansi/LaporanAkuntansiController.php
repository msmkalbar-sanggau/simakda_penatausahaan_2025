<?php

namespace App\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Static_;
use PhpParser\ErrorHandler\Collecting;
use PDF;
use Knp\Snappy\Pdf as SnappyPdf;

class LaporanAkuntansiController extends Controller
{
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'bendahara' => DB::table('ms_ttd')
                ->whereIn('kode', ['1'])
                ->orderBy('nip')
                ->orderBy('nama')
                ->get(),
            'data_skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')->where('kd_skpd', $kd_skpd)->first(),
            'jns_anggaran' => jenis_anggaran(),
            'jns_anggaran2' => jenis_anggaran()
        ];

        return view('akuntansi.index')->with($data);
    }

    public function konsolidasi()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'ttd' => DB::table('ms_ttd')
                ->whereIn('kode', ['1'])
                ->orderBy('nip')
                ->orderBy('nama')
                ->get(),
            'data_skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')->where('kd_skpd', $kd_skpd)->first(),
            'jns_anggaran' => jenis_anggaran(),
            'jns_anggaran2' => jenis_anggaran()
        ];

        return view('akuntansi.konsolidasi')->with($data);
    }

    public function perda()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'ttd' => DB::table('ms_ttd')
                ->whereIn('kode', ['1'])
                ->orderBy('nip')
                ->orderBy('nama')
                ->get(),
            'data_skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')->where('kd_skpd', $kd_skpd)->first(),
            'jns_anggaran' => jenis_anggaran(),
            'jns_anggaran2' => jenis_anggaran()
        ];

        return view('akuntansi.perda')->with($data);
    }

    // get skpd by radio
    public function cariSkpd(Request $request)
    {
        // $type       = Auth::user()->is_admin;
        // $jenis      = $request->jenis;
        //     // echo $jenis;
        //     // return;
        //     if ($jenis == 'skpd') {
        //         $data   = DB::table('ms_organisasi')->select(DB::raw("kd_org AS kd_skpd"), DB::raw("nm_org AS nm_skpd"))->orderBy('kd_org')->get();
        //     } else {
        //         $data   = DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->orderBy('kd_skpd')->get();
        //     }

        $type       = Auth::user()->is_admin;
        // $jenis      = $request->jenis;
        $jenis_skpd = substr(Auth::user()->kd_skpd, 18, 4);
        if ($jenis_skpd=='0000') {
            $jenis  = 'skpd';
        }else{
            $jenis  = 'unit';
        }
        $kd_skpd    = Auth::user()->kd_skpd;
        $kd_org     = substr($kd_skpd, 0, 17);
        if ($type == '1') {
            if ($jenis == 'skpd') {
                $data   = DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->orderBy('kd_org')->get();
            } else {
                $data   = DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->orderBy('kd_skpd')->get();
            }
        } else {
            if ($jenis == 'skpd') {
                // select kd_org AS kd_skpd, nm_org AS nm_skpd from [ms_skpd] where LEFT(kd_org) = 5.02.0.00.0.00.01)
                $data   = DB::table('ms_skpd')->where(DB::raw("LEFT(kd_skpd,17)"), '=', $kd_org)->select(DB::raw("kd_skpd AS kd_skpd"), DB::raw("nm_skpd AS nm_skpd"))->get();
            } else {
                $data   = DB::table('ms_skpd')->where(DB::raw("kd_skpd"), '=', $kd_skpd)->select('kd_skpd', 'nm_skpd')->get();
            }
        }
        // dd($kd_skpd);
        return response()->json($data);


        return response()->json($data);
    }

    public function cariSkpd2(Request $request)
    {
        $type       = Auth::user()->is_admin;
        // $jenis      = $request->jenis;
        $jenis_skpd = substr(Auth::user()->kd_skpd, 18, 4);
        if ($jenis_skpd=='0000') {
            $jenis  = 'skpd';
        }else{
            $jenis  = 'unit';
        }
        $kd_skpd    = Auth::user()->kd_skpd;
        $kd_org     = substr($kd_skpd, 0, 17);
        if ($type == '1') {
            if ($jenis == 'skpd') {
                $data   = DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->orderBy('kd_org')->get();
            } else {
                $data   = DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->orderBy('kd_skpd')->get();
            }
        } else {
            if ($jenis == 'skpd') {
                // select kd_org AS kd_skpd, nm_org AS nm_skpd from [ms_skpd] where LEFT(kd_org) = 5.02.0.00.0.00.01)
                $data   = DB::table('ms_skpd')->where(DB::raw("LEFT(kd_skpd,17)"), '=', $kd_org)->select(DB::raw("kd_skpd AS kd_skpd"), DB::raw("nm_skpd AS nm_skpd"))->get();
            } else {
                $data   = DB::table('ms_skpd')->where(DB::raw("kd_skpd"), '=', $kd_skpd)->select('kd_skpd', 'nm_skpd')->get();
            }
        }
        // dd($kd_skpd);
        return response()->json($data);
    }

    // get bendahara pengeluaran
    function cariTtd(Request $request)
    {
        $data       = DB::table('ms_ttd')
            ->whereIn('kode', ['1'])
            ->orderBy('nip')
            ->orderBy('nama')
            ->get();
        return response()->json($data);
    }

    function cariPaKpa(Request $request)
    {
        if (strlen($request->kd_skpd) == '17') {
            $kd_skpd    = $request->kd_skpd . '.0000';
        } else {
            $kd_skpd    = $request->kd_skpd;
        }
        $data       = DB::table('ms_ttd')->where(['kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->orderBy('nip')->orderBy('nama')->get();
        return response()->json($data);
    }

    function cariSubkegiatan(Request $request)
    {
        $kd_skpd        = $request->kd_skpd;
        $jns_anggaran   = $request->jns_anggaran;
        $data           = DB::table('trskpd')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $jns_anggaran])->orderBy('kd_sub_kegiatan')->get();
        return response()->json($data);
    }
    function cariAkunBelanja(Request $request)
    {
        $kd_skpd        = $request->kd_skpd;
        $jns_anggaran   = $request->jns_anggaran;
        $subkegiatan    = $request->subkegiatan;
        $data           = DB::table('trdrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $jns_anggaran, 'kd_sub_kegiatan' => $subkegiatan])->orderBy('kd_rek6')->get();
        return response()->json($data);
    }

    public function carirek6(Request $request)
    {
        
        $data           = DB::table('ms_rek6')
                        ->orderBy('kd_rek6')->get();
        return response()->json($data);

    }

    public function cariskpdbb(Request $request)
    {
        
        $data           = DB::table('ms_skpd')
                        ->orderBy('kd_skpd')->get();
        return response()->json($data);

    }


    // Cetak List
    public function cetakbku(Request $request)
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
        $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();

        // rekal
        $stmt      = DB::update("exec recall_skpd ?", array($kd_skpd));

        $data_tahun_lalu = DB::table('ms_skpd')
            ->select(DB::raw('isnull(sld_awal,0) AS nilai'), 'sld_awalpajak')
            ->where('kd_skpd', $kd_skpd)
            ->first();
        $data_sawal1 = DB::table('trhrekal as a')
            ->select(
                'kd_skpd',
                'tgl_kas',
                'tgl_kas AS tanggal',
                'no_kas',
                DB::raw("'' AS kegiatan"),
                DB::raw("'' AS rekening"),
                'uraian',
                DB::raw("'0' AS terima"),
                DB::raw("'0' AS keluar"),
                DB::raw("'' AS st"),
                'jns_trans'
            )
            ->where(DB::raw("month(tgl_kas)"), '<', $bulan)
            ->where(DB::raw("YEAR(tgl_kas)"), $tahun_anggaran)
            ->where('kd_skpd', $kd_skpd);

        $data_sawal2 = DB::table('trdrekal as a')
            ->leftjoin('trhrekal as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->where(DB::raw("month(b.tgl_kas)"), '<', $bulan)->where(DB::raw("YEAR(b.tgl_kas)"), $tahun_anggaran)
            ->where('b.kd_skpd', $kd_skpd)->select(
                'b.kd_skpd',
                'b.tgl_kas',
                DB::raw(" '' AS tanggal"),
                'a.no_kas',
                'a.kd_sub_kegiatan as kegiatan',
                'a.kd_rek6 AS rekening',
                'a.nm_rek6 AS uraian',
                DB::raw("CASE WHEN a.keluar + a.terima <0 THEN (a.keluar*-1) ELSE a.terima END as terima"),
                DB::raw("CASE WHEN a.keluar+a.terima<0 THEN (a.terima*-1) ELSE a.keluar END as keluar"),
                DB::raw("case when a.terima<>0 then '1' else '2' end AS st"),
                'b.jns_trans'
            )
            ->unionAll($data_sawal1)
            ->distinct();

        $result = DB::table(DB::raw("({$data_sawal2->toSql()}) AS sub"))
            ->select(DB::raw('SUM(terima) AS terima'), DB::raw('SUM(keluar) AS keluar'), DB::raw('SUM(terima) - SUM(keluar) AS sel'))
            ->mergeBindings($data_sawal2)
            ->first();


        // RINCIAN
        $rincian1 = DB::table('trhrekal as a')
            ->select(
                'kd_skpd',
                'tgl_kas',
                'tgl_kas AS tanggal',
                'no_kas',
                DB::raw("'' AS kegiatan"),
                DB::raw("'' AS rekening"),
                'uraian',
                DB::raw("'0' AS terima"),
                DB::raw("'0' AS keluar"),
                DB::raw("'' AS st"),
                'jns_trans'
            )
            ->where(DB::raw("month(tgl_kas)"), '=', $bulan)
            ->where(DB::raw("YEAR(tgl_kas)"), $tahun_anggaran)
            ->where('kd_skpd', $kd_skpd);

        $rincian2 = DB::table('trdrekal as a')
            ->leftjoin('trhrekal as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->where(DB::raw("month(b.tgl_kas)"), '=', $bulan)->where(DB::raw("YEAR(b.tgl_kas)"), $tahun_anggaran)
            ->where('b.kd_skpd', $kd_skpd)->select(
                'b.kd_skpd',
                'b.tgl_kas',
                DB::raw(" '' AS tanggal"),
                'a.no_kas',
                'a.kd_sub_kegiatan as kegiatan',
                'a.kd_rek6 AS rekening',
                'a.nm_rek6 AS uraian',
                DB::raw("CASE WHEN a.keluar + a.terima <0 THEN (a.keluar*-1) ELSE a.terima END as terima"),
                DB::raw("CASE WHEN a.keluar+a.terima<0 THEN (a.terima*-1) ELSE a.keluar END as keluar"),
                DB::raw("case when a.terima<>0 then '1' else '2' end AS st"),
                'b.jns_trans'
            )
            ->unionAll($rincian1)
            ->distinct();

        $result_rincian = DB::table(DB::raw("({$rincian2->toSql()}) AS sub"))
            ->orderBy('tgl_kas')
            ->orderBy(DB::raw("CAST(no_kas AS INT)"))
            ->orderBy('jns_trans')
            ->orderBy('st')
            ->orderBy('rekening')
            ->mergeBindings($rincian2)
            ->get();

        // SALDO TUNAI
        // DB::select('exec my_stored_procedure(?,?,..)',array($Param1,$param2));
        $tunai_lalu = DB::select("exec kas_tunai_lalu ?,?", array($kd_skpd, $bulan));
        $tunai      = DB::select("exec kas_tunai ?,?", array($kd_skpd, $bulan));


        $terima_lalu = 0;
        $keluar_lalu = 0;
        foreach ($tunai_lalu as $lalu) {
            $terima_lalu += $lalu->terima;
            $keluar_lalu += $lalu->keluar;
        }

        $terima = 0;
        $keluar = 0;
        foreach ($tunai as $sekarang) {
            $terima += $sekarang->terima;
            $keluar += $sekarang->keluar;
        }
        // KAS BANK
        $kas_bank = sisa_bank_by_bulan($bulan);

        // KAS SALDO BERHARGA

        $surat_berharga = DB::table('trhsp2d')
            ->select(DB::raw('isnull(sum(nilai),0) AS nilai'))
            ->where(DB::raw("month(tgl_terima)"), '=', $bulan)
            ->where(['kd_skpd' => $kd_skpd, 'status_terima' => '1'])
            ->where(function ($query) use ($bulan) {
                $query->where(DB::raw('month(tgl_kas)'), '>', $bulan)->orWhereNull('no_kas')->orWhere('no_kas', '');
            })
            ->first();

        // SALDO PAJAK
        // $saldo_pajak_1 = DB::table('trhtrmpot as a')
        //     ->select('b.kd_rek6', 'b.nm_rek6', 'a.kd_skpd', DB::raw("SUM(CASE WHEN MONTH(tgl_bukti)< $bulan THEN b.nilai ELSE 0 END) AS terima_lalu"), DB::raw("SUM(CASE WHEN MONTH(tgl_bukti)= $bulan THEN b.nilai ELSE 0 END) AS terima_ini"), DB::raw("SUM(CASE WHEN MONTH(tgl_bukti)<= $bulan THEN b.nilai ELSE 0 END) AS terima"), DB::raw("0 AS setor_lalu"), DB::raw("0 AS setor_ini"), DB::raw("0 AS setor"))
        //     ->join('trdtrmpot as b', function ($join) {
        //         $join->on('a.no_bukti', '=', 'b.no_bukti');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->where('a.kd_skpd', $kd_skpd)
        //     ->groupBy('b.kd_rek6', 'b.nm_rek6', 'a.kd_skpd');

        // $saldo_pajak_2 = DB::table('trhstrpot as a')
        //     ->select('b.kd_rek6', 'b.nm_rek6', 'a.kd_skpd', DB::raw("SUM(CASE WHEN MONTH(tgl_bukti)< $bulan THEN b.nilai ELSE 0 END) AS terima_lalu"), DB::raw("SUM(CASE WHEN MONTH(tgl_bukti)= $bulan THEN b.nilai ELSE 0 END) AS terima_ini"), DB::raw("SUM(CASE WHEN MONTH(tgl_bukti)<= $bulan THEN b.nilai ELSE 0 END) AS terima"), DB::raw("0 AS setor_lalu"), DB::raw("0 AS setor_ini"), DB::raw("0 AS setor"))
        //     ->join('trdstrpot as b', function ($join) {
        //         $join->on('a.no_bukti', '=', 'b.no_bukti');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->leftJoin('trhsp2d as c', function ($join) {
        //         $join->on('a.no_bukti', '=', 'c.no_sp2d');
        //         $join->on('a.kd_skpd', '=', 'c.kd_skpd');
        //     })
        //     ->where('a.kd_skpd', $kd_skpd)
        //     ->groupBy('b.kd_rek6', 'b.nm_rek6', 'a.kd_skpd')
        //     ->unionAll($saldo_pajak_1);

        // $saldo_pajak_3 = DB::table('ms_pot as a')
        //     ->select(DB::raw('RTRIM(a.map_pot) as kd_rek6'), 'a.nm_rek6')
        //     ->whereIn('a.kd_rek6', ['210106010001', '210105020001', '210105010001', '210105030001', '210109010001']);

        // $saldo_pajak1 = DB::table($saldo_pajak_3, 'a')
        //     ->leftJoinSub($saldo_pajak_2, 'b', function ($join) {
        //         $join->on('a.kd_rek6', '=', 'b.kd_rek6');
        //     })->distinct()->get();

        // $sisa_pajak             = 0;
        // foreach ($saldo_pajak1 as $pajak1) {
        //     $sisa_pajak             += $pajak1->terima_ini + $pajak1->terima_lalu - $pajak1->setor_lalu - $pajak1->setor_ini;
        // }

        $sisa_pajak = collect(DB::select("SELECT ISNULL(SUM(terima_lalu),0) as terima_lalu, ISNULL(SUM(terima_ini),0) as terima_ini, ISNULL(SUM(terima),0) as terima,
            ISNULL(SUM(setor_lalu),0) as setor_lalu, ISNULL(SUM(setor_ini),0) as setor_ini, ISNULL(SUM(setor),0) as setor,
            ISNULL(SUM(terima)-SUM(setor),0) as sisa
            FROM
            (SELECT RTRIM(map_pot) as kd_rek6, nm_rek6 nm_rek6 FROM ms_pot WHERE kd_rek6 IN ('210106010001','210105020001 ','210105010001 ','210105030001','210109010001'))a
            LEFT JOIN
            (SELECT b.kd_rek6, b.nm_rek6,a.kd_skpd,
            SUM(CASE WHEN MONTH(tgl_bukti)< ? THEN b.nilai ELSE 0 END) AS terima_lalu,
            SUM(CASE WHEN MONTH(tgl_bukti)= ? THEN b.nilai ELSE 0 END) AS terima_ini,
            SUM(CASE WHEN MONTH(tgl_bukti)<= ? THEN b.nilai ELSE 0 END) AS terima,
            0 as setor_lalu,
            0 as setor_ini,
            0 as setor
            FROM trhtrmpot a
            INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
            LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
            WHERE a.kd_skpd= ?
            GROUP BY  b.kd_rek6, b.nm_rek6, a.kd_skpd

            UNION ALL

            SELECT b.kd_rek6, b.nm_rek6,a.kd_skpd,
            0 as terima_lalu,
            0 as terima_ini,
            0 as terima,
            SUM(CASE WHEN MONTH(tgl_bukti)< ? THEN b.nilai ELSE 0 END) AS setor_lalu,
            SUM(CASE WHEN MONTH(tgl_bukti)= ? THEN b.nilai ELSE 0 END) AS setor_ini,
            SUM(CASE WHEN MONTH(tgl_bukti)<= ? THEN b.nilai ELSE 0 END) AS setor
            FROM trhstrpot a
            INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
            LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
            WHERE a.kd_skpd= ?
            GROUP BY  b.kd_rek6, b.nm_rek6, a.kd_skpd)b ON a.kd_rek6=b.kd_rek6", [$bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd]))->first();


        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();


        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'data_sawal'        => $result,
            'data_rincian'      => $result_rincian,
            'data_tahun_lalu'   => $data_tahun_lalu,
            'tunai_lalu'        => $tunai_lalu,
            'tunai'             => $tunai,
            'terima_lalu'       => $terima_lalu,
            'keluar_lalu'       => $keluar_lalu,
            'terima'            => $terima,
            'keluar'            => $keluar,
            'saldo_bank'        => $kas_bank,
            'surat_berharga'    => $surat_berharga,
            'pajak'             => $sisa_pajak->sisa,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        
        $view =  view('skpd.laporan_bendahara.cetak.bku')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('laporan BKU.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="laporan BKU - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    // Cetak List
    public function cetakbku13(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $tahun_anggaran = '2022';

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();

        // rekal
        DB::update("exec recall_skpd ?", array($kd_skpd));

        $saldo_awal = collect(DB::select("SELECT SUM(z.terima) AS jmter,SUM(z.keluar) AS jm_kel , SUM(z.terima)-SUM(z.keluar) AS sel FROM (

                SELECT distinct z.* FROM ((SELECT kd_skpd,tgl_kas,tgl_kas AS tanggal,no_kas,'' AS kegiatan,
           '' AS rekening,uraian,0 AS terima,0 AS keluar , '' AS st,jns_trans FROM trhrekal a
           where month(a.tgl_kas) < ? AND
           year(a.tgl_kas) = ? and kd_skpd=?)
               UNION ALL
              ( SELECT a.kd_skpd,a.tgl_kas,NULL AS tanggal,b.no_kas,b.kd_sub_kegiatan as kegiatan,b.kd_rek6 AS rekening,
               b.nm_rek6 AS uraian,
               CASE WHEN b.keluar+b.terima<0 THEN (keluar*-1) ELSE terima END as terima,
               CASE WHEN b.keluar+b.terima<0 THEN (terima*-1) ELSE keluar END as keluar,
               case when b.terima<>0 then '1' else '2' end AS st, b.jns_trans FROM
               trdrekal b LEFT JOIN trhrekal a ON a.no_kas = b.no_kas and a.kd_skpd = b.kd_skpd where month(a.tgl_kas) <'$bulan' AND
               year(a.tgl_kas) = ? and b.kd_skpd=?))z


             )z WHERE
             month(z.tgl_kas) < ? and year(z.tgl_kas) = ? AND z.kd_skpd = ?", [$bulan, $tahun_anggaran, $kd_skpd, $tahun_anggaran, $kd_skpd, $bulan, $tahun_anggaran, $kd_skpd]))->first();

        $saldo_awal_pajak = collect(DB::select("SELECT isnull(sld_awal,0) AS jumlah,sld_awalpajak FROM ms_skpd where kd_skpd=?", [$kd_skpd]))->first();


        $sisa_bank = collect(DB::select("SELECT terima-keluar as sisa FROM(select
            SUM(case when jns=1 then jumlah else 0 end) AS terima,
            SUM(case when jns=2 then jumlah else 0 end) AS keluar
            from (

                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_setorsimpanan where (tunai<>1 OR tunai is null) union
                SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM trhINlain WHERE pay='BANK' union
            select c.tgl_kas [tgl],c.no_kas [bku] ,c.keterangan [ket],c.nilai [jumlah],'1' [jns],c.kd_skpd [kode] from tr_jpanjar c join tr_panjar d on
            c.no_panjar_lalu=d.no_panjar and c.kd_skpd=d.kd_skpd where c.jns='2' and c.kd_skpd=? and  d.pay='BANK' union all
            select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
            from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
            where jns_trans IN ('5') and bank='BNK' and a.kd_skpd=?
            GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
            union all

            SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,total-isnull(pot,0)-isnull(f.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM trhtransout
            a join trhsp2d b on a.no_sp2d=b.no_sp2d left join (select no_spm, sum(nilai)pot
            from trspmpot group by no_spm) c on b.no_spm=c.no_spm
             left join
            (
            select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
            where e.kd_skpd=? and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
                ) f on f.no_kas=a.no_bukti and f.kd_skpd=a.kd_skpd
              WHERE pay='BANK' and
             (panjar not in ('1') or panjar is null)

             union
             select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],sum(b.nilai) [jumlah],'2' [jns],a.kd_skpd [kode] from trhstrpot a
             join trdstrpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
             where a.kd_skpd=? and a.pay='BANK' group by a.tgl_bukti,a.no_bukti,a.ket,a.kd_skpd
      UNION
            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_ambilsimpanan union
      SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM trhoutlain WHERE pay='BANK' union
      SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'2' AS jns,kd_skpd_sumber AS kode FROM tr_setorpelimpahan_bank union

            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_ambilsimpanan WHERE status_drop!='1' union

      SELECT a.tgl_kas AS tgl,a.no_panjar AS bku,a.keterangan as ket,a.nilai-isnull(b.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM tr_panjar a
            left join
            (
                select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
                where e.kd_skpd=? and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
             ) b on a.no_panjar=b.no_kas and a.kd_skpd=b.kd_skpd
            where a.pay='BANK' and a.kd_skpd=?
            union all
            select d.tgl_bukti, d.no_bukti,d.ket [ket],sum(e.nilai) [jumlah],'1' [jns],d.kd_skpd [kode] from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
            where e.kd_skpd=? and d.no_sp2d='2977/TU/2022' and d.pay='BANK' group by d.tgl_bukti,d.no_bukti,d.ket,d.kd_skpd
            union all
            select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
            from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd and a.kd_sub_kegiatan=b.kd_sub_kegiatan
            where jns_trans NOT IN ('4','2','5') and pot_khusus =0  and bank='BNK' and a.kd_skpd=?
            GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd union all
            select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '1' as jns, a.kd_skpd as kode
            from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
            where jns_trans IN ('5') and bank='BNK' and a.kd_skpd=?
            GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
            ) a
      where month(tgl)<=? and kode=?) a", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $bulan, $kd_skpd]))->first();

        $data_tunai_lalu = DB::update("exec kas_tunai_lalu ?,?", array($kd_skpd, $bulan));

        $data_tunai = DB::update("exec kas_tunai ?,?", array($kd_skpd, $bulan));

        $saldo_pajak = collect(DB::select("SELECT ISNULL(SUM(terima_lalu),0) as terima_lalu, ISNULL(SUM(terima_ini),0) as terima_ini, ISNULL(SUM(terima),0) as terima,
        ISNULL(SUM(setor_lalu),0) as setor_lalu, ISNULL(SUM(setor_ini),0) as setor_ini, ISNULL(SUM(setor),0) as setor,
        ISNULL(SUM(terima)-SUM(setor),0) as sisa
        FROM
        (SELECT RTRIM(map_pot) as kd_rek6, nm_rek6 nm_rek6 FROM ms_pot WHERE kd_rek6 IN ('210106010001','210105020001 ','210105010001 ','210105030001','210109010001'))a
        LEFT JOIN
        (SELECT b.kd_rek6, b.nm_rek6,a.kd_skpd,
        SUM(CASE WHEN MONTH(tgl_bukti)<? THEN b.nilai ELSE 0 END) AS terima_lalu,
        SUM(CASE WHEN MONTH(tgl_bukti)=? THEN b.nilai ELSE 0 END) AS terima_ini,
        SUM(CASE WHEN MONTH(tgl_bukti)<=? THEN b.nilai ELSE 0 END) AS terima,
        0 as setor_lalu,
        0 as setor_ini,
        0 as setor
        FROM trhtrmpot a
        INNER JOIN trdtrmpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
        WHERE a.kd_skpd=?
        GROUP BY  b.kd_rek6, b.nm_rek6, a.kd_skpd

        UNION ALL

        SELECT b.kd_rek6, b.nm_rek6,a.kd_skpd,
        0 as terima_lalu,
        0 as terima_ini,
        0 as terima,
        SUM(CASE WHEN MONTH(tgl_bukti)<? THEN b.nilai ELSE 0 END) AS setor_lalu,
        SUM(CASE WHEN MONTH(tgl_bukti)=? THEN b.nilai ELSE 0 END) AS setor_ini,
        SUM(CASE WHEN MONTH(tgl_bukti)<=? THEN b.nilai ELSE 0 END) AS setor
        FROM trhstrpot a
        INNER JOIN trdstrpot b on a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
        LEFT JOIN trhsp2d c on a.kd_skpd=c.kd_skpd AND a.no_sp2d=c.no_sp2d
        WHERE a.kd_skpd=?
        GROUP BY  b.kd_rek6, b.nm_rek6, a.kd_skpd)b ON a.kd_rek6=b.kd_rek6", [$bulan, $bulan, $bulan, $kd_skpd, $bulan, $bulan, $bulan, $kd_skpd]))->first();

        $saldo_berharga = collect(DB::select("SELECT sum(nilai) as total from trhsp2d where month(tgl_terima)=? and kd_skpd = ? and status_terima = '1' and (month(tgl_kas) > ? or no_kas is null or no_kas='')", [$bulan, $kd_skpd, $bulan]))->first();

        $data_bku = DB::select("SELECT * FROM ( SELECT  z.* FROM ((SELECT kd_skpd,tgl_kas,tgl_kas AS tanggal,no_kas,'' AS kegiatan,
           '' AS rekening,uraian,0 AS terima,0 AS keluar , '' AS st,jns_trans FROM trhrekal a
           where month(a.tgl_kas) = ? AND
           year(a.tgl_kas) = ? and kd_skpd=?)
               UNION ALL
              ( SELECT a.kd_skpd,a.tgl_kas,NULL AS tanggal,b.no_kas,b.kd_sub_kegiatan as kegiatan,b.kd_rek6 AS rekening,
               b.nm_rek6 AS uraian,
			   CASE WHEN b.keluar+b.terima<0 THEN (keluar*-1) ELSE terima END as terima,
			   CASE WHEN b.keluar+b.terima<0 THEN (terima*-1) ELSE keluar END as keluar,
			   case when b.terima<>0 then '1' else '2' end AS st, b.jns_trans FROM
               trdrekal b LEFT JOIN trhrekal a ON a.no_kas = b.no_kas and a.kd_skpd = b.kd_skpd where month(a.tgl_kas) =? AND
               year(a.tgl_kas) = ? and b.kd_skpd=?))z ) OKE
               ORDER BY tgl_kas,CAST(no_kas AS INT),jns_trans,st,rekening", [$bulan, $tahun_anggaran, $kd_skpd, $bulan, $tahun_anggaran, $kd_skpd]);

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'data_bku'          => $data_bku,
            // 'data_sawal'        => $result,
            // 'data_rincian'      => $result_rincian,
            // 'data_tahun_lalu'   => $data_tahun_lalu,
            // 'tunai_lalu'        => $tunai_lalu,
            // 'tunai'             => $tunai,
            // 'terima_lalu'       => $terima_lalu,
            // 'keluar_lalu'       => $keluar_lalu,
            // 'terima'            => $terima,
            // 'keluar'            => $keluar,
            // 'saldo_bank'        => $kas_bank,
            // 'surat_berharga'    => $surat_berharga,
            // 'pajak'             => $sisa_pajak->sisa,
            // 'enter'             => $enter,
            // 'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        return view('skpd.laporan_bendahara.cetak.bku13')->with($data);
    }

    public function cetak_bb(Request $request){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);
        $dcetak    = $request->tanggal1;
        $dcetak2    = $request->tanggal2;
        $cetak          = $request->cetak;
        $skpd        = $request->kd_skpd;
        $rek6          = $request->rek6;
        // $kd_skpd        = Auth::user()->kd_skpd;

        
        $thn_ang = tahun_anggaran();

        if ((substr($rek6,0,1)=='9') or (substr($rek6,0,1)=='8') or (substr($rek6,0,1)=='4') or (substr($rek6,0,1)=='5') or (substr($rek6,0,1)=='7')){
        $csql3 = collect(DB::select("SELECT sum(a.debet) as debet,sum(a.kredit) as kredit FROM trdju_pkd a LEFT JOIN trhju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_unit=b.kd_skpd WHERE a.kd_rek6='$rek6' AND b.kd_skpd='$skpd' and b.tgl_voucher < '$dcetak'   AND YEAR(b.tgl_voucher)='$thn_ang'"))->first();
        } else if ($rek6=='310101010001'){
        $csql3 = collect(DB::select("SELECT sum(a.debet) as debet,sum(a.kredit) as kredit from (
                    
                    select sum(debet) debet, sum(kredit) kredit 
                    from trdju_pkd a inner join trhju_pkd b on a.no_voucher=b.no_voucher and a.kd_unit=b.kd_skpd
                    where kd_rek6='310101010001' and reev='0' and kd_skpd='$skpd' and tgl_voucher < '$dcetak'
                    union all
                    select sum(debet) debet, sum(kredit) kredit 
                    from trdju_pkd a inner join trhju_pkd b on a.no_voucher=b.no_voucher and a.kd_unit=b.kd_skpd
                    where kd_rek6='310101010001' and reev not in ('0') and kd_skpd='$skpd' and tgl_voucher < '$dcetak'
                    ) a "))->first();

        }else if ($rek6=='310102010001') {
            $csql3 = collect(DB::select("SELECT sum(a.debet) as debet,sum(a.kredit) as kredit from (
                    select sum(debet) debet, sum(kredit) kredit 
                    from trdju_pkd a inner join trhju_pkd b on a.no_voucher=b.no_voucher and a.kd_unit=b.kd_skpd
                    where left(kd_rek6,1) in ('7','8') and kd_skpd='$skpd' and tgl_voucher < '$dcetak'
                    
                    ) a "))->first();
        }
        else {
         $csql3 = collect(DB::select("SELECT sum(a.debet) as debet,sum(a.kredit) as kredit FROM trdju_pkd a LEFT JOIN trhju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_unit=b.kd_skpd WHERE a.kd_rek6='$rek6' AND b.kd_skpd='$skpd' and b.tgl_voucher < '$dcetak'   "))->first();
        }

        
        $idx=1;
        if($rek6=='310101010001'){
                $query = DB::select("SELECT kd_rek6, debet, kredit, tgl_voucher, ket, no_voucher FROM (
                                           SELECT a.kd_rek6,a.debet,a.kredit,b.tgl_voucher,b.ket,b.no_voucher FROM trdju_pkd a LEFT JOIN trhju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_unit=b.kd_skpd WHERE a.kd_rek6='310101010001' AND b.kd_skpd='$skpd' AND b.tgl_voucher>='$dcetak' AND b.tgl_voucher<='$dcetak2'  
                                           ) a
                                           ORDER BY tgl_voucher, debet-kredit");  

        }else if ($rek6=='310102010001') {
                    $query = DB::select("SELECT kd_rek6, debet, kredit, tgl_voucher, ket, no_voucher FROM (
                                           
                                           SELECT '310102010001' kd_rek6, SUM(a.debet) debet, SUM(a.kredit) kredit, b.tgl_voucher, 'SURPLUS/DEFISIT LO ('+b.ket+' )' ket, 'SURPLUS/DEFISIT LO - '+b.no_voucher as no_voucher FROM trdju_pkd a LEFT JOIN trhju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_unit=b.kd_skpd WHERE LEFT(a.kd_rek6,1) IN ('7','8') AND b.kd_skpd='$skpd' AND b.tgl_voucher>='$dcetak' AND b.tgl_voucher<='$dcetak2'
                                           GROUP BY b.tgl_voucher, b.no_voucher, b.ket) a
                                           ORDER BY tgl_voucher, debet-kredit");

        }else{
                $query = DB::select("SELECT a.kd_rek6,a.debet,a.kredit,b.tgl_voucher,b.ket,b.no_voucher FROM trdju_pkd a LEFT JOIN trhju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_unit=b.kd_skpd WHERE a.kd_rek6='$rek6' AND b.kd_skpd='$skpd' AND b.tgl_voucher>='$dcetak' AND b.tgl_voucher<='$dcetak2'  ORDER BY b.tgl_voucher, 
                                           case when left('$rek6',1) in (1,5,6,9) then kredit-debet else debet-kredit end");  
                //$query = $this->db->query("SELECT a.kd_rek6,a.debet,a.kredit,b.tgl_voucher,b.ket,b.no_voucher FROM trdju_pkd a LEFT JOIN trhju_pkd b ON a.no_voucher=b.no_voucher WHERE a.kd_rek6='$rek6' AND b.kd_skpd='$skpd' and b.tgl_voucher>='$dcetak' and b.tgl_voucher<='$dcetak2' and a.pos='1' ORDER by b.tgl_voucher, convert(b.no_voucher,unsigned)");  
        }
        
        
        $sc = collect(DB::select("SELECT tgl_rka,provinsi,kab_kota,daerah,thn_ang FROM sclient"))->first();

        $nogub = collect(DB::select("SELECT ket_perda, ket_perda_no, ket_perda_tentang FROM config_nogub_akt"))->first();


 
        // dd($query);


        // $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
            
        $data = [
            'header'    => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'csql3'     => $csql3,
            'query'     => $query,
            'daerah'    => $sc,
            'nogub'     => $nogub,
            'dcetak'    => $dcetak,
            'dcetak2'   => $dcetak2,
            'thn_ang'   => $thn_ang,
            'skpd'      => $skpd,
            'rek6'      => $rek6
        ];
        // if($format=='sap'){
        //     $view =  view('akuntansi.cetakan.lra_semester')->with($data);
        // }elseif($format=='djpk'){
        //     $view =  view('akuntansi.cetakan.lra_djpk')->with($data);
        // }elseif($format=='p77'){
        //     $view =  view('akuntansi.cetakan.lra_77')->with($data);
        // }elseif($format=='sng'){
            $view =  view('akuntansi.cetakan.buku_besar')->with($data);
        // }
        
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('BUKU BESAR.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="BUKU BESAR.xls"');
            return $view;
        }
    }
}
