<?php

namespace App\Http\Controllers\BUD;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class PengesahanController extends Controller
{
    public function indexPengesahanLpjUp()
    {
        $data = [
            'ttd1' => DB::select("SELECT nip,nama,jabatan FROM ms_ttd where kode='BUD' group by  nip,nama,jabatan")
        ];

        return view('bud.pengesahan_lpj_up.index')->with($data);
    }

    public function loadPengesahanLpjUp()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $role           = Auth::user()->role;
        $id_pengguna    = Auth::user()->id;

        $data = DB::table('trhlpj as a')
            ->selectRaw("a.*, (SELECT b.nm_skpd FROM ms_skpd b WHERE a.kd_skpd=b.kd_skpd) as nm_skpd")
            ->where(function ($query) use ($role, $id_pengguna) {
                if ($role == '1012' || $role == '1017') {
                    $query->whereRaw("a.kd_skpd IN (SELECT kd_skpd FROM pengguna_skpd where id=?)", [$id_pengguna]);
                }
            })
            ->where('jenis', '1')
            ->orderBy('a.tgl_lpj')
            ->orderBy('a.no_lpj')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("pengesahan_lpj_upgu.edit", ['no_lpj' => Crypt::encrypt($row->no_lpj), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-primary btn-sm"  style="margin-right:4px"><i class="uil-eye"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_lpj . '\',\'' . $row->kd_skpd . '\');" class="btn btn-dark btn-sm" style="margin-right:4px"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function editPengesahanLpjUp($no_lpj, $kd_skpd)
    {
        $no_lpj = Crypt::decrypt($no_lpj);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $data = [
            'lpj' => DB::table('trhlpj as a')
                ->selectRaw("a.*, (SELECT b.nm_skpd FROM ms_skpd b WHERE a.kd_skpd=b.kd_skpd) as nm_skpd")
                ->where(['jenis' => '1', 'no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->first()
        ];

        return view('bud.pengesahan_lpj_up.edit')->with($data);
    }

    public function detailPengesahanLpjUp(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        $data = DB::table('trlpj as a')
            ->join('trhlpj as b', function ($join) {
                $join->on('a.no_lpj', '=', 'b.no_lpj');
                $join->on('a.kd_bp_skpd', '=', 'b.kd_skpd');
            })
            ->select('a.*')
            ->where(['b.no_lpj' => $no_lpj, 'b.kd_skpd' => $kd_skpd, 'b.jenis' => '1'])
            ->get();

        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function kegiatanPengesahanLpjUp(Request $request)
    {
        $no_lpj = $request->no_lpj;

        $data = DB::select("SELECT a.kd_sub_kegiatan, c.nm_sub_kegiatan
		from trlpj a
		INNER JOIN trhlpj b ON a.no_lpj=b.no_lpj AND a.kd_skpd=b.kd_skpd
		LEFT JOIN trskpd c ON a.kd_sub_kegiatan=c.kd_sub_kegiatan AND a.kd_skpd=c.kd_skpd
		WHERE a.no_lpj = ?
		GROUP BY a.kd_sub_kegiatan,c.nm_sub_kegiatan
		ORDER BY a.kd_sub_kegiatan", [$no_lpj]);

        return response()->json($data);
    }

    public function setujuPengesahanLpjUp(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhlpj')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->update([
                    'status' => '1'
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

    public function batalSetujuPengesahanLpjUp(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhlpj')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->update([
                    'status' => '0'
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

    public function cetakPengesahanLpjUp(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;
        $ttd = $request->ttd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $pilihan = $request->pilihan;
        $jenis_print = $request->jenis_print;
        $status_anggaran = status_anggaran();

        if ($pilihan == '0') {
            $data_lpj = DB::select("SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,7) as kode, b.nm_program as uraian, SUM(a.nilai) as nilai
                                FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_program,nm_program,kd_skpd FROM trskpd  GROUP BY kd_program,nm_program,kd_skpd)b
                                ON LEFT(a.kd_sub_kegiatan,7) =b.kd_program AND a.kd_skpd=b.kd_skpd
                                WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                                AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                                AND jns_spp IN ('1','2','3'))
                                GROUP BY LEFT(a.kd_sub_kegiatan,7), b.nm_program
                                UNION ALL

                                SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,12) as kode, b.nm_kegiatan as uraian, SUM(a.nilai) as nilai
                                FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_kegiatan,nm_kegiatan,kd_skpd FROM trskpd  GROUP BY kd_kegiatan,nm_kegiatan,kd_skpd)b
                                ON LEFT(a.kd_sub_kegiatan,12) =b.kd_kegiatan AND a.kd_skpd=b.kd_skpd
                                WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                                AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                                AND jns_spp IN ('1','2','3'))
                                GROUP BY LEFT(a.kd_sub_kegiatan,12), b.nm_kegiatan

                                UNION ALL
                                SELECT 2 as urut, a.kd_sub_kegiatan as kode, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                                FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                                WHERE no_lpj=? AND a.kd_bp_skpd=?
                                AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                                --AND (panjar NOT IN ('3') or panjar IS NULL)
                                AND jns_spp IN ('1','2','3'))
                                GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan
                                UNION ALL
                                SELECT 3 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,2) as kode, b.nm_rek2 as uraian, SUM(nilai) as nilai FROM trlpj a
                                INNER JOIN ms_rek2 b ON LEFT(a.kd_rek6,2)=b.kd_rek2
                                WHERE no_lpj=? AND a.kd_bp_skpd=?
                                AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                                --AND (panjar NOT IN ('3') or panjar IS NULL)
                                AND jns_spp IN ('1','2','3'))
                                GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,2), b.nm_rek2
                                UNION ALL
                                SELECT 4 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,4) as kode, b.nm_rek3 as uraian, SUM(nilai) as nilai FROM trlpj a
                                INNER JOIN ms_rek3 b ON LEFT(a.kd_rek6,4)=b.kd_rek3
                                WHERE no_lpj=? AND a.kd_bp_skpd=?
                                AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                                --AND (panjar NOT IN ('3') or panjar IS NULL)
                                AND jns_spp IN ('1','2','3'))
                                GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,4), b.nm_rek3
                                UNION ALL

                                SELECT 5 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,6) as kode, b.nm_rek4 as uraian, SUM(nilai) as nilai FROM trlpj a
                                INNER JOIN ms_rek4 b ON LEFT(a.kd_rek6,6)=b.kd_rek4
                                WHERE no_lpj=? AND a.kd_bp_skpd=?
                                AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                                --AND (panjar NOT IN ('3') or panjar IS NULL)
                                AND jns_spp IN ('1','2','3'))
                                GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,6), b.nm_rek4
                                UNION ALL

                                SELECT 6 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,8) as kode, b.nm_rek5 as uraian, SUM(nilai) as nilai FROM trlpj a
                                INNER JOIN ms_rek5 b ON LEFT(a.kd_rek6,8)=b.kd_rek5
                                WHERE no_lpj=? AND a.kd_bp_skpd=?
                                AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                                --AND (panjar NOT IN ('3') or panjar IS NULL)
                                AND jns_spp IN ('1','2','3'))
                                GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,8), b.nm_rek5
                                UNION ALL
                                SELECT 7 as urut, kd_sub_kegiatan+'.'+kd_rek6 as kode, nm_rek6 as uraian, SUM(nilai) as nilai FROM trlpj
                                WHERE no_lpj=? AND kd_bp_skpd=?
                                AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                                --AND (panjar NOT IN ('3') or panjar IS NULL)
                                AND jns_spp IN ('1','2','3'))
                                GROUP BY kd_sub_kegiatan, kd_rek6, nm_rek6
                                ORDER BY kode", [$no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd,]);
        } elseif ($pilihan == '1') {
            $data_lpj = DB::select("SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,7) as kode, b.nm_program as uraian, SUM(a.nilai) as nilai
                    FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_program,nm_program,kd_skpd FROM trskpd  GROUP BY kd_program,nm_program,kd_skpd)b
                    ON LEFT(a.kd_sub_kegiatan,7) =b.kd_program AND a.kd_skpd=b.kd_skpd
                    WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                    AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                    AND jns_spp IN ('1','2','3'))
                    GROUP BY LEFT(a.kd_sub_kegiatan,7), b.nm_program
                    UNION ALL

                    SELECT 2 as urut, LEFT(a.kd_sub_kegiatan,12) as kode, b.nm_kegiatan as uraian, SUM(a.nilai) as nilai
                    FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_kegiatan,nm_kegiatan,kd_skpd FROM trskpd  GROUP BY kd_kegiatan,nm_kegiatan,kd_skpd)b
                    ON LEFT(a.kd_sub_kegiatan,12) =b.kd_kegiatan AND a.kd_skpd=b.kd_skpd
                    WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                    AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                    AND jns_spp IN ('1','2','3'))
                    GROUP BY LEFT(a.kd_sub_kegiatan,12), b.nm_kegiatan

                    UNION ALL
                    SELECT 3 as urut, a.kd_sub_kegiatan as kode, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                    FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                    WHERE no_lpj=? AND a.kd_bp_skpd=?
                    AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                    --AND (panjar NOT IN ('3') or panjar IS NULL)
                    AND jns_spp IN ('1','2','3'))
                    GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan
                                ORDER BY kode", [$no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd]);
        } elseif ($pilihan == '2') {
            $data_lpj = DB::select("SELECT 1 as urut, a.kd_sub_kegiatan as kode, a.kd_sub_kegiatan as rek, b.nm_kegiatan as uraian, SUM(a.nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti]
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY a.kd_sub_kegiatan, b.nm_kegiatan
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,2) as kode, LEFT(a.kd_rek6,2) as rek,  nm_rek2 as uraian, SUM(nilai) as nilai,
                        '' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek2 b ON LEFT(a.kd_rek6,2)=b.kd_rek2
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,2), nm_rek2
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,4) as kode, LEFT(a.kd_rek6,4) as rek,  nm_rek3 as uraian, SUM(nilai) as nilai,
                        '' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek3 b ON LEFT(a.kd_rek6,4)=b.kd_rek3
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,4), nm_rek3
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,6) as kode, LEFT(a.kd_rek6,6) as rek,  nm_rek4 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek4 b ON LEFT(a.kd_rek6,6)=b.kd_rek4
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,6), nm_rek4
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,8) as kode, LEFT(a.kd_rek6,8) as rek,  nm_rek5 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek5 b ON LEFT(a.kd_rek6,8)=b.kd_rek5
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,8), nm_rek5
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+kd_rek6 as kode, kd_rek6 as rek,  nm_rek6 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti]
                        FROM trlpj a
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj=? AND a.kd_skpd=?
                        AND kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, kd_rek6, nm_rek6
                        UNION ALL
                        SELECT 3 as urut, a.kd_sub_kegiatan+'.'+a.kd_rek6+'.1' as kode,'' as rek, c.ket+' \\ No BKU: '+a.no_bukti as uraian, sum(a.nilai) as nilai,
                        c.tgl_bukti,a.no_bukti
                        FROM trlpj a
                        INNER JOIN trhlpj b ON a.no_lpj=b.no_lpj AND a.kd_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE a.no_lpj=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY a.kd_sub_kegiatan, a.kd_rek6,nm_rek6,a.no_bukti, ket,tgl_bukti
                        ORDER BY kode,tgl_bukti,no_bukti", [$no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan]);
        }

        if ($pilihan == '2') {
            $kd_sub_kegiatan = $kd_sub_kegiatan;
        } else {
            $kd_sub_kegiatan = '';
        }

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'no_lpj' => $no_lpj,
            'dpa' => DB::table('trhrka')
                ->select('no_dpa', 'tgl_dpa')
                ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => status_anggaran()])
                ->first(),
            'jumlah_belanja' => DB::table('trlpj as a')
                ->join('trhlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_bp_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("sum(nilai) as nilai,b.tgl_lpj")
                ->whereRaw("a.no_lpj=? and b.jenis=? and left(a.kd_bp_skpd,17)=left(?,17)", [$no_lpj, '1', $kd_skpd])
                ->groupBy('b.tgl_lpj')
                ->first(),
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD'])->first(),
            'daerah' => DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first(),
            'lpj' => DB::table('trhlpj')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->first(),
            'pilihan' => $pilihan,
            'data_lpj' => $data_lpj,
            'persediaan' => collect(DB::select("SELECT SUM(a.nilai) AS nilai FROM trdspp a LEFT JOIN trhsp2d b ON b.no_spp=a.no_spp
						  WHERE b.kd_skpd=? AND (b.jns_spp=1)", [$kd_skpd]))->first()->nilai,
            'kegiatan' => $kd_sub_kegiatan
        ];

        $view = view('bud.pengesahan_lpj_up.cetak')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } else {
            return $view;
        }
    }

    // PENGESAHAN LPJ TU
    public function indexPengesahanLpjTu()
    {
        $data = [
            'ttd1' => DB::select("SELECT nip,nama,jabatan FROM ms_ttd where kode='BUD' group by  nip,nama,jabatan")
        ];

        return view('bud.pengesahan_lpj_tu.index')->with($data);
    }

    public function loadPengesahanLpjTu()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $role           = Auth::user()->role;
        $id_pengguna    = Auth::user()->id;

        $data = DB::table('trhlpj_tu as a')
            ->selectRaw("a.*, (SELECT b.nm_skpd FROM ms_skpd b WHERE a.kd_skpd=b.kd_skpd) as nm_skpd")
            ->where(function ($query) use ($role, $id_pengguna) {
                if ($role == '1012' || $role == '1017') {
                    $query->whereRaw("a.kd_skpd IN (SELECT kd_skpd FROM pengguna_skpd where id=?)", [$id_pengguna]);
                }
            })
            ->where('jenis', '3')
            ->orderBy('a.tgl_lpj')
            ->orderBy('a.no_lpj')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("pengesahan_lpj_tu.edit", ['no_lpj' => Crypt::encrypt($row->no_lpj), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-primary btn-sm"  style="margin-right:4px"><i class="uil-eye"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_lpj . '\',\'' . $row->kd_skpd . '\',\'' . $row->no_sp2d . '\');" class="btn btn-dark btn-sm" style="margin-right:4px"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function editPengesahanLpjTu($no_lpj, $kd_skpd)
    {
        $no_lpj = Crypt::decrypt($no_lpj);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $data = [
            'lpj' => DB::table('trhlpj_tu as a')
                ->selectRaw("a.*, (SELECT b.nm_skpd FROM ms_skpd b WHERE a.kd_skpd=b.kd_skpd) as nm_skpd")
                ->where(['jenis' => '3', 'no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->first()
        ];

        return view('bud.pengesahan_lpj_tu.edit')->with($data);
    }

    public function detailPengesahanLpjTu(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        $data = DB::table('trlpj_tu as a')
            ->join('trhlpj_tu as b', function ($join) {
                $join->on('a.no_lpj', '=', 'b.no_lpj');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select('a.*')
            ->where(['b.no_lpj' => $no_lpj, 'b.kd_skpd' => $kd_skpd, 'b.jenis' => '3'])
            ->get();

        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function setujuPengesahanLpjTu(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhlpj_tu')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->update([
                    'status' => '1'
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

    public function batalSetujuPengesahanLpjTu(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhlpj_tu')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->update([
                    'status' => '0'
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

    public function cetakPengesahanLpjTu(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = $request->kd_skpd;
        $tgl_ttd = $request->tgl_ttd;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;
        $status_anggaran = status_anggaran();

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'no_sp2d' => $no_sp2d,
            'no_lpj' => $no_lpj,
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD'])->first(),
            'daerah' => DB::table('sclient')->where('kd_skpd', $kd_skpd)->first(),
            'lpj' => collect(DB::select("SELECT LEFT(c.kd_sub_kegiatan,7) as kd_program,
                (select nm_program from ms_program where LEFT(c.kd_sub_kegiatan,7)=kd_program)as nm_program,
                LEFT(c.kd_sub_kegiatan,12) as kd_kegiatan,
                (select nm_kegiatan from ms_kegiatan where LEFT(c.kd_sub_kegiatan,12)=kd_kegiatan)as nm_kegiatan,
                c.kd_sub_kegiatan,c.nm_sub_kegiatan
                FROM trhspp a INNER JOIN trhsp2d b ON a.no_spp = b.no_spp AND a.kd_skpd=b.kd_skpd
                join trdspp c ON a.no_spp = c.no_spp AND a.kd_skpd=c.kd_skpd
                WHERE no_sp2d = ? group by nm_program,c.kd_sub_kegiatan,
                c.nm_sub_kegiatan,LEFT(c.kd_sub_kegiatan,18)", [$no_sp2d]))->first(),
            'data_lpj' => DB::select("SELECT
                        kd_rek6,nm_rek6,SUM(nilai) as nilai
                        FROM
                            trlpj_tu c
                        LEFT JOIN trhlpj_tu d ON c.no_lpj = d.no_lpj AND c.kd_skpd=d.kd_skpd
                        WHERE
                        c.no_lpj = ? AND d.kd_skpd=?
                        GROUP BY kd_rek6,nm_rek6 order by kd_rek6,nm_rek6", [$no_lpj, $kd_skpd]),
            'persediaan' => collect(DB::select("SELECT SUM(a.nilai) AS nilai FROM trdspp a LEFT JOIN trhsp2d b ON b.no_spp=a.no_spp
                         WHERE b.kd_skpd=? AND b.jns_spp=3 AND  no_sp2d = ?", [$kd_skpd, $no_sp2d]))->first()->nilai
        ];

        $view = view('bud.pengesahan_lpj_tu.cetak')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } else {
            return $view;
        }
    }

    // PENGESAHAN SPM TU
    public function indexPengesahanSpmTu()
    {
        return view('bud.pengesahan_spm_tu.index');
    }

    public function loadPengesahanSpmTu()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $role           = Auth::user()->role;
        $id_pengguna    = Auth::user()->id;

        $data = DB::table('trhspm as a')
            ->join('trhspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.*, b.sts_setuju")
            ->where(function ($query) use ($role, $id_pengguna) {
                if ($role == '1012' || $role == '1017') {
                    $query->whereRaw("a.kd_skpd IN (SELECT kd_skpd FROM pengguna_skpd where id=?)", [$id_pengguna]);
                }
            })
            ->whereRaw("a.jns_spp=? AND (sp2d_batal!=? or sp2d_batal is null)", ['3', '1'])
            ->orderBy('a.no_spm')
            ->orderBy('a.kd_skpd')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("pengesahan_spm_tu.edit", ['no_spm' => Crypt::encrypt($row->no_spm), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-primary btn-sm"  style="margin-right:4px"><i class="uil-eye"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_spm . '\',\'' . $row->kd_skpd . '\');" class="btn btn-dark btn-sm" style="margin-right:4px"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function editPengesahanSpmTu($no_spm, $kd_skpd)
    {
        $no_spm = Crypt::decrypt($no_spm);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $data = [
            'spm' => DB::table('trhspm as a')
                ->join('trhspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("a.no_spm,a.tgl_spm,a.no_spp,a.kd_skpd,a.nm_skpd,a.tgl_spp,a.no_spd,a.bulan,a.keperluan,a.jns_spp,a.bank,a.no_rek,a.status,b.sts_setuju")
                ->whereRaw("a.jns_spp=? AND (sp2d_batal!=? or sp2d_batal is null)", ['3', '1'])
                ->where(['a.no_spm' => $no_spm, 'a.kd_skpd' => $kd_skpd])
                ->first()
        ];
        // dd($data['spm']);
        return view('bud.pengesahan_spm_tu.edit')->with($data);
    }

    public function detailPengesahanSpmTu(Request $request)
    {
        $no_spp = $request->no_spp;
        $kd_skpd = $request->kd_skpd;

        $data = DB::table('trdspp as a')
            ->selectRaw("kd_sub_kegiatan,nm_sub_kegiatan,kd_rek6,nm_rek6,sumber,(select DISTINCT nm_sumberdana from hsumber_dana where kd_sumberdana=sumber)as nmsumber,nilai,no_bukti")
            ->where(['a.no_spp' => $no_spp])
            ->orderBy('no_bukti')
            ->orderBy('kd_sub_kegiatan')
            ->orderBy('kd_rek6')
            ->get();

        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function setujuPengesahanSpmTu(Request $request)
    {
        $no_spp = $request->no_spp;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhspp')
                ->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])
                ->update([
                    'sts_setuju' => '1'
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

    public function batalSetujuPengesahanSpmTu(Request $request)
    {
        $no_spp = $request->no_spp;
        $kd_skpd = $request->kd_skpd;
        $keterangan = $request->keterangan;
        $beban = $request->beban;

        DB::beginTransaction();
        try {
            // DB::table('trhspp')
            //     ->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])
            //     ->update([
            //         'sp2d_batal' => '1',
            //         'ket_batal' => $keterangan,
            //         'user_batal' => Auth::user()->nama,
            //         'user_batal' => Auth::user()->nama,
            //         'tgl_batal' => date('d-m-y H:i:s')
            //     ]);

            // if ($beban == '6') {
            //     $no_tagih = DB::table('trhspp')
            //         ->selectRaw("ltrim(no_tagih) as no_tagih")
            //         ->where(['no_spp' => $no_spp])
            //         ->first();

            //     if (isset($no_tagih)) {
            //         DB::table('trhspp')
            //             ->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])
            //             ->update([
            //                 'no_tagih' => '',
            //                 'kontrak' => '',
            //                 'sts_tagih' => '0',
            //                 'nmrekan' => '',
            //                 'pimpinan' => '',
            //             ]);

            //         DB::table('trhtagih')
            //             ->where(['no_bukti' => $no_tagih->no_tagih])
            //             ->update([
            //                 'sts_tagih' => '0'
            //             ]);
            //     }
            // }

            DB::table('trhspp')
                ->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])
                ->update([
                    'sts_setuju' => '0'
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

    // KENDALI PROTEKSI LPJ
    public function indexKendaliProteksi()
    {
        return view('bud.kendali_proteksi_lpj.index');
    }

    public function loadKendaliProteksi()
    {
        $data = DB::table('tb_kendali_lpj as a')
            ->orderBy('a.kd_skpd')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="javascript:void(0);" onclick="proteksi(\'' . $row->kd_skpd . '\',\'' . $row->nm_skpd . '\',\'' . $row->status . '\');" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function simpanKendaliProteksi(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $nm_skpd = $request->nm_skpd;
        $status = $request->status;

        DB::beginTransaction();
        try {
            DB::table('tb_kendali_lpj')
                ->where(['kd_skpd' => $kd_skpd])
                ->update([
                    'status' => $status
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
