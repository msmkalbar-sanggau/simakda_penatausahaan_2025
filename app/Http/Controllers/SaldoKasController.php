<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Yajra\DataTables\Facades\DataTables;



class SaldoKasController extends Controller
{
    public function index()
    {
        return view('bud.saldo_kas.index');
    }

    public function loadData()
    {
        $data = DB::table('buku_kas')->select('nomor','uraian','nilai','rek_bank')->get();
        // dd($data);

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("saldo_kas.edit", Crypt::encryptString($row->nomor)) . '" class="btn btn-warning btn-sm" title="Edit Saldo Kas" style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .=  '<a href="javascript:void(0);" onclick="hapus(' . $row->nomor . ');" class="btn btn-danger btn-sm" title="Hapus Saldo Kas"><i class="uil-trash"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function tambah()
    {
        return view('bud.saldo_kas.create');
    }

    public function simpan(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {
            DB::table('buku_kas')
                ->insert([
                    'uraian'    => $data['ket'],
                    'nilai'     => $data['nil'],
                    'rek_bank'  => $data['rek'],
                ]);

            DB::commit();
            return response()->json([
                'message' => '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }

    }

    public function hapus(Request $request)
    {
        $nomor = $request->nomor;

        DB::beginTransaction();
        try {
            DB::table('buku_kas')->where(['nomor' => $nomor])->delete();
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => '0'
            ]);
        }
    }

    public function edit($nomor)
    {
        $nomor = Crypt::decryptString($nomor);
        $bk_kas = DB::table('buku_kas')->where('nomor', $nomor)->first();
        $data = [
            'saldokas'  => $bk_kas,
        ];

        return view('bud.saldo_kas.edit')->with($data);
    }

    public function simpanEdit(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try{
            DB::table('buku_kas')->where(['nomor'=>$data['nomor']])
            ->update([
                'uraian'    => $data['ket'],
                'nilai'     => $data['nil'],
                'rek_bank'  => $data['rek'],
            ]);

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
