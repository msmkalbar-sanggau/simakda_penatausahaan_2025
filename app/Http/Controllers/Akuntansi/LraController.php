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

class LraController extends Controller
{

    public function cetakLra(Request $request){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);
        $tanggal_ttd    = $request->tgl_ttd;
        $ttd            = $request->ttd;
        $bulan          = $request->bulan;
        $format          = $request->format;
        $enter          = $request->spasi;
        $cetak          = $request->cetak;
        $tanggal1       = $request->tanggal1;
        $tanggal2       = $request->tanggal2;
        $akumulasi      = $request->pilihakumulsai;
        $jns_ang        = $request->jenis_anggaran;
        $periodebulan   = $request->periodebulan;
        $jenis_data     = $request->jenis_data;
        $jns_rincian    = $request->jns_rincian;
        if($request->kd_skpd==''){
            $kd_skpd        = Auth::user()->kd_skpd;
            $skpd_clause="";
            $skpd_clauses= "";
        }else{
            $kd_skpd        = $request->kd_skpd;
            $skpd_clause = "AND left(a.kd_skpd,len('$kd_skpd'))='$kd_skpd'";
            $skpd_clauses= "WHERE left(kd_skpd,len('$kd_skpd'))='$kd_skpd'";
        }
        
        $tahun_anggaran = tahun_anggaran();

        // TANDA TANGAN
        if($ttd == '0'){
            $tandatangan="";
        }else{
            $tandatangan = DB::table('ms_ttd')
                            ->select('nama', 'nip', 'jabatan', 'pangkat')
                            ->where('nip', $ttd)
                            ->whereIn('kode', ['1'])
                            ->first();
        }
            if($akumulasi=='akum'){
                    $isi    = "sd_bulan_ini";
                    $pilih  = "S/D";
                    $judul  = BULAN($bulan);
                    $operator="<=";
                }else{
                    $isi = "bulan_ini";
                    $pilih = "BULAN";
                    $judul  = BULAN($bulan);
                    $operator="=";
                }

