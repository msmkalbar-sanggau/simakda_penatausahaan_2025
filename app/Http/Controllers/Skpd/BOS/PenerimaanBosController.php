<?php

namespace App\Http\Controllers\Skpd\BOS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenerimaanBosController extends Controller
{
    public function index()
    {
        return view('skpd.penerimaan_bos.index');
    }

    public function load()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT * from tr_terima_bos WHERE kd_skpd=? order by no_terima", [$kd_skpd]);

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("penerimaan_bos.edit", ['no_terima' => Crypt::encrypt($row->no_terima), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->status == '0') {
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_terima . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_kegiatan' => DB::select("SELECT * FROM (
                SELECT '1.01.2.22.0.00.01.0000'as kd_skpd,'1.01.00.0.00.04' as kd_sub_kegiatan,'PENDAPATAN'as nm_sub_kegiatan
                UNION
                SELECT '4.01.0.00.0.00.01.0003','4.01.00.0.00.04','PENDAPATAN'
                )z where kd_skpd=?", [$kd_skpd]),
            'daftar_rekening' => DB::select("SELECT kd_rek6,nm_rek6 from ms_rek6 where kd_rek6 in ('430301010001','420101040001','420101040002','420101040003')")
        ];

        return view('skpd.penerimaan_bos.create')->with($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('tr_terima_bos')->where(['no_terima' => $data['no_terima'], 'kd_skpd' => $kd_skpd])->count();
            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('tr_terima_bos')->insert([
                'no_terima' => $data['no_terima'],
                'tgl_terima' => $data['tgl_terima'],
                'kd_skpd' => $data['kd_skpd'],
                'nm_skpd' => $data['nm_skpd'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                'kd_rek6' => $data['rekening'],
                'nm_rek6' => $data['nm_rek6'],
                'kd_satdik' => $data['satdik'],
                'nm_satdik' => $data['nm_satdik'],
                'keterangan' => $data['keterangan'],
                'nilai' => $data['nilai'],
                'status' => '0',
                'username' => Auth::user()->nama,
                'last_update' => date('Y-m-d H:i:s'),
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

    public function edit($no_terima, $kd_skpd)
    {
        $no_terima = Crypt::decrypt($no_terima);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $data = [
            'terima' => DB::table('tr_terima_bos')->where(['no_terima' => $no_terima, 'kd_skpd' => $kd_skpd])->first(),
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_kegiatan' => DB::select("SELECT * FROM (
                SELECT '1.01.2.22.0.00.01.0000'as kd_skpd,'1.01.00.0.00.04' as kd_sub_kegiatan,'PENDAPATAN'as nm_sub_kegiatan
                UNION
                SELECT '4.01.0.00.0.00.01.0003','4.01.00.0.00.04','PENDAPATAN'
                )z where kd_skpd=?", [$kd_skpd]),
            'daftar_rekening' => DB::select("SELECT kd_rek6,nm_rek6 from ms_rek6 where kd_rek6 in ('430301010001','420101040001','420101040002','420101040003')")
        ];

        return view('skpd.penerimaan_bos.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {
            DB::table('tr_terima_bos')
                ->where(['no_terima' => $data['no_terima'], 'kd_skpd' => $data['kd_skpd']])
                ->update([
                    'tgl_terima' => $data['tgl_terima'],
                    'kd_rek6' => $data['rekening'],
                    'nm_rek6' => $data['nm_rek6'],
                    'kd_satdik' => $data['satdik'],
                    'nm_satdik' => $data['nm_satdik'],
                    'keterangan' => $data['keterangan'],
                    'nilai' => $data['nilai'],
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
        $no_terima = $request->no_terima;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_terima_bos')
                ->where(['no_terima' => $no_terima, 'kd_skpd' => $kd_skpd])
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
