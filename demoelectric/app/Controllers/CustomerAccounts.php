<?php

namespace App\Controllers;

use App\Models\CustomerAccountModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CustomerAccounts extends BaseController
{
    private CustomerAccountModel $accounts;

    public function __construct()
    {
        $this->accounts = new CustomerAccountModel();
    }

    public function index(): string
    {
        $search = trim((string) $this->request->getGet('search'));
        $status = trim((string) $this->request->getGet('status'));
        $type = trim((string) $this->request->getGet('type'));

        if ($search !== '') {
            $this->accounts->groupStart()
                ->like('account_number', $search)
                ->orLike('customer_name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->groupEnd();
        }
        if ($status !== '') {
            $this->accounts->where('status', $status);
        }
        if ($type !== '') {
            $this->accounts->where('connection_type', $type);
        }

        $accounts = $this->accounts->orderBy('created_at', 'DESC')->paginate(10);
        $stats = new CustomerAccountModel();

        return view('dashboard/index', [
            'title' => 'Customer Dashboard',
            'accounts' => $accounts,
            'pager' => $this->accounts->pager,
            'totalAccounts' => $stats->countAllResults(),
            'activeAccounts' => (new CustomerAccountModel())->where('status', 'active')->countAllResults(),
            'inactiveAccounts' => (new CustomerAccountModel())->where('status', 'inactive')->countAllResults(),
            'suspendedAccounts' => (new CustomerAccountModel())->where('status', 'suspended')->countAllResults(),
            'search' => $search,
            'status' => $status,
            'type' => $type,
        ]);
    }

    public function new(): string
    {
        return view('dashboard/form', ['title' => 'Add Customer Account', 'account' => null]);
    }

    public function create()
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->accountData();
        if ($this->accounts->where('account_number', $data['account_number'])->first() !== null) {
            return redirect()->back()->withInput()->with('errors', ['account_number' => 'The account number is already in use.']);
        }

        $this->accounts->insert($data);

        return redirect()->to(base_url('dashboard'))->with('success', 'Customer account created successfully.');
    }

    public function show(int $id): string
    {
        return view('dashboard/show', ['title' => 'Customer Details', 'account' => $this->findAccount($id)]);
    }

    public function edit(int $id): string
    {
        return view('dashboard/form', ['title' => 'Edit Customer Account', 'account' => $this->findAccount($id)]);
    }

    public function update(int $id)
    {
        $this->findAccount($id);
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->accountData();
        $duplicate = $this->accounts->where('account_number', $data['account_number'])->where('id !=', $id)->first();
        if ($duplicate !== null) {
            return redirect()->back()->withInput()->with('errors', ['account_number' => 'The account number is already in use.']);
        }

        $this->accounts->update($id, $data);

        return redirect()->to(base_url('customers/' . $id))->with('success', 'Customer account updated successfully.');
    }

    public function delete(int $id)
    {
        $account = $this->findAccount($id);
        $this->accounts->delete($id);

        return redirect()->to(base_url('dashboard'))->with('success', $account['customer_name'] . ' was deleted.');
    }

    private function findAccount(int $id): array
    {
        $account = $this->accounts->find($id);
        if ($account === null) {
            throw PageNotFoundException::forPageNotFound('Customer account not found.');
        }

        return $account;
    }

    private function rules(): array
    {
        return [
            'account_number' => 'required|max_length[30]',
            'customer_name' => 'required|min_length[2]|max_length[150]',
            'address' => 'required|min_length[5]|max_length[255]',
            'phone' => 'required|max_length[30]',
            'email' => 'required|valid_email|max_length[150]',
            'meter_number' => 'required|max_length[50]',
            'connection_type' => 'required|in_list[residential,commercial,industrial]',
            'status' => 'required|in_list[active,inactive,suspended]',
        ];
    }

    private function accountData(): array
    {
        return [
            'account_number' => trim((string) $this->request->getPost('account_number')),
            'customer_name' => trim((string) $this->request->getPost('customer_name')),
            'address' => trim((string) $this->request->getPost('address')),
            'phone' => trim((string) $this->request->getPost('phone')),
            'email' => trim((string) $this->request->getPost('email')),
            'meter_number' => trim((string) $this->request->getPost('meter_number')),
            'connection_type' => (string) $this->request->getPost('connection_type'),
            'status' => (string) $this->request->getPost('status'),
        ];
    }
}
