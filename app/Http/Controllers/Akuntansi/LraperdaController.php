<?php

namespace App\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Static_;
use PDF;
use Knp\Snappy\Pdf as SnappyPdf;

class LraperdaController extends Controller
{

    public function cetak_i4_urusan(Request $request){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);
        $tanggal_ttd    = $request->tgl_ttd;
        $bulan          = $request->bulan;
        $cetak          = $request->cetak;
        $jns_ang        = $request->jenis_anggaran;
        $jenis          = $request->jenis;
        // $kd_skpd        = Auth::user()->kd_skpd;

        
        $tahun_anggaran = tahun_anggaran();



            // rincian
        if ($jenis=="1") {
            
            $rincian = DB::select("SELECT kd_skpd,kd_sub_kegiatan kode ,nm_rek,ang_peg,ang_brng,ang_mod,ang_hibah,ang_bansos,ang_bghasil,ang_bankeu,ang_btt,real_peg,real_brng,real_mod,real_hibah,real_bansos,real_bghasil,real_bankeu,real_btt 
            FROM [perda_lampI.3_rinci2]($bulan,'$jns_ang',$tahun_anggaran)
                    where len(kd_skpd)='22'
                    ORDER BY kd_skpd,kd_sub_kegiatan"
                );
        }else if($jenis=="2"){
            $rincian = DB::select("SELECT kd_skpd kode,nm_skpd nm_rek,ang_peg,ang_brng,ang_modal ang_mod,ang_hibah,ang_bansos,ang_bghasil,
            ang_bankeu,ang_btt,
          real_peg,real_brng,real_modal real_mod,real_hibah,real_bansos,real_bghasil,real_bankeu,real_btt 
          FROM [perda_lampI.3_skpd_spj]($bulan,'$jns_ang',$tahun_anggaran)
          where len(kd_skpd)='22'
          ORDER BY kd_skpd"
                );
        }else if($jenis=="3"){
            $rincian = DB::select("SELECT kd_skpd,kd_sub_kegiatan kode ,nm_rek,ang_peg,ang_brng,ang_mod,ang_hibah,ang_bansos,ang_bghasil,
                        ang_bankeu,ang_btt,
                    real_peg,real_brng,real_mod,real_hibah,real_bansos,real_bghasil,real_bankeu,real_btt 
                    FROM [perda_lampI.3_rinci2]($bulan,'$jns_ang',$tahun_anggaran)
                    where len(kd_skpd)<='22'
                    ORDER BY kd_skpd,kd_sub_kegiatan"
                );
        }else if($jenis=="4"){
            $rincian = DB::select("SELECT kd_skpd,kd_sub_kegiatan kode ,nm_rek,ang_peg,ang_brng,ang_mod,ang_hibah,ang_bansos,ang_bghasil,
                        ang_bankeu,ang_btt,
                    real_peg,real_brng,real_mod,real_hibah,real_bansos,real_bghasil,real_bankeu,real_btt 
                    FROM [perda_lampI.3_rinci2]($bulan,'$jns_ang',$tahun_anggaran) ORDER BY kd_skpd,kd_sub_kegiatan"
                );
        }else if($jenis=="5"){
            $rincian = DB::select("SELECT kd_skpd,kd_sub_kegiatan kode ,nm_rek,ang_peg,ang_brng,ang_mod,ang_hibah,ang_bansos,ang_bghasil,
                        ang_bankeu,ang_btt,
                    real_peg,real_brng,real_mod,real_hibah,real_bansos,real_bghasil,real_bankeu,real_btt 
                    FROM [perda_lampI.3_rinci2]($bulan,'$jns_ang',$tahun_anggaran)
                    where len(kd_skpd)='17'
                    ORDER BY kd_skpd,kd_sub_kegiatan"
                );
        }
        
        $sc = collect(DB::select("SELECT tgl_rka,provinsi,kab_kota,daerah,thn_ang FROM sclient"))->first();

        $nogub = collect(DB::select("SELECT ket_perda, ket_perda_no, ket_perda_tentang FROM config_nogub_akt"))->first();


 
        


        // $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
            
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'rincian'           => $rincian,
            'daerah'                => $sc,
            'nogub'             => $nogub,
            'tanggal_ttd'       => $tanggal_ttd,
            'judul'             => $bulan,
            'tahun_anggaran'    => $tahun_anggaran
        ];
        // if($format=='sap'){
        //     $view =  view('akuntansi.cetakan.lra_semester')->with($data);
        // }elseif($format=='djpk'){
        //     $view =  view('akuntansi.cetakan.lra_djpk')->with($data);
        // }elseif($format=='p77'){
        //     $view =  view('akuntansi.cetakan.lra_77')->with($data);
        // }elseif($format=='sng'){
            $view =  view('akuntansi.cetakan.perda.i_4_urusan.perda_i4_urusan')->with($data);
        // }
        
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('PERDA LAMP I4 URUSAN.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="PERDA LAMP I4 URUSAN.xls"');
            return $view;
        }
    }
    
}
