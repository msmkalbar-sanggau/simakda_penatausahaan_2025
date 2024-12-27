<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengumumanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PengumumanController extends Controller
{
    public function index()
    {
        return view('master.pengumuman.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('ms_pengumuman')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("pengumuman.show", Crypt::encryptString($row->id)) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>';
            $btn .= '<a href="' . route("pengumuman.edit", Crypt::encryptString($row->id)) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="deleteData(\'' . $row->id . '\', \'' . $row->file . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        return view('master.pengumuman.create');
    }

    public function store(PengumumanRequest $request)
    {

        $input = array_map('htmlentities', $request->validated());
        $file = $request->file('dokumen');
        if($file !=null || $file !=''){
            DB::table('ms_pengumuman')
            ->insert([
                'judul'     => $input['judul'],
                'isi'       => $input['isi'],
                'file'      => "/".$file->getClientOriginalName(),
                'tanggal'   => $input['tanggal'],
                'status'    => $input['status'],
                'aktif'     => $input['aktif'],
            ]);

        // upload ke folder file_siswa di dalam folder public
        $file->move('pengumuman', $file->getClientOriginalName());
        }else{
            DB::table('ms_pengumuman')
            ->insert([
                'judul'     => $input['judul'],
                'isi'       => $input['isi'],
                'file'      => "/",
                'tanggal'   => $input['tanggal'],
                'status'    => $input['status'],
                'aktif'     => $input['aktif'],
            ]);
        }


        return redirect()->route('pengumuman.index');
    }

    public function show($id)
    {
        $id = Crypt::decryptString($id);
        $kd_skpd = Auth::user()->kd_skpd;
        $data_awal = DB::table('ms_pengumuman')->where(['id' => $id])->first();

        $data = [
            'data_pengumuman' => $data_awal,
            'tanggal' => date('d M Y', strtotime($data_awal->tanggal))
        ];
        return view('master.pengumuman.show')->with($data);
    }

    public function edit($id)
    {
        $id = Crypt::decryptString($id);
        $kd_skpd = Auth::user()->kd_skpd;
        $data_awal = DB::table('ms_pengumuman')->where(['id' => $id])->first();

        $data = [
            'data_pengumuman' => $data_awal
        ];

        return view('master.pengumuman.edit')->with($data);
    }

    public function update(PengumumanRequest $request, $id)
    {
        $id = Crypt::decryptString($id);
        $input = array_map('htmlentities', $request->validated());
        $file = $request->file('dokumen');
        if($file !=null || $file !=''){
            DB::table('ms_pengumuman')->where('id', $id)->update([
                'judul'     => $input['judul'],
                'isi'       => $input['isi'],
                'file'      => "/".$file->getClientOriginalName(),
                'tanggal'   => $input['tanggal'],
                'status'    => $input['status'],
                'aktif'     => $input['aktif'],
        ]);
        unlink("pengumuman".$input['dokumenasli']);
        $file->move('pengumuman', $file->getClientOriginalName());
        }else{
            DB::table('ms_pengumuman')->where('id', $id)->update([
                'judul'     => $input['judul'],
                'isi'       => $input['isi'],
                'tanggal'   => $input['tanggal'],
                'status'    => $input['status'],
                'aktif'     => $input['aktif'],
        ]);
        }


        return redirect()->route('pengumuman.index');
    }

    public function destroy($id,$dokumen)
    {
        return $id;
        $data = DB::table('ms_pengumuman')->where(['id' => $id])->delete();
        if ($data) {
            unlink("pengumuman".$dokumen);
            return response()->json([
                'message' => '1'
            ]);
        } else {
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function hapus(Request $request)
    {
        $id         = $request->id;
        $dokumen    = $request->file;
        $data = DB::table('ms_pengumuman')->where(['id' => $id])->delete();
        if ($data) {
            unlink("pengumuman".$dokumen);
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
