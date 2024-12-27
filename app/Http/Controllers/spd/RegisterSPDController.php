<?php

namespace App\Http\Controllers\spd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Knp\Snappy\Pdf as SnappyPdf;
use PDF;

class RegisterSPDController extends Controller
{
    public function index()
    {
        return view('penatausahaan.spd.register_spd.index');
    }

    public function getSKPD(Request $request)
    {
        $term = $request->term;

        $results = DB::table('ms_skpd')
            ->select('kd_skpd as id', 'nm_skpd as text', 'kd_skpd', 'nm_skpd')
            ->when($term, function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->orWhere('kd_skpd', 'like', '%' . $term . '%')
                        ->orWhere('nm_skpd', 'like', '%' . $term . '%');
                });
            })->get();

        return response()->json(['results' => $results]);
    }

    public function getNipSKPD(Request $request)
    {
        $term = $request->term;
        $kd_skpd = $request->kd_skpd;

        $results = DB::table('ms_ttd')
            ->select('nip as id', 'nama as text', 'nip', 'nama', 'jabatan', 'kd_skpd')
            ->whereIn('kode', ['PA', 'KPA'])->where('kd_skpd', $kd_skpd)
            ->when($term, function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->orWhere('nip', 'like', '%' . $term . '%')
                        ->orWhere('nama', 'like', '%' . $term . '%');
                });
            })->get();

        return response()->json(['results' => $results]);
    }

    public function CetakURS(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $nip = $request->nip_ttd;
        $tgl_ttd = $request->tgl_ttd;
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $jenis = $request->jenis;

        $total = 0;

        $skpd = DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')
            ->where(['kd_skpd' => $kd_skpd])->first();

        $ttd = Collect(DB::select("select * from ms_ttd where trim(nip) = ?",[$nip]))->first();

        $data = DB::table('trdspd as a')->select('a.no_spd', 'b.tgl_spd', 'b.kd_skpd', 'b.nm_skpd', 'c.nama', DB::raw('sum(a.nilai) as nilai'))
            ->join('trhspd as b', function ($join) {
                $join->on('a.no_spd', '=', 'b.no_spd');
            })->join('ms_ttd as c', function ($join) {
                $join->on('b.kd_bkeluar', '=', 'c.nip');
            })->whereRaw("b.tgl_spd >= ? and b.tgl_spd <= ? and b.kd_skpd = ?", [$tgl_awal, $tgl_akhir, $kd_skpd])
            ->groupBy(['a.no_spd', 'b.tgl_spd', 'b.kd_skpd', 'b.nm_skpd', 'c.nama'])
            ->orderBy('b.tgl_spd')->orderBy('a.no_spd')->orderBy('b.kd_skpd')->get();

        $view = view('penatausahaan.spd.register_spd.cetak.cetakurs', array(
            'jenis' => $jenis,
            'skpd' => $skpd,
            'kd_skpd' => $kd_skpd,
            'ttd' => $ttd,
            'tgl_ttd' => $tgl_ttd,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'data' => $data,
            'total' => $total,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } else if ($request->jenis == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    public function CetakSRS(Request $request)
    {
        $kd_skpd = left($request->kd_skpd, 17);
        $kdskpd = $request->kd_skpd;
        $nip = $request->nip_ttd;
        $tgl_ttd = $request->tgl_ttd;
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $jenis = $request->jenis;

        $total = 0;

        $skpd = DB::table('ms_organisasi')->select('kd_org', 'nm_org')
            ->where(['kd_org' => $kd_skpd])->first();

            $ttd = Collect(DB::select("select * from ms_ttd where trim(nip) = ?",[$nip]))->first();

        $data = DB::table('trdspd as a')->select('a.no_spd', 'b.tgl_spd', 'b.kd_skpd', 'b.nm_skpd', 'c.nama', DB::raw('sum(a.nilai) as nilai'))
            ->join('trhspd as b', function ($join) {
                $join->on('a.no_spd', '=', 'b.no_spd');
            })->join('ms_ttd as c', function ($join) {
                $join->on('b.kd_bkeluar', '=', 'c.nip');
            })->whereRaw("b.tgl_spd >= ? and b.tgl_spd <= ? and left(b.kd_skpd, 17) = ?", [$tgl_awal, $tgl_akhir, $kd_skpd])
            ->groupBy(['a.no_spd', 'b.tgl_spd', 'b.kd_skpd', 'b.nm_skpd', 'c.nama'])
            ->orderBy('b.tgl_spd')->orderBy('a.no_spd')->orderBy('b.kd_skpd')->get();

        $view = view('penatausahaan.spd.register_spd.cetak.cetaksrs', array(
            'jenis' => $jenis,
            'skpd' => $skpd,
            'kdskpd' => $kdskpd,
            'ttd' => $ttd,
            'tgl_ttd' => $tgl_ttd,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'data' => $data,
            'total' => $total,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } else if ($request->jenis == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    public function CetakKRS(Request $request)
    {
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $jenis = $request->jenis;

        $total = 0;

        $data = DB::table('trdspd as a')
            ->select('a.no_spd', 'b.tgl_spd', 'b.kd_skpd', 'b.nm_skpd', DB::raw('sum(a.nilai) as nilai'))
            ->selectRaw("(SELECT TOP 1 nama from ms_ttd where b.kd_bkeluar=nip) as nama")
            ->join('trhspd as b', function ($join) {
                $join->on('a.no_spd', '=', 'b.no_spd');
            })->whereRaw("b.tgl_spd >= ? and b.tgl_spd <= ?", [$tgl_awal, $tgl_akhir])
            ->groupBy(['a.no_spd', 'b.tgl_spd', 'b.kd_skpd', 'b.nm_skpd', 'b.kd_bkeluar'])
            ->orderBy('b.tgl_spd')->orderBy('a.no_spd')->orderBy('b.kd_skpd')->get();

        $view = view('penatausahaan.spd.register_spd.cetak.cetakkrs', array(
            'jenis' => $jenis,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'data' => $data,
            'total' => $total,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } else if ($request->jenis == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    public function CetakKRRS(Request $request)
    {
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $jenis = $request->jenis;

        $total = 0;

        $data = DB::table('trdspd as a')
            ->select('a.no_spd', 'b.tgl_spd', 'b.kd_skpd', 'b.nm_skpd', DB::raw('sum(a.nilai) as nilai'), 'revisi_ke', DB::raw('(SELECT isnull(max(revisi_ke),0) as revisi from trhspd where kd_skpd=b.kd_skpd and bulan_akhir=b.bulan_akhir) as revisi'))
            ->selectRaw("(SELECT TOP 1 nama from ms_ttd where b.kd_bkeluar=nip) as nama")
            ->join('trhspd as b', function ($join) {
                $join->on('a.no_spd', '=', 'b.no_spd');
            })
            // ->join('ms_ttd as c', function ($join) {
            //     $join->on('b.kd_bkeluar', '=', 'c.nip');
            // })
            ->whereRaw("b.tgl_spd >= ? and b.tgl_spd <= ?", [$tgl_awal, $tgl_akhir])
            ->groupBy(['a.no_spd', 'b.tgl_spd', 'b.kd_skpd', 'b.nm_skpd', 'bulan_awal', 'bulan_akhir', 'revisi_ke', 'b.kd_bkeluar']);
        // dd($data->get());
        $data1 = DB::table(DB::raw("({$data->toSql()}) AS sub"))
            ->mergeBindings($data)
            ->whereRaw("revisi_ke = revisi")
            ->orderBy('tgl_spd')->orderBy('no_spd')->orderBy('kd_skpd')->get();

        $view = view('penatausahaan.spd.register_spd.cetak.cetakkrrs', array(
            'jenis' => $jenis,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'data1' => $data1,
            'total' => $total,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } else if ($request->jenis == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }
}
