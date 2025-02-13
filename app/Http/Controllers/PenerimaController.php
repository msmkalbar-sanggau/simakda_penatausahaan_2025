<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenerimaRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use Yajra\DataTables\Facades\DataTables;

class PenerimaController extends Controller
{
    public function index()
    {
        return view('master.penerima.index');
    }

    public function loadData()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $admin = Auth::user()->is_admin;

        $data = DB::table('ms_rekening_bank_online')
            ->where(function ($query) use ($admin, $kd_skpd) {
                if ($admin == '2') {
                    $query->where(['kd_skpd' => $kd_skpd]);
                }
            })
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("penerima.show_penerima", ['rekening' => Crypt::encryptString($row->rekening), 'kd_skpd' => Crypt::encryptString($row->kd_skpd)]) . '" class="btn btn-info btn-sm" style="margin-right:4px"><i class="uil-eye"></i></a>';
            $btn .= '<a href="' . route("penerima.edit_penerima", ['rekening' => Crypt::encryptString($row->rekening), 'kd_skpd' => Crypt::encryptString($row->kd_skpd)]) . '" class="btn btn-warning btn-sm" style="margin-right:4px"><i class="uil-edit"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="deleteData(\'' . $row->rekening . '|' . $row->kd_skpd . '\');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create()
    {
        $skpd = Auth::user()->kd_skpd;
        $kd_skpd = substr($skpd, 0, 17);

        $perusahaan1 = DB::table('ms_perusahaan')->select('nama as nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEFT(kd_skpd,17) = ?', [$kd_skpd])->groupBy('nama', 'pimpinan', 'npwp', 'alamat');
        $perusahaan2 = DB::table('trhspp')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEN(nmrekan)>1')->where('kd_skpd', $skpd)->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($perusahaan1);
        $perusahaan3 = DB::table('trhtrmpot_cmsbank')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEN(nmrekan)>1')->where('kd_skpd', $skpd)->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($perusahaan2);
        $perusahaan4 = DB::table('trhtrmpot')->select('nmrekan', 'pimpinan', 'npwp', 'alamat')->whereRaw('LEN(nmrekan)>1')->where('kd_skpd', $skpd)->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')->unionAll($perusahaan3);
        $result = DB::table(DB::raw("({$perusahaan4->toSql()}) AS sub"))
            ->select("nmrekan", "pimpinan", "npwp", "alamat")
            ->mergeBindings($perusahaan4)
            ->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->orderBy('nmrekan', 'ASC')
            ->orderBy('pimpinan', 'ASC')
            ->orderBy('npwp', 'ASC')
            ->orderBy('alamat', 'ASC')
            ->get();

        $data = [
            'daftar_bank' => DB::table('ms_bank_online')->get(),
            'daftar_kode_akun' => DB::table('ms_map_billing')->select('kd_map', 'nm_map')->groupBy('nm_map', 'kd_map')->get(),
            'daftar_rekanan' => $result,
        ];

        return view('master.penerima.create')->with($data);
    }

