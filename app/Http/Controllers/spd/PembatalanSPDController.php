<?php

namespace App\Http\Controllers\spd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class PembatalanSPDController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('penatausahaan.spd.pembatalan_spd.index');
    }

    public function loadData()
    {
        $id = Auth::user()->id;
        $skpd = Auth::user()->kd_skpd;

        $data = DB::table('trhspd as a')->Select(
            'a.*',
            DB::raw("(select TOP 1 nama from ms_ttd b where a.kd_bkeluar=b.nip ) as nama"),
            DB::raw("case when jns_beban='5' then 'BELANJA' else 'PEMBIAYAAN' end AS nm_beban"),
            DB::raw("(select nama from tb_status_angkas where a.jns_ang=status_kunci) as nm_angkas"),
            DB::raw("(select count(no_spd) from trhspp where a.no_spd=no_spd) as total")
        )
            ->whereIn('a.jns_beban', [5, 6])
            ->where(function ($query) use ($skpd) {
                if (Auth::user()->is_admin == 2) {
                    $query->where(['a.kd_skpd' => $skpd]);
                }
            })
            ->groupBy([
                'a.no_spd', 'a.tgl_spd', 'a.kd_skpd', 'a.nm_skpd', 'a.jns_beban',
                'a.no_dpa', 'a.bulan_awal', 'a.bulan_akhir', 'a.kd_bkeluar', 'a.triwulan', 'a.klain',
                'a.username', 'a.tglupdate', 'a.st', 'a.status', 'a.total', 'revisi_ke', 'jns_ang', 'jns_angkas'
            ])
            ->orderBy('no_spd')->orderBy('tgl_spd')->orderBy('kd_skpd')->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            if($row->total > 0) {
                if ($row->status == '1') {
                    $btn = '<font color="green"><i class="fa fa-check-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="SPD Aktif dan Sudah Digunakan"></i></font>';
                }else{
                    $btn = '<font color="red"><i class="fa fa-check-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="SPD Tidak Aktif dan Sudah Digunakan"></i></font>';
                }
                
            } else {
                if ($row->status == '1') {
                    $btn = '<div class="form-check form-switch form-switch-lg">
                    <input type="checkbox" class="form-check-input" onChange="ubahStatus(\'' . $row->no_spd . '\', \'' . $row->status . '\');"
                         checked></div>';
                } else {
                    $btn = '<div class="form-check form-switch form-switch-lg">
                    <input type="checkbox" class="form-check-input" onChange="ubahStatus(\'' . $row->no_spd . '\', \'' . $row->status . '\');"
                        ></div>';
                }
            } 
            return $btn;
        })->rawColumns(['aksi'])->make(true);
        return view('penatausahaan.spd.pembatalan_spd.index');
    }

    public function updateStatus(Request $request)
    {
        $nospd = $request->no_spd;
        $status = $request->status;

        DB::beginTransaction();
        try {
            if ($status == '0') {
                DB::table('trhspd')->where(['no_spd' => $nospd])
                    ->update([
                        'status' => '1',
                    ]);
                DB::commit();
                return response()->json([
                    'message' => '1',
                ]);
            } else {
                DB::table('trhspd')->where(['no_spd' => $nospd])
                    ->update([
                        'status' => '0',
                    ]);
                DB::commit();
                return response()->json([
                    'message' => '2',
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
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
        //
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
