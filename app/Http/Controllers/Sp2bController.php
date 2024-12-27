<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Yajra\DataTables\Facades\DataTables;



class Sp2bController extends Controller
{
    public function indexSp2b()
    {
        $skpd = DB::table('ms_skpd_blud')->select('kd_skpd', 'nm_skpd')->first();
        $data = [
            'data_sp2b' => DB::table('trhsp2b_blud')->select('no_sp3b', 'kd_skpd', 'keterangan', 'bulan', 'no_lpj', 'no_sp2b', 'skpd', 'status', 'status_bud', 'tgl_sp2b', 'tgl_sp3b', 'tgl_awal', 'tgl_akhir', 'total')->where('kd_skpd', $skpd->kd_skpd)->get(),
            'bud'   => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->whereIn('kode', ['BUD','PPKD'])->get(),
        ];

        return view('bud.sp2b.index')->with($data);
    }

    public function loadData()
    {
        $skpd = DB::table('ms_skpd_blud')->select('kd_skpd', 'nm_skpd')->first();
        $data = DB::table('trhsp2b_blud')->select('no_sp3b', 'kd_skpd', 'keterangan', 'bulan', 'no_lpj', 'no_sp2b', 'skpd', 'status', 'status_bud', 'tgl_sp2b', 'tgl_sp3b', 'tgl_awal', 'tgl_akhir', 'total')->where(['kd_skpd' => $skpd->kd_skpd])->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("sp2b.edit", Crypt::encrypt($row->no_sp2b)) . '" class="btn btn-warning btn-sm" title="Edit SP2b"  style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_sp2b . '\');" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak SP2b" style="margin-right:4px"><i class="uil-print"></i></a>';
            $btn .=  '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_sp2b . '\');" class="btn btn-danger btn-sm" title="Hapus SP2b"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambahSp2b()
    {
        $skpd = DB::table('ms_skpd_blud')->select('kd_skpd', 'nm_skpd')->first();
        $data = [
            'blud' => $skpd,
            'data_sp2b' => DB::table('trhsp2b_blud')
                ->select('no_sp3b', 'kd_skpd', 'keterangan', 'bulan', 'no_lpj', 'no_sp2b', 'skpd', 'status', 'status_bud', 'tgl_sp2b', 'tgl_sp3b', 'tgl_awal', 'tgl_akhir', 'total', 'revisi_ke')
                ->where('kd_skpd', $skpd->kd_skpd)->get(),
        ];

        return view('bud.sp2b.create')->with($data);
    }

    public function simpanSp2b(Request $request)
    {
        $skpd = DB::table('ms_skpd_blud')->select('kd_skpd', 'nm_skpd')->first();
        $data = $request->data;
        $nama = Auth::user()->nama;

        DB::beginTransaction();
        try {
            DB::table('trhsp2b_blud')
                ->insert([
                    'no_sp3b'   => $data['no_sp3b'],
                    'no_sp2b'   => $data['no_sp2b'],
                    // 'tgl_sp3b'  => $data['tgl_sp3b'],
                    'tgl_sp2b'  => $data['tgl_sp2b'],
                    'bulan'     => $data['bulan'],
                    'skpd'      => $data['skpd'],
                    'kd_skpd'   => $data['skpd'],
                    'keterangan' => $data['keterangan'],
                    'revisi_ke'    => $data['revisi'],
                    'tgl_awal'  => $data['tgl_awal'],
                    'tgl_akhir' => $data['tgl_akhir'],
                    'total'     => $data['total'],
                    'username'  =>  $nama,
                    'status'    => '1',
                    'number_sp2b'   => 1
                ]);


            $rincian_data = $data['detail_sp3b'];
            $no_sp2b = $data['no_sp2b'];
            $kd_skpd = $data['skpd'];

            if (isset($data['detail_sp3b'])) {
                foreach ($rincian_data as $data => $value) {
                    $data = [
                        'kd_skpd'           => $kd_skpd,
                        'nosp2b'            => $no_sp2b,
                        'kd_sub_kegiatan'   => $rincian_data[$data]['kd_sub_kegiatan'],
                        'kd_rek6'           => $rincian_data[$data]['kd_rek6'],
                        'nm_rek6'           => $rincian_data[$data]['nm_rek6'],
                        'nilai'             => $rincian_data[$data]['nilai'],
                    ];
                    DB::table('trsp2b_blud')->insert($data);
                }
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'nomor'  => $no_sp2b
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '1'
            ]);
        }
    }

    public function detailSp2b(Request $request)
    {
        $tgl_awal   = $request->tgl_awal;
        $tgl_akhir  = $request->tgl_akhir;
        $kd_skpd    = $request->kd_skpd;

        $data   = DB::select("SELECT a.no_sp2b,b.no_sp3b,b.no_bukti,b.tgl_sp3b,b.keterangan,b.kd_rek6,b.nm_rek6,SUM (b.nilai) AS nilai,a.kd_skpd,b.kd_sub_kegiatan,b.no_lpj FROM trhsp3b_blud a INNER JOIN trsp3b_blud b ON a.kd_skpd =b.kd_skpd AND a.no_sp3b =b.no_sp3b AND a.tgl_sp3b =b.tgl_sp3b WHERE a.kd_skpd =? AND a.tgl_sp2b >=? AND a.tgl_sp2b <=? GROUP BY a.no_sp2b,b.no_sp3b,b.no_bukti,b.tgl_sp3b,b.keterangan,b.no_lpj,a.kd_skpd,b.kd_sub_kegiatan,b.kd_rek6,b.nm_rek6 ORDER BY kd_rek6", [$kd_skpd, $tgl_awal, $tgl_akhir]);

        return response()->json($data);
    }

