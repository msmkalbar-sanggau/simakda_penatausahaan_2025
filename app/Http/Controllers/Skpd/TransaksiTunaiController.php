<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransaksiTunaiController extends Controller
{
    public function index()
    {
        return view('skpd.transaksi_tunai.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('trhtransout as a')->select('a.*', DB::raw("'' as nokas_pot"), DB::raw("'' as tgl_pot"), DB::raw("'' as kete"), DB::raw("(SELECT COUNT(*) FROM trlpj z JOIN trhlpj v ON v.no_lpj=z.no_lpj WHERE v.jenis=a.jns_spp AND z.no_bukti=a.no_bukti AND z.kd_bp_skpd=a.kd_skpd) as ketlpj"), DB::raw("CASE WHEN a.tgl_bukti<'2018-01-01' THEN 1 ELSE 0 END as ketspj"))->where(['a.panjar' => '0', 'a.kd_skpd' => $kd_skpd, 'a.pay' => 'TUNAI'])->orderBy(DB::raw("CAST(a.no_bukti as numeric)"))->orderBy('a.kd_skpd')->get();
        // dd ($data);
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.transaksi_tunai.edit", Crypt::encryptString($row->no_bukti)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->ketlpj != 1) {
                $btn .= '<a href="javascript:void(0);" onclick="hapusTransaksi(' . $row->no_bukti . ');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.transaksi_tunai.index');
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_urut2 = collect(DB::select("SELECT case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor from (
            select no_kas nomor,'Pencairan SP2D' ket,kd_skpd from trhsp2d where isnumeric(no_kas)=1 and status=1 union ALL
            select no_terima nomor,'Penerimaan SP2D' ket,kd_skpd from trhsp2d where isnumeric(no_terima)=1 and status_terima=1 union ALL
            select no_bukti nomor, 'Pembayaran Transaksi' ket, kd_skpd from trhtransout where  isnumeric(no_bukti)=1 AND (panjar !='3' OR panjar IS NULL) union ALL
            select no_bukti nomor, 'Koreksi Transaksi' ket, kd_skpd from trhtransout where  isnumeric(no_bukti)=1 AND panjar ='3' union ALL
            select no_panjar nomor, 'Pemberian Panjar CMS' ket,kd_skpd from tr_panjar_cmsbank where  isnumeric(no_panjar)=1  union ALL
            select no_kas nomor, 'Pertanggungjawaban Panjar' ket, kd_skpd from tr_jpanjar where  isnumeric(no_kas)=1 union ALL
            select no_bukti nomor, 'Penerimaan Potongan' ket,kd_skpd from trhtrmpot where  isnumeric(no_bukti)=1  union ALL
            select no_bukti nomor, 'Penyetoran Potongan' ket,kd_skpd from trhstrpot where  isnumeric(no_bukti)=1 union ALL
            select no_sts+1 nomor, 'Setor Sisa Kas' ket,kd_skpd from trhkasin_pkd where  isnumeric(no_sts)=1 and jns_trans<>4 union ALL
            select no_sts+1 nomor, 'Setor Sisa Kas' ket,kd_skpd from trhkasin_pkd where  isnumeric(no_sts)=1 and jns_trans<>4 and pot_khusus=1 union ALL
            select no_bukti+1 nomor, 'Ambil Simpanan' ket,kd_skpd from tr_ambilsimpanan where  isnumeric(no_bukti)=1 AND status_drop !='1' union ALL
            select no_bukti nomor, 'Ambil Drop Dana' ket,kd_skpd from tr_ambilsimpanan where  isnumeric(no_bukti)=1 AND status_drop ='1' union ALL
            select no_kas nomor, 'Setor Simpanan' ket,kd_skpd from tr_setorsimpanan where  isnumeric(no_bukti)=1 union all
            select no_kas nomor, 'Setor Simpanan CMS' ket,kd_skpd_sumber kd_skpd from tr_setorpelimpahan_bank_cms where  isnumeric(no_bukti)=1 union all
            select no_kas+1 nomor, 'Setor Simpanan' ket,kd_skpd from tr_setorsimpanan where  isnumeric(no_bukti)=1 and jenis='2' union ALL
            select no_kas+1 nomor, 'Setor Simpanan' ket,kd_skpd from tr_setorsimpanan where  isnumeric(no_bukti)=1 and jenis='3' union ALL
            select NO_BUKTI nomor, 'Terima lain-lain' ket,KD_SKPD as kd_skpd from TRHINLAIN where  isnumeric(NO_BUKTI)=1 union ALL
            select NO_BUKTI nomor, 'Keluar lain-lain' ket,KD_SKPD as kd_skpd from TRHOUTLAIN where  isnumeric(NO_BUKTI)=1 union ALL
            select no_kas nomor, 'Drop Uang ke Bidang' ket,kd_skpd_sumber as kd_skpd from tr_setorpelimpahan where  isnumeric(no_kas)=1) z WHERE KD_SKPD = ?", [$kd_skpd]))->first();
            //dd($no_urut2);

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_kegiatan' => DB::table('trdrka as a')->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan', DB::raw("SUM(a.nilai) as total"))->where(['a.kd_skpd' => $kd_skpd])->whereRaw("left(a.kd_rek6,1)=?", ['5'])->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->orderBy('a.kd_sub_kegiatan')->orderBy('a.nm_sub_kegiatan')->get(),
            'persen' => DB::table('config_app')->select('persen_kkpd', 'persen_tunai')->first(),
            'no_urut3'   => $no_urut2,
        ];
        return view('skpd.transaksi_tunai.create')->with($data);
    }

    public function nomorSp2d(Request $request)
    {
        $beban = $request->beban;
        $kd_skpd = $request->kd_skpd;
        $kode = substr($kd_skpd, 0, 17);
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        if ((isset($beban) && empty($kd_sub_kegiatan)) || ($beban == '1')) {
            $where = "a.jns_spp IN ('1','2')";
        }
        if (isset($kd_sub_kegiatan) && $beban != '1') {
            $where = "a.jns_spp=? AND b.kd_sub_kegiatan =?";
        }

        $data = DB::table('trhsp2d as a')->join('trdspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select('a.no_sp2d', 'a.tgl_sp2d', DB::raw("SUM(a.nilai) as nilai"))->whereRaw("LEFT(a.kd_skpd,17)=LEFT(?,17)", [$kd_skpd])->where(['a.status' => '1'])->whereRaw($where, [$beban, $kd_sub_kegiatan])->groupBy('a.no_sp2d', 'a.tgl_sp2d')->orderByDesc('a.tgl_sp2d')->orderBy('a.no_sp2d')->distinct()->get();
        return response()->json($data);
    }

    public function cariRekening(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_bukti = $request->no_bukti;
        $beban = $request->beban;
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = $request->kd_skpd;
        $jenis_ang = status_anggaran();
        $kd_rek6 = $request->kd_rek6;
        // dd ($kd_rek6);
        // if ($kd_rek6 !=''){
        //     $notIn = " and kd_rek6 not in ($kd_rek6) " ;
        // }else{
        //     $notIn  = "";
        // }
        // dd($kd_sub_kegiatan);
        $notIn  = "";

        // if ($beban == '1') {
        //     if ($kd_sub_kegiatan == '1.01.1.01.01.00.22.002') {
        //         $data = DB::table('trdrka as a')->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.status_aktif' => '1', 'a.kd_rek6' => '5221104'])->select('a.kd_rek6', 'a.nm_rek6', DB::raw("'0' as sp2d"), 'nilai as anggaran')->selectRaw("(SELECT SUM( nilai ) FROM(SELECT SUM( c.nilai ) AS nilai FROM trdtransout c LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti  AND c.kd_skpd = d.kd_skpd WHERE c.kd_sub_kegiatan = a.kd_sub_kegiatan  AND d.kd_skpd = a.kd_skpd  AND c.kd_rek6 = a.kd_rek6  AND d.jns_spp= ? AND c.no_voucher <> ? AND d.status_validasi = '0' UNION ALL SELECT SUM(c.nilai) as nilai FROM trdtransout c LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti AND c.kd_skpd = d.kd_skpd WHERE c.kd_sub_kegiatan = a.kd_sub_kegiatan AND d.kd_skpd=a.kd_skpd AND c.kd_rek6 = a.kd_rek6 AND d.jns_spp=? UNION ALL SELECT SUM(x.nilai) as nilai FROM trdspp x INNER JOIN trhspp y ON x.no_spp= y.no_spp AND x.kd_skpd= y.kd_skpd WHERE x.kd_sub_kegiatan = a.kd_sub_kegiatan AND x.kd_skpd=a.kd_skpd AND x.kd_rek6 = a.kd_rek6 AND y.jns_spp IN ( '3', '4', '5', '6' ) AND ( sp2d_batal IS NULL OR sp2d_batal = '' OR sp2d_batal = '0') UNION ALL SELECT SUM( nilai ) AS nilai FROM trdtagih t INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti AND t.kd_skpd= u.kd_skpd WHERE t.kd_sub_kegiatan = a.kd_sub_kegiatan AND u.kd_skpd = a.kd_skpd AND t.kd_rek = a.kd_rek6 AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd=? )r) AS lalu", [$beban, $no_bukti, $no_bukti, $beban, $kd_skpd])->selectRaw("(SELECT SUM ( nilai ) FROM trdrka WHERE no_trdrka = a.no_trdrka AND jns_ang =?) as nilai_ubah", [$jenis_ang])->distinct()->get();
        //     } elseif ($kd_sub_kegiatan == '4.08.4.08.01.00.01.351') {
        //         $data = DB::table('trdrka as a')->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.status_aktif' => '1'])->select('a.kd_rek6', 'a.nm_rek6', DB::raw("'0' as sp2d"), 'nilai as anggaran')->selectRaw("(SELECT SUM( nilai ) FROM(SELECT SUM(c.nilai) AS nilai FROM trdtransout_cmsbank c LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher AND c.kd_skpd = d.kd_skpd WHERE c.kd_sub_kegiatan = a.kd_sub_kegiatan AND d.kd_skpd=a.kd_skpd AND c.kd_rek6 = a.kd_rek6 AND c.no_voucher <> ? AND d.jns_spp=? AND d.status_validasi= '0' UNION ALL SELECT SUM(c.nilai) AS nilai FROM trdtransout c LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti AND c.kd_skpd = d.kd_skpd WHERE c.kd_sub_kegiatan = a.kd_sub_kegiatan AND d.kd_skpd=a.kd_skpd AND c.kd_rek6 = a.kd_rek6 AND d.jns_spp=? UNION ALL SELECT SUM(x.nilai) AS nilai FROM trdspp x INNER JOIN trhspp y ON x.no_spp= y.no_spp AND x.kd_skpd= y.kd_skpd WHERE x.kd_sub_kegiatan = a.kd_sub_kegiatan AND x.kd_skpd=a.kd_skpd AND x.kd_rek6 = a.kd_rek6 AND y.jns_spp IN ( '3', '4', '5', '6' ) AND ( sp2d_batal IS NULL OR sp2d_batal = '' OR sp2d_batal = '0') UNION ALL SELECT SUM(nilai) AS nilai FROM trdtagih t INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti AND t.kd_skpd= u.kd_skpd WHERE t.kd_sub_kegiatan = a.kd_sub_kegiatan AND u.kd_skpd = a.kd_skpd AND t.kd_rek = a.kd_rek6 AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd =? ))r) AS lalu", [$no_bukti, $beban, $beban, $kd_skpd])->selectRaw("(SELECT SUM ( nilai ) FROM trdrka WHERE no_trdrka = a.no_trdrka AND jns_ang =?) as nilai_ubah", [$jenis_ang])->distinct()->get();
        //     } else {
        //         $data = DB::table('trdrka as a')->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.status_aktif' => '1'])->select('a.kd_rek6', 'a.nm_rek6', DB::raw("'0' as sp2d"), 'nilai as anggaran')->selectRaw("(SELECT SUM( nilai ) FROM(SELECT SUM(c.nilai) AS nilai FROM trdtransout_cmsbank c LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher AND c.kd_skpd = d.kd_skpd WHERE c.kd_sub_kegiatan = a.kd_sub_kegiatan AND d.kd_skpd=a.kd_skpd AND c.kd_rek6 = a.kd_rek6 AND c.no_voucher <> ? AND d.jns_spp=? AND d.status_validasi= '0' UNION ALL SELECT SUM(c.nilai) AS nilai FROM trdtransout c LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti AND c.kd_skpd = d.kd_skpd WHERE c.kd_sub_kegiatan = a.kd_sub_kegiatan AND d.kd_skpd= a.kd_skpd AND c.kd_rek6 = a.kd_rek6 AND d.jns_spp=? UNION ALL SELECT SUM(x.nilai) AS nilai FROM trdspp x INNER JOIN trhspp y ON x.no_spp= y.no_spp AND x.kd_skpd= y.kd_skpd WHERE x.kd_sub_kegiatan = a.kd_sub_kegiatan AND x.kd_skpd = a.kd_skpd AND x.kd_rek6 = a.kd_rek6 AND y.jns_spp IN ( '3', '4', '5', '6' ) AND ( sp2d_batal IS NULL OR sp2d_batal = '' OR sp2d_batal = '0' ) UNION ALL SELECT SUM(nilai) AS nilai FROM trdtagih t INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti AND t.kd_skpd= u.kd_skpd WHERE t.kd_sub_kegiatan = a.kd_sub_kegiatan AND u.kd_skpd = a.kd_skpd AND t.kd_rek = a.kd_rek6 AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd =? ))r) AS lalu", [$no_bukti, $beban, $beban, $kd_skpd])->selectRaw("(SELECT SUM ( nilai ) FROM trdrka WHERE no_trdrka = a.no_trdrka AND jns_ang =?) as nilai_ubah", [$jenis_ang])->distinct()->get();
        //     }
        // } else {
        //     $data = DB::table('trhspp as a')->join('trdspp as b', function ($join) {
        //         $join->on('a.no_spp', '=', 'b.no_spp');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })->join('trhspm as c', function ($join) {
        //         $join->on('b.no_spp', '=', 'c.no_spp');
        //         $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        //     })->join('trhsp2d as d', function ($join) {
        //         $join->on('c.no_spm', '=', 'd.no_spm');
        //         $join->on('c.kd_skpd', '=', 'd.kd_skpd');
        //     })->join('trdrka as f', function ($join) {
        //         $join->on('b.kd_bidang', '=', 'f.kd_skpd');
        //         $join->on('b.kd_sub_kegiatan', '=', 'f.kd_sub_kegiatan');
        //         $join->on('b.kd_rek6', '=', 'f.kd_rek6');
        //     })->where(['d.no_sp2d' => $no_sp2d, 'b.kd_sub_kegiatan' => $kd_sub_kegiatan, 'f.status_aktif' => '1'])->select('b.kd_rek6', 'b.nm_rek6', DB::raw("'0' as anggaran"), DB::raw("'0' as nilai_ubah"), 'b.nilai as sp2d')->selectRaw("(SELECT SUM(c.nilai) FROM trdtransout_cmsbank c LEFT JOIN trhtransout_cmsbank d ON c.no_voucher= d.no_voucher AND c.kd_skpd= d.kd_skpd WHERE c.kd_sub_kegiatan = b.kd_sub_kegiatan AND d.kd_skpd= a.kd_skpd AND c.kd_rek6= b.kd_rek6 AND c.no_voucher <> ? AND d.jns_spp = ? AND c.no_sp2d = ?) AS lalu", [$no_bukti, $beban, $no_sp2d])->distinct()->get();
        // }

        if ($beban == '1') {
            if ($kd_sub_kegiatan == '1.01.1.01.01.00.22.002') {
                $data = DB::select("SELECT a.kd_rek6,a.nm_rek6,
                (SELECT SUM(nilai) FROM
                    (SELECT
                        SUM (c.nilai) as nilai
                    FROM
                        trdtransout_cmsbank c
                    LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher
                    AND c.kd_skpd = d.kd_skpd
                    WHERE
                    c.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND left(d.kd_skpd,22) = left(a.kd_skpd,22)
                    AND c.kd_rek6 = a.kd_rek6
                    AND c.no_voucher <> ?
                    AND d.jns_spp=? AND d.status_validasi='0'
                    UNION ALL
                    SELECT
                        SUM (c.nilai) as nilai
                    FROM
                        trdtransout c
                    LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
                    AND c.kd_skpd = d.kd_skpd
                    WHERE
                    c.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND left(d.kd_skpd,22) = left(a.kd_skpd,22)
                    AND c.kd_rek6 = a.kd_rek6 AND d.jns_spp=?
                    UNION ALL
                    SELECT SUM(x.nilai) as nilai FROM trdspp x
                    INNER JOIN trhspp y
                    ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                    WHERE
                    x.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND left(x.kd_skpd,22) = left(a.kd_skpd,22)
                    AND x.kd_rek6 = a.kd_rek6
                    AND y.jns_spp IN ('3','4','5','6')
                    AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
                    UNION ALL
                    SELECT SUM(nilai) as nilai FROM trdtagih t
                    INNER JOIN trhtagih u
                    ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                    WHERE
                    t.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND u.kd_skpd = a.kd_skpd
                    AND t.kd_rek = a.kd_rek6
                    AND u.no_bukti
                    NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=?)
                    )r) AS lalu,
                    0 AS sp2d,nilai AS anggaran FROM trdrka a WHERE a.kd_sub_kegiatan= ? AND a.jns_ang = ? AND a.kd_rek6 in ('5221104') AND a.kd_skpd = ? ", [$no_bukti,$beban,$beban,$kd_skpd, $kd_sub_kegiatan, $jenis_ang, $kd_skpd]);
            }elseif ($kd_sub_kegiatan == '4.08.4.08.01.00.01.351') {
                $data = DB::select("SELECT a.kd_rek6,a.nm_rek6,
                (SELECT SUM(nilai) FROM
                    (SELECT
                        SUM (c.nilai) as nilai
                    FROM
                        trdtransout_cmsbank c
                    LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher
                    AND c.kd_skpd = d.kd_skpd
                    WHERE
                    c.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND left(d.kd_skpd,22) = left(a.kd_skpd,22)
                    AND c.kd_rek6 = a.kd_rek6
                    AND c.no_voucher <> ?
                    AND d.jns_spp=? AND d.status_validasi='0'
                    UNION ALL
                    SELECT
                        SUM (c.nilai) as nilai
                    FROM
                        trdtransout c
                    LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
                    AND c.kd_skpd = d.kd_skpd
                    WHERE
                    c.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND left(d.kd_skpd,22) = left(a.kd_skpd,22)
                    AND c.kd_rek6 = a.kd_rek6 AND d.jns_spp=?
                    UNION ALL
                    SELECT SUM(x.nilai) as nilai FROM trdspp x
                    INNER JOIN trhspp y
                    ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                    WHERE
                    x.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND left(x.kd_skpd,22) = left(a.kd_skpd,22)
                    AND x.kd_rek6 = a.kd_rek6
                    AND y.jns_spp IN ('3','4','5','6')
                    AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
                    UNION ALL
                    SELECT SUM(nilai) as nilai FROM trdtagih t
                    INNER JOIN trhtagih u
                    ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                    WHERE
                    t.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND u.kd_skpd = a.kd_skpd
                    AND t.kd_rek = a.kd_rek6
                    AND u.no_bukti
                    NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=? )
                    )r) AS lalu,
                    0 AS sp2d,nilai AS anggaran
                    FROM trdrka a WHERE a.kd_sub_kegiatan= ? AND a.jns_ang = ? AND a.kd_skpd = ? ", [$no_bukti, $beban, $beban, $kd_skpd, $kd_sub_kegiatan, $jenis_ang, $kd_skpd]);
            }else {
                $data = DB::select("SELECT a.kd_rek6,a.nm_rek6,
                    (SELECT SUM(nilai) FROM
                    (SELECT
                        SUM (c.nilai) as nilai
                    FROM
                        trdtransout_cmsbank c
                    LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher
                    AND c.kd_skpd = d.kd_skpd
                    WHERE
                    c.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND left(d.kd_skpd,22) = left(a.kd_skpd,22)
                    AND c.kd_rek6 = a.kd_rek6
                    AND c.no_voucher <> ?
                    AND d.jns_spp=? AND d.status_validasi='0'
                    UNION ALL
                    SELECT
                        SUM (c.nilai) as nilai
                    FROM
                        trdtransout c
                    LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
                    AND c.kd_skpd = d.kd_skpd
                    WHERE
                    c.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND left(d.kd_skpd,22) = left(a.kd_skpd,22)
                    AND c.kd_rek6 = a.kd_rek6 AND d.jns_spp=?
                    UNION ALL
                    SELECT SUM(x.nilai) as nilai FROM trdspp x
                    INNER JOIN trhspp y
                    ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                    WHERE
                    x.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND left(x.kd_skpd,22) = left(a.kd_skpd,22)
                    AND x.kd_rek6 = a.kd_rek6
                    AND y.jns_spp IN ('3','4','5','6')
                    AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
                    UNION ALL
                    SELECT SUM(nilai) as nilai FROM trdtagih t
                    INNER JOIN trhtagih u
                    ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                    WHERE
                    t.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND u.kd_skpd = a.kd_skpd
                    AND t.kd_rek = a.kd_rek6
                    AND u.no_bukti
                    NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=? )
                    )r) AS lalu,
                    0 AS sp2d,nilai AS anggaran
                    FROM trdrka a WHERE a.kd_sub_kegiatan= ? AND a.jns_ang = ?
                    AND a.kd_skpd = ?
                    AND a.kd_rek6 not in (select kd_rek6 from ms_rek6 where kd_rek6 = ?)
                    ", [$no_bukti, $beban, $beban, $kd_skpd, $kd_sub_kegiatan, $jenis_ang, $kd_skpd, $kd_rek6]);


            }
        }else {
            $data = DB::select("SELECT b.kd_rek6,b.nm_rek6,
            (SELECT SUM(c.nilai) FROM trdtransout_cmsbank c LEFT JOIN trhtransout_cmsbank d ON c.no_voucher=d.no_voucher AND c.kd_skpd=d.kd_skpd
            WHERE c.kd_sub_kegiatan = b.kd_sub_kegiatan AND
            d.kd_skpd=a.kd_skpd
            AND c.kd_rek6=b.kd_rek6 AND c.no_voucher <> ? AND d.jns_spp = ? and c.no_sp2d = ?) AS lalu,
            b.nilai AS sp2d,
            0 AS anggaran
            FROM trhspp a INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
            INNER JOIN trhspm c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
            INNER JOIN trhsp2d d ON c.no_spm=d.no_Spm AND c.kd_skpd=d.kd_skpd
            WHERE d.no_sp2d = ? and b.kd_sub_kegiatan=? ", [$no_bukti, $beban, $no_sp2d, $no_sp2d, $kd_sub_kegiatan]);
        }


        return response()->json($data);
    }

    public function cariSumber(Request $request)
    {
        $kd_rek6 = $request->kd_rek6;
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_sp2d = $request->no_sp2d;
        $beban = $request->beban;

        $jenis_ang = status_anggaran();

        $data1 = DB::table('trdrka as a')->select('sumber1 as sumber_dana', DB::raw("ISNULL(nsumber1,0) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_rek6' => $kd_rek6, 'a.kd_skpd' => $kd_skpd, 'a.jns_ang' => $jenis_ang]);

        $data2 = DB::table('trdrka as a')->select('sumber2 as sumber_dana', DB::raw("ISNULL(nsumber2,0) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_rek6' => $kd_rek6, 'a.kd_skpd' => $kd_skpd, 'a.jns_ang' => $jenis_ang])->where('a.nsumber2', '<>', '0')->unionAll($data1);

        $data3 = DB::table('trdrka as a')->select('sumber3 as sumber_dana', DB::raw("ISNULL(nsumber3,0) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_rek6' => $kd_rek6, 'a.kd_skpd' => $kd_skpd, 'a.jns_ang' => $jenis_ang])->where('a.nsumber3', '<>', '0')->unionAll($data2);

        $data4 = DB::table('trdrka as a')->select('sumber4 as sumber_dana', DB::raw("ISNULL(nsumber4,0) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_rek6' => $kd_rek6, 'a.kd_skpd' => $kd_skpd, 'a.jns_ang' => $jenis_ang])->where('a.nsumber4', '<>', '0')->unionAll($data3);

        $data = DB::table(DB::raw("({$data4->toSql()}) AS sub"))
            ->mergeBindings($data4)
            ->get();

        return response()->json($data);
    }

    public function sisaTunai(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        // $data1 = DB::table('tr_ambilsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where(['kd_skpd' => $kd_skpd]);

        // $data2 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
        //     $join->on('a.no_sts', '=', 'b.no_sts');
        //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        // })->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("sum(b.rupiah) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->where(['a.kd_skpd' => $kd_skpd, 'bank' => 'TN'])->whereIn('pot_khusus', ['0', '2'])->whereNotIn('jns_trans', ['2', '4', '5'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->unionAll($data1);

        // $join1 = DB::table('trspmpot')->select('no_spm', DB::raw("SUM(nilai) as pot"))->groupBy('no_spm');

        // $data3 = DB::table('trhtransout as a')->join('trdtransout as b', function ($join) {
        //     $join->on('a.no_bukti', '=', 'b.no_bukti');
        //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        // })->leftJoin('trhsp2d as c', function ($join) {
        //     $join->on('b.no_sp2d', '=', 'c.no_sp2d');
        // })->leftJoinSub($join1, 'd', function ($join) {
        //     $join->on('c.no_spm', '=', 'd.no_spm');
        // })->select('a.tgl_bukti as tgl', 'a.no_bukti as bku', 'a.ket as ket', DB::raw("sum(b.nilai - isnull(pot,0)) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->where(['a.kd_skpd' => $kd_skpd, 'pay' => 'TUNAI'])->where('panjar', '<>', '1')->whereRaw("a.no_bukti NOT IN (SELECT no_bukti FROM trhtransout WHERE no_sp2d IN ( SELECT no_sp2d AS no_bukti FROM trhtransout WHERE kd_skpd=? GROUP BY no_sp2d HAVING COUNT ( no_sp2d ) > 1 ) AND no_kas NOT IN (SELECT MIN( z.no_kas ) AS no_bukti FROM trhtransout z WHERE z.jns_spp IN ( 4, 5, 6 ) AND kd_skpd=? GROUP BY z.no_sp2d HAVING COUNT ( z.no_sp2d ) > 1) AND jns_spp IN ( 4, 5, 6 ) AND kd_skpd=?)", [$kd_skpd, $kd_skpd, $kd_skpd])->groupBy('a.tgl_bukti', 'a.no_bukti', 'a.ket', 'a.no_sp2d', 'b.no_sp2d', 'a.total', 'pot', 'a.kd_skpd')->unionAll($data2);

        // $data4 = DB::table('trhtransout')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket as ket', DB::raw("isnull(total,0) as jumlah"), DB::raw("'2' as jns"), 'kd_skpd as kode')->where(['kd_skpd' => $kd_skpd, 'pay' => 'TUNAI'])->whereIn('jns_spp', ['4', '5', '6'])->where('panjar', '<>', '1')->whereRaw("no_sp2d IN (SELECT no_sp2d AS no_bukti FROM trhtransout WHERE kd_skpd=? GROUP BY no_sp2d HAVING COUNT ( no_sp2d ) > 1)", [$kd_skpd])->whereRaw("no_kas NOT IN(SELECT MIN( z.no_kas ) AS no_bukti FROM trhtransout z WHERE z.jns_spp IN ( 4, 5, 6 ) AND kd_skpd=? GROUP BY z.no_sp2d HAVING COUNT ( z.no_sp2d ) > 1)", [$kd_skpd])->unionAll($data3);

        // $data5 = DB::table('tr_setorsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where(['kd_skpd' => $kd_skpd, 'jenis' => '2'])->unionAll($data4);

        // $data6 = DB::table('tr_setorpelimpahan')->select('tgl_bukti as tgl', 'no_bukti as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd_sumber as kode')->where(['kd_skpd_sumber' => $kd_skpd])->unionAll($data5);

        // $data = DB::table(DB::raw("({$data6->toSql()}) AS sub"))
        //     ->select(DB::raw("(CASE WHEN jns=1 THEN jumlah ELSE 0 END) as terima"), DB::raw("(CASE WHEN jns=2 THEN jumlah ELSE 0 END) as keluar"))
        //     ->mergeBindings($data6)
        //     ->first();

        $skpdbp = explode('.',$kd_skpd);



        if($skpdbp[7]=='0000'){
                $init_skpd = "a.kd_skpd='$kd_skpd'";
                $init_skpd2 = "kd_skpd='$kd_skpd'";
                $init_skpd3 = "kd_skpd_sumber='$kd_skpd'";
                $init_skpd4 = "kode='$kd_skpd'";

        }else{
            $init_skpd = "left(a.kd_skpd,22)=left('$kd_skpd',22)";
            $init_skpd2 = "left(kd_skpd,22)=left('$kd_skpd',22)";
            $init_skpd3 = "left(kd_skpd_sumber,22)=left('$kd_skpd',22)";
            $init_skpd4 = "left(kode,22)=left('$kd_skpd',22)";
        }

        $data = collect(DB::select("SELECT
        SUM(case when jns=1 then jumlah else 0 end ) AS terima,
        SUM(case when jns=2 then jumlah else 0 end) AS keluar
        FROM (
        SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_ambilsimpanan WHERE $init_skpd2 UNION ALL
        select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
            from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
            where jns_trans NOT IN ('4','2') and pot_khusus =0  and bank='TNK' and jns_cp not in ('2')  AND $init_skpd
            GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
        UNION ALL
        SELECT  a.tgl_bukti AS tgl, a.no_bukti AS bku, a.ket AS ket, SUM(z.nilai) - isnull(pot, 0) AS jumlah, '2' AS jns, a.kd_skpd AS kode
                        FROM trhtransout a INNER JOIN trdtransout z ON a.no_bukti=z.no_bukti AND a.kd_skpd=z.kd_skpd
                        LEFT JOIN trhsp2d b ON z.no_sp2d = b.no_sp2d
                        LEFT JOIN (SELECT no_spm, SUM (nilai) pot   FROM trspmpot GROUP BY no_spm) c
                        ON b.no_spm = c.no_spm WHERE pay = 'TUNAI' AND panjar <> 1
                        AND $init_skpd
                        AND a.no_bukti NOT IN(
                        select no_bukti from trhtransout
                        where no_sp2d in
                        (SELECT no_sp2d as no_bukti FROM trhtransout where $init_skpd2 GROUP BY no_sp2d HAVING COUNT(no_sp2d)>1)
                         and  no_kas not in
                        (SELECT min(z.no_kas) as no_bukti FROM trhtransout z WHERE z.jns_spp in (4,5,6) and $init_skpd2

                        GROUP BY z.no_sp2d HAVING COUNT(z.no_sp2d)>1)
                        and jns_spp in (4,5,6) and $init_skpd2)
                        GROUP BY a.tgl_bukti,a.no_bukti,a.ket,a.no_sp2d,z.no_sp2d,a.total,pot,a.kd_skpd
                UNION ALL
        SELECT  tgl_bukti AS tgl,   no_bukti AS bku, ket AS ket,  isnull(total, 0) AS jumlah, '2' AS jns, kd_skpd AS kode
                        from trhtransout
                        WHERE pay = 'TUNAI' AND panjar <> 1 and no_sp2d in
                        (SELECT no_sp2d as no_bukti FROM trhtransout where $init_skpd2 GROUP BY no_sp2d HAVING COUNT(no_sp2d)>1)
                        AND   no_kas not in
                        (SELECT min(z.no_kas) as no_bukti FROM trhtransout z WHERE z.jns_spp in (4,5,6) and $init_skpd2

                        GROUP BY z.no_sp2d HAVING COUNT(z.no_sp2d)>1)
                        and jns_spp in (4,5,6) and $init_skpd2
        UNION ALL
        SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_setorsimpanan WHERE jenis ='2' AND $init_skpd2 UNION ALL
        SELECT tgl_bukti AS tgl,no_bukti AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd_sumber AS kode FROM tr_setorpelimpahan WHERE $init_skpd3
        ) a
        where $init_skpd4"))->first();

        return response()->json($data);
    }

    public function cekSimpan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtransout')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->count();
        return response()->json($data);
    }

    public function simpanTransaksi(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = no_urut($kd_skpd);
            // $no_urut = $data['no_bukti'];

            // TRHTRANSOUT
            DB::table('trhtransout')->where(['no_bukti' => $no_urut, 'kd_skpd' => $kd_skpd, 'pay' => 'TUNAI'])->delete();

            DB::table('trhtransout')->insert([
                'no_kas' => $no_urut,
                'tgl_kas' => $data['tgl_bukti'],
                'no_bukti' => $no_urut,
                'tgl_bukti' => $data['tgl_bukti'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $kd_skpd,
                'nm_skpd' => $data['nm_skpd'],
                'total' => $data['total'],
                'no_tagih' => '',
                'sts_tagih' => '0',
                'tgl_tagih' => '',
                'jns_spp' => $data['beban'],
                'pay' => $data['pembayaran'],
                'no_kas_pot' => $no_urut,
                'panjar' => '0',
                'no_sp2d' => $data['sp2d'],
            ]);

            // TRDTRANSOUT
            DB::table('trdtransout')->where(['no_bukti' => $no_urut, 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['tabel_rincian'])) {
                DB::table('trdtransout')->insert(array_map(function ($value) use ($no_urut, $kd_skpd) {
                    return [
                        'no_bukti' => $no_urut,
                        'no_sp2d' => $value['no_sp2d'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $kd_skpd,
                        'sumber' => $value['sumber'],
                    ];
                }, $data['tabel_rincian']));
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_bukti' => $no_urut
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapusTransaksi(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trdtransout')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trhtransout')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd, 'pay' => 'TUNAI'])->delete();

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

    public function editTransaksi(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // TRHTRANSOUT
            DB::table('trhtransout')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd, 'pay' => 'TUNAI'])->delete();

            DB::table('trhtransout')->insert([
                'no_kas' => $data['no_bukti'],
                'tgl_kas' => $data['tgl_bukti'],
                'no_bukti' => $data['no_bukti'],
                'tgl_bukti' => $data['tgl_bukti'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $kd_skpd,
                'nm_skpd' => $data['nm_skpd'],
                'total' => $data['total'],
                'no_tagih' => '',
                'sts_tagih' => '0',
                'tgl_tagih' => '',
                'jns_spp' => $data['beban'],
                'pay' => $data['pembayaran'],
                'no_kas_pot' => $data['no_bukti'],
                'panjar' => '0',
                'no_sp2d' => $data['sp2d'],
            ]);

            // TRDTRANSOUT
            DB::table('trdtransout')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['tabel_rincian'])) {
                DB::table('trdtransout')->insert(array_map(function ($value) use ($data, $kd_skpd) {
                    return [
                        'no_bukti' => $data['no_bukti'],
                        'no_sp2d' => $value['no_sp2d'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $kd_skpd,
                        'sumber' => $value['sumber'],
                    ];
                }, $data['tabel_rincian']));
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_bukti' => $data['no_bukti']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function edit($no_bukti)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_bukti = Crypt::decryptString($no_bukti);

        $data = [
            'transaksi' => DB::table('trhtransout as a')->join('trdtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.*')->where(['a.kd_skpd' => $kd_skpd, 'a.no_bukti' => $no_bukti, 'a.pay' => 'TUNAI'])->first(),
            'daftar_transaksi' => DB::table('trdtransout as a')->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.*')->where(['a.kd_skpd' => $kd_skpd, 'a.no_bukti' => $no_bukti, 'b.pay' => 'TUNAI'])->get(),
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_kegiatan' => DB::table('trdrka as a')->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan', DB::raw("SUM(a.nilai) as total"))->where(['a.kd_skpd' => $kd_skpd])->whereRaw("left(a.kd_rek6,1)=?", ['5'])->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->orderBy('a.kd_sub_kegiatan')->orderBy('a.nm_sub_kegiatan')->get(),
            'persen' => DB::table('config_app')->select('persen_kkpd', 'persen_tunai')->first(),
        ];
        return view('skpd.transaksi_tunai.edit')->with($data);
    }
}
