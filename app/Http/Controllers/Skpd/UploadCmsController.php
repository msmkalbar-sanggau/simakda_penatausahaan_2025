<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class UploadCmsController extends Controller
{
    public function index()
    {
        $data = [
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.upload_cms.index')->with($data);
    }

    public function loadUpload()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $join1 = DB::table('trdtransout_transfercms as a')
            ->select('a.no_voucher', 'a.kd_skpd', DB::raw("SUM(a.nilai) as bersih"))
            ->where(['a.kd_skpd' => $kd_skpd])
            ->groupBy('a.no_voucher', 'a.kd_skpd');

        $data = DB::table('trhtransout_cmsbank as a')
            ->leftJoin('trdtransout_cmsbank as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoinSub($join1, 'c', function ($join) {
                $join->on('c.no_voucher', '=', 'a.no_voucher');
                $join->on('c.kd_skpd', '=', 'a.kd_skpd');
            })
            ->select('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_upload', 'a.tgl_upload', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'c.bersih')
            ->selectRaw("CASE WHEN a.jns_spp IN('4','6') THEN (SELECT SUM(x.nilai) as tot_pot FROM trspmpot x INNER JOIN trhsp2d b ON x.kd_skpd=b.kd_skpd AND x.no_spm=b.no_spm INNER JOIN trhtransout_cmsbank c ON b.kd_skpd=c.kd_skpd AND b.no_sp2d=c.no_sp2d WHERE c.kd_skpd=? AND c.no_sp2d=a.no_sp2d AND c.jns_spp IN('4','6')) ELSE 0 END as tot_pot", [$kd_skpd])
            ->where(['a.kd_skpd' => $kd_skpd, 'status_upload' => '0'])
            ->groupBy('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_upload', 'a.tgl_upload', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'c.bersih', 'a.jns_spp')
            ->orderBy(DB::raw("CAST(a.no_voucher as int)"))
            ->orderBy('a.kd_skpd')
            ->get();

        return Datatables::of($data)->addIndexColumn()->make(true);
    }

    public function draftUpload()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtransout_cmsbank as a')
            ->leftJoin('trdtransout_cmsbank as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_voucher', '=', 'b.no_voucher');
            })
            ->leftJoin('trdupload_cmsbank as c', function ($join) {
                $join->on('a.kd_skpd', '=', 'c.kd_skpd');
                $join->on('a.no_voucher', '=', 'c.no_voucher');
            })
            ->selectRaw("a.kd_skpd,a.nm_skpd,a.no_tgl,a.no_voucher,a.tgl_voucher,a.no_sp2d,a.ket,a.total,a.status_upload,a.tgl_upload,a.status_validasi,a.tgl_validasi,a.rekening_awal,a.nm_rekening_tujuan,a.rekening_tujuan,a.bank_tujuan,a.ket_tujuan,b.kd_sub_kegiatan,b.nm_sub_kegiatan,c.no_upload,c.no_upload_tgl")
            ->where(['a.kd_skpd' => $kd_skpd, 'a.status_upload' => '1', 'a.status_validasi' => '0'])
            ->groupByRaw("a.kd_skpd,a.nm_skpd,a.no_tgl,a.no_voucher,a.tgl_voucher,a.no_sp2d,a.ket,a.total,a.status_upload,a.tgl_upload,a.status_validasi,a.tgl_validasi,a.rekening_awal,a.nm_rekening_tujuan,a.rekening_tujuan,a.bank_tujuan,a.ket_tujuan,b.kd_sub_kegiatan,b.nm_sub_kegiatan,c.no_upload,c.no_upload_tgl")
            ->orderByRaw("cast(c.no_upload as int),cast(a.no_voucher as int),a.kd_skpd")
            ->get();

        return Datatables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="javascript:void(0);" onclick="lihatDataUpload(\'' . $row->no_upload . '\',\'' . $row->total . '\');" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="batalUpload(\'' . $row->no_upload . '\',\'' . $row->kd_skpd . '\',\'' . $row->no_voucher . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function rekeningTransaksi(Request $request)
    {
        $no_voucher = $request->nomor;
        $kd_skpd = $request->kd_skpd;

        $data = DB::table('trhtransout_cmsbank as a')->join('trdtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_voucher' => $no_voucher, 'a.kd_skpd' => $kd_skpd])->orderBy('b.kd_sub_kegiatan')->orderBy('b.kd_rek6')->select('b.*', DB::raw("'0' as lalu"), DB::raw("'0' as sp2d"), DB::raw("'0' as anggaran"))->get();

        return DataTables::of($data)->addIndexColumn()->make(true);;
        return view('skpd.upload_cms.index');
    }

    public function rekeningPotongan(Request $request)
    {
        $no_voucher = $request->nomor;
        $kd_skpd = $request->kd_skpd;

        $data = DB::table('trdtrmpot_cmsbank as a')->join('trhtrmpot_cmsbank as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['b.no_voucher' => $no_voucher, 'b.kd_skpd' => $kd_skpd])->select('a.*')->get();

        return DataTables::of($data)->addIndexColumn()->make(true);;
        return view('skpd.upload_cms.index');
    }

    public function rekeningTujuan(Request $request)
    {
        $no_voucher = $request->nomor;
        $kd_skpd = $request->kd_skpd;

        $data = DB::table('trhtransout_cmsbank as a')->join('trdtransout_transfercms as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['b.no_voucher' => $no_voucher, 'b.kd_skpd' => $kd_skpd])->groupBy('b.no_voucher', 'b.tgl_voucher', 'b.rekening_awal', 'b.nm_rekening_tujuan', 'b.rekening_tujuan', 'b.bank_tujuan', 'b.kd_skpd', 'b.nilai')->select('b.no_voucher', 'b.tgl_voucher', 'b.rekening_awal', 'b.nm_rekening_tujuan', 'b.rekening_tujuan', 'b.bank_tujuan', 'b.kd_skpd', 'b.nilai', DB::raw("(SELECT SUM(nilai) FROM trdtransout_transfercms WHERE no_voucher=b.no_voucher AND kd_skpd=b.kd_skpd) as total"))->get();

        return DataTables::of($data)->addIndexColumn()->make(true);;
        return view('skpd.upload_cms.index');
    }

    public function loadTransaksi(Request $request)
    {
        $no_voucher = $request->no_voucher;
        $kd_skpd = Auth::user()->kd_skpd;

        $no_bukti = array();
        if (!empty($no_voucher)) {
            foreach ($no_voucher as $voucher) {
                $no_bukti[] = $voucher['no_voucher'];
            }
        } else {
            $no_bukti[] = '';
        }

        $join1 = DB::table('trdtransout_transfercms as a')
            ->select('a.no_voucher', 'a.kd_skpd', DB::raw("SUM(a.nilai) as bersih"))
            ->where(['a.kd_skpd' => $kd_skpd])
            ->groupBy('a.no_voucher', 'a.kd_skpd');

        $data = DB::table('trhtransout_cmsbank as a')
            ->leftJoin('trdtransout_cmsbank as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoinSub($join1, 'c', function ($join) {
                $join->on('c.no_voucher', '=', 'a.no_voucher');
                $join->on('c.kd_skpd', '=', 'a.kd_skpd');
            })
            ->select('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_upload', 'a.tgl_upload', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'c.bersih')
            ->selectRaw("CASE WHEN a.jns_spp IN('4','6') THEN (SELECT SUM(x.nilai) as tot_pot FROM trspmpot x INNER JOIN trhsp2d b ON x.kd_skpd=b.kd_skpd AND x.no_spm=b.no_spm INNER JOIN trhtransout_cmsbank c ON b.kd_skpd=c.kd_skpd AND b.no_sp2d=c.no_sp2d WHERE c.kd_skpd=? AND c.no_sp2d=a.no_sp2d AND c.jns_spp IN('4','6')) ELSE 0 END as tot_pot", [$kd_skpd])
            ->where(['a.kd_skpd' => $kd_skpd, 'status_upload' => '0'])
            ->whereNotIn('a.no_voucher', $no_bukti)
            ->groupBy('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_upload', 'a.tgl_upload', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'c.bersih', 'a.jns_spp')
            ->orderBy(DB::raw("CAST(a.no_voucher as int)"))
            ->orderBy('a.kd_skpd')
            ->get();

        return response()->json($data);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        // $join1 = DB::table('trdtransout_transfercms as a')->select('a.no_voucher', 'a.kd_skpd', DB::raw("SUM(a.nilai) as bersih"))->where(['a.kd_skpd' => $kd_skpd])->groupBy('a.no_voucher', 'a.kd_skpd');
        $data = [
            // 'daftar_transaksi' => DB::table('trhtransout_cmsbank as a')->leftJoin('trdtransout_cmsbank as b', function ($join) {
            //     $join->on('a.no_voucher', '=', 'b.no_voucher');
            //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            // })->leftJoinSub($join1, 'c', function ($join) {
            //     $join->on('c.no_voucher', '=', 'a.no_voucher');
            //     $join->on('c.kd_skpd', '=', 'a.kd_skpd');
            // })->select('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_upload', 'a.tgl_upload', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'c.bersih')->selectRaw("CASE WHEN a.jns_spp IN('4','6') THEN (SELECT SUM(x.nilai) as tot_pot FROM trspmpot x INNER JOIN trhsp2d b ON x.kd_skpd=b.kd_skpd AND x.no_spm=b.no_spm INNER JOIN trhtransout_cmsbank c ON b.kd_skpd=c.kd_skpd AND b.no_sp2d=c.no_sp2d WHERE c.kd_skpd=? AND c.no_sp2d=a.no_sp2d AND c.jns_spp IN('4','6')) ELSE 0 END as tot_pot", [$kd_skpd])->where(['a.kd_skpd' => $kd_skpd, 'status_upload' => '0'])->groupBy('a.kd_skpd', 'a.nm_skpd', 'a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'a.no_sp2d', 'a.ket', 'a.total', 'a.status_upload', 'a.tgl_upload', 'a.status_validasi', 'a.tgl_validasi', 'a.rekening_awal', 'a.nm_rekening_tujuan', 'a.rekening_tujuan', 'a.bank_tujuan', 'a.ket_tujuan', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'c.bersih', 'a.jns_spp')->orderBy(DB::raw("CAST(a.no_voucher as int)"))->orderBy('a.kd_skpd')->get(),
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.upload_cms.create')->with($data);
    }

    public function prosesUpload(Request $request)
    {
        $total_transaksi = $request->total_transaksi;
        $rincian_data = $request->rincian_data;
        $tanggal = date("Y-m-d");
        $kd_skpd = Auth::user()->kd_skpd;
        // return response()->json($rincian_data);
        DB::beginTransaction();
        try {

            // $nomor1 = DB::table('trhupload_cmsbank')->select('no_upload as nomor', DB::raw("'Urut Upload Pengeluaran cms' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd]);

            // $nomor2 = DB::table('trhupload_cmsbank_panjar')->select('no_upload as nomor', DB::raw("'Urut Upload Panjar Bank cms' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->unionAll($nomor1);

            // $nomor3 = DB::table('trhupload_sts_cmsbank')->select('no_upload as nomor', DB::raw("'Urut Upload Penerimaan cms' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->unionAll($nomor2);

            // $nomor = DB::table(DB::raw("({$nomor3->toSql()}) AS sub"))
            //     ->select(DB::raw("case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor"))
            //     ->mergeBindings($nomor3)
            //     ->first();

            $nomor = collect(DB::select("select case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor from (
	select no_upload nomor, 'Urut Upload Pengeluaran cms' ket, kd_skpd from trhupload_cmsbank where kd_skpd=?
    union all
    select no_upload nomor, 'Urut Upload Panjar Bank cms' ket, kd_skpd from trhupload_cmsbank_panjar where kd_skpd=?
    union all
    select no_upload nomor, 'Urut Upload Penerimaan cms' ket, kd_skpd from trhupload_sts_cmsbank where kd_skpd=?
    )
    z WHERE kd_skpd=?", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]))->first();

            // $no_upload1 = DB::table('trdupload_cmsbank as a')->leftJoin('trhupload_cmsbank as b', function ($join) {
            //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            //     $join->on('a.no_upload', '=', 'b.no_upload');
            // })->select('a.no_upload as nomor', 'b.tgl_upload as tanggal', DB::raw("'Urut Upload Pengeluaran cms' as ket"), 'a.kd_skpd')->where(['a.kd_skpd' => $kd_skpd, 'b.tgl_upload' => $tanggal]);

            // $no_upload2 = DB::table('trdupload_cmsbank_panjar as a')->leftJoin('trhupload_cmsbank_panjar as b', function ($join) {
            //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            //     $join->on('a.no_upload', '=', 'b.no_upload');
            // })->select('a.no_upload as nomor', 'b.tgl_upload as tanggal', DB::raw("'Urut Upload Panjar Bank cms' as ket"), 'a.kd_skpd')->where(['a.kd_skpd' => $kd_skpd, 'b.tgl_upload' => $tanggal])->unionAll($no_upload1);

            // $no_upload3 = DB::table('trdupload_sts_cmsbank as a')->leftJoin('trhupload_sts_cmsbank as b', function ($join) {
            //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            //     $join->on('a.no_upload', '=', 'b.no_upload');
            // })->select('a.no_upload as nomor', 'b.tgl_upload as tanggal', DB::raw("'Urut Upload Penerimaan cms' as ket"), 'a.kd_skpd')->where(['a.kd_skpd' => $kd_skpd, 'b.tgl_upload' => $tanggal])->unionAll($no_upload2);

            // $no_upload = DB::table(DB::raw("({$no_upload3->toSql()}) AS sub"))
            //     ->select(DB::raw("case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor"))
            //     ->mergeBindings($no_upload3)
            //     ->first();

            $no_upload = collect(DB::select("select case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor from (
		select a.no_upload_tgl nomor, b.tgl_upload tanggal,'Urut Upload Pengeluaran cms' ket, a.kd_skpd from trdupload_cmsbank a
		left join trhupload_cmsbank b on b.kd_skpd=a.kd_bp and b.no_upload=a.no_upload
    where a.kd_skpd=?
		union all
    select a.no_upload_tgl nomor, b.tgl_upload tanggal,'Urut Upload Panjar Bank cms' ket, a.kd_skpd from trdupload_cmsbank_panjar a
		left join trhupload_cmsbank_panjar b on b.kd_skpd=a.kd_bp and b.no_upload=a.no_upload
    where a.kd_skpd=?
		union all
    select a.no_upload_tgl nomor, b.tgl_upload tanggal,'Urut Upload Penerimaan cms' ket, a.kd_skpd from trdupload_sts_cmsbank a
		left join trhupload_sts_cmsbank b on b.kd_skpd=a.kd_bp and b.no_upload=a.no_upload
    where a.kd_skpd=?
    )
    z WHERE kd_skpd=? AND tanggal=?", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $tanggal]))->first();

            $no_upload = $no_upload->nomor;

            if (Str::length($no_upload) == '1') {
                $no_upload1 = "00" . $no_upload;
            } elseif (Str::length($no_upload) == '2') {
                $no_upload1 = "0" . $no_upload;
            } elseif (Str::length($no_upload) == '3') {
                $no_upload1 = $no_upload;
            }

            DB::table('trhupload_cmsbank')->where(['no_upload' => $nomor->nomor, 'kd_skpd' => $kd_skpd])->delete();
            DB::table('trdupload_cmsbank')->where(['no_upload' => $nomor->nomor, 'kd_skpd' => $kd_skpd])->delete();

            if (isset($rincian_data)) {
                DB::table('trdupload_cmsbank')->insert(array_map(function ($value) use ($nomor, $no_upload1) {
                    return [
                        'no_voucher' => $value['no_voucher'],
                        'tgl_voucher' => $value['tgl_voucher'],
                        'no_upload' => $nomor->nomor,
                        'rekening_awal' => $value['rekening_awal'],
                        'nm_rekening_tujuan' => $value['nm_rekening_tujuan'],
                        'rekening_tujuan' => $value['rekening_tujuan'],
                        'bank_tujuan' => $value['bank_tujuan'],
                        'ket_tujuan' => $value['ket_tujuan'],
                        'nilai' => $value['nilai_pengeluaran'],
                        'kd_skpd' => $value['kd_skpd'],
                        'kd_bp' => $value['kd_skpd'],
                        'status_upload' => '1',
                        'no_upload_tgl' => $no_upload1,
                    ];
                }, $rincian_data));
            }

            DB::table('trhupload_cmsbank')->insert([
                'no_upload' => $nomor->nomor,
                'tgl_upload' => $tanggal,
                'kd_skpd' => $kd_skpd,
                'total' => $total_transaksi,
                'no_upload_tgl' => $no_upload1,
            ]);

            $data1 = DB::table('trhupload_cmsbank as a')->leftJoin('trdupload_cmsbank as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_upload', '=', 'b.no_upload');
            })->where(['b.kd_bp' => $kd_skpd, 'a.no_upload' => $nomor->nomor])->select('a.no_upload', 'b.kd_skpd', 'a.tgl_upload', 'b.status_upload', 'b.no_voucher', 'b.kd_bp');

            $data = DB::table('trhtransout_cmsbank as c')->joinSub($data1, 'd', function ($join) {
                $join->on('c.no_voucher', '=', 'd.no_voucher');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->whereRaw('left(c.kd_skpd,17) = left(?,17)', $kd_skpd)->update([
                'c.status_upload' => '1',
                'c.tgl_upload' => date("Y-m-d")
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

    public function batalUpload(Request $request)
    {
        $no_upload = $request->no_upload;
        $no_voucher = $request->no_voucher;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = collect(DB::select("SELECT count(*) as jum from trdupload_cmsbank where no_upload=? AND kd_skpd=?", [$no_upload, $kd_skpd]))->first();

            if ($cek->jum > 1) {
                DB::delete("DELETE from trdupload_cmsbank where no_voucher=? and no_upload=? AND kd_skpd=?", [$no_voucher, $no_upload, $kd_skpd]);

                DB::update("UPDATE
                            trhupload_cmsbank
                            SET trhupload_cmsbank.total = Table_B.total
                        FROM trhupload_cmsbank
                        INNER JOIN (select a.no_upload,b.kd_skpd,a.tgl_upload,b.status_upload,b.no_voucher,b.kd_bp,sum(b.nilai) as total from trhupload_cmsbank a left join
                        trdupload_cmsbank b on b.kd_bp=a.kd_skpd and a.no_upload=b.no_upload
                        where b.kd_bp=? and a.no_upload=?
                        group by a.no_upload,b.kd_skpd,a.tgl_upload,b.status_upload,b.no_voucher,b.kd_bp) AS Table_B ON trhupload_cmsbank.no_upload = Table_B.no_upload AND trhupload_cmsbank.kd_skpd = Table_B.kd_skpd
                        where left(trhupload_cmsbank.kd_skpd,17)=left(?,17)", [$kd_skpd, $no_upload, $kd_skpd]);
            } else {
                DB::delete("DELETE from trdupload_cmsbank where no_voucher=? and no_upload=? AND kd_skpd=?", [$no_voucher, $no_upload, $kd_skpd]);

                DB::delete("DELETE from trhupload_cmsbank where no_upload=? AND kd_skpd=?", [$no_upload, $kd_skpd]);
            }

            DB::update("UPDATE trhtransout_cmsbank set status_upload='0', tgl_upload='' where no_voucher=? AND kd_skpd=?", [$no_voucher, $kd_skpd]);

            // $data1 = DB::table('trhupload_cmsbank as a')->leftJoin('trdupload_cmsbank as b', function ($join) {
            //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            //     $join->on('a.no_upload', '=', 'b.no_upload');
            // })->where(['b.kd_bp' => $kd_skpd, 'a.no_upload' => $no_upload])->select('a.no_upload', 'b.kd_skpd', 'a.tgl_upload', 'b.status_upload', 'b.no_voucher', 'b.kd_bp', DB::raw("SUM(b.nilai) as total"))->groupBy('a.no_upload', 'b.kd_skpd', 'a.tgl_upload', 'b.status_upload', 'b.no_voucher', 'b.kd_bp');

            // $data = DB::table('trhtransout_cmsbank as c')->joinSub($data1, 'd', function ($join) {
            //     $join->on('c.no_voucher', '=', 'd.no_voucher');
            //     $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            // })->whereRaw('left(c.kd_skpd,17) = left(?,17)', $kd_skpd)->update([
            //     'c.status_upload' => '0',
            //     'c.tgl_upload' => ''
            // ]);

            // DB::table('trdupload_cmsbank')->where(['no_upload' => $no_upload, 'kd_skpd' => $kd_skpd])->delete();
            // DB::table('trhupload_cmsbank')->where(['no_upload' => $no_upload, 'kd_skpd' => $kd_skpd])->delete();

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

    public function cetakCsvKalbar(Request $request)
    {
        ob_start();
        $no_upload = $request->no_upload;
        $kd_skpd = Auth::user()->kd_skpd;

        $obskpd = DB::table('ms_skpd')->select('obskpd')->where(['kd_skpd' => $kd_skpd])->first();

        $query1 = DB::table('trhupload_cmsbank as a')->leftJoin('trdupload_cmsbank as b', function ($join) {
            $join->on('a.no_upload', '=', 'b.no_upload');
            $join->on('a.kd_skpd', '=', 'b.kd_bp');
        })->leftJoin('trdtransout_transfercms as c', function ($join) {
            $join->on('b.no_voucher', '=', 'c.no_voucher');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            $join->on('b.tgl_voucher', '=', 'c.tgl_voucher');
        })->leftJoin('ms_rekening_bank_online as d', function ($join) {
            $join->on('b.kd_bp', '=', 'd.kd_skpd');
            $join->on(DB::raw("RTRIM(c.rekening_tujuan)"), '=', DB::raw("RTRIM(d.rekening)"));
        })->leftJoin('trdtransout_cmsbank as e', function ($join) {
            $join->on('b.kd_skpd', '=', 'e.kd_skpd');
            $join->on('b.no_voucher', '=', 'e.no_voucher');
        })->leftJoin('ms_bank_online as f', function ($join) {
            $join->on('d.kd_bank', '=', 'f.kd_bank');
            $join->on('d.bic', '=', 'f.bic');
        })->where(['a.kd_skpd' => $kd_skpd, 'a.no_upload' => $no_upload, 'f.bic' => 'PDKBIDJ1'])->select(
            'a.tgl_upload',
            'a.kd_skpd',
            DB::raw("(SELECT obskpd FROM ms_skpd WHERE kd_skpd=b.kd_skpd) as nm_skpd"),
            'b.rekening_awal',
            'c.nm_rekening_tujuan',
            'c.rekening_tujuan',
            'c.nilai',
            DB::raw("(REPLACE(b.ket_tujuan, '2023.', RIGHT(e.kd_sub_kegiatan,5)+ '/')) as ket_tujuan"),
            'b.no_upload_tgl'
        );

        $query = DB::table(DB::raw("({$query1->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($query1)
            ->groupBy('tgl_upload', 'kd_skpd', 'nm_skpd', 'rekening_awal', 'nm_rekening_tujuan', 'rekening_tujuan', 'nilai', 'ket_tujuan', 'no_upload_tgl')
            ->get();

        foreach ($query as $data) {
            $tgl_upload = $data->tgl_upload;
            $no_upload_tgl = $data->no_upload_tgl;
            $nilai = strval($data->nilai);
            $nilai = str_replace('.00', '', $nilai);

            $result = $data->nm_skpd . ";" . str_replace(" ", "", rtrim($data->rekening_awal)) . ";" . rtrim($data->nm_rekening_tujuan) . ";" . str_replace(" ", "", rtrim($data->rekening_tujuan)) . ";" . $nilai . ";" . $data->ket_tujuan . "\n";

            $init_tgl = explode("-", $tgl_upload);
            $tglupl = $init_tgl[2] . $init_tgl[1] . $init_tgl[0];
            $filename = 'OB' . "_" . $obskpd->obskpd . "_" . $tglupl . "_" . $no_upload_tgl;

            echo $result;
            header("Cache-Control: no-cache, no-store");
            header('Content-Type: application/csv');
            header('Content-Disposition: attachement; filename="' . $filename . '.csv"');
        }
    }

    public function cetakCsvLuarKalbar(Request $request)
    {
        $no_upload = $request->no_upload;
        $kd_skpd = Auth::user()->kd_skpd;

        $obskpd = DB::table('ms_skpd')->select('obskpd')->where(['kd_skpd' => $kd_skpd])->first();

        $query1 = DB::table('trhupload_cmsbank as a')->leftJoin('trdupload_cmsbank as b', function ($join) {
            $join->on('a.no_upload', '=', 'b.no_upload');
            $join->on('a.kd_skpd', '=', 'b.kd_bp');
        })->leftJoin('trdtransout_transfercms as c', function ($join) {
            $join->on('b.no_voucher', '=', 'c.no_voucher');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            $join->on('b.tgl_voucher', '=', 'c.tgl_voucher');
        })->leftJoin('ms_rekening_bank_online as d', function ($join) {
            $join->on('b.kd_bp', '=', 'd.kd_skpd');
            $join->on(DB::raw("RTRIM(c.rekening_tujuan)"), '=', DB::raw("RTRIM(d.rekening)"));
        })->leftJoin('trdtransout_cmsbank as e', function ($join) {
            $join->on('b.kd_skpd', '=', 'e.kd_skpd');
            $join->on('b.no_voucher', '=', 'e.no_voucher');
        })->leftJoin('ms_bank_online as f', function ($join) {
            $join->on('d.kd_bank', '=', 'f.kd_bank');
            $join->on('d.bic', '=', 'f.bic');
        })->where(['a.kd_skpd' => $kd_skpd, 'a.no_upload' => $no_upload])->where('f.bic', '<>', 'PDKBIDJ1')->select(
            'a.tgl_upload',
            'a.kd_skpd',
            DB::raw("(SELECT obskpd FROM ms_skpd WHERE kd_skpd=b.kd_skpd) as nm_skpd"),
            'b.rekening_awal',
            'c.nm_rekening_tujuan',
            'c.rekening_tujuan',
            'c.nilai',
            DB::raw("SUBSTRING(b.ket_tujuan + '/' + RIGHT(e.kd_sub_kegiatan,5) + '/' + c.nm_rekening_tujuan,0,30) as ket_tujuan"),
            'b.no_upload_tgl',
            'f.bic'
        );

        $query = DB::table(DB::raw("({$query1->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($query1)
            ->groupBy('tgl_upload', 'kd_skpd', 'nm_skpd', 'rekening_awal', 'nm_rekening_tujuan', 'rekening_tujuan', 'nilai', 'ket_tujuan', 'no_upload_tgl', 'bic')
            ->get();

        foreach ($query as $data) {
            $tgl_upload = $data->tgl_upload;
            $no_upload_tgl = $data->no_upload_tgl;
            $nilai = strval($data->nilai);
            $nilai = str_replace('.00', '', $nilai);
            $init_tgl = explode("-", $tgl_upload);
            $tglupl = $init_tgl[2] . $init_tgl[1] . $init_tgl[0];
            $filename = 'SKN' . "_" . $obskpd->obskpd . "_" . $tglupl . "_" . $no_upload_tgl;

            $result = $data->nm_skpd . ";" . str_replace(" ", "", rtrim($data->rekening_awal)) . ";" . rtrim($data->nm_rekening_tujuan) . ";" . rtrim($data->bic) . ";" . str_replace(" ", "", rtrim($data->rekening_tujuan)) . ";" . $nilai . ";" .  'IDR' . ";" . $data->ket_tujuan . ";" . "\n";

            echo $result;
        }
        header("Cache-Control: no-cache, no-store, must_revalidate");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachement; filename="' . $filename . '.csv"');
    }

    public function dataUpload(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_upload = $request->no_upload;

        $join1 = DB::table('trdtransout_transfercms as a')->select('a.no_voucher', 'a.kd_skpd', DB::raw("SUM(a.nilai) as bersih"))->where(['a.kd_skpd' => $kd_skpd])->groupBy('a.no_voucher', 'a.kd_skpd');

        $data = DB::table('trhupload_cmsbank as a')->leftJoin('trdupload_cmsbank as b', function ($join) {
            $join->on('a.kd_skpd', '=', 'b.kd_bp');
            $join->on('a.no_upload', '=', 'b.no_upload');
        })->join('trhtransout_cmsbank as d', function ($join) {
            $join->on('b.no_voucher', '=', 'd.no_voucher');
            $join->on('b.kd_skpd', '=', 'd.kd_skpd');
        })->leftJoinSub($join1, 'c', function ($join) {
            $join->on('c.no_voucher', '=', 'b.no_voucher');
            $join->on('c.kd_skpd', '=', 'b.kd_skpd');
        })->select('d.ket', 'b.kd_skpd', 'b.no_voucher', 'b.tgl_voucher', 'a.no_upload', 'a.tgl_upload', 'a.total', 'b.nilai', 'b.status_upload', 'b.rekening_awal', 'b.nm_rekening_tujuan', 'b.rekening_tujuan', 'b.bank_tujuan', 'b.ket_tujuan', 'c.bersih', DB::raw("b.nilai - c.bersih as pot"))->where(['a.kd_skpd' => $kd_skpd, 'a.no_upload' => $no_upload])->groupBy('b.kd_skpd', 'b.no_voucher', 'b.tgl_voucher', 'a.no_upload', 'a.tgl_upload', 'a.total', 'b.nilai', 'b.status_upload', 'b.rekening_awal', 'b.nm_rekening_tujuan', 'b.rekening_tujuan', 'b.bank_tujuan', 'b.ket_tujuan', 'c.bersih', 'd.ket')->orderBy(DB::raw("CAST(a.no_upload as int)"))->orderBy('b.kd_skpd')->get();
        return Datatables::of($data)->addIndexColumn()->make(true);
    }
}
