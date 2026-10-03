<?php

namespace App\Controllers;

use App\Models\CustomerAccountModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CustomerAuth extends BaseController
{
    public function login()
    {
        if (session()->get('customerLoggedIn') === true) {
            return redirect()->to(base_url('customer/account'));
        }

        return view('auth/customer_login', ['title' => 'Customer Login']);
    }

    public function attempt()
    {
        if (! $this->validate(['username' => 'required', 'password' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Enter both your username and password.');
        }

        $customer = (new CustomerAccountModel())
            ->where('username', trim((string) $this->request->getPost('username')))
            ->first();
        $valid = $customer !== null
            && ! empty($customer['password'])
            && password_verify((string) $this->request->getPost('password'), $customer['password']);

        if (! $valid) {
            return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
        }
        if ($customer['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'This customer account is currently ' . $customer['status'] . '.');
        }

        session()->regenerate(true);
        session()->set([
            'customerLoggedIn' => true,
            'customerId' => $customer['id'],
            'customerName' => $customer['customer_name'],
        ]);

        return redirect()->to(base_url('customer/account'));
    }

    public function account(): string
    {
        $customer = (new CustomerAccountModel())->find((int) session()->get('customerId'));
        if ($customer === null) {
            session()->remove(['customerLoggedIn', 'customerId', 'customerName']);
            throw PageNotFoundException::forPageNotFound('Customer account not found.');
        }

        return view('customer/account', ['title' => 'My Account', 'customer' => $customer]);
    }

    public function logout()
    {
        session()->remove(['customerLoggedIn', 'customerId', 'customerName']);
        session()->regenerate(true);

        return redirect()->to(base_url('customer/login'))->with('success', 'You have been logged out.');
    }
}