    public function editSp2b($no_sp2b)
    {
        $no_sp2b = Crypt::decrypt($no_sp2b);
        $skpd    = DB::table('ms_skpd_blud')->select('kd_skpd', 'nm_skpd')->first();
        $detail  = DB::table('trhsp2b_blud')->select('kd_skpd', 'tgl_awal', 'tgl_akhir')->first();
        $detailSp2b    = DB::select("SELECT a.no_sp2b,a.kd_skpd,b.nosp2b,b.kd_rek6,b.nm_rek6, b.nilai,b.kd_sub_kegiatan FROM trhsp2b_blud a INNER JOIN trsp2b_blud b ON a.kd_skpd = b.kd_skpd AND a.no_sp2b = b.nosp2b WHERE a.kd_skpd = ? AND a.tgl_awal >= ? AND a.tgl_akhir <= ? ORDER BY kd_rek6", [$detail->kd_skpd, $detail->tgl_awal, $detail->tgl_akhir]);

        $data = [
            'datasp2b'    => collect(DB::select(" SELECT  *,(SELECT a.nm_skpd FROM ms_skpd a where a.kd_skpd=b.kd_skpd) as nm_skpd FROM trhsp2b_blud b WHERE b.kd_skpd = ? and b.no_sp2b = ? ", [$skpd->kd_skpd, $no_sp2b]))->first(),
            'detail'      => $detailSp2b,
        ];

        return view('bud.sp2b.edit')->with($data);
    }

    public function simpanEditSp2b(Request $request)
    {
        $data = $request->data;
        $skpd = DB::table('ms_skpd_blud')->select('kd_skpd', 'nm_skpd')->first();
        $nama = Auth::user()->nama;

        DB::beginTransaction();
        try {
            DB::table('trhsp2b_blud')->where(['no_sp2b' => $data['no_sp2b'], 'kd_skpd' => $skpd->kd_skpd])
                ->update([
                    //'no_sp3b'   => $data['no_sp3b'],
                    //'no_sp2b'   => $data['no_sp2b'],
                    // 'tgl_sp3b'  => $data['tgl_sp3b'],
                    'tgl_sp2b'  => $data['tgl_sp2b'],
                    'keterangan' => $data['keterangan'],
                    'username'  => $nama,
                ]);

            DB::commit();
            return response()->json([
                'message' => '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '1'
            ]);
        }
    }

    public function hapusSp2b(Request $request)
    {
        $no_sp2b = $request->no_sp2b;

        DB::beginTransaction();
        try {
            DB::table('trhsp2b_blud')
                ->where(['no_sp2b' => $no_sp2b])
                ->delete();

            DB::table('trsp2b_blud')
                ->where(['nosp2b' => $no_sp2b])
                ->delete();

            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',

                //jika eror muncul message 0
                'error' => $e->getMessage()
            ]);
        }
    }

    public function cetakPermintaanLayar(Request $request)
    {

        //request tuh ngambil dari view nya
        //kalo spp ls pake crypt:decryptString di konversi menjadi string
        //klo di navicat begin with itu sama kaya left(kd_rek6,1)
        //klo contains yang mengandung kata kunci yg di masukan, sama kaya di query itu LIKE '%a%'
        $no_sp2b    = $request->no_sp2b;
        $ttd        = $request->bud;
        $kd_skpd    = $request->kd_skpd;
        $jenis_print = $request->jenis_print;
        $dataSp2b   = collect(DB::select("SELECT no_sp3b, kd_skpd, keterangan, bulan, no_lpj, no_sp2b, skpd, status, status_bud, tgl_sp2b, tgl_sp3b, tgl_awal, tgl_akhir, total from trhsp2b_blud where no_sp2b=?", [$no_sp2b]))->first();
        $bud        = collect(DB::select("SELECT nip, nama, jabatan from ms_ttd where nip =?", [$ttd]))->first();
        $tahun      = tahun_anggaran();
        $sld_awal   = collect(DB::select("SELECT saldo_lalu from ms_skpd_blud "))->first();
        $sld_pend   = collect(DB::select("SELECT SUM(nilai) as sld_pend FROM trsp2b_blud WHERE nosp2b =? AND LEFT ( kd_rek6, 1 ) = '4'", [$no_sp2b]))->first();
        $sld_bel    = collect(DB::select("SELECT SUM(nilai) as sld_bel FROM trsp2b_blud WHERE nosp2b =? AND LEFT ( kd_rek6, 1 ) = '5'", [$no_sp2b]))->first();

        $data = [
            'header'    => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'no_sp3b'   => $dataSp2b->no_sp3b,
            'tgl_sp3b'  => $dataSp2b->tgl_sp2b,
            'skpd'      => $dataSp2b->skpd,
            'tgl_sp2b'  => $dataSp2b->tgl_sp2b,
            'nosp2b'    => $dataSp2b->no_sp2b,
            'nip'       => $bud->nip,
            'nama'      => $bud->nama,
            'jabatan'   => $bud->jabatan,
            'tahun'     => $tahun,
            'sawal'     => $sld_awal->saldo_lalu,
            'pend'      => $sld_pend,
            'belanja'   => $sld_bel,
        ];

        // dd($jenis_print);
        // return;
        $view = view('bud.sp2b.cetak')->with($data);
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
