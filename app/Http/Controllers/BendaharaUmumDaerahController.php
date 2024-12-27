<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class BendaharaUmumDaerahController extends Controller
{
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'bendahara' => DB::table('ms_ttd')
                ->where(['kd_skpd' => $kd_skpd])
                ->whereIn('kode', ['BK', 'BPP'])
                ->orderBy('nip')
                ->orderBy('nama')
                ->get(),
            'pa_kpa' => DB::table('ms_ttd')->whereIn('kode', ['PA', 'KPA'])->orderBy('nama')->get(),
            'data_skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')->where('kd_skpd', $kd_skpd)->first(),
            'jns_anggaran' => jenis_anggaran(),
            'daftar_skpd' => DB::table('ms_skpd')->orderBy('kd_skpd')->get(),
            'daftar_pengirim' => DB::table('ms_pengirim')
                ->selectRaw("kd_pengirim,nm_pengirim,kd_skpd")
                // ->orderByRaw("cast(kd_pengirim as int)")
                ->orderByRaw("kd_pengirim")
                ->get(),
            'daftar_wilayah' => DB::table('ms_wilayah')->selectRaw("kd_wilayah,nm_wilayah")->orderByRaw("cast(kd_wilayah as int)")->get(),
            'bud' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->whereIn('kode', ['BUD', 'PA'])->get(),
            'ppkd' => DB::table('ms_ttd')->select('nip', 'nama', 'jabatan')->where('kd_skpd', '5.02.0.00.0.00.02.0000')->whereIn('kode', ['BUD', 'KPA'])->get(),
            'daftar_rekening' => DB::table('trdrka')->select('kd_rek6', 'nm_rek6')->groupBy('kd_rek6', 'nm_rek6')->get(),
            'daftar_org' => DB::table('ms_organisasi')
                ->select('kd_org', 'nm_org')
                ->get(),
            'daftar_anggaran' => DB::table('tb_status_anggaran')
                ->where(['status_aktif' => '1'])
                ->get()
        ];
        return view('bud.laporan_bendahara.index')->with($data);
    }

    public function realisasiPendapatan(Request $request)
    {
        $skpd_global = Auth::user()->kd_skpd;
        $pilihan = $request->pilihan;
        $periode = $request->periode;
        $anggaran = $request->anggaran;
        $jenis = $request->jenis;
        $ttd = $request->ttd;
        $tgl_ttd = $request->tgl_ttd;
        $kd_skpd = $request->kd_skpd;
        $kd_unit = $request->kd_unit;
        $spasi = $request->spasi;
        $jenis_print = $request->jenis_print;
        // dd ($spasi);

        // if ($ttd) {
        //     $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD', 'PA'])->first();
        // } else {
        //     $tanda_tangan = null;
        // }

        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        if ($pilihan == '1') {
            $daftar_realisasi = DB::select("SELECT * FROM penerimaan_kasda_new(?,?) WHERE LEFT(kd_rek,1)='4' AND  len(kd_rek)<=? and left(kd_rek,6)!='410416' ORDER BY urut1,urut2", [$periode, $anggaran, $jenis]);
        } else if ($pilihan == '2') {
            $daftar_realisasi = DB::select("SELECT * FROM penerimaan_kasda_new_skpd(?,?,?) WHERE LEFT(kd_rek,1)='4' AND  len(kd_rek)<=? and left(kd_rek,6)!='410416' ORDER BY urut1,urut2", [$periode, $anggaran, $kd_skpd, $jenis]);
        } else if ($pilihan == '3') {
            $daftar_realisasi  = DB::select("SELECT * FROM penerimaan_kasda_new_unit(?,?,?) WHERE LEFT(kd_rek,1)='4' AND len(kd_rek)<=? and left(kd_rek,6)!='410416' ORDER BY urut1,urut2", [$periode, $anggaran, $kd_unit, $jenis]);
        }

        if ($pilihan == '1') {
            $skpd = DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $skpd_global])->first();
        } elseif ($pilihan == '2') {
            $skpd = DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first();
        } elseif ($pilihan == '3') {
            $skpd = DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_unit])->first();
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'tanda_tangan' => $tanda_tangan,
            'daftar_realisasi' => $daftar_realisasi,
            'skpd' => $skpd,
            'tanggal' => $tgl_ttd,
            'spasi' => $spasi,
            'periode' => $periode
        ];

        $judul = 'REALISASI_PENDPATAN';

        $view = view('bud.laporan_bendahara.cetak.realisasi_pendapatan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename= $judul.xls");
            echo $view;
        }
    }

    public function pembantuPenerimaan(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        if ($pilihan == '1') {
            $where = "b.tgl_kas= '$tgl'";
            $where2 = "tanggal = '$tgl'";
            $where3 = "b.tgl_kas<'$tgl'";
            $where4 = "c.tanggal<'$tgl'";
            $where5 = "tanggal<'$tgl'";
        } elseif ($pilihan == '2') {
            $where = "b.tgl_kas BETWEEN '$periode1' AND '$periode2'";
            $where2 = "tanggal BETWEEN '$periode1' AND '$periode2'";
            $where3 = "b.tgl_kas<'$periode1'";
            $where4 = "c.tanggal<'$periode1'";
            $where5 = "tanggal<'$periode1'";
        }

        $penerimaan = DB::select(
            "SELECT * FROM
            (
                SELECT
                    1 AS urut, '' AS no_sts, '' AS kd_skpd, f.nm_skpd, '' AS kd_sub_kegiatan,
                    '' AS kd_rek6, Cast(b.no_kas as varchar) as no_kas, '' AS tgl_kas, ISNULL( e.nm_pengirim, '' ) AS nm_pengirim,
                    '' AS nm_rek6, 0 AS rupiah
                FROM trdkasin_ppkd a
                INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd= b.kd_skpd
                INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
                LEFT JOIN ms_pengirim e ON a.sumber = e.kd_pengirim AND b.kd_skpd= e.kd_skpd
                INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
                WHERE $where AND a.kd_skpd != '4.02.02.02'
                    AND LEFT ( a.kd_rek6, 4 ) NOT IN ( '4101', '4301', '4104', '4201' ) AND a.kd_rek6 NOT IN ( '420101040001' )
                GROUP BY b.no_kas, nm_pengirim, f.nm_skpd
                UNION ALL

                SELECT
                    2 AS urut, b.no_sts, a.kd_skpd, '' AS nm_skpd, a.kd_sub_kegiatan,
                    a.kd_rek6,  Cast(b.no_kas as varchar) as no_kas, b.tgl_kas, '' AS nm_pengirim, c.nm_rek6, a.rupiah
                FROM trdkasin_ppkd a
                INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd= b.kd_skpd
                INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
                LEFT JOIN ms_pengirim e ON a.sumber = e.kd_pengirim AND b.kd_skpd= e.kd_skpd
                WHERE $where AND a.kd_skpd != '4.02.02.02'
                    AND LEFT ( a.kd_rek6, 4 ) NOT IN ( '4101', '4301', '4104', '4201' ) AND a.kd_rek6 NOT IN ( '420101040001' )
                UNION ALL

                SELECT
                    1 AS urut, '' AS no_sts, '' AS kd_skpd, f.nm_skpd, '' AS kd_sub_kegiatan,
                    '' AS kd_rek6,  Cast(b.no_kas as varchar) as no_kas, '' AS tgl_kas, '' AS nm_pengirim, '' AS nm_rek6, 0 AS rupiah
                FROM trdkasin_ppkd a
                INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas
                AND a.kd_skpd= b.kd_skpd
                INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
                LEFT JOIN ms_pengirim e ON a.sumber = e.kd_pengirim
                AND b.kd_skpd= e.kd_skpd
                INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
                WHERE $where AND a.kd_skpd != '4.02.02.02'
                    AND LEFT ( a.kd_rek6, 4 ) IN ( '4101', '4301', '4104', '4201' ) AND a.kd_rek6!= '410416010001'
                    AND a.kd_rek6 NOT IN ( '420101040001' )
                GROUP BY b.no_kas, f.nm_skpd
                UNION ALL

                SELECT
                    2 AS urut, b.no_sts, a.kd_skpd, '' AS nm_skpd, a.kd_sub_kegiatan, a.kd_rek6,
                    Cast(b.no_kas as varchar) as no_kas, b.tgl_kas, ISNULL( e.nm_pengirim, '' ) AS nm_pengirim, c.nm_rek6, a.rupiah
                FROM trdkasin_ppkd a
                INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd= b.kd_skpd
                INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
                LEFT JOIN ms_pengirim e ON a.sumber = e.kd_pengirim AND b.kd_skpd= e.kd_skpd
                WHERE $where AND a.kd_skpd != '4.02.02.02'
                    AND LEFT ( a.kd_rek6, 4 ) IN ( '4101', '4301', '4104', '4201' )
                    AND a.kd_rek6!= '410416010001' AND a.kd_rek6 NOT IN ( '420101040001' )
                UNION ALL

                SELECT
                    1 AS urut, '' AS no_sts, '' AS kd_skpd, f.nm_skpd, '' AS kd_sub_kegiatan,
                    '' AS kd_rek6, Cast(b.no_kas as varchar) as no_kas, '' AS tgl_kas, '' AS nm_pengirim, '' AS nm_rek6, 0 AS rupiah
                FROM trdkasin_ppkd a
                INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd= b.kd_skpd
                INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
                INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
                WHERE $where AND a.kd_skpd = '4.02.02.02'
                GROUP BY b.no_kas, f.nm_skpd
                UNION ALL

                SELECT
                    2 AS urut, b.no_sts, a.kd_skpd, '' AS nm_skpd, a.kd_sub_kegiatan, a.kd_rek6,
                    Cast(b.no_kas as varchar) as no_kas, b.tgl_kas, '' AS nm_pengirim, b.keterangan AS nm_rek6, a.rupiah
                FROM trdkasin_ppkd a
                INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd= b.kd_skpd
                INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
                WHERE $where AND a.kd_skpd = '4.02.02.02'
                UNION ALL

                SELECT
                    1 AS urut, '' AS no_sts, '' AS kd_skpd, nm_skpd, '' AS kd_sub_kegiatan,
                    '' AS kd_rek6, [no] AS no_kas, '' AS tgl_kas, '' AS nm_pengirim, '' AS nm_rek6, 0 rupiah
                FROM trkasout_ppkd
                WHERE $where2
                UNION ALL

                SELECT
                    2 AS urut, [no] AS no_sts, kd_skpd, '' AS nm_skpd, kd_sub_kegiatan,
                    kd_rek AS kd_rek6, [no] AS no_kas, [tanggal] AS tgl_kas, '' AS nm_pengirim,
                    keterangan + ' ' + nm_rek nm_rek6, nilai AS rupiah
                FROM trkasout_ppkd
                WHERE $where2
                UNION ALL

                SELECT
                    1 AS urut, '' no_sts, '' kd_skpd,
                    ( SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = '4.02.02.02' ) nm_skpd,
                    '' kd_sub_kegiatan, '' kd_rek6, cast(nomor as VARCHAR) AS no_kas, '' tgl_kas,
                    '' nm_pengirim, '' nm_rek6, 0 rupiah
                FROM penerimaan_non_sp2d
                WHERE $where2
                UNION ALL

                SELECT
                    2 AS urut, '[nomor]' AS no_sts, '3.13.01.17' kd_skpd, '' nm_skpd, '' kd_sub_kegiatan,
                    '' kd_rek6, cast(nomor as VARCHAR) AS no_kas, [tanggal] tgl_kas, '' nm_pengirim, keterangan nm_rek6, nilai AS rupiah
                FROM penerimaan_non_sp2d
                WHERE $where2
            ) a ORDER BY no_kas, urut"
        );

        $total_penerimaan = Collect(DB::select(
            "SELECT
                SUM( nilai ) AS nilai
            FROM
            (
                SELECT
                    SUM( a.rupiah ) AS nilai
                FROM trdkasin_ppkd a
                INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd= b.kd_skpd
                INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
                LEFT JOIN ms_pengirim e ON a.sumber = e.kd_pengirim
                WHERE $where3 AND a.kd_skpd != '4.02.02.02'
                    AND LEFT ( a.kd_rek6, 4 ) NOT IN ( '4101', '4301', '4104', '4201' )
                    AND a.kd_rek6 NOT IN ( '420101040001' )
                UNION ALL

                SELECT
                    SUM( a.rupiah )
                FROM trdkasin_ppkd a
                INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd= b.kd_skpd
                INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
                LEFT JOIN ms_pengirim e ON a.sumber = e.kd_pengirim
                WHERE $where3 AND a.kd_skpd != '4.02.02.02'
                    AND LEFT ( a.kd_rek6, 4 ) IN ( '4101', '4301', '4104', '4201' )
                    AND a.kd_rek6!= '410416010001' AND a.kd_rek6 NOT IN ( '420101040001' )
                UNION ALL

                SELECT
                    SUM( a.rupiah )
                FROM trdkasin_ppkd a
                INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd= b.kd_skpd
                INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
                WHERE $where3 AND a.kd_skpd = '4.02.02.02'
                UNION ALL

                SELECT
                    SUM( nilai ) AS rupiah
                FROM trkasout_ppkd c
                WHERE $where4
                UNION ALL

                SELECT
                    SUM( nilai ) AS rupiah
                FROM penerimaan_non_sp2d
                WHERE $where5
            ) a"
        ))->first();

        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanda_tangan' => $tanda_tangan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_penerimaan' => $penerimaan,
            'spasi' => $spasi,
            'total_penerimaan' => $total_penerimaan->nilai,
        ];

        $judul = 'BUKU KAS PEMBANTU PENERIMAAN';

        $view = view('bud.laporan_bendahara.cetak.pembantu_penerimaan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
            ->setPaper('legal')
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename= $judul.xls");
            echo $view;
        }

    }

    public function bkuTanpaTanggal(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $no_urut = $request->no_urut;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;
        $tahun = tahun_anggaran();

        if ($pilihan == '1') {
            $where = "a.tgl_kas=?";
            $where2 = "a.tgl_kas_bud = ?";
            $where3 = "x.tanggal = ?";
            $where4 = "w.tanggal = ?";
            $where5 = "a.tgl_kas<?";
            $where6 = "a.tgl_kas_bud<?";
            $where7 = "x.tanggal < ?";
            $where8 = "w.tanggal < ?";
            $where9 = "a.tgl_kas < ?";
        } elseif ($pilihan == '2') {
            $where = "a.tgl_kas BETWEEN ? AND ?";
            $where2 = "a.tgl_kas_bud BETWEEN ? AND ?";
            $where3 = "x.tanggal between ? AND ?";
            $where4 = "w.tanggal between ? AND ?";
            $where5 = "a.tgl_kas<?";
            $where6 = "a.tgl_kas_bud<?";
            $where7 = "x.tanggal < ?";
            $where8 = "w.tanggal < ?";
            $where9 = "a.tgl_kas < ?";
        }

        if ($tgl == $tahun . '-01-01') {
            $saldo = DB::table('buku_kas')->selectRaw("'4' kd_rek, 'SALDO AWAL' nama, nilai , 1 jenis");
        }

        $bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->leftJoin('ms_rek3 as c', function ($join) {
                $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
            })
            ->selectRaw("LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN ('4') and  b.kd_rek6 not in ('420101040001','420101040002','420101040003','410416010001','410409010001')")
            ->where(function ($query) use ($pilihan, $where, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where, [$periode1, $periode2]);
                }
            })
            ->groupByRaw("LEFT(b.kd_rek6,4),c.nm_rek3");

        $bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->leftJoin('ms_rek3 as c', function ($join) {
                $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
            })
            ->selectRaw("LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("b.kd_rek6 in ('410409010001') and b.sumber<>'y'")
            ->where(function ($query) use ($pilihan, $where, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where, [$periode1, $periode2]);
                }
            })
            ->groupByRaw("LEFT(b.kd_rek6,4),c.nm_rek3")
            ->unionAll($bku1);

        $bku3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("LEFT(b.kd_rek6,4) as kd_rek, 'UYHD' as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("b.kd_rek6 in ('410409010001','410412010010') and a.keterangan like '%(UYHD)%'")
            ->where(function ($query) use ($pilihan, $where, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where, [$periode1, $periode2]);
                }
            })
            ->groupByRaw("LEFT(b.kd_rek6,4)")
            ->unionAll($bku2);

        $bku4 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'414' as kd_rek, 'LAIN-LAIN PENDAPATAN ASLI DAERAH YANG SAH' as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus=?", ['3'])
            ->where(function ($query) use ($pilihan, $where, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where, [$periode1, $periode2]);
                }
            })
            ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,1)")
            ->unionAll($bku3);

        $bku5 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("510 as kd_rek, 'CONTRA POST' as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN ('5','1','2') and pot_khusus<>?", ['3'])
            ->where(function ($query) use ($pilihan, $where, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku4);

        $bku6 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("'5101' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud = ? AND a.jns_spp = ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)", ['1', '4'])
            ->where(function ($query) use ($pilihan, $where2, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where2, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where2, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku5);

        $bku7 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("'512' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud = ? AND a.jns_spp != ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)", ['1', '4'])
            ->where(function ($query) use ($pilihan, $where2, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where2, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where2, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku6);

        $bku8 = DB::table('pengeluaran_non_sp2d as x')
            ->selectRaw("'513' kd_rek, 'PENGELUARAN NON SP2D' nama,isnull(SUM(x.nilai), 0) AS nilai, 2 jenis")
            ->where(function ($query) use ($pilihan, $where3, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where3, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where3, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku7);

        $bku9 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'513' kd_rek, 'RESTITUSI' nama,isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis")
            ->where('a.jns_trans', '3')
            ->where(function ($query) use ($pilihan, $where, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku8);

        $bku10 = DB::table('trkasout_ppkd as w')
            ->selectRaw("'514' as kd_rek,'KOREKSI' nama,isnull(SUM(w.nilai),0) as nilai,1 jenis")
            ->where(function ($query) use ($pilihan, $where4, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where4, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where4, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku9);

        $bku11 = DB::table('tkoreksi_penerimaan as w')
            ->selectRaw("'517' as kd_rek,'KOREKSI PENERIMAAN' nama,isnull(SUM(w.nilai),0) as nilai,1 jenis")
            ->where(function ($query) use ($pilihan, $where4, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where4, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where4, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku10);

        $bku12 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("'515' AS kd_rek,'DEPOSITO' nama,isnull(SUM(w.nilai), 0) AS nilai,1 jenis")
            ->where('w.jenis', '1')
            ->where(function ($query) use ($pilihan, $where4, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where4, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where4, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku11);

        $bku13 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("'516' AS kd_rek,'PENERIMAAN NON PENDAPATAN' nama,isnull(SUM(w.nilai), 0) AS nilai,1 jenis")
            ->where('w.jenis', '2')
            ->where(function ($query) use ($pilihan, $where4, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where4, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where4, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku12);

        $bku14 = DB::table('trkoreksi_pengeluaran as w')
            ->selectRaw("'523' as kd_rek,'KOREKSI PENGELUARAN' nama,isnull(SUM(w.nilai),0) as nilai,2 jenis")
            ->where(function ($query) use ($pilihan, $where4, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->whereRaw($where4, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where4, [$periode1, $periode2]);
                }
            })
            ->unionAll($bku13);

        if (isset($saldo)) {
            $bku15 = $bku14->unionAll($saldo);
        } else {
            $bku15 = $bku14;
        }

        $bku = DB::table(DB::raw("({$bku15->toSql()}) AS sub"))
            ->selectRaw("kd_rek, nama, sum(nilai) nilai, jenis")
            ->mergeBindings($bku15)
            ->groupByRaw("kd_rek, nama, jenis")
            ->orderBy('kd_rek')
            ->get();

        $total_bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->leftJoin('ms_rek3 as c', function ($join) {
                $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
            })
            ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN ('4') and b.kd_rek6 not in ('420101040001','420101040002','420101040003','410416010001')")
            ->where(function ($query) use ($pilihan, $where5, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where5, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where5, [$periode1]);
                }
            })
            ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,4),c.nm_rek3");

        $total_bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN ('5','1','2')")
            ->where(function ($query) use ($pilihan, $where5, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where5, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where5, [$periode1]);
                }
            })
            ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,1)")->unionAll($total_bku1);

        $total_bku3 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud =? AND  a.jns_spp !=? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)", ['1', '4'])
            ->where(function ($query) use ($pilihan, $where6, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where6, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where6, [$periode1]);
                }
            })
            ->groupByRaw("a.tgl_kas_bud")
            ->unionAll($total_bku2);

        $total_bku4 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud =? AND a.jns_spp =? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)", ['1', '4'])
            ->where(function ($query) use ($pilihan, $where6, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where6, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where6, [$periode1]);
                }
            })
            ->groupByRaw("a.tgl_kas_bud")
            ->unionAll($total_bku3);

        $total_bku5 = DB::table('pengeluaran_non_sp2d as x')
            ->selectRaw("x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,isnull(SUM(x.nilai), 0) AS nilai, 2 jenis")
            ->where(function ($query) use ($pilihan, $where7, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where7, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where7, [$periode1]);
                }
            })
            ->groupByRaw("x.tanggal")
            ->unionAll($total_bku4);

        $total_bku6 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis")
            ->where('a.jns_trans', '3')
            ->where(function ($query) use ($pilihan, $where9, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where9, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where9, [$periode1]);
                }
            })
            ->groupByRaw("a.tgl_kas")
            ->unionAll($total_bku5);

        $total_bku7 = DB::table('trkasout_ppkd as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI ' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->where(function ($query) use ($pilihan, $where8, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where8, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where8, [$periode1]);
                }
            })
            ->groupByRaw("w.tanggal,w.kd_rek")
            ->unionAll($total_bku6);

        $total_bku8 = DB::table('tkoreksi_penerimaan as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->where(function ($query) use ($pilihan, $where8, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where8, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where8, [$periode1]);
                }
            })
            ->groupByRaw("w.tanggal")
            ->unionAll($total_bku7);

        $total_bku9 = DB::table('trkoreksi_pengeluaran as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENGELUARAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 2 jenis")
            ->where(function ($query) use ($pilihan, $where8, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where8, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where8, [$periode1]);
                }
            })
            ->groupByRaw("w.tanggal,w.kd_rek")
            ->unionAll($total_bku8);

        $total_bku10 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'DEPOSITO' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->where('w.jenis', '1')
            ->where(function ($query) use ($pilihan, $where8, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where8, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where8, [$periode1]);
                }
            })
            ->groupByRaw("w.tanggal")
            ->unionAll($total_bku9);

        $total_bku11 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'PENERIMAAN NON PENDAPATAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->where('w.jenis', '2')
            ->where(function ($query) use ($pilihan, $where8, $tgl, $periode1) {
                if ($pilihan == '1') {
                    $query->whereRaw($where8, [$tgl]);
                } elseif ($pilihan == '2') {
                    $query->whereRaw($where8, [$periode1]);
                }
            })
            ->groupByRaw("w.tanggal")
            ->unionAll($total_bku10);

        $total_bku = DB::table(DB::raw("({$total_bku11->toSql()}) AS sub"))
            ->selectRaw("SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl")
            ->mergeBindings($total_bku11)
            ->first();

        $total_saldo_awal = DB::table('buku_kas')->select('nilai')->where(['nomor' => '0'])->first();
        if ($tgl == "2021-01-01") {
            $saldo_awal = 0;
        } else {
            $saldo_awal = $total_saldo_awal->nilai;
        }

        if ($ttd) {
            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->first();
        } else {
            $tanda_tangan = null;
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_bku' => $bku,
            'total_bku' => $total_bku,
            'saldo_awal' => $saldo_awal,
            'tanda_tangan' => $tanda_tangan
        ];

        $view = view('bud.laporan_bendahara.cetak.bku_tanpa_tanggal')->with($data);

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

    public function bkuTanpaTanggal1(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $st_rek = $request->st_rek;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $no_urut = $request->no_urut;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;
        $tahun = tahun_anggaran();
        //pertanggal
        //dd($periode2);
        //dd($st_rek);

        if($st_rek == '1'){
            $wherestrenk = "";
            $wherestrenk1 = "";
        }else if($st_rek == '3001006966'){
            $wherestrenk = "AND a.rek_bank = ? ";
            $wherestrenk1 = "AND b.rek_bank = ? ";
        }else{
            $wherestrenk = "AND a.rek_bank = ? ";
            $wherestrenk1 = "AND b.rek_bank = ? ";
        }

        if ($pilihan == '1') {
            $where ="AND a.tgl_kas = ?";
			$where2 ="AND a.tgl_kas_bud = ?";
			$where3 ="x.tanggal = ?";
			$where4 ="w.tanggal = ?";
			$where5 ="AND a.tgl_kas<?";
			$where6 ="AND a.tgl_kas_bud<?";
			$where7 ="x.tanggal < ?";
			$where8 ="w.tanggal < ?";
			$where9 ="AND a.tgl_kas < ?";
            $whereBB ="AND a.tgl_bukti < ?";

        } elseif ($pilihan == '2') {
            $where = "AND a.tgl_kas BETWEEN ? AND ?";
            $where2 = "AND a.tgl_kas_bud BETWEEN ? AND ?";
            $where3 = "x.tanggal between ? AND ?";
            $where4 = "w.tanggal between ? AND ?";
            $where5 = "AND a.tgl_kas<?";
            $where6 = "AND a.tgl_kas_bud<?";
            $where7 = "x.tanggal < ?";
            $where8 = "w.tanggal < ?";
            $where9 = "AND a.tgl_kas < ?";
            $whereBB ="AND a.tgl_bukti between ? AND ? ";
        }

        if ($pilihan == '2'){
            if($st_rek != '1'){

                $data_bku = DB::select("SELECT kd_rek, nama, sum(nilai) nilai, jenis from(

                    SELECT LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama
                        ,SUM(rupiah) as nilai, 1 jenis
                        FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                        LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,4)=c.kd_rek3
                        WHERE LEFT(b.kd_rek6,1) IN ('4') $where $wherestrenk
                        GROUP BY LEFT(b.kd_rek6,4),c.nm_rek3

                        UNION ALL

                    SELECT  '414' as kd_rek, 'LAIN-LAIN PENDAPATAN ASLI DAERAH YANG SAH' as nama
                        ,SUM(rupiah) as nilai, 1 jenis
                        FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                        WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus=3 $where $wherestrenk
                            GROUP BY a.tgl_kas,LEFT(b.kd_rek6,1)

                        UNION ALL

                    SELECT  510 as kd_rek, 'CONTRA POST' as nama
                        ,SUM(rupiah) as nilai, 1 jenis
                        FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                        WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus<>3 $where $wherestrenk

                        UNION ALL

                        SELECT
					515 AS kd_rek,
					'SETOR SISA UYHD TAHUN LALU' AS nama,
					SUM ( a.nilai ) AS nilai,
					1 jenis
				FROM
					TRHOUTLAIN a left join trhkasin_ppkd b on a.kd_skpd = b.kd_skpd and a.no_bukti = b.no_sts
				WHERE
					a.status = '1' $whereBB $wherestrenk1
					UNION ALL

                    SELECT '511' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,
                        isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                        FROM trhsp2d a
                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                        INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                        WHERE a.status_bud = '1' AND a.jns_spp = '4'
                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                        $where2

                        UNION ALL

                    SELECT '512' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,
                        isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                        FROM
                        trhsp2d a
                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                        INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                        WHERE a.status_bud = '1' AND a.jns_spp != '4'
                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                        $where2

                        UNION ALL

                    SELECT '513' kd_rek, 'PENGELUARAN NON SP2D' nama,
                        isnull(SUM(x.nilai), 0) AS nilai, 2 jenis
                        FROM
                        pengeluaran_non_sp2d x
                        WHERE $where3

                        UNION ALL

                    SELECT '513' kd_rek, 'RESTITUSI' nama,
                        isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis
                        FROM trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts
                        WHERE a.jns_trans=3 $where

                        UNION ALL

                    SELECT '514' as kd_rek, 'KOREKSI PENERIMAAN' nama,
                        isnull(SUM(w.nilai),0) as nilai,
                        1 jenis
                        FROM
                        trkasout_ppkd w
                        WHERE $where4

                    UNION ALL
                        SELECT '515' AS kd_rek,
                            'DEPOSITO' nama,
                            isnull(SUM(w.nilai), 0) AS nilai,
                            1 jenis
                        FROM
                            penerimaan_non_sp2d w
                        WHERE
                            $where4
                        AND w.jenis='1'
                    UNION ALL
                        SELECT
                            '516' AS kd_rek,
                            'PENERIMAAN NON SP2D' nama,
                            isnull(SUM(w.nilai), 0) AS nilai,
                            1 jenis
                        FROM
                            penerimaan_non_sp2d w
                        WHERE
                            $where4
                        AND w.jenis='2'
                        ) a
                            where a.nilai<>0 group by kd_rek, nama, jenis order by kd_rek",[$periode1,$periode2,$st_rek,$periode1,$periode2,$st_rek,$periode1,$periode2,$st_rek,$periode1,$periode2,$st_rek,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2]);
                } else {

                    $data_bku = DB::select("SELECT kd_rek, nama, sum(nilai) nilai, jenis from(

                        SELECT LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama
                            ,SUM(rupiah) as nilai, 1 jenis
                            FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                            LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,4)=c.kd_rek3
                            WHERE LEFT(b.kd_rek6,1) IN ('4') $where
                            GROUP BY LEFT(b.kd_rek6,4),c.nm_rek3

                            UNION ALL

                        SELECT  '414' as kd_rek, 'LAIN-LAIN PENDAPATAN ASLI DAERAH YANG SAH' as nama
                            ,SUM(rupiah) as nilai, 1 jenis
                            FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                            WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus=3 $where
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,1)

                            UNION ALL

                        SELECT  510 as kd_rek, 'CONTRA POST' as nama
                            ,SUM(rupiah) as nilai, 1 jenis
                            FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                            WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus<>3 $where

                            UNION ALL

                            SELECT
                            515 AS kd_rek,
                            'SETOR SISA UYHD TAHUN LALU' AS nama,
                            SUM ( a.nilai ) AS nilai,
                            1 jenis
                        FROM
                            TRHOUTLAIN a left join trhkasin_ppkd b on a.kd_skpd = b.kd_skpd and a.no_bukti = b.no_sts
                        WHERE
                            a.status = '1' $whereBB
                            UNION ALL

                        SELECT '511' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,
                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                            FROM trhsp2d a
                            INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                            INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                            INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                            WHERE a.status_bud = '1' AND a.jns_spp = '4'
                            AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                            $where2

                            UNION ALL

                        SELECT '512' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,
                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                            FROM
                            trhsp2d a
                            INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                            INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                            INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                            WHERE a.status_bud = '1' AND a.jns_spp != '4'
                            AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                            $where2

                            UNION ALL

                        SELECT '513' kd_rek, 'PENGELUARAN NON SP2D' nama,
                            isnull(SUM(x.nilai), 0) AS nilai, 2 jenis
                            FROM
                            pengeluaran_non_sp2d x
                            WHERE $where3

                            UNION ALL

                        SELECT '513' kd_rek, 'RESTITUSI' nama,
                            isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis
                            FROM trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts
                            WHERE a.jns_trans=3 $where

                            UNION ALL

                        SELECT '514' as kd_rek, 'KOREKSI PENERIMAAN' nama,
                            isnull(SUM(w.nilai),0) as nilai,
                            1 jenis
                            FROM
                            trkasout_ppkd w
                            WHERE $where4

                        UNION ALL
                            SELECT '515' AS kd_rek,
                                'DEPOSITO' nama,
                                isnull(SUM(w.nilai), 0) AS nilai,
                                1 jenis
                            FROM
                                penerimaan_non_sp2d w
                            WHERE
                                $where4
                            AND w.jenis='1'
                        UNION ALL
                            SELECT
                                '516' AS kd_rek,
                                'PENERIMAAN NON SP2D' nama,
                                isnull(SUM(w.nilai), 0) AS nilai,
                                1 jenis
                            FROM
                                penerimaan_non_sp2d w
                            WHERE
                                $where4
                            AND w.jenis='2'
                            ) a
                                where a.nilai<>0 group by kd_rek, nama, jenis order by kd_rek",[$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2]);

                }
                //dd($data_bku);

                if ($st_rek != '1'){
                $total_bku = collect(DB::select("SELECT SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,
                                SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl
                                FROM(
                                SELECT  a.tgl_kas,LEFT(b.kd_rek6,3) as kd_rek, UPPER(c.nm_rek3) as nama
                                ,SUM(rupiah) as nilai, 1 jenis
                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                                LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,3)=c.kd_rek3
                                WHERE LEFT(b.kd_rek6,1) IN ('4') $where5 $wherestrenk
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,3),c.nm_rek3
                                UNION ALL
                                SELECT  a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama
                                ,SUM(rupiah) as nilai, 1 jenis
                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                                WHERE LEFT(b.kd_rek6,1) IN ('5','1') $where5 $wherestrenk
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,1)
                                UNION ALL
                                SELECT
                                a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,
                                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                                        FROM
                                            trhsp2d a
                                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                        INNER JOIN trdspp d ON c.no_spp = d.no_spp 	AND c.kd_skpd = d.kd_skpd
                                        WHERE a.status_bud = '1' AND  a.jns_spp != '4'
                                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                                        $where6
                                GROUP BY a.tgl_kas_bud
                                UNION ALL
                                SELECT
                                a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,
                                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                                        FROM trhsp2d a
                                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                        INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                                        WHERE a.status_bud = '1' AND a.jns_spp = '4'
                                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                                        $where6
                                GROUP BY a.tgl_kas_bud
                                UNION ALL
                                SELECT
                                                    x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,
                                                    isnull(SUM(x.nilai), 0) AS nilai, 2 jenis
                                                FROM
                                                    pengeluaran_non_sp2d x
                                                WHERE
                                                    $where7
                                GROUP BY x.tanggal
                                UNION ALL
                                SELECT
                                                    a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,
                                                    isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis
                                                FROM
                                                    trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts
                                                WHERE a.jns_trans=3
                                                    $where9
                                GROUP BY a.tgl_kas
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    trkasout_ppkd w
                                                WHERE
                                                    $where8

                                GROUP BY w.tanggal,w.kd_rek
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'DEPOSITO' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    penerimaan_non_sp2d w
                                                WHERE
                                                    $where8
                                                AND w.jenis='1'
                                GROUP BY w.tanggal
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'PENERIMAAN NON SP2D' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    penerimaan_non_sp2d w
                                                WHERE
                                                    $where8
                                                AND w.jenis='2'
                                GROUP BY w.tanggal
                                ) a",[$periode1,$st_rek,$periode1,$st_rek,$periode1,$periode1,$periode1,$periode1,$periode1,$periode1,$periode1]))->first();
                } else{
                    $total_bku = collect(DB::select("SELECT SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,
                                SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl
                                FROM(
                                SELECT  a.tgl_kas,LEFT(b.kd_rek6,3) as kd_rek, UPPER(c.nm_rek3) as nama
                                ,SUM(rupiah) as nilai, 1 jenis
                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                                LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,3)=c.kd_rek3
                                WHERE LEFT(b.kd_rek6,1) IN ('4') $where5
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,3),c.nm_rek3
                                UNION ALL
                                SELECT  a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama
                                ,SUM(rupiah) as nilai, 1 jenis
                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                                WHERE LEFT(b.kd_rek6,1) IN ('5','1') $where5
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,1)
                                UNION ALL
                                SELECT
                                a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,
                                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                                        FROM
                                            trhsp2d a
                                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                        INNER JOIN trdspp d ON c.no_spp = d.no_spp 	AND c.kd_skpd = d.kd_skpd
                                        WHERE a.status_bud = '1' AND  a.jns_spp != '4'
                                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                                        $where6
                                GROUP BY a.tgl_kas_bud
                                UNION ALL
                                SELECT
                                a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,
                                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                                        FROM trhsp2d a
                                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                        INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                                        WHERE a.status_bud = '1' AND a.jns_spp = '4'
                                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                                        $where6
                                GROUP BY a.tgl_kas_bud
                                UNION ALL
                                SELECT
                                                    x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,
                                                    isnull(SUM(x.nilai), 0) AS nilai, 2 jenis
                                                FROM
                                                    pengeluaran_non_sp2d x
                                                WHERE
                                                    $where7
                                GROUP BY x.tanggal
                                UNION ALL
                                SELECT
                                                    a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,
                                                    isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis
                                                FROM
                                                    trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts
                                                WHERE a.jns_trans=3
                                                    $where9
                                GROUP BY a.tgl_kas
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    trkasout_ppkd w
                                                WHERE
                                                    $where8

                                GROUP BY w.tanggal,w.kd_rek
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'DEPOSITO' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    penerimaan_non_sp2d w
                                                WHERE
                                                    $where8
                                                AND w.jenis='1'
                                GROUP BY w.tanggal
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'PENERIMAAN NON SP2D' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    penerimaan_non_sp2d w
                                                WHERE
                                                    $where8
                                                AND w.jenis='2'
                                GROUP BY w.tanggal
                                ) a",[$periode1,$periode1,$periode1,$periode1,$periode1,$periode1,$periode1,$periode1,$periode1]))->first();
                }
        } else {
            if($st_rek != '1'){

                $data_bku = DB::select("SELECT kd_rek, nama, sum(nilai) nilai, jenis from(

                    SELECT LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama
                        ,SUM(rupiah) as nilai, 1 jenis
                        FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                        LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,4)=c.kd_rek3
                        WHERE LEFT(b.kd_rek6,1) IN ('4') $where $wherestrenk
                        GROUP BY LEFT(b.kd_rek6,4),c.nm_rek3

                        UNION ALL

                    SELECT  '414' as kd_rek, 'LAIN-LAIN PENDAPATAN ASLI DAERAH YANG SAH' as nama
                        ,SUM(rupiah) as nilai, 1 jenis
                        FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                        WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus=3 $where $wherestrenk
                            GROUP BY a.tgl_kas,LEFT(b.kd_rek6,1)

                        UNION ALL

                    SELECT  510 as kd_rek, 'CONTRA POST' as nama
                        ,SUM(rupiah) as nilai, 1 jenis
                        FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                        WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus<>3 $where $wherestrenk

                        UNION ALL
                        SELECT
                            515 AS kd_rek,
                            'SETOR SISA UYHD TAHUN LALU' AS nama,
                            SUM ( a.nilai ) AS nilai,
                            1 jenis
                        FROM
                            TRHOUTLAIN a left join trhkasin_ppkd b on a.kd_skpd = b.kd_skpd and a.no_bukti = b.no_sts
                        WHERE
                            a.status = '1' $whereBB $wherestrenk1
                            UNION ALL

                    SELECT '511' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,
                        isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                        FROM trhsp2d a
                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                        INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                        WHERE a.status_bud = '1' AND a.jns_spp = '4'
                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                        $where2

                        UNION ALL

                    SELECT '512' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,
                        isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                        FROM
                        trhsp2d a
                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                        INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                        WHERE a.status_bud = '1' AND a.jns_spp != '4'
                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                        $where2

                        UNION ALL

                    SELECT '513' kd_rek, 'PENGELUARAN NON SP2D' nama,
                        isnull(SUM(x.nilai), 0) AS nilai, 2 jenis
                        FROM
                        pengeluaran_non_sp2d x
                        WHERE $where3

                        UNION ALL

                    SELECT '513' kd_rek, 'RESTITUSI' nama,
                        isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis
                        FROM trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts
                        WHERE a.jns_trans=3 $where

                        UNION ALL

                    SELECT '514' as kd_rek, 'KOREKSI PENERIMAAN' nama,
                        isnull(SUM(w.nilai),0) as nilai,
                        1 jenis
                        FROM
                        trkasout_ppkd w
                        WHERE $where4

                    UNION ALL
                        SELECT '515' AS kd_rek,
                            'DEPOSITO' nama,
                            isnull(SUM(w.nilai), 0) AS nilai,
                            1 jenis
                        FROM
                            penerimaan_non_sp2d w
                        WHERE
                            $where4
                        AND w.jenis='1'
                    UNION ALL
                        SELECT
                            '516' AS kd_rek,
                            'PENERIMAAN NON SP2D' nama,
                            isnull(SUM(w.nilai), 0) AS nilai,
                            1 jenis
                        FROM
                            penerimaan_non_sp2d w
                        WHERE
                            $where4
                        AND w.jenis='2'
                        ) a
                            where a.nilai<>0 group by kd_rek, nama, jenis order by kd_rek",[$tgl,$st_rek,$tgl,$st_rek,$tgl,$st_rek,$tgl,$st_rek,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl]);
                } else {

                    $data_bku = DB::select("SELECT kd_rek, nama, sum(nilai) nilai, jenis from(

                        SELECT LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama
                            ,SUM(rupiah) as nilai, 1 jenis
                            FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                            LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,4)=c.kd_rek3
                            WHERE LEFT(b.kd_rek6,1) IN ('4') $where
                            GROUP BY LEFT(b.kd_rek6,4),c.nm_rek3

                            UNION ALL

                        SELECT  '414' as kd_rek, 'LAIN-LAIN PENDAPATAN ASLI DAERAH YANG SAH' as nama
                            ,SUM(rupiah) as nilai, 1 jenis
                            FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                            WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus=3 $where
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,1)

                            UNION ALL

                        SELECT  510 as kd_rek, 'CONTRA POST' as nama
                            ,SUM(rupiah) as nilai, 1 jenis
                            FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                            WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus<>3 $where

                            UNION ALL
                            SELECT
                            515 AS kd_rek,
                            'SETOR SISA UYHD TAHUN LALU' AS nama,
                            SUM ( a.nilai ) AS nilai,
                            1 jenis
                        FROM
                            TRHOUTLAIN a left join trhkasin_ppkd b on a.kd_skpd = b.kd_skpd and a.no_bukti = b.no_sts
                        WHERE
                            a.status = '1' $whereBB
                            UNION ALL

                        SELECT '511' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,
                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                            FROM trhsp2d a
                            INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                            INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                            INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                            WHERE a.status_bud = '1' AND a.jns_spp = '4'
                            AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                            $where2

                            UNION ALL

                        SELECT '512' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,
                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                            FROM
                            trhsp2d a
                            INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                            INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                            INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                            WHERE a.status_bud = '1' AND a.jns_spp != '4'
                            AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                            $where2

                            UNION ALL

                        SELECT '513' kd_rek, 'PENGELUARAN NON SP2D' nama,
                            isnull(SUM(x.nilai), 0) AS nilai, 2 jenis
                            FROM
                            pengeluaran_non_sp2d x
                            WHERE $where3

                            UNION ALL

                        SELECT '513' kd_rek, 'RESTITUSI' nama,
                            isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis
                            FROM trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts
                            WHERE a.jns_trans=3 $where

                            UNION ALL

                        SELECT '514' as kd_rek, 'KOREKSI PENERIMAAN' nama,
                            isnull(SUM(w.nilai),0) as nilai,
                            1 jenis
                            FROM
                            trkasout_ppkd w
                            WHERE $where4

                        UNION ALL
                            SELECT '515' AS kd_rek,
                                'DEPOSITO' nama,
                                isnull(SUM(w.nilai), 0) AS nilai,
                                1 jenis
                            FROM
                                penerimaan_non_sp2d w
                            WHERE
                                $where4
                            AND w.jenis='1'
                        UNION ALL
                            SELECT
                                '516' AS kd_rek,
                                'PENERIMAAN NON SP2D' nama,
                                isnull(SUM(w.nilai), 0) AS nilai,
                                1 jenis
                            FROM
                                penerimaan_non_sp2d w
                            WHERE
                                $where4
                            AND w.jenis='2'
                            ) a
                                where a.nilai<>0 group by kd_rek, nama, jenis order by kd_rek",[$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl]);

                }
                //dd($data_bku);

                if ($st_rek != '1'){
                $total_bku = collect(DB::select("SELECT SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,
                                SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl
                                FROM(
                                SELECT  a.tgl_kas,LEFT(b.kd_rek6,3) as kd_rek, UPPER(c.nm_rek3) as nama
                                ,SUM(rupiah) as nilai, 1 jenis
                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                                LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,3)=c.kd_rek3
                                WHERE LEFT(b.kd_rek6,1) IN ('4') $where5 $wherestrenk
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,3),c.nm_rek3
                                UNION ALL
                                SELECT  a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama
                                ,SUM(rupiah) as nilai, 1 jenis
                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                                WHERE LEFT(b.kd_rek6,1) IN ('5','1') $where5 $wherestrenk
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,1)
                                UNION ALL
                                SELECT
                                a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,
                                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                                        FROM
                                            trhsp2d a
                                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                        INNER JOIN trdspp d ON c.no_spp = d.no_spp 	AND c.kd_skpd = d.kd_skpd
                                        WHERE a.status_bud = '1' AND  a.jns_spp != '4'
                                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                                        $where6
                                GROUP BY a.tgl_kas_bud
                                UNION ALL
                                SELECT
                                a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,
                                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                                        FROM trhsp2d a
                                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                        INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                                        WHERE a.status_bud = '1' AND a.jns_spp = '4'
                                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                                        $where6
                                GROUP BY a.tgl_kas_bud
                                UNION ALL
                                SELECT
                                                    x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,
                                                    isnull(SUM(x.nilai), 0) AS nilai, 2 jenis
                                                FROM
                                                    pengeluaran_non_sp2d x
                                                WHERE
                                                    $where7
                                GROUP BY x.tanggal
                                UNION ALL
                                SELECT
                                                    a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,
                                                    isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis
                                                FROM
                                                    trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts
                                                WHERE a.jns_trans=3
                                                    $where9
                                GROUP BY a.tgl_kas
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    trkasout_ppkd w
                                                WHERE
                                                    $where8

                                GROUP BY w.tanggal,w.kd_rek
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'DEPOSITO' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    penerimaan_non_sp2d w
                                                WHERE
                                                    $where8
                                                AND w.jenis='1'
                                GROUP BY w.tanggal
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'PENERIMAAN NON SP2D' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    penerimaan_non_sp2d w
                                                WHERE
                                                    $where8
                                                AND w.jenis='2'
                                GROUP BY w.tanggal
                                ) a",[$tgl,$st_rek,$tgl,$st_rek,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl]))->first();
                } else{
                    $total_bku = collect(DB::select("SELECT SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,
                                SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl
                                FROM(
                                SELECT  a.tgl_kas,LEFT(b.kd_rek6,3) as kd_rek, UPPER(c.nm_rek3) as nama
                                ,SUM(rupiah) as nilai, 1 jenis
                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                                LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,3)=c.kd_rek3
                                WHERE LEFT(b.kd_rek6,1) IN ('4') $where5
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,3),c.nm_rek3
                                UNION ALL
                                SELECT  a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama
                                ,SUM(rupiah) as nilai, 1 jenis
                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
                                WHERE LEFT(b.kd_rek6,1) IN ('5','1') $where5
                                GROUP BY a.tgl_kas,LEFT(b.kd_rek6,1)
                                UNION ALL
                                SELECT
                                a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,
                                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                                        FROM
                                            trhsp2d a
                                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                        INNER JOIN trdspp d ON c.no_spp = d.no_spp 	AND c.kd_skpd = d.kd_skpd
                                        WHERE a.status_bud = '1' AND  a.jns_spp != '4'
                                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                                        $where6
                                GROUP BY a.tgl_kas_bud
                                UNION ALL
                                SELECT
                                a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,
                                            isnull(SUM(d.nilai), 0) AS nilai, 2 jenis
                                        FROM trhsp2d a
                                        INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                        INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                        INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                                        WHERE a.status_bud = '1' AND a.jns_spp = '4'
                                        AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                                        $where6
                                GROUP BY a.tgl_kas_bud
                                UNION ALL
                                SELECT
                                                    x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,
                                                    isnull(SUM(x.nilai), 0) AS nilai, 2 jenis
                                                FROM
                                                    pengeluaran_non_sp2d x
                                                WHERE
                                                    $where7
                                GROUP BY x.tanggal
                                UNION ALL
                                SELECT
                                                    a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,
                                                    isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis
                                                FROM
                                                    trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts
                                                WHERE a.jns_trans=3
                                                    $where9
                                GROUP BY a.tgl_kas
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    trkasout_ppkd w
                                                WHERE
                                                    $where8

                                GROUP BY w.tanggal,w.kd_rek
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'DEPOSITO' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    penerimaan_non_sp2d w
                                                WHERE
                                                    $where8
                                                AND w.jenis='1'
                                GROUP BY w.tanggal
                                UNION ALL
                                SELECT
                                                    w.tanggal,'' as kd_rek, 'PENERIMAAN NON SP2D' nama,
                                                    isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                                                FROM
                                                    penerimaan_non_sp2d w
                                                WHERE
                                                    $where8
                                                AND w.jenis='2'
                                GROUP BY w.tanggal
                                ) a",[$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl,$tgl]))->first();
                }
        }

        if ($tgl == $tahun . '-01-01') {
            $saldo = DB::table('buku_kas')->selectRaw("'4' kd_rek, 'SALDO AWAL' nama, nilai , 1 jenis");
        }

        $total_saldo_awal = DB::table('buku_kas')->select('nilai')->first();
        if ($tgl == "2023-01-01") {
            $saldo_awal = 0;
        } else {
            $saldo_awal = $total_saldo_awal->nilai;
        }

        if ($ttd) {
            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->first();
        } else {
            $tanda_tangan = null;
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_bku' => $data_bku,
            'total_bku' => $total_bku,
            'saldo_awal' => $saldo_awal,
            'no_urut' => $no_urut,
            'tanda_tangan' => $tanda_tangan
        ];
         //dd($data['data_bku']);

        $view = view('bud.laporan_bendahara.cetak.bku_tanpa_tanggal')->with($data);

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

    public function bkuDenganTanggal(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $halaman = $request->halaman;
        $no_urut = $request->no_urut;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        if ($ttd) {
            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd, 'kode' => 'BUD'])->first();
        } else {
            $tanda_tangan = null;
        }

        $terima1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->leftJoin('ms_rek3 as c', function ($join) {
                $join->on(DB::raw("left(b.kd_rek6,3)"), '=', 'c.kd_rek3');
            })
            ->selectRaw("a.tgl_kas, SUM ( rupiah ) AS nilai,1 jenis")
            ->whereRaw("LEFT( b.kd_rek6, 1 ) IN ( ? ) AND a.tgl_kas BETWEEN ? AND ?", ['4', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas");

        $terima2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas,SUM ( rupiah ) AS nilai,1 jenis")
            ->whereRaw("LEFT (b.kd_rek6,1) IN (?,?) AND pot_khusus=? AND a.tgl_kas BETWEEN ? AND ?", ['5', '1', '3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas")
            ->unionAll($terima1);

        $terima3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas,SUM ( rupiah ) AS nilai,1 jenis")
            ->whereRaw("LEFT ( b.kd_rek6, 1 ) IN ( ?,? ) AND pot_khusus <> ? AND a.tgl_kas BETWEEN ? AND ?", ['5', '1', '3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas")
            ->unionAll($terima2);

        $terima4 = DB::table('trkasout_ppkd as w')
            ->selectRaw("w.tanggal as tgl_kas, isnull( SUM ( w.nilai ), 0 ) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal BETWEEN ? AND ?", [$periode1, $periode2])
            ->groupByRaw("w.tanggal")
            ->unionAll($terima3);

        $terima5 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal as tgl_kas, isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal BETWEEN ? AND ? AND w.jenis=?", [$periode1, $periode2, '1'])
            ->groupByRaw("w.tanggal")
            ->unionAll($terima4);

        $terima6 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal as tgl_kas, isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal BETWEEN ? AND ? AND w.jenis=?", [$periode1, $periode2, '2'])
            ->groupByRaw("w.tanggal")
            ->unionAll($terima5);

        $terima = DB::table(DB::raw("({$terima6->toSql()}) AS a"))
            ->selectRaw("tgl_kas, SUM(nilai) nilai")
            ->mergeBindings($terima6)
            ->groupBy('tgl_kas');

        $keluar1 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud tgl_kas,isnull( SUM ( d.nilai ), 0 ) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud =? AND a.jns_spp = ? AND ( c.sp2d_batal= 0 OR c.sp2d_batal IS NULL ) AND a.tgl_kas_bud BETWEEN ? AND ?", ['1', '4', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas_bud");

        $keluar2 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud tgl_kas, isnull( SUM ( d.nilai ), 0 ) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud =? AND a.jns_spp != ? AND ( c.sp2d_batal= 0 OR c.sp2d_batal IS NULL ) AND a.tgl_kas_bud BETWEEN ? AND ?", ['1', '4', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas_bud")->unionAll($keluar1);

        $keluar3 = DB::table('pengeluaran_non_sp2d as x')
            ->selectRaw("x.tanggal tgl_kas, isnull( SUM ( x.nilai ), 0 ) AS nilai,2 jenis")
            ->whereRaw("x.tanggal BETWEEN ? AND ?", [$periode1, $periode2])
            ->groupByRaw("x.tanggal")
            ->unionAll($keluar2);

        $keluar4 = DB::table('trkoreksi_pengeluaran as w')
            ->selectRaw("w.tanggal tgl_kas, isnull( SUM ( w.nilai ), 0 ) AS nilai,2 jenis")
            ->whereRaw("w.tanggal BETWEEN ? AND ?", [$periode1, $periode2])
            ->groupByRaw("w.tanggal")
            ->unionAll($keluar3);

        $keluar5 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas, isnull( SUM ( b.rupiah ), 0 ) AS nilai,2 jenis")
            ->whereRaw("a.jns_trans= ? AND a.tgl_kas BETWEEN ? AND ?", ['3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas")->unionAll($keluar4);

        $keluar = DB::table(DB::raw("({$keluar5->toSql()}) AS b"))
            ->selectRaw("tgl_kas, SUM(nilai) nilai")
            ->mergeBindings($keluar5)
            ->groupBy('tgl_kas');

        $nilai = DB::table($terima, 'terima')
            ->selectRaw("terima.tgl_kas, terima.nilai terima, keluar.nilai keluar")
            ->leftJoinSub($keluar, 'keluar', function ($join) {
                $join->on('terima.tgl_kas', '=', 'keluar.tgl_kas');
            })
            ->orderBy('terima.tgl_kas')
            ->get();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_bku' => $nilai,
            'no_urut' => $no_urut,
            'tanda_tangan' => $tanda_tangan
        ];

        $view = view('bud.laporan_bendahara.cetak.bku_dengan_tanggal')->with($data);

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

    //calvin
    public function bkuDenganTanggal1(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $halaman = $request->halaman;
        $no_urut = $request->no_urut;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;
        $st_rek = $request->st_rek;
        //dd($periode2);
        if ($ttd) {
            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd, 'kode' => 'BUD'])->first();
        } else {
            $tanda_tangan = null;
        }

        if($st_rek == '1'){
            $wherestrenk = "";
        }else if($st_rek == '3001006966'){
            $wherestrenk = "AND a.rek_bank = ? ";
        }else{
            $wherestrenk = "AND a.rek_bank = ? ";
        }

        if($st_rek == '1'){
        $data_bku = DB::select("SELECT terima.tgl_kas, terima.nilai terima, keluar.nilai keluar FROM (
            SELECT tgl_kas, SUM(nilai) nilai FROM (

            SELECT a.tgl_kas, SUM ( rupiah ) AS nilai,1 jenis
            FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas= b.no_kas AND a.kd_skpd= b.kd_skpd
                                 LEFT JOIN ms_rek3 c ON LEFT ( b.kd_rek6, 3 ) = c.kd_rek3
            WHERE LEFT( b.kd_rek6, 1 ) IN ( '4' ) AND a.tgl_kas BETWEEN ? AND ?
            group by a.tgl_kas
            UNION ALL

            SELECT a.tgl_kas,SUM ( rupiah ) AS nilai,1 jenis
            FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas= b.no_kas AND a.kd_skpd= b.kd_skpd
            WHERE LEFT (b.kd_rek6,1) IN ('5','1') AND pot_khusus=3 AND a.tgl_kas BETWEEN ? AND ?
            GROUP BY a.tgl_kas
            UNION ALL

            SELECT a.tgl_kas,SUM ( rupiah ) AS nilai,1 jenis
            FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas= b.no_kas AND a.kd_skpd= b.kd_skpd
            WHERE LEFT ( b.kd_rek6, 1 ) IN ( '5', '1' ) AND pot_khusus <> 3 AND a.tgl_kas BETWEEN ? AND ?
            GROUP BY a.tgl_kas
            UNION ALL

            SELECT w.tgl_bukti, isnull(SUM(w.nilai), 0) as nilai, 1 jenis FROM TRHOUTLAIN w WHERE w.tgl_bukti BETWEEN ? AND ? and w.status = '1' GROUP BY w.tgl_bukti
				UNION ALL

            SELECT w.tanggal, isnull( SUM ( w.nilai ), 0 ) AS nilai, 1 jenis
            FROM trkasout_ppkd w
            WHERE w.tanggal BETWEEN ? AND ?
            GROUP BY w.tanggal
            UNION ALL

            SELECT w.tanggal, isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
            FROM penerimaan_non_sp2d w
            WHERE w.tanggal between ? AND ? AND w.jenis='1'
            GROUP BY w.tanggal
            UNION ALL

            SELECT w.tanggal, isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
            FROM penerimaan_non_sp2d w
            WHERE w.tanggal between ? AND ? AND w.jenis='2'
            GROUP BY w.tanggal) x
            GROUP BY tgl_kas) terima
            LEFT JOIN
            (SELECT tgl_kas, SUM(nilai) nilai FROM (

            SELECT a.tgl_kas_bud tgl_kas,isnull( SUM ( d.nilai ), 0 ) AS nilai, 2 jenis
                 FROM trhsp2d a INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                 WHERE a.status_bud = '1' AND a.jns_spp = '4'
                 AND ( c.sp2d_batal= 0 OR c.sp2d_batal IS NULL ) AND a.tgl_kas_bud BETWEEN ? AND ?
            GROUP BY a.tgl_kas_bud
            UNION ALL

            SELECT a.tgl_kas_bud tgl_kas, isnull( SUM ( d.nilai ), 0 ) AS nilai, 2 jenis
                 FROM trhsp2d a INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                 WHERE a.status_bud = '1' AND a.jns_spp != '4' AND ( c.sp2d_batal= 0 OR c.sp2d_batal IS NULL )
                 AND a.tgl_kas_bud BETWEEN ? AND ?
            GROUP BY a.tgl_kas_bud
            UNION ALL

            SELECT x.tanggal tgl_kas, isnull( SUM ( x.nilai ), 0 ) AS nilai,2 jenis
            FROM pengeluaran_non_sp2d x
            WHERE x.tanggal BETWEEN ? AND ?
            GROUP BY x.tanggal
            UNION ALL

            SELECT a.tgl_kas, isnull( SUM ( b.rupiah ), 0 ) AS nilai,2 jenis
            FROM trdrestitusi b INNER JOIN trhrestitusi a ON a.kd_skpd= b.kd_skpd AND a.no_kas= b.no_kas AND a.no_sts= b.no_sts
            WHERE a.jns_trans= 3 AND a.tgl_kas BETWEEN ? AND ?
            GROUP BY a.tgl_kas) x
            GROUP BY tgl_kas) keluar on terima.tgl_kas=keluar.tgl_kas
            ORDER BY terima.tgl_kas",[$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2]);
        } else{
            $data_bku = DB::select("SELECT terima.tgl_kas, terima.nilai terima, keluar.nilai keluar FROM (
                SELECT tgl_kas, SUM(nilai) nilai FROM (

                SELECT a.tgl_kas, SUM ( rupiah ) AS nilai,1 jenis
                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas= b.no_kas AND a.kd_skpd= b.kd_skpd
                                     LEFT JOIN ms_rek3 c ON LEFT ( b.kd_rek6, 3 ) = c.kd_rek3
                WHERE LEFT( b.kd_rek6, 1 ) IN ( '4' ) AND a.tgl_kas BETWEEN ? AND ? $wherestrenk
                group by a.tgl_kas
                UNION ALL

                SELECT a.tgl_kas,SUM ( rupiah ) AS nilai,1 jenis
                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas= b.no_kas AND a.kd_skpd= b.kd_skpd
                WHERE LEFT (b.kd_rek6,1) IN ('5','1') AND pot_khusus=3 AND a.tgl_kas BETWEEN ? AND ? $wherestrenk
                GROUP BY a.tgl_kas
                UNION ALL

                SELECT a.tgl_kas,SUM ( rupiah ) AS nilai,1 jenis
                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas= b.no_kas AND a.kd_skpd= b.kd_skpd
                WHERE LEFT ( b.kd_rek6, 1 ) IN ( '5', '1' ) AND pot_khusus <> 3 AND a.tgl_kas BETWEEN ? AND ? $wherestrenk
                GROUP BY a.tgl_kas
                UNION ALL

                SELECT w.tanggal, isnull( SUM ( w.nilai ), 0 ) AS nilai, 1 jenis
                FROM trkasout_ppkd w
                WHERE w.tanggal BETWEEN ? AND ?
                GROUP BY w.tanggal
                UNION ALL

                SELECT w.tanggal, isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                FROM penerimaan_non_sp2d w
                WHERE w.tanggal between ? AND ? AND w.jenis='1'
                GROUP BY w.tanggal
                UNION ALL

                SELECT w.tanggal, isnull(SUM(w.nilai), 0) AS nilai, 1 jenis
                FROM penerimaan_non_sp2d w
                WHERE w.tanggal between ? AND ? AND w.jenis='2'
                GROUP BY w.tanggal) x
                GROUP BY tgl_kas) terima
                LEFT JOIN
                (SELECT tgl_kas, SUM(nilai) nilai FROM (

                SELECT a.tgl_kas_bud tgl_kas,isnull( SUM ( d.nilai ), 0 ) AS nilai, 2 jenis
                     FROM trhsp2d a INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                    INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                     WHERE a.status_bud = '1' AND a.jns_spp = '4'
                     AND ( c.sp2d_batal= 0 OR c.sp2d_batal IS NULL ) AND a.tgl_kas_bud BETWEEN ? AND ?
                GROUP BY a.tgl_kas_bud
                UNION ALL

                SELECT a.tgl_kas_bud tgl_kas, isnull( SUM ( d.nilai ), 0 ) AS nilai, 2 jenis
                     FROM trhsp2d a INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                                    INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                     WHERE a.status_bud = '1' AND a.jns_spp != '4' AND ( c.sp2d_batal= 0 OR c.sp2d_batal IS NULL )
                     AND a.tgl_kas_bud BETWEEN ? AND ?
                GROUP BY a.tgl_kas_bud
                UNION ALL

                SELECT x.tanggal tgl_kas, isnull( SUM ( x.nilai ), 0 ) AS nilai,2 jenis
                FROM pengeluaran_non_sp2d x
                WHERE x.tanggal BETWEEN ? AND ?
                GROUP BY x.tanggal
                UNION ALL

                SELECT a.tgl_kas, isnull( SUM ( b.rupiah ), 0 ) AS nilai,2 jenis
                FROM trdrestitusi b INNER JOIN trhrestitusi a ON a.kd_skpd= b.kd_skpd AND a.no_kas= b.no_kas AND a.no_sts= b.no_sts
                WHERE a.jns_trans= 3 AND a.tgl_kas BETWEEN ? AND ?
                GROUP BY a.tgl_kas) x
                GROUP BY tgl_kas) keluar on terima.tgl_kas=keluar.tgl_kas
                ORDER BY terima.tgl_kas",[$periode1,$periode2,$st_rek,$periode1,$periode2,$st_rek,$periode1,$periode2,$st_rek,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2,$periode1,$periode2]);
        }
//       dd($data_bku);
        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_bku' => $data_bku,
            'no_urut' => $no_urut,
            'tanda_tangan' => $tanda_tangan
        ];

        $view = view('bud.laporan_bendahara.cetak.bku_dengan_tanggal')->with($data);

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
    //end

    public function bkuTanpaBlud(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $halaman = $request->halaman;
        $no_urut = $request->no_urut;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        if ($ttd) {
            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd, 'kode' => 'BUD'])->first();
        } else {
            $tanda_tangan = null;
        }

        $terima1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->leftJoin('ms_rek3 as c', function ($join) {
                $join->on(DB::raw("left(b.kd_rek6,3)"), '=', 'c.kd_rek3');
            })
            ->selectRaw("a.tgl_kas, SUM ( rupiah ) AS nilai,1 jenis")
            ->whereRaw("LEFT( b.kd_rek6, 1 ) IN ( ? ) AND a.tgl_kas BETWEEN ? AND ? and b.kd_rek6 not in ('420101040001','410416010001')", ['4', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas");

        $terima2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas,SUM ( rupiah ) AS nilai,1 jenis")
            ->whereRaw("LEFT (b.kd_rek6,1) IN (?,?) AND pot_khusus=? AND a.tgl_kas BETWEEN ? AND ?", ['5', '1', '3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas")
            ->unionAll($terima1);

        $terima3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas,SUM ( rupiah ) AS nilai,1 jenis")
            ->whereRaw("LEFT ( b.kd_rek6, 1 ) IN ( ?,? ) AND pot_khusus <> ? AND a.tgl_kas BETWEEN ? AND ?", ['5', '1', '3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas")
            ->unionAll($terima2);

        $terima4 = DB::table('trkasout_ppkd as w')
            ->selectRaw("w.tanggal as tgl_kas, isnull( SUM ( w.nilai ), 0 ) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal BETWEEN ? AND ?", [$periode1, $periode2])
            ->groupByRaw("w.tanggal")
            ->unionAll($terima3);

        $terima5 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal as tgl_kas, isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal BETWEEN ? AND ? AND w.jenis=?", [$periode1, $periode2, '1'])
            ->groupByRaw("w.tanggal")
            ->unionAll($terima4);

        $terima6 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal as tgl_kas, isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal BETWEEN ? AND ? AND w.jenis=?", [$periode1, $periode2, '2'])
            ->groupByRaw("w.tanggal")
            ->unionAll($terima5);

        $terima = DB::table(DB::raw("({$terima6->toSql()}) AS a"))
            ->selectRaw("tgl_kas, SUM(nilai) nilai")
            ->mergeBindings($terima6)
            ->groupBy('tgl_kas');

        $keluar1 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud tgl_kas,isnull( SUM ( d.nilai ), 0 ) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud =? AND a.jns_spp = ? AND ( c.sp2d_batal= 0 OR c.sp2d_batal IS NULL ) AND a.tgl_kas_bud BETWEEN ? AND ?", ['1', '4', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas_bud");

        $keluar2 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud tgl_kas, isnull( SUM ( d.nilai ), 0 ) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud =? AND a.jns_spp != ? AND ( c.sp2d_batal= 0 OR c.sp2d_batal IS NULL ) AND a.tgl_kas_bud BETWEEN ? AND ?", ['1', '4', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas_bud")->unionAll($keluar1);

        $keluar3 = DB::table('pengeluaran_non_sp2d as x')
            ->selectRaw("x.tanggal tgl_kas, isnull( SUM ( x.nilai ), 0 ) AS nilai,2 jenis")
            ->whereRaw("x.tanggal BETWEEN ? AND ?", [$periode1, $periode2])
            ->groupByRaw("x.tanggal")
            ->unionAll($keluar2);

        $keluar4 = DB::table('trkoreksi_pengeluaran as w')
            ->selectRaw("w.tanggal tgl_kas, isnull( SUM ( w.nilai ), 0 ) AS nilai,2 jenis")
            ->whereRaw("w.tanggal BETWEEN ? AND ?", [$periode1, $periode2])
            ->groupByRaw("w.tanggal")
            ->unionAll($keluar3);

        $keluar5 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas, isnull( SUM ( b.rupiah ), 0 ) AS nilai,2 jenis")
            ->whereRaw("a.jns_trans= ? AND a.tgl_kas BETWEEN ? AND ?", ['3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas")->unionAll($keluar4);

        $keluar = DB::table(DB::raw("({$keluar5->toSql()}) AS b"))
            ->selectRaw("tgl_kas, SUM(nilai) nilai")
            ->mergeBindings($keluar5)
            ->groupBy('tgl_kas');

        $nilai = DB::table($terima, 'terima')
            ->selectRaw("terima.tgl_kas, terima.nilai terima, keluar.nilai keluar")
            ->leftJoinSub($keluar, 'keluar', function ($join) {
                $join->on('terima.tgl_kas', '=', 'keluar.tgl_kas');
            })
            ->orderBy('terima.tgl_kas')
            ->get();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_bku' => $nilai,
            'no_urut' => $no_urut,
            'tanda_tangan' => $tanda_tangan
        ];

        $view = view('bud.laporan_bendahara.cetak.bku_tanpa_blud')->with($data);

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

    public function bkuRincian(Request $request)
    {
        $pilihan = $request->pilihan;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $no_urut = $request->no_urut;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;
        $tahun = tahun_anggaran();

        if ($ttd) {
            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd, 'kode' => 'BUD'])->first();
        } else {
            $tanda_tangan = null;
        }

        if ($tgl == $tahun . '-01-01') {
            $saldo = DB::table('buku_kas')->selectRaw("'4' kd_rek, 'SALDO AWAL' nama, nilai , 1 jenis");
        }

        $cek_pengeluaran = DB::table('pengeluaran_non_sp2d')->where(['tanggal' => $tgl])->count();
        if ($cek_pengeluaran > 0) {
            $keluar_non_sp2d = DB::table('pengeluaran_non_sp2d as x')
                ->selectRaw("CAST(nomor as VARCHAR) as no_kas,nomor as urut, '' as uraian,keterangan+'. Rp. ','' kode, 'PENGELUARAN NON SP2D' nm_rek6,0 as terima,isnull(SUM(x.nilai), 0) AS keluar, 2 jenis, isnull(SUM(x.nilai), 0) as netto, ''sp")
                ->where(['tanggal' => $tgl])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan1 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '1'])->count();
        if ($cek_penerimaan1 > 0) {
            $masuk_non_sp2d1 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR),nomor as urut,keterangan as uraian,''kode,'Deposito'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto, ''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '1'])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan2 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '2'])->count();
        if ($cek_penerimaan2 > 0) {
            $masuk_non_sp2d2 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR) as nokas,nomor as urut,keterangan as uraian,'-'kode,'Penerimaan NON SP2D'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto, ''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '2'])
                ->groupBy('nomor', 'keterangan');
        }

        $bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.no_kas,a.no_kas as urut,keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(b.rupiah) netto, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas=?", ['4', $tgl])
            ->groupByRaw("a.no_kas,keterangan");

        $bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek6 as c', function ($join) {
                $join->on('b.kd_rek6', '=', 'c.kd_rek6');
            })
            ->selectRaw("'' as no_kas,a.no_kas as urut,keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6 as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis, 0 netto, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas=?", ['4', $tgl])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku1);

        $bku3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, ''as nm_rek6,0 as terima,0 as keluar, 1 jenis,SUM(rupiah) netto, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas=?", ['5', '1', '3', $tgl])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku2);

        $bku4 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'Lain-lain PAD yang sah'as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis,0 netto, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas=?", ['5', '1', '3', $tgl])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku3);

        $bku5 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(rupiah) netto, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas=?", ['5', '1', '3', $tgl])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku4);

        $bku6 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'CONTRA POST' as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis, 0 netto, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas=?", ['5', '1', '3', $tgl])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku5);

        $bku7 = DB::table('trhsp2d as a')
            ->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("no_kas_bud AS no_kas,a.no_kas_bud as urut,'No.SP2D :'+' '+a.no_sp2d+'<br> '+a.keperluan+'Netto Rp. ' AS uraian,'' AS kode,'' AS nm_rek6,0 AS terima,0 AS keluar,2 AS jenis,(SUM(b.nilai))-(SELECT ISNULL(SUM(nilai),0) FROM trspmpot WHERE no_spm=a.no_spm) AS netto,'' as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud=?", ['1', $tgl])
            ->groupByRaw("a.no_sp2d,no_kas_bud,a.keperluan,a.no_spm")
            ->unionAll($bku6);

        $bku8 = DB::table('trdspp as b')
            ->join('trhsp2d as a', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("'' AS no_kas,a.no_kas_bud AS urut,'' AS uraian,(b.kd_sub_kegiatan+'.'+b.kd_rek6) AS kode,b.nm_rek6 AS nm_rek6,0 AS terima,b.nilai AS keluar,2 AS jenis,0 as netto,''as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud=?", ['1', $tgl])
            ->unionAll($bku7);

        $bku9 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->selectRaw("a.no_kas as no_kas,a.no_kas as urut,'RESTITUSI<br>'+keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 AS terima,0 keluar, 2 jenis,isnull(SUM(b.rupiah), 0) as netto,''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas=?", ['3', $tgl])
            ->groupByRaw("a.no_kas,keterangan")
            ->unionAll($bku8);

        $bku10 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->leftJoin('ms_rek6 as c', 'b.kd_rek6', '=', 'c.kd_rek6')
            ->selectRaw("'' as no_kas,a.no_kas as urut,''as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6,0 terima,isnull(SUM(b.rupiah), 0) AS keluar, 2 jenis,0 netto, ''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas=?", ['3', $tgl])
            ->groupByRaw("a.no_kas,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku9);

        $bku11 = DB::table('trkasout_ppkd as w')
            ->selectRaw("no as no_kas, no as urut,'KOREKSI PENERIMAAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,isnull(SUM(w.nilai),0) as terima,0 as keluar,1 jenis,isnull(SUM(w.nilai),0) as netto,''sp")
            ->whereRaw("tanggal=?", [$tgl])
            ->groupByRaw("no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
            ->unionAll($bku10);

        $bku12 = DB::table('trkoreksi_pengeluaran as w')
            ->selectRaw("no as no_kas, no as urut,'KOREKSI PENGELUARAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,0 as terima,isnull(SUM(w.nilai),0) as keluar,2 jenis,isnull(SUM(w.nilai),0) as netto,''sp")
            ->whereRaw("tanggal=?", [$tgl])
            ->groupByRaw("no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
            ->unionAll($bku11);

        if (isset($saldo)) {
            $bku13 = $bku12->unionAll($saldo);
        } else {
            $bku13 = $bku12;
        }

        if (isset($keluar_non_sp2d)) {
            $bku14 = $bku13->unionAll($keluar_non_sp2d);
        } else {
            $bku14 = $bku13;
        }

        if (isset($masuk_non_sp2d1)) {
            $bku15 = $bku14->unionAll($masuk_non_sp2d1);
        } else {
            $bku15 = $bku14;
        }

        if (isset($masuk_non_sp2d2)) {
            $bku16 = $bku15->unionAll($masuk_non_sp2d2);
        } else {
            $bku16 = $bku15;
        }

        $bku = DB::table(DB::raw("({$bku16->toSql()}) AS sub"))
            ->mergeBindings($bku16)
            ->orderBy('urut')
            ->orderBy('kode')
            ->orderBy('jenis')
            ->get();

        $total_bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek3 as c', function ($join) {
                $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
            })
            ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas<?", ['4', $tgl])
            ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,4),c.nm_rek3");

        $total_bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and a.tgl_kas<?", ['5', '1', $tgl])
            ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,1)")
            ->unionAll($total_bku1);

        $total_bku3 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud = ? AND  a.jns_spp != ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $tgl])
            ->groupByRaw("a.tgl_kas_bud")
            ->unionAll($total_bku2);

        $total_bku4 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud = ? AND  a.jns_spp = ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $tgl])
            ->groupByRaw("a.tgl_kas_bud")
            ->unionAll($total_bku3);

        $total_bku5 = DB::table('pengeluaran_non_sp2d as x')
            ->selectRaw("x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,isnull(SUM(x.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("x.tanggal<?", [$tgl])
            ->groupByRaw("x.tanggal")
            ->unionAll($total_bku4);

        $total_bku6 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->selectRaw("a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis")
            ->whereRaw("a.tgl_kas<?", [$tgl])
            ->where('a.jns_trans', '3')
            ->groupByRaw("a.tgl_kas")
            ->unionAll($total_bku5);

        $total_bku7 = DB::table('trkasout_ppkd as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal<?", [$tgl])
            ->groupByRaw("w.tanggal,w.kd_rek")
            ->unionAll($total_bku6);

        $total_bku8 = DB::table('trkoreksi_pengeluaran as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENGELUARAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("w.tanggal<?", [$tgl])
            ->groupByRaw("w.tanggal,w.kd_rek")
            ->unionAll($total_bku7);

        $total_bku9 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'DEPOSITO' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal<?", [$tgl])
            ->where('w.jenis', '1')
            ->groupByRaw("w.tanggal")
            ->unionAll($total_bku8);

        $total_bku10 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'PENERIMAAN NON SP2D' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal<?", [$tgl])
            ->where('w.jenis', '2')
            ->groupByRaw("w.tanggal")
            ->unionAll($total_bku9);

        $total_bku = DB::table(DB::raw("({$total_bku10->toSql()}) AS sub"))
            ->selectRaw("SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl")
            ->mergeBindings($total_bku10)
            ->first();

        $saldo_awal = DB::table('buku_kas')->select('nilai')->where(['nomor' => '0'])->first();
        if ($tgl == '2019-01-01') {
            $saldo_awal = 0;
        } else {
            $saldo_awal = $saldo_awal->nilai;
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => '5.02.0.00.0.00.02.0000'])->first(),
            'data_bku' => $bku,
            'tanggal' => $tgl,
            'total_bku' => $total_bku,
            'saldo_awal' => $saldo_awal,
            'no_urut' => $no_urut,
            'tanda_tangan' => $tanda_tangan
        ];

        $view = view('bud.laporan_bendahara.cetak.bku_rincian')->with($data);

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

    //created by calvin
    public function bkuRincian1(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $st_rek = $request->st_rek;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $no_urut = $request->no_urut;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;
        $tahun = tahun_anggaran();

        if($st_rek == '1'){
            $wherestrenk = "";
        }else if($st_rek == '3001006966'){
            $wherestrenk = "AND a.rek_bank = ? ";
        }else{
            $wherestrenk = "AND a.rek_bank = ? ";
        }

            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd, 'kode' => 'BUD'])->first();


        if ($tgl == $tahun . '-01-01') {
            $saldo = DB::table('buku_kas')->selectRaw("'4' kd_rek, 'SALDO AWAL' nama, nilai , 1 jenis");
        }


    if($st_rek == '1'){  ////kondisi uraian bku ketika semua rekening
        $cek_pengeluaran = DB::table('pengeluaran_non_sp2d')->where(['tanggal' => $tgl])->count();
        if ($cek_pengeluaran > 0) {
            $keluar_non_sp2d = DB::table('pengeluaran_non_sp2d as x')
                ->selectRaw("CAST(nomor as VARCHAR) as no_kas,nomor as urut, '' as uraian,keterangan+'. Rp. ','' kode, 'PENGELUARAN NON SP2D' nm_rek6,0 as terima,isnull(SUM(x.nilai), 0) AS keluar, 2 jenis, isnull(SUM(x.nilai), 0) as netto,isnull(SUM(x.nilai), 0) AS tot_kel, ''sp")
                ->where(['tanggal' => $tgl])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan1 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '1'])->count();
        if ($cek_penerimaan1 > 0) {
            $masuk_non_sp2d1 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR),nomor as urut,keterangan as uraian,''kode,'Deposito'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto,0 tot_kel, ''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '1'])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan2 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '2'])->count();
        if ($cek_penerimaan2 > 0) {
            $masuk_non_sp2d2 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR) as nokas,nomor as urut,keterangan as uraian,'-'kode,'Penerimaan NON SP2D'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto, 0 tot_kel,''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '2'])
                ->groupBy('nomor', 'keterangan');
        }

        $bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(b.rupiah) netto,  0 tot_kel,''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas=?", ['4', $tgl])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan");

        $bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek6 as c', function ($join) {
                $join->on('b.kd_rek6', '=', 'c.kd_rek6');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6 as nm_rek6,b.rupiah as terima,0 as keluar, 1 jenis, 0 netto,  0 tot_kel,''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas=?", ['4', $tgl])
            //->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku1);

        $bku3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, ''as nm_rek6,0 as terima,0 as keluar, 1 jenis,SUM(rupiah) netto,  0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas=?", ['5', '1', '3', $tgl])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku2);

        $bku4 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'Lain-lain PAD yang sah'as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis,0 netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas=?", ['5', '1', '3', $tgl])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku3);

        $bku5 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0as terima,0 as keluar, 1 jenis, sum(rupiah) netto,  0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas=?", ['5', '1', '3', $tgl])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan")
            ->unionAll($bku4);

        $bku6 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl, '' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'CONTRA POST' as nm_rek6,SUM(rupiah) as terima,0 as keluar,1 jenis, 0 netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas=?", ['5', '1', '3', $tgl])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku5);

        $bku7 = DB::table('trhsp2d as a')
            ->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("a.tgl_kas_bud as tgl,no_kas_bud AS no_kas,a.no_kas_bud as urut,'No.SP2D :'+' '+a.no_sp2d+' '+a.keperluan+'Netto Rp. ' AS uraian,'' AS kode,'' AS nm_rek6,0 AS terima, sum(b.nilai) AS keluar,2 AS jenis,(SUM(b.nilai))-(SELECT ISNULL(SUM(nilai),0) FROM trspmpot WHERE no_spm=a.no_spm) AS netto, 0 as tot_kel,'' as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud=?", ['1', $tgl])
            ->groupByRaw("a.tgl_kas_bud,a.no_sp2d,no_kas_bud,a.keperluan,a.no_spm")
            ->unionAll($bku6);

        $bku8 = DB::table('trdspp as b')
            ->join('trhsp2d as a', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("'' as tgl,'' AS no_kas,a.no_kas_bud AS urut,'' AS uraian,case when b.kd_sub_kegiatan is null then a.kd_skpd+'.'+b.kd_rek6 else ( b.kd_sub_kegiatan+'.'+b.kd_rek6) END  AS kode,b.nm_rek6 AS nm_rek6,0 AS terima,b.nilai AS keluar,2 AS jenis,0 as netto,b.nilai AS tot_kel,''as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud=?", ['1', $tgl])
            ->unionAll($bku7);

        $bku9 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas as no_kas,a.no_kas as urut,'RESTITUSI<br>'+keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 AS terima,0 keluar, 2 jenis,isnull(SUM(b.rupiah), 0) as netto,0 tot_kel,''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas=?", ['3', $tgl])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan")
            ->unionAll($bku8);

        $bku10 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->leftJoin('ms_rek6 as c', 'b.kd_rek6', '=', 'c.kd_rek6')
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,''as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6,0 terima,isnull(SUM(b.rupiah), 0) AS keluar, 2 jenis,0 netto, isnull(SUM(b.rupiah), 0) AS tot_kel,''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas=?", ['3', $tgl])
            ->groupByRaw("a.no_kas,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku9);

        $bku11 = DB::table('trkasout_ppkd as w')
            ->selectRaw("tanggal as tgl,no as no_kas, no as urut,'KOREKSI PENERIMAAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,isnull(SUM(w.nilai),0) as terima,0 as keluar,1 jenis,isnull(SUM(w.nilai),0) as netto,0 tot_kel,''sp")
            ->whereRaw("tanggal=?", [$tgl])
            ->groupByRaw("tanggal,no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
            ->unionAll($bku10);


        // $bku12 = DB::table('trkoreksi_pengeluaran as w')
        //     ->selectRaw("no as no_kas, no as urut,'KOREKSI PENGELUARAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,0 as terima,isnull(SUM(w.nilai),0) as keluar,2 jenis,isnull(SUM(w.nilai),0) as netto,''sp")
        //     ->whereRaw("tanggal=?", [$tgl])
        //     ->groupByRaw("no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
        //     ->unionAll($bku11);
        $bku12 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas, a.no_kas as urut,a.keterangan+'. Rp.' as uraian,'' as kode, '' as nm_rek6, 0 as terima,0 as keluar,1 jenis,isnull(sum(b.rupiah),0) as netto,0 tot_kel, '' sp ")
            ->whereRaw("c.status = 1 and a.tgl_kas = ?", [$tgl])
            ->groupByRaw("a.tgl_kas,a.no_kas,a.keterangan")->unionAll($bku11);

        $bku121 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' no_kas, a.no_kas as urut,a.keterangan as uraian,'SETOR SISA UYHD TAHUN LALU' as kode, 'Setor Sisa UYHD TAHUN LALU' as nm_rek6,isnull(sum(b.rupiah),0) as terima,0 as keluar,1  jenis,0 as netto,0 tot_kel,'' sp ")
            ->whereRaw("c.status = 1 and a.tgl_kas = ?", [$tgl])
            ->groupByRaw("a.no_kas,a.keterangan")->unionAll($bku12);


        if (isset($keluar_non_sp2d)) {
            $bku14 = $bku121->unionAll($keluar_non_sp2d);
        } else {
            $bku14 = $bku121;
        }

        if (isset($masuk_non_sp2d1)) {
            $bku15 = $bku14->unionAll($masuk_non_sp2d1);
        } else {
            $bku15 = $bku14;
        }

        if (isset($masuk_non_sp2d2)) {
            $bku16 = $bku15->unionAll($masuk_non_sp2d2);
        } else {
            $bku16 = $bku15;
        }

        $bku = DB::table(DB::raw("({$bku16->toSql()}) AS sub"))
            ->mergeBindings($bku16)
            ->orderBy('urut')
            ->orderBy('kode')
            ->orderBy('jenis')
            ->get();

    } else if ($st_rek == '3001006966' ){ //kondisi uraian bku ketika per rekening 66
        $cek_pengeluaran = DB::table('pengeluaran_non_sp2d')->where(['tanggal' => $tgl])->count();
        if ($cek_pengeluaran > 0) {
            $keluar_non_sp2d = DB::table('pengeluaran_non_sp2d as x')
                ->selectRaw("CAST(nomor as VARCHAR) as no_kas,nomor as urut, '' as uraian,keterangan+'. Rp. ','' kode, 'PENGELUARAN NON SP2D' nm_rek6,0 as terima,isnull(SUM(x.nilai), 0) AS keluar, 2 jenis, isnull(SUM(x.nilai), 0) as netto,isnull(SUM(x.nilai), 0) as netto,isnull(SUM(x.nilai), 0) AS tot_kel, ''sp")
                ->where(['tanggal' => $tgl])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan1 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '1'])->count();
        if ($cek_penerimaan1 > 0) {
            $masuk_non_sp2d1 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR),nomor as urut,keterangan as uraian,''kode,'Deposito'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto,0 tot_kel, ''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '1'])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan2 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '2'])->count();
        if ($cek_penerimaan2 > 0) {
            $masuk_non_sp2d2 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR) as nokas,nomor as urut,keterangan as uraian,'-'kode,'Penerimaan NON SP2D'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto,0 tot_kel, ''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '2'])
                ->groupBy('nomor', 'keterangan');
        }

        $bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(b.rupiah) netto,0 tot_kel, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas=? $wherestrenk", ['4', $tgl, $st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan");

        $bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek6 as c', function ($join) {
                $join->on('b.kd_rek6', '=', 'c.kd_rek6');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6 as nm_rek6,b.rupiah as terima,0 as keluar, 1 jenis, 0 netto,0 tot_kel, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas=? $wherestrenk", ['4', $tgl, $st_rek])
            //->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku1);

        $bku3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, ''as nm_rek6,0 as terima,0 as keluar, 1 jenis,SUM(rupiah) netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas=? $wherestrenk", ['5', '1', '3', $tgl,$st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku2);

        $bku4 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'Lain-lain PAD yang sah'as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis,0 netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas=? $wherestrenk", ['5', '1', '3', $tgl,$st_rek])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku3);

        $bku5 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(rupiah) netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas=? $wherestrenk", ['5', '1', '3', $tgl, $st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku4);

        $bku6 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl, '' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'CONTRA POST' as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis, 0 netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas=? $wherestrenk", ['5', '1', '3', $tgl, $st_rek])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku5);

        $bku7 = DB::table('trhsp2d as a')
            ->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("a.tgl_kas_bud as tgl,no_kas_bud AS no_kas,a.no_kas_bud as urut,'No.SP2D :'+' '+a.no_sp2d+' '+a.keperluan+'Netto Rp. ' AS uraian,'' AS kode,'' AS nm_rek6,0 AS terima, sum(b.nilai) AS keluar,2 AS jenis,(SUM(b.nilai))-(SELECT ISNULL(SUM(nilai),0) FROM trspmpot WHERE no_spm=a.no_spm) AS netto,0 tot_kel,'' as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud=?", ['1', $tgl])
            ->groupByRaw("a.tgl_kas_bud,a.no_sp2d,no_kas_bud,a.keperluan,a.no_spm")
            ->unionAll($bku6);

        $bku8 = DB::table('trdspp as b')
            ->join('trhsp2d as a', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("'' as tgl,'' AS no_kas,a.no_kas_bud AS urut,'' AS uraian,case when b.kd_sub_kegiatan is null then a.kd_skpd+'.'+b.kd_rek6 else ( b.kd_sub_kegiatan+'.'+b.kd_rek6) END  AS kode,b.nm_rek6 AS nm_rek6,0 AS terima,b.nilai AS keluar,2 AS jenis,0 as netto,b.nilai AS tot_kel,''as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud=?", ['1', $tgl])
            ->unionAll($bku7);

        $bku9 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas as no_kas,a.no_kas as urut,'RESTITUSI<br>'+keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 AS terima,0 keluar, 2 jenis,isnull(SUM(b.rupiah), 0) as netto,0 tot_kel,''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas=?", ['3', $tgl])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan")
            ->unionAll($bku8);

        $bku10 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->leftJoin('ms_rek6 as c', 'b.kd_rek6', '=', 'c.kd_rek6')
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,''as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6,0 terima,isnull(SUM(b.rupiah), 0) AS keluar, 2 jenis,isnull(SUM(b.rupiah), 0) AS tot_kel,0 netto, ''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas=?", ['3', $tgl])
            ->groupByRaw("a.no_kas,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku9);

        $bku11 = DB::table('trkasout_ppkd as w')
            ->selectRaw("tanggal as tgl,no as no_kas, no as urut,'KOREKSI PENERIMAAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,isnull(SUM(w.nilai),0) as terima,0 as keluar,1 jenis,isnull(SUM(w.nilai),0) as netto,0 tot_kel,''sp")
            ->whereRaw("tanggal=?", [$tgl])
            ->groupByRaw("tanggal,no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
            ->unionAll($bku10);


        // $bku12 = DB::table('trkoreksi_pengeluaran as w')
        //     ->selectRaw("no as no_kas, no as urut,'KOREKSI PENGELUARAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,0 as terima,isnull(SUM(w.nilai),0) as keluar,2 jenis,isnull(SUM(w.nilai),0) as netto,''sp")
        //     ->whereRaw("tanggal=?", [$tgl])
        //     ->groupByRaw("no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
        //     ->unionAll($bku11);
        // $bku12 = DB::table('trhkasin_ppkd as a')
        //     ->join('trdkasin_ppkd as b', function ($join) {
        //         $join->on('a.no_kas', '=', 'b.no_kas');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->join('TRHOUTLAIN as c', function ($join) {
        //         $join->on('b.no_sts', '=', 'c.no_bukti');
        //         $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        //     })
        //     ->selectRaw("a.tgl_kas as tgl,a.no_kas, a.no_kas as urut,a.keterangan+'. Rp.' as uraian,'' as kode, '' as nm_rek6, 0 as terima,0 as keluar,1 jenis,isnull(sum(b.rupiah),0) as netto,'' sp ")
        //     ->whereRaw("c.status = 1 and a.tgl_kas = ?", [$tgl])
        //     ->groupByRaw("a.tgl_kas,a.no_kas,a.keterangan")->unionAll($bku11);

        // $bku121 = DB::table('trhkasin_ppkd as a')
        //     ->join('trdkasin_ppkd as b', function ($join) {
        //         $join->on('a.no_kas', '=', 'b.no_kas');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->join('TRHOUTLAIN as c', function ($join) {
        //         $join->on('b.no_sts', '=', 'c.no_bukti');
        //         $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        //     })
        //     ->selectRaw("'' as tgl,'' no_kas, a.no_kas as urut,a.keterangan as uraian,'SETOR SISA UYHD TAHUN LALU' as kode, 'Setor Sisa UYHD TAHUN LALU' as nm_rek6,isnull(sum(b.rupiah),0) as terima,0 as keluar,1  jenis,0 as netto,'' sp ")
        //     ->whereRaw("c.status = 1 and a.tgl_kas = ?", [$tgl])
        //     ->groupByRaw("a.no_kas,a.keterangan")->unionAll($bku12);


        if (isset($keluar_non_sp2d)) {
            $bku14 = $bku11->unionAll($keluar_non_sp2d);
        } else {
            $bku14 = $bku11;
        }

        if (isset($masuk_non_sp2d1)) {
            $bku15 = $bku14->unionAll($masuk_non_sp2d1);
        } else {
            $bku15 = $bku14;
        }

        if (isset($masuk_non_sp2d2)) {
            $bku16 = $bku15->unionAll($masuk_non_sp2d2);
        } else {
            $bku16 = $bku15;
        }

        $bku = DB::table(DB::raw("({$bku16->toSql()}) AS sub"))
            ->mergeBindings($bku16)
            ->orderBy('urut')
            ->orderBy('kode')
            ->orderBy('jenis')
            ->get();

    } else { // kondisi ketika cetak per tanggal rekening 16
        $bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(b.rupiah) netto,0 tot_kel, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas=? $wherestrenk", ['4', $tgl, $st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan");

        $bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek6 as c', function ($join) {
                $join->on('b.kd_rek6', '=', 'c.kd_rek6');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6 as nm_rek6,b.rupiah as terima,0 as keluar, 1 jenis, 0 netto,0 tot_kel, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas=? $wherestrenk", ['4', $tgl, $st_rek])
            //->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku1);

        $bku3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, ''as nm_rek6,0 as terima,0 as keluar, 1 jenis,SUM(rupiah) netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas=? $wherestrenk", ['5', '1', '3', $tgl,$st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku2);

        $bku4 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'Lain-lain PAD yang sah'as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis,0 netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas=? $wherestrenk", ['5', '1', '3', $tgl,$st_rek])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku3);

        $bku5 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(rupiah) netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas=? $wherestrenk", ['5', '1', '3', $tgl, $st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku4);

        $bku6 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl, '' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'CONTRA POST' as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis, 0 netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas=? $wherestrenk", ['5', '1', '3', $tgl, $st_rek])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku5);

        $bku12 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas, a.no_kas as urut,a.keterangan+'. Rp.' as uraian,'' as kode, '' as nm_rek6, 0 as terima,0 as keluar,1 jenis,isnull(sum(b.rupiah),0) as netto,0 tot_kel,'' sp ")
            ->whereRaw("c.status = 1 and a.tgl_kas = ?", [$tgl])
            ->groupByRaw("a.tgl_kas,a.no_kas,a.keterangan")->unionAll($bku6);

        $bku121 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' no_kas, a.no_kas as urut,a.keterangan as uraian,'SETOR SISA UYHD TAHUN LALU' as kode, 'Setor Sisa UYHD TAHUN LALU' as nm_rek6,isnull(sum(b.rupiah),0) as terima,0 as keluar,1  jenis,0 as netto,0 tot_kel,'' sp ")
            ->whereRaw("c.status = 1 and a.tgl_kas = ? $wherestrenk", [$tgl,$st_rek])
            ->groupByRaw("a.no_kas,a.keterangan")->unionAll($bku12);

        $bku = DB::table(DB::raw("({$bku121->toSql()}) AS sub"))
            ->mergeBindings($bku121)
            ->orderBy('urut')
            ->orderBy('kode')
            ->orderBy('jenis')
            ->get();
    }
        // if ($tgl == $tahun . '-01-01') {
        //     if($st_rek=='1'){
        //         $saldo = collect(DB::select("SELECT SUM(CASE WHEN jenis IN('1') THEN nilai1 ELSE 0 END) as nilai1,
        //         SUM(CASE WHEN jenis IN('2') THEN nilai1 ELSE 0 END) as nilai2
        //         FROM (select sum(nilai) as nilai1, 1 jenis from buku_kas where rek_bank='3001000016'
        //         UNION ALL
        //         select sum(nilai) as nilai1,2 jenis from buku_kas where rek_bank='3001006966')a "))->first();
        //     }else {
        //         $saldo = collect(DB::select("select nilai as nilai1,0 as nilai2 from buku_kas where rek_bank= ? ",[$st_rek]))->first();
        //     }
        // } else{
        //     $saldo = collect(DB::select("select nilai as nilai1,0 as nilai2 from buku_kas where rek_bank= ? ",[$st_rek]))->first();
        // }

//        dd($saldo);
    if($st_rek == '1') {
        $total_bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek3 as c', function ($join) {
                $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
            })
            ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas<?", ['4', $tgl])
            ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,4),c.nm_rek3");

        $total_bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and a.tgl_kas<?", ['5', '1', $tgl])
            ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,1)")
            ->unionAll($total_bku1);

        $total_bku3 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud = ? AND  a.jns_spp != ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $tgl])
            ->groupByRaw("a.tgl_kas_bud")
            ->unionAll($total_bku2);

        $total_bku4 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud = ? AND  a.jns_spp = ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $tgl])
            ->groupByRaw("a.tgl_kas_bud")
            ->unionAll($total_bku3);

        $total_bku5 = DB::table('pengeluaran_non_sp2d as x')
            ->selectRaw("x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,isnull(SUM(x.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("x.tanggal<?", [$tgl])
            ->groupByRaw("x.tanggal")
            ->unionAll($total_bku4);

        $total_bku6 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->selectRaw("a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis")
            ->whereRaw("a.tgl_kas<?", [$tgl])
            ->where('a.jns_trans', '3')
            ->groupByRaw("a.tgl_kas")
            ->unionAll($total_bku5);

        $total_bku7 = DB::table('trkasout_ppkd as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal<?", [$tgl])
            ->groupByRaw("w.tanggal,w.kd_rek")
            ->unionAll($total_bku6);

        // $total_bku8 = DB::table('trkoreksi_pengeluaran as w')
        //     ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENGELUARAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 2 jenis")
        //     ->whereRaw("w.tanggal<?", [$tgl])
        //     ->groupByRaw("w.tanggal,w.kd_rek")
        //     ->unionAll($total_bku7);

        $total_bku9 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'DEPOSITO' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal<?", [$tgl])
            ->where('w.jenis', '1')
            ->groupByRaw("w.tanggal")
            ->unionAll($total_bku7);

        $total_bku10 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'PENERIMAAN NON SP2D' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal<?", [$tgl])
            ->where('w.jenis', '2')
            ->groupByRaw("w.tanggal")
            ->unionAll($total_bku9);

        $total_bku11 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,'' kd_rek, 'SETOR SISA UYHD TAHUN LALU' as nama, isnull(sum(b.rupiah),0) as nilai,1 jenis")
            ->whereRaw("c.status = 1 and a.tgl_kas < ?", [$tgl])
            ->groupByRaw("a.tgl_kas")
            ->unionAll($total_bku10);

        $total_bku = DB::table(DB::raw("({$total_bku11->toSql()}) AS sub"))
            ->selectRaw("SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl")
            ->mergeBindings($total_bku11)
            ->first();

    } else if($st_rek == '3001006966') {
        $total_bku1 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->leftJoin('ms_rek3 as c', function ($join) {
            $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
        })
        ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
        ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas<? $wherestrenk ", ['4', $tgl, $st_rek])
        ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,4),c.nm_rek3");

    $total_bku2 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama,SUM(rupiah) as nilai, 1 jenis")
        ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and a.tgl_kas<? $wherestrenk", ['5', '1', $tgl, $st_rek])
        ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,1)")
        ->unionAll($total_bku1);

    $total_bku3 = DB::table('trhsp2d as a')
        ->join('trhspm as b', function ($join) {
            $join->on('a.no_spm', '=', 'b.no_spm');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->join('trhspp as c', function ($join) {
            $join->on('b.no_spp', '=', 'c.no_spp');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        })
        ->join('trdspp as d', function ($join) {
            $join->on('c.no_spp', '=', 'd.no_spp');
            $join->on('c.kd_skpd', '=', 'd.kd_skpd');
        })
        ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
        ->whereRaw("a.status_bud = ? AND  a.jns_spp != ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $tgl])
        ->groupByRaw("a.tgl_kas_bud")
        ->unionAll($total_bku2);

    $total_bku4 = DB::table('trhsp2d as a')
        ->join('trhspm as b', function ($join) {
            $join->on('a.no_spm', '=', 'b.no_spm');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->join('trhspp as c', function ($join) {
            $join->on('b.no_spp', '=', 'c.no_spp');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        })
        ->join('trdspp as d', function ($join) {
            $join->on('c.no_spp', '=', 'd.no_spp');
            $join->on('c.kd_skpd', '=', 'd.kd_skpd');
        })
        ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
        ->whereRaw("a.status_bud = ? AND  a.jns_spp = ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $tgl])
        ->groupByRaw("a.tgl_kas_bud")
        ->unionAll($total_bku3);

    $total_bku5 = DB::table('pengeluaran_non_sp2d as x')
        ->selectRaw("x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,isnull(SUM(x.nilai), 0) AS nilai, 2 jenis")
        ->whereRaw("x.tanggal<?", [$tgl])
        ->groupByRaw("x.tanggal")
        ->unionAll($total_bku4);

    $total_bku6 = DB::table('trdrestitusi as b')
        ->join('trhrestitusi as a', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            $join->on('a.no_sts', '=', 'b.no_sts');
        })
        ->selectRaw("a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis")
        ->whereRaw("a.tgl_kas<?", [$tgl])
        ->where('a.jns_trans', '3')
        ->groupByRaw("a.tgl_kas")
        ->unionAll($total_bku5);

    $total_bku7 = DB::table('trkasout_ppkd as w')
        ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
        ->whereRaw("w.tanggal<?", [$tgl])
        ->groupByRaw("w.tanggal,w.kd_rek")
        ->unionAll($total_bku6);

    // $total_bku8 = DB::table('trkoreksi_pengeluaran as w')
    //     ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENGELUARAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 2 jenis")
    //     ->whereRaw("w.tanggal<?", [$tgl])
    //     ->groupByRaw("w.tanggal,w.kd_rek")
    //     ->unionAll($total_bku7);

    $total_bku9 = DB::table('penerimaan_non_sp2d as w')
        ->selectRaw("w.tanggal,'' as kd_rek, 'DEPOSITO' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
        ->whereRaw("w.tanggal<?", [$tgl])
        ->where('w.jenis', '1')
        ->groupByRaw("w.tanggal")
        ->unionAll($total_bku7);

    $total_bku10 = DB::table('penerimaan_non_sp2d as w')
        ->selectRaw("w.tanggal,'' as kd_rek, 'PENERIMAAN NON SP2D' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
        ->whereRaw("w.tanggal<?", [$tgl])
        ->where('w.jenis', '2')
        ->groupByRaw("w.tanggal")
        ->unionAll($total_bku9);

    // $total_bku11 = DB::table('trhkasin_ppkd as a')
    //     ->join('trdkasin_ppkd as b', function ($join) {
    //         $join->on('a.no_kas', '=', 'b.no_kas');
    //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
    //     })
    //     ->join('TRHOUTLAIN as c', function ($join) {
    //         $join->on('b.no_sts', '=', 'c.no_bukti');
    //         $join->on('b.kd_skpd', '=', 'c.kd_skpd');
    //     })
    //     ->selectRaw("a.tgl_kas as tgl,'' kd_rek, 'SETOR SISA UYHD TAHUN LALU' as nama, isnull(sum(b.rupiah),0) as nilai,1 jenis")
    //     ->whereRaw("c.status = 1 and a.tgl_kas < ?", [$tgl])
    //     ->groupByRaw("a.tgl_kas")
    //     ->unionAll($total_bku10);

    $total_bku = DB::table(DB::raw("({$total_bku10->toSql()}) AS sub"))
        ->selectRaw("SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl")
        ->mergeBindings($total_bku10)
        ->first();
    } else {
        $total_bku1 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->leftJoin('ms_rek3 as c', function ($join) {
            $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
        })
        ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
        ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas<? $wherestrenk ", ['4', $tgl, $st_rek])
        ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,4),c.nm_rek3");

    $total_bku2 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama,SUM(rupiah) as nilai, 1 jenis")
        ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and a.tgl_kas<? $wherestrenk", ['5', '1', $tgl, $st_rek])
        ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,1)")
        ->unionAll($total_bku1);

    $total_bku11 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->join('TRHOUTLAIN as c', function ($join) {
            $join->on('b.no_sts', '=', 'c.no_bukti');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        })
        ->selectRaw("a.tgl_kas as tgl,'' kd_rek, 'SETOR SISA UYHD TAHUN LALU' as nama, isnull(sum(b.rupiah),0) as nilai,1 jenis")
        ->whereRaw("c.status = 1 and a.tgl_kas < ?", [$tgl])
        ->groupByRaw("a.tgl_kas")
        ->unionAll($total_bku2);

    $total_bku = DB::table(DB::raw("({$total_bku11->toSql()}) AS sub"))
        ->selectRaw("SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl")
        ->mergeBindings($total_bku11)
        ->first();
    }
        if($st_rek == '1'){
            $saldo_awal = DB::table('buku_kas')->selectRaw('SUM(nilai) as nil')->first();
        }else {
            $saldo_awal = DB::table('buku_kas')->selectRaw('SUM(nilai) as nil')->whereRaw("rek_bank = ?",[$st_rek])->first();
        }
        //dd($saldo_awal);

        // if ($tgl == $tahun . '-01-01') {
        //     $saldo_awal1 = $saldo_awal->nil;
        // } else {
        //     $saldo_awal1 = $saldo_awal->nil;
        // }
        //dd($tgl);

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => '5.02.0.00.0.00.02.0000'])->first(),
            'data_bku' => $bku,
            //'data_keluar' => $bku['keluar'],
            'tanggal' => $tgl,
            'total_bku' => $total_bku,
            'saldo_awal' => $saldo_awal,
            'no_urut' => $no_urut,
            'tanda_tangan' => $tanda_tangan
        ];

        // dd($data['tanda_tangan']);

        $view = view('bud.laporan_bendahara.cetak.bku_rincian')->with($data);

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
    //end

    //cetak B9 periode by calvin

    public function bkuRincianPeriode(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $st_rek = $request->st_rek;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $no_urut = $request->no_urut;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;
        $tahun = tahun_anggaran();

        if($st_rek == '1'){
            $wherestrenk = "";
        }else if($st_rek == '3001006966'){
            $wherestrenk = "AND a.rek_bank = ? ";
        }else{
            $wherestrenk = "AND a.rek_bank = ? ";
        }

            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd, 'kode' => 'BUD'])->first();


        if ($tgl == $tahun . '-01-01') {
            $saldo = DB::table('buku_kas')->selectRaw("'4' kd_rek, 'SALDO AWAL' nama, nilai , 1 jenis");
        }


    if($st_rek == '1'){  ////kondisi uraian bku ketika semua rekening
        $cek_pengeluaran = DB::table('pengeluaran_non_sp2d')->where(['tanggal' => $tgl])->count();
        if ($cek_pengeluaran > 0) {
            $keluar_non_sp2d = DB::table('pengeluaran_non_sp2d as x')
                ->selectRaw("CAST(nomor as VARCHAR) as no_kas,nomor as urut, '' as uraian,keterangan+'. Rp. ','' kode, 'PENGELUARAN NON SP2D' nm_rek6,0 as terima,isnull(SUM(x.nilai), 0) AS keluar, 2 jenis, isnull(SUM(x.nilai), 0) as netto,isnull(SUM(x.nilai), 0) AS tot_kel, ''sp")
                ->where(['tanggal' => $tgl])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan1 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '1'])->count();
        if ($cek_penerimaan1 > 0) {
            $masuk_non_sp2d1 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR),nomor as urut,keterangan as uraian,''kode,'Deposito'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto,0 tot_kel, ''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '1'])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan2 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '2'])->count();
        if ($cek_penerimaan2 > 0) {
            $masuk_non_sp2d2 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR) as nokas,nomor as urut,keterangan as uraian,'-'kode,'Penerimaan NON SP2D'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto, 0 tot_kel,''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '2'])
                ->groupBy('nomor', 'keterangan');
        }

        $bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(b.rupiah) netto,  0 tot_kel,''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') AND a.tgl_kas between ? AND ?", ['4', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan");

        $bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek6 as c', function ($join) {
                $join->on('b.kd_rek6', '=', 'c.kd_rek6');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6 as nm_rek6,b.rupiah as terima,0 as keluar, 1 jenis, 0 netto,  0 tot_kel,''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') AND a.tgl_kas between ? AND ?", ['4', $periode1, $periode2])
            //->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku1);

        $bku3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, ''as nm_rek6,0 as terima,0 as keluar, 1 jenis,SUM(rupiah) netto,  0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=?  AND a.tgl_kas between ? AND ?", ['5', '1', '3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku2);

        $bku4 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'Lain-lain PAD yang sah'as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis,0 netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=?  AND a.tgl_kas between ? AND ?", ['5', '1', '3', $periode1, $periode2])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku3);

        $bku5 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0as terima,0 as keluar, 1 jenis, sum(rupiah) netto,  0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>?  AND a.tgl_kas between ? AND ?", ['5', '1', '3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan")
            ->unionAll($bku4);

        $bku6 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl, '' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'CONTRA POST' as nm_rek6,SUM(rupiah) as terima,0 as keluar,1 jenis, 0 netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>?  AND a.tgl_kas between ? AND ?", ['5', '1', '3', $periode1, $periode2])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku5);

        $bku7 = DB::table('trhsp2d as a')
            ->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("a.tgl_kas_bud as tgl,no_kas_bud AS no_kas,a.no_kas_bud as urut,'No.SP2D :'+' '+a.no_sp2d+' '+a.keperluan+'Netto Rp. ' AS uraian,'' AS kode,'' AS nm_rek6,0 AS terima, sum(b.nilai) AS keluar,2 AS jenis,(SUM(b.nilai))-(SELECT ISNULL(SUM(nilai),0) FROM trspmpot WHERE no_spm=a.no_spm) AS netto, 0 as tot_kel,'' as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud between ? AND ?", ['1', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas_bud,a.no_sp2d,no_kas_bud,a.keperluan,a.no_spm")
            ->unionAll($bku6);

        $bku8 = DB::table('trdspp as b')
            ->join('trhsp2d as a', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("'' as tgl,'' AS no_kas,a.no_kas_bud AS urut,'' AS uraian,case when b.kd_sub_kegiatan is null then a.kd_skpd+'.'+b.kd_rek6 else ( b.kd_sub_kegiatan+'.'+b.kd_rek6) END  AS kode,b.nm_rek6 AS nm_rek6,0 AS terima,b.nilai AS keluar,2 AS jenis,0 as netto,b.nilai AS tot_kel,''as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud between ? AND ?", ['1', $periode1, $periode2])
            ->unionAll($bku7);

        $bku9 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas as no_kas,a.no_kas as urut,'RESTITUSI<br>'+keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 AS terima,0 keluar, 2 jenis,isnull(SUM(b.rupiah), 0) as netto,0 tot_kel,''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas between ? and ?", ['3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan")
            ->unionAll($bku8);

        $bku10 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->leftJoin('ms_rek6 as c', 'b.kd_rek6', '=', 'c.kd_rek6')
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,''as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6,0 terima,isnull(SUM(b.rupiah), 0) AS keluar, 2 jenis,0 netto, isnull(SUM(b.rupiah), 0) AS tot_kel,''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas between ? and ?", ['3', $periode1, $periode2])
            ->groupByRaw("a.no_kas,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku9);

        $bku11 = DB::table('trkasout_ppkd as w')
            ->selectRaw("tanggal as tgl,no as no_kas, no as urut,'KOREKSI PENERIMAAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,isnull(SUM(w.nilai),0) as terima,0 as keluar,1 jenis,isnull(SUM(w.nilai),0) as netto,0 tot_kel,''sp")
            ->whereRaw("tanggal between ? and ?", [$periode1, $periode2])
            ->groupByRaw("tanggal,no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
            ->unionAll($bku10);


        // $bku12 = DB::table('trkoreksi_pengeluaran as w')
        //     ->selectRaw("no as no_kas, no as urut,'KOREKSI PENGELUARAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,0 as terima,isnull(SUM(w.nilai),0) as keluar,2 jenis,isnull(SUM(w.nilai),0) as netto,''sp")
        //     ->whereRaw("tanggal=?", [$tgl])
        //     ->groupByRaw("no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
        //     ->unionAll($bku11);
        $bku12 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas, a.no_kas as urut,a.keterangan+'. Rp.' as uraian,'' as kode, '' as nm_rek6, 0 as terima,0 as keluar,1 jenis,isnull(sum(b.rupiah),0) as netto,0 tot_kel, '' sp ")
            ->whereRaw("c.status = 1 and a.tgl_kas between ? and ?", [$periode1, $periode2])
            ->groupByRaw("a.tgl_kas,a.no_kas,a.keterangan")->unionAll($bku11);

        $bku121 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' no_kas, a.no_kas as urut,a.keterangan as uraian,'SETOR SISA UYHD TAHUN LALU' as kode, 'Setor Sisa UYHD TAHUN LALU' as nm_rek6,isnull(sum(b.rupiah),0) as terima,0 as keluar,1  jenis,0 as netto,0 tot_kel,'' sp ")
            ->whereRaw("c.status = 1 and a.tgl_kas between ? and ?", [$periode1, $periode2])
            ->groupByRaw("a.no_kas,a.keterangan")->unionAll($bku12);


        if (isset($keluar_non_sp2d)) {
            $bku14 = $bku121->unionAll($keluar_non_sp2d);
        } else {
            $bku14 = $bku121;
        }

        if (isset($masuk_non_sp2d1)) {
            $bku15 = $bku14->unionAll($masuk_non_sp2d1);
        } else {
            $bku15 = $bku14;
        }

        if (isset($masuk_non_sp2d2)) {
            $bku16 = $bku15->unionAll($masuk_non_sp2d2);
        } else {
            $bku16 = $bku15;
        }

        $bku = DB::table(DB::raw("({$bku16->toSql()}) AS sub"))
            ->mergeBindings($bku16)
            ->orderBy('urut')
            ->orderBy('kode')
            ->orderBy('jenis')
            ->get();

    } else if ($st_rek == '3001006966' ){ //kondisi uraian bku ketika per rekening 66
        $cek_pengeluaran = DB::table('pengeluaran_non_sp2d')->where(['tanggal' => $tgl])->count();
        if ($cek_pengeluaran > 0) {
            $keluar_non_sp2d = DB::table('pengeluaran_non_sp2d as x')
                ->selectRaw("CAST(nomor as VARCHAR) as no_kas,nomor as urut, '' as uraian,keterangan+'. Rp. ','' kode, 'PENGELUARAN NON SP2D' nm_rek6,0 as terima,isnull(SUM(x.nilai), 0) AS keluar, 2 jenis, isnull(SUM(x.nilai), 0) as netto,isnull(SUM(x.nilai), 0) as netto,isnull(SUM(x.nilai), 0) AS tot_kel, ''sp")
                ->where(['tanggal' => $tgl])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan1 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '1'])->count();
        if ($cek_penerimaan1 > 0) {
            $masuk_non_sp2d1 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR),nomor as urut,keterangan as uraian,''kode,'Deposito'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto,0 tot_kel, ''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '1'])
                ->groupBy('nomor', 'keterangan');
        }

        $cek_penerimaan2 = DB::table('penerimaan_non_sp2d')->where(['tanggal' => $tgl, 'jenis' => '2'])->count();
        if ($cek_penerimaan2 > 0) {
            $masuk_non_sp2d2 = DB::table('penerimaan_non_sp2d as w')
                ->selectRaw("CAST(nomor as VARCHAR) as nokas,nomor as urut,keterangan as uraian,'-'kode,'Penerimaan NON SP2D'nm_rek6,isnull(SUM(w.nilai), 0) AS terima,0 as keluar,1 jenis, 0 netto,0 tot_kel, ''sp")
                ->where(['tanggal' => $tgl, 'w.jenis' => '2'])
                ->groupBy('nomor', 'keterangan');
        }

        $bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(b.rupiah) netto,0 tot_kel, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas between ? and ? $wherestrenk", ['4', $periode1,$periode2, $st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan");

        $bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek6 as c', function ($join) {
                $join->on('b.kd_rek6', '=', 'c.kd_rek6');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6 as nm_rek6,b.rupiah as terima,0 as keluar, 1 jenis, 0 netto,0 tot_kel, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas between ? and ? $wherestrenk", ['4', $periode1,$periode2, $st_rek])
            //->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku1);

        $bku3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, ''as nm_rek6,0 as terima,0 as keluar, 1 jenis,SUM(rupiah) netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas between ? and ? $wherestrenk", ['5', '1', '3', $periode1,$periode2,$st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku2);

        $bku4 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'Lain-lain PAD yang sah'as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis,0 netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas between ? and ? $wherestrenk", ['5', '1', '3', $periode1,$periode2,$st_rek])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku3);

        $bku5 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(rupiah) netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas between ? and ? $wherestrenk", ['5', '1', '3', $periode1,$periode2, $st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan")
            ->unionAll($bku4);

        $bku6 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl, '' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'CONTRA POST' as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis, 0 netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas between ? and ? $wherestrenk", ['5', '1', '3', $periode1,$periode2, $st_rek])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku5);

        $bku7 = DB::table('trhsp2d as a')
            ->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("a.tgl_kas_bud as tgl,no_kas_bud AS no_kas,a.no_kas_bud as urut,'No.SP2D :'+' '+a.no_sp2d+' '+a.keperluan+'Netto Rp. ' AS uraian,'' AS kode,'' AS nm_rek6,0 AS terima, sum(b.nilai) AS keluar,2 AS jenis,(SUM(b.nilai))-(SELECT ISNULL(SUM(nilai),0) FROM trspmpot WHERE no_spm=a.no_spm) AS netto,0 tot_kel,'' as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud between ? and ?", ['1', $periode1 , $periode2])
            ->groupByRaw("a.tgl_kas_bud,a.no_sp2d,no_kas_bud,a.keperluan,a.no_spm")
            ->unionAll($bku6);

        $bku8 = DB::table('trdspp as b')
            ->join('trhsp2d as a', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
            })
            ->selectRaw("'' as tgl,'' AS no_kas,a.no_kas_bud AS urut,'' AS uraian,case when b.kd_sub_kegiatan is null then a.kd_skpd+'.'+b.kd_rek6 else ( b.kd_sub_kegiatan+'.'+b.kd_rek6) END  AS kode,b.nm_rek6 AS nm_rek6,0 AS terima,b.nilai AS keluar,2 AS jenis,0 as netto,b.nilai AS tot_kel,''as sp")
            ->whereRaw("a.status_bud = ? AND (a.sp2d_batal=0 OR a.sp2d_batal is NULL) AND a.tgl_kas_bud between ? and ?", ['1', $periode1 , $periode2])
            ->unionAll($bku7);

        $bku9 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas as no_kas,a.no_kas as urut,'RESTITUSI<br>'+keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 AS terima,0 keluar, 2 jenis,isnull(SUM(b.rupiah), 0) as netto,0 tot_kel,''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas between ? and ?", ['3', $periode1, $periode2])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan")
            ->unionAll($bku8);

        $bku10 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->leftJoin('ms_rek6 as c', 'b.kd_rek6', '=', 'c.kd_rek6')
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,''as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6,0 terima,isnull(SUM(b.rupiah), 0) AS keluar, 2 jenis,isnull(SUM(b.rupiah), 0) AS tot_kel,0 netto, ''sp")
            ->whereRaw("a.jns_trans=? and a.tgl_kas between ? and ?", ['3', $periode1, $periode2])
            ->groupByRaw("a.no_kas,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku9);

        $bku11 = DB::table('trkasout_ppkd as w')
            ->selectRaw("tanggal as tgl,no as no_kas, no as urut,'KOREKSI PENERIMAAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,isnull(SUM(w.nilai),0) as terima,0 as keluar,1 jenis,isnull(SUM(w.nilai),0) as netto,0 tot_kel,''sp")
            ->whereRaw("tanggal between ? and ?", [$periode1, $periode2])
            ->groupByRaw("tanggal,no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
            ->unionAll($bku10);


        // $bku12 = DB::table('trkoreksi_pengeluaran as w')
        //     ->selectRaw("no as no_kas, no as urut,'KOREKSI PENGELUARAN<br>'+keterangan as uraian,kd_sub_kegiatan+'.'+kd_rek kode,nm_rek as nm_rek6,0 as terima,isnull(SUM(w.nilai),0) as keluar,2 jenis,isnull(SUM(w.nilai),0) as netto,''sp")
        //     ->whereRaw("tanggal=?", [$tgl])
        //     ->groupByRaw("no,keterangan,kd_sub_kegiatan,kd_rek,nm_rek")
        //     ->unionAll($bku11);
        // $bku12 = DB::table('trhkasin_ppkd as a')
        //     ->join('trdkasin_ppkd as b', function ($join) {
        //         $join->on('a.no_kas', '=', 'b.no_kas');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->join('TRHOUTLAIN as c', function ($join) {
        //         $join->on('b.no_sts', '=', 'c.no_bukti');
        //         $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        //     })
        //     ->selectRaw("a.tgl_kas as tgl,a.no_kas, a.no_kas as urut,a.keterangan+'. Rp.' as uraian,'' as kode, '' as nm_rek6, 0 as terima,0 as keluar,1 jenis,isnull(sum(b.rupiah),0) as netto,'' sp ")
        //     ->whereRaw("c.status = 1 and a.tgl_kas = ?", [$tgl])
        //     ->groupByRaw("a.tgl_kas,a.no_kas,a.keterangan")->unionAll($bku11);

        // $bku121 = DB::table('trhkasin_ppkd as a')
        //     ->join('trdkasin_ppkd as b', function ($join) {
        //         $join->on('a.no_kas', '=', 'b.no_kas');
        //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        //     })
        //     ->join('TRHOUTLAIN as c', function ($join) {
        //         $join->on('b.no_sts', '=', 'c.no_bukti');
        //         $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        //     })
        //     ->selectRaw("'' as tgl,'' no_kas, a.no_kas as urut,a.keterangan as uraian,'SETOR SISA UYHD TAHUN LALU' as kode, 'Setor Sisa UYHD TAHUN LALU' as nm_rek6,isnull(sum(b.rupiah),0) as terima,0 as keluar,1  jenis,0 as netto,'' sp ")
        //     ->whereRaw("c.status = 1 and a.tgl_kas = ?", [$tgl])
        //     ->groupByRaw("a.no_kas,a.keterangan")->unionAll($bku12);


        if (isset($keluar_non_sp2d)) {
            $bku14 = $bku11->unionAll($keluar_non_sp2d);
        } else {
            $bku14 = $bku11;
        }

        if (isset($masuk_non_sp2d1)) {
            $bku15 = $bku14->unionAll($masuk_non_sp2d1);
        } else {
            $bku15 = $bku14;
        }

        if (isset($masuk_non_sp2d2)) {
            $bku16 = $bku15->unionAll($masuk_non_sp2d2);
        } else {
            $bku16 = $bku15;
        }

        $bku = DB::table(DB::raw("({$bku16->toSql()}) AS sub"))
            ->mergeBindings($bku16)
            ->orderBy('urut')
            ->orderBy('kode')
            ->orderBy('jenis')
            ->get();

    } else { // kondisi ketika cetak per tanggal rekening 16
        $bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(b.rupiah) netto,0 tot_kel, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas between ? and ? $wherestrenk", ['4', $periode1, $periode2 , $st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan");

        $bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek6 as c', function ($join) {
                $join->on('b.kd_rek6', '=', 'c.kd_rek6');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, c.nm_rek6 as nm_rek6,b.rupiah as terima,0 as keluar, 1 jenis, 0 netto,0 tot_kel, ''as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and  b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas between ? and ? $wherestrenk", ['4', $periode1, $periode2 , $st_rek])
            //->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6,c.nm_rek6")
            ->unionAll($bku1);

        $bku3 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, ''as nm_rek6,0 as terima,0 as keluar, 1 jenis,SUM(rupiah) netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas between ? and ? $wherestrenk", ['5', '1', '3', $periode1, $periode2 ,$st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku2);

        $bku4 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'Lain-lain PAD yang sah'as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis,0 netto,0 tot_kel, '' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus=? and a.tgl_kas between ? and ? $wherestrenk", ['5', '1', '3', $periode1, $periode2 ,$st_rek])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku3);

        $bku5 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas,a.no_kas as urut,a.keterangan+'. Rp. ' as uraian,'' as kode, '' as nm_rek6,0 as terima,0 as keluar, 1 jenis, SUM(rupiah) netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas between ? and ? $wherestrenk", ['5', '1', '3', $periode1, $periode2 , $st_rek])
            ->groupByRaw("a.tgl_kas,a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku4);

        $bku6 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("'' as tgl, '' as no_kas,a.no_kas as urut,a.keterangan as uraian,b.kd_sub_kegiatan+'.'+b.kd_rek6 as kode, 'CONTRA POST' as nm_rek6,SUM(rupiah) as terima,0 as keluar, 1 jenis, 0 netto, 0 tot_kel,'' as sp")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and pot_khusus<>? and a.tgl_kas between ? and ? $wherestrenk", ['5', '1', '3', $periode1, $periode2 , $st_rek])
            ->groupByRaw("a.no_kas,keterangan,b.kd_sub_kegiatan,b.kd_rek6")
            ->unionAll($bku5);

        $bku12 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,a.no_kas, a.no_kas as urut,a.keterangan+'. Rp.' as uraian,'' as kode, '' as nm_rek6, 0 as terima,0 as keluar,1 jenis,isnull(sum(b.rupiah),0) as netto,0 tot_kel,'' sp ")
            ->whereRaw("c.status = 1 and a.tgl_kas between ? and ?", [$periode1, $periode2])
            ->groupByRaw("a.tgl_kas,a.no_kas,a.keterangan")->unionAll($bku6);

        $bku121 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("'' as tgl,'' no_kas, a.no_kas as urut,a.keterangan as uraian,'SETOR SISA UYHD TAHUN LALU' as kode, 'Setor Sisa UYHD TAHUN LALU' as nm_rek6,isnull(sum(b.rupiah),0) as terima,0 as keluar,1  jenis,0 as netto,0 tot_kel,'' sp ")
            ->whereRaw("c.status = 1 and a.tgl_kas between ? and ? $wherestrenk", [$periode1, $periode2,$st_rek])
            ->groupByRaw("a.no_kas,a.keterangan")->unionAll($bku12);

        $bku = DB::table(DB::raw("({$bku121->toSql()}) AS sub"))
            ->mergeBindings($bku121)
            ->orderBy('urut')
            ->orderBy('kode')
            ->orderBy('jenis')
            ->get();
    }
        // if ($tgl == $tahun . '-01-01') {
        //     if($st_rek=='1'){
        //         $saldo = collect(DB::select("SELECT SUM(CASE WHEN jenis IN('1') THEN nilai1 ELSE 0 END) as nilai1,
        //         SUM(CASE WHEN jenis IN('2') THEN nilai1 ELSE 0 END) as nilai2
        //         FROM (select sum(nilai) as nilai1, 1 jenis from buku_kas where rek_bank='3001000016'
        //         UNION ALL
        //         select sum(nilai) as nilai1,2 jenis from buku_kas where rek_bank='3001006966')a "))->first();
        //     }else {
        //         $saldo = collect(DB::select("select nilai as nilai1,0 as nilai2 from buku_kas where rek_bank= ? ",[$st_rek]))->first();
        //     }
        // } else{
        //     $saldo = collect(DB::select("select nilai as nilai1,0 as nilai2 from buku_kas where rek_bank= ? ",[$st_rek]))->first();
        // }

//        dd($saldo);
    if($st_rek == '1') {
        $total_bku1 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->leftJoin('ms_rek3 as c', function ($join) {
                $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
            })
            ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas<?", ['4', $periode1])
            ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,4),c.nm_rek3");

        $total_bku2 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama,SUM(rupiah) as nilai, 1 jenis")
            ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and a.tgl_kas<?", ['5', '1', $periode1])
            ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,1)")
            ->unionAll($total_bku1);

        $total_bku3 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud = ? AND  a.jns_spp != ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $periode1])
            ->groupByRaw("a.tgl_kas_bud")
            ->unionAll($total_bku2);

        $total_bku4 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("a.status_bud = ? AND  a.jns_spp = ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $periode1])
            ->groupByRaw("a.tgl_kas_bud")
            ->unionAll($total_bku3);

        $total_bku5 = DB::table('pengeluaran_non_sp2d as x')
            ->selectRaw("x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,isnull(SUM(x.nilai), 0) AS nilai, 2 jenis")
            ->whereRaw("x.tanggal<?", [$periode1])
            ->groupByRaw("x.tanggal")
            ->unionAll($total_bku4);

        $total_bku6 = DB::table('trdrestitusi as b')
            ->join('trhrestitusi as a', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->selectRaw("a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis")
            ->whereRaw("a.tgl_kas<?", [$periode1])
            ->where('a.jns_trans', '3')
            ->groupByRaw("a.tgl_kas")
            ->unionAll($total_bku5);

        $total_bku7 = DB::table('trkasout_ppkd as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal<?", [$periode1])
            ->groupByRaw("w.tanggal,w.kd_rek")
            ->unionAll($total_bku6);

        // $total_bku8 = DB::table('trkoreksi_pengeluaran as w')
        //     ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENGELUARAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 2 jenis")
        //     ->whereRaw("w.tanggal<?", [$periode1])
        //     ->groupByRaw("w.tanggal,w.kd_rek")
        //     ->unionAll($total_bku7);

        $total_bku9 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'DEPOSITO' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal<?", [$periode1])
            ->where('w.jenis', '1')
            ->groupByRaw("w.tanggal")
            ->unionAll($total_bku7);

        $total_bku10 = DB::table('penerimaan_non_sp2d as w')
            ->selectRaw("w.tanggal,'' as kd_rek, 'PENERIMAAN NON SP2D' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
            ->whereRaw("w.tanggal<?", [$periode1])
            ->where('w.jenis', '2')
            ->groupByRaw("w.tanggal")
            ->unionAll($total_bku9);

        $total_bku11 = DB::table('trhkasin_ppkd as a')
            ->join('trdkasin_ppkd as b', function ($join) {
                $join->on('a.no_kas', '=', 'b.no_kas');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('TRHOUTLAIN as c', function ($join) {
                $join->on('b.no_sts', '=', 'c.no_bukti');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("a.tgl_kas as tgl,'' kd_rek, 'SETOR SISA UYHD TAHUN LALU' as nama, isnull(sum(b.rupiah),0) as nilai,1 jenis")
            ->whereRaw("c.status = 1 and a.tgl_kas < ?", [$periode1])
            ->groupByRaw("a.tgl_kas")
            ->unionAll($total_bku10);

        $total_bku = DB::table(DB::raw("({$total_bku11->toSql()}) AS sub"))
            ->selectRaw("SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl")
            ->mergeBindings($total_bku11)
            ->first();

    } else if($st_rek == '3001006966') {
        $total_bku1 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->leftJoin('ms_rek3 as c', function ($join) {
            $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
        })
        ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
        ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas<? $wherestrenk ", ['4', $periode1, $st_rek])
        ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,4),c.nm_rek3");

    $total_bku2 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama,SUM(rupiah) as nilai, 1 jenis")
        ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and a.tgl_kas<? $wherestrenk", ['5', '1', $periode1, $st_rek])
        ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,1)")
        ->unionAll($total_bku1);

    $total_bku3 = DB::table('trhsp2d as a')
        ->join('trhspm as b', function ($join) {
            $join->on('a.no_spm', '=', 'b.no_spm');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->join('trhspp as c', function ($join) {
            $join->on('b.no_spp', '=', 'c.no_spp');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        })
        ->join('trdspp as d', function ($join) {
            $join->on('c.no_spp', '=', 'd.no_spp');
            $join->on('c.kd_skpd', '=', 'd.kd_skpd');
        })
        ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA NON GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
        ->whereRaw("a.status_bud = ? AND  a.jns_spp != ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $periode1])
        ->groupByRaw("a.tgl_kas_bud")
        ->unionAll($total_bku2);

    $total_bku4 = DB::table('trhsp2d as a')
        ->join('trhspm as b', function ($join) {
            $join->on('a.no_spm', '=', 'b.no_spm');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->join('trhspp as c', function ($join) {
            $join->on('b.no_spp', '=', 'c.no_spp');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        })
        ->join('trdspp as d', function ($join) {
            $join->on('c.no_spp', '=', 'd.no_spp');
            $join->on('c.kd_skpd', '=', 'd.kd_skpd');
        })
        ->selectRaw("a.tgl_kas_bud, '' kd_rek, 'PENGELUARAN BELANJA GAJI' nama,isnull(SUM(d.nilai), 0) AS nilai, 2 jenis")
        ->whereRaw("a.status_bud = ? AND  a.jns_spp = ? AND (c.sp2d_batal=0 OR c.sp2d_batal is NULL) AND a.tgl_kas_bud<?", ['1', '4', $periode1])
        ->groupByRaw("a.tgl_kas_bud")
        ->unionAll($total_bku3);

    $total_bku5 = DB::table('pengeluaran_non_sp2d as x')
        ->selectRaw("x.tanggal,'' kd_rek, 'PENGELUARAN NON SP2D' nama,isnull(SUM(x.nilai), 0) AS nilai, 2 jenis")
        ->whereRaw("x.tanggal<?", [$periode1])
        ->groupByRaw("x.tanggal")
        ->unionAll($total_bku4);

    $total_bku6 = DB::table('trdrestitusi as b')
        ->join('trhrestitusi as a', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            $join->on('a.no_sts', '=', 'b.no_sts');
        })
        ->selectRaw("a.tgl_kas,'' kd_rek, 'RESTITUSI' nama,isnull(SUM(b.rupiah), 0) AS nilai, 2 jenis")
        ->whereRaw("a.tgl_kas<?", [$periode1])
        ->where('a.jns_trans', '3')
        ->groupByRaw("a.tgl_kas")
        ->unionAll($total_bku5);

    $total_bku7 = DB::table('trkasout_ppkd as w')
        ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENERIMAAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
        ->whereRaw("w.tanggal<?", [$periode1])
        ->groupByRaw("w.tanggal,w.kd_rek")
        ->unionAll($total_bku6);

    // $total_bku8 = DB::table('trkoreksi_pengeluaran as w')
    //     ->selectRaw("w.tanggal,'' as kd_rek, 'KOREKSI PENGELUARAN' nama,isnull(SUM(w.nilai), 0) AS nilai, 2 jenis")
    //     ->whereRaw("w.tanggal<?", [$periode1])
    //     ->groupByRaw("w.tanggal,w.kd_rek")
    //     ->unionAll($total_bku7);

    $total_bku9 = DB::table('penerimaan_non_sp2d as w')
        ->selectRaw("w.tanggal,'' as kd_rek, 'DEPOSITO' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
        ->whereRaw("w.tanggal<?", [$periode1])
        ->where('w.jenis', '1')
        ->groupByRaw("w.tanggal")
        ->unionAll($total_bku7);

    $total_bku10 = DB::table('penerimaan_non_sp2d as w')
        ->selectRaw("w.tanggal,'' as kd_rek, 'PENERIMAAN NON SP2D' nama,isnull(SUM(w.nilai), 0) AS nilai, 1 jenis")
        ->whereRaw("w.tanggal<?", [$periode1])
        ->where('w.jenis', '2')
        ->groupByRaw("w.tanggal")
        ->unionAll($total_bku9);

    // $total_bku11 = DB::table('trhkasin_ppkd as a')
    //     ->join('trdkasin_ppkd as b', function ($join) {
    //         $join->on('a.no_kas', '=', 'b.no_kas');
    //         $join->on('a.kd_skpd', '=', 'b.kd_skpd');
    //     })
    //     ->join('TRHOUTLAIN as c', function ($join) {
    //         $join->on('b.no_sts', '=', 'c.no_bukti');
    //         $join->on('b.kd_skpd', '=', 'c.kd_skpd');
    //     })
    //     ->selectRaw("a.tgl_kas as tgl,'' kd_rek, 'SETOR SISA UYHD TAHUN LALU' as nama, isnull(sum(b.rupiah),0) as nilai,1 jenis")
    //     ->whereRaw("c.status = 1 and a.tgl_kas < ?", [$periode1])
    //     ->groupByRaw("a.tgl_kas")
    //     ->unionAll($total_bku10);

    $total_bku = DB::table(DB::raw("({$total_bku10->toSql()}) AS sub"))
        ->selectRaw("SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl")
        ->mergeBindings($total_bku10)
        ->first();
    } else {
        $total_bku1 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->leftJoin('ms_rek3 as c', function ($join) {
            $join->on(DB::raw("left(b.kd_rek6,4)"), '=', 'c.kd_rek3');
        })
        ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,4) as kd_rek, UPPER(c.nm_rek3) as nama,SUM(rupiah) as nilai, 1 jenis")
        ->whereRaw("LEFT(b.kd_rek6,1) IN (?) and b.kd_rek6 not in ('420101040001','410416010001') and a.tgl_kas<? $wherestrenk ", ['4', $periode1, $st_rek])
        ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,4),c.nm_rek3");

    $total_bku2 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->selectRaw("a.tgl_kas,LEFT(b.kd_rek6,1) as kd_rek, 'CONTRA POST' as nama,SUM(rupiah) as nilai, 1 jenis")
        ->whereRaw("LEFT(b.kd_rek6,1) IN (?,?) and a.tgl_kas<? $wherestrenk", ['5', '1', $periode1, $st_rek])
        ->groupByRaw("a.tgl_kas,LEFT(b.kd_rek6,1)")
        ->unionAll($total_bku1);

    $total_bku11 = DB::table('trhkasin_ppkd as a')
        ->join('trdkasin_ppkd as b', function ($join) {
            $join->on('a.no_kas', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })
        ->join('TRHOUTLAIN as c', function ($join) {
            $join->on('b.no_sts', '=', 'c.no_bukti');
            $join->on('b.kd_skpd', '=', 'c.kd_skpd');
        })
        ->selectRaw("a.tgl_kas as tgl,'' kd_rek, 'SETOR SISA UYHD TAHUN LALU' as nama, isnull(sum(b.rupiah),0) as nilai,1 jenis")
        ->whereRaw("c.status = 1 and a.tgl_kas < ?", [$periode1])
        ->groupByRaw("a.tgl_kas")
        ->unionAll($total_bku2);

    $total_bku = DB::table(DB::raw("({$total_bku11->toSql()}) AS sub"))
        ->selectRaw("SUM(CASE WHEN jenis IN('1') THEN nilai ELSE 0 END) as trm_sbl,SUM(CASE WHEN jenis IN('2') THEN nilai ELSE 0 END) as klr_sbl")
        ->mergeBindings($total_bku11)
        ->first();
    }
        if($st_rek == '1'){
            $saldo_awal = DB::table('buku_kas')->selectRaw('SUM(nilai) as nil')->first();
        }else {
            $saldo_awal = DB::table('buku_kas')->selectRaw('SUM(nilai) as nil')->whereRaw("rek_bank = ?",[$st_rek])->first();
        }
        //dd($saldo_awal);

        // if ($tgl == $tahun . '-01-01') {
        //     $saldo_awal1 = $saldo_awal->nil;
        // } else {
        //     $saldo_awal1 = $saldo_awal->nil;
        // }
        //dd($tgl);

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => '5.02.0.00.0.00.02.0000'])->first(),
            'data_bku' => $bku,
            //'data_keluar' => $bku['keluar'],
            'periode1' => $periode1,
            'periode2' => $periode2,
            'tanggal' => $tgl,
            'total_bku' => $total_bku,
            'saldo_awal' => $saldo_awal,
            'no_urut' => $no_urut,
            'tanda_tangan' => $tanda_tangan
        ];

        // dd($data['tanda_tangan']);

        $view = view('bud.laporan_bendahara.cetak.bku_rincianperiode')->with($data);

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

    //end cetak b9 periode
    public function pajakDaerah(Request $request)
    {
        $req = $request->all();
        $kd_skpd = Auth::user()->kd_skpd;
        $wilayah = $req['wilayah'];

        $data1 = DB::table('ms_wilayah')->select('kd_pengirim')->where(['kd_wilayah' => $wilayah])->get();
        $data3 = '';
        foreach ($data1 as $data) {
            $data3 = $data->kd_pengirim;
        }

        $pkb_all = "('410101010001','410101020001','410101030001','410101010002','410101020002','410101030002','410101010004','410101020004','410101030004','410101050001','410101050002','410101050004','410101080001','410101060001','410101080002','410101060002','410101080004','410101060004','410101130001','410101100001','410101100004','410101120001')";
        $denda_pkb_all = "('410412010001','410412010002','410412010003','410412010005','410412010006','410412010008','410412010010','410412010012','410412010013')";
        $tgk_pkb_all = "('4110114')";
        $bbn_all = "('4110201','4110202','4110203','4110204','4110205','410102010001','410102020001','410102030001','410102050001','410102060001','410102080001','410102100001','410102120001')";
        $denda_bbn_all = "('410412020001','410412020002','410412020003','410412020005','410412020006','410412020008','410412020010','410412020012','410412020013')";
        $denda_bbn_tka = "('4140704')";
        $pka_all = "('')";
        $bbnka_all = "('4110213')";
        $pap_all = "('410104010001')";
        $denda_pap_all = "('410412040001')";
        $sp3_all = "('430105010001')";
        $pbb_kb_all = "('410103010001','410103040001','410103020001')";
        $jumlah_all = "('410101010001','410101020001','410101030001','410101010002','410101020002','410101030002','410101010004','410101020004','410101030004','410101050001','410101050002','410101050004','410101080001','410101060001','410101080002','410101060002','410101080004','410101060004','410101130001','410101100001','410101100004','410412010001','410412010002','410412010003','410412010005','410412010006','410412010008','410412010010','410412010012','410412010013','4110114','4110201','4110202','4110203','4110204','4110205','410102010001','410102020001','410102030001','410102050001','410102060001','410102080001','410102100001','410102120001','410412020001','410412020002','410412020003','410412020005','410412020006','410412020008','410412020010','410412020012','410412020013','410101120001','410104010001','4110401','410412040001','430105010001','410103010001','410103040001','410103020001','4140704')";

        if ($req['pilihan'] == '1') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("MONTH(a.tgl_kas)=?", [$req['bulan_perbulan']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $pajak_daerah = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("a.kd_pengirim,a.nm_pengirim,ISNULL(b.pkb,0) pkb, ISNULL(b.denda_pkb,0) denda_pkb, ISNULL(b.tgk_pkb,0) tgk_pkb, ISNULL(b.bbn,0) bbn, ISNULL(b.denda_bbn,0) denda_bbn,ISNULL(b.denda_bbntka,0) denda_bbntka , ISNULL(b.pka,0) pka, ISNULL(b.bbn_ka,0) bbn_ka, ISNULL(b.pap,0) pap, ISNULL(b.denda_pap,0) denda_pap, ISNULL(b.sp3,0) sp3,ISNULL(b.pbb_kb,0) pbb_kb, ISNULL(b.jumlah,0) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=?", ['4101'])
                // ->orderByRaw("cast(a.kd_pengirim AS int)")
                ->orderByRaw("kd_pengirim")
                ->get();
        } elseif ($req['pilihan'] == '2') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("a.tgl_kas=?", [$req['tgl_kas_pertanggal']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $pajak_daerah = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("a.kd_pengirim,a.nm_pengirim,ISNULL(b.pkb,0) pkb, ISNULL(b.denda_pkb,0) denda_pkb, ISNULL(b.tgk_pkb,0) tgk_pkb, ISNULL(b.bbn,0) bbn, ISNULL(b.denda_bbn,0) denda_bbn,ISNULL(b.denda_bbntka,0) denda_bbntka, ISNULL(b.pka,0) pka, ISNULL(b.bbn_ka,0) bbn_ka, ISNULL(b.pap,0) pap,ISNULL(b.denda_pap,0) denda_pap, ISNULL(b.sp3,0) sp3,ISNULL(b.pbb_kb,0) pbb_kb, ISNULL(b.jumlah,0) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=?", ['4101'])
                // ->orderByRaw("cast(a.kd_pengirim AS int)")
                ->orderByRaw("kd_pengirim")
                ->get();
        } elseif ($req['pilihan'] == '32') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd ,a.tgl_kas
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("b.sumber=? AND (MONTH(a.tgl_kas)>=? AND MONTH(a.tgl_kas)<=?)", [$req['pengirim'], $req['bulan1_pengirim'], $req['bulan2_pengirim']])
                ->groupByRaw("b.sumber,a.kd_skpd,a.tgl_kas");

            $pajak_daerah = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("a.kd_pengirim,a.nm_pengirim, b.tgl_kas, ISNULL(b.pkb,0) pkb, ISNULL(b.denda_pkb,0) denda_pkb, ISNULL(b.tgk_pkb,0) tgk_pkb, ISNULL(b.bbn,0) bbn, ISNULL(b.denda_bbn,0) denda_bbn,ISNULL(b.denda_bbntka,0) denda_bbntka, ISNULL(b.pka,0) pka, ISNULL(b.bbn_ka,0) bbn_ka, ISNULL(b.pap,0) pap,ISNULL(b.denda_pap,0) denda_pap, ISNULL(b.sp3,0) sp3,ISNULL(b.pbb_kb,0) pbb_kb, ISNULL(b.jumlah,0) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=? AND a.kd_pengirim=?", ['4101', $req['pengirim']])
                // ->orderByRaw("cast(a.kd_pengirim AS int)")
                ->orderByRaw("kd_pengirim")
                ->get();
        } elseif ($req['pilihan'] == '31') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("b.sumber=? AND a.tgl_kas=?", [$req['pengirim'], $req['tgl_kas_pengirim']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $pajak_daerah = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("a.kd_pengirim,a.nm_pengirim,ISNULL(b.pkb,0) pkb, ISNULL(b.denda_pkb,0) denda_pkb, ISNULL(b.tgk_pkb,0) tgk_pkb, ISNULL(b.bbn,0) bbn, ISNULL(b.denda_bbn,0) denda_bbn,ISNULL(b.denda_bbntka,0) denda_bbntka, ISNULL(b.pka,0) pka, ISNULL(b.bbn_ka,0) bbn_ka, ISNULL(b.pap,0) pap,ISNULL(b.denda_pap,0) denda_pap, ISNULL(b.sp3,0) sp3,ISNULL(b.pbb_kb,0) pbb_kb, ISNULL(b.jumlah,0) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=? AND a.kd_pengirim=?", ['4101', $req['pengirim']])
                // ->orderByRaw("cast(a.kd_pengirim AS int)")
                ->orderByRaw("kd_pengirim")
                ->get();
        } elseif ($req['pilihan'] == '41') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("a.tgl_kas=? AND b.sumber IN ($data3)", [$req['tgl_kas_wilayah']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $pajak_daerah = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("a.kd_pengirim,a.nm_pengirim,ISNULL(b.pkb,0) pkb, ISNULL(b.denda_pkb,0) denda_pkb, ISNULL(b.tgk_pkb,0) tgk_pkb, ISNULL(b.bbn,0) bbn, ISNULL(b.denda_bbn,0) denda_bbn,ISNULL(b.denda_bbntka,0) denda_bbntka, ISNULL(b.pka,0) pka, ISNULL(b.bbn_ka,0) bbn_ka, ISNULL(b.pap,0) pap,ISNULL(b.denda_pap,0) denda_pap, ISNULL(b.sp3,0) sp3,ISNULL(b.pbb_kb,0) pbb_kb, ISNULL(b.jumlah,0) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=? AND a.kd_pengirim IN ($data3)", ['4101'])
                // ->orderByRaw("cast(a.kd_pengirim AS int)")
                ->orderByRaw("kd_pengirim")
                ->get();
        } elseif ($req['pilihan'] == '42') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("b.sumber IN ($data3) AND (MONTH(a.tgl_kas)>=? AND MONTH(a.tgl_kas)<=?)", [$req['bulan1_wilayah'], $req['bulan2_wilayah']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $pajak_daerah = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("a.kd_pengirim,a.nm_pengirim,ISNULL(b.pkb,0) pkb, ISNULL(b.denda_pkb,0) denda_pkb, ISNULL(b.tgk_pkb,0) tgk_pkb, ISNULL(b.bbn,0) bbn, ISNULL(b.denda_bbn,0) denda_bbn,ISNULL(b.denda_bbntka,0) denda_bbntka, ISNULL(b.pka,0) pka, ISNULL(b.bbn_ka,0) bbn_ka, ISNULL(b.pap,0) pap,ISNULL(b.denda_pap,0) denda_pap, ISNULL(b.sp3,0) sp3,ISNULL(b.pbb_kb,0) pbb_kb, ISNULL(b.jumlah,0) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=? AND a.kd_pengirim IN ($data3)", ['4101'])
                // ->orderByRaw("cast(a.kd_pengirim AS int)")
                ->orderByRaw("kd_pengirim")
                ->get();
        } elseif ($req['pilihan'] == '5') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("MONTH(a.tgl_kas)>=? AND MONTH(a.tgl_kas)<=?", [$req['bulan_rekap1'], $req['bulan_rekap2']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $pajak_daerah = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("a.kd_pengirim,a.nm_pengirim,ISNULL(b.pkb,0) pkb, ISNULL(b.denda_pkb,0) denda_pkb, ISNULL(b.tgk_pkb,0) tgk_pkb, ISNULL(b.bbn,0) bbn, ISNULL(b.denda_bbn,0) denda_bbn,ISNULL(b.denda_bbntka,0) denda_bbntka, ISNULL(b.pka,0) pka, ISNULL(b.bbn_ka,0) bbn_ka, ISNULL(b.pap,0) pap, ISNULL(b.denda_pap,0) denda_pap, ISNULL(b.sp3,0) sp3,ISNULL(b.pbb_kb,0) pbb_kb, ISNULL(b.jumlah,0) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=?", ['4101'])
                // ->orderByRaw("cast(a.kd_pengirim AS int)")
                ->orderByRaw("kd_pengirim")
                ->get();
        }

        if ($req['pilihan'] == '1') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("MONTH(a.tgl_kas)<=?", [$req['bulan_perbulan']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $total_pajak_sebelumnya = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("SUM(ISNULL(b.pkb,0)) pkb, SUM(ISNULL(b.denda_pkb,0)) denda_pkb, SUM(ISNULL(b.tgk_pkb,0)) tgk_pkb, SUM(ISNULL(b.bbn,0)) bbn, SUM(ISNULL(b.denda_bbn,0)) denda_bbn,SUM(ISNULL(b.denda_bbntka,0)) denda_bbntka, SUM(ISNULL(b.pka,0)) pka, SUM(ISNULL(b.bbn_ka,0)) bbn_ka, SUM(ISNULL(b.pap,0)) pap,sum(ISNULL(b.denda_pap,0)) denda_pap, SUM(ISNULL(b.sp3,0)) sp3,SUM(ISNULL(b.pbb_kb,0)) pbb_kb, SUM(ISNULL(b.jumlah,0)) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=?", ['4101'])
                ->get();
        } elseif ($req['pilihan'] == '2') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("a.tgl_kas <=?", [$req['tgl_kas_sbl_pertanggal']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $total_pajak_sebelumnya = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("SUM(ISNULL(b.pkb,0)) pkb, SUM(ISNULL(b.denda_pkb,0)) denda_pkb, SUM(ISNULL(b.tgk_pkb,0)) tgk_pkb, SUM(ISNULL(b.bbn,0)) bbn, SUM(ISNULL(b.denda_bbn,0)) denda_bbn,SUM(ISNULL(b.denda_bbntka,0)) denda_bbntka, SUM(ISNULL(b.pka,0)) pka, SUM(ISNULL(b.bbn_ka,0)) bbn_ka, SUM(ISNULL(b.pap,0)) pap,sum(ISNULL(b.denda_pap,0)) denda_pap, SUM(ISNULL(b.sp3,0)) sp3, SUM(ISNULL(b.pbb_kb,0)) pbb_kb, SUM(ISNULL(b.jumlah,0)) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=?", ['4101'])
                ->get();
        } elseif ($req['pilihan'] == '31') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("b.sumber=? AND  a.tgl_kas<=?", [$req['pengirim'], $req['tgl_kas_sbl_pengirim']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $total_pajak_sebelumnya = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("SUM(ISNULL(b.pkb,0)) pkb, SUM(ISNULL(b.denda_pkb,0)) denda_pkb, SUM(ISNULL(b.tgk_pkb,0)) tgk_pkb, SUM(ISNULL(b.bbn,0)) bbn, SUM(ISNULL(b.denda_bbn,0)) denda_bbn,SUM(ISNULL(b.denda_bbntka,0)) denda_bbntka, SUM(ISNULL(b.pka,0)) pka, SUM(ISNULL(b.bbn_ka,0)) bbn_ka, SUM(ISNULL(b.pap,0)) pap,sum(ISNULL(b.denda_pap,0)) denda_pap, SUM(ISNULL(b.sp3,0)) sp3, SUM(ISNULL(b.pbb_kb,0)) pbb_kb, SUM(ISNULL(b.jumlah,0)) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=? and a.kd_pengirim=?", ['4101', $req['pengirim']])
                ->get();
        } elseif ($req['pilihan'] == '32') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("b.sumber=? AND  MONTH(a.tgl_kas)<=?", [$req['pengirim'], $req['bulan2_pengirim']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $total_pajak_sebelumnya = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("SUM(ISNULL(b.pkb,0)) pkb, SUM(ISNULL(b.denda_pkb,0)) denda_pkb, SUM(ISNULL(b.tgk_pkb,0)) tgk_pkb, SUM(ISNULL(b.bbn,0)) bbn, SUM(ISNULL(b.denda_bbn,0)) denda_bbn,SUM(ISNULL(b.denda_bbntka,0)) denda_bbntka, SUM(ISNULL(b.pka,0)) pka, SUM(ISNULL(b.bbn_ka,0)) bbn_ka, SUM(ISNULL(b.pap,0)) pap,sum(ISNULL(b.denda_pap,0)) denda_pap, SUM(ISNULL(b.sp3,0)) sp3, SUM(ISNULL(b.pbb_kb,0)) pbb_kb, SUM(ISNULL(b.jumlah,0)) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=? and a.kd_pengirim=?", ['4101', $req['pengirim']])
                ->get();
        } elseif ($req['pilihan'] == '41') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("b.sumber in ($data3) AND  a.tgl_kas<=?", [$req['tgl_kas_sbl_wilayah']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $total_pajak_sebelumnya = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("SUM(ISNULL(b.pkb,0)) pkb, SUM(ISNULL(b.denda_pkb,0)) denda_pkb, SUM(ISNULL(b.tgk_pkb,0)) tgk_pkb, SUM(ISNULL(b.bbn,0)) bbn, SUM(ISNULL(b.denda_bbn,0)) denda_bbn,SUM(ISNULL(b.denda_bbntka,0)) denda_bbntka, SUM(ISNULL(b.pka,0)) pka, SUM(ISNULL(b.bbn_ka,0)) bbn_ka, SUM(ISNULL(b.pap,0)) pap,sum(ISNULL(b.denda_pap,0)) denda_pap, SUM(ISNULL(b.sp3,0)) sp3, SUM(ISNULL(b.pbb_kb,0)) pbb_kb, SUM(ISNULL(b.jumlah,0)) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=? AND a.kd_pengirim in ($data3)", ['4101'])
                ->get();
        } elseif ($req['pilihan'] == '42') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("b.sumber in ($data3) AND  MONTH(a.tgl_kas)<=?", [$req['bulan2_wilayah']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $total_pajak_sebelumnya = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("SUM(ISNULL(b.pkb,0)) pkb, SUM(ISNULL(b.denda_pkb,0)) denda_pkb, SUM(ISNULL(b.tgk_pkb,0)) tgk_pkb, SUM(ISNULL(b.bbn,0)) bbn, SUM(ISNULL(b.denda_bbn,0)) denda_bbn,SUM(ISNULL(b.denda_bbntka,0)) denda_bbntka, SUM(ISNULL(b.pka,0)) pka, SUM(ISNULL(b.bbn_ka,0)) bbn_ka, SUM(ISNULL(b.pap,0)) pap,sum(ISNULL(b.denda_pap,0)) denda_pap, SUM(ISNULL(b.sp3,0)) sp3, SUM(ISNULL(b.pbb_kb,0)) pbb_kb, SUM(ISNULL(b.jumlah,0)) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=? AND a.kd_pengirim in ($data3)", ['4101'])
                ->get();
        } elseif ($req['pilihan'] == '5') {
            $join1 = DB::table('trhkasin_ppkd as a')
                ->join('trdkasin_ppkd as b', function ($join) {
                    $join->on('a.no_sts', '=', 'b.no_sts');
                    $join->on('a.no_kas', '=', 'b.no_kas');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })
                ->selectRaw("b.sumber,a.kd_skpd
                                ,SUM(CASE WHEN b.kd_rek6 IN $pkb_all THEN b.rupiah ELSE 0 END) as pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pkb_all THEN b.rupiah ELSE 0 END) as denda_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $tgk_pkb_all THEN b.rupiah ELSE 0 END) as tgk_pkb
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbn_all THEN b.rupiah ELSE 0 END) as bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_all THEN b.rupiah ELSE 0 END) as denda_bbn
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_bbn_tka THEN b.rupiah ELSE 0 END) as denda_bbntka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pka_all THEN b.rupiah ELSE 0 END) as pka
                                ,SUM(CASE WHEN b.kd_rek6 IN $bbnka_all THEN b.rupiah ELSE 0 END) as bbn_ka
                                ,SUM(CASE WHEN b.kd_rek6 IN $pap_all THEN b.rupiah ELSE 0 END) as pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $denda_pap_all THEN b.rupiah ELSE 0 END) as denda_pap
                                ,SUM(CASE WHEN b.kd_rek6 IN $sp3_all THEN b.rupiah ELSE 0 END) as sp3
                                ,SUM(CASE WHEN b.kd_rek6 IN $pbb_kb_all THEN b.rupiah ELSE 0 END) as pbb_kb
                                ,SUM(CASE WHEN b.kd_rek6 IN $jumlah_all THEN b.rupiah ELSE 0 END) as jumlah")
                ->whereRaw("MONTH(a.tgl_kas)>=? AND MONTH(a.tgl_kas)<=?", [$req['bulan_rekap1'], $req['bulan_rekap2']])
                ->groupByRaw("b.sumber,a.kd_skpd");

            $total_pajak_sebelumnya = DB::table('ms_pengirim as a')
                ->leftJoinSub($join1, 'b', function ($join) {
                    $join->on('a.kd_pengirim', '=', 'b.sumber');
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                })->selectRaw("SUM(ISNULL(b.pkb,0)) pkb, SUM(ISNULL(b.denda_pkb,0)) denda_pkb, SUM(ISNULL(b.tgk_pkb,0)) tgk_pkb, SUM(ISNULL(b.bbn,0)) bbn, SUM(ISNULL(b.denda_bbn,0)) denda_bbn,SUM(ISNULL(b.denda_bbntka,0)) denda_bbntka, SUM(ISNULL(b.pka,0)) pka, SUM(ISNULL(b.bbn_ka,0)) bbn_ka, SUM(ISNULL(b.pap,0)) pap,sum(ISNULL(b.denda_pap,0)) denda_pap, SUM(ISNULL(b.sp3,0)) sp3, SUM(ISNULL(b.pbb_kb,0)) pbb_kb,SUM(ISNULL(b.jumlah,0)) jumlah")
                ->whereRaw("LEFT(jns_rek,4)=?", ['4101'])
                ->get();
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'pilihan' => $req['pilihan'],
            'wilayah' => DB::table('ms_wilayah')->select('nm_wilayah')->where(['kd_wilayah' => $req['wilayah']])->first(),
            'data_awal' => $req,
            'pajak_daerah' => $pajak_daerah,
            'total_pajak_sebelumnya' => $total_pajak_sebelumnya
        ];

        $view = view('bud.laporan_bendahara.cetak.pajak_daerah')->with($data);

        if ($req['jenis_print'] == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($req['jenis_print'] == 'layar') {
            return $view;
        }
    }

    public function rekapGaji(Request $request)
    {
        $req = $request->all();

        $rekap_gaji1 = DB::table('trhsp2d as a')
            ->selectRaw("a.kd_skpd,a.nm_skpd,a.no_sp2d nomor,a.nilai nilai_sp2d,0 as IWP1,0 AS IWP8,0 AS JKK,0 JKM,0 AS BPJS,0 AS PPH21,0 AS TAPERUM,0 AS HKPG")
            ->whereRaw("a.no_sp2d like '%GJ%' and (a.sp2d_batal IS NULL OR a.sp2d_batal !=?)", ['1'])
            ->where(function ($query) use ($req) {
                if ($req['kd_skpd']) {
                    $query->where('a.kd_skpd', $req['kd_skpd']);
                }
            })
            ->where(function ($query) use ($req) {
                if ($req['pilihan'] == '12' || $req['pilihan'] == '22') {
                    $query->where(DB::raw("MONTH(tgl_sp2d)"), $req['bulan'])->where('a.jenis_beban', '1');
                }
                if ($req['pilihan'] == '13' || $req['pilihan'] == '23') {
                    $query->whereBetween('tgl_sp2d', [$req['periode1'], $req['periode2']]);
                }
            })
            ->groupByRaw("a.kd_skpd,a.nm_skpd,a.no_sp2d,a.nilai");

        $rekap_gaji2 = DB::table('trhsp2d as a')
            ->join('trspmpot as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->selectRaw("a.kd_skpd,a.nm_skpd,a.no_sp2d nomor,0 nilai_sp2d,SUM(CASE WHEN b.map_pot='210108010001a' THEN b.nilai ELSE 0 END) AS IWP1,SUM(CASE WHEN b.map_pot='210108010001b' THEN b.nilai ELSE 0 END) AS IWP8,SUM(CASE WHEN b.kd_rek6='210103010001' THEN b.nilai ELSE 0 END) AS JKK,SUM(CASE WHEN b.kd_rek6='210104010001' THEN b.nilai ELSE 0 END) AS JKM,SUM(CASE WHEN b.kd_rek6='210102010001' THEN b.nilai ELSE 0 END) AS BPJS,SUM(CASE WHEN b.kd_rek6='210105010001' THEN b.nilai ELSE 0 END) AS PPH21,SUM(CASE WHEN b.kd_rek6='' THEN 0 ELSE 0 END) AS TAPERUM,SUM(CASE WHEN b.kd_rek6 in ('210601010007','210601010003','210601010011','210601010009') THEN b.nilai ELSE 0 END) AS HKPG")
            ->whereRaw("a.no_sp2d like '%GJ%' and (a.sp2d_batal IS NULL OR a.sp2d_batal !=?)", ['1'])
            ->where(function ($query) use ($req) {
                if ($req['kd_skpd']) {
                    $query->where('a.kd_skpd', $req['kd_skpd']);
                }
            })
            ->where(function ($query) use ($req) {
                if ($req['pilihan'] == '12' || $req['pilihan'] == '22') {
                    $query->where(DB::raw("MONTH(tgl_sp2d)"), $req['bulan'])->where('a.jenis_beban', '1');
                }
                if ($req['pilihan'] == '13' || $req['pilihan'] == '23') {
                    $query->whereBetween('tgl_sp2d', [$req['periode1'], $req['periode2']]);
                }
            })
            ->groupByRaw("a.kd_skpd,a.nm_skpd,a.no_sp2d,a.nilai")
            ->unionAll($rekap_gaji1);

        $rekap_gaji = DB::table(DB::raw("({$rekap_gaji2->toSql()}) AS sub"))
            ->selectRaw("kd_skpd,nm_skpd,nomor,sum(nilai_sp2d) nilai_sp2d, sum(IWP1) IWP1, sum(IWP8) IWP8, sum(JKK) JKK, sum(JKM) JKM, sum(BPJS) BPJS, sum(PPH21) PPH21, sum(TAPERUM) TAPERUM, sum(HKPG) HKPG, sum(IWP1) + sum(IWP8) + sum(JKK) + sum(JKM) + sum(BPJS) + sum(PPH21) + sum(TAPERUM) + sum(HKPG) as Total")
            ->mergeBindings($rekap_gaji2)
            ->groupByRaw("kd_skpd,nm_skpd,nomor")
            ->orderBy('kd_skpd')
            ->get();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $req['pilihan'],
            'data_awal' => $req,
            'rekap_gaji' => $rekap_gaji
        ];

        $view = view('bud.laporan_bendahara.cetak.rekap_gaji')->with($data);

        if ($req['jenis_print'] == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($req['jenis_print'] == 'layar') {
            return $view;
        }
    }

    public function rekapBBKasda(Request $request)
    {
        $req = $request->all();
        $kd_skpd  = $request->kd_skpd;
        $kd_rek  = $request->rekening;
        $ttd = $request->ttd;

        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? AND kode IN ('PA','KPA')", [$ttd]))->first();


        $buku_besar_kasda = DB::select("SELECT tgl_kas, no_kas,no_sts,keterangan,0 as debet, kredit from (
					select a.kd_skpd, a.tgl_kas, a.no_kas,a.no_sts, b.kd_rek6, keterangan+', '+(select nm_skpd from ms_skpd where kd_skpd=a.kd_skpd) keterangan,0 as debet, rupiah as kredit
					from trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts AND a.no_kas=b.no_kas
					where pot_khusus<>3 AND jns_trans NOT IN ('2') AND b.kd_rek6 = ?
					union all
					select '5.02.0.00.0.00.02.0000' kd_skpd, a.tgl_kas, a.no_kas,a.no_sts, '410415030001' kd_rek6, keterangan+', '+(select nm_skpd from ms_skpd where kd_skpd=a.kd_skpd) keterangan,0 as debet, rupiah as kredit
					from trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts and a.no_kas=b.no_kas
					where jns_trans IN ('5') AND jns_cp='1' AND pot_khusus='3'
					union all
					select '5.02.0.00.0.00.02.0000' kd_skpd, a.tgl_kas, a.no_kas, a.no_sts, '4141009' kd_rek6, keterangan+', '+(select nm_skpd from ms_skpd where kd_skpd=a.kd_skpd) keterangan,0 as debet, rupiah as kredit
					from trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts and a.no_kas=b.no_kas
					where jns_trans IN ('2')
					) a
					where kd_skpd= ? and kd_rek6= ? and tgl_kas between ? AND ?
					order by tgl_kas, no_kas, no_sts", [$req['rekening'], $req['kd_skpd'], $req['rekening'], $req['periode1'], $req['periode2']]);

        $periode1  = $request->periode1;
        $periode2  = $request->periode2;
        $periode1 = tanggal($periode1);
        $periode2 = tanggal($periode2);

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'   => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'rekening'   => DB::table('ms_rek6')->select('kd_rek6', 'nm_rek6')->where(['kd_rek6' => $kd_rek])->first(),
            'periode1' => $periode1,
            'periode2' => $periode2,
            'tanda_tangan' => $tanda_tangan,
            'data_awal' => $req,
            'buku_besar_kasda' => $buku_besar_kasda
        ];

        $view = view('bud.laporan_bendahara.cetak.buku_besar_kasda')->with($data);

        if ($req['jenis_print'] == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($req['jenis_print'] == 'layar') {
            return $view;
        }
    }

    public function pembantuPengeluaran(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $tipe = $request->tipe;
        $jenis_print = $request->jenis_print;

        if ($pilihan == '1') {
            $where = "AND a.tgl_kas_bud=?";
            $where2 = "AND a.tgl_kas_bud < ?";
        } elseif ($pilihan == '2') {
            $where = "a.tgl_kas_bud BETWEEN ? AND ?";
            $where2 = "a.tgl_kas_bud < ?";
        }

        $pengeluaran1 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("'1' + a.no_sp2d as urut0,1 as urut, a.no_kas_bud as urut2,a.no_kas_bud, a.no_sp2d ,a.tgl_sp2d, a.kd_skpd,a.keperluan, a.jns_spp,a.jenis_beban,a.nmrekan,c.pimpinan, '' kd_sub_kegiatan,''kd_rek6,SUM(d.nilai) nilai, '' no_bukti, count(a.no_sp2d) as jumlah")
            ->whereRaw("a.status_bud=? AND (c.sp2d_batal=? OR c.sp2d_batal is NULL)", ['1', 0])
            ->where(function ($query) use ($tipe) {
                if ($tipe == '0') {
                    $query->where('a.jns_spp', '4');
                } else if ($tipe == '1') {
                    $query->where('a.jns_spp', '!=', '4');
                }
            })
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->where('a.tgl_kas_bud', $tgl);
                } else if ($pilihan == '2') {
                    $query->whereBetween('a.tgl_kas_bud', [$periode1, $periode2]);
                }
            })
            ->groupByRaw("a.no_kas_bud, a.no_sp2d ,a.tgl_sp2d, a.kd_skpd,a.keperluan,a.jns_spp,a.jenis_beban,a.nmrekan,c.pimpinan");

        $pengeluaran2 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("'1' +a.no_sp2d as urut0,2 urut, '' no_kas_bud, a.no_kas_bud AS urut2, '' no_sp2d, '' tgl_sp2d, a.kd_skpd, '' keperluan, '' jns_spp, '' jenis_beban, '' nmrekan, '' pimpinan, d.kd_sub_kegiatan, d.kd_rek6, d.nilai, d.no_bukti, 0 AS jumlah")
            ->whereRaw("a.status_bud=? AND (c.sp2d_batal=? OR c.sp2d_batal is NULL)", ['1', 0])
            ->where(function ($query) use ($tipe) {
                if ($tipe == '0') {
                    $query->where('a.jns_spp', '4');
                } else if ($tipe == '1') {
                    $query->where('a.jns_spp', '!=', '4');
                }
            })
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->where('a.tgl_kas_bud', $tgl);
                } else if ($pilihan == '2') {
                    $query->whereBetween('a.tgl_kas_bud', [$periode1, $periode2]);
                }
            })
            ->groupByRaw("a.no_kas_bud, a.no_sp2d ,a.tgl_sp2d, a.kd_skpd,a.keperluan, d.kd_sub_kegiatan, d.kd_rek6,d.nilai,d.no_bukti,d.kd")
            ->union($pengeluaran1);

        $pengeluaran = DB::table(DB::raw("({$pengeluaran2->toSql()}) AS sub"))
            ->mergeBindings($pengeluaran2)
            ->orderByRaw("urut0,urut,cast(no_kas_bud as int),kd_sub_kegiatan,kd_rek6")
            ->get();

        $pengeluaran_lalu = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('c.kd_skpd', '=', 'd.kd_skpd');
            })
            ->selectRaw("sum(d.nilai) as nilai")
            ->whereRaw("a.status_bud=? AND (c.sp2d_batal=? OR c.sp2d_batal is NULL)", ['1', 0])
            ->where(function ($query) use ($tipe) {
                if ($tipe == '0') {
                    $query->where('a.jns_spp', '4');
                } else if ($tipe == '1') {
                    $query->where('a.jns_spp', '!=', '4');
                }
            })
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                if ($pilihan == '1') {
                    $query->where('a.tgl_kas_bud', '<', $tgl);
                } else if ($pilihan == '2') {
                    $query->where('a.tgl_kas_bud', '<', $periode1);
                }
            })
            ->first();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'tipe' => $tipe,
            'spasi' => $spasi,
            'data_pengeluaran' => $pengeluaran,
            'tanda_tangan' => DB::table('ms_ttd')
                ->where(['kode' => 'BUD', 'nip' => $ttd])
                ->first(),
            'total_pengeluaran_lalu' => $pengeluaran_lalu->nilai
        ];

        $view = view('bud.laporan_bendahara.cetak.pembantu_pengeluaran')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function retribusi(Request $request)
    {
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        $retribusi = DB::select("SELECT * from(
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						ISNULL(e.nm_pengirim, '') nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas=? AND a.kd_skpd !='1.20.15.17'  AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND LEFT(a.kd_rek6,6) NOT IN ('410412','410416') AND a.kd_rek6 NOT IN ('420101040001','420101040002','420101040003')
					and a.sumber<>'y'
					GROUP BY b.no_kas,nm_pengirim, f.nm_skpd
					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						c.nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					WHERE b.tgl_kas=? AND a.kd_skpd !='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND LEFT(a.kd_rek6,6) NOT IN ('410412','410416') AND a.kd_rek6 NOT IN ('420101040001','420101040002','420101040003')
					and a.sumber<>'y'
					UNION ALL
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						''nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas=? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND a.kd_rek6 NOT IN ('420101040001','420101040002','420101040003')
					and a.sumber<>'y'
					GROUP BY b.no_kas,f.nm_skpd
					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						b.keterangan nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas  AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					WHERE b.tgl_kas=? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND a.kd_rek6 NOT IN ('420101040001','420101040002','420101040003')
					and a.sumber<>'y'

					UNION ALL
					SELECT
							1 AS urut,
							'' no_sts,
							'' kd_skpd,
							nm_skpd,
							'' kd_sub_kegiatan,
							'' kd_rek6,
							[no] as no_kas,
							'' tgl_kas,
							'' nm_pengirim,
							'' nm_rek6,
							0 rupiah
						FROM
							trkasout_ppkd
						WHERE
							tanggal = ? AND LEFT(kd_rek,4) IN ('4102','4103','4104','4201','4202') AND LEFT(kd_rek,5) NOT IN ('41407') AND kd_rek NOT IN ('420101040001','420101040002','420101040003')
						UNION ALL
						SELECT
								2 AS urut,
								[no] as no_sts,
								kd_skpd,
								'' nm_skpd,
								''kd_sub_kegiatan,
								kd_rek kd_rek6,
								[no] no_kas,
								[tanggal] tgl_kas,
								'' nm_pengirim,
								keterangan+' '+nm_rek nm_rek6,
								nilai rupiah
							FROM
							trkasout_ppkd
							WHERE
							tanggal = ?
							AND LEFT(kd_rek,4) IN ('4102','4103','4104','4201','4202') AND LEFT(kd_rek,5) NOT IN ('41407') AND kd_rek NOT IN ('420101040001','420101040002','420101040003')
					) a

					order by cast(no_kas as int),urut", [$tgl, $tgl, $tgl, $tgl, $tgl, $tgl]);

        $retribusi_lalu = collect(DB::select("SELECT sum(rupiah) as nilai from(
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						ISNULL(e.nm_pengirim, '') nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas <=? AND a.kd_skpd !='1.20.15.17'  AND LEFT(a.kd_rek6,4) IN ('4102')  and a.sumber<>'y'
					GROUP BY b.no_kas,nm_pengirim, f.nm_skpd
					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						c.nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					WHERE b.tgl_kas<=? AND a.kd_skpd !='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102')  and a.sumber<>'y'

					UNION ALL
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						''nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas<=? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102') and a.sumber<>'y'
					GROUP BY b.no_kas,f.nm_skpd
					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						b.keterangan nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas  AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					WHERE b.tgl_kas<=? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102') and a.sumber<>'y'

					UNION ALL
					SELECT
							1 AS urut,
							'' no_sts,
							'' kd_skpd,
							nm_skpd,
							'' kd_sub_kegiatan,
							'' kd_rek6,
							[no] as no_kas,
							'' tgl_kas,
							'' nm_pengirim,
							'' nm_rek6,
							0 rupiah
						FROM
							trkasout_ppkd
						WHERE
							tanggal <= ? AND LEFT(kd_rek,4) IN ('4102')
						UNION ALL
						SELECT
								2 AS urut,
								[no] as no_sts,
								kd_skpd,
								'' nm_skpd,
								''kd_sub_kegiatan,
								kd_rek kd_rek6,
								[no] no_kas,
								[tanggal] tgl_kas,
								'' nm_pengirim,
								keterangan+' '+nm_rek nm_rek6,
								nilai rupiah
							FROM
							trkasout_ppkd
							WHERE
							tanggal <= ?
							AND LEFT(kd_rek,4) IN ('4102')
					) a", [$tgl, $tgl, $tgl, $tgl, $tgl, $tgl]))->first();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'tanggal' => $tgl,
            'daftar_retribusi' => $retribusi,
            'total_retribusi_lalu' => $retribusi_lalu->nilai,
            'spasi' => $spasi,
            'tanda_tangan' => DB::table('ms_ttd')
                ->where(['kode' => 'BUD', 'nip' => $ttd])
                ->first(),
        ];

        $view = view('bud.laporan_bendahara.cetak.retribusi')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function kartuKendali(Request $request)
    {
        $data = [
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->orderBy('kd_skpd')
                ->get(),
            'jenis_anggaran' => DB::table('tb_status_anggaran')
                ->select('kode', 'nama')
                ->where(['status_aktif' => '1'])
                ->get(),
            'daftar_ttd' => DB::table('ms_ttd')
                ->select('nama', 'nip', 'jabatan', 'kd_skpd')
                ->whereIn('kode', ['PA', 'KPA'])
                ->orderBy('nama')
                ->get(),
        ];

        return view('bud.kartu_kendali.index')->with($data);
    }

    public function kegiatanKartuKendali(Request $request)
    {
        $kd_skpd = $request->kd_skpd;

        $jenis_anggaran = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();

        $data = DB::table('trskpd as a')
            ->join('ms_sub_kegiatan as b', function ($join) {
                $join->on('a.kd_sub_kegiatan', '=', 'b.kd_sub_kegiatan');
            })
            ->select('a.kd_sub_kegiatan', 'a.nm_sub_kegiatan')
            ->where(['a.kd_skpd' => $kd_skpd, 'jns_ang' => $jenis_anggaran->jns_ang])
            ->orderBy('a.kd_sub_kegiatan')
            ->get();

        return response()->json($data);
    }

    public function rekeningKartuKendali(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        $jenis_anggaran = DB::table('trhrka')
            ->select('jns_ang')
            ->where(['kd_skpd' => $kd_skpd, 'status' => '1'])
            ->orderByDesc('tgl_dpa')
            ->first();

        $data = DB::table('trdrka as a')
            ->join('ms_rek6 as b', function ($join) {
                $join->on('a.kd_rek6', '=', 'b.kd_rek6');
            })
            ->select('a.kd_rek6', 'b.nm_rek6')
            ->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'jns_ang' => $jenis_anggaran->jns_ang])
            ->orderBy('a.kd_rek6')
            ->get();

        return response()->json($data);
    }

    public function cetakKegiatanKartuKendali(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_rek = $request->kd_rek;
        $jns_ang = $request->jns_ang;
        $periode_awal = $request->periode_awal;
        $periode_akhir = $request->periode_akhir;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        $program = substr($kd_sub_kegiatan, 0, 7);
        $kegiatan = substr($kd_sub_kegiatan, 0, 12);

        $data = [
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'program' => DB::table('trskpd')
                ->select('nm_program', DB::raw("'$program' as kd_program"))
                ->where(['kd_program' => $program])
                ->first(),
            'kegiatan' => DB::table('trskpd')
                ->select('nm_kegiatan', DB::raw("'$kegiatan' as kd_kegiatan"))
                ->where(['kd_kegiatan' => $kegiatan])
                ->first(),
            'sub_kegiatan' => DB::table('trskpd')
                ->select('nm_sub_kegiatan', DB::raw("'$kd_sub_kegiatan' as kd_sub_kegiatan"))
                ->where(['kd_sub_kegiatan' => $kd_sub_kegiatan])
                ->first(),
            'periode_awal' => $periode_awal,
            'periode_akhir' => $periode_akhir,
            'rincian' =>  DB::select("exec kartu_kendali ?,?,?,?,?", array($jns_ang, $kd_skpd, $kd_sub_kegiatan, $periode_awal, $periode_akhir)),
            'jns_ang' => $jns_ang
        ];

        $view =  view('bud.kartu_kendali.cetak_per_sub_kegiatan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function cetakRekeningKartuKendali(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $kd_rek = $request->kd_rek;
        $jns_ang = $request->jns_ang;
        $periode_awal = $request->periode_awal;
        $periode_akhir = $request->periode_akhir;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        $kegiatan = substr($kd_sub_kegiatan, 0, 12);

        $data = [
            'nilai_ang' => DB::table('trdrka as a')
                ->selectRaw("sum(nilai)nilai, (select sum(nilai) from trdrka where no_trdrka=a.no_trdrka and jns_ang=?) as nilai_ubah", [$jns_ang])
                ->where(['a.kd_sub_kegiatan' => $kd_sub_kegiatan, 'a.kd_skpd' => $kd_skpd, 'a.kd_rek6' => $kd_rek])
                ->groupBy('no_trdrka')
                ->first(),
            'header' => DB::table('config_app')
                ->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')
                ->first(),
            'skpd' => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => $kd_skpd])
                ->first(),
            'kegiatan' => DB::table('trskpd')
                ->select('nm_kegiatan', DB::raw("'$kegiatan' as kd_kegiatan"))
                ->where(['kd_kegiatan' => $kegiatan])
                ->first(),
            'sub_kegiatan' => DB::table('trskpd')
                ->select('nm_sub_kegiatan', DB::raw("'$kd_sub_kegiatan' as kd_sub_kegiatan"))
                ->where(['kd_sub_kegiatan' => $kd_sub_kegiatan])
                ->first(),
            'rekening' => DB::table('ms_rek6')
                ->select('nm_rek6', DB::raw("'$kd_sub_kegiatan' as kd_rek6"))
                ->where(['kd_rek6' => $kd_rek])
                ->first(),
            'periode_awal' => $periode_awal,
            'periode_akhir' => $periode_akhir,
            'rincian' =>  DB::select("exec kartu_kendali_rek ?,?,?,?,?", array($kd_rek, $kd_skpd, $kd_sub_kegiatan, $periode_awal, $periode_akhir)),
            'jns_ang' => $jns_ang
        ];
        // return $data['nilai_ang'];
        $view = view('bud.kartu_kendali.cetak_per_rekening')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function registerCp(Request $request)
    {
        $skpd_global = Auth::user()->kd_skpd;
        $pilihan = $request->pilihan;
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $ttd = $request->ttd;
        $kd_skpd = $request->kd_skpd;
        $kd_unit = $request->kd_unit;
        $jenis_print = $request->jenis_print;
        $spasi = $request->spasi;

        // if ($ttd) {
        //     $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD', 'PA'])->first();
        // } else {
        //     $tanda_tangan = null;
        // }

         $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        if ($pilihan == '1') {
            $register_cp = DB::select("SELECT a.kd_skpd,a.nm_skpd,isnull(b.total,0) total from ms_skpd a
                                            left join
                                            (SELECT a.kd_skpd,(select nm_skpd from ms_skpd where a.kd_skpd=kd_skpd)nm_skpd,isnull(sum(cp),0) total
                                            from
                                            (SELECT no_kas ,no_sts,tgl_kas, kd_skpd , jns_trans, jns_cp, pot_khusus,keterangan,kd_sub_kegiatan,kd_rek6,
                                            SUM(isnull((case when rtrim(jns_cp) in  ('3','2','1') and (tgl_kas  BETWEEN ? AND ?) then z.nilai else 0 end),0)) AS cp
                                            from ( SELECT d.no_kas,d.no_sts , d.kd_skpd , d.jns_trans, d.jns_cp, pot_khusus,rupiah as nilai,d.tgl_sts,d.tgl_kas,keterangan,d.kd_sub_kegiatan,kd_rek6  from
                                            trdkasin_pkd c INNER JOIN trhkasin_pkd d ON c.no_sts = d.no_sts AND c.kd_skpd = d.kd_skpd where
                                            jns_trans in('5','1') AND pot_khusus in('0','1','2') ) z
                                            group by  no_kas ,no_sts,tgl_kas, kd_skpd , jns_trans, jns_cp, pot_khusus,z.keterangan,kd_sub_kegiatan,kd_rek6) a
                                            group by a.kd_skpd) b on a.kd_skpd=b.kd_skpd
                                            order by a.kd_skpd", [$tgl1, $tgl2]);
        } else if ($pilihan == '2') {
            $register_cp = DB::select("SELECT '1' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,'' kd_sub_kegiatan, ''kd_rek,nilai FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,SUM(rupiah) as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas  BETWEEN ? AND ?)
						AND LEFT(kd_skpd,17)=?

						UNION ALL

						SELECT '2' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,kd_sub_kegiatan,kd_rek,nilai FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas  BETWEEN ? AND ?)
						AND LEFT(kd_skpd,17)=?
						ORDER BY tgl_kas,no_kas,jenis", [$tgl1, $tgl2, $kd_skpd, $tgl1, $tgl2, $kd_skpd]);
        } else if ($pilihan == '3') {
            $register_cp = DB::select("SELECT '1' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,'' kd_sub_kegiatan, ''kd_rek,nilai FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,SUM(rupiah) as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas  BETWEEN ? AND ?)
						AND LEFT(kd_skpd,22)=?

						UNION ALL

						SELECT '2' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,kd_sub_kegiatan,kd_rek,nilai FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas  BETWEEN ? AND ?)
						AND LEFT(kd_skpd,22)=?
						ORDER BY tgl_kas,no_kas,jenis", [$tgl1, $tgl2, $kd_unit, $tgl1, $tgl2, $kd_unit]);
        } else if ($pilihan == '4') {
            $register_cp = DB::select("SELECT '1' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,'' kd_sub_kegiatan, ''kd_rek,nilai FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,SUM(rupiah) as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas  BETWEEN ? AND ?)

						UNION ALL

						SELECT '2' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,kd_sub_kegiatan,kd_rek,nilai FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas  BETWEEN ? AND ?)
						ORDER BY tgl_kas,no_kas,jenis", [$tgl1, $tgl2, $tgl1, $tgl2]);
        }

        if ($pilihan == '1') {
            $register_lalu = 0;
        } elseif ($pilihan == '2') {
            $register_lalu = collect(DB::select("SELECT SUM(nilai) as nilai_lalu FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas <?) AND LEFT(kd_skpd,17)=?", [$tgl1, $kd_skpd]))->first();
        } elseif ($pilihan == '3') {
            $register_lalu = collect(DB::select("SELECT SUM(nilai) as nilai_lalu FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas <?) AND LEFT(kd_skpd,22)=?", [$tgl1, $kd_unit]))->first();
        } elseif ($pilihan == '4') {
            $register_lalu = collect(DB::select("SELECT SUM(nilai) as nilai_lalu FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas <?)", [$tgl1]))->first();
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'tanda_tangan' => $tanda_tangan,
            'tanggal1' => $tgl1,
            'tanggal2' => $tgl2,
            'pilihan' => $pilihan,
            'tanda_tangan' => $tanda_tangan,
            'data_register' => $register_cp,
            'spasi' => $spasi,
            'total_lalu' => $register_lalu,
        ];

        $judul = 'REGISTER CP';

        $view = view('bud.laporan_bendahara.cetak.register_cp')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename= $judul.xls");
            echo $view;
        }
    }

    public function registerCpRinci(Request $request)
    {
        $skpd_global = Auth::user()->kd_skpd;
        $pilihan = $request->pilihan;
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $ttd = $request->ttd;
        $kd_skpd = $request->kd_skpd;
        $kd_unit = $request->kd_unit;
        $jenis_print = $request->jenis_print;
        $spasi = $request->spasi;

        // if ($ttd) {
        //     $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD', 'PA'])->first();
        // } else {
        //     $tanda_tangan = null;
        // }

        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        if ($pilihan == '1') {
            $register_cp = DB::select("SELECT a.kd_skpd,a.nm_skpd
                        ,ISNULL(hkpg,0) hkpg
                        ,ISNULL(pot_lain,0) pot_lain
                        ,ISNULL(cp,0) cp
                        ,ISNULL(ls_peg,0) ls_peg
                        ,ISNULL(ls_brng,0) ls_brng
                        ,ISNULL(ls_modal,0) ls_modal
                        ,ISNULL(phl,0) ls_phl
                        ,ISNULL(gu,0) gu
                        ,ISNULL(up_gu_peg,0) up_gu_peg
                        ,ISNULL(up_gu_brng,0) up_gu_brng
                        ,ISNULL(up_gu_modal,0) up_gu_modal
                        ,ISNULL(total,0) total
                        FROM ms_skpd a LEFT JOIN
                    (SELECT kd_skpd
                    ,SUM(CASE  WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '1' THEN nilai ELSE 0 END) AS hkpg
                    ,SUM(CASE  WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '2' THEN nilai ELSE 0 END) AS pot_lain
                    ,SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus NOT IN ('1','2') THEN   nilai ELSE 0 END) AS cp
                    ,SUM(CASE  WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) IN ('5101') THEN nilai ELSE 0 END) AS ls_peg
                    ,SUM(  CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) = '5102' THEN    nilai ELSE 0 END) AS ls_brng
                    ,SUM(   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) not in ('5201','5202','5203','5204','5205','5206','5102','5101') THEN    nilai ELSE 0 END) AS phl
                    ,SUM(  CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai ELSE 0 END) AS ls_modal
                    ,sum(CASE   WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) = '1101' THEN    nilai ELSE 0 END)as gu
                    ,SUM (  CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5101') THEN nilai    ELSE 0 END) AS up_gu_peg
                    ,SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5102') THEN nilai    ELSE 0 END) AS up_gu_brng
                    ,SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai    ELSE 0 END) AS up_gu_modal
                    ,SUM (nilai) AS total
                    FROM
                    (
                    SELECT a.no_kas,a.tgl_kas, a.kd_skpd,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
                    FROM (
                    SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, LEFT(kd_rek6,4) as kd_rek, SUM(rupiah) as nilai FROM trhkasin_ppkd a
                    INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
                    WHERE jns_trans IN ('5','1')
                    GROUP BY a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd,LEFT(kd_rek6,4)) a
                    LEFT JOIN
                    (SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
                    INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
                    WHERE jns_trans IN ('5','1')
                    GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
                    ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
                    WHERE (tgl_kas BETWEEN ? AND ?)
                    GROUP BY a.kd_skpd) b
                    ON a.kd_skpd=b.kd_skpd
                    order by a.kd_skpd", [$tgl1, $tgl2]);
        } else if ($pilihan == '2') {
            $register_cp = DB::select("SELECT '1' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,'' kd_sub_kegiatan, ''kd_rek,
						SUM ( CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '1' THEN nilai ELSE 0 END) AS hkpg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '2' THEN nilai ELSE 0 END) AS pot_lain,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus NOT IN ('1','2') THEN   nilai ELSE 0 END) AS cp,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) IN ('5101') THEN nilai ELSE 0 END) AS ls_peg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) = '5102' THEN    nilai ELSE 0 END) AS ls_brng,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai ELSE 0 END) AS ls_modal,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5105') THEN nilai ELSE 0 END) AS ls_phl,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) = '1101' THEN    nilai ELSE 0 END) AS up_gu,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5101') THEN nilai    ELSE 0 END) AS tu_peg,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5102') THEN nilai    ELSE 0 END) AS tu_brng,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai    ELSE 0 END) AS tu_modal,
                        SUM (nilai) AS total
							 FROM(

							SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, nilai, jns_trans,jns_cp,pot_khusus,kd_rek
							FROM (
							SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,kd_rek6 as kd_rek, SUM(rupiah) as nilai
							FROM trhkasin_ppkd a
							INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
							WHERE jns_trans IN ('5','1')
							GROUP BY a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd,keterangan,kd_rek6) a
							LEFT JOIN
							(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
							INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
							WHERE jns_trans IN ('5','1')
							GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
							ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
							WHERE (tgl_kas  BETWEEN ? AND ?)
							AND left(kd_skpd,17)=?
							GROUP BY no_kas,tgl_kas,kd_skpd,keterangan

							UNION ALL

							SELECT '2' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,kd_sub_kegiatan,kd_rek,
						SUM (    CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '1' THEN nilai ELSE 0 END) AS hkpg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '2' THEN nilai ELSE 0 END) AS pot_lain,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus NOT IN ('1','2') THEN   nilai ELSE 0 END) AS cp,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) IN ('5101') THEN nilai ELSE 0 END) AS ls_peg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) = '5102' THEN    nilai ELSE 0 END) AS ls_brng,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai ELSE 0 END) AS ls_modal,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5105') THEN nilai ELSE 0 END) AS ls_phl,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) = '1101' THEN    nilai ELSE 0 END) AS gu,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5101') THEN nilai    ELSE 0 END) AS up_gu_peg,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5102') THEN nilai    ELSE 0 END) AS up_gu_brng,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai    ELSE 0 END) AS up_gu_modal,
                        SUM (nilai) AS total
							 FROM(
							SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
							FROM (
							SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
							FROM trhkasin_ppkd a
							INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
							WHERE jns_trans IN ('5','1')) a
							LEFT JOIN
							(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
							INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
							WHERE jns_trans IN ('5','1')
							GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
							ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
							WHERE (tgl_kas  BETWEEN ? AND ?)
							AND left(kd_skpd,17)=?
							GROUP BY no_kas,tgl_kas,kd_skpd,keterangan,kd_sub_kegiatan,kd_rek
							ORDER BY tgl_kas,no_kas,jenis", [$tgl1, $tgl2, $kd_skpd, $tgl1, $tgl2, $kd_skpd]);
        } else if ($pilihan == '3') {
            $register_cp = DB::select("SELECT '1' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,'' kd_sub_kegiatan, ''kd_rek,
						SUM ( CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '1' THEN nilai ELSE 0 END) AS hkpg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '2' THEN nilai ELSE 0 END) AS pot_lain,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus NOT IN ('1','2') THEN   nilai ELSE 0 END) AS cp,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) IN ('5101') THEN nilai ELSE 0 END) AS ls_peg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) = '5102' THEN    nilai ELSE 0 END) AS ls_brng,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai ELSE 0 END) AS ls_modal,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5105') THEN nilai ELSE 0 END) AS ls_phl,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) = '1101' THEN    nilai ELSE 0 END) AS up_gu,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5101') THEN nilai    ELSE 0 END) AS tu_peg,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5102') THEN nilai    ELSE 0 END) AS tu_brng,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai    ELSE 0 END) AS tu_modal,
                        SUM (nilai) AS total
							 FROM(

							SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, nilai, jns_trans,jns_cp,pot_khusus,kd_rek
							FROM (
							SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,kd_rek6 as kd_rek, SUM(rupiah) as nilai
							FROM trhkasin_ppkd a
							INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
							WHERE jns_trans IN ('5','1')
							GROUP BY a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd,keterangan,kd_rek6) a
							LEFT JOIN
							(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
							INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
							WHERE jns_trans IN ('5','1')
							GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
							ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
							WHERE (tgl_kas  BETWEEN ? AND ?)
							AND left(kd_skpd,22)=?
							GROUP BY no_kas,tgl_kas,kd_skpd,keterangan

							UNION ALL

							SELECT '2' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,kd_sub_kegiatan,kd_rek,
						SUM (    CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '1' THEN nilai ELSE 0 END) AS hkpg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '2' THEN nilai ELSE 0 END) AS pot_lain,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus NOT IN ('1','2') THEN   nilai ELSE 0 END) AS cp,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) IN ('5101') THEN nilai ELSE 0 END) AS ls_peg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) = '5102' THEN    nilai ELSE 0 END) AS ls_brng,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai ELSE 0 END) AS ls_modal,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5105') THEN nilai ELSE 0 END) AS ls_phl,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) = '1101' THEN    nilai ELSE 0 END) AS gu,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5101') THEN nilai    ELSE 0 END) AS up_gu_peg,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5102') THEN nilai    ELSE 0 END) AS up_gu_brng,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai    ELSE 0 END) AS up_gu_modal,
                        SUM (nilai) AS total
							 FROM(
							SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
							FROM (
							SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
							FROM trhkasin_ppkd a
							INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
							WHERE jns_trans IN ('5','1')) a
							LEFT JOIN
							(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
							INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
							WHERE jns_trans IN ('5','1')
							GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
							ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
							WHERE (tgl_kas  BETWEEN ? AND ?)
							AND left(kd_skpd,22)=?
							GROUP BY no_kas,tgl_kas,kd_skpd,keterangan,kd_sub_kegiatan,kd_rek
							ORDER BY tgl_kas,no_kas,jenis", [$tgl1, $tgl2, $kd_unit, $tgl1, $tgl2, $kd_unit]);
        } else if ($pilihan == '4') {
            $register_cp = DB::select("SELECT '1' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,'' kd_sub_kegiatan, ''kd_rek,
						SUM ( CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '1' THEN nilai ELSE 0 END) AS hkpg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '2' THEN nilai ELSE 0 END) AS pot_lain,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus NOT IN ('1','2') THEN   nilai ELSE 0 END) AS cp,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) IN ('5101') THEN nilai ELSE 0 END) AS ls_peg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) = '5102' THEN    nilai ELSE 0 END) AS ls_brng,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai ELSE 0 END) AS ls_modal,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5105') THEN nilai ELSE 0 END) AS ls_phl,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) = '1101' THEN    nilai ELSE 0 END) AS up_gu,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5101') THEN nilai    ELSE 0 END) AS tu_peg,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5102') THEN nilai    ELSE 0 END) AS tu_brng,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai    ELSE 0 END) AS tu_modal,
                        SUM (nilai) AS total
							 FROM(

							SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, nilai, jns_trans,jns_cp,pot_khusus,kd_rek
							FROM (
							SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,kd_rek6 as kd_rek, SUM(rupiah) as nilai
							FROM trhkasin_ppkd a
							INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
							WHERE jns_trans IN ('5','1')
							GROUP BY a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd,keterangan,kd_rek6) a
							LEFT JOIN
							(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
							INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
							WHERE jns_trans IN ('5','1')
							GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
							ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
							WHERE (tgl_kas  BETWEEN ? AND ?)
							GROUP BY no_kas,tgl_kas,kd_skpd,keterangan

							UNION ALL

							SELECT '2' as jenis,no_kas,tgl_kas,kd_skpd,keterangan as keterangan,kd_sub_kegiatan,kd_rek,
						SUM (    CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '1' THEN nilai ELSE 0 END) AS hkpg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus = '2' THEN nilai ELSE 0 END) AS pot_lain,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '1' AND pot_khusus NOT IN ('1','2') THEN   nilai ELSE 0 END) AS cp,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) IN ('5101') THEN nilai ELSE 0 END) AS ls_peg,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) = '5102' THEN    nilai ELSE 0 END) AS ls_brng,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai ELSE 0 END) AS ls_modal,
                        SUM (   CASE    WHEN jns_trans IN ('1','5') AND jns_cp = '2' AND LEFT(kd_rek,4) in ('5105') THEN nilai ELSE 0 END) AS ls_phl,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) = '1101' THEN    nilai ELSE 0 END) AS gu,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5101') THEN nilai    ELSE 0 END) AS up_gu_peg,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) IN ('5102') THEN nilai    ELSE 0 END) AS up_gu_brng,
                        SUM (   CASE    WHEN jns_trans = '1'    AND jns_cp = '3' AND LEFT(kd_rek,4) in ('5201','5202','5203','5204','5205','5206') THEN  nilai    ELSE 0 END) AS up_gu_modal,
                        SUM (nilai) AS total
							 FROM(
							SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
							FROM (
							SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
							FROM trhkasin_ppkd a
							INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
							WHERE jns_trans IN ('5','1')) a
							LEFT JOIN
							(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
							INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
							WHERE jns_trans IN ('5','1')
							GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
							ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
							WHERE (tgl_kas  BETWEEN ? AND ?)
							GROUP BY no_kas,tgl_kas,kd_skpd,keterangan,kd_sub_kegiatan,kd_rek
							ORDER BY tgl_kas,no_kas,jenis", [$tgl1, $tgl2, $tgl1, $tgl2]);
        }

        if ($pilihan == '1') {
            $register_lalu = 0;
        } elseif ($pilihan == '2') {
            $register_lalu = collect(DB::select("SELECT SUM(nilai) as nilai_lalu FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas <?) AND LEFT(kd_skpd,17)=?", [$tgl1, $kd_skpd]))->first();
        } elseif ($pilihan == '3') {
            $register_lalu = collect(DB::select("SELECT SUM(nilai) as nilai_lalu FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas <?) AND LEFT(kd_skpd,22)=?", [$tgl1, $kd_unit]))->first();
        } elseif ($pilihan == '4') {
            $register_lalu = collect(DB::select("SELECT SUM(nilai) as nilai_lalu FROM(
						SELECT a.no_kas,a.tgl_kas, a.kd_skpd,keterangan, kd_sub_kegiatan,kd_rek, nilai, jns_trans,jns_cp,pot_khusus
						FROM (
						SELECT a.no_kas,a.no_sts,a.tgl_kas,a.kd_skpd, keterangan,b.kd_sub_kegiatan,kd_rek6 as kd_rek, rupiah as nilai
						FROM trhkasin_ppkd a
						INNER JOIN trdkasin_ppkd b ON a.kd_skpd=b.kd_skpd AND a.no_kas=b.no_kas
						WHERE jns_trans IN ('5','1')) a
						LEFT JOIN
						(SELECT a.no_sts , a.kd_skpd , a.jns_trans, a.jns_cp, pot_khusus FROM trhkasin_pkd a
						INNER JOIN trdkasin_pkd b ON a.kd_skpd=b.kd_skpd AND a.no_sts=b.no_sts
						WHERE jns_trans IN ('5','1')
						GROUP BY a.no_sts, a.kd_skpd, a.jns_trans, a.jns_cp, pot_khusus ) b
						ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd)a
						WHERE (tgl_kas <?)", [$tgl1]))->first();
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'tanda_tangan' => $tanda_tangan,
            'tanggal1' => $tgl1,
            'tanggal2' => $tgl2,
            'pilihan' => $pilihan,
            'data_register' => $register_cp,
            'tanda_tangan' => $tanda_tangan,
            'spasi' => $spasi,
            'total_lalu' => $register_lalu,
            'skpd' => $kd_skpd,
            'unit' => $kd_unit,
        ];

        $judul = 'REGISTER CP RINCI';

        $view = view('bud.laporan_bendahara.cetak.register_cp_rinci')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename= $judul.xls");
            echo $view;
        }
    }

    public function potonganPajakk(Request $request)
    {
        $skpd_global = Auth::user()->kd_skpd;
        $pilihan = $request->pilihan;
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $ttd = $request->ttd;
        $sp2d = $request->sp2d;
        $belanja = $request->belanja;
        $kd_skpd = $request->kd_skpd;
        $kd_unit = $request->kd_unit;
        $jenis_print = $request->jenis_print;

        if ($ttd) {
            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD', 'PA'])->first();
        } else {
            $tanda_tangan = null;
        }

        if ($sp2d == '0') {
            if ($pilihan == '1') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, ISNULL(a.nilai,0) as nilai,
					ISNULL(iwp,0) iwp,
					ISNULL(taperum,0) taperum,
					ISNULL(hkpg,0) hkpg,
					ISNULL(pph,0) pph,
					iwp+taperum+hkpg+pph as jumlah_potongan
					FROM
					(SELECT a.kd_skpd, a.nm_skpd, SUM(d.nilai) as nilai FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
					INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
					WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd
					)a LEFT JOIN
					(SELECT a.kd_skpd, a.nm_skpd
					,SUM(CASE WHEN kd_rek6 in ('210108010001') THEN c.nilai ELSE 0 END) AS iwp
					,SUM(CASE WHEN kd_rek6 ='2110501' THEN c.nilai ELSE 0 END) AS taperum
					,SUM(CASE WHEN kd_rek6 ='2110801' THEN c.nilai ELSE 0 END) AS hkpg
					,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph
					FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
					WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd
					) b ON a.kd_skpd=b.kd_skpd", [$tgl1, $tgl2, $tgl1, $tgl2]);
            } else if ($pilihan == '2') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, ISNULL(a.nilai,0) as nilai,
					ISNULL(iwp,0) iwp,
					ISNULL(taperum,0) taperum,
					ISNULL(hkpg,0) hkpg,
					ISNULL(pph,0) pph,
					iwp+taperum+hkpg+pph as jumlah_potongan
					FROM
					(SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
					INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
					WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND LEFT(a.kd_skpd,17)=? AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
					)a LEFT JOIN
					(SELECT a.kd_skpd, a.nm_skpd
					,SUM(CASE WHEN kd_rek6 in ('210108010001') THEN c.nilai ELSE 0 END) AS iwp
					,SUM(CASE WHEN kd_rek6 ='2110501' THEN c.nilai ELSE 0 END) AS taperum
					,SUM(CASE WHEN kd_rek6 ='2110801' THEN c.nilai ELSE 0 END) AS hkpg
					,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph
					FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
					WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND LEFT(a.kd_skpd,17)=? AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd
					) b ON a.kd_skpd=b.kd_skpd", [$tgl1, $tgl2, $kd_skpd, $tgl1, $tgl2, $kd_skpd]);
            } else if ($pilihan == '3') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, ISNULL(a.nilai,0) as nilai,
					ISNULL(iwp,0) iwp,
					ISNULL(taperum,0) taperum,
					ISNULL(hkpg,0) hkpg,
					ISNULL(pph,0) pph,
					iwp+taperum+hkpg+pph as jumlah_potongan
					FROM
					(SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
					INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
					WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.kd_skpd=? AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
					)a LEFT JOIN
					(SELECT a.kd_skpd, a.nm_skpd
					,SUM(CASE WHEN kd_rek6 in ('210108010001') THEN c.nilai ELSE 0 END) AS iwp
					,SUM(CASE WHEN kd_rek6 ='2110501' THEN c.nilai ELSE 0 END) AS taperum
					,SUM(CASE WHEN kd_rek6 ='2110801' THEN c.nilai ELSE 0 END) AS hkpg
					,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph
					FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
					WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.kd_skpd=? AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd
					) b ON a.kd_skpd=b.kd_skpd", [$tgl1, $tgl2, $kd_unit, $tgl1, $tgl2, $kd_unit]);
            } else if ($pilihan == '4') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, ISNULL(a.nilai,0) as nilai,
					ISNULL(iwp,0) iwp,
					ISNULL(taperum,0) taperum,
					ISNULL(hkpg,0) hkpg,
					ISNULL(pph,0) pph,
					iwp+taperum+hkpg+pph as jumlah_potongan
					FROM
					(SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
					INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
					WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
					)a LEFT JOIN
					(SELECT a.kd_skpd, a.nm_skpd, a.no_sp2d
					,SUM(CASE WHEN kd_rek6 in ('210108010001') THEN c.nilai ELSE 0 END) AS iwp
					,SUM(CASE WHEN kd_rek6 ='2110501' THEN c.nilai ELSE 0 END) AS taperum
					,SUM(CASE WHEN kd_rek6 ='2110801' THEN c.nilai ELSE 0 END) AS hkpg
					,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph
					FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
					WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd, a.no_sp2d
					) b ON a.kd_skpd=b.kd_skpd
					ORDER BY cast(no_kas_bud as int)", [$tgl1, $tgl2, $tgl1, $tgl2]);
            }
        } else {
            if ($pilihan == '1') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, ISNULL(a.nilai,0) as nilai,
					ISNULL(ppn,0) ppn,
					ISNULL(pph21,0) pph21,
					ISNULL(pph22,0) pph22,
					ISNULL(pph23,0) pph23,
					ISNULL(psl4_a2,0) psl4_a2,
					ISNULL(iwppnpn,0) iwppnpn,
					ISNULL(pot_lain,0) pot_lain,
					ppn+pph21+pph22+pph23+psl4_a2+iwppnpn+pot_lain as jumlah_potongan
					FROM
					(SELECT a.kd_skpd, a.nm_skpd, SUM(d.nilai) as nilai FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
					INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
					WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd
					)a LEFT JOIN
					(SELECT a.kd_skpd, a.nm_skpd
					,SUM(CASE WHEN kd_rek6 ='2130301' THEN c.nilai ELSE 0 END) AS ppn
					,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph21
					,SUM(CASE WHEN kd_rek6 ='210105020001' THEN c.nilai ELSE 0 END) AS pph22
					,SUM(CASE WHEN kd_rek6 ='210105030001' THEN c.nilai ELSE 0 END) AS pph23
					,SUM(CASE WHEN kd_rek6 ='2130501' THEN c.nilai ELSE 0 END) AS psl4_a2
					,SUM(CASE WHEN map_pot ='210102010001d' THEN c.nilai ELSE 0 END) AS iwppnpn
					,SUM(CASE WHEN kd_rek6 not in ('2130301','210105010001','210105020001','210105030001','2130501','210102010001d') THEN c.nilai ELSE 0 END) AS pot_lain
					FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
					WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd
					) b ON a.kd_skpd=b.kd_skpd", [$tgl1, $tgl2, $tgl1, $tgl2]);
            } else if ($pilihan == '2') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d,
				    ISNULL(a.nilai,0) as nilai,
					ISNULL(ppn,0) ppn,
					ISNULL(pph21,0) pph21,
					ISNULL(pph22,0) pph22,
					ISNULL(pph23,0) pph23,
					ISNULL(psl4_a2,0) psl4_a2,
					ISNULL(iwppnpn,0) iwppnpn,
					ISNULL(pot_lain,0) pot_lain,
					ppn+pph21+pph22+pph23+psl4_a2+iwppnpn+pot_lain as jumlah_potongan
					FROM
					(SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
					INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
					WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1' AND LEFT(a.kd_skpd,17)=?
					GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
					)a LEFT JOIN
					(SELECT a.kd_skpd, a.nm_skpd, a.no_sp2d
					,SUM(CASE WHEN kd_rek6 ='2130301' THEN c.nilai ELSE 0 END) AS ppn
					,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph21
					,SUM(CASE WHEN kd_rek6 ='210105020001' THEN c.nilai ELSE 0 END) AS pph22
					,SUM(CASE WHEN kd_rek6 ='210105030001' THEN c.nilai ELSE 0 END) AS pph23
					,SUM(CASE WHEN kd_rek6 ='2130501' THEN c.nilai ELSE 0 END) AS psl4_a2
					,SUM(CASE WHEN map_pot ='210102010001d' THEN c.nilai ELSE 0 END) AS iwppnpn
					,SUM(CASE WHEN kd_rek6 not in ('2130301','210105010001','210105020001','210105030001','2130501','210102010001d') THEN c.nilai ELSE 0 END) AS pot_lain
					FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
					WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1' AND LEFT(a.kd_skpd,17)=?
					GROUP BY a.kd_skpd, a.nm_skpd,a.no_sp2d
					) b ON a.kd_skpd=b.kd_skpd AND a.no_sp2d=b.no_sp2d
					ORDER BY cast(no_kas_bud as int)", [$tgl1, $tgl2, $kd_skpd, $tgl1, $tgl2, $kd_skpd]);
            } else if ($pilihan == '3') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d,
				    ISNULL(a.nilai,0) as nilai,
					ISNULL(ppn,0) ppn,
					ISNULL(pph21,0) pph21,
					ISNULL(pph22,0) pph22,
					ISNULL(pph23,0) pph23,
					ISNULL(psl4_a2,0) psl4_a2,
					ISNULL(iwppnpn,0) iwppnpn,
					ISNULL(pot_lain,0) pot_lain,
					ppn+pph21+pph22+pph23+psl4_a2+iwppnpn+pot_lain as jumlah_potongan
					FROM
					(SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
					INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
					WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1' AND a.kd_skpd=?
					GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
					)a LEFT JOIN
					(SELECT a.kd_skpd, a.nm_skpd, a.no_sp2d
					,SUM(CASE WHEN kd_rek6 ='2130301' THEN c.nilai ELSE 0 END) AS ppn
					,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph21
					,SUM(CASE WHEN kd_rek6 ='210105020001' THEN c.nilai ELSE 0 END) AS pph22
					,SUM(CASE WHEN kd_rek6 ='210105030001' THEN c.nilai ELSE 0 END) AS pph23
					,SUM(CASE WHEN kd_rek6 ='2130501' THEN c.nilai ELSE 0 END) AS psl4_a2
					,SUM(CASE WHEN map_pot ='210102010001d' THEN c.nilai ELSE 0 END) AS iwppnpn
					,SUM(CASE WHEN kd_rek6 not in ('2130301','210105010001','210105020001','210105030001','2130501','210102010001d') THEN c.nilai ELSE 0 END) AS pot_lain
					FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
					WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1' AND a.kd_skpd=?
					GROUP BY a.kd_skpd, a.nm_skpd,a.no_sp2d
					) b ON a.kd_skpd=b.kd_skpd AND a.no_sp2d=b.no_sp2d
					ORDER BY cast(no_kas_bud as int)", [$tgl1, $tgl2, $kd_unit, $tgl1, $tgl2, $kd_unit]);
            } else if ($pilihan == '4') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d,
				    ISNULL(a.nilai,0) as nilai,
					ISNULL(ppn,0) ppn,
					ISNULL(pph21,0) pph21,
					ISNULL(pph22,0) pph22,
					ISNULL(pph23,0) pph23,
					ISNULL(psl4_a2,0) psl4_a2,
					ISNULL(iwppnpn,0) iwppnpn,
					ISNULL(pot_lain,0) pot_lain,
					ppn+pph21+pph22+pph23+psl4_a2+iwppnpn+pot_lain as jumlah_potongan
					FROM
					(SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
					INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
					WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
					)a LEFT JOIN
					(SELECT a.kd_skpd, a.nm_skpd, a.no_sp2d
					,SUM(CASE WHEN kd_rek6 ='2130301' THEN c.nilai ELSE 0 END) AS ppn
					,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph21
					,SUM(CASE WHEN kd_rek6 ='210105020001' THEN c.nilai ELSE 0 END) AS pph22
					,SUM(CASE WHEN kd_rek6 ='210105030001' THEN c.nilai ELSE 0 END) AS pph23
					,SUM(CASE WHEN kd_rek6 ='2130501' THEN c.nilai ELSE 0 END) AS psl4_a2
					,SUM(CASE WHEN map_pot ='210102010001d' THEN c.nilai ELSE 0 END) AS iwppnpn
					,SUM(CASE WHEN kd_rek6 not in ('2130301','210105010001','210105020001','210105030001','2130501','210102010001d') THEN c.nilai ELSE 0 END) AS pot_lain
					FROM trhsp2d a
					INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
					INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
					WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
					GROUP BY a.kd_skpd, a.nm_skpd,a.no_sp2d
					) b ON a.kd_skpd=b.kd_skpd AND a.no_sp2d=b.no_sp2d
					ORDER BY cast(no_kas_bud as int)", [$tgl1, $tgl2, $tgl1, $tgl2]);
            }
        }


        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'tanda_tangan' => $tanda_tangan,
            'tanggal1' => $tgl1,
            'tanggal2' => $tgl2,
            'pilihan' => $pilihan,
            'data_potongan' => $potongan_pajak,
            'sp2d' => $sp2d,
            'belanja' => $belanja,
        ];

        $view = view('bud.laporan_bendahara.cetak.potongan_pajak2')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function potonganPajak(Request $request)
    {
        $skpd_global = Auth::user()->kd_skpd;
        $pilihan = $request->pilihan;
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $ttd = $request->ttd;
        $sp2d = $request->sp2d;
        $belanja = $request->belanja;
        $kd_skpd = $request->kd_skpd;
        $kd_unit = $request->kd_unit;
        $spasi = $request->spasi;
        $jenis_print = $request->jenis_print;

        // if ($ttd) {
            // $tanda_tangan = DB::table('sms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD', 'PA'])->first();
        // } else {
        //     $tanda_tangan = null;
        // }

        //dd ($spasi);

        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        if ($sp2d == '0') {
            if ($pilihan == '1') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, ISNULL(a.nilai,0) as nilai,
                ISNULL(iwp,0) iwp,
                ISNULL(taperum,0) taperum,
                ISNULL(hkpg,0) hkpg,
                ISNULL(pph,0) pph,
                iwp+taperum+hkpg+pph as jumlah_potongan
                FROM
                (SELECT a.kd_skpd, a.nm_skpd, SUM(d.nilai) as nilai FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd
                )a LEFT JOIN
                (SELECT a.kd_skpd, a.nm_skpd
                ,SUM(CASE WHEN kd_rek6 in ('210108010001','210108010001') THEN c.nilai ELSE 0 END) AS iwp
                ,SUM(CASE WHEN kd_rek6 ='210107010001' THEN c.nilai ELSE 0 END) AS taperum
                ,SUM(CASE WHEN kd_rek6 in ('2110201') THEN c.nilai ELSE 0 END) AS hkpg
                ,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph
                FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
                WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd
                ) b ON a.kd_skpd=b.kd_skpd", [$tgl1, $tgl2, $tgl1, $tgl2]);
            } else if ($pilihan == '2') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, ISNULL(a.nilai,0) as nilai,
                ISNULL(iwp,0) iwp,
                ISNULL(taperum,0) taperum,
                ISNULL(hkpg,0) hkpg,
                ISNULL(pph,0) pph,
                iwp+taperum+hkpg+pph as jumlah_potongan
                FROM
                (SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND LEFT(a.kd_skpd,7)=? AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
                )a LEFT JOIN
                (SELECT a.kd_skpd, a.nm_skpd
                ,SUM(CASE WHEN kd_rek6 in ('210108010001','210108010001') THEN c.nilai ELSE 0 END) AS iwp
                ,SUM(CASE WHEN kd_rek6 ='210107010001' THEN c.nilai ELSE 0 END) AS taperum
                ,SUM(CASE WHEN kd_rek6 in ('2110201') THEN c.nilai ELSE 0 END) AS hkpg
                ,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph
                FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
                WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND LEFT(a.kd_skpd,7)=? AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd
                ) b ON a.kd_skpd=b.kd_skpd", [$tgl1, $tgl2, $kd_skpd, $tgl1, $tgl2, $kd_skpd]);
            } else if ($pilihan == '3') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, ISNULL(a.nilai,0) as nilai,
                ISNULL(iwp,0) iwp,
                ISNULL(taperum,0) taperum,
                ISNULL(hkpg,0) hkpg,
                ISNULL(pph,0) pph,
                iwp+taperum+hkpg+pph as jumlah_potongan
                FROM
                (SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.kd_skpd = ? AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
                )a LEFT JOIN
                (SELECT a.kd_skpd, a.nm_skpd, a.no_sp2d
                ,SUM(CASE WHEN kd_rek6 in ('210108010001','210108010001') THEN c.nilai ELSE 0 END) AS iwp
                ,SUM(CASE WHEN kd_rek6 ='210107010001' THEN c.nilai ELSE 0 END) AS taperum
                ,SUM(CASE WHEN kd_rek6 in ('2110201') THEN c.nilai ELSE 0 END) AS hkpg
                ,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph
                FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
                WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) and a.kd_skpd = ? AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd, a.no_sp2d
                ) b ON a.kd_skpd=b.kd_skpd AND a.no_sp2d=b.no_sp2d
                ORDER BY cast(no_kas_bud as int)", [$tgl1, $tgl2, $kd_unit, $tgl1, $tgl2, $kd_unit]);
            } else if ($pilihan == '4') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, ISNULL(a.nilai,0) as nilai,
                ISNULL(iwp,0) iwp,
                ISNULL(taperum,0) taperum,
                ISNULL(hkpg,0) hkpg,
                ISNULL(pph,0) pph,
                iwp+taperum+hkpg+pph as jumlah_potongan
                FROM
                (SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
                )a LEFT JOIN
                (SELECT a.kd_skpd, a.nm_skpd, a.no_sp2d
                ,SUM(CASE WHEN kd_rek6 in ('210108010001','210108010001') THEN c.nilai ELSE 0 END) AS iwp
                ,SUM(CASE WHEN kd_rek6 ='210107010001' THEN c.nilai ELSE 0 END) AS taperum
                ,SUM(CASE WHEN kd_rek6 in ('2110201') THEN c.nilai ELSE 0 END) AS hkpg
                ,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph
                FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
                WHERE (a.jns_spp='4' AND a.jenis_beban='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd, a.no_sp2d
                ) b ON a.kd_skpd=b.kd_skpd AND a.no_sp2d=b.no_sp2d
                ORDER BY kd_skpd", [$tgl1, $tgl2, $tgl1, $tgl2]);
            }
        } else {
            if ($pilihan == '1') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd,a.nmrekan,  a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d,
                ISNULL(a.nilai,0) as nilai,
                ISNULL(ppn,0) ppn,
                ISNULL(pph21,0) pph21,
                ISNULL(pph22,0) pph22,
                ISNULL(pph23,0) pph23,
                ISNULL(psl4_a2,0) psl4_a2,
                ISNULL(iwppnpn,0) iwppnpn,
                ISNULL(pot_lain,0) pot_lain,
                ppn+pph21+pph22+pph23+psl4_a2 as jumlah_potongan
                FROM
                (SELECT a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d,SUM(d.nilai) as nilai FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
                )a LEFT JOIN
                (SELECT a.kd_skpd, a.nm_skpd, a.nmrekan,a.no_sp2d
                ,SUM(CASE WHEN kd_rek6 ='210106010001' THEN c.nilai ELSE 0 END) AS ppn
                ,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph21
                ,SUM(CASE WHEN kd_rek6 ='210105020001' THEN c.nilai ELSE 0 END) AS pph22
                ,SUM(CASE WHEN kd_rek6 ='210105030001' THEN c.nilai ELSE 0 END) AS pph23
                ,SUM(CASE WHEN kd_rek6 ='210601050005' THEN c.nilai ELSE 0 END) AS psl4_a2
                ,SUM(CASE WHEN kd_rek6 ='2110901' THEN c.nilai ELSE 0 END) AS iwppnpn
                ,SUM(CASE WHEN kd_rek6 not in ('210106010001','210105010001','210105020001','210105030001','210601050005','2110901') THEN c.nilai ELSE 0 END) AS pot_lain
                FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
                WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd,a.nmrekan,a.no_sp2d
                ) b ON a.kd_skpd=b.kd_skpd AND a.no_sp2d=b.no_sp2d
                ORDER BY cast(no_kas_bud as int)", [$tgl1, $tgl2, $tgl1, $tgl2]);
            } else if ($pilihan == '2') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d,
                ISNULL(a.nilai,0) as nilai,
                ISNULL(ppn,0) ppn,
                ISNULL(pph21,0) pph21,
                ISNULL(pph22,0) pph22,
                ISNULL(pph23,0) pph23,
                ISNULL(psl4_a2,0) psl4_a2,
                ISNULL(iwppnpn,0) iwppnpn,
                ISNULL(pot_lain,0) pot_lain,
                ppn+pph21+pph22+pph23+psl4_a2+iwppnpn+pot_lain as jumlah_potongan
                FROM
                (SELECT a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1' AND LEFT(a.kd_skpd,7)=?
                GROUP BY a.kd_skpd, a.nm_skpd, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
                )a LEFT JOIN
                (SELECT a.kd_skpd, a.nm_skpd, a.no_sp2d
                ,SUM(CASE WHEN kd_rek6 ='210106010001' THEN c.nilai ELSE 0 END) AS ppn
                ,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph21
                ,SUM(CASE WHEN kd_rek6 ='210105020001' THEN c.nilai ELSE 0 END) AS pph22
                ,SUM(CASE WHEN kd_rek6 ='210105030001' THEN c.nilai ELSE 0 END) AS pph23
                ,SUM(CASE WHEN kd_rek6 ='210601050005' THEN c.nilai ELSE 0 END) AS psl4_a2
                ,SUM(CASE WHEN kd_rek6 ='2110901' THEN c.nilai ELSE 0 END) AS iwppnpn
                ,SUM(CASE WHEN kd_rek6 not in ('210106010001','210105010001','210105020001','210105030001','210601050005','2110901') THEN c.nilai ELSE 0 END) AS pot_lain
                FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
                WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1' AND LEFT(a.kd_skpd,7)=?
                GROUP BY a.kd_skpd, a.nm_skpd,a.no_sp2d
                ) b ON a.kd_skpd=b.kd_skpd AND a.no_sp2d=b.no_sp2d
                ORDER BY cast(no_kas_bud as int)", [$tgl1, $tgl2, $kd_skpd, $tgl1, $tgl2, $kd_skpd]);
            } else if ($pilihan == '3') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d,
                ISNULL(a.nilai,0) as nilai,
                ISNULL(ppn,0) ppn,
                ISNULL(pph21,0) pph21,
                ISNULL(pph22,0) pph22,
                ISNULL(pph23,0) pph23,
                ISNULL(psl4_a2,0) psl4_a2,
                ISNULL(iwppnpn,0) iwppnpn,
                ISNULL(pot_lain,0) pot_lain,
                ppn+pph21+pph22+pph23+psl4_a2  as jumlah_potongan
                FROM
                (SELECT a.kd_skpd, a.nm_skpd,  a.nmrekan, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1' AND a.kd_skpd=?
                GROUP BY a.kd_skpd, a.nm_skpd,  a.nmrekan, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
                )a LEFT JOIN
                (SELECT a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_sp2d
                ,SUM(CASE WHEN kd_rek6 ='210106010001' THEN c.nilai ELSE 0 END) AS ppn
                ,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph21
                ,SUM(CASE WHEN kd_rek6 ='210105020001' THEN c.nilai ELSE 0 END) AS pph22
                ,SUM(CASE WHEN kd_rek6 ='210105030001' THEN c.nilai ELSE 0 END) AS pph23
                ,SUM(CASE WHEN kd_rek6 ='210601050005' THEN c.nilai ELSE 0 END) AS psl4_a2
                ,SUM(CASE WHEN kd_rek6 ='2110901' THEN c.nilai ELSE 0 END) AS iwppnpn
                ,SUM(CASE WHEN kd_rek6 not in ('210106010001','210105010001','210105020001','210105030001','210601050005','2110901') THEN c.nilai ELSE 0 END) AS pot_lain
                FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
                WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1' AND a.kd_skpd=?
                GROUP BY a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_sp2d
                ) b ON a.kd_skpd=b.kd_skpd AND a.no_sp2d=b.no_sp2d
                ORDER BY cast(no_kas_bud as int)", [$tgl1, $tgl2, $kd_unit, $tgl1, $tgl2, $kd_unit]);
            } else if ($pilihan == '4') {
                $potongan_pajak = DB::select("SELECT a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d,
                ISNULL(a.nilai,0) as nilai,
                ISNULL(ppn,0) ppn,
                ISNULL(pph21,0) pph21,
                ISNULL(pph22,0) pph22,
                ISNULL(pph23,0) pph23,
                ISNULL(psl4_a2,0) psl4_a2,
                ISNULL(iwppnpn,0) iwppnpn,
                ISNULL(pot_lain,0) pot_lain,
                ppn+pph21+pph22+pph23+psl4_a2 as jumlah_potongan
                FROM
                (SELECT a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, SUM(d.nilai) as nilai FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trhspp c ON b.no_spp = c.no_spp AND b.kd_skpd = c.kd_skpd
                INNER JOIN trdspp d ON c.no_spp = d.no_spp AND c.kd_skpd = d.kd_skpd
                WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d
                )a LEFT JOIN
                (SELECT a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_sp2d

                ,SUM(CASE WHEN kd_rek6 ='210106010001' THEN c.nilai ELSE 0 END) AS ppn
                ,SUM(CASE WHEN kd_rek6 ='210105010001' THEN c.nilai ELSE 0 END) AS pph21
                ,SUM(CASE WHEN kd_rek6 ='210105020001' THEN c.nilai ELSE 0 END) AS pph22
                ,SUM(CASE WHEN kd_rek6 ='210105030001' THEN c.nilai ELSE 0 END) AS pph23
                ,SUM(CASE WHEN kd_rek6 ='210601050005' THEN c.nilai ELSE 0 END) AS psl4_a2
                ,SUM(CASE WHEN kd_rek6 ='2110901' THEN c.nilai ELSE 0 END) AS iwppnpn
                ,SUM(CASE WHEN kd_rek6 not in ('210106010001','210105010001','210105020001','210105030001','210601050005','2110901') THEN c.nilai ELSE 0 END) AS pot_lain
                FROM trhsp2d a
                INNER JOIN trhspm b ON a.no_spm = b.no_spm AND a.kd_skpd = b.kd_skpd
                INNER JOIN trspmpot c ON b.no_spm = c.no_spm AND b.kd_skpd = c.kd_skpd
                WHERE (a.jns_spp!='4' AND a.jenis_beban!='1') AND (a.tgl_kas_bud >= ? AND  a.tgl_kas_bud <= ?) AND a.status_bud='1'
                GROUP BY a.kd_skpd, a.nm_skpd, a.nmrekan, a.no_sp2d
                ) b ON a.kd_skpd=b.kd_skpd AND a.no_sp2d=b.no_sp2d
                ORDER BY cast(no_kas_bud as int)", [$tgl1, $tgl2, $tgl1, $tgl2]);
            }
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'tanda' => $tanda_tangan,
            'tanggal1' => $tgl1,
            'tanggal2' => $tgl2,
            'pilihan' => $pilihan,
            'data_potongan' => $potongan_pajak,
            'sp2d' => $sp2d,
            'spasi' => $spasi,
            'belanja' => $belanja,
        ];

         //dd ($data['tanda']);

        $view = view('bud.laporan_bendahara.cetak.potongan_pajak')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function daftarPengeluaran(Request $request)
    {
        $skpd_global = Auth::user()->kd_skpd;
        $pilihan = $request->pilihan;
        $tgl = $request->tgl;
        $ttd = $request->ttd;
        $kd_skpd = $request->kd_skpd;
        $kd_unit = $request->kd_unit;
        $beban = $request->beban;
        $bulan = $request->bulan;
        $spasi = $request->spasi;
        $jenis_print = $request->jenis_print;

        // if ($ttd) {
        //     $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD', 'PA'])->first();
        // } else {
        //     $tanda_tangan = null;
        // }
        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        $data_pengeluaran2 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("2 urut, '' no_kas_bud, a.no_kas_bud as urut2, '' tgl_kas_bud, '' no_sp2d,  '' tgl_sp2d,  a.kd_skpd, '' keperluan, '' nmrekan, '' pimpinan, '' nm_skpd, d.kd_sub_kegiatan,  d.kd_rek6, d.nm_rek6, d.nilai, d.no_bukti")
            ->whereRaw("a.status_bud=? and month(a.tgl_kas_bud)=?", ['1', $bulan])
            ->where(function ($query) use ($beban) {
                if ($beban == '0') {
                    $query->where('a.jns_spp', '4');
                } else if ($beban == '1') {
                    $query->whereRaw("(a.jns_spp=? or a.jns_spp=?)", ['5', '6']);
                } else if ($beban == '2') {
                    $query->where('a.jns_spp', '1');
                } else if ($beban == '3') {
                    $query->where('a.jns_spp', '3');
                } else if ($beban == '4') {
                    $query->where('a.jns_spp', '2');
                }
            })
            ->where(function ($query) use ($pilihan, $kd_skpd, $kd_unit) {
                if ($pilihan == '2') {
                    $query->whereRaw("LEFT(a.kd_skpd,17)=?", [$kd_skpd]);
                } else if ($pilihan == '3') {
                    $query->where('a.kd_skpd', $kd_unit);
                }
            })
            ->groupByRaw("a.no_kas_bud, a.kd_skpd, a.keperluan, d.kd_sub_kegiatan, d.kd_rek6, d.nm_rek6,d.nilai,d.no_bukti");

        $data_pengeluaran1 = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("1 urut, a.no_kas_bud as urut2, a.no_kas_bud, a.tgl_kas_bud, a.no_sp2d, a.tgl_sp2d, a.kd_skpd, a.keperluan, a.nmrekan, c.pimpinan, a.nm_skpd, '' kd_sub_kegiatan, '' kd_rek6, '' nm_rek6, 0 nilai, 0 no_bukti")
            ->whereRaw("a.status_bud=? and month(a.tgl_kas_bud)=?", ['1', $bulan])
            ->where(function ($query) use ($beban) {
                if ($beban == '0') {
                    $query->where('a.jns_spp', '4');
                } else if ($beban == '1') {
                    $query->whereRaw("(a.jns_spp=? or a.jns_spp=?)", ['5', '6']);
                } else if ($beban == '2') {
                    $query->where('a.jns_spp', '1');
                } else if ($beban == '3') {
                    $query->where('a.jns_spp', '3');
                } else if ($beban == '4') {
                    $query->where('a.jns_spp', '2');
                }
            })
            ->where(function ($query) use ($pilihan, $kd_skpd, $kd_unit) {
                if ($pilihan == '2') {
                    $query->whereRaw("LEFT(a.kd_skpd,17)=?", [$kd_skpd]);
                } else if ($pilihan == '3') {
                    $query->where('a.kd_skpd', $kd_unit);
                }
            })
            ->groupByRaw("a.tgl_kas_bud, a.no_kas_bud, a.no_sp2d, a.tgl_sp2d, a.keperluan, a.nmrekan, c.pimpinan, a.kd_skpd, a.nm_skpd")->unionAll($data_pengeluaran2);

        $total_pengeluaran = DB::table('trhsp2d as a')
            ->join('trhspm as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->join('trdspp as d', function ($join) {
                $join->on('c.no_spp', '=', 'd.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("sum(d.nilai) as nilai")
            ->whereRaw("a.status_bud=? and month(a.tgl_kas_bud)=?", ['1', $bulan])
            ->where(function ($query) use ($beban) {
                if ($beban == '0') {
                    $query->where('a.jns_spp', '4');
                } else if ($beban == '1') {
                    $query->whereRaw("(a.jns_spp=? or a.jns_spp=?)", ['5', '6']);
                } else if ($beban == '2') {
                    $query->where('a.jns_spp', '1');
                } else if ($beban == '3') {
                    $query->where('a.jns_spp', '3');
                } else if ($beban == '4') {
                    $query->where('a.jns_spp', '2');
                }
            })
            ->where(function ($query) use ($pilihan, $kd_skpd, $kd_unit) {
                if ($pilihan == '2') {
                    $query->whereRaw("LEFT(a.kd_skpd,17)=?", [$kd_skpd]);
                } else if ($pilihan == '3') {
                    $query->where('a.kd_skpd', $kd_unit);
                }
            })
            ->first();

        $pengeluaran = DB::table(DB::raw("({$data_pengeluaran1->toSql()}) AS sub"))
            ->mergeBindings($data_pengeluaran1)
            ->orderBy(DB::raw("CAST(no_kas_bud as int)"))
            ->orderBy('urut')
            ->orderBy('kd_sub_kegiatan')
            ->orderBy('kd_rek6')
            ->get();


        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'tanda_tangan' => $tanda_tangan,
            'pilihan' => $pilihan,
            'data_pengeluaran' => $pengeluaran,
            'bulan' => $bulan,
            'beban' => $beban,
            'spasi' => $spasi,
            'total_pengeluaran' => $total_pengeluaran->nilai
        ];

        $view = view('bud.laporan_bendahara.cetak.daftar_pengeluaran')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function daftarPenerimaan(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $pengirim = $request->pengirim;
        $jenis_print = $request->jenis_print;

        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        $penerimaan = DB::table('trhkasin_ppkd as a')
            ->leftJoin('ms_pengirim as b', function ($join) {
                $join->on('a.sumber', '=', 'b.kd_pengirim');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->where(['a.sumber' => $pengirim])
            ->where(function ($query) use ($pilihan, $tgl1, $tgl2, $periode1, $periode2) {
                // PILIHAN PER TANGGAL
                if ($pilihan == '1') {
                    $query->whereRaw("(a.tgl_sts >= ? and a.tgl_sts <= ?)", [$tgl1, $tgl2]);
                }
                //PILIHAN PER PERIODE
                elseif ($pilihan == '2') {
                    $query->whereRaw("(month(a.tgl_sts)>=? and month(a.tgl_sts)<=?)", [$periode1, $periode2]);
                }
            })
            ->orderBy('a.tgl_sts')
            ->get();

        $penerimaan_lalu = DB::table('trhkasin_pkd as a')
            ->selectRaw("sum(a.total) as nilai")
            ->where(['a.sumber' => $pengirim])
            ->where(function ($query) use ($pilihan, $tgl1, $periode1) {
                // PILIHAN PER TANGGAL
                if ($pilihan == '1') {
                    $query->whereRaw("a.tgl_sts < ?", [$tgl1]);
                }
                //PILIHAN PER PERIODE
                elseif ($pilihan == '2') {
                    $query->whereRaw("month(a.tgl_sts) < ?", [$periode1]);
                }
            })
            ->first();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal1' => $tgl1,
            'tanggal2' => $tgl2,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'kd_pengirim' => $pengirim,
            'pengirim' => DB::table('ms_pengirim')
                ->select('nm_pengirim')
                ->where(['kd_pengirim' => $pengirim])
                ->first(),
            'periode1' => $periode1,
            'periode2' => $periode2,
            'spasi' => $spasi,
            'data_penerimaan' => $penerimaan,
            'tanda_tangan' => $tanda_tangan,
            'penerimaan_lalu' => $penerimaan_lalu->nilai,
            'list_pengirim' => [
                '102', '153', '154', '167', '168', '169', '170', '172', '173', '22', '23', '25', '26', '43', '44', '45', '46', '47', '48', '49', '50', '54', '55', '56', '58', '89', '91', '92', '95', '113', '143', '144', '101', '174',
            ],
        ];

        $view = view('bud.laporan_bendahara.cetak.daftar_penerimaan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function penerimaanNonPendapatan(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        $penerimaan = DB::table('penerimaan_non_sp2d as a')
            ->whereIn('jenis', ['1', '2'])
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                // PILIHAN PER TANGGAL
                if ($pilihan == '1') {
                    $query->whereRaw("tanggal=?", [$tgl]);
                }
                //PILIHAN PER PERIODE
                elseif ($pilihan == '2') {
                    $query->whereRaw("tanggal between ? and ?", [$periode1, $periode2]);
                }
            })
            ->get();

        $penerimaan_lalu = DB::table('penerimaan_non_sp2d as a')
            ->selectRaw("sum(nilai) as nilai")
            ->whereIn('jenis', ['1', '2'])
            ->where(function ($query) use ($pilihan, $tgl, $periode1) {
                // PILIHAN PER TANGGAL
                if ($pilihan == '1') {
                    $query->whereRaw("tanggal < ?", [$tgl]);
                }
                //PILIHAN PER PERIODE
                elseif ($pilihan == '2') {
                    $query->whereRaw("tanggal < ?", [$periode1]);
                }
            })
            ->first();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_penerimaan' => $penerimaan,
            'tanda_tangan' => $tanda_tangan,
            'spasi' => $spasi,
            'penerimaan_lalu' => $penerimaan_lalu->nilai,
        ];

        $view = view('bud.laporan_bendahara.cetak.penerimaan_non_pendapatan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function transferDana(Request $request)
    {
        $tgl = $request->tgl;
        $ttd = $request->ttd;
        $bln = $request->bulan;
        $jenis_print = $request->jenis_print;

        $total_transfer = collect(DB::select("SELECT SUM(rupiah) rupiah FROM (
            SELECT * FROM (
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '1' urut, '1' as spasi, 'PENYALURAN TRANSFER MELALUI KPPN' nama) c
						LEFT JOIN
						(SELECT '1' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '2' urut, '1' as spasi, 'TRANSFER DBH PAJAK' nama) c
						LEFT JOIN
						(SELECT '2' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '3' urut, '2' as spasi, 'DBH PPh Pasal 21' nama) c
						LEFT JOIN
						( SELECT '3' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010002') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '4' urut, '2' as spasi, 'DBH PPh Pasal 25-29 WPOPDN' nama) c
						LEFT JOIN
						( SELECT '4' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010003') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '5' urut, '2' as spasi, 'DBH PBB Bagi Rata' nama) c
						LEFT JOIN
						( SELECT '5' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '6' urut, '2' as spasi, 'DBH PBB Migas' nama) c
						LEFT JOIN
						( SELECT '6' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010001') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '7' urut, '2' as spasi, 'Biaya Pemungutan PBB Migas' nama) c
						LEFT JOIN
						( SELECT '7' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210104') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '8' urut, '2' as spasi, 'DBH PBB Panas Bumi' nama) c
						LEFT JOIN
						( SELECT '8' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '9' urut, '2' as spasi, 'Biaya Pemungutan PBB Panas Bumi' nama) c
						LEFT JOIN
						( SELECT '9' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210102') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '10' urut, '2' as spasi, 'PBB Bagian Prov/Kab/Kota' nama) c
						LEFT JOIN
						( SELECT '10' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '11' urut, '2' as spasi, 'Kurang Bayar DBH PBB Bagian Prov TA 2014 pada' nama) c
						LEFT JOIN
						( SELECT '11' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '12' urut, '1' as spasi, 'TRANSFER DBH CUKAI' nama) c
						LEFT JOIN
						( SELECT '12' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '13' urut, '2' as spasi, 'DBH Cukai Hasil Tembakau' nama) c
						LEFT JOIN
						( SELECT '13' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010004') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '14' urut, '1' as spasi, 'TRANSFER DBH SDA' nama) c
						LEFT JOIN
						( SELECT '14' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '15' urut, '1' as spasi, 'PERTAMBANGAN UMUM' nama) c
						LEFT JOIN
						( SELECT '15' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '16' urut, '2' as spasi, 'DBH Pertambangan Umum - Iuran Tetap' nama) c
						LEFT JOIN
						( SELECT '16' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210204') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '17' urut, '2' as spasi, 'DBH Pertambangan Umum - Royalti' nama) c
						LEFT JOIN
						( SELECT '17' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210205') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '18' urut, '1' as spasi, 'MINYAK' nama) c
						LEFT JOIN
						( SELECT '18' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '19' urut, '2' as spasi, 'DBH Minyak 15%' nama) c
						LEFT JOIN
						( SELECT '19' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '20' urut, '2' as spasi, 'DBH Minyak 0,5%' nama) c
						LEFT JOIN
						( SELECT '20' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '21' urut, '2' as spasi, 'DBH Minyak Dalam Rangka Otsus' nama) c
						LEFT JOIN
						( SELECT '21' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '22' urut, '1' as spasi, 'GAS' nama) c
						LEFT JOIN
						( SELECT '22' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '23' urut, '2' as spasi, 'DBH Gas 30%' nama) c
						LEFT JOIN
						( SELECT '23' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '24' urut, '2' as spasi, 'DBH Gas 0,5%' nama) c
						LEFT JOIN
						( SELECT '24' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '25' urut, '2' as spasi, 'DBH Gas Dalam Rangka Otsus' nama) c
						LEFT JOIN
						( SELECT '25' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '26' urut, '1' as spasi, 'PANAS BUMI' nama) c
						LEFT JOIN
						( SELECT '26' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '27' urut, '2' as spasi, 'DBH Panas Bumi' nama) c
						LEFT JOIN
						( SELECT '27' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '28' urut, '1' as spasi, 'KEHUTANAN' nama) c
						LEFT JOIN
						( SELECT '28' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '29' urut, '2' as spasi, 'DBH PSDH Reboisasi Kehutanan' nama) c
						LEFT JOIN
						( SELECT '29' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010010') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '30' urut, '2' as spasi, 'DBH PSDH' nama) c
						LEFT JOIN
						( SELECT '30' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010012') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '31' urut, '2' as spasi, 'DBH HUPH' nama) c
						LEFT JOIN
						( SELECT '31' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210201','420101030046') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '32' urut, '2' as spasi, 'DBH DR' nama) c
						LEFT JOIN
						( SELECT '32' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '33' urut, '1' as spasi, 'PERIKANAN' nama) c
						LEFT JOIN
						( SELECT '33' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '34' urut, '2' as spasi, 'DBH Perikanan' nama) c
						LEFT JOIN
						( SELECT '34' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '35' urut, '1' as spasi, 'TRANSFER DAU' nama) c
						LEFT JOIN
						( SELECT '35' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '36' urut, '2' as spasi, 'Transfer Dana Alokasi Umum' nama) c
						LEFT JOIN
						( SELECT '36' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4220101') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '37' urut, '1' as spasi, 'TRANSFER DAK' nama) c
						LEFT JOIN
						( SELECT '37' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '38' urut, '2' as spasi, 'Transfer Dana Alokasi Khusus' nama) c
						LEFT JOIN
						( SELECT '38' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '39' urut, '2' as spasi, 'DAK Fisik Bidang Infrastruktur Jalan dan Irigasi' nama) c
						LEFT JOIN
						( SELECT '39' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101030043') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '40' urut, '2' as spasi, 'DAK Fisik Bidang Pendidikan' nama) c
						LEFT JOIN
						( SELECT '40' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230103') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '41' urut, '2' as spasi, 'DAK Fisik Bidang Kelautan dan Perikanan' nama) c
						LEFT JOIN
						( SELECT '41' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101030032') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '42' urut, '2' as spasi, 'DAK Fisik Bidang Kesehatan' nama) c
						LEFT JOIN
						( SELECT '42' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230104','4230206') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '43' urut, '2' as spasi, 'DAK Fisik Bidang Pertanian' nama) c
						LEFT JOIN
						( SELECT '43' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230105') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '44' urut, '2' as spasi, 'DAK Fisik Bidang Lingkungan Hidup dan Kehutanan' nama) c
						LEFT JOIN
						( SELECT '44' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101030046') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '45' urut, '2' as spasi, 'DAK Fisik Bidang Energi Skala Kecil' nama) c
						LEFT JOIN
						( SELECT '45' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230111') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '46' urut, '2' as spasi, 'Transfer Dana Alokasi Khusus BOS Satuan Pendidikan' nama) c
						LEFT JOIN
						( SELECT '46' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230207') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '47' urut, '1' as spasi, 'TRANSFER DANA OTONOMI KHUSUS' nama) c
						LEFT JOIN
						( SELECT '47' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '48' urut, '2' as spasi, 'Transfer Dana Otonomi Khusus' nama) c
						LEFT JOIN
						( SELECT '48' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '49' urut, '1' as spasi, 'TRANSFER DANA PENYESUAIAN' nama) c
						LEFT JOIN
						( SELECT '49' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '50' urut, '2' as spasi, 'Pelayanan Administrasi Kependudukan' nama) c
						LEFT JOIN
						( SELECT '50' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101040017') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '51' urut, '2' as spasi, 'Dana Tambahan Penghasilan Guru PNSD' nama) c
						LEFT JOIN
						( SELECT '51' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101040005') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '52' urut, '2' as spasi, 'Dana Tunjangan Profesi Guru PNSD' nama) c
						LEFT JOIN
						( SELECT '52' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230202') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '53' urut, '2' as spasi, 'Dana Tunjangan Khusus Guru PNSD' nama) c
						LEFT JOIN
						( SELECT '53' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230209') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '54' urut, '2' as spasi, 'Dana Bantuan Operasional Sekolah' nama) c
						LEFT JOIN
						( SELECT '54' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230201') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '55' urut, '2' as spasi, 'Dana Insentif Daerah (DID)' nama) c
						LEFT JOIN
						( SELECT '55' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4340104') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '56' urut, '2' as spasi, 'Dana Proyek Pemerintah Daerah dan Desentralisasi' nama) c
						LEFT JOIN
						( SELECT '56' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '57' urut, '1' as spasi, 'TRANSFER DANA DESA' nama) c
						LEFT JOIN
						( SELECT '57' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '58' urut, '2' as spasi, 'Dana Desa' nama) c
						LEFT JOIN
						( SELECT '58' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x ) z
						) z", [$bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln]))->first();

        $transfer = DB::select("SELECT * FROM (
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '1' urut, '1' as spasi, 'PENYALURAN TRANSFER MELALUI KPPN' nama) c
						LEFT JOIN
						(SELECT '1' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '2' urut, '1' as spasi, 'TRANSFER DBH PAJAK' nama) c
						LEFT JOIN
						(SELECT '2' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '3' urut, '2' as spasi, 'DBH PPh Pasal 21' nama) c
						LEFT JOIN
						( SELECT '3' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010002') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '4' urut, '2' as spasi, 'DBH PPh Pasal 25-29 WPOPDN' nama) c
						LEFT JOIN
						( SELECT '4' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010003') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '5' urut, '2' as spasi, 'DBH PBB Bagi Rata' nama) c
						LEFT JOIN
						( SELECT '5' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '6' urut, '2' as spasi, 'DBH PBB Migas' nama) c
						LEFT JOIN
						( SELECT '6' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010001') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '7' urut, '2' as spasi, 'Biaya Pemungutan PBB Migas' nama) c
						LEFT JOIN
						( SELECT '7' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210104') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '8' urut, '2' as spasi, 'DBH PBB Panas Bumi' nama) c
						LEFT JOIN
						( SELECT '8' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '9' urut, '2' as spasi, 'Biaya Pemungutan PBB Panas Bumi' nama) c
						LEFT JOIN
						( SELECT '9' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210102') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '10' urut, '2' as spasi, 'PBB Bagian Prov/Kab/Kota' nama) c
						LEFT JOIN
						( SELECT '10' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '11' urut, '2' as spasi, 'Kurang Bayar DBH PBB Bagian Prov TA 2014 pada' nama) c
						LEFT JOIN
						( SELECT '11' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '12' urut, '1' as spasi, 'TRANSFER DBH CUKAI' nama) c
						LEFT JOIN
						( SELECT '12' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '13' urut, '2' as spasi, 'DBH Cukai Hasil Tembakau' nama) c
						LEFT JOIN
						( SELECT '13' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010004') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '14' urut, '1' as spasi, 'TRANSFER DBH SDA' nama) c
						LEFT JOIN
						( SELECT '14' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '15' urut, '1' as spasi, 'PERTAMBANGAN UMUM' nama) c
						LEFT JOIN
						( SELECT '15' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '16' urut, '2' as spasi, 'DBH Pertambangan Umum - Iuran Tetap' nama) c
						LEFT JOIN
						( SELECT '16' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210204') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '17' urut, '2' as spasi, 'DBH Pertambangan Umum - Royalti' nama) c
						LEFT JOIN
						( SELECT '17' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210205') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '18' urut, '1' as spasi, 'MINYAK' nama) c
						LEFT JOIN
						( SELECT '18' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '19' urut, '2' as spasi, 'DBH Minyak 15%' nama) c
						LEFT JOIN
						( SELECT '19' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '20' urut, '2' as spasi, 'DBH Minyak 0,5%' nama) c
						LEFT JOIN
						( SELECT '20' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '21' urut, '2' as spasi, 'DBH Minyak Dalam Rangka Otsus' nama) c
						LEFT JOIN
						( SELECT '21' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '22' urut, '1' as spasi, 'GAS' nama) c
						LEFT JOIN
						( SELECT '22' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '23' urut, '2' as spasi, 'DBH Gas 30%' nama) c
						LEFT JOIN
						( SELECT '23' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '24' urut, '2' as spasi, 'DBH Gas 0,5%' nama) c
						LEFT JOIN
						( SELECT '24' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '25' urut, '2' as spasi, 'DBH Gas Dalam Rangka Otsus' nama) c
						LEFT JOIN
						( SELECT '25' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '26' urut, '1' as spasi, 'PANAS BUMI' nama) c
						LEFT JOIN
						( SELECT '26' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '27' urut, '2' as spasi, 'DBH Panas Bumi' nama) c
						LEFT JOIN
						( SELECT '27' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '28' urut, '1' as spasi, 'KEHUTANAN' nama) c
						LEFT JOIN
						( SELECT '28' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '29' urut, '2' as spasi, 'DBH PSDH Reboisasi Kehutanan' nama) c
						LEFT JOIN
						( SELECT '29' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010010') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '30' urut, '2' as spasi, 'DBH PSDH' nama) c
						LEFT JOIN
						( SELECT '30' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101010012') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '31' urut, '2' as spasi, 'DBH HUPH' nama) c
						LEFT JOIN
						( SELECT '31' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4210201','420101030046') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '32' urut, '2' as spasi, 'DBH DR' nama) c
						LEFT JOIN
						( SELECT '32' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '33' urut, '1' as spasi, 'PERIKANAN' nama) c
						LEFT JOIN
						( SELECT '33' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '34' urut, '2' as spasi, 'DBH Perikanan' nama) c
						LEFT JOIN
						( SELECT '34' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '35' urut, '1' as spasi, 'TRANSFER DAU' nama) c
						LEFT JOIN
						( SELECT '35' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '36' urut, '2' as spasi, 'Transfer Dana Alokasi Umum' nama) c
						LEFT JOIN
						( SELECT '36' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4220101') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '37' urut, '1' as spasi, 'TRANSFER DAK' nama) c
						LEFT JOIN
						( SELECT '37' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '38' urut, '2' as spasi, 'Transfer Dana Alokasi Khusus' nama) c
						LEFT JOIN
						( SELECT '38' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '39' urut, '2' as spasi, 'DAK Fisik Bidang Infrastruktur Jalan dan Irigasi' nama) c
						LEFT JOIN
						( SELECT '39' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101030043') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '40' urut, '2' as spasi, 'DAK Fisik Bidang Pendidikan' nama) c
						LEFT JOIN
						( SELECT '40' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230103') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '41' urut, '2' as spasi, 'DAK Fisik Bidang Kelautan dan Perikanan' nama) c
						LEFT JOIN
						( SELECT '41' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101030032') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '42' urut, '2' as spasi, 'DAK Fisik Bidang Kesehatan' nama) c
						LEFT JOIN
						( SELECT '42' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230104','4230206') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '43' urut, '2' as spasi, 'DAK Fisik Bidang Pertanian' nama) c
						LEFT JOIN
						( SELECT '43' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230105') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '44' urut, '2' as spasi, 'DAK Fisik Bidang Lingkungan Hidup dan Kehutanan' nama) c
						LEFT JOIN
						( SELECT '44' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230109') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '45' urut, '2' as spasi, 'DAK Fisik Bidang Energi Skala Kecil' nama) c
						LEFT JOIN
						( SELECT '45' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230111') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '46' urut, '2' as spasi, 'Transfer Dana Alokasi Khusus BOS Satuan Pendidikan' nama) c
						LEFT JOIN
						( SELECT '46' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230207') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '47' urut, '1' as spasi, 'TRANSFER DANA OTONOMI KHUSUS' nama) c
						LEFT JOIN
						( SELECT '47' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '48' urut, '2' as spasi, 'Transfer Dana Otonomi Khusus' nama) c
						LEFT JOIN
						( SELECT '48' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '49' urut, '1' as spasi, 'TRANSFER DANA PENYESUAIAN' nama) c
						LEFT JOIN
						( SELECT '49' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '50' urut, '2' as spasi, 'Pelayanan Administrasi Kependudukan' nama) c
						LEFT JOIN
						( SELECT '50' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101040017') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '51' urut, '2' as spasi, 'Dana Tambahan Penghasilan Guru PNSD' nama) c
						LEFT JOIN
						( SELECT '51' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('420101040005') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '52' urut, '2' as spasi, 'Dana Tunjangan Profesi Guru PNSD' nama) c
						LEFT JOIN
						( SELECT '52' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230202') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '53' urut, '2' as spasi, 'Dana Tunjangan Khusus Guru PNSD' nama) c
						LEFT JOIN
						( SELECT '53' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230209') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '54' urut, '2' as spasi, 'Dana Bantuan Operasional Sekolah' nama) c
						LEFT JOIN
						( SELECT '54' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4230201') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '55' urut, '2' as spasi, 'Dana Insentif Daerah (DID)' nama) c
						LEFT JOIN
						( SELECT '55' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4340104') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '56' urut, '2' as spasi, 'Dana Proyek Pemerintah Daerah dan Desentralisasi' nama) c
						LEFT JOIN
						( SELECT '56' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '57' urut, '1' as spasi, 'TRANSFER DANA DESA' nama) c
						LEFT JOIN
						( SELECT '57' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x

						UNION
						SELECT urut, spasi, nama, ISNULL(rupiah,0) rupiah, ISNULL(tgl_kas,'') tgl_kas FROM (
						SELECT c.urut, c.spasi, c.nama, d.rupiah, d.tgl_kas FROM
						(select '58' urut, '2' as spasi, 'Dana Desa' nama) c
						LEFT JOIN
						( SELECT '58' urut, b.tgl_kas, a.rupiah from trdkasin_ppkd a inner join trhkasin_ppkd b on a.no_kas=b.no_kas and a.kd_skpd=b.kd_skpd
						where kd_rek6 in ('4') AND MONTH(tgl_kas)=? ) d on c.urut=d.urut )x ) z
						order by CAST(urut as int)", [$bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln, $bln]);

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'tanggal' => $tgl,
            'tanda_tangan' => DB::table('ms_ttd')
                ->where(['kode' => 'BUD', 'nip' => $ttd])
                ->first(),
            'total_transfer' => $total_transfer->rupiah,
            'data_transfer' => $transfer,
            'bulan' => $bln
        ];

        $view = view('bud.laporan_bendahara.cetak.transfer_dana')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function restitusi(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        $restitusi = DB::table('trhrestitusi as a')
            ->join('trdrestitusi as b', function ($join) {
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                $join->on('a.no_sts', '=', 'b.no_sts');
            })
            ->selectRaw("b.no_sts no_bukti, a.keterangan, b.kd_skpd, (SELECT nm_skpd FROM ms_skpd WHERE kd_skpd=b.kd_skpd) nm_skpd, a.tgl_sts tgl_bukti, b.kd_rek6, (SELECT nm_rek6 FROM ms_rek6 WHERE kd_rek6=b.kd_rek6) nm_rek6, b.rupiah")
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                // PILIHAN PER TANGGAL
                if ($pilihan == '1') {
                    $query->whereRaw("a.tgl_sts=?", [$tgl]);
                }
                //PILIHAN PER PERIODE
                elseif ($pilihan == '2') {
                    $query->whereRaw("a.tgl_sts between ? and ?", [$periode1, $periode2]);
                }
            })
            ->orderBy('tgl_bukti')
            ->orderBy('kd_rek6')
            ->get();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_restitusi' => $restitusi,
            'tanda_tangan' => DB::table('ms_ttd')
                ->where(['kode' => 'BUD', 'nip' => $ttd])
                ->first(),
        ];

        $view = view('bud.laporan_bendahara.cetak.restitusi')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function rth(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $bulan = $request->bulan;
        $ttd = $request->ttd;
        $spasi = $request->spasi;
        $jenis_print = $request->jenis_print;

        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD','PA')", [$ttd]))->first();

        if ($pilihan == '1') {
            $rth = DB::select("exec cetak_rth2 ?", array($bulan));
        } elseif ($pilihan == '2') {
            $rth = DB::select("exec cetak_rth_periode2 ?,?", array($periode1, $periode2));
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'bulan' => $bulan,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_rth' => $rth,
            'spasi' => $spasi,
            'tanda_tangan' => $tanda_tangan,
            'total_data' => count($rth),
            // 'tanda_tangan' => DB::table('ms_ttd')
            //     ->where(['kode' => 'BUD', 'nip' => $ttd])
            //     ->first(),
        ];

        $judul = 'RTH';

        $view = view('bud.laporan_bendahara.cetak.rth')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename= $judul.xls");
            echo $view;
        }
    }

    public function pengeluaranNonSp2d(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        $pengeluaran = DB::table('pengeluaran_non_sp2d as a')
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                // PILIHAN PER TANGGAL
                if ($pilihan == '1') {
                    $query->whereRaw("tanggal=?", [$tgl]);
                }
                //PILIHAN PER PERIODE
                elseif ($pilihan == '2') {
                    $query->whereRaw("tanggal between ? and ?", [$periode1, $periode2]);
                }
            })
            ->get();

        $pengeluaran_lalu = DB::table('pengeluaran_non_sp2d as a')
            ->selectRaw("sum(nilai) as nilai")
            ->where(function ($query) use ($pilihan, $tgl, $periode1) {
                // PILIHAN PER TANGGAL
                if ($pilihan == '1') {
                    $query->whereRaw("tanggal < ?", [$tgl]);
                }
                //PILIHAN PER PERIODE
                elseif ($pilihan == '2') {
                    $query->whereRaw("tanggal < ?", [$periode1]);
                }
            })
            ->first();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_pengeluaran' => $pengeluaran,
            'tanda_tangan' => DB::table('ms_ttd')
                ->where(['kode' => 'BUD', 'nip' => $ttd])
                ->first(),
            'pengeluaran_lalu' => $pengeluaran_lalu->nilai,
        ];

        $view = view('bud.laporan_bendahara.cetak.pengeluaran_non_sp2d')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function dth(Request $request)
    {
        $pilihan = $request->pilihan;
        $skpd = $request->skpd;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $bendahara = $request->bendahara;
        $pa_kpa = $request->pa_kpa;
        $spasi = $request->spasi;
        $bulan = $request->bulan;
        $jenis_print = $request->jenis_print;
        // $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        if ($pilihan == '1' && $jenis_print == 'keseluruhan') {
            $dth = DB::select("SELECT 1 urut, p.no_spm, p.nil_spm nilai, p.no_sp2d, p.nil_sp2d nilai_belanja, '' no_bukti, '' kode_belanja, '' kd_rek6, '' as jenis_pajak,0 as nilai_pot, (select npwp from trhspm WHERE no_spm=p.no_spm) npwp, p.nmrekan as nmrekan, '' ket,p.jns_spp, '' ntpn
            FROM (
                                    SELECT x.kd_skpd, x.no_sp2d, y.no_spm, x.pot, y.nil_spm, y.nil_sp2d, y.jns_spp,y.nmrekan FROM (
                                    SELECT b.kd_skpd, a.no_sp2d, SUM(b.nilai) pot
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.kd_skpd=b.kd_skpd AND a.no_bukti=b.no_bukti
                                    WHERE MONTH(a.tgl_bukti)=?
                                    GROUP BY b.kd_skpd, a.no_sp2d ) x
                                    LEFT JOIN
                                    (
                                    SELECT d.kd_skpd, d.no_spm, c.nilai nil_spm, d.no_sp2d, d.nilai nil_sp2d, d.jns_spp, c.nmrekan
                                    FROM trdspp a INNER JOIN trhspp b
                                    ON a.no_spp = b.no_spp AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspm c
                                    ON b.no_spp = c.no_spp AND a.kd_skpd = c.kd_skpd
                                    INNER JOIN trhsp2d d
                                    on c.no_spm = d.no_spm AND c.kd_skpd=d.kd_skpd
                                    WHERE (d.sp2d_batal=0 OR d.sp2d_batal is NULL) and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
                                    GROUP BY d.kd_skpd,d.no_spm, d.no_sp2d, c.nilai, d.nilai, d.jns_spp,c.nmrekan) y
                                    ON x.kd_skpd=y.kd_skpd AND x.no_sp2d=y.no_sp2d ) p
                                    UNION ALL

                                    SELECT 2 as urut, '' as no_spm,0 as nilai,p.no_sp2d,0 as nilai_belanja,
                                                        p.no_bukti, p.kode_belanja,p.kd_rek6,'' as jenis_pajak,p.pot as nilai_pot,p.npwp,
                                                        rekanan nmrekan,    case when p.jns_spp='6' or p.jns_spp='5' or  p.jns_spp='4' then p.keperluan else
                                    'No Set: ' + p.no_bukti end AS ket, p.jns_spp, p.ntpn
                                    FROM (
                                    SELECT x.*, y.keperluan FROM (
                                    SELECT b.kd_skpd, b.no_bukti, a.kd_sub_kegiatan+'.'+b.kd_rek_trans kode_belanja,
                                           RTRIM(b.kd_rek6) kd_rek6, a.no_sp2d, b.nilai pot, b.rekanan, b.npwp, b.ntpn, a.jns_spp
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.kd_skpd=b.kd_skpd AND a.no_bukti=b.no_bukti
                                    WHERE MONTH(a.tgl_bukti)=? ) x
                                    LEFT JOIN
                                    (
                                    SELECT d.kd_skpd, d.no_spm, c.nilai nil_spm, d.no_sp2d, d.nilai nil_sp2d, d.jns_spp, b.keperluan
                                    FROM trdspp a INNER JOIN trhspp b
                                    ON a.no_spp = b.no_spp AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspm c
                                    ON b.no_spp = c.no_spp AND a.kd_skpd = c.kd_skpd
                                    INNER JOIN trhsp2d d
                                    on c.no_spm = d.no_spm AND c.kd_skpd=d.kd_skpd
                                    WHERE (d.sp2d_batal=0 OR d.sp2d_batal is NULL) and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
                                    GROUP BY d.kd_skpd,d.no_spm, d.no_sp2d, c.nilai, d.nilai, d.jns_spp,b.keperluan) y
                                    ON x.kd_skpd=y.kd_skpd AND x.no_sp2d=y.no_sp2d ) p
                                    where p.kd_rek6 in ('2110301','2130101','2130201','2130301','2130401','2130501')
                                    ORDER BY no_sp2d,urut,no_spm,kode_belanja,kd_rek6", [$bulan, $bulan]);
        } elseif ($pilihan == '2' && $jenis_print == 'keseluruhan') {
            $dth = DB::select("SELECT 1 urut, p.no_spm, p.nil_spm nilai, p.no_sp2d, p.nil_sp2d nilai_belanja, '' no_bukti, '' kode_belanja, '' kd_rek6, '' as jenis_pajak,0 as nilai_pot, (select npwp from trhspm WHERE no_spm=p.no_spm) npwp, p.nmrekan as nmrekan, '' ket,p.jns_spp, '' ntpn
            FROM (
                                    SELECT x.kd_skpd, x.no_sp2d, y.no_spm, x.pot, y.nil_spm, y.nil_sp2d, y.jns_spp,y.nmrekan FROM (
                                    SELECT b.kd_skpd, a.no_sp2d, SUM(b.nilai) pot
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.kd_skpd=b.kd_skpd AND a.no_bukti=b.no_bukti
                                    WHERE (a.tgl_bukti>=? and a.tgl_bukti <=?)
                                    GROUP BY b.kd_skpd, a.no_sp2d ) x
                                    LEFT JOIN
                                    (
                                    SELECT d.kd_skpd, d.no_spm, c.nilai nil_spm, d.no_sp2d, d.nilai nil_sp2d, d.jns_spp, c.nmrekan
                                    FROM trdspp a INNER JOIN trhspp b
                                    ON a.no_spp = b.no_spp AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspm c
                                    ON b.no_spp = c.no_spp AND a.kd_skpd = c.kd_skpd
                                    INNER JOIN trhsp2d d
                                    on c.no_spm = d.no_spm AND c.kd_skpd=d.kd_skpd
                                    WHERE (d.sp2d_batal=0 OR d.sp2d_batal is NULL) and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
                                    GROUP BY d.kd_skpd,d.no_spm, d.no_sp2d, c.nilai, d.nilai, d.jns_spp,c.nmrekan) y
                                    ON x.kd_skpd=y.kd_skpd AND x.no_sp2d=y.no_sp2d ) p
                                    UNION ALL

                                    SELECT 2 as urut, '' as no_spm,0 as nilai,p.no_sp2d,0 as nilai_belanja,
                                                        p.no_bukti, p.kode_belanja,p.kd_rek6,'' as jenis_pajak,p.pot as nilai_pot,p.npwp,
                                                        rekanan nmrekan,    case when p.jns_spp='6' or p.jns_spp='5' or  p.jns_spp='4' then p.keperluan else
                                    'No Set: ' + p.no_bukti end AS ket, p.jns_spp, p.ntpn
                                    FROM (
                                    SELECT x.*, y.keperluan FROM (
                                    SELECT b.kd_skpd, b.no_bukti, a.kd_sub_kegiatan+'.'+b.kd_rek_trans kode_belanja,
                                           RTRIM(b.kd_rek6) kd_rek6, a.no_sp2d, b.nilai pot, b.rekanan, b.npwp, b.ntpn, a.jns_spp
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.kd_skpd=b.kd_skpd AND a.no_bukti=b.no_bukti
                                    WHERE (a.tgl_bukti>=? and a.tgl_bukti <=?) ) x
                                    LEFT JOIN
                                    (
                                    SELECT d.kd_skpd, d.no_spm, c.nilai nil_spm, d.no_sp2d, d.nilai nil_sp2d, d.jns_spp, b.keperluan
                                    FROM trdspp a INNER JOIN trhspp b
                                    ON a.no_spp = b.no_spp AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspm c
                                    ON b.no_spp = c.no_spp AND a.kd_skpd = c.kd_skpd
                                    INNER JOIN trhsp2d d
                                    on c.no_spm = d.no_spm AND c.kd_skpd=d.kd_skpd
                                    WHERE (d.sp2d_batal=0 OR d.sp2d_batal is NULL) and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
                                    GROUP BY d.kd_skpd,d.no_spm, d.no_sp2d, c.nilai, d.nilai, d.jns_spp,b.keperluan) y
                                    ON x.kd_skpd=y.kd_skpd AND x.no_sp2d=y.no_sp2d ) p
                                    where p.kd_rek6 in ('2110301','2130101','2130201','2130301','2130401','2130501')
                                    ORDER BY no_sp2d,urut,no_spm,kode_belanja,kd_rek6", [$periode1, $periode2, $periode1, $periode2]);
        } elseif ($pilihan == '1') {
            $dth = DB::select("SELECT 1 urut, p.no_spm, p.nil_spm nilai, p.no_sp2d, p.nil_sp2d nilai_belanja, '' no_bukti, '' kode_belanja, '' kd_rek6, '' as jenis_pajak,0 as nilai_pot, (select npwp from trhspm WHERE no_spm=p.no_spm) npwp, p.nmrekan as nmrekan, '' ket,p.jns_spp, '' ntpn
            FROM (
                                    SELECT x.kd_skpd, x.no_sp2d, y.no_spm, x.pot, y.nil_spm, y.nil_sp2d, y.jns_spp,y.nmrekan FROM (
                                    SELECT b.kd_skpd, a.no_sp2d, SUM(b.nilai) pot
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.kd_skpd=b.kd_skpd AND a.no_bukti=b.no_bukti
                                    WHERE MONTH(a.tgl_bukti)=? and b.kd_skpd=?
                                    GROUP BY b.kd_skpd, a.no_sp2d ) x
                                    LEFT JOIN
                                    (
                                    SELECT d.kd_skpd, d.no_spm, c.nilai nil_spm, d.no_sp2d, d.nilai nil_sp2d, d.jns_spp, c.nmrekan
                                    FROM trdspp a INNER JOIN trhspp b
                                    ON a.no_spp = b.no_spp AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspm c
                                    ON b.no_spp = c.no_spp AND a.kd_skpd = c.kd_skpd
                                    INNER JOIN trhsp2d d
                                    on c.no_spm = d.no_spm AND c.kd_skpd=d.kd_skpd
                                    WHERE (d.sp2d_batal=0 OR d.sp2d_batal is NULL) and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
                                    GROUP BY d.kd_skpd,d.no_spm, d.no_sp2d, c.nilai, d.nilai, d.jns_spp,c.nmrekan) y
                                    ON x.kd_skpd=y.kd_skpd AND x.no_sp2d=y.no_sp2d ) p
                                    UNION ALL

                                    SELECT 2 as urut, '' as no_spm,0 as nilai,p.no_sp2d,0 as nilai_belanja,
                                                        p.no_bukti, p.kode_belanja,p.kd_rek6,'' as jenis_pajak,p.pot as nilai_pot,p.npwp,
                                                        rekanan nmrekan,    case when p.jns_spp='6' or p.jns_spp='5' or  p.jns_spp='4' then p.keperluan else
                                    'No Set: ' + p.no_bukti end AS ket, p.jns_spp, p.ntpn
                                    FROM (
                                    SELECT x.*, y.keperluan FROM (
                                    SELECT b.kd_skpd, b.no_bukti, a.kd_sub_kegiatan+'.'+b.kd_rek_trans kode_belanja,
                                           RTRIM(b.kd_rek6) kd_rek6, a.no_sp2d, b.nilai pot, b.rekanan, b.npwp, b.ntpn, a.jns_spp
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.kd_skpd=b.kd_skpd AND a.no_bukti=b.no_bukti
                                    WHERE MONTH(a.tgl_bukti)=? and b.kd_skpd=? ) x
                                    LEFT JOIN
                                    (
                                    SELECT d.kd_skpd, d.no_spm, c.nilai nil_spm, d.no_sp2d, d.nilai nil_sp2d, d.jns_spp, b.keperluan
                                    FROM trdspp a INNER JOIN trhspp b
                                    ON a.no_spp = b.no_spp AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspm c
                                    ON b.no_spp = c.no_spp AND a.kd_skpd = c.kd_skpd
                                    INNER JOIN trhsp2d d
                                    on c.no_spm = d.no_spm AND c.kd_skpd=d.kd_skpd
                                    WHERE (d.sp2d_batal=0 OR d.sp2d_batal is NULL) and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
                                    GROUP BY d.kd_skpd,d.no_spm, d.no_sp2d, c.nilai, d.nilai, d.jns_spp,b.keperluan) y
                                    ON x.kd_skpd=y.kd_skpd AND x.no_sp2d=y.no_sp2d ) p
                                    where p.kd_rek6 in ('2110301','2130101','2130201','2130301','2130401','2130501')
                                    ORDER BY no_sp2d,urut,no_spm,kode_belanja,kd_rek6", [$bulan, $skpd, $bulan, $skpd]);
        } elseif ($pilihan == '2') {
            $dth = DB::select("SELECT 1 urut, p.no_spm, p.nil_spm nilai, p.no_sp2d, p.nil_sp2d nilai_belanja, '' no_bukti, '' kode_belanja, '' kd_rek6, '' as jenis_pajak,0 as nilai_pot, (select npwp from trhspm WHERE no_spm=p.no_spm) npwp, p.nmrekan as nmrekan, '' ket,p.jns_spp, '' ntpn
            FROM (
                                    SELECT x.kd_skpd, x.no_sp2d, y.no_spm, x.pot, y.nil_spm, y.nil_sp2d, y.jns_spp,y.nmrekan FROM (
                                    SELECT b.kd_skpd, a.no_sp2d, SUM(b.nilai) pot
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.kd_skpd=b.kd_skpd AND a.no_bukti=b.no_bukti
                                    WHERE (a.tgl_bukti>=? and a.tgl_bukti <=?) and b.kd_skpd=?
                                    GROUP BY b.kd_skpd, a.no_sp2d ) x
                                    LEFT JOIN
                                    (
                                    SELECT d.kd_skpd, d.no_spm, c.nilai nil_spm, d.no_sp2d, d.nilai nil_sp2d, d.jns_spp, c.nmrekan
                                    FROM trdspp a INNER JOIN trhspp b
                                    ON a.no_spp = b.no_spp AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspm c
                                    ON b.no_spp = c.no_spp AND a.kd_skpd = c.kd_skpd
                                    INNER JOIN trhsp2d d
                                    on c.no_spm = d.no_spm AND c.kd_skpd=d.kd_skpd
                                    WHERE (d.sp2d_batal=0 OR d.sp2d_batal is NULL) and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
                                    GROUP BY d.kd_skpd,d.no_spm, d.no_sp2d, c.nilai, d.nilai, d.jns_spp,c.nmrekan) y
                                    ON x.kd_skpd=y.kd_skpd AND x.no_sp2d=y.no_sp2d ) p
                                    UNION ALL

                                    SELECT 2 as urut, '' as no_spm,0 as nilai,p.no_sp2d,0 as nilai_belanja,
                                                        p.no_bukti, p.kode_belanja,p.kd_rek6,'' as jenis_pajak,p.pot as nilai_pot,p.npwp,
                                                        rekanan nmrekan,    case when p.jns_spp='6' or p.jns_spp='5' or  p.jns_spp='4' then p.keperluan else
                                    'No Set: ' + p.no_bukti end AS ket, p.jns_spp, p.ntpn
                                    FROM (
                                    SELECT x.*, y.keperluan FROM (
                                    SELECT b.kd_skpd, b.no_bukti, a.kd_sub_kegiatan+'.'+b.kd_rek_trans kode_belanja,
                                           RTRIM(b.kd_rek6) kd_rek6, a.no_sp2d, b.nilai pot, b.rekanan, b.npwp, b.ntpn, a.jns_spp
                                    FROM trhstrpot a INNER JOIN trdstrpot b ON a.kd_skpd=b.kd_skpd AND a.no_bukti=b.no_bukti
                                    WHERE (a.tgl_bukti>=? and a.tgl_bukti <=?) and b.kd_skpd=? ) x
                                    LEFT JOIN
                                    (
                                    SELECT d.kd_skpd, d.no_spm, c.nilai nil_spm, d.no_sp2d, d.nilai nil_sp2d, d.jns_spp, b.keperluan
                                    FROM trdspp a INNER JOIN trhspp b
                                    ON a.no_spp = b.no_spp AND a.kd_skpd = b.kd_skpd
                                    INNER JOIN trhspm c
                                    ON b.no_spp = c.no_spp AND a.kd_skpd = c.kd_skpd
                                    INNER JOIN trhsp2d d
                                    on c.no_spm = d.no_spm AND c.kd_skpd=d.kd_skpd
                                    WHERE (d.sp2d_batal=0 OR d.sp2d_batal is NULL) and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
                                    GROUP BY d.kd_skpd,d.no_spm, d.no_sp2d, c.nilai, d.nilai, d.jns_spp,b.keperluan) y
                                    ON x.kd_skpd=y.kd_skpd AND x.no_sp2d=y.no_sp2d ) p
                                    where p.kd_rek6 in ('2110301','2130101','2130201','2130301','2130401','2130501')
                                    ORDER BY no_sp2d,urut,no_spm,kode_belanja,kd_rek6", [$periode1, $periode2, $skpd, $periode1, $periode2, $skpd]);
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'bulan' => $bulan,
            'data_dth' => $dth,
            'spasi' => $spasi,
            'bendahara' => DB::table('ms_ttd')
                ->where(['nip' => $bendahara])
                ->first(),
            'pa_kpa' => DB::table('ms_ttd')
                ->where(['nip' => $pa_kpa])
                ->first(),
            'jenis_print' => $jenis_print,
            'skpd' => $skpd
        ];

        $judul = 'DTH';

        $view = view('bud.laporan_bendahara.cetak.dth')->with($data);

        if ($jenis_print == 'pdf' || $jenis_print == 'keseluruhan') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename= $judul.xls");
            echo $view;
        }
    }

    public function koreksiPenerimaan(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        $koreksi = DB::table('trkasout_ppkd as a')
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                // PILIHAN PER TANGGAL
                if ($pilihan == '1') {
                    $query->whereRaw("a.tanggal=?", [$tgl]);
                }
                //PILIHAN PER PERIODE
                elseif ($pilihan == '2') {
                    $query->whereRaw("a.tanggal between ? and ?", [$periode1, $periode2]);
                }
            })
            ->orderBy('tanggal')
            ->orderBy('no')
            ->get();

        $koreksi_lalu = DB::table('trkasout_ppkd as a')
            ->selectRaw("sum(a.nilai) as nilai")
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                // PILIHAN PER TANGGAL
                if ($pilihan == '1') {
                    $query->whereRaw("a.tanggal<?", [$tgl]);
                }
                //PILIHAN PER PERIODE
                elseif ($pilihan == '2') {
                    $query->whereRaw("a.tanggal<?", [$periode1]);
                }
            })
            ->first();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_koreksi' => $koreksi,
            'koreksi_lalu' => $koreksi_lalu->nilai,
            'tanda_tangan' => DB::table('ms_ttd')
                ->where(['kode' => 'BUD', 'nip' => $ttd])
                ->first(),
        ];

        $view = view('bud.laporan_bendahara.cetak.koreksi_penerimaan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function harianKasda(Request $request)
    {
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        if ($tgl == '2022-01-01' || $tgl == '2022-1-1') {
            $saldoawal = "SELECT '2022-01-01' as urut,0 as urut1,0 as kode,'0' as nomor,uraian,nilai as masuk,0 as keluar from buku_kas
		                  UNION ALL";
        } else {
            $saldoawal = "";
        }

        if ($tgl != '2022-01-02' || $tgl != '2022-1-2') {
            $saldoawals = "SELECT '2022-01-01' as urut,0 as urut1,0 as kode,'0' as nomor,uraian,nilai as masuk,0 as keluar from buku_kas
		                  UNION ALL";
        } else {
            $saldoawals = "";
        }

        $kas_kasda_lalu = collect(DB::select("SELECT SUM(masuk)as masuk, sum(keluar)as keluar FROM (
			SELECT tgl_kas_bud as urut,
		no_kas_bud as urut1,
		1 as kode,
		no_sp2d as nomor,a.keperluan as uraian,0 as masuk ,sum(b.nilai) as keluar from trhsp2d a inner join trdspp b
		on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd where status_bud=1 and tgl_kas_bud<?
		group by tgl_kas_bud,no_kas_bud,no_sp2d,a.keperluan
		UNION ALL
		$saldoawals
		-- LAIN-LAIN PENDAPATAN ASLI DAERAH YANG SAH
		SELECT a.tgl_kas,a.no_kas,3 as kode,a.no_kas,a.keterangan,SUM(rupiah) as masuk,0 as keluar
		FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas and a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
		WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus=3  and tgl_kas<?
		GROUP BY a.tgl_kas,a.no_kas,keterangan

		UNION ALL
		-- 4104	LAIN-LAIN PAD YANG SAH
		-- 4102	RETRIBUSI DAERAH
		-- 4103	HASIL PENGELOLAAN KEKAYAAN DAERAH YANG DIPISAHKAN
		-- 4201	PENDAPATAN TRANSFER PEMERINTAH PUSAT
		-- 4301	PENDAPATAN HIBAH
		-- 4101	PAJAK DAERAH
		SELECT a.tgl_kas,a.no_kas,3 as kode,a.no_kas,a.keterangan,SUM(rupiah) as masuk,0 as keluar
						FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas and a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
						LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,4)=c.kd_rek3
						WHERE LEFT(b.kd_rek6,1) IN ('4') and  b.kd_rek6 not in ('420101040001','420101040002','420101040003','410416010001') and a.tgl_kas<?
						GROUP BY a.tgl_kas,a.no_kas,keterangan

		UNION ALL
		-- CP
		SELECT  a.tgl_kas,a.no_kas,2 as kode,a.no_kas,a.keterangan,SUM(rupiah) as masuk,0 as keluar
		FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas and a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
		WHERE LEFT(b.kd_rek6,1) IN ('5','1','2') and pot_khusus<>3 and a.tgl_kas<?
		GROUP BY a.tgl_kas,a.no_kas,keterangan

		UNION ALL
		--PENGELUARAN NON SP2D
		SELECT tanggal,nomor,3,CAST(nomor as VARCHAR),keterangan,0,nilai FROM pengeluaran_non_sp2d x where tanggal<?

		UNION ALL
		-- RESTITUSI
		SELECT tgl_kas,a.no_kas,3,a.no_kas,keterangan,0,rupiah
		FROM trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts WHERE a.jns_trans=3 and tgl_kas<?

		UNION ALL
		-- KOREKSI
		SELECT tanggal,[no],3,[no],keterangan,nilai,0 FROM	 trkasout_ppkd w where tanggal<?

		UNION ALL
		-- KOREKSI PENGELUARAN
		SELECT tanggal,[no],2,[no],keterangan,0,nilai FROM	 trkoreksi_pengeluaran w where tanggal<?

		UNION ALL
		-- DEPOSITO
		SELECT tanggal,nomor,3,cast(nomor as VARCHAR),keterangan,nilai,0 FROM penerimaan_non_sp2d w WHERE w.jenis='1' and tanggal<?

		UNION ALL
		-- PENERIMAAN NON SP2D
		SELECT tanggal,nomor,3,cast(nomor as VARCHAR),keterangan,nilai,0 FROM penerimaan_non_sp2d w WHERE w.jenis='2' and tanggal<?

		UNION ALL
		-- KOREKSI PENERIMAAN
		SELECT tanggal,nomor,3,cast(nomor as VARCHAR),keterangan,nilai,0 FROM tkoreksi_penerimaan w WHERE w.jenis='1' and tanggal<?
		)zz
		", [$tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl]))->first();

        $kas_kasda = DB::select("SELECT 'sp2d' as jenis,c.jns_spp,c.jns_beban, tgl_kas_bud as urut,no_kas_bud as urut1, 1 as kode,
		no_sp2d as nomor,a.keperluan as uraian,0 as masuk ,sum(b.nilai) as keluar from trhsp2d a
		inner join trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd
		inner join trhspp c on a.no_spp=c.no_spp and a.kd_skpd=c.kd_skpd
		where status_bud=1 and tgl_kas_bud=?
		group by tgl_kas_bud,no_kas_bud,no_sp2d,a.keperluan,c.jns_spp,c.jns_beban
		UNION ALL
		$saldoawal
		-- LAIN-LAIN PENDAPATAN ASLI DAERAH YANG SAH
		SELECT 'LLPADYS' as jenis,'' as jns_spp, '' as jns_beban, a.tgl_kas,a.no_kas,3 as kode,a.no_kas,a.keterangan,SUM(rupiah) as masuk,0 as keluar
		FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas AND a.kd_skpd=b.kd_skpd
		WHERE LEFT(b.kd_rek6,1) IN ('5','1') and pot_khusus=3  and tgl_kas=?
		GROUP BY a.tgl_kas,a.no_kas,keterangan

		UNION ALL
		-- 4104	LAIN-LAIN PAD YANG SAH
		-- 4102	RETRIBUSI DAERAH
		-- 4103	HASIL PENGELOLAAN KEKAYAAN DAERAH YANG DIPISAHKAN
		-- 4201	PENDAPATAN TRANSFER PEMERINTAH PUSAT
		-- 4301	PENDAPATAN HIBAH
		-- 4101	PAJAK DAERAH
		SELECT 'PAD' as jenis,'' as jns_spp, '' as jns_beban, a.tgl_kas,a.no_kas,3 as kode,a.no_kas,a.keterangan,SUM(rupiah) as masuk,0 as keluar
						FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas and a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
						LEFT JOIN ms_rek3 c ON LEFT(b.kd_rek6,4)=c.kd_rek3
						WHERE LEFT(b.kd_rek6,1) IN ('4') and  b.kd_rek6 not in ('420101040001','420101040002','420101040003','410416010001') and a.tgl_kas=?
						GROUP BY a.tgl_kas,a.no_kas,keterangan

		UNION ALL
		-- CP
		SELECT  'CP' as jenis,'' as jns_spp, '' as jns_beban, a.tgl_kas,a.no_kas,2 as kode,a.no_kas,a.keterangan,SUM(rupiah) as masuk,0 as keluar
		FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b ON a.no_kas=b.no_kas and a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd
		WHERE LEFT(b.kd_rek6,1) IN ('5','1','2') and pot_khusus<>3 and a.tgl_kas=?
		GROUP BY a.tgl_kas,a.no_kas,keterangan

		UNION ALL
		--PENGELUARAN NON SP2D
		SELECT 'keluarnonsp2d' as jenis,'' as jns_spp, '' as jns_beban, tanggal,nomor,3,CAST(nomor as VARCHAR),keterangan,0,nilai FROM pengeluaran_non_sp2d x where tanggal=?

		UNION ALL
		-- RESTITUSI
		SELECT 'restitusi' as jenis,'' as jns_spp, '' as jns_beban, tgl_kas,a.no_kas,3,a.no_kas,keterangan,0,rupiah
		FROM trdrestitusi b inner join trhrestitusi a on a.kd_skpd=b.kd_skpd and a.no_kas=b.no_kas and a.no_sts=b.no_sts WHERE a.jns_trans=3 and tgl_kas=?

		UNION ALL
		-- KOREKSI
		SELECT 'koreksi' as jenis,'' as jns_spp, '' as jns_beban, tanggal,[no],3,[no],keterangan,nilai,0 FROM	 trkasout_ppkd w where tanggal=?

		UNION ALL
		-- KOREKSI PENGELUARAN
		SELECT 'koreksipengeluaran' as jenis,'' as jns_spp, '' as jns_beban, tanggal,[no],2,[no],keterangan,0,nilai FROM	 trkoreksi_pengeluaran w where tanggal=?

		UNION ALL
		-- DEPOSITO
		SELECT'deposito' as jenis,'' as jns_spp, '' as jns_beban, tanggal,nomor,3,cast(nomor as VARCHAR),keterangan,nilai,0 FROM penerimaan_non_sp2d w WHERE w.jenis='1' and tanggal=?

		UNION ALL
		-- PENERIMAAN NON SP2D
		SELECT 'terimanonsp2d' as jenis,'' as jns_spp, '' as jns_beban, tanggal,nomor,3,cast(nomor as VARCHAR),keterangan,nilai,0 FROM penerimaan_non_sp2d w WHERE w.jenis='2' and tanggal=?

		UNION ALL
		-- KOREKSI PENERIMAAN
		SELECT 'koreksiterima' as jenis,'' as jns_spp, '' as jns_beban, tanggal,nomor,3,cast(nomor as VARCHAR),keterangan,nilai,0 FROM tkoreksi_penerimaan w WHERE w.jenis='1' and tanggal=?
		ORDER BY urut,urut1", [$tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl, $tgl]);

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'tanggal' => $tgl,
            'data_kasda' => $kas_kasda,
            'kasda_lalu' => $kas_kasda_lalu,
            'tanda_tangan' => DB::table('ms_ttd')
                ->where(['kode' => 'BUD', 'nip' => $ttd])
                ->first(),
        ];

        $judul = 'KAS HARIAN KASDA';

        $view = view('bud.laporan_bendahara.cetak.harian_kasda')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename= $judul.xls");
            echo $view;
        }
    }

    public function uyhd(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;
        $tanda_tangan = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kode in ('BUD', 'PA')", [$ttd]))->first();

        if ($pilihan == '1') {
            $uyhd = DB::select("SELECT * from(
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						ISNULL(e.nm_pengirim, '') nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas=? AND a.kd_skpd !='1.20.15.17'  AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND LEFT(a.kd_rek6,6) NOT IN ('410416') AND a.kd_rek6 NOT IN ('420101040001')
					and b.keterangan like '%(UYHD)%'
					GROUP BY b.no_kas,nm_pengirim, f.nm_skpd

					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						c.nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					WHERE b.tgl_kas=? AND a.kd_skpd !='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND LEFT(a.kd_rek6,6) NOT IN ('410416') AND a.kd_rek6 NOT IN ('420101040001')
					and b.keterangan like '%(UYHD)%'
					UNION ALL
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						''nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas=? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND a.kd_rek6 NOT IN ('420101040001')
					and b.keterangan like '%(UYHD)%'
					GROUP BY b.no_kas,f.nm_skpd
					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						b.keterangan nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas  AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					WHERE b.tgl_kas=? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND a.kd_rek6 NOT IN ('420101040001')
					and b.keterangan like '%(UYHD)%'

					UNION ALL
					SELECT
							1 AS urut,
							'' no_sts,
							'' kd_skpd,
							nm_skpd,
							'' kd_sub_kegiatan,
							'' kd_rek6,
							[no] as no_kas,
							'' tgl_kas,
							'' nm_pengirim,
							'' nm_rek6,
							0 rupiah
						FROM
							trkasout_ppkd
						WHERE
							tanggal = ? AND LEFT(kd_rek,4) IN ('4102','4103','4104','4201','4202') AND LEFT(kd_rek,5) NOT IN ('41407') AND kd_rek NOT IN ('420101040001')
						UNION ALL
						SELECT
								2 AS urut,
								[no] as no_sts,
								kd_skpd,
								'' nm_skpd,
								''kd_sub_kegiatan,
								kd_rek kd_rek6,
								[no] no_kas,
								[tanggal] tgl_kas,
								'' nm_pengirim,
								keterangan+' '+nm_rek nm_rek6,
								nilai rupiah
							FROM
							trkasout_ppkd
							WHERE
							tanggal = ?
							AND LEFT(kd_rek,4) IN ('4102','4103','4104','4201','4202') AND LEFT(kd_rek,5) NOT IN ('41407') AND kd_rek NOT IN ('420101040001')
					) a

					order by cast(no_kas as int),urut", [$tgl, $tgl, $tgl, $tgl, $tgl, $tgl]);
        } elseif ($pilihan == '2') {
            $uyhd = DB::select("SELECT * from(
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						ISNULL(e.nm_pengirim, '') nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas BETWEEN ? AND ? AND a.kd_skpd !='1.20.15.17'  AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND LEFT(a.kd_rek6,6) NOT IN ('410416') AND a.kd_rek6 NOT IN ('420101040001')
					and b.keterangan like '%(UYHD)%'
					GROUP BY b.no_kas,nm_pengirim, f.nm_skpd
					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						c.nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					WHERE b.tgl_kas BETWEEN ? AND ? AND a.kd_skpd !='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND LEFT(a.kd_rek6,6) NOT IN ('410416') AND a.kd_rek6 NOT IN ('420101040001')
					and b.keterangan like '%(UYHD)%'
					UNION ALL
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						''nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas BETWEEN ? AND ? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND a.kd_rek6 NOT IN ('420101040001')
					and b.keterangan like '%(UYHD)%'
					GROUP BY b.no_kas,f.nm_skpd
					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						b.keterangan nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas  AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					WHERE b.tgl_kas BETWEEN ? AND ? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND a.kd_rek6 NOT IN ('420101040001')
					and b.keterangan like '%(UYHD)%'

					UNION ALL
					SELECT
							1 AS urut,
							'' no_sts,
							'' kd_skpd,
							nm_skpd,
							'' kd_sub_kegiatan,
							'' kd_rek6,
							[no] as no_kas,
							'' tgl_kas,
							'' nm_pengirim,
							'' nm_rek6,
							0 rupiah
						FROM
							trkasout_ppkd
						WHERE
							tanggal BETWEEN ? AND ? AND LEFT(kd_rek,4) IN ('4102','4103','4104','4201','4202') AND LEFT(kd_rek,5) NOT IN ('41407') AND kd_rek NOT IN ('420101040001','410412010010')
						UNION ALL
						SELECT
								2 AS urut,
								[no] as no_sts,
								kd_skpd,
								'' nm_skpd,
								''kd_sub_kegiatan,
								kd_rek kd_rek6,
								[no] no_kas,
								[tanggal] tgl_kas,
								'' nm_pengirim,
								keterangan+' '+nm_rek nm_rek6,
								nilai rupiah
							FROM
							trkasout_ppkd
							WHERE
							tanggal BETWEEN ? AND ?
							AND LEFT(kd_rek,4) IN ('4102','4103','4104','4201','4202') AND LEFT(kd_rek,5) NOT IN ('41407') AND kd_rek NOT IN ('420101040001','410412010010')
					) a

					order by cast(no_kas as int),urut", [$periode1, $periode2, $periode1, $periode2, $periode1, $periode2, $periode1, $periode2, $periode1, $periode2, $periode1, $periode2,]);
        }

        if ($pilihan == '1') {
            $uyhd_lalu = collect(DB::select("SELECT sum(rupiah) as nilai from(
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						ISNULL(e.nm_pengirim, '') nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas <=? AND a.kd_skpd !='1.20.15.17'  AND LEFT(a.kd_rek6,4) IN ('4102')
					and b.keterangan like '%(UYHD)%'
					GROUP BY b.no_kas,nm_pengirim, f.nm_skpd
					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						c.nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
					WHERE b.tgl_kas<=? AND a.kd_skpd !='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102')
					and b.keterangan like '%(UYHD)%'

					UNION ALL
					SELECT
						1 as urut,
						''no_sts,
						''kd_skpd,
						f.nm_skpd,
						''kd_sub_kegiatan,
						''kd_rek6,
						b.no_kas,
						''tgl_kas,
						''nm_pengirim,
						''nm_rek6,
						0 rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
					WHERE b.tgl_kas<=? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102')
					and b.keterangan like '%(UYHD)%'
					GROUP BY b.no_kas,f.nm_skpd
					UNION ALL
					SELECT
						2 as urut,
						b.no_sts,
						a.kd_skpd,
						'' nm_skpd,
						a.kd_sub_kegiatan,
						a.kd_rek6,
						b.no_kas,
						b.tgl_kas,
						'' nm_pengirim,
						b.keterangan nm_rek6,
						a.rupiah
					FROM
						trdkasin_ppkd a
					INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas  AND a.kd_skpd=b.kd_skpd
					INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
					WHERE b.tgl_kas<=? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102')
					and b.keterangan like '%(UYHD)%'

					UNION ALL
					SELECT
							1 AS urut,
							'' no_sts,
							'' kd_skpd,
							nm_skpd,
							'' kd_sub_kegiatan,
							'' kd_rek6,
							[no] as no_kas,
							'' tgl_kas,
							'' nm_pengirim,
							'' nm_rek6,
							0 rupiah
						FROM
							trkasout_ppkd
						WHERE
							tanggal <= ? AND LEFT(kd_rek,4) IN ('4102')
						UNION ALL
						SELECT
								2 AS urut,
								[no] as no_sts,
								kd_skpd,
								'' nm_skpd,
								''kd_sub_kegiatan,
								kd_rek kd_rek6,
								[no] no_kas,
								[tanggal] tgl_kas,
								'' nm_pengirim,
								keterangan+' '+nm_rek nm_rek6,
								nilai rupiah
							FROM
							trkasout_ppkd
							WHERE
							tanggal <= ?
							AND LEFT(kd_rek,4) IN ('4102')
					) a", [$tgl, $tgl, $tgl, $tgl, $tgl, $tgl]))->first();
        } elseif ($pilihan == '2') {
            $uyhd_lalu = collect(DB::select("SELECT SUM(rupiah) nilai from(
			SELECT
				1 as urut,
				''no_sts,
				''kd_skpd,
				f.nm_skpd,
				''kd_sub_kegiatan,
				''kd_rek6,
				b.no_kas,
				''tgl_kas,
				ISNULL(e.nm_pengirim, '') nm_pengirim,
				''nm_rek6,
				0 rupiah
			FROM
				trdkasin_ppkd a
			INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
			INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
			LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
			INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
			WHERE b.tgl_kas < ? AND a.kd_skpd !='1.20.15.17'  AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND LEFT(a.kd_rek6,6) NOT IN ('410412','410416') AND a.kd_rek6 NOT IN ('420101040001')
			and b.keterangan like '%(UYHD)%'
			GROUP BY b.no_kas,nm_pengirim, f.nm_skpd
			UNION ALL
			SELECT
				2 as urut,
				b.no_sts,
				a.kd_skpd,
				'' nm_skpd,
				a.kd_sub_kegiatan,
				a.kd_rek6,
				b.no_kas,
				b.tgl_kas,
				'' nm_pengirim,
				c.nm_rek6,
				a.rupiah
			FROM
				trdkasin_ppkd a
			INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
			INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
			LEFT JOIN ms_pengirim e ON b.sumber = e.kd_pengirim and e.kd_skpd=b.kd_skpd
			WHERE b.tgl_kas < ? AND a.kd_skpd !='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND LEFT(a.kd_rek6,6) NOT IN ('410412','410416') AND a.kd_rek6 NOT IN ('420101040001')
			and b.keterangan like '%(UYHD)%'
			UNION ALL
			SELECT
				1 as urut,
				''no_sts,
				''kd_skpd,
				f.nm_skpd,
				''kd_sub_kegiatan,
				''kd_rek6,
				b.no_kas,
				''tgl_kas,
				''nm_pengirim,
				''nm_rek6,
				0 rupiah
			FROM
				trdkasin_ppkd a
			INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas AND a.kd_skpd=b.kd_skpd
			INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
			INNER JOIN ms_skpd f ON a.kd_skpd = f.kd_skpd
			WHERE b.tgl_kas < ? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND a.kd_rek6 NOT IN ('420101040001')
			and b.keterangan like '%(UYHD)%'
			GROUP BY b.no_kas,f.nm_skpd
			UNION ALL
			SELECT
				2 as urut,
				b.no_sts,
				a.kd_skpd,
				'' nm_skpd,
				a.kd_sub_kegiatan,
				a.kd_rek6,
				b.no_kas,
				b.tgl_kas,
				'' nm_pengirim,
				b.keterangan nm_rek6,
				a.rupiah
			FROM
				trdkasin_ppkd a
			INNER JOIN trhkasin_ppkd b ON a.no_kas = b.no_kas  AND a.kd_skpd=b.kd_skpd
			INNER JOIN ms_rek6 c ON a.kd_rek6 = c.kd_rek6
			WHERE b.tgl_kas < ? AND a.kd_skpd ='1.20.15.17' AND LEFT(a.kd_rek6,4) IN ('4102','4103','4104','4201','4202') AND LEFT(a.kd_rek6,5) NOT IN ('41407') AND a.kd_rek6 NOT IN ('420101040001')
			and b.keterangan like '%(UYHD)%'

			UNION ALL
			SELECT
					1 AS urut,
					'' no_sts,
					'' kd_skpd,
					nm_skpd,
					'' kd_sub_kegiatan,
					'' kd_rek6,
					[no] as no_kas,
					'' tgl_kas,
					'' nm_pengirim,
					'' nm_rek6,
					0 rupiah
				FROM
					trkasout_ppkd
				WHERE
					tanggal < ? AND LEFT(kd_rek,4) IN ('4102','4103','4104','4201','4202') AND LEFT(kd_rek,5) NOT IN ('41407') AND kd_rek NOT IN ('420101040001','410412010010')
				UNION ALL
				SELECT
						2 AS urut,
						[no] as no_sts,
						kd_skpd,
						'' nm_skpd,
						''kd_sub_kegiatan,
						kd_rek kd_rek6,
						[no] no_kas,
						[tanggal] tgl_kas,
						'' nm_pengirim,
						keterangan+' '+nm_rek nm_rek6,
						nilai rupiah
					FROM
					trkasout_ppkd
					WHERE
					tanggal < ?
					AND LEFT(kd_rek,4) IN ('4102','4103','4104','4201','4202') AND LEFT(kd_rek,5) NOT IN ('41407') AND kd_rek NOT IN ('420101040001','410412010010')
			) a", [$periode1, $periode1, $periode1, $periode1, $periode1, $periode1]))->first();
        }


        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_uyhd' => $uyhd,
            'spasi' => $spasi,
            'uyhd_lalu' => $uyhd_lalu->nilai,
            // 'tanda_tangan' => DB::table('ms_ttd')
            //     ->where(['kode' => 'BUD', 'nip' => $ttd])
            //     ->first(),
            'tanda_tangan' => $tanda_tangan,
        ];

        $view = view('bud.laporan_bendahara.cetak.uyhd')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function koreksiPengeluaran(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        $koreksi = DB::table('trkoreksi_pengeluaran')
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                // PILIHAN TANGGAL
                if ($pilihan == '1') {
                    $query->where('tanggal', $tgl);
                } elseif ($pilihan == '2') {
                    $query->whereRaw("tanggal between ? and ?", [$periode1, $periode2]);
                }
            })
            ->orderBy('tanggal')
            ->orderBy('no')
            ->get();

        $koreksi_lalu = DB::table('trkoreksi_pengeluaran')
            ->selectRaw("sum(nilai) as nilai")
            ->where(function ($query) use ($pilihan, $tgl, $periode1) {
                // PILIHAN TANGGAL
                if ($pilihan == '1') {
                    $query->where('tanggal', '<', $tgl);
                } elseif ($pilihan == '2') {
                    $query->where('tanggal', '<', $periode1);
                }
            })
            ->first();


        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_koreksi' => $koreksi,
            'koreksi_lalu' => $koreksi_lalu->nilai,
            'tanda_tangan' => DB::table('ms_ttd')
                ->where(['kode' => 'BUD', 'nip' => $ttd])
                ->first(),
        ];

        $view = view('bud.laporan_bendahara.cetak.koreksi_pengeluaran')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function koreksiPenerimaan2(Request $request)
    {
        $pilihan = $request->pilihan;
        $periode1 = $request->periode1;
        $periode2 = $request->periode2;
        $tgl = $request->tgl;
        $halaman = $request->halaman;
        $spasi = $request->spasi;
        $ttd = $request->ttd;
        $jenis_print = $request->jenis_print;

        $koreksi = DB::table('tkoreksi_penerimaan')
            ->whereIn('jenis', ['1'])
            ->where(function ($query) use ($pilihan, $tgl, $periode1, $periode2) {
                // PILIHAN TANGGAL
                if ($pilihan == '1') {
                    $query->where('tanggal', $tgl);
                } elseif ($pilihan == '2') {
                    $query->whereRaw("tanggal between ? and ?", [$periode1, $periode2]);
                }
            })
            ->get();

        $koreksi_lalu = DB::table('tkoreksi_penerimaan')
            ->whereIn('jenis', ['1'])
            ->selectRaw("sum(nilai) as nilai")
            ->where(function ($query) use ($pilihan, $tgl, $periode1) {
                // PILIHAN TANGGAL
                if ($pilihan == '1') {
                    $query->where('tanggal', '<', $tgl);
                } elseif ($pilihan == '2') {
                    $query->where('tanggal', '<', $periode1);
                }
            })
            ->first();


        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $pilihan,
            'tanggal' => $tgl,
            'periode1' => $periode1,
            'periode2' => $periode2,
            'data_koreksi' => $koreksi,
            'koreksi_lalu' => $koreksi_lalu->nilai,
            'tanda_tangan' => DB::table('ms_ttd')
                ->where(['kode' => 'BUD', 'nip' => $ttd])
                ->first(),
        ];

        $view = view('bud.laporan_bendahara.cetak.koreksi_penerimaan2')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        }
    }

    public function registerSp2d(Request $request)
    {
        $req = $request->all();
        // dd($req);
        $join1 = DB::table('trdspp')
            ->selectRaw("no_spp, sum(nilai) [nilai]")
            ->groupBy('no_spp');

        $register_sp2d = DB::table('trhspm as a')
            ->join('trhsp2d as b', function ($join) {
                $join->on('a.no_spm', '=', 'b.no_spm');
                // $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->joinSub($join1, 'c', function ($join) {
                $join->on('a.no_spp', '=', 'c.no_spp');
            })
            ->selectRaw("a.kd_skpd,a.nm_skpd,a.no_spm,a.tgl_spm,b.tgl_sp2d,b.no_sp2d,b.keperluan,
				(case when a.jns_spp=1 then c.nilai else 0  end)up,
				(case when a.jns_spp=2 then c.nilai else 0  end)gu,
				(case when a.jns_spp=3 then c.nilai else 0  end)tu,
				(case when a.jns_spp=4 then c.nilai else 0  end)gaji,
				(case when a.jns_spp=6 then c.nilai else 0  end)ls,
                (case when a.jns_spp=5 then c.nilai else 0  end)ph3")
            ->where(function ($query) use ($req) {
                if ($req['pilihan'] == '11' || $req['pilihan'] == '12' || $req['pilihan'] == '13') {
                    $query->whereRaw("(b.sp2d_batal IS NULL  OR b.sp2d_batal !=1)");
                } else {
                    $query->whereRaw("(b.sp2d_batal IS NULL  OR b.sp2d_batal !=1) and a.kd_skpd =?", [$req['kd_skpd']]);
                }
            })
            ->where(function ($query) use ($req) {
                if ($req['status'] == '2') {
                    $query->whereRaw("status_bud=?", ['1']);
                } else if ($req['status'] == '3') {
                    $query->whereRaw("no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)");
                } else if ($req['status'] == '4') {
                    $query->whereRaw("no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji) and status_bud <> 1");
                } else if ($req['status'] == '5') {
                    $query->whereRaw("no_sp2d NOT IN (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)");
                }
            })
            ->where(function ($query) use ($req) {
                if (substr($req['pilihan'], -1) == '2') {
                    if ($req['status'] == '2') {
                        $query->whereRaw("MONTH(tgl_kas_bud)=?", [$req['bulan']]);
                    } else {
                        $query->whereRaw("MONTH(tgl_sp2d)=?", [$req['bulan']]);
                    }
                } elseif (substr($req['pilihan'], -1) == '3') {
                    if ($req['status'] == '2') {
                        $query->whereRaw("( tgl_kas_bud between ? and ?)", [$req['periode1'], $req['periode2']]);
                    } else {
                        $query->whereRaw("( tgl_sp2d between ? and ?)", [$req['periode1'], $req['periode2']]);
                    }
                }
            })
            ->where(function ($query) use ($req) {
                if ($req['urutan'] == '1') {
                    $query->orderBy('tgl_sp2d')->orderBy('no_sp2d');
                } else if ($req['urutan'] == '2') {
                    $query->orderByRaw("CAST(no_kas_bud as int)");
                }
            })
            ->get();

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $req['pilihan'],
            'data_awal' => $req,
            'register_sp2d' => $register_sp2d
        ];

        $view = view('bud.laporan_bendahara.cetak.register_sp2d')->with($data);

        if ($req['jenis_print'] == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', $req['margin_kiri'])
                ->setOption('margin-right', $req['margin_kanan'])
                ->setOption('margin-top', $req['margin_atas'])
                ->setOption('margin-bottom', $req['margin_bawah']);
                // ->setOption('margin-left', 5)
                // ->setOption('margin-right', 5)
                // ->setOption('margin-top', 15)
                // ->setOption('margin-bottom', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($req['jenis_print'] == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="Register SP2D' . '.xls"');
            return $view;
        }
    }

    public function realisasiSp2d(Request $request)
    {
        $req = $request->all();

        if (substr($req['pilihan'], -1) == '2') {
            if ($req['status'] == '2') {
                $where3 = "and MONTH(tgl_kas_bud)=?";
            } else {
                $where3 = "and MONTH(tgl_sp2d)=?";
            }
        } elseif (substr($req['pilihan'], -1) == '3') {
            if ($req['status'] == '2') {
                $where3 = "and ( tgl_kas_bud between ? and ?)";
            } else {
                $where3 = "and ( tgl_sp2d between ? and ?)";
            }
        }

        if (substr($req['pilihan'], -1) == '2') {
            $realisasi_sp2d = DB::select("SELECT a.kd_skpd as kode ,a.nm_skpd as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_skpd a
				LEFT JOIN
				(SELECT a.kd_skpd
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT a.kd_skpd, a.nm_skpd
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY a.kd_skpd, a.nm_skpd)a
				LEFT JOIN
				(SELECT a.kd_skpd
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				$where3
				GROUP BY a.kd_skpd)b
				ON a.kd_skpd=b.kd_skpd)c
				ON a.kd_skpd=c.kd_skpd
				UNION ALL
				SELECT a.kd_org as kode ,a.nm_org as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_organisasi a
				LEFT JOIN
				(SELECT a.kode
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT LEFT(a.kd_skpd,17) as kode
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY  LEFT(a.kd_skpd,17))a
				LEFT JOIN
				(SELECT LEFT(a.kd_skpd,17) as kode
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				$where3
				GROUP BY LEFT(a.kd_skpd,17))b
				ON a.kode=b.kode)c
				ON a.kd_org=c.kode

				UNION ALL

				SELECT a.kd_bidang_urusan as kode ,a.nm_bidang_urusan as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_bidang_urusan a
				LEFT JOIN
				(SELECT a.kode
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT LEFT(a.kd_skpd,4) as kode
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY LEFT(a.kd_skpd,4))a
				LEFT JOIN
				(SELECT LEFT(a.kd_skpd,4) as kode
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				$where3
				GROUP BY LEFT(a.kd_skpd,4))b
				ON a.kode=b.kode)c
				ON a.kd_bidang_urusan=c.kode

				UNION ALL

				SELECT a.kd_urusan as kode ,a.nm_urusan as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_urusan a
				LEFT JOIN
				(SELECT a.kode
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT LEFT(a.kd_skpd,1) as kode
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY LEFT(a.kd_skpd,1))a
				LEFT JOIN
				(SELECT LEFT(a.kd_skpd,1) as kode
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				$where3
				GROUP BY LEFT(a.kd_skpd,1))b
				ON a.kode=b.kode)c
				ON a.kd_urusan=c.kode
				ORDER BY kode", [$req['anggaran'], $req['bulan'], $req['anggaran'], $req['bulan'], $req['anggaran'], $req['bulan'], $req['anggaran'], $req['bulan']]);
        } elseif (substr($req['pilihan'], -1) == '3') {
            $realisasi_sp2d = DB::select("SELECT a.kd_skpd as kode ,a.nm_skpd as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_skpd a
				LEFT JOIN
				(SELECT a.kd_skpd
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT a.kd_skpd, a.nm_skpd
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY a.kd_skpd, a.nm_skpd)a
				LEFT JOIN
				(SELECT a.kd_skpd
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				$where3
				GROUP BY a.kd_skpd)b
				ON a.kd_skpd=b.kd_skpd)c
				ON a.kd_skpd=c.kd_skpd
				UNION ALL
				SELECT a.kd_org as kode ,a.nm_org as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_organisasi a
				LEFT JOIN
				(SELECT a.kode
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT LEFT(a.kd_skpd,17) as kode
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY  LEFT(a.kd_skpd,17))a
				LEFT JOIN
				(SELECT LEFT(a.kd_skpd,17) as kode
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				$where3
				GROUP BY LEFT(a.kd_skpd,17))b
				ON a.kode=b.kode)c
				ON a.kd_org=c.kode

				UNION ALL

				SELECT a.kd_bidang_urusan as kode ,a.nm_bidang_urusan as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_bidang_urusan a
				LEFT JOIN
				(SELECT a.kode
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT LEFT(a.kd_skpd,4) as kode
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY LEFT(a.kd_skpd,4))a
				LEFT JOIN
				(SELECT LEFT(a.kd_skpd,4) as kode
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				$where3
				GROUP BY LEFT(a.kd_skpd,4))b
				ON a.kode=b.kode)c
				ON a.kd_bidang_urusan=c.kode

				UNION ALL

				SELECT a.kd_urusan as kode ,a.nm_urusan as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_urusan a
				LEFT JOIN
				(SELECT a.kode
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT LEFT(a.kd_skpd,1) as kode
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY LEFT(a.kd_skpd,1))a
				LEFT JOIN
				(SELECT LEFT(a.kd_skpd,1) as kode
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				$where3
				GROUP BY LEFT(a.kd_skpd,1))b
				ON a.kode=b.kode)c
				ON a.kd_urusan=c.kode
				ORDER BY kode", [$req['anggaran'], $req['periode1'], $req['periode2'], $req['anggaran'], $req['periode1'], $req['periode2'], $req['anggaran'], $req['periode1'], $req['periode2'], $req['anggaran'], $req['periode1'], $req['periode2']]);
        } elseif (substr($req['pilihan'], -1) == '1') {
            $realisasi_sp2d = DB::select("SELECT a.kd_skpd as kode ,a.nm_skpd as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_skpd a
				LEFT JOIN
				(SELECT a.kd_skpd
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT a.kd_skpd, a.nm_skpd
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY a.kd_skpd, a.nm_skpd)a
				LEFT JOIN
				(SELECT a.kd_skpd
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				GROUP BY a.kd_skpd)b
				ON a.kd_skpd=b.kd_skpd)c
				ON a.kd_skpd=c.kd_skpd
				UNION ALL
				SELECT a.kd_org as kode ,a.nm_org as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_organisasi a
				LEFT JOIN
				(SELECT a.kode
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT LEFT(a.kd_skpd,17) as kode
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY  LEFT(a.kd_skpd,17))a
				LEFT JOIN
				(SELECT LEFT(a.kd_skpd,17) as kode
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				GROUP BY LEFT(a.kd_skpd,17))b
				ON a.kode=b.kode)c
				ON a.kd_org=c.kode

				UNION ALL

				SELECT a.kd_bidang_urusan as kode ,a.nm_bidang_urusan as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_bidang_urusan a
				LEFT JOIN
				(SELECT a.kode
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT LEFT(a.kd_skpd,4) as kode
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY LEFT(a.kd_skpd,4))a
				LEFT JOIN
				(SELECT LEFT(a.kd_skpd,4) as kode
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				GROUP BY LEFT(a.kd_skpd,4))b
				ON a.kode=b.kode)c
				ON a.kd_bidang_urusan=c.kode

				UNION ALL

				SELECT a.kd_urusan as kode ,a.nm_urusan as nama
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM ms_urusan a
				LEFT JOIN
				(SELECT a.kode
				,ISNULL(ang,0) as ang
				,ISNULL(bel,0) as bel
				FROM
				(SELECT LEFT(a.kd_skpd,1) as kode
				,SUM(CASE WHEN LEFT(a.kd_rek6,1) in ('5','1') THEN a.nilai ELSE 0 END) AS ang
				FROM trdrka a where a.jns_ang=?
				GROUP BY LEFT(a.kd_skpd,1))a
				LEFT JOIN
				(SELECT LEFT(a.kd_skpd,1) as kode
				,SUM(CASE WHEN LEFT(d.kd_rek6,1) in ('5','1') THEN d.nilai ELSE 0 END) AS bel
				FROM trhsp2d a
				INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd = b.kd_skpd
				INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd = c.kd_skpd
				INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd = d.kd_skpd
				WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
				and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)
				GROUP BY LEFT(a.kd_skpd,1))b
				ON a.kode=b.kode)c
				ON a.kd_urusan=c.kode
				ORDER BY kode", [$req['anggaran'], $req['anggaran'], $req['anggaran'], $req['anggaran']]);
        }

        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $req['pilihan'],
            'data_awal' => $req,
            'register_sp2d' => $realisasi_sp2d,
        ];

        $view = view('bud.laporan_bendahara.cetak.realisasi_sp2d')->with($data);

        if ($req['jenis_print'] == 'pdf') {
            $pdf = PDF::loadHtml($view)
            ->setPaper('legal')
            ->setOrientation('landscape')
            ->setOption('margin-left', $req['margin_kiri'])
            ->setOption('margin-right', $req['margin_kanan'])
            ->setOption('margin-top', $req['margin_atas'])
            ->setOption('margin-bottom', $req['margin_bawah']);
            // ->setOption('margin-left', 5)
            // ->setOption('margin-right', 5)
            // ->setOption('margin-top', 15)
            // ->setOption('margin-bottom', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($req['jenis_print'] == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="Register SP2D' . '.xls"');
            return $view;
        }
    }

    public function realisasiSkpdSp2d(Request $request)
    {
        $req = $request->all();
        // dd($req);
        // return $req['dengan'];
        $realisasi1 = DB::table('trdrka')
            ->selectRaw("kd_skpd,nm_skpd,sum(nilai)
                    as anggaran,0 as realisasi ")
            ->whereRaw("left(kd_rek6,1)='5' and kd_sub_kegiatan NOT IN ('1.01.02.1.01.53','1.01.02.1.02.46','1.01.02.1.03.52') and jns_ang=? ", [$req['anggaran']])
            // ->where(function ($query) use ($req) {
            //     if ($req['dengan'] == 'true') {
            //         $query->whereRaw("LEFT(kd_rek6,1) in ('5') and right(kd_rek6,7) not in ('9999999','8888888')");
            //     } elseif ($req['tanpa'] == 'true') {
            //         $query->whereRaw("LEFT(kd_rek6,1) in ('5') and right(kd_rek6,7) not in ('9999999','8888888')");
            //     }
            // })
            ->whereRaw("
            kd_rek6 != (
             CASE WHEN kd_skpd=? THEN ('540203010001')
                  ELSE ('') END
            )
            AND
            kd_rek6 != (
                    CASE WHEN kd_skpd=? THEN ('530101010001')
                        ELSE ('') END
                    )
            AND
            kd_rek6 != (
                    CASE WHEN kd_skpd=? THEN ('540101020001')
                        ELSE ('') END
                    )
            AND
            kd_rek6 != (
                    CASE WHEN kd_skpd=? THEN ('540101010001')
                        ELSE ('')
            END
            )  ", ['5.02.0.00.0.00.02.0000', '5.02.0.00.0.00.02.0000', '5.02.0.00.0.00.02.0000', '5.02.0.00.0.00.02.0000'])
            ->groupBy('kd_skpd', 'nm_skpd');

        $realisasi2 = DB::table('trhsp2d as a')
            ->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("b.kd_bidang as kd_skpd, (select nm_skpd from ms_skpd where kd_skpd=kd_bidang)as nm_skpd,0 as anggaran,sum(b.nilai) as realisasi")
            ->whereRaw("(c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                    and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)")
            ->where(function ($query) use ($req) {
                if ($req['dengan'] == 'true') {
                    $query->whereRaw("LEFT(b.kd_rek6,1) in ('5','1') and right(b.kd_rek6,7) not in ('9999999','8888888')");
                } elseif ($req['tanpa'] == 'true') {
                    $query->whereRaw("LEFT(b.kd_rek6,1) in ('5') and right(b.kd_rek6,7) not in ('9999999','8888888')");
                }
            })
            ->where(function ($query) use ($req) {
                if (substr($req['pilihan'], -1) == '1') {
                    if ($req['status'] == '2') {
                        $query->whereRaw("status_bud=1");
                    }
                } elseif (substr($req['pilihan'], -1) == '2') {
                    if ($req['status'] == '2') {
                        $query->whereRaw("MONTH(tgl_kas_bud)=?", [$req['bulan']]);
                    } else {
                        $query->whereRaw("MONTH(tgl_sp2d)=?", [$req['bulan']]);
                    }
                } elseif (substr($req['pilihan'], -1) == '3') {
                    if ($req['status'] == '2') {
                        $query->whereRaw("( tgl_kas_bud between ? and ?)", [$req['periode1'], $req['periode2']]);
                    } else {
                        $query->whereRaw("( tgl_sp2d between ? and ?)", [$req['periode1'], $req['periode2']]);
                    }
                }
            })
            ->whereRaw("
            b.kd_rek6 != (
             CASE WHEN c.kd_skpd=? THEN ('540203010001')
                  ELSE ('') END
            )
            AND
            b.kd_rek6 != (
                    CASE WHEN c.kd_skpd=? THEN ('530101010001')
                        ELSE ('') END
                    )
            AND
            b.kd_rek6 != (
                    CASE WHEN c.kd_skpd=? THEN ('540101020001')
                        ELSE ('') END
                    )
            AND
            b.kd_rek6 != (
                    CASE WHEN c.kd_skpd=? THEN ('540101010001')
                        ELSE ('')
            END
            )  ", ['5.02.0.00.0.00.02.0000', '5.02.0.00.0.00.02.0000', '5.02.0.00.0.00.02.0000', '5.02.0.00.0.00.02.0000'])
            ->groupBy('b.kd_bidang')
            ->union($realisasi1);

        $realisasi = DB::table(DB::raw("({$realisasi2->toSql()}) AS sub"))
            ->selectRaw("kd_skpd as kode,nm_skpd as nama,sum(anggaran)as ang,sum(realisasi)as bel")
            ->mergeBindings($realisasi2)
            ->groupByRaw("kd_skpd,nm_skpd")
            ->orderBy('kd_skpd')
            ->get();

        $blud_soedarso = DB::table('trdrka')
            ->selectRaw("kd_skpd,nm_skpd,sum(nilai)
                    as anggaran,0 as realisasi ")
            ->whereRaw("left(kd_rek6,1)='5' and right(kd_rek6,7) in ('9999999') and jns_ang=? and kd_skpd=?", [$req['anggaran'], ['1.02.0.00.0.00.02.0000']])
            // ->where(function ($query) use ($req) {
            //     if ($req['dengan'] == 'true') {
            //         $query->whereRaw("LEFT(kd_rek6,1) in ('5')");
            //     } elseif ($req['tanpa'] == 'true') {
            //         $query->whereRaw("LEFT(kd_rek6,1) in ('5') and right(kd_rek6,7) in ('9999999')");
            //     }
            // })
            ->groupBy('kd_skpd', 'nm_skpd')
            ->first();

        $blud_rsj = DB::table('trdrka')
            ->selectRaw("kd_skpd,nm_skpd,sum(nilai)
                    as anggaran,0 as realisasi ")
            ->whereRaw("left(kd_rek6,1)='5' and right(kd_rek6,7) in ('9999999') and jns_ang=? and kd_skpd=?", [$req['anggaran'], ['1.02.0.00.0.00.03.0000']])
            // ->where(function ($query) use ($req) {
            //     if ($req['dengan'] == 'true') {
            //         $query->whereRaw("LEFT(kd_rek6,1) in ('5')");
            //     } elseif ($req['tanpa'] == 'true') {
            //         $query->whereRaw("LEFT(kd_rek6,1) in ('5') and right(kd_rek6,7) in ('9999999')");
            //     }
            // })
            ->groupBy('kd_skpd', 'nm_skpd')
            ->first();

        $bos_dikbud = DB::table('trdrka')
            ->selectRaw("kd_skpd,nm_skpd,sum(nilai)
                    as anggaran,0 as realisasi ")
            ->whereRaw("jns_ang=? and kd_skpd=? and kd_sub_kegiatan IN ('1.01.02.1.01.53','1.01.02.1.02.46','1.01.02.1.03.52')", [$req['anggaran'], ['1.01.2.22.0.00.01.0000']])
            // ->where(function ($query) use ($req) {
            //     if ($req['dengan'] == 'true') {
            //         $query->whereRaw("LEFT(kd_rek6,1) in ('5')");
            //     } elseif ($req['tanpa'] == 'true') {
            //         $query->whereRaw("LEFT(kd_rek6,1) in ('5') and right(kd_rek6,7) in ('8888888')");
            //     }
            // })
            ->groupBy('kd_skpd', 'nm_skpd')
            ->first();
        // dd([
        //     $blud_soedarso, $blud_rsj, $bos_dikbud
        // ]);

        $bantuan_keuangan = DB::table('trdrka')
            ->selectRaw("kd_skpd,nm_skpd,sum(nilai)
                    as anggaran,0 as realisasi ")
            ->whereRaw("jns_ang=? and kd_skpd=? and kd_rek6 IN (?)", [$req['anggaran'], ['5.02.0.00.0.00.02.0000', '540203010001']])
            ->groupBy('kd_skpd', 'nm_skpd')
            ->first();

        $btt = DB::table('trdrka')
            ->selectRaw("kd_skpd,nm_skpd,sum(nilai)
                    as anggaran,0 as realisasi ")
            ->whereRaw("jns_ang=? and kd_skpd=? and kd_rek6 IN (?)", [$req['anggaran'], ['5.02.0.00.0.00.02.0000', '530101010001']])
            ->groupBy('kd_skpd', 'nm_skpd')
            ->first();

        $bagi_hasil = DB::table('trdrka')
            ->selectRaw("kd_skpd,nm_skpd,sum(nilai)
                    as anggaran,0 as realisasi ")
            ->whereRaw("jns_ang=? and kd_skpd=? and kd_rek6 IN (?,?)", [$req['anggaran'], ['5.02.0.00.0.00.02.0000', '540101020001', '540101010001']])
            ->groupBy('kd_skpd', 'nm_skpd')
            ->first();

        $pembiayaan = DB::table('trdrka')
            ->selectRaw("kd_skpd,nm_skpd,sum(nilai)
                    as anggaran,0 as realisasi ")
            ->whereRaw("jns_ang=? and kd_skpd=? and kd_sub_kegiatan=?", [$req['anggaran'], ['5.02.0.00.0.00.02.0000', '5.02.00.0.06.62']])
            ->groupBy('kd_skpd', 'nm_skpd')
            ->first();

        $realisasi_pembiayaan = DB::table('trhsp2d as a')
            ->join('trdspp as b', function ($join) {
                $join->on('a.no_spp', '=', 'b.no_spp');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->join('trhspp as c', function ($join) {
                $join->on('b.no_spp', '=', 'c.no_spp');
                $join->on('b.kd_skpd', '=', 'c.kd_skpd');
            })
            ->selectRaw("sum(b.nilai) as nilai")
            ->whereRaw("(c.sp2d_batal=0 OR c.sp2d_batal is NULL)
                    and no_sp2d in (select no_sp2d from trhuji a inner join trduji b on a.no_uji=b.no_uji)")
            ->where(['a.jns_spp' => '5', 'a.jenis_beban' => '8'])
            ->first();


        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'pilihan' => $req['pilihan'],
            'data_awal' => $req,
            'realisasi' => $realisasi,
            'tanda_tangan' => DB::table('ms_ttd')
                ->select('nip', 'nama', 'jabatan', 'pangkat')
                ->where(['nip' => $req['ttd']])
                ->whereIn('kode', ['BUD', 'PA'])
                ->first(),
            'tanggal' => now(),
            'dengan' => $req['dengan'],
            'blud_soedarso' => $blud_soedarso->anggaran,
            'blud_rsj' => $blud_rsj->anggaran,
            'bos_dikbud' => $bos_dikbud->anggaran,
            'bantuan_keuangan' => $bantuan_keuangan->anggaran,
            'btt' => $btt->anggaran,
            'bagi_hasil' => $bagi_hasil->anggaran,
            'pembiayaan' => $pembiayaan->anggaran,
            'realisasi_pembiayaan' => $realisasi_pembiayaan->nilai,
            'nama_anggaran' => DB::table('tb_status_anggaran')
                ->select('nama')
                ->where(['kode' => $req['anggaran']])
                ->first()
        ];

        $view = view('bud.laporan_bendahara.cetak.realisasi_skpd_sp2d')->with($data);
        if ($req['jenis_print'] == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOption('margin-left', $req['margin_kiri'])
                ->setOption('margin-right', $req['margin_kanan'])
                ->setOption('margin-top', $req['margin_atas'])
                ->setOption('margin-bottom', $req['margin_bawah']);
            return $pdf->stream('laporan.pdf');
        } elseif ($req['jenis_print'] == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="Register SP2D' . '.xls"');
            return $view;
        }
        // return view('bud.laporan_bendahara.cetak.realisasi_skpd_sp2d')->with($data);
    }

    //created by calvin
    public function cekDataAkuntansiB(Request $request)
    {
        $tgl = $request->tgl;
        $ttd = $request->ttd;
        $bln = $request->bulan;
        $jenis_print = $request->jenis_print;
        $tahun = tahun_anggaran();

        if ($ttd) {
            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD', 'PA'])->first();
        } else {
            $tanda_tangan = null;
        }

        $data_akuntansi = DB::select("SELECT z.kd_skpd AS kode_skpd,q.nm_skpd,isnull(SUM (z.spj_nilai),0) AS spj_nilai,isnull(SUM (z.lra),0) AS lra_rek,isnull(SUM (z.lra_input),0) AS lra_rek_input,isnull(SUM (z.cp),0) AS cp,isnull(SUM (z.hkpg),0) AS hkpg,SUM (z.lra)+SUM (z.lra_input)+SUM (z.cp)+SUM (z.hkpg) AS tt_lra,(SUM (z.spj_nilai)-SUM (z.cp)-SUM (z.hkpg)-(SUM (z.lra))) AS selisih,(CASE WHEN SUM (z.spj_nilai)-(SUM (z.lra)+SUM (z.cp) + sum (z.hkpg))='0' THEN 'Nilai Sesuai' ELSE 'Nilai Tidak Sesuai' END) AS ket FROM (
            SELECT a.kd_skpd,SUM (gaji_lalu)+SUM (gaji_ini)+SUM (brg_lalu)+SUM (brg_ini)+SUM (up_lalu)+SUM (up_ini)+SUM (jkn) AS spj_nilai,isnull(SUM (lra_rek_4),0) AS lra,isnull(SUM (lra_rek_4_input),0) AS lra_input,isnull(SUM (cp),0) AS cp,isnull(SUM (hkpg),0) AS hkpg,isnull(SUM (jkn),0) AS jkn_s,(CASE WHEN (SUM (gaji_lalu)+SUM (gaji_ini)+SUM (brg_lalu)+SUM (brg_ini)+SUM (up_lalu)+SUM (up_ini)+SUM (jkn))=SUM (lra_rek_4) THEN 'Nilai Sesuai' ELSE 'Nilai Tidak Sesuai' END) AS keterangan FROM (
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,isnull(a.nilai,0) AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)=? AND jns_spp IN (1,2,3) and panjar not in (5) AND pay NOT IN ('PANJAR') UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,isnull(a.nilai*-1,0) AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdinlain a JOIN TRHINLAIN b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.TGL_BUKTI)=? AND b.pengurang_belanja=1 UNION ALL
            SELECT a.kd_skpd,isnull(a.nilai,0) AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)=? AND panjar NOT IN (5) AND jns_spp IN (4) UNION ALL
            SELECT a.kd_skpd,isnull(a.nilai,0) AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)=? AND jns_spp IN (5) AND panjar NOT IN (5) UNION ALL
            SELECT a.kd_skpd,isnull(a.rupiah*-1,0) AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdkasin_pkd a JOIN trhkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_sts)=? AND b.jns_cp IN (1) AND b.pot_khusus NOT IN (0,1,2,3) UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,isnull(a.nilai,0) AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)=? AND jns_spp IN (6) UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,isnull(a.nilai,0) AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)=? AND jns_spp IN (5) AND LEFT (a.kd_rek6,3) IN ('515','516','531') UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,isnull(a.rupiah*-1,0) AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdkasin_pkd a JOIN trhkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_sts)=? AND b.jns_cp IN (2) AND b.pot_khusus NOT IN (0,1,2,3) UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,isnull(a.nilai,0) AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)< ? AND jns_spp IN (1,2,3) and panjar not in (5) AND pay NOT IN ('PANJAR') UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,isnull(a.nilai*-1,0) AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdinlain a JOIN TRHINLAIN b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.TGL_BUKTI)<=? AND b.pengurang_belanja=1 AND b.kd_skpd NOT IN ('4.02.01.00') UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,isnull(a.nilai,0) AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)< ? AND jns_spp IN (4) AND panjar NOT IN (5) UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,isnull(a.rupiah*-1,0) AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdkasin_pkd a JOIN trhkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_sts)< ? AND b.jns_cp IN (1) AND b.pot_khusus NOT IN (0,1,2,3) UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,isnull(a.nilai,0) AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)< ? AND panjar NOT IN (5) AND jns_spp IN (6) UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,isnull(a.nilai,0) AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)< ? AND jns_spp IN (5) AND panjar NOT IN (5) AND LEFT (a.kd_rek6,6) IN ('515','516','531') UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,isnull(a.rupiah*-1,0) AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdkasin_pkd a JOIN trhkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_sts)< ? AND b.jns_cp IN (2) AND b.pot_khusus NOT IN (0,1,2,3) UNION ALL
            SELECT a.kd_skpd,isnull(a.nilai,0) AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdtransout a JOIN trhtransout b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_bukti)< ? AND jns_spp IN (5) UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,isnull(SUM (b.debet)-SUM (b.kredit),0) AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.kd_skpd=b.kd_unit AND a.no_voucher=b.no_voucher WHERE YEAR (a.tgl_voucher)=? AND MONTH (a.tgl_voucher)<=? AND LEFT (b.kd_rek6,1) IN ('5','6') AND a.tabel='0' GROUP BY a.kd_skpd UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,0 AS hkpg,0 AS lra_rek_4,isnull(SUM (b.debet)-SUM (b.kredit),0) AS lra_rek_4_input,0 AS jkn FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.kd_skpd=b.kd_unit AND a.no_voucher=b.no_voucher WHERE YEAR (a.tgl_voucher)=? AND MONTH (a.tgl_voucher)<=? AND LEFT (b.kd_rek6,1) IN ('5','6') AND a.tabel='1' GROUP BY a.kd_skpd--nilai cp
            UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,isnull(a.rupiah,0) AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdkasin_pkd a JOIN trhkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_sts)=? AND b.pot_khusus NOT IN (1,2,3) AND b.jns_cp IN (1,2,3) UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,isnull(a.rupiah,0) AS cp,0 AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdkasin_pkd a JOIN trhkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_sts)< ? AND b.jns_cp IN (1,2,3) AND b.pot_khusus NOT IN (1,2,3)--end
            --nilai hkpg
            UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,isnull(a.rupiah,0) AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdkasin_pkd a JOIN trhkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_sts)=? AND b.jns_cp IN (1) AND b.pot_khusus IN (1,2,3) UNION ALL
            SELECT a.kd_skpd,0 AS gaji_ini,0 AS brg_ini,0 AS up_ini,0 AS gaji_lalu,0 AS brg_lalu,0 AS up_lalu,0 AS cp,isnull(a.rupiah,0) AS hkpg,0 AS lra_rek_4,0 AS lra_rek_4_input,0 AS jkn FROM trdkasin_pkd a JOIN trhkasin_pkd b ON a.no_sts=b.no_sts AND a.kd_skpd=b.kd_skpd WHERE MONTH (b.tgl_sts)< ? AND b.jns_cp IN (1) AND b.pot_khusus IN (1,2,3)--end
            ) a GROUP BY kd_skpd) z LEFT JOIN ms_skpd q ON z.kd_skpd=q.kd_skpd where z.kd_skpd not in ('1.02.0.00.0.00.01.0002','1.02.0.00.0.00.01.0003') GROUP BY z.kd_skpd,q.nm_skpd ORDER BY z.kd_skpd",[$bln,$bln,$bln,$bln,$bln,$bln,$bln,$bln,$bln,$bln,$bln,$bln,$bln,$bln,$bln,$bln,$tahun,$bln,$tahun,$bln,$bln,$bln,$bln,$bln,]);
           // dd($data_akuntansi);
        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'data_akuntansi' => $data_akuntansi,
            'tanda_tangan' => $tanda_tangan,
            'bulan' => $bln,
        ];

        // dd($data['data_akuntansi']);

        $view = view('bud.laporan_bendahara.cetak.cek_data_akuntansi_belanja')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename= Data Akuntansi Belanja.xls");
            echo $view;
        }
    }

    public function cekDataAkuntansiP(Request $request)
    {
        $tgl = $request->tgl;
        $ttd = $request->ttd;
        $bln = $request->bulan;
        $jenis_print = $request->jenis_print;
        $tahun = tahun_anggaran();

        if ($ttd) {
            $tanda_tangan = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $ttd])->whereIn('kode', ['BUD', 'PA'])->first();
        } else {
            $tanda_tangan = null;
        }

        $data_akuntansi = DB::select("SELECT x.kd_skp AS kode_skpd,q.nm_skpd,isnull(SUM (x.tr_tran),0) AS tr_trnsak,isnull(SUM (x.j_tran),0) AS trnsak,isnull(SUM (x.j_akun),0) AS jurnal,isnull(SUM (x.j_akun_input),0) AS jurnal_input,isnull(SUM (x.jml_tt_akun),0) AS tt_jurnal,isnull(SUM (x.selisih_set_tran),0) AS sels_tran,isnull(SUM (x.selisih_set_jur),0) AS sels_jur,(CASE WHEN SUM (x.selisih_set_tran)='0' AND SUM (x.selisih_set_jur)='0' THEN 'Nilai Sesuai' ELSE 'Nilai Tidak Sesuai' END) AS ket FROM (
            SELECT z.kd_skp,isnull(SUM (z.jml_akun),0) AS j_akun,isnull(SUM (z.jml_setor),0) AS j_tran,isnull(SUM (z.jml_terima),0) AS tr_tran,isnull(SUM (z.jml_akun_input),0) AS j_akun_input,SUM (z.jml_akun)+SUM (z.jml_akun_input) AS jml_tt_akun,SUM (z.jml_terima)-SUM (z.jml_setor) AS selisih_set_tran,SUM (z.jml_akun)-SUM (z.jml_setor) AS selisih_set_jur FROM (
            SELECT a.kd_skpd AS kd_skp,a.tgl_voucher AS bulan,SUM (b.kredit-b.debet) AS jml_akun,'0' AS jml_akun_input,'0' AS jml_setor,'0' AS jml_terima,'0' AS jml_terima_pusk FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.kd_skpd COLLATE DATABASE_DEFAULT=b.kd_unit COLLATE DATABASE_DEFAULT AND a.no_voucher COLLATE DATABASE_DEFAULT=b.no_voucher COLLATE DATABASE_DEFAULT WHERE LEFT (b.map_real,1)='4' AND YEAR (a.tgl_voucher)=? AND a.tabel NOT IN ('1') GROUP BY a.kd_skpd,a.tgl_voucher UNION ALL
            SELECT a.kd_skpd AS kd_skp,a.tgl_voucher AS bulan,'0' AS jml_akun,SUM (b.kredit-b.debet) AS jml_akun_input,'0' AS jml_setor,'0' AS jml_terima,'0' AS jml_terima_pusk FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.kd_skpd COLLATE DATABASE_DEFAULT=b.kd_unit COLLATE DATABASE_DEFAULT AND a.no_voucher COLLATE DATABASE_DEFAULT=b.no_voucher COLLATE DATABASE_DEFAULT WHERE LEFT (b.map_real,1)='4' AND YEAR (a.tgl_voucher)=? AND a.tabel IN ('1') GROUP BY a.kd_skpd,a.tgl_voucher UNION ALL
            SELECT a.kd_skpd AS kd_skp,a.tgl_sts AS bulan,'0' AS jml_akun,'0' AS jml_akun_input,SUM (b.rupiah) AS jml_setor,'0' AS jml_terima,'0' AS jml_terima_pusk FROM trhkasin_pkd a LEFT JOIN trdkasin_pkd b ON a.kd_skpd COLLATE DATABASE_DEFAULT=b.kd_skpd COLLATE DATABASE_DEFAULT AND a.no_sts COLLATE DATABASE_DEFAULT=b.no_sts COLLATE DATABASE_DEFAULT WHERE a.jns_trans IN ('4','2') AND LEFT (b.kd_rek6,1)='4' GROUP BY a.kd_skpd,a.tgl_sts UNION ALL
            SELECT a.kd_skpd AS kd_skp,a.tgl_sts AS bulan,'0' AS jml_akun,'0' AS jml_akun_input,SUM (b.rupiah) AS jml_setor,'0' AS jml_terima,'0' AS jml_terima_pusk FROM trhkasin_ppkd a LEFT JOIN trdkasin_ppkd b ON a.kd_skpd COLLATE DATABASE_DEFAULT=b.kd_skpd COLLATE DATABASE_DEFAULT AND a.no_sts COLLATE DATABASE_DEFAULT=b.no_sts COLLATE DATABASE_DEFAULT WHERE LEFT (b.kd_rek6,1)='4' AND a.kd_skpd='4.02.01.00' GROUP BY a.kd_skpd,a.tgl_sts UNION ALL
            SELECT kd_skpd AS kd_skp,tgl_terima AS bulan,'0' AS jml_akun,'0' AS jml_akun_input,'0' AS jml_setor,SUM (nilai) AS jml_terima,'0' AS jml_terima_pusk FROM tr_terima GROUP BY kd_skpd,tgl_terima UNION ALL
            SELECT a.kd_skpd AS kd_skp,a.tgl_sts AS bulan,'0' AS jml_akun,'0' AS jml_akun_input,'0' AS jml_setor,SUM (b.rupiah) AS jml_terima,'0' AS jml_terima_pusk FROM trhkasin_ppkd a LEFT JOIN trdkasin_ppkd b ON a.kd_skpd COLLATE DATABASE_DEFAULT=b.kd_skpd COLLATE DATABASE_DEFAULT AND a.no_sts COLLATE DATABASE_DEFAULT=b.no_sts COLLATE DATABASE_DEFAULT WHERE LEFT (b.kd_rek6,1)='4' AND a.kd_skpd='4.02.01.00' GROUP BY a.kd_skpd,a.tgl_sts) z WHERE MONTH (z.bulan)=? GROUP BY z.kd_skp) x LEFT JOIN ms_skpd q ON x.kd_skp COLLATE DATABASE_DEFAULT=q.kd_skpd COLLATE DATABASE_DEFAULT GROUP BY x.kd_skp,q.nm_skpd ORDER BY x.kd_skp ",[$tahun, $tahun, $bln]);
           // dd($data_akuntansi);
        $data = [
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'data_akuntansi' => $data_akuntansi,
            'tanda_tangan' => $tanda_tangan,
            'bulan' => $bln,
        ];

        // dd($data['data_akuntansi']);

        $view = view('bud.laporan_bendahara.cetak.cek_data_akuntansi_pendapatan')->with($data);

        if ($jenis_print == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setPaper('legal')
                ->setOrientation('landscape')
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);
            return $pdf->stream('laporan.pdf');
        } elseif ($jenis_print == 'layar') {
            return $view;
        } else {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename= Data Akuntansi Penerimaan.xls");
            echo $view;
        }
    }
}
