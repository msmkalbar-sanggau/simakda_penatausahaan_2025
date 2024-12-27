<?php

namespace App\Http\Controllers\Skpd\Anggaran;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use PDF;

use function PHPUnit\Framework\isNull;

class RakController extends Controller
{
    public function index()
    {
        if (Gate::denies('akses')) {
            abort(401);
        }

        $kd_skpd = Auth::user()->kd_skpd;
        if (Auth::user()->is_admin == 2) {
            $data = [
                'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'jns')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->get(),
                'username' => Auth::user()->nama,
                'role' => Auth::user()->role
            ];
        } else {
            $data = [
                'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'jns')->orderBy('kd_skpd')->get(),
                'username' => Auth::user()->nama,
                'role' => Auth::user()->role
            ];
        }

        return view('skpd.input_rak.create')->with($data);
    }

    public function jenisAnggaran(Request $request)
    {
        $data = DB::table('tb_status_anggaran')->where(['status_aktif' => '1'])->get();
        return response()->json($data);
    }

    public function jenisRak(Request $request)
    {
        $jns_ang = $request->jns_ang;
        if (strlen($jns_ang) == '1') {
            $len = '1';
        } else {
            $len = '2';
        }

        $data = DB::table('tb_status_angkas')->where(['status' => '1'])->whereRaw("left(jns_ang,?)=?", [$len, $jns_ang])->get();
        return response()->json($data);
    }

    public function subKegiatan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jns_ang = $request->jns_ang;

