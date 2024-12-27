<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransaksiKKPDController extends Controller
{
    public function index()
    {
        return view('skpd.transaksi_kkpd.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtransout_kkpd as a')->where(['a.panjar' => '0', 'kd_skpd' => $kd_skpd])->select('a.*', DB::raw("'' as nokas_pot"), DB::raw("'' as tgl_pot"), DB::raw("'' as kete"))->orderBy('tgl_voucher')->orderBy(DB::raw("CAST(a.no_bukti as int)"))->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.transaksi_kkpd.edit", Crypt::encryptString($row->no_voucher)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            if ($row->status_validasi != '1') {
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_voucher . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            } else {
                $btn .= '';
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
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'persen' => DB::table('config_app')->select('persen_kkpd', 'persen_tunai')->first(),
        ];

        return view('skpd.transaksi_kkpd.create')->with($data);
    }

    public function no_urut(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $urut1 = DB::table('trhtransout_kkpd')->where(['kd_skpd' => $kd_skpd])->select('no_voucher as nomor', DB::raw("'Daftar Transaksi Non Tunai KKPD' as ket"), 'kd_skpd');
        $urut2 = DB::table('trhtrmpot_kkpd')->select('no_bukti as nomor', DB::raw("'Potongan Pajak Transaksi Non Tunai' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->unionAll($urut1);
        $urut = DB::table(DB::raw("({$urut2->toSql()}) AS sub"))
            ->select(DB::raw("CASE WHEN MAX(nomor+1) IS NULL THEN 1 ELSE MAX(nomor+1) END AS nomor"))
            ->mergeBindings($urut2)
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

        $data = DB::table('trskpd as a')->join('ms_sub_kegiatan as b', 'a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan')->where(['a.kd_skpd' => $kd_skpd, 'a.status_sub_kegiatan' => '1', 'b.jns_sub_kegiatan' => '5', 'a.jns_ang' => $anggaran])->select('a.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'a.kd_program', DB::raw("(SELECT nm_program FROM ms_program WHERE kd_program=a.kd_program) as nm_program"), 'a.total')->get();

        return response()->json($data);
    }

    public function cariSp2d(Request $request)
    {
        $beban = $request->beban;
        $kd_skpd = $request->kd_skpd;
        $kode = substr($kd_skpd, 0, 17);
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_bukti = $request->no_bukti;

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
            })->where(['c.kd_skpd' => $kd_skpd, 'c.status' => '1'])->whereRaw($where, [$beban, $kd_sub_kegiatan])->whereRaw('c.no_sp2d NOT IN (SELECT no_sp2d FROM trhlpj WHERE kd_skpd=?)', [$kd_skpd])->orderByDesc('c.tgl_sp2d')->orderBy('c.no_sp2d')->select('c.no_sp2d', 'c.tgl_sp2d', 'c.nilai', DB::raw("'0' as sisa"))->distinct()->get();
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
            })->where(['c.status' => '1'])->where(DB::raw("LEFT(c.kd_skpd,17)"), $kode)->whereRaw($where, [$beban, $kd_sub_kegiatan])->orderByDesc('c.tgl_sp2d')->orderBy('c.no_sp2d')->select('c.no_sp2d', 'c.tgl_sp2d', 'c.nilai', DB::raw("'0' as sisa"))->distinct()->get();
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

        if ($beban == '1') {
            // $data1 = DB::table('trdrka')->where(['kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rek6, 'kd_skpd' => $kd_skpd, 'jns_ang' => $jenis_ang])->select('sumber1 as sumber_dana', DB::raw("ISNULL(nsumber1,0) as nilai"));
            // $data2 = DB::table('trdrka')->where(['kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rek6, 'kd_skpd' => $kd_skpd, 'jns_ang' => $jenis_ang])->where('nsumber2', '<>', '0')->select('sumber2 as sumber_dana', DB::raw("ISNULL(nsumber2,0) as nilai"))->unionAll($data1);
            // $data3 = DB::table('trdrka')->where(['kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rek6, 'kd_skpd' => $kd_skpd, 'jns_ang' => $jenis_ang])->where('nsumber3', '<>', '0')->select('sumber3 as sumber_dana', DB::raw("ISNULL(nsumber3,0) as nilai"))->unionAll($data2);
            // $data4 = DB::table('trdrka')->where(['kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rek6, 'kd_skpd' => $kd_skpd, 'jns_ang' => $jenis_ang])->where('nsumber4', '<>', '0')->select('sumber4 as sumber_dana', DB::raw("ISNULL(nsumber4,0) as nilai"))->unionAll($data3);
            // $data = DB::table(DB::raw("({$data4->toSql()}) AS sub"))
            //     ->mergeBindings($data4)
            //     ->whereRaw("sumber_dana <> ''")
            //     ->get();
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
            $data = DB::table('trhspp as a')->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->join('trhsp2d as c', function ($join) {
                $join->on('a.no_spp', '=', 'c.no_spp');
                $join->on('a.kd_skpd', '=', 'c.kd_skpd');
            })->where(['c.no_sp2d' => $no_sp2d, 'a.kd_skpd' => $kd_skpd, 'b.kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rek6])->groupBy('b.sumber')->select('b.sumber as sumber_dana', DB::raw("SUM(b.nilai) as nilai"), DB::raw("SUM(b.nilai) as nilai_sempurna"), DB::raw("SUM(b.nilai) as nilai_ubah"))->get();
        }
        return response()->json($data);
    }

    public function sisaBank(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data1 = DB::table('tr_setorsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode');

        $data2 = DB::table('TRHINLAIN')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where(['pay' => 'BANK'])->unionAll($data1);

        $data3 = DB::table('tr_jpanjar as a')->join('tr_panjar as b', function ($join) {
            $join->on('a.no_panjar', '=', 'b.no_panjar');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.jns' => '2', 'a.kd_skpd' => $kd_skpd, 'b.pay' => 'BANK'])->select('a.tgl_kas as tgl', 'a.no_kas as bku', 'a.keterangan as ket', 'a.nilai as jumlah', DB::raw("'1' as jns"), 'a.kd_skpd as kode')->unionAll($data2);

        $data4 = DB::table('trhtrmpot as a')->join('trdtrmpot as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd, 'a.pay' => 'BANK'])->whereNotIn('jns_spp', ['1', '2', '3'])->groupBy('a.tgl_bukti', 'a.no_bukti', 'a.ket', 'a.kd_skpd')->select('a.tgl_bukti as tgl', 'a.no_bukti as bku', 'a.ket as ket', DB::raw("SUM(b.nilai) as jumlah"), DB::raw("'1' as jns"), 'a.kd_skpd as kode')->unionAll($data3);

        $data5 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
            $join->on('a.no_sts', '=', 'b.no_sts');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd, 'bank' => 'BNK', 'jns_trans' => '5'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data4);

        $joinsub = DB::table('trspmpot')->select('no_spm', DB::raw("SUM(nilai) as pot"))->groupBy('no_spm');
        $joinsub1 = DB::table('trhtrmpot as d')->join('trdtrmpot as e', function ($join) {
            $join->on('d.no_bukti', '=', 'e.no_bukti');
            $join->on('d.kd_skpd', '=', 'e.kd_skpd');
        })->where(['e.kd_skpd' => $kd_skpd, 'd.pay' => 'BANK'])->where('d.no_kas', '<>', '')->select('d.no_kas', DB::raw("SUM(e.nilai) as pot2"), 'd.kd_skpd')->groupBy('d.no_kas', 'd.kd_skpd');

        $data6 = DB::table('trhtransout as a')->join('trhsp2d as b', function ($join) {
            $join->on('a.no_sp2d', '=', 'b.no_sp2d');
        })->leftJoinSub($joinsub, 'c', function ($join) {
            $join->on('b.no_spm', '=', 'c.no_spm');
        })->leftJoinSub($joinsub1, 'f', function ($join) {
            $join->on('f.no_kas', '=', 'a.no_bukti');
            $join->on('f.kd_skpd', '=', 'a.kd_skpd');
        })->where(['pay' => 'BANK'])->where(function ($query) {
            $query->where('panjar', '<>', '1')->orWhereNull('panjar');
        })->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket', DB::raw("total-ISNULL(pot,0)-ISNULL(f.pot2,0) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data5);

        $data7 = DB::table('trhstrpot as a')->join('trdstrpot as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd, 'a.pay' => 'BANK'])->groupBy('a.tgl_bukti', 'a.no_bukti', 'a.ket', 'a.kd_skpd')->select('a.tgl_bukti as tgl', 'a.no_bukti as bku', 'a.ket as ket', DB::raw("SUM(b.nilai) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data6);

        $data8 = DB::table('tr_ambilsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->unionAll($data7);

        $data9 = DB::table('trhoutlain')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where(['pay' => 'BANK'])->unionAll($data8);

        $data10 = DB::table('tr_setorpelimpahan_bank')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd_sumber as kode')->unionAll($data9);

        $data11 = DB::table('tr_ambilsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where('status_drop', '!=', '1')->unionAll($data10);

        $data12 = DB::table('tr_panjar as a')->leftJoinSub($joinsub1, 'b', function ($join) {
            $join->on('a.no_panjar', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.pay' => 'BANK', 'a.kd_skpd' => $kd_skpd])->select('a.tgl_kas as tgl', 'a.no_panjar as bku', 'a.keterangan as ket', DB::raw("a.nilai-ISNULL(b.pot2,0) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data11);

        $data13 = DB::table('trhtrmpot as d')->join('trdtrmpot as e', function ($join) {
            $join->on('d.no_bukti', '=', 'e.no_bukti');
            $join->on('d.kd_skpd', '=', 'e.kd_skpd');
        })->where(['d.no_sp2d' => '2977/TU/2022', 'e.kd_skpd' => $kd_skpd, 'd.pay' => 'BANK'])->groupBy('d.tgl_bukti', 'd.no_bukti', 'd.ket', 'd.kd_skpd')->select('d.tgl_bukti as tgl', 'd.no_bukti as bku', 'd.ket as ket', DB::raw("SUM(e.nilai) as jumlah"), DB::raw("'1' as jns"), 'd.kd_skpd as kode')->unionAll($data12);

        $data14 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
            $join->on('a.no_sts', '=', 'b.no_sts');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['pot_khusus' => '0', 'a.kd_skpd' => $kd_skpd, 'bank' => 'BNK'])->whereNotIn('jns_trans', ['2', '4', '5'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data13);

        $data15 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
            $join->on('a.no_sts', '=', 'b.no_sts');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['jns_trans' => '5', 'a.kd_skpd' => $kd_skpd, 'bank' => 'BNK'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'1' as jns"), 'a.kd_skpd as kode')->unionAll($data14);

        $data = DB::table(DB::raw("({$data15->toSql()}) AS sub"))
            ->select(DB::raw("SUM(CASE WHEN jns=1 THEN jumlah ELSE 0 END)-SUM(CASE WHEN jns=2 THEN jumlah ELSE 0 END) as sisa"))
            ->mergeBindings($data15)
            ->whereRaw("kode = '$kd_skpd'")
            ->first();

        return response()->json($data->sisa);
    }

    public function potonganLs(Request $request)
    {
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('trspmpot as a')->join('trhsp2d as b', function ($join) {
            $join->on('a.no_spm', '=', 'b.no_spm');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['b.no_sp2d' => $no_sp2d, 'b.kd_skpd' => $kd_skpd])->where(function ($query) {
            $query->whereRaw('b.jns_spp = ? AND b.jenis_beban != ?', ['4', '1'])->orWhereRaw('b.jns_spp = ? AND b.jenis_beban !=?', ['6', '3']);
        })->select(DB::raw("SUM(a.nilai) as total"))->first();

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

        if ($beban == '4' || substr($kd_sub_kegiatan, 5, 10) == '01.1.02.01') {
            $bulan1 = $bulan + 1;
            $data = DB::table('trdskpd_ro as a')->join('trskpd as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
            })->where(['a.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rekening, 'jns_ang' => $jenis_ang])->where('bulan', '<=', $bulan1)->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')->select('a.kd_sub_kegiatan', DB::raw("SUM(a.$angkas) as nilai"))->first();
        } else {
            $data = DB::table('trdskpd_ro as a')->join('trskpd as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
            })->where(['a.kd_skpd' => $kd_skpd, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_rek6' => $kd_rekening, 'jns_ang' => $jenis_ang])->where('bulan', '<=', $bulan)->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')->select('a.kd_sub_kegiatan', DB::raw("SUM(a.$angkas) as nilai"))->first();
        }

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

            $data = DB::table(DB::raw("({$data4->toSql()}) AS sub"))
                ->select(DB::raw("SUM(nilai) as total"))
                ->mergeBindings($data4)
                ->first();
        } else {
            $spp = DB::table('trhsp2d')->select('no_spp')->where(['no_sp2d' => $no_sp2d])->first();
            $no_spp = $spp->no_spp;

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
            })->where(['c.kd_sub_kegiatan' => $kd_sub_kegiatan, 'c.kd_skpd' => $kd_skpd, 'c.kd_rek6' => $kd_rekening])->whereIn('d.jns_spp', ['3', '4', '5', '6'])->where('d.no_spp', '<>', $no_spp)->where(function ($query) {
                $query->where('sp2d_batal', '')->orWhereNull('sp2d_batal');
            })->select(DB::raw("SUM(ISNULL(c.nilai,0)) as nilai"))->unionAll($data2);

            $data4 = DB::table('trdtagih as c')->join('trhtagih as d', function ($join) {
                $join->on('c.no_bukti', '=', 'd.no_bukti');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->where(['c.kd_sub_kegiatan' => $kd_sub_kegiatan, 'd.kd_skpd' => $kd_skpd, 'c.kd_rek6' => $kd_rekening])->whereRaw('d.no_bukti NOT IN (SELECT no_tagih FROM trhspp WHERE kd_skpd=?)', [$kd_skpd])->select(DB::raw("SUM(ISNULL(nilai,0)) as nilai"))->unionAll($data3);

            $data = DB::table(DB::raw("({$data4->toSql()}) AS sub"))
                ->select(DB::raw("SUM(nilai) as total"))
                ->mergeBindings($data4)
                ->first();
        }

        return response()->json($data);
    }

    public function loadSpd(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = $request->kd_skpd;
        $kd_rekening = $request->kd_rekening;

        $data = load_spd($kd_sub_kegiatan, $kd_skpd, $kd_rekening);

        return response()->json($data);
    }

    public function cekSimpan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('trhtransout_kkpd')->where(['no_voucher' => $no_bukti, 'kd_skpd' => $kd_skpd])->count();
        return response()->json($data);
    }

    public function simpanCms(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhtransout_kkpd')->where(['kd_skpd' => $kd_skpd, 'no_voucher' => $data['no_bukti']])->delete();

            DB::table('trhtransout_kkpd')->insert([
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
            DB::table('trdtransout_kkpd')->where(['no_voucher' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();
            if (isset($data['rincian_rekening'])) {
                DB::table('trdtransout_kkpd')->insert(array_map(function ($value) use ($data, $kd_skpd) {
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

    public function edit($no_voucher)
    {
        $no_voucher = Crypt::decryptString($no_voucher);
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'cms' => DB::table('trhtransout_kkpd as a')->where(['a.panjar' => '0', 'kd_skpd' => $kd_skpd, 'no_voucher' => $no_voucher])->select('a.*', DB::raw("'' as nokas_pot"), DB::raw("'' as tgl_pot"), DB::raw("'' as kete"))->first(),
            'data_rek' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->first(),
            'data_rek_tujuan' => DB::table('ms_rekening_bank_online as a')->where(['kd_skpd' => $kd_skpd])->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nmbank"))->orderBy('a.nm_rekening')->get(),
            'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->get(),
            'data_rincian_rekening' => DB::table('trdtransout_kkpd as a')->join('trhtransout_kkpd as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['a.no_voucher' => $no_voucher, 'a.kd_skpd' => $kd_skpd])->select('a.*')->get(),
            'persen' => DB::table('config_app')->select('persen_kkpd', 'persen_tunai')->first(),
        ];

        return view('skpd.transaksi_kkpd.edit')->with($data);
    }

    public function hapusCms(Request $request)
    {
        $no_voucher = $request->no_voucher;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trdtransout_kkpd')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])->delete();
            DB::table('trhtransout_kkpd')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])->delete();

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

    // VALIDASI KKPD
    public function indexValidasi()
    {
        $data = [
            'sisa_bank' => sisa_bank()
        ];
        return view('skpd.validasi_kkpd.index')->with($data);
    }

    public function loadDataValidasi(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtransout_kkpd as a')->leftJoin('trdtransout_kkpd as b', function ($join) {
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            $join->on('a.no_voucher', '=', 'b.no_voucher');
        })->select('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_upload', 'a.tgl_upload', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'a.status_trmpot')->selectRaw("(CASE WHEN a.jns_spp IN ( '4', '6' ) THEN(SELECT SUM( x.nilai ) tot_pot FROM trspmpot x INNER JOIN trhsp2d b ON x.kd_skpd= b.kd_skpd AND x.no_spm= b.no_spm INNER JOIN trhtransout_kkpd c ON b.kd_skpd= c.kd_skpd AND b.no_sp2d= c.no_sp2d WHERE c.kd_skpd=? AND c.no_sp2d= a.no_sp2d AND c.jns_spp IN ( '4', '6' )) ELSE 0 END) as tot_pot", [$kd_skpd])->where(['a.kd_skpd' => $kd_skpd, 'status_validasi' => '0'])->groupBy('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_upload', 'a.tgl_upload', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'a.status_trmpot', 'a.jns_spp')->orderBy('a.kd_skpd')->orderBy(DB::raw("CAST(a.no_voucher as int)"))->get();
        return Datatables::of($data)->addIndexColumn()->make(true);
    }

    public function createValidasi()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'daftar_transaksi' => DB::table('trhtransout_kkpd as a')->leftJoin('trdtransout_kkpd as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_voucher', '=', 'b.no_voucher');
            })->select('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'a.status_trmpot')->selectRaw("(CASE WHEN a.jns_spp IN ( '4', '6' ) THEN(SELECT SUM( x.nilai ) tot_pot FROM trspmpot x INNER JOIN trhsp2d b ON x.kd_skpd= b.kd_skpd AND x.no_spm= b.no_spm INNER JOIN trhtransout_kkpd c ON b.kd_skpd= c.kd_skpd AND b.no_sp2d= c.no_sp2d WHERE c.kd_skpd=? AND c.no_sp2d= a.no_sp2d AND c.jns_spp IN ( '4', '6' )) ELSE 0 END) as tot_pot", [$kd_skpd])->where(['a.kd_skpd' => $kd_skpd, 'status_validasi' => '0'])->groupBy('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'a.status_trmpot', 'a.jns_spp')->orderBy('a.kd_skpd')->orderBy(DB::raw("CAST(a.no_voucher as int)"))->get(),
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.validasi_kkpd.create')->with($data);
    }

    public function prosesValidasi(Request $request)
    {
        $rincian_data = $request->rincian_data;
        $tanggal_validasi = $request->tanggal_validasi;
        $kd_skpd = Auth::user()->kd_skpd;

        $nomor1 = DB::table('trvalidasi_kkpd')->select('no_validasi as nomor', DB::raw("'Urut Validasi KKPD' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd]);
        $nomor = DB::table(DB::raw("({$nomor1->toSql()}) AS sub"))
            ->select(DB::raw("case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor"))
            ->mergeBindings($nomor1)
            ->first();

        DB::beginTransaction();
        try {
            $nomor1 = DB::table('trvalidasi_kkpd')->select('no_validasi as nomor', DB::raw("'Urut Validasi KKPD' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd]);
            $nomor = DB::table(DB::raw("({$nomor1->toSql()}) AS sub"))
                ->select(DB::raw("case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor"))
                ->mergeBindings($nomor1)
                ->first();

            $no_validasi = $nomor->nomor;
            $no_bku = no_urut($kd_skpd);
            $bku = $no_bku - 1;

            foreach ($rincian_data as $data => $value) {
                $data = [
                    'no_voucher' => $rincian_data[$data]['no_voucher'],
                    'tgl_voucher' => $rincian_data[$data]['tgl_voucher'],
                    'rekening_awal' => $rincian_data[$data]['rekening_awal'],
                    'nm_rekening_tujuan' => $rincian_data[$data]['nm_rekening_tujuan'],
                    'rekening_tujuan' => $rincian_data[$data]['rekening_tujuan'],
                    'bank_tujuan' => $rincian_data[$data]['bank_tujuan'],
                    'ket_tujuan' => $rincian_data[$data]['ket_tujuan'],
                    'nilai' => $rincian_data[$data]['total'],
                    'kd_skpd' => $rincian_data[$data]['kd_skpd'],
                    'kd_bp' => $rincian_data[$data]['kd_skpd'],
                    'tgl_validasi' => $tanggal_validasi,
                    'status_validasi' => '1',
                    'no_validasi' => $no_validasi,
                    'no_bukti' => ++$bku,
                ];
                DB::table('trvalidasi_kkpd')->insert($data);
            }

            $data1 = DB::table('trvalidasi_kkpd as a')->where(['a.kd_skpd' => $kd_skpd, 'a.no_validasi' => $no_validasi])->select('a.no_voucher', 'a.no_bukti', 'a.kd_skpd', 'a.kd_bp', 'a.tgl_validasi', 'a.status_validasi');

            DB::table('trhtransout_kkpd as c')->joinSub($data1, 'd', function ($join) {
                $join->on('c.no_voucher', '=', 'd.no_voucher');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->whereRaw('left(c.kd_skpd,17) = left(?,17)', $kd_skpd)->update([
                'c.status_validasi' => DB::raw("d.status_validasi"),
                'c.tgl_validasi' => DB::raw("d.tgl_validasi"),
                'c.no_bukti' => DB::raw("d.no_bukti"),
                'c.tgl_bukti' => DB::raw("d.tgl_validasi"),
            ]);

            $data_transout = DB::table('trhtransout_kkpd as a')->leftJoin('trvalidasi_kkpd as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['b.no_validasi' => $no_validasi, 'b.kd_skpd' => $kd_skpd])->select('b.no_bukti as no_kas', 'b.tgl_validasi as tgl_kas', 'a.no_bukti', 'a.tgl_bukti', 'a.no_sp2d', 'a.ket', 'b.kd_skpd as username', 'a.tgl_update', 'b.kd_skpd', 'a.nm_skpd', 'a.total', 'a.no_tagih', 'a.sts_tagih', 'a.tgl_tagih', 'a.jns_spp', 'a.pay', 'a.no_kas_pot', 'a.panjar', 'a.no_panjar');

            DB::table('trhtransout')->insertUsing(['no_kas', 'tgl_kas', 'no_bukti', 'tgl_bukti', 'no_sp2d', 'ket', 'username', 'tgl_update', 'kd_skpd', 'nm_skpd', 'total', 'no_tagih', 'sts_tagih', 'tgl_tagih', 'jns_spp', 'pay', 'no_kas_pot', 'panjar', 'no_panjar'], $data_transout);

            $data_transout1 = DB::table('trhtransout_kkpd as a')->join('trdtransout_kkpd as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->leftJoin('trvalidasi_kkpd as c', function ($join) {
                $join->on('a.no_voucher', '=', 'c.no_voucher');
                $join->on('a.kd_skpd', '=', 'c.kd_skpd');
            })->where(['c.no_validasi' => $no_validasi, 'c.kd_skpd' => $kd_skpd])->select('c.no_bukti', 'a.no_sp2d', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'b.kd_rek6', 'b.nm_rek6', 'b.nilai', 'b.kd_skpd', DB::raw("'' as kunci"), 'b.sumber', 'b.volume', 'b.satuan');

            DB::table('trdtransout')->insertUsing(['no_bukti', 'no_sp2d', 'kd_sub_kegiatan', 'nm_sub_kegiatan', 'kd_rek6', 'nm_rek6', 'nilai', 'kd_skpd', 'kunci', 'sumber', 'volume', 'satuan'], $data_transout1);

            // POTONGAN
            $data_transout2 = DB::table('trhtrmpot_kkpd as a')->join('trhtransout_kkpd as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->leftJoin('trvalidasi_kkpd as c', function ($join) {
                $join->on('b.no_voucher', '=', 'c.no_voucher');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->where(['c.no_validasi' => $no_validasi, 'b.status_trmpot' => '1', 'c.kd_skpd' => $kd_skpd])->select(DB::raw("CAST(c.no_bukti as int)+1 as no_bukti"), 'c.tgl_validasi as tgl_bukti', 'a.ket', 'a.username', 'a.tgl_update', 'a.kd_skpd', 'a.nm_skpd', 'a.no_sp2d', 'a.nilai', 'a.npwp', 'a.jns_spp', 'a.status', 'a.kd_sub_kegiatan', 'a.nm_sub_kegiatan', 'a.kd_rek6', 'a.nm_rek6', 'a.nmrekan', 'a.pimpinan', 'a.alamat', 'a.ebilling', 'a.rekening_tujuan', 'a.nm_rekening_tujuan', 'c.no_bukti', DB::raw("'BANK' as pay"));

            DB::table('trhtrmpot')->insertUsing(['no_bukti', 'tgl_bukti', 'ket', 'username', 'tgl_update', 'kd_skpd', 'nm_skpd', 'no_sp2d', 'nilai', 'npwp', 'jns_spp', 'status', 'kd_sub_kegiatan', 'nm_sub_kegiatan', 'kd_rek6', 'nm_rek6', 'nmrekan', 'pimpinan', 'alamat', 'ebilling', 'rekening_tujuan', 'nm_rekening_tujuan', 'no_kas', 'pay'], $data_transout2);

            $data_transout3 = DB::table('trhtrmpot_kkpd as a')->join('trdtrmpot_kkpd as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->leftJoin('trhtransout_kkpd as c', function ($join) {
                $join->on('a.no_voucher', '=', 'c.no_voucher');
                $join->on('a.kd_skpd', '=', 'c.kd_skpd');
            })->leftJoin('trvalidasi_kkpd as d', function ($join) {
                $join->on('d.no_voucher', '=', 'c.no_voucher');
                $join->on('d.kd_skpd', '=', 'c.kd_skpd');
            })->where(['d.no_validasi' => $no_validasi, 'c.status_trmpot' => '1', 'd.kd_skpd' => $kd_skpd])->select(DB::raw("CAST(d.no_bukti as int)+1 as no_bukti"), 'b.kd_rek6', 'b.nm_rek6', 'b.nilai', 'b.kd_skpd', 'b.kd_rek_trans', 'b.ebilling', 'b.rekanan', 'b.npwp');

            DB::table('trdtrmpot')->insertUsing(['no_bukti', 'kd_rek6', 'nm_rek6', 'nilai', 'kd_skpd', 'kd_rek_trans', 'ebilling', 'rekanan', 'npwp'], $data_transout3);

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

    public function draftValidasi()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtransout_kkpd as a')->leftJoin('trdtransout_kkpd as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->leftJoin('trvalidasi_kkpd as d', function ($join) {
            $join->on('d.no_voucher', '=', 'a.no_voucher');
            $join->on('d.kd_bp', '=', 'a.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd, 'a.status_validasi' => '1'])->groupBy('a.kd_skpd', 'a.no_voucher', 'a.tgl_voucher', 'a.ket', 'a.total', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'a.status_trmpot', 'd.no_bukti')->orderBy(DB::raw("CAST(d.no_bukti as int)"))->orderBy('a.tgl_validasi')->orderBy('a.kd_skpd')->select('a.kd_skpd', 'a.no_voucher', 'a.tgl_voucher', 'a.ket', 'a.total', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'a.status_trmpot', 'd.no_bukti')->get();
        return Datatables::of($data)->addIndexColumn()->make(true);
    }

    public function batalValidasi(Request $request)
    {
        $no_voucher = $request->no_voucher;
        $no_bukti = $request->no_bukti;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            $no_bukti1 = strval($no_bukti) + 1;
            $spjbulan = cek_status_spj($kd_skpd);
            $cek_spj = DB::table('trlpj')->select(DB::raw("COUNT(*) as tot_lpj"))->selectRaw("(SELECT DISTINCT CASE WHEN MONTH(a.tgl_bukti)<=?  THEN 1 ELSE 0 END FROM trhtransout a WHERE  a.panjar = '0' AND a.kd_skpd=? AND a.no_bukti=?) as tot_spj", [$spjbulan, $kd_skpd, $no_bukti])->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->first();

            if ($cek_spj->tot_lpj != 0 || $cek_spj->tot_spj != 0) {
                return response()->json([
                    'message' => '3'
                ]);
            }

            DB::table('trhtransout')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trdtransout')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trvalidasi_kkpd')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd, 'no_voucher' => $no_voucher])->delete();

            DB::table('trhtransout_kkpd')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])->update([
                'status_validasi' => '0',
                'tgl_validasi' => '',
            ]);

            $data_potongan = DB::table('trhtransout_kkpd')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd, 'status_trmpot' => '1'])->count();

            if ($data_potongan == '1') {
                DB::table('trhtrmpot')->where(['no_bukti' => $no_bukti1, 'kd_skpd' => $kd_skpd])->delete();

                DB::table('trdtrmpot')->where(['no_bukti' => $no_bukti1, 'kd_skpd' => $kd_skpd])->delete();
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

    // TERIMA POTONGAN KKPD
    public function indexPotongan()
    {
        return view('skpd.potongan_kkpd.index');
    }

    public function loadDataPotongan()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtrmpot_kkpd as a')->select('a.*')->selectRaw("(SELECT status_upload FROM trhtransout_kkpd WHERE no_voucher=a.no_voucher and kd_skpd=?)  as status_upload", [$kd_skpd])->selectRaw("(SELECT status_validasi FROM trhtransout_kkpd WHERE no_voucher=a.no_voucher and kd_skpd=?)  as status_validasi", [$kd_skpd])->where(['a.kd_skpd' => $kd_skpd])->orderBy('no_bukti')->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if ($row->status_validasi != '1') {
                $btn = '<a href="' . route("skpd.transaksi_kkpd.edit_potongan", Crypt::encryptString($row->no_bukti)) . '" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="fa fa-eye"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="hapusPotongan(' . $row->no_bukti . ',\'' . $row->no_voucher . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            } else {
                $btn = '';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function createPotongan()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $potongan1 = DB::table('trhtransout_kkpd as a')->join('trdtransout_kkpd as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd])->whereIn('a.jns_spp', ['1', '2', '3'])->where('a.status_upload', '!=', '1')->whereRaw("a.no_voucher NOT IN (SELECT no_voucher FROM trhtrmpot_kkpd a WHERE a.kd_skpd=?)", $kd_skpd)->distinct()->select('a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'b.no_sp2d', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'b.kd_rek6', 'b.nm_rek6', 'a.jns_spp', 'a.total');

        // $potongan3 = DB::table('tr_panjar_cmsbank as a')->where(['a.kd_skpd' => $kd_skpd])->where('a.status_upload', '!=', '1')->whereRaw("a.no_panjar NOT IN (SELECT no_voucher FROM trhtrmpot_cmsbank a WHERE a.kd_skpd=?)", $kd_skpd)->select(DB::raw("'' as no_tgl"), 'a.no_panjar as no_voucher', 'a.tgl_panjar as tgl_voucher', DB::raw("'' as no_sp2d"), 'a.kd_sub_kegiatan', DB::raw("'' as nm_sub_kegiatan"), DB::raw("'' as kd_rek6"), DB::raw("'' as nm_rek6"), DB::raw("'1' as jns_spp"), 'nilai as total')->union($potongan1);

        $potongan = DB::table(DB::raw("({$potongan1->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($potongan1)
            ->orderBy('tgl_voucher')
            ->orderBy('no_voucher')
            ->get();

        $rekanan1 = DB::table('trhspp')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5);

        $rekanan2 = DB::table('trhtrmpot')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5)->unionAll($rekanan1);

        $rekanan3 = DB::table('trhtrmpot_kkpd')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($rekanan2);

        $rekanan = DB::table(DB::raw("({$rekanan3->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($rekanan3)
            ->get();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_potongan' => $potongan,
            'daftar_sp2d' => DB::table('trhtransout_kkpd as a')->join('trdtransout_kkpd as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_voucher', '=', 'b.no_voucher');
            })->where(['b.kd_skpd' => $kd_skpd])->select('b.no_sp2d', 'a.jns_spp')->groupBy('b.no_sp2d', 'jns_spp')->orderBy('no_sp2d')->get(),
            'daftar_rekanan' => $rekanan,
            'daftar_rek' => DB::table('ms_pot')->select('kd_rek6', 'nm_rek6')->get(),
            'tahun_anggaran' => tahun_anggaran()
        ];

        return view('skpd.potongan_kkpd.create')->with($data);
    }

    public function cariKegiatanPotongan(Request $request)
    {
        $no_transaksi = $request->no_transaksi;
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = Auth::user()->kd_skpd;

        $data1 = DB::table('trdtransout_kkpd as a')->join('trhtransout_kkpd as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_sp2d' => $no_sp2d])->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->distinct();

        // $data2 = DB::table('tr_panjar_kkpd as a')->join('trdrka as b', function ($join) {
        //     $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
        // })->where(['a.kd_skpd' => $kd_skpd, 'a.no_panjar' => $no_transaksi])->select('a.kd_sub_kegiatan', 'b.nm_sub_kegiatan')->union($data1);

        $data = DB::table(DB::raw("({$data1->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($data1)
            ->get();

        return response()->json($data);
    }

    public function simpanPotongan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // NOMOR BUKTI
            $nomor1 = DB::table('trhtransout_kkpd')->select('no_voucher as nomor', DB::raw("'Daftar Transaksi Non Tunai' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd]);
            $nomor2 = DB::table('trhtrmpot_kkpd')->select('no_bukti as nomor', DB::raw("'Potongan Pajak Transaksi Non Tunai' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->union($nomor1);

            $nomor = DB::table(DB::raw("({$nomor2->toSql()}) AS sub"))
                ->select(DB::raw("CASE WHEN MAX(nomor+1) is null THEN 1 else MAX(nomor+1) END as nomor"))
                ->mergeBindings($nomor2)
                ->first();
            $no_bukti = $nomor->nomor;
            // TRHTRMPOT
            DB::table('trhtrmpot_kkpd')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $no_bukti])->delete();

            DB::table('trhtrmpot_kkpd')->insert([
                'no_bukti' => $no_bukti,
                'tgl_bukti' => $data['tgl_bukti'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $data['kd_skpd'],
                'nm_skpd' => $data['nm_skpd'],
                'nilai' => $data['total_potongan'],
                'npwp' => $data['npwp'],
                'jns_spp' => $data['beban'],
                'status' => '0',
                'no_sp2d' => $data['no_sp2d'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                'kd_rek6' => $data['kd_rekening'],
                'nm_rek6' => $data['nm_rekening'],
                'nmrekan' => $data['rekanan'],
                'pimpinan' => $data['pimpinan'],
                'alamat' => $data['alamat'],
                'no_voucher' => $data['no_transaksi'],
                'rekening_tujuan' => '',
                'nm_rekening_tujuan' => '',
                'status_upload' => '0',
            ]);

            DB::table('trhtransout_kkpd')->where(['kd_skpd' => $data['kd_skpd'], 'no_voucher' => $data['no_transaksi']])->update([
                'status_trmpot' => '1',
            ]);

            // TRDTRMPOT
            DB::table('trdtrmpot_kkpd')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_potongan'])) {
                DB::table('trdtrmpot_kkpd')->insert(array_map(function ($value) use ($data, $no_bukti) {
                    return [
                        'no_bukti' => $no_bukti,
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'kd_rek_trans' => $value['kd_rek_trans'],
                        'ebilling' => $value['no_billing'],
                        'rekanan' => $value['rekanan'],
                        'npwp' => $value['npwp'],
                    ];
                }, $data['rincian_potongan']));
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

    public function editPotongan($no_bukti)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_bukti = Crypt::decryptString($no_bukti);
        $potongan1 = DB::table('trhtransout_kkpd as a')->join('trdtransout_kkpd as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd])->whereIn('a.jns_spp', ['1', '2', '3'])->where('a.status_upload', '!=', '1')->whereRaw("a.no_voucher NOT IN (SELECT no_voucher FROM trhtrmpot_kkpd a WHERE a.kd_skpd=?)", $kd_skpd)->distinct()->select('a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'b.no_sp2d', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'b.kd_rek6', 'b.nm_rek6', 'a.jns_spp', 'a.total');

        // $potongan3 = DB::table('tr_panjar_cmsbank as a')->where(['a.kd_skpd' => $kd_skpd])->where('a.status_upload', '!=', '1')->whereRaw("a.no_panjar NOT IN (SELECT no_voucher FROM trhtrmpot_cmsbank a WHERE a.kd_skpd=?)", $kd_skpd)->select(DB::raw("'' as no_tgl"), 'a.no_panjar as no_voucher', 'a.tgl_panjar as tgl_voucher', DB::raw("'' as no_sp2d"), 'a.kd_sub_kegiatan', DB::raw("'' as nm_sub_kegiatan"), DB::raw("'' as kd_rek6"), DB::raw("'' as nm_rek6"), DB::raw("'1' as jns_spp"), 'nilai as total')->union($potongan1);

        $potongan = DB::table(DB::raw("({$potongan1->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($potongan1)
            ->orderBy('tgl_voucher')
            ->orderBy('no_voucher')
            ->get();

        $rekanan1 = DB::table('trhspp')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5);

        $rekanan2 = DB::table('trhtrmpot')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5)->unionAll($rekanan1);

        $rekanan3 = DB::table('trhtrmpot_kkpd')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($rekanan2);

        $rekanan = DB::table(DB::raw("({$rekanan3->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($rekanan3)
            ->get();

        $data = [
            'no_bukti' => $no_bukti,
            'data_potongan' => DB::table('trhtrmpot_kkpd')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->first(),
            'daftar_list_potongan' => DB::table('trdtrmpot_kkpd as a')->join('trhtrmpot_kkpd as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.*')->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->get(),
            'daftar_potongan' => $potongan,
            'daftar_sp2d' => DB::table('trhtransout_kkpd as a')->join('trdtransout_kkpd as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_voucher', '=', 'b.no_voucher');
            })->where(['b.kd_skpd' => $kd_skpd])->select('b.no_sp2d', 'a.jns_spp')->groupBy('b.no_sp2d', 'jns_spp')->orderBy('no_sp2d')->get(),
            'daftar_rekanan' => $rekanan,
            'daftar_rek' => DB::table('ms_pot')->select('kd_rek6', 'nm_rek6')->get(),
            'tahun_anggaran' => tahun_anggaran(),
        ];

        return view('skpd.potongan_kkpd.edit')->with($data);
    }

    public function simpanEditPotongan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // TRHTRMPOT
            DB::table('trhtrmpot_kkpd')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])->delete();

            DB::table('trhtrmpot_kkpd')->insert([
                'no_bukti' => $data['no_bukti'],
                'tgl_bukti' => $data['tgl_bukti'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $data['kd_skpd'],
                'nm_skpd' => $data['nm_skpd'],
                'nilai' => $data['total_potongan'],
                'npwp' => $data['npwp'],
                'jns_spp' => $data['beban'],
                'status' => '0',
                'no_sp2d' => $data['no_sp2d'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                'kd_rek6' => $data['kd_rekening'],
                'nm_rek6' => $data['nm_rekening'],
                'nmrekan' => $data['rekanan'],
                'pimpinan' => $data['pimpinan'],
                'alamat' => $data['alamat'],
                'no_voucher' => $data['no_transaksi'],
                'rekening_tujuan' => '',
                'nm_rekening_tujuan' => '',
                'status_upload' => '0',
            ]);

            DB::table('trhtransout_kkpd')->where(['kd_skpd' => $data['kd_skpd'], 'no_voucher' => $data['no_transaksi']])->update([
                'status_trmpot' => '1',
            ]);

            // TRDTRMPOT
            DB::table('trdtrmpot_kkpd')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_potongan'])) {
                DB::table('trdtrmpot_kkpd')->insert(array_map(function ($value) use ($data) {
                    return [
                        'no_bukti' => $data['no_bukti'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'kd_rek_trans' => $value['kd_rek_trans'],
                        'ebilling' => $value['no_billing'],
                        'rekanan' => $value['rekanan'],
                        'npwp' => $value['npwp'],
                    ];
                }, $data['rincian_potongan']));
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

    public function hapusPotongan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $no_voucher = $request->no_voucher;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhtransout_kkpd')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])->update([
                'status_trmpot' => '0'
            ]);

            DB::table('trdtrmpot_kkpd')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trhtrmpot_kkpd')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

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
