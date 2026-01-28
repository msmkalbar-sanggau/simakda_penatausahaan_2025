<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Crypt;
use stdClass;
use PDF;

use function PHPUnit\Framework\isNull;

class SetorSisaController extends Controller
{
    public function index()
    {
        $kd_skpd    = Auth::user()->kd_skpd;
        $skpd       = collect(DB::select('SELECT kd_skpd, nm_skpd FROM ms_skpd WHERE kd_skpd = ?', [$kd_skpd]))->first();
        $ttdbd      = DB::select('SELECT nip, nama, jabatan, pangkat FROM ms_ttd WHERE kd_skpd = ? AND kode=?', [$kd_skpd, 'BK']);
        $ttdpag     = DB::select('SELECT nip, nama, jabatan, pangkat FROM ms_ttd WHERE kd_skpd = ? AND kode = ?', [$kd_skpd, 'KPA']);

        $data = [
            'skpd1'    => $skpd,
            'bend'      => $ttdbd,
            'pa'        => $ttdpag,
        ];
        return view('skpd.setor_sisa_kas.index')->with($data);
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data1 = DB::table('trhkasin_pkd as a')->select('a.*', DB::raw("(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd=a.kd_skpd) as nm_skpd"))->where(['a.kd_skpd' => $kd_skpd])->whereIn('a.jns_trans', ['1', '5']);
        $data2 = DB::table('trhkasin_pkd as a')->select('a.*', DB::raw("(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd=a.kd_skpd) as nm_skpd"))->where(['a.kd_skpd' => $kd_skpd, 'a.jns_trans' => '4'])->whereRaw("no_sts IN (SELECT no_sts FROM trdkasin_pkd WHERE LEFT(kd_rek6,12)=?)", ['410411010001'])->unionAll($data1);
        $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
            ->mergeBindings($data2)
            ->orderBy(DB::raw("CAST(no_sts as INT)"))
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if (rtrim($row->status) != '1') {
                $btn = '<a href="' . route("skpd.setor_sisa.edit", Crypt::encryptString($row->no_sts)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="hapusSetor(' . $row->no_sts . ', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            } else {
                $btn = '<a href="' . route("skpd.setor_sisa.edit", Crypt::encryptString($row->no_sts)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.setor_sisa_kas.index');
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'sisa_tunai' => load_sisa_tunai(),
            'no_urut' => no_urut($kd_skpd)
        ];

        return view('skpd.setor_sisa_kas.create')->with($data);
    }

    public function noSp2d(Request $request)
    {
        $jenis_transaksi = $request->jenis_transaksi;
        $kd_skpd = Auth::user()->kd_skpd;
        // if ($jenis_transaksi == '1') {
        //     $data = DB::table('trhsp2d')
        //         ->select('no_sp2d', 'jns_spp', DB::raw("CASE jns_spp WHEN '4' THEN 'LS GAJI' WHEN '6' THEN 'LS BARANG/JASA' WHEN '1' THEN 'UP' WHEN '2' THEN 'GU' WHEN '7' THEN 'GU NIHIL' ELSE 'TU' END as jns_cp"))
        //         ->where(['kd_skpd' => $kd_skpd])
        //         ->whereIn('jns_spp', ['1', '2', '7'])
        //         ->get();
        // } elseif ($jenis_transaksi == '5') {
        //     $data = DB::table('trhsp2d')
        //         ->select('no_sp2d', 'jns_spp', DB::raw("CASE jns_spp WHEN '4' THEN 'LS GAJI' WHEN '6' THEN 'LS BARANG/JASA' WHEN '1' THEN 'UP' WHEN '5' THEN 'LS PIHAK KETIGA LAINNYA' WHEN '2' THEN 'GU' WHEN '7' THEN 'GU NIHIL' ELSE 'TU' END as jns_cp"))
        //         ->where(['kd_skpd' => $kd_skpd])
        //         ->whereIn('jns_spp', ['4', '5', '6'])
        //         ->get();
        // }

        DB::enableQueryLog();
        if ($jenis_transaksi == '1') {
            $data = DB::table('trhsp2d as a')
                ->join('trhspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->select('a.no_sp2d', 'a.jns_spp', 'b.jns_beban', 'a.nilai', DB::raw("CASE a.jns_spp WHEN '4' THEN 'LS GAJI' WHEN '6' THEN 'LS BARANG/JASA' WHEN '1' THEN 'UP' WHEN '2' THEN 'GU' WHEN '7' THEN 'GU NIHIL' ELSE 'TU' END as jns_cp"))
                ->where(['a.kd_skpd' => $kd_skpd])
                ->whereIn('a.jns_spp', ['3', '6', '1', '2', '7'])
                ->whereRaw("b.no_spp NOT IN (SELECT no_spp FROM trhspp WHERE jns_spp=? and jns_beban=?)", ['6', '6'])
                ->get();
        } elseif ($jenis_transaksi == '5') {
            $data = DB::select("SELECT * from (
                select a.no_sp2d,a.jns_spp, a.nilai,b.jns_beban,CASE a.jns_spp WHEN '4' THEN 'LS GAJI' WHEN '6' THEN 'LS BARANG/JASA' WHEN '1' THEN 'UP' WHEN '5' THEN 'LS PIHAK KETIGA LAINNYA' WHEN '2' THEN 'GU' WHEN '7' THEN 'GU NIHIL' ELSE 'TU' END as jns_cp FROM trhsp2d as a INNER JOIN trhspp as b ON a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd where a.kd_skpd=? and a.jns_spp IN (?,?,?)
                )z", [$kd_skpd, '4', '5', '6']);
        }

        return response()->json($data);
    }

    public function kegiatan(Request $request)
    {
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = Auth::user()->kd_skpd;

        $jenis_spp = DB::table('trhsp2d')->select('jns_spp')->where(['no_sp2d' => $no_sp2d])->first();
        $jns_spp = $jenis_spp->jns_spp;

        if ($jns_spp == '1' || $jns_spp == '2' || $jns_spp == '7') {
            $data = DB::table('trdtransout as a')->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->where(['a.no_sp2d' => $no_sp2d])->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->get();
        } else {
            $data = DB::table('trdspp as a')->join('trhsp2d as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->where(['b.no_sp2d' => $no_sp2d])->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->get();
        }

        //SISA BANK
        $kas = DB::select("SELECT terima-keluar as saldo_lalu FROM(select
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
         where  kode= ? ) a", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]);
        foreach ($kas as $kas) {
            $kas             = $kas->saldo_lalu;
        }
        // dd($sisa_bank);


        //SISA TUNAI
        $sisa_tunai = collect(DB::select("SELECT SUM(a.masuk) as terima,
        SUM(a.keluar) as keluar FROM (
        -- Saldo Awal
        SELECT '2022-01-01' AS tgl, null AS bku,
        'Saldo Awal' AS ket, sld_awal_tunai AS masuk, 0 AS keluar, kd_skpd AS kode FROM ms_skpd WHERE kd_skpd = ?
        UNION ALL
                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS masuk,0 AS keluar,kd_skpd AS kode FROM tr_ambilsimpanan UNION ALL
                select f.tgl_kas as tgl,f.no_kas as bku,f.keterangan as ket, f.nilai as masuk, 0 as keluar,f.kd_skpd as kode from tr_jpanjar f join tr_panjar g
                on f.no_panjar_lalu=g.no_panjar and f.kd_skpd=g.kd_skpd where f.jns=2 and g.pay='TUNAI' UNION ALL
                select tgl_bukti [tgl],no_bukti [bku],ket [ket],nilai AS masuk,0 AS keluar,kd_skpd [kode] from trhtrmpot a
                where kd_skpd=? and (pay='' OR pay='TUNAI')and jns_spp in('1','2','3') union all
                select tgl_panjar as tgl,no_panjar as bku,keterangan as ket, 0 as masuk,nilai as keluar,kd_skpd as kode from tr_panjar where pay='TUNAI' UNION ALL
                select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, 0 as masuk,SUM(b.rupiah) as keluar, a.kd_skpd as kode
                        from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
                        where jns_trans NOT IN ('4','2') and pot_khusus =0  and bank='TNK' and jns_cp not in ('2')
                        GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
                UNION ALL
                SELECT a.tgl_bukti AS tgl,a.no_bukti AS bku,a.ket AS ket,0 AS masuk, SUM(z.nilai)-isnull(pot,0)  AS keluar,a.kd_skpd AS kode
                        FROM trhtransout a INNER JOIN trdtransout z ON a.no_bukti=z.no_bukti AND a.kd_skpd=z.kd_skpd
                        LEFT JOIN trhsp2d b ON z.no_sp2d = b.no_sp2d
                        LEFT JOIN (SELECT no_spm, SUM (nilai) pot	FROM trspmpot GROUP BY no_spm) c
                        ON b.no_spm = c.no_spm WHERE pay = 'TUNAI' AND panjar NOT IN('1','3')
                        AND a.kd_skpd=?
                        AND a.no_bukti NOT IN(
                        select no_bukti from trhtransout
                        where no_sp2d in
                        (SELECT ISNULL(no_sp2d,'') as no_bukti FROM trhtransout where kd_skpd=? GROUP BY no_sp2d HAVING COUNT(no_sp2d)>1)
                        AND  no_kas not in
                        (SELECT ISNULL(min(z.no_kas),'') as no_bukti FROM trhtransout z WHERE z.jns_spp in (4,5,6) and kd_skpd=?
                        GROUP BY z.no_sp2d HAVING COUNT(z.no_sp2d)>1)
                        and jns_spp in (4,5,6) and kd_skpd=?)
                        GROUP BY a.tgl_bukti,a.no_bukti,a.ket,a.no_sp2d,z.no_sp2d,a.total,pot,a.kd_skpd
                UNION ALL
                select tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,0 AS masuk, ISNULL(total,0)  AS keluar,kd_skpd AS kode
                        from trhtransout
                        WHERE pay = 'TUNAI' AND panjar NOT IN('1','3') AND no_sp2d in
                        (SELECT ISNULL(no_sp2d,'') as no_bukti FROM trhtransout where kd_skpd=? GROUP BY no_sp2d HAVING COUNT(no_sp2d)>1)
                        AND no_kas not in
                        (SELECT ISNULL(min(z.no_kas),'') as no_bukti FROM trhtransout z WHERE z.jns_spp in (4,5,6) and kd_skpd=?
                        GROUP BY z.no_sp2d HAVING COUNT(z.no_sp2d)>1)
                        and jns_spp in (4,5,6) and kd_skpd=?

                UNION ALL
                SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,0 as masuk,nilai AS keluar,kd_skpd AS kode FROM trhoutlain WHERE pay='TUNAI' UNION ALL
                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket, 0 as masuk,nilai AS keluar,kd_skpd AS kode FROM tr_setorsimpanan WHERE jenis ='2' UNION  ALL
                SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,nilai as masuk,0 AS keluar,kd_skpd AS kode FROM trhINlain WHERE pay='TUNAI' union all
                select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],0 as masuk,nilai AS keluar,a.kd_skpd [kode] from trhstrpot a
                where a.kd_skpd=? and (a.pay='' OR a.pay='TUNAI') and jns_spp in ('1','2','3')

                ) a where kode=?", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]))->first();
        $sisa_tunai = $sisa_tunai->terima - $sisa_tunai->keluar;


        $potongan_ls = collect(DB::select("SELECT SUM(a.nilai) as total  FROM trspmpot a INNER JOIN trhsp2d b on b.no_spm = a.no_spm AND b.kd_skpd=a.kd_skpd
		where ((b.jns_spp = '4' AND b.jenis_beban != '1') or (b.jns_spp = '6' AND b.jenis_beban != '3'))
		and b.no_sp2d = ? and b.kd_skpd = ?", [$no_sp2d, $kd_skpd]))->first();

        return response()->json([
            'kegiatan' => $data,
            'sisa_bank' => $kas,
            'sisa_tunai' =>  $sisa_tunai,
            'potongan_ls' => $potongan_ls->total
        ]);
    }

    public function rekening(Request $request)
    {
        $jenis_transaksi = $request->jenis_transaksi;
        $no_sp2d = $request->no_sp2d;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_rek = $request->kd_rek6;
        $kd_skpd = Auth::user()->kd_skpd;

        if (isset($kd_rek)) {
            $kd_rek1 = [];
            foreach ($kd_rek as $rek) {
                $kd_rek1[] = $rek['kd_rek6'];
            }
            $kd_rek6 = json_decode(json_encode($kd_rek1), true);
        } else {
            $kd_rek6 = [];
        }

        // if ($jenis_transaksi == '5') {
        //     $data1 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
        //         $join->on('a.no_spp', '=', 'b.no_spp');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })->join('trhsp2d as c', function ($join) {
        //         $join->on('b.no_spp', '=', 'c.no_spp');
        //         $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        //     })->select('a.kd_rek6', 'a.nm_rek6', DB::raw("SUM(a.nilai) as nilai"), DB::raw("(SELECT SUM(nilai) FROM trdtransout WHERE no_sp2d=c.no_sp2d AND kd_sub_kegiatan=a.kd_sub_kegiatan AND kd_rek6=a.kd_rek6) as transaksi"))->selectRaw("(SELECT f.rupiah FROM trhkasin_pkd e JOIN trdkasin_pkd f ON e.no_sts=f.no_sts AND e.kd_skpd=f.kd_skpd WHERE f.kd_sub_kegiatan=a.kd_sub_kegiatan AND e.no_sp2d=? AND f.kd_rek6=a.kd_rek6) as cp", [$no_sp2d])->where(['c.no_sp2d' => $no_sp2d, 'c.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan])->whereNotIn('a.kd_rek6', $kd_rek6)->groupBy('kd_rek6', 'nm_rek6', 'no_sp2d', 'a.kd_sub_kegiatan');

        //     $data = DB::table(DB::raw("({$data1->toSql()}) AS sub"))
        //         ->select('sub.*', DB::raw("nilai - isnull(transaksi,0) - isnull(cp,0) as sisa"))
        //         ->mergeBindings($data1)
        //         ->get();
        // } else if ($jenis_transaksi == '1') {
        //     $data = DB::table('ms_rek6')->select('kd_rek6', 'nm_rek6')->whereRaw("LEFT(kd_rek6,4)=?", ['1101'])->whereNotIn('kd_rek6', $kd_rek6)->get();
        // }

        if (isset($kd_sub_kegiatan)) {
            $data1 = DB::table('trdspp as a')
                ->join('trhspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->join('trhsp2d as c', function ($join) {
                    $join->on('b.no_spp', '=', 'c.no_spp');
                    $join->on('b.kd_skpd', '=', 'c.kd_skpd');
                })
                ->selectRaw("a.kd_rek6,a.nm_rek6,SUM(a.nilai) as nilai,
					(SELECT sum(nilai) FROM trdtransout WHERE no_sp2d=c.no_sp2d and kd_sub_kegiatan=a.kd_sub_kegiatan and kd_rek6=a.kd_rek6) as transaksi,
					(select sum(f.rupiah) from trhkasin_pkd e join trdkasin_pkd f on e.no_sts=f.no_sts and e.kd_skpd=f.kd_skpd
					where f.kd_sub_kegiatan=a.kd_sub_kegiatan and e.no_sp2d=? and f.kd_rek6=a.kd_rek6) [cp]", [$no_sp2d])
                ->whereRaw("c.no_sp2d =? AND c.kd_skpd =? and a.kd_sub_kegiatan=?", [$no_sp2d, $kd_skpd, $kd_sub_kegiatan])
                ->whereNotIn('a.kd_rek6', $kd_rek6)
                ->groupBy('kd_rek6', 'nm_rek6', 'no_sp2d', 'a.kd_sub_kegiatan');

            $data = DB::table(DB::raw("({$data1->toSql()}) AS sub"))
                ->select('sub.*', DB::raw("nilai - isnull(transaksi,0) - isnull(cp,0) as sisa"))
                ->mergeBindings($data1)
                ->get();
        } else {
            $data = DB::table('ms_rek6')
                ->select('kd_rek6', 'nm_rek6')
                ->whereRaw("LEFT(kd_rek6,4)=?", ['1101'])
                ->whereNotIn('kd_rek6', $kd_rek6)
                ->get();
        }

        return response()->json($data);
    }

    public function cekSimpan(Request $request)
    {
        $no_kas = $request->no_kas;
        $data = DB::table('trhkasin_pkd')->where('no_sts', $no_kas)->count();
        return response()->json($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        $current_date = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            $no_urut = no_urut($kd_skpd);

            DB::table('trhkasin_pkd')->where(['kd_skpd' => $kd_skpd, 'no_sts' => $no_urut])->delete();

            DB::table('trhju_pkd')->where(['kd_skpd' => $kd_skpd, 'no_voucher' => $no_urut])->delete();

            DB::table('trhju')->where(['kd_skpd' => $kd_skpd, 'no_voucher' => $no_urut])->delete();
            // Setor Sisa Kas/CP
            if ($data['jenis_transaksi'] == '5') {
                DB::table('trhkasin_pkd')->insert([
                    'no_kas' => $no_urut,
                    'no_sts' => $no_urut,
                    'kd_skpd' => $data['kd_skpd'],
                    'tgl_sts' => $data['tgl_kas'],
                    'tgl_kas' => $data['tgl_kas'],
                    'keterangan' => $data['uraian'],
                    'total' => $data['jumlah'],
                    'kd_bank' => '',
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'jns_trans' => $data['jenis_transaksi'],
                    'rek_bank' => '',
                    'sumber' => '0',
                    'pot_khusus' => $data['potlain'],
                    'no_sp2d' => $data['no_sp2d'],
                    'jns_cp' => $data['jns_cp'],
                    'bank' => $data['pembayaran'],
                    'username_created' => Auth::user()->nama,
                    'created_at' => $current_date,
                ]);
            } else {
                DB::table('trhkasin_pkd')->insert([
                    'no_kas' => $no_urut,
                    'no_sts' => $no_urut,
                    'kd_skpd' => $data['kd_skpd'],
                    'tgl_sts' => $data['tgl_kas'],
                    'tgl_kas' => $data['tgl_kas'],
                    'keterangan' => $data['uraian'],
                    'total' => $data['jumlah'],
                    'kd_bank' => '',
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'jns_trans' => $data['jenis_transaksi'],
                    'rek_bank' => '',
                    'sumber' => '0',
                    'pot_khusus' => '0',
                    'no_sp2d' => $data['no_sp2d'],
                    'jns_cp' => $data['jns_cp'],
                    'bank' => $data['pembayaran'],
                    'username_created' => Auth::user()->nama,
                    'created_at' => $current_date,
                ]);
            }

            DB::table('trdkasin_pkd')->where(['no_sts' => $no_urut, 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['detail'])) {
                DB::table('trdkasin_pkd')->insert(array_map(function ($value) use ($no_urut, $kd_skpd, $data) {
                    return [
                        'no_sts' => $no_urut,
                        'kd_rek6' => $value['kd_rek6'],
                        'rupiah' => $value['rupiah'],
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                        'kd_skpd' => $kd_skpd,
                        'sumber' => $value['sumber']
                    ];
                }, $data['detail']));
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_kas' => $no_urut
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function edit($no_sts)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_sts = Crypt::decryptString($no_sts);

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'sisa_tunai' => load_sisa_tunai(),
            'setor' => DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
                $join->on('a.no_sts', '=', 'b.no_sts');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.*')->where(['a.no_sts' => $no_sts, 'a.kd_skpd' => $kd_skpd])->first(),
            'data_list' => DB::table('trhkasin_pkd as a')
                ->join('trdkasin_pkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->join('ms_rek6 as c', function ($join) {
                    $join->on('b.kd_rek6', '=', 'c.kd_rek6');
                })
                ->selectRaw('b.*,c.nm_rek6')->where(['a.no_sts' => $no_sts, 'a.kd_skpd' => $kd_skpd])->get(),
        ];

        return view('skpd.setor_sisa_kas.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        $current_date = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {

            DB::table('trhju_pkd')->where(['kd_skpd' => $kd_skpd, 'no_voucher' => $data['no_kas']])->delete();

            DB::table('trhju')->where(['kd_skpd' => $kd_skpd, 'no_voucher' => $data['no_kas']])->delete();
            // Setor Sisa Kas/CP

            DB::table("trhkasin_pkd")->where(['kd_skpd' => $kd_skpd, 'no_sts' => $data['no_kas']])
                ->update([
                    'tgl_sts' => $data['tgl_kas'],
                    'tgl_kas' => $data['tgl_kas'],
                    'keterangan' => $data['uraian'],
                    'total' => $data['jumlah'],
                    'kd_bank' => '',
                    'jns_trans' => $data['jenis_transaksi'],
                    'rek_bank' => '',
                    'sumber' => '0',
                    'pot_khusus' => $data['potlain'],
                    'no_sp2d' => $data['no_sp2d'],
                    'jns_cp' => $data['jns_cp'],
                    'bank' => $data['pembayaran'],
                    'username_updated' => Auth::user()->nama,
                    'updated_at' => $current_date,
                ]);

            DB::table('trdkasin_pkd')->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['detail'])) {
                DB::table('trdkasin_pkd')->insert(array_map(function ($value) use ($kd_skpd, $data) {
                    return [
                        'no_sts' => $data['no_kas'],
                        'kd_rek6' => $value['kd_rek6'],
                        'rupiah' => $value['rupiah'],
                        'kd_sub_kegiatan' => isset($data['kd_sub_kegiatan']) ? $data['kd_sub_kegiatan'] : '',
                        'kd_skpd' => $kd_skpd,
                        'sumber' => $value['sumber']
                    ];
                }, $data['detail']));
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_kas' => $data['no_kas']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapus(Request $request)
    {
        $no_sts = $request->no_sts;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_terima as a')->join('trdkasin_pkd as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_terima', '=', 'b.no_terima');
                $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
            })->where(['a.kd_skpd' => $kd_skpd, 'b.no_sts' => $no_sts])->update([
                'a.kunci' => '0'
            ]);

            DB::table('trhkasin_pkd')->where(['no_sts' => $no_sts, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trdkasin_pkd')->where(['no_sts' => $no_sts, 'kd_skpd' => $kd_skpd])->delete();

            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }
    // Pelimpahan UP Sampai hapusUp

    public function cetakPermintaanLayar(Request $request)
    {
        $kdskpd     = $request->kd_skpd;
        $nmskpd     = $request->nm_skpd;
        $tgl1       = $request->tgl1;
        $tgl2       = $request->tgl2;
        $tglttd     = $request->tgl_ttd;
        $ttdbend    = collect(DB::select('SELECT nip, nama, jabatan, pangkat FROM ms_ttd WHERE kd_skpd = ? AND kode=?', [$kdskpd, 'BK']))->first();
        $ttdpa      = collect(DB::select('SELECT nip, nama, jabatan, pangkat FROM ms_ttd WHERE kd_skpd = ? AND kode = ?', [$kdskpd, 'KPA']))->first();
        $jns_print  = $request->jenis_print;

        $query      = DB::select('SELECT a.tgl_sts,b.no_sts, a.no_sp2d, keterangan,
        SUM ( CASE WHEN jns_trans = 1 AND jns_cp = 3 THEN b.rupiah ELSE 0 END ) AS up_gu,
        SUM ( CASE WHEN jns_trans = 5 AND jns_cp = 1 AND pot_khusus = 2 THEN b.rupiah ELSE 0 END ) AS pot_lain,
        SUM ( CASE WHEN jns_trans = 5 AND jns_cp = 1 AND pot_khusus = 1 THEN b.rupiah ELSE 0 END ) AS hkpg,
        SUM ( CASE WHEN jns_trans = 5 AND jns_cp = 2 THEN b.rupiah ELSE 0 END ) AS cp,
        SUM ( CASE WHEN jns_trans = 1 AND jns_cp = 2 THEN b.rupiah ELSE 0 END ) AS ls
        FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts = b.no_sts AND a.kd_skpd=b.kd_skpd
        WHERE a.kd_skpd=? AND a.tgl_sts BETWEEN ? AND ? AND jns_trans IN (1,5)
        GROUP BY a.tgl_sts, b.no_sts, a.no_sp2d, keterangan', [$kdskpd, $tgl1, $tgl2]);
        // dd($jns_print);
        // return;


        $data = [
            'header'    => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'opd'       => $kdskpd,
            'nmopd'     => $nmskpd,
            'pa'        => $ttdpa,
            'bend'      => $ttdbend,
            'ttd'       => $tglttd,
            'detail'    => $query,
        ];

        $view = view('skpd.setor_sisa_kas.cetak')->with($data);
        if ($jns_print == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } else {
            return $view;
        }
    }
}
