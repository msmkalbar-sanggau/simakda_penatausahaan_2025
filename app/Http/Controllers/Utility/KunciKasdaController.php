<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KunciKasdaController extends Controller
{
    public function index()
    {
        $data = [
            'skpd' => DB::table('tr_kunci')
                ->selectRaw("kd_skpd,tgl_kunci")
                ->orderBy('kd_skpd')
                ->get(),
        ];

        return view('bud.kunci_kasda.index')->with($data);
    }

    public function kunci(Request $request)
    {
        $pilihan = $request->pilihan;
        $kd_skpd = $request->kd_skpd;
        $tgl_akhir = $request->tgl_akhir;
        $username = Auth::user()->nama;

        DB::beginTransaction();
        try {
            if ($pilihan == '1') {
                DB::update("UPDATE tr_kunci set tgl_kunci=?, user_kunci=?", [$tgl_akhir, $username]);
            } elseif ($pilihan == '2') {
                DB::update("UPDATE tr_kunci set tgl_kunci=?, user_kunci=? where kd_skpd=?", [$tgl_akhir, $username, $kd_skpd]);
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
