<?php

namespace App\Controllers;

use App\Models\AdminUserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn') === true) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/login', ['title' => 'Admin Login']);
    }

    public function attempt()
    {
        $rules = [
            'username' => 'required|max_length[100]',
            'password' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Enter both your username and password.');
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $user = (new AdminUserModel())->where('username', $username)->first();

        if ($user === null || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
        }

        session()->regenerate(true);
        session()->set([
            'isLoggedIn' => true,
            'adminId' => $user['id'],
            'username' => $user['username'],
            'displayName' => $user['display_name'],
        ]);

        return redirect()->to(base_url('dashboard'))->with('success', 'Welcome back, ' . $user['display_name'] . '!');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url('login'))->with('success', 'You have been logged out.');
    }
}
