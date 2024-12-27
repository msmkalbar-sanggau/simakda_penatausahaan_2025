<?php

namespace App\Http\Controllers;

// use App\Http\Requests\KontrakRequest;

use Facade\FlareClient\Stacktrace\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{



    public function edit()
    {   
        $kd_skpd = Auth::user()->kd_skpd;
        $data_awal = DB::table('ms_skpd')->where('kd_skpd', $kd_skpd)->first();
        $data = [
            'data_setting'  => $data_awal,
            'daftar_bank'   => DB::table('ms_bank')->select('kode', 'nama')->orderBy('kode')->get()
        ];

        return view('master.profile.skpd')->with($data);
    }

    public function update(Request $request)
    {
       
            DB::table('ms_skpd')->where('kd_skpd', $request['kd_skpd'])->update([
                'bank'          => $request['bank'],
                'obskpd'        => $request['obskpd'],
                'npwp'          => $request['npwp'],
                'rekening_pend' => $request['rekening_pend'],
                'rekening'      => $request['rekening'],
                'alamat'        => $request['alamat'],
                'kodepos'       => $request['kodepos'],
                'email'         => $request['email']
            ]);
            // kondisi 2
        


        return redirect()->route('profile.edit');
    }
}
