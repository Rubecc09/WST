<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\MembershipCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', [
            'categories' => MembershipCategory::query()->withCount('members')
                ->orderByRaw("CASE LOWER(name) WHEN 'regular' THEN 0 WHEN 'student' THEN 1 WHEN 'corporate' THEN 2 WHEN 'senior citizen' THEN 3 ELSE 4 END")
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            MembershipCategory::create($request->validated());
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['name' => 'This name is already in use.']);
        } catch (QueryException $exception) {
            report($exception);

            return to_route('categories.create')->withInput()->with('error', 'The category could not be saved. Please try again.');
        }

        return to_route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(MembershipCategory $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, MembershipCategory $category): RedirectResponse
    {
        try {
            $category->update($request->validated());
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['name' => 'This name is already in use.']);
        } catch (QueryException $exception) {
            report($exception);

            return to_route('categories.edit', $category)->withInput()->with('error', 'The category could not be saved. Please try again.');
        }

        return to_route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(MembershipCategory $category): RedirectResponse
    {
        if ($category->members()->exists()) {
            return to_route('categories.index')->with('error', 'This category has assigned members and cannot be deleted.');
        }
        try {
            $category->delete();
        } catch (QueryException $exception) {
            if (in_array((string) $exception->getCode(), ['23000', '23503'], true)) {
                return to_route('categories.index')->with('error', 'This category is in use and cannot be deleted.');
            }
            report($exception);

            return to_route('categories.index')->with('error', 'The category could not be deleted. Please try again.');
        }

        return to_route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
