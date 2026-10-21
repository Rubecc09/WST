<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $data = [
            'course' => 'Web System Technologies',
            'module' => 'Laravel Foundations',
        ];

        return view('about', $data);
    }

    public function task(string $id): View
    {
        $data = [
            'id' => $id,
            'title' => 'Buy groceries',
        ];

        return view('task', $data);
    }
}
