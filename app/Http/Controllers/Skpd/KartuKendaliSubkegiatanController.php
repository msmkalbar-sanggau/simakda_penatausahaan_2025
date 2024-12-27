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

class KartuKendaliSubkegiatanController extends Controller
{


    // Cetak cetakKkSpj
    public function cetakKkSpj(Request $request)
    {
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $bulan          = $request->bulan;
        $subkegiatan    = $request->subkegiatan;
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
        $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');

        $rincian = DB::select("exec cetak_kartu_kendali ?,?,?,?", array($kd_skpd, $bulan, $subkegiatan, $jns_anggaran));

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'nm_skpd'           => $nm_skpd,
            'subkegiatan'       => $subkegiatan,
            'bulan'             => $bulan,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.kartukendali')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('KARTU KENDALI SUB KEGIATAN.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="KARTU KENDALI SUB KEGIATAN - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }

    // Pengajuan
    // Cetak cetakKkpengajuan
    public function cetakKkpengajuan(Request $request)
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
        $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();
        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
        $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');

        $rincian = DB::select("SELECT
                                a.tgl_sts,
                                b.no_sts,
                                a.no_sp2d,
                                CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '1' AND pot_khusus = '1' THEN (select nm_rek6 from ms_rek6 where kd_rek6=b.kd_rek6) ELSE keterangan END AS keterangan,
                                CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '1' AND pot_khusus = '1' THEN b.rupiah	ELSE 0 END AS hkpg,
                                CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '1' AND pot_khusus = '2' THEN b.rupiah	ELSE 0 END AS pot_lain,
                                CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '1' AND pot_khusus NOT IN ('1','2') THEN	b.rupiah ELSE 0 END AS cp,
                                CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '2' AND LEFT(b.kd_rek6,4) IN ('5101') THEN	b.rupiah ELSE 0	END AS ls_peg,
                                CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '2' AND LEFT(b.kd_rek6,4) = '5102' THEN	b.rupiah ELSE 0	END AS ls_brng,
                                CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '2' AND LEFT(b.kd_rek6,4) in ('5201','5202','5203','5204','5205','5206') THEN	b.rupiah ELSE 0	END AS ls_modal,
                                CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '2' AND LEFT(b.kd_rek6,4) in ('5105') THEN	b.rupiah ELSE 0	END AS ls_phl,
                                CASE	WHEN jns_trans = '1'	AND jns_cp = '3' AND LEFT(b.kd_rek6,4) = '1101' THEN	b.rupiah ELSE 0	END AS gu,
                                CASE	WHEN jns_trans = '1'	AND jns_cp = '3' AND LEFT(b.kd_rek6,4) IN ('5101') THEN b.rupiah	ELSE 0 END AS up_gu_peg,
                                CASE	WHEN jns_trans = '1'	AND jns_cp = '3' AND LEFT(b.kd_rek6,4) IN ('5102') THEN	b.rupiah	ELSE 0 END AS up_gu_brng,
                                CASE	WHEN jns_trans = '1'	AND jns_cp = '3' AND LEFT(b.kd_rek6,4) in ('5201','5202','5203','5204','5205','5206') THEN	b.rupiah	ELSE 0 END AS up_gu_modal,
                                b.rupiah AS total
                            FROM
                                trhkasin_pkd a
                            INNER JOIN trdkasin_pkd b ON a.no_sts = b.no_sts
                            AND a.kd_skpd = b.kd_skpd
                            WHERE
                            a.kd_skpd =  ?
                            AND MONTH(a.tgl_sts)= ? AND pot_khusus !='3'
                            AND jns_trans IN ('1', '5') AND LEFT(b.kd_rek6,1)<>4
                            GROUP BY
                                a.tgl_sts,
                                b.no_sts,
                                a.no_sp2d,
                                keterangan,b.rupiah,jns_trans,jns_cp,pot_khusus,kd_rek6", [$kd_skpd, $bulan]);

        $lalu = DB::select("SELECT
                                SUM (	CASE	WHEN jns_trans = '5'	AND jns_cp = '1' AND pot_khusus = '1' THEN b.rupiah	ELSE 0 END) AS hkpg_l,
                                SUM (	CASE	WHEN jns_trans = '5'	AND jns_cp = '1' AND pot_khusus = '2' THEN b.rupiah	ELSE 0 END) AS pot_lain_l,
                                SUM (	CASE	WHEN jns_trans = '1'	AND jns_cp = '1' THEN	b.rupiah ELSE 0 END) AS cp_l,
                                SUM (	CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '2' AND LEFT(b.kd_rek6,4) IN ('5101','5201') THEN	b.rupiah ELSE 0	END) AS ls_peg_l,
                                SUM (	CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '2' AND LEFT(b.kd_rek6,4) = '5102' THEN	b.rupiah ELSE 0	END) AS ls_brng_l,
                                SUM (	CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '2' AND LEFT(b.kd_rek6,4) in  ('5201','5202','5203','5204','5205','5206') THEN	b.rupiah ELSE 0	END) AS ls_modal_l,
                                SUM (	CASE	WHEN jns_trans IN ('1','5')	AND jns_cp = '2' AND LEFT(b.kd_rek6,4) in  ('5105') THEN	b.rupiah ELSE 0	END) AS ls_phl_l,
                                SUM (	CASE	WHEN jns_trans = '1'	AND jns_cp = '3' AND LEFT(b.kd_rek6,4) = '1101' THEN	b.rupiah ELSE 0	END) AS gu_l,
                                SUM (	CASE	WHEN jns_trans = '1'	AND jns_cp = '3' AND LEFT(b.kd_rek6,4) IN ('5101') THEN b.rupiah	ELSE 0 END) AS up_gu_peg_l,
                                SUM (	CASE	WHEN jns_trans = '1'	AND jns_cp = '3' AND LEFT(b.kd_rek6,4) IN ('5102') THEN	b.rupiah	ELSE 0 END) AS up_gu_brng_l,
                                SUM (	CASE	WHEN jns_trans = '1'	AND jns_cp = '3' AND LEFT(b.kd_rek6,4) in ('5201','5202','5203','5204','5205','5206') THEN	b.rupiah	ELSE 0 END) AS up_gu_modal_l,
                                SUM (b.rupiah) AS total_l
                            FROM
                                trhkasin_pkd a
                            INNER JOIN trdkasin_pkd b ON a.no_sts = b.no_sts
                            AND a.kd_skpd = b.kd_skpd
                            WHERE
                                a.kd_skpd =  ?
                            AND MONTH(a.tgl_sts)< ? AND pot_khusus !='3'
                            AND jns_trans IN ('1', '5') AND LEFT(b.kd_rek6,1)<>4", [$kd_skpd, $bulan]);

        // KIRIM KE VIEW
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'bulan'             => $bulan,
            'nm_skpd'           => $nm_skpd,
            'lalu'              => $lalu,
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'cari_pa_kpa'       => $cari_pakpa,
            'cari_bendahara'    => $cari_bendahara
        ];

        $view =  view('skpd.laporan_bendahara.cetak.registercp')->with($data);
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('KARTU KENDALI SUB KEGIATAN.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="KARTU KENDALI SUB KEGIATAN - ' . $nm_skpd . '.xls"');
            return $view;
        }
    }
}
