<?php

namespace App\Http\Controllers;

// use App\Http\Requests\KontrakRequest;

use Facade\FlareClient\Stacktrace\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Exception;
class SettingController extends Controller
{



    public function edit()
    {
        $data_awal = DB::table('config_app')->where('id', 1)->first();
        $data = [
            'data_setting' => $data_awal
        ];

        return view('master.setting.edit')->with($data);
    }

    public function update(Request $request)
    {
        if ($request['persen_kkpd'] < 30) {
            Session::flash('errors', 'Persen KKPD tidak boleh kurang dari 30%!');
            return redirect()->back();
        }
        if ($request['persen_kkpd'] + $request['persen_tunai'] != 100) {
            Session::flash('errors', 'Total Persen Tunai dan Persen KKPD tidak boleh kurang atau lebih dari 100%!');
            return redirect()->back();
        }
        // $request = array_map('htmlentities', $request->validated());
        // kondisi 1
        if ($request['logo_pemda_warna'] == '' && $request['logo_pemda_hp'] == '') {
            DB::table('config_app')->where('id', 1)->update([
                'nm_pemda'      => $request['nm_pemda'],
                'nm_badan'      => $request['nm_badan'],
                'thn_ang'       => $request['thn_ang'],
                'persen_tunai'       => $request['persen_tunai'],
                'persen_kkpd'       => $request['persen_kkpd'],
                'updated_at'    => date('Y-m-d  h:m:s')
            ]);
            // kondisi 2
        } else if ($request['logo_pemda_warna'] != '' && $request['logo_pemda_hp'] == '') {
            $file = $request->file('logo_pemda_warna');
            $new_logo       = 'logo_pemda' . '.' . $file->getClientOriginalExtension();
            $image = public_path('/template/assets/images/' . $new_logo);
            if (file_exists($image)) {
                unset($image);
            }
            $path = public_path('template/assets/images/');


            $file->move($path, $new_logo);

            DB::table('config_app')->where('id', 1)->update([
                'nm_pemda'          => $request['nm_pemda'],
                'nm_badan'          => $request['nm_badan'],
                'thn_ang'           => $request['thn_ang'],
                'persen_tunai'       => $request['persen_tunai'],
                'persen_kkpd'       => $request['persen_kkpd'],
                'logo_pemda_warna'  => $new_logo,
                'updated_at'        => date('Y-m-d  h:m:s')
            ]);
            // kondisi 3
        } else if ($request['logo_pemda_warna'] == '' && $request['logo_pemda_hp'] != '') {
            $file = $request->file('logo_pemda_hp');
            $new_logo_hp        = 'logo_pemda_hp' . '.' . $file->getClientOriginalExtension();
            $image = public_path('/template/assets/images/' . $new_logo_hp);
            if (file_exists($image)) {
                unset($image);
            }
            $path = public_path('template/assets/images/');
            $file->move($path, $new_logo_hp);

            DB::table('config_app')->where('id', 1)->update([
                'nm_pemda'      => $request['nm_pemda'],
                'nm_badan'      => $request['nm_badan'],
                'thn_ang'       => $request['thn_ang'],
                'persen_tunai'       => $request['persen_tunai'],
                'persen_kkpd'       => $request['persen_kkpd'],
                'logo_pemda_hp' => $new_logo_hp,
                'updated_at'    => date('Y-m-d  h:m:s')
            ]);
            // kondisi 4
        } else {
            $file2 = $request->file('logo_pemda_hp');
            $file = $request->file('logo_pemda_warna');
            $new_logo       = 'logo_pemda' . '.' . $file->getClientOriginalExtension();
            $new_logo_hp    = 'logo_pemda_hp' . '.' . $file2->getClientOriginalExtension();

            $image = public_path('/template/assets/images/' . $new_logo);
            unset($image);
            $image2 = public_path('/template/assets/images/' . $new_logo_hp);
            unset($image2);

            $path = public_path('template/assets/images/');
            $file->move($path, $new_logo);

            $path2 = 'template/assets/images';
            $file2->move($path2, $new_logo_hp);



            DB::table('config_app')->where('id', 1)->update([
                'nm_pemda'          => $request['nm_pemda'],
                'nm_badan'          => $request['nm_badan'],
                'logo_pemda_warna'  => $new_logo,
                'logo_pemda_hp'     => $new_logo_hp,
                'persen_tunai'       => $request['persen_tunai'],
                'persen_kkpd'       => $request['persen_kkpd'],
                'updated_at'        => date('Y-m-d  h:m:s')
            ]);
        }


        return redirect()->route('setting.edit');
    }

    function BackupDatabase(Request $request)
    {   
        $tahunx = "_2023";
        $tahun  = "2023";
        $keterangan='_';
        date_default_timezone_set('Asia/Jakarta');
        $oke = date('Y-m-d_H:i');
        $mantap = str_replace(':', '-', $oke);
        $strip = "_";
        $datetime = date('Y-m-d H:i:s');
        // DB::beginTransaction();
        try {
            
            DB::unprepared(DB::raw("BACKUP database simakda$tahunx to disk = 'E:backup_database_harian" . "\\2023\\" . "db_simakda_$tahun$strip$mantap$keterangan.bak'"));

            DB::update("UPDATE config_app set last_db_backup='$datetime'");


            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
                'error' => $e
            ]);
        }


       
       
    }
}
