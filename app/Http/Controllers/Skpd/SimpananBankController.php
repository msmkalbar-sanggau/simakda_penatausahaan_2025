<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Crypt;

class SimpananBankController extends Controller
{
    // Ambil Simpanan Kasben (AWAL)
    public function kasben()
    {
        return view('skpd.kasben.index');
    }

    public function loadKasben()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_setorsimpanan')->where(['kd_skpd' => $kd_skpd, 'status_drop' => '1'])->orderBy(DB::raw("CAST(no_kas as INT)"))->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            // if ($row->status_upload != '1') {
            $btn = '<a href="' . route("skpd.simpanan_bank.edit_kasben", Crypt::encryptString($row->no_kas)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusPelimpahan(' . $row->no_kas . ', \'' . $row->kd_link_drop . '\', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            // } else {
            //     $btn = '';
            // }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.kasben.index');
    }

    public function tambahKasben()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        // $kas1 = DB::table('tr_setorpelimpahan_bank')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where(['kd_skpd' => $kd_skpd]);
        // $kas2 = DB::table('tr_setorsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where(['kd_skpd' => $kd_skpd, 'status_drop' => '1'])->unionAll($kas1);
        // $kas3 = DB::table('tr_setorpelimpahan_tunai')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where(['kd_skpd' => $kd_skpd])->where(function ($query) {
        //     $query->where('status_ambil', '0')->orWhereNull('status_ambil');
        // })->unionAll($kas2);

        // $kas = DB::table(DB::raw("({$kas3->toSql()}) AS sub"))
        //     ->select(DB::raw("SUM(CASE WHEN jns=1 THEN jumlah ELSE 0 END)-SUM(CASE WHEN jns=2 THEN jumlah ELSE 0 END) as sisa"))
        //     ->mergeBindings($kas3)
        //     ->first();
        $kas = collect(DB::select("SELECT
            SUM(case when jns=1 then jumlah else 0 end) AS terima,
            SUM(case when jns=2 then jumlah else 0 end) AS keluar
            from (

            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_setorpelimpahan_bank WHERE kd_skpd=?
            union all
            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_setorsimpanan WHERE kd_skpd=? AND status_drop='1'
            UNION ALL
            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_setorpelimpahan_tunai WHERE kd_skpd=?
            ) a
                where  kode=?", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]))->first();

        $kas = $kas->terima - $kas->keluar;


        // $ketdrop1 = DB::table('tr_setorpelimpahan_bank')->select('no_bukti', 'tgl_bukti', 'nilai', 'keterangan', 'kd_skpd_sumber')->where(['kd_skpd' => $kd_skpd])->whereNull('status_ambil');
        // $ketdrop2 = DB::table('tr_setorpelimpahan_tunai')->select('no_bukti', 'tgl_bukti', 'nilai', 'keterangan', 'kd_skpd_sumber')->where(['kd_skpd' => $kd_skpd])->whereNull('status_ambil')->unionAll($ketdrop1);

        // $ketdrop = DB::table(DB::raw("({$ketdrop2->toSql()}) AS sub"))
        //     ->mergeBindings($ketdrop2)
        //     ->get();

        $ketdrop = DB::select("SELECT no_bukti,tgl_bukti,nilai,keterangan,kd_skpd_sumber from tr_setorpelimpahan_bank where kd_skpd=? and
        status_ambil is null
        UNION ALL
        SELECT no_bukti,tgl_bukti,nilai,keterangan,kd_skpd_sumber from tr_setorpelimpahan_tunai where kd_skpd=? and
        status_ambil is null", [$kd_skpd, $kd_skpd]);

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'sisa_bank' => $kas,
            'ketdrop' => $ketdrop

        ];

        return view('skpd.kasben.create')->with($data);
    }

    public function simpanKasben(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = no_urut($kd_skpd);

            // Ambil Simpanan Kasben

            DB::table('tr_setorsimpanan')->insert([
                'no_kas' => $no_urut,
                'tgl_kas' => $data['tgl_kas'],
                'no_bukti' => $no_urut,
                'tgl_bukti' => $data['tgl_kas'],
                'kd_skpd' => $data['kd_skpd'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'jenis' => '1',
                'status_drop' => '1',
                'kd_link_drop' => $data['no_kas_asli'],
            ]);

            DB::table('tr_setorpelimpahan_bank')->where(['no_bukti' => $data['no_kas_asli'], 'kd_skpd' => $kd_skpd])->update([
                'status_ambil' => '1'
            ]);

            DB::table('tr_setorpelimpahan_tunai')->where(['no_bukti' => $data['no_kas_asli'], 'kd_skpd' => $kd_skpd])->update([
                'status_ambil' => '1'
            ]);

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

    public function editKasben($no_kas)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_kas = Crypt::decryptString($no_kas);
        $data_kasben = DB::table('tr_setorsimpanan as a')->join('tr_setorpelimpahan_bank as b', function ($join) {
            $join->on('a.kd_link_drop', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select('a.*', 'b.keterangan as ketdrop', 'b.nilai as ketdana')->where(['a.kd_skpd' => $kd_skpd, 'a.no_kas' => $no_kas])->first();

        $kas1 = DB::table('tr_setorpelimpahan_bank')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where(['kd_skpd' => $kd_skpd]);
        $kas2 = DB::table('tr_setorsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where(['kd_skpd' => $kd_skpd, 'status_drop' => '1'])->unionAll($kas1);
        $kas3 = DB::table('tr_setorpelimpahan_tunai')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where(['kd_skpd' => $kd_skpd])->where(function ($query) {
            $query->where('status_ambil', '0')->orWhereNull('status_ambil');
        })->unionAll($kas2);

        $kas = DB::table(DB::raw("({$kas3->toSql()}) AS sub"))
            ->select(DB::raw("SUM(CASE WHEN jns=1 THEN jumlah ELSE 0 END)-SUM(CASE WHEN jns=2 THEN jumlah ELSE 0 END) as sisa"))
            ->mergeBindings($kas3)
            ->first();

        $data = [
            'data_kasben' => $data_kasben,
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'sisa_bank' => $kas
        ];

        return view('skpd.kasben.edit')->with($data);
    }

    public function updateKasben(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_setorsimpanan')->where(['no_kas' => $data['no_kas'], 'kd_skpd' => $kd_skpd])->update([
                'keterangan' => $data['keterangan'],
                'nilai' => $data['nilai'],
            ]);

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

    public function hapusKasben(Request $request)
    {
        $no_kas = $request->no_kas;
        $no_kas_asli = $request->no_kas_asli;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_setorsimpanan')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('tr_setorpelimpahan_bank')->where(['no_bukti' => $no_kas_asli, 'kd_skpd' => $kd_skpd])->update([
                'status_ambil' => null
            ]);

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
    // Ambil Simpanan Kasben (AKHIR)

    // Ambil Simpanan Tunai (AWAL)
    public function tunai()
    {
        return view('skpd.tunai.index');
    }

    public function loadTunai()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_ambilsimpanan')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            // if ($row->status_upload != '1') {
            $btn = '<a href="' . route("skpd.simpanan_bank.edit_tunai", Crypt::encryptString($row->no_kas)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusSimpanan(' . $row->no_kas . ', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            // } else {
            //     $btn = '';
            // }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.tunai.index');
    }

    public function tambahTunai()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $kas = collect(DB::select("SELECT
        SUM(case when jns=1 then jumlah else 0 end) AS terima,
        SUM(case when jns=2 then jumlah else 0 end) AS keluar
        from (
                                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_setorsimpanan union
                                SELECT '2022-01-01' AS tgl, null AS bku,
            'Saldo Awal' AS ket, sld_awal_bank AS jumlah, '1' as jns, kd_skpd AS kode FROM ms_skpd WHERE kd_skpd = ?
        union
        SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM trhINlain WHERE pay='BANK' union
        select c.tgl_kas [tgl],c.no_kas [bku] ,c.keterangan [ket],c.nilai [jumlah],'1' [jns],c.kd_skpd [kode] from tr_jpanjar c join tr_panjar d on
        c.no_panjar_lalu=d.no_panjar and c.kd_skpd=d.kd_skpd where c.jns='2' and c.kd_skpd=? and  d.pay='BANK' union all
         select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],sum(b.nilai) [jumlah],'1' [jns],a.kd_skpd [kode] from trhtrmpot a
         join trdtrmpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
         where a.kd_skpd=? and a.pay='BANK' and jns_spp not in('1','2','3') group by a.tgl_bukti,a.no_bukti,a.ket,a.kd_skpd
         union all
        select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '1' as jns, a.kd_skpd as kode
        from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
        where jns_trans IN ('5') and bank='BNK' and a.kd_skpd=?
        GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd  union all
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
        SELECT a.tgl_kas AS tgl,a.no_panjar AS bku,a.keterangan as ket,a.nilai-isnull(b.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM tr_panjar a
        left join
        (
            select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
            where e.kd_skpd=? and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
         ) b on a.no_panjar=b.no_kas and a.kd_skpd=b.kd_skpd
        where a.pay='BANK' and a.kd_skpd=?
        union all
        select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
        from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
        where jns_trans NOT IN ('4','2','5') and pot_khusus =0  and bank='BNK' and a.kd_skpd=?
        GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd union all
        select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
        from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
        where jns_trans IN ('5') and bank='BNK' and a.kd_skpd=?
        GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd

        ) a where kode=?",[$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd]))->first();
        $sisa = $kas->terima - $kas->keluar;

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'sisa_bank' => $sisa,
            'daftar_bank' => DB::table('ms_bank')->select('kode', 'nama')->orderBy('kode')->get()
        ];

        return view('skpd.tunai.create')->with($data);
    }

    public function simpanTunai(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = no_urut_tunai($kd_skpd);

            // Ambil Simpanan Tunai

            DB::table('tr_ambilsimpanan')->insert([
                'no_kas' => $no_urut,
                'tgl_kas' => $data['tgl_kas'],
                'no_bukti' => $no_urut,
                'tgl_bukti' => $data['tgl_bukti'],
                'kd_skpd' => $data['kd_skpd'],
                'nilai' => $data['nilai'],
                'bank' => $data['bank'],
                'nm_rekening' => $data['nm_rekening'],
                'keterangan' => $data['keterangan'],
            ]);

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

    public function editTunai($no_kas)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_kas = Crypt::decryptString($no_kas);

        $kas = collect(DB::select("SELECT
        SUM(case when jns=1 then jumlah else 0 end) AS terima,
        SUM(case when jns=2 then jumlah else 0 end) AS keluar
        from (
                                SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_setorsimpanan union
                                SELECT '2022-01-01' AS tgl, null AS bku,
            'Saldo Awal' AS ket, sld_awal_bank AS jumlah, '1' as jns, kd_skpd AS kode FROM ms_skpd WHERE kd_skpd = ?
        union
        SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM trhINlain WHERE pay='BANK' union
        select c.tgl_kas [tgl],c.no_kas [bku] ,c.keterangan [ket],c.nilai [jumlah],'1' [jns],c.kd_skpd [kode] from tr_jpanjar c join tr_panjar d on
        c.no_panjar_lalu=d.no_panjar and c.kd_skpd=d.kd_skpd where c.jns='2' and c.kd_skpd=? and  d.pay='BANK' union all
         select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],sum(b.nilai) [jumlah],'1' [jns],a.kd_skpd [kode] from trhtrmpot a
         join trdtrmpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
         where a.kd_skpd=? and a.pay='BANK' and jns_spp not in('1','2','3') group by a.tgl_bukti,a.no_bukti,a.ket,a.kd_skpd
         union all
        select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '1' as jns, a.kd_skpd as kode
        from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
        where jns_trans IN ('5') and bank='BNK' and a.kd_skpd=?
        GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd  union all
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
        SELECT a.tgl_kas AS tgl,a.no_panjar AS bku,a.keterangan as ket,a.nilai-isnull(b.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM tr_panjar a
        left join
        (
            select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
            where e.kd_skpd=? and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
         ) b on a.no_panjar=b.no_kas and a.kd_skpd=b.kd_skpd
        where a.pay='BANK' and a.kd_skpd=?
        union all
        select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
        from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
        where jns_trans NOT IN ('4','2','5') and pot_khusus =0  and bank='BNK' and a.kd_skpd=?
        GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd union all
        select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
        from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
        where jns_trans IN ('5') and bank='BNK' and a.kd_skpd=?
        GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd

        ) a where kode=?",[$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd]))->first();
        $sisa = $kas->terima - $kas->keluar;

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'sisa_bank' => $sisa,
            'daftar_bank' => DB::table('ms_bank')->select('kode', 'nama')->orderBy('kode')->get(),
            'tunai' => DB::table('tr_ambilsimpanan')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->first()
        ];
        // /dd($data['tunai']);
        return view('skpd.tunai.edit')->with($data);
    }

    public function updateTunai(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // Ambil Simpanan Tunai

            DB::table('tr_ambilsimpanan')->where(['no_kas' => $data['no_kas'], 'kd_skpd' => $data['kd_skpd']])->update([
                'tgl_kas' => $data['tgl_kas'],
                'tgl_bukti' => $data['tgl_bukti'],
                'nilai' => $data['nilai'],
                'bank' => $data['bank'],
                'nm_rekening' => $data['nm_rekening'],
                'keterangan' => $data['keterangan'],
            ]);

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

    public function hapusTunai(Request $request)
    {
        $no_kas = $request->no_kas;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_ambilsimpanan')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->delete();

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
    // Ambil Simpanan Tunai (AKHIR)

    // Setor Simpanan (AWAL)
    public function setor()
    {
        return view('skpd.setor_simpanan.index');
    }

    public function loadSetor()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_setorsimpanan')->where(['kd_skpd' => $kd_skpd])->orderBy(DB::raw("CAST(no_kas as int)"))->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.simpanan_bank.edit_setor", Crypt::encryptString($row->no_kas)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusSimpanan(' . $row->no_kas . ', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.setor_simpanan.index');
    }

    public function tambahSetor()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $kas = collect(DB::select("SELECT SUM(a.masuk) as terima,
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

                ) a where kode=?",[$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd,$kd_skpd]))->first();
        $sisa = $kas->terima - $kas->keluar;

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'sisa_tunai' => $sisa,
            'daftar_bank' => DB::table('ms_bank')->select('kode', 'nama')->orderBy('kode')->get()
        ];

        return view('skpd.setor_simpanan.create')->with($data);
    }

    public function simpanSetor(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = no_urut($kd_skpd);

            // Setor Simpanan

            DB::table('tr_setorsimpanan')->insert([
                'no_kas' => $no_urut,
                'tgl_kas' => $data['tgl_kas'],
                'no_bukti' => $no_urut,
                'tgl_bukti' => $data['tgl_bukti'],
                'kd_skpd' => $data['kd_skpd'],
                'nilai' => $data['nilai'],
                'bank' => $data['bank'],
                'nm_rekening' => $data['nm_rekening'],
                'keterangan' => $data['keterangan'],
                'jenis' => 2,
            ]);

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

    public function editSetor($no_kas)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_kas = Crypt::decryptString($no_kas);
        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'sisa_bank' => sisa_bank(),
            'daftar_bank' => DB::table('ms_bank')->select('kode', 'nama')->orderBy('kode')->get(),
            'setor' => DB::table('tr_setorsimpanan')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->first()
        ];

        return view('skpd.setor_simpanan.edit')->with($data);
    }

    public function updateSetor(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // Setor Simpanan

            DB::table('tr_setorsimpanan')->where(['no_kas' => $data['no_kas'], 'kd_skpd' => $data['kd_skpd']])->update([
                'tgl_kas' => $data['tgl_kas'],
                'tgl_bukti' => $data['tgl_bukti'],
                'bank' => $data['bank'],
                'nilai' => $data['nilai'],
                'nm_rekening' => $data['nm_rekening'],
                'keterangan' => $data['keterangan'],
            ]);

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

    public function hapusSetor(Request $request)
    {
        $no_kas = $request->no_kas;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_setorsimpanan')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->delete();

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
    // Setor Simpanan (AKHIR)
}
