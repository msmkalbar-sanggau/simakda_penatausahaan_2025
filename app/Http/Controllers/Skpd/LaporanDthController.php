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

class LaporanDthController extends Controller
{


    // Cetak DTH
    public function cetakLaporanDth(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
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
        // $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $cari_pakpa = collect(DB::select("SELECT nama, nip, jabatan, pangkat from ms_ttd where LTRIM(nip) = ? and kd_skpd = ? and kode in ('PA', 'KPA')", [$pa_kpa, $kd_skpd]))->first();
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');

        $rincian = DB::select("SELECT 1 urut, c.no_spm,c.nilai,a.no_sp2d,x.nil_trans as nilai_belanja,'' no_bukti,'' kode_belanja,
            '' as kd_rek6,'' as jenis_pajak,0 as nilai_pot,'' as npwp,
            '' as nmrekan,z.banyak, ''ket,c.jns_spp, '' ntpn,''ebilling,''keperluan
            FROM trhstrpot a
            INNER JOIN trdstrpot b
            ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
            LEFT JOIN trhsp2d c
            ON left(a.kd_skpd,17)=left(c.kd_skpd,17) AND a.no_sp2d=c.no_sp2d
            LEFT JOIN
            (SELECT b.kd_skpd, a.no_sp2d, SUM(a.nilai) as nil_trans FROM trdtransout a
            INNER JOIN trhtransout b ON a.kd_skpd=b.kd_skpd AND a.no_bukti=b.no_bukti
            WHERE b.kd_skpd= ?
            GROUP BY b.kd_skpd, a.no_sp2d) x
            ON a.kd_skpd=x.kd_skpd AND a.no_sp2d=x.no_sp2d
            LEFT JOIN
            (SELECT b.kd_skpd,b.no_sp2d, COUNT(b.no_sp2d) as banyak
            FROM trdstrpot a JOIN trhstrpot b ON a.no_bukti = b.no_bukti and a.kd_skpd=b.kd_skpd
            WHERE b.kd_skpd =  ? AND month(b.tgl_bukti)= ?
            AND RTRIM(a.kd_rek6) IN ('210106010001','210105010001','210105020001','210105030001','210109010001' )
            GROUP BY b.kd_skpd,b.no_sp2d)z
            ON a.kd_skpd=z.kd_skpd and a.no_sp2d=z.no_sp2d
            WHERE a.kd_skpd =  ? AND month(a.tgl_bukti)= ?
            AND b.kd_rek6 IN ('210106010001','210105010001','210105020001','210105030001','210109010001' )
            GROUP BY c.no_spm,c.nilai,a.no_sp2d,x.nil_trans,z.banyak,c.jns_spp
            UNION ALL
            SELECT 2 as urut, '' as no_spm,0 as nilai,b.no_sp2d as no_sp2d,0 as nilai_belanja,
            a.no_bukti, kd_sub_kegiatan+'.'+a.kd_rek_trans as kode_belanja,RTRIM(a.kd_rek6),'' as jenis_pajak,a.nilai as nilai_pot,b.npwp,
            b.nmrekan,0 banyak,
            'No Set: '+a.no_bukti as ket,
            '' jns_spp, a.ntpn,a.ebilling,b.ket as keperluan
            FROM trdstrpot a JOIN trhstrpot b ON a.no_bukti = b.no_bukti and a.kd_skpd=b.kd_skpd
            WHERE b.kd_skpd =  ? AND month(b.tgl_bukti)= ?
            AND RTRIM(a.kd_rek6) IN ('210106010001','210105010001','210105020001','210105030001','210109010001' )
            ORDER BY no_sp2d,urut,no_spm,kode_belanja,kd_rek6 ", [$kd_skpd, $kd_skpd, $bulan, $kd_skpd, $bulan, $kd_skpd, $bulan]);

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'nm_skpd'           => $nm_skpd,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.dth')->with($data);

        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('DTH.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="DTH - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }
}
