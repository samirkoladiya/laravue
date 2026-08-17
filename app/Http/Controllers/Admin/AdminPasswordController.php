<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminPasswordController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/Profile/ChangePassword');
    }

    public function update(AdminPasswordUpdateRequest $request): RedirectResponse
    {
        if (! $request->isSpam()) {
            // The User model casts `password` as `hashed`, so this is
            // hashed automatically on assignment (see AdminAuthController).
            $request->user()->update([
                'password' => $request->validated('password'),
            ]);
        }

        return back()->with('success', 'Password updated successfully.');
    }
}
