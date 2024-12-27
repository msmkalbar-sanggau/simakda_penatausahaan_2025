<?php

namespace App\Http\Controllers\Skpd\BOS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PengembalianBosController extends Controller
{
    public function index()
    {
        return view('skpd.pengembalian_bos.index');
    }

    public function load()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT a.* ,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) AS nm_skpd,(SELECT sum(jumlah) as jumlah FROM (
                SELECT count(*) as jumlah FROM trdsp2b where no_bukti=a.no_sts and kd_skpd =a.kd_skpd
                UNION ALL
                SELECT count(*) as jumlah FROM trdsp2h where no_bukti=a.no_sts and kd_skpd =a.kd_skpd
            )Z) as total_spb from trhkasin_pkd_bos a
                where a.kd_skpd=? and a.jns_trans in (?,?) order by CAST(no_sts as int)", [$kd_skpd, '5', '1']);

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("pengembalian_bos.edit", ['no_sts' => Crypt::encrypt($row->no_sts), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            if ($row->total_spb == 0) {
                $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_sts . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="uil-trash"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_rekening' => DB::select("SELECT kd_rek6,nm_rek6 from ms_rek6 where left(kd_rek6,4)=?", ['1101']),
            'no_urut' => no_urut($kd_skpd)
        ];

        return view('skpd.pengembalian_bos.create')->with($data);
    }

    public function kegiatan(Request $request)
    {
        $tanggal = $request->tanggal;
        $kd_skpd = Auth::user()->kd_skpd;

        $jns_ang = collect(DB::select("SELECT jns_ang from trhrka a inner join tb_status_anggaran b ON a.jns_ang=b.kode where a.kd_skpd =? and status=? and tgl_dpa <= ? order by tgl_dpa DESC", [$kd_skpd, '1', $tanggal]))->first();

        $data = DB::select("SELECT DISTINCT a.kd_sub_kegiatan,b.nm_sub_kegiatan,a.kd_program, a.total FROM trskpd a INNER JOIN ms_sub_kegiatan b ON a.kd_sub_kegiatan=b.kd_sub_kegiatan WHERE a.kd_skpd=? AND a.status_sub_kegiatan=? AND left(a.kd_sub_kegiatan,9) in (?,?) AND a.jns_ang=?", [$kd_skpd, '1', '1.01.02.1', '4.01.04.1', $jns_ang->jns_ang]);

        return response()->json($data);
    }

    public function sisaBos(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $satdik = $request->satdik;

        $data = collect(DB::select("SELECT isnull(sum(terima),0) as terima,isnull(sum(keluar),0) as keluar from (
        SELECT kd_skpd,kd_satdik,isnull(sum(nilai),0)as terima, 0 as keluar from tr_terima_bos z where kd_satdik=? and kd_skpd=? GROUP BY kd_skpd,kd_satdik
        UNION ALL
        select a.kd_skpd,kd_satdik,0,isnull(sum(nilai),0) from trhtransout_blud a INNER JOIN trdtransout_blud b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd where kd_satdik=? and a.kd_skpd=?
        GROUP BY a.kd_skpd,kd_satdik
        )s", [$satdik, $kd_skpd, $satdik, $kd_skpd]))->first();

        return response()->json($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhkasin_pkd_bos')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd])
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhkasin_pkd_bos')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_sts' => $data['no_kas']])
                ->delete();

            DB::table('trhkasin_pkd_bos')
                ->insert([
                    'no_kas' => $data['no_kas'],
                    'no_sts' => $data['no_kas'],
                    'tgl_kas' => $data['tgl_kas'],
                    'tgl_sts' => $data['tgl_kas'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'total' => $data['total'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'jns_trans' => $data['jenis_transaksi'],
                    'rek_bank' => '',
                    'jns_cp' => '4',
                    'bank' => $data['pembayaran'],
                    'kd_satdik' => $data['satdik'],
                    'nm_satdik' => $data['nama_satdik'],
                    'user_name' => Auth::user()->nama,
                    'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                ]);

            DB::table('trdkasin_pkd_bos')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_sts' => $data['no_kas']])
                ->delete();

            if (isset($data['rincian'])) {
                DB::table('trdkasin_pkd_bos')->insert(array_map(function ($value) use ($data, $kd_skpd) {
                    return [
                        'no_sts' => $data['no_kas'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'rupiah' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan']
                    ];
                }, $data['rincian']));
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

    public function edit($no_sts, $kd_skpd)
    {
        $no_sts = Crypt::decrypt($no_sts);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'bos' => collect(DB::select("SELECT a.* ,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) AS nm_skpd,(SELECT sum(jumlah) as jumlah FROM (
                SELECT count(*) as jumlah FROM trdsp2b where no_bukti=a.no_sts and kd_skpd =a.kd_skpd
                UNION ALL
                SELECT count(*) as jumlah FROM trdsp2h where no_bukti=a.no_sts and kd_skpd =a.kd_skpd
            )Z) as total_spb from trhkasin_pkd_bos a
                where a.kd_skpd=? and a.no_sts=? and a.jns_trans in (?,?) order by CAST(no_sts as int)", [$kd_skpd, $no_sts, '5', '1']))->first(),
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daftar_rekening' => DB::select("SELECT kd_rek6,nm_rek6 from ms_rek6 where left(kd_rek6,4)=?", ['1101']),
            'no_urut' => no_urut($kd_skpd),
            'detail_bos' => DB::select("SELECT a.* from trdkasin_pkd_bos a INNER JOIN trhkasin_pkd_bos b ON a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd WHERE b.no_sts=? and b.kd_skpd=?", [$no_sts, $kd_skpd])
        ];

        return view('skpd.pengembalian_bos.edit ')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhkasin_pkd_bos')
                ->where(['no_sts' => $data['no_kas'], 'kd_skpd' => $kd_skpd])
                ->count();

            if ($cek > 0 && $data['no_kas'] != $data['no_simpan']) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhkasin_pkd_bos')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_sts' => $data['no_kas']])
                ->delete();

            DB::table('trhkasin_pkd_bos')
                ->insert([
                    'no_kas' => $data['no_kas'],
                    'no_sts' => $data['no_kas'],
                    'tgl_kas' => $data['tgl_kas'],
                    'tgl_sts' => $data['tgl_kas'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keterangan' => $data['keterangan'],
                    'total' => $data['total'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'jns_trans' => $data['jenis_transaksi'],
                    'rek_bank' => '',
                    'jns_cp' => '4',
                    'bank' => $data['pembayaran'],
                    'kd_satdik' => $data['satdik'],
                    'nm_satdik' => $data['nama_satdik'],
                    'user_name' => Auth::user()->nama,
                    'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                ]);

            DB::table('trdkasin_pkd_bos')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_sts' => $data['no_kas']])
                ->delete();

            if (isset($data['rincian'])) {
                DB::table('trdkasin_pkd_bos')->insert(array_map(function ($value) use ($data, $kd_skpd) {
                    return [
                        'no_sts' => $data['no_kas'],
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'rupiah' => $value['nilai'],
                        'kd_skpd' => $data['kd_skpd'],
                        'kd_sub_kegiatan' => $data['kd_sub_kegiatan']
                    ];
                }, $data['rincian']));
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

    public function hapus(Request $request)
    {
        $no_sts = $request->no_sts;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhkasin_pkd_bos')
                ->where(['no_sts' => $no_sts, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::table('trdkasin_pkd_bos')
                ->where(['no_sts' => $no_sts, 'kd_skpd' => $kd_skpd])
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
}
