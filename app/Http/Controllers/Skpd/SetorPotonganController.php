<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Exception;

class SetorPotonganController extends Controller
{
    public function index()
    {
        return view('skpd.setor_potongan.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhstrpot')->select('no_bukti', 'no_ntpn', 'tgl_bukti', 'no_terima', 'kd_skpd', 'no_sp2d', DB::raw("RTRIM(jns_spp) as jns_spp"), 'nm_skpd', 'nm_sub_kegiatan', 'kd_sub_kegiatan', 'nmrekan', 'pimpinan', 'alamat', 'npwp', 'ket', 'nilai', 'pay')->where(['kd_skpd' => $kd_skpd])->orderBy('no_bukti')->orderBy('kd_skpd')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if ($row->jns_spp == 1 || $row->jns_spp == 2) {
                $btn = '<a href="' . route("skpd.setor_potongan.edit", $row->no_bukti) . '" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="fa fa-eye"></i></a>';
                $btn .= '<a href="javascript:void(0);" onclick="hapusPotongan(\'' . $row->no_bukti . '\', \'' . $row->no_terima . '\', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            } else {
                $btn = '<a href="' . route("skpd.setor_potongan.edit", $row->no_bukti) . '" class="btn btn-primary btn-sm" style="margin-right:4px"><i class="fa fa-eye"></i></a>';
            }

            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function loadPotongan(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_terima = $request->no_terima;

        $data = DB::table('trdtrmpot')->where(['no_bukti' => $no_terima, 'kd_skpd' => $kd_skpd])->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="javascript:void(0);" onclick="tambahNtpn(' . $row->id . ',\'' . $row->kd_rek6 . '\',\'' . $row->nm_rek6 . '\',\'' . $row->npwp . '\',\'' . $row->nilai . '\',\'' . $row->ntpn . '\',\'' . $row->ebilling . '\');" class="btn btn-success btn-sm"><i class="fas fa-edit"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function totalPotongan(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_terima = $request->no_terima;

        $data = DB::table('trdtrmpot')->select(DB::raw("sum(nilai) as nilai"))->where(['no_bukti' => $no_terima, 'kd_skpd' => $kd_skpd])->first();
        return response()->json($data);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'daftar_terima' => DB::table('trhtrmpot')->where(['kd_skpd' => $kd_skpd, 'status' => '0'])->orderBy('no_bukti')->get(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_urut' => no_urut($kd_skpd)
        ];

        return view('skpd.setor_potongan.create')->with($data);
    }

    public function simpanNtpn(Request $request)
    {
        $id_terima = $request->id_terima;
        $id_setor = $request->id_setor;
        $no_terima = $request->no_terima;
        $kd_rek6 = $request->kd_rek6;
        $nm_rek6 = $request->nm_rek6;
        $ntpn_validasi = $request->ntpn_validasi;
        $kd_skpd = $request->kd_skpd;
        $id_billing_validasi = $request->id_billing_validasi;

        DB::beginTransaction();
        try {
            DB::table('trdtrmpot')->where(['no_bukti' => $no_terima, 'kd_skpd' => $kd_skpd, 'kd_rek6' => $kd_rek6, 'id' => $id_terima])->update([
                'ntpn' => $ntpn_validasi,
                'ebilling' => $id_billing_validasi
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

    public function simpanPotongan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // NOMOR BUKTI
            // $no_bukti = no_urut($kd_skpd);
            $no_bukti = $data['no_bukti'];
            $bayar = $data['pembayaran'];

            if ($bayar == 'BANK') {
                $bayar1 = "BANK";
            } else {
                $bayar1 = '';
            }

            // TRHSTRPOT
            DB::table('trhstrpot')->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $no_bukti])->delete();

            DB::table('trhstrpot')->insert([
                'no_bukti' => $no_bukti,
                'tgl_bukti' => $data['tgl_bukti'],
                'ket' => $data['keterangan'],
                'username' => Auth::user()->nama,
                'tgl_update' => '',
                'kd_skpd' => $data['kd_skpd'],
                'nm_skpd' => $data['nm_skpd'],
                'no_terima' => $data['no_terima'],
                'npwp' => $data['npwp'],
                'jns_spp' => $data['beban'],
                'nilai' => $data['total_potongan'],
                'no_sp2d' => $data['no_sp2d'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                'kd_rek6' => $data['kd_rekening'],
                'nm_rek6' => $data['nm_rekening'],
                'nmrekan' => $data['rekanan'],
                'pimpinan' => $data['pimpinan'],
                'alamat' => $data['alamat'],
                'pay' => $bayar1
            ]);

            // TRHTRMPOT
            DB::table('trhtrmpot')->where(['no_bukti' => $data['no_terima'], 'kd_skpd' => $kd_skpd])->update([
                'status' => '1'
            ]);

            // TRDSTRPOT
            DB::table('trdstrpot')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            $data_potongan = DB::table('trdtrmpot')->where(['no_bukti' => $data['no_terima'], 'kd_skpd' => $kd_skpd])->select(DB::raw("'$no_bukti' as no_bukti"), 'kd_rek6', 'nm_rek6', 'nilai', 'kd_skpd', 'kd_rek_trans', 'ntpn', 'rekanan', 'npwp', 'ebilling', 'id');

            DB::table('trdstrpot')->insertUsing(['no_bukti', 'kd_rek6', 'nm_rek6', 'nilai', 'kd_skpd', 'kd_rek_trans', 'ntpn', 'rekanan', 'npwp', 'ebilling', 'id_terima'], $data_potongan);

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
        $setor = DB::table('trhstrpot as a')->join('trdstrpot as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->select('a.*')->first();
        $data = [
            'data_setor' => $setor,
            'tahun_anggaran' => tahun_anggaran(),
            'total_potongan' => DB::table('trdtrmpot')->select(DB::raw("sum(nilai) as nilai"))->where(['no_bukti' => $setor->no_terima, 'kd_skpd' => $kd_skpd])->first(),
            'ebilling'  => collect(DB::select("SELECT ebilling from trdstrpot where no_bukti=? and kd_skpd=? ", [$no_bukti, $kd_skpd]))->first(),
        ];
        // dd($data);

        return view('skpd.setor_potongan.edit')->with($data);
    }

    public function editPotongan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            // TRHSTRPOT
            DB::table('trhstrpot')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $data['no_bukti']])->update([
                'tgl_bukti' => $data['tgl_bukti'],
                'pay' => $data['pembayaran'],
            ]);

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

    public function editNtpn(Request $request)
    {
        $id_terima = $request->id_terima;
        $no_terima = $request->no_terima;
        $no_bukti = $request->no_bukti;
        $kd_rek6 = $request->kd_rek6;
        $ntpn_validasi = $request->ntpn_validasi;
        $kd_skpd = $request->kd_skpd;
        $id_billing_validasi = $request->id_billing_validasi;

        DB::beginTransaction();
        try {
            DB::table('trdtrmpot')->where(['no_bukti' => $no_terima, 'kd_skpd' => $kd_skpd, 'kd_rek6' => $kd_rek6, 'id' => $id_terima])->update([
                'ntpn' => $ntpn_validasi,
                'ebilling' => $id_billing_validasi
            ]);

            DB::table('trdstrpot')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd, 'kd_rek6' => $kd_rek6, 'id_terima' => $id_terima])->update([
                'ntpn' => $ntpn_validasi,
                'ebilling' => $id_billing_validasi
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

    public function hapusPotongan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $no_terima = $request->no_terima;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trdstrpot')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('trhstrpot')->where(['no_bukti' => $no_bukti, 'kd_skpd' => $kd_skpd])->delete();

            DB::update("UPDATE trhtrmpot SET status = '0' WHERE no_bukti=? AND kd_skpd=?", [$no_terima, $kd_skpd]);

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
