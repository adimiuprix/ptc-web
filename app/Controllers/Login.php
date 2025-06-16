<?php

namespace App\Controllers;
use App\Models\UserModel;

class Login extends BaseController
{
    public function login()
    {
        return view('login');
    }

    public function auth()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Username doesn\'t match');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Password invalid');
        }

        // Simpan data user ke session
        session()->set([
            'user_id'    => $user['id'],
            'username'  => $user['username'],
            'logged_in' => true,
        ]);

        return redirect()->to('/dashboard');
    }
}
