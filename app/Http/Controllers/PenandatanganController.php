<?php

namespace App\Http\Controllers;

use App\Http\Requests\TandatanganRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;

class PenandatanganController extends Controller
{
    public function index()
    {
        if (Gate::denies('akses')) {
            abort(401);
        }
        return view('master.penandatangan.index');
    }

    public function loadData()
    {
        if(Auth::user()->is_admin==2){
            $kd_skpd = Auth::user()->kd_skpd;
            $data = DB::table('ms_ttd AS a')
                    ->where('a.kd_skpd', $kd_skpd)
                    ->get();
        }else{
            $data = DB::table('ms_ttd AS a')
                    ->get();
        }
        


        
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn    = '<a href="' . route("tandatangan.edit", Crypt::encryptString($row->id)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn   .= '<a href="javascript:void(0);" onclick="deleteData(\'' . $row->id . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        if (Gate::denies('akses')) {
            abort(401);
        }

        if(Auth::user()->is_admin==2){
            $kd_skpd = Auth::user()->kd_skpd;
            $data = [
                'skpd' => DB::table('ms_skpd')->select('*')->where('kd_skpd', $kd_skpd)->first()
            ];
        }else{
            $data = [
                'skpd' => DB::table('ms_skpd')->select('*')->get()
            ];
        }
        

        return view('master.penandatangan.create')->with($data);
    }

    public function cariSkpd()
    {   
        $type       = Auth::user()->is_admin;
        $kd_skpd    = Auth::user()->kd_skpd;

        if ($type=='1'){
                $data   = DB::table('ms_skpd')
                            ->select('kd_skpd', 'nm_skpd')
                            ->orderBy('kd_skpd')
                            ->get();
        }else{
                $data   = DB::table('ms_skpd')
                            ->where('kd_skpd',$kd_skpd)
                            ->select('kd_skpd', 'nm_skpd')
                            ->get();
        }

        return response()->json($data);
    }

    public function store(TandatanganRequest $request)
    {
        $input = array_map('htmlentities', $request->validated());
        DB::table('ms_ttd')->insert([
            'nip'       => $input['nip'],
            'nama'      => $input['nama'],
            'jabatan'   => $input['jabatan'],
            'pangkat'   => $input['pangkat'],
            'kd_skpd'   => $input['kd_skpd'],
            'kode'      => $input['kode'],
        ]);

        return redirect()->route('tandatangan.index');
    }


    public function edit($id)
    {   
        if (Gate::denies('akses')) {
            abort(401);
        }

        $id = Crypt::decryptString($id);
        $data_awal = DB::table('ms_ttd')
            ->where(['id' => $id])
            ->first();

        $data = [
            'data_tandatangan' => $data_awal,
            'skpd' => DB::table('ms_skpd')->select('nm_skpd')->where('kd_skpd', $data_awal->kd_skpd)->first(),
        ];

        return view('master.penandatangan.edit')->with($data);
    }

    public function update(TandatanganRequest $request, $id)
    {
        $id = Crypt::decryptString($id);
        $input = array_map('htmlentities', $request->validated());
        DB::table('ms_ttd')->where('id', $id)->update([
            'nip'           => $input['nip'],
            'nama'          => $input['nama'],
            'jabatan'       => $input['jabatan'],
            'pangkat'       => $input['pangkat'],
            'kode'          => $input['kode'],
            'kd_skpd' => $input['kd_skpd']
        ]);

        return redirect()->route('tandatangan.index');
    }



    public function hapus(Request $request)
    {   
        if (Gate::denies('akses')) {
            abort(401);
        }
        
        $id = $request->id;
        $data = DB::table('ms_ttd')->where('id', $id)->delete();
        if ($data) {
            return response()->json([
                'message' => '1'
            ]);
        } else {
            return response()->json([
                'message' => '0'
            ]);
        }
    }
}
