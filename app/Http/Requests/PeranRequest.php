<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PeranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        $rules = [
            'role' => ['required', Rule::unique('peran')->ignore(request()->segment(3))],
            'nama_role' => ['required', Rule::unique('peran')->ignore(request()->segment(3))],
            'hak_akses' => ['required'],
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
            'role.required'    => 'Kode peran harus diisi!',
            'role.unique'    => 'Kode peran telah ada!',
            'nama_role.required'    => 'Nama peran harus diisi!',
            'nama_role.unique'    => 'Nama peran telah ada!',
            'hak_akses.required'    => 'Hak akses harus dipilih!',
        ];
    }
}
