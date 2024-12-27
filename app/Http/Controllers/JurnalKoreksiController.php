<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use function PHPUnit\Framework\isNull;

class JurnalKoreksiController extends Controller
{
    // KOREKSI TRANSAKSI REKENING
    public function indexRekening()
    {
        return view('skpd.koreksi_rekening.index');
    }

    public function loadDataRekening()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $tgl_terima = DB::table('trhspj_ppkd')
            ->selectRaw("ISNULL(MAX(tgl_terima),'2016-01-01') as tgl_terima")
            ->where(['cek' => '1', 'kd_skpd' => $kd_skpd])
            ->first();
        $tgl_terima = $tgl_terima->tgl_terima;

        $data = DB::table('trhtransout as a')
            ->selectRaw("a.*,'' AS nokas_pot,'' AS tgl_pot,'' AS kete,(SELECT COUNT(*) from trlpj z where z.no_bukti = a.no_bukti and z.kd_skpd = a.kd_skpd) ketlpj,(CASE WHEN a.tgl_bukti<? THEN 1 ELSE 0 END ) ketspj", [$tgl_terima])
            ->where(['a.panjar' => '3', 'a.kd_skpd' => $kd_skpd])
            ->orderBy('a.no_bukti')
            ->orderBy('a.kd_skpd')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("koreksi_rekening.edit", Crypt::encryptString($row->no_bukti)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusRekening(' . $row->no_bukti . ', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahRekening()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_kegiatan' => DB::table('trdtransout as a')
                ->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->where(['a.kd_skpd' => $kd_skpd])
                ->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->distinct()
                ->get(),
            'daftar_kegiatan_koreksi' => DB::table('trskpd as a')
                ->join('ms_sub_kegiatan as b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')
                ->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->where(['a.kd_skpd' => $kd_skpd, 'a.status_sub_kegiatan' => '1', 'b.jns_sub_kegiatan' => '5'])
                ->groupByRaw("a.kd_sub_kegiatan,a.nm_sub_kegiatan")
                ->get()
        ];
        return view('skpd.koreksi_rekening.create')->with($data);
    }

    public function nomorSp2d(Request $request)
    {
        $req = $request->all();

        $data = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select('a.no_sp2d')
            ->distinct()
            ->where(['a.kd_skpd' => $req['kd_skpd'], 'b.jns_spp' => $req['beban'], 'a.kd_sub_kegiatan' => $req['kd_sub_kegiatan']])
            ->orderBy('a.no_sp2d')
            ->get();
        return response()->json($data);
    }

    public function nomorSp2d2(Request $request)
    {
        $req = $request->all();

        $data = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select('a.no_sp2d')
            ->distinct()
            ->where(['a.kd_skpd' => $req['kd_skpd'], 'b.jns_spp' => $req['beban']])
            ->orderBy('a.no_sp2d')
            ->get();
        return response()->json($data);
    }

    public function rekening(Request $request)
    {
        $req = $request->all();

        $data = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.no_bukti, a.kd_rek6, a.nm_rek6,nilai,sumber")
            ->where(['a.kd_skpd' => $req['kd_skpd'], 'b.jns_spp' => $req['beban'], 'a.kd_sub_kegiatan' => $req['kd_sub_kegiatan'], 'b.no_sp2d' => $req['no_sp2d']])
            ->orderBy('a.no_bukti')
            ->get();
        return response()->json($data);
    }

