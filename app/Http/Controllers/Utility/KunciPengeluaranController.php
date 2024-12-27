<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KunciPengeluaranController extends Controller
{
    public function index()
    {
        return view('bud.kunci_pengeluaran.index');
    }

    public function load()
    {
        $data = DB::select("SELECT '-' as kd_skpd, 'SEMUA SKPD' as nm_skpd, 0 as kunci_kontrak, 0 as kunci_tagih, 0 as kunci_spp, 0 as kunci_spp_gu, 0 as kunci_spp_ls, 0 as kunci_spp_gu_nihil, 0 as kunci_spm, 0 as kunci_jurnal UNION ALL select kd_skpd,nm_skpd,kunci_kontrak, kunci_tagih,kunci_spp,kunci_spp_gu,kunci_spp_ls,kunci_spp_gu_nihil,kunci_spm, kunci_jurnal from ms_skpd order by kd_skpd");

        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function kunci(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kunci = $request->kunci;
        $jenis = $request->jenis;

        $jenis = 'kunci_' . $jenis;

        DB::beginTransaction();
        try {
            if ($kd_skpd == '-') {
                if ($kunci == 1) {
                    DB::table('ms_skpd')
                        ->update([
                            $jenis => '0'
                        ]);
                } else {
                    DB::table('ms_skpd')
                        ->update([
                            $jenis => '1'
                        ]);
                }
            } else {
                if ($kunci == 1) {
                    DB::table('ms_skpd')
                        ->where(['kd_skpd' => $kd_skpd])
                        ->update([
                            $jenis => '0'
                        ]);
                } else {
                    DB::table('ms_skpd')
                        ->where(['kd_skpd' => $kd_skpd])
                        ->update([
                            $jenis => '1'
                        ]);
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
}
