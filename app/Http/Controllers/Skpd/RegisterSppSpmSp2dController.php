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

class RegisterSppSpmSp2dController extends Controller
{


    // Cetak Register SPP/SPM/SP2D
    public function cetakRegisterSppSpmSp2d(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $jenis_reg      = $request->jenis_reg;
        $kd_skpd        = $request->kd_skpd;
        $cetak          = $request->cetak;
        $tahun_anggaran = tahun_anggaran();

        // TANDA TANGAN
        $cari_bendahara = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $bendahara, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['BK', 'BPP'])->first();
        // $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');

        if ($jenis_reg == 'SPP') {
            $rincian = DB::select("SELECT a.tgl_spp as tanggal,a.no_spp as nomor,a.keperluan,a.jns_spp,SUM(b.nilai) nilai
                        FROM trhspp a
                        INNER JOIN trdspp b ON a.kd_skpd=b.kd_skpd AND a.no_spp=b.no_spp
                        WHERE (a.sp2d_batal=0 OR a.sp2d_batal is NULL) and  month(a.tgl_spp)<= ?
                        and a.kd_skpd= ?
                        GROUP BY a.tgl_spp,a.no_spp,a.keperluan,a.jns_spp
                        ORDER BY a.tgl_spp,a.no_spp", [$bulan, $kd_skpd]);
        } else if ($jenis_reg == 'SPM') {
            $rincian = DB::select("SELECT a.tgl_spm as tanggal,a.no_spm as nomor,a.keperluan,a.jns_spp,SUM(c.nilai) nilai
                                    FROM trhspm a
                                    INNER JOIN trhspp b ON a.no_spp=b.no_spp AND a.kd_skpd=b.kd_skpd
                                    INNER JOIN trdspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                                    WHERE (b.sp2d_batal=0 OR b.sp2d_batal is NULL) and month(a.tgl_spm)<= ?
                                    and a.kd_skpd= ?
                                    GROUP BY a.tgl_spm,a.no_spm,a.keperluan,a.jns_spp
                                    ORDER BY a.tgl_spm,a.no_spm", [$bulan, $kd_skpd]);
        } else {
            $rincian = DB::select("SELECT a.tgl_sp2d as tanggal,a.no_sp2d as nomor,a.no_spm,a.no_spp,a.keperluan,a.jns_spp,SUM(d.nilai) nilai
                                        FROM trhsp2d a
                                        INNER JOIN trhspm b ON a.no_spm=b.no_spm AND a.kd_skpd=b.kd_skpd
                                        INNER JOIN trhspp c ON b.no_spp=c.no_spp AND b.kd_skpd=c.kd_skpd
                                        INNER JOIN trdspp d ON c.no_spp=d.no_spp AND c.kd_skpd=d.kd_skpd
                                        WHERE (c.sp2d_batal=0 OR c.sp2d_batal is NULL) and month(a.tgl_sp2d)<= ?
                                        and a.kd_skpd= ?
                                        GROUP BY a.tgl_sp2d,a.no_sp2d,a.no_spm,a.no_spp,a.keperluan,a.jns_spp
                                        ORDER BY a.tgl_sp2d,a.no_sp2d", [$bulan, $kd_skpd]);
        }



        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'nm_skpd'           => $nm_skpd,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'jenis_reg'         => $jenis_reg,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];
        if ($jenis_reg == 'SP2D') {
            $view =  view('skpd.laporan_bendahara.cetak.registersp2d')->with($data);
            if ($cetak == '1') {
                return $view;
            } else if ($cetak == '2') {
                $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
                return $pdf->stream('REGISTER SP2D.pdf');
            } else {

                header("Cache-Control: no-cache, no-store, must_revalidate");
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachement; filename="REGISTER SP2D - ' . $nm_skpd . '.xls"');
                return $view;
            }
        } else {
            $view2 =  view('skpd.laporan_bendahara.cetak.registersppspm')->with($data);

            if ($cetak == '1') {
                return $view2;
            } else if ($cetak == '2') {
                $pdf = PDF::loadHtml($view2)->setOrientation('landscape')->setPaper('a4');
                return $pdf->stream('REGISTER ' . $jenis_reg . '.pdf');
            } else {

                header("Cache-Control: no-cache, no-store, must_revalidate");
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachement; filename="REGISTER ' . $jenis_reg . ' - ' . $nm_skpd . '.xls"');
                return $view2;
            }
        }
    }
}