    public function rekeningKoreksi(Request $request)
    {
        $req = $request->all();
        $no_bukti = isNull($req['no_bukti']) ? '' : $req['no_bukti'];
        $jns_ang = status_anggaran();
        if ($req['beban'] == '1') {
            $data = DB::table('trdrka as a')
                ->selectRaw("a.kd_rek6,a.nm_rek6,0 AS sp2d,nilai AS anggaran")
                ->selectRaw("(SELECT SUM(nilai) FROM
        				(SELECT
        					SUM (c.nilai) as nilai
        				FROM
        					trdtransout c
        				LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
        				AND c.kd_skpd = d.kd_skpd
        				WHERE
        					c.kd_sub_kegiatan = a.kd_sub_kegiatan
        				AND d.kd_skpd = a.kd_skpd
        				AND c.kd_rek6 = a.kd_rek6
        				AND c.no_bukti <> ?
        				AND d.jns_spp=?
        				UNION ALL
        				SELECT
        					SUM (c.nilai) as nilai
        				FROM
        					trdtransout_cmsbank c
        				LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher
        				AND c.kd_skpd = d.kd_skpd
        				WHERE
        					c.kd_sub_kegiatan = a.kd_sub_kegiatan
        				AND d.kd_skpd = a.kd_skpd
        				AND c.kd_rek6 = a.kd_rek6
        				AND d.jns_spp=?
        				AND d.status_validasi<>'1'
        				UNION ALL
        				SELECT SUM(x.nilai) as nilai FROM trdspp x
        				INNER JOIN trhspp y
        				ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
        				WHERE
        					x.kd_sub_kegiatan = a.kd_sub_kegiatan
        				AND x.kd_skpd = a.kd_skpd
        				AND x.kd_rek6 = a.kd_rek6
        				AND y.jns_spp IN ('3','4','5','6')
        				AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
        				UNION ALL
        				SELECT SUM(nilai) as nilai FROM trdtagih t
        				INNER JOIN trhtagih u
        				ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
        				WHERE
        				t.kd_sub_kegiatan = a.kd_sub_kegiatan
        				AND u.kd_skpd = a.kd_skpd
        				AND t.kd_rek = a.kd_rek6
        				AND u.no_bukti
        				NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=? )
        				)r) AS lalu", [$no_bukti, $req['beban'], $req['beban'], $req['kd_skpd']])
                ->where(['a.kd_sub_kegiatan' => $req['kd_sub_kegiatan'], 'a.kd_skpd' => $req['kd_skpd'], 'a.jns_ang' => $jns_ang])
                ->get();
        } else {
            $data = DB::table('trhspp as a')
                ->join('trdspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->join('trhspm as c', function ($join) {
                    $join->on('b.no_spp', '=', 'c.no_spp');
                    $join->on('b.kd_skpd', '=', 'c.kd_skpd');
                })
                ->join('trhsp2d as d', function ($join) {
                    $join->on('c.no_spm', '=', 'd.no_spm');
                    $join->on('c.kd_skpd', '=', 'd.kd_skpd');
                })
                ->selectRaw("b.kd_rek6,b.nm_rek6,
                    (SELECT SUM(c.nilai) FROM trdtransout c LEFT JOIN trhtransout d ON c.no_bukti=d.no_bukti AND c.kd_skpd=d.kd_skpd
        			WHERE c.kd_sub_kegiatan = b.kd_sub_kegiatan AND
                    d.kd_skpd=a.kd_skpd
        			AND c.kd_rek6=b.kd_rek6 AND c.no_bukti <> ? AND d.jns_spp = ? and c.no_sp2d = ?
        			) AS lalu,
                    b.nilai AS sp2d,
                    0 AS anggaran", [$req['no_bukti'], $req['beban'], $req['no_sp2d']])
                ->where(['d.no_sp2d' => $req['no_sp2d'], 'b.kd_sub_kegiatan' => $req['kd_sub_kegiatan']])
                ->get();
        }

        $data1 = DB::select("SELECT a.no_bukti, a.kd_rek6, a.nm_rek6,nilai,sumber
                FROM trdtransout a INNER JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
				WHERE a.kd_skpd=? AND  b.no_sp2d =? and a.kd_sub_kegiatan=? AND b.jns_spp = ? ORDER BY a.no_bukti", [$req['kd_skpd'], $req['no_sp2d'], $req['kd_sub_kegiatan'], $req['beban']]);

        return response()->json([
            'rekening_awal' => $data1,
            'rekening_koreksi' => $data,
        ]);
    }

