<?php

namespace App\Models\Users;

use App\Traits\HasMarkdownFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DiscordBan extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'user_id', 'reason', 'ban_start_timestamp', 'ban_end_timestamp',
    ];

     /**
     * Retrieve the list of fields that should be processed as Markdown.
     * Required for HasMarkdownFields
     *
     * @return array An array of field names that are treated as Markdown.
     */
    protected function getMarkdownFields(): array
    {
        return ['reason'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPermanent()
    {
        if (! $this->ban_end_timestamp) {
            return true;
        }

        return false;
    }

    public function banStartPretty()
    {
        return Carbon::create($this->ban_start_timestamp)->toDayDateTimeString().' Zulu';
    }

    public function banEndPretty()
    {
        if ($this->isPermanment) {
            return null;
        }

        return Carbon::create($this->ban_end_timestamp)->toDayDateTimeString().' Zulu';
    }

    public function isCurrent()
    {
        if (Carbon::create($this->ban_end_timestamp) > Carbon::now()) {
            return true;
        }

        return false;
    }

    public function durationPretty()
    {
        return Carbon::create($this->ban_end_timestamp)->diffForHumans(Carbon::create($this->ban_start_timestamp));
    }
}
