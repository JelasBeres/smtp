<?php

namespace App\Http\Controllers;

use App\Services\UnsubscribeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnsubscribeController extends Controller
{
    public function show(string $token): View { return view('unsubscribe.show', compact('token')); }
    public function store(Request $request, string $token, UnsubscribeService $service): View
    {
        $contact = $service->unsubscribe($token);
        return view('unsubscribe.done', compact('contact'));
    }
}
