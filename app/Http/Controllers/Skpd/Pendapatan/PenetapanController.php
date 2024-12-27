<?php

namespace App\Http\Controllers\Skpd\Pendapatan;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenetapanController extends Controller
{
    // Penetapan Pendapatan
    public function indexPenetapanPendapatan()
    {
        return view('skpd.penetapan_pendapatan.index');
    }

    // load penetapan
    public function loadDataPenetapanPendapatan()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_tetap as a')
            ->leftJoin('tr_terima as b', function ($join) {
                $join->on('a.no_tetap', '=', 'b.no_tetap');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.*, (SELECT b.nm_rek6 FROM ms_rek6 b WHERE a.kd_rek6=b.kd_rek6) as nm_rek6, b.sumber, b.sts_tetap")
            ->where(['a.kd_skpd' => $kd_skpd])
            ->orderBy('tgl_tetap')
            ->orderBy('no_tetap')
            ->get();
        // $data = DB::select("SELECT a.*, c.nm_rek6 FROM tr_tetap a INNER JOIN tr_terima b ON a.no_tetap = b.no_tetap
        // AND a.kd_skpd = b.kd_skpd INNER JOIN ms_rek6 c ON a.kd_rek6= c.kd_rek6 WHERE a.kd_skpd= ?
        // ORDER BY a.tgl_tetap, a.no_tetap", [$kd_skpd])->get();
        // dd($data);
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if ($row->sts_tetap != 0) {
                $btn = '';
            } else {
                $btn = '<a href="' . route("penetapan_pendapatan.edit", Crypt::encrypt($row->no_tetap)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_tetap . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahPenetapanPendapatan()
    {
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
                ->selectRaw("a.kd_rek6 as kd_rek6,b.nm_rek6 AS nm_rek,b.map_lo as kd_rek, c.nm_rek5, a.kd_sub_kegiatan")
                ->where(['a.kd_skpd' => $kd_skpd])
                ->whereRaw("left(a.kd_rek6,1)=? and a.jns_ang=?", ['4', $status_ang_pend->jns_ang])
                ->orderBy('kd_rek6')
                ->distinct()
                ->get()
        ];

        return view('skpd.penetapan_pendapatan.create')->with($data);
    }

    public function simpanPenetapanPendapatan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;
        DB::beginTransaction();
        try {
            $total = DB::table('tr_tetap')->where(['no_tetap' => $data['no_tetap'], 'kd_skpd' => $kd_skpd])->count();
            if ($total > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }
            DB::table('tr_tetap')->insert([
                'no_tetap' => $data['no_tetap'],
                'tgl_tetap' => $data['tgl_tetap'],
                'kd_skpd' => $data['kd_skpd'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'kd_rek6' => $data['kode_akun'],
                'kd_rek_lo' => $data['kode_rek'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'jenis' => $data['pajak_hotel'],
                'user_name' => Auth::user()->nama,
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

    public function editPenetapanPendapatan($no_tetap)
    {
        $no_tetap = Crypt::decrypt($no_tetap);
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
                ->selectRaw("a.kd_rek6 as kd_rek6,b.nm_rek6 AS nm_rek,b.map_lo as kd_rek, c.nm_rek5, a.kd_sub_kegiatan")
                ->where(['a.kd_skpd' => $kd_skpd])
                ->whereRaw("left(a.kd_rek6,1)=? and a.jns_ang=?", ['4', $status_ang_pend->jns_ang])
                ->orderBy('kd_rek6')
                ->distinct()
                ->get(),
            'tetap' => DB::table('tr_tetap')->where(['no_tetap' => $no_tetap, 'kd_skpd' => $kd_skpd])->first()
        ];

        return view('skpd.penetapan_pendapatan.edit')->with($data);
    }

    public function simpanEditPenetapanPendapatan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;

        DB::beginTransaction();
        try {
            $total = DB::table('tr_tetap')->where(['no_tetap' => $data['no_tetap'], 'kd_skpd' => $kd_skpd])->count();
            if ($total > 0 && $data['no_tetap'] != $data['no_simpan']) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('tr_tetap')->where(['kd_skpd' => $kd_skpd, 'no_tetap' => $data['no_simpan']])->delete();

            DB::table('tr_tetap')->insert([
                'no_tetap' => $data['no_tetap'],
                'tgl_tetap' => $data['tgl_tetap'],
                'kd_skpd' => $data['kd_skpd'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'kd_rek6' => $data['kode_akun'],
                'kd_rek_lo' => $data['kode_rek'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'jenis' => $data['pajak_hotel'],
                'user_name' => Auth::user()->nama,
                // 'kanal' => ''
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

    public function hapusPenetapanPendapatan(Request $request)
    {
        $no_tetap = $request->no_tetap;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_tetap')->where(['no_tetap' => $no_tetap, 'kd_skpd' => $kd_skpd])->delete();

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

    // Penetapan Penerimaan
    public function indexPenetapanPenerimaan()
    {
        return view('skpd.penetapan_penerimaan.index');
    }

    public function loadDataPenetapanPenerimaan()
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
            $btn = '<a href="' . route("penetapan_penerimaan.edit", Crypt::encrypt($row->no_tetap)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_tetap . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahPenetapanPenerimaan()
    {
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
                ->selectRaw("a.kd_rek6 as kd_rek6,b.nm_rek6 AS nm_rek,b.map_lo as kd_rek, c.nm_rek5, a.kd_sub_kegiatan")
                ->where(['a.kd_skpd' => $kd_skpd])
                ->whereRaw("left(a.kd_rek6,1)=? and a.jns_ang=?", ['4', $status_ang_pend->jns_ang])
                ->orderBy('kd_rek6')
                ->distinct()
                ->get(),
            'daftar_pengirim' => DB::table('ms_pengirim')
                ->select('kd_pengirim', 'nm_pengirim', 'kd_skpd')
                ->where(function ($query) use ($kd_skpd) {
                    if (substr($kd_skpd, 0, 17) == '5-02.0-00.0-00.02') {
                        $query->where('kd_skpd', $kd_skpd);
                    } else {
                        $query->whereRaw("left(kd_skpd,15)=left(?,15)", [$kd_skpd]);
                    }
                })
                // ->orderByRaw("cast(kd_pengirim as int)")
                ->orderByRaw("kd_pengirim")
                ->get()
        ];

        return view('skpd.penetapan_penerimaan.create')->with($data);
    }

    public function simpanPenetapanPenerimaan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;
        DB::beginTransaction();
        try {
            $total = DB::table('tr_tetap')->where(['no_tetap' => $data['no_tetap'], 'kd_skpd' => $kd_skpd])->count();
            if ($total > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            if ($data['tanpa_penetapan'] == '1') {
                DB::table('tr_terima')->insert([
                    'no_terima' => $data['no_tetap'] . '/TRM',
                    'tgl_terima' => $data['tgl_tetap'],
                    'no_tetap' => '',
                    'tgl_tetap' => '',
                    'sts_tetap' => '0',
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['kode_akun'],
                    'kd_rek_lo' => $data['kode_rek'],
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'jenis' => '1',
                    'sumber' => $data['kode_pengirim'],
                    // 'kanal' => ''
                ]);
            } else {
                DB::table('tr_tetap')->insert([
                    'no_tetap' => $data['no_tetap'],
                    'tgl_tetap' => $data['tgl_tetap'],
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['kode_akun'],
                    'kd_rek_lo' => $data['kode_rek'],
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    // 'kanal' => ''
                ]);

                DB::table('tr_terima')->insert([
                    'no_terima' => $data['no_tetap'] . '/TRM',
                    'tgl_terima' => $data['tgl_tetap'],
                    'no_tetap' => $data['no_tetap'],
                    'tgl_tetap' => $data['tgl_tetap'],
                    'sts_tetap' => '1',
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['kode_akun'],
                    'kd_rek_lo' => $data['kode_rek'],
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'jenis' => '1',
                    'sumber' => $data['kode_pengirim'],
                    // 'kanal' => ''
                ]);
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

    public function editPenetapanPenerimaan($no_tetap)
    {
        $no_tetap = Crypt::decrypt($no_tetap);
        $kd_skpd = Auth::user()->kd_skpd;
        $status_ang_pend = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();
        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tetap' => DB::table('tr_tetap as a')
                ->leftJoin('tr_terima as b', function ($join) {
                    $join->on('a.no_tetap', '=', 'b.no_tetap');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("a.*, (SELECT b.nm_rek6 FROM ms_rek6 b WHERE a.kd_rek6=b.kd_rek6) as nm_rek6, b.sumber, b.sts_tetap")
                ->where(['a.kd_skpd' => $kd_skpd, 'a.no_tetap' => $no_tetap])
                ->first(),
            'daftar_akun' => DB::table('trdrka as a')
                ->leftJoin('ms_rek6 as b', 'a.kd_rek6', '=', 'b.kd_rek6')
                ->leftJoin('ms_rek5 as c', DB::raw("left(a.kd_rek6,8)"), '=', 'c.kd_rek5')
                ->selectRaw("a.kd_rek6 as kd_rek6,b.nm_rek6 AS nm_rek,b.map_lo as kd_rek, c.nm_rek5, a.kd_sub_kegiatan")
                ->where(['a.kd_skpd' => $kd_skpd])
                ->whereRaw("left(a.kd_rek6,1)=? and a.jns_ang=?", ['4', $status_ang_pend->jns_ang])
                ->orderBy('kd_rek6')
                ->distinct()
                ->get(),
            'daftar_pengirim' => DB::table('ms_pengirim')
                ->select('kd_pengirim', 'nm_pengirim', 'kd_skpd')
                ->where(function ($query) use ($kd_skpd) {
                    if (substr($kd_skpd, 0, 17) == '5-02.0-00.0-00.02') {
                        $query->where('kd_skpd', $kd_skpd);
                    } else {
                        $query->whereRaw("left(kd_skpd,15)=left(?,15)", [$kd_skpd]);
                    }
                })
                // ->orderByRaw("cast(kd_pengirim as int)")
                ->orderByRaw("kd_pengirim")
                ->get()
        ];

        return view('skpd.penetapan_penerimaan.edit')->with($data);
    }

    public function simpanEditPenetapanPenerimaan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $total = DB::table('tr_tetap')->where(['no_tetap' => $data['no_tetap'], 'kd_skpd' => $kd_skpd])->count();
            if ($total > 0 && $data['no_tetap'] != $data['nomor']) {
                return response()->json([
                    'message' => '2'
                ]);
            }
            $kunci = DB::table('tr_terima')->where(['no_terima' => $data['no_tetap'] . '/TRM', 'kd_skpd' => $kd_skpd, 'kunci' => '1'])->count();
            if ($total >= '1') {
                return response()->json([
                    'message' => '3'
                ]);
            }

            if ($data['tanpa_penetapan'] != '1') {
                DB::table('tr_tetap')->where(['no_tetap' => $data['nomor'], 'kd_skpd' => $data['kd_skpd']])->delete();

                DB::table('tr_tetap')->insert([
                    'no_tetap' => $data['no_tetap'],
                    'tgl_tetap' => $data['tgl_tetap'],
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['kode_akun'],
                    'kd_rek_lo' => $data['kode_rek'],
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    // 'kanal' => ''
                ]);

                DB::table('trhkasin_pkd')->where(['no_sts' => $data['nomor'] . '/STS', 'kd_skpd' => $data['kd_skpd']])->delete();

                DB::table('trhkasin_pkd')->insert([
                    'no_sts' => $data['no_tetap'] . '/STS',
                    'tgl_sts' => $data['tgl_tetap'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'total' => $data['nilai'],
                    'kd_bank' => '',
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'jns_trans' => '4',
                    'rek_bank' => $data['kode_rek'],
                    'no_kas' => $data['no_tetap'] . '/STS',
                    'tgl_kas' => $data['tgl_tetap'],
                    'no_cek' => '',
                    'status' => '',
                    'sumber' => '0',
                    'jns_cp' => '',
                    'pot_khusus' => '0',
                    'no_sp2d' => '0',
                    'no_terima' => $data['no_tetap'] . '/TRM',
                ]);

                DB::table('trdkasin_pkd')->where(['no_sts' => $data['nomor'] . '/STS', 'kd_skpd' => $data['kd_skpd']])->delete();

                DB::table('trdkasin_pkd')->insert([
                    'no_sts' => $data['no_tetap'] . '/STS',
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_rek6' => $data['kode_akun'],
                    'rupiah' => $data['nilai'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                ]);

                DB::table('tr_terima')->where(['no_terima' => $data['nomor'] . '/TRM', 'kd_skpd' => $data['kd_skpd']])->delete();

                DB::table('tr_terima')->insert([
                    'no_terima' => $data['no_tetap'] . '/TRM',
                    'tgl_terima' => $data['tgl_tetap'],
                    'no_tetap' => $data['no_tetap'],
                    'tgl_tetap' => $data['tgl_tetap'],
                    'sts_tetap' => '1',
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['kode_akun'],
                    'kd_rek_lo' => $data['kode_rek'],
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'jenis' => '1',
                    // 'kanal' => ''
                ]);
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

    public function hapusPenetapanPenerimaan(Request $request)
    {
        $no_tetap = $request->no_tetap;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_tetap')->where(['no_tetap' => $no_tetap, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('tr_terima')->where(['no_terima' => $no_tetap . '/TRM', 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trhkasin_pkd')->where(['no_sts' => $no_tetap . '/STS', 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trdkasin_pkd')->where(['no_sts' => $no_tetap . '/STS', 'kd_skpd' => $kd_skpd])->delete();

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
