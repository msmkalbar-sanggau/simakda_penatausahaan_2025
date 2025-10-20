<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class BankController extends Controller
{
    public function index()
    {
        $data = [
            'daftar_bic' => DB::table('ms_bank_online')
                ->select('bic', 'nama_bank')
                ->get()
        ];

        return view('fungsi.bank.index')->with($data);
    }

    public function load()
    {
        $data = DB::table('ms_bank')
            ->orderByRaw("CAST(kode as int) asc")
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $btn = '<a href="javascript:void(0);" onclick="edit(\'' . $row->kode . '\');" class="btn btn-warning btn-sm"><i class="uil-edit"></i></a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function nomor()
    {
        $data = DB::table('ms_bank')
            ->selectRaw("ISNULL(MAX(CAST(kode as int)),0)+1 as nomor")
            ->first()
            ->nomor;

        return response()->json($data);
    }

    public function simpan(Request $request)
    {
        try {
            DB::beginTransaction();

            $cek = DB::table('ms_bank')
                ->where(function ($query) use ($request) {
                    $query->where('kode', '=', $request->kode)
                        ->orWhere('nama', '=', $request->nama);
                })
                ->count();

            if ($cek > 0)
                return response()->json(['message' => 'Data Gagal Disimpan. Data sudah ada'], 400);

            DB::table('ms_bank')
                ->insert([
                    'kode' => $request->kode,
                    'nama' => $request->nama,
                    'bic' => $request->bic,
                ]);

            DB::commit();
            return response()->json(['message' => 'Data Berhasil Disimpan'], 200);
        } catch (Exception $th) {
            DB::rollBack();

            Log::error('Exception caught: ' . $th->getMessage(), [
                'exception' => get_class($th),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return response()->json(['message' => 'Data Gagal Disimpan. Server Error !!!'], 500);
        }
    }
}
