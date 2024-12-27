<?php

namespace App\Http\Controllers\Skpd\Pendapatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class PenetapanBaruTahunini extends Controller
{
    public function index()
    {
        return view('skpd.penetapan_pendapatan.indexnew');
    }

    public function listdata()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_tetap as a')
            ->leftJoin('tr_terima as b', function ($join) {
                $join->on('a.no_tetap', '=', 'b.no_tetap');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.*, (SELECT b.nm_rek6 FROM ms_rek6 b WHERE a.kd_rek6=b.kd_rek6) as nm_rek6, b.sumber")
            ->where(['a.kd_skpd' => $kd_skpd])
            ->orderBy('tgl_tetap')
            ->orderBy('no_tetap')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route('listeditpenetapantahunini.edit', ['no_tetap' => Crypt::encrypt($row->no_tetap), 'kd_skpd' => $row->kd_skpd]) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_tetap . '\', \'' . $row->kd_skpd . '\')" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        // return "HAKAM";
        $kd_skpd = Auth::user()->kd_skpd;
        $status_ang_pend = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();
        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_akun' => DB::table('trdrka as a')
                ->leftJoin('ms_rek6 as b', 'a.kd_rek6', '=', 'b.kd_rek6')
                ->leftJoin('ms_rek5 as c', DB::raw("left(a.kd_rek6,8)"), '=', 'c.kd_rek5')
                ->selectRaw("a.kd_rek6 as kd_rek6,b.nm_rek6 AS nm_rek6,b.map_lo as kd_rek, c.nm_rek5, a.kd_sub_kegiatan")
                ->where(['a.kd_skpd' => $kd_skpd])
                ->whereRaw("left(a.kd_rek6,1)=? and a.jns_ang=?", ['4', $status_ang_pend->jns_ang])
                ->orderBy('kd_rek6')
                ->distinct()
                ->get(),
            'ms_pajak' => DB::table('ms_pajak')
                ->where('status_aktif', '=', 1)
                ->get()
        ];
        return view('skpd.penetapan_pendapatan.createnew')->with($data);
    }

    public function simpandata(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $username = Auth::user()->username;
        $data = $request->data;
        // return dd($data);
        try {
            DB::beginTransaction();
            $tetap = DB::table('tr_tetap')
                ->where('no_tetap', '=', $data['no_penetapan'])
                ->where('kd_skpd', '=', $kd_skpd)
                ->count();
            if ($tetap > 0) {
                return response()->json([
                    'sudahada' => 'Data sudah ada'
                ]);
            }
            DB::table('tr_tetap')->insert([
                'no_tetap' => $data['no_penetapan'],
                'tgl_tetap' => $data['tgl_penetapan'],
                'kd_skpd' =>  $data['kd_skpd'],
                'kd_rek6' => $data['kd_rek6'],
                'kd_rek_lo' => $data['kd_rek_lo'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'user_name' => $username,
                'jenis' => $data['jns_pajak'],
            ]);
            DB::commit();
            return response()->json([
                'berhasil' => 'Data berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'erorr' => 'Tidak bisa Simpan, Erorr'
            ]);
        }
    }

    public function listedit(Request $request, $no_tetap, $kd_skpd)
    {
        // $kd_skpd = Auth::user()->kd_skpd;
        // return "HAKAM";
        $no_tetap = Crypt::decrypt($no_tetap);
        // return dd($no_tetap);
        // // $ckd_skpd = Crypt::decrypt($ckd_skpd);
        // return dd($kd_skpd);
        $status_ang_pend = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();
        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_akun' => DB::table('trdrka as a')
                ->leftJoin('ms_rek6 as b', 'a.kd_rek6', '=', 'b.kd_rek6')
                ->leftJoin('ms_rek5 as c', DB::raw("left(a.kd_rek6,8)"), '=', 'c.kd_rek5')
                ->selectRaw("a.kd_rek6 as kd_rek6,b.nm_rek6 AS nm_rek6,b.map_lo as kd_rek, c.nm_rek5, a.kd_sub_kegiatan")
                ->where(['a.kd_skpd' => $kd_skpd])
                ->whereRaw("left(a.kd_rek6,1)=? and a.jns_ang=?", ['4', $status_ang_pend->jns_ang])
                ->orderBy('kd_rek6')
                ->distinct()
                ->get(),
            'ms_pajak' => DB::table('ms_pajak')
                ->where('status_aktif', '=', 1)
                ->get(),
            'tetap' => DB::table('tr_tetap')->where(['no_tetap' => $no_tetap, 'kd_skpd' => $kd_skpd])->first(),
        ];
        return view('skpd.penetapan_pendapatan.editnew')->with($data);
    }

    public function updatedata(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $username = Auth::user()->username;
        $data = $request->data;
        try {
            DB::beginTransaction();
            $tetap = DB::table('tr_terima')
                ->where('no_tetap', '=', $data['no_penetapan_hide'])
                ->where('kd_skpd', '=', $kd_skpd)
                ->count();
            if ($tetap > 0) {
                return response()->json([
                    'sudahada' => 'Data sudah menjadi penerimaan'
                ]);
            }
            $tetap1 = DB::table('tr_tetap')
                ->where('no_tetap', '=',  $data['no_penetapan'])
                ->where('kd_skpd', '=', $kd_skpd)
                ->count();
            if ($tetap1 > 0) {
                return response()->json([
                    'sudahada' => 'Nomor sudah dipakai'
                ]);
            }
            $tetap2 = DB::table('tr_terima')
                ->where('no_tetap', '=',  $data['no_penetapan'])
                ->where('kd_skpd', '=', $kd_skpd)
                ->count();
            if ($tetap2 > 0) {
                return response()->json([
                    'sudahada' => 'Data sudah menjadi penerimaan'
                ]);
            }
            // Delete
            DB::table('tr_tetap')
                ->where('no_tetap', '=', $data['no_penetapan_hide'])
                ->delete();
            // Insert
            DB::table('tr_tetap')->insert([
                'no_tetap' => $data['no_penetapan'],
                'tgl_tetap' => $data['tgl_penetapan'],
                'kd_skpd' =>  $data['kd_skpd'],
                'kd_rek6' => $data['kd_rek6'],
                'kd_rek_lo' => $data['kd_rek_lo'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'user_name' => $username,
                'jenis' => $data['jns_pajak'],
            ]);
            DB::commit();
            return response()->json([
                'berhasil' => 'Data berhasil diupdate'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'erorr' => 'Tidak bisa Edit, Erorr'
            ]);
        }
    }

    public function hapusdata(Request $request)
    {
        $data = $request->data;
        try {
            DB::beginTransaction();
            $no_tetap =  DB::table('tr_terima')
                ->where('no_tetap', '=', $data['no_tetap'])
                ->where('kd_skpd', '=', $data['kd_skpd'])
                ->count();
            if ($no_tetap > 0) {
                return response()->json([
                    'sudahada' => 'Data sudah menjadi penerimaan'
                ]);
            }
            DB::table('tr_tetap')
                ->where('no_tetap', '=', $data['no_tetap'])
                ->where('kd_skpd', '=', $data['kd_skpd'])
                ->delete();
            DB::commit();
            return response()->json([
                'berhasil' => 'Data berhasil diupdate'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'erorr' => 'Tidak bisa Edit, Erorr'
            ]);
        }
    }
}
