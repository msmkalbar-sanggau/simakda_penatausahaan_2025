<?php

namespace App\Http\Controllers\Skpd\Panjar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PertanggungjawabanPanjarController extends Controller
{
    public function index()
    {
        return view('skpd.pertanggungjawaban_panjar.index');
    }

    public function load()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('tr_jpanjar')
            ->where(['jns' => '1', 'kd_skpd' => $kd_skpd])
            ->orderBy('no_panjar')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("jawabpanjar.edit", ['no_kas' => Crypt::encrypt($row->no_kas), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_kas . '\',\'' . $row->no_panjar . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambah()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'daftar_panjar' => DB::table('tr_panjar')
                ->where(['status' => '0', 'kd_skpd' => $kd_skpd, 'jns' => 1])
                ->get(),
            'no_urut' => no_urut_tukd()
        ];

        return view('skpd.pertanggungjawaban_panjar.create')->with($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek_panjar = DB::table('tr_jpanjar')->where(['no_kas' => $data['no_kas'], 'kd_skpd' => $kd_skpd])->count();
            if ($cek_panjar > 0) {
                return response()->json([
                    'message' => '4'
                ]);
            }

            DB::table('tr_jpanjar')
                ->insert([
                    'no_kas' => $data['no_kas'],
                    'tgl_kas' => $data['tgl_kas'],
                    'no_panjar' => $data['no_panjar'],
                    'tgl_panjar' => $data['tgl_panjar'],
                    'kd_skpd' => $data['kd_skpd'],
                    'pengguna' => Auth::user()->nama,
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'rek_bank' => '',
                    'jns' => '1',
                    'no_panjar_lalu' => $data['no_panjar'],
                ]);

            DB::table('tr_panjar')
                ->where(['no_panjar' => $data['no_panjar'], 'kd_skpd' => $kd_skpd])
                ->update([
                    'status' => '1'
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

    public function edit($no_kas, $kd_skpd)
    {
        $no_kas = Crypt::decrypt($no_kas);
        $kd_skpd = Crypt::decrypt($kd_skpd);
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'daftar_panjar' => DB::table('tr_panjar')
                ->where(['status' => '0', 'kd_skpd' => $kd_skpd])
                ->get(),
            'panjar' => DB::table('tr_jpanjar')
                ->where(['jns' => '1', 'kd_skpd' => $kd_skpd, 'no_kas' => $no_kas])
                ->first()
        ];

        return view('skpd.pertanggungjawaban_panjar.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek_panjar = DB::table('tr_jpanjar')
                ->where(['no_kas' => $data['no_kas'], 'kd_skpd' => $kd_skpd])
                ->count();
            if ($cek_panjar > 0 && $data['no_kas'] != $data['no_simpan']) {
                return response()->json([
                    'message' => '4'
                ]);
            }

            DB::table('tr_jpanjar')
                ->where(['kd_skpd' => $kd_skpd, 'no_kas' => $data['no_simpan']])
                ->delete();

            DB::table('tr_jpanjar')
                ->insert([
                    'no_kas' => $data['no_kas'],
                    'tgl_kas' => $data['tgl_kas'],
                    'no_panjar' => $data['no_panjar'],
                    'tgl_panjar' => $data['tgl_panjar'],
                    'kd_skpd' => $data['kd_skpd'],
                    'pengguna' => Auth::user()->nama,
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'rek_bank' => '',
                    'jns' => '1',
                    'no_panjar_lalu' => $data['no_panjar'],
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

    public function hapus(Request $request)
    {
        $no_kas = $request->no_kas;
        $no_panjar = $request->no_panjar;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_panjar')
                ->where(['no_panjar' => $no_panjar, 'kd_skpd' => $kd_skpd])
                ->update([
                    'status' => '0'
                ]);

            DB::table('tr_jpanjar')
                ->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])
                ->delete();

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
