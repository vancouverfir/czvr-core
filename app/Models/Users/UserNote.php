<?php

namespace App\Models\Users;

use App\Traits\HasMarkdownFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNote extends Model
{
    use HasMarkdownFields;

    protected $table = 'user_notes';

    protected $fillable = [
        'user_id', 'author_id', 'author', 'author_name', 'content', 'confidential', 'timestamp',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function setAuthorAttribute(int|string $authorId): void
    {
        $this->attributes['author_id'] = $authorId;
    }
}
