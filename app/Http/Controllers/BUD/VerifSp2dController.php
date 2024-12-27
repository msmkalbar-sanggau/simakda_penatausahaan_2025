<?php

namespace App\Http\Controllers\BUD;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class VerifSp2dController extends Controller
{
    public function index()
    {
        return view('bud.verif_sp2d.index');
    }

    public function loadData( Request $request)
    {
        // $query = DB::table('trhsp2d')
        // ->selectRaw('*, (SELECT TOP 1 nm_rekening from ms_rekening_bank_online where trhsp2d.no_rek=rekening)as penerima')
        // ->whereRaw('(is_verified is null OR is_verified <> 1) and (status_bud <> 1 OR status_bud is null)');

        $query = DB::table('trhsp2d')
        ->selectRaw('*, (SELECT TOP 1 nm_rekening from ms_rekening_bank_online where trhsp2d.no_rek=rekening)as penerima')
        ->whereRaw('(is_verified is null OR is_verified not in (1, 3)) and (status_bud <> 1 OR status_bud is null) and (sp2d_batal is null or sp2d_batal <> 1)');

        $column_seacrh = ['no_sp2d','tgl_sp2d','keperluan','nilai'];
        $filtered           =   $query->where(function ($query) use ($column_seacrh, $request) {
                                        foreach ($column_seacrh as $eachElement) {
                                            $query->orWhere($eachElement, 'LIKE', '%'.$request->search['value'].'%');
                                        }
                                    });

        if(!$request->search['value']){
            $record_total   = $query->get()->count();
            $data = $query->skip($request->start)
                    ->take($request->length)
                    ->orderByRaw('CAST(SUBSTRING(no_sp2d,12, 6) as int )')
                    ->get();
        }else{
            $record_total   = $filtered->get()->count();
            $data           = $filtered->get();
        }

        return Datatables::of($data)
                        ->with([
                        "recordsTotal" =>$record_total,
                        "recordsFiltered" => $record_total,
                        ])
                        ->rawColumns(['action'])
                        ->addIndexColumn()
                        ->setTransformer(function($item){
                                return [
                                    'nomor'         => $item->no_sp2d,
                                    'tanggal'       => $item->tgl_sp2d,
                                    'keterangan'    => $item->kd_skpd.'<br />'.$item->nm_skpd.'<br />'.$item->keperluan,
                                    'nilai'         => rupiah($item->nilai),
                                    'user'          => $item->user_verif,
                                    'aksi'          => '<a href="' . route("verif_sp2d.tampil_sp2d", Crypt::encryptString($item->no_sp2d)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>',
                                ];
                            })

                        ->skipPaging()
                        ->make(true);

    }

    public function loadDataVerif( Request $request)
    {
        $query = DB::table('trhsp2d')
        ->selectRaw('*, (SELECT TOP 1 nm_rekening from ms_rekening_bank_online where trhsp2d.no_rek=rekening)as penerima')
        ->whereRaw('(is_verified = 1) and (status_bud <> 1 OR status_bud is null)');

        $column_seacrh = ['no_sp2d','tgl_sp2d','keperluan','nilai'];
        $filtered           =   $query->where(function ($query) use ($column_seacrh, $request) {
                                        foreach ($column_seacrh as $eachElement) {
                                            $query->orWhere($eachElement, 'LIKE', '%'.$request->search['value'].'%');
                                        }
                                    });

        if(!$request->search['value']){
            $record_total   = $query->get()->count();
            $data = $query->skip($request->start)
                    ->take($request->length)
                    ->orderByRaw('CAST(SUBSTRING(no_sp2d,12, 6) as int )')
                    ->get();
        }else{
            $record_total   = $filtered->get()->count();
            $data           = $filtered->get();
        }

        return Datatables::of($data)
                        ->with([
                        "recordsTotal" =>$record_total,
                        "recordsFiltered" => $record_total,
                        ])
                        ->rawColumns(['action'])
                        ->addIndexColumn()
                        ->setTransformer(function($item){
                                return [
                                    'nomor'         => $item->no_sp2d,
                                    'tanggal'       => $item->tgl_sp2d,
                                    'keterangan'    => $item->kd_skpd.'<br />'.$item->nm_skpd.'<br />'.$item->keperluan,
                                    'nilai'         => rupiah($item->nilai),
                                    'user'          => $item->user_verif,
                                    'aksi'          => '<a href="' . route("verif_sp2d.tampil_sp2d", Crypt::encryptString($item->no_sp2d)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>',
                                ];
                            })

                        ->skipPaging()
                        ->make(true);

    }

    public function loadDataSalur( Request $request)
    {
        $query = DB::table('trhsp2d')
                ->selectRaw('*, (SELECT TOP 1 nm_rekening from ms_rekening_bank_online where trhsp2d.no_rek=rekening)as penerima')
                ->whereRaw('status_bud = 1 ');

        $column_seacrh = ['no_sp2d','tgl_sp2d','keperluan','nilai'];
        $filtered           =   $query->where(function ($query) use ($column_seacrh, $request) {
                                        foreach ($column_seacrh as $eachElement) {
                                            $query->orWhere($eachElement, 'LIKE', '%'.$request->search['value'].'%');
                                        }
                                    });

        if(!$request->search['value']){
            $record_total   = $query->get()->count();
            $data = $query->skip($request->start)
                      ->take($request->length)
                      ->orderByRaw('CAST(SUBSTRING(no_sp2d,12, 6) as int )')
                      ->get();
        }else{
            $record_total   = $filtered->get()->count();
            $data           = $filtered->get();
        }

        return Datatables::of($data)
                        ->with([
                        "recordsTotal" =>$record_total,
                        "recordsFiltered" => $record_total,
                        ])
                        ->rawColumns(['action'])
                        ->addIndexColumn()
                        ->setTransformer(function($item){
                                return [
                                    'nomor'         => $item->no_sp2d,
                                    'tanggal'       => $item->tgl_sp2d,
                                    'keterangan'    => $item->kd_skpd.'<br />'.$item->nm_skpd.'<br />'.$item->keperluan,
                                    'nilai'         => rupiah($item->nilai),
                                    'user'          => $item->user_verif,
                                    'aksi'          => '<a href="' . route("verif_sp2d.tampil_sp2d", Crypt::encryptString($item->no_sp2d)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>',
                                ];
                            })
                        ->skipPaging()
                        ->make(true);
    }

    public function tampilSp2d($no_sp2d)
    {
        $no_sp2d = Crypt::decryptString($no_sp2d);

        $data_sp2d = DB::table('trhsp2d as a')
            ->where(['a.no_sp2d' => $no_sp2d])
            ->select('a.*', DB::raw("(SELECT nmrekan FROM trhspp WHERE no_spp=a.no_spp AND kd_skpd=a.kd_skpd) as nmrekan"), DB::raw("(SELECT pimpinan FROM trhspp WHERE no_spp=a.no_spp AND kd_skpd=a.kd_skpd) as pimpinan"), DB::raw("(SELECT alamat FROM trhspp WHERE no_spp=a.no_spp AND kd_skpd=a.kd_skpd) as alamat"))
            ->first();

        $nilai = DB::table('trdspp')
            ->select(DB::raw("SUM(nilai) as nilai"))
            ->where(['no_spp' => $data_sp2d->no_spp])
            ->first();

        if (in_array($data_sp2d->jns_spp, ['1', '2', '4', '5'])) {
            $kd_kegi = '';
            $nm_kegi = '';
            $kd_prog = '';
            $nm_prog = '';
        } else {
            $data_kegiatan = DB::table('trdspp as a')
                ->join('trhsp2d as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->where(['b.kd_skpd' => $data_sp2d->kd_skpd, 'no_sp2d' => $no_sp2d])
                ->select('kd_sub_kegiatan')
                ->groupBy('kd_sub_kegiatan')
                ->first();

            $nama_kegiatan = DB::table('trskpd')
                ->select('nm_sub_kegiatan')
                ->where(['kd_sub_kegiatan' => $data_kegiatan->kd_sub_kegiatan])
                ->first();

            $kd_kegi = $data_kegiatan->kd_sub_kegiatan;
            $nm_kegi = $nama_kegiatan->nm_sub_kegiatan;
            $kd_prog = left($kd_kegi, 7);
            $nama_program = DB::table('trskpd')
                ->select('nm_program')
                ->where(['kd_program' => $kd_prog])
                ->first();
            $nm_prog = $nama_program->nm_program;
        }
        $kd_skpd = Auth::user()->kd_skpd;

        $status_anggaran = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();

        if (empty($status_anggaran)) {
            return redirect()->back()->with(['message' => 'DPA Belum Disahkan!', 'alert' => 'alert-danger']);
        }

        if ($data_sp2d->jns_spp == '2') {
            $sub_kegiatan = DB::table('trdspp as a')->join('trhspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['a.no_spp' => $data_sp2d->no_spp])->where(DB::raw("LEFT(a.kd_skpd,17)"), DB::raw("LEFT('$data_sp2d->kd_skpd',17)"))->whereNotIn('b.jns_spp', ['1', '2'])->where(DB::raw("LEFT(b.kd_skpd,17)"), DB::raw("LEFT('$data_sp2d->kd_skpd',17)"))->select('a.kd_sub_kegiatan')->get();
            $sub = json_decode(json_encode($sub_kegiatan), true);
            $pagu = DB::table('trdrka')->select(DB::raw("SUM(nilai) as nilai"))->where(['jns_ang' => $status_anggaran->jns_ang])->whereIn('kd_sub_kegiatan', $sub)->first();
        } else {
            $sub_kegiatan = DB::table('trdspp as a')
                ->join('trhspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->where(['a.no_spp' => $data_sp2d->no_spp, 'a.kd_skpd' => $data_sp2d->kd_skpd, 'b.kd_skpd' => $data_sp2d->kd_skpd])
                ->whereNotIn('b.jns_spp', ['1', '2'])
                ->select('a.kd_sub_kegiatan')
                ->get();
            $sub = json_decode(json_encode($sub_kegiatan), true);
            $pagu = DB::table('trdrka')
                ->select(DB::raw("SUM(nilai) as nilai"))
                ->where(['jns_ang' => $status_anggaran->jns_ang])
                ->whereIn('kd_sub_kegiatan', $sub)
                ->first();
        }

        // $sp2d = cair_sp2d($data_sp2d);
        $sp2d = DB::table('trhsp2d as a')->where(['a.no_sp2d' => $no_sp2d])
            ->select(
                'a.*',
                DB::raw("(SELECT nmrekan FROM trhspp WHERE no_spp=a.no_spp AND kd_skpd=a.kd_skpd) as nmrekan"),
                DB::raw("(SELECT pimpinan FROM trhspp WHERE no_spp=a.no_spp AND kd_skpd=a.kd_skpd) as pimpinan"),
                DB::raw("(SELECT alamat FROM trhspp WHERE no_spp=a.no_spp AND kd_skpd=a.kd_skpd) as alamat")
            )->first();
        $data_lamp = cari_lampiran($sp2d);
        $sp2d1 = cari_sp2d($sp2d, '10', $kd_skpd);

        // $cek_callback = DB::table('trhsp2d as a')
        //     ->select('b.status', 'b.ket_payment')
        //     ->join('trduji as b', 'a.no_sp2d', '=', 'b.no_sp2d')
        //     ->where(['a.no_sp2d' => $no_sp2d])
        //     ->first();

        // dd($cek_callback);

        $data = [
            'sp2d' => DB::table('trhsp2d')->where(['no_sp2d' => $no_sp2d])->first(),
            'daerah' => DB::table('sclient')->select('provinsi')->where(['kd_skpd' => $data_sp2d->kd_skpd])->first(),
            'no_sp2d' => $no_sp2d,
            'data_sp2d' => $data_sp2d,
            'nilai' => $nilai->nilai,
            'bank' => DB::table('ms_skpd')->where(['kd_skpd' => $data_sp2d->kd_skpd])->select('bank', 'rekening', 'npwp')->first(),
            'kd_kegi' => $kd_kegi,
            'nm_kegi' => $nm_kegi,
            'kd_prog' => $kd_prog,
            'nm_prog' => $nm_prog,
            'pagu' => $pagu->nilai,
            'bud' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['kode' => 'BUD'])->first(),
            // 'bk' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['kd_skpd' => $data_sp2d->kd_skpd])
            //     ->whereIn('kode', ['BK', 'BPP'])
            //     ->first(),
            'bk'  => DB::table('ms_rekening_bank_online')->select('rekening', 'nm_rekening')->where(['kd_skpd'=>$data_sp2d->kd_skpd, 'bank' => $data_sp2d->bank, 'rekening'=> $data_sp2d->no_rek])->first(),
            'total_kegiatan' => DB::table('trdspp')->select(DB::raw("SUM(nilai) as nilai"))->where(['no_spp' => $data_sp2d->no_spp, 'kd_skpd' => $data_sp2d->kd_skpd])->first(),
            'sub_kegiatan' => $sp2d1,
            'potongan1' => DB::table('trspmpot as a')->join('ms_pot as b', 'a.map_pot', '=', 'b.map_pot')->where(['no_spm' => $data_sp2d->no_spm, 'status_potongan' => '1', 'kd_skpd' => $data_sp2d->kd_skpd])->get(),
            'potongan2' => DB::table('trspmpot as a')->join('ms_pot as b', 'a.map_pot', '=', 'b.map_pot')->where(['no_spm' => $data_sp2d->no_spm, 'status_potongan' => '0', 'kd_skpd' => $data_sp2d->kd_skpd])->get(),
            'kd_skpd' => $kd_skpd,
            'data_lamp' => $data_lamp
            // 'cek' => $cek_callback
        ];

        return view('bud.verif_sp2d.show')->with($data);
    }

    public function verifSp2d(Request $request)
    {
        $no_sp2d = $request->no_sp2d;
        DB::beginTransaction();
        try {

            DB::table('trhsp2d')
                ->where(['no_sp2d' => $no_sp2d])
                ->update([
                    'user_verif'    => Auth::user()->nama,
                    'is_verified'   => '1',
                    'tgl_verif'     => date("Y-m-d H:i:s"),
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

    public function batalVerif(Request $request)
    {
        $no_sp2d = $request->no_sp2d;
        DB::beginTransaction();
        try {
            DB::table('trhsp2d')
                ->where(['no_sp2d' => $no_sp2d])
                ->update([
                    'user_batal_verif'  => Auth::user()->nama,
                    'is_verified'       => '0',
                    'tgl_verif'         => date("Y-m-d H:i:s"),
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
}