    public function rekeningKoreksi2(Request $request)
    {
        $req = $request->all();
        $no_bukti = isNull($req['no_bukti']) ? '' : $req['no_bukti'];
        $jns_ang = status_anggaran();
        if ($req['beban'] == '1') {
            $data = DB::table('trdrka as a')
                ->selectRaw("a.kd_rek6,a.nm_rek6,0 AS sp2d,nilai AS anggaran")
                ->selectRaw("(SELECT SUM(nilai) FROM
						(SELECT
							SUM (c.nilai) as nilai
						FROM
							trdtransout c
						LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
						AND c.kd_skpd = d.kd_skpd
						WHERE
							c.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND d.kd_skpd = a.kd_skpd
						AND c.kd_rek6 = a.kd_rek6
						AND c.no_bukti <> ?
						AND d.jns_spp=?
						UNION ALL
						SELECT
							SUM (c.nilai) as nilai
						FROM
							trdtransout_cmsbank c
						LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher
						AND c.kd_skpd = d.kd_skpd
						WHERE
							c.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND d.kd_skpd = a.kd_skpd
						AND c.kd_rek6 = a.kd_rek6
						AND d.jns_spp=?
						AND d.status_validasi<>'1'
						UNION ALL
						SELECT SUM(x.nilai) as nilai FROM trdspp x
						INNER JOIN trhspp y
						ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
						WHERE
							x.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND x.kd_skpd = a.kd_skpd
						AND x.kd_rek6 = a.kd_rek6
						AND y.jns_spp IN ('3','4','5','6')
						AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
						UNION ALL
						SELECT SUM(nilai) as nilai FROM trdtagih t
						INNER JOIN trhtagih u
						ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
						WHERE
						t.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND u.kd_skpd = a.kd_skpd
						AND t.kd_rek = a.kd_rek6
						AND u.no_bukti
						NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=? )
						)r) AS lalu", [$no_bukti, $req['beban'], $req['beban'], $req['kd_skpd']])
                ->where(['a.kd_sub_kegiatan' => $req['kd_sub_kegiatan'], 'a.kd_skpd' => $req['kd_skpd'], 'a.jns_ang' => $jns_ang])
                ->get();
        } else {
            $data = DB::table('trhspp as a')
                ->join('trdspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->join('trhspm as c', function ($join) {
                    $join->on('b.no_spp', '=', 'c.no_spp');
                    $join->on('b.kd_skpd', '=', 'c.kd_skpd');
                })
                ->join('trhsp2d as d', function ($join) {
                    $join->on('c.no_spm', '=', 'd.no_spm');
                    $join->on('c.kd_skpd', '=', 'd.kd_skpd');
                })
                ->selectRaw("b.kd_rek6,b.nm_rek6,
                    (SELECT SUM(c.nilai) FROM trdtransout c LEFT JOIN trhtransout d ON c.no_bukti=d.no_bukti AND c.kd_skpd=d.kd_skpd
					WHERE c.kd_sub_kegiatan = b.kd_sub_kegiatan AND
                    d.kd_skpd=a.kd_skpd
					AND c.kd_rek6=b.kd_rek6 AND c.no_bukti <> ? AND d.jns_spp = ? and c.no_sp2d = ?
					) AS lalu,
                    b.nilai AS sp2d,
                    0 AS anggaran", [$req['no_bukti'], $req['beban'], $req['no_sp2d']])
                ->where([ 'b.kd_sub_kegiatan' => $req['kd_sub_kegiatan']])
                ->get();
        }
        return response()->json($data);
    }

    public function sumber(Request $request)
    {
        $req = $request->all();

        $data = DB::select("SELECT * from (
        SELECT sumber,(select nm_sumber_dana1 from sumber_dana where kd_sumber_dana1=b.sumber) as nmsumber,
        		sum(nilai) as nilai FROM trhtransout a INNER JOIN trdtransout b ON
                a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                where b.no_bukti=? and a.no_sp2d=? and  b.kd_skpd=? and b.kd_sub_kegiatan=? and b.kd_rek6=?
                GROUP BY sumber)zz order by  sumber", [$req['no_bukti'], $req['no_sp2d'], $req['kd_skpd'], $req['kd_sub_kegiatan'], $req['kd_rek6']]);

        return response()->json($data);
    }

    public function sumber2(Request $request)
    {
        $req = $request->all();

        $data = DB::select("SELECT * from (
        SELECT sumber,(select nm_sumber_dana1 from sumber_dana where kd_sumber_dana1=b.sumber) as nmsumber,
        		sum(nilai) as nilai FROM trhtransout a INNER JOIN trdtransout b ON
                a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                where b.no_bukti=? and a.no_sp2d=? and  b.kd_skpd=? and b.kd_sub_kegiatan=? and b.kd_rek6=?
                GROUP BY sumber)zz order by  sumber", [$req['no_bukti'], $req['no_sp2d'], $req['kd_skpd'], $req['kd_sub_kegiatan'], $req['kd_rek6']]);

        return response()->json($data);
    }

    public function sumberKoreksi(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_rek6 = $request->kd_rek6;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_bukti = $request->no_bukti;
        $no_sp2d = $request->no_sp2d;
        $jns_ang = status_anggaran();

        $no_trdrka = $kd_skpd . '.' . $kd_sub_kegiatan . '.' . $kd_rek6;

        $data1 = DB::table('trdpo')
            ->select('sumber', 'nm_sumber', DB::raw("SUM(total) as nilai_sumber"))
            ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $jns_ang])
            ->whereNotNull('sumber')
            ->groupBy('sumber', 'nm_sumber');

        $data2 = DB::table('trdpo')
            ->select('sumber', DB::raw("'Silahkan isi sumber di anggaran' as nm_sumber"), DB::raw("SUM(total) as nilai_sumber"))
            ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $jns_ang])
            ->where(function ($query) {
                $query->where('sumber', '')->orWhereNull('sumber');
            })
            ->groupBy('sumber', 'nm_sumber')
            ->union($data1);

        $data_koreksi = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
            ->mergeBindings($data2)
            ->get();

        $data = DB::select("SELECT * from (
        SELECT sumber,(select nm_sumber_dana1 from sumber_dana where kd_sumber_dana1=b.sumber) as nmsumber, (select kd_sumber_dana1 from sumber_dana where kd_sumber_dana1=b.sumber) as kdsumber,
        		sum(nilai) as nilai FROM trhtransout a INNER JOIN trdtransout b ON
                a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                where b.no_bukti=? and a.no_sp2d=? and  b.kd_skpd=? and b.kd_sub_kegiatan=? and b.kd_rek6=?
                GROUP BY sumber)zz order by  sumber", [$no_bukti, $no_sp2d, $kd_skpd, $kd_sub_kegiatan, $kd_rek6]);

        // dd ($data_koreksi);

        return response()->json([
            'sumber_awal' => $data,
            'sumber_koreksi' => $data_koreksi,
        ]);
    }

    public function sumberKoreksi2(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_rek6 = $request->kd_rek6;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_bukti = $request->no_bukti;
        $no_sp2d = $request->no_sp2d;
        $jns_ang = status_anggaran();

        $no_trdrka = $kd_skpd . '.' . $kd_sub_kegiatan . '.' . $kd_rek6;

        $data1 = DB::table('trdpo')
            ->select('sumber', 'nm_sumber', DB::raw("SUM(total) as nilai_sumber"))
            ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $jns_ang])
            ->whereNotNull('sumber')
            ->groupBy('sumber', 'nm_sumber');

        $data2 = DB::table('trdpo')
            ->select('sumber', DB::raw("'Silahkan isi sumber di anggaran' as nm_sumber"), DB::raw("SUM(total) as nilai_sumber"))
            ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $jns_ang])
            ->where(function ($query) {
                $query->where('sumber', '')->orWhereNull('sumber');
            })
            ->groupBy('sumber', 'nm_sumber')
            ->union($data1);

        $data_koreksi = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
            ->mergeBindings($data2)
            ->get();

        return response()->json($data_koreksi);
    }

    public function simpanKoreksi(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_bukti = no_urut($kd_skpd);

            DB::table('trhtransout')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $no_bukti])->delete();

            DB::table('trhtransout')->insert([
                'no_kas' => $no_bukti,
                'tgl_kas' => $data['tgl_transaksi'],
                'no_bukti' => $no_bukti,
                'tgl_bukti' => $data['tgl_transaksi'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $data['kd_skpd'],
                'nm_skpd' => $data['nm_skpd'],
                'total' => $data['total'],
                'no_tagih' => '',
                'sts_tagih' => '0',
                'tgl_tagih' => $data['tgl_koreksi'],
                'jns_spp' => $data['beban'],
                'pay' => $data['pembayaran'],
                'no_kas_pot' => $no_bukti,
                'panjar' => '3',
                'no_sp2d' => '',
            ]);

            DB::table('trdtransout')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $no_bukti])->delete();

            if (isset($data['rincian_rekening'])) {
                DB::table('trdtransout')->insert(array_map(function ($value) use ($no_bukti, $data) {
                    return [
                        'no_bukti' => $no_bukti,
                        'no_sp2d' => $value['no_sp2d'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'sumber' => $value['sumber'],
                    ];
                }, $data['rincian_rekening']));
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_bukti' => $no_bukti
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function editRekening($no_bukti)
    {
        $no_bukti = Crypt::decryptString($no_bukti);
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'daftar_kegiatan' => DB::table('trdtransout as a')
                ->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->where(['a.kd_skpd' => $kd_skpd])
                ->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->distinct()
                ->get(),
            'daftar_kegiatan_koreksi' => DB::table('trskpd as a')
                ->join('ms_sub_kegiatan as b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')
                ->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->where(['a.kd_skpd' => $kd_skpd, 'a.status_sub_kegiatan' => '1', 'b.jns_sub_kegiatan' => '5'])
                ->groupByRaw("a.kd_sub_kegiatan,a.nm_sub_kegiatan")
                ->get(),
            'koreksi' => DB::table('trhtransout')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $no_bukti])->first(),
            'detail_koreksi' => DB::table('trdtransout as a')
                ->join('trhtransout as b', function ($join) {
                    $join->on('a.no_bukti', '=', 'b.no_bukti');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->select('a.*')
                ->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->get(),
        ];

        return view('skpd.koreksi_rekening.edit')->with($data);
    }

    public function updateRekening(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {

            DB::table('trhtransout')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])->delete();

            DB::table('trhtransout')->insert([
                'no_kas' => $data['no_bukti'],
                'tgl_kas' => $data['tgl_transaksi'],
                'no_bukti' => $data['no_bukti'],
                'tgl_bukti' => $data['tgl_transaksi'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $data['kd_skpd'],
                'nm_skpd' => $data['nm_skpd'],
                'total' => $data['total'],
                'no_tagih' => '',
                'sts_tagih' => '0',
                'tgl_tagih' => $data['tgl_koreksi'],
                'jns_spp' => $data['beban'],
                'pay' => $data['pembayaran'],
                'no_kas_pot' => $data['no_bukti'],
                'panjar' => '3',
                'no_sp2d' => '',
            ]);

            DB::table('trdtransout')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])->delete();

            if (isset($data['rincian_rekening'])) {
                DB::table('trdtransout')->insert(array_map(function ($value) use ($data) {
                    return [
                        'no_bukti' => $data['no_bukti'],
                        'no_sp2d' => $value['no_sp2d'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'sumber' => $value['sumber'],
                    ];
                }, $data['rincian_rekening']));
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_bukti' => $data['no_bukti']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapusRekening(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {

            DB::table('trhtransout')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $no_bukti])->delete();
            DB::table('trdtransout')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $no_bukti])->delete();

            DB::commit();
            return response()->json([
                'message' => '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    // KOREKSI TRANSAKSI NOMINAL
    public function indexNominal()
    {
        return view('skpd.koreksi_nominal.index');
    }

    public function loadDataNominal()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $tgl_terima = DB::table('trhspj_ppkd')
            ->selectRaw("ISNULL(MAX(tgl_terima),'2016-01-01') as tgl_terima")
            ->where(['cek' => '1', 'kd_skpd' => $kd_skpd])
            ->first();
        $tgl_terima = $tgl_terima->tgl_terima;

        $data = DB::table('trhtransout as a')
            ->selectRaw("a.*,'' AS nokas_pot,'' AS tgl_pot,'' AS kete,(SELECT COUNT(*) from trlpj z where z.no_bukti = a.no_bukti and z.kd_skpd = a.kd_skpd) ketlpj,(CASE WHEN a.tgl_bukti<? THEN 1 ELSE 0 END ) ketspj", [$tgl_terima])
            ->where(['a.panjar' => '5', 'a.kd_skpd' => $kd_skpd])
            ->orderBy('a.no_bukti')
            ->orderBy('a.kd_skpd')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("koreksi_nominal.edit", Crypt::encryptString($row->no_bukti)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusRekening(' . $row->no_bukti . ', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahNominal()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_kegiatan' => DB::table('trdtransout as a')
                ->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->where(['a.kd_skpd' => $kd_skpd])
                ->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->distinct()
                ->get(),
        ];
        return view('skpd.koreksi_nominal.create')->with($data);
    }

    public function simpanNominal(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_bukti = no_urut($kd_skpd);

            DB::table('trhtransout')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $no_bukti])->delete();

            DB::table('trhtransout')->insert([
                'no_kas' => $no_bukti,
                'tgl_kas' => $data['tgl_transaksi'],
                'no_bukti' => $no_bukti,
                'tgl_bukti' => $data['tgl_transaksi'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $data['kd_skpd'],
                'nm_skpd' => $data['nm_skpd'],
                'total' => $data['total'],
                'no_tagih' => '',
                'sts_tagih' => '0',
                'tgl_tagih' => $data['tgl_koreksi'],
                'jns_spp' => $data['beban'],
                'pay' => $data['pembayaran'],
                'no_kas_pot' => $no_bukti,
                'panjar' => '5',
                'no_sp2d' => '',
            ]);

            DB::table('trdtransout')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $no_bukti])->delete();

            if (isset($data['rincian_rekening'])) {
                DB::table('trdtransout')->insert(array_map(function ($value) use ($no_bukti, $data) {
                    return [
                        'no_bukti' => $no_bukti,
                        'no_sp2d' => $value['no_sp2d'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'sumber' => $value['sumber'],
                    ];
                }, $data['rincian_rekening']));
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_bukti' => $no_bukti
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function editNominal($no_bukti)
    {
        $no_bukti = Crypt::decryptString($no_bukti);
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_kegiatan' => DB::table('trdtransout as a')
                ->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->where(['a.kd_skpd' => $kd_skpd])
                ->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
                ->distinct()
                ->get(),
            'koreksi' => DB::table('trhtransout')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $no_bukti])->first(),
            'detail_koreksi' => DB::table('trdtransout as a')
                ->join('trhtransout as b', function ($join) {
                    $join->on('a.no_bukti', '=', 'b.no_bukti');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->select('a.*')
                ->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->get(),
        ];

        return view('skpd.koreksi_nominal.edit')->with($data);
    }

    public function updateNominal(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {

            DB::table('trhtransout')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])->delete();

            DB::table('trhtransout')->insert([
                'no_kas' => $data['no_bukti'],
                'tgl_kas' => $data['tgl_transaksi'],
                'no_bukti' => $data['no_bukti'],
                'tgl_bukti' => $data['tgl_transaksi'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $data['kd_skpd'],
                'nm_skpd' => $data['nm_skpd'],
                'total' => $data['total'],
                'no_tagih' => '',
                'sts_tagih' => '0',
                'tgl_tagih' => $data['tgl_koreksi'],
                'jns_spp' => $data['beban'],
                'pay' => $data['pembayaran'],
                'no_kas_pot' => $data['no_bukti'],
                'panjar' => '5',
                'no_sp2d' => '',
            ]);

            DB::table('trdtransout')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])->delete();

            if (isset($data['rincian_rekening'])) {
                DB::table('trdtransout')->insert(array_map(function ($value) use ($data) {
                    return [
                        'no_bukti' => $data['no_bukti'],
                        'no_sp2d' => $value['no_sp2d'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'sumber' => $value['sumber'],
                    ];
                }, $data['rincian_rekening']));
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_bukti' => $data['no_bukti']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapusNominal(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {

            DB::table('trhtransout')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $no_bukti])->delete();
            DB::table('trdtransout')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $no_bukti])->delete();

            DB::commit();
            return response()->json([
                'message' => '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }
}
