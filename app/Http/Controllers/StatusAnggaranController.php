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

use PDF;

class StatusAnggaranController extends Controller
{
    function StatusAnggaran(Request $request)
    {
        if ($request->ajax()) {
            $skpd       = Auth::user()->kd_skpd;
            $data = DB::table('trhrka as a')->join('tb_status_anggaran as b', 'a.jns_ang', '=', 'b.kode')->select('nama', 'jns_ang')->where(['a.kd_skpd' => $skpd, 'status' => '1'])->orderBy('tgl_dpa', 'DESC')->first();
            return response()->json($data);
        }
    }

    function StatusAnggaranKas(Request $request)
    {
        if ($request->ajax()) {
            $skpd       = Auth::user()->kd_skpd;
            $urut1 = DB::table('status_angkas')->select(DB::raw("'1' AS urut"), DB::raw("'Murni' AS status"), 'murni as nilai')->where(['kd_skpd' => $skpd, 'murni' => '1']);
            $urut2 = DB::table('status_angkas')->select(DB::raw("'2' AS urut"), DB::raw("'Murni Geser I' AS status"), 'murni_geser1 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser1' => '1'])->unionAll($urut1);
            $urut3 = DB::table('status_angkas')->select(DB::raw("'3' AS urut"), DB::raw("'Murni Geser II' AS status"), 'murni_geser2 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser2' => '1'])->unionAll($urut2);
            $urut4 = DB::table('status_angkas')->select(DB::raw("'4' AS urut"), DB::raw("'Murni Geser III' AS status"), 'murni_geser3 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser3' => '1'])->unionAll($urut3);
            $urut5 = DB::table('status_angkas')->select(DB::raw("'5' AS urut"), DB::raw("'Murni Geser IV' AS status"), 'murni_geser4 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser4' => '1'])->unionAll($urut4);
            $urut6 = DB::table('status_angkas')->select(DB::raw("'6' AS urut"), DB::raw("'Murni Geser V' AS status"), 'murni_geser5 as nilai')->where(['kd_skpd' => $skpd, 'murni_geser5' => '1'])->unionAll($urut5);
            $urut7 = DB::table('status_angkas')->select(DB::raw("'7' AS urut"), DB::raw("'Penyempurnaan I' AS status"), 'sempurna1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1' => '1'])->unionAll($urut6);
            $urut8 = DB::table('status_angkas')->select(DB::raw("'8' AS urut"), DB::raw("'Penyempurnaan I Geser I' AS status"), 'sempurna1_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser1' => '1'])->unionAll($urut7);
            $urut9 = DB::table('status_angkas')->select(DB::raw("'9' AS urut"), DB::raw("'Penyempurnaan I Geser II' AS status"), 'sempurna1_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser2' => '1'])->unionAll($urut8);
            $urut10 = DB::table('status_angkas')->select(DB::raw("'10' AS urut"), DB::raw("'Penyempurnaan I Geser III' AS status"), 'sempurna1_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser3' => '1'])->unionAll($urut9);
            $urut11 = DB::table('status_angkas')->select(DB::raw("'11' AS urut"), DB::raw("'Penyempurnaan I Geser IV' AS status"), 'sempurna1_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser4' => '1'])->unionAll($urut10);
            $urut12 = DB::table('status_angkas')->select(DB::raw("'12' AS urut"), DB::raw("'Penyempurnaan I Geser V' AS status"), 'sempurna1_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna1_geser5' => '1'])->unionAll($urut11);
            $urut13 = DB::table('status_angkas')->select(DB::raw("'13' AS urut"), DB::raw("'Penyempurnaan II' AS status"), 'sempurna2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2' => '1'])->unionAll($urut12);
            $urut14 = DB::table('status_angkas')->select(DB::raw("'14' AS urut"), DB::raw("'Penyempurnaan II Geser I' AS status"), 'sempurna2_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser1' => '1'])->unionAll($urut13);
            $urut15 = DB::table('status_angkas')->select(DB::raw("'15' AS urut"), DB::raw("'Penyempurnaan II Geser II' AS status"), 'sempurna2_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser2' => '1'])->unionAll($urut14);
            $urut16 = DB::table('status_angkas')->select(DB::raw("'16' AS urut"), DB::raw("'Penyempurnaan II Geser III' AS status"), 'sempurna2_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser3' => '1'])->unionAll($urut15);
            $urut17 = DB::table('status_angkas')->select(DB::raw("'17' AS urut"), DB::raw("'Penyempurnaan II Geser IV' AS status"), 'sempurna2_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser4' => '1'])->unionAll($urut16);
            $urut18 = DB::table('status_angkas')->select(DB::raw("'18' AS urut"), DB::raw("'Penyempurnaan II Geser V' AS status"), 'sempurna2_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna2_geser5' => '1'])->unionAll($urut17);
            $urut19 = DB::table('status_angkas')->select(DB::raw("'19' AS urut"), DB::raw("'Penyempurnaan III' AS status"), 'sempurna3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3' => '1'])->unionAll($urut18);
            $urut20 = DB::table('status_angkas')->select(DB::raw("'20' AS urut"), DB::raw("'Penyempurnaan III Geser I' AS status"), 'sempurna3_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser1' => '1'])->unionAll($urut19);
            $urut21 = DB::table('status_angkas')->select(DB::raw("'21' AS urut"), DB::raw("'Penyempurnaan III Geser II' AS status"), 'sempurna3_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser2' => '1'])->unionAll($urut20);
            $urut22 = DB::table('status_angkas')->select(DB::raw("'22' AS urut"), DB::raw("'Penyempurnaan III Geser III' AS status"), 'sempurna3_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser3' => '1'])->unionAll($urut21);
            $urut23 = DB::table('status_angkas')->select(DB::raw("'23' AS urut"), DB::raw("'Penyempurnaan III Geser IV' AS status"), 'sempurna3_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser4' => '1'])->unionAll($urut22);
            $urut24 = DB::table('status_angkas')->select(DB::raw("'24' AS urut"), DB::raw("'Penyempurnaan III Geser V' AS status"), 'sempurna3_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna3_geser5' => '1'])->unionAll($urut23);
            $urut25 = DB::table('status_angkas')->select(DB::raw("'25' AS urut"), DB::raw("'Penyempurnaan IV' AS status"), 'sempurna4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4' => '1'])->unionAll($urut24);
            $urut26 = DB::table('status_angkas')->select(DB::raw("'26' AS urut"), DB::raw("'Penyempurnaan IV Geser I' AS status"), 'sempurna4_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser1' => '1'])->unionAll($urut25);
            $urut27 = DB::table('status_angkas')->select(DB::raw("'27' AS urut"), DB::raw("'Penyempurnaan IV Geser II' AS status"), 'sempurna4_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser2' => '1'])->unionAll($urut26);
            $urut28 = DB::table('status_angkas')->select(DB::raw("'28' AS urut"), DB::raw("'Penyempurnaan IV Geser III' AS status"), 'sempurna4_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser3' => '1'])->unionAll($urut27);
            $urut29 = DB::table('status_angkas')->select(DB::raw("'29' AS urut"), DB::raw("'Penyempurnaan IV Geser IV' AS status"), 'sempurna4_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser4' => '1'])->unionAll($urut28);
            $urut30 = DB::table('status_angkas')->select(DB::raw("'30' AS urut"), DB::raw("'Penyempurnaan IV Geser V' AS status"), 'sempurna4_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna4_geser5' => '1'])->unionAll($urut29);
            $urut31 = DB::table('status_angkas')->select(DB::raw("'31' AS urut"), DB::raw("'Penyempurnaan V' AS status"), 'sempurna5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5' => '1'])->unionAll($urut30);
            $urut32 = DB::table('status_angkas')->select(DB::raw("'32' AS urut"), DB::raw("'Penyempurnaan V Geser I' AS status"), 'sempurna5_geser1 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser1' => '1'])->unionAll($urut31);
            $urut33 = DB::table('status_angkas')->select(DB::raw("'33' AS urut"), DB::raw("'Penyempurnaan V Geser II' AS status"), 'sempurna5_geser2 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser2' => '1'])->unionAll($urut32);
            $urut34 = DB::table('status_angkas')->select(DB::raw("'34' AS urut"), DB::raw("'Penyempurnaan V Geser III' AS status"), 'sempurna5_geser3 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser3' => '1'])->unionAll($urut33);
            $urut35 = DB::table('status_angkas')->select(DB::raw("'35' AS urut"), DB::raw("'Penyempurnaan V Geser IV' AS status"), 'sempurna5_geser4 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser4' => '1'])->unionAll($urut34);
            $urut36 = DB::table('status_angkas')->select(DB::raw("'36' AS urut"), DB::raw("'Penyempurnaan V Geser V' AS status"), 'sempurna5_geser5 as nilai')->where(['kd_skpd' => $skpd, 'sempurna5_geser5' => '1'])->unionAll($urut35);
            $urut37 = DB::table('status_angkas')->select(DB::raw("'37' AS urut"), DB::raw("'Ubah' AS status"), 'ubah as nilai')->where(['kd_skpd' => $skpd, 'ubah' => '1'])->unionAll($urut36);
            $urut38 = DB::table('status_angkas')->select(DB::raw("'38' AS urut"), DB::raw("'Ubah I Geser I' AS status"), 'ubah11 as nilai')->where(['kd_skpd' => $skpd, 'ubah11' => '1'])->unionAll($urut37);
            $urut39 = DB::table('status_angkas')->select(DB::raw("'39' AS urut"), DB::raw("'Ubah I Geser II' AS status"), 'ubah12 as nilai')->where(['kd_skpd' => $skpd, 'ubah12' => '1'])->unionAll($urut38);
            $urut40 = DB::table('status_angkas')->select(DB::raw("'40' AS urut"), DB::raw("'Ubah I Geser III' AS status"), 'ubah13 as nilai')->where(['kd_skpd' => $skpd, 'ubah13' => '1'])->unionAll($urut39);
            $urut41 = DB::table('status_angkas')->select(DB::raw("'41' AS urut"), DB::raw("'Ubah I Geser IV' AS status"), 'ubah14 as nilai')->where(['kd_skpd' => $skpd, 'ubah14' => '1'])->unionAll($urut40);
            $urut42 = DB::table('status_angkas')->select(DB::raw("'42' AS urut"), DB::raw("'Ubah I Geser V' AS status"), 'ubah15 as nilai')->where(['kd_skpd' => $skpd, 'ubah15' => '1'])->unionAll($urut41);
            $urut43 = DB::table('status_angkas')->select(DB::raw("'43' AS urut"), DB::raw("'Ubah II' AS status"), 'ubah2 as nilai')->where(['kd_skpd' => $skpd, 'ubah2' => '1'])->unionAll($urut42);
            $urut44 = DB::table('status_angkas')->select(DB::raw("'44' AS urut"), DB::raw("'Ubah II Geser I' AS status"), 'ubah21 as nilai')->where(['kd_skpd' => $skpd, 'ubah21' => '1'])->unionAll($urut43);
            $urut45 = DB::table('status_angkas')->select(DB::raw("'45' AS urut"), DB::raw("'Ubah II Geser II' AS status"), 'ubah22 as nilai')->where(['kd_skpd' => $skpd, 'ubah22' => '1'])->unionAll($urut44);
            $urut46 = DB::table('status_angkas')->select(DB::raw("'46' AS urut"), DB::raw("'Ubah II Geser III' AS status"), 'ubah23 as nilai')->where(['kd_skpd' => $skpd, 'ubah23' => '1'])->unionAll($urut45);
            $urut47 = DB::table('status_angkas')->select(DB::raw("'47' AS urut"), DB::raw("'Ubah II Geser IV' AS status"), 'ubah24 as nilai')->where(['kd_skpd' => $skpd, 'ubah24' => '1'])->unionAll($urut46);
            $urut48 = DB::table('status_angkas')->select(DB::raw("'48' AS urut"), DB::raw("'Ubah II Geser V' AS status"), 'ubah25 as nilai')->where(['kd_skpd' => $skpd, 'ubah25' => '1'])->unionAll($urut47);
            $urut49 = DB::table('status_angkas')->select(DB::raw("'49' AS urut"), DB::raw("'Ubah III' AS status"), 'ubah3 as nilai')->where(['kd_skpd' => $skpd, 'ubah3' => '1'])->unionAll($urut48);
            $urut50 = DB::table('status_angkas')->select(DB::raw("'50' AS urut"), DB::raw("'Ubah III Geser I' AS status"), 'ubah31 as nilai')->where(['kd_skpd' => $skpd, 'ubah31' => '1'])->unionAll($urut49);
            $urut51 = DB::table('status_angkas')->select(DB::raw("'51' AS urut"), DB::raw("'Ubah III Geser II' AS status"), 'ubah32 as nilai')->where(['kd_skpd' => $skpd, 'ubah32' => '1'])->unionAll($urut50);
            $urut52 = DB::table('status_angkas')->select(DB::raw("'52' AS urut"), DB::raw("'Ubah III Geser III' AS status"), 'ubah33 as nilai')->where(['kd_skpd' => $skpd, 'ubah33' => '1'])->unionAll($urut51);
            $urut53 = DB::table('status_angkas')->select(DB::raw("'53' AS urut"), DB::raw("'Ubah III Geser IV' AS status"), 'ubah34 as nilai')->where(['kd_skpd' => $skpd, 'ubah34' => '1'])->unionAll($urut52);
            $urut54 = DB::table('status_angkas')->select(DB::raw("'54' AS urut"), DB::raw("'Ubah III Geser V' AS status"), 'ubah35 as nilai')->where(['kd_skpd' => $skpd, 'ubah35' => '1'])->unionAll($urut53);
            $result = DB::table(DB::raw("({$urut54->toSql()}) AS sub"))
                ->select("urut", "status", "nilai")
                ->mergeBindings($urut54)
                ->orderByRaw("CAST(urut AS INT) DESC")
                ->first();
            return response()->json($result);
        }
    }
}
