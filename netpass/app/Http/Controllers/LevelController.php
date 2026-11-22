<?php

namespace App\Http\Controllers;

use App\Http\Requests\LevelRequest;
use App\Models\MembershipLevel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class LevelController extends Controller
{
    public function index(): View
    {
        return view('levels.index', [
            'levels' => MembershipLevel::query()->withCount('members')
                ->orderByRaw("CASE LOWER(name) WHEN 'basic' THEN 0 WHEN 'silver' THEN 1 WHEN 'gold' THEN 2 WHEN 'platinum' THEN 3 ELSE 4 END")
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('levels.create');
    }

    public function store(LevelRequest $request): RedirectResponse
    {
        try {
            MembershipLevel::create($request->validated());
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['name' => 'This name is already in use.']);
        } catch (QueryException $exception) {
            report($exception);

            return to_route('levels.create')->withInput()->with('error', 'The level could not be saved. Please try again.');
        }

        return to_route('levels.index')->with('success', 'Level created successfully.');
    }

    public function edit(MembershipLevel $level): View
    {
        return view('levels.edit', compact('level'));
    }

    public function update(LevelRequest $request, MembershipLevel $level): RedirectResponse
    {
        try {
            $level->update($request->validated());
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['name' => 'This name is already in use.']);
        } catch (QueryException $exception) {
            report($exception);

            return to_route('levels.edit', $level)->withInput()->with('error', 'The level could not be saved. Please try again.');
        }

        return to_route('levels.index')->with('success', 'Level updated successfully.');
    }

    public function destroy(MembershipLevel $level): RedirectResponse
    {
        if ($level->members()->exists() || $level->transactions()->exists()) {
            return to_route('levels.index')->with('error', 'This level has assigned members or transaction history and cannot be deleted.');
        }
        try {
            $level->delete();
        } catch (QueryException $exception) {
            if (in_array((string) $exception->getCode(), ['23000', '23503'], true)) {
                return to_route('levels.index')->with('error', 'This level is in use and cannot be deleted.');
            }
            report($exception);

            return to_route('levels.index')->with('error', 'The level could not be deleted. Please try again.');
        }

        return to_route('levels.index')->with('success', 'Level deleted successfully.');
    }
}
