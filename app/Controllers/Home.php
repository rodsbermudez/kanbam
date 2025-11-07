<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function changelog(): string
    {
        // A variável $app_version já é injetada globalmente pelo BaseController
        return view('changelog');
    }
}