        if ($format=='sng') {
                 if($periodebulan=='periode'){
                    $rincian = DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                            -- anggaran
                                            isnull((SELECT sum(nilai) FROM trdrka 
                                                    where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0
                                                    ) AS anggaran, 
                                            --realisasi
                                            isnull((
                                                SELECT sum(realisasi) realisasi FROM(
                                                SELECT
                                                b.kd_unit,
                                                b.kd_sub_kegiatan,
                                                b.kd_rek6, 
                                                CASE
                                                    WHEN LEFT(b.kd_rek6, 1) = '4' THEN SUM(kredit) - SUM(debet)
                                                    WHEN LEFT(b.kd_rek6, 1) = '5' THEN SUM(debet) - SUM(kredit)
                                                    WHEN LEFT(b.kd_rek6, 2) = '61' THEN SUM(kredit) - SUM(debet)
                                                    WHEN LEFT(b.kd_rek6, 2) = '62' THEN SUM(debet) - SUM(kredit)
                                                    ELSE 0
                                                END AS realisasi
                                                FROM trhju_sinergi a
                                                JOIN trdju_sinergi b ON a.no_voucher = b.no_voucher 
                                                            AND a.kd_skpd = b.kd_unit
                                                WHERE b.kd_rek1_cmp IN ('4', '5', '6') 
                                                $skpd_clause AND (tgl_voucher between ? and ? ) and  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                GROUP BY a.tgl_voucher,b.no_voucher,b.kd_unit,b.kd_sub_kegiatan,b.kd_rek6)a
                                            ) ,0)realisasi 
                                            FROM map_lra_2023
                                            
                                            where group_id <= ?
                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                            ORDER BY BY map_lra_2023.id,group_id, nama", [$jns_ang,$tanggal1,$tanggal2,$jns_rincian]);
                    $sus=collect(DB::select("SELECT SUM(ang_surplus)ang_surplus,sum(nil_surplus)nil_surplus,sum(ang_neto)ang_neto,sum(nil_neto)nil_neto FROM data_jurnal_n_surnet_tgl_sinergi_oyoy(?,?,?) $skpd_clauses",[$tanggal1,$tanggal2,$jns_ang]))->first();
        
                    
                }else{
                    $rincian = DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align,  -- anggaran
                                            isnull((SELECT sum(nilai) FROM trdrka 
                                                    where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0
                                                    ) AS anggaran,
                                        --realisasi
                                            isnull((
                                                SELECT sum(realisasi) realisasi FROM(
                                                SELECT
                                                b.kd_unit,
                                                b.kd_sub_kegiatan,
                                                b.kd_rek6, 
                                                CASE
                                                    WHEN LEFT(b.kd_rek6, 1) = '4' THEN SUM(kredit) - SUM(debet)
                                                    WHEN LEFT(b.kd_rek6, 1) = '5' THEN SUM(debet) - SUM(kredit)
                                                    WHEN LEFT(b.kd_rek6, 2) = '61' THEN SUM(kredit) - SUM(debet)
                                                    WHEN LEFT(b.kd_rek6, 2) = '62' THEN SUM(debet) - SUM(kredit)
                                                    ELSE 0
                                                END AS realisasi
                                                FROM trhju_sinergi a
                                                JOIN trdju_sinergi b ON a.no_voucher = b.no_voucher 
                                                            AND a.kd_skpd = b.kd_unit
                                                WHERE b.kd_rek1_cmp IN ('4', '5', '6') 
                                                $skpd_clause  AND MONTH(tgl_voucher) $operator ? and  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                GROUP BY a.tgl_voucher,b.no_voucher,b.kd_unit,b.kd_sub_kegiatan,b.kd_rek6)a
                                            ) ,0)realisasi 

                                            FROM map_lra_2023
                                            
                                            where group_id <= ?
                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align                                       
                                            ORDER BY map_lra_2023.id,group_id, nama", [$jns_ang,$bulan,$jns_rincian]);
                    $sus=collect(DB::select("SELECT SUM(ang_surplus)ang_surplus,sum(nil_surplus)nil_surplus,sum(ang_neto)ang_neto,sum(nil_neto)nil_neto FROM data_jurnal_n_surnet_sinergi_oyoy(?,?,?) $skpd_clauses",[$bulan,$jns_ang,$tahun_anggaran]))->first();

                }
        }else{
                
            // rincian
            if($jenis_data==5){ //Jurnal
                if($periodebulan=='periode'){
                    $rincian = DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                            -- anggaran
                                            isnull((SELECT sum(nilai) FROM trdrka 
                                                    where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0
                                                    ) AS anggaran, 
                                            --realisasi
                                            isnull((
                                                SELECT sum(realisasi) realisasi FROM(
                                                SELECT
                                                b.kd_unit,
                                                b.kd_sub_kegiatan,
                                                b.kd_rek6, 
                                                CASE
                                                    WHEN LEFT(b.kd_rek6, 1) = '4' THEN SUM(kredit) - SUM(debet)
                                                    WHEN LEFT(b.kd_rek6, 1) = '5' THEN SUM(debet) - SUM(kredit)
                                                    WHEN LEFT(b.kd_rek6, 2) = '61' THEN SUM(kredit) - SUM(debet)
                                                    WHEN LEFT(b.kd_rek6, 2) = '62' THEN SUM(debet) - SUM(kredit)
                                                    ELSE 0
                                                END AS realisasi
                                                FROM trhju_pkd a
                                                JOIN trdju_pkd b ON a.no_voucher = b.no_voucher 
                                                            AND a.kd_skpd = b.kd_unit
                                                WHERE b.kd_rek1_cmp IN ('4', '5', '6') 
                                                $skpd_clause AND (tgl_voucher between ? and ? ) and  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                GROUP BY a.tgl_voucher,b.no_voucher,b.kd_unit,b.kd_sub_kegiatan,b.kd_rek6)a
                                            ) ,0)realisasi 
                                            FROM map_lra_2023
                                            
                                            where group_id <= ?
                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                            ORDER BY  map_lra_2023.id,group_id, nama", [$jns_ang,$tanggal1,$tanggal2,$jns_rincian]);
                        $sus=collect(DB::select("SELECT SUM(ang_surplus)ang_surplus,sum(nil_surplus)nil_surplus,sum(ang_neto)ang_neto,sum(nil_neto)nil_neto FROM data_jurnal_n_surnet_tgl_oyoy(?,?,?) $skpd_clauses",[$tanggal1,$tanggal2,$jns_ang]))->first();
        
                    
                }else{
                    $rincian = DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align,  -- anggaran
                                            isnull((SELECT sum(nilai) FROM trdrka 
                                                    where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0
                                                    ) AS anggaran,
                                        --realisasi
                                            isnull((
                                                SELECT sum(realisasi) realisasi FROM(
                                                SELECT
                                                b.kd_unit,
                                                b.kd_sub_kegiatan,
                                                b.kd_rek6, 
                                                CASE
                                                    WHEN LEFT(b.kd_rek6, 1) = '4' THEN SUM(kredit) - SUM(debet)
                                                    WHEN LEFT(b.kd_rek6, 1) = '5' THEN SUM(debet) - SUM(kredit)
                                                    WHEN LEFT(b.kd_rek6, 2) = '61' THEN SUM(kredit) - SUM(debet)
                                                    WHEN LEFT(b.kd_rek6, 2) = '62' THEN SUM(debet) - SUM(kredit)
                                                    ELSE 0
                                                END AS realisasi
                                                FROM trhju_pkd a
                                                JOIN trdju_pkd b ON a.no_voucher = b.no_voucher 
                                                            AND a.kd_skpd = b.kd_unit
                                                WHERE b.kd_rek1_cmp IN ('4', '5', '6') 
                                                $skpd_clause  AND MONTH(tgl_voucher) $operator ? and  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                GROUP BY a.tgl_voucher,b.no_voucher,b.kd_unit,b.kd_sub_kegiatan,b.kd_rek6)a
                                            ) ,0)realisasi 

                                            FROM map_lra_2023
                                            
                                            where group_id <= ?
                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align                                       
                                            ORDER BY map_lra_2023.id,group_id, nama", [$jns_ang,$bulan,$jns_rincian]);
                    $sus=collect(DB::select("SELECT SUM(ang_surplus)ang_surplus,sum(nil_surplus)nil_surplus,sum(ang_neto)ang_neto,sum(nil_neto)nil_neto FROM data_jurnal_n_surnet_oyoy(?,?,?) $skpd_clauses",[$bulan,$jns_ang,$tahun_anggaran]))->first();
                }
            }else if ($jenis_data==4){
                if ($periodebulan=='periode') {
                    # code...
                    $rincian=DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                        -- anggaran
                                        isnull((SELECT sum(nilai) FROM trdrka where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0) AS anggaran, 
                                        -- realisasi SPJ
                                        ( 
                                            SELECT sum(realisasi) from (
                                                SELECT
                                                SUM(a.nilai) realisasi
                                                FROM trdtransout a
                                                JOIN trhtransout b ON a.no_bukti = b.no_bukti
                                                AND b.kd_skpd = a.kd_skpd
                                                WHERE left(a.kd_rek6,1) IN ('5', '6')  $skpd_clause
                                                AND (b.tgl_kas between ? and ? ) and  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek

                                                UNION ALL
                                                -- CP
                                                SELECT
                                                CASE 
                                                    WHEN b.jns_trans=5 and b.jns_cp in (1) and b.pot_khusus<>0 THEN sum(a.rupiah)*-1
                                                    WHEN b.jns_trans=5 and b.jns_cp in (2)THEN sum(a.rupiah)*-1
                                                ELSE 0
                                                END as realisasi
                                                from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
                                                WHERE (b.tgl_sts between ? and ? ) and left(a.kd_rek6,1)='5'  $skpd_clause
                                                AND  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by b.jns_trans,b.jns_cp,b.pot_khusus
                                                UNION ALL
                                                -- PENDAPATAN													
                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  and b.kd_rek6='410411010001' and b.kd_skpd='5.02.0.00.0.00.02.0000'
                                                $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  $skpd_clause
                                                AND  LEFT(b.kd_rek5, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                            )zzzz
                                                ) AS realisasi
                                                            FROM map_lra_2023
                                                            where group_id <= ?
                                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                                            ORDER BY id,group_id, nama",[$jns_ang,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$jns_rincian]);
                        $sus=collect(DB::select("SELECT * FROM data_jurnal_n_sal_awal_spj_tgl(?,?,?)",[$tanggal1,$tanggal2,$jns_ang]))->first();

                } else {
                    # code...
                    $rincian=DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                        -- anggaran
                                        isnull((SELECT sum(nilai) FROM trdrka where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0) AS anggaran, 
                                        -- realisasi SPJ
                                        ( 
                                            SELECT sum(realisasi) from (
                                                SELECT
                                                SUM(a.nilai) realisasi
                                                FROM trdtransout a
                                                JOIN trhtransout b ON a.no_bukti = b.no_bukti
                                                AND b.kd_skpd = a.kd_skpd
                                                WHERE left(a.kd_rek6,1) IN ('5', '6')  $skpd_clause
                                                AND MONTH(b.tgl_bukti) $operator ? and  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek

                                                UNION ALL
                                                -- CP
                                                SELECT
                                                CASE 
                                                    WHEN b.jns_trans=5 and b.jns_cp in (1) and b.pot_khusus<>0 THEN sum(a.rupiah)*-1
                                                    WHEN b.jns_trans=5 and b.jns_cp in (2)THEN sum(a.rupiah)*-1
                                                ELSE 0
                                                END as realisasi
                                                from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
                                                WHERE MONTH(b.tgl_sts) $operator ? and left(a.kd_rek6,1)='5'  $skpd_clause
                                                AND  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by b.jns_trans,b.jns_cp,b.pot_khusus
                                                UNION ALL
                                                -- PENDAPATAN													
                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? and b.kd_rek6='410411010001' and b.kd_skpd='5.02.0.00.0.00.02.0000'
                                                $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? $skpd_clause
                                                AND  LEFT(b.kd_rek5, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                            )zzzz
                                                ) AS realisasi
                                                            FROM map_lra_2023
                                                            where group_id <= ?
                                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                                            ORDER BY id,group_id, nama",[$jns_ang,$bulan,$bulan,$bulan,$bulan,$bulan,$jns_rincian]);
                        $sus=collect(DB::select("SELECT * FROM data_jurnal_n_sal_awal_spj(?,?,?)",[$bulan,$jns_ang,$tahun_anggaran]))->first();

                }
                
            }else if ($jenis_data==3){ // SP2D LUNAS
                if ($periodebulan=='periode') {
                    # code...
                    $rincian=DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                        -- anggaran
                                        isnull((SELECT sum(nilai) FROM trdrka where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0) AS anggaran, 
                                        -- realisasi SPJ
                                        ( 
                                            SELECT sum(realisasi) from (
                                                SELECT
                                                SUM(a.nilai) realisasi
                                                FROM trdspp a
                                                INNER JOIN trhsp2d b ON a.no_spp = b.no_spp
                                                AND b.kd_skpd = a.kd_skpd
                                                WHERE left(a.kd_rek6,1) IN ('5', '6')  $skpd_clause
                                                and status_bud = 1
                                                and (sp2d_batal is null OR sp2d_batal <> 1)
                                                AND (b.tgl_sp2d between ? and ? ) and  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek

                                                UNION ALL
                                                -- CP
                                                SELECT
                                                CASE 
                                                    WHEN b.jns_trans=5 and b.jns_cp in (1) and b.pot_khusus<>0 THEN sum(a.rupiah)*-1
                                                    WHEN b.jns_trans=5 and b.jns_cp in (2)THEN sum(a.rupiah)*-1
                                                ELSE 0
                                                END as realisasi
                                                from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
                                                WHERE (b.tgl_sts between ? and ? ) and left(a.kd_rek6,1)='5'  $skpd_clause
                                                AND  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by b.jns_trans,b.jns_cp,b.pot_khusus
                                                UNION ALL
                                                -- PENDAPATAN													
                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  and b.kd_rek6='410411010001' and b.kd_skpd='5.02.0.00.0.00.02.0000'
                                                $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  $skpd_clause
                                                AND  LEFT(b.kd_rek5, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                            )zzzz
                                                ) AS realisasi
                                                            FROM map_lra_2023
                                                            where group_id <= ?
                                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                                            ORDER BY id,group_id, nama",[$jns_ang,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$jns_rincian]);
                            $sus=collect(DB::select("SELECT * FROM data_jurnal_n_sal_awal_spj_tgl(?,?,?)",[$tanggal1,$tanggal2,$jns_ang]))->first();

                } else {
                    # code...
                    $rincian=DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                        -- anggaran
                                        isnull((SELECT sum(nilai) FROM trdrka where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0) AS anggaran, 
                                        -- realisasi SPJ
                                        ( 
                                            SELECT sum(realisasi) from (
                                                SELECT
                                                SUM(a.nilai) realisasi
                                                FROM trdspp a
                                                INNER JOIN trhsp2d b ON a.no_spp = b.no_spp
                                                AND b.kd_skpd = a.kd_skpd
                                                WHERE left(a.kd_rek6,1) IN ('5', '6')  $skpd_clause
                                                and status_bud=1
                                                and (sp2d_batal is null OR sp2d_batal <> 1)
                                                AND MONTH(b.tgl_sp2d) $operator ? and  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek

                                                UNION ALL
                                                -- CP
                                                SELECT
                                                CASE 
                                                    WHEN b.jns_trans=5 and b.jns_cp in (1) and b.pot_khusus<>0 THEN sum(a.rupiah)*-1
                                                    WHEN b.jns_trans=5 and b.jns_cp in (2)THEN sum(a.rupiah)*-1
                                                ELSE 0
                                                END as realisasi
                                                from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
                                                WHERE MONTH(b.tgl_sts) $operator ? and left(a.kd_rek6,1)='5'  $skpd_clause
                                                AND  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by b.jns_trans,b.jns_cp,b.pot_khusus
                                                UNION ALL
                                                -- PENDAPATAN													
                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? and b.kd_rek6='410411010001' and b.kd_skpd='5.02.0.00.0.00.02.0000'
                                                $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? $skpd_clause
                                                AND  LEFT(b.kd_rek5, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                            )zzzz
                                                ) AS realisasi
                                                            FROM map_lra_2023
                                                            where group_id <= ?
                                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                                            ORDER BY id,group_id, nama",[$jns_ang,$bulan,$bulan,$bulan,$bulan,$bulan,$jns_rincian]);

                    $sus=collect(DB::select("SELECT * FROM data_jurnal_n_sal_awal_spj(?,?,?)",[$bulan,$jns_ang,$tahun_anggaran]))->first();
                                

                }
                
            }else if ($jenis_data==2){ // SP2D Advis
                if ($periodebulan=='periode') {
                    # code...
                    $rincian=DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                        -- anggaran
                                        isnull((SELECT sum(nilai) FROM trdrka where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0) AS anggaran, 
                                        -- realisasi SPJ
                                        ( 
                                            SELECT sum(realisasi) from (
                                                SELECT
                                                SUM(a.nilai) realisasi
                                                FROM trdspp a
                                                INNER JOIN trhsp2d b ON a.no_spp = b.no_spp
                                                AND b.kd_skpd = a.kd_skpd
                                                INNER JOIN trduji c on b.no_sp2d=c.no_sp2d
                                                WHERE left(a.kd_rek6,1) IN ('5', '6')  $skpd_clause
                                                and (sp2d_batal is null OR sp2d_batal <> 1)
                                                AND (b.tgl_sp2d between ? and ? ) and  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek

                                                UNION ALL
                                                -- CP
                                                SELECT
                                                CASE 
                                                    WHEN b.jns_trans=5 and b.jns_cp in (1) and b.pot_khusus<>0 THEN sum(a.rupiah)*-1
                                                    WHEN b.jns_trans=5 and b.jns_cp in (2)THEN sum(a.rupiah)*-1
                                                ELSE 0
                                                END as realisasi
                                                from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
                                                WHERE (b.tgl_sts between ? and ? ) and left(a.kd_rek6,1)='5'  $skpd_clause
                                                AND  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by b.jns_trans,b.jns_cp,b.pot_khusus
                                                UNION ALL
                                                -- PENDAPATAN													
                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  and b.kd_rek6='410411010001' and b.kd_skpd='5.02.0.00.0.00.02.0000'
                                                $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  $skpd_clause
                                                AND  LEFT(b.kd_rek5, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                            )zzzz
                                                ) AS realisasi
                                                            FROM map_lra_2023
                                                            where group_id <= ?
                                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                                            ORDER BY id,group_id, nama",[$jns_ang,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$jns_rincian]);
                        $sus=collect(DB::select("SELECT * FROM data_jurnal_n_sal_awal_spj_tgl(?,?,?)",[$tanggal1,$tanggal2,$jns_ang]))->first();

                } else {
                    # code...
                    $rincian=DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                        -- anggaran
                                        isnull((SELECT sum(nilai) FROM trdrka where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0) AS anggaran, 
                                        -- realisasi SPJ
                                        ( 
                                            SELECT sum(realisasi) from (
                                                SELECT
                                                SUM(a.nilai) realisasi
                                                FROM trdspp a
                                                INNER JOIN trhsp2d b ON a.no_spp = b.no_spp
                                                AND b.kd_skpd = a.kd_skpd
                                                INNER JOIN trduji c on b.no_sp2d=c.no_sp2d
                                                WHERE left(a.kd_rek6,1) IN ('5', '6')  $skpd_clause
                                                and (sp2d_batal is null OR sp2d_batal <> 1)
                                                AND MONTH(b.tgl_sp2d) $operator ? and  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek

                                                UNION ALL
                                                -- CP
                                                SELECT
                                                CASE 
                                                    WHEN b.jns_trans=5 and b.jns_cp in (1) and b.pot_khusus<>0 THEN sum(a.rupiah)*-1
                                                    WHEN b.jns_trans=5 and b.jns_cp in (2)THEN sum(a.rupiah)*-1
                                                ELSE 0
                                                END as realisasi
                                                from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
                                                WHERE MONTH(b.tgl_sts) $operator ? and left(a.kd_rek6,1)='5'  $skpd_clause
                                                AND  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by b.jns_trans,b.jns_cp,b.pot_khusus
                                                UNION ALL
                                                -- PENDAPATAN													
                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? and b.kd_rek6='410411010001' and b.kd_skpd='5.02.0.00.0.00.02.0000'
                                                $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? $skpd_clause
                                                AND  LEFT(b.kd_rek5, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                            )zzzz
                                                ) AS realisasi
                                                            FROM map_lra_2023
                                                            where group_id <= ?
                                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                                            ORDER BY id,group_id, nama",[$jns_ang,$bulan,$bulan,$bulan,$bulan,$bulan,$jns_rincian]);
                        $sus=collect(DB::select("SELECT * FROM data_jurnal_n_sal_awal_spj(?,?,?)",[$bulan,$jns_ang,$tahun_anggaran]))->first();
            
                }
                
            }else if ($jenis_data==1){ // SP2D terbit
                if ($periodebulan=='periode') {
                    # code...
                    $rincian=DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                        -- anggaran
                                        isnull((SELECT sum(nilai) FROM trdrka where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0) AS anggaran, 
                                        -- realisasi SPJ
                                        ( 
                                            SELECT sum(realisasi) from (
                                                SELECT
                                                SUM(a.nilai) realisasi
                                                FROM trdspp a
                                                INNER JOIN trhsp2d b ON a.no_spp = b.no_spp
                                                AND b.kd_skpd = a.kd_skpd
                                                WHERE left(a.kd_rek6,1) IN ('5', '6')  $skpd_clause
                                                and (sp2d_batal is null OR sp2d_batal <> 1)
                                                AND (b.tgl_sp2d between ? and ? ) and  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek

                                                UNION ALL
                                                -- CP
                                                SELECT
                                                CASE 
                                                    WHEN b.jns_trans=5 and b.jns_cp in (1) and b.pot_khusus<>0 THEN sum(a.rupiah)*-1
                                                    WHEN b.jns_trans=5 and b.jns_cp in (2)THEN sum(a.rupiah)*-1
                                                ELSE 0
                                                END as realisasi
                                                from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
                                                WHERE (b.tgl_sts between ? and ? ) and left(a.kd_rek6,1)='5'  $skpd_clause
                                                AND  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by b.jns_trans,b.jns_cp,b.pot_khusus
                                                UNION ALL
                                                -- PENDAPATAN													
                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  and b.kd_rek6='410411010001' and b.kd_skpd='5.02.0.00.0.00.02.0000'
                                                $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE (a.tgl_sts between ? and ? )  $skpd_clause
                                                AND  LEFT(b.kd_rek5, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                            )zzzz
                                                ) AS realisasi
                                                            FROM map_lra_2023
                                                            where group_id <= ?
                                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                                            ORDER BY id,group_id, nama",[$jns_ang,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal2,$jns_rincian]);
                        $sus=collect(DB::select("SELECT * FROM data_jurnal_n_sal_awal_spj_tgl(?,?,?)",[$tanggal1,$tanggal2,$jns_ang]))->first();

                } else {
                    # code...
                    $rincian=DB::select("SELECT map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align, 
                                        -- anggaran
                                        isnull((SELECT sum(nilai) FROM trdrka where jns_ang= ? and LEFT(trdrka.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek),0) AS anggaran, 
                                        -- realisasi SPJ
                                        ( 
                                            SELECT sum(realisasi) from (
                                                SELECT
                                                SUM(a.nilai) realisasi
                                                FROM trdspp a
                                                INNER JOIN trhsp2d b ON a.no_spp = b.no_spp
                                                AND b.kd_skpd = a.kd_skpd
                                                WHERE left(a.kd_rek6,1) IN ('5', '6')  $skpd_clause
                                                and (sp2d_batal is null OR sp2d_batal <> 1)
                                                AND MONTH(b.tgl_sp2d) $operator ? and  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek

                                                UNION ALL
                                                -- CP
                                                SELECT
                                                CASE 
                                                    WHEN b.jns_trans=5 and b.jns_cp in (1) and b.pot_khusus<>0 THEN sum(a.rupiah)*-1
                                                    WHEN b.jns_trans=5 and b.jns_cp in (2)THEN sum(a.rupiah)*-1
                                                ELSE 0
                                                END as realisasi
                                                from trdkasin_pkd a join trhkasin_pkd b on a.no_sts=b.no_sts and a.kd_skpd=b.kd_skpd
                                                WHERE MONTH(b.tgl_sts) $operator ? and left(a.kd_rek6,1)='5'  $skpd_clause
                                                AND  LEFT(a.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by b.jns_trans,b.jns_cp,b.pot_khusus
                                                UNION ALL
                                                -- PENDAPATAN													
                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) 
                                                FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? and b.kd_rek6='410411010001' and b.kd_skpd='5.02.0.00.0.00.02.0000'
                                                $skpd_clause
                                                AND  LEFT(b.kd_rek6, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                                UNION ALL

                                                SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                                                ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                                                WHERE month(a.tgl_sts) $operator ? $skpd_clause
                                                AND  LEFT(b.kd_rek5, LEN(map_lra_2023.kd_rek)) = map_lra_2023.kd_rek
                                                group by a.jns_trans
                                            )zzzz
                                                ) AS realisasi
                                                            FROM map_lra_2023
                                                            where group_id <= ?
                                                            GROUP BY map_lra_2023.id,group_id, kd_rek, nama, padding, is_bold, is_show_kd_rek, is_right_align
                                                            ORDER BY id,group_id, nama",[$jns_ang,$bulan,$bulan,$bulan,$bulan,$bulan,$jns_rincian]);
                    $sus=collect(DB::select("SELECT * FROM data_jurnal_n_sal_awal_spj(?,?,?)",[$bulan,$jns_ang,$tahun_anggaran]))->first();

                }
                
            }
        
        }
        
        


        $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
            // dd($sus);
        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'rincian'           => $rincian,
            'enter'             => $enter,
            'daerah'            => $daerah,
            'tanggal_ttd'       => $tanggal_ttd,
            'tandatangan'       => $tandatangan,
            'judul'             => $bulan,
            'pilih'             => $pilih,
            'jenis_ttd'         => $ttd,
            'jenis'             => $jns_rincian,
            'sus'               => $sus            
        ];
        if($format=='sap'){
            $view =  view('akuntansi.cetakan.lra_semester')->with($data);
        }elseif($format=='djpk'){
            $view =  view('akuntansi.cetakan.lra_djpk')->with($data);
        }elseif($format=='p77'){
            $view =  view('akuntansi.cetakan.lra_77')->with($data);
        }elseif($format=='sng'){
            $view =  view('akuntansi.cetakan.lra_sinergi')->with($data);
        }
        
        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('LRA 77.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="LRA 77.xls"');
            return $view;
        }
    }
}
