<?php

namespace App\Traits;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use League\CommonMark\Extension\Table\TableExtension;

/**
 * Trait for models with markdown fields that need HTML conversion.
 *
 * Usage:
 * 1. Add trait to your model
 * 2. Define getMarkdownFields() method returning array of field names
 * 3. Access HTML via $model->toHtml('field_name')
 */
trait HasMarkdownFields
{
    /**
     * Define which fields contain markdown content
     * Must be implemented by the model using this trait.
     *
     * @return array List of field names that contain markdown
     */
    abstract protected function getMarkdownFields(): array;

    /**
     * Convert markdown text to HTML.
     *
     * @param  string|null  $markdown  The markdown content to convert
     * @return HtmlString The HTML-rendered version of the markdown
     */
    private function convertMarkdownToHtml(?string $markdown): HtmlString
    {
        if (! $markdown) {
            return new HtmlString('');
        }

        return new HtmlString(Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => true,
        ], [new TableExtension]));
    }

    /**
     * Get the HTML version of a markdown field.
     *
     * @param  string  $field  The name of the markdown field
     * @return HtmlString The HTML version of the field content
     *
     * @throws \InvalidArgumentException If the field is not defined as a markdown field
     */
    public function toHtml(string $field): HtmlString
    {
        if (! in_array($field, $this->getMarkdownFields())) {
            throw new \InvalidArgumentException("Field '{$field}' is not defined as a markdown field in ".static::class);
        }

        return $this->convertMarkdownToHtml($this->getAttribute($field));
    }
}
