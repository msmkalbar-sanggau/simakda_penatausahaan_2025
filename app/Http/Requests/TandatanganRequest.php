<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TandatanganRequest extends FormRequest
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
            'nip'       => 'required',
            'nama'      => 'required',
            'jabatan'   => 'required',
            'pangkat'   => 'required',
            'kode'      => 'required',
            'kd_skpd'   => 'required',
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
            'kd_skpd.required'      => 'Kode SKPD harus diisi!',
            'nip.required'          => 'NIP harus diisi!',
            'nama.required'         => 'Nama harus diisi!',
            'jabatan.required'      => 'jabatan harus diisi!',
            'pangkat.required'      => 'Pangkat harus diisi!',
            'kode.required'         => 'kode harus diisi!',
            'nm_kerja.required'     => 'Nama pekerjaan harus diisi!',
        ];
    }
}
