<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class UploadPendapatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['ms_skpd'] = DB::table('ms_skpd')->select("kd_skpd", "nm_skpd")->get();

        return view('penatausahaan.upload_pendapatan.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $kd_skpd = $request->kd_skpd;

            $ms_kegiatan = DB::table("trdrka")->select("kd_sub_kegiatan")
                ->where(["nm_sub_kegiatan" => 'pendapatan', "kd_skpd" => $kd_skpd])
                ->groupBy("kd_sub_kegiatan")
                ->first();

            $kd_sub_kegiatan = $ms_kegiatan->kd_sub_kegiatan;

            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
            ]);

            $sheets = Excel::toArray([], $request->file('file'));



            //============================================== Validasi Double
            $rows = $sheets[1];

            $noList = [];

            for ($i = 1; $i < count($rows); $i++) {
                $no_terima = trim(($rows[$i][1] ?? '')); // kolom A = no_terimaa
                if ($no_terima !== '') $noList[] = $no_terima;
            }

            $counts = array_count_values($noList);

            $duplicates = array_keys(array_filter($counts, fn($c) => $c > 1));

            if (!empty($duplicates)) {
                // STOP sebelum simpan

                return response()->json(['message' => 'Ada no_terima duplikat di Sheet Penerimaan Tahun ini Excel: ' . implode(', ', $duplicates)], 400);
            }

            //==========================

            $rows = $sheets[2];

            $noList = [];

            for ($i = 1; $i < count($rows); $i++) {
                $no_terima  = trim($rows[$i][2] ?? '');

                if ($no_terima !== '') $noList[] = $no_terima;
            }

            $counts = array_count_values($noList);

            $duplicates = array_keys(array_filter($counts, fn($c) => $c > 1));

            if (!empty($duplicates)) {
                return response()->json(['message' => 'Ada no_terima duplikat di Sheet Penyetoran Tahun ini Excel: ' . implode(', ', $duplicates)], 400);
            }

            //============================================== End Validasi Double


            //================================================= Penerimaan Tahun ini
            $rows = $sheets[1];


            for ($i = 1; $i < count($rows); $i++) {

                $no_terima  = trim($rows[$i][1] ?? '');

                $tglRaw  = trim($rows[$i][2] ?? null);
                if ($tglRaw) {
                    if (is_numeric($tglRaw)) {
                        $tgl_terima = Carbon::instance(ExcelDate::excelToDateTimeObject($tglRaw))->format('Y-m-d');
                    } else {
                        $tgl_terima = Carbon::parse($tglRaw)->format('Y-m-d');
                    }
                } else {
                    $tgl_terima = null;
                }

                $kd_rek6  = str_replace('.', '', trim($rows[$i][8] ?? ''));
                $kd_rek_lo  = str_replace('.', '', trim($rows[$i][9] ?? ''));
                $nilai = preg_replace('/[^0-9.]/', '', $rows[$i][10] ?? 0);
                $keterangan  = str_replace('.', '', trim($rows[$i][11] ?? ''));
                $jns_pembayaran  = str_replace('.', '', trim($rows[$i][18] ?? ''));

                DB::beginTransaction();

                if ($no_terima != "") {
                    DB::table("excel_terima")->where("no_terima", $no_terima)->delete();

                    DB::table("excel_terima")->insert([
                        'no_terima' => $no_terima,
                        'tgl_terima' => $tgl_terima,
                        'kd_skpd' => $kd_skpd,
                        'kd_sub_kegiatan' => $kd_sub_kegiatan,
                        'kd_rek6' => $kd_rek6,
                        'kd_rek_lo' => $kd_rek_lo,
                        'nilai' => $nilai,
                        'keterangan' => $keterangan,
                        'jns_pembayaran' => $jns_pembayaran,
                        'created_at' => now(),
                    ]);


                    DB::table("tr_terima")->where("no_terima", $no_terima)->delete();

                    DB::table("tr_terima")->insert([
                        'no_terima' => $no_terima,
                        'tgl_terima' => $tgl_terima,
                        "sts_tetap" => 0,
                        'kd_skpd' => $kd_skpd,
                        'kd_sub_kegiatan' => $kd_sub_kegiatan,
                        'kd_rek6' => $kd_rek6,
                        'kd_rek_lo' => $kd_rek_lo,
                        'nilai' => $nilai,
                        'keterangan' => $keterangan,
                        'jenis' => 1,
                        'user_name' => Auth::user()->nama,
                        'sumber' => 1,
                        'kunci' => 1,
                        'status_setor' => 'Dengan Setor',
                        'jns_pembayaran' => $jns_pembayaran,
                    ]);

                    DB::commit();
                }
            }


            //================================================= End Penerimaan Tahun ini


            //================================================= Penyetoran Tahun ini
            $rows = $sheets[2];

            for ($i = 1; $i < count($rows); $i++) {
                $no_sts  = trim($rows[$i][1] ?? '');
                $no_terima  = trim($rows[$i][2] ?? '');
                $tglRaw  = trim($rows[$i][4] ?? null);
                if ($tglRaw) {
                    if (is_numeric($tglRaw)) {
                        $tgl_sts = Carbon::instance(ExcelDate::excelToDateTimeObject($tglRaw))->format('Y-m-d');
                    } else {
                        $tgl_sts = Carbon::parse($tglRaw)->format('Y-m-d');
                    }
                } else {
                    $tgl_sts = null;
                }

                $keterangan  = trim($rows[$i][5] ?? '');

                $total =  preg_replace('/[^0-9.]/', '', $rows[$i][6] ?? 0);

                if ($no_sts != "") {
                    DB::beginTransaction();

                    DB::table("excel_setor")->where("no_sts", $no_sts)->delete();
                    DB::table("excel_setor")->insert([
                        'no_sts' => $no_sts,
                        'no_terima' => $no_terima,
                        'kd_skpd' => $kd_skpd,
                        'tgl_sts' => $tgl_sts,
                        'keterangan' => $keterangan,
                        'total' => $total,
                        'kd_sub_kegiatan' => $kd_sub_kegiatan,
                        'created_at' => now(),
                    ]);

                    DB::table("trdkasin_pkd")->where("no_sts", $no_sts)->delete();
                    DB::table("trhkasin_pkd")->where("no_sts", $no_sts)->delete();

                    DB::commit();
                }
            }

            DB::beginTransaction();

            DB::statement(
                "INSERT into trdkasin_pkd (kd_skpd, no_sts, kd_rek6, rupiah, kd_sub_kegiatan, no_terima, sumber)
            select a.kd_skpd, a.no_sts, b.kd_rek6, b.nilai, a.kd_sub_kegiatan, a.no_terima,'-' from excel_setor a 
            join excel_terima b on a.no_terima=b.no_terima
            where no_sts not in (select no_sts from trhkasin_pkd)"
            );

            DB::statement(
                "INSERT into trhkasin_pkd (no_sts, kd_skpd, tgl_sts, keterangan, total, kd_sub_kegiatan, jns_trans, no_cek, pot_khusus, username_created, created_at)
            select a.no_sts, a.kd_skpd, a.tgl_sts, 
            (select STRING_AGG(CAST(keterangan AS VARCHAR(MAX)), ', ') from (
                SELECT no_sts, keterangan from excel_setor b 
                group by no_sts, keterangan 
            )as c where c.no_sts=a.no_sts) as keterangan,
            (select sum(b.rupiah) from trdkasin_pkd b where b.no_sts=a.no_sts) as total, a.kd_sub_kegiatan, '4' as jns_trans, '1' no_cek, '0' as pot_khusus, 
            :username as username_created, GETDATE()
            from excel_setor a where a.no_sts not in (select no_sts from trhkasin_pkd)
            group by a.no_sts, a.kd_skpd, a.tgl_sts, a.kd_sub_kegiatan",
                ['username' => Auth::user()->nama]
            );

            DB::commit();

            //================================================= End Penyetoran Tahun ini

            return response()->json(['message' => 'File Berhasil Diupload'], 200);
        } catch (\Throwable $th) {
            Log::error('Exception caught: ' . $th->getMessage(), [
                'exception' => get_class($th),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            DB::rollBack();
            return response()->json(['message' => 'Server Error !!!'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
