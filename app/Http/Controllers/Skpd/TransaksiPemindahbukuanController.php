<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransaksiPemindahbukuanController extends Controller
{
    public function index()
    {
        return view('skpd.transaksi_pemindahbukuan.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhtransout as a')->select('a.*', DB::raw("'' as nokas_pot"), DB::raw("'' as tgl_pot"), DB::raw("(SELECT COUNT(*) FROM trhtrmpot WHERE no_kas=a.no_bukti AND kd_skpd=a.kd_skpd) as kete"), DB::raw("(SELECT COUNT(*) FROM trlpj z JOIN trhlpj v ON v.no_lpj=z.no_lpj WHERE v.jenis=a.jns_spp AND z.no_bukti=a.no_bukti AND z.kd_skpd=a.kd_skpd) as ketlpj"), DB::raw("'0' as ketspj"))->selectRaw("(SELECT rekening FROM ms_skpd WHERE kd_skpd=?) as rekening_awal", [$kd_skpd])->where(['a.panjar' => '0', 'a.kd_skpd' => $kd_skpd, 'a.pay' => 'BANK'])->orderBy(DB::raw("CAST(a.no_bukti as numeric)"))->orderBy('a.kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.transaksi_pemindahbukuan.edit", $row->no_bukti) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->ketlpj != 1) {
                $btn .= '<a href="javascript:void(0);" onclick="hapusTransaksi(' . $row->no_bukti . ');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            }
            return $btn;

        })->rawColumns(['aksi'])->make(true);
        return view('skpd.transaksi_pemindahbukuan.index');
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_ang = status_anggaran();

        if ($jns_ang == '0') {
            return redirect()->back()->with(['message' => 'DPA Belum Disahkan!', 'alert' => 'alert-danger']);
        }

        $data = [
            'rekening_awal' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->get(),
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_kegiatan' => DB::table('trdrka as a')->join('trskpd as b', function ($join) {
                $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan', DB::raw("SUM(a.nilai) as total"))->where(['a.kd_skpd' => $kd_skpd, 'b.status_sub_kegiatan' => '1', 'b.jns_ang' => $jns_ang])->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->orderBy('a.kd_sub_kegiatan')->get(),
            'data_rek_tujuan' => DB::table('ms_rekening_bank_online as a')->where(['kd_skpd' => $kd_skpd])->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nmbank"))->orderBy('a.nm_rekening')->get(),
            'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->get(),
            'persen' => DB::table('config_app')->select('persen_kkpd', 'persen_tunai')->first(),
            'no_urut' => no_urut($kd_skpd)
        ];
        return view('skpd.transaksi_pemindahbukuan.create')->with($data);
    }

    public function simpanTransaksi(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_urut = no_urut($kd_skpd);
            // $no_urut = $data['no_bukti'];

            // TRHTRANSOUT
            DB::table('trhtransout')->where(['no_bukti' => $no_urut, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trhtransout')->insert([
                'no_kas' => $no_urut,
                'tgl_kas' => $data['tgl_voucher'],
                'no_bukti' => $no_urut,
                'tgl_bukti' => $data['tgl_voucher'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $kd_skpd,
                'nm_skpd' => $data['nm_skpd'],
                'total' => $data['total_belanja'],
                'no_tagih' => '',
                'sts_tagih' => '0',
                'tgl_tagih' => '',
                'jns_spp' => $data['beban'],
                'pay' => $data['pembayaran'],
                'no_kas_pot' => $no_urut,
                'panjar' => '0',
                'no_sp2d' => $data['sp2d'],
            ]);

            // TRDTRANSOUT
            DB::table('trdtransout')->where(['no_bukti' => $no_urut, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trdtransout_transfer')->where(['no_bukti' => $no_urut, 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_rekening'])) {
                DB::table('trdtransout')->insert(array_map(function ($value) use ($no_urut, $kd_skpd) {
                    return [
                        'no_bukti' => $no_urut,
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

            // TRDTRANSOUT_TRANSFER
            if (isset($data['rincian_rek_tujuan'])) {
                DB::table('trdtransout_transfer')->insert(array_map(function ($value) use ($no_urut) {
                    return [
                        'no_bukti' => $no_urut,
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
                'message' => '1',
                'no_bukti' => $no_urut
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapusTransaksi(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trdtransout')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trhtransout')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trdtransout_transfer')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

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
    public function edit($no_bukti)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_ang = status_anggaran();
        $data = [
            'data_transaksi' => DB::table('trhtransout as a')->join('trdtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->leftJoin('trdtransout_transfer as c', function ($join) {
                $join->on('b.no_bukti', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->select('a.*', 'c.rekening_awal')->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->first(),
            'list_rekening_belanja' => DB::table('trdtransout as a')->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->get(),
            'list_rekening_tujuan' => DB::table('trdtransout_transfer as a')->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.*')->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->get(),
            'rekening_awal' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_skpd')->get(),
            'daftar_kegiatan' => DB::table('trdrka as a')->join('trskpd as b', function ($join) {
                $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan', DB::raw("SUM(a.nilai) as total"))->where(['a.kd_skpd' => $kd_skpd, 'b.status_sub_kegiatan' => '1', 'b.jns_ang' => $jns_ang])->groupBy('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')->orderBy('a.kd_sub_kegiatan')->get(),
            'data_rek_tujuan' => DB::table('ms_rekening_bank_online as a')->where(['kd_skpd' => $kd_skpd])->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nmbank"))->orderBy('a.nm_rekening')->get(),
            'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->get(),
            'persen' => DB::table('config_app')->select('persen_kkpd', 'persen_tunai')->first(),
        ];

        return view('skpd.transaksi_pemindahbukuan.edit')->with($data);
    }

    public function editTransaksi(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // TRHTRANSOUT
            DB::table('trhtransout')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trhtransout')->insert([
                'no_kas' => $data['no_bukti'],
                'tgl_kas' => $data['tgl_voucher'],
                'no_bukti' => $data['no_bukti'],
                'tgl_bukti' => $data['tgl_voucher'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => date('Y-m-d H:i:s'),
                'kd_skpd' => $kd_skpd,
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
            ]);

            // TRDTRANSOUT
            DB::table('trdtransout')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trdtransout_transfer')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

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
                        'kd_skpd' => $kd_skpd,
                        'sumber' => $value['sumber'],
                        'volume' => $value['volume'],
                        'satuan' => $value['satuan'],
                    ];
                }, $data['rincian_rekening']));
            }

            // TRDTRANSOUT_TRANSFER
            if (isset($data['rincian_rek_tujuan'])) {
                DB::table('trdtransout_transfer')->insert(array_map(function ($value) use ($data) {
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
}
