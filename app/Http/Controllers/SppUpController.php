<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PDF;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SppUpController extends Controller
{
    public function index()
    {
        $kunci = kunci()->kunci_spp;
        $role = Auth::user()->role;

        $kuncian = $kunci == 1 && !in_array($role, ['1006', '1012', '1016', '1017']) ? '1' : '0';

        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'data_spp' => DB::table('trhspp')->where(['kd_skpd' => $kd_skpd, 'jns_spp' => '1'])->orderBy('no_spp')->orderBy('kd_skpd')->get(),
            'bendahara' => DB::table('ms_ttd')
                ->select('nip', 'nama', 'jabatan')
                ->where('kd_skpd', $kd_skpd)
                ->whereIn('kode', ['BK', 'BPP', 'KPA'])
                ->get(),
            'pptk' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PPTK', 'KPA'])->get(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PA', 'KPA'])->get(),
            'ppkd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', '5.02.0.00.0.00.02.0000')->whereIn('kode', ['BUD', 'KPA'])->get(),
            'kunci' => $kuncian,
        ];
        return view('penatausahaan.pengeluaran.spp_up.index')->with($data);
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhspp')
            ->where(['kd_skpd' => $kd_skpd, 'jns_spp' => '1'])
            ->orderBy('no_spp')
            ->orderBy('kd_skpd')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("sppup.edit", Crypt::encryptString($row->no_spp)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" style="margin-right:4px" onclick="cetak(\'' . $row->no_spp . '\', \'' . $row->jns_spp . '\', \'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm"><i class="uil-print"></i></a>';
            if ($row->status != 1) {
                $btn .= '<a href="javascript:void(0);" onclick="deleteData(\'' . $row->no_spp . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="fas fa-trash-alt"></i></a>';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $nomorup = DB::table('trhspp')->select(DB::raw("ISNULL(MAX(urut),0)+1 as nilai"))->where(['kd_skpd' => $kd_skpd, 'jns_spp' => '1'])->first();
        $nomor   = str::padLeft($nomorup -> nilai, 6, '0');
        $data = [
            'ketentuan' => DB::table('ms_sk_up')->orderBy('keterangan_up')->first(),
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'data_spd' => DB::table('trhspd')->select('no_spd', 'tgl_spd','bulan_awal')->where(DB::raw("LEFT(kd_skpd,22)"), DB::raw("LEFT('$kd_skpd',22)"))->where(['status' => '1', 'jns_beban' => '5'])->orderByDesc('bulan_awal')->get(),
            'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->orderBy('kode')->get(),
            'data_rek' => DB::table('ms_rekening_bank_online')->select('rekening', 'nm_rekening', 'npwp')->where(['kd_skpd' => $kd_skpd])->orderBy('rekening')->get(),
            'nilai_up' => DB::table('ms_up')->select('nilai_up as nilai')->where(['kd_skpd' => $kd_skpd])->first(),
            'no_up' => $nomor,
            'kd_skpd' => $kd_skpd,
            'data_tgl' => DB::table('trhspp')->select(DB::raw("MAX(tgl_spp) as tgl_spp"))->where(['kd_skpd' => $kd_skpd])->where(function ($query) {
                $query->where('sp2d_batal', '')->orWhereNull('sp2d_batal');
            })->first(),
            'tahun_anggaran' => tahun_anggaran()
        ];
        return view('penatausahaan.pengeluaran.spp_up.create')->with($data);
    }

    public function cariNoSppUp(Request $request)
    {
        $jenis_beban = $request->jenis_beban;
        $kd_skpd = Auth::user()->kd_skpd;
        $nomor = DB::table('trhspp')->select(DB::raw("ISNULL(MAX(urut),0)+1 as nilai"))->where(['kd_skpd' => $kd_skpd])->whereIn('jns_spp',['4','5','6'])->first();
        $data = str::padLeft($nomor -> nilai, 6, '0');
        return response()->json([
            'nilai' => $data
        ]);
    }

    // TABEL TRH TAGIH NO BUKTI DAPAT DARIMANA
    public function simpanSpp(Request $request)
    {
        $no_spp = $request->no_spp;
        $tgl_spp = $request->tgl_spp;
        $bulan = $request->bulan;
        $beban = $request->beban;
        $keperluan = $request->keperluan;
        $bank = $request->bank;
        $no_spd = $request->no_spd;
        $npwp = $request->npwp;
        $rekening = $request->rekening;
        $nama_penerima = $request->nama_penerima;
        $nm_skpd = $request->nm_skpd;
        $kode_akun = $request->kode_akun;
        $nama_akun = $request->nama_akun;
        $nilai_up = $request->nilai_up;
        // $no_urut = $request->no_urut;
        $kd_skpd = $request->kd_skpd;
        DB::beginTransaction();
        try {
            $nomorSppBaru = nomorSppBaru("spp", $no_spp, $tgl_spp, $beban);
            $cek = DB::table('trhspp')
            ->select('no_spp')
            ->where(['no_spp' => $nomorSppBaru])
            ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            } else {
                DB::table('trhspp')
                ->insert([
                    'no_spp' => $nomorSppBaru,
                    'kd_skpd' => $kd_skpd,
                    'keperluan' => $keperluan,
                    'no_spd' => $no_spd,
                    'jns_spp' => $beban,
                    'bank' => $bank,
                    'nmrekan' => $nama_penerima,
                    'penerima' => $nama_penerima,
                    'no_rek' => $rekening,
                    'npwp' => $npwp,
                    'nm_skpd' => $nm_skpd,
                    'tgl_spp' => $tgl_spp,
                    'status' => '0',
                    'nilai' => $nilai_up,
                    'urut' => $no_spp,
                    'username' => Auth::user()->nama,
                    'last_update' => date('Y-m-d H:i:s')
                ]);

                DB::table('trdspp')
                    ->where(['no_spp' => $nomorSppBaru, 'kd_rek6' => $kode_akun])
                    ->delete();

                DB::table('trdspp')
                    ->insert([
                        'no_spp' => $nomorSppBaru,
                        'kd_skpd' => $kd_skpd,
                        'kd_rek6' => $kode_akun,
                        'nm_rek6' => $nama_akun,
                        'nilai' => $nilai_up,
                    ]);

                DB::commit();
                return response()->json([
                    'message' => '1'
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
                'error' => $e->getMessage()
            ]);
        }
    }

    // public function simpanDetailSpp(Request $request)
    // {
    //     $no_spp = $request->no_spp;
    //     $kode_akun = $request->kode_akun;
    //     $nama_akun = $request->nama_akun;
    //     $nilai_up = $request->nilai_up;
    //     $kd_skpd = $request->kd_skpd;

    //     DB::beginTransaction();
    //     try {
    //         DB::table('trdspp') ->where(['no_spp' => $nomorSppBaru, 'kd_rek6' => $kode_akun])->delete();
    //         DB::table('trdspp')->insert([
    //             'no_spp' => $nomorSppBaru,
    //             'kd_skpd' => $kd_skpd,
    //             'kd_rek6' => $kode_akun,
    //             'nm_rek6' => $nama_akun,
    //             'nilai' => $nilai_up,
    //         ]);
    //         DB::commit();
    //         return response()->json([
    //             'message' => '1'
    //         ]);
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => '0',
    //             'error' => $e->getMessage()
    //         ]);
    //     }
    // }

    public function edit($no_spp)
    {
        $no_spp = Crypt::decryptString($no_spp);
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'spp' => DB::table('trhspp')->where(['no_spp' => $no_spp])->first(),
            'data_tgl' => DB::table('trhspp')->select(DB::raw("MAX(tgl_spp) as tgl_spp"))->where(['kd_skpd' => $kd_skpd])->where(function ($query) {
                $query->where('sp2d_batal', '')->orWhereNull('sp2d_batal');
            })->first(),
            'data_spd' => DB::table('trhspd')->select('no_spd', 'tgl_spd')->where(DB::raw("LEFT(kd_skpd,22)"), DB::raw("LEFT('$kd_skpd',22)"))->where(['status' => '1', 'jns_beban' => '5'])->get(),
            'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->orderBy('kode')->get(),
            'data_rek' => DB::table('ms_rekening_bank_online')->select('rekening', 'nm_rekening', 'npwp')->where(['kd_skpd' => $kd_skpd])->orderBy('rekening')->get(),
            'tahun_anggaran' => tahun_anggaran()
        ];
        return view('penatausahaan.pengeluaran.spp_up.edit')->with($data);
    }

    public function editSpp(Request $request)
    {
        $no_spp = $request->no_spp;
        $tgl_spp = $request->tgl_spp;
        $beban = $request->beban;
        $keperluan = $request->keperluan;
        $bank = $request->bank;
        $no_spd = $request->no_spd;
        $npwp = $request->npwp;
        $rekening = $request->rekening;
        $nama_penerima = $request->nama_penerima;
        $nm_skpd = $request->nm_skpd;
        $kode_akun = $request->kode_akun;
        $nilai_up = $request->nilai_up;
        $no_urut = $request->no_urut;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhspp')->select('no_spp')->where(['no_spp' => $no_spp])->count();
            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            } else {
                DB::table('trhspp')->insert([
                    'no_spp' => $no_spp,
                    'kd_skpd' => $kd_skpd,
                    'keperluan' => $keperluan,
                    'no_spd' => $no_spd,
                    'jns_spp' => $beban,
                    'bank' => $bank,
                    'nmrekan' => $nama_penerima,
                    'no_rek' => $rekening,
                    'npwp' => $npwp,
                    'nm_skpd' => $nm_skpd,
                    'tgl_spp' => $tgl_spp,
                    'status' => '0',
                    'nilai' => $nilai_up,
                    'urut' => $no_urut,
                    'username' => Auth::user()->nama,
                    'last_update' => date('Y-m-d H:i:s')
                ]);

                DB::commit();
                return response()->json([
                    'message' => '1'
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    // TABEL TRHTAGIH DAPAT NO TAGIH DARIMANA
    public function hapus(Request $request)
    {
        $no_spp = $request->no_spp;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhspm')
                ->where([
                    'no_spp' => $no_spp,
                    'kd_skpd' => $kd_skpd
                ])
                ->where(function ($query) {
                    $query->where('spmBatal', '')->orWhereNull('spmBatal');
                })
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trdspp')
                ->where([
                    'kd_skpd' => $kd_skpd,
                    'no_spp' => $no_spp
                ])
                ->delete();

            DB::table('trhspp')
                ->where([
                    'kd_skpd' => $kd_skpd,
                    'no_spp' => $no_spp
                ])
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

    // CETAKAN
    public function pengantarUp(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $unit = right($kd_skpd, 2);
        if ($unit == '01' || $kd_skpd == '1.20.03.00') {
            $peng = 'Pengguna Anggaran';
        } else {
            $peng = 'Kuasa Pengguna Anggaran';
        }
        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'spp' => DB::table('trhspp as a')->join('ms_skpd as b', 'a.kd_skpd', '=', 'b.kd_skpd')->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])->select('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'b.urusan1 as kd_bidang_urusan', 'a.no_spd', 'a.nilai', 'no_rek', 'a.npwp', 'a.bank', DB::raw("(SELECT nm_bidang_urusan FROM ms_bidang_urusan WHERE kd_bidang_urusan=b.urusan1)as nm_bidang_urusan"), DB::raw("(SELECT SUM(nilai) FROM trdspd WHERE no_spd=a.no_spd)as spd"), DB::raw("(SELECT SUM(nilai) FROM trhspp WHERE no_spd=a.no_spd AND kd_skpd=a.kd_skpd AND no_spp <> a.no_spp)as spp"))->first(),
            'no_spp' => $no_spp,
            'peng' => $peng,
            'pergub' => DB::table('ms_sk_up')->first(),
            'bendahara' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->first(),
            'kd_skpd' => $kd_skpd,
            'tanpa' => $request->tanpa
        ];

        $view = view('penatausahaan.pengeluaran.spp_up.cetak.pengantar')->with($data);
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

    public function ringkasanUp(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'no_spp' => $no_spp,
            'pergub' => DB::table('ms_sk_up')->first(),
            'bendahara' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->first(),
            'kd_skpd' => $kd_skpd,
            'tanpa' => $request->tanpa,
            'spp' => DB::table('trhspp as a')->join('ms_skpd as b', 'a.kd_skpd', '=', 'b.kd_skpd')->where(['a.no_spp' => $no_spp])->select('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'b.urusan1 as kd_urusan', 'a.no_spd', 'a.nilai', DB::raw("(SELECT nm_bidang_urusan FROM ms_bidang_urusan WHERE kd_bidang_urusan=b.urusan1) as nm_urusan"))->first()
        ];

        $view = view('penatausahaan.pengeluaran.spp_up.cetak.ringkasan')->with($data);
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

    public function rincianUp(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'no_spp' => $no_spp,
            'bendahara' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->first(),
            'kd_skpd' => $kd_skpd,
            'tanpa' => $request->tanpa,
            'spp' => DB::table('trdspp')->select('kd', 'kd_sub_kegiatan', 'kd_rek6', 'nm_rek6', 'nilai')->where(['no_spp' => $no_spp])->orderBy('kd')->first(),
            'total' => DB::table('trdspp')->select(DB::raw("SUM(nilai) as nilai"))->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first(),
            'spp1' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()
        ];

        $view = view('penatausahaan.pengeluaran.spp_up.cetak.rincian')->with($data);
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

    public function pernyataanUp(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'no_spp' => $no_spp,
            'bendahara' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->first(),
            'pa_kpa' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['PA', 'KPA'])
                ->first(),
            'kd_skpd' => $kd_skpd,
            'tanpa' => $request->tanpa,
            'spp' => DB::table('trhspp as a')->join('ms_skpd as b', 'a.kd_skpd', '=', 'b.kd_skpd')->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])->select('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'b.urusan1 as kd_bidang_urusan', 'a.no_spd', 'a.nilai', DB::raw("(SELECT nm_bidang_urusan from ms_bidang_urusan where kd_bidang_urusan=b.urusan1) as nm_bidang_urusan"))->first(),
        ];

        $view = view('penatausahaan.pengeluaran.spp_up.cetak.pernyataan')->with($data);
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

    public function sppUp(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;
        $tahun_anggaran = tahun_anggaran();

        $kd_sub_kegiatan = DB::table('trdspp')->select('kd_sub_kegiatan')->where(['no_spp' => $no_spp])->groupBy('kd_sub_kegiatan')->first();
        $sub_kegiatan = $kd_sub_kegiatan->kd_sub_kegiatan;
        if ($beban == 1) {
            $jenisspp = 'Uang Persediaan';
            $jenis_spp = 'SPP-UP';
        } else if ($beban == 2) {
            $jenisspp = 'Ganti Uang Persediaan';
            $jenis_spp = 'SPP-GU';
        } else if ($beban == 3) {
            $jenisspp = 'Tambahan Uang Persediaan';
            $jenis_spp = 'SPP-TU';
        } else if ($beban == 4) {
            $jenisspp = 'Langsung Gaji dan Tunjangan';
            $jenis_spp = 'SPP-LS';
        } else if ($beban == 5) {
            $jenisspp = 'Langsung Pihak Ketiga Lainnya';
            $jenis_spp = 'SPP-LS';
        } else if ($beban == 6) {
            $jenisspp = 'Langsung Barang dan Jasa';
            $jenis_spp = 'SPP-LS';
        }
        $data = DB::table('trhspp as a')->join('trdspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->select('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'a.bank', 'no_rek', 'keperluan', 'a.no_spd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', DB::raw("SUM(b.nilai) as nilaispp"))->groupBy('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'a.bank', 'no_rek', 'keperluan', 'a.no_spd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan')->first();

        if ($beban == 1 || $beban == 2) {
            $kd_sub_kegiatan1 = '';
            $nm_sub_kegiatan1 = '';
        } else {
            $kd_sub_kegiatan1 = $data->kd_sub_kegiatan;
            $nm_sub_kegiatan1 = $data->nm_sub_kegiatan;
        }
        $data = [
            'no_spp' => $no_spp,
            'jenisspp' => $jenisspp,
            'jenis_spp' => $jenis_spp,
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'npwp')->where(['kd_skpd' => $kd_skpd])->first(),
            'kd_sub_kegiatan1' => $kd_sub_kegiatan1,
            'nm_sub_kegiatan1' => $nm_sub_kegiatan1,
            'bendahara' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->first(),
            'pptk' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pptk, 'kd_skpd' => $kd_skpd, 'kode' => 'PPTK'])->first(),
            'pa' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first(),
            'bank' => DB::table('ms_bank')->select('nama')->where(['kode' => $data->bank])->first(),
            'tglspd' => DB::table('trhspd')->select('tgl_spd')->where(['no_spd' => $data->no_spd])->first(),
            'data' => $data,
            'nilaispd' => DB::table('trhspp')->select('nilai')->where(['no_spp' => $no_spp])->first(),
            'dataspd' => DB::table('trhspd')->select('bulan_awal','no_spd', 'tgl_spd', 'total')->whereRaw("LEFT(kd_skpd,22) = LEFT('$kd_skpd',22)")->orderBy('bulan_awal', 'asc')->get(),
            'datasp2d' => DB::table('trhsp2d')->select('no_sp2d', 'tgl_sp2d', 'nilai as total')->where(['kd_skpd' => $kd_skpd, 'jns_spp' => '6'])->orderBy('tgl_sp2d')->orderBy('no_sp2d')->get(),
            'sub_kegiatan' => $sub_kegiatan,
            'tanpa' => $tanpa,
            'kd_skpd' => $kd_skpd
        ];
        $view = view('penatausahaan.pengeluaran.spp_up.cetak.spp')->with($data);
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

    public function rincian77Up(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;
        $tahun_anggaran = tahun_anggaran();

        $kd_sub_kegiatan = DB::table('trdspp')->select('kd_sub_kegiatan')->where(['no_spp' => $no_spp])->groupBy('kd_sub_kegiatan')->first();

        if ($beban == 1) {
            $jenisspp = 'UANG PERSEDIAAN (SPP-UP)';
        } else if ($beban == 2) {
            $jenisspp = 'GANTI UANG PERSEDIAAN (SPP-GU)';
        } else if ($beban == 3) {
            $jenisspp = 'TAMBAHAN UANG PERSEDIAAN (SPP-TU)';
        } else if ($beban == 4) {
            $jenisspp = 'LANGSUNG (SPP-LS) GAJI DAN TUNJANGAN';
        } else if ($beban == 5) {
            $jenisspp = 'LANGSUNG (SPP-LS) PIHAK KETIGA LAINNYA';
        } else if ($beban == 6) {
            $jenisspp = 'LANGSUNG (SPP-LS) BARANG DAN JASA';
        }

        $data_spp = DB::table('trhspp as a')->join('trdspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupBy('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan')->select('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', DB::raw("SUM(b.nilai) as nilaisub"))->first();
        $kd_kegiatan = substr($data_spp->kd_sub_kegiatan, 0, 12);
        $data = [
            'jenisspp' => $jenisspp,
            'no_spp' => $no_spp,
            'tahun_anggaran' => $tahun_anggaran,
            'data_spp' => $data_spp,
            'nama_kegiatan' => DB::table('ms_kegiatan')->select('nm_kegiatan')->where(['kd_kegiatan' => $kd_kegiatan])->first(),
            'data_spp_rinci' => DB::table('trhspp as a')->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['a.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd, 'b.kd_sub_kegiatan' => $data_spp->kd_sub_kegiatan])->groupBy('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'kd_rek6', 'nm_rek6')->select('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'kd_rek6', 'nm_rek6', DB::raw("SUM(b.nilai) as nilaispp"))->get(),
            'spp' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp])->first(),
            'bendahara' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first(),
            'sub_kegiatan' => $kd_sub_kegiatan->kd_sub_kegiatan,
            'tanpa' => $tanpa,
            'kd_skpd' => $kd_skpd,
            'pa' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->first()
        ];
        $view = view('penatausahaan.pengeluaran.spp_up.cetak.rincian77')->with($data);
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

    public function batalSpp(Request $request)
    {
        $no_spp = $request->no_spp;
        $keterangan = $request->keterangan;
        $nama = Auth::user()->nama;
        $waktu_ubah = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            $cek = DB::table('trhspm')
                ->where([
                    'no_spp' => $no_spp
                ])
                ->where(function ($query) {
                    $query->where('spmBatal', '')->orWhereNull('spmBatal');
                })
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhspp')
                ->where(['no_spp' => $no_spp])
                ->update([
                    'sp2d_batal' => '1',
                    'ket_batal' => $keterangan,
                    'user_batal' => $nama,
                    'tgl_batal' => $waktu_ubah
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

