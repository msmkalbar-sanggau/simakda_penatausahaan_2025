<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PDF;

class VerifikasiSpmController extends Controller
{
    public function index()
    {
        $kunci = kunci()->kunci_spm;
        $role = Auth::user()->role;

        $kuncian = $kunci == 1 && !in_array($role, ['1006', '1012', '1016', '1017']) ? '1' : '0';

        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['BPP', 'BK'])->get(),
            'pptk' => DB::table('ms_ttd')->select('nip', 'nama', 'kode', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PPTK', 'PPK'])->get(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'kode', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PA', 'KPA'])->get(),
            'ppkd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->whereIn('kode', ['BUD'])->get(),
            'kunci' => $kuncian,
        ];

        return view('penatausahaan.pengeluaran.verifikasi_spm.index')->with($data);
    }

    public function cariSkpd(Request $request)
    {
        $beban = $request->beban;
        // $data = DB::table('trhspm as a')->join('trhspp as b', 'a.no_spp', '=', 'b.no_spp')->where(['a.jns_spp' => $beban])->whereIn('a.status', ['0', '1', '2'])->select('a.kd_skpd','a.nm_skpd')->groupBy('a.kd_skpd','a.nm_skpd')->orderBy('a.kd_skpd', 'asc')->get();
        $data = DB::select("SELECT a.kd_skpd, a.nm_skpd FROM trhspm a INNER JOIN trhspp b ON a.no_spp=b.no_spp WHERE a.jns_spp = ? AND a.status IN (0,1,2) AND ( b.sp2d_batal != '1' OR b.sp2d_batal IS NULL ) GROUP BY a.kd_skpd, a.nm_skpd ORDER BY a.kd_skpd ASC",[$beban]);
        return response()->json($data);
    }

    public function cariSpm(Request $request)
    {
        $beban = $request->beban;
        $kd_skpd = $request->kd_skpd;

        // $data = DB::table('strhspm as a')->join('trhspp as b', 'a.no_spp', '=', 'b.no_spp')->where(['a.kd_skpd' => $kd_skpd])->where(['a.jns_spp' => $beban])->whereIn('a.status', ['0', '1', '2'])->select('a.*', DB::raw("ISNULL(b.sp2d_batal,'') as sp2d_batal"), DB::raw("ISNULL(b.ket_batal,'') as ket_batal"))->orderBy('a.no_spm', 'asc')->orderBy('a.kd_skpd', 'asc')->get();
        $data = DB::select("SELECT a.*,ISNULL(b.sp2d_batal,'') AS sp2d_batal,ISNULL(b.ket_batal,'') AS ket_batal FROM trhspm a INNER JOIN trhspp b ON a.no_spp=b.no_spp WHERE a.kd_skpd = ?  AND a.jns_spp = ? AND a.status IN (0,1,2) and ( b.sp2d_batal != '1' OR b.sp2d_batal IS NULL ) ORDER BY a.no_spm ASC,a.kd_skpd ASC",[$kd_skpd,$beban]);
        //dd($data);

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = "";
            $btn .= '<a href="javascript:void(0);" onclick="tampil(\'' . $row->no_spm . '\',\'' . $row->status . '\',\'' . $row->kd_skpd . '\',\'' . $row->jns_spp . '\');" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat" style="margin-right:4px"><i class="fa fa-bars"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('penatausahaan.pengeluaran.verifikasi_spm.index');
    }

    public function detailSpm(Request $request){
        $no_spm = $request->no_spm;
        $jns_spp = $request->jns_spp;
        $kd_skpd = $request->kd_skpd;
        $skpd = substr($kd_skpd, 0, 17);

        $data = collect(DB::select("SELECT a.*, b.no_tagih, b.pimpinan, c.ket_bast,d.nama as nmbank from trhspm a inner join trhspp b on a.kd_skpd = b.kd_skpd and a.no_spp = b.no_spp left join trhtagih c on b.no_tagih = c.no_bukti INNER JOIN ms_bank d on a.bank = d.kode where a.no_spm = ? and left(a.kd_skpd,22) = ?",[$no_spm,$kd_skpd]))->first();

        return response()->json($data);

    }

    public function updateCek(Request $request)
{
    $request->validate([
        'no_spm' => 'required|string',
        'cek' => 'required|int'
    ]);

    $data = DB::table('trhspm')
        ->where('no_spm', $request->no_spm)
        ->update(['cek' => $request->cek]);

    if ($data) {
        return response()->json(['success' => true, 'message' => 'Status cek berhasil diperbarui.']);
    } else {
        return response()->json(['success' => false, 'message' => 'SPM tidak ditemukan atau tidak ada perubahan.'], 404);
    }
}


     public function cekListSpm(Request $request){
        $jenis_print = $request->jenis_print;
        $data =
        [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'detail' => DB::select("SELECT a.*,b.no_tagih,b.pimpinan,c.ket_bast,ISNULL(b.sp2d_batal,'') AS sp2d_batal,ISNULL(b.ket_batal,'') AS ket_batal FROM trhspm a INNER JOIN trhspp b ON a.no_spp =b.no_spp LEFT JOIN trhtagih c ON b.no_tagih =c.no_bukti WHERE a.status IN (0,2) and ( b.sp2d_batal != '1' OR b.sp2d_batal IS NULL ) ORDER BY a.status DESC, a.kd_skpd ASC, a.no_spm ASC"),
        ];
        // dd($data['detail']);
        $view = view('penatausahaan.pengeluaran.verifikasi_spm.cetak.cek_list')->with($data);
        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('listSPM.pdf');
        } elseif ($jenis_print == 'excel') {
            header("Cache-Control:no-cache,no-store,must-revalidate");
            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=listSPM.xls");
            return $view;
        }else {
            return $view;
        }
    }

    public function setujuSpm(Request $request){
        $no_spm = $request->no_spm;
        $no_spp = $request->no_spp;
        $jns_spp = $request->jns_spp;
        $kd_skpd = $request->kd_skpd;
        $nama = Auth::user()->nama;
        $tgl_terima = $request->tgl_terima;
        $stsspm = $request->stsspm;
        $ket = $request->ket;

        try {
            DB::beginTransaction();

                DB::table('trhspm')
                    ->where(['no_spm' => $no_spm, 'kd_skpd' => $kd_skpd, 'jns_spp' => $jns_spp])
                    ->update([
                        'status' => $stsspm,
                        'tgl_setujui' => $tgl_terima,
                        'ket_validasi' => $ket,
                        'user_validasi'  => $nama
                    ]);

                if ($stsspm == 2) {
                    DB::table('trhspp')->where(['no_spp' => $no_spp])->update([
                        'sp2d_batal' => '1',
                        'ket_batal' => $ket,
                        'user_batal' => $nama,
                        'tgl_batal' => date('d-m-y H:i:s')
                    ]);

                    if ($jns_spp == '6') {
                        $no_tagih = DB::table('trhspp')->select('no_tagih')->where(['no_spp' => $no_spp])->first();
                        if ($no_tagih->no_tagih) {
                            DB::table('trhspp')->where(['no_spp' => $no_spp])->update([
                                'no_tagih' => '',
                                'kontrak' => '',
                                'sts_tagih' => '0',
                                'nmrekan' => '',
                                'pimpinan' => '',
                            ]);
                            DB::table('trhtagih')->where(['no_bukti' => $no_tagih->no_tagih])->update([
                                'sts_tagih' => '0',
                            ]);
                        }
                    }

                    if ($jns_spp == '1' || $jns_spp == '2') {
                        DB::table('trhlpj')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->update([
                            'status' => '1',
                        ]);
                    }
                }

            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function create()
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
        // $skpd1 = DB::table('trhspj_ppkd')->select('kd_skpd')->where(['bulan' => $bulan2, 'cek' => '1', 'kd_skpd' => $kd_skpd])->get();
        // $skpd = json_decode(json_encode($skpd1), true);
        // $prov = DB::table('trhspj_ppkd')->select(DB::raw("ISNULL(cek,0) as cek"))->where(['kd_skpd' => $kd_skpd, 'bulan' => $bulan2])->first();
        // $cek = $prov->cek;

        // if ($cek == '0' || $cek == null || $cek == 0) {
        //     if ($tanggal < 13) {
        //         $data_spp1 = DB::table('trhspp')->select('no_spp', 'tgl_spp', 'kd_skpd', 'nm_skpd', 'jns_spp', 'keperluan', 'bulan', 'no_spd', 'bank', 'nmrekan', 'no_rek', 'jns_beban', DB::raw("(replace( replace( npwp, '.', '' ), '-', '' )) as npwp"))->where('kd_skpd', $kd_skpd)->where('jns_spp', '!=', '3')->where(function ($query) {
        //             $query->where('sp2d_batal', '!=', '1')->orWhereNull('sp2d_batal');
        //         })->whereNotIn('no_spp', $data2);
        //         $data_spp2 = DB::table('trhspp')->select('no_spp', 'tgl_spp', 'kd_skpd', 'nm_skpd', 'jns_spp', 'keperluan', 'bulan', 'no_spd', 'bank', 'nmrekan', 'no_rek', 'jns_beban', DB::raw("(replace( replace( npwp, '.', '' ), '-', '' )) as npwp"))->where(['kd_skpd' => $kd_skpd, 'jns_spp' => '3'])->where(function ($query) {
        //             $query->where('sp2d_batal', '!=', '1')->orWhereNull('sp2d_batal');
        //         })->whereNotIn('no_spp', $data2)->unionAll($data_spp1);
        //         $data_spp = DB::table(DB::raw("({$data_spp2->toSql()}) AS sub"))
        //             ->mergeBindings($data_spp2)
        //             ->get();
        //     } else {
        //         $data_spp = DB::table('trhspp')->select('no_spp', 'tgl_spp', 'kd_skpd', 'nm_skpd', 'jns_spp', 'keperluan', 'bulan', 'no_spd', 'bank', 'nmrekan', 'no_rek', 'jns_beban', DB::raw("(replace( replace( npwp, '.', '' ), '-', '' )) as npwp"))->where('kd_skpd', $kd_skpd)->whereIn('jns_spp', ['4', '5', '6'])->where(function ($query) {
        //             $query->where('sp2d_batal', '!=', '1')->orWhereNull('sp2d_batal');
        //         })->whereNotIn('no_spp', $data2)->get();
        //     }
        // } else {
            $data_spp1 = DB::table('trhspp')->select('no_spp', 'tgl_spp', 'kd_skpd', 'nm_skpd', 'jns_spp', 'keperluan', 'bulan', 'no_spd', 'bank', 'nmrekan', 'no_rek', 'jns_beban', DB::raw("(replace( replace( npwp, '.', '' ), '-', '' )) as npwp"))->where('kd_skpd', $kd_skpd)->whereIn('jns_spp', ['1', '2'])->where(function ($query) {
                $query->where('sp2d_batal', '!=', '1')->orWhereNull('sp2d_batal');
            })->whereNotIn('no_spp', $data2)->where(['kd_skpd' => $kd_skpd]);
            // 'sts_setuju' => '1' MATIIN DLU KARENA PENGESAHAN SPM TU DI WHERE
            $data_spp2 = DB::table('trhspp')->select('no_spp', 'tgl_spp', 'kd_skpd', 'nm_skpd', 'jns_spp', 'keperluan', 'bulan', 'no_spd', 'bank', 'nmrekan', 'no_rek', 'jns_beban', DB::raw("(replace( replace( npwp, '.', '' ), '-', '' )) as npwp"))->where(['kd_skpd' => $kd_skpd])->whereIn('jns_spp', ['3'])->where(function ($query) {
                $query->where('sp2d_batal', '!=', '1')->orWhereNull('sp2d_batal');
            })->whereNotIn('no_spp', $data2)->where(['kd_skpd' => $kd_skpd])->unionAll($data_spp1);

            $data_spp3 = DB::table('trhspp')->select('no_spp', 'tgl_spp', 'kd_skpd', 'nm_skpd', 'jns_spp', 'keperluan', 'bulan', 'no_spd', 'bank', 'nmrekan', 'no_rek', 'jns_beban', DB::raw("(replace( replace( npwp, '.', '' ), '-', '' )) as npwp"))->where(['kd_skpd' => $kd_skpd])->whereIn('jns_spp', ['4', '5', '6'])->where(function ($query) {
                $query->where('sp2d_batal', '!=', '1')->orWhereNull('sp2d_batal');
            })->whereNotIn('no_spp', $data2)->unionAll($data_spp2);

            $data_spp = DB::table(DB::raw("({$data_spp3->toSql()}) AS sub"))
                ->mergeBindings($data_spp3)
                ->get();
        //}
        $data = [
            'data_spp' => $data_spp,
        ];
        return view('penatausahaan.pengeluaran.verifikasi_spm.create')->with($data);
    }

    public function cariJenis(Request $request)
    {
        $beban = $request->beban;
        $jenis = $request->jenis;
        $data = DB::table('ms_jenis_beban')->select('nama', 'jenis')->where(['jns_spp' => $beban, 'jenis' => $jenis])->first();
        return response()->json($data);
    }

    public function cariBank(Request $request)
    {
        $bank = $request->bank;
        $data = DB::table('ms_bank')->select('nama')->where(['kode' => $bank])->first();
        return response()->json($data);
    }

    public function cariNoSpd(Request $request)
    {
        $no_spd = $request->no_spd;
        $data = DB::table('trhspd')->select('tgl_spd')->where(['no_spd' => $no_spd])->first();
        return response()->json($data);
    }

    public function cariNoSpm(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_spm = $request->no_spm;
        $nomor = DB::table('trhspm')
            //->select(DB::raw("ISNULL(MAX(urut),0)+1 as nilai"))
             ->select(DB::raw("MAX(urut) + 1 as nilai"))
            ->where(['kd_skpd' => $kd_skpd])->where('no_spm', '!=', $no_spm)
            ->first();

       // return response()->json($data);
        $data = str::padLeft($nomor -> nilai, 6, '0');
        return response()->json([
            'nilai' => $data
        ]);

    }

    public function tglSpmLalu(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhspm as a')->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd])->where(function ($query) {
            $query->where('b.sp2d_batal', '=', '0')->orWhereNull('b.sp2d_batal');
        })->select(DB::raw("MAX(a.tgl_spm) as tanggal"))->first();
        return response()->json($data);
    }

    public function simpanSpm(Request $request)
    {
        $no_spm = $request->no_spm;
        $tgl_spm = $request->tgl_spm;
        $no_spp = $request->no_spp;
        $kd_skpd = $request->kd_skpd;
        $nm_skpd = $request->nm_skpd;
        $tgl_spp = $request->tgl_spp;
        $bulan = $request->bulan;
        $no_spd = $request->no_spd;
        $keperluan = $request->keperluan;
        $beban = $request->beban;
        $bank = $request->bank;
        $rekanan = $request->rekanan;
        $rekening = $request->rekening;
        $npwp = $request->npwp;
        $total = $request->total;
        $urut = $request->urut;
        $no_spp = $request->no_spp;
        $jenis = $request->jenis;
        $skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhspm')->where(['no_spp' => $no_spp])->count();
            if ($cek > 0) {
                return response()->json([
                    'message' => '3'
                ]);
            }

            $cek1 = DB::table('trhspm')->where(['no_spm' => $no_spm])->count();
            if ($cek1 > 0) {
                    return response()->json([
                        'message' => '1'
                    ]);
            }

            $kode_spm = explode("/", $no_spm);
            $kode_spp = explode("/", $no_spp);

            if ($kode_spm[2] != $kode_spp[2]) {
                return response()->json([
                    'message' => '5'
                ]);
            }

            DB::table('trhspm')->insert([
                'no_spm' => $no_spm,
                'tgl_spm' => $tgl_spm,
                'no_spp' => $no_spp,
                'kd_skpd' => $kd_skpd,
                'nm_skpd' => $nm_skpd,
                'tgl_spp' => $tgl_spp,
                'bulan' => $bulan,
                'no_spd' => $no_spd,
                'keperluan' => $keperluan,
                'jns_spp' => $beban,
                'jenis_beban' => $jenis,
                'bank' => $bank,
                'nmrekan' => $rekanan,
                'no_rek' => $rekening,
                'npwp' => $npwp,
                'nilai' => $total,
                'urut' => $urut,
                'status' => '0',
                'username' => $nama,
                'last_update' => date('Y-m-d H:i:s')
            ]);

            DB::table('trhspp')->where(['no_spp' => $no_spp, 'kd_skpd' => $skpd])->update([
                'status' => '1'
            ]);

            DB::commit();
            return response()->json([
                'message' => '2',
                'url' => route('spm.tambah_potongan', Crypt::encryptString($no_spm))
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function tambahPotongan($no_spm)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_spm = Crypt::decryptString($no_spm);
        $cari_spm = DB::table('trhspm as a')
            ->select('a.*')
            ->selectRaw("(SELECT isnull(is_verified, '0') FROM trhsp2d c WHERE a.no_spm=c.no_spm and a.kd_skpd=c.kd_skpd) as is_verified")
            ->where(['a.no_spm' => $no_spm])
            ->first();

        $data = [
            'daftar_kode_akun' => DB::table('ms_map_billing')->select('kd_map', 'nm_map')->groupBy('nm_map', 'kd_map')->get(),
            'no_spm' => $no_spm,
            'spm' => $cari_spm,
            'daftar_transaksi' => DB::table('trdspp')->select('kd_rek6', 'nm_rek6')->where(['no_spp' => $cari_spm->no_spp, 'kd_skpd' => $kd_skpd])->groupBy('kd_rek6', 'nm_rek6')->get(),
            'daftar_potongan' => DB::table('ms_pot')->select('kd_rek6', 'map_pot', 'nm_pot as nm_rek6')->groupBy('kd_rek6', 'nm_pot', 'map_pot')->get(),
            'total_pajak' => DB::table('trspmpot')->select(DB::raw("SUM(nilai) as nilai"))->where(['no_spm' => $no_spm])->first(),
            'kd_skpd' => $kd_skpd,
            'rincian_spm' => DB::table('trspmpot')->where(['no_spm' => $no_spm, 'kd_skpd' => $kd_skpd])->orderBy('kd_rek6')->get(),
        ];
        return view('penatausahaan.pengeluaran.verifikasi_spm.tambah_potongan')->with($data);
    }

    public function cariRekPot(Request $request)
    {
        $kode_akun = $request->kode_akun;
        $kode_setor = $request->kode_setor;
        $data = DB::table('ms_pot as a')->join('ms_map_billing as b', 'a.kd_rek6', '=', 'b.kd_rek6')->where(['b.kd_map' => $kode_akun, 'kd_setor' => $kode_setor])->select('a.map_pot', 'a.nm_rek6')->groupBy('a.map_pot', 'a.nm_rek6')->get();
        return response()->json($data);
    }

    public function loadRincian(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_spm = $request->no_spm;
        $data = DB::table('trspmpot')->where(['no_spm' => $no_spm, 'kd_skpd' => $kd_skpd])->orderBy('kd_rek6')->get();

        $spm = DB::table('trhspm as a')
            ->selectRaw("(SELECT isnull(is_verified, '0') FROM trhsp2d c WHERE a.no_spm=c.no_spm and a.kd_skpd=c.kd_skpd) as is_verified")
            ->where(['a.no_spm' => $no_spm, 'a.kd_skpd' => $kd_skpd])
            ->first();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) use ($spm) {
            if ($spm->is_verified > '0') {
                $btn = '';
            } else {
                $btn = '<a href="javascript:void(0);" onclick="hapusPajak(\'' . $row->no_spm . '\',\'' . $row->kd_rek6 . '\',\'' . $row->nm_rek6 . '\',\'' . $row->idBilling . '\',\'' . $row->nilai . '\',\'' . $row->status_setor . '\')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            }
            if ($row->kd_rek6 == '210105010001' || $row->kd_rek6 == '210105020001' || $row->kd_rek6 == '210105030001' || $row->kd_rek6 == '210105040001' || $row->kd_rek6 == '210105050001' || $row->kd_rek6 == '210106010001' ){
                $btn .= '<button type="button" onclick="cetakPajak(\'' . $row->no_spm . '\',\'' . $row->kd_rek6 . '\',\'' . $row->nm_rek6 . '\',\'' . $row->nilai . '\',\'' . $row->idBilling . '\')" class="btn btn-success btn-sm" style="margin-left:4px"><i class="uil-print"></i></button>';
            } else {
                $btn .= '';
            }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function hapusRincianPajak(Request $request)
    {
        $no_spm = $request->no_spm;
        $kd_rek6 = $request->kd_rek6;

        DB::beginTransaction();
        try {
            DB::table('trspmpot')->where(['no_spm' => $no_spm, 'kd_rek6' => $kd_rek6])->delete();
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

    public function tambahListPotongan(Request $request)
    {
        $rekening_transaksi = $request->rekening_transaksi;
        $rekening_potongan = $request->rekening_potongan;
        $map_pot = $request->map_pot;
        $nm_rek_pot = $request->nm_rek_pot;
        $id_billing = $request->id_billing;
        $jns_pot1 = $request->jns_pot1;
        $nilai_pot = $request->nilai_pot;
        $no_spm = $request->no_spm;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trspmpot')->insert([
                'no_spm' => $no_spm,
                'kd_rek6' => $rekening_potongan,
                'nm_rek6' => $nm_rek_pot,
                'nilai' => $nilai_pot,
                'kd_skpd' => $kd_skpd,
                'status_potongan' => $jns_pot1,
                'kd_trans' => $rekening_transaksi,
                'map_pot' => $map_pot,
                'idBilling' => $id_billing
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

    // cetak kelengkapan
    public function cetakKelengkapan(Request $request)
    {
        $no_spm = $request->no_spm;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $jenis_print = $request->jenis_print;
        $baris_spm = $request->baris_spm;
        $jenis_ls = $request->jenis_ls;
        $kd_skpd = $request->kd_skpd;
        $beban = $request->beban;

        $data = [
            'daerah' => DB::table('sclient')->select('kab_kota', 'daerah')->where(['kd_skpd' => $kd_skpd])->first(),
            'spm' => DB::table('trhspm')->select('no_spp', 'tgl_spp', 'jenis_beban')->where(['no_spm' => $no_spm, 'kd_skpd' => $kd_skpd])->first(),
            'pptk' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan', 'kd_skpd', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pptk])->whereIn('kode', ['PPK', 'PPTK'])->first(),
            'skpd' => DB::table('trhspp')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'ms_skpd' => DB::table('ms_skpd')->select('alamat', 'email', 'kodepos')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'beban' => $beban,
            'jenis' => $jenis_ls,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'beban5' => [
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '98', '99'
            ]
        ];
        $view = view('penatausahaan.pengeluaran.verifikasi_spm.cetak.kelengkapan')->with($data);
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

    // cetak berkas spm
    public function cetakBerkas(Request $request)
    {
        $no_spm = $request->no_spm;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $jenis_print = $request->jenis_print;
        $baris_spm = $request->baris_spm;
        $jenis_ls = $request->jenis_ls;
        $kd_skpd = $request->kd_skpd;
        $skpd = Auth::user()->kd_skpd;
        $beban = $request->beban;

        $kd_sub_kegiatan = DB::table('trdspp as a')->join('trhspm as b', 'a.no_spp', '=', 'b.no_spp')->select('a.kd_sub_kegiatan')->where(['b.no_spm' => $no_spm])->groupBy('a.kd_sub_kegiatan')->first();

        $data_spm = DB::table('trhspm as a')
            ->join('trhspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select('a.*', DB::raw("(SELECT nmrekan FROM trhspp WHERE no_spp=a.no_spp) as nmrekan"), DB::raw("(SELECT pimpinan FROM trhspp WHERE no_spp=a.no_spp) as pimpinan"), DB::raw("(SELECT tgl_spd FROM trhspd WHERE no_spd=a.no_spd and LEFT(kd_skpd,17)=LEFT(a.kd_skpd,17)) as tgl_spd"), 'b.jns_beban')
            ->where(['a.no_spm' => $no_spm, 'a.kd_skpd' => $kd_skpd])->first();

        $status_angkas = DB::table('trhrka as a')->join('tb_status_anggaran as b', 'a.jns_ang', '=', 'b.kode')
            ->select('b.nama', 'a.jns_ang')->where(['a.kd_skpd' => $skpd, 'status' => '1'])->first();

        $total_beban = total_beban($data_spm, $kd_skpd, $status_angkas);

        if ($total_beban <= $baris_spm) {
            $data_beban = data_beban($data_spm, $kd_skpd, $status_angkas);
        } else {
            $data_beban = data_beban1($data_spm, $kd_skpd, $status_angkas);
        }
        $data_potongan = DB::table('trspmpot as a')->select('a.nilai', 'b.nm_pot')->join('ms_pot as b', 'a.map_pot', '=', 'b.map_pot')->where(['no_spm' => $no_spm, 'status_potongan' => '1', 'kd_skpd' => $kd_skpd])->get();
        $total_potongan = 0;
        foreach ($data_potongan as $potongan) {
            $total_potongan += $potongan->nilai;
        }
        $data_potongan1 = DB::table('trspmpot as a')->select('a.nilai', 'a.nm_rek6')->join('ms_pot as b', 'a.map_pot', '=', 'b.map_pot')->where(['no_spm' => $no_spm, 'status_potongan' => '0', 'kd_skpd' => $kd_skpd])->get();
        $total_potongan1 = 0;
        foreach ($data_potongan1 as $potongan) {
            $total_potongan1 += $potongan->nilai;
        }

        $data = [
            'pihak_lain' => collect(DB::select("SELECT a.*,
                SUBSTRING(npwp, 0, 3)+'.'+SUBSTRING(npwp, 3, 3)+'.'+SUBSTRING(npwp, 6, 3)+'.'+SUBSTRING(npwp, 9, 1)+'-'+SUBSTRING(npwp, 10, 3)
            +'.'+SUBSTRING(npwp, 13, 3)npwp1,
                (SELECT nmrekan FROM trhspp WHERE no_spp = a.no_spp) AS nmrekan,
                (SELECT pimpinan FROM trhspp WHERE no_spp = a.no_spp) AS pimpinan,
                (SELECT tgl_spd FROM trhspd WHERE no_spd=a.no_spd and left(kd_skpd,17)=left(a.kd_skpd,17)) AS tgl_spd,
                (SELECT case when jns_beban='5' then 'Belanja Langsung' else 'Belanja Tidak Langsung' end
                FROM trhspd WHERE no_spd=a.no_spd and kd_skpd=a.kd_skpd) AS jns_beban
                FROM trhspm a WHERE a.no_spm = ?  AND a.kd_skpd=?", [$no_spm, $kd_skpd]))->first(),
            'no_spm' => $no_spm,
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daerah' => DB::table('sclient')->select('kab_kota', 'daerah')->where(['kd_skpd' => $kd_skpd])->first(),
            'bendahara' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['kd_skpd' => $kd_skpd, 'nip' => $bendahara])
                ->whereIn('kode', ['BPP', 'BK'])
                ->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pa_kpa])->whereIn('kode', ['PA', 'KPA'])->first(),
            'data_spm' => $data_spm,
            'tahun_anggaran' => tahun_anggaran(),
            'wp' => DB::table('trhspm')->select('npwp')->where(['kd_skpd' => $kd_skpd, 'no_spm' => $no_spm])->first(),
            'bank' => DB::table('ms_skpd as a')->select('bank', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nm_bank"), 'rekening', 'npwp')->where(['a.kd_skpd' => $kd_skpd])->first(),
            'beban1' => DB::table('trdspp')->select(DB::raw("SUM(nilai) as nilai"))->where(['no_spp' => $data_spm->no_spp, 'kd_skpd' => $kd_skpd])->first(),
            'beban' => $beban,
            'kd_skpd' => $kd_skpd,
            'baris' => $baris_spm,
            'total_beban' => $total_beban,
            'data_beban' => $data_beban,
            'data_potongan' => $data_potongan,
            'data_potongan1' => $data_potongan1,
            'total_potongan' => $total_potongan,
            'total_potongan1' => $total_potongan1,
            'tanpa' => $tanpa,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
        ];

        $view = view('penatausahaan.pengeluaran.verifikasi_spm.cetak.berkas')->with($data);
        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setOrientation('landscape')
                ->setPaper('legal');
            return $pdf->stream('laporan.pdf');
        } else {
            return $view;
        }
    }
    // cetak ringkasan
    // cetak pengantar
    public function cetakPengantar(Request $request)
    {
        $no_spm = $request->no_spm;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $jenis_print = $request->jenis_print;
        $baris_spm = $request->baris_spm;
        $jenis_ls = $request->jenis_ls;
        $kd_skpd = $request->kd_skpd;
        $kd1 = substr($kd_skpd, 0, 17);
        $skpd = Auth::user()->kd_skpd;
        $beban = $request->beban;
        $no_spp = DB::table('trhspm')->select('no_spp')->where(['no_spm' => $no_spm, 'kd_skpd' => $kd_skpd])->first();
        $data_spp = DB::table('trhspp')->select('tgl_spp', 'no_spd')->where(['no_spp' => $no_spp->no_spp, 'kd_skpd' => $kd_skpd])->first();
        $tgl_spd = DB::table('trhspd')->select('tgl_spd')->where(['no_spd' => $data_spp->no_spd])->where(DB::raw("LEFT(kd_skpd,17)"), $kd1)->first();
        $sub_giat = DB::table('trdspp')->select('kd_sub_kegiatan')->where(['no_spp' => $no_spp->no_spp])->groupBy('kd_sub_kegiatan')->first();
        $giatspp = $sub_giat->kd_sub_kegiatan;
        $cari_rek = DB::table('trdspp')->select('kd_rek6')->where(['no_spp' => $no_spp->no_spp, 'kd_skpd' => $kd_skpd])->orderBy('kd_rek6')->first();
        $data_beban = pengantar_spm($no_spm, $kd_skpd, $beban, $data_spp, $no_spp, $tgl_spd, $giatspp, $cari_rek);
        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat', 'kodepos')->where(['kd_skpd' => $kd_skpd])->first(),
            'no_spp' => $no_spp,
            'data_spp' => $data_spp,
            'tgl_spd' => $tgl_spd,
            'sub_giat' => $sub_giat,
            'tahun_anggaran' => tahun_anggaran(),
            'beban' => $beban,
            'status_anggaran' => DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first(),
            'kd_skpd' => $kd_skpd,
            'no_spm' => $no_spm,
            'data_beban' => $data_beban,
            'tanpa' => $tanpa,
            'bendahara' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['kd_skpd' => $kd_skpd, 'nip' => $bendahara])
                ->whereIn('kode', ['BK', 'BPP'])
                ->first(),
            'pptk' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pptk])->whereIn('kode', ['PPK', 'PPTK'])->first(),
            'daerah' => DB::table('sclient')->select('daerah')->where(['kd_skpd' => $kd_skpd])->first(),
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
        ];
        $view = view('penatausahaan.pengeluaran.verifikasi_spm.cetak.pengantar')->with($data);
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

    // cetak lampiran
    public function cetakLampiran(Request $request)
    {
        $no_spm = $request->no_spm;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $jenis_print = $request->jenis_print;
        $baris_spm = $request->baris_spm;
        $jenis_ls = $request->jenis_ls;
        $kd_skpd = $request->kd_skpd;
        $kd1 = substr($kd_skpd, 0, 17);
        $skpd = Auth::user()->kd_skpd;
        $beban = $request->beban;

        $data_spm = DB::table('trhspm')->select('nm_skpd', 'tgl_spm', 'bulan', 'no_spp')->where(['no_spm' => $no_spm, 'kd_skpd' => $kd_skpd])->first();
        $data_beban = lampiran_spm($beban, $no_spm, $kd_skpd);

        $total = 0;
        foreach ($data_beban as $nilai) {
            if ($nilai->urut == '6') {
                $total += $nilai->nilai;
            }
        }
        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat', 'kodepos')->where(['kd_skpd' => $kd_skpd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first(),
            'pptk' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pptk, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PPTK', 'PPK'])->first(),
            'ppkd' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ppkd, 'kd_skpd' => $kd_skpd, 'kode' => 'PPKD'])->first(),
            'data_spm' => $data_spm,
            'data_spp' => DB::table('trhspp')->select('kd_kegiatan', 'nm_kegiatan', 'kd_program', 'nm_program')->where(['no_spp' => $data_spm->no_spp, 'kd_skpd' => $kd_skpd])->first(),
            'daerah' => DB::table('sclient')->select('kab_kota', 'daerah')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'data_beban' => $data_beban,
            'beban' => $beban,
            'no_spm' => $no_spm,
            'total' => $total,
            'tanpa' => $tanpa,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
        ];

        $view = view('penatausahaan.pengeluaran.verifikasi_spm.cetak.lampiran')->with($data);
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

    // cetak tanggung jawab
    public function cetakTanggung(Request $request)
    {
        $no_spm = $request->no_spm;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $jenis_print = $request->jenis_print;
        $baris_spm = $request->baris_spm;
        $jenis_ls = $request->jenis_ls;
        $kd_skpd = $request->kd_skpd;
        $kd1 = substr($kd_skpd, 0, 17);
        $skpd = Auth::user()->kd_skpd;
        $beban = $request->beban;

        $data = [
            'daerah' => DB::table('sclient')->select('kab_kota', 'daerah')->where(['kd_skpd' => $kd_skpd])->first(),
            'data_skpd' => DB::table('ms_skpd')->select('alamat', 'kodepos')->where(['kd_skpd' => $kd_skpd])->first(),
            'nama_skpd' => DB::table('trhspp')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first(),
            'tgl_spm' => DB::table('trhspm')->select('tgl_spm')->where(['kd_skpd' => $kd_skpd, 'no_spm' => $no_spm])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'tanpa' => $tanpa,
            'beban' => $beban,
            'no_spm' => $no_spm,
            'kd_skpd' => $kd_skpd,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
        ];

        $view = view('penatausahaan.pengeluaran.verifikasi_spm.cetak.tanggung_jawab')->with($data);
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

    // cetak pernyataan
    public function cetakPernyataan(Request $request)
    {
        $no_spm = $request->no_spm;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $jenis_print = $request->jenis_print;
        $baris_spm = $request->baris_spm;
        $jenis_ls = $request->jenis_ls;
        $kd_skpd = $request->kd_skpd;
        $kd1 = substr($kd_skpd, 0, 17);
        $skpd = Auth::user()->kd_skpd;
        $beban = $request->beban;

        $data_beban = pernyataan_spm($no_spm, $kd_skpd, $beban);

        $data = [
            'data_skpd' => DB::table('ms_skpd')->select('alamat', 'kodepos')->where(['kd_skpd' => $kd_skpd])->first(),
            'nama_skpd' => DB::table('trhspp')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'daerah' => DB::table('sclient')->select('kab_kota', 'daerah')->where(['kd_skpd' => $kd_skpd])->first(),
            'data_spm' => DB::table('trhspm')->select('no_spp', 'tgl_spp', 'jenis_beban')->where(['no_spm' => $no_spm, 'kd_skpd' => $kd_skpd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'tanpa' => $tanpa,
            'beban' => $beban,
            'no_spm' => $no_spm,
            'kd_skpd' => $kd_skpd,
            'data_beban' => $data_beban,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
        ];

        $view = view('penatausahaan.pengeluaran.verifikasi_spm.cetak.pernyataan')->with($data);
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

    // cetak ringkasan UP
    public function cetakRingkasanUp(Request $request)
    {
        $no_spm = $request->no_spm;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $jenis_print = $request->jenis_print;
        $baris_spm = $request->baris_spm;
        $jenis_ls = $request->jenis_ls;
        $kd_skpd = $request->kd_skpd;
        $kd1 = substr($kd_skpd, 0, 17);
        $skpd = Auth::user()->kd_skpd;
        $beban = $request->beban;

        $data = [
            'pergub' => DB::table('ms_sk_up')->first(),
            'data_beban' => DB::table('trhspm as a')->select('a.no_spm', 'a.jenis_beban', 'a.tgl_spm', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'b.kd_bidang_urusan', 'b.nm_bidang_urusan', 'a.no_spd', 'a.nilai')->join('ms_bidang_urusan as b', DB::raw("SUBSTRING(a.kd_skpd,1,4)"), '=', 'b.kd_bidang_urusan')->where(['a.no_spm' => $no_spm])->first(),
            'bendahara' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['kd_skpd' => $kd_skpd, 'nip' => $bendahara])
                ->whereIn('kode', ['BK', 'BPP'])
                ->first(),
            'pptk' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pptk])->whereIn('kode', ['PPK', 'PPTK'])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spm' => $no_spm,
            'daerah' => DB::table('sclient')->select('daerah')->where(['kd_skpd' => $kd_skpd])->first(),
            'tanpa' => $tanpa,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
        ];

        $view = view('penatausahaan.pengeluaran.verifikasi_spm.cetak.ringkasan_up')->with($data);
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

    // cetak ringkasan GU
    public function cetakRingkasanGu(Request $request)
    {
        $no_spm = $request->no_spm;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $jenis_print = $request->jenis_print;
        $baris_spm = $request->baris_spm;
        $jenis_ls = $request->jenis_ls;
        $kd_skpd = $request->kd_skpd;
        $kd1 = substr($kd_skpd, 0, 17);
        $skpd = Auth::user()->kd_skpd;
        $beban = $request->beban;

        $status_anggaran = DB::table('trhrka as a')
            ->join('tb_status_anggaran as b', 'a.jns_ang', '=', 'b.kode')
            ->where(['a.kd_skpd' => $skpd, 'status' => '1'])
            ->select('jns_ang')
            ->orderByDesc('a.tgl_dpa')
            ->first();
        $no_spp = DB::table('trhspm')->select('no_spp')->where(['no_spm' => $no_spm])->first();
        $sub_giat = DB::table('trdspp')->select('kd_sub_kegiatan')->where(['no_spp' => $no_spp->no_spp])->groupBy('kd_sub_kegiatan')->first();
        $kd_sub_kegiatan = $sub_giat->kd_sub_kegiatan;
        $no_spd = DB::table('trhspp')->select('no_spd')->where(['no_spp' => $no_spp->no_spp])->first();
        $tgl_spd = DB::table('trhspd')->select('tgl_spd')->where(['no_spd' => $no_spd->no_spd])->first();
        $data_beban = ringkasan_gu($kd_skpd, $beban, $tgl_spd->tgl_spd, $kd_sub_kegiatan);
        $total_spd = 0;
        foreach ($data_beban as $beban1) {
            $total_spd += $beban1->nilai;
        }
        $no_spp = DB::table('trhspm')->select('no_spp', 'jenis_beban')->where(['no_spm' => $no_spm])->first();
        $tgl_spp = DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp->no_spp])->first();

        $data = [
            'data_skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spm' => $no_spm,
            'nilai_ang' => nilai_anggaran_ringkasan($beban, $kd_skpd, $status_anggaran->jns_ang, $kd_sub_kegiatan, $no_spp->no_spp),
            'bendahara' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'pangkat')
                ->where(['kd_skpd' => $kd_skpd, 'nip' => $bendahara])
                ->whereIn('kode', ['BPP', 'BK'])
                ->first(),
            'pptk' => DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['kd_skpd' => $kd_skpd, 'nip' => $pptk])->whereIn('kode', ['PPK', 'PPTK'])->first(),
            'data_beban' => $data_beban,
            'total_spd' => $total_spd,
            'kd_skpd' => $kd_skpd,
            'no_spp' => $no_spp,
            'tgl_spp' => $tgl_spp,
            'jenis_beban' => $no_spp->jenis_beban,
            'beban' => $beban,
            'daerah' => DB::table('sclient')->select('daerah')->where(['kd_skpd' => $kd_skpd])->first(),
            'tanpa' => $tanpa,
            'tgl_spm' => DB::table('trhspm as a')->join('trhspp as b', 'a.no_spp', '=', 'b.no_spp')->where(['a.no_spm' => $no_spm])->select('tgl_spm')->first(),
            'beban' => $beban,
            'jenis' => $no_spp->jenis_beban,
            'kd_sub_kegiatan' => $kd_sub_kegiatan,
            'beban6' => DB::table('trhspm as a')->join('trhspp as b', 'a.no_spp', '=', 'b.no_spp')->select('a.*', 'b.*', DB::raw("(SELECT nama as nama_bank FROM ms_bank WHERE kode=a.bank) as nama_bank"))->where(['a.no_spm' => $no_spm])->first(),
            'dpa' => DB::table('trhrka')->select('tgl_dpa', 'no_dpa')->where(['jns_ang' => $status_anggaran->jns_ang, 'kd_skpd' => $kd_skpd])->first(),
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
        ];
        $view = view('penatausahaan.pengeluaran.verifikasi_spm.cetak.ringkasan_gu')->with($data);
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

    // batal spm-spp
    public function batalSpmSpp(Request $request)
    {
        $no_spm = $request->no_spm;
        $no_spp = $request->no_spp;
        $keterangan = $request->keterangan;
        $beban = $request->beban;
        // $batal_spm = $request->batal_spm;
        $user = Auth::user()->nama;
        $kd_skpd = Auth::user()->kd_skpd;
        $lpj = DB::table('trhspp')->select('no_lpj')->where(['no_spp' => $no_spp])->first();
        $no_lpj = $lpj->no_lpj;

        DB::beginTransaction();
        try {
            // if ($batal_spm == "false") {
            //     DB::table('trhspm')->where(['no_spm' => $no_spm, 'no_spp' => $no_spp])->update([
            //         'sp2d_batal' => '1',
            //         'ket_batal' => $keterangan,
            //         'user_batal' => $user,
            //         'tgl_batal' => date('d-m-y H:i:s')
            //     ]);
            // } else {
            // DB::table('trhspm')->where(['no_spm' => $no_spm, 'no_spp' => $no_spp])->update([
            //     'sp2d_batal' => '1',
            //     'ket_batal' => $keterangan,
            //     'user_batal' => $user,
            //     'tgl_batal' => date('d-m-y H:i:s')
            // ]);

            DB::table('trhspp')->where(['no_spp' => $no_spp])->update([
                'sp2d_batal' => '1',
                'ket_batal' => $keterangan,
                'user_batal' => $user,
                'tgl_batal' => date('d-m-y H:i:s')
            ]);

            if ($beban == '6') {
                $no_tagih = DB::table('trhspp')->select('no_tagih')->where(['no_spp' => $no_spp])->first();
                if ($no_tagih->no_tagih) {
                    DB::table('trhspp')->where(['no_spp' => $no_spp])->update([
                        'no_tagih' => '',
                        'kontrak' => '',
                        'sts_tagih' => '0',
                        'nmrekan' => '',
                        'pimpinan' => '',
                    ]);
                    DB::table('trhtagih')->where(['no_bukti' => $no_tagih->no_tagih])->update([
                        'sts_tagih' => '0',
                    ]);
                }
            }

            if ($beban == '1' || $beban == '2' || $beban == '3') {
                DB::table('trhlpj_unit as a')->join('trlpj_unit_temp as b', 'a.no_lpj', '=', 'b.no_lpj')->where(['b.no_lpj_global' => $no_lpj, 'kd_bp_skpd' => $kd_skpd])->update([
                    'a.status' => '1',
                ]);
                DB::table('trhlpj')->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])->update([
                    'status' => '2',
                ]);
            }
            // }
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

    // tampil spm
    public function tampilSpm($no_spm)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_spm = Crypt::decryptString($no_spm);
        $data = [
            'data_spm' => DB::table('trhspm as a')
                ->join('trhspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->select('a.*', 'b.sp2d_batal')
                ->where(['a.kd_skpd' => $kd_skpd, 'a.no_spm' => $no_spm])->first(),
        ];

        return view('penatausahaan.pengeluaran.verifikasi_spm.show')->with($data);
    }

    // load rincian tampil
    public function loadRincianShow(Request $request)
    {
        $no_spp = $request->no_spp;
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('trdspp')->select('kd_sub_kegiatan', 'kd_rek6', 'nm_rek6', 'nilai')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->orderBy('kd_sub_kegiatan')->orderBy('kd_rek6')->get();
        return Datatables::of($data)->make(true);;
        return view('penatausahaan.pengeluaran.verifikasi_spm.show');
    }

    public function totalShow(Request $request)
    {
        $no_spp = $request->no_spp;
        $kd_skpd = Auth::user()->kd_skpd;

        $nilai = DB::table('trdspp')->select(DB::raw("SUM(nilai) as nilai"))->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first();
        return response()->json($nilai->nilai);
    }
}
