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

class BukuPenerimaanPenyetoranController extends Controller
{


    // Cetak List
    public function cetakBukuPenerimaanPenyetoran(Request $request)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);
        $tanggal_ttd    = $request->tgl_ttd;
        $pa_kpa         = $request->pa_kpa;
        $bendahara      = $request->bendahara;
        $tanggal1       = $request->tanggal1;
        $tanggal2       = $request->tanggal2;
        $enter          = $request->spasi;
        $format         = $request->format;
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
        if ($kd_skpd == '1.02.0.00.0.00.02.0000' || $kd_skpd == '1.02.0.00.0.00.03.0000') {    //ada kondisi BLUD
            $rincian = DB::select("SELECT a.tgl_terima tgl, a.no_terima no,
            -- cast(left(a.no_terima, charindex('/', a.no_terima) - 1) as int) as urut,
                case when a.tgl_terima >= ? and a.tgl_terima <= ? then cast (a.tgl_terima as varchar(25)) else '' end tgl_terima,
                case when a.tgl_terima >= ? and a.tgl_terima <= ? then a.no_terima else '' end no_terima,
                a.kd_rek5 as kd_rek6,b.nm_rek6,
                case when a.tgl_terima >= ? and a.tgl_terima <= ? then a.nilai else 0 end nilai,
                case when c.tgl_sts >= ? and c.tgl_sts <= ? then cast (c.tgl_sts as varchar(25)) else '' end tgl_sts,
                case when c.tgl_sts >= ? and c.tgl_sts <= ? then c.no_sts else '' end no_sts,
                case when c.tgl_sts >= ? and c.tgl_sts <= ? then c.rupiah else 0 end total,
                a.keterangan, c.status FROM tr_terima_blud a INNER JOIN ms_rek6 b
                ON a.kd_rek5=b.kd_rek6
                LEFT JOIN (SELECT x.tgl_sts,x.no_sts,x.kd_skpd,y.no_terima,SUM(y.rupiah) as rupiah,''status FROM trhkasin_blud x INNER JOIN trdkasin_blud y ON x.no_sts=y.no_sts AND x.kd_skpd=y.kd_skpd
                GROUP BY x.tgl_sts,x.no_sts,x.kd_skpd,y.no_terima) c
                ON a.no_terima=c.no_terima AND a.kd_skpd=c.kd_skpd
                where ((a.tgl_terima >= ? and a.tgl_terima <= ?) or (c.tgl_sts >= ? and c.tgl_sts <= ?))
                and left(a.kd_skpd,len(?)) = ?
                union all
                select x.tgl_sts tgl, x.no_sts no, '' tgl_terima, '' no_terima, kd_rek5 as kd_rek6, (select nm_rek6 from ms_rek6 where kd_rek6=y.kd_rek5) nm_rek6,
                -- '' urut,
                0 nilai, cast (x.tgl_sts as varchar(25)) tgl_sts, x.no_sts, y.rupiah total, x.keterangan,	''status
                FROM trhkasin_blud x INNER JOIN trdkasin_blud y ON x.no_sts=y.no_sts AND x.kd_skpd=y.kd_skpd
                where x.tgl_sts >= ? and x.tgl_sts <= ? and left(x.kd_skpd,len(?)) = ? and jns_trans='2'
                union all
                select x.tgl_sts tgl, x.no_sts no, cast(x.tgl_sts as varchar(25)) tgl_terima, x.no_sts no_terima, kd_rek5 as kd_rek6, (select nm_rek6 from ms_rek6 where kd_rek6=y.kd_rek5) nm_rek6,
                -- '' urut,
                y.rupiah*-1 nilai, cast(x.tgl_sts as varchar(25)) tgl_sts, x.no_sts, y.rupiah*-1 total, x.keterangan, ''status
                FROM trhkasin_blud x INNER JOIN trdkasin_blud y ON x.no_sts=y.no_sts AND x.kd_skpd=y.kd_skpd
                where x.tgl_sts >= ? and x.tgl_sts <= ? and left(x.kd_skpd,len(?)) = ? and jns_trans='3'
                order by tgl, no", [$tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $kd_org, $kd_org, $tanggal1, $tanggal2, $kd_org, $kd_org, $tanggal1, $tanggal2, $kd_org, $kd_org]);
        } else {
            $rincian = DB::select("SELECT a.tgl_terima tgl, a.no_terima no,
            -- cast(left(a.no_terima, charindex('/', a.no_terima) - 1) as int) as urut,
                case when a.tgl_terima >= ? and a.tgl_terima <= ? then cast (a.tgl_terima as varchar(25)) else '' end tgl_terima,
                case when a.tgl_terima >= ? and a.tgl_terima <= ? then a.no_terima else '' end no_terima,
                a.kd_rek6,b.nm_rek6,a.status_setor as status_setor,
                case when a.tgl_terima >= ? and a.tgl_terima <= ? then a.nilai else 0 end nilai,
                case when c.tgl_sts >= ? and c.tgl_sts <= ? then cast (c.tgl_sts as varchar(25)) else '' end tgl_sts,
                case when c.tgl_sts >= ? and c.tgl_sts <= ? then c.no_sts else '' end no_sts,
                case when c.tgl_sts >= ? and c.tgl_sts <= ? then c.rupiah else 0 end total,
                a.keterangan, c.status FROM tr_terima a INNER JOIN ms_rek6 b
                ON a.kd_rek6=b.kd_rek6
                LEFT JOIN (SELECT x.tgl_sts,x.no_sts,x.kd_skpd,y.no_terima,y.rupiah as rupiah, x.status FROM trhkasin_pkd x INNER JOIN trdkasin_pkd y ON x.no_sts=y.no_sts AND x.kd_skpd=y.kd_skpd AND x.kd_sub_kegiatan=y.kd_sub_kegiatan
                GROUP BY x.tgl_sts,x.no_sts,x.kd_skpd,y.no_terima,y.rupiah ,x.status) c
                ON a.no_terima=c.no_terima AND a.kd_skpd=c.kd_skpd and a.nilai=c.rupiah
                where ((a.tgl_terima >= ? and a.tgl_terima <= ?) or (c.tgl_sts >= ? and c.tgl_sts <= ?))
                and left(a.kd_skpd,len(?)) = ?
                union all
                select x.tgl_sts tgl, x.no_sts no, '' tgl_terima, '' no_terima, kd_rek6, (select nm_rek6 from ms_rek6 where kd_rek6=y.kd_rek6) nm_rek6, 	'' as status_setor,
                -- '' urut,
                0 nilai, cast (x.tgl_sts as varchar(25)) tgl_sts, x.no_sts, y.rupiah total, x.keterangan,	x.status
                FROM trhkasin_pkd x INNER JOIN trdkasin_pkd y ON x.no_sts=y.no_sts AND x.kd_skpd=y.kd_skpd AND x.kd_sub_kegiatan=y.kd_sub_kegiatan
                where x.tgl_sts >= ? and x.tgl_sts <= ? and left(x.kd_skpd,len(?)) = ? and jns_trans='2'
                union all
                select x.tgl_sts tgl, x.no_sts no, cast(x.tgl_sts as varchar(25)) tgl_terima, x.no_sts no_terima, kd_rek6, (select nm_rek6 from ms_rek6 where kd_rek6=y.kd_rek6) nm_rek6,'' as status_setor,
                -- '' urut,
                y.rupiah*-1 nilai, cast(x.tgl_sts as varchar(25)) tgl_sts, x.no_sts, y.rupiah*-1 total, x.keterangan, x.status
                FROM trhkasin_pkd x INNER JOIN trdkasin_pkd y ON x.no_sts=y.no_sts AND x.kd_skpd=y.kd_skpd AND x.kd_sub_kegiatan=y.kd_sub_kegiatan
                where x.tgl_sts >= ? and x.tgl_sts <= ? and left(x.kd_skpd,len(?)) = ? and jns_trans='3'
                order by tgl, no", [$tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $tanggal1, $tanggal2, $kd_org, $kd_org, $tanggal1, $tanggal2, $kd_org, $kd_org, $tanggal1, $tanggal2, $kd_org, $kd_org]);
        }
        //dd($rincian);



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
            'spasi'             => $spasi,
        ];

        if ($format == '13') {
            $view = view('skpd.laporan_bendahara_penerimaan.cetak.buku_penerimaan_penyetoran')->with($data);
        } else {
            $view = view('skpd.laporan_bendahara_penerimaan.cetak.buku_penerimaan_penyetoran_77')->with($data);
        }



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
