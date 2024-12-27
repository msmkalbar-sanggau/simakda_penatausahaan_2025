<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class PengesahanAngkasController extends Controller
{
    public function index()
    {
        if (Gate::denies('akses')) {
            return abort(401);
        }
        return view('penatausahaan.pengesahan_angkas.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        if (Auth::user()->is_admin == 2) {
            $data = DB::table('status_angkas')
                ->where(['kd_skpd' => $kd_skpd])
                ->orderBy('kd_skpd')
                ->get();
        } else {
            $data = DB::table('status_angkas')
                ->orderBy('kd_skpd')
                ->get();
        }
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="javascript:void(0);" onclick="detail(\'' . $row->kd_skpd . '\',\'' . $row->nm_skpd . '\',\'' . $row->murni . '\',\'' . $row->murni_geser1 . '\',\'' . $row->murni_geser2 . '\',\'' . $row->murni_geser3 . '\',\'' . $row->murni_geser4 . '\',\'' . $row->murni_geser5 . '\',\'' . $row->sempurna1 . '\',\'' . $row->sempurna1_geser1 . '\',\'' . $row->sempurna1_geser2 . '\',\'' . $row->sempurna1_geser3 . '\',\'' . $row->sempurna1_geser4 . '\',\'' . $row->sempurna1_geser5 . '\',\'' . $row->sempurna2 . '\',\'' . $row->sempurna2_geser1 . '\',\'' . $row->sempurna2_geser2 . '\',\'' . $row->sempurna2_geser3 . '\',\'' . $row->sempurna2_geser4 . '\',\'' . $row->sempurna2_geser5 . '\',\'' . $row->sempurna3 . '\',\'' . $row->sempurna3_geser1 . '\',\'' . $row->sempurna3_geser2 . '\',\'' . $row->sempurna3_geser3 . '\',\'' . $row->sempurna3_geser4 . '\',\'' . $row->sempurna3_geser5 . '\',\'' . $row->sempurna4 . '\',\'' . $row->sempurna4_geser1 . '\',\'' . $row->sempurna4_geser2 . '\',\'' . $row->sempurna4_geser3 . '\',\'' . $row->sempurna4_geser4 . '\',\'' . $row->sempurna4_geser5 . '\',\'' . $row->sempurna5 . '\',\'' . $row->sempurna5_geser1 . '\',\'' . $row->sempurna5_geser2 . '\',\'' . $row->sempurna5_geser3 . '\',\'' . $row->sempurna5_geser4 . '\',\'' . $row->sempurna5_geser5 . '\',\'' . $row->ubah . '\',\'' . $row->ubah2 . '\');" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="uil-list-ul"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function simpan(Request $request)
    {
        if (Gate::denies('akses')) {
            return response()->json([
                'message' => '3'
            ]);
        }

        $data = $request->data;

        DB::beginTransaction();
        try {
            DB::table('status_angkas')
                ->where(['kd_skpd' => $data['kd_skpd']])
                ->update([
                    'murni' => $data['angkas_murni'],
                    'murni_geser1' => $data['angkas_murni_geser1'],
                    'murni_geser2' => $data['angkas_murni_geser2'],
                    'murni_geser3' => $data['angkas_murni_geser3'],
                    'murni_geser4' => $data['angkas_murni_geser4'],
                    'murni_geser5' => $data['angkas_murni_geser5'],
                    'sempurna1' => $data['angkas_sempurna1'],
                    'sempurna1_geser1' => $data['angkas_sempurna1_geser1'],
                    'sempurna1_geser2' => $data['angkas_sempurna1_geser2'],
                    'sempurna1_geser3' => $data['angkas_sempurna1_geser3'],
                    'sempurna1_geser4' => $data['angkas_sempurna1_geser4'],
                    'sempurna1_geser5' => $data['angkas_sempurna1_geser5'],
                    'sempurna2' => $data['angkas_sempurna2'],
                    'sempurna2_geser1' => $data['angkas_sempurna2_geser1'],
                    'sempurna2_geser2' => $data['angkas_sempurna2_geser2'],
                    'sempurna2_geser3' => $data['angkas_sempurna2_geser3'],
                    'sempurna2_geser4' => $data['angkas_sempurna2_geser4'],
                    'sempurna2_geser5' => $data['angkas_sempurna2_geser5'],
                    'sempurna3' => $data['angkas_sempurna3'],
                    'sempurna3_geser1' => $data['angkas_sempurna3_geser1'],
                    'sempurna3_geser2' => $data['angkas_sempurna3_geser2'],
                    'sempurna3_geser3' => $data['angkas_sempurna3_geser3'],
                    'sempurna3_geser4' => $data['angkas_sempurna3_geser4'],
                    'sempurna3_geser5' => $data['angkas_sempurna3_geser5'],
                    'sempurna4' => $data['angkas_sempurna4'],
                    'sempurna4_geser1' => $data['angkas_sempurna4_geser1'],
                    'sempurna4_geser2' => $data['angkas_sempurna4_geser2'],
                    'sempurna4_geser3' => $data['angkas_sempurna4_geser3'],
                    'sempurna4_geser4' => $data['angkas_sempurna4_geser4'],
                    'sempurna4_geser5' => $data['angkas_sempurna4_geser5'],
                    'sempurna5' => $data['angkas_sempurna5'],
                    'sempurna5_geser1' => $data['angkas_sempurna5_geser1'],
                    'sempurna5_geser2' => $data['angkas_sempurna5_geser2'],
                    'sempurna5_geser3' => $data['angkas_sempurna5_geser3'],
                    'sempurna5_geser4' => $data['angkas_sempurna5_geser4'],
                    'sempurna5_geser5' => $data['angkas_sempurna5_geser5'],
                    'ubah' => $data['angkas_ubah'],
                    'ubah2' => $data['angkas_ubah2'],
                    'user_sah' => Auth::user()->nama,
                    'last_update' => date("Y-m-d H:i:s")
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
}
