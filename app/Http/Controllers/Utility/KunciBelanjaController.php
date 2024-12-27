<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KunciBelanjaController extends Controller
{
    public function index()
    {
        $data = [
            'skpd' => DB::table('ms_skpd')
                ->orderBy('kd_skpd')
                ->get(),
        ];

        return view('bud.kunci_belanja.index')->with($data);
    }

    public function load()
    {
        $data = DB::select("SELECT '-' as kd_skpd, 'SEMUA SKPD' as nm_skpd, 0 as kunci_tagih,0 as kunci_spp,0 as kunci_spp_tu, 0 as kunci_spp_gu, 0 as kunci_spp_ls, 0 as kunci_spm
        UNION ALL
        select kd_skpd,nm_skpd,kunci_tagih,kunci_spp,kunci_spp_tu,kunci_spp_gu,kunci_spp_ls,kunci_spm from ms_skpd order by kd_skpd");

        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function kegiatan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;

        $data = DB::select("SELECT a.kd_sub_kegiatan,a.nm_sub_kegiatan FROM trskpd a where a.kd_skpd=?
                   and a.status_sub_kegiatan=? group by a.kd_sub_kegiatan,a.nm_sub_kegiatan order by a.kd_sub_kegiatan", [$kd_skpd, '1']);

        return response()->json($data);
    }

    public function rekening(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        $data = DB::select("SELECT kd_sub_kegiatan,nm_sub_kegiatan,kd_rek6,nm_rek6,status_aktif FROM trdrka a where kd_skpd=? and kd_sub_kegiatan=? group by kd_sub_kegiatan,nm_sub_kegiatan,kd_rek6,nm_rek6,status_aktif order by kd_sub_kegiatan,kd_rek6", [$kd_skpd, $kd_sub_kegiatan]);

        return response()->json($data);
    }

    public function kunci(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kode_rekening = $request->kode_rekening;
        $jenis = $request->jenis;

        DB::beginTransaction();
        try {
            if ($jenis == 'aktifkan_skpd') {
                DB::table('trdrka')
                    ->where(['kd_skpd' => $kd_skpd])
                    ->update([
                        'status_aktif' => '1'
                    ]);
            }
            if ($jenis == 'nonaktifkan_skpd') {
                DB::table('trdrka')
                    ->where(['kd_skpd' => $kd_skpd])
                    ->update([
                        'status_aktif' => '0'
                    ]);
            }
            if ($jenis == 'aktifkan_kegiatan') {
                DB::table('trdrka')
                    ->where(['kd_skpd' => $kd_skpd, 'kd_sub_kegiatan' => $kd_sub_kegiatan])
                    ->update([
                        'status_aktif' => '1'
                    ]);
            }
            if ($jenis == 'nonaktifkan_kegiatan') {
                DB::table('trdrka')
                    ->where(['kd_skpd' => $kd_skpd, 'kd_sub_kegiatan' => $kd_sub_kegiatan])
                    ->update([
                        'status_aktif' => '0'
                    ]);
            }
            if ($jenis == 'aktifkan_rekening') {
                DB::table('trdrka')
                    ->where(['kd_skpd' => $kd_skpd, 'kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kode_rekening])
                    ->update([
                        'status_aktif' => '1'
                    ]);
            }
            if ($jenis == 'nonaktifkan_rekening') {
                DB::table('trdrka')
                    ->where(['kd_skpd' => $kd_skpd, 'kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kode_rekening])
                    ->update([
                        'status_aktif' => '0'
                    ]);
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
