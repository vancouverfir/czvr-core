<?php

namespace App\Models\News;

use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'id', 'title', 'user_id', 'show_author', 'image', 'content', 'summary', 'published', 'edited', 'visible', 'email_level', 'certification', 'slug',
    ];

    /**
     * Retrieve the list of fields that should be processed as Markdown.
     * Required for HasMarkdownFields.
     *
     * @return array An array of field names that are treated as Markdown.
     */
    protected function getMarkdownFields(): array
    {
        return ['content'];
    }

    /*
     * Return who posted the article
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function published_pretty()
    {
        $t = Carbon::create($this->published);

        return $t->day.' '.$t->monthName.' '.$t->year;
    }

    public function edited_pretty()
    {
        if (! $this->edited) {
            return null;
        }

        return Carbon::create($this->edited)->toDayDateTimeString();
    }

    public function author_pretty()
    {
        if (! $this->show_author) {
            return 'Vancouver FIR Staff';
        }

        return $this->user->fullName('FLC');
    }

    public function posted_on_pretty()
    {
        $t = Carbon::create($this->published);

        return $t->diffForHumans();
    }
}
