<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KontrakRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'kd_skpd' => 'required',
            'nm_skpd' => 'required',
            'no_kontrak' => 'required',
            'tgl_kerja' => 'required|date',
            'nmpel' => 'required',
            'pimpinan' => 'required',
            'nm_kerja' => 'required',
            'nm_rekening' => 'required',
            'no_rekening' => 'required|numeric',
            'npwp' => 'required|numeric',
            // 'nilai' => 'required|numeric',
            'nilai' => 'required|regex:/^\d+(\.\d+)?$/',
            'no_ref' => [
                'nullable',
                'min:10', // Menambahkan aturan minimal 10 karakter
                function ($attribute, $value, $fail) {
                    if (request()->input('nilai') > 100000000) {
                        if (empty($value)) {
                            $fail('No Referensi Bank harus diisi jika nilai kontrak lebih dari 100 juta!');
                        } elseif (strlen($value) < 10) {
                            $fail('No Referensi Bank harus memiliki minimal 10 karakter!');
                        }
                    }
                },
            ],
        ];
        if (request()->isMethod('post')) {
            $rule = $rules;
        } elseif (request()->isMethod('put')) {
            $rule = $rules;
        }
        return $rule;
    }

    public function messages()
    {
        return [
            'kd_skpd.required'    => 'Kode SKPD harus diisi!',
            'nm_skpd.required'    => 'Nama SKPD harus diisi!',
            'no_kontrak.required'    => 'No kontrak harus diisi!',
            'tgl_kerja.required'    => 'Tanggal kontrak harus diisi!',
            'tgl_kerja.date'    => 'Tanggal kontrak harus berformat tanggal!',
            'nmpel.required'    => 'Pelaksana pekerjaan/rekanan harus diisi!',
            'pimpinan.required'    => 'Pimpinan harus diisi!',
            'nm_kerja.required'    => 'Nama pekerjaan harus diisi!',
            // 'no_ref.required'    => 'Nomor referensi bank harus diisi!',
            'nm_rekening.required'    => 'Nama pemilik rekening harus dipilih!',
            'no_rekening.required'    => 'No rekening harus diisi!',
            'no_rekening.numeric'    => 'No rekening harus berformat angka!',
            'npwp.required'    => 'NPWP harus diisi!',
            'npwp.numeric'    => 'NPWP harus berformat angka!',
            'nilai.required'    => 'Nilai kontrak harus diisi!',
            // 'nilai.numeric'    => 'Nilai kontrak harus berformat angka!',
            'nilai.regex' => 'Nilai kontrak harus berupa angka atau desimal!',
        ];
    }
}
