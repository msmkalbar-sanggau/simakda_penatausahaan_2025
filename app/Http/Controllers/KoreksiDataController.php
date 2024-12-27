<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KoreksiDataController extends Controller
{
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $status_ang_pend = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_skpd' => DB::select("SELECT kd_skpd,nm_skpd from ms_skpd ORDER BY kd_skpd"),
            'daftar_pengirim' => DB::select("SELECT * from ms_pengirim WHERE LEFT(kd_skpd,17)=LEFT(?,17)
                order by kd_pengirim", [$kd_skpd])
        ];

        return view('fungsi.koreksi_data.create')->with($data);
    }

    public function sp2d(Request $request)
    {
        $kd_skpd = $request->kd_skpd;

        $data_sp2d = DB::select("SELECT a.no_sp2d,a.keperluan,c.tgl_mulai,c.tgl_akhir,(SELECT jenis FROM trhtagih d WHERE c.no_tagih=d.no_bukti AND c.kd_skpd=d.kd_skpd) as jenis FROM trhsp2d a INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd=b.kd_skpd INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd WHERE a.kd_skpd=? AND (a.sp2d_batal!=? OR a.sp2d_batal is null) AND a.status_bud!=?", [$kd_skpd, '1', '1']);

        $data_spm = DB::select("SELECT a.no_spm,a.keperluan,b.tgl_mulai,b.tgl_akhir,(SELECT jenis FROM trhtagih c WHERE b.no_tagih=c.no_bukti AND b.kd_skpd=c.kd_skpd) as jenis FROM trhspm a INNER JOIN trhspp b ON a.no_spp=b.no_spp AND a.kd_skpd=b.kd_skpd WHERE a.kd_skpd=? AND (b.sp2d_batal!=? OR b.sp2d_batal is null) AND a.status!=?", [$kd_skpd, '1', '1']);

        return response()->json([
            'sp2d' => $data_sp2d,
            'spm' => $data_spm,
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {
            if ($data['pilihan'] == '1') {
                DB::table('trhsp2d')
                    ->where(['no_sp2d' => $data['no_sp2d'], 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'keperluan' => $data['keterangan'],
                        'username' => Auth::user()->nama,
                        'last_update' => date('Y-m-d H:i:s')
                    ]);

                $data_sp2d = DB::table('trhsp2d')
                    ->where(['no_sp2d' => $data['no_sp2d'], 'kd_skpd' => $data['kd_skpd']])
                    ->first();

                DB::table('trhspm')
                    ->where(['no_spm' => $data_sp2d->no_spm, 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'keperluan' => $data['keterangan'],
                        'username' => Auth::user()->nama,
                        'last_update' => date('Y-m-d H:i:s')
                    ]);

                DB::table('trhspp')
                    ->where(['no_spp' => $data_sp2d->no_spp, 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'keperluan' => $data['keterangan'],
                        'username' => Auth::user()->nama,
                        'last_update' => date('Y-m-d H:i:s'),
                        'tgl_mulai' => $data['tgl_mulai'],
                        'tgl_akhir' => $data['tgl_akhir'],
                    ]);

                $data_spp = DB::table('trhspp')
                    ->where(['no_spp' => $data_sp2d->no_spp, 'kd_skpd' => $data['kd_skpd']])
                    ->first();

                if (isset($data_spp->no_tagih)) {
                    DB::table('trhtagih')
                        ->where(['no_bukti' => $data_spp->no_tagih, 'kd_skpd' => $data['kd_skpd']])
                        ->update([
                            'ket' => $data['keterangan'],
                            'username' => Auth::user()->nama,
                            'tgl_update' => date('Y-m-d H:i:s'),
                            'jenis' => $data['jenis'],
                        ]);
                }
            } else {
                DB::table('trhspm')
                    ->where(['no_spm' => $data['no_spm'], 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'keperluan' => $data['keterangan'],
                        'username' => Auth::user()->nama,
                        'last_update' => date('Y-m-d H:i:s')
                    ]);

                $data_spm = DB::table('trhspm')
                    ->where(['no_spm' => $data['no_spm'], 'kd_skpd' => $data['kd_skpd']])
                    ->first();

                DB::table('trhspp')
                    ->where(['no_spp' => $data_spm->no_spp, 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'keperluan' => $data['keterangan'],
                        'username' => Auth::user()->nama,
                        'last_update' => date('Y-m-d H:i:s'),
                        'tgl_mulai' => $data['tgl_mulai'],
                        'tgl_akhir' => $data['tgl_akhir'],
                    ]);

                $data_spp = DB::table('trhspp')
                    ->where(['no_spp' => $data_spm->no_spp, 'kd_skpd' => $data['kd_skpd']])
                    ->first();

                if (isset($data_spp->no_tagih)) {
                    DB::table('trhtagih')
                        ->where(['no_bukti' => $data_spp->no_tagih, 'kd_skpd' => $data['kd_skpd']])
                        ->update([
                            'ket' => $data['keterangan'],
                            'username' => Auth::user()->nama,
                            'tgl_update' => date('Y-m-d H:i:s'),
                            'jenis' => isset($data['jenis']) ? $data['jenis'] : '',
                        ]);
                }
            }

            DB::commit();
            return response()->json([
                'message' => '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
                // 'error' => $e->getMessage()
            ]);
        }
    }
}
