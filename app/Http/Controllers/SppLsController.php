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

class SppLsController extends Controller
{
    public function index()
    {
        $kunci = kunci()->kunci_spp_ls;
        $role = Auth::user()->role;

        $kuncian = $kunci == 1 && !in_array($role, ['1006', '1012', '1016', '1017']) ? '1' : '0';

        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'data_spp' => DB::table('trhspp')->where('kd_skpd', $kd_skpd)->whereNotIn('jns_spp', ['1', '2', '3'])->orderByRaw("tgl_spp ASC, no_spp ASC,CAST(urut AS INT) ASC")->get(),
            'bendahara' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['KPA', 'BPP', 'BK'])->get(),
            'pptk' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PPTK', 'KPA'])->get(),
            'pa_kpa' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', $kd_skpd)->whereIn('kode', ['PA', 'KPA'])->get(),
            'ppkd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', '5.02.0.00.0.00.02.0000')->whereIn('kode', ['BUD', 'KPA'])->get(),
            'cek' => selisih_angkas(),
            'kd_skpd' => $kd_skpd,
            'kunci' => $kuncian,
        ];

        //dd($data['kd_skpd']);
        return view('penatausahaan.pengeluaran.spp_ls.index')->with($data);
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trhspp')->where('kd_skpd', $kd_skpd)->whereNotIn('jns_spp', ['1', '2', '3'])->orderByRaw("tgl_spp ASC, no_spp ASC,CAST(urut AS INT) ASC")->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("sppls.show", Crypt::encryptString($row->no_spp)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="fas fa-info-circle"></i></a>';
            $btn .= '<a href="' . route("sppls.edit", Crypt::encryptString($row->no_spp)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" style="margin-right:4px" onclick="cetak(\'' . $row->no_spp . '\', \'' . $row->jns_spp . '\', \'' . $row->kd_skpd . '\');" class="btn btn-success btn-sm"><i class="uil-print"></i></a>';

            if ($row->status == 1 || $row->sp2d_batal == '1') {
                $btn .= "";
            } else {
                $btn .= '<a href="javascript:void(0);" onclick="batal_spp(\'' . $row->no_spp . '\', \'' . $row->jns_spp . '\', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-ban"></i></a>';
            }

            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $skpd = Auth::user()->kd_skpd;
        $kd_skpd = substr($skpd, 0, 22);
        $perusahaan1 = DB::table('ms_perusahaan')->select('nama as nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEFT(kd_skpd,22) = ?', [$kd_skpd])->groupBy('nama', 'pimpinan', 'npwp', 'alamat');
        $perusahaan2 = DB::table('trhspp')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEN(nmrekan)>1')->where('kd_skpd', $skpd)->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($perusahaan1);
        $perusahaan3 = DB::table('trhtrmpot_cmsbank')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEN(nmrekan)>1')->where('kd_skpd', $skpd)->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($perusahaan2);
        $perusahaan4 = DB::table('trhtrmpot')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEN(nmrekan)>1')->where('kd_skpd', $skpd)->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($perusahaan3);
        $result = DB::table(DB::raw("({$perusahaan4->toSql()}) AS sub"))
            ->select("nmrekan", "pimpinan", "npwp", "alamat")
            ->mergeBindings($perusahaan4)
            ->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->orderBy('nmrekan', 'ASC')
            ->orderBy('pimpinan', 'ASC')
            ->orderBy('npwp', 'ASC')
            ->orderBy('alamat', 'ASC')
            ->get();
        $data1 = DB::select(DB::raw("SELECT isnull(no_tagih,'') no_tagih from trhspp where kd_skpd='$skpd' and (sp2d_batal is null OR sp2d_batal<>'1') GROUP BY no_tagih"));
        $data2 = json_decode(json_encode($data1), true);
        $data = [
            'data_skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')->where('kd_skpd', $skpd)->first(),
            'daftar_rekanan' => $result,
            'daftar_penerima' => DB::table('ms_rekening_bank_online')
            ->select('rekening', 'nm_rekening', 'npwp', 'nmrekan', 'pimpinan', 'alamat', 'bank', 'nm_bank')
            ->where(['kd_skpd' => $skpd, 'keperluan' => '2'])
            ->orderBy('rekening')
            ->get(),
            'daftar_bank' => DB::table('ms_bank')->select('kode', 'nama')->orderBy('kode')->get(),
            'daftar_penagihan' => DB::table('trhtagih as a')->select('a.kd_skpd', 'a.no_bukti', 'tgl_bukti', 'a.ket', 'a.kontrak', 'kd_sub_kegiatan', DB::raw('SUM(b.nilai) as total'))->join('trdtagih as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where('a.kd_skpd', $skpd)->where('a.jns_trs', '1')->whereNotIn('a.no_bukti', $data2)->groupBy('a.kd_skpd', 'a.no_bukti', 'tgl_bukti', 'a.ket', 'a.kontrak', 'kd_sub_kegiatan')->orderBy('a.no_bukti')->get(),
            'data_tgl' => DB::table('trhspp')->selectRaw('MAX(tgl_spp) as tgl_spp')->where('kd_skpd', $skpd)->where(function ($query) {
                $query->where('sp2d_batal', '=', '0')
                    ->orWhereNull('sp2d_batal');
            })->first(),
            'data_opd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where('kd_skpd', $skpd)->first(),
        ];

        return view('penatausahaan.pengeluaran.spp_ls.create')->with($data);
    }

    public function cariJenis(Request $request)
    {
        $beban = $request->beban;
        if ($beban == '3') {
            $data = [
                "id"   => 1,
                "text" => " TU",
            ];
        } elseif ($beban == '4') {
            $data = [
                [
                    "id"   => 1,
                    "text" => " Gaji & Tunjangan"
                ],
                // [
                //     "id"   => 2,
                //     "text" => " Kespeg"
                // ],
                // [
                //     "id"   => 3,
                //     "text" => " Uang Makan"
                // ],
                // [
                //     "id"   => 4,
                //     "text" => " Upah Pungut"
                // ],
                // [
                //     "id"   => 5,
                //     "text" => " Upah Pungut PBB"
                // ],
                // [
                //     "id"   => 6,
                //     "text" => " Upah Pungut PBB-KB PKB & BBN-KB"
                // ],
                [
                    "id"   => 7,
                    "text" => " Tambahan/Kekurangan Gaji & Tunjangan"
                ],
                [
                    "id"   => 8,
                    "text" => " Tunjangan Transport"
                ],
                [
                    "id"   => 9,
                    "text" => " Tunjangan Lainnya"
                ]
                // [
                //     "id"   => 10,
                //     "text" => " Gaji Anggota DPRD"
                // ]
            ];
        } elseif ($beban == '5') {
            $data = [
                [
                    "id"   => 1,
                    "text" => "Hibah berupa uang"
                ],
                [
                    "id"   => 2,
                    "text" => " Bantuan Sosial berupa uang"
                ],
                [
                    "id"   => 3,
                    "text" => " Bantuan Keuangan"
                ],
                [
                    "id"   => 4,
                    "text" => " Subsidi"
                ],
                [
                    "id"   => 5,
                    "text" => " Bagi Hasil"
                ],
                [
                    "id"   => 6,
                    "text" => " Belanja Tidak Terduga"
                ],
                [
                    "id"   => 7,
                    "text" => " Pembayaran kewajiban pemda atas putusan pengadilan, dan rekomendasi APIP dan/atau rekomendasi BPK"
                ],
                [
                    "id"   => 8,
                    "text" => " Pengeluaran Pembiayaan"
                ],
                [
                    "id" => 9,
                    "text" => "Barang yang diserahkan ke masyarakat"
                ]
            ];
        } elseif ($beban == '6') {
            $data = [
                [
                    "id"   => 1,
                    "text" => " LS Rutin (PNS)"
                ],
                [
                    "id"   => 2,
                    "text" => " LS Rutin (Non PNS)"
                ],
                [
                    "id"   => 3,
                    "text" => " LS Pihak Ketiga"
                ]
                // [
                //     "id"   => 4,
                //     "text" => " Honor Kontrak"
                // ],
                // [
                //     "id"   => 5,
                //     "text" => " Jasa Pelayanan Kesehatan"
                // ],
                // [
                //     "id"   => 6,
                //     "text" => " Pihak ketiga"
                // ],
                // [
                //     "id"   => 7,
                //     "text" => " Rutin (PNS)"
                // ]
            ];
        }

        return response()->json($data);
    }

    public function cariNomorSpd(Request $request)
    {
        $beban = $request->beban;
        $tgl_spp = $request->tgl_spp;
        $kd_skpd = Auth::user()->kd_skpd;
        $skpd = substr($kd_skpd, 0, 22);
        $bulan = date('m', strtotime($tgl_spp));
        if ($beban == '4') {
            $beban = ['5'];
        } elseif ($beban == '5') {
            $beban = ['5', '6'];
        } elseif ($beban == '6') {
            $beban = ['5'];
        }

        $data = DB::table('trhspd')->select('no_spd', 'tgl_spd', 'bulan_awal','total')->whereRaw('LEFT(kd_skpd,22) = ?', [$skpd])->where('status', '1')->where('tgl_spd', '<=', $tgl_spp)->whereIn('jns_beban', $beban)->orderBy('bulan_awal', 'DESC')->get();
        return response()->json($data);
    }

    public function cariSubKegiatan(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $spd = $request->spd;
        $skpd = substr($kd_skpd, 18, 4);

        $kd_bpp = Auth::user()->kd_bpp;
        $id_user = Auth::user()->id;
        $bpp = substr($kd_bpp, 23, 1);

        if ($skpd == "0000") {
            if ($bpp != '0') {
                $data = DB::table('trdspd as a')->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan', 'a.kd_program', 'a.nm_program', 'c.status_keg', DB::raw("(SELECT distinct trskpd.kd_skpd from trskpd where trskpd.kd_sub_kegiatan=a.kd_sub_kegiatan and trskpd.kd_skpd=b.kd_skpd) as bidang"))->distinct()->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->join('trskpd as c', function ($join) {
                    $join->on('a.kd_sub_kegiatan', '=', 'c.kd_sub_kegiatan');
                    $join->on('b.kd_skpd', '=', 'c.kd_skpd');
                })->where('a.no_spd', $spd)->where('c.status_sub_kegiatan', '1')->where(function ($query) {
                    $query->where('c.status_keg', '<>', '0')
                        ->orWhereNull('c.status_keg');
                })->whereRaw("a.kd_sub_kegiatan IN (SELECT kd_sub_kegiatan FROM pelimpahan_kegiatan WHERE kd_bpp=? AND kd_skpd=? AND id_user=?)", [$kd_bpp, $kd_skpd, $id_user])->orderBy('a.kd_sub_kegiatan')->get();
            } else {
                $data = DB::table('trdspd as a')->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan', 'a.kd_program', 'a.nm_program', 'c.status_keg', DB::raw("(SELECT distinct trskpd.kd_skpd from trskpd where trskpd.kd_sub_kegiatan=a.kd_sub_kegiatan and trskpd.kd_skpd=b.kd_skpd) as bidang"))->distinct()->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->join('trskpd as c', function ($join) {
                    $join->on('a.kd_sub_kegiatan', '=', 'c.kd_sub_kegiatan');
                    $join->on('b.kd_skpd', '=', 'c.kd_skpd');
                })->where('a.no_spd', $spd)->where('c.status_sub_kegiatan', '1')->where(function ($query) {
                    $query->where('c.status_keg', '<>', '0')
                        ->orWhereNull('c.status_keg');
                })->orderBy('a.kd_sub_kegiatan')->get();
            }
        } else {
            if ($bpp != '0') {
                $data = DB::table('trdspd as a')->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan', 'a.kd_program', 'a.nm_program', 'c.status_keg', 'c.kd_skpd as bidang')->distinct()->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->join('trskpd as c', function ($join) {
                    $join->on('a.kd_sub_kegiatan', '=', 'c.kd_sub_kegiatan');
                    $join->on(DB::raw("LEFT(b.kd_skpd, 22)"), '=', DB::raw("LEFT(c.kd_skpd, 22)"));
                })->where('a.no_spd', $spd)->where('c.status_sub_kegiatan', '1')->where('c.kd_skpd', $kd_skpd)->whereRaw("a.kd_sub_kegiatan IN (SELECT kd_sub_kegiatan FROM pelimpahan_kegiatan WHERE kd_bpp=? AND kd_skpd=? AND id_user=?)", [$kd_bpp, $kd_skpd, $id_user])->orderBy('a.kd_sub_kegiatan')->get();
            } else {
                $data = DB::table('trdspd as a')->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan', 'a.kd_program', 'a.nm_program', 'c.status_keg', 'c.kd_skpd as bidang')->distinct()->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->join('trskpd as c', function ($join) {
                    $join->on('a.kd_sub_kegiatan', '=', 'c.kd_sub_kegiatan');
                    $join->on(DB::raw("LEFT(b.kd_skpd, 22)"), '=', DB::raw("LEFT(c.kd_skpd, 22)"));
                })->where('a.no_spd', $spd)->where('c.status_sub_kegiatan', '1')->where('c.kd_skpd', $kd_skpd)->orderBy('a.kd_sub_kegiatan')->get();
            }
        }
        return response()->json($data);
    }

    public function cariRekening(Request $request)
    {
        //kenapa dikasih status aktif di trdrka???
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trdrka')->select('kd_rek6', 'nm_rek6')->distinct()->where(['kd_sub_kegiatan' => $kd_sub_kegiatan, 'kd_skpd' => $kd_skpd
        , 'status_aktif' => '1'
         ])->orderBy('kd_rek6')->get();
        return response()->json($data);
    }

    public function jumlahAnggaranPenyusunan(Request $request)
    {
        $skpd = $request->skpd;
        $kdgiat = $request->kdgiat;
        $kdrek = $request->kdrek;
        $no_spp = $request->no_spp;
        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $skpd, 'status' => 1])->orderBy('tgl_dpa', 'DESC')->first();
        $no_bukti1 = DB::select(DB::raw("SELECT no_tagih from trhspp where kd_skpd='$skpd'"));
        $no_bukti = json_decode(json_encode($no_bukti1), true);

        $query1 = DB::table('trdspp as a')->select(DB::raw("SUM(a.nilai) as nilai"))->join('trhspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'a.kd_skpd' => $skpd])->where('a.no_spp', '<>', $no_spp)->whereIn('b.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('b.sp2d_batal', '<>', '1')
                ->orWhereNull('b.sp2d_batal');
        });

        $query2 = DB::table('trdtagih as t')->select(DB::raw("SUM(nilai) as nilai"))->join('trhtagih as u', function ($join) {
            $join->on('t.no_bukti', '=', 'u.no_bukti');
            $join->on('t.kd_skpd', '=', 'u.kd_skpd');
        })->where(['t.kd_sub_kegiatan' => $kdgiat, 't.kd_rek' => $kdrek, 'u.kd_skpd' => $skpd])->whereNotIn('u.no_bukti', $no_bukti)->unionAll($query1);

        $query3 = DB::table('trdtransout as a')->select(DB::raw("SUM(a.nilai) as nilai"))->join('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'a.kd_skpd' => $skpd])->whereIn('b.jns_spp', ['1', '2'])->unionAll($query2);

        $query4 = DB::table('trdtransout as a')->select(DB::raw("SUM(a.nilai) as nilai"))->join('trhtransout as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'a.kd_skpd' => $skpd])->whereIn('b.jns_spp', ['4', '6'])->whereIn('panjar', ['3'])->unionAll($query3);

        $query5 = DB::table('trdtransout_cmsbank as a')->select(DB::raw("SUM(a.nilai) as nilai"))->join('trhtransout_cmsbank as b', function ($join) {
            $join->on('a.no_voucher', '=', 'b.no_voucher');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'a.kd_skpd' => $skpd, 'b.status_validasi' => '0'])->unionAll($query4);

        $result = DB::table(DB::raw("({$query5->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as rektotal_spp_lalu"))
            ->mergeBindings($query5)
            ->first();

        $rektotal = DB::table('trdrka')->select(DB::raw("SUM(nilai) as rektotal"))->where(['kd_rek6' => $kdrek, 'kd_sub_kegiatan' => $kdgiat, 'jns_ang' => $status_anggaran->jns_ang, 'kd_skpd' => $skpd])->first();

        return response()->json([
            'rektotal' => $rektotal->rektotal,
            'rektotal_lalu' => $result->rektotal_spp_lalu
        ]);
    }

    public function totalSpd(Request $request)
    {
        $skpd = $request->skpd;
        $kdgiat = $request->kdgiat;
        $kdrek = $request->kdrek;
        $no_spp = $request->no_spp;
        $tgl_spd = $request->tgl_spd;
        $tgl_spp = $request->tgl_spp;
        $beban = $request->beban;
        $blnawal = $request->blnawal;

        // $sql1 = DB::table('trhspd')->selectRaw('MAX(revisi_ke) as revisi')->whereRaw("LEFT(kd_skpd,17) = LEFT('$skpd',17)")->where('bulan_akhir', '3')->where('tgl_spd', '<=', $tgl_spp)->first();
        // $revisi1 = $sql1->revisi;
        // $sql2 = DB::table('trhspd')->selectRaw('ISNULL(MAX(revisi_ke),0) as revisi')->whereRaw("LEFT(kd_skpd,17) = LEFT('$skpd',17)")->where('bulan_akhir', '6')->where('tgl_spd', '<=', $tgl_spp)->first();
        // $revisi2 = $sql2->revisi;
        // $sql3 = DB::table('trhspd')->selectRaw('ISNULL(MAX(revisi_ke),0) as revisi')->whereRaw("LEFT(kd_skpd,17) = LEFT('$skpd',17)")->where('bulan_akhir', '9')->where('tgl_spd', '<=', $tgl_spp)->first();
        // $revisi3 = $sql3->revisi;
        // $sql4 = DB::table('trhspd')->selectRaw('ISNULL(MAX(revisi_ke),0) as revisi')->whereRaw("LEFT(kd_skpd,17) = LEFT('$skpd',17)")->where('bulan_akhir', '12')->where('tgl_spd', '<=', $tgl_spp)->first();
        // $revisi4 = $sql4->revisi;

        // $spd1 = DB::table('trdspd as a')->select(DB::raw("'TW1' as ket"), DB::raw("ISNULL(SUM(a.nilai),0) as nilai"))->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->where(['a.kd_unit' => $skpd, 'a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'b.status' => '1', 'bulan_akhir' => '3', 'revisi_ke' => $revisi1])->where('tgl_spd', '<=', $tgl_spp)->where('bulan_awal', '<=', DB::raw("MONTH('$tgl_spp')"));
        // $spd2 = DB::table('trdspd as a')->select(DB::raw("'TW2' as ket"), DB::raw("ISNULL(SUM(a.nilai),0) as nilai"))->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->where(['a.kd_unit' => $skpd, 'a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'b.status' => '1', 'bulan_akhir' => '6', 'revisi_ke' => $revisi2])->where('tgl_spd', '<=', $tgl_spp)->where('bulan_awal', '<=', DB::raw("MONTH('$tgl_spp')"))->unionAll($spd1);
        // $spd3 = DB::table('trdspd as a')->select(DB::raw("'TW3' as ket"), DB::raw("ISNULL(SUM(a.nilai),0) as nilai"))->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->where(['a.kd_unit' => $skpd, 'a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'b.status' => '1', 'bulan_akhir' => '9', 'revisi_ke' => $revisi3])->where('tgl_spd', '<=', $tgl_spp)->where('bulan_awal', '<=', DB::raw("MONTH('$tgl_spp')"))->unionAll($spd2);
        // $spd4 = DB::table('trdspd as a')->select(DB::raw("'TW3' as ket"), DB::raw("ISNULL(SUM(a.nilai),0) as nilai"))->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->where(['a.kd_unit' => $skpd, 'a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'b.status' => '1', 'bulan_akhir' => '12', 'revisi_ke' => $revisi4])->where('tgl_spd', '<=', $tgl_spp)->where('bulan_awal', '<=', DB::raw("MONTH('$tgl_spp')"))->unionAll($spd3);

        // $result = DB::table(DB::raw("({$spd4->toSql()}) AS sub"))
        //     ->select(DB::raw("SUM(nilai) as total_spd"))
        //     ->mergeBindings($spd4)
        //     ->first();

        //spd triwulan prov awal
        $revisi1 = collect(DB::select("SELECT max(revisi_ke) as revisi from trhspd where left(kd_skpd,22)=left(?,22) and bulan_akhir='3' and tgl_spd<=?", [$skpd, $tgl_spp]))->first()->revisi;

        $revisi2 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where left(kd_skpd,22)=left(?,22) and bulan_akhir='6' and tgl_spd<=?", [$skpd, $tgl_spp]))->first()->revisi;

        $revisi3 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where left(kd_skpd,22)=left(?,22) and bulan_akhir='9' and tgl_spd<=?", [$skpd, $tgl_spp]))->first()->revisi;

        $revisi4 = collect(DB::select("SELECT isnull(max(revisi_ke),0) as revisi from trhspd where left(kd_skpd,22)=left(?,22) and bulan_akhir='12' and tgl_spd<=?", [$skpd, $tgl_spp]))->first()->revisi;

        $result = collect(DB::select("SELECT sum(nilai)as total_spd from (
                    SELECT
                    'TW1' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    a.kd_unit = ?
                    AND a.kd_sub_kegiatan = ?
                    AND a.kd_rek6 = ?
                    AND b.status = '1'
                    and bulan_akhir='3'
                    and revisi_ke=?
                    and tgl_spd<=?
                    and bulan_awal <= month(?)
                    UNION ALL
                    SELECT
                    'TW2' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    a.kd_unit = ?
                    AND a.kd_sub_kegiatan = ?
                    AND a.kd_rek6 = ?
                    AND b.status = '1'
                    and bulan_akhir='6'
                    and revisi_ke=?
                    and tgl_spd<=?
                    and bulan_awal <= month(?)
                    UNION ALL
                    SELECT
                    'TW3' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    a.kd_unit = ?
                    AND a.kd_sub_kegiatan = ?
                    AND a.kd_rek6 = ?
                    AND b.status = '1'
                    and bulan_akhir='9'
                    and revisi_ke=?
                    and tgl_spd<=?
                    and bulan_awal <= month(?)
                    UNION ALL
                    SELECT
                    'TW4' ket,isnull(SUM(a.nilai),0) AS nilai
                    FROM
                    trdspd a
                    JOIN trhspd b ON a.no_spd = b.no_spd
                    WHERE
                    a.kd_unit = ?
                    AND a.kd_sub_kegiatan = ?
                    AND a.kd_rek6 = ?
                    AND b.status = '1'
                    and bulan_akhir='12'
                    and revisi_ke=?
                    and tgl_spd<=?
                    and bulan_awal <= month(?)
                    )spd", [$skpd, $kdgiat, $kdrek, $revisi1, $tgl_spp, $tgl_spp, $skpd, $kdgiat, $kdrek, $revisi2, $tgl_spp, $tgl_spp, $skpd, $kdgiat, $kdrek, $revisi3, $tgl_spp, $tgl_spp, $skpd, $kdgiat, $kdrek, $revisi4, $tgl_spp, $tgl_spp]))->first();
        //end spd triwulan prov

        //spd perbulan mulai sanggau calvin
        // if ($beban == 4){
        //     $result = collect(DB::select("SELECT SUM(nilai) AS total_spd FROM (
        //         SELECT kd_skpd, kd_sub_kegiatan, kd_rek6, bulan_awal, bulan_akhir, MAX(tglupdate) AS latest_spd FROM trhspd
        //         JOIN trdspd ON trhspd.no_spd = trdspd.no_spd
        //         WHERE kd_skpd = ? AND kd_sub_kegiatan = ? AND kd_rek6 = ?
        //         AND bulan_awal <= ? + 1 AND bulan_akhir <= ? + 1
        //         GROUP BY kd_skpd, kd_sub_kegiatan, kd_rek6, bulan_awal, bulan_akhir
        //     ) spd_final
        //     JOIN (
        //         SELECT kd_skpd, kd_rek6, kd_sub_kegiatan, bulan_awal, bulan_akhir, tglupdate, nilai
        //         FROM trhspd JOIN trdspd ON trhspd.no_spd = trdspd.no_spd
        //         WHERE kd_skpd = ? AND kd_sub_kegiatan = ? AND kd_rek6 = ?
        //         AND bulan_awal <= ? + 1 AND bulan_akhir <= ? + 1
        //     ) spd
        //     ON spd_final.kd_skpd = spd.kd_skpd AND spd_final.kd_sub_kegiatan = spd.kd_sub_kegiatan
        //     AND spd_final.kd_rek6 = spd.kd_rek6
        //     AND spd_final.bulan_awal = spd.bulan_awal AND spd_final.bulan_akhir = spd.bulan_akhir
        //     AND spd_final.latest_spd = spd.tglupdate", [$skpd, $kdgiat, $kdrek, $blnawal, $blnawal, $skpd, $kdgiat, $kdrek, $blnawal, $blnawal]))->first();
        // } else {
        //     $result = collect(DB::select("SELECT SUM(nilai) AS total_spd FROM (
        //         SELECT kd_skpd, kd_sub_kegiatan, kd_rek6, bulan_awal, bulan_akhir, MAX(tglupdate) AS latest_spd FROM trhspd
        //         JOIN trdspd ON trhspd.no_spd = trdspd.no_spd
        //         WHERE kd_skpd = ? AND kd_sub_kegiatan = ? AND kd_rek6 = ?
        //         AND bulan_awal <= ? + 1 AND bulan_akhir <= ?
        //         GROUP BY kd_skpd, kd_sub_kegiatan, kd_rek6, bulan_awal, bulan_akhir
        //     ) spd_final
        //     JOIN (
        //         SELECT kd_skpd, kd_rek6, kd_sub_kegiatan, bulan_awal, bulan_akhir, tglupdate, nilai
        //         FROM trhspd JOIN trdspd ON trhspd.no_spd = trdspd.no_spd
        //         WHERE kd_skpd = ? AND kd_sub_kegiatan = ? AND kd_rek6 = ?
        //         AND bulan_awal <= ? + 1 AND bulan_akhir <= ?
        //     ) spd
        //     ON spd_final.kd_skpd = spd.kd_skpd AND spd_final.kd_sub_kegiatan = spd.kd_sub_kegiatan
        //     AND spd_final.kd_rek6 = spd.kd_rek6
        //     AND spd_final.bulan_awal = spd.bulan_awal AND spd_final.bulan_akhir = spd.bulan_akhir
        //     AND spd_final.latest_spd = spd.tglupdate", [$skpd, $kdgiat, $kdrek, $blnawal, $blnawal, $skpd, $kdgiat, $kdrek, $blnawal, $blnawal]))->first();
        // }
        //end spd perbulan

        return response()->json($result);
    }

    public function totalAngkas(Request $request)
    {
        $skpd = $request->skpd;
        $kd_skpd = Auth::user()->kd_skpd;
        $kdgiat = $request->kdgiat;
        $kdrek = $request->kdrek;
        $no_spp = $request->no_spp;
        $nomor_spd = $request->nomor_spd;
        $tgl_spd = $request->tgl_spd;
        $tgl_spp = $request->tgl_spp;
        $bulan = date('m', strtotime($tgl_spp));
        $beban = $request->beban;
        $status_ang = $request->status_ang;
        $sts_angkas = $request->status_angkas;

        if ($sts_angkas == 'murni') {
            $field_angkas = 'nilai_susun';
        } else if ($sts_angkas == 'murni_geser1') {
            $field_angkas = 'nilai_susun1';
        } else if ($sts_angkas == 'murni_geser2') {
            $field_angkas = 'nilai_susun2';
        } else if ($sts_angkas == 'murni_geser3') {
            $field_angkas = 'nilai_susun3';
        } else if ($sts_angkas == 'murni_geser4') {
            $field_angkas = 'nilai_susun4';
        } else if ($sts_angkas == 'murni_geser5') {
            $field_angkas = 'nilai_susun5';
        } else if ($sts_angkas == 'sempurna1') {
            $field_angkas = 'nilai_sempurna';
        } else if ($sts_angkas == 'sempurna1_geser1') {
            $field_angkas = 'nilai_sempurna11';
        } else if ($sts_angkas == 'sempurna1_geser2') {
            $field_angkas = 'nilai_sempurna12';
        } else if ($sts_angkas == 'sempurna1_geser3') {
            $field_angkas = 'nilai_sempurna13';
        } else if ($sts_angkas == 'sempurna1_geser4') {
            $field_angkas = 'nilai_sempurna14';
        } else if ($sts_angkas == 'sempurna1_geser5') {
            $field_angkas = 'nilai_sempurna15';
        } else if ($sts_angkas == 'sempurna2') {
            $field_angkas = 'nilai_sempurna2';
        } else if ($sts_angkas == 'sempurna2_geser1') {
            $field_angkas = 'nilai_sempurna21';
        } else if ($sts_angkas == 'sempurna2_geser2') {
            $field_angkas = 'nilai_sempurna22';
        } else if ($sts_angkas == 'sempurna2_geser3') {
            $field_angkas = 'nilai_sempurna23';
        } else if ($sts_angkas == 'sempurna2_geser4') {
            $field_angkas = 'nilai_sempurna24';
        } else if ($sts_angkas == 'sempurna2_geser5') {
            $field_angkas = 'nilai_sempurna25';
        } else if ($sts_angkas == 'sempurna3') {
            $field_angkas = 'nilai_sempurna3';
        } else if ($sts_angkas == 'sempurna3_geser1') {
            $field_angkas = 'nilai_sempurna31';
        } else if ($sts_angkas == 'sempurna3_geser2') {
            $field_angkas = 'nilai_sempurna32';
        } else if ($sts_angkas == 'sempurna3_geser3') {
            $field_angkas = 'nilai_sempurna33';
        } else if ($sts_angkas == 'sempurna3_geser4') {
            $field_angkas = 'nilai_sempurna34';
        } else if ($sts_angkas == 'sempurna3_geser5') {
            $field_angkas = 'nilai_sempurna35';
        } else if ($sts_angkas == 'sempurna4') {
            $field_angkas = 'nilai_sempurna4';
        } else if ($sts_angkas == 'sempurna4_geser1') {
            $field_angkas = 'nilai_sempurna41';
        } else if ($sts_angkas == 'sempurna4_geser2') {
            $field_angkas = 'nilai_sempurna42';
        } else if ($sts_angkas == 'sempurna4_geser3') {
            $field_angkas = 'nilai_sempurna43';
        } else if ($sts_angkas == 'sempurna4_geser4') {
            $field_angkas = 'nilai_sempurna44';
        } else if ($sts_angkas == 'sempurna4_geser5') {
            $field_angkas = 'nilai_sempurna45';
        } else if ($sts_angkas == 'sempurna5') {
            $field_angkas = 'nilai_sempurna5';
        } else if ($sts_angkas == 'sempurna5_geser1') {
            $field_angkas = 'nilai_sempurna51';
        } else if ($sts_angkas == 'sempurna5_geser2') {
            $field_angkas = 'nilai_sempurna52';
        } else if ($sts_angkas == 'sempurna5_geser3') {
            $field_angkas = 'nilai_sempurna53';
        } else if ($sts_angkas == 'sempurna5_geser4') {
            $field_angkas = 'nilai_sempurna1';
        } else if ($sts_angkas == 'sempurna5_geser5') {
            $field_angkas = 'nilai_sempurna55';
        } else if ($sts_angkas == 'ubah') {
            $field_angkas = 'nilai_ubah';
        } else if ($sts_angkas == 'ubah1') {
            $field_angkas = 'nilai_ubah1';
        } else if ($sts_angkas == 'ubah2') {
            $field_angkas = 'nilai_ubah2';
        } else if ($sts_angkas == 'ubah3') {
            $field_angkas = 'nilai_ubah3';
        } else if ($sts_angkas == 'ubah4') {
            $field_angkas = 'nilai_ubah4';
        } else {
            $field_angkas = 'nilai_ubah5';
        }

        $status_anggaran = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => 1])->orderBy('tgl_dpa', 'DESC')->first();

        $hasil = DB::table('trhspd')->select(DB::raw("COUNT(*) as spd"))->whereRaw("LEFT(kd_skpd,22) = LEFT('$skpd',22)")->groupBy('bulan_awal', 'bulan_akhir')->first();

        // $spd = DB::table('trhspd')->select('bulan_awal','bulan_akhir')->where(['no_spd' => $nomor_spd])->first();

        // $bulanspd = $spd->bulan_akhir;

        if ($beban == '4') {
            $bulan1 = $bulan  + 1;
        } else {
            $bulan1 = $bulan;
        }
        $data = DB::table('trdskpd_ro as a')->select('a.kd_sub_kegiatan', 'kd_rek6', DB::raw("SUM(a.$field_angkas) as nilai"))->join('trskpd as b', function ($join) {
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
        })->where(['a.kd_skpd' => $skpd, 'a.kd_sub_kegiatan' => $kdgiat, 'a.kd_rek6' => $kdrek, 'jns_ang' => $status_anggaran->jns_ang])->where('bulan', '<=', $bulan1)->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')->first();

        return response()->json($data);
    }

    public function realisasiSpd(Request $request)
    {
        $skpd = $request->skpd;
        $kdgiat = $request->kdgiat;
        $kdrek = $request->kdrek;
        $sql = DB::table('trhspp')->select('no_tagih')->where('kd_skpd', $skpd)->get();
        $no_bukti = json_decode(json_encode($sql), true);
        //$realisasi1 = DB::table('tb_transaksi')->selectRaw("SUM(ISNULL(nilai,0)) as nilai")->where(['kd_sub_kegiatan' => $kdgiat, 'kd_skpd' => $skpd, 'kd_rek6' => $kdrek]);

        $realisasi2 = DB::table('trdtransout as c')->leftJoin('trhtransout as d', function ($join) {
            $join->on('c.no_bukti', '=', 'd.no_bukti');
            $join->on('c.kd_skpd', '=', 'd.kd_skpd');
        })->selectRaw("SUM(ISNULL(c.nilai,0)) as nilai")->where(['c.kd_sub_kegiatan' => $kdgiat, 'd.kd_skpd' => $skpd, 'c.kd_rek6' => $kdrek])->whereIn('d.jns_spp', ['1']);

        $realisasi3 = DB::table('trdtransout_cmsbank as c')->leftJoin('trhtransout_cmsbank as d', function ($join) {
            $join->on('c.no_voucher', '=', 'd.no_voucher');
            $join->on('c.kd_skpd', '=', 'd.kd_skpd');
        })->selectRaw("SUM(ISNULL(c.nilai,0)) as nilai")->where(['c.kd_sub_kegiatan' => $kdgiat, 'd.kd_skpd' => $skpd, 'c.kd_rek6' => $kdrek])->whereIn('d.jns_spp', ['1'])->where(function ($query) {
            $query->where('d.status_validasi', '=', '0')
                ->orWhereNull('d.status_validasi');
        })->unionAll($realisasi2);

        $realisasi4 = DB::table('trdspp as x')->join('trhspp as y', function ($join) {
            $join->on('x.no_spp', '=', 'y.no_spp');
            $join->on('x.kd_skpd', '=', 'y.kd_skpd');
        })->selectRaw("SUM(ISNULL(x.nilai,0)) as nilai")->where(['x.kd_sub_kegiatan' => $kdgiat, 'x.kd_skpd' => $skpd, 'x.kd_rek6' => $kdrek])->whereIn('y.jns_spp', ['3', '4', '5', '6'])->where(function ($query) {
            $query->where('sp2d_batal', '=', '0')
                ->orWhereNull('sp2d_batal');
        })->unionAll($realisasi3);

        $realisasi5 = DB::table('trdtagih as t')->selectRaw("SUM(ISNULL(t.nilai,0)) as nilai")->join('trhtagih as u', function ($join) {
            $join->on('t.no_bukti', '=', 'u.no_bukti');
            $join->on('t.kd_skpd', '=', 'u.kd_skpd');
        })->where(['t.kd_sub_kegiatan' => $kdgiat, 't.kd_rek' => $kdrek, 'u.kd_skpd' => $skpd])->whereNotIn('u.no_bukti', $no_bukti)->unionAll($realisasi4);

        // $sql = $realisasi5->toSql();
        // dd($sql);
        // return;
        $result = DB::table(DB::raw("({$realisasi5->toSql()}) AS sub"))
            ->select(DB::raw("SUM(nilai) as total"))
            ->mergeBindings($realisasi5)
            ->first();
        return response()->json($result);
    }

    public function sumberDana(Request $request)
    {
        $kode               = $request->skpd;
        $giat               = $request->kdgiat;
        $rek                = $request->kdrek;
        $status             = DB::table('trhrka')->where(['kd_skpd' => $kode, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
        $status_anggaran    = $status->jns_ang;

        $data = DB::select(DB::raw("SELECT sumber_dana,nilai, isnull( tagihlalu, 0 ) + isnull( tampungan, 0 ) + isnull( spplalu, 0 ) + isnull( upgulalucms, 0 ) + isnull( upgulalu, 0 ) AS lalu FROM (SELECT sumber1 AS sumber_dana, isnull( nsumber1, 0 ) AS nilai,
            (
            SELECT SUM
                ( nilai ) AS nilai
            FROM
                trdtagih t
                INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti
                AND t.kd_skpd= u.kd_skpd
            WHERE
                t.kd_sub_kegiatan = '$giat'
                AND u.kd_skpd = '$kode'
                AND t.kd_rek = '$rek'
                AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd = '$kode' )
                AND sumber = z.sumber1
            ) AS tagihlalu,
            (
            SELECT SUM
                ( nilai ) AS nilai
            FROM
                tb_transaksi
            WHERE
                kd_sub_kegiatan = '$giat'
                AND kd_skpd = '$kode'
                AND kd_rek6 = '$rek'
                AND sumber = z.sumber1
            ) AS tampungan,
            (
            SELECT SUM
                ( b.nilai )
            FROM
                trhspp a
                INNER JOIN trdspp b ON a.no_spp= b.no_spp
                AND a.kd_skpd= b.kd_skpd
            WHERE
                b.kd_skpd= '$kode'
                AND b.kd_Sub_kegiatan= '$giat'
                AND b.kd_rek6= '$rek'
                AND sumber = sumber1
                AND ( sp2d_batal <> '1' OR sp2d_batal IS NULL )
                AND jns_spp NOT IN ( '1', '2' )
            ) AS spplalu,
            (
            SELECT SUM
                ( g.nilai )
            FROM
                trhtransout_cmsbank f
                INNER JOIN trdtransout_cmsbank g ON f.no_voucher= g.no_voucher
                AND f.kd_skpd= g.kd_skpd
            WHERE
                g.kd_skpd = '$kode'
                AND g.kd_sub_kegiatan= '$giat'
                AND g.kd_rek6= '$rek'
                AND f.jns_spp IN ( '1' )
                AND ( f.status_validasi= '0' OR f.status_validasi IS NULL )
                AND sumber = z.sumber1
            ) upgulalucms,
            (
            SELECT SUM
                ( g.nilai )
            FROM
                trhtransout f
                INNER JOIN trdtransout g ON f.no_bukti= g.no_bukti
                AND f.kd_skpd= g.kd_skpd
            WHERE
                g.kd_skpd = '$kode'
                AND g.kd_sub_kegiatan= '$giat'
                AND g.kd_rek6= '$rek'
                AND f.jns_spp IN ( '1' )
                AND sumber = z.sumber1
            ) upgulalu
            FROM
                trdrka z
            WHERE
                z.kd_skpd= '$kode'
                AND z.kd_sub_kegiatan= '$giat'
                AND jns_ang = '$status_anggaran'
                AND z.kd_rek6= '$rek' UNION ALL
            SELECT
                sumber2 AS sumber_dana,
                isnull( nsumber2, 0 ) AS nilai,
                (
                SELECT SUM
                    ( nilai ) AS nilai
                FROM
                    trdtagih t
                    INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti
                    AND t.kd_skpd= u.kd_skpd
                WHERE
                    t.kd_sub_kegiatan = '$giat'
                    AND u.kd_skpd = '$kode'
                    AND t.kd_rek = '$rek'
                    AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd = '$kode' )
                    AND sumber = z.sumber2
                ) AS tagihlalu,
                (
                SELECT SUM
                    ( nilai ) AS nilai
                FROM
                    tb_transaksi
                WHERE
                    kd_sub_kegiatan = '$giat'
                    AND kd_skpd = '$kode'
                    AND kd_rek6 = '$rek'
                    AND sumber = z.sumber2
                ) AS tampungan,
                (
                SELECT SUM
                    ( u.nilai ) AS nilai
                FROM
                    trhspp t
                    INNER JOIN trdspp u ON t.no_spp= u.no_spp
                    AND t.kd_skpd= u.kd_skpd
                WHERE
                    u.kd_sub_kegiatan = '$giat'
                    AND u.kd_skpd = '$kode'
                    AND u.kd_rek6 = '$rek'
                    AND sumber = z.sumber2
                    AND ( sp2d_batal <> '1' OR sp2d_batal IS NULL )
                    AND jns_spp NOT IN ( '1', '2' )
                ) AS spplalu,
                (
                SELECT SUM
                    ( g.nilai )
                FROM
                    trhtransout_cmsbank f
                    INNER JOIN trdtransout_cmsbank g ON f.no_voucher= g.no_voucher
                    AND f.kd_skpd= g.kd_skpd
                WHERE
                    g.kd_skpd = '$kode'
                    AND g.kd_sub_kegiatan= '$giat'
                    AND g.kd_rek6= '$rek'
                    AND f.jns_spp IN ( '1' )
                    AND ( f.status_validasi= '0' OR f.status_validasi IS NULL )
                    AND sumber = z.sumber2
                ) upgulalucms,
                (
                SELECT SUM
                    ( g.nilai )
                FROM
                    trhtransout f
                    INNER JOIN trdtransout g ON f.no_bukti= g.no_bukti
                    AND f.kd_skpd= g.kd_skpd
                WHERE
                    g.kd_skpd = '$kode'
                    AND g.kd_sub_kegiatan= '$giat'
                    AND g.kd_rek6= '$rek'
                    AND f.jns_spp IN ( '1' )
                    AND sumber = z.sumber2
                ) upgulalu
            FROM
                trdrka z
            WHERE
                z.kd_sub_kegiatan= '$giat'
                AND z.kd_rek6= '$rek'
                AND jns_ang = '$status_anggaran'
                AND z.kd_skpd= '$kode' UNION ALL
            SELECT
                sumber3 AS sumber_dana,
                isnull( nsumber3, 0 ) AS nilai,
                (
                SELECT SUM
                    ( nilai ) AS nilai
                FROM
                    trdtagih t
                    INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti
                    AND t.kd_skpd= u.kd_skpd
                WHERE
                    t.kd_sub_kegiatan = '$giat'
                    AND u.kd_skpd = '$kode'
                    AND t.kd_rek = '$rek'
                    AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd = '$kode' )
                    AND sumber = sumber3
                ) AS tagihlalu,
                (
                SELECT SUM
                    ( nilai ) AS nilai
                FROM
                    tb_transaksi
                WHERE
                    kd_sub_kegiatan = '$giat'
                    AND kd_skpd = '$kode'
                    AND kd_rek6 = '$rek'
                    AND sumber = a.sumber3
                ) AS tampungan,
                (
                SELECT SUM
                    ( t.nilai ) AS nilai
                FROM
                    trdspp t
                    INNER JOIN trhspp u ON t.no_spp= u.no_spp
                    AND t.kd_skpd= u.kd_skpd
                WHERE
                    t.kd_sub_kegiatan = '$giat'
                    AND t.kd_skpd = '$kode'
                    AND t.kd_rek6 = '$rek'
                    AND sumber = sumber3
                    AND jns_spp NOT IN ( '1', '2' )
                    AND ( sp2d_batal <> '1' OR sp2d_batal IS NULL )
                ) AS spplalu,
                (
                SELECT SUM
                    ( g.nilai )
                FROM
                    trhtransout_cmsbank f
                    INNER JOIN trdtransout_cmsbank g ON f.no_voucher= g.no_voucher
                    AND f.kd_skpd= g.kd_skpd
                WHERE
                    g.kd_skpd = '$kode'
                    AND g.kd_sub_kegiatan= '$giat'
                    AND g.kd_rek6= '$rek'
                    AND f.jns_spp IN ( '1' )
                    AND ( f.status_validasi= '0' OR f.status_validasi IS NULL )
                    AND sumber = sumber3
                ) upgulalucms,
                (
                SELECT SUM
                    ( g.nilai )
                FROM
                    trhtransout f
                    INNER JOIN trdtransout g ON f.no_bukti= g.no_bukti
                    AND f.kd_skpd= g.kd_skpd
                WHERE
                    g.kd_skpd = '$kode'
                    AND g.kd_sub_kegiatan= '$giat'
                    AND g.kd_rek6= '$rek'
                    AND f.jns_spp IN ( '1' )
                    AND sumber = sumber3
                ) upgulalu
            FROM
                trdrka a
            WHERE
                a.kd_sub_kegiatan= '$giat'
                AND a.kd_rek6= '$rek'
                AND jns_ang = '$status_anggaran'
                AND a.kd_skpd= '$kode' UNION ALL
            SELECT
                sumber4 AS sumber_dana,
                isnull( nsumber4, 0 ) AS nilai,
                (
                SELECT SUM
                    ( nilai ) AS nilai
                FROM
                    trdtagih t
                    INNER JOIN trhtagih u ON t.no_bukti= u.no_bukti
                    AND t.kd_skpd= u.kd_skpd
                WHERE
                    t.kd_sub_kegiatan = '$giat'
                    AND u.kd_skpd = '$kode'
                    AND t.kd_rek = '$rek'
                    AND u.no_bukti NOT IN ( SELECT no_tagih FROM trhspp WHERE kd_skpd = '$kode' )
                    AND sumber = sumber4
                ) AS lalu,
                (
                SELECT SUM
                    ( nilai ) AS nilai
                FROM
                    tb_transaksi
                WHERE
                    kd_sub_kegiatan = '$giat'
                    AND kd_skpd = '$kode'
                    AND kd_rek6 = '$rek'
                    AND sumber = a.sumber4
                ) AS tampungan,
                (
                SELECT SUM
                    ( t.nilai ) AS nilai
                FROM
                    trdspp t
                    INNER JOIN trhspp u ON t.no_spp= u.no_spp
                    AND t.kd_skpd= u.kd_skpd
                WHERE
                    t.kd_sub_kegiatan = '$giat'
                    AND t.kd_skpd = '$kode'
                    AND t.kd_rek6 = '$rek'
                    AND jns_spp NOT IN ( '1', '2' )
                    AND sumber = sumber4
                    AND ( sp2d_batal <> '1' OR sp2d_batal IS NULL )
                ) AS lalu,
                (
                SELECT SUM
                    ( g.nilai )
                FROM
                    trhtransout_cmsbank f
                    INNER JOIN trdtransout_cmsbank g ON f.no_voucher= g.no_voucher
                    AND f.kd_skpd= g.kd_skpd
                WHERE
                    g.kd_skpd = '$kode'
                    AND g.kd_sub_kegiatan= '$giat'
                    AND g.kd_rek6= '$rek'
                    AND f.jns_spp IN ( '1' )
                    AND ( f.status_validasi= '0' OR f.status_validasi IS NULL )
                    AND sumber = sumber4
                ) upgulalucms,
                (
                SELECT SUM
                    ( g.nilai )
                FROM
                    trhtransout f
                    INNER JOIN trdtransout g ON f.no_bukti= g.no_bukti
                    AND f.kd_skpd= g.kd_skpd
                WHERE
                    g.kd_skpd = '$kode'
                    AND g.kd_sub_kegiatan= '$giat'
                    AND g.kd_rek6= '$rek'
                    AND f.jns_spp IN ( '1' )
                    AND sumber = sumber4
                ) upgulalu
            FROM
                trdrka a
            WHERE
                a.kd_sub_kegiatan= '$giat'
                AND a.kd_rek6= '$rek'
                AND jns_ang = '$status_anggaran'
                AND a.kd_skpd= '$kode'
            ) z
            WHERE
            z.nilai<>0"));
        return response()->json($data);
    }

    public function cariNoSpp(Request $request)
    {
        $jenis_beban2 = $request->jenis_beban2;
        $kd_skpd = Auth::user()->kd_skpd;
        $nomor = DB::table('trhspp')->select(DB::raw("ISNULL(MAX(urut),0)+1 as nilai"))->where(['kd_skpd' => $kd_skpd])->first();
        $data = str::padLeft($nomor -> nilai, 6, '0');
        return response()->json([
            'nilai' => $data
        ]);
    }

    public function cekSimpan(Request $request)
    {
        $no_spp = $request->no_spp;
        $data = DB::table('trhspp')->where('no_spp', $no_spp)->count();
        return response()->json($data);
    }

    public function simpanSppLs(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;

        try {
            DB::beginTransaction();

            DB::raw('LOCK TABLES trhspp WRITE');
            DB::raw('LOCK TABLES trdspp WRITE');

            $nomorSppBaru = nomorSppBaru("spp", $data['no_spp'], $data['tgl_spp'], $data['beban']);
            $cek = DB::table('trhspp')
            ->where(['no_spp' => $nomorSppBaru, 'kd_skpd' => $data['kd_skpd']])
            ->count();
            if ($cek > 0 ) {
                return response()->json([
                    'message' => '2'
                ]);
            }
            DB::table('trhspp')
                ->insert([
                    'no_spp' => $nomorSppBaru,
                    'kd_skpd' => $data['kd_skpd'],
                    'keperluan' => $data['keperluan'],
                    'bulan' => $data['bulan'],
                    'no_spd' => $data['nomor_spd'],
                    'jns_spp' => $data['beban'],
                    'jns_beban' => $data['jenis'],
                    'bank' => $data['bank'],
                    'nmrekan' => $data['rekanan'],
                    'no_rek' => $data['rekening'],
                    'npwp' => $data['npwp'],
                    'nm_skpd' => $data['nm_skpd'],
                    'tgl_spp' => $data['tgl_spp'],
                    'status' => '0',
                    'username' => Auth::user()->nama,
                    'last_update' =>  date('Y-m-d H:i:s'),
                    'nilai' => $data['total'],
                    'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                    'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                    'kd_program' => $data['kd_program'],
                    'nm_program' => $data['nm_program'],
                    'pimpinan' => $data['pimpinan'],
                    'no_tagih' => $data['no_penagihan'],
                    'tgl_tagih' => $data['tgl_penagihan'],
                    'sts_tagih' => $data['sts_tagih'],
                    'alamat' => $data['alamat'],
                    'kontrak' => $data['no_kontrak'],
                    'tgl_mulai' => $data['tgl_awal'],
                    'tgl_akhir' => $data['tgl_akhir'],
                    'urut' => $data['no_spp'],
                    'urut' => $data['no_spp'],
                    'penerima' => $data['nm_penerima'],
                ]);
            // set status trhtagih
            if ($data['no_penagihan']) {
                DB::table('trhtagih')
                    ->where(['no_bukti' => $data['no_penagihan'], 'kd_skpd' => $kd_skpd])
                    ->update([
                        'sts_tagih' => '1',
                    ]);
            }

            DB::table('trdspp')->where(['no_spp' => $nomorSppBaru, 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_rekening'])) {
                DB::table('trdspp')->insert(array_map(function ($value) use ($data, $kd_skpd, $nomorSppBaru) {
                    return [
                        'no_spp' => $nomorSppBaru,
                        'kd_rek6' => $value['kd_rek6'],
                        'nm_rek6' => $value['nm_rek6'],
                        'nilai' => $value['nilai'],
                        'kd_skpd' => $kd_skpd,
                        'nm_skpd' => nama_skpd($kd_skpd),
                        'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                        'no_spd' => $data['nomor_spd'],
                        'kd_bidang' => $data['bidang'],
                        'sumber' => $value['sumber'],
                    ];
                }, $data['rincian_rekening']));
            }

            DB::update("UPDATE a
                                SET a.nm_sub_kegiatan=b.nm_sub_kegiatan
                                FROM trdspp  a
                                INNER JOIN trskpd b
                                ON a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_skpd=b.kd_skpd
                                WHERE no_spp=?", [$nomorSppBaru]);

            DB::raw("UNLOCK TABLES");

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

    // public function simpanDetailSppLs(Request $request)
    // {
    //     $data = $request->data;
    //     $kd_skpd = Auth::user()->kd_skpd;
    //     $nama = Auth::user()->nama;
    //     DB::beginTransaction();
    //     try {
    //         DB::table('trdspp')->where(['no_spp' => $data['no_spp'], 'kd_skpd' => $kd_skpd])->delete();
    //         if (isset($data['rincian_rekening'])) {
    //             DB::table('trdspp')->insert(array_map(function ($value) use ($data, $kd_skpd) {
    //                 return [
    //                     'no_spp' => $data['no_spp'],
    //                     'kd_rek6' => $value['kd_rek6'],
    //                     'nm_rek6' => $value['nm_rek6'],
    //                     'nilai' => $value['nilai'],
    //                     'kd_skpd' => $kd_skpd,
    //                     'kd_sub_kegiatan' => $value['kd_sub_kegiatan'],
    //                     'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
    //                     'no_spd' => $data['nomor_spd'],
    //                     'kd_bidang' => $data['bidang'],
    //                     'sumber' => $value['sumber'],
    //                     'volume' => $value['volume_output'],
    //                     'satuan' => $value['satuan_output'],
    //                 ];
    //             }, $data['rincian_rekening']));
    //         }
    //         DB::table('tb_transaksi')->where(['kd_skpd' => $kd_skpd, 'no_transaksi' => $data['no_spp'], 'username' => $nama])->delete();
    //         DB::table('trhspp')->where(['kd_skpd' => $kd_skpd, 'no_spp' => $data['no_spp']])->whereNull('username')->update([
    //             'username'  => $nama,
    //             'last_update' => date('Y-m-d H:i:s')
    //         ]);
    //         DB::commit();
    //         return response()->json([
    //             'message' => '1'
    //         ]);
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => '0'
    //         ]);
    //     }
    // }

    public function tampilSppLs($no_spp)
    {
        $no_spp = Crypt::decryptString($no_spp);
        $kd_skpd = Auth::user()->kd_skpd;
        $data_sppls = DB::table('trhspp as a')->join('trdspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where('a.no_spp', $no_spp)->select('a.*')->first();

        $data = [
            'sppls' => $data_sppls,
            'tgl_spd' => DB::table('trhspd')->select('tgl_spd')->where('no_spd', $data_sppls->no_spd)->first(),
            'bank' => DB::table('ms_bank')->select('nama')->where('kode', $data_sppls->bank)->first(),
            'detail_spp' => DB::table('trdspp as a')->select('a.*', 'c.nm_sumber_dana1')->join('trhspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->join('sumber_dana as c', 'a.sumber', '=', 'c.kd_sumber_dana1')->where('a.no_spp', $no_spp)->get(),
        ];
        return view('penatausahaan.pengeluaran.spp_ls.show')->with($data);
    }

    public function editSppLs($no_spp)
    {
        $no_spp = Crypt::decryptString($no_spp);
        $kd_skpd = Auth::user()->kd_skpd;
        $perusahaan1 = DB::table('ms_perusahaan')->select('nama as nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEFT(kd_skpd,22) = ?', [$kd_skpd])->groupBy('nama', 'pimpinan', 'npwp', 'alamat');
        $perusahaan2 = DB::table('trhspp')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEN(nmrekan)>1')->where('kd_skpd', $kd_skpd)->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($perusahaan1);
        $perusahaan3 = DB::table('trhtrmpot_cmsbank')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEN(nmrekan)>1')->where('kd_skpd', $kd_skpd)->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($perusahaan2);
        $perusahaan4 = DB::table('trhtrmpot')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEN(nmrekan)>1')->where('kd_skpd', $kd_skpd)->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($perusahaan3);
        $result = DB::table(DB::raw("({$perusahaan4->toSql()}) AS sub"))
            ->select("nmrekan", "pimpinan", "npwp", "alamat")
            ->mergeBindings($perusahaan4)
            ->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->orderBy('nmrekan', 'ASC')
            ->orderBy('pimpinan', 'ASC')
            ->orderBy('npwp', 'ASC')
            ->orderBy('alamat', 'ASC')
            ->get();
        $data1 = DB::select(DB::raw("SELECT isnull(no_tagih,'') no_tagih from trhspp where kd_skpd='$kd_skpd' and (sp2d_batal is null OR sp2d_batal<>'1') GROUP BY no_tagih"));
        $data2 = json_decode(json_encode($data1), true);
        $data = [
            'daftar_rekanan' => $result,
            'sppls' => DB::table('trhspp as a')->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->where('a.no_spp', $no_spp)->select('a.*')->first(),
            'daftar_penagihan' => DB::table('trhtagih as a')->select('a.kd_skpd', 'a.no_bukti', 'tgl_bukti', 'a.ket', 'a.kontrak', 'kd_sub_kegiatan', DB::raw('SUM(b.nilai) as total'))->join('trdtagih as b', 'a.no_bukti', '=', 'b.no_bukti')->where('a.kd_skpd', $kd_skpd)->where('a.jns_trs', '1')->whereNotIn('a.no_bukti', $data2)->groupBy('a.kd_skpd', 'a.no_bukti', 'tgl_bukti', 'a.ket', 'a.kontrak', 'kd_sub_kegiatan')->orderBy('a.no_bukti')->get(),
            'data_tgl' => DB::table('trhspp')->selectRaw('MAX(tgl_spp) as tgl_spp')->where('kd_skpd', $kd_skpd)->where(function ($query) {
                $query->where('sp2d_batal', '=', '0')
                    ->orWhereNull('sp2d_batal');
            })->first(),
            'daftar_penerima' => DB::table('ms_rekening_bank_online')->select('rekening', 'nm_rekening', 'npwp', 'nmrekan', 'pimpinan', 'alamat')->where('kd_skpd', $kd_skpd)->orderBy('rekening')->get(),
            'daftar_bank' => DB::table('ms_bank')->select('kode', 'nama')->orderBy('kode')->get(),
            'detail_spp' => DB::table('trdspp as a')->select('a.*', 'c.nm_sumber_dana1')->join('trhspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->join('sumber_dana as c', 'a.sumber', '=', 'c.kd_sumber_dana1')->where('a.no_spp', $no_spp)->get(),
        ];

        return view('penatausahaan.pengeluaran.spp_ls.edit')->with($data);
    }

    public function hapusSppLs(Request $request)
    {
        $no_spp = $request->no_spp;
        $kd_skpd = Auth::user()->kd_skpd;

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

            DB::table('trhspp')
                ->where([
                    'no_spp' => $no_spp,
                    'kd_skpd' => $kd_skpd
                ])
                ->delete();
            DB::table('trdspp')
                ->where([
                    'no_spp' => $no_spp,
                    'kd_skpd' => $kd_skpd
                ])
                ->delete();
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function cariPenagihanSpp(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('trdtagih as a')->join('sumber_dana as b', 'a.sumber', '=', 'b.kd_sumber_dana1')->where(['a.no_bukti' => $no_bukti, 'a.kd_skpd' => $kd_skpd])->get();
        return response()->json($data);
    }

    public function simpanEditSppLs(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;
        $nama = Auth::user()->nama;
        DB::beginTransaction();
        try {
            // input trhspp
            DB::table('trhspp')->where(['no_spp' => $data['no_spp'], 'kd_skpd' => $kd_skpd])->update([
                'no_spp' => $data['no_spp'],
                'kd_skpd' => $data['kd_skpd'],
                'keperluan' => $data['keperluan'],
                'bulan' => $data['bulan'],
                'no_spd' => $data['nomor_spd'],
                'jns_spp' => $data['beban'],
                'jns_beban' => $data['jenis'],
                'bank' => $data['bank'],
                'nmrekan' => $data['rekanan'],
                'no_rek' => $data['rekening'],
                'npwp' => $data['npwp'],
                'nm_skpd' => $data['nm_skpd'],
                'tgl_spp' => $data['tgl_spp'],
                'status' => '0',
                'username' => Auth::user()->nama,
                'nilai' => $data['total'],
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'nm_sub_kegiatan' => $data['nm_sub_kegiatan'],
                'kd_program' => $data['kd_program'],
                'nm_program' => $data['nm_program'],
                'pimpinan' => $data['pimpinan'],
                'no_tagih' => $data['no_penagihan'],
                'tgl_tagih' => $data['tgl_penagihan'],
                'sts_tagih' => $data['sts_tagih'],
                'alamat' => $data['alamat'],
                'kontrak' => $data['no_kontrak'],
                'lanjut' => $data['lanjut'],
                'tgl_mulai' => $data['tgl_awal'],
                'tgl_akhir' => $data['tgl_akhir'],
                'urut' => $data['no_urut'],
                'penerima' => $data['nm_penerima'],
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
    // cetak pengantar layar
    public function cetakPengantarLayar(Request $request)
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
        $tahun_anggaran = tahun_anggaran();

        $cari_kode = DB::table('ms_skpd')->select('kodepos')->where('kd_skpd', $kd_skpd)->first();
        if ($cari_kode == '') {
            $kodepos = "------";
        } else {
            $kodepos = $cari_kode->kodepos;
        }
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        $cari_pptk = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pptk, 'kode' => 'PPTK', 'kd_skpd' => $kd_skpd])->first();
        $cari_spp = DB::table('trhspp')->select('no_spd', 'tgl_spp')->where('no_spp', $no_spp)->first();
        $cari_spd = DB::table('trhspd')->select('tgl_spd')->where('no_spd', $cari_spp->no_spd)->first();
        $kd_sub_kegiatan = DB::table('trdspp')->select('kd_sub_kegiatan')->where('no_spp', $no_spp)->groupBy('kd_sub_kegiatan')->first();
        $sub_kegiatan = $kd_sub_kegiatan->kd_sub_kegiatan == "" ? "" : $kd_sub_kegiatan->kd_sub_kegiatan;
        if ($beban == '4') {
            $jenis = DB::table('trhspp')->select('jns_beban')->where('no_spp', $no_spp)->first();
            switch ($jenis->jns_beban) {
                case '1': //UP
                    $lcbeban = "Gaji dan Tunjangan";
                    break;
                // case '2': //GU
                //     $lcbeban = "Uang Kespeg";
                //     break;
                // case '3': //TU
                //     $lcbeban = "Uang Makan";
                //     break;
                // case '4': //TU
                //     $lcbeban = "Upah Pungut";
                //     break;
                // case '5': //TU
                //     $lcbeban = "Upah Pungut PBB";
                //     break;
                // case '6': //TU
                //     $lcbeban = "Upah Pungut PBB-KB PKB & BBN-KB ";
                //     break;
                case '7': //TU
                    $lcbeban = "Gaji & Tunjangan";
                    break;
                case '8': //TU
                    $lcbeban = "Tunjangan Transport";
                    break;
                case '9': //TU
                    $lcbeban = "Tunjangan Lainnya";
                    break;
                // case '10': //TU
                //     $lcbeban = "Gaji Anggota DPRD";
                //     break;
                default:
                    $lcbeban = "LS";
            }
            $jumlah_spp = '';
            $cari_rek = DB::table('trdspp')
                ->select('kd_rek6')
                ->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])
                ->orderBy('kd_rek6')
                ->first();
            $rek = substr($cari_rek->kd_rek6, 0, 6);
            if ($rek == '510101') {
                $rek = '510101';
            } else {
                $rek = $cari_rek->kd_rek6;
            }

            if ($rek == '510101' || $rek == '510105') {
                $cari_data = DB::table('trhspp as a')
                    ->join('trdspp as b', function ($join) {
                        $join->on('a.no_spp', '=', 'b.no_spp');
                        $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    })
                    ->join('ms_bidang_urusan as c', DB::raw("SUBSTRING(a.kd_skpd,1,4)"), '=', 'c.kd_bidang_urusan')
                    ->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])
                    ->select('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'a.nmrekan', 'a.jns_beban', 'c.kd_bidang_urusan', 'c.nm_bidang_urusan', 'a.bank', 'no_rek as no_rek', 'a.npwp', 'a.no_spd', DB::raw("SUM(b.nilai) as nilai"), DB::raw("(SELECT SUM(trdspd.nilai) FROM trdspd INNER JOIN trhspd ON trdspd.no_spd = trhspd.no_spd WHERE trhspd.kd_skpd = '$kd_skpd' AND trhspd.tgl_spd <= '$cari_spd->tgl_spd') as spd"), DB::raw("(SELECT SUM(trdspp.nilai) FROM trdspp INNER JOIN trhspp ON trdspp.no_spp = trhspp.no_spp AND trdspp.kd_skpd = trhspp.kd_skpd INNER JOIN trhsp2d ON trhspp.no_spp = trhsp2d.no_spp WHERE trhspp.kd_skpd = '$kd_skpd' AND trhspp.jns_spp = '4' AND trhspp.no_spp != '$no_spp' AND trhsp2d.tgl_sp2d <= '$cari_spp->tgl_spp') as spp"))->groupBy('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'a.nmrekan', 'a.no_rek', 'a.npwp', 'a.jns_beban', 'c.kd_bidang_urusan', 'c.nm_bidang_urusan', 'a.bank', 'a.no_spd')->first();
            } else {
                $cari_data = DB::table('trhspp as a')
                    ->join('trdspp as b', function ($join) {
                        $join->on('a.no_spp', '=', 'b.no_spp');
                        $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    })
                    ->join('ms_bidang_urusan as c', DB::raw("SUBSTRING(a.kd_skpd,1,4)"), '=', 'c.kd_bidang_urusan')
                    ->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])
                    ->whereRaw("b.kd_rek6 IN (SELECT kd_rek6 FROM trdspp WHERE b.kd_rek6=kd_rek6)")
                    ->select('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'a.nmrekan', 'a.jns_beban', 'c.kd_bidang_urusan', 'c.nm_bidang_urusan', 'a.bank', 'no_rek as no_rek', 'a.npwp', 'a.no_spd', DB::raw("SUM(b.nilai) as nilai"), DB::raw("(SELECT SUM(trdspd.nilai) FROM trdspd INNER JOIN trhspd ON trdspd.no_spd = trhspd.no_spd WHERE trhspd.kd_skpd = '$kd_skpd' AND trhspd.tgl_spd <= '$cari_spd->tgl_spd' AND trdspd.kd_rek6 = '$rek') as spd"), DB::raw("(SELECT SUM(trdspp.nilai) FROM trdspp INNER JOIN trhspp ON trdspp.no_spp = trhspp.no_spp AND trdspp.kd_skpd = trhspp.kd_skpd INNER JOIN trhsp2d ON trhspp.no_spp = trhsp2d.no_spp WHERE trhspp.kd_skpd = '$kd_skpd' AND trhspp.jns_spp = '4' AND trhspp.no_spp != '$no_spp' AND trhsp2d.tgl_sp2d <= '$cari_spp->tgl_spp' AND trdspp.kd_rek6 = '$rek') as spp"))
                    ->groupBy('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'a.nmrekan', 'a.no_rek', 'a.npwp', 'a.jns_beban', 'c.kd_bidang_urusan', 'c.nm_bidang_urusan', 'a.bank', 'a.no_spd')->first();
            }

            $jenis = $cari_data->jns_beban;
            $bank = DB::table('ms_bank')->select('nama')->where('kode', $cari_data->bank)->first();

            $unit = substr($kd_skpd, -2);
            if ($unit == '01' || $kd_skpd == '1.20.03.00') {
                $peng = 'Pengguna Anggaran';
            } else {
                $peng = 'Kuasa Pengguna Anggaran';
            }

            $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

            $status = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
            $status_anggaran = $status->jns_ang;
            if ($status_anggaran == 'M') {
                $nogub = $daerah->nogub_susun;
            } else if ($status_anggaran == 'P1') {
                $nogub = $daerah->nogub_p1;
            } else if ($status_anggaran == 'P2') {
                $nogub = $daerah->nogub_p2;
            } else if ($status_anggaran == 'P3') {
                $nogub = $daerah->nogub_p3;
            } else if ($status_anggaran == 'P4') {
                $nogub = $daerah->nogub_p4;
            } else if ($status_anggaran == 'P5') {
                $nogub = $daerah->nogub_p5;
            } else if ($status_anggaran == 'U1') {
                $nogub = $daerah->nogub_perubahan;
            } else if ($status_anggaran == 'U2') {
                $nogub = $daerah->nogub_perubahan2;
            } else if ($status_anggaran == 'U3') {
                $nogub = $daerah->nogub_perubahan3;
            } else if ($status_anggaran == 'U4') {
                $nogub = $daerah->nogub_perubahan4;
            } else {
                $nogub = $daerah->nogub_perubahan5;
            }
            $tanggal = $cari_data->tgl_spp;
        } else if ($beban == '5') {
            $cari_data = DB::table('trhspp as a')->join('ms_bidang_urusan as b', DB::raw("SUBSTRING(a.kd_skpd,1,4)"), '=', 'b.kd_bidang_urusan')->select('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'a.nmrekan', 'a.jns_beban', 'a.no_rek', 'a.npwp', 'b.kd_bidang_urusan', 'b.nm_bidang_urusan', 'a.bank', 'no_rek', 'a.bank', 'a.npwp', 'a.no_spd', 'a.nilai', DB::raw("(SELECT TOP 1 nama from ms_bank WHERE kode = a.bank) as nama_bank_rek"), DB::raw("(SELECT SUM(a.nilai) FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' AND LEFT ( b.kd_skpd, 22 ) = LEFT ( '$kd_skpd', 22 ) AND b.tgl_spd <= '$cari_spd->tgl_spd' AND a.kd_sub_kegiatan= '$sub_kegiatan') as spd"), DB::raw("(SELECT SUM(b.nilai) FROM trdspp b INNER JOIN trhspp a ON b.no_spp = a.no_spp AND b.kd_skpd = a.kd_skpd INNER JOIN trhsp2d c ON a.no_spp = c.no_spp WHERE LEFT(a.kd_skpd,22) = LEFT('$kd_skpd', 22) AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp IN ('1','2','3','6') AND a.no_spp != '$no_spp' AND c.tgl_sp2d <= '$cari_spp->tgl_spp') as spp"))->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])->first();
            $jumlah_spp = '';
            $bank = DB::table('ms_bank')->select('nama')->where('kode', $cari_data->bank)->first();
            $jenis = $cari_data->jns_beban;
            $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

            $status = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
            $status_anggaran = $status->jns_ang;
            if ($status_anggaran == 'M') {
                $nogub = $daerah->nogub_susun;
            } else if ($status_anggaran == 'P1') {
                $nogub = $daerah->nogub_p1;
            } else if ($status_anggaran == 'P2') {
                $nogub = $daerah->nogub_p2;
            } else if ($status_anggaran == 'P3') {
                $nogub = $daerah->nogub_p3;
            } else if ($status_anggaran == 'P4') {
                $nogub = $daerah->nogub_p4;
            } else if ($status_anggaran == 'P5') {
                $nogub = $daerah->nogub_p5;
            } else if ($status_anggaran == 'U1') {
                $nogub = $daerah->nogub_perubahan;
            } else if ($status_anggaran == 'U2') {
                $nogub = $daerah->nogub_perubahan2;
            } else if ($status_anggaran == 'U3') {
                $nogub = $daerah->nogub_perubahan3;
            } else if ($status_anggaran == 'U4') {
                $nogub = $daerah->nogub_perubahan4;
            } else {
                $nogub = $daerah->nogub_perubahan5;
            }

            $unit = substr($kd_skpd, -2);
            if ($unit == '01' || $kd_skpd == '1.20.03.00') {
                $peng = 'Pengguna Anggaran';
            } else {
                $peng = 'Kuasa Pengguna Anggaran';
            }

            $tanggal = $cari_data->tgl_spp;

            // $lcbeban = "LS PIHAK KETIGA LAINNYA";
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Hibah";
                    break;
                case '2': //GU
                    $lcbeban = "Bantuan Sosial";
                    break;
                case '3': //TU
                    $lcbeban = " Bantuan Keuangan";
                    break;
                case '4': //TU
                    $lcbeban = "  Subsidi";
                    break;
                case '5': //TU
                    $lcbeban = " Bagi Hasil";
                    break;
                case '6': //TU
                    $lcbeban = " Belanja Tidak Terduga";
                    break;
                case '7': //TU
                    $lcbeban = " Pihak Ketiga Lainnya";
                    break;
                case '8': //TU
                    $lcbeban = " Pengeluaran Pembiayaan";
                    break;
                case '9': //TU
                    $lcbeban = " Barang yang diserahkan ke masyarakat";
                    break;
            }
        } else if ($beban == '6') {
            $cari_data = DB::table('trhspp as a')->join('ms_bidang_urusan as b', DB::raw("SUBSTRING(a.kd_skpd,1,4)"), '=', 'b.kd_bidang_urusan')->select('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.bulan', 'a.nmrekan', 'a.jns_beban', 'a.no_rek', 'a.npwp', 'b.kd_bidang_urusan', 'b.nm_bidang_urusan', 'a.bank', 'no_rek', 'a.bank', 'a.npwp', 'a.no_spd', 'a.nilai', DB::raw("(SELECT nama FROM ms_bank WHERE kode = a.bank) as nama_bank_rek"), DB::raw("(SELECT SUM(a.nilai) FROM trdspd a INNER JOIN trhspd b ON a.no_spd = b.no_spd WHERE b.jns_beban = '5' AND LEFT ( b.kd_skpd, 22 ) = LEFT ( '$kd_skpd', 22 ) AND b.tgl_spd <='$cari_spd->tgl_spd' AND a.kd_sub_kegiatan= '$sub_kegiatan') as spd"), DB::raw("(SELECT SUM(b.nilai) FROM trdspp b INNER JOIN trhspp a ON b.no_spp= a.no_spp AND b.kd_skpd = a.kd_skpd INNER JOIN trhsp2d c ON a.no_spp = c.no_spp WHERE LEFT ( a.kd_skpd, 22 ) = LEFT ( '$kd_skpd', 22 ) AND b.kd_sub_kegiatan= '$sub_kegiatan' AND a.jns_spp IN ( '1', '2', '3', '6' ) AND a.no_spp != '$no_spp'
		    AND c.tgl_sp2d <= '$cari_spp->tgl_spp') as spp"))->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])->first();

            $bank = DB::table('ms_bank')->select('nama')->where('kode', $cari_data->bank)->first();
            $jenis = $cari_data->jns_beban;
            $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();

            $status = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
            $status_anggaran = $status->jns_ang;
            if ($status_anggaran == 'M') {
                $nogub = $daerah->nogub_susun;
            } else if ($status_anggaran == 'P1') {
                $nogub = $daerah->nogub_p1;
            } else if ($status_anggaran == 'P2') {
                $nogub = $daerah->nogub_p2;
            } else if ($status_anggaran == 'P3') {
                $nogub = $daerah->nogub_p3;
            } else if ($status_anggaran == 'P4') {
                $nogub = $daerah->nogub_p4;
            } else if ($status_anggaran == 'P5') {
                $nogub = $daerah->nogub_p5;
            } else if ($status_anggaran == 'U1') {
                $nogub = $daerah->nogub_perubahan;
            } else if ($status_anggaran == 'U2') {
                $nogub = $daerah->nogub_perubahan2;
            } else if ($status_anggaran == 'U3') {
                $nogub = $daerah->nogub_perubahan3;
            } else if ($status_anggaran == 'U4') {
                $nogub = $daerah->nogub_perubahan4;
            } else {
                $nogub = $daerah->nogub_perubahan5;
            }

            $unit = substr($kd_skpd, -2);
            if ($unit == '01' || $kd_skpd == '1.20.03.00') {
                $peng = 'Pengguna Anggaran';
            } else {
                $peng = 'Kuasa Pengguna Anggaran';
            }

            $tanggal = $cari_data->tgl_spp;

            switch ($cari_data->jns_beban) {
                case '1': //UP
                    $lcbeban = "Rutin (PNS)";
                    break;
                case '2': //GU
                    $lcbeban = "Rutin (Non PNS)";
                    break;
                case '3': //TU
                    $lcbeban = "Pihak Ketiga";
                    break;
                // case '4': //TU
                //     $lcbeban = " Honor Kontrak";
                //     break;
                // case '5': //TU
                //     $lcbeban = " Jasa Pelayanan Kesehatan";
                //     break;
                // case '6': //TU
                //     $lcbeban = " Pihak ketiga";
                //     break;
                // case '7': //TU
                //     $lcbeban = " PNS";
                //     break;
                default:
                    $lcbeban = "LS";
            }

            $jumlah_spp = DB::table('trhspp')->whereRaw("keperluan like '%Tambahan Penghasilan Pegawai%'")->where('no_spp', $no_spp)->count();
        }
        $skpd = DB::table('ms_skpd')
            ->select('nm_skpd')
            ->where(['kd_skpd' => $kd_skpd])
            ->first();
        $header =  DB::table('config_app')
            ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
            ->first();
        $view = view('penatausahaan.pengeluaran.spp_ls.cetak.pengantar', compact('tanpa', 'jenis', 'kd_skpd', 'beban', 'lcbeban', 'no_spp', 'peng', 'cari_data', 'tahun_anggaran', 'cari_bendahara', 'bank', 'daerah', 'tanggal', 'nogub', 'cari_pptk', 'sub_kegiatan', 'jumlah_spp', 'header', 'skpd'));
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

    public function cetakRincianLayar(Request $request)
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
        $tahun_anggaran = tahun_anggaran();

        $skpd = DB::table('ms_skpd')->select('nm_skpd', 'kodepos')->where('kd_skpd', $kd_skpd)->first();
        $nama_skpd = $skpd->nm_skpd;
        $kodepos = $skpd->kodepos == '' ? "--------" : $skpd->kodepos;

        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        $cari_pptk = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pptk, 'kode' => 'PPTK', 'kd_skpd' => $kd_skpd])->first();
        $kd_sub_kegiatan = DB::table('trdspp')->select('kd_sub_kegiatan')->where('no_spp', $no_spp)->groupBy('kd_sub_kegiatan')->first();
        $sub_kegiatan = $kd_sub_kegiatan->kd_sub_kegiatan == "" ? "" : $kd_sub_kegiatan->kd_sub_kegiatan;

        if ($beban == '4') {
            $cari_data = DB::table('trhspp')->select('tgl_spp', 'bulan')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first();
            $tanggal = $cari_data->tgl_spp;
            $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
            $cari_jenis = DB::table('trhspp')->select('jns_beban')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first();
            $jenis = $cari_jenis->jns_beban;
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Gaji dan Tunjangan";
                    break;
                // case '2': //GU
                //     $lcbeban = "Uang Kespeg";
                //     break;
                // case '3': //TU
                //     $lcbeban = "Uang Makan";
                //     break;
                // case '4': //TU
                //     $lcbeban = "Upah Pungut";
                //     break;
                // case '5': //TU
                //     $lcbeban = "Upah Pungut PBB";
                //     break;
                // case '6': //TU
                //     $lcbeban = "Upah Pungut PBB-KB PKB & BBN-KB ";
                //     break;
                case '7': //TU
                    $lcbeban = "Gaji & Tunjangan";
                    break;
                case '8': //TU
                    $lcbeban = "Tunjangan Transport";
                    break;
                case '9': //TU
                    $lcbeban = "Tunjangan Lainnya";
                    break;
                default:
                    $lcbeban = "LS";
            }
            $spp1 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->join('trskpd as d', function ($join) {
                $join->on('c.kd_sub_kegiatan', '=', 'd.kd_sub_kegiatan');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->select('d.nm_program as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'1' as urut"), DB::raw("LEFT(c.kd_sub_kegiatan,18) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("LEFT(c.kd_sub_kegiatan, 18), d.nm_program");
            $spp2 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->join('trskpd as d', function ($join) {
                $join->on('c.kd_sub_kegiatan', '=', 'd.kd_sub_kegiatan');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->select('c.nm_sub_kegiatan as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'2' as urut"), 'c.kd_sub_kegiatan as kode')->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, c.nm_sub_kegiatan")->unionAll($spp1);
            $spp3 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->leftJoin('ms_rek3 as d', DB::raw("LEFT(c.kd_rek6,3)"), '=', 'd.kd_rek3')->select('d.nm_rek3 as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'3' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,3)) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, LEFT(c.kd_rek6,3), d.nm_rek3")->unionAll($spp2);
            $spp4 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->leftJoin('ms_rek4 as d', DB::raw("LEFT(c.kd_rek6,5)"), '=', 'd.kd_rek4')->select('d.nm_rek4 as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'4' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,5)) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, LEFT(c.kd_rek6,5), d.nm_rek4")->unionAll($spp3);
            $spp5 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->select('c.nm_rek6 as nama', 'c.nilai', DB::raw("'5' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+ c.kd_rek6) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->unionAll($spp4);
            $result = DB::table(DB::raw("({$spp5->toSql()}) AS sub"))
                ->select("urut", "kode", "nama", "nilai")
                ->mergeBindings($spp5)
                // ->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')
                ->orderBy('kode')
                ->orderBy('urut')
                ->get();
            $total = 0;
            foreach ($result as $data) {
                if ($data->urut == '5')
                    $total += $data->nilai;
            }
            $jumlah_spp = '';
        } else if ($beban == '5') {
            $cari_data = DB::table('trhspp')->select('tgl_spp', 'bulan')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first();
            $tanggal = $cari_data->tgl_spp;
            $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
            $cari_jenis = DB::table('trhspp')->select('jns_beban')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first();
            $jenis = $cari_jenis->jns_beban;
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Hibah";
                    break;
                case '2': //GU
                    $lcbeban = "Bantuan Sosial";
                    break;
                case '3': //TU
                    $lcbeban = " Bantuan Keuangan";
                    break;
                case '4': //TU
                    $lcbeban = "  Subsidi";
                    break;
                case '5': //TU
                    $lcbeban = " Bagi Hasil";
                    break;
                case '6': //TU
                    $lcbeban = " Belanja Tidak Terduga";
                    break;
                case '7': //TU
                    $lcbeban = " Pihak Ketiga Lainnya";
                    break;
                case '8': //TU
                    $lcbeban = " Pengeluaran Pembiayaan";
                    break;
                case '9': //TU
                    $lcbeban = " Barang yang diserahkan ke masyarakat";
                    break;
            }
            $status_anggaran = status_anggaran();
            // $spp1 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
            //     $join->on('b.no_spp', '=', 'c.no_spp');
            //     $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            // })->join('trskpd as d', function ($join) {
            //     $join->on('c.kd_sub_kegiatan', '=', 'd.kd_sub_kegiatan');
            //     $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            // })->select('d.nm_program as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'1' as urut"), DB::raw("LEFT(c.kd_sub_kegiatan,7) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("LEFT(c.kd_sub_kegiatan, 7), d.nm_program");

            // $spp2 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
            //     $join->on('b.no_spp', '=', 'c.no_spp');
            //     $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            // })->join('trskpd as d', function ($join) {
            //     $join->on('c.kd_sub_kegiatan', '=', 'd.kd_sub_kegiatan');
            //     $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            // })->select('d.nm_kegiatan as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'2' as urut"), DB::raw("LEFT(c.kd_sub_kegiatan,12) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("LEFT(c.kd_sub_kegiatan, 12), d.nm_kegiatan")->unionAll($spp1);

            // $spp3 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
            //     $join->on('b.no_spp', '=', 'c.no_spp');
            //     $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            // })->join('trskpd as d', function ($join) {
            //     $join->on('c.kd_sub_kegiatan', '=', 'd.kd_sub_kegiatan');
            //     $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            // })->select('c.nm_sub_kegiatan as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'3' as urut"), 'c.kd_sub_kegiatan as kode')->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, c.nm_sub_kegiatan")->unionAll($spp2);

            // $spp4 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
            //     $join->on('b.no_spp', '=', 'c.no_spp');
            //     $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            // })->leftJoin('ms_rek3 as d', DB::raw("LEFT(c.kd_rek6,4)"), '=', 'd.kd_rek3')->select('d.nm_rek3 as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'4' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,4)) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, LEFT(c.kd_rek6,4), d.nm_rek3")->unionAll($spp3);

            // $spp5 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
            //     $join->on('b.no_spp', '=', 'c.no_spp');
            //     $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            // })->leftJoin('ms_rek4 as d', DB::raw("LEFT(c.kd_rek6,6)"), '=', 'd.kd_rek4')->select('d.nm_rek4 as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'5' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,6)) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, LEFT(c.kd_rek6,6), d.nm_rek4")->unionAll($spp4);

            // $spp6 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
            //     $join->on('b.no_spp', '=', 'c.no_spp');
            //     $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            // })->leftJoin('ms_rek5 as d', DB::raw("LEFT(c.kd_rek6,8)"), '=', 'd.kd_rek5')->select('d.nm_rek5 as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'6' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,8)) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, LEFT(c.kd_rek6,8), d.nm_rek5")->unionAll($spp5);

            // $spp7 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
            //     $join->on('b.no_spp', '=', 'c.no_spp');
            //     $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            // })->select('c.nm_rek6 as nama', 'c.nilai', DB::raw("'7' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+c.kd_rek6) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->unionAll($spp6);

            // $result = DB::table(DB::raw("({$spp7->toSql()}) AS sub"))
            //     ->select("urut", "kode", "nama", "nilai")
            //     ->mergeBindings($spp7)
            //     ->orderBy('kode')
            //     ->orderBy('urut')
            //     ->get();

            $result = DB::select("SELECT 1 urut, LEFT(c.kd_sub_kegiatan,7) as kode, d.nm_program as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trskpd d ON c.kd_sub_kegiatan=d.kd_sub_kegiatan  AND c.kd_skpd=d.kd_skpd
                    WHERE b.no_spp=? AND b.kd_skpd=? and d.jns_ang=?
                    GROUP BY LEFT(c.kd_sub_kegiatan,7), d.nm_program
                    UNION ALL
                    SELECT 2 urut, LEFT(c.kd_sub_kegiatan,12) as kode, d.nm_kegiatan as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trskpd d ON c.kd_sub_kegiatan=d.kd_sub_kegiatan  AND c.kd_skpd=d.kd_skpd
                    WHERE b.no_spp=? AND b.kd_skpd=? and d.jns_ang=?
                    GROUP BY LEFT(c.kd_sub_kegiatan,12), d.nm_kegiatan
                    UNION ALL
                    SELECT 3 urut, c.kd_sub_kegiatan as kode, c.nm_sub_kegiatan as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    INNER JOIN trskpd d ON c.kd_sub_kegiatan=d.kd_sub_kegiatan  AND c.kd_skpd=d.kd_skpd
                    WHERE b.no_spp=? AND b.kd_skpd=? and d.jns_ang=?
                    GROUP BY c.kd_sub_kegiatan,c.nm_sub_kegiatan
                    UNION ALL
                    SELECT 4 urut, c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,4) as kode, d.nm_rek3 as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    LEFT JOIN ms_rek3 d ON LEFT(c.kd_rek6,4)=d.kd_rek3
                    WHERE b.no_spp=? AND b.kd_skpd=?
                    GROUP BY c.kd_sub_kegiatan,LEFT(c.kd_rek6,4),d.nm_rek3
                    UNION ALL
                    SELECT 5 urut, c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,6) as kode, d.nm_rek4 as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    LEFT JOIN ms_rek4 d ON LEFT(c.kd_rek6,6)=d.kd_rek4
                    WHERE b.no_spp=? AND b.kd_skpd=?
                    GROUP BY c.kd_sub_kegiatan,LEFT(c.kd_rek6,6),d.nm_rek4
                    UNION ALL
                    SELECT 6 urut, c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,8) as kode, d.nm_rek5 as nama,SUM(c.nilai) as nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    LEFT JOIN ms_rek5 d ON LEFT(c.kd_rek6,8)=d.kd_rek5
                    WHERE b.no_spp=? AND b.kd_skpd=?
                    GROUP BY c.kd_sub_kegiatan,LEFT(c.kd_rek6,8),d.nm_rek5
                    UNION ALL
                    SELECT 7 urut, c.kd_sub_kegiatan+'.'+c.kd_rek6 as kode, c.nm_rek6 as nama, c.nilai
                    FROM trhspp b
                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                    WHERE b.no_spp=? AND b.kd_skpd=?
                    order by kode", [$no_spp, $kd_skpd, $status_anggaran, $no_spp, $kd_skpd, $status_anggaran, $no_spp, $kd_skpd, $status_anggaran, $no_spp, $kd_skpd, $no_spp, $kd_skpd, $no_spp, $kd_skpd, $no_spp, $kd_skpd]);

            $total = 0;
            foreach ($result as $data) {
                if ($data->urut == '7')
                    $total += $data->nilai;
            }
            $jumlah_spp = '';
        } else if ($beban == '6') {
            $cari_data = DB::table('trhspp')->select('tgl_spp', 'bulan')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first();
            $tanggal = $cari_data->tgl_spp;
            $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
            $cari_jenis = DB::table('trhspp')->select('jns_beban')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first();
            $jenis = $cari_jenis->jns_beban;
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Rutin (PNS)";
                    break;
                case '2': //GU
                    $lcbeban = "Rutin (Non PNS)";
                    break;
                case '3': //TU
                    $lcbeban = "Pihak Ketiga";
                    break;
                // case '4': //TU
                //     $lcbeban = "  Honor Kontrak";
                //     break;
                // case '5': //TU
                //     $lcbeban = " Jasa Pelayanan Kesehatan";
                //     break;
                // case '6': //TU
                //     $lcbeban = " Pihak ketiga";
                //     break;
                // case '7': //TU
                //     $lcbeban = " PNS";
                //     break;
            }

            $spp1 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->join('trskpd as d', function ($join) {
                $join->on('c.kd_sub_kegiatan', '=', 'd.kd_sub_kegiatan');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->select('d.nm_program as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'1' as urut"), DB::raw("LEFT(c.kd_sub_kegiatan,18) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("LEFT(c.kd_sub_kegiatan, 18), d.nm_program");

            $spp2 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->join('trskpd as d', function ($join) {
                $join->on('c.kd_sub_kegiatan', '=', 'd.kd_sub_kegiatan');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })->select('c.nm_sub_kegiatan as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'2' as urut"), 'c.kd_sub_kegiatan as kode')->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, c.nm_sub_kegiatan")->unionAll($spp1);

            $spp3 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->leftJoin('ms_rek3 as d', DB::raw("LEFT(c.kd_rek6,4)"), '=', 'd.kd_rek3')->select('d.nm_rek3 as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'3' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,4)) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, LEFT(c.kd_rek6,4), d.nm_rek3")->unionAll($spp2);

            $spp4 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->leftJoin('ms_rek4 as d', DB::raw("LEFT(c.kd_rek6,6)"), '=', 'd.kd_rek4')->select('d.nm_rek4 as nama', DB::raw("SUM(c.nilai) as nilai"), DB::raw("'4' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+LEFT(c.kd_rek6,6)) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->groupByRaw("c.kd_sub_kegiatan, LEFT(c.kd_rek6,6), d.nm_rek4")->unionAll($spp3);

            $spp5 = DB::table('trhspp as b')->join('trdspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })->select('c.nm_rek6 as nama', 'c.nilai', DB::raw("'5' as urut"), DB::raw("(c.kd_sub_kegiatan+'.'+c.kd_rek6) as kode"))->where(['b.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->unionAll($spp4);

            $result = DB::table(DB::raw("({$spp5->toSql()}) AS sub"))
                ->select("urut", "kode", "nama", "nilai")
                ->mergeBindings($spp5)
                ->orderBy('kode')
                ->orderBy('urut')
                ->get();
            $total = 0;
            foreach ($result as $data) {
                if ($data->urut == '5')
                    $total += $data->nilai;
            }
            $jumlah_spp = DB::table('trhspp')->whereRaw("keperluan like '%Tambahan Penghasilan Pegawai%'")->where('no_spp', $no_spp)->count();
        }
        $skpd = DB::table('ms_skpd')
            ->select('nm_skpd')
            ->where(['kd_skpd' => $kd_skpd])
            ->first();
        $header =  DB::table('config_app')
            ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
            ->first();
        $view = view('penatausahaan.pengeluaran.spp_ls.cetak.rincian', compact('jumlah_spp', 'sub_kegiatan', 'cari_pptk', 'tanggal', 'cari_bendahara', 'tanpa', 'daerah', 'total', 'result', 'beban', 'nama_skpd', 'tahun_anggaran', 'lcbeban', 'no_spp', 'cari_data', 'header', 'skpd'));
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

    public function cetakPermintaanLayar(Request $request)
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
        $tahun_anggaran = tahun_anggaran();

        $skpd = DB::table('ms_skpd')->select('nm_skpd', 'kodepos', 'alamat')->where('kd_skpd', $kd_skpd)->first();
        $cari_jenis = DB::table('trhspp')->select('jns_beban')->where('no_spp', $no_spp)->first();
        $jenis = $cari_jenis->jns_beban;
        $nama_skpd = $skpd->nm_skpd;
        $alamat_skpd = $skpd->alamat;
        $kodepos = $skpd->kodepos == '' ? "--------" : $skpd->kodepos;

        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        $cari_pptk = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pptk, 'kode' => 'PPTK', 'kd_skpd' => $kd_skpd])->first();
        $kd_sub_kegiatan = DB::table('trdspp')->select('kd_sub_kegiatan')->where('no_spp', $no_spp)->groupBy('kd_sub_kegiatan')->first();
        $sub_kegiatan = $kd_sub_kegiatan->kd_sub_kegiatan == "" ? "" : $kd_sub_kegiatan->kd_sub_kegiatan;

        if ($beban == 1) {
            $lcbeban = "Uang Persedian";
        }
        if ($beban == 2) {
            $lcbeban = "Ganti Uang Persedian";
        }
        if ($beban == 3) {
            $lcbeban = "Tambah Uang Persedian";
        }
        if ($beban == 4) {
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "LS - Gaji dan Tunjangan";
                    break;
                // case '2': //GU
                //     $lcbeban = "LS - Uang Kespeg";
                //     break;
                // case '3': //TU
                //     $lcbeban = "LS - Uang Makan";
                //     break;
                // case '4': //TU
                //     $lcbeban = "LS - Upah Pungut";
                //     break;
                // case '5': //TU
                //     $lcbeban = "LS - Upah Pungut PBB";
                //     break;
                // case '6': //TU
                //     $lcbeban = "LS - Upah Pungut PBB-KB PKB & BBN-KB ";
                //     break;
                case '7': //TU
                    $lcbeban = "LS - Tambah / Kekurangan Gaji Tunjangan";
                    break;
                case '8': //TU
                    $lcbeban = "LS - Tunjangan Transport";
                    break;
                case '9': //TU
                    $lcbeban = "LS - Tunjangan Lainnya";
                    break;
                default:
                    $lcbeban = "LS";
            }
        }
        if ($beban == 5) {
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Hibah";
                    break;
                case '2': //GU
                    $lcbeban = "Bantuan Sosial";
                    break;
                case '3': //TU
                    $lcbeban = " Bantuan Keuangan";
                    break;
                case '4': //TU
                    $lcbeban = "  Subsidi";
                    break;
                case '5': //TU
                    $lcbeban = " Bagi Hasil";
                    break;
                case '6': //TU
                    $lcbeban = " Belanja Tidak Terduga";
                    break;
                case '7': //TU
                    $lcbeban = " Pihak Ketiga Lainnya";
                    break;
                case '8': //TU
                    $lcbeban = " Pengeluaran Pembiayaan";
                    break;
                case '9': //TU
                    $lcbeban = " Barang yang diserahkan ke masyarakat";
                    break;
            }
        }
        if ($beban == 6) {
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Rutin (PNS)";
                    break;
                case '2': //GU
                    $lcbeban = "Rutin (Non PNS)";
                    break;
                case '3': //TU
                    $lcbeban = "Pihak Ketiga";
                    break;
                // case '4': //TU
                //     $lcbeban = "  Honor Kontrak";
                //     break;
                // case '5': //TU
                //     $lcbeban = " Jasa Pelayanan Kesehatan";
                //     break;
                // case '6': //TU
                //     $lcbeban = " Pihak ketiga";
                //     break;
                // case '7': //TU
                //     $lcbeban = " PNS";
                //     break;
            }
        }
        $cari_spp = DB::table('trhspp')->select('tgl_spp')->where('no_spp', $no_spp)->first();
        $tanggal = $cari_spp->tgl_spp;
        $cari_spd = DB::table('trhspp')->select('no_spd')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first();
        $no_spd = $cari_spd->no_spd;
        $daerah = DB::table('sclient')->select('daerah', 'nogub_susun', 'nogub_perubahan')->where('kd_skpd', $kd_skpd)->first();
        $data_spp = DB::table('trhspp as a')->join('ms_bidang_urusan as b', DB::raw("SUBSTRING(a.kd_skpd,1,4)"), '=', 'b.kd_bidang_urusan')->select('a.no_spp', 'a.tgl_spp', 'a.kd_skpd', 'a.nm_skpd', 'a.kd_program', 'a.nm_program', 'a.nm_sub_kegiatan', 'a.kd_sub_kegiatan', 'a.bulan', 'a.nmrekan', 'a.no_rek as no_rek_rek', 'a.npwp as npwp_rek', 'b.kd_bidang_urusan', 'b.nm_bidang_urusan', 'a.bank', 'lanjut', 'kontrak', 'keperluan', 'pimpinan', 'alamat', 'a.no_spd', 'a.nilai', DB::raw("(SELECT nama from ms_bank WHERE kode=a.bank) as nama_bank_rek"), DB::raw("(SELECT rekening FROM ms_skpd WHERE kd_skpd = a.kd_skpd) as no_rek"), DB::raw("(SELECT npwp FROM ms_skpd WHERE kd_skpd=a.kd_skpd) as npwp"))->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])->first();
        $bank = DB::table('ms_skpd')->select('bank')->where('kd_skpd', $kd_skpd)->first();
        $nama_bank = DB::table('ms_bank')->select('nama')->where('kode', $bank->bank)->first();
        $daerah1 = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();
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
        $status = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
        $status_anggaran = $status->jns_ang;
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
        $jumlah_spp = DB::table('trhspp')->whereRaw("keperluan like '%Tambahan Penghasilan Pegawai%'")->where('no_spp', $no_spp)->count();
        $dpa = DB::table('trhrka')->select('no_dpa', 'tgl_dpa')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first();
        $header =  DB::table('config_app')
            ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
            ->first();

        $view = view('penatausahaan.pengeluaran.spp_ls.cetak.permintaan', compact('nama_kegiatan', 'nama_program', 'data_spp', 'dpa', 'alamat_skpd', 'lcbeban', 'nama_skpd', 'tahun_anggaran', 'no_spp', 'beban', 'daerah', 'nogub', 'jenis', 'no_spd', 'cari_bendahara', 'nama_bank', 'sub_kegiatan', 'jumlah_spp', 'cari_pptk', 'tanpa', 'tanggal', 'header', 'skpd'));
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

    public function cetakRingkasanLayar(Request $request)
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
        $tahun_anggaran = tahun_anggaran();

        $skpd = DB::table('ms_skpd')->select('nm_skpd', 'kodepos', 'alamat')->where('kd_skpd', $kd_skpd)->first();
        $cari_jenis = DB::table('trhspp')->select('jns_beban')->where('no_spp', $no_spp)->first();
        $jenis = $cari_jenis->jns_beban;
        $nama_skpd = $skpd->nm_skpd;
        $alamat_skpd = $skpd->alamat;
        $kodepos = $skpd->kodepos == '' ? "--------" : $skpd->kodepos;
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BPP', 'BK'])
            ->first();
        $cari_pptk = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pptk, 'kode' => 'PPTK', 'kd_skpd' => $kd_skpd])->first();
        $kd_sub_kegiatan = DB::table('trdspp')->select('kd_sub_kegiatan')->where('no_spp', $no_spp)->groupBy('kd_sub_kegiatan')->first();
        $sub_kegiatan = $kd_sub_kegiatan->kd_sub_kegiatan == "" ? "" : $kd_sub_kegiatan->kd_sub_kegiatan;
        $jumlah_spp = DB::table('trhspp')->whereRaw("keperluan like '%Tambahan Penghasilan Pegawai%'")->where('no_spp', $no_spp)->count();

        if ($beban == '4') {
            $cari_jenis = DB::table('trhspp')->select('jns_beban')->where('no_spp', $no_spp)->first();
            $jenis = $cari_jenis->jns_beban;
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Gaji dan Tunjangan";
                    break;
                // case '2': //GU
                //     $lcbeban = "Uang Kespeg";
                //     break;
                // case '3': //TU
                //     $lcbeban = "Uang Makan";
                //     break;
                // case '4': //TU
                //     $lcbeban = "Upah Pungut";
                //     break;
                // case '5': //TU
                //     $lcbeban = "Upah Pungut PBB";
                //     break;
                // case '6': //TU
                //     $lcbeban = "Upah Pungut PBB-KB PKB & BBN-KB ";
                //     break;
                case '7': //TU
                    $lcbeban = "Tambah/Kekurangan Gaji Tunjangan";
                    break;
                case '8': //TU
                    $lcbeban = "Tunjangan Transport";
                    break;
                case '9': //TU
                    $lcbeban = "Tunjangan Lainnya";
                    break;
                default:
                    $lcbeban = "LS";
            }
            // $cari_rek = DB::table('trdspp')->select('kd_rek6')->where(['no_spp' => $no_spp, 'kd_skpd' => $kd_skpd])->first();
            $cari_spp = DB::table('trhspp')->select('tgl_spp', 'no_spd')->where('no_spp', $no_spp)->first();
            $tanggal = $cari_spp->tgl_spp;
            $cari_spd = DB::table('trhspd')->select('tgl_spd')->where('no_spd', $cari_spp->no_spd)->first();

            $daerah = DB::table('sclient')->select('tgl_rka', 'provinsi', 'kab_kota', 'daerah', 'thn_ang', 'nogub_susun', 'nogub_p1', 'nogub_p2', 'nogub_p3', 'nogub_p4', 'nogub_p5', 'nogub_perubahan', 'nogub_perubahan2', 'nogub_perubahan3', 'nogub_perubahan4', 'nogub_perubahan5')->where('kd_skpd', $kd_skpd)->first();
            $data_spp = '';
            $status = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
            $status_anggaran = $status->jns_ang;
            if ($status_anggaran == 'M') {
                $nogub = $daerah->nogub_susun;
            } else if ($status_anggaran == 'P1') {
                $nogub = $daerah->nogub_p1;
            } else if ($status_anggaran == 'P2') {
                $nogub = $daerah->nogub_p2;
            } else if ($status_anggaran == 'P3') {
                $nogub = $daerah->nogub_p3;
            } else if ($status_anggaran == 'P4') {
                $nogub = $daerah->nogub_p4;
            } else if ($status_anggaran == 'P5') {
                $nogub = $daerah->nogub_p5;
            } else if ($status_anggaran == 'U1') {
                $nogub = $daerah->nogub_perubahan;
            } else if ($status_anggaran == 'U2') {
                $nogub = $daerah->nogub_perubahan2;
            } else if ($status_anggaran == 'U3') {
                $nogub = $daerah->nogub_perubahan3;
            } else if ($status_anggaran == 'U4') {
                $nogub = $daerah->nogub_perubahan4;
            } else {
                $nogub = $daerah->nogub_perubahan5;
            }

            $data_nilai = DB::table('trdrka')->select(DB::raw("SUM(nilai) as nilai"))->whereRaw("LEFT(kd_skpd,22)=left('$kd_skpd',22) AND kd_sub_kegiatan='$sub_kegiatan' AND jns_ang='$status_anggaran'")->first();
            $tglspd = $cari_spp->tgl_spp;
            $revisi1 = DB::table('trhspd')->select(DB::raw("MAX(revisi_ke) as revisi"))->where(['kd_skpd' => $kd_skpd, 'bulan_akhir' => '3'])->where('tgl_spd', '<=', $tglspd)->first();
            $revisi2 = DB::table('trhspd')->select(DB::raw("ISNULL(MAX(revisi_ke),0) as revisi"))->where(['kd_skpd' => $kd_skpd, 'bulan_akhir' => '6'])->where('tgl_spd', '<=', $tglspd)->first();
            $revisi3 = DB::table('trhspd')->select(DB::raw("ISNULL(MAX(revisi_ke),0) as revisi"))->where(['kd_skpd' => $kd_skpd, 'bulan_akhir' => '9'])->where('tgl_spd', '<=', $tglspd)->first();
            $revisi4 = DB::table('trhspd')->select(DB::raw("ISNULL(MAX(revisi_ke),0) as revisi"))->where(['kd_skpd' => $kd_skpd, 'bulan_akhir' => '12'])->where('tgl_spd', '<=', $tglspd)->first();

            $data_spp1 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("LEFT(a.kd_unit,22)=LEFT('$kd_skpd',22) AND b.tgl_spd <= '$tglspd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '3' AND revisi_ke = '$revisi1->revisi' ")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd');
            $data_spp2 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("LEFT(a.kd_unit,22)=LEFT('$kd_skpd',22) AND b.tgl_spd <= '$tglspd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '6' AND revisi_ke = '$revisi2->revisi' ")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd')->unionAll($data_spp1);
            $data_spp3 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("LEFT(a.kd_unit,22)=LEFT('$kd_skpd',22) AND b.tgl_spd <= '$tglspd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '9' AND revisi_ke = '$revisi3->revisi' ")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd')->unionAll($data_spp2);
            $data_spp4 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("LEFT(a.kd_unit,22)=LEFT('$kd_skpd',22) AND b.tgl_spd <= '$tglspd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '12' AND revisi_ke = '$revisi4->revisi' ")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd')->unionAll($data_spp3);
            $result = DB::table(DB::raw("({$data_spp4->toSql()}) AS sub"))
                ->select("no_spd", "tgl_spd", "nilai")
                ->mergeBindings($data_spp4)
                ->get();
            $nilai1 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("LEFT(a.kd_skpd,22) = LEFT('$kd_skpd',22) AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp = '4' AND SUBSTRING(kd_rek6,1,5)='51010' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();
            $nilai2 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("LEFT(a.kd_skpd,22) = LEFT('$kd_skpd',22) AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp = '5' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();
            $nilai3 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("a.kd_skpd= '$kd_skpd' AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp = '6' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();
            $nilai4 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("LEFT(a.kd_skpd,22) = LEFT('$kd_skpd',22) AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp IN ('1','2') AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();
            $nilai5 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("LEFT(a.kd_skpd,22) = LEFT('$kd_skpd',22) AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp ='3' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();
            $totalbelanja = $nilai1->nilai + $nilai2->nilai + $nilai3->nilai + $nilai4->nilai + $nilai5->nilai;
            $totalspd = 0;
            foreach ($result as $nilai) {
                $totalspd += $nilai->nilai;
            }
            $blmspd = $data_nilai->nilai - $totalspd;
            $sisaspp = $totalspd - $totalbelanja;
            $data_dpa = '';
        } elseif ($beban == '5') {
            $cari_jenis = DB::table('trhspp')->select('jns_beban')->where('no_spp', $no_spp)->first();
            $jenis = $cari_jenis->jns_beban;
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Hibah";
                    break;
                case '2': //GU
                    $lcbeban = "Bantuan Sosial";
                    break;
                case '3': //TU
                    $lcbeban = " Bantuan Keuangan";
                    break;
                case '4': //TU
                    $lcbeban = "  Subsidi";
                    break;
                case '5': //TU
                    $lcbeban = " Bagi Hasil";
                    break;
                case '6': //TU
                    $lcbeban = " Belanja Tidak Terduga";
                    break;
                case '7': //TU
                    $lcbeban = " Pihak Ketiga Lainnya";
                    break;
                case '8': //TU
                    $lcbeban = " Pengeluaran Pembiayaan";
                    break;
                case '9': //TU
                    $lcbeban = " Barang yang diserahkan ke masyarakat";
                    break;
            }
            $cari_spp = DB::table('trhspp')->select('tgl_spp', 'no_spd')->where('no_spp', $no_spp)->first();
            $tanggal = $cari_spp->tgl_spp;
            $cari_spd = DB::table('trhspd')->select('tgl_spd')->where('no_spd', $cari_spp->no_spd)->first();

            $data_spp = DB::table('trhspp as a')->select('nm_program', 'nm_sub_kegiatan', 'nmrekan', 'pimpinan', 'kontrak', 'alamat', 'tgl_mulai', 'tgl_akhir', 'lanjut', 'no_rek', 'keperluan', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nama_bank"))->where('a.no_spp', $no_spp)->first();

            $status_anggaran = status_anggaran();

            $data_dpa = DB::table('trhrka')->select('no_dpa', 'tgl_dpa')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first();

            if (substr($kd_skpd, 18, 4) == '0000') {
                // $data_nilai = DB::table('trdrka')->select(DB::raw("SUM(nilai) as nilai"))->whereRaw("LEFT(kd_rek6,1)= '5' AND left(kd_skpd,17) = LEFT('$kd_skpd', 17) AND kd_sub_kegiatan='$sub_kegiatan' AND jns_ang='$status_anggaran'")->first();
                if ($beban == '5' && $jenis == '8') {
                    $data_nilai = collect(DB::select("SELECT SUM(nilai) as nilai FROM trdrka where left(kd_rek6,1)='6' and left(kd_skpd,22)=left(?,22) and kd_sub_kegiatan=? and jns_ang=?", [$kd_skpd, $sub_kegiatan, $status_anggaran]))->first();
                } else {
                    $data_nilai = collect(DB::select("SELECT SUM(nilai) as nilai FROM trdrka where left(kd_rek6,1)='5' and left(kd_skpd,22)=left(?,22) and kd_sub_kegiatan=? and jns_ang=?", [$kd_skpd, $sub_kegiatan, $status_anggaran]))->first();
                }
            } else {
                $data_nilai = DB::table('trdrka')->select(DB::raw("SUM(nilai) as nilai"))->where(['kd_skpd' => $kd_skpd, 'kd_sub_kegiatan' => $kd_sub_kegiatan, 'jns_ang' => $status_anggaran])->first();
            }
            $daerah = DB::table('sclient')->select('daerah')->where(['kd_skpd' => $kd_skpd])->first();
            $tglspd = $cari_spp->tgl_spp;

            $revisi1 = DB::table('trhspd')->select(DB::raw("MAX(revisi_ke) as revisi"))->where(['kd_skpd' => $kd_skpd, 'bulan_akhir' => '3'])->where('tgl_spd', '<=', $tglspd)->first();
            $revisi2 = DB::table('trhspd')->select(DB::raw("ISNULL(MAX(revisi_ke),0) as revisi"))->where(['kd_skpd' => $kd_skpd, 'bulan_akhir' => '6'])->where('tgl_spd', '<=', $tglspd)->first();
            $revisi3 = DB::table('trhspd')->select(DB::raw("ISNULL(MAX(revisi_ke),0) as revisi"))->where(['kd_skpd' => $kd_skpd, 'bulan_akhir' => '9'])->where('tgl_spd', '<=', $tglspd)->first();
            $revisi4 = DB::table('trhspd')->select(DB::raw("ISNULL(MAX(revisi_ke),0) as revisi"))->where(['kd_skpd' => $kd_skpd, 'bulan_akhir' => '12'])->where('tgl_spd', '<=', $tglspd)->first();

            $data_spp1 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("LEFT(a.kd_unit,22)=LEFT('$kd_skpd',22) AND b.tgl_spd <= '$tglspd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '3' AND revisi_ke = '$revisi1->revisi' ")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd');
            $data_spp2 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("LEFT(a.kd_unit,22)=LEFT('$kd_skpd',22) AND b.tgl_spd <= '$tglspd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '6' AND revisi_ke = '$revisi2->revisi' ")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd')->unionAll($data_spp1);
            $data_spp3 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("LEFT(a.kd_unit,22)=LEFT('$kd_skpd',22) AND b.tgl_spd <= '$tglspd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '9' AND revisi_ke = '$revisi3->revisi' ")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd')->unionAll($data_spp2);
            $data_spp4 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("LEFT(a.kd_unit,22)=LEFT('$kd_skpd',22) AND b.tgl_spd <= '$tglspd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '12' AND revisi_ke = '$revisi4->revisi' ")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd')->unionAll($data_spp3);
            $result = DB::table(DB::raw("({$data_spp4->toSql()}) AS sub"))
                ->select("no_spd", "tgl_spd", "nilai")
                ->mergeBindings($data_spp4)
                ->get();

            $sorting = "a.kd_skpd = '$kd_skpd'";
            $nilai1 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp = '6' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();

            $nilai2 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp = '5' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();

            $nilai3 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp IN ('1','2') AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();

            $nilai4 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp IN ('3') AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();

            $nilai5 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp ='4' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->first();

            $totalbelanja = $nilai1->nilai + $nilai2->nilai + $nilai3->nilai + $nilai4->nilai + $nilai5->nilai;
            $totalspd = 0;
            foreach ($result as $nilai) {
                $totalspd += $nilai->nilai;
            }
            $blmspd = $data_nilai->nilai - $totalspd;
            $sisaspp = $totalspd - $totalbelanja;
        } elseif ($beban == '6') {
            $cari_jenis = DB::table('trhspp')->select('jns_beban')->where('no_spp', $no_spp)->first();
            $jenis = $cari_jenis->jns_beban;
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Rutin (PNS)";
                    break;
                case '2': //GU
                    $lcbeban = "Rutin (Non PNS)";
                    break;
                case '3': //TU
                    $lcbeban = "Pihak Ketiga";
                    break;
                // case '4': //TU
                //     $lcbeban = " Honor Kontrak";
                //     break;
                // case '5': //TU
                //     $lcbeban = " Jasa Pelayanan Kesehatan";
                //     break;
                // case '6': //TU
                //     $lcbeban = " Pihak ketiga";
                //     break;
                // case '7': //TU
                //     $lcbeban = " PNS";
                //     break;
            }
            $cari_spp = DB::table('trhspp')->select('tgl_spp', 'no_spd')->where('no_spp', $no_spp)->first();
            $tanggal = $cari_spp->tgl_spp;
            $cari_spd = DB::table('trhspd')->select('tgl_spd')->where('no_spd', $cari_spp->no_spd)->first();

            $data_spp = DB::table('trhspp as a')->select('nm_program', 'nm_sub_kegiatan', 'nmrekan', 'pimpinan', 'kontrak', 'alamat', 'tgl_mulai', 'tgl_akhir', 'lanjut', 'no_rek', 'keperluan', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nama_bank"))->where('a.no_spp', $no_spp)->first();
            $status = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
            $status_anggaran = $status->jns_ang;
            $data_dpa = DB::table('trhrka')->select('no_dpa', 'tgl_dpa')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first();


            $data_nilai = DB::table('trdrka')
                    ->selectRaw("sum(nilai) as nilai")
                    ->where(['kd_skpd' => $kd_skpd, 'kd_sub_kegiatan' => $sub_kegiatan, 'jns_ang' => $status_anggaran])
                    ->first();

            $daerah = DB::table('sclient')->select('daerah')->where(['kd_skpd' => $kd_skpd])->first();
            $tglspd = $cari_spp->tgl_spp;

            $revisi1 = DB::table('trhspd')->select(DB::raw("MAX(revisi_ke) as revisi"))->whereRaw("LEFT(kd_skpd,22)=LEFT('$kd_skpd',22) AND bulan_akhir='3' AND tgl_spd<='$cari_spd->tgl_spd'")->first();
            $revisi2 = DB::table('trhspd')->select(DB::raw("ISNULL(MAX(revisi_ke),0) as revisi"))->whereRaw("LEFT(kd_skpd,22)=LEFT('$kd_skpd',22) AND bulan_akhir='6' AND tgl_spd<='$cari_spd->tgl_spd'")->first();
            $revisi3 = DB::table('trhspd')->select(DB::raw("ISNULL(MAX(revisi_ke),0) as revisi"))->whereRaw("LEFT(kd_skpd,22)=LEFT('$kd_skpd',22) AND bulan_akhir='9' AND tgl_spd<='$cari_spd->tgl_spd'")->first();
            $revisi4 = DB::table('trhspd')->select(DB::raw("ISNULL(MAX(revisi_ke),0) as revisi"))->whereRaw("LEFT(kd_skpd,22)=LEFT('$kd_skpd',22) AND bulan_akhir='12' AND tgl_spd<='$cari_spd->tgl_spd'")->first();


            $sorting = "a.kd_unit = '$kd_skpd'";
            $sorting2 = "a.kd_skpd = '$kd_skpd'";

            $data_spp1 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("$sorting AND b.tgl_spd <= '$cari_spd->tgl_spd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '3' AND revisi_ke = '$revisi1->revisi' AND b.jns_beban = '5'")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd');

            $data_spp2 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("$sorting AND b.tgl_spd <= '$cari_spd->tgl_spd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '6' AND revisi_ke = '$revisi2->revisi' AND b.jns_beban ='5'")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd')->unionAll($data_spp1);

            $data_spp3 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("$sorting AND b.tgl_spd <= '$cari_spd->tgl_spd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '9' AND revisi_ke = '$revisi3->revisi' AND b.jns_beban ='5'")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd')->unionAll($data_spp2);

            $data_spp4 = DB::table('trdspd as a')->join('trhspd as b', 'a.no_spd', '=', 'b.no_spd')->whereRaw("$sorting AND b.tgl_spd <= '$cari_spd->tgl_spd' AND a.kd_sub_kegiatan= '$sub_kegiatan' AND bulan_akhir = '12' AND revisi_ke = '$revisi4->revisi' AND b.jns_beban ='5'")->select('a.no_spd', 'b.tgl_spd', DB::raw("SUM(a.nilai) as nilai"))->groupBy('a.no_spd', 'b.tgl_spd')->unionAll($data_spp3);

            $result = DB::table(DB::raw("({$data_spp4->toSql()}) AS sub"))
                ->select("no_spd", "tgl_spd", "nilai")
                ->mergeBindings($data_spp4)
                ->get();

            $nilai1 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting2 AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp = '6' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->where(function ($query) {
                $query->where('a.sp2d_batal', '=', '')->orWhereNull('a.sp2d_batal');
            })->first();

            $nilai2 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting2 AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp IN ('1','2') AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->where(function ($query) {
                $query->where('a.sp2d_batal', '=', '')->orWhereNull('a.sp2d_batal');
            })->first();

            $nilai3 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting2 AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp = '3' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->where(function ($query) {
                $query->where('a.sp2d_batal', '=', '')->orWhereNull('a.sp2d_batal');
            })->first();

            $nilai4 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting2 AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp = '5' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->where(function ($query) {
                $query->where('a.sp2d_batal', '=', '')->orWhereNull('a.sp2d_batal');
            })->first();

            $nilai5 = DB::table('trdspp as b')->join('trhspp as a', function ($join) {
                $join->on('b.no_spp', '=', 'a.no_spp');
                $join->on('b.kd_skpd', '=', 'a.kd_skpd');
            })->join('trhsp2d as c', 'a.no_spp', '=', 'c.no_spp')->select(DB::raw("SUM(b.nilai) as nilai"))->whereRaw("$sorting2 AND b.kd_sub_kegiatan = '$sub_kegiatan' AND a.jns_spp ='4' AND a.no_spp != '$no_spp' AND c.tgl_sp2d <='$tglspd'")->where(function ($query) {
                $query->where('a.sp2d_batal', '=', '')->orWhereNull('a.sp2d_batal');
            })->first();

            $totalbelanja = $nilai1->nilai + $nilai2->nilai + $nilai3->nilai + $nilai4->nilai + $nilai5->nilai;
            $totalspd = 0;
            foreach ($result as $nilai) {
                $totalspd += $nilai->nilai;
            }
            $blmspd = $data_nilai->nilai - $totalspd;
            $sisaspp = $totalspd - $totalbelanja;
        }
        $header =  DB::table('config_app')
            ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
            ->first();

        $view = view('penatausahaan.pengeluaran.spp_ls.cetak.ringkasan', compact('result', 'data_nilai', 'no_spp', 'beban', 'nama_skpd', 'tahun_anggaran', 'lcbeban', 'nilai1', 'nilai2', 'nilai3', 'nilai4', 'nilai5', 'totalspd', 'blmspd', 'totalbelanja', 'sisaspp', 'tanpa', 'tanggal', 'cari_bendahara', 'cari_pptk', 'daerah', 'sub_kegiatan', 'jenis', 'data_spp', 'data_dpa', 'jumlah_spp', 'header', 'skpd'));
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

    public function cetakPernyataanLayar(Request $request)
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
        $tahun_anggaran = tahun_anggaran();

        $skpd = DB::table('ms_skpd')->select('nm_skpd', 'kodepos', 'alamat')->where('kd_skpd', $kd_skpd)->first();
        $cari_jenis = DB::table('trhspp')->select('jns_beban')->where('no_spp', $no_spp)->first();
        $jenis = $cari_jenis->jns_beban;
        $nama_skpd = $skpd->nm_skpd;
        $alamat_skpd = $skpd->alamat;
        $kodepos = $skpd->kodepos == '' ? "--------" : $skpd->kodepos;
        $cari_bendahara = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $kd_sub_kegiatan = DB::table('trdspp')->select('kd_sub_kegiatan')->where('no_spp', $no_spp)->groupBy('kd_sub_kegiatan')->first();
        $sub_kegiatan = $kd_sub_kegiatan->kd_sub_kegiatan == "" ? "" : $kd_sub_kegiatan->kd_sub_kegiatan;
        $jumlah_spp = DB::table('trhspp')->whereRaw("keperluan like '%Tambahan Penghasilan Pegawai%'")->where('no_spp', $no_spp)->count();
        $data = DB::table('trhspp as a')->join('ms_bidang_urusan as b', DB::raw("SUBSTRING(a.kd_skpd,1,4)"), '=', 'b.kd_bidang_urusan')->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])->select('nm_skpd', 'tgl_spp', 'jns_beban', 'nilai')->first();
        $daerah = DB::table('sclient')->where(['kd_skpd' => $kd_skpd])->select('daerah')->first();
        if ($beban == '4') {
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Gaji dan Tunjangan";
                    break;
                // case '2': //GU
                //     $lcbeban = "Uang Kespeg";
                //     break;
                // case '3': //TU
                //     $lcbeban = "Uang Makan";
                //     break;
                // case '4': //TU
                //     $lcbeban = "Upah Pungut";
                //     break;
                // case '5': //TU
                //     $lcbeban = "Upah Pungut PBB";
                //     break;
                // case '6': //TU
                //     $lcbeban = "Upah Pungut PBB-KB PKB & BBN-KB ";
                //     break;
                case '7': //TU
                    $lcbeban = "Tambah/Kekurangan Gaji Tunjangan";
                    break;
                case '8': //TU
                    $lcbeban = "Tunjangan Transport";
                    break;
                case '9': //TU
                    $lcbeban = "Tunjangan Lainnya";
                    break;
                default:
                    $lcbeban = "LS";
            }
        } elseif ($beban == '5') {
            $lcbeban = "LS Pihak Ketiga Lainnya";
        } elseif ($beban == '6') {
            switch ($jenis) {
                case '1': //UP
                    $lcbeban = "Rutin (PNS)";
                    break;
                case '2': //GU
                    $lcbeban = "Rutin (Non PNS)";
                    break;
                case '3': //TU
                    $lcbeban = " Pihak ketiga";
                    break;
                // case '4': //TU
                //     $lcbeban = "  Honor Kontrak";
                //     break;
                // case '5': //TU
                //     $lcbeban = " Jasa Pelayanan Kesehatan";
                //     break;
                // case '6': //TU
                //     $lcbeban = " Pihak ketiga";
                //     break;
                // case '7': //TU
                //     $lcbeban = " PNS";
                //     break;
            }
        }
        $header =  DB::table('config_app')
            ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
            ->first();

        $view = view('penatausahaan.pengeluaran.spp_ls.cetak.pernyataan', compact('data', 'beban', 'no_spp', 'tahun_anggaran', 'lcbeban', 'tanpa', 'daerah', 'cari_bendahara', 'header', 'skpd'));
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

    public function cetakSptbLayar(Request $request)
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
        $tahun_anggaran = tahun_anggaran();

        $status = DB::table('trhrka')->select('jns_ang')->where(['kd_skpd' => $kd_skpd, 'status' => '1'])->orderByDesc('tgl_dpa')->first();
        $status_anggaran = $status->jns_ang;
        $data_dpa = DB::table('trhrka')->select('no_dpa', 'tgl_dpa')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $status_anggaran])->first();
        $cari_bendahara = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $kd_sub_kegiatan = DB::table('trdspp')->select('kd_sub_kegiatan')->where('no_spp', $no_spp)->groupBy('kd_sub_kegiatan')->first();
        $sub_kegiatan = $kd_sub_kegiatan->kd_sub_kegiatan == "" ? "" : $kd_sub_kegiatan->kd_sub_kegiatan;
        $jumlah_spp = DB::table('trhspp')->whereRaw("keperluan like '%Tambahan Penghasilan Pegawai%'")->where('no_spp', $no_spp)->count();
        $data = DB::table('trhspp as a')->join('ms_bidang_urusan as b', DB::raw("SUBSTRING(a.kd_skpd,1,4)"), '=', 'b.kd_bidang_urusan')->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])->select('nm_skpd', 'tgl_spp', 'jns_beban', 'nilai')->first();
        $daerah = DB::table('sclient')->where(['kd_skpd' => $kd_skpd])->select('daerah')->first();
        $data = DB::table('trhspp as a')->join('ms_bidang_urusan as b', DB::raw("SUBSTRING(a.kd_skpd,1,4)"), '=', 'b.kd_bidang_urusan')->select('nm_skpd', 'tgl_spp', 'jns_beban', 'nilai', 'bulan')->where(['a.no_spp' => $no_spp, 'a.kd_skpd' => $kd_skpd])->first();
        if ($beban == '4') {
            switch ($data->jns_beban) {
                case '1': //UP
                    $lcbeban = "Gaji dan Tunjangan";
                    break;
                // case '2': //GU
                //     $lcbeban = "Uang Kespeg";
                //     break;
                // case '3': //TU
                //     $lcbeban = "Uang Makan";
                //     break;
                // case '4': //TU
                //     $lcbeban = "Upah Pungut";
                //     break;
                // case '5': //TU
                //     $lcbeban = "Upah Pungut PBB";
                //     break;
                // case '6': //TU
                //     $lcbeban = "Upah Pungut PBB-KB PKB & BBN-KB ";
                //     break;
                case '7': //TU
                    $lcbeban = "Tambah/Kekurangan Gaji Tunjangan";
                    break;
                case '8': //TU
                    $lcbeban = "Tunjangan Transport";
                    break;
                case '9': //TU
                    $lcbeban = "Tunjangan Lainnya";
                    break;
                default:
                    $lcbeban = "LS";
            }
        } elseif ($beban == '5') {
            $lcbeban = '';
        } elseif ($beban == '6') {
            switch ($data->jns_beban) {
                case '1': //UP
                    $lcbeban = "Rutin PNS";
                    break;
                case '2': //GU
                    $lcbeban = "Rutin Non PNS";
                    break;
                case '3': //TU
                    $lcbeban = "Pihak Ketiga";
                    break;
                default:
                    $lcbeban = "LS";
            }
        }
        $header =  DB::table('config_app')
            ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
            ->first();
        $skpd = DB::table('ms_skpd')
            ->select('nm_skpd')
            ->where(['kd_skpd' => $kd_skpd])
            ->first();

        $view = view('penatausahaan.pengeluaran.spp_ls.cetak.sptb', compact('tahun_anggaran', 'no_spp', 'beban', 'lcbeban', 'data', 'cari_bendahara', 'daerah', 'tanpa', 'kd_skpd', 'data_dpa', 'header', 'skpd'));
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

    public function cetakSpp77Layar(Request $request)
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
        $tahun_anggaran = tahun_anggaran();

        $skpd = DB::table('ms_skpd')->select('nm_skpd', 'npwp')->where(['kd_skpd' => $kd_skpd])->first();
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        $cari_pptk = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pptk, 'kd_skpd' => $kd_skpd, 'kode' => 'PPTK'])->first();
        $cari_pa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
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
        //sini vin
        $data = DB::table('trhspp as a')->join('trdspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd])->select('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'a.bank', 'no_rek', 'keperluan', 'a.no_spd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', DB::raw("SUM(b.nilai) as nilaispp"))->groupBy('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'a.bank', 'no_rek', 'keperluan', 'a.no_spd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan')->first();
        $bank = DB::table('ms_bank')->select('nama')->where(['kode' => $data->bank])->first();
        $tglspd = DB::table('trhspd')->select('tgl_spd')->where(['no_spd' => $data->no_spd])->first();
        $nilaispd = DB::table('trhspp')->select('nilai')->where(['no_spp' => $no_spp])->first();
        if ($beban == 1 || $beban == 2) {
            $kd_sub_kegiatan1 = '';
            $nm_sub_kegiatan1 = '';
        } else {
            $kd_sub_kegiatan1 = $data->kd_sub_kegiatan;
            $nm_sub_kegiatan1 = $data->nm_sub_kegiatan;
        }
        $dataspd = DB::table('trhspd')->select('no_spd', 'tgl_spd', 'total','bulan_awal')->whereRaw("LEFT(kd_skpd,22) = LEFT('$kd_skpd',22)")->orderBy('bulan_awal', 'asc')->get();
        $datasp2d = DB::table('trhsp2d')->select('no_sp2d', 'tgl_sp2d', 'nilai as total')->where(['kd_skpd' => $kd_skpd, 'jns_spp' => '6'])->orderBy('no_sp2d','asc')->get();
        $header =  DB::table('config_app')
            ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
            ->first();

        $view = view('penatausahaan.pengeluaran.spp_ls.cetak.spp77', compact('no_spp', 'jenisspp', 'jenis_spp', 'skpd', 'kd_sub_kegiatan1', 'nm_sub_kegiatan1', 'cari_bendahara', 'cari_pptk', 'cari_pa', 'bank', 'tglspd', 'data', 'nilaispd', 'dataspd', 'datasp2d', 'sub_kegiatan', 'tanpa', 'header'));
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

    public function cetakRincian77Layar(Request $request)
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
        $tahun_anggaran = tahun_anggaran();

        $spp = DB::table('trhspp')->select('tgl_spp')->where(['no_spp' => $no_spp])->first();
        $cari_bendahara = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->first();
        $cari_pa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->first();
        $kd_sub_kegiatan = DB::table('trdspp')->select('kd_sub_kegiatan')->where(['no_spp' => $no_spp])->groupBy('kd_sub_kegiatan')->first();
        $sub_kegiatan = $kd_sub_kegiatan->kd_sub_kegiatan;

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
        $nama_kegiatan = DB::table('ms_kegiatan')->select('nm_kegiatan')->where(['kd_kegiatan' => $kd_kegiatan])->first();
        $data_spp_rinci = DB::table('trhspp as a')->join('trdspp as b', function ($join) {
            $join->on('a.no_spp', '=', 'b.no_spp');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.no_spp' => $no_spp, 'b.kd_skpd' => $kd_skpd, 'b.kd_sub_kegiatan' => $data_spp->kd_sub_kegiatan])->groupBy('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'kd_rek6', 'nm_rek6')->select('a.no_spp', 'a.tgl_spp', 'b.kd_skpd', 'b.nm_skpd', 'b.kd_sub_kegiatan', 'b.nm_sub_kegiatan', 'kd_rek6', 'nm_rek6', DB::raw("SUM(b.nilai) as nilaispp"))->get();
        $view = view('penatausahaan.pengeluaran.spp_ls.cetak.rincian77', compact('jenisspp', 'no_spp', 'tahun_anggaran', 'data_spp', 'nama_kegiatan', 'data_spp_rinci', 'spp', 'cari_bendahara', 'sub_kegiatan', 'tanpa', 'cari_pa'));
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

    public function batalSppLs(Request $request)
    {
        $no_spp = $request->no_spp;
        $keterangan = $request->keterangan;
        $beban = $request->beban;
        $nama = Auth::user()->nama;
        $waktu_ubah = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            DB::table('trhspp')->where(['no_spp' => $no_spp])->update([
                'sp2d_batal' => '1',
                'ket_batal' => $keterangan,
                'user_batal' => $nama,
                'tgl_batal' => $waktu_ubah
            ]);
            if ($beban == '6') {
                $data = DB::table('trhspp')->select('no_tagih')->where(['no_spp' => $no_spp])->first();
                if ($data->no_tagih) {
                    DB::table('trhspp')->where(['no_spp' => $no_spp])->update([
                        'no_tagih' => '',
                        'kontrak' => '',
                        'sts_tagih' => '0',
                        'nmrekan' => '',
                        'pimpinan' => ''
                    ]);
                    DB::table('trhtagih')->where(['no_bukti' => $data->no_tagih])->update([
                        'sts_tagih' => '0'
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
                'message' => '0'
            ]);
        }
    }
}
