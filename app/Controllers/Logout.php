<?php

namespace App\Controllers;
class Logout extends BaseController
{
    public function proses()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
