<?php

namespace App\Models;

use App\Models\Analytics\AnalyticsSessionModel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'subject', 'message', 'analytics_session_id'])]
class InquiryModel extends Model
{
    protected $table = 'contact_inquiry';

    public function analyticsSession(): BelongsTo
    {
        return $this->belongsTo(AnalyticsSessionModel::class, 'analytics_session_id');
    }
}
