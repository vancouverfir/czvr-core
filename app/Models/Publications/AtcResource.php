<?php

namespace App\Models\Publications;

use App\Traits\HasMarkdownFields;
use Illuminate\Database\Eloquent\Model;

class AtcResource extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'user_id', 'title', 'font_awesome', 'description', 'url', 'atc_only',
    ];

    /**
     * Retrieve the list of fields that should be processed as Markdown.
     * Required for HasMarkdownFields.
     *
     * @return array An array of field names that are treated as Markdown.
     */
    protected function getMarkdownFields(): array
    {
        return ['description'];
    }
}
