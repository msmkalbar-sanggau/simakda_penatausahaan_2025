<?php

namespace App\Http\Controllers\Skpd\BOS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransaksiBosController extends Controller
{
    public function index()
    {
        return view('skpd.transaksi_bos.index');
    }

    public function load()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $tgl_terima = collect(DB::select("SELECT ISNULL(MAX(tgl_terima),'2016-01-01') as tgl_terima FROM trhspj_ppkd WHERE cek='1' AND kd_skpd=?", [$kd_skpd]))->first()->tgl_terima;

        $data = DB::select("SELECT a.*,'' AS nokas_pot,'' AS tgl_pot,'' AS kete,(SELECT COUNT(*) from trdsp2b z where z.no_bukti = a.no_bukti and z.kd_skpd = a.kd_skpd) ketlpj,
        (CASE WHEN a.tgl_bukti<? THEN 1 ELSE 0 END ) ketspj FROM trhtransout_blud a
        WHERE   a.kd_skpd=? order by a.no_bukti,kd_skpd", [$tgl_terima, $kd_skpd]);

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("transaksi_bos.edit", ['no_kas' => Crypt::encrypt($row->no_kas), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->ketlpj == 1 || $row->ketspj == 1) {
                $btn .= '';
            } else {
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_kas . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_rekening' => DB::select("SELECT kd_rek6,nm_rek6 from ms_rek6 where left(kd_rek6,4)=?", ['1101']),
            'no_urut' => no_urut($kd_skpd)
        ];

        return view('skpd.transaksi_bos.create')->with($data);
    }

    public function status(Request $request)
    {
        $tanggal = $request->tanggal;
        $kd_skpd = Auth::user()->kd_skpd;

        $status_ang = collect(DB::select("SELECT nama,jns_ang from trhrka a inner join tb_status_anggaran b ON a.jns_ang=b.kode where a.kd_skpd =? and status=? and tgl_dpa <= ? order by tgl_dpa DESC", [$kd_skpd, '1', $tanggal]))->first();

        $status_angkas = collect(DB::select("SELECT TOP 1 * from (
            select '1'as urut,'murni' as status,murni as nilai from status_angkas where kd_skpd =?
            UNION ALL
            select '2'as urut,'murni_geser1',murni_geser1 from status_angkas where kd_skpd =?
            UNION ALL
            select '3'as urut,'murni_geser2',murni_geser2 from status_angkas where kd_skpd =?
            UNION ALL
            select '4'as urut,'murni_geser3',murni_geser3 from status_angkas where kd_skpd =?
            UNION ALL
            select '5'as urut,'murni_geser4',murni_geser4 from status_angkas where kd_skpd =?
            UNION ALL
            select '6'as urut,'murni_geser5',murni_geser5 from status_angkas where kd_skpd =?
            UNION ALL
            select '7'as urut,'sempurna1',sempurna1 from status_angkas where kd_skpd =?
            UNION ALL
            select '8'as urut,'sempurna1_geser1',sempurna1_geser1 from status_angkas where kd_skpd =?
            UNION ALL
            select '9'as urut,'sempurna1_geser2',sempurna1_geser2 from status_angkas where kd_skpd =?
            UNION ALL
            select '10'as urut,'sempurna1_geser3',sempurna1_geser3 from status_angkas where kd_skpd =?
            UNION ALL
            select '11'as urut,'sempurna1_geser4',sempurna1_geser4 from status_angkas where kd_skpd =?
            UNION ALL
            select '12'as urut,'sempurna1_geser5',sempurna1_geser5 from status_angkas where kd_skpd =?
            UNION ALL
            select '13'as urut,'sempurna2',sempurna2 from status_angkas where kd_skpd =?
            UNION ALL
            select '14'as urut,'sempurna2_geser1',sempurna2_geser1 from status_angkas where kd_skpd =?
            UNION ALL
            select '15'as urut,'sempurna2_geser2',sempurna2_geser2 from status_angkas where kd_skpd =?
            UNION ALL
            select '16'as urut,'sempurna2_geser3',sempurna2_geser3 from status_angkas where kd_skpd =?
            UNION ALL
            select '17'as urut,'sempurna2_geser4',sempurna2_geser4 from status_angkas where kd_skpd =?
            UNION ALL
            select '18'as urut,'sempurna2_geser5',sempurna2_geser5 from status_angkas where kd_skpd =?
            UNION ALL
            select '19'as urut,'sempurna3',sempurna3 from status_angkas where kd_skpd =?
            UNION ALL
            select '20'as urut,'sempurna3_geser1',sempurna3_geser1 from status_angkas where kd_skpd =?
            UNION ALL
            select '21'as urut,'sempurna3_geser2',sempurna3_geser2 from status_angkas where kd_skpd =?
            UNION ALL
            select '22'as urut,'sempurna3_geser3',sempurna3_geser3 from status_angkas where kd_skpd =?
            UNION ALL
            select '23'as urut,'sempurna3_geser4',sempurna3_geser4 from status_angkas where kd_skpd =?
            UNION ALL
            select '24'as urut,'sempurna3_geser5',sempurna3_geser5 from status_angkas where kd_skpd =?
            UNION ALL
            select '25'as urut,'sempurna4',sempurna4 from status_angkas where kd_skpd =?
            UNION ALL
            select '26'as urut,'sempurna4_geser1',sempurna4_geser1 from status_angkas where kd_skpd =?
            UNION ALL
            select '27'as urut,'sempurna4_geser2',sempurna4_geser2 from status_angkas where kd_skpd =?
            UNION ALL
            select '28'as urut,'sempurna4_geser3',sempurna4_geser3 from status_angkas where kd_skpd =?
            UNION ALL
            select '29'as urut,'sempurna4_geser4',sempurna4_geser4 from status_angkas where kd_skpd =?
            UNION ALL
            select '30'as urut,'sempurna4_geser5',sempurna4_geser5 from status_angkas where kd_skpd =?
            UNION ALL
            select '31'as urut,'sempurna5',sempurna5 from status_angkas where kd_skpd =?
            UNION ALL
            select '32'as urut,'sempurna5_geser1',sempurna5_geser1 from status_angkas where kd_skpd =?
            UNION ALL
            select '33'as urut,'sempurna5_geser2',sempurna5_geser2 from status_angkas where kd_skpd =?
            UNION ALL
            select '34'as urut,'sempurna5_geser3',sempurna5_geser3 from status_angkas where kd_skpd =?
            UNION ALL
            select '35'as urut,'sempurna5_geser4',sempurna5_geser4 from status_angkas where kd_skpd =?
            UNION ALL
            select '36'as urut,'sempurna5_geser5',sempurna5_geser5 from status_angkas where kd_skpd =?
            UNION ALL
            select '37'as urut,'ubah',ubah from status_angkas where kd_skpd =?
            UNION ALL
            select '38'as urut,'ubah11',ubah11 from status_angkas where kd_skpd =?
            UNION ALL
            select '39'as urut,'ubah12',ubah12 from status_angkas where kd_skpd =?
            UNION ALL
            select '40'as urut,'ubah13',ubah13 from status_angkas where kd_skpd =?
            UNION ALL
            select '41'as urut,'ubah14',ubah14 from status_angkas where kd_skpd =?
            UNION ALL
            select '42'as urut,'ubah15',ubah15 from status_angkas where kd_skpd =?
            UNION ALL
            select '43'as urut,'ubah2',ubah2 from status_angkas where kd_skpd =?
            UNION ALL
            select '44'as urut,'ubah21',ubah21 from status_angkas where kd_skpd =?
            UNION ALL
            select '45'as urut,'ubah22',ubah22 from status_angkas where kd_skpd =?
            UNION ALL
            select '46'as urut,'ubah23',ubah23 from status_angkas where kd_skpd =?
            UNION ALL
            select '47'as urut,'ubah24',ubah24 from status_angkas where kd_skpd =?
            UNION ALL
            select '48'as urut,'ubah25',ubah25 from status_angkas where kd_skpd =?
            UNION All
            select '49'as urut,'ubah3',ubah3 from status_angkas where kd_skpd =?
            UNION ALL
            select '50'as urut,'ubah31',ubah31 from status_angkas where kd_skpd =?
            UNION ALL
            select '51'as urut,'ubah32',ubah32 from status_angkas where kd_skpd =?
            UNION ALL
            select '52'as urut,'ubah33',ubah33 from status_angkas where kd_skpd =?
            UNION ALL
            select '53'as urut,'ubah34',ubah34 from status_angkas where kd_skpd =?
            UNION ALL
            select '54'as urut,'ubah35',ubah35 from status_angkas where kd_skpd =?
            )zz where nilai='1' ORDER BY cast(urut as int) DESC", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]))->first();

        return response()->json([
            'status_ang' => $status_ang,
            'status_angkas' => $status_angkas
        ]);
    }

    public function kegiatan(Request $request)
    {
        $jenis_beban = $request->jenis_beban;
        $kd_skpd = $request->kd_skpd;
        $jns_ang = $request->jns_ang;

        $data = DB::select("SELECT DISTINCT a.kd_sub_kegiatan,b.nm_sub_kegiatan,a.kd_program, a.total FROM trskpd a INNER JOIN ms_sub_kegiatan b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan WHERE a.kd_skpd=? AND a.status_sub_kegiatan=? AND left(a.kd_sub_kegiatan,9) in ('1.01.02.1','4.01.04.1') AND a.jns_ang=?", [$kd_skpd, '1', $jns_ang]);

        return response()->json($data);
    }

    public function rekening(Request $request)
    {
        $jenis_beban = $request->jenis_beban;
        $kd_skpd = $request->kd_skpd;
        $no_bukti = $request->no_bukti;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        $data = DB::select("SELECT DISTINCT a.kd_rek6,a.nm_rek6,
                    (SELECT SUM(nilai) FROM
                        (SELECT
                            SUM (c.nilai) as nilai
                        FROM
                            trdtransout_blud c
                        LEFT JOIN trhtransout_blud d ON c.no_bukti = d.no_bukti
                        AND c.kd_skpd = d.kd_skpd
                        WHERE
                            c.kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND d.kd_skpd = a.kd_skpd
                        AND c.kd_rek6 = a.kd_rek6
                        AND c.no_bukti <> ?
                        AND d.jns_spp=?
                        UNION ALL
                        SELECT SUM(x.nilai) as nilai FROM trdspp x
                        INNER JOIN trhspp y
                        ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                        WHERE
                            x.kd_sub_kegiatan = a.kd_sub_kegiatan
                        AND x.kd_skpd = a.kd_skpd
                        AND x.kd_rek6 = a.kd_rek6
                        AND y.jns_spp IN ('3','4','5','6'))r) AS lalu,
                    0 AS sp2d,nilai AS anggaran
                    FROM trdrka a WHERE a.kd_sub_kegiatan= ? AND a.kd_skpd = ?", [$no_bukti, $jenis_beban, $kd_sub_kegiatan, $kd_skpd]);

        return response()->json($data);
    }

    public function sumber(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_bukti = $request->no_bukti;
        $jenis_beban = $request->jenis_beban;
        $rekening = $request->rekening;
        $jns_ang = $request->jns_ang;

        $data = DB::select("SELECT a.sumber1 as kode,b.nm_sumberdana as nama,sum(nsumber1)as nilai,
                (SELECT SUM(nilai) FROM
                        (SELECT
                            SUM (c.nilai) as nilai
                        FROM
                            trdtransout_blud c
                        LEFT JOIN trhtransout_blud d ON c.no_bukti = d.no_bukti
                        AND c.kd_skpd = d.kd_skpd
                        WHERE
                            c.kd_sub_kegiatan = ?
                        AND d.kd_skpd = ?
                        AND c.kd_rek6 = ?
                        AND c.no_bukti <> ?
                        and c.sumber=a.sumber1
                        UNION ALL
                        SELECT SUM(x.nilai) as nilai FROM trdspp x
                        INNER JOIN trhspp y
                        ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                        WHERE
                            x.kd_sub_kegiatan = ?
                        AND x.kd_skpd = ?
                        AND x.kd_rek6 = ?
                        and x.sumber=a.sumber1
                        AND y.jns_spp IN ('3','4','5','6'))r) AS lalu

                FROM trdrka a INNER JOIN hsumber_dana b
                ON a.sumber1=b.kd_sumberdana
                WHERE a.kd_skpd=? AND a.kd_sub_kegiatan= ? and a.jns_ang=? and a.kd_rek6=?
                group by a.sumber1,b.nm_sumberdana

                UNION ALL

                SELECT a.sumber2 as kode,b.nm_sumberdana as nama,sum(nsumber1)as nilai,
                (SELECT SUM(nilai) FROM
                        (SELECT
                            SUM (c.nilai) as nilai
                        FROM
                            trdtransout_blud c
                        LEFT JOIN trhtransout_blud d ON c.no_bukti = d.no_bukti
                        AND c.kd_skpd = d.kd_skpd
                        WHERE
                            c.kd_sub_kegiatan = ?
                        AND d.kd_skpd = ?
                        AND c.kd_rek6 = ?
                        AND c.no_bukti <> ?
                    AND d.jns_spp='1'
                        and c.sumber=a.sumber2
                        UNION ALL
                        SELECT SUM(x.nilai) as nilai FROM trdspp x
                        INNER JOIN trhspp y
                        ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                        WHERE
                            x.kd_sub_kegiatan = ?
                        AND x.kd_skpd = ?
                        AND x.kd_rek6 = ?
                        and x.sumber=a.sumber2
                        AND y.jns_spp IN ('3','4','5','6'))r) AS lalu
                FROM trdrka a INNER JOIN hsumber_dana b
                ON a.sumber2=b.kd_sumberdana
                WHERE a.kd_skpd=? AND a.kd_sub_kegiatan= ? and a.kd_rek6=?
                group by a.sumber2,b.nm_sumberdana", [$kd_sub_kegiatan, $kd_skpd, $rekening, $no_bukti, $kd_sub_kegiatan, $kd_skpd, $rekening, $kd_skpd, $kd_sub_kegiatan, $jns_ang, $rekening, $kd_sub_kegiatan, $kd_skpd, $rekening, $no_bukti, $kd_sub_kegiatan, $kd_skpd, $rekening, $kd_skpd, $kd_sub_kegiatan, $rekening]);

        return response()->json($data);
    }

    public function spd(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_bukti = $request->no_bukti;
        $beban = $request->jenis_beban;
        $rekening = $request->rekening;
        $jns_ang = $request->jns_ang;
        $tgl_bukti = $request->tgl_bukti;

        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='3'", [$kd_skpd]))->first()->revisi;

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='6'", [$kd_skpd]))->first()->revisi;

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='9'", [$kd_skpd]))->first()->revisi;

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='12'", [$kd_skpd]))->first()->revisi;


        $nilai_spd = collect(DB::select("SELECT sum(nilai)as total_spd from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    a.kd_unit = ?
                    AND a.kd_sub_kegiatan = ?
                    AND a.kd_rek6 = ?
                    AND b.status = '1'
                    and bulan_akhir='3'
                    and revisi_ke=?
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    a.kd_unit = ?
                    AND a.kd_sub_kegiatan = ?
                    AND a.kd_rek6 = ?
                    AND b.status = '1'
                    and bulan_akhir='6'
                    and revisi_ke=?
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    a.kd_unit = ?
                    AND a.kd_sub_kegiatan = ?
                    AND a.kd_rek6 = ?
                    AND b.status = '1'
                    and bulan_akhir='9'
                    and revisi_ke=?
                    UNION ALL
                    SELECT
                    'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    a.kd_unit = ?
                    AND a.kd_sub_kegiatan = ?
                    AND a.kd_rek6 = ?
                    AND b.status = '1'
                    and bulan_akhir='12'
                    and revisi_ke=?)spd", [$kd_skpd, $kd_sub_kegiatan, $rekening, $revisi1, $kd_skpd, $kd_sub_kegiatan, $rekening, $revisi2, $kd_skpd, $kd_sub_kegiatan, $rekening, $revisi3, $kd_skpd, $kd_sub_kegiatan, $rekening, $revisi4]))->first();

        $bulan = date('m', strtotime($tgl_bukti));
        $sts_angkas = $request->status_angkas;

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

        $status_anggaran = status_anggaran();

        $hasil = DB::table('trhspd')->select(DB::raw("COUNT(*) as spd"))->whereRaw("LEFT(kd_skpd,17) = LEFT(?,17)", [$kd_skpd])->groupBy('bulan_awal', 'bulan_akhir')->first();

        if ($beban == '4' || substr($kd_sub_kegiatan, 5, 10) == '01.1.02.01') {
            $bulan1 = $bulan  + 1;
            $nilai_angkas = collect(DB::select("SELECT  a.kd_sub_kegiatan, SUM(a.$field_angkas) as nilai FROM trdskpd_ro a INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd and a.kd_sub_kegiatan=b.kd_sub_kegiatan
            where a.kd_skpd = ? and  a.kd_sub_kegiatan = ? and a.kd_rek6=? and (bulan <=?) and jns_ang=? GROUP BY a.kd_sub_kegiatan,a.kd_rek6", [$kd_skpd, $kd_sub_kegiatan, $rekening, $bulan1, $status_anggaran]))->first();
        } else {
            $nilai_angkas = collect(DB::select("SELECT  a.kd_sub_kegiatan, SUM(a.$field_angkas) as nilai FROM trdskpd_ro a INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd and a.kd_sub_kegiatan=b.kd_sub_kegiatan
            where a.kd_skpd = ? and  a.kd_sub_kegiatan = ? and a.kd_rek6=? and (bulan <=?) and jns_ang=? GROUP BY a.kd_sub_kegiatan,a.kd_rek6", [$kd_skpd, $kd_sub_kegiatan, $rekening, $bulan, $status_anggaran]))->first();
        }

        $total_trans = collect(DB::select("SELECT SUM(nilai) total FROM(SELECT SUM (isnull(c.nilai,0)) as nilai
                                    FROM trdtransout_blud c
                                    LEFT JOIN trhtransout_blud d ON c.no_bukti = d.no_bukti
                                    AND c.kd_skpd = d.kd_skpd
                                    WHERE c.kd_sub_kegiatan = ?
                                    AND d.kd_skpd = ?
                                    AND c.kd_rek6 = ?)r", [$kd_sub_kegiatan, $kd_skpd, $rekening]))->first();

        return response()->json([
            'angkas' => $nilai_angkas->nilai,
            'spd' => $nilai_spd->total_spd,
            'transaksi' => $total_trans->total,
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhtransout_blud')
                ->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhtransout_blud')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])
                ->delete();

            DB::table('trhtransout_blud')
                ->insert([
                    'no_kas' => $data['no_bukti'],
                    'tgl_kas' => $data['tgl_bukti'],
                    'no_bukti' => $data['no_bukti'],
                    'tgl_bukti' => $data['tgl_bukti'],
                    'ket' => $data['keterangan'],
                    'username' => Auth::user()->nama,
                    'tgl_update' => date('Y-m-d H:i:s'),
                    'kd_skpd' => $data['kd_skpd'],
                    'nm_skpd' => $data['nm_skpd'],
                    'total' => $data['total'],
                    'jns_spp' => $data['beban'],
                    'pay' => $data['pembayaran'],
                    'no_kas_pot' => $data['no_bukti'],
                    'panjar' => '3',
                    'kd_satdik' => $data['satdik'],
                    'nm_satdik' => $data['nama_satdik'],
                    'tahap' => $data['tahap'],
                    'jns_bos' => $data['jenis_bos'],
                ]);

            DB::table('trdtransout_blud')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])
                ->delete();

            if (isset($data['rincian'])) {
                DB::table('trdtransout_blud')->insert(array_map(function ($value) use ($data, $kd_skpd) {
                    return [
                        'no_bukti' => $data['no_bukti'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'sumber' => $value['sumber'],
                    ];
                }, $data['rincian']));
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

    public function edit($no_kas, $kd_skpd)
    {
        $no_kas = Crypt::decrypt($no_kas);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_rekening' => DB::select("SELECT kd_rek6,nm_rek6 from ms_rek6 where left(kd_rek6,4)=?", ['1101']),
            'bos' => DB::table('trhtransout_blud')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->first(),
            'data_bos' => DB::table('trdtransout_blud as a')
                ->join('trhtransout_blud as b', function ($join) {
                    $join->on('a.no_bukti', '=', 'b.no_bukti');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->select('a.*')
                ->where(['b.no_kas' => $no_kas, 'b.kd_skpd' => $kd_skpd])->get()
        ];

        return view('skpd.transaksi_bos.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhtransout_blud')
                ->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])
                ->count();

            if ($cek > 0 && $data['no_bukti'] != $data['no_simpan']) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhtransout_blud')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])
                ->delete();

            DB::table('trhtransout_blud')
                ->insert([
                    'no_kas' => $data['no_bukti'],
                    'tgl_kas' => $data['tgl_bukti'],
                    'no_bukti' => $data['no_bukti'],
                    'tgl_bukti' => $data['tgl_bukti'],
                    'ket' => $data['keterangan'],
                    'username' => Auth::user()->nama,
                    'tgl_update' => date('Y-m-d H:i:s'),
                    'kd_skpd' => $data['kd_skpd'],
                    'nm_skpd' => $data['nm_skpd'],
                    'total' => $data['total'],
                    'jns_spp' => $data['beban'],
                    'pay' => $data['pembayaran'],
                    'no_kas_pot' => $data['no_bukti'],
                    'panjar' => '3',
                    'kd_satdik' => $data['satdik'],
                    'nm_satdik' => $data['nama_satdik'],
                    'tahap' => $data['tahap'],
                    'jns_bos' => $data['jenis_bos'],
                ]);

            DB::table('trdtransout_blud')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])
                ->delete();

            if (isset($data['rincian'])) {
                DB::table('trdtransout_blud')->insert(array_map(function ($value) use ($data, $kd_skpd) {
                    return [
                        'no_bukti' => $data['no_bukti'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'sumber' => $value['sumber'],
                    ];
                }, $data['rincian']));
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

    public function hapus(Request $request)
    {
        $no_kas = $request->no_kas;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhtransout_blud')
                ->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::table('trdtransout_blud')
                ->where(['no_bukti' => $no_kas, 'kd_skpd' => $kd_skpd])
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
