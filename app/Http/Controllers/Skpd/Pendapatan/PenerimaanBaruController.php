<?php

namespace App\Http\Controllers\Skpd\Pendapatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class PenerimaanBaruController extends Controller
{
    // First

    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $status_ang_pend = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();

        $tetap1 = DB::table('tr_tetap')
            ->selectRaw("no_tetap, tgl_tetap, kd_skpd, keterangan, nilai, kd_rek6, kd_rek_lo,
                (SELECT a.nm_rek6 FROM ms_rek6 a WHERE a.kd_rek6=tr_tetap.kd_rek6) as nm_rek, kd_sub_kegiatan")
            // ->whereRaw("no_tetap not in(select no_tetap from tr_terima)")
            ->where(['kd_skpd' => $kd_skpd]);

        $from = DB::table('tr_tetap')
            ->selectRaw("*,(SELECT a.nm_rek6 FROM ms_rek6 a WHERE a.kd_rek6=tr_tetap.kd_rek6) as nm_rek")
            ->where(['kd_skpd' => $kd_skpd]);

        $join1 = DB::table('tr_terima')
            ->selectRaw("no_tetap as tetap,ISNULL(SUM(nilai),0) as nilai_terima")
            ->where(['kd_skpd' => $kd_skpd])
            ->groupBy('no_tetap');

        $tetap2 = DB::table($from, 'a')->leftJoinSub($join1, 'b', function ($join) {
            $join->on('a.no_tetap', '=', 'b.tetap');
        })
            ->selectRaw("no_tetap,tgl_tetap,kd_skpd,keterangan,ISNULL(nilai,0)-ISNULL(nilai_terima,0) as nilai,kd_rek6,kd_rek_lo,a.nm_rek, a.kd_sub_kegiatan as kd_sub_kegiatan")
            ->whereRaw("nilai != nilai_terima")
            ->unionAll($tetap1);

        $tetap = DB::table(DB::raw("({$tetap2->toSql()}) AS sub"))
            ->mergeBindings($tetap2)
            ->orderBy('no_tetap')
            ->get();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_akun' => DB::table('trdrka as a')
                ->leftJoin('ms_rek6 as b', 'a.kd_rek6', '=', 'b.kd_rek6')
                ->leftJoin('ms_rek5 as c', DB::raw("left(a.kd_rek6,8)"), '=', 'c.kd_rek5')
                ->selectRaw("a.kd_rek6 as kd_rek6,b.nm_rek6 AS nm_rek,b.map_lo as kd_rek_lo, c.nm_rek5, a.kd_sub_kegiatan")
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
                        $query->whereRaw("left(kd_skpd,22)=left(?,22)", [$kd_skpd]);
                    }
                })
                ->orderByRaw("kd_pengirim")
                ->get(),
            'daftar_penetapan' => $tetap,
            'no_penetapan' => DB::table('tr_tetap')
                ->where('kd_skpd', '=', $kd_skpd)->get(),
            'ms_pajak' => DB::table('ms_pajak')
                ->where('status_aktif', '=', 1)
                ->get()

        ];

        return view('skpd.penerimaan_tahun_ini.indexnew')->with($data);
    }

    public function listpenerimaan()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $spjbulan = cek_status_spj_pend($kd_skpd);
        $data = DB::table('tr_terima as a')
            ->selectRaw("no_terima,no_tetap,tgl_terima,tgl_tetap,kd_skpd,keterangan as ket, sumber,
        nilai, kd_rek6,kd_rek_lo,kd_sub_kegiatan,sts_tetap, status_setor, jns_pembayaran, jns_pajak,(CASE WHEN month(tgl_terima)<=? THEN 1 ELSE 0 END) ketspj,user_name,kunci", [$spjbulan])
            ->where(['a.kd_skpd' => $kd_skpd])
            ->where(function ($query) {
                $query->where('jenis', '<>', '2')->orWhereNull('jenis');
            })
            ->orderBy('tgl_terima')
            ->orderBy('no_terima')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="javascript:void(0);" id="edit" class="btn btn-info btn-sm" style="margin-right:4px; margin-left:20px"><i class="bx bx-edit-alt"></i></a>';
            $btn .= '<a href="javascript:void(0);" id="hapusdata" onclick="hapusdata(\'' . $row->no_terima . '\')" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="bx bx-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function simpanpenerimaan(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $cdengan_penetapan = $request->input(['cdengan_penetapan']);
        $cno_tetap = $request->input(['cno_tetap']);
        $ctgl_tetap = $request->input(['ctgl_tetap']);
        $cno_terima = $request->input(['cno_terima']);
        $cstatusSetor = $request->input(['cstatusSetor']);
        $cjns_pembayaran = $request->input(['cjns_pembayaran']);
        $crekening = $request->input(['crekening']);
        $ctgl_terima = $request->input(['ctgl_terima']);
        $cnilai_terima = $request->input(['cnilai_terima']);
        $cketerangan = $request->input(['cketerangan']);
        $ckd_sub_kegiatan = $request->input(['ckd_sub_kegiatan']);
        $ckd_rek_lo = $request->input(['ckd_rek_lo']);
        $cjenispajak = $request->input(['cjenispajak']);


        try {
            DB::beginTransaction();

            $terima = DB::table('tr_terima')
                ->where('no_terima', '=', $cno_terima)
                ->where('kd_skpd', '=', $kd_skpd)
                ->count();

            if ($terima > 0) {
                return response()->json([
                    'message' => 'sudah ada'
                ]);
            }

            DB::table('tr_terima')->insert([
                'no_terima' => $cno_terima,
                'tgl_terima' =>  $ctgl_terima,
                'no_tetap' =>  $cno_tetap,
                'tgl_tetap' => $ctgl_tetap,
                'sts_tetap' => $cdengan_penetapan,
                'kd_skpd' => $kd_skpd,
                'kd_sub_kegiatan' => $ckd_sub_kegiatan,
                'kd_rek6' => $crekening,
                'kd_rek_lo' => $ckd_rek_lo,
                'nilai' => $cnilai_terima,
                'keterangan' => $cketerangan,
                'jenis' => '1',
                'sumber' => '-',
                'status_setor' => $cstatusSetor,
                'jns_pembayaran' =>  $cjns_pembayaran,
                'jns_pajak' => $cjenispajak
            ]);
            if ($cstatusSetor == 'Tanpa Setor') {
                DB::table('trhkasin_pkd')->insert([
                    'no_sts'            => $cno_terima,
                    'no_terima'         => $cno_terima,
                    'kd_skpd'           => $kd_skpd,
                    'tgl_sts'           => $ctgl_terima,
                    'keterangan'        => $cketerangan,
                    'total'             => $cnilai_terima,
                    'kd_sub_kegiatan'   => $ckd_sub_kegiatan,
                    'jns_trans'         => '4',
                    'pot_khusus'        => '0',
                    'sumber'            => '-',
                ]);
                DB::table('trdkasin_pkd')->insert([
                    'no_sts'            => $cno_terima,
                    'kd_skpd'           => $kd_skpd,
                    'kd_rek6'           => $crekening,
                    'rupiah'            => $cnilai_terima,
                    'kd_sub_kegiatan'   => $ckd_sub_kegiatan,
                    'no_terima'         => $cno_terima,
                    'sumber'            => '-',
                ]);
                DB::table('tr_terima')
                    ->where('no_terima', $cno_terima)
                    ->update(['kunci' => 1]);
            }

            DB::commit();
            return response()->json([
                'message' => 'berhasil'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'tidak berhasil'
            ]);
        }
    }

    public function updatepenerimaan(Request $request, $no_terima)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_terima_edit = $request->input('no_terima_edit');
        $status_setor_edit = $request->input('status_setor_edit');
        $jenis_pembayaran_edit = $request->input('jenis_pembayaran_edit');
        $tanggal_edit = $request->input('tanggal_edit');
        $kd_rek_lo_edit = $request->input('kd_rek_lo_edit');
        $jenispajak_edit = $request->input('jenispajak_edit');
        $nilai_terima_edit = $request->input('nilai_terima_edit');
        $ket_edit = $request->input('ket_edit');
        $kdkegiatan_edit = $request->input('kdkegiatan_edit');

        $tgl_tetap_edit = $request->input('tgl_tetap_edit');
        $no_tetap_edit = $request->input('no_tetap_edit');
        $rekening_edit = $request->input('rekening_edit');
        // return dd($kdkegiatan_edit);

        try {
            DB::beginTransaction();
            DB::table('tr_terima')
                ->where('no_terima', $no_terima)
                ->where('kd_skpd', $kd_skpd)
                ->update([
                    'no_terima' => $no_terima_edit,
                    'tgl_terima' => $tanggal_edit,
                    'no_tetap' => $no_tetap_edit,
                    'tgl_tetap' => $tgl_tetap_edit,
                    'kd_skpd' => $kd_skpd,
                    'kd_sub_kegiatan' => $kdkegiatan_edit,
                    'kd_rek6' => $rekening_edit,
                    'kd_rek_lo' => $kd_rek_lo_edit,
                    'nilai' => $nilai_terima_edit,
                    'keterangan' => $ket_edit,
                    'jenis' => '1',
                    'sumber' => '-',
                    'status_setor' => $status_setor_edit,
                    'jns_pembayaran' => $jenis_pembayaran_edit,
                    'jns_pajak' => $jenispajak_edit

                ]);
            DB::commit();
            return redirect()->route('penerimaantahunini.index');
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    public function hapuspenerimaan(Request $request)
    {
        $cno_terima = $request->cno_terima;
        try {
            DB::beginTransaction();
            $no_terima =  DB::table('trhkasin_pkd')
                ->where('no_terima', '=', $cno_terima)
                ->count();
            if ($no_terima > 0) {
                return response()->json([
                    'message' => '0'
                ]);
            }
            DB::table('tr_terima')
                ->where('no_terima', '=', $cno_terima)
                ->delete();
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => '2'
            ]);
        }
    }
}
