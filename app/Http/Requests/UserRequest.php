<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        if (request()->isMethod('post')) {
            $passwordRule = 'required';
            $passwordLamaRule = 'sometimes';
            $cek = Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised();
        } elseif (request()->isMethod('put')) {
            $passwordRule = 'sometimes';
            $passwordLamaRule = 'sometimes';
            if (request()->password_lama == '') {
                $cek = '';
            } else {
                $cek = Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised();
            }
        }
        return [
            'username' => ['required', Rule::unique('pengguna')->ignore(request()->segment(3))],
            'nama' => ['required', Rule::unique('pengguna')->ignore(request()->segment(3))],
            'password' => [$passwordRule, $cek],
            'password_lama' => [$passwordLamaRule],
            'confirmation_password' => [$passwordRule, 'same:password'],
            'kd_skpd' => ['required'],
            'tipe' => ['required'],
            'status' => ['required'],
            'peran' => ['required'],
            // 'jabatan' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'username.required'    => 'Username harus diisi!',
            'username.unique'    => 'Username telah ada!',
            'nama.required'    => 'Nama harus diisi!',
            'nama.unique'    => 'Nama telah ada!',
            'password.required'    => 'Password harus diisi!',
            'confirmation_password.required'    => 'Konfirmasi password harus diisi!',
            'kd_skpd.required'    => 'Kode SKPD harus dipilih!',
            'tipe.required'    => 'Tipe harus dipilih!',
            'status.required'    => 'Status harus dipilih!',
            'peran.required'    => 'Peran harus dipilih!',
            // 'jabatan.required'    => 'Jabatan harus diisi!',
            'password_lama.required' => 'Password lama harus diisi!',
            'password.sometimes' => 'Password harus diisi, ketika password lama diisi!'
        ];
    }
}
