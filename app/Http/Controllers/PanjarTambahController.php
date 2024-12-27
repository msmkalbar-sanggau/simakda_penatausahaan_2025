<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Static_;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;
use stdClass;

class PanjarTambahController extends Controller
{
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        // $sql = "SELECT top $rows * from tr_panjar_cmsbank where kd_skpd='$kd_skpd' $where and no_panjar not in (SELECT top $offset no_panjar FROM tr_panjar_cmsbank  where kd_skpd='$kd_skpd' and jns='1' $where order by no_panjar) and jns='1' order by no_panjar";

        $data = [
            'dpanjar' => DB::table('tr_panjar_cmsbank')->select('no_panjar', 'tgl_panjar', 'kd_skpd','nilai')->where('kd_skpd',$kd_skpd)->whereIn('jns',['2'])->orderByRaw("no_panjar")->get()
        ];
        

        return view('skpd.tpanjar_cms.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('tr_panjar_cmsbank')->select('*')->where('kd_skpd', $kd_skpd)->whereIn('jns',['2'])->orderByRaw("no_panjar")->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
                $btn = '<a href="' . route("tpanjar_cms.edit", Crypt::encryptString($row->no_panjar)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="fa fa-edit"></i></a>';
            if($row->status_upload == 0){
                $btn .= '<a href="javascript:void(0);" onclick="deleteData(' . $row->no_panjar . ', \'' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm" id="delete" style="margin-right:4px"><i class="fas fa-trash-alt"></i></a>';
             }
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function edit($no_panjar)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_panjar = Crypt::decryptString($no_panjar);
        $jns_anggaran = status_anggaran_new();
        $jns_ang = $jns_anggaran->jns_ang;
        
        $rek_tujuan = DB::select("SELECT b.no_bukti,b.tgl_bukti,b.rekening_awal,b.nm_rekening_tujuan,b.rekening_tujuan,
        b.bank_tujuan,b.kd_skpd,b.nilai,(select sum(nilai) from tr_panjar_transfercms where no_bukti=b.no_bukti and kd_skpd=b.kd_skpd and tgl_bukti=b.tgl_bukti) as total
        FROM tr_panjar_cmsbank a INNER JOIN tr_panjar_transfercms b ON a.no_kas=b.no_bukti
        AND a.kd_skpd=b.kd_skpd and a.tgl_kas=b.tgl_bukti
        WHERE b.no_bukti=? AND b.kd_skpd=?
        group by b.no_bukti,b.tgl_bukti,b.rekening_awal,b.nm_rekening_tujuan,b.rekening_tujuan,
        b.bank_tujuan,b.kd_skpd,b.nilai",[$no_panjar,$kd_skpd]);
        $total_belanja = 0;
     
        foreach($rek_tujuan as $tujuan){
            $total_belanja += $tujuan->nilai;
            $total_transfer= $tujuan->total;
        }
        $hasil = $total_belanja - $total_transfer;
        // dd($total_transfer);
        // return;
        //$data = DB::select("SELECT * from tr_panjar_cmsbank where kd_skpd=? and no_panjar = '?' and jns='1' order by no_panjar",[$kd_skpd,$no_panjar]);
        $data = [
            'data_panjar' => DB::table('tr_panjar_cmsbank as a')->join('ms_sub_kegiatan as b ', 'a.kd_sub_kegiatan','=','b.kd_sub_kegiatan')
            ->select('a.*',DB::raw("b.nm_sub_kegiatan"))
            ->where(['a.kd_skpd' => $kd_skpd, 'a.no_panjar' => $no_panjar])->first(),
            // 'sub_kegiatan' =>DB::table('ms_sub_kegiatan as a')->join('tr_panjar_cmsbank as b','a.kd_sub_kegiatan','=','b.kd_sub_kegiatan')->select('a.kd_sub_kegiatan','a.nm_sub_kegiatan') ->where(['b.kd_skpd' => $kd_skpd, 'b.no_panjar' => $no_panjar])->get(),
            'sub_kegiatan'=>DB::select("SELECT a.kd_sub_kegiatan,a.nm_sub_kegiatan, (SELECT ISNULL(SUM(nilai),0) FROM trdrka WHERE kd_sub_kegiatan = a.kd_sub_kegiatan AND kd_skpd=? and jns_ang=?) AS anggaran, 
            (select  nilai=trans-kembali_pjr from (
                SELECT SUM(nilai) [trans], 
                (select isnull(sum(i.nilai),0) from tr_jpanjar i join tr_panjar j on i.no_panjar_lalu=j.no_panjar and i.kd_skpd=j.kd_skpd where 
                j.kd_sub_kegiatan=a.kd_sub_kegiatan and i.jns='2') [kembali_pjr]
                FROM 
                    (SELECT
                        isnull(SUM(c.nilai),0) as nilai
                    FROM
                        trdtransout c
                    LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
                    AND c.kd_skpd = d.kd_skpd
                    WHERE
                        c.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND d.kd_skpd = a.kd_skpd
                    ----------------------------
                    AND d.jns_spp in ('1','3') 
                    ----------------------------
                    UNION ALL
                    SELECT isnull(SUM(x.nilai),0) as nilai FROM trdspp x
                    INNER JOIN trhspp y 
                    ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                    WHERE
                        x.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND x.kd_skpd =a.kd_skpd
                    -------------------------
                    AND y.jns_spp IN ('4','5','6')
                    ------------------------
                    AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
                    UNION ALL
                    SELECT isnull(SUM(nilai),0) as nilai FROM trdtagih t 
                    INNER JOIN trhtagih u 
                    ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                    WHERE 
                    t.kd_sub_kegiatan = a.kd_sub_kegiatan
                    AND u.kd_skpd = a.kd_skpd
                    AND u.no_bukti 
                    NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=a.kd_skpd )
                    ------------------------
                    union all
                    select isnull(sum(f.rupiah),0) [nilai] from trhkasin_pkd e join trdkasin_pkd f 
                    on e.no_sts=f.no_sts and e.kd_skpd=f.kd_skpd where e.no_sp2d like '%TU/BL%' 
                    and f.kd_sub_kegiatan=a.kd_sub_kegiatan and e.jns_cp='3' group by f.kd_sub_kegiatan
                    union all
                    select isnull(sum(nilai),0) [nilai] from tr_panjar where kd_sub_kegiatan=a.kd_sub_kegiatan
                    and no_panjar not in 
                        (select h.no_panjar FROM trdtransout g
                          JOIN trhtransout h ON g.no_bukti = h.no_bukti AND g.kd_skpd = h.kd_skpd
                          WHERE g.kd_sub_kegiatan = a.kd_sub_kegiatan AND g.kd_skpd = a.kd_skpd 
                          AND h.jns_spp in ('1','3') and h.panjar='1') 	and jns='1'						  
                    )r 
                ) z) as transaksi
            FROM trskpd a where a.kd_skpd=? AND a.status_sub_kegiatan='1' and a.jns_ang=? order by a.kd_sub_kegiatan",[$kd_skpd,$jns_ang,$kd_skpd,$jns_ang]),
            
            'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
            'tahun_anggaran' => tahun_anggaran(),
            'daftar_rekening' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->first(),
            'data_rek_tujuan' => DB::table('ms_rekening_bank_online as a')->where(['kd_skpd' => $kd_skpd])->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nmbank"))->orderBy('a.nm_rekening')->get(),
            'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->get(),
            'rincian_rek_tujuan' => $rek_tujuan,
            'pajak' => $hasil,
        ];
        $data['datanopanjarlalu'] = $data['data_panjar']->no_panjar_lalu;
        $data['no_panjar'] = DB::table('tr_panjar')->select('*')->where(['kd_skpd' => $kd_skpd,'no_kas' => $data['datanopanjarlalu'] ])->first();
        //dd($data['data_panjar']);
       
        //return;
        return view('skpd.tpanjar_cms.edit')->with($data);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
         $data = [
             'skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->where(['kd_skpd' => $kd_skpd])->first(),
             'tahun_anggaran' => tahun_anggaran(),
             'daftar_rekening' => DB::table('ms_skpd')->select('rekening')->where(['kd_skpd' => $kd_skpd])->get(),
             'data_rek_tujuan' => DB::table('ms_rekening_bank_online as a')->where(['kd_skpd' => $kd_skpd])->select('a.rekening', 'a.nm_rekening', 'a.bank', 'a.keterangan', 'a.kd_skpd', 'a.jenis', DB::raw("(SELECT nama FROM ms_bank WHERE kode=a.bank) as nmbank"))->orderBy('a.nm_rekening')->get(),

             'data_bank' => DB::table('ms_bank')->select('kode', 'nama')->get()
         ];
       
        return view('skpd.tpanjar_cms.create')->with($data);;
    }

    public function no_urut(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $urut1 = DB::table('trhtransout_cmsbank')->where(['kd_skpd' => $kd_skpd])->select('no_voucher as nomor', DB::raw("'Daftar Transaksi Non Tunai' as ket"), 'kd_skpd');
        $urut2 = DB::table('trhtrmpot_cmsbank')->where(['kd_skpd' => $kd_skpd])->select('no_bukti as nomor', DB::raw("'Potongan Pajak Transaksi Non Tunai' as ket"), 'kd_skpd')->unionAll($urut1);
        $urut3 = DB::table('tr_panjar_cmsbank')->where(['kd_skpd' => $kd_skpd])->select('no_panjar as nomor', DB::raw("'Daftar Panjar' as ket"), 'kd_skpd')->unionAll($urut2);
        $urut = DB::table(DB::raw("({$urut3->toSql()}) AS sub"))
            ->select(DB::raw("CASE WHEN MAX(nomor+1) IS NULL THEN 1 ELSE MAX(nomor+1) END AS nomor"))
            ->mergeBindings($urut3)
            ->whereRaw("kd_skpd = '$kd_skpd'")
            ->groupBy('kd_skpd')
            ->first();
        return response()->json($urut->nomor);
    }

    public function getSubKegiatan(){
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_anggaran = status_anggaran_new();
        $jns_ang = $jns_anggaran->jns_ang;

        // echo $jns_ang;
        // return;
        $data = DB::select("SELECT a.kd_sub_kegiatan,a.nm_sub_kegiatan, (SELECT ISNULL(SUM(nilai),0) FROM trdrka WHERE kd_sub_kegiatan = a.kd_sub_kegiatan AND kd_skpd=? and jns_ang=?) AS anggaran, 
        (select  nilai=trans-kembali_pjr from (
            SELECT SUM(nilai) [trans], 
            (select isnull(sum(i.nilai),0) from tr_jpanjar i join tr_panjar j on i.no_panjar_lalu=j.no_panjar and i.kd_skpd=j.kd_skpd where 
            j.kd_sub_kegiatan=a.kd_sub_kegiatan and i.jns='2') [kembali_pjr]
            FROM 
                (SELECT
                    isnull(SUM(c.nilai),0) as nilai
                FROM
                    trdtransout c
                LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
                AND c.kd_skpd = d.kd_skpd
                WHERE
                    c.kd_sub_kegiatan = a.kd_sub_kegiatan
                AND d.kd_skpd = a.kd_skpd
                ----------------------------
                AND d.jns_spp in ('1','3') 
                ----------------------------
                UNION ALL
                SELECT isnull(SUM(x.nilai),0) as nilai FROM trdspp x
                INNER JOIN trhspp y 
                ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                WHERE
                    x.kd_sub_kegiatan = a.kd_sub_kegiatan
                AND x.kd_skpd =a.kd_skpd
                -------------------------
                AND y.jns_spp IN ('4','5','6')
                ------------------------
                AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
                UNION ALL
                SELECT isnull(SUM(nilai),0) as nilai FROM trdtagih t 
                INNER JOIN trhtagih u 
                ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                WHERE 
                t.kd_sub_kegiatan = a.kd_sub_kegiatan
                AND u.kd_skpd = a.kd_skpd
                AND u.no_bukti 
                NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=a.kd_skpd )
                ------------------------
                union all
                select isnull(sum(f.rupiah),0) [nilai] from trhkasin_pkd e join trdkasin_pkd f 
                on e.no_sts=f.no_sts and e.kd_skpd=f.kd_skpd where e.no_sp2d like '%TU/BL%' 
                and f.kd_sub_kegiatan=a.kd_sub_kegiatan and e.jns_cp='3' group by f.kd_sub_kegiatan
                union all
                select isnull(sum(nilai),0) [nilai] from tr_panjar where kd_sub_kegiatan=a.kd_sub_kegiatan
                and no_panjar not in 
                    (select h.no_panjar FROM trdtransout g
                      JOIN trhtransout h ON g.no_bukti = h.no_bukti AND g.kd_skpd = h.kd_skpd
                      WHERE g.kd_sub_kegiatan = a.kd_sub_kegiatan AND g.kd_skpd = a.kd_skpd 
                      AND h.jns_spp in ('1','3') and h.panjar='1') 	and jns='1'						  
                )r 
            ) z) as transaksi
        FROM trskpd a where a.kd_skpd=? AND a.status_sub_kegiatan='1' and a.jns_ang=? order by a.kd_sub_kegiatan",[$kd_skpd,$jns_ang,$kd_skpd,$jns_ang]);

        return response()->json($data);
    }

    public function cekSimpan(Request $request)
    {
        $no_bukti = $request->no_bukti;
        //$no_panjar = Crypt::decryptString($no_panjar);
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::table('tr_panjar_cmsbank')->where(['no_panjar' => $no_bukti, 'kd_skpd' => $kd_skpd])->count();
        return response()->json($data);
    }

    public function simpanPanjar(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        // dd($data);
        // return;
        DB::beginTransaction();
        try {
            DB::table('tr_panjar_cmsbank')->insert([
                'no_kas' => $data['no_bukti'],
                'tgl_kas' => $data['tgl_voucher'],
                'no_panjar' => $data['no_bukti'],
                'tgl_panjar' => $data['tgl_voucher'],
                'kd_skpd' => $data['kd_skpd'],
                'pengguna' => '',
                'nilai' => $data['total_belanja'],
                'keterangan' => $data['keterangan'],
                'pay' => $data['pembayaran'],
                'rek_bank' => '',
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'status' => '0',
                'jns' => '2',
                'no_panjar_lalu' => $data['no_tpanjar'],
                'rekening_awal' => $data['rek_awal'],
                'ket_tujuan' => $data['ket_tujuan'],
                'status_validasi' => '0',
                'status_upload' => '0',
            ]);
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
            ]);
        }
    }

