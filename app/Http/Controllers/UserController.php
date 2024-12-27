<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        if (Gate::denies('akses')) {
            abort(401);
        }

        return view('master.user.index');
    }

    public function loadData()
    {
        $data = DB::table('pengguna as a')
            ->select('a.*')
            ->selectRaw("(select nm_skpd from ms_skpd as b where a.kd_skpd=b.kd_skpd) as nm_skpd,(select nama_role from peran where a.role=id) as jabatan")
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            // $btn = '<a href="' . route("user.show", Crypt::encryptString($row->id)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>';
            $btn = '<a href="' . route("user.edit", Crypt::encryptString($row->id)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="deleteData(\'' . $row->id . '\', \'' . Auth::user()->id . '\');" data-id="\'' . $row->id . '\'" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        if (Gate::denies('akses')) {
            abort(401);
        }

        $data = [
            'daftar_role' => DB::table('peran')->get(),
            'daftar_skpd' => DB::table('ms_skpd')->get()
        ];

        return view('master.user.create')->with($data);
    }

    public function store(UserRequest $request)
    {
        $input = array_map('htmlentities', $request->validated());
        DB::beginTransaction();
        try {

            $role =  $input['peran'];

            $no_bp = $input['kd_skpd'].'.0';

            $no_bpp = collect(DB::select("SELECT max(SUBSTRING(kd_bpp,24,1)+1) as no_akhir from pengguna where kd_skpd = ? ",[$input['kd_skpd']]))->first();

            $kd_bpp = $input['kd_skpd'].'.'.$no_bpp->no_akhir;

            if( $role == 1008 ){
                $id = DB::table('pengguna')->insertGetId([
                    'username' => $input['username'],
                    'password' => Hash::make($input['password']),
                    'nama' => $input['nama'],
                    'kd_skpd' => $input['kd_skpd'],
                    'is_admin' => $input['tipe'],
                    'status' => $input['status'],
                    'role' => $input['peran'],
                    'kd_bpp' => $kd_bpp,
                ]);
                DB::table('pengguna_peran')->insert([
                    'id_user' => $id,
                    'id_role' => $input['peran'],
                ]);
            } else if ($role == 1007){
                $id = DB::table('pengguna')->insertGetId([
                    'username' => $input['username'],
                    'password' => Hash::make($input['password']),
                    'nama' => $input['nama'],
                    'kd_skpd' => $input['kd_skpd'],
                    'is_admin' => $input['tipe'],
                    'status' => $input['status'],
                    'role' => $input['peran'],
                    'kd_bpp' => $no_bp,
                ]);
                DB::table('pengguna_peran')->insert([
                    'id_user' => $id,
                    'id_role' => $input['peran'],
                ]);
            } else {
                $id = DB::table('pengguna')->insertGetId([
                    'username' => $input['username'],
                    'password' => Hash::make($input['password']),
                    'nama' => $input['nama'],
                    'kd_skpd' => $input['kd_skpd'],
                    'is_admin' => $input['tipe'],
                    'status' => $input['status'],
                    'role' => $input['peran'],
                ]);
                DB::table('pengguna_peran')->insert([
                    'id_user' => $id,
                    'id_role' => $input['peran'],
                ]);
            }
            DB::commit();
            return redirect()->route('user.index');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        if (Gate::denies('akses')) {
            abort(401);
        }
        $id = Crypt::decryptString($id);
        $user = DB::table('pengguna')->where('id', $id)->first();
        $data = [
            'user' => $user,
            'role' => DB::table('peran')->where('id', $user->role)->first()
        ];

        return view('master.user.show')->with($data);
    }

    public function edit($id)
    {
        if (Gate::denies('akses')) {
            abort(401);
        }
        $id = Crypt::decryptString($id);
        $user = DB::table('pengguna')->where('id', $id)->first();
        $data = [
            'user' => $user,
            'daftar_role' => DB::table('peran')->get(),
            'daftar_skpd' => DB::table('ms_skpd')->get(),
            'role' => DB::table('peran')->where('id', $user->role)->first(),
        ];

        return view('master.user.edit')->with($data);
    }

    public function update(UserRequest $request, $id)
    {
        $input = array_map('htmlentities', $request->validated());

        $password_lama = DB::table('pengguna')
        ->where(['id' => $id])
        ->first()
        ->password;

        if (!Hash::check($input['password_lama'], $password_lama) && $input['password_lama'] != '') {
            return back()->with("error", "Password lama tidak sesuai");
        }

        DB::beginTransaction();
        try {
            if ($input['password'] == '') {
                DB::table('pengguna')
                    ->where('id', $id)
                    ->update([
                        'username' => $input['username'],
                        'nama' => $input['nama'],
                        'kd_skpd' => $input['kd_skpd'],
                        'is_admin' => $input['tipe'],
                        'status' => $input['status'],
                        'role' => $input['peran'],
                        // 'jabatan' => $input['jabatan'],
                    ]);
            } else {
                DB::table('pengguna')
                    ->where('id', $id)
                    ->update([
                        'username' => $input['username'],
                        'password' => Hash::make($input['password']),
                        'nama' => $input['nama'],
                        'kd_skpd' => $input['kd_skpd'],
                        'is_admin' => $input['tipe'],
                        'status' => $input['status'],
                        'role' => $input['peran'],
                        // 'jabatan' => $input['jabatan'],
                    ]);
            }
            DB::table('pengguna_peran')->where(['id_user' => $id])->update([
                'id_role' => $input['peran'],
            ]);


            DB::commit();
            return redirect()->route('user.index');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
        // return redirect()->route('user.index');
    }

    public function destroy($id)
    {
        if (Gate::denies('akses')) {
            return response()->json([
                'message' => '403',
            ]);
        }

        DB::beginTransaction();
        try {
            DB::table('pengguna')->where('id', $id)->delete();
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
