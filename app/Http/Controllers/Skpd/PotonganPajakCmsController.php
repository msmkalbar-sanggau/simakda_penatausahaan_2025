<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class PotonganPajakCmsController extends Controller
{
    public function index()
    {
        return view('skpd.potongan_pajak_cms.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtrmpot_cmsbank as a')->select('a.*')->selectRaw("(SELECT status_upload FROM trhtransout_cmsbank WHERE no_voucher=a.no_voucher and kd_skpd=?)  as status_upload", [$kd_skpd])->selectRaw("(SELECT status_validasi FROM trhtransout_cmsbank WHERE no_voucher=a.no_voucher and kd_skpd=?)  as status_validasi", [$kd_skpd])->where(['a.kd_skpd' => $kd_skpd])->orderBy('no_bukti')->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.potongan_pajak_cms.edit", $row->no_bukti) . '" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="fa fa-eye"></i></a>';
            if ($row->status != 1) {
                $btn .= '<a href="javascript:void(0);" onclick="hapusPotongan(' . $row->no_bukti . ',\'' . $row->no_voucher . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            } else {
                $btn .= '';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('skpd.potongan_pajak_cms.index');
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $potongan1 = DB::table('trhtransout_cmsbank as a')->join('trdtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd])->whereIn('a.jns_spp', ['1', '2', '3'])->where('a.status_upload', '!=', '1')->whereRaw("a.no_voucher NOT IN (SELECT no_voucher FROM trhtrmpot_cmsbank a WHERE a.kd_skpd=?)", $kd_skpd)->distinct()->select('a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'b.no_sp2d', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'b.kd_rek6', 'b.nm_rek6', 'a.jns_spp', 'a.total');

        $potongan3 = DB::table('tr_panjar_cmsbank as a')->where(['a.kd_skpd' => $kd_skpd])->where('a.status_upload', '!=', '1')->whereRaw("a.no_panjar NOT IN (SELECT no_voucher FROM trhtrmpot_cmsbank a WHERE a.kd_skpd=?)", $kd_skpd)->select(DB::raw("'' as no_tgl"), 'a.no_panjar as no_voucher', 'a.tgl_panjar as tgl_voucher', DB::raw("'' as no_sp2d"), 'a.kd_sub_kegiatan', DB::raw("'' as nm_sub_kegiatan"), DB::raw("'' as kd_rek6"), DB::raw("'' as nm_rek6"), DB::raw("'1' as jns_spp"), 'nilai as total')->union($potongan1);

        $potongan = DB::table(DB::raw("({$potongan3->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($potongan3)
            ->orderBy('tgl_voucher')
            ->orderBy('no_voucher')
            ->get();

        $rekanan1 = DB::table('trhspp')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5);

        $rekanan2 = DB::table('trhtrmpot')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5)->unionAll($rekanan1);

        $rekanan3 = DB::table('trhtrmpot_cmsbank')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($rekanan2);

        $rekanan4 = DB::query()->select(DB::raw("'Input Manual' as nmrekan"), DB::raw("'' as pimpinan"), DB::raw("'' as npwp"), DB::raw("'' as alamat"))->unionAll($rekanan3);

        $rekanan = DB::table(DB::raw("({$rekanan4->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($rekanan4)
            ->get();

        $nomor1 = DB::table('trhtransout_cmsbank')->select('no_voucher as nomor', DB::raw("'Daftar Transaksi Non Tunai' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd]);
        $nomor2 = DB::table('trhtrmpot_cmsbank')->select('no_bukti as nomor', DB::raw("'Potongan Pajak Transaksi Non Tunai' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->union($nomor1);
        $nomor3 = DB::table('tr_panjar_cmsbank')->select('no_panjar as nomor', DB::raw("'Daftar Panjar' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->union($nomor2);

        $nomor = DB::table(DB::raw("({$nomor3->toSql()}) AS sub"))
            ->select(DB::raw("CASE WHEN MAX(nomor+1) is null THEN 1 else MAX(nomor+1) END as nomor"))
            ->mergeBindings($nomor3)
            ->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_potongan' => $potongan,
            'daftar_sp2d' => DB::table('trhtransout_cmsbank as a')->join('trdtransout_cmsbank as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_voucher', '=', 'b.no_voucher');
            })->where(['b.kd_skpd' => $kd_skpd])->select('b.no_sp2d', 'a.jns_spp')->groupBy('b.no_sp2d', 'jns_spp')->orderBy('no_sp2d')->get(),
            'daftar_rekanan' => $rekanan,
            'daftar_rek' => DB::table('ms_pot')->select('kd_rek6', 'nm_rek6')->get(),
            'tahun_anggaran' => tahun_anggaran(),
            'nomor' => $nomor->nomor
        ];

        return view('skpd.potongan_pajak_cms.create')->with($data);
    }

    public function cariKegiatan(Request $request)
    {
        $no_transaksi = $request->no_transaksi;
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = Auth::user()->kd_skpd;

        $data1 = DB::table('trdtransout_cmsbank as a')->join('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_sp2d' => $no_sp2d])->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->distinct();

        $data2 = DB::table('tr_panjar_cmsbank as a')->join('trdrka as b', function ($join) {
            $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
        })->where(['a.kd_skpd' => $kd_skpd, 'a.no_panjar' => $no_transaksi])->select('a.kd_sub_kegiatan', 'b.nm_sub_kegiatan')->union($data1);

        $data = DB::table(DB::raw("({$data2->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($data2)
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
            // $nomor1 = DB::table('trhtransout_cmsbank')->select('no_voucher as nomor', DB::raw("'Daftar Transaksi Non Tunai' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd]);
            // $nomor2 = DB::table('trhtrmpot_cmsbank')->select('no_bukti as nomor', DB::raw("'Potongan Pajak Transaksi Non Tunai' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->union($nomor1);
            // $nomor3 = DB::table('tr_panjar_cmsbank')->select('no_panjar as nomor', DB::raw("'Daftar Panjar' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->union($nomor2);

            // $nomor = DB::table(DB::raw("({$nomor3->toSql()}) AS sub"))
            //     ->select(DB::raw("CASE WHEN MAX(nomor+1) is null THEN 1 else MAX(nomor+1) END as nomor"))
            //     ->mergeBindings($nomor3)
            //     ->first();
            // $no_bukti = $nomor->nomor;
            // TRHTRMPOT
            DB::table('trhtrmpot_cmsbank')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])->delete();

            DB::table('trhtrmpot_cmsbank')->insert([
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

            DB::table('trhtransout_cmsbank')->where(['kd_skpd' => $data['kd_skpd'], 'no_voucher' => $data['no_transaksi']])->update([
                'status_trmpot' => '1',
            ]);

            // TRDTRMPOT
            DB::table('trdtrmpot_cmsbank')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_potongan'])) {
                DB::table('trdtrmpot_cmsbank')->insert(array_map(function ($value) use ($data) {
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

    public function edit($no_bukti)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $potongan1 = DB::table('trhtransout_cmsbank as a')->join('trdtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd])->whereIn('a.jns_spp', ['1', '2', '3'])->where('a.status_upload', '!=', '1')->whereRaw("a.no_voucher NOT IN (SELECT no_voucher FROM trhtrmpot_cmsbank a WHERE a.kd_skpd=?)", $kd_skpd)->distinct()->select('a.no_tgl', 'a.no_voucher', 'a.tgl_voucher', 'b.no_sp2d', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'b.kd_rek6', 'b.nm_rek6', 'a.jns_spp', 'a.total');

        $potongan3 = DB::table('tr_panjar_cmsbank as a')->where(['a.kd_skpd' => $kd_skpd])->where('a.status_upload', '!=', '1')->whereRaw("a.no_panjar NOT IN (SELECT no_voucher FROM trhtrmpot_cmsbank a WHERE a.kd_skpd=?)", $kd_skpd)->select(DB::raw("'' as no_tgl"), 'a.no_panjar as no_voucher', 'a.tgl_panjar as tgl_voucher', DB::raw("'' as no_sp2d"), 'a.kd_sub_kegiatan', DB::raw("'' as nm_sub_kegiatan"), DB::raw("'' as kd_rek6"), DB::raw("'' as nm_rek6"), DB::raw("'1' as jns_spp"), 'nilai as total')->union($potongan1);

        $potongan = DB::table(DB::raw("({$potongan3->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($potongan3)
            ->orderBy('tgl_voucher')
            ->orderBy('no_voucher')
            ->get();

        $rekanan1 = DB::table('trhspp')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5);

        $rekanan2 = DB::table('trhtrmpot')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5)->unionAll($rekanan1);

        $rekanan3 = DB::table('trhtrmpot_cmsbank')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($rekanan2);

        $rekanan = DB::table(DB::raw("({$rekanan3->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($rekanan3)
            ->get();

        $data = [
            'no_bukti' => $no_bukti,
            'data_potongan' => DB::table('trhtrmpot_cmsbank')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->first(),
            'daftar_list_potongan' => DB::table('trdtrmpot_cmsbank as a')->join('trhtrmpot_cmsbank as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.*')->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->get(),
            'daftar_potongan' => $potongan,
            'daftar_sp2d' => DB::table('trhtransout_cmsbank as a')->join('trdtransout_cmsbank as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_voucher', '=', 'b.no_voucher');
            })->where(['b.kd_skpd' => $kd_skpd])->select('b.no_sp2d', 'a.jns_spp')->groupBy('b.no_sp2d', 'jns_spp')->orderBy('no_sp2d')->get(),
            'daftar_rekanan' => $rekanan,
            'daftar_rek' => DB::table('ms_pot')->select('kd_rek6', 'nm_rek6')->get(),
            'tahun_anggaran' => tahun_anggaran()
        ];

        return view('skpd.potongan_pajak_cms.edit')->with($data);
    }

    public function editPotongan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // TRHTRMPOT
            DB::table('trhtrmpot_cmsbank')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])->delete();

            DB::table('trhtrmpot_cmsbank')->insert([
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

            DB::table('trhtransout_cmsbank')->where(['kd_skpd' => $data['kd_skpd'], 'no_voucher' => $data['no_transaksi']])->update([
                'status_trmpot' => '1',
            ]);

            // TRDTRMPOT
            DB::table('trdtrmpot_cmsbank')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_potongan'])) {
                DB::table('trdtrmpot_cmsbank')->insert(array_map(function ($value) use ($data) {
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
            DB::table('trhtransout_cmsbank')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])->update([
                'status_trmpot' => '0'
            ]);

            DB::table('trdtrmpot_cmsbank')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trhtrmpot_cmsbank')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

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
