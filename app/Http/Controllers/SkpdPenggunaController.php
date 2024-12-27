<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeranRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SkpdPenggunaController extends Controller
{
    public function index()
    {
        return view('master.PenggunaSkpd.index');
    }

    public function loadData()
    {
        $data = DB::table('pengguna')->whereIn('role', ['1012', '1017'])->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("skpd_pengguna.edit", Crypt::encryptString($row->id)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $data = [
            'daftar_hak_akses' => DB::table('akses')
                ->where(['urutan_menu' => '1'])
                ->get(),
            'daftar_hak_akses1' => DB::table('akses')
                ->get(),
        ];

        return view('master.peran.create')->with($data);
    }

    public function store(PeranRequest $request)
    {
        $input              = $request->validated();
        $data['role']       = $input['role'];
        $data['nama_role']  = $input['nama_role'];
        $hak_akses          = $input['hak_akses'];
        $input = array_map('htmlentities', $data);
        $input_akses = array_map('htmlentities', $hak_akses);

        DB::beginTransaction();
        try {
            $id = DB::table('peran')
                ->insertGetId([
                    'role' => $input['role'],
                    'nama_role' => $input['nama_role'],
                ]);
            if (isset($input_akses)) {
                DB::table('akses_peran')
                    ->insert(array_map(function ($value) use ($id) {
                        return ['id_permission' => $value, 'id_role' => $id, "username_created" => Auth::user()->nama, "created_at" => date('Y-m-d H:i:s')];
                    }, $input_akses));
            }
            DB::commit();
            return redirect()->route('peran.index');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $id = Crypt::decryptString($id);
        $peran = DB::table('peran')->where('id', $id)->first();
        $data = [
            'daftar_hak_akses' => DB::table('akses_peran')
                ->join('akses', 'akses_peran.id_permission', '=', 'akses.id')
                ->where('akses_peran.id_role', $peran->id)
                ->get()
        ];

        return view('master.peran.show')->with($data);
    }

    public function edit($id)
    {
        $id             = Crypt::decryptString($id);
        $daftar_skpd    = DB::table('pengguna_skpd')
            ->select('kd_skpd')
            ->where('id', $id)
            ->get();
        $array = json_decode(json_encode($daftar_skpd), true);
        $array = array_column($array, "kd_skpd");
        $data = [
            // 'available_daftar_hak_akses' => json_decode(json_encode(DB::table('permission')->get()), true),
            'pengguna'      => DB::table('pengguna')->where('id', $id)->first(),
            'list_skpd'     => $array,
            'daftar_skpd'   => DB::table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->orderBy('kd_skpd')
                ->get(),
        ];
        return view('master.PenggunaSkpd.edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $skpd_akses = $input['skpd'];
        $input_akses = array_map('htmlentities', $skpd_akses);


        DB::beginTransaction();
        try {

            DB::table('pengguna_skpd')->where('id', $id)->delete();

            if (isset($input_akses)) {
                DB::table('pengguna_skpd')->insert(array_map(function ($value) use ($id) {
                    return [
                        'kd_skpd' => $value,
                        'id' => $id
                    ];
                }, $input_akses));
            }
            DB::commit();
            return redirect()->route('skpd_pengguna.index');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DB::table('peran')->where('id', $id)->delete();
            DB::table('akses_peran')->where('id_role', $id)->delete();

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
