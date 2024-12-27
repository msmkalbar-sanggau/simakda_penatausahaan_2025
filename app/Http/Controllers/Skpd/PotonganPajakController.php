<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Exception;

class PotonganPajakController extends Controller
{
    public function index()
    {
        return view('skpd.potongan_pajak.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtrmpot')->where(['kd_skpd' => $kd_skpd])->orderBy('no_bukti')->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.potongan_pajak.edit", $row->no_bukti) . '" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="fa fa-eye"></i></a>';
            if ($row->status != 1) {
                $btn .= '<a href="javascript:void(0);" onclick="hapusPotongan(' . $row->no_bukti . ');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            } else {
                $btn .= '';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $potongan1 = DB::table('trhtransout as a')->join('trdtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd])->whereIn('a.jns_spp', ['1', '2', '3'])->where(function ($query) {
            $query->where('a.no_panjar', '<>', '1')->orWhereNull('a.no_panjar');
        })->select('a.no_bukti', 'tgl_bukti', 'a.no_sp2d', 'a.ket', 'a.kd_skpd', DB::raw("SUM(b.nilai) as nilai"))->groupBy('a.no_bukti', 'tgl_bukti', 'a.no_sp2d', 'a.ket', 'a.kd_skpd');

        $potongan2 = DB::table('tr_panjar as a')->where(['a.kd_skpd' => $kd_skpd])->select('a.no_panjar as no_bukti', 'a.tgl_panjar as tgl_bukti', DB::raw("'' as no_sp2d"), 'a.keterangan as ket', 'a.kd_skpd', 'nilai')->union($potongan1);

        $potongan = DB::table(DB::raw("({$potongan2->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($potongan2)
            ->orderBy('tgl_bukti')
            ->orderBy(DB::raw("CAST(no_bukti as int)"))
            ->get();

        $rekanan1 = DB::table('trhspp')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5);

        $rekanan2 = DB::table('trhtrmpot')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->take(5)->unionAll($rekanan1);

        $rekanan3 = DB::table('trhtrmpot_cmsbank')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw("LEN(nmrekan)>1")->where(['kd_skpd' => $kd_skpd])->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($rekanan2);

        $rekanan4 = DB::query()->select(DB::raw("'Input Manual' as nmrekan"), DB::raw("'' as pimpinan"), DB::raw("'' as npwp"), DB::raw("'' as alamat"))->unionAll($rekanan3);

        $rekanan = DB::table(DB::raw("({$rekanan4->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($rekanan4)
            ->get();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_potongan' => $potongan,
            'daftar_sp2d' => DB::table('trhtransout as a')->join('trdtransout as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_bukti', '=', 'b.no_bukti');
            })->where(['b.kd_skpd' => $kd_skpd])->select('b.no_sp2d', 'a.jns_spp')->groupBy('b.no_sp2d', 'jns_spp')->orderBy('no_sp2d')->get(),
            'daftar_rekanan' => $rekanan,
            'daftar_rek' => DB::table('ms_pot')->select('kd_rek6', 'nm_rek6')->get(),
            'tahun_anggaran' => tahun_anggaran()
        ];

        return view('skpd.potongan_pajak.create')->with($data);
    }

    public function cariKegiatan(Request $request)
    {
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('trdtransout as a')->join('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_sp2d' => $no_sp2d, 'a.kd_skpd' => $kd_skpd])->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->distinct()->get();

        return response()->json($data);
    }

    public function cariRekening(Request $request)
    {
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $no_sp2d = $request->no_sp2d;
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('trdtransout as a')->join('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_sp2d' => $no_sp2d, 'a.kd_sub_kegiatan' => $kd_sub_kegiatan])->select('a.kd_rek6', 'a.nm_rek6')->distinct()->get();

        return response()->json($data);
    }

    public function simpanPotongan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // NOMOR BUKTI
            $no_bukti = no_urut($kd_skpd);
            $bayar = $data['pembayaran'];

            if ($bayar == 'BANK') {
                $bayar1 = "BANK";
            } else {
                $bayar1 = '';
            }

            // TRHTRMPOT
            DB::table('trhtrmpot')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $no_bukti])->delete();

            DB::table('trhtrmpot')->insert([
                'no_bukti' => $no_bukti,
                'tgl_bukti' => $data['tgl_bukti'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => '',
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
                'no_kas' => $data['no_transaksi'],
                'pay' => $bayar1,
            ]);
            // TRDTRMPOT
            DB::table('trdtrmpot')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_potongan'])) {
                DB::table('trdtrmpot')->insert(array_map(function ($value) use ($data, $no_bukti) {
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
                        'kd_rek_trans' => $value['kd_rek_trans'],
                        'ebilling' => $value['no_billing'],
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

    public function edit($no_bukti)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $potongan1 = DB::table('trhtransout as a')->join('trdtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd])->whereIn('a.jns_spp', ['1', '2', '3'])->where(function ($query) {
            $query->where('a.no_panjar', '<>', '1')->orWhereNull('a.no_panjar');
        })->select('a.no_bukti', 'tgl_bukti', 'a.no_sp2d', 'a.ket', 'a.kd_skpd', DB::raw("SUM(b.nilai) as nilai"))->groupBy('a.no_bukti', 'tgl_bukti', 'a.no_sp2d', 'a.ket', 'a.kd_skpd');

        $potongan2 = DB::table('tr_panjar as a')->where(['a.kd_skpd' => $kd_skpd])->select('a.no_panjar as no_bukti', 'a.tgl_panjar as tgl_bukti', DB::raw("'' as no_sp2d"), 'a.keterangan as ket', 'a.kd_skpd', 'nilai')->union($potongan1);

        $potongan = DB::table(DB::raw("({$potongan2->toSql()}) AS sub"))
            ->select('*')
            ->mergeBindings($potongan2)
            ->orderBy('tgl_bukti')
            ->orderBy(DB::raw("CAST(no_bukti as int)"))
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
            'data_potongan' => DB::table('trhtrmpot as a')->join('trdtrmpot as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->select('a.*', 'b.kd_rek_trans')->first(),
            'daftar_list_potongan' => DB::table('trdtrmpot as a')->join('trhtrmpot as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.*')->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->get(),
            'daftar_potongan' => $potongan,
            'daftar_sp2d' => DB::table('trhtransout as a')->join('trdtransout as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_bukti', '=', 'b.no_bukti');
            })->where(['b.kd_skpd' => $kd_skpd])->select('b.no_sp2d', 'a.jns_spp')->groupBy('b.no_sp2d', 'jns_spp')->orderBy('no_sp2d')->get(),
            'daftar_rekanan' => $rekanan,
            'daftar_rek' => DB::table('ms_pot')->select('kd_rek6', 'nm_rek6')->get(),
            'tahun_anggaran' => tahun_anggaran(),
        ];

        return view('skpd.potongan_pajak.edit')->with($data);
    }

    public function editPotongan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // TRHTRMPOT
            DB::table('trhtrmpot')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])->delete();

            DB::table('trhtrmpot')->insert([
                'no_bukti' => $data['no_bukti'],
                'tgl_bukti' => $data['tgl_bukti'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => '',
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
                'no_kas' => $data['no_transaksi'],
                'pay' => $data['pembayaran'],
            ]);
            // TRDTRMPOT
            DB::table('trdtrmpot')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_potongan'])) {
                DB::table('trdtrmpot')->insert(array_map(function ($value) use ($data) {
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
                        'kd_rek_trans' => $value['kd_rek_trans'],
                        'ebilling' => $value['no_billing'],
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
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trdtrmpot')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trhtrmpot')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

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
