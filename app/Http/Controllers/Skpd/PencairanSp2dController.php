<?php

namespace App\Http\Controllers\Skpd;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

use function PHPUnit\Framework\isNull;

class PencairanSp2dController extends Controller
{
    public function index()
    {
        return view('skpd.pencairan_sp2d.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhsp2d')->where(['status_terima' => '1', 'kd_skpd' => $kd_skpd])->orderBy('no_sp2d')->orderBy('kd_skpd')->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd.pencairan_sp2d.tampil_sp2d", Crypt::encryptString($row->no_sp2d)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tampilSp2d($no_sp2d)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_sp2d = Crypt::decryptString($no_sp2d);
        $sp2d = DB::table('trhsp2d')->where(['status_terima' => '1', 'no_sp2d' => $no_sp2d])->first();
        $data = [
            'sp2d' => $sp2d,
            'total_spm' => DB::table('trdspp')->select(DB::raw("SUM(nilai) as nilai"))->where(['no_spp' => $sp2d->no_spp])->first(),
            'total_potongan' => DB::table('trspmpot')->select(DB::raw("SUM(nilai) as nilai"))->where(['no_spm' => $sp2d->no_spm])->first(),
            'urut' => no_urut($kd_skpd)
        ];

        return view('skpd.pencairan_sp2d.show')->with($data);
    }

