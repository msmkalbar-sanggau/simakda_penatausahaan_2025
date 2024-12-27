<?php

namespace App\Http\Controllers\spd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

use Exception;
use Knp\Snappy\Pdf as SnappyPdf;
use PDF;

class SPDBelanjaController extends Controller
{
    public function index()
    {
        $data = [
            'ppkd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where(['kode' => 'PPKD'])->get(),
        ];
        return view('penatausahaan.spd.spd_belanja.index')->with($data);
    }

    public function loadData()
    {
        $id = Auth::user()->id;
        $skpd = Auth::user()->kd_skpd;

        $data = DB::table('trhspd as a')->Select(
            'a.*',
            DB::raw("(select TOP 1 nama from ms_ttd b where a.kd_bkeluar=b.nip ) as nama"),
            DB::raw("case when jns_beban='5' then 'BELANJA' else 'PEMBIAYAAN' end AS nm_beban"),
            DB::raw("(select nama from tb_status_angkas where a.jns_ang=status_kunci) as nm_angkas")
        )
            ->whereIn('a.jns_beban', [5, 6])
            // ->whereRaw('a.kd_skpd IN (SELECT kd_skpd FROM user_bud WHERE user_id= ?)', [$id])
            ->where(function ($query) use ($skpd) {
                if (Auth::user()->is_admin == 2) {
                    $query->where(['a.kd_skpd' => $skpd]);
                }
            })
            ->groupBy([
                'a.no_spd', 'a.tgl_spd', 'a.kd_skpd', 'a.nm_skpd', 'a.jns_beban',
                'a.no_dpa', 'a.bulan_awal', 'a.bulan_akhir', 'a.kd_bkeluar', 'a.triwulan', 'a.klain',
                'a.username', 'a.tglupdate', 'a.st', 'a.status', 'a.total', 'revisi_ke', 'jns_ang', 'jns_angkas'
            ])
            ->orderBy('no_spd')->orderBy('tgl_spd')->orderBy('kd_skpd')->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("spdBP.show", Crypt::encryptString($row->no_spd)) . '" class="btn btn-info btn-sm" style="margin-right:4px" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat SPD"><i class="fas fa-info-circle"></i></a>';
            if ($row->status != '1') {
                $btn .= '<a href="javascript:void(0);" onclick="hapusSPD(\'' . $row->no_spd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus SPD"><i class="fas fa-trash-alt"></i></a>';
            }
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_spd . '\');" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak SPD"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('penatausahaan.spd.spd_belanja.index');
    }

    public function create()
    {
        $user = Auth()->user()->nama;
        DB::table('spd_temp')->where(['username' => $user])->Delete();
        $data = [
            'idpage' => uniqid('spd-page-id#', true),
            'jenisblnspd' => DB::table('trkonfig_spd')->select('jenis_spd')->first(),
        ];
        return view('penatausahaan.spd.spd_belanja.create')->with($data);
    }

