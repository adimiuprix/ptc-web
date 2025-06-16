<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session()->get();
        $user = new UserModel();
        $data = [
            'user' => $user->where('username', $session['username'])->first(),
        ];

        return view('dashboard', $data);
    }
}
