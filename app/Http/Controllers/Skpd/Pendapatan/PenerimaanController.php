<?php

namespace App\Http\Controllers\Skpd\Pendapatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class PenerimaanController extends Controller
{
    // Penerimaan Tahun Lalu
    public function indexPenerimaanLalu()
    {
        return view('skpd.penerimaan_tahun_lalu.index');
    }

    public function loadDataPenerimaanLalu()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_terima as a')
            ->selectRaw("a.*")
            ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '2'])
            ->orderBy('tgl_terima')
            ->orderBy('no_terima')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if ($row->kunci != '1') {
                $btn = '<a href="' . route("penerimaan_lalu.edit", Crypt::encrypt($row->no_terima)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_terima . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            } else {
                $btn = '';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahPenerimaanLalu()
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

        return view('skpd.penerimaan_tahun_lalu.create')->with($data);
    }

    public function simpanPenerimaanLalu(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;
        $jns_pembayaran = $data['jns_pembayaran'];

        DB::beginTransaction();
        try {
            if ($jns_pembayaran == 'TUNAI') {
                DB::table('tr_terima')->insert([
                    'no_terima' => $data['no_terima'],
                    'tgl_terima' => $data['tgl_terima'],
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['rekening'],
                    'kd_rek_lo' => $data['kode_rek'],
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'jenis' => '2',
                    'sumber' => '-',
                    'status_setor' => $data['statusSetor'],
                    'jns_pembayaran' => $data['jns_pembayaran'],
                    'jns_pajak' => $data['pajakk'],
                    'user_name' => Auth::user()->nama,
                ]);
            }
            if ($jns_pembayaran == 'BANK') {
                DB::table('tr_terima')->insert([
                    'no_terima' => $data['no_terima'],
                    'tgl_terima' => $data['tgl_terima'],
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['rekening'],
                    'kd_rek_lo' => $data['kode_rek'],
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'jenis' => '2',
                    'sumber' => '-',
                    'status_setor' => $data['statusSetor'],
                    'jns_pembayaran' => $data['jns_pembayaran'],
                    'jns_pajak' => $data['pajakk'],
                    'user_name' => Auth::user()->nama,
                ]);

                DB::table('trhkasin_pkd')->insert([
                    'no_sts' => $data['no_terima'],
                    'kd_skpd' => $data['kd_skpd'],
                    'tgl_sts' => $data['tgl_terima'],
                    'keterangan' => $data['keterangan'],
                    'total' => $data['nilai'],
                    'kd_bank' => '',
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'jns_trans' => '4',
                    // 'rek_bank' => '',
                    // 'no_kas' => '',
                    // 'no_cek' => '',
                    // 'status' => '',
                    // 'jns_cp' => '',
                    'pot_khusus' => '0',
                    // 'no_sp2d' => '',
                    'no_terima' => $data['no_terima'],
                    // 'sumber' => '',
                    'user_name' => Auth::user()->nama,
                    'bank' => ''
                ]);

                DB::table('trdkasin_pkd')->insert([
                    'kd_skpd' => $data['kd_skpd'],
                    'no_sts' => $data['no_terima'],
                    'kd_rek6' => $data['rekening'],
                    'rupiah' => $data['nilai'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'no_terima' => $data['no_terima'],
                    'sumber' => '-'
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

    public function editPenerimaanLalu($no_terima)
    {
        $no_terima = Crypt::decrypt($no_terima);
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
            'terima' => DB::table('tr_terima as a')
                ->selectRaw("a.*")
                ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '2', 'no_terima' => $no_terima])
                ->orderBy('tgl_terima')
                ->orderBy('no_terima')
                ->first()
        ];

        return view('skpd.penerimaan_tahun_lalu.edit')->with($data);
    }

    public function simpanEditPenerimaanLalu(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {

            DB::table('tr_terima')->where(['no_terima' => $data['no_simpan'], 'kd_skpd' => $data['kd_skpd']])->delete();

            DB::table('tr_terima')->insert([
                'no_terima' => $data['no_terima'],
                'tgl_terima' => $data['tgl_terima'],
                'kd_skpd' => $data['kd_skpd'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'kd_rek6' => $data['rekening'],
                'kd_rek_lo' => $data['kode_rek'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'jenis' => '2',
                'sumber' => '-',
                'status_setor' => $data['statusSetor'],
                'jns_pembayaran' => $data['jns_pembayaran'],
                'jns_pajak' => $data['pajakk']
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

    public function hapusPenerimaanLalu(Request $request)
    {
        $no_terima = $request->no_terima;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_terima')->where(['no_terima' => $no_terima, 'kd_skpd' => $kd_skpd, 'jenis' => '2'])->delete();

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

    // Penerimaan Tahun Ini
    public function indexPenerimaanIni()
    {
        return view('skpd.penerimaan_tahun_ini.index');
    }

    public function loadDataPenerimaanIni()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $spjbulan = cek_status_spj_pend($kd_skpd);
        $data = DB::table('tr_terima as a')
            ->selectRaw("no_terima,no_tetap,tgl_terima,tgl_tetap,kd_skpd,keterangan as ket, sumber,
        nilai, kd_rek6,kd_rek_lo,kd_sub_kegiatan,sts_tetap,(CASE WHEN month(tgl_terima)<=? THEN 1 ELSE 0 END) ketspj,user_name,kunci", [$spjbulan])
            ->where(['a.kd_skpd' => $kd_skpd])
            ->where(function ($query) {
                $query->where('jenis', '<>', '2')->orWhereNull('jenis');
            })
            ->orderBy('tgl_terima')
            ->orderBy('no_terima')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("penerimaan_ini.edit", Crypt::encrypt($row->no_terima)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->kunci != 0) {
                $btn .= '';
            } else {

                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_terima . '\',\'' . $row->no_tetap . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahPenerimaanIni()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $status_ang_pend = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();

        $tetap = DB::select("SELECT no_tetap, jenis,tgl_tetap, kd_skpd, keterangan, nilai, kd_rek6, kd_rek_lo, kd_sub_kegiatan,
                            (SELECT a.nm_rek6 FROM ms_rek6 a WHERE a.kd_rek6=tr_tetap.kd_rek6) as nm_rek FROM tr_tetap WHERE kd_skpd = ?
                            AND no_tetap not in(select isnull(no_tetap,'') from tr_terima)
                            UNION ALL
                            SELECT no_tetap,jenis,tgl_tetap,kd_skpd,keterangan,ISNULL(nilai,0)-ISNULL(nilai_terima,0) as nilai,kd_rek6,kd_rek_lo,kd_sub_kegiatan,a.nm_rek
                            FROM
                            (SELECT *,(SELECT a.nm_rek6 FROM ms_rek6 a WHERE a.kd_rek6=tr_tetap.kd_rek6) as nm_rek FROM tr_tetap WHERE kd_skpd = ? )a
                            LEFT JOIN
                            (SELECT no_tetap as tetap,ISNULL(SUM(nilai),0) as nilai_terima from tr_terima WHERE kd_skpd = ? GROUP BY no_tetap)b
                            ON a.no_tetap=b.tetap
                            WHERE nilai !=nilai_terima
                            order by no_tetap", [$kd_skpd, $kd_skpd, $kd_skpd]);

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
                ->orderByRaw("cast(kd_pengirim as int)")
                ->orderByRaw("kd_pengirim")
                ->get(),
            'daftar_penetapan' => $tetap
        ];

        return view('skpd.penerimaan_tahun_ini.create')->with($data);
    }

    public function cekSimpan(Request $request)
    {
        $no_terima = $request->no_terima;
        $data = DB::table('tr_terima')
            ->where('no_terima', $no_terima)
            ->count();
        return response()->json($data);
    }

    public function simpanPenerimaanIni(Request $request)
    {
        $data = $request->data;

        $jns_pembayaran = $data['jns_pembayaran'];

        DB::beginTransaction();
        try {

            if ($data['tanpa_setor'] == 'true') {
                DB::table('tr_terima')->insert([
                    'no_terima' => $data['no_terima'],
                    'tgl_terima' => $data['tgl_terima'],
                    'no_tetap' => $data['no_tetap'],
                    'tgl_tetap' => $data['tgl_tetap'],
                    'sts_tetap' => $data['dengan_penetapan'],
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['kode_akun'],
                    'kd_rek_lo' => $data['kode_rek'],
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'jenis' => '1',
                    'sumber' => '-',
                    'status_setor' => $data['statusSetor'],
                    'jns_pembayaran' => $data['jns_pembayaran'],
                    'jns_pajak' => $data['pajakk'],
                    'user_name' => Auth::user()->nama,
                ]);
            }

            if ($data['dengan_setor'] == 'true') {
                $cekSetor = DB::table('trhkasin_pkd')
                    ->where([
                        'no_sts' => $data['no_sts'],
                        'kd_skpd' => $data['kd_skpd']
                    ])
                    ->count();

                if ($cekSetor > 0) {
                    return response()->json([
                        'message' => '2'
                    ]);
                }

                DB::table('tr_terima')
                    ->insert([
                        'no_terima' => $data['no_terima'],
                        'tgl_terima' => $data['tgl_terima'],
                        'no_tetap' => $data['no_tetap'],
                        'tgl_tetap' => $data['tgl_tetap'],
                        'sts_tetap' => $data['dengan_penetapan'],
                        'kd_skpd' => $data['kd_skpd'],
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                        'kd_rek6' => $data['kode_akun'],
                        'kd_rek_lo' => $data['kode_rek'],
                        'nilai' => $data['nilai'],
                        'keterangan' => $data['keterangan'],
                        'jenis' => '1',
                        'sumber' => '-',
                        'kunci' => '1',
                        'status_setor' => $data['statusSetor'],
                        'jns_pembayaran' => $data['jns_pembayaran'],
                        'jns_pajak' => $data['pajakk'],
                        'user_name' => Auth::user()->nama,
                    ]);

                DB::table('trhkasin_pkd')
                    ->insert([
                        'no_sts' => $data['no_sts'],
                        'kd_skpd' => $data['kd_skpd'],
                        'tgl_sts' => $data['tgl_terima'],
                        'keterangan' => $data['keterangan'],
                        'total' => $data['nilai'],
                        'kd_bank' => '',
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                        'jns_trans' => '4',
                        // 'rek_bank' => '',
                        // 'no_kas' => '',
                        // 'no_cek' => '',
                        // 'status' => '',
                        // 'jns_cp' => '',
                        'pot_khusus' => '0',
                        // 'no_sp2d' => '',
                        'no_terima' => $data['no_terima'],
                        // 'sumber' => '',
                        'user_name' => Auth::user()->nama,
                        'bank' => ''
                    ]);

                DB::table('trdkasin_pkd')
                    ->insert([
                        'kd_skpd' => $data['kd_skpd'],
                        'no_sts' => $data['no_sts'],
                        'kd_rek6' => $data['kode_akun'],
                        'rupiah' => $data['nilai'],
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                        'no_terima' => $data['no_terima'],
                        'sumber' => '-'
                    ]);
            }

            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
            ]);
        }
    }

    public function editPenerimaanIni($no_terima)
    {
        $no_terima = Crypt::decrypt($no_terima);
        $kd_skpd = Auth::user()->kd_skpd;
        $status_ang_pend = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();

        $tetap = DB::select("SELECT no_tetap, jenis,tgl_tetap, kd_skpd, keterangan, nilai, kd_rek6, kd_rek_lo, kd_sub_kegiatan,
                            (SELECT a.nm_rek6 FROM ms_rek6 a WHERE a.kd_rek6=tr_tetap.kd_rek6) as nm_rek FROM tr_tetap WHERE kd_skpd = ?
                            AND no_tetap not in(select isnull(no_tetap,'') from tr_terima)
                            UNION ALL
                            SELECT no_tetap,jenis,tgl_tetap,kd_skpd,keterangan,ISNULL(nilai,0)-ISNULL(nilai_terima,0) as nilai,kd_rek6,kd_rek_lo,kd_sub_kegiatan,a.nm_rek
                            FROM
                            (SELECT *,(SELECT a.nm_rek6 FROM ms_rek6 a WHERE a.kd_rek6=tr_tetap.kd_rek6) as nm_rek FROM tr_tetap WHERE kd_skpd = ? )a
                            LEFT JOIN
                            (SELECT no_tetap as tetap,ISNULL(SUM(nilai),0) as nilai_terima from tr_terima WHERE kd_skpd = ? GROUP BY no_tetap)b
                            ON a.no_tetap=b.tetap
                            WHERE nilai !=nilai_terima
                            order by no_tetap", [$kd_skpd, $kd_skpd, $kd_skpd]);

        $data = [
            'terima' => DB::table('tr_terima')
                ->where(['no_terima' => $no_terima, 'kd_skpd' => $kd_skpd])
                ->first(),
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
                ->get(),
            'daftar_penetapan' => $tetap
        ];

        // dd ($data);

        return view('skpd.penerimaan_tahun_ini.edit')->with($data);
    }

    public function simpanEditPenerimaanIni(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;
        $jns_pembayaran = $data['jns_pembayaran'];
        DB::beginTransaction();
        try {
            if ($jns_pembayaran == 'TUNAI') {
                DB::table('tr_terima')
                    ->where(['no_terima' => $data['no_simpan'], 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'no_terima' => $data['no_terima'],
                        'tgl_terima' => $data['tgl_terima'],
                        'kd_skpd' => $data['kd_skpd'],
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                        'kd_rek6' => $data['kode_akun'],
                        'kd_rek_lo' => $data['kode_rek'],
                        'nilai' => $data['nilai'],
                        'keterangan' => $data['keterangan'],
                        'jenis' => '1',
                        'sumber' => '-',
                        'status_setor' => $data['statusSetor'],
                        'jns_pembayaran' => $data['jns_pembayaran'],
                        'jns_pajak' => $data['pajakk'],
                        'user_name' => Auth::user()->nama,
                    ]);
            }
            if ($jns_pembayaran == 'BANK') {
                DB::table('tr_terima')
                    ->where(['no_terima' => $data['no_simpan'], 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'no_terima' => $data['no_terima'],
                        'tgl_terima' => $data['tgl_terima'],
                        'kd_skpd' => $data['kd_skpd'],
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                        'kd_rek6' => $data['kode_akun'],
                        'kd_rek_lo' => $data['kode_rek'],
                        'nilai' => $data['nilai'],
                        'keterangan' => $data['keterangan'],
                        'jenis' => '1',
                        'sumber' => '-',
                        'kunci' => '1',
                        'status_setor' => $data['statusSetor'],
                        'jns_pembayaran' => $data['jns_pembayaran'],
                        'jns_pajak' => $data['pajakk'],
                        'user_name' => Auth::user()->nama,
                    ]);

                DB::table('trhkasin_pkd')
                    ->where(['no_terima' => $data['no_simpan'], 'kd_skpd' => $data['kd_skpd'], 'no_sts' => $data['no_sts']])
                    ->update([
                        'no_sts' => $data['no_terima'],
                        'kd_skpd' => $data['kd_skpd'],
                        'tgl_sts' => $data['tgl_terima'],
                        'keterangan' => $data['keterangan'],
                        'total' => $data['nilai'],
                        'kd_bank' => '',
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                        'jns_trans' => '4',
                        // 'rek_bank' => '',
                        // 'no_kas' => '',
                        // 'no_cek' => '',
                        // 'status' => '',
                        // 'jns_cp' => '',
                        'pot_khusus' => '0',
                        // 'no_sp2d' => '',
                        'no_terima' => $data['no_terima'],
                        // 'sumber' => '',
                        'user_name' => Auth::user()->nama,
                        'bank' => ''
                    ]);

                DB::table('trdkasin_pkd')
                    ->where(['no_terima' => $data['no_simpan'], 'kd_skpd' => $data['kd_skpd'], 'no_sts' => $data['no_sts']])
                    ->update([
                        'kd_skpd' => $data['kd_skpd'],
                        'no_sts' => $data['no_terima'],
                        'kd_rek6' => $data['kode_akun'],
                        'rupiah' => $data['nilai'],
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                        'no_terima' => $data['no_terima'],
                        'sumber' => '-'
                    ]);
            }


            // DB::table('tr_terima')->where(['no_terima' => $data['no_simpan'], 'kd_skpd' => $data['kd_skpd']])->delete();

            // DB::table('tr_terima')->insert([
            //     'no_terima' => $data['no_terima'],
            //     'tgl_terima' => $data['tgl_terima'],
            //     'no_tetap' => $data['no_tetap'],
            //     'tgl_tetap' => $data['tgl_tetap'],
            //     'sts_tetap' => $data['dengan_penetapan'],
            //     'kd_skpd' => $data['kd_skpd'],
            //     'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
            //     'kd_rek6' => $data['kode_akun'],
            //     'kd_rek_lo' => $data['kode_rek'],
            //     'nilai' => $data['nilai'],
            //     'keterangan' => $data['keterangan'],
            //     'jenis' => '1',
            //     'sumber' => '-',
            //     'status_setor' => $data['statusSetor'],
            //     'jns_pembayaran' => $data['jns_pembayaran'],
            //     'jns_pajak' => $data['pajakk']
            // ]);

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

    public function hapusPenerimaanIni(Request $request)
    {
        $no_terima = $request->no_terima;
        $no_tetap = $request->no_tetap;
        $kd_skpd = $request->kd_skpd;
        $jenis = $request->jenis;

        DB::beginTransaction();
        try {
            if ($jenis == '1') {
                DB::table('tr_terima')
                    ->where(['no_terima' => $no_terima, 'kd_skpd' => $kd_skpd])
                    ->delete();
            }
            if ($jenis == '2') {
                DB::table('tr_terima')
                    ->where(['no_terima' => $no_terima, 'kd_skpd' => $kd_skpd])
                    ->delete();

                DB::table('tr_tetap')
                    ->where(['no_tetap' => $no_tetap, 'kd_skpd' => $kd_skpd])
                    ->delete();
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

    // Penerimaan Lain PPKD
    public function indexPenerimaanPpkd()
    {
        return view('skpd.penerimaan_lain_ppkd.index');
    }

    public function loadDataPenerimaanPpkd()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhkasin_pkd as a')
            ->join('trdkasin_pkd as b', function ($join) {
                $join->on('a.no_sts', '=', 'b.no_sts');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek6 as c', function ($join) {
                $join->on('b.kd_rek6', '=', 'c.kd_rek6');
            })
            ->selectRaw("a.*, b.kd_sub_kegiatan, b.kd_rek6, c.nm_rek6,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) AS nm_skpd")
            ->where(['a.kd_skpd' => $kd_skpd, 'a.jns_trans' => '4'])
            ->orderByRaw('a.no_sts ASC')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("penerimaan_ppkd.edit", Crypt::encrypt($row->no_sts)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_sts . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahPenerimaanPpkd()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'daftar_jenis' => DB::table('trdrka as a')
                ->select('kd_rek6', 'nm_rek6')
                ->whereRaw("left(kd_rek6,1)=? and kd_skpd=?", ['4', '5.02.0.00.0.00.02.0000'])
                ->orderBy('kd_rek6')
                ->distinct()
                ->get(),
            'daftar_pengirim' => DB::table('ms_pengirim as a')
                ->where(['kd_skpd' => $kd_skpd])
                ->orderByRaw('cast(kd_pengirim as int)')
                ->orderByRaw('kd_pengirim')
                ->get(),
        ];

        return view('skpd.penerimaan_lain_ppkd.create')->with($data);
    }

    public function urutPenerimaanPpkd(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('trhkasin_pkd')
            ->selectRaw("count(no_sts)+1 as nomor")
            ->where(['kd_skpd' => $kd_skpd, 'jns_trans' => '4'])
            ->first();

        return response()->json($data);
    }

    public function simpanPenerimaanPpkd(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // $no_urut = DB::table('trhkasin_pkd')
            //     ->selectRaw("count(no_sts)+1 as nomor")
            //     ->where(['kd_skpd' => $kd_skpd, 'jns_trans' => '4'])
            //     ->first();
            // $nomor = $no_urut->nomor;
            $cek_terima = DB::table('trhkasin_pkd')->where(['no_sts' => $data['no_kas'] . '/BP', 'kd_skpd' => '5.02.0.00.0.00.02.0000'])->count();
            if ($cek_terima > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            $no_kas = nomor_tukd();

            DB::table('trhkasin_pkd')->insert([
                'no_sts' => $data['no_kas'] . '/BP',
                'tgl_sts' => $data['tgl_kas'],
                'kd_skpd' => $kd_skpd,
                'keterangan' => $data['keterangan'],
                'total' => $data['nilai'],
                'kd_sub_kegiatan' => '5.02.00.0.00.04',
                'jns_trans' => '4',
                'no_kas' => $no_kas,
                'tgl_kas' => $data['tgl_kas'],
                'sumber' => $data['pengirim'],
                'user_name' => Auth::user()->nama,
                'no_cek' => '1',
                'status' => '1',
            ]);

            DB::table('trdkasin_pkd')->insert([
                'no_sts' => $data['no_kas'] . '/BP',
                'kd_skpd' => $kd_skpd,
                'kd_rek6' => $data['jenis'],
                'rupiah' => $data['nilai'],
                'kd_sub_kegiatan' => '5.02.00.0.00.04',
                'sumber' => $data['pengirim'],
            ]);

            DB::table('trhkasin_ppkd')->insert([
                'no_kas' => $no_kas,
                'no_sts' => $data['no_kas'] . '/BP',
                'kd_skpd' => $kd_skpd,
                'tgl_sts' => $data['tgl_kas'],
                'tgl_kas' => $data['tgl_kas'],
                'keterangan' => $data['keterangan'],
                'total' => $data['nilai'],
                'kd_bank' => '1',
                'kd_sub_kegiatan' => '5.02.00.0.00.04',
                'jns_trans' => '4',
                'rek_bank' => '',
                'sumber' => $data['pengirim'],
                'pot_khusus' => '0',
                'no_sp2d' => '',
                'jns_cp' => '',
                'username' => Auth::user()->nama
            ]);

            DB::table('trdkasin_ppkd')->insert([
                'no_kas' => $no_kas,
                'kd_skpd' => $kd_skpd,
                'no_sts' => $data['no_kas'] . '/BP',
                'kd_rek6' => $data['jenis'],
                'rupiah' => $data['nilai'],
                'kd_sub_kegiatan' => '5.02.00.0.00.04',
                'sumber' => $data['pengirim'],
            ]);

            DB::commit();
            return response()->json([
                'message' => '1',
                'nomor' => $data['no_kas'] . '/BP'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
            ]);
        }
    }

    public function editPenerimaanPpkd($no_sts)
    {
        $no_sts = Crypt::decrypt($no_sts);
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'terima' => $data = DB::table('trhkasin_pkd as a')
                ->join('trdkasin_pkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("a.*,b.kd_rek6")
                ->where(['a.kd_skpd' => $kd_skpd, 'a.jns_trans' => '4', 'a.no_sts' => $no_sts])
                ->first(),
            'daftar_jenis' => DB::table('trdrka as a')
                ->select('kd_rek6', 'nm_rek6')
                ->whereRaw("left(kd_rek6,1)=? and kd_skpd=?", ['4', '5.02.0.00.0.00.02.0000'])
                ->orderBy('kd_rek6')
                ->distinct()
                ->get(),
            'daftar_pengirim' => DB::table('ms_pengirim as a')
                ->where(['kd_skpd' => $kd_skpd])
                // ->orderByRaw("cast(kd_pengirim as int)")
                ->orderByRaw("kd_pengirim")
                ->get()
        ];
        // dd($data['terima']);
        return view('skpd.penerimaan_lain_ppkd.edit')->with($data);
    }

    public function simpanEditPenerimaanPpkd(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhkasin_pkd')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd, 'jns_trans' => '4'])
                ->update([
                    'tgl_sts' => $data['tgl_kas'],
                    'keterangan' => $data['keterangan'],
                    'total' => $data['nilai'],
                    'tgl_kas' => $data['tgl_kas'],
                    'sumber' => $data['pengirim'],
                ]);

            DB::table('trdkasin_pkd')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd])
                ->whereRaw("LEFT(kd_rek6,1)=?", ['4'])
                ->update([
                    'kd_rek6' => $data['jenis'],
                    'rupiah' => $data['nilai'],
                    'sumber' => $data['pengirim'],
                ]);

            DB::table('trhkasin_ppkd')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd, 'jns_trans' => '4'])
                ->update([
                    'tgl_sts' => $data['tgl_kas'],
                    'tgl_kas' => $data['tgl_kas'],
                    'keterangan' => $data['keterangan'],
                    'total' => $data['nilai'],
                    'sumber' => $data['pengirim'],
                ]);

            DB::table('trdkasin_ppkd')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd])
                ->whereRaw("LEFT(kd_rek6,1)=?", ['4'])
                ->update([
                    'kd_rek6' => $data['jenis'],
                    'rupiah' => $data['nilai'],
                    'sumber' => $data['pengirim'],
                ]);

            DB::commit();
            return response()->json([
                'message' => '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
            ]);
        }
    }

    public function hapusPenerimaanPpkd(Request $request)
    {
        $no_kas = $request->no_kas;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhkasin_pkd')
                ->where(['no_sts' => $no_kas, 'kd_skpd' => $kd_skpd, 'jns_trans' => '4'])
                ->delete();

            DB::table('trdkasin_pkd')
                ->where(['no_sts' => $no_kas, 'kd_skpd' => $kd_skpd])
                ->whereRaw("LEFT(kd_rek6,1)=?", ['4'])
                ->delete();

            DB::table('trhkasin_ppkd')
                ->where(['no_sts' => $no_kas, 'kd_skpd' => $kd_skpd, 'jns_trans' => '4'])
                ->delete();

            DB::table('trdkasin_ppkd')
                ->where(['no_sts' => $no_kas, 'kd_skpd' => $kd_skpd])
                ->whereRaw("LEFT(kd_rek6,1)=?", ['4'])
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

    // Penerimaan Lain PPKD
    public function indexPenerimaanKas()
    {
        return view('skpd.penerimaan_kas.index');
    }

    //created by calvin
    public function cekDataValidasi()
    {
        $data =
            [
                'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
                'detail' => DB::select("SELECT*FROM (
                    SELECT no_sts,tgl_sts,total,kd_skpd FROM trhkasin_pkd UNION ALL
                    SELECT no_bukti AS no_sts,TGL_BUKTI AS tgl_sts,nilai AS total,kd_skpd FROM TRHOUTLAIN WHERE jns_beban='1') a WHERE a.no_sts NOT IN (
                    SELECT no_sts FROM trhkasin_ppkd WHERE kd_skpd=a.kd_skpd) ORDER BY tgl_sts")
            ];
        //dd($data['detail']);

        return view('skpd.penerimaan_kas.cek')->with($data)->with('_blank');;
    }
    //end

    public function loadDataPenerimaanKas(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $tipe = $request->tipe;

        if ($tipe == "cair") {
            $ambil = "Where a.status = '1'";
            $ambil1 = "Where a.status = '1'";
        } elseif ($tipe == "belum") {
            $ambil = "WHERE a.no_sts NOT IN (SELECT no_sts FROM trhkasin_ppkd WHERE kd_skpd=a.kd_skpd)";
            $ambil1 = "WHERE a.no_bukti NOT IN (SELECT no_sts FROM trhkasin_ppkd WHERE kd_skpd=a.kd_skpd)";
        } else {
            $ambil = "";
            $ambil1 = "";
        }


        $data = DB::select("SELECT
        a.kd_sub_kegiatan AS kd_sub_kegiatan,
        a.status AS status,
        a.no_sts AS no_sts,
        a.kd_skpd AS kd_skpd,
        a.tgl_sts AS tgl_sts,
        a.total AS total,
        a.keterangan AS keterangan,
        b.no_kas AS no_kas,
        b.tgl_kas AS tgl_kas,
        ( SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd ) AS nm_skpd,
        rtrim( a.jns_trans ) AS nm_trans
        -- (
        -- SELECT
        --     rtrim( rek_bank ) AS rek_bank
        -- FROM
        --     trhkasin_ppkd
        -- WHERE
        --     no_sts = a.no_sts
        --     AND kd_skpd = a.kd_skpd
        --     -- AND b.kd_sub_kegiatan = a.kd_sub_kegiatan
        -- ) AS rek_bank1
        -- ( SELECT TOP 1 rtrim( rek_bank ) AS rek_bank FROM trhkasin_ppkd WHERE no_sts = a.no_sts AND kd_skpd = a.kd_skpd ORDER BY rek_bank) AS rek_bank1
    FROM
        trhkasin_pkd a
        LEFT JOIN trhkasin_ppkd b ON b.kd_skpd= a.kd_skpd
        AND b.no_sts= a.no_sts
        -- AND b.kd_sub_kegiatan = a.kd_sub_kegiatan
        $ambil
        UNION ALL
    SELECT
        '' AS kd_sub_kegiatan,
        a.status AS status,
        a.no_bukti AS no_sts,
        a.kd_skpd ,
        a.tgl_bukti AS tgl_sts,
        a.nilai AS total,
        a.KET AS keterangan,
        b.no_kas AS no_kas,
        b.tgl_kas AS tgl_kas,
        ( SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd ) AS nm_skpd,
        rtrim( a.jns_beban ) AS nm_trans
        -- ( SELECT rtrim( rek_bank ) AS rek_bank FROM trhkasin_ppkd WHERE no_sts = b.no_sts AND kd_skpd = b.kd_skpd ) AS rek_bank1
        -- ( SELECT TOP 1 rtrim( rek_bank ) AS rek_bank FROM trhkasin_ppkd WHERE no_sts = b.no_sts AND kd_skpd = b.kd_skpd ORDER BY rek_bank) AS rek_bank1
    FROM
        trhoutlain a
        LEFT JOIN trhkasin_ppkd b ON b.kd_skpd= a.kd_skpd
        AND a.KET = b.keterangan
        AND a.no_bukti= b.no_sts
        $ambil1");


        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("penerimaan_kas.edit", [Crypt::encrypt($row->no_sts), Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->status != 1) {
                $btn .= '';
            } else {
                $btn .= '<a href="javascript:void(0);" onclick="batal(\'' . $row->no_sts . '\',\'' . $row->kd_skpd . '\',\'' . $row->no_kas . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-ban"></i></a>';
            }
            // $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_kas . '\',\'' . $row->no_sts . '\',\'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm" style="margin-right:4px"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahPenerimaanKas()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'daftar_skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd', 'jns')
                ->orderBy('kd_skpd')
                ->get(),
            'daftar_jenis' => DB::table('trdrka as a')
                ->select('kd_rek6', 'nm_rek6')
                ->whereRaw("left(kd_rek6,1)=? and kd_skpd=?", ['4', '5.02.0.00.0.00.02.0000'])
                ->orderBy('kd_rek6')
                ->distinct()
                ->get(),
            'daftar_pengirim' => DB::table('ms_pengirim as a')
                ->where(['kd_skpd' => $kd_skpd])
                // ->orderByRaw("cast(kd_pengirim as int)")
                ->orderByRaw("kd_pengirim")
                ->get()
        ];

        return view('skpd.penerimaan_kas.create')->with($data);
    }

    public function noBuktiPenerimaanKas(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $tgl_kas = $request->tgl_kas;

        // if ($kd_skpd == '1.02.0.00.0.00.01.0000') {
        //     $data1 = DB::table('trhkasin_pkd')
        //         ->selectRaw("no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total")
        //         ->whereRaw("no_sts+jns_trans NOT IN(SELECT a.no_sts+jns_trans FROM trhkasin_ppkd a where kd_skpd=? and a.jns_trans=4 ) and kd_skpd=? and tgl_sts=? and jns_trans=4", [$kd_skpd, $kd_skpd, $tgl_kas]);

        //     $data2 = DB::table('trhkasin_pkd')
        //         ->selectRaw("no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total")
        //         ->whereRaw("no_sts+jns_trans NOT IN(SELECT a.no_sts+jns_trans FROM trhkasin_ppkd a where kd_skpd=? and a.jns_trans NOT IN (4,3)) and kd_skpd=? and tgl_sts=? and jns_trans NOT IN (4,3)", [$kd_skpd, $kd_skpd, $tgl_kas])
        //         ->unionAll($data1);

        //     $data3 = DB::table('TRHOUTLAIN')
        //         ->selectRaw("NO_BUKTI no_sts, TGL_BUKTI tgl_sts, KD_SKPD, KET keterangan, (CASE WHEN thnlalu='1' THEN 'y' ELSE 'n' END) sumber,
        // 		'' kd_sub_kegiatan, '' jns_trans,'' jns_cp ,nilai as total")
        //         ->whereRaw("KD_SKPD=? AND TGL_BUKTI=? AND jns_beban<>7 AND NO_BUKTI NOT IN (select no_sts from trhkasin_ppkd where  sumber='y')", [$kd_skpd, $tgl_kas])
        //         ->unionAll($data2);
        // } else {
        //     $data1 = DB::table('trhkasin_pkd')
        //         ->selectRaw("no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total")
        //         ->whereRaw("no_sts+kd_skpd+jns_trans NOT IN(SELECT a.no_sts+kd_skpd+jns_trans FROM trhkasin_ppkd a where kd_skpd=? and a.jns_trans=4 ) and kd_skpd=? and tgl_sts=? and jns_trans=4", [$kd_skpd, $kd_skpd, $tgl_kas]);

        //     $data2 = DB::table('trhkasin_pkd')
        //         ->selectRaw("no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total")
        //         ->whereRaw("no_sts+kd_skpd+jns_trans NOT IN(SELECT a.no_sts+kd_skpd+jns_trans FROM trhkasin_ppkd a where kd_skpd=? and a.jns_trans NOT IN (4,3)) and kd_skpd=? and tgl_sts=? and jns_trans NOT IN (4,3)", [$kd_skpd, $kd_skpd, $tgl_kas])
        //         ->unionAll($data1);

        //     $data3 = DB::table('TRHOUTLAIN')
        //         ->selectRaw("NO_BUKTI no_sts, TGL_BUKTI tgl_sts, KD_SKPD, KET keterangan, (CASE WHEN thnlalu='1' THEN 'y' ELSE 'n' END) sumber,
        // 		'' kd_sub_kegiatan, '' jns_trans,'' jns_cp ,nilai as total")
        //         ->whereRaw("KD_SKPD=? AND TGL_BUKTI=? AND jns_beban<>7 AND NO_BUKTI NOT IN (select no_sts from trhkasin_ppkd where  sumber='y')", [$kd_skpd, $tgl_kas])
        //         ->unionAll($data2);
        // }
        // $data = DB::table(DB::raw("({$data3->toSql()}) AS sub"))
        //     ->mergeBindings($data3)
        //     ->get();

        if ($kd_skpd == '1.02.0.00.0.00.01.0000') {
            $data = DB::select("SELECT no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total
				FROM trhkasin_pkd WHERE no_sts+kd_skpd+jns_trans NOT IN(SELECT a.no_sts+kd_skpd+jns_trans FROM trhkasin_ppkd a where kd_skpd=? and a.jns_trans=4 ) AND no_sts+kd_skpd+jns_trans NOT IN(SELECT a.no_sts+kd_skpd_sumber+jns_trans FROM trhkasin_ppkd a where kd_skpd_sumber=? and a.jns_trans=4 )
                and kd_skpd=?
				and tgl_sts=?
				and jns_trans=4 --AND status=1
				UNION ALL
				SELECT no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total
				FROM trhkasin_pkd WHERE no_sts+kd_skpd+jns_trans NOT IN(SELECT a.no_sts+kd_skpd+jns_trans FROM trhkasin_ppkd a where kd_skpd=? and a.jns_trans NOT IN (4,3))
                and kd_skpd=?
				and tgl_sts=?
				and jns_trans NOT IN (4,3)
				UNION ALL
				SELECT NO_BUKTI no_sts, TGL_BUKTI tgl_sts, KD_SKPD, KET keterangan, (CASE WHEN thnlalu='1' THEN 'y' ELSE 'n' END) sumber,
				'' kd_sub_kegiatan, '' jns_trans,'' jns_cp ,nilai as total
				FROM TRHOUTLAIN
				WHERE KD_SKPD=? AND TGL_BUKTI=?
				AND jns_beban<>7
				AND NO_BUKTI NOT IN (select no_sts from trhkasin_ppkd where  sumber='y')
                UNION ALL
                SELECT a.no_bukti, b.tgl_bukti,a.kd_skpd, ket,''sumber,kd_sub_kegiatan,'5'jns_spp,''jns_cp,a.nilai FROM trdstrpot a
                inner join trhstrpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                WHERE a.kd_skpd=? and tgl_bukti=? and a.kd_rek6='210601010007' AND a.no_bukti NOT IN (
                select no_sts from trhkasin_ppkd
                where kd_skpd=? )", [$kd_skpd, $kd_skpd, $kd_skpd, $tgl_kas, $kd_skpd, $kd_skpd, $tgl_kas, $kd_skpd, $tgl_kas, $kd_skpd, $tgl_kas, $kd_skpd]);
        } elseif ($kd_skpd == '1.02.0.00.0.00.01.0000' || $kd_skpd == '1.03.0.00.0.00.01.0000') {
            $data = DB::select("SELECT no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total
				FROM trhkasin_pkd WHERE no_sts+jns_trans NOT IN(SELECT a.no_sts+jns_trans FROM trhkasin_ppkd a where kd_skpd=? OR kd_skpd_sumber=? and a.jns_trans=4 )
                and kd_skpd=?
				and tgl_sts=?
				and jns_trans=4 --AND status=1
				UNION ALL
				SELECT no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total
				FROM trhkasin_pkd WHERE no_sts+jns_trans NOT IN(SELECT a.no_sts+jns_trans FROM trhkasin_ppkd a where kd_skpd=? OR kd_skpd_sumber=? and a.jns_trans NOT IN (4,3))
                and kd_skpd=?
				and tgl_sts=?
				and jns_trans NOT IN (4,3)
				UNION ALL
				SELECT NO_BUKTI no_sts, TGL_BUKTI tgl_sts, KD_SKPD, KET keterangan, (CASE WHEN thnlalu='1' THEN 'y' ELSE 'n' END) sumber,
				'' kd_sub_kegiatan, '' jns_trans,'' jns_cp ,nilai as total
				FROM TRHOUTLAIN
				WHERE KD_SKPD=? AND TGL_BUKTI=?
				AND jns_beban<>7
				AND NO_BUKTI NOT IN (select no_sts from trhkasin_ppkd where  sumber='y')", [$kd_skpd, $kd_skpd, $kd_skpd, $tgl_kas, $kd_skpd, $kd_skpd, $kd_skpd, $tgl_kas, $kd_skpd, $tgl_kas]);
        } else {
            $data = DB::select("SELECT no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total
				FROM trhkasin_pkd WHERE no_sts+kd_skpd+jns_trans NOT IN(SELECT a.no_sts+kd_skpd+jns_trans FROM trhkasin_ppkd a where kd_skpd=? and a.jns_trans=4 ) AND no_sts+kd_skpd+jns_trans NOT IN(SELECT a.no_sts+kd_skpd_sumber+jns_trans FROM trhkasin_ppkd a where kd_skpd_sumber=? and a.jns_trans=4 )
                and kd_skpd=?
				and tgl_sts=?
				and jns_trans=4 --AND status=1
				UNION ALL
				SELECT no_sts, tgl_sts,kd_skpd, keterangan,sumber,kd_sub_kegiatan,jns_trans,jns_cp,total
				FROM trhkasin_pkd WHERE no_sts+kd_skpd+jns_trans NOT IN(SELECT a.no_sts+kd_skpd+jns_trans FROM trhkasin_ppkd a where kd_skpd=? and a.jns_trans NOT IN (4,3))
                and kd_skpd=?
				and tgl_sts=?
				and jns_trans NOT IN (4,3)
				UNION ALL
				SELECT NO_BUKTI no_sts, TGL_BUKTI tgl_sts, KD_SKPD, KET keterangan, (CASE WHEN thnlalu='1' THEN 'y' ELSE 'n' END) sumber,
				'' kd_sub_kegiatan, '' jns_trans,'' jns_cp ,nilai as total
				FROM TRHOUTLAIN
				WHERE KD_SKPD=? AND TGL_BUKTI=?
				AND jns_beban<>7
				AND NO_BUKTI NOT IN (select no_sts from trhkasin_ppkd where  sumber='y')", [$kd_skpd, $kd_skpd, $kd_skpd, $tgl_kas, $kd_skpd, $kd_skpd, $tgl_kas, $kd_skpd, $tgl_kas]);
        }

        return response()->json($data);
    }

    public function detailPenerimaanKas(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = $request->kd_skpd;
        $jenis = $request->jenis;

        // $data1 = DB::table('trdkasin_pkd as a')
        //     ->join('trhkasin_pkd as b', function ($join) {
        //         $join->on('a.no_sts', '=', 'b.no_sts');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //         $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
        //     })
        //     ->leftJoin('ms_pengirim as c', function ($join) {
        //         $join->on('a.sumber', '=', 'c.kd_pengirim');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->leftJoin('ms_rek5 as d', function ($join) {
        //         $join->on(DB::raw("LEFT(a.kd_rek6,8)"), '=', 'd.kd_rek5');
        //     })
        //     ->selectRaw("a.*, (select nm_rek6 from ms_rek6 where kd_rek6 = a.kd_rek6) as nm_rek, c.nm_pengirim, d.nm_rek5")
        //     ->where(['a.no_sts' => $no_bukti, 'a.kd_skpd' => $kd_skpd, 'b.jns_trans' => $jenis]);

        // $data2 = DB::table('TRHOUTLAIN')
        //     ->selectRaw("KD_SKPD, NO_BUKTI no_sts, '' kd_rek6, nilai as rupiah, '' kd_sub_kegiatan, '' no_terima,  (CASE WHEN thnlalu='1' THEN 'y' ELSE 'n' END) sumber,''kanal,
        // '' nm_rek, '' nm_pengirim, '' nm_rek5")
        //     ->whereRaw("KD_SKPD=? AND jns_beban<>? AND NO_BUKTI=?", [$kd_skpd, '7', $no_bukti])
        //     ->unionAll($data1);

        // $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
        //     ->mergeBindings($data2)
        //     ->orderBy('no_sts')
        //     ->get();

        $data = DB::select("SELECT * FROM (
            SELECT
                a.no_sts AS no_sts,
                a.kd_skpd AS kd_skpd,
                a.kd_rek6 AS kd_rek6,
                ( SELECT nm_rek6 FROM ms_rek6 WHERE kd_rek6 = a.kd_rek6 ) AS nm_rek,
                a.sumber AS sumber,
                '' AS nm_pengirim,
                a.rupiah AS rupiah
            FROM
                trdkasin_pkd a
                LEFT JOIN ms_pengirim b ON a.sumber= b.kd_pengirim
                UNION ALL
            SELECT
                a.no_bukti AS no_sts,
                a.kd_skpd AS kd_skpd,
                '' AS kd_rek6,
                '' AS nm_rek,
                '' AS sumber,
                '' AS nm_pengirim,
                a.nilai AS rupiah
            FROM
                TRHOUTLAIN a ) a
            WHERE
            a.no_sts = ?
            AND a.kd_skpd= ?", [$no_bukti, $kd_skpd]);

        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function kegiatanPenerimaanKas(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $data = DB::table('ms_sub_kegiatan')->select('nm_sub_kegiatan')->where(['kd_sub_kegiatan' => $kd_sub_kegiatan])->first();
        return response()->json($data->nm_sub_kegiatan);
    }

    public function simpanPenerimaanKas(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        $nomor = DB::table('trhkasin_ppkd')
            ->select(DB::raw("CASE WHEN MAX(urut+1) is null THEN 1 else MAX(urut+1) END as nomor"))
            ->first();

        $no_bukti = $nomor->nomor;

        if (Str::length($no_bukti) == '1') {
            $nomor = "00000" . $no_bukti;
        } elseif (Str::length($no_bukti) == '2') {
            $nomor = "0000" . $no_bukti;
        } elseif (Str::length($no_bukti) == '3') {
            $nomor = "000" . $no_bukti;
        } elseif (Str::length($no_bukti) == '4') {
            $nomor = "00" . $no_bukti;
        } elseif (Str::length($no_bukti) == '5') {
            $nomor = "0" . $no_bukti;
        } elseif (Str::length($no_bukti) == '6') {
            $nomor = $no_bukti;
        }

        $nomorKas = "PB" . $nomor;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhkasin_ppkd')
                ->where([
                    'no_kas' => $nomorKas,
                    'kd_skpd' => $data['kd_skpd']
                ])
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            if ($data['jenis'] == 4 && $data['jns_cp'] == 2) {
                $skpd = '5.02.0.00.0.00.02.0000';
                $giat = '5.02.00.0.00.0004';
            } else {
                $skpd = $data['kd_skpd'];
                $giat = $data['kd_sub_kegiatan'];
            }
            $kas  = $nomorKas;

            DB::table('trhkasin_ppkd')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_kas' => $nomorKas])
                ->delete();

            DB::table('trhkasin_ppkd')
                ->insert([
                    'no_kas' => $nomorKas,
                    'tgl_kas' => $data['tgl_kas'],
                    'no_sts' => $data['no_bukti'],
                    'tgl_sts' => $data['tgl_bukti'],
                    'kd_skpd' => $skpd,
                    'keterangan' => $data['keterangan'],
                    'total' => $data['total'],
                    'kd_sub_kegiatan' => $giat,
                    'jns_trans' => $data['jenis'],
                    'sumber' => '',
                    'rek_bank' => $data['rek_bank'],
                    'kd_bank' => '',
                    'urut' => $no_bukti,
                    'username' => Auth::user()->nama
                ]);

            // DB::table('trhkasin_pkd')
            // ->where(['no_sts' => $data['no_bukti'], 'kd_skpd' => $skpd, 'kd_sub_kegiatan' => $giat, 'jns_trans' => $data['jenis']])
            // ->update([
            //     'no_cek' => '1',
            //     'status' => '1'
            // ]);

            DB::table('trhkasin_pkd')->where(['no_sts' => $data['no_bukti'], 'kd_skpd' => $data['kd_skpd']])->update(['no_cek' => '1', 'status' => '1']);
            DB::table('TRHOUTLAIN')
                ->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $data['kd_skpd']])
                ->update([
                    'status' => '1',
                ]);

            if ($data['sumber'] == 'y') {
                DB::table('trhkasin_ppkd')
                    ->where(['no_kas' => $nomorKas, 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'kd_skpd' => $data['kd_skpd'],
                        'kd_sub_kegiatan' => DB::raw("LEFT('$kd_skpd',4)+'.00.0.00.04'")
                    ]);
            }

            DB::table('trdkasin_ppkd')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_kas' => $nomorKas])
                ->delete();

            if (isset($data['detail_sts'])) {
                DB::table('trdkasin_ppkd')->insert(array_map(function ($value) use ($data, $kas, $skpd, $giat) {
                    return [
                        'kd_skpd' => $skpd,
                        'no_sts' => $value['no_sts'],
                        'kd_rek6' => $value['kd_rek6'],
                        'rupiah' => $value['rupiah'],
                        'no_kas' => $kas,
                        'kd_sub_kegiatan' => $giat,
                        'sumber' => $value['sumber'],
                    ];
                }, $data['detail_sts']));
            }

            if ($data['sumber'] == 'n') {
                DB::table('trdkasin_ppkd')
                    ->where(['no_kas' => $nomorKas, 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'kd_rek6' => '1110301',
                    ]);
            }

            if ($data['sumber'] == 'y') {
                DB::table('trdkasin_ppkd')
                    ->where(['no_kas' => $nomorKas, 'kd_skpd' => $data['kd_skpd']])
                    ->update([
                        'kd_rek6' => '410409010001',
                        'kd_skpd' => $skpd,
                        'kd_sub_kegiatan' => DB::raw("LEFT('$kd_skpd',4)+'.00.0.00.04'"),
                    ]);
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'nomor' => $nomorKas
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
            ]);
        }
    }

    public function batalPenerimaanKas(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {

            DB::table('trhkasin_pkd')->where(['no_sts' => $data['no_bukti'], 'kd_skpd' => $data['kd_skpd']])->update(['no_cek' => '0', 'status' => '0']);

            DB::table('trhkasin_ppkd')->where(['no_kas' => $data['no_kas'], 'kd_skpd' => $data['kd_skpd']])->delete();

            DB::table('TRHOUTLAIN')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $data['kd_skpd']])->update(['status' => '0']);

            DB::table('trdkasin_ppkd')->where(['no_sts' => $data['no_bukti'], 'kd_skpd' => $data['kd_skpd']])->delete();

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

    public function editPenerimaanKas($no_sts, $kd_skpd)
    {
        $no_sts = Crypt::decrypt($no_sts);
        $skpd = Crypt::decrypt($kd_skpd);
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'terima' => collect(DB::select("SELECT
            c.kd_sub_kegiatan AS kd_sub_kegiatan,
            (SELECT nm_sub_kegiatan from ms_sub_kegiatan where kd_sub_kegiatan = c.kd_sub_kegiatan) AS nm_sub_kegiatan,
            a.status AS status,
            a.no_sts AS no_sts,
            a.kd_skpd AS kd_skpd,
            a.tgl_sts AS tgl_sts,
            a.total AS total,
            a.keterangan AS keterangan,
            b.no_kas AS no_kas,
            b.tgl_kas AS tgl_kas,
            a.jns_cp as jns_cp,
	        a.jns_trans as jns_trans,
            b.sumber as sumber,
            ( SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd ) AS nm_skpd,
            rtrim( a.jns_trans ) AS nm_trans,
            (
            SELECT
                rtrim( rek_bank ) AS rek_bank
            FROM
                trhkasin_ppkd
            WHERE
                no_sts = a.no_sts
                AND kd_skpd = a.kd_skpd
                AND b.kd_sub_kegiatan = a.kd_sub_kegiatan
            ) AS rek_bank1
        FROM
            trhkasin_pkd a
            LEFT JOIN trhkasin_ppkd b ON b.kd_skpd= a.kd_skpd
            AND b.no_sts= a.no_sts
            AND b.kd_skpd = a.kd_skpd
            INNER JOIN trdkasin_pkd c on a.kd_skpd = c.kd_skpd
        WHERE a.no_sts = ?  and a.kd_skpd = ?
            UNION ALL


        SELECT
            '' AS kd_sub_kegiatan,
            '' AS nm_sub_kegiatan,
            a.status AS status,
            a.no_bukti AS no_sts,
            a.kd_skpd ,
            a.tgl_bukti AS tgl_sts,
            a.nilai AS total,
            a.KET AS keterangan,
            b.no_kas AS no_kas,
            b.tgl_kas AS tgl_kas,
            '' as jns_cp,
	        '' as jns_trans,
            '' as sumber,
            ( SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd ) AS nm_skpd,
            rtrim( a.jns_beban ) AS nm_trans,
            ( SELECT rtrim( rek_bank ) AS rek_bank FROM trhkasin_ppkd WHERE no_sts = b.no_sts AND kd_skpd = b.kd_skpd ) AS rek_bank1
        FROM
            trhoutlain a
            LEFT JOIN trhkasin_ppkd b ON b.kd_skpd= a.kd_skpd
            AND a.KET = b.keterangan
            AND a.no_bukti= b.no_sts
            WHERE a.no_bukti = ? and a.kd_skpd = ?", [$no_sts, $skpd, $no_sts, $skpd]))->first(),
        ];
        // dd($data['terima']);
        return view('skpd.penerimaan_kas.edit')->with($data);
    }

    public function kunciPenerimaanKas(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $data = DB::table('tr_kunci')->select('tgl_kunci')->where(['kd_skpd' => $kd_skpd])->first();
        return response()->json($data->tgl_kunci);
    }

    public function simpanEditPenerimaanKas(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhkasin_pkd')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd, 'jns_trans' => '4'])
                ->update([
                    'tgl_sts' => $data['tgl_kas'],
                    'keterangan' => $data['keterangan'],
                    'total' => $data['nilai'],
                    'tgl_kas' => $data['tgl_kas'],
                    'sumber' => $data['pengirim'],
                ]);

            DB::table('trdkasin_pkd')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd])
                ->whereRaw("LEFT(kd_rek6,1)=?", ['4'])
                ->update([
                    'kd_rek6' => $data['jenis'],
                    'rupiah' => $data['nilai'],
                    'sumber' => $data['pengirim'],
                ]);

            DB::table('trhkasin_ppkd')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd, 'jns_trans' => '4'])
                ->update([
                    'tgl_sts' => $data['tgl_kas'],
                    'tgl_kas' => $data['tgl_kas'],
                    'keterangan' => $data['keterangan'],
                    'total' => $data['nilai'],
                    'sumber' => $data['pengirim'],
                ]);

            DB::table('trdkasin_ppkd')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd])
                ->whereRaw("LEFT(kd_rek6,1)=?", ['4'])
                ->update([
                    'kd_rek6' => $data['jenis'],
                    'rupiah' => $data['nilai'],
                    'sumber' => $data['pengirim'],
                ]);

            DB::commit();
            return response()->json([
                'message' => '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
            ]);
        }
    }

    public function hapusPenerimaanKas(Request $request)
    {
        $no_kas = $request->no_kas;
        $no_sts = $request->no_sts;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhkasin_pkd')
                ->where(['no_sts' => $no_sts, 'kd_skpd' => $kd_skpd])
                ->update([
                    'no_cek' => '0'
                ]);

            DB::table('trhkasin_ppkd')
                ->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::table('trdkasin_ppkd')
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

    public function cetakPenerimaanKas(Request $request)
    {
        $no_sts = $request->no_sts;

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'no_sts' => $no_sts,
            'data' => DB::table('trhkasin_pkd as a')
                ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) AS nm_skpd,
                (SELECT nama FROM ms_bank WHERE kode = a.kd_bank) AS nm_bank")
                ->where(['a.no_sts' => $no_sts])
                ->first(),
            'detail' => DB::table('trdkasin_pkd as a')
                ->selectRaw("a.*,(SELECT nm_rek6 FROM ms_rek6 WHERE kd_rek6 = a.kd_rek6) AS nm_rek6")
                ->where(['no_sts' => $no_sts])
                ->get()
        ];

        return view('skpd.penerimaan_kas.cetak')->with($data);
    }

    // Koreksi Pendapatan
    public function indexKoreksi()
    {
        return view('skpd.koreksi_pendapatan.index');
    }

    public function loadDataKoreksi()
    {
        $data = DB::table('trkasout_ppkd')
            ->orderByRaw("cast(no as int)")
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("koreksi_pendapatan.edit", Crypt::encrypt($row->no)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahKoreksi()
    {
        $data = [
            'daftar_skpd' => DB::table('ms_skpd as a')
                ->orderBy('kd_skpd')
                ->get(),
        ];

        return view('skpd.koreksi_pendapatan.create')->with($data);
    }

    public function jenisKoreksi(Request $request)
    {
        $kd_skpd = $request->kd_skpd;

        if ($kd_skpd == '1.02.0.00.0.00.02.0000') {
            $data1 = DB::table('trdrka as a')
                ->selectRaw(" a.kd_skpd, a.nm_skpd, a.kd_rek6, a.nm_rek6")
                ->where(['a.kd_skpd' => $kd_skpd])
                ->groupByRaw("a.kd_skpd,a.nm_skpd, a.kd_rek6, a.nm_rek6");
            $data2 = DB::query()
                ->select(DB::raw("'1.02.0.00.0.00.02.0000' as kd_skpd"), DB::raw("'RUMAH SAKIT UMUM DAERAH DR. SOEDARSO' as nm_skpd"), DB::raw("'210601010009' as kd_rek6"), DB::raw("'Utang Belanja Tunjangan Fungsional UmumASN-Tunjangan Fungsional Umum PNS' as nm_rek6"))
                ->unionAll($data1);
            $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
                ->mergeBindings($data2)
                ->get();
        } else {
            $data = DB::table('trdrka as a')
                ->selectRaw("a.kd_skpd, a.nm_skpd, a.kd_rek6, a.nm_rek6")
                ->where(['a.kd_skpd' => $kd_skpd])
                ->groupByRaw("a.kd_skpd,a.nm_skpd, a.kd_rek6, a.nm_rek6")
                ->get();
        }
        return response()->json($data);
    }

    public function simpanKoreksi(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = nomor_urut_ppkd();

            $cek_terima = DB::table('trkasout_ppkd')->where(['no' => $no_urut, 'kd_skpd' => $kd_skpd])->count();
            if ($cek_terima > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trkasout_ppkd')
                ->insert([
                    'no' => $no_urut,
                    'tanggal' => $data['tgl_kas'],
                    'keterangan' => $data['keterangan'],
                    'nilai' => $data['total'],
                    'kd_rek' => $data['jenis'],
                    'nm_rek' => $data['nama_jenis'],
                    'kd_skpd' => $data['kd_skpd'],
                    'nm_skpd' => $data['nm_skpd'],
                    'status' => $data['ngaruh'],
                    'kd_sub_kegiatan' => DB::raw("left('$kd_skpd',4)+'.00.0.00.04'")
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

    public function editKoreksi($no)
    {
        $no = Crypt::decrypt($no);

        $data = [
            'daftar_skpd' => DB::table('ms_skpd as a')
                ->orderBy('kd_skpd')
                ->get(),
            'koreksi' => DB::table('trkasout_ppkd')
                ->where(['no' => $no])
                ->first()
        ];

        return view('skpd.koreksi_pendapatan.edit')->with($data);
    }

    public function simpanEditKoreksi(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {

            DB::table('trkasout_ppkd')
                ->where(['no' => $data['no_kas']])
                ->update([
                    'tanggal' => $data['tgl_kas'],
                    'keterangan' => $data['keterangan'],
                    'nilai' => $data['total'],
                    'kd_rek' => $data['jenis'],
                    'nm_rek' => $data['nama_jenis'],
                    'kd_skpd' => $data['kd_skpd'],
                    'nm_skpd' => $data['nm_skpd'],
                    'status' => $data['ngaruh'],
                    'kd_sub_kegiatan' => DB::raw("left('$kd_skpd',4)+'.00.0.00.04'")
                ]);

            DB::commit();
            return response()->json([
                'message' => '1',
                'nomor' => $data['no_kas']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapusKoreksi(Request $request)
    {
        $no = $request->no;

        DB::beginTransaction();
        try {
            DB::table('trkasout_ppkd')->where(['no' => $no])->delete();

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

    // Penerimaan Non Pendapatan
    public function indexPenerimaanNonPendapatan()
    {
        return view('skpd.penerimaan_non_pendapatan.index');
    }

    public function loadDataPenerimaanNonPendapatan()
    {
        $data = DB::table('penerimaan_non_sp2d')
            ->orderByRaw("nomor")
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("non_pendapatan.edit", Crypt::encrypt($row->nomor)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->nomor . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahPenerimaanNonPendapatan()
    {
        return view('skpd.penerimaan_non_pendapatan.create');
    }

    public function simpanPenerimaanNonPendapatan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = nomor_urut_ppkd();

            DB::table('penerimaan_non_sp2d')
                ->insert([
                    'nomor' => $no_urut,
                    'tanggal' => $data['tgl_kas'],
                    'keterangan' => $data['keterangan'],
                    'nilai' => $data['nilai'],
                    'jenis' => $data['jenis'],
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

    public function editPenerimaanNonPendapatan($nomor)
    {
        $nomor = Crypt::decrypt($nomor);

        $data = [
            'terima' => DB::table('penerimaan_non_sp2d')
                ->where(['nomor' => $nomor])
                ->first()
        ];

        return view('skpd.penerimaan_non_pendapatan.edit')->with($data);
    }

    public function simpanEditPenerimaanNonPendapatan(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {
            DB::table('penerimaan_non_sp2d')
                ->where(['nomor' => $data['no_kas']])
                ->update([
                    'tanggal' => $data['tgl_kas'],
                    'keterangan' => $data['keterangan'],
                    'nilai' => $data['nilai'],
                    'jenis' => $data['jenis'],
                ]);

            DB::commit();
            return response()->json([
                'message' => '1',
                'nomor' => $data['no_kas']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapusPenerimaanNonPendapatan(Request $request)
    {
        $nomor = $request->nomor;

        DB::beginTransaction();
        try {
            DB::table('penerimaan_non_sp2d')->where(['nomor' => $nomor])->delete();

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
