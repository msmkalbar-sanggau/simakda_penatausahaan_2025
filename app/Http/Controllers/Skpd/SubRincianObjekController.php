<?php

namespace App\Http\Controllers\Skpd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Static_;
use PDF;
use Knp\Snappy\Pdf as SnappyPdf;

class SubRincianObjekController extends Controller
{


    // Cetak cetakSubRincianObjek77
    public function cetakSubRincianObjek77(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $tanggal1       = $request->tanggal1;
        $tanggal2       = $request->tanggal2;
        $subkegiatan    = $request->subkegiatan;
        $akunbelanja    = $request->akunbelanja;
        $jns_anggaran   = $request->jns_anggaran;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;
        $tahun_anggaran = tahun_anggaran();

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        // $cari_pakpa     = DB::table('ms_ttd')
        //     ->select('nama', 'nip', 'jabatan', 'pangkat')
        //     ->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])
        //     ->whereIn('kode', ['PA', 'KPA'])
        //     ->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah         = DB::table('sclient')
            ->select('daerah')
            ->where('kd_skpd', $kd_skpd)
            ->first();
        $nm_skpd        = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $nm_akunbelanja = cari_nama($akunbelanja, 'ms_rek6', 'kd_rek6', 'nm_rek6');

        $dpa         = DB::table('trdrka')
            ->select(DB::raw("SUM(nilai) AS nilai"))
            ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => 'M', 'kd_rek6' => $akunbelanja, 'kd_sub_kegiatan' => $subkegiatan])
            ->first();
        $dppa        = DB::table('trdrka')
            ->select(DB::raw("SUM(nilai) AS nilai"))
            ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $jns_anggaran, 'kd_rek6' => $akunbelanja, 'kd_sub_kegiatan' => $subkegiatan])
            ->first();

        $lalu       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS lalu_up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS lalu_gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS lalu_ls")
            )
            ->where(['a.kd_sub_kegiatan' => $subkegiatan, 'a.kd_rek6' => $akunbelanja, 'b.kd_skpd' => $kd_skpd])
            ->whereRaw("b.tgl_bukti < ?", [$tanggal1])
            ->first();


        $rincian       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                DB::raw("ISNULL(a.no_sp2d,'') as no_sp2d"),
                DB::raw("ISNULL(a.no_bukti,'') as no_bukti"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls"),
                'b.tgl_bukti',
                'ket'
            )
            ->where(['a.kd_sub_kegiatan' => $subkegiatan, 'a.kd_rek6' => $akunbelanja, 'b.kd_skpd' => $kd_skpd])
            ->whereBetween('tgl_bukti', [$tanggal1, $tanggal2])
            ->groupBy('a.no_bukti', 'b.tgl_bukti', 'a.no_sp2d', 'ket')
            ->orderBy('tgl_bukti')
            ->orderBy('no_bukti')
            ->distinct()->get();
        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tanggal1'          => $tanggal1,
            'tanggal2'          => $tanggal2,
            'nm_skpd'           => $nm_skpd,
            'dpa'               => $dpa,
            'dppa'              => $dppa,
            'kd_akunbelanja'    => $akunbelanja,
            'nm_akunbelanja'    => $nm_akunbelanja,
            'lalu'              => $lalu,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.Subrincianobjek_77')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('Sub Rincian Objek.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="Sub Rincian Objek - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    // Cetak cetakSubRincianObjekRekening
    public function cetakSubRincianObjekRekening(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $tanggal1       = $request->tanggal1;
        $tanggal2       = $request->tanggal2;
        $subkegiatan    = $request->subkegiatan;
        $akunbelanja    = $request->akunbelanja;
        $jns_anggaran   = $request->jns_anggaran;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        // $cari_pakpa     = DB::table('ms_ttd')
        //     ->select('nama', 'nip', 'jabatan', 'pangkat')
        //     ->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])
        //     ->whereIn('kode', ['PA', 'KPA'])
        //     ->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah         = DB::table('sclient')
            ->select('daerah')
            ->where('kd_skpd', $kd_skpd)
            ->first();
        $nm_skpd        = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $nm_akunbelanja = cari_nama($akunbelanja, 'ms_rek6', 'kd_rek6', 'nm_rek6');
        $nm_subkegiatan = cari_nama($subkegiatan, 'ms_sub_kegiatan', 'kd_sub_kegiatan', 'nm_sub_kegiatan');

        $dpa         = DB::table('trdrka')
            ->select(DB::raw("SUM(nilai) AS nilai"))
            ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => 'M', 'kd_rek6' => $akunbelanja, 'kd_sub_kegiatan' => $subkegiatan])
            ->first();
        $dppa        = DB::table('trdrka')
            ->select(DB::raw("SUM(nilai) AS nilai"))
            ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $jns_anggaran, 'kd_rek6' => $akunbelanja, 'kd_sub_kegiatan' => $subkegiatan])
            ->first();

        $lalu       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls")
            )
            ->where(['a.kd_sub_kegiatan' => $subkegiatan, 'a.kd_rek6' => $akunbelanja, 'b.kd_skpd' => $kd_skpd])
            ->whereRaw("b.tgl_bukti < ?", [$tanggal1])
            ->first();

        $sd_ini       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls")
            )
            ->where(['a.kd_sub_kegiatan' => $subkegiatan, 'a.kd_rek6' => $akunbelanja, 'b.kd_skpd' => $kd_skpd])
            ->whereRaw("b.tgl_bukti <= ?", [$tanggal1])
            ->first();

        $rincian       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                DB::raw("ISNULL(a.no_sp2d,'') as no_sp2d"),
                DB::raw("ISNULL(a.no_bukti,'') as no_bukti"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls"),
                'b.tgl_bukti',
                'ket'
            )
            ->where(['a.kd_sub_kegiatan' => $subkegiatan, 'a.kd_rek6' => $akunbelanja, 'b.kd_skpd' => $kd_skpd])
            ->whereBetween('tgl_bukti', [$tanggal1, $tanggal2])
            ->groupBy('a.no_bukti', 'b.tgl_bukti', 'a.no_sp2d', 'ket')
            ->orderBy('tgl_bukti')
            ->orderBy('no_bukti')
            ->distinct()->get();


        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tanggal1'          => $tanggal1,
            'tanggal2'          => $tanggal2,
            'nm_skpd'           => $nm_skpd,
            'dpa'               => $dpa,
            'dppa'              => $dppa,
            'kd_subkegiatan'    => $subkegiatan,
            'nm_subkegiatan'    => $nm_subkegiatan,
            'kd_akunbelanja'    => $akunbelanja,
            'nm_akunbelanja'    => $nm_akunbelanja,
            'lalu'              => $lalu,
            'sd_ini'            => $sd_ini,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.Subrincianobjek_akunbelanja')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('Sub Rincian Objek.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="Sub Rincian Objek - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    // Cetak cetakSubRincianObjekSubkegiatan
    public function cetakSubRincianObjekSubkegiatan(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $tanggal1       = $request->tanggal1;
        $tanggal2       = $request->tanggal2;
        $subkegiatan    = $request->subkegiatan;
        $akunbelanja    = $request->akunbelanja;
        $jns_anggaran   = $request->jns_anggaran;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        // $cari_pakpa     = DB::table('ms_ttd')
        //     ->select('nama', 'nip', 'jabatan', 'pangkat')
        //     ->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])
        //     ->whereIn('kode', ['PA', 'KPA'])
        //     ->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah         = DB::table('sclient')
            ->select('daerah')
            ->where('kd_skpd', $kd_skpd)
            ->first();
        $nm_skpd        = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $nm_akunbelanja = cari_nama($akunbelanja, 'ms_rek6', 'kd_rek6', 'nm_rek6');
        $nm_subkegiatan = cari_nama($subkegiatan, 'ms_sub_kegiatan', 'kd_sub_kegiatan', 'nm_sub_kegiatan');

        $rekening       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select('a.kd_rek6')
            ->where(['a.kd_sub_kegiatan' => $subkegiatan, 'b.kd_skpd' => $kd_skpd])
            ->whereRaw('b.tgl_bukti <= ?', $tanggal2)
            ->distinct()->get();
        $kd_rek6        = array();
        foreach ($rekening as $rek) {
            $kd_rek6[] =  $rek->kd_rek6;
        }

        $rincian       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                'a.kd_rek6',
                DB::raw("ISNULL(a.no_sp2d,'') as no_sp2d"),
                DB::raw("ISNULL(a.no_bukti,'') as no_bukti"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls"),
                'b.tgl_bukti',
                'ket'
            )
            ->where(['a.kd_sub_kegiatan' => $subkegiatan, 'b.kd_skpd' => $kd_skpd])
            ->whereIn('a.kd_rek6', $kd_rek6) //kd_rek6 dari array
            ->whereBetween('tgl_bukti', [$tanggal1, $tanggal2])
            ->groupBy('a.no_bukti', 'b.tgl_bukti', 'a.kd_rek6', 'a.no_sp2d', 'ket')
            ->orderBy('tgl_bukti')
            ->orderBy('no_bukti')
            ->distinct()->get();

        $lalu       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                'a.kd_rek6',
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls")
            )
            ->where(['a.kd_sub_kegiatan' => $subkegiatan, 'b.kd_skpd' => $kd_skpd])
            ->whereIn('a.kd_rek6', $kd_rek6)
            ->whereRaw("b.tgl_bukti < ?", [$tanggal1])
            ->groupBy('a.kd_rek6')
            ->get();

        $sd_ini       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                'a.kd_rek6',
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls")
            )
            ->where(['a.kd_sub_kegiatan' => $subkegiatan, 'b.kd_skpd' => $kd_skpd])
            ->whereIn('a.kd_rek6', $kd_rek6)
            ->whereRaw("b.tgl_bukti <= ?", [$tanggal2])
            ->groupBy('a.kd_rek6')
            ->get();

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tanggal1'          => $tanggal1,
            'tanggal2'          => $tanggal2,
            'nm_skpd'           => $nm_skpd,
            'kd_subkegiatan'    => $subkegiatan,
            'nm_subkegiatan'    => $nm_subkegiatan,
            'rekening'          => $rekening,
            'lalu'              => $lalu,
            'sd_ini'            => $sd_ini,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.Subrincianobjek_subkegiatan')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('Sub Rincian Objek.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="Sub Rincian Objek - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    // Cetak cetakSubRincianObjekSkpd
    public function cetakSubRincianObjekSkpd(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $tanggal1       = $request->tanggal1;
        $tanggal2       = $request->tanggal2;
        $subkegiatan    = $request->subkegiatan;
        $akunbelanja    = $request->akunbelanja;
        $jns_anggaran   = $request->jns_anggaran;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        // $cari_pakpa     = DB::table('ms_ttd')
        //     ->select('nama', 'nip', 'jabatan', 'pangkat')
        //     ->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])
        //     ->whereIn('kode', ['PA', 'KPA'])
        //     ->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah         = DB::table('sclient')
            ->select('daerah')
            ->where('kd_skpd', $kd_skpd)
            ->first();
        $nm_skpd        = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');

        $headers       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                'a.kd_rek6',
                'a.kd_sub_kegiatan'
            )
            ->where('b.kd_skpd', $kd_skpd)
            ->whereRaw('b.tgl_bukti <= ?', $tanggal2)
            ->orderBy('kd_sub_kegiatan')
            ->orderBy('kd_rek6')
            ->distinct()->get();
        $header        = array();
        foreach ($headers as $rek) {
            $header[] =  $rek->kd_sub_kegiatan . '.' . $rek->kd_rek6;
        }

        $rincian       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                'a.kd_rek6',
                'a.kd_sub_kegiatan',
                DB::raw("ISNULL(a.no_sp2d,'') as no_sp2d"),
                DB::raw("ISNULL(a.no_bukti,'') as no_bukti"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls"),
                'b.tgl_bukti',
                'ket'
            )
            ->where('b.kd_skpd', $kd_skpd)
            ->whereIn(DB::raw("a.kd_sub_kegiatan+'.'+a.kd_rek6"), $header) //kd_rek6 dari array
            ->whereBetween('tgl_bukti', [$tanggal1, $tanggal2])
            ->groupBy('a.no_bukti', 'b.tgl_bukti', 'a.kd_sub_kegiatan', 'a.kd_rek6', 'a.no_sp2d', 'ket')
            ->orderBy('tgl_bukti')
            ->orderBy('no_bukti')
            ->distinct()->get();

        $lalu       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                'a.kd_sub_kegiatan',
                'a.kd_rek6',
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls")
            )
            ->where('b.kd_skpd', $kd_skpd)
            ->whereIn(DB::raw("a.kd_sub_kegiatan+'.'+a.kd_rek6"), $header) //kd_rek6 dari array
            ->whereRaw("b.tgl_bukti < ?", [$tanggal1])
            ->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')
            ->get();

        $sd_ini       = DB::table('trdtransout as a')
            ->join('trhtransout as b', function ($join) {
                $join->on('a.no_bukti', '=', 'b.no_bukti');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })
            ->select(
                'a.kd_sub_kegiatan',
                'a.kd_rek6',
                DB::raw("SUM(CASE WHEN jns_spp IN ('1','2') THEN a.nilai ELSE 0 END) AS up"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('3') THEN a.nilai ELSE 0 END) AS gu"),
                DB::raw("SUM(CASE WHEN jns_spp IN ('4','5','6') THEN a.nilai ELSE 0 END) AS ls")
            )
            ->where('b.kd_skpd', $kd_skpd)
            ->whereIn(DB::raw("a.kd_sub_kegiatan+'.'+a.kd_rek6"), $header) //kd_rek6 dari array
            ->whereRaw("b.tgl_bukti <= ?", [$tanggal2])
            ->groupBy('a.kd_sub_kegiatan', 'a.kd_rek6')
            ->get();

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tanggal1'          => $tanggal1,
            'tanggal2'          => $tanggal2,
            'nm_skpd'           => $nm_skpd,
            'headers'           => $headers,
            'lalu'              => $lalu,
            'sd_ini'            => $sd_ini,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.Subrincianobjek_skpd')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('Sub Rincian Objek.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="Sub Rincian Objek - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }


    // Cetak cetakSubRincianObjekPemakaian
    public function cetakSubRincianObjekPemakaian(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $tanggal1       = $request->tanggal1;
        $tanggal2       = $request->tanggal2;
        $subkegiatan    = $request->subkegiatan;
        $akunbelanja    = $request->akunbelanja;
        $jns_anggaran   = $request->jns_anggaran;
        $enter          = $request->spasi;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')
            ->select('nama', 'nip', 'jabatan', 'pangkat')
            ->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])
            ->whereIn('kode', ['BK', 'BPP'])
            ->first();
        // $cari_pakpa     = DB::table('ms_ttd')
        //     ->select('nama', 'nip', 'jabatan', 'pangkat')
        //     ->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])
        //     ->whereIn('kode', ['PA', 'KPA'])
        //     ->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah         = DB::table('sclient')
            ->select('daerah')
            ->where('kd_skpd', $kd_skpd)
            ->first();
        $nm_skpd        = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
        $nm_akunbelanja = cari_nama($akunbelanja, 'ms_rek6', 'kd_rek6', 'nm_rek6');
        $nm_subkegiatan = cari_nama($subkegiatan, 'ms_sub_kegiatan', 'kd_sub_kegiatan', 'nm_sub_kegiatan');

        $dppa        = DB::table('trdrka')
            ->select(DB::raw("SUM(nilai) AS nilai"))
            ->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $jns_anggaran, 'kd_rek6' => $akunbelanja, 'kd_sub_kegiatan' => $subkegiatan])
            ->first();


        $rincian       = DB::select("SELECT 1 [no],b.no_sp2d [no1],b.tgl_sp2d [tgl],b.keperluan [ket],(select nm_sumber_dana1 from sumber_dana
                                        where kd_sumber_dana1=a.sumber) as sumberdana,a.nilai [nilai]
                                        from trdspp a join trhsp2d b on a.no_spp=b.no_spp where b.jns_spp not  in ('1','2')
                                        and a.kd_sub_kegiatan= ? and a.kd_rek6= ? and a.kd_skpd= ? and (b.sp2d_batal is null or b.sp2d_batal<>'1')
                                        union all
                                        select 2 [no],b.no_spp [no1],b.tgl_spp [tgl],b.keperluan [ket],(select nm_sumber_dana1 from sumber_dana where kd_sumber_dana1=a.sumber),a.nilai [nilai]
                                        from trdspp a join trhspp b on a.no_spp=b.no_spp where b.jns_spp not  in ('1','2')
                                        and a.no_spp  not in(select no_spp from trhsp2d where kd_skpd= ?)
                                        and a.kd_sub_kegiatan= ? and a.kd_rek6= ? and a.kd_skpd= ? and (b.sp2d_batal is null or b.sp2d_batal<>'1')
                                        union all
                                        select  3 [no],b.no_bukti [no1],a.tgl_bukti [tgl],a.ket [ket],(select nm_sumber_dana1 from sumber_dana where kd_sumber_dana1=b.sumber),b.nilai [nilai] from trhtagih a join trdtagih b on a.kd_skpd=b.kd_skpd and a.no_bukti=b.no_bukti
                                        where b.kd_sub_kegiatan= ? and kd_rek= ? and a.kd_skpd= ? and a.no_bukti not in(select no_tagih from trhspp where kd_skpd= ?)
                                        union all
                                        select 4 [no],a.no_bukti [no1],b.tgl_bukti [tgl],b.ket,(select nm_sumber_dana1 from sumber_dana where kd_sumber_dana1=a.sumber),a.nilai from trdtransout a join trhtransout b on a.no_bukti=b.no_bukti and a.kd_skpd=b.kd_skpd
                                        where b.jns_spp in ('1','2') and a.kd_sub_kegiatan= ? and a.kd_rek6= ? and a.kd_skpd= ? order by [no],tgl,no1", [$subkegiatan, $akunbelanja, $kd_skpd, $kd_skpd, $subkegiatan, $akunbelanja, $kd_skpd, $subkegiatan, $akunbelanja, $kd_skpd, $kd_skpd, $subkegiatan, $akunbelanja, $kd_skpd]);


        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tanggal1'          => $tanggal1,
            'tanggal2'          => $tanggal2,
            'nm_skpd'           => $nm_skpd,
            'dppa'              => $dppa,
            'kd_subkegiatan'    => $subkegiatan,
            'nm_subkegiatan'    => $nm_subkegiatan,
            'kd_akunbelanja'    => $akunbelanja,
            'nm_akunbelanja'    => $nm_akunbelanja,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.Subrincianobjek_pemakaian')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('Sub Rincian Objek.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="Sub Rincian Objek - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }
}
