<?php

namespace App\Http\Controllers;

use App\Models\FaqModel;
use App\Models\TeamModel;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Team members shown in the homepage preview section. Kept small so
     * the teaser doesn't grow unbounded as the admin adds more people -
     * the full roster lives on the dedicated /team page.
     */
    private const HOME_TEAM_LIMIT = 4;

    public function index(): Response
    {
        $faqs = FaqModel::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (FaqModel $faq) => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
            ]);

        $teams = TeamModel::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(self::HOME_TEAM_LIMIT)
            ->get()
            ->map(fn (TeamModel $team) => [
                'id' => $team->id,
                'name' => $team->name,
                'designation' => $team->designation,
                'bio' => $team->bio,
                'photo_url' => $team->photo ? '/storage/'.$team->photo : null,
                'facebook_url' => $team->facebook_url,
                'twitter_url' => $team->twitter_url,
                'instagram_url' => $team->instagram_url,
                'linkedin_url' => $team->linkedin_url,
            ]);

        return Inertia::render('Home', [
            'faqs' => $faqs,
            'teams' => $teams,
        ]);
    }
}
