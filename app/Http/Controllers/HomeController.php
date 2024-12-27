<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class HomeController extends Controller
{

    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        // dd(status_anggaran_dashboard());
        // if (status_anggaran_dashboard() == 0) {
        //     $status_anggaran = 'S';
        // } else {
        //     $status_anggaran = status_anggaran_dashboard();
        // }

        $status = status_anggaran_dashboard();

        $cekp = DB::table('ms_pengumuman')->select(DB::raw("COUNT(aktif) as aktif"))->where('aktif', '1')->first();

        $status_anggaran = isset($status) ? $status : 'S';
        // dd($status_anggaran);
        if (Auth::user()->is_admin == 1) {
            $data = [

                'data_pendapatan' => DB::table('trdrka')
                    ->select(DB::raw("isnull(sum(nilai),0) as pendapatan"))
                    ->where(['jns_ang' => $status_anggaran])
                    ->where(DB::raw('left(kd_rek6,1)'), 4)
                    ->first(),
                'data_belanja' => DB::table('trdrka')
                    ->select(DB::raw("isnull(sum(nilai),0) as belanja"))
                    ->where(['jns_ang' => $status_anggaran])
                    ->where(DB::raw('left(kd_rek6,1)'), 5)
                    ->first(),
                'data_pem_terima' => DB::table('trdrka')
                    ->select(DB::raw("isnull(sum(nilai),0)as pem_terima"))
                    ->where(['jns_ang' => $status_anggaran])
                    ->where(DB::raw('left(kd_rek6,2)'), 61)
                    ->first(),
                'data_pem_keluar' => DB::table('trdrka')
                    ->select(DB::raw("isnull(sum(nilai),0) as pem_keluar"))
                    ->where(['jns_ang' => $status_anggaran])
                    ->where(DB::raw('left(kd_rek6,2)'), 62)
                    ->first(),
                'data_penagihan' => DB::table('trhtagih')
                    ->select(DB::raw("isnull(sum(total),0) as penagihan"))
                    ->first(),
                'data_spp' => DB::table('trhspp')
                    ->select(DB::raw("isnull(sum(nilai),0) as spp"))
                    ->whereRaw("sp2d_batal is null OR sp2d_batal <> 1")
                    ->first(),
                'data_spm' => DB::table('trhspm as a')
                    ->select(DB::raw("isnull(sum(b.nilai),0) as spm"))
                    ->join('trhspp as b', function ($join) {
                        $join->on('a.no_spp', '=', 'b.no_spp');
                        $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    })
                    ->whereRaw("sp2d_batal is null OR sp2d_batal <> 1")
                    ->first(),
                'data_sp2d' => DB::table('trhsp2d')
                    ->select(DB::raw("isnull(sum(nilai),0) as sp2d"))
                    ->whereRaw("(sp2d_batal is null OR sp2d_batal <> 1)")
                    ->first(),
                'cekpengumuman' => $cekp,
            ];
        } else {
            $data = [
                'data_pendapatan' => DB::table('trdrka')
                    ->select(DB::raw("isnull(sum(nilai),0) as pendapatan"))
                    ->where(['jns_ang' => $status_anggaran])
                    ->where(DB::raw('left(kd_rek6,1)'), 4)
                    ->where('kd_skpd', $kd_skpd)
                    ->first(),
                'data_belanja' => DB::table('trdrka')
                    ->select(DB::raw("isnull(sum(nilai),0) as belanja"))
                    ->where(['jns_ang' => $status_anggaran])
                    ->where(DB::raw('left(kd_rek6,1)'), 5)
                    ->where('kd_skpd', $kd_skpd)
                    ->first(),
                'data_pem_terima' => DB::table('trdrka')
                    ->select(DB::raw("isnull(sum(nilai),0)as pem_terima"))
                    ->where(['jns_ang' => $status_anggaran])
                    ->where(DB::raw('left(kd_rek6,2)'), 61)
                    ->where('kd_skpd', $kd_skpd)
                    ->first(),
                'data_pem_keluar' => DB::table('trdrka')
                    ->select(DB::raw("isnull(sum(nilai),0) as pem_keluar"))
                    ->where(['jns_ang' => $status_anggaran])
                    ->where(DB::raw('left(kd_rek6,2)'), 62)
                    ->where('kd_skpd', $kd_skpd)
                    ->first(),

                'data_penagihan' => DB::table('trhtagih')
                    ->select(DB::raw("isnull(sum(total),0) as penagihan"))
                    ->where('kd_skpd', $kd_skpd)
                    ->first(),
                'data_spp' => DB::table('trhspp')
                    ->select(DB::raw("isnull(sum(nilai),0) as spp"))
                    ->where('kd_skpd', $kd_skpd)
                    ->whereRaw("sp2d_batal is null OR sp2d_batal <> 1")
                    ->first(),
                'data_spm' => DB::table('trhspm as a')
                    ->select(DB::raw("isnull(sum(b.nilai),0) as spm"))
                    ->join('trhspp as b', function ($join) {
                        $join->on('a.no_spp', '=', 'b.no_spp');
                        $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    })
                    ->whereRaw("(sp2d_batal is null OR sp2d_batal <> 1)")
                    ->where('a.kd_skpd', $kd_skpd)
                    ->first(),
                'data_sp2d' => DB::table('trhsp2d')
                    ->select(DB::raw("isnull(sum(nilai),0) as sp2d"))
                    ->whereRaw("(sp2d_batal is null OR sp2d_batal <> 1)")
                    ->where('kd_skpd', $kd_skpd)
                    ->first(),
                'cekpengumuman' => $cekp,
            ];
        }

        return view('home')->with($data);;
    }

    public function pengumuman()
    {
        $data = [
            'data_pengumuman' => DB::table('ms_pengumuman')
                ->where('aktif', 1)
                ->get()
        ];

        return view('pengumuman.pengumuman')->with($data);;
    }

    public function pengumumanid($id)
    {
        $id = Crypt::decryptString($id);
        $data = [
            'pengumuman_by_id' => DB::table('ms_pengumuman')->where('id', $id)->first()
        ];

        // dd($data['pengumuman_by_id']);
        return view('pengumuman.pengumuman_by_id')->with($data);
    }

    public function coba()
    {
        return view('coba');
    }

    // Ubah SKPD
    public function ubahSkpd($id)
    {
        $id = Crypt::decryptString($id);
        $data = [
            'user' => DB::table('pengguna')->where(['id' => $id])->first(),
            'kd_skpd' => DB::table('ms_skpd')->orderBy('kd_skpd')->get()
        ];

        return view('fungsi.ubah_skpd.index')->with($data);
    }

    public function simpanUbahSkpd(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {
            DB::table('pengguna')->where(['id' => $data['id'], 'username' => $data['username']])->update([
                'kd_skpd' => $data['kd_skpd']
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

    // Ubah Password
    public function ubahPassword($id)
    {
        $id = Crypt::decryptString($id);
        $data = [
            'user' => DB::table('pengguna')->where(['id' => $id])->first(),
            'kd_skpd' => DB::table('ms_skpd')->orderBy('kd_skpd')->get()
        ];

        return view('fungsi.ubah_password.index')->with($data);
    }

    public function simpanUbahPassword(Request $request)
    {
        $data = $request->data;
        $id = Crypt::decryptString($data['id']);

        if ($id != Auth::user()->id) {
            return response()->json([
                'message' => '4'
            ]);
        }

        $password_lama = DB::table('pengguna')
            ->where(['id' => $id])
            ->first()
            ->password;

        if (!Hash::check($data['password_lama'], $password_lama) && $data['password_lama'] != '') {
            return response()->json([
                'message' => '3'
            ]);
        }

        if ($data['password'] != $data['password2']) {
            return response()->json([
                'message' => '2'
            ]);
        }

        DB::beginTransaction();
        try {
            DB::table('pengguna')->where(['id' => $id])
                ->update([
                    'password' => Hash::make($data['password'])
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