    public function getSKPD(Request $request)
    {
        $term = $request->term;

        $results = DB::table('ms_skpd')
            ->select('kd_skpd as id', 'nm_skpd as text', 'kd_skpd', 'nm_skpd')
            //->whereRaw("right(kd_skpd, 5) = '.0000'")
            ->when($term, function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->orWhere('kd_skpd', 'like', '%' . $term . '%')
                        ->orWhere('nm_skpd', 'like', '%' . $term . '%');
                });
            })->get();

        return response()->json(['results' => $results]);
    }

    public function getNipSKPD(Request $request)
    {
        $term = $request->term;
        $kd_skpd = $request->kd_skpd;

        $results = DB::table('ms_ttd')
            ->select('nip as id', 'nama as text', 'nip', 'nama', 'jabatan', 'kd_skpd')
            ->whereIn('kode', ['PA', 'KPA'])->where('kd_skpd', $kd_skpd)
            ->when($term, function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->orWhere('nip', 'like', '%' . $term . '%')
                        ->orWhere('nama', 'like', '%' . $term . '%');
                });
            })->get();

        return response()->json(['results' => $results]);
    }

    public function getJenisAng(Request $request)
    {
        $term = $request->term;
        $kd_skpd = $request->kd_skpd;

        $results = DB::table('trskpd as a')
            ->select(DB::raw('DISTINCT(a.jns_ang) as id'), 'b.nama as text', 'b.kode as kode', 'b.nama as nama')
            ->join('tb_status_anggaran as b', function ($join) {
                $join->on('a.jns_ang', '=', 'b.kode');
            })->where(['a.kd_skpd' => $kd_skpd])
            ->when($term, function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->orWhere('nama', 'like', '%' . $term . '%');
                });
            })
            ->get();

        return response()->json(['results' => $results]);
    }

    public function getStatusAng(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jns_ang = $request->kode;

        $results = statusAngkas($kd_skpd, $jns_ang);

        return response()->json(['results' => $results]);
    }

    public function getSpdBelanja(Request $request)
    {
        $kd_skpd = left($request->kd_skpd, 22);
        $skpd = $request->kd_skpd;
        $jns_ang = $request->jns_ang;
        $tgl = $request->tanggal;
        $bulanAwal = $request->bln_awal;
        $bulanAkhir = $request->bln_akhir;
        $jenis = $request->jenis;
        $revisi = $request->revisi;
        $nomor = $request->nomor;
        $page = $request->page;
        $status_angkas = $request->status_ang;
        $sts_ang = tbStatusAngkas($status_angkas);

        if ($jenis == 5) {
            if ($revisi == 1) {
                $data =  DB::select(
                    "SELECT ROW_NUMBER() OVER (ORDER BY a.kd_unit ASC) AS rownumber, a.kd_unit, a.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program,
                            a.kd_rek6 , a.nm_rek6, isnull(a.total_ubah, 0) AS anggaran, isnull(nilai,0) AS nilai, isnull(lalu, 0) as lalu
                        FROM
                        (
                            SELECT a.kd_skpd AS kd_unit, b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program, b.kd_rek6 , b.nm_rek6 , SUM(b.nilai) AS total_ubah, LEFT(a.kd_skpd, 22) kd_skpd
                                FROM trskpd a
                                INNER JOIN trdrka b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_skpd= b.kd_skpd
                                INNER JOIN ms_sub_kegiatan c ON a.kd_sub_kegiatan= c.kd_sub_kegiatan
                            WHERE LEFT(b.kd_skpd, 22) = ? AND c.jns_sub_kegiatan= '5' AND b.jns_ang= ?
                            GROUP BY LEFT(a.kd_skpd, 22), a.kd_skpd, a.kd_program, a.nm_program, b.kd_sub_kegiatan, a.nm_sub_kegiatan, b.kd_rek6, b.nm_rek6
                        ) a LEFT JOIN
                        (
                            SELECT kd_sub_kegiatan, b.kd_rek6, LEFT(kd_skpd, 22) kd_skpd, kd_skpd AS kd_unit, SUM($sts_ang) AS nilai
                                FROM trdskpd_ro b
                            WHERE (b.bulan BETWEEN ? AND ?) AND LEFT(kd_skpd, 22) = ?
                            GROUP BY LEFT(kd_skpd, 22), kd_skpd, kd_sub_kegiatan, kd_rek6
                        ) b ON a.kd_unit= b.kd_unit AND a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_rek6= b.kd_rek6 LEFT JOIN
                        (
                            SELECT kd_unit AS kd_skpd, kd_sub_kegiatan, kd_rek6, isnull(SUM(a.nilai), 0) AS lalu
                                FROM trdspd a
                                LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                            WHERE LEFT(b.kd_skpd, 22) = ?  AND a.no_spd != ? AND b.tgl_spd < ?
                            GROUP BY kd_unit, kd_sub_kegiatan, kd_rek6
                        ) c ON a.kd_unit= c.kd_skpd AND a.kd_sub_kegiatan= c.kd_sub_kegiatan AND a.kd_rek6= c.kd_rek6
                    WHERE a.kd_sub_kegiatan NOT IN(SELECT TOP 0 x.kd_sub_kegiatan FROM trskpd x INNER JOIN ms_sub_kegiatan y ON x.kd_sub_kegiatan= y.kd_sub_kegiatan
                            WHERE LEFT (x.kd_skpd, 22) = ? AND y.jns_sub_kegiatan IN ('61', '62', '4'))
                        AND NOT EXISTS (
                            SELECT * FROM spd_temp where
                            left(a.kd_unit, 22) = left(spd_temp.kd_skpd, 22)
                            AND a.kd_sub_kegiatan = spd_temp.kd_sub_kegiatan
                            AND a.kd_rek6 = spd_temp.kd_rek6 AND spd_temp.bulan_awal = ?
                            AND spd_temp.bulan_akhir = ? AND spd_temp.jns_ang = ?
                            AND spd_temp.jns_angkas = ? AND spd_temp.jns_beban = ? and spd_temp.page_id = ?
                        )
                    ORDER BY a.kd_unit, a.kd_sub_kegiatan",
                    [
                        $kd_skpd, $jns_ang, $bulanAwal, $bulanAkhir, $kd_skpd, $kd_skpd, $nomor, $tgl, $kd_skpd,
                        $bulanAwal, $bulanAkhir, $jns_ang, $status_angkas, $jenis, $page
                    ]
                );
            } else {
                $data = DB::select(
                    "SELECT ROW_NUMBER() OVER (ORDER BY a.kd_unit ASC) AS rownumber, a.kd_unit, a.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program,
                            a.kd_rek6 , a.nm_rek6, isnull(a.total_ubah, 0) AS anggaran, nilai - isnull(lalu_tw, 0) AS nilai, isnull(lalu, 0) as lalu
                        FROM
                        (
                            SELECT a.kd_skpd AS kd_unit, b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program,
                                    b.kd_rek6, b.nm_rek6, SUM(b.nilai) AS total_ubah, LEFT(a.kd_skpd, 22) kd_skpd
                                FROM trskpd a
                                INNER JOIN trdrka b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_skpd= b.kd_skpd
                                INNER JOIN ms_sub_kegiatan c ON a.kd_sub_kegiatan= c.kd_sub_kegiatan
                            WHERE LEFT(b.kd_skpd, 22) = ? AND c.jns_sub_kegiatan= '5' AND b.jns_ang = ?
                            GROUP BY LEFT(a.kd_skpd, 22), a.kd_skpd, a.kd_program, a.nm_program, b.kd_sub_kegiatan,
                                    a.nm_sub_kegiatan, b.kd_rek6, b.nm_rek6
                        ) a LEFT JOIN
                        (
                            SELECT kd_sub_kegiatan, b.kd_rek6, LEFT(kd_skpd, 22) kd_skpd, kd_skpd AS kd_unit, SUM($sts_ang) AS nilai
                                FROM trdskpd_ro b
                            WHERE b.bulan >= ? AND b.bulan <= ? AND LEFT(kd_skpd, 22) = ?
                            GROUP BY LEFT(kd_skpd, 22), kd_skpd, kd_sub_kegiatan, kd_rek6
                        ) b ON a.kd_unit= b.kd_unit AND a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_rek6= b.kd_rek6 LEFT JOIN
                        (
                            SELECT kd_unit AS kd_skpd, kd_sub_kegiatan, kd_rek6, isnull(SUM(a.nilai), 0) AS lalu
                                FROM trdspd a
                                LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                            WHERE LEFT(b.kd_skpd, 22) = ? AND a.no_spd != ? AND b.tgl_spd < ?
                            GROUP BY kd_unit, kd_sub_kegiatan, kd_rek6
                        ) c ON a.kd_unit= c.kd_skpd AND a.kd_sub_kegiatan= c.kd_sub_kegiatan AND a.kd_rek6= c.kd_rek6 LEFT JOIN
                        (
                            SELECT kd_unit AS kd_skpd, kd_sub_kegiatan, kd_rek6, isnull(SUM(a.nilai), 0) AS lalu_tw
                                FROM trdspd a
                                LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                            WHERE LEFT(b.kd_skpd, 22) = ? AND b.bulan_awal= ? AND b.bulan_akhir= ? AND a.no_spd != ? AND b.tgl_spd < ?
                            GROUP BY kd_unit, kd_sub_kegiatan, kd_rek6
                        ) d ON a.kd_unit= d.kd_skpd AND a.kd_sub_kegiatan= d.kd_sub_kegiatan AND a.kd_rek6= d.kd_rek6
                    WHERE a.kd_unit + a.kd_sub_kegiatan + a.kd_rek6 NOT IN
                        (SELECT b.kd_skpd + b.kd_sub_kegiatan + b.kd_rek6
                            FROM trskpd a
                            INNER JOIN trdrka b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_skpd= b.kd_skpd
                            INNER JOIN ms_sub_kegiatan c ON a.kd_sub_kegiatan= c.kd_sub_kegiatan
                            WHERE LEFT(b.kd_skpd, 22) = ? AND c.jns_sub_kegiatan= '6'
                            GROUP BY b.kd_skpd, b.kd_sub_kegiatan, b.kd_rek6
                        )
                        AND NOT EXISTS (
                            SELECT * FROM spd_temp where
                            left(a.kd_unit, 22) = left(spd_temp.kd_skpd, 22)
                            AND a.kd_sub_kegiatan = spd_temp.kd_sub_kegiatan
                            AND a.kd_rek6 = spd_temp.kd_rek6
                            AND spd_temp.bulan_awal = ? AND spd_temp.bulan_akhir = ? AND spd_temp.jns_ang = ?
                            AND spd_temp.jns_angkas = ? AND spd_temp.jns_beban = ? and spd_temp.page_id = ?
                        )
                    ORDER BY a.kd_unit, a.kd_sub_kegiatan",
                    [
                        $kd_skpd, $jns_ang, $bulanAwal, $bulanAkhir, $kd_skpd, $kd_skpd, $nomor, $tgl, $kd_skpd,
                        $bulanAwal, $bulanAkhir, $nomor, $tgl, $kd_skpd, $bulanAwal, $bulanAkhir, $jns_ang, $status_angkas, $jenis, $page
                    ]
                );
            }
            return DataTables::of($data)->addIndexColumn()->make(true);
        } else {
            $data = DB::select(
                "SELECT ROW_NUMBER() OVER (ORDER BY a.kd_skpd ASC) AS rownumber, a.kd_skpd as kd_unit, a.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program, a.kd_rek6, a.nm_rek6,
                        a.total_ubah AS anggaran, (nilai - isnull(lalu_tw, 0)) AS nilai, isnull(lalu, 0) as lalu
                    FROM
                    (
                        SELECT b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program, b.kd_rek6,
                            b.nm_rek6, SUM(nilai) AS total_ubah, b.kd_skpd
                            FROM trskpd a
                            INNER JOIN trdrka b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan  AND a.kd_skpd= b.kd_skpd
                            INNER JOIN ms_sub_kegiatan c ON b.kd_sub_kegiatan= c.kd_sub_kegiatan AND b.jns_ang = ?
                        WHERE b.kd_skpd = ? AND c.jns_sub_kegiatan = '62'
                        GROUP BY b.kd_rek6, b.nm_rek6, b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program, b.kd_skpd
                    ) a LEFT JOIN
                    (
                        SELECT kd_rek6, kd_sub_kegiatan, kd_skpd, SUM($sts_ang) AS nilai
                            FROM trdskpd_ro b
                        WHERE b.bulan >= ? AND b.bulan <= ? AND kd_skpd = ?
                        GROUP BY kd_rek6, kd_sub_kegiatan, kd_skpd
                    ) b ON a.kd_rek6= b.kd_rek6 AND a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_skpd= b.kd_skpd LEFT JOIN
                    (
                        SELECT a.kd_rek6, kd_sub_kegiatan, SUM(a.nilai) AS lalu
                            FROM trdspd a
                            LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                        WHERE b.kd_skpd = ? AND a.no_spd != ? AND b.tgl_spd < ?
                        GROUP BY a.kd_rek6, kd_sub_kegiatan
                    ) c ON a.kd_rek6= c.kd_rek6 AND a.kd_sub_kegiatan= c.kd_sub_kegiatan LEFT JOIN
                    (
                        SELECT kd_unit AS kd_skpd, kd_sub_kegiatan, kd_rek6, isnull(SUM(a.nilai), 0) AS lalu_tw
                            FROM trdspd a
                            LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                        WHERE b.kd_skpd = ? AND b.bulan_awal= ? AND b.bulan_akhir= ? AND a.no_spd != ? AND b.tgl_spd< ?
                        GROUP BY kd_unit, kd_sub_kegiatan, kd_rek6
                    ) d ON a.kd_rek6= d.kd_rek6 AND a.kd_sub_kegiatan= d.kd_sub_kegiatan
                    WHERE a.kd_sub_kegiatan NOT IN
                        (
                            SELECT a.kd_sub_kegiatan FROM trskpd a
                            INNER JOIN ms_sub_kegiatan b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan
                            WHERE a.kd_skpd = ? AND b.jns_sub_kegiatan = '5'
                        ) AND NOT EXISTS (
                            SELECT * FROM spd_temp where
                            left(a.kd_skpd, 22) = left(spd_temp.kd_skpd, 22)
                            AND a.kd_sub_kegiatan = spd_temp.kd_sub_kegiatan
                            AND a.kd_rek6 = spd_temp.kd_rek6
                            AND spd_temp.bulan_awal = ? AND spd_temp.bulan_akhir = ? AND spd_temp.jns_ang = ?
                            AND spd_temp.jns_angkas = ? AND spd_temp.jns_beban = ? and spd_temp.page_id = ?
                        )
                        ORDER BY a.kd_sub_kegiatan",
                [
                    $jns_ang, $skpd, $bulanAwal, $bulanAkhir, $skpd, $skpd, $nomor, $tgl, $skpd, $bulanAwal, $bulanAkhir, $nomor, $tgl, $skpd,
                    $bulanAwal, $bulanAkhir, $jns_ang, $status_angkas, $jenis, $page
                ]
            );

            return DataTables::of($data)->addIndexColumn()->make(true);
        }
    }

    public function getInsertSpd(Request $request)
    {
        $data = $request->data;
        DB::beginTransaction();
        try {
            DB::table('spd_temp')
                ->insert([
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['kd_rek6'],
                    'bulan_awal' => $data['bln_awal'],
                    'bulan_akhir' => $data['bln_akhir'],
                    'nilai' => is_null($data['nilai']) ? '0' : $data['nilai'],
                    'nilai_lalu' => $data['lalu'],
                    'page_id' => $data['page'],
                    'anggaran' => $data['anggaran'],
                    'jns_ang' => $data['jns_ang'],
                    'jns_angkas' => $data['status_ang'],
                    'jns_beban' => $data['jenis'],
                    'revisi' => $data['revisi'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'username' => Auth::user()->nama
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

    public function getSpdBelanjaTemp(Request $request)
    {
        $kd_skpd = left($request->kd_skpd, 22);
        $jns_ang = $request->jns_ang;
        $bulanAwal = $request->bln_awal;
        $bulanAkhir = $request->bln_akhir;
        $jenis = $request->jenis;
        $revisi = $request->revisi;
        $page = $request->page;
        $status_angkas = $request->status_ang;

        $data = DB::select(
            "SELECT * FROM spd_temp where
                left(kd_skpd, 22) = ? AND bulan_awal = ?
                AND bulan_akhir = ? AND jns_ang = ?
                AND jns_angkas = ? AND jns_beban = ? and revisi = ? and page_id = ?",
            [
                $kd_skpd, $bulanAwal, $bulanAkhir, $jns_ang, $status_angkas, $jenis, $revisi, $page
            ]
        );
        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function getDeleteSpdTemp(Request $request)
    {
        $data = $request->data;
        DB::beginTransaction();

        try {
            DB::table('spd_temp')
                ->where([
                    'kd_skpd' => $data['kd_skpd'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'kd_rek6' => $data['kd_rek6'],
                    'bulan_awal' => $data['bln_awal'],
                    'bulan_akhir' => $data['bln_akhir'],
                    'nilai' => $data['nilai'],
                    'nilai_lalu' => $data['lalu'],
                    'page_id' => $data['page'],
                    'anggaran' => $data['anggaran'],
                    'jns_ang' => $data['jns_ang'],
                    'jns_angkas' => $data['status_ang'],
                    'jns_beban' => $data['jenis'],
                    'revisi' => $data['revisi'],
                    'username' => Auth::user()->nama,
                ])->delete();
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

    public function getInsertAllSpdTemp(Request $request)
    {
        $data = $request->data;
        $kd_skpd = left($data['kd_skpd'], 22);
        $skpd = $data['kd_skpd'];
        $jns_ang = $data['jns_ang'];
        $tgl = $data['tanggal'];
        $bulanAwal = $data['bln_awal'];
        $bulanAkhir = $data['bln_akhir'];
        $jenis = $data['jenis'];
        $revisi = $data['revisi'];
        $nomor = $data['nomor'];
        $page = $data['page'];
        $status_angkas = $data['status_ang'];
        $sts_ang = tbStatusAngkas($status_angkas);
        DB::beginTransaction();

        try {
            if ($jenis == 5) {
                if ($revisi == 1) {
                    $data =  DB::statement(
                        "INSERT spd_temp (kd_skpd, kd_sub_kegiatan, kd_rek6, bulan_awal, bulan_akhir, nilai,
                        created_at, jns_ang, jns_angkas, jns_beban, nilai_lalu, anggaran, page_id, revisi, username)
                        SELECT a.kd_unit, a.kd_sub_kegiatan, a.kd_rek6, ?, ?, isnull(nilai, 0) AS nilai,
                            ?, ?, ?, ?, isnull(lalu,0) as lalu, isnull(a.total_ubah,0) AS anggaran, ?, ?, ?
                            FROM
                            (
                                SELECT a.kd_skpd AS kd_unit, b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program, b.kd_rek6 , b.nm_rek6 , SUM(b.nilai) AS total_ubah, LEFT(a.kd_skpd, 22) kd_skpd
                                    FROM trskpd a
                                    INNER JOIN trdrka b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_skpd= b.kd_skpd
                                    INNER JOIN ms_sub_kegiatan c ON a.kd_sub_kegiatan= c.kd_sub_kegiatan
                                WHERE LEFT(b.kd_skpd, 22) = ? AND c.jns_sub_kegiatan= '5' AND b.jns_ang= ?
                                GROUP BY LEFT(a.kd_skpd, 22), a.kd_skpd, a.kd_program, a.nm_program, b.kd_sub_kegiatan, a.nm_sub_kegiatan, b.kd_rek6, b.nm_rek6
                            ) a LEFT JOIN
                            (
                                SELECT kd_sub_kegiatan, b.kd_rek6, LEFT(kd_skpd, 22) kd_skpd, kd_skpd AS kd_unit, SUM($sts_ang) AS nilai
                                    FROM trdskpd_ro b
                                WHERE (b.bulan BETWEEN ? AND ?) AND LEFT(kd_skpd, 22) = ?
                                GROUP BY LEFT(kd_skpd, 22), kd_skpd, kd_sub_kegiatan, kd_rek6
                            ) b ON a.kd_unit= b.kd_unit AND a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_rek6= b.kd_rek6 LEFT JOIN
                            (
                                SELECT kd_unit AS kd_skpd, kd_sub_kegiatan, kd_rek6, isnull(SUM(a.nilai), 0) AS lalu
                                    FROM trdspd a
                                    LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                                WHERE LEFT(b.kd_skpd, 22) = ?  AND a.no_spd != ? AND b.tgl_spd < ?
                                GROUP BY kd_unit, kd_sub_kegiatan, kd_rek6
                            ) c ON a.kd_unit= c.kd_skpd AND a.kd_sub_kegiatan= c.kd_sub_kegiatan AND a.kd_rek6= c.kd_rek6
                        WHERE a.kd_sub_kegiatan NOT IN(SELECT TOP 0 x.kd_sub_kegiatan FROM trskpd x INNER JOIN ms_sub_kegiatan y ON x.kd_sub_kegiatan= y.kd_sub_kegiatan
                                WHERE LEFT (x.kd_skpd, 22) = ? AND y.jns_sub_kegiatan IN ('61', '62', '4'))
                            AND NOT EXISTS (
                                SELECT 1 FROM spd_temp where
                                left(a.kd_unit, 22) = left(spd_temp.kd_skpd, 22) AND a.kd_sub_kegiatan = spd_temp.kd_sub_kegiatan
                                AND a.kd_rek6 = spd_temp.kd_rek6 AND spd_temp.bulan_awal = ?
                                AND spd_temp.bulan_akhir = ? AND spd_temp.jns_ang = ?
                                AND spd_temp.jns_angkas = ? AND spd_temp.jns_beban = ? and spd_temp.page_id = ?
                            )
                        ORDER BY a.kd_unit, a.kd_sub_kegiatan",
                        [
                            $bulanAwal, $bulanAkhir, date('Y-m-d H:i:s'), $jns_ang, $status_angkas,
                            $jenis, $page, $revisi, Auth()->user()->nama,
                            $kd_skpd, $jns_ang, $bulanAwal, $bulanAkhir, $kd_skpd, $kd_skpd, $nomor, $tgl, $kd_skpd,
                            $bulanAwal, $bulanAkhir, $jns_ang, $status_angkas, $jenis, $page
                        ]
                    );
                } else {
                    $data = DB::statement(
                        "INSERT spd_temp (kd_skpd, kd_sub_kegiatan, kd_rek6, bulan_awal, bulan_akhir, nilai,
                        created_at, jns_ang, jns_angkas, jns_beban, nilai_lalu, anggaran, page_id, revisi, username)
                        SELECT a.kd_unit, a.kd_sub_kegiatan, a.kd_rek6, ?, ?, isnull((nilai - isnull(lalu_tw, 0)),0) AS nilai,
                        ?, ?, ?, ?, isnull(lalu,0) as lalu, isnull(a.total_ubah,0) AS anggaran, ?, ?, ?
                            FROM
                            (
                                SELECT a.kd_skpd AS kd_unit, b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program,
                                        b.kd_rek6, b.nm_rek6, SUM(b.nilai) AS total_ubah, LEFT(a.kd_skpd, 22) kd_skpd
                                    FROM trskpd a
                                    INNER JOIN trdrka b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_skpd= b.kd_skpd
                                    INNER JOIN ms_sub_kegiatan c ON a.kd_sub_kegiatan= c.kd_sub_kegiatan
                                WHERE LEFT(b.kd_skpd, 22) = ? AND c.jns_sub_kegiatan= '5' AND b.jns_ang = ?
                                GROUP BY LEFT(a.kd_skpd, 22), a.kd_skpd, a.kd_program, a.nm_program, b.kd_sub_kegiatan,
                                        a.nm_sub_kegiatan, b.kd_rek6, b.nm_rek6
                            ) a LEFT JOIN
                            (
                                SELECT kd_sub_kegiatan, b.kd_rek6, LEFT(kd_skpd, 22) kd_skpd, kd_skpd AS kd_unit, SUM($sts_ang) AS nilai
                                    FROM trdskpd_ro b
                                WHERE b.bulan >= ? AND b.bulan <= ? AND LEFT(kd_skpd, 22) = ?
                                GROUP BY LEFT(kd_skpd, 22), kd_skpd, kd_sub_kegiatan, kd_rek6
                            ) b ON a.kd_unit= b.kd_unit AND a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_rek6= b.kd_rek6 LEFT JOIN
                            (
                                SELECT kd_unit AS kd_skpd, kd_sub_kegiatan, kd_rek6, isnull(SUM(a.nilai), 0) AS lalu
                                    FROM trdspd a
                                    LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                                WHERE LEFT(b.kd_skpd, 22) = ? AND a.no_spd != ? AND b.tgl_spd < ?
                                GROUP BY kd_unit, kd_sub_kegiatan, kd_rek6
                            ) c ON a.kd_unit= c.kd_skpd AND a.kd_sub_kegiatan= c.kd_sub_kegiatan AND a.kd_rek6= c.kd_rek6 LEFT JOIN
                            (
                                SELECT kd_unit AS kd_skpd, kd_sub_kegiatan, kd_rek6, isnull(SUM(a.nilai), 0) AS lalu_tw
                                    FROM trdspd a
                                    LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                                WHERE LEFT(b.kd_skpd, 22) = ? AND b.bulan_awal= ? AND b.bulan_akhir= ? AND a.no_spd != ? AND b.tgl_spd < ?
                                GROUP BY kd_unit, kd_sub_kegiatan, kd_rek6
                            ) d ON a.kd_unit= d.kd_skpd AND a.kd_sub_kegiatan= d.kd_sub_kegiatan AND a.kd_rek6= d.kd_rek6
                        WHERE a.kd_unit + a.kd_sub_kegiatan + a.kd_rek6 NOT IN
                            (SELECT b.kd_skpd + b.kd_sub_kegiatan + b.kd_rek6
                                FROM trskpd a
                                INNER JOIN trdrka b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_skpd= b.kd_skpd
                                INNER JOIN ms_sub_kegiatan c ON a.kd_sub_kegiatan= c.kd_sub_kegiatan
                                WHERE LEFT(b.kd_skpd, 22) = ? AND c.jns_sub_kegiatan= '6'
                                GROUP BY b.kd_skpd, b.kd_sub_kegiatan, b.kd_rek6
                            )
                            AND NOT EXISTS (
                                SELECT 1 FROM spd_temp where
                                left(a.kd_unit, 22) = left(spd_temp.kd_skpd, 22) AND a.kd_sub_kegiatan = spd_temp.kd_sub_kegiatan
                                AND a.kd_rek6 = spd_temp.kd_rek6 AND spd_temp.bulan_awal = ?
                                AND spd_temp.bulan_akhir = ? AND spd_temp.jns_ang = ?
                                AND spd_temp.jns_angkas = ? AND spd_temp.jns_beban = ? and spd_temp.page_id = ?
                            )
                        ORDER BY a.kd_unit, a.kd_sub_kegiatan",
                        [
                            $bulanAwal, $bulanAkhir, date('Y-m-d H:i:s'), $jns_ang, $status_angkas,
                            $jenis, $page, $revisi, Auth()->user()->nama,
                            $kd_skpd, $jns_ang, $bulanAwal, $bulanAkhir, $kd_skpd, $kd_skpd, $nomor, $tgl, $kd_skpd,
                            $bulanAwal, $bulanAkhir, $nomor, $tgl, $kd_skpd, $bulanAwal, $bulanAkhir, $jns_ang, $status_angkas, $jenis, $page
                        ]
                    );
                }
            } else {
                $data = DB::statement(
                    "INSERT spd_temp (kd_skpd, kd_sub_kegiatan, kd_rek6, bulan_awal, bulan_akhir, nilai,
                    created_at, jns_ang, jns_angkas, jns_beban, nilai_lalu, anggaran, page_id, revisi, username)
                    SELECT a.kd_skpd as kd_unit, a.kd_sub_kegiatan, a.kd_rek6, ?, ?, isnull((nilai - isnull(lalu_tw, 0)), 0) AS nilai,
                            ?, ?, ?, ?, isnull(lalu, 0) as lalu, isnull(a.total_ubah,0) AS anggaran, ?, ?, ?
                        FROM
                        (
                            SELECT b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program, b.kd_rek6,
                                b.nm_rek6, SUM(nilai) AS total_ubah, b.kd_skpd
                                FROM trskpd a
                                INNER JOIN trdrka b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan  AND a.kd_skpd= b.kd_skpd
                                INNER JOIN ms_sub_kegiatan c ON b.kd_sub_kegiatan= c.kd_sub_kegiatan AND b.jns_ang = ?
                            WHERE b.kd_skpd = ? AND c.jns_sub_kegiatan = '62'
                            GROUP BY b.kd_rek6, b.nm_rek6, b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program, b.kd_skpd
                        ) a LEFT JOIN
                        (
                            SELECT kd_rek6, kd_sub_kegiatan, kd_skpd, SUM($sts_ang) AS nilai
                                FROM trdskpd_ro b
                            WHERE b.bulan >= ? AND b.bulan <= ? AND kd_skpd = ?
                            GROUP BY kd_rek6, kd_sub_kegiatan, kd_skpd
                        ) b ON a.kd_rek6= b.kd_rek6 AND a.kd_sub_kegiatan= b.kd_sub_kegiatan AND a.kd_skpd= b.kd_skpd LEFT JOIN
                        (
                            SELECT a.kd_rek6, kd_sub_kegiatan, SUM(a.nilai) AS lalu
                                FROM trdspd a
                                LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                            WHERE b.kd_skpd = ? AND a.no_spd != ? AND b.tgl_spd < ?
                            GROUP BY a.kd_rek6, kd_sub_kegiatan
                        ) c ON a.kd_rek6= c.kd_rek6 AND a.kd_sub_kegiatan= c.kd_sub_kegiatan LEFT JOIN
                        (
                            SELECT kd_unit AS kd_skpd, kd_sub_kegiatan, kd_rek6, isnull(SUM(a.nilai), 0) AS lalu_tw
                                FROM trdspd a
                                LEFT JOIN trhspd b ON a.no_spd= b.no_spd
                            WHERE b.kd_skpd = ? AND b.bulan_awal= ? AND b.bulan_akhir= ? AND a.no_spd != ? AND b.tgl_spd< ?
                            GROUP BY kd_unit, kd_sub_kegiatan, kd_rek6
                        ) d ON a.kd_rek6= d.kd_rek6 AND a.kd_sub_kegiatan= d.kd_sub_kegiatan
                        WHERE a.kd_sub_kegiatan NOT IN
                            (
                                SELECT a.kd_sub_kegiatan FROM trskpd a
                                INNER JOIN ms_sub_kegiatan b ON a.kd_sub_kegiatan= b.kd_sub_kegiatan
                                WHERE a.kd_skpd = ? AND b.jns_sub_kegiatan = '5'
                            ) AND NOT EXISTS (
                                SELECT * FROM spd_temp where
                                left(a.kd_skpd, 22) = left(spd_temp.kd_skpd, 22)
                                AND a.kd_sub_kegiatan = spd_temp.kd_sub_kegiatan
                                AND a.kd_rek6 = spd_temp.kd_rek6
                                AND spd_temp.bulan_awal = ? AND spd_temp.bulan_akhir = ? AND spd_temp.jns_ang = ?
                                AND spd_temp.jns_angkas = ? AND spd_temp.jns_beban = ? and spd_temp.page_id = ?
                            )
                            ORDER BY a.kd_sub_kegiatan",
                    [
                        $bulanAwal, $bulanAkhir, date('Y-m-d H:i:s'), $jns_ang, $status_angkas,
                        $jenis, $page, $revisi, Auth()->user()->nama,
                        $jns_ang, $skpd, $bulanAwal, $bulanAkhir, $skpd, $skpd, $nomor, $tgl, $skpd, $bulanAwal, $bulanAkhir, $nomor, $tgl, $skpd,
                        $bulanAwal, $bulanAkhir, $jns_ang, $status_angkas, $jenis, $page
                    ]
                );
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

    public function getDeleteAllSpdTemp(Request $request)
    {
        $data = $request->data;
        DB::beginTransaction();

        try {
            DB::table('spd_temp')
                ->where([
                    'jns_ang' => $data['jns_ang'],
                    'bulan_awal' => $data['bln_awal'],
                    'bulan_akhir' => $data['bln_akhir'],
                    'jns_beban' => $data['jenis'],
                    'page_id' => $data['page'],
                    'jns_angkas' => $data['status_ang'],
                    'revisi' => $data['revisi'],
                ])->delete();
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

    public function simpanSPP(Request $request)
    {
        $data = $request->data;
        $user = Auth::user()->nama;

        DB::beginTransaction();
        try {
            ini_set('max_input_vars', '6000');
            $nospdexplode = explode("/", $data['nomor']);
            $nospd = $nospdexplode[2];

            $nomorspd = DB::table('trhspd')->where(['no_spd' => $data['nomor']])->count();
            $nomor_spd = $data['nomor'];
            $field_angkas = status_angkas1($data['skpd']);
            $status = DB::table('trhrka')
                ->select('jns_ang')
                ->where(['kd_skpd' => $data['skpd'], 'status' => '1'])
                ->orderByDesc('tgl_dpa')
                ->first();

            $status1 = $status->jns_ang;

            $data['daftar_spd'] = json_decode($data['daftar_spd'], true);

            $rincian_data = $data['daftar_spd'];

            if ($nomorspd > 0) {
                DB::rollBack();
                return response()->json([
                    'message' => '2'
                ]);
            } else {
                if ($data['revisi'] == '1') {

                    $revisi = DB::table('trhspd')->selectRaw('max(revisi_ke)+1 as revisi')
                        ->where(['kd_skpd' => $data['skpd'], 'bulan_awal' => $data['bulan_awal'], 'bulan_akhir' => $data['bulan_akhir']])
                        ->first();

                    $nmskpd = DB::table('ms_skpd')->select('nm_skpd')
                        ->where(['kd_skpd' => $data['skpd']])->first();

                    DB::table('trhspd')->insert([
                        'no_spd' => $data['nomor'],
                        'tgl_spd' => $data['tanggal'],
                        'kd_skpd' => $data['skpd'],
                        'nm_skpd' => Str::of($nmskpd->nm_skpd)->trim(),
                        'jns_beban' => $data['jenis'],
                        'bulan_awal' => $data['bulan_awal'],
                        'bulan_akhir' => $data['bulan_akhir'],
                        'kd_bkeluar' => $data['nipp'],
                        'klain' => Str::of($data['keterangan'])->trim(),
                        'username' => $user,
                        'tglupdate' => date('Y-m-d H:i:s'),
                        'total' => $data['totalNilai'],
                        'status' => '0',
                        'revisi_ke' => $revisi->revisi,
                        'jns_ang' => $data['jenis_anggaran'],
                        'jns_angkas' => $data['status_angkas'],
                    ]);

                    if ($data['jenis'] == '6') {
                        if (isset($rincian_data)) {
                            // DB::table('trdspd')->insert(array_map(function ($value) use ($data) {
                            //     return [
                            //         'no_spd' => $value['nomor'],
                            //         'kd_program' => kd_Program($value['kd_sub_kegiatan'])->kd_program,
                            //         'nm_program' => kd_Program($value['kd_sub_kegiatan'])->nm_program,
                            //         'kd_kegiatan' => kd_kegiatan($value['kd_sub_kegiatan'])->kd_kegiatan,
                            //         'nm_kegiatan' => kd_kegiatan($value['kd_sub_kegiatan'])->nm_kegiatan,
                            //         'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            //         'nm_sub_kegiatan' => kd_kegiatan($value['kd_sub_kegiatan'])->nm_sub_kegiatan,
                            //         'kd_rek6' => $value['kd_rek6'],
                            //         'nm_rek6' => kd_kegiatan($value['kd_rek6'])->nm_kegiatan,
                            //         'nilai' => $value['nilai'],
                            //         'kd_unit' => $value['kd_skpd'],
                            //     ];
                            // }, $data['daftar_spd']));
                            foreach ($rincian_data as $data => $value) {
                                $data = [
                                    'no_spd' => $rincian_data[$data]['no_spd'],
                                    'kd_program' => kd_Program($rincian_data[$data]['kd_sub_kegiatan'])->kd_progam,
                                    'nm_program' => kd_Program($rincian_data[$data]['kd_sub_kegiatan'])->nm_program,
                                    'kd_kegiatan' => kd_kegiatan($rincian_data[$data]['kd_sub_kegiatan'])->kd_kegiatan,
                                    'nm_kegiatan' => kd_kegiatan($rincian_data[$data]['kd_sub_kegiatan'])->nm_kegiatan,
                                    'kd_sub_kegiatan' => $rincian_data[$data]['kd_sub_kegiatan'],
                                    'nm_sub_kegiatan' => kd_kegiatan($rincian_data[$data]['kd_sub_kegiatan'])->nm_sub_kegiatan,
                                    'kd_rek6' => $rincian_data[$data]['kd_rek6'],
                                    'nm_rek6' => kd_kegiatan($rincian_data[$data]['kd_rek6'])->nm_kegiatan,
                                    'nilai' => $rincian_data[$data]['nilai'],
                                    'kd_unit' => $rincian_data[$data]['kd_skpd'],
                                ];
                                DB::table('trdspd')->insert($data);
                            }
                        }
                    } else {
                        DB::insert("INSERT trdspd
                                    select '$nomor_spd' as no_spd,kd_program,RTRIM(nm_program),left(kd_sub_kegiatan,12),
                                    RTRIM((select nm_kegiatan from ms_kegiatan where left(kd_sub_kegiatan,12)=kd_kegiatan))as nm_kegiatan,
                                    kd_sub_kegiatan,RTRIM(nm_sub_kegiatan),kd_rek6,RTRIM(nm_rek6),nilai,kd_unit
                                     from (
                                    SELECT a.kd_unit, a.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program, a.kd_rek6 , a.nm_rek6, a.total_ubah as anggaran,
                                    b.nilai,c.lalu FROM(

                                     SELECT a.kd_skpd as kd_unit, b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program,b.kd_rek6 , b.nm_rek6 ,
                                        sum(b.nilai) as total_ubah, left(a.kd_skpd,22) kd_skpd FROM trskpd a
                                     inner join trdrka b on a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.kd_skpd=b.kd_skpd
                                     inner join ms_sub_kegiatan c on a.kd_sub_kegiatan=c.kd_sub_kegiatan
                                     WHERE left(b.kd_skpd,22)=left(?,22) and c.jns_sub_kegiatan='5' and a.jns_ang=?
                                    group by left(a.kd_skpd,22), a.kd_skpd,a.kd_program, a.nm_program,b.kd_sub_kegiatan,a.nm_sub_kegiatan,b.kd_rek6,b.nm_rek6

                                     ) a LEFT JOIN (

                                        SELECT kd_sub_kegiatan, b.kd_rek6, left(kd_skpd,22) kd_skpd, kd_skpd as kd_unit, SUM($field_angkas) as nilai FROM trdskpd_ro b
                                        WHERE b.bulan>=? AND b.bulan<=? AND left(kd_skpd,22)=left(?,22)
                                        GROUP BY left(kd_skpd,22),kd_skpd,kd_sub_kegiatan,kd_rek6

                                        )b ON a.kd_unit=b.kd_unit and a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.kd_rek6=b.kd_rek6

                                    LEFT JOIN (

                                     SELECT kd_unit as kd_skpd,kd_sub_kegiatan,kd_rek6,isnull(SUM(a.nilai),0) as lalu FROM trdspd a LEFT JOIN trhspd b
                                    ON a.no_spd=b.no_spd
                                    WHERE left(b.kd_skpd,22)=left(?,22) and a.no_spd != ?
                                    and b.tgl_spd<?
                                    GROUP BY kd_unit,kd_sub_kegiatan,kd_rek6
                                     ) c

                                     ON a.kd_unit=c.kd_skpd and a.kd_sub_kegiatan=c.kd_sub_kegiatan and a.kd_rek6=c.kd_rek6


                                    )xxx

                                    ORDER BY kd_unit,kd_sub_kegiatan", [$data['skpd'], $status1, $data['bulan_awal'], $data['bulan_akhir'], $data['skpd'], $data['skpd'], $nomor_spd, $data['tanggal']]);
                    }
                } else {
                    $nmskpd = DB::table('ms_skpd')->select('nm_skpd')
                        ->where(['kd_skpd' => $data['skpd']])->first();

                    DB::table('trhspd')->insert([
                        'no_spd' => $data['nomor'],
                        'tgl_spd' => $data['tanggal'],
                        'kd_skpd' => $data['skpd'],
                        'nm_skpd' => Str::of($nmskpd->nm_skpd)->trim(),
                        'jns_beban' => $data['jenis'],
                        'bulan_awal' => $data['bulan_awal'],
                        'bulan_akhir' => $data['bulan_akhir'],
                        'kd_bkeluar' => $data['nipp'],
                        'klain' => Str::of($data['keterangan'])->trim(),
                        'username' => $user,
                        'tglupdate' => date('Y-m-d H:i:s'),
                        'total' => $data['totalNilai'],
                        'status' => '0',
                        'revisi_ke' => '0',
                        'jns_ang' => $data['jenis_anggaran'],
                        'jns_angkas' => $data['status_angkas'],
                    ]);

                    $nomor1 = $data['nomor'];

                    if ($data['jenis'] == '6') {
                        if (isset($rincian_data)) {
                            // DB::table('trdspd')->insert(array_map(function ($value) use ($data) {
                            //     return [
                            //         'no_spd' => $data['nomor'],
                            //         'kd_program' => kd_Program($value['kd_sub_kegiatan']),
                            //         'nm_program' => nm_Program($value['kd_sub_kegiatan']),
                            //         'kd_kegiatan' => kd_kegiatan($value['kd_sub_kegiatan']),
                            //         'nm_kegiatan' => nm_kegiatan($value['kd_sub_kegiatan']),
                            //         'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            //         'nm_sub_kegiatan' => kd_sub_kegiatan($value['kd_sub_kegiatan']),
                            //         'kd_rek6' => $value['kd_rek6'],
                            //         'nm_rek6' => kd_rek($value['kd_rek6']),
                            //         'nilai' => $value['nilai'],
                            //         'kd_unit' => $value['kd_skpd'],
                            //     ];
                            // }, $data['daftar_spd']));
                            foreach ($rincian_data as $data => $value) {
                                $data = [
                                    'no_spd' => $nomor1,
                                    'kd_program' => kd_Program($rincian_data[$data]['kd_sub_kegiatan']),
                                    'nm_program' => nm_Program($rincian_data[$data]['kd_sub_kegiatan']),
                                    'kd_kegiatan' => kd_kegiatan($rincian_data[$data]['kd_sub_kegiatan']),
                                    'nm_kegiatan' => nm_kegiatan($rincian_data[$data]['kd_sub_kegiatan']),
                                    'kd_sub_kegiatan' => $rincian_data[$data]['kd_sub_kegiatan'],
                                    'nm_sub_kegiatan' => kd_sub_kegiatan($rincian_data[$data]['kd_sub_kegiatan']),
                                    'kd_rek6' => $rincian_data[$data]['kd_rek6'],
                                    'nm_rek6' => kd_rek($rincian_data[$data]['kd_rek6']),
                                    'nilai' => $rincian_data[$data]['nilai'],
                                    'kd_unit' => $rincian_data[$data]['kd_skpd'],
                                ];
                                DB::table('trdspd')->insert($data);
                            }
                        }
                    } else {
                        DB::insert("INSERT trdspd
                                            select '$nomor_spd' as no_spd,kd_program,RTRIM(nm_program),left(kd_sub_kegiatan,12),
                                            RTRIM((select nm_kegiatan from ms_kegiatan where left(kd_sub_kegiatan,12)=kd_kegiatan))as nm_kegiatan,
                                            kd_sub_kegiatan,RTRIM(nm_sub_kegiatan),kd_rek6,RTRIM(nm_rek6),nilai,kd_unit
                                             from (
                                            SELECT a.kd_unit, a.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program, a.kd_rek6 , a.nm_rek6, a.total_ubah as anggaran,
                                            b.nilai-isnull(lalu_tw,0) as nilai,c.lalu FROM(

                                             SELECT a.kd_skpd as kd_unit, b.kd_sub_kegiatan, a.nm_sub_kegiatan, a.kd_program, a.nm_program,b.kd_rek6 , b.nm_rek6 ,
                                                sum(b.nilai) as total_ubah, left(a.kd_skpd,22) kd_skpd FROM trskpd a
                                             inner join trdrka b on a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.kd_skpd=b.kd_skpd
                                             inner join ms_sub_kegiatan c on a.kd_sub_kegiatan=c.kd_sub_kegiatan
                                             WHERE left(b.kd_skpd,22)=left(?,22) and c.jns_sub_kegiatan='5' and a.jns_ang=?
                                            group by left(a.kd_skpd,22), a.kd_skpd,a.kd_program, a.nm_program,b.kd_sub_kegiatan,a.nm_sub_kegiatan,b.kd_rek6,b.nm_rek6

                                             ) a LEFT JOIN (

                                                SELECT kd_sub_kegiatan, b.kd_rek6, left(kd_skpd,22) kd_skpd, kd_skpd as kd_unit, SUM($field_angkas) as nilai FROM trdskpd_ro b
                                                WHERE b.bulan>=? AND b.bulan<=? AND left(kd_skpd,22)=left(?,22)
                                                GROUP BY left(kd_skpd,22),kd_skpd,kd_sub_kegiatan,kd_rek6

                                                )b ON a.kd_unit=b.kd_unit and a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.kd_rek6=b.kd_rek6

                                            LEFT JOIN (

                                             SELECT kd_unit as kd_skpd,kd_sub_kegiatan,kd_rek6,isnull(SUM(a.nilai),0) as lalu FROM trdspd a LEFT JOIN trhspd b
                                            ON a.no_spd=b.no_spd
                                            WHERE left(b.kd_skpd,22)=left(?,22) and a.no_spd != ?
                                            and b.tgl_spd<?
                                            GROUP BY kd_unit,kd_sub_kegiatan,kd_rek6
                                             ) c

                                             ON a.kd_unit=c.kd_skpd and a.kd_sub_kegiatan=c.kd_sub_kegiatan and a.kd_rek6=c.kd_rek6

                                            LEFT JOIN (

                                             SELECT kd_unit as kd_skpd,kd_sub_kegiatan,kd_rek6,isnull(SUM(a.nilai),0) as lalu_tw FROM trdspd a LEFT JOIN trhspd b ON a.no_spd=b.no_spd
                                            WHERE left(b.kd_skpd,22)=left(?,22) and b.bulan_awal=? AND b.bulan_akhir=? and a.no_spd != ? and b.tgl_spd<?
                                            GROUP BY kd_unit,kd_sub_kegiatan,kd_rek6
                                             ) d

                                             ON a.kd_unit=d.kd_skpd and a.kd_sub_kegiatan=d.kd_sub_kegiatan and a.kd_rek6=d.kd_rek6

                                            )xxx

                                            ORDER BY kd_unit,kd_sub_kegiatan", [$data['skpd'], $status1, $data['bulan_awal'], $data['bulan_akhir'], $data['skpd'], $data['skpd'], $nomor_spd, $data['tanggal'], $data['skpd'], $data['bulan_awal'], $data['bulan_akhir'], $nomor_spd, $data['tanggal']]);
                    }
                }

                $user = Auth()->user()->nama;
                DB::table('spd_temp')->where(['username' => $user])->Delete();

                DB::commit();
                return response()->json([
                    'message' => '1'
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $nospd = $request->no_spd;
        try {
            DB::beginTransaction();
            DB::table('trhspd')->where(['no_spd' => $nospd])->delete();
            DB::table('trdspd')->where(['no_spd' => $nospd])->delete();
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function cetakLamp(Request $request)
    {
        $nospd = $request->no_spd;
        $nip = $request->nip;
        $jsprint = $request->jenis;

        $konfig = DB::table('trkonfig_spd')->first();
        // $jenis = DB::table('trhspd')->where(['no_spd' => $nospd])->first();
        $jenis = DB::table('trhspd as a')
            ->join('trdspd as b', 'a.no_spd', '=', 'b.no_spd')
            ->select('a.*', 'b.kd_rek6')
            ->where(['a.no_spd' => $nospd])->first();
        $no_dpa = DB::table('trhrka')->where(['kd_skpd' => $jenis->kd_skpd, 'jns_ang' => $jenis->jns_ang])->first();
        // $total_anggaran = DB::table('trdrka')
        //     ->whereRaw("left(kd_skpd, 22) = left(?, 22) and left(kd_rek6, 1) = ?", [$jenis->kd_skpd, $jenis->jns_beban])
        //     ->where(['jns_ang' => $jenis->jns_ang])->sum('nilai');

        $ttd = DB::table('ms_ttd')->where(['nip' => $nip])->first();

        // $spd_lalu = array_column(DB::select(
        //     "SELECT TOP (1) WITH TIES no_spd, RANK() OVER (PARTITION BY kd_skpd, bulan_awal, bulan_akhir ORDER BY jns_ang, revisi_ke DESC) AS ranking
        //         FROM trhspd
        //         WHERE kd_skpd = ? AND bulan_awal < ? AND bulan_akhir < ? AND jns_ang <= ?
        //         ORDER BY RANK() OVER (PARTITION BY kd_skpd, bulan_awal, bulan_akhir ORDER BY jns_ang, revisi_ke DESC)",
        //     [$jenis->kd_skpd, $jenis->bulan_awal, $jenis->bulan_akhir, $jenis->jns_ang]
        // ), 'no_spd');

        // if (count($spd_lalu) == 0) $spd_lalu = [null];
        // $arr = implode("','", $spd_lalu);

        // $datalamp = DB::select(
        //     "SELECT * FROM (
        // 		SELECT spd.kd_program as urutan, spd.kd_program AS kode, spd.nm_program AS nama, '' AS kd_rek, '' AS nm_rek, SUM(trdrka.nilai) AS anggaran, SUM(spd.nilai) nilai, SUM(ISNULL(spd_lalu.nilai, 0)) AS nilai_lalu, 'program' AS jenis
        // 		FROM (
        // 			SELECT trhspd.no_spd, kd_skpd, kd_program, nm_program, kd_kegiatan, nm_kegiatan, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, bulan_awal, bulan_akhir, nilai, jns_ang
        // 			FROM trhspd
        // 			JOIN trdspd ON trhspd.no_spd = trdspd.no_spd
        // 			WHERE trhspd.no_spd = '$nospd'
        // 		) spd
        // 		LEFT JOIN (
        // 			SELECT h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6, SUM(d.nilai) AS nilai FROM trhspd h
        // 			JOIN trdspd d ON h.no_spd = d.no_spd
        // 			WHERE h.no_spd IN ('$arr')
        // 			GROUP BY h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6
        // 		) spd_lalu
        // 		ON left(spd.kd_skpd, 22) = left(spd_lalu.kd_skpd, 22) AND spd.kd_sub_kegiatan = spd_lalu.kd_sub_kegiatan AND spd.kd_rek6 = spd_lalu.kd_rek6
        // 		JOIN trdrka ON left(trdrka.kd_skpd, 22) = left(spd.kd_skpd, 22) AND trdrka.kd_sub_kegiatan = spd.kd_sub_kegiatan AND trdrka.kd_rek6 = spd.kd_rek6 AND trdrka.jns_ang = spd.jns_ang
        // 		GROUP BY spd.kd_program, spd.nm_program, spd.kd_skpd

        // 		UNION ALL

        // 		SELECT spd.kd_program+'.'+spd.kd_kegiatan as urutan,  spd.kd_kegiatan AS kode, spd.nm_kegiatan AS nama, '' AS kd_rek, '' AS nm_rek, SUM(trdrka.nilai) AS anggaran, SUM(spd.nilai) nilai, SUM(ISNULL(spd_lalu.nilai, 0)) AS nilai_lalu, 'kegiatan' AS jenis
        // 		FROM (
        // 			SELECT trhspd.no_spd, kd_skpd, kd_program, nm_program, kd_kegiatan, nm_kegiatan, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, bulan_awal, bulan_akhir, nilai, jns_ang
        // 			FROM trhspd
        // 			JOIN trdspd ON trhspd.no_spd = trdspd.no_spd
        // 			WHERE trhspd.no_spd = '$nospd'
        // 		) spd
        // 		LEFT JOIN (
        // 			SELECT h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6, SUM(d.nilai) AS nilai FROM trhspd h
        // 			JOIN trdspd d ON h.no_spd = d.no_spd
        // 			WHERE h.no_spd in ('$arr')
        // 			GROUP BY h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6
        // 		) spd_lalu
        // 		ON left(spd.kd_skpd, 22) = left(spd_lalu.kd_skpd, 22) AND spd.kd_sub_kegiatan = spd_lalu.kd_sub_kegiatan AND spd.kd_rek6 = spd_lalu.kd_rek6
        // 		JOIN trdrka ON left(trdrka.kd_skpd, 22) = left(spd.kd_skpd, 22) AND trdrka.kd_sub_kegiatan = spd.kd_sub_kegiatan AND trdrka.kd_rek6 = spd.kd_rek6 AND trdrka.jns_ang = spd.jns_ang
        // 		GROUP BY spd.kd_kegiatan, spd.nm_kegiatan, spd.kd_skpd, spd.kd_program

        // 		UNION ALL

        // 		SELECT spd.kd_program+'.'+spd.kd_kegiatan+'.'+spd.kd_sub_kegiatan as urutan, spd.kd_sub_kegiatan AS kode, spd.nm_sub_kegiatan AS nama, '' AS kd_rek, '' AS nm_rek, SUM(trdrka.nilai) AS anggaran, SUM(spd.nilai) nilai, SUM(ISNULL(spd_lalu.nilai, 0)) AS nilai_lalu, 'sub_kegiatan' AS jenis
        // 		FROM (
        // 			SELECT trhspd.no_spd, kd_skpd, kd_program, nm_program, kd_kegiatan, nm_kegiatan, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, bulan_awal, bulan_akhir, nilai, jns_ang
        // 			FROM trhspd
        // 			JOIN trdspd ON trhspd.no_spd = trdspd.no_spd
        // 			WHERE trhspd.no_spd = '$nospd'
        // 		) spd
        // 		LEFT JOIN (
        // 			SELECT h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6, SUM(d.nilai) AS nilai FROM trhspd h
        // 			JOIN trdspd d ON h.no_spd = d.no_spd
        // 			WHERE h.no_spd in ('$arr')
        // 			GROUP BY h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6
        // 		) spd_lalu
        // 		ON left(spd.kd_skpd, 22) = left(spd_lalu.kd_skpd, 22) AND spd.kd_sub_kegiatan = spd_lalu.kd_sub_kegiatan AND spd.kd_rek6 = spd_lalu.kd_rek6
        // 		JOIN trdrka ON left(trdrka.kd_skpd, 22) = left(spd.kd_skpd,22) AND trdrka.kd_sub_kegiatan = spd.kd_sub_kegiatan AND trdrka.kd_rek6 = spd.kd_rek6 AND trdrka.jns_ang = spd.jns_ang
        // 		GROUP BY spd.kd_sub_kegiatan, spd.nm_sub_kegiatan, spd.kd_skpd, spd.kd_program, spd.kd_kegiatan

        // 		UNION ALL

        // 		SELECT  spd.kd_program+'.'+spd.kd_kegiatan+'.'+spd.kd_sub_kegiatan+'.'+spd.kd_rek6 as urutan, spd.kd_sub_kegiatan AS kode, spd.nm_sub_kegiatan AS nama, spd.kd_rek6 AS kd_rek, spd.nm_rek6 AS nm_rek, SUM(trdrka.nilai) AS anggaran, SUM(spd.nilai) nilai, SUM(ISNULL(spd_lalu.nilai, 0)) AS nilai_lalu, 'rekening' AS jenis
        // 		FROM (
        // 			SELECT trhspd.no_spd, kd_skpd, kd_program, nm_program, kd_kegiatan, nm_kegiatan, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, bulan_awal, bulan_akhir, nilai, jns_ang
        // 			FROM trhspd
        // 			JOIN trdspd ON trhspd.no_spd = trdspd.no_spd
        // 			WHERE trhspd.no_spd = '$nospd'
        // 		) spd
        // 		LEFT JOIN (
        // 			SELECT h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6, SUM(d.nilai) AS nilai FROM trhspd h
        // 			JOIN trdspd d ON h.no_spd = d.no_spd
        // 			WHERE h.no_spd in ('$arr')
        // 			GROUP BY h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6
        // 		) spd_lalu
        // 		ON left(spd.kd_skpd, 22) = left(spd_lalu.kd_skpd, 22) AND spd.kd_sub_kegiatan = spd_lalu.kd_sub_kegiatan AND spd.kd_rek6 = spd_lalu.kd_rek6
        // 		JOIN trdrka ON left(trdrka.kd_skpd, 22) = left(spd.kd_skpd, 22) AND trdrka.kd_sub_kegiatan = spd.kd_sub_kegiatan AND trdrka.kd_rek6 = spd.kd_rek6 AND trdrka.jns_ang = spd.jns_ang
        // 		GROUP BY spd.kd_sub_kegiatan, spd.nm_sub_kegiatan, spd.kd_skpd, spd.kd_rek6, spd.nm_rek6, spd.kd_program, spd.kd_kegiatan
        // 	) spd ORDER BY urutan"
        // );

        $kd_skpd = $jenis->kd_skpd;
        $tgl_spd = $jenis->tgl_spd;
        $bulan_akhir = $jenis->bulan_akhir;
        $jns_beban = $jenis->jns_beban;
        $jns_ang = $jenis->jns_ang;

        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '3', '1', $tgl_spd]))->first();

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '6', '1', $tgl_spd]))->first();

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '9', '1', $tgl_spd]))->first();

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '12', '1', $tgl_spd]))->first();
        // return [$revisi1, $revisi2, $revisi3, $revisi4];
        if ($bulan_akhir == '3') {
            $spdlalu = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu6 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        )zz";

            $spdlalu2 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and b.status = '1'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu26 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='5'
        and b.status = '1'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        )zz";

            $spdlalu3 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
                    AND b.jns_beban='5'
                    and b.status = '1'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu36 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
        AND b.jns_beban='6'
        and b.status = '1'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        )zz";
            $spdlalu4 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='5'
                    and b.status = '1'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                )zz";
            $spdlalu46 = "SELECT sum(nilai)as jm_spd_l from (
            SELECT
            'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
            FROM
            trdspd a
            JOIN trhspd b ON a.no_spd = b.no_spd
            WHERE
            left(a.kd_unit,22) = left('$kd_skpd',22)
            AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
            AND a.kd_rek6=z.kd_rek6
            AND b.jns_beban='6'
            and b.status = '1'
            and bulan_akhir<='$bulan_akhir'
            and bulan_awal='1'
            and revisi_ke='$revisi1->revisi'
            and tgl_spd<='$tgl_spd'
            and a.no_spd<>'$nospd'
        )zz";
        } else if ($bulan_akhir == '6') {
            $spdlalu = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and b.status = '1'
                    and revisi_ke='$revisi2->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu6 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and revisi_ke='$revisi2->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        )zz";
            $spdlalu2 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and b.status = '1'
                    and revisi_ke='$revisi2->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu26 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and revisi_ke='$revisi2->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        )zz";
            $spdlalu3 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and revisi_ke='$revisi2->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu36 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and revisi_ke='$revisi2->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        )zz";
            $spdlalu4 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and revisi_ke='$revisi2->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                )zz";
            $spdlalu46 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='6'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='6'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and revisi_ke='$revisi2->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                )zz";
        } else if ($bulan_akhir == '9') {
            $spdlalu = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and revisi_ke='$revisi2->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='7'
                    and revisi_ke='$revisi3->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                )zz";
            $spdlalu6 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and revisi_ke='$revisi2->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='7'
        and revisi_ke='$revisi3->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
    )zz";
            $spdlalu2 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and revisi_ke='$revisi2->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='7'
                    and revisi_ke='$revisi3->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                )zz";
            $spdlalu26 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and revisi_ke='$revisi2->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='7'
        and revisi_ke='$revisi3->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
    )zz";
            $spdlalu3 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and revisi_ke='$revisi2->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='7'
                    and revisi_ke='$revisi3->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                )zz";
            $spdlalu36 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and revisi_ke='$revisi2->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,17)=left(z.kd_sub_kegiatan,17)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='7'
        and revisi_ke='$revisi3->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
    )zz";
            $spdlalu4 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and revisi_ke='$revisi1->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and revisi_ke='$revisi2->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='7'
                    and revisi_ke='$revisi3->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                )zz";
            $spdlalu46 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND a.kd_rek6=z.kd_rek6
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND a.kd_rek6=z.kd_rek6
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and revisi_ke='$revisi2->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND a.kd_rek6=z.kd_rek6
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='7'
        and revisi_ke='$revisi3->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
    )zz";
        } else {
            $spdlalu = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and b.status = '1'
                    and revisi_ke='$revisi1->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and b.status = '1'
                    and revisi_ke='$revisi2->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='7'
                    and b.status = '1'
                    and revisi_ke='$revisi3->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='10'
                    and b.status = '1'
                    and revisi_ke='$revisi4->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu6 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and b.status = '1'
        and revisi_ke='$revisi1->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and b.status = '1'
        and revisi_ke='$revisi2->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='7'
        and revisi_ke='$revisi3->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,7)=left(z.kd_sub_kegiatan,7)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='10'
        and b.status = '1'
        and revisi_ke='$revisi4->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        )zz";
            $spdlalu2 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and b.status = '1'
                    and revisi_ke='$revisi1->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and b.status = '1'
                    and revisi_ke='$revisi2->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='7'
                    and revisi_ke='$revisi3->revisi'
                    and b.status = '1'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='10'
                    and b.status = '1'
                    and revisi_ke='$revisi4->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu26 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and b.status = '1'
        and revisi_ke='$revisi1->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and b.status = '1'
        and revisi_ke='$revisi2->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='7'
        and revisi_ke='$revisi3->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND left(a.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='10'
        and b.status = '1'
        and revisi_ke='$revisi4->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        )zz";
            $spdlalu3 = "SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and b.status = '1'
                    and revisi_ke='$revisi1->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and b.status = '1'
                    and revisi_ke='$revisi2->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='7'
                    and b.status = '1'
                    and revisi_ke='$revisi3->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='10'
                    and b.status = '1'
                    and revisi_ke='$revisi4->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu36 = "SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and b.status = '1'
        and revisi_ke='$revisi1->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and revisi_ke='$revisi2->revisi'
        and b.status = '1'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='7'
        and b.status = '1'
        and revisi_ke='$revisi3->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND b.jns_beban='6'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='10'
        and b.status = '1'
        and revisi_ke='$revisi4->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        )zz";
            $spdlalu4 = "     SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='1'
                    and b.status = '1'
                    and revisi_ke='$revisi1->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='5'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='4'
                    and b.status = '1'
                    and revisi_ke='$revisi2->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left('$kd_skpd',22)
                    AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    AND a.kd_rek6=z.kd_rek6
                    AND b.jns_beban='5'
                    and b.status = '1'
                    and bulan_akhir<='$bulan_akhir'
                    and bulan_awal='7'
                    and revisi_ke='$revisi3->revisi'
                    and tgl_spd<='$tgl_spd'
                    and a.no_spd<>'$nospd'
                    -- UNION ALL
                    -- SELECT
                    -- 'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
                    -- FROM
                    -- trdspd a
                    -- JOIN trhspd b ON a.no_spd = b.no_spd
                    -- WHERE
                    -- left(a.kd_unit,22) = left('$kd_skpd',22)
                    -- AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
                    -- AND a.kd_rek6=z.kd_rek6
                    -- AND b.jns_beban='5'
                    -- AND b.status = '1'
                    -- and bulan_akhir<='$bulan_akhir'
                    -- and bulan_awal='10'
                    -- and revisi_ke='$revisi4->revisi'
                    -- -- and tgl_spd<='$tgl_spd'
                    -- and a.no_spd<>'$nospd'
                    )zz";
            $spdlalu46 = "     SELECT sum(nilai)as jm_spd_l from (
        SELECT
        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND a.kd_rek6=z.kd_rek6
        AND b.jns_beban='6'
        and b.status = '1'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='1'
        and revisi_ke='$revisi1->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND a.kd_rek6=z.kd_rek6
        AND b.jns_beban='6'
        and b.status = '1'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='4'
        and revisi_ke='$revisi2->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        UNION ALL
        SELECT
        'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
        FROM
        trdspd a
        JOIN trhspd b ON a.no_spd = b.no_spd
        WHERE
        left(a.kd_unit,22) = left('$kd_skpd',22)
        AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        AND a.kd_rek6=z.kd_rek6
        AND b.jns_beban='6'
        and b.status = '1'
        and bulan_akhir<='$bulan_akhir'
        and bulan_awal='7'
        and revisi_ke='$revisi3->revisi'
        and tgl_spd<='$tgl_spd'
        and a.no_spd<>'$nospd'
        -- UNION ALL
        -- SELECT
        -- 'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
        -- FROM
        -- trdspd a
        -- JOIN trhspd b ON a.no_spd = b.no_spd
        -- WHERE
        -- left(a.kd_unit,22) = left('$kd_skpd',22)
        -- AND a.kd_sub_kegiatan=z.kd_sub_kegiatan
        -- AND a.kd_rek6=z.kd_rek6
        -- AND b.jns_beban='5'
        -- AND b.status = '1'
        -- and bulan_akhir<='$bulan_akhir'
        -- and bulan_awal='10'
        -- and revisi_ke='$revisi4->revisi'
        -- -- and tgl_spd<='$tgl_spd'
        -- and a.no_spd<>'$nospd'
        )zz";
        }

        if ($jns_beban == '5') {
            $datalamp = DB::select("SELECT
left(kd_skpd,22)+left(kd_sub_kegiatan,7) as no_urut,
left(kd_skpd,22) kd_skpd,
left(kd_sub_kegiatan,7)as kode,
left(kd_sub_kegiatan,7)as kode1,
(select nm_program from ms_program a where a.kd_program= left(z.kd_sub_kegiatan,7))as uraian,
isnull(sum(nilai),0)as anggaran,

(

    $spdlalu


)as spd_lalu,

(select isnull(sum(e.nilai),0) from trhspd d inner join trdspd e on d.no_spd=e.no_spd where
d.no_spd='$nospd' and e.kd_program=left(z.kd_sub_kegiatan,7)
and left(e.kd_unit,22)=left(z.kd_skpd,22) and jns_beban='5')as nilai
from trdrka z where left(kd_rek6,1)='5' and  left(kd_skpd,22)=left('$kd_skpd',22) and jns_ang='$jns_ang'


group by left(kd_skpd,22),left(kd_sub_kegiatan,7)

UNION ALL

select
left(kd_skpd,22)+left(kd_sub_kegiatan,12) as no_urut,
left(kd_skpd,22),
left(kd_sub_kegiatan,12)as kd_kegiatan,
left(kd_sub_kegiatan,12)as kode1,
(select nm_kegiatan from ms_kegiatan a where a.kd_kegiatan= left(z.kd_sub_kegiatan,12))as uraian,
isnull(sum(nilai),0)as anggaran,
(

    $spdlalu2


)as spd_lalu,
(select isnull(sum(e.nilai),0) from trhspd d inner join trdspd e on d.no_spd=e.no_spd where
d.no_spd='$nospd' and left(e.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
and left(e.kd_unit,22)=left(z.kd_skpd,22) and jns_beban='5')as nilai
from trdrka z where left(kd_rek6,1)='5' and  left(kd_skpd,22)=left('$kd_skpd',22) and jns_ang='$jns_ang'
group by left(kd_skpd,22),left(kd_sub_kegiatan,12)


UNION ALL

select
left(kd_skpd,22)+kd_sub_kegiatan as no_urut,
left(kd_skpd,22) kd_skpd,
kd_sub_kegiatan as kd_kegiatan,
kd_sub_kegiatan as kode1,
nm_sub_kegiatan as uraian,
isnull(sum(nilai),0)as anggaran,
(

 $spdlalu3


)as spd_lalu,
(select isnull(sum(e.nilai),0) from trhspd d inner join trdspd e on d.no_spd=e.no_spd where
d.no_spd='$nospd' and e.kd_sub_kegiatan=z.kd_sub_kegiatan
and left(e.kd_unit,22)=left(z.kd_skpd,22) and jns_beban='5')as nilai
from trdrka z where left(kd_rek6,1)='5' and  left(kd_skpd,22)=left('$kd_skpd',22) and jns_ang='$jns_ang'
group by left(kd_skpd,22),kd_sub_kegiatan,nm_sub_kegiatan

UNION ALL

select
left(kd_skpd,22)+kd_sub_kegiatan+kd_rek6 as no_urut,
left(kd_skpd,22),
kd_rek6 as kd_kegiatan,
kd_sub_kegiatan as kode ,
nm_rek6 as uraian,
isnull(sum(nilai),0)as anggaran,

(
$spdlalu4


)as spd_lalu,

(select isnull(sum(e.nilai),0) from trhspd d inner join trdspd e on d.no_spd=e.no_spd where
d.no_spd='$nospd'
and e.kd_sub_kegiatan=z.kd_sub_kegiatan and e.kd_rek6=z.kd_rek6
and left(e.kd_unit,22)=left(z.kd_skpd,22) and jns_beban='5')as nilai
from trdrka z where left(kd_rek6,1)='5' and  left(kd_skpd,22)=left('$kd_skpd',22) and jns_ang='$jns_ang'
group by left(kd_skpd,22),kd_sub_kegiatan,kd_rek6,nm_rek6



order by no_urut");
        } else {
            $datalamp = DB::select("SELECT
left(kd_skpd,22)+left(kd_sub_kegiatan,7) as no_urut,
left(kd_skpd,22) kd_skpd,
left(kd_sub_kegiatan,7)as kode,
left(kd_sub_kegiatan,7)as kode1,
(select nm_program from ms_program a where a.kd_program= left(z.kd_sub_kegiatan,7))as uraian,
isnull(sum(nilai),0)as anggaran,

(

    $spdlalu6


)as spd_lalu,

(select isnull(sum(e.nilai),0) from trhspd d inner join trdspd e on d.no_spd=e.no_spd where
d.no_spd='$nospd' and e.kd_program=left(z.kd_sub_kegiatan,7)
and left(e.kd_unit,22)=left(z.kd_skpd,22) and jns_beban='6')as nilai
from trdrka z where left(kd_rek6,2)='62' and  left(kd_skpd,22)=left('$kd_skpd',22) and jns_ang='$jns_ang'
group by left(kd_skpd,22),left(kd_sub_kegiatan,7)

UNION ALL

select
left(kd_skpd,22)+left(kd_sub_kegiatan,12) as no_urut,
left(kd_skpd,22),
left(kd_sub_kegiatan,12)as kd_kegiatan,
left(kd_sub_kegiatan,12)as kode1,
(select nm_kegiatan from ms_kegiatan a where a.kd_kegiatan= left(z.kd_sub_kegiatan,12))as uraian,
isnull(sum(nilai),0)as anggaran,
(

    $spdlalu26


)as spd_lalu,
(select isnull(sum(e.nilai),0) from trhspd d inner join trdspd e on d.no_spd=e.no_spd where
d.no_spd='$nospd' and left(e.kd_sub_kegiatan,12)=left(z.kd_sub_kegiatan,12)
and left(e.kd_unit,22)=left(z.kd_skpd,22) and jns_beban='6')as nilai
from trdrka z where left(kd_rek6,2)='62' and  left(kd_skpd,22)=left('$kd_skpd',22) and jns_ang='$jns_ang'
group by left(kd_skpd,22),left(kd_sub_kegiatan,12)


UNION ALL

select
left(kd_skpd,22)+kd_sub_kegiatan as no_urut,
left(kd_skpd,22) kd_skpd,
kd_sub_kegiatan as kd_kegiatan,
kd_sub_kegiatan as kode1,
nm_sub_kegiatan as uraian,
isnull(sum(nilai),0)as anggaran,
(

 $spdlalu36


)as spd_lalu,
(select isnull(sum(e.nilai),0) from trhspd d inner join trdspd e on d.no_spd=e.no_spd where
d.no_spd='$nospd' and e.kd_sub_kegiatan=z.kd_sub_kegiatan
and left(e.kd_unit,22)=left(z.kd_skpd,22) and jns_beban='6')as nilai
from trdrka z where left(kd_rek6,2)='62' and  left(kd_skpd,22)=left('$kd_skpd',22) and jns_ang='$jns_ang'
group by left(kd_skpd,22),kd_sub_kegiatan,nm_sub_kegiatan

UNION ALL

select
left(kd_skpd,22)+kd_sub_kegiatan+kd_rek6 as no_urut,
left(kd_skpd,22),
kd_rek6 as kd_kegiatan,
kd_sub_kegiatan as kode ,
nm_rek6 as uraian,
isnull(sum(nilai),0)as anggaran,

(
$spdlalu46


)as spd_lalu,

(select isnull(sum(e.nilai),0) from trhspd d inner join trdspd e on d.no_spd=e.no_spd where
d.no_spd='$nospd'
and e.kd_sub_kegiatan=z.kd_sub_kegiatan and e.kd_rek6=z.kd_rek6
and left(e.kd_unit,22)=left(z.kd_skpd,22) and jns_beban='6')as nilai
from trdrka z where left(kd_rek6,2)='62' and  left(kd_skpd,22)=left('$kd_skpd',22) and jns_ang='$jns_ang'
group by left(kd_skpd,22),kd_sub_kegiatan,kd_rek6,nm_rek6



order by no_urut");
        }

        //elvara
        // karena kd_skpd lenght nya 22 itu total anggarannya pakai query ini
        // klo pakai dari datalamp itu kd_skpdnya harus 17
        // cuman ketika pakai kd_skpd 17 itu lamp spd rsud dan dinkes tergabung

        $total_spd2 = DB::table('trhspd as a')
        ->join('trdspd as b', 'a.no_spd', '=', 'b.no_spd')
        ->where('a.no_spd', $nospd)
        ->sum('b.nilai');

        if ($jenis->jns_beban == '5') {
            $total_anggaran = DB::table('trdrka')
            ->whereRaw("left(kd_skpd, 22) = left(?, 22) and left(kd_rek6, 1) = ?", [$jenis->kd_skpd, $jenis->jns_beban])
            ->where(['jns_ang' => $jenis->jns_ang])->sum('nilai');
        } else {
            if (substr($jenis->kd_rek6, 0, 2) == '62') {
                $total_anggaran = DB::table('trdrka')
                    ->whereRaw("left(kd_skpd, 22) = left(?, 22) and left(kd_rek6, 2) = ?", [$jenis->kd_skpd, '62'])
                    ->where(['jns_ang' => $jenis->jns_ang])->sum('nilai');
            } else {
                $total_anggaran = DB::table('trdrka')
                    ->whereRaw("left(kd_skpd, 22) = left(?, 22) and left(kd_rek6, 2) = ?", [$jenis->kd_skpd, '61'])
                    ->where(['jns_ang' => $jenis->jns_ang])->sum('nilai');
            }
        }

        $tw = collect(DB::select("SELECT bulan_akhir FROM trhspd WHERE no_spd=?", [$nospd]))->first();

        $tw = $tw->bulan_akhir;

        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '3', '1', $tgl_spd]))->first();

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '6', '1', $tgl_spd]))->first();

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '9', '1', $tgl_spd]))->first();

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '12', '1', $tgl_spd]))->first();

        switch ($tw) {
            case '3':
                $total_spd_lalu2 = collect(DB::select("SELECT sum(nilai)as jm_spd_l from (
                        SELECT
                        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                        FROM
                        trdspd a
                        JOIN trhspd b ON a.no_spd = b.no_spd
                        WHERE
                        left(a.kd_unit,22) = left(?,22)
                        AND b.jns_beban=?
                        AND b.status = ?
                        and bulan_akhir=?
                        and revisi_ke=?
                        and tgl_spd<=?
                        and a.no_spd<>?
                    )zz", [$kd_skpd, $jns_beban, '1', '3', $revisi1->revisi, $tgl_spd, $nospd]))->first()->jm_spd_l;
                break;
            case '6':
                $total_spd_lalu2 = collect(DB::select("SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status =?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    )zz", [$kd_skpd, $jns_beban, '1', '3', $revisi1->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '6', $revisi2->revisi, $tgl_spd, $nospd]))->first()->jm_spd_l;
                break;
            case '9':
                $total_spd_lalu2 = collect(DB::select("SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    )zz", [$kd_skpd, $jns_beban, '1', '3', $revisi1->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '6', $revisi2->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '9', $revisi3->revisi, $tgl_spd, $nospd]))->first()->jm_spd_l;
                break;
            case '12':
                $total_spd_lalu2 = collect(DB::select("SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd

                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?

                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?

                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?

                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?

                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    )zz", [$kd_skpd, $jns_beban, '1', '3', $revisi1->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '6', $revisi2->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '9', $revisi3->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '12', $revisi4->revisi, $tgl_spd, $nospd]))->first()->jm_spd_l;
                break;
        }

                    // end elvara


        $view = view('penatausahaan.spd.spd_belanja.cetak.cetak-lampiran', array(
            'jsprint' => $jsprint,
            'nospd' => $nospd,
            'konfig' => $konfig,
            'jns_beban' => $jns_beban,
            'no_dpa' => $no_dpa,
            'datalamp' => $datalamp,
            'data' => $jenis,
            'ttd' => $ttd,
            'spasi' => $request->spasi,
            'total_anggaran2' => $total_anggaran,
            'total_spd2' => $total_spd2,
            'total_spd_lalu2' => $total_spd_lalu2
        ));
        if ($jsprint == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('page-width', 215)
                ->setOption('page-height', 330)
                ->setOption('margin-top', $request->atas)
                ->setOption('margin-bottom', $request->bawah)
                ->setOption('margin-right', $request->kanan)
                ->setOption('margin-left', $request->kiri)
                ->setOption('footer-right', "Halaman [page]")
                ->setOption('footer-font-size', 9)
                ->setOption('footer-line', true);
            return $pdf->stream('laporan.pdf');
        } else if ($request->jenis == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    public function cetakOto(Request $request)
    {
        $nospd = $request->no_spd;
        $nip = $request->nip;
        $tambahan = $request->tambahan;
        $jenispr = $request->jenis;
        $jns_ang = $request->jns_ang;

        $tambahanbln = $tambahan ? "Tambahan" : "";

        $jenis = DB::table('trhspd as a')
            ->join('trdspd as b', 'a.no_spd', '=', 'b.no_spd')
            ->select('a.*', 'b.kd_rek6')
            ->where(['a.no_spd' => $nospd])->first();

            $total_ingat = count_ingat($jenis->jns_ang);

            $konfig = DB::table('trkonfig_spd')
                ->where('jns_ang', $jenis->jns_ang)
                ->first();

            $no_dpa = DB::table('trhrka')
                ->where(['kd_skpd' => $jenis->kd_skpd, 'jns_ang' => $jenis->jns_ang])
                ->first();

            $kepala_skpd = DB::table('ms_ttd')->where(['nip' => $jenis->kd_bkeluar])->first();

        if ($jenis->jns_beban == '5') {
            $total_anggaran = DB::table('trdrka')
                ->whereRaw("left(kd_skpd, 22) = left(?, 22) and left(kd_rek6, 1) = ?", [$jenis->kd_skpd, $jenis->jns_beban])
                ->where(['jns_ang' => $jenis->jns_ang])->sum('nilai');
        } else {
            if (substr($jenis->kd_rek6, 0, 2) == '62') {
                $total_anggaran = DB::table('trdrka')
                    ->whereRaw("left(kd_skpd, 22) = left(?, 22) and left(kd_rek6, 2) = ?", [$jenis->kd_skpd, '62'])
                    ->where(['jns_ang' => $jenis->jns_ang])->sum('nilai');
            } else {
                $total_anggaran = DB::table('trdrka')
                    ->whereRaw("left(kd_skpd, 22) = left(?, 22) and left(kd_rek6, 2) = ?", [$jenis->kd_skpd, '61'])
                    ->where(['jns_ang' => $jenis->jns_ang])->sum('nilai');
            }
        }

        // $spd_lalu = array_column(DB::select(
        //     "SELECT TOP (1) WITH TIES no_spd, RANK() OVER (PARTITION BY kd_skpd, bulan_awal, bulan_akhir ORDER BY jns_ang, revisi_ke DESC) AS ranking
        // 	        FROM trhspd
        // 	        WHERE kd_skpd = ? AND bulan_awal < ? AND bulan_akhir < ? AND jns_ang = ?
        // 	        ORDER BY RANK() OVER (PARTITION BY kd_skpd, bulan_awal, bulan_akhir ORDER BY jns_ang, revisi_ke DESC)",
        //     [$jenis->kd_skpd, $jenis->bulan_awal, $jenis->bulan_akhir, $jenis->jns_ang]
        // ), 'no_spd');
        // if (count($spd_lalu) == 0) $spd_lalu = [null];

        // $total_spd_lalu = DB::table('trhspd')->wherein('no_spd', $spd_lalu)->sum('total');

        $kd_skpd = $jenis->kd_skpd;
        $tgl_spd = $jenis->tgl_spd;
        $jns_beban = $jenis->jns_beban;

        $tw = collect(DB::select("SELECT bulan_akhir FROM trhspd WHERE no_spd=?", [$nospd]))->first();

        $tw = $tw->bulan_akhir;

        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '3', '1', $tgl_spd]))->first();

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '6', '1', $tgl_spd]))->first();

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '9', '1', $tgl_spd]))->first();

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                kd_skpd=?
                                and bulan_akhir=? and [status] =?
                                and tgl_spd<=?", [$kd_skpd, '12', '1', $tgl_spd]))->first();

        switch ($tw) {
            case '3':
                $total_spd_lalu = collect(DB::select("SELECT sum(nilai)as jm_spd_l from (
                        SELECT
                        'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                        FROM
                        trdspd a
                        JOIN trhspd b ON a.no_spd = b.no_spd
                        WHERE
                        left(a.kd_unit,22) = left(?,22)
                        AND b.jns_beban=?
                        AND b.status = ?
                        and bulan_akhir=?
                        and revisi_ke=?
                        and tgl_spd<=?
                        and a.no_spd<>?
                    )zz", [$kd_skpd, $jns_beban, '1', '3', $revisi1->revisi, $tgl_spd, $nospd]))->first()->jm_spd_l;
                break;
            case '6':
                $total_spd_lalu = collect(DB::select("SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status =?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    )zz", [$kd_skpd, $jns_beban, '1', '3', $revisi1->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '6', $revisi2->revisi, $tgl_spd, $nospd]))->first()->jm_spd_l;
                break;
            case '9':
                $total_spd_lalu = collect(DB::select("SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?
                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    )zz", [$kd_skpd, $jns_beban, '1', '3', $revisi1->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '6', $revisi2->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '9', $revisi3->revisi, $tgl_spd, $nospd]))->first()->jm_spd_l;
                break;
            case '12':
                $total_spd_lalu = collect(DB::select("SELECT sum(nilai)as jm_spd_l from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd

                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?

                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?

                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?

                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    UNION ALL
                    SELECT
                    'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    left(a.kd_unit,22) = left(?,22)
                    AND b.jns_beban=?
                    AND b.status = ?

                    and bulan_akhir=?
                    and revisi_ke=?
                    and tgl_spd<=?
                    and a.no_spd<>?
                    )zz", [$kd_skpd, $jns_beban, '1', '3', $revisi1->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '6', $revisi2->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '9', $revisi3->revisi, $tgl_spd, $nospd, $kd_skpd, $jns_beban, '1', '12', $revisi4->revisi, $tgl_spd, $nospd]))->first()->jm_spd_l;
                break;
        }

        $ttd = DB::table('ms_ttd')->where(['nip' => $nip])->first();

        $total2 =  DB::table('trdspd as a')
        ->join('trhspd as b', function ($join) {
            $join->on('a.no_spd', '=', 'b.no_spd');
        })
        ->selectRaw("sum(nilai) as nilai")
        ->where(['a.no_spd' => $nospd])
        ->first()
        ->nilai;
        $hasil = round($total_anggaran - ($total_spd_lalu + $total2), 2);
        // dd ($hasil);

        $view = view('penatausahaan.spd.spd_belanja.cetak.cetak-otori', array(
            'jenispr' => $jenispr,
            'jns_ang' => $jenis->jns_ang,
            'nospd' => $nospd,
            'total_ingat' => $total_ingat,
            'konfig' => $konfig,
            'no_dpa' => $no_dpa,
            'data' => $jenis,
            'total' => DB::table('trdspd as a')
                ->join('trhspd as b', function ($join) {
                    $join->on('a.no_spd', '=', 'b.no_spd');
                })
                ->selectRaw("sum(nilai) as nilai")
                ->where(['a.no_spd' => $nospd])
                ->first()
                ->nilai,
            'ttd' => $ttd,
            'total_spd_lalu' => $total_spd_lalu,
            'tambahanbln' => $tambahanbln,
            'total_anggaran' => $total_anggaran,
            'kepala_skpd' => $kepala_skpd,
            'jenis' => $jenis->jns_beban == 5 ? 'Belanja' : 'Pembiayaan',
            'hasil' => $hasil,
        ));

        if ($jenispr == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('page-width', 215)
                ->setOption('page-height', 330)
                ->setOption('margin-top', $request->atas)
                ->setOption('margin-bottom', $request->bawah)
                ->setOption('margin-right', $request->kanan)
                ->setOption('margin-left', $request->kiri);
            // ->setOption('header-font-name',  'Arial')
            // ->setOption('header-font-size',  6);
            return $pdf->stream('laporan.pdf');
        } else if ($request->jenis == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    public function tampilspdBP($no_spd)
    {
        $no_spd = Crypt::decryptString($no_spd);
        $data_spd = DB::table('trhspd')->where(['no_spd' => $no_spd])->first();
        $data = [
            'dataspd' => $data_spd,
            //'nm_bend' => DB::table('ms_ttd')->select('nama')->where(['nip' => $data_spd->kd_bkeluar])->first(),
            'nm_bend' => Collect(DB::select("select nama from ms_ttd where trim(nip) = ?",[ $data_spd->kd_bkeluar]))->first(),
        ];
        return view('penatausahaan.spd.spd_belanja.show')->with($data);
    }

    public function ShowloadData(Request $request)
    {
        $noSpd = $request->no_spd;
        $data = DB::table('trdspd')->where(['no_spd' => $noSpd])->get();
        return DataTables::of($data)->addIndexColumn()->make(true);
        return view('penatausahaan.spd.spd_belanja.show');
    }

    public function cekSkpd(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jns_ang = $request->jns_ang;

        $total_skpd = collect(DB::select("SELECT COUNT(*) as total FROM ms_skpd where left(kd_skpd,22)=left(?,22)", [$kd_skpd]))->first();

        $total_angkas = collect(DB::select("SELECT count(*) as total FROM  (SELECT kd_skpd FROM trdrka where jns_ang=? and left(kd_skpd,22)=left(?,22) GROUP BY kd_skpd)z", [$jns_ang, $kd_skpd]))->first();

        $nama_anggaran = DB::table('tb_status_anggaran')
            ->select('nama')
            ->where(['kode' => $jns_ang])
            ->first();

        if ($total_skpd->total != $total_angkas->total) {
            return response()->json([
                'message' => '0',
                'nama' => $nama_anggaran->nama
            ]);
        } else {
            return response()->json([
                'message' => '1',
                'nama' => $nama_anggaran->nama
            ]);
        }
    }

    public function cekAngkas(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jns_ang = $request->jns_ang;
        $sts_angkas = $request->sts_ang;
        $bln_awal = $request->bln_awal;
        $bln_akhir = $request->bln_akhir;

        if ($sts_angkas == 'murni') {
            $field_angkas = 'nilai_susun';
        } else if ($sts_angkas == 'murni_geser1') {
            $field_angkas = 'nilai_susun1';
        } else if ($sts_angkas == 'murni_geser2') {
            $field_angkas = 'nilai_susun2';
        } else if ($sts_angkas == 'murni_geser3') {
            $field_angkas = 'nilai_susun3';
        } else if ($sts_angkas == 'murni_geser4') {
            $field_angkas = 'nilai_susun4';
        } else if ($sts_angkas == 'murni_geser5') {
            $field_angkas = 'nilai_susun5';
        } else if ($sts_angkas == 'sempurna1') {
            $field_angkas = 'nilai_sempurna';
        } else if ($sts_angkas == 'sempurna1_geser1') {
            $field_angkas = 'nilai_sempurna11';
        } else if ($sts_angkas == 'sempurna1_geser2') {
            $field_angkas = 'nilai_sempurna12';
        } else if ($sts_angkas == 'sempurna1_geser3') {
            $field_angkas = 'nilai_sempurna13';
        } else if ($sts_angkas == 'sempurna1_geser4') {
            $field_angkas = 'nilai_sempurna14';
        } else if ($sts_angkas == 'sempurna1_geser5') {
            $field_angkas = 'nilai_sempurna15';
        } else if ($sts_angkas == 'sempurna2') {
            $field_angkas = 'nilai_sempurna2';
        } else if ($sts_angkas == 'sempurna2_geser1') {
            $field_angkas = 'nilai_sempurna21';
        } else if ($sts_angkas == 'sempurna2_geser2') {
            $field_angkas = 'nilai_sempurna22';
        } else if ($sts_angkas == 'sempurna2_geser3') {
            $field_angkas = 'nilai_sempurna23';
        } else if ($sts_angkas == 'sempurna2_geser4') {
            $field_angkas = 'nilai_sempurna24';
        } else if ($sts_angkas == 'sempurna2_geser5') {
            $field_angkas = 'nilai_sempurna25';
        } else if ($sts_angkas == 'sempurna3') {
            $field_angkas = 'nilai_sempurna3';
        } else if ($sts_angkas == 'sempurna3_geser1') {
            $field_angkas = 'nilai_sempurna31';
        } else if ($sts_angkas == 'sempurna3_geser2') {
            $field_angkas = 'nilai_sempurna32';
        } else if ($sts_angkas == 'sempurna3_geser3') {
            $field_angkas = 'nilai_sempurna33';
        } else if ($sts_angkas == 'sempurna3_geser4') {
            $field_angkas = 'nilai_sempurna34';
        } else if ($sts_angkas == 'sempurna3_geser5') {
            $field_angkas = 'nilai_sempurna35';
        } else if ($sts_angkas == 'sempurna4') {
            $field_angkas = 'nilai_sempurna4';
        } else if ($sts_angkas == 'sempurna4_geser1') {
            $field_angkas = 'nilai_sempurna41';
        } else if ($sts_angkas == 'sempurna4_geser2') {
            $field_angkas = 'nilai_sempurna42';
        } else if ($sts_angkas == 'sempurna4_geser3') {
            $field_angkas = 'nilai_sempurna43';
        } else if ($sts_angkas == 'sempurna4_geser4') {
            $field_angkas = 'nilai_sempurna44';
        } else if ($sts_angkas == 'sempurna4_geser5') {
            $field_angkas = 'nilai_sempurna45';
        } else if ($sts_angkas == 'sempurna5') {
            $field_angkas = 'nilai_sempurna5';
        } else if ($sts_angkas == 'sempurna5_geser1') {
            $field_angkas = 'nilai_sempurna51';
        } else if ($sts_angkas == 'sempurna5_geser2') {
            $field_angkas = 'nilai_sempurna52';
        } else if ($sts_angkas == 'sempurna5_geser3') {
            $field_angkas = 'nilai_sempurna53';
        } else if ($sts_angkas == 'sempurna5_geser4') {
            $field_angkas = 'nilai_sempurna1';
        } else if ($sts_angkas == 'sempurna5_geser5') {
            $field_angkas = 'nilai_sempurna55';
        } else if ($sts_angkas == 'ubah') {
            $field_angkas = 'nilai_ubah';
        } else if ($sts_angkas == 'ubah1') {
            $field_angkas = 'nilai_ubah1';
        } else if ($sts_angkas == 'ubah2') {
            $field_angkas = 'nilai_ubah2';
        } else if ($sts_angkas == 'ubah3') {
            $field_angkas = 'nilai_ubah3';
        } else if ($sts_angkas == 'ubah4') {
            $field_angkas = 'nilai_ubah4';
        } else {
            $field_angkas = 'nilai_ubah5';
        }

        $total_anggaran = collect(DB::select("SELECT kd_skpd,sum(nilai) as total from trdrka where kd_skpd= ? and jns_ang = ? group by kd_skpd",[$kd_skpd,$jns_ang]))->first();

        //$total_angkas = collect(DB::select("SELECT kd_skpd, sum(?) as total from trdskpd_ro where kd_skpd= ? group by kd_skpd", [$field_angkas, $kd_skpd]))->first();
        $total_angkas = DB::table('trdskpd_ro')->select('kd_skpd', DB::raw("SUM($field_angkas) as total"))->where(['kd_skpd' => $kd_skpd])->groupBy('kd_skpd')->first();
        //dd($total_angkas);

        if ($total_anggaran->total != $total_angkas->total) {
            return response()->json([
                'message' => '0',
            ]);
        } else {
            return response()->json([
                'message' => '1',
            ]);
        }
    }
}
