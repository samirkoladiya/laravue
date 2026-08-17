<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Admin/Profile/Edit', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'photo_url' => $user->photo ? '/storage/'.$user->photo : null,
            ],
        ]);
    }

    public function update(AdminProfileUpdateRequest $request): RedirectResponse
    {
        if (! $request->isSpam()) {
            $user = $request->user();
            $data = $request->safe()->only(['name', 'email']);

            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }

                $data['photo'] = $request->file('photo')->store('avatars', 'public');
            }

            $user->update($data);
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}
