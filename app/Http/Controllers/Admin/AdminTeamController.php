<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminTeamRequest;
use App\Models\Admin\AdminTeamModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminTeamController extends Controller
{
    /**
     * Root-relative photo URL, e.g. "/storage/teams/xyz.jpg".
     *
     * Deliberately not Storage::url(), which returns an absolute URL
     * derived from APP_URL - that doesn't always match the host actually
     * serving the app (e.g. a local vhost vs. `php artisan serve`), which
     * would silently break every image src. A relative path always
     * resolves against whatever origin the browser is actually using.
     */
    private function photoUrl(AdminTeamModel $team): ?string
    {
        return $team->photo ? '/storage/'.$team->photo : null;
    }

    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));

        $teams = AdminTeamModel::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            }))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (AdminTeamModel $team) => [
                'id' => $team->id,
                'name' => $team->name,
                'designation' => $team->designation,
                'email' => $team->email,
                'photo_url' => $this->photoUrl($team),
                'status' => $team->status,
                'sort_order' => $team->sort_order,
            ]);

        return Inertia::render('Admin/Team/Index', [
            'teams' => $teams,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Team/Create');
    }

    public function store(AdminTeamRequest $request): RedirectResponse
    {
        if ($request->isSpam()) {
            return redirect()->route('admin.team.index')->with('success', 'Team member added successfully.');
        }

        $data = $request->safe()->except(['website', 'photo']);
        $data['photo'] = $request->file('photo')->store('teams', 'public');

        AdminTeamModel::create($data);

        return redirect()->route('admin.team.index')->with('success', 'Team member added successfully.');
    }

    public function edit(AdminTeamModel $team): Response
    {
        return Inertia::render('Admin/Team/Edit', [
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'designation' => $team->designation,
                'bio' => $team->bio,
                'email' => $team->email,
                'facebook_url' => $team->facebook_url,
                'twitter_url' => $team->twitter_url,
                'instagram_url' => $team->instagram_url,
                'linkedin_url' => $team->linkedin_url,
                'sort_order' => $team->sort_order,
                'status' => $team->status,
                'photo_url' => $this->photoUrl($team),
            ],
        ]);
    }

    public function update(AdminTeamRequest $request, AdminTeamModel $team): RedirectResponse
    {
        if ($request->isSpam()) {
            return redirect()->route('admin.team.index')->with('success', 'Team member updated successfully.');
        }

        $data = $request->safe()->except(['website', 'photo']);

        if ($request->hasFile('photo')) {
            if ($team->photo) {
                Storage::disk('public')->delete($team->photo);
            }

            $data['photo'] = $request->file('photo')->store('teams', 'public');
        }

        $team->update($data);

        return redirect()->route('admin.team.index')->with('success', 'Team member updated successfully.');
    }

    public function destroy(AdminTeamModel $team): RedirectResponse
    {
        if ($team->photo) {
            Storage::disk('public')->delete($team->photo);
        }

        $team->delete();

        return back()->with('success', 'Team member deleted successfully.');
    }
}
