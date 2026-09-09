<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function index(): string
    {
        $users = [
            ['username' => 'rbilbao', 'full_name' => 'Rovic Bilbao', 'email' => 'rovic.bilbao@mail.com', 'role' => 'Administrator'],
            ['username' => 'pbateman', 'full_name' => 'Patrick Bateman', 'email' => 'patrick.bateman@mail.com', 'role' => 'Store Manager'],
            ['username' => 'vvega', 'full_name' => 'Vincent Vega', 'email' => 'vincent.vega@mail.com', 'role' => 'Cashier'],
            ['username' => 'mwallace', 'full_name' => 'Mia Wallace', 'email' => 'mia.wallace@mail.com', 'role' => 'Cashier'],
            ['username' => 'jwinfield', 'full_name' => 'Jules Winnfield', 'email' => 'jules.winnfield@mail.com', 'role' => 'Inventory Clerk'],
        ];

        return view('users/index', [
            'title' => 'User Accounts',
            'users' => $users,
        ]);
    }
}
