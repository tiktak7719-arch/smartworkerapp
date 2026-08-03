<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function set(string $locale, Request $request): RedirectResponse
    {
        abort_unless(in_array($locale, ['en', 'ur'], true), 404);
        $request->session()->put('locale', $locale);
        return back();
    }
}
