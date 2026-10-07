<?php

namespace App\Controllers;

class About extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'About Us - WER Electric',
            'page' => 'about'
        ];
        return view('about', $data);
    }
}
