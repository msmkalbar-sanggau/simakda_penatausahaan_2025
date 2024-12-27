<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Crypt;

class UyhdController extends Controller
{
    public function index()
    {
        return view('skpd.uyhd.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('TRHOUTLAIN as a')->leftJoin('ms_pot as b', function ($join) {
            $join->on('a.kd_rek6', '=', 'b.kd_rek6');
        })->where(['kd_skpd' => $kd_skpd, 'thnlalu' => '1'])->where(function ($query) {
            $query->where('b.kd_rek6', '')->orWhereNull('b.kd_rek6');
        })->orderBy(DB::raw("CAST(NO_BUKTI as INT)"))->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.uyhd.edit", Crypt::encryptString($row->NO_BUKTI)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusUyhd(' . $row->NO_BUKTI . ', \'' . $row->KD_SKPD . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.uyhd.index');
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
        ];

        return view('skpd.uyhd.create')->with($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = no_urut($kd_skpd);

            // UYHD

            DB::table('TRHOUTLAIN')->insert([
                'NO_BUKTI' => $no_urut,
                'TGL_BUKTI' => $data['tanggal'],
                'nilai' => $data['nilai'],
                'KET' => $data['keterangan'],
                'KD_SKPD' => $data['kd_skpd'],
                'jns_beban' => $data['beban'],
                'pay' => $data['pembayaran'],
                'thnlalu' => $data['lalu'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
            return response()->json([
                'message' => '1',
                'nomor' => $no_urut
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
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'uyhd' => DB::table('TRHOUTLAIN')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->first(),
        ];

        return view('skpd.uyhd.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // UYHD
            DB::table('TRHOUTLAIN')->where(['NO_BUKTI' => $data['nomor'], 'KD_SKPD' => $kd_skpd])->delete();

            DB::table('TRHOUTLAIN')->insert([
                'NO_BUKTI' => $data['nomor'],
                'TGL_BUKTI' => $data['tanggal'],
                'nilai' => $data['nilai'],
                'KET' => $data['keterangan'],
                'KD_SKPD' => $data['kd_skpd'],
                'jns_beban' => $data['beban'],
                'pay' => $data['pembayaran'],
                'thnlalu' => $data['lalu'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
            return response()->json([
                'message' => '1',
                'nomor' => $data['nomor']
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
        $no_bukti = $request->no_bukti;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('TRHOUTLAIN')->where(['NO_BUKTI' => $no_bukti, 'KD_SKPD' => $kd_skpd])->delete();

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
