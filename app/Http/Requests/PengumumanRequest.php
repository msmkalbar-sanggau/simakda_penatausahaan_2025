<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengumumanRequest extends FormRequest
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
            'judul' => 'required',
            'isi' => 'required',
            'status' => 'required',
            'aktif' => 'required',
            'tanggal' => 'required|date',
            'dokumenasli'=>''
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
            'judul.required'    => 'Judul harus diisi!',
            'isi.required'    => 'isi harus diisi!',
            'tanggal.date'    => 'Tanggal harus diisi!',
            'status.required'    => 'Status harus diisi!',
            'aktif.required'    => 'Aktif harus diisi!',
        ];
    }
}
