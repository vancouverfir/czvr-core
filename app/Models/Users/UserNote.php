<?php

namespace App\Models\Users;

use App\Traits\HasMarkdownFields;
use Illuminate\Database\Eloquent\Model;
class UserNote extends Model
{
    use HasMarkdownFields;

    protected $table = 'user_notes';

    protected $fillable = [
        'user_id', 'position', 'author', 'author_name', 'content', 'confidential', 'timestamp',
    ];

    /**
     * Retrieve the list of fields that should be processed as Markdown.
     * Required for HasMarkdownFields
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
