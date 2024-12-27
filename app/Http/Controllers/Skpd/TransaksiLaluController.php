<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransaksiLaluController extends Controller
{
    public function index()
    {
        return view('skpd.transaksi_lalu.index');
    }

    public function load()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT a.*,'' AS nokas_pot,'' AS tgl_pot,(select count(*) from trhtrmpot where no_kas=a.no_bukti and kd_skpd=a.kd_skpd) AS ketpot,(SELECT COUNT(*) from trlpj z
        join trhlpj v on v.no_lpj = z.no_lpj
        where v.jenis=a.jns_spp and z.no_bukti = a.no_bukti and z.kd_skpd = a.kd_skpd) ketlpj,
		0 ketspj,(select rekening from ms_skpd where kd_skpd=?) as rekening_awal FROM trhtransout a
        WHERE  a.panjar = '4' AND a.kd_skpd=? and a.pay='BANK'
         order by CAST (a.no_bukti as NUMERIC),kd_skpd", [$kd_skpd, $kd_skpd]);

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            // $btn = '<a href="' . route("transaksipanjar.edit", ['no_bukti' => Crypt::encrypt($row->no_bukti), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn = '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_bukti . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambah()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'no_urut' => no_urut($kd_skpd),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'daftar_rekening' => DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->get(),
            'data_rek_tujuan' => DB::table('ms_rekening_bank_online as a')->where(['kd_skpd' => $kd_skpd])->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nmbank"))->orderBy('a.nm_rekening')->get(),
            'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->get(),
        ];

        return view('skpd.transaksi_lalu.create')->with($data);
    }

    public function kegiatan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jns_ang = status_anggaran();
        $beban = $request->beban;

        $data = DB::select("SELECT a.kd_sub_kegiatan,a.nm_sub_kegiatan, sum(a.nilai) as total from trdrka a
                inner join trskpd b on a.kd_skpd=b.kd_skpd and a.kd_sub_kegiatan=b.kd_sub_kegiatan
            where a.kd_skpd=? and b.status_sub_kegiatan='1' and b.jns_ang=?
            group by  a.kd_sub_kegiatan,a.nm_sub_kegiatan
            order by a.kd_sub_kegiatan", [$kd_skpd, $jns_ang]);

        return response()->json($data);
    }

    public function sp2d(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $beban = $request->beban;
        $kode = substr($kd_skpd, 0, 17);

        if ($beban == '3') {
            $data = DB::select("SELECT DISTINCT c.no_sp2d,c.tgl_sp2d,c.nilai,
                    0 as sisa
                    FROM trhspp a
                    INNER JOIN trhspm b ON a.no_spp=b.no_spp AND a.kd_skpd=b.kd_skpd
                    INNER JOIN trhsp2d c ON b.no_spm=c.no_spm AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trdspp d ON a.no_spp=d.no_spp AND a.kd_skpd=d.kd_skpd
                    WHERE c.kd_skpd = ? AND c.status = 1 and a.jns_spp=? and d.kd_sub_kegiatan=?
                    AND c.no_sp2d
                    NOT IN (SELECT no_sp2d FROM trhlpj WHERE kd_skpd=?) ORDER BY c.tgl_sp2d DESC, c.no_sp2d", [$kd_skpd, $beban, $kd_sub_kegiatan, $kd_skpd]);
        } elseif ($beban == '6') {
            $data = DB::select("SELECT DISTINCT c.no_sp2d,c.tgl_sp2d,c.nilai,
                    0 as sisa
                    FROM trhspp a
                    INNER JOIN trhspm b ON a.no_spp=b.no_spp AND a.kd_skpd=b.kd_skpd
                    INNER JOIN trhsp2d c ON b.no_spm=c.no_spm AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trdspp d ON a.no_spp=d.no_spp AND a.kd_skpd=d.kd_skpd
                    WHERE c.kd_skpd = ? AND c.status = 1 and a.jns_spp=? and d.kd_sub_kegiatan=? ORDER BY c.tgl_sp2d DESC, c.no_sp2d", [$kd_skpd, $beban, $kd_sub_kegiatan]);
        } elseif ($beban == '1') {
            $data = DB::select("SELECT DISTINCT c.no_sp2d,c.tgl_sp2d,c.nilai,
                    0 as sisa
                    FROM trhspp a
                    INNER JOIN trhspm b ON a.no_spp=b.no_spp AND a.kd_skpd=b.kd_skpd
                    INNER JOIN trhsp2d c ON b.no_spm=c.no_spm AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trdspp d ON a.no_spp=d.no_spp AND a.kd_skpd=d.kd_skpd
                    WHERE left(c.kd_skpd,17) = ? AND c.status = 1 and a.jns_spp IN ('1','2') ORDER BY c.tgl_sp2d DESC, c.no_sp2d", [$kode]);
        } else {
            $data = DB::select("SELECT DISTINCT c.no_sp2d,c.tgl_sp2d,c.nilai,
                    0 as sisa
                    FROM trhspp a
                    INNER JOIN trhspm b ON a.no_spp=b.no_spp AND a.kd_skpd=b.kd_skpd
                    INNER JOIN trhsp2d c ON b.no_spm=c.no_spm AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trdspp d ON a.no_spp=d.no_spp AND a.kd_skpd=d.kd_skpd
                    WHERE left(c.kd_skpd,17) = ? AND c.status = 1 and a.jns_spp=? and d.kd_sub_kegiatan=? ORDER BY c.tgl_sp2d DESC, c.no_sp2d", [$kode, $beban, $kd_sub_kegiatan]);
        }

        return response()->json($data);
    }

    public function rekening(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = $request->kd_skpd;
        $beban = $request->beban;
        $no_sp2d = $request->no_sp2d;

        $status_anggaran = status_anggaran();

        if ($beban == '1') {
            $data = DB::select("SELECT a.kd_rek6,a.nm_rek6,
                    (SELECT SUM(nilai) FROM
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
						AND d.jns_spp=?
						UNION ALL
					SELECT SUM(nilai) FROM
						(SELECT
							SUM (c.nilai) as nilai
						FROM
							trdtransout_cmsbank c
						LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher
						AND c.kd_skpd = d.kd_skpd
						WHERE
							c.kd_sub_kegiatan = a.kd_sub_kegiatan
						AND d.kd_skpd = a.kd_skpd
						AND c.kd_rek6 = a.kd_rek6
						AND c.no_voucher <> ?
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
						)r) r) AS lalu,
						0 AS sp2d,nilai AS anggaran
						FROM trdrka a WHERE a.kd_sub_kegiatan= ?
                        AND a.kd_skpd = ?
                        and a.status_aktif='1'
                        and jns_ang=?
                        order by a.kd_rek6", [$beban, $no_bukti, $beban, $kd_skpd, $kd_sub_kegiatan, $kd_skpd, $status_anggaran]);
        } else {
            $data = DB::select("SELECT kd_rek6, nm_rek6,
            (SELECT SUM(nilai) FROM
                        (SELECT
                            SUM (c.nilai) as nilai
                        FROM
                            trdtransout c
                        LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
                        AND c.kd_skpd = d.kd_skpd
                        WHERE
                            c.kd_sub_kegiatan = x.kd_sub_kegiatan
                        AND d.kd_skpd = x.kd_skpd
                        AND c.kd_rek6 = x.kd_rek6
                        AND d.jns_spp=?
                        and d.no_sp2d = ?
                        UNION ALL

                        SELECT SUM(nilai) FROM
                        (SELECT
                            SUM (c.nilai) as nilai
                        FROM
                            trdtransout_cmsbank c
                        LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher
                        AND c.kd_skpd = d.kd_skpd
                        WHERE
                            c.kd_sub_kegiatan = x.kd_sub_kegiatan
                        AND d.kd_skpd = x.kd_skpd
                        AND c.kd_rek6 = x.kd_rek6
                        AND c.no_voucher <> ?
                        AND d.jns_spp=?
                        AND d.status_validasi<>'1'
                        and d.no_sp2d = ?
                        )r

                        ) r) AS lalu,

            sp2d, 0 AS anggaran  from(
            SELECT b.kd_skpd,b.kd_sub_kegiatan,b.kd_rek6,b.nm_rek6, sum(b.nilai) AS sp2d, 0 AS anggaran
            FROM trhspp a
            INNER JOIN trdspp b ON a.no_spp=b.no_spp AND a.kd_skpd = b.kd_skpd
            INNER JOIN trhspm c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
            INNER JOIN trhsp2d d ON c.no_spm=d.no_Spm AND c.kd_skpd=d.kd_skpd
            WHERE d.no_sp2d = ? and b.kd_sub_kegiatan=?
            group by b.kd_skpd,b.kd_sub_kegiatan,b.kd_rek6,b.nm_rek6
            )x", [$beban, $no_sp2d, $no_bukti, $beban, $no_sp2d, $no_sp2d, $kd_sub_kegiatan]);
        }

        return response()->json($data);
    }

    public function angkasSpd(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_rek6 = $request->kd_rek6;
        $tgl_kas = $request->tgl_kas;
        $beban = $request->beban;
        $no_sp2d = $request->no_sp2d;
        $bulan = date('m', strtotime($tgl_kas));
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
            where a.kd_skpd = ? and  a.kd_sub_kegiatan = ? and a.kd_rek6=? and (bulan <=?) and jns_ang=? GROUP BY a.kd_sub_kegiatan,a.kd_rek6", [$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $bulan1, $status_anggaran]))->first();
        } else {
            $nilai_angkas = collect(DB::select("SELECT  a.kd_sub_kegiatan, SUM(a.$field_angkas) as nilai FROM trdskpd_ro a INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd and a.kd_sub_kegiatan=b.kd_sub_kegiatan
            where a.kd_skpd = ? and  a.kd_sub_kegiatan = ? and a.kd_rek6=? and (bulan <=?) and jns_ang=? GROUP BY a.kd_sub_kegiatan,a.kd_rek6", [$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $bulan, $status_anggaran]))->first();
        }


        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where left(kd_skpd,17)=left(?,17) and bulan_akhir='3'", [$kd_skpd]))->first();

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where left(kd_skpd,17)=left(?,17) and bulan_akhir='6'", [$kd_skpd]))->first();

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where left(kd_skpd,17)=left(?,17) and bulan_akhir='9'", [$kd_skpd]))->first();

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where left(kd_skpd,17)=left(?,17) and bulan_akhir='12'", [$kd_skpd]))->first();

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
                    and revisi_ke=?)spd", [$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $revisi1->revisi, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $revisi2->revisi, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $revisi3->revisi, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $revisi4->revisi,]))->first();

        if ($beban == '1') {
            $total_trans = collect(DB::select("SELECT SUM(nilai) total FROM
                                    (
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
                                    AND t.kd_rek6 =?
                                    AND u.kd_skpd = ?
                                    AND u.no_bukti
                                    NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=?)
                                    )r", [$kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $kd_rek6, $kd_skpd, $kd_skpd]))->first();
        } else {
            $spp = DB::table('trhsp2d')
                ->select('no_spp')
                ->where(['no_sp2d' => $no_sp2d])
                ->first();
            $no_spp = $spp->no_spp;

            $total_trans = collect(DB::select("SELECT SUM(nilai) total FROM
                                    (
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
                                    AND y.no_spp<>?
                                    AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')

                                    UNION ALL
                                    -- Penagihan yang belum jadi SPP
                                    SELECT SUM(isnull(nilai,0)) as nilai FROM trdtagih t
                                    INNER JOIN trhtagih u
                                    ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                                    WHERE t.kd_sub_kegiatan =?
                                    AND t.kd_rek6 =?
                                    AND u.kd_skpd = ?
                                    AND u.no_bukti
                                    NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=?)
                                    )r", [$kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_sub_kegiatan, $kd_skpd, $kd_rek6, $no_spp, $kd_sub_kegiatan, $kd_rek6, $kd_skpd, $kd_skpd]))->first();
        }

        return response()->json([
            'angkas' => $nilai_angkas->nilai,
            'spd' => $nilai_spd->total_spd,
            'transaksi' => $total_trans->total,
        ]);
    }

    public function potongan(Request $request)
    {
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = Auth::user()->kd_skpd;

        $sisa_bank = collect(DB::select("SELECT
            SUM(case when jns=1 then jumlah else 0 end) AS terima,
            SUM(case when jns=2 then jumlah else 0 end) AS keluar
            from (
            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_setorsimpanan
            union all
            SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM trhINlain WHERE pay='BANK'
            union all
            select c.tgl_kas [tgl],c.no_kas [bku] ,c.keterangan [ket],c.nilai [jumlah],'1' [jns],c.kd_skpd [kode] from tr_jpanjar c join tr_panjar d on c.no_panjar_lalu=d.no_panjar and c.kd_skpd=d.kd_skpd where c.jns='2' and c.kd_skpd=? and  d.pay='BANK'
            union all
            select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],sum(b.nilai) [jumlah],'1' [jns],a.kd_skpd [kode] from trhtrmpot a
            join trdtrmpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
            where a.kd_skpd=? and a.pay='BANK' and jns_spp not in('1','2','3') group by a.tgl_bukti,a.no_bukti,a.ket,a.kd_skpd
            union all
            select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
            from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
            where jns_trans IN ('5') and bank='BNK' and a.kd_skpd=?
            GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
            union all
            SELECT tgl_bukti AS tgl,no_bukti AS bku,ket AS ket,total-isnull(pot,0)-isnull(f.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM trhtransout a join trhsp2d b on a.no_sp2d=b.no_sp2d left join (select no_spm, sum(nilai)pot
                from trspmpot group by no_spm) c on b.no_spm=c.no_spm
                left join
                    (select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd where e.kd_skpd=? and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
                    ) f on f.no_kas=a.no_bukti and f.kd_skpd=a.kd_skpd WHERE pay='BANK' and (panjar not in ('1') or panjar is null)
             union all
            select a.tgl_bukti [tgl],a.no_bukti [bku],a.ket [ket],sum(b.nilai) [jumlah],'2' [jns],a.kd_skpd [kode] from trhstrpot a
            join trdstrpot b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
            where a.kd_skpd=? and a.pay='BANK' group by a.tgl_bukti,a.no_bukti,a.ket,a.kd_skpd
            UNION ALL
            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_ambilsimpanan
            union all
            SELECT tgl_bukti AS tgl,no_bukti AS bku,ket as ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM trhoutlain WHERE pay='BANK'
            union all
            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'2' AS jns,kd_skpd_sumber AS kode FROM tr_setorpelimpahan_bank
            union all
            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan AS ket,nilai AS jumlah,'2' AS jns,kd_skpd AS kode FROM tr_ambilsimpanan WHERE status_drop!='1'
            union all
            SELECT a.tgl_kas AS tgl,a.no_panjar AS bku,a.keterangan as ket,a.nilai-isnull(b.pot2,0) AS jumlah,'2' AS jns,a.kd_skpd AS kode FROM tr_panjar a
            left join
            (
                select d.no_kas,sum(e.nilai) [pot2],d.kd_skpd from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
                where e.kd_skpd=? and d.no_kas<>'' and d.pay='BANK' group by d.no_kas,d.kd_skpd
             ) b on a.no_panjar=b.no_kas and a.kd_skpd=b.kd_skpd
            where a.pay='BANK' and a.kd_skpd=?
            union all
            select d.tgl_bukti, d.no_bukti,d.ket [ket],sum(e.nilai) [jumlah],'1' [jns],d.kd_skpd [kode] from trhtrmpot d join trdtrmpot e on d.no_bukti=e.no_bukti and d.kd_skpd=e.kd_skpd
            where e.kd_skpd=? and d.no_sp2d in ('2977/TU/2022','8379/TU/2022','5250/TU/2022','8523/TU/2022','1182/TU/2022','1888/TU/2022','1886/TU/2022','5249/TU/2022','8380/TU/2022') and d.pay='BANK' group by d.tgl_bukti,d.no_bukti,d.ket,d.kd_skpd
            union all
            select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '2' as jns, a.kd_skpd as kode
            from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
            where jns_trans NOT IN ('4','2','5') and pot_khusus =0  and bank='BNK' and a.kd_skpd=?
            GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
            union all
            select a.tgl_sts as tgl,a.no_sts as bku, a.keterangan as ket, SUM(b.rupiah) as jumlah, '1' as jns, a.kd_skpd as kode
            from trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
            where jns_trans IN ('5') and bank='BNK' and a.kd_skpd=?
            GROUP BY a.tgl_sts,a.no_sts, a.keterangan,a.kd_skpd
            ) a
        where  kode=?", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]))->first();

        $potongan_ls = collect(DB::select("SELECT SUM(a.nilai) as total  FROM trspmpot a INNER JOIN trhsp2d b on b.no_spm = a.no_spm AND b.kd_skpd=a.kd_skpd
        where ((b.jns_spp = '4' AND b.jenis_beban != '1') or (b.jns_spp = '6' AND b.jenis_beban != '3'))
        and b.no_sp2d = ? and b.kd_skpd= ?", [$no_sp2d, $kd_skpd]))->first();

        return response()->json([
            'sisa_bank' => $sisa_bank->terima - $sisa_bank->keluar,
            'potongan' => $potongan_ls->total,
        ]);
    }

    public function sumberDana(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = $request->kd_skpd;
        $sumber = $request->sumber;
        $kd_rek6 = $request->kd_rek6;
        $no_sp2d = $request->no_sp2d;
        $beban = $request->beban;

        $no_trdrka = $kd_skpd . '.' . $kd_sub_kegiatan . '.' . $kd_rek6;
        $status_anggaran = status_anggaran();

        $data1 = DB::table('trdpo')
            ->select('sumber', 'nm_sumber', DB::raw("SUM(total) as nilai"))
            ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $status_anggaran])
            ->whereNotNull('sumber')
            ->groupBy('sumber', 'nm_sumber');

        $data2 = DB::table('trdpo')
            ->select('sumber', DB::raw("'Silahkan isi sumber di anggaran' as nm_sumber"), DB::raw("SUM(total) as nilai"))
            ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $status_anggaran])
            ->where(function ($query) {
                $query->where('sumber', '')->orWhereNull('sumber');
            })
            ->groupBy('sumber', 'nm_sumber')
            ->union($data1);

        $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
            ->mergeBindings($data2)
            ->get();

        return response()->json($data);
    }

    public function sumber(Request $request)
    {
        $tgl_bukti = $request->tgl_bukti;
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_sp2d = $request->no_sp2d;
        $beban = $request->beban;
        $kd_rek6 = $request->kd_rek6;
        $jenis_ang = status_anggaran();

        if ($beban == '1') {
            $no_trdrka = $kd_skpd . '.' . $kd_sub_kegiatan . '.' . $kd_rek6;

            $data1 = DB::table('trdpo')
                ->select('sumber as sumber_dana', 'nm_sumber', DB::raw("SUM(total) as nilai"))
                ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $jenis_ang])
                ->whereNotNull('sumber')
                ->groupBy('sumber', 'nm_sumber');

            $data2 = DB::table('trdpo')
                ->select('sumber as sumber_dana', DB::raw("'Silahkan isi sumber di anggaran' as nm_sumber"), DB::raw("SUM(total) as nilai"))
                ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $jenis_ang])
                ->where(function ($query) {
                    $query->where('sumber', '')->orWhereNull('sumber');
                })
                ->groupBy('sumber', 'nm_sumber')
                ->union($data1);

            $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
                ->mergeBindings($data2)
                ->get();
        } else {
            $data = DB::table('trhspp as a')
                ->join('trdspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->join('trhsp2d as c', function ($join) {
                    $join->on('a.no_spp', '=', 'c.no_spp');
                    $join->on('a.kd_skpd', '=', 'c.kd_skpd');
                })
                ->where(['c.no_sp2d' => $no_sp2d, 'a.kd_skpd' => $kd_skpd, 'b.kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rek6])
                ->groupBy('b.sumber')
                ->select('b.sumber as sumber_dana', DB::raw("SUM(b.nilai) as nilai"), DB::raw("SUM(b.nilai) as nilai_sempurna"), DB::raw("SUM(b.nilai) as nilai_ubah"))
                ->get();
        }
        return response()->json($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhtransout')
                ->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $data['kd_skpd']])
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '4'
                ]);
            }

            DB::table('trhtransout')
                ->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            DB::table('trhtransout')
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
                    'no_tagih' => '',
                    'sts_tagih' => '0',
                    'tgl_tagih' => '',
                    'jns_spp' => $data['beban'],
                    'pay' => $data['pembayaran'],
                    'no_kas_pot' => $data['no_bukti'],
                    'panjar' => '4',
                    'no_sp2d' => $data['nomor_sp2d'],
                ]);

            DB::table('trdtransout')
                ->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            DB::table('trdtransout_transfer')
                ->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            if (isset($data['rincian_rekening'])) {
                DB::table('trdtransout')->insert(array_map(function ($value) use ($data, $kd_skpd) {
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
                        'volume' => $value['volume'],
                        'satuan' => $value['satuan'],
                    ];
                }, $data['rincian_rekening']));
            }

            if (isset($data['rincian_rek_tujuan'])) {
                DB::table('trdtransout_transfer')->insert(array_map(function ($value) use ($data, $kd_skpd) {
                    return [
                        'no_bukti' => $data['no_bukti'],
                        'tgl_bukti' => $value['tgl_bukti'],
                        'rekening_awal' => $value['rekening_awal'],
                        'nm_rekening_tujuan' => $value['nm_rekening_tujuan'],
                        'rekening_tujuan' => $value['rekening_tujuan'],
                        'bank_tujuan' => $value['bank_tujuan'],
                        'kd_skpd' => $value['kd_skpd'],
                        'nilai' => $value['nilai'],
                    ];
                }, $data['rincian_rek_tujuan']));
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

    public function edit($no_panjar, $kd_skpd)
    {
        $no_panjar = Crypt::decrypt($no_panjar);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $panjar = DB::table('tr_jpanjar')
            ->where(['no_kas' => $no_panjar, 'kd_skpd' => $kd_skpd])
            ->first();

        $data = [
            'panjar' => $panjar,
            'load_detail' => collect(DB::select("SELECT no_panjar, nilai,(SELECT no_panjar from tr_panjar where jns = '2' AND no_panjar_lalu = ? AND kd_skpd=?) as no_panjar2,
					(SELECT nilai from tr_panjar where jns = '2' AND no_panjar_lalu = ? AND kd_skpd=? ) as nilai2
					FROM tr_panjar WHERE no_panjar_lalu = ? AND jns = '1' AND kd_skpd=?", [$panjar->no_panjar, $kd_skpd, $panjar->no_panjar, $kd_skpd, $panjar->no_panjar, $kd_skpd]))->first(),
            'load_total' => collect(DB::select("SELECT SUM(a.nilai) as panjar, (SELECT SUM(c.nilai) FROM trdtransout c join trhtransout b on c.no_bukti = b.no_bukti AND c.kd_skpd=b.kd_skpd WHERE b.no_panjar = ? and b.panjar = '1' AND b.kd_skpd=?) as trans FROM tr_panjar a WHERE a.no_panjar_lalu = ? AND a.kd_skpd=? GROUP BY kd_skpd", [$panjar->no_panjar, $kd_skpd, $panjar->no_panjar, $kd_skpd]))->first()
        ];

        return view('skpd.transaksi_lalu.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek_panjar = collect(DB::select("SELECT count(*) as jumlah FROM (select no_panjar as nomor FROM tr_panjar WHERE kd_skpd = ? UNION ALL SELECT no_kas as nomor FROM tr_jpanjar WHERE kd_skpd = ?)a WHERE a.nomor = ?", [$kd_skpd, $kd_skpd, $data['no_panjar']]))->first();
            if ($cek_panjar->jumlah > 0) {
                return response()->json([
                    'message' => '4'
                ]);
            }

            DB::table('tr_jpanjar')
                ->where(['kd_skpd' => $kd_skpd, 'no_kas' => $data['no_simpan']])
                ->update([
                    'no_kas' => $data['no_panjar'],
                    'tgl_kas' => $data['tgl_panjar'],
                    'keterangan' => $data['keterangan'],
                    'pengguna' => Auth::user()->nama
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
        $no_bukti = $request->no_bukti;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trdtransout')
                ->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::table('trhtransout')
                ->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::table('trdtransout_transfer')
                ->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])
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