        $data = DB::table('trskpd as a')
            ->join('trdrka as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
                $join->on('a.jns_ang', '=', 'b.jns_ang');
            })
            ->select('b.kd_skpd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', 'a.nm_program')
            ->selectRaw("sum(b.nilai) as total")
            ->where(['b.kd_skpd' => $kd_skpd, 'b.jns_ang' => $jns_ang])
            ->groupBy('b.kd_skpd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', 'a.nm_program')
            ->orderBy('b.kd_sub_kegiatan')
            ->get();
        return response()->json($data);
    }

    public function rekeningRak(Request $request)
    {
        $jenis_anggaran = $request->jenis_anggaran;
        $kd_skpd = $request->kd_skpd;
        $jenis_rak = empty($request->jenis_rak) ? 'nilai' : "nilai_" . $request->jenis_rak;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        $data = DB::table('trdrka as a')
            ->select('a.kd_rek6', 'a.nm_rek6', DB::raw("SUM(a.nilai) as nilai"))
            ->selectRaw("(SELECT sum($jenis_rak) FROM trdskpd_ro WHERE kd_sub_kegiatan=a.kd_sub_kegiatan AND kd_rek6=a.kd_rek6 AND kd_skpd=a.kd_skpd) as nilai_rak")
            ->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.jns_ang' => $jenis_anggaran, 'status_aktif' => '1'])->groupBy('a.kd_skpd', 'a.kd_sub_kegiatan', 'a.kd_rek6', 'a.nm_rek6')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="javascript:void(0);" onclick="detail(' . $row->kd_rek6 . ', \'' . $row->nm_rek6 . '\', \'' . $row->nilai . '\');" class="btn btn-primary btn-sm"><i class="fa fa-list-ul"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }
    // RAK
    public function nilaiTriwulan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jenis_rak = $request->jenis_rak;
        $jenis = 'nilai_' . $jenis_rak;
        $kd_rek6 = $request->kd_rek6;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        $data = DB::table('trdskpd_ro')->select("bulan", "$jenis as nilai")->where(['kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rek6, 'kd_skpd' => $kd_skpd])->orderBy('bulan')->get();
        return response()->json($data);
    }
    // REALISASI TOTAL TRIWULAN 1-4
    public function nilaiRealisasi(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_rek6 = $request->kd_rek6;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        // TRIWULAN I

        // TRANSAKSI UP/GU
        $data1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti) BETWEEN 1 AND 3");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $data2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher) BETWEEN 1 AND 3")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($data1);

        // TRANSAKSI SPP SELAIN UP/GU
        $data3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp) BETWEEN 1 AND 3")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($data2);

        // PENAGIHAN YANG BELUM JADI SPP
        $data4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti) BETWEEN 1 AND 3")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($data3);

        // TRIWULAN II

        // TRANSAKSI UP/GU
        $data5 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti) BETWEEN 4 AND 6");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $data6 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher) BETWEEN 4 AND 6")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($data5);

        // TRANSAKSI SPP SELAIN UP/GU
        $data7 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp) BETWEEN 4 AND 6")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($data6);

        // PENAGIHAN YANG BELUM JADI SPP
        $data8 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti) BETWEEN 4 AND 6")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($data7);

        // TRIWULAN III

        // TRANSAKSI UP/GU
        $data9 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti) BETWEEN 7 AND 9");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $data10 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher) BETWEEN 7 AND 9")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($data9);

        // TRANSAKSI SPP SELAIN UP/GU
        $data11 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp) BETWEEN 7 AND 9")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($data10);

        // PENAGIHAN YANG BELUM JADI SPP
        $data12 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti) BETWEEN 7 AND 9")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($data11);

        // TRIWULAN IV

        // TRANSAKSI UP/GU
        $data13 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti) BETWEEN 10 AND 12");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $data14 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher) BETWEEN 10 AND 12")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($data13);

        // TRANSAKSI SPP SELAIN UP/GU
        $data15 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp) BETWEEN 10 AND 12")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($data14);

        // PENAGIHAN YANG BELUM JADI SPP
        $data16 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti) BETWEEN 10 AND 12")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($data15);

        // TRIWULAN I
        $tw1 = DB::table(DB::raw("({$data4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($data4)
            ->first();
        // TRIWULAN II
        $tw2 = DB::table(DB::raw("({$data8->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($data8)
            ->first();
        // TRIWULAN III
        $tw3 = DB::table(DB::raw("({$data12->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($data12)
            ->first();
        // TRIWULAN IV
        $tw4 = DB::table(DB::raw("({$data16->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($data16)
            ->first();
        return response()->json([
            'tw1' => $tw1,
            'tw2' => $tw2,
            'tw3' => $tw3,
            'tw4' => $tw4,
        ]);
    }
    // REALISASI PER BULAN DALAM TRIWULAN
    public function nilaiRealisasiBulan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_rek6 = $request->kd_rek6;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        // BULAN JANUARI

        // TRANSAKSI UP/GU
        $januari1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='1'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $januari2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='1'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($januari1);

        // TRANSAKSI SPP SELAIN UP/GU
        $januari3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='1'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($januari2);

        // PENAGIHAN YANG BELUM JADI SPP
        $januari4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='1'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($januari3);

        // BULAN FEBRUARI

        // TRANSAKSI UP/GU
        $februari1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='2'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $februari2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='2'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($februari1);

        // TRANSAKSI SPP SELAIN UP/GU
        $februari3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='2'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($februari2);

        // PENAGIHAN YANG BELUM JADI SPP
        $februari4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='2'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($februari3);

        // BULAN MARET

        // TRANSAKSI UP/GU
        $maret1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='3'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $maret2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='3'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($maret1);

        // TRANSAKSI SPP SELAIN UP/GU
        $maret3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='3'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($maret2);

        // PENAGIHAN YANG BELUM JADI SPP
        $maret4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='3'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($maret3);

        // BULAN APRIL

        // TRANSAKSI UP/GU
        $april1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='4'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $april2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='4'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($april1);

        // TRANSAKSI SPP SELAIN UP/GU
        $april3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='4'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($april2);

        // PENAGIHAN YANG BELUM JADI SPP
        $april4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='4'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($april3);

        // BULAN MEI

        // TRANSAKSI UP/GU
        $mei1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='5'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $mei2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='5'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($mei1);

        // TRANSAKSI SPP SELAIN UP/GU
        $mei3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='5'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($mei2);

        // PENAGIHAN YANG BELUM JADI SPP
        $mei4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='5'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($mei3);

        // BULAN JUNI

        // TRANSAKSI UP/GU
        $juni1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='6'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $juni2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='6'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($juni1);

        // TRANSAKSI SPP SELAIN UP/GU
        $juni3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='6'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($juni2);

        // PENAGIHAN YANG BELUM JADI SPP
        $juni4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='6'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($juni3);

        // BULAN JULI

        // TRANSAKSI UP/GU
        $juli1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='7'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $juli2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='7'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($juli1);

        // TRANSAKSI SPP SELAIN UP/GU
        $juli3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='7'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($juli2);

        // PENAGIHAN YANG BELUM JADI SPP
        $juli4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='7'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($juli3);

        // BULAN AGUSTUS

        // TRANSAKSI UP/GU
        $agustus1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='8'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $agustus2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='8'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($agustus1);

        // TRANSAKSI SPP SELAIN UP/GU
        $agustus3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='8'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($agustus2);

        // PENAGIHAN YANG BELUM JADI SPP
        $agustus4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='8'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($agustus3);

        // BULAN SEPTEMBER

        // TRANSAKSI UP/GU
        $september1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='9'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $september2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='9'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($september1);

        // TRANSAKSI SPP SELAIN UP/GU
        $september3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='9'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($september2);

        // PENAGIHAN YANG BELUM JADI SPP
        $september4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='9'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($september3);

        // BULAN OKTOBER

        // TRANSAKSI UP/GU
        $oktober1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='10'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $oktober2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='10'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($oktober1);

        // TRANSAKSI SPP SELAIN UP/GU
        $oktober3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='10'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($oktober2);

        // PENAGIHAN YANG BELUM JADI SPP
        $oktober4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='10'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($oktober3);

        // BULAN NOVEMBER

        // TRANSAKSI UP/GU
        $november1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='11'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $november2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='11'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($november1);

        // TRANSAKSI SPP SELAIN UP/GU
        $november3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='11'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($november2);

        // PENAGIHAN YANG BELUM JADI SPP
        $november4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='11'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($november3);

        // BULAN DESEMBER

        // TRANSAKSI UP/GU
        $desember1 = DB::table('trdtransout as a')->leftJoin('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_bukti)='12'");

        // TRANSAKSI UP/GU CMS BANK BELUM VALIDASI
        $desember2 = DB::table('trdtransout_cmsbank as a')->leftJoin('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'b.jns_spp' => '1'])->whereRaw("month(b.tgl_voucher)='12'")->where(function ($query) {
            $query->where('b.status_validasi', '0')->orWhereNull('b.status_validasi');
        })->unionAll($desember1);

        // TRANSAKSI SPP SELAIN UP/GU
        $desember3 = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_spp)='12'")->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '0')->orWhere('b.sp2d_batal', '')->orWhereNull('b.sp2d_batal');
        })->unionAll($desember2);

        // PENAGIHAN YANG BELUM JADI SPP
        $desember4 = DB::table('trdtagih as a')->join('trhtagih as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select(DB::raw("SUM(ISNULL(a.nilai,0)) as nilai"))->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6])->whereRaw("month(b.tgl_bukti)='12'")->whereRaw("b.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)", [$kd_skpd])->unionAll($desember3);

        // Bulan Januari
        $januari = DB::table(DB::raw("({$januari4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($januari4)
            ->first();
        // Bulan Februari
        $februari = DB::table(DB::raw("({$februari4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($februari4)
            ->first();
        // Bulan Maret
        $maret = DB::table(DB::raw("({$maret4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($maret4)
            ->first();
        // Bulan April
        $april = DB::table(DB::raw("({$april4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($april4)
            ->first();
        // Bulan MEI
        $mei = DB::table(DB::raw("({$mei4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($mei4)
            ->first();
        // Bulan JUNI
        $juni = DB::table(DB::raw("({$juni4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($juni4)
            ->first();
        // Bulan JULI
        $juli = DB::table(DB::raw("({$juli4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($juli4)
            ->first();
        // Bulan AGUSTUS
        $agustus = DB::table(DB::raw("({$agustus4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($agustus4)
            ->first();
        // Bulan SEPTEMBER
        $september = DB::table(DB::raw("({$september4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($september4)
            ->first();
        // Bulan OKTOBER
        $oktober = DB::table(DB::raw("({$oktober4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($oktober4)
            ->first();
        // Bulan NOVEMBER
        $november = DB::table(DB::raw("({$november4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($november4)
            ->first();
        // Bulan DESEMBER
        $desember = DB::table(DB::raw("({$desember4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->mergeBindings($desember4)
            ->first();
        return response()->json([
            'januari' => $januari,
            'februari' => $februari,
            'maret' => $maret,
            'april' => $april,
            'mei' => $mei,
            'juni' => $juni,
            'juli' => $juli,
            'agustus' => $agustus,
            'september' => $september,
            'oktober' => $oktober,
            'november' => $november,
            'desember' => $desember,
        ]);
    }

    public function statusKunci(Request $request)
    {
        $jenis_rak = $request->jenis_rak;
        $kd_skpd = $request->kd_skpd;

        $status = DB::table('tb_status_angkas')->select('status_kunci')->where(['kode' => $jenis_rak])->first();

        $data = DB::table('status_angkas')->select("$status->status_kunci as status")->where(['kd_skpd' => $kd_skpd])->first();
        return response()->json($data);
    }

    // SIMPAN RAK
    public function simpanRak(Request $request)
    {
        if (Gate::denies('akses')) {
            return response()->json([
                'message' => '3'
            ]);
        }

        $data = $request->data;
        $status = "nilai_" . $data['jenis_rak'];

        DB::beginTransaction();
        try {
            $jumlah = DB::table('trdskpd_ro')->where(['kd_sub_kegiatan' => $data['kd_sub_kegiatan'], 'kd_skpd' => $data['kd_skpd'], 'kd_rek6' => $data['kode_rekening']])->count();

            if ($jumlah > 0) {
                $kd_gabungan = $data['kd_skpd'] . '.' . $data['kd_sub_kegiatan'] . '.' . $data['kode_rekening'];

                for ($i = 1; $i <= 12; $i++) {
                    $bulan = "bln$i";
                    switch ($status) {
                        case 'nilai_susun':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai' => $data[$bulan],

                                'nilai_susun' => $data[$bulan],
                                'nilai_susun1' => $data[$bulan],
                                'nilai_susun2' => $data[$bulan],
                                'nilai_susun3' => $data[$bulan],
                                'nilai_susun4' => $data[$bulan],
                                'nilai_susun5' => $data[$bulan],

                                'nilai_sempurna' => $data[$bulan],
                                'nilai_sempurna11' => $data[$bulan],
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_susun11':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_susun1' => $data[$bulan],
                                'nilai_susun2' => $data[$bulan],
                                'nilai_susun3' => $data[$bulan],
                                'nilai_susun4' => $data[$bulan],
                                'nilai_susun5' => $data[$bulan],

                                'nilai_sempurna' => $data[$bulan],
                                'nilai_sempurna11' => $data[$bulan],
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna' => $data[$bulan],
                                'nilai_sempurna11' => $data[$bulan],
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna11':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna11' => $data[$bulan],
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna12':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna2':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna21':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna3':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna31':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna32':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna4':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna41':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna42':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_ubah':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_ubah2':
                            DB::table('trdskpd_ro')->where(['kd_gabungan' => $kd_gabungan, 'kd_rek6' => $data['kode_rekening'], 'bulan' => $i])->update([
                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],

                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                    }
                }
            } else {
                DB::table('trdskpd_ro')->where(['kd_sub_kegiatan' => $data['kd_sub_kegiatan'], 'kd_skpd' => $data['kd_skpd'], 'kd_rek6' => $data['kode_rekening']])->delete();
                $kd_gabungan = $data['kd_skpd'] . '.' . $data['kd_sub_kegiatan'] . '.' . $data['kode_rekening'];

                for ($i = 1; $i <= 12; $i++) {
                    $bulan = "bln$i";
                    switch ($status) {
                        case 'nilai_susun':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => $data[$bulan],

                                'nilai_susun' => $data[$bulan],
                                'nilai_susun1' => $data[$bulan],
                                'nilai_susun2' => $data[$bulan],
                                'nilai_susun3' => $data[$bulan],
                                'nilai_susun4' => $data[$bulan],
                                'nilai_susun5' => $data[$bulan],

                                'nilai_sempurna' => $data[$bulan],
                                'nilai_sempurna11' => $data[$bulan],
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_susun1':

                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => $data[$bulan],
                                'nilai_susun2' => $data[$bulan],
                                'nilai_susun3' => $data[$bulan],
                                'nilai_susun4' => $data[$bulan],
                                'nilai_susun5' => $data[$bulan],

                                'nilai_sempurna' => $data[$bulan],
                                'nilai_sempurna11' => $data[$bulan],
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_susun2':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => $data[$bulan],
                                'nilai_susun2' => $data[$bulan],
                                'nilai_susun3' => $data[$bulan],
                                'nilai_susun4' => $data[$bulan],
                                'nilai_susun5' => $data[$bulan],

                                'nilai_sempurna' => $data[$bulan],
                                'nilai_sempurna11' => $data[$bulan],
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => $data[$bulan],

                                'nilai_sempurna' => $data[$bulan],
                                'nilai_sempurna11' => $data[$bulan],
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna11':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => $data[$bulan],
                                'nilai_sempurna11' => $data[$bulan],
                                'nilai_sempurna12' => $data[$bulan],
                                'nilai_sempurna13' => $data[$bulan],
                                'nilai_sempurna14' => $data[$bulan],
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna2':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => $data[$bulan],

                                'nilai_sempurna2' => $data[$bulan],
                                'nilai_sempurna21' => $data[$bulan],
                                'nilai_sempurna22' => $data[$bulan],
                                'nilai_sempurna23' => $data[$bulan],
                                'nilai_sempurna24' => $data[$bulan],
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna3':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => $data[$bulan],

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna31':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => $data[$bulan],
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna32':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => '0',
                                'nilai_sempurna31' => $data[$bulan],
                                'nilai_sempurna32' => $data[$bulan],
                                'nilai_sempurna33' => $data[$bulan],
                                'nilai_sempurna34' => $data[$bulan],
                                'nilai_sempurna35' => $data[$bulan],

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna4':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => '0',
                                'nilai_sempurna31' => '0',
                                'nilai_sempurna32' => '0',
                                'nilai_sempurna33' => '0',
                                'nilai_sempurna34' => '0',
                                'nilai_sempurna35' => '0',

                                'nilai_sempurna4' => $data[$bulan],
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna41':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => '0',
                                'nilai_sempurna31' => '0',
                                'nilai_sempurna32' => '0',
                                'nilai_sempurna33' => '0',
                                'nilai_sempurna34' => '0',
                                'nilai_sempurna35' => '0',

                                'nilai_sempurna4' => '0',
                                'nilai_sempurna41' => $data[$bulan],
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_sempurna42':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => '0',
                                'nilai_sempurna31' => '0',
                                'nilai_sempurna32' => '0',
                                'nilai_sempurna33' => '0',
                                'nilai_sempurna34' => '0',
                                'nilai_sempurna35' => '0',

                                'nilai_sempurna4' => '0',
                                'nilai_sempurna41' => '0',
                                'nilai_sempurna42' => $data[$bulan],
                                'nilai_sempurna43' => $data[$bulan],
                                'nilai_sempurna44' => $data[$bulan],
                                'nilai_sempurna45' => $data[$bulan],

                                'nilai_sempurna5' => $data[$bulan],
                                'nilai_sempurna51' => $data[$bulan],
                                'nilai_sempurna52' => $data[$bulan],
                                'nilai_sempurna53' => $data[$bulan],
                                'nilai_sempurna54' => $data[$bulan],
                                'nilai_sempurna55' => $data[$bulan],

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_ubah':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => '0',
                                'nilai_sempurna31' => '0',
                                'nilai_sempurna32' => '0',
                                'nilai_sempurna33' => '0',
                                'nilai_sempurna34' => '0',
                                'nilai_sempurna35' => '0',

                                'nilai_sempurna4' => '0',
                                'nilai_sempurna41' => '0',
                                'nilai_sempurna42' => '0',
                                'nilai_sempurna43' => '0',
                                'nilai_sempurna44' => '0',
                                'nilai_sempurna45' => '0',

                                'nilai_sempurna5' => '0',
                                'nilai_sempurna51' => '0',
                                'nilai_sempurna52' => '0',
                                'nilai_sempurna53' => '0',
                                'nilai_sempurna54' => '0',
                                'nilai_sempurna55' => '0',

                                'nilai_ubah' => $data[$bulan],
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_ubah11':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => '0',
                                'nilai_sempurna31' => '0',
                                'nilai_sempurna32' => '0',
                                'nilai_sempurna33' => '0',
                                'nilai_sempurna34' => '0',
                                'nilai_sempurna35' => '0',

                                'nilai_sempurna4' => '0',
                                'nilai_sempurna41' => '0',
                                'nilai_sempurna42' => '0',
                                'nilai_sempurna43' => '0',
                                'nilai_sempurna44' => '0',
                                'nilai_sempurna45' => '0',

                                'nilai_sempurna5' => '0',
                                'nilai_sempurna51' => '0',
                                'nilai_sempurna52' => '0',
                                'nilai_sempurna53' => '0',
                                'nilai_sempurna54' => '0',
                                'nilai_sempurna55' => '0',

                                'nilai_ubah' => '0',
                                'nilai_ubah11' => $data[$bulan],
                                'nilai_ubah12' => $data[$bulan],
                                'nilai_ubah13' => $data[$bulan],
                                'nilai_ubah14' => $data[$bulan],
                                'nilai_ubah15' => $data[$bulan],

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_ubah2':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => '0',
                                'nilai_sempurna31' => '0',
                                'nilai_sempurna32' => '0',
                                'nilai_sempurna33' => '0',
                                'nilai_sempurna34' => '0',
                                'nilai_sempurna35' => '0',

                                'nilai_sempurna4' => '0',
                                'nilai_sempurna41' => '0',
                                'nilai_sempurna42' => '0',
                                'nilai_sempurna43' => '0',
                                'nilai_sempurna44' => '0',
                                'nilai_sempurna45' => '0',

                                'nilai_sempurna5' => '0',
                                'nilai_sempurna51' => '0',
                                'nilai_sempurna52' => '0',
                                'nilai_sempurna53' => '0',
                                'nilai_sempurna54' => '0',
                                'nilai_sempurna55' => '0',

                                'nilai_ubah' => '0',
                                'nilai_ubah11' => '0',
                                'nilai_ubah12' => '0',
                                'nilai_ubah13' => '0',
                                'nilai_ubah14' => '0',
                                'nilai_ubah15' => '0',

                                'nilai_ubah2' => $data[$bulan],
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_ubah21':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => '0',
                                'nilai_sempurna31' => '0',
                                'nilai_sempurna32' => '0',
                                'nilai_sempurna33' => '0',
                                'nilai_sempurna34' => '0',
                                'nilai_sempurna35' => '0',

                                'nilai_sempurna4' => '0',
                                'nilai_sempurna41' => '0',
                                'nilai_sempurna42' => '0',
                                'nilai_sempurna43' => '0',
                                'nilai_sempurna44' => '0',
                                'nilai_sempurna45' => '0',

                                'nilai_sempurna5' => '0',
                                'nilai_sempurna51' => '0',
                                'nilai_sempurna52' => '0',
                                'nilai_sempurna53' => '0',
                                'nilai_sempurna54' => '0',
                                'nilai_sempurna55' => '0',

                                'nilai_ubah' => '0',
                                'nilai_ubah11' => '0',
                                'nilai_ubah12' => '0',
                                'nilai_ubah13' => '0',
                                'nilai_ubah14' => '0',
                                'nilai_ubah15' => '0',

                                'nilai_ubah2' => '0',
                                'nilai_ubah21' => $data[$bulan],
                                'nilai_ubah22' => $data[$bulan],
                                'nilai_ubah23' => $data[$bulan],
                                'nilai_ubah24' => $data[$bulan],
                                'nilai_ubah25' => $data[$bulan],

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        case 'nilai_ubah3':
                            DB::table('trdskpd_ro')->insert([
                                'kd_gabungan' => $kd_gabungan,
                                'kd_skpd' => $data['kd_skpd'],
                                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                                'kd_rek6' => $data['kode_rekening'],
                                'bulan' => $i,

                                'nilai' => '0',

                                'nilai_susun' => '0',
                                'nilai_susun1' => '0',
                                'nilai_susun2' => '0',
                                'nilai_susun3' => '0',
                                'nilai_susun4' => '0',
                                'nilai_susun5' => '0',

                                'nilai_sempurna' => '0',
                                'nilai_sempurna11' => '0',
                                'nilai_sempurna12' => '0',
                                'nilai_sempurna13' => '0',
                                'nilai_sempurna14' => '0',
                                'nilai_sempurna15' => '0',

                                'nilai_sempurna2' => '0',
                                'nilai_sempurna21' => '0',
                                'nilai_sempurna22' => '0',
                                'nilai_sempurna23' => '0',
                                'nilai_sempurna24' => '0',
                                'nilai_sempurna25' => '0',

                                'nilai_sempurna3' => '0',
                                'nilai_sempurna31' => '0',
                                'nilai_sempurna32' => '0',
                                'nilai_sempurna33' => '0',
                                'nilai_sempurna34' => '0',
                                'nilai_sempurna35' => '0',

                                'nilai_sempurna4' => '0',
                                'nilai_sempurna41' => '0',
                                'nilai_sempurna42' => '0',
                                'nilai_sempurna43' => '0',
                                'nilai_sempurna44' => '0',
                                'nilai_sempurna45' => '0',

                                'nilai_sempurna5' => '0',
                                'nilai_sempurna51' => '0',
                                'nilai_sempurna52' => '0',
                                'nilai_sempurna53' => '0',
                                'nilai_sempurna54' => '0',
                                'nilai_sempurna55' => '0',

                                'nilai_ubah' => '0',
                                'nilai_ubah11' => '0',
                                'nilai_ubah12' => '0',
                                'nilai_ubah13' => '0',
                                'nilai_ubah14' => '0',
                                'nilai_ubah15' => '0',

                                'nilai_ubah2' => '0',
                                'nilai_ubah21' => '0',
                                'nilai_ubah22' => '0',
                                'nilai_ubah23' => '0',
                                'nilai_ubah24' => '0',
                                'nilai_ubah25' => '0',

                                'nilai_ubah3' => $data[$bulan],
                                'nilai_ubah31' => $data[$bulan],
                                'nilai_ubah32' => $data[$bulan],
                                'nilai_ubah33' => $data[$bulan],
                                'nilai_ubah34' => $data[$bulan],
                                'nilai_ubah35' => $data[$bulan],

                                'nilai_ubah4' => $data[$bulan],
                                'nilai_ubah41' => $data[$bulan],
                                'nilai_ubah42' => $data[$bulan],
                                'nilai_ubah43' => $data[$bulan],
                                'nilai_ubah44' => $data[$bulan],
                                'nilai_ubah45' => $data[$bulan],

                                'nilai_ubah5' => $data[$bulan],
                                'nilai_ubah51' => $data[$bulan],
                                'nilai_ubah52' => $data[$bulan],
                                'nilai_ubah53' => $data[$bulan],
                                'nilai_ubah54' => $data[$bulan],
                                'nilai_ubah55' => $data[$bulan],
                                'status' => '1',
                                'username' => Auth::user()->nama,
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);
                    }
                }
            }

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

    // Cari TTD SKPD
    function cariTtdSkpd(Request $request)
    {
        $kd_skpd    = $request->kd_skpd;
        $data       = DB::table('ms_ttd')->where(['kd_skpd' => $kd_skpd])->orderBy('nip')->orderBy('nama')->get();
        return response()->json($data);
    }

    // CETAK RAK PER SUB KEGIATAN
    public function cetakPerSubKegiatanIndex()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => get_skpd($kd_skpd),
            'daftar_ttd2' => DB::table('ms_ttd')->select('nip', 'nama', 'id')->where(['kode' => 'bud'])->get(),
        ];

        return view('skpd.cetak_rak.per_sub_kegiatan.cetak')->with($data);
    }

    public function jenisAnggaranCetak(Request $request)
    {
        $data = DB::table('tb_status_anggaran')->select('kode', 'nama')->where(['status_aktif' => '1'])->get();
        return response()->json($data);
    }

    public function cetakRakPerKegiatan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jenis_anggaran = $request->jenis_anggaran;
        $jenis_rak = $request->jenis_rak;
        $ttd1 = $request->ttd1;
        $tanggal_ttd = $request->tanggal_ttd;
        $jenis_print = $request->jenis_print;
        $hidden = $request->hidden;

        $jenis = "nilai_" . $jenis_rak;

        $angkas1 = DB::table('trdskpd_ro as a')
            ->selectRaw("a.kd_sub_kegiatan,
            case when bulan=1 then sum($jenis) else 0 end as jan,
            case when bulan=2 then sum($jenis) else 0 end as feb,
            case when bulan=3 then sum($jenis) else 0 end as mar,
            case when bulan=4 then sum($jenis) else 0 end as apr,
            case when bulan=5 then sum($jenis) else 0 end as mei,
            case when bulan=6 then sum($jenis) else 0 end as jun,
            case when bulan=7 then sum($jenis) else 0 end as jul,
            case when bulan=8 then sum($jenis) else 0 end as ags,
            case when bulan=9 then sum($jenis) else 0 end as sep,
            case when bulan=10 then sum($jenis) else 0 end as okt,
            case when bulan=11 then sum($jenis) else 0 end as nov,
            case when bulan=12 then sum($jenis) else 0 end as des")
            ->where(['a.kd_skpd' => $kd_skpd])
            ->groupBy('a.kd_sub_kegiatan', 'a.bulan');

        $angkas2 = DB::table(DB::raw("({$angkas1->toSql()}) as sub"))
            ->select('kd_sub_kegiatan as giat', DB::raw("(SELECT nm_sub_kegiatan FROM ms_sub_kegiatan WHERE kd_sub_kegiatan=sub.kd_sub_kegiatan) as nm_giat"), DB::raw("ISNULL(SUM(jan),0) as jan"), DB::raw("ISNULL(SUM(feb),0) as feb"), DB::raw("ISNULL(SUM(mar),0) as mar"), DB::raw("ISNULL(SUM(apr),0) as apr"), DB::raw("ISNULL(SUM(mei),0) as mei"), DB::raw("ISNULL(SUM(jun),0) as jun"), DB::raw("ISNULL(SUM(jul),0) as jul"), DB::raw("ISNULL(SUM(ags),0) as ags"), DB::raw("ISNULL(SUM(sep),0) as sep"), DB::raw("ISNULL(SUM(okt),0) as okt"), DB::raw("ISNULL(SUM(nov),0) as nov"), DB::raw("ISNULL(SUM(des),0) as des"))
            ->selectRaw("(SELECT sum(nilai) from trdrka where kd_sub_kegiatan=sub.kd_sub_kegiatan and kd_skpd=? and jns_ang=?) as ang", [$kd_skpd, $jenis_anggaran])
            ->mergeBindings($angkas1)
            ->groupBy('kd_sub_kegiatan');

        $angkas3 = DB::table('trdskpd_ro as a')
            ->selectRaw("left(a.kd_sub_kegiatan,12) as kd_sub_kegiatan,
            case when bulan=1 then sum($jenis) else 0 end as jan,
            case when bulan=2 then sum($jenis) else 0 end as feb,
            case when bulan=3 then sum($jenis) else 0 end as mar,
            case when bulan=4 then sum($jenis) else 0 end as apr,
            case when bulan=5 then sum($jenis) else 0 end as mei,
            case when bulan=6 then sum($jenis) else 0 end as jun,
            case when bulan=7 then sum($jenis) else 0 end as jul,
            case when bulan=8 then sum($jenis) else 0 end as ags,
            case when bulan=9 then sum($jenis) else 0 end as sep,
            case when bulan=10 then sum($jenis) else 0 end as okt,
            case when bulan=11 then sum($jenis) else 0 end as nov,
            case when bulan=12 then sum($jenis) else 0 end as des")
            ->where(['a.kd_skpd' => $kd_skpd])
            ->groupBy(DB::raw("LEFT(kd_sub_kegiatan,12)"), 'a.bulan');

        $angkas4 = DB::table(DB::raw("({$angkas3->toSql()}) as sub"))
            ->select('kd_sub_kegiatan as giat', DB::raw("(SELECT DISTINCT nm_kegiatan FROM ms_kegiatan WHERE left(kd_kegiatan,12)=left(sub.kd_sub_kegiatan,12)) as nm_giat"), DB::raw("ISNULL(SUM(jan),0) as jan"), DB::raw("ISNULL(SUM(feb),0) as feb"), DB::raw("ISNULL(SUM(mar),0) as mar"), DB::raw("ISNULL(SUM(apr),0) as apr"), DB::raw("ISNULL(SUM(mei),0) as mei"), DB::raw("ISNULL(SUM(jun),0) as jun"), DB::raw("ISNULL(SUM(jul),0) as jul"), DB::raw("ISNULL(SUM(ags),0) as ags"), DB::raw("ISNULL(SUM(sep),0) as sep"), DB::raw("ISNULL(SUM(okt),0) as okt"), DB::raw("ISNULL(SUM(nov),0) as nov"), DB::raw("ISNULL(SUM(des),0) as des"))
            ->selectRaw("(SELECT sum(nilai) from trdrka where left(kd_sub_kegiatan,12)=left(sub.kd_sub_kegiatan,12) and kd_skpd=? and jns_ang=?) as ang", [$kd_skpd, $jenis_anggaran])
            ->mergeBindings($angkas3)
            ->groupBy('kd_sub_kegiatan')
            ->unionAll($angkas2);

        $angkas = DB::table(DB::raw("({$angkas4->toSql()}) as sub"))
            ->mergeBindings($angkas4)
            ->orderBy('giat')
            ->get();

        $data = [
            'nama_angkas'   => DB::table('tb_status_angkas')->select('nama')->where(['kode' => $jenis_rak])->first(),
            'nama_skpd'     => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'data_giat'     => $angkas,
            'ttd1'          => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['nip' => $ttd1, 'kd_skpd' => $kd_skpd])->first(),
            'tanggal'       => $tanggal_ttd,
            'hidden'        => $hidden,
        ];

        $view = view('skpd.cetak_rak.per_sub_kegiatan.cetakan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'excel') {
            header("Cache-Control:no-cache,no-store,must-revalidate");
            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    // CETAK RAK PER SUB RINCIAN OBJEK
    public function rincianObjekIndex()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => get_skpd($kd_skpd),
            'daftar_ttd2' => DB::table('ms_ttd')->select('nip', 'nama', 'id')->where(['kode' => 'bud'])->get(),
        ];

        if(Auth::user()->is_admin==1){
            return view('skpd.cetak_rak.per_sub_rincian_objek.cetak_seluruh')->with($data);
        }else{
            return view('skpd.cetak_rak.per_sub_rincian_objek.cetak')->with($data);
        }


    }

    public function cetakRakPerObjek(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jenis_anggaran = $request->jenis_anggaran;
        $jenis_rak = $request->jenis_rak;
        $ttd1 = $request->ttd1;
        $tanggal_ttd = $request->tanggal_ttd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $jenis_print = $request->jenis_print;
        $margin = $request->margin;
        $hidden = $request->hidden;
        if ($margin == '') {
            $margin = 10;
        } else {
            $margin = $margin;
        }
        $jenis = "nilai_" . $jenis_rak;

        $join1 = DB::table('trdrka')->select('kd_sub_kegiatan', 'kd_skpd', 'kd_rek6')->where(['jns_ang' => $jenis_anggaran])->groupBy('kd_sub_kegiatan', 'kd_skpd', 'kd_rek6');

        $angkas1 = DB::table('trdskpd_ro as a')->joinSub($join1, 'b', function ($join) {
            $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            $join->on('a.kd_rek6', '=', 'b.kd_rek6');
        })->selectRaw("a.kd_sub_kegiatan,a.kd_rek6,
            case when bulan=1 then sum($jenis) else 0 end as jan,
            case when bulan=2 then sum($jenis) else 0 end as feb,
            case when bulan=3 then sum($jenis) else 0 end as mar,
            case when bulan=4 then sum($jenis) else 0 end as apr,
            case when bulan=5 then sum($jenis) else 0 end as mei,
            case when bulan=6 then sum($jenis) else 0 end as jun,
            case when bulan=7 then sum($jenis) else 0 end as jul,
            case when bulan=8 then sum($jenis) else 0 end as ags,
            case when bulan=9 then sum($jenis) else 0 end as sep,
            case when bulan=10 then sum($jenis) else 0 end as okt,
            case when bulan=11 then sum($jenis) else 0 end as nov,
            case when bulan=12 then sum($jenis) else 0 end as des")->where(['a.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan])->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6', 'a.bulan');

        $angkas = DB::table(DB::raw("({$angkas1->toSql()}) as sub"))
            ->select(DB::raw("(kd_sub_kegiatan+'.') as giat"), 'kd_rek6', DB::raw("(SELECT nm_rek6 FROM ms_rek6 WHERE kd_rek6=sub.kd_rek6) as nm_rek"), DB::raw("ISNULL(SUM(jan),0) as jan"), DB::raw("ISNULL(SUM(feb),0) as feb"), DB::raw("ISNULL(SUM(mar),0) as mar"), DB::raw("ISNULL(SUM(apr),0) as apr"), DB::raw("ISNULL(SUM(mei),0) as mei"), DB::raw("ISNULL(SUM(jun),0) as jun"), DB::raw("ISNULL(SUM(jul),0) as jul"), DB::raw("ISNULL(SUM(ags),0) as ags"), DB::raw("ISNULL(SUM(sep),0) as sep"), DB::raw("ISNULL(SUM(okt),0) as okt"), DB::raw("ISNULL(SUM(nov),0) as nov"), DB::raw("ISNULL(SUM(des),0) as des"))
            ->mergeBindings($angkas1)
            ->groupBy('kd_sub_kegiatan', 'kd_rek6')
            ->orderBy('kd_sub_kegiatan')
            ->get();

        $data = [
            'nama_angkas' => DB::table('tb_status_angkas')->select('nama')->where(['kode' => $jenis_rak])->first(),
            'nama_skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'data_giat' => $angkas,
            'kd_sub_kegiatan' => $kd_sub_kegiatan,
            'jenis_anggaran' => $jenis_anggaran,
            'kd_skpd' => $kd_skpd,
            'ttd1' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['nip' => $ttd1, 'kd_skpd' => $kd_skpd])->first(),
            'tanggal' => $tanggal_ttd,
            'sub_header' => DB::table('ms_skpd as a')->select(DB::raw("left(kd_skpd,1) as urusan"), DB::raw("(SELECT nm_urusan FROM ms_urusan WHERE kd_urusan=left(a.kd_skpd,1)) as nmurusan"), DB::raw("left(kd_skpd,4) as bidang"), DB::raw("(SELECT nm_bidang_urusan FROM ms_bidang_urusan WHERE kd_bidang_urusan=left(a.kd_skpd,4)) as nmbidang"), DB::raw("left(kd_skpd,17) as org"), DB::raw("(SELECT nm_skpd FROM ms_skpd WHERE left(kd_skpd,17)=left(a.kd_skpd,17) AND right(kd_skpd,4)='0000') as nmorg"), 'kd_skpd as unit', 'nm_skpd as nmunit')->where(['kd_skpd' => $kd_skpd])->first(),

            'sub_header1' => DB::table('trdrka as a')->select(DB::raw("left(kd_sub_kegiatan,7) as program"), DB::raw("(SELECT nm_program FROM ms_program WHERE kd_program=left(a.kd_sub_kegiatan,7)) as nmprogram"), DB::raw("left(kd_sub_kegiatan,12) as kegiatan"), DB::raw("(SELECT nm_kegiatan FROM ms_kegiatan WHERE kd_kegiatan=left(a.kd_sub_kegiatan,12)) as nmkegiatan"), 'kd_sub_kegiatan as subkegiatan', 'nm_sub_kegiatan as nmsubkegiatan', 'kd_skpd', DB::raw("SUM(nilai) as ang"))->where(['kd_skpd' => $kd_skpd, 'kd_sub_kegiatan' => $kd_sub_kegiatan, 'jns_ang' => $jenis_anggaran])->groupBy('kd_skpd', 'kd_sub_kegiatan', 'nm_sub_kegiatan')->first(),
            'hidden' => $hidden
        ];

        $view = view('skpd.cetak_rak.per_sub_rincian_objek.cetakan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')

                ->setOption('margin-top', $margin);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'excel') {
            header("Cache-Control:no-cache,no-store,must-revalidate");
            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    public function cetakRakPerObjekSkpd(Request $request)
    {
        ini_set("memory_limit", -1);
        ini_set("execution_time", -1);
        $kd_skpd                = $request->kd_skpd;
        $jenis_anggaran         = $request->jenis_anggaran;
        $jenis_rak              = $request->jenis_rak;
        $ttd1                   = $request->ttd1;
        $tanggal_ttd            = $request->tanggal_ttd;
        $jenis_print            = $request->jenis_print;
        $margin                 = $request->margin;
        $hidden                 = $request->hidden;
        if ($margin == '') {
            $margin = 10;
        } else {
            $margin = $margin;
        }
        $jenis      = "nilai_" . $jenis_rak;

        $anggaran   = DB::select("SELECT kd_skpd+kd_sub_kegiatan as urut,
                                kd_skpd,kd_sub_kegiatan,nm_sub_kegiatan,
                                kd_sub_kegiatan as kd_rek,nm_sub_kegiatan as nm_rek,
                                sum(nilai)as anggaran
                                from trdrka a
                                where kd_skpd= ? and jns_ang= ?
                                GROUP BY kd_skpd,kd_sub_kegiatan,nm_sub_kegiatan
                                UNION ALL
                                SELECT kd_skpd+kd_sub_kegiatan as urut,
                                kd_skpd,kd_sub_kegiatan,nm_sub_kegiatan,
                                kd_rek6,nm_rek6,
                                sum(nilai)as anggaran
                                from trdrka a
                                where kd_skpd= ? and jns_ang= ?
                                GROUP BY kd_skpd,kd_sub_kegiatan,nm_sub_kegiatan,kd_rek6,nm_rek6
                                ",[$kd_skpd,$jenis_anggaran,$kd_skpd,$jenis_anggaran]);

        $angkas = DB::select("SELECT a.kd_skpd+a.kd_sub_kegiatan as urut, a.kd_skpd,a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.kd_sub_kegiatan as kd_rek,a.nm_sub_kegiatan as nm_rek,
                                SUM(CASE WHEN bulan=1 THEN $jenis ELSE 0 END) as jan,
                                SUM(CASE WHEN bulan=2 THEN $jenis ELSE 0 END) as feb,
                                SUM(CASE WHEN bulan=3 THEN $jenis ELSE 0 END) as mar,
                                SUM(CASE WHEN bulan=4 THEN $jenis ELSE 0 END) as apr,
                                SUM(CASE WHEN bulan=5 THEN $jenis ELSE 0 END) as mei,
                                SUM(CASE WHEN bulan=6 THEN $jenis ELSE 0 END) as jun,
                                SUM(CASE WHEN bulan=7 THEN $jenis ELSE 0 END) as jul,
                                SUM(CASE WHEN bulan=8 THEN $jenis ELSE 0 END) as ags,
                                SUM(CASE WHEN bulan=9 THEN $jenis ELSE 0 END) as sep,
                                SUM(CASE WHEN bulan=10 THEN $jenis ELSE 0 END) as okt,
                                SUM(CASE WHEN bulan=11 THEN $jenis ELSE 0 END) as nov,
                                SUM(CASE WHEN bulan=12 THEN $jenis ELSE 0 END) as des
                                from trdrka a INNER JOIN trdskpd_ro b on a.kd_skpd=b.kd_skpd and a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.kd_rek6=b.kd_rek6
                                where a.kd_skpd= ? and a.jns_ang= ?
                                GROUP BY a.kd_skpd,a.kd_sub_kegiatan,a.nm_sub_kegiatan
                                UNION ALL
                                SELECT a.kd_skpd+a.kd_sub_kegiatan+a.kd_rek6 as urut,a.kd_skpd,a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.kd_rek6,a.nm_rek6,
                                SUM(CASE WHEN bulan=1 THEN $jenis ELSE 0 END) as jan,
                                SUM(CASE WHEN bulan=2 THEN $jenis ELSE 0 END) as feb,
                                SUM(CASE WHEN bulan=3 THEN $jenis ELSE 0 END) as mar,
                                SUM(CASE WHEN bulan=4 THEN $jenis ELSE 0 END) as apr,
                                SUM(CASE WHEN bulan=5 THEN $jenis ELSE 0 END) as mei,
                                SUM(CASE WHEN bulan=6 THEN $jenis ELSE 0 END) as jun,
                                SUM(CASE WHEN bulan=7 THEN $jenis ELSE 0 END) as jul,
                                SUM(CASE WHEN bulan=8 THEN $jenis ELSE 0 END) as ags,
                                SUM(CASE WHEN bulan=9 THEN $jenis ELSE 0 END) as sep,
                                SUM(CASE WHEN bulan=10 THEN $jenis ELSE 0 END) as okt,
                                SUM(CASE WHEN bulan=11 THEN $jenis ELSE 0 END) as nov,
                                SUM(CASE WHEN bulan=12 THEN $jenis ELSE 0 END) as des
                                from trdrka a INNER JOIN trdskpd_ro b on a.kd_skpd=b.kd_skpd and a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.kd_rek6=b.kd_rek6
                                where a.kd_skpd= ? and a.jns_ang= ?
                                GROUP BY a.kd_skpd,a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.nm_rek6,a.kd_rek6,a.nm_rek6
                                ORDER BY urut",[$kd_skpd,$jenis_anggaran,$kd_skpd,$jenis_anggaran]);



        $data = [
            'nama_angkas'       => DB::table('tb_status_angkas')->select('nama')->where(['kode' => $jenis_rak])->first(),
            'nama_skpd'         => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'angkas'            => $angkas,
            'anggaran'          => $anggaran,
            'jenis_anggaran'    => $jenis_anggaran,
            'kd_skpd'           => $kd_skpd,
            'ttd1'              => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['nip' => $ttd1, 'kd_skpd' => $kd_skpd])->first(),
            'tanggal'           => $tanggal_ttd,
            'sub_header'        => DB::table('ms_skpd as a')->select(DB::raw("left(kd_skpd,1) as urusan"), DB::raw("(SELECT nm_urusan FROM ms_urusan WHERE kd_urusan=left(a.kd_skpd,1)) as nmurusan"), DB::raw("left(kd_skpd,4) as bidang"), DB::raw("(SELECT nm_bidang_urusan FROM ms_bidang_urusan WHERE kd_bidang_urusan=left(a.kd_skpd,4)) as nmbidang"), DB::raw("left(kd_skpd,17) as org"), DB::raw("(SELECT nm_skpd FROM ms_skpd WHERE left(kd_skpd,17)=left(a.kd_skpd,17) AND right(kd_skpd,4)='0000') as nmorg"), 'kd_skpd as unit', 'nm_skpd as nmunit')->where(['kd_skpd' => $kd_skpd])->first(),
            'hidden'            => $hidden
        ];


        $view = view('skpd.cetak_rak.per_sub_rincian_objek.cetakan_seluruh')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')

                ->setOption('margin-top', $margin);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'excel') {
            header("Cache-Control:no-cache,no-store,must-revalidate");
            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    // CEK RAK CETAK ANGGARAN
    public function cekAnggaranIndex()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        if (Auth::user()->is_admin == 2) {
            $data = [
                'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->get(),
            ];
        } else {
            $data = [
                'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->orderBy('kd_skpd')->get(),
            ];
        }

        return view('skpd.cek_rak.cetak')->with($data);
    }

    public function cetakCekAnggaran(Request $request)
    {
        $kd_skpd        = $request->kd_skpd;
        $jenis_anggaran = $request->jenis_anggaran;
        $jenis_rak      = $request->jenis_rak;
        $jenis_print    = $request->jenis_print;

        $jenis = "nilai_" . $jenis_rak;

        $cek_rak = DB::select("SELECT [a].[giat] as [kd_kegiatan], [a].[nama] as [nm_kegiatan], [a].[kd_rek6], [a].[nm_rek6],
                            [a].[nilai_ang], isnull(b.nilai_kas,0) as nilai_kas, CASE WHEN isnull(b.nilai_kas,0)=a.nilai_ang THEN 'SAMA' ELSE 'SELISIH' END AS hasil from (
                                    select [kd_sub_kegiatan] as [giat], [nm_sub_kegiatan] as [nama], [kd_skpd], [kd_rek6], [nm_rek6], sum(nilai) as nilai_ang from [trdrka] where [jns_ang] = ? and [kd_skpd] = ? group by [kd_skpd], [kd_sub_kegiatan], [nm_sub_kegiatan], [kd_rek6], [nm_rek6]
                                ) as a

                                    left join (select [kd_sub_kegiatan] as [giat], [kd_skpd], [kd_rek6], SUM($jenis) as nilai_kas from [trdskpd_ro] where [kd_skpd] = ? group by [kd_skpd], [kd_sub_kegiatan], [kd_rek6]
                                ) as [b] on [a].[giat] = [b].[giat] and [a].[kd_skpd] = [b].[kd_skpd] and [a].[kd_rek6] = [b].[kd_rek6] where ISNULL(b.nilai_kas,0) <> a.nilai_ang order by [hasil] asc, [a].[giat] asc", [$jenis_anggaran, $kd_skpd, $kd_skpd]);

        $data = [
            'nama_angkas' => DB::table('tb_status_angkas')->select('nama')->where(['kode' => $jenis_rak])->first(),
            'nama_skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'cek_rak' => $cek_rak
        ];

        $view = view('skpd.cek_rak.cetakan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'excel') {
            header("Cache-Control:no-cache,no-store,must-revalidate");
            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    // CETAK RAK PER SKPD
    public function rincianPerSkpd()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => get_skpd($kd_skpd),
            'daftar_ttd2' => DB::table('ms_ttd')->select('nip', 'nama', 'id')->where(['kode' => 'bud'])->get(),
            'data_skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')->where('kd_skpd', $kd_skpd)->first(),
        ];

        return view('skpd.cetak_rak.per_skpd.cetak')->with($data);
    }

    // get skpd by radio
    public function cariSkpd(Request $request)
    {
        $type       = Auth::user()->is_admin;
        $jenis      = $request->jenis;
        $kd_skpd    = $request->kd_skpd;
        $kd_org     = substr($kd_skpd, 0, 17);
        if ($type == '1') {
            if ($jenis == 'skpd') {
                $data   = DB::table('ms_skpd')->select(DB::raw("kd_skpd AS kd_skpd"), DB::raw("nm_skpd AS nm_skpd"))->whereRaw("right(kd_skpd,4)='0000'")->orderBy('kd_skpd')->get();
            } else {
                $data   = DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->orderBy('kd_skpd')->get();
            }
        } else {
            if ($jenis == 'unit') {
                if (substr($kd_skpd, 18, 4) == '0000') {
                    $data   = DB::table('ms_skpd')->where(DB::raw("LEFT(kd_skpd,17)"), '=', $kd_org)->select(DB::raw("kd_skpd AS kd_skpd"), DB::raw("nm_skpd AS nm_skpd"))->get();
                } else {
                    $data   = DB::table('ms_skpd')->where(DB::raw("LEFT(kd_skpd,17)"), '=', $kd_org)->select(DB::raw("kd_skpd AS kd_skpd"), DB::raw("nm_skpd AS nm_skpd"))->whereRaw("right(kd_skpd,4)='0000'")->get();
                }
            } else {
                $data   = DB::table('ms_skpd')->where(DB::raw("kd_skpd"), '=', $kd_skpd)->select('kd_skpd', 'nm_skpd')->get();
            }
        }

        return response()->json($data);
    }

    public function cetakPerSkpd(Request $request)
    {
        $kd_skpd            = $request->kd_skpd;
        $jenis_anggaran     = $request->jenis_anggaran;
        $jenis_rak          = $request->jenis_rak;
        $ttd1               = $request->ttd1;
        $tanggal_ttd        = $request->tanggal_ttd;
        $jenis_print        = $request->jenis_print;
        //$jenis_cetakan      = $request->jenis_cetakan;
        $hidden             = $request->hidden;
        $kd_organisasi      = substr($kd_skpd, 0, 22);

        $jenis = "nilai_" . $jenis_rak;
        // per skpd
            $angkas3 = DB::table('trdskpd_ro as a')
                ->selectRaw("left(a.kd_sub_kegiatan,12) as kd_sub_kegiatan,
            case when bulan=1 then sum($jenis) else 0 end as jan,
            case when bulan=2 then sum($jenis) else 0 end as feb,
            case when bulan=3 then sum($jenis) else 0 end as mar,
            case when bulan=4 then sum($jenis) else 0 end as apr,
            case when bulan=5 then sum($jenis) else 0 end as mei,
            case when bulan=6 then sum($jenis) else 0 end as jun,
            case when bulan=7 then sum($jenis) else 0 end as jul,
            case when bulan=8 then sum($jenis) else 0 end as ags,
            case when bulan=9 then sum($jenis) else 0 end as sep,
            case when bulan=10 then sum($jenis) else 0 end as okt,
            case when bulan=11 then sum($jenis) else 0 end as nov,
            case when bulan=12 then sum($jenis) else 0 end as des")
                ->whereRaw("left(a.kd_skpd,22) = '$kd_organisasi'")
                ->whereRaw("right(a.kd_sub_kegiatan,10) NOT IN ('00.0.00.04')")
                ->groupBy(DB::raw("LEFT(kd_sub_kegiatan,12)"), 'a.bulan');

            $angkas4 = DB::table(DB::raw("({$angkas3->toSql()}) as sub"))
                ->select('kd_sub_kegiatan as giat', DB::raw("(SELECT DISTINCT nm_kegiatan FROM ms_kegiatan WHERE left(kd_kegiatan,12)=left(sub.kd_sub_kegiatan,12)) as nm_giat"), DB::raw("ISNULL(SUM(jan),0) as jan"), DB::raw("ISNULL(SUM(feb),0) as feb"), DB::raw("ISNULL(SUM(mar),0) as mar"), DB::raw("ISNULL(SUM(apr),0) as apr"), DB::raw("ISNULL(SUM(mei),0) as mei"), DB::raw("ISNULL(SUM(jun),0) as jun"), DB::raw("ISNULL(SUM(jul),0) as jul"), DB::raw("ISNULL(SUM(ags),0) as ags"), DB::raw("ISNULL(SUM(sep),0) as sep"), DB::raw("ISNULL(SUM(okt),0) as okt"), DB::raw("ISNULL(SUM(nov),0) as nov"), DB::raw("ISNULL(SUM(des),0) as des"))
                ->selectRaw("(SELECT sum(nilai) from trdrka where left(kd_sub_kegiatan,12)=left(sub.kd_sub_kegiatan,12) and left(kd_skpd,22)=? and jns_ang=?) as ang", [$kd_organisasi, $jenis_anggaran])
                ->mergeBindings($angkas3)
                ->groupBy('kd_sub_kegiatan');

            $angkas = DB::table(DB::raw("({$angkas4->toSql()}) as sub"))
                ->mergeBindings($angkas4)
                ->orderBy('giat')
                ->get();

            $angkas_rek1 = DB::table('trdskpd_ro as a')
                ->selectRaw("left(a.kd_rek6,2) as kode,
            case when bulan=1 then sum($jenis) else 0 end as jan,
            case when bulan=2 then sum($jenis) else 0 end as feb,
            case when bulan=3 then sum($jenis) else 0 end as mar,
            case when bulan=4 then sum($jenis) else 0 end as apr,
            case when bulan=5 then sum($jenis) else 0 end as mei,
            case when bulan=6 then sum($jenis) else 0 end as jun,
            case when bulan=7 then sum($jenis) else 0 end as jul,
            case when bulan=8 then sum($jenis) else 0 end as ags,
            case when bulan=9 then sum($jenis) else 0 end as sep,
            case when bulan=10 then sum($jenis) else 0 end as okt,
            case when bulan=11 then sum($jenis) else 0 end as nov,
            case when bulan=12 then sum($jenis) else 0 end as des")
                ->whereRaw("left(a.kd_skpd,22) = '$kd_organisasi'")
                ->whereRaw("left(a.kd_rek6,1)='4'")
                ->groupByRaw('left(a.kd_rek6,2), a.bulan');

            $angkas_rek2 = DB::table(DB::raw("({$angkas_rek1->toSql()}) as sub"))
                ->select('kode as rek', DB::raw("(SELECT nm_rek2 FROM ms_rek2 WHERE kd_rek2=sub.kode) as nama_rek"), DB::raw("ISNULL(SUM(jan),0) as jan"), DB::raw("ISNULL(SUM(feb),0) as feb"), DB::raw("ISNULL(SUM(mar),0) as mar"), DB::raw("ISNULL(SUM(apr),0) as apr"), DB::raw("ISNULL(SUM(mei),0) as mei"), DB::raw("ISNULL(SUM(jun),0) as jun"), DB::raw("ISNULL(SUM(jul),0) as jul"), DB::raw("ISNULL(SUM(ags),0) as ags"), DB::raw("ISNULL(SUM(sep),0) as sep"), DB::raw("ISNULL(SUM(okt),0) as okt"), DB::raw("ISNULL(SUM(nov),0) as nov"), DB::raw("ISNULL(SUM(des),0) as des"))
                ->selectRaw("(SELECT sum(nilai) from trdrka where left(kd_rek6,2)=sub.kode and left(kd_skpd,22)=? and jns_ang=?) as ang", [$kd_organisasi, $jenis_anggaran])
                ->mergeBindings($angkas_rek1)
                ->groupBy('kode');

            $angkas_rek3 = DB::table('trdskpd_ro as a')
                ->selectRaw("left(a.kd_rek6,4) as kode,
            case when bulan=1 then sum($jenis) else 0 end as jan,
            case when bulan=2 then sum($jenis) else 0 end as feb,
            case when bulan=3 then sum($jenis) else 0 end as mar,
            case when bulan=4 then sum($jenis) else 0 end as apr,
            case when bulan=5 then sum($jenis) else 0 end as mei,
            case when bulan=6 then sum($jenis) else 0 end as jun,
            case when bulan=7 then sum($jenis) else 0 end as jul,
            case when bulan=8 then sum($jenis) else 0 end as ags,
            case when bulan=9 then sum($jenis) else 0 end as sep,
            case when bulan=10 then sum($jenis) else 0 end as okt,
            case when bulan=11 then sum($jenis) else 0 end as nov,
            case when bulan=12 then sum($jenis) else 0 end as des")
                ->whereRaw("left(a.kd_skpd,22) = '$kd_organisasi'")
                ->whereRaw("left(a.kd_rek6,1)='4'")
                ->groupByRaw('left(a.kd_rek6,4), a.bulan');

            $angkas_rek4 = DB::table(DB::raw("({$angkas_rek3->toSql()}) as sub"))
                ->select('kode as rek', DB::raw("(SELECT nm_rek3 FROM ms_rek3 WHERE kd_rek3=sub.kode) as nama_rek"), DB::raw("ISNULL(SUM(jan),0) as jan"), DB::raw("ISNULL(SUM(feb),0) as feb"), DB::raw("ISNULL(SUM(mar),0) as mar"), DB::raw("ISNULL(SUM(apr),0) as apr"), DB::raw("ISNULL(SUM(mei),0) as mei"), DB::raw("ISNULL(SUM(jun),0) as jun"), DB::raw("ISNULL(SUM(jul),0) as jul"), DB::raw("ISNULL(SUM(ags),0) as ags"), DB::raw("ISNULL(SUM(sep),0) as sep"), DB::raw("ISNULL(SUM(okt),0) as okt"), DB::raw("ISNULL(SUM(nov),0) as nov"), DB::raw("ISNULL(SUM(des),0) as des"))
                ->selectRaw("(SELECT sum(nilai) from trdrka where left(kd_rek6,4)=sub.kode and left(kd_skpd,22)=? and jns_ang=?) as ang", [$kd_organisasi, $jenis_anggaran])
                ->mergeBindings($angkas_rek3)
                ->groupBy('kode')
                ->unionAll($angkas_rek2);

            $angkas_rek = DB::table(DB::raw("({$angkas_rek4->toSql()}) as sub"))
                ->mergeBindings($angkas_rek4)
                ->orderBy('rek')
                ->get();
            // per unit


        $data = [
            'nama_angkas'   => DB::table('tb_status_angkas')->select('nama')->where(['kode' => $jenis_rak])->first(),
            'nama_skpd'     => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'data_giat'     => $angkas,
            'data_rek'      => $angkas_rek,
            'ttd1'          => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['nip' => $ttd1, 'kd_skpd' => $kd_skpd])->first(),
            'tanggal'       => $tanggal_ttd,
            'hidden'        => $hidden,
            'header'        => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first()
        ];

        $view = view('skpd.cetak_rak.per_skpd.cetakan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'excel') {
            header("Cache-Control:no-cache,no-store,must-revalidate");
            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    public function RakPemda()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'jenis_anggaran'    => DB::table('tb_status_anggaran')->where(['status_aktif' => '1'])->get(),
            'daftar_ttd2' => DB::table('ms_ttd')->select('nip', 'nama', 'id', 'jabatan')->where(['kode' => 'bud'])->get(),
            'data_skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')->where('kd_skpd', $kd_skpd)->first(),
        ];

        return view('skpd.cetak_rak.pemda.cetak')->with($data);
    }

    public function cetakPemda(Request $request)
    {
        $jenis_anggaran     = $request->jenis_anggaran;
        $jenis_rak          = $request->jenis_rak;
        $ttd2               = $request->ttd2;
        $tanggal_ttd        = $request->tanggal_ttd;
        $jenis_print        = $request->jenis_print;
        $hidden             = $request->hidden;
        $jenis              = 'nilai_' . $jenis_rak;
        $kd_skpd            = Auth::user()->kd_skpd;
        // per skpd

        $pendapatan = DB::select("SELECT kode giat, (select nm_rek2 from ms_rek2 WHERE kd_rek2=xxx.kode) nm_giat,
                                    (select sum(nilai) from trdrka where left(kd_rek6,2)=xxx.kode and jns_ang=?)as ang,
                                    isnull(sum(jan),0) jan, isnull(sum(feb),0) feb, isnull(sum(mar),0) mar, isnull(sum(apr),0) apr, isnull(sum(mei),0) mei, isnull(sum(jun),0) jun,
                                    isnull(sum(jul),0) jul, isnull(sum(ags),0) ags, isnull(sum(sep),0) sep, isnull(sum(okt),0) okt, isnull(sum(nov),0) nov, isnull(sum(des),0) des
                                    from (
                                    -- 1
                                    select left(kd_rek6,2)as kode,
                                    case when bulan=1 then sum($jenis) else 0 end as jan,
                                    case when bulan=2 then sum($jenis) else 0 end as feb,
                                    case when bulan=3 then sum($jenis) else 0 end as mar,
                                    case when bulan=4 then sum($jenis) else 0 end as apr,
                                    case when bulan=5 then sum($jenis) else 0 end as mei,
                                    case when bulan=6 then sum($jenis) else 0 end as jun,
                                    case when bulan=7 then sum($jenis) else 0 end as jul,
                                    case when bulan=8 then sum($jenis) else 0 end as ags,
                                    case when bulan=9 then sum($jenis) else 0 end as sep,
                                    case when bulan=10 then sum($jenis) else 0 end as okt,
                                    case when bulan=11 then sum($jenis) else 0 end as nov,
                                    case when bulan=12 then sum($jenis) else 0 end as des
                                    from trdskpd_ro a inner join
                                    (select left(kd_rek6,2) oke from trdrka
                                    GROUP by left(kd_rek6,2)) b
                                    on b.oke=left(a.kd_rek6,2) where (left(a.kd_rek6,1) = '4' OR left(a.kd_rek6,2) ='61')
                                    GROUP BY left(kd_rek6,2), bulan
                                    -- 1
                                    )xxx
                                    GROUP BY kode

                                    UNION all
                                    /*kegiatan*/
                                    SELECT kode giat, (select DISTINCT nm_rek3 from ms_rek3 WHERE kd_rek3=xxx.kode) nm_rek,
                                    (select sum(nilai) from trdrka where left(kd_rek6,4)=xxx.kode and jns_ang=?)as ang,
                                    sum(jan) jan, sum(feb) feb, sum(mar) mar, sum(apr) apr, sum(mei) mei, sum(jun) jun,
                                    sum(jul) jul, sum(ags) ags, sum(sep) sep, sum(okt) okt, sum(nov) nov, sum(des) des
                                    from (
                                    select left(kd_rek6,4) kode,
                                    case when bulan=1 then sum($jenis) else 0 end as jan,
                                    case when bulan=2 then sum($jenis) else 0 end as feb,
                                    case when bulan=3 then sum($jenis) else 0 end as mar,
                                    case when bulan=4 then sum($jenis) else 0 end as apr,
                                    case when bulan=5 then sum($jenis) else 0 end as mei,
                                    case when bulan=6 then sum($jenis) else 0 end as jun,
                                    case when bulan=7 then sum($jenis) else 0 end as jul,
                                    case when bulan=8 then sum($jenis) else 0 end as ags,
                                    case when bulan=9 then sum($jenis) else 0 end as sep,
                                    case when bulan=10 then sum($jenis) else 0 end as okt,
                                    case when bulan=11 then sum($jenis) else 0 end as nov,
                                    case when bulan=12 then sum($jenis) else 0 end as des from trdskpd_ro a inner join
                                    (select left(kd_rek6,4) oke from trdrka GROUP by left(kd_rek6,4)) b
                                    on b.oke=left(a.kd_rek6,4) where
                                    (left(a.kd_rek6,1) = '4' OR left(a.kd_rek6,2) ='61')
                                    GROUP BY left(kd_rek6,4), bulan)xxx
                                    GROUP BY kode

                                    ORDER BY kode", [$jenis_anggaran, $jenis_anggaran]);

        $belanja = DB::select("SELECT kode giat, (select nm_rek2 from ms_rek2 WHERE kd_rek2=xxx.kode) nm_giat,
                                (select sum(nilai) from trdrka where left(kd_rek6,2)=xxx.kode and jns_ang=?)as ang,
                                isnull(sum(jan),0) jan, isnull(sum(feb),0) feb, isnull(sum(mar),0) mar, isnull(sum(apr),0) apr, isnull(sum(mei),0) mei, isnull(sum(jun),0) jun,
                                isnull(sum(jul),0) jul, isnull(sum(ags),0) ags, isnull(sum(sep),0) sep, isnull(sum(okt),0) okt, isnull(sum(nov),0) nov, isnull(sum(des),0) des
                                from (
                                -- 1
                                select left(kd_rek6,2)as kode,
                                case when bulan=1 then sum($jenis) else 0 end as jan,
                                case when bulan=2 then sum($jenis) else 0 end as feb,
                                case when bulan=3 then sum($jenis) else 0 end as mar,
                                case when bulan=4 then sum($jenis) else 0 end as apr,
                                case when bulan=5 then sum($jenis) else 0 end as mei,
                                case when bulan=6 then sum($jenis) else 0 end as jun,
                                case when bulan=7 then sum($jenis) else 0 end as jul,
                                case when bulan=8 then sum($jenis) else 0 end as ags,
                                case when bulan=9 then sum($jenis) else 0 end as sep,
                                case when bulan=10 then sum($jenis) else 0 end as okt,
                                case when bulan=11 then sum($jenis) else 0 end as nov,
                                case when bulan=12 then sum($jenis) else 0 end as des
                                from trdskpd_ro a inner join
                                (select left(kd_rek6,2) oke from trdrka
                                GROUP by left(kd_rek6,2)) b
                                on b.oke=left(a.kd_rek6,2) where (left(a.kd_rek6,1) = '5' OR left(a.kd_rek6,2) ='62')
                                GROUP BY left(kd_rek6,2), bulan
                                -- 1
                                )xxx
                                GROUP BY kode

                                UNION all
                                /*kegiatan*/
                                SELECT kode giat, (select DISTINCT nm_rek3 from ms_rek3 WHERE kd_rek3=xxx.kode) nm_rek,
                                (select sum(nilai) from trdrka where left(kd_rek6,4)=xxx.kode and jns_ang=?)as ang,
                                sum(jan) jan, sum(feb) feb, sum(mar) mar, sum(apr) apr, sum(mei) mei, sum(jun) jun,
                                sum(jul) jul, sum(ags) ags, sum(sep) sep, sum(okt) okt, sum(nov) nov, sum(des) des
                                from (
                                select left(kd_rek6,4) kode,
                                case when bulan=1 then sum($jenis) else 0 end as jan,
                                case when bulan=2 then sum($jenis) else 0 end as feb,
                                case when bulan=3 then sum($jenis) else 0 end as mar,
                                case when bulan=4 then sum($jenis) else 0 end as apr,
                                case when bulan=5 then sum($jenis) else 0 end as mei,
                                case when bulan=6 then sum($jenis) else 0 end as jun,
                                case when bulan=7 then sum($jenis) else 0 end as jul,
                                case when bulan=8 then sum($jenis) else 0 end as ags,
                                case when bulan=9 then sum($jenis) else 0 end as sep,
                                case when bulan=10 then sum($jenis) else 0 end as okt,
                                case when bulan=11 then sum($jenis) else 0 end as nov,
                                case when bulan=12 then sum($jenis) else 0 end as des from trdskpd_ro a inner join
                                (select left(kd_rek6,4) oke from trdrka GROUP by left(kd_rek6,4)) b
                                on b.oke=left(a.kd_rek6,4) where
                                (left(a.kd_rek6,1) = '5' OR left(a.kd_rek6,2) ='62')
                                GROUP BY left(kd_rek6,4), bulan)xxx
                                GROUP BY kode

                                ORDER BY kode", [$jenis_anggaran, $jenis_anggaran]);





        $data = [
            'nama_angkas'   => DB::table('tb_status_angkas')->select('nama')->where(['kode' => $jenis_rak])->first(),
            'nama_skpd'     => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'pendapatan'    => $pendapatan,
            'belanja'       => $belanja,
            'ttd2'          => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['nip' => $ttd2, 'kode' => 'bud'])->first(),
            'tanggal'       => $tanggal_ttd,
            'hidden'        => $hidden,
            'header'        => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first()
        ];

        $view = view('skpd.cetak_rak.pemda.cetakan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'excel') {
            header("Cache-Control:no-cache,no-store,must-revalidate");
            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    // RAK PENDAPATAN
    // CETAK RAK PENDAPATAN
    public function pendapatanIndex()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => get_skpd($kd_skpd),
            'daftar_ttd2' => DB::table('ms_ttd')->select('nip', 'nama', 'id')->where(['kode' => 'bud'])->get(),
        ];

        return view('skpd.cetak_rak.pendapatan.cetak')->with($data);
    }

    public function cetakRakPendapatan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jenis_anggaran = $request->jenis_anggaran;
        $jenis_rak = $request->jenis_rak;
        $ttd1 = $request->ttd1;
        $tanggal_ttd = $request->tanggal_ttd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $jenis_print = $request->jenis_print;
        $margin = $request->margin;
        $hidden = $request->hidden;
        if ($margin == '') {
            $margin = 10;
        } else {
            $margin = $margin;
        }
        $jenis = "nilai_" . $jenis_rak;

        $join1 = DB::table('trdrka')->select('kd_sub_kegiatan', 'kd_skpd', 'kd_rek6')->where(['jns_ang' => $jenis_anggaran])->groupBy('kd_sub_kegiatan', 'kd_skpd', 'kd_rek6');

        $angkas1 = DB::table('trdskpd_ro as a')->joinSub($join1, 'b', function ($join) {
            $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            $join->on('a.kd_rek6', '=', 'b.kd_rek6');
        })->selectRaw("a.kd_sub_kegiatan,a.kd_rek6,
            case when bulan=1 then sum($jenis) else 0 end as jan,
            case when bulan=2 then sum($jenis) else 0 end as feb,
            case when bulan=3 then sum($jenis) else 0 end as mar,
            case when bulan=4 then sum($jenis) else 0 end as apr,
            case when bulan=5 then sum($jenis) else 0 end as mei,
            case when bulan=6 then sum($jenis) else 0 end as jun,
            case when bulan=7 then sum($jenis) else 0 end as jul,
            case when bulan=8 then sum($jenis) else 0 end as ags,
            case when bulan=9 then sum($jenis) else 0 end as sep,
            case when bulan=10 then sum($jenis) else 0 end as okt,
            case when bulan=11 then sum($jenis) else 0 end as nov,
            case when bulan=12 then sum($jenis) else 0 end as des")->where(['a.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan])->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6', 'a.bulan');

        $angkas = DB::table(DB::raw("({$angkas1->toSql()}) as sub"))
            ->select(DB::raw("(kd_sub_kegiatan+'.') as giat"), 'kd_rek6', DB::raw("(SELECT nm_rek6 FROM ms_rek6 WHERE kd_rek6=sub.kd_rek6) as nm_rek"), DB::raw("ISNULL(SUM(jan),0) as jan"), DB::raw("ISNULL(SUM(feb),0) as feb"), DB::raw("ISNULL(SUM(mar),0) as mar"), DB::raw("ISNULL(SUM(apr),0) as apr"), DB::raw("ISNULL(SUM(mei),0) as mei"), DB::raw("ISNULL(SUM(jun),0) as jun"), DB::raw("ISNULL(SUM(jul),0) as jul"), DB::raw("ISNULL(SUM(ags),0) as ags"), DB::raw("ISNULL(SUM(sep),0) as sep"), DB::raw("ISNULL(SUM(okt),0) as okt"), DB::raw("ISNULL(SUM(nov),0) as nov"), DB::raw("ISNULL(SUM(des),0) as des"))
            ->mergeBindings($angkas1)
            ->groupBy('kd_sub_kegiatan', 'kd_rek6')
            ->orderBy('kd_sub_kegiatan')
            ->get();

        $data = [
            'nama_angkas' => DB::table('tb_status_angkas')->select('nama')->where(['kode' => $jenis_rak])->first(),
            'nama_skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'data_giat' => $angkas,
            'kd_sub_kegiatan' => $kd_sub_kegiatan,
            'jenis_anggaran' => $jenis_anggaran,
            'kd_skpd' => $kd_skpd,
            'ttd1' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['nip' => $ttd1])->first(),
            'tanggal' => $tanggal_ttd,
            'sub_header' => DB::table('ms_skpd as a')->select(DB::raw("left(kd_skpd,1) as urusan"), DB::raw("(SELECT nm_urusan FROM ms_urusan WHERE kd_urusan=left(a.kd_skpd,1)) as nmurusan"), DB::raw("left(kd_skpd,4) as bidang"), DB::raw("(SELECT nm_bidang_urusan FROM ms_bidang_urusan WHERE kd_bidang_urusan=left(a.kd_skpd,4)) as nmbidang"), DB::raw("left(kd_skpd,17) as org"), DB::raw("(SELECT nm_skpd FROM ms_skpd WHERE left(kd_skpd,17)=left(a.kd_skpd,17) AND right(kd_skpd,4)='0000') as nmorg"), 'kd_skpd as unit', 'nm_skpd as nmunit')->where(['kd_skpd' => $kd_skpd])->first(),

            'sub_header1' => DB::table('trdrka as a')->select(DB::raw("left(kd_sub_kegiatan,7) as program"), DB::raw("(SELECT nm_program FROM ms_program WHERE kd_program=left(a.kd_sub_kegiatan,7)) as nmprogram"), DB::raw("left(kd_sub_kegiatan,12) as kegiatan"), DB::raw("(SELECT nm_kegiatan FROM ms_kegiatan WHERE kd_kegiatan=left(a.kd_sub_kegiatan,12)) as nmkegiatan"), 'kd_sub_kegiatan as subkegiatan', 'nm_sub_kegiatan as nmsubkegiatan', 'kd_skpd', DB::raw("SUM(nilai) as ang"))->where(['kd_skpd' => $kd_skpd, 'kd_sub_kegiatan' => $kd_sub_kegiatan, 'jns_ang' => $jenis_anggaran])->groupBy('kd_skpd', 'kd_sub_kegiatan', 'nm_sub_kegiatan')->first(),
            'hidden' => $hidden
        ];

        $view = view('skpd.cetak_rak.per_sub_rincian_objek.cetakan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')

                ->setOption('margin-top', $margin);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'excel') {
            header("Cache-Control:no-cache,no-store,must-revalidate");
            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }
}
