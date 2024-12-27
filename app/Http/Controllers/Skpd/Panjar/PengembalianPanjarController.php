<?php

namespace App\Http\Controllers\Skpd\Panjar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PengembalianPanjarController extends Controller
{
    public function index()
    {
        return view('skpd.pengembalian_panjar.index');
    }

    public function load()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('tr_jpanjar')
            ->where(['kd_skpd' => $kd_skpd, 'jns' => '2'])
            ->orderBy('no_kas')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("kembalipanjar.edit", ['no_kas' => Crypt::encrypt($row->no_kas), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_kas . '\',\'' . $row->no_panjar . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambah()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'no_urut' => no_urut($kd_skpd),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'daftar_panjar' => DB::table('tr_panjar')
                ->where(['status_kembali' => '0', 'jns' => '1', 'kd_skpd' => $kd_skpd])
                ->orderBy('no_panjar')
                ->get()
        ];

        return view('skpd.pengembalian_panjar.create')->with($data);
    }

    public function loadData(Request $request)
    {
        $no_panjar = $request->no_panjar;
        $no_panjar_lalu = $request->no_panjar_lalu;
        $kd_skpd = Auth::user()->kd_skpd;

        $load_total = collect(DB::select("SELECT SUM(a.nilai) as panjar, (SELECT SUM(c.nilai) FROM trdtransout c join trhtransout b on c.no_bukti = b.no_bukti AND c.kd_skpd=b.kd_skpd WHERE b.no_panjar = ? and b.panjar = '1' AND b.kd_skpd=?) as trans FROM tr_panjar a WHERE a.no_panjar_lalu = ? AND a.kd_skpd=? GROUP BY kd_skpd", [$no_panjar, $kd_skpd, $no_panjar_lalu, $kd_skpd]))->first();

        $load_detail = collect(DB::select("SELECT no_panjar, nilai,(SELECT no_panjar from tr_panjar where jns = '2' AND no_panjar_lalu = ? AND kd_skpd=?) as no_panjar2,
					(SELECT nilai from tr_panjar where jns = '2' AND no_panjar_lalu = ? AND kd_skpd=? ) as nilai2
					FROM tr_panjar WHERE no_panjar_lalu = ? AND jns = '1' AND kd_skpd=?", [$no_panjar_lalu, $kd_skpd, $no_panjar_lalu, $kd_skpd, $no_panjar_lalu, $kd_skpd]))->first();

        return response()->json([
            'load_total' => $load_total,
            'load_detail' => $load_detail
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek_panjar = collect(DB::select("SELECT count(*) as jumlah FROM (select no_panjar as nomor FROM tr_panjar WHERE kd_skpd = ? UNION ALL SELECT no_kas as nomor FROM tr_jpanjar WHERE kd_skpd = ?)a WHERE a.nomor = ?", [$kd_skpd, $kd_skpd, $data['no_panjar']]))->first();
            if ($cek_panjar->jumlah > 0) {
                return response()->json([
                    'message' => '4'
                ]);
            }

            DB::table('tr_jpanjar')
                ->insert([
                    'no_kas' => $data['no_panjar'],
                    'tgl_kas' => $data['tgl_panjar'],
                    'no_panjar' => $data['no_panjar_lalu'],
                    'tgl_panjar' => $data['tgl_panjar_lalu'],
                    'kd_skpd' => $data['kd_skpd'],
                    'pengguna' => Auth::user()->nama,
                    'nilai' => $data['sisa_panjar'],
                    'keterangan' => $data['keterangan'],
                    'jns' => '2',
                    'no_panjar_lalu' => $data['no_panjar_lalu'],
                ]);

            DB::table('tr_panjar')
                ->where(['no_panjar_lalu' => $data['no_panjar_lalu'], 'kd_skpd' => $kd_skpd])
                ->update([
                    'status_kembali' => '1'
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

    public function edit($no_panjar, $kd_skpd)
    {
        $no_panjar = Crypt::decrypt($no_panjar);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $panjar = DB::table('tr_jpanjar')
            ->where(['no_kas' => $no_panjar, 'kd_skpd' => $kd_skpd])
            ->first();

        $data = [
            'panjar' => $panjar,
            'load_detail' => collect(DB::select("SELECT no_panjar, nilai,(SELECT no_panjar from tr_panjar where jns = '2' AND no_panjar_lalu = ? AND kd_skpd=?) as no_panjar2,
					(SELECT nilai from tr_panjar where jns = '2' AND no_panjar_lalu = ? AND kd_skpd=? ) as nilai2
					FROM tr_panjar WHERE no_panjar_lalu = ? AND jns = '1' AND kd_skpd=?", [$panjar->no_panjar, $kd_skpd, $panjar->no_panjar, $kd_skpd, $panjar->no_panjar, $kd_skpd]))->first(),
            'load_total' => collect(DB::select("SELECT SUM(a.nilai) as panjar, (SELECT SUM(c.nilai) FROM trdtransout c join trhtransout b on c.no_bukti = b.no_bukti AND c.kd_skpd=b.kd_skpd WHERE b.no_panjar = ? and b.panjar = '1' AND b.kd_skpd=?) as trans FROM tr_panjar a WHERE a.no_panjar_lalu = ? AND a.kd_skpd=? GROUP BY kd_skpd", [$panjar->no_panjar, $kd_skpd, $panjar->no_panjar, $kd_skpd]))->first()
        ];

        return view('skpd.pengembalian_panjar.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek_panjar = collect(DB::select("SELECT count(*) as jumlah FROM (select no_panjar as nomor FROM tr_panjar WHERE kd_skpd = ? UNION ALL SELECT no_kas as nomor FROM tr_jpanjar WHERE kd_skpd = ?)a WHERE a.nomor = ?", [$kd_skpd, $kd_skpd, $data['no_panjar']]))->first();
            if ($cek_panjar->jumlah > 0) {
                return response()->json([
                    'message' => '4'
                ]);
            }

            DB::table('tr_jpanjar')
                ->where(['kd_skpd' => $kd_skpd, 'no_kas' => $data['no_simpan']])
                ->update([
                    'no_kas' => $data['no_panjar'],
                    'tgl_kas' => $data['tgl_panjar'],
                    'keterangan' => $data['keterangan'],
                    'pengguna' => Auth::user()->nama
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
            DB::table('tr_jpanjar')
                ->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::table('tr_panjar')
                ->where(['no_panjar_lalu' => $no_panjar])
                ->update([
                    'status_kembali' => '0'
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
