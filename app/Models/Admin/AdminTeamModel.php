<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'designation',
    'bio',
    'email',
    'photo',
    'facebook_url',
    'twitter_url',
    'instagram_url',
    'linkedin_url',
    'sort_order',
    'status',
])]
class AdminTeamModel extends Model
{
    protected $table = 'teams';

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'status' => 'boolean',
        ];
    }
}
