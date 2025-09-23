<?php

namespace App\Http\Controllers\Skpd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Static_;
use Yajra\DataTables\Facades\DataTables;

class TransaksiCmsController extends Controller
{
    public function index()
    {
        $data = [
            'cek' => selisih_angkas()
        ];
        return view('skpd.transaksi_cms.index')->with($data);
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtransout_cmsbank as a')->where(['a.panjar' => '0', 'kd_skpd' => $kd_skpd])->select('a.*', DB::raw("'' as nokas_pot"), DB::raw("'' as tgl_pot"), DB::raw("'' as kete"))->orderBy('tgl_voucher')->orderBy(DB::raw("CAST(a.no_bukti as int)"))->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.transaksi_cms.edit", Crypt::encryptString($row->no_voucher)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->status_upload == '1' || $row->status_trmpot == '1') {
                $btn .= '';
            } else {
                $btn .= '<a href="javascript:void(0);" onclick="deleteData(\'' . $row->no_voucher . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="fas fa-trash-alt"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'data_rek' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->first(),
            'data_rek_tujuan' => DB::table('ms_rekening_bank_online as a')->where(['kd_skpd' => $kd_skpd])->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nmbank"))->orderBy('a.nm_rekening')->get(),
            'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->get(),
            'persen' => DB::table('config_app')->select('persen_kkpd', 'persen_tunai')->first(),
        ];

