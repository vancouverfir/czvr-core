<?php

namespace Tests\Unit\Traits;

use App\Traits\HasMarkdownFields;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Tests\TestCase;

class HasMarkdownFieldsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Markdown facade
        $mockRendered1 = \Mockery::mock(RenderedContentInterface::class);
        $mockRendered1->shouldReceive('getContent')->andReturn('<strong>test</strong>');

        $mockRendered2 = \Mockery::mock(RenderedContentInterface::class);
        $mockRendered2->shouldReceive('getContent')->andReturn('<h1>Hello</h1>');

        Markdown::shouldReceive('convert')
            ->with('**test**')
            ->andReturn($mockRendered1);

        Markdown::shouldReceive('convert')
            ->with('# Hello')
            ->andReturn($mockRendered2);
    }

    public function test_skips_field_if_markdown_fields_are_not_defined()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Field 'note' is not defined as a markdown field");

        $model = new TestModelWithoutMarkdown(['note' => '# Hello']);
        $model->toHtml('note');
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

    public function test_returns_empty_html_for_non_markdown_field()
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

class TestModelWithoutMarkdown extends Model
{
    use HasMarkdownFields;

    protected $fillable = ['note'];

    protected function getMarkdownFields(): array
    {
        return [];
    }
}
