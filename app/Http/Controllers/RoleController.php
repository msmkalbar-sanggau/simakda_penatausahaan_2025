<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeranRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        return view('master.peran.index');
    }

    public function loadData()
    {
        $data = DB::table('peran')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("peran.show", Crypt::encryptString($row->id)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>';
            $btn .= '<a href="' . route("peran.edit", Crypt::encryptString($row->id)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="deleteData(\'' . $row->id . '\',\'' . Auth::user()->role . '\');" data-id="\'' . $row->id . '\'" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
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
        $input = $request->validated();
        $data['role'] = $input['role'];
        $data['nama_role'] = $input['nama_role'];
        $hak_akses = $input['hak_akses'];
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
        $id = Crypt::decryptString($id);
        $peran = DB::table('peran')->where('id', $id)->first();
        $daftar_hak_akses = DB::table('akses_peran')
            ->select('akses_peran.id_permission')
            ->join('akses', 'akses_peran.id_permission', '=', 'akses.id')
            ->where('id_role', $peran->id)
            ->get();
        $array = json_decode(json_encode($daftar_hak_akses), true);
        $array = array_column($array, "id_permission");
        $data = [
            // 'available_daftar_hak_akses' => json_decode(json_encode(DB::table('permission')->get()), true),
            'peran' => DB::table('peran')->where('id', $id)->first(),
            'hak_akses1' => $array,
            'daftar_hak_akses' => DB::table('akses')
                ->where(['urutan_menu' => '1'])
                ->orderBy('id')
                ->get(),
            'daftar_hak_akses1' => DB::table('akses')
                ->orderBy('urut_akses')
                ->orderBy('urut_akses2')
                ->get(),
        ];
        return view('master.peran.edit')->with($data);
    }

    public function update(PeranRequest $request, $id)
    {
        $input = $request->validated();
        $data['role'] = $input['role'];
        $data['nama_role'] = $input['nama_role'];
        $hak_akses = $input['hak_akses'];
        $input = array_map('htmlentities', $data);
        $input_akses = array_map('htmlentities', $hak_akses);

        DB::beginTransaction();
        try {
            DB::table('peran')->where('id', $id)->update([
                'role' => $input['role'],
                'nama_role' => $input['nama_role'],
            ]);

            DB::table('akses_peran')->where('id_role', $id)->delete();

            if (isset($input_akses)) {
                DB::table('akses_peran')->insert(array_map(function ($value) use ($id) {
                    return [
                        'id_permission' => $value,
                        'id_role' => $id,
                        "username_created" => Auth::user()->nama,
                        "created_at" => date('Y-m-d H:i:s')
                    ];
                }, $input_akses));
            }
            DB::commit();
            return redirect()->route('peran.index');
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
