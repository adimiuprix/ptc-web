<?php

namespace App\Controllers;
use App\Models\UserModel;

class Register extends BaseController
{
    public function index()
    {
        return view('register');
    }

    public function proses()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();

        // Cek apakah username sudah digunakan
        if ($userModel->where('username', $username)->first()) {
            return redirect()->back()->with('error', 'Username sudah terdaftar.');
        }

        // Simpan user baru
        $userModel->insert([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/')->with('success', 'Registrasi berhasil. Silakan login.');    }
}
