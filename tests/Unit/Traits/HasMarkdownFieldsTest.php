<?php

namespace Tests\Unit\Traits;

use App\Traits\HasMarkdownFields;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use PHPUnit\Framework\TestCase;

class HasMarkdownFieldsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Markdown facade
        Markdown::shouldReceive('convert')
            ->with('**test**')
            ->andReturn((object) ['getContent' => fn () => '<strong>test</strong>']);

        Markdown::shouldReceive('convert')
            ->with('# Hello')
            ->andReturn((object) ['getContent' => fn () => '<h1>Hello</h1>']);
    }

    public function test_converts_markdown_field_to_html()
    {
        $model = new TestModelWithMarkdown([
            'description' => '**test**',
            'content' => '# Hello',
        ]);

        $html = $model->toHtml('description');

        $this->assertInstanceOf(HtmlString::class, $html);
        $this->assertEquals('<strong>test</strong>', $html->toHtml());
    }

    public function test_converts_different_markdown_fields()
    {
        $model = new TestModelWithMarkdown([
            'description' => '**test**',
            'content' => '# Hello',
        ]);

        $descriptionHtml = $model->toHtml('description');
        $contentHtml = $model->toHtml('content');

        $this->assertEquals('<strong>test</strong>', $descriptionHtml->toHtml());
        $this->assertEquals('<h1>Hello</h1>', $contentHtml->toHtml());
    }

    public function test_throws_exception_for_non_markdown_field()
    {
        $model = new TestModelWithMarkdown();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Field 'non_markdown' is not defined as a markdown field");

        $model->toHtml('non_markdown');
    }

    public function test_handles_null_markdown_content()
    {
        $model = new TestModelWithMarkdown(['description' => null]);

        $html = $model->toHtml('description');

        $this->assertInstanceOf(HtmlString::class, $html);
        $this->assertEquals('', $html->toHtml());
    }

    public function test_handles_empty_string_markdown_content()
    {
        $model = new TestModelWithMarkdown(['description' => '']);

        $html = $model->toHtml('description');

        $this->assertInstanceOf(HtmlString::class, $html);
        $this->assertEquals('', $html->toHtml());
    }
}

// Test model for the trait
class TestModelWithMarkdown extends Model
{
    use HasMarkdownFields;

    protected $fillable = ['description', 'content'];

    protected function getMarkdownFields(): array
    {
        return ['description', 'content'];
    }
}
