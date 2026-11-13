<?php

namespace App\Models\Feedback;

use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControllerFeedback extends Model
{
    use HasMarkdownFields, SoftDeletes;

    protected $table = 'controller_feedback';

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function controller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'controller_cid');
    }
}
