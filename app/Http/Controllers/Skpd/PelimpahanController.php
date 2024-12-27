<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class PelimpahanController extends Controller
{
    // PELIMPAHAN UP DARI indexUp
    public function indexUp()
    {
        return view('skpd.pelimpahan_up.index');
    }

    public function loadDataUp()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_setorpelimpahan_bank_cms')->where(['kd_skpd_sumber' => $kd_skpd])->orderBy('tgl_kas')->orderBy(DB::raw("CAST(no_kas as INT)"))->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if ($row->status_upload != '1') {
                $btn = '<a href="' . route("skpd.pelimpahan.edit_up", Crypt::encryptString($row->no_kas)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="hapusPelimpahan(' . $row->no_kas . ', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            } else {
                $btn = '';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.pelimpahan_up.index');
    }

    public function tambahUp()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $skpd = substr($kd_skpd, 0, 17);

        $data = [
            'tujuan_skpd' => DB::table('ms_skpd')->select(DB::raw("SUBSTRING(kd_skpd,1,4)+SUBSTRING(kd_skpd,15,8) as kd_ringkas"), 'kd_skpd', 'nm_skpd', DB::raw("'$kd_skpd' as skpd"))->whereRaw("LEFT(kd_skpd,17) = ?", $skpd)->whereNotIn('kd_skpd', [$kd_skpd])->orderBy('kd_skpd')->get(),
            'tahun_anggaran' => tahun_anggaran(),
            'rekening_bendahara' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->first(),
            'rekening_tujuan' => DB::table('ms_rekening_bank_online as a')->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nm_bank"))->where(['a.kd_skpd' => $kd_skpd])->orderBy('a.nm_rekening')->get(),
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.pelimpahan_up.create')->with($data);
    }

    public function simpanUp(Request $request)
    {
        $data = $request->data;
        // return response()->json($data);
        // $data = array_map('htmlentities', $request->data);
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = no_urut($kd_skpd);

            // SETOR PELIMPAHAN UP

            DB::table('tr_setorpelimpahan_bank_cms')->insert([
                'no_kas' => $no_urut,
                'tgl_kas' => $data['tgl_kas'],
                'no_bukti' => $no_urut,
                'tgl_bukti' => $data['tgl_kas'],
                'kd_skpd' => $data['kd_skpd'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'kd_skpd_sumber' => $data['skpd_sumber'],
                'jenis_spp' => $data['beban'],
                'rekening_awal' => $data['rekening_bendahara'],
                'nm_rekening_tujuan' => $data['nama_tujuan'],
                'rekening_tujuan' => $data['rekening_tujuan'],
                'bank_tujuan' => $data['bank_tujuan'],
                'ket_tujuan' => $data['ketcms'],
                'status_validasi' => '0',
                'status_upload' => '0',
            ]);

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_kas' => $no_urut
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function editUp($no_kas)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $skpd = substr($kd_skpd, 0, 17);
        $no_kas = Crypt::decryptString($no_kas);
        $data_up = DB::table('tr_setorpelimpahan_bank_cms')->where(['kd_skpd_sumber' => $kd_skpd, 'no_kas' => $no_kas])->first();

        $data = [
            'data_up' => $data_up,
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', DB::raw("SUBSTRING(kd_skpd,1,4)+SUBSTRING(kd_skpd,15,8) as kd_ringkas"))->where(['kd_skpd' => $data_up->kd_skpd])->first(),
            'tujuan_skpd' => DB::table('ms_skpd')->select(DB::raw("SUBSTRING(kd_skpd,1,4)+SUBSTRING(kd_skpd,15,8) as kd_ringkas"), 'kd_skpd', 'nm_skpd', DB::raw("'$kd_skpd' as skpd"))->whereRaw("LEFT(kd_skpd,17) = ?", $skpd)->whereNotIn('kd_skpd', [$kd_skpd])->orderBy('kd_skpd')->get(),
            'tahun_anggaran' => tahun_anggaran(),
            'rekening_bendahara' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->first(),
            'rekening_tujuan' => DB::table('ms_rekening_bank_online as a')->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nm_bank"))->where(['a.kd_skpd' => $kd_skpd])->orderBy('a.nm_rekening')->get(),
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.pelimpahan_up.edit')->with($data);
    }

    public function updateUp(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_setorpelimpahan_bank_cms')->where(['no_kas' => $data['no_kas'], 'kd_skpd_sumber' => $kd_skpd])->delete();

            DB::table('tr_setorpelimpahan_bank_cms')->insert([
                'no_kas' => $data['no_kas'],
                'tgl_kas' => $data['tgl_kas'],
                'no_bukti' => $data['no_kas'],
                'tgl_bukti' => $data['tgl_kas'],
                'kd_skpd' => $data['kd_skpd'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'kd_skpd_sumber' => $data['skpd_sumber'],
                'jenis_spp' => $data['beban'],
                'rekening_awal' => $data['rekening_bendahara'],
                'nm_rekening_tujuan' => $data['nama_tujuan'],
                'rekening_tujuan' => $data['rekening_tujuan'],
                'bank_tujuan' => $data['bank_tujuan'],
                'ket_tujuan' => $data['ketcms'],
                'status_validasi' => '0',
                'status_upload' => '0',
            ]);

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_kas' => $data['no_kas']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapusUp(Request $request)
    {
        $no_kas = $request->no_kas;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_setorpelimpahan_bank_cms')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->delete();

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
    // Pelimpahan UP Sampai hapusUp

    // PELIMPAHAN GU DARI indexGu
    public function indexGu()
    {
        return view('skpd.pelimpahan_gu.index');
    }

    public function loadDataGu()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_setorpelimpahan_bank_cms')->where(['kd_skpd_sumber' => $kd_skpd])->orderBy('tgl_kas')->orderBy(DB::raw("CAST(no_kas as INT)"))->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if ($row->status_upload != '1') {
                $btn = '<a href="' . route("skpd.pelimpahan.edit_gu", Crypt::encryptString($row->no_kas)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="hapusPelimpahan(' . $row->no_kas . ', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            } else {
                $btn = '';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.pelimpahan_gu.index');
    }

    public function tambahGu()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $skpd = substr($kd_skpd, 0, 17);

        $data = [
            'tujuan_skpd' => DB::table('ms_skpd')->select(DB::raw("SUBSTRING(kd_skpd,1,4)+SUBSTRING(kd_skpd,15,8) as kd_ringkas"), 'kd_skpd', 'nm_skpd', DB::raw("'$kd_skpd' as skpd"))->whereRaw("LEFT(kd_skpd,17) = ?", $skpd)->whereNotIn('kd_skpd', [$kd_skpd])->orderBy('kd_skpd')->get(),
            'tahun_anggaran' => tahun_anggaran(),
            'rekening_bendahara' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->first(),
            'rekening_tujuan' => DB::table('ms_rekening_bank_online as a')->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nm_bank"))->where(['a.kd_skpd' => $kd_skpd])->orderBy('a.nm_rekening')->get(),
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.pelimpahan_gu.create')->with($data);
    }

    public function noLpj(Request $request)
    {
        $kd_skpd = $request->kd_skpd;

        $data = DB::table('trhlpj_unit')->select('no_lpj', 'kd_skpd')->selectRaw("(SELECT sum(nilai) from trlpj where no_lpj_unit=trhlpj_unit.no_lpj and kd_skpd=?) as nilai", [$kd_skpd])->where(['kd_skpd' => $kd_skpd])->whereRaw("no_lpj not in (select ISNULL(lpj_unit,'') from tr_setorpelimpahan_bank_cms)")->get();
        return response()->json($data);
    }

    public function simpanGu(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = no_urut($kd_skpd);

            // SETOR PELIMPAHAN UP

            DB::table('tr_setorpelimpahan_bank_cms')->insert([
                'no_kas' => $no_urut,
                'tgl_kas' => $data['tgl_kas'],
                'no_bukti' => $no_urut,
                'tgl_bukti' => $data['tgl_kas'],
                'kd_skpd' => $data['kd_skpd'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'kd_skpd_sumber' => $data['skpd_sumber'],
                'jenis_spp' => $data['beban'],
                'rekening_awal' => $data['rekening_bendahara'],
                'nm_rekening_tujuan' => $data['nama_tujuan'],
                'rekening_tujuan' => $data['rekening_tujuan'],
                'bank_tujuan' => $data['bank_tujuan'],
                'ket_tujuan' => $data['ketcms'],
                'status_validasi' => '0',
                'status_upload' => '0',
                'lpj_unit' => $data['no_lpj'],
            ]);

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_kas' => $no_urut
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function editGu($no_kas)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_kas = Crypt::decryptString($no_kas);
        $data_up = DB::table('tr_setorpelimpahan_bank_cms')->where(['kd_skpd_sumber' => $kd_skpd, 'no_kas' => $no_kas])->first();

        $data = [
            'data_up' => $data_up,
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', DB::raw("SUBSTRING(kd_skpd,1,4)+SUBSTRING(kd_skpd,15,8) as kd_ringkas"))->where(['kd_skpd' => $data_up->kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'rekening_bendahara' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->first(),
            'rekening_tujuan' => DB::table('ms_rekening_bank_online as a')->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nm_bank"))->where(['a.kd_skpd' => $kd_skpd])->orderBy('a.nm_rekening')->get(),
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.pelimpahan_gu.edit')->with($data);
    }

    public function updateGu(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // DELETE PELIMPAHAN GU
            DB::table('tr_setorpelimpahan_bank_cms')->where(['kd_skpd' => $data['kd_skpd'], 'no_kas' => $data['no_kas']])->delete();
            // SETOR PELIMPAHAN GU
            DB::table('tr_setorpelimpahan_bank_cms')->insert([
                'no_kas' => $data['no_kas'],
                'tgl_kas' => $data['tgl_kas'],
                'no_bukti' => $data['no_kas'],
                'tgl_bukti' => $data['tgl_kas'],
                'kd_skpd' => $data['kd_skpd'],
                'nilai' => $data['nilai'],
                'keterangan' => $data['keterangan'],
                'kd_skpd_sumber' => $data['skpd_sumber'],
                'jenis_spp' => $data['beban'],
                'rekening_awal' => $data['rekening_bendahara'],
                'nm_rekening_tujuan' => $data['nama_tujuan'],
                'rekening_tujuan' => $data['rekening_tujuan'],
                'bank_tujuan' => $data['bank_tujuan'],
                'ket_tujuan' => $data['ketcms'],
                'status_validasi' => '0',
                'status_upload' => '0',
                'lpj_unit' => $data['no_lpj'],
            ]);

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_kas' => $data['no_kas']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapusGu(Request $request)
    {
        $no_kas = $request->no_kas;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_setorpelimpahan_bank_cms')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->delete();

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
    // Pelimpahan GU Sampai hapusGu

    // Upload UP/GU
    public function upload()
    {
        return view('skpd.upload_pelimpahan.index');
    }

    public function loadUpload()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('tr_setorpelimpahan_bank_cms as a')->where(['a.kd_skpd_sumber' => $kd_skpd, 'a.status_upload' => '0'])->orderBy(DB::raw("CAST(a.no_bukti as int)"))->orderBy('a.kd_skpd')->get();
        return Datatables::of($data)->addIndexColumn()->make(true);
        return view('skpd.upload_pelimpahan.index');
    }

    public function draftUpload()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhupload_cmsbank_bidang as a')->leftJoin('trdupload_cmsbank_bidang as b', function ($join) {
            $join->on('a.no_upload', '=', 'b.no_upload');
            $join->on('a.no_upload_tgl', '=', 'b.no_upload_tgl');
            $join->on('a.kd_skpd', '=', 'b.kd_bp');
        })->leftJoin('tr_setorpelimpahan_bank_cms as c', function ($join) {
            $join->on('b.no_bukti', '=', 'c.no_kas');
            $join->on('b.kd_bp', '=', 'c.kd_skpd_sumber');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        })->select('c.kd_skpd', DB::raw("(SELECT nm_skpd FROM ms_skpd WHERE c.kd_skpd=kd_skpd) as nm_skpd"), 'b.no_upload_tgl', 'c.no_kas', 'c.tgl_bukti', 'c.no_sp2d', 'c.keterangan', 'b.nilai AS total', 'c.status_upload', 'c.tgl_upload', 'c.status_validasi', 'c.tgl_validasi', 'c.rekening_awal', 'c.nm_rekening_tujuan', 'c.rekening_tujuan', 'c.bank_tujuan', 'c.ket_tujuan', 'b.no_upload', 'b.no_upload_tgl')->where(['c.kd_skpd_sumber' => $kd_skpd, 'c.status_upload' => '1'])->where(function ($query) {
            $query->where('c.status_validasi', '0')->orWhereNull('c.status_validasi');
        })->groupBy('c.kd_skpd', 'b.no_upload_tgl', 'c.no_kas', 'c.tgl_bukti', 'c.no_sp2d', 'c.keterangan', 'b.nilai', 'c.status_upload', 'c.tgl_upload', 'c.status_validasi', 'c.tgl_validasi', 'c.rekening_awal', 'c.nm_rekening_tujuan', 'c.rekening_tujuan', 'c.bank_tujuan', 'c.ket_tujuan', 'b.no_upload', 'b.no_upload_tgl')->orderBy(DB::raw("CAST(b.no_upload as int)"))->orderBy('c.kd_skpd')->get();
        return Datatables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if ($row->status_validasi == '1') {
                $btn = '<a href="javascript:void(0);" onclick="lihatDataUpload(' . $row->no_upload . ', \'' . $row->tgl_upload . '\', \'' . $row->total . '\')" class="btn btn-info btn-sm"><i class="fas fa-info-circle"></i></a>';
            } else {
                $btn = '<a href="javascript:void(0);" onclick="lihatDataUpload(' . $row->no_upload . ', \'' . $row->tgl_upload . '\', \'' . $row->total . '\')" class="btn btn-info btn-sm"><i class="fas fa-info-circle"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="batalUpload(' . $row->no_upload . ', \'' . $row->tgl_upload . '\', \'' . $row->no_upload_tgl . '\', \'' . $row->kd_skpd . '\')" class="btn btn-danger btn-sm" style="margin-left:4px"><i class="fas fa-trash-alt"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.upload_pelimpahan.index');
    }

    public function dataUpload(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        if (substr($kd_skpd, 8, 2) == '00') {
            $init_skpd = "left(a.kd_skpd,17)=left(?,17)";
        } else {
            $init_skpd = "a.kd_skpd=?";
        }
        $no_upload = $request->no_upload;

        $data = DB::table('trhupload_cmsbank_bidang as a')->leftJoin('trdupload_cmsbank_bidang as b', function ($join) {
            $join->on('a.kd_skpd', '=', 'b.kd_bp');
            $join->on('a.no_upload', '=', 'b.no_upload');
        })->whereRaw($init_skpd, $kd_skpd)->where('a.no_upload', $no_upload)->orderBy(DB::raw("CAST(a.no_upload as int)"))->orderBy('a.kd_skpd')->get();
        return Datatables::of($data)->addIndexColumn()->make(true);
        return view('skpd.upload_pelimpahan.index');
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

    public function tambahUpload()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'daftar_pelimpahan' => DB::table('tr_setorpelimpahan_bank_cms as a')->where(['a.kd_skpd_sumber' => $kd_skpd, 'a.status_upload' => '0'])->orderBy(DB::raw("CAST(a.no_bukti as int)"))->orderBy('a.kd_skpd')->get()
        ];

        return view('skpd.upload_pelimpahan.create')->with($data);
    }

    public function prosesUpload(Request $request)
    {
        $total_pelimpahan = $request->total_pelimpahan;
        $rincian_data = $request->rincian_data;
        $tanggal = date("Y-m-d");
        $kd_skpd = Auth::user()->kd_skpd;
        $username = Auth::user()->nama;

        DB::beginTransaction();
        try {
            // Nomor Upload
            $nomor1 = DB::table('trdupload_cmsbank')
                ->select('no_upload as nomor', DB::raw("'Urut Upload Pengeluaran cms' as ket"), 'kd_skpd', 'username')
                ->where(['kd_skpd' => $kd_skpd]);

            $nomor2 = DB::table('trhupload_cmsbank_bidang')
                ->select('no_upload as nomor', DB::raw("'Urut Upload Setor Dana Bank cms' as ket"), 'kd_skpd', 'username')
                ->where(['kd_skpd' => $kd_skpd])
                ->unionAll($nomor1);

            $nomor3 = DB::table('trhupload_cmsbank_panjar')
                ->select('no_upload as nomor', DB::raw("'Urut Upload Panjar Bank cms' as ket"), 'kd_skpd', 'username')
                ->where(['kd_skpd' => $kd_skpd])
                ->unionAll($nomor2);

            $nomor4 = DB::table('trhupload_sts_cmsbank')
                ->select('no_upload as nomor', DB::raw("'Urut Upload Penerimaan cms' as ket"), 'kd_skpd', 'username')
                ->where(['kd_skpd' => $kd_skpd])
                ->unionAll($nomor3);

            $nomor = DB::table(DB::raw("({$nomor4->toSql()}) AS sub"))
                ->select(DB::raw("case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor"))
                ->mergeBindings($nomor4)
                ->first();

            // Nomor Upload Hari
            $no_upload1 = DB::table('trhupload_cmsbank')
                ->select('no_upload_tgl as nomor', 'tgl_upload as tanggal', DB::raw("'Urut Upload Pengeluaran cms' as ket"), 'kd_skpd')
                ->where(['kd_skpd' => $kd_skpd]);

            $no_upload2 = DB::table('trdupload_cmsbank_bidang as a')
                ->leftJoin('trhupload_cmsbank_bidang as b', function ($join) {
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    $join->on('a.no_upload', '=', 'b.no_upload');
                })
                ->select('a.no_upload_tgl as nomor', 'b.tgl_upload as tanggal', DB::raw("'Urut Upload Setor Dropping Bank cms' as ket"), 'a.kd_skpd')
                ->where(['a.kd_skpd' => $kd_skpd])
                ->unionAll($no_upload1);

            $no_upload3 = DB::table('trdupload_cmsbank_panjar as a')
                ->leftJoin('trhupload_cmsbank_panjar as b', function ($join) {
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    $join->on('a.no_upload', '=', 'b.no_upload');
                })
                ->select('a.no_upload_tgl as nomor', 'b.tgl_upload as tanggal', DB::raw("'Urut Upload Panjar Bank cms' as ket"), 'a.kd_skpd')
                ->where(['a.kd_skpd' => $kd_skpd])
                ->unionAll($no_upload2);

            $no_upload4 = DB::table('trdupload_sts_cmsbank as a')
                ->leftJoin('trhupload_sts_cmsbank as b', function ($join) {
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    $join->on('a.no_upload', '=', 'b.no_upload');
                })
                ->select('a.no_upload_tgl as nomor', 'b.tgl_upload as tanggal', DB::raw("'Urut Upload Penerimaan cms' as ket"), 'a.kd_skpd')
                ->where(['a.kd_skpd' => $kd_skpd])
                ->unionAll($no_upload3);

            $no_upload = DB::table(DB::raw("({$no_upload4->toSql()}) AS sub"))
                ->select(DB::raw("case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor"))
                ->mergeBindings($no_upload4)
                ->first();

            if (Str::length($no_upload->nomor) == '1') {
                $no_upload5 = "00" . $no_upload->nomor;
            } elseif (Str::length($no_upload->nomor) == '2') {
                $no_upload5 = "0" . $no_upload->nomor;
            } elseif (Str::length($no_upload->nomor) == '3') {
                $no_upload5 = $no_upload->nomor;
            }

            DB::table('trhupload_cmsbank_bidang')->where(['no_upload' => $nomor->nomor, 'kd_skpd' => $kd_skpd, 'username' => $username])->delete();
            DB::table('trdupload_cmsbank_bidang')->where(['no_upload' => $nomor->nomor, 'kd_skpd' => $kd_skpd])->delete();

            if (isset($rincian_data)) {
                DB::table('trdupload_cmsbank_bidang')->insert(array_map(function ($value) use ($nomor, $no_upload5, $kd_skpd) {
                    return [
                        'no_bukti' => $value['no_bukti'],
                        'tgl_bukti' => $value['tgl_bukti'],
                        'no_upload' => $nomor->nomor,
                        'rekening_awal' => $value['rekening_awal'],
                        'nm_rekening_tujuan' => $value['nm_rekening_tujuan'],
                        'rekening_tujuan' => $value['rekening_tujuan'],
                        'bank_tujuan' => $value['bank_tujuan'],
                        'ket_tujuan' => $value['ket_tujuan'],
                        'nilai' => $value['total'],
                        'kd_skpd' => $value['kd_skpd'],
                        'kd_bp' => $kd_skpd,
                        'status_upload' => '1',
                        'no_upload_tgl' => $no_upload5,
                    ];
                }, $rincian_data));
            }

            DB::table('trhupload_cmsbank_bidang')->insert([
                'no_upload' => $nomor->nomor,
                'tgl_upload' => date('Y-m-d'),
                'kd_skpd' => $kd_skpd,
                'total' => $total_pelimpahan,
                'no_upload_tgl' => $no_upload5,
                'username' => $username,
            ]);

            $data1 = DB::table('trhupload_cmsbank_bidang as a')->leftJoin('trdupload_cmsbank_bidang as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_bp');
                $join->on('a.no_upload', '=', 'b.no_upload');
            })->where(['b.kd_bp' => $kd_skpd, 'a.no_upload' => $nomor->nomor])->select('a.no_upload', 'b.kd_skpd', 'a.tgl_upload', 'b.status_upload', 'b.no_bukti', 'b.kd_bp');

            DB::table('tr_setorpelimpahan_bank_cms as c')->joinSub($data1, 'd', function ($join) {
                $join->on('c.no_bukti', '=', 'd.no_bukti');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->whereRaw('left(c.kd_skpd,17) = left(?,17)', $kd_skpd)->update([
                'c.status_upload' => DB::raw("d.status_upload"),
                'c.tgl_upload' => date('Y-m-d')
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
        $tgl_upload = $request->tgl_upload;
        $no_upload_tgl = $request->no_upload_tgl;
        $kd_skpd = $request->kd_skpd;
        $kd_bp = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $data1 = DB::table('trdupload_cmsbank_bidang as a')->leftJoin('trhupload_cmsbank_bidang as b', function ($join) {
                $join->on('a.kd_bp', '=', 'b.kd_skpd');
                $join->on('a.no_upload', '=', 'b.no_upload');
                $join->on('a.no_upload_tgl', '=', 'b.no_upload_tgl');
            })->where(['a.kd_skpd' => $kd_skpd, 'a.no_upload' => $no_upload, 'a.no_upload_tgl' => $no_upload_tgl, 'b.tgl_upload' => $tgl_upload])->select('a.no_bukti', 'b.tgl_upload', 'a.kd_bp');

            DB::table('tr_setorpelimpahan_bank_cms as a')->joinSub($data1, 'b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.tgl_upload', '=', 'b.tgl_upload');
                $join->on('a.kd_skpd_sumber', '=', 'b.kd_bp');
            })->update([
                'a.status_upload' => '0',
                'a.tgl_upload' => null
            ]);

            DB::table('trdupload_cmsbank_bidang')->where(['no_upload' => $no_upload, 'no_upload_tgl' => $no_upload_tgl, 'kd_bp' => $kd_bp, 'kd_skpd' => $kd_skpd])->delete();

            $cek = DB::table('trdupload_cmsbank_bidang')
                ->where(['no_upload' => $no_upload, 'no_upload_tgl' => $no_upload_tgl, 'kd_bp' => $kd_bp])
                ->count();

            if ($cek == 0) {
                DB::table('trhupload_cmsbank_bidang')->where(['no_upload' => $no_upload, 'tgl_upload' => $tgl_upload, 'kd_skpd' => $kd_bp])->delete();
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

    public function cetakCsv(Request $request)
    {
        $no_upload = $request->no_upload;
        $tgl_upload = $request->tgl_upload;
        $kd_skpd = Auth::user()->kd_skpd;

        $obskpd = DB::table('ms_skpd')->select('obskpd')->where(['kd_skpd' => $kd_skpd])->first();

        $query = DB::table('trhupload_cmsbank_bidang as a')->leftJoin('trdupload_cmsbank_bidang as b', function ($join) {
            $join->on('a.kd_skpd', '=', 'b.kd_bp');
            $join->on('a.no_upload', '=', 'b.no_upload');
            $join->on('a.no_upload_tgl', '=', 'b.no_upload_tgl');
        })->select('a.tgl_upload', 'a.kd_skpd', DB::raw("(SELECT obskpd FROM ms_skpd WHERE kd_skpd=a.kd_skpd) as nm_skpd"), 'b.rekening_awal', 'b.nm_rekening_tujuan', 'b.rekening_tujuan', 'b.nilai', 'b.ket_tujuan', 'b.no_upload_tgl')->whereRaw("left(a.kd_skpd,17)=left(?,17)", $kd_skpd)->where(['a.no_upload' => $no_upload, 'b.kd_bp' => $kd_skpd, 'a.tgl_upload' => $tgl_upload])->get();

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

    // VALIDASI UP/GU
    public function validasi()
    {
        $data = [
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.validasi_pelimpahan.index')->with($data);
    }

    public function loadValidasi(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_setorpelimpahan_bank_cms as a')->leftJoin('trdupload_cmsbank_bidang as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->select('a.*', 'b.no_upload')->where(['a.kd_skpd_sumber' => $kd_skpd, 'a.status_upload' => '1', 'a.status_validasi' => '0'])->orderBy(DB::raw("CAST(a.no_bukti as int)"))->orderBy('a.kd_skpd')->get();
        return Datatables::of($data)->addIndexColumn()->make(true);
        return view('skpd.validasi_pelimpahan.index');
    }

    public function tambahValidasi()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'daftar_transaksi' => DB::table('tr_setorpelimpahan_bank_cms as a')->leftJoin('trdupload_cmsbank_bidang as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.*', 'b.no_upload')->where(['a.kd_skpd_sumber' => $kd_skpd, 'a.status_upload' => '1', 'a.status_validasi' => '0'])->orderBy(DB::raw("CAST(a.no_bukti as int)"))->orderBy('a.kd_skpd')->get(),
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.validasi_pelimpahan.create')->with($data);
    }

    public function prosesValidasi(Request $request)
    {
        $rincian_data = $request->rincian_data;
        $tanggal_validasi = $request->tanggal_validasi;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            if (substr($kd_skpd, 18, 4) == '0000') {
                $init_skpd = "left(kd_skpd,17) = left(?,17)";
            } else {
                $init_skpd = "KD_SKPD = ?";
            }

            $nomor = collect(DB::select("SELECT case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor from (
    select no_validasi nomor, 'Urut Validasi cms' ket, kd_skpd as kd_skpd from trvalidasi_cmsbank where kd_skpd = ?
    union all
    select no_validasi nomor, 'Urut Validasi cms Perbidang' ket, kd_skpd as kd_skpd from trvalidasi_cmsbank_bidang where kd_skpd = ?
    union all
    select no_validasi nomor, 'Urut Validasi cms Panjar' ket, kd_skpd as kd_skpd from trvalidasi_cmsbank_panjar where kd_skpd = ?
    )
    z WHERE $init_skpd", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]))->first();

            // $nomor1 = DB::table('trvalidasi_cmsbank')
            //     ->select('no_validasi as nomor', DB::raw("'Urut Validasi cms' as ket"), 'kd_skpd')
            //     ->where(['kd_skpd' => $kd_skpd]);

            // $nomor2 = DB::table('trvalidasi_cmsbank_panjar')
            //     ->select('no_validasi as nomor', DB::raw("'Urut Validasi cms Panjar' as ket"), 'kd_skpd')
            //     ->where(['kd_skpd' => $kd_skpd])
            //     ->unionAll($nomor1);

            // $nomor3 = DB::table('trvalidasi_cmsbank_bidang')
            //     ->select('no_validasi as nomor', DB::raw("'Urut Validasi cms Perbidang' as ket"), 'kd_skpd')
            //     ->where(['kd_skpd' => $kd_skpd])
            //     ->unionAll($nomor2);

            // $nomor = DB::table(DB::raw("({$nomor3->toSql()}) AS sub"))
            //     ->select(DB::raw("case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor"))
            //     ->mergeBindings($nomor3)
            //     ->first();

            $no_validasi = $nomor->nomor;
            // $no_bku = no_urut($kd_skpd);
            // $bku = $no_bku - 1;

            DB::delete("DELETE from trvalidasi_cmsbank_bidang where kd_bp=? and no_validasi=?", [$kd_skpd, $no_validasi]);

            foreach ($rincian_data as $data => $value) {
                $data = [
                    'no_bukti' => $rincian_data[$data]['no_bukti'],
                    'tgl_bukti' => $rincian_data[$data]['tgl_bukti'],
                    'no_upload' => $rincian_data[$data]['no_upload'],
                    'rekening_awal' => $rincian_data[$data]['rekening_awal'],
                    'nm_rekening_tujuan' => $rincian_data[$data]['nm_rekening_tujuan'],
                    'rekening_tujuan' => $rincian_data[$data]['rekening_tujuan'],
                    'bank_tujuan' => $rincian_data[$data]['bank_tujuan'],
                    'ket_tujuan' => $rincian_data[$data]['ket_tujuan'],
                    'nilai' => $rincian_data[$data]['nilai'],
                    'kd_skpd' => $rincian_data[$data]['kd_skpd'],
                    'kd_bp' => $kd_skpd,
                    'status_upload' => $rincian_data[$data]['status_upload'],
                    'tgl_validasi' => $tanggal_validasi,
                    'status_validasi' => '1',
                    'no_validasi' => $no_validasi,
                ];
                DB::table('trvalidasi_cmsbank_bidang')->insert($data);
            }

            // $data1 = DB::table('trvalidasi_cmsbank_bidang as a')
            //     ->where(['a.kd_bp' => $kd_skpd, 'a.no_validasi' => $no_validasi])
            //     ->select('a.no_bukti', 'a.kd_skpd', 'a.kd_bp', 'a.tgl_validasi', 'a.status_validasi');

            // DB::table('tr_setorpelimpahan_bank_cms as c')
            //     ->joinSub($data1, 'd', function ($join) {
            //         $join->on('c.no_bukti', '=', 'd.no_bukti');
            //         $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            //     })
            //     ->whereRaw('left(c.kd_skpd,17) = left(?,17)', $kd_skpd)
            //     ->update([
            //         'c.status_validasi' => DB::raw("d.status_validasi"),
            //         'c.tgl_validasi' => DB::raw("d.tgl_validasi"),
            //     ]);

            // $data_transout1 = DB::table('tr_setorpelimpahan_bank_cms as a')
            //     ->join('trvalidasi_cmsbank_bidang as b', function ($join) {
            //         $join->on('a.no_bukti', '=', 'b.no_bukti');
            //         $join->on('a.kd_skpd_sumber', '=', 'b.kd_bp');
            //     })
            //     ->where(['b.no_validasi' => $no_validasi, 'b.kd_bp' => $kd_skpd])
            //     ->select(DB::raw("RTRIM(a.no_kas)"), 'a.tgl_kas', 'a.no_bukti', 'a.tgl_bukti', 'a.kd_skpd', 'a.nilai', 'a.jenis_spp', 'a.keterangan', 'a.kd_skpd_sumber');

            // DB::table('tr_setorpelimpahan_bank')
            //     ->insertUsing(['no_kas', 'tgl_kas', 'no_bukti', 'tgl_bukti', 'kd_skpd', 'nilai', 'jenis_spp', 'keterangan', 'kd_skpd_sumber'], $data_transout1);

            DB::update("UPDATE tr_setorpelimpahan_bank_cms SET tr_setorpelimpahan_bank_cms.status_validasi = Table_B.status_validasi,
            tr_setorpelimpahan_bank_cms.tgl_validasi = Table_B.tgl_validasi FROM tr_setorpelimpahan_bank_cms
            INNER JOIN (select a.no_bukti,a.kd_skpd,a.kd_bp,a.tgl_validasi,a.status_validasi from trvalidasi_cmsbank_bidang a
            where a.kd_bp=? and no_validasi=?) AS Table_B ON tr_setorpelimpahan_bank_cms.no_bukti = Table_B.no_bukti AND tr_setorpelimpahan_bank_cms.kd_skpd = Table_B.kd_skpd where left(tr_setorpelimpahan_bank_cms.kd_skpd,17)=left(?,17)", [$kd_skpd, $no_validasi, $kd_skpd]);

            DB::insert("INSERT INTO tr_setorpelimpahan_bank (no_kas, tgl_kas, no_bukti, tgl_bukti, kd_skpd, nilai, jenis_spp, keterangan, kd_skpd_sumber) SELECT rtrim(a.no_kas), a.tgl_kas, a.no_bukti, a.tgl_bukti, a.kd_skpd, a.nilai, a.jenis_spp, a.keterangan, a.kd_skpd_sumber FROM tr_setorpelimpahan_bank_cms a left join trvalidasi_cmsbank_bidang b on b.no_bukti=a.no_bukti and a.kd_skpd_sumber=b.kd_bp WHERE b.no_validasi=? and b.kd_bp=?", [$no_validasi, $kd_skpd]);


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
        $data = DB::table('tr_setorpelimpahan_bank_cms as a')->select('a.*', 'b.no_upload', 'c.status_ambil as status_ambil1')->leftJoin('trdupload_cmsbank_bidang as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->leftJoin('tr_setorpelimpahan_bank as c', function ($join) {
            $join->on('a.no_bukti', '=', 'c.no_bukti');
            $join->on('a.kd_skpd', '=', 'c.kd_skpd');
        })->where(['a.kd_skpd_sumber' => $kd_skpd, 'a.status_validasi' => '1'])->orderBy(DB::raw("CAST(a.no_bukti as int)"))->orderBy('a.kd_skpd')->get();
        return Datatables::of($data)->addIndexColumn()->make(true);
        // return view('skpd.pelimpahan.validasi');
    }

    public function batalValidasi(Request $request)
    {
        $no_kas = $request->no_kas;
        $no_bukti = $request->no_bukti;
        $kd_skpd = $request->kd_skpd;
        $kd_bp = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_setorpelimpahan_bank')->where(['kd_skpd_sumber' => $kd_bp, 'no_kas' => $no_kas, 'no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            $data1 = DB::table('trvalidasi_cmsbank_bidang as a')->where(['a.kd_bp' => $kd_bp, 'a.no_bukti' => $no_bukti])->select('a.no_bukti', 'a.kd_skpd', 'a.kd_bp', 'a.tgl_validasi', 'a.status_validasi');

            DB::table('tr_setorpelimpahan_bank_cms as c')->joinSub($data1, 'd', function ($join) {
                $join->on('c.no_bukti', '=', 'd.no_bukti');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->whereRaw('left(c.kd_skpd,17) = left(?,17)', $kd_bp)->update([
                'c.status_validasi' => '0',
                'c.tgl_validasi' => null,
            ]);

            DB::table('trvalidasi_cmsbank_bidang')->where(['kd_bp' => $kd_bp, 'no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

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
