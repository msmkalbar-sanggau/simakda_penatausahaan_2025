<?php

namespace App\Http\Controllers\spd;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonfigurasiSPDController extends Controller
{
    public function index()
    {
        $data_awal = DB::table('trkonfig_spd')->first();
        $data = [
            'data_konfig' => $data_awal
        ];
        return view('penatausahaan.spd.konfigurasi_spd.index')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {
            DB::table('trkonfig_spd')->update([
                'no_konfig_spd' => $data['no_konfig'],
                'tgl_konfig_spd' => $data['tgl_konfig'],
                'jenis_spd' => $data['jenis'],
                'ingat1' => $data['ingat1'],
                'ingat2' => $data['ingat2'],
                'ingat3' => $data['ingat3'],
                'ingat4' => $data['ingat4'],
                'ingat5' => $data['ingat5'],
                'ingat6' => $data['ingat6'],
                'ingat7' => $data['ingat7'],
                'ingat8' => $data['ingat8'],
                'ingat9' => $data['ingat9'],
                'ingat10' => $data['ingat10'],
                'ingat11' => $data['ingat11'],
                'ingat_akhir' => $data['ingat_akhir'],
                'memutuskan' => $data['memutuskan'],
            ]);

            DB::commit();
            return response()->json([
                'message' => '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }
}
