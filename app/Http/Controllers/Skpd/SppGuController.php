<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use PDF;

class SppGuController extends Controller
{
    public function index()
    {
        $kunci = kunci()->kunci_spp_gu;
        $role = Auth::user()->role;

        $kuncian = $kunci == 1 && !in_array($role, ['1006', '1012', '1016', '1017']) ? '1' : '0';

        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['KPA', 'BPP', 'BK'])->get(),
            'pptk' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PPTK', 'KPA'])->get(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PA', 'KPA'])->get(),
            'ppkd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', '5.02.0.00.0.00.02.0000')->whereIn('kode', ['BUD', 'KPA'])->get(),
            'kunci' => $kuncian,
        ];

        return view('skpd.spp_gu.index')->with($data);
    }

    public function load()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhspp as a')
            ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
            ->where(['a.kd_skpd' => $kd_skpd, 'a.jns_spp' => '2'])
            ->orderBy('a.no_spp')
            ->orderBy('a.kd_skpd')
            ->get();
            return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
                $btn = '<a href="' . route("spp_gu.edit", ['no_spp' => Crypt::encrypt($row->no_spp), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-warning btn-sm"  style="margin-right:4px"><i class="uil-edit"></i></a>';
                // if ($row->status == 1) {
                //     $btn .= "";
                // } else {
                //     $btn .= '<a href="javascript:void(0);" onclick="hapus(\'' . $row->no_spp . '\',\'' . $row->no_lpj . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
                // }
                $btn .= '<a href="javascript:void(0);" onclick="cetak(\'' . $row->no_spp . '\',\'' . $row->jns_spp . '\',\'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak LPJ" style="margin-right:4px"><i class="uil-print"></i></a>';
                return $btn;
            })->rawColumns(['aksi'])->make(true);
    }

    public function tambah()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = [
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'daftar_bank' => DB::table('ms_skpd')
                ->selectRaw("bank,replace(replace(npwp,'.',''),'-','')as npwp ,(select nama from ms_bank where kode = bank)as nama_bank,rekening")
                ->where(['kd_skpd' => $kd_skpd])
                ->get(),
            'daftar_rekening' => DB::table('ms_rekening_bank_online')
                ->selectRaw("rekening, nm_rekening,npwp")
                ->where(['kd_skpd' => $kd_skpd])
                ->orderBy('rekening')
                ->get(),
            'daftar_spd' => DB::table('trhspd')
                ->selectRaw("no_spd,tgl_spd,'5' as jenis,bulan_awal")
                ->whereRaw("left(kd_skpd,22)=left(?,22) and status=? and jns_beban=?", [$kd_skpd, '1', '5'])
                ->orderBy('bulan_awal', 'DESC')
                ->get(),
            'daftar_lpj' => DB::table('trhlpj')
                ->selectRaw("no_lpj,tgl_lpj")
                ->where(['status' => '1', 'jenis' => '1', 'kd_skpd' => $kd_skpd])
                ->whereRaw("no_lpj NOT IN(select ISNULL(no_lpj,'') FROM trhspp WHERE kd_skpd=? AND jns_spp=? and (sp2d_batal<>? or sp2d_batal is null))", [$kd_skpd, '2', '1'])
                ->get(),
            'tanggal_lalu' => DB::table('trhspp')
                ->selectRaw("max(tgl_spp) as tgl_spp")
                ->where(['kd_skpd' => $kd_skpd])
                ->whereRaw("(sp2d_batal is null or sp2d_batal= '0')")
                ->first(),
        ];

        return view('skpd.spp_gu.create')->with($data);
    }

    public function detail(Request $request)
    {
        $no_lpj = $request->no_lpj;
        $no_spp = $request->no_spp;
        $tipe = $request->tipe;
        $kd_skpd = Auth::user()->kd_skpd;

        if ($tipe == 'create') {
            $data = DB::table('trlpj as a')
                ->join('trhlpj as b', function ($join) {
                    $join->on('a.no_lpj', '=', 'b.no_lpj');
                })
                ->selectRaw("a.kd_sub_kegiatan,a.kd_rek6, a.nm_rek6, a.nilai ,a.no_bukti,a.no_lpj, a.kd_skpd as kd_unit,a.kd_bp_skpd as kd_skpd,
        (select DISTINCT sumber from trdtransout where trdtransout.no_bukti=a.no_bukti and trdtransout.kd_skpd=a.kd_skpd and trdtransout.kd_sub_kegiatan=a.kd_sub_kegiatan and trdtransout.kd_rek6=a.kd_rek6)as sumber")
                ->where(['a.kd_bp_skpd' => $kd_skpd, 'a.no_lpj' => $no_lpj])
                ->orderBy('a.no_bukti')
                ->orderBy('a.kd_sub_kegiatan')
                ->orderBy('a.kd_rek6')
                ->get();
        } else if ($tipe == 'edit') {
            $data = DB::table('trhspp as a')
                ->join('trdspp as b', function ($join) {
                    $join->on('a.no_spp', '=', 'b.no_spp');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.kd_sub_kegiatan,b.kd_rek6, b.nm_rek6, b.nilai ,b.no_bukti, b.kd_skpd as kd_unit,b.sumber")
                ->where(['a.kd_skpd' => $kd_skpd, 'a.no_spp' => $no_spp])
                ->orderBy('b.no_bukti')
                ->orderBy('b.kd_sub_kegiatan')
                ->orderBy('b.kd_rek6')
                ->get();
        }

        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function nomor(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        // $data = DB::table('trhspp')
        //     ->selectRaw("isnull(max(urut),0)+1 as nilai")
        //     ->where(['kd_skpd' => $kd_skpd])
        //     ->first();
        $nomor = DB::table('trhspp')->select(DB::raw("ISNULL(MAX(urut),0)+1 as nilai"))->where(['kd_skpd' => $kd_skpd, 'jns_spp' => '2'])->first();
        $data = str::padLeft($nomor -> nilai, 6, '0');
        return response()->json([
            'nilai' => $data
        ]);

        // return response()->json($data);
    }

    public function simpan(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $cek = DB::table('trhspp')->where(['no_spp' => $data['no_spp']])->count();
            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            }

            DB::table('trhspp')
                ->insert([
                    'no_spp' => $data['no_spp'],
                    'kd_skpd' => $data['kd_skpd'],
                    'keperluan' => $data['keterangan'],
                    'bulan' => '',
                    'no_spd' => $data['no_spd'],
                    'jns_spp' => $data['beban'],
                    'bank' => $data['bank'],
                    'nmrekan' => $data['nm_rekening'],
                    'no_rek' => $data['rekening'],
                    'npwp' => $data['npwp'],
                    'nm_skpd' => $data['nm_skpd'],
                    'tgl_spp' => $data['tgl_spp'],
                    'status' => '0',
                    'username' => Auth::user()->nama,
                    'last_update' => date('Y-m-d H:i:s'),
                    'nilai' => $data['total'],
                    'no_lpj' => $data['no_lpj'],
                    'urut' => $data['no_urut'],
                ]);

            DB::table('trdspp')
                ->where(['no_spp' => $data['no_spp'], 'kd_skpd' => $data['kd_skpd']])
                ->delete();

            $cek = collect(DB::select("SELECT count(*)jml FROM [dbo].[trlpj] where no_lpj=?", [$data['no_lpj']]))->first();

            $no_spp = $data['no_spp'];
            $spd = $data['no_spd'];

            // if ($cek->jml >= 1000) {
            DB::insert("INSERT INTO trdspp (no_spp,kd_rek6,nm_rek6,nilai,kd_skpd,nm_skpd,kd_sub_kegiatan,nm_sub_kegiatan,no_spd,no_bukti,kd_bidang,sumber,kd)
                        SELECT
                        '$no_spp' no_spp,
                        kd_rek6,
                        nm_rek6,
                        nilai,
                        kd_bp_skpd,
                        (select nm_skpd from ms_skpd where ms_skpd.kd_skpd=trlpj.kd_bp_skpd)as nm_skpd,
                        kd_sub_kegiatan,
                        (select nm_sub_kegiatan from ms_sub_kegiatan where ms_sub_kegiatan.kd_sub_kegiatan=trlpj.kd_sub_kegiatan)as nm_sub_kegiatan,'$spd' as no_spd,no_bukti,kd_skpd, (select sumber from trdtransout where trdtransout.kd_skpd=trlpj.kd_skpd and trdtransout.kd_sub_kegiatan=trlpj.kd_sub_kegiatan and trdtransout.kd_rek6=trlpj.kd_rek6 and trdtransout.no_bukti=trlpj.no_bukti)as sumber,(select max(isnull(kd,0))+1 from trdspp where no_spp=?) as rows
                         from trlpj where no_lpj=?", [$no_spp, $data['no_lpj']]);
            // } else {
            //     $data_lpj = DB::table('trlpj as a')
            //         ->selectRaw("'$no_spp' no_spp, kd_rek6, nm_rek6, nilai, kd_bp_skpd, (Select nm_skpd from ms_skpd where kd_skpd=a.kd_bp_skpd) as nm_skpd, kd_sub_kegiatan, (Select nm_sub_kegiatan from ms_sub_kegiatan where kd_sub_kegiatan=a.kd_sub_kegiatan) as nm_sub_kegiatan, '$spd' as no_spd, no_bukti, kd_skpd, (Select sumber from trdtransout where kd_skpd=a.kd_skpd and kd_sub_kegiatan=a.kd_sub_kegiatan and kd_rek6=a.kd_rek6 and no_bukti=a.no_bukti) as sumber, (Select max(isnull(kd,0))+1 from trdspp where no_spp=?) as rows", [$no_spp])
            //         ->where(['no_lpj' => $data['no_lpj']]);


            //     DB::table('trdspp')
            //         ->insertUsing(['no_spp', 'kd_rek6', 'nm_rek6', 'nilai', 'kd_skpd', 'nm_skpd', 'kd_sub_kegiatan', 'nm_sub_kegiatan', 'no_spd', 'no_bukti', 'kd_bidang', 'sumber', 'kd'], $data_lpj);
            // }

            DB::update("UPDATE a
                                SET a.nm_sub_kegiatan=b.nm_sub_kegiatan
                                FROM trdspp  a
                                INNER JOIN trskpd b
                                ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                                WHERE no_spp=?", [$no_spp]);

            DB::table('trhlpj')
                ->where(['no_lpj' => $data['no_lpj'], 'kd_skpd' => $data['kd_skpd']])
                ->update([
                    'status' => '2'
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

    public function edit($no_spp, $kd_skpd)
    {
        $no_spp = Crypt::decrypt($no_spp);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $data = [
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'daftar_bank' => DB::table('ms_skpd')
                ->selectRaw("bank,replace(replace(npwp,'.',''),'-','')as npwp ,(select nama from ms_bank where kode = bank)as nama_bank,rekening")
                ->where(['kd_skpd' => $kd_skpd])
                ->get(),
            'daftar_rekening' => DB::table('ms_rekening_bank_online')
                ->selectRaw("rekening, nm_rekening,npwp")
                ->where(['kd_skpd' => $kd_skpd])
                ->orderBy('rekening')
                ->get(),
            'tanggal_lalu' => DB::table('trhspp')
                ->selectRaw("max(tgl_spp) as tgl_spp")
                ->where(['kd_skpd' => $kd_skpd])
                ->whereRaw("(sp2d_batal is null or sp2d_batal= '0')")
                ->first(),
            'spp' => DB::table('trhspp as a')
                ->selectRaw("a.*,(SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as nm_skpd")
                ->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd, 'a.jns_spp' => '2'])
                ->first()
        ];

        return view('skpd.spp_gu.edit')->with($data);
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trhspp')
                ->where(['no_spp' => $data['no_spp'], 'kd_skpd' => $data['kd_skpd']])
                ->update([
                    'tgl_spp' => $data['tgl_spp'],
                    'keperluan' => $data['keterangan'],
                    'username' => Auth::user()->nama,
                    'last_update' => date('Y-m-d H:i:s'),
                    'bank' => $data['bank'],
                    'nmrekan' => $data['nm_rekening'],
                    'no_rek' => $data['rekening'],
                    'npwp' => $data['npwp'],
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

    public function hapus(Request $request)
    {
        $no_spp = $request->no_spp;
        $no_lpj = $request->no_lpj;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('trdspp')
                ->where([
                    'no_spp' => $no_spp,
                    'kd_skpd' => $kd_skpd
                ])
                ->delete();

            DB::table('trhspp')
                ->where([
                    'no_spp' => $no_spp,
                    'kd_skpd' => $kd_skpd,
                ])
                ->delete();

            DB::table('trhlpj')
                ->where(['no_lpj' => $no_lpj, 'kd_skpd' => $kd_skpd])
                ->update([
                    'status' => '1'
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

    public function pengantar(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $spp = DB::table('trhspp')
            ->select('no_spd', 'tgl_spp')
            ->where(['no_spp' => $no_spp])
            ->first();

        $tgl_spd = DB::table('trhspd')
            ->select('tgl_spd')
            ->where(['no_spd' => $spp->no_spd])
            ->first();

        $tgl_spd = $tgl_spd->tgl_spd;
        $tgl_spp = $spp->tgl_spp;

        $spp = collect(DB::select("SELECT a.no_spp,a.tgl_spp,a.kd_skpd,a.nm_skpd,a.bulan,a.nmrekan,
        b.urusan1 as kd_bidang_urusan,
        (select nm_bidang_urusan from ms_bidang_urusan where kd_bidang_urusan=b.urusan1)as nm_bidang_urusan,
         a.bank,
                (SELECT rekening FROM ms_skpd WHERE kd_skpd=a.kd_skpd) AS no_rek,
                (SELECT npwp FROM ms_skpd WHERE kd_skpd=a.kd_skpd) AS npwp,
                a.no_spd,(select sum(nilai) from trdspp where no_spp=a.no_spp)as nilai,
                (SELECT SUM(a.nilai) FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' and b.kd_skpd=?
                    and b.tgl_spd <=?) AS spd,
                    (SELECT SUM(b.nilai) FROM trdspp b INNER JOIN trhspp a ON b.no_spp=a.no_spp and b.kd_skpd = a.kd_skpd
                    INNER JOIN trhsp2d c ON a.no_spp = c.no_spp WHERE a.kd_skpd=?
                    AND a.jns_spp IN ('1','2','3','6') AND a.no_spp != ? AND c.tgl_sp2d <=?) AS spp
                FROM trhspp a INNER JOIN ms_skpd b ON a.kd_skpd=b.kd_skpd  where a.no_spp=? AND a.kd_skpd=?", [$kd_skpd, $tgl_spd, $kd_skpd, $no_spp, $tgl_spp, $no_spp, $kd_skpd]))->first();

        $bank = DB::table('ms_skpd')
            ->select('bank')
            ->where(['kd_skpd' => $kd_skpd])
            ->first();

        $nama_bank = empty($bank->bank) || $bank->bank == '' ? '-' : DB::table('ms_bank')->select('nama')->where(['kode' => $bank->bank])->first()->nama;

        $unit = substr($kd_skpd, -2);
        if ($unit == '01' || $kd_skpd == '1.20.03.00') {
            $peng = "Pengguna Anggaran";
        } else {
            $peng = "Kuasa Pengguna Anggaran";
        }

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'beban' => $beban,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'spp' => $spp,
            'bendahara' => DB::table('ms_ttd')->where(['kode' => 'BK', 'nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first(),
            'nama_bank' => $nama_bank,
            'peng' => $peng,
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
        ];
        $view = view('skpd.spp_gu.cetak.pengantar')->with($data);
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

    public function rincian(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $spp = DB::select("SELECT SUM(nilai) AS nilai FROM trdspp WHERE no_spp=? AND kd_skpd=?", [$no_spp, $kd_skpd]);

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'data_spp' => $spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['kode' => 'BK', 'nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
        ];

        $view = view('skpd.spp_gu.cetak.rincian')->with($data);
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

    public function ringkasan(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $kegiatan = DB::table('trdspp')
            ->select('kd_sub_kegiatan')
            ->where(['no_spp' => $no_spp])
            ->groupBy('kd_sub_kegiatan')
            ->first();

        $kd_sub_kegiatan = $kegiatan->kd_sub_kegiatan;

        $spp = DB::table('trhspp')
            ->select('no_spd', 'tgl_spp')
            ->where(['no_spp' => $no_spp])
            ->first();

        $tgl_spd = DB::table('trhspd')
            ->select('tgl_spd')
            ->where(['no_spd' => $spp->no_spd])
            ->first();

        $tgl_spd = $tgl_spd->tgl_spd;
        $tgl_spp = $spp->tgl_spp;

        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where
                                left(kd_skpd,22)=left(?,22)
                                and bulan_akhir='3' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,22)=left(?,22)
                                and bulan_akhir='6' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,22)=left(?,22)
                                and bulan_akhir='9' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where
                                left(kd_skpd,22)=left(?,22)
                                and bulan_akhir='12' and tgl_spd<=?", [$kd_skpd, $tgl_spd]))->first();

        $data_beban = DB::select("SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' and left(a.kd_unit,22)=left(?,22)
                    and bulan_akhir='3' and revisi_ke=?
                    GROUP BY a.no_spd, b.tgl_spd
                    UNION ALL
                    SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' and left(a.kd_unit,22)=left(?,22)
                    and bulan_akhir='6' and revisi_ke=?
                    GROUP BY a.no_spd, b.tgl_spd
                    UNION ALL
                    SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' and left(a.kd_unit,22)=left(?,22)
                    and bulan_akhir='9' and revisi_ke=?
                    GROUP BY a.no_spd, b.tgl_spd
                    UNION ALL
                    SELECT a.no_spd, b.tgl_spd, SUM(a.nilai) as nilai FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' and left(a.kd_unit,22)=left(?,22)
                    and bulan_akhir='12' and revisi_ke=?
                    GROUP BY a.no_spd, b.tgl_spd", [$kd_skpd, $revisi1->revisi, $kd_skpd, $revisi2->revisi, $kd_skpd, $revisi3->revisi, $kd_skpd, $revisi4->revisi]);

        $nilai_ang = collect(DB::select("SELECT sum(nilai) as nilai FROM trdrka where left(kd_rek6,1)='5' and left(kd_skpd,22)=left(?,22) and jns_ang=?", [$kd_skpd, $status_anggaran]))->first();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $belanja_up = collect(DB::select("SELECT SUM(b.nilai)AS nilai  FROM trdspp b INNER JOIN trhspp a ON b.no_spp=a.no_spp and b.kd_skpd = a.kd_skpd INNER JOIN trhsp2d c ON a.no_spp = c.no_spp WHERE left(a.kd_skpd,22)=left(?,22) AND a.jns_spp IN ('1','2') AND a.no_spp != ? AND c.tgl_sp2d <=?", [$kd_skpd, $no_spp, $tgl_spp]))->first();

        $belanja_tu = collect(DB::select("SELECT SUM(b.nilai)AS nilai  FROM trdspp b INNER JOIN trhspp a ON b.no_spp=a.no_spp and b.kd_skpd = a.kd_skpd INNER JOIN trhsp2d c ON a.no_spp = c.no_spp WHERE left(a.kd_skpd,22)=left(?,22) AND a.jns_spp='3' AND a.no_spp != ? AND c.tgl_sp2d <=? and (c.sp2d_batal is null OR c.sp2d_batal ='')", [$kd_skpd, $no_spp, $tgl_spp]))->first();

        $belanja_gaji = collect(DB::select("SELECT SUM(b.nilai)AS nilai  FROM trdspp b INNER JOIN trhspp a ON b.no_spp=a.no_spp and b.kd_skpd = a.kd_skpd INNER JOIN trhsp2d c ON a.no_spp = c.no_spp WHERE left(a.kd_skpd,22)=left(?,22) AND a.jns_spp IN ('4') AND a.no_spp != ? AND c.tgl_sp2d <=?", [$kd_skpd, $no_spp, $tgl_spp]))->first();

        $belanja_barjas = collect(DB::select("SELECT SUM(b.nilai)AS nilai  FROM trdspp b INNER JOIN trhspp a ON b.no_spp=a.no_spp and b.kd_skpd = a.kd_skpd INNER JOIN trhsp2d c ON a.no_spp = c.no_spp WHERE left(a.kd_skpd,22)=left(?,22) AND a.jns_spp='6' AND a.no_spp != ? AND c.tgl_sp2d <=? and  (c.sp2d_batal is null OR c.sp2d_batal ='')", [$kd_skpd, $no_spp, $tgl_spp]))->first();

        $belanja_ketiga = collect(DB::select("SELECT SUM(b.nilai)AS nilai  FROM trdspp b INNER JOIN trhspp a ON b.no_spp=a.no_spp and b.kd_skpd = a.kd_skpd INNER JOIN trhsp2d c ON a.no_spp = c.no_spp WHERE left(a.kd_skpd,22)=left(?,22) AND a.jns_spp IN ('5') AND a.no_spp != ? AND c.tgl_sp2d <=? and  (c.sp2d_batal is null OR c.sp2d_batal ='')", [$kd_skpd, $no_spp, $tgl_spp]))->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['kode' => 'BK', 'nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'nilai_ang' => $nilai_ang,
            'data_beban' => $data_beban,
            'kd_skpd' => $kd_skpd,
            'beban' => $beban,
            'tgl_spp' => $tgl_spp,
            'kd_sub_kegiatan' => $kd_sub_kegiatan,
            'belanja_up' => $belanja_up,
            'belanja_tu' => $belanja_tu,
            'belanja_gaji' => $belanja_gaji,
            'belanja_barjas' => $belanja_barjas,
            'belanja_ketiga' => $belanja_ketiga,
        ];

        $view = view('skpd.spp_gu.cetak.ringkasan')->with($data);
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

    public function pernyataan(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $data = collect(DB::select("SELECT a.no_spp,a.tgl_spp,a.kd_skpd,a.nm_skpd,a.bulan,a.nmrekan, a.no_rek,a.npwp,b.kd_bidang_urusan, b.nm_bidang_urusan, a.bank,a.no_spd,a.nilai
                , ( SELECT
                            nama
                        FROM
                            ms_bank
                        WHERE
                            kode=a.bank
                ) AS nama_bank
                FROM trhspp a INNER JOIN ms_bidang_urusan b
                ON SUBSTRING(a.kd_skpd,1,4)=b.kd_bidang_urusan  where a.no_spp=? AND a.kd_skpd=?", [$no_spp, $kd_skpd]))->first();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['kode' => 'BK', 'nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first(),
            'data' => $data
        ];

        $view = view('skpd.spp_gu.cetak.pernyataan')->with($data);
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

    public function permintaan(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $data_spp = collect(DB::select("SELECT a.no_spp,a.tgl_spp,a.kd_skpd,a.nm_skpd,a.kd_program,a.nm_program,a.nm_sub_kegiatan,a.kd_sub_kegiatan,a.bulan,a.nmrekan,
                a.no_rek as no_rek_rek, a.npwp as npwp_rek,b.kd_bidang_urusan, b.nm_bidang_urusan, a.bank, lanjut, kontrak, keperluan,pimpinan, alamat,
                ( SELECT nama FROM ms_bank WHERE  kode=a.bank ) AS nama_bank_rek,
                ( SELECT rekening FROM ms_skpd WHERE  kd_skpd=a.kd_skpd ) AS no_rek,
                ( SELECT npwp FROM ms_skpd WHERE  kd_skpd=a.kd_skpd ) AS npwp,
                a.no_spd,a.nilai
                FROM trhspp a INNER JOIN ms_bidang_urusan b
                ON SUBSTRING(a.kd_skpd,1,4)=b.kd_bidang_urusan  where a.no_spp=? AND a.kd_skpd=?", [$no_spp, $kd_skpd]))->first();

        $program1 = substr($data_spp->kd_sub_kegiatan, 0, 7);
        $program2 = substr($data_spp->kd_sub_kegiatan, 0, 12);

        if (substr($data_spp->kd_sub_kegiatan, 0, 12) == 0 || substr($data_spp->kd_sub_kegiatan, 0, 12) == '') {
            $nama_program = '';
            $nama_kegiatan = '';
        } else {
            $program = DB::table('ms_program')->select('nm_program')->where('kd_program', $program1)->first();
            $nama_program = $program->nm_program;
            $kegiatan = DB::table('ms_kegiatan')->select('nm_kegiatan')->where('kd_kegiatan', $program2)->first();
            $nama_kegiatan = $kegiatan->nm_kegiatan;
        }

        $daerah1 = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $bank = DB::table('ms_skpd')
            ->select('bank')
            ->where(['kd_skpd' => $kd_skpd])
            ->first();

        $nama_bank = empty($bank->bank) || $bank->bank == '' ? '-' : DB::table('ms_bank')->select('nama')->where(['kode' => $bank->bank])->first()->nama;

        if ($status_anggaran == 'M') {
            $nogub = $daerah1->nogub_susun;
        } else if ($status_anggaran == 'P1') {
            $nogub = $daerah1->nogub_p1;
        } else if ($status_anggaran == 'P2') {
            $nogub = $daerah1->nogub_p2;
        } else if ($status_anggaran == 'P3') {
            $nogub = $daerah1->nogub_p3;
        } else if ($status_anggaran == 'P4') {
            $nogub = $daerah1->nogub_p4;
        } else if ($status_anggaran == 'P5') {
            $nogub = $daerah1->nogub_p5;
        } else if ($status_anggaran == 'U1') {
            $nogub = $daerah1->nogub_perubahan;
        } else if ($status_anggaran == 'U2') {
            $nogub = $daerah1->nogub_perubahan2;
        } else if ($status_anggaran == 'U3') {
            $nogub = $daerah1->nogub_perubahan3;
        } else if ($status_anggaran == 'U4') {
            $nogub = $daerah1->nogub_perubahan4;
        } else {
            $nogub = $daerah1->nogub_perubahan5;
        }

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['kode' => 'BK', 'nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first(),
            'dpa' => DB::table('trhrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first(),
            'data_spp' => $data_spp,
            'nama_program' => $nama_program,
            'nama_kegiatan' => $nama_kegiatan,
            'nogub' => $nogub,
            'nama_bank' => $nama_bank
        ];

        $view = view('skpd.spp_gu.cetak.permintaan')->with($data);
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

    public function sptb(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $spp = collect(DB::select("SELECT a.no_spp,a.tgl_spp,a.kd_skpd,a.nm_skpd,a.bulan,a.nmrekan, a.no_rek,a.npwp,b.kd_bidang_urusan, b.nm_bidang_urusan, a.bank
                , ( SELECT
                            nama
                        FROM
                            ms_bank
                        WHERE
                            kode=a.bank
                ) AS nama_bank, a.nilai
                FROM trhspp a INNER JOIN ms_bidang_urusan b
                ON SUBSTRING(a.kd_skpd,1,4)=b.kd_bidang_urusan  where a.no_spp=? AND a.kd_skpd=?", [$no_spp, $kd_skpd]))->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat', 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['kode' => 'BK', 'nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first(),
            'data_dpa' => DB::table('trhrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first(),
            'data' => $spp
        ];

        $view = view('skpd.spp_gu.cetak.sptb')->with($data);
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

    public function spp(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $spp = collect(DB::select("SELECT a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,a.bank,no_rek,keperluan,a.no_spd,b.kd_sub_kegiatan,b.nm_sub_kegiatan,sum(b.nilai)as nilaispp FROM trhspp a inner join trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd where a.no_spp=? AND b.kd_skpd=? GROUP BY a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,a.bank,no_rek,keperluan,a.no_spd,b.kd_sub_kegiatan,b.nm_sub_kegiatan", [$no_spp, $kd_skpd]))->first();

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat', 'kd_skpd', 'npwp')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['kode' => 'BK', 'nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first(),
            'data_dpa' => DB::table('trhrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first(),
            'data' => $spp,
            'bank' => DB::table('ms_bank')->select('nama')->where(['kode' => $spp->bank])->first(),
            'nilai_spp' => DB::table('trhspp')->select('nilai')->where(['no_spp' => $no_spp])->first(),
            'spd' => DB::table('trhspd')->select('tgl_spd')->where(['no_spd' => $spp->no_spd])->first(),
            'dataspd' => DB::select("SELECT no_spd,tgl_spd,total from trhspd where left(kd_skpd,22)=left(?,22)", [$kd_skpd]),
            'datasp2d' => DB::select("SELECT no_sp2d,tgl_sp2d,nilai as total from trhsp2d where kd_skpd=? and jns_spp='6'", [$kd_skpd]),
            'sub_kegiatan' => collect(DB::select("SELECT kd_sub_kegiatan FROM trdspp WHERE no_spp=? GROUP BY kd_sub_kegiatan", [$no_spp]))->first()->kd_sub_kegiatan
        ];

        $view = view('skpd.spp_gu.cetak.spp77')->with($data);
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

    public function rincian77(Request $request)
    {
        $no_spp = $request->no_spp;
        $beban = $request->beban;
        $spasi = $request->spasi;
        $bendahara = $request->bendahara;
        $pptk = $request->pptk;
        $pa_kpa = $request->pa_kpa;
        $ppkd = $request->ppkd;
        $tanpa = $request->tanpa;
        $kd_skpd = $request->kd_skpd;
        $jenis_print = $request->jenis_print;

        $status_anggaran = status_anggaran();

        $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

        $kegiatan = collect(DB::select("SELECT a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan,sum(b.nilai)as nilaisub FROM trhspp a inner join trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd where a.no_spp=? AND b.kd_skpd=? GROUP BY a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan", [$no_spp, $kd_skpd]))->first();

        $sub_kegiatan = substr($kegiatan->kd_sub_kegiatan, 0, 12);

        $data = [
            'skpd' => DB::table('ms_skpd')->select('nm_skpd', 'alamat', 'kd_skpd', 'npwp')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'no_spp' => $no_spp,
            'header' =>  DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'bendahara' => DB::table('ms_ttd')->where(['kode' => 'BK', 'nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first(),
            'daerah' => $daerah,
            'tanpa' => $tanpa,
            'tanggal' => DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first()->tgl_spp,
            'pptk' => DB::table('ms_ttd')->where(['kode' => 'PPTK', 'nip' => $pptk, 'kd_skpd' => $kd_skpd])->first(),
            'ppkd' => DB::table('ms_ttd')->where(['kode' => 'BUD', 'nip' => $ppkd])->first(),
            'pa_kpa' => DB::table('ms_ttd')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first(),
            'data_dpa' => DB::table('trhrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first(),
            'nilai_spp' => DB::table('trhspp')->select('nilai')->where(['no_spp' => $no_spp])->first(),
            'dataspd' => DB::select("SELECT no_spd,tgl_spd,total from trhspd where left(kd_skpd,22)=left(?,22)", [$kd_skpd]),
            'datasp2d' => DB::select("SELECT no_sp2d,tgl_sp2d,nilai as total from trhsp2d where kd_skpd=? and jns_spp='6'", [$kd_skpd]),
            'sub_kegiatan' => collect(DB::select("SELECT kd_sub_kegiatan FROM trdspp WHERE no_spp=? GROUP BY kd_sub_kegiatan", [$no_spp]))->first()->kd_sub_kegiatan,
            'data_spp' => $kegiatan,
            'data_kegiatan' => DB::select("SELECT a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan,sum(b.nilai)as nilaisub FROM trhspp a inner join trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd where a.no_spp=? AND b.kd_skpd=? GROUP BY a.no_spp,a.tgl_spp,b.kd_skpd,b.nm_skpd,b.kd_sub_kegiatan,b.nm_sub_kegiatan", [$no_spp, $kd_skpd]),
            'spp' => DB::table('trhspp')->where(['no_spp' => $no_spp])->first()
        ];

        $view = view('skpd.spp_gu.cetak.rincian77')->with($data);
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
