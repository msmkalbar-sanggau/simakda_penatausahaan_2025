<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use App\Models\KelengkapanSPM;
use App\Models\VerifikasiSPP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class VerifikasiSPPController extends Controller
{

    public function index()
    {
        $kd_skpd_diknas = Auth::user()->kd_skpd;
        $kd_skpd = Auth::user()->kd_skpd;

        // if (cekBppOrUnitOrIndukRequest($kd_skpd) > 0) {
        //     $pptk2 = DB::table('ms_ttd')->select('nip', 'nama', 'kode', 'jabatan')->where('kd_skpd', $kd_skpd_diknas)->whereIn('kode', ['PPK']);
        //     $pptk = DB::table('ms_ttd')->select('nip', 'nama', 'kode', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PPK'])->union($pptk2)->get();
        // } else {
        //     $pptk = DB::table('ms_ttd')->select('nip', 'nama', 'kode', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PPK'])->get();
        // }
        $pptk = DB::table('ms_ttd')->select('nip', 'nama', 'kode', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PPK'])->get();

        $data = [
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['BPP', 'BK'])->get(),
            'pptk' => $pptk,
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'kode', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PA', 'KPA'])->get(),
            'ppkd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->whereIn('kode', ['BUD'])->get(),
        ];

        return view('penatausahaan.pengeluaran.verifikasi_spp.index')->with($data);
    }

    public function cetakKelengkapan(Request $request)
    {
        $no_spm = $request->no_spm;
        $pptk = $request->pptk;
        $jenis_print = $request->jenis_print;
        $jenis_ls = $request->jenis_ls;
        $kd_skpd = $request->kd_skpd;
        $beban = $request->beban;
        $kd_org = substr($kd_skpd, 0, Config('constants.LENGTH_ORG'));
        $skpd = DB::table('trhspp')->select('nm_skpd')->where(['kd_skpd' => $request->kd_skpd])->first();

        $spp = DB::table('trhspp')->select(
            'no_spp',
            'tgl_spp',
            'jns_beban',
            'jns_spp',
            'tgl_terima_kelengkapan_spm',
            'tgl_kembali_kelengkapan_spm',
            'tgl_terima_kembali_kelengkapan_spm',
            'jenis_ls_kelengkapan_spm',
            'ket_kelengkapan_spm',
            // 'kd_sub_skpd'
        )
            ->where(['no_spp' => $request->no_spp, 'kd_skpd' => $request->kd_skpd])
            ->first();

        // $kd_sub_skpd = $spp->kd_sub_skpd;

        // if (cekBppOrUnitOrIndukRequest($kd_sub_skpd) > 0) {
        //     $sub_skpd = DB::table('ms_bpp')->select('kd_bpp as kd_sub_skpd', 'nm_bpp as nm_sub_skpd')->where(['kd_bpp' => $kd_sub_skpd])->first();

        //     $nm_skpd = $skpd->nm_skpd;
        // } else if (substr($kd_sub_skpd, -4) != '0000') {
        //     $org = DB::table('ms_organisasi')->select('nm_org')->where(['kd_org' => $kd_org])->first();

        //     $nm_skpd = $skpd->nm_skpd . ' ( ' . $org->nm_org . ' )';
        // } else {
        //     $nm_skpd = $skpd->nm_skpd;
        // }
        $nm_skpd = $skpd->nm_skpd;

        $kelengkapan_spm = VerifikasiSPP::select("b.*", 'verifikasi_spp.checked')->join('kelengkapan_spm as b', function ($join) {
            $join->on('verifikasi_spp.id_kelengkapan_spm', '=', 'b.id');
        })
            ->where("verifikasi_spp.kd_skpd", $kd_skpd)
            ->where("verifikasi_spp.no_spp", $request->no_spp)->get();;

        $data = [
            'daerah' => DB::table('sclient')->select('kab_kota', 'daerah')->where(['kd_skpd' => $request->kd_skpd])->first(),
            'spp' => $spp,
            'kelengkapan_spm' => $kelengkapan_spm,
            'pptk' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'kd_skpd', 'pangkat')
                ->where(['kd_skpd' => $request->kd_skpd, 'nip' => $request->pptk])
                ->whereIn('kode', ['PPK', 'PPTK'])->first(),
            'skpd' => DB::table('trhspp')->select('nm_skpd')->where(['kd_skpd' => $request->kd_skpd])->first(),
            'ms_skpd' => DB::table('ms_skpd')->select('alamat', 'email', 'kodepos')->where(['kd_skpd' => $request->kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'beban' => $spp->jns_spp,
            'jenis' => $spp->jenis_ls_kelengkapan_spm,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'beban5' => [
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98', '99'
            ],
            'nm_skpd' => $nm_skpd
        ];

        $view = view('penatausahaan.pengeluaran.verifikasi_spp.cetak.kelengkapan')->with($data);
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

    public function create(Request $request)
    {

        $kd_skpd = Auth::user()->kd_skpd;
        $tanggal = date('d');
        $bulan = date('m');
        if ($bulan - 1 == 0) {
            $bulan2 = 1;
        } else {
            $bulan2 = $bulan - 1;
        }
        $data1 = DB::table('trhspm')->select('no_spp')->where(['kd_skpd' => $kd_skpd])->get();
        $data2 = json_decode(json_encode($data1), true);
        $skpd1 = DB::table('trhspj_ppkd')->select('kd_skpd')->where(['bulan' => $bulan2, 'cek' => '1', 'kd_skpd' => $kd_skpd])->get();
        $skpd = json_decode(json_encode($skpd1), true);

        $data_spp = DB::select("SELECT no_spp,tgl_spp,kd_skpd,nm_skpd,jns_spp,keperluan,bulan,no_spd,bank,nmrekan,no_rek,jns_beban,
        -- kd_sub_skpd,
        replace(replace(npwp,'.',''),'-','')as npwp
            FROM trhspp WHERE no_spp NOT IN (SELECT no_spp FROM verifikasi_spp WHERE kd_skpd=?) and no_spp NOT IN (SELECT no_spp FROM trhspm WHERE kd_skpd=?) AND jns_spp IN ('1','2') and kd_skpd = ?
            and (sp2d_batal!='1' or sp2d_batal is null)
            UNION ALL
            SELECT no_spp,tgl_spp,kd_skpd,nm_skpd,jns_spp,keperluan,bulan,no_spd,bank,nmrekan,no_rek,jns_beban,
            -- kd_sub_skpd,
            replace(replace(npwp,'.',''),'-','')as npwp
            FROM trhspp WHERE no_spp NOT IN (SELECT no_spp FROM verifikasi_spp WHERE kd_skpd=?) and no_spp NOT IN (SELECT no_spp FROM trhspm WHERE kd_skpd=?) AND jns_spp IN ('3') and kd_skpd = ?
            and (sp2d_batal!='1' or sp2d_batal is null)
            UNION ALL
            SELECT no_spp,tgl_spp,kd_skpd,nm_skpd,jns_spp,keperluan,bulan,no_spd,bank,nmrekan,no_rek,jns_beban,
            -- kd_sub_skpd,
            replace(replace(npwp,'.',''),'-','')as npwp
            FROM trhspp WHERE no_spp NOT IN (SELECT no_spp FROM verifikasi_spp WHERE kd_skpd=?) and no_spp NOT IN (SELECT no_spp FROM trhspm WHERE kd_skpd=?) AND jns_spp IN ('4','5','6') and kd_skpd = ?   and (sp2d_batal!='1' or sp2d_batal is null)", [$kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd, $kd_skpd]);

        $data = [
            'data_spp' => $data_spp,
        ];

        $role = Auth::user()->role;

        return view('penatausahaan.pengeluaran.verifikasi_spp.create')->with($data);
    }

    public function edit($no_spp)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_spp = Crypt::decryptString($no_spp);

        $data["trhspp"] = DB::table("trhspp")->where("kd_skpd", $kd_skpd)->where("no_spp", $no_spp)->first();
        $data["trhspd"] = DB::table("trhspd")->where("no_spd", $data["trhspp"]->no_spd)->first();
        $data["trdspp"] = DB::table("trdspp")->where("kd_skpd", $kd_skpd)->where("no_spp", $no_spp)->get();
        $data["verifikasi_spp"] = VerifikasiSPP::select("b.*", 'verifikasi_spp.checked')->join('kelengkapan_spm as b', function ($join) {
            $join->on('verifikasi_spp.id_kelengkapan_spm', '=', 'b.id');
        })
            ->where("verifikasi_spp.kd_skpd", $kd_skpd)
            ->where("verifikasi_spp.no_spp", $no_spp)->get();
        $data["ms_bank"] = DB::table("ms_bank")->where("kode", $data["trhspp"]->bank)->first();

        return view('penatausahaan.pengeluaran.verifikasi_spp.edit')->with($data);
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhspp as a')->select("a.*")
            ->where(['a.kd_skpd' => $kd_skpd])
            ->whereIn("a.no_spp", function ($query) use ($kd_skpd) {
                $query->select('no_spp')->from('verifikasi_spp')->where('kd_skpd', $kd_skpd);
            })
            ->orderBy('a.tgl_spp', 'asc')->orderBy('a.no_spp', 'asc')->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_spp . '\',\'' . $row->jns_spp . '\',\'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak Kelengkapan SPM" style="margin-right:4px"><i class="uil-print"></i></a>';

            if ($row->sp2d_batal != '1') {
                $btn .= '<a href="' . route("verifikasi_spp.edit", ['no_spp' => Crypt::encryptString($row->no_spp)]) . '" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Verifikasi SPP" style="margin-right:4px"><i class="uil-edit"></i></a>';
            };

            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function loadKelengkapanSPM(Request $request)
    {
        try {
            $spp = DB::table("trhspp")->where("no_spp", $request->no_spp)->first();

            $sql = "SELECT [kelengkapan_spm].*, isnull(verifikasi_spp_.checked,'0') as checked from [kelengkapan_spm]
                        left join (
                            select * from  [verifikasi_spp] where no_spp=:no_spp
                        ) as [verifikasi_spp_]
                        on [kelengkapan_spm].[id] = [verifikasi_spp_].[id_kelengkapan_spm]
                        where [jns_spp] = :jns_spp and [jenis_ls] = :jenis_ls order by [urut] asc";


            $result = DB::select($sql, [
                "no_spp" =>  $request->no_spp,
                "jns_spp" => $spp->jns_spp,
                "jenis_ls" => $request->jenis_ls,
            ]);

            return response()->json(['message' => '', 'data' => $result], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => 'Server Error !!!'], 500);
        }
    }


    public function update(Request $request)
    {
        // try {
            $kd_skpd = Auth::user()->kd_skpd;
            // $kd_bpp_skpd        = Auth::user()->kd_bpp_skpd;
            $currentDateTime = date('Y-m-d H:i:s');

            $data = [];
            foreach ($request->data_detail as $key => $value) {
                $data[] = array(
                    "no_spp" => $request->no_spp,
                    "kd_skpd" => $kd_skpd,
                    // "kd_sub_skpd" => $kd_bpp_skpd,
                    "id_kelengkapan_spm" => $value["id"],
                    "checked" => $value["checked"],
                    "username_updated" => Auth::user()->username,
                    "updated_at" => $currentDateTime,
                );
            }

            $spp = DB::table("trhspp")->where("no_spp", $request->no_spp)->first();
            //TODO:: sementara dimatikan
            // if ($spp->status == 2)
            //     return response()->json(['message' => 'Kelengkapan No SPP Sudah Disetujui. Tidak Bisa Diedit Lagi. Silahkan tekan tombol Dikembalikan.'], 400);

            DB::beginTransaction();

            DB::table("trhspp")->where("no_spp", $request->no_spp)
                ->where("kd_skpd", $kd_skpd)
                ->update([
                    "tgl_terima_kelengkapan_spm" => $request->tgl_terima_kelengkapan_spm,
                    "tgl_kembali_kelengkapan_spm" => $request->tgl_kembali_kelengkapan_spm,
                    "tgl_terima_kembali_kelengkapan_spm" => $request->tgl_terima_kembali_kelengkapan_spm,
                    "jenis_ls_kelengkapan_spm" => $request->jenis_ls,
                    "ket_kelengkapan_spm"   => $request->ket_kelengkapan_spm
                ]);

            VerifikasiSPP::where("no_spp", $request->no_spp)->where("kd_skpd", $kd_skpd)->delete();
            VerifikasiSPP::insert($data);

            DB::commit();

            return response()->json(['message' => 'Kelengkapan No SPP ' . $request->no_spp . ' Berhasil Disimpan'], 200);
        // } catch (\Throwable $th) {
        //     Log::error($th->getMessage());
        //     DB::rollBack();
        //     return response()->json(['message' => 'Server Error !!!'], 500);
        // }
    }

    public function pernyataan(Request $request)
    {
        try {
            $data['pptk'] = DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'kd_skpd', 'pangkat')
                ->where(['kd_skpd' => $request->kd_skpd, 'nip' => $request->pptk])
                ->whereIn('kode', ['PPK', 'PPTK'])->first();

            $data['spp'] =  DB::table('trhspp')->select('no_spp', 'tgl_spp', 'jns_beban', 'jns_spp', 'tgl_terima_kelengkapan_spm', 'tgl_kembali_kelengkapan_spm', 'tgl_terima_kembali_kelengkapan_spm', 'jenis_ls_kelengkapan_spm', 'ket_kelengkapan_spm')
                ->where(['no_spp' => $request->no_spp, 'kd_skpd' => $request->kd_skpd])
                ->first();

            $data['header'] =  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first();

            $data['skpd'] = DB::table('trhspp')->select('nm_skpd')->where(['kd_skpd' => $request->kd_skpd])->first();

            $data['daerah'] = DB::table('sclient')->select('kab_kota', 'daerah')->where(['kd_skpd' => $request->kd_skpd])->first();

            $data["tahun_anggaran"] = tahun_anggaran();
            $view = view('penatausahaan.pengeluaran.verifikasi_spp.cetak.pernyataan')->with($data);
            if ($request->jenis_print == 'pdf') {
                $pdf = PDF::loadHtml($view)
                    ->setPaper('legal')
                    ->setOption('margin-left', 15)
                    ->setOption('margin-right', 15);
                return $pdf->stream('laporan.pdf');
            } else {
                return $view;
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    public function simpan(Request $request)
    {
        try {
            $kd_skpd = Auth::user()->kd_skpd;
            // $kd_bpp_skpd        = Auth::user()->kd_bpp_skpd;
            $currentDateTime = date('Y-m-d H:i:s');

            $data = [];
            foreach ($request->data_detail as $key => $value) {
                $data[] = array(
                    "no_spp" => $request->no_spp,
                    "kd_skpd" => $kd_skpd,
                    // "kd_sub_skpd" => $kd_bpp_skpd,
                    "id_kelengkapan_spm" => $value["id"],
                    "checked" => $value["checked"],
                    "username_created" => Auth::user()->username,
                    "created_at" => $currentDateTime,
                );
            }

            DB::beginTransaction();

            DB::table("trhspp")->where("no_spp", $request->no_spp)
                ->where("kd_skpd", $kd_skpd)
                ->update([
                    "tgl_terima_kelengkapan_spm" => $request->tgl_terima_kelengkapan_spm,
                    "tgl_kembali_kelengkapan_spm" => $request->tgl_kembali_kelengkapan_spm,
                    "tgl_terima_kembali_kelengkapan_spm" => $request->tgl_terima_kembali_kelengkapan_spm,
                    "jenis_ls_kelengkapan_spm" => $request->jenis_ls,
                    "ket_kelengkapan_spm"   => $request->ket_kelengkapan_spm,
                ]);

            VerifikasiSPP::insert($data);

            DB::commit();

            return response()->json(['message' => 'Kelengkapan No SPP ' . $request->no_spp . ' Berhasil Disimpan'], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Server Error !!!'], 500);
        }
    }


    public function setuju(Request $request)
    {
        try {

            $kd_skpd = Auth::user()->kd_skpd;
            // $kd_bpp_skpd        = Auth::user()->kd_bpp_skpd;
            $currentDateTime = date('Y-m-d H:i:s');

            $failed = 0;
            $data = [];
            foreach ($request->data_detail as $key => $value) {
                $data[] = array(
                    "no_spp" => $request->no_spp,
                    "kd_skpd" => $kd_skpd,
                    // "kd_sub_skpd" => $kd_bpp_skpd,
                    "id_kelengkapan_spm" => $value["id"],
                    "checked" => $value["checked"],
                    "username_updated" => Auth::user()->username,
                    "updated_at" => $currentDateTime,
                );

                if (!strpos($value['uraian'], "Dokumen lain yang dipersyaratkan")) {
                    if ($value["checked"] == 0) $failed += 1;
                }
            }

            if ($request->jenis_ls == '1' || $request->jenis_ls == '2' || $request->jenis_ls == '3') {
                if ($failed > 0)
                    return response()->json(['message' => 'Kelengkapan Hanya Bisa Disetuji Jika Semua data sudah dichecklist'], 400);
            }

            DB::beginTransaction();

            DB::table("trhspp")->where("no_spp", $request->no_spp)
                ->where("kd_skpd", $kd_skpd)
                ->update([
                    "tgl_terima_kelengkapan_spm" => $request->tgl_terima_kelengkapan_spm,
                    "tgl_kembali_kelengkapan_spm" => $request->tgl_kembali_kelengkapan_spm,
                    "tgl_terima_kembali_kelengkapan_spm" => $request->tgl_terima_kembali_kelengkapan_spm,
                    "jenis_ls_kelengkapan_spm" => $request->jenis_ls,
                    "status" => 2,
                    "ket_kelengkapan_spm"   => $request->ket_kelengkapan_spm,
                ]);

            VerifikasiSPP::where("no_spp", $request->no_spp)->where("kd_skpd", $kd_skpd)->delete();
            VerifikasiSPP::insert($data);

            DB::commit();

            return response()->json(['message' => 'Kelengkapan No SPP ' . $request->no_spp . ' Berhasil Disetujui'], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Server Error !!!'], 500);
        }
    }

    public function kembali(Request $request)
    {
        try {
            $kd_skpd = Auth::user()->kd_skpd;
            // $kd_bpp_skpd        = Auth::user()->kd_bpp_skpd;
            $currentDateTime = date('Y-m-d H:i:s');

            $spm = DB::table('trhspm')->where("no_spp", $request->no_spp)
                ->where("kd_skpd", $kd_skpd)->count();

            //TODO:: sementara dimatikan
            // if ($spm > 0)
            //     return response()->json(['message' => 'SPP Sudah Dibuat SPM. Tidak bisa dikembalikan lagi.'], 400);

            DB::beginTransaction();

            DB::table("trhspp")->where("no_spp", $request->no_spp)
                ->where("kd_skpd", $kd_skpd)
                ->update([
                    "tgl_kembali_kelengkapan_spm" => $request->tgl_kembali_kelengkapan_spm,
                    "status" => 3,
                ]);

            DB::commit();

            return response()->json(['message' => 'Kelengkapan SPP dari No SPP ' . $request->no_spp . ' Berhasil Ditolak'], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Server Error !!!'], 500);
        }
    }
}
