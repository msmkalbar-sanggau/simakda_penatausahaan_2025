<?php

namespace App\Http\Controllers\Skpd\PanjarCMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ValidasiPanjarCMSController extends Controller
{
    public function index()
    {
        $data = [
            'sisa_bank' => sisa_bank()
        ];
        return view('skpd.validasi_panjar_cms.index')->with($data);
    }

    public function loadData(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT a.*,c.no_upload FROM tr_panjar_cmsbank a
        left join trdupload_cmsbank_panjar c on a.no_kas = c.no_bukti and a.kd_skpd = c.kd_skpd
        where a.kd_skpd=? and a.status_upload='1' and a.status_validasi='0'
        order by cast(a.no_kas as int),a.kd_skpd", [$kd_skpd]);

        return Datatables::of($data)->addIndexColumn()->make(true);
    }

    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'daftar_transaksi' => DB::select("SELECT a.*,c.no_upload FROM tr_panjar_cmsbank a
        left join trdupload_cmsbank_panjar c on a.no_kas = c.no_bukti and a.kd_skpd = c.kd_skpd
        where a.kd_skpd=? and a.status_upload='1' and a.status_validasi='0'
        order by cast(a.no_kas as int),a.kd_skpd", [$kd_skpd]),
            'sisa_bank' => sisa_bank()
        ];

