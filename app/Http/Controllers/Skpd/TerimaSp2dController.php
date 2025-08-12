<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TerimaSp2dController extends Controller
{
    public function index()
    {
        return view('skpd.terima_sp2d.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhsp2d')->where(['kd_skpd' => $kd_skpd, 'status_bud' => '1'])->orderBy('no_sp2d')->orderBy('kd_skpd')->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("terima_sp2d.tampil_sp2d", Crypt::encryptString($row->no_sp2d)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
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

        $sp2d = cair_sp2d($data_sp2d);

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
            'bk' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['kd_skpd' => $data_sp2d->kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->first(),
            'total_kegiatan' => DB::table('trdspp')->select(DB::raw("SUM(nilai) as nilai"))->where(['no_spp' => $data_sp2d->no_spp, 'kd_skpd' => $data_sp2d->kd_skpd])->first(),
            'sub_kegiatan' => $sp2d,
            'potongan1' => DB::table('trspmpot as a')->join('ms_pot as b', 'a.map_pot', '=', 'b.map_pot')->where(['a.no_spm' => $data_sp2d->no_spm, 'status_potongan' => '1', 'kd_skpd' => $data_sp2d->kd_skpd])->get(),
            'potongan2' => DB::table('trspmpot as a')->join('ms_pot as b', 'a.map_pot', '=', 'b.map_pot')->where(['a.no_spm' => $data_sp2d->no_spm, 'status_potongan' => '0', 'kd_skpd' => $data_sp2d->kd_skpd])->get(),
            'kd_skpd' => $kd_skpd
        ];
        return view('skpd.terima_sp2d.show')->with($data);
    }

    public function terimaSp2d(Request $request)
    {
        $no_terima = $request->no_terima;
        $tgl_terima = $request->tgl_terima;
        $no_sp2d = $request->no_sp2d;
        $nocek = $request->nocek;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhsp2d')
                ->where(['status_bud' => '1', 'status_terima' => '1', 'kd_skpd' => $kd_skpd])
                ->where('status', '<>', '1')
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhsp2d')
                ->where(['no_sp2d' => $no_sp2d])
                ->update([
                    'status_terima' => '1',
                    'no_terima' => $no_terima,
                    'tgl_terima' => $tgl_terima,
                ]);

            $bukti_terima = $no_terima + 1;
            $no_bukti_terima = "$bukti_terima";

            $cek_potongan =  DB::table('trspmpot as a')
                ->join('trhsp2d as b', function ($join) {
                    $join->on('a.no_spm', '=', 'b.no_spm');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->where(['b.no_sp2d' => $no_sp2d])
                ->whereNotIn('a.kd_rek6', ['2110801', '4140612'])
                ->count();

            if ($cek_potongan > 0) {
                $data_potongan = DB::table('trspmpot as a')
                    ->join('trhsp2d as b', function ($join) {
                        $join->on('a.no_spm', '=', 'b.no_spm');
                        $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    })
                    ->selectRaw("a.*,b.jns_spp")
                    ->where(['b.no_sp2d' => $no_sp2d, 'b.kd_skpd' => $kd_skpd])
                    ->whereNotIn('a.kd_rek6', ['2110801', '4140612'])
                    ->get();

                $data_potongan = json_decode(json_encode($data_potongan), true);

                if (isset($data_potongan)) {
                    DB::table('trdtrmpot')->insert(array_map(function ($value) use ($no_bukti_terima, $kd_skpd, $no_sp2d) {
                        return [
                            'no_bukti' => $no_bukti_terima,
                            'kd_rek6' => $value['kd_rek6'],
                            'nm_rek6' => $value['nm_rek6'],
                            'nilai' => $value['nilai'],
                            'kd_skpd' => $kd_skpd,
                            'kd_rek_trans' => $value['kd_trans'],
                            'map_pot' => $value['map_pot'],
                        ];
                    }, $data_potongan));
                }

                $data_potongan2 = DB::table('trspmpot as a')
                    ->join('trhsp2d as b', function ($join) {
                        $join->on('a.no_spm', '=', 'b.no_spm');
                        $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    })
                    ->join('trhspp as c', function ($join) {
                        $join->on('b.no_spp', '=', 'c.no_spp');
                        $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    })
                    ->selectRaw("SUM(a.nilai) as nilai_pot,b.keperluan, b.npwp,b.jns_spp, b.nm_skpd, c.kd_sub_kegiatan, c.nm_sub_kegiatan,c.nmrekan,c.pimpinan,c.alamat")
                    ->where(['b.no_sp2d' => $no_sp2d, 'b.kd_skpd' => $kd_skpd])
                    ->groupByRaw("no_sp2d,b.keperluan, b.npwp,b.jns_spp,b.nm_skpd,c.kd_sub_kegiatan, c.nm_sub_kegiatan,c.nmrekan,c.pimpinan,c.alamat")
                    ->get();;

                $data_potongan2 = json_decode(json_encode($data_potongan2), true);

                if (isset($data_potongan2)) {
                    DB::table('trhtrmpot')->insert(array_map(function ($value) use ($no_bukti_terima, $tgl_terima, $kd_skpd, $no_sp2d) {
                        return [
                            'no_bukti' => $no_bukti_terima,
                            'tgl_bukti' => $tgl_terima,
                            'ket' => 'Terima pajak nomor SP2D  ' . $no_sp2d,
                            'username' => Auth::user()->nama,
                            'tgl_update' => '',
                            'kd_skpd' => $kd_skpd,
                            'nm_skpd' => $value['nm_skpd'],
                            'no_sp2d' => $no_sp2d,
                            'nilai' => $value['nilai_pot'],
                            'npwp' => $value['npwp'],
                            'jns_spp' => $value['jns_spp'],
                            'status' => '1',
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                            'nmrekan' => $value['nmrekan'],
                            'pimpinan' => $value['pimpinan'],
                            'alamat' => $value['alamat'],
                        ];
                    }, $data_potongan2));
                }
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

    public function batalTerima(Request $request)
    {
        $no_terima = $request->no_terima;
        $tgl_terima = $request->tgl_terima;
        $no_sp2d = $request->no_sp2d;
        $nocek = $request->nocek;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhsp2d')
                ->where(['no_sp2d' => $no_sp2d, 'kd_skpd' => $kd_skpd])
                ->update([
                    'status_terima' => '0',
                    'no_terima' => '',
                    'tgl_terima' => '',
                ]);

            $bukti_terima = $no_terima + 1;
            $no_bukti_terima = "$bukti_terima";

            DB::table('trdtrmpot')
                ->where(['no_bukti' => $no_bukti_terima, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::table('trhtrmpot')
                ->where(['no_bukti' => $no_bukti_terima, 'kd_skpd' => $kd_skpd])
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
    //created by calvin
    public function cekTerima()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $skpd = collect(DB::select("SELECT kd_skpd, nm_skpd from ms_skpd where kd_skpd = ?", [$kd_skpd]))->first();

        $data =
            [
                'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
                'detail' => DB::select("SELECT no_sp2d, tgl_sp2d,tgl_kas_bud, no_spm, nm_skpd, keperluan, nilai from trhsp2d where status_bud = '1' and status_terima = '0' and kd_skpd = ? order by no_sp2d", [$kd_skpd]),
                'skpd' => $skpd->nm_skpd
            ];
        //dd($data['detail']);
        return view('skpd.terima_sp2d.cekterima')->with($data)->with('_blank');;
    }
    //end
}
