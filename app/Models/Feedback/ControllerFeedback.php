<?php

namespace App\Models\Feedback;

use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Illuminate\Database\Eloquent\Model;

class ControllerFeedback extends Model
{
    use HasMarkdownFields;

    protected $hidden = ['id'];

    protected $fillable = [
        'user_id', 'controller_cid', 'position', 'content',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
