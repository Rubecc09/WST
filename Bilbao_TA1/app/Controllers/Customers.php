<?php

namespace App\Controllers;

class Customers extends BaseController
{
    public function index(): string
    {
        $customers = [
            ['full_name' => 'Kadeem Alford', 'email' => 'kadeem.alford@mail.com', 'phone' => '0917-123-4567'],
            ['full_name' => 'LeBron James', 'email' => 'lebron.james@mail.com', 'phone' => '0918-234-5678'],
            ['full_name' => 'Will Smith', 'email' => 'will.smith@mail.com', 'phone' => '0919-345-6789'],
            ['full_name' => 'Jordan Belfort', 'email' => 'jordan.belfort@mail.com', 'phone' => '0920-456-7890'],
            ['full_name' => 'Naomi Lapaglia', 'email' => 'naomi.lapaglia@mail.com', 'phone' => '0921-567-8901'],
        ];

        return view('customers/index', [
            'title'     => 'Customer Accounts',
            'customers' => $customers,
        ]);
    }
}
