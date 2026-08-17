<?php

namespace App\Http\Controllers;

use App\Models\TeamModel;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(): Response
    {
        $teams = TeamModel::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (TeamModel $team) => [
                'id' => $team->id,
                'name' => $team->name,
                'designation' => $team->designation,
                'bio' => $team->bio,
                // Root-relative so it resolves against whatever host is
                // actually serving the app, regardless of APP_URL.
                'photo_url' => $team->photo ? '/storage/'.$team->photo : null,
                'facebook_url' => $team->facebook_url,
                'twitter_url' => $team->twitter_url,
                'instagram_url' => $team->instagram_url,
                'linkedin_url' => $team->linkedin_url,
            ]);

        return Inertia::render('Team', [
            'teams' => $teams,
        ]);
    }
}