    public function simpanDetailPanjar(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;
        
        DB::beginTransaction();
        try {
             if (isset($data['rincian_rek_tujuan'])) {
                DB::table('tr_panjar_transfercms')->insert(array_map(function ($value) use ($data, $kd_skpd) {
                    return [
                        'no_bukti' => $value['no_bukti'],
                        'tgl_bukti' => $value['tgl_bukti'],
                        'rekening_awal' => $data['rek_awal'],
                        'nm_rekening_tujuan' => $value['nm_rekening_tujuan'],
                        'rekening_tujuan' => $value['rekening_tujuan'],
                        'bank_tujuan' => $value['bank_tujuan'],
                        'kd_skpd' => $kd_skpd,
                        'nilai' => $value['nilai'],
                    ];
                },$data['rincian_rek_tujuan']));
             }
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function sisaAng(Request $request){
        $kd_skpd = Auth::user()->kd_skpd;
        $jns_anggaran = status_anggaran_new();
        $jns_ang = $jns_anggaran->jns_ang;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        // echo $jns_ang;
        // return;
        $sisa = DB::select("SELECT a.kd_sub_kegiatan,a.nm_sub_kegiatan, (SELECT ISNULL(SUM(nilai),0) FROM trdrka WHERE kd_sub_kegiatan = a.kd_sub_kegiatan AND kd_skpd=? and jns_ang=?) AS anggaran, 
        (select  nilai=trans-kembali_pjr from (
            SELECT SUM(nilai) [trans], 
            (select isnull(sum(i.nilai),0) from tr_jpanjar i join tr_panjar j on i.no_panjar_lalu=j.no_panjar and i.kd_skpd=j.kd_skpd where 
            j.kd_sub_kegiatan=a.kd_sub_kegiatan and i.jns='2') [kembali_pjr]
            FROM 
                (SELECT
                    isnull(SUM(c.nilai),0) as nilai
                FROM
                    trdtransout c
                LEFT JOIN trhtransout d ON c.no_bukti = d.no_bukti
                AND c.kd_skpd = d.kd_skpd
                WHERE
                    c.kd_sub_kegiatan = a.kd_sub_kegiatan
                AND d.kd_skpd = a.kd_skpd
                ----------------------------
                AND d.jns_spp in ('1','3') 
                ----------------------------
                UNION ALL
                SELECT isnull(SUM(x.nilai),0) as nilai FROM trdspp x
                INNER JOIN trhspp y 
                ON x.no_spp=y.no_spp AND x.kd_skpd=y.kd_skpd
                WHERE
                    x.kd_sub_kegiatan = a.kd_sub_kegiatan
                AND x.kd_skpd =a.kd_skpd
                -------------------------
                AND y.jns_spp IN ('4','5','6')
                ------------------------
                AND (sp2d_batal IS NULL or sp2d_batal ='' or sp2d_batal='0')
                UNION ALL
                SELECT isnull(SUM(nilai),0) as nilai FROM trdtagih t 
                INNER JOIN trhtagih u 
                ON t.no_bukti=u.no_bukti AND t.kd_skpd=u.kd_skpd
                WHERE 
                t.kd_sub_kegiatan = a.kd_sub_kegiatan
                AND u.kd_skpd = a.kd_skpd
                AND u.no_bukti 
                NOT IN (select no_tagih FROM trhspp WHERE kd_skpd=a.kd_skpd )
                ------------------------
                union all
                select isnull(sum(f.rupiah),0) [nilai] from trhkasin_pkd e join trdkasin_pkd f 
                on e.no_sts=f.no_sts and e.kd_skpd=f.kd_skpd where e.no_sp2d like '%TU/BL%' 
                and f.kd_sub_kegiatan=a.kd_sub_kegiatan and e.jns_cp='3' group by f.kd_sub_kegiatan
                union all
                select isnull(sum(nilai),0) [nilai] from tr_panjar where kd_sub_kegiatan=a.kd_sub_kegiatan
                and no_panjar not in 
                    (select h.no_panjar FROM trdtransout g
                      JOIN trhtransout h ON g.no_bukti = h.no_bukti AND g.kd_skpd = h.kd_skpd
                      WHERE g.kd_sub_kegiatan = a.kd_sub_kegiatan AND g.kd_skpd = a.kd_skpd 
                      AND h.jns_spp in ('1','3') and h.panjar='1') 	and jns='1'						  
                )r 
            ) z) as transaksi
        FROM trskpd a where a.kd_skpd=? AND a.status_sub_kegiatan='1' and a.jns_ang=? and a.kd_sub_kegiatan = ? order by a.kd_sub_kegiatan",[$kd_skpd,$jns_ang,$kd_skpd,$jns_ang,$kd_sub_kegiatan]);
        $anggaran = 0;
        $transaksi = 0;
     
            foreach($sisa as $s){
                $anggaran += $s->anggaran;
                $transaksi= $s->transaksi;
            }
            $hasil = $anggaran - $transaksi;
            // dd($hasil);
            // return;
            $data = ['sisa' => $hasil,];
     
        return response()->json($data);
    }


    public function sisaBank(){
        $kd_skpd = Auth::user()->kd_skpd;
        $data1 = DB::table('tr_setorsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode');

        $data2 = DB::table('TRHINLAIN')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket', 'nilai as jumlah', DB::raw("'1' as jns"), 'kd_skpd as kode')->where(['pay' => 'BANK'])->unionAll($data1);

        $data3 = DB::table('tr_jpanjar as a')->join('tr_panjar as b', function ($join) {
            $join->on('a.no_panjar', '=', 'b.no_panjar');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.jns' => '2', 'a.kd_skpd' => $kd_skpd, 'b.pay' => 'BANK'])->select('a.tgl_kas as tgl', 'a.no_kas as bku', 'a.keterangan as ket', 'a.nilai as jumlah', DB::raw("'1' as jns"), 'a.kd_skpd as kode')->unionAll($data2);

        $data4 = DB::table('trhtrmpot as a')->join('trdtrmpot as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd, 'a.pay' => 'BANK'])->whereNotIn('jns_spp', ['1', '2', '3'])->groupBy('a.tgl_bukti', 'a.no_bukti', 'a.ket', 'a.kd_skpd')->select('a.tgl_bukti as tgl', 'a.no_bukti as bku', 'a.ket as ket', DB::raw("SUM(b.nilai) as jumlah"), DB::raw("'1' as jns"), 'a.kd_skpd as kode')->unionAll($data3);

        $data5 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
            $join->on('a.no_sts', '=', 'b.no_sts');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd, 'bank' => 'BNK', 'jns_trans' => '5'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data4);

        $joinsub = DB::table('trspmpot')->select('no_spm', DB::raw("SUM(nilai) as pot"))->groupBy('no_spm');
        $joinsub1 = DB::table('trhtrmpot as d')->join('trdtrmpot as e', function ($join) {
            $join->on('d.no_bukti', '=', 'e.no_bukti');
            $join->on('d.kd_skpd', '=', 'e.kd_skpd');
        })->where(['e.kd_skpd' => $kd_skpd, 'd.pay' => 'BANK'])->where('d.no_kas', '<>', '')->select('d.no_kas', DB::raw("SUM(e.nilai) as pot2"), 'd.kd_skpd')->groupBy('d.no_kas', 'd.kd_skpd');

        $data6 = DB::table('trhtransout as a')->join('trhsp2d as b', function ($join) {
            $join->on('a.no_sp2d', '=', 'b.no_sp2d');
        })->leftJoinSub($joinsub, 'c', function ($join) {
            $join->on('b.no_spm', '=', 'c.no_spm');
        })->leftJoinSub($joinsub1, 'f', function ($join) {
            $join->on('f.no_kas', '=', 'a.no_bukti');
            $join->on('f.kd_skpd', '=', 'a.kd_skpd');
        })->where(['pay' => 'BANK'])->where(function ($query) {
            $query->where('panjar', '<>', '1')->orWhereNull('panjar');
        })->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket', DB::raw("total-ISNULL(pot,0)-ISNULL(f.pot2,0) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data5);

        $data7 = DB::table('trhstrpot as a')->join('trdstrpot as b', function ($join) {
            $join->on('a.no_bukti', '=', 'b.no_bukti');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.kd_skpd' => $kd_skpd, 'a.pay' => 'BANK'])->groupBy('a.tgl_bukti', 'a.no_bukti', 'a.ket', 'a.kd_skpd')->select('a.tgl_bukti as tgl', 'a.no_bukti as bku', 'a.ket as ket', DB::raw("SUM(b.nilai) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data6);

        $data8 = DB::table('tr_ambilsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->unionAll($data7);

        $data9 = DB::table('trhoutlain')->select('tgl_bukti as tgl', 'no_bukti as bku', 'ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where(['pay' => 'BANK'])->unionAll($data8);

        $data10 = DB::table('tr_setorpelimpahan_bank')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd_sumber as kode')->unionAll($data9);

        $data11 = DB::table('tr_ambilsimpanan')->select('tgl_kas as tgl', 'no_kas as bku', 'keterangan as ket', 'nilai as jumlah', DB::raw("'2' as jns"), 'kd_skpd as kode')->where('status_drop', '!=', '1')->unionAll($data10);

        $data12 = DB::table('tr_panjar as a')->leftJoinSub($joinsub1, 'b', function ($join) {
            $join->on('a.no_panjar', '=', 'b.no_kas');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['a.pay' => 'BANK', 'a.kd_skpd' => $kd_skpd])->select('a.tgl_kas as tgl', 'a.no_panjar as bku', 'a.keterangan as ket', DB::raw("a.nilai-ISNULL(b.pot2,0) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data11);

        $data13 = DB::table('trhtrmpot as d')->join('trdtrmpot as e', function ($join) {
            $join->on('d.no_bukti', '=', 'e.no_bukti');
            $join->on('d.kd_skpd', '=', 'e.kd_skpd');
        })->where(['d.no_sp2d' => '2977/TU/2022', 'e.kd_skpd' => $kd_skpd, 'd.pay' => 'BANK'])->groupBy('d.tgl_bukti', 'd.no_bukti', 'd.ket', 'd.kd_skpd')->select('d.tgl_bukti as tgl', 'd.no_bukti as bku', 'd.ket as ket', DB::raw("SUM(e.nilai) as jumlah"), DB::raw("'1' as jns"), 'd.kd_skpd as kode')->unionAll($data12);

        $data14 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
            $join->on('a.no_sts', '=', 'b.no_sts');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['pot_khusus' => '0', 'a.kd_skpd' => $kd_skpd, 'bank' => 'BNK'])->whereNotIn('jns_trans', ['2', '4', '5'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'2' as jns"), 'a.kd_skpd as kode')->unionAll($data13);

        $data15 = DB::table('trhkasin_pkd as a')->join('trdkasin_pkd as b', function ($join) {
            $join->on('a.no_sts', '=', 'b.no_sts');
            $join->on('a.kd_skpd', '=', 'b.kd_skpd');
        })->where(['jns_trans' => '5', 'a.kd_skpd' => $kd_skpd, 'bank' => 'BNK'])->groupBy('a.tgl_sts', 'a.no_sts', 'a.keterangan', 'a.kd_skpd')->select('a.tgl_sts as tgl', 'a.no_sts as bku', 'a.keterangan as ket', DB::raw("SUM(b.rupiah) as jumlah"), DB::raw("'1' as jns"), 'a.kd_skpd as kode')->unionAll($data14);

        $data = DB::table(DB::raw("({$data15->toSql()}) AS sub"))
            ->select(DB::raw("SUM(CASE WHEN jns=1 THEN jumlah ELSE 0 END)-SUM(CASE WHEN jns=2 THEN jumlah ELSE 0 END) as sisa"))
            ->mergeBindings($data15)
            ->whereRaw("kode = '$kd_skpd'")
            ->first();
                    
        return response()->json($data->sisa);
    }

    public function hapus(Request $request)
    {
        $no_panjar = $request->no_panjar;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::table('tr_panjar_cmsbank')->where(['no_panjar' => $no_panjar, 'kd_skpd' => $kd_skpd])->delete();

            DB::table('tr_panjar_transfercms')->where(['no_bukti' => $no_panjar, 'kd_skpd' => $kd_skpd])->delete();

            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function update(Request $request)
    {
        $data = $request->data;
        $kd_skpd = Auth::user()->kd_skpd;

        // dd($data);
        // return;
        DB::beginTransaction();
        try {
            DB::table('tr_panjar_cmsbank')->where(['no_panjar' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();
            DB::table('tr_panjar_cmsbank')->insert([
                'no_kas' => $data['no_bukti'],
                'tgl_kas' => $data['tgl_voucher'],
                'no_panjar' => $data['no_bukti'],
                'tgl_panjar' => $data['tgl_voucher'],
                'kd_skpd' => $data['kd_skpd'],
                'pengguna' => '',
                'nilai' => $data['total_belanja'],
                'keterangan' => $data['keterangan'],
                'pay' => $data['pembayaran'],
                'rek_bank' => '',
                'kd_sub_kegiatan' => $data['kd_sub_kegiatan'],
                'status' => '0',
                'jns' => '2',
                'no_panjar_lalu' => $data['no_tpanjar'],
                'rekening_awal' => $data['rek_awal'],
                'ket_tujuan' => $data['ket_tujuan'],
                'status_validasi' => '0',
                'status_upload' => '0',
            ]);
            DB::table('tr_panjar_transfercms')->where(['no_bukti' => $data['no_bukti'], 'kd_skpd' => $kd_skpd])->delete();

            if (isset($data['rincian_rek_tujuan'])) {
                DB::table('tr_panjar_transfercms')->insert(array_map(function ($value) use ($kd_skpd, $data) {
                    return [
                        'no_bukti' => $value['no_bukti'],
                        'tgl_bukti' => $data['tgl_voucher'],
                        'rekening_awal' => $data['rek_awal'],
                        'nm_rekening_tujuan' => $value['nm_rekening_tujuan'],
                        'rekening_tujuan' => $value['rekening_tujuan'],
                        'bank_tujuan' => $value['bank_tujuan'],
                        'kd_skpd' => $kd_skpd,
                        'nilai' => $value['nilai'],
                    ];
                }, $data['rincian_rek_tujuan']));
            }

            DB::commit();
            return response()->json([
                'message' => '1',
                'no_kas' => $data['no_kas']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function noPanjar(){
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT * FROM tr_panjar WHERE status='1' AND jns = '1' AND kd_skpd=? order by no_panjar ", [$kd_skpd]);

        //$data = ['no_panjar' => $no,];

        // dd($data);
        // return;
        return response()->json($data);
    }
}
