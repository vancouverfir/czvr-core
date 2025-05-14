<?php

namespace App\Models\AtcTraining\CBT;

use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Illuminate\Database\Eloquent\Model;

class CbtModuleLesson extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'name', 'lesson', 'content_html', 'created_by', 'updated_by', 'updated_at', 'cbt_modules_id',
    ];

    /**
     * Retrieve the list of fields that should be processed as Markdown.
     * Required for HasMarkdownFields
     *
     * @return array An array of field names that are treated as Markdown.
     */
    protected function getMarkdownFields(): array
    {
        return ['content_html'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function CbtModule()
    {
        return $this->hasMany(CbtModule::class);
    }
}
