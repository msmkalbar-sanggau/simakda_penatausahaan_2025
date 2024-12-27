<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Connection;
use Illuminate\Database\Query\JoinClause;
use App\Http\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

use PDF;

class SppTU1Controller extends Controller
{
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'data_spp' => DB::table('trhspp')->where('kd_skpd', $kd_skpd)->whereNotIn('jns_spp', ['1', '2', '3'])->orderByRaw("tgl_spp ASC, no_spp ASC,CAST(urut AS INT) ASC")->get(),
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['BK', 'KPA'])->get(),
            'pptk' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PPTK', 'KPA'])->get(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PA', 'KPA'])->get(),
            'ppkd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', '5.02.0.00.0.00.02.0000')->whereIn('kode', ['BUD', 'KPA'])->get(),
        ];

        return view('penatausahaan.pengeluaran.spp_tu.index')->with($data);
    }

    // List SPP TU
    function getSPPTU(Request $request)
    {
        try {
            $kd_skpd = Auth::user()->kd_skpd;
            $data = DB::table('trhspp')
                ->select('trhspp.no_spp', 'trhspp.tgl_spp', 'trhspp.keperluan as keterangan')
                ->join('trdspp', function ($join) {
                    $join->on('trhspp.no_spp', '=', 'trdspp.no_spp');
                    $join->on('trhspp.kd_skpd', '=', 'trdspp.kd_skpd');
                })->where(['trhspp.kd_skpd' => $kd_skpd])->where(['trhspp.jns_spp' => '3'])->groupBy('trhspp.no_spp', 'trhspp.tgl_spp', 'trhspp.keperluan')
                ->get();
            // Ajax
            if ($request->ajax()) {
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="' . route("spptu.showdata", $row->no_spp) . '" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="fa fa-eye"></i></a>';
                        $btn .= '<a href="javascript:void(0);" onclick="DeleteData(\'' . $row->no_spp . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="fas fa-trash-alt"></i></a>';
                        $btn .= '<a href="javascript:void(0);" onclick="Print(\'' . $row->no_spp . '\' );" class="btn btn-warning btn-sm"><i class="fa fa-print"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    // Showdata
    public function showData(Request $request, $no_spp)
    {
        try {
            $skpd = Auth::user()->kd_skpd;
            $data['data_skpd'] = DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')
                ->where('kd_skpd', $skpd)->first();
            $data['dataspptu'] = DB::table('trhspp as a')->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where('a.no_spp', $no_spp)->select('a.*')->first();

            // SPD
            $tgl_spp = prev_month($data['dataspptu']->tgl_spp);
            $data['nomor_spd'] = DB::table('trhspd')
                ->select('trhspd.no_spd', 'trhspd.tgl_spd', DB::raw('SUM(trdspd.nilai)as nilai'))
                ->join('trdspd', function ($join) {
                    $join->on('trhspd.no_spd', '=', 'trdspd.no_spd');
                    $join->on('trhspd.kd_skpd', '=', 'trdspd.kd_unit');
                })->where(['kd_skpd' => $skpd], ['jns_beban', '=', '5'], ['status', '=', '1'])
                ->where('trhspd.bulan_awal', '<=', $tgl_spp)
                ->groupBy('trhspd.no_spd', 'trhspd.tgl_spd')
                ->get();
            //
            // Master Bank
            $data['master_bank'] = DB::table('ms_bank')
                ->select('kode', 'nama')->get();
            //
            // Master Rekening Bank
            $data['rekening_bank'] = DB::table('ms_rekening_bank_online')
                ->select('rekening', 'nm_rekening', 'npwp')
                ->where('kd_skpd', $skpd)
                ->get();
            //
            // Master Kode Sub Kegiatan
            // $data['kode_sub_kegiatan'] = $this->getSubKegiatan();
            // dd($data['kode_sub_kegiatan']);
            // return;
            $data['detailspptu'] = DB::table('trdspp as a')->select('a.*', 'c.nm_sumber_dana1')->join('trhspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->join('sumber_dana as c', 'a.sumber', '=', 'c.kd_sumber_dana1')->where('a.no_spp', $no_spp)->get();

            return view('penatausahaan.pengeluaran.spp_tu.edit', $data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    // Create
    function create()
    {
        try {
            $skpd = Auth::user()->kd_skpd;
            $data['data_skpd'] = DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')
                ->where('kd_skpd', $skpd)->first();
            $data['master_bank'] = DB::table('ms_bank')
                ->select('kode', 'nama')->get();
            return view('penatausahaan.pengeluaran.spp_tu.create', $data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    // Get No SPP TU
    function getNospp(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhspp')->selectRaw("ISNULL(MAX(urut),0)+1 as nilai")->where('kd_skpd', $kd_skpd)->first();
        return response()->json($data);
    }

    // Get getSPD
    public function getSPD(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $tgl_spp = prev_month($request->tglspp);
        $data = DB::table('trhspd')
            ->select('trhspd.no_spd', 'trhspd.tgl_spd', DB::raw('SUM(trdspd.nilai)as nilai'))
            ->join('trdspd', function ($join) {
                $join->on('trhspd.no_spd', '=', 'trdspd.no_spd');
                $join->on('trhspd.kd_skpd', '=', 'trdspd.kd_unit');
            })->where(['kd_skpd' => $kd_skpd], ['jns_beban', '=', '5'], ['status', '=', '1'])
            ->where('trhspd.bulan_awal', '<=', $tgl_spp)
            ->groupBy('trhspd.no_spd', 'trhspd.tgl_spd')
            ->get();
        return response()->json($data);
    }

    // Get Rekening Bank
    public function getRekeningBank(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('ms_rekening_bank_online')
            ->select('rekening', 'nm_rekening', 'npwp')
            ->where('kd_skpd', $kd_skpd)
            ->get();
        return response()->json($data);
    }

    // Get RekeningKegiatan
    public function getRekening(Request $request)
    {
        try {
            $kd_skpd = Auth::user()->kd_skpd;
            $kd_sub = $request->ckd_subkeg;
            $jns_anggaran = status_anggaran_new();
            $jns_ang = $jns_anggaran->jns_ang;
            $data = DB::table('trdrka')
                ->select('kd_rek6', 'nm_rek6')
                ->where('kd_skpd', '=', $kd_skpd)
                ->where('jns_ang', '=', $jns_ang)
                ->where('kd_sub_kegiatan', '=', $kd_sub)
                ->get();
            return response()->json($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    // Get Sub Kegiatan
    public function getSubKegiatan(Request $request)
    {
        try {
            $kd_skpd = Auth::user()->kd_skpd;
            $jns_anggaran = status_anggaran_new();
            $jns_ang = $jns_anggaran->jns_ang;
            $no_spd = $request->cspd;
            // dd($no_spd);
            // return;
            $kartu_kendali = DB::table('tb_kendali_tu')->select('status', 'kd_sub_kegiatan')->where('kd_skpd', '=', $kd_skpd)->get();
            foreach ($kartu_kendali as $kendali) {
                $statusproteksi     = $kendali->status;
                $subkegiatanproteksi = $kendali->kd_sub_kegiatan;
            }
            if ($statusproteksi == '1') {
                $data  = DB::select("SELECT kd_sub_kegiatan, nm_sub_kegiatan, kd_program, nm_program FROM (SELECT DISTINCT a.kd_sub_kegiatan,b.nm_sub_kegiatan,a.kd_program,b.nm_program FROM trdspd a INNER JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan where b.kd_skpd = ? AND a.no_spd= ? AND b.status_sub_kegiatan='1' AND b.jns_ang= ? ) h WHERE h.kd_sub_kegiatan NOT IN (SELECT b.kd_sub_kegiatan FROM trhspp a INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd WHERE a.jns_spp='3' AND a.kd_skpd = ? and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1') AND b.no_spp NOT IN (SELECT no_spp from trhsp2d where kd_skpd = ? and jns_spp ='3') GROUP BY b.kd_sub_kegiatan
                UNION ALL
                SELECT b.kd_sub_kegiatan FROM trhspp a
                INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhsp2d c on a.no_spp=b.no_spp and a.kd_skpd=c.kd_skpd
                WHERE a.jns_spp='3' AND a.kd_skpd = ?
                AND b.kd_sub_kegiatan not in ('') AND (a.sp2d_batal is null or a.sp2d_batal<>'1')
                AND b.kd_sub_kegiatan IN (SELECT e.kd_sub_kegiatan FROM trhtransout_cmsbank f INNER JOIN trdtransout_cmsbank e on f.no_voucher=e.no_voucher
                AND f.kd_skpd=e.kd_skpd AND jns_spp='3'
                WHERE f.kd_skpd = ? AND f.jns_spp ='3' and f.status_validasi<>'1')
                UNION ALL
                SELECT b.kd_sub_kegiatan FROM trhspp a
                INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhsp2d c on a.no_spp=b.no_spp AND a.kd_skpd=c.kd_skpd
                WHERE a.jns_spp='3' AND a.kd_skpd = ?
                AND b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1')
                AND b.kd_sub_kegiatan IN (SELECT e.kd_sub_kegiatan FROM trhtransout f INNER JOIN trdtransout e on f.no_bukti=e.no_bukti
                AND f.kd_skpd=e.kd_skpd
                where f.kd_skpd = ? and f.jns_spp ='3'
                and f.no_bukti not in (select no_bukti FROM trlpj g inner join trhlpj_tu h on g.no_lpj=h.no_lpj where h.kd_skpd = ? and jenis='3')) GROUP BY b.kd_sub_kegiatan
                UNION ALL
                select kd_sub_kegiatan FROM trhlpj_tu a inner join trlpj b on a.no_lpj=b.no_lpj and a.kd_skpd=b.kd_skpd WHERE status<>'1' AND jenis = '3' AND a.kd_skpd = ?)
                union all
                SELECT kd_sub_kegiatan, nm_sub_kegiatan, kd_program, nm_program FROM (SELECT DISTINCT a.kd_sub_kegiatan,b.nm_sub_kegiatan,a.kd_program,b.nm_program FROM trdspd a INNER JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan where b.kd_skpd = ? AND a.no_spd= ? AND b.jns_ang= ? AND b.status_sub_kegiatan='1') h WHERE  h.kd_sub_kegiatan in ('?')", [$kd_skpd, $no_spd, $jns_ang, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $no_spd, $jns_ang, $subkegiatanproteksi]);
            } else {
                $data  = DB::select("SELECT kd_sub_kegiatan, nm_sub_kegiatan, kd_program, nm_program FROM (SELECT DISTINCT a.kd_sub_kegiatan,b.nm_sub_kegiatan,a.kd_program,b.nm_program FROM trdspd a INNER JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan where b.kd_skpd = ? AND a.no_spd= ? AND b.status_sub_kegiatan='1' AND b.jns_ang= ?) h WHERE h.kd_sub_kegiatan NOT IN (
                    SELECT b.kd_sub_kegiatan FROM trhspp a INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd WHERE a.jns_spp='3' AND a.kd_skpd = ? and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1') AND b.no_spp NOT IN (
                    select no_spp from trhsp2d where kd_skpd = ? and jns_spp ='3'
                    ) GROUP BY b.kd_sub_kegiatan
                    UNION ALL
                    SELECT b.kd_sub_kegiatan FROM trhspp a
                    INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
                    INNER JOIN trhsp2d c on a.no_spp=b.no_spp and a.kd_skpd=c.kd_skpd
                    WHERE a.jns_spp='3' AND a.kd_skpd = ?
                    and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1')
                    AND b.kd_sub_kegiatan IN (
                    SELECT e.kd_sub_kegiatan FROM trhtransout_cmsbank f INNER JOIN trdtransout_cmsbank e on f.no_voucher=e.no_voucher
                    AND f.kd_skpd=e.kd_skpd and jns_spp='3'
                    where f.kd_skpd = ? and f.jns_spp ='3' and f.status_validasi<>'1'
                    )
                UNION ALL
                SELECT b.kd_sub_kegiatan FROM trhspp a
                INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhsp2d c on a.no_spp=b.no_spp and a.kd_skpd=c.kd_skpd
                WHERE a.jns_spp='3' AND a.kd_skpd = ?
                and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1')
                AND b.kd_sub_kegiatan IN (
                SELECT e.kd_sub_kegiatan FROM trhtransout f INNER JOIN trdtransout e on f.no_bukti=e.no_bukti
                AND f.kd_skpd=e.kd_skpd
                where f.kd_skpd = ? and f.jns_spp ='3'
                and f.no_bukti not in (SELECT no_bukti FROM trlpj_tu g inner join trhlpj_tu h on g.no_lpj=h.no_lpj where h.kd_skpd= ? and jenis='3')
                ) GROUP BY b.kd_sub_kegiatan
                UNION ALL
                SELECT kd_sub_kegiatan FROM trhlpj_tu a inner join trlpj_tu b on a.no_lpj=b.no_lpj and a.kd_skpd=b.kd_skpd WHERE status<>'1' AND jenis = '3' AND a.kd_skpd= ?)", [$kd_skpd, $no_spd, $jns_ang, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]);
            }
            return response()->json($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function Sumberdana(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_anggaran = status_anggaran_new();
        $jns_ang = $jns_anggaran->jns_ang;
        $giat = $request->ckd_subkeg;
        $rek = $request->ckd_rek;
        $data = DB::select("SELECT sumber as sumber,nm_sumber as nm_sumber_dana,sum(total) as nilai , (SELECT ISNULL(SUM(nilai),0) as nilai FROM trdtagih t  INNER JOIN trhtagih u  ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd WHERE  t.kd_sub_kegiatan = '$giat' AND
        u.kd_skpd = '$kd_skpd' AND t.kd_rek = '$rek' AND u.no_bukti  NOT IN (select no_tagih FROM trhspp WHERE kd_skpd='$kd_skpd' ) and sumber=sumber)as lalu from trdpo where kd_sub_kegiatan = '$giat' and kd_rek6 = '$rek' and kd_skpd = '$kd_skpd' and jns_ang = '$jns_ang' GROUP BY sumber, nm_sumber");

        // $data = DB::select("SELECT nilai, sumber,nm_sumber, jns_ang FROM (SELECT ISNULL(a.nsumber1,0) as nilai, a.sumber1 as sumber, b.nm_sumberdana as nm_sumber, a.jns_ang FROM trdrka a INNER JOIN hsumber_dana b ON b.kd_sumberdana=a.sumber1 WHERE a.kd_skpd='$kd_skpd' AND a.kd_sub_kegiatan='$giat' AND a.kd_rek6='$rek'
        // UNION ALL
        // SELECT ISNULL(a.nsumber2,0) as nilai, a.sumber2 as sumber, b.nm_sumberdana as nm_sumber, a.jns_ang FROM trdrka a INNER JOIN hsumber_dana b ON b.kd_sumberdana=a.sumber2 WHERE a.kd_skpd='$kd_skpd' AND a.kd_sub_kegiatan='$giat' AND a.kd_rek6='$rek'
        // UNION ALL
        // SELECT ISNULL(a.nsumber3,0) as nilai, a.sumber3 as sumber, b.nm_sumberdana as nm_sumber, a.jns_ang FROM trdrka a INNER JOIN hsumber_dana b ON b.kd_sumberdana=a.sumber3 WHERE a.kd_skpd='$kd_skpd' AND a.kd_sub_kegiatan='$giat' AND a.kd_rek6='$rek'
        // UNION ALL
        // SELECT ISNULL(a.nsumber4,0) as nilai, a.sumber4 as sumber, b.nm_sumberdana as nm_sumber, a.jns_ang FROM trdrka a INNER JOIN hsumber_dana b ON b.kd_sumberdana=a.sumber4 WHERE a.kd_skpd='$kd_skpd' AND a.kd_sub_kegiatan='$giat' AND a.kd_rek6='$rek'

        // ) x WHERE jns_ang='$jns_ang' AND nilai <>0 GROUP BY sumber,nm_sumber,jns_ang,nilai
        // ");
        return response()->json($data);
    }

    // total SPD
    public function TotalSPD(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $tgl_spp = $request->ctgl_spp;
        $kdgiat = $request->ckd_subkeg;
        $kdrek = $request->ckd_rek;
        // SPD
        $sql1 = DB::table('trhspd')->selectRaw('MAX(revisi_ke) as revisi')->whereRaw("LEFT(kd_skpd,22) = LEFT('$kd_skpd',22)")->where('bulan_akhir', '3')->where('tgl_spd', '<=', $tgl_spp)->first();
        $revisi1 = $sql1->revisi;
        $sql2 = DB::table('trhspd')->selectRaw('ISNULL(MAX(revisi_ke),0) as revisi')->whereRaw("LEFT(kd_skpd,22) = LEFT('$kd_skpd',22)")->where('bulan_akhir', '6')->where('tgl_spd', '<=', $tgl_spp)->first();
        $revisi2 = $sql2->revisi;
        $sql3 = DB::table('trhspd')->selectRaw('ISNULL(MAX(revisi_ke),0) as revisi')->whereRaw("LEFT(kd_skpd,22) = LEFT('$kd_skpd',22)")->where('bulan_akhir', '9')->where('tgl_spd', '<=', $tgl_spp)->first();
        $revisi3 = $sql3->revisi;
        $sql4 = DB::table('trhspd')->selectRaw('ISNULL(MAX(revisi_ke),0) as revisi')->whereRaw("LEFT(kd_skpd,22) = LEFT('$kd_skpd',22)")->where('bulan_akhir', '12')->where('tgl_spd', '<=', $tgl_spp)->first();
        $revisi4 = $sql4->revisi;
        // End

        $spd1 = DB::table('trdspd as a')->select(DB::raw("'TW1' as ket"), DB::raw("ISNULL(SUM(a.nilai),0) as nilai"))->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->where(['a.kd_unit' => $kd_skpd, 'a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'b.status' => '1', 'bulan_akhir' => '3', 'revisi_ke' => $revisi1])->where('tgl_spd', '<=', $tgl_spp)->where('bulan_awal', '<=', DB::raw("MONTH('$tgl_spp')"));
        $spd2 = DB::table('trdspd as a')->select(DB::raw("'TW2' as ket"), DB::raw("ISNULL(SUM(a.nilai),0) as nilai"))->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->where(['a.kd_unit' => $kd_skpd, 'a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'b.status' => '1', 'bulan_akhir' => '6', 'revisi_ke' => $revisi2])->where('tgl_spd', '<=', $tgl_spp)->where('bulan_awal', '<=', DB::raw("MONTH('$tgl_spp')"))->unionAll($spd1);
        $spd3 = DB::table('trdspd as a')->select(DB::raw("'TW3' as ket"), DB::raw("ISNULL(SUM(a.nilai),0) as nilai"))->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->where(['a.kd_unit' => $kd_skpd, 'a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'b.status' => '1', 'bulan_akhir' => '9', 'revisi_ke' => $revisi3])->where('tgl_spd', '<=', $tgl_spp)->where('bulan_awal', '<=', DB::raw("MONTH('$tgl_spp')"))->unionAll($spd2);
        $spd4 = DB::table('trdspd as a')->select(DB::raw("'TW3' as ket"), DB::raw("ISNULL(SUM(a.nilai),0) as nilai"))->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->where(['a.kd_unit' => $kd_skpd, 'a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'b.status' => '1', 'bulan_akhir' => '12', 'revisi_ke' => $revisi4])->where('tgl_spd', '<=', $tgl_spp)->where('bulan_awal', '<=', DB::raw("MONTH('$tgl_spp')"))->unionAll($spd3);

        $data = DB::table(DB::raw("({$spd4->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as total_spd"))
            ->mergeBindings($spd4)
            ->first();
        return response()->json($data);
    }

    // Total Angkas
    public function TotalAngkas(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_anggaran = status_anggaran_new();
        $jns_ang = $jns_anggaran->jns_ang;
        $tgl_spp = $request->ctgl_spp;
        $bulan = date('m', strtotime($tgl_spp));
        $kd_rek = $request->ckd_rek;
        $kd_subkeg = $request->ckd_subkeg;
        $beban = $request->cbeban;
        $status_angkas = $request->cstatus_angkas;

        if ($status_angkas == 'Murni') {
            $field = 'nilai_susun';
        } else if ($status_angkas == 'Murni Geser I') {
            $field = 'nilai_susun1';
        } else if ($status_angkas == 'Murni Geser II') {
            $field = 'nilai_susun2';
        } else if ($status_angkas == 'Murni Geser III') {
            $field = 'nilai_susun3';
        } else if ($status_angkas == 'Murni Geser IV') {
            $field = 'nilai_susun4';
        } else if ($status_angkas == 'Murni Geser V') {
            $field = 'nilai_susun5';
        } else if ($status_angkas == 'Penyempurnaan I') {
            $field = 'nilai_sempurna';
        } else if ($status_angkas == 'Penyempurnaan I Geser I') {
            $field = 'nilai_sempurna11';
        } else if ($status_angkas == 'Penyempurnaan I Geser II') {
            $field = 'nilai_sempurna12';
        } else if ($status_angkas == 'Penyempurnaan I Geser III') {
            $field = 'nilai_sempurna13';
        } else if ($status_angkas == 'Penyempurnaan I Geser IV') {
            $field = 'nilai_sempurna14';
        } else if ($status_angkas == 'Penyempurnaan I Geser V') {
            $field = 'nilai_sempurna15';
        } else if ($status_angkas == 'Penyempurnaan II') {
            $field = 'nilai_sempurna2';
        } else if ($status_angkas == 'Penyempurnaan II Geser I') {
            $field = 'nilai_sempurna21';
        } else if ($status_angkas == 'Penyempurnaan II Geser II') {
            $field = 'nilai_sempurna22';
        } else if ($status_angkas == 'Penyempurnaan II Geser III') {
            $field = 'nilai_sempurna23';
        } else if ($status_angkas == 'Penyempurnaan II Geser IV') {
            $field = 'nilai_sempurna24';
        } else if ($status_angkas == 'Penyempurnaan II Geser V') {
            $field = 'nilai_sempurna25';
        }

        if ($beban == '4' || substr($kd_subkeg, 5, 10) == '01.1.02.01') {
            $bulan1 = $bulan  + 1;
        } else {
            $bulan1 = $bulan;
        }

        $data = DB::table('trdskpd_ro as a')->select('a.kd_sub_kegiatan', 'kd_rek6', DB::raw("SUM(a.$field) as nilai"))->join('trskpd as b', function ($join) {
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
        })->where([
            'a.kd_skpd' => $kd_skpd,
            'a.kd_sub_kegiatan' => $kd_subkeg,
            'a.kd_rek6' => $kd_rek,
            'jns_ang' => $jns_ang
        ])
            ->where('bulan', '<=', $bulan1)
            ->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')->first();

        return response()->json($data);
    }


    public function TotalAnggaran(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_anggaran = status_anggaran_new();
        $jns_ang = $jns_anggaran->jns_ang;
        $kd_rek = $request->ckd_rek;
        $kd_subkeg = $request->ckd_subkeg;
        $rektotal = DB::table('trdrka')->select(DB::raw("SUM(nilai) as rektotal"))->where(['kd_rek6' => $kd_rek, 'kd_sub_kegiatan' => $kd_subkeg, 'jns_ang' => $jns_ang, 'kd_skpd' => $kd_skpd])->first();

        return response()->json([
            'totalanggaran' => $rektotal->rektotal
        ]);
    }

    public function Realisasi(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_anggaran = status_anggaran_new();
        $jns_ang = $jns_anggaran->jns_ang;
        $kd_rek = $request->ckd_rek;
        $kd_subkeg = $request->ckd_subkeg;
        $kd_sumberdana = $request->ckd_sumberdana;

        $query1 = DB::table('trdspp as a')->select(DB::raw("SUM(a.nilai) as nilai"))->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_sub_kegiatan' => $kd_subkeg, 'a.kd_rek6' => $kd_rek, 'a.kd_skpd' => $kd_skpd, 'a.sumber' => $kd_sumberdana])
            ->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
                $query->where('b.sp2d_batal', '<>', '1')
                    ->orWhereNull('b.sp2d_batal');
            });

        $query2 = DB::table('trdtagih as t')->select(DB::raw("SUM(nilai) as nilai"))->join('trhtagih as u', function ($join) {
            $join->on('t.no_bukti', '=', 'u.no_bukti');
            $join->on('t.kd_skpd', '=', 'u.kd_skpd');
        })->where(['t.kd_sub_kegiatan' => $kd_subkeg, 't.kd_rek' => $kd_rek, 'u.kd_skpd' => $kd_skpd, 't.sumber' => $kd_sumberdana])->unionAll($query1);

        $query3 = DB::table('trdtransout as a')->select(DB::raw("SUM(a.nilai) as nilai"))->join('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_sub_kegiatan' => $kd_subkeg, 'a.kd_rek6' => $kd_rek, 'a.kd_skpd' => $kd_skpd, 'a.sumber' => $kd_sumberdana])->whereIn('b.jns_spp', ['1', '2'])->unionAll($query2);

        $query4 = DB::table('trdtransout as a')->select(DB::raw("SUM(a.nilai) as nilai"))->join('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_sub_kegiatan' => $kd_subkeg, 'a.kd_rek6' => $kd_rek, 'a.kd_skpd' => $kd_skpd, 'a.sumber' => $kd_sumberdana])->whereIn('b.jns_spp', ['4', '6'])->whereIn('panjar', ['3'])->unionAll($query3);

        $query5 = DB::table('trdtransout_cmsbank as a')->select(DB::raw("SUM(a.nilai) as nilai"))->join('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_sub_kegiatan' => $kd_subkeg, 'a.kd_rek6' => $kd_rek, 'a.kd_skpd' => $kd_skpd, 'b.status_validasi' => '0', 'a.sumber' => $kd_sumberdana])->unionAll($query4);

        $result = DB::table(DB::raw("({$query5->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as rektotal_spp_lalu"))
            ->mergeBindings($query5)
            ->first();

        return response()->json([
            'totalrealisasi' => $result->rektotal_spp_lalu
        ]);
    }

    // ceksimpan
    public function CekSimpan(Request $request)
    {
        $no_spp = $request->cno_spp;
        $data = DB::table('trhspp')->where('no_spp', $no_spp)->count();
        return response()->json($data);
    }
    // simpandata
    public function SimpanData(Request $request)
    {
        try {
            $data1 = $request->cdata1;
            $data2 = $request->cdata2;

            $data = DB::table('ms_program')->select('kd_program', 'nm_program')->where(['kd_program' => LEFT($data2['kd_subkeg'], 7)])->first();
            // Detail
            DB::table('trhspp')->insert([
                'kd_skpd' => $data2['kd_skpd'],
                'nm_skpd' => $data2['nm_skpd'],
                'tgl_spp' => $data2['tgl_spp'],
                'jns_spp' => $data2['beban'],
                'no_spd' => $data2['no_spd'],
                'kd_sub_kegiatan' => $data2['kd_subkeg'],
                'nm_sub_kegiatan' => $data2['nm_subkeg'],
                'kd_program' => $data->kd_program,
                'nm_program' => $data->nm_program,
                'bank' => $data2['bank'],
                'keperluan' => $data2['keperluan'],
                'username' => Auth::user()->nama,
                'bulan' => $data2['kebutuhan_bulan'],
                'no_spp' => $data2['no_spp'],
                'no_rek' => $data2['rek_bank'],
                'nmrekan' => $data2['nm_rekening'],
                'npwp' => $data2['npwp'],
                'nilai' => $data2['total_nilai']
            ]);
            // Rincian Rekening SPP
            DB::table('trdspp')->insert(array_map(function ($data1) use ($data2) {
                return [
                    'no_spp' => $data2['no_spp'],
                    'kd_skpd' => $data2['kd_skpd'],
                    'nm_skpd' => $data2['nm_skpd'],
                    'kd_sub_kegiatan' => $data2['kd_subkeg'],
                    'nm_sub_kegiatan' => $data2['nm_subkeg'],
                    'kd_rek6' => $data1['kd_rek6'],
                    'nm_rek6' => $data1['nm_rek6'],
                    'nilai' => $data1['nilai'],
                    'no_spd' => $data2['no_spd'],
                    'kd_bidang' => $data2['kd_skpd'],
                    'sumber' => $data1['sumber']
                ];
            }, $data1));
            DB::commit();
            return response()->json([
                'message' => '2'
            ]);
        } catch (Exception $e) {
            echo $e->getMessage("Data Error");
            die();
        }
    }

    // Hapus Data
    public function HapusData(Request $request)
    {
        $no_spp = $request->cno_spp;
        $kd_skpd = Auth::user()->kd_skpd;
        DB::beginTransaction();
        try {
            DB::table('trhspp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->delete();
            DB::table('trdspp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->delete();
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => '0'
            ]);
        }
    }
}
