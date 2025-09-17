<?php

namespace App\Http\Controllers\Skpd\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use PDF;
use function PHPUnit\Framework\isEmpty;

class LPJController extends Controller
{
    // INPUT LPJ UP/GU SKPD TANPA UNIT
    public function indexSkpdTanpaUnit()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'ttd1' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->get(),
            'ttd2' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['PA', 'KPA'])
                ->get(),
        ];

        return view('skpd.lpj.skpd_tanpa_unit.index')->with($data);
    }

    public function loadSkpdTanpaUnit()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhlpj as a')
            ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
            ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
            ->orderBy('a.tgl_lpj')
            ->orderBy('a.no_lpj')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("lpj.skpd_tanpa_unit.edit", Crypt::encrypt($row->no_lpj)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->status == '1' || $row->status == '2') {
                $btn .= "";
            } else {
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_lpj . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_lpj . '\',\'' . $row->jenis . '\',\'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak LPJ" style="margin-right:4px"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahSkpdTanpaUnit()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'nilai_up' => DB::table('ms_up')
                ->selectRaw("SUM(nilai_up) as nilai")
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'spd_global' => collect(DB::select("SELECT ISNULL(nilai_spd,0) spd, ISNULL(transaksi,0) transaksi, isnull(nilai_spd,0)-isnull(transaksi,0) sisa_spd FROM(
                select 1 as nomor, SUM(nilai) as nilai_spd from trhspd a INNER JOIN trdspd b ON a.no_spd=b.no_spd WHERE kd_skpd = ? AND (RIGHT(kd_sub_kegiatan,10) !='01.1.02.01' OR kd_sub_kegiatan !='4.01.01.1.11.01') AND status='1') a LEFT JOIN (SELECT 1 as nomor, SUM(b.nilai) as transaksi FROM trhspp a INNER JOIN trdspp b ON a.kd_skpd=b.kd_skpd AND a.no_spp=b.no_spp WHERE a.kd_skpd = ? AND (RIGHT(b.kd_sub_kegiatan,10) !='01.1.02.01' OR b.kd_sub_kegiatan !='4.01.01.1.11.01') and (sp2d_batal is null or sp2d_batal<>'1')) b ON a.nomor=b.nomor", [$kd_skpd, $kd_skpd]))->first(),
            'tanggal_awal' => collect(DB::select("SELECT DATEADD(DAY,1,MAX(tgl_akhir)) as tanggal_awal FROM trhlpj WHERE jenis=? AND kd_skpd = ?", ['1', $kd_skpd]))->first()->tanggal_awal
        ];

        return view('skpd.lpj.skpd_tanpa_unit.create')->with($data);
    }

    public function detailSkpdTanpaUnit(Request $request)
    {
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $kd_skpd = $request->kd_skpd;

        $data1 = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("b.kd_skpd,b.tgl_bukti,a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.kd_rek6,a.nm_rek6,a.no_bukti,a.nilai,a.kd_skpd as kd_skpd1")
            ->whereRaw("(a.no_bukti+a.kd_sub_kegiatan+a.kd_rek6+a.kd_skpd) NOT IN(SELECT (no_bukti+kd_sub_kegiatan+kd_rek6+kd_skpd) FROM trlpj) AND b.panjar not in ('3','5') AND b.tgl_bukti >= ? and b.tgl_bukti <= ? and b.jns_spp='1' and b.kd_skpd=?", [$tgl_awal, $tgl_akhir, $kd_skpd]);

        $data2 = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("b.kd_skpd,b.tgl_bukti,a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.kd_rek6,a.nm_rek6,a.no_bukti,a.nilai,a.kd_skpd as kd_skpd1")
            ->whereRaw("(a.no_bukti+a.kd_sub_kegiatan+a.kd_rek6+a.kd_skpd) NOT IN(SELECT (no_bukti+kd_sub_kegiatan+kd_rek6+kd_skpd) FROM trlpj) AND b.panjar in ('3','5') AND b.tgl_bukti >= ? and b.tgl_bukti <= ? and b.jns_spp='1' and b.kd_skpd=?", [$tgl_awal, $tgl_akhir, $kd_skpd])
            ->unionAll($data1);

        $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
            ->mergeBindings($data2)
            ->orderByRaw("kd_skpd,tgl_bukti,kd_sub_kegiatan, kd_rek6, cast(no_bukti as int)")
            ->get();

        return response()->json($data);
    }

    public function totalspdSkpdTanpaUnit(Request $request)
    {
        $jns = '5';
        $kd_skpd = Auth::user()->kd_skpd;
        $nospp = '';
        $no_bukti = '';

        // $data = collect(DB::select("SELECT spd,keluar1 = keluar-terima,keluarspp  from(
        //                 select sum(spd) as spd,sum(terima) as terima,sum(keluar) as keluar,sum(keluarspp) as keluarspp from(SELECT 'SPD' as ket,isnull(sum(nilai),0) as spd,0 as terima,0 as keluar,0 as keluarspp from trhspd d join trdspd e on d.no_spd=e.no_spd
        //                     where d.kd_skpd=? and d.status='1' and d.jns_beban=? UNION ALL
        //                     SELECT 'SPP' as ket,0 as spd,0 as terima,isnull(sum(a.nilai),0) [keluar],0 as keluarspp from trdspp a join trhspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd
        //                     where LEFT(kd_rek6,1)=? and b.jns_spp in ('3','4','5','6') and left(a.kd_skpd,17)=left(?,17) and b.no_spp<>? and (sp2d_batal is null or sp2d_batal <>'1')
        //                     union all
        //                     select 'Trans UP/GU' as ket,0 as spd,0 as terima,isnull(sum(a.nilai),0) [keluar],0 as keluarspp from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
        //                     where LEFT(kd_rek6,1)=? and b.jns_spp in ('1','2') and left(a.kd_skpd,17)=left(?,17) and a.no_bukti<>?
        //                     union all
        //                     select 'Trans UP/GU CMS' as ket,0 as spd,0 as terima,isnull(sum(a.nilai),0) [keluar],0 as keluarspp from trdtransout_cmsbank a join trhtransout_cmsbank b on a.no_voucher=b.no_voucher
        //                     and a.kd_skpd=b.kd_skpd where LEFT(kd_rek6,1)=? and b.jns_spp in ('1','2') and left(a.kd_skpd,17)=left(?,17) and status_validasi<>'1' union all
        //                     select 'Panjar' as ket,0 as spd,0 as terima,ISNULL(sum(nilai),0) as keluar,0 as keluarspp from tr_panjar where jns='1' and left(kd_skpd,17)=left(?,17) and no_kas<>?
        //                     union all
        //                     select 'T/P Panjar' as ket,0 as spd,ISNULL(sum(nilai),0) as terima,0 as keluar,0 as keluarspp from tr_jpanjar where left(kd_skpd,17)=left(?,17) and no_kas<>? union all
        //                     select 'SPP' as ket,0 as spd,0 as terima,0 as keluar,isnull(sum(a.nilai),0) [keluarspp] from trdspp a join trhspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd
        //                     where b.jns_spp in ('1','2','3','6') and left(a.kd_skpd,17)=left(?,17) and b.no_spp<>? and (sp2d_batal is null or sp2d_batal <>'1') )as f
        //             )as g", [$kd_skpd, $jns, $jns, $kd_skpd, $nospp, $jns, $kd_skpd, $no_bukti, $jns, $kd_skpd, $kd_skpd, $no_bukti, $kd_skpd, $no_bukti, $kd_skpd, $nospp]))->first();

        $data = collect(DB::select("SELECT spd,keluar1 = keluar-terima,keluarspp  from(
                        select sum(spd) as spd,sum(terima) as terima,sum(keluar) as keluar,sum(keluarspp) as keluarspp from(SELECT 'SPD' as ket,isnull(sum(nilai),0) as spd,0 as terima,0 as keluar,0 as keluarspp from trhspd d join trdspd e on d.no_spd=e.no_spd
                            where d.kd_skpd=? and d.status='1' and d.jns_beban=?
                            UNION ALL
                            SELECT 'SPP' as ket,0 as spd,0 as terima,isnull(sum(a.nilai),0) [keluar],0 as keluarspp from trdspp a join trhspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd
                            where LEFT(kd_rek6,1)=? and b.jns_spp in ('3','4','5','6') and left(a.kd_skpd,22)=left(?,22) and b.no_spp<>? and (sp2d_batal is null or sp2d_batal <>'1')
                            union all
                            select 'Trans UP/GU' as ket,0 as spd,0 as terima,isnull(sum(a.nilai),0) [keluar],0 as keluarspp from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                            where LEFT(kd_rek6,1)=? and b.jns_spp in ('1','2') and left(a.kd_skpd,22)=left(?,22) and a.no_bukti<>?
                            union all
                            select 'Trans UP/GU CMS' as ket,0 as spd,0 as terima,isnull(sum(a.nilai),0) [keluar],0 as keluarspp from trdtransout_cmsbank a join trhtransout_cmsbank b on a.no_voucher=b.no_voucher
                            and a.kd_skpd=b.kd_skpd where LEFT(kd_rek6,1)=? and b.jns_spp in ('1','2') and left(a.kd_skpd,22)=left(?,22) and status_validasi<>'1'
                            union all
                            select 'Panjar' as ket,0 as spd,0 as terima,ISNULL(sum(nilai),0) as keluar,0 as keluarspp from tr_panjar where jns='1' and left(kd_skpd,22)=left(?,22) and no_kas<>?
                            union all
                            select 'T/P Panjar' as ket,0 as spd,ISNULL(sum(nilai),0) as terima,0 as keluar,0 as keluarspp from tr_jpanjar where left(kd_skpd,22)=left(?,22) and no_kas<>?
                            union all
                            select 'SPP' as ket,0 as spd,0 as terima,0 as keluar,isnull(sum(a.nilai),0) [keluarspp] from trdspp a join trhspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd
                            where b.jns_spp in ('1','2','3','6') and left(a.kd_skpd,22)=left(?,22) and b.no_spp<>? and (sp2d_batal is null or sp2d_batal <>'1'))as f
                    )as g", [$kd_skpd, $jns, $jns, $kd_skpd, $nospp, $jns, $kd_skpd, $no_bukti, $jns, $kd_skpd, $kd_skpd, $no_bukti, $kd_skpd, $no_bukti, $kd_skpd, $nospp]))->first();

        return response()->json($data);
    }

    public function simpanSkpdTanpaUnit(Request $request)
    {
        // ini_set('max_input_vars', '10000');
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        // $data['detail_lpj'] = json_decode($data['detail_lpj'], true);

        DB::beginTransaction();
        try {
            $no_lpj = $data['no_lpj'] . "/LPJ/UPGU/" . $data['kd_skpd'] . "/" . tahun_anggaran();

            $cek_lpj = DB::table('trhlpj')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->count();
            if ($cek_lpj > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhlpj')
                ->insert([
                    'no_lpj' => $no_lpj,
                    'tgl_lpj' => $data['tgl_lpj'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'tgl_awal' => $data['tgl_awal'],
                    'tgl_akhir' => $data['tgl_akhir'],
                    'status' => '0',
                    'jenis' => '1',
                ]);

            $data['detail_lpj'] = json_decode($data['detail_lpj'], true);

            $rincian_data = $data['detail_lpj'];
            $tgl_lpj = $data['tgl_lpj'];
            $kd_skpd = $data['kd_skpd'];

            if (isset($rincian_data)) {
                // DB::table('trlpj')
                //     ->insert(array_map(function ($value) use ($no_lpj, $data) {
                //         return [
                //             'no_lpj' => $no_lpj,
                //             'kd_skpd' => $value['kd_skpd'],
                //             'no_bukti' => $value['no_bukti'],
                //             'tgl_lpj' => $data['tgl_lpj'],
                //             'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                //             'kd_rek6' => $value['kdrek6'],
                //             'nm_rek6' => $value['nmrek6'],
                //             'nilai' => $value['nilai'],
                //             'kd_bp_skpd' => $data['kd_skpd'],
                //             'no_lpj_unit' => $no_lpj,
                //         ];
                //     }, $data['detail_lpj']));
                foreach ($rincian_data as $data => $value) {
                    $data = [
                        'no_lpj' => $no_lpj,
                        'kd_skpd' => $rincian_data[$data]['kd_skpd'],
                        'no_bukti' => $rincian_data[$data]['no_bukti'],
                        'tgl_lpj' => $tgl_lpj,
                        'kd_sub_kegiatan' => $rincian_data[$data]['kd_sub_kegiatan'],
                        'kd_rek6' => $rincian_data[$data]['kdrek6'],
                        'nm_rek6' => $rincian_data[$data]['nmrek6'],
                        'nilai' => $rincian_data[$data]['nilai'],
                        'kd_bp_skpd' => $kd_skpd,
                        //'no_lpj_unit' => $no_lpj,
                    ];
                    DB::table('trlpj')->insert($data);
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

    public function editSkpdTanpaUnit($no_lpj)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_lpj = Crypt::decrypt($no_lpj);
        $arr = explode("/", $no_lpj);

        $data = [
            'nomor' => $arr[0],
            'lpj' => DB::table('trhlpj as a')
                ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
                ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '1', 'a.no_lpj' => $no_lpj])
                ->orderBy('a.tgl_lpj')
                ->orderBy('a.no_lpj')
                ->first(),
            'detail_lpj' => DB::table('trhlpj as a')
                ->join('trlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->select('b.*')
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
                ->get(),
            'total_detail' => DB::table('trhlpj as a')
                ->join('trlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("SUM(b.nilai) as nilai")
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
                ->first(),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'nilai_up' => DB::table('ms_up')
                ->selectRaw("SUM(nilai_up) as nilai")
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'spd_global' => collect(DB::select("SELECT ISNULL(nilai_spd,0) spd, ISNULL(transaksi,0) transaksi, isnull(nilai_spd,0)-isnull(transaksi,0) sisa_spd FROM(
                select 1 as nomor, SUM(nilai) as nilai_spd from trhspd a INNER JOIN trdspd b ON a.no_spd=b.no_spd WHERE kd_skpd = ? AND (RIGHT(kd_sub_kegiatan,10) !='01.1.02.01' OR kd_sub_kegiatan !='4.01.01.1.11.01') AND status='1') a LEFT JOIN (SELECT 1 as nomor, SUM(b.nilai) as transaksi FROM trhspp a INNER JOIN trdspp b ON a.kd_skpd=b.kd_skpd AND a.no_spp=b.no_spp WHERE a.kd_skpd = ? AND (RIGHT(b.kd_sub_kegiatan,10) !='01.1.02.01' OR b.kd_sub_kegiatan !='4.01.01.1.11.01') and (sp2d_batal is null or sp2d_batal<>'1')) b ON a.nomor=b.nomor", [$kd_skpd, $kd_skpd]))->first(),
        ];

        return view('skpd.lpj.skpd_tanpa_unit.edit')->with($data);
    }

    public function updateSkpdTanpaUnit(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_lpj = $data['no_lpj'] . "/LPJ/UPGU/" . $data['kd_skpd'] . "/" . tahun_anggaran();

            $cek_terima = DB::table('trhlpj')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $data['kd_skpd']])->count();
            if ($cek_terima > 0 && $no_lpj != $data['no_lpj_simpan']) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhlpj')->where(['no_lpj' => $data['no_lpj_simpan'], 'kd_skpd' => $data['kd_skpd']])->delete();

            DB::table('trhlpj')
                ->insert([
                    'no_lpj' => $no_lpj,
                    'tgl_lpj' => $data['tgl_lpj'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'tgl_awal' => $data['tgl_awal'],
                    'tgl_akhir' => $data['tgl_akhir'],
                    'status' => '0',
                    'jenis' => '1',
                ]);

            DB::table('trlpj')->where(['no_lpj' => $data['no_lpj_simpan'], 'kd_skpd' => $data['kd_skpd']])->delete();

            if (isset($data['detail_lpj'])) {
                DB::table('trlpj')
                    ->insert(array_map(function ($value) use ($no_lpj, $data) {
                        return [
                            'no_lpj' => $no_lpj,
                            'kd_skpd' => $value['kd_skpd'],
                            'no_bukti' => $value['no_bukti'],
                            'tgl_lpj' => $data['tgl_lpj'],
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            'kd_rek6' => $value['kdrek6'],
                            'nm_rek6' => $value['nmrek6'],
                            'nilai' => $value['nilai'],
                            'kd_bp_skpd' => $data['kd_skpd'],
                            //'no_lpj_unit' => $no_lpj,
                        ];
                    }, $data['detail_lpj']));
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

    public function cekSkpdTanpaUnit(Request $request)
    {
        $kd_skpd = $request->kd_skpd;

        $data = collect(DB::select("SELECT * from tb_kendali_lpj where kd_skpd=?", [$kd_skpd]))->first();

        return response()->json($data);
    }

    public function hapusSkpdTanpaUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trlpj')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::table('trhlpj')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
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

    public function subKegiatanSkpdTanpaUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = Auth::user()->kd_skpd;

        // $data = DB::table('trlpj as a')
        //     ->join('trhlpj as b', function ($join) {
        //         $join->on('a.no_lpj', '=', 'b.no_lpj');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->leftJoin('trskpd as c', function ($join) {
        //         $join->on('a.kd_sub_kegiatan', '=', 'c.kd_sub_kegiatan');
        //     })
        //     ->select('a.kd_sub_kegiatan', 'c.nm_sub_kegiatan')
        //     ->whereRaw("a.no_lpj = ? AND left(a.kd_skpd,17)=left(?,17)", [$no_lpj, $kd_skpd])
        //     ->groupBy('a.kd_sub_kegiatan', 'c.nm_sub_kegiatan')
        //     ->orderBy('a.kd_sub_kegiatan')
        //     ->get();

        $data = DB::select("SELECT a.kd_sub_kegiatan, c.nm_sub_kegiatan
		from trlpj a
		INNER JOIN trhlpj b ON a.no_lpj=b.no_lpj AND a.kd_bp_skpd=b.kd_skpd
		LEFT JOIN trskpd c ON a.kd_sub_kegiatan=c.kd_sub_kegiatan
		WHERE a.no_lpj = ? AND left(a.kd_skpd,22)=left(?,22)
		GROUP BY a.kd_sub_kegiatan,c.nm_sub_kegiatan
		ORDER BY a.kd_sub_kegiatan", [$no_lpj, $kd_skpd]);

        return response()->json($data);
    }

    public function sptbSkpdTanpaUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;
        $pa_kpa = $request->pa_kpa;
        $jenis_print = $request->jenis_print;

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'dpa' => DB::table('trhrka')
                ->select('no_dpa', 'tgl_dpa')
                ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => status_anggaran()])
                ->first(),
            'jumlah_belanja' => DB::table('trlpj as a')
                ->join('trhlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_bp_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("sum(nilai) as nilai,b.tgl_lpj")
                ->whereRaw("a.no_lpj=? and b.jenis=? and left(a.kd_bp_skpd,22)=left(?,22)", [$no_lpj, '1', $kd_skpd])
                ->groupBy('b.tgl_lpj')
                ->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'daerah' => DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first()
        ];

        $view = view('skpd.lpj.skpd_tanpa_unit.cetak.sptb')->with($data);

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

    public function rincianSkpdTanpaUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;
        $bendahara = $request->bendahara;
        $pa_kpa = $request->pa_kpa;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $pilihan = $request->pilihan;
        $jenis_print = $request->jenis_print;
        $status_anggaran = status_anggaran();

        if ($pilihan == '0') {
            $data_lpj = DB::select("SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,7) as kode, b.nm_program as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_program,nm_program,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_program,nm_program,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,7) =b.kd_program AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,7), b.nm_program
                        UNION ALL

                        SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,12) as kode, b.nm_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_kegiatan,nm_kegiatan,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_kegiatan,nm_kegiatan,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,12) =b.kd_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,12), b.nm_kegiatan

                        UNION ALL
                        SELECT 2 as urut, a.kd_sub_kegiatan as kode, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE no_lpj=? AND a.kd_bp_skpd=? and b.jns_ang=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan
                        UNION ALL
                        SELECT 3 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,2) as kode, b.nm_rek2 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek2 b ON LEFT(a.kd_rek6,2)=b.kd_rek2
                        WHERE no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,2), b.nm_rek2
                        UNION ALL
                        SELECT 4 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,4) as kode, b.nm_rek3 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek3 b ON LEFT(a.kd_rek6,4)=b.kd_rek3
                        WHERE no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,4), b.nm_rek3
                        UNION ALL

                        SELECT 5 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,6) as kode, b.nm_rek4 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek4 b ON LEFT(a.kd_rek6,6)=b.kd_rek4
                        WHERE no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,6), b.nm_rek4
                        UNION ALL

                        SELECT 6 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,8) as kode, b.nm_rek5 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek5 b ON LEFT(a.kd_rek6,8)=b.kd_rek5
                        WHERE no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,8), b.nm_rek5
                        UNION ALL
                        SELECT 7 as urut, kd_sub_kegiatan+'.'+kd_rek6 as kode, nm_rek6 as uraian, SUM(nilai) as nilai FROM trlpj
                        WHERE no_lpj=? AND kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, kd_rek6, nm_rek6
                        ORDER BY kode", [$status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $status_anggaran, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd]);
        } elseif ($pilihan == '1') {
            $data_lpj = DB::select("SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,7) as kode, b.nm_program as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_program,nm_program,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_program,nm_program,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,7) =b.kd_program AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj=? AND a.kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,7), b.nm_program
                        UNION ALL
                        SELECT 2 as urut, LEFT(a.kd_sub_kegiatan,12) as kode, b.nm_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_kegiatan,nm_kegiatan,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_kegiatan,nm_kegiatan,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,12) =b.kd_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,12), b.nm_kegiatan
                        UNION ALL
                        SELECT 3 as urut, a.kd_sub_kegiatan as kode, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE no_lpj=? AND a.kd_bp_skpd=? and b.jns_ang=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,22)=left(?,22)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan
                        ORDER BY kode", [$status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $status_anggaran, $kd_skpd]);
        } elseif ($pilihan == '2') {
            $data_lpj = DB::select("SELECT 1 as urut, a.kd_sub_kegiatan as kode, a.kd_sub_kegiatan as rek, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti]
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? and jns_ang=?
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND left(a.kd_skpd,22)=left(?,22)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan

                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,2) as kode, LEFT(a.kd_rek6,2) as rek,  nm_rek2 as uraian, SUM(nilai) as nilai,
                        '' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek2 b ON LEFT(a.kd_rek6,2)=b.kd_rek2
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,22)=left(?,22)
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,2), nm_rek2
                        UNION ALL
                        SELECT 3 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,4) as kode, LEFT(a.kd_rek6,4) as rek,  nm_rek3 as uraian, SUM(nilai) as nilai,
                        '' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek3 b ON LEFT(a.kd_rek6,4)=b.kd_rek3
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,22)=left(?,22)
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,4), nm_rek3
                        UNION ALL
                        SELECT 4 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,6) as kode, LEFT(a.kd_rek6,6) as rek,  nm_rek4 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek4 b ON LEFT(a.kd_rek6,6)=b.kd_rek4
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,22)=left(?,22)
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,6), nm_rek4
                        UNION ALL
                        SELECT 5 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,8) as kode, LEFT(a.kd_rek6,8) as rek,  nm_rek5 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek5 b ON LEFT(a.kd_rek6,8)=b.kd_rek5
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,22)=left(?,22)
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,8), nm_rek5
                        UNION ALL

                        SELECT 6 as urut, kd_sub_kegiatan+'.'+kd_rek6 as kode, kd_rek6 as rek,  nm_rek6 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti]
                        FROM trlpj a
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,22)=left(?,22)
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, kd_rek6, nm_rek6
                        UNION ALL

                        SELECT 7 as urut, a.kd_sub_kegiatan+'.'+a.kd_rek6+'.1' as kode,'' as rek, c.ket+' \\ No BKU: '+a.no_bukti as uraian, sum(a.nilai) as nilai,
                        c.tgl_bukti,a.no_bukti
                        FROM trlpj a
                        INNER JOIN trhlpj b ON a.no_lpj=b.no_lpj AND a.kd_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE a.no_lpj=?
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND left(a.kd_skpd,22)=left(?,22)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY a.kd_sub_kegiatan, a.kd_rek6,nm_rek6,a.no_bukti, ket,tgl_bukti
                        ORDER BY kode,tgl_bukti,no_bukti", [$no_lpj, $status_anggaran, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan]);
        }

        if ($pilihan == '2') {
            $kd_sub_kegiatan = $kd_sub_kegiatan;
        } else {
            $kd_sub_kegiatan = '';
        }

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'dpa' => DB::table('trhrka')
                ->select('no_dpa', 'tgl_dpa')
                ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => status_anggaran()])
                ->first(),
            'jumlah_belanja' => DB::table('trlpj as a')
                ->join('trhlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_bp_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("sum(nilai) as nilai,b.tgl_lpj")
                ->whereRaw("a.no_lpj=? and b.jenis=? and left(a.kd_bp_skpd,22)=left(?,22)", [$no_lpj, '1', $kd_skpd])
                ->groupBy('b.tgl_lpj')
                ->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $bendahara])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first(),
            'lpj' => DB::table('trhlpj')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->first(),
            'pilihan' => $pilihan,
            'data_lpj' => $data_lpj,
            'persediaan' => DB::table('ms_up')->selectRaw("SUM(nilai_up) as nilai")->where(['kd_skpd' => $kd_skpd])->first(),
            'kegiatan' => $kd_sub_kegiatan
        ];

        $view = view('skpd.lpj.skpd_tanpa_unit.cetak.rincian')->with($data);

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

    public function rekapSkpdTanpaUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;
        $pa_kpa = $request->pa_kpa;
        $bendahara = $request->bendahara;
        $jenis_print = $request->jenis_print;

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'dpa' => DB::table('trhrka')
                ->select('no_dpa', 'tgl_dpa')
                ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => status_anggaran()])
                ->first(),
            'jumlah_belanja' => DB::table('trlpj as a')
                ->join('trhlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_bp_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("sum(nilai) as nilai,b.tgl_lpj")
                ->whereRaw("a.no_lpj=? and b.jenis=? and left(a.kd_bp_skpd,22)=left(?,22)", [$no_lpj, '1', $kd_skpd])
                ->groupBy('b.tgl_lpj')
                ->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $bendahara])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first(),
            'data_lpj' => DB::select("SELECT kd_skpd as kode, (select nm_skpd from ms_skpd z where z.kd_skpd=a.kd_skpd )as nama, SUM(a.nilai) as nilai FROM trlpj a  WHERE a.no_lpj=? and kd_bp_skpd=? GROUP BY kd_skpd ORDER BY kode", [$no_lpj, $kd_skpd]),
            'lpj' => DB::table('trhlpj')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->first(),
        ];

        $view = view('skpd.lpj.skpd_tanpa_unit.cetak.rekap')->with($data);

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

    // INPUT LPJ UP/GU SKPD + UNIT
    public function indexSkpdDanUnit()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'ttd1' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->get(),
            'ttd2' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['PA', 'KPA'])
                ->get(),
        ];

        return view('skpd.lpj.skpd_dan_unit.index')->with($data);
    }

    public function loadSkpdDanUnit()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhlpj as a')
            ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
            ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
            ->orderBy('a.tgl_lpj')
            ->orderBy('a.no_lpj')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("lpj.skpd_dan_unit.edit", Crypt::encrypt($row->no_lpj)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->status == '1' || $row->status == '2') {
                $btn .= "";
            } else {
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_lpj . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_lpj . '\',\'' . $row->jenis . '\',\'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak LPJ" style="margin-right:4px"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahSkpdDanUnit()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'nilai_up' => DB::table('ms_up')
                ->selectRaw("SUM(nilai_up) as nilai")
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'spd_global' => collect(DB::select("SELECT ISNULL(nilai_spd,0) spd, ISNULL(transaksi,0) transaksi, isnull(nilai_spd,0)-isnull(transaksi,0) sisa_spd FROM(
                select 1 as nomor, SUM(nilai) as nilai_spd from trhspd a INNER JOIN trdspd b ON a.no_spd=b.no_spd WHERE kd_skpd = ? AND (RIGHT(kd_sub_kegiatan,10) !='01.1.02.01' OR kd_sub_kegiatan !='4.01.01.1.11.01') AND status='1') a LEFT JOIN (SELECT 1 as nomor, SUM(b.nilai) as transaksi FROM trhspp a INNER JOIN trdspp b ON a.kd_skpd=b.kd_skpd AND a.no_spp=b.no_spp WHERE a.kd_skpd = ? AND (RIGHT(b.kd_sub_kegiatan,10) !='01.1.02.01' OR b.kd_sub_kegiatan !='4.01.01.1.11.01') and (sp2d_batal is null or sp2d_batal<>'1')) b ON a.nomor=b.nomor", [$kd_skpd, $kd_skpd]))->first(),
        ];

        return view('skpd.lpj.skpd_dan_unit.create')->with($data);
    }

    public function loadLpjSkpdDanUnit(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $no_lpj_unit = $request->no_lpj_unit;

        $no_lpj = array();
        if (!empty($no_lpj_unit)) {
            foreach ($no_lpj_unit as $lpj) {
                $no_lpj[] = $lpj['no_lpj_unit'];
            }
        } else {
            $no_lpj[] = '';
        }

        $data = DB::table('trhlpj_unit as a')
            ->selectRaw("a.*,(SELECT SUM(nilai) FROM trlpj WHERE no_lpj_unit=a.no_lpj) AS nilai,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd=a.kd_skpd) AS nm_skpd")
            ->where(['a.status' => '1', 'a.jenis' => '1', 'a.status_validasi' => '1'])
            ->whereRaw("a.no_lpj NOT IN (SELECT no_lpj_unit FROM trlpj WHERE a.no_lpj=no_lpj_unit AND a.kd_skpd=kd_skpd AND (no_lpj <> '' OR kd_bp_skpd <> ''))")
            ->whereRaw("LEFT(a.kd_skpd,17)=LEFT(?,17)", [$kd_skpd])
            ->whereNotIn('no_lpj', $no_lpj)
            ->get();

        return response()->json($data);
    }

    public function simpanSkpdDanUnit(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $list_lpj = array();
            if (!empty($data['detail_lpj'])) {
                foreach ($data['detail_lpj'] as $lpj) {
                    $list_lpj[] = $lpj['no_lpj_unit'];
                }
            } else {
                $list_lpj[] = '';
            }

            $no_lpj = $data['no_lpj'] . "/LPJ/GLOBAL/UPGU/" . $data['kd_skpd'] . "/" . tahun_anggaran();

            $cek_lpj = DB::table('trhlpj')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->count();

            if ($cek_lpj > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhlpj')
                ->insert([
                    'no_lpj' => $no_lpj,
                    'tgl_lpj' => $data['tgl_lpj'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'tgl_awal' => '',
                    'tgl_akhir' => '',
                    'status' => '1',
                    'jenis' => '1',
                ]);

            DB::table('trlpj')
                ->whereIn('no_lpj_unit', $list_lpj)
                ->update([
                    'no_lpj' => $no_lpj,
                    'kd_bp_skpd' => $data['kd_skpd']
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

    public function editSkpdDanUnit($no_lpj)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_lpj = Crypt::decrypt($no_lpj);
        $arr = explode("/", $no_lpj);

        $data = [
            'nomor' => $arr[0],
            'lpj' => DB::table('trhlpj as a')
                ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
                ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '1', 'a.no_lpj' => $no_lpj])
                ->first(),
            'detail_lpj' => DB::table('trhlpj as a')
                ->join('trlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_skpd', '=', 'b.kd_bp_skpd');
                })
                ->join('trhlpj_unit as c', function ($join) {
                    $join->on('b.no_lpj_unit', '=', 'c.no_lpj');
                    $join->on('b.kd_skpd', '=', 'c.kd_skpd');
                })
                ->select('a.no_lpj as lpj_global', 'c.*')
                ->selectRaw("(SELECT SUM(nilai) FROM trlpj WHERE no_lpj_unit=c.no_lpj) AS nilai")
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
                ->distinct()
                ->get(),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'nilai_up' => DB::table('ms_up')
                ->selectRaw("SUM(nilai_up) as nilai")
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'spd_global' => collect(DB::select("SELECT ISNULL(nilai_spd,0) spd, ISNULL(transaksi,0) transaksi, isnull(nilai_spd,0)-isnull(transaksi,0) sisa_spd FROM(
                select 1 as nomor, SUM(nilai) as nilai_spd from trhspd a INNER JOIN trdspd b ON a.no_spd=b.no_spd WHERE kd_skpd = ? AND (RIGHT(kd_sub_kegiatan,10) !='01.1.02.01' OR kd_sub_kegiatan !='4.01.01.1.11.01') AND status='1') a LEFT JOIN (SELECT 1 as nomor, SUM(b.nilai) as transaksi FROM trhspp a INNER JOIN trdspp b ON a.kd_skpd=b.kd_skpd AND a.no_spp=b.no_spp WHERE a.kd_skpd = ? AND (RIGHT(b.kd_sub_kegiatan,10) !='01.1.02.01' OR b.kd_sub_kegiatan !='4.01.01.1.11.01') and (sp2d_batal is null or sp2d_batal<>'1')) b ON a.nomor=b.nomor", [$kd_skpd, $kd_skpd]))->first(),
        ];
        // dd($data['detail_lpj']);
        return view('skpd.lpj.skpd_dan_unit.edit')->with($data);
    }

    public function updateSkpdDanUnit(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_lpj = $data['no_lpj'] . "/LPJ/GLOBAL/UPGU/" . $data['kd_skpd'] . "/" . tahun_anggaran();

            $cek_terima = DB::table('trhlpj')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $data['kd_skpd']])->count();
            if ($cek_terima > 0 && $no_lpj != $data['no_lpj_simpan']) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            $list_lpj = array();
            if (!empty($data['detail_lpj'])) {
                foreach ($data['detail_lpj'] as $lpj) {
                    $list_lpj[] = $lpj['no_lpj_unit'];
                }
            } else {
                $list_lpj[] = '';
            }

            DB::table('trhlpj')
                ->where(['no_lpj' => $data['no_lpj_simpan'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            DB::table('trhlpj')
                ->insert([
                    'no_lpj' => $no_lpj,
                    'tgl_lpj' => $data['tgl_lpj'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'tgl_awal' => '',
                    'tgl_akhir' => '',
                    'status' => '0',
                    'jenis' => '1',
                ]);

            DB::table('trlpj')
                ->where(['no_lpj' => $data['no_lpj_simpan'], 'kd_bp_skpd' => $data['kd_skpd']])
                ->update([
                    'no_lpj' => '',
                    'kd_bp_skpd' => ''
                ]);

            DB::table('trlpj')
                ->whereIn('no_lpj_unit', $list_lpj)
                ->update([
                    'no_lpj' => $no_lpj,
                    'kd_bp_skpd' => $data['kd_skpd']
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

    public function hapusSkpdDanUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trlpj')
                ->where(['no_lpj' => $no_lpj, 'kd_bp_skpd' => $kd_skpd])
                ->update([
                    'no_lpj' => '',
                    'kd_bp_skpd' => ''
                ]);

            DB::table('trhlpj')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
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

    public function rincianSkpdDanUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;
        $bendahara = $request->bendahara;
        $pa_kpa = $request->pa_kpa;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $pilihan = $request->pilihan;
        $jenis_print = $request->jenis_print;
        $status_anggaran = status_anggaran();

        if ($pilihan == '0') {
            $data_lpj = DB::select("SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,7) as kode, b.nm_program as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_program,nm_program,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_program,nm_program,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,7) =b.kd_program AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,7), b.nm_program
                        UNION ALL

                        SELECT 2 as urut, LEFT(a.kd_sub_kegiatan,12) as kode, b.nm_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_kegiatan,nm_kegiatan,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_kegiatan,nm_kegiatan,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,12) =b.kd_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,12), b.nm_kegiatan

                        UNION ALL
                        SELECT 3 as urut, a.kd_sub_kegiatan as kode, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE no_lpj=? AND a.kd_bp_skpd=? and jns_ang=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan
                        UNION ALL
                        SELECT 4 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,2) as kode, b.nm_rek2 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek2 b ON LEFT(a.kd_rek6,2)=b.kd_rek2
                        WHERE no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,2), b.nm_rek2
                        UNION ALL
                        SELECT 5 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,4) as kode, b.nm_rek3 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek3 b ON LEFT(a.kd_rek6,4)=b.kd_rek3
                        WHERE no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,4), b.nm_rek3
                        UNION ALL

                        SELECT 6 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,6) as kode, b.nm_rek4 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek4 b ON LEFT(a.kd_rek6,6)=b.kd_rek4
                        WHERE no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,6), b.nm_rek4
                        UNION ALL

                        SELECT 7 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,8) as kode, b.nm_rek5 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek5 b ON LEFT(a.kd_rek6,8)=b.kd_rek5
                        WHERE no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,8), b.nm_rek5
                        UNION ALL
                        SELECT 8 as urut, kd_sub_kegiatan+'.'+kd_rek6 as kode, nm_rek6 as uraian, SUM(nilai) as nilai FROM trlpj
                        WHERE no_lpj=? AND kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, kd_rek6, nm_rek6
                        ORDER BY kode", [$status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $status_anggaran, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd]);
        } elseif ($pilihan == '1') {
            $data_lpj = DB::select("SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,7) as kode, b.nm_program as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_program,nm_program,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_program,nm_program,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,7) =b.kd_program AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,7), b.nm_program
                        UNION ALL
                        SELECT 2 as urut, LEFT(a.kd_sub_kegiatan,12) as kode, b.nm_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_kegiatan,nm_kegiatan,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_kegiatan,nm_kegiatan,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,12) =b.kd_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj=? AND a.kd_bp_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,12), b.nm_kegiatan
                        UNION ALL
                        SELECT 3 as urut, a.kd_sub_kegiatan as kode, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE no_lpj=? AND a.kd_bp_skpd=?  and jns_ang=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE left(kd_skpd,17)=left(?,17)
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan
                        ORDER BY kode", [$status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $status_anggaran, $kd_skpd]);
        } elseif ($pilihan == '2') {
            $data_lpj = DB::select("SELECT 1 as urut, a.kd_sub_kegiatan as kode,a.kd_sub_kegiatan as kode1, a.kd_sub_kegiatan as rek, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti]
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=?
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND left(a.kd_skpd,17)=left(?,17)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan

                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,2) as kode,kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,2) as kode1, LEFT(a.kd_rek6,2) as rek,  nm_rek2 as uraian, SUM(nilai) as nilai,
                        '' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek2 b ON LEFT(a.kd_rek6,2)=b.kd_rek2
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,17)=left(?,17)
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,2), nm_rek2
                        UNION ALL
                        SELECT 3 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,4) as kode,kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,4) as kode1, LEFT(a.kd_rek6,4) as rek,  nm_rek3 as uraian, SUM(nilai) as nilai,
                        '' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek3 b ON LEFT(a.kd_rek6,4)=b.kd_rek3
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,17)=left(?,17)
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,4), nm_rek3
                        UNION ALL
                        SELECT 4 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,6) as kode,kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,6) as kode1, LEFT(a.kd_rek6,6) as rek,  nm_rek4 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek4 b ON LEFT(a.kd_rek6,6)=b.kd_rek4
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,17)=left(?,17)
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,6), nm_rek4
                        UNION ALL
                        SELECT 5 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,8) as kode,kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,8) as kode1, LEFT(a.kd_rek6,8) as rek,  nm_rek5 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek5 b ON LEFT(a.kd_rek6,8)=b.kd_rek5
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,17)=left(?,17)
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,8), nm_rek5
                        UNION ALL

                        SELECT 6 as urut, kd_sub_kegiatan+'.'+kd_rek6 as kode,kd_sub_kegiatan+'.'+kd_rek6 as kode1, kd_rek6 as rek,  nm_rek6 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti]
                        FROM trlpj a
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE no_lpj=? AND left(a.kd_skpd,17)=left(?,17)
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, kd_rek6, nm_rek6
                        UNION ALL

                        SELECT 7 as urut, a.kd_sub_kegiatan+'.'+a.kd_rek6+'.1' as kode,a.kd_sub_kegiatan+'.'+a.kd_rek6 as kode1,'' as rek, c.ket+' \\ No BKU: '+a.no_bukti as uraian, sum(a.nilai) as nilai,
                        c.tgl_bukti,a.no_bukti
                        FROM trlpj a
                        INNER JOIN trhlpj b ON a.no_lpj=b.no_lpj AND a.kd_bp_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        WHERE a.no_lpj=?
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        AND left(a.kd_skpd,17)=left(?,17)
                        AND a.kd_sub_kegiatan=?
                        GROUP BY a.kd_sub_kegiatan, a.kd_rek6,nm_rek6,a.no_bukti, ket,tgl_bukti
                        ORDER BY kode,tgl_bukti,no_bukti", [$no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan]);
        }

        if ($pilihan == '2') {
            $kd_sub_kegiatan = $kd_sub_kegiatan;
        } else {
            $kd_sub_kegiatan = '';
        }

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'dpa' => DB::table('trhrka')
                ->select('no_dpa', 'tgl_dpa')
                ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => status_anggaran()])
                ->first(),
            'jumlah_belanja' => DB::table('trlpj as a')
                ->join('trhlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_bp_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("sum(nilai) as nilai,b.tgl_lpj")
                ->whereRaw("a.no_lpj=? and b.jenis=? and left(a.kd_bp_skpd,17)=left(?,17)", [$no_lpj, '1', $kd_skpd])
                ->groupBy('b.tgl_lpj')
                ->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $bendahara])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first(),
            'lpj' => DB::table('trhlpj')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->first(),
            'pilihan' => $pilihan,
            'data_lpj' => $data_lpj,
            'persediaan' => DB::table('ms_up')->selectRaw("SUM(nilai_up) as nilai")->where(['kd_skpd' => $kd_skpd])->first(),
            'kegiatan' => $kd_sub_kegiatan
        ];

        $view = view('skpd.lpj.skpd_dan_unit.cetak.rincian')->with($data);

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

    // INPUT LPJ UP/GU SKPD / UNIT
    public function indexSkpdAtauUnit()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'ttd1' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->get(),
            'ttd2' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['PA', 'KPA'])
                ->get(),
        ];

        return view('skpd.lpj.skpd_atau_unit.index')->with($data);
    }

    public function loadSkpdAtauUnit()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhlpj_unit as a')
            ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
            ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
            ->orderBy('a.tgl_lpj')
            ->orderBy('a.no_lpj')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("lpj.skpd_atau_unit.edit", Crypt::encrypt($row->no_lpj)) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->status == '1' || $row->status == '2') {
                $btn .= "";
            } else {
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_lpj . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_lpj . '\',\'' . $row->jenis . '\',\'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak LPJ" style="margin-right:4px"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahSkpdAtauUnit()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'tanggal_awal' => collect(DB::select("SELECT DATEADD(DAY,1,MAX(tgl_akhir)) as tanggal_awal FROM trhlpj_unit WHERE jenis=? AND kd_skpd = ?", ['1', $kd_skpd]))->first()->tanggal_awal
        ];

        return view('skpd.lpj.skpd_atau_unit.create')->with($data);
    }

    public function detailSkpdAtauUnit(Request $request)
    {
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $kd_skpd = $request->kd_skpd;

        // $data1 = DB::table('trdtransout as a')
        //     ->join('trhtransout as b', function ($join) {
        //         $join->on('a.no_bukti', '=', 'b.no_bukti');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->selectRaw("b.kd_skpd,b.tgl_bukti,a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.kd_rek6,a.nm_rek6,a.no_bukti,a.nilai,a.kd_skpd as kd_skpd1")
        //     ->whereRaw("(a.no_bukti+a.kd_sub_kegiatan+a.kd_rek6+a.kd_skpd) NOT IN(SELECT (no_bukti+kd_sub_kegiatan+kd_rek6+kd_skpd) FROM trlpj) AND b.panjar not in ('3','5') AND b.tgl_bukti >= ? and b.tgl_bukti <= ? and b.jns_spp='1' and b.kd_skpd=?", [$tgl_awal, $tgl_akhir, $kd_skpd]);

        // $data2 = DB::table('trdtransout as a')
        //     ->join('trhtransout as b', function ($join) {
        //         $join->on('a.no_bukti', '=', 'b.no_bukti');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->selectRaw("b.kd_skpd,b.tgl_bukti,a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.kd_rek6,a.nm_rek6,a.no_bukti,a.nilai,a.kd_skpd as kd_skpd1")
        //     ->whereRaw("(a.no_bukti+a.kd_sub_kegiatan+a.kd_rek6+a.kd_skpd) NOT IN(SELECT (no_bukti+kd_sub_kegiatan+kd_rek6+kd_skpd) FROM trlpj) AND b.panjar in ('3','5') AND b.tgl_bukti >= ? and b.tgl_bukti <= ? and b.jns_spp='1' and b.kd_skpd=?", [$tgl_awal, $tgl_akhir, $kd_skpd])
        //     ->unionAll($data1);

        // $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
        //     ->mergeBindings($data2)
        //     ->orderByRaw("kd_skpd,tgl_bukti,kd_sub_kegiatan, kd_rek6, cast(no_bukti as int)")
        //     ->get();

        $data = DB::select("SELECT * FROM (SELECT b.kd_skpd,b.tgl_bukti,a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.kd_rek6,a.nm_rek6,a.no_bukti,a.nilai,a.kd_skpd as kd_skpd1 FROM trdtransout a inner join trhtransout b on
                   a.no_bukti=b.no_bukti AND a.kd_skpd = b.kd_skpd WHERE (a.no_bukti+a.kd_sub_kegiatan+a.kd_rek6+a.kd_skpd) NOT IN(SELECT (no_bukti+kd_sub_kegiatan+kd_rek6+kd_skpd) FROM trlpj) AND b.panjar not in ('3','5') AND b.tgl_bukti >= ? and b.tgl_bukti <= ? and b.jns_spp='1' and b.kd_skpd=?

                   UNION ALL

                   SELECT b.kd_skpd,b.tgl_bukti,a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.kd_rek6,a.nm_rek6,a.no_bukti,a.nilai,a.kd_skpd as kd_skpd1 FROM trdtransout a inner join trhtransout b on
                   a.no_bukti=b.no_bukti AND a.kd_skpd = b.kd_skpd WHERE (a.no_bukti+a.kd_sub_kegiatan+a.kd_rek6+a.kd_skpd) NOT IN(SELECT (no_bukti+kd_sub_kegiatan+kd_rek6+kd_skpd) FROM trlpj) AND b.panjar in ('3','5') AND b.tgl_bukti >= ? and b.tgl_bukti <= ? and b.jns_spp='1' and b.kd_skpd=?
                   )z
                   ORDER BY  kd_skpd,tgl_bukti,kd_sub_kegiatan, kd_rek6, cast(no_bukti as int)", [$tgl_awal, $tgl_akhir, $kd_skpd, $tgl_awal, $tgl_akhir, $kd_skpd]);

        return response()->json($data);
    }

    public function subKegiatanSkpdAtauUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT a.kd_sub_kegiatan, c.nm_sub_kegiatan
        from trlpj a
        INNER JOIN trhlpj_unit b ON a.no_lpj_unit=b.no_lpj AND a.kd_skpd=b.kd_skpd
        LEFT JOIN trskpd c ON a.kd_sub_kegiatan=c.kd_sub_kegiatan
        WHERE a.no_lpj_unit = ? AND a.kd_skpd=?
        GROUP BY a.kd_sub_kegiatan,c.nm_sub_kegiatan
        ORDER BY a.kd_sub_kegiatan", [$no_lpj, $kd_skpd]);

        return response()->json($data);
    }

    public function simpanSkpdAtauUnit(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_lpj = $data['no_lpj'] . "/LPJ/UPGU/" . $data['kd_skpd'] . "/" . tahun_anggaran();

            $cek_lpj = DB::table('trhlpj_unit')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->count();
            if ($cek_lpj > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            $data['detail_lpj'] = json_decode($data['detail_lpj'], true);

            $rincian_data = $data['detail_lpj'];

            DB::table('trhlpj_unit')
                ->insert([
                    'no_lpj' => $no_lpj,
                    'tgl_lpj' => $data['tgl_lpj'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'tgl_awal' => $data['tgl_awal'],
                    'tgl_akhir' => $data['tgl_akhir'],
                    'status' => '0',
                    'jenis' => '1',
                ]);

            DB::table('trlpj')
                ->where(['no_lpj_unit' => $no_lpj, 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            // if (isset($data['detail_lpj'])) {
            //     DB::table('trlpj')
            //         ->insert(array_map(function ($value) use ($no_lpj, $data) {
            //             return [
            //                 'no_lpj' => '',
            //                 'kd_skpd' => $value['kd_skpd'],
            //                 'no_bukti' => $value['no_bukti'],
            //                 'tgl_lpj' => $data['tgl_lpj'],
            //                 'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
            //                 'kd_rek6' => $value['kdrek6'],
            //                 'nm_rek6' => $value['nmrek6'],
            //                 'nilai' => $value['nilai'],
            //                 'kd_bp_skpd' => '',
            //                 'no_lpj_unit' => $no_lpj,
            //             ];
            //         }, $data['detail_lpj']));
            // }

            $tgl_lpj = $data['tgl_lpj'];

            if (isset($rincian_data)) {
                foreach ($rincian_data as $data => $value) {
                    $data = [
                        'no_lpj' => '',
                        'kd_skpd' => $rincian_data[$data]['kd_skpd'],
                        'no_bukti' => $rincian_data[$data]['no_bukti'],
                        'tgl_lpj' => $tgl_lpj,
                        'kd_sub_kegiatan' => $rincian_data[$data]['kd_sub_kegiatan'],
                        'kd_rek6' => $rincian_data[$data]['kdrek6'],
                        'nm_rek6' => $rincian_data[$data]['nmrek6'],
                        'nilai' => $rincian_data[$data]['nilai'],
                        'kd_bp_skpd' => '',
                        'no_lpj_unit' => $no_lpj,
                    ];
                    DB::table('trlpj')->insert($data);
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

    public function editSkpdAtauUnit($no_lpj)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_lpj = Crypt::decrypt($no_lpj);
        $arr = explode("/", $no_lpj);

        $data = [
            'nomor' => $arr[0],
            'lpj' => DB::table('trhlpj_unit as a')
                ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
                ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '1', 'a.no_lpj' => $no_lpj])
                ->first(),
            'detail_lpj' => DB::table('trhlpj_unit as a')
                ->join('trlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj_unit');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->select('b.*')
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
                ->get(),
            'total_detail' => DB::table('trhlpj_unit as a')
                ->join('trlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj_unit');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("SUM(b.nilai) as nilai")
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
                ->first(),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
        ];

        return view('skpd.lpj.skpd_atau_unit.edit')->with($data);
    }

    public function updateSkpdAtauUnit(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_lpj = $data['no_lpj'] . "/LPJ/UPGU/" . $data['kd_skpd'] . "/" . tahun_anggaran();

            $cek_terima = DB::table('trhlpj_unit')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $data['kd_skpd']])->count();
            if ($cek_terima > 0 && $no_lpj != $data['no_lpj_simpan']) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhlpj_unit')
                ->where(['no_lpj' => $data['no_lpj_simpan'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            DB::table('trhlpj_unit')
                ->insert([
                    'no_lpj' => $no_lpj,
                    'tgl_lpj' => $data['tgl_lpj'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'tgl_awal' => $data['tgl_awal'],
                    'tgl_akhir' => $data['tgl_akhir'],
                    'status' => '0',
                    'jenis' => '1',
                ]);

            DB::table('trlpj')
                ->where(['no_lpj_unit' => $data['no_lpj_simpan'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            // if (isset($data['detail_lpj'])) {
            //     DB::table('trlpj')
            //         ->insert(array_map(function ($value) use ($no_lpj, $data) {
            //             return [
            //                 'no_lpj' => '',
            //                 'kd_skpd' => $value['kd_skpd'],
            //                 'no_bukti' => $value['no_bukti'],
            //                 'tgl_lpj' => $data['tgl_lpj'],
            //                 'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
            //                 'kd_rek6' => $value['kdrek6'],
            //                 'nm_rek6' => $value['nmrek6'],
            //                 'nilai' => $value['nilai'],
            //                 'kd_bp_skpd' => '',
            //                 'no_lpj_unit' => $no_lpj,
            //             ];
            //         }, $data['detail_lpj']));
            // }

            $data['detail_lpj'] = json_decode($data['detail_lpj'], true);

            $rincian_data = $data['detail_lpj'];

            $tgl_lpj = $data['tgl_lpj'];

            if (isset($rincian_data)) {
                foreach ($rincian_data as $data => $value) {
                    $data = [
                        'no_lpj' => '',
                        'kd_skpd' => $rincian_data[$data]['kd_skpd'],
                        'no_bukti' => $rincian_data[$data]['no_bukti'],
                        'tgl_lpj' => $tgl_lpj,
                        'kd_sub_kegiatan' => $rincian_data[$data]['kd_sub_kegiatan'],
                        'kd_rek6' => $rincian_data[$data]['kdrek6'],
                        'nm_rek6' => $rincian_data[$data]['nmrek6'],
                        'nilai' => $rincian_data[$data]['nilai'],
                        'kd_bp_skpd' => '',
                        'no_lpj_unit' => $no_lpj,
                    ];
                    DB::table('trlpj')->insert($data);
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

    public function hapusSkpdAtauUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trlpj')
                ->where([
                    'no_lpj_unit' => $no_lpj,
                    'kd_skpd' => $kd_skpd
                ])
                ->delete();

            DB::table('trhlpj_unit')
                ->where([
                    'no_lpj' => $no_lpj,
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

    public function sptbSkpdAtauUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;
        $pa_kpa = $request->pa_kpa;
        $jenis_print = $request->jenis_print;

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'dpa' => DB::table('trhrka')
                ->select('no_dpa', 'tgl_dpa')
                ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => status_anggaran()])
                ->first(),
            'jumlah_belanja' => collect(DB::select("SELECT sum(nilai) [nilai],b.tgl_lpj from trlpj a join trhlpj_unit b on a.no_lpj_unit=b.no_lpj and a.kd_skpd=b.kd_skpd
                where a.no_lpj_unit=? and b.jenis=?  and  a.kd_skpd=? group by b.tgl_lpj", [$no_lpj, '1', $kd_skpd]))->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'daerah' => DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first()
        ];

        $view = view('skpd.lpj.skpd_atau_unit.cetak.sptb')->with($data);

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

    public function rincianSkpdAtauUnit(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;
        $bendahara = $request->bendahara;
        $pa_kpa = $request->pa_kpa;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $pilihan = $request->pilihan;
        $jenis_print = $request->jenis_print;
        $margin_kiri = $request->margin_kiri;
        $margin_kanan = $request->margin_kanan;
        $margin_atas = $request->margin_atas;
        $margin_bawah = $request->margin_bawah;
        $status_anggaran = status_anggaran();

        if ($pilihan == '0') {
            $data_lpj = DB::select("SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,7) as kode, b.nm_program as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_program,nm_program,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_program,nm_program,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,7) =b.kd_program AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj_unit=? AND a.kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,7), b.nm_program
                        UNION ALL

                        SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,12) as kode, b.nm_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_kegiatan,nm_kegiatan,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_kegiatan,nm_kegiatan,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,12) =b.kd_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj_unit=? AND a.kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,12), b.nm_kegiatan

                        UNION ALL
                        SELECT 2 as urut, a.kd_sub_kegiatan as kode, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE no_lpj_unit=? AND a.kd_skpd=? and b.jns_ang=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan
                        UNION ALL
                        SELECT 3 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,2) as kode, b.nm_rek2 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek2 b ON LEFT(a.kd_rek6,2)=b.kd_rek2
                        WHERE no_lpj_unit=? AND a.kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,2), b.nm_rek2
                        UNION ALL
                        SELECT 4 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,4) as kode, b.nm_rek3 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek3 b ON LEFT(a.kd_rek6,4)=b.kd_rek3
                        WHERE no_lpj_unit=? AND a.kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,4), b.nm_rek3
                        UNION ALL

                        SELECT 5 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,6) as kode, b.nm_rek4 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek4 b ON LEFT(a.kd_rek6,6)=b.kd_rek4
                        WHERE no_lpj_unit=? AND a.kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,6), b.nm_rek4
                        UNION ALL

                        SELECT 6 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,8) as kode, b.nm_rek5 as uraian, SUM(nilai) as nilai FROM trlpj a
                        INNER JOIN ms_rek5 b ON LEFT(a.kd_rek6,8)=b.kd_rek5
                        WHERE no_lpj_unit=? AND a.kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,8), b.nm_rek5
                        UNION ALL
                        SELECT 7 as urut, kd_sub_kegiatan+'.'+kd_rek6 as kode, nm_rek6 as uraian, SUM(nilai) as nilai FROM trlpj
                        WHERE no_lpj_unit=? AND kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY kd_sub_kegiatan, kd_rek6, nm_rek6
                        ORDER BY kode", [$status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $status_anggaran, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $kd_skpd]);
        } elseif ($pilihan == '1') {
            $data_lpj = DB::select("SELECT 1 as urut, LEFT(a.kd_sub_kegiatan,7) as kode, b.nm_program as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_program,nm_program,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_program,nm_program,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,7) =b.kd_program AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj=? AND a.kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_bp_skpd=?
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,7), b.nm_program
                        UNION ALL
                        SELECT 2 as urut, LEFT(a.kd_sub_kegiatan,12) as kode, b.nm_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN (SELECT DISTINCT kd_kegiatan,nm_kegiatan,kd_skpd FROM trskpd where jns_ang=? GROUP BY kd_kegiatan,nm_kegiatan,kd_skpd)b
                        ON LEFT(a.kd_sub_kegiatan,12) =b.kd_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE a.no_lpj_unit=? AND a.kd_skpd=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY LEFT(a.kd_sub_kegiatan,12), b.nm_kegiatan
                        UNION ALL
                        SELECT 3 as urut, a.kd_sub_kegiatan as kode, b.nm_sub_kegiatan as uraian, SUM(a.nilai) as nilai
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        WHERE no_lpj_unit=? AND a.kd_skpd=? and b.jns_ang=?
                        AND no_bukti IN (SELECT no_bukti FROM trhtransout WHERE kd_skpd=?
                        --AND (panjar NOT IN ('3') or panjar IS NULL)
                        AND jns_spp IN ('1','2','3'))
                        GROUP BY a.kd_sub_kegiatan, b.nm_sub_kegiatan
                        ORDER BY kode", [$status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $status_anggaran, $no_lpj, $kd_skpd, $kd_skpd, $no_lpj, $kd_skpd, $status_anggaran, $kd_skpd]);
        } elseif ($pilihan == '2') {
            $data_lpj = DB::select("SELECT 1 as urut ,kode,rek,uraian,sum(nilai)nilai, [tgl_bukti],[no_bukti] from (
                         SELECT a.kd_sub_kegiatan as kode, a.kd_sub_kegiatan as rek, b.nm_kegiatan as uraian, a.nilai
                         ,'' [tgl_bukti],0 [no_bukti],jns_ang
                        FROM trlpj a LEFT JOIN trskpd b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                      WHERE no_lpj_unit=? AND a.kd_skpd=? and b.jns_ang=?
                        AND a.kd_sub_kegiatan=?
                        ) z
                        group by  kode,rek,uraian,[tgl_bukti],[no_bukti],jns_ang
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,2) as kode, LEFT(a.kd_rek6,2) as rek,  nm_rek2 as uraian, SUM(nilai) as nilai,
                        '' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek2 b ON LEFT(a.kd_rek6,2)=b.kd_rek2
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj_unit=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,2), nm_rek2
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,4) as kode, LEFT(a.kd_rek6,4) as rek,  nm_rek3 as uraian, SUM(nilai) as nilai,
                        '' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek3 b ON LEFT(a.kd_rek6,4)=b.kd_rek3
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj_unit=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,4), nm_rek3
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,6) as kode, LEFT(a.kd_rek6,6) as rek,  nm_rek4 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek4 b ON LEFT(a.kd_rek6,6)=b.kd_rek4
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj_unit=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,6), nm_rek4
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+LEFT(a.kd_rek6,8) as kode, LEFT(a.kd_rek6,8) as rek,  nm_rek5 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti] FROM trlpj a
                        INNER JOIN ms_rek5 b ON LEFT(a.kd_rek6,8)=b.kd_rek5
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj_unit=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, LEFT(a.kd_rek6,8), nm_rek5
                        UNION ALL
                        SELECT 2 as urut, kd_sub_kegiatan+'.'+kd_rek6 as kode, kd_rek6 as rek,  nm_rek6 as uraian, SUM(nilai) as nilai
                        ,'' [tgl_bukti],0 [no_bukti]
                        FROM trlpj a
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE no_lpj_unit=? AND a.kd_skpd=?
                        AND kd_sub_kegiatan=?
                        GROUP BY kd_sub_kegiatan, kd_rek6, nm_rek6
                        UNION ALL
                        SELECT 3 as urut, a.kd_sub_kegiatan+'.'+a.kd_rek6+'.1' as kode,'' as rek, c.ket+' \\ No BKU: '+a.no_bukti as uraian, sum(a.nilai) as nilai,
                        c.tgl_bukti,a.no_bukti
                        FROM trlpj a
                        INNER JOIN trhlpj_unit b ON a.no_lpj_unit=b.no_lpj AND a.kd_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        -- AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE a.no_lpj_unit=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY a.kd_sub_kegiatan, a.kd_rek6,nm_rek6,a.no_bukti, ket,tgl_bukti
                        ORDER BY kode,tgl_bukti,no_bukti", [$no_lpj, $kd_skpd, $status_anggaran, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan, $no_lpj, $kd_skpd, $kd_sub_kegiatan]);
        }

        if ($pilihan == '2') {
            $kd_sub_kegiatan = $kd_sub_kegiatan;
        } else {
            $kd_sub_kegiatan = '';
        }

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'dpa' => DB::table('trhrka')
                ->select('no_dpa', 'tgl_dpa')
                ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => status_anggaran()])
                ->first(),
            'jumlah_belanja' => DB::table('trlpj as a')
                ->join('trhlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_bp_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("sum(nilai) as nilai,b.tgl_lpj")
                ->whereRaw("a.no_lpj=? and b.jenis=? and left(a.kd_bp_skpd,17)=left(?,17)", [$no_lpj, '1', $kd_skpd])
                ->groupBy('b.tgl_lpj')
                ->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $bendahara])->whereIn('kode', ['BK', 'BPP'])->first(),
            'daerah' => DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first(),
            'lpj' => DB::table('trhlpj_unit')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->first(),
            'pilihan' => $pilihan,
            'data_lpj' => $data_lpj,
            'persediaan' => DB::table('ms_up')->selectRaw("SUM(nilai_up_unit) as nilai")->where(['kd_skpd' => $kd_skpd])->first(),
            'kegiatan' => $kd_sub_kegiatan
        ];

        $view = view('skpd.lpj.skpd_atau_unit.cetak.rincian')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setOption('page-width', 215)
                ->setOption('page-height', 330)
                ->setOption('margin-left', $margin_kiri)
                ->setOption('margin-right', $margin_kanan)
                ->setOption('margin-top', $margin_atas)
                ->setOption('margin-bottom', $margin_bawah);
            return $pdf->stream('laporan.pdf');
        } else {
            return $view;
        }
    }

    // VALIDASI LPJ UP/GU UNIT
    public function indexValidasiLpj()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'ttd1' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->get(),
            'ttd2' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['PA', 'KPA'])
                ->get(),
        ];

        return view('skpd.lpj.validasi_lpj.index')->with($data);
    }


    public function loadValidasiLpj()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhlpj_unit as a')
            ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
            ->where(['a.jenis' => '1'])
            ->whereRaw("left(kd_skpd,17)=left(?,17)", [$kd_skpd])
            ->orderBy('a.tgl_lpj')
            ->orderBy('a.no_lpj')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("lpj.validasi.edit", ['no_lpj' => Crypt::encrypt($row->no_lpj), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-primary btn-sm"  style="margin-right:4px"><i class="uil-eye"></i></a>';
            if ($row->status == '2') {
                $btn .= "";
                $btn .= '<a href="javascript:void(0);" style="margin-right:4px" onclick="cetak(\'' . $row->no_lpj . '\',\'' . $row->jenis . '\',\'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm"><i class="uil-print"></i></a>';
            } else {
                $btn = '<a href="' . route("lpj.validasi.edit", ['no_lpj' => Crypt::encrypt($row->no_lpj), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-primary btn-sm"  style="margin-right:4px"><i class="uil-eye"></i></a>';
                $btn .= '<a href="javascript:void(0);" style="margin-right:4px" onclick="cetak(\'' . $row->no_lpj . '\',\'' . $row->jenis . '\',\'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm"><i class="uil-print"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function editValidasiLpj($no_lpj, $kd_skpd)
    {
        $no_lpj = Crypt::decrypt($no_lpj);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $data = [
            'lpj' => DB::table('trhlpj_unit as a')
                ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
                ->where(['a.jenis' => '1', 'a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd])
                ->first(),
            'detail_lpj' => DB::table('trhlpj_unit as a')
                ->join('trlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj_unit');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->select('b.*')
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
                ->get(),
            'total_detail' => DB::table('trhlpj_unit as a')
                ->join('trlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj_unit');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("SUM(b.nilai) as nilai")
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
                ->first(),
        ];

        return view('skpd.lpj.validasi_lpj.edit')->with($data);
    }

    public function showValidasiLpj($no_lpj, $kd_skpd)
    {
        $no_lpj = Crypt::decrypt($no_lpj);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $data = [
            'lpj' => DB::table('trhlpj as a')
                ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
                ->where(['a.jenis' => '1', 'a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd])
                ->first(),
            'detail_lpj' => DB::table('trhlpj as a')
                ->join('trlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->select('b.*')
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
                ->get(),
            'total_detail' => DB::table('trhlpj as a')
                ->join('trlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("SUM(b.nilai) as nilai")
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '1'])
                ->first(),
        ];
        //dd($data['detail_lpj']);

        return view('skpd.lpj.validasi_lpj.edit')->with($data);
    }

    public function setujuValidasiLpj(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;
        $nama = Auth::user()->nama;

        DB::beginTransaction();
        try {
            DB::table('trhlpj_unit')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->update([
                    'status' => '1',
                    'status_validasi' => '1',
                    'username'  => $nama,
                    'last_update' => date('Y-m-d H:i:s')
                ]);

            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $th) {
            DB::rollBack();
            Log::error('Exception caught: ' . $th->getMessage(), [
                'exception' => get_class($th),  // Type of exception
                'file' => $th->getFile(),       // File where it occurred
                'line' => $th->getLine(),       // Line number where it occurred
            ]);
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function batalSetujuValidasiLpj(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            $cek_global = DB::table('trlpj')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->where(function ($query) {
                    $query->where('no_lpj', '<>', '')->orWhere('kd_bp_skpd', '<>', '');
                })
                ->count();

            if ($cek_global > 0) {
                DB::rollBack();
                return response()->json([
                    'message' => '2'
                ]);
            }
            DB::table('trhlpj_unit')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->update([
                    'status' => '0',
                    'status_validasi' => '0'
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

    // INPUT LPJ TU
    public function indexLpjTu()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'ttd1' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->get(),
            'ttd2' => DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['PA', 'KPA'])
                ->get(),
        ];

        return view('skpd.lpj.lpj_tu.index')->with($data);
    }

    public function loadLpjTu()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhlpj_tu as a')
            ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
            ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '3'])
            ->orderBy('a.tgl_lpj')
            ->orderBy('a.no_lpj')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if ($row->status == '1') {
                $btn = "";
            } else {
                $btn = '<a href="' . route("lpj_tu.edit", ['no_lpj' => Crypt::encrypt($row->no_lpj), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_lpj . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_lpj . '\',\'' . $row->jenis . '\',\'' . $row->kd_skpd . '\',\'' . $row->no_sp2d . '\');" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak LPJ" style="margin-right:4px"><i class="uil-print"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahLpjTu()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'daftar_sp2d' => DB::table('trhsp2d')
                ->selectRaw("no_sp2d,tgl_sp2d")
                ->where(['jns_spp' => '3', 'status' => '1'])
                ->whereRaw("no_sp2d NOT IN (SELECT ISNULL(no_sp2d,'') FROM trhlpj_tu where no_sp2d <> '731/TU/2022')")
                ->where(['kd_skpd' => $kd_skpd])
                ->get(),
        ];
        // dd($data['daftar_sp2d']);
        return view('skpd.lpj.lpj_tu.create')->with($data);
    }

    public function detailLpjTu(Request $request)
    {
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = Auth::user()->kd_skpd;
        $cek = substr($kd_skpd, 8, 2);

        $data = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.kd_sub_kegiatan,a.nm_sub_kegiatan,a.kd_rek6,a.nm_rek6,a.nilai, a.no_bukti,a.kd_skpd as kd_bp_skpd")
            ->where(['a.no_sp2d' => $no_sp2d])
            ->where(function ($query) use ($cek, $kd_skpd) {
                if ($cek == '00') {
                    $query->whereRaw("left(b.kd_skpd,7)=left(?,7)", [$kd_skpd]);
                } else {
                    $query->where('b.kd_skpd', $kd_skpd);
                }
            })
            ->orderBy('a.no_bukti')
            ->orderBy('a.kd_sub_kegiatan')
            ->orderBy('a.kd_rek6')
            ->get();
        return response()->json($data);
    }

    public function simpanLpjTu(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_lpj = $data['no_lpj'] . "/LPJ/TU/" . $data['kd_skpd'] . "/" . tahun_anggaran();

            $cek_lpj = DB::table('trhlpj_tu')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->count();
            if ($cek_lpj > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhlpj_tu')
                ->insert([
                    'no_lpj' => $no_lpj,
                    'tgl_lpj' => $data['tgl_lpj'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'tgl_awal' => $data['tgl_sp2d'],
                    'no_sp2d' => $data['no_sp2d'],
                    'status' => '0',
                    'jenis' => '3',
                ]);

            DB::table('trlpj_tu')
                ->where(['no_lpj' => $no_lpj, 'kd_bp_skpd' => $data['kd_skpd']])
                ->delete();

            if (isset($data['detail_lpj'])) {
                DB::table('trlpj_tu')
                    ->insert(array_map(function ($value) use ($no_lpj, $data) {
                        return [
                            'no_lpj' => $no_lpj,
                            'kd_skpd' => $data['kd_skpd'],
                            'no_bukti' => $value['no_bukti'],
                            'tgl_lpj' => $data['tgl_lpj'],
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            'keterangan' => $data['keterangan'],
                            'kd_rek6' => $value['kd_rek6'],
                            'nm_rek6' => $value['nm_rek6'],
                            'nilai' => $value['nilai'],
                            'kd_bp_skpd' => $value['kd_skpd'],
                            'no_lpj_unit' => $no_lpj,
                        ];
                    }, $data['detail_lpj']));
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

    public function editLpjTu($no_lpj, $kd_skpd)
    {
        $no_lpj = Crypt::decrypt($no_lpj);
        $kd_skpd = Crypt::decrypt($kd_skpd);
        $arr = explode("/", $no_lpj);

        $data = [
            'nomor' => $arr[0],
            'lpj' => DB::table('trhlpj_tu as a')
                ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
                ->where(['a.kd_skpd' => $kd_skpd, 'a.jenis' => '3', 'a.no_lpj' => $no_lpj])
                ->first(),
            'detail_lpj' => DB::table('trhlpj_tu as a')
                ->join('trlpj_tu as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->select('b.*')
                ->where(['a.no_lpj' => $no_lpj, 'a.kd_skpd' => $kd_skpd, 'a.jenis' => '3'])
                ->get(),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
        ];

        return view('skpd.lpj.lpj_tu.edit')->with($data);
    }

    public function updateLpjTu(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_lpj = $data['no_lpj'] . "/LPJ/TU/" . $data['kd_skpd'] . "/" . tahun_anggaran();

            $cek = DB::table('trhlpj_tu')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $data['kd_skpd']])
                ->count();

            if ($cek > 0 && $no_lpj != $data['no_lpj_simpan']) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhlpj_tu')
                ->where(['no_lpj' => $data['no_lpj_simpan'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            DB::table('trhlpj_tu')
                ->insert([
                    'no_lpj' => $no_lpj,
                    'tgl_lpj' => $data['tgl_lpj'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'tgl_awal' => $data['tgl_sp2d'],
                    'no_sp2d' => $data['no_sp2d'],
                    'status' => '0',
                    'jenis' => '3',
                ]);

            DB::table('trlpj_tu')
                ->where(['no_lpj' => $data['no_lpj_simpan'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            if (isset($data['detail_lpj'])) {
                DB::table('trlpj_tu')
                    ->insert(array_map(function ($value) use ($no_lpj, $data) {
                        return [
                            'no_lpj' => $no_lpj,
                            'kd_skpd' => $data['kd_skpd'],
                            'no_bukti' => $value['no_bukti'],
                            'tgl_lpj' => $data['tgl_lpj'],
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            'keterangan' => $data['keterangan'],
                            'kd_rek6' => $value['kd_rek6'],
                            'nm_rek6' => $value['nm_rek6'],
                            'nilai' => $value['nilai'],
                            'kd_bp_skpd' => $value['kd_skpd'],
                            'no_lpj_unit' => $no_lpj,
                        ];
                    }, $data['detail_lpj']));
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

    public function hapusLpjTu(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trlpj_tu')
                ->where([
                    'no_lpj' => $no_lpj,
                    'kd_skpd' => $kd_skpd
                ])
                ->delete();

            DB::table('trhlpj_tu')
                ->where([
                    'no_lpj' => $no_lpj,
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

    public function sptbLpjTu(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $no_sp2d = $request->no_sp2d;
        $tgl_ttd = $request->tgl_ttd;
        $kd_skpd = $request->kd_skpd;
        $bendahara = $request->bendahara;
        $pa_kpa = $request->pa_kpa;
        $jenis_print = $request->jenis_print;

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'tgl_ttd' => $tgl_ttd,
            'dpa' => DB::table('trhrka')
                ->select('no_dpa', 'tgl_dpa')
                ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => status_anggaran()])
                ->first(),
            'jumlah_belanja' => collect(DB::select("SELECT SUM(nilai) AS nilai
                        FROM
                            trlpj_tu c
                        INNER JOIN trhlpj_tu d ON c.no_lpj = d.no_lpj
                        WHERE
                        c.no_lpj =?", [$no_lpj]))->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'daerah' => DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first()
        ];

        $view = view('skpd.lpj.lpj_tu.cetak.sptb')->with($data);

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

    public function rincianLpjTu(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $no_sp2d = $request->no_sp2d;
        $tgl_ttd = $request->tgl_ttd;
        $kd_skpd = $request->kd_skpd;
        $bendahara = $request->bendahara;
        $pa_kpa = $request->pa_kpa;
        $jenis_print = $request->jenis_print;


        $program = collect(DB::select("SELECT c.kd_program,c.kd_kegiatan,a.kd_sub_kegiatan FROM trdspp a INNER JOIN trhsp2d b ON a.no_spp = b.no_spp join trskpd c on a.kd_sub_kegiatan=c.kd_sub_kegiatan WHERE no_sp2d = ? group by c.kd_program,c.kd_kegiatan,a.kd_sub_kegiatan", [$no_sp2d]))->first();

        $cek = collect(DB::select("SELECT
                         COUNT (*) as tot
                        FROM
                            trlpj_tu c
                        LEFT JOIN trhlpj_tu d ON c.no_lpj = d.no_lpj AND c.kd_skpd=d.kd_skpd
                        WHERE
                        c.no_lpj = ? AND d.kd_skpd=?", [$no_lpj, $kd_skpd]))->first();

        if ($cek->tot == 0) {
            $data_rincian = DB::select("SELECT c.kd_rek6 ,c.nm_rek6,0 as nilai,'' tgl_bukti,'' no_bukti
                FROM trhspp a INNER JOIN trhsp2d b ON a.no_spp = b.no_spp AND a.kd_skpd=b.kd_skpd
                join trdspp c ON a.no_spp = c.no_spp AND a.kd_skpd=c.kd_skpd
                WHERE no_sp2d = ? union all
                        SELECT a.kd_rek6+'.1' as kd_rek6, c.ket+' \\ No BKU: '+a.no_bukti as nm_rek6, sum(a.nilai) as nilai,
                        c.tgl_bukti,a.no_bukti
                        FROM trlpj_tu a
                        INNER JOIN trhlpj_tu b ON a.no_lpj=b.no_lpj AND a.kd_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE a.no_lpj=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY a.kd_sub_kegiatan, a.kd_rek6, nm_rek6,a.nilai,a.no_bukti, ket,tgl_bukti
                        ORDER BY kd_rek6,tgl_bukti,no_bukti", [$no_sp2d, $no_lpj, $kd_skpd, $program->kd_sub_kegiatan]);
        } else {
            $data_rincian = DB::select("SELECT
                        kd_rek6,nm_rek6,SUM(nilai) as nilai,'' tgl_bukti,'' no_bukti
                        FROM
                        trlpj_tu c
                        LEFT JOIN trhlpj_tu d ON c.no_lpj = d.no_lpj AND c.kd_skpd=d.kd_skpd
                        WHERE
                        c.no_lpj = ? AND d.kd_skpd=?
                        GROUP BY kd_rek6,nm_rek6 union all
                        SELECT a.kd_rek6+'.1' as kd_rek6, c.ket+' \\ No BKU: '+a.no_bukti as nm_rek6, sum(a.nilai) as nilai,
                        c.tgl_bukti,a.no_bukti
                        FROM trlpj_tu a
                        INNER JOIN trhlpj_tu b ON a.no_lpj=b.no_lpj AND a.kd_skpd=b.kd_skpd
                        INNER JOIN trhtransout c ON a.no_bukti=c.no_bukti AND a.kd_skpd=c.kd_skpd
                        AND (c.panjar NOT IN('3') or c.panjar IS NULL)
                        WHERE a.no_lpj=? AND a.kd_skpd=?
                        AND a.kd_sub_kegiatan=?
                        GROUP BY a.kd_sub_kegiatan, a.kd_rek6, nm_rek6,a.nilai,a.no_bukti, ket,tgl_bukti
                        ORDER BY kd_rek6,tgl_bukti,no_bukti", [$no_lpj, $kd_skpd, $no_lpj, $kd_skpd, $program->kd_sub_kegiatan]);
        }

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'kd_skpd' => $kd_skpd,
            'tgl_ttd' => $tgl_ttd,
            'dpa' => DB::table('trhrka')
                ->select('no_dpa', 'tgl_dpa')
                ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => status_anggaran()])
                ->first(),
            'jumlah_belanja' => collect(DB::select("SELECT SUM(nilai) AS nilai
                        FROM
                            trlpj_tu c
                        INNER JOIN trhlpj_tu d ON c.no_lpj = d.no_lpj
                        WHERE
                        c.no_lpj =?", [$no_lpj]))->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'daerah' => DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first(),
            'program' => $program,
            'no_sp2d' => $no_sp2d,
            'data_rincian' => $data_rincian,
            'total' => collect(DB::select("SELECT
                        SUM(nilai) nilai
                        FROM
                            trlpj_tu c
                        LEFT JOIN trhlpj_tu d ON c.no_lpj = d.no_lpj
                        WHERE
                        c.no_lpj =?", [$no_lpj]))->first()->nilai,
            'persediaan' => collect(DB::select("SELECT SUM(a.nilai) AS nilai FROM trdspp a LEFT JOIN trhsp2d b ON b.no_spp=a.no_spp
                         WHERE b.kd_skpd=? AND b.jns_spp=3 AND  no_sp2d = ?", [$kd_skpd, $no_sp2d]))->first()->nilai,
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $bendahara])->whereIn('kode', ['BK', 'BPP'])->first(),
        ];

        $view = view('skpd.lpj.lpj_tu.cetak.rincian')->with($data);

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
