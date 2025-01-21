<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenagihanController extends Controller
{
    public function index()
    {
        $kunci = kunci()->kunci_tagih;
        $role = Auth::user()->role;

        $kuncian = $kunci == 1 && !in_array($role, ['1006', '1012', '1016', '1017']) ? '1' : '0';

        $data = [
            'cek' => selisih_angkas(),
            'kunci' =>$kuncian,
        ];
        return view('penatausahaan.pengeluaran.penagihan.index')->with($data);
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtagih as a')
            ->select('no_bukti','status','nm_rekanan','tgl_bukti', 'sts_tagih')
            ->where(['kd_skpd' => $kd_skpd])
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("penagihan.show", Crypt::encryptString($row->no_bukti)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="fas fa-info-circle"></i></a>';

            if ($row->sts_tagih != 0) {
                $btn .= '';
            } else {
                $btn .= '<a href="' . route("penagihan.edit", Crypt::encryptString($row->no_bukti)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="deleteData(\'' . $row->no_bukti . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => 1])->orderByDesc('tgl_dpa')->first();
        $skpd = substr($kd_skpd, 18, 4);

        $kd_bpp = Auth::user()->kd_bpp;
        $id_user = Auth::user()->id;
        $bpp = substr($kd_bpp, 23, 1);

        if ($bpp != '0') {
            $sub_kegiatan = DB::table('trskpd as a')
            ->select('a.total', 'a.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', DB::raw("(SELECT nm_program FROM ms_program WHERE kd_program=a.kd_program) as nm_program"))
            ->join('ms_sub_kegiatan AS b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')
            ->where(['a.kd_skpd' => $kd_skpd, 'a.status_sub_kegiatan' => '1', 'a.jns_ang' => $status_anggaran->jns_ang, 'b.jns_sub_kegiatan' => '5'])
            ->whereRaw("a.kd_sub_kegiatan IN (SELECT kd_sub_kegiatan FROM pelimpahan_kegiatan WHERE kd_bpp=? AND kd_skpd=? AND id_user=?)", [$kd_bpp, $kd_skpd, $id_user])
            ->get();
        } else {
            $sub_kegiatan = DB::table('trskpd as a')
            ->select('a.total', 'a.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', DB::raw("(SELECT nm_program FROM ms_program WHERE kd_program=a.kd_program) as nm_program"))
            ->join('ms_sub_kegiatan AS b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')
            ->where(['a.kd_skpd' => $kd_skpd, 'a.status_sub_kegiatan' => '1', 'a.jns_ang' => $status_anggaran->jns_ang, 'b.jns_sub_kegiatan' => '5'])->get();
        }

        $data = [
            'data_penagihan' => DB::table('trhtagih')->get(),
            'kd_skpd' => $kd_skpd,
            'skpd' => DB::table('ms_skpd')->select('nm_skpd','kd_skpd')->where('kd_skpd', $kd_skpd)->first(),
            'daftar_kontrak' => DB::table('ms_kontrak as z')->where('z.kd_skpd', $kd_skpd)
                ->select('z.no_kontrak', 'z.nmpel','z.nilai', DB::raw("(SELECT SUM(nilai) FROM trhtagih a INNER JOIN trdtagih b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE kontrak=z.no_kontrak and z.kd_skpd=a.kd_skpd) as lalu"))->orderBy('z.no_kontrak', 'ASC')->get(),
            'daftar_rekanan' => DB::table('ms_rekening_bank_online')->where('kd_skpd', $kd_skpd)->orderBy('rekening', 'ASC')->get(),
            'daftar_sub_kegiatan' => $sub_kegiatan,
        ];


        return view('penatausahaan.pengeluaran.penagihan.create')->with($data);
    }

    public function show($no_bukti)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_bukti = Crypt::decryptString($no_bukti);
        $data_tagih = DB::table('trhtagih')->where('no_bukti', $no_bukti)->first();
        $data = [
            'data_tagih' => DB::table('trhtagih as a')->select('a.*','c.nm_kerja','c.nmpel')->join('trdtagih as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->join('ms_kontrak as c', function ($join){
                $join->on('a.kontrak','=','c.no_kontrak');
                $join->on('a.kd_skpd','=','c.kd_skpd');
            })->where(['a.no_bukti'=> $no_bukti, 'a.kd_skpd' =>$kd_skpd])->first(),
            'detail_tagih' => DB::table('trdtagih as a')->select('a.*')->join('trhtagih as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['a.no_bukti'=> $no_bukti, 'a.kd_skpd' =>$kd_skpd])->get(),
            'kontrak' => DB::table('ms_kontrak')->where('no_kontrak', $data_tagih->kontrak)->first(),
            'dttagih' => collect(DB::select("SELECT nmpel,nm_kerja,no_kontrak,nilai,SUM (lalu) AS lalu FROM (
                SELECT a.nmpel AS nmpel,a.nm_kerja AS nm_kerja,a.no_kontrak AS no_kontrak,a.nilai AS nilai,SUM (b.total) AS lalu FROM ms_kontrak a LEFT JOIN trhtagih b ON b.kd_skpd =a.kd_skpd AND b.kontrak =a.no_kontrak WHERE a.kd_skpd = ? and a.no_kontrak = ? GROUP BY a.nmpel,a.nm_kerja,a.no_kontrak,a.nilai,b.total) oke
                GROUP BY nmpel,nm_kerja,no_kontrak,nilai ORDER BY no_kontrak",[$kd_skpd,$data_tagih->kontrak]))->first(),
        ];

        return view('penatausahaan.pengeluaran.penagihan.show')->with($data);
    }

    public function cariRekening(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => 1])->orderBy('tgl_dpa', 'DESC')->first();
        $daftar_rekening = DB::table('trdrka as a')->select('a.kd_rek6', 'a.nm_rek6', 'e.map_lo', DB::raw("(SELECT SUM(nilai) FROM
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
                        AND d.jns_spp='1'
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
                        NOT IN (select no_tagih FROM trhspp WHERE kd_skpd='$kd_skpd' )

                        -- tambahan tampungan
                        UNION ALL
                        SELECT SUM(nilai) as nilai FROM tb_transaksi
                        WHERE
                        kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND kd_skpd = a.kd_skpd
                        AND kd_rek6 = a.kd_rek6
                        -- tambahan tampungan
                        )r) AS lalu,
                    0 AS sp2d,a.nilai AS anggaran"))->leftJoin('ms_rek6 as e', 'a.kd_rek6', '=', 'e.kd_rek6')->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.jns_ang' => $status_anggaran->jns_ang, 'a.kd_skpd' => $kd_skpd, 'a.status_aktif' => '1'])->get();
        return response()->json($daftar_rekening);
    }

    public function totalSpd(Request $request){
        $kd_skpd = Auth::user()->kd_skpd;
        $giat   = $request->kd_sub_kegiatan;

        $data = collect(DB::select("SELECT SUM (a.nilai) AS totalspd FROM trdspd a JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.kd_skpd = ? AND a.kd_sub_kegiatan = ? AND b.status = '1'",[$kd_skpd,$giat]))->first();

        return response()->json($data);
    }

    public function totalAngkas(Request $request){
        $skpd    = $request->skpd;
        $giat    = $request->kdgiat;
        $rek     = $request->kdrek;
        $bln     = $request->bulan;

        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $skpd, 'status' => 1])->orderBy('tgl_dpa', 'DESC')->first();

        $stsangkas1 = status_angkas1($skpd);

        $data = DB::table('trdskpd_ro as a')->select('a.kd_sub_kegiatan', 'kd_rek6', DB::raw("SUM(a.$stsangkas1) as nilai"))->join('trskpd as b', function ($join) {
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
        })->where(['a.kd_skpd' => $skpd, 'a.kd_sub_kegiatan' => $giat, 'a.kd_rek6' => $rek, 'jns_ang' => $status_anggaran->jns_ang])->where('bulan', '<=', $bln)->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')->first();

        return response()->json($data);
    }

    public function cekStatusAngNew(Request $request)
    {
        if ($request->ajax()) {
            $skpd       = Auth::user()->kd_skpd;
            $tgl_bukti = $request->tgl_bukti;
            $data = DB::table('trhrka as a')->join('tb_status_anggaran as b', 'a.jns_ang', '=', 'b.kode')->select('nama', 'jns_ang')->where(['a.kd_skpd' => $skpd, 'status' => '1'])->where('tgl_dpa', '<=', $tgl_bukti)->orderBy('tgl_dpa', 'DESC')->first();
            return response()->json($data);
        }
    }

    public function cekStatusAng(Request $request)
    {
        if ($request->ajax()) {
            $skpd       = Auth::user()->kd_skpd;
            $tgl_bukti = $request->tgl_bukti;
            $urut1 = DB::table('status_angkas')->select(DB::raw("'1' AS urut"), DB::raw("'murni' AS status"), 'murni as nilai')->where(['kd_skpd' => $skpd, 'murni' => '1']);
            $urut2 = DB::table('status_angkas')->select(DB::raw("'2' AS urut"), DB::raw("'murni_geser1' AS status"), 'murni_geser1 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser1' => '1'])->unionAll($urut1);
            $urut3 = DB::table('status_angkas')->select(DB::raw("'3' AS urut"), DB::raw("'murni_geser2' AS status"), 'murni_geser2 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser2' => '1'])->unionAll($urut2);
            $urut4 = DB::table('status_angkas')->select(DB::raw("'4' AS urut"), DB::raw("'murni_geser3' AS status"), 'murni_geser3 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser3' => '1'])->unionAll($urut3);
            $urut5 = DB::table('status_angkas')->select(DB::raw("'5' AS urut"), DB::raw("'murni_geser4' AS status"), 'murni_geser4 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser4' => '1'])->unionAll($urut4);
            $urut6 = DB::table('status_angkas')->select(DB::raw("'6' AS urut"), DB::raw("'murni_geser5' AS status"), 'murni_geser5 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser5' => '1'])->unionAll($urut5);
            $urut7 = DB::table('status_angkas')->select(DB::raw("'7' AS urut"), DB::raw("'sempurna1' AS status"), 'sempurna1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1' => '1'])->unionAll($urut6);
            $urut8 = DB::table('status_angkas')->select(DB::raw("'8' AS urut"), DB::raw("'sempurna1_geser1' AS status"), 'sempurna1_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser1' => '1'])->unionAll($urut7);
            $urut9 = DB::table('status_angkas')->select(DB::raw("'9' AS urut"), DB::raw("'sempurna1_geser2' AS status"), 'sempurna1_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser2' => '1'])->unionAll($urut8);
            $urut10 = DB::table('status_angkas')->select(DB::raw("'10' AS urut"), DB::raw("'sempurna1_geser3' AS status"), 'sempurna1_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser3' => '1'])->unionAll($urut9);
            $urut11 = DB::table('status_angkas')->select(DB::raw("'11' AS urut"), DB::raw("'sempurna1_geser4' AS status"), 'sempurna1_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser4' => '1'])->unionAll($urut10);
            $urut12 = DB::table('status_angkas')->select(DB::raw("'12' AS urut"), DB::raw("'sempurna1_geser5' AS status"), 'sempurna1_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser5' => '1'])->unionAll($urut11);
            $urut13 = DB::table('status_angkas')->select(DB::raw("'13' AS urut"), DB::raw("'sempurna2' AS status"), 'sempurna2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2' => '1'])->unionAll($urut12);
            $urut14 = DB::table('status_angkas')->select(DB::raw("'14' AS urut"), DB::raw("'sempurna2_geser1' AS status"), 'sempurna2_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser1' => '1'])->unionAll($urut13);
            $urut15 = DB::table('status_angkas')->select(DB::raw("'15' AS urut"), DB::raw("'sempurna2_geser2' AS status"), 'sempurna2_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser2' => '1'])->unionAll($urut14);
            $urut16 = DB::table('status_angkas')->select(DB::raw("'16' AS urut"), DB::raw("'sempurna2_geser3' AS status"), 'sempurna2_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser3' => '1'])->unionAll($urut15);
            $urut17 = DB::table('status_angkas')->select(DB::raw("'17' AS urut"), DB::raw("'sempurna2_geser4' AS status"), 'sempurna2_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser4' => '1'])->unionAll($urut16);
            $urut18 = DB::table('status_angkas')->select(DB::raw("'18' AS urut"), DB::raw("'sempurna2_geser5' AS status"), 'sempurna2_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser5' => '1'])->unionAll($urut17);
            $urut19 = DB::table('status_angkas')->select(DB::raw("'19' AS urut"), DB::raw("'sempurna3' AS status"), 'sempurna3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3' => '1'])->unionAll($urut18);
            $urut20 = DB::table('status_angkas')->select(DB::raw("'20' AS urut"), DB::raw("'sempurna3_geser1' AS status"), 'sempurna3_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser1' => '1'])->unionAll($urut19);
            $urut21 = DB::table('status_angkas')->select(DB::raw("'21' AS urut"), DB::raw("'sempurna3_geser2' AS status"), 'sempurna3_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser2' => '1'])->unionAll($urut20);
            $urut22 = DB::table('status_angkas')->select(DB::raw("'22' AS urut"), DB::raw("'sempurna3_geser3' AS status"), 'sempurna3_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser3' => '1'])->unionAll($urut21);
            $urut23 = DB::table('status_angkas')->select(DB::raw("'23' AS urut"), DB::raw("'sempurna3_geser4' AS status"), 'sempurna3_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser4' => '1'])->unionAll($urut22);
            $urut24 = DB::table('status_angkas')->select(DB::raw("'24' AS urut"), DB::raw("'sempurna3_geser5' AS status"), 'sempurna3_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser5' => '1'])->unionAll($urut23);
            $urut25 = DB::table('status_angkas')->select(DB::raw("'25' AS urut"), DB::raw("'sempurna4' AS status"), 'sempurna4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4' => '1'])->unionAll($urut24);
            $urut26 = DB::table('status_angkas')->select(DB::raw("'26' AS urut"), DB::raw("'sempurna4_geser1' AS status"), 'sempurna4_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser1' => '1'])->unionAll($urut25);
            $urut27 = DB::table('status_angkas')->select(DB::raw("'27' AS urut"), DB::raw("'sempurna4_geser2' AS status"), 'sempurna4_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser2' => '1'])->unionAll($urut26);
            $urut28 = DB::table('status_angkas')->select(DB::raw("'28' AS urut"), DB::raw("'sempurna4_geser3' AS status"), 'sempurna4_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser3' => '1'])->unionAll($urut27);
            $urut29 = DB::table('status_angkas')->select(DB::raw("'29' AS urut"), DB::raw("'sempurna4_geser4' AS status"), 'sempurna4_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser4' => '1'])->unionAll($urut28);
            $urut30 = DB::table('status_angkas')->select(DB::raw("'30' AS urut"), DB::raw("'sempurna4_geser5' AS status"), 'sempurna4_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser5' => '1'])->unionAll($urut29);
            $urut31 = DB::table('status_angkas')->select(DB::raw("'31' AS urut"), DB::raw("'sempurna5' AS status"), 'sempurna5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5' => '1'])->unionAll($urut30);
            $urut32 = DB::table('status_angkas')->select(DB::raw("'32' AS urut"), DB::raw("'sempurna5_geser1' AS status"), 'sempurna5_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser1' => '1'])->unionAll($urut31);
            $urut33 = DB::table('status_angkas')->select(DB::raw("'33' AS urut"), DB::raw("'sempurna5_geser2' AS status"), 'sempurna5_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser2' => '1'])->unionAll($urut32);
            $urut34 = DB::table('status_angkas')->select(DB::raw("'34' AS urut"), DB::raw("'sempurna5_geser3' AS status"), 'sempurna5_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser3' => '1'])->unionAll($urut33);
            $urut35 = DB::table('status_angkas')->select(DB::raw("'35' AS urut"), DB::raw("'sempurna5_geser4' AS status"), 'sempurna5_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser4' => '1'])->unionAll($urut34);
            $urut36 = DB::table('status_angkas')->select(DB::raw("'36' AS urut"), DB::raw("'sempurna5_geser5' AS status"), 'sempurna5_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser5' => '1'])->unionAll($urut35);
            $urut37 = DB::table('status_angkas')->select(DB::raw("'37' AS urut"), DB::raw("'ubah' AS status"), 'ubah as nilai')->where(['kd_skpd' => $skpd, 'ubah' => '1'])->unionAll($urut36);
            $urut38 = DB::table('status_angkas')->select(DB::raw("'38' AS urut"), DB::raw("'ubah2' AS status"), 'ubah2 as nilai')->where(['kd_skpd' => $skpd, 'ubah2' => '1'])->unionAll($urut37);
            $urut39 = DB::table('status_angkas')->select(DB::raw("'39' AS urut"), DB::raw("'ubah3' AS status"), 'ubah3 as nilai')->where(['kd_skpd' => $skpd, 'ubah3' => '1'])->unionAll($urut38);
            $result = DB::table(DB::raw("({$urut38->toSql()}) AS sub"))
                ->select("urut", "status", "nilai")
                ->mergeBindings($urut38)
                ->orderByRaw("CAST(urut AS INT) DESC")
                ->first();
             return response()->json($result);
        }
    }

    public function cariSumberDana(Request $request)
    {
        $kode               = $request->skpd;
        $giat               = $request->kdgiat;
        $rek                = $request->kdrek;
        $status             = DB::table('trhrka')->where(['kd_skpd' => $kode, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
        $status_anggaran    = $status->jns_ang;
        // $data = DB::select(DB::raw("SELECT sumber_dana, nilai, isnull( tagihlalu, 0 ) + isnull( tampungan, 0 ) + isnull( spplalu, 0 ) + isnull( upgulalucms, 0 ) + isnull( upgulalu, 0 ) AS lalu
        // FROM
        // (
        // SELECT
        //     sumber1 AS sumber_dana,
        //     isnull( nsumber1, 0 ) AS nilai,
        //     (
        //     SELECT SUM
        //         ( nilai ) AS nilai
        //     FROM
        //         trdtagih t
        //         INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti
        //         AND t.kd_skpd= u.kd_skpd
        //     WHERE
        //         t.kd_sub_kegiatan = '$giat'
        //         AND u.kd_skpd = '$kode'
        //         AND t.kd_rek6 = '$rek'
        //         AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd = '$kode' )
        //         AND sumber = z.sumber1
        //     ) AS tagihlalu,
        //     (
        //     SELECT SUM
        //         ( nilai ) AS nilai
        //     FROM
        //         tb_transaksi
        //     WHERE
        //         kd_sub_kegiatan = '$giat'
        //         AND kd_skpd = '$kode'
        //         AND kd_rek6 = '$rek'
        //         AND sumber = z.sumber1
        //     ) AS tampungan,
        //     (
        //     SELECT SUM
        //         ( b.nilai )
        //     FROM
        //         trhspp a
        //         INNER JOIN trdspp b ON a.no_spp= b.no_spp
        //         AND a.kd_skpd= b.kd_skpd
        //     WHERE
        //         b.kd_skpd= '$kode'
        //         AND b.kd_Sub_kegiatan= '$giat'
        //         AND b.kd_rek6= '$rek'
        //         AND sumber = sumber1
        //         AND ( sp2d_batal <> '1' OR sp2d_batal IS NULL )
        //         AND jns_spp NOT IN ( '1', '2' )
        //     ) AS spplalu,
        //     (
        //     SELECT SUM
        //         ( g.nilai )
        //     FROM
        //         trhtransout_cmsbank f
        //         INNER JOIN trdtransout_cmsbank g ON f.no_voucher= g.no_voucher
        //         AND f.kd_skpd= g.kd_skpd
        //     WHERE
        //         g.kd_skpd = '$kode'
        //         AND g.kd_sub_kegiatan= '$giat'
        //         AND g.kd_rek6= '$rek'
        //         AND f.jns_spp IN ( '1' )
        //         AND ( f.status_validasi= '0' OR f.status_validasi IS NULL )
        //         AND sumber = z.sumber1
        //     ) upgulalucms,
        //     (
        //     SELECT SUM
        //         ( g.nilai )
        //     FROM
        //         trhtransout f
        //         INNER JOIN trdtransout g ON f.no_bukti= g.no_bukti
        //         AND f.kd_skpd= g.kd_skpd
        //     WHERE
        //         g.kd_skpd = '$kode'
        //         AND g.kd_sub_kegiatan= '$giat'
        //         AND g.kd_rek6= '$rek'
        //         AND f.jns_spp IN ( '1' )
        //         AND sumber = z.sumber1
        //     ) upgulalu
        // FROM
        //     trdrka z
        // WHERE
        //     z.kd_skpd= '$kode'
        //     AND z.kd_sub_kegiatan= '$giat'
        //     AND jns_ang = '$status_anggaran'
        //     AND z.kd_rek6= '$rek'
        // UNION ALL
        // SELECT
        //     sumber2 AS sumber_dana,
        //     isnull( nsumber2, 0 ) AS nilai,
        //     (
        //     SELECT SUM
        //         ( nilai ) AS nilai
        //     FROM
        //         trdtagih t
        //         INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti
        //         AND t.kd_skpd= u.kd_skpd
        //     WHERE
        //         t.kd_sub_kegiatan = '$giat'
        //         AND u.kd_skpd = '$kode'
        //         AND t.kd_rek6 = '$rek'
        //         AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd = '$kode' )
        //         AND sumber = z.sumber2
        //     ) AS tagihlalu,
        //     (
        //     SELECT SUM
        //         ( nilai ) AS nilai
        //     FROM
        //         tb_transaksi
        //     WHERE
        //         kd_sub_kegiatan = '$giat'
        //         AND kd_skpd = '$kode'
        //         AND kd_rek6 = '$rek'
        //         AND sumber = z.sumber2
        //     ) AS tampungan,
        //     (
        //     SELECT SUM
        //         ( u.nilai ) AS nilai
        //     FROM
        //         trhspp t
        //         INNER JOIN trdspp u ON t.no_spp= u.no_spp
        //         AND t.kd_skpd= u.kd_skpd
        //     WHERE
        //         u.kd_sub_kegiatan = '$giat'
        //         AND u.kd_skpd = '$kode'
        //         AND u.kd_rek6 = '$rek'
        //         AND sumber = z.sumber2
        //         AND ( sp2d_batal <> '1' OR sp2d_batal IS NULL )
        //         AND jns_spp NOT IN ( '1', '2' )
        //     ) AS spplalu,
        //     (
        //     SELECT SUM
        //         ( g.nilai )
        //     FROM
        //         trhtransout_cmsbank f
        //         INNER JOIN trdtransout_cmsbank g ON f.no_voucher= g.no_voucher
        //         AND f.kd_skpd= g.kd_skpd
        //     WHERE
        //         g.kd_skpd = '$kode'
        //         AND g.kd_sub_kegiatan= '$giat'
        //         AND g.kd_rek6= '$rek'
        //         AND f.jns_spp IN ( '1' )
        //         AND ( f.status_validasi= '0' OR f.status_validasi IS NULL )
        //         AND sumber = z.sumber2
        //     ) upgulalucms,
        //     (
        //     SELECT SUM
        //         ( g.nilai )
        //     FROM
        //         trhtransout f
        //         INNER JOIN trdtransout g ON f.no_bukti= g.no_bukti
        //         AND f.kd_skpd= g.kd_skpd
        //     WHERE
        //         g.kd_skpd = '$kode'
        //         AND g.kd_sub_kegiatan= '$giat'
        //         AND g.kd_rek6= '$rek'
        //         AND f.jns_spp IN ( '1' )
        //         AND sumber = z.sumber2
        //     ) upgulalu
        // FROM
        //     trdrka z
        // WHERE
        //     z.kd_sub_kegiatan= '$giat'
        //     AND z.kd_rek6= '$rek'
        //     AND jns_ang = '$status_anggaran'
        //     AND z.kd_skpd= '$kode'
        // UNION ALL
        // SELECT
        //     sumber3 AS sumber_dana,
        //     isnull( nsumber3, 0 ) AS nilai,
        //     (
        //     SELECT SUM
        //         ( nilai ) AS nilai
        //     FROM
        //         trdtagih t
        //         INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti
        //         AND t.kd_skpd= u.kd_skpd
        //     WHERE
        //         t.kd_sub_kegiatan = '$giat'
        //         AND u.kd_skpd = '$kode'
        //         AND t.kd_rek6 = '$rek'
        //         AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd = '$kode' )
        //         AND sumber = sumber3
        //     ) AS tagihlalu,
        //     (
        //     SELECT SUM
        //         ( nilai ) AS nilai
        //     FROM
        //         tb_transaksi
        //     WHERE
        //         kd_sub_kegiatan = '$giat'
        //         AND kd_skpd = '$kode'
        //         AND kd_rek6 = '$rek'
        //         AND sumber = a.sumber3
        //     ) AS tampungan,
        //     (
        //     SELECT SUM
        //         ( t.nilai ) AS nilai
        //     FROM
        //         trdspp t
        //         INNER JOIN trhspp u ON t.no_spp= u.no_spp
        //         AND t.kd_skpd= u.kd_skpd
        //     WHERE
        //         t.kd_sub_kegiatan = '$giat'
        //         AND t.kd_skpd = '$kode'
        //         AND t.kd_rek6 = '$rek'
        //         AND sumber = sumber3
        //         AND jns_spp NOT IN ( '1', '2' )
        //         AND ( sp2d_batal <> '1' OR sp2d_batal IS NULL )
        //     ) AS spplalu,
        //     (
        //     SELECT SUM
        //         ( g.nilai )
        //     FROM
        //         trhtransout_cmsbank f
        //         INNER JOIN trdtransout_cmsbank g ON f.no_voucher= g.no_voucher
        //         AND f.kd_skpd= g.kd_skpd
        //     WHERE
        //         g.kd_skpd = '$kode'
        //         AND g.kd_sub_kegiatan= '$giat'
        //         AND g.kd_rek6= '$rek'
        //         AND f.jns_spp IN ( '1' )
        //         AND ( f.status_validasi= '0' OR f.status_validasi IS NULL )
        //         AND sumber = sumber3
        //     ) upgulalucms,
        //     (
        //     SELECT SUM
        //         ( g.nilai )
        //     FROM
        //         trhtransout f
        //         INNER JOIN trdtransout g ON f.no_bukti= g.no_bukti
        //         AND f.kd_skpd= g.kd_skpd
        //     WHERE
        //         g.kd_skpd = '$kode'
        //         AND g.kd_sub_kegiatan= '$giat'
        //         AND g.kd_rek6= '$rek'
        //         AND f.jns_spp IN ( '1' )
        //         AND sumber = sumber3
        //     ) upgulalu
        // FROM
        //     trdrka a
        // WHERE
        //     a.kd_sub_kegiatan= '$giat'
        //     AND a.kd_rek6= '$rek'
        //     AND jns_ang = '$status_anggaran'
        //     AND a.kd_skpd= '$kode'
        // UNION ALL
        // SELECT
        //     sumber4 AS sumber_dana,
        //     isnull( nsumber4, 0 ) AS nilai,
        //     (
        //     SELECT SUM
        //         ( nilai ) AS nilai
        //     FROM
        //         trdtagih t
        //         INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti
        //         AND t.kd_skpd= u.kd_skpd
        //     WHERE
        //         t.kd_sub_kegiatan = '$giat'
        //         AND u.kd_skpd = '$kode'
        //         AND t.kd_rek6 = '$rek'
        //         AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd = '$kode' )
        //         AND sumber = sumber4
        //     ) AS lalu,
        //     (
        //     SELECT SUM
        //         ( nilai ) AS nilai
        //     FROM
        //         tb_transaksi
        //     WHERE
        //         kd_sub_kegiatan = '$giat'
        //         AND kd_skpd = '$kode'
        //         AND kd_rek6 = '$rek'
        //         AND sumber = a.sumber4
        //     ) AS tampungan,
        //     (
        //     SELECT SUM
        //         ( t.nilai ) AS nilai
        //     FROM
        //         trdspp t
        //         INNER JOIN trhspp u ON t.no_spp= u.no_spp
        //         AND t.kd_skpd= u.kd_skpd
        //     WHERE
        //         t.kd_sub_kegiatan = '$giat'
        //         AND t.kd_skpd = '$kode'
        //         AND t.kd_rek6 = '$rek'
        //         AND jns_spp NOT IN ( '1', '2' )
        //         AND sumber = sumber4
        //         AND ( sp2d_batal <> '1' OR sp2d_batal IS NULL )
        //     ) AS lalu,
        //     (
        //     SELECT SUM
        //         ( g.nilai )
        //     FROM
        //         trhtransout_cmsbank f
        //         INNER JOIN trdtransout_cmsbank g ON f.no_voucher= g.no_voucher
        //         AND f.kd_skpd= g.kd_skpd
        //     WHERE
        //         g.kd_skpd = '$kode'
        //         AND g.kd_sub_kegiatan= '$giat'
        //         AND g.kd_rek6= '$rek'
        //         AND f.jns_spp IN ( '1' )
        //         AND ( f.status_validasi= '0' OR f.status_validasi IS NULL )
        //         AND sumber = sumber4
        //     ) upgulalucms,
        //     (
        //     SELECT SUM
        //         ( g.nilai )
        //     FROM
        //         trhtransout f
        //         INNER JOIN trdtransout g ON f.no_bukti= g.no_bukti
        //         AND f.kd_skpd= g.kd_skpd
        //     WHERE
        //         g.kd_skpd = '$kode'
        //         AND g.kd_sub_kegiatan= '$giat'
        //         AND g.kd_rek6= '$rek'
        //         AND f.jns_spp IN ( '1' )
        //         AND sumber = sumber4
        //     ) upgulalu
        // FROM
        //     trdrka a
        // WHERE
        //     a.kd_sub_kegiatan= '$giat'
        //     AND a.kd_rek6= '$rek'
        //     AND jns_ang = '$status_anggaran'
        //     AND a.kd_skpd= '$kode'
        // ) z
        // WHERE z.nilai<>0"));
        // $no_trdrka = $kode . '.' . $giat . '.' . $rek;

        // $data1 = DB::table('trdpo')
        //     ->select('sumber', 'nm_sumber', DB::raw("SUM(total) as nilai"))
        //     ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $status_anggaran])
        //     ->whereNotNull('sumber')
        //     ->groupBy('sumber', 'nm_sumber');

        // $data2 = DB::table('trdpo')
        //     ->select('sumber', DB::raw("'Silahkan isi sumber di anggaran' as nm_sumber"), DB::raw("SUM(total) as nilai"))
        //     ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $status_anggaran])
        //     ->where(function ($query) {
        //         $query->where('sumber', '')->orWhereNull('sumber');
        //     })
        //     ->groupBy('sumber', 'nm_sumber')
        //     ->union($data1);

        // $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
        //     ->mergeBindings($data2)
        //     ->get();

        $data = DB::select("SELECT oke.sumber as kd_sumber, (select sumber_dana from ms_sumber_dana c where c.kd_dana = oke.sumber) as nm_sumber, SUM (nilai) AS nilai,SUM (sd) AS sd FROM
        (
            SELECT * FROM (SELECT sumber, sum(total) as nilai, 0 as sd from trdpo where kd_sub_kegiatan = ? and kd_rek6 = ? and kd_skpd = ? and jns_ang = ? GROUP BY sumber)z
            UNION ALL
            SELECT sumber,nilai,sd FROM (
            SELECT c.sumber,0 AS nilai,SUM (c.nilai) AS sd FROM trdtransout_cmsbank c LEFT JOIN trhtransout_cmsbank d ON c.no_voucher =d.no_voucher AND c.kd_skpd =d.kd_skpd WHERE c.kd_sub_kegiatan =? AND LEFT (d.kd_skpd,22)=LEFT (?,22) AND c.kd_rek6 =? AND d.status_validasi='0' GROUP BY c.sumber UNION ALL
            SELECT c.sumber, 0 AS nilai,SUM (c.nilai) AS sd FROM trdtransout c LEFT JOIN trhtransout d ON c.no_bukti =d.no_bukti AND c.kd_skpd =d.kd_skpd WHERE c.kd_sub_kegiatan =? AND LEFT (d.kd_skpd,22)=LEFT (?,22) AND c.kd_rek6 =? AND d.jns_spp in ('1')  GROUP BY c.sumber UNION ALL
            SELECT x.sumber, 0 AS nilai,SUM (x.nilai) AS sd FROM trdspp x INNER JOIN trhspp y ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd WHERE x.kd_sub_kegiatan =? AND LEFT (x.kd_skpd,22)=LEFT (?,22) AND x.kd_rek6 =? AND y.jns_spp IN ('3','4','5','6') AND (sp2d_batal IS NULL OR sp2d_batal='' OR sp2d_batal='0') GROUP BY x.sumber UNION ALL
            SELECT t.sumber,0 AS nilai,SUM (t.nilai) AS sd FROM trdtagih t INNER JOIN trhtagih u ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd WHERE t.kd_sub_kegiatan =? AND u.kd_skpd =? AND t.kd_rek =? AND u.no_bukti NOT IN (
            SELECT no_tagih FROM trhspp WHERE kd_skpd=?) GROUP BY t.sumber) r
            ) oke GROUP BY oke.sumber", [$giat,$rek,$kode,$status_anggaran,$giat,$kode,$rek,$giat,$kode,$rek,$giat,$kode,$rek,$giat,$kode,$rek,$kode]);
        return response()->json($data);
    }

    public function realisasiSumber(Request $request)
    {
        $sumber = $request->sumber;
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = $request->kd_skpd;
        $kd_rek6 = $request->kd_rek6;

        $tagih_lalu = DB::table('trdtagih as a')
            ->join('trhtagih as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("SUM( nilai ) AS nilai")
            ->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'a.kd_rek' => $kd_rek6, 'sumber' => $sumber])
            ->whereRaw("b.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd =? )", [$kd_skpd])
            ->first();

        $tampungan = DB::table('tb_transaksi as a')
            ->selectRaw("SUM( nilai ) AS nilai")
            ->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek6, 'a.sumber' => $sumber])
            ->first();

        $spplalu = DB::table('trhspp as a')
            ->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("SUM( b.nilai ) AS nilai")
            ->where(['b.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'b.kd_rek6' => $kd_rek6, 'sumber' => $sumber])
            ->where(function ($query) {
                $query->where('sp2d_batal', '<>', '1')->orWhereNull('sp2d_batal');
            })
            ->whereNotIn('jns_spp', ['1', '2'])
            ->first();

        $upgulalucms = DB::table('trhtransout_cmsbank as a')
            ->join('trdtransout_cmsbank as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("SUM( b.nilai ) AS nilai")
            ->where(['b.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'b.kd_rek6' => $kd_rek6, 'sumber' => $sumber])
            ->where(function ($query) {
                $query->where('a.status_validasi', '<>', '1')->orWhereNull('a.status_validasi');
            })
            ->whereIn('a.jns_spp', ['1'])
            ->first();

        $upgulalu = DB::table('trhtransout as a')
            ->join('trdtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("SUM( b.nilai ) AS nilai")
            ->where(['b.kd_sub_kegiatan' => $kd_sub_kegiatan, 'b.kd_skpd' => $kd_skpd, 'b.kd_rek6' => $kd_rek6, 'sumber' => $sumber])
            ->whereIn('a.jns_spp', ['1'])
            ->first();

        $realisasi = $tagih_lalu->nilai + $spplalu->nilai + $upgulalucms->nilai + $upgulalu->nilai;
        return response()->json($realisasi);
    }

    public function cariNamaSumber(Request $request)
    {
        $sumber_dana = $request->sumber_dana;
        $data = DB::table('sumber_dana')->select('nm_sumber_dana1', 'kd_sumber_dana1')->where('kd_sumber_dana1', $sumber_dana)->first();
        return response()->json($data);
    }

    public function cariNamaSumber2(Request $request)
    {
        $sumber_dana = $request->sumber_dana;
        $data = DB::table('sumber_dana')->select('nm_sumber_dana1','kd_sumber_dana1')->where('nm_sumber_dana1', $sumber_dana)->first();
        return response()->json($data);
    }

    public function cariTotalKontrak(Request $request)
    {
        $no_kontrak = $request->no_kontrak;
        $skpd = $request->skpd;
        $data = DB::table('ms_kontrak')->select(DB::raw('SUM(nilai) as total_kontrak'))->where(['kd_skpd' => $skpd, 'no_kontrak' => $no_kontrak])->first();
        return response()->json($data);
    }

    public function simpanTampungan(Request $request)
    {
        $nomor = $request->nomor;
        $kdgiat = $request->kdgiat;
        $kdrek = $request->kdrek;
        $nilai_tagih = $request->nilai_tagih;
        $sumber = $request->sumber;
        $skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;
        $tanggal_ubah = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tb_transaksi')->insert(
                [
                    'kd_skpd' => $skpd,
                    'no_transaksi' => $nomor,
                    'kd_sub_kegiatan' => $kdgiat,
                    'kd_rek6' => $kdrek,
                    'sumber' => $sumber,
                    'nilai' => $nilai_tagih,
                    'username' => $nama,
                    'last_update' => $tanggal_ubah,
                ]
            );
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

    public function cekNilaiKontrak(Request $request)
    {
        $no_kontrak = $request->no_kontrak;
        $tgl_bukti = $request->tgl_bukti;
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('ms_kontrak')->select(DB::raw('SUM(nilai) as nilai'))->where(['no_kontrak' => $no_kontrak, 'kd_skpd' => $kd_skpd])->first();
        return response()->json($data);
    }

    public function cekNilaiKontrak2(Request $request)
    {
        $no_kontrak = $request->no_kontrak;
        $tgl_bukti = $request->tgl_bukti;
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('ms_kontrak')->select(DB::raw('SUM(nilai) as nilai'))->where(['no_kontrak' => $no_kontrak, 'kd_skpd' => $kd_skpd])->first();
        return response()->json($data);
    }

    // Cek simpan Input
    public function cekSimpanPenagihan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtagih')->select(DB::raw('COUNT(*) as jumlah'))->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->first();
        return response()->json($data);
    }

    public function simpanPenagihan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;
        DB::beginTransaction();
        try {
            $cek_simpan = DB::table('trhtagih')->select('no_bukti')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->count();
            if ($cek_simpan > 0) {
                return response()->json([
                    'message' => '1'
                ]);
            }
            DB::table('trhtagih')->insert([
                'no_bukti' => $request->no_bukti,
                'tgl_bukti' => $request->tgl_bukti,
                'ket' => $request->ket,
                'username' => '',
                'tgl_update' => '',
                'kd_skpd' => $request->kd_skpd,
                'nm_skpd' => $request->nm_skpd,
                'total' => $request->total_nilai,
                'no_tagih' => '',
                'sts_tagih' => $request->cstatus,
                'status' => $request->status_bayar,
                'tgl_tagih' => $request->ctgltagih,
                'jns_spp' => $request->cjenis,
                // 'jenis' => empty($request->jenis) ? '' : $request->jenis,
                'jenis' => isset($request->jenis) ? $request->jenis : '',
                'kontrak' => $request->no_kontrak,
                'jns_trs' => $request->jns_trs,
                'ket_bast' => $request->ket_bast,
                'nm_rekanan' => $request->rekanan,
                'no_bapp' => $request->bapp,
                'no_basthp' => $request->basthp,
                'no_bap' => $request->bap,
            ]);
            DB::commit();
            return response()->json([
                'message' => '2'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function simpanDetailPenagihan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $status_bayar = $request->status_bayar;
        $rincian_penagihan = $request->rincian_penagihan;
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;
        DB::beginTransaction();
        try {
            if (isset($rincian_penagihan)) {
                DB::table('trdtagih')->insert(array_map(function ($value) {
                    return [
                        'no_bukti' => $value['no_bukti'],
                        'no_sp2d' => '',
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'kd_rek' => $value['kd_rek'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $value['kd_skpd'],
                        'sumber' => $value['sumber'],
                    ];
                }, $rincian_penagihan));
                DB::table('tb_transaksi')->where(['kd_skpd' => $kd_skpd, 'no_transaksi' => $no_bukti, 'username' => $nama])->delete();
            }
            DB::commit();
            return response()->json([
                'message' => '4'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '5'
            ]);
        }
    }

    public function edit($no_bukti)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_bukti = Crypt::decryptString($no_bukti);
        $data_tagih = DB::table('trhtagih')->where('no_bukti', $no_bukti)->first();
        // dd($data_tagih);
        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $data_tagih->kd_skpd, 'status' => 1])->orderBy('tgl_dpa', 'DESC')->first();
        $data = [
            'data_tagih' => DB::table('trhtagih as a')->join('trdtagih as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where('a.no_bukti', $no_bukti)->first(),
            'detail_tagih' => DB::table('trdtagih as a')->select('a.*')->join('trhtagih as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where('a.no_bukti', $no_bukti)->get(),
            'daftar_kontrak' => DB::table('ms_kontrak as z')->where('z.kd_skpd', $data_tagih->kd_skpd)
                ->select('z.no_kontrak', 'z.nmpel', 'z.nilai', DB::raw("(SELECT SUM(nilai) FROM trhtagih a INNER JOIN trdtagih b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE kontrak=z.no_kontrak) as lalu"))->orderBy('z.no_kontrak', 'ASC')->get(),
            'kontrak' => DB::table('ms_kontrak')->where('no_kontrak', $data_tagih->kontrak)->first(),
            'daftar_rekanan' => DB::table('ms_rekening_bank_online')->where('kd_skpd', $data_tagih->kd_skpd)->orderBy('rekening', 'ASC')->get(),
            'daftar_sub_kegiatan' => DB::table('trskpd as a')
                ->select('a.total', 'a.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', DB::raw("(SELECT nm_program FROM ms_program WHERE kd_program=a.kd_program) as nm_program"))
                ->join('ms_sub_kegiatan AS b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')
                ->where(['a.kd_skpd' => $data_tagih->kd_skpd, 'a.status_sub_kegiatan' => '1', 'a.jns_ang' => $status_anggaran->jns_ang, 'b.jns_sub_kegiatan' => '5'])->get(),
            'kontrak' => DB::table('ms_kontrak')->where('no_kontrak', $data_tagih->kontrak)->first(),
            'bulan' => $data_tagih->tgl_bukti,
            'dttagih' => collect(DB::select("SELECT nmpel,nm_kerja,no_kontrak,nilai,SUM (lalu) AS lalu FROM (
                SELECT a.nmpel AS nmpel,a.nm_kerja AS nm_kerja,a.no_kontrak AS no_kontrak,a.nilai AS nilai,SUM (b.total) AS lalu FROM ms_kontrak a LEFT JOIN trhtagih b ON b.kd_skpd =a.kd_skpd AND b.kontrak =a.no_kontrak WHERE a.kd_skpd = ? AND b.kontrak = ? GROUP BY a.nmpel,a.nm_kerja,a.no_kontrak,a.nilai,b.total) oke
                GROUP BY nmpel,nm_kerja,no_kontrak,nilai ORDER BY no_kontrak",[$kd_skpd, $data_tagih->kontrak]))->first()
        ];

        return view('penatausahaan.pengeluaran.penagihan.edit')->with($data);
    }

    public function hapusPenagihan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;
        DB::beginTransaction();
        try {
            DB::table('trdtagih')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();
            DB::table('trhtagih')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();
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

    public function hapusTampunganPenagihan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_rek = $request->kd_rek;
        $sumber = $request->sumber;
        $nama = Auth::user()->nama;
        $kd_skpd = Auth::user()->kd_skpd;
        $nilai = $request->nilai;
        DB::beginTransaction();
        try {
            DB::table('tb_transaksi')->where(['no_transaksi' => $no_bukti, 'username' => $nama, 'kd_skpd' => $kd_skpd, 'kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rek, 'sumber' => $sumber])->delete();
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

    public function hapusSemuaTampungan()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;
        DB::beginTransaction();
        try {
            DB::table('tb_transaksi')->where(['kd_skpd' => $kd_skpd, 'username' => $nama])->delete();
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

    public function hapusDetailEditPenagihan(Request $request)
    {
        DB::beginTransaction();
        try {
            $no_bukti = $request->no_bukti;
            $kd_sub_kegiatan = $request->kd_sub_kegiatan;
            $kd_rek = $request->kd_rek;
            $sumber = $request->sumber;
            $nilai = $request->nilai;
            $kd_skpd = Auth::user()->kd_skpd;

            DB::table('trdtagih')->where(['no_bukti' => $no_bukti, 'kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek' => $kd_rek, 'sumber' => $sumber])->delete();
            $cari_total = DB::table('trhtagih')->select('total')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->first();
            if ($cari_total) {
                $total = $cari_total->total;
                DB::table('trhtagih')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->update([
                    'total' => $total - $nilai,
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

    public function updatePenagihan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;
        $kd_skpd1 = $request->kd_skpd;
        $no_tersimpan = $request->no_tersimpan;
        DB::beginTransaction();
        try {
            if ($no_bukti != $no_tersimpan) {
                $cek_simpan = DB::table('trhtagih')->select('no_bukti')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->count();
                if ($cek_simpan > 0) {
                    return response()->json([
                        'message' => '1'
                    ]);
                }
            }
            $cek_spp = DB::table('trhspp')->select('no_tagih')->where('no_tagih', $no_bukti)->where('sp2d_batal', '0')->where('sp2d_batal', null)->count();
            if ($cek_spp == '0') {
                DB::table('trhtagih')->where(['no_bukti' => $no_tersimpan, 'kd_skpd' => $kd_skpd1])->update([
                    'no_bukti' => $request->no_bukti,
                    'tgl_bukti' => $request->tgl_bukti,
                    'ket' => $request->ket,
                    'username' => '',
                    'tgl_update' => '',
                    'nm_skpd' => $request->nm_skpd,
                    'total' => $request->total_nilai,
                    // 'no_tagih' => $request->ctagih,
                    'no_tagih'  => '',
                    'sts_tagih' => $request->cstatus,
                    'status' => $request->status_bayar,
                    'tgl_tagih' => $request->ctgltagih,
                    'jns_spp' => $request->cjenis,
                    // 'jenis' => $request->jenis,
                    'jenis' => isset($request->jenis) ? $request->jenis : '',
                    'kontrak' => $request->no_kontrak,
                    'ket_bast' => $request->ket_bast,
                    'nm_rekanan' => $request->rekanan,
                    'no_bapp' => $request->bapp,
                    'no_basthp' => $request->basthp,
                    'no_bap' => $request->bap,
                ]);
                DB::commit();
                return response()->json([
                    'message' => '2'
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function updateDetailPenagihan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $no_tersimpan = $request->no_tersimpan;
        $status_bayar = $request->status_bayar;
        $rincian_penagihan = $request->rincian_penagihan;
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;
        DB::beginTransaction();
        try {
            $cek_spp = DB::table('trhspp')->select('no_tagih')->where('no_tagih', $no_bukti)->where('sp2d_batal', '0')->where('sp2d_batal', null)->count();
            if ($cek_spp == '0') {
                DB::table('trdtagih')->where(['no_bukti' => $no_tersimpan, 'kd_skpd' => $kd_skpd])->delete();
                if (isset($rincian_penagihan)) {
                    DB::table('trdtagih')->insert(array_map(function ($value) use ($no_bukti) {
                        return [
                            'no_bukti' => $no_bukti,
                            // 'no_sp2d' => $value['no_sp2d'],
                            'no_sp2d'   => '',
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                            'kd_rek6' => $value['kd_rek6'],
                            'kd_rek' => $value['kd_rek'],
                            'nm_rek6' => $value['nm_rek6'],
                            'nilai' => $value['nilai'],
                            'kd_skpd' => $value['kd_skpd'],
                            'sumber' => $value['sumber'],
                        ];
                    }, $rincian_penagihan));
                    DB::commit();
                    return response()->json([
                        'message' => '1'
                    ]);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function simpanEditTampungan(Request $request)
    {
        $nomor = $request->nomor;
        $no_simpan = $request->no_simpan;
        $kdgiat = $request->kdgiat;
        $nmgiat = $request->nmgiat;
        $kdrek6 = $request->kdrek6;
        $kdrek = $request->kdrek;
        $nmrek = $request->nmrek;
        $nilai_tagih = $request->nilai_tagih;
        $sumber = $request->sumber;
        $kd_skpd = Auth::user()->kd_skpd;
        DB::beginTransaction();
        try {
            DB::table('trdtagih')->insert([
                'no_bukti' => $no_simpan,
                'kd_sub_kegiatan' => $kdgiat,
                'nm_sub_kegiatan' => $nmgiat,
                'kd_rek6' => $kdrek6,
                'kd_rek' => $kdrek,
                'nm_rek6' => $nmrek,
                'nilai' => $nilai_tagih,
                'kd_skpd' => $kd_skpd,
                'sumber' => $sumber
            ]);
            $cari_total = DB::table('trhtagih')->select('total')->where(['no_bukti' => $nomor, 'kd_skpd' => $kd_skpd])->first();
            if ($cari_total) {
                $total = $cari_total->total;
                DB::table('trhtagih')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $no_simpan])->update([
                    'total' => $total + $nilai_tagih,
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
}
