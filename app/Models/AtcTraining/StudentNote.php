<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\Node;
use Dom\Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;

class StudentNote extends Model
{
    protected $fillable = [
        'student_id', 'author_cid', 'title', 'content', 'created_at',
    ];

    public function formattedContent(): HtmlString
    {
        $document = HTMLDocument::createFromString('<!doctype html><body>'.($this->content ?? ''), LIBXML_NOERROR, 'UTF-8');

        return new HtmlString(self::formatNode($document->body));
    }

    private static function formatNode(Node $node): string
    {
        if ($node instanceof Text) {
            return e($node->data);
        }

        if (! $node instanceof Element
            || in_array($node->localName, ['script', 'style', 'template'], true)) {
            return '';
        }

        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= self::formatNode($child);
        }

        // Rebuild formatting with fixed tags and no attributes.
        return match ($node->localName) {
            'br' => '<br>',
            'b', 'blockquote', 'code', 'div', 'em', 'i', 'li', 'ol', 'p', 'pre', 's', 'strong', 'u', 'ul' => '<'.$node->localName.'>'.$html.'</'.$node->localName.'>',
            default => $html,
        };
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_cid');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
