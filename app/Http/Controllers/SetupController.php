<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SetupController extends Controller
{
    public function index()
    {
        return view('Setup.index');
    }
    public function setup(Request $request)
    {
        $validate = Validator::make($request->only(['nama', 'username', 'password', 'confirm_password']), ['nama' => 'required', 'username' => 'required|unique:users,username', 'password' => 'required', 'confirm_password' => 'required|same:password'], [
            'nama.required' => "Nama harus di isi",
            'username.required' => "Username harus di isi",
            'password.required' => "Password harus di isi",
            'confirm_password.required' => "Konfirmasi password harus di isi",
            'username.unique' => 'Username sudah ada',
            'confirm_password.same' => 'Konfirmasi password tidak sama'

        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        } else {
            $user = new User();
            $data = [
                'nama' => $request->nama,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => "ADMIN"
            ];
            $register = $user->create($data);
            if ($register) {
                return redirect('/');
            } else {
                return redirect()->back();
            }
        }
    }
}
