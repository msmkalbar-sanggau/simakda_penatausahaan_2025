<?php

namespace App\Http\Controllers\Skpd\PanjarCMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class UploadPanjarCMSController extends Controller
{
    public function index()
    {
        $data = [
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.upload_panjar_cms.index')->with($data);
    }

    public function loadUpload()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $status_ang = status_anggaran_new();

        $data = DB::select("SELECT a.*,b.nm_sub_kegiatan,isnull((select sum(nilai) from trhtrmpot_cmsbank where no_voucher=a.no_panjar and kd_skpd=a.kd_skpd ),0) [pot],
            isnull((select sum(nilai) from tr_panjar_transfercms where no_bukti=a.no_panjar and kd_skpd=a.kd_skpd),0) [bersih]
            FROM tr_panjar_cmsbank a  join trskpd b on a.kd_sub_kegiatan=b.kd_sub_kegiatan and b.jns_ang=? and a.kd_skpd=b.kd_skpd
        where a.kd_skpd=? and a.status_upload='0'
        order by cast(a.no_kas as int),a.kd_skpd", [$status_ang->jns_ang, $kd_skpd]);

        return Datatables::of($data)->addIndexColumn()->make(true);
    }

    public function draftUpload()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT a.kd_skpd, a.no_kas, a.no_panjar, a.tgl_panjar, a.keterangan, a.nilai, a.status_upload, a.tgl_upload, a.status_validasi, a.tgl_validasi, a.rekening_awal, a.nm_rekening_tujuan, a.rekening_tujuan, a.bank_tujuan, a.ket_tujuan, b.no_upload, b.no_upload_tgl FROM tr_panjar_cmsbank a LEFT JOIN trdupload_cmsbank_panjar b ON b.kd_skpd = a.kd_skpd AND a.no_panjar = b.no_bukti WHERE a.kd_skpd=? AND a.status_upload = '1' AND a.status_validasi = '0' GROUP BY a.kd_skpd, a.no_kas, a.no_panjar, a.tgl_panjar, a.keterangan, a.nilai, a.status_upload, a.tgl_upload, a.status_validasi, a.tgl_validasi, a.rekening_awal, a.nm_rekening_tujuan, a.rekening_tujuan, a.bank_tujuan, a.ket_tujuan, b.no_upload, b.no_upload_tgl ORDER BY CAST (b.no_upload AS INT), CAST (a.no_panjar AS INT), a.kd_skpd", [$kd_skpd]);

        return Datatables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="javascript:void(0);" onclick="lihatDataUpload(\'' . $row->no_upload . '\',\'' . $row->nilai . '\');" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="batalUpload(\'' . $row->no_upload . '\',\'' . $row->no_kas . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function dataTransaksi(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_kas = $request->no_kas;

        $no_bukti = array();
        if (!empty($no_kas)) {
            foreach ($no_kas as $voucher) {
                $no_bukti[] = $voucher['no_kas'];
            }
        } else {
            $no_bukti[] = '';
        }

        $kd_skpd = Auth::user()->kd_skpd;
        $status_ang = status_anggaran_new();

        // $data = DB::select("SELECT a.*,b.nm_sub_kegiatan,isnull((select sum(nilai) from trhtrmpot_cmsbank where no_voucher=a.no_panjar and kd_skpd=a.kd_skpd ),0) [pot],
        //     isnull((select sum(nilai) from tr_panjar_transfercms where no_bukti=a.no_panjar and kd_skpd=a.kd_skpd),0) [bersih]
        //     FROM tr_panjar_cmsbank a  join trskpd b on a.kd_sub_kegiatan=b.kd_sub_kegiatan and b.jns_ang=? and a.kd_skpd=b.kd_skpd
        // where a.kd_skpd=? and a.status_upload='0'
        // order by cast(a.no_kas as int),a.kd_skpd", [$status_ang->jns_ang, $kd_skpd]);

        $data = DB::table('tr_panjar_cmsbank as a')
            ->join('trskpd as b', function ($query) use ($status_ang) {
                $query->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
                $query->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.*,b.nm_sub_kegiatan,isnull((select sum(nilai) from trhtrmpot_cmsbank where no_voucher=a.no_panjar and kd_skpd=a.kd_skpd ),0) [pot],
            isnull((select sum(nilai) from tr_panjar_transfercms where no_bukti=a.no_panjar and kd_skpd=a.kd_skpd),0) [bersih]")
            ->where(['a.kd_skpd' => $kd_skpd, 'a.status_upload' => '0', 'b.jns_ang' => $status_ang->jns_ang])
            ->whereNotIn('a.no_kas', $no_bukti)
            ->orderByRaw("cast(a.no_kas as int),a.kd_skpd")
            ->get();

        return response()->json($data);
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

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $status_ang = status_anggaran_new();

        $data = DB::select("SELECT a.*,b.nm_sub_kegiatan,isnull((select sum(nilai) from trhtrmpot_cmsbank where no_voucher=a.no_panjar and kd_skpd=a.kd_skpd ),0) [pot],
            isnull((select sum(nilai) from tr_panjar_transfercms where no_bukti=a.no_panjar and kd_skpd=a.kd_skpd),0) [bersih]
            FROM tr_panjar_cmsbank a  join trskpd b on a.kd_sub_kegiatan=b.kd_sub_kegiatan and b.jns_ang=? and a.kd_skpd=b.kd_skpd
        where a.kd_skpd=? and a.status_upload='0'
        order by cast(a.no_kas as int),a.kd_skpd", [$status_ang->jns_ang, $kd_skpd]);

        $data = [
            'daftar_transaksi' => $data,
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.upload_panjar_cms.create')->with($data);
    }

    public function prosesUpload(Request $request)
    {
        $total_transaksi = $request->total_transaksi;
        $rincian_data = $request->rincian_data;
        $tanggal = date("Y-m-d");
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $nomor = collect(DB::select("SELECT case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor from (
	select no_upload nomor, 'Urut Upload Pengeluaran cms' ket, kd_skpd from trhupload_cmsbank where kd_skpd=?
    union all
    select no_upload nomor, 'Urut Upload Panjar Bank cms' ket, kd_skpd from trhupload_cmsbank_panjar where kd_skpd=?
    union all
    select no_upload nomor, 'Urut Upload Penerimaan cms' ket, kd_skpd from trhupload_sts_cmsbank where kd_skpd=?
    )
    z WHERE kd_skpd=?", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]))->first();

            $no_upload = collect(DB::select("SELECT case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor from (
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

            if (Str::length($no_upload->nomor) == '1') {
                $no_upload1 = "00" . $no_upload->nomor;
            } elseif (Str::length($no_upload->nomor) == '2') {
                $no_upload1 = "0" . $no_upload->nomor;
            } elseif (Str::length($no_upload->nomor) == '3') {
                $no_upload1 = $no_upload->nomor;
            }

            DB::table('trhupload_cmsbank_panjar')->where(['no_upload' => $nomor->nomor, 'kd_skpd' => $kd_skpd])->delete();
            DB::table('trdupload_cmsbank_panjar')->where(['no_upload' => $nomor->nomor, 'kd_skpd' => $kd_skpd])->delete();

            if (isset($rincian_data)) {
                DB::table('trdupload_cmsbank_panjar')->insert(array_map(function ($value) use ($nomor, $no_upload1) {
                    return [
                        'no_bukti' => $value['no_kas'],
                        'tgl_bukti' => $value['tgl_kas'],
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

            DB::table('trhupload_cmsbank_panjar')->insert([
                'no_upload' => $nomor->nomor,
                'tgl_upload' => $tanggal,
                'kd_skpd' => $kd_skpd,
                'total' => $total_transaksi,
                'no_upload_tgl' => $no_upload1,
            ]);

            DB::update("UPDATE
                            tr_panjar_cmsbank
                            SET tr_panjar_cmsbank.status_upload = Table_B.status_upload,
                                 tr_panjar_cmsbank.tgl_upload = Table_B.tgl_upload
                        FROM tr_panjar_cmsbank
                        INNER JOIN (select a.no_upload,b.kd_skpd,a.tgl_upload,b.status_upload,b.no_bukti,b.kd_bp from trhupload_cmsbank_panjar a left join
                        trdupload_cmsbank_panjar b on b.kd_bp=a.kd_skpd and a.no_upload=b.no_upload
                        where b.kd_bp=? and a.no_upload=?) AS Table_B ON tr_panjar_cmsbank.no_kas = Table_B.no_bukti AND tr_panjar_cmsbank.kd_skpd = Table_B.kd_skpd
                        where tr_panjar_cmsbank.kd_skpd=?", [$kd_skpd, $nomor->nomor, $kd_skpd]);

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
        $no_kas = $request->no_kas;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trdupload_cmsbank_panjar')
                ->where(['no_upload' => $no_upload, 'kd_skpd' => $kd_skpd])
                ->count();

            if ($cek > 1) {
                DB::table('trdupload_cmsbank_panjar')
                    ->where(['no_bukti' => $no_kas, 'no_upload' => $no_upload, 'kd_skpd' => $kd_skpd])
                    ->delete();

                DB::update("UPDATE
                            trhupload_cmsbank_panjar
                            SET trhupload_cmsbank_panjar.total = Table_B.total
                        FROM trhupload_cmsbank_panjar
                        INNER JOIN (select a.no_upload,b.kd_skpd,a.tgl_upload,b.status_upload,b.no_bukti,b.kd_bp,sum(b.nilai) as total from trhupload_cmsbank_panjar a left join
                        trdupload_cmsbank_panjar b on b.kd_bp=a.kd_skpd and a.no_upload=b.no_upload
                        where b.kd_bp=? and a.no_upload=?
                        group by a.no_upload,b.kd_skpd,a.tgl_upload,b.status_upload,b.no_bukti,b.kd_bp) AS Table_B ON trhupload_cmsbank_panjar.no_upload = Table_B.no_upload AND trhupload_cmsbank_panjar.kd_skpd = Table_B.kd_skpd
                        where left(trhupload_cmsbank_panjar.kd_skpd,7)=left(?,7)", [$kd_skpd, $no_upload, $kd_skpd]);
            } else {
                DB::table('trdupload_cmsbank_panjar')
                    ->where(['no_bukti' => $no_kas, 'no_upload' => $no_upload, 'kd_skpd' => $kd_skpd])
                    ->delete();

                DB::table('trhupload_cmsbank_panjar')
                    ->where(['no_upload' => $no_upload, 'kd_skpd' => $kd_skpd])
                    ->delete();
            }

            DB::update("UPDATE tr_panjar_cmsbank set status_upload='0', tgl_upload='' where no_kas=? AND kd_skpd=?", [$no_kas, $kd_skpd]);

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
        $no_upload = $request->no_upload;
        $kd_skpd = Auth::user()->kd_skpd;

        $obskpd = DB::table('ms_skpd')->select('obskpd')->where(['kd_skpd' => $kd_skpd])->first();

        $query = DB::select("SELECT * FROM (SELECT a.tgl_upload,a.kd_skpd,(SELECT obskpd from ms_skpd where kd_skpd=a.kd_skpd) as nm_skpd,
        b.rekening_awal,c.nm_rekening_tujuan,c.rekening_tujuan,c.nilai,b.ket_tujuan,b.no_upload_tgl FROM trhupload_cmsbank_panjar a
        left join trdupload_cmsbank_panjar b on b.kd_bp=a.kd_skpd and a.no_upload=b.no_upload
        left join tr_panjar_transfercms c on c.kd_skpd=b.kd_skpd and c.no_bukti=b.no_bukti
        where a.kd_skpd=? and a.no_upload=?) x
        GROUP BY tgl_upload, kd_skpd, nm_skpd, rekening_awal, nm_rekening_tujuan, rekening_tujuan, nilai, ket_tujuan, no_upload_tgl", [$kd_skpd, $no_upload]);

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
        }
        header("Cache-Control: no-cache, no-store, must_revalidate");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachement; filename="' . $filename . '.csv"');
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

        $data = DB::select("SELECT a.*,b.* FROM trhupload_cmsbank_panjar a left join trdupload_cmsbank_panjar b on b.kd_bp=a.kd_skpd and a.no_upload=b.no_upload
        where a.kd_skpd=? and a.no_upload=?
        order by cast(a.no_upload as int),a.kd_skpd", [$kd_skpd, $no_upload]);

        return Datatables::of($data)->addIndexColumn()->make(true);
    }
}