    public function simpanPenerima(Request $request)
    {
        $data = $request->data;

        $cek = DB::table('ms_rekening_bank_online')
            ->where([
                'rekening' => $data['rekening'],
                'kd_skpd' => Auth::user()->kd_skpd,
                'npwp' => $data['npwp'],
            ])
            ->count();

        DB::beginTransaction();
        try {
            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            } else {
                DB::table('ms_rekening_bank_online')->insert([
                    'kd_bank' => $data['bank'],
                    'rekening' => $data['rekening'],
                    'nm_rekening' => $data['nm_rekening'],
                    'bank' => $data['cabang'],
                    'nm_bank' => $data['nama_cabang'],
                    'kd_skpd' => Auth::user()->kd_skpd,
                    'jenis' => $data['jenis'],
                    'npwp' => isset($data['npwp']) ? $data['npwp'] : '',
                    'nm_wp' => $data['nm_npwp'],
                    'nm_wp' => isset($data['nm_npwp']) ? $data['nm_npwp'] : '',
                    'kd_map' => isset($data['kode_akun']) ? $data['kode_akun'] : '',
                    'kd_setor' => isset($data['kode_setor']) ? $data['kode_setor'] : '',
                    'keterangan' => $data['keterangan'],
                    'bic' => $data['bic'],
                    'nmrekan' => $data['rekanan'],
                    'pimpinan' => $data['pimpinan'],
                    'alamat' => $data['alamat'],
                    'keperluan' => $data['keperluan'],
                ]);
            }
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

    // public function store(PenerimaRequest $request)
    // {
    //     // $input = array_map('htmlentities', $request->validated());
    //     $input = $request->validated();
    //     // $input = $request->all();
    //     $cek = DB::table('ms_rekening_bank_online')
    //         ->where([
    //             'rekening' => $input['no_rekening_validasi'],
    //             'nm_rekening' => $input['nm_rekening_validasi'],
    //             'kd_skpd' => Auth::user()->kd_skpd
    //         ])
    //         ->count();

    //     if ($cek > 0) {
    //         return redirect()->back()->withInput()
    //             ->with(['message' => 'Rekening Telah Ada di SKPD', 'alert' => 'alert-danger']);
    //     }

    //     DB::table('ms_rekening_bank_online')->insert([
    //         'kd_bank' => $input['bank'],
    //         'rekening' => $input['no_rekening_validasi'],
    //         'nm_rekening' => $input['nm_rekening_validasi'],
    //         'bank' => $input['cabang'],
    //         'nm_bank' => $input['nama_cabang'],
    //         'kd_skpd' => Auth::user()->kd_skpd,
    //         'jenis' => $input['jenis'],
    //         'npwp' => isset($input['npwp_validasi']) ? $input['npwp_validasi'] : '',
    //         'nm_wp' => isset($input['nm_npwp_validasi']) ? $input['nm_npwp_validasi'] : '',
    //         'kd_map' => isset($input['kode_akun']) ? $input['kode_akun'] : '',
    //         'kd_setor' => isset($input['kode_setor']) ? $input['kode_setor'] : '',
    //         'keterangan' => $input['keterangan'],
    //         'bic' => $input['bic'],
    //         'nmrekan' => $input['rekanan'],
    //         'pimpinan' => $input['pimpinan'],
    //         'alamat' => $input['alamat'],
    //         'keperluan' => $input['keperluan'],
    //     ]);

    //     return redirect()->route('penerima.index');
    // }

    public function store(request $request)
    {
        // $input = array_map('htmlentities', $request->validated());
        $data = $request->data;
        // $input = $request->all();
        $cek = DB::table('ms_rekening_bank_online')
            ->where([
                'rekening' => $data['no_rekening'],
                'nm_rekening' => $data['nm_rekening'],
                'kd_skpd' => Auth::user()->kd_skpd
            ])
            ->count();

        if ($cek > 0) {
            return response()->json([
                'message' => '2'
            ]);
        }

        DB::beginTransaction();
        try {
            DB::table('ms_rekening_bank_online')
                ->insert([
                    'kd_bank' => $data['bank'],
                    'rekening' => $data['no_rekening'],
                    'nm_rekening' => $data['nm_rekening'],
                    'bank' => $data['cabang'],
                    'nm_bank' => $data['nama_cabang'],
                    'kd_skpd' => Auth::user()->kd_skpd,
                    'jenis' => $data['jenis'],
                    'npwp' => isset($data['npwp']) ? $data['npwp'] : '',
                    'nm_wp' => isset($data['nm_npwp']) ? $data['nm_npwp'] : '',
                    // 'kd_map' => isset($data['kode_akun']) ? $data['kode_akun'] : '',
                    // 'kd_setor' => isset($data['kode_setor']) ? $data['kode_setor'] : '',
                    'keterangan' => $data['keterangan'],
                    'bic' => $data['bic'],
                    'nmrekan' => $data['rekanan'],
                    'pimpinan' => $data['pimpinan'],
                    'alamat' => $data['alamat'],
                    'keperluan' => $data['keperluan'],
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

    public function showPenerima($rekening, $kd_skpd)
    {
        $rekening = Crypt::decryptString($rekening);
        $kd_skpd = Crypt::decryptString($kd_skpd);
        $data_awal = DB::table('ms_rekening_bank_online')->where(['rekening' => $rekening, 'kd_skpd' => $kd_skpd])->first();
        $data = [
            'data_penerima' => DB::table('ms_rekening_bank_online')->select("*")
                ->selectRaw("(SELECT count(no_rek) from trhspp a where a.no_rek = rekening and a.kd_skpd = kd_skpd) as totspp")
                ->selectRaw("(SELECT count(no_rekening) from ms_kontrak b where b.no_rekening = rekening and b.kd_skpd = kd_skpd) as totkon")->where(['rekening' => $rekening, 'kd_skpd' => $kd_skpd])->first(),
            'bank' => DB::table('ms_bank_online')->where('kd_bank', $data_awal->kd_bank)->first(),
            'billing' => DB::table('ms_map_billing')->where('kd_map', $data_awal->kd_map)->where('kd_setor', $data_awal->kd_setor)->first(),
        ];
        //dd($data['data_penerima']);

        return view('master.penerima.show')->with($data);
    }

    public function editPenerima($rekening, $kd_skpd)
    {
        $rekening = Crypt::decryptString($rekening);
        $kd_skpd = Crypt::decryptString($kd_skpd);

        $data_awal = DB::table('ms_rekening_bank_online')
            ->where(['rekening' => $rekening, 'kd_skpd' => $kd_skpd])
            ->first();

        $skpd = Auth::user()->kd_skpd;
        $kd_skpd = substr($skpd, 0, 17);

        $perusahaan1 = DB::table('ms_perusahaan')
            ->select('nama as nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->whereRaw('LEFT(kd_skpd,17) = ?', [$kd_skpd])
            ->groupBy('nama', 'pimpinan', 'npwp', 'alamat');

        $perusahaan2 = DB::table('trhspp')
            ->select('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->whereRaw('LEN(nmrekan)>1')
            ->where('kd_skpd', $skpd)
            ->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->unionAll($perusahaan1);

        $perusahaan3 = DB::table('trhtrmpot_cmsbank')
            ->select('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->whereRaw('LEN(nmrekan)>1')
            ->where('kd_skpd', $skpd)
            ->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->unionAll($perusahaan2);

        $perusahaan4 = DB::table('trhtrmpot')
            ->select('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->whereRaw('LEN(nmrekan)>1')
            ->where('kd_skpd', $skpd)
            ->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->unionAll($perusahaan3);

        $result = DB::table(DB::raw("({$perusahaan4->toSql()}) AS sub"))
            ->select("nmrekan", "pimpinan", "npwp", "alamat")
            ->mergeBindings($perusahaan4)
            ->groupBy('nmrekan', 'pimpinan', 'npwp', 'alamat')
            ->orderBy('nmrekan', 'ASC')
            ->orderBy('pimpinan', 'ASC')
            ->orderBy('npwp', 'ASC')
            ->orderBy('alamat', 'ASC')
            ->get();

        $cek = DB::table('trhspp')
            ->where(['no_rek' => $data_awal->rekening, 'kd_skpd' => $data_awal->kd_skpd])
            ->count();

        $data = [
            'data_penerima' => $data_awal,
            'daftar_rekanan' => $result,
            'daftar_bank' => DB::table('ms_bank_online')->get(),
            'nama_bank' => DB::table('ms_bank_online')->where('kd_bank', $data_awal->kd_bank)->first(),
            'daftar_kode_akun' => DB::table('ms_map_billing')->select('kd_map', 'nm_map')->groupBy('nm_map', 'kd_map')->get(),
            'cek' => $cek
        ];

        return view('master.penerima.edit')->with($data);
    }

    public function simpanEditPenerima(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();
        try {
            DB::table('ms_rekening_bank_online')
                ->where(['rekening' => $data['rekening'], 'kd_skpd' => Auth::user()->kd_skpd])
                ->update([
                    'kd_bank' => $data['bank'],
                    'rekening' => $data['rekening'],
                    'nm_rekening' => $data['nm_rekening'],
                    'bank' => $data['cabang'],
                    'nm_bank' => $data['nama_cabang'],
                    'kd_skpd' => Auth::user()->kd_skpd,
                    'jenis' => $data['jenis'],
                    'npwp' => isset($data['npwp']) ? $data['npwp'] : '',
                    'nm_wp' => isset($data['nm_npwp']) ? $data['nm_npwp'] : '',
                    'keterangan' => $data['keterangan'],
                    'bic' => $data['bic'],
                    'nmrekan' => $data['rekanan'],
                    'pimpinan' => $data['pimpinan'],
                    'alamat' => $data['alamat'],
                    'keperluan' => $data['keperluan'],
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

    public function updatePenerima(Request $request, $rekening, $kd_skpd)
    {
        $rekening = Crypt::decryptString($rekening);
        $kd_skpd = Crypt::decryptString($kd_skpd);

        DB::table('ms_rekening_bank_online')
            ->where([
                'rekening' => $rekening,
                'kd_skpd' => $kd_skpd
            ])
            ->update([
                'kd_bank' => $request['bank'],
                'rekening' => $request['rekening'],
                'nm_rekening' => $request['nm_rekening'],
                'bank' => $request['cabang'],
                'nm_bank' => $request['nama_cabang'],
                'kd_skpd' => $kd_skpd,
                'jenis' => $request['jenis'],
                'npwp' => isset($request['npwp']) ? $request['npwp'] : '',
                'nm_wp' => isset($request['nm_npwp']) ? $request['nm_npwp'] : '',
                'keterangan' => $request['keterangan'],
                'bic' => $request['bic'],
                'nmrekan' => $request['rekanan'],
                'pimpinan' => $request['pimpinan'],
                'alamat' => $request['alamat'],
            ]);

        return redirect()->route('penerima.index');
    }

    public function destroy($id)
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = DB::table('ms_rekening_bank_online')->where(['rekening' => $id, 'kd_skpd' => $kd_skpd])->delete();
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

    public function cekPenerima(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->id;
            $kd_skpd = Auth::user()->kd_skpd;
            $rekening = DB::table('ms_rekening_bank_online')->select('rekening')->where(['rekening' => $id, 'kd_skpd' => $kd_skpd])->first();
            $data = DB::table('ms_kontrak')->where(['no_rekening' => $rekening->rekening, 'kd_skpd' => $kd_skpd])->count();
            return response()->json($data);
        }
    }

    public function cabang(Request $request)
    {
        if ($request->ajax()) {
            $bic = $request->bic;
            $data = DB::table('ms_bank')->where('bic', $bic)->get();
            return response()->json($data);
        }
    }

    public function kode_setor(Request $request)
    {
        if ($request->ajax()) {
            $kd_map = $request->kd_map;
            $data = DB::table('ms_map_billing')->where('kd_map', $kd_map)->get();
            return response()->json($data);
        }
    }

    // buat sini aja, biar 1 controller

    public function kode_setor_pot(Request $request)
    {
        if ($request->ajax()) {
            $kd_rek6 = $request->kd_rek6;
            $data = DB::table('ms_map_billing')->where('kd_rek6', $kd_rek6)->get();
            return response()->json($data);
        }
    }

    public function coba()
    {
        $data = Http::get('https://simakda.kalbarprov.go.id/simakdaservice_2022/index.php/api/skpd/format/json');
        $data1 = json_decode($data, true);
        $result = [];
        foreach ($data1 as $data) {
            $result[] = $data;
        }
        // dd($result);
        $arraycolumn = array_column($result, 'kd_skpd');
        dd($arraycolumn);
        if (array_search('4.01.0.00.0.00.01.0000', array_column($data1, 'kd_skpd')) !== false) {
            echo 'value is in multidim array';
        } else {
            echo 'value is not in multidim array';
        }
        // dd($data5);
    }
}
