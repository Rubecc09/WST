<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', 'exists:membership_categories,id'],
            'level' => ['nullable', 'integer', 'exists:membership_levels,id'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
        ]);

        $members = Member::query()
            ->with(['membershipCategory', 'membershipLevel', 'approvedMembership', 'unresolvedApplication'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('membership_number', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['category'] ?? null, function ($query, int $category): void {
                $query->where('membership_category_id', $category);
            })
            ->when($filters['level'] ?? null, function ($query, int $level): void {
                $query->where('membership_level_id', $level);
            })
            ->when($filters['status'] ?? null, function ($query, string $status): void {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('members.index', [
            'members' => $members,
            'categories' => MembershipCategory::query()->orderBy('name')->get(),
            'levels' => MembershipLevel::query()->orderBy('name')->get(),
            'totalMembers' => Member::count(),
            'activeMembers' => Member::where('status', 'Active')->count(),
            'inactiveMembers' => Member::where('status', 'Inactive')->count(),
            'filters' => $filters,
            'hasFilters' => count(array_filter($filters, fn ($value): bool => $value !== null && $value !== '')) > 0,
        ]);
    }

    public function create(): View
    {
        return view('members.create', [
            'categories' => MembershipCategory::query()->orderBy('name')->get(),
            'levels' => $this->levelsForMemberForm(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $memberData = $this->validatedMemberData($request);
        $memberData['registration_date'] = today();
        $member = Member::create($memberData);

        return redirect()
            ->route('members.show', $member)
            ->with('success', 'Member profile created. Submit an application to request paid membership.');
    }

    public function show(Member $member): View
    {
        $member->load(['membershipCategory', 'membershipLevel', 'approvedMembership', 'unresolvedApplication']);

        $transactions = $member->transactions()->latest('id')->paginate(10, ['*'], 'history_page');

        return view('members.show', compact('member', 'transactions'));
    }

    public function edit(Member $member): View
    {
        return view('members.edit', [
            'member' => $member,
            'categories' => MembershipCategory::query()->orderBy('name')->get(),
            'levels' => $this->levelsForMemberForm(),
        ]);
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $member->update($this->validatedMemberData($request, $member));

        return redirect()
            ->route('members.index')
            ->with('success', 'Member details updated successfully.');
    }

    private function levelsForMemberForm(): Collection
    {
        return MembershipLevel::query()
            ->orderByRaw("CASE LOWER(name) WHEN 'basic' THEN 0 WHEN 'silver' THEN 1 WHEN 'gold' THEN 2 WHEN 'platinum' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->get();
    }

    public function deactivate(Member $member): RedirectResponse
    {
        $member->deactivate();

        return redirect()
            ->route('members.index')
            ->with('success', 'Member deactivated. Their record and history have been retained.');
    }

    public function reactivate(Member $member): RedirectResponse
    {
        $member->reactivate();

        return redirect()
            ->route('members.index')
            ->with('success', 'Member reactivated successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedMemberData(Request $request, ?Member $member = null): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('members', 'email')->ignore($member)],
            'contact_number' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:5000'],
            'membership_category_id' => ['required', 'integer', 'exists:membership_categories,id'],
            'membership_level_id' => ['required', 'integer', 'exists:membership_levels,id'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);
    }
}