    public function batalCair(Request $request)
    {
        $no_kas = $request->no_kas;
        $no_sp2d = $request->no_sp2d;
        $beban = $request->beban;
        $jenis = $request->jenis;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $no_spp = DB::table('trhsp2d')->select('no_spp')->where(['no_sp2d' => $no_sp2d])->first();
            $kontrak = DB::table('trhspp')->select('kontrak')->where(['no_spp' => $no_spp->no_spp])->first();
            $total_data = DB::table('trspmpot as a')->join('trhsp2d as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['b.no_sp2d' => $no_sp2d, 'b.kd_skpd' => $kd_skpd,  'a.status_potongan' => '1'])
            //->whereNotIn('a.kd_rek6', ['210601010007', '4140612'])
            ->count();
            if ($total_data > 0) {
                $no_bukti = $no_kas + 1;
            }
            $sts = $no_kas + 1;
            $no_sts =  "$sts";
            $setor = $sts + 2;
            $no_setor = "$setor";
            $total_data1 = DB::table('trspmpot as a')->join('trhsp2d as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where(['b.no_sp2d' => $no_sp2d, 'b.kd_skpd' => $kd_skpd])->whereIn('a.kd_rek6', ['2110801', '4140612'])->count();
            if ($total_data1 > 0) {
                $sts = $no_kas + 1;
                $no_sts = "$sts";
                $setor = $sts + 2;
                $no_setor = "$setor";
            }

            if (($beban < 5) || ($beban == 6 && $jenis != 3)) {
                $total_data2 = DB::table('tr_setorsimpanan')->where(['kd_skpd' => $kd_skpd, 'no_sp2d' => $no_sp2d])->count();
                if ($total_data2 > 0) {
                    DB::table('tr_setorsimpanan')->where(['no_sp2d' => $no_sp2d, 'kd_skpd' => $kd_skpd])->delete();
                } else {
                    DB::table('tr_setorsimpanan')->where(['no_kas' => $no_setor, 'kd_skpd' => $kd_skpd])->delete();
                }
            }
            // dd ($total_data2);
            $tgl_terima = DB::table('trhsp2d')->select('tgl_terima')->where(['no_sp2d' => $no_sp2d, 'kd_skpd' => $kd_skpd])->first();
            DB::table('trhsp2d')->where(['no_sp2d' => $no_sp2d, 'kd_skpd' => $kd_skpd])->update([
                'status' => '0',
                'no_kas' => '',
                'tgl_kas' => $tgl_terima->tgl_terima,
            ]);
            DB::table('trdkasout_pkd')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->delete();
            DB::table('trhkasout_pkd')->where(['no_kas' => $no_kas, 'kd_skpd' => $kd_skpd])->delete();

            if ($beban == '4' && ($jenis == '1' || $jenis == '10')) {
                DB::table('trdtransout')->where(['kd_skpd' => $kd_skpd, 'no_sp2d' => $no_sp2d])->delete();
                DB::table('trhtransout')->where(['no_sp2d' => $no_sp2d, 'kd_skpd' => $kd_skpd])->delete();
            }

            if (($beban == '6' && isset($kontrak)) || $beban == '5') {
                DB::table('trdtransout')->where(['kd_skpd' => $kd_skpd, 'no_sp2d' => $no_sp2d])->delete();
                DB::table('trhtransout')->where(['no_sp2d' => $no_sp2d, 'kd_skpd' => $kd_skpd])->delete();
            }

            if (($beban == '6' && $jenis == '3' )) {
                DB::table('trdtransout')->where(['kd_skpd' => $kd_skpd, 'no_sp2d' => $no_sp2d])->delete();
                DB::table('trhtransout')->where(['no_sp2d' => $no_sp2d, 'kd_skpd' => $kd_skpd])->delete();
            }

            if ($beban == '6' || $beban == '5' || $beban == '4') {
                DB::table('trdstrpot')->where(['kd_skpd' => $kd_skpd, 'no_sp2d' => $no_sp2d])->delete();
                DB::table('trhstrpot')->where(['no_sp2d' => $no_sp2d, 'kd_skpd' => $kd_skpd])->delete();
                DB::table('trdkasin_pkd')->where(['no_sts' => $no_sts, 'kd_skpd' => $kd_skpd])->delete();
                DB::table('trhkasin_pkd')->where(['no_sts' => $no_sts, 'kd_skpd' => $kd_skpd])->delete();
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

    public function simpanCair(Request $request)
    {
        $no_kas = $request->no_kas;
        $tgl_cair = $request->tgl_cair;
        $jenis = $request->jenis;
        $beban = $request->beban;
        $no_kontrak = $request->no_kontrak;
        $tgl_terima = $request->tgl_terima;
        $nilai = $request->nilai;
        $no_sp2d = $request->no_sp2d;
        $opd = $request->opd;
        $keperluan = $request->keperluan;
        $npwp = $request->npwp;
        $total_potongan = $request->total_potongan;
        $tgl_sp2d = $request->tgl_sp2d;
        $nama = Auth::user()->nama;

        DB::beginTransaction();
        try {
            $no_spp = DB::table('trhsp2d')->select('no_spp')->where(['no_sp2d' => $no_sp2d])->first();
            // $kontrak = DB::table('trhspp')->select('kontrak')->where(['no_spp' => $no_spp->no_spp]);
            $kontrak = DB::table('trhspp')->selectRaw("ISNULL(kontrak, '') as kontrak")->where(['no_spp' => $no_spp->no_spp])->first();
            $kontrak = isset($kontrak) ? $kontrak->kontrak : '';
            $skpd = DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $opd])->first();

            DB::table('trhsp2d')->where(['no_sp2d' => $no_sp2d])->update([
                'status' => '1',
                'no_kas' => $no_kas,
                'tgl_kas' => $tgl_cair,
                'nocek' => $no_kontrak,
                'user_cair' => Auth::user()->nama,
                'update_cair' => date('Y-m-d H:i:s')
            ]);

            // $total_data = DB::table('trspmpot as a')
            //     ->join('trhsp2d as b', 'a.no_spm', '=', 'b.no_spm')
            //     ->where(['b.no_sp2d' => $no_sp2d])
            //     ->whereNotIn('a.kd_rek6', ['2110801', '4140612'])
            //     ->count();
            $total_data = collect(DB::select("SELECT COUNT(*) as jumlah FROM trspmpot a
            INNER JOIN trhsp2d b ON a.no_spm = b.no_spm
            WHERE b.no_sp2d = ? AND a.kd_rek6 NOT IN ('2110801','4140612')", [$no_sp2d]))->first();
            // berhasil
            $bukti_str = $no_kas;

            if ($total_data->jumlah > 0) {
                $bukti_str = $no_kas + 1;
                $bukti_str1 = "$bukti_str";
                $data_pot = DB::table('trhtrmpot')->select('no_bukti')->where(['no_sp2d' => $no_sp2d])->first();
                // if (isNull($data_pot)) {
                //     $no_bukti = '';
                // } else {
                //     $no_bukti = $data_pot->no_bukti;
                // }

                $no_bukti = isset($data_pot) ? $data_pot->no_bukti : '';

                $data_potongan = DB::table('trspmpot as a')
                    ->join('trhsp2d as b', 'a.no_spm', '=', 'b.no_spm')
                    ->where(['b.no_sp2d' => $no_sp2d])
                    ->whereNotIn('a.kd_rek6', ['2110801', '4140612'])
                    ->select('a.*', 'b.jns_spp', 'b.nmrekan', 'b.npwp')
                    ->get();

                $data_potongan = json_decode(json_encode($data_potongan), true);

                if (isset($data_potongan)) {
                    DB::table('trdstrpot')->insert(array_map(function ($value) use ($bukti_str1, $opd, $no_sp2d) {
                        return [
                            'no_bukti' => $bukti_str1,
                            'kd_rek6' => $value['kd_rek6'],
                            'nm_rek6' => $value['nm_rek6'],
                            'nilai' => $value['nilai'],
                            'kd_skpd' => $opd,
                            'kd_rek_trans' => $value['kd_trans'],
                            'map_pot' => $value['map_pot'],
                            'no_sp2d' => $no_sp2d,
                            'ntpn' => $value['ntpn'],
                            'rekanan' => $value['nmrekan'],
                            'npwp' => $value['npwp'],
                            'ebilling' => $value['idBilling'],
                        ];
                    }, $data_potongan));
                }

                $data_potongan2 = DB::table('trspmpot as a')
                    ->join('trhsp2d as b', 'a.no_spm', '=', 'b.no_spm')
                    ->join('trhspp as c', 'b.no_spp', '=', 'c.no_spp')
                    ->where(['b.no_sp2d' => $no_sp2d])
                    ->select(DB::raw("SUM(a.nilai) as nilai_pot"), 'b.keperluan', 'b.npwp', 'b.jns_spp', 'b.nm_skpd', 'c.kd_sub_kegiatan', 'c.nm_sub_kegiatan', 'c.nmrekan', 'c.pimpinan', 'c.alamat')
                    ->groupBy('no_sp2d', 'b.keperluan', 'b.npwp', 'b.jns_spp', 'b.nm_skpd', 'c.kd_sub_kegiatan', 'c.nm_sub_kegiatan', 'c.nmrekan', 'c.pimpinan', 'c.alamat')
                    ->get();

                $data_potongan2 = json_decode(json_encode($data_potongan2), true);

                if (isset($data_potongan2)) {
                    DB::table('trhstrpot')->insert(array_map(function ($value) use ($bukti_str1, $tgl_cair, $nama, $opd, $no_bukti, $no_sp2d) {
                        return [
                            'no_bukti' => $bukti_str1,
                            'tgl_bukti' => $tgl_cair,
                            'ket' => 'Setor pajak nomor SP2D  ' . $no_sp2d,
                            'username' => $nama,
                            'tgl_update' => '',
                            'kd_skpd' => $opd,
                            'nm_skpd' => $value['nm_skpd'],
                            'no_terima' => $no_bukti,
                            'nilai' => $value['nilai_pot'],
                            'npwp' => $value['npwp'],
                            'jns_spp' => $value['jns_spp'],
                            'no_sp2d' => $no_sp2d,
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            'nm_sub_kegiatan' => $value['nm_sub_kegiatan'],
                            'nmrekan' => $value['nmrekan'],
                            'pimpinan' => $value['pimpinan'],
                            'alamat' => $value['alamat'],
                        ];
                    }, $data_potongan2));
                }
            }

            $setor = $bukti_str + 1;
            $no_setor = "$setor";

            // $total_data1 = DB::table('trspmpot as a')->join('trhsp2d as b', 'a.no_spm', '=', 'b.no_spm')->where(['b.no_sp2d' => $no_sp2d])->whereIn('a.kd_rek6', ['210601010003', '210601010017', '210601010001', '210601010021', '210601010019', '210601010007', '210601020001', '210601020009', '210601010022', '210601010011', '210601010012', '210601010009', '410411010001'])->count();

            $total_data1 = collect(DB::select("SELECT COUNT(*) as jumlah FROM trspmpot a INNER JOIN trhsp2d b ON a.no_spm = b.no_spm WHERE b.no_sp2d = '$no_sp2d' AND a.kd_rek6 = '2110801' and a.status_potongan = '1' "))->first();

            if ($total_data1->jumlah > 0) {
                $sts = $bukti_str + 1;
                $no_sts = "$sts";
                $setor = $sts + 2;
                $no_setor = "$setor";

                $data_potongan = DB::table('trspmpot as a')
                    ->selectRaw("a.*,c.kd_sub_kegiatan")
                    ->leftJoin('trhsp2d as b', function ($join) {
                        $join->on('a.no_spm', '=', 'b.no_spm');
                        $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    })
                    ->join('trdspp as c', function ($join) {
                        $join->on('b.no_spp', '=', 'c.no_spp');
                        $join->on('b.kd_skpd', '=', 'c.kd_skpd');
                    })
                    ->whereRaw("b.no_sp2d = ? and a.status_potongan = '1' ", [$no_sp2d])
                    ->groupByRaw("a.no_spm,a.kd_skpd,a.kd_rek6,a.nm_rek6,a.nilai,a.pot,a.kd_trans,a.map_pot,a.nm_pot,a.status_potongan,a.noreff,a.nomorPokokWajibPajak,a.namaWajibPajak,a.alamatWajibPajak,a.kota,a.nik,a.kodeMap,a.keteranganKodeMap,a.kodeSetor,a.keteranganKodeSetor,a.masaPajak,a.tahunPajak,a.jumlahBayar,a.nomorObjekPajak,a.nomorSK,a.nomorPokokWajibPajakPenyetor,a.nomorPokokWajibPajakRekanan,a.nikRekanan,a.nomorFakturPajak,a.idBilling,a.tanggalExpiredBilling,a.tgl_setor,a.status_setor,a.ntpn,a.keterangan,a.jenis,a.username,a.last_update,c.kd_sub_kegiatan")
                    ->get();

                $totalkasin = 0;
                foreach ($data_potongan as $potongan) {
                    $totalkasin += $potongan->nilai;
                    $nm_rek6 = $potongan->nm_rek6;
                    $kd_rek6 = $potongan->kd_rek6;
                    $kd_sub_kegiatan = $potongan->kd_sub_kegiatan;
                }
                $data_potongan = json_decode(json_encode($data_potongan), true);

                if (isset($data_potongan)) {
                    DB::table('trdkasin_pkd')->insert(array_map(function ($value) use ($no_sts, $opd) {
                        return [
                            'kd_skpd' => $opd,
                            'no_sts' => $no_sts,
                            'kd_rek6' => $value['kd_rek6'],
                            'rupiah' => $value['nilai'],
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        ];
                    }, $data_potongan));
                }

                if ($beban == '4') {
                    // if (isset($data_potongan)) {
                    //     DB::table('trhkasin_pkd')->insert(array_map(function ($value) use ($no_sts, $opd, $tgl_cair, $no_sp2d, $totalkasin) {
                    //         // if ($value['kd_rek6'] == '410411010001') {
                    //         //     $jns_transdenda = '4';
                    //         // } else {
                    //         //     $jns_transdenda = '5';
                    //         // }
                    //         return [
                    //             'no_sts' => $no_sts,
                    //             'kd_skpd' => $opd,
                    //             'tgl_sts' => $tgl_cair,
                    //             'keterangan' => $value['nm_rek6'] . ' atas SP2D ' . $no_sp2d,
                    //             'total' => $totalkasin,
                    //             'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                    //             'jns_trans' => trans_denda($value['kd_rek6']),
                    //             'no_kas' => $no_sts,
                    //             'tgl_kas' => $tgl_cair,
                    //             'sumber' => '0',
                    //             'jns_cp' => '1',
                    //             'pot_khusus' => '1',
                    //             'no_sp2d' => $no_sp2d,
                    //         ];
                    //     }, $data_potongan));
                    // }

                    DB::table('trhkasin_pkd')
                        ->insert([
                            'no_sts' => $no_sts,
                            'kd_skpd' => $opd,
                            'tgl_sts' => $tgl_cair,
                            'keterangan' => $nm_rek6 . ' atas SP2D ' . $no_sp2d,
                            'total' => $totalkasin,
                            'kd_sub_kegiatan' => $kd_sub_kegiatan,
                            'jns_trans' => trans_denda($kd_rek6),
                            'no_kas' => $no_sts,
                            'tgl_kas' => $tgl_cair,
                            'sumber' => '0',
                            'jns_cp' => '1',
                            'pot_khusus' => '1',
                            'no_sp2d' => $no_sp2d,
                        ]);
                }

                if ($beban == '6') {
                    // if (isset($data_potongan)) {
                    //     DB::table('trhkasin_pkd')->insert(array_map(function ($value) use ($no_sts, $opd, $tgl_cair, $no_sp2d, $totalkasin) {
                    //         // if ($value['kd_rek6'] == '410411010001') {
                    //         //     $jns_transdenda = '4';
                    //         // } else {
                    //         //     $jns_transdenda = '5';
                    //         // }
                    //         return [
                    //             'no_sts' => $no_sts,
                    //             'kd_skpd' => $opd,
                    //             'tgl_sts' => $tgl_cair,
                    //             'keterangan' => $value['nm_rek6'] . ' atas SP2D ' . $no_sp2d,
                    //             'total' => $totalkasin,
                    //             'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                    //             'jns_trans' => trans_denda($value['kd_rek6']),
                    //             'no_kas' => $no_sts,
                    //             'tgl_kas' => $tgl_cair,
                    //             'sumber' => '0',
                    //             'jns_cp' => '2',
                    //             'pot_khusus' => '2',
                    //             'no_sp2d' => $no_sp2d,
                    //         ];
                    //     }, $data_potongan));
                    // }
                    DB::table('trhkasin_pkd')
                        ->insert([
                            'no_sts' => $no_sts,
                            'kd_skpd' => $opd,
                            'tgl_sts' => $tgl_cair,
                            'keterangan' => $nm_rek6 . ' atas SP2D ' . $no_sp2d,
                            'total' => $totalkasin,
                            'kd_sub_kegiatan' => $kd_sub_kegiatan,
                            'jns_trans' => trans_denda($kd_rek6),
                            'no_kas' => $no_sts,
                            'tgl_kas' => $tgl_cair,
                            'sumber' => '0',
                            'jns_cp' => '2',
                            'pot_khusus' => '2',
                            'no_sp2d' => $no_sp2d,
                        ]);
                }
            }

            if (($beban < 5) or ($beban == 6 && $jenis != 3)) {
                DB::table('tr_setorsimpanan')->insert([
                    'no_kas' => $no_setor,
                    'tgl_kas' => $tgl_cair,
                    'no_bukti' => $no_setor,
                    'tgl_bukti' => $tgl_cair,
                    'kd_skpd' => $opd,
                    'nilai' => $nilai - $total_potongan,
                    'keterangan' => 'PU BANK atas SP2D ' . $no_sp2d,
                    'jenis' => '1',
                    'no_sp2d' => $no_sp2d,
                ]);
            }

            $trans = $setor + 1;
            $no_trans = "$trans";

            if (($beban == '4') && ($jenis == '1' || $jenis == '10')) {
                DB::table('trhtransout')->insert([
                    'no_kas' => $no_trans,
                    'tgl_kas' => $tgl_cair,
                    'no_bukti' => $no_trans,
                    'tgl_bukti' => $tgl_cair,
                    'no_sp2d' => $no_sp2d,
                    'kd_skpd' => $opd,
                    'nm_skpd' => $skpd->nm_skpd,
                    'total' => $nilai,
                    'ket' => $keperluan,
                    'jns_spp' => $beban,
                    'username' => $nama,
                    'tgl_update' => '',
                    'pay' => 'BANK',
                ]);
            }

            // berhasil
            if ($beban == '5') {
                DB::table('trhtransout')->insert([
                    'no_kas' => $no_kas,
                    'tgl_kas' => $tgl_cair,
                    'no_bukti' => $no_kas,
                    'tgl_bukti' => $tgl_cair,
                    'no_sp2d' => $no_sp2d,
                    'kd_skpd' => $opd,
                    'nm_skpd' => $skpd->nm_skpd,
                    'total' => $nilai,
                    'ket' => $keperluan,
                    'jns_spp' => $beban,
                    'username' => $nama,
                    'tgl_update' => '',
                    'pay' => 'LS',
                ]);
            }


            // berhasil
            if (($beban=='6' && $jenis == 3)) {
                DB::table('trhtransout')->insert([
                    'no_kas' => $no_kas,
                    'tgl_kas' => $tgl_cair,
                    'no_bukti' => $no_kas,
                    'tgl_bukti' => $tgl_cair,
                    'no_sp2d' => $no_sp2d,
                    'kd_skpd' => $opd,
                    'nm_skpd' => $skpd->nm_skpd,
                    'total' => $nilai,
                    'ket' => $keperluan,
                    'jns_spp' => $beban,
                    'username' => $nama,
                    'tgl_update' => '',
                    'pay' => 'LS',
                ]);
            }

            $data_spp = DB::table('trdspp as a')
                ->leftJoin('trhspp as b', 'a.no_spp', '=', 'b.no_spp')
                ->leftJoin('trhspm as c', 'b.no_spp', '=', 'c.no_spp')
                ->leftJoin('trhsp2d as d', 'c.no_spm', '=', 'd.no_spm')
                ->where(['d.no_sp2d' => $no_sp2d])
                ->selectRaw("a.no_spp,a.kd_skpd,a.kd_sub_kegiatan,a.kd_rek6,a.nilai,b.bulan,c.no_spm,d.no_sp2d,b.sts_tagih,a.sumber")
                ->get();

            // if (isset($data_spp)) {
            //     $giat = DB::table('trskpd')->select('nm_sub_kegiatan')->where(['kd_sub_kegiatan' => $data_spp->kd_sub_kegiatan])->first();
            //     $nm_giat = $giat->nm_sub_kegiatan;
            // } else {
            //     $nm_giat = '';
            // }
            // if ($beban == '1') {
            //     $nmrek6 = "Uang Persediaan";
            // } else {
            //     if (isset($data_spp)) {
            //         $rek6 = DB::table('ms_rek6')->select('nm_rek6')->where(['kd_rek6' => $data_spp->kd_rek6])->first();
            //         $nmrek6 = $rek6->nm_rek6;
            //     } else {
            //         $nmrek6 = '';
            //     }
            // }
            $data_spp = json_decode(json_encode($data_spp), true);

            if (($beban == '4') && ($jenis == '1' || $jenis == '10')) {
                if (isset($data_spp)) {
                    DB::table('trdtransout')->insert(array_map(function ($value) use ($no_trans, $no_sp2d, $beban) {
                        return [
                            'no_bukti' => $no_trans,
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            'nm_sub_kegiatan' => empty($value['kd_sub_kegiatan']) ? '' : nama_kegiatan_cair($value['kd_sub_kegiatan']),
                            'kd_rek6' => $value['kd_rek6'],
                            'nm_rek6' => empty($value['kd_rek6']) ? '' : nama_rekening_cair($beban, $value['kd_rek6']),
                            'nilai' => $value['nilai'],
                            'no_sp2d' => $no_sp2d,
                            'kd_skpd' => $value['kd_skpd'],
                            'sumber' => $value['sumber'],
                        ];
                    }, $data_spp));
                }

                // DB::table('trdtransout')->insert([
                //     'no_bukti' => $no_trans,
                //     'kd_sub_kegiatan' => $data_spp->kd_sub_kegiatan,
                //     'nm_sub_kegiatan' => $nm_giat,
                //     'kd_rek6' => $data_spp->kd_rek6,
                //     'nm_rek6' => $nmrek6,
                //     'nilai' => $data_spp->nilai,
                //     'no_sp2d' => $no_sp2d,
                //     'kd_skpd' => $data_spp->kd_skpd,
                //     'sumber' => $data_spp->sumber,
                // ]);
            }

             // berhasil
             if (($beban == 5)) {
                if (isset($data_spp)) {
                    DB::table('trdtransout')->insert(array_map(function ($value) use ($no_kas, $no_sp2d, $beban) {
                        return [
                            'no_bukti' => $no_kas,
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            'nm_sub_kegiatan' => empty($value['kd_sub_kegiatan']) ? '' : nama_kegiatan_cair($value['kd_sub_kegiatan']),
                            'kd_rek6' => $value['kd_rek6'],
                            'nm_rek6' => empty($value['kd_rek6']) ? '' : nama_rekening_cair($beban, $value['kd_rek6']),
                            'nilai' => $value['nilai'],
                            'no_sp2d' => $no_sp2d,
                            'kd_skpd' => $value['kd_skpd'],
                            'sumber' => $value['sumber'],
                        ];
                    }, $data_spp));
                }

                // DB::table('trdtransout')->insert([
                //     'no_bukti' => $no_kas,
                //     'kd_sub_kegiatan' => $data_spp->kd_sub_kegiatan,
                //     'nm_sub_kegiatan' => $nm_giat,
                //     'kd_rek6' => $data_spp->kd_rek6,
                //     'nm_rek6' => $nmrek6,
                //     'nilai' => $data_spp->nilai,
                //     'no_sp2d' => $no_sp2d,
                //     'kd_skpd' => $data_spp->kd_skpd,
                //     'sumber' => $data_spp->sumber,
                // ]);
            }

            // berhasil
            if (($beban == '6' && $jenis ==3)) {
                if (isset($data_spp)) {
                    DB::table('trdtransout')->insert(array_map(function ($value) use ($no_kas, $no_sp2d, $beban) {
                        return [
                            'no_bukti' => $no_kas,
                            'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                            'nm_sub_kegiatan' => empty($value['kd_sub_kegiatan']) ? '' : nama_kegiatan_cair($value['kd_sub_kegiatan']),
                            'kd_rek6' => $value['kd_rek6'],
                            'nm_rek6' => empty($value['kd_rek6']) ? '' : nama_rekening_cair($beban, $value['kd_rek6']),
                            'nilai' => $value['nilai'],
                            'no_sp2d' => $no_sp2d,
                            'kd_skpd' => $value['kd_skpd'],
                            'sumber' => $value['sumber'],
                        ];
                    }, $data_spp));
                }

                // DB::table('trdtransout')->insert([
                //     'no_bukti' => $no_kas,
                //     'kd_sub_kegiatan' => $data_spp->kd_sub_kegiatan,
                //     'nm_sub_kegiatan' => $nm_giat,
                //     'kd_rek6' => $data_spp->kd_rek6,
                //     'nm_rek6' => $nmrek6,
                //     'nilai' => $data_spp->nilai,
                //     'no_sp2d' => $no_sp2d,
                //     'kd_skpd' => $data_spp->kd_skpd,
                //     'sumber' => $data_spp->sumber,
                // ]);
            }

            // berhasil
            // DB::table('trdstrpot as a')->join('trhstrpot as b', function ($join) {
            //     $join->on('a.no_bukti', '=', 'b.no_bukti');
            //     $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            // })->where(['a.kd_skpd' => $opd])->update([
            //     'a.id_terima' => DB::raw("(SELECT DISTINCT id FROM trdtrmpot WHERE trdtrmpot.kd_skpd=a.kd_skpd AND trdtrmpot.kd_rek6=a.kd_rek6 AND trdtrmpot.kd_rek_trans=a.kd_rek_trans AND trdtrmpot.nilai=a.nilai AND trdtrmpot.no_bukti=b.no_terima)")
            // ]);

            DB::update("UPDATE R
        SET R.id_terima = (select DISTINCT id from trdtrmpot  where
        trdtrmpot.kd_skpd=R.kd_skpd
        and trdtrmpot.kd_rek6=R.kd_rek6
        and trdtrmpot.kd_rek_trans=R.kd_rek_trans
        and trdtrmpot.nilai=R.nilai
        and trdtrmpot.no_bukti=H.no_terima
        )
        FROM trdstrpot AS R
        INNER JOIN trhstrpot AS H ON R.no_bukti = H.no_bukti and R.kd_skpd=H.kd_skpd where R.kd_skpd=?", [$opd]);

            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => '0',
                'error' => $e
            ]);
        }
    }

    public function cekCair(){
        $kd_skpd = Auth::user()->kd_skpd;
        $skpd = collect(DB::select("SELECT kd_skpd, nm_skpd from ms_skpd where kd_skpd = ?",[$kd_skpd]))->first();

        $data =
        [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'detail' => DB::select("SELECT no_sp2d, tgl_sp2d, no_spm, nm_skpd, keperluan, nilai from trhsp2d where status_terima = '1' and status = '0' and kd_skpd = ? order by no_sp2d",[$kd_skpd]),
            'skpd' => $skpd->nm_skpd
        ];
        //dd($data['detail']);
        return view('skpd.pencairan_sp2d.cekcair')->with($data)->with('_blank');;
    }
}