        return view('skpd.transaksi_cms.create')->with($data);
    }

    public function no_urut(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $urut1 = DB::table('trhtransout_cmsbank')
            ->where(['kd_skpd' => $kd_skpd])
            ->select('no_voucher as nomor', DB::raw("'Daftar Transaksi Non Tunai' as ket"), 'kd_skpd');
        $urut2 = DB::table('trhtrmpot_cmsbank')
            ->where(['kd_skpd' => $kd_skpd])
            ->select('no_bukti as nomor', DB::raw("'Potongan Pajak Transaksi Non Tunai' as ket"), 'kd_skpd')
            ->unionAll($urut1);
        $urut3 = DB::table('tr_panjar_cmsbank')
            ->where(['kd_skpd' => $kd_skpd])
            ->select('no_panjar as nomor', DB::raw("'Daftar Panjar' as ket"), 'kd_skpd')
            ->unionAll($urut2);

        $urut = DB::table(DB::raw("({$urut3->toSql()}) AS sub"))
            ->select(DB::raw("CASE WHEN MAX(nomor+1) IS NULL THEN 1 ELSE MAX(nomor+1) END AS nomor"))
            ->mergeBindings($urut3)
            ->first();
        return response()->json($urut->nomor);
    }

    public function skpd()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first();
        return response()->json($data);
    }

    public function cariKegiatan(Request $request)
    {
        $beban = $request->beban;
        $kd_skpd = $request->kd_skpd;
        $anggaran = status_anggaran();

        $data = DB::table('trskpd as a')
            ->join('ms_sub_kegiatan as b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')
            ->where(['a.kd_skpd' => $kd_skpd, 'a.status_sub_kegiatan' => '1', 'b.jns_sub_kegiatan' => '5', 'a.jns_ang' => $anggaran])
            ->select('a.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', DB::raw("(SELECT nm_program FROM ms_program WHERE kd_program=a.kd_program) as nm_program"), 'a.total')
            ->get();

        return response()->json($data);
    }

    public function cariSp2d(Request $request)
    {
        $beban = $request->beban;
        $kd_skpd = $request->kd_skpd;
        $kode = substr($kd_skpd, 0, 17);
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_bukti = $request->no_bukti;
        $tgl = $request->tgl;

        if ((isset($beban) && empty($kd_sub_kegiatan)) || ($beban == '1')) {
            $where = "a.jns_spp IN ('1','2')";
        }
        if (isset($kd_sub_kegiatan) && $beban != '1') {
            $where = 'a.jns_spp=? AND d.kd_sub_kegiatan =?';
        }
        if ($beban == '3') {
            $data = DB::table('trhspp as a')->join('trhspm as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->join('trhsp2d as c', function ($join) {
                $join->on('b.no_spm', '=', 'c.no_spm');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->join('trdspp as d', function ($join) {
                $join->on('a.no_spp', '=', 'd.no_spp');
                $join->on('a.kd_skpd', '=', 'd.kd_skpd');
            })->where(['c.kd_skpd' => $kd_skpd, 'c.status' => '1'])->where('c.tgl_kas', '<=', $tgl)->whereRaw($where, [$beban, $kd_sub_kegiatan])->whereRaw('c.no_sp2d NOT IN (SELECT no_sp2d FROM trhlpj WHERE kd_skpd=?)', [$kd_skpd])->orderByDesc('c.tgl_sp2d')->orderBy('c.no_sp2d')->select('c.no_sp2d', 'c.tgl_sp2d', 'c.nilai', DB::raw("'0' as sisa"))->distinct()->get();
        } else if ($beban == '6') {
            $data = DB::table('trhspp as a')->join('trhspm as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->join('trhsp2d as c', function ($join) {
                $join->on('b.no_spm', '=', 'c.no_spm');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->join('trdspp as d', function ($join) {
                $join->on('a.no_spp', '=', 'd.no_spp');
                $join->on('a.kd_skpd', '=', 'd.kd_skpd');
            })->where(['c.kd_skpd' => $kd_skpd, 'c.status' => '1'])->whereRaw($where, [$beban, $kd_sub_kegiatan])->orderByDesc('c.tgl_sp2d')->orderBy('c.no_sp2d')->select('c.no_sp2d', 'c.tgl_sp2d', 'c.nilai', DB::raw("'0' as sisa"))->distinct()->get();
        } else {
            $data = DB::table('trhspp as a')->join('trhspm as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->join('trhsp2d as c', function ($join) {
                $join->on('b.no_spm', '=', 'c.no_spm');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->join('trdspp as d', function ($join) {
                $join->on('a.no_spp', '=', 'd.no_spp');
                $join->on('a.kd_skpd', '=', 'd.kd_skpd');
            })->where(['c.status' => '1'])->where('c.tgl_kas', '<=', $tgl)->where(DB::raw("LEFT(c.kd_skpd,17)"), $kode)->whereRaw($where, [$beban, $kd_sub_kegiatan])->orderByDesc('c.tgl_sp2d')->orderBy('c.no_sp2d')->select('c.no_sp2d', 'c.tgl_sp2d', 'c.nilai', DB::raw("'0' as sisa"))->distinct()->get();
        }
        return response()->json($data);
    }

    public function cariRekening(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_bukti = $request->no_bukti;
        $beban = $request->beban;
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = $request->kd_skpd;
        $jenis_ang = status_anggaran();

        $rekening = cari_rekening($kd_sub_kegiatan, $kd_skpd, $jenis_ang, $beban, $no_bukti, $no_sp2d);
        return response()->json($rekening);
    }

    public function cariSumber(Request $request)
    {
        $kd_rek6 = $request->kd_rek6;
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_sp2d = $request->no_sp2d;
        $beban = $request->beban;
        $jenis_ang = status_anggaran();

        // if ($beban == '1') {
        //     $no_trdrka = $kd_skpd . '.' . $kd_sub_kegiatan . '.' . $kd_rek6;

        //     $data1 = DB::table('trdpo')
        //         ->select('sumber as sumber_dana', 'nm_sumber', DB::raw("SUM(total) as nilai"))
        //         ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $jenis_ang])
        //         ->whereNotNull('sumber')
        //         ->groupBy('sumber', 'nm_sumber');

        //     $data2 = DB::table('trdpo')
        //         ->select('sumber as sumber_dana', DB::raw("'Silahkan isi sumber di anggaran' as nm_sumber"), DB::raw("SUM(total) as nilai"))
        //         ->where(['no_trdrka' => $no_trdrka, 'jns_ang' => $jenis_ang])
        //         ->where(function ($query) {
        //             $query->where('sumber', '')->orWhereNull('sumber');
        //         })
        //         ->groupBy('sumber', 'nm_sumber')
        //         ->union($data1);

        //     $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
        //         ->mergeBindings($data2)
        //         ->get();
        // } else {
        //     $data = DB::table('trhspp as a')
        //         ->join('trdspp as b', function ($join) {
        //             $join->on('a.no_spp', '=', 'b.no_spp');
        //             $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //         })->join('trhsp2d as c', function ($join) {
        //             $join->on('a.no_spp', '=', 'c.no_spp');
        //             $join->on('a.kd_skpd', '=', 'c.kd_skpd');
        //         })
        //         ->where(['c.no_sp2d' => $no_sp2d, 'a.kd_skpd' => $kd_skpd, 'b.kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rek6])
        //         ->groupBy('b.sumber')
        //         ->select('b.sumber as sumber_dana', DB::raw("SUM(b.nilai) as nilai"), DB::raw("SUM(b.nilai) as nilai_sempurna"), DB::raw("SUM(b.nilai) as nilai_ubah"))
        //         ->get();
        // }
        $data = DB::select("SELECT sumber as sumber_dana,nm_sumber,sum(total) as nilai , (SELECT ISNULL(SUM(nilai),0) as nilai FROM trdtagih t  INNER JOIN trhtagih u  ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd WHERE  t.kd_sub_kegiatan = ? AND
        u.kd_skpd = ? AND t.kd_rek = ? AND u.no_bukti  NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=? ) and sumber=sumber)as lalu from trdpo where kd_sub_kegiatan = ? and kd_rek6 = ? and kd_skpd = ? and jns_ang = ? GROUP BY sumber, nm_sumber", [$kd_sub_kegiatan, $kd_skpd, $kd_rek6, $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $kd_skpd, $jenis_ang]);

        return response()->json($data);
    }

    public function sisaBank(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        // $data1 = DB::table('tr_setorsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode');

        // $data2 = DB::table('TRHINLAIN')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where(['pay' => 'BANK'])->unionAll($data1);

        // $data3 = DB::table('tr_jpanjar as a')->join('tr_panjar as b', function ($join) {
        //     $join->on('a.no_panjar', '=', 'b.no_panjar');
        //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        // })->where(['a.jns' => '2', 'a.kd_skpd' => $kd_skpd, 'b.pay' => 'BANK'])->select('a.tgl_kas as tgl', 'a.no_kas as bku', 'a.keterangan as ket', 'a.nilai as jumlah', DB::raw("'1' as jns"), 'a.kd_skpd as kode')->unionAll($data2);

        // $data4 = DB::table('trhtrmpot as a')->join('trdtrmpot as b', function ($join) {
        //     $join->on('a.no_bukti', '=', 'b.no_bukti');
        //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        // })->where(['a.kd_skpd' => $kd_skpd, 'a.pay' => 'BANK'])->whereNotIn('jns_spp', ['1', '2', '3'])->groupBy('a.tgl_bukti', 'a.no_bukti', 'a.ket', 'a.kd_skpd')->select('a.tgl_bukti as tgl', 'a.no_bukti as bku', 'a.ket as ket', DB::raw("SUM(b.nilai) as jumlah"), DB::raw("'1' as jns"), 'a.kd_skpd as kode')->unionAll($data3);

        // $data5 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
        //     $join->on('a.no_sts', '=', 'b.no_sts');
        //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        // })->where(['a.kd_skpd' => $kd_skpd, 'bank' => 'BNK', 'jns_trans' => '5'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data4);

        // $joinsub = DB::table('trspmpot')->select('no_spm', DB::raw("SUM(nilai) as pot"))->groupBy('no_spm');
        // $joinsub1 = DB::table('trhtrmpot as d')->join('trdtrmpot as e', function ($join) {
        //     $join->on('d.no_bukti', '=', 'e.no_bukti');
        //     $join->on('d.kd_skpd', '=', 'e.kd_skpd');
        // })->where(['e.kd_skpd' => $kd_skpd, 'd.pay' => 'BANK'])->where('d.no_kas', '<>', '')->select('d.no_kas', DB::raw("SUM(e.nilai) as pot2"), 'd.kd_skpd')->groupBy('d.no_kas', 'd.kd_skpd');

        // $data6 = DB::table('trhtransout as a')->join('trhsp2d as b', function ($join) {
        //     $join->on('a.no_sp2d', '=', 'b.no_sp2d');
        // })->leftJoinSub($joinsub, 'c', function ($join) {
        //     $join->on('b.no_spm', '=', 'c.no_spm');
        // })->leftJoinSub($joinsub1, 'f', function ($join) {
        //     $join->on('f.no_kas', '=', 'a.no_bukti');
        //     $join->on('f.kd_skpd', '=', 'a.kd_skpd');
        // })->where(['pay' => 'BANK'])->where(function ($query) {
        //     $query->where('panjar', '<>', '1')->orWhereNull('panjar');
        // })->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket', DB::raw("total-ISNULL(pot,0)-ISNULL(f.pot2,0) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data5);

        // $data7 = DB::table('trhstrpot as a')->join('trdstrpot as b', function ($join) {
        //     $join->on('a.no_bukti', '=', 'b.no_bukti');
        //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        // })->where(['a.kd_skpd' => $kd_skpd, 'a.pay' => 'BANK'])->groupBy('a.tgl_bukti', 'a.no_bukti', 'a.ket', 'a.kd_skpd')->select('a.tgl_bukti as tgl', 'a.no_bukti as bku', 'a.ket as ket', DB::raw("SUM(b.nilai) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data6);

        // $data8 = DB::table('tr_ambilsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->unionAll($data7);

        // $data9 = DB::table('trhoutlain')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where(['pay' => 'BANK'])->unionAll($data8);

        // $data10 = DB::table('tr_setorpelimpahan_bank')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd_sumber as kode')->unionAll($data9);

        // $data11 = DB::table('tr_ambilsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where('status_drop', '!=', '1')->unionAll($data10);

        // $data12 = DB::table('tr_panjar as a')->leftJoinSub($joinsub1, 'b', function ($join) {
        //     $join->on('a.no_panjar', '=', 'b.no_kas');
        //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        // })->where(['a.pay' => 'BANK', 'a.kd_skpd' => $kd_skpd])->select('a.tgl_kas as tgl', 'a.no_panjar as bku', 'a.keterangan as ket', DB::raw("a.nilai-ISNULL(b.pot2,0) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data11);

        // $data13 = DB::table('trhtrmpot as d')->join('trdtrmpot as e', function ($join) {
        //     $join->on('d.no_bukti', '=', 'e.no_bukti');
        //     $join->on('d.kd_skpd', '=', 'e.kd_skpd');
        // })->where(['d.no_sp2d' => '2977/TU/2022', 'e.kd_skpd' => $kd_skpd, 'd.pay' => 'BANK'])->groupBy('d.tgl_bukti', 'd.no_bukti', 'd.ket', 'd.kd_skpd')->select('d.tgl_bukti as tgl', 'd.no_bukti as bku', 'd.ket as ket', DB::raw("SUM(e.nilai) as jumlah"), DB::raw("'1' as jns"), 'd.kd_skpd as kode')->unionAll($data12);

        // $data14 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
        //     $join->on('a.no_sts', '=', 'b.no_sts');
        //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        // })->where(['pot_khusus' => '0', 'a.kd_skpd' => $kd_skpd, 'bank' => 'BNK'])->whereNotIn('jns_trans', ['2', '4', '5'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data13);

        // $data15 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
        //     $join->on('a.no_sts', '=', 'b.no_sts');
        //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        // })->where(['jns_trans' => '5', 'a.kd_skpd' => $kd_skpd, 'bank' => 'BNK'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'1' as jns"), 'a.kd_skpd as kode')->unionAll($data14);

        // $data = DB::table(DB::raw("({$data15->toSql()}) AS sub"))
        //     ->select(DB::raw("SUM(CASE WHEN jns=1 THEN jumlah ELSE 0 END)-SUM(CASE WHEN jns=2 THEN jumlah ELSE 0 END) as sisa"))
        //     ->mergeBindings($data15)
        //     ->whereRaw("kode = '$kd_skpd'")
        //     ->first();
        // DB::enableQueryLog();
        $data = collect(DB::select("SELECT
      SUM(case when jns=1 then jumlah else 0 end) AS terima,
      SUM(case when jns=2 then jumlah else 0 end) AS keluar
      from (
            SELECT tgl_kas AS tgl,no_kas AS bku,keterangan as ket,nilai AS jumlah,'1' AS jns,kd_skpd AS kode FROM tr_setorsimpanan
            union all
            SELECT '2023-01-01' AS tgl, null AS bku,'Saldo Awal' AS ket, sld_awal_bank AS jumlah, '1' as jns, kd_skpd AS kode FROM ms_skpd WHERE kd_skpd = ?
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
            where e.kd_skpd=? and d.no_sp2d='2977/TU/2022' and d.pay='BANK' group by d.tgl_bukti,d.no_bukti,d.ket,d.kd_skpd
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
        where  kode=?", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]))->first();
        // print_r(DB::getQueryLog());
        // dd($data);
        return response()->json($data->terima - $data->keluar);
    }

    public function potonganLs(Request $request)
    {
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = Auth::user()->kd_skpd;

        // $data = DB::table('trspmpot as a')
        //     ->join('trhsp2d as b', function ($join) {
        //         $join->on('a.no_spm', '=', 'b.no_spm');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->where(['b.no_sp2d' => $no_sp2d, 'b.kd_skpd' => $kd_skpd])
        //     ->where(function ($query) {
        //         $query->whereRaw('b.jns_spp = ? AND b.jenis_beban != ?', ['4', '1'])
        //             ->orWhereRaw('b.jns_spp = ? AND b.jenis_beban !=?', ['6', '3']);
        //     })
        //     ->select(DB::raw("SUM(a.nilai) as total"))
        //     ->first();

        $data = collect(DB::select("SELECT SUM(a.nilai) as total  FROM trspmpot a INNER JOIN trhsp2d b on b.no_spm = a.no_spm AND b.kd_skpd=a.kd_skpd
		where ((b.jns_spp = '4' AND b.jenis_beban != '1') or (b.jns_spp = '6' AND b.jenis_beban != '3'))
		and b.no_sp2d = ? and b.kd_skpd = ?", [$no_sp2d, $kd_skpd]))->first();

        return response()->json($data->total);
    }

    public function loadDana(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = $request->kd_skpd;
        $sumber = $request->sumber;
        $kd_rekening = $request->kd_rekening;
        $no_sp2d = $request->no_sp2d;
        $beban = $request->beban;
        $spp = DB::table('trhsp2d')->select('no_spp')->where(['no_sp2d' => $no_sp2d])->first();
        $no_spp = $spp->no_spp;
        $data = cari_dana($sumber, $kd_sub_kegiatan, $kd_rekening, $kd_skpd, $no_sp2d, $no_spp, $beban);

        return response()->json($data);
    }

    public function statusAng(Request $request)
    {
        return response()->json(status_anggaran_new());
    }

    public function loadAngkas(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = $request->kd_skpd;
        $kd_rekening = $request->kd_rekening;
        $tgl_voucher = $request->tgl_voucher;
        $beban = $request->beban;
        $status_angkas = $request->status_angkas;

        $bulan = date('m', strtotime($tgl_voucher));
        $bulan1 = 0;
        $angkas = field_angkas($status_angkas);
        $jenis_ang = status_anggaran();

        // if ($beban == '4' || substr($kd_sub_kegiatan, 5, 10) == '01.1.02.01') {
        //     $bulan1 = $bulan + 1;
        //     $data = DB::table('trdskpd_ro as a')->join('trskpd as b', function ($join) {
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //         $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
        //     })->where(['a.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rekening, 'jns_ang' => $jenis_ang])->where('bulan', '<=', $bulan1)->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')->select('a.kd_sub_kegiatan', DB::raw("SUM(a.$angkas) as nilai"))->first();
        // } else {
        //     $data = DB::table('trdskpd_ro as a')->join('trskpd as b', function ($join) {
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //         $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
        //     })->where(['a.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rekening, 'jns_ang' => $jenis_ang])->where('bulan', '<=', $bulan)->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')->select('a.kd_sub_kegiatan', DB::raw("SUM(a.$angkas) as nilai"))->first();
        // }

        $bulan1 = DB::table('trhspd')
            ->selectRaw("MAX(bulan_akhir) as bulan")
            ->whereRaw("left(kd_skpd,17)=left(?,17)", [$kd_skpd])
            ->first()
            ->bulan;

        $data = DB::table('trdskpd_ro as a')
            ->join(
                'trskpd as b',
                function ($join) {
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
                }
            )->where([
                'a.kd_skpd' => $kd_skpd,
                'a.kd_sub_kegiatan' => $kd_sub_kegiatan,
                'kd_rek6' => $kd_rekening,
                'jns_ang' => $jenis_ang
            ])->where('bulan', '<=', $bulan1)
            ->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')
            ->select('a.kd_sub_kegiatan', DB::raw("SUM(a.$angkas) as nilai"))
            ->first();

        return response()->json($data);
    }

    public function loadAngkasLalu(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = $request->kd_skpd;
        $kd_rekening = $request->kd_rekening;
        $no_sp2d = $request->no_sp2d;
        $tgl_voucher = $request->tgl_voucher;
        $beban = $request->beban;
        $sumber = $request->sumber;

        if ($beban == '1') {
            $data1 = DB::table('trdtransout as c')->join('trhtransout as d', function ($join) {
                $join->on('c.no_bukti', '=', 'd.no_bukti');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->where(['c.kd_sub_kegiatan' => $kd_sub_kegiatan, 'd.kd_skpd' => $kd_skpd, 'c.kd_rek6' => $kd_rekening, 'd.jns_spp' => '1'])->select(DB::raw("SUM(ISNULL(c.nilai,0)) as nilai"));

            $data2 = DB::table('trdtransout_cmsbank as c')->join('trhtransout_cmsbank as d', function ($join) {
                $join->on('c.no_voucher', '=', 'd.no_voucher');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->where(['c.kd_sub_kegiatan' => $kd_sub_kegiatan, 'd.kd_skpd' => $kd_skpd, 'c.kd_rek6' => $kd_rekening, 'd.jns_spp' => '1'])->where(function ($query) {
                $query->where('d.status_validasi', '0')->orWhereNull('d.status_validasi');
            })->select(DB::raw("SUM(ISNULL(c.nilai,0)) as nilai"))->unionAll($data1);

            $data3 = DB::table('trdspp as c')->join('trhspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->where(['c.kd_sub_kegiatan' => $kd_sub_kegiatan, 'c.kd_skpd' => $kd_skpd, 'c.kd_rek6' => $kd_rekening])->whereIn('d.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
                $query->where('sp2d_batal', '')->orWhereNull('sp2d_batal');
            })->select(DB::raw("SUM(ISNULL(c.nilai,0)) as nilai"))->unionAll($data2);

            $data4 = DB::table('trdtagih as c')->join('trhtagih as d', function ($join) {
                $join->on('c.no_bukti', '=', 'd.no_bukti');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->where(['c.kd_sub_kegiatan' => $kd_sub_kegiatan, 'd.kd_skpd' => $kd_skpd, 'c.kd_rek6' => $kd_rekening])->whereRaw('d.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)', [$kd_skpd])->select(DB::raw("SUM(ISNULL(nilai,0)) as nilai"))->unionAll($data3);

            /*================== STS KAS SUDAH VALIDASI KASDA =====================*/
            $sql_sts = DB::table('trdkasin_pkd as c')->join('trhkasin_ppkd as d', function ($join) {
                $join->on('c.no_sts', '=', 'd.no_sts');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->where(['c.kd_sub_kegiatan' => $kd_sub_kegiatan, 'd.kd_skpd' => $kd_skpd, 'c.kd_rek6' => $kd_rekening])
                ->select(DB::raw("SUM(ISNULL(rupiah,0))*-1 as nilai"))->unionAll($data4);
            /*================== END STS KAS SUDAH VALIDASI KASDA =====================*/

            $data = DB::table(DB::raw("({$sql_sts->toSql()}) AS sub"))
                ->select(DB::raw("SUM(nilai) as total"))
                ->mergeBindings($sql_sts)
                ->first();
        } else {
            // $spp         = $this->tukd_model->get_nama($this->input->post('nosp2d'),'no_spp','trhsp2d','no_sp2d');
            // $sqlsc = "SELECT no_spp from trhsp2d where no_sp2d='$sp2d'";
            // $sqlsclient = $this->db->query($sqlsc);
            // foreach ($sqlsclient->result() as $rowsc) {
            //     $spp     = $rowsc->no_spp;
            // }

            $data = collect(DB::select("SELECT SUM(nilai) total, SUM(nilai_lalu) total_lalu FROM
            (
            SELECT SUM(isnull(c.nilai,0)) as nilai, SUM(isnull(c.nilai,0)) as nilai_lalu
            FROM trdtransout c
            LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti AND c.no_sp2d=d.no_sp2d
            AND c.kd_skpd = d.kd_skpd
            WHERE c.kd_sub_kegiatan = ?
            AND d.kd_skpd = ?
            AND c.kd_rek6 = ?
            AND d.jns_spp in ('3','4','5','6')
            AND d.no_sp2d=?
            UNION ALL
            -- transaksi UP/GU CMS BANK Belum Validasi
            SELECT SUM(isnull(c.nilai,0)) as nilai, SUM(isnull(c.nilai,0)) as nilai_lalu
            FROM trdtransout_cmsbank c
            LEFT JOIN trhtransout_cmsbank d ON c.no_voucher = d.no_voucher
            AND c.kd_skpd = d.kd_skpd
            WHERE c.kd_sub_kegiatan =?
            AND d.kd_skpd = ?
            AND c.kd_rek6=?
            AND d.jns_spp in ('1')
            AND (d.status_validasi='0' OR d.status_validasi is null)
            UNION ALL
            -- Penagihan yang belum jadi SPP
            SELECT SUM(isnull(nilai,0)) as nilai, SUM(isnull(nilai,0)) as nilai_lalu FROM trdtagih t
            INNER JOIN trhtagih u
            ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
            WHERE t.kd_sub_kegiatan =?
            AND t.kd_rek =?
            AND u.kd_skpd = ?
            AND u.no_bukti
            NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=?)
            )r", [$kd_sub_kegiatan, $kd_skpd, $kd_rekening, $no_sp2d, $kd_sub_kegiatan, $kd_skpd, $kd_rekening, $kd_sub_kegiatan, $kd_skpd, $kd_rekening, $kd_skpd]))->first();
        }

        return response()->json($data);
    }

    public function loadSpd(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = $request->kd_skpd;
        $kd_rekening = $request->kd_rekening;
        $bulan = $request->bulan;

        $data = load_spd($kd_sub_kegiatan, $kd_skpd, $kd_rekening, $bulan);

        return response()->json($data);
    }

    public function cekSimpan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('trhtransout_cmsbank')->where(['no_voucher' => $no_bukti, 'kd_skpd' => $kd_skpd])->count();
        return response()->json($data);
    }

    public function simpanCms(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhtransout_cmsbank')->where(['kd_skpd' => $kd_skpd, 'no_voucher' => $data['no_bukti']])->delete();

            DB::table('trhtransout_cmsbank')->insert([
                'no_voucher' => $data['no_bukti'],
                'tgl_voucher' => $data['tgl_voucher'],
                'no_bukti' => $data['no_bukti'],
                'tgl_bukti' => $data['tgl_voucher'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $data['kd_skpd'],
                'nm_skpd' => $data['nm_skpd'],
                'total' => $data['total_belanja'],
                'no_tagih' => '',
                'sts_tagih' => '0',
                'tgl_tagih' => '',
                'jns_spp' => $data['beban'],
                'pay' => $data['pembayaran'],
                'no_kas_pot' => $data['no_bukti'],
                'panjar' => '0',
                'no_sp2d' => $data['sp2d'],
                'rekening_awal' => $data['rekening'],
                'nm_rekening_tujuan' => '',
                'rekening_tujuan' => '',
                'bank_tujuan' => '',
                'status_validasi' => '0',
                'status_upload' => '0',
                'ket_tujuan' => $data['ketcms'],
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

    public function simpanDetailCms(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $data['rincian_rek_tujuan'] = json_decode($data['rincian_rek_tujuan'], true);

            $rincian_data = $data['rincian_rek_tujuan'];

            DB::table('trdtransout_cmsbank')->where(['no_voucher' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trdtransout_transfercms')->where(['no_voucher' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_rekening'])) {
                DB::table('trdtransout_cmsbank')->insert(array_map(function ($value) use ($data, $kd_skpd) {
                    return [
                        'no_voucher' => $data['no_bukti'],
                        'no_sp2d' => $value['no_sp2d'],
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $kd_skpd,
                        'sumber' => $value['sumber'],
                        'volume' => $value['volume'],
                        'satuan' => $value['satuan'],
                    ];
                }, $data['rincian_rekening']));
            }

            // if (isset($data['rincian_rek_tujuan'])) {
            //     DB::table('trdtransout_transfercms')->insert(array_map(function ($value) use ($data, $kd_skpd) {
            //         return [
            //             'no_voucher' => $data['no_bukti'],
            //             'tgl_voucher' => $data['tgl_voucher'],
            //             'rekening_awal' => $value['rekening_awal'],
            //             'nm_rekening_tujuan' => $value['nm_rekening_tujuan'],
            //             'rekening_tujuan' => $value['rekening_tujuan'],
            //             'bank_tujuan' => $value['bank_tujuan'],
            //             'kd_skpd' => $value['kd_skpd'],
            //             'nilai' => $value['nilai'],
            //         ];
            //     }, $data['rincian_rek_tujuan']));
            // }

            $no_bukti = $data['no_bukti'];
            $tgl_voucher = $data['tgl_voucher'];

            if (isset($rincian_data)) {
                foreach ($rincian_data as $data => $value) {
                    $data = [
                        'no_voucher' => $no_bukti,
                        'tgl_voucher' => $tgl_voucher,
                        'rekening_awal' => $rincian_data[$data]['rekening_awal'],
                        'nm_rekening_tujuan' => $rincian_data[$data]['nm_rekening_tujuan'],
                        'rekening_tujuan' => $rincian_data[$data]['rekening_tujuan'],
                        'bank_tujuan' => $rincian_data[$data]['bank_tujuan'],
                        'kd_skpd' => $rincian_data[$data]['kd_skpd'],
                        'nilai' => $rincian_data[$data]['nilai'],
                    ];
                    DB::table('trdtransout_transfercms')->insert($data);
                }
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

    // EDIT
    public function edit($no_voucher)
    {
        $no_voucher = Crypt::decryptString($no_voucher);
        $kd_skpd = Auth::user()->kd_skpd;

        $rek_tujuan = DB::table('trdtransout_transfercms as a')->join('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_voucher' => $no_voucher, 'a.kd_skpd' => $kd_skpd])->select('a.*')->get();
        $total_transfer = 0;
        foreach ($rek_tujuan as $tujuan) {
            $total_transfer += $tujuan->nilai;
        }
        $data = [
            'cms' => DB::table('trhtransout_cmsbank as a')->where(['a.panjar' => '0', 'kd_skpd' => $kd_skpd, 'no_voucher' => $no_voucher])->select('a.*', DB::raw("'' as nokas_pot"), DB::raw("'' as tgl_pot"), DB::raw("'' as kete"))->first(),
            'data_rek' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->first(),
            'data_rek_tujuan' => DB::table('ms_rekening_bank_online as a')->where(['kd_skpd' => $kd_skpd])->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nmbank"))->orderBy('a.nm_rekening')->get(),
            'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->get(),
            'data_rincian_rekening' => DB::table('trdtransout_cmsbank as a')->join('trhtransout_cmsbank as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['a.no_voucher' => $no_voucher, 'a.kd_skpd' => $kd_skpd])->select('a.*')->get(),
            'rincian_rek_tujuan' => $rek_tujuan,
            'total_transfer' => $total_transfer,
            'persen' => DB::table('config_app')->select('persen_kkpd', 'persen_tunai')->first(),
        ];

        return view('skpd.transaksi_cms.edit')->with($data);
    }

    // Hapus
    public function hapusCms(Request $request)
    {
        $no_voucher = $request->no_voucher;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trdtransout_cmsbank')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])->delete();
            DB::table('trhtransout_cmsbank')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])->delete();
            DB::table('trdtransout_transfercms')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])->delete();

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

    // Cetak List
    public function cetakList(Request $request)
    {
        $tgl_voucher = $request->tgl_voucher;
        $kd_skpd = Auth::user()->kd_skpd;
        $tahun_anggaran = tahun_anggaran();

        $data1 = DB::table('trhtransout_cmsbank as a')->where(DB::raw("YEAR(a.tgl_voucher)"), $tahun_anggaran)->where(['a.tgl_voucher' => $tgl_voucher, 'a.kd_skpd' => $kd_skpd])->select(DB::raw("'1' as urut"), 'a.kd_skpd', 'a.tgl_voucher', 'a.no_voucher', 'a.no_sp2d as kegiatan', DB::raw("'' as rekening"), 'a.ket', DB::raw("'0' as terima"), DB::raw("'0' as keluar"), 'a.jns_spp', 'a.status_upload');

        $data2 = DB::table('trhtransout_cmsbank as a')->leftJoin('trdtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(DB::raw("YEAR(a.tgl_voucher)"), $tahun_anggaran)->where(['a.tgl_voucher' => $tgl_voucher, 'a.kd_skpd' => $kd_skpd])->select(DB::raw("'2' as urut"), 'a.kd_skpd', 'a.tgl_voucher', 'a.no_voucher', 'b.kd_sub_kegiatan as kegiatan', 'b.kd_rek6 as rekening', DB::raw("b.nm_sub_kegiatan + ', ' + b.nm_rek6 as ket"), DB::raw("'0' as terima"), 'b.nilai as keluar', 'a.jns_spp', DB::raw("'' as status_upload"))->union($data1);

        $data3 = DB::table('trdtransout_transfercms as a')->where(DB::raw("YEAR(a.tgl_voucher)"), $tahun_anggaran)->where(['a.tgl_voucher' => $tgl_voucher, 'a.kd_skpd' => $kd_skpd])->select(DB::raw("'3' as urut"), 'a.kd_skpd', 'a.tgl_voucher', 'a.no_voucher', DB::raw("'Rek. Tujuan :' as kegiatan"), DB::raw("'' as rekening"), DB::raw("RTRIM(a.rekening_tujuan) + ' , AN : ' + RTRIM(a.nm_rekening_tujuan) as ket"), DB::raw("'0' as terima"), 'a.nilai as keluar', DB::raw("'' as jns_spp"), DB::raw("'' as status_upload"))->union($data2);

        $data4 = DB::table('trhtransout_cmsbank as a')->join('trhtrmpot_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->join('trdtrmpot_cmsbank as c', function ($join) {
            $join->on('b.no_bukti', '=', 'c.no_bukti');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        })->where(DB::raw("YEAR(a.tgl_voucher)"), $tahun_anggaran)->where(['a.tgl_voucher' => $tgl_voucher, 'a.kd_skpd' => $kd_skpd])->select(DB::raw("'4' as urut"), 'a.kd_skpd', 'a.tgl_voucher', 'a.no_voucher', 'b.kd_sub_kegiatan as kegiatan', 'c.kd_rek6 as rekening', DB::raw("'Terima ' + c.nm_rek6 as ket"), 'c.nilai as terima', DB::raw("'0' as keluar"), DB::raw("'' as jns_spp"), DB::raw("'' as status_upload"))->union($data3);

        $bank1 = DB::table('tr_setorsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode');

        $bank2 = DB::table('TRHINLAIN')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where('pay', 'BANK')->unionAll($bank1);

        $bank3 = DB::table('tr_jpanjar as c')->join('tr_panjar as d', function ($join) {
            $join->on('c.no_panjar_lalu', '=', 'd.no_panjar');
            $join->on('c.kd_skpd', '=', 'd.kd_skpd');
        })->select('c.tgl_kas as tgl', 'c.no_kas as bku', 'c.keterangan as ket', 'c.nilai as jumlah', DB::raw("'1' as jns"), 'c.kd_skpd as kode')->where(['c.jns' => '1', 'c.kd_skpd' => $kd_skpd, 'd.pay' => 'BANK'])->unionAll($bank2);

        $bank4 = DB::table('trhtrmpot')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where(['pay' => 'BANK', 'kd_skpd' => $kd_skpd])->unionAll($bank3);

        $bank5 = DB::table('tr_ambilsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->unionAll($bank4);

        $bank6 = DB::table('trhoutlain')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where('pay', 'BANK')->unionAll($bank5);

        $bank7 = DB::table('tr_panjar')->select('tgl_panjar as tgl', 'no_panjar as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where(['jns' => '1', 'kd_skpd' => $kd_skpd, 'pay' => 'BANK'])->unionAll($bank6);

        $leftjoin1 = DB::table('trspmpot')->select('no_spm', DB::raw("SUM(nilai) as pot"))->groupBy('no_spm');

        $bank8 = DB::table('trhtransout as a')->join('trhsp2d as b', 'a.no_sp2d', '=', 'b.no_sp2d')->leftJoinSub($leftjoin1, 'c', function ($join) {
            $join->on('b.no_spm', '=', 'c.no_spm');
        })->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket as ket', DB::raw("total - ISNULL(pot,0) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->where(['pay' => 'BANK'])->unionAll($bank7);

        $bank9 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
            $join->on('a.no_sts', '=', 'b.no_sts');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['pot_khusus' => '0', 'bank' => 'BANK', 'a.kd_skpd' => $kd_skpd])->whereNotIn('jns_trans', ['4', '2'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($bank8);

        $bank10 = DB::table('trhstrpot')->where(['kd_skpd' => $kd_skpd, 'pay' => 'BANK'])->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->unionAll($bank9);

        $data = [
            'daerah' => DB::table('sclient')->select('daerah', 'kab_kota')->where(['kd_skpd' => $kd_skpd])->first(),
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tgl_voucher' => $tgl_voucher,
            'data_cms' => DB::table(DB::raw("({$data4->toSql()}) AS sub"))
                ->mergeBindings($data4)
                ->orderBy('kd_skpd')
                ->orderBy('tgl_voucher')
                ->orderBy(DB::raw("CAST(no_voucher as INT)"))
                ->orderBy('urut')
                ->get(),
            'bank' => DB::table(DB::raw("({$bank10->toSql()}) AS sub"))
                ->select(DB::raw("SUM(CASE WHEN jns=1 THEN jumlah ELSE 0 END) as terima"), DB::raw("SUM(CASE WHEN jns=2 THEN jumlah ELSE 0 END) as keluar"))
                ->mergeBindings($bank10)
                ->whereRaw("tgl<='$tgl_voucher' AND kode='$kd_skpd'")
                ->first()
        ];

        return view('skpd.transaksi_cms.cetak')->with($data);
    }
}
