<?php

namespace App\Http\Controllers\Skpd\Panjar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TambahPanjarController extends Controller
{
    public function index()
    {
        return view('skpd.tambah_panjar.index');
    }

    public function load()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('tr_panjar')
            ->where(['kd_skpd' => $kd_skpd, 'jns' => '2'])
            ->orderBy('no_panjar')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("tambahpanjar.edit", ['no_panjar' => Crypt::encrypt($row->no_panjar), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_kas . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambah()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $status_anggaran = status_anggaran();

        $data = [
            'daftar_kegiatan' => DB::select("SELECT a.kd_sub_kegiatan,a.nm_sub_kegiatan, (SELECT SUM(nilai) FROM trdrka WHERE kd_sub_kegiatan = a.kd_sub_kegiatan AND kd_skpd=? and jns_ang=?) AS anggaran,
                (select  nilai=trans-kembali_pjr from (
                    SELECT SUM(nilai) [trans],
                    (select isnull(sum(i.nilai),0) from tr_jpanjar i join tr_panjar j on i.no_panjar_lalu=j.no_panjar and i.kd_skpd=j.kd_skpd where
                    j.kd_sub_kegiatan=a.kd_sub_kegiatan and i.jns='2') [kembali_pjr]
                    FROM
						(SELECT
							isnull(SUM(c.nilai),0) as nilai
						FROM
							trdtransout c
						LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
						AND c.kd_skpd = d.kd_skpd
						WHERE
							c.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND d.kd_skpd = a.kd_skpd
						----------------------------
						AND d.jns_spp in ('1','3')
						----------------------------
						UNION ALL
						SELECT isnull(SUM(x.nilai),0) as nilai FROM trdspp x
						INNER JOIN trhspp y
						ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
						WHERE
							x.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND x.kd_skpd =a.kd_skpd
						-------------------------
						AND y.jns_spp IN ('4','5','6')
						------------------------
						AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
						UNION ALL
						SELECT isnull(SUM(nilai),0) as nilai FROM trdtagih t
						INNER JOIN trhtagih u
						ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
						WHERE
						t.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND u.kd_skpd = a.kd_skpd
						AND u.no_bukti
						NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=a.kd_skpd )
						------------------------
						union all
						select isnull(sum(f.rupiah),0) [nilai] from trhkasin_pkd e join trdkasin_pkd f
						on e.no_sts=f.no_sts and e.kd_skpd=f.kd_skpd where e.no_sp2d like '%TU/BL%'
						and f.kd_sub_kegiatan=a.kd_sub_kegiatan and e.jns_cp='3' group by f.kd_sub_kegiatan
						union all
						select isnull(sum(nilai),0) [nilai] from tr_panjar where kd_sub_kegiatan=a.kd_sub_kegiatan
						and no_panjar not in
							(select h.no_panjar FROM trdtransout g
						      JOIN trhtransout h ON g.no_bukti = h.no_bukti AND g.kd_skpd = h.kd_skpd
							  WHERE g.kd_sub_kegiatan = a.kd_sub_kegiatan AND g.kd_skpd = a.kd_skpd
							  AND h.jns_spp in ('1','3') and h.panjar='1') 	and jns='1'
						)r
                    ) z) as transaksi
				FROM trskpd a where a.kd_skpd=? AND a.status_sub_kegiatan='1' and a.jns_ang=? order by a.kd_sub_kegiatan", [$kd_skpd, $status_anggaran, $kd_skpd, $status_anggaran]),
            'no_urut' => no_urut($kd_skpd),
            'sisa_tunai' => load_sisa_tunai(),
            'sisa_bank' => sisa_bank_panjar(),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'daftar_panjar' => DB::table('tr_panjar')
                ->where(['status' => '1', 'jns' => '1', 'kd_skpd' => $kd_skpd])
                ->orderBy('no_panjar')
                ->get()
        ];

        return view('skpd.tambah_panjar.create')->with($data);
    }

    public function subKegiatan(Request $request)
    {
        $no_panjar = $request->no_panjar;
        $kd_skpd = Auth::user()->kd_skpd;
        $status_anggaran = status_anggaran();

        $data = DB::select("SELECT a.kd_sub_kegiatan,e.nm_sub_kegiatan, (SELECT SUM(nilai) FROM trdrka WHERE kd_sub_kegiatan = a.kd_sub_kegiatan AND kd_skpd=? and jns_ang=?) AS anggaran, (SELECT SUM(c.nilai) FROM trdtransout c INNER JOIN trhtransout d ON c.no_bukti=d.no_bukti AND c.kd_skpd=d.kd_skpd WHERE c.kd_sub_kegiatan = a.kd_sub_kegiatan AND d.kd_skpd=a.kd_skpd) as transaksi FROM tr_panjar a INNER JOIN trskpd e ON a.kd_sub_kegiatan = e.kd_sub_kegiatan AND a.kd_skpd=e.kd_skpd where a.kd_skpd=? AND a.no_panjar = ? and e.jns_ang=? order by a.kd_sub_kegiatan", [$kd_skpd, $status_anggaran, $kd_skpd, $no_panjar, $status_anggaran]);

        return response()->json($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek_panjar = DB::table('tr_panjar')->where(['no_panjar' => $data['no_panjar'], 'kd_skpd' => $kd_skpd])->count();
            if ($cek_panjar > 0) {
                return response()->json([
                    'message' => '4'
                ]);
            }

            DB::table('tr_panjar')
                ->insert([
                    'no_kas' => $data['no_panjar'],
                    'tgl_kas' => $data['tgl_panjar'],
                    'no_panjar' => $data['no_panjar'],
                    'tgl_panjar' => $data['tgl_panjar'],
                    'kd_skpd' => $data['kd_skpd'],
                    'pengguna' => Auth::user()->nama,
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'pay' => $data['pembayaran'],
                    'rek_bank' => '',
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'status' => '0',
                    'jns' => '2',
                    'no_panjar_lalu' => $data['no_panjar_lalu'],
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

        $status_anggaran = status_anggaran();

        $data = [
            'daftar_kegiatan' => DB::select("SELECT a.kd_sub_kegiatan,a.nm_sub_kegiatan, (SELECT SUM(nilai) FROM trdrka WHERE kd_sub_kegiatan = a.kd_sub_kegiatan AND kd_skpd=? and jns_ang=?) AS anggaran,
                (select  nilai=trans-kembali_pjr from (
                    SELECT SUM(nilai) [trans],
                    (select isnull(sum(i.nilai),0) from tr_jpanjar i join tr_panjar j on i.no_panjar_lalu=j.no_panjar and i.kd_skpd=j.kd_skpd where
                    j.kd_sub_kegiatan=a.kd_sub_kegiatan and i.jns='2') [kembali_pjr]
                    FROM
						(SELECT
							isnull(SUM(c.nilai),0) as nilai
						FROM
							trdtransout c
						LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
						AND c.kd_skpd = d.kd_skpd
						WHERE
							c.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND d.kd_skpd = a.kd_skpd
						----------------------------
						AND d.jns_spp in ('1','3')
						----------------------------
						UNION ALL
						SELECT isnull(SUM(x.nilai),0) as nilai FROM trdspp x
						INNER JOIN trhspp y
						ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
						WHERE
							x.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND x.kd_skpd =a.kd_skpd
						-------------------------
						AND y.jns_spp IN ('4','5','6')
						------------------------
						AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
						UNION ALL
						SELECT isnull(SUM(nilai),0) as nilai FROM trdtagih t
						INNER JOIN trhtagih u
						ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
						WHERE
						t.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND u.kd_skpd = a.kd_skpd
						AND u.no_bukti
						NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=a.kd_skpd )
						------------------------
						union all
						select isnull(sum(f.rupiah),0) [nilai] from trhkasin_pkd e join trdkasin_pkd f
						on e.no_sts=f.no_sts and e.kd_skpd=f.kd_skpd where e.no_sp2d like '%TU/BL%'
						and f.kd_sub_kegiatan=a.kd_sub_kegiatan and e.jns_cp='3' group by f.kd_sub_kegiatan
						union all
						select isnull(sum(nilai),0) [nilai] from tr_panjar where kd_sub_kegiatan=a.kd_sub_kegiatan
						and no_panjar not in
							(select h.no_panjar FROM trdtransout g
						      JOIN trhtransout h ON g.no_bukti = h.no_bukti AND g.kd_skpd = h.kd_skpd
							  WHERE g.kd_sub_kegiatan = a.kd_sub_kegiatan AND g.kd_skpd = a.kd_skpd
							  AND h.jns_spp in ('1','3') and h.panjar='1') 	and jns='1'
						)r
                    ) z) as transaksi
				FROM trskpd a where a.kd_skpd=? AND a.status_sub_kegiatan='1' and a.jns_ang=? order by a.kd_sub_kegiatan", [$kd_skpd, $status_anggaran, $kd_skpd, $status_anggaran]),
            'no_urut' => no_urut($kd_skpd),
            'sisa_tunai' => load_sisa_tunai(),
            'sisa_bank' => sisa_bank_panjar(),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'panjar' => DB::table('tr_panjar')
                ->where(['no_kas' => $no_panjar, 'kd_skpd' => $kd_skpd])
                ->first(),
            'daftar_panjar' => DB::table('tr_panjar')
                ->where(['status' => '1', 'jns' => '1', 'kd_skpd' => $kd_skpd])
                ->orderBy('no_panjar')
                ->get(),
            'panjar' => DB::table('tr_panjar')
                ->where(['jns' => '2', 'kd_skpd' => $kd_skpd, 'no_kas' => $no_panjar])
                ->first()
        ];

        return view('skpd.tambah_panjar.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek_panjar = DB::table('tr_panjar')->where(['no_panjar' => $data['no_panjar'], 'kd_skpd' => $kd_skpd])->count();
            if ($cek_panjar > 0 && $data['no_panjar'] != $data['no_simpan']) {
                return response()->json([
                    'message' => '4'
                ]);
            }

            DB::table('tr_panjar')
                ->where(['kd_skpd' => $kd_skpd, 'no_panjar' => $data['no_simpan']])
                ->delete();

            DB::table('tr_panjar')
                ->insert([
                    'no_kas' => $data['no_panjar'],
                    'tgl_kas' => $data['tgl_panjar'],
                    'no_panjar' => $data['no_panjar'],
                    'tgl_panjar' => $data['tgl_panjar'],
                    'kd_skpd' => $data['kd_skpd'],
                    'pengguna' => Auth::user()->nama,
                    'nilai' => $data['nilai'],
                    'keterangan' => $data['keterangan'],
                    'pay' => $data['pembayaran'],
                    'rek_bank' => '',
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'status' => '0',
                    'jns' => '2',
                    'no_panjar_lalu' => $data['no_panjar_lalu'],
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
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_panjar')
                ->where(['no_panjar' => $no_kas, 'kd_skpd' => $kd_skpd, 'jns' => '2'])
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