        return view('skpd.validasi_panjar_cms.create')->with($data);
    }

    public function prosesValidasi(Request $request)
    {
        $rincian_data = $request->rincian_data;
        $tanggal_validasi = $request->tanggal_validasi;
        $kd_skpd = Auth::user()->kd_skpd;

        DB::beginTransaction();
        try {
            $nomor1 = DB::table('trvalidasi_cmsbank')->select('no_validasi as nomor', DB::raw("'Urut Validasi cms' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd]);
            $nomor2 = DB::table('trvalidasi_cmsbank_panjar')->select('no_validasi as nomor', DB::raw("'Urut Validasi cms Panjar' as ket"), 'kd_skpd')->where(['kd_skpd' => $kd_skpd])->unionAll($nomor1);
            $nomor = DB::table(DB::raw("({$nomor2->toSql()}) AS sub"))
                ->select(DB::raw("case when max(nomor+1) is null then 1 else max(nomor+1) end as nomor"))
                ->mergeBindings($nomor2)
                ->first();

            $no_validasi = $nomor->nomor;
            $no_bku = no_urut($kd_skpd);
            $bku = $no_bku - 1;

            DB::table('trvalidasi_cmsbank_panjar')
                ->where(['kd_bp' => $kd_skpd, 'no_validasi' => $no_validasi])
                ->delete();

            foreach ($rincian_data as $data => $value) {
                $data = [
                    'no_voucher' => $rincian_data[$data]['no_kas'],
                    'tgl_bukti' => $rincian_data[$data]['tgl_kas'],
                    'no_upload' => $rincian_data[$data]['no_upload'],
                    'rekening_awal' => isset($rincian_data[$data]['rekening_awal']) ? $rincian_data[$data]['rekening_awal'] : '',
                    'nm_rekening_tujuan' => isset($rincian_data[$data]['nm_rekening_tujuan']) ? $rincian_data[$data]['nm_rekening_tujuan'] : '',
                    'rekening_tujuan' => isset($rincian_data[$data]['rekening_tujuan']) ? $rincian_data[$data]['rekening_tujuan'] : '',
                    'bank_tujuan' => isset($rincian_data[$data]['bank_tujuan']) ? $rincian_data[$data]['bank_tujuan'] : '',
                    'ket_tujuan' => isset($rincian_data[$data]['ket_tujuan']) ? $rincian_data[$data]['ket_tujuan'] : '',
                    'nilai' => $rincian_data[$data]['nilai'],
                    'kd_skpd' => $rincian_data[$data]['kd_skpd'],
                    'kd_bp' => $rincian_data[$data]['kd_skpd'],
                    'status_upload' => $rincian_data[$data]['status_upload'],
                    'tgl_validasi' => $tanggal_validasi,
                    'status_validasi' => '1',
                    'no_validasi' => $no_validasi,
                    'no_bukti' => ++$bku,
                ];
                DB::table('trvalidasi_cmsbank_panjar')->insert($data);
            }

            DB::update("UPDATE
                            tr_panjar_cmsbank
                            SET tr_panjar_cmsbank.status_validasi = Table_B.status_validasi,
                                tr_panjar_cmsbank.tgl_validasi = Table_B.tgl_validasi
                        FROM tr_panjar_cmsbank
                        INNER JOIN (select a.no_voucher [no_bukti],a.kd_skpd,a.kd_bp,a.tgl_validasi,a.status_validasi from trvalidasi_cmsbank_panjar a
                        where a.kd_bp=? and no_validasi=?) AS Table_B ON tr_panjar_cmsbank.no_kas = Table_B.no_bukti AND tr_panjar_cmsbank.kd_skpd = Table_B.kd_skpd
                        where tr_panjar_cmsbank.kd_skpd=?", [$kd_skpd, $no_validasi, $kd_skpd]);

            DB::insert("INSERT INTO tr_panjar (no_kas,tgl_kas,no_panjar,tgl_panjar,kd_skpd,pengguna,nilai,keterangan,pay,rek_bank,kd_sub_kegiatan,status,jns,no_panjar_lalu)
                                    SELECT b.no_bukti,a.tgl_kas,b.no_bukti,a.tgl_panjar,a.kd_skpd,a.pengguna,a.nilai,a.keterangan,a.pay,a.rek_bank,a.kd_sub_kegiatan,a.status,a.jns,
                                    (case when a.jns='2' then no_panjar_lalu else b.no_bukti end) no_panjar_lalu
                                    FROM tr_panjar_cmsbank a left join trvalidasi_cmsbank_panjar b on b.no_voucher=a.no_kas and a.kd_skpd=b.kd_skpd
                                    WHERE b.no_validasi=? and b.kd_bp=?", [$no_validasi, $kd_skpd]);

            DB::insert("INSERT INTO trhtrmpot (no_bukti, tgl_bukti, ket, username, tgl_update, kd_skpd, nm_skpd, no_sp2d, nilai, npwp, jns_spp,
                                            status, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, nmrekan, pimpinan, alamat, ebilling,
                                            rekening_tujuan, nm_rekening_tujuan, no_kas,pay)
                                            SELECT cast(c.no_bukti as int)+1 as no_bukti, c.tgl_validasi as tgl_bukti, d.ket, d.username, d.tgl_update, d.kd_skpd, d.nm_skpd, d.no_sp2d, d.nilai, d.npwp, d.jns_spp, d.status, d.kd_sub_kegiatan, d.nm_sub_kegiatan, d.kd_rek6, d.nm_rek6, d.nmrekan, d.pimpinan, d.alamat, d.ebilling, d.rekening_tujuan, d.nm_rekening_tujuan, c.no_bukti, 'BANK'
                                            FROM trhtrmpot_cmsbank d JOIN tr_panjar_cmsbank a on d.no_voucher=a.no_panjar and a.kd_skpd=d.kd_skpd
                                            LEFT JOIN trvalidasi_cmsbank_panjar c on c.no_voucher=a.no_panjar and a.kd_skpd=c.kd_skpd
                                            WHERE c.no_validasi=? and c.kd_skpd=?", [$no_validasi, $kd_skpd]);

            DB::insert("INSERT INTO trdtrmpot (no_bukti, kd_rek6, nm_rek6, nilai, kd_skpd, kd_rek_trans,ebilling)
                                        SELECT cast(c.no_bukti as int)+1 as no_bukti, b.kd_rek6, b.nm_rek6, b.nilai, b.kd_skpd, b.kd_rek_trans,b.ebilling
                                        FROM trhtrmpot_cmsbank d inner join trdtrmpot_cmsbank b on b.no_bukti=d.no_bukti and b.kd_skpd=d.kd_skpd
                                        LEFT JOIN tr_panjar_cmsbank a on d.no_voucher=a.no_panjar and a.kd_skpd=d.kd_skpd
                                        LEFT JOIN trvalidasi_cmsbank_panjar c on c.no_voucher=a.no_panjar and a.kd_skpd=c.kd_skpd
                                        WHERE c.no_validasi=? and c.kd_skpd=?", [$no_validasi, $kd_skpd]);

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

    public function dataTransaksi(Request $request)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $no_kas = $request->no_kas;

        $no_bukti = array();
        if (!empty($no_kas)) {
            foreach ($no_kas as $voucher) {
                $no_bukti[] = $voucher['no_kas'];
            }
        } else {
            $no_bukti[] = '';
        }

        $data = DB::table('tr_panjar_cmsbank as a')
            ->leftJoin('trdupload_cmsbank_panjar as c', function ($query) {
                $query->on('a.no_kas', '=', 'c.no_bukti');
                $query->on('a.kd_skpd', '=', 'c.kd_skpd');
            })
            ->where(['a.kd_skpd' => $kd_skpd, 'a.status_upload' => '1', 'a.status_validasi' => '0'])
            ->orderByRaw("cast(a.no_kas as int),a.kd_skpd")
            ->whereNotIn('a.no_kas', $no_bukti)
            ->get();

        return response()->json($data);
    }

    public function draftValidasi()
    {
        $kd_skpd = Auth::user()->kd_skpd;

        $data = DB::select("SELECT a.*,c.no_upload ,d.no_bukti,d.tgl_bukti,d.no_voucher,d.tgl_validasi FROM tr_panjar_cmsbank a left join trdupload_cmsbank_panjar c on a.no_kas = c.no_bukti and a.kd_skpd = c.kd_skpd left join trvalidasi_cmsbank_panjar d on a.no_kas=d.no_voucher and a.kd_skpd = d.kd_skpd and a.tgl_kas=d.tgl_bukti where a.kd_skpd=? and a.status_upload='1' and a.status_validasi='1' order by cast(a.no_kas as int),a.kd_skpd", [$kd_skpd]);

        return Datatables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="javascript:void(0);" onclick="batalValidasi(\'' . $row->no_voucher . '\',\'' . $row->no_bukti . '\',\'' . $row->kd_skpd . '\',\'' . $row->tgl_kas . '\');" class="btn btn-danger btn-sm" style="margin-right:4px"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function batalValidasi(Request $request)
    {
        $no_voucher = $request->no_voucher;
        $no_bukti = $request->no_bukti;
        $kd_skpd = $request->kd_skpd;
        $tgl_kas = $request->tgl_kas;


        DB::beginTransaction();
        try {
            DB::table('trvalidasi_cmsbank_panjar')
                ->where(['no_bukti' => $no_bukti, 'no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::table('tr_panjar')
                ->where(['no_kas' => $no_bukti, 'tgl_kas' => $tgl_kas, 'kd_skpd' => $kd_skpd])
                ->delete();

            DB::update("update tr_panjar_cmsbank set status_validasi='0', tgl_validasi='' where no_kas=? and kd_skpd=?", [$no_voucher, $kd_skpd]);

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
}
