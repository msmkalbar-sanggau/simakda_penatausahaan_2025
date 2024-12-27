<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Crypt;

class PelimpahanKegiatanController extends Controller
{
    public function index()
    {
        return view('skpd.pelimpahan_kegiatan.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('pelimpahan_kegiatan')->select('kd_bpp', 'kd_skpd', 'id_user')->where(['kd_skpd' => $kd_skpd])->groupBy('kd_bpp', 'kd_skpd', 'id_user')->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.pelimpahan_kegiatan.edit", ['id_user' => Crypt::encryptString($row->id_user), 'kd_bpp' => Crypt::encryptString($row->kd_bpp)]) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusPelimpahan(' . $row->id_user . ', \'' . $row->kd_bpp . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.pelimpahan_kegiatan.index');
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $kd_bpp = Auth::user()->kd_bpp;

        $data = [
            'daftar_bpp' => DB::table('pengguna')->where(['kd_skpd' => $kd_skpd])->whereNotIn('kd_bpp', [$kd_bpp])->whereRaw("id NOT IN (SELECT id_user FROM pelimpahan_kegiatan WHERE kd_skpd=?)", $kd_skpd)->get(),
            'daftar_kegiatan' => DB::table('trdrka')->select('kd_sub_kegiatan', 'nm_sub_kegiatan', 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->groupBy('kd_sub_kegiatan', 'nm_sub_kegiatan', 'kd_skpd')->get()
        ];

        return view('skpd.pelimpahan_kegiatan.create')->with($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {
            if (isset($data['rincian_data'])) {
                DB::table('pelimpahan_kegiatan')->insert(array_map(function ($value) use ($data) {
                    return [
                        'kd_bpp' => $data['kd_bpp'],
                        'kd_skpd' => Auth::user()->kd_skpd,
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'id_user' => $data['id_user'],
                    ];
                }, $data['rincian_data']));
            }

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

    public function edit($id_user, $kd_bpp)
    {
        $id_user = Crypt::decryptString($id_user);
        $kd_bpp1 = Crypt::decryptString($kd_bpp);
        $kd_skpd = Auth::user()->kd_skpd;
        $kd_bpp = Auth::user()->kd_bpp;

        $data = [
            'data_bpp' => DB::table('pelimpahan_kegiatan')->where(['id_user' => $id_user, 'kd_skpd' => $kd_skpd, 'kd_bpp' => $kd_bpp1])->first(),
            'daftar_data' => DB::table('pelimpahan_kegiatan')->where(['id_user' => $id_user, 'kd_skpd' => $kd_skpd, 'kd_bpp' => $kd_bpp1])->get(),
            'daftar_kegiatan' => DB::table('trdrka')->select('kd_sub_kegiatan', 'nm_sub_kegiatan', 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->groupBy('kd_sub_kegiatan', 'nm_sub_kegiatan', 'kd_skpd')->get()
        ];

        return view('skpd.pelimpahan_kegiatan.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {

            DB::table('pelimpahan_kegiatan')->where(['id_user' => $data['id_user'], 'kd_skpd' => $kd_skpd, 'kd_bpp' => $data['kd_bpp']])->delete();

            if (isset($data['rincian_data'])) {
                DB::table('pelimpahan_kegiatan')->insert(array_map(function ($value) use ($data) {
                    return [
                        'kd_bpp' => $data['kd_bpp'],
                        'kd_skpd' => Auth::user()->kd_skpd,
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'id_user' => $data['id_user'],
                    ];
                }, $data['rincian_data']));
            }

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


    public function hapus(Request $request)
    {
        $id_user = $request->id_user;
        $kd_bpp = $request->kd_bpp;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('pelimpahan_kegiatan')->where(['id_user' => $id_user, 'kd_skpd' => $kd_skpd, 'kd_bpp' => $kd_bpp])->delete();

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
