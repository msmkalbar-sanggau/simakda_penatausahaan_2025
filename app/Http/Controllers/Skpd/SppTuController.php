<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class SppTuController extends Controller
{
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'ttd1' => DB::table('ms_ttd')
                ->where('kd_skpd', $kd_skpd)
                ->whereIn('kode', ['BPP', 'BK'])
                ->get(),
            'ttd2' => DB::table('ms_ttd')
                ->where('kd_skpd', $kd_skpd)
                ->whereIn('kode', ['PPTK'])
                ->get(),
            'ttd3' => DB::table('ms_ttd')
                ->where('kd_skpd', $kd_skpd)
                ->whereIn('kode', ['KPA', 'PA'])
                ->get(),
            'ttd4' => DB::table('ms_ttd')
                ->where('kd_skpd', '5.02.0.00.0.00.02.0000')
                ->whereIn('kode', ['KPA', 'BUD'])
                ->get(),
        ];
        return view('skpd.spp_tu.index')->with($data);
    }

    public function load()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT DISTINCT a.*,b.kd_sub_kegiatan as giat,b.nm_sub_kegiatan as nmgiat from trhspp a INNER JOIN trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd
                WHERE a.kd_skpd = ? and jns_spp=? order by no_spp,kd_skpd", [$kd_skpd, '3']);

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if ($row->status == 1) {
                $btn = "";
            } else {
                // $btn = '<a href="' . route("spp_tu.edit", ['no_spp' => Crypt::encrypt($row->no_spp), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
                $btn = '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_spp . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_spp . '\',\'' . $row->jns_spp . '\',\'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak LPJ" style="margin-right:4px"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambah()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'daftar_bank' => DB::table('ms_skpd')
                ->selectRaw("bank,replace(replace(npwp,'.',''),'-','')as npwp ,(select nama from ms_bank where kode = bank)as nama_bank,rekening")
                ->where(['kd_skpd' => $kd_skpd])
                ->get(),
            'daftar_rekening' => DB::table('ms_rekening_bank_online')
                ->selectRaw("rekening, nm_rekening,npwp")
                ->where(['kd_skpd' => $kd_skpd])
                ->orderBy('rekening')
                ->get(),
            'daftar_spd' => DB::select("SELECT no_spd, tgl_spd from trhspd where left(kd_skpd,17)=left(?,17) and status=? and jns_beban =?", [$kd_skpd, '1', '5'])
        ];

        return view('skpd.spp_tu.create')->with($data);
    }

    public function kegiatan(Request $request)
    {
        $no_spd = $request->no_spd;
        $kd_skpd = Auth::user()->kd_skpd;

        $kendali_tu = collect(DB::select("SELECT * from tb_kendali_tu where kd_skpd=?", [$kd_skpd]))->first();

        $cek = collect(DB::select("SELECT COUNT(a.no_sp2d) as jumlah FROM (
                SELECT a.no_sp2d , a.tgl_kas , DATEDIFF(day,a.tgl_kas,GETDATE()) as selisih
                FROM trhsp2d a join trdspp b on a.no_spp=b.no_spp WHERE a.jns_spp='3' AND a.kd_skpd = ?
                and b.kd_sub_kegiatan not in ('3.27.07.1.02.02') and a.no_sp2d
                NOT IN (select no_sp2d FROM trhlpj_tu WHERE kd_skpd=? AND jenis='3' and status=1))a
                WHERE selisih>30 and no_sp2d not in ('')", [$kd_skpd, $kd_skpd]))->first();

        if ($cek->jumlah > 0) {
            // SP2D KHUSUS
            if ($kd_skpd == '3.27.0.00.0.00.03.0000') {
                $data = DB::select("SELECT a.kd_sub_kegiatan,a.nm_sub_kegiatan,b.kd_program,b.nm_program from trdrka a join
                            trskpd b on a.kd_sub_kegiatan=b.kd_sub_kegiatan where a.kd_sub_kegiatan not in ('3.27.07.1.02.02') AND a.kd_skpd=? and b.status_sub_kegiatan='1'
                            group by a.kd_sub_kegiatan,a.nm_sub_kegiatan,b.kd_program,b.nm_program", [$kd_skpd]);
            } else {
                $data = DB::select("SELECT '-' kd_sub_kegiatan, 'Tidak Bisa Melakukan SPP TU karena Ada SP2D Yang Melebihi Batas Satu Bulan' nm_sub_kegiatan, '' kd_program, '' nm_program");
            }
        } else {
            if ($kendali_tu->status == 1) {
                $data = DB::select("SELECT kd_sub_kegiatan, nm_sub_kegiatan, kd_program, nm_program FROM (SELECT DISTINCT a.kd_sub_kegiatan,b.nm_sub_kegiatan,a.kd_program,b.nm_program FROM trdspd a INNER JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan where b.kd_skpd = ? AND a.no_spd=? AND b.status_sub_kegiatan='1') h WHERE h.kd_sub_kegiatan NOT IN (

                        SELECT b.kd_sub_kegiatan FROM trhspp a INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd WHERE a.jns_spp='3' AND a.kd_skpd = ? and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1') AND b.no_spp NOT IN (
                        select no_spp from trhsp2d where kd_skpd =? and jns_spp ='3'
                        ) GROUP BY b.kd_sub_kegiatan

                        UNION ALL

                        SELECT b.kd_sub_kegiatan FROM trhspp a
                        INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
                        INNER JOIN trhsp2d c on a.no_spp=b.no_spp and a.kd_skpd=c.kd_skpd
                        WHERE a.jns_spp='3' AND a.kd_skpd = ?
                        and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1')
                        AND b.kd_sub_kegiatan IN (
                        select e.kd_sub_kegiatan from trhtransout_cmsbank f INNER JOIN trdtransout_cmsbank e on f.no_voucher=e.no_voucher
                        AND f.kd_skpd=e.kd_skpd and jns_spp='3'
                        where f.kd_skpd =? and f.jns_spp ='3' and f.status_validasi<>'1'
                        )

                    UNION ALL

                    SELECT b.kd_sub_kegiatan FROM trhspp a
                    INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
                    INNER JOIN trhsp2d c on a.no_spp=b.no_spp and a.kd_skpd=c.kd_skpd
                    WHERE a.jns_spp='3' AND a.kd_skpd = ?
                    and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1')
                    AND b.kd_sub_kegiatan IN (

                    select e.kd_sub_kegiatan from trhtransout f INNER JOIN trdtransout e on f.no_bukti=e.no_bukti
                    AND f.kd_skpd=e.kd_skpd
                    where f.kd_skpd =? and f.jns_spp ='3'
                    and f.no_bukti not in (select no_bukti from trlpj g inner join trhlpj_tu h on g.no_lpj=h.no_lpj where h.kd_skpd=? and jenis='3')

                    ) GROUP BY b.kd_sub_kegiatan

                    UNION ALL
                    select kd_sub_kegiatan FROM trhlpj_tu a inner join trlpj b on a.no_lpj=b.no_lpj and a.kd_skpd=b.kd_skpd WHERE status<>'1' AND jenis = '3' AND a.kd_skpd=?

                     )

                    union all
                    SELECT kd_sub_kegiatan, nm_sub_kegiatan, kd_program, nm_program FROM (SELECT DISTINCT a.kd_sub_kegiatan,b.nm_sub_kegiatan,a.kd_program,b.nm_program FROM trdspd a INNER JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan where b.kd_skpd = ? AND a.no_spd=? AND b.status_sub_kegiatan='1') h WHERE h.kd_sub_kegiatan in (?)", [$kd_skpd, $no_spd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $no_spd, $kendali_tu->kd_sub_kegiatan]);
            } else {
                $data = DB::select("SELECT kd_sub_kegiatan, nm_sub_kegiatan, kd_program, nm_program FROM (SELECT DISTINCT a.kd_sub_kegiatan,b.nm_sub_kegiatan,a.kd_program,b.nm_program FROM trdspd a INNER JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan where b.kd_skpd = ? AND a.no_spd=? AND b.status_sub_kegiatan='1') h WHERE h.kd_sub_kegiatan NOT IN (

                        SELECT b.kd_sub_kegiatan FROM trhspp a INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd WHERE a.jns_spp='3' AND a.kd_skpd = ? and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1') AND b.no_spp NOT IN (
                        select no_spp from trhsp2d where kd_skpd =? and jns_spp ='3'
                        ) GROUP BY b.kd_sub_kegiatan

                        UNION ALL

                        SELECT b.kd_sub_kegiatan FROM trhspp a
                        INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
                        INNER JOIN trhsp2d c on a.no_spp=b.no_spp and a.kd_skpd=c.kd_skpd
                        WHERE a.jns_spp='3' AND a.kd_skpd = ?
                        and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1')
                        AND b.kd_sub_kegiatan IN (
                        select e.kd_sub_kegiatan from trhtransout_cmsbank f INNER JOIN trdtransout_cmsbank e on f.no_voucher=e.no_voucher
                        AND f.kd_skpd=e.kd_skpd and jns_spp='3'
                        where f.kd_skpd =? and f.jns_spp ='3' and f.status_validasi<>'1'
                        )

                    UNION ALL

                    SELECT b.kd_sub_kegiatan FROM trhspp a
                    INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
                    INNER JOIN trhsp2d c on a.no_spp=b.no_spp and a.kd_skpd=c.kd_skpd
                    WHERE a.jns_spp='3' AND a.kd_skpd = ?
                    and b.kd_sub_kegiatan not in ('') and (a.sp2d_batal is null or a.sp2d_batal<>'1')
                    AND b.kd_sub_kegiatan IN (

                    select e.kd_sub_kegiatan from trhtransout f INNER JOIN trdtransout e on f.no_bukti=e.no_bukti
                    AND f.kd_skpd=e.kd_skpd
                    where f.kd_skpd =? and f.jns_spp ='3'
                    and f.no_bukti not in (select no_bukti from trlpj_tu g inner join trhlpj_tu h on g.no_lpj=h.no_lpj where h.kd_skpd=? and jenis='3')

                    ) GROUP BY b.kd_sub_kegiatan

                    UNION ALL
                    select kd_sub_kegiatan FROM trhlpj_tu a inner join trlpj_tu b on a.no_lpj=b.no_lpj and a.kd_skpd=b.kd_skpd WHERE status<>'1' AND jenis = '3' AND a.kd_skpd=?

                     )", [$kd_skpd, $no_spd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]);
            }
        }

        return response()->json($data);
    }

    public function rekening(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT distinct kd_rek6, nm_rek6 FROM trdrka where kd_sub_kegiatan = ? and kd_skpd=? and status_aktif=? order by kd_rek6", [$kd_sub_kegiatan, $kd_skpd, '1']);

        return response()->json($data);
    }

    // CARI NILAI ANGGARAN, SPD, ANGKAS
    public function angSpdAngkas(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_rek6 = $request->kd_rek6;
        $no_spp = $request->no_spp;
        $tgl_spp = $request->tgl_spp;
        $beban = $request->beban;
        $kd_skpd = Auth::user()->kd_skpd;

        $bulan = date('m', strtotime($tgl_spp));
        $sts_angkas = $request->status_angkas;

        $status_anggaran = status_anggaran();

        // ANGGARAN
        $anggaran = collect(DB::select("SELECT SUM(nilai) as nilai,
        ( SELECT SUM(nilai) FROM
                    (select sum(a.nilai) nilai
                    from trdspp a
                    inner join trhspp b on a.no_spp=b.no_spp AND a.kd_skpd=b.kd_skpd
                    where a.kd_sub_kegiatan=? and a.kd_rek6=? and a.kd_skpd=? and a.no_spp <> ?
                    AND b.jns_spp IN ('3','4','5','6')
                    and (b.sp2d_batal !='1' or b.sp2d_batal IS NULL)
                    UNION ALL
                    SELECT SUM(nilai) as nilai FROM trdtagih t
                    INNER JOIN trhtagih u
                    ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                    WHERE
                    t.kd_sub_kegiatan = ?
                    AND u.kd_skpd=?
                    AND t.kd_rek = ?
                    AND u.no_bukti
                    NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=?)
                    UNION ALL
                    SELECT SUM(a.nilai) nilai FROM trdtransout a INNER JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                    WHERE a.kd_sub_kegiatan=? and a.kd_rek6=? and a.kd_skpd=? AND b.jns_spp IN ('1','2')

                    UNION ALL
                    SELECT SUM(a.nilai) nilai FROM trdtransout a INNER JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
                    WHERE a.kd_sub_kegiatan=? and a.kd_rek6=? and a.kd_skpd=? AND b.jns_spp IN ('4','6') and panjar in ('3')

                    UNION ALL
                    SELECT SUM(a.nilai) nilai FROM trdtransout_cmsbank a INNER JOIN trhtransout_cmsbank b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_skpd
                    WHERE a.kd_sub_kegiatan=? and a.kd_rek6=? and a.kd_skpd=? AND b.status_validasi = '0'

                    )b)
          as rektotal_spp_lalu
          FROM trdrka WHERE kd_rek6=? and kd_sub_kegiatan=? and jns_ang=? and kd_skpd=?", [$kd_sub_kegiatan, $kd_rek6, $kd_skpd, $no_spp, $kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $status_anggaran, $kd_skpd]))->first();

        // SPD
        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where left(kd_skpd,17)=left(?,17) and bulan_akhir='3' and tgl_spd<=?", [$kd_skpd, $tgl_spp]))->first();

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where left(kd_skpd,17)=left(?,17) and bulan_akhir='6' and tgl_spd<=?", [$kd_skpd, $tgl_spp]))->first();

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where left(kd_skpd,17)=left(?,17) and bulan_akhir='9' and tgl_spd<=?", [$kd_skpd, $tgl_spp]))->first();

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where left(kd_skpd,17)=left(?,17) and bulan_akhir='12' and tgl_spd<=?", [$kd_skpd, $tgl_spp]))->first();

        $total_spd = collect(DB::select("SELECT sum(nilai)as total_spd from (
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
                    and tgl_spd<=?
                    and bulan_awal <= month(?)
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
                    and tgl_spd<=?
                    and bulan_awal <= month(?)
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
                    and tgl_spd<=?
                    and bulan_awal <= month(?)
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
                    and revisi_ke=?
                    and tgl_spd<=?
                    and bulan_awal <= month(?)
                    )spd", [$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $revisi1->revisi, $tgl_spp, $tgl_spp, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $revisi2->revisi, $tgl_spp, $tgl_spp, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $revisi3->revisi, $tgl_spp, $tgl_spp, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $revisi4->revisi, $tgl_spp, $tgl_spp]))->first();

        // ANGKAS
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

        if ($beban == '4' || substr($kd_sub_kegiatan, 5, 10) == '01.1.02.01') {
            $bulan1 = $bulan  + 1;
            $nilai_angkas = collect(DB::select("SELECT  a.kd_sub_kegiatan, SUM(a.$field_angkas) as nilai FROM trdskpd_ro a INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd and a.kd_sub_kegiatan=b.kd_sub_kegiatan
            where a.kd_skpd = ? and  a.kd_sub_kegiatan = ? and a.kd_rek6=? and (bulan <=?) and jns_ang=? GROUP BY a.kd_sub_kegiatan,a.kd_rek6", [$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $bulan1, $status_anggaran]))->first();
        } else {
            $nilai_angkas = collect(DB::select("SELECT  a.kd_sub_kegiatan, SUM(a.$field_angkas) as nilai FROM trdskpd_ro a INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd and a.kd_sub_kegiatan=b.kd_sub_kegiatan
            where a.kd_skpd = ? and  a.kd_sub_kegiatan = ? and a.kd_rek6=? and (bulan <=?) and jns_ang=? GROUP BY a.kd_sub_kegiatan,a.kd_rek6", [$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $bulan, $status_anggaran]))->first();
        }

        // TOTAL TRANSAKSI SPD LALU
        $total_transaksi = collect(DB::select("SELECT SUM(nilai) total FROM
                                    (

                                    --Table tampungan // tambahan tampungan
                                    SELECT SUM (isnull(nilai,0)) as nilai
                                    FROM tb_transaksi
                                    WHERE kd_sub_kegiatan = ?
                                    AND kd_skpd = ?
                                    AND kd_rek6 = ?
                                    UNION ALL
                                    -- transaksi UP/GU
                                    SELECT SUM (isnull(c.nilai,0)) as nilai
                                    FROM trdtransout c
                                    LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
                                    AND c.kd_skpd = d.kd_skpd
                                    WHERE c.kd_sub_kegiatan = ?
                                    AND d.kd_skpd = ?
                                    AND c.kd_rek6 = ?
                                    AND d.jns_spp in ('1')

                                    UNION ALL
                                    -- transaksi UP/GU CMS BANK Belum Validasi
                                    SELECT SUM (isnull(c.nilai,0)) as nilai
                                    FROM trdtransout_cmsbank c
                                    LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher
                                    AND c.kd_skpd = d.kd_skpd
                                    WHERE c.kd_sub_kegiatan =?
                                    AND d.kd_skpd = ?
                                    AND c.kd_rek6=?
                                    AND d.jns_spp in ('1')
                                    AND (d.status_validasi='0' OR d.status_validasi is null)

                                    UNION ALL
                                    -- transaksi SPP SELAIN UP/GU
                                    SELECT SUM(isnull(x.nilai,0)) as nilai FROM trdspp x
                                    INNER JOIN trhspp y
                                    ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                                    WHERE x.kd_sub_kegiatan = ?
                                    AND x.kd_skpd = ?
                                    AND x.kd_rek6 = ?
                                    AND y.jns_spp IN ('3','4','5','6')
                                    AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')

                                    UNION ALL
                                    -- Penagihan yang belum jadi SPP
                                    SELECT SUM(isnull(nilai,0)) as nilai FROM trdtagih t
                                    INNER JOIN trhtagih u
                                    ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                                    WHERE t.kd_sub_kegiatan =?
                                    AND u.kd_skpd = ?
                                    AND t.kd_rek =?
                                    AND u.no_bukti
                                    NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=?)
                                    )r", [$kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_skpd]))->first();

        return response()->json([
            'angkas' => $nilai_angkas->nilai,
            'spd' => $total_spd->total_spd,
            'transaksi' => $total_transaksi->total,
            'anggaran' => $anggaran,
        ]);
    }

    public function detail(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $no_spp = $request->no_spp;
        $tipe = $request->tipe;
        $kd_skpd = Auth::user()->kd_skpd;

        if ($tipe == 'create') {
            $data = DB::table('trlpj as a')
                ->join('trhlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                })
                ->selectRaw("a.kd_sub_kegiatan,a.kd_rek6, a.nm_rek6, a.nilai ,a.no_bukti,a.no_lpj, a.kd_skpd as kd_unit,a.kd_bp_skpd as kd_skpd,
        (select DISTINCT sumber from trdtransout where trdtransout.no_bukti=a.no_bukti and trdtransout.kd_skpd=a.kd_skpd and trdtransout.kd_sub_kegiatan=a.kd_sub_kegiatan and trdtransout.kd_rek6=a.kd_rek6)as sumber")
                ->where(['a.kd_bp_skpd' => $kd_skpd, 'a.no_lpj' => $no_lpj])
                ->orderBy('a.no_bukti')
                ->orderBy('a.kd_sub_kegiatan')
                ->orderBy('a.kd_rek6')
                ->get();
        } else if ($tipe == 'edit') {
            $data = DB::table('trhspp as a')
                ->join('trdspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.kd_sub_kegiatan,b.kd_rek6, b.nm_rek6, b.nilai ,b.no_bukti, b.kd_skpd as kd_unit,b.sumber")
                ->where(['a.kd_skpd' => $kd_skpd, 'a.no_spp' => $no_spp])
                ->orderBy('b.no_bukti')
                ->orderBy('b.kd_sub_kegiatan')
                ->orderBy('b.kd_rek6')
                ->get();
        }

        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function nomor(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('trhspp')
            ->selectRaw("isnull(max(urut),0)+1 as nilai")
            ->where(['kd_skpd' => $kd_skpd])
            ->first();

        return response()->json($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhspp')
                ->where(['no_spp' => $data['no_spp']])
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhspp')
                ->insert([
                    'no_spp' => $data['no_spp'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keperluan' => $data['keterangan'],
                    'bulan' => $data['bulan'],
                    'no_spd' => $data['no_spd'],
                    'jns_spp' => $data['beban'],
                    'jns_beban' => '1',
                    'bank' => $data['bank'],
                    'nmrekan' => $data['nm_rekening'],
                    'no_rek' => $data['rekening'],
                    'npwp' => $data['npwp'],
                    'nm_skpd' => $data['nm_skpd'],
                    'tgl_spp' => $data['tgl_spp'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                    'kd_program' => $data['kd_program'],
                    'nm_program' => $data['nm_program'],
                    'status' => '0',
                    'username' => Auth::user()->nama,
                    'last_update' => date('Y-m-d H:i:s'),
                    'nilai' => $data['total'],
                    'urut' => $data['no_urut'],
                ]);

            DB::table('trdspp')
                ->where(['no_spp' => $data['no_spp'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            if (isset($data['detail_spp'])) {
                DB::table('trdspp')->insert(array_map(function ($value) use ($data) {
                    return [
                        'no_spp' => $data['no_spp'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'nm_skpd' => $data['nm_skpd'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'no_spd' => $data['no_spd'],
                        'sumber' => $value['sumber'],
                        'kd_bidang' => $data['kd_skpd'],
                    ];
                }, $data['detail_spp']));
            }

            DB::update("UPDATE a
                                SET a.nm_sub_kegiatan=b.nm_sub_kegiatan
                                FROM trdspp  a
                                INNER JOIN trskpd b
                                ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                                WHERE no_spp=?", [$data['no_spp']]);

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

    public function edit($no_spp, $kd_skpd)
    {
        $no_spp = Crypt::decrypt($no_spp);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $data = [
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'daftar_bank' => DB::table('ms_skpd')
                ->selectRaw("bank,replace(replace(npwp,'.',''),'-','')as npwp ,(select nama from ms_bank where kode = bank)as nama_bank,rekening")
                ->where(['kd_skpd' => $kd_skpd])
                ->get(),
            'daftar_rekening' => DB::table('ms_rekening_bank_online')
                ->selectRaw("rekening, nm_rekening,npwp")
                ->where(['kd_skpd' => $kd_skpd])
                ->orderBy('rekening')
                ->get(),
            'tanggal_lalu' => DB::table('trhspp')
                ->selectRaw("max(tgl_spp) as tgl_spp")
                ->where(['kd_skpd' => $kd_skpd])
                ->whereRaw("(sp2d_batal is null or sp2d_batal= '0')")
                ->first(),
            'spp' => DB::table('trhspp as a')
                ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
                ->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd, 'a.jns_spp' => '2'])
                ->first()
        ];

        return view('skpd.spp_gu.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhspp')
                ->where(['no_spp' => $data['no_spp'], 'kd_skpd' => $data['kd_skpd']])
                ->update([
                    'tgl_spp' => $data['tgl_spp'],
                    'keperluan' => $data['keterangan'],
                    'username' => Auth::user()->nama,
                    'last_update' => date('Y-m-d H:i:s'),
                    'bank' => $data['bank'],
                    'nmrekan' => $data['nm_rekening'],
                    'no_rek' => $data['rekening'],
                    'npwp' => $data['npwp'],
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
        $no_spp = $request->no_spp;
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trdspp')
                ->where([
                    'no_spp' => $no_spp,
                    'kd_skpd' => $kd_skpd
                ])
                ->delete();

            DB::table('trhspp')
                ->where([
                    'no_spp' => $no_spp,
                    'kd_skpd' => $kd_skpd,
                ])
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

    public function pengantar(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        if (substr($kd_skpd, 18, 4) == '0000') {
            $skpdspd = "and left(a.kd_unit,17)=left(?,17)";
        } else {
            $skpdspd = "and a.kd_unit=?";
        }

        $spp = DB::table('trhspp')
            ->select('no_spd', 'tgl_spp')
            ->where(['no_spp' => $no_spp])
            ->first();

        $tgl_spd = DB::table('trhspd')
            ->select('tgl_spd')
            ->where(['no_spd' => $spp->no_spd])
            ->first();

        $tgl_spd = $tgl_spd->tgl_spd;
        $tgl_spp = $spp->tgl_spp;

        $kegiatan = DB::table('trdspp')
            ->select('kd_sub_kegiatan')
            ->where(['no_spp' => $no_spp])
            ->groupBy('kd_sub_kegiatan')
            ->first();

        $kd_sub_kegiatan = $kegiatan->kd_sub_kegiatan;

        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='3' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='6' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='9' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='12' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();


        $spp = collect(DB::select("SELECT a.no_spp,a.tgl_spp,a.kd_skpd,a.nm_skpd,a.bulan,a.nmrekan,
        b.urusan1 as kd_bidang_urusan, (select nm_bidang_urusan from ms_bidang_urusan where kd_bidang_urusan=b.urusan1)as nm_bidang_urusan,
         a.bank,
                (SELECT rekening FROM ms_skpd WHERE kd_skpd=a.kd_skpd) AS no_rek,
                (SELECT npwp FROM ms_skpd WHERE kd_skpd=a.kd_skpd) AS npwp,
                a.no_spd,a.nilai,
                (
                    (SELECT isnull(SUM(a.nilai),0) FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' $skpdspd
                    AND a.kd_sub_kegiatan=? and b.tgl_spd <=? and b.bulan_akhir='3' and revisi_ke=?)+
                    (SELECT isnull(SUM(a.nilai),0) FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' $skpdspd
                    AND a.kd_sub_kegiatan=? and b.tgl_spd <=? and b.bulan_akhir='6' and revisi_ke=?)+
                    (SELECT isnull(SUM(a.nilai),0) FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' $skpdspd
                    AND a.kd_sub_kegiatan=? and b.tgl_spd <=? and b.bulan_akhir='9' and revisi_ke=?)+
                    (SELECT isnull(SUM(a.nilai),0) FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' $skpdspd
                    AND a.kd_sub_kegiatan=? and b.tgl_spd <=? and b.bulan_akhir='12' and revisi_ke=?)
                    ) AS spd,
                    (SELECT SUM(b.nilai) FROM trdspp b INNER JOIN trhspp a ON b.no_spp=a.no_spp and b.kd_skpd = a.kd_skpd
                    INNER JOIN trhsp2d c ON a.no_spp = c.no_spp WHERE a.kd_skpd=?
                    AND b.kd_sub_kegiatan=?
                    AND a.jns_spp IN ('1','2','3','6') AND a.no_spp != ? AND c.tgl_sp2d <=?) AS spp
                FROM trhspp a INNER JOIN ms_skpd b ON a.kd_skpd=b.kd_skpd  where a.no_spp=? AND a.kd_skpd=?", [$kd_skpd, $kd_sub_kegiatan, $tgl_spd, $revisi1->revisi, $kd_skpd, $kd_sub_kegiatan, $tgl_spd, $revisi2->revisi, $kd_skpd, $kd_sub_kegiatan, $tgl_spd, $revisi3->revisi, $kd_skpd, $kd_sub_kegiatan, $tgl_spd, $revisi4->revisi, $kd_skpd, $kd_sub_kegiatan, $no_spp, $tgl_spp, $no_spp, $kd_skpd]))->first();

        $bank = DB::table('ms_skpd')
            ->select('bank')
            ->where(['kd_skpd' => $kd_skpd])
            ->first();

        $nama_bank = empty($bank->bank) || $bank->bank == '' ? '-' : DB::table('ms_bank')->select('nama')->where(['kode' => $bank->bank])->first()->nama;

        $unit = substr($kd_skpd, -2);
        if ($unit == '01' || $kd_skpd == '1.20.03.00') {
            $peng = "Pengguna Anggaran";
        } else {
            $peng = "Kuasa Pengguna Anggaran";
        }

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'beban' => $beban,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'spp' => $spp,
            'bendahara' => DB::table('ms_ttd')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first(),
            'nama_bank' => $nama_bank,
            'peng' => $peng,
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
        ];
        $view = view('skpd.spp_tu.cetak.pengantar')->with($data);
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

    public function rincian(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $spp = DB::select("SELECT 1 urut, LEFT(c.kd_sub_kegiatan,7) as kode, d.nm_program as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trskpd d ON c.kd_sub_kegiatan=d.kd_sub_kegiatan  AND c.kd_skpd=d.kd_skpd
                    WHERE b.no_spp=? AND b.kd_skpd=? and d.jns_ang=?
                    GROUP BY LEFT(c.kd_sub_kegiatan,7), d.nm_program
                    UNION ALL
                    SELECT 2 urut, LEFT(c.kd_sub_kegiatan,12) as kode, d.nm_kegiatan as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trskpd d ON c.kd_sub_kegiatan=d.kd_sub_kegiatan  AND c.kd_skpd=d.kd_skpd
                    WHERE b.no_spp=? AND b.kd_skpd=? and d.jns_ang=?
                    GROUP BY LEFT(c.kd_sub_kegiatan,12), d.nm_kegiatan
                    UNION ALL
                    SELECT 3 urut, c.kd_sub_kegiatan as kode, c.nm_sub_kegiatan as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trskpd d ON c.kd_sub_kegiatan=d.kd_sub_kegiatan  AND c.kd_skpd=d.kd_skpd
                    WHERE b.no_spp=? AND b.kd_skpd=? and d.jns_ang=?
                    GROUP BY c.kd_sub_kegiatan,c.nm_sub_kegiatan
                    UNION ALL
                    SELECT 4 urut, c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,4) as kode, d.nm_rek3 as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    LEFT JOIN ms_rek3 d ON LEFT(c.kd_rek6,4)=d.kd_rek3
                    WHERE b.no_spp=? AND b.kd_skpd=?
                    GROUP BY c.kd_sub_kegiatan,LEFT(c.kd_rek6,4),d.nm_rek3
                    UNION ALL
                    SELECT 5 urut, c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,6) as kode, d.nm_rek4 as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    LEFT JOIN ms_rek4 d ON LEFT(c.kd_rek6,6)=d.kd_rek4
                    WHERE b.no_spp=? AND b.kd_skpd=?
                    GROUP BY c.kd_sub_kegiatan,LEFT(c.kd_rek6,6),d.nm_rek4
                    UNION ALL
                    SELECT 6 urut, c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,8) as kode, d.nm_rek5 as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    LEFT JOIN ms_rek5 d ON LEFT(c.kd_rek6,8)=d.kd_rek5
                    WHERE b.no_spp=? AND b.kd_skpd=?
                    GROUP BY c.kd_sub_kegiatan,LEFT(c.kd_rek6,8),d.nm_rek5
                    UNION ALL
                    SELECT 7 urut, c.kd_sub_kegiatan+'.'+c.kd_rek6 as kode, c.nm_rek6 as nama, c.nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    WHERE b.no_spp=? AND b.kd_skpd=?
                    order by kode", [$no_spp, $kd_skpd, $status_anggaran, $no_spp, $kd_skpd, $status_anggaran, $no_spp, $kd_skpd, $status_anggaran, $no_spp, $kd_skpd, $no_spp, $kd_skpd, $no_spp, $kd_skpd, $no_spp, $kd_skpd]);

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'data_spp' => $spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
        ];

        $view = view('skpd.spp_tu.cetak.rincian')->with($data);
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

    public function ringkasan(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $kegiatan = DB::table('trdspp')
            ->select('kd_sub_kegiatan')
            ->where(['no_spp' => $no_spp])
            ->groupBy('kd_sub_kegiatan')
            ->first();

        $kd_sub_kegiatan = $kegiatan->kd_sub_kegiatan;

        $spp = DB::table('trhspp')
            ->select('no_spd', 'tgl_spp')
            ->where(['no_spp' => $no_spp])
            ->first();

        $tgl_spd = DB::table('trhspd')
            ->select('tgl_spd')
            ->where(['no_spd' => $spp->no_spd])
            ->first();

        $tgl_spd = $tgl_spd->tgl_spd;
        $tgl_spp = $spp->tgl_spp;

        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='3' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='6' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='9' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,17)=left(?,17)
                                and bulan_akhir='12' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        if (substr($kd_skpd, 18, 4) == '0000') {
            $data_beban = DB::select("SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd
                     WHERE
                    left(a.kd_unit,17)=left(?,17) and b.tgl_spd <=? and a.kd_sub_kegiatan=? and bulan_akhir='3' and revisi_ke=? GROUP BY a.no_spd, b.tgl_spd

                    UNION ALL
                    SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd
                     WHERE
                    left(a.kd_unit,17)=left(?,17) and b.tgl_spd <=? and a.kd_sub_kegiatan=? and bulan_akhir='6' and revisi_ke=? GROUP BY a.no_spd, b.tgl_spd

                    UNION ALL
                    SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd
                     WHERE
                    left(a.kd_unit,17)=left(?,17) and b.tgl_spd <=? and a.kd_sub_kegiatan=? and bulan_akhir='9' and revisi_ke=? GROUP BY a.no_spd, b.tgl_spd

                    UNION ALL
                    SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd
                     WHERE
                    left(a.kd_unit,17)=left(?,17) and b.tgl_spd <=? and a.kd_sub_kegiatan=? and bulan_akhir='12' and revisi_ke=? GROUP BY a.no_spd, b.tgl_spd", [$kd_skpd, $tgl_spd, $kd_sub_kegiatan, $revisi1->revisi, $kd_skpd, $tgl_spd, $kd_sub_kegiatan, $revisi2->revisi, $kd_skpd, $tgl_spd, $kd_sub_kegiatan, $revisi3->revisi, $kd_skpd, $tgl_spd, $kd_sub_kegiatan, $revisi4->revisi]);
        } else {
            $data_beban = DB::select("SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd
                     WHERE
                    left(a.kd_unit,22)=left(?,22) and b.tgl_spd <=? and a.kd_sub_kegiatan=? and bulan_akhir='3' and revisi_ke=? GROUP BY a.no_spd, b.tgl_spd

                    UNION ALL
                    SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd
                     WHERE
                    left(a.kd_unit,22)=left(?,22) and b.tgl_spd <=? and a.kd_sub_kegiatan=? and bulan_akhir='6' and revisi_ke=? GROUP BY a.no_spd, b.tgl_spd

                    UNION ALL
                    SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd
                     WHERE
                    left(a.kd_unit,22)=left(?,22) and b.tgl_spd <=? and a.kd_sub_kegiatan=? and bulan_akhir='9' and revisi_ke=? GROUP BY a.no_spd, b.tgl_spd

                    UNION ALL
                    SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd
                     WHERE
                    left(a.kd_unit,22)=left(?,22) and b.tgl_spd <=? and a.kd_sub_kegiatan=? and bulan_akhir='12' and revisi_ke=? GROUP BY a.no_spd, b.tgl_spd", [$kd_skpd, $tgl_spd, $kd_sub_kegiatan, $revisi1->revisi, $kd_skpd, $tgl_spd, $kd_sub_kegiatan, $revisi2->revisi, $kd_skpd, $tgl_spd, $kd_sub_kegiatan, $revisi3->revisi, $kd_skpd, $tgl_spd, $kd_sub_kegiatan, $revisi4->revisi]);
        }

        if (substr($kd_skpd, 18, 4) == '0000') {
            $nilai_ang = collect(DB::select("SELECT sum(nilai) as nilai FROM trdrka where kd_sub_kegiatan=? and left(kd_skpd,17)=left(?,17) and jns_ang=?", [$kd_sub_kegiatan, $kd_skpd, $status_anggaran]))->first();
        } else {
            $nilai_ang = collect(DB::select("SELECT sum(nilai) as nilai FROM trdrka where kd_sub_kegiatan=? and left(kd_skpd,22)=left(?,22) and jns_ang=?", [$kd_sub_kegiatan, $kd_skpd, $status_anggaran]))->first();
        }

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'nilai_ang' => $nilai_ang,
            'data_beban' => $data_beban,
            'kd_skpd' => $kd_skpd,
            'beban' => $beban,
            'tgl_spp' => $tgl_spp,
            'kd_sub_kegiatan' => $kd_sub_kegiatan
        ];

        $view = view('skpd.spp_tu.cetak.ringkasan')->with($data);
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

    public function pernyataan(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $data = collect(DB::select("SELECT a.no_spp,a.tgl_spp,a.kd_skpd,a.nm_skpd,a.bulan,a.nmrekan, a.no_rek,a.npwp,b.kd_bidang_urusan, b.nm_bidang_urusan, a.bank
                , ( SELECT
                            nama
                        FROM
                            ms_bank
                        WHERE
                            kode=a.bank
                ) AS nama_bank,
                a.no_spd,a.nilai
                FROM trhspp a INNER JOIN ms_bidang_urusan b
                ON SUBSTRING(a.kd_skpd,1,4)=b.kd_bidang_urusan  where a.no_spp=? AND a.kd_skpd=?", [$no_spp, $kd_skpd]))->first();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'data' => $data
        ];

        $view = view('skpd.spp_tu.cetak.pernyataan')->with($data);
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

    public function permintaan(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $data_spp = collect(DB::select("SELECT a.no_spp,a.tgl_spp,a.kd_skpd,a.nm_skpd,a.kd_program,a.nm_program,a.nm_sub_kegiatan,a.kd_sub_kegiatan,a.bulan,a.nmrekan,
                a.no_rek as no_rek_rek, a.npwp as npwp_rek,b.kd_bidang_urusan, b.nm_bidang_urusan, a.bank, lanjut, kontrak, keperluan,pimpinan, alamat,
                ( SELECT nama FROM ms_bank WHERE  kode=a.bank ) AS nama_bank_rek,
                ( SELECT rekening FROM ms_skpd WHERE  kd_skpd=a.kd_skpd ) AS no_rek,
                ( SELECT npwp FROM ms_skpd WHERE  kd_skpd=a.kd_skpd ) AS npwp,
                a.no_spd,a.nilai
                FROM trhspp a INNER JOIN ms_bidang_urusan b
                ON SUBSTRING(a.kd_skpd,1,4)=b.kd_bidang_urusan  where a.no_spp=? AND a.kd_skpd=?", [$no_spp, $kd_skpd]))->first();

        $program1 = substr($data_spp->kd_sub_kegiatan, 0, 7);
        $program2 = substr($data_spp->kd_sub_kegiatan, 0, 12);

        if (substr($data_spp->kd_sub_kegiatan, 0, 12) == 0 || substr($data_spp->kd_sub_kegiatan, 0, 12) == '') {
            $nama_program = '';
            $nama_kegiatan = '';
        } else {
            $program = DB::table('ms_program')->select('nm_program')->where('kd_program', $program1)->first();
            $nama_program = $program->nm_program;
            $kegiatan = DB::table('ms_kegiatan')->select('nm_kegiatan')->where('kd_kegiatan', $program2)->first();
            $nama_kegiatan = $kegiatan->nm_kegiatan;
        }

        $daerah1 = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $bank = DB::table('ms_skpd')
            ->select('bank')
            ->where(['kd_skpd' => $kd_skpd])
            ->first();

        $nama_bank = empty($bank->bank) || $bank->bank == '' ? '-' : DB::table('ms_bank')->select('nama')->where(['kode' => $bank->bank])->first()->nama;

        if ($status_anggaran == 'M') {
            $nogub = $daerah1->nogub_susun;
        } else if ($status_anggaran == 'P1') {
            $nogub = $daerah1->nogub_p1;
        } else if ($status_anggaran == 'P2') {
            $nogub = $daerah1->nogub_p2;
        } else if ($status_anggaran == 'P3') {
            $nogub = $daerah1->nogub_p3;
        } else if ($status_anggaran == 'P4') {
            $nogub = $daerah1->nogub_p4;
        } else if ($status_anggaran == 'P5') {
            $nogub = $daerah1->nogub_p5;
        } else if ($status_anggaran == 'U1') {
            $nogub = $daerah1->nogub_perubahan;
        } else if ($status_anggaran == 'U2') {
            $nogub = $daerah1->nogub_perubahan2;
        } else if ($status_anggaran == 'U3') {
            $nogub = $daerah1->nogub_perubahan3;
        } else if ($status_anggaran == 'U4') {
            $nogub = $daerah1->nogub_perubahan4;
        } else {
            $nogub = $daerah1->nogub_perubahan5;
        }

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'dpa' => DB::table('trhrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first(),
            'data_spp' => $data_spp,
            'nama_program' => $nama_program,
            'nama_kegiatan' => $nama_kegiatan,
            'nogub' => $nogub,
            'nama_bank' => $nama_bank
        ];

        $view = view('skpd.spp_tu.cetak.permintaan')->with($data);
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

    public function sptb(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $spp = collect(DB::select("SELECT a.no_spp,a.tgl_spp,a.kd_skpd,a.nm_skpd,a.bulan,a.nmrekan, a.no_rek,a.npwp,b.kd_bidang_urusan, b.nm_bidang_urusan, a.bank
                , ( SELECT
                            nama
                        FROM
                            ms_bank
                        WHERE
                            kode=a.bank
                ) AS nama_bank, a.nilai
                FROM trhspp a INNER JOIN ms_bidang_urusan b
                ON SUBSTRING(a.kd_skpd,1,4)=b.kd_bidang_urusan  where a.no_spp=? AND a.kd_skpd=?", [$no_spp, $kd_skpd]))->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat', 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'data_dpa' => DB::table('trhrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first(),
            'data' => $spp
        ];

        $view = view('skpd.spp_tu.cetak.sptb')->with($data);
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

    public function spp(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $spp = collect(DB::select("SELECT a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,a.bank,no_rek,keperluan,a.no_spd,b.kd_sub_kegiatan,b.nm_sub_kegiatan,sum(b.nilai)as nilaispp FROM trhspp a inner join trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd where a.no_spp=? AND b.kd_skpd=? GROUP BY a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,a.bank,no_rek,keperluan,a.no_spd,b.kd_sub_kegiatan,b.nm_sub_kegiatan", [$no_spp, $kd_skpd]))->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat', 'kd_skpd', 'npwp')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'data_dpa' => DB::table('trhrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first(),
            'data' => $spp,
            'bank' => DB::table('ms_bank')->select('nama')->where(['kode' => $spp->bank])->first(),
            'nilai_spp' => DB::table('trhspp')->select('nilai')->where(['no_spp' => $no_spp])->first(),
            'spd' => DB::table('trhspd')->select('tgl_spd')->where(['no_spd' => $spp->no_spd])->first(),
            'dataspd' => DB::select("SELECT no_spd,tgl_spd,total from trhspd where left(kd_skpd,17)=left(?,17)", [$kd_skpd]),
            'datasp2d' => DB::select("SELECT no_sp2d,tgl_sp2d,nilai as total from trhsp2d where kd_skpd=? and jns_spp='6'", [$kd_skpd]),
            'sub_kegiatan' => collect(DB::select("SELECT kd_sub_kegiatan FROM trdspp WHERE no_spp=? GROUP BY kd_sub_kegiatan", [$no_spp]))->first()->kd_sub_kegiatan
        ];

        $view = view('skpd.spp_tu.cetak.spp77')->with($data);
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

    public function rincian77(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $kegiatan = collect(DB::select("SELECT a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan,sum(b.nilai)as nilaisub FROM trhspp a inner join trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd where a.no_spp=? AND b.kd_skpd=? GROUP BY a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan", [$no_spp, $kd_skpd]))->first();

        $sub_kegiatan = substr($kegiatan->kd_sub_kegiatan, 0, 12);

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat', 'kd_skpd', 'npwp')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'data_dpa' => DB::table('trhrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first(),
            'nilai_spp' => DB::table('trhspp')->select('nilai')->where(['no_spp' => $no_spp])->first(),
            'dataspd' => DB::select("SELECT no_spd,tgl_spd,total from trhspd where left(kd_skpd,17)=left(?,17)", [$kd_skpd]),
            'datasp2d' => DB::select("SELECT no_sp2d,tgl_sp2d,nilai as total from trhsp2d where kd_skpd=? and jns_spp='6'", [$kd_skpd]),
            'sub_kegiatan' => collect(DB::select("SELECT kd_sub_kegiatan FROM trdspp WHERE no_spp=? GROUP BY kd_sub_kegiatan", [$no_spp]))->first()->kd_sub_kegiatan,
            'nama_kegiatan' => DB::table('ms_kegiatan')->whereRaw("kd_kegiatan=?", [$sub_kegiatan])->first(),
            'data_spp' => $kegiatan,
            'data_spp_rinci' => DB::select("SELECT a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan,kd_rek6,nm_rek6,sum(b.nilai)as nilaispp FROM trhspp a inner join trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd where a.no_spp=? AND b.kd_skpd=? and b.kd_sub_kegiatan=? GROUP BY a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan,kd_rek6,nm_rek6", [$no_spp, $kd_skpd, $kegiatan->kd_sub_kegiatan]),
            'spp' => DB::table('trhspp')->where(['no_spp' => $no_spp])->first()
        ];

        $view = view('skpd.spp_tu.cetak.rincian77')->with($data);
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
}
