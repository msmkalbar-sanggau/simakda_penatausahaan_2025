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

class SpjPendapatanController extends Controller
{


     // Cetak List
     public function cetakSpjPendapatan(Request $request)
     {
          $tanggal_ttd    = $request->tgl_ttd;
          $pa_kpa         = $request->pa_kpa;
          $bendahara      = $request->bendahara;
          $tanggal1       = $request->tanggal1;
          $tanggal2       = $request->tanggal2;
          $enter          = $request->spasi;
          $jns_ang        = $request->jns_anggaran;
          $spasi          = $request->spasi;

          $cetak          = $request->cetak;
          $jenis_cetakan  = $request->jenis_cetakan;
          $tahun_anggaran = tahun_anggaran();

          if ($jenis_cetakan == 'skpd') {
               $kd_skpd        = $request->kd_skpd;
               $kd_org         = $request->kd_skpd;
          } else {
               $kd_org        =  substr($request->kd_skpd, 0, 17);
               $kd_skpd        =  $request->kd_skpd;
          }

          // TANDA TANGAN
          $cari_bendahara = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $bendahara, 'kode' => 'BP', 'kd_skpd' => $kd_skpd])->first();
          $cari_pakpa = DB::table('ms_ttd')->select('nama', 'nip', 'jabatan', 'pangkat')->where(['nip' => $pa_kpa, 'kd_skpd' => $kd_skpd])->whereIn('kode', ['PA', 'KPA'])->first();



          // rincian
          if ($kd_skpd == '5.02.0.00.0.00.02.0000') {     //ada kondisi BKAD
               $rincian = DB::select("SELECT a.kd_skpd, a.kd_sub_kegiatan, b.kd_rek2 kode, b.nm_rek2 nama, a.ang,
                a.anggaran, isnull(terima_ini,0) as terima_ini,
                isnull(terima_lalu,0) terima_lalu, isnull(keluar_ini,0) keluar_ini,
                isnull(keluar_lalu,0) keluar_lalu
         from (

            SELECT left(kd_skpd,len(?)) as kd_skpd,kd_sub_kegiatan,kd_rek,sum(ang)as ang,sum(anggaran)as anggaran,
            sum(terima_ini)as terima_ini,sum(terima_lalu)as terima_lalu,sum(keluar_ini)as keluar_ini,sum(keluar_lalu)as keluar_lalu from(

            SELECT z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,2) kd_rek, SUM(z.nilai) AS ang,
            SUM(z.nilai) AS anggaran,
            ((SELECT isnull(SUM(a.nilai),0) nilai
            FROM tr_terima a WHERE left(a.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd
            and (a.tgl_terima >= ? AND a.tgl_terima <= ?))+
            (SELECT isnull(SUM(a.nilai),0) nilai
            FROM tr_terima_blud a WHERE left(a.kd_rek5,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd
            and (a.tgl_terima >= ? AND a.tgl_terima <= ?))
            -
            (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
            and (a.tgl_sts >= ? AND a.tgl_sts <= ?)
            )
            ) AS terima_ini,

            ((SELECT isnull(SUM(a.nilai),0) FROM tr_terima a WHERE left(a.kd_rek6,2)=left(z.kd_rek6,2)
            and a.kd_skpd=z.kd_skpd and (a.tgl_terima < ?))+(SELECT isnull(SUM(a.nilai),0) FROM tr_terima_blud a WHERE left(a.kd_rek5,2)=left(z.kd_rek6,2)
            and a.kd_skpd=z.kd_skpd and (a.tgl_terima < ?))
            -
            (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
            and (a.tgl_sts < ?))

            ) AS terima_lalu,

            (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd and
            (a.tgl_sts >= ? AND a.tgl_sts <= ?))

            +
             (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
             ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
             WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd and
             (a.tgl_sts >= ? AND a.tgl_sts <= ?) and b.kd_rek6='410411010001')

            +

            (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek5,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd and
            (a.tgl_sts >= ? AND a.tgl_sts <= ?)) AS keluar_ini,



            (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd
            and (a.tgl_sts < ?) )
            +

            (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd
            and (a.tgl_sts < ?) and b.kd_rek6='410411010001' )
            +

            (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek5,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd
            and (a.tgl_sts < ?)) AS keluar_lalu

            FROM trdrka z WHERE left(z.kd_skpd,len(?))=?  and z.jns_ang=?
            and left(z.kd_rek6,1)='4' and right(z.kd_sub_kegiatan,7)='00.0004'
            GROUP BY z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,2))zzz group by left(kd_skpd,len(?)),kd_skpd,kd_sub_kegiatan,kd_rek )a
            left join (select kd_rek2, nm_rek2 from ms_rek2) b on a.kd_rek=b.kd_rek2

                     UNION ALL
         SELECT a.kd_skpd, a.kd_sub_kegiatan, b.kd_rek3 kode, b.nm_rek3 nama, a.ang,
                a.anggaran, isnull(terima_ini,0) as terima_ini,
                isnull(terima_lalu,0) terima_lalu, isnull(keluar_ini,0) keluar_ini,
                isnull(keluar_lalu,0) keluar_lalu
         from (

            SELECT left(kd_skpd,len(?)) as kd_skpd,kd_sub_kegiatan,kd_rek,sum(ang)as ang,sum(anggaran)as anggaran,
            sum(terima_ini)as terima_ini,sum(terima_lalu)as terima_lalu,sum(keluar_ini)as keluar_ini,sum(keluar_lalu)as keluar_lalu from(

            SELECT z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,4) kd_rek, SUM(z.nilai) AS ang,
            SUM(z.nilai) AS anggaran,
            ((SELECT isnull(SUM(a.nilai),0) nilai
            FROM tr_terima a WHERE left(a.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd
            and (a.tgl_terima >= ? AND a.tgl_terima <= ?))+
            (SELECT isnull(SUM(a.nilai),0) nilai
            FROM tr_terima_blud a WHERE left(a.kd_rek5,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd
            and (a.tgl_terima >= ? AND a.tgl_terima <= ?))
            -
            (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
            and (a.tgl_sts >= ? AND a.tgl_sts <= ?)
            )
            ) AS terima_ini,

            ((SELECT isnull(SUM(a.nilai),0) FROM tr_terima a WHERE left(a.kd_rek6,4)=left(z.kd_rek6,4)
            and a.kd_skpd=z.kd_skpd and (a.tgl_terima < ?))+(SELECT isnull(SUM(a.nilai),0) FROM tr_terima_blud a WHERE left(a.kd_rek5,4)=left(z.kd_rek6,4)
            and a.kd_skpd=z.kd_skpd and (a.tgl_terima < ?))
            -
            (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
            and (a.tgl_sts < ?))

            ) AS terima_lalu,

            (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd and
            (a.tgl_sts >= ? AND a.tgl_sts <= ?))

            +
             (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
             ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
             WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd and
             (a.tgl_sts >= ? AND a.tgl_sts <= ?) and b.kd_rek6='410411010001')

            +

            (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek5,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd and
            (a.tgl_sts >= ? AND a.tgl_sts <= ?)) AS keluar_ini,



            (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd
            and (a.tgl_sts < ?) )
            +

            (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd
            and (a.tgl_sts < ?) and b.kd_rek6='410411010001' )
            +

            (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek5,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd
            and (a.tgl_sts < ?)) AS keluar_lalu

            FROM trdrka z WHERE left(z.kd_skpd,len(?))=?  and z.jns_ang=?
            and left(z.kd_rek6,1)='4' and right(z.kd_sub_kegiatan,7)='00.0004'
            GROUP BY z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,4))zzz group by left(kd_skpd,len(?)),kd_skpd,kd_sub_kegiatan,kd_rek )a
            left join (select kd_rek3, nm_rek3 from ms_rek3) b on a.kd_rek=b.kd_rek3

             UNION ALL

         SELECT a.kd_skpd, a.kd_sub_kegiatan, b.kd_rek4 kode, b.nm_rek4 nama, a.ang,
                a.anggaran, isnull(terima_ini,0) as terima_ini,
                isnull(terima_lalu,0) terima_lalu, isnull(keluar_ini,0) keluar_ini,
                isnull(keluar_lalu,0) keluar_lalu
         from (

            SELECT left(kd_skpd,len(?)) as kd_skpd,kd_sub_kegiatan,kd_rek,sum(ang)as ang,sum(anggaran)as anggaran,
            sum(terima_ini)as terima_ini,sum(terima_lalu)as terima_lalu,sum(keluar_ini)as keluar_ini,sum(keluar_lalu)as keluar_lalu from(

            SELECT z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,6) kd_rek, SUM(z.nilai) AS ang,
            SUM(z.nilai) AS anggaran,
            ((SELECT isnull(SUM(a.nilai),0) nilai
            FROM tr_terima a WHERE left(a.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd
            and (a.tgl_terima >= ? AND a.tgl_terima <= ?))+
            (SELECT isnull(SUM(a.nilai),0) nilai
            FROM tr_terima_blud a WHERE left(a.kd_rek5,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd
            and (a.tgl_terima >= ? AND a.tgl_terima <= ?))
            -
            (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
            and (a.tgl_sts >= ? AND a.tgl_sts <= ?)
            )
            ) AS terima_ini,

            ((SELECT isnull(SUM(a.nilai),0) FROM tr_terima a WHERE left(a.kd_rek6,6)=left(z.kd_rek6,6)
            and a.kd_skpd=z.kd_skpd and (a.tgl_terima < ?))+(SELECT isnull(SUM(a.nilai),0) FROM tr_terima_blud a WHERE left(a.kd_rek5,6)=left(z.kd_rek6,6)
            and a.kd_skpd=z.kd_skpd and (a.tgl_terima < ?))
            -
            (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
            and (a.tgl_sts < ?))

            ) AS terima_lalu,

            (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd and
            (a.tgl_sts >= ? AND a.tgl_sts <= ?))

            +
             (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
             ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
             WHERE left(b.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd and
             (a.tgl_sts >= ? AND a.tgl_sts <= ?) and b.kd_rek6='410411010001')

            +

            (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek5,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd and
            (a.tgl_sts >= ? AND a.tgl_sts <= ?)) AS keluar_ini,



            (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd
            and (a.tgl_sts < ?) )
            +

            (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd
            and (a.tgl_sts < ?) and b.kd_rek6='410411010001' )
            +

            (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
            ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
            WHERE left(b.kd_rek5,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd
            and (a.tgl_sts < ?)) AS keluar_lalu

            FROM trdrka z WHERE left(z.kd_skpd,len(?))=?  and z.jns_ang=?
            and left(z.kd_rek6,1)='4' and right(z.kd_sub_kegiatan,7)='00.0004'
            GROUP BY z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,6))zzz group by left(kd_skpd,len(?)),kd_skpd,kd_sub_kegiatan,kd_rek )a
            left join (select kd_rek4, nm_rek4 from ms_rek4) b on a.kd_rek=b.kd_rek4
                     UNION ALL

         select a.kd_skpd, a.kd_sub_kegiatan, b.kd_rek5 kode, b.nm_rek5 nama, a.ang, a.anggaran, isnull(terima_ini,0) as terima_ini, isnull(terima_lalu,0) terima_lalu,
         isnull(keluar_ini,0) keluar_ini,isnull(keluar_lalu,0) keluar_lalu
         from (
         SELECT left(kd_skpd,len(?)) as kd_skpd,kd_sub_kegiatan,kd_rek,sum(ang)as ang,sum(anggaran)as anggaran,
            sum(terima_ini)as terima_ini,sum(terima_lalu)as terima_lalu,sum(keluar_ini)as keluar_ini,sum(keluar_lalu)as keluar_lalu from(
         SELECT z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,8) kd_rek, SUM(z.nilai) AS ang,
         SUM(z.nilai) AS anggaran,
         ((SELECT isnull(SUM(a.nilai),0) nilai FROM tr_terima a
                     WHERE left(a.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and (a.tgl_terima >= ? AND a.tgl_terima <= ?))+
                     (SELECT isnull(SUM(a.nilai),0) nilai FROM tr_terima_blud a
                     WHERE left(a.kd_rek5,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and (a.tgl_terima >= ? AND a.tgl_terima <= ?))
                     -
                     (SELECT ISNULL(SUM(b.rupiah),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3') and (a.tgl_sts >= ? AND a.tgl_sts <= ?))) AS terima_ini,
         ((SELECT isnull(SUM(a.nilai),0) FROM tr_terima a
                    WHERE left(a.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd
                    and (a.tgl_terima < ?)) +

                    (SELECT isnull(SUM(a.nilai),0) FROM tr_terima_blud a
                    WHERE left(a.kd_rek5,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd
                    and (a.tgl_terima < ?))
                    -
                     (SELECT ISNULL(SUM(b.rupiah),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3') and (a.tgl_sts < ?))

                     ) AS terima_lalu,


                (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and (a.tgl_sts >= ? AND a.tgl_sts <= ?))
                     +
                     (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd
                     and (a.tgl_sts >= ? AND a.tgl_sts <= ?) and b.kd_rek6='410411010001' )
                     +
             (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek5,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and (a.tgl_sts >= ? AND a.tgl_sts <= ?))  AS keluar_ini,

                (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd
                     and (a.tgl_sts < ?))+
                     (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd
                     and (a.tgl_sts < ?) and b.kd_rek6='410411010001' )
                     +
                     (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek5,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd
                     and (a.tgl_sts < ?)) AS keluar_lalu

         FROM trdrka z WHERE left(z.kd_skpd,len(?))=?  and z.jns_ang=?
         and left(z.kd_rek6,1)='4' and right(z.kd_sub_kegiatan,7)='00.0004'
         GROUP BY z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,8)
         )zzz group by kd_skpd,left(kd_skpd,len(?)),kd_sub_kegiatan,kd_rek
         )a
         left join
         (select kd_rek5, nm_rek5 from ms_rek5) b
         on a.kd_rek=b.kd_rek5

         UNION ALL
          SELECT left(kd_skpd,len(?)) as kd_skpd,kd_sub_kegiatan,kode, nama,sum(ang)as ang,sum(anggaran)as anggaran,
            sum(terima_ini)as terima_ini,sum(terima_lalu)as terima_lalu,sum(keluar_ini)as keluar_ini,sum(keluar_lalu)as keluar_lalu from(

         SELECT z.kd_skpd, z.kd_sub_kegiatan, z.kd_rek6 kode, z.nm_rek6 nama,
                SUM(z.nilai) AS ang, SUM(z.nilai) AS anggaran,
                ((SELECT isnull(SUM(a.nilai),0) FROM tr_terima a
                     WHERE a.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd and (a.tgl_terima >= ? AND a.tgl_terima <= ?))+
                     (SELECT isnull(SUM(a.nilai),0) FROM tr_terima_blud a
                     WHERE left(a.kd_rek5,12)=z.kd_rek6 and a.kd_skpd=z.kd_skpd and (a.tgl_terima >= ? AND a.tgl_terima <= ?))
                     -
                     (SELECT ISNULL(SUM(b.rupiah),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd and jns_trans in ('3') and (a.tgl_sts >= ? AND a.tgl_sts <= ?))) AS terima_ini,

                (
                 (SELECT isnull(SUM(a.nilai),0) FROM tr_terima a
                    WHERE a.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd
                    and (a.tgl_terima < ?))
                    +
                    (SELECT isnull(SUM(a.nilai),0) FROM tr_terima_blud a
                    WHERE left(a.kd_rek5,12)=z.kd_rek6 and a.kd_skpd=z.kd_skpd
                    and (a.tgl_terima < ?))
                    -
                     (SELECT ISNULL(SUM(b.rupiah),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd and jns_trans in ('3') and (a.tgl_sts < ?))


                     ) AS terima_lalu,


                (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd and (a.tgl_sts >= ? AND a.tgl_sts <= ?))
                     +

                     (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd and (a.tgl_sts >= ? AND a.tgl_sts <= ?) and b.kd_rek6='410411010001')
                     +
                     (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek5,12)=z.kd_rek6 and a.kd_skpd=z.kd_skpd and (a.tgl_sts >= ? AND a.tgl_sts <= ?)) AS keluar_ini,


                (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd
                     and (a.tgl_sts < ?))
                     +
                     (SELECT isnull(SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_ppkd a INNER JOIN trdkasin_ppkd b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd
                     and (a.tgl_sts < ?) and b.kd_rek6='410411010001')
                     +

                     (SELECT isnull(SUM(case when jns_trans in ('4') then b.rupiah*-1 else b.rupiah end),0) FROM trhkasin_blud a INNER JOIN trdkasin_blud b
                     ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
                     WHERE left(b.kd_rek5,12)=z.kd_rek6 and a.kd_skpd=z.kd_skpd
                     and (a.tgl_sts < ?)) AS keluar_lalu

         FROM trdrka z WHERE left(z.kd_skpd,len(?))=?  and z.jns_ang=?
         and left(z.kd_rek6,1)='4' and right(z.kd_sub_kegiatan,7)='00.0004'
         GROUP BY z.kd_skpd, z.kd_sub_kegiatan, z.kd_rek6,z.nm_rek6
         )zzz group by kd_skpd,left(kd_skpd,len(?)),kd_sub_kegiatan,kode,nama

                     order by kd_skpd,kode", [$kd_org, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $kd_org, $kd_org, $jns_ang, $kd_org, $kd_org, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $kd_org, $kd_org, $jns_ang, $kd_org, $kd_org, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $kd_org, $kd_org, $jns_ang, $kd_org, $kd_org, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $kd_org, $kd_org, $jns_ang, $kd_org, $kd_org, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal1, $tanggal1, $kd_org, $kd_org, $jns_ang, $kd_org]);
          } else {
               $rincian = DB::select("SELECT a.kd_skpd, a.kd_sub_kegiatan, b.kd_rek2 kode, b.nm_rek2 nama, a.ang, isnull(terima_ini,0) as terima_ini,
								   isnull(terima_lalu,0) terima_lalu, isnull(keluar_ini,0) keluar_ini,
								   isnull(keluar_lalu,0) keluar_lalu
							from (
							   SELECT z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,2) kd_rek, SUM(z.nilai) AS ang,
							   ((SELECT SUM(a.nilai) nilai
							   FROM tr_terima a WHERE left(a.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd
							   and (a.tgl_terima >= ? AND a.tgl_terima <= ?))
							   -
							   (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
							   ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
							   WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
							   and (a.tgl_sts >= ? AND a.tgl_sts <= ?)
							   )) AS terima_ini,
							   ((SELECT SUM(a.nilai) FROM tr_terima a WHERE left(a.kd_rek6,2)=left(z.kd_rek6,2)
							   and a.kd_skpd=z.kd_skpd and (a.tgl_terima < ?))
							   -
							   (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
							   ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
							   WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
							   and (a.tgl_sts < ?)
							   )) AS terima_lalu,
							   (SELECT SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
							   ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
							   WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd and
							   (a.tgl_sts >= ? AND a.tgl_sts <= ?)) AS keluar_ini,
							   (SELECT SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
							   ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
							   WHERE left(b.kd_rek6,2)=left(z.kd_rek6,2) and a.kd_skpd=z.kd_skpd
							   and (a.tgl_sts < ?)) AS keluar_lalu
							   FROM trdrka z WHERE left(z.kd_skpd,len(?))=? and z.jns_ang = ?
							   and left(z.kd_rek6,1)='4' and right(z.kd_sub_kegiatan,7)='00.0004'
							   GROUP BY z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,2) )a
							   left join (select kd_rek2, nm_rek2 from ms_rek2) b on a.kd_rek=b.kd_rek2

							UNION ALL

							select a.kd_skpd, a.kd_sub_kegiatan, b.kd_rek3 kode, b.nm_rek3 nama, a.ang,
								   isnull(terima_ini,0) as terima_ini,
								   isnull(terima_lalu,0) terima_lalu, isnull(keluar_ini,0) keluar_ini,
								   isnull(keluar_lalu,0) keluar_lalu
							from (
							   SELECT z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,4) kd_rek, SUM(z.nilai) AS ang,
							   ((SELECT SUM(a.nilai) nilai
							   FROM tr_terima a WHERE left(a.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd
							   and (a.tgl_terima >= ? AND a.tgl_terima <= ?))
							   -
							   (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
							   ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
							   WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
							   and (a.tgl_sts >= ? AND a.tgl_sts <= ?)
							   )) AS terima_ini,
							   ((SELECT SUM(a.nilai) FROM tr_terima a WHERE left(a.kd_rek6,4)=left(z.kd_rek6,4)
							   and a.kd_skpd=z.kd_skpd and (a.tgl_terima < ?))
							   -
							   (SELECT ISNULL(SUM(b.rupiah),0) nilai FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
							   ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
							   WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3')
							   and (a.tgl_sts < ?)
							   )) AS terima_lalu,
							   (SELECT SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
							   ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
							   WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd and
							   (a.tgl_sts >= ? AND a.tgl_sts <= ?)) AS keluar_ini,
							   (SELECT SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
							   ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
							   WHERE left(b.kd_rek6,4)=left(z.kd_rek6,4) and a.kd_skpd=z.kd_skpd
							   and (a.tgl_sts < ?)) AS keluar_lalu
							   FROM trdrka z WHERE left(z.kd_skpd,len(?))=? and z.jns_ang = ?
							   and left(z.kd_rek6,1)='4' and right(z.kd_sub_kegiatan,7)='00.0004'
							   GROUP BY z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,4) )a
							   left join (select kd_rek3, nm_rek3 from ms_rek3) b on a.kd_rek=b.kd_rek3

							UNION ALL
                            select a.kd_skpd, a.kd_sub_kegiatan, b.kd_rek4 kode, b.nm_rek4 nama, a.ang,
                                isnull(terima_ini,0) as terima_ini, isnull(terima_lalu,0) terima_lalu, isnull(keluar_ini,0) keluar_ini,
                                isnull(keluar_lalu,0) keluar_lalu
                            from ( SELECT z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,6) kd_rek, SUM(z.nilai) AS ang,
                                (SELECT SUM(a.rupiah) nilai
                                    FROM trdkasin_pkd a inner join trhkasin_pkd b on a.kd_skpd=b.kd_skpd and a.no_sts=b.no_sts
                                    WHERE left(a.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd AND a.kd_rek6<>'4141009' AND LEFT(kd_rek6,1)=4 AND b.jns_trans NOT IN (5,1)
                                    and (b.tgl_sts between ? AND ? )) AS terima_ini,
                                (SELECT SUM(a.rupiah) nilai
                                    FROM trdkasin_pkd a inner join trhkasin_pkd b on a.kd_skpd=b.kd_skpd and a.no_sts=b.no_sts
                                    WHERE left(a.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd AND a.kd_rek6<>'4141009' AND LEFT(kd_rek6,1)=4 AND b.jns_trans NOT IN (5,1)
                                    and (b.tgl_sts < ?  )) AS terima_lalu,
                                (SELECT SUM(a.rupiah) nilai
                                    FROM trdkasin_pkd a inner join trhkasin_pkd b on a.kd_skpd=b.kd_skpd and a.no_sts=b.no_sts
                                    WHERE left(a.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd AND a.kd_rek6<>'4141009' AND LEFT(kd_rek6,1)=4 AND b.jns_trans NOT IN (5,1)
                                    and (b.tgl_sts between ? AND ? )) AS keluar_ini,
                                (SELECT SUM(a.rupiah) nilai
                                    FROM trdkasin_pkd a inner join trhkasin_pkd b on a.kd_skpd=b.kd_skpd and a.no_sts=b.no_sts
                                    WHERE left(a.kd_rek6,6)=left(z.kd_rek6,6) and a.kd_skpd=z.kd_skpd AND a.kd_rek6<>'4141009' AND LEFT(kd_rek6,1)=4 AND b.jns_trans NOT IN (5,1)
                                    and (b.tgl_sts < ? )) AS keluar_lalu
                                            FROM trdrka z
                                    WHERE left(z.kd_skpd,len( ? ))= ?  and left(z.kd_rek6,1)='4' and z.jns_ang = ?
                                    and right(z.kd_sub_kegiatan,7)='00.0004' GROUP BY z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,6) )a
                                    left join (select kd_rek4, nm_rek4 from ms_rek4) b on a.kd_rek=b.kd_rek4

                            UNION ALL

							select a.kd_skpd, a.kd_sub_kegiatan, b.kd_rek5 kode, b.nm_rek5 nama, a.ang, isnull(terima_ini,0) as terima_ini, isnull(terima_lalu,0) terima_lalu,
							isnull(keluar_ini,0) keluar_ini,isnull(keluar_lalu,0) keluar_lalu
							from (
							SELECT z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,8) kd_rek, SUM(z.nilai) AS ang,
							((SELECT SUM(a.nilai) nilai FROM tr_terima a
										WHERE left(a.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and (a.tgl_terima >= ? AND a.tgl_terima <= ?))
										-
										(SELECT ISNULL(SUM(b.rupiah),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
										ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
										WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3') and (a.tgl_sts >= ? AND a.tgl_sts <= ? ))) AS terima_ini,
							((SELECT SUM(a.nilai) FROM tr_terima a
									   WHERE left(a.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd
									   and (a.tgl_terima < ? ))
									   -
										(SELECT ISNULL(SUM(b.rupiah),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
										ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
										WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and jns_trans IN ('3') and (a.tgl_sts < ? ))) AS terima_lalu,
								   (SELECT SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
										ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
										WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd and (a.tgl_sts >= ? AND a.tgl_sts <= ?)) AS keluar_ini,
								   (SELECT SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
										ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
										WHERE left(b.kd_rek6,8)=left(z.kd_rek6,8) and a.kd_skpd=z.kd_skpd
										and (a.tgl_sts < ?)) AS keluar_lalu
							FROM trdrka z WHERE left(z.kd_skpd,len( ? ))= ?  and z.jns_ang = ?
							and left(z.kd_rek6,1)='4' and right(z.kd_sub_kegiatan,7)='00.0004'
							GROUP BY z.kd_skpd, z.kd_sub_kegiatan, left(z.kd_rek6,8)
							)a
							left join
							(select kd_rek5, nm_rek5 from ms_rek5) b
							on a.kd_rek=b.kd_rek5

							UNION ALL

							SELECT z.kd_skpd, z.kd_sub_kegiatan, z.kd_rek6 kode, z.nm_rek6 nama,
								   SUM(z.nilai) AS ang,
								   ((SELECT SUM(a.nilai) FROM tr_terima a
										WHERE a.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd and (a.tgl_terima >= ? AND a.tgl_terima <= ? ))
										-
										(SELECT ISNULL(SUM(b.rupiah),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
										ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
										WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd and jns_trans in ('3') and (a.tgl_sts >= ? AND a.tgl_sts <= ? ))) AS terima_ini,
								   ((SELECT SUM(a.nilai) FROM tr_terima a
									   WHERE a.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd
									   and (a.tgl_terima < ? ))
									   -
										(SELECT ISNULL(SUM(b.rupiah),0) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
										ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
										WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd and jns_trans in ('3') and (a.tgl_sts < ? ))) AS terima_lalu,
								   (SELECT SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
										ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
										WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd and (a.tgl_sts >= ? AND a.tgl_sts <= ? )) AS keluar_ini,
								   (SELECT SUM(case when jns_trans in ('3') then b.rupiah*-1 else b.rupiah end) FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b
										ON RTRIM(a.no_sts)=RTRIM(b.no_sts) and a.kd_skpd=b.kd_skpd
										WHERE b.kd_rek6=z.kd_rek6 and a.kd_skpd=z.kd_skpd
										and (a.tgl_sts < ? )) AS keluar_lalu
							FROM trdrka z WHERE left(z.kd_skpd,len( ? ))= ?  and z.jns_ang = ?
							and left(z.kd_rek6,1)='4' and right(z.kd_sub_kegiatan,7)='00.0004'
							GROUP BY z.kd_skpd, z.kd_sub_kegiatan, z.kd_rek6,z.nm_rek6
										order by kode", [$tanggal1, $tanggal2,$tanggal1, $tanggal2,$tanggal1,$tanggal1,$tanggal1,$tanggal2,$tanggal1,$kd_skpd,$kd_skpd, $jns_ang,$tanggal1, $tanggal2,$tanggal1,$tanggal2,$tanggal1,$tanggal1,$tanggal1,$tanggal2,$tanggal1,$kd_skpd,$kd_skpd,$jns_ang,$tanggal1, $tanggal2,$tanggal1,$tanggal1, $tanggal2,$tanggal1, $kd_skpd, $kd_skpd, $jns_ang,$tanggal1, $tanggal2,$tanggal1, $tanggal2,$tanggal1, $tanggal1,$tanggal1, $tanggal2,$tanggal1, $kd_skpd, $kd_skpd, $jns_ang,$tanggal1, $tanggal2,$tanggal1, $tanggal2,$tanggal1, $tanggal1,$tanggal1, $tanggal2,$tanggal1, $kd_skpd, $kd_skpd, $jns_ang]);
          }



          $daerah = DB::table('sclient')->select('daerah')->where('kd_skpd', $kd_skpd)->first();
          $nm_skpd = cari_nama($kd_skpd, 'ms_skpd', 'kd_skpd', 'nm_skpd');
          $data = [
               'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
               'skpd'              => DB::table('ms_skpd')->select('nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
               'tanggal1'          => $tanggal1,
               'tanggal2'          => $tanggal2,
               'rincian'           => $rincian,
               'enter'             => $enter,
               'kd_skpd'           => $kd_skpd,
               'daerah'            => $daerah,
               'tanggal_ttd'       => $tanggal_ttd,
               'cari_pa_kpa'       => $cari_pakpa,
               'cari_bendahara'    => $cari_bendahara,
               'spasi'             => $spasi
          ];

          $view = view('skpd.laporan_bendahara_penerimaan.cetak.spj_pendapatan')->with($data);

          if ($cetak == '1') {
               return $view;
          } else if ($cetak == '2') {
               $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
               return $pdf->stream('BP KAS BANK.pdf');
          } else {

               header("Cache-Control: no-cache, no-store, must_revalidate");
               header('Content-Type: application/vnd.ms-excel');
               header('Content-Disposition: attachement; filename="BP KAS BANK - ' . $nm_skpd . '.xls"');
               return $view;
          }
     }
}
