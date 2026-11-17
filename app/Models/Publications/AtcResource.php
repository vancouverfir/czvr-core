<?php

namespace App\Models\Publications;

use App\Support\Publications\BcVfrTerminalProcedures;
use App\Traits\HasMarkdownFields;
use Illuminate\Database\Eloquent\Model;

class AtcResource extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'user_id', 'title', 'font_awesome', 'description', 'url',
    ];

    public static function bcVfrTerminalProcedures(): ?self
    {
        return static::where('url', 'like', '%BC\_VFR\_TERMINAL\_PROCEDURES\_%')->latest('id')->first();
    }

    public function resolvedUrl(): string
    {
        return BcVfrTerminalProcedures::resolveUrl($this->url);
    }

    public function resolvedTitle(): string
    {
        return BcVfrTerminalProcedures::resolveText($this->title, $this->url);
    }

    public function resolvedDescription(): string
    {
        return BcVfrTerminalProcedures::resolveText($this->description ?? '', $this->url);
    }

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
