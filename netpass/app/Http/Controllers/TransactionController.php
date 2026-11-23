<?php

namespace App\Http\Controllers;

use App\Actions\CreateMembershipTransaction;
use App\Http\Requests\TransactionRequest;
use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Notifications\MembershipApplicationSubmitted;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(MembershipTransaction::TYPES)],
            'status' => ['nullable', Rule::in(MembershipTransaction::STATUSES)],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
        ]);
        $transactions = MembershipTransaction::with('member')
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('reference_number', 'like', '%'.$search.'%')
                        ->orWhereHas('member', fn ($member) => $member->where('full_name', 'like', '%'.$search.'%')->orWhere('membership_number', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('transaction_type', $type))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('transaction_date', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('transaction_date', '<=', $date))
            ->latest('id')->paginate(15)->withQueryString();

        return view('transactions.index', compact('transactions', 'filters'));
    }

    public function createApplication(): View
    {
        return $this->form(MembershipTransaction::APPLICATION);
    }

    public function createRenewal(): View
    {
        return $this->form(MembershipTransaction::RENEWAL);
    }

    private function form(string $type): View
    {
        $members = Member::with(['membershipLevel', 'approvedMembership'])
            ->whereDoesntHave('unresolvedApplication')
            ->when($type === MembershipTransaction::RENEWAL,
                fn ($query) => $query->whereHas('approvedMembership'),
                fn ($query) => $query->whereDoesntHave('approvedMembership'))
            ->orderBy('full_name')->get();
        $levels = MembershipLevel::query()
            ->orderByRaw("CASE LOWER(name) WHEN 'basic' THEN 0 WHEN 'silver' THEN 1 WHEN 'gold' THEN 2 WHEN 'platinum' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->get();

        return view($type === MembershipTransaction::APPLICATION ? 'transactions.create-application' : 'transactions.create-renewal', compact('members', 'levels', 'type'));
    }

    public function storeApplication(TransactionRequest $request, CreateMembershipTransaction $action): RedirectResponse
    {
        return $this->store($request, $action, MembershipTransaction::APPLICATION);
    }

    public function storeRenewal(TransactionRequest $request, CreateMembershipTransaction $action): RedirectResponse
    {
        return $this->store($request, $action, MembershipTransaction::RENEWAL);
    }

    private function store(TransactionRequest $request, CreateMembershipTransaction $action, string $type): RedirectResponse
    {
        try {
            $transaction = $action->handle($request->validated(), $type, $request->user());
        } catch (QueryException $exception) {
            report($exception);

            return back()->withInput()->with('error', 'The transaction could not be saved. Please try again.');
        }

        if ($type === MembershipTransaction::APPLICATION) {
            Notification::route('mail', $transaction->member->email)
                ->notify(new MembershipApplicationSubmitted($transaction));
        }

        return to_route('transactions.show', $transaction)->with('success', $type.' transaction created. Awaiting payment and approval.');
    }

    public function show(MembershipTransaction $transaction): View
    {
        $transaction->load(['member.approvedMembership', 'administrator', 'approver', 'payments.administrator']);

        return view('transactions.show', compact('transaction'));
    }

    public function cancel(Request $request, MembershipTransaction $transaction): RedirectResponse
    {
        $request->validate(['status' => ['prohibited']]);
        if (! $transaction->cancel()) {
            return to_route('transactions.show', $transaction)->with('error', 'Only Pending transactions without payments can be cancelled.');
        }

        return to_route('transactions.show', $transaction)->with('success', 'Transaction cancelled. Its history has been retained.');
    }
}
