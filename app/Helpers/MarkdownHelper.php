<?php

namespace App\Helpers;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Support\HtmlString;

class MarkdownHelper
{
    /**
     * Convert markdown text to HTML using Laravel-Markdown
     *
     * @param string|null $markdown The markdown content to convert
     * @return HtmlString The HTML-rendered version of the markdown content
     */
    public function toHtml(?string $markdown): HtmlString
    {
        if (!$markdown) {
            return new HtmlString('');
        }
        
        return new HtmlString(Markdown::convert($markdown)->getContent());
    }
}