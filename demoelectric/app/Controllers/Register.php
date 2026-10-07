<?php

namespace App\Controllers;

use App\Models\CustomerAccountModel;

class Register extends BaseController
{
    protected CustomerAccountModel $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerAccountModel();
    }
    public function index(): string
    {
        $data = [
            'title' => 'Register - WER Electric',
            'page' => 'register',
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
            'validation' => session()->getFlashdata('validation')
        ];
        return view('register', $data);
    }
    public function create()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'username' => 'required|min_length[4]|max_length[100]|alpha_numeric_punct|is_unique[customer_accounts.username]',
            'email' => 'required|valid_email|is_unique[customer_accounts.email]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'address' => 'required|min_length[5]|max_length[255]',
            'city' => 'required|min_length[2]|max_length[100]',
            'state' => 'required|min_length[2]|max_length[50]',
            'zip_code' => 'required|min_length[5]|max_length[10]',
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
            'terms' => 'required'
        ]);
        if (!$validation->withRequest($this->request)->run()) {
            session()->setFlashdata('validation', $validation->getErrors());
            return redirect()->back()->withInput();
        }
        $customerData = [
            'account_number' => $this->generateUniqueValue('account_number', 'PF-' . date('Y') . '-'),
            'meter_number' => $this->generateUniqueValue('meter_number', 'MTR-'),
            'username' => trim((string) $this->request->getPost('username')),
            'customer_name' => trim((string) $this->request->getPost('first_name')) . ' ' . trim((string) $this->request->getPost('last_name')),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => implode(', ', [
                trim((string) $this->request->getPost('address')),
                trim((string) $this->request->getPost('city')),
                trim((string) $this->request->getPost('state')) . ' ' . trim((string) $this->request->getPost('zip_code')),
            ]),
            'password' => $this->request->getPost('password'),
            'connection_type' => 'residential',
            'status' => 'active',
        ];
        try {
            $customerId = $this->customerModel->insert($customerData);
            if ($customerId) {
                return redirect()->to(base_url('customer/login'))
                    ->with('success', 'Registration successful! You can now log in to your customer account.');
            } else {
                session()->setFlashdata('error', 'Registration failed. Please try again.');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Registration failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    private function generateUniqueValue(string $field, string $prefix): string
    {
        do {
            $value = $prefix . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->customerModel->where($field, $value)->first() !== null);

        return $value;
    }
}
